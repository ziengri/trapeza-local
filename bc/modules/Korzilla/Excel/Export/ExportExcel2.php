<?php

namespace App\modules\Korzilla\Excel\Export;

use \PhpOffice\PhpSpreadsheet\IOFactory;
use nc_Core;
use Exception;

class ExportExcel2
{
    /** @var nc_Core обект ядра */
    protected $nc_core;

    /** @var array Настройки выгрузки*/
    protected $settingExport;

    /** @var string Путь до файла excel*/
    protected $filePath;

    /** @var array Параметры корневого каталога*/
    protected $rootGroup;

    /** @var array Настройки сайта*/
    protected $catalogue;

    protected $highestRow = 0;

    protected $row = 0;

    protected $dopGroup;

    protected $spaces = 0;

    /** @var array Замена символов в наименовнии*/
    protected const NAME_REPLACE = ["#" => " ", "_" => " ", "=" => " ", "!" => " "];

    public const STATUS_NEW = 0, STATUS_PROCESS = 1, STATUS_COMPLIT = 2, STATUS_ERROR = 3;

    public function __construct(int $Catalogue_ID, int $messageID)
    {
        $this->setNcCore();
        $this->messageID = $messageID;
        $this->DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        $this->catalogue = $this->nc_core->catalogue->get_by_id($Catalogue_ID);
        $this->path = $this->DOCUMENT_ROOT . "/a/{$this->catalogue['login']}/export_excel/{$this->messageID}";
        $this->logPath = $this->path . '/logExcel.log';

        @mkdir($this->path .'/', 0777, true);
    }

    public function export()
    {
        try {
            $this->setSettingExport();

            $this->exportBefore();

            $this->parsing();
    
            $this->exportAfter();

            $this->setLog('Загрузка завершена');
        } catch (Exception $e) {
            $this->setProcess(['error' => $e->getMessage(), 'status' => self::STATUS_ERROR], true);
            $this->setLog('Ошибка: ' . $e->getMessage());

            throw new Exception($e->getMessage(), 1);
        }
    }

    protected function parsing()
    {
        $this->filePath = $this->DOCUMENT_ROOT . nc_file_path(2257, $this->messageID, 'excel');
        if (!file_exists($this->filePath)) throw new Exception("Not find file from path " . $this->filePath, 1);
        $this->setLog('Файл ' . $this->filePath);

        $spreadsheet = IOFactory::load($this->filePath);

        //активный лист;
        if (!empty($this->settingExport['list'])) $spreadsheet->setActiveSheetIndex($this->settingExport['list'] - 1);
        elseif (!empty($this->settingExport['list_name'])) $spreadsheet->setActiveSheetIndexByName($this->settingExport['list_name']);
        $this->worksheet = $spreadsheet->getActiveSheet();

        $this->highestRow = $this->worksheet->getHighestRow();
        $this->indexColumnHeader = $this->getHeader();

        $this->setColsParamsName($this->colsItem);

        $this->setProcess(['message' => 'Обработали']);

        for ($this->row = $this->indexColumnHeader + 1; $this->row <= $this->highestRow; ++$this->row) {
            $isItem = $this->isItem();
            $patern = $isItem ? $this->colsItem : $this->colsCatigory;
            $rowValue = $this->getRowValue($patern);

            if (empty($rowValue)) {
                $this->setLog("Пустая строка {$this->row}");
                continue;
            }

            if ($isItem) {
                $this->itemConstruct($rowValue);
            } else {
                $this->subConstruct($rowValue);
            }

            if (($this->row % 100) == 0) {
                $this->setProcess(['procent' => $this->getProcent(), ]);
            }
        }

        $this->setProcess(['procent' => $this->getProcent()]);
    }

    /**
     * Проверка строки на товар
     * 
     * @return bool
     */
    protected function isItem() {

        if ($this->settingExport['group_col'] === 0) return true;

        return $this->worksheet->getCellByColumnAndRow($this->settingExport['group_col'], $this->row)->getFormattedValue() === '';
    }

