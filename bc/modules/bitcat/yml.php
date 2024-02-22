<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
ini_set('memory_limit', '2500M');
set_time_limit(1000000);
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db, $pathInc, $DOCUMENT_ROOT, $HTTP_FILES_PATH, $catalogue, $current_catalogue, $nc_core, $titleArr;

while (ob_get_level() > 0) {
    ob_end_flush();
}

$domenName = $_SERVER['SERVER_NAME'];
$domenUrl = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $domenName;

$poddomen = array_reverse(explode('.', $domenName));
$poddomen = encodestring($poddomen[2]);
$filenameYml = ($poddomen ? $poddomen . '_' : '') . (isset($turbo) ? "yml_turbo.xml" : "yml.xml");



$path = $ROOTDIR . $pathInc . '/';
$iniSetPath = $path . 'ymlsetting.ini';

$services = $_GET['services'];
if($services) {
    $filenameYml = 'ymlservices.xml';

    if (!$current_catalogue) {
        $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        if (!$catalogue) {
            $catalogue = $current_catalogue['Catalogue_ID'];
        }
    }

    function normisPathToImgYml($photo) {
        global $domenUrl;
        $img[] = $photo;
        $im = '';
        if (!$img) {
            return false;
        }
        foreach ($img as $value) {
            $im .= (trim($value) ? (!strstr($value, "http://") && !strstr($value, "https://") ? $domenUrl : "") . "" . trim($value) : null);
        }
        return $im;
    }
    function setImgForYml($photo, $preview) {
        if(!empty($photo)) return (normisPathToImgYml($photo));
        if(!empty($preview)) return (normisPathToImgYml($preview));
        return '';
    }
    function setCattegoriesYml($dom, $toappend) {
        global $db, $catalogue;
        $categoriesArr = $db->get_results("SELECT a.Subdivision_ID, a.Subdivision_Name, a.Parent_Sub_ID, b.Class_ID 
                                            FROM Subdivision as a, Sub_Class as b 
                                            WHERE a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 2021 AND a.Catalogue_ID = {$catalogue}", ARRAY_A);
        if ($categoriesArr) {
            foreach ($categoriesArr as $cat) {
                if ($cat['Subdivision_Name'] == 'Поиск по каталогу') continue;

                $category = $dom->createElement('category', htmlspecialchars($cat['Subdivision_Name']));
                $category->setAttribute('id', $cat['Subdivision_ID']);
                if(!empty($cat['Parent_Sub_ID'])) $category->setAttribute('parentId', $cat['Parent_Sub_ID']);
            }
            $toappend->appendChild($category);
        }
    }

    $uslugi = $db->get_results("SELECT `Message2021`.`Message_ID` as url, `Message2021`.`name`, `Message2021`.`price`, `Message2021`.`Subdivision_ID` as categoryId, `Message2021`.`text` as description, `Message2021`.`Checked` as available, multi.`Preview` as picture2, multi.`Path` as picture
                                    FROM `Message2021`
                                        LEFT JOIN 
                                            (SELECT `Multifield`.`Message_ID`, `Multifield`.`Preview`, `Multifield`.`Path` FROM `Multifield` WHERE `Field_ID` = 2462 group by `Message_ID`) as multi 
                                        ON multi.`Message_ID` = `Message2021`.`Message_ID` 
                                    WHERE `Message2021`.`Catalogue_ID` = {$catalogue}", ARRAY_A);
    // var_dump($uslugi);exit;
    $dom = new DOMDocument();
    $implementation = new DOMImplementation();
    $dom->appendChild($implementation->createDocumentType('yml_catalog SYSTEM "shops.dtd"'));
    $ycat = $dom->createElement('yml_catalog');
    $ycat->setAttribute('date', date("Y-m-d H:i"));
    $shop = $dom->createElement('shop');
    $shop->appendChild($dom->createElement('name', $current_catalogue['Catalogue_Name']));
    $shop->appendChild($dom->createElement('company', $current_catalogue['Catalogue_Name']));
    $shop->appendChild($dom->createElement('url', $domenUrl));
    $currs = $dom->createElement('currencies');
    $currency = $dom->createElement('currency');
    $currency->setAttribute('id', 'RUR');
    $currency->setAttribute('rate', '1');
    $currency->setAttribute('plus', '0');
    $currs->appendChild($currency);
    $shop->appendChild($currs);
    $ymlcategories = $shop->appendChild($dom->createElement('categories'));
    setCattegoriesYml($dom, $ymlcategories);
    $shop->appendChild($ymlcategories);

    foreach($uslugi as $key => $value) {
        $offer = $dom->createElement('offer');
        $value['available'] = $value['available'] == 1 ? 'true' : 'false';
        $value['currencyId'] = 'RUR';
        $offer->setAttribute('id', $value['url']); $offer->setAttribute('available', $value['available']); unset($value['available']);
        $value['picture'] = setImgForYml($value['picture'], $value['picture2']);
        unset($value['picture2']);
        $value['url'] = $domenUrl . nc_message_link($value['url'], 2021);
        $value['text'] = htmlspecialchars(strip_tags($value['text']));
        foreach($value as $tagname => $tagvalue) {
            $tag = $dom->createElement($tagname, $tagvalue);
            $offer->appendChild($tag);
        }
        $shop->appendChild($offer);
    }
    $ycat->appendChild($shop);
    $dom->appendChild($ycat);
    $yml = $dom->saveXML();
    if (file_put_contents($ROOTDIR . $pathInc . "/" . $filenameYml, $yml)) {
        echo "YML файл сформирован: <a href='{$domenUrl}{$pathInc}/{$filenameYml}'>{$domenUrl}{$pathInc}/{$filenameYml}</a>";
    } else {
        echo "file not put";
    }
    exit;
}

if (file_exists($iniSetPath)) {
    $ymlSet = parse_ini_file($iniSetPath);
}

if ($ymlSet['notdeliv'] > 0) {
    $notdeliv = $ymlSet['notdeliv'];
}

if ($ymlSet['store'] > 0) {
    $store = $ymlSet['store'];
}

// if ($ymlSet['pickup'] > 0) {
//     $pickup = $ymlSet['pickup'];
// }

$sales_notes = ($ymlSet['sales_notes'] ? $ymlSet['sales_notes'] : "Необходима предоплата.");
if ($ymlSet['allnalich'] > 0) {
    $allnalich = $ymlSet['allnalich'];
}

$delivery = ($notdeliv ? "false" : "true");

if ($turbo) {
    $settingTurbo = getSettingsManifestBlock('yml_turbo_setting', 'yml_turbo');
    if ($settingTurbo && $settingTurbo['all_turbo'] !== "") {
        if ($settingTurbo['all_turbo']) {
            $all = true;
        }
        $delivery = ($settingTurbo['delivery_turbo'] ? 'true' : 'false');
        $store = $settingTurbo['store_turbo'];
        // $pickup = $settingTurbo['picup_turbo'];
        $allnalich = $settingTurbo['allnalich_turbo'];
        $sales_notes = ($settingTurbo['sales_notes_on_turbo'] ? ($settingTurbo['sales_notes_turbo'] ? $settingTurbo['sales_notes_turbo'] : "Необходима предоплата.") : null);
    }
} else {
    $settingTurbo = getSettingsManifestBlock('yml_setting', 'yml');
    if ($settingTurbo && $settingTurbo['all_yml'] !== "") {
        if ($settingTurbo['all_yml']) {
            $all = true;
        }
        $delivery = ($settingTurbo['delivery_yml'] ? 'true' : 'false');
        $store = $settingTurbo['store_yml'];
        // $pickup = $settingTurbo['picup_yml'];
        $allnalich = $settingTurbo['allnalich_yml'];
        $sales_notes = ($settingTurbo['sales_notes_on_yml'] ? ($settingTurbo['sales_notes_yml'] ? $settingTurbo['sales_notes_yml'] : "Необходима предоплата.") : null);
    }
}

// получить ID сайта и параметры
if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (!$catalogue) {
        $catalogue = $current_catalogue['Catalogue_ID'];
    }
}

if (!$catalogue) {
    echo "not catalogue";
    exit;
}

# категории
$categoriesArr = $db->get_results("select a.Subdivision_ID, a.Subdivision_Name, a.Parent_Sub_ID, b.Class_ID from Subdivision as a, Sub_Class as b where " . (!isset($all) ? "a.inMarket = 1 AND" : "") . " a.Subdivision_ID=b.Subdivision_ID AND b.Class_ID=2001 AND a.Catalogue_ID = '$catalogue'", ARRAY_A);
if ($categoriesArr) {
    foreach ($categoriesArr as $c) {
        if ($c['Subdivision_Name'] == 'Поиск по каталогу') {
            continue;
        }
        $category .= "<category id=\"{$c['Subdivision_ID']}\"" . ($c['Parent_Sub_ID'] ? " parentId=\"{$c['Parent_Sub_ID']}\"" : null) . ">" . htmlspecialchars($c['Subdivision_Name']) . "</category>\n";
        $subid = $c['Subdivision_ID'];
        $subs[] = $subid;
        $subname[$subid] = $c['Subdivision_Name'];
        //$subdescr[$subid] = $c['DescriptionObj'];  /* не используется, отключено */
    }
}
unset($categoriesArr);
# товары
$sql = "SELECT * FROM Message2001 where Checked = 1 AND Price > 0 AND Subdivision_ID IN (" . implode(",", $subs) . ")";

$itemsArr = $db->get_results($sql, ARRAY_A);
if ($itemsArr) {
    echo "-1-\n";
    flush();
    ob_flush();
    foreach ($itemsArr as $i) {
        if (!$i['Message_ID']) {
            continue;
        }
        unset($price);
        unset($newprice);
        unset($importFile);
            $variant_ID = 0;
            $subdiv = $i['Subdivision_ID'];
            $name = trim(htmlspecialchars(strip_tags($i['name'])));
            
            # стоп слова в имени
        if (mb_stristr($name, "б/у") || mb_stristr($name, "комиссионный") || mb_stristr($name, "некомплект") || mb_stristr($name, "некондиция") || mb_stristr($name, "потертости")) {
            continue;
        }
            
            $img = $db->get_row("select * from Multifield where Message_ID = '{$i['Message_ID']}' AND Field_ID = '2353' ORDER BY Priority LIMIT 0,1", ARRAY_A);
            $price = $i['price'];
        if (($i['discont'] || $i['pricediscont'] > 0) && $i['disconttime'] && dateCompare(date("d.m.Y H:i"), $i['disconttime'], "minutes", 1) > 0) {
            if ($i['discont'] > 0) {
                $newprice = $price - $price * $i['discont'] / 100;
            }
            if ($i['pricediscont'] > 0) {
                $newprice = $i['pricediscont'];
            }
        }
            
        if ($img['Path']) { // фото есть в базе
            $importFile[] = $img['Path'];
        } else {
            if ($i['photourl']) {
                foreach (explode(",", $i['photourl']) as $ph) {
                    if (!strstr($i['photourl'], "http://") && !strstr($i['photourl'], "https://")) {
                          $ph = trim($ph);
                          $importFile[] = $HTTP_FILES_PATH . "import/" . (stristr($ph, "jpg") || stristr($ph, "jpeg") || stristr($ph, "gif") || stristr($ph, "png") ? $ph : $ph . ".jpg");
                    } else {
                        $importFile[] = $ph;
                    }
                }
            }

            # photo on ftp
            if ($i['code'] && !$importFile) {
                if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile($i['code']))) {
                    $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile($i['code']);
                }
            }
            if ($i['art'] && !$importFile) {
                if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile($i['art'], "jpeg"))) {
                    $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile($i['art'], "jpeg");
                }
                if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile($i['art'])) && (!$importFile || (!in_array($HTTP_FILES_PATH . "/import/" . normArtFile($i['art']), $importFile) && $importFile))) {
                    $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile($i['art']);
                }
                if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile2($i['art'], "jpg")) && (!$importFile || (!in_array($HTTP_FILES_PATH . "/import/" . normArtFile2($i['art'], "jpg"), $importFile) && $importFile))) {
                    $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile2($i['art'], "jpg");
                }

                for ($ii = 2; $ii < 6; $ii++) {
                    if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile($i[art] . "_" . $ii)) && (!$importFile || (!in_array($HTTP_FILES_PATH . "/import/" . normArtFile($i[art] . "_" . $ii), $importFile) && $importFile))) {
                        $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile($i[art] . "_" . $ii);
                    }
                    if (@file_exists($DOCUMENT_ROOT . $HTTP_FILES_PATH . "/import/" . normArtFile2($i[art] . "_" . $ii)) && (!$importFile || (!in_array($HTTP_FILES_PATH . "/import/" . normArtFile2($i[art] . "_" . $ii), $importFile) && $importFile))) {
                        $importFile[] = $HTTP_FILES_PATH . "/import/" . normArtFile2($i[art] . "_" . $ii);
                    }
                    echo "\r\n";
                    flush();
                    ob_flush();
                }
            }
        }
        if ($turbo) {
            $description = "";
            $description = ($i['text'] ? $i['text'] : '');
            if (!$description) {
                $description = "Приобрести {$name} Вы можете в компании {$current_catalogue['Catalogue_Name']} " . ($i['stock'] > 0 ? "в наличии {$i['stock']} шт" : "под заказ") . "";
            }

            if (isset($importFile) && !empty($importFile) && $description) {
                if (!$i['buyvariable']) { // основной товар
                    $items[] = "		<offer id=\"{$i['Message_ID']}\" available=\"" . ($i['stock'] > 0 || $allnalich ? "true" : "false") . "\">
						<url>" . $domenUrl . nc_message_link($i['Message_ID'], 2001) . "</url>
						<price>" . floatval(str_replace(",", ".", ($newprice ? $newprice : $price))) . "</price>
						<currencyId>RUR</currencyId>
						<categoryId>{$i['Subdivision_ID']}</categoryId>
						" . viewimg($importFile) . "
						" . (!$store ? "<store>false</store>" : "<store>true</store>") . "
						
						<delivery>" . $delivery . "</delivery>
						<name>" . (is_numeric($name) ? $subname[$subdiv] . " №" : "") . "{$name}</name>
						" . ($i['vendor'] ? "<vendor>" . htmlspecialchars($i['vendor']) . "</vendor>\n" : null) . "
						<description>" . htmlspecialchars(strip_tags($description)) . "</description>
						" . ($i['ves'] > 0 ? "<weight>" . $i['ves'] . "</weight>" : "") . "
						" . ($sales_notes ? "<sales_notes>{$sales_notes}</sales_notes>" : null) . "
						</offer>\n";
                }
                    
                $variantsArr = orderArray($i['variable']);
                    
                if (!isset($novar) && $variantsArr) { // варианты товаров
                    foreach ($variantsArr as $variantid => $variant) {
                        unset($newprice);
                        if ($variant[name] && $variant[price] > 0) {
                            $price = $variant[price];
                            if (($i['discont'] || $i['pricediscont'] > 0) && $i['disconttime'] && dateCompare(date("d.m.Y H:i"), $i['disconttime'], "minutes", 1) > 0) {
                                if ($i['discont'] > 0) {
                                    $newprice = $price - $price * $i['discont'] / 100;
                                }
                                if ($i['pricediscont'] > 0) {
                                    $newprice = $i['pricediscont'];
                                }
                            }
                            
                            $nameV = trim(htmlspecialchars(strip_tags("$i[name] $variant[name]")));
                            $items[] = "		<offer id=\"{$i['Message_ID']}v{$variant_ID}\" available=\"" . ($i['stock'] > 0 || $allnalich ? "true" : "false") . "\">
									<url>" . $domenUrl . nc_message_link($i['Message_ID'], 2001) . "#v_{$variant_ID}</url>
									<price>" . intval(round($newprice ? $newprice : $price)) . "</price>
									<currencyId>RUR</currencyId>
									<categoryId>{$i['Subdivision_ID']}</categoryId>
									" . viewimg($importFile) . "
									" . (!$store ? "<store>false</store>" : "<store>true</store>") . "
									
									<delivery>" . $delivery . "</delivery>
									<name>" . (mb_strlen(preg_replace("/\d/i", "", $name)) < 6 ? $subname[$subdiv] . " №" : "") . "{$nameV}</name>
									" . ($i['vendor'] ? "<vendor>{$i['vendor']}</vendor>\n" : null) . "
									<description>" . htmlspecialchars(strip_tags($description)) . "</description>
									" . ($i['ves'] > 0 ? "<weight>" . $i['ves'] . "</weight>" : "") . "
                                    " . ($sales_notes ? "<sales_notes>{$sales_notes}</sales_notes>" : null) . "
								</offer>\n";
                        }
                        $variant_ID++;
                    }
                }
            }
        } else {
            if (!$i['buyvariable']) { // основной товар
                $items[] = "		<offer id=\"{$i[Message_ID]}\" available=\"" . ($i[stock] > 0 || $allnalich ? "true" : "false") . "\">
					<url>http://" . $current_catalogue['Domain'] . nc_message_link($i[Message_ID], 2001) . "</url>
					<price>" . floatval(str_replace(",", ".", ($newprice ? $newprice : $price))) . "</price>
					<currencyId>RUR</currencyId>
					<categoryId>{$i[Subdivision_ID]}</categoryId>
					" . viewimg($importFile) . "
					" . (!$store ? "<store>false</store>" : "<store>true</store>") . "
					
					<delivery>" . $delivery . "</delivery>
					<name>" . (is_numeric($name) ? $subname[$subdiv] . " №" : "") . "{$name}</name>
					" . ($i[vendor] ? "<vendor>" . htmlspecialchars($i[vendor]) . "</vendor>\n" : null) . ($i[text] ? "<description>
					" . htmlspecialchars(strip_tags($i[text])) . "
					</description>\n" : null) . "
					" . ($i[ves] > 0 ? "<weight>" . $i[ves] . "</weight>" : "") . "
					" . ($sales_notes ? "<sales_notes>{$sales_notes}</sales_notes>" : null) . "
					</offer>\n";
            }
                
            $variantsArr = orderArray($i['variable']);
                
            if (!isset($novar) && $variantsArr) { // варианты товаров
                foreach ($variantsArr as $variantid => $variant) {
                    unset($newprice);
                    if ($variant[name] && $variant[price] > 0) {
                        $price = $variant[price];
                        if (($i['discont'] || $i['pricediscont'] > 0) && $i['disconttime'] && dateCompare(date("d.m.Y H:i"), $i['disconttime'], "minutes", 1) > 0) {
                            if ($i['discont'] > 0) {
                                $newprice = $price - $price * $i['discont'] / 100;
                            }
                            if ($i['pricediscont'] > 0) {
                                $newprice = $i['pricediscont'];
                            }
                        }
                        
                        $nameV = trim(htmlspecialchars(strip_tags("$i[name] $variant[name]")));
                        $items[] = "		<offer id=\"{$i[Message_ID]}v{$variant_ID}\" available=\"" . ($i[stock] > 0 || $allnalich ? "true" : "false") . "\">
								<url>http://" . $current_catalogue['Domain'] . nc_message_link($i[Message_ID], 2001) . "#v_{$variant_ID}</url>
								<price>" . intval(round($newprice ? $newprice : $price)) . "</price>
								<currencyId>RUR</currencyId>
								<categoryId>{$i[Subdivision_ID]}</categoryId>
								" . viewimg($importFile) . "
								" . (!$store ? "<store>false</store>" : "<store>true</store>") . "
								
								<delivery>" . $delivery . "</delivery>
								<name>" . (mb_strlen(preg_replace("/\d/i", "", $name)) < 6 ? $subname[$subdiv] . " №" : "") . "{$nameV}</name>
								" . ($i[vendor] ? "<vendor>{$i[vendor]}</vendor>\n" : null) . ($i[text] ? "<description>
								" . htmlspecialchars(strip_tags($i[text])) . "
								</description>\n" : null) . "
								" . ($i[ves] > 0 ? "<weight>" . $i[ves] . "</weight>" : "") . "
                                " . ($sales_notes ? "<sales_notes>{$sales_notes}</sales_notes>" : null) . "
							</offer>\n";
                    }
                    $variant_ID++;
                }
            }
        }
            echo ". ";
            flush();
            ob_flush();
    }
}

