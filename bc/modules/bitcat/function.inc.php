<?php

use App\modules\Korzilla\Excel\Import\Controller as ImportExcelController;
use App\modules\Korzilla\Excel\Export\ExportSite\Controller as ExportSiteControlle;
use App\modules\Korzilla\Service\Delivery\Cdek\Cdek;
use App\modules\Korzilla\UploaderPhotoItems\Controller as UploderPhotoItems;

class bc {
    public $curCat, $catID;

    public function __construct() {
        global $db, $nc_core, $perm, $AUTH_USER_ID, $ADMIN_PATH, $perm, $catalogue,$current_user;
        $this->curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
        $this->catID = $this->curCat['Catalogue_ID'];
        $catalogue = $this->curCat['Catalogue_ID'];
    }

    # вызов действия
    public function init_bc($action) {
        global $catID;

        if (strstr($_SERVER['HTTP_REFERER'],"рф")) $refer = encode_host($_SERVER['HTTP_REFERER']);
        else $refer = $_SERVER['HTTP_REFERER'];
  
        if (strstr($refer, "https://".$_SERVER['HTTP_HOST']) || strstr($refer, "http://".$_SERVER['HTTP_HOST'])) {
            switch ($action) {
                case 'sortblock': return $this->sortBlocks($_POST['blockid'],$_POST['place'],$_POST['prior'],$_POST['blockid2'],$_POST['prior2']);
                case 'changezone': return $this->changeZone($_POST['blockid'],$_POST['width'],$_POST['fromzone'],$_POST['tozone'],$_POST['tozonewidth']);
                case 'dropZone': return $this->dropZone($_POST['arrzone']);
                case 'dropBlocks': return $this->dropBlocks($_POST['arrblocks']);
                case 'dropSub': return $this->dropSub($_POST['serialize']);
                case 'checkSub': return $this->checkSub($_POST['id'], $_POST['check']);
                case 'topset': return $this->topsettings();
                case 'blockvis': return $this->blockVis($_POST['blockid'],$_POST['check']);
                case 'formset': return $this->formSet();
                case 'formsetsave': return $this->formSetSave();
                case 'savecss': return $this->savecss();
                case 'sitetree': return $this->sitetree($_GET['parent']);
                case 'savecolorfont': return $this->savecolorfont($_POST['numcolor'], $_POST['namefont'], $_POST['random']);
                case 'openSaveColor': return $this->openSaveColor($_POST['id']);
                case 'saveColor': return $this->saveColor();
                case 'editsub': return $this->editSub($_GET['subdiv']);
                case 'editsubsave': return $this->editSubSave();
                case 'addsub': return $this->editSub($_GET['subdiv'],'add');
                case 'addsubsave': return $this->addSubSave();
                case 'subdelete': return $this->subDelete($_GET['subdiv']);
                case 'redirectlist': return $this->redirectList();
                case 'addredirect': return $this->addRedirect();
                case 'removeRedirect': return $this->removeRedirect($_GET['id']);
                case 'editredirect': return $this->addRedirect($_GET['rid']);
                case 'addredirectsave': return $this->addRedirectSave();
                case 'loadsetclass': return $this->LoadSetClass($_POST['subid'],$_POST['typecont'],$_POST['subidthisblk']);
                case 'getphoto': return $this->getGooglePhoto($_POST['query'], $_POST['start']);
                case 'itemsNoPhoto': return $this->itemsNoPhoto();
                case 'itemsSavePhoto': return $this->itemsSavePhoto($_POST['mess'],$_POST['googlephoto']);
                case 'support': return $this->Support();
                case 'iiko': return $this->iiko($_GET['do']);
                case 'firebaseid': return $this->FireBaseID();
                case 'buyapp': return $this->BuyApp();
                case 'getmultiline': return $this->getMultiLine();
                case 'get_manifest': return $this->getFormFromManifest($_GET['name']);
                case 'get_setting_export_1c': return $this->formSettingExport1C();
                case 'save_export_setting': return $this->saveSettingExport($_POST);
                case 'set_setting_export1c': return $this->setSettingExport1C();
                case 'delete_setting_export1c': return $this->deleteSettingExport1C($_GET['id']);
                case 'get_view_by_params_values_list': return $this->getViewByParamsValuesList();
                case 'frontpad_export_usynchronic_products_form': return $this->getFrontpadExportUsynchronicProductsForm();
                case 'frontpad_export_usynchronic_products': return $this->frontpadExportUsynchronicProducts();
                case 'frontpad_update_synced_products': return $this->frontpadUpdateSyncedProducts();
                case 'import_excel': return $this->importExcel($_GET['params_e']);
                case 'get_uploader_photo': return $this->getUploaderPhotoItems($_GET['action']) ;
            };
        } else {
            return encode_host($_SERVER['HTTP_REFERER'])." | ".$_SERVER['HTTP_HOST'];
        }
    }
    /**
     * Получение левого меню админки из манифеста
     * @return string HTML меню
     */
    public function getLeftMenuManifest() {
        global $db;
        
        $menu = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bc/modules/bitcat/manifestes/menu_manifest.json'), 1);
        
        $dateOrdersJson = $db->get_var("select ordersletter from Catalogue where Catalogue_ID = '{$this->catID}'");
        $dateOrders = json_decode($dateOrdersJson, true);
        $this->totalCountOrders = 0;
        $countOrdersName = array('cart','feedback','callme');
        if(is_array($dateOrders)){
            foreach ($countOrdersName as $name) {
                $this->countOrders[$name] = getCountOrders($name, $dateOrders[$name], $this->catID);
                $this->totalCountOrders += $this->countOrders[$name];
            }
        }

    
        return "<div class='bc_wrap'>
        <div class='left-logo'></div>
            <div class='bc_menubody'>
                <div class='bc_menubody_second'>
                    <ul id='bc_adminmenu'>
                        ".$this->createMenuAdmin($menu)."
                        ".$this->createMenuOther()."
                    </ul>
                </div>
            </div>
        </div>";
    }

    public function createMenuOther()
    {
        global $login;

        $otherManifestMenuJson = $_SERVER['DOCUMENT_ROOT'] . "/b/{$login['login']}/manifest/menu_manifest.json";

        if (!file_exists($otherManifestMenuJson)) return '';
        $otherManifestMenu = json_decode(file_get_contents($otherManifestMenuJson), 1);

        return $this->createMenuAdmin($otherManifestMenu);
    }

    /**
     * Поиск файлов по patrern в директории
     * @param string $path - путь в директории где ищем
     * @param string $pattern - наименование файла
     * @param string $_base_path - системная переменая 
     * @return array
     */
    public function globTreeFiles($path, $pattern, $_base_path = null)
    {
        if (is_null($_base_path)) {
            $_base_path = '';
        } else {
            $_base_path .= basename($path) . '/';
        }
     
        $out = array();
        foreach(glob($path . '/' . $pattern, GLOB_BRACE) as $file) {
            $out[] = $_base_path . basename($file);
        }
        
        foreach(glob($path . '/*', GLOB_ONLYDIR) as $file) {
            $out = array_merge($out, $this->globTreeFiles($file, $pattern, $_base_path));
        }
     
        return $out;
    }

