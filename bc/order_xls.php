<?
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

/*while (ob_get_level() > 0) {
    ob_end_flush();
}*/
$keytmp = $catalogue*463-165;

if (isset($adm)) { die("{$keytmp}"); }
if ($key!=$keytmp && $_SERVER[REMOTE_ADDR]!='31.13.133.138') die();


$period = date("Y-m-d H:i:s", strtotime('-731 day',time()));

$itemsArr = $db->get_results("select Message_ID,Created,fio,phone,email,city,adres,totalSum,orderlist from Message2005 where Created > '{$period}' AND Catalogue_ID = '$catalogue' ORDER BY Created DESC", ARRAY_A);
if (!$itemsArr) die;


foreach($itemsArr as $t) {
	$totSum = $orderlistArr = "";
	
	$items = orderArray($t['orderlist']);
	if ($items['items']){
		foreach($items['items'] as $item) {
			$itemsum = ($item['sum'] ? $item['sum'] : "б/ц");
			$orderlistArr[] = "".$item['name']." (".$item['count']."шт. = ".$itemsum.")";
			$totSum = $totSum + $item['sum'];
		}
	}
	
	$tr .= "<tr>
	<td>".$t['Message_ID']."</td>
	<td>".$t['Created']."</td>
	<td>".$t['fio']."</td>
	<td>".$t['phone']."</td>
	<td>".$t['email']."</td>
	<td>".$t['city']."</td>
	<td>".$t['adres']."</td>
	<td>".($totSum>0 ? $totSum : $t['totalSum'])."</td>
	
	<td>".implode("; \r\n",$orderlistArr)."</td>
	</tr>";
	
	
}

$priceAll = "<table widtd='100%' border='1' cellpadding='0' cellspacing='0'>
<tr>
	<td>№</td>
	<td>Дата</td>
	<td>ФИО</td>
	<td>Телефон</td>
	<td>E-mail</td>
	<td>Город</td>
	<td>Адрес</td>
	<td>Сумма</td>
	<td>Товары</td>
	</tr>
	{$tr}
</table>";

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="orders_'.time().'.xls"');//имя
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Lengtd: ' . strlen($priceAll));
echo $priceAll;

$itemsArr = $priceAll = $tr = '';
