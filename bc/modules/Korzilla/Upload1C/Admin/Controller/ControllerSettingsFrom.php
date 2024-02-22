<?php

namespace App\modules\Korzilla\Upload1C\Admin\Controller;


class ControllerSettingsFrom
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getForm()
    {
        return $this->renderTemplate('setting_form', ['obmenList' => $this->model->obmenList()]);
    }

    public function updateAutoload($data) {

        try {
            $res = array_merge(['status' => 1], $this->model->updateAutoload($data));
        } catch (\Exception $e) {
            $res = ['message' => $e->getMessage(), 'status' => 0];
        }
        return $res;
    }

    private function renderTemplate($view, $params = [])
    {
        $view = dirname(__DIR__) . '/View/' . $view . '.php';
        var_dump($view);
        if (!empty($params)) extract($params);
        if (file_exists($view)) {
            ob_start();
            include $view;
            $result = ob_get_flush();
            ob_end_clean();
            return $result;
        } else {
            throw new \Exception(sprintf('Файл %s не найден.', $view));
        }
    }
}
