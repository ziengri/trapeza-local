<?php

namespace App\modules\Korzilla\Excel\Export\ExportSite;

use App\modules\Korzilla\Excel\Export\ExportSite\Model\Export;
use App\modules\bitcat\Cron\Process;
use App\modules\Korzilla\Excel\ProcessLog;


class Controller
{
    public function getSettingView(int $Catalogue_ID)
    {
        return $this->render('setting.html');
    }

    public function setCatalog(int $Catalogue_ID)
    {
        global $PHP_PATH;
        $export = new Export($Catalogue_ID);
        $ProcessLog = new ProcessLog($export->pathProcess, true);
        $stausFile = $export->setCatalog();


        if (!$stausFile['status']) {
            $ProcessLog
                ->setError($stausFile['message'])
                ->setStatus($export::STATUS_ERROR)
                ->save();
            return false;
        }
        $ProcessLog
            ->setMessage('Файл загружен, начинаем обрабатывать ...')
            ->setStatus($export::STATUS_PROCESSING)
            ->save();

        try {
            $Process = new Process("{$PHP_PATH} {$_SERVER['DOCUMENT_ROOT']}/bc/modules/Korzilla/Excel/Export/ExportSite/cron.php 'Catalogue_ID={$Catalogue_ID}&HTTP_HOST={$_SERVER['HTTP_HOST']}'");
            $pidID = $Process->getPid() + 0;
            if (!$pidID) throw new \Exception("Не удалось прочитать каталог");
            $export->setSetting(['pid_id' => $pidID]);
            
        } catch (\Exception $e) {
            $ProcessLog
                ->setError($e->getMessage())
                ->setStatus($export::STATUS_ERROR)
                ->save();
        }
    }

    public function getProcess(int $Catalogue_ID)
    {
        $export = new Export($Catalogue_ID);
        $pidID = $export->getSettings()['pid_id'];
        $statusProcess = $export->getProcess();
        $statusEnd = [$export::STATUS_COMPLITE, $export::STATUS_ERROR, $export::STATUS_NEW];

        $Process = new Process();
        $ststusScript  = $Process->setPid($pidID)->status();
       
        if (!$ststusScript && is_array($statusProcess) && !in_array($statusProcess['status'], $statusEnd)) {
            $ProcessLog = new ProcessLog($export->pathProcess);
            $ProcessLog->setError('Скрипт неожиданно прервался, необходимо загрузить еще раз файл.')
            ->setStatus($export::STATUS_ERROR)
            ->save();
            $statusProcess = $export->getProcess();
        }

        return $statusProcess;
    }

    protected function render($view, $params = [])
    {
        $path = __DIR__ . '/View/template/' . $view;
        ob_start();
        if (!file_exists($path)) return '';
        extract($params);
        require $path;
        return ob_get_clean();
    }
}
