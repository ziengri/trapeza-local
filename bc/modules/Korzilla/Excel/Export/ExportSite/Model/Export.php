<?php

namespace App\modules\Korzilla\Excel\Export\ExportSite\Model;

use App\modules\Korzilla\Excel\ProcessLog;
use nc_Core;
use Exception;

class Export
{
    protected $settingExport;

    protected const ROOT_GRUOP = [
        'Subdivision_ID' => 0,
        'Sub_Class_ID' => 0,
        'Class_ID' => 2001,
        'Class_Template_ID' => 0,
        'Hidden_URL' => '/',
    ];

    public const STATUS_NEW = 0;
    public const STATUS_PRI_PARSING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_COMPLITE = 3;
    public const STATUS_ERROR = 4;

    protected const NAME_REPLACE = ["#" => " ", "_" => " ", "=" => " ", "!" => " "];

    protected $handle;
    protected $gruops = [];
    protected $gruopsSite = [];
    protected $indexRow = 0;
    protected $itelGruopOld = [];
    protected $process = [];
    protected $totalRow = 0;
    public $pathProcess = '';
    protected $ProcessLog = null;
    public $ROOTDIR = null;
    public $setting = null;
    public $catalogue = null;
    public $nc_core = null;
    public $logPath = null;
    public $path = null;
    public $fieldsItemUpdate = null;
    public $whiteListSubdivision = null;
    public $whiteListItem = null;
    public $paramsList = null;
    public $fieldsGruopUpdate = null;
    public $delimiter = null;
    public $code = null;
    public $current_sub = null;

    public function __construct(int $Catalogue_ID)
    {
        $this->setNcCore();
        $this->catalogue = $this->nc_core->catalogue->get_by_id($Catalogue_ID);
        $this->setting = getSettings();
        $this->ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
        $this->path = $this->ROOTDIR . '/a/' . $this->catalogue['login'] . '/reverse_unloading/export_excel/';
        $this->logPath = $this->path . 'log.txt';
        $this->pathProcess = $this->path . 'process.json';
        @mkdir($this->path);
    }


