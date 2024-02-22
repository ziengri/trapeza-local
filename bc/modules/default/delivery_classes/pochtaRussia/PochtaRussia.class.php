<?php

class PochtaRussia
{
    public $text = [
        'recalcDelivery' => 'Необходимо произвести перерасчет',
        'error' => 'Ошибка расчета доставки',
        'cartEmpty' => 'Корзина пуста',
        'noSelectChooser' => 'Не выбран способ доставки' 
    ];

    public function __construct()
    {
        global $setting;
        $this->widgetID = $setting['PR_widgetID'];
        $this->checked = $setting['PR_checked'];
        $this->defaultWeight = $setting['PR_defaultWeight'];
    }

    public function getDeliveryInfo($address, $deliveryType, $type)
    {
        unset($_SESSION['pochta_russia']);
        $textArr = [
            'pvzType' => [
                'russian_post' => 'Отделение почтовой связи Почты России',
                'postamat' => 'Почтомат'
            ],
            'mailType' => [
                'POSTAL_PARCEL' => 'Посылка нестандартная',
                'PARCEL_CLASS_1' => 'Посылка первого класса',
                'ONLINE_PARCEL' => 'Посылка онлайн',
                'ECOM' => 'ЕКОМ',
                'ECOM_MARKETPLACE' => 'ЕКОМ Маркетплейс',
                'ONLINE_COURIER' => 'Курьер онлайн',
                'EMS' => 'Отправление EMS',
                'BUSINESS_COURIER' => 'Бизнес курьер',
                'BUSINESS_COURIER_ES' => 'Бизнес курьер экспресс',
            ]
        ];

        $info = "Способ доставки: {$textArr['pvzType'][$deliveryType]}.<br/>Тип: {$textArr['mailType'][$type]}.<br/>По адресу: {$address}<br/>";
        return $info;
    }

    public function recalcDelivery()
    {
        if ($_SESSION['pochta_russia']['success']) {
            $description = $this->text['recalcDelivery'];
        } else {
            $description = $this->text['noSelectChooser'];
        }

        return $this->selectChooser(['success' => 0, 'description' => $description]);
    }
    public function getParamItems()
    {
        try {
            if (!$_SESSION['cart']['items']) {
                throw new Exception($this->text['cartEmpty'], 1);
            }
            $ordersum = 0;
            $weightAll = 0;
            foreach ($_SESSION['cart']['items'] as $itemID => $itemValue) {
                $itemObj = Class2001::getItemById($itemID);
                $weight = ($itemObj->ves ? $itemObj->ves : $this->defaultWeight);
                $ordersum += $itemObj->price * $itemValue['count'];
                $weightAll += $weight * $itemValue['count'];
            }
            $result = ['success' => true, 'data' => ['weight' => $weightAll, 'ordersum' => $ordersum, 'widgetID' => $this->widgetID]];
        } catch (\Exception $e) {
            $result = ['success' => false, 'description' => $e->getMessage()];
        }
        return $result;
    }

    public function selectChooser($param)
    {
        try {

            if (!$param['success']) {
                throw new Exception($param['description'], 1);
            }
            $_SESSION['cart']['delivery']['sum'] = $_SESSION['cart']['delivery']['sum_result'] = (float) str_replace([',', ' '], ['.', ''], ($param['price'] / 100));
            $_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['delivery']['sum'] + $_SESSION['cart']['totalSumDiscont'];
            
            $_SESSION['cart']['delivery']['assist'] = [
                'type' => 'pochta_russia',
                'success' => true,
                'description' => $param['description'],
                'price' => $_SESSION['cart']['delivery']['sum'],
                'totaldelsum' => $_SESSION['cart']['totaldelsum'],
                'mailType' => $param['mailType'],
                'pvzType' => $param['pvzType'],
                'pvzCode' => $param['pvzCode'],
            ];

            $_SESSION['pochta_russia'] = $_SESSION['cart']['delivery']['assist'];

        } catch (\Exception $e) {

            $_SESSION['cart']['delivery']['sum'] = $_SESSION['cart']['delivery']['sum_result'] = 0;
            $_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['delivery']['sum'] + $_SESSION['cart']['totalSumDiscont'];

            $_SESSION['cart']['delivery']['assist'] = [
                'type' => 'pochta_russia',
                'success' => false,
                'description' => $e->getMessage(),
                'price' => $_SESSION['cart']['delivery']['sum'],
                'totaldelsum' => $_SESSION['cart']['totaldelsum']
            ];
        }

        return $_SESSION['cart']['delivery']['assist'];
    }
    public static function on()
    {
        $PR = new PochtaRussia();
        if ($PR->widgetID && $PR->checked) {
            return true;
        } else {
            return false;
        }
    }
}

