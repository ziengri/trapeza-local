<?php

namespace App\modules\Korzilla\CatalogItem\Tab\Controllers;

use App\modules\Korzilla\CatalogItem\Tab\Models\ModelSetting;
use App\modules\Korzilla\CatalogItem\Tab\Views\SettingView;

class SettingController
{
    protected $view;

    protected $model;

    public function __construct($pathUserDir)
    {
        $this->model = new ModelSetting($pathUserDir);
        $this->view = new SettingView;
    }

    public function getView(): string
    {
        return $this->view->generate(dirname(__DIR__, 1) . '/src/setting_template.html', $this->model->getData());
    }

    public function getModalSettingOneTab(string $id): string
    {
        $dataTab = $this->model->getDataTab($id);

        list($sub) = explode('|', $dataTab['params']['f_sub']);

        $classSettings = $this->loadSetClass(1, (int) $sub, $dataTab);

        $dataTab['class_settings_html'] = $classSettings;

        return $this->view->generate(dirname(__DIR__, 1) . '/src/modal_setting_tempalte.html', $dataTab);
    }

    public function getModalSettingNewTab(): string
    {
        return $this->view->generate(dirname(__DIR__, 1) . '/src/modal_setting_tempalte.html', ['id' => uniqid('id'), 'Checked' => 1]);
    }

    public function loadSetClass($contentType, $sub, $dataTab)
    {
        $params = [];

        if ($contentType == 1) {
            $params['nc_ctpl_options'] = $this->model->getClassID($this->model->getClassTemplate($sub));
            $params['nc_ctpl'] = $dataTab['params']['nc_ctpl'];
        }

        return $this->view->generate(dirname(__DIR__, 1) . '/src/classSettings.html', $params);
    }

    public function save($data)
    {
        return $this->model->saveTab($data);
    }

    public function draggedTab($data)
    {
        return $this->model->draggedTab($data);
    }

    public function delete(string $id): array
    {
        return $this->model->deleteTab($id);
    }
}