    protected function subConstruct($rowValue)
    {
        $rowValue = $this->subLevelConstruct($rowValue);

        $parentSub = count($this->subs) > 1 ? $this->subs[count($this->subs) - 2] : $this->rootGroup;
        $this->current_sub = $this->subs[count($this->subs) - 1];
        $this->current_sub['Hidden_URL'] = $parentSub['Hidden_URL'] . $this->encodestring($rowValue['name'], 1) . '/';
        $this->current_sub['Subdivision_Name'] = $this->current_sub['name'];
        $this->current_sub['code1C'] = (isset($this->current_sub['code1C']) ? $this->current_sub['code1C'] : 'excel_' . md5(@$parentSub['code1C'] . $this->current_sub['Subdivision_Name']));
        $this->current_sub['Catalogue_ID'] = $this->catalogue['Catalogue_ID'];
        $this->current_sub['Parent_Sub_ID'] = $parentSub['Subdivision_ID'];
        $this->current_sub['EnglishName'] = $this->encodestring($rowValue['name'], 1);
        $this->current_sub['priority'] = $this->nc_core->db->get_var(
            "SELECT 
                MAX(priority)
            FROM 
                Subdivision 
            WHERE Parent_Sub_ID = '{$this->current_sub['Parent_Sub_ID']}'"
        ) ?: 0;
        $this->current_sub['Checked'] = 1;
        $this->current_sub['subdir'] = 3;

        $isSub = $this->nc_core->db->get_row(
            "SELECT 
                sub.`Subdivision_ID`,
                cc.`Sub_Class_ID`,
                sub.`Hidden_URL`,
                sub.`code1C`
            FROM 
            Subdivision AS sub,
                Sub_Class AS cc
            WHERE 
                sub.Catalogue_ID = '{$this->current_sub['Catalogue_ID']}' 
                AND 
                    (
                        sub.code1C = '{$this->current_sub['code1C']}' 
                        OR sub.Hidden_URL = '{$this->current_sub['Hidden_URL']}'
                    )
                AND sub.Subdivision_ID = cc.Subdivision_ID",
            ARRAY_A
        );

        if ($isSub) {

            if ($this->settingUpload['update_sub']['checked']) {
                $log = $this->update('Subdivision', $this->current_sub, $this->colsGroupUpdate, " Subdivision_ID = '{$isSub['Subdivision_ID']}'");
                $isSub = $this->nc_core->db->get_row(
                    "SELECT 
                        sub.`Subdivision_ID`,
                        cc.`Sub_Class_ID`,
                        sub.`Hidden_URL`
                    FROM 
                    Subdivision AS sub,
                        Sub_Class AS cc
                    WHERE 
                        sub.Catalogue_ID = '{$this->current_sub['Catalogue_ID']}' 
                        AND sub.Subdivision_ID = '{$isSub['Subdivision_ID']}'
                        AND sub.Subdivision_ID = cc.Subdivision_ID",
                    ARRAY_A
                );
            } else {
                $log = 'Не обновлен. Обновления разделов отключено';
            }

            $this->current_sub = array_merge($this->current_sub, $isSub);
        } else {
            $subID = $this->insert('Subdivision', $this->current_sub, $this->whiteListSubdivision);
            if (!$subID) {
                throw new \Exception("Error create subdivision {$this->current_sub['name']}", 1);
            }

            $subCC = $this->insert('Sub_Class', [
                'Subdivision_ID' => $subID,
                'Class_ID' => $this->rootGroup['Class_ID'],
                'Sub_Class_Name' => $this->current_sub['code1C'],
                'EnglishName' => 'item',
                'Checked' => 1,
                'Catalogue_ID' => $this->current_sub['Catalogue_ID'],
                'AllowTags' => -1,
                'DefaultAction' => 'index',
                'NL2BR' => -1,
                'UseCaptcha' => -1,
                'CacheForUser' => -1,
                'Class_Template_ID' => $this->params['patern_ncctpl']
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
            if (!$subCC) throw new \Exception("Error create Sub_Class from {$subID}", 1);

            $this->current_sub = array_merge($this->current_sub, ['Subdivision_ID' => $subID, 'Sub_Class_ID' => $subCC]);

            $log = 'Создан';
        }

        $this->subs[count($this->subs) - 1] = $this->current_sub;
        $this->subsData[$this->current_sub['code1C']] = [
            'Subdivision_ID' => $this->current_sub['Subdivision_ID'],
            'Sub_Class_ID' => $this->current_sub['Sub_Class_ID']
        ];
        $this->setLog("Раздел {$this->current_sub['Subdivision_ID']}:{$this->current_sub['Subdivision_Name']} на стороке {$this->row}: {$log}");
    }

    protected function subLevelConstruct($rowValue)
    {
        $subdivisionName = trim($rowValue['name']);

        if (empty($subdivisionName)) throw new \Exception("Invalid Subdivision Name in {$this->row} row", 1);

        if (is_array($this->spaces)) {
            foreach ($this->spaces as $indexLevel => $space) {
                if (preg_match("/^({$space})[\s\S]+$/i", $subdivisionName)) {
                    $rowValue['name'] = trim(preg_replace("/^({$space})([\s\S]+)$/i", '${2}', $subdivisionName));
                    $this->subs[$indexLevel] = $rowValue;
                    foreach ($this->subs as $level => $v) {
                        if ($indexLevel < $level) unset($this->subs[$level]);
                    }
                    break;
                }
            }
        } else {
            $this->spaces++;

            if ($this->spaces == 2) {
                $this->subs[1] = $this->subs[0];
            }
            if ($this->spaces == 3) {
                $this->subs[2] = $this->subs[1];
                $this->subs[1] = $this->subs[0];
            }
            if ($this->spaces == 4) {
                $this->subs[3] = $this->subs[2];
                $this->subs[2] = $this->subs[1];
                $this->subs[1] = $this->subs[0];
            }
            if ($this->spaces == 5) {
                $this->subs[4] = $this->subs[3];
                $this->subs[3] = $this->subs[2];
                $this->subs[2] = $this->subs[1];
                $this->subs[1] = $this->subs[0];
            }
            $this->subs[0] = $rowValue;

        }
        return $rowValue;
    }
    protected function itemConstruct($rowValue)
    {
        if (is_numeric($this->spaces)) $this->spaces = 0;
        if (!isset($rowValue['name']) || empty(trim($rowValue['name']))) throw new \Exception("Item no name in row {$this->row}", 1);
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
                    $price = str_replace(",", ".", preg_replace("([^0-9,\.])", "", $value));
                    
                    $markupPrice = 0;
                    if ($this->priceMarkup > 0) {
                        $markupPrice = $price * ($this->priceMarkup / 100);
                    }
                    
                    $item[$field] = $price + $markupPrice;
                    break;
                case stristr($field, "stock"):
                    if (stristr(trim($value), 'Да')) $value = 1;
                    if (stristr(trim($value), 'Нет')) $value = 0;
                    if (!$value) $value = 0;
                    $item[$field] = round(preg_replace("([^0-9,\.])", "", $value));
                    break;
                case mb_strstr($field, 'param_'):
                    $item['paramCol'][] = ['keyword' => $field, 'value' => str_replace('|', '', $value)];
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
                case 'text':
                    $item[$field] = str_replace("\n", "<br />", $value);
                    break;
                default:
                    $item[$field] = $value;
                    break;
            }
        }
        $item['Keyword'] = $this->tiredel($this->encodestring($item['name'] . (isset($item['art']) ? '-' . $item['art'] : (isset($item['code']) ? '-' . $item['code'] : null)), 1));
        $item['Catalogue_ID'] = $this->catalogue['Catalogue_ID'];
        $item['Checked'] = 1;
        $item['fromxls'] = 1;
        $item['xlslist'] = $this->messageID;
        $item['Subdivision_ID'] = $this->current_sub['Subdivision_ID'] ?:  $this->dopGroup['Subdivision_ID'];
        $item['Sub_Class_ID'] = $this->current_sub['Sub_Class_ID'] ?:  $this->dopGroup['Sub_Class_ID'];

        if (!empty($item['paramRow']) || !empty($item['paramCol'])) {
            $item['params'] = array_reduce(array_merge($item['paramRow'], $item['paramCol']), function ($carry, $param) {
                if (!isset($param['value'])) return $carry;
                return $carry .= trim($param['keyword']) . "||" . trim($param['value']) . "|\r\n";
            }, '');
            unset($item['paramRow'], $item['paramCol']);
        }

        $tovar = $this->nc_core->db->get_row(
            "SELECT
                Message_ID AS id,
                Keyword,
                Checked,
                Subdivision_IDS,
                fromxls,
                art,
                xlslist
            FROM 
                Message2001 
            WHERE 
                " . ($this->settingUpload['find_art']['checked'] ? "art = '{$item['art']}'" : "Keyword = '{$item['Keyword']}' ") . "
                AND Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'",
            ARRAY_A
        );

        if (empty($tovar)) {
            $tovar['id'] = $this->insert('Message2001', $item, $this->whiteListItem);
            $log = $tovar['id'] ? 'Создан' : 'Не создан, ошибка в sql запросе';
        } else {
            if ($this->settingUpload['update_item']['checked']) {
                if (($tovar['xlslist'] != $this->messageID || $tovar['xlslist'] != 1) && $tovar['fromxls'] != 1) {
                    $log = 'Товар из другой выгрузки и не подлежит обновлению: ' . $tovar['xlslist'];
                } else {
                    $log = $this->update('Message2001', $item, $this->colsItemUpdate, "Message_ID = {$tovar['id']}");
                }
            }
        }
        $this->dataItems[] = $tovar['id'];
        $this->setLog("Товар {$tovar['id']}:{$item['name']} на стороке {$this->row}: {$log}");
    }

