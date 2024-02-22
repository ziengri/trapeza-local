<?php

$catsubArr = $db->get_results("select a.Subdivision_Name as subname, a.Subdivision_ID as subid, a.Hidden_URL as psubid,  a.Checked
    from Subdivision as a, Sub_Class as b
    where a.Subdivision_ID = b.Subdivision_ID
        AND a.Catalogue_ID = '$catalogue'
        AND a.Hidden_URL NOT LIKE '%search%'
        AND a.Hidden_URL NOT LIKE '%404%'
        AND a.Hidden_URL NOT LIKE '%zone%'
        AND a.Hidden_URL NOT LIKE '%blockofsite%'
        AND a.Hidden_URL NOT LIKE '%sitemap%'
        AND a.Hidden_URL NOT LIKE '%excel%'
        AND a.Hidden_URL NOT LIKE '%profile%'
        AND a.Hidden_URL NOT LIKE '%settings%'
        AND a.Hidden_URL NOT LIKE '%cart/success%'
        AND a.Hidden_URL NOT LIKE '%cart/fail%'
        ORDER BY a.Hidden_URL, a.Priority", ARRAY_A);

$block_id = (int)$db->get_var("SELECT block_id+1 FROM Message2016 where Catalogue_ID = '{$catalogue}' ORDER BY block_id DESC LIMIT 1");

$catsubOpt = "<option value=''>- не выбран -</option>";
$catsubOpt .= "<option ".(!$f_sub || $f_sub==1 ? "selected" : "")." value='0'>0. Корневой раздел</option>";
foreach($catsubArr as $cs) { $o = '';
    $c = substr_count($cs[psubid],"/")-2;
    for($i=1;$i<=$c;$i++) {
        $o .= "-";
    }
    $catsubOpt .= "<option ".($cs[subid]==$f_sub ? "selected" : NULL)." value='{$cs[subid]}'>{$o} {$cs[subid]}. {$cs[subname]}</option>";
}
# END Разделы (список)


if ($f_settings) $settings = orderArray($f_settings);
if ($f_phpset) $phpset = orderArray($f_phpset);


# список разделов, где показывать / не показывать

if ($f_insub) $insub = substr( substr($f_insub, 0, -1), 1);
if ($f_noinsub) $noinsub = substr( substr($f_noinsub, 0, -1), 1);

# ТИП контента
$typeArray = array(
    "0"=> "Текст",
    "1"=> "Вывод из раздела",
    "2"=> "Список разделов (меню)",
    "3"=> "Контактная информация",
    "4"=> "Copyright",
    "5"=> "Создатель сайта",
    "6"=> checkModule('check') ? "Модули" : "",
    "7"=> "Хлебные крошки"
);
$contentSelect = getOptionsFromArray($typeArray, $phpset['contenttype']);


# шаблон компонента
if ($f_cc) $classidd = $db->get_var("select Class_ID from Sub_Class where Sub_Class_ID = '$f_cc'");

$ShowingBlocksDB = $db->get_results("SELECT Subdivision_ID, extends FROM Showing_Blocks WHERE Block_ID = {$message}", ARRAY_A);

$showingBlocks = [];
foreach ($ShowingBlocksDB as $block) {
    $showingBlocks[$block['Subdivision_ID']] = $block['extends'];
}

?>

<input type='hidden' name='f_block_id' value='<?=($f_block_id ? $f_block_id : $block_id)?>'>
<input name="f_col" class='none' value="<?=($f_col ? $f_col : 0)?>" type="hidden">
<input name="f_width" class='none' value="<?=($f_width ? $f_width : 9)?>" type="hidden">

<ul class="tabs tabs-border">
    <?=(!$f_sys ? "<li class='tab'><a href='#tab_content'>Содержимое</a></li>" : NULL)?>
    <li class="tab"><a href="#tab_text">Контент<?=$dirr?></a></li>
    <?php  if($setting['language']){ ?> <li class='tab'><a href='#tab_lang'>Язык вывода</a></li> <?php  } ?>
    <li class='tab <?=(permission("design") ? "" : "none-important")?>'><a href='#tab_zag'>Дизайн</a></li>
    <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <li class='tab <?=(permission("design") ? "" : "none-important")?>'><a href='#tab_system'>Системное</a></li>
</ul>

<div class="modal-body tabs-body">
    <?php  if (!$f_sys) { ?>
        <div id='tab_content'>
            <div class='colline colline-3 colline-name'><?=bc_input("f_name", $f_name, "Заголовок блока", "maxlength='255' size='50'", 1)?></div>
           <div class='colline colline-3 colline-contenttype'><?=bc_select("phpset[contenttype]", $contentSelect, "Содержимое блока", "class='ns'")?></div>
            <div class='colline colline-3 colline-sub setgroup1 setgroup2 <?=($phpset['contenttype']!=1 && $phpset['contenttype']!=2? "none" : "")?>'><?=bc_select("f_sub", $catsubOpt, "Раздела для вывода информации", "class='ns'")?></div>


            <div id='classSettings' data-type="<?=$phpset['contenttype']?>" data-sub="<?=$f_sub?>" data-typecontcur="<?=$phpset['contenttype']?>_<?=$f_sub?>">
                <?=setClassBlock(array_merge($phpset['contsetclass'] ?? [], $settings), $classidd, $phpset['contenttype'])?>
                <?php /*print_r(array_merge($phpset['contsetclass'],$settings),1)*/?>
            </div>
            <div class="colheap setgroup1 <?=($phpset['contenttype']!=1 ? "none" : "")?>" data-jsopenmain='scrollVyvod'>
                <h4 data-jsopen='scrollVyvod'>Настройки вывода объектов</h4>
                <div data-jsopenthis='scrollVyvod' class='none'>
                    <div class='colline colline-2 colline-recnum'><?=bc_input("f_recnum", ($f_recnum ? $f_recnum : 8), "Количество объектов", "max=500 min=1", 1)?></div>
                    <div class='colline colline-2 colline-rand'><?=bc_checkbox("f_rand", 1, "Сортировка случайным образом", $f_rand)?></div>
                    <div class='colline colline-2 colline-substr'><?=bc_input("f_substr", $f_substr, "Количество символов текста", "maxlength='12' size='12'")?></div>
                    <div class='colline colline-2 colline-msg'><?=bc_input("f_msg", $f_msg, "Запись в разделе №", "maxlength='12' size='12'")?></div>
                </div>
            </div>

        </div>
    <?php  } ?>

    <div class='none' id='tab_zag'>
        <div class='colheap' data-jsopenmain='blkHead'>
            <h4 data-jsopen='blkHead'>Заголовок</h4>
            <div class='miniright'><?=bc_checkbox("saveblk[head]", 1, "Применить ко всем блокам в зоне", "", "", 0, "mark")?></div>
            <div data-jsopenthis='blkHead' class='none'>
                <div class='colline colline-4 colline-notitle' data-jsopen="head"><?=bc_checkbox("f_notitle", 1, "Скрыть заголовок", $f_notitle)?></div>
                <div class='colline colline-4 colline-nolink'><?=bc_checkbox("f_nolink", 1, "Скрыть ссылку<br>на раздел", $f_nolink, "class='switch-twoline'")?></div>
                <div class='colline colline-4 colline-headupper'><?=bc_checkbox("settings[headupper]", 1, "Все буквы<br><div class='u'>заглавные</div>", $settings[headupper], "class='switch-twoline'")?></div>
                <div class='colline colline-4 colline-headbold'><?=bc_checkbox("settings[headbold]", 1, "Заголовок<br><b>Жирный</b>", $settings[headbold], "class='switch-twoline'")?></div>
                <div class='colline colline-5 colline-floathead'><?=bc_align("settings[floathead]", $settings['floathead'], "Выравнивание заголовка", "data-head")?></div>
                <div class='colline colline-5 colline-headsize'><?=bc_input("settings[headsize]", $settings[headsize], "Размер (px)")?></div>
                <div class='colline colline-5 colline-headcolor'><?=bc_color("settings[headcolor]", $settings['headcolor'], "Цвет <span class='color-active'>текста</span> заголовка", "data-twoline")?></div>
                <div class='colline colline-5 colline-headbg'><?=bc_color("settings[headbg]", $settings['headbg'], "Фон заголовка", "data-twoline")?></div>
            </div>
        </div>


        <div class='colheap' data-jsopenmain='blkText'>
            <h4 data-jsopen='blkText'>Текст в блоке</h4>
            <div class='miniright'><?=bc_checkbox("saveblk[body]", 1, "Применить ко всем блокам в зоне", "", "", 0, "mark")?></div>
            <div data-jsopenthis='blkText' class='none'>
                <div class='colline colline-5 colline-floatbody nosetgroup2 <?=($phpset[contenttype]==2 ? "none" : "")?>'><?=bc_align("settings[floatbody]", $settings['floatbody'], "Выравнивание текста")?></div>
                <div class='colline colline-5 colline-menuFontSize setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_input("settings[menuFontSize]", ($settings[menuFontSize]?$settings[menuFontSize]:14), "Размер текста меню")?></div>
                <div class='colline colline-5 colline-fontcolor nosetgroup2 <?=($phpset[contenttype]==2 ? "none" : "")?>'><?=bc_color("settings[fontcolor]", $settings['fontcolor'], "Цвет текста")?></div>
                <div class='colline colline-5 colline-linkcolor nosetgroup2 <?=($phpset[contenttype]==2 ? "none" : "")?>'><?=bc_color("settings[linkcolor]", $settings['linkcolor'], "Цвет ссылок")?></div>
                <div class='colline colline-5 colline-iconcolor nosetgroup2 <?=($phpset[contenttype]==2 ? "none" : "")?>'><?=bc_color("settings[iconcolor]", $settings['iconcolor'], "Цвет иконок")?></div>
                <div class='colline colline-5 colline-namefont setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_select("settings[namefont]", getFonts($settings['namefont'], null, 1), "Шрифт меню", "class='ns'")?></div>
                <div class='colline colline-2 colline-MenuUppercase setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_checkbox("settings[MenuUppercase]", 1, "Использовать заглавные буквы", $settings[MenuUppercase])?></div>
                <div class='colline colline-2 colline-MenuColor setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_color("settings[MenuColor]", $settings['MenuColor'], "Цвет текста неактивного пункта")?></div>
                <div class='colline colline-2 colline-MenuColorActive setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_color("settings[MenuColorActive]", $settings['MenuColorActive'], "Цвет текста активного пункта")?></div>
            </div>
        </div>

        <div class='colheap' data-jsopenmain='blkBack'>
            <h4 data-jsopen='blkBack'>Фон <span class='nosetgroup2 <?=($phpset[contenttype]==2 ? "none" : "")?>'>блока</span><span class='setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'>меню</span></h4>
            <div class='miniright'><?=bc_checkbox("saveblk[background]", 1, "Применить ко всем блокам в зоне", "", "", 0, "mark")?></div>
            <div data-jsopenthis='blkBack' class='none'>
                <div class='colline colline-4 colline-bg'><?=bc_color("settings[bg]", $settings['bg'], "Цвет фона <span class='setgroup2 ".($phpset[contenttype]!=2 ? "none" : "")."'>меню</span>")?></div>
                <div class='colline colline-2 colline-bgimg'><?=bc_file('f_bgimg', $f_bgimg_old, "Фоновое изображение", $f_bgimg, 2533)?></div>
                <div class='colline colline-4 colline-bgimgpos'><?=bc_select("settings[bgimgpos]", position_img($settings[bgimgpos]), "Положение изображения", "class='ns'")?></div>
                <div class='colline colline-1 colline-menuBgActive setgroup2 <?=($phpset[contenttype]!=2 ? "none" : "")?>'><?=bc_color("settings[menuBgActive]", $settings['menuBgActive'], "Цвет фона активного пункта")?></div>
            </div>
        </div>

        <div class='colheap' data-jsopenmain='blkBorder'>
            <h4 data-jsopen='blkBorder'>Граница</h4>
            <div class='miniright'><?=bc_checkbox("saveblk[border]", 1, "Применить ко всем блокам в зоне", "", "", 0, "mark")?></div>
            <div data-jsopenthis='blkBorder' class='none'>
                <div class='colline colline-4 colline-borderwidth'><?=bc_input("settings[borderwidth]", ($settings[borderwidth] ? $settings[borderwidth] : 0), "Толщина границы")?></div>
                <div class='colline colline-4 colline-radius'><?=bc_input("settings[radius]", ($settings[radius] ? $settings[radius] : 0), "Скругление")?></div>
                <div class='colline colline-4 colline-bordercolor'><?=bc_color("settings[bordercolor]", $settings['bordercolor'], "Цвет границы")?></div>
                <div class='colline colline-4 colline-padding'><?=bc_checkbox("f_padding", 1, "Отключить внутренние отступы", $f_padding, "class='switch-twoline'")?></div>
            </div>
        </div>
    </div>

    <div class='none' id='tab_text'>
        <div class='colline colline-height colline-textblk'><?=bc_textarea("f_text", $f_text, "Текст", "data-ckeditor='1'")?></div>
    </div>
    <div class='none' id='tab_lang'>
        <?=nc_lang_field($f_lang)?>
    </div>
    <div id='tab_targeting' class='none'>
        <?=nc_city_field($f_citytarget); ?>
    </div>


    <div class='none' id='tab_system'>
        <div class='colline colline-2 colline-fixblock'><?=bc_checkbox("f_fixblock", 1, "Закрепить блок на всех страницах", $f_fixblock)?></div>
        <div class='colheap' data-jsopenmain='mapSub'>
            <h4 data-jsopen='mapSub' data-fixblock='<?=$f_fixblock?>'></h4>
            <div data-jsopenthis='mapSub' class='none'>
                <div class='colline colline-height colline-map'><?= siteMapExtendetList(getMapSubdivision(), $showingBlocks) ?></div>
            </div>
        </div>
        <div class='colline colline-2 colline-noinde'><?=bc_checkbox("f_noindex", 1, "Запретить индексацию блока (noindex)", $f_noindex)?></div>
        <div class='colline colline-3 colline-bottmarg'><?=bc_checkbox("settings[bottmarg]", 1, "Убрать отступ снизу", $settings['bottmarg'])?></div>
        <div class='colline colline-3 colline-cssclass'><?=bc_input("f_cssclass", $f_cssclass, "CSS классы", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3 colline-heightpx'><?=bc_input("f_height", $f_height, "Высота блока (в пикселях)")?></div>

        <div class='colline colline-height colline-inmob'>
            <div class='radio-name'><?=LNG_visibleblock_BL?></div>
            <?=bc_radio('f_inmob', '0', LNG_visibleblock1_BL, $f_inmob == '0' || !$f_inmob)?>
            <?=bc_radio('f_inmob', '1', LNG_visibleblock2_BL, $f_inmob == '1')?>
            <?=bc_radio('f_inmob', '2', LNG_visibleblock3_BL, $f_inmob == '2')?>
			<?=bc_radio('f_inmob', '3', LNG_visibleblock4_BL, $f_inmob == '3')?>
			<?=bc_radio('f_inmob', '4', LNG_visibleblock5_BL, $f_inmob == '4')?>
        </div>

        <div class='colline colline-3 colline-iscache'><?=bc_checkbox("f_iscache", 1, "Кэшировать блок", $f_iscache)?></div>
        <div class='colline colline-3 colline-cachetime'><?=bc_input("f_cachetime", $f_cachetime, "Актуальность кэша (в минутах)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3 colline-iscache'><?=bc_checkbox("f_no_in_full_obj", 1, "Не выводить внутри объекта", $f_no_in_full_obj)?></div>
        <div class='colline colline-2 colline-insub'><?=bc_input("f_insub", $insub, "Доступен только на страницах (номера через запятую)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2 colline-noinsub'><?=bc_input("f_noinsub", $noinsub, "Не доступен на страницах (номера через запятую)", "maxlength='255' size='50'")?></div>

        <?=($current_user['PermissionGroup_ID']==1 ? "
            <div class='colline colline-2 colline-vars'>".bc_input("f_vars", $f_vars, "Дополнительные параметры", "maxlength='255' size='50'")."</div>
            <div class='colline colline-2 colline-sys'>".bc_input("f_sys", $f_sys, "sys")."</div>
        " : "")?>
    </div>
</div>
<script>
    document.querySelector('input[name="f_fixblock"]').addEventListener('click', el => {
        document.querySelector('h4[data-fixblock]').setAttribute('data-fixblock', Number(el.target.checked));
    })
    initSubMapList();
</script>