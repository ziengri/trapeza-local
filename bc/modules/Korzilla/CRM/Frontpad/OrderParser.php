<?php

namespace App\modules\Korzilla\CRM\Frontpad;

use Class2001;
use Class2005;
use DateTime;

class OrderParser
{
    protected $order;
    protected $orderList;
    protected $formData;
    protected $delivery;
    protected $payment;
    protected $products;

    public function __construct($order)
    {
        $this->order = $order;
    }
    
    /**
     * product – массив артикулов товаров [ОБЯЗАТЕЛЬНЫЙ ПАРАМЕТР]
     * 
     * @return array
     */
    public function getProduct(): array
    {
        $result = [];
        foreach ($this->getProducts() as $num => $product) {
            $result[$num] = $product['code'];
        }
        return $result;
    }

    /**
     * product_kol – массив количества товаров [ОБЯЗАТЕЛЬНЫЙ ПАРАМЕТР]
     * 
     * @return array
     */
    public function getProductCount(): array
    {
        $result = [];
        foreach ($this->getProducts() as $num => $product) {
            $result[$num] = $product['count'];
        }
        return $result;
    }

    /**
     * product_mod – массив модификаторов товаров, где значение элемента 
     * массива является ключом родителя (товара в который добавлен модификатор)
     * 
     * @return array|null
     */
    public function getProductMod()
    {
        $result = [];
        foreach ($this->getProducts() as $num => $product) {
            if ($product['isModificator']) $result[$num] = $product['parent'];
        }
        return $result ?: null;
    }

    /**
     * product_price – массив цен товаров (установка цены при заказе через API возможна только для товаров с включенной опцией "Изменение цены при создании заказа"
     * 
     * @return array|null
     */
    public function getProductPrice()
    {
        $result = [];
        foreach ($this->getProducts() as $num => $product) {
            $result[$num] = $product['price'];
        }
        return $result;
    }

    /**
     * score – баллы для оплаты заказа
     * 
     * @return float|null
     */
    public function getScore()
    {
        return null;
    }

    /**
     * sale – скидка, положительное, целое число от 1 до 100
     * 
     * @return int|null
     */
    public function getSale()
    {
        return null;
    }

    /**
     * sale_amount - скидка суммой, назначить к заказу можно один тип скидки - процентную или суммой
     */
    public function getSaleAmount()
    {
        return null;
    }

    /**
     * card – карта клиента, положительное, целое число до 16 знаков
     * 
     * @return int|null
     */
    public function getClientCard()
    {
        return null;
    }

    /**
     * street – улица, длина до 50 знаков
     * 
     * @return string|null
     */
    public function getAddressStreet()
    {
        if ($street = $this->getFormData()['street']['value'] ?? null) {
            $street = substr($street, 0, 50);
        }
        return $street;
    }

    /**
     * home – дом, длина до 50 знаков
     * 
     * @return string|null
     */
    public function getAddressHome()
    {
        if ($home = $this->getFormData()['home']['value'] ?? null) {
            $home = substr($home, 0, 50);
        }
        return $home;
    }

    /**
     * pod – подъезд, длина до 2 знаков
     * 
     * @return string|null
     */
    public function getAddressPorch()
    {
        if ($porch = $this->getFormData()['porch']['value'] ?? null) {
            $porch = substr($porch, 0, 2);
        }
        return $porch;
    }

    /**
     * et – этаж, длина до 2 знаков
     * 
     * @return string|null
     */
    public function getAddressFloor()
    {
        if ($floor = $this->getFormData()['floor']['value'] ?? null) {
            $floor = substr($floor, 0, 2);
        }
        return $floor;
    }

    /**
     * apart – квартира, длина до 50 знаков
     * 
     * @return string|null
     */
    public function getAddressApartment()
    {
        if ($apart = $this->getFormData()['apart']['value'] ?? null) {
            $apart = substr($apart, 0, 50);
        }
        return $apart;
    }

    /**
     * phone – телефон, длина до 50 знаков
     * 
     * @return string|null
     */
    public function getClientPhone()
    {
        if ($phone = $this->getFormData()['phone']['value'] ?? null) {
            $phone = substr($phone, 0, 50);
        }
        return $phone;
    }

    /**
     * mail – адрес электронной почты, длина до 50 знаков, 
     * доступно только с активной опцией автоматического сохранения клиентов
     * 
     * @return string|null
     */
    public function getClientEmail()
    {
        if ($email = $this->getFormData()['email']['value'] ?? null) {
            $email = substr($email, 0, 50);
        }
        return $email;
    }

