<?php 
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once($NETCAT_FOLDER."vars.inc.php");
require_once ($ROOT_FOLDER."connect_io.php");


global $db, $nc_core;

            file_put_contents('sql.txt', json_encode($_REQUEST));
            exit();
            
$SpamFromEmail = $nc_core->settings[system][SpamFromEmail];
$shopCCset = nc_get_visual_settings(12);
$siteName = $db->get_var("select Catalogue_Name from Catalogue where Catalogue_ID = '1'");

$shopSettingsArr = $db->get_results("select `key`, value from Bitcat WHERE `key` LIKE 'rkassa%'", ARRAY_A);
foreach($shopSettingsArr as $shopSet) {
    $key = $shopSet['key'];
	$shopSetting[$key] = $shopSet['value'];
}

// as a part of ResultURL script

// your registration data
$mrh_pass2 = $shopSetting['rkassaPass2'];   // merchant pass2 here

// HTTP parameters:
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$crc = $_REQUEST["SignatureValue"];

// HTTP parameters: $out_summ, $inv_id, $crc
$crc = strtoupper($crc);   // force uppercase

// build own CRC
$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

if (strtoupper($my_crc) != strtoupper($crc)){
  echo "bad sign\n";
  exit();
}

// print OK signature
echo "OK$inv_id\n";

$db->query("update Message2005 set ShopOrderStatus = 3 where Message_ID = '$inv_id'");

$Email = "info@".str_replace("www.","",$_SERVER['HTTP_HOST']);
$text = "Здравствуйте!
<br>
<br>".date("d.m.Y - H:i")."
<br>Поступила оплата заказа № {$inv_id}.
<br>Сумма {$out_summ} рублей
<br>
<br>-------------------------------
<br>Интернет-магазин {$siteName}";
$mailer = new CMIMEMail();
$mailer->setCharset('utf-8');
$mailer->mailbody(strip_tags($text), $text);
$mailer->send(($shopMail['EmailTo'] ? $shopMail['EmailTo'] : $SpamFromEmail), $Email, $Email, "Поступила оплата заказа $inv_id", "Интернет-магазин ".$siteName);
$mailer->send("wultrex@ya.ru", $Email, $Email, "Поступила оплата заказа $inv_id", "Интернет-магазин ".$siteName);


?>