<?php

namespace turbo;

class OrderTurbo
{
    protected $nc_core;
    protected $db;
    protected $current_catalogue;
    protected $catalogue;
    protected $text = [
        'PREPAID' => 'Предоплата при оформлении заказа.',
        'POSTPAID' => 'Оплата при получении заказа.',
        'YANDEX' => 'Банковской картой.'
    ];

    public function __construct()
    {
        global $setting;
        $this->nc_core = \nc_Core::get_object();
        $this->db = $this->nc_core->db;
        $this->current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $this->catalogue =  $this->current_catalogue['Catalogue_ID'];
        $this->setting = $setting;

        $this->checkHeaderToken();
    }

    public function getAction($action)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), 1);
            if (!is_array($data) || empty($data)) {
                file_put_contents(__DIR__ . '/queryNoData.txt', print_r(apache_request_headers(), 1) . print_r(json_decode(file_get_contents('php://input'), 1), 1));
                throw new \Exception("Not order body", 400);
            }
            switch ($action) {
                case 'accept':
                    $this->actionAccept($data);
                    break;
                case 'status':
                    $this->actionStatus($data);
                    break;
                default:
                    throw new \Exception("Not methode " . $action, 400);
                    break;
            }
        } catch (\Exception $e) {
            $this->setRespons($e->getCode(), $e->getMessage());
        }
    }
    private function actionAccept($data)
    {
        file_put_contents(__DIR__ . '/queryLastAccept.txt', print_r(apache_request_headers(), 1) . print_r(json_decode(file_get_contents('php://input'), 1), 1));

        $id = $data['order']['id'];
        $idOrderDB = $this->db->get_var("SELECT Message_ID FROM Message2005 WHERE orderId = '{$id}' AND Catalogue_ID = '{$this->catalogue}'");
        if (!$idOrderDB) {
            $items = [];
            $totalSum = 0;

            foreach ($data['order']['items'] as $item) {
                $itemDB = $this->db->get_row("SELECT `name`, `Message_ID` FROM Message2001 WHERE Message_ID = '{$item['offerId']}'", 'ARRAY_A');

                if (!$itemDB) {
                    $this->setRespons(200, json_encode(['order' => ['accepted' => false, 'reason' => 'OUT_OF_DATE']]));
                }
                $item['price'] = $this->toPrice($item['price']);
                $sum = $item['price'] * $item['count'];
                $totalSum += $sum;
                $items[$itemDB['Message_ID']] = [
                    'id' => $itemDB['Message_ID'],
                    'name' => $itemDB['name'],
                    'discont' => 0,
                    'pricefirst' => $item['price'],
                    'price' => $item['price'],
                    'count' => $item['count'],
                    'sum' => $sum,
                ];
            }

            $deliveryPrice = $this->toPrice($data['order']['delivery']['price']);
            $order = [
                'items' => $items,
                'totalSum' => $totalSum,
                'totalSumDiscont' => $totalSum,
                'discont' => 0,
                'delivery' => [
                    'addressid' => 0,
                    'free' => 0,
                    'sum' => $deliveryPrice,
                ],
                'totaldelsum' => $totalSum + $deliveryPrice
            ];
            $orderJson = json_encode($order);

            $notes = "Тип оплаты заказа: ({$this->text[$data['order']['paymentType']]})\r\nКомментарий к заказу.\r\n{$data['order']['notes']}";

            $infoOrder = [
                'name' => $data['order']['user']['name'],
                'phone' => $data['order']['user']['phone'],
                'email' => $data['order']['user']['email'],
                'comment' => $notes,
            ];

            $customf = [];

            foreach (json_decode($this->setting['cartForm'], 1) as $row) {
                foreach ($infoOrder as $key => $val) {
                    if (in_array($row['name']['value'], explode('|', $key))) {
                        $customf[$row['name']['value']] = [
                           'value' => $val,
                           'name' => $row['label']['value']
                        ];
                    }
                }
            }

            $customfJson = json_encode($customf);

            $OrderSub = $this->db->get_row("SELECT Subdivision_ID as sub, Sub_Class_ID as cc FROM Sub_Class WHERE EnglishName = 'cart' AND Catalogue_ID = {$this->catalogue}", 'ARRAY_A');
            
            $ptiority = $this->db->get_var("SELECT COUNT(*) FROM Message2005 WHERE Catalogue_ID = '{$this->catalogue}'") + 1;

            $this->db->query("INSERT INTO Message2005 (
                    Subdivision_ID, 
                    Sub_Class_ID, 
                    Checked, 
                    Catalogue_ID, 
                    fio, 
                    phone, 
                    email, 
                    orderlist, 
                    textcomment,
                    orderId,
                    customf,
                    Priority,
                    Created
                ) VALUES (
                    '{$OrderSub['sub']}',
                    '{$OrderSub['cc']}',
                    '1',
                    '{$this->catalogue}',
                    '{$data['order']['user']['name']}',
                    '{$data['order']['user']['phone']}',
                    '{$data['order']['user']['email']}',
                    '{$orderJson}',
                    '{$notes}',
                    '{$id}',
                    '{$customfJson}',
                    '{$ptiority}',
                    NOW()
                )");

            $idOrderDB = $this->db->insert_id;
            if (!$idOrderDB) {
                $this->setRespons(500);
            }
        }


        $this->setRespons(200, json_encode(['order' => ['accepted' => true, 'id' => $idOrderDB]]));
    }
    private function actionStatus($data)
    {
        file_put_contents(__DIR__ . '/queryLastStatus.txt', print_r(apache_request_headers(), 1) . print_r(json_decode(file_get_contents('php://input'), 1), 1));
        
        $this->setRespons(200);
    }
    private function toPrice($price)
    {
        return str_replace(',', '.', $price) + 0;
    }
    public function setRespons($code, $body = '')
    {
        header('Content-Type: application/json;charset=utf-8');
        http_response_code($code);
        if ($body) {
            echo $body;
        }
        die;
    }
    public function checkHeaderToken()
    {
        $authorization = (isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : ''));
        if (!$authorization || !is_array($this->setting['turbo_tokens']) || empty($this->setting['turbo_tokens'])) {
            $this->setRespons(403);
        }

        $tokens = [];

        foreach ($this->setting['turbo_tokens'] as $row) {
            $tokens[$row['token']] = $row['name'];
        }
        if (!isset($tokens[$authorization])) {
            $this->setRespons(403);
        }
    }
}
