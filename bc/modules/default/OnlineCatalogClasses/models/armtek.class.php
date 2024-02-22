<?php

class Armtek
{
    private static $find;
    private static $pass;
    private static $login;
    private static $vkong;
    private static $kunnr_rg;
    private static $sub;
    private static $cc;

    public static function find($find)
    {
        global $setting, $db, $catalogue;

        self::$find = strip_tags(addslashes($find));

        if (!$setting["armtek"]) {
            throw new Exception("Armtek uncheced", 1);
        }
        if (strlen(self::$find) < 4) {
            throw new Exception("Length find < 4", 1);
        }

        self::$login = $setting["armtek_login"];
        self::$pass = $setting["armtek_password"];
        self::$sub = $setting['armtek_save_sub'];
        self::setOrg();

        if (!self::$login || !self::$pass || !self::$vkong || !self::$kunnr_rg || !self::$sub) {
            throw new Exception("Invalid params setting", 1);
        }
        self::$cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = ' " . self::$sub . "' AND Class_ID = 2001");
        $items = [];
        
        foreach (['LP','GP'] as $program) { # легковая и грузовая программы
            if (!$setting["armtek_{$program}"]) {
                continue;
            }
            
            $post = array("VKORG" => self::$vkong, "PIN" => urlencode(self::$find), 'PROGRAM' => $program);
            $serchResultShort = self::getCurl("http://ws.armtek.ru/api/ws_search/assortment_search?format=json", $post);
            if (!is_array($serchResultShort["result"]["RESP"]) || isset($serchResultShort["result"]["RESP"]['MSG'])) {
                continue;
            }

            foreach ($serchResultShort["result"]["RESP"] as $short) {
                $post = array(
                   "VKORG" => self::$vkong,
                   "KUNNR_RG" => self::$kunnr_rg,
                   "PIN" => urlencode(self::$find),
                   'PROGRAM' => $program,
                   'BRAND' => $short['BRAND'],
                   'QUERY_TYPE' => 2
                );

                $serchResult = self::getCurl("http://ws.armtek.ru/api/ws_search/search?format=json", $post);
            
                if ($serchResult["success"] && $serchResult["result"]["RESP"] && !isset($serchResult["result"]['RESP']["MSG"])) {
                    
                    foreach ($serchResult["result"]["RESP"] as $armItem) {
                        if (!is_array($armItem) || !$armItem['NAME'] || !$armItem['PIN'] || !$armItem['PRICE'] || (! (int) $armItem['RVALUE'] && $armItem['ANALOG'])) {
                            continue;
                        }
                        $art = addslashes(trim($armItem['PIN']));
                        $name = addslashes(trim($armItem['NAME']));
                        $code = addslashes($armItem['ARTID']);
                        $item = [
                           "Catalogue_ID" => $catalogue,
                           "Subdivision_ID" => self::$sub,
                           "Sub_Class_ID" => self::$cc,
                           "Checked" => 1,
                           "code" => $code,
                           "name" => $name,
                           "price" => (float)$armItem['PRICE'] * (1 + $setting['armtek_markup'] / 100),
                           "stock" => $armItem['RVALUE'] ? (int) $armItem['RVALUE'] : 0,
                           "art" => $art,
                           "vendor" => addslashes($armItem['BRAND']),
                           "Keyword" => encodestring($name . " " . $code . " " . $art, 1),
                           "var15" => addslashes(trim(self::$find))
                        ];
                        $items[] = $item;
                    }
                }
            }
        }

        return $items;
    }
    /**
     * Получить номер сбытовой организации
     *
     * @return void
     */
    private static function setOrg()
    {
        global $setting;

        if (!$setting['armtek_vkorg'] || !$setting['armtek_kunnr_rg']) {
            $respInfo = self::getCurl("http://ws.armtek.ru/api/ws_user/getUserVkorgList?format=json");
            if ($respInfo["success"]) {
                $vokng = array();
                foreach ($respInfo['result']['RESP'] as $resp) {
                    $vkong[] = $resp['VKORG'];
                }
                if ($vkong) {
                    $post = array("VKORG" => $vkong[0], "STRUCTURE" => 1, "FTPDATA" => 0);
                    $userInfo = self::getCurl("http://ws.armtek.ru/api/ws_user/getUserInfo?format=json", $post);
                    if ($userInfo['success']) {
                        $kunnr_rg = array();
                        foreach ($userInfo['result']['RESP']['STRUCTURE']['RG_TAB'] as $rg_tab) {
                            $kunnr_rg[] = $rg_tab['KUNNR'];
                        }
                    }
                    if ($kunnr_rg) {
                        $setting['armtek_vkorg'] = $vkong[0];
                        $setting['armtek_kunnr_rg'] = $kunnr_rg[0];
                        setSettings($setting);
                    }
                }
            }
        }

        self::$vkong = $setting["armtek_vkorg"];
        self::$kunnr_rg = $setting["armtek_kunnr_rg"];
    }

    private static function getCurl($url, $post = false)
    {
        
        $paramsCurl = [
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => self::$login . ":" . self::$pass,
            CURLOPT_RETURNTRANSFER => true,

        ];
        if ($post != false) {
            $paramsCurl = $paramsCurl + [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post];
        }
        $curl = curl_init();
        curl_setopt_array($curl, $paramsCurl);
        $result = curl_exec($curl);
        curl_close($curl);

        return array(
            "success" => (mb_stristr($result, "error") ? false : true),
            "result" => json_decode($result, true)
        );
    }
}
