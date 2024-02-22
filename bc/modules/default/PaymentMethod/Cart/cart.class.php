<?php

class Cart
{
    /**
     * Получить html заказа
     *
     * @param integer $message
     * @return string
     */
    public function getCartHtml($message): string
    {
        if (is_numeric($message)) {
            $params = $this->getCartParams($message);
        } elseif (is_array($message)) {
            $params = $message;
        } else {
            return '';
        }
        return $this->renderCart($params);
    }

    public function getCartParams(int $message): array
    {
        global $db, $setting, $current_catalogue, $settingCont, $nc_core;

        $cartData = $db->get_row("SELECT * FROM Message2005 WHERE `Message_ID` = '{$message}'", ARRAY_A);

        if (!$current_catalogue) {
            $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        }

        $order = orderArray($cartData['orderlist']);
        $itemParams = [];
        foreach ($order['items'] as $itemID => $item) {
            $itemParam = $item;

            $img = $db->get_row("SELECT `Preview`, `Path` FROM Multifield WHERE Message_ID = '{$item['id']}' AND Field_ID = '2353' ORDER BY `Priority` LIMIT 0,1", ARRAY_A);
            if ($img['Preview']) {
                $itemParam['img'] = "http://{$_SERVER['HTTP_HOST']}{$img['Preview']}";
            }

            $fullLink = nc_message_link($itemID, 2001);
            $itemParam['fullLink'] = "http://{$_SERVER['HTTP_HOST']}{$fullLink}";

            $itemParams[] = $itemParam;
        }

        $deliveryInfo = '';
        if ($cartData['delivery']) {
            $deliveryvalue = ($order['delivery']['sum_result'] ? $order['delivery']['sum_result'] : 0);
            if ($cartData['deliveryAssist']) {
                $deliveryAssist = orderArray($cartData['deliveryAssist']);
                switch ($deliveryAssist['type']) {
                    case 'cdek':
                        $cdek = new Cdek();
                        $deliveryname = $cdek->name;
                        $deliveryInfo = '<br/>'.$cdek->getDeliveryInfo($deliveryAssist['tariffid'], $deliveryAssist['pvzcode']);
                        break;
                    default:
                        $deliveryname = "<b>{$deliveryAssist['name']}</b> - {$deliveryAssist['description']}";
                        break;
                }
            } else {
                $deliveryname = ($order['delivery']['type'] ? $order['delivery']['type'] : $order['delivery']['name']);
            }
        }
        $user = [];
        foreach (orderArray($cartData['customf']) as $key => $p) {
            $name = $p['name'];

            if(is_array($p['value'])){ # чекбоксы
                foreach ($p['value'] as $vl) {
                    $value .= ($value ? ", ": "").$vl;
                }
            }else{ # остальные поля
                $value = $p['value'];
            }

            # Юр.лицо
            if(stristr($name, "(Юр.лицо)") && !$cartData['usertype']) continue;

            $name = str_replace("(Юр.лицо)", "", $name);
            $user[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        if ($cartData['payment']) {
            $paymentname = Class2005::getListName('payment', $cartData['payment'], 'name');
        }

        if (!$settingCont) $settingCont = $db->get_row("SELECT a.*, a.Message_ID AS mesid FROM Message2024 AS a, Subdivision AS b WHERE b.Catalogue_ID = '{$current_catalogue['Catalogue_ID']}' AND a.Subdivision_ID=b.Subdivision_ID ORDER BY a.Created LIMIT 0,1", ARRAY_A);

        return [
            'dateCreated' => date("d.m.Y H:i", strtotime($cartData['Created'])),
            'order' => orderArray($cartData['orderlist']),
            'user' => $user,
            'deliveryAssist' => orderArray($cartData['deliveryAssist']),
            'delivery' => $cartData['delivery'],
            'items' => $itemParams,
            'payment' => $cartData['payment'],
            'Message_ID' => $cartData['Message_ID'],
            'currency' => ($setting['lists_texts']['currency']['checked'] ? $setting['lists_texts']['currency']['name'] : "руб"),
            'totalSum' => $order['totalsum'],
            'deliveryname' => $deliveryname,
            'deliveryInfo' => $deliveryInfo,
            'deliveryvalue' => $deliveryvalue,
            'totalSumToPay' => $cartData['totalSum'],
            'paymentname' => $paymentname,
            'siteName' => $current_catalogue['Catalogue_Name'],
            'phone' => $settingCont['phone']
        ];
    }
    /**
     * Получить email формы оплаты
     *
     * @param int $message
     * @return string
     */
    public function getMailInOrder($message): string
    {
        global $db;

        $cartData = $db->get_row("SELECT email, customf FROM Message2005 WHERE `Message_ID` = '{$message}'", ARRAY_A);
        $res = '';
        if (!empty($cartData['email'])) {
            $res = $cartData['email'];
        } else {
            $mailName = ['email'];
            foreach (orderArray($cartData['customf']) as $key => $value) {
                if (in_array($key, $mailName)) {
                    $res = $value['value'];
                    break;
                }
            }
        }
        return $res;
    }

    /**
     * Рендер html письма
     *
     * @param array $params
     * @param string $view
     * @return string
     */
    public function renderCart(array $params, string $view = ''): string
    {
        ob_start();
        if (is_array($params)) {
            extract($params);
        }
        if (empty($view)) {
            $view = $_SERVER['DOCUMENT_ROOT'] . '/template/class/2005/cart.html';
        }
        if (!file_exists($view)) {
            throw new Exception(printf("File not exist %s", $view), 500);
        }
        require $view;
        return ob_get_clean();
    }

}
