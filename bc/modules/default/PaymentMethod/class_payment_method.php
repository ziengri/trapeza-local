<?php

class PaymentMethod
{    
    /**
     * Настройки с сайта.
     *
     * @var array
     */
    public $setting;
        
    /**
     * id заказа
     *
     * @var int
     */
    public $message;
    
    /**
     * тело корзины
     *
     * @var array
     */
    public $order;
    public $customf;
        
    public function __construct($message = 0, $order = [], $customf = [])
    {
        global $current_catalogue, $setting;

        $this->message = $message;
        $this->order = $order;
        $this->setting = $setting;
        $this->customf = $customf;
        $this->current_catalogue = $current_catalogue;
        $this->siteName = str_replace(["«", "»", "'", '"'], "", $current_catalogue['Catalogue_Name']);
        $this->result = [
            'status' => 0,
            'payUrl' => '',
            'payButton' => '',
            'message' => ''
        ];
    }

    public function dolyami()
    {   
        global $DOCUMENT_ROOT, $pathInc,$setting;
        // ini_set('display_errors', '1');
        // ini_set('display_startup_errors', '1');
        // error_reporting(E_ALL);

        try {
            $createOrderBuilder = new App\modules\Korzilla\Payments\DolyameAPI\Builders\CreateOrderBuilder($this->message, $this->order, $this->customf['name']['value'], str_replace(['-',' '], '', $this->customf['phone']['value']), $this->customf['email']['value']);
            $createOrderRequest =  $createOrderBuilder->build();
    
            $mtlsCertPath = $DOCUMENT_ROOT . $pathInc . "/certificates/dolyami/dolyami.pem";
            $sslKeyPath = $DOCUMENT_ROOT . $pathInc . "/certificates/dolyami/dolyami_private.key";
            $dolyamiClient = new App\modules\Korzilla\Payments\DolyameAPI\DolyameAPIClient($setting['dolyamiLogin'], $setting['dolyamiPassword'], $mtlsCertPath, $sslKeyPath);
            
            $orderInfo = $dolyamiClient->createOrder($createOrderRequest);
    
            if($orderInfo->getStatus() == "new"){
                $this->result['status'] = 1;
                $this->result['payUrl'] = $orderInfo->getLink();
            }
            else{
                $this->result['status'] = 0;
                $this->result['message'] = 'Заказ не создан';
            }

        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }


    public function payKeeper()
    {
        global $DOCUMENT_ROOT, $pathInc;
        try {
            if (!$this->setting['paykeeperLogin'] || !$this->setting['paykeeperPass'] || !$this->setting['paykeeperServes']) {
                throw new \Exception("Not login or password or servis", 402);
            }

            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode("{$this->setting['paykeeperLogin']}:{$this->setting['paykeeperPass']}")
            ];

            # Для сетевых запросов в этом примере используется cURL
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, 'https://'. $this->setting['paykeeperServes'] . "/info/settings/token/");
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, false);

            # Инициируем запрос к API
            $response = curl_exec($curl);
            $php_array = json_decode($response, true);
            file_put_contents($DOCUMENT_ROOT.'/'.$pathInc.'/logPaykeeper.log', print_r([
                'date' => date('Y-m-d H:i:s'),
                'php_array' => $php_array,
                'curl' => $curl,
                'response' => $response,
                ], 1), FILE_APPEND);

            # В ответе должно быть заполнено поле token, иначе - ошибка
            if (isset($php_array['token'])) $token = $php_array['token'];
            else throw new \Exception("Invalid token", 402);

            # Параметры платежа, сумма - обязательный параметр
            $payKeeperCart = array();
            foreach ($this->order['items'] as $item) {
                $payKeeperCart[] = array(
                    "name" => $item['name'],
                    "price" =>  number_format((float) $item['price'], 2, '.', ''),
                    "quantity" => $item['count'],
                    "sum" => number_format((float) $item['sum'], 2, '.', ''),
                    "tax" => 'none'
                );
            }

