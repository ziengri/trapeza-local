<?php

class ForumAuto
{
    private $rest = 'https://api.forum-auto.ru/v2/';

    public function getSearchResult($find)
    {
        global $setting, $catalogue, $db;

        $respons = $this->curlGet('listGoods', [
            'login' => $setting['forum_auto_login'],
            'pass' => $setting['forum_auto_passwor'],
            'art' => $find,
            'cross' => $setting['forum_auto_analogs'] ? 1 : 0
        ]);

        if (isset($respons['errors'])) {
            throw new Exception($respons['errors']['FaultString'] . "\r\n" . $respons['errors']['FaultDetail'], $respons['errors']['FaultCode']);
        }
        $sub = $setting['forum_auto_save_sub'];
        $cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = {$sub} AND Class_ID = 2001");

        $items = [];

        

        foreach ($respons as $item) {
            if ($item['num'] == 0) {
                continue;
            }
            $id = $item['gid'];
            $art = $item['art'];
            $name = $item['name'];
            $keyword = encodestring(trim($name) . " " . trim($id) . " " . trim($art), 1);

            $items[$id] = [
                'Subdivision_ID' => $sub,
                'Sub_Class_ID' => $cc,
                'vendor' => $item['brand'],
                'name' => $name,
                'art' => $art,
                'code' => $id,
                'price' => $item['price'] * (1 + $setting['forum_auto_markup'] / 100),
                'stock' => $item['num'],
                'Keyword' => $keyword,
                'Catalogue_ID' => $catalogue,
                'Checked' => 1,
            ];
        }
        return $items;
    }

    public function curlGet($methode, $params)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->rest . $methode . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);

        $res = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        return $res;
    }
}
