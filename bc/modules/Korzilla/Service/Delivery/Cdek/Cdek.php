<?php 
/**
 * Модуль работы со CDEK
 * 
 * Реализован паттерн Facade https://refactoring.guru/design-patterns/facade
 * Документация API https://api-docs.cdek.ru/
 * 
 * @author Хрулёв Олег 
 */
namespace App\modules\Korzilla\Service\Delivery\Cdek;

use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order\Calculator;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order\Registrator as OrderRegistrator;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Pvz\Collection as PvzCollection;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Builder as RequestBulder;
use App\modules\Korzilla\ToolsAssist\SingletonTrait;
use Exception;
use RuntimeException;

class Cdek
{
    use SingletonTrait;
    
    /**
     * Модуль включен
     * 
     * @return bool
     */
    public function isOn()
    {
        try {
            $tools = ToolsAssist::getInstance();
            return $tools->getClientLogin() && $tools->getClientPassword() && $tools->isChecked();
        } catch (Exception $e){}

        return false;
    }

    /**
     * Расчитать доставку по всем доступным тарифам
     * 
     * @param CalculatorDataInterface $data
     * 
     * @return array
     */
    public function calculateTariffList($data)
    {
        return (new Calculator())
            ->setFromLocation(['code' => ToolsAssist::getInstance()->getFromCityCodeDefault()])
            ->setToLocation(['code' => $data->getDeliveryCityCode()])
            ->setProducts($data->getProducts())
            ->calculateTariffList()
        ;
    }

    /**
     * Расчитать доставку по  тарифу
     * 
     * @param CalculatorDataInterface $data
     * @param int $tariffCode
     * 
     * @return array
     */
    public function calculateTariff($data, $tariffCode)
    {
        return (new Calculator())
            ->setFromLocation(['code' => ToolsAssist::getInstance()->getFromCityCodeDefault()])
            ->setToLocation(['code' => $data->getDeliveryCityCode()])
            ->setProducts($data->getProducts())
            ->setServices($data->getServices())
            ->calculateTariff($tariffCode)
        ;
    }

