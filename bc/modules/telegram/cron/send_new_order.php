<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/krza/data/www/krza.ru';
$_SERVER['HTTP_HOST'] = 'krza.ru';

require $_SERVER['DOCUMENT_ROOT']."/vars.inc.php";
require $_SERVER['DOCUMENT_ROOT']."/bc/connect_io.php";
require $_SERVER['DOCUMENT_ROOT']."/bc/modules/default/function.inc.php";

global $db;

if (Lock::isLocked()) {
    die('Предыдущая отправка не завершила работу');
}

Lock::setLock();

$botSettings = new TelegramBots\Order\Provider\Settings();
$botAPI = new TelegramBotAPI\LongmanBot\BotAPI($botSettings);
unset($botSettings);

$sender = new sendOrders($botAPI, $db);
$sender->run();

$sender = new sendForms($botAPI, $db);
$sender->run();

Lock::unlock();

class sendOrders
{
    private $botApi;
    private $db;

    public function __construct($botApi, $db)
    {
        $this->botApi = $botApi;
        $this->db = $db;
    }

    public function run()
    {
        $this->sendOrders();
    }
    
    private function sendOrders()
    {
        $orders = $this->getOrders();
        if (empty($orders)) {
            return;
        }
        Lock::setLock();

        $usedCatalogue = [];
        foreach ($orders as $order) {
            $usedCatalogue[$order['Catalogue_ID']] = 1;
        }

        $chats = $this->getChats(array_keys($usedCatalogue));

        unset($usedCatalogue);

        if (empty($chats)) {
            return;
        }

        Lock::setLock();

        $chatsSwaper = $chats;
        $chats = [];
        foreach ($chatsSwaper as $chat) {
            $chats[$chat['Catalogue_ID']][] = $chat;
        }
        unset($chatsSwaper, $chat);

        foreach ($orders as $order) {
            if (!isset($chats[$order['Catalogue_ID']])) {
                continue;
            }
            
            Lock::setLock();

            foreach ($chats[$order['Catalogue_ID']] as $chat) {
                $this->botApi->sendMessage($chat['id'], $this->getOrderMessage($order));
                Lock::setLock();
            }

            $sql = "INSERT INTO Message2248 (`order_id`) VALUES ({$order['id']})";
            $this->db->query($sql);
        }
    }
    
    private function getOrders(): array
    {
        $botID = $this->getBotID();
        $sql = "SELECT orders.`orderlist`, 
                       orders.`Message_ID` AS id,
                       orders.`fio`,
                       orders.`phone`,
                       orders.`email`,
                       orders.`customf`,
                       orders.`usertype`,
                       orders.`Catalogue_ID`,
                       Catalogue.`Domain` AS site_domain,
                       Catalogue.`login` AS site_login,
                       Catalogue.`https` AS site_https
                FROM Message2005 AS orders
                    INNER JOIN Catalogue ON orders.`Catalogue_ID` = Catalogue.`Catalogue_ID`
                WHERE
                    orders.`Created` > DATE_SUB(NOW(), INTERVAL 1 HOUR)

                    AND EXISTS (SELECT * 
                                FROM Message2249 AS cat_subscribers 
                                WHERE orders.`Catalogue_ID` = cat_subscribers.`Catalogue_ID` 
                                    AND cat_subscribers.`Checked` = 1
                                    AND cat_subscribers.`bot_id` = '{$botID}')

                    AND EXISTS (SELECT * 
                                FROM Message2247 AS tg_chats 
                                WHERE orders.`Catalogue_ID` = tg_chats.`Catalogue_ID`)

                    AND NOT EXISTS (SELECT * 
                                    FROM Message2248 AS tg_sended_orders 
                                    WHERE orders.`Message_ID` = tg_sended_orders.`order_id`)";
        
        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }
    
