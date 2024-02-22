<?php
include_once($_SERVER['DOCUMENT_ROOT']."/bc/require/classes/nc_imagetransform.class.php");
class Iiko
{
    public $db;
    public $log;
    public $path;
    public $pass;
    public $login;
    public $shortPath;
    public $fields2001;
    public $organizations;

    public $token = array();
    public $keywords = array();

    public $iikoUrl = 'https://iiko.biz:9900/api/0/';
    public $platusUrl = 'https://api.platius.ru:9900/api/0/';
    public $key = 105107111; # keyCode букв слова iko в js

    public $style = "<style>
                        span { width: 2px; height: 2px; margin-bottom: 5px; display: inline-block; background-color: #000; }
                        .success { background-color: #4CAF50; }
                        .error { background-color: #f44336; }
                     </style>";

    public function __construct($auth, $catalogue, $db, $path)
    {
        $this->db = $db;
        $this->pass = $auth['pass'];
        $this->login = $auth['login'];
        $this->catalogue = $catalogue;
        $this->path = $_SERVER['DOCUMENT_ROOT'].$path;
        $this->shortPath = $path;

        if (!file_exists($this->path.'/iiko/') || !is_dir($this->path.'/iiko/')) mkdir($this->path.'/iiko/');

        # подключение отделенных функций
        $functionsPath = str_replace('/a/', '/b/', $this->path).'/class/iiko.php';
        if (file_exists($functionsPath)) include_once($functionsPath);
        $this->setOrganization();
    }

    # допустимые значения $type 'phone' 'id' 'card'
    public function getUser($type, $val)
    {
        if (!in_array($type, ['phone', 'id', 'card'])) return ['success' => false, 'error'=> 'недопустимый тип'];
        $orgId = $this->getOrganization();
        return $this->api($this->iikoUrl."customers/get_customer_by_{$type}?access_token=".$this->token()."&organization={$orgId}&{$type}={$val}");
    }

    public function addUpUser($params = array())
    {
        $post = '{"customer":'.json_encode($params).'}';
        $orgId = $this->getOrganization();
        return $this->api($this->iikoUrl."customers/create_or_update?access_token=".$this->token()."&organization={$orgId}", $post, 0, 1);
    }

    public function getStreet($city, $street)
    {
        $orgId = $this->getOrganization();
        $cities = $this->api($this->iikoUrl."cities/cities?access_token=".$this->token()."&organization={$orgId}");
        $result = array();
        if ($cities ) {
            foreach ($cities as $cityIiko) {
                if ($city == $cityIiko['city']['name']) {
                    $result['cityID'] = $cityIiko['city']['id'];
                    foreach ($cityIiko['streets'] as $streetIiko) {
                        if ($street == $streetIiko['name']){
    						$result['streetID'] = $streetIiko['id'];
    						break;
    					}
                    }
                    break;
                }
            }
        }
        return $result;
    }

    public function token($force = false)
    {
        $token = null;
        $tokenPath = $this->path.'/iiko/token.ini';
        $tokenLock = $this->path.'/iiko/tokenLock.ini';

        if (!file_exists($tokenPath) || filectime($tokenPath) + 55 < date('U') || $force) { # update
            file_put_contents($tokenLock, 1);

            $try = 0;
            do {
                if ($try > 1) usleep(10000); # 0.1 секунда
                $token = trim($this->api($this->iikoUrl."auth/access_token?user_id={$this->login}&user_secret={$this->pass}"), '"');
                $try++;
            } while(!$token && $try < 3);

            if ($token) file_put_contents($tokenPath, $token);
            else {
                file_put_contents($this->path.'/iiko/tokeerror.ini', "Запрос: {$this->iikoUrl}auth/access_token?user_id={$this->login}&user_secret={$this->pass}\r\n".print_r($token, 1));
                if (file_exists($tokenPath)) unlink($tokenPath);
            }

            unlink($tokenLock);
        } elseif (file_exists($tokenPath)) {
            $token = file_get_contents($tokenPath);
        }

        return $token;
    }

    public function setOrganization()
    {
        $this->organizations = $this->api($this->iikoUrl."organization/list?access_token=".$this->token()."&request_timeout=10");
    }

    public function getOrganization()
    {
        return $this->organizations[0]['id'];
    }

    # рычные условия
    public function getManualConditions()
    {
        $date = new \DateTime('now');
        if (!isset($this->manuals) || $this->manuals['time'] + 300 < $date->format('U')) { # кэш 5 минут
            $this->manuals = array(
                'time' => $date->format('U'),
                'values' => array()
            );
            $orgId = $this->getOrganization();
            $manuals = $this->api($this->iikoUrl."orders/get_manual_condition_infos?access_token=".$this->token()."&organization={$orgId}");
            if ($manuals) {
                foreach ($manuals as $manual) {
                     $this->manuals['values'][$manual['caption']] = $manual['id'];
                }
            }
        }
        return $this->manuals['values'];
    }

    public function api($url, $post = '', $debug = false, $json = false, $timeout = 60)
    {
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);

        if ($debug) curl_setopt($process, CURLOPT_HEADER, 1);
        if ($json) curl_setopt($process, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));
        if ($post) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $post);
        }

        $return = curl_exec($process);
        curl_close($process);

