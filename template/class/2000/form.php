<?php 
    $prior = (int)$db->get_var("SELECT zone_priority FROM Message2000 where Catalogue_ID = '{$catalogue}' AND zone_position = 1 ORDER BY zone_priority DESC LIMIT 1"); 
    $prior = (is_numeric($prior) ? $prior+1 : 1);
    $zone_id = (int)$db->get_var("SELECT zone_id+1 FROM Message2000 where Catalogue_ID = '{$catalogue}' ORDER BY zone_id DESC LIMIT 1");  

    if($f_setting) $setzone = orderArray($f_setting);
?>

<div id='formcontent'>
    <input type='hidden' name='f_zone_id' value='<?=($f_zone_id ? $f_zone_id : $zone_id)?>'>
    <input type='hidden' name='f_zone_position' value='<?=($f_zone_position ? $f_zone_position : 1)?>'>
    <input type='hidden' name='f_zone_width' value='<?=($f_zone_width ? $f_zone_width : 1)?>'>


    <ul class="tabs tabs-border">
        <li class="tab"><a href="#tab_main">Основное</a></li>
        <li class="tab"><a href="#tab_design">Дизайн</a></li>
        <?=($f_zone_position!=6 ? "<li class='tab'><a href='#tab_dop'>Доп. настройки</a></li>" : "")?>
    </ul>
    <div class="modal-body tabs-body">
        <div id='tab_main'>
            <div class='colline colline-3'><?=bc_input("f_name", $f_name, "Наименование зоны", "maxlength='255' size='50'", 1)?></div>
            <?=($f_zone_position!=6
                ? "<div class='colline colline-3'>".bc_checkbox("settingzone[width]", 1, "Стакан контента зоны", $setzone[width])."</div>"
                : "<div class='colline colline-3'>".bc_checkbox("f_mobilemenu", 1, "Только в моб. версии", $f_mobilemenu)."</div>")?>

            <!--<div class='bc_setrow' style='display: block;'>
                <span style='' id='nc_capfld_2414'>Ширина зоны:</span>
                <div class="addz-blks">
                    <label class='addz-blk addz-blk-1'>
                        <input name='settingzone[width]' type='radio' max='500' <?=($setzone[width] == 1 || !$setzone[width] ? "checked" : NULL)?> value='1'>
                        <div class='addz-text'><span>100%</span><div class="addz-img"></div></div>
                    </label>
                    <label class='addz-blk addz-blk-2'>
                        <input name='settingzone[width]' type='radio' max='500' <?=($setzone[width] == 2 ? "checked" : NULL)?> value='2'>
                        <div class='addz-text'><span>Стакан</span><div class="addz-img"></div></div>
                    </label>
                    <label class='addz-blk addz-blk-3'>
                        <input name='settingzone[width]' type='radio' max='500' <?=($setzone[width] == 3 ? "checked" : NULL)?> value='3'>
                        <div class='addz-text'><span>Стакан</span><div class="addz-img"></div></div>
                    </label>
                </div>
            </div>-->
            <!--<div class='bc_setrow'>
                <div class='bc_setname'>Ширина зоны (длина)</div>
                <div class='bc_setvalue'><input name='f_zone_width' maxlength='12' size='12' type='number' max='500' min='1' value='<?=($f_zone_width ? $f_zone_width : 12 )?>'></div>
            </div>-->
            <?php if($f_zone_priority){?>
                <!--<div class='colline colline-3'><?=bc_input("f_zone_priority", $f_zone_priority, "Приоритет", "maxlength='255' size='50'", 1)?></div>-->
            <?php }?>
            <?php if($f_zone_position){?>
                <!--<div class='bc_setrow'>
                    <div class='bc_setname'>Позиция зоны</div>
                    <div class='bc_setvalue'><input name='f_zone_position' maxlength='12' size='12' type='number' max='500' min='1' value='<?=$f_zone_position?>'></div>
                </div>-->
            <?php }?>
        </div>
        <div class='none' id='tab_design'>
            <div class="colblock">
                <h4>Текст в зоне</h4>
                <div class='colline colline-3'><?=bc_color("settingzone[textcolor]", $setzone['textcolor'], "Цвет текста")?></div>
                <div class='colline colline-3'><?=bc_color("settingzone[linkcolor]", $setzone['linkcolor'], "Цвет ссылок")?></div>
                <div class='colline colline-3'><?=bc_color("settingzone[iconcolor]", $setzone['iconcolor'], "Цвет иконок")?></div>
            </div>
            <div class="colblock">
                <h4>Фон</h4>
                <div class='colline colline-2'><?=bc_color("settingzone[bgcolor]", $setzone['bgcolor'], "Цвет фона")?></div>
                <div class='colline colline-2'><?=bc_file('f_bgimg', $f_bgimg_old, "Фон зоны", $f_bgimg, 2811)?></div>
                <div class='colline colline-3'><?=bc_select("settingzone[bgimgpos]", position_img($setzone[bgimgpos]), "Положение изображения", "class='ns'")?></div>
                <div class='colline colline-3'><?=bc_checkbox("settingzone[fixed]", 1, "Фиксировать картинку", $setzone[fixed])?></div>
                <div class='colline colline-3'><?=bc_checkbox("settingzone[parallaxZone]", 1, "Параллакс", $setzone[parallaxZone])?></div>
                <div class='colline colline-3'><?=bc_checkbox("settingzone[fixwidth]", 1, "Фиксированная<br>ширина (стакан)", $setzone[fixwidth])?></div>
            </div>
            <div class="colblock">
                <h4>Высота и отступы</h4>
                <div class='colline colline-2'><?=bc_input("settingzone[height]", $setzone[height], "Мин. высота зоны")?></div>
                <div class='colline colline-2'><?=bc_checkbox("settingzone[fixheight]", 1, "Фиксированная высота", $setzone[fixheight])?></div>
                <div class='colline colline-2'><?=bc_input("settingzone[mrgn_top]", $setzone[mrgn_top], "margin-top")?></div>
                <div class='colline colline-2'><?=bc_input("settingzone[mrgn_bottom]", $setzone[mrgn_bottom], "margin-bottom")?></div>
                <div class='colline colline-2'><?=bc_input("settingzone[padd_top]", $setzone[padd_top], "padding-top")?></div>
                <div class='colline colline-2'><?=bc_input("settingzone[padd_bottom]", $setzone[padd_bottom], "padding-bottom")?></div>
                <div class='colline colline-2'><?=bc_checkbox("settingzone[blkmarginbot0]", 1, "Все вложенные блоки margin-bottom: 0", $setzone[blkmarginbot0])?></div>
                <div class='colline colline-2'><?=bc_checkbox("settingzone[blkvertmid]", 1, "Все вложенные блоки vertical-align: middle", $setzone[blkvertmid])?></div>
            </div>
        </div>
        <div class='none' id='tab_dop'>
            <div class='colline colline-2'><?=bc_checkbox("settingzone[header]", 1, "Header", $setzone[header])?></div>
            <div class='colline colline-2'><?=bc_checkbox("settingzone[footer]", 1, "Footer", $setzone[footer])?></div>
            <div class='colline colline-2'><?=bc_checkbox("settingzone[fixedZone]", 1, "Зафиксировать при прокрутке", $setzone[fixedZone])?></div>
            <div class='colline colline-5'><?=bc_align("settingzone[alignblocks]", $setzone['alignblocks'], "Выравнивание блоков")?></div>
        </div>
    </div>
</div>