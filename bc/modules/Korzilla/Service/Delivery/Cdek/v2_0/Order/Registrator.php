<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Builder;
use Exception;

/**
 * Класс регистрации заказа в CDEK
 * 
 * @link https://api-docs.cdek.ru/29923926.html описание регистрации закза v 2.0
 */
class Registrator
{
    /**
     * @var array обязательный
     */
    private $packages;
    /**
     * @var array обязательный
     */
    private $recipient;
    /**
     * @var int обязательный
     */
    private $tariff_code;
    /**
     * @var int обязательный, нельзя использовать вместе с deliveryPoint
     */
    private $to_location;
    /**
     * @var int обязательный, нельзя использовать вместе с shipment_point
     */
    private $from_location;
    /**
     * @var string обязательный, нельзя использовать вместе с fromLocation
     */
    private $shipment_point;
    /**
     * @var string обязательный, нельзя использовать вместе с toLocation
     */
    private $delivery_point;

    /**
     * @var array обязательный, если тип закза "доставка" ($type == 2)
     */
    private $sender;
    /**
     * @var array обязательный, если заказ международный
     */
    private $seller;
    /**
     * @var string обязательный, если заказ международный
     */
    private $date_invoice;
    /**
     * @var string обязательный, если заказ международный
     */
    private $shipper_name;
     /**
     * @var string обязательный, если заказ международный
     */
    private $shipper_address;

    /**
     * @var int опциоанльный
     */
    private $type;
    /**
     * @var string опциоанльный
     */
    private $print;    
    /**
     * @var int|string опциоанльный
     */
    private $number;
    /**
     * @var string опциоанльный
     */
    private $comment;
    /**
     * @var array опциоанльный
     */
    private $services;
    /**
     * @var string опциоанльный
     */
    private $developer_key;
    /**
     * @var array опциоанльный
     */
    private $delivery_recipient_cost;
    /**
     * @var array опциоанльный
     */
    private $delivery_recipient_cost_adv;

    /**
     * Установить код тарифа
     * 
     * @param int $code
     * 
     * @return static
     */
    public function setTariffCode($code)
    {
        $this->tariff_code = $code;
        return $this;
    }

    /**
     * Установить пункт ПВЗ 
     * 
     * Установка кода пункта ПВЗ на который будет доставлен заказ 
     * для отправки (точка отправления)
     * 
     * @param string $code код ПВЗ
     * 
     * @return static
     */
    public function setShipmentPoint($code)
    {
        $this->shipment_point = $code;
        return $this;
    }

    /**
     * Установить пункт ПВЗ 
     * 
     * Установка кода пункта ПВЗ на который будет доставлен заказ 
     * для выдачи получателю (точка прибытия)
     * 
     * @param string $code код ПВЗ
     * 
     * @return static
     */
    public function setDeliveryPoint($code)
    {
        $this->delivery_point = $code;
        return $this;
    }

    /**
     * Установить точку отправки
     * 
     * Установка точки отправления откуда курьер заберет заказ
     * 
     * @param array $location
     * 
     * @return static
     */
    public function setFromLocation($location)
    {
        $this->from_location = $location;
        return $this;
    }

    /**
     * Установить точку прибытия
     * 
     * Установка точки прибытия куда курьер доставит заказ
     * 
     * @param array $location
     * 
     * @return static
     */
    public function setToLocation($location)
    {
        $this->to_location = $location;
        return $this;
    }

    /**
     * Установить получателя
     * 
     * @param array $recipient
     * 
     * @return static
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * Установить список продуктов
     * 
     * $products = [
     *      'weight' => 'Общий вес (в граммах)',
     *      'length' => 'Габариты упаковки. Длина (в сантиметрах)',
     *      'width' => 'Габариты упаковки. Ширина (в сантиметрах)',
     *      'height' => 'Габариты упаковки. Высота (в сантиметрах)',
     *      'count' => 'количество'
     * ];
     * 
     * @param array $products
     * 
     * @return static
     */
    public function setProducts($products)
    {
        $this->packages = ConvertorProducts::convert($products);
        return $this;
    }

