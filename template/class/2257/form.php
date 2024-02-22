<?php 
$catsubArr = $db->get_results("select a.Subdivision_Name as subname, a.Subdivision_ID as subid, a.Hidden_URL as psubid,  a.Checked from Subdivision as a, Sub_Class as b where a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 2001 AND a.Catalogue_ID = '$catalogue' AND a.Hidden_URL NOT LIKE '%search%' ORDER BY a.Hidden_URL, a.Priority", ARRAY_A);
foreach($catsubArr as $cs) { $o = '';
	$c = substr_count($cs['psubid'],"/")-2;
	for($i=1;$i<=$c;$i++) {
		$o .= "-";
	}
	$catsubOpt .= "<option ".($cs['subid']==$f_parent ? "selected" : NULL)." value='{$cs['subid']}'>{$o} {$cs['subid']}. {$cs['subname']}</option>";
}
$catsubOpt = "<option value=''>- не выбран -</option>".$catsubOpt;

$currency = $db->get_results("SELECT currency_ID as id, currency_Name as name FROM Classificator_currency WHERE Checked = 1", ARRAY_A);
$currencyOpt =  getOptionsFromArray(array_column($currency, 'name', 'id'), $f_currency);

$ncctpl = ['2001' => 'Плитки', '2052' => 'Списки', '2025' => 'Таблица (без фото)','2031' => 'Таблица (с фото)'];
$optionsNcctpl = getOptionsFromArray($ncctpl, $f_patern_ncctpl);
$defaulSettingUpload = json_decode(file_get_contents( __DIR__ . '/defaultSettingUpload.json'), 1);
$settingUpload = ($f_setting_upload ? orderArray($f_setting_upload) : $defaulSettingUpload);
function getSettingUpload($defaulSettingUpload, $settingUpload, $keyParent = '') {
	$result = '';
	foreach ($defaulSettingUpload as $key => $value) {
		$thisKey = "{$keyParent}{$key}";
		$nameInput = "f_setting_upload[$thisKey]";
		$childs = '';
		$classUl = [];
		if ($keyParent) $classUl[] = 'none';

		if (isset($value['childs']) && count($value['childs']) > 0) {
			$childs = getSettingUpload($value['childs'], $settingUpload[$key]['childs'], $thisKey);
		}
		$checked = (!isset($settingUpload[$key]['checked']) ? $value['checked'] : $settingUpload[$key]['checked']);
		$result .= "<li>
			<div class='switch'>
				<label>
					<input type='checkbox' value='1' " . ($checked ? 'checked' : '') . " name='{$nameInput}'>
					<span class='lever'></span>
					<span class='sw-text'>{$value['name']}</span>
				</label>
				" . ($childs ? '<span class="open" data-title="+"></span>' : '') . "
			</div>
			{$childs}
		</li>";

	}
	return $result ? "<ul class=" . implode(' ', $classUl). ">{$result}</ul>" : '';
}
?>


<ul class="tabs tabs-border">
    <li class="tab"><a href="#tab_t1">Основное</a></li>
    <li class="tab"><a href="#tab_t2">Настройки</a></li>
    <!-- <li class="tab"><a href="#tab_t3">Дополнительно</a></li> -->
</ul>
<div class="modal-body tabs-body excel_export">
	<div id='tab_t1'>
		<div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название (если нужно):", "maxlength='255' size='50'")?></div>
    	<div class='colline colline-2'><?=bc_file("f_excel", $f_excel_old, "Excel файл", $f_excel, 2520)?></div>
		<div class='colline colline-2'><?=bc_input("f_url", $f_url, "Или ссылка на файл:", "maxlength='255' size='50'")?></div>
		<div class='colline colline-height excel-checkbox'>
			<fieldset class="setting_upload">
				<legend>Настройки "Cоздания и обновления"</legend>
				<?=getSettingUpload($defaulSettingUpload, $settingUpload)?>
			</fieldset>
		</div>
	</div>
	<div class='none' id='tab_t2'>
		<fieldset class="list">
			<legend>Настройки рабочего листа</legend>
			<div class='colline colline-5-2'><?=bc_input("f_list", $f_list, "№ листа", "maxlength='12' size='12'")?></div>
			<span class='or'> или </span>
			<div class='colline colline-5-2'><?=bc_input("f_list_name", $f_list_name, "Наименование листа", "maxlength='12' size='12'", 1)?></div>
		</fieldset>
		<fieldset class="header_excel">
			<legend>Настройки шапки</legend>
			<div class='colline colline-5-2'><?=bc_input("f_number_row_head", $f_number_row_head, "№ строки", "maxlength='255' size='50'", 1)?></div>
			<span class='or'> или </span>
			<div class='colline colline-5-2'><?=bc_input("f_firsthead", $f_firsthead, "Первое слово шапки таблицы", "maxlength='255' size='50'", 1)?></div>
			<p class="note">* Если нет шапки то значения "№ строки" должно быть <i>0</i></p>
		</fieldset>
		<fieldset class="group">
			<legend>Настройки разделов</legend>
			<div class='colline colline-1'><?=bc_select("f_parent", $catsubOpt, "Выгрузить в раздел", "class='ns'")?></div>
			<div class='colline colline-2'><?=bc_input("f_group_col", ($f_group_col ?: 1), "№ столбца с разделами", "maxlength='12' size='12'", 1)?></div>
			<div class='colline colline-2' <?=$f_ncctpl?>><?=bc_select("f_patern_ncctpl", $optionsNcctpl, "Шаблон отображения", "class='ns'")?></div>
			<div class='colline colline-1'><?=bc_input("f_spacesub", $f_spacesub, "Структура подразделов, символы перед наименований (1:2:3:4)")?></div>
			<div class='colline colline-height'><?=bc_textarea("f_fields_group", $f_fields_group, "Поля раздела через запятую (последовательность как в файле)", "", 1)?></div>
		</fieldset>
		<fieldset class="item">
			<legend>Настройки товара</legend>
			<div class='colline colline-2'><?=bc_select("f_currency", $currencyOpt, "Валюта в прайсе", "class='ns'")?></div>
			<div class='colline colline-2'><?=bc_select("f_ssub", $catsubOpt, "Товары без категорий разместить в", "class='ns'")?></div>
			<div class='colline colline-height'><?=bc_textarea("f_fields_item", $f_fields_item, "Поля товара через запятую (последовательность как в файле)", "", 1)?></div>
		</fieldset>
		<?php if ($catalogue == 895) : ?>
			<fieldset class="list">
				<legend>Наценка на все товары в прайс листе</legend>
				<div class='colline colline-1'><?=bc_input("f_markup", $f_markup, "Наценка в процентах, положительное число, по умолчанию 0", "maxlength='12' size='12'")?></div>
			</fieldset>
		<?php endif; ?>
	</div>
	<!-- <div class='none' id='tab_t3'>
		
	</div> -->
	<script>
		$('.excel-checkbox .open').on('click', function() {
			$(this).attr('data-title', $(this).attr('data-title') == '+' ? '-' : '+');
			$(this).parents('li').find('ul:first').toggleClass('none')
		})
	</script>
</div>