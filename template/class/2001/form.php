<?php
global $setting_texts, $setting;
?>
<?php $productObj = Class2001::getItemById($message); ?>
<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main" <?= $setting['full_cat_opisitem2'] ?>>Главное</a></li>
    <? if ($catalogue == 1066) : ?>
        <li class="tab"><a href="#tab_banner">Баннер</a></li>
    <? endif; ?>
    <li class="tab"><a href="#tab_photo">Фотографии</a></li>
    <li class="tab"><a href="#tab_text2001">Описание</a></li>
    <li class="tab"><a href="#tab_prices">Цены</a></li>
    <li class="tab"><a href="#tab_variable">Варианты и цвета</a></li>
    <?php if ($setting['targeting']) { ?> <li class="tab"><a href="#tab_targeting">Таргетинг</a></li> <?php  } ?>
    <li class="tab"><a href="#tab_vars">Поля</a></li>
    <li class="tab"><a href="#tab_with">Вывод</a></li>
    <?= editItemChecked(1) ?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?= bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'", 1) ?></div>
        <?php global $catalogue; ?>
        <!-- варианты товаров -->
        <div class='colline colline-1' title='Внимание! Если выключить переключатель, товар сменит название и отделится от текущей группы'><?= bc_checkbox("changeAll", 1, "Изменить название всех товаров в группе", 1) ?></div>

        <?php if (permission('seo_super')) { ?><div class='colline colline-1'><?= bc_input("f_h1", $f_h1, "Альтернативное название (h1)", "maxlength='255' size='50'", 0) ?></div><?php  } ?>
        <div class='colline colline-2'><?= bc_input("f_art", $f_art, "Артикул", "maxlength='255' size='50'") ?></div>
        <div class='colline colline-2'><?= bc_input("f_art2", $f_art2, "Артикул-2 / Код", "maxlength='255' size='50'") ?></div>
        <div class='colline colline-2'><?= bc_input("f_vendor", $f_vendor, "Производитель", "maxlength='255' size='50'") ?></div>
        <div class='colline colline-3'><?= bc_checkbox("f_spec", 1, "Спецпредложение", $f_spec) ?></div>
        <div class='colline colline-3'><?= bc_checkbox("f_new", 1, "Новинки", $f_new) ?></div>
        <div class='colline colline-3'><?= bc_checkbox("f_action", 1, "Хит продаж", $f_action) ?></div>

        <div class='colblock colblock-sale'>
            <h4>Лейбл товара</h4>
            <?php
            $labels = $setting['lists_itemlabel'];
            $itemlabel = ($f_itemlabel || is_numeric($f_itemlabel)) && $f_itemlabel != "null" ? explode(",", $f_itemlabel) : array(54);
            foreach ($labels as $key => $item) {
                if ($item['checked']) echo "<div class='colline colline-3'>" . bc_checkbox('itemlabel[]', $key, $item['name'], in_array($key, $itemlabel)) . "</div>";
            }
            ?>
        </div>
    </div>

    <?php if ($catalogue == 1066) : ?>
        <div id='tab_banner'>
            <div class='colline colline-2'><?= bc_file('f_photo_banner', $f_photo_banner_old, "Фотография-слайд", $f_photo_banner, 3401) ?></div>
        </div>
    <?php endif; ?>
    ?>
    <div id='tab_photo' class='none'>
        <?php $previwSize = $setting['size2001_imagepx'] ? $setting['size2001_imagepx'] : "300"; ?>
        <?php $f_photo->settings->resize(1000, 800)->preview($previwSize, 900)->use_name('Описание фото'); ?>

        <div class='colline colline-height'><?= gv_multifile_field($f_photo, "Изображения") ?></div>

        <?php if ($_SERVER['REMOTE_ADDR'] == '31.13.133.138') { ?>
            <div class='colblock colblock-search'>
                <h4>Найти изображения</h4>
                <div class='multi-colline'>
                    <a href="" class="icons add-btn" data-name='name'><span>По названию</span></a><a href="" class="icons add-btn" data-name='art'><span>По артикулу</span></a>
                    <div class='serach-photo'></div>
                </div>
            </div>
        <?php  } ?>

        <div class='colline colline-height'><?= bc_textarea("f_photourl", $f_photourl, "Ссылки на картинки") ?></div>
    </div>

    <div class='none' id='tab_text2001'>
        <div class='colline colline-height'><?= bc_textarea("f_text", $f_text, ($setting_texts['full_cat_opisitem']['checked'] ? $setting_texts['full_cat_opisitem']['name'] : "Полное описание"), "data-ckeditor='1'") ?></div>
        <?php if ($setting_texts['full_cat_opisitem2']['checked']) { ?>
            <div class='colline colline-height'><?= bc_textarea("f_text2", $f_text2, $setting_texts['full_cat_opisitem2']['name'], "data-ckeditor='1'") ?></div>
        <?php  } ?>
        <div class='colline colline-height'><?= bc_textarea("f_descr", $f_descr, "Краткое описание в списке товаров", "data-bbcode='1'") ?></div>
    </div>


    <div id='tab_variable' class='none'>
        <?if ($action == 'add') $f_variablenameSide = 0;?>
        <div class='colline colline-1'><?= bc_checkbox("main_variant", '1', "Основной вариант товара", !$f_variablenameSide) ?></div>
        <?php
        if ($setting['groupItem'] && $action != 'add') {
            $variableItems = $db->get_results("SELECT Message_ID, variablename, Checked FROM Message2001 WHERE Subdivision_ID = {$sub} AND name = '{$f_name}' ORDER BY Priority", ARRAY_A);
            if ($variableItems) { ?>
                <div id='variableItemAll'>
                    <div id='variableItems'>
                        <?php foreach ($variableItems as $item) { ?>
                            <div class='multi-line' data-num='<?= $item['Message_ID'] ?>'>
                                <div class='colline colline-4'><?= bc_input("variable[{$item['Message_ID']}][name]", $item['variablename'], "Название варианта") ?></div>
                                <div class='colline colline-2'><?= bc_text_standart($item['Message_ID'] == $message ? "<div class='this-variable-item'>Данный товар</div>" : "<a href='" . nc_message_link($item['Message_ID'], "2001", "edit") . "?isNaked=1&template=-1&tab=variable' title='Редактировать объект' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' class='edit-variable-item'>Редактировать товар</a>") ?></div>
                                <div class='colline colline-5'><?= bc_text_standart($item['Checked'] ? "<span class='green'>Включен</span>" : "Выключен") ?></div>
                            </div>
                        <?php  } ?>
                    </div>
                    <a href="" class="add-btn" onclick="add_line('variableItems'); return false;">добавить вариант</a>
                </div>
            <?php  } ?>
        <?php  } else { ?>
            <div class='colline colline-3'><?= bc_input("f_variablename", $f_variablename, "Название варианта", "maxlength='12' size='12'") ?></div>
        <?php  } ?>

        <div class="colblock">
            <h4><?= ($setting['typeSelectVariable'] == 1 ? "Вариант товара" : "Цвет товара") ?></h4>
            <div id='colors'>
                <?php
                if (count(json_decode($f_colors, 1)) > 0) {
                    foreach (json_decode($f_colors, 1) as $colorid => $colors) {
                        if ($colors['name']) $colorshtml .= "
                                <div class='multi-line' data-num='{$colorid}'>
                                    <div class='multi-inp' style='width:36%'>" . bc_input("colors[{$colorid}][name]", $colors['name'], "Название") . "</div>
                                    <div class='multi-inp' style='width:7%'>" . bc_color("colors[{$colorid}][code]", $colors['code']) . "</div>
                                    <div class='multi-inp' style='width:22%'>" . bc_input("colors[{$colorid}][price]", $colors['price'], "Цена") . "</div>
                                    <div class='multi-inp' style='width:19%'>" . bc_input("colors[{$colorid}][stock]", $colors['stock'], "Кол-во") . "</div>
                                    <div class='multi-inp' style='width:16%'>" . bc_input("colors[{$colorid}][photo]", $colors['photo'], "№ фото") . "</div>
                                </div>";
                    }
                }
                if (!$colorshtml) {
                    $colorshtml = "<div class='multi-line' data-num='0'>
                                        <div class='multi-inp' style='width:36%'>" . bc_input("colors[0][name]", "", "Название") . "</div>
                                        <div class='multi-inp' style='width:7%'>" . bc_color("colors[0][code]", "") . "</div>
                                        <div class='multi-inp' style='width:19%'>" . bc_input("colors[0][price]", "", "Цена") . "</div>
                                        <div class='multi-inp' style='width:19%'>" . bc_input("colors[0][stock]", "", "Кол-во") . "</div>
                                        <div class='multi-inp' style='width:16%'>" . bc_input("colors[0][photo]", "", "№ фото") . "</div>
                                    </div>";
                }
                echo $colorshtml;
                ?>
            </div>
            <a href="" class="add-btn" onclick="add_line('colors'); return false;">добавить еще</a>
        </div>
        <div class='colline colline-1'><?= bc_checkbox("f_buycolors", 1, "Обязательный выбор цвета товара", $f_buycolors) ?></div>
    </div>

    <div class='none' id='tab_prices'>
        <div class='colline colline-4'><?= bc_input("f_price", $f_price, "Цена (только число)", "maxlength='12' size='12'") ?></div>
        <div class='colline colline-4'><?= bc_input("f_price2", $f_price2, "Цена 2 (для авторизованных)", "maxlength='12' size='12'") ?></div>
        <div class='colline colline-4'><?= bc_input("f_price3", $f_price3, "Цена 3 (для авторизованных)", "maxlength='12' size='12'") ?></div>
        <div class='colline colline-4'><?= bc_input("f_price4", $f_price4, "Цена 4 (для авторизованных)", "maxlength='12' size='12'") ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_notmarkup", 1, "Не учитывать общую наценку", $f_notmarkup) ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_firstprice", 1, "Это нижняя граница цены товара (от)", $f_firstprice) ?></div>
        <div class='colline colline-3'><?= bc_checkbox("f_dogovor", 1, "Цена договорная<br>(число не показывается)", $f_dogovor) ?></div>
        <div class='colline colline-3'><?= bc_checkbox("f_torg", 1, "Возможен торг", $f_torg) ?></div>
        <?php
        $currencyArray = $db->get_results("select currency_ID as id, currency_Name as name from Classificator_currency WHERE Checked = 1 ORDER BY currency_Priority", ARRAY_A);
        foreach ($currencyArray as $v) $currencyItem[$v[id]] = $v[name];
        ?>
        <div class='colline colline-3 colline-currency'><?= bc_select("f_currency", getOptionsFromArray($currencyItem, $f_currency), "Валюта", "class='ns'") ?></div>
        <div class='colblock colblock-sale'>
            <h4>Скидка</h4>
            <div class='multi-colline'>
                <div class='colline colline-2'><?= bc_input("f_discont", $f_discont, "Скидка (в %)", "maxlength='12' size='12'") ?></div>
                <div class='colline colline-2'><?= bc_input("f_pricediscont", $f_pricediscont, "Цена со скидкой", "maxlength='12' size='12'") ?></div>
                <div class='colline colline-1'><?= bc_date('f_disconttime', $f_disconttime, "Скидка действует до:", 1, 1) ?></div>
                <div class='colline colline-3'><?= bc_checkbox("f_timer", 1, "Таймер обратного отсчета", $f_timer) ?></div>
            </div>
        </div>
        <div class='colheap colheap-double' data-jsopenmain='scrollVyvod'>
            <h4 data-jsopen='scrollVyvod'>Цена для кол-ва в корзине</h4>
            <div data-jsopenthis='scrollVyvod' class='none'>
                <?= bc_multi_line('f_order_count_price', $productObj ? $productObj->getOrderCountPrice() : []) ?>
            </div>
        </div>
    </div>
    <div class='none' id='tab_targeting'>
        <?php if ($setting['targeting']) if ($f_pricecity) $pricecity = orderArray($f_pricecity); ?>
        <?= nc_city_prices($pricecity) ?>
        <?= nc_city_field($f_citytarget) ?>
    </div>

    <div class='none' id='tab_vars'>
        <ul class="tabs tabs-border">
            <li class="tab"><a href="#other_param">Стандартные</a></li>
            <li class="tab"><a href="#tab_fields">Доп.поля</a></li>
        </ul>
        <div class="modal-body tabs-body">
            <div id='other_param'>
                <div class='colline colline-4'><?= bc_input("f_stock", $f_stock, "Наличие на складе", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_stock2", $f_stock2, "Наличие (склад 2)", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_stock3", $f_stock3, "Наличие (склад 3)", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_stock4", $f_stock4, "Наличие (склад 4)", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_code", $f_code, "Код во внешней системе", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_ves", $f_ves, "Вес", "maxlength='255' size='50'") ?></div>

                <div class='colline colline-4'><?
                                                if ($setting['lists_edizm']) {
                                                    foreach ($setting['lists_edizm'] as $e) {
                                                        $listsEdizm[$e['name']] = $e['name'];
                                                    }
                                                    echo bc_select("f_edizm", getOptionsFromArray($listsEdizm, $f_edizm), "Единица измерения", "class='ns'");
                                                } else {
                                                    echo bc_input("f_edizm", $f_edizm, "Единица измерения", "maxlength='255' size='50'");
                                                } ?>
                </div>

                <div class='colline colline-4'><?= bc_input("f_sizes_item", $f_sizes_item, "Размер", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_height", $f_height, "Высота", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_width", $f_width, "Ширина", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_depth", $f_depth, "Глубина", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-4'><?= bc_input("f_length", $f_length, "Длина", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-2'><?= bc_input("f_tags", $f_tags, "Тэги", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-2'><?= bc_input("f_extlink", $f_extlink, "Внешняя ссылка", "maxlength='255' size='50'") ?></div>
                <div class='colline colline-3'><?= bc_checkbox("f_nocart", 1, "Запретить добавлять<br>товар в корзину", $f_nocart) ?></div>
                <div class='colline colline-3'><?= bc_checkbox("f_noorder", 1, "Запретить заказывать товар", $f_noorder) ?></div>
                <div class='colline colline-3'><?= bc_checkbox("f_oneitem", 1, "Один товар в разделе<br>(вывести всю инф-ию)", $f_oneitem) ?></div>
                <? if (true) { ?>
                    <h4>Доп. разделы для вывода</h4>
                    <?php
                    $stuctureSubBuilder = new class($catalogue, $classID, $db, $f_Subdivision_IDS)
                    {
                        private $catalogue;
                        private $classID;
                        /** @var \nc_Db */
                        private $db;
                        private $subdivisionIds;

                        private $structure = [];

                        public function __construct($catalogue, $classID, $db, $f_Subdivision_IDS)
                        {
                            $this->catalogue = $catalogue;
                            $this->classID = $classID;
                            $this->db = $db;
                            $this->subdivisionIds = array_flip(explode(',', trim($f_Subdivision_IDS, ',')));
                        }

                        public function __invoke()
                        {
                            $this->structure = [];

                            $sql = "SELECT a.`Subdivision_ID`
                                        FROM `Subdivision` AS a
                                        WHERE a.`Catalogue_ID` = {$this->catalogue}
                                            AND EXISTS (SELECT * FROM `Sub_Class` WHERE `Class_ID` = {$this->classID} AND `Subdivision_ID` = a.`Subdivision_ID`)
                                            AND NOT EXISTS (SELECT * FROM `Subdivision` WHERE `Parent_Sub_ID` = a.`Subdivision_ID`)";


                            $this->fillStructure($this->getSub($sql));
                            $this->buildStructure();

                            echo "<div class='selected-list'>";
                            echo "</div>";
                            echo "<div class='sub-item-main'>";
                            echo "<input type='text' name='srch' placeholder='Поиск разделов'>";
                            echo "<div class='sub-item-items'>";
                            $this->print($this->structure);
                            echo "</div>";
                            echo "</div>";
                        }

                        private function print($subs)
                        {
                            uasort($subs, function ($a, $b) {
                                if ($a->Subdivision_Name > $b->Subdivision_Name) return 1;
                                if ($a->Subdivision_Name < $b->Subdivision_Name) return -1;
                                return 0;
                            });

                            foreach ($subs as $sub) {
                                echo "<div class='sub-item' data-name='" . mb_strtolower($sub->Subdivision_Name) . "'>";
                                if ($sub->Class_ID == $this->classID) {
                                    echo bc_checkbox("subs[]", $sub->Subdivision_ID, $sub->Subdivision_Name, isset($this->subdivisionIds[$sub->Subdivision_ID]));
                                } else {
                                    echo "<span style='display: inline-block; margin: 3px 0;'>{$sub->Subdivision_Name}</span>";
                                }

                                if (!empty($sub->children)) {
                                    echo "<div class='sub-item-child'>";
                                    echo $this->print($sub->children);
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }

                        private function getSub($subIn)
                        {
                            $sql = "SELECT sub.`Subdivision_ID`,
                                                sub.`Subdivision_Name`,
                                                sub.`Parent_Sub_ID`,
                                                cc.`Class_ID`
                                        FROM `Subdivision` AS sub
                                            INNER JOIN `Sub_Class` AS cc ON sub.`Subdivision_ID` = cc.`Subdivision_ID`
                                        WHERE sub.`Subdivision_ID` IN ({$subIn})
                                        GROUP BY sub.`Subdivision_ID`";

                            return $this->db->get_results($sql) ?: [];
                        }

                        private function fillStructure($subs)
                        {
                            foreach ($subs as $sub) {
                                $this->structure[$sub->Subdivision_ID] = $sub;

                                if ($sub->Parent_Sub_ID && !isset($this->structure[$sub->Parent_Sub_ID])) {
                                    $this->fillStructure($this->getSub($sub->Parent_Sub_ID));
                                }
                            }
                        }

                        private function buildStructure()
                        {
                            $children = [];
                            foreach ($this->structure as $sub) {
                                $sub->isChild = isset($this->structure[$sub->Parent_Sub_ID]);

                                if ($sub->isChild) {
                                    $children[] = $sub->Subdivision_ID;
                                    $this->structure[$sub->Parent_Sub_ID]->children[] = $sub;
                                }
                            }

                            foreach ($children as $subId) {
                                unset($this->structure[$subId]);
                            }
                        }
                    };
                    $stuctureSubBuilder();
                    ?>
                <? } else { ?>
                    <div class='colline colline-1'><?= bc_input("f_Subdivision_IDS", $f_Subdivision_IDS, "Доп. разделы для вывода (чере запятую)", "maxlength='255' size='50'") ?></div>
                <? } ?>
                <?php if (permission('monster')) : ?>

                    <h4>Тэги</h4>
                    <?php $tagProvider = new \App\modules\Korzilla\Tag\Provider(); ?>
                    <?php if ($tagList = $tagProvider->tagGetList()) : ?>
                        <?php
                        $objectTagList = [];
                        if (!empty($message)) {
                            $filter = $tagProvider->filterGet();
                            $filter->objectId[] = $message;
                            $filter->objectType[] = $classID;
                            $objectTagList = $tagProvider->tagGetList($filter);
                        }
                        ?>
                        <?php foreach ($tagList as $tag) : ?>
                            <div class='colline colline-5'>
                                <?= bc_checkbox("tag_list[{$tag->Message_ID}]", 1, $tag->tag, isset($objectTagList[$tag->Message_ID])) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="txt">Нет тэгов</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class='none' id='tab_fields'>
                <!-- <div class='colline colline-height'><?= bc_textarea("f_var1", $f_var1, ($setting['lists_texts']['var1']['checked'] ? $setting['lists_texts']['var1']['name'] : 'Параметр 1')) ?></div> -->
                <div class='colline colline-4'><?= bc_input("f_var2", $f_var2, ($setting['lists_texts']['var2']['checked'] ? $setting['lists_texts']['var2']['name'] : 'Параметр 2')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var3", $f_var3, ($setting['lists_texts']['var3']['checked'] ? $setting['lists_texts']['var3']['name'] : 'Параметр 3')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var4", $f_var4, ($setting['lists_texts']['var4']['checked'] ? $setting['lists_texts']['var4']['name'] : 'Параметр 4')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var5", $f_var5, ($setting['lists_texts']['var5']['checked'] ? $setting['lists_texts']['var5']['name'] : 'Параметр 5')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var6", $f_var6, ($setting['lists_texts']['var6']['checked'] ? $setting['lists_texts']['var6']['name'] : 'Параметр 6')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var7", $f_var7, ($setting['lists_texts']['var7']['checked'] ? $setting['lists_texts']['var7']['name'] : 'Параметр 7')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var8", $f_var8, ($setting['lists_texts']['var8']['checked'] ? $setting['lists_texts']['var8']['name'] : 'Параметр 8')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var9", $f_var9, ($setting['lists_texts']['var9']['checked'] ? $setting['lists_texts']['var9']['name'] : 'Параметр 9')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var10", $f_var10, ($setting['lists_texts']['var10']['checked'] ? $setting['lists_texts']['var10']['name'] : 'Параметр 10')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var11", $f_var11, ($setting['lists_texts']['var11']['checked'] ? $setting['lists_texts']['var11']['name'] : 'Параметр 11')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var12", $f_var12, ($setting['lists_texts']['var12']['checked'] ? $setting['lists_texts']['var12']['name'] : 'Параметр 12')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var13", $f_var13, ($setting['lists_texts']['var13']['checked'] ? $setting['lists_texts']['var13']['name'] : 'Параметр 13')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var14", $f_var14, ($setting['lists_texts']['var14']['checked'] ? $setting['lists_texts']['var14']['name'] : 'Параметр 14')) ?></div>
                <div class='colline colline-4'><?= bc_input("f_var15", $f_var15, ($setting['lists_texts']['var15']['checked'] ? $setting['lists_texts']['var15']['name'] : 'Параметр 15')) ?></div>
                <div class='colline colline-height'><?= bc_textarea("f_var1", $f_var1, ($setting['lists_texts']['var1']['checked'] ? $setting['lists_texts']['var1']['name'] : "Параметр 1"), "data-ckeditor='1'") ?></div>

                <div class="colblock colline-1">
                    <h4>Дополнительные характеристики товара</h4>

                    <div id='params'>
                        <?php
                        if (count($setting_params) > 0) {

                            if (trim($f_params)) {
                                if (stristr(trim($f_params), "\r\n") !== false) {
                                    $paramPara = explode("\r\n", trim($f_params));
                                } else {
                                    $paramPara = explode("| ", trim($f_params));
                                }
                                if (count($paramPara) > 0) {
                                    foreach ($paramPara as $paramRow) {
                                        $paramRow = explode("||", $paramRow);
                                        if ($paramRow[1]) $params[$paramRow[0]] = trim($paramRow[1], "|");
                                    }
                                }
                            }

                            foreach ($setting_params as $paramid => $paramname) {
                                if (trim($paramname[keyword])) $paramhtml .= "
									<div class='colline colline-4'><div class='input-field'>
										<input type='text' name='params[" . $paramname[keyword] . "]' value='" . $params[$paramname[keyword]] . "'>
										<label class='" . ($params[$paramname[keyword]] ? "active" : NULL) . "'>" . $paramname['name'] . "</label>
										<span></span>
									</div></div>";
                            }
                        }
                        echo $paramhtml;
                        ?>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class='none' id='tab_with'>
        <? if ($catalogue == 1057) : ?>
            <div class='colline colline-1 colline-height'>
                <div class="searchform products-life-search iconsCol">
                    <div class='searchform-inp'>
                        <input type='text' name='srch-products' placeholder='Поиск товаров'>
                    </div>
                </div>
                <div id="pls-s-products" style='margin-top:10px;' class="products-life-search-selected-products">
                    <h6 style='margin-bottom:5px; margin-top: 0px;'>Выбранные товары</h6>
                    <div class="selected-products-list">
                        <?php
                        if ($f_analogs_new) {
                            $html = "";
                            $analogs_decoded = json_decode($f_analogs_new);
                            $analogs_ids = implode(",", $analogs_decoded->products);

                            $analogs_list = $db->get_results("SELECT `Message_ID` as id, `name` FROM `Message2001` WHERE `Message_ID` IN ({$analogs_ids})");
                            foreach ($analogs_list as $analog) {
                                $html .= "<div class='selected-product'>
                                            <div class='icons i_del3'>
                                                <input type='hidden' value='{$analog->id}' name='analogs_new[products][]'>
                                            </div>
                                            <span style='left:25px;font-size:16px;' class='sw-text'>{$analog->name}</span>
                                        </div>";
                            }
                            echo $html;
                        }
                        ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
        <div class='colline colline-2 colline-height'><?= bc_textarea("f_analog", $f_analog, "Список артикулов аналог. товаров (по одному в строке)") ?></div>
        <div class='colline colline-2 colline-height'><?= bc_textarea("f_buywith", $f_buywith, "C этим товаром покупают (артикулы по одному в строке)") ?></div>

        <h4>Настройка блока "Вам также может понравиться"</h4>
        <div class='colline colline-3'>
            <div class='switch'>
                <label>
                    <input type='checkbox' value='all' <?= (strstr($f_otherItem, "all") ? "checked" : NULL) ?> name='otherItem[]'>
                    <span class='lever'></span>
                    <span class='sw-text'>Искать по всему каталогу</span>
                </label>
            </div>
        </div>
        <div class='colline colline-3'>
            <div class='switch'>
                <label>
                    <input type='checkbox' value='photo' <?= (strstr($f_otherItem, "photo") ? "checked" : NULL) ?> name='otherItem[]'>
                    <span class='lever'></span>
                    <span class='sw-text'>Только с фото</span>
                </label>
            </div>
        </div>

        <div class='colline colline-3'>
            <div class='switch'>
                <label>
                    <input type='checkbox' value='none' <?= (strstr($f_otherItem, "none") ? "checked" : NULL) ?> name='otherItem[]'>
                    <span class='lever'></span>
                    <span class='sw-text'>Отключить блок</span>
                </label>
            </div>
        </div>
        <?php
        $sql = "SELECT sub.`Subdivision_ID` AS sub, sub.`Subdivision_Name` AS subName";
        $sql .= ", obj.`Message_ID` AS id, obj.`name`";
        $sql .= " FROM `Subdivision` AS sub";
        $sql .= " INNER JOIN `Message2021` AS obj ON sub.`Subdivision_ID` = obj.`Subdivision_ID`";
        $sql .= " WHERE sub.`Catalogue_ID` = {$catalogue}";
        $sql .= " ORDER BY sub.`Subdivision_ID`";

        $portfolioItems = $db->get_results($sql, ARRAY_A);

        $outItems = orderArray($f_outItems);
        $lastSub = null;
        ?>
        <?php if (is_array($portfolioItems) && count($portfolioItems)) : ?>
            <div class='colheap colheap-double' data-jsopenmain='scrollVyvod'>
                <h4 data-jsopen='scrollVyvod'>Портфолио</h4>
                <div data-jsopenthis='scrollVyvod' class='none'>
                    <?php foreach ($portfolioItems as $item) : ?>
                        <?php if ($lastSub != $item['sub']) : ?>
                            <?php if ($lastSub !== null) : ?>
                </div>
            <?php endif; ?>
            <?php $lastSub = $item['sub']; ?>
            <div class="out-items" data-jsopenmain="out-open-<?= $item['sub'] ?>">
                <h4><?= $item['subName'] ?></h4>
            <?php endif; ?>
            <div class='colline colline-3 <?= $outItems['portfolio'][$item['Message_ID']] ?>'>
                <?= bc_checkbox("outItems[portfolio][]", $item['id'], $item['name'], in_array($item['id'], $outItems['portfolio'])) ?>
            </div>
        <?php endforeach; ?>
            </div>
            </div>
    </div>
<?php endif; ?>
<?php
$sql = "SELECT sub.`Subdivision_ID` AS sub, sub.`Subdivision_Name` AS subName";
$sql .= ", obj.`Message_ID` AS id, obj.`name`";
$sql .= " FROM `Subdivision` AS sub";
$sql .= " INNER JOIN `Message2009` AS obj ON sub.`Subdivision_ID` = obj.`Subdivision_ID`";
$sql .= " WHERE sub.`Catalogue_ID` = {$catalogue}";
$sql .= " ORDER BY sub.`Subdivision_ID`";

$documents = $db->get_results($sql, ARRAY_A);

$lastSub = null;
?>
<?php if (is_array($documents) && count($documents)) : ?>
    <div class='colheap documents colheap-double' data-jsopenmain='scrollVyvod'>
        <h4 data-jsopen='scrollVyvod'>Документы</h4>
        <div data-jsopenthis='scrollVyvod' class='none'>
            <?php foreach ($documents as $item) : ?>
                <?php if ($lastSub != $item['sub']) : ?>
                    <?php if ($lastSub !== null) : ?>
        </div>
    <?php endif; ?>
    <?php $lastSub = $item['sub']; ?>
    <div class="out-items" data-jsopenmain="out-open-<?= $item['sub'] ?>">
        <h4><?= $item['subName'] ?></h4>
    <?php endif; ?>
    <div class='colline colline-3 <?= $outItems['documents'][$item['Message_ID']] ?>'>
        <?= bc_checkbox("outItems[documents][]", $item['id'], $item['name'], in_array($item['id'], $outItems['documents'])) ?>
    </div>
<?php endforeach; ?>
    </div>
    </div>
</div>
<?php endif; ?>
</div>
<?= editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID, '', $f_lang) ?>
</div>