    /**
     * Получить данные для работы карты на фронте
     * 
     * @return array
     */
    public function getPvzData()
    {
        $cache = ToolsAssist::getInstance()->getRepository()->getSiteDir().'/pvzdata_cache.json';

        if (file_exists($cache) && filectime($cache) + 300 > date('U')) {
            $result = json_decode(file_get_contents($cache), true);
        }

        if (empty($result)) {
            $result = [];

            foreach (PvzCollection::getInstance()->iterator() as $pvzCode => $pvz) {
                if (!$pvz->isHandout()) continue;
                $result['pvz'][$pvzCode] = [
                    'code' => $pvzCode,
                    'name' => $pvz->getName(),
                    'address' => $pvz->getAddressFull(),
                    'cityCode' => $pvz->getCityCode(),
                    'coord' => ['x' => $pvz->getCoordinates()[0], 'y' => $pvz->getCoordinates()[1]],
                    'workTime' => $pvz->getWorkTime(),
                    'type' => strtolower($pvz->getType()),
                ];
                
                $result['city'][$pvz->getCityCode()] = [
                    'code' => $pvz->getCityCode(),
                    'name' => $pvz->getCityName(),
                    'coord' => [
                        'x' => (($result['city'][$pvz->getCityCode()]['coord']['x'] ?? $pvz->getCoordinates()[0]) + $pvz->getCoordinates()[0]) / 2,
                        'y' => (($result['city'][$pvz->getCityCode()]['coord']['y'] ?? $pvz->getCoordinates()[1]) + $pvz->getCoordinates()[1]) / 2,
                    ],
                ];
            }

            $result['citySort'] = $result['city'];

            usort($result['citySort'], function($a, $b){
                return mb_strtolower($a['name']) > mb_strtolower($b['name']);
            });

            $result['text'] = ToolsAssist::getInstance()->getTexts();

            $result['test'] = $_COOKIE['cityname'];
            file_put_contents($cache, json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        
        return $result;
    }

    /**
     * Получить список городов
     * 
     * @return array
     */
    public function getCityList()
    {
        $cityList = [];
        foreach (PvzCollection::getInstance()->iterator() as $pvz) {
            if (!isset($cityList[$pvz->getCityCode()])) {
                $cityList[$pvz->getCityCode()] = $pvz->getCityName();
            }
        }
        return $cityList;
    }

    /**
     * Получить коды всех пвз
     * 
     * @return array
     */
    public function getAllPvzCode()
    {
        return PvzCollection::getInstance()->getAllCodes();
    }

    /**
     * Получить значение пункта выдачи
     * 
     * @param string $code код пункта выдачи
     * @param string $field ключ значения
     * 
     * @return mixed
     *
     * @throws RuntimeException|Exception
     */
    public function getPvz($code, $field)
    {
        if (!$pvz = PvzCollection::getInstance()->get($code)) {
            throw new RuntimeException('Неизвестный пункт выдачи: '.$code);
        }
        
        switch ($field) {
            case 'name': return $pvz->getName();
            case 'addressFull': return $pvz->getAddressFull();
            case 'address': return $pvz->getAddress();
            case 'cityCode': return $pvz->getCityCode();
            case 'cityName': return $pvz->getCityName();
            case 'type': return $pvz->getType();
            case 'isHandout': return $pvz->isHandout();
            case 'isReception': return $pvz->isReception();
            default:
                throw new Exception('Неизвестный параметр пункта выдачи'. $field);
        }

    }

    /**
     * Получить значение поля города
     * 
     * @param int $code код города
     * @param string $field ключ значения 
     * 
     * @return string
     * 
     * @throws RuntimeException|Exception
     */
    public function getCity($code, $field)
    {
        foreach (PvzCollection::getInstance()->iterator() as $pvz) {
            if ($code == $pvz->getCityCode()) {
                switch ($field) {
                    case 'name': return $pvz->getCityName();
                    default:
                        throw new Exception('Неизвестный параметр населенного пункта'.$field);
                }
            }
        }

        throw new RuntimeException('Неизвестный населенный пункт: '.$code);
    }

    /**
     * Получить значение поля тарифа
     * 
     * @param int $code код тарифа
     * @param string $field ключ значения 
     * 
     * @return string
     */
    public function getTariff($code, $field)
    {
        if (!$tariff = ToolsAssist::getInstance()->getTariffList()[$code] ?? null) {
            throw new RuntimeException('Неизвестный тириф код: '.$code);
        }
        
        if (!isset($tariff[$field])) {
            throw new RuntimeException('Неизвестный параметр: '.$field);
        }

        return $tariff[$field];
    }

    /**
     * Получить список кодов тарифов
     * 
     * @return array
     */
    public function getTariffCodes()
    {
        return array_keys(ToolsAssist::getInstance()->getTariffList());
    }

    /**
     * Получить значение типа тарифа
     * 
     * @param int $code код тарифа
     * @param string $field ключ значения типа
     * 
     * @return string|int
     */
    public function getTariffType($code, $field)
    {
        return ToolsAssist::getInstance()->getTariffType($code, $field);
    }

    /**
     * Тариф активен
     * 
     * @return bool
     */
    public function isCheckedTariff($code)
    {
        return isset(ToolsAssist::getInstance()->getTariffCheckedList()[$code]);
    }

    /**
     * Зарегестрировать заказ
     * 
     * @param OrderDataInterface $data
     * 
     * @return array
     */
    public function orderRegistrate($data)
    {
        $tools = ToolsAssist::getInstance();

        return (new OrderRegistrator())
            ->setShipmentPoint($tools->getShipmentPoint())
            ->setFromLocation(['address' => $tools->getFromLocationAddress()])
            ->setTariffCode($data->getTariffCode())            
            ->setDeliveryPoint($data->getDeliveryPoint())            
            ->setToLocation($data->getToLocation())
            ->setRecipient($data->getRecipient())
            ->setProducts($data->getProducts())
            ->setServices($data->getServices())
            ->setNumber($data->getOrderNumber())
            ->handle()
        ;
    }

    /**
     * Получить информацию о заказе
     * 
     * Получение информации о заказе по uuid заказа в CDEK
     * 
     * @param string $uuid
     * 
     * @return array
     */
    public function getOrderInfo($uuid)
    {
        return (new RequestBulder())->getOrderInfo($uuid, RequestBulder::ORDER_NUMBER_UUID);
    }
    public function getOrderInfoByImNumber($im_number)
    {
        return (new RequestBulder())->getOrderInfo($im_number, RequestBulder::ORDER_NUMBER_IM_NUMBER);
    }

    /**
     * Получить текста
     * 
     * @return array
     */
    public function getTexts()
    {
        return ToolsAssist::getInstance()->getTexts();
    }

    /**
     * Получить код города отправки по умолчанию
     * 
     * @return int
     */
    public function getFromCityCodeDefault()
    {
        return ToolsAssist::getInstance()->getFromCityCodeDefault();
    }

    /**
     * Учитывать сумму доставки в заказе
     * 
     * @return bool
     */
    public function isUseSumInOrder()
    {
        return ToolsAssist::getInstance()->isUseSumInOrder();
    }
}