    /**
     * Получения параметров в строке по патерну
     * 
     * @return array
     */
    protected function getRowValue($patern)
    {
        $result = [];
        foreach ($patern as $colIndex => $field) {
            $value = (string) $this->worksheet->getCellByColumnAndRow($colIndex + 1, $this->row)->getCalculatedValue();
            $value = trim(str_replace(['_x000D_'], '', $value));
            $result[$field] = $value;
        }
        
        if (empty(array_filter($result))) $result = [];

        return $result;
    }

    protected function exportBefore()
    {
        if ($this->settingUpload['delete_item']['checked']) {
            $this->nc_core->db->query(
                "DELETE FROM 
                    Message2001
                WHERE 
                    Catalogue_ID = {$this->catalogue['Catalogue_ID']} 
                    AND fromxls = '{$this->messageID}'"
            );
        }
    }

    protected function exportAfter()
    {
        if ($this->settingUpload['update_item']['childs']['params']['checked']) {
            $this->setting['lists_params'] = array_values($this->paramsList);
            setSettings($this->setting);
        }

        // Выключения товаров не участвовавших в выгрузки
        if (!$this->settingUpload['unchecked']['checked']) {
            $res = $this->nc_core->db->query(
                "UPDATE 
                    Message2001
                SET
                    Checked = 0
                WHERE 
                    Catalogue_ID = {$this->catalogue['Catalogue_ID']} 
                    AND (
                            xlslist = '{$this->messageID}' OR
                            xlslist = '1'
                        )
                    AND Message_ID NOT IN (" . implode(',', $this->dataItems) . ")"
            );
            $this->setLog('Товаров выключено: ' . $res);
        }

        if (in_array('Subdivision_IDS', $this->colsItem) && in_array('code1C', $this->colsCatigory)) {
            foreach ($this->subsData as $code1C => $data) {
                $this->nc_core->db->query(
                    "UPDATE
                        Message2001
                    SET
                        Subdivision_IDS = REPLACE(Subdivision_IDS, ',{$code1C},', ',{$data['Subdivision_ID']},')
                    WHERE
                        Subdivision_IDS LIKE '%,{$code1C},%'
                        AND Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'"
                );
            }
        }

        $this->setProcess(['status' => self::STATUS_COMPLIT, 'message' => 'Загрузка завершена']);
    }

