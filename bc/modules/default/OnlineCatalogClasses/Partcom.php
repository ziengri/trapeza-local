<?php

class Partcom
{
    /**
     * Логин
     *
     * @var string
     */
    private $login;
    /**
     * Пароль
     *
     * @var string
     */
    private $pass;
    private $type = ['Original' => 'original', 'ReplacementOriginal' => 'replace', 'ReplacementNonOriginal' => 'analog'];

    public function __construct($login, $pass)
    {
        $this->login = $login;
        $this->pass = $pass;
    }

    public function getGoods($code, $vendorid, $vedorName = '')
    {
        
        if (!$vendorid) {
            $brands = $this->getCurl("http://www.part-kom.ru/engine/api/v3/ref/brands");
            foreach ($$brands as $brand) {
                if ($brand['name'] == $vedorName) {
                    $vendorid = $brand['id'];
                    break;
                }
            }
        }
        $data = $this->getCurl("http://www.part-kom.ru/engine/api/v3/search/parts?number={$code}&maker_id={$vendorid}&find_substitutes=1");
        // global $current_user;
        // if ($current_user['PermissionGroup_ID'] == 1 ) {
        //  return $data;
        // }
        if (!$data) {
            return 'Нет результата';
        }
        foreach ($data as $val) { //собираем массив товаров
            if ($val['description'] && $val['price'] && $val['detailGroup'] && $val['maker']) {
                    $develery = ($val['expectedDays'] == $val['guaranteedDays'] ? $val['guaranteedDays'] : $val['expectedDays'] . '-' . $val['guaranteedDays']);
                    $item = array(
                        'name'      => $val['description'],
                        'art'       => $val['number'],
                        'price'     => ceil($val['price']),
                        'stock'     => $val['quantity'],
                        'develery'  => $develery,
                        'existence' => preg_match('/партком набережные челны/iu', $val['providerDescription']),
                        'direction' => $val['providerDescription'] . ' ' . $val['providerId'],
                        'provider'  => 'partcom',
                        'vendor'    => $val['maker'],
                        'vendorId'  => $val['makerId']
                        );
                    if ($this->type[$val['detailGroup']] == "original") {
                        $partItems[$this->type[$val['detailGroup']]][$val['maker']]["art.{$code}"][] = $item;
                    } else {
                        $partItems[$this->type[$val['detailGroup']]][$val['maker']]["art." . $val['number']][] = $item;
                    }
            }
        }
        return $partItems;
    }

    public function getCatalog($code)
    {
        global $current_user;
        $data = $this->getCurl("http://www.part-kom.ru/engine/api/v3/search/parts?number={$code}");
        if (isset($data['detail']) and $data['detail'] == 'Access is denied') {
            return '<!--нет доступа к partcom-->';
        }
        if (isset($data['status']) and $data['status'] == '403') {
            return '<!--нет доступа к partcom: ' . $data['detail'] . '-->';
        }
        if (empty($data)) {
            return 'Нет результата';
        }
        // if ($current_user['PermissionGroup_ID'] == 1 ) {
        //  return $data;
        // }
        foreach ($data as $val) { //собираем массив категорий
            if ($val['description'] && $val['maker'] && $val['makerId'] && $val['price'] && (!$partCat[$val['maker']] || $partCat[$val['maker']]['price'] > $val['price'])) {
                $partCat[$val['maker']] = [
                                            'vendorId' => $val['makerId'],
                                            'name'     => $val['description'],
                                            'provider' => 'partcom',
                                            'price'    => ceil($val['price']),
                                            'vendorName' => $val['maker'],
                                            ];
            }
        }
        return $partCat;
    }

    private function getCurl($url)
    {
        $headers = [
                    "Authorization: Basic " . base64_encode($this->login . ':' . $this->pass),
                    "Content-Type: application/json",
                    "Accept: application/json"
                    ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, true);
    }
}
