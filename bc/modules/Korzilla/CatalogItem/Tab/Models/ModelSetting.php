<?php

namespace App\modules\Korzilla\CatalogItem\Tab\Models;

class ModelSetting
{
    protected $data = [];

    protected $pathUserFiel = '';

    protected $fileName = '/full_cart_tab.json';

    protected $pathDefaultData = '/src/full_cart_tab_default.json';

        
    /**
     * nc_core
     *
     * @var \nc_Core
     */
    protected $nc_core;

    public function __construct($pathUserDir)
    {
        $this->nc_core = \nc_Core::get_object();
        $this->pathUserFiel = $pathUserDir . $this->fileName;
    }

    public function getData()
    {
        if (!empty($this->data)) return $this->data;
        if (file_exists($this->pathUserFiel)) $this->data = json_decode(file_get_contents($this->pathUserFiel), 1) ?: [];
        else $this->data = json_decode(file_get_contents(dirname(__DIR__, 1) . $this->pathDefaultData), 1) ?: [];
        return $this->data;
    }

    public function getDataTab(string $id): array
    {
        $data = $this->getData();
        if (isset($data[$id])) return $data[$id];
        else throw new \Exception('Нет такого таба', 400);
    }

    public function getClassTemplate(int $sub): int
    {
        if (!$sub) return 0; 
        else {
            return $this->nc_core->db->get_var(
                "SELECT 
                    Class_ID 
                FROM 
                    Sub_Class 
                WHERE 
                    Subdivision_ID = {$sub} 
                LIMIT 0,1"
            ) ?: 0;
        }
    }

    public function getClassID(int $classid): array
    {
        if ($classid) {
            $ctplarr = $this->nc_core->db->get_results(
                "SELECT 
                    Class_ID, 
                    Class_Name 
                FROM 
                    Class 
                WHERE 
                    ClassTemplate = {$classid} 
                    AND Class_Name NOT LIKE '%KORZILLA%'
                    AND Class_ID NOT IN (2072, 2019)
                ORDER BY 
                    Class_Name",
                    ARRAY_A
            ) ?: [];
        }

        $result = [];
   
        if ($ctplarr) $result[''] = '- не выбран -';

        foreach ($ctplarr as $ctpl) {
            $result[$ctpl['Class_ID']] =  $ctpl['Class_Name'];
        }
        return $result;
    }

    public function saveTab(array $data): array
    {
        try {
            $id = $data['id'];
            if (!$id) throw new \Exception('Нет id элемента', 500);
            
            $cleanData = [
                'id' => $id,
                'name' => $data['name'],
                'params' => $data['params'],
                'Checked' => $data['Checked'] ?: 0,
            ];
    
            $allData = $this->getData();
    
            $allData[$id] = $cleanData;
    
            if (!file_put_contents($this->pathUserFiel, json_encode($allData,  JSON_UNESCAPED_UNICODE))) {
                throw new \Exception('Ошибка выполнения запроса', 500);
            }

            return [
                "title" => "ОК",
                "succes" => "Настройки сохранены",
                "reloadtab" => "1",
                "modal" => "close"
            ];

        } catch (\Exception $e) {
            return [
                "title" => "Ошибка",
                "error" => $e->getMessage()
            ];
        }
    }

    public function draggedTab(array $data)
    {
        $allData = $this->getData();
        $result = [];
        if (empty($data)) return ['error' => 1];
        foreach ($data as $id) {
            $result[$id] = $allData[$id];
        }
        if (count($allData) != count($result)) return ['error' => 1];
        if (!file_put_contents($this->pathUserFiel, json_encode($result,  JSON_UNESCAPED_UNICODE))) {
            return ['error' => 1];
        }

        $this->data = $result;

        return ['succes' => 1];
    }

    public function deleteTab(string $id)
    {
        try {
            $allData = $this->getData();
            unset($allData[$id]);
            if (!file_put_contents($this->pathUserFiel, json_encode($allData,  JSON_UNESCAPED_UNICODE))) {
                throw new \Exception('Ошибка выполнения запроса', 500);
            }
            return [
                "title" => "ОК",
                "succes" => "Таб удален",
                "reloadtab" => "1",
                "modal" => "close"
            ];
        } catch (\Throwable $th) {
            return [
                "title" => "Ошибка",
                "error" => $e->getMessage()
            ];
        }

    }
}