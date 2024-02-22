<?php
require_once '/var/www/krza/data/www/krza.ru/bc/modules/default/include_console/include_console.php';

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once $ROOTDIR . '/bc/modules/default/PaymentMethod/class_payment_method.php';

global $db, $catalogue, $current_catalogue, $nc_core;

$PaymentMethod = new PaymentMethod();

if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

$allOrdersDB = $db->get_results("SELECT Message_ID, 
                                        Catalogue_ID, 
                                        orderId, 
                                        paymentService 
                                FROM    Message2005 
                                WHERE   orderId IS NOT NULL AND 
                                        paymentService IS NOT NULL AND 
                                        ((statusOplaty != 2 AND statusOplaty != 3) OR statusOplaty IS NULL)  AND 
                                        Created > NOW() - INTERVAL 5 DAY AND Catalogue_ID = {$catalogue}", 'ARRAY_A' );

$allOrders = [];
foreach ($allOrdersDB as $orderDB) {
    $allOrders[$orderDB['Catalogue_ID']][$orderDB['Message_ID']] = ['orderId' => $orderDB['orderId'], 'paymentService' => $orderDB['paymentService']];
}
foreach ($allOrders as $Catalogue => $orders) {
    $login = $db->get_var("SELECT login FROM Catalogue WHERE Catalogue_ID = '{$Catalogue}'");

    if (!$login) continue;

    $settingThisSite = json_decode(file_get_contents($ROOTDIR."/a/{$login}/settings.ini"), 1);

    foreach ($orders as $message => $order) {
        switch ($order['paymentService']) {
            case 'yKassa':
                $param = ['yaShopId' => $settingThisSite['yaShopId'], 'yaScid' => $settingThisSite['yaScid']];
                $PaymentMethod->stastusOplatyYkassa($message, $param);
                break;
        }
    }

}