    public function parsing()
    {
        $this->ProcessLog = new ProcessLog($this->pathProcess);

        $this->ProcessLog
            ->setMessage('Производим расчеты ...')
            ->setStatus(self::STATUS_PRI_PARSING)
            ->save();

        $this->setLog('Старт !!!', 1);
        $this->settingExport = $this->getSettings();

        if (!file_exists($this->settingExport['file'])) {
            $this->ProcessLog
                ->setMessage('')
                ->setStatus(self::STATUS_ERROR)
                ->setError('Закаченый файл не найден')
                ->save();
            throw new Exception("Not find file from path " . $this->settingExport['file'], 1);
        }

        if (!($this->handle = fopen($this->settingExport['file'], "r"))) {
            $this->ProcessLog
                ->setMessage('')
                ->setStatus(self::STATUS_ERROR)
                ->setError('Не удалось октрыть файл')
                ->save();
            throw new Exception("Не удалось октрыть файл " . $this->settingExport['file'], 1);
        }

        $this->gruopsSite = $this->getGruopsSite();
        $this->whiteListSubdivision = $this->explain("Subdivision");
        $this->whiteListItem = $this->explain('Message2001');

        foreach ($this->setting['lists_params'] as $value) {
            $this->paramsList[$value['keyword']] = $value;
        }

        $this->delimiter = $this->getDelimiter();

        while (($row = fgetcsv($this->handle, 1000000000, $this->delimiter)) !== false) {
            $this->totalRow++;
        }
        fseek($this->handle, 0);

        $this->ProcessLog
            ->setMessage('Выгружаем ')
            ->setStatus(self::STATUS_PROCESSING)
            ->setProcent($this->getProcent())
            ->save();

        while (($row = fgetcsv($this->handle, 1000000000, $this->delimiter)) !== false) {
            ++$this->indexRow;
            if ($this->code !== 'UTF-8') {
                foreach ($row as $key => $value) {
                    $row[$key] = mb_convert_encoding($value, 'UTF-8', $this->code);
                }
            }
            if ($this->indexRow == 2) {
                $fields['gruop'] = $row;
                $indexGruopName = array_search('Subdivision_Name', $fields['gruop']);
            }
            if ($this->indexRow == 3) {
                $fields['item_name_col'] = $row;
            }
            if ($this->indexRow == 4) {
                $fields['item'] = $row;
                $this->fieldsItemUpdate = $this->getFieldsUpdate($fields['item'], $this->whiteListItem);
                $this->setLog("Столбцы обновления товара " . print_r($this->fieldsItemUpdate, 1));
                $this->fieldsGruopUpdate = $this->getFieldsUpdate($fields['gruop'], $this->whiteListSubdivision);
                $this->setColsParamsName($fields['item'], $fields['item_name_col']);
            }

            if ($this->indexRow <= 4) continue;
            $isItem = empty(trim($row[$indexGruopName]));
            $value = $this->getRowValue(($isItem ? $fields['item'] : $fields['gruop']), $row);
            
            if (empty($value)) {
                $this->setLog("Пустая строка {$this->indexRow}");
                continue;
            }

            if ($isItem) {
                $this->itemConstruct($value);
            } else {
                $this->subConstruct($value);
            }
            $this->ProcessLog->setProcent($this->getProcent());
            if ($this->indexRow % 100 == 0) $this->ProcessLog->save();
        }
        if (!empty($this->itelGruopOld)) {
            $log = $this->update('Message2001', ['Checked' => 0], ['Checked'], "Message_ID IN (" . implode(',', array_keys($this->itelGruopOld)) . ")");
        }
        $this->setLog("Выключено товаров :{$log}");
        if (!empty($this->gruopsSite)) {
            $log = $this->update('Subdivision_ID', ['Checked' => 0], ['Checked'], "Subdivision_ID IN (" . implode(',', array_keys($this->gruopsSite)) . ")");
        }
        $this->setLog("Выключено разделов: {$log}");

        $this->ProcessLog
            ->setProcent(100)
            ->setStatus(self::STATUS_COMPLITE)
            ->setMessage('Каталог загружен на сайт ' . date('Y-m-d H:i:s'))
            ->save();

        $this->setLog("Финиш!!!");
    }

