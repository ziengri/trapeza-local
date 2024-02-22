<?php

namespace App\modules\Korzilla\MySklad;

class Products {

    public function getProductList($token, $param = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_URL => 'https://online.moysklad.ru/api/remap/1.2/entity/product' . (!empty($param) ? '?' . http_build_query($param, '', '&') : ''),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        //var_dump($result);
    }

    public function getAssortimentList($token, $param = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_URL => 'https://online.moysklad.ru/api/remap/1.2/entity/assortment' . (!empty($param) ? '?' . http_build_query($param, '', '&') : ''),
            CURLOPT_RETURNTRANSFER => true
        ]);
		
        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);
		
		/*echo print_r($result,1);
        flush();
        ob_flush();*/

        return $result;
    }
}