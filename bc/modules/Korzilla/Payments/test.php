<?php

use App\modules\Korzilla\Payments\DolyameAPI\DolyameAPIClient;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\ClientInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\Item;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CommitOrderRequest;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CreateOrderRequest;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once('autoload.php');

$login = "MakfurnituraRu";
$password = "MPBXyXYx6C";
$keyFile = __DIR__ . "/dolyami_private.key";
$certFile = __DIR__ . "/dolyami.pem";


$dolyami = new DolyameAPIClient($login, $password, $certFile, $keyFile);


// $item = new Item("tovar_1", 1, 50, "1");
// $item2 = new Item("tovar_2", 2, 25, "2");


// $orderItems = new OrderItems();
// $orderItems->addItem($item)->addItem($item2);


// $clientInfo = new ClientInfo();
// $clientInfo->setFirstName("Павел");
// $clientInfo->setLastName("Кротов");
// $clientInfo->setMiddleName("Тестович");
// $clientInfo->setEmail("5784416e-4d63-44db-aaca-2ff88aeb9bf0@email.webhook.site");
// $clientInfo->setPhone("+79393867399");
// $clientInfo->setBirthDate("1989-01-01");

// $orderRequest = new CreateOrderRequest("test_8", 100, $orderItems);

// $orderRequest->setClientInfo($clientInfo);
// $orderRequest->setNotificationURL("https://webhook.site/5784416e-4d63-44db-aaca-2ff88aeb9bf0");
// $orderRequest->setFailURL("https://webhook.site/5784416e-4d63-44db-aaca-2ff88aeb9bf0");
// $orderRequest->setSuccessURL("https://webhook.site/5784416e-4d63-44db-aaca-2ff88aeb9bf0");



// var_dump($dolyami->createOrder($orderRequest)->toArray()); echo"<br><br>";
var_dump($dolyami->orderInfo("35605")->toArray()); echo"<br><br>";
// $commitOrderRequest = new CommitOrderRequest("test_7", 100, $orderItems); echo"<br><br>";
// // var_dump($dolyami->commitOrder($commitOrderRequest)->toArray()); echo"<br><br>";
// var_dump($dolyami->cancelOrder("test_8")->toArray()); echo"<br><br>";
// var_dump($dolyami->orderInfo("test_7")->toArray()); echo"<br><br>";



