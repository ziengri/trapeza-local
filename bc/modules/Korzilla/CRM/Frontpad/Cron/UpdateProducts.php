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

$updater = new UpdateProducts($catalogue);
$updater->handle();
# [END]

use App\modules\Korzilla\CRM\Frontpad\FrontpadController;
use App\modules\Korzilla\Loker\Loker;

class UpdateProducts
{
    const LOCK_TIME_LIMIT = 300;

    private $catalogueId;
    /**
     * @var Loker
     */
    private $locker;
    /**
     * @var FrontpadController
     */
    private $frontapdController;

    public function __construct(int $catalogueId)
    {
        $this->catalogueId = $catalogueId;
        $this->locker = new Loker('frontpadUpdateProducts');
        $this->frontapdController = new FrontpadController();
    }

    public function handle()
    {
        if (!$this->frontapdController->isOn() || $this->locker->isLocked(self::LOCK_TIME_LIMIT)) {
            echo 'Фронтпад выключен или идет выгрузка';
            return;
        }
        $this->locker->lock();
        try {
            $this->frontapdController->updateSyncedProducts($this->catalogueId);
        } catch (Exception $e) {
            $this->writeErrorLog($e->getMessage());
        }
        $this->clearOldLogs();
        $this->locker->unlock();
    }

    private function writeErrorLog($message)
    {
        global $pathInc;

        $logDir = $_SERVER['DOCUMENT_ROOT'].$pathInc.'/log';
        if (!file_exists($logDir)) mkdir($logDir, '0666');
        $logDir .= '/frontpad';
        if (!file_exists($logDir)) mkdir($logDir, '0666');
        $logDir .= '/exportErrors';
        if (!file_exists($logDir)) mkdir($logDir, '0666');

        $log = date('d.m.Y H:i');
        $log .= PHP_EOL."Сообщение: ".$message;

        $filePath = $logDir.'/'.date('d_m_Y__H_i').'.log';

        file_put_contents($filePath, $log);
    }

    private function clearOldLogs()
    {
        global $pathInc;

        $logDir = $_SERVER['DOCUMENT_ROOT'].$pathInc.'/log/frontpad/exportErrors';

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
