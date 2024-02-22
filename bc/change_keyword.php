<?php 

#### Массовое изменение keyword товара на имя + id товара
# adm - получение ключа
# key - ключ для выполнения скрипта
# onlyname - ключевое слово только из названия (не рекомендуется)
# withart - ключевое слово = имя + артикул
# fix - если есть косяк со сменой ключевого слова, но хочется всё исправить. Перезаписывает ключевое слово с новыми параметрами. Для работы также нужно добавить id сайта в массив $returncats.
# Сначала рекомендуется изучить echo (по умолчанию), после можно раскомментить сам запрос.

ini_set('memory_limit', '700M');

set_time_limit(1000000);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $patdInc, $catalogue, $current_catalogue, $nc_core, $field_connect,$pathInc2;

// получить ID сайта и параметры
$hostt = str_replace("www.","",$_SERVER['HTTP_HOST']);
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name($hostt);
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}
	
$connectTemplate = $ROOTDIR.$pathInc2."/change_keyword.php";
if($current_catalogue['customCode'] && file_exists($connectTemplate)){
  include($connectTemplate);
}else{

	// $cats = array(623);
	// if (in_array($catalogue, $cats)) {
	// 	$subs = $db->get_results("select Subdivision_ID, Subdivision_Name, code1C from Subdivision where Subdivision_Name LIKE 'Автошины R%' and Catalogue_ID = '$catalogue'", ARRAY_A);
	// 	foreach ($subs as $key => $s) {
	// 		$newpath = preg_replace('/Автошины Р[\d]{2}/', $s['Subdivision_Name'], $s['code1C']);
	// 		$sql = "update Subdivision set code1C = '$newpath' where Subdivision_ID = '$s[Subdivision_ID]'";
	// 		// echo $sql."<br>";
	// 		$db->query($sql);
	// 	}
	// 	echo "ok";
	// 	die;
	// }

	if (isset($adm)) echo $catalogue*1271-14; 
	if ($key!=$catalogue*1271-14 || $_SERVER[REMOTE_ADDR]!='31.13.133.138') die;

	# исправление замены
	$returncats = array(751);
	if ($fix && in_array($catalogue, $returncats)) {
		$itemsArr = $db->get_results("select * from Message2001 where Catalogue_ID = '$catalogue' AND (Keyword != '' OR Keyword IS NOT NULL)", ARRAY_A);
		foreach($itemsArr as $t) {
			$nameenc = encodestring($t[name],1);
			if (strpos($t[Keyword], $nameenc)===false) {}
			else {
				$art = $kw = $sql = "";
				# если берем артикул, для исключения ошибок переводим в транслит
				$art = $withart ? encodestring($t[art],1) : $t[art];
				$kw = strtolower(encodestring($t[name],1)).($onlyname ? "" : "_".($withart ? $art : $t[Message_ID]));
				$sql = "update Message2001 set Keyword = '{$kw}' where Catalogue_ID = '$catalogue' AND Message_ID = '".$t[Message_ID]."' AND Keyword LIKE '%{$nameenc}%'";
				echo $sql."<br>";
				// $db->query($sql);
			}
		}
	}
	else {
		$itemsArr = $db->get_results("select * from Message2001 where Catalogue_ID = '$catalogue' AND (Keyword = '' OR Keyword IS NULL)", ARRAY_A);
		if (!$itemsArr) die;

		foreach($itemsArr as $t) {
			$art = $kw = $sql = "";
			# если берем артикул, для исключения ошибок переводим в транслит
			$art = $withart ? encodestring($t[art],1) : $t[art];
			$kw = strtolower(encodestring($t[name],1)).($onlyname ? "" : "_".($withart ? $art : $t[Message_ID]));
			$sql = "update Message2001 set Keyword = '$kw' where Catalogue_ID = '$catalogue' AND Message_ID = '".$t[Message_ID]."' AND (Keyword = '' OR Keyword IS NULL)";
			echo $sql."<br>";
			// $db->query($sql);
		}
	}
}

$itemsArr = "";
echo "OK";
?>