    protected function itemConstruct($rowValue)
    {
        if (!isset($rowValue['name']) || empty(trim($rowValue['name']))) {
            $log = "Нет наименования товара на строке: {$this->indexRow}";
            $this->ProcessLog->setWarning([$log]);
            $this->setLog($log);
            return false;
        } else {
            $this->setLog("Наименования товара на строке: {$this->indexRow}");
        }
        $item = [
            'paramRow' => [],
            'paramCol' => []
        ];
        foreach ($rowValue as $field => $value) {
            switch ($field) {
                case 'name':
                    $item['name'] = trim(strtr($value, self::NAME_REPLACE));
                    break;
                case stristr($field, "paramName") || stristr($field, "paramValue"):
                    $index = (int) str_replace(['paramName', 'paramValue'], '', $field);
                    if (stristr($field, "paramName")) {
                        if (!empty($value)) {
                            $id = 'param_' . md5($value);
                            $item['paramRow'][$index]['keyword'] = $id;
                            $this->addParamsList(['keyword' => $id, 'name' => $value]);
                        }
                    } else {
                        if (isset($item['paramRow'][$index])) $item['paramRow'][$index]['value'] = $value;
                    }
                    break;
                case 'art':
                    if (!empty(trim($value))) $item['art'] = $value;
                    break;
                case 'code':
                    if (!empty(trim($value))) $item['code'] = $value;
                    break;
                case 'firstprice':
                    if (trim($value) == '+') $value = 1;
                    if (trim($value) == '-') $value = 0;
                    if (!empty(trim($value))) $item['firstprice'] = $value;
                    break;
                case stristr($field, "price"):
                    if (stristr($value, "дог")) $item['dogovor'] = 1;
                    $item[$field] = str_replace(",", ".", preg_replace("([^0-9,\.])", "", $value));
                    break;
                case stristr($field, "stock"):
                    if (stristr(trim($value), 'Да')) $value = 1;
                    if (stristr(trim($value), 'Нет')) $value = 0;
                    $item[$field] = round(preg_replace("([^0-9,\.])", "", $value));
                    break;
                case mb_strstr($field, 'param_'):
                    $item['paramCol'][] = ['keyword' => preg_replace('/^param_/', '', $field), 'value' => str_replace('|', '', $value)];
                    break;
                case 'currency':
                    $currency = [
                        1 => [1, 'rub', 'рубль', 'ruble'],
                        2 => [2, 'usd', 'доллар', 'dollar'],
                        3 => [3, 'eur', 'евро', 'euro'],
                        4 => [4, 'kzt', 'тенге', 'tenge']
                    ];
                    $item[$field] = 1;
                    $value = trim(mb_strtolower($value));
                    foreach ($currency as $num => $patern) {
                        if (in_array($value, $patern)) {
                            $item[$field] = $num;
                            break;
                        }
                    }
                    break;
                case 'Subdivision_IDS':
                    $item[$field] = ',' . trim($value, ",") . ',';
                    break;
                case 'disconttime':
                    $item[$field] = date( 'Y-m-d H:i:s', strtotime($value));
                    break;
                default:
                    $item[$field] = $value;
                    break;
            }
        }


        $item['Catalogue_ID'] = $this->catalogue['Catalogue_ID'];
        $item['Subdivision_ID'] = $this->current_sub['Subdivision_ID'];
        $item['Sub_Class_ID'] = $this->current_sub['Sub_Class_ID'];

        if (!empty($item['paramRow']) || !empty($item['paramCol'])) {
            $item['params'] = array_reduce(array_merge($item['paramRow'], $item['paramCol']), function ($carry, $param) {
                if (!isset($param['value']) || $param['value'] === '') return $carry;
                return $carry .= trim($param['keyword']) . "||" . trim($param['value']) . "|\r\n";
            }, '');

            if (!empty($item['params']) && !isset($this->fieldsItemUpdate['params'])) $this->fieldsItemUpdate = array_merge($this->fieldsItemUpdate, ['params']);
            unset($item['paramRow'], $item['paramCol']);
        }



        if (!$item['Message_ID']) {
            if (!isset($item['Keyword']) || empty($item['Keyword'])) {
                $keyword = $item['name'];

                if (isset($item['art'])) {
                    $keyword .= '-' . $item['art'];
                } else if (isset($item['code'])) {
                    $keyword .= '-' . $item['code'];
                }

                if (isset($item['variablename'])) {
                    $keyword .= '-' . $item['variablename'];
                }

                $item['Keyword'] = $this->tiredel($this->encodestring($keyword, 1));
            }

            $itemInSite = $this->nc_core->db->get_row(
                "SELECT
                    Message_ID,
                    Subdivision_IDS
                FROM 
                    Message2001 
                WHERE 
                    Keyword = '{$item['Keyword']}'
                    AND Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'",
                ARRAY_A
            );
            if ($itemInSite && $itemInSite['Subdivision_ID'] != $this->current_sub['Subdivision_ID']) {
                $itemInSite['Subdivision_IDS'] = ',' . implode(
                    ',',
                    array_unique(array_merge(
                        array_filter(
                            explode(',', $itemInSite['Subdivision_IDS'])
                        ),
                        [$this->current_sub['Subdivision_ID']]
                    ))
                ) . ',';
                $item = [
                    'Message_ID' => $itemInSite['Message_ID'],
                    'Subdivision_IDS' => $itemInSite['Subdivision_IDS']
                ];

                $this->fieldsItemUpdate = array_merge($this->fieldsItemUpdate, ['Subdivision_IDS']);
            }
        }


        if ($item['Message_ID']) {
            $this->fieldsItemUpdate = array_merge($this->fieldsItemUpdate, ['Subdivision_ID', 'Sub_Class_ID']);
            $log = $this->update('Message2001', $item, $this->fieldsItemUpdate, "Message_ID = {$item['Message_ID']}");
        } else {
            $item['Checked'] = ($item['Checked'] ?: 1);
            $item['Message_ID'] = $this->insert('Message2001', $item, $this->whiteListItem);
            $log = $item['Message_ID'] ? 'Создан' : 'Не создан, ошибка в sql запросе';
        }
        unset($this->itelGruopOld[$item['Message_ID']]);
        $this->setLog("Товар {$item['Message_ID']}:{$item['name']} на стороке {$this->indexRow}: {$log}");
    }

