<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

use App\modules\Korzilla\Service\Delivery\Cdek\Request\Request;
use App\modules\Korzilla\ToolsAssist\SingletonTrait;
use Exception;

class ToolsAssist
{
    use SingletonTrait;

    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Request
     */
    private $request;

    private $packageDefaultParams;
    
    /**
     * Получить тектса
     * 
     * @return array
     */
    public function getTexts()
    {
        return [
            'courier' => 'Курьер',
            'pvz' => 'Пункт выдачи',
            'courierTitle' => 'Доставка курьером',
            'pickupTitle' => 'Доставка до пункта выдачи',
            'pickupNoSelect' => 'Пункт не выбран',
            'pickupAddressTitle' => 'Адрес:',
            'chooseBtn' => 'Выбрать',
            'cityTitle' => 'Город:',
            'workTimeTitle' => 'Время работы:',
            'deliveryTimeTitle' => 'Дней достаки:',
            'deliveryPriceTitle' => 'Цена доставки:',
            'cantDeliveryCourier' => 'Ваш заказа не может быть доставлен курьером в выбранный город',
            'cantDEliveryPickup' => 'Ваш заказа не может быть доставлен в выбранный пункты выдачи',
            'searchCity' => 'Поиск города',
            'deliveryNoSelect' => 'Не выбран способ доставки',
            'pvz' => 'Пункт выдачи',
            'postamat' => 'Постамат',
            'calcError' => 'ошибка расчета доставки'
        ];
    }

    /**
     * Получить класс репозиторий
     * 
     * Не использовать за пределами модуля!!!
     * 
     * @return Repository
     */
    public function getRepository()
    {
        global $pathInc, $DOCUMENT_ROOT;
        $siteDir = $DOCUMENT_ROOT.$pathInc;
        return $this->repository ?? $this->repository = new Repository($siteDir);
    }

    /**
     * Получить логин пользователя
     * 
     * @return string
     * 
     * @throws Exeption
     */
    public function getClientLogin()
    {
        global $setting;

        if (empty($setting['cdekLogin'])) {
            throw new Exception('Не установлен логин пользователя CDEK');
        }

        return $setting['cdekLogin'];
    }

    /**
     * Получить пароль пользователя
     * 
     * @return string
     * 
     * @throws Exeption
     */
    public function getClientPassword()
    {
        global $setting;

        if (empty($setting['cdekPassword'])) {
            throw new Exception('Не установлен пароль пользователя CDEK');
        }

        return $setting['cdekPassword'];
    }

    /**
     * Включен
     */
    public function isChecked()
    {
        global $setting;

        return 1 === (int)($setting['cdekCheck'] ?? 0);
    }

    /**
     * Получить код города отправки по умолчанию
     * 
     * @return int
     */
    public function getFromCityCodeDefault()
    {
        global $setting;

        if (empty($setting['cdekMainCity'])) {
            throw new Exception('Не установлен город поумолчанию CDEK');
        }

        return $setting['cdekMainCity'];
    }

    /**
     * Получить код пвз отправки
     * 
     * @return string
     */
    public function getShipmentPoint()
    {
        global $setting;
        return $setting['cdek_shipment_point'] ?? null;
    }

    /**
     * Получить адрес отправки
     * 
     * @return string
     */
    public function getFromLocationAddress()
    {
        global $setting;
        return $setting['cdek_from_location_address'] ?? null;
    }

