<?php
# [START]
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? $argv[1] ?? '';
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?: getRootDir(6);

require_once $_SERVER['DOCUMENT_ROOT']."/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/bc/connect_io.php";

global $nc_core, $current_catalogue, $catalogue;

if (
    !($current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']), true))
    || !isValidCatalogue($current_catalogue)
) {
    die('Не удалось определить сайт');
}

$catalogue = (int) $current_catalogue['Catalogue_ID'];

require_once $_SERVER['DOCUMENT_ROOT']."/bc/modules/default/function.inc.php";

$sender = new SendOrders($catalogue);
$sender->handle();
# [END]

use App\modules\Korzilla\CRM\Planfix\PlanfixController;
use App\modules\Korzilla\Loker\Loker;

class SendOrders
{
    const LOCK_TIME_LIMIT = 300;
    /**
     * Количество секунд по истечении которых 
     * перестать отправлять заказы после их создания
     */
    const ORDERS_TIME_LIMIT = 259200; # 3 дня

    private $catalogueId;
    /**
     * @var nc_Core
     */
    private $nc_core;
    /**
     * @var Loker
     */
    private $locker;
    /**
     * @var PlanfixController
     */
    private $planfixController;

    public function __construct(int $catalogueId)
    {
        $this->catalogueId = $catalogueId;
        $this->nc_core = nc_Core::get_object();
        $this->locker = new Loker('planfixSendOrders');
        $this->planfixController = new PlanfixController();
    }

    public function handle()
    {
        if ($this->locker->isLocked(self::LOCK_TIME_LIMIT)) {
            return;
        }
        
        $this->locker->lock();

        foreach ($this->getOrders() as $order) {
            $this->locker->lock();
            try {
                $response = $this->planfixController->sendOrderByWebhook($order);
            } catch (Exception $e) {
                $this->writeLog($order, $e->getMessage());
                continue;
            }
            $this->writeLog($order, json_encode($response, JSON_UNESCAPED_UNICODE));
            $this->updateOrder($order, $response);
        }

        $this->clearOldLogs();
        $this->locker->unlock();
    }

    private function getOrders(): array
    {
        $sql = "SELECT * 
                FROM `Message2005` 
                WHERE `Catalogue_ID` = {$this->catalogueId}
                    AND (`code` = '' OR `code` IS NULL)
                    AND `Created` > '".$this->getOrdersTimeLimit()."'";

        return $this->nc_core->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function getOrdersTimeLimit(): string
    {
        return (new DateTime())->modify("- ".self::ORDERS_TIME_LIMIT." sec")->format('Y:m:d H:i:s');
    }

    private function writeLog($order, $message)
    {
        global $pathInc;

        $logDir = $_SERVER['DOCUMENT_ROOT'].$pathInc.'/log';
        if (!file_exists($logDir)) mkdir($logDir);
        $logDir .= '/Planfix';
        if (!file_exists($logDir)) mkdir($logDir);
        $logDir .= '/orders';
        if (!file_exists($logDir)) mkdir($logDir);

        $log = date('d.m.Y H:i');
        $log .= PHP_EOL."Сообщение: ".$message;

        $filePath = $logDir.'/'.$order['Message_ID'].'.log';
        
        if (file_exists($filePath)) {
            $log = PHP_EOL.'-------------'.PHP_EOL.$log;
        }

        file_put_contents($filePath, $log, FILE_APPEND);
    }

    private function updateOrder($order, $response)
    {
        $sql = "UPDATE `Message2005` SET `code` = '{$response['task']}' WHERE `Message_ID` = {$order['Message_ID']}";
        $this->nc_core->db->query($sql);
    }

    private function clearOldLogs()
    {
        global $pathInc;

        $logDir = $_SERVER['DOCUMENT_ROOT'].$pathInc.'/log/Planfix/orders';

        if (!file_exists($logDir)) return;

        $date = (new DateTime())->modify('-2 week')->format('U');

        foreach (scandir($logDir) as $file) {
            $filePath = $logDir.'/'.$file;

            if (in_array($file, ['.', '..']) || is_dir($filePath)) continue;

            if (filectime($filePath) < $date) {
                unlink($filePath);
            }
        }
    }
}

/**
 * Получить корневую директорию
 * 
 * @author Олег Хрулёв
 * 
 * @param int $deepLevel уровень глубены относительно корня проекта
 * 
 * @return string 
 */
function getRootDir(int $deepLevel = 0): string
{
    $pathArr = explode('/', __DIR__);
    return implode('/', array_slice($pathArr, 0, count($pathArr) - $deepLevel));
}

/**
 * Обязательно к использованию из-за особенностей работы метода \nc_Catalogue::get_by_host_name
 * 
 * @author Олег Хрулёв
 * 
 * @param array $current_catalogue сайт
 * 
 * @return bool
 */
function isValidCatalogue($current_catalogue): bool
{
    $domains = [$current_catalogue['Domain'] ?: 'nodomen_qwertyxyz'];
    foreach (explode("\n", $current_catalogue['Mirrors']) as $mirror) {
        $domains[] = strtolower(trim(str_replace(array('http://', 'https://', '/'), '', $mirror))) ?: 'nodomen_qwertyxyz';
    }
    return in_array($_SERVER['HTTP_HOST'], $domains, true);
}