    protected function setColsParamsName($fielItem, $fieldItemName)
    {
        foreach ($fielItem as $key => $field) {
            if (!mb_strstr($field, 'param_')) continue;
            $name = $fieldItemName[$key];
            if (!empty(trim($name))) $this->addParamsList(['keyword' => preg_replace('/^param_/', '', $field), 'name' => $name]);
        }
    }

    protected function addParamsList($param)
    {
        if (!isset($this->paramsList[$param['keyword']])) {
            $this->paramsList[$param['keyword']] = [
                'keyword' => $param['keyword'],
                'name' => $param['name'],
                'checked' => 1
            ];
        }
    }

    protected function selectDelimiter($line, $code)
    {
        $line = mb_convert_encoding($line, 'UTF-8', $code);
        $delimiter = preg_replace('/((?:\"|\'.*?\"|\')|(?:\w+|\n|\r|\0|\s|\)|\(|\.|\-))/isUu', '', $line)[0];
        if (in_array($delimiter, [",", ";", "|", "\t"])) {
            return $delimiter;
        } else return false;
    }

    protected function getDelimiter()
    {
        if (($firstLine = fgets($this->handle)) === false) return false;
        $this->code = mb_detect_encoding($firstLine, ['Windows-1251', 'UTF-8', 'ASCII']);

        if (($delimiter = $this->selectDelimiter($firstLine, 'Windows-1251')) !== false) {
            $this->code = 'Windows-1251';
        } elseif (($delimiter = $this->selectDelimiter($firstLine, 'UTF-8')) !== false) {
            $this->code = 'UTF-8';
        } elseif (($delimiter = $this->selectDelimiter($firstLine, 'ASCII')) !== false) {
            $this->code = 'ASCII';
        }

        if (!$delimiter) {
            $this->setLog('Разделитель не найден');
            throw new Exception("Разделитель не найден", 1);
        }

        fseek($this->handle, 0);
        return $delimiter;
    }

    protected function getFieldsUpdate($fields, $whiteFields)
    {
        $result = [];

        foreach ($fields as $field) {
            if (in_array($field, $whiteFields) || mb_strstr($field, 'param_')) $result[] = $field;
        }

        return $result;
    }

