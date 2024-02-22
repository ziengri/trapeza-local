<?php
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

global $db, $setting;

$company_id = $_POST['company_id'] ? $_POST['company_id'] : $_GET['company_id'];
$device_id = isset($_POST['device_id'])? $_POST['device_id'] : null;
$firebase_token = isset($_POST['firebase_token'])? $_POST['firebase_token'] : null;
$push_title = $_POST['push_title'];
$push_text = $_POST['push_text'];
$push_href = punycode_encode($_POST['push_href']);
$method = $_POST['method'] ? $_POST['method'] : $_GET['method'];
$data = array();

switch ($method) {
    case 'get_start_url':
        //mobile_method
        if ($company_id != null) {
            $login = $db->get_var("select login from Catalogue where Catalogue_ID = '".$company_id."'");
            $settingMy =  json_decode(file_get_contents($ROOTDIR."/a/".$login."/settings.ini"), 1);

            $start_url = punycode_encode($settingMy['start_utl']);

            $data['data']['start_url'] = $start_url;

            if (strpos($start_url, 'http') !== false) {
                $res = false;
            } else {
                $res = true;
            }

            $data = getError($data, $res);
        } else {
            $data = getError($data, true);
        }

        echo outJson($data);
        break;

    case 'set_firebase_token':
        //mobile_method

        if ($company_id == null || $device_id == null || $firebase_token == null) {
            $data = getError($data, true);
        } else {
            if ($db->get_row("select firebase_token from Message2100 where Catalogue_ID = '".$company_id."' and device_ID = '".$device_id."'", ARRAY_A) == null) {
                $db->query("insert into Message2100 (Catalogue_ID, device_ID, firebase_token) VALUES ('$company_id','$device_id', '$firebase_token')");
            } else {
                $db->query("update Message2100 set firebase_token = '".$firebase_token."' where Catalogue_ID = '".$company_id."' and device_ID = '".$device_id."'");
            }

            $data = getError($data, false);
        }
        echo outJson($data);
        break;

    case 'push':
        $error = '';

        switch (true) {
            case !$push_title:
                $error = 'Нет заголовка';
                break;

            case !$push_text:
                $error = 'Нет описания';
                break;

            case !$push_href || (strpos($push_href, 'http://') ===  false && strpos($push_href, 'https://') ===  false) || strpos($push_href, $_SERVER['HTTP_HOST']) ===  false:
                $error = 'Не валидная ссылка';
                break;
        }

        if ($error) {
            echo json_encode(['success' => '0','error'=> $error]);
            die;
        }


        if ($company_id != null) {
            switch ($_POST['orderpush']) {
                case '1':
                    $sql_where = "AND User_ID != 0";
                    break;
                case '2':
                    $sql_where = "AND User_ID = 0";
                    break;
                case '0':
                default:
                    $sql_where = "";
                    break;
            }
            $firebase_id = $db->get_var("select firebase_ID from Message2101 where Catalogue_ID = '$company_id'");
            $firebase_tokens = $db->get_results("SELECT firebase_token FROM Message2100 WHERE Catalogue_ID = '$company_id' {$sql_where} GROUP BY firebase_token", ARRAY_A);
            // echo "select firebase_token from Message2100 where Catalogue_ID = '$company_id' ".$sql_where;

            if ($firebase_tokens) {
                $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($firebase_tokens));
                $firebase_tokens_list = iterator_to_array($iterator, false);
            } else {
                $firebase_tokens_list = [];
                echo json_encode(['success' => '0','error'=>'Нет скаченых приложений']);
                die();
            }


            $fields = array(
                'registration_ids' => $firebase_tokens_list,
                'notification' => array('title' => $push_title, 'body'=> $push_text, "sound"=> "default"),
                'data' => $push_href ? array('notification_href' => $push_href) : null
            );

            $headers = array("Authorization: key=$firebase_id", "Content-Type: application/json");


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $response = curl_exec($ch);
            curl_close($ch);

            // echo $response === false ? "curl_exec failed: ".curl_error($ch)."<br>" : $response;
            echo json_encode(['succes' => 'Уведомление отправлено', 'success' => '1']);
        }
        break;
    case 'FBI':
        if (!empty($_POST['FBI'])) {
            $fbi = $_POST['FBI'];


            if ($db->query("select firebase_ID from Message2101 where Catalogue_ID = '$company_id'")) {
                $db->query("update Message2101 set firebase_ID = '".$fbi."' where Catalogue_ID = '".$company_id."' ");
            } else {
                $db->query("insert into Message2101 (firebase_ID, Catalogue_ID) VALUES ('$fbi','$company_id')");
            }

            echo json_encode(['company_id' => $company_id,'success' => '1', 'FBI' => $fbi, 'title' => 'LOL работает','succes'=>'че кого']);
        } else {
            echo json_encode(['success' => '0','error'=>'ID не валидный']);
        }
        break;
}

function getError($data, $isError)
{
    $data['error'] =
        [
            'status' => $isError,
            'description' => "error"
        ];
    return $data;
}

function outJson($data)
{
    header('Content-Type: application/json');
    return json_encode($data);
}
