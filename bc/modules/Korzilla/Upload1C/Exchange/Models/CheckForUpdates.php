<?php

namespace App\modules\Korzilla\Upload1C\Exchange\Models;


class CheckForUpdates
{
    /**
     * @var nc_Core
     */
    protected $nc_core;

    /**
     * catalogue
     *
     * @var array
     */
    protected $catalogue;
    
    /**
     * pathUserFolder
     *
     * @var string
     */
    protected $pathUserFolder;
    
    /**
     * rootDir
     *
     * @var string
     */
    protected $rootDir;

    protected $setting1C;    
    /**
     * catalogueID
     *
     * @var int
     */
    protected $catalogueID;
    
    /**
     * version
     *
     * @var int
     */
    protected $version;
    
    /**
     * cmd
     *
     * @var string
     */
    protected $cmd;
    
    /**
     * settingField
     *
     * @var array
     */
    protected $settingField = [];

    protected const FILE_SETTING_NAME = 'setting1C.json';
    protected const LALS_UPDATE_TIME = 5 * 60;

    public function __construct($catalogueID, $version, $settingField = [])
    {
        global $PHP_PATH;

        $this->nc_core = \nc_Core::get_object();
        $this->catalogueID = $catalogueID;
        $this->version = $version;
        $this->settingField = $settingField;
        $this->catalogue = $this->nc_core->catalogue->get_by_id($catalogueID);
        $this->rootDir = $_SERVER['DOCUMENT_ROOT'];
        $this->pathUserFolder = $this->rootDir . '/a/' . $this->catalogue['login'];
        $this->getSetting();

        $this->cmd = "{$PHP_PATH} {$this->rootDir}/bc/modules/Korzilla/Upload1C/Exchange/cron.php 'catalogueID={$this->catalogueID}&version={$this->version}&HTTP_HOST={$_SERVER['HTTP_HOST']}'";
    }

    protected function getSetting()
    {
        $path = $this->pathUserFolder . '/' . self::FILE_SETTING_NAME;

        if (!file_exists($path)) {
            if (empty($this->settingField)) throw new \Exception("Файл с настройками не найден", 500);

            $this->setting1C = [
                $this->version => [
                    'name' => "Обмен {$this->version}",
                    'path' => $this->settingField['path'],
                    'update_time' => $this->settingField['update_time'],
                    'setting' => ['autoload' => 0]
                ]
            ];
            $this->setSetting();

        } else {
            $this->setting1C = json_decode(file_get_contents($path), 1);
        }

        if (!is_array($this->setting1C)) throw new \Exception("Файл настроек не коретен", 500);

        if (!isset($this->setting1C[$this->version])) {
            $this->setting1C[$this->version] = [
                'name' => "Обмен {$this->version}",
                'path' => $this->settingField['path'],
                'update_time' => $this->settingField['update_time'],
                'setting' => ['autoload' => 0]
            ];

            $this->setSetting();
        }

        if (!empty($this->settingField)) {
            $this->setting1C[$this->version]['update_time'] = $this->settingField['update_time'];
            $this->setting1C[$this->version]['path'] = $this->settingField['path'];
            $this->setSetting();
        }
    }

    public function checkFolder()
    {
        $array = array_diff(scandir($this->setting1C[$this->version]['path']), array('.', '..'));
        $fileUpdateTime = false;

        foreach ($array as $value) {
            $path = $this->setting1C[$this->version]['path'] . $value;
            if (!file_exists($path) || !is_file($path)) continue;
            $fileTime = filectime($path);
            if ($fileTime > $fileUpdateTime) $fileUpdateTime = $fileTime;
        }
        if ($fileUpdateTime) $fileUpdateTime = $fileUpdateTime + self::LALS_UPDATE_TIME < time();

        return $fileUpdateTime;
    }

    public function setSetting()
    {
        if (!file_put_contents($this->pathUserFolder . '/' . self::FILE_SETTING_NAME, json_encode($this->setting1C))) {
            throw new \Exception("Не удалось записать настройки", 1);
        }
    }

    public function setCheckCron($Cron)
    {
        try {
            if (!$this->setting1C[$this->version]['setting']['autoload']) return true;
            
            if ($this->setting1C[$this->version]['cron_id']) {
                return $this->updateCheckCron($Cron, $this->setting1C[$this->version]['cron_id']);
            }
            $this->setting1C[$this->version]['cron_id'] = $Cron->setCmd(
                $this->cmd,
                1,
                $this->catalogueID,
                -1
            );
            $this->setSetting();
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 404);
        }
    }

    public function updateCheckCron($Cron, $cron_id)
    {
        $fields = [
            'Checked' => 1,
            'cmd' => $this->cmd,
            'infinitely' => 1
        ];

        return $Cron->updateTask($cron_id, $fields);
    }

    public function disabledCheckCron($Cron)
    {
        $fields = [
            'Checked' => 0,
            'cmd' => $this->cmd,
        ];

        return $Cron->updateTask($this->setting1C[$this->version]['cron_id'], $fields);
    }
}