    protected function subConstruct($rowValue)
    {
        $this->current_sub = $this->subLevelConstruct($rowValue);

        $parentSub = count($this->gruops) > 1 ? $this->gruops[count($this->gruops) - 2] : self::ROOT_GRUOP;

        $this->current_sub['Catalogue_ID'] = $this->catalogue['Catalogue_ID'];
        $this->current_sub['Parent_Sub_ID'] = $parentSub['Subdivision_ID'];

        if (!$this->current_sub['Subdivision_ID']) {
            $this->current_sub['Hidden_URL'] = $parentSub['Hidden_URL'] . $this->encodestring($rowValue['Subdivision_Name'], 1) . '/';
            $this->current_sub['EnglishName'] = $this->encodestring($rowValue['Subdivision_Name'], 1);
            $this->current_sub = array_merge($this->current_sub, $this->nc_core->db->get_row(
                "SELECT 
                    sub.`Subdivision_ID`,
                    cc.`Sub_Class_ID`,
                    sub.`Hidden_URL`
                FROM 
                Subdivision AS sub,
                    Sub_Class AS cc
                WHERE 
                    sub.Catalogue_ID = '{$this->current_sub['Catalogue_ID']}' 
                    AND sub.Hidden_URL = '{$this->current_sub['Hidden_URL']}'
                    AND sub.Subdivision_ID = cc.Subdivision_ID",
                ARRAY_A
            ) ?: []);
        }

        if ($this->current_sub['Subdivision_ID']) {
            if (!isset($this->gruopsSite[$this->current_sub['Subdivision_ID']])) {
                $log = "ID {$this->current_sub['Subdivision_ID']} на сайте нет!!!";
            } else {
                $log = $this->update('Subdivision', $this->current_sub, $this->fieldsGruopUpdate, " Subdivision_ID = '{$this->current_sub['Subdivision_ID']}'");
                $this->current_sub = array_merge($this->current_sub, $this->gruopsSite[$this->current_sub['Subdivision_ID']]);
            }
        } else {

            $this->current_sub['priority'] = $this->nc_core->db->get_var(
                "SELECT 
                    MAX(priority)
                FROM 
                    Subdivision 
                WHERE Parent_Sub_ID = '{$this->current_sub['Parent_Sub_ID']}'"
            ) ?: 0;
            $this->current_sub['Checked'] = 1;
            $this->current_sub['subdir'] = 3;

            $subID = $this->insert('Subdivision', $this->current_sub, $this->whiteListSubdivision);
            if (!$subID) {
                $this->setLog("Ошибка при создании раздела: {$this->current_sub['name']}. На строке {$this->indexRow}");
                throw new \Exception("Error create subdivision {$this->current_sub['name']}", 1);
            }

            $subCC = $this->insert('Sub_Class', [
                'Subdivision_ID' => $subID,
                'Class_ID' => self::ROOT_GRUOP['Class_ID'],
                'Sub_Class_Name' => $this->current_sub['EnglishName'],
                'EnglishName' => 'item',
                'Checked' => 1,
                'Catalogue_ID' => $this->current_sub['Catalogue_ID'],
                'AllowTags' => -1,
                'DefaultAction' => 'index',
                'NL2BR' => -1,
                'UseCaptcha' => -1,
                'CacheForUser' => -1,
                'Class_Template_ID' => 0
            ], [
                'Subdivision_ID',
                'Class_ID',
                'Sub_Class_Name',
                'EnglishName',
                'Checked',
                'Catalogue_ID',
                'AllowTags',
                'DefaultAction',
                'NL2BR',
                'UseCaptcha',
                'CacheForUser',
                'Class_Template_ID'
            ]);
            if (!$subCC) {
                $this->setLog("Ошибка при создании инфоблока на строке {$this->indexRow}");
                throw new \Exception("Error create Sub_Class from {$subID}", 1);
            }

            $this->current_sub = array_merge($this->current_sub, ['Subdivision_ID' => $subID, 'Sub_Class_ID' => $subCC]);

            $log = 'Создан';
        }

        $this->gruops[count($this->gruops) - 1] = [
            'Subdivision_ID' => $this->current_sub['Subdivision_ID'],
            'Hidden_URL' => $this->current_sub['Hidden_URL'],
        ];

        $this->setLog("Раздел {$this->current_sub['Subdivision_ID']}:{$this->current_sub['Subdivision_Name']} на стороке {$this->indexRow}: {$log}");
        $this->setItemGruop($this->current_sub['Subdivision_ID']);
        if (isset($this->gruopsSite[$this->current_sub['Subdivision_ID']])) {
            unset($this->gruopsSite[$this->current_sub['Subdivision_ID']]);
        }
    }
    protected function setItemGruop($Subdivision_ID)
    {
        $items = $this->nc_core->db->get_results("SELECT Message_ID FROM Message2001 WHERE Subdivision_ID = {$Subdivision_ID}", ARRAY_A) ?: [];
        foreach ($items as $item) {
            $this->itelGruopOld[$item['Message_ID']] = true;
        }
    }
    protected function getGruopsSite()
    {
        $groups = $this->nc_core->db->get_results(
            "SELECT 
                sub.`Subdivision_ID`,
                cc.`Sub_Class_ID`,
                sub.`Hidden_URL`
            FROM 
                Subdivision as sub,
                Sub_Class as cc
            WHERE 
                sub.Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'
                AND sub.systemsub != 1
                AND sub.nosettings != 1
                AND sub.EnglishName NOT IN ('comparison', 'spec', 'new', 'hits')
                AND sub.Subdivision_ID = cc.Subdivision_ID
                AND cc.Class_ID = 2001
            ORDER BY sub.Hidden_URL DESC",
            ARRAY_A
        );

        return array_reduce($groups, function ($carry, $group) {
            $carry[$group['Subdivision_ID']] = $group;
            return $carry;
        }, []);
    }


