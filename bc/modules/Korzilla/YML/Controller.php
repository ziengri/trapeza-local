<?php

namespace App\modules\Korzilla\YML;

use \nc_Core;
use \Exception;
use App\modules\bitcat\Cron\Controller as Cron;

class Controller
{
    public function __construct()
    {
        $this->setNcCore();
    }
    public function getStatusImport($message_id)
    {
        $current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/a/' . $current_catalogue['login'] . "/yml/import/yml{$message_id}.log";
        if (!file_exists($logPath)) {
            return json_encode(['status' => 1, 'message' => 'Создается ...', 'link' => [], 'item' => 0], JSON_UNESCAPED_UNICODE);
        } else {
            return file_get_contents($logPath) ?: json_encode([]);
        }
    }

    public function setImportCron($message_id, $catalogue, $interval_minut, $count)
    {
        global $PHP_PATH;
        
        $message_id += 0;
        $catalogue += 0;
        $interval_minut += 0;
        $interval_minut = $interval_minut ?: 360;

        if (empty($message_id)) throw new Exception('Нет id выгрузки', 404);
        if (empty($catalogue)) throw new Exception('Нет catalogue выгрузки', 404);

        try {
            $cron = new Cron;
            return $cron->setCmd(
                "{$PHP_PATH} {$_SERVER['DOCUMENT_ROOT']}/bc/modules/default/controller_yml.php 'action=import&message_id={$message_id}&HTTP_HOST={$_SERVER['HTTP_HOST']}'",
                $interval_minut,
                $catalogue,
                $count
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 404);
        }
    }

    public function updateCron($cron_id, $message_id, $params)
    {
        global $PHP_PATH;

        $infinitely = (isset($params['infinitely']) ? $params['infinitely'] += 0 : 0);
        $update = (isset($params['update']) ? $params['update'] += 0 : 0);
        $checked = ($infinitely > 0 || $update ? 1 : 0);
        $count = (!$infinitely && $update ? 1 : 0);
        
        
        $fields = [
            'Checked' => $checked,
            'infinitely' => $infinitely,
            'count' => $count,
            'cmd' => addslashes("{$PHP_PATH} {$_SERVER['DOCUMENT_ROOT']}/bc/modules/default/controller_yml.php 'action=import&message_id={$message_id}&HTTP_HOST={$_SERVER['HTTP_HOST']}'")
        ];
        
        if (isset($params['interval_minutes'])) {
            $fields['interval_minutes'] = ($params['interval_minutes'] >= 1 ? $params['interval_minutes'] : 1);
        }

        if ($update) {
            $fields['last_update'] = 0;

            $current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/a/' . $current_catalogue['login'] . "/yml/import/yml{$message_id}.log", json_encode(['status' => 1, 'message' => 'Создается ...', 'link' => '', 'item' => 0], JSON_UNESCAPED_UNICODE));
        }

        $cron = new Cron();
        return $cron->updateTask($cron_id, $fields);
    }

    public function deleteCron($cron_id)
    {
        $cron = new Cron;
        $cron->deleteTask($cron_id);
    }

    /**
     * Подключения ядра
     * 
     * @return void
     */
    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }
}
