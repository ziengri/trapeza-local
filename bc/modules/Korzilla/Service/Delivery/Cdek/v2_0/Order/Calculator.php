<?php 

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order\ConvertorProducts;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Builder;
use Exception;
use RuntimeException;

class Calculator
{ 
    /**
     * @var array Точка отправления
     */
    private $fromLocation;
    /**
     * @var array Точка прибытия
     */
    private $toLocation;
    /** 
     * @var array Список информации по местам (упаковкам)
     */
    private $packages;
    /** 
     * @var array Дополнительные услуги
     */
    private $services;

    /**
     * Установить точку отправления
     * 
     * обязательное наличие одного из полей, при расчете используется только 1 поле приоритетность сверху вниз
     * 
     * $location = [
     *      'code' => 'Код населенного пункта СДЭК (метод "Список населенных пунктов")',
     *      'postal_code' => 'Почтовый индекс',
     *      'country_code' => 'Код страны в формате  ISO_3166-1_alpha-2',
     *      'city' => 'Название города',
     *      'address' => 'Полная строка адреса',
     * ];
     * 
     * @param array $location
     * 
     * @return static
     */
    public function setFromLocation($location)
    {
        $this->fromLocation = $location;
        return $this;
    }

    /**
     * Установить точку прибытия
     *
     * обязательное наличие одного из полей, при расчете используется только 1 поле приоритетность сверху вниз
     * 
     * $location = [
     *      'code' => 'Код населенного пункта СДЭК (метод "Список населенных пунктов")',
     *      'postal_code' => 'Почтовый индекс',
     *      'country_code' => 'Код страны в формате  ISO_3166-1_alpha-2',
     *      'city' => 'Название города',
     *      'address' => 'Полная строка адреса',
     * ];
     * 
     * @param array $location
     * 
     * @return static
     */
    public function setToLocation($location)
    {
        $this->toLocation = $location;
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
     * Установить дополнительные услуги
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
     * Расчет доставки по коду тарифа
     */
    public function calculateTariff($tariffCode)
    {
        $this->checkImportantParameters();

        if (!isset(ToolsAssist::getInstance()->getTariffCheckedList()[$tariffCode])) {
            throw new RuntimeException('Выбранный тариф не включен '.$tariffCode);
        }

        return (new Builder())->calculateTariff($this->fromLocation, $this->toLocation, $this->packages, $tariffCode, $this->services);
    }

    /**
     * Расчет доставки по доступным тарифам
     */
    public function calculateTariffList()
    {
        $this->checkImportantParameters();

        return $this->tariffListPrepare((new Builder())->calculateTariffList($this->fromLocation, $this->toLocation, $this->packages));
    }

    /**
     * Подготовка списка тарифов
     * 
     * 1)Убирает выключенные тарифы
     * 2)Сортируем по возрастанию цены
     * 
     * @return array
     */
    private function tariffListPrepare($list)
    {
        $tariffCheckedList = ToolsAssist::getInstance()->getTariffCheckedList();

        $list = array_reduce($list['tariff_codes'] ?? [], function($carry = [], $tariff) use ($tariffCheckedList) {
            if (isset($tariffCheckedList[$tariff['tariff_code']])) {
                $carry[$tariff['tariff_code']] = $tariff;
            }
            return $carry;
        }, []);

        uasort($list, function($a, $b){
            return $a['delivery_sum'] - $b['delivery_sum'];
        });

        return $list;
    }

    /**
     * Проверка установленны ли основные параметры
     * 
     * @throws Exception
     */
    private function checkImportantParameters()
    {
        if (!isset($this->fromLocation)) {
            throw new Exception('Не установлен параметр fromLocation - Точка отправления');
        }

        if (!isset($this->toLocation)) {
            throw new Exception('Не установлен параметр toLocation - Точка прибытия');
        }

        if (!isset($this->packages)) {
            throw new Exception('Не установлен параметр packages - список информации по местам (упаковкам)');
        }
    }    
}