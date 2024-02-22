<?php

namespace App\modules\Korzilla\Excel\Import;

use App\modules\Korzilla\Excel\Import\ImportExcel;
use App\modules\bitcat\Cron\Controller as Cron;
use App\modules\bitcat\Cron\Process;

class Controller
{
    public function getSettingView(int $Catalogue_ID, $powerseo = false)
    {
        $import = new ImportExcel($Catalogue_ID);
        $params = $import->getFields();
        $params['process'] = $import->getProcess();
        $params['powerseo'] = $powerseo;
        return $this->render('setting.html', $params);
    }

    public function getCatalog(array $params)
    {
        global $PHP_PATH;

        $import = new ImportExcel($params['catalogue']);

        $import->procesLog(['message' => 'Смотрим файлы ...'], true);
        $import->setSetting(['get_photo_zip' => ($params['get_photo_zip'] ? 1 : 0)]);
        $import->setFields($params['field']);

        $Process = new Process("{$PHP_PATH} {$_SERVER['DOCUMENT_ROOT']}/bc/modules/Korzilla/Excel/Import/cront.php 'Catalogue_ID={$params['catalogue']}&HTTP_HOST={$_SERVER['HTTP_HOST']}'");

        if (!($Process->getPid() + 0)) {
            $import->procesLog(['message' => "Не удалось прочитать каталог", 'status' => 1], true);
        }
    }

    public function getProcess(int $Catalogue_ID)
    {
        $import = new ImportExcel($Catalogue_ID);

        return $import->getProcess();
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
