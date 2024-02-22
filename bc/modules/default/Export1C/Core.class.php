<?php

class Core1C
{

    public $data = [];

    public $groups = [];

    public $items = [];

    public $result = [];

    public $filesNew = [];

    public function __construct($paramsExport)
    {
        global $v1c;
        $this->nc_core = nc_Core::get_object();
        $this->db = $this->nc_core->db;
        $this->current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $this->catalogue =  $this->current_catalogue['Catalogue_ID'];
        $this->paramsExport = $paramsExport;

        if (!is_object($this->db)) {
            die('Нет подключения к db!!!');
        }
        if (!is_numeric($this->catalogue)) {
            die('Нет ид сайта!!!');
        }

        $this->login = $this->current_catalogue['login'];
        $this->path1C = $_SERVER['DOCUMENT_ROOT'] . "/a/{$this->login}/1C{$v1c}";
        
        $this->pathLog = $this->path1C . 'log1c.log';

        $this->paternPars = ['import', 'offers'];

        $this->main();
    }

    public function main()
    {
        $this->scanDir1C();
        $this->filesNew = array_shift($this->files);
        $lastExport = $this->db->get_var("SELECT file1Ctime FROM Catalogue  WHERE Catalogue_ID = '{$this->catalogue}'");
        if ($this->filesNew['time'] <= $lastExport && !$this->paramsExport['notest']) {
            die('Файлы уже выгружены');
        }

        $this->ParsXML();
        $this->paramsExport['time'] = $this->filesNew['time'];
        
        if (count($this->result) > 0) {
            $this->export();
        }
        $this->db->query("UPDATE Catalogue SET file1Ctime = '{$this->filesNew['time']}' WHERE Catalogue_ID = '{$this->catalogue}'");
    }

    public function scanDir1C()
    {
        $listsXml = glob($this->path1C . '/*[!{_old}].xml');

        if (empty($listsXml)) {
            die('Файлы не найдены!!!');
        }

        $files = [];
        foreach ($listsXml as $listXml) {
            $time = $this->getTimeFile($listXml);
            $files[$time]['path'][] = $listXml;
            $files[$time]['time'] = $time;
        }
        krsort($files);
        $this->files = $files;
    }

    public function getTimeFile($url)
    {
        $file = file_get_contents($url, 0);
        $xml = new SimplexmlElement($file);
        $dateFile = (string) $xml['ДатаФормирования'];

        if ($dateFile) {
            return strtotime($dateFile);
        } else {
            die('Не удалось получить время файла!!!');
        }
    }

    public function ParsXML()
    {
        foreach ($this->filesNew['path'] as $path) {
            $parsControler = new Parser($path, $this->result);
            $this->result = $parsControler->getResult();
        }
    }

    public function export()
    {
        $export = new Export($this->result, $this->paramsExport);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function &__get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Неопределённое свойство в __get(): ' . $name .
            ' в файле ' . $trace[0]['file'] .
            ' на строке ' . $trace[0]['line'],
            E_USER_NOTICE
        );
        return null;
    }
}
