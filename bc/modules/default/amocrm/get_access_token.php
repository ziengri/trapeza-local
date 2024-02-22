<?

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

GLOBAL $setting, $current_catalogue, $HTTP_HOST;

$action = ($_POST['action'] ? $_POST['action'] : '');
$subdomain = ($_POST['subdomain'] ? $_POST['subdomain'] : '');
$client_secret = ($_POST['client_secret'] ? $_POST['client_secret'] : '');
$client_id = ($_POST['client_id'] ? $_POST['client_id'] : '');
$code = ($_POST['code'] ? $_POST['code'] : '');
$redirect_uri = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')."://" .$HTTP_HOST."/";
if($action == 'get_accest_token') {
    if(!$subdomain || !$client_id || !$client_secret || !$code || !$redirect_uri) return response("Заполните обязательные поля !", 0);

    $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

    /** Соберем данные для запроса */
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
    ];
    $curl = curl_init(); 
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); 
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $code = (int)$code;
    $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    try
    {
        if ($code < 200 || $code > 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
        }
    }
    catch(\Exception $e)
    {
        return response('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode(),0);
    }

    $response = json_decode($out, true);

    $setting['actoken'] = $response['access_token']; //Access токен
    $setting['acreftoken'] = $response['refresh_token']; //Refresh токен
    $setting['acsubdomen'] = $subdomain; //subdomain
    $setting['actime'] = date("Y-m-d") .' по '. date("Y-m-d", time() + $response['expires_in']);
    $setting['actimestamp'] = time() + $response['expires_in'];
    $setting['acclientid'] = $client_id;
    $setting['acclientsecret'] = $client_secret;
    if(setSettings($setting)) return response("Токен получен", 1);
}
function response($text, $success) {
    if ($success){
        $succes = $text;
        $reload = '1';
    } else $error = $text;

    echo json_encode(['success' => $success, 'error' => $error,'succes'=> $succes, 'reload' => $reload]);
}
?>
