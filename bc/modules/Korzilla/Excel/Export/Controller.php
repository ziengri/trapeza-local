<?php
namespace App\modules\Korzilla\Excel\Export;

ini_set('memory_limit', '-1');
set_time_limit(0);

use App\modules\Korzilla\Excel\Export\ExportExcel2;
use Custom\Excel\Export\ExportExcelCustom;
use App\modules\bitcat\Cron\Process;
use Exception;

class Controller
{
    public static function import(int $Catalogue_ID, int $messageID)
    {
        try {
            if (class_exists('Custom\Excel\Export\ExportExcelCustom')) {
                $import = new ExportExcelCustom($Catalogue_ID, $messageID);
            } else {
                $import = new ExportExcel2($Catalogue_ID, $messageID);
            }
            $import->export();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function run(int $Catalogue_ID, int $messageID)
    {
        $cmd = "/opt/php71/bin/php " . __DIR__ . "/index.php 'Catalogue_ID={$Catalogue_ID}&messageID={$messageID}&action=import&HTTP_HOST={$_SERVER['HTTP_HOST']}'";
        $process = new Process($cmd);
        return $process->getPid() + 0;
    }

    public static function process(int $Catalogue_ID, int $messageID)
    {
        if (class_exists('ExportExcelCustom')) {
            $import = new ExportExcelCustom($Catalogue_ID, $messageID);
        } else {
            $import = new ExportExcel2($Catalogue_ID, $messageID);
        }
        
        return json_encode($import->getProcess());
    }
}
