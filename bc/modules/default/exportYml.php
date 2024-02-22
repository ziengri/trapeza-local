<?php 
class ExportYml
{
    private $db;
    private $log;
    private $path;
    private $rootSub;
    private $rootDir;
    private $fileName;
    private $catalogue;
    private $shortPath;
    private $fields2001;
    private $groupItems;
    private $classTemplate;

    private $groups = array();
    private $allGoods = array();
    private $exportSubs = array();
    private $wordsKeyword = array();

    private $itemParams = false;
    private $itemVariable = false;

    private $YMLkey = '121109108'; # keyCode символов слова yml в js
    private $style = "<style>
                        span { width: 2px; height: 2px; margin-bottom: 5px; display: inline-block; background-color: #000; }
                        .success { background-color: #4CAF50; }
                        .error { background-color: #f44336; }
                     </style>";

    public function __construct($path, $catalogue, $db)
    {
        echo $this->style;

        $this->db = $db;
        $this->path = $_SERVER['DOCUMENT_ROOT'].$path.'/yml/';
        $this->rootDir = $_SERVER['DOCUMENT_ROOT'];
        $this->catalogue = $catalogue;
        $this->shortPath = $path.'/yml/';

        $this->setSettings();
        $this->setVariable();
        $this->setItemParams();
    }

    public function pull($forcibly = false)
    {
        if (strstr($this->fileName, 'http')) { # файл доступен по ссылке
            $result = $this->curlResponsCode($this->fileName);
            if ($result !== 200) return array('success' => 'error', 'error' => "Файл выгрузки не доступен по ссылке {$this->fileName}.");
            $file = $this->curlGetContent($this->fileName);
        } else { # не ссылка, искать на сервере
            if (!file_exists($this->path.$this->fileName)) return array('success' => 'error', 'error' => 'Файл выгрузки не обнаружен.');
            $file = file_get_contents($this->path.$this->fileName);
        }

        if (!$file) return array('success' => 'error', 'error' => 'пустой файл.');
        $xml = new SimplexmlElement($file);

        if (!$forcibly
            && file_exists($this->path.'check.ini')
            && trim(file_get_contents($this->path.'check.ini')) >= (new DateTime($xml->attributes()->date->__toString()))->format("U")
            ) {
            return array('success' => 'error', 'error' => 'Файл выгрузки не обновлен.');
        }

        $this->log('export');
        $this->log('write', "###СТАРТ###\r\n");
        $this->log('write', "Дата: ".((new DateTime())->format("Y-m-d H:i:s"))."\r\n");

        if (!$this->rootSub) return array('success' => 'error', 'error' => 'не найден корневой раздел.');

        $groupsXml = ($xml->shop->categories ? $xml->shop->categories : false);
        $offersXml = ($xml->shop->offers ? $xml->shop->offers : false);

        if ($groupsXml) $this->exportGroups($groupsXml);
        if ($offersXml) $this->exportGoods($offersXml);

        # выключение разделова и товаров неучавствующих в выгрузке
        $this->offNoneExported();

        file_put_contents($this->path.'check.ini', (new DateTime('now'))->format('U'));
    }

    private function exportGoods($offersXml)
    {
        $this->log('write', "\r\n\r\n###ТОВАРЫ###\r\n\r\n");

        $aloneItems = $groupItems = $variableItems = $photos = array();
        $counter = 0;

        foreach($offersXml->offer as $key => $itemXml) {
            $this->write('success');
            $xmlGroup = $this->groups[$itemXml->categoryId->__toString()];
            if (!$xmlGroup) continue;

            $itemXmlAtrs = $itemXml->attributes();

            $itemGroupId = $itemXmlAtrs->group_id ? $itemXmlAtrs->group_id->__toString() : false;
            $itemId = $itemXmlAtrs->id->__toString();

            $photoUrl = '';
            if ($itemXml->picture) foreach ($itemXml->picture as $photoXml) $photoUrl .= ($photoUrl ? ',' : '').$photoXml->__toString();

            $otherParams = ($this->itemParams !== false && $itemXml->param ? $this->getOtherParams('xml', $itemXml) : array());

            $item = array_merge(array(
                'art' => $itemXml->model->__toString(),
                'id1c' => $itemId,
                'name' => $itemXml->name->__toString(),
                'code' => $itemXml->barcode->__toString(),
                'text' => $itemXml->description ? $itemXml->description->__toString() : '',
                'price' => (string)$this->markup($itemXml->price->__toString()),
                'stock' => '100',
                'vendor' => $itemXml->vendor ? $itemXml->vendor->__toString() : '',
                'Checked' => '1',
                'xlslist' => $this->YMLkey,
                'Priority' => $counter,
                'photourl' => $photoUrl,
                'Catalogue_ID' => $this->catalogue,
                'Sub_Class_ID' => ($xmlGroup ? $xmlGroup['subClass'] : $this->rootSub['Sub_Class_ID']),
                'Subdivision_ID' => ($xmlGroup ? $xmlGroup['sub'] : $this->rootSub['Subdivision_ID'])
            ), $otherParams);

            # распределяем на группируемые товары и одиночные
            if ($this->groupItems && $this->itemVariable !== false && $itemGroupId !== false) {
                $variable = $this->getVariable($itemXml);
                $groupItems[$itemGroupId][$itemId] = array_merge($item, ['variablename' => $variable['name']]);
            } else {
                $aloneItems[$itemId] = $item;
            }
            # заполняем все фото в массив для последущего обновления
            $counter++;
        }

        $allGoodsData = $this->db->get_results("SELECT * FROM Message2001 WHERE Catalogue_ID = {$this->catalogue} AND xlslist = {$this->YMLkey}", ARRAY_A);

        if ($allGoodsData) foreach ($allGoodsData as $itemData) {
            $this->wordsKeyword[] = $itemData['Keyword'];
            $this->allGoods[$itemData['id1c']] = $itemData;
        }

        $this->log('write', "Всего товаров в выгрузке: ".count($offersXml->offer)."\r\nТоваров в базе: ".count($this->allGoods)."\r\n\r\n");

        foreach ($aloneItems as $itemXmlId => $item) {
            $this->insertUpdateGoods($item, false);
            if ($photos[$itemXmlId]) $photos[$itemXmlId]['Message_ID'] = $this->allGoods[$itemXmlId]['Message_ID'];
        }

        foreach ($groupItems as $itemGroupId => $group) {
            foreach ($group as $itemXmlId => $item) {
                $this->insertUpdateGoods($item, $itemGroupId);
                $variableItems[$itemGroupId][] = $this->allGoods[$itemXmlId]['Message_ID'];
                if ($photos[$itemXmlId]) $photos[$itemXmlId]['Message_ID'] = $this->allGoods[$itemXmlId]['Message_ID'];
            }
        }

        if ($variableItems) {
            foreach ($variableItems as $variableGroup) {
                $itemOff .= ($itemOff ? "','" : '').implode("','", $variableGroup);
                $itemOn .= ($itemOn ? "','" : '').$variableGroup[0];
            }
            $this->update(['variablenameSide' => 1], 'Message2001', "AND Message_ID in ('{$itemOff}')");
            $this->update(['variablenameSide' => ''], 'Message2001', "AND Message_ID in ('{$itemOn}')");
        }
    }

    private function insertUpdateGoods($item, $groupId = false)
    {
        $this->write('success');
        if ($groupId !== false) {
            $item['variablename'] = $item['variablename'];
            $item['name'] = $item['name']."_[{$groupId}]_";
        }

        $this->log('write', "".json_encode($item)."\r\n");

        if ($this->allGoods[$item['id1c']]) {
            # update
            $this->allGoods[$item['id1c']]['exported'] = true;
            $otherParams = ($this->itemParams ? $this->getOtherParams('db', $this->allGoods[$item['id1c']]) : array());
            $itemCheck = array_merge(array(
                'art' => $this->allGoods[$item['id1c']]['art'],
                'id1c' => $this->allGoods[$item['id1c']]['id1c'],
                'name' => $this->allGoods[$item['id1c']]['name'],
                'code' => $this->allGoods[$item['id1c']]['code'],
                'text' => $this->allGoods[$item['id1c']]['text'],
                'price' => $this->allGoods[$item['id1c']]['price'],
                'stock' => $this->allGoods[$item['id1c']]['stock'],
                'vendor' => $this->allGoods[$item['id1c']]['vendor'],
                'Checked' => $this->allGoods[$item['id1c']]['Checked'],
                'xlslist' => $this->allGoods[$item['id1c']]['xlslist'],
                'Priority' => $this->allGoods[$item['id1c']]['Priority'],
                'photourl' => $this->allGoods[$item['id1c']]['photourl'],
                'Catalogue_ID' => $this->allGoods[$item['id1c']]['Catalogue_ID'],
                'Sub_Class_ID' => $this->allGoods[$item['id1c']]['Sub_Class_ID'],
                'Subdivision_ID' => $this->allGoods[$item['id1c']]['Subdivision_ID'],
            ), $otherParams);

            if ($groupId !== false) {
                $itemCheck['variablename'] = $this->allGoods[$item['id1c']]['variablename'];
                $itemCheck['colors'] = orderArray($this->allGoods[$item['id1c']]['colors']);
            }

            $this->log('write', "Сравниваемый товар: ".json_encode($itemCheck)."\r\n");

            if ($itemCheck == $item) {
                $this->log('write', "Товар не требуется нуждается в обновлении db_id = {$this->allGoods[$item['id1c']]['Message_ID']}\r\n\r\n");
                return; # товар не изменен, не обновлять
            }
            if ($groupId !== false) $item['colors'] = isset($item['colors']) ? json_encode($item['colors']) : '';
            $this->update($item, 'Message2001', "AND Message_ID = {$this->allGoods[$item['id1c']]['Message_ID']}");
        } else {
            #create
            $item['Keyword'] = $this->getKeyword(trim(preg_replace('/[^\da-z_-]/i', '-', encodestring($item['name']))));
            if ($groupId !== false) $item['colors'] = isset($item['colors']) ? json_encode($item['colors']) : '';

            $this->insert($item, 'Message2001');
            $this->allGoods[$item['id1c']] = array(
                'exported' => true,
                'Message_ID' => $this->db->insert_id
            );
        }
    }

    private function markup($price)
    {
        $result = 0;

        if (isset($this->markup) && $this->markup) {
            if (strstr($this->markup, '%')) $markup = 1 + ((float)preg_replace("/[^\d,.]+/", '', $this->markup) / 100);
            else $markup = (float)preg_replace("/[^\d,.]+/", '', $this->markup);

            $result = $price * $markup;
        }

        return $result ? $result : $price;
    }

    private function exportGroups($groupsXml)
    {
        $this->log('write', "\r\n\r\n###ГРУППЫ###\r\n\r\n");

        $subsData = $this->db->get_results("SELECT a.code1C as code,
                                                   a.Hidden_URL as url,
                                                   b.Sub_Class_ID as subClass,
                                                   a.Parent_Sub_ID as parent,
                                                   a.Subdivision_Name as name,
                                                   a.Subdivision_ID as sub,
                                                   a.Priority,
                                                   a.Checked
                                           FROM Subdivision as a,
                                                Sub_Class as b
                                           WHERE a.Catalogue_ID = {$this->catalogue}
                                                 AND a.Subdivision_ID = b.Subdivision_ID
                                                 AND a.code1C LIKE 'YML&%'
                                                 AND b.Class_ID = '2001'", ARRAY_A);

        # если группы уже есть выносим xmlId группы в ключ для бастрого доступа
        if ($subsData) foreach($subsData as $sub) { $this->groups[substr($sub['code'], 4)] = $sub; }

        $this->log('write', "Всего групп в выгрузке: ".count($groupsXml->category)."\r\nГрупп на сайте: ".count($subsData)."\r\n\r\n");
        $counter = 0;
        foreach ($groupsXml->category as $key => $groupXml) {
            $this->write('success');
            $name = $groupXml->__toString();
            $attributes = $groupXml->attributes();

            $xmlId = $attributes->id->__toString();

            # собираем id родителя раздела на сайте
            $xmlParentId = ($attributes->parentId ? $attributes->parentId->__toString() : false);
            $parenId = ($xmlParentId && isset($this->groups[$xmlParentId]) && $this->groups[$xmlParentId]['exported'] ? $this->groups[$xmlParentId]['sub'] : $this->rootSub['Subdivision_ID']);

            # требуется ли выгрузка
            if ($this->exportSubs) {
                if (!in_array($xmlId, $this->exportSubs) && ($xmlParentId === false || !in_array($xmlParentId, $this->exportSubs))) continue;
                if (!in_array($xmlId, $this->exportSubs)) $this->exportSubs[] = $xmlId;
            }

            # собираем url путь к разделу
            $enName = preg_replace('/[^\da-z_-]/i', '-', encodestring($name));
            $urlSub = ($xmlParentId ? $this->groups[$xmlParentId]['url'].$enName.'/' : $this->rootSub['Hidden_URL'].$enName.'/');

            if ($this->groups[$xmlId]) {
                # update
                if ($parenId === false) continue;

                $this->groups[$xmlId]['exported'] = true;
                # нет изменений
                if ($this->groups[$xmlId]['parent'] == $parenId
                    && $this->groups[$xmlId]['url'] == $urlSub
                    && $this->groups[$xmlId]['name'] == $name
                    && $this->groups[$xmlId]['Priority'] == $counter
                    && $this->groups[$xmlId]['Checked']
                ) {
                    $this->log('write', "{$counter} группа db_id = {$this->groups[$xmlId]['sub']} не нуждается в обновлении\r\n\r\n");
                    continue;
                }

                $group = array(
                    'Parent_Sub_ID' => $parenId,
                    'Hidden_URL' => $urlSub,
                    'Checked' => 1,
                    'Subdivision_Name' => $name,
                    'EnglishName' => $enName,
                    'Priority' => $counter
                );

                $this->log('write', "{$key} #UPDATE# ".json_encode($group)."\r\n");

                $this->update($group, 'Subdivision', "AND Subdivision_ID = {$this->groups[$xmlId]['sub']}");
                $this->groups[$xmlId]['url'] = $urlSub;
                $this->groups[$xmlId]['parent'] = $parenId;
            } else {
                # create
                if ($parenId === false) continue;
                $group = array(
                    'Catalogue_ID' => $this->catalogue,
                    'Parent_Sub_ID' => $parenId,
                    'Hidden_URL' => $urlSub,
                    'Checked' => 1,
                    'Subdivision_Name' => $name,
                    'EnglishName' => $enName,
                    'code1C' => 'YML&'.$xmlId,
                    'Priority' => $counter
                );
                $this->log('write', "{$key} #INCETR#".json_encode($group)."\r\n");
                $this->insert($group, 'Subdivision');
                $subId = $this->db->insert_id;
                if (!$subId) continue; # неудалось добавть раздел

                $subClass = array(
                    'Catalogue_ID' => $this->catalogue,
                    'Subdivision_ID' => $subId,
                    'Class_Template_ID' => $this->classTemplate,
                    'Class_ID' => 2001,
                    'Checked' => 1,
                    'Sub_Class_Name' => 'YML'.$xmlId,
                    'EnglishName' => 'YML'.$xmlId
                );
                $this->log('write', "{$key} #INCETR#".json_encode($subClass)."\r\n");
                $this->insert($subClass, 'Sub_Class');
                $subClass = $this->db->insert_id;
                if (!$subClass) continue; # неудалось создать инфаблок

                $this->groups[$xmlId] = array(
                    'url' => $urlSub,
                    'sub' => $subId,
                    'code' => $xmlId,
                    'parent' => $parenId,
                    'subClass' => $subClass,
                    'exported' => true
                );
            }
            $counter++;
        }
    }

    private function getKeyword($keyword, $addkey = '')
    {
        if (in_array($keyword.$addkey, $this->wordsKeyword)) return $this->getKeyword($keyword, ++$addkey);
        $this->wordsKeyword[] = $keyword.$addkey;
        return $keyword.$addkey;
    }

    private function loadImg($photoArr) { }

    private function setSettings()
    {
        $file = $this->path.'settings.ini';
        $settings = array();
        if (file_exists($file) && trim(file_get_contents($file))) {
            $settings = json_decode(trim(file_get_contents($file)), true);
        }

        $this->groupItems = ($settings['groupItems'] ? $settings['groupItems'] : false);
        $this->classTemplate = ($settings['classTemplate'] ? $settings['classTemplate'] : 0);
        $this->fileName = ($settings['fileName'] ? $settings['fileName'] : 'import.xml');

        # Subdivision_ID раздела на в котроый выгружать товары и подразделы
        $rootWhere = ($settings['rootSub']
                        ? "a.Subdivision_ID = {$settings['rootSub']}"
                        : "a.Hidden_URL = '/catalog/' AND a.Catalogue_ID = '{$this->catalogue}'");

        $this->rootSub = $this->db->get_row("SELECT a.Subdivision_ID, a.Hidden_URL, b.Sub_Class_ID
                                       FROM Subdivision as a, Sub_Class as b
                                       WHERE {$rootWhere} AND a.Subdivision_ID = b.Subdivision_ID", ARRAY_A);

        if (isset($settings['exportSubs'])) $this->exportSubs = $settings['exportSubs'];

        if (isset($settings['otherPrice'])) $this->otherPrice = $settings['otherPrice'];

        if (isset($settings['markup'])) $this->markup = $settings['markup'];

    }

    /**
     * если есть файл itemparam.ini заполняет переменную itemParams
     * формат файла должен быть (ключ в выгрузке;поле в таблице) пример: 13qw-qwe-A15;var1
     * каждое новое соответствие с новой строки
     */
    private function setItemParams()
    {
        $path = $this->path.'itemparam.ini';

        if (!file_exists($path) || !trim(file_get_contents($path))) return false;

        foreach(explode(PHP_EOL, file_get_contents($path)) as $param) {
            if (!$param) continue;
            $keyVal = explode(";", trim($param));
            $this->itemParams[$keyVal[0]] = $keyVal[1];
        }
    }

    private function getOtherParams($type, $item)
    {
        if ($this->itemParams === false) return array();

        $itemParams = $result = array();

        if (!$this->fields2001) {
            # все поля каталога и их типы
            $fieldsData2001 = $this->db->get_results("EXPLAIN Message2001", ARRAY_A);
            foreach ($fieldsData2001 as $field) {
                $this->fields2001[$field['Field']] = array('type'=> $field['Type']);
            }
        }

        switch ($type) {
            case 'xml':
                foreach ($item->param as $param) {
                    $itemParams[$param->attributes()->name->__toString()] = $param->__toString();
                }
                foreach ($this->itemParams as $xmlKey => $field) {
                    if (!isset($this->fields2001[$field]) || !$itemParams[$xmlKey]) continue; # такого поля в каталоге нет || такого ключа нет
                    $result[$field] = (string)$itemParams[$xmlKey];
                }
                # другие цены
                if (isset($this->otherPrice) && $this->otherPrice) {
                    $price = $this->markup($item->price->__toString());
                    foreach ($this->otherPrice as $fName => $scale) {
                        if (!$this->fields2001[$fName]) continue; # такого поля в каталоге нет
                        $result[$fName] = (string)($price * $scale);
                    }
                }
            break;
            case 'db':
                foreach ($this->itemParams as $xmlKey => $field) {
                    if (!isset($this->fields2001[$field])) continue; # такого поля в каталоге нет
                    $result[$field] = $item[$field];
                }
                # другие цены
                if (isset($this->otherPrice) && $this->otherPrice) {
                    foreach ($this->otherPrice as $fName => $scale) {
                        if (!$this->fields2001[$fName]) continue; # такого поля в каталоге нет
                        $result[$fName] = $item[$fName];
                    }
                }
            break;
            default: break;
        }
        return $result;
    }

    /**
     * если есть файл variableItems.ini заполняет переменную itemParams
     * формат файла должен быть (ключ в выгрузке;json с шаблоном) пример: param&name&Размер;{"name":"val unit item&description&val","price":"item&price&val"}
     * param&name&Размер - тэг (param) (обязательный параметр), атрибут тэга (name), Значение атрибута (Размер) из которого забирать значения, атрибут и значение идут в паре
     * val - забрать значение тэга
     * item&descrition&val - забрать значение тэга из данного товара из другого поля по ключу "descrition", элемент шалбона записывается через &
     * любые значения не val, например unit значет что нужо забрать атрибут по ключу "unit"
     * элементы шаблона сбора записываются через пробел "val unit item&description&val"
     */
    private function setVariable()
    {
        $path = $this->path.'variableItems.ini';

        if (!file_exists($path) || !trim(file_get_contents($path))) return false;

        foreach(explode(PHP_EOL, file_get_contents($path)) as $param) {
            if (!$param) continue;
            $keyVal = explode(";", trim($param));
            $template = array();
            foreach (json_decode($keyVal[1], true) as $key => $value) {
                foreach (explode(' ', $value) as $templateKey) {
                    $template[$key][] = explode('&', $templateKey);
                }
            }
            $this->itemVariable = array(
                'template' => $template,
                'key' => $keyVal[0]
            );
        }
    }

    private function getVariable($item)
    {
        $result = array();

        $fieldKey = explode('&', $this->itemVariable['key']);
        if (count($fieldKey) == 3) {
            foreach ($item->$fieldKey[0] as $val) {
                if ($val->attributes()->$fieldKey[1] == $fieldKey[2]) {
                    $mainXmlField = $val;
                    break;
                }
            }
        } else {
            $mainXmlField = $item->$fieldKey[0];
        }

        foreach ($this->itemVariable['template'] as $key => $tamplate) {
            $variableVal = '';
            foreach ($tamplate as $val) {
                $thisField = ($val[0] == 'item' ? $item->$val[1] : $mainXmlField);
                $do = ($val[0] == 'item' ? $val[2] : $val[0]);

                if ($do == 'val') {
                    $variableVal .= ($variableVal ? ' ' : '').$thisField->__toString();
                } elseif (preg_match('/^ART_/', $do, $match)) {
                    $do = substr($do, 4);
                    $variableVal .= ($variableVal ? ' ' : '').$thisField->attributes()->$do->__toString();
                } else {
                    $variableVal .= ($variableVal ? ' ' : '').$do;
                }
            }
            $result[$key] = $variableVal;
        }
        return $result;
    }

    private function insert($item, $tabName)
    {
        $this->log('write', "INSERT INTO {$tabName} (`".implode('`,`', array_keys($item))."`) VALUES ('".implode("','", $item)."')");
        $this->db->query("INSERT INTO {$tabName} (`".implode('`,`', array_keys($item))."`) VALUES ('".implode("','", $item)."')");
        $this->log('write', "\r\n db_id = {$this->db->insert_id}--->\r\n\r\n");
    }

    private function update($item, $tabName, $where)
    {
        $query = '';
        foreach ($item as $field => $val) {
            $query .= ($query ? ',' : '')."`{$field}` = '{$val}'";
        }
        $this->log('write', "UPDATE {$tabName} SET {$query} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->db->query("UPDATE {$tabName} SET {$query} WHERE Catalogue_ID = {$this->catalogue} {$where}");
        $this->log('write', "\r\n--->\r\n\r\n");
    }

    private function write($type = '')
    {
        echo "<span class='{$type}'></span>";
        flush();
    	ob_flush();
    }

    private function offNoneExported()
    {
        $groupOff = $itemOff = array();

        foreach ($this->allGoods as $item) {
            if (!in_array($item['Message_ID'], $itemOff) && !$item['exported']) $itemOff[] = $item['Message_ID'];
        }

        if ($this->groups) {
            do {
                $groupOffCheck = $groupOff;
                foreach ($this->groups as $groupXmlId => $group) {
                    if (!in_array($group['sub'], $groupOff) && (!$group['exported'] || in_array($group['parent'], $groupOff))) $groupOff[] = $group['sub'];
                }
            } while ($groupOffCheck != $groupOff);
        }

        if ($groupOff) {
            $groupOffWhere = "Subdivision_ID in ('".implode("','", $groupOff)."')";
            $this->update(['Checked' => 0], 'Subdivision', "AND {$groupOffWhere}");
        }

        if ($itemOff) $itemOffWhere = ($groupOffWhere ? ' OR': '')." Message_ID in ('".implode("','", $itemOff)."')";

        if ($groupOffWhere || $itemOffWhere) $this->update(['Checked' => 0], 'Message2001', "AND (".($groupOffWhere ? $groupOffWhere : '').($itemOffWhere ? $itemOffWhere : '').")");
    }

    public function deleteAllSubs()
    {
        $subsData = $this->db->get_results("SELECT b.Sub_Class_ID,
                                                   a.Subdivision_ID
                                            FROM Subdivision as a,
                                                 Sub_Class as b
                                            WHERE a.Catalogue_ID = {$this->catalogue}
                                                  AND a.Subdivision_ID = b.Subdivision_ID
                                                  AND a.code1C LIKE 'YML&%'
                                                  AND b.Class_ID = '2001'", ARRAY_A);

        if (!$subsData) { $this->write('error'); return; }

        foreach ($subsData as $a) {
            $cc .= ($cc ? ',' : '').$a['Sub_Class_ID'];
            $sub .= ($sub ? ',' : '').$a['Subdivision_ID'];
        }
        $this->db->query("DELETE FROM Subdivision WHERE Catalogue_ID = {$this->catalogue} AND Subdivision_ID in ({$sub})");
        $this->db->query("DELETE FROM Sub_Class WHERE Catalogue_ID = {$this->catalogue} AND Sub_Class_ID in ({$cc})");
    }

    public function deleteAllGoods()
    {
        $this->db->query("DELETE FROM Message2001 WHERE Catalogue_ID = {$this->catalogue} AND xlslist = {$this->YMLkey}");
    }

    private function log($type = '', $text = '')
    {
        switch ($type) {
            case 'export':
                if (file_exists($this->path.'export_log_4.txt')) file_put_contents($this->path.'export_log_5.txt', file_get_contents($this->path.'export_log_4.txt'));
                if (file_exists($this->path.'export_log_3.txt')) file_put_contents($this->path.'export_log_4.txt', file_get_contents($this->path.'export_log_3.txt'));
                if (file_exists($this->path.'export_log_2.txt')) file_put_contents($this->path.'export_log_3.txt', file_get_contents($this->path.'export_log_2.txt'));
                if (file_exists($this->path.'export_log.txt')) file_put_contents($this->path.'export_log_2.txt', file_get_contents($this->path.'export_log.txt'));
                $this->log = fopen($this->path.'export_log.txt', 'wr');
            case 'write': fwrite($this->log, $text);
            break;
            case 'close': fclose($this->log);
            break;
            default: break;
        }
    }

    private function curlResponsCode($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_NOBODY => 1,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;
    }

    private function curlGetContent($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ['Accept: application/xml'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