    protected function subLevelConstruct($rowValue)
    {
        $subdivisionName = trim($rowValue['Subdivision_Name']);

        if (empty($subdivisionName)) {
            $this->setLog("Ошибка наименования раздела. На строке {$this->indexRow}");
            throw new \Exception("Invalid Subdivision Name in {$this->indexRow} row", 1);
        }

        for ($indexLevel = 0; $indexLevel <= 20; $indexLevel++) {
            $space = $indexLevel . '. ';
            if (preg_match("/^({$space})[\s\S]+$/i", $subdivisionName)) {
                $rowValue['Subdivision_Name'] = trim(preg_replace("/^({$space})([\s\S]+)$/i", '${2}', $subdivisionName));
                $this->gruops[$indexLevel] = [
                    'Subdivision_ID' => @$rowValue['Subdivision_ID']
                ];
                foreach ($this->gruops as $level => $v) {
                    if ($indexLevel < $level) unset($this->gruops[$level]);
                }
                break;
            }
        }

        return $rowValue;
    }

    protected function getRowValue(array $patern, array $row): array
    {
        $result = [];
        $resultRow = '';
        foreach ($patern as $colIndex => $field) {
            if (isset($row[$colIndex]) && (is_numeric($row[$colIndex]) || !empty($row[$colIndex]) || $row[$colIndex] == '')) {
                $result[$field] = $row[$colIndex];
                $resultRow .= $row[$colIndex];
            }
        }
        if (empty(trim($resultRow))) $result = [];
        return $result;
    }


    public function setCatalog()
    {
        $result = ['status' => 0, 'message' => ''];

        if (empty($_FILES['catalog'])) {
            $result['message'] = 'Нет файла';
            return $result;
        }
        $exiption = end(explode(".", basename($_FILES['catalog']['name'])));

        if ($exiption !== 'csv') {
            $result['message'] = 'Файл должен быть в формате csv';
            return $result;
        }

        $uploadfile = $this->path . 'catalog.' . $exiption;

        if (move_uploaded_file($_FILES['catalog']['tmp_name'], $uploadfile)) {
            $result['message'] = "Файл корректен и был успешно загружен.";
            $result['status'] = 1;
            $this->setSetting(['file' => $uploadfile]);
        } else {
            $result['message'] = "Возможная атака с помощью файловой загрузки!";
        }
        return $result;
    }

    public function setSetting($data)
    {
        if (!is_array($this->settingExport)) $this->settingExport = $this->getSettings();
        foreach ($data as $key => $value) {
            $this->settingExport[$key] = $value;
        }

        file_put_contents($this->path . 'setting.json', json_encode($this->settingExport));
    }

    public function getSettings()
    {
        return json_decode((@file_get_contents($this->path . 'setting.json') ?: '{}'), true);
    }

    protected function explain($name)
    {
        return array_reduce($this->nc_core->db->get_results("EXPLAIN {$name}", ARRAY_A), function ($carry, $field) {
            $carry[] = $field['Field'];
            return $carry;
        }, []);
    }