    /**
     * Метод создания меню из манифеста рекурсией
     * @param array $menu массив из манифеста меню
     * @param string $classLvl на даный момент устанавливаеться автоматический
     * @return string HTML меню
     */
    private function createMenuAdmin($menu, $classLvl = 'first')
    {
        $menuHtml = '';
        foreach($menu as $key => $li) {
            if ($li['permission'] && !$this->getPermission($li['permission'])) continue;
    
            $data = $tabs = $className = []; $link = $href = '';
            #data
            foreach($li['data'] as $dataName => $dataValue) {
                $data[] = "data-{$dataName}='{$dataValue}'";
            }
            $li['tabs'] = (isset($li['tabs']) ? $li['tabs'] : []);
            foreach($li['tabs'] as $tabName => $tabVal) {
                if ($tabVal['permission'] && !$this->getPermission($tabVal['permission'])) continue;
                if(!$link) $link = $tabVal['link'];
                $tabs[] = "\"{$tabName}\":\"{$tabVal['link']}\"";
            }
    
            if ($li['link']) {
                $data[] = "data-link='".($link ? $link : $li['link'])."'";
            }
            if ($li['link_blank']) {
                $href = $li['link_blank'];
                $data[] = 'target="_blank"';
            }
    
            if($tabs) $data[] = "data-links='{".implode(",", $tabs)."}'";
            #end data
    
            # Число рядом с наименованием
            $number_nextto_name = (isset($li['number_nextto_name']) ? eval('return ($this->'.$li['number_nextto_name'].' > 0 ? "<div class=\'adm-count\'>{$this->'.$li['number_nextto_name'].'}</div>" : "");') : '');

            $nameHTML = "<a ".implode(' ', $data)." href='{$href}'><span>{$li['name']}</span>{$number_nextto_name}</a>";
    
            $next_lvl = ($li['second'] ? "<div class='adm-second'><ul>".$this->createMenuAdmin($li['second'], 'second')."</ul></div>" : '');
            # className
            if(!$next_lvl && $classLvl == 'first') $className[] = 'nochild';
            if ($classLvl == 'first') $className[] = "adm-first";
            if($li['class_name']) $className[] = $li['class_name'];
    
            $liHTML = "<li class='".implode(' ', $className)."'>
                            ".($classLvl == 'first' ? "<div class='adm-{$classLvl}-name'>{$nameHTML}</div>" : $nameHTML)."
                            {$next_lvl}
                        </li>";
        
            $menuHtml .= $liHTML;
        }
        return $menuHtml;
    }
    /**
     * Метод для условия вывода блока или формы меню админки
     * @param string $permissions условия для определения прав
     * @return bool
     */
    public function getPermission($permissions)
    {
        global $setting, $AUTH_USER_ID;
        // if ($AUTH_USER_ID == 681) {
        //     file_put_contents('/var/www/krza/data/www/krza.ru/b/ilsur/test_per.lоg', print_r( [(int) eval('return ('.str_replace(['(', ')'], ['permission(', ')'] ,$permissions).' ? true : false);'), 'return ('.str_replace(['(', ')'], ['permission(', ')'] ,$permissions).' ? true : false);'], 1), FILE_APPEND);
        // }
        return eval('return ('.str_replace(['(', ')'], ['permission(', ')'] ,$permissions).' ? true : false);');
    }
    /**
     * @param string $name Наименование файла манифеста с формой
     * @return string HTML 
     */
    public function getFormFromManifest($name)
    {
        global $setting, $db, $login, $HTTP_FILES_PATH, $AUTH_USER_ID;

        $pathManifest = $_SERVER['DOCUMENT_ROOT']."/bc/modules/bitcat/manifestes/{$name}.json";

        if (!file_exists($pathManifest)) {
            $pathManifest = $_SERVER['DOCUMENT_ROOT'] . "/b/{$login['login']}/manifest/{$name}.json";
            if (!file_exists($pathManifest)) return 'Манифест не найден';
        }

        $arrayMenu = json_decode(file_get_contents($pathManifest), 1);

        $formHtmlBody = ''; $paramPackAll = $inputHidden = [];

        $blackListRowName = ['notes', 'kurs'];

        foreach ($arrayMenu as $blockId => $block) {
            $formInp = '';
            # Перебор полей настроек
            if (isset($block['permission']) && !$this->getPermission($block['permission'])) continue;

            foreach ($block['row'] as $row) {
                if (isset($row['permission']) && !$this->getPermission($row['permission'])) continue;

                if (!in_array($row['name'], $blackListRowName)) {
                    $paramPack = "bc_".$row['name'];
                    # Все используемые поля в на странице, через запятую
                    $paramPackAll[] = $paramPack;
                }
                $set_name = $row['label'];
                $dataList = '';

                if ($row['default_value']['data']) {
                    $defaultValue = $row['default_value']['data'];
                } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $row['default_value']['url'])) {
                    $defaultValue = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $row['default_value']['url']), 1);
                } else {
                    $defaultValue = null;
                }
                
                if ($defaultValue && empty($setting[$row['name']])) $setting[$row['name']] = $defaultValue;

                switch ($row['type']) {
                    case 'text': # строка
                        $formValue = bc_input($paramPack, $setting[$row['name']] ?: $row['default_value'], $set_name);
                        break;
                    case 'select': # списки
                        if ($row['data']) {
                            foreach($row['data'] as $key => $field) {
                                $dataList .= "<option ".($key==$setting[$row['name']] ? "selected" : null)." value='{$key}'>{$field}</option>";
                            }
                        }
                        $formValue = bc_select($paramPack, $dataList, $set_name, "class='ns'");
                        break;
                    case 'checkbox': # чекбоксы
                        $formValue = bc_checkbox($paramPack, 1, $set_name, $setting[$row['name']]);
                        break;
                    case 'sitename': # название сайта
                        $nameSite = $db->get_var("SELECT Catalogue_Name FROM `Catalogue` WHERE Catalogue_ID = '{$this->catID}'");
                        $formValue = bc_input($paramPack, $nameSite, $set_name);
                        break;
                    case 'multi_line': # Списки
                        $formValue = bc_multi_line($paramPack, $setting[$row['name']], $set_name, ($row['type_view'] ?: 1), ($row['params'] ?: []));
                        break;
                    case 'multi_line_v2': # Списки
                        $formValue = bc_multi_line_v2($paramPack, $setting[$row['name']], $set_name, ($row['params'] ?: []));
                        break;
                    case 'lists_texts': # Списки
                        $setting['lists_texts'] = ($setting['lists_texts'] ? $setting['lists_texts'] : orderArray($db->get_var("SELECT value FROM Bitcat WHERE `key` = 'lists_texts'")));
                        foreach ($setting['lists_texts'] as $key => $lng) {
                            $setting['lists_texts'][$key]['keyword'] = $key;
                        }
                        $setting['lists_texts']['value'] = array_values($setting['lists_texts']);
                        $formValue = bc_multi_line($paramPack, $setting[$row['name']]['value'], $set_name, ($row['type_view'] ?: 1), ($row['params'] ?: []));
                        break;
                    case 'notes': # анатация
                        $formValue = "<div class='headr_notes'>{$set_name}</div><div class='notes'>{$row['text']}</div>";
                        break;
                    case 'sub_select': # Вывод дерева разделов
                        foreach (getSubSelectOption($row['data']['class'] ?: 2001, $row['data']['separator'] ?: '') as $subID => $name) {
                            $dataList .= "<option ".($subID==$setting[$row['name']] ? "selected" : null)." value='{$subID}'>{$name}</option>";
                        }
                        $formValue = bc_select($paramPack, $dataList, $set_name, "class='ns'");
                        break;
                    case 'color': # цвета
                        $formValue = bc_color($paramPack, $setting[$row['name']], $set_name);
                        break;
                    case 'kurs':
                        global $kurs;
                        $formValue = kz_input($paramPack, $kurs[$row['name']], $set_name, ['readonly' => 1]);
                        break;
                    case 'file': # файлы
                        $formValue = bc_file($paramPack, $setting[$row['name']], $set_name, $HTTP_FILES_PATH . $setting[$row['name']], 'bc');
                        break;
                    case 'textarea':
                        // if($setkey=='robot') $set['value'] = $db->get_var("SELECT Robots FROM `Catalogue` WHERE Catalogue_ID = '{$this->catID}'");
                        // if ($setkey=='siteOffText') $set['value'] = $db->get_var("SELECT ncOfflineText FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
                        $formValue = bc_textarea($paramPack, $setting[$row['name']], $set_name, "id='{$paramPack}'");
                        break;
                    case 'registration':
                        $value = $setting[$row['name']] ?: file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template/class/sys/default_fields_reg_form.json');
                        // if($setkey=='robot') $set['value'] = $db->get_var("SELECT Robots FROM `Catalogue` WHERE Catalogue_ID = '{$this->catID}'");
                        // if ($setkey=='siteOffText') $set['value'] = $db->get_var("SELECT ncOfflineText FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
                        $formValue = bc_textarea($paramPack, $value, $set_name, "id='{$paramPack}'");
                        break;
                    case 'hidden':
                        $formValue = "<input type='hidden' name='{$paramPack}' value='{$setting[$row['name']]}'>";
                        break;
                    case 'btn_a':
                        $formValue = bc_button_a($set_name, $row['data']);
                        break;
                    case 'tags':
                        $formValue = (function(){
                            $settings = [
                                'cols' => [
                                    'tag' => [
                                        'type' => 'input',
                                        'name' => 'tag',
                                        'title' => 'тэг',
                                        'col' => 1,
                                    ]
                                ]
                            ];

                            $tagProvider = new \App\modules\Korzilla\Tag\Provider();

                            foreach ($tagProvider->tagGetList() as $tag) {
                                $settings['values'][$tag->Message_ID] = [
                                    'tag' => $tag->tag,
                                ];
                            }

                            return bc_multi_line('bc_tags', json_encode($settings), '', 3);
                        })();
                        break;
                }

                $class = [
                    'colline',
                    "colline-{$row['colline']}",
                    "type-{$row['type']}",
                    "name-{$row['name']}"
                ];
                if (isset($row['type_old'])) {
                    $class[] = "type-{$row['type_old']}";
                }
                $formInp .= "<div class='".implode(' ', $class)."'>{$formValue}</div>";
            }

            if ($block['path_view']) {
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $block['path_view'])) continue;
                $formInp = @include($_SERVER['DOCUMENT_ROOT'] . $block['path_view']);
                if (!$formInp) continue;
            }

            $formHtmlBody .= "<div class='colblock' id='gr_{$blockId}'>
                                <h3>{$block['name']}</h3>
                                <div class='colblock-body'>
                                    {$formInp}
                                </div>
                            </div>";
        }
        # группы
        $formHtml = "<form class='bc_form ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=formsetsave&name_manifest={$name}' method='post'>
                <div class='formwrap'>
                    <input type=hidden name='bc_field' value='" . implode(',', $paramPackAll) . "'>
                    <input type=hidden name='bc_action' value='formsetsave'>
                    " . implode(' ', $inputHidden) . "
                    {$formHtmlBody}
                </div>
                <div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения'></span><div class='result respadding'></div></div></div>
            </form>";

        return $formHtml;
    }

    private function deleteSettingExport1C($id)
    {
        if (!$id) return json_encode(["error" => 'Нет ID']);

        $settingsExport = getSettingsExport();

        unset($settingsExport[$id]);

        if (setSettingsExport($settingsExport)) {
            $reslt = json_encode([
                "succes" => '1'
            ]);
        } else {
            $reslt = json_encode([
                "error" => 'Не удалено '.print_r($settingsExport, 1)
            ]);
        }

        return $reslt;

    }
    private function formSettingExport1C() {

        $settingsExport = getSettingsExport();

        $form = '';

        if (is_array($settingsExport)) {
            foreach ($settingsExport as $id => $params) {
                if ($params['export_type'] == '1c') $form .= $this->getFormExport($id, '1c');
            }
        }
        
        return $form
                ."<div class='view-body-inline addFrom'><a class='bc-btn-green' href='' onclick='Export1C.addFormSettingExport(this); return false;'>Добавить выгрузку</a></div>";
    }
    private function getFormExport($idFrom, $type)
    {
        $settingsExport = getSettingsExport($idFrom);

        $paterns = [
            '1c' => [
                'id' 			=> ['type' => 'hidden'],
                'root_sub'		=> ['name' => 'Корневой каталог', 'colline' => 3, 'type' => 'select', 'params' => [], 'data' => array_column(get_subs_class(2001), 'name', 'sub')],
                'noprice' 		=> ['name' => 'Выгружать без цен', 'colline' => 3, 'type' => 'checkbox', 'params' => []],
                'nooffers'		=> ['name' => 'Обрабатывать без файла offers.xml', 'colline' => 3, 'type' => 'checkbox', 'params' => []],
                'v1c'			=> ['name' => 'Номер 1С папки', 'colline' => 3, 'type' => 'input', 'params' => ['type' => 'number']],
                'export_type'	=> ['type' => 'hidden'],
            ]
        ];
        $form = bc_export_form($settingsExport, $paterns[$type]);

        return "<div class='view-body-inline'>{$form}</div>";

    }
    private function setSettingExport1C()
    {
        $idNew = uniqid('1c_');

        $defaultData = [
            'id' => $idNew,
            'root_sub' => '',
            'noprice' => 0,
            'nooffers' => 0,
            'v1c' => 1,
            'export_type' => '1c'
        ];

        $this->saveSettingExport($defaultData);

        return $this->getFormExport($idNew, '1c');
    }

    private function saveSettingExport($data) {
        
        if (!isset($data['id'])) {
            return json_encode([
                "error" => 'Нет ID'
            ]);
        }

        $id = $data['id'];
        $settingsExport = getSettingsExport();
        $settingsExport[$id] = $data;

        if (setSettingsExport($settingsExport)) {
            $reslt = json_encode([
                "succes" => 'Сохранено'
            ]);
        } else {
            $reslt = json_encode([
                "error" => 'Не сохранено'
            ]);
        }

        return $reslt;
    }

    # support
    public function Support() {
        return "<iframe src='//support.korzilla.ru/?v=3' scrolling=auto frameborder=no border=0 height=800 width=100%></iframe>";
    }
    # купить мобильное прилжение 
    public function BuyApp() {
        return "<iframe src='https://korzilla.ru/mobile/?template=1' scrolling=auto frameborder=no border=0 height=800 width=100%></iframe>";
    }

    # itemsnoph
    public function itemsNoPhoto() {
        global $db, $setting;

        # проверка на доступ
        if((!permission('monster') && $_SERVER[REMOTE_ADDR]!='31.13.133.138') && !$setting['powerseo_super']) return "";

        $subs = $db->get_col("select a.Subdivision_ID from Subdivision as a, Sub_Class as b where a.Checked = 1 AND a.Subdivision_ID = b.Subdivision_ID AND a.Catalogue_ID = '{$this->catID}' AND b.Class_ID = '2001'");
        $sql = "select a.Message_ID as id from Message2001 as a where a.Checked = 1 AND a.Catalogue_ID ='{$this->catID}' AND a.Subdivision_ID IN (".implode(",", $subs).") AND a.Message_ID NOT IN (SELECT b.Message_ID FROM Multifield as b where b.Path like '/a/{$this->curCat[login]}/%') ORDER BY RAND() LIMIT 0,100";
        $items = $db->get_col($sql);
        $html = array();

        if ($items) {
            foreach($items as $id) {

                # объект товара
                $item = Class2001::getItemById($id);
                # если есть фото пропустить
                if(!$item || !$item->nophoto) continue;

                $name = addslashes(str_replace('"', '', str_replace('\'', '', $item->name)));

                $html[] = "<tr data-orderid='{$id}' class='photo-search-item'>
                                <td class='t-id'>{$id}</td>
                                <td class='t-name-link'><a target=_blank href='".nc_message_link($id, 2001)."'>{$name}</a> - {$item->priceHtml}</td>
                                <td class='t-name t-input'><input name='f_name' type='text' value='{$name}'><a href='' class='icons add-btn ws' data-name='name'><span>По названию</span></a></td>
                                <td class='t-id t-input'><input name='f_art' type='text' value='{$item->art}'><a href='' class='icons add-btn ws' data-name='art'><span>По артикулу</span></a></td>
                            </tr>";
            }
        } else {
            $html[] = "<div class='v-line center nobefore'><div class='v-inline'>Нет товаров без фото!</div></div>";
        }

        $resilt = "<table class='table-order table-search-photo'>
                        <thead>
                            <tr>
                                <th class='t-id'>ID</th>
                                <th class='t-link'>Ссылка</th>
                                <th class='t-name'>Название (поисковый запрос)</th>
                                <th class='t-art'>Артикул (поисковый запрос)</th>
                            </tr>
                        </thead>
                        <tbody>".implode("", $html)."</tbody>
                    </table>";

        return $resilt;
    }

    public function itemsSavePhoto($mess, $googlephoto='') {
        global $db, $setting;
        # проверка на доступ
        if((!permission('monster') && $_SERVER[REMOTE_ADDR]!='31.13.133.138') && !$setting['powerseo_super']) exit;

        if ($googlephoto && count($googlephoto)>0) {
            $res = savePhotoByGoogle($mess, $googlephoto);
            return json_encode(ARRAY(
                "title" => "ОК",
                "succes" =>  " ".($res>0 ? "добавлено {$res} фото" : "ОШИБКА, ни одно фото не добавлено {$res}")." ",
            ));
        } else {
            return json_encode(ARRAY(
                "title" => "error",
                "succes" =>  "выберите фото",
            ));
        }
    }

    # google photo get
    public function getGooglePhoto($query, $start=1) {
        global $db, $setting;
        $keyword = $query;

        # проверка на доступ
        if((!permission('monster') && $_SERVER[REMOTE_ADDR]!='31.13.133.138') && !$setting['powerseo_super']) exit;

        $keyword = str_replace('.', ' ', $keyword);
        $keyword = str_replace('!', ' ', $keyword);
        $keyword = str_replace('\'', ' ', $keyword);
        $keyword = str_replace('\'', ' ', $keyword);
        $keyword = str_replace('"', ' ', $keyword);
        $keyword = str_replace('\\', ' ', $keyword);
        $keyword = str_replace(',', ' ', $keyword);
        $keyword = str_replace('  ', ' ', $keyword);
        $keyword = str_replace(' ', '+', $keyword);

        $api_key[7] = 'AIzaSyCyPLDMGcgpLXFF6pAFdjEbcB-0BnL1c-g';
        $cx[7] = '006430078468389998113:-j2neaelooq';
        $api_key[8] = 'AIzaSyAoxAZcVqAjrPzFp2g8OMUnw_z47fV_B-o';
        $cx[8] = '000861350156678644673:5c4ziple_k4';
        $api_key[9] = 'AIzaSyA_Kdla4yky3ckHASZ8FEjTwFOYkXOpi-A';
        $cx[9] = '004311787534680949791:bfm5nh8dwnc';
        $api_key[1] = 'AIzaSyBA920HUBld3uJCEnjqo_cXCxaqco9MkPE';
        $cx[1] = '009502872693368451373:jtbgzaubygk';
        $api_key[2] = 'AIzaSyBy6WVj7j4VO4gmYgMAXaXihqDXe8OCf08';
        $cx[2] = '004945856064388454407:yoltp4htsg8';
        $api_key[3] = 'AIzaSyCWJw7MMbC6tSUuiR25AJLbg-fDdYkCVEo';
        $cx[3] = '009502872693368451373:cj11d2qg_hc';
        $api_key[4] = 'AIzaSyDfJ5O_K0ynIZEo1rv2VgjkR9DMGI5i4Q0';
        $cx[4] = '009502872693368451373:br-2lkvcouo';
        $api_key[5] = 'AIzaSyDkAV8vBsLkGtNzRcwS7G0AsnureeVEeEs';
        $cx[5] = '005774390936313776366:9mflitjdrtw';
        $api_key[6] = 'AIzaSyAwGhC-WjutD_L0OGXmBb23x2AJCS-MyWE';
        $cx[6] = '012981717858460304928:kngqqogpnni';


        $rand = rand(1,9);

        if(!is_numeric($start)) $start = 1;

        $url = 'https://www.googleapis.com/customsearch/v1?q='.urlencode($keyword).'&searchType=image&start='.$start.'&num=8&cx='.$cx[$rand].'&key='.$api_key[$rand];
        $page = @file_get_contents($url);
        if (!$page) {
            return json_encode(ARRAY(
                "status" => "error",
                "html" => 'Исчерпаны лимиты на сегодня '.$rand
            ));
        }
        $dataimg = json_decode($page);
        $images = array();
        if($dataimg->items) {
            foreach($dataimg->items as $result) {
                $images[] = $result->link;
                if ($result->image->width > 300 && $result->image->width < 2000){
                    $imagesHtml .= "<label>
                                        <input type='checkbox' value='{$result->link}' name='googlephoto[]'>
                                        <div class='image-default'><img src='{$result->link}' alt='{$query}'></div>
                                        <div class=s><a href='{$result->link}' data-rel='lightcase:image-insearch'>увеличить</a></div>
                                        <span>{$result->image->width}x{$result->image->height}</span>
                                    </label>";
                }
            }

            if($_POST['panel'] && $imagesHtml){
                $imagesHtml = "<form method='post' class='ajax2' action='/bc/modules/bitcat/index.php?bc_action=itemsSavePhoto'>
                                    <input type='hidden' name='mess' value='{$_POST['id']}'>
                                    {$imagesHtml}
                                    <div class='result'></div>
                                    <div class='btn-strt'><input type='submit' value='Добавить'></div>
                                </div>";
            }

            return json_encode(ARRAY(
                "status" => "ok",
                "html" => $imagesHtml
            ));
        } else {
            return json_encode(ARRAY(
                "status" => "error",
                "html" => $page."<br>".$url
            ));
        }

    }

    # сортировка блоков
    public function sortBlocks($blockid,$place,$prior,$blockid2,$prior2) {
        global $db;
        if (is_numeric($blockid) && is_numeric($prior) && is_numeric($blockid2) && is_numeric($prior2)) {
            if ($prior2==$prior) {
                if ($place=='up') $prior=$prior+1;
                if ($place=='down') $prior2=$prior2+1;
            }
            $db->query("update Message2016 set Priority = '$prior2', cache = '' where Message_ID = '$blockid'");
            $db->query("update Message2016 set Priority = '$prior', cache = '' where Message_ID = '$blockid2'");
            $otvet = '{"status":"ok","block":"'.$prior2.'","block2":"'.$prior.'","g":"'.$g.'"}';
            return $otvet;
        }
    }


    # перемещение блоков в другую зону
     public function changeZone($blockid,$width,$fromzone,$tozone,$tozonewidth) {
        global $db;
        if (is_numeric($blockid) && is_numeric($fromzone) && is_numeric($tozone)) {
            if($width>$tozonewidth) $width = $tozonewidth;
            if ($fromzone!=$tozone) {
                $rr = "update Message2016 set col = '$tozone', ".($width ? "width='{$width}'," : NULL)." cache = '' where block_id = '$blockid' AND  Catalogue_ID = '{$this->catID}'";
                $db->query($rr);
                $otvet = '{"status":"ok","blockzone":"'.$tozone.'","width":"'.$width.'","dsfsd":"'.$rr.'"}';
            }
            return $otvet;
        }
    }
    # перемещение Зон
     public function dropZone($arrzone) {
        global $db;
        if ($arrzone) {
            $clearArr = securityForm($arrzone);
            foreach ($clearArr as $pos => $clearArrSec) {
                foreach ($clearArrSec as $k => $v) {
                    if(is_numeric((int)$k) && is_numeric((int)$v['id']) && is_numeric((int)$pos)){
                        $sql = "update Message2000 set zone_priority = {$k}, zone_position = {$pos} where zone_id = {$v['id']} AND Catalogue_ID = '{$this->catID}'";
                        //$res .= $sql;
                        $db->query($sql);
                    }
                }
            }
            $otvet = '{"status":"ok","response":"Расположения зон сохранены"}';
            return $otvet;
        }
    }
    # перемещение Блоков
     public function dropBlocks($arrblocks) {
        global $db;
        if ($arrblocks) {
            $clearArr = securityForm($arrblocks);
            foreach ($clearArr as $i => $v) {
                if(is_numeric((int)$v['zone-id']) && is_numeric((int)$v['gridblk']) && is_numeric((int)$v['block-id'])){
                    $sql = "update Message2016 set Priority = {$i}, width = {$v['gridblk']}, col = {$v['zone-id']} where Message_ID = {$v['block-id']} AND Catalogue_ID = '{$this->catID}'";
                    $res .= $sql."<br>";
                    $db->query($sql);
                }
            }
            $otvet = '{"status":"ok","response":"Расположения блоков сохранены '.$res.'"}';
            return $otvet;
        }
    }
    # перемещение Разделов
     public function dropSub($serialize) {
        global $db;
        if ($serialize) {
            $arrSerialize = securityForm($serialize);
            foreach ($arrSerialize as $k => $v) {
                if($v['id']){
                     $engName = $db->get_row("select Parent_Sub_ID, EnglishName from Subdivision where Subdivision_ID = {$v['id']} AND Catalogue_ID = '{$this->catID}'", ARRAY_A);
                    if($v['parent_id']){
                         $URLParent = $db->get_var("select Hidden_URL from Subdivision where Subdivision_ID = {$v['parent_id']} AND Catalogue_ID = '{$this->catID}'");
                         $URLsub = $URLParent.$engName['EnglishName'].'/';
                    }else{
                         $URLsub = '/'.$engName['EnglishName'].'/';
                    }
                    $sql = "update Subdivision set Priority = {$k} ".
                        ($URLsub ? ", Hidden_URL = '{$URLsub}' " : null).
                        ($engName['Parent_Sub_ID'] != $v['parent_id'] ? ", Parent_Sub_ID = ".($v['parent_id']>0 ? $v['parent_id'] : 0)." " : null).
                        " where Subdivision_ID = {$v['id']} AND Catalogue_ID = '{$this->catID}'";
                    $res .= $sql."<br>";
                    $db->query($sql);
                    if((int)$engName['Parent_Sub_ID'] != (int)$v['parent_id']) $res .= $this->findsub($v['id']);
                    unset($engName);
                }
            }

            return json_encode(ARRAY(
                "status" => "ok",
                "response" => "Расположения разделов сохранены"
            ));
        }
    }
     public function findsub($id) {
        global $db;
         $subs = $db->get_col("select Subdivision_ID from Subdivision where Parent_Sub_ID = {$id} AND Catalogue_ID = '{$this->catID}'");
         foreach ($subs as $v) {
             if($v>0){
                 $URLParent = $db->get_var("select Hidden_URL from Subdivision where Subdivision_ID = {$id} AND Catalogue_ID = '{$this->catID}'");
                $engName = $db->get_var("select EnglishName from Subdivision where Subdivision_ID = {$v} AND Catalogue_ID = '{$this->catID}'");
                $URLsub = $URLParent.$engName.'/';
                $sql = "update Subdivision set Hidden_URL = '{$URLsub}' where Subdivision_ID = {$v} AND Catalogue_ID = '{$this->catID}'";
                $res .= $sql."<br>";
                $db->query($sql);
                $res .= $this->findsub($v);
             }
         }
         return $res;
     }


    # раздел вкл выкл
    public function checkSub($id, $check)
    {
        global $db;
        if (is_numeric($check) && is_numeric($id)) {
            $db->query("update Subdivision set Checked = '".($check==1 ? 1 : 0)."' where Subdivision_ID = '$id' AND Catalogue_ID = '{$this->catID}'");
            $otvet = '{"status":"ok"}';
            return $otvet;
        }
    }


    # блок основных настроек
    public function topsettings()
    {
        return json_encode([
            "status" => "ok",
            "topLine" => $this->getTopLine(),
            "leftMenu" => $this->getLeftMenuManifest()
        ]);
    }

    # все доступные цветовые схемы
    public function getColors($id = '') {
        global $HTTP_COLORS_PATH, $catalogue, $db;

        if($id>0){
            $color = $db->get_results("SELECT Name, Colors FROM Template_Color WHERE Catalogue_ID = {$this->catID} AND ID = {$id}");
            return $color;
        }else{
            $colors = $db->get_results("SELECT ID, Name, Colors FROM Template_Color WHERE Catalogue_ID = {$this->catID}", ARRAY_A);
            return $colors;
        }
    }

    # взять TopLine
    public function getTopLine() {
        global $DOCUMENT_ROOT, $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $HTTP_COLORS_PATH, $current_catalogue, $current_sub, $sub;
        if ($perm->GetUserID()) $userName = @$db->get_var("select Login from User where User_ID = '{$perm->GetUserID()}'");

        /* Вывод всех достыпных цветов */
        $allColors = $this->getColors();
        $colorsHtml = array();
        if($allColors){
            foreach ($allColors as $key => $color) {
                $spanColors = array();

                foreach (json_decode($color[Colors], 1) as $hex) $spanColors[] = "<span style='background:{$hex}'></span>";

                $colorsHtml[] = "<div class='sc-col-item' data-numcolor='{$color[ID]}'>
                                    ".(permission('monster') ? "<div class='block-edit-content block-edit-obj-btn'>
                                        <a class='btn-a btn-a-edit icons admin_icon_7' title='Редактировать цветовую схему' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"groupClass\":\"modal-nopaddding modal-edit modal-addcolor\"}' href='/bc/modules/bitcat/index.php?bc_action=openSaveColor&id={$color[ID]}'><span>
                                        </span></a>
                                    </div>" : "")."
                                    <div class='sc-col-item-body'>
                                        <!--<div class='sc-col-item-name'>{$color[Name]}</div>-->
                                        <div class='sc-col-item-color'>".implode("", $spanColors)."</div>
                                        <div class='sc-col-item-num'>№{$key}</div>
                                    </div>
                                </div>";
            }
        }
        /* Вывод всех достыпных шрифтов */
        $allfonts= '';
        $fonts_lists = getFonts();
        $i = 0;
        foreach ($fonts_lists as $name => $font) {
            $i++;
            if($name == 'clear') $allfonts .= "<div class='sc-col-item' data-namefont='clear'>
                                    <div class='sc-col-item-num'>№{$i}</div>
                                    <div class='sc-col-item-name'>Noto Sans</div>
                                    <div class='sc-col-item-text'><span></span></div>
                                </div>";
            else $allfonts .= "<div class='sc-col-item' data-namefont='".str_replace(' ', '+', $name)."'>
                                <div class='sc-col-item-num'>№{$i}</div>
                                <div class='sc-col-item-name'>{$name}</div>
                                <div class='sc-col-item-text'><span style='background-position:{$font[posimg]}'></span></div>
                            </div>";
        }

        // Кнопка Выбора цвета
        /*$colorbtn = "<div class='bc_element bc_getcolor'>
            <div class='bc_element_head'><span></span></div>
            <div class='bc_element_body bc_element_line'>
                <div class='bc_inner'>
                    <div class='bc_name'>
                        <div class='bc_name_tit'>Цветовые решения</div>
                        <span class='bc_clear'>сбросить</span>
                    </div>
                    <div class='bc_select_items'>
                        <div class='bc_select_item owl-carousel'>".implode($colorsHtml, " ")."</div>
                    </div>
                    <div class='bc_close'></div>
                    ".(permission('monster') ? "<div class='bc_savecolor'><a href='/bc/modules/bitcat/index.php?bc_action=openSaveColor' title='Добавить цветовую схему' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"groupClass\":\"modal-nopaddding modal-edit modal-addcolor\"}'>Сохранить цв. схему</a></div>" : "")."
                </div>
            </div>
        </div>";*/

        // Кнопка Выбора шрифта
        $fontbtn = "<div class='bc_element bc_getfont'>
            <div class='bc_element_head'><span></span></div>
            <div class='bc_element_body bc_element_line'>
                <div class='bc_inner'>
                    <div class='bc_name'>
                        <div class='bc_name_tit'>Шрифтовые решения</div>
                        <span class='bc_clear'>сбросить</span>
                    </div>
                    <div class='bc_select_items'>
                        <div class='bc_select_item owl-carousel'>{$allfonts}</div>
                    </div>
                    <div class='bc_close'></div>
                </div>
            </div>
        </div>";

        // Переключатель настроек
        $adminsett = array("1" => "Просмотр", "2" => "Редактирование", "3" => "Зоны");
        foreach ($adminsett as $key => $admsett) {
            if(!permission('zoneAndBlocks') && $key==3) continue;
            if($auth_hash) $_COOKIE["adminkorzilla"] = 2; # после разворачивания сайта - режим редактирвания
            $alladminsett .= "<label><input type='radio' name='scsett' value='{$key}' ".($_COOKIE["adminkorzilla"]==$key || (!$_COOKIE["adminkorzilla"] && $key == 1) ? "checked" : "")."><span class='sc-set-{$key}'>{$admsett}</span></label>";
        }
        $alladminsett = "<div class='sc-settings'>
                            <div class='sc-set'>{$alladminsett}</div>
                        </div>";

        // Кнопка настройки профиля
        if(permission('file')){
            $modalCKfinder = "<div class='bc_element bc_ckfinder'>
                                <div class='bc_element_head'>
                                        <a href='//{$_SERVER['HTTP_HOST']}/bc/editors/ckeditor4/ckfinder/ckfinder.html?type=Images&CKEditorFuncNum=1&langCode=ru&mainpage=1' data-rel='lightcase' data-lc-options='{\"maxWidth\":1100,\"groupClass\":\"ck-filemanager\"}''></a>
                                </div>
                            </div>";
        }
        // Кнопка настройки профиля
		
		$profile_img = nc_file_path('User', $AUTH_USER_ID, 'ForumAvatar');
		if (!$profile_img || !file_exists($DOCUMENT_ROOT.$profile_img)) $profile_img = "/bc/modules/bitcat/img/no_avatar.png";
		
        $profilebtn = "<div class='bc_element bc_profile'>
                            <div class='bc_element_head'><div class='bc_profile_img'><img src='".$profile_img."' alt=''></div></div>
                            <div class='bc_element_body'>
                                <div class='bc_inner'>
                                    <div class='bc_el_head'>{$userName}</div>
                                    <div class='bc_el_body'>
                                        <div class='bc_el_line bc_el_profile'><a href='/profile/'>Профиль</a></div>
                                        <div class='bc_el_line bc_el_zayavki'><a href='' onclick='bc_showsettings(false, 603); return false;'>Заявки</a></div>
                                        <div class='bc_el_line bc_el_password'><a href='/profile/#user3'>Пароль</a></div>
                                        <div class='bc_el_line bc_el_exit'><a href='/bc/modules/auth/?logoff=1&REQUESTED_FROM=/&REQUESTED_BY=GET'>Выйти</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>";
        // SUPER INFO
        $tarifs = $db->get_results("SELECT sitetype_ID as id, sitetype_Name as name FROM Classificator_sitetype", ARRAY_A);
        foreach ($tarifs as $t) if($t['id'] == $current_catalogue['sitetype_id']) $tarif = $t['name'];
        $superinfo = "<div class='superinfo'>
                            <div class='si-line'><span class='si-title'>C:</span> <span class='si-value'>".$current_catalogue['Catalogue_ID']."</span></div>
                            <div class='si-line'><span class='si-title'>S:</span> <span class='si-value'>-</span></div>
                            <div class='si-line'><span class='si-title'>L:</span> <span class='si-value'>".$current_catalogue['login']."</span></div>
                            <div class='si-line'><span class='si-title'>SEO:</span> <span class='si-value'>".($current_catalogue['seo'] ? "on" : "off")."</span></div>
                            <div class='si-line'><span class='si-title'>Дизайн:</span> <span class='si-value'>".($current_catalogue['design'] ? "on" : "off")."</span></div>
                            <div class='si-line'><span class='si-title'>Т:</span> <span class='si-value'>{$tarif}</span></div>
                            ".(permission("monster") && !permission('design', 'only') ? "<div class='si-line seperate-link'><span>Separate code</span></div>" : "")."
                        </div>";

        $topline = "<div class='bc_topline'>
                        <span class='bc_logo'></span>
                        ".(!stristr($_SERVER[HTTP_HOST],'krza.ru') ? "<div class='bc_interes'><iframe id=bc_interes src='' scrolling=no frameborder=no border=0></iframe></div>
                        <script>
                            window.onload = function(){
                             setTimeout(function(){
                               document.getElementById('bc_interes').src = '//start.korzilla.ru/intresting123/';
                             },7000);
                            };
                        </script>" : NULL)."
                        ".($setting[сolorscheme] ? $colorbtn : "")."
                        ".(permission('fontScheme') ? $fontbtn : "")."
                        {$modalCKfinder}
                        {$profilebtn}
                        {$alladminsett}
                        ".(permission('superadmin') || permission("monster") ? $superinfo : "")."
                        <div class='bc_settings'>
                            <div class='openpanel'><span></span>".LNG_sitesettings_GR."</div>
                        </div>
                    </div>";


        return $topline;
    }


    # блоков вкл выкл
    public function blockVis($blockid,$check) {
        global $db, $AUTH_USER_ID;
        if (is_numeric($check) && is_numeric($blockid)) {
            $db->query("update Message2016 set Checked = '".($check==1 ? 1 : 0)."', LastIP = '".$_SERVER['REMOTE_ADDR']."', LastUser_ID = '{$AUTH_USER_ID}' where Message_ID = '$blockid' AND Catalogue_ID = '{$this->catID}'");

            return $reslt = json_encode(ARRAY(
                "title" => "ОК",
                "succes" => "Блок ".($check==1 ? "включен" : "выключен")
            ));
        }
    }


    # карта сайта
    public function siteTree($parnt='0') {
        if ($parnt==0) $r = "<div id='siteTree'>
                                <div id='siteTree-head'>
                                    <div class='st-line st-name ws'>{$this->curCat['Catalogue_Name']}</div>
                                    <div class='st-line st-num'>№ страницы</div>
                                    <div class='st-line st-type'>тип страницы</div>
                                </div>
                                <div id='siteTree-body'>";
        $r .= $this->getTreeLevel($parnt);
        if ($parnt==0) $r .= "</div></div>";
        return $r;
    }

    public function getTreeLevel($parent='0', $lvl=0) {
        global $db, $perm;

        $sitetreeArr = $db->get_results("select * from Subdivision where Catalogue_ID = '{$this->catID}' AND systemsub != 1 AND Parent_Sub_ID = '$parent' ORDER BY Priority", ARRAY_A);

        if ($sitetreeArr) {
            // id компонентов
            foreach ($sitetreeArr as $sub) $subid .= ($subid ? "," : "").$sub['Subdivision_ID'];
            $sub_class = $db->get_results("SELECT Class_ID, Subdivision_ID FROM Sub_Class WHERE Subdivision_ID IN ({$subid})", ARRAY_A);
            if($sub_class) foreach ($sub_class as $cc) $classid[$cc['Subdivision_ID']] = $cc['Class_ID'];
            // названия типов страниц
            $nameclass = $db->get_results("SELECT Class_ID, Class_Name FROM Class", ARRAY_A);
            foreach ($nameclass as $name) $classname[$name['Class_ID']] = $name['Class_Name'];
            // кнопка добавления раздела
            if ($parent==0) $sublist.= "<a class='add-subdivision' title='Добавления нового раздела' data-rel='lightcase' data-lc-options='{\"maxWidth\":624,\"groupClass\":\"modal-admin-order\"}' href='/bc/modules/bitcat/index.php?bc_action=addsub&subdiv=0' class='btn-admmenu-a btn-strt-a'><span>+</span> Новый раздел</a>";
            // разделы
            $sublist .= "<ol ".($parent>0 ? "" : "class='parent0 sortable'").">";

            foreach($sitetreeArr as $stree) {
                $id = $stree['Subdivision_ID'];

                if (!permission("catalogue") && $classid[$id]==2001) continue; // проскочить разделы магазина, если он скрыт
                $isChild = $db->get_var("select Subdivision_ID from Subdivision where Catalogue_ID = '{$this->catID}' AND Parent_Sub_ID = '$id' AND systemsub != 1 LIMIT 0,1");

                $class = array();
                $class[] = "mapitem";
                $class[] = $stree['Checked']==1 ? "checked" : "unchecked";
                $class[] = ($isChild ? "mjs-nestedSortable-branch ".(true ? "mjs-nestedSortable-collapsed" : "mjs-nestedSortable-expanded") : "mjs-nestedSortable-leaf");

                $sublist .= "<li class='".implode($class, " ")."' id='map-sub-{$id}' data-id='{$id}' data-priority='{$stree[Priority]}'>
                                <div class='link'>
                                    <span class='dropthree st-second'></span>
                                    <div class='st-line st-name'>
                                        <div class='st-a-link st-a-icon st-a{$classid[$id]}'>
                                            <a href='".($stree['ExternalURL'] ? $stree['ExternalURL'] : $stree['Hidden_URL'])."' target='_blank' class='ws'><span>{$stree['Subdivision_Name']}</span></a>
                                            <span class='st-open'></span>
                                        </div>
                                    </div>
                                    <div class='st-line st-num'>
                                        <div class='st-second'>
                                            <div class='st-check'></div>
                                            <a title='Изменение раздела №{$id}' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' href='/bc/modules/bitcat/index.php?bc_action=editsub&subdiv={$id}' class='st-add addsub'></a>
                                        </div>
                                        <div class='st-first ws'>{$id}</div>
                                    </div>
                                    <div class='st-line st-type'>
                                        <div class='st-second'>
                                            <a title='Добавления нового раздела' data-rel='lightcase' data-lc-options='{\"maxWidth\":624,\"groupClass\":\"modal-admin-order\"}'  href='/bc/modules/bitcat/index.php?bc_action=addsub&subdiv={$id}' class='add-btn editmes'>Подраздел</a>
                                            ".(!$stree[nodeletesub] ? "<a title='Удалить раздел <br>{$stree['Subdivision_Name']}?' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"showTitle\":false}' data-confirm-href='/bc/modules/bitcat/index.php?bc_action=subdelete&subdiv={$id}' href='#сonfirm-actions' class='st-remove dropsub'></a>" : NULL)."
                                        </div>
                                        <div class='st-first ws'>".($stree['ExternalURL'] ? "внешняя ссылка" : $classname[$classid[$id]])."</div>
                                    </div>
                                </div>
                                ".($isChild && !$lvl ? $this->getTreeLevel($stree[Subdivision_ID], 1): null)."
                            </li>";
            }

            $sublist .= "</ol>";
        }
        return $sublist;
    }


    # ДОБАВЛЕНИЕ / ИЗМЕНЕНИЕ РАЗДЕЛА: форма
    public function editSub($subdiv, $add='') {
        if (function_exists('bc_editSub')) {
            $form = bc_editSub($this, $subdiv, $add);
        }
        else {
            global $db, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $setting, $current_catalogue, $DOCUMENT_ROOT, $pathInc2, $nc_core, $AUTH_USER_ID;
            if (is_numeric($subdiv) && (($subdiv>0 && !$add) || $add)) {

                if (!$add) { // изменение
                    $subVar = $db->get_row("select a.*, b.Class_ID, b.SortBy from Subdivision as a, Sub_Class as b where
                    a.Subdivision_ID = '$subdiv' AND (a.Subdivision_ID = b.Subdivision_ID OR a.Hidden_URL='/index/') AND a.Catalogue_ID = '{$this->catID}' ORDER BY b.Priority LIMIT 0,1",ARRAY_A);
                    //$class_ID = $db->get_var("select Class_ID, SortBy from Sub_Class where Subdivision_ID = '$subdiv' ORDER BY Priority limit 0,1");
                    if ($subVar['Class_ID']) {
                        $class_Name = $subVar['Class_ID'] == 2073 ? $setting['message2073_name'] : $db->get_var("select Class_Name from Class where Class_ID = '".$subVar['Class_ID']."'");
                        $ccID = $db->get_row("select Sub_Class_ID, Class_Template_ID from Sub_Class where Subdivision_ID = '$subdiv' AND Catalogue_ID = '{$this->catID}' ORDER BY Priority limit 0,1", 'ARRAY_A');
                        $ctplarr = $db->get_results("select Class_ID, Class_Name from Class where ClassTemplate = '".$subVar['Class_ID']."' order by Class_Name", 'ARRAY_A');
                        $ctplHtml = "<div class='colline colline-2'>".bc_input("classname", "{$class_Name} | Инфоблок: №".$ccID['Sub_Class_ID'], "Тип страницы<!-{$subVar['Class_ID']}->", "disabled")."</div>";
                        $arrSubSett = orderArray($subVar['settingsSub']);
                    }

                    # Шаблоны карточек
                    $template = $db->get_var("SELECT data FROM Bitcat WHERE `key` = 'size{$subVar[Class_ID]}_template'");
                    if($template){
                        $template = array_merge(array('' => "Не выбрано"), json_decode($template, 1));

                        # модули шаблонов компонентов
                        if(!$current_catalogue) $current_catalogue = $nc_core->catalogue->get_by_id($this->catID);
                        if($current_catalogue['customCode']){
                            $templatePath = $DOCUMENT_ROOT.$pathInc2."/template/{$subVar[Class_ID]}/template/objects/";
                            if(is_dir($templatePath)){
                                if($handle = opendir($templatePath)){
                                    while(false !== ($file = readdir($handle))){
                                        if($file != '.' && $file != '..'){
                                            $pathFile = $templatePath.$file;
                                            if(is_file($pathFile)){
                                                $name = str_replace(".php", "", $file);
                                                $template = array_merge($template, array($name => $name));
                                            }
                                         }
                                    }
                                    closedir($handle);
                                }
                            }
                        }

                        $templateOption = getOptionsFromArray($template, $arrSubSett[template]);
                        $ctplHtml = "<div class='colline colline-2'>".bc_select("subSett[template]", $templateOption, "Шаблон карточек", "class='ns'")."</div>".$ctplHtml;
                    }else if ($ctplarr) { # Шаблоны карточек компонента
                        $ctpls .= "<option value=''>- по умолчанию -</option>";
                        foreach($ctplarr as $ctpl) {
                            $noClassID = array(2072);
                            if(!permission('dev')) $noClassID[] = 2076;
                            if(!in_array($ctpl[Class_ID], $noClassID)) $ctpls .= "<option ".($ctpl['Class_ID']==$ccID['Class_Template_ID'] ? "selected" : NULL)." value='{$ctpl[Class_ID]}'>{$ctpl[Class_ID]}. {$ctpl[Class_Name]}</option>";
                        }
                        $ctplHtml = "<div class='colline colline-2'>".bc_select("Class_Template", $ctpls, "Шаблон вывода", "class='ns'")."</div>".$ctplHtml;
                    }


                }
                else { // добавление
                    // 2030 - производители
                    $classIDs = array(182, 2003, 2009, 2010, 2012, 2020, 2021);
                    
                    if($setting['message2073_check']) $classIDs[] = 2073;

                    if(permission("catalogue")) $classIDs[] = 2001; // и каталог
                    if(permission("design")) $classIDs[] = 244; // и каталог
                    if(permission("korzilla_admin")) $classIDs[] = 2260;
                    
                    $classArr = $db->get_results("select Class_ID, Class_Name from Class where Class_ID IN (".implode(",", $classIDs).")",ARRAY_A);
                    foreach($classArr as $itm => $cl) {
                        $id = $cl['Class_ID'];
                        $name = $id==2073 ? $setting['message2073_name'] : $cl['Class_Name'];
                        $classHtml .= "<label class='slideid-{$id}'><input value='{$id}' name='Class_ID' type='radio'><div class='add-slide-icon add-si-{$id}'></div><div class='add-slide-name'>{$name}</div></label>";
                    }
                    $prior = $db->get_var("select Priority from Subdivision where Parent_Sub_ID = '$subdiv' AND Catalogue_ID = '{$this->catID}' ORDER BY Priority DESC limit 0,1");
                }

                // сортировка
                if ($subVar['Class_ID']==2001) {
                    $sortArr = array(
                        "Priority"=>"по приоритету",
                        "name"=>"по названию",
                        "price"=>"по цене",
                        "art"=>"по артикулу",
                        "stock"=>"по наличию",
                        "vendor"=>"по производителю"
                    );
                }
                if ($subVar['Class_ID']==2003) $sortArr = array("Priority"=>"по приоритету","date"=>"по указанной дате","Created"=>"по дате создания новости");
                if ($subVar['Class_ID']>0 && !$sortArr) $sortArr = array("Priority"=>"по приоритету","Created"=>"по дате");

                if (in_array($subVar['Class_ID'], array(2001, 2021, 2009))) $sortArr['name'] = "по названию";
                if ($sortArr) {
                    if ($subVar['Class_ID'] == 2001) {
                        $sort = "<option " . (!$subVar['SortBy'] ? "selected" : "") . " value='0'>Не выбрано</option>";
                    }
                    foreach($sortArr as $k => $n) {
                        $sort .= "<option " . ($k == $subVar['SortBy'] ? "selected" : "") . " value='{$k}'>{$n} (a-z)</option>";
                        $sort .= "<option ".($k.' DESC'==$subVar['SortBy'] ? "selected" : "")." value='$k DESC'>$n (z-a)</option>";
                    }
                }

                if($add){ // все ок, показать форму (добавление)
                    $prior = ($prior ? $prior+1 : 1);
                    $form = "
                    <form class='bc_form ajax2 addsubform' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=addsubsave' method='post'><div id='formcontent'>
                        <input type='hidden' name='sb_Parent_Sub_ID' value='{$subdiv}'>
                        <input type='hidden' name='reload' value='".($_GET[reload] ? 1 : 0)."'>
                        <div class='add-slide-1'>$classHtml</div>
                        <div class='add-slide-2'>
                            <div class='add-s2-body'>
                                <div class='add-s2-left'>
                                    <label class='add-s2-file'>
                                        <input type='file' name='fl_img'>
                                        <div class='add-s2-text'><span>Загрузить изображение раздела</span></div>
                                        <div class='add-preview-photo'><canvas id='add-preview-canvas'></div>
                                    </label>
                                </div>
                                <div class='add-s2-right'>
                                    <div class='add-s2-line'>".bc_checkbox("sb_Checked", 1, "Включен в меню сайта", 1)."</div>
                                    <div class='add-s2-line'>".bc_input("sb_Subdivision_Name", "", "НАЗВАНИЕ РАЗДЕЛА", "", 1)."</div>
                                    <div class='add-s2-line'>".bc_input("sb_EnglishName", "", "URL страницы (создается автоматически)", "size=50")."</div>
                                    <div class='none'>Приоритет в списке<input type='number' min=1 name='sb_Priority' value='".$prior."'></div>
                                </div>
                                <div class='result'></div>
                                <div class='add-s2-bottom'>
                                    <div class='add-s2-btn'><input type=submit value='Создать раздел'></span></div>
                                    <div class='add-s2-btn-white'><input type=submit onclick='setInputOpenSettings(this);' value='Создать и открыть настройки'></span></div>
                                </div>
                            </div>
                        </div>
                    </form>";

                }
                else if ($subVar) { // все ок, показать форму (редактирование)

                    $subdirList .= "<option value=''>- не показывать -</option>";
                    foreach($db->get_results("select * from Classificator_subdir where Checked = 1", ARRAY_A) as $field) {
                        $subdirList .= "<option ".($field['subdir_ID']==$subVar['subdir'] ? "selected" : NULL)." value='{$field['subdir_ID']}'>{$field['subdir_Name']}</option>";
                    }
                    $subdir = "<div class='colline colline-4'>".bc_select("sb_subdir", $subdirList, "Показ подразделов", "class='ns'")."</div>";

                    $subdirLvlList = "";
                    foreach($db->get_results("select * from Classificator_subdirLvl where Checked = 1 ORDER BY subdirLvl_Priority", ARRAY_A) as $field) {
                        $subdirLvlList .= "<option ".($field['subdirLvl_ID'] == $subVar['subdirLvl'] ? "selected" : NULL)." value='{$field['subdirLvl_ID']}'>{$field['subdirLvl_Name']}</option>";
                    }
                    $subdirLvl = "<div class='colline colline-4'>".bc_select("sb_subdirLvl", $subdirLvlList, "Уровень подразделов", "class='ns'")."</div>";

                    if ($subVar['img']) {
                        $tmpf = explode(":",$subVar[img]);
                        $img = $HTTP_FILES_PATH.$subdiv."/".$tmpf[0];
                    }
                    if ($subVar['icon']) {
                        $tmpi = explode(":",$subVar['icon']);
                        $icon = $HTTP_FILES_PATH.$subdiv."/".$tmpi[0];
                    }

                    /*if($subVar[Class_ID]==2001){
                        $setBl .= "<div class='colline colline-1'>".bc_checkbox("subSett[instock]", 1, "Показывать товары тоько в наличии", $arrSubSett[instock])."</div>";
                    }*/

                    if($subVar[Class_ID]==2003){
                        $setBl .= "<div class='colline colline-1'>".bc_checkbox("subSett[objInModal]", 1, "Открывать новость в модальном окне", $arrSubSett[objInModal])."</div>";
                    }

                    if($subVar[Class_ID]==2001 || $subVar[Class_ID]==2010 || $subVar[Class_ID]==2030 || $subVar[Class_ID]==244 || $subVar[Class_ID]==2021 || $subVar[Class_ID]==2003){
                        $size = getsizeclass(array(
                            "id" => $subVar[Class_ID],
                            "sizeitem_select" => ($arrSubSett["sizeitem_select"] ? $arrSubSett["sizeitem_select"] : $setting["size{$subVar[Class_ID]}_select"]),
                            "sizeitem" => ($arrSubSett["sizeitem"] ? $arrSubSett["sizeitem"] : $setting["size{$subVar[Class_ID]}"]),
                            "sizeitem_counts" => ($arrSubSett["sizeitem_counts"] ? $arrSubSett["sizeitem_counts"] : ""),
                            "sizeitem_margin" => (is_numeric($arrSubSett["sizeitem_margin"]) ? $arrSubSett["sizeitem_margin"] : $setting["size{$subVar[Class_ID]}_margin"]),
                            "sizeitem_image_select" => ($arrSubSett["sizeitem_image_select"] ? $arrSubSett["sizeitem_image_select"] : $setting["size{$subVar[Class_ID]}_image_select"]),
                            "sizeitem_image" => ($arrSubSett["sizeitem_image"] ? $arrSubSett["sizeitem_image"] : $setting["size{$subVar[Class_ID]}_image"]),
                            "sizeitem_fit" => ($arrSubSett["sizeitem_fit"] ? $arrSubSett["sizeitem_fit"] : $setting["size{$subVar[Class_ID]}_fit"])
                        ));
                        // Размеры карточек
                        $setBl .= "<div class='colheap'>";
                            $setBl .= "<h4>Размеров объектов</h4>";
                            $setBl .= "<div>";

                                $setBl .= "<div class='colline colline-1' data-jsopen='settMasonry'>".bc_checkbox("subSett[masonry]", 1, "Включить Masonry", $arrSubSett[masonry])."</div>";
                                $setBl .= "<div class='".(!$arrSubSett[masonry] ? "none" : "")."' data-jsopenthis='settMasonry'>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[type_masonry]", getOptionSelect("type_masonry", $arrSubSett[type_masonry]), "Порядок размеров", "class='ns'")."</div>";
                                $setBl .= "</div>";

                                $setBl .= "<div class='colline colline-1' data-jsopen='settCartSize'>".bc_checkbox("subSett[sizehave]", 1, "Индивидуальные отображение в разделе", $arrSubSett[sizehave])."</div>";
                                $setBl .= "<div class='".(!$arrSubSett[sizehave] ? "none" : "")."' data-jsopenthis='settCartSize'>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[sizeitem_select]", $size["sizeitem_select"], "Размер карточек", "class='ns'")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_input("subSett[sizeitem]", $size["sizeitem"]."px", "px")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_multi_line("subSett[sizeitem_counts]", $size["sizeitem_counts"], "", 2)."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_input("subSett[sizeitem_margin]", $size["sizeitem_margin"], "Отступ справа (px)")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[sizeitem_image_select]", $size["sizeitem_image_select"], "Пропорции изображения", "class='ns'")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_input("subSett[sizeitem_image]", $size["sizeitem_image"]."%", "%")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[sizeitem_fit]", $size["sizeitem_fit"], "Отображение", "class='ns'")."</div>";
                                $setBl .= "</div>";

                                $setBl .= "<div class='colline colline-1' data-jsopen='settAnimate'>".bc_checkbox("subSett[animate]", 1, "Анимация", $arrSubSett[animate])."</div>";
                                $setBl .= "<div class='".(!$arrSubSett[animate] ? "none" : "")."' data-jsopenthis='settAnimate'>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[animate_title]", getOptionSelect("animate_title", $arrSubSett[animate_title]), "Анимация заголовка", "class='ns'")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[animate_text]", getOptionSelect("animate_text", $arrSubSett[animate_text]), "Анимация описания", "class='ns'")."</div>";
                                    $setBl .= "<div class='colline colline-3'>".bc_select("subSett[animate_items]", getOptionSelect("animate_items", $arrSubSett[animate_items]), "Анимация объектов", "class='ns'")."</div>";
                                $setBl .= "</div>";

                            $setBl .= "</div>";
                        $setBl .= "</div>";
                    }

                    # Вывод товаров по параметрам
                    # [START]
                    $viewByParm = array(
                        'fields' => array(
                            '2001' => array('vendor','ves', 'capacity', 'sizes_item', 'height', 'width', 'length', 'depth', 'var2','var3','var4','var5','var6','var7','var8','var9','var10','var11','var12','var13','var14','var15')
                        ),
                        'html' => '',
                        'titles' => array(),
                        'checked' => orderArray($subVar['view_obj_by_param'])
                    );
                    if (isset($viewByParm['fields'][$subVar['Class_ID']])) {
                        $sql  = "SELECT `Field_Name`, `Description` FROM `Field`";
                        $sql .= " WHERE `Class_ID` = {$subVar['Class_ID']}";
                        $sql .= " AND `Field_Name` IN ('".implode("','", $viewByParm['fields'][$subVar['Class_ID']])."')";

                        $dbRows = $db->get_results($sql, ARRAY_A);

                        if (is_array($dbRows)) {
                            foreach ($dbRows as $dbRow) {
                                $viewByParm['titles'][$dbRow['Field_Name']] = $dbRow['Description'];
                            }
                        }

                        if (is_array($setting['lists_params']) && $subVar['Class_ID'] == 2001) {
                            foreach ($setting['lists_params'] as $params) {
                                $viewByParm['fields']['2001'][] = 'params_'.$params['keyword'];
                                $viewByParm['titles']['params_'.$params['keyword']] = $params['name'];
                            }
                        }

                        $viewByParm['html'] .= "<div class='colheap' data-jsopenmain='view-obj-by-param'>";
                        $viewByParm['html'] .= "<h4 data-jsopen='view-obj-by-param'>Вывод объектов по параметрам</h4>";

        
                        $viewByParm['html'] .= "<div class='view-obj-by-param none' data-jsopenthis='view-obj-by-param' data-class='{$subVar['Class_ID']}'>";
                        if (getIP('office')) {
                            $viewByParm['html'] .= "<div class='colline colline-1 colline-height custom-scroll-1 params-select'><ul>";
                            foreach ($viewByParm['checked'] as $fName => $paramsValues) {
                                if (isset($setting['lists_texts'][$fName]) && $setting['lists_texts'][$fName]['checked']) {
                                    $title = $setting['lists_texts'][$fName]['name'];
                                } else {
                                    $title = $viewByParm['titles'][$fName] ?? $fName;
                                }
                                $viewByParm['html'] .= "<li data-key='{$fName}'><b>{$title}</b><ul>";
                                foreach ($paramsValues as $paramValue) {
                                    $viewByParm['html'] .= "<li>{$paramValue}</li>";
                                }
                                $viewByParm['html'] .= "</ul></li>";
                            }

                            $viewByParm['html'] .= "</ul></div>";
                        }
                            $viewByParm['html'] .= "<div class='colline colline-2 colline-height custom-scroll-1'>";
                                foreach ($viewByParm['fields'][$subVar['Class_ID']] as $fName) {
                                    $viewByParm['html'] .= "<div class='colline colline-2 colline-height view-obj-by-param__field-wrapper'>";
                                            if (isset($setting['lists_texts'][$fName]) && $setting['lists_texts'][$fName]['checked']) {
                                                $title = $setting['lists_texts'][$fName]['name'];
                                            } else {
                                                $title = $viewByParm['titles'][$fName] ?? $fName;
                                            }
                                        $viewByParm['html'] .= "<span class='view-obj-by-param__param-title' data-name='{$fName}' data-classid='{$subVar['Class_ID']}' data-subid='{$subdiv}'>{$title}</span>";
                                    $viewByParm['html'] .= "</div>";
                                }
                            $viewByParm['html'] .= "</div>";
                            $viewByParm['html'] .= "<div class='colline colline-2 colline-height custom-scroll-1'>";
                                $viewByParm['html'] .= "<div class='view-obj-by-param__values'>";
                                    $viewByParm['html'] .= "<div><p>Выберите параметр</p></div>";
                                    foreach ($viewByParm['fields'][$subVar['Class_ID']] as $key) {
                                        $viewByParm['html'] .= "<div class='view-obj-by-param__values-block none' data-name='{$key}'></div>";
                                    }
                                    $viewByParm['html'] .= "</div>";
                                $viewByParm['html'] .= "</div>";
                            $viewByParm['html'] .= "</div>";
                        $viewByParm['html'] .= "</div>";
                    }
                    # [END]

                    # Тэги
                    # [START]
                    // $sql  = "SELECT `Subdivision_ID` AS id, `Subdivision_Name` AS name";
                    //     $sql .= ", LENGTH(`tags_links`) AS tags_links";
                    //     $sql .= ", LENGTH(`tags_links_bottom`) AS tags_links_bottom";
                    // $sql .= " FROM `Subdivision`";
                    // $sql .= " WHERE `Catalogue_ID` = {$this->catID}";
                    //     $sql .= " AND `Subdivision_ID` != {$subVar['Subdivision_ID']}";
                    //     $sql .= " AND (";
                    //         $sql .= " `tags_links` IS NOT NULL AND `tags_links` != ''";
                    //         $sql .= " OR `tags_links_bottom` IS NOT NULL AND `tags_links_bottom` != ''";
                    //     $sql .= ")";
                    // $sql .= " ORDER BY `Subdivision_Name`";

                    // $dbRows = $db->get_results($sql, ARRAY_A);
                    // $tagImportOptions = '';
                    // if (is_array($dbRows)) {
                    //     foreach ($dbRows as $row) {
                    //         if ($row['tags_links']) {
                    //             $title = htmlspecialchars("<b>{$row['name']}</b> <small>верхние тэги ({$row['id']})</small>");
                    //             $tagImportOptions .= "<option value='{$row['id']}_top' data-field='tags_links' data-name='{$title}'>{$title}</option>";
                    //         }
                    //         if ($row['tags_links_bottom']) {
                    //             $title = htmlspecialchars("<b>{$row['name']}</b> <small>нижние тэги ({$row['id']})</small>");
                    //             $tagImportOptions .= "<option value='{$row['id']}_bot' data-field='tags_links_bottom' data-name='{$title}'>{$title}</option>";
                    //         }
                    //     }
                    // }

                    // $tagsTop = $subVar['tags_links'] ? json_decode($subVar['tags_links'], true) : array();
                    // $tagsBottom = $subVar['tags_links_bottom'] ? json_decode($subVar['tags_links_bottom'], true) : array();

                    // $tagsHtml  = "<h4>Тэги сверху</h4>";
                    // $tagsHtml .= "<div class='colline colline-2'>";
                    //     $tagsHtml .= bc_checkbox("tags_use_child", 1, "Применить к дочерним разделам", 0);
                    // $tagsHtml .= "</div>";
                    // if ($tagImportOptions) {
                    //     $tagsHtml .= "<div class='colline colline-4'>";
                    //         $tagsHtml .= bc_select("tags_links_import", $tagImportOptions, "Импорт тэгов из другого раздела", "class='ns'");
                    //     $tagsHtml .= "</div>";
                    //     $tagsHtml .= "<div class='colline colline-4 tags_links_import-wrapper'>";
                    //         $tagsHtml .= "<button class='bc-btn-green' type='button' id='tags_links_import'>Выбрать</button>";
                    //     $tagsHtml .= "</div>";
                    // }
                    // $tagsHtml .= "<div class='colline colline-height'>";
                    //     $tagsHtml .= bc_multi_line('sb_tags_links', $tagsTop);
                    // $tagsHtml .= "</div>";

                    // $tagsHtml .= "<h4>Тэги Снизу</h4>";
                    // $tagsHtml .= "<div class='colline colline-2'>";
                    //     $tagsHtml .= bc_checkbox("tags_bot_use_child", 1, "Применить к дочерним разделам", 0);
                    // $tagsHtml .= "</div>";
                    // if ($tagImportOptions) {
                    //     $tagsHtml .= "<div class='colline colline-4'>";
                    //         $tagsHtml .= bc_select("tags_links_bottom_import", $tagImportOptions, "Импорт тэгов из другого раздела", "class='ns'");
                    //     $tagsHtml .= "</div>";
                    //     $tagsHtml .= "<div class='colline colline-4 tags_links_import-wrapper'>";
                    //         $tagsHtml .= "<button class='bc-btn-green' type='button' id='tags_links_bottom_import'>Выбрать</button>";
                    //     $tagsHtml .= "</div>";
                    // }
                    // $tagsHtml .= "<div class='colline colline-height'>";
                    //     $tagsHtml .= bc_multi_line('sb_tags_links_bottom', $tagsBottom);
                    // $tagsHtml .= "</div>";

                    // unset($tagImportOptions); unset($dbRows); unset($sql);
                    $tagsHtml = (function() use ($db, $subVar) {
                        $sql = "SELECT `Subdivision_ID` AS id, `Subdivision_Name` AS name,
                                    LENGTH(`tags_links`) AS tags_links,
                                    LENGTH(`tags_links_bottom`) AS tags_links_bottom
                                FROM `Subdivision`
                                WHERE `Catalogue_ID` = {$this->catID}
                                    AND `Subdivision_ID` != {$subVar['Subdivision_ID']}
                                    AND (`tags_links` IS NOT NULL AND `tags_links` != '' OR `tags_links_bottom` IS NOT NULL AND `tags_links_bottom` != '')
                                ORDER BY `Subdivision_Name`";
                    
                        $tagImportOptions = '';
                        foreach ($db->get_results($sql) ?: [] as $row) {
                            if ($row->tags_links) {
                                $title = htmlspecialchars("<b>{$row->name}</b> <small>верхние тэги ({$row->id})</small>");
                                $tagImportOptions .= "<option value='{$row->id}_top' data-field='tags_links' data-name='{$title}'>{$title}</option>";
                            }
                            if ($row->tags_links_bottom) {
                                $title = htmlspecialchars("<b>{$row->name}</b> <small>нижние тэги ({$row->id})</small>");
                                $tagImportOptions .= "<option value='{$row->id}_bot' data-field='tags_links_bottom' data-name='{$title}'>{$title}</option>";
                            }
                        }
                    
                        # Верхние тэги
                        $tagsTop = $subVar['tags_links'] ? json_decode($subVar['tags_links'], true) : [];
                    
                        $tagsHtml  = "<h4>Тэги сверху</h4>";
                        $tagsHtml .= "<div class='colline colline-2'>";
                            $tagsHtml .= bc_checkbox("tags_use_child", 1, "Применить к дочерним разделам", 0);
                        $tagsHtml .= "</div>";
                        if ($tagImportOptions) {
                            $tagsHtml .= "<div class='colline colline-4'>";
                                $tagsHtml .= bc_select("tags_links_import", $tagImportOptions, "Импорт тэгов из другого раздела", "class='ns'");
                            $tagsHtml .= "</div>";
                            $tagsHtml .= "<div class='colline colline-4 tags_links_import-wrapper'>";
                                $tagsHtml .= "<button class='bc-btn-green' type='button' id='tags_links_import'>Выбрать</button>";
                            $tagsHtml .= "</div>";
                        }
                        $tagsHtml .= "<div class='colline colline-height'>";
                            $tagsHtml .= bc_multi_line('sb_tags_links', $tagsTop);
                            $tagsHtml .= bc_textarea('tags_links_massive_area_top', '', 'Массовая загрузка тэгов', "class='massive-tag-area'");
                            $tagsHtml .= "<br/><button class='bc-btn-green' type='button' onclick='subTagFiller.init(\"sb_tags_links\", \"tags_links_massive_area_top\").fill();'>Добавить</button>";
                        $tagsHtml .= "</div>";
                    
                        $tagsBottom = $subVar['tags_links_bottom'] ? json_decode($subVar['tags_links_bottom'], true) : [];
                    
                        $tagsHtml .= "<h4>Тэги Снизу</h4>";
                        $tagsHtml .= "<div class='colline colline-2'>";
                            $tagsHtml .= bc_checkbox("tags_bot_use_child", 1, "Применить к дочерним разделам", 0);
                        $tagsHtml .= "</div>";
                        if ($tagImportOptions) {
                            $tagsHtml .= "<div class='colline colline-4'>";
                                $tagsHtml .= bc_select("tags_links_bottom_import", $tagImportOptions, "Импорт тэгов из другого раздела", "class='ns'");
                            $tagsHtml .= "</div>";
                            $tagsHtml .= "<div class='colline colline-4 tags_links_import-wrapper'>";
                                $tagsHtml .= "<button class='bc-btn-green' type='button' id='tags_links_bottom_import'>Выбрать</button>";
                            $tagsHtml .= "</div>";
                        }
                        $tagsHtml .= "<div class='colline colline-height'>";
                            $tagsHtml .= bc_multi_line('sb_tags_links_bottom', $tagsBottom);
                            $tagsHtml .= bc_textarea('tags_links_massive_area_bottom', '', 'Массовая загрузка тэгов', "class='massive-tag-area'");
                            $tagsHtml .= "<br/><button class='bc-btn-green' type='button' onclick='subTagFiller.init(\"sb_tags_links_bottom\", \"tags_links_massive_area_bottom\").fill();'>Добавить</button>";
                        $tagsHtml .= "</div>";
                        
                        return $tagsHtml;
                    })();
                    # [END]

                    if ($setting['fillterForNameOn']) {
                        $fillterForName = [];
                        $filterForNameDB = orderArray($subVar['filter_for_name'], 1);
                        ksort($filterForNameDB);
                        foreach ($filterForNameDB as $name => $value) {
                            $fillterForName[] = ['name' => $name] + $value;
                        }
                    }

                    $prior = ($prior ? $prior+1 : $subVar['Priority']);
                    $form = "<form class='bc_form ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=".(!$add ? "editsubsave" : "addsubsave")."' method='post'>
                                <input name='reload' value='{$_GET['reload']}' type='hidden'>
                                <ul class='tabs tabs-border'>
                                    <li class='tab'><a href='#tab_t1'>Основное</a></li>
                                    ".($setBl && permission('design') ? "<li class='tab'><a href='#tab_t6'>Содержимое</a></li>" : NULL)."
                                    <li class='tab'><a href='#tab_t5'>Фото</a></li>
                                    <li class='tab'><a href='#tab_t2'>Описание</a></li>
                                    <li class='tab'><a href='#tab_t3'>SEO</a></li>
                                    <li class='tab'><a href='#tab_t8'>Тэги</a></li>
                                    <li class='tab'><a href='#tab_t4'>Разное</a></li>
                                    <li class='tab'><a href='#tab_t7'>Доп.поля</a></li>
                                    <li class='tab'><a href='#tab_targeting'>Таргетинг</a></li>
                                    ".($setting['language'] ? "<li class='tab'><a href='#tab_lang'>Язык вывода</a></li>" : '')."
                                </ul>
                                <div class='modal-body tabs-body'>
                                    <div id='tab_t1'>
                                        <input type='hidden' name='oldenname' value='{$subVar['EnglishName']}'>
                                        <input type='hidden' name='sb_Parent_Sub_ID' value='{$subVar['Parent_Sub_ID']}'>
                                        <input type='hidden' name='subdiv' value='{$subdiv}'>
                                        <div class='colline colline-2'>".bc_input("sb_Subdivision_Name", $subVar['Subdivision_Name'], "Название раздела", "min=1", 1)."</div>
                                       
                                        <div class='colline colline-4'>".bc_checkbox("sb_Checked", 1, "Включен в меню", $subVar['Checked'])."</div>
                                        <div class='colline colline-4'>".bc_input("sb_Priority", $prior, "Приоритет в списке", "min=1")."</div>
                                        <div class='colline colline-2'>".bc_input("sb_AlterTitle", $subVar['AlterTitle'], "Альтернативное наименование внутри раздела", "size=50")."</div>
                                        {$ctplHtml}
                                        <div class='colline colline-2'>".bc_input("sb_EnglishName", $subVar['EnglishName'], "Ссылка (создается автоматически)", "size=50")."</div>".$subdir.$subdirLvl."
                                        ".($sort ? "<div class='colline colline-2'>".bc_select("sortby", $sort, "Сортировка товаров в разделе", "class='ns'")."</div>" : "")."
                                    </div>
                                    ".($setBl ? "<div class='none' id='tab_t6'>{$setBl}</div>" : null)."
                                    <div class='none' id='tab_t5'>
                                        <div class='colline colline-2'>".bc_file("fl_img", $subVar['img'], "Изображение раздела", $img, 'fl')."</div>
                                        <div class='colline colline-2'>".bc_file("fl_icon", $subVar['icon'], "Иконка раздела в меню", $icon, 'fl')."</div>
                                        <div class='colline colline-1'>".bc_checkbox("sb_imgtoall", 1, "Использовать изображение для всех товаров раздела", $subVar['imgtoall'])."</div>
                                    </div>

                                    <div class='none' id='tab_t2'>
                                        <div class='colline colline-height'>".bc_textarea("sb_descr", htmlspecialchars($subVar['descr']), "Краткое описание")."</div>
                                        <div class='colline colline-2'>".bc_checkbox("sb_txttoall", 1, "Использовать \"Описание сверху\"<br> во всех товарах раздела", $subVar['txttoall'])."</div>
                                        <div class='colline colline-2'>".bc_checkbox("subTextRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                        <div class='colline colline-height'>".bc_textarea("sb_text", htmlspecialchars($subVar['text']), "Описание в разделе (сверху)", "data-ckeditor='1'")."</div>
                                        <div class='colline colline-2'>".bc_checkbox("subText2Rekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                        <div class='colline colline-height'>".bc_textarea("sb_text2", htmlspecialchars($subVar['text2']), "Описание в разделе (снизу)", "data-ckeditor='1'")."</div>
                                    </div>

                                    <div class='none' id='tab_t3'>
                                        ".(permission('seo') ? "
                                            <div class='colheap' data-jsopenmain='seo-prefix-h1'>
                                                <h4 data-jsopen='seo-prefix-h1'>Общее наименование товаров в единственном числе<span></span></h4>
                                                <div data-jsopenthis='seo-prefix-h1' ".($subVar['PrefixProductH1'] ? '' : 'class="none"').">
                                                    ".(permission('seo_super') ? "
                                                        <div class='colline colline-6-4'>".bc_input("sb_PrefixProductH1", $subVar['PrefixProductH1'], "Префикс заголовка H1 товара", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("PrefixProductH1Rekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                    " : "")."
                                                </div>
                                            </div>
                                            <div class='colheap' data-jsopenmain='seo-title'>
                                                <h4 data-jsopen='seo-h1'>Альтернативный заголовок H1<span></span></h4>
                                                <div data-jsopenthis='seo-h1' ".($subVar['AlterTitleObj'] || $subVar['AlterTitle2'] ? '' : 'class="none"').">
                                                    ".(permission('seo_super') ? "
                                                        <div class='colline colline-6-4'>".bc_input("sb_AlterTitle2", $subVar['AlterTitle2'], "Шаблон заголовока h1", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("alterTitleRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-6-4'>".bc_input("sb_AlterTitleObj", $subVar['AlterTitleObj'], "Заголовок h1 для карточки объекта", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("alterTitleObjRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                    " : "")."
                                                </div>
                                            </div>

                                            <div class='colheap' data-jsopenmain='seo-title'>
                                                <h4 data-jsopen='seo-title'>Заголовок / Title <span></span></h4>
                                                <div data-jsopenthis='seo-title' ".($subVar['Title2'] || $subVar['Title'] || $subVar['TitleObj'] || $subVar['TitleImg'] ? '' : 'class="none"').">
                                                    <div class='colline colline-1'>".bc_input("sb_Title2", $subVar['Title2'], "Заголовок страницы", "size=50")."</div>
                                                    ".(permission('seo_super') ? "
                                                        <div class='colline colline-6-4'>".bc_input("sb_Title", $subVar['Title'], "Шаблон заголовока страницы", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("titleRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-6-4'>".bc_input("sb_TitleObj", $subVar['TitleObj'], "Заголовок карточки объекта", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("titleObjRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-6-4'>".bc_input("sb_TitleImg", $subVar['TitleImg'], "Альтернативный текст картинок", "size=50")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("titleImgRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                    " : "")."
                                                </div>
                                            </div>

                                            <div class='colheap' data-jsopenmain='seo-description'>
                                                <h4 data-jsopen='seo-description'>Описание страницы / Description</h4>
                                                <div data-jsopenthis='seo-description' ".($subVar['Description'] || $subVar['Description2'] || $subVar['DescriptionObj'] ? '' : 'class="none"').">
                                                    <div class='colline colline-height'>".bc_textarea("sb_Description", $subVar['Description'], "Описание страницы")."</div>
                                                    ".(permission('seo_super') ? "
                                                        <div class='colline colline-6-4 colline-height'>".bc_textarea("sb_Description2", $subVar['Description2'], "Шаблон описания страницы")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("descriptionRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-6-4 colline-height'>".bc_textarea("sb_DescriptionObj", $subVar['DescriptionObj'], "Описание карточки объекта")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("descriptionObjRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                    " : "")."
                                                </div>
                                            </div>

                                            <div class='colheap' data-jsopenmain='seo-keywords'>
                                                <h4 data-jsopen='seo-keywords'>Ключевые слова / Keywords</h4>
                                                <div data-jsopenthis='seo-keywords' ".($subVar['Keywords'] || $subVar['Keywords2'] || $subVar['KeywordsObj'] || $subVar['seotext'] ? '' : 'class="none"').">
                                                    <div class='colline colline-height'>".bc_textarea("sb_Keywords", $subVar['Keywords'], "Ключевые слова страницы")."</div>
                                                    ".(permission('seo_super') ? "
                                                        <div class='colline colline-6-4 colline-height'>".bc_textarea("sb_Keywords2", $subVar['Keywords2'], "Шаблон ключевых слов страницы")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("keywordsRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-6-4 colline-height'>".bc_textarea("sb_KeywordsObj", $subVar['KeywordsObj'], "Ключевые слова карточки объекта")."</div>
                                                        <div class='colline colline-6-2'>".bc_checkbox("keywordsObjRekurs", 1, "Изменить для раздела<br>и подразделов")."</div>
                                                        <div class='colline colline-height'>".bc_textarea("sb_seotext", $subVar['seotext'], "SEO текст в карточке товара")."</div>
                                                    " : "")."
                                                </div>
                                            </div>
                                            <div class='colheap' data-jsopenmain='seo-razdel-words'>
                                                <h4 data-jsopen='seo-razdel-words'>Описание страницы снизу</h4>
                                                <div data-jsopenthis='seo-razdel-words' ".($subVar['seoTextBottom'] ? '' : 'class="none"').">
                                                <div class='colline colline-height'>".bc_textarea("sb_seoTextBottom", $subVar['seoTextBottom'], "SEO текст снизу страницы")."</div>
                                                    <div class='colline colline-6-4 colline-height'>".bc_textarea("sb_seoTextBottom2", $subVar['seoTextBottom2'], "SEO текст снизу страницы для под. разделов")."</div>
                                                    <div class='colline colline-6-2'>".bc_checkbox("seorazdelRekurs", 1, "Изменить для подразделов")."</div>
                                                </div>
                                            </div>
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("sb_no_index_seo", 1, "Не индексировать страницу", $subVar['no_index_seo']) . "
                                            </div>
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("sb_no_follow_seo", 1, "Не индексировать<br>ссылки страницы", $subVar['no_follow_seo']) . "
                                            </div>
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("noIndexSeoRekurs", 1, "Изменить для раздела<br>и подразделов") . "
                                            </div>
                                            " . ($setting['metaTagRobotPagination'] ? "
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("sb_no_index_pagination_seo", 1, "Не индексировать<br>страницы пагинации", $subVar['no_index_pagination_seo']) . "
                                            </div>
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("sb_no_follow_pagination_seo", 1, "Не индексировать ссылки<br>на страницах пагинации", $subVar['no_follow_pagination_seo']) . "
                                            </div>
                                            <div class='colline colline-3'>
                                                " . bc_checkbox("noIndexSeoRekursPagination", 1, "Изменить для раздела<br>и подразделов") . "
                                            </div>" : '') . "
                                            <div class='colline colline-3'>
                                            " . bc_checkbox("sb_inSitemap", 1, "Выгрузить в Sitemap", $subVar['inSitemap']) . "
                                            </div>

                                            " 
                                            . ($subVar['Class_ID'] == 2003 ? "<div class='colline colline-3'>" . bc_checkbox("sb_type_article", 1, "Режим статьи", $subVar['type_article']) . "</div>" : null)  
                                            . ($subVar['Class_ID']==2003 || $subVar['Class_ID']==182 || $subVar['Class_ID']==2021 ? "<div class='colline colline-2'>".bc_checkbox("sb_rss_turbo_yandex", 1, "RSS для контентных турбо станиц", $subVar['rss_turbo_yandex'])."</div>" : null) . "

                                                " . ($setting['fillterForNameOn'] && $subVar['Class_ID']? "
                                                <div class='colline colline-2'>".bc_checkbox("sb_fillter_for_name_off", 1, "Выключить фильтр в разделе", $subVar['fillter_for_name_off'])."</div>
                                                <div class='colheap' data-jsopenmain='seo-fillter-for-name'>
                                                    <h4 data-jsopen='seo-fillter-for-name'>Фильтр по алфавиту<span></span></h4>
                                                    <div data-jsopenthis='seo-fillter-for-name' class='none'>
                                                        <div class='colline colline-height'>
                                                            " . bc_multi_line('sb_filter_for_name', $fillterForName) . "
                                                        </div>
                                                    </div>
                                                </div>" : null) . "
                                            
                                        " : "<div class='colheap'><h5>В целях безопасности настройки отключены. Для включения настроек обратитесь к разработчику.</h5></div>")."
                                    </div>

                                    <div class='none' id='tab_t4'>
                                        <div class='colline colline-3'>".bc_checkbox("sb_noleftcol", 1, "Скрывать левую колонку", $subVar['noleftcol'])."</div>
                                        <div class='colline colline-3'>".bc_checkbox("sb_filtermenu", 1, "Подразделы как фильтр", $subVar['filtermenu'])."</div>
                                        <div class='colline colline-3'>".bc_checkbox("sb_inMarket", 1, "Выгружать в Яндекс.Маркет", $subVar['inMarket'])."</div>
                                        <div class='colline colline-2'>".bc_input("sb_ExternalURL", $subVar['ExternalURL'], "Внешняя ссылка", "size=50")."</div>
                                        <div class='colline colline-2'>".bc_checkbox("sb_blank", 1, "Открывать ссылку в новом окне", $subVar['blank'])."</div>
                                        <div class='colline colline-2'>".bc_input("sb_code1C", $subVar['code1C'], "Код категории в 1C/Excel", "size=50")."</div>
                                        <div class='colline colline-2'>".bc_input("sb_sub_find", $subVar['sub_find'], "Из каких разделов выводить товар", "size=50")."</div>

                                        <div class='colline colline-2 colline-height'>".bc_textarea("sb_find", $subVar['find'], "Вывод товаров по фразе")."</div>
                                        <div class='colline colline-2 colline-height'>".bc_textarea("sb_strictFind", $subVar['strictFind'], "Строгий вывод товаров по фразе")."</div>
                                        <div class='colline colline-2'>".bc_checkbox("sb_ajaxload", 1, "Плавная подгрузка данных", $subVar['ajaxload'])."</div>
                                        <div class='colline colline-2'>".bc_checkbox("sb_outallitem", 1, "Вывод товаров из подразделов", $subVar['outallitem'])."</div>
                                        ".($viewByParm['html'] ?: null)."
                                    </div>
                                    <div class='none' id='tab_t7'>
                                        <div class='colline colline-2'>".bc_input("sb_var1", $subVar['var1'], getLangWord("sb_var1","Параметр 1"), "size=50")."</div>
                                        <div class='colline colline-2'>".bc_input("sb_var2", $subVar['var2'], getLangWord("sb_var2","Параметр 2"), "size=50")."</div>
                                        <div class='colline colline-2'>".bc_input("sb_var3", $subVar['var3'], getLangWord("sb_var3","Параметр 3"), "size=50")."</div>
                                        <div class='colline colline-2'>".bc_input("sb_var4", $subVar['var4'], getLangWord("sb_var4","Параметр 4"), "size=50")."</div>
                                    </div>
                                    <div class='none' id='tab_targeting'>
                                    " . nc_city_field($subVar['citytarget']) . "
                                    </div> 
                                    <div class='none' id='tab_t8'>
                                        {$tagsHtml}
                                    </div>
                                    ".($setting['language'] ? (
                                        "<div class='none' id='tab_lang'>
                                            ".nc_lang_field($subVar['sub_lang'])."
                                        </div>") 
                                    : '')."
                                </div>
                                <div class='bc_submitblock'>
                                    <div class='result'></div>
                                    <span class='btn-strt'><input type='submit' value='Сохранить изменения'></span>
                                    ".(
                                        in_array($subVar['Class_ID'], [182, 244, 2001, 2003, 2009, 2010, 2012, 2020, 2021]) 
                                        ? "<a 
                                                class='btn-strt-a' 
                                                title='Копирование раздела' 
                                                data-rel='lightcase' 
                                                data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' 
                                                href='/bc/modules/bitcat/index.php?bc_copy_action=getCopySubdivisionForm&sub_id={$subVar['Subdivision_ID']}'
                                                >
                                                    <span>Копировать раздел</span>
                                                </a>"
                                        : ''
                                    )."
                                    ".(!$add && !$subVar['nodeletesub'] ? "<a class='btn-strt-a' title='Удалить объект?'' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"showTitle\":false}' href='#сonfirm-actions' data-confirm-href='/bc/modules/bitcat/index.php?bc_action=subdelete&subdiv={$subdiv}'><span>Удалить раздел</span></a>" : NULL)."
                                </div>
                            </form>";
                }
            }
        }
        clearSeoCach($subdiv);

        return ($form ? $form : "Ошибка в форме");
    }

    # УДАЛЕНИЕ РАЗДЕЛА
    public function subDelete($subdiv) {
        global $db, $HTTP_FILES_PATH, $HTTP_ROOT_PATH;
        if (is_numeric($subdiv) && $subdiv>0 && $db->get_var("select Catalogue_ID from Subdivision where Subdivision_ID = '$subdiv'")==$this->catID) {
            $Parent_Sub_ID = $db->get_var("select Parent_Sub_ID from Subdivision where Subdivision_ID = '$subdiv'");
            $childs = $this->getChildSub($subdiv);
            if ($childs) {
                foreach(array_reverse(explode(",",$childs)) as $ch) {
                    $psub = trim($ch);
                    if ($psub>0) {
                        DeleteSystemTableFiles('Subdivision', $psub);
                        CascadeDeleteSubdivision($psub);
                    }
                }
            }
            DeleteSystemTableFiles('Subdivision', $subdiv);
            CascadeDeleteSubdivision($subdiv);

            // чистка кэша блоков
            clearCache('','', 1, $this->catID);

            $reslt = json_encode(ARRAY(
                    "title" => "ОК",
                    "succes" => "Раздел удален",
                    "reloadsubtree" => $Parent_Sub_ID,
                    "reloadtab" => 1,
                    "modal" => "close"
            ));
        } else {
            $reslt = json_encode(ARRAY(
                "title" => "Ошибка",
                "error" => "Раздел не удален"
            ));
        }
        return $reslt;
    }


    # СОЗДАНИЕ РАЗДЕЛА: сохранение
    public function addSubSave() {
        global $db, $HTTP_FILES_PATH, $HTTP_ROOT_PATH;

        foreach($_POST as $setkey => $set) { // массив полей
            $setkey = strip_tags($setkey);
            $set = (!stristr($setkey,"text") ? strip_tags($set) : $set);
            if (strstr($setkey,"sb_")) {
                $setkey = str_replace("sb_","",$setkey);
                $valArr[$setkey] = $set;
            }
            if ($setkey=='Class_ID') $classid = $set;
        }
        if (!$valArr['Parent_Sub_ID']) (int)$valArr['Parent_Sub_ID'] = "0";
        if ($valArr['Parent_Sub_ID']>=0) { // необязателен ID корневого раздела, если заливаешь в корень
            if (!$valArr['EnglishName'] && $valArr['Subdivision_Name']) { // ключевое слово, если нет
                $valArr['EnglishName'] = $this->encodestring($valArr['Subdivision_Name'],1);
            } else {
                $valArr['EnglishName'] = $this->encodestring($valArr['EnglishName'],1);
            }
            if ($valArr['Parent_Sub_ID']>0) $parrentURL = $db->get_var("select Hidden_URL from Subdivision where Subdivision_ID = '".$valArr['Parent_Sub_ID']."' AND Catalogue_ID = '".$this->catID."'"); // путь родителя
            $valArr['Hidden_URL'] = ($parrentURL ? $parrentURL : "/").$valArr['EnglishName']."/"; // путь до раздела
            $valArr['Catalogue_ID'] = $this->catID;
            $valArr['UseMultiSubClass'] = 1;

            foreach($valArr as $setkey => $set) { // составление запроса
                if ($set) {
                    $keys .= ($keys ? ", " : "")."{$setkey}";
                    $values .= ($values ? ", " : "")."'{$set}'";
                }
            }
            # вывод подразделов
            $subdir = ($classid==2001 || $classid==2010 ? 3 : 1);
            $keys .= ($keys ? ", " : "")."subdir";
            $values .= ($values ? ", " : "")."'{$subdir}'";

            if($db->get_var("select Subdivision_ID from Subdivision where Catalogue_ID = '{$this->catID}' AND Hidden_URL = '{$valArr['Hidden_URL']}'")){
                $errText = "Создаваемый URL занят";
            }

            if ($valArr['Catalogue_ID'] && $valArr['Hidden_URL'] && $valArr['EnglishName'] && $valArr['Subdivision_Name'] && $valArr['Parent_Sub_ID']>=0 && $classid>0) {
                $sql = "INSERT INTO Subdivision ($keys) VALUES ($values)";
            }else{
                if(!$valArr['Subdivision_Name']) $errText = "Введите название раздела";
            }

            if ($db->query($sql) && $sql) { // выполнения запроса на создание раздела
                $subid = $db->get_var("select Subdivision_ID from Subdivision where Hidden_URL = '".$valArr['Hidden_URL']."' AND Catalogue_ID = '".$valArr['Catalogue_ID']."'");


                if (isset($_FILES)) { # сохранение файлов раздела
                    $valuesFile = $this->imagesSub($_FILES, $subid);
                    if ($valuesFile) $db->query("UPDATE Subdivision set {$valuesFile} where Subdivision_ID = '{$subid}'");
                }
                $sql2 = "INSERT INTO Sub_Class (Subdivision_ID,Class_ID,Checked,Sub_Class_Name,EnglishName,Catalogue_ID) VALUES ('{$subid}','{$classid}',1,'{$valArr['Subdivision_Name']}','{$valArr['EnglishName']}',{$valArr['Catalogue_ID']})";

                if ($db->query($sql2) && $sql2) { // выполнения запроса на добавление инфоблока в раздел

                    // смена сортировки
                    if ($classid==244) $db->query("update Sub_Class set SortBy = 'Priority' where Subdivision_ID = '{$subid}' AND Catalogue_ID = '{$this->catID}'");
                    if ($classid==2003) $db->query("update Sub_Class set SortBy = 'Created DESC' where Subdivision_ID = '{$subid}' AND Catalogue_ID = '{$this->catID}'");

                    // чистка кэша блоков
                    clearCache('','', 1, $this->catID);

                    $this->savecss();

                    $reslt = json_encode(ARRAY(
                            "title" => "ОК",
                            "succes" => "Раздел создан",
                            "modal" => !$_POST[openSettings] ? "close" : '',
                            "reload" => $_POST[reload] ? 1 : 0,
                            "reloadtab" => 1,
                            "reloadsubtree" => $valArr['Parent_Sub_ID'],
                            "openModalEdit" => $_POST[openSettings] ? '/bc/modules/bitcat/index.php?bc_action=editsub&subdiv='.$subid : '',
                            "idsub" => $subid
                    ));
                } else {
                    $reslt = json_encode(ARRAY(
                            "title" => "Ошибка",
                            "error" => "Раздел создан. Ошибка при добавлении инфоблока."
                    ));
                }
            } else { // ошибка запроса
                $reslt = json_encode(ARRAY(
                        "title" => "Ошибка",
                        "error" => $errText ? $errText : "Ошибка при создании раздела 1"
                ));
            }
        }
        if (!$reslt) { // другая ошибка
            $reslt = json_encode(ARRAY(
                    "title" => "Ошибка",
                    "error" => "Ошибка при создании раздела 2"
            ));
        }
        return $reslt;
    }

    # ИЗМЕНЕНИЕ РАЗДЕЛА: сохранение
    public function editSubSave() {
        if (function_exists('bc_editSubSave')) {
            $reslt = bc_editSubSave($this);
        }
        else {
            global $db, $FILES_FOLDER, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $AUTH_USER_ID, $setting;
            $subdiv = (int)$_POST['subdiv'];
            $parentsub = (int)$_POST['sb_Parent_Sub_ID'];
            if (is_numeric($subdiv) && $subdiv>0 && $db->get_var("select Catalogue_ID from Subdivision where Subdivision_ID = '$subdiv'")==$this->catID) {
                foreach($_POST as $setkey => $set) { // составление запроса
                    if (strstr($setkey,"sb_")) {
                        $setkey = strip_tags(str_replace("sb_","",$setkey));
                        switch (true) {
                            case in_array($setkey, ['tags_links', 'tags_links_bottom']) :
                                $$setkey = [];
                                if (is_array($set)) {
                                    foreach ($set as $key => $tag) {
                                        if (!empty($tag['name'])) {
                                            array_push($$setkey, $tag);
                                        }
                                    }
                                }
                                $$setkey = !empty($$setkey) ? addslashes(json_encode($set)) : '';
                                $set = $$setkey;
                                break;
                            case $setkey == 'filter_for_name':
                              
                                if (is_array($set)) {
                                    $filterForNameValue = array_reduce($set, function ($carry, $item) {
                                        $name = $item['name'];
                                        if ($name) {
                                            unset($item['name']);
                                            $item['on'] = isset($item['on'] ) ? 1 : 0;
                                            if (!isset($item['text'])) $item['text'] = $name;
                                            $carry[$name] = $item;
                                        }
                                        return $carry;
                                    }, []);
                                }
                               
                                $set = addslashes(json_encode($filterForNameValue));
                                break;
                            case $setkey == 'view_obj_by_param':
                                $subParamChecked = $db->get_var("SELECT `view_obj_by_param` FROM `Subdivision` WHERE `Subdivision_ID` = {$subdiv}");
                                $subParamChecked = orderArray($subParamChecked) ?: [];
                                foreach ($set as $fieldName => $vals) {
                                    unset($vals['loaded']);
                                    if (count($vals)) {
                                        $subParamChecked[$fieldName] = $vals;
                                    } else {
                                        unset($subParamChecked[$fieldName]);
                                    }
                                }
                                $set = !empty($subParamChecked) ? json_encode(securityForm($subParamChecked)) : '';
                                
                                unset($subParamChecked, $fieldName, $vals);
                                break;
                            case is_array($set):
                                $set = json_encode(securityForm($set));
                                break;
                            default:
                                $set = (!stristr($setkey,"text") && !stristr($setkey,"descr") ? strip_tags($set) : $set);
                                break;
                        }
                        $values .= ($values ? ", " : "")."{$setkey} = '" . $db->escape($set) ."'";
                    }
                }
                
                if ($setting['language']) {
                    $lang = NULL;
                    if (!empty($_POST['f_lang']) && is_array($_POST['f_lang'])) {
                        $lang = json_encode(securityForm($_POST['f_lang']));
                    }
                    if ($lang !== NULL) {
                        $values .= ($values ? ", " : "")."`sub_lang` = '{$lang}'";
                    } else {
                        $values .= ($values ? ", " : "")."`sub_lang` = NULL";
                    }
                    unset($lang);
                }

                if(isset($_POST['subSett'])){
                    foreach($_POST['subSett'] as $setSubkey => $setSubval) { // составление запроса
                        if(isset($_POST['subSett'][$setSubkey."_select"])) $setSubval = preg_replace("/[^0-9]/", '', $setSubval);
                        $setSubkey = securityForm($setSubkey);
                        $setSubval = securityForm($setSubval);
                        $valuesSubset[$setSubkey] = $setSubval;
                    }
                }
                    foreach ($_POST['f_citytarget'] as $setTar) { // составление запроса
    
                        $valuet .= "{$setTar},";
                    }
                    $valuet = trim($valuet, ',');
                    $valuet = ",{$valuet},";
                    $values .= ($values ? ", " : "") . "citytarget = '{$valuet}'";

                $rSets = is_array($valuesSubset) ? json_encode($valuesSubset) : "";
                $db->query("UPDATE Subdivision set settingsSub = '{$rSets}'	where Catalogue_ID = '{$this->catID}' AND Subdivision_ID = '$subdiv'");
             
                echo "\n"; flush(); ob_flush();
                if (isset($_FILES)) { # сохранение файлов раздела
                    $valuesFile = $this->imagesSub($_FILES, $subdiv, ($_POST['sb_imgtoall'] ? 1 : NULL));
                    $values .= ($values && $valuesFile ? ", " : "").$valuesFile;
                }
                echo "\n"; flush(); ob_flush();

                # удаление файлов раздела
                # [START]
                $filesToRemove = [];
                $fileFields = '';
                if ($_POST['nofile']) {
                    foreach($_POST['nofile'] as $key => $field) {

                        if (!$_FILES['fl_'.$key] || $_FILES['fl_'.$key]['tmp_name'] == '') {
                            $values .= ($values ? ", " : "")."{$key} = ''";
                            $fileFields .= ($fileFields ? ',' : null)."`{$key}`";
                            if ($key == 'img') $fileFields .= ",`imgBig`";
                        }
                    }
                }
        
                if (!empty($fileFields)) {
                    $dbRow = $db->get_row("SELECT {$fileFields} FROM `Subdivision` WHERE `Subdivision_ID` = {$subdiv}", ARRAY_A);
                    if (is_array($dbRow)) {
                        foreach ($dbRow as $val) {
                            $valArr = explode(':', $val);
                            $filesToRemove[] = $FILES_FOLDER.$valArr[3];
                        }
                    }
                }
                # [END]

                $values .= ($values ? ", " : "")."LastModified = '".date("Y-m-d H:i:s")."'";

                if (!$_POST['sb_EnglishName'] && $_POST['sb_Subdivision_Name']) { // ключевое слово, если нет
                    $EnglishName = $this->encodestring($_POST['sb_Subdivision_Name'],1);
                }

                # очищать поля если они не были переданы
                $clearField = [];
                foreach ($clearField as $fName => $val) {
                    if (!isset($_POST['sb_'.$fName])) {
                        $values .= ($values ? ',' : null)."`{$fName}` = '" . str_replace("'", "\'", $val) ."'";
                    }
                }

                $sql = "update Subdivision set {$values}".
                (!$_POST['sb_Checked'] ? ", Checked = '0'" : NULL).
                (!$_POST['sb_noleftcol'] ? ", noleftcol = '0'" : NULL).
                (!$_POST['sb_blank'] ? ", blank = '0'" : NULL).
                (!$_POST['sb_inMarket'] ? ", inMarket = '0'" : NULL).
                (!$_POST['sb_var5'] ? ", var5 = '0'" : NULL).
                (!$_POST['sb_imgtoall'] ? ", imgtoall = '0'" : NULL).
                (!$_POST['sb_txttoall'] ? ", txttoall = '0'" : NULL).
                (!$_POST['sb_ajaxload'] ? ", ajaxload = '0'" : NULL).
                (!$_POST['sb_outallitem'] ? ", outallitem = '0'" : NULL).
                (!$_POST['sb_noPjax'] ? ", noPjax = '0'" : NULL).
                (!$_POST['sb_no_index_seo'] ? ", no_index_seo = '0'" : NULL).
                (!$_POST['sb_no_follow_seo'] ? ", no_follow_seo = '0'" : NULL).
                (!$_POST['sb_no_index_pagination_seo'] ? ", no_index_pagination_seo = '0'" : NULL).
                (!$_POST['sb_no_follow_pagination_seo'] ? ", no_follow_pagination_seo = '0'" : NULL).
                (!$_POST['sb_inSitemap'] ? ", inSitemap = '0'" : NULL).
                (!$_POST['sb_fillter_for_name_off'] ? ", fillter_for_name_off = '0'" : NULL).
                (!$_POST['sb_type_article'] ? ", type_article = '0'" : NULL) .
                (!$_POST['sb_rss_turbo_yandex'] ? ", rss_turbo_yandex = '0'" : NULL).
                (!$_POST['sb_filtermenu'] ? ", filtermenu = ''" : NULL).
                (!$_POST['sb_EnglishName'] ? ", EnglishName = '{$EnglishName}'" : NULL).
                " where Subdivision_ID = '$subdiv'  AND Catalogue_ID = '{$this->catID}'";

                if ($db->query($sql)) { // выполнения запроса

                    $oldenname = strip_tags($_POST['oldenname']);
                    $newenname = strip_tags(($_POST['sb_EnglishName'] ? $_POST['sb_EnglishName'] : $EnglishName));
                    if (md5($newenname)!=md5($oldenname)) { // если поменялось ключевое слово
                        $hiddenurls = $db->get_results("select Hidden_URL as url, Subdivision_ID as sub from Subdivision where Subdivision_ID IN (".$this->getChildSub($subdiv).$subdiv.") AND Catalogue_ID = '{$this->catID}'", ARRAY_A);
                        if ($hiddenurls) {
                            foreach($hiddenurls as $hid) {
                                unset($new_hidurl);
                                $new_hidurl = str_replace("/{$oldenname}/","/{$newenname}/",$hid['url']);
                                if ($new_hidurl) $db->query("update Subdivision set Hidden_URL = '{$new_hidurl}' where Subdivision_ID = '{$hid['sub']}' AND Catalogue_ID = '{$this->catID}'");
                            }
                        }
                        //$new_hiddenurl = str_replace("","",$hiddenurl);
                    }

                    // смена варианта вывода
                    $ctplID = (is_numeric($_POST['Class_Template']) ? strip_tags($_POST['Class_Template']) : '0');
                    $db->query("update Sub_Class set Class_Template_ID = '".strip_tags($ctplID)."' where Subdivision_ID = '{$subdiv}'  AND Catalogue_ID = '{$this->catID}'");

                    // список дочерних разделов
                    if ($_POST['titleRekurs']
                        || $_POST['titleObjRekurs']
                        || $_POST['titleImgRekurs']
                        || $_POST['descriptionRekurs']
                        || $_POST['descriptionObjRekurs']
                        || $_POST['keywordsRekurs']
                        || $_POST['keywordsObjRekurs']
                        || $_POST['alterTitleRekurs']
                        || $_POST['alterTitleObjRekurs']
                        || $_POST['PrefixProductH1Rekurs']
                        || $_POST['tags_use_child']
                        || $_POST['tags_bot_use_child']
                        || $_POST['noIndexSeoRekurs']
                        || $_POST['seorazdelRekurs']
                        || $_POST['noIndexSeoRekursPagination']
                        || $_POST['subTextRekurs']
                        || $_POST['subText2Rekurs']
                    ) {
                        $childSubs = $this->getChildSub($subdiv).$subdiv;
                    }

                    if ($_POST['subTextRekurs']==1) {
                        $db->query(
                            "UPDATE
                                Subdivision
                            SET
                                txttoall = '" . ($_POST['sb_txttoall'] ? 1 : 0) . "',
                                text = '".$_POST['sb_text']."'
                            WHERE 
                                Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    }

                    if ($_POST['subText2Rekurs']==1) $db->query("update Subdivision set text2 = '".$_POST['sb_text2']."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");

                    if ($_POST['noIndexSeoRekursPagination'] == 1) {
                        $db->query(
                            "UPDATE
                                Subdivision
                            SET
                                no_index_pagination_seo = '" . ($_POST['sb_no_index_pagination_seo'] ? 1 : 0 ) . "',
                                no_follow_pagination_seo = '" . ($_POST['sb_no_follow_pagination_seo'] ? 1 : 0 ) . "'
                            WHERE 
                                Subdivision_ID IN ({$childSubs}) 
                                AND Catalogue_ID = '{$this->catID}'"
                        );
                    }

                    // рекурсии для индексирования разделов
                    if ($_POST['noIndexSeoRekurs'] == 1) {
                        $db->query(
                            "UPDATE
                                Subdivision
                            SET
                                no_index_seo = '" . ($_POST['sb_no_index_seo'] ? 1 : 0 ) . "',
                                no_follow_seo = '" . ($_POST['sb_no_follow_seo'] ? 1 : 0 ) . "'
                            WHERE 
                                Subdivision_ID IN ({$childSubs}) 
                                AND Catalogue_ID = '{$this->catID}'"
                        );
                    }

                    // рекурсии для title
                    if ($_POST['titleRekurs']==1) $db->query("update Subdivision set Title = '".strip_tags($_POST['sb_Title'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['titleObjRekurs']==1) $db->query("update Subdivision set TitleObj = '".strip_tags($_POST['sb_TitleObj'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['titleImgRekurs']==1) $db->query("update Subdivision set TitleImg = '".strip_tags($_POST['sb_TitleImg'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");

                    // для тэгов
                    if ($_POST['tags_use_child'] == 1) $db->query("UPDATE Subdivision SET tags_links = '{$tags_links}' WHERE Subdivision_ID IN ({$childSubs})");
                    if ($_POST['tags_bot_use_child'] == 1) $db->query("UPDATE Subdivision SET tags_links_bottom = '{$tags_links_bottom}' WHERE Subdivision_ID IN ({$childSubs})");
                    //для description
                    if ($_POST['descriptionRekurs']==1) $db->query("update Subdivision set Description2 = '".strip_tags($_POST['sb_Description2'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['descriptionObjRekurs']==1) $db->query("update Subdivision set DescriptionObj = '".strip_tags($_POST['sb_DescriptionObj'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");

                    //для keywords
                    if ($_POST['keywordsRekurs']==1) $db->query("update Subdivision set Keywords2 = '".strip_tags($_POST['sb_Keywords2'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['keywordsObjRekurs']==1) $db->query("update Subdivision set KeywordsObj = '".strip_tags($_POST['sb_KeywordsObj'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");

                    //для seoTextBottom
                    if ($_POST['seorazdelRekurs']==1) $db->query("UPDATE Subdivision SET seoTextBottom2 = '{$_POST['sb_seoTextBottom2']}' WHERE Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");


                    // для h1
                    if ($_POST['alterTitleRekurs']==1) $db->query("update Subdivision set AlterTitle2 = '".strip_tags($_POST['sb_AlterTitle2'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['alterTitleObjRekurs']==1) $db->query("update Subdivision set AlterTitleObj = '".strip_tags($_POST['sb_AlterTitleObj'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");
                    if ($_POST['PrefixProductH1Rekurs']==1) $db->query("update Subdivision set PrefixProductH1 = '".strip_tags($_POST['sb_PrefixProductH1'])."' where Subdivision_ID IN (".$childSubs.") AND Catalogue_ID = '{$this->catID}'");

                    // смена сортировки
                    if (isset($_POST['sortby'])) $db->query("update Sub_Class set SortBy = '".strip_tags($_POST['sortby'])."' where Subdivision_ID = '{$subdiv}'  AND Catalogue_ID = '{$this->catID}'");

                    // чистка кэша блоков
                    clearCache('','', 1, $this->catID);

                    $this->savecss();

                    if(md5($newenname)!=md5($oldenname)) $newUrl = $db->get_var("select Hidden_URL from Subdivision where Subdivision_ID = '$subdiv'");

                    // ответ
                    $reslt = json_encode(ARRAY(
                                "title" => "ОК",
                                "succes" => "Сохранено",
                                "reloadsubtree" => $parentsub,
                                "modal" => "close",
                                "reloadtab" => $_POST['reload'] ? 0 : 1,
                                "reload" => $_POST['reload'] ? (isset($newUrl) ? 0 : 1) : 0,
                                "redirect" => $_POST['reload'] ? (isset($newUrl) ? $newUrl : "") : ""
                    ));
                    # удаление заменяемых файлов из радела
                    $removePath = $FILES_FOLDER.$subdiv.'/to_remove';
                    if (is_dir($removePath)) {
                        foreach (scandir($removePath) as $fileName) {
                            if (in_array($fileName, ['.', '..']) || is_dir($removePath.'/'.$fileName)) continue;
                            @unlink($removePath.'/'.$fileName);
                        }
                        rmdir($removePath);
                    }
                    # удаление файлов из разделам
                    foreach ($filesToRemove as $fileName) {
                        @unlink($fileName);
                    }
                } else { // ошибка запроса

                    $reslt = json_encode(ARRAY(
                                "title" => "Ошибка",
                                "error" => "Нет изменений",
                                // "error" => "Нет изменений ".$sql
                    ));
                    # если не удалось обновить, то удалить новые файлы и вернуть файлы из to_remove
                    // ! Какой obj это переменой нет
                    if (isset($obj->newSubFile) && is_array($obj->newSubFile)) {
                        foreach ($obj->newSubFile as $filePath) @unlink($filePath);
                    }
                    $removePath = $FILES_FOLDER.$subdiv.'/to_remove';
                    if (is_dir($removePath)) {
                        foreach (scandir($removePath) as $fileName) {
                            if (in_array($fileName, ['.', '..']) || is_dir($removePath.'/'.$fileName)) continue;
                            @rename(
                                $removePath.'/'.$fileName,
                                str_replace('/to_remove', '', $removePath.'/'.$fileName)
                            );
                        }
                        rmdir($removePath);
                    }
                }
            }
        }
        if (!$reslt) { // другая ошибка
            $reslt = json_encode(ARRAY(
                            "title" => "Ошибка",
                            "error" => "Ошибка"
            ));
        }
        return $reslt;
    }

    # РАЗДЕЛ: добавление изображений раздела
    public function imagesSub($files, $subdiv, $createBig='', $sizeX=300, $quality=100) {
        if (function_exists('bc_imagesSub')) {
            $setFiels = bc_imagesSub($this, $files, $subdiv, $createBig, $sizeX, $quality);
        }
        else {
            global $setting, $db, $AUTH_USER_ID;
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            require_once('class.upload.php');
            global $FILES_FOLDER;


            # перемещаем изображения раздела, которые дожны обновиться, в папку "to_delete"
            # [START]
                $fileFields = '';
                $deleteFolder = 'to_remove';
                $toDeletedFiels = [];
                # в переданных файлах ищем какие поля используются
                foreach($files as $key => $unused) {
                    if (strpos($key, 'fl_') === 0) {
                        if ($unused['error'] > 0) continue; 
                        $key = str_replace("fl_", "", $key);
                        $fileFields .= ($fileFields ? ',' : null)."`{$key}`";
                        if ($key == 'img') $fileFields .= ",`imgBig`";
                    }
                }

                if (!empty($fileFields)) {
                    $dbRow = $db->get_row("SELECT {$fileFields} FROM `Subdivision` WHERE `Subdivision_ID` = {$subdiv}", ARRAY_A);
 
                    if (is_array($dbRow)) {
                        # перемещаем файлы в папку "to_delete", если удалось, запоминаем для дальнейшего использования
                        foreach ($dbRow as $field => $val) {
                            if (!empty($val)) {
                                $valArr = explode(':', $val);
                                $fileInfo = pathinfo($FILES_FOLDER.$valArr[3]);

                                if (!file_exists($fileInfo['dirname'].'/'.$deleteFolder)) {
                                    mkdir($fileInfo['dirname'].'/'.$deleteFolder);
                                }

                                if (
                                    @rename(
                                        $fileInfo['dirname'].'/'.$fileInfo['basename'],
                                        $fileInfo['dirname'].'/'.$deleteFolder.'/'.$fileInfo['basename']
                                    )
                                ) {
                                    $toDeletedFiels[$field] = $fileInfo;
                                }
                            }
                        }
                    }
                }
            # [END]

            $setFiels = '';
            foreach($files as $filekey => $file) {
                if (strpos($filekey, 'fl_') === 0) {
                    if ($file['error'] > 0) continue; 
                    $handle = new upload($file, "RU");
                    if (!$handle->uploaded) continue;

                    $filekeyName = str_replace("fl_", "", $filekey);
                    if (getIP('office')) {
                        $handle->no_upload_check = true;
                    }
                    switch (true) {
                        # основное фото раздела
                        case strpos($filekey, 'img') !== false:
                            if ($setting['sizesub_imagepx'] > 0 && $setting['sizesub_imagepx'] < 1000) {
                                $sizeX = $setting['sizesub_imagepx'];
                            }
                            if ($handle->image_src_x > $sizeX) {
                                $handle->image_resize = true;
                                $handle->image_x = $sizeX;
                                $handle->image_ratio_y = true;
                            }
                            if ($createBig) {
                                $handle2 = new upload($file, "RU");
                                $handle2->file_new_name_body .= $handle2->file_src_name_body.'_big';
                                $handle2->file_auto_rename = false;
                                $handle2->file_overwrite = true;
                                $handle2->image_resize = true;
                                $handle2->image_x = 800;
                                $handle2->image_ratio_y = true;
                                $handle->allowed = array('image/*');
                                $handle2->process($FILES_FOLDER.$subdiv."/");


                                if ($handle->processed) {
                                    if (!empty($setFiels)) $setFiels .= ',';
                                    $setFiels .= "`imgBig` = ";
                                    $setFiels .= "'{$handle2->file_dst_name}:{$handle2->file_src_mime}";
                                    $setFiels .= ":{$handle2->file_src_size}:{$subdiv}/{$handle2->file_dst_name}'";
                                    $obj->newSubFile[] = $handle2->file_dst_pathname;
                                }
                                # если не удалось загрузить файл, вернуть старый на место
                                elseif (isset($toDeletedFiels['imgBig'])) {
                                    @rename(
                                        $toDeletedFiels['imgBig']['dirname'].'/'.$deleteFolder.'/'.$toDeletedFiels['imgBig']['basename'],
                                        $toDeletedFiels['imgBig']['dirname'].'/'.$toDeletedFiels['imgBig']['basename']
                                    );
                                    unset($toDeletedFiels['imgBig']);
                                }
                            }
                            break;
                        # иконка раздела
                        case strpos($filekey, 'icon') !== false:
                            $sizeX_icon = 22;
                            if ($setting['sizesub_imageicon'] > 0 && $setting['sizesub_imageicon'] < 1000) {
                                $sizeX_icon = $setting['sizesub_imageicon'];
                            }
                            if ($handle->image_src_x > $sizeX_icon) {
                                $handle->image_resize = true;
                                $handle->image_x = $sizeX_icon;
                                $handle->image_ratio_y = true;
                            }
                            $handle->file_new_name_body .= $handle->file_src_name_body.'_icon';
                            break;
                    }
                    $handle->allowed = array('image/*');
                    $handle->process($FILES_FOLDER.$subdiv."/");

             
                    if ($handle->processed) {
                        $handle->clean();
                        if (!empty($setFiels)) $setFiels .= ',';
                        $setFiels .= "`{$filekeyName}` = ";
                        $setFiels .= "'{$handle->file_dst_name}:{$handle->file_src_mime}";
                        $setFiels .= ":{$handle->file_src_size}:{$subdiv}/{$handle->file_dst_name}'";
                        $obj->newSubFile[] = $handle->file_dst_pathname;
                    }
                    # если не удалось загруить файл вернуть старый на место
                    elseif (isset($toDeletedFiels[$filekeyName])) {
                        @rename(
                            $toDeletedFiels[$filekeyName]['dirname'].'/'.$deleteFolder.'/'.$toDeletedFiels[$filekeyName]['basename'],
                            $toDeletedFiels[$filekeyName]['dirname'].'/'.$toDeletedFiels[$filekeyName]['basename']
                        );
                        unset($toDeletedFiels[$filekeyName]);
                    }
                }

                echo "\n"; flush(); ob_flush();
            }
        }
        return $setFiels;
    }

    # РАЗДЕЛ: список дочерних разделов для изменение ключевого слова.
    public function getChildSub($subdiv) {
        global $db;
        $subArr = $db->get_results("select Subdivision_ID as sub from Subdivision where Parent_Sub_ID = '$subdiv' AND Catalogue_ID = '{$this->catID}'", ARRAY_A);
        if ($subArr) {
            foreach($subArr as $sd) {
                $sddiv = $sd[sub];
                $reslt .= "{$sddiv},".$this->getChildSub($sddiv);
            }
        }
        return $reslt;
    }

    # список редиректов
    public function redirectList() {
        global $db, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $nc_core;
        $redurectArr = $db->get_results("select * FROM Redirect WHERE OldURL LIKE '".$this->curCat['Domain']."%' ORDER BY Redirect_ID", ARRAY_A);
        if ($redurectArr) {
            foreach($redurectArr as $key => $r) {
                $redirectLi .= "<div class='v-line'>
                                    <div class='v-inline v-line-num'>".($key+1).".</div>
                                    <div class='v-inline v-line-oldurl ws'>{$r['OldURL']}</div>
                                    <div class='v-inline v-line-newurl ws'>{$r['NewURL']}</div>
                                    <div class='v-inline v-line-header'>{$r['Header']}</div>
                                    <div class='v-line-sett'>
                                            <a href='/bc/modules/bitcat/index.php?bc_action=editredirect&rid=".$r['Redirect_ID']."' class='v-setting' title='".LNG_edit_redirect."' data-rel='lightcase' data-lc-options='{\"maxWidth\":590,\"groupClass\":\"modal-nopaddding modal-edit-filter\"}'></a>
                                            <a title='".LNG_delete_redirect."' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"showTitle\":false}' data-confirm-success='{\"succes\": 1,\"modal\":\"close\",\"reloadtab\":1}' data-confirm-href='/bc/modules/bitcat/index.php?bc_action=removeRedirect&id={$r['Redirect_ID']}' href='#сonfirm-actions' class='v-remove'></a>
                                    </div>
                                </div>";
            }
        }else{
            $redirectLi = "<div class='v-line center nobefore'><div class='v-inline'>".LNG_no_redirects."</div></div>";
        }
        return "<div class='v-redirect'>
                    <div class='v-title'>
                        ".($nc_core->catalogue->get_by_id($this->catID,'redirects')==1 ? "<a href='/bc/modules/bitcat/index.php?bc_action=addredirect' class='add-btn' title='".LNG_add_redirect."' data-rel='lightcase' data-lc-options='{\"maxWidth\":590,\"groupClass\":\"modal-nopaddding modal-edit-redirect\"}'>".LNG_add_redirect."</a>" : "<p>".LNG_redirects_off.".</p>")."
                        <div class='v-title-line'>

                            <div class='v-line-num'>№</div>
                            <div class='v-line-oldurl'>".LNG_redirect_from."</div>
                            <div class='v-line-newurl'>".LNG_redirect_to."</div>
                            <div class='v-line-header'>".LNG_redirect_status."</div>
                            <div class='v-line-sett'></div>
                        </div>
                    </div>
                    <div class='v-body'>{$redirectLi}</div>
                </div>";
    }

    # форма добавления/изменения редиректов
    public function addRedirect($rid='') {
        global $db;
        if (is_numeric($rid)) { // изменение редиректа
            $isEdit = 1;
            $redurect = $db->get_row("select * FROM Redirect WHERE Redirect_ID = '".(int)$rid."'", ARRAY_A);
        }
        $form = "<form id='adminForm' class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=addredirectsave' method='post'>
                    <div class='modal-body'>
                        ".($isEdit ? "<input type='hidden' name='sb_Redirect_ID' value='".(int)$rid."'>" : NULL)."
                        <div class='colline colline-1'>".bc_input("sb_OldURL", $redurect['OldURL'], "Пересылать с", "", 1)."</div>
                        <div class='colline colline-1'>".bc_input("sb_NewURL", $redurect['NewURL'], "Пересылать на", "", 1)."</div>
                        <div class='colline colline-1'>".bc_select("sb_Header", $this->arrSelect(array("301"=>"301 - постоянная пересылка","302"=>"302 - временная пересылка"),$rid,1), "Тип", "class='ns'")."</div>
                    </div>
                    <div class='bc_submitblock'>
                        <div class='result respadding'>".NETCAT_MODERATION_INFO_REQFIELDS."</div>
                        <span class='btn-strt'><input type='submit' value='".($isEdit ? "Сохранить изменения" : "Добавить")."'></span>
                    </div>
                </form>";
        return $form;
    }


    # сохранение редиректов
    public function addRedirectSave() {
        global $db;
        foreach($_POST as $setkey => $set) { // составление запроса
            if (strstr($setkey,"sb_")) {
                $setkey = strip_tags(str_replace("sb_","",$setkey));
                $set = (!stristr($setkey,"text") ? strip_tags($set) : $set);
                $set = str_replace("http://","",$set);
                $set = str_replace("https://","",$set);
                $values .= ($values ? ", " : "")."{$setkey} = '{$set}'";
            }
        }

        $thisDomain = str_replace("http://","",$this->curCat['Domain']);
        $thisDomain = str_replace("https://","",$thisDomain);
        $domainLeng = mb_strlen($thisDomain);


        if (mb_substr(str_replace("http://","",str_replace("https://","",$_POST['sb_OldURL'])), 0, $domainLeng)==$thisDomain) { // проверка на соответствие домена
            if (is_numeric($_POST['sb_Redirect_ID'])) {
                $rid = $_POST['sb_Redirect_ID'];
                $sql = "UPDATE Redirect set $values where Redirect_ID = '".(int)$rid."'";
            } else {
                $sql = "INSERT INTO Redirect SET $values";
            }

            if ($db->query($sql)) {
                $reslt = json_encode(ARRAY(
                    "title" => "ОК",
                    "succes" => "Сохранено",
                    "reloadtab" => "1",
                    "modal" => "close"
                ));
            } else {
                $reslt = json_encode(ARRAY(
                    "title" => "Ошибка",
                    "succes" => "Ошибка выполнения запроса"
                ));
            }
        } else {
            $reslt = json_encode(ARRAY(
                "title" => "Ошибка",
                "error" => "«Пересылать с» должен начинаться с текущего доменного имени (без http)"
            ));
        }
        return $reslt;
    }

    # удаление редиректов
    public function removeRedirect($id) {
        global $db;
        $id = intval($id);
        $newurl = $db->get_var("SELECT NewURL FROM Redirect where Redirect_ID = '{$id}'");


        $thisDomain = str_replace("http://","",$this->curCat['Domain']);
        $thisDomain = str_replace("https://","",$thisDomain);


        if (mb_stristr($newurl, $thisDomain)) {

            if ($db->query("DELETE FROM Redirect WHERE Redirect_ID = '{$id}'")) {
                $reslt = json_encode(ARRAY(
                    "title" => "ОК",
                    "succes" => "Редирект удален",
                    "reloadtab" => "1",
                    "modal" => "close"
                ));
            } else {
                $reslt = json_encode(ARRAY(
                    "title" => "Ошибка",
                    "succes" => "Ошибка выполнения запроса"
                ));
            }
        } else {
            $reslt = json_encode(ARRAY(
                "title" => "Ошибка",
                "error" => "Ошибка выполнения запроса"
            ));
        }
        return $reslt;
    }

    # массив в список
    public function arrSelect($arr,$sel='',$req='') {
        foreach($arr as $k => $a) {
            $option.= "<option value='$k' ".($k==$sel && $sel ? "selected" : "").">$a</option>";
        }
        if (!$req) $option = "<option value=''>- не выбрано -</option>".$option;
        return $option;
    }


    # форма настроек
    public function formSet() {
        global $db, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $DOCUMENT_ROOT, $pathInc, $pathInc2, $current_user, $setting;

        $paramPack = isset($_GET['parampack']) && $_GET['parampack'] != '' ? explode(',', $_GET['parampack']) : NULL ;

        if($paramPack){
            foreach ($paramPack as $val) {
                $names = array(
                    '1' => 'Robokassa',
                    '2' => 'Общие настройки магазина',
                    '3' => 'Почта для уведомлений',
                    '4' => 'Основные настройки',
                    '5' => 'SEO CSS Скрипты',
                    '6' => 'Города таргетинга',
                    '7' => 'Надписи на сайте',
                    '8' => 'Способы доставки',
                    '9' => 'Способы оплаты',
                    '10' => 'Время обратного звонка',
                    '11' => 'Карточки товаров',
                    '12' => 'Полное отображение товара',
                    '13' => 'Внутренняя страница',
                    '14' => 'Таргетинг',
                    '15' => 'Альфа-Банк',
                    '16' => 'Сбербанк',
                    '17' => 'Яндекс.Касса',
                    '18' => 'Доп. настройки',
                    '19' => 'ПАРТКОМ',
                    '20' => 'Лейблы товара',
                    '21' => 'Таблица товаров',
                    '22' => 'Расширенные настройки',
                    '23' => 'Данные разработчика',
                    '24' => 'Деловые линии',
                    '25' => 'Цены / Скидки',
                    '26' => 'Acat.Online',
                    '27' => 'Корзина',
                    '28' => 'Поиск',
                    '29' => 'Оформить заказ',
                    '30' => 'Компонент',
                    '31' => 'Отображение',
                    '32' => 'Форма',
                    '33' => 'Мультиязычность',
                    '35' => 'Разделы',
                    '36' => 'Блоки',
                    '37' => 'Ключевые слова',
                    '38' => 'Frontpad',
                    '39' => 'Best2Pay',
                    '40' => 'Параметры товара',
                    '41' => 'TRADESOFT',
                    '42' => 'СДЭК',
                    '43' => 'Тинькофф',
                    '44' => 'Armtek',
                    '45' => 'Omega',
                    '46' => 'Выкючение сайта',
                    '47' => 'Расчет доставки eDost',
                    '48' => 'PayAnyWay',
                    '49' => 'Битрикс24',
                    '50' => 'IIKO',
                    '51' => 'Директ Кредит',
                    '52' => 'Uniteller',
                    '53' => 'Авангард',
                    '54' => 'СБИС',
                    '55' => 'Напишите нам',
                    '56' => 'Основные настройки',
                    '57' => 'Push',
                    '58' => 'AmoCRM',
                    '59' => 'SMTP',
                    '60' => 'Бот рассыльщик заказов',
                    '61' => 'Planfix',
                    '62' => 'Статусы заказа',
                    '63' => 'Письма изменения статусов',
                    '65' => 'Виджет почты России',
                );
                if($names[$val]){
                    $paramPackSql[$val]['name'] = $names[$val];
                    $paramPackSql[$val]['key'] = paramArr($val);
                }
            }
        }

        if ($paramPackSql) {
            $paramPackSqlAll = '';
            foreach ($paramPackSql as $key => $value) {
                if (empty($value['key'])) continue;
                $paramPackSqlAll .= $paramPackSqlAll ? ',' : '';
                $paramPackSqlAll .= implode(',',$value['key']);
            }
        }

        // take settings from BD
        $settingsBD = $db->get_results("select SQL_NO_CACHE * from Bitcat ".($paramPackSqlAll ? "WHERE `key` IN ({$paramPackSqlAll})" : null)." order by Priority", ARRAY_A);

        if ($settingsBD) {
            // take settings from File
            $settingFile = getSettings();

            foreach($settingsBD as $settingDB) {
                if($settingDB['key'] == 'lists_texts'){
                    $iarr=0;
                    $tmsetting = $settingFile[$settingDB['key']] ? $settingFile[$settingDB['key']] : orderArray($settingDB['value']);
                    foreach ($tmsetting as $key4 => $value) {
                        if($key4 && $value['name']){
                            $settingdecode[$iarr]['keyword'] = $key4;
                            $settingdecode[$iarr]['name'] = $value['name'];
                            $settingdecode[$iarr]['checked'] = $value['checked'];
                        }
                        $iarr++;
                    }
                    $settings[$settingDB['key']]['value'] = $settingdecode;
                }else{
                    $settings[$settingDB['key']]['value'] =  isset($settingFile[$settingDB['key']]) ? $settingFile[$settingDB['key']] : ($settingDB['type']==11 ? orderArray($settingDB['value']) : $settingDB['value'] );
                }
                $settings[$settingDB['key']]['group'] = $settingDB['group'];
                $settings[$settingDB['key']]['type'] = $settingDB['type'];
                $settings[$settingDB['key']]['col'] = $settingDB['col'] ? $settingDB['col'] : 1;
                if ($settingDB['data']) $settings[$settingDB['key']]['data'] = $settingDB[data];
                if ($settingDB['headerhtml']) $settings[$settingDB['key']]['headerhtml'] = $settingDB[headerhtml];

                if($paramPackSql) foreach ($paramPackSql as $key => $value) if(in_array("'{$settingDB['key']}'", $value['key'])) $packgroup = $key;
                if($packgroup) $settings[$settingDB['key']]['group'] = $packgroup;
                unset($packgroup);unset($settingdecode);
            }

            $allParGet = paramArr();
            # перебор полей настроек
            foreach($settings as $setkey => $set) {

                unset($notThis);
                if(in_array("'".$setkey."'", $allParGet)) $notThis = 1;
                if(!($notThis && !count($paramPack))){

                    if($setkey == "robot" && !permission('seo')) continue;
                    if($setkey == "SEOitemcard" && !permission('seo_super')) continue;

                    // Все используемые поля в на странице, через запятую
                    $paramPackAll[] = "bc_".$setkey;
        
                    $set_name = ($settings[$setkey]['headerhtml'] ? $settings[$setkey]['headerhtml'] : (defined("LNG_".$setkey) ? constant("LNG_".$setkey) : ''));
                    switch ($set['type']) {
                        case '1': # строка
                            if($setkey=='sitename') $set['value'] = $db->get_var("SELECT Catalogue_Name FROM `Catalogue` WHERE Catalogue_ID = '{$this->catID}'");
                            $formValue = bc_input("bc_".$setkey, $set['value'], $set_name);
                            break;
                        case '2': # число
                            if(stristr($set_name, 'none') && is_numeric($set['value'])){
                                $dp = trim(str_replace('none', '', $set_name));
                                $set_name = $dp;
                            }
                            if(preg_match("/px$/i", $setkey) && is_numeric($set['value'])){ $dp = "px"; }

                            $formValue = bc_input("bc_".$setkey, $set['value'].$dp, $set_name);
                            break;
                        case '3': # текстовые блоки
                            if($setkey=='robot') $set['value'] = $db->get_var("SELECT Robots FROM `Catalogue` WHERE Catalogue_ID = '{$this->catID}'");
                            if ($setkey=='siteOffText') $set['value'] = $db->get_var("SELECT ncOfflineText FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
                            $formValue = bc_textarea("bc_{$setkey}", $set['value'], $set_name, "id='bc_{$setkey}'");
                            $set['col'] = "height";
                            break;
                        case '4': # списки
                            try {
                                unset($dataList);
                                if ($set['data']) {
                                    if (strstr($set['data'],"Classificator_")) {
                                        $listField = str_replace("Classificator_","",$set['data']);
                                        $listFieldID = $listField."_ID";
                                        $listFieldName = $listField."_Name";
                                        foreach($db->get_results("select * from {$set['data']} where Checked = 1", ARRAY_A) as $field) {
                                            $dataList .= "<option ".($field[$listFieldID]==$set['value'] ? "selected" : NULL)." value='{$field[$listFieldID]}'>{$field[$listFieldName]}</option>";
                                        }
                                    }else if($setkey=='mainFontName'){
                                        $dataList = getFonts($set['value'], null, 1);
                                    }else {
                                        $fieldArr = json_decode($set['data']);

                                        if ($fieldArr) {
                                            foreach(json_decode($set['data']) as $key => $field) {
                                                $dataList .= "<option ".($key==$set['value'] ? "selected" : NULL)." value='{$key}'>{$field}</option>";
                                            }
                                        }

                                        # модули шаблонов компонентов
                                        if(preg_match("/^size[\d]+_template$/", $setkey)){
                                            $id = str_replace(array("size", "_template"), "", $setkey);
                                            $templatePath = $DOCUMENT_ROOT.$pathInc2."/template/{$id}/template/objects/";

                                            if(is_dir($templatePath)){
                                                if($handle = opendir($templatePath)){
                                                    while(false !== ($file = readdir($handle))){
                                                        if($file != '.' && $file != '..'){
                                                            $pathFile = $templatePath.$file;
                                                            if(is_file($pathFile)){
                                                                $name = str_replace(".php", "", $file);
                                                                $dataList .= "<option ".($name==$set['value'] ? "selected" : NULL)." value='{$name}'>{$name}</option>";
                                                            }
                                                        }
                                                    }
                                                    closedir($handle);
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                if ($setkey == 'cdekMainCity' && Cdek::getInstance()->isOn()) {
                                    foreach (Cdek::getInstance()->getCityList() as $id => $city) {
                                        $atr = "value='{$id}'";
                                        if ($id == $setting['cdekMainCity']) {
                                            $atr .= " selected";
                                        }
                                        $dataList .= "<option {$atr}>{$city}</option>";
                                    }
                                }
                                if ($setkey == 'cdek_shipment_point' && Cdek::getInstance()->isOn()) {
                                    $cdek = Cdek::getInstance();
                                    foreach ($cdek->getAllPvzCode() as $pvzCode) {
                                        if (!$cdek->getPvz($pvzCode, 'isReception')) continue;
                                        $atr = "value='{$pvzCode}'";
                                        if ($pvzCode == $setting['cdek_shipment_point']) {
                                            $atr .= " selected";
                                        }
                                        $dataList .= "<option {$atr}>".$cdek->getPvz($pvzCode, 'name')." (".$cdek->getPvz($pvzCode, 'addressFull').")</option>";
                                    }
                                }
                                $formValue = bc_select("bc_".$setkey, $dataList, $set_name, "class='ns'");
                            } catch (\Exception $e) {
                                $formValue = $e->getMessage();
                            }
                      
                            break;
                        case '5': # чекбоксы
                            if ($setkey == 'siteOff') $set['value'] = !$db->get_var("SELECT Checked FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
                            $formValue = bc_checkbox("bc_".$setkey, 1, $set_name, $set['value']);
                            break;

                        
                        case '6': # файлы
                            $formValue = bc_file("bc_".$setkey, $set['value'], $set_name, $HTTP_FILES_PATH.$set['value'], 'bc');
                            break;
                        case '11': # Списки
                            if ($setkey == 'lists_cdekTarifId' && Cdek::getInstance()->isOn()) {
                                $cdek = Cdek::getInstance();
                                $formValue = "<div class=''><h4>{$set_name}</h4>";
                                foreach ($cdek->getTariffCodes() as $tariffCode) {
                                    $formValue .= "<div class='colline colline-2'>
                                                ".bc_checkbox(
                                                    "bc_lists_cdekTarifId[]", 
                                                    $tariffCode, 
                                                    $cdek->getTariff($tariffCode, 'name').' '.$cdek->getTariffType($tariffCode, 'titleShort')." ({$tariffCode})", 
                                                    $cdek->isCheckedTariff($tariffCode)
                                                )."
                                                <div class='cdek-tarif-info'>
                                                    <span class='cdek-tarif-info-icon'></span>
                                                    <div class='cdek-tarif-info-body'>
                                                        <span class='info-row title'>Способ доставки: ".$cdek->getTariffType($tariffCode, 'title')."</span>
                                                        <span class='info-row'>Описание: ".$cdek->getTariff($tariffCode, 'description')."</span>
                                                    </div>
                                                </div>
                                            </div>";
                                }
                                $formValue .= "</div>";
                            }
                            else {
                                $formValue = bc_multi_line("bc_".$setkey, $set['value'], $set_name);
                            }
                            $set['col'] = "height";
                            break;
                        case '12': # цвета
                            $formValue = bc_color("bc_".$setkey, $set['value'], $set_name);
                            break;
                        case '13': # текст - строка
                            $formValue = bc_text($set_name);
                            break;
                        case '14': # Кол-во выводимых объектов (резина)
                            $formValue = bc_multi_line("bc_".$setkey, $set['value'], $set_name, 2);
                            break;
                        case '15': # генерация форм
                            $formValue = bc_textarea("bc_{$setkey}", $set['value'], $set_name, "id='bc_{$setkey}'");
                            $set['col'] = "height";
                            break;
                        case '16': # чекбоксы superadmin
                            if (permission('superadmin')) {
                                if ($setkey == 'siteOff') $set['value'] = !$db->get_var("SELECT Checked FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
                                $formValue = bc_checkbox("bc_".$setkey, 1, $set_name, $set['value']);
                            }
                            break;
                    };


                    $formInp[$set['group']] .= "<div class='colline colline-{$set['col']} type-{$set['type']} name-{$setkey}'>".($current_user['superadmin'] ? saGetSettparam() : null)."{$formValue}</div>";
                }

            }


            
            # группы
            if(is_array($paramPack)){
                $formHtmlBody = '';
                foreach($paramPack as $i => $gr) {
                        $formHtmlBody .= "<div class='colblock' id='gr_".$gr."'>
                                            <h3>".$paramPackSql[$gr]['name']."</h3>
                                            <div class='colblock-body'>";
                        
                        $colBlockBody = $formInp[$gr] ?? '';

                        $this->formSetHtmlColBlockBodyExtentions($colBlockBody, $gr);

                        $formHtmlBody .= $colBlockBody;

                        $formHtmlBody .= "</div></div>";
                }
            }else{
                $groupnameArr = $db->get_results("select groupname from Bitcat_group ORDER BY Priority",ARRAY_A);
                foreach($groupnameArr as $i => $gr) {
                    if ($gr['groupname'] == 'shop' && !permission("catalogue")) continue;
                    if($formInp[$gr['groupname']]){
                        $formHtmlBody .= "<div class='colblock' id='gr_".$gr['groupname']."'>
                                            <h3>".constant("LNG_".$gr['groupname']."_GR")."</h3>
                                            <div class='colblock-body'>";
                        $formHtmlBody .= $formInp[$gr['groupname']];
                        $formHtmlBody .= "</div></div>";
                    }
                }
            }



            $formHtml = "<form class='bc_form ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=formsetsave' method='post'>
                            <div class='formwrap'>
                                <input type=hidden name='bc_field' value='".implode(',', $paramPackAll)."'>
                                <input type=hidden name='bc_action' value='formsetsave'>
                                {$formHtmlBody}
                            </div>
                            <div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения'></span><div class='result respadding'></div></div></div>
                        </form>";




            return $formHtml;
        }
    }

    /**
     * Расширение блоков настроек
     * 
     * @param string $body
     * @param int $paramPackNumber
     * 
     * @return void
     */
    private function formSetHtmlColBlockBodyExtentions(&$body, $paramPackNumber)
    {
        $extensions = [
            # статусы заказов
            62 => function(&$body) {
                $class2005 = new Class2005();
                $orderStatusList = $class2005->getOrderStatusList();

                $beforeContent = "<div class='colline colline-height type-11 name-lists_order_status'>";
                    foreach ($orderStatusList as $orderStatus) {
                        if ('default' !== $orderStatus['type']) continue;
                        $beforeContent .= "<div class='multi-line multi-list'>";
                            $beforeContent .= "<span class='txt'>{$orderStatus['name']}</span>";
                        $beforeContent .= "</div>";
                    }
                $beforeContent .= "</div>";
                $beforeContent .= "<div class='colline colline-height type-11 name-lists_order_status'>";
                    $beforeContent .= bc_multi_line('bc_lists_order_status', array_filter($orderStatusList, function($item){return 'custom' === $item['type'];}), '', 1);
                $beforeContent .= "</div>";

                $body = $beforeContent.$body;
            },
            # Письма изменения статусов
            63 => function(&$body) {
                $beforeContent = "<div class='colline colline-height txt'>";
                    $beforeContent .= "<b>Ключевые слова для использования в письмах:</b>";
                    $beforeContent .= "<br/>%FIO% - имя пользователя в заказе";
                    $beforeContent .= "<br/>%STATUS% - новый статус заказа";
                    $beforeContent .= "<br/>%ORDER_ID% - номер закза";
                $beforeContent .= "</div>";

                $body = $beforeContent.$body;
            }
        ];

        if (isset($extensions[$paramPackNumber])) {
            $extensions[$paramPackNumber]($body);
        }
    }


    # сохранение настроек
    public function formSetSave() {
        global $db, $FILES_FOLDER, $DOCUMENT_ROOT, $pathInc, $current_user, $login;
        include('class.upload.php');

        // take settings from BD
        $settingsBD = $db->get_results("select * from Bitcat order by `group`,`Priority`", ARRAY_A);

        if ($settingsBD) {
            // take settings from File
            $settingsFile = getSettings();
            foreach($settingsBD as $setting) {
                //декодируем, если это 'списки'
                if($setting['type']==11) $setting['value'] = orderArray($setting['value']);
                $settings[$setting['key']]['value'] = (isset($settingsFile[$setting['key']]) ? $settingsFile[$setting['key']] : $setting['value']);
                $settings[$setting['key']]['type'] = $setting['type'];
                //$res .= $setting['key']." ".$settings[$setting['key']]['type']."\n\r";
            }
        }

        $pathManifest = glob($_SERVER['DOCUMENT_ROOT']."/bc/modules/bitcat/manifestes/*.json");
        foreach($pathManifest as $manifest) {
            if (strpos($manifest, 'menu_manifest.json') !== false ) continue;
            $menuServis = json_decode(file_get_contents($manifest), 1);
            if (!$menuServis) return "Ошибка в файле {$manifest}";
            foreach ($menuServis as $block) {
                foreach($block['row'] as $row) {
                    $settings[$row['name']]['value'] = (isset($settingsFile[$row['name']]) ? $settingsFile[$row['name']] : '');
                    //to-do связь с старыми типами
                    $settings[$row['name']]['type'] = ($row['type_old'] ? $row['type_old'] : 1);
                    $settings[$row['name']]['new_type'] = $row['type'];
                }
            }
        }

        $otherManifests = glob($_SERVER['DOCUMENT_ROOT'] . "/b/{$login['login']}/manifest/*.json");

        if (count($otherManifests) > 0) {
            foreach ($otherManifests as $otherManifestJson) {
                if (strpos($otherManifestJson, 'menu_manifest.json') !== false ) continue;
                $otherManifest = json_decode(file_get_contents($otherManifestJson), 1);
                if (!$otherManifest) return "Ошибка в файле {$otherManifestJson}";
                foreach ($otherManifest as $block) {
                    foreach($block['row'] as $row) {
                        $settings[$row['name']]['value'] = (isset($settingsFile[$row['name']]) ? $settingsFile[$row['name']] : '');
                        //to-do связь с старыми типами
                        $settings[$row['name']]['type'] = ($row['type_old'] ? $row['type_old'] : 1);
                        $settings[$row['name']]['new_type'] = $row['type'];
                    }
                }
            }
        
        }

        $res = $settings;
        if (!$settings) {
            $errorText = "Настройки не найдены";
            return $errorText;
        }
        // Определяем первый сайт или нет
        $saveBD = ($this->catID == 1 ? true : false );
        $filesFiled = $db->get_col("SELECT `key` FROM `Bitcat` where type = 6");

        // Все используемые параметры со страницы
        $bc_field = isset($_POST['bc_field']) && $_POST['bc_field'] != '' ? explode(',', $_POST['bc_field']) : NULL ;
        if($bc_field) foreach ($bc_field as $key){
            $bc_fields[] = str_replace("bc_","",$key);
        }

        # запись IP редактирования скриптов
        if(in_array('bc_meta', $bc_field) || in_array('bc_counter', $bc_field)){
            $settingspath = $DOCUMENT_ROOT.$pathInc."/IP_meta.ini";
            $settingFile = @file_get_contents($settingspath);
            $setting = orderArray($settingFile, 'file');


            if(!is_array($setting)) $setting = array();
            $setting[] = array('date' => date("Y-m-d H:i:s"), 'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'], 'IPJS' => $_POST['IPJS'], 'login' => $current_user['Login']);

            file_put_contents($settingspath, json_encode($setting));
        }

        if (isset($_POST)) {

            if ($_POST['lang']) { # для мультиязычности чтобы не упираться в лимит инпутов
                global $frKey, $secKey, $thKey;
                foreach (json_decode($_POST['lang'], true) as $key => $val) {
                    preg_replace_callback("/(^[^\[\]]*)|(\[[\d]*\])|(\[[^\d]*\])/", function($match){
                        global $frKey, $secKey, $thKey;
                        switch (count($match)) {
                            case 2: $frKey = $match[0];
                            break;
                            case 3: $secKey = substr($match[0], 1, -1);
                            break;
                            case 4: $thKey = substr($match[0], 1, -1);
                            break;
                            default: break;
                        }
                        return $match[0];
                    }, $key);
                    $_POST[$frKey][$secKey][$thKey] = $val;
                }
                unset($_POST['lang']);
            }

            foreach($_POST as $setkey => $set) {
                if (strstr($setkey,"bc_")) {
                    $set = stripcslashesAll($set);
                    
                    if ($setkey=='bc_action' || $setkey=='bc_filed') continue;
                    

                    $setkeyName = str_replace("bc_","",$setkey);
                    // Типо защита
                    $nocheckjs = array('meta', 'counter', 'robot');

                    if(!in_array($setkeyName, $nocheckjs)) $set = securityForm($set, "noslashes");
                    
                    if($settings[$setkeyName]['type'] != 11){
                        $set = str_replace("inherit","",trim($set));
                    }

                    if($settings[$setkeyName]['type'] == 2){
                        $set =  preg_replace("/[^0-9]/", '', $set);
                    }
                        
                    $saveInDb = array('robot', 'sitename', 'siteOffText', 'telegram_bot_order_provider_token', 'tags');
                    if(in_array($setkeyName, $saveInDb)){
                        // Сохранение данных robot, sitename
                        if (!is_array($set)) $set = strip_tags($set);
                        
                        switch ($setkeyName) {
                            case 'robot':
                                $db->query("UPDATE Catalogue set Robots = '{$set}' where Catalogue_ID = '{$this->catID}'");
                            break;
                            case 'sitename':
                                if ($set) {
                                    // $haveThisName = $db->get_var("SELECT Catalogue_Name FROM `Catalogue` WHERE Catalogue_Name = '{$set}'");
                                    // if (!$haveThisName) 
                                    $db->query("UPDATE Catalogue set Catalogue_Name = '{$set}' where Catalogue_ID = '{$this->catID}'");
                                }
                            break;
                            case 'siteOffText':
                                $db->query("UPDATE Catalogue SET ncOfflineText ='{$set}' WHERE Catalogue_ID = '{$this->catID}'");
                                $db->query("UPDATE Catalogue SET Checked = '".(isset($_POST['bc_siteOff']) ? 0 : 1)."' WHERE Catalogue_ID = '{$this->catID}'");
                            break;
                            case 'telegram_bot_order_provider_token':
                                $botRepo = new \TelegramBotRepository\RepositoryBase\Repository(new \TelegramBots\Order\Provider\Settings(), $db);
                                
                                $checked = (int) isset($_POST['bc_telegram_bot_order_provider_checked']);
                                $botID = $botRepo->getBotID();
                                $token = $db->escape(securityForm($_POST['bc_telegram_bot_order_provider_token']));
                                
                                $sql = "SELECT count(*)
                                        FROM Message2249 
                                        WHERE `Catalogue_ID` = {$this->catID} AND `bot_id` = {$botID}";
                                        
                                if (!$db->get_var($sql)) {
                                    $sql = "INSERT INTO Message2249 (`Checked`, `Catalogue_ID`, `token`, `bot_id`)
                                            VALUES ({$checked}, {$this->catID}, '{$token}', {$botID})";
                                    $db->query($sql);
                                } else {
                                    $sql = "UPDATE Message2249 SET `Checked` = {$checked}, `token` = '{$token}' WHERE `Catalogue_ID` = {$this->catID} AND `bot_id` = {$botID}";
                                    $db->query($sql);
                                }
                                break;
                            case 'tags':
                                (function() use ($set){
                                    unset($set['default']);

                                    $tagProvider = new \App\modules\Korzilla\Tag\Provider();
                                    $tagList = $tagProvider->tagGetList();

                                    foreach ($set as $id => $row) {
                                        if (empty($row['tag'])) continue;

                                        $tag = $tagProvider->tagCreate($row['tag']);

                                        if (isset($tagList[$id])) {
                                            $tag->Message_ID = $tagList[$id]->Message_ID;
                                            unset($tagList[$id]);
                                        }

                                        $tagProvider->tagSave($tag);
                                    }

                                    foreach ($tagList as $tag) {
                                        $tagProvider->tagRemove($tag);
                                    }
                                })();
                                break;
                        }
                    }else{
                        # Если массив списков
                        unset($setJson);
                        if ($settings[$setkeyName]['type'] == 11) {
                            $setn = array();
                            switch ($setkeyName) {
                                case 'lists_texts': # texts
                                    foreach ($set as $k => $v) {
                                        $setn[$v['keyword']]['name'] = $v['name'];
                                        $setn[$v['keyword']]['checked'] = $v['checked'] ?: "";
                                    }
                                    break;
                                case 'lists_language_keys': # lang
                                    $i = 0;
                                    foreach ($set as $v) {
                                        $setn[++$i] = $v;
                                    }
                                    break;
                                case 'lists_order_status':
                                    $class2005 = new Class2005();
                                    $maxId = $class2005->getOrderStatusListMaxId();
                                    foreach ($set as $k => &$v) {
                                        if (empty($v['name'])) {
                                            unset($set[$k]);
                                            continue;
                                        }
                                        if (empty($v['id'])) {
                                            $v['id'] = ++$maxId;
                                        }
                                    }
                                    unset($v);
                                    break;
                            }
                            $set = $setn ?: $set;
                            $setJson = json_encode($set);
                        }
                        $set2 = $setJson ?? $set;
                    }
                    // перезаписываем массив новыми значениями
                    if(!in_array($setkeyName, $filesFiled)){
                        $settings[$setkeyName]['value'] = $set;
                        if(!$set && $set !=0) $settings[$setkeyName]['value'] = "";
                    }
                } else {
                    if ($setkey=='nofile') { # удаление файла
                        foreach($set as $k => $s){
                            if ($k=='nophoto') {
                                $userImagePng = $DOCUMENT_ROOT.$pathInc."/files/nophoto.png";
                                $userImageJpg = $DOCUMENT_ROOT.$pathInc."/files/nophoto.jpg";
                                if(file_exists($userImagePng)) @unlink($userImagePng);
                                if(file_exists($userImageJpg)) @unlink($userImageJpg);
                            }
                            if ($saveBD) $db->query("update `Bitcat` SET `value` = '' where type = '6' AND `key` = '{$k}'");
                            // перезаписываем массив новыми значениями
                            $settings[$k]['value'] = '';
                        }
                    }
                }
            }
            // Сбросить чекбокс, если он пуст
            foreach ($bc_fields as $setkey) {
                (!$_POST['bc_'.$setkey] && ($settings[$setkey]['type']==5 ||$settings[$setkey]['type']==16  || $settings[$setkey]['new_type']=='checkbox') ? $settings[$setkey]['value'] = "" : null);
            }

        }
        if (isset($_FILES)) { # сохранение файлов
            foreach($_FILES as $setkey => $file) {
                $handle = new upload($file,"RU");
                $setkeyName = str_replace("bc_","",$setkey);
                if ($handle->uploaded) {
                    $handle->file_new_name_body   = $setkeyName;
                    $handle->file_auto_rename = false;
                    $handle->file_overwrite = true;
                    $handle->allowed = array('image/*');
                    if ($setkeyName=='bg') {
                        $handle->image_convert = 'jpg';
                        $handle->image_background_color = ($_POST[bc_bodybg] ? $_POST[bc_bodybg] : "#ffffff");
                        $handle->jpeg_quality = 100;
                        $filename = $setkeyName.".jpg";
                    } else {
                        $part = explode(".",$file[name]);
                        $filename = $setkeyName.".".$part[1];
                    }
                    $handle->process($FILES_FOLDER);
                    if ($handle->processed) {
                        $sql4 = "update `Bitcat` SET `value` = '{$filename}' WHERE type = '6' AND `key` = '".$setkeyName."'"; //$debug .= $sql4."  ### ";
                        if ($saveBD) $db->query($sql4);

                        // перезаписываем массив новыми значениями
                        $settings[$setkeyName]['value'] = $filename;

                        $handle->clean();
                    } else {
                        $debug = 'Ошибка: '.$handle->error;
                    }
                }
            }
        }

        foreach($settings as $key => $set) { $settingResult[$key] = $set['value'];}
        $peremetntt = json_encode($settings);

        if(setSettings($settingResult)){
            $reslt = json_encode(ARRAY(
                "title" => "Сохранено",
                "succes" => $this->savecss().$debug,
                "todo" => "ReloadCSSLink",
                "clearalltab" => 1,
                "reloadbtn" => 1
            ));
        }else{
            $reslt = json_encode(ARRAY(
                "title" => "Не сохранено",
                "error" => $debug,
                "todo" => "ReloadCSSLink"
            ));
        }
        return $reslt;
    }

    # получение настроек разделов в CSS
    public function subCSS() {
        global $db, $DOCUMENT_ROOT, $HTTP_FILES_PATH;
        $css = "";

        $subs = $db->get_results("SELECT Subdivision_ID as id, settingsSub FROM Subdivision WHERE Catalogue_ID = '{$this->catID}' AND settingsSub != 'null' AND settingsSub != ''", ARRAY_A);
        if ($subs) {
            foreach($subs as $sb) {
                $settingsSub = orderArray($sb['settingsSub']);
                // count items in blocks
                if($settingsSub['sizehave']){
                    $class = $db->get_var("SELECT Class_ID FROM Sub_Class WHERE Subdivision_ID = {$sb[id]} LIMIT 1");
                    $classType = array("2001" => "catalog", "2010" => "gallery", "2030" => "vendor", "244" => "advantage", "2021" => "portfolio", "2003" => "news", "2073" => "gencomponent");
                    if($class && $classType[$class]){
                        $css .= $this->countItems(array(
                            "tag" => ".page{$sb['id']}",
                            "type" => $classType[$class],
                            "item" => ".obj",
                            "margin" => $settingsSub['sizeitem_margin'],
                            "width" => $settingsSub['sizeitem'],
                            "image" => $settingsSub['sizeitem_image']
                        ));
                    }
                }
            }
            return array(
                'css' => $css
            );
        }

    }


    # получение настроек блоков в CSS
    public function blockCSS() {
        global $db, $DOCUMENT_ROOT, $HTTP_FILES_PATH;
        $fonts = array();  # все шрифты блоков

        $blocks = $db->get_results("select a.*, SUBSTRING_INDEX(a.bgimg, ':', -1) as bgimg from Message2016 as a, Subdivision as b where b.Catalogue_ID = '{$this->catID}' AND b.Subdivision_ID = a.Subdivision_ID", ARRAY_A);
        if ($blocks) {
            foreach($blocks as $bl) {
                unset($blockHeadRadius);
                $settings = orderArray($bl['settings']);
                $phpset = orderArray($bl['phpset']); $contset = $phpset['contsetclass'];
                if($settings['namefont']) $fonts[] = $settings['namefont']; # массив с шрифтами

                $cssb .= "#block{$bl['block_id']} {";
                    # фон
                    $cssb .= ($bl['bgimg'] ? "background-image: url('{$HTTP_FILES_PATH}{$bl['bgimg']}');" : NULL);
                    $cssb .= ($settings['bgimgpos'] && $bl['bgimg'] ? position_img_css($settings['bgimgpos']) : NULL);

                    $cssb .= ($settings['radius'] ? "-moz-border-radius: {$settings['radius']}px; -webkit-border-radius: {$settings['radius']}px; -khtml-border-radius: {$settings['radius']}px; border-radius: {$settings['radius']}px;" : NULL);
                    $cssb .= ($bl['height'] && $bl['height']!='100%' ? "overflow:auto;" : NULL);
                    $cssb .= ($settings['bottmarg']==1 ? "margin-bottom: 0;" : NULL);
                $cssb .= "}";

                $cssb .= ($settings['bg'] ? "#block{$bl['block_id']}, #block{$bl['block_id']} ul.h_menu_sec, #block{$bl['block_id']} ul.h_menu_third {background-color: {$settings['bg']} !important;}" : NULL);
                if ($settings['radius']) $blockHeadRadius = ($settings['borderwidth'] && $settings['bordercolor'] ? "" : ($settings['radius']-1));

                $cssb .= (strstr($bl['height'],"%") ? "#block{$bl['block_id']}, #block{$bl['block_id']} .blk_body {height:".$bl['height'].";}" : NULL);

                $cssb .= ($bl['height'] && !strstr($bl['height'],"%") ? "#block{$bl['block_id']} {max-height:".$bl['height']."px; _height:".$bl['height']."px;}" : NULL);

                $cssb .= ($blockHeadRadius ? "#block{$bl['block_id']} .blk_head {-webkit-border-top-left-radius: {$blockHeadRadius}px; -webkit-border-top-right-radius: {$blockHeadRadius}px; -moz-border-radius-topleft: {$blockHeadRadius}px; -moz-border-radius-topright: {$blockHeadRadius}px; border-top-left-radius: {$blockHeadRadius}px; border-top-right-radius: {$blockHeadRadius}px;}" : NULL);
                $cssb .= ($settings['radius'] ? "#block{$bl['block_id']} #slider {-moz-border-radius: {$settings['radius']}px; -webkit-border-radius: {$settings['radius']}px; -khtml-border-radius: {$settings['radius']}px; border-radius: {$settings['radius']}px;}" : NULL);
                $cssb .= ($bl['bg'] ? "#block{$bl['block_id']} .blk_body {background: none;}" : NULL);
                $cssb .= ($settings['fontcolor'] ? ".block{$bl['block_id']} .blk_head.nobg, #block{$bl['block_id']} .blk_head.nobg a, #block{$bl['block_id']} .blk_body, #block{$bl['block_id']} .tel_lp_item a {color:{$settings['fontcolor']} !important;}" : NULL);

                $cssb .= ($settings['linkcolor'] || $settings['fontcolor']  ? "#block{$bl['block_id']} .blk_body a {color:".($settings['linkcolor'] ? $settings['linkcolor'] : $settings['fontcolor']).";} ul.left_m_sec li.menu_open span.menu_plus:before, ul.left_m_sec li.menu_open span.menu_plus:after {background:".($settings['linkcolor'] ? $settings['linkcolor'] : $settings['fontcolor']).";}" : NULL);
                $cssb .= ($settings['iconcolor']  ? "#block{$bl['block_id']} .blk_body .iconsCol:before {color: {$settings['iconcolor']};}" : NULL);

                $cssb .= ($settings['floathead'] ? "#block{$bl['block_id']} .blk_head {text-align: {$settings['floathead']};}" : NULL);
                $cssb .= ($settings['floatbody'] ? "#block{$bl['block_id']} .blk_body {text-align: {$settings['floatbody']};}" : NULL);

                $cssb .= ($settings['borderwidth'] && $settings['bordercolor'] ? "
                #block{$bl['block_id']} {padding: {$settings['borderwidth']}px; box-shadow: 0px 0px 0px {$settings['borderwidth']}px {$settings['bordercolor']} inset;}
                #block{$bl['block_id']} .blk_head {border-bottom: 1px rgba(".HEXNaRGB($settings['bordercolor']).",0.2) solid;}
                #block{$bl['block_id']} .blk_body > ul > li:first-of-type {border-top: 0px;}
                #block{$bl['block_id']} .blk_body > ul > li:last-of-type {border-bottom: 0px;}
                " : NULL);
                $cssb .= ($settings['headbg'] ? "#block{$bl['block_id']} .blk_head {background: {$settings['headbg']};}" : NULL);

                $cssb .= ($contset['bannerNameSize'] ? "#block{$bl['block_id']} .slider-name {font-size: ".$contset['bannerNameSize'].(!stristr($contset['bannerNameSize'], 'px') ? "px": "").";}" : NULL);
                $cssb .= ($contset['bannerTextSize'] ? "#block{$bl['block_id']} .slider-text {font-size: ".$contset['bannerTextSize'].(!stristr($contset['bannerTextSize'], 'px') ? "px": "").";}" : NULL);

                $cssb .= ($settings['textsize'] ? "#block{$bl['block_id']} .blk_body{font-size:{$settings['textsize']}px;}" : NULL);
                $cssb .= ($settings['headupper'] ? "#block{$bl['block_id']} .blk_head .h2{text-transform:uppercase;}" : NULL);
                $cssb .= ($settings['headcolor'] ? "#block{$bl['block_id']} .blk_head .h2, #block{$bl['block_id']} .blk_head a {color: {$settings['headcolor']};}" : NULL);
                $cssb .= ($settings['headbold'] ? "#block{$bl['block_id']} .blk_head .h2, #block{$bl['block_id']} .blk_head a {font-weight: bold;}" : NULL);
                $cssb .= ($settings['headsize'] ? "#block{$bl['block_id']} .blk_head .h2, #block{$bl['block_id']} .blk_head a {font-size: $settings[headsize]px;}" : NULL);


                $cssb .= ($settings['scrollbutcol'] || $settings['scrollbutfont'] ? "
                    #block{$bl['block_id']} .owl-carousel .owl-nav div, #block{$bl['block_id']} .owl-carousel .owl-dot span {
                        ".($settings['scrollbutcol'] ? "background-color: {$settings['scrollbutcol']};" : "")."
                        ".($settings['scrollbutfont'] ? "color: {$settings['scrollbutfont']};" : "")."
                    }
                " : NULL);

                // count items in blocks
                if($contset['sizehave']){
                    if($phpset['contenttype']==2){ # разделы
                        $cssb .= $this->countItems(array(
                            "tag" => "#block{$bl['block_id']}",
                            "type" => "subdivision",
                            "item" => ".sub",
                            "margin" => $contset['sizeitem_margin'],
                            "width" => $contset['sizeitem'],
                            "image" => $contset['sizeitem_image']
                        ));
                    }else{ # объекты
                        $class = $db->get_var("SELECT Class_ID FROM Sub_Class WHERE Subdivision_ID = {$bl[sub]} LIMIT 1");
                        $classType = array(
                            "2001" => "catalog",
                            "2010" => "gallery",
                            "2030" => "vendor",
                            "244" => "advantage",
                            "2021" => "portfolio",
                            "2003" => "news",
                            "2073" => "gencomponent"
                        );
                        if($class && $classType[$class]){
                            $cssb .= $this->countItems(array(
                                "tag" => "#block{$bl['block_id']}",
                                "type" => $classType[$class],
                                "item" => ".obj",
                                "margin" => $contset['sizeitem_margin'],
                                "width" => $contset['sizeitem'],
                                "image" => $contset['sizeitem_image']
                            ));
                        }
                    }
                }

                # настройки меню
                if($phpset['contenttype']==2){
                    $cssb .= "body #block{$bl['block_id']}.thismenu .blk_body ul>li>a{
                        ".($settings['MenuColor'] ? "color: {$settings['MenuColor']};" : NULL)."
                        ".($settings['MenuUppercase'] ? "text-transform: uppercase;" : NULL)."
                        ".($settings['menuFontSize'] ? "font-size:{$settings['menuFontSize']}px;" : NULL)."
                        ".($settings['namefont'] ? "font-family:{$settings['namefont']};" : NULL)."
                    }";
                    $cssb .= "body #block{$bl['block_id']}.thismenu .blk_body ul>li.active>a{
                        ".($settings['MenuColorActive'] ? "color: {$settings['MenuColorActive']};" : NULL)."
                        ".($settings['menuBgActive'] ? "background-color: {$settings['menuBgActive']};" : NULL)."
                    }";
                }

            }
            $zone = $db->get_results("select Message_ID,Subdivision_ID,zone_id,Sub_Class_ID,Priority,Keyword,Checked,Catalogue_ID,name,zone_position,zone_priority,setting,SUBSTRING_INDEX(
                bgimg, ':', -1) as bgimg from Message2000 where Checked = 1  AND Catalogue_ID = '{$this->catID}' AND zone_position != 0 ORDER BY zone_position, zone_priority", ARRAY_A);
            if ($zone) {
                foreach($zone as $zn) {
                    $settingszone = orderArray($zn['setting']);

                    if($settingszone['height']>0){
                        $cssb .= "#zone{$zn['zone_id']} .blocks {";
                            $cssb .= "height: {$settingszone['height']}px; line-height: {$settingszone['height']}px;";
                        $cssb .= "}";

                        $cssb .= "@media only screen and (max-width: 780px) {
                            #zone{$zn['zone_id']} .blocks { height: auto; line-height: normal; }
                        }";
                        $cssb .= "#zone{$zn['zone_id']} .logo-img img {";
                            $cssb .= "max-height: {$settingszone['height']}px;";
                        $cssb .= "}";

                    }

                    if($settingszone['alignblocks']=='center' || $settingszone['alignblocks']=='right'){
                        $cssb .= "#zone{$zn['zone_id']} {";
                            $cssb .= "text-align: {$settingszone['alignblocks']};";
                        $cssb .= "}";
                        $cssb .= "#zone{$zn['zone_id']} .blocks {";
                            $cssb .= "float: none; display: inline-block;";
                        $cssb .= "}";
                    }

                    $cssb .= "#zone{$zn['zone_id']} {";
                        $cssb .= ($settingszone['mrgn_top'] ? "margin-top: {$settingszone['mrgn_top']}px;" : NULL);
                        $cssb .= ($settingszone['mrgn_bottom'] ? "margin-bottom: {$settingszone['mrgn_bottom']}px;" : NULL);
                        $cssb .= ($settingszone['padd_top'] ? "padding-top: {$settingszone['padd_top']}px;" : NULL);
                        $cssb .= ($settingszone['padd_bottom'] ? "padding-bottom: {$settingszone['padd_bottom']}px;" : NULL);
                        $cssb .= ($zn['zone_position']==5 ? "position: relative; z-index: 2;" : NULL);
                    $cssb .= "}";

                    $cssb .= "#zone{$zn['zone_id']} .zone-bg {";
                        $cssb .= ($settingszone['bgcolor'] ? "background-color: {$settingszone['bgcolor']};" : NULL);
                        $cssb .= ($zn['bgimg'] ? "background-image: url('{$HTTP_FILES_PATH}{$zn['bgimg']}');" : NULL);
                        $cssb .= ($settingszone['bgimgpos'] && $zn['bgimg'] ? position_img_css($settingszone['bgimgpos']) : NULL);
                        $cssb .= ($settingszone['fixed']  ? "background-attachment: fixed;" : NULL);
                    $cssb .= "}";

                    $cssb .= "#zone{$zn['zone_id']} > * {";
                        $cssb .= ($settingszone['height'] && $settingszone['fixheight'] ? "height: {$settingszone['height']}px;" : NULL);
                        $cssb .= ($settingszone['height'] ? "min-height: {$settingszone['height']}px;" : NULL);
                        $cssb .= ($settingszone['textcolor'] ? "color: {$settingszone['textcolor']};" : NULL);
                    $cssb .= "}";
                    $cssb .= "@media only screen and (max-width: 780px) {";
                        $cssb .= "#zone{$zn['zone_id']} > *:not(.zone-bg) {";
                            $cssb .= ($settingszone['height'] && $settingszone['fixheight'] ? "height: auto;" : NULL);
                            $cssb .= ($settingszone['height'] ? "min-height: auto;" : NULL);
                        $cssb .= "}";
                        $cssb .= "#zone{$zn['zone_id']} > .zone-bg { height: 100%; }";
                    $cssb .= "}";
                    $cssb .= ($settingszone['blkmarginbot0'] ? "#zone{$zn['zone_id']} .blocks { margin-bottom: 0; }" : NULL);

                    $cssb .= ($settingszone['linkcolor'] ? "#zone{$zn['zone_id']} a{color: {$settingszone['linkcolor']};}" : NULL);
                    $cssb .= ($settingszone['iconcolor'] ? "#zone{$zn['zone_id']} .icons:before{color: {$settingszone['iconcolor']};}" : NULL);
                }
            }
            return array(
                    'css' => $cssb,
                    'fonts' => $fonts
                );
        }

    }

    # создание CSS файла
    public function savecss() {
        global $db, $DOCUMENT_ROOT, $HTTP_FILES_PATH, $pathInc;

        if ($pathInc) {
            $csspath = $DOCUMENT_ROOT.$pathInc."/bc_custom.css";
            $csspath_min = $DOCUMENT_ROOT.$pathInc."/bc_custom.min.css";
            $csspath_mobileapp = $DOCUMENT_ROOT.$pathInc."/bc_custom_app.css";
            $csspath_mobileapp_min = $DOCUMENT_ROOT.$pathInc."/bc_custom_app.min.css";
            $settingspath = $DOCUMENT_ROOT.$pathInc."/settings.ini";
        }

        $colorID = $db->get_var("SELECT colorid FROM Catalogue WHERE Catalogue_ID = '{$this->catID}'");
        $v_img = $colorID ? "?c={$colorID}" : "";

        # создание массива настроек
        $setting = getSettings();

        if (!$setting) {
            $settings = $db->get_results("select * from Bitcat", ARRAY_A);
            foreach($settings as $key => $set) { $setting[$set['key']] = $set['value'];	}
            if (!$settings){
                $errorText = "Настройки не найдены";
                return $errorText;
            }
        }

        # Заменяем знаечения из массива data
        if ($data) foreach ($data as $key => $value) $setting[$key] = $value;

        # Плюс css разделов
        $subCSS = $this->subCSS();

        # Плюс css блоков
        $blockCSS = $this->blockCSS();


        $css .= '@charset "utf-8"; ';
        # css шрифты
        if ($setting['mainFontName']) $blockCSS['fonts'][] = $setting['mainFontName'];
        if ($blockCSS['fonts']){
            $fontsforlink = array();
            foreach (array_unique($blockCSS['fonts']) as $name) {
                $customize = getFonts($name, 'customize');
                $fontsforlink[] = str_replace(' ', '+', $name).($customize ? ":".$customize : null);
            }
            $css .= "@import url(//fonts.googleapis.com/css?family=".implode('|', $fontsforlink)."&subset=cyrillic-ext,latin,cyrillic);";
            $css .= "body {font-family: '{$name}'}";
        }

        # css
        $css .= ($setting['mainFontColor'] ? "body, #main, #topcontact .phone, #submenu ul li a, .ssubm, .typeblock header, .typeblock header a, .commenttype1 li div.name b, div.dialog .h1, table, td, footer a,
        .spoler.act a, .contact_title, .vkladki ul.kz_tabs_items a span.vk_op {color:".$setting['mainFontColor'].";}
        " : NULL);
        $css .= ($setting['mainFontName'] ? ".submenutype1, .submenutype2, .blk_head {font-family:'".$setting['mainFontName']."', Tahoma, Geneva, sans-serif;}" : NULL);
        $css .= ($setting['mainFontSize'] ? ".text_block, .txt {font-size:".$setting['mainFontSize']."px;}" : NULL);
        $css .= ($setting['bodybg'] ? "body {background-color: ".$setting['bodybg']."}" : NULL);

        # МЕНЮ
        $css .= ($setting['mainMenuBg'] ? ".submenutype1, .modal_head, ul.h_menu_sec, .btn-strt, a.btn-strt-a, span.radio:before, .catalog-items .fast_prew, .slider-blue .irs-slider, .slider-blue span.irs-bar, .filter-main-slider .irs-bar, body .mainmenubg, a.btn-a,ul.left_m_sec li.menu_open span.menu_plus:before, ul.left_m_sec li.menu_open span.menu_plus:after, body .blocks .owl-nav div, body .blocks .owl-dot span, body .owl-carousel .owl-nav div, body .owl-carousel .owl-dot span, .filter_m_hide .filter_m_hide_footer, .news-date > *, .template-1 li.sub, #cart-info .t-border, .cart-tags a:hover, .template-type2 #cart-info .tabs .tab a.active, .incart-typefull1 .incart_up, .incart-typefull1 .incart_down, .radio-standart .rdo-st:before, .userline-option .chb-standart label input[type='checkbox']:checked + span {background-color:".$setting['mainMenuBg'].";}
            .mblk-type-2 li.active > a, .ask_question a, .fast_buy a, .kz_napisat a, #cart-info-mini:before, .template-type2 .map_marker.icons:before {color:".$setting['mainMenuBg'].";}
            #cart-info .tabs .tab a.active { color: {$setting['mainMenuBg']} !important; }
        body .mainmenubg-font,
        body .mainmenubg-font-bf:before,
        body .mainmenubg-font-hov:hover,
        body .mainmenubg-font-hov-bf:hover:before, .txt ul li:before, .txt ol li:before, body .load-more a {color:".$setting['mainMenuBg'].";}
        body .mainmenubg-bordb {border-bottom-color:".$setting['mainMenuBg'].";}
        body .mainmenubg-bord, body .mainmenubg-bord-hov:hover, body .admtab, .ask_question a span, #cart-info ul.tabs, .fast_buy a span, .cart-tags a, .kz_napisat a, .template-type2 .fast_buy a, .radio-standart input:checked + .rdo-st, .userline-option .chb-standart label input[type='checkbox']:checked + span {border-color:".$setting['mainMenuBg'].";}
        body .mainmenubg-bord-hov-sh:hover, body .load-more a {border-color:".$setting['mainMenuBg'].";box-shadow: inset 0 0 0 1px ".$setting['mainMenuBg'].";}
        " : NULL);


        $css .= ($setting['linkcolor'] ? "a { color: ".$setting['linkcolor']."; border-bottom-color: ".$setting['linkcolor']."; } ul.left_m_sec li.menu_open span.menu_plus:before, ul.left_m_sec li.menu_open span.menu_plus:after{ background: ".$setting['linkcolor'].";}" : NULL);

        $css .= ($setting['bg'] ? "body { background-image: url(".$HTTP_FILES_PATH.$setting['bg'].$v_img.");}" : NULL);
        $css .= ($setting['bgnofix'] ? "body { background-attachment: fixed;}" : NULL);
        $css .= ($setting['bgpos'] ? "body { background-position: ".$setting['bgpos'].";}" : NULL);
        $css .= ($setting['bgfill'] && $setting['bgfill']!='_empty_' ? "body { background-repeat: ".$setting['bgfill']."; -webkit-background-size: auto; -moz-background-size: auto; -o-background-size: auto; background-size: auto;}" : NULL);

        $css .= ($setting['bginner'] ? "body.innerpage { background-image: url(".$HTTP_FILES_PATH.$setting['bginner'].$v_img.");}
        @media screen and (max-width: 800px) {
            body { background-image: url(".$HTTP_FILES_PATH.$setting['bginner'].") !important;}
        }" : NULL);
        $css .= ($setting['bginnernofix'] ? "body.innerpage { background-attachment: fixed;}" : NULL);
        $css .= ($setting['bginnerpos'] ? "body.innerpage { background-position: ".$setting['bginnerpos'].";}" : NULL);
        $css .= ($setting['bginnerfill'] && $setting['bginnerfill']!='_empty_' ? "body.innerpage { background-repeat: ".$setting['bginnerfill']."; -webkit-background-size: auto; -moz-background-size: auto; -o-background-size: auto; background-size: auto;}" : NULL);

        $css .= ($setting['bg2'] ? "#site { background-image: url(".$HTTP_FILES_PATH.$setting['bg2'].$v_img.");}" : NULL);
        $css .= ($setting['bg2nofix'] ? "#site { background-attachment: fixed;}" : NULL);
        $css .= ($setting['bg2pos'] ? "#site { background-position: ".$setting['bg2pos'].";}" : NULL);
        $css .= ($setting['bg2fill'] && $setting['bg2fill']!='_empty_' ? "#site { background-repeat: ".$setting['bg2fill'].";}" : "#site { -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;}");

        $css .= ($setting['bgcolor'] ? "#site:before { background-color: {$setting['bgcolor']}; display: block; }" : NULL);
        $css .= ($setting['bgcolor'] ? ".topplashmob { background-color: ".$setting['bgcolor']." }" : NULL);

        $css .= ($setting['blockcolor'] ? ".typeblock header, #bigcart table thead th, div.dialog .h1 {".$setting['blockcolor']."}" : NULL);
        $css .= ($setting['blocktext'] ? ".typeblock header, .typeblock header a, #bigcart th, #menu ul li.active div, div.dialog .h1, #menu ul li:hover ul li:hover div{color: ".$setting['blocktext']."}" : NULL);
        $css .= ($setting['bannerHeight'] ? "#slider, #slider .ws_images, #slider .ws_images ul a, #slider .ws_images > div > img {max-height:".$setting['bannerHeight']."px;}" : NULL);
        $css .= ($setting['bannerTextBot'] ? "#slider .ws-title {bottom:".$setting['bannerTextBot']."px;}" : NULL);
        $css .= ($setting['bannerBottom'] ? "#slider {margin-bottom:".$setting['bannerBottom']."px;}" : NULL);

        $css .= ($setting['radius'] ? "#main {-moz-border-radius: ".$setting['radius']."px; -webkit-border-radius: ".$setting['radius']."px; -khtml-border-radius: ".$setting['radius']."px; border-radius: ".$setting['radius']."px;}" : NULL);

        $css .= ($setting['buttoncolor'] || $setting['cardtextcololorbtn'] ? "body .btn-strt, body .btn-bg, body .btn-strt-a, body .slider-blue .irs-slider, body .slider-blue .irs-slider, body .slider-blue span.irs-bar {
            ".($setting['buttoncolor'] ? "background-color: {$setting['buttoncolor']};": "")."
            ".($setting['cardtextcololorbtn'] ? "color: {$setting['cardtextcololorbtn']};": "")."
        }" : NULL);
        $css .= ($setting['cardtextcololorbtn'] ? "body span.podbor_add_g:before, body .btn-strt input {color: ".$setting['cardtextcololorbtn'].";}" : NULL);
        $css .= ($setting['icons'] ? "body .iconsCol:before {color: ".$setting['icons'].";}" : NULL);
        $css .= ($setting['iconcart'] ? "body .cart-btn a, body .cart-btn a:before, .cart-btn.incart-type1 .incart_up:before, .cart-btn.incart-type1 .incart_down:before {color: ".$setting['iconcart']." !important;}" : NULL);


        $css .= ($setting['cardbg'] ? ".catalog-item {background-color: ".$setting['cardbg'].";}" : NULL);
        $css .= ($setting['cardcolortext'] ? ".catalog-item .blk_text {color: ".$setting['cardcolortext'].";}" : NULL);
        $css .= ($setting['cardcolorprice1'] ? ".normal_price {color: ".$setting['cardcolorprice1'].";}" : NULL);
        $css .= ($setting['cardcolorprice2'] ? ".last_price, .card_price_second .last_price {color: ".$setting['cardcolorprice2'].";}" : NULL);
        $css .= ($setting['cardcolorprice3'] ? ".new_price {color: ".$setting['cardcolorprice3'].";}" : NULL);

        $css .= ($setting['cardborderwidthpx']>=0 ? ".catalog-item { box-shadow: 0px 0px 0px {$setting['cardborderwidthpx']}px ".($setting['cardcolorbord'] ? $setting['cardcolorbord'] : "transparent").";} .catalog-items { padding: {$setting['cardborderwidthpx']}px;}" : NULL);
        $css .= ($setting['cardborderradiuspx'] ? ".catalog-item { border-radius: {$setting['cardborderradiuspx']}px; }" : NULL);
        $css .= ($setting['cardborderwidthpx'] && $setting['cardcolorbord'] ? ".catalog-item:hover { box-shadow: 0px 0px 0px {$setting['cardborderwidthpx']}px rgba(0, 0, 0, 0.5);}" : NULL);

        $css .= ($setting['cardbutbg'] ? "
            .cart-btn.mainmenubg, .cart-btn .mainmenubg, .incart-typefull1 .incart_up, .incart-typefull1 .incart_down {background: ".$setting['cardbutbg'].";}
            .cart-line .incart_up:before, .cart-line .incart_down:before{color: ".$setting['cardbutbg']." !important;}
        " : NULL);
        $css .= ($setting['itemtitlecolor'] || $setting['itemtitlebold'] || $setting['itemtitleupper'] || $setting['itemtitleborder'] ? ".blk_name a {
            ".($setting['itemtitlecolor'] ? "color: {$setting['itemtitlecolor']};" : "")."
            ".($setting['itemtitlebold'] ? "font-weight: bold;" : "")."
            ".($setting['itemtitleupper'] ? "text-transform: uppercase;" : "")."
            ".($setting['itemtitleborder'] ? "text-decoration: underline;" : "")."
        }" : NULL);


        $css .= ($setting['icons'] ? "body .typeblock article .smallcart_info .i {color: ".$setting[icons].";}" : NULL);
        $css .= ($setting['notopcontact'] ? "#logoslogan {padding-right:0px;}" : NULL);

        $css .= ($setting['bottomFontColor'] ? "footer .blocks, footer .blocks a, footer .blocks a:hover {color:".$setting['bottomFontColor'].";}" : NULL);
        $css .= ($setting['bottomIconColor'] ? "footer .blocks .i {color:".$setting['bottomIconColor'].";}" : NULL);
        $css .= ($setting['bottomLine'] ? "footer .blocks {border-top: 1px #b2b2b2 solid;}" : NULL);

        // count items in settings
        $css .= $this->countItems(array("type" => "subdivision", "class" => "sub", "item" => ".sub"));
        $css .= $this->countItems(array("type" => "catalog", "class" => "2001"));
        $css .= $this->countItems(array("type" => "gallery", "class" => "2010"));
        $css .= $this->countItems(array("type" => "vendor", "class" => "2030"));
        $css .= $this->countItems(array("type" => "advantage", "class" => "244"));
        $css .= $this->countItems(array("type" => "portfolio", "class" => "2021"));
        $css .= $this->countItems(array("type" => "news", "class" => "2003"));
        $css .= $this->countItems(array("type" => "gencomponent", "class" => "2073"));

        $css .= $subCSS[css]; // плюс css разделов
        $css .= $blockCSS[css]; // плюс css блоков

        if ($setting[css]) $css .= $setting[css]; // плюс свои CSS стили
        if ($setting[css1280]) $css .= "@media screen and (max-width: 1279px) and (min-width: 781px){ ".$setting[css1280]." }"; // плюс свои CSS стили
        if ($setting[css780]) $css .= "@media screen and (max-width: 780px){ ".$setting[css780]." }"; // плюс свои CSS стили
        if ($setting[cssColor]) $css .= $setting[cssColor]; // CSS color template

        if ($setting[mainMenuBg]) $this->favicon($setting[mainMenuBg]);


        //$css = str_replace("\\", "&#92;", $css);
        if ($csspath && $css && file_put_contents($csspath, $css)>0 && file_put_contents($csspath_min, $this->csscompress($css))>0) {
            # mobile app css
            $css_mobileApp = $setting['mobileApp'] ? $setting['mobileApp'] : "";
            file_put_contents($csspath_mobileapp, $css_mobileApp);
            file_put_contents($csspath_mobileapp_min, $this->csscompress($css_mobileApp));

            unset($setting);
            $this->sendlog();
            return "Настройки сохранены";
        } else {
            return "Настройки не сохранены";
        }
    }

    # минификация css
    public function csscompress($buffer) {

        /* удалить комментарии */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        /* удалить табуляции, пробелы, символы новой строки и т.д. */
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
        $buffer = str_replace('    ', ' ', $buffer);
        $buffer = str_replace('   ', ' ', $buffer);
        $buffer = str_replace('  ', ' ', $buffer);
        $buffer = str_replace(" and"," and ",$buffer);
        $buffer = str_replace("@"," @",$buffer);
        return $buffer;
    }

    # log
    public function sendlog() {
        /*global $current_user;
        $datalog = array(
            'ip' => $_SERVER[REMOTE_ADDR],
            'login' => $current_user['Login'],
            'dom' => $_SERVER[HTTP_HOST]
        );
        @file_get_contents("http://start.korzilla.ru/log/l.php?".http_build_query($datalog, '', '&'));
		*/
    }

    # Ширина элементов
    public function countItems($param) {
        global $db;
        if(!$param[type]) return;

        $setting = getSettings();

        $p = array(
            "item" => ".obj",
            "margin" => $setting["size{$param['class']}_margin"],
            "width" => $setting["size{$param['class']}"],
            "image" => $setting["size{$param['class']}_image"]
        );
        $p = array_merge($p, $param);

        $reslt = "
            {$p[tag]} .{$p[type]}-items .image-default:before,
            {$p[tag]} .{$p[type]}-items-list .image-default:before {padding-top: {$p[image]}%}
            {$p[tag]} .{$p[type]}-items {$p[item]} {max-width: 100%; width: {$p[width]}px; margin-right: {$p[margin]}px; margin-bottom: {$p[margin]}px;}
            {$p[tag]} .count-{$p[type]}-1 {$p[item]} { width: 100%; margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-2 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 2) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-2 {$p[item]}:nth-child(2n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-3 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 3) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-3 {$p[item]}:nth-child(3n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-4 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 4) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-4 {$p[item]}:nth-child(4n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-5 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 5) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-5 {$p[item]}:nth-child(5n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-6 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 6) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-6 {$p[item]}:nth-child(6n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-7 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 7) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-7 {$p[item]}:nth-child(7n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-8 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 8) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-8 {$p[item]}:nth-child(8n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-9 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 9) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-9 {$p[item]}:nth-child(9n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-10 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 10) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-10 {$p[item]}:nth-child(10n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-11 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 11) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-11 {$p[item]}:nth-child(11n){ margin-right: 0; }
            {$p[tag]} .count-{$p[type]}-12 {$p[item]} { width: calc(((100% + {$p[margin]}px) / 12) - {$p[margin]}px); }
            {$p[tag]} .count-{$p[type]}-12 {$p[item]}:nth-child(12n){ margin-right: 0; }
        ";
        return $p[type] ? $reslt : "";
    }


    # дополнительные настройки компонента в блоке
    public function LoadSetClass($sub,$typecont,$subidthisblk) {
        global $db;

        $sub = (int) $sub;
        $typecont = (int) $typecont;

        if ($typecont == 1) {
            $classid = $db->get_var("select a.Class_ID from Sub_Class as a, Subdivision as b where a.Subdivision_ID = '$sub' AND a.Subdivision_ID=b.Subdivision_ID");
            $reslt = setClassBlock("",$classid,$typecont, $subidthisblk);
        } else {
            $reslt = setClassBlock("","",$typecont, $subidthisblk);
        }

        return $reslt;
    }

    # создание favicon
    public function favicon($color) {
        global $db, $DOCUMENT_ROOT, $HTTP_FILES_PATH, $pathInc;
        if ($color && @mime_content_type($DOCUMENT_ROOT.$pathInc.'/favicon.ico')!='image/x-ico') {
            $im = imagecreatetruecolor(16, 16);
            imagesavealpha($im, true);
            $trans_color = imagecolorallocatealpha($im, 0, 0, 0, 127);
            imagefill($im, 0, 0, $trans_color);
            $prm = explode(",",HEXNaRGB($color));
            $background_color = ImageColorAllocate($im, trim($prm[0]), trim($prm[1]), trim($prm[2]));
            $myPoints = array(8,1,15,8,8,15);
            ImageFilledPolygon($im,$myPoints,3,$background_color);
            ImageFilledRectangle($im,0,6,7,10,$background_color);
            imagePNG($im, $DOCUMENT_ROOT.$pathInc.'/favicon.ico');
            imagedestroy($im);
        }
    }

    public function encodestring($string, $url='') {
        $table = array(
         'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e',
         'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'j', 'К' => 'k',
         'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r',
         'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c',
         'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'csh', 'Ь' => '', 'Ы' => 'y', 'Ъ' => '',
         'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya', 'а' => 'a', 'б' => 'b', 'в' => 'v',
         'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
         'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
         'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
         'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh',
         'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya');
        $string = trim($string);
        $output = str_replace(array_keys($table), array_values($table),$string);
        if ($url==1) {
            $output = str_replace(" ","-",$output);
            $output = str_replace("_","-",$output);
            $output = preg_replace("/[^a-zA-Z0-9-]/","",$output);
            $output = str_replace("kamaz","kamz",$output);
        }
        return trim($output);
    }
    # поля для сохранения
    public function saveFieldColor($type){
        $fields = array('bgcolor', 'bodybg', 'buttoncolor', 'cardbg', 'cardbutbg', 'cardcolorbord', 'cardcolorprice1', 'cardcolorprice2', 'cardcolorprice3', 'cardcolortext', 'headerIconColor', 'headerLinkColor', 'headerPhoneCodeColor', 'headerPhoneColor', 'iconcart', 'icons', 'linkcolor', 'mainFontColor', 'mainMenuBg', 'cardtextcololorbtn', 'itemtitlecolor', 'cssColor', 'lists_itemlabel');
        $files = array('bg', 'bg2', 'bginner', 'createrLogo'); # Картинки
        $block_settings = array('headcolor', 'headbg', 'MenuColor', 'MenuColorActive', 'bg', 'menuBgActive', 'bordercolor', 'fontcolor', 'linkcolor', 'iconcolor');
        $zone_settings = array('textcolor', 'linkcolor', 'iconcolor', 'bgcolor');

        if($type=='settingsFields') return $fields;
        if($type=='settingsFiles') return $files;
        if($type=='settings') return array_merge($fields, $files);
        if($type=='block_settings') return $block_settings;
        if($type=='zone_settings') return $zone_settings;
    }
    # Применение цветовой палитры
     public function savecolorfont($numcolor = '', $namefont = '', $random = 0) {
        global $db, $DOCUMENT_ROOT, $pathInc, $settings;

        if($random){
            $namefont = array_rand(getFonts());
            //$numcolor = array_rand($this->getColors());
        }
        if ($numcolor && is_numeric($numcolor)) {

            $db->query("UPDATE Catalogue SET colorid = {$numcolor} WHERE Catalogue_ID = '{$this->catID}'");

            $colorArray = $db->get_row("SELECT * FROM Template_Color WHERE ID = {$numcolor}", ARRAY_A);
            # вставка цвета settings
            $settingsFile = getSettings(); # Свой файл
            $settingsColor = orderArray($colorArray[settings], 'file'); # файл с базы
            foreach ($settingsColor as $key => $value){
                if(in_array($key, $this->saveFieldColor("settings"))){
                    if($key=="lists_itemlabel"){ #лейблы
                        if(is_array($value)) foreach ($value as $labelItem) {
                            foreach ($settingsFile[$key] as $i => $labelItemLast) {
                                if($labelItem[name]==$labelItemLast[name]){
                                    $settingsFile[$key][$i][color1] = $labelItem[color1];
                                    $settingsFile[$key][$i][color2] = $labelItem[color2];
                                }
                            }
                        }
                    }else{ # замена полей
                         $settingsFile[$key] = $value;
                    }
                }
            }
            if(!setSettings($settingsFile)){
                return json_encode(ARRAY(
                    "title" => "Не удалось выбрать цвет",
                    "error" => "#1"
                ));
            }

            # вставка цвета блоков
            if($colorArray[blocks]) $blkArray = orderArray($colorArray[blocks]);
            if(is_array($blkArray)){
                foreach($blkArray as $block) {
                    $row = $db->get_row("SELECT Message_ID, settings FROM Message2016 WHERE block_id = {$block[block_id]} AND Catalogue_ID = {$this->catID}", ARRAY_A);
                    $id = $row[Message_ID];
                    $settings = orderArray($row[settings]);
                    if($id){
                        foreach ($block[settings] as $key => $val) {
                            if(in_array($key, $this->saveFieldColor("block_settings"))) $settings[$key] = $val;
                        }
                        /*unset($block[block_id]);
                        $param = "";
                        foreach ($block as $key => $val) {
                            if($key=='settings' || $key=='phpset'){
                                $val = json_encode($val);
                            }
                            $param .= ($param ? ", " : "")."{$key} = '{$val}'";
                        }*/
                        $db->query("UPDATE Message2016 SET settings = '".json_encode($settings)."' WHERE Message_ID = {$id} AND Catalogue_ID = '{$this->catID}'");
                    }
                }
            }
            # вставка цвета зон
            if($colorArray[zone]) $zoneArray = orderArray($colorArray[zone]);
            if(is_array($zoneArray)){
                foreach($zoneArray as $zone) {
                    $row = $db->get_row("SELECT Message_ID, setting FROM Message2000 WHERE zone_id = {$zone[zone_id]} AND Catalogue_ID = {$this->catID}", ARRAY_A);
                    $id = $row[Message_ID];
                    $setting = orderArray($row[setting]);
                    if($id){
                        foreach ($zone[setting] as $key => $val) {
                            if(in_array($key, $this->saveFieldColor("zone_settings"))) $setting[$key] = $val;
                        }
                        /*unset($zone[zone_id]);
                        $param = "";
                        foreach ($zone as $key => $val) {
                            if($key=='setting'){
                                $val = json_encode($val);
                            }
                            $param .= ($param ? ", " : "")."{$key} = '{$val}'";
                        }*/
                        $db->query("UPDATE Message2000 SET setting = '".json_encode($setting)."' WHERE Message_ID = {$id} AND Catalogue_ID = '{$this->catID}'");
                    }
                }
            }
            # копирование картинок
            $imagePath = $DOCUMENT_ROOT."/start/concept-images/{$numcolor}";
            $this->copydirect($imagePath."/settings/", $DOCUMENT_ROOT.$pathInc."/files/");
            $colorgood = 1;
        }

        if($namefont){
            // Считываем нынешний файл с цветами
            if(getFonts($namefont)){
                if ($pathInc) $settingspath = $DOCUMENT_ROOT.$pathInc."/settings.ini";
                $nowFontOpen = @file_get_contents($settingspath);
                if ($nowFontOpen) $nowFont = orderArray($nowFontOpen);

                $nowFont['mainFontName'] = ($namefont!='clear' ? $namefont : "");

                if(file_put_contents($settingspath, json_encode($nowFont))>0){
                    $fontgood = 1;
                }else{
                    $reslt = json_encode(ARRAY(
                        "title" => "Не сохранено",
                        "error" => "Ошибка применения шрифтов"
                    ));
                }
            }else{
                $reslt = json_encode(ARRAY(
                    "title" => "Не сохранено",
                    "error" => "Не найден шрифт"
                ));
            }
        }
        if(!$reslt && ($colorgood || $fontgood)){
            if($this->savecss()){
                return json_encode(ARRAY(
                    "title" => "Сохранено",
                    "succes" => "Цветовая схема применилась",
                    "reload" => 1
                ));
            }
        }
    }
    # Форма цв. палитры
     public function openSaveColor() {
         global $db;
         $id = securityForm($_GET[id]);
         if(is_numeric($id)){
            $color = $db->get_row("SELECT Name, Colors FROM Template_Color WHERE ID = {$id}", ARRAY_A);
            $colors = orderArray($color[Colors]);
         }
         $html = "<form name='adminForm' id='adminForm' class='ajax2' method='post' action='/bc/modules/bitcat/index.php?bc_action=saveColor".($id ? "&id={$id}" : "")."'>";
             $html .= "<div class='modal-body'>";
                 $html .= "<div class='colline colline-1'>".bc_input("name", $color[Name], "Название", "maxlength='255' size='50'", 1)."</div>";
                 $html .= "<div class='colline colline-5'>".bc_color("color[]", $colors[0], "")."</div>";
                 $html .= "<div class='colline colline-5'>".bc_color("color[]", $colors[1], "")."</div>";
                 $html .= "<div class='colline colline-5'>".bc_color("color[]", $colors[2], "")."</div>";
                 $html .= "<div class='colline colline-5'>".bc_color("color[]", $colors[3], "")."</div>";
                 $html .= "<div class='colline colline-5'>".bc_color("color[]", $colors[4], "")."</div>";
                 $html .= "<div class='colline colline-1'>".bc_checkbox("editColor", 1, "Сохранить все настройки")."</div>";
             $html .= "</div>";
             $html .= "<div class='bc_submitblock'>
                        <div class='result'></div>
                            <span class='btn-strt'><input type='submit' value='".($id ? "Сохранить" : "Добавить")."'>
                        </span>
                    </div>";
        $html .= "</form>";
        return $html;
     }
    # Сохранение цв. палитры
     public function saveColor() {
         global $db, $pathInc, $DOCUMENT_ROOT;

         $name = securityForm($_POST[name]);
         $colors = securityForm($_POST[color]);
         $id = securityForm($_GET[id]);
         $editColor = securityForm($_POST[editColor]);

         foreach ($colors as $i => $color) if(!$color || !stristr($color, "#")) unset($colors[$i]);

         if($name && $colors){
            if($id){
                 $db->query("update Template_Color set Name = '{$name}', Colors = '".json_encode($colors)."' where ID = {$id} AND Catalogue_ID = '{$this->catID}'");

            }
             if($id && !$editColor){
                return $reslt = json_encode(ARRAY(
                        "title" => "ОК",
                        "succes" => "Цветовая схема изменена",
                        "reload" => 1,
                        "modal" => "close"
                ));
             }else{ # Создание цв. схемы
                $settingFile = getSettings();

                # добавление новой цв.схемы
                if(!$id){
                    $insert = "INSERT INTO Template_Color (Name, Colors, Catalogue_ID) VALUES ('{$name}', '".json_encode($colors)."', '{$this->catID}')";
                    $db->query($insert);
                    $id = $db->insert_id; # id цветовой палитры
                }

                # изменение цв. схемы
                if($id){
                    foreach ($settingFile as $key => $value) {
                         # сохранение картинок
                        if(in_array($key, $this->saveFieldColor("settingsFiles")) && $value){
                            $this->saveColorImages($DOCUMENT_ROOT.$pathInc."/files/", $value, array($id, "settings"));
                        }
                        # сохранение полей
                        if(in_array($key, $this->saveFieldColor("settingsFields"))) $settingFile[$key] = securityForm($value, "noslashes"); #Поля
                        else if(in_array($key, $this->saveFieldColor("settingsFiles"))) $settingFile[$key] = $value; # Файлы
                        else unset($settingFile[$key]);
                    }
                    # блоки
                    $blocks = $db->get_results("SELECT block_id, settings, phpset, notitle, nolink, bgimg, cssclass, height FROM Message2016 WHERE Catalogue_ID = '{$this->catID}'", ARRAY_A);
                    foreach ($blocks as $key => $blk) {
                        $blocks[$key][settings] = orderArray($blocks[$key][settings]);
                        $blocks[$key][phpset] = orderArray($blocks[$key][phpset]);
                    }
                    # зоны
                    $zone = $db->get_results("SELECT zone_id, setting, bgimg FROM Message2000 WHERE Catalogue_ID = '{$this->catID}'", ARRAY_A);
                    foreach ($zone as $key => $zn) {
                        $zone[$key][setting] = orderArray($zone[$key][setting]);
                    }

                    $updateBlock = "update Template_Color set blocks = '".json_encode($blocks)."' where ID = {$id} AND Catalogue_ID = '{$this->catID}'";
                    $db->query($updateBlock);
                    $updateZone = "update Template_Color set zone = '".json_encode($zone)."' where ID = {$id} AND Catalogue_ID = '{$this->catID}'";
                    $db->query($updateZone);
                     $updateSettings = "update Template_Color set settings = '".str_replace("\\","\\\\", json_encode($settingFile, JSON_HEX_QUOT))."' where ID = {$id} AND Catalogue_ID = '{$this->catID}'";
                    $db->query($updateSettings);

                     if(isJson(json_encode($blocks)) && isJson(json_encode($zone)) && is_array($settingFile)){
                        return json_encode(ARRAY(
                                "title" => "ОК",
                                "succes" => "Цветовая схема ".($editColor ? "изменена" : "добавлена"),
                                "reload" => 1,
                                "modal" => "close"
                        ));
                     }else{
                        return json_encode(ARRAY(
                            "title" => "Не сохранено",
                            "error" => "Запрос не удался #2",
                            "Block" => isJson(json_encode($blocks)),
                            "Zone" => isJson(json_encode($zone)),
                            "Settings" => is_array($settingFile)
                        ));
                     }
                 }else{
                    return json_encode(ARRAY(
                        "title" => "Не сохранено",
                        "error" => "Запрос не удался #1"
                    ));
                 }
             }

         }else{
            return json_encode(ARRAY(
                "title" => "Не сохранено",
                "error" => "Указаны не все параметры"
            ));
         }
     }

     # копирование картинок
     public function saveColorImages($lastpath, $filename, $newdirs){
         global $DOCUMENT_ROOT;

         $file = $lastpath.$filename;
        $newPath = $DOCUMENT_ROOT."/start/concept-images/";

        if(is_file($file)){
            foreach ($newdirs as $dir) {
                if($dir){
                    $newPath = $newPath.$dir."/";
                    if(!is_dir($newPath)) mkdir($newPath);
                }
            }
            @copy($file, $newPath.$filename);
        }
     }

    # Копирование папок с файлами
    public function copydirect($last, $now){
        if(!is_dir($last)) return false;
        if($handle = opendir($last)){
            while(false !== ($file = readdir($handle))){
                if($file != '.' && $file != '..'){
                    $path = $last.$file;
                    if(is_file($path)){
                        @copy($path, $now.$file);
                    }elseif(is_dir($path)){
                        if(!is_dir($now.$file)) mkdir($now.$file);
                        $this->copydirect($path."/", $now.$file."/");
                    }
                 }
            }
            closedir($handle);
        }
    }
    public function iiko($do)
    {
        global $MODULE_FOLDER, $setting, $catalogue, $pathInc, $db, $current_user;

        require_once $MODULE_FOLDER.'default/iiko.php';
        if (!$setting['iikoCheck'] || !$setting['iikoLogin'] || !$setting['iikoPassword']) return json_encode(array('error'));
        $auth = array(
            'login' => $setting['iikoLogin'],
            'pass' => $setting['iikoPassword']
        );
        $iiko = new Iiko($auth, $catalogue, $db, $pathInc);
        switch ($do) {
            case 'export':
                $reuslt = $iiko->export();
                break;
            case 'deleteall':
                $result = $iiko->deleteAll();
                break;
            default: break;
        }
    }

    public function getMultiLine()
    {
        global $db;
        $post = securityForm($_POST);
        switch ($post['elementType']) {
            default:
                $prefArr = array(
                    'Subdivision' => 'sb_'
                );
                $objFildArr = array(
                    'Subdivision' => 'Subdivision_ID',
                    'User' => 'User_ID',
                    'Sub_Class' => 'Sub_Class_ID'
                );
                if ($post['elementType'] && $post['fieldName'] && is_numeric($post['objID'])) {
                    $fKey = isset($objFildArr[$post['elementType']]) ? $objFildArr[$post['elementType']] : 'Message_ID';
                    $data = $db->get_var("SELECT {$post['fieldName']} FROM {$post['elementType']} WHERE {$fKey} = {$post['objID']}");
                    $data = $data ? orderArray($data) : array();
                    return bc_multi_line($prefArr[$post['elementType']].$post['fieldName'], $data);
                }
                break;
        }
    }

    public function getUniqueFieldValues($tableName, $fieldName)
    {
        global $db;

        $sql = "SELECT DISTINCT `{$fieldName}`
                FROM `{$tableName}`
                WHERE `Catalogue_ID` = {$this->catID}
                    AND `{$fieldName}` != '' AND `{$fieldName}` IS NOT NULL
                ORDER BY `{$fieldName}`";
        
        return $db->get_col($sql) ?: [];
    }

    public function getUniqueValuesFromParams($paramKey)
    {
        global $db;

        $result = [];

        $sql = "SELECT DISTINCT `params`
                FROM `Message2001`
                WHERE `Catalogue_ID` = {$this->catID}
                    AND `params` != '' AND `params` IS NOT NULL";

        $dbRows = $db->get_col($sql) ?: [];

        foreach ($dbRows as $dbRow) {
            foreach(explode("\r\n", $dbRow) as $paramRow) {
                $paramRow = explode("||", $paramRow);
                if ($paramRow[0] == $paramKey) {
                    $result[trim($paramRow[1],"|")] = true;
                }
            }
        }

        return array_keys($result);
    }

    public function isTableExistField($tableName, $fieldName)
    {
        global $db;

        $dbRows = $db->get_results("EXPLAIN `{$tableName}`", ARRAY_A) ?: [];

        foreach ($dbRows as $dbRow) {
            if ($dbRow['Field'] == $fieldName) {
                return true;
            }
        }
        
        return false;
    }

    public function getViewByParamsValuesList()
    {
        global $db;

        $error = false;
        if (empty($_REQUEST['class_id']) || empty($_REQUEST['sub_id']) || empty($_REQUEST['field_name'])) {
            $error = true;
            $errorText = 'Не переданы данные';
        }

        if (!$error) {
            $classID = (int) $_REQUEST['class_id'];
            $subID = (int) $_REQUEST['sub_id'];
            
            $isValueInParams = false;
            if (strpos($_REQUEST['field_name'], 'params_') === 0) {
                $fieldName = 'params';
                $isValueInParams = true;
                $paramKey = substr($_REQUEST['field_name'], 7);
            } else {
                $fieldName = $_REQUEST['field_name'];
            }
            
            if (!$this->isTableExistField("Message{$classID}", $fieldName)) {                
                $error = true;
                $errorText = "Не найдено поле {$fieldName} в таблице Message{$classID} (???)";
            }
        }
        
        if (!$error) {
            $subParamChecked = $db->get_var("SELECT `view_obj_by_param` FROM `Subdivision` WHERE `Subdivision_ID` = {$subID}");
            $subParamChecked = orderArray($subParamChecked) ?: [];
            $subParamChecked = $subParamChecked[$_REQUEST['field_name']] ?? [];

            $list = '';
            $values = $isValueInParams ? $this->getUniqueValuesFromParams($paramKey) : $this->getUniqueFieldValues("Message{$classID}", $fieldName);
            foreach ($values as $value) {
                $key = $_REQUEST['field_name'];
                $title = $value;

                $list .= "<div class='colline colline-2 view-obj-by-param__velue-wrapper colline-height'>";
                    $list .= bc_checkbox("sb_view_obj_by_param[{$key}][]", $value, $title, in_array($value, $subParamChecked));
                $list .= "</div>";
            }          
            if (!empty($list)) {
                $list .= "<input type='hidden' name='sb_view_obj_by_param[{$_REQUEST['field_name']}][loaded]' value='1' />";
            }  
        }

        return $error ? "<p>{$errorText}</p>" : ($list ? : '<p>Нет параметров</p>');
    }

    private function getFrontpadExportUsynchronicProductsForm()
    {
        global $db;

        $sql = "SELECT Subdivision.`Subdivision_ID`, Subdivision.`Subdivision_Name`
                FROM `Subdivision`
                    INNER JOIN `Sub_Class` ON Subdivision.`Subdivision_ID` = Sub_Class.`Subdivision_ID`
                WHERE Subdivision.`Catalogue_ID` = {$this->catID}
                    AND Sub_Class.`Class_ID` = 2001
                ORDER BY Subdivision.`Subdivision_Name`";

        $subOptions = '';
        foreach ($db->get_results($sql, ARRAY_A) ?: [] as $sub) {
            $subOptions .= "<option value='{$sub['Subdivision_ID']}'>{$sub['Subdivision_Name']}</option>";
        }

        $html  = "<form method='POST' class='ajax2' action='/bc/modules/bitcat/index.php?bc_action=frontpad_export_usynchronic_products'>";
            $html .= "<div class='modal-body'>";
                $html .= "<div class='colline colline-1'>";
                    $html .= bc_select('sub_id', $subOptions, "Раздел для выгрузки", "class='ns'");
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='bc_submitblock'>";
                $html .= "<div class='result'></div>";
                $html .= "<span class='btn-strt'>";
                    $html .= "<input type='submit' value='Выгрузить' />";
                $html .= "</span>";
            $html .= "</div>";
        $html .= "</form>";

        return $html;
    }

    private function frontpadExportUsynchronicProducts()
    {
        global $nc_core;

        $subId = (int) ($_POST['sub_id'] ?? 0);

        try {
            $frontpadController = new \App\modules\Korzilla\CRM\Frontpad\FrontpadController();
            $frontpadController->ExportUsynchronicProducts($this->catID, $subId);
            $sub = $nc_core->subdivision->get_by_id($subId);
        } catch (\Exception $e) {
            return json_encode([
                'title' => 'Ошибка выгрузки',
                'error' => $e->getMessage(),
            ]);
        }

        return json_encode([
            'succes' => true,
            'submodal' => 1,
            'title' => 'Товары выгружены',
            'confirmtext' => "<a href='{$sub['Hidden_URL']}'>{$sub['Subdivision_Name']}<a>",
        ]);
    }

    private function frontpadUpdateSyncedProducts()
    {
        try {
            $frontpadController = new \App\modules\Korzilla\CRM\Frontpad\FrontpadController();
            $frontpadController->updateSyncedProducts($this->catID, $subId);
        } catch (\Exception $e) {
            return json_encode([
                'succes' => 'true',
                'submodal' => 1,
                'title' => 'Ошибка выгрузки',
                'confirmtext' => $e->getMessage(),
            ]);
        }

        return json_encode([
            'succes' => 'true',
            'submodal' => 1,
            'title' => 'Товары обновлены',
            'confirmtext' => "",
        ]);
    }

    private function importExcel($action = '')
    {
        global $setting;

        $controller = new ImportExcelController();
        $controllerExport = new ExportSiteControlle();
        $result = '';
        switch ($action) {
            case 'view':
                $result = $controller->getSettingView($this->catID, $setting['powerseo']);
                $result .= $controllerExport->getSettingView($this->catID);
                break;
            case 'get_catalog':
                $result = $controller->getCatalog(['catalogue' => $this->catID] + $_POST);
                $result = json_encode(['data' => $result]);
                break;
            case 'get_process':
                $result = json_encode($controller->getProcess($this->catID));
                break;
            case 'set_catalog':
                $result = json_encode(['process' => $controllerExport->setCatalog($this->catID)]);
                break;
            case 'get_process_export':
                $result = json_encode($controllerExport->getProcess($this->catID));
                break;
        }

        return $result;
    }

    private function getUploaderPhotoItems($action)
    {
        switch ($action) {
            case 'get_settings':
                echo UploderPhotoItems::getTemplateSetting();
                break;
            case 'upload_photo':
                echo UploderPhotoItems::uploadPhotes();
                break;
        }
        
    }
}
?>