$xml = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="' . date("Y-m-d H:i") . '">
<shop>
	<name>' . $current_catalogue['Catalogue_Name'] . '</name>
	<company>' . $current_catalogue['Catalogue_Name'] . '</company>
	<url>' . $domenUrl . '</url>
	<currencies>
	<currency id="RUR" rate="1" />
	</currencies>
	<categories>
		' . $category . '
	</categories>
	<offers>
		' . implode("", $items) . '
	</offers>
</shop>
</yml_catalog>';

if (file_put_contents($ROOTDIR . $pathInc . "/" . $filenameYml, $xml)) {
    echo "YML файл сформирован: <a href='{$domenUrl}{$pathInc}/{$filenameYml}'>{$domenUrl}{$pathInc}/{$filenameYml}</a>";
} else {
    echo "file not put";
}


function viewimg($img1 = '')
{
    global $current_catalogue, $domenUrl;
    if (!$img1) {
        return false;
    }
    foreach ($img1 as $img) {
        $im .= (trim($img) ? "<picture>" . (!strstr($img, "http://") && !strstr($img, "https://") ? $domenUrl : "") . "" . trim($img) . "</picture>\r\n" : null);
    }
    return $im;
}


# json в массив с исправлением  ошибочных записей
/*function orderArray($orderlist) {
    if ((strstr($orderlist,"u04") && !strstr($orderlist,"\u04")) || (strstr($orderlist,"u00") && !strstr($orderlist,"\u00"))) {
        $z = array("u04"=>"\u04", "u00"=>"\u00", "u21"=>"\u21", "u20"=>"\u20");
        $orderlist = strtr($orderlist,$z);
    } else {
        $orderlist = $orderlist;
    }
    if ($orderlist) return json_decode(html_entity_decode($orderlist),1); else return "0";
}*/