    /**
     * @param string $botName
     * @param array $catalogueList
     * 
     * @return array
     */
    private function getChats(array $catalogueList): array
    {
        $botID = $this->getBotID();
        $catalogueList = implode(',', $catalogueList);

        $sql = "SELECT `chat_id` AS id, `Catalogue_ID`
                FROM Message2247
                WHERE `bot_id` = '{$botID}' AND `Catalogue_ID` IN ({$catalogueList})";

        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function getBotID()
    {
        if (!isset($this->botID)) {
            $botName = $this->db->escape($this->botApi->getBotSettings()->getUserName());
            $this->botID = $this->db->get_var("SELECT `Message_ID` FROM Message2246 WHERE Message2246.`user_name` = '{$botName}'");
        }

        return $this->botID;
    }

    private function getOrderMessage($order)
    {
        $seporateFuntions = $_SERVER['DOCUMENT_ROOT'].'/b/'.$order['site_login'].'/function.php';

        if (file_exists($seporateFuntions)) {
            require_once $seporateFuntions;
        }

        $finctionName = 'telegramGetOrderMessage_cat'.$order['Catalogue_ID'];

        if (function_exists($finctionName)) {
            return call_user_func($finctionName, $order);
        }

        $orderList = orderArray($order['orderlist']);
        $form = orderArray($order['customf']);

        $count = 0;
        foreach ($orderList['items'] as $item) {
            $count += $item['count'];
        }

        $message  = "На сайте {$order['site_domain']} появился новый закз №{$order['id']}.";
        $message .= "\nСумма заказа: ".$orderList['totaldelsum'];
        foreach ($form as $field) {
            if (mb_strpos($field['name'], '(Юр.лицо)') !== false && !$order['usertype']) {
                continue;
            }
            switch (true) {
                case empty($field['value']):
                    $value = '-';
                    break;
                case is_array($field['value']):
                    $value = '';
                    foreach ($field['value'] as $val) {
                        $value .= !empty($value) ? ', ' : '';
                        $value .= $val;
                    }
                    break;
                default:
                    $value = $field['value'];
                    break;
            }
            $message .= "\n".str_replace('(Юр.лицо)', '', $field['name']).': '.$value;
        }

        $message .= "\n\nСостав заказа:";
        foreach ($orderList['items'] as $item) {
            $objLink = ($order['site_https'] ? 'https' : 'http').'://'.$order['site_domain'].nc_message_link($item['id'], 2001);

            $message .= "\n<a href='{$objLink}'>{$item['name']}</a>";
            if (!empty($item['count'])) $message .= " - {$item['count']} шт.";
            if (!empty($item['sum'])) $message .= " - {$item['sum']} руб.";
        }

        return $message;
    }
}

class sendForms
{
    private $botApi;
    private $db;

    public function __construct($botApi, $db)
    {
        $this->botApi = $botApi;
        $this->db = $db;
    }

    public function run()
    {
        $this->sendForms();
        $this->sendFeedBack();
    }

    private function sendForms()
    {
        $forms = $this->getForms();
        if (empty($forms)) {
            return;
        }
        Lock::setLock();

        $usedCatalogue = [];
        foreach ($forms as $form) {
            $usedCatalogue[$form['Catalogue_ID']] = 1;
        }

        $chats = $this->getChats(array_keys($usedCatalogue));
        unset($usedCatalogue);

        if (empty($chats)) {
            return;
        }

        Lock::setLock();

        $chatsSwaper = $chats;
        $chats = [];
        foreach ($chatsSwaper as $chat) {
            $chats[$chat['Catalogue_ID']][] = $chat;
        }
        unset($chatsSwaper, $chat);

        foreach ($forms as $form) {
            if (!isset($chats[$form['Catalogue_ID']])) {
                continue;
            }
            
            Lock::setLock();

            $message  = "На сайте {$form['site_domain']} появилась новая заявка \"".($form['nameform'] ?: $form['subname'])."\" №{$form['id']}.";
            $message .= "\nИмя: ".$form['Name'];
            if (!empty($form['phone'])) {
                $message .= "\nТелефон: ".$form['phone'];
            }
            if (!empty($form['Email'])) {
                $message .= "\nПочта: ".$form['Email'];
            }
            if (!empty($form['Subject'])) {
                $message .= "\nКонтакты: ".$form['Subject'];
            }
            if (!empty($form['subname'])) {
                $message .= "\nЗапрос со страницы: ".$form['subname'];
            }
            if (is_numeric($form['itemID'])) {
                $item = Class2001::getItemById($form['itemID']);
                if (!empty($item)) {
                    $message .= "\n<a href='//{$form['site_domain']}/{$item->fullLink}'>{$item->name}</a>";
                }
                unset($item);
            }
            $message .= "\n".$form['mailtext'];

            foreach ($chats[$form['Catalogue_ID']] as $chat) {
                $this->botApi->sendMessage($chat['id'], $message);
                Lock::setLock();
            }

            $sql = "INSERT INTO Message2250 (`form_id`) VALUES ({$form['id']})";
            
            $this->db->query($sql);
        }
    }

