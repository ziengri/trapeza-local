<?php

global $altTexts, $setting;

$whiteListFields = [
    "'new'",          // Новинки
    "'action'",       // Хит продаж
    "'spec'",         // Спецпредложение
    "'var1'",         // Параметр 1
    "'var2'",         // Параметр 2
    "'var3'",         // Параметр 3
    "'var4'",         // Параметр 4
    "'var5'",         // Параметр 5
    "'var6'",         // Параметр 6
    "'var7'",         // Параметр 7
    "'var8'",         // Параметр 8
    "'var9'",         // Параметр 9
    "'var10'",        // Параметр 10
    "'var11'",        // Параметр 11
    "'var12'",        // Параметр 12
    "'var13'",        // Параметр 13
    "'var14'",        // Параметр 14
    "'var15'",        // Параметр 15
    "'stock'",        // Наличие на складе (шт.)
    "'stock4'",       // Наличие на складе №4 (шт.)
    "'stock3'",       // Наличие на складе №3 (шт.)
    "'stock2'",       // Наличие на складе №2 (шт.)
    // "'torg'",         // Возможен торг
    // "'dogovor'",      // Цена договорная (число не показывается)
    "'rate'",         // Рейтинг
    // "'ratecount'",    // Количество оценок
    "'price'",        // Цена (только число)
    "'price2'",       // Цена 2 (только число)
    "'price3'",       // Цена 3 (только число)
    "'price4'",       // Цена 4 (только число)
    "'vendor'",       // Производитель
    "'length'",       // длина
    "'height'",       // Высота
    "'width'",        // Ширина
    "'depth'",        // Глубина
    "'ves'",          // Вес
    "'capacity'",     // Объем
    "'sizes_item'",   // Размер
    "'edizm'",        // Единица измерения
];

$allField = $db->get_results(
    "SELECT 
            Field_ID, 
            Field_Name, 
            Description 
        FROM 
            Field 
        WHERE 
            Class_ID = 2001 
            AND Checked = 1
            AND Field_Name IN (" . implode(',', $whiteListFields) . ")",
    ARRAY_A
);

$dopParamsItems = $setting['lists_params'];

foreach ($dopParamsItems as $param) {
    $allField[] = [
        'Field_ID' => $param['keyword'], 
        'Field_Name' => $param['keyword'], 
        'Description' => $param['name']
    ];
}

if ($allField) {

    $arr = orderArray($f_data);
    # сортировка включенные->приоритет
    uasort($allField, function ($a, $b) use ($arr, $altTexts) {
        if (isset($arr['checked'][$a['Field_ID']]) && isset($arr['checked'][$b['Field_ID']])) {
            return $arr['priority'][$a['Field_ID']] - $arr['priority'][$b['Field_ID']];
        } elseif (isset($arr['checked'][$a['Field_ID']])) return false;
        elseif (isset($arr['checked'][$b['Field_ID']])) return true;
        else {
            $nameA = $altTexts[$a['Field_Name']] ?: $a['Description'];
            $nameB = $altTexts[$b['Field_Name']] ?: $b['Description'];
            return $nameA > $nameB;
        }
    });

    foreach ($allField as $f) {
        $ii++;
        $fid = $f['Field_ID'];
        $fname = $f['Field_Name'];

        $selectdata = "<option value=''>-- не выбрано --</option>
            <option value='1' " . ($arr['view'][$fid] == 1 ? "selected" : null) . ">варианты в списке</option>
            <option value='2' " . ($arr['view'][$fid] == 2 ? "selected" : null) . ">варианты галочками</option>
            <option value='3' " . ($arr['view'][$fid] == 3 ? "selected" : null) . ">есть/нет (одна галочка)</option>";

        $options .= "<div class='multi-line' data-num='{$ii}'>
                <div class='multi-inp multi-inp-on' style='width:100%'>" . bc_checkbox("field[checked][{$fid}]", $fid, ($altTexts[$fname] ? $altTexts[$fname] : $f['Description']), ($arr['checked'][$fid] ? "checked" : ""), "class='bold'") . "</div>
                <div class='multi-inp-onbody " . (!$arr['checked'][$fid] ? "none" : "") . "'>
                    <div class='multi-inp' style='width:28%'>" . bc_input("field[name][{$fid}]", ($arr['name'][$fid] ? $arr['name'][$fid] : ($altTexts[$fname] ? $altTexts[$fname] : $f['Description'])), "Показывать как", "size=50") . "</div>
                    <div class='multi-inp' style='width:20%'>" . bc_select("field[view][{$fid}]", $selectdata, "Вид", "class='ns'") . "</div>
                    <div class='multi-inp' style='width:10%'>" . bc_input("fieldTmp[priority][{$fid}]", ($arr['priority'][$fid] ? $arr['priority'][$fid] : $ii), "Приоритет", "size='4'") . "</div>
                    <div class='multi-inp' style='width:17%'>" . bc_checkbox("field[otdo][{$fid}]", 1, "2 поля (от-до)", $arr['otdo'][$fid]) . "</div>
					<div class='multi-inp' style='width:15%'>" . bc_checkbox("field[minimized][{$fid}]", 1, "Свернутый", $arr['minimized'][$fid]) . "</div>
                </div>
            </div>";
    }
}
$fields = "<div class='multi-lines'>
                <div class='multi-body'>{$options}</div>
            </div>";
?>
<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_settings">Настройки</a></li>
    <li class="tab"><a href="#tab_fields">Поля</a></li>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_settings'>
        <div class='colline colline-1 <?= $catalogue ?>'><?= bc_input("f_name", $f_name, "Наименование фильтра", "", 1) ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_namefieldbool", 1, "Выводить название полей", $f_namefieldbool) ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_hide_bigfilter", 1, "Скрыть фильтр (не полностью)", $f_hide_bigfilter) ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_file_newline", 1, "Выводить параметры с новой строки", $f_file_newline) ?></div>
        <div class='colline colline-2'><?= bc_checkbox("f_live_count", 1, "Живой подсчет", $f_live_count) ?></div>
        <div class='colline colline-1'><?= bc_checkbox("f_adaptiv", 1, "Учитывать значения других выбранных параметров", $f_adaptiv) ?></div>
        <div class='colline colline-1'><?= bc_checkbox("f_one_sub_mode", 1, "Сбор параметров только из объектов текущего раздела", $f_one_sub_mode) ?></div>
        <div class='colline colline-1'><?= bc_checkbox("f_ajax_filter", 1, "Ajax обновления", $f_ajax_filter) ?></div>
        <div class='colline colline-2'><?= bc_input("f_button", $f_button, "Надпись на кнопке", "maxlength='255' size='50'") ?></div>
        <div class='colline colline-height'><?= bc_textarea("f_subs", $f_subs, "Поиск только по разделам № (через запятую, без вложенных разделов)") ?></div>
    </div>
    <div id='tab_fields' class="none">
        <?= $fields ?>
    </div>
</div>