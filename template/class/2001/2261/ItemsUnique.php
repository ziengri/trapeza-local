<?php

class ItemsUnique
{
    const NAMES_WRAPP_TPL = [
        [
            [
                'prefix' => 'Купить %PRICE%',
                'declination' => 'B_KOGO', 
            ],[
                'suffix' => 'купить',
                'declination' => 'B_KOGO', 
            ],[
                'prefix' => 'Купить',
                'declination' => 'B_KOGO', 
            ],
        ],[
            [
                'suffix' => 'с доставкой по %CITYNAME-KOMY%',
            ],[
                'suffix' => 'с доставкой в город %CITYNAME%',
            ],[
                'suffix' => '. Доставим по городу %CITYNAME-KOMY%',
            ],
        ],[
            [
                'suffix' => 'в наличии',
            ],[
                'suffix' => 'в интернет-магазине',
            ],[
                'prefix' => 'В наличии',
            ],
        ],[
            [
                'suffix' => '%CITYNAME-GDE%',
            ],[
                'suffix' => '%CITYNAME-GDE% %PRICE%',
            ],[
                'suffix' => 'в городе %CITYNAME-GDE%',
            ],
        ],
    ];

    private $catalog;
    private $productId;
    private $step;

    /**
     * @param array $catalog массив всех товаров, должен быть отсартирован по id в порядке возрастания [['id' => 1, 'name' => 'product_name']]
     * @param int $productId id продукта
     * @param int $step шаг
     */
    public function __construct($catalog, $productId, $step = 1)
    {
        $this->catalog = $catalog;
        $this->productId = $productId;
        $this->step = 10 * ($step ?: 1);
    }

    /**
     * Получить список уникальных товаров
     * 
     * @param int $length длина возвращаемого массива
     * @param int $similarMinLimit уровень дозволенной схожести между названиями
     * 
     * @return array
     */
    public function getUniqueList($length = 4, $similarMinLimit = 75)
    {
        $result = [];
        $used = [];
        $catalogLength = count($this->catalog);

        $i = $this->getProductIndex();
        $used[$i] = 1;
        while (count($result) < $length && $catalogLength > count($used)) {            

            $i = ($i + $this->step) % $catalogLength;
            while (isset($used[$i])) $i++;
            $used[$i] = 1;

            $isFited = true;
            foreach ($result as $fitedObject) {
                similar_text($fitedObject['name'], $this->catalog[$i]['name'], $similarProcent);
                if ($similarProcent > $similarMinLimit) {
                    $isFited = false;
                    break;
                }
            }
            if ($isFited) $result[] = $this->catalog[$i];
        }

        return $this->namesWrapp($result);
    }

    /**
     * Получить индекс продукта в каталоге
     * 
     * @return int|null
     */
    private function getProductIndex()
    {
        $catalogLength = count($this->catalog);

        for ($i = 0; $i < $catalogLength; $i++) {
            if ($this->catalog[$i]['id'] == $this->productId) {
                return $i;
            }
        }

        return null;
    }
    
    /**
     * Обернуть названия товаров из выборки
     * 
     * @param array $array массив выборки
     * 
     * @return array
     */
    private function namesWrapp($array)
    {
        global $currency;
        foreach ($array as $num => &$object) {
            $tpl = $this->getTpl($object['id'], $num);

            if (!empty($tpl['declination'])) {
                $object['name'] = \Korzilla\Morpher::convert($object['name'], $tpl['declination']);
            }

            if (!empty($tpl['prefix'])) {
                $object['name'] = $tpl['prefix'].' '.mb_strtolower($object['name']);    
            }

            if (!empty($tpl['suffix'])) {
                $object['name'] = $object['name'].' '.$tpl['suffix'];    
            }

            $priceText = $object['price'] ? 'по цене от '.$object['price'].''.($currency['html'] ?: 'руб.') : 'недорого';
            $object['name'] = str_replace('%PRICE%', $priceText, $object['name']);
            $object['name'] = \Korzilla\Replacer::replaceText($object['name']);
        }

        return $array;
    }

    /**
     * Получить шаблон
     * 
     * @param int $objectID id объекта
     * @param int $num номер объекта в выборке
     */
    private function getTpl($objectID, $num)
    {
        $tplKey = $num % count(self::NAMES_WRAPP_TPL);
        $tpl = self::NAMES_WRAPP_TPL[$tplKey];

        $tplSubKey = substr($objectID, -1, 1) % count($tpl);

        return $tpl[$tplSubKey];
    }    
}