<?php

namespace App\modules\Korzilla\Upload1C\Admin\Models;

class ModelSettingsFrom
{
    protected $pathSetting1C;

    public function __construct()
    {
        global $ROOTDIR, $pathInc;

        $this->pathSetting1C = $ROOTDIR . $pathInc . '/setting1C.json';
    }
    public function obmenList()
    {
        return file_exists($this->pathSetting1C) ? json_decode(file_get_contents($this->pathSetting1C), 1) : [];
    }

    public function updateAutoload($data)
    {
        $obmenList = $this->obmenList();
        if ($data['version'] && !empty($obmenList) && isset($obmenList[$data['version']])) {
            $obmenList[$data['version']]['setting']['autoload'] = $data['checked'];
            file_put_contents($this->pathSetting1C, json_encode($obmenList));
            return $data;
        } else throw new \Exception("Invalid requst", 500);
    }
}