    protected function update($tableName, $data, $whiteListFields, $queryWhere = '')
    {
        $set = [];
        foreach ($data as $find => $value) {
            if (in_array($find, $whiteListFields)) {
                $set[] = "`{$find}` = '" . addslashes($value) . "'";
            }
        }

        if (empty($set)) {
            $log = 'Нет полей для обновления';
        } else {
            $sql = "
            UPDATE 
                {$tableName} 
            SET 
                " . implode(",\r\n", $set) . " 
            WHERE 
                Catalogue_ID = {$this->catalogue['Catalogue_ID']} " . ($queryWhere ? " AND {$queryWhere}" : null);
            $res = $this->nc_core->db->query($sql);
            $log = $res ? 'Строка обновлена' : 'Строка не нуждалась в обновление';

            $this->setLog('SQL UPDATE: ' . $sql);
        }

        return $log;
    }

    protected function insert($tableName, $fields, $whiteListFields)
    {
        $fields = array_filter($fields, function ($fildKey) use ($whiteListFields) {
            return in_array($fildKey, $whiteListFields);
        }, ARRAY_FILTER_USE_KEY);

        $keys = implode(',', array_map(function ($key) {
            return "`{$key}`";
        }, array_keys($fields)));

        $values = implode(',', array_map(function ($field) {
            return "'" . addslashes($field) . "'";
        }, $fields));
        $sql = "INSERT INTO {$tableName} ({$keys}) VALUES ({$values})";
        $this->nc_core->db->query($sql);
        $this->setLog('SQL INSERT: ' . $sql);
        return $this->nc_core->db->insert_id;
    }

    protected function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }

    private function getMemoryUse()
    {
        echo (memory_get_usage() / 1048576) . " Mb\n";
    }

    protected function encodestring($string, $url = '')
    {
        $table = array(
            'А' => 'a', 'Б' => 'b', 'В' => 'v',
            'Г' => 'g', 'Д' => 'd', 'Е' => 'e',
            'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z',
            'И' => 'i', 'Й' => 'j', 'К' => 'k',
            'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О' => 'o', 'П' => 'p', 'Р' => 'r',
            'С' => 's', 'Т' => 't', 'У' => 'u',
            'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c',
            'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'csh',
            'Ь' => '', 'Ы' => 'y', 'Ъ' => '',
            'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'j', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '*' => 'x'
        );

        $output = str_replace(array_keys($table), array_values($table), trim($string));
        if ($url != 2) $output = str_replace("_", "-", $output);
        if ($url) {
            if (!stristr($output, "http://") && !stristr($output, "https://")) $output = str_replace(" ", "-", trim($output));
            if ($url == 1) { // ссылки
                $output = preg_replace("/[^a-zA-Z0-9-]/", "", $output);
                if (is_numeric($output)) $output = "s" . $output;
            }
            if ($url == 2) { // картинки
                if (!stristr($output, "http://") && !stristr($output, "https://")) $output = preg_replace("/[^a-zA-Z0-9-_\.\,]/", "", $output);
            }
            $output = trim($output, "-");
        }
        return $output;
    }

    protected function reductionDuplicates($pattern, $string)
    {
        $result = [];
        $symbol = '';
        $string = str_split($string);
        for ($i = count($string) - 1; $i >= 0; --$i) {
            if (in_array($symbol, $pattern) && $symbol === $string[$i]) continue;
            $result[] = $symbol = $string[$i];
        }
        return implode(array_reverse($result));
    }

    protected function tiredel($word)
    {
        return $this->reductionDuplicates(['-'], $word);
    }

    protected function setLog($log, $start = 0)
    {
        if ($start) unlink($this->logPath);
        if (($f = fopen($this->logPath, "a")) === false) throw new \Exception('Log don`t save', 1);
        fwrite($f, print_r($log, 1) . "\n\r");
        fclose($f);
    }

    public function getProcess()
    {
        $this->ProcessLog = new ProcessLog($this->pathProcess);
        return $this->ProcessLog->getProcess();
    }

    protected function getProcent()
    {
        return round(($this->indexRow * 100 / $this->totalRow), 2);
    }
}
