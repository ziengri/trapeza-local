<?php 
$catsubArr = $db->get_results("select a.Subdivision_Name as subname, a.Subdivision_ID as subid, a.Hidden_URL as psubid,  a.Checked from Subdivision as a, Sub_Class as b where a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 2001 AND a.Catalogue_ID = '$catalogue' AND a.Hidden_URL NOT LIKE '%search%' ORDER BY a.Hidden_URL, a.Priority", ARRAY_A);
foreach($catsubArr as $cs) { $o = '';
	$c = substr_count($cs[psubid],"/")-2;
	for($i=1;$i<=$c;$i++) {
		$o .= "-";
	}
	$catsubOpt .= "<option ".($cs[subid]==$f_parent ? "selected" : NULL)." value='{$cs[subid]}'>{$o} {$cs[subid]}. {$cs[subname]}</option>";
}
$catsubOpt = "<option value=''>- не выбран -</option>".$catsubOpt;

$currency = $db->get_results("SELECT currency_ID as id, currency_Name as name FROM Classificator_currency WHERE Checked = 1", ARRAY_A);
foreach ($currency as $v)  $currencyArr[$v[id]] = $v[name];

$ncctpl = array('2001' => 'Плитки', '2052' => 'Списки', '2025' => 'Таблица (без фото)','2031' => 'Таблица (с фото)');
$optionsNcctpl = getOptionsFromArray($ncctpl, $f_ncctpl);
?>

<input type="hidden" name="f_startcol" value="1">

<ul class="tabs tabs-border">
    <li class="tab"><a href="#tab_t1">Основное</a></li>
</ul>
<div class="modal-body tabs-body">
	<div id='tab_t1'>
		<div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название (если нужно):", "maxlength='255' size='50'")?></div>
    	<div class='colline colline-2'><?=bc_file("f_excel", "", "Excel файл (формат XLS)", $f_excel, 2520)?></div>
		<div class='colline colline-2'><?=bc_input("f_url", $f_url, "Или ссылка на файл:", "maxlength='255' size='50'")?></div>
		<div class='colline colline-5'><?=bc_input("f_list", ($f_list ? $f_list : 1), "№ листа", "maxlength='12' size='12'", 1)?></div>
		<div class='colline colline-5-2'><?=bc_input("f_firsthead", $f_firsthead, "Первое слово шапки таблицы", "maxlength='255' size='50'", 1)?></div>
		<div class='colline colline-5-2'><?=bc_select("f_currency", getOptionsFromArray($currencyArr, $f_currency), "Валюта в прайсе", "class='ns'")?></div>
	</div>
</div>