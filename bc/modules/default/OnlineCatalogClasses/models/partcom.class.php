<?php

class Partcom
{
    private $searchUrl = 'https://www.part-kom.ru/engine/api/v3/search/parts';

    public function getSearchResult($find)
    {
        global $setting, $catalogue, $db;

        $type = ['Original' => 'original', 'ReplacementOriginal' => 'analog', 'ReplacementNonOriginal' => 'analog'];
        $sub = $setting['partcom_save_sub'];
        $cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = {$sub} AND Class_ID = 2001");

        if (!$sub || !$cc) {
            throw new Exception("Не выбран раздел для сохранения товаров", 1);
        }

        $respons = $this->curlGet($this->searchUrl, [
            'number' => $find,
            'find_substitutes' => $setting['partcom_analog'],
            'store' => $setting['partcom_only_stock']
        ]);

        if (!$respons) {
            throw new Exception("Invalid params request", 1);
        } elseif (isset($respons['status']) && $respons['status'] > 200) {
            throw new Exception($respons['title'] . ". " . $respons['detail'], $respons['status']);
        }
       
        $groupItems = $groupAnalogs = $items = $groupArt2 = [];
        
        foreach ($respons as $item) {
            $itemType = ($type[$item['detailGroup']] ?: 'analog');

            if ($itemType == 'analog' && $item['quantity'] < 1) {
                continue;
            }
            // Не работает без maker_id
            // if ($itemType == 'analog' && !in_array($item['number'], $groupAnalogs[])) {
            //     $groupAnalogs[] = $item['number'];
            // }
          
            $items[$itemType][] = [
                'art' => $item['number'],
                'name' => $item['description'],
                'vendor' => $item['maker'],
                'stock' => $item['quantity'],
                'price' => $item['price'] * (1 + $setting['partcom_markup'] / 100),
                'code' => $item['providerId'],
                'Catalogue_ID' => $catalogue,
                'Subdidvision_ID' => $sub,
                'Sub_Class_ID' => $cc,
                'Checked' => 1,
                'Keyword' => encodestring(trim($item['description']) . " " . trim($item['providerId']) . " " . trim($item['number']), 1)
            ];
        }

        // foreach ($items['original'] as $key => $item) {
        //     $items['original'][$key]['analog'] = implode("\r\n", $groupAnalogs);
        // }

        return array_merge(($items['analog'] ?: []), ($items['original'] ?: []));
    }

    private function curlGet($url, $params)
    {
        global $setting;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($setting['partcom_login'] . ':' . $setting['partcom_passwor']),
                'Accept: application/json',
                'Content-type: application/json'
            ],
        ]);
        $res = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        return $res;
    }
}