    /**
     * Получить список тарифов
     * 
     * @return array
     */
    public function getTariffList()
    {
        return [
            7   => ['type' => 1, 'name' => 'Международный экспресс документы', 'description' => 'Экспресс-доставка за/из-за границы документов и писем.'],
            8   => ['type' => 1, 'name' => 'Международный экспресс грузы',     'description' => 'Экспресс-доставка за/из-за границы грузов и посылок до 30 кг.'],
            136 => ['type' => 4, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            137 => ['type' => 3, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            138 => ['type' => 2, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            139 => ['type' => 1, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            366 => ['type' => 6, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            368 => ['type' => 7, 'name' => 'Посылка',                          'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'],
            233 => ['type' => 3, 'name' => 'Экономичная посылка',              'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'],
            234 => ['type' => 4, 'name' => 'Экономичная посылка',              'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'],
            378 => ['type' => 7, 'name' => 'Экономичная посылка',              'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'],
            291 => ['type' => 4, 'name' => 'CDEK Express',                     'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'],
            293 => ['type' => 1, 'name' => 'CDEK Express',                     'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'],
            294 => ['type' => 3, 'name' => 'CDEK Express',                     'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'],
            295 => ['type' => 2, 'name' => 'CDEK Express',                     'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'],
            243 => ['type' => 4, 'name' => 'Китайский экспресс',               'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'],
            245 => ['type' => 1, 'name' => 'Китайский экспресс',               'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'],
            246 => ['type' => 3, 'name' => 'Китайский экспресс',               'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'],
            247 => ['type' => 2, 'name' => 'Китайский экспресс',               'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'],
            1   => ['type' => 1, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'],
            361 => ['type' => 6, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'],
            363 => ['type' => 7, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'],
            10  => ['type' => 4, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '],
            11  => ['type' => 3, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '],
            12  => ['type' => 2, 'name' => 'Экспресс лайт',                    'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '],
            5   => ['type' => 4, 'name' => 'Экономичный экспресс',             'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'],
            118 => ['type' => 1, 'name' => 'Экономичный экспресс',             'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'],
            119 => ['type' => 3, 'name' => 'Экономичный экспресс',             'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'],
            120 => ['type' => 2, 'name' => 'Экономичный экспресс',             'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'],
            15  => ['type' => 4, 'name' => 'Экспресс тяжеловесы',              'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'],
            16  => ['type' => 3, 'name' => 'Экспресс тяжеловесы',              'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'],
            17  => ['type' => 2, 'name' => 'Экспресс тяжеловесы',              'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'],
            18  => ['type' => 1, 'name' => 'Экспресс тяжеловесы',              'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'],
            57  => ['type' => 1, 'name' => 'Супер-экспресс до 9',              'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'],
            58  => ['type' => 1, 'name' => 'Супер-экспресс до 10',             'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'],
            59  => ['type' => 1, 'name' => 'Супер-экспресс до 12',             'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'],
            60  => ['type' => 1, 'name' => 'Супер-экспресс до 14',             'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'],
            61  => ['type' => 1, 'name' => 'Супер-экспресс до 16',             'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'],
            3   => ['type' => 1, 'name' => 'Супер-экспресс до 18',             'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу.'],
            62  => ['type' => 4, 'name' => 'Магистральный экспресс',           'description' => 'Быстрая экономичная доставка грузов по России'],
            121 => ['type' => 1, 'name' => 'Магистральный экспресс',           'description' => 'Быстрая экономичная доставка грузов по России'],
            122 => ['type' => 3, 'name' => 'Магистральный экспресс',           'description' => 'Быстрая экономичная доставка грузов по России'],
            123 => ['type' => 2, 'name' => 'Магистральный экспресс',           'description' => 'Быстрая экономичная доставка грузов по России'],
            63  => ['type' => 4, 'name' => 'Магистральный супер-экспресс',     'description' => 'Быстрая экономичная доставка грузов к определенному часу'],
            124 => ['type' => 1, 'name' => 'Магистральный супер-экспресс',     'description' => 'Быстрая экономичная доставка грузов к определенному часу'],
            125 => ['type' => 3, 'name' => 'Магистральный супер-экспресс',     'description' => 'Быстрая экономичная доставка грузов к определенному часу'],
            126 => ['type' => 2, 'name' => 'Магистральный супер-экспресс',     'description' => 'Быстрая экономичная доставка грузов к определенному часу'],
        ];
    }

    /**
     * Получить тариф
     * 
     * @param int $code ключ тарифа
     * 
     * @return array|null
     */
    public function getTariff($code)
    {
        return $this->getTariffList()[$code] ?? null;
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
        return $this->getTariffTypes()[$this->getTariff($code)['type']][$field] ?? null;
    }

    /**
     * Получить типы тарифов
     * 
     * @return array
     */
    public function getTariffTypes()
    {
        return [
            1 => [
                'code' => 1,
                'title' => 'дверь-дверь',
                'titleShort' => 'Д – Д',
                'type' => 'courier',
                'fromType' => 'door',
                'pointType' => 'door',
                'description' => 'Курьер забирает груз у отправителя и доставляет получателю на указанный адрес.',
            ],
            2 => [
                'code' => 2,
                'title' => 'дверь-склад',
                'titleShort' => 'Д – С',
                'type' => 'pickup',
                'fromType' => 'door',
                'pointType' => 'pvz',
                'description' => 'Курьер забирает груз у отправителя и довозит до склада, получатель забирает груз самостоятельно в ПВЗ (самозабор).',
            ],
            3 => [
                'code' => 3,
                'title' => 'склад-дверь',
                'titleShort' => 'С – Д',
                'type' => 'courier',
                'fromType' => 'pvz',
                'pointType' => 'door',
                'description' => 'Отправитель доставляет груз самостоятельно до склада, курьер доставляет получателю на указанный адрес.',
            ],
            4 => [
                'code' => 4,
                'title' => 'склад-склад',
                'titleShort' => 'С – С',
                'type' => 'pickup',
                'fromType' => 'pvz',
                'pointType' => 'pvz',
                'description' => 'Отправитель доставляет груз самостоятельно до склада, получатель забирает груз самостоятельно в ПВЗ (самозабор).',
            ],
            6 => [
                'code' => 6,
                'title' => 'дверь-постамат',
                'titleShort' => 'Д – П',
                'type' => 'pickup',
                'fromType' => 'door',
                'pointType' => 'postamat',
                'description' => 'Курьер забирает груз у отправителя и доставляет в указанный постамат, получатель забирает груз самостоятельно из постамата',
            ],
            7 => [
                'code' => 7,
                'title' => 'склад-постамат',
                'titleShort' => 'С – П',
                'type' => 'pickup',
                'fromType' => 'pvz',
                'pointType' => 'postamat',
                'description' => 'Отправитель доставляет груз самостоятельно до склада, курьер доставляет в указанный постамат, получатель забирает груз самостоятельно из постамата',
            ],
        ];
    }

    /**
     * Получить список id включенных тарифов
     * 
     * $list = ['id тарифа' => 'unused'];
     * 
     * @return array
     */
    public function getTariffCheckedList()
    {
        global $setting;
        
        return !empty($setting['lists_cdekTarifId']) ? array_flip($setting['lists_cdekTarifId']) : [];
    }

    /**
     * Получить габариты упоков по умолчанию
     * 
     * @param string $parameter 
     * @param mixed $default возвращаемое значение при отсутствии параметра
     * 
     * @return int|null
     */
    public function getPackageDefaultParameter($parameter, $default = null)
    {
        global $setting;

        switch ($parameter) {
            case 'weight': return !empty($setting['cdek_parameter_weight_default']) ? $setting['cdek_parameter_weight_default'] : $default;
            case 'height': return  !empty($setting['cdek_parameter_height_default']) ? $setting['cdek_parameter_height_default'] : $default;
            case 'width': return  !empty($setting['cdek_parameter_width_default']) ? $setting['cdek_parameter_width_default'] : $default;
            case 'length': return  !empty($setting['cdek_parameter_length_default']) ? $setting['cdek_parameter_length_default'] : $default;
            default: 
                throw new Exception(sprintf('Неизвестный тип параметра поумолчанию %s', $parameter));
        }
    }

    /**
     * Учитывать сумму доставки в заказе
     * 
     * @return bool
     */
    public function isUseSumInOrder()
    {
        global $setting;

        return !empty($setting['cdek_delivery_sum_in_order']);
    }
}