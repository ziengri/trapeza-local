<?php 
class Omega
{
    private static $hash;
    private static $find;
    private static $pass;
    private static $login;

    public static function find($find)
    {
        global $setting, $db, $catalogue;

        self::$find = $find;

        if ($setting["omegaCheck"] && strlen($find) > 4 && !preg_match('/[а-яё]/ui', $find)) {

            self::$login = $setting["omegaLogin"];
            self::$pass = $setting["omegaPassword"];

            $subIds = self::getSubClas_Sub();
            if (!$subIds["success"]) return;
            $db->query("DELETE FROM Message2001
                        WHERE Subdivision_ID = {$subIds['subId']} AND Catalogue_ID = {$catalogue} AND LastUpdated < (NOW() - INTERVAL 31 DAY)");
            $db->query("UPDATE Message2001
                        SET Checked = 0, price = 0, stock = 0
                        WHERE Subdivision_ID = {$subIds['subId']} AND Catalogue_ID = {$catalogue} AND LastUpdated < (NOW() - INTERVAL 1 DAY)");

            $search = self::getSearchResult();

            if ($search["success"]) {
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

                $markUp = 1 + ($setting['omegaMarkUp'] ? ((float)$setting['omegaMarkUp'] / 100) : 0);

                foreach ($search["reuslt"] as $part) {
                    $itemsRemain = self::getItems($part->code);
                    if ($itemsRemain["success"] && is_array($itemsRemain["items"])) {
                        $analogs = array();
                        foreach($itemsRemain["items"] as $itemRemain) {
                            if (!in_array($itemRemain->manufacturer_number ,$analogs)) {
                                $analogs[] = $itemRemain->manufacturer_number;
                            }
                        }
                        foreach ($itemsRemain["items"] as $itemRemain) {
                            if (!$itemRemain->price) continue;

                            if ($cityName == 'Набережные Челны') {
                                if (mb_stristr($itemRemain->storage_name, $cityName)) $delivery = 'на складе';
                                elseif (mb_stristr($itemRemain->storage_name, 'казань')) $delivery = '1-2 дня';
                            }
                            if (!isset($delivery)) $delivery = '3-5 дней';

                            $item = array(
                                "Catalogue_ID" => $catalogue,
                                "Subdivision_ID" => $subIds['subId'],
                                "Sub_Class_ID" => $subIds['subClassId'],
                                "Checked" => 1,
                                "code" => $part->code,
                                "name" => addslashes($part->name),
                                "price" => $itemRemain->price,
                                "stock" => $itemRemain->quantity,
                                "art" => addslashes($itemRemain->goods_code),
                                "text" => addslashes($part->note),
                                "vendor" => addslashes($itemRemain->manufacturer_name),
                                "ves" => $itemRemain->weight,
                                "analog" => $part->unique_number."\n".implode("\n", $analogs)."\n".$part->code,
                                "var1" => $part->unique_number.",".implode(",", $analogs).",".$part->code,
                                "var5" => $find,
                                "var13" => 'Партнер: '.($itemRemain->storage_id ? "[{$itemRemain->storage_id}]" : ''),
                                "var14" => $delivery,
                                "var15" => md5($itemRemain->id_goods_unit) # поле для проверки
                            );
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
                                #create
                                self::insertDB("Message2001", $item);
                            }
                        }
                    }
                }
            }
        }
    }

    private function getSearchResult()
    {
        $client = new soapclient("http://ws.etsp.ru/Security.svc?singleWsdl", array("trace"=>1, "encoding" => "utf-8", "exceptions"=>0));
        $logon = $client->__soapCall('Logon', array(array(
                        'Login' => self::$login,
                        'Password' => self::$pass)))->LogonResult;

        $checkLogon = self::checkApiError($logon);
        if ($checkLogon["success"]) {
            self::$hash = $logon;
            $client = new soapclient("http://ws.etsp.ru/Search.svc?singleWsdl", array("trace"=>1, "encoding" => "utf-8", "exceptions"=>0));
            $searchResult = $client->__soapCall('SearchBasic', array(array(
                                'Text' => self::$find,
                                'HashSession' => $logon)))->SearchBasicResult;

            $checkSearch = self::checkApiError($searchResult);
            if ($checkSearch["success"]) {
                $reuslt = array(
                    "success" => true,
                    "reuslt" => self::xmlToArray($searchResult, "/root/part")
                );
            } else {
                $result = $checkSearch;
            }
        } else {
            $result = $checkLogon;
        }
        return $reuslt;
    }

    private function getItems($code)
    {
        $client = new soapclient("http://ws.etsp.ru/PartsRemains.svc?singleWsdl", array("trace"=>1, "encoding" => "utf-8", "exceptions"=>0));
        $response = $client->__soapCall('GetPartsRemainsByCode2', array(array(
                        'Code' => $code,
                        'ShowRetailRemains' => true, # Признак показа остатков розничной сети (работает только по отдельному доступу)
                        'ShowOutsideRemains' => false, # Признак показа товаров под заказ (работает только по отдельному доступу)
                        'ShowPriceByQuantity' => false, # Признак, показывать ли цену в зависимости от количества товара
                        'HashSession' => self::$hash)))->GetPartsRemainsByCode2Result;

        $checkResponse = self::checkApiError($response);
        if ($checkResponse["success"]) {
            $result = array(
                "success" => true,
                "items" => self::xmlToArray($response, "/root/sklad_remains/item")
            );
        } else {
            $result = $checkResponse;
        }

        return $result;
    }

    private function getSubClas_Sub()
    {
        global $db, $catalogue;

        $subId = $db->get_var("SELECT Subdivision_ID
                               FROM Subdivision
                               WHERE Catalogue_ID = {$catalogue} AND EnglishName = 'omgCat'");
        if (!$subId) {
            # добавляем раздел
            $subFields = array(
                "Checked" => 0,
                "EnglishName" => "omgCat",
                "Hidden_URL" => "/catalog/omgCat/",
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
                    "EnglishName" => "omgCat",
                    "Catalogue_ID" => $catalogue,
                    "Subdivision_ID" => $subId,
                    "Sub_Class_Name" => "Онлайн каталог Омега",
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

    private function insertDB($tabName, $item)
    {
        global $db;
        $db->query("INSERT INTO {$tabName} (`".implode("`,`", array_keys($item))."`) VALUES ('".implode("','", $item)."')");
    }

    private function checkApiError($value)
    {
        $error = mb_stristr($value, "error_message") ? self::xmlToArray($value,"/root/error_message")[0] : false;
        return array(
            "success" => $error === false ? true : false,
            "error" => $error
        );
    }

    private function xmlToArray($xml, $path = "")
    {
        return (new \SimpleXMLElement($xml))->xpath($path);
    }
}