<?php 
class Armtek
{
    private static $find;
    private static $pass;
    private static $login;
    private static $vkong;
    private static $kunnr_rg;

    public static function find($find)
    {
        global $setting, $db, $catalogue, $AUTH_USER_ID;

        self::$find = strip_tags(addslashes($find));

        if ($setting["armtekCheck"] && strlen(self::$find) >= 4) {
            self::$login = $setting["armtekLogin"];
            self::$pass = $setting["armtekPassword"];
            self::setOrg();
            $subIds = self::getSubClas_Sub();

            if (!$subIds["success"]) return $subIds["error"];

            if (self::$login && self::$pass && self::$vkong && self::$kunnr_rg) {
                $db->query("DELETE FROM Message2001
                            WHERE Subdivision_ID = {$subIds['subId']} AND Catalogue_ID = {$catalogue} AND LastUpdated < (NOW() - INTERVAL 31 DAY)");
                $db->query("UPDATE Message2001
                            SET price = '', stock = '', Checked = 0
                            WHERE Subdivision_ID = {$subIds['subId']} AND Catalogue_ID = {$catalogue} AND LastUpdated < (NOW() - INTERVAL 1 DAY)");

                $itemsData = $db->get_results("SELECT *
                                               FROM Message2001
                                               WHERE Catalogue_ID = {$catalogue} AND Subdivision_ID = {$subIds['subId']}", ARRAY_A);
                $itemsDataNormal = array();
                # выводим art в ключ для поиска
                if ($itemsData) {
                    foreach ($itemsData as $itemData) {
                        $itemsDataNormal[$itemData['var15']] = $itemData;
                    }
                }
                # список городов со складами armtek
                $storagesApi = self::getArmtekCurl("http://ws.armtek.ru/api/ws_user/getStoreList?format=json", ["VKORG" => self::$vkong]);
                $storages = array();
                if ($storagesApi['success'] && $storagesApi['result']['RESP']) {
                    foreach ($storagesApi['result']['RESP'] as $storage) {
                        $storages[$storage['KEYZAK']] = $storage;
                    }
                }

                # имя города для расчета доставки
                if (is_array($setting['lists_targetcity'])) {
                    foreach ($setting['lists_targetcity'] as $key => $city) {
                        if (isset($city['main']) && $city['main']) {
                            $cityName = $city['name'];
                            break;
                        }
                    }
                }
                if (!isset($cityName)) $cityName = 'Набережные Челны';

                $markUp = 1 + ($setting['armtekMarkUp'] ? ((float)$setting['armtekMarkUp'] / 100) : 0);

                foreach (['LP','GP'] as $program) { # легковая и грузовая программы
                    if (!$setting["armtek{$program}"]) continue;

                    $post = array("VKORG" => self::$vkong, "PIN" => urlencode(self::$find), 'PROGRAM' => $program);
                    $serchResultShort = self::getArmtekCurl("http://ws.armtek.ru/api/ws_search/assortment_search?format=json", $post);

                    if (!is_array($serchResultShort["result"]["RESP"]) || isset($serchResultShort["result"]["RESP"]['MSG'])) continue;

                    foreach ($serchResultShort["result"]["RESP"] as $short) {
                        $post = array(
                           "VKORG" => self::$vkong,
                           "KUNNR_RG" => self::$kunnr_rg,
                           "PIN" => urlencode(self::$find),
                           'PROGRAM' => $program,
                           'BRAND' => $short['BRAND'],
                           'QUERY_TYPE' => 2
                       );
                       $serchResult = self::getArmtekCurl("http://ws.armtek.ru/api/ws_search/search?format=json", $post);

                       if ($serchResult["success"] && $serchResult["result"]["RESP"] && !isset($serchResult["result"]['RESP']["MSG"])) {

                           foreach ($serchResult["result"]["RESP"] as $armItem) {

                               if(!is_array($armItem) || !$armItem['NAME'] || !$armItem['PIN'] || !$armItem['PRICE']) continue;

                               $delivery = '3-5 дней';
                               if ($cityName == 'Набережные Челны') {
                                   if (mb_stristr($storages[$armItem['KEYZAK']]['SKLNAME'], $cityName)) $delivery = 'на складе';
                                   elseif (mb_stristr($storages[$armItem['KEYZAK']]['SKLNAME'], 'казань')) $delivery = '1-2 дня';
                               }

                               $item = array(
                                   "Catalogue_ID" => $catalogue,
                                   "Subdivision_ID" => $subIds['subId'],
                                   "Sub_Class_ID" => $subIds['subClassId'],
                                   "Checked" => 1,
                                   "code" => addslashes($armItem['ARTID']),
                                   "name" => addslashes(trim($armItem['NAME'])),
                                   "price" => (float)$armItem['PRICE'] * $markUp,
                                   "stock" => $armItem['RVALUE'] ? $armItem['RVALUE'] : 0,
                                   "art" => addslashes(trim($armItem['PIN'])),
                                   "vendor" => addslashes($armItem['BRAND']),
                                   "var2" => $armItem['VENSL'] > 80 ? "Возможна доставка под заказ" : "",
                                   "var3" => $armItem['ANALOG'] ? "аналог" : NULL,
                                   "var5" => $find,
                                   "var13" => 'Партнер: '.($armItem['KEYZAK'] ? "[{$armItem['KEYZAK']}]" : ''),
                                   "var14" => $delivery,
                                   "var15" => md5(trim($armItem['NAME']).$armItem['BRAND'].$armItem['ARTID'].trim($armItem['PIN']).$armItem['KEYZAK'])
                               );
                               // if ($AUTH_USER_ID == 2419) {
                               //     echo '<pre>';
                               //     var_dump($item);
                               //     var_dump($armItem);
                               // }
                               if ($itemsDataNormal[$item["var15"]]) {
                                   # update
                                   $itemData = $itemsDataNormal[$item["var15"]];
                                   $query = "";
                                   foreach ($item as $fNmae => $val) {
                                       if ($fNmae == "var5") {
                                           $val = mb_stristr($itemData["var5"], $find) ? $itemData["var5"] : $itemData["var5"]." ".$find;
                                       }
                                       $query .= ($query ? "," : "")."{$fNmae} = '{$val}'";
                                   }
                                   $db->query("UPDATE Message2001 SET {$query} WHERE Message_ID = {$itemData['Message_ID']}");
                               } else {
                                   # create
                                   self::insertDB("Message2001", $item);
                               }

                           }
                       }
                    }
                }
            } else {
                # нет логина / пароля / информации об организациях
            }
        }
    }

    private static function setOrg()
    {
        global $setting;
        if (!$setting['armtekVKORG'] || !$setting['armtekKUNNR_RG']) {
            $respInfo = self::getArmtekCurl("http://ws.armtek.ru/api/ws_user/getUserVkorgList?format=json");
            if ($respInfo["success"]) {
                $vokng = array();
                foreach ($respInfo['result']['RESP'] as $resp) {
                    $vkong[] = $resp['VKORG'];
                }
                if ($vkong) {
                    $post = array("VKORG" => $vkong[0], "STRUCTURE" => 1, "FTPDATA" => 0);
                    $userInfo = self::getArmtekCurl("http://ws.armtek.ru/api/ws_user/getUserInfo?format=json", $post);
                    if ($userInfo['success']) {
                        $kunnr_rg = array();
                        foreach ($userInfo['result']['RESP']['STRUCTURE']['RG_TAB'] as $rg_tab) {
                            $kunnr_rg[] = $rg_tab['KUNNR'];
                        }
                    }
                    if ($kunnr_rg) {
                        $setting['armtekVKORG'] = $vkong;
                        $setting['armtekKUNNR_RG'] = $kunnr_rg;
                        setSettings($setting);
                    }
                }
            }
        }

        self::$vkong = $setting["armtekVKORG"][0];
        self::$kunnr_rg = $setting["armtekKUNNR_RG"][0];
    }

    private static function getSubClas_Sub()
    {
        global $db, $catalogue;

        $subId = $db->get_var("SELECT Subdivision_ID
                               FROM Subdivision
                               WHERE Catalogue_ID = {$catalogue} AND EnglishName = 'armCat'");
        if (!$subId) {
            # добавляем раздел
            $subFields = array(
                "Checked" => 0,
                "EnglishName" => "armCat",
                "Hidden_URL" => "/catalog/armCat/",
                "Catalogue_ID" => $catalogue,
                "Subdivision_Name" => "Онлайн каталог",
                "Parent_Sub_ID" => $db->get_var("SELECT Subdivision_ID
                                                 FROM Subdivision
                                                 WHERE Catalogue_ID = {$catalogue} AND Hidden_URL = '/catalog/'")
            );
            self::insertDB("Subdivision", $subFields);
            $subId = $db->insert_id;

            # добавляем инфоблок
            if ($subId) {
                $subClassFields = array(
                    "Checked" => 1,
                    "Class_ID" => 2001,
                    "EnglishName" => "armCat",
                    "Catalogue_ID" => $catalogue,
                    "Subdivision_ID" => $subId,
                    "Sub_Class_Name" => "Онлайн каталог Армтек",
                    "Class_Template_ID" => 0
                );
                self::insertDB("Sub_Class", $subClassFields);
                $subClassId = $db->insert_id;
                $error = $subClassId ? "" : "неудалось содздать инфаблок";
            } else {
                $error = "неудалось содздать раздел";
            }
        } else {
            $subClassId = $db->get_var("SELECT Sub_Class_ID
                                        FROM Sub_Class
                                        WHERE Catalogue_ID = {$catalogue} AND Subdivision_ID = {$subId}"
                                      );
            $error = $subClassId ? "" : "неудалось получить инфаблок";
        }

        return array(
            "success" => ($subId && $subClassId ? true : false),
            "error" => $error,
            "subId" => $subId,
            "subClassId" => $subClassId
        );
    }

    private static function insertDB($tabName, $item)
    {
        global $db;
        $db->query("INSERT INTO {$tabName} (`".implode("`,`", array_keys($item))."`) VALUES ('".implode("','", $item)."')");
    }

    private static function getArmtekCurl($url, $post = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, self::$login.":".self::$pass);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($post != false) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        $result = curl_exec($curl);
        curl_close($curl);

        return array(
            "success" => (mb_stristr($result, "error") ? false : true),
            "result" => json_decode($result, true)
        );
    }
}