        if (substr($return,0,1)=='[' or substr($return,0,1)=='{') return json_decode($return, 1);
        return $return;
    }

    public function sendOrder($orderId, $user = false, $params = array())
    {
        if (function_exists('iiko_sendOrder')) {
            return iiko_sendOrder($this, $orderId, $user);
        } else {
            global $citymain;
            $city = $citymain;
    		# Получаем заказ
    		if (!$orderId) return array('success' => false, 'error' => 'неверный id');

            $this->log('order', '', $orderId);
            $this->log('write', "Заказ № {$orderId}");
    		$order = $this->db->get_row("SELECT * FROM Message2005 WHERE Message_ID = '{$orderId}'", ARRAY_A);
    		if (!$order) {
                $this->log('write', "\r\n\r\nНеудалось получть заказ из базы: SELECT * FROM Message2005 WHERE Message_ID = '{$orderId}'");
                return array('success' => false, 'error' => 'не смог получить заказ');
            } else $this->log('write', "\r\n\r\nДанные заказа успешны взяты из базы");

            $formInfo = orderArray($order['customf'], true);
            $orderList = orderArray($order['orderlist'], true);

    		# терминалы
            $orgId = $this->getOrganization();
            if (!$orgId) {
                $this->log('write', "\r\n\r\nНеудалось получть список организацию: {$orgId}
                                     \r\nСписок организаций: ".print_r($this->organizations, true));
                return array('success' => false, 'error' => 'не найдена организация');
            }
    		$terminals = $this->api($this->iikoUrl."deliverySettings/getDeliveryTerminals?access_token=".$this->token()."&organization={$orgId}&request_timeout=00:00:30");

            if (!$terminals['deliveryTerminals']) {
                $this->log('write', "\r\n\r\nНеудалось получть список терминалов: deliverySettings/getDeliveryTerminals?access_token=".$this->token()."&organization={$orgId}&request_timeout=00:00:30
                                     \r\nОтвет: ".print_r($terminals, true));
                return array('success' => false, 'error' => 'не найдены терминалы');
            } else $this->log('write', "\r\n\r\nТерминалы получены ".json_encode($terminals));

            $terminalId = $terminals['deliveryTerminals'][0]['deliveryTerminalId'];

            # получение пользователя
            $phone = preg_replace("/[^\d]+/", '', $formInfo['phone']['value']);
            $iikoUser = $this->getUser('phone', $phone);
    		if (!$iikoUser['phone']) { # усли не найден, то создать
    			$name = $formInfo['name']['value'];
    			$this->addUpUser(array('name' => $name, 'phone' => $phone));
    			$iikoUser = $this->getUser('phone', $phone);
    		}
            if (!$iikoUser['phone']) {
                $this->log('write', "\r\n\r\nНет телефона пользователя: ".$formInfo['phone']['value']."\r\nОтвет: ".print_r($iikoUser, true));
                return array('success' => false, 'error' => 'не удалось создать пользователя');
            } else $this->log('write', "\r\n\r\Пользователь получен ".json_encode($iikoUser));

            if (mb_stristr($iikoUser['name'], 'Клиент не был авторизован')) $this->addUpUser(array('name' => $name, 'phone' => $phone));

            $iikoUser['name'] = $formInfo['name']['value'];

            # товары
            $selectIDS = array();
            foreach ($orderList['items'] as $id => $item) {
                $selectIDS[$item['id']] = true;
            }
            $dbGoods = $this->db->get_results("SELECT * FROM Message2001
                                               WHERE Catalogue_ID = '{$this->catalogue}' AND xlslist = '{$this->key}' AND Message_ID IN (".implode(',', array_keys($selectIDS)).")", ARRAY_A);
            if (!$dbGoods) {
                $this->log('write', "\r\n\r\nтовары не найдены: {$selectIDS}\r\n SELECT * FROM Message2001
                                                   WHERE Catalogue_ID = '{$this->catalogue}' AND xlslist = '{$this->key}' AND Message_ID IN (".implode(',', array_keys($selectIDS)).")");
                return array('success' => false, 'error' => 'в заказе нет товаров из iiko');
            }

            $items = array();
            foreach ($dbGoods as $item) {
                $items[$item['Message_ID']] = $item;
            }

            $iikoOrderitems = array();
            foreach ($orderList['items'] as $id => $item) {
                if (!isset($items[$item['id']]) || $item['freeProductsIllusion']) continue;
                $dbItem = $items[$item['id']];
                $iikoOrderitems[] = array(
                    'id'	=>	$dbItem['code'],
                    'code'	=>	$dbItem['art'],
                    'name'	=>	$dbItem['name'],
                    'category' => $item['iikoGroup'],
                    'amount'=>	$item['realCount'] ? $item['realCount'] : ($item['count'] ? $item['count'] : 1),
                    'sum'	=>	$item['realSum'] ? $item['realSum'] : ($item['sum'] ? $item['sum'] : 0)
                );
            }

            # способ оплаты
            $class2005 = new Class2005();
            $pamentData = $class2005->getListName('payment', $order['payment']);
            $payment = array(
                ['sum' => $orderList['totaldelsum'],
                 'paymentType' => ['code' => $pamentData['iikoType']],
                 'isProcessedExternally' => 0
                ]
            );

            # способ доставки
            $deliveryData = $class2005->getListName('delivery', $order['delivery']);
            # способы дставки самовывоз
            if ($deliveryData['delivery_type'] == 2) {
                $address = array(
                    'city' => $city,
                    'street' => $formInfo['street']['value'] ? clearStreet($formInfo['street']['value'], false) : 'Не указано',
                    'home' => $formInfo['home']['value'] ? $formInfo['home']['value'] : 'Не указано',
                    'housing' => $formInfo['housing']['value'] ? $formInfo['housing']['value'] : 'Не указано',
                    'apartment' => $formInfo['apartment']['value'] ? $formInfo['apartment']['value'] : 'Не указано',
                    'floor' => $formInfo['floor']['value'] ? $formInfo['floor']['value'] : 'Не указано',
                    'entrance' => $formInfo['porch']['value'] ? $formInfo['porch']['value'] : 'Не указано'
                );
                if ($formInfo['street']['value']) { # id улицы
                    $streetArr = $this->getStreet($city, clearStreet($street, false));
                    if (isset($streetArr['streetID'])) $address['streetId'] = $streetArr['streetID'];
                    if(!$address['streetId']){
                        $streetArr = $this->getStreet($city, clearStreet($street, true));
                        if (isset($streetArr['streetID'])) $address['streetId'] = $streetArr['streetID'];
                    }
                }
            }

            $date = null;

            # не уверен что работает правильгл todo - 1
            if ($formInfo['delivery_time_app']['value'] || $formInfo['delivery_time_exact']['value']) {
                preg_match("/^\d{2}:\d{2}/", $formInfo['delivery_time_app']['value'], $match);
                $time = $formInfo['delivery_time_exact']['value'] ? $formInfo['delivery_time_exact']['value'] : $match[0];
                $date = (new \DateTime($formInfo['delivery_date']['value'].' '.$time))->format('Y-m-d H:i:s');
            }

            $comment = ($formInfo['Sdacha']['value'] ? "Подготовить сдачу с ".$formInfo['Sdacha']['value'] : null).$formInfo['comments']['value'];
            # массив заказа
            $iikoOrder = array(
                'organization' => $orgId,
                'deliveryTerminalId' => $terminalId,
                'customer' => [
                    'id' => $iikoUser['id'],
                    'name' => $iikoUser['name'],
                    'phone' => $iikoUser['phone']
                ],
                'order' => [
                    'phone' => $iikoUser['phone'],
                    'date' => $date,
                    'isSelfService' => $deliveryData['delivery_type'] == 1,
                    'personsCount' => $formInfo['prsonCount']['value'] ? $formInfo['prsonCount']['value'] : 1,
                    'items'=> $iikoOrderitems,
                    'externalId' => $orderId,
                    'comment' => $comment,
                    'fullSum' => $orderList['totaldelsum'],
                    'discountOrIncreaseSum' => $orderList['discont'],
                    'paymentItems' => $payment
                ]
            );

            if (isset($address) && $address) $iikoOrder['order']['address'] = $address;

            if (isset($orderList['coupon']) && $orderList['coupon']) {
                $manuals = $this->getManualConditions();
                if (isset($manuals[$orderList['coupon']])) { # ручное условие
                    $iikoOrder['applicableManualConditions'] = array($manuals[$orderList['coupon']]);
                } else { # купон
                    $iikoOrder['coupon'] = $orderList['coupon'];
                }
            }
            if (isset($orderList['useProgramm']) && is_array($orderList['useProgramm'])) {
                foreach ($orderList['useProgramm'] as $programID => $unused) {
                    $iikoOrder['availablePaymentMarketingCampaignIds'][] = $programID;
                }
            }

            $this->log('write', "\r\n\r\nТело заказа: ".print_r($iikoOrder, true));

            # отправка заказа
    		$temp = $this->api($this->iikoUrl.'orders/add?access_token='.$this->token().'&request_timeout=00:00:10', json_encode($iikoOrder), 0, 1);

            $this->log('write', "\r\n\r\nОтвет: ".print_r($temp, true));

            if ($temp && $temp['orderId']) {
                $this->db->query("UPDATE Message2005 SET code = '{$temp['orderId']}' WHERE Message_ID = '{$orderId}'");
            } else {
                return array('success' => false, 'error' => $temp);
            }
            $this->log('close');
        }
    }

    # реализовано на сайтах 890
    public function checkOrder($order, $user = array())
    {
        global $current_user, $AUTH_USER_ID;

        $result = array();
        $iikoData = array(
            'organization' => $this->getOrganization(),
            'isLoyaltyTraceEnabled' => true
        );
        if ($user) {
            $iikoData['customer'] = array(
                'phone' => $user['phone'],
                'name' => $user['name'],
            );
        } elseif ($current_user) {
            $iikoData['customer'] = array(
                'phone' => $current_user['phone'],
                'name' => $current_user['ForumName']
            );
        }

        if (!$iikoData['customer']['name']) $iikoData['customer']['name'] = 'nouser';
        if (!$iikoData['customer']['phone']) $iikoData['customer']['phone'] = '71111111111';

        $iikoData['order'] = array(
            'date' => (new \DateTime('now'))->format('Y-m-d H:i:s'),
            'phone' => $iikoData['customer']['phone']
        );

        if (isset($order['delivery']['id'])) {
            $delivery = Class2005::getListName('delivery', $order['delivery']['id']);
            switch ($delivery['delivery_type']) {
                case 1: # самовывоз
                    $iikoData['order']['isSelfService'] = true;
                    break;
                case 2: # доставка
                    $iikoData['order']['isSelfService'] = false;
                    $iikoData['order']['address'] = array(
                        'city' => 'Не указано',
                        'street' => 'Не указано',
                        'home' => 'Не указано'
                    );
                    break;
            }
        }

        $items = array();
        $ids = '';
        foreach ($order['items'] as $item) {
            if ($item['id']) $ids .= ($ids ? ',' : '').$item['id'];
        }
        $dbGoods = $this->db->get_results("SELECT Message_ID, code, art, name FROM Message2001 WHERE Catalogue_ID = '{$this->catalogue}' AND xlslist = '{$this->key}' AND Message_ID IN ({$ids})", ARRAY_A);
        if (is_array($dbGoods)) {
            foreach ($dbGoods as $item) {
                $items[$item['Message_ID']] = $item;
            }
        } else {
            return false;
        }

        foreach ($order['items'] as $item) {
            $dbItem = $items[$item['id']];
            if (!$dbItem || $item['freeProducts'] || $item['freeProductsIllusion']) continue;
            $iikoData['order']['items'][] = array(
                'id'	=>	$dbItem['code'],
                'code'	=>	$dbItem['art'],
                'category' => $item['iikoGroup'],
                'name'	=>	$dbItem['name'],
                'amount'=>	$item['realCount'] ? $item['realCount'] : $item['count'],
                'sum'	=>	$item['realSum'] ? $item['realSum'] : ($item['sum'] ? $item['sum'] : 0)
            );
        }

        $result['requestResult'] = $this->api($this->iikoUrl.'orders/calculate_checkin_result?access_token='.$this->token().'&request_timeout=00:00:10', json_encode($iikoData), 0, 1);

        if ($order['coupon']) {
            $result['couponResult'] = false;
            $manuals = $this->getManualConditions();
            if (isset($manuals[$order['coupon']])) { # ручное условие
                $iikoData['applicableManualConditions'] = array($manuals[$order['coupon']]);
            } else { # купон
                $iikoData['coupon'] = $order['coupon'];
            }
            $requestResult = $this->api($this->iikoUrl.'orders/calculate_checkin_result?access_token='.$this->token().'&request_timeout=00:00:10', json_encode($iikoData), 0, 1);
            if ($requestResult['loyatyResult']['loyaltyTrace'] != $result['requestResult']['loyatyResult']['loyaltyTrace']) {
                 $result['couponResult'] = true;
            }
            $result['requestResult'] = $requestResult;
        }

        return $result;
    }

    public function updateOrderByactions(array $params = array())
    {
        if (function_exists('iiko_updateOrderByActions')) {
            return iiko_updateOrderByActions($this, $params);
        } else {
            global $db, $AUTH_USER_ID;
            if (!$_SESSION['cart'] || !is_array($_SESSION['cart']['items'])) {
                unset($_SESSION['cart']);
                return false;
            }

            $checkResult = $this->checkOrder(array_merge($_SESSION['cart'], $params));

            $struct = array(
                'discounts' => array(),
                'freeProducts' => array(),
                'useProgramm' => array()
            );

            # собираем скидки и подарки
            if (is_array($checkResult['requestResult'])) {
                foreach ($checkResult['requestResult']['loyatyResult']['programResults'] as $programm) {
                    foreach ($programm['discounts'] as $item) {
                        $struct['discounts'][$item['orderItemId']] += $item['discountSum'];
                        $struct['useProgramm'][$programm['programId']] = true;
                    }
                    foreach ($programm['freeProducts'] as $freeProducts) {
                        if (isset($freeProducts['products']) && is_array($freeProducts['products'])) {
                            foreach ($freeProducts['products'] as $item) {

                                if (isset($struct['freeProducts'][$item['code']])) $struct['freeProducts'][$item['code']]++;
                                else $struct['freeProducts'][$item['code']] = 1;

                                $struct['useProgramm'][$programm['programId']] = true;
                            }
                        }
                    }
                }
            }

            $useFree = array();
            $totalSum = $totalDiscont = 0;
            # проверям скидки к товарам и подарки
            foreach ($_SESSION['cart']['items'] as $id => $item) {
                # проверяем нужно ли удалять подарок из заказа
                if ($item['freeProducts']) {
                    if (isset($struct['freeProducts'][$item['art']])) {
                        $_SESSION['cart']['items'][$id]['count'] = $struct['freeProducts'][$item['art']];
                        $useFree[$item['art']] = true;
                    } else {
                        unset($_SESSION['cart']['items'][$id]);
                    }
                } elseif (isset($struct['discounts'][$item['iikoID']])) {
                    $discont = $struct['discounts'][$item['iikoID']];
                    $totalDiscont += $discont;
                    $_SESSION['cart']['items'][$id]['dicont'] = $discont;
                } else {
                    $_SESSION['cart']['items'][$id]['dicont'] = 0;
                }
                $totalSum += $_SESSION['cart']['items'][$id]['sum'];
            }

            $_SESSION['cart']['discont'] = $totalDiscont;
            $_SESSION['cart']['totalsum'] = $totalSum - $totalDiscont;
            $_SESSION['cart']['totalSumDiscont'] = $totalSum;
            $_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['totalsum'] + $_SESSION['cart']['delivery']['sum_result'];
            $_SESSION['cart']['useProgramm'] = $struct['useProgramm'];
            $_SESSION['cart']['couponResult'] = $checkResult['couponResult'];

            $codes = '';
            foreach ($struct['freeProducts'] as $code => $count) {
                if (isset($useFree[$code])) continue;
                $codes .= ($codes ? ',' : '')."'{$code}'";
            }

            if ($codes) {
                $freeItems = $db->get_results("SELECT Message_ID as id, name, art, code
                                               FROM Message2001
                                               WHERE Catalogue_ID = {$this->catalogue} AND xlslist = '{$this->key}' AND art IN ($codes)", ARRAY_A);
                if (is_array($freeItems)) {
                    foreach ($freeItems as $item) {
                        $_SESSION['cart']['items'][$item['id'].'_freeprod'] = array(
                            'id' => $item['id'],
                            'name' => $item['name'],
                            'variant' => 'подарок',
                            'price' => 0,
                            'count' => $struct['freeProducts'][$item['art']],
                            'sum' => 0,
                            'art' => $item['art'],
                            'iikoID' => $item['code'],
                            'freeProducts' => true
                        );
                    }
                }
            }
        }
    }


    # Export Block
    public function export()
    {
        if (function_exists('iiko_export')) {
            return iiko_export($this);
        } else {
            echo $this->style;
            $this->log('export');
            $this->log('write', "###СТАРТ###\r\n");
            $this->log('write', "Дата: ".((new DateTime())->format("Y-m-d H:i:s"))."\r\n");
            $this->setExportSettings();

            $orgId = $this->getOrganization();
            $nom = $this->api($this->iikoUrl."nomenclature/{$orgId}?access_token=".$this->token());

            $groups = ($nom['groups'] ? $this->exportGroup($nom['groups']) : '');

            $this->log('write', "\r\n\r\n###ТОВАРЫ###\r\n\r\n");

            if (is_array($nom['products'])) {
                $allGoods = $allModifiers = array();

                $dbGoods = $this->db->get_results("SELECT * FROM Message2001 WHERE Catalogue_ID = {$this->catalogue} AND xlslist = {$this->key}", ARRAY_A);
                if ($dbGoods) {
                    foreach ($dbGoods as $dbItem) {
                        $allGoods[$dbItem['code']] = $dbItem;
                    }
                }
                $this->log('write', "Всего товаров в выгрузке: ".count($nom['products'])."\r\nТоваров в базе: ".count($allGoods)."\r\n\r\n");

                $exportItems = $priorityInOther = array();

                # выгрузка товаров
                foreach ($nom['products'] as $key => $iikoItem) {

                    if (!$groups[$iikoItem['parentGroup']]) {
                        $this->log('write', "iiko_id = {$iikoItem['id']} неопределена группа\r\n\r\n");
                        continue;
                    }

                    $group = $groups[$iikoItem['parentGroup']];

                    if (isset($exportItems[$iikoItem['id']])) {
                        if (!$exportItems[$iikoItem['id']]['photourl'] && $iikoItem['images']) {
                            $exportItems[$iikoItem['id']]['photourl'] = $this->addImg($iikoItem['images'], $iikoItem['id']);
                        }
                        $exportItems[$iikoItem['id']]['Subdivision_IDS'] .= ($exportItems[$iikoItem['id']]['Subdivision_IDS'] ? '' : ',').$group['sub'].',';
                        # приоритет в других разделах
                        $priorityInOther[] = array(
                            'id' => $iikoItem['id'],
                            'priority' => $iikoItem['order'],
                            'subID' => $group['sub']
                        );
                    } else {
                        $exportItems[$iikoItem['id']] = array_merge(array(
                            'art' => $iikoItem['code'],
                            'name' => trim($iikoItem['name']),
                            'code' => $iikoItem['id'],
                            'price' => $iikoItem['price'],
                            'Checked' => $iikoItem['isIncludedInMenu'] ? 1 : 0,
                            'xlslist' => $this->key,
                            'photourl' => $iikoItem['images'] ? $this->addImg($iikoItem['images'], $iikoItem['id']) : '',
                            'Priority' => $iikoItem['order'],
                            'Catalogue_ID' => $this->catalogue,
                            'Sub_Class_ID' => $group['subClass'],
                            'Subdivision_ID' => $group['sub'],
                            'Subdivision_IDS' => ''
                        ), $this->getOtherParams($iikoItem));
                    }
                }
                $counter = -1;
                foreach ($exportItems as $itemID => $item) {
                    $counter++;
                    if ($counter % 100 == 0) echo $counter;

                    $this->write('success');

                    $this->log('write', "{$counter}:".json_encode($item)."\r\n");
                    if (!$item['Checked']) continue;
                    if (isset($allGoods[$itemID])) { # update
                        $allGoods[$itemID]['exported'] = true;
                        $itemCheck = array(
                            'art' => $allGoods[$itemID]['art'],
                            'name' => $allGoods[$itemID]['name'],
                            'code' => $allGoods[$itemID]['code'],
                            'price' => $allGoods[$itemID]['price'],
                            'Checked' => $allGoods[$itemID]['Checked'],
                            'xlslist' => $allGoods[$itemID]['xlslist'],
                            'photourl' => $allGoods[$itemID]['photourl'],
                            'Priority' => $allGoods[$itemID]['Priority'],
                            'Catalogue_ID' => $allGoods[$itemID]['Catalogue_ID'],
                            'Sub_Class_ID' => $allGoods[$itemID]['Sub_Class_ID'],
                            'Subdivision_ID' => $allGoods[$itemID]['Subdivision_ID'],
                            'Subdivision_IDS' => $allGoods[$itemID]['Subdivision_IDS']
                        );
                        if ($this->otherParams) {
                            foreach ($this->otherParams as $fName) {
                                if (isset($item[$fName])) $itemCheck[$fName] = $allGoods[$itemID][$fName];
                            }
                        }
                        $exportItems[$itemID]['Message_ID'] = $allGoods[$itemID]['Message_ID'];
                        $this->log('write', "Сравниваемый товар: ".json_encode($itemCheck)."\r\n");
                        if ($itemCheck == $item) {
                            $this->log('write', "Товар не требуется нуждается в обновлении db_id = {$allGoods[$itemID]['Message_ID']}\r\n\r\n");
                            continue; # нет изменений в товаре, не обновлять
                        }

                        $this->update($item, 'Message2001', "AND Message_ID = {$allGoods[$itemID]['Message_ID']}");
                    } else { #create
                        $item['Keyword'] = $this->getKeyword('Message2001', encodestring($item['name'], 1));
                        $this->insert($item, 'Message2001');
                        $exportItems[$itemID]['Message_ID'] = $this->db->insert_id;
                    }
                }

                # приоритет в других разделах

                if (count($priorityInOther) > 0) {
                    $priorityOld = $priorityNew = array();

                    foreach ($priorityInOther as $item) {
                        $key = $item['subID'].'_'.$exportItems[$item['id']]['Message_ID'];
                        $priorityNew[$key] = $item;
                        $priorityNew[$key]['Message_ID'] = $exportItems[$item['id']]['Message_ID'];
                    }

                    $priorityDB = $this->db->get_results("SELECT Message_ID, Subdivision_ID, Priority, Checked, item_ID FROM Message2099 WHERE Catalogue_ID = {$this->catalogue}", ARRAY_A);

                    if ($priorityDB) {
                        foreach ($priorityDB as $item) {
                            $key = $item['Subdivision_ID'].'_'.$item['item_ID'];
                            $priorityOld[$key] = $item;
                        }
                    }

                    foreach ($priorityNew as $key => $priority) {
                        if (isset($priorityOld[$key])) {
                            # update
                            if ($priorityOld[$key]['Priority'] == $priority['priority'] && $priorityOld[$key]['Checked']) continue;
                            $priority = array(
                                'Priority' => $priority['priority'],
                                'Checked' => 1
                            );
                            $this->update($priority, 'Message2099', "AND Message_ID = '{$priorityOld[$key]['Message_ID']}'");
                        } else {
                            # create
                            $priority = array(
                                'Subdivision_ID' => $priority['subID'],
                                'item_ID' => $priority['Message_ID'],
                                'Catalogue_ID' => $this->catalogue,
                                'Priority' => $priority['priority'],
                                'Checked' => 1
                            );
                            $this->insert($priority, 'Message2099');
                        }
                    }
                }

                # выключение невыгруженных товаров
                $this->log('write', "\r\n\r\n###Выключение невыгруженого###\r\n\r\n");

                $groupOff = $itemOff = array();
                foreach ($allGoods as $item) {
                    if (!in_array($item['Message_ID'], $itemOff) && !$item['exported']) $itemOff[] = $item['Message_ID'];
                }
                if ($groups) {
                    do {
                        $groupOffCheck = $groupOff;
                        foreach ($groups as $groupId => $group) {
                            if (!in_array($group['sub'], $groupOff) && (!$group['exported'] || in_array($group['parent'], $groupOff))) $groupOff[] = $group['sub'];
                        }
                    } while ($groupOffCheck != $groupOff);
                }
                if ($groupOff) {
                    $groupOffWhere = "Subdivision_ID in ('".implode("','", $groupOff)."')";
                    $this->update(['Checked' => 0], 'Subdivision', "AND {$groupOffWhere}");
                }
                if ($itemOff) $this->delete("Message2001", "AND Message_ID IN (".implode(",", $itemOff).")");
                if ($groupOffWhere) $this->update(['Checked' => 0], 'Message2001', "AND {$groupOffWhere}");
            }
            $this->log('close');
        }
    }

    public function getKeyword($dbName, $name, $addkey = 0)
    {
        global $AUTH_USER_ID;
        if (!isset($this->keyword[$dbName])) {
            switch ($dbName) {
                case 'Subdivision':
                case 'Message2001':
                    $this->keyword[$dbName] = array();
                    $fields = array(
                        'Subdivision' => 'EnglishName',
                        'Message2001' => 'Keyword'
                    );
                    $dbKeys = $this->db->get_col("SELECT {$fields[$dbName]} FROM {$dbName} WHERE Catalogue_ID = {$this->catalogue}");

                    if (is_array($dbKeys)) foreach ($dbKeys as $key) $this->keyword[$dbName][$key] = true;
                    break;
                case 'default':
                    $this->log("write", "\r\n Неверный тип keyword: {$dbName}");
                    exit;
            }
        }
        $keyword = $name.($addkey ? '-'.$addkey : null);
        $keyword = trim(preg_replace('/[^0-9a-z-]+/i', '-', $keyword), '- ');
        if (isset($this->keyword[$dbName][$keyword])) return $this->getKeyword($dbName, $name, ++$addkey);
        else {
            $this->keyword[$dbName][$keyword] = true;
            return $keyword;
        }
    }

    public function addImg($imgs, $itemId)
    {
        $imgNames = "";
        $this->write('success');
        $this->log("write", "загрузка изображений: ".json_encode($imgs)."\r\n");
        foreach (array_reverse($imgs) as $key => $img) {
            if ($this->goodsImgLimit !== false && $key >= $this->goodsImgLimit) break;

            $this->log("write", "\t{$key}: ".json_encode($img)."\r\n");

            $imgName = $img['imageId'].'_iiko.png';
            $filePath = $this->path.'/files/import/'.$imgName;
            $imgNames .= ($imgNames ? ',' : '').$imgName;
            $imgLastUpdate = (new DateTime($img['uploadDate']))->format('U');

            if (file_exists($filePath) && filemtime($filePath) > $imgLastUpdate) {
                $this->log("write", "\t\tизображение не требует обновления\r\n");
                continue;
            }

            $this->log("write", "\t\t путь: {$filePath} загрузка изображения...");
            $imgContent = file_get_contents($img['imageUrl']);
            file_put_contents($filePath, $imgContent);
            $this->log("write", "->успех");

            if (@getimagesize($filePath)[0] > 800) {
                $this->log("write", "->сжатие");
                @nc_ImageTransform::imgResize($filePath, $filePath, 800, 800, 0, "", 90);
                $this->log("write", "->успех");
            }

            if ($this->whaterMark !== false) {
                $this->log("write", "->водяной знак, путь к фото :{$filePath} , путь к вод знаку {$this->whaterMark}");
                @nc_ImageTransform::putWatermark_file($filePath, $this->whaterMark, 0);
                $this->log("write", "->успех");
            }

            $this->log("write", "\r\n");
        }
        return $imgNames;
    }

    public function getOtherParams($item)
    {
        if (!$this->otherParams) return array();

        if (!$this->fields2001) { # все поля каталога и их типы
            $fieldsData2001 = $this->db->get_results("EXPLAIN Message2001", ARRAY_A);
            foreach ($fieldsData2001 as $field) {
                $this->fields2001[$field['Field']] = array('type'=> $field['Type']);
            }
        }

        $result = array();
        foreach ($this->otherParams as $iikoKey => $fName) {
            if (!isset($this->fields2001[$fName]) || ($fName == 'name' && !$item[$iikoKey])) continue; # такого поля в каталоге нет || такого ключа нет
            $result[$fName] = trim((string)$item[$iikoKey]);
        }
        return $result;
    }

    public function setExportSettings()
    {
        $settings = $result = array();
        if (file_exists($this->path.'/iiko/settings.ini') && trim(file_get_contents($this->path.'/iiko/settings.ini'))) {
            $settings = json_decode(trim(file_get_contents($this->path.'/iiko/settings.ini')), true);
        }

        # Subdivision_ID раздела в котроый выгружать товары и подразделы
        if (isset($settings['rootSub']) && $settings['rootSub']) {
            $this->rootSub = $this->db->get_row("SELECT Subdivision_ID as sub, Hidden_URL as url FROM Subdivision WHERE Subdivision_ID = {$settings['rootSub']}", ARRAY_A);
        } else {
            $this->rootSub = $this->db->get_row("SELECT a.Subdivision_ID as sub, a.Hidden_URL as url
                                                 FROM Subdivision as a LEFT JOIN Sub_Class as b ON a.Subdivision_ID = b.Subdivision_ID
                                                 WHERE a.Catalogue_ID = {$this->catalogue}
                                                    AND a.Parent_Sub_ID = 0
                                                    AND a.EnglishName != 'search'
                                                    AND b.Class_ID = 2001 limit 0,1", ARRAY_A);
        }

        if (!$this->rootSub) {
            $this->log('write', "\r\n\r\n не удалось получть крневой раздел для выгрузки");
            exit;
        }

        $this->classTemplate = isset($settings['classTemplate']) ? $settings['classTemplate'] : 0;
        $this->otherParams = isset($settings['otherParams']) ? $settings['otherParams'] : array();
        $this->goodsImgLimit = isset($settings['goodsImgLimit']) ? $settings['goodsImgLimit'] : false;
        $this->whaterMark = isset($settings['whaterMark']) ? $this->path.'/'.$settings['whaterMark'] : false;
    }

    public function exportGroup($iikoGroups)
    {
        if (function_exists('iiko_exportGroup')) {
            return iiko_exportGroup($this, $iikoGroups);
        } else {
            $this->log('write', "\r\n\r\n###ГРУППЫ###\r\n\r\n");

            $this->subKeywords = $exportGroups = $groups = array();

            $subsData = $this->db->get_results("SELECT a.code1C as code,
                                                       a.Hidden_URL as url,
                                                       a.EnglishName as enName,
                                                       b.Sub_Class_ID as subClass,
                                                       a.Parent_Sub_ID as parent,
                                                       a.Subdivision_Name as name,
                                                       a.Subdivision_ID as sub,
                                                       a.Priority,
                                                       a.Checked,
                                                       a.descr
                                               FROM Subdivision as a,
                                                    Sub_Class as b
                                               WHERE a.Catalogue_ID = {$this->catalogue}
                                                     AND a.Subdivision_ID = b.Subdivision_ID
                                                     AND a.code1C LIKE 'IIKO&%'
                                                     AND b.Class_ID = '2001'", ARRAY_A);

            if ($subsData) {
                foreach($subsData as $sub) {
                    $groups[substr($sub['code'], 5)] = $sub;
                }
            }

            $exportGroups = $this->sortGroup($iikoGroups);

            $this->log('write', "Всего групп в выгрузке: ".count($exportGroups)."\r\nГрупп на сайте: ".count($subsData)."\r\n\r\n");

            $counter = -1;
            foreach ($exportGroups as $groupId => $iikoGroup) {
                $this->write('success');
                $counter++;
                if (!$iikoGroup['parentGroup'] && $iikoGroup['parentGroup'] !== null) continue;

                $enName = isset($groups[$groupId]) ? $groups[$groupId]['enName'] : $this->getKeyword('Subdivision', encodestring($iikoGroup['name'], 1));
                $parentId = (isset($groups[$iikoGroup['parentGroup']]) ? $groups[$iikoGroup['parentGroup']]['sub'] : $this->rootSub['sub']);
                $urlSub = (isset($groups[$iikoGroup['parentGroup']]) ? $groups[$iikoGroup['parentGroup']]['url'].$enName.'/' : $this->rootSub['url'].$enName.'/');

                if (isset($groups[$groupId])) { # update
                    $this->addGroupImg($iikoGroup['images'], $groups[$groupId]['sub']);
                    $groups[$groupId]['exported'] = $iikoGroup['isIncludedInMenu'];

                    if ($groups[$groupId]['parent'] == $parentId
                        && $groups[$groupId]['url'] == $urlSub
                        && $groups[$groupId]['name'] == $iikoGroup['name']
                        && $groups[$groupId]['Priority'] == $iikoGroup['order']
                        && !trim($iikoGroup['description'])
                        && $groups[$groupId]['Checked'] == $iikoGroup['isIncludedInMenu']
                        ) {
                            $this->log('write', "{$counter} группа db_id = {$groups[$groupId]['sub']} не нуждается в обновлении\r\n\r\n");
                            continue; # нет изменений
                        }

                    $group = array(
                        'Catalogue_ID' => $this->catalogue,
                        'Parent_Sub_ID' => $parentId,
                        'Hidden_URL' => $urlSub,
                        'Checked' => $iikoGroup['isIncludedInMenu'] ? 1 : 0,
                        'Priority' => $iikoGroup['order'],
                        'Subdivision_Name' => $iikoGroup['name'],
                        'code1C' => 'IIKO&'.$groupId,
                    );
                    if (trim($iikoGroup['description'])) $group['descr'] = trim($iikoGroup['description']);

                    $this->log('write', "{$counter} #UPDATE# ".json_encode($group)."\r\n");

                    $this->update($group, 'Subdivision', "AND Subdivision_ID = {$groups[$groupId]['sub']}");
                    $groups[$groupId]['url'] = $urlSub;
                    $groups[$groupId]['parent'] = $parentId;
                } else { # create
                    $group = array(
                        'Catalogue_ID' => $this->catalogue,
                        'Parent_Sub_ID' => $parentId,
                        'Hidden_URL' => $urlSub,
                        'Checked' => $iikoGroup['isIncludedInMenu'] ? 1 : 0,
                        'Priority' => $iikoGroup['order'],
                        'Subdivision_Name' => $iikoGroup['name'],
                        'EnglishName' => $enName,
                        'code1C' => 'IIKO&'.$groupId,
                        'descr' => trim($iikoGroup['description']) ? trim($iikoGroup['description']) : ''
                    );

                    $this->log('write', "{$counter} #INCETR#".json_encode($group)."\r\n");

                    $this->insert($group, 'Subdivision');
                    $subId = $this->db->insert_id;
                    if (!$subId) continue; # неудалось добавть раздел

                    $subClass = array(
                        'Catalogue_ID' => $this->catalogue,
                        'Subdivision_ID' => $subId,
                        'Class_Template_ID' => $this->classTemplate,
                        'Class_ID' => 2001,
                        'Checked' => 1,
                        'Sub_Class_Name' => 'IIKO'.$groupId,
                        'EnglishName' => 'IIKO'.$groupId
                    );

                    $this->log('write', "{$counter} #INCETR#".json_encode($subClass)."\r\n");

                    $this->insert($subClass, 'Sub_Class');
                    $subClass = $this->db->insert_id;
                    if (!$subClass) continue; # неудалось создать инфаблок

                    $groups[$groupId] = array(
                        'url' => $urlSub,
                        'sub' => $subId,
                        'parent' => $parentId,
                        'subClass' => $subClass,
                        'exported' => $iikoGroup['isIncludedInMenu']
                    );
                    $this->addGroupImg($iikoGroup['images'], $subId);
                }
            }
            return $groups;
        }
    }

    public function addGroupImg($imgs, $sub)
    {
        if ($imgs) {
            $img = $imgs[count($imgs)-1];
            $imgName = $img['imageId'].'_iiko.jpg';
            $dirPath = $this->path."/files/{$sub}";
            $imgLastUpdate = (new DateTime($img['uploadDate']))->format('U');

            if (!file_exists($dirPath.'/'.$imgName) || filemtime($dirPath.'/'.$imgName) < $imgLastUpdate) {
                if (!file_exists($dirPath)) mkdir($dirPath);
                $imgContent = file_get_contents($img['imageUrl']);
                file_put_contents($dirPath.'/'.$imgName, $imgContent);
                $this->update(array('img' => "{$img['imageId']}jpg:image/jpeg:10101:{$sub}/{$imgName}"), 'Subdivision', "AND Subdivision_ID = {$sub}");
            }
        }
    }

    # сортируем группы по порядку, сначала родительские потом дочерние
    public function sortGroup($groups)
    {
        $result = array();
        $remArr = false;
        while ($result !== $remArr) {
            $groupAdd = array();
            foreach ($groups as $group) {
                if ($group['parentGroup'] && !in_array($group['id'], array_keys($result)) && !in_array($group['parentGroup'], array_keys($result))) continue;
                $groupAdd[$group['id']] = $group;
            }
            $remArr = $result;
            $result = array_merge($result, $groupAdd);
        }
        return $result;
    }

    public function insert($item, $tabName)
    {
        $this->log('write', "INSERT INTO {$tabName} (`".implode('`,`', array_keys($item))."`) VALUES ('".implode("','", $item)."')");
        $this->db->query("INSERT INTO {$tabName} (`".implode('`,`', array_keys($item))."`) VALUES ('".implode("','", $item)."')");
        $this->log('write', "\r\n db_id = {$this->db->insert_id}--->\r\n\r\n");
    }

    public function update($item, $tabName, $where)
    {
        $query = '';
        foreach ($item as $field => $val) {
            $query .= ($query ? ',' : '')."`{$field}` = '{$val}'";
        }
        $this->log('write', "UPDATE {$tabName} SET {$query} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->db->query("UPDATE {$tabName} SET {$query} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->log('write', "\r\n--->\r\n\r\n");
    }

    public function delete($tabName, $where)
    {
        $this->log('write', "DELETE FROM {$tabName} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->db->query("DELETE FROM {$tabName} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->log('write', "\r\n--->\r\n\r\n");
    }

    public function deleteAll()
    {
        # удаляем все разделы и инфоблоки
        $dbSubs = $this->db->get_results("SELECT b.Sub_Class_ID as subClass, a.Subdivision_ID as sub
                                          FROM Subdivision as a, Sub_Class as b
                                          WHERE a.Catalogue_ID = {$this->catalogue}
                                                AND a.Subdivision_ID = b.Subdivision_ID
                                                AND a.code1C LIKE 'IIKO&%'
                                                AND b.Class_ID = '2001'", ARRAY_A);
        if ($dbSubs) {
            $cc = $subs = array();
            foreach ($dbSubs as $dbSub) {
                if (!in_array($dbSub['sub'], $subs)) $subs[] = $dbSub['sub'];
                if (!in_array($dbSub['subClass'], $cc)) $cc[] = $dbSub['subClass'];
            }
            $this->db->query("DELETE FROM Subdivision WHERE Catalogue_ID = {$this->catalogue} AND Subdivision_ID IN ('".implode("','", $subs)."')");
            $this->db->query("DELETE FROM Sub_Class WHERE Catalogue_ID = {$this->catalogue} AND Sub_Class_ID IN ('".implode("','", $cc)."')");
        }

        # удаление товаров
        $this->db->query("DELETE FROM Message2001 WHERE Catalogue_ID = {$this->catalogue} AND xlslist = {$this->key}");
    }

    public function log($type = '', $text = '', $afterName = '')
    {
        $path = $this->path.'/iiko';
        switch ($type) {
            case 'export':
                if (file_exists($path.'/export_log_4.txt')) file_put_contents($path.'/export_log_5.txt', file_get_contents($path.'/export_log_4.txt'));
                if (file_exists($path.'/export_log_3.txt')) file_put_contents($path.'/export_log_4.txt', file_get_contents($path.'/export_log_3.txt'));
                if (file_exists($path.'/export_log_2.txt')) file_put_contents($path.'/export_log_3.txt', file_get_contents($path.'/export_log_2.txt'));
                if (file_exists($path.'/export_log.txt')) file_put_contents($path.'/export_log_2.txt', file_get_contents($path.'/export_log.txt'));
                $this->log = fopen($path.'/export_log.txt', 'wr');
            break;
            case 'order':
                if (!file_exists($path."/order/")) mkdir($path."/order/");
                $this->clearLogs($path."/order/");
                $this->log = fopen($path."/order/order_{$afterName}.txt", 'wr');
            break;
            case 'write': fwrite($this->log, $text);
            break;
            case 'close': fclose($this->log);
            break;
            default: break;
        }
    }

    # очистка логов которые старше месяца
    private function clearLogs($path)
    {
        $date = new \DateTime();
        $date->modify('-1 month');
        foreach (scandir($path) as $fName) {
            if (in_array($fName, array('.', '..')) || is_dir($path.$fName)) continue;
            if (filectime($path.$fName) < $date->format('U')) unlink($path.$fName);
        }
    }

    public function write($type = '')
    {
        echo "<span class='{$type}'></span>";
        flush();
    	ob_flush();
    }
}
