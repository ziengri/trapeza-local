<?php

class Search
{
    public function __construct($find, $nameModule)
    {
        global $pathInc, $ROOTDIR;

        if ($find === '') {
            throw new Exception("Empty field find", 1);
        }
        $this->cachePath = $ROOTDIR . $pathInc . '/suppliersCache.json';
        $this->find = $find;
        $this->nameModule = $nameModule;
    }

    public function saveItems($items)
    {
        global $db;

        $requiredFields = ['Catalogue_ID', 'Keyword'];
        $additionalFields = [
            'import_source' => "'{$this->nameModule}'",
            'keyword_find' => "'|{$this->findKey}|'",
            'LastUpdated' => 'NOW()',
            'Created' => 'NOW()',
            'timestamp_export' => time()
        ];

        // Сбор полей в массиве товара
        $fields = array_reduce($items, function ($carry, $item) {
            foreach ($item as $field => $value) {
                if (!in_array($field, $carry)) {
                    $carry[] = $field;
                }
            }
            return $carry;
        }, []);

        // Добовления доп полей
        foreach ($additionalFields as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $fields)) {
                $fields[] = $fieldName;
            }
        }
        if (count(array_intersect($fields, $requiredFields)) != count($requiredFields)) {
            throw new Exception("default fields missing (" . implode(', ', $requiredFields) . ")", 1);
        }

        // Значения полей
        $values = array_reduce($items, function ($carry, $item) use ($fields, $additionalFields) {
            $row = [];
            foreach ($fields as $field) {
                $row[] = ($item[$field] ? "'" . str_replace("'", '"', $item[$field]) . "'" : ($additionalFields[$field] ?: "''"));
            }
            $carry[] = "(" . implode(',', $row) . ")";
            return $carry;
        }, []);

        // Обновить дубликаты кроме обезательных полей
        $dublicateUpdate = array_reduce($fields, function ($carry, $field) use ($requiredFields) {
            if (!in_array($field, $requiredFields) && $field != 'keyword_find') {
                $carry .= "{$field} = VALUES({$field}),";
            }
            return $carry;
        }, 'ON DUPLICATE KEY UPDATE ');

        $dublicateUpdate .= " keyword_find = CONCAT('|{$this->findKey}|', REPLACE(VALUES(keyword_find), '|{$this->findKey}|', ''))";

        $sql = "INSERT INTO Message2001 (" . implode(',', $fields) . ") VALUES " . implode(',', $values) . trim($dublicateUpdate, ',');
        return $db->query($sql);
    }

    public function setCache($result = true)
    {
        $cache = $this->getCache();
        $cache[$this->findKey]['modules'][$this->nameModule] = $result;
        $cache[$this->findKey]['time'] = time();

        file_put_contents($this->cachePath, json_encode($cache));
    }
    
    public function isCacheFind()
    {
        $cache = $this->getCache();

        return $cache[$this->findKey]['modules'][$this->nameModule] ?: false;
    }

    private function getCache()
    {
        $cache = json_decode((file_get_contents($this->cachePath) ?: "{}"), 1);
        $oneHourInSeconds = 3600;
        foreach ($cache as $key => $value) {
            if ((time() - (int) $value['time']) > $oneHourInSeconds) {
                unset($cache[$key]);
            }
        }
        return $cache;
    }

    private function setFindKey()
    {
        return preg_replace('/[^a-zа-я0-9]/ui', '', mb_strtolower($this->find));
    }

    private function __get($name)
    {
        switch ($name) {
            case 'findKey':
                return $this->setFindKey();
                break;
            default:
                return $this->$name;
                break;
        }
    }
}