            if ($this->order['delivery']['sum_result']) {
                $payKeeperCart[] = array(
                    "name" => "Доставка: ".$_SESSION['cart']['delivery']['name'],
                    "price" => number_format((float) $this->order['delivery']['sum_result'], 2, '.', ''),
                    "quantity" => 1,
                    "sum" => number_format((float) $this->order['delivery']['sum_result'], 2, '.', ''),
                    "tax" => 'none'
                );
            }

            # Остальные параметры можно не задавать
            $payment_data = array(
                "token" => $token,
                "pay_amount" => number_format((float) $this->order['totaldelsum'], 2, '.', ''),
                "clientid" => $this->customf['name']['value'],
                "orderid" => $this->message,
                "client_email" => $this->customf['email'],
                "service_name" => json_encode([
                    'service_name' => "Товар",
                    'cart' => $payKeeperCart,
                    'lang' => 'ru'
                ]),
                "client_phone" => $this->customf['phone'],
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, 'https://'. $this->setting['paykeeperServes'] . "/change/invoice/preview/");
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data, '', '&'));

            $response = json_decode(curl_exec($curl), true);
            file_put_contents($DOCUMENT_ROOT.'/'.$pathInc.'/logPaykeeper.log', print_r([
                'date' => date('Y-m-d H:i:s'),
                'orderlist_from_db' => $this->order,
                'order_data' => $payKeeperCart,
                'payment_data' => $payment_data,
                'curl' => $curl,
                'response' => $response,
                ], 1), FILE_APPEND);

            # В ответе должно быть поле invoice_id, иначе - ошибка
            if (isset($response['invoice_id'])) $invoice_id = $response['invoice_id'];
            else throw new \Exception($response['msg'], 402);

            $this->result = [
                'status' => 1,
                'payUrl' => "https://{$this->setting['paykeeperServes']}/bill/$invoice_id/"
            ];

        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function yKassa()
    {
        global $db;
        try {
            $zamen = array("«" => "", "»" => "", "'" => "", "\"" => "");
            $sum = 0;
            $items = [];
            //массив товаров
            foreach ($this->order['items'] as $item) {
                $amount = $item['price'] * $item['count'];
                $sum += $amount;
                $items[] = [
                    'description' => mb_substr($item['name'], 0, 64),
                    'amount' => ['value' => $item['price'], 'currency' => 'RUB'],
                    'quantity'  => $item['count'],
                    'vat_code' => 2
                ];
            }
            // учитывать доставку в сумме заказа
            if ($this->order['delivery']['sum_result'] > 0) {
                $sum += ($this->order['delivery']['sum_result']);
                $items[] = [
                    'description' => 'Доставка',
                    'amount' => ['value' => $this->order['delivery']['sum_result'], 'currency' => 'RUB'],
                    'quantity'  => '1',
                    'vat_code' => 2
                ];
            }


            $payment = [
                'amount' => ['value' => $sum, 'currency' => 'RUB'],
                'confirmation' => ['type' => 'redirect', 'return_url' => "http://{$_SERVER['HTTP_HOST']}/bc/modules/default/PaymentMethod/ykassa_redirect.php?message={$this->message}"],
                'capture' => true,
                'description' => strtr("Заказ №{$this->message} в магазине {$this->current_catalogue['Catalogue_Name']}", $zamen),
                'receipt' => [
                    'id' => $this->message,
                    'type' => 'payment',
                    'customer' => ['email' => $this->customf['email']['value']],
                    "items" => $items
                ],
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.yookassa.ru/v3/payments',
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', "Idempotence-Key: {$this->message}"],
                CURLOPT_HEADER => false,
                CURLOPT_USERPWD => "{$this->setting['yaShopId']}:{$this->setting['yaScid']}",
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($payment)
            ]);

            $res = json_decode(curl_exec($ch), 1);
            curl_close($ch);

            if ($res['type'] == 'error') {
                throw new Exception($res['description'], 1);
            }

            $db->query("UPDATE Message2005 SET orderId = '{$res['id']}', paymentService = 'yKassa' WHERE Catalogue_ID = '{$this->current_catalogue['Catalogue_ID']}' AND Message_ID = '{$this->message}'");

            $this->result = [
                'status' => 1,
                'payUrl' => $res['confirmation']['confirmation_url']
            ];
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage() . print_r($payment);
        }
        file_put_contents('/var/www/krza/data/www/krza.ru/bc/modules/default/PaymentMethod/logYKassa/pay.log', print_r([
            'result' => $this->result,
            'time' => date("Ymd H:i:s")
        ], 1), FILE_APPEND);
        return $this->result;
    }

    public function stastusOplatyYkassa($message, $params)
    {
        global $db;
        if (!$params['yaShopId'] || !$params['yaScid']) {
            return false;
        }
        $statusYkassAndDB = ['pending' => '1', 'succeeded' => '2', 'canceled' => '3', 'waiting_for_capture' => '4'];

        $orderIdYkass = $db->get_var("SELECT orderId FROM Message2005 WHERE  Message_ID = '{$message}'");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.yookassa.ru/v3/payments/{$orderIdYkass}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Idempotence-Key: {$message}"
        ));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, "{$params['yaShopId']}:{$params['yaScid']}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = json_decode(curl_exec($ch), 1);

        $status = $statusYkassAndDB[$res['status']];
        file_put_contents('/var/www/krza/data/www/krza.ru/bc/modules/default/PaymentMethod/logYKassa/status.log', print_r([
            'time' => date("Ymd H:i:s"),
            'result' => $res
        ], 1), FILE_APPEND);
        updateStatusOplaty($message, $status);
    }

    public function alfa($message, $out_summ)
    {

        if (function_exists('paymentMethode_alfa')) {
            return paymentMethode_alfa($message, $out_summ, $this); // своя функция
        } else {
            global $DOCUMENT_ROOT, $pathInc;
            $sum = 0;
            $positionId = 0;
            $items = array_reduce($this->order['items'], function ($carry, $item) use (&$sum, &$positionId) {
                $price = normalizePriceSber($item['price']);
                $itemAmount = $price * $item['count'];
                $sum += $itemAmount;
                $carry[] = [
                    'positionId' => $positionId++,
                    'name' => $item['name'],
                    'quantity' => ['value' => $item['count'], 'measure' => 'штук'],
                    'itemAmount' => $itemAmount,
                    'itemCode' => $item['id'],
                    'tax' => ['taxType' => 0],
                    'itemPrice' => $price
                ];
                return $carry;
            }, []);


            $data = [
                'amount' => $sum, // Сумма
                'language' => 'ru',
                'orderNumber' => $this->message,
                'password' => $this->setting['alfaPass'],
                'userName' => $this->setting['alfaLogin'],
                'returnUrl' => 'http://' . $_SERVER['HTTP_HOST'] . '/',
                'taxSystem' => 0,
                'orderBundle' => json_encode(['cartItems' => ['items' => $items]])
            ];
            $check = explode("-", $data['userName']);
            //$domainAlfa = (stristr($data['password'], $check[0]) ? "web.rbsuat.com/ab" : "pay.alfabank.ru/payment");
            $domainAlfa = 'pay.alfabank.ru/payment';

            if (stristr($data['userName'], 'r-')) {
                $domainAlfa = (stristr($data['password'], $check[1]) ? "web.rbsuat.com/ab" : "payment.alfabank.ru/payment");
            } 
			
            $url = "https://{$domainAlfa}/rest/register.do";

            file_put_contents($DOCUMENT_ROOT.'/'.$pathInc.'/logAlfaBank.log', print_r([
                '$dataAlfa' => $data,
                'domainAlfa' => $domainAlfa,
                'time' => date("Ymd H:i:s")
            ], 1), FILE_APPEND);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($data, '', '&'),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded']
            ]);

            $respons = json_decode(curl_exec($curl), 1);
            curl_close($curl);

            file_put_contents($DOCUMENT_ROOT.'/'.$pathInc.'/logAlfaBank.log', print_r([
                '$dataAlfa' => $data,
                'respons' => $respons,
                'time' => date("Ymd H:i:s")
            ], 1), FILE_APPEND);

            if ($respons['formUrl']) {
                $this->result['payButton'] = "<script type='text/javascript'> location='{$respons['formUrl']}' </script>";
                $this->result['status'] = 1;
            } else {
                $this->result['message'] = "{$respons['errorMessage']}";
            }

            return $this->result;
        }
    }
    // протестировать !!!
    public function uniteller()
    {
        try {
            if (!$this->setting['unitellerUPID'] || !$this->setting['unitellerPass']) {
                throw new Exception("Invalid auth params", 1);
            }
            $formArray = [
                'Shop_IDP' => $this->setting['unitellerUPID'],
                'Order_IDP' => $this->message,
                'Subtotal_P' => $this->order['totaldelsum'],
                'Lifetime' => 300,
                'Preauth' => 1,
                'URL_RETURN' => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/bc/modules/default/index.php?user_action=uniteller&type=preauth",
            ];

            $needliFields = ['Shop_IDP', 'Order_IDP', 'Subtotal_P', 'MeanType', 'EMoneyType', 'Lifetime', 'Customer_IDP', 'Card_IDP', 'IData', 'PT_Code'];

            $formArray['Signature'] = unitellerGetSinatere($formArray, $needliFields, $this->setting['unitellerPass'], '&');

            $this->result['payButton'] = "<form method='POST' action='https://fpay.uniteller.ru/v1/preauth' style='display: none;'>";

            foreach ($formArray as $key => $value) {
                $this->result['payButton'] .= "<input type='hidden' name='{$key}' value='{$value}'>";
            }
            $this->result['payButton'] .= "<input type='submit' name='unitellerSubmit' value='1'>
                        </form>
                        <script type='text/javascript'>$('input[name=\"unitellerSubmit\"]').click();</script>";
            $this->result['status'] = 1;
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }
        return $this->result;
    }
    // протестировать!!!
    public function robokassa()
    {
        try {
            if (!$this->setting['rkassaPass1'] || !$this->setting['rkassaPass2'] || !$this->setting['rkassaLogin']) {
                throw new Exception("Invalid auth params", 1);
            }
			
			$rkassaTypePay = ($this->setting['rkassaTypePay'] ? $this->setting['rkassaTypePay'] : "commodity");
			$taxations = ($this->setting['rkassaVat'] ? $this->setting['rkassaVat'] : "osn");
			
			
			
			$robokassaCart = array();
            foreach ($this->order['items'] as $item) {
                $robokassaCart[] = array(
                    "name" => $item['name'],
                    "cost" => (float)$item['price'],
                    "quantity" => $item['count'],
                    "sum" => $item['sum'],
                    "tax" => 'none',
					"payment_method" => "full_prepayment",
					"payment_object" => $rkassaTypePay
                );
            }

            if ($this->order['delivery']['sum_result']) {
                $robokassaCart[] = array(
                    "name" => "Доставка: ".$_SESSION['cart']['delivery']['name'],
                    "cost" => (float)$_SESSION['cart']['delivery']['sum_result'],
                    "quantity" => 1,
                    "sum" => (float)$_SESSION['cart']['delivery']['sum_result'],
                    "tax" => 'none',
					"payment_method" => "full_prepayment",
					"payment_object" => $rkassaTypePay
                );
            }


            $receipt = [
                'sno' => $taxations,
                'items' => $robokassaCart,
            ];
			
			$receiptJson = json_encode($receipt, JSON_UNESCAPED_UNICODE);
			
			

            // your registration data
            $mrh_login = $this->setting['rkassaLogin'];
            $mrh_pass1 = $this->setting['rkassaPass1'];

            // order properties
            $inv_id = $this->message;
            $out_summ = $this->order['totaldelsum'];
            $inv_desc  = urlencode("Заказ №{$inv_id} в магазине " . str_replace('"', "'", $this->current_catalogue['Catalogue_Name']));
            $inv_desc = str_replace(['+', '_', '.', '-'], ['%20', '%5F', '%2E', '%2D'], $inv_desc);

            // build CRC value
			if ($receiptJson) {
				$crc  = md5("$mrh_login:$out_summ:$inv_id:$receiptJson:$mrh_pass1");
				$receiptUrl = "&Receipt=".$receiptJson;
			} else {
				$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
			}
            

            // build URL
            $this->result['payUrl'] = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin={$mrh_login}&OutSum={$out_summ}&InvId={$inv_id}&Desc={$inv_desc}&SignatureValue={$crc}{$receiptUrl}";

            // $this->result['payButton'] = "<div class='payButton'><a target=_blank class='btn-strt-a' href='{$this->result['payUrl']}'><span class='btn-pad-lr'>Оплатить заказ сейчас</span></a></div>";

            $this->result['status'] = 1;
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function stastusOplatyRobokassa($message, $params)
    {
        global $db;

        $crc = strtoupper($params['crc']);
        $my_crc = strtoupper(md5($params['out_summ'] . ":" . $message . ":" . $params['rkassaPass2']));
        $status = 2;

        $res[] = [
            'crc' => $crc,
            'my_crc' => $my_crc
        ];

        file_put_contents('/var/www/krza/data/www/krza.ru/bc/modules/default/PaymentMethod/logRKassa/status.log', print_r([
            'time' => date("Ymd H:i:s"),
            'result' => $res
        ], 1), FILE_APPEND);

        if (strtoupper($my_crc) != strtoupper($crc)) {
            return "bad sign\n";
            exit();
        }

        updateStatusOplaty($message, $status);
    }

    public function tinkoff()
    {

        try {
            if (!$this->setting['tinkoffLogin'] || !$this->setting['tinkoffPassword']) {
                throw new \Exception("Invalid auth params", 1);
            }

            $items = array_reduce($this->order['items'], function ($acum, $item) {
                $acum[] = [
                    'Name'      => mb_substr($item['name'], 0, 64),
                    'Price'     => $item['price'] * 100,
                    'Quantity'  => $item['count'],
                    'Tax'       => 'none',
                    'Amount'    => $item['price'] * $item['count'] * 100
                ];
                return $acum;
            }, []);
            // учитывать доставку в сумме заказа
            if ($this->order['delivery']['sum_result'] > 0) {
                $items[] = [
                    'Name'      => 'Доставка',
                    'Price'     => $this->order['delivery']['sum_result'] * 100,
                    'Quantity'  => '1',
                    'Tax'       => 'none',
                    'Amount'    => $this->order['delivery']['sum_result'] * 100
                ];
            }

            $taxations = ($this->setting['tinkoffVat'] ? $this->setting['tinkoffVat'] : "osn");

            $receipt = [
                'Taxation' => $taxations,
                'Items' => $items,
            ];
            if (!empty($this->customf['email']['value'])) {
                $receipt['Email'] = $this->customf['email']['value'];
            }
            if (preg_match('/\d{10}$/', clearPhoneSNG($this->customf['phone']['value']), $phone)) {
                $receipt['Phone'] = sprintf('+7%d', $phone[0]);
            }

            $params = [
                'TerminalKey' => $this->setting['tinkoffLogin'],
                'OrderId' => $this->message,
                'Amount' => $this->order['totaldelsum'] * 100,
                'Receipt' => $receipt,
                'Password' => $this->setting['tinkoffPassword']
            ];
            ksort($params);

            foreach ($params as $tokenVal) {
                if (!is_array($tokenVal)) $token .= $tokenVal;
            }

            $token = array_reduce($params, function ($acum, $tokenVal) {
                if (!is_array($tokenVal)) $acum .= $tokenVal;
                return $acum;
            }, '');

            $params['Token'] = hash('sha256', $token);
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://securepay.tinkoff.ru/v2/Init/',
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($params),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
            ]);

            $respons = json_decode(curl_exec($curl), 1);
            curl_close($curl);

            if (isset($respons['Success'])) {
                $this->result['payUrl'] = $respons['PaymentURL'];
                $this->result['status'] = 1;
            } else {
                throw new \Exception(print_r($respons, 1), 10);
            }
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function sberbank()
    {
        global $db;
        try {
            if (!$this->setting['sberLogin'] || !$this->setting['sberPass']) {
                throw new \Exception("Invalid auth params", 1);
            }

            $items = array_reduce($this->order['items'], function ($acum, $item) {
                $acum[] = [
                    'positionId' => $item['id'],
                    'name' => $item['name'],
                    'quantity' => [
                        'value' => $item['count'],
                        'measure' => ''
                    ],
                    'itemPrice' => normalizePriceSber($item['price']),
                    'itemAmount' => normalizePriceSber($item['sum']),
                    'itemCurrency' => 643,
                    'itemCode' => $item['art'],

                ];
                return $acum;
            }, []);

            $orderBundleSber = [
                'customerDetails' => [
                    'email' => $this->customf['email']['value'],
                    'phone' => preg_replace("/^((+7|7|8)?([0-9]){10})$./", "", $this->customf['phone']['value']),
                    'contact' => $this->customf['name']['value'],
                    'deliveryInfo' => [
                        'country' => 'RU',
                        'city' => ($this->customf['city']['value'] ? $this->customf['city']['value'] : ($this->customf['address']['value'] ? $this->customf['address']['value'] : 'не указано')),
                        'postAddress' => ($this->customf['address']['value'] ? $this->customf['address']['value'] : ($this->customf['city']['value'] ? $this->customf['city']['value'] : 'не указано'))
                    ]
                ],
                'cartItems' => [
                    'items' => $items
                ]
            ];

            $sberParams = [
                'userName' => $this->setting['sberLogin'],
                'password' => $this->setting['sberPass'],
                'orderNumber' => $this->message,
                'amount' => normalizePriceSber($this->order['totaldelsum']),
                'returnUrl' => "http://{$_SERVER['HTTP_HOST']}/bc/modules/default/PaymentMethod/sber_redirect.php",
                'jsonParams' => json_encode($orderBundleSber)
            ];
            $domainSber = (stristr($this->setting['sberLogin'], $this->setting['sberPass']) ? "3dsec.sberbank.ru" : "securepayments.sberbank.ru");
            if ($this->setting["sberLogin"] === "t1650294720-api") {
                $domainSber = "3dsec.sberbank.ru";
            }
			
			
			for ($n = 0; $n < 5; $n++) { // цикл нужен, так как СБЕР отдает данные через раз.
				$curl = curl_init();
				curl_setopt_array($curl, [
					CURLOPT_URL            => "https://{$domainSber}/payment/rest/register.do",
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_POST           => true,
					CURLOPT_POSTFIELDS     => http_build_query($sberParams, '', '&'),
				]);
				$respons = json_decode(curl_exec($curl), 1);
				curl_close($curl);
				
				if ($respons && !isset($respons['errorCode'])) {
					break;
				}
				sleep(1);
			}

			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bc/modules/default/PaymentMethod/logSber/paysber.log', print_r([
				"req" => $sberParams,
				"reqparams" => $orderBundleSber,
				'result' => $respons,
			], 1), FILE_APPEND);


            if ($respons && !isset($respons['errorCode'])) {
                $db->query("UPDATE Message2005 SET orderId = '{$respons['orderId']}' WHERE Message_ID = '{$this->message}'");
                $this->result['payUrl'] = $respons['formUrl'];
                $this->result['status'] = 1;
            } else {
                throw new \Exception(print_r($respons['errorMessage'], 1), 10);
            }
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function payAnyWay()
    {
        try {
            if (!$this->setting['payAnyWayLogin']) {
                throw new Exception("Invalid auth params", 1);
            }

            $params = [
                'MNT_ID' => urlencode($this->setting['payAnyWayLogin']),
                'MNT_TRANSACTION_ID' => $this->message,
                'MNT_AMOUNT' => normalizePriceSber($this->order['totaldelsum']) / 100,
                'MNT_SUCCESS_URL' => "http://" . $_SERVER['HTTP_HOST'],
                'MNT_RETURN_URL' => "http://" . $_SERVER['HTTP_HOST']
            ];
            $this->result['payUrl'] = "https://www.payanyway.ru/assistant.htm?" . http_build_query($params, '', '&');
            $this->result['status'] = 1;
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function directCredit()
    {
        global $db;
        try {
            if (!$this->setting['dcPartnerId']) {
                throw new Exception("Invalid auth params", 1);
            }

            $items = array_reduce($this->order['items'], function ($acum, $item) use ($db) {
                $curritem = Class2001::getItemById($item['id']);
                $itemsub = $db->get_var("SELECT Subdivision_Name FROM Subdivision WHERE Subdivision_ID = (SELECT Subdivision_ID FROM Message2001 WHERE Message_ID = '$item[id]')");
                $itemtype = $curritem->var2 ? $curritem->var2 : ($itemsub == 'Аксессуары' ? $itemsub : 'Смартфоны');

                $acum[] = [
                    'id' => $item['id'],
                    'name' => mb_substr($item['name'], 0, 64),
                    'price' => $item['price'],
                    'count' => $item['count'],
                    'type' => $itemtype
                ];
                return $acum;
            }, []);

            $params = [
                'codeTT' => $this->setting['dcCodeTT'],
                'order' => $this->message,
                'phone' => preg_replace("/^((+7|7|8)?([0-9]){10})$./", "", $this->customf['phone']['value']),
                'firstName' => $this->customf['name']['value'],
                'email' => $this->customf['email']['value']
            ];

            $items = json_encode($items);
            $params = json_encode($params);

            $this->result['payButton'] = "
            <script type='text/javascript'>
                $(document).ready(function(){
                    DCLoans('{$this->setting['dcPartnerId']}', 'delProduct', false, function(result){
                        if (result.status == true) {
                            DCLoans('{$this->setting['dcPartnerId']}', 'addProduct', { products : {$items} }, function(result){
                                if (result.status == true) {
                                    DCLoans('{$this->setting['dcPartnerId']}', 'saveOrder', {$params}, function(result){
                                        //result.status может быть либо false, либо всплывет окошко для оформление кредитных заявок
                                    });
                                }
                            });
                        }
                    });
                })
            </script>";
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function avangarde()
    {
        try {
            $this->setting['avangardPassword'] = 'gPrMPitgsBuZhLjPbeGn';
            $this->setting['avangardLogin'] = '34071';
            if (!$this->setting['avangardLogin'] || !$this->setting['avangardPassword']) {
                throw new Exception("Invalid auth params", 1);
            }
            $dataAvangard = [
                'shop_id' => urlencode($this->setting['avangardLogin']),
                'signature' => mb_strtoupper(md5(mb_strtoupper(md5($this->setting['avangardPassword']) . md5(urlencode($this->setting['avangardLogin'] . $this->message . normalizePriceSber($this->order['totaldelsum'])))))),
                'order_number' => $this->message,
                'order_description' => "Заказ №{$this->message} в магазине " . str_replace('"', "'", $this->current_catalogue['Catalogue_Name']),
                'amount' => normalizePriceSber($this->order['totaldelsum']),
                'back_url' => "http://" . $_SERVER['HTTP_HOST'] . "/cart/",
                'back_url_ok' => "http://" . $_SERVER['HTTP_HOST'] . "/cart/?status=success",
                'back_url_fail' => "http://" . $_SERVER['HTTP_HOST'] . "/cart/?status=fail",
                'language' => "RU"
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.avangard.ru/iacq/post',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($dataAvangard, '', '&')
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            if (preg_match('/(?<=href=")[^"]*/m', $result, $matches)) $payUrl3 = $matches[0];
            if (preg_match('/(?<=ticket=).*/', $payUrl3, $matches)) $ticket = $matches[0];
            if ($ticket) {
                $this->result['payUrl'] = "https://www.avangard.ru/iacq/pay?ticket=" . $ticket;
                $this->result['status'] = 1;
            } else {
                throw new \Exception('No ticket', 10);
            }
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }
        return $this->result;
    }

    public function check()
    {
        $this->result['payUrl'] = "http://{$_SERVER['HTTP_HOST']}/?template=3&order={$this->message}&hash=" . hexorder($this->message) . "";
        $this->result['payButton'] = "<p>Счет на оплату также отправлен на Вашу электронную почту.</p><div class='payButton'><a class='btn-strt-a' href='' onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('{$this->result['payUrl']}', 'schet', 'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,width=890,height=500,resizable=0');this.newWindow.focus();this.newWindow.opener=window;return false;\"><span class='btn-pad-lr'>Распечатать счет на оплату</span></a></div>";
        $this->result['status'] = 1;

        return $this->result;
        // $cart .= "<br>Счет на оплату: <a target=_blank href='{$payUrl}'>{$payUrl}</a>";
    }



    public function ubrr()
    {
        global $DOCUMENT_ROOT, $pathInc2;
        //header('Content-Type: text/xml');

        // корневой сертификат банка
        if (file_exists($DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/bank.crt")) {
            $caFile = $DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/bank.crt";
        }
        // сертификат торговца
        if (file_exists($DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/user.crt")) {
            $certFile = $DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/user.crt";
        }
        // ключ сертификата
        if (file_exists($DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/user.key")) {
            $keyFile = $DOCUMENT_ROOT . $pathInc2 . "/files/ubrr/user.key";
        }
        // пароль ключа (если есть)
        $privateCertPass = $this->setting['ubrrPrivateCertPass'];
        // идентификатор мерчанта
        $mid = $this->setting['ubrrMerchantID'];

        if ($caFile && $certFile && $keyFile && $privateCertPass && $mid) {
            // адрес для отправки запроса
            $serverURI = 'https://91.208.121.69:7443/Exec';

            // исходящее xml_сообщение
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<TKKPG>';
            $xml .= '<Request>';
            $xml .= '<Operation>CreateOrder</Operation>';
            $xml .= '<Language>RU</Language>';
            $xml .= '<Order>';
            $xml .= '<OrderType>Purchase</OrderType>';
            $xml .= '<Merchant>' . $mid . '</Merchant>';
            $xml .= "<Amount>" . normalizePriceSber($this->order['totaldelsum']) . "</Amount>";
            $xml .= '<Currency>643</Currency>';
            $xml .= "<Description>" . "Заказ №{$this->message} в магазине {$this->current_catalogue['Catalogue_Name']}" . "</Description>";
            $xml .= "<ApproveURL>http://" . $_SERVER['HTTP_HOST'] . "/cart/?status=success</ApproveURL>";
            $xml .= "<CancelURL>http://" . $_SERVER['HTTP_HOST'] . "/cart/</CancelURL>";
            $xml .= "<DeclineURL>http://" . $_SERVER['HTTP_HOST'] . "/cart/?status=fail</DeclineURL>";
            $xml .= '</Order>';
            $xml .= '</Request>';
            $xml .= '</TKKPG>';

            $ch = curl_init($serverURI);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
            curl_setopt($ch, CURLOPT_CAINFO, $caFile);

            curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $privateCertPass);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            // ответ TWPG
            $xmlResponse = simplexml_load_string(curl_exec($ch));
            //echo $xmlResponse;
            //echo '<pre>';
            //print_r($xmlResponse);
            $payUrl = $xmlResponse->Response->Order->URL . '?ORDERID=' . $xmlResponse->Response->Order->OrderID . '&SESSIONID=' . $xmlResponse->Response->Order->SessionID;
            //echo $payUrl;
            //echo "<a href='$xmlResponse'></a>"

            // 0 - успешное выполнение запроса
            //echo "<br/>";
            //echo curl_errno($ch);
            $this->result['payUrl'] = $payUrl;
            $this->result['status'] = 1;
            return $this->result;
        } else {
            $this->result['message'] = 'Произошла ошибка';
            return $this->result;
        }
    }
}
