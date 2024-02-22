<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request;

use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\PostConvertor\Form as PostConvertorForm;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\PostConvertor\Json as PostConvertorJson;
use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use RuntimeException;

class Builder
{
    const ORDER_NUMBER_UUID = 1;
    const ORDER_NUMBER_CDEK_NUMBER = 2;
    const ORDER_NUMBER_IM_NUMBER = 3;

    /**
     * Получить токен
     * 
     * @return array
     * 
     * @throws RuntimeException
     */
    public function getToken()
    {
        $method = 'oauth/token';

        $request = (new Request(new Response(), new PostConvertorForm()))
            ->setUrl(Request::API_URL.$method)
            ->addPost('grant_type', 'client_credentials')
            ->addPost('client_id', ToolsAssist::getInstance()->getClientLogin())
            ->addPost('client_secret', ToolsAssist::getInstance()->getClientPassword())
        ;
        $request->withToken = false;

        $response = $request->handle();

        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка получения токена. Cообщение ошибки: %s", $response->getErrorMessage()));
        }

        return $response->normalize();
    }

    /**
     * Получить список пунктов выдачи
     * 
     * @return array
     * 
     * @throws RuntimeException
     */
    public function getPvzList()
    {
        $method = 'deliverypoints';

        $response = (new Request(new Response()))
            ->setUrl(Request::API_URL.$method)
            ->handle()
        ;

        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка получения списка pvz. Cообщение ошибки: %s", $response->getErrorMessage()));
        }

        return $response->normalize();
    }

    /**
     * Расчитать доставку по доступным тарифам
     * 
     * @param array $fromLocation
     * @param array $toLocation
     * @param array $package
     * 
     * @return array
     */
    public function calculateTariffList($fromLocation, $toLocation, $packages)
    {   
        global $setting;
        $method = 'calculator/tarifflist';

        $response = (new Request(new Response(), new PostConvertorJson()))
            ->setUrl(Request::API_URL.$method)
            ->addHeaders(['Content-Type: application/json', 'charset=UTF-8'])
            ->addPost('from_location', $fromLocation)
            ->addPost('to_location', $toLocation)
            ->addPost('packages', $packages)
            ->handle()
        ;        

        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка расчета по доступным тарифам. Cообщение ошибки: %s", $response->getErrorMessage()));
        }



        $res = $response->normalize();

        //Надбавка цены и дней
        foreach ($res['tariff_codes'] as $key => $tariff) {
            $res['tariff_codes'][$key]['delivery_sum']+=($setting['cdek_add_price']?(int)$setting['cdek_add_price']:0);
            $res['tariff_codes'][$key]['calendar_min']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
            $res['tariff_codes'][$key]['calendar_max']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
            $res['tariff_codes'][$key]['period_min']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
            $res['tariff_codes'][$key]['period_max']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
        }
        
       

        return $res;
    }

    /**
     * Расчитать доставку по тарифу
     * 
     * @param array $fromLocation
     * @param array $toLocation
     * @param array $package
     * @param int $tariffCode
     * @param null|array $services
     * 
     * @return array
     */
    public function calculateTariff($fromLocation, $toLocation, $packages, $tariffCode, $services = null)
    {
        global $setting;

        $method = 'calculator/tariff';

        $response = (new Request(new Response(), new PostConvertorJson()))
            ->setUrl(Request::API_URL.$method)
            ->addHeaders(['Content-Type: application/json', 'charset=UTF-8'])
            ->addPost('from_location', $fromLocation)
            ->addPost('to_location', $toLocation)
            ->addPost('packages', $packages)
            ->addPost('tariff_code', $tariffCode)
            ->addPost('services', $services)
            ->handle()
        ;

        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка расчета по тарифу. Cообщение ошибки: %s", $response->getErrorMessage()));
        }


        

        $res = $response->normalize();

        $res['delivery_sum']+=($setting['cdek_add_price']?(int)$setting['cdek_add_price']:0);
        $res['calendar_min']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
        $res['calendar_max']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
        $res['period_min']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
        $res['period_max']+=($setting['cdek_add_days']?(int)$setting['cdek_add_days']:0);
        $res['total_sum']+=($setting['cdek_add_price']?(int)$setting['cdek_add_price']:0);

        return $res;
    }

    /**
     * Получить список населенных пунктов
     * 
     * @param int $page номер страницы
     * @param int $size кол-во записей со строницы
     * @param bool $all собрать все записи
     * 
     * @return array
     * 
     * @throws RuntimeException
     */
    public function getCityList($page = 0, $size = 10000, $all = true)
    {
        $method = 'location/cities';

        $response = (new Request(new Response()))
            ->setUrl(Request::API_URL.$method)
            ->addGet('country_code', 'RU')
            ->addGet('page', $page)
            ->addGet('size', $size)
            ->handle()
        ;
        

        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка получения списка населенных пунктов. Cообщение ошибки: %s", $response->getErrorMessage()));
        }

        $result = $response->normalize();

        // if ($all && count($result) === $size) {
        //     $result = array_merge($result, $this->getCityList($page + 1, $size, $all));
        // }
        
        return $result;
    }

    /**
     * Зарегестрировать заказ
     * 
     * $parameters = [
     * - [Обязательный параметры]
     * - - 'tariff_code' => 'код тарифа', (int)
     * - - 'shipment_point' => 'Код ПВЗ отправки СДЭК', (string) не может использоваться одновременно с fromLocation
     * - - 'delivery_point' => 'Код ПВЗ получения СДЭК', (string) не может использоваться одновременно с toLocation
     * - - 'from_location' => 'Адрес отправления', (array) не может использоваться одновременно с shipmentPoint
     * - - 'to_location' => 'Адрес получения', (array) не может использоваться одновременно с deliveryPoint
     * - - 'recipient' => 'Получатель', (array)
     * - - 'packages' => 'Список информации по местам (упаковкам)' (array)
     * - [Обязательные при условиях]
     * - - 'date_invoice' => 'Дата инвойса', (string) Только для международных заказов
     * - - 'shipper_name' => 'Грузоотправитель', (string) Только для международных заказов
     * - - 'shipper_address' => 'Адрес грузоотправителя', (string) Только для международных заказов
     * - - 'sender' => 'Отправитель', (array) если заказ типа "доставка" (type = 2)
     * - - 'seller' => 'Реквизиты истинного продавца', (array) Только для международных заказов
     * - [Опциональные параметры]
     * - - 'type' => 'Тип заказа', (int)
     * - - 'number' => 'Номер заказка в магазине', (string|int)
     * - - 'comment' => 'Комментарий к заказу', (string)
     * - - 'developer_key' => 'Ключ разработчика', (string)
     * - - 'delivery_recipient_cost' => 'Доп. сбор за доставку, которую ИМ берет с получателя.', (array)
     * - - 'delivery_recipient_cost_adv' => 'Доп. сбор за доставку (которую ИМ берет с получателя) в зависимости от суммы заказа', (array)
     * - - 'services' => 'Дополнительные услуги', (array)
     * - - 'print' => 'Необходимость сформировать печатную форму по заказу', (string)
     * ];
     * 
     * @param array $parameters массив данных для оформления заказа
     * 
     * @return array
     */
    public function orderRegistrate($paramseters)
    {
        $method = 'orders';

        $request = new Request(new Response(), new PostConvertorJson());

        $request->addHeaders(['Content-Type: application/json', 'charset=UTF-8']);
        $request->setUrl(Request::API_URL.$method);
        
        foreach ($paramseters as $key => $value) {
            $request->addPost($key, $value);
        }

        $response = $request->handle();
        
        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка регистрации заказа. Cообщение ошибки: %s", $response->getErrorMessage()));
        }

        return $response->normalize();
    }

    /**
     * Получить информацию о заказе
     * 
     * @param string|int $number номер заказ
     * @param int $type тип номера заказа 1 - uuid в CDEK; 2 - номер в CDEK; 3 - номер в магазине
     * 
     * @return array
     */
    public function getOrderInfo($number, $type = 1)
    {
        $method = 'orders';

        $request = (new Request(new Response()));
        
        switch ($type) {
            case self::ORDER_NUMBER_UUID:
                $request->setUrl(Request::API_URL.$method.'/'.$number);
                break;
            case self::ORDER_NUMBER_CDEK_NUMBER:
                $request
                    ->setUrl(Request::API_URL.$method)
                    ->addGet('cdek_number', $number)
                ;
                break;
            case self::ORDER_NUMBER_IM_NUMBER:
                $request
                    ->setUrl(Request::API_URL.$method)
                    ->addGet('im_number', $number)
                ;
                break;
        }
        
        $response = $request->handle();
        
        if ($response->isError()) {
            throw new RuntimeException(sprintf("Ошибка регистрации заказа. Cообщение ошибки: %s", $response->getErrorMessage()));
        }

        return $response->normalize();
    }
}