    /**
     * descr – примечание, длина до 100 знаков
     * 
     * @return string|null
     */
    public function getComment()
    {
        $comment = '';

        if ($clientComment = trim($this->getFormData()['comments']['value'] ?? null)) {
            $comment .= $comment ? PHP_EOL : '';
            $comment .= "Комментарий клиента: {$clientComment}.";
        }
        
        if ($sdacha = trim($this->getFormData()['sdacha']['value'] ?? null)) {
            $comment .= $comment ? PHP_EOL : '';
            $comment .= "Сдача с: {$sdacha}.";
        }
        
        $comment .= $comment ? PHP_EOL : '';
        $comment .= "Заказ с сайта №{$this->order['Message_ID']}.";

        if ($paymentName = trim($this->getPayment('name'))) {
            $comment .= $comment ? PHP_EOL : '';
            $comment .= "Способ оплаты: {$paymentName}.";
        }

        
        if ($deliveryName = trim($this->getDelivery('name'))) {
            $comment .= $comment ? PHP_EOL : '';
            $comment .= "Способ доставки: {$deliveryName}.";
        }

        return $comment;
    }

    /**
     * name – имя клиента, длина до 50 знаков
     * 
     * @return string|null
     */
    public function getClientName()
    {
        return $this->order['fio'] ? substr($this->order['fio'], 0, 50) : null;
    }

    /**
     * pay – отметка оплаты заказа, значение можно посмотреть в справочнике “Варианты оплаты”
     * 
     * @return string|null
     */
    public function getPay()
    {
		$paymentArt = trim($this->getPayment('frontpadType'));
		
        return $paymentArt ? $paymentArt : null;
    }

    /**
     * certificate – номер сертификата
     * 
     * @return string|null
     */
    public function getCerificate()
    {
        return null;
    }

    /**
     * person – количество персон, длина 2 знака. Обратите внимание, 
     * привязка "автосписания" к количеству персон, переданному через api, не осуществляется
     * 
     * @return string|null
     */
    public function getPersonCount()
    {
        return null;
    }

    /**
     * datetime – время “предзаказа”, указывается в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС, 
     * например 2016-08-15 15:30:00. Максимальный период предзаказа - 30 дней от текущей даты
     * 
     * @return string|null
     */
    public function getDateTime()
    {
        if (!$datetime = $this->formData['datetime']['value'] ?? '') {
            return null;
        }

        return (new DateTime($datetime))->format('Y-m-d H:i:s');
    }

    protected function getOrderList(): array
    {
        return $this->orderList ?? ($this->orderList = orderArray($this->order['orderlist']) ?: []);
    }

    protected function getFormData(): array
    {
        return $this->formData ?? ($this->formData = orderArray($this->order['customf']) ?: []);
    }

    protected function getDelivery(string $key = null)
    {
        if (!isset($this->delivery)) {
            $this->delivery = false;

            if (!empty($this->order['delivery'])) {
                $this->delivery = Class2005::getListName('delivery', $this->order['delivery']) ?: false;
            }
        }
        
        if (!$this->delivery) {
            return null;
        }

        return $key ? ($this->delivery[$key] ?? null) : $this->delivery;
    }

    protected function getPayment(string $key = null)
    {
        if (!isset($this->payment)) {
            $this->payment = false;
            if (!empty($this->order['payment'])) {
                $this->payment = Class2005::getListName('payment', $this->order['payment']) ?: false;
            }
        }
        
        if (!$this->payment) {
            return null;
        }

        return $key ? ($this->payment[$key] ?? null) : $this->payment;
    }

    protected function getProducts()
    {
        if (!isset($this->products)) {
            $products = [];
            $num = 0;

            foreach ($this->getOrderList()['items'] ?? [] as $item) {
                $productObj = Class2001::getItemById($item['id']);
                if (!$productObj || !$productObj->code) continue;

                $productNum = $num;

                $products[$productNum] = [
                    'code' => $productObj->code,
                    'count' => $item['count'],
                    'price' => $item['priceOrigin'],
                    'isModificator' => false,
                ];

                foreach ($item['modificators'] ?? [] as $modificator) {
                    $productObj = Class2001::getItemById($modificator['id']);

                    if (!$productObj || !$productObj->code) continue;

                    $num++;

                    $products[$num] = [
                        'code' => $productObj->code,
                        'count' => $modificator['count'],
                        'price' => $modificator['price'],
                        'isModificator' => true,
                        'parent' => $productNum,
                    ];

                }
                $num++;
            }

            # доставка
            if ($this->getDelivery('art')) {
                $products[$num] = [
                    'code' => $this->getDelivery('art'),
                    'count' => 1,
                    'price' => $this->getOrderList()['delivery']['sum_result'] ?? 0,
                    'isModificator' => false,
                ];
                $num++;
            }

            $this->products = $products;
        }

        return $this->products;
    }
}