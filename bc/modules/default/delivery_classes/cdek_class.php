<?php

class Cdek {
    public $login;
    public $pass;
    public $defaulParams;

    public $tarifList = array(
        7   => array('type' => 1, 'name' => 'Международный экспресс документы', 'description' => 'Экспресс-доставка за/из-за границы документов и писем.'),
        8   => array('type' => 1, 'name' => 'Международный экспресс грузы', 'description' => 'Экспресс-доставка за/из-за границы грузов и посылок до 30 кг.'),
        136 => array('type' => 4, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        137 => array('type' => 3, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        138 => array('type' => 2, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        139 => array('type' => 1, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        366 => array('type' => 6, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        368 => array('type' => 7, 'name' => 'Посылка', 'description' => 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'),
        233 => array('type' => 3, 'name' => 'Экономичная посылка', 'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'),
        234 => array('type' => 4, 'name' => 'Экономичная посылка', 'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'),
        378 => array('type' => 7, 'name' => 'Экономичная посылка', 'description' => 'Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'),
        291 => array('type' => 4, 'name' => 'CDEK Express', 'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'),
        293 => array('type' => 1, 'name' => 'CDEK Express', 'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'),
        294 => array('type' => 3, 'name' => 'CDEK Express', 'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'),
        295 => array('type' => 2, 'name' => 'CDEK Express', 'description' => 'Сервис по доставке товаров из-за рубежа в Россию, Украину, Казахстан, Киргизию, Узбекистан с услугами по таможенному оформлению.'),
        243 => array('type' => 4, 'name' => 'Китайский экспресс', 'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'),
        245 => array('type' => 1, 'name' => 'Китайский экспресс', 'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'),
        246 => array('type' => 3, 'name' => 'Китайский экспресс', 'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'),
        247 => array('type' => 2, 'name' => 'Китайский экспресс', 'description' => 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'),
        1   => array('type' => 1, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'),
        361 => array('type' => 6, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'),
        363 => array('type' => 7, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка по России документов и грузов до 30 кг.'),
        10  => array('type' => 4, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '),
        11  => array('type' => 3, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '),
        12  => array('type' => 2, 'name' => 'Экспресс лайт', 'description' => 'Классическая экспресс-доставка документов и грузов внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами. '),
        5   => array('type' => 4, 'name' => 'Экономичный экспресс', 'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'),
        118 => array('type' => 1, 'name' => 'Экономичный экспресс', 'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'),
        119 => array('type' => 3, 'name' => 'Экономичный экспресс', 'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'),
        120 => array('type' => 2, 'name' => 'Экономичный экспресс', 'description' => 'Недорогая доставка грузов по России ЖД и автотранспортом (доставка грузов с увеличением сроков).'),
        15  => array('type' => 4, 'name' => 'Экспресс тяжеловесы', 'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'),
        16  => array('type' => 3, 'name' => 'Экспресс тяжеловесы', 'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'),
        17  => array('type' => 2, 'name' => 'Экспресс тяжеловесы', 'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'),
        18  => array('type' => 1, 'name' => 'Экспресс тяжеловесы', 'description' => 'Классическая экспресс-доставка внутри РФ, Белоруссии, Казахстана, Армении, Киргизии и между этим странами.'),
        57  => array('type' => 1, 'name' => 'Супер-экспресс до 9', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'),
        58  => array('type' => 1, 'name' => 'Супер-экспресс до 10', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'),
        59  => array('type' => 1, 'name' => 'Супер-экспресс до 12', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'),
        60  => array('type' => 1, 'name' => 'Супер-экспресс до 14', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'),
        61  => array('type' => 1, 'name' => 'Супер-экспресс до 16', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу'),
        3   => array('type' => 1, 'name' => 'Супер-экспресс до 18', 'description' => 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу.'),
        62  => array('type' => 4, 'name' => 'Магистральный экспресс', 'description' => 'Быстрая экономичная доставка грузов по России'),
        121 => array('type' => 1, 'name' => 'Магистральный экспресс', 'description' => 'Быстрая экономичная доставка грузов по России'),
        122 => array('type' => 3, 'name' => 'Магистральный экспресс', 'description' => 'Быстрая экономичная доставка грузов по России'),
        123 => array('type' => 2, 'name' => 'Магистральный экспресс', 'description' => 'Быстрая экономичная доставка грузов по России'),
        63  => array('type' => 4, 'name' => 'Магистральный супер-экспресс', 'description' => 'Быстрая экономичная доставка грузов к определенному часу'),
        124 => array('type' => 1, 'name' => 'Магистральный супер-экспресс', 'description' => 'Быстрая экономичная доставка грузов к определенному часу'),
        125 => array('type' => 3, 'name' => 'Магистральный супер-экспресс', 'description' => 'Быстрая экономичная доставка грузов к определенному часу'),
        126 => array('type' => 2, 'name' => 'Магистральный супер-экспресс', 'description' => 'Быстрая экономичная доставка грузов к определенному часу')
    );
    public $tarifTypes = array(
        1 => array('short' => 'Д – Д', 'full' => 'дверь-дверь', 'type' => 'courier', 'description' => 'Курьер забирает груз у отправителя и доставляет получателю на указанный адрес.'),
        2 => array('short' => 'Д – С', 'full' => 'дверь-склад', 'type' => 'pickup', 'description' => 'Курьер забирает груз у отправителя и довозит до склада, получатель забирает груз самостоятельно в ПВЗ (самозабор).'),
        3 => array('short' => 'С – Д', 'full' => 'склад-дверь', 'type' => 'courier', 'description' => 'Отправитель доставляет груз самостоятельно до склада, курьер доставляет получателю на указанный адрес.'),
        4 => array('short' => 'С – С', 'full' => 'склад-склад', 'type' => 'pickup', 'description' => 'Отправитель доставляет груз самостоятельно до склада, получатель забирает груз самостоятельно в ПВЗ (самозабор).'),
        6 => array('short' => 'Д – П', 'full' => 'дверь-постамат', 'type' => 'pickup', 'description' => 'Курьер забирает груз у отправителя и доставляет в указанный постамат, получатель забирает груз самостоятельно из постамата'),
        7 => array('short' => 'С – П', 'full' => 'склад-постамат', 'type' => 'pickup', 'description' => 'Отправитель доставляет груз самостоятельно до склада, курьер доставляет в указанный постамат, получатель забирает груз самостоятельно из постамата')
    );

    public function __construct()
    {
        $this->setVariables();
        $this->setSessionsValues();
    }

    public function getDeliveryInfo($tariffID, $pvzCode = null)
    {
        $textArr = array(
            'delType' => array('courier' => 'Курьером (до девери)', 'pickup' => 'В пункт выдачи / постамат')
        );
        $tariff = $this->tarifList[$tariffID];
        $deliveryType = $this->tarifTypes[$tariff['type']]['type'];

        $info = "Способ доставки: {$textArr['delType'][$deliveryType]}";
        if ($pvzCode) $info.= " по адресу: ".$this->getPvzAddress($pvzCode);
        $info .= "<br/>Тариф: {$tariff['name']}({$tariffID})";
        return $info;
    }

    public function getPvzData()
    {
        if ($this->pvz) {
            $pvzList = array();
            foreach ($this->pvz as $pvz) {
                if (!isset($this->cityList[$pvz['cityCode']]['coord'])) {
                    $this->cityList[$pvz['cityCode']]['coord'] = array('x' => $pvz['coordX'], 'y' => $pvz['coordY']);
                } else {
                   $this->cityList[$pvz['cityCode']]['coord']['x'] = ($this->cityList[$pvz['cityCode']]['coord']['x'] + $pvz['coordX']) / 2;
                   $this->cityList[$pvz['cityCode']]['coord']['y'] = ($this->cityList[$pvz['cityCode']]['coord']['y'] + $pvz['coordY']) / 2;
                }
                $pvzList[$pvz['code']] = array(
                    'code' => $pvz['code'],
                    'name' => $pvz['name'],
                    'address' => $pvz['city'].', '.$pvz['address'],
                    'cityCode' => $pvz['cityCode'],
                    'coord' => array('x' => $pvz['coordX'], 'y' => $pvz['coordY']),
                    'workTime' => $pvz['workTime'],
                    'type' => strtolower($pvz['type'])
                );
            }
            $cityPrice = $this->getDeliveryPrice($this->city['code']);
            $this->cityList[$this->city['code']]['price'] = $cityPrice;
            $citySort = $this->cityList;
            usort($citySort, function($a, $b){
                return mb_strtolower($a['name']) > mb_strtolower($b['name']);
            });
            $result = array(
                'text' => $this->text,
                'city' => $this->cityList,
                'citySort' => $citySort,
                'pvz' => $pvzList
            );

        } else {
            $result = array(
                'error' => 'Ошибка загрузки пунктов выдачи'
            );
        }
        return json_encode($result);
    }

    public function getDeliveryPrice($cityCode)
    {
        $priceResult = array();
        if ($cityCode) {
            if (!$this->checkSession('cartHash', $this->cartHash)) unset($_SESSION['cdek']['cityPriceCache']);

            if (isset($_SESSION['cdek']['cityPriceCache'][$cityCode])) {
                $priceResult = $_SESSION['cdek']['cityPriceCache'][$cityCode];
            } else {
                $request = array(
                    'version'        => '1.0',
                    'dateExecute'    => date('Y-m-d'),
                    'authLogin'      => $this->login,
                    'secure'         => md5(date('Y-m-d')."&".$this->pass),
                    'senderCityId'   => $this->cityMain['code'],
                    'receiverCityId' => $cityCode,
                    'goods'          => $this->getGoods()
                );
                foreach ($this->usedTarif as $id) {
                    $request['tariffList'][] = array('id' => $id);
                }
                $result = $this->request('http://api.cdek.ru/calculator/calculate_tarifflist.php', json_encode($request));
                if ($result['code'] == 200) $priceResult = $this->getPriceResultStruct($result['result']['result']);

                $_SESSION['cdek']['cartHash'] = $this->cartHash;
                $_SESSION['cdek']['cityPriceCache'][$cityCode] = $priceResult;
            }
        }

        return $priceResult;
    }

    public function getPriceResultStruct($tariffs)
    {
        global $currency;

        $result = $struct = array();

        if (is_array($tariffs)) {
            foreach ($tariffs as $tariff) {
                if (!$tariff['status']) continue;
                $tariff = $tariff['result'];
                $dayDelivery = $tariff['deliveryPeriodMin'] == $tariff['deliveryPeriodMax'] ? $tariff['deliveryPeriodMin'] : $tariff['deliveryPeriodMin'].'-'.$tariff['deliveryPeriodMax'];
                $key = $this->getOptionTarif('pointType', $tariff['tariffId']).$this->getOptionTarif('deliveryType', $tariff['tariffId']).$dayDelivery;
                if (!isset($struct[$key]) || $struct[$key]['price'] > $tariff['price']) {
                    $struct[$key] = array(
                        'price' => $tariff['price'],
                        'period' => $dayDelivery,
                        'pricehtml' => price($tariff['price']).' '.$currency['html'],
                        'id' => $tariff['tariffId'],
                        'pointType' => $this->getOptionTarif('pointType', $tariff['tariffId'])
                    );
                }
            }
            usort($struct, function($a, $b){
                return $a['price'] - $b['price'];
            });
            foreach ($struct as $tariff) {
                $result[$this->getOptionTarif('deliveryType', $tariff['id'])][] = $tariff;
            }
        }

        return $result;
    }

    public function getOptionTarif($key, $id)
    {
        switch ($key) {
            case 'deliveryType': # тип доставки
                return $this->tarifTypes[$this->tarifList[$id]['type']]['type'];
            case 'pointType': # тип пункта
                $types = array('С' => 'pvz', 'Д' => 'door', 'П' => 'postamat');
                return $types[mb_substr($this->tarifTypes[$this->tarifList[$id]['type']]['short'], -1)];
            case 'deliveryTypeID': # id способа доставки
               switch ($this->getOptionTarif('deliveryType', $id)) {
                   case 'courier': return 2;
                   case 'pickup': return 1;
                   default: return null;
               }
               break;
        }
    }

    public function applyChoose($params = array())
    {
        $assistArr = array(
            'type' => 'cdek',
            'success' => false,
            'cityCode' => $params['cityCode'],
            'tariffid' => $params['tariffid'],
            'pvzCode' => isset($params['pvzcode']) ? $params['pvzcode'] : null,
            'description' => $this->text['calcError'],
            'price' => 0
        );

        if ($params['tariffid'] && $params['cityCode'] && isset($this->cityList[$params['cityCode']])) {
            $tariff = null;
            $assistArr['tariffType'] = $this->getOptionTarif('deliveryType', $params['tariffid']);
            $priceList = $this->getDeliveryPrice($params['cityCode']);
            if (is_array($priceList[$assistArr['tariffType']])) {
                foreach ($priceList[$assistArr['tariffType']] as $item) {
                    if ($item['id'] == $params['tariffid']) {
                        $tariff = $item;
                        break;
                    }
                }
            }
            if ($tariff) {
                $assistArr = array_merge($assistArr, array(
                    'tariffid' => $tariff['id'],
                    'success' => true,
                    'price' => $tariff['price']
                ));
            }

            if ($assistArr['tariffType'] == 'courier') $assistArr['description'] = $assistArr['success'] ? "Доставка курьером в город: <b>{$this->cityList[$params['cityCode']]['name']}</b>" : $this->text['cantDeliveryCourier'];
            elseif ($assistArr['tariffType'] == 'pickup') $assistArr['description'] = $assistArr['success'] ? $this->getPvzAddress($params['pvzcode']) : $this->text['cantDEliveryPickup'];
        }

        $_SESSION['cart']['delivery']['assist'] = $assistArr;
        $_SESSION['cart']['delivery']['sum'] = $_SESSION['cart']['delivery']['sum_result'] = $assistArr['price'];
        $_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['delivery']['sum'] + $_SESSION['cart']['totalSumDiscont'];

        $this->setSessionsValues();

        $result = array(
            'deliversum' => $assistArr['price'],
            'totdelsum' => $_SESSION['cart']['totaldelsum'],
            'address' => $assistArr['description']
        );

        if ($assistArr['success']) {
            $result['success'] = true;
            $result['tariffType'] = $assistArr['tariffType'];
            $result['deliveryType'] = $this->getOptionTarif('deliveryTypeID', $assistArr['tariffid']);
        }
        else $result['error'] = true;

        return $result;
    }

    public function recalcDelivery()
    {
        $session = $_SESSION['cart']['delivery']['assist'];
        if ($session['type'] == 'cdek') {
            $params = array(
                'tariffid' => $session['tariffid'],
                'cityCode' => $session['cityCode'],
                'pvzcode' => $session['pvzCode']
            );
            $this->applyChoose($params);
        }
    }

    public function getPvzAddress($key, $full = false)
    {
        return $full ? $this->pvz[$key]['fullAddress'] : $this->pvz[$key]['city'].', '.$this->pvz[$key]['address'];
    }

    public function adminTarifList()
    {
        global $setting;
        $html = "";
        foreach ($this->tarifList as $tarifID => $tarif) {
            $html .= "<div class='colline colline-2'>
                        ".bc_checkbox("bc_lists_cdekTarifId[]", $tarifID, "{$tarif['name']}  {$this->tarifTypes[$tarif['type']]['short']} ({$tarifID})", isset($this->usedTarif[$tarifID]))."
                        <div class='cdek-tarif-info'>
                            <span class='cdek-tarif-info-icon'></span>
                            <div class='cdek-tarif-info-body'>
                                <span class='info-row title'>Способ доставки: {$this->tarifTypes[$tarif['type']]['full']}</span>
                                <span class='info-row'>Описание: {$tarif['description']}</span>
                            </div>
                        </div>
                    </div>";
        }
        return $html;
    }

    public function getGoods()
    {
        if (function_exists('cdek_getGoods')) {
            return cdek_getGoods($this);
        }

        global $db;

        $result = [];

        if (is_array($_SESSION['cart']['items'])) {
            $basketProducts = [];
            foreach ($_SESSION['cart']['items'] as $item) {
                $basketProducts[] = [
                    'id' => $item['id'],
                    'count' => $item['count'],
                ];
                foreach ($item['modificators'] ?? [] as $modificator) {
                    $basketProducts[] = [
                        'id' => $modificator['id'],
                        'count' => $modificator['count'],
                    ];
                }
            }
            
            $itemIds = array_reduce($basketProducts, function($carry, $item){
                if (empty($carry)) $carry = '';
                else $carry .= ',';                
                return $carry.$item['id'];
            }, 0);

            $sql = "SELECT `Message_ID` AS id, `ves`, `length`, `width`, `height`, `edizm`
                    FROM `Message2001` 
                    WHERE `Message_ID` IN ({$itemIds})";

            $dbProducts = array_reduce($db->get_results($sql, ARRAY_A) ?: [], function($carry, $item){
                if (empty($carry)) $carry = [];
                $carry[$item['id']] = $item;
                return $carry;
            }, []);

            foreach ($basketProducts as $item) {
                $dbItem = $dbProducts[$item['id']];

                $resultItem = [
                    'weight' => $dbItem['ves'] ? $this->getWeightByUnit($dbItem['ves'], $dbItem['edizm']) : $this->defaulParams['weight'],
                    'length' => ((float) $dbItem['length']) ?: $this->defaulParams['length'],
                    'width'  => ((float) $dbItem['width']) ?: $this->defaulParams['width'],
                    'height' => ((float) $dbItem['height']) ?: $this->defaulParams['height'],
                ];

                for ($i = 0; $i < $item['count']; $i++) {
                    $result[] = $resultItem;
                }
            }
        }

        return $result;
    }

    public function getWeightByUnit($weight, $unit)
    {
        $weight = str_replace(',', '.', $weight);
        # проверяем наличие единиц измерения сначала в весе, потом в еденице измеренеия
        foreach (['weight', 'unit'] as $key) {
            # килограммы
            if (preg_match("/кило|кг/", $$key)) {
                $weight = (float) $weight;
                break;
            }
            # граммы
            elseif (preg_match("/гр/", $$key)) {
                $weight = ((float) $weight) / 1000;
                break;
            }
        }
        return (float) $weight;
    }

    public function setPvz()
    {
        $data = null;
        $pvzPath = $this->root.'/template/class/2005/resurs/cdek_pvz.json';

        if (!file_exists($pvzPath) || date('d', filectime($pvzPath)) != date('d')) { # запрос на получение данных
            $data = $this->getPVZ();
            if ($data) {
                $data = $this->convertPvz($data);
                file_put_contents($pvzPath, json_encode($data));
            }
        }

        if (!$data && file_exists($pvzPath)) {
            $data = file_get_contents($pvzPath);
            $data = json_decode($data, true);
        }

        $this->pvz = $data;
    }

    public function getPvz()
    {
        $getPvzUrl = "https://integration.cdek.ru/pvzlist/v1/json";
        $result = $this->request($getPvzUrl);
        return $result['code'] == 200 ? $result['result'] : null;
    }

    public function convertPvz($pvzList)
    {
        if (!is_array($pvzList['pvz'])) return null;
        $result = array();
        foreach ($pvzList['pvz'] as $pvz) $result[$pvz['code']] = $pvz;
        return $result;
    }

    public function setCityList()
    {
        global $setting, $cityname;

        $this->cityList = array();
        if (is_array($this->pvz)) {
            foreach ($this->pvz as $pvz) {
                if (!isset($this->cityList[$pvz['cityCode']])) {
                    $this->cityList[$pvz['cityCode']] = array(
                        'name' => $pvz['city'],
                        'code' => $pvz['cityCode']
                    );
                    if ($setting['cdekMainCity'] == $pvz['cityCode']) {
                        $this->cityMain = $this->cityList[$pvz['cityCode']];
                    }
                    if (mb_strtolower($pvz['city']) == mb_strtolower($cityname)) {
                        $this->city = $this->cityList[$pvz['cityCode']];
                    }
                }
            }
            if (isset($this->cityMain) && !isset($this->city)) {
                $this->city = $this->cityMain;
            }

            $this->cityList[$this->city['code']]['selected'] = true;

            uasort($this->cityList, function($a, $b){
                return $a['name'] > $b['name'];
            });
        }
    }

    public function setCatalogue()
    {
        global $current_catalogue, $nc_core;

        if (!$current_catalogue) $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace('www.', '', $_SERVER['HTTP_HOST']));

        $this->catalogue = $current_catalogue['Catalogue_ID'];
    }

    public function setCartHash()
    {
        $this->cartHash = md5(json_encode($_SESSION['cart']['items']));
    }

    public function setUsedTarif()
    {
        global $setting;
        $this->usedTarif = array();
        if (is_array($setting['lists_cdekTarifId'])) {
            foreach ($setting['lists_cdekTarifId'] as $id) {
                if (is_numeric($id)) $this->usedTarif[$id] = $id;
            }
        }
    }

    public function setVariables()
    {
        global $setting, $DOCUMENT_ROOT;

        $this->login = $setting['cdekLogin'];
        $this->pass = $setting['cdekPassword'];

        $this->root = $DOCUMENT_ROOT;

        $defaulParams = explode(';', $setting['cdekDefaultParams']);
        $this->defaulParams = array(
            'weight' => ((float) $defaulParams[0]) ?: 0.5,
    		'length' => ((float) $defaulParams[1]) ?: 30,
    		'width'  => ((float) $defaulParams[2]) ?: 30,
    		'height' => ((float) $defaulParams[3]) ?: 30
        );
        $this->name = "СДЭК";

        $this->text = array(
            'courier' => 'Курьер',
            'pvz' => 'Пункт выдачи',
            'courierTitle' => 'Доставка курьером',
            'pickupTitle' => 'Доставка до пункта выдачи',
            'pickupNoSelect' => 'Пункт не выбран',
            'pickupAddressTitle' => 'Адрес:',
            'chooseBtn' => 'Выбрать',
            'cityTitle' => 'Город:',
            'workTimeTitle' => 'Время работы:',
            'deliveryTimeTitle' => 'Дней доставки:',
            'deliveryPriceTitle' => 'Цена доставки:',
            'cantDeliveryCourier' => 'Ваш заказа не может быть доставлен курьером в выбранный город',
            'cantDEliveryPickup' => 'Ваш заказа не может быть доставлен в выбранный пункты выдачи',
            'searchCity' => 'Поиск города',
            'deliveryNoSelect' => 'Не выбран способ доставки',
            'pvz' => 'Пункт выдачи',
            'postamat' => 'Постамат',
            'calcError' => 'ошибка расчета доставки'
        );
    }

    public function setSessionsValues()
    {
        $this->session = isset($_SESSION['cart']) && isset($_SESSION['cart']['delivery']['assist']) && $_SESSION['cart']['delivery']['assist']['type'] == 'cdek';
        $this->tariffID = $this->session ? $_SESSION['cart']['delivery']['assist']['tariffid'] : null;
        $this->cityCode = $this->session ? $_SESSION['cart']['delivery']['assist']['cityCode'] : null;
        $this->pvzCode  = $this->session ? $_SESSION['cart']['delivery']['assist']['pvzCode'] : null;
    }

    public function checkSession($key, $val = null)
    {
        if (!isset($_SESSION['cdek']) || !isset($_SESSION['cdek'][$key])) return false;
        elseif ($val === null) return true;

        switch ($key) {
            case 'cartHash': return $_SESSION['cdek']['cartHash'] == $val;
            case 'cityPriceCache': return isset($_SESSION['cdek']['cityPriceCache'][$val]);
        }
    }

    public function request($url, $post = null)
    {
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ($post) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
		}

        $result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		file_put_contents($this->root."/bc/modules/default/delivery_classes/lastquery.log",$result);
		return array(
			'code'   => $code,
			'result' => json_decode($result, true)
		);
    }

    public static function sessionClear()
    {
        unset($_SESSION['cdek']);
    }

    public static function on()
    {
        global $setting;
        return (boolean) $setting['cdekCheck'];
    }

    public function __get($name)
    {
        if (!isset($this->$name)) {
            switch ($name) {
                case 'pvz':
                    $this->setPvz();
                    break;
                case 'city':
                case 'cityMain':
                case 'cityList':
                    $this->setCityList();
                    break;
                case 'catID':
                case 'catalogue':
                    $this->setCatalogue();
                    break;
                case 'cartHash':
                    $this->setCartHash();
                    break;
                case 'usedTarif':
                    $this->setUsedTarif();
                    break;
            }
        }
        return isset($this->$name) ? $this->$name : null;
    }
}
