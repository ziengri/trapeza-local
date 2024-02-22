<?php
use App\modules\Korzilla\Payments\DolyameAPI\Builders\CommitOrderBuilder;
use App\modules\Korzilla\Payments\DolyameAPI\DolyameAPIClient;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

define("SUCCESS", 2);
define("CANCEL", 3);
define("WAIT", 1);

//*TODO Проверить IP адрес. 
//*TODO Уведомления банка приходят с маски сети 91.194.226.0/23, соответственно IP адрес первого хоста — 91.194.226.1 и IP адрес последнего хоста — 91.194.227.254, хостов в сети 510.

global $db, $pathInc, $current_catalogue, $nc_core;


$data = json_decode(file_get_contents("php://input"), true);


if (empty($data)) {
    file_put_contents(__DIR__ . "/logs/" . $_SERVER['HTTP_HOST'] . ".log", date('m/d/Y h:i:s a', time()) . ": POST IS EMPTY\r\n", FILE_APPEND);
    die("No POST data");
}


switch ($data['status']) {
    case 'approved':
        break;
    case 'rejected':
        updateStatusOplaty($data['id'], CANCEL);
        break;
    case 'canceled':
        updateStatusOplaty($data['id'], CANCEL);
        break;
    case 'wait_for_commit':
        $message = $data['id'];
        $dbOrder = $db->get_row("SELECT * FROM Message2005 WHERE Message_ID = $message", ARRAY_A);
        $order = orderArray($dbOrder['orderlist']);
        $commitOrderBuilder = new CommitOrderBuilder($message, $order);
        $commitOrderRequest = $commitOrderBuilder->build();

        $login = "MakfurnituraRu";
        $password = "MPBXyXYx6C";
        $mtlsCertPath = $ROOTDIR . $pathInc . "/certificates/dolyami/dolyami.pem";
        $sslKeyPath = $ROOTDIR . $pathInc . "/certificates/dolyami/dolyami_private.key";
        $dolyamiClient = new DolyameAPIClient($login, $password, $mtlsCertPath, $sslKeyPath);
        $orderInfo = $dolyamiClient->commitOrder($commitOrderRequest);
        var_dump($orderInfo->toArray());
        file_put_contents(__DIR__ . "/logs/" . $_SERVER['HTTP_HOST'] . ".log", date('m/d/Y h:i:s a', time()) . ": wait_for_commit\r\n", FILE_APPEND);
        break;
    case 'completed':
        file_put_contents(__DIR__ . "/logs/" . $_SERVER['HTTP_HOST'] . ".log", date('m/d/Y h:i:s a', time()) . ": committed\r\n", FILE_APPEND);

        updateStatusOplaty($data['id'], SUCCESS);
        break;

    default:
        break;
}


file_put_contents(__DIR__ . "/logs/" . $_SERVER['HTTP_HOST'] . ".log", date('m/d/Y h:i:s a', time()) . ": " . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\r\n", FILE_APPEND);