    private function getForms(): array
    {
        $botID = $this->getBotID();
        $sql = "SELECT forms.`Message_ID` AS id,
                       forms.`Name`,
                       forms.`nameform`,
                       forms.`phone`,
                       forms.`Email`,
                       forms.`subname`,
                       forms.`Subject`,
                       forms.`Catalogue_ID`,
                       forms.`mailtext`,
                       forms.`itemID`,
                       Catalogue.`Domain` AS site_domain,
                       Catalogue.`login` AS site_login
                FROM Message197 AS forms
                    INNER JOIN Catalogue ON forms.`Catalogue_ID` = Catalogue.`Catalogue_ID`
                WHERE
                    forms.`Created` > DATE_SUB(NOW(), INTERVAL 1 HOUR)

                    AND EXISTS (SELECT * 
                                FROM Message2249 AS cat_subscribers 
                                WHERE forms.`Catalogue_ID` = cat_subscribers.`Catalogue_ID` 
                                    AND cat_subscribers.`Checked` = 1
                                    AND cat_subscribers.`bot_id` = '{$botID}')

                    AND EXISTS (SELECT * 
                                FROM Message2247 AS tg_chats 
                                WHERE forms.`Catalogue_ID` = tg_chats.`Catalogue_ID`)

                    AND NOT EXISTS (SELECT * 
                                    FROM Message2250 AS tg_sended_forms
                                    WHERE forms.`Message_ID` = tg_sended_forms.`form_id`)";
        
        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function sendFeedBack()
    {
        $forms = $this->getFeedBackForms();
        if (empty($forms)) {
            return;
        }
        Lock::setLock();

        $usedCatalogue = [];
        foreach ($forms as $form) {
            $usedCatalogue[$form['Catalogue_ID']] = 1;
        }

        $chats = $this->getChats(array_keys($usedCatalogue));
        unset($usedCatalogue);

        if (empty($chats)) {
            return;
        }

        Lock::setLock();

        $chatsSwaper = $chats;
        $chats = [];
        foreach ($chatsSwaper as $chat) {
            $chats[$chat['Catalogue_ID']][] = $chat;
        }
        unset($chatsSwaper, $chat);

        foreach ($forms as $form) {
            if (!isset($chats[$form['Catalogue_ID']])) {
                continue;
            }
            
            Lock::setLock();

            $message  = "На сайте {$form['site_domain']} появился заказ обратного звонка №{$form['id']}.";
            $message .= "\nИмя: ".$form['Name'];
            if (!empty($form['phone'])) {
                $message .= "\nТелефон: ".$form['phone'];
            }
            if (!empty($form['city'])) {
                $message .= "\nГород: ".$form['city'];
            }
            if (!empty($form['time'])) {
                $message .= "\nВремя для связи: ".$form['time'];
            }

            foreach ($chats[$form['Catalogue_ID']] as $chat) {
                $this->botApi->sendMessage($chat['id'], $message);
                Lock::setLock();
            }

            $sql = "INSERT INTO Message2251 (`form_id`) VALUES ({$form['id']})";

            $this->db->query($sql);
        }
    }

    private function getFeedBackForms(): array
    {
        $botID = $this->getBotID();
        $sql = "SELECT forms.`Message_ID` AS id,
                       forms.`Name`,
                       forms.`phone`,
                       forms.`city`,
                       forms.`time`,
                       forms.`subname`,
                       forms.`Catalogue_ID`,
                       Catalogue.`Domain` AS site_domain,
                       Catalogue.`login` AS site_login
                FROM Message2013 AS forms
                    INNER JOIN Catalogue ON forms.`Catalogue_ID` = Catalogue.`Catalogue_ID`
                WHERE
                    forms.`Created` > DATE_SUB(NOW(), INTERVAL 1 HOUR)

                    AND EXISTS (SELECT * 
                                FROM Message2249 AS cat_subscribers 
                                WHERE forms.`Catalogue_ID` = cat_subscribers.`Catalogue_ID` 
                                    AND cat_subscribers.`Checked` = 1
                                    AND cat_subscribers.`bot_id` = '{$botID}')

                    AND EXISTS (SELECT * 
                                FROM Message2247 AS tg_chats 
                                WHERE forms.`Catalogue_ID` = tg_chats.`Catalogue_ID`)

                    AND NOT EXISTS (SELECT * 
                                    FROM Message2251 AS tg_sended_feed_back
                                    WHERE forms.`Message_ID` = tg_sended_feed_back.`form_id`)";
        
        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    /**
     * @param string $botName
     * @param array $catalogueList
     * 
     * @return array
     */
    private function getChats(array $catalogueList): array
    {
        $botID = $this->getBotID();
        $catalogueList = implode(',', $catalogueList);

        $sql = "SELECT `chat_id` AS id, `Catalogue_ID`
                FROM Message2247
                WHERE `bot_id` = '{$botID}' AND `Catalogue_ID` IN ({$catalogueList})";

        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function getBotID()
    {
        if (!isset($this->botID)) {
            $botName = $this->db->escape($this->botApi->getBotSettings()->getUserName());
            $this->botID = $this->db->get_var("SELECT `Message_ID` FROM Message2246 WHERE Message2246.`user_name` = '{$botName}'");
        }

        return $this->botID;
    }
}

class Lock {
    const LOCK_FILE = __DIR__.'/send_new_order.lock';

    public static function setLock()
    {
        file_put_contents(self::LOCK_FILE, 1);
    }

    public static function unlock()
    {
        unlink(self::LOCK_FILE);
    }

    public static function isLocked()
    {
        return file_exists(self::LOCK_FILE) && filectime(self::LOCK_FILE) > date('U') - 20;
    }
}