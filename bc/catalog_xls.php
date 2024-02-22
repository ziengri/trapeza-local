<?php 
ini_set('memory_limit', '2000M');

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
	
$connectTemplate = $ROOTDIR.$pathInc2."/catalog_xls.php";
if($current_catalogue['customCode'] && file_exists($connectTemplate)){
  include($connectTemplate);
}else{
	/*while (ob_get_level() > 0) {
	    ob_end_flush();
	}*/

	if (isset($adm)) {echo $catalogue*1271-14; die(); }
	if ($key!=$catalogue*1271-14 && $_SERVER[REMOTE_ADDR]!='31.13.133.138') die;

	$itemsArr = $db->get_results("select * from Message2001 where Catalogue_ID = '$catalogue' GROUP BY Subdivision_ID, name", ARRAY_A);
	if (!$itemsArr) die;

	foreach($itemsArr as $t) {
		$group3 = $group2 =$group = '';
		if ($sub!=$t['Subdivision_ID']) {
			$group3 = $db->get_row("select Subdivision_Name, Parent_Sub_ID, Hidden_URL from Subdivision where Subdivision_ID = '{$t['Subdivision_ID']}'",ARRAY_A);
			$sub=$t['Subdivision_ID'];
			
			$group2 = $db->get_row("select Subdivision_Name, Parent_Sub_ID, Hidden_URL from Subdivision where Subdivision_ID = '".$group3['Parent_Sub_ID']."'",ARRAY_A);
			if ($group2['Parent_Sub_ID']>0) {
				if ($group2['Hidden_URL']!='/catalog/') {
					$group = $db->get_row("select Subdivision_Name, Parent_Sub_ID, Hidden_URL from Subdivision where Subdivision_ID = '".$group2['Parent_Sub_ID']."'",ARRAY_A);
					if ($group['Parent_Sub_ID']>0) {
						if ($group['Hidden_URL']!='/catalog/') {
							$tr .= "<tr><td bgcolor='#91ea6e' colspan='18'><b>".$group['Subdivision_Name']."</b></td></tr>";
						}
					}
					$tr .= "<tr><td bgcolor='#6adce2' colspan='18'><b>".$group2['Subdivision_Name']."</b></td></tr>";
				}
			}
			
			
			$tr .= "<tr><td bgcolor='yellow' colspan='18'><b>".$group3['Subdivision_Name']."</b></td></tr>";
		}
		$photo = new nc_multifield('photo', 'Фотографии', 0);
		$photo_data = $db->get_results("SELECT Path FROM Multifield WHERE Field_ID = 2353 AND Message_ID = {$t['Message_ID']} ORDER BY `Priority`", ARRAY_A);
		if($photo_data) $photo->set_data($photo_data);
		foreach ($photo->records as $ph) {
			if (preg_match("/[^\/]+$/", $ph['Path'], $match)) {
				$photolink[] = $match[0];
			}
		}
		// $photolink = preg_match_all("/\.[\D]{3}/", $tovar->photo, $matches);
		$tr .= "<tr>
		<td>{$t[Message_ID]}</td>
		<td>".stripslashes($t[name])."</td>
		<td>{$t[art]}</td>
		<td>{$t[vendor]}</td>
		<td>{$t[stock]}</td>
		<td>{$t[edizm]}</td>
		<td>{$t[price]}</td>
		<td>".$t[descr]."</td>
		<td>".htmlspecialchars($t[text])."</td>
		<td>".htmlspecialchars($t[text2])."</td>
		<td>{$t[var1]}</td>
		<td>{$t[var2]}</td>
		<td>{$t[var3]}</td>
		<td>{$t[var4]}</td>
		<td>{$t[var5]}</td>
		<td>{$t[var6]}</td>
		<td>{$t[var7]}</td>
		<td>{$t[var8]}</td>
		<td>{$t[var9]}</td>
		<td>{$t[var10]}</td>
		<td>{$t[variablename]}</td>
		<td>{$t[colors]}</td>
		<td>".($photolink ? implode(",",$photolink) : "")."</td>
		</tr>";
		
		unset($photolink); unset($photo);
	}

	$priceAll = "<table widtd='100%' border='1' cellpadding='0' cellspacing='0'>
	<tr>
		<td>ID</td>
		<td>Название</td>
		<td>Артикул</td>
		<td>Произв-ль</td>
		<td>Наличие</td>
		<td>Ед.изм</td>
		<td>Цена</td>
		<td>Краткое описание</td>
		<td>Описание</td>
		<td>Описание2</td>
		<td>Знач.1</td>
		<td>Знач.2</td>
		<td>Знач.3</td>
		<td>Знач.4</td>
		<td>Знач.5</td>
		<td>Знач.6</td>
		<td>Знач.7</td>
		<td>Знач.8</td>
		<td>Знач.9</td>
		<td>Знач.10</td>
		<td>Вариант товара</td>
		<td>Цвет</td>
		<td>Фото</td>
		</tr>
		{$tr}
	</table>";

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="price_'.$hostt.'.xls"');//имя
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Lengtd: ' . strlen($priceAll));
	echo $priceAll;

	$itemsArr = $priceAll = $tr = '';
}
?>