    /**
     * Установить дату инвойса
     * 
     * @param \DateTimeInterface $date
     * 
     * @return static
     */
    public function setDateInvoice($date)
    {
        $this->date_invoice = $date->format('Y-m-d');
        return $this;
    }

    /**
     * Установить имя грузоотправителя
     * 
     * @param string $name
     * 
     * @return static
     */
    public function setShipperName($name)
    {
        $this->shipper_name = $name;
        return $this;
    }

    /**
     * Установить адрес грузоотправителя
     * 
     * @param string $address
     * 
     * @return static
     */
    public function setShipperAddress($address)
    {
        $this->shipper_address = $address;
        return $this;
    }

    /**
     * Установить отправителя
     * 
     * @param array $sender
     * 
     * @return static
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Установить реквизиты продавца
     * 
     * @param array $seller
     * 
     * @return static
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * Установить тип заказа
     * 
     * @param int $type
     * 
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Установить номер заказа в магазине
     * 
     * @param int|string $number
     * 
     * @return static
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Установить комментарий к заказу
     * 
     * @param string $comment
     * 
     * @return static
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Установить ключ разработчика
     * 
     * @param string $key
     * 
     * @return static
     */
    public function setDeveloperKey($key)
    {
        $this->developer_key = $key;
        return $this;
    }

    /**
     * Установить доп. сбор за доставку
     * 
     * Установка доп. сбора за доставку, которую ИМ берет с получателя
     * 
     * @param array $cost
     * 
     * @return static
     */
    public function setDeliveryRecipientCost($cost)
    {
        $this->delivery_recipient_cost = $cost;
        return $this;
    }

    /**
     * Установит доп. сбор за доставку
     * 
     * Установка доп. сбора за доставку, которую ИМ берет с получателя,  в зависимости от суммы заказа
     * 
     * @param array $cost
     * 
     * @return static
     */
    public function setDeliveryRecipientCostAdv($cost)
    {
        $this->delivery_recipient_cost_adv = $cost;
        return $this;
    }

    /**
     * Установить доп. услуги
     * 
     * @param array $services
     * 
     * @return static
     */
    public function setServices($services)
    {
        $this->services = $services;
        return $this;
    }

    /**
     * Установить необходимость сформировать печатную форму по заказу
     * 
     * @param string $print
     * 
     * @return static
     */
    public function setPrint($print)
    {
        $this->print = $print;
        return $this;
    }

    /**
     * Выполнить регистрацию закза
     * 
     * @return array
     */
    public function handle()
    {
        $this->isValid();

        $parameters = [];

        if (ToolsAssist::getInstance()->getTariffType($this->tariff_code, 'fromType') === 'door') {
            $parameters['from_location'] = $this->from_location;
        } else {
            $parameters['shipment_point'] = $this->shipment_point;
        }

        if (ToolsAssist::getInstance()->getTariffType($this->tariff_code, 'pointType') === 'door') {
            $parameters['to_location'] = $this->to_location;
        } else {
            $parameters['delivery_point'] = $this->delivery_point;
        }

        $ignore = [
            'from_location' => 1,
            'delivery_point' => 1,
            'shipment_point' => 1,
            'to_location' => 1,
        ];
        foreach ($this as $key => $value)
        {
            if (!isset($ignore[$key]) && $value) $parameters[$key] = $value;
        }

        return (new Builder())->orderRegistrate($parameters);
    }

    /**
     * Проверить валидность заполненых данных
     * 
     * @return bool
     */
    private function isValid()
    {
        if (!isset($this->tariff_code)) {
            throw new Exception('Не установлен код тарифа');
        }
        if (!isset($this->shipment_point) && !isset($this->from_location)) {
            throw new Exception('Не установлена точка отправки');
        }
        if (!isset($this->delivery_point) && !isset($this->to_location)) {
            throw new Exception('Не установлена точка прибытия');
        }
        if (!isset($this->recipient)) {
            throw new Exception('Не установлены данные о получателе');
        }
        if (!isset($this->packages)) {
            throw new Exception('Не установлена донные об упаковках');
        }
        if (2 === ($this->type ?? 1) && !isset($this->sender)) {
            throw new Exception('Не установлен отпарвитель для заказа типа "доставка"');
        }

        return true;
    }
}