    protected function setColsParamsName($fielItem)
    {
        foreach ($fielItem as $key => $field) {
            if (!mb_strstr($field, 'param_')) continue;
            $name = (string) $this->worksheet->getCellByColumnAndRow($key + 1, $this->indexColumnHeader)->getCalculatedValue();
            if (!empty(trim($name))) {
                $this->addParamsList(['keyword' => $field, 'name' => $name]);
            }
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

    protected function getHeader(): int
    {
        $headerRowIndex = false;
        if ($this->settingExport['number_row_head']) $headerRowIndex = $this->settingExport['number_row_head'];
        elseif ($this->settingExport['firsthead']) {
            $highestRow = $this->worksheet->getHighestRow();
            for ($this->row = 1; $this->row <= $highestRow; ++$this->row) {
                for ($col = 1; $col <= 5; ++$col) {
                    if (mb_stristr($this->worksheet->getCellByColumnAndRow($col, $this->row)->getFormattedValue(), $this->settingExport['firsthead']) !== false) {
                        $headerRowIndex = $this->row;
                    }
                }

                if ($headerRowIndex !== false) break;
            }
        }
        if ($headerRowIndex !== false) {
            $this->setLog('Шапка найдена. Строка ' . $headerRowIndex);
            return $headerRowIndex;
        }
        throw new \Exception('Header not find', 1);
    }
    // Setting
    protected function setSettingExport()
    {
        $this->setProcess(['message' => 'Производим расчеты...', 'status' => self::STATUS_PROCESS], true);

        $this->settingExport = $this->getSettingDB();
        if (empty($this->settingExport)) throw new Exception('Настройки не найдены', 1);
        
        $this->setLog('Старт !!!', 1);
        $this->setLog("ID Выгрузки {$this->messageID}");

        $this->rootGroup = $this->getRootGroup($this->settingExport['parent']);
        $this->setLog('Доп раздел для товаров без категории' . print_r($this->rootGroup, 1));

        $this->dopGroup = $this->getDopGroup();
        $this->setLog('Доп раздел для товаров без категории' . print_r($this->dopGroup, 1));

        $this->settingUpload = orderArray($this->settingExport['setting_upload']);
        $this->setLog('Настройки выгрузки ' . print_r($this->settingUpload, 1));

        $this->colsItem = array_filter(array_map('trim', explode(',', $this->settingExport['fields_item'])));
        $this->setLog('Поля товара ' . print_r($this->colsItem, 1));

        $this->colsItemUpdate = $this->getColsItemUpdate();
        $this->setLog('Поля товара для обновления ' . print_r($this->colsItemUpdate, 1));

        $this->colsCatigory = array_filter(array_map('trim', explode(',', $this->settingExport['fields_group'])));
        $this->setLog('Поля раздела ' . print_r($this->colsCatigory, 1));

        $this->colsGroupUpdate = $this->getColsGroupUpdate();
        $this->setLog('Поля раздела для обновления ' . print_r($this->colsGroupUpdate, 1));

        $this->priceMarkup = (float) $this->settingExport['markup'];
        $this->setLog('Наценка на все товары в прайс листе ' . print_r($this->priceMarkup, 1));

        $this->whiteListSubdivision = $this->explain("Subdivision");
        $this->whiteListItem = $this->explain('Message2001');
        $this->setting = getSettings();
        
        foreach ($this->setting['lists_params'] as $value) {
            $this->paramsList[$value['keyword']] = $value;
        }

        if (isset($this->settingExport['spacesub']) && mb_strstr($this->settingExport['spacesub'], ':')) {
            $this->spaces = explode(":", rtrim(ltrim($this->settingExport['spacesub'], '('), ')'));
        }
    }

    protected function getProcent()
    {
        return round(($this->row * 100 / $this->highestRow), 2);
    }

    public function setProcess($data = [], $newProcess = false)
    {
        $path = $this->path . '/process.json';
        if (empty($this->process)) $this->process = $this->getProcess($newProcess);
        foreach ($data as $key => $value) {
            $this->process[$key] = $value;
        }

        file_put_contents($path, json_encode($this->process));
    }

    public function getProcess($newProcess = false)
    {
        $path = $this->path . '/process.json';

        $patern = [
            'status' => self::STATUS_NEW,
            'message' => '',
            'error' => '',
            'procent' => 0
        ];

        return ($newProcess || !file_exists($path) ? $patern : json_decode(file_get_contents($path), 1));
    }

    protected function getColsGroupUpdate()
    {
        $result = [];
        $this->setLog('Обновления разделов: ' . ($this->settingUpload['update_sub']['checked'] ? 'Включено!' : 'Отключено!'));
        if (!$this->settingUpload['update_sub']['checked']) return $result;
        $result = ['Checked'];
        foreach ($this->settingUpload['update_sub']['childs'] as $field => $value) {
            switch ($field) {
                case 'Parent_Sub_ID':
                    if ($value['checked']) {
                        $result[] = 'Parent_Sub_ID';
                        $result[] = 'Hidden_URL';
                    }
                    break;
                default:
                    if ($value['checked']) {
                        $result[] = $field;
                    }
                    break;
            }
        }
        return $result;
    }
    protected function getColsItemUpdate()
    {
        $result = [];
        $this->setLog('Обновления товаров: ' . ($this->settingUpload['update_item']['checked'] ? 'Включено!' : 'Отключено!'));
        if (!$this->settingUpload['update_item']['checked']) return $result;
        $result = ['Checked', 'fromxls', 'xlslist'];
        foreach ($this->settingUpload['update_item']['childs'] as $field => $value) {
            switch ($field) {
                case 'Subdivision_ID':
                    if ($value['checked']) {
                        $result[] = 'Sub_Class_ID';
                        $result[] = 'Subdivision_ID';
                    }
                    break;
                default:
                    if (!empty($value['checked'])) {
                        $result[] = $field;
                    }
                    break;
            }
        }
        return $result;
    }

    protected function getSettingDB(): array
    {
        return $this->nc_core->db->get_row("SELECT * FROM Message2257 WHERE Message_ID = {$this->messageID}", ARRAY_A) ?: [];
    }

    protected function getRootGroup(int $subID): array
    {
        if (empty($subID)) throw new Exception('Нет ID корневого раздела', 1);

        
        $rootSub = $this->nc_core->db->get_row(
            "SELECT 
                sub.`Subdivision_ID`,
                cc.`Sub_Class_ID`,
                cc.`Class_ID`,
                sub.`Hidden_URL`,
                cc.`Class_Template_ID`
            FROM 
                Subdivision AS sub,
                Sub_Class AS cc
            WHERE 
                sub.Subdivision_ID = {$subID} 
                AND sub.Subdivision_ID = cc.Subdivision_ID",
            ARRAY_A
        );

        if (empty($rootSub)) throw new Exception('Корневой раздел не найден', 2);

        return $rootSub;
    }

    protected function getDopGroup(): array
    {
        if (!empty($this->settingExport['ssub'])) {
            $dopGroup = $this->nc_core->db->get_row(
                "SELECT 
                    sub.`Subdivision_ID`,
                    cc.`Sub_Class_ID`,
                    cc.`Class_ID`,
                    sub.`Hidden_URL`,
                    cc.`Class_Template_ID`
                FROM 
                    Subdivision AS sub,
                    Sub_Class AS cc
                WHERE 
                    sub.Subdivision_ID = {$this->settingExport['ssub']} 
                    AND sub.Subdivision_ID = cc.Subdivision_ID",
                ARRAY_A
            );
        }

        if (!isset($dopGroup) || empty($dopGroup)) {
            $dopGroup = $this->rootGroup;
        }

        return $dopGroup;
    }

    // sql
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

    // Log
    protected function setLog($log, $start = 0)
    {
        if (!@file_put_contents($this->logPath, print_r($log, 1) . "\n\r", ($start ? null : FILE_APPEND))) {
            throw new \Exception('Log don`t save', 1);
        }
    }

    protected function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
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
}
