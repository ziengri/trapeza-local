<?php

use App\modules\Korzilla\Service\Delivery\Cdek\CalculatorData as CdekCalculatorData;
use App\modules\Korzilla\Service\Delivery\Cdek\Cdek;

class userfunction
{
    private $curCat, $catID;

    public function __construct()
    {
        global $db, $nc_core, $perm, $AUTH_USER_ID, $ADMIN_PATH, $perm;
        $this->curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $this->catID = $this->curCat['Catalogue_ID'];
    }

    # вызов действия
    public function init($action)
    {
        global $catID;
        switch ($action) {
            case 'cbr':
                return $this->cbr();
            case 'backup':
                return $this->backupDisk();
            case 'yadisk':
                return $this->yaDisk();
            case 'uniteller':
                return $this->uniteller();
            case 'citylist':
                return $this->getCitesList();
            case 'comparison':
                return $this->comparison();
            case 'searchlife':
                return $this->searchlife();
            case 'fav_change':
                return $this->favChange();
            case 'mobileInfo':
                return $this->mobileInfo();
            case 'mobile_menu':
                return $this->mobileMenu();
            case 'getItemsLoad':
                return $this->getItemsLoad();
            case 'delivery_days':
                return $this->deliveryDays();
            case 'edost_delivery':
                return $this->edostCalcDelivery();
            case 'uniteller_confirm':
                return $this->unitellerConfirm();
            case 'checkOrderStatus_sberbank':
                return $this->checkOrderStatus('sberbank');
            case 'yookassa':
                return $this->checkOrderStatus('yookassa');
            case 'filter':
                return $this->filter();
            case 'langlist':
                return $this->getLangList();
            case 'cdek':
                return $this->cdek();
            case 'get_org_by_inn':
                return $this->getOrgByInn($_POST['inn']);
            case 'productsLifeSearch':
                return $this->productsLifeSearch();
            case 'productsLifeSearchAddProductByID':
                return $this->productsLifeSearchAddProductByID();
            case 'pochta_russia':
                return $this->pochtaRussia();
            case 'delete_current_user':
                return $this->deleteCurrentUser();
            default:
                break;
        }
        ;
    }
    public function getLangList()
    {
        global $langs, $setting, $current_catalogue;
        if ($setting['language']) {
            # выбор языка
            foreach ($langs[lang] as $key => $lng) {
                $link = ($key != $langs['main']['keyword'] ? "{$key}." : "") . $current_catalogue['Domain'];
                $langselect .= "<option " . ($key == $langs['langnow'] ? "selected" : "") . " data-domain='{$current_catalogue['Domain']}' data-link='//{$link}'>" . ($setting['language_select'] == 2 ? $lng['name'] : $key) . "</option>";
            }
            $langselect = "<div class='regper_link lang-list'><select class='select-style'>{$langselect}</select></div>";
        }
        return $langselect;
    }
    private function uniteller()
    {
        global $db, $DOCUMENT_ROOT, $pathInc;

        switch ($_POST['Status']) {
            case 'authorized':
                $status = 4;
                break;
            case 'canceled':
                $status = 3;
                break;
            case 'paid':
                $status = 2;
                break;
            default:
                $status = 0;
                break;
        }

        $id = securityForm($_POST['Order_ID']);

        if ($id && $status)
            $orderListDB = $db->query("UPDATE Message2005 SET statusOplaty = {$status} WHERE Message_ID = {$id}");

        header("Location: //{$_SERVER[HTTP_HOST]}/");
    }

    private function unitellerConfirm()
    {
        global $setting, $db, $DOCUMENT_ROOT, $pathInc;

        $dir = $DOCUMENT_ROOT . $pathInc . '/uniteller/log';

        if (!file_exists($dir))
            mkdir($dir);

        $id = securityForm($_GET['order_id']);

        if ($id) {
            $orderListDB = $db->get_var("SELECT orderlist FROM Message2005 WHERE Message_ID = {$id}");

            $orderListDB = html_entity_decode($orderListDB);
            $orderListDB = str_replace(' "', ' ', $orderListDB);
            $orderListDB = str_replace('" ', ' ', $orderListDB);
            $orderListDB = str_replace(':""', ":null", $orderListDB);
            $orderListDB = str_replace('""', '"', $orderListDB);

            $order = orderArray($orderListDB);

            $tax = $setting['unitellerTax'] ? (int) $setting['unitellerTax'] : 0;
            $vat = $setting['unitellerVat'] ? (int) $setting['unitellerVat'] : -1;
            $payattr = $setting['unitellerPayattr'] ? (int) $setting['unitellerPayattr'] : 1;
            $lineattr = $setting['unitellerLineattr'] ? (int) $setting['unitellerLineattr'] : 1;

            $Receipt = array();
            foreach ($order['items'] as $itemID => $item) {
                $Receipt['lines'][] = array(
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $item['count'],
                    'sum' => $item['sum'],
                    'vat' => $vat,
                    'taxmode' => $tax,
                    'payattr' => $payattr,
                    'lineattr' => $lineattr
                );
            }

            if ($order['delivery']['sum_result'] > 0) {
                $Receipt['lines'][] = array(
                    'name' => addslashes($order['delivery']['name']),
                    'price' => $order['delivery']['sum_result'],
                    'qty' => 1,
                    'sum' => $order['delivery']['sum_result'],
                    'vat' => $vat,
                    'taxmode' => $tax,
                    'payattr' => $payattr,
                    'lineattr' => $lineattr
                );
            }

            $Receipt['total'] = $order['totaldelsum'];
            $Receipt = $Receipt;

            $ReceiptSignature = strtoupper(
                hash(
                    'sha256',
                    hash('sha256', $setting['unitellerUPID'])
                    . '&' . hash('sha256', $id)
                    . '&' . hash('sha256', $order['totaldelsum'])
                    . '&' . hash('sha256', base64_encode(json_encode($Receipt)))
                    . '&' . hash('sha256', $setting['unitellerPass'])
                )
            );

            $post = array(
                'ShopID' => $setting['unitellerUPID'],
                'OrderID' => $id,
                'Receipt' => base64_encode(json_encode($Receipt)),
                'ReceiptSignature' => $ReceiptSignature,
                'Subtotal' => $order['totaldelsum']
            );

            $post['Signature'] = unitellerGetSinatere($post, array('ShopID', 'OrderID', 'Subtotal'), $setting['unitellerPass']);

            if (!file_exists($dir . "/order_{$id}_data.txt")) {
                $data = array(
                    '$_POST' => $_POST,
                    '$_GET' => $_GET,
                    'post' => $post,
                    'Receipt' => $Receipt
                );
                file_put_contents($dir . "/order_{$id}_data.txt", json_encode($data));
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://fpay.uniteller.ru/v1/api/iaconfirm');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post, '', '&'));
            $result = curl_exec($curl);
            curl_close($curl);

            $result = new SimpleXMLElement($result);

            if (!file_exists($dir . "/order_{$id}_result.txt"))
                file_put_contents($dir . "/order_{$id}_result.txt", $result);
        }
    }

    # Города таргетинг
    private function getCitesList()
    {
        if (function_exists('function_getCitesList')) {
            return function_getCitesList(); // своя функция
        } else {
            global $cityvars, $setting, $HTTP_HOST, $cityid, $current_catalogue, $curdomen, $THIS_HOST;

            if ($cityvars) {
                $count = count($cityvars) > 10 && !$_GET[mobile] ? true : false;
                if (!$setting['language']) {
                    $ARRAY_HOST = explode(".", $HTTP_HOST);
                    if (stristr($HTTP_HOST, $THIS_HOST)) { # на тестовом домене
                        $curdomen = ($ARRAY_HOST[3] ? $ARRAY_HOST[1] . "." . $ARRAY_HOST[2] . "." . $ARRAY_HOST[3] : $HTTP_HOST);
                    } else { # основной домен
                        $curdomen = ($ARRAY_HOST[2] ? $ARRAY_HOST[1] . "." . $ARRAY_HOST[2] : $HTTP_HOST);
                    }
                } else {
                    $curdomen = $HTTP_HOST;
                    foreach ($cityvars as $key => $val) {
                        $cityvars[$key]['name'] = getLangCityName($val['name'], $key);
                    }
                }


                if ($count) {
                    asort($cityvars);
                    $incol = ceil(count($cityvars) / 4) + 5;
                }
                foreach ($cityvars as $trgID => $trg) {
                    $ii++;
                    if ($count) {
                        $curLet = mb_substr($trg['name'], 0, 1);
                        if ($let != $curLet) {
                            $newcol = 1;
                            $citylistselect .= ($citylistselect ? "</ul>" : NULL);
                            if ($ii >= $incol && $newcol) {
                                $citylistselect .= "</div><div class='modal_city_col'>";
                                $ii = 1;
                                $modal_city_col = 1;
                            }
                            $citylistselect .= "<ul>";
                            $citylistselect .= "<li class=let>{$curLet}</li>";
                            $let = $curLet;
                        } else {
                            $newcol = '';
                        }
                    }
                    //$cityurl = ($setting['targdomen'] ? "http://".($trg['keyword'] && $trgID>0  ? $trg['keyword']."." : NULL)."{$curdomen}".$_SERVER[REQUEST_URI] : "/city/{$trg['keyword']}/contacts/");
                    if ($setting['targdomen'] && stristr($current_catalogue[Mirrors], $trg['keyword'] . ".{$curdomen}")) {
                        $cityurl = "//" . ($trg['keyword'] && $trgID > 0 ? $trg['keyword'] . "." : NULL) . "{$curdomen}" . preg_replace("/http[s]{0,1}:\/\/.*\/(.*)/U", "/", $_SERVER[HTTP_REFERER]);
                        $domaincity = ($trg['keyword'] && $trgID > 0 ? $trg['keyword'] . "." : NULL) . $curdomen;
                    } else {
                        $cityurl = "/city/{$trg['keyword']}/contacts/";
                        $domaincity = $curdomen;
                    }

                    /*$citylistselect .= "<li class='".(isset($cityid) && $cityid==$trgID ? "act" : "")." dotted'>
                                            <a href='{$cityurl}' data-cityid='{$trgID}'><span>{$trg['name']}</span></a>
                                        </li>";*/
                    // 	$linkcitydomen = (!stristr($current_catalogue[Mirrors], $trg['keyword'].".{$curdomen}") && $_SERVER[HTTP_HOST]!="$curdomen" ? "//{$curdomen}" : NULL);
                    $domenName = getLangWord($trg['name']);
                    $citylistselect .= "<li class='trg_{$trg['keyword']} " . (isset($cityid) && $cityid == $trgID ? "act" : "") . " dotted " . (stristr($current_catalogue[Mirrors], $trg['keyword'] . ".{$curdomen}") ? "targdomen" : "targcookie") . "'>
                        <a rel='nofollow' href='{$linkcitydomen}{$cityurl}' data-cityid='{$trgID}' " . ($_SERVER[HTTP_HOST] != $domaincity ? "data-dom='{$curdomen}'" : NULL) . ">
                        <span>{$domenName}</span></a>
                    </li>";

                }
            }

            return $citylistselect ? ($modal_city_col ? "<div class='modal_city_col'>" : "<ul>") . $citylistselect . ($modal_city_col ? "</div>" : "</ul>") : "";
        }
    }

    # Мобильное меню
    private function mobileInfo()
    {
        global $setting, $setting_texts, $db, $cityphone, $cityname, $AUTH_USER_ID, $cityid;

        $where_targeting = "";
        if ($setting['targeting'] && ($cityid >= 0 || !$cityid)) {
            if (!isset($cityid))
                $cityid = 9999;
            $where_targeting = "(citytarget like '%,{$cityid},%' OR citytarget IS NULL OR citytarget = '' OR citytarget = ',,')";
        }

        $blsub = $db->get_var(
            "SELECT 
                Subdivision_ID as sub 
            FROM 
                Sub_Class 
            WHERE 
                Class_ID = '2016' 
                AND Catalogue_ID = '{$this->catID}' 
            LIMIT 0,1"
        );

        if ($blsub)
            $blocksInfo = $db->get_results("select Message_ID, name, phpset, settings, block_id from Message2016 where Checked = 1 AND phpset like '%\"contenttype\":\"3\"%' AND Subdivision_ID = '{$blsub}' AND inmob != 2 " . ($where_targeting ? "AND {$where_targeting}" : "") . " ORDER BY Priority", ARRAY_A);

        if ($blocksInfo) {
            $infoHtml = $telHtml = $textHtml = "";
            foreach ($blocksInfo as $b) {
                unset($contset);
                if ($b['phpset']) { // настройки php
                    $phpset = orderArray($b['phpset']);
                    if ($phpset['contsetclass']) {
                        $contset = $phpset['contsetclass'];
                    }
                }
                if ($contset) {
                    if (!$contacts) {
                        $contacts = $db->get_row(
                            "SELECT 
                                a.time, 
                                a.targetcode, 
                                a.targetcode2, 
                                a.targetphone, 
                                a.targetphone2,
                                a.soc_show
                            FROM 
                                Message2012 as a, 
                                Subdivision as b 
                            WHERE 
                                a.Checked = 1 
                                AND a.Subdivision_ID = b.Subdivision_ID 
                                AND b.Catalogue_ID = '{$this->catID}' 
                            ORDER BY 
                                a.time DESC, 
                                a.Priority 
                            LIMIT 0,1",
                            ARRAY_A
                        );
                    }

                    if (!$textHtml && $contset['phonetext']) {
                        # текст (не используется)

                        $textHtml = "<div class='mh-phone-text' {$b['Message_ID']}>" . ($contset['phonetext'] ? str_replace("rn", "<br>", $contset['phonetext']) : $contacts['time']) . "</div>";
                    }
                    if (!$telHtml && $contset['showphones']) {
                        $kod1 = ($cityphone['targetcode'] || $cityphone['targetphone'] ? $cityphone['targetcode'] : ($contset['phonekod1'] ? $contset['phonekod1'] : $contacts['targetcode']));
                        $phone1 = ($cityphone['targetcode'] || $cityphone['targetphone'] ? $cityphone['targetphone'] : ($contset['phone1'] ? $contset['phone1'] : $contacts['targetphone']));
                        $kod2 = ($contset['phonekod2'] ? $contset['phonekod2'] : $contacts['targetcode2']);
                        $phone2 = ($contset['phone2'] ? $contset['phone2'] : $contacts['targetphone2']);

                        $hidephone = $setting['hidephone'] ? '' : 'hidephone ';
                        if ($phone1) {
                            $telHtml .= "<div class='mh-phone {$hidephone}'>
                                            <a href='tel:" . clearPhone($kod1 . " " . $phone1) . "' data-metr='headphone'>" . $kod1 . " " . $phone1 . "</a>
                                            " . ($hidephone ? "<span class='show_phone' data-metr='showphone'>Показать телефон</span>" : "") . "
                                        </div>";
                        }
                        if ($phone2) {
                            if (!($kod1 == $kod2 && $phone1 == $phone2)) {
                                $telHtml .= "<div class='mh-phone {$hidephone}'>
                                                <a href='tel:" . clearPhone($kod2 . " " . $phone2) . "' data-metr='headphone'>" . $kod2 . " " . $phone2 . "</a>
                                                " . ($hidephone ? "<span class='show_phone' data-metr='showphone'>Показать телефон</span>" : "") . "
                                            </div>";
                            }
                        }
                    }
                    if ($contset['targeting'] && $setting['targeting']) {
                        $targeting = "<div class='mh-info iconsCol icons i_city'>" . getCityLink() . "</div>";
                    }

                    if ($contacts['soc_show']) {

                        $subSoc = $db->get_row(
                            "SELECT 
                                Subdivision_ID as sub,
                                Sub_Class_ID as cc
                            FROM 
                                Sub_Class
                            WHERE 
                                Catalogue_ID = {$this->catID}
                                AND Class_ID = 2011
                            LIMIT 0,1",
                            ARRAY_A
                        );

                        if (!empty($subSoc))
                            $socialMedia = nc_objects_list($subSoc['sub'], $subSoc['cc'], "&recNum=200");
                    }
                }
            }
        }
        if ($textHtml || $telHtml || $socialMedia) {
            $telHtmlRes = "<div class='mh-phone-body'>{$textHtml}{$telHtml}{$socialMedia}</div>";
        }
        $contactLink = $db->get_var("select Hidden_URL from Subdivision where Subdivision_ID = (select Subdivision_ID from Sub_Class where Class_ID = '2012' AND Catalogue_ID = '{$this->catID}' LIMIT 1) LIMIT 0,1");
        if ($contactLink) {
            $contactLinkHtml = "<div class='mh-line-btn mh-line-contacts'>
                                    <a href='{$contactLink}' class='mainmenubg'>" . ($setting_texts['link_contacts']['checked'] ? $setting_texts['link_contacts']['name'] : "Все контакты") . "</a>
                                </div>";
        }
        $html = "<div class='mobile-info' {$r}>
                    <div class='mobile-info-head'>
                        <div class='mh-head-title'>" . ($targeting ? $targeting : getLangWord("lang_sub_contacts", 'Контакты')) . "</div>
                        <div class='mh-head-close'><a href='#' class='lc-close'></a></div>
                    </div>
                    <div class='mobile-info-body'>
                        <div class='mh-body'>
                            {$telHtmlRes}
                            <div class='mh-line-btn'>
                                <a href='/feedback/add_feedback.html?isNaked=1' title='" . ($setting_texts['link_mail']['checked'] ? $setting_texts['link_mail']['name'] : "Получить консультацию") . "' data-metr='mailtoplink' data-rel='lightcase' data-lc-options='{\"maxWidth\":380,\"groupClass\":\"feedback modal-form\"}'>" . ($setting_texts['link_mail']['checked'] ? $setting_texts['link_mail']['name'] : "Получить консультацию") . "</a>
                            </div>
                            <div class='mh-line-btn'>
                                <a href='/callme/add_callme.html?isNaked=1' title='" . ($setting_texts['link_call']['checked'] ? $setting_texts['link_call']['name'] : "Обратный звонок") . "'data-metr='calltoplink' data-rel='lightcase' data-lc-options='{\"maxWidth\":390,\"groupClass\":\"callme modal-form\"}'>" . ($setting_texts['link_call']['checked'] ? $setting_texts['link_call']['name'] : "Обратный звонок") . "</a>
                            </div>
                            {$contactLinkHtml}
                        </div>
                    </div>
                </div>";
        return $html;
    }

    # Мобильное меню
    private function mobileMenu()
    {
        if (function_exists('function_mobileMenu')) {
            return function_mobileMenu(); // своя функция
        } else {
            global $setting, $AUTH_USER_ID, $db, $catalogue, $mobile_menu_drop, $widgetArr, $authInBlock, $cityid;

            $html = "";
            // Авторизация
            if ($setting[allowreg] && $authInBlock)
                $html .= "<div class='mobile-user iconsCol icons i_user'>
                                                " . ($AUTH_USER_ID
                    ? "<a href='/profile/'>Личный кабинет</a>"
                    : "<a href='/profile/?isNaked=1' title='Вход' data-rel='lightcase' data-lc-options='{\"maxWidth\":320,\"groupClass\":\"login\"}'>Вход</a> | <a href='/registration/'>Регистрация</a>") . "
                                            </div>";
            // блоки
            $blsub = $db->get_var("SELECT Subdivision_ID as sub FROM Sub_Class WHERE Class_ID = '2016' AND Catalogue_ID='{$this->catID}' LIMIT 0,1");
            $zoneid = $db->get_var("SELECT zone_id FROM Message2000 WHERE Catalogue_ID = {$catalogue} AND zone_position = 6 LIMIT 1");
            if ($zoneid) {
                $where = "col = {$zoneid} AND Checked = 1 AND Subdivision_ID = '{$blsub}'";
                $where = getLangQuery($where);

                $where_targeting = "";
                if ($setting['targeting'] && ($cityid >= 0 || !$cityid)) {
                    if (!isset($cityid))
                        $cityid = 9999;
                    $where_targeting = "(citytarget like '%,{$cityid},%' OR citytarget IS NULL OR citytarget = '' OR citytarget = ',,')";
                }
                if ($where_targeting)
                    $where .= "AND {$where_targeting}";

                $blocks = $db->get_results("SELECT Message_ID, name, sub, cc, phpset, settings, text, block_id, notitle, cssclass FROM Message2016 WHERE {$where} ORDER BY Priority", ARRAY_A);
            }

            if ($blocks)
                foreach ($blocks as $b) {
                    $blockHtml = $contentSubBlock = $contentBlock = "";
                    if ($b['phpset']) { // настройки php
                        $phpset = orderArray($b['phpset']);
                        if ($phpset['contsetclass']) {
                            $contset = $phpset['contsetclass'];
                            foreach ($phpset['contsetclass'] as $k => $v) {
                                $contsetclass .= "&{$k}={$v}";
                            }
                            // баг
                            // if (!$contset[nc_ctpl])
                            //     $contsetclass .= "&nc_ctpl=1";
                        }
                    }
                    $settings = orderArray($b['settings']); // настройки визуальные

                    $params = subParams($b['sub']);
                    $name = ($b['name'] ? $b['name'] : $params['name']);

                    /* КОНТЕНТ БЛОКА */
                    if ($b['text']) { // содершимое - текст
                        $contentBlock = strtr($b['text'], $widgetArr);
                    }

                    if ($b[sub] > 0 && $b[cc] > 0 && $phpset[contenttype] == 1) { // содершимое - из раздела
                        $contentSubBlock = strtr(nc_objects_list($b[sub], $b[cc], "&width={$b[width]}&vars={$b[vars]}&tsub={$b[sub]}&tcc={$b[cc]}&msg={$b[msg]}&mesid={$b[Message_ID]}&block_id={$b[block_id]}&isTitle=1&recNum={$b[recnum]}&rand={$b[rand]}&link={$params[url]}&name={$name}" . ($b[notitle] ? "&notitle=1" : NULL) . "&substr={$b[substr]}" . ($b[noindex] ? "&noindex=1" : "") . $contsetclass, 1), $widgetArr);
                    }

                    if ($contentBlock || $contentSubBlock) {
                        $blockHtml .= "<div class='mob-basictext'>
                                        " . ($contentBlock ? "<div class='mob-basictext-contentBlock'>" . $contentBlock . "</div>" : "") . "
                                        " . ($contentSubBlock ? "<div class='mob-basictext-contentSubBlock'>" . $contentSubBlock . "</div>" : "") . "
                                    </div>";
                    }

                    if ($contset['menutpl'] > 0 && $phpset[contenttype] == 2) { // содержимое - меню
                        # мобильное меню
                        $mobile_menu[0]['prefix'] = "<ul class='mobile-menu'>";
                        $mobile_menu[0]['suffix'] = "</ul>";
                        $mobile_menu[0]['unactive'] = "<li class='sub%SUB'><a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></li>";
                        $mobile_menu[0]['active'] = "<li class='sub%SUB active'><a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></li>";
                        # мобильное меню РАСКРЫВАШКА
                        $mobile_menu_drop[0]['prefix'] = "\";global \$mobile_menu_drop;\$result.=\"<ul class='mobile-menu-drop'>";
                        $mobile_menu_drop[0]['suffix'] = $mobile_menu[0]['suffix'];
                        $mobile_menu_drop[0]['unactive'] = "\".opt(\$menu=s_browse_sub(\$data[\$i][Subdivision_ID], \$mobile_menu_drop[1]),\"\").\"
                                                            <li class='\".(\$menu ? \"menu-open\" : NULL).\" sub%SUB'>
                                                                <a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a>
                                                                \".opt(\$menu, \"<ul class='menu-second'>
                                                                    <div class='mblock-head'><a href='%URL'><span>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></div>
                                                                    <li class='mm-back'><a href='%URL'>\".getLangWord(\"mob_menu_back\", \"назад\").\"</a></li>
                                                                \$menu</ul>\").\"
                                                            </li> ";
                        $mobile_menu_drop[0]['active'] = "\".opt(\$menu=s_browse_sub(\$data[\$i][Subdivision_ID], \$mobile_menu_drop[1]),\"\").\"
                                                        <li class='active \".(\$menu ? \"menu-open\" : NULL).\" sub%SUB'>
                                                            <a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a>
                                                            \".opt(\$menu, \"<ul class='menu-second'>
                                                                    <div class='mblock-head'><a href='%URL'><span>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></div>
                                                                    <li class='mm-back'><a href='%URL'>\".getLangWord(\"mob_menu_back\", \"\".getLangWord(\"mob_menu_back\", \"назад\").\"\").\"</a></li>
                                                            \$menu</ul>\").\"
                                                        </li> ";

                        $mobile_menu_drop[1]['prefix'] = "\";global \$mobile_menu_drop;\$result.=\"";
                        $mobile_menu_drop[1]['suffix'] = "";
                        $mobile_menu_drop[1]['unactive'] = "\".opt(\$menu=s_browse_sub(\$data[\$i][Subdivision_ID], \$mobile_menu_drop[2]),\"\").\"
                                                        <li class='sub%SUB \".(\$menu ? \"menu-open\" : NULL).\"'>
                                                            <a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a>
                                                            \".opt(\$menu, \"<ul class='menu-second'>
                                                                <div class='mblock-head'><a href='%URL'><span>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></div>
                                                                <li class='mm-back'><a href='%URL'>\".getLangWord(\"mob_menu_back\", \"назад\").\"</a></li>
                                                            \$menu</ul>\").\"
                                                        </li> ";
                        $mobile_menu_drop[1]['active'] = "\".opt(\$menu=s_browse_sub(\$data[\$i][Subdivision_ID], \$mobile_menu_drop[2]),\"\").\"
                                                        <li class='active sub%SUB \".(\$menu ? \"menu-open\" : NULL).\"'>
                                                            <a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'><span class='mm-inner-img none'><img src='%icon' alt=''></span><span class='mm-inner'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a>
                                                            \".opt(\$menu, \"<ul class='menu-second'>
                                                                <div class='mblock-head'><a href='%URL'><span>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</span></a></div>
                                                                <li class='mm-back'><a href='%URL'>\".getLangWord(\"mob_menu_back\", \"назад\").\"</a></li>
                                                            \$menu</ul>\").\"
                                                        </li>";

                        $mobile_menu_drop[2]['prefix'] = "";
                        $mobile_menu_drop[2]['suffix'] = "";
                        $mobile_menu_drop[2]['unactive'] = "<li class='sub%SUB'><a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</a></li> ";
                        $mobile_menu_drop[2]['active'] = "<li class='sub%SUB active'><a href='%URL' class='\".noPjax(\$data[\$i][Subdivision_ID]).\"'>\".getLangWord(\"lang_sub_%KEYWORD\", \"%NAME\").\"</a></li> ";



                        $sortsubArr = array(1 => "Subdivision_Name");

                        $drop = $contset['dropmenu'] ? '_drop' : '';
                        $name_nav = ${'mobile_menu' . $drop};
                        if ($contset['sortsub'] > 0)
                            $name_nav[0]['sortby'] = $sortsubArr[$contset['sortsub']];
                        $blockHtml .= "<div class='mobile_menu{$drop}'>" . ($b[sub] > 1 ? s_browse_sub($b[sub], $name_nav[0]) : s_browse_level(0, $name_nav[0])) . "</div>";
                    }
                    if ($phpset[contenttype] == 3) { // содержимое - контакты

                        if ($contset[showphones]) {
                            $get_info = false;
                            $blocks_main = $db->get_results("SELECT Message_ID, name, sub, cc, phpset, settings, text, block_id, notitle FROM Message2016 WHERE col != {$zoneid} AND Checked = 1 AND Subdivision_ID = '{$blsub}' ORDER BY Priority", ARRAY_A);
                            foreach ($blocks_main as $b_main) {
                                if ($b_main['phpset']) { // настройки php
                                    $phpset_main = orderArray($b_main['phpset']);
                                    if ($phpset_main['contsetclass']) {
                                        $contset_main = $phpset_main['contsetclass'];
                                        if ($contset_main[showphones]) {
                                            $get_info = true;
                                            $contset['phonekod1'] = $contset_main[phonekod1];
                                            $contset['phone1'] = $contset_main[phone1];
                                            $contset['phonekod2'] = $contset_main[phonekod2];
                                            $contset['phone2'] = $contset_main[phone2];
                                            break;
                                        }

                                    }
                                }
                            }
                            if (!$get_info)
                                continue;
                        }

                        $blockHtml .= smallcontacts($contset, "mobile");
                    }

                    if ((trim($blockHtml) && ($b[sub] > 0 && $b[cc] > 0 && $phpset[contenttype] == 1)) || $phpset[contenttype] != 1) {
                        $html .= "<div id='mobile-block{$b[block_id]}' class='mobile-block mblk-type-{$phpset[contenttype]} {$b[cssclass]}'>
                                " . (!$b[notitle] ? "<div class='mblock-head'><span>" . getLangWord("lang_blk_" . $b[block_id], $name) . "</span></div>" : "") . "
                                <div class='mblock-body'>{$blockHtml}</div>
                            </div>";
                    }



                }
            return $html;
        }
    }

    # Модуль подсчета кол-ва дней доставки
    private function deliveryDays()
    {

        $citymain = securityForm($_POST[citymain]);
        $cityname = securityForm($_POST[cityname]);

        return deliveryDays($citymain, $cityname);
    }

    # Подгрузка объектов
    private function getItemsLoad()
    {
        global $setting;
        $param = securityForm($_GET);
        if ($param) {
            foreach ($param as $key => $value) {
                if (!in_array($key, array('sub', 'cc')))
                    $getParam .= "&{$key}={$value}";
            }
            $result = nc_objects_list($param[sub], $param[cc], $getParam);
        }

        return $result;
    }

    # Добавление в сравнение
    private function comparison()
    {
        global $setting, $catalogue;
        $id = securityForm($_POST['id']);
        $action = securityForm($_POST['action']);

        if ($action && $id > 0) {
            switch ($action) {
                case 'add':
                    $_SESSION['comparison'][$id] = $catalogue;
                    $result = "Добавлено в сравнение";
                    break;
                case 'remove':
                    unset($_SESSION['comparison'][$id]);
                    $result = "Удалено из сравнения";
                    break;
                case 'removeAdd':
                    unset($_SESSION['comparison']);
                    $result = "Товары удалены из сравнения";
                    break;
                default:
                    # code...
                    break;
            }
        }
        if ($result) {
            return json_encode(
                array(
                    "title" => $result,
                    "succes" => $result
                )
            );
        } else {
            return json_encode(
                array(
                    "title" => "Ошибка сравнений",
                    "error" => "Ошибка сравнений"
                )
            );
        }

    }

    private function productsLifeSearchAddProductByID()
    {
        global $db, $catalogue;

        $mesid = securityForm($_POST['mesid']);
        $addid = securityForm($_POST['addid']);
        $sql = "SELECT `analog`
                FROM Message2001
                WHERE `Catalogue_ID` = {$catalogue}
                AND `Message_ID` = {$mesid}";

        $objs = [];
        $objs[0]["name"] = "test";
        $objs[0]["id"] = 123123123;
        if (!empty($mesid)) {
            $analogy = $db->get_results($sql, 'ARRAY_A');

        }

        if (!empty($addid)) {
            $sql = "SELECT `name`, `Message_ID` as id FROM `Message2001` WHERE `Message_ID` = {$addid}";
            $prodData = $db->get_results($sql, 'ARRAY_A');
            $index = 1; // нужно сделать в качестве приоритета
            foreach ($prodData as $data) {
                $objs[$index]["name"] = $data["name"];
                $objs[$index]["id"] = $data['id'];
                $id++;
            }
        }
        // echo "<pre>";
        // var_dump($mesid, $addid, $sql, $analogy, $prodData);
        // echo "</pre>";

        return json_encode([
            $objs,
        ]);
    }

    private function productsLifeSearch()
    {
        global $db, $catalogue;
        $val = securityForm($_POST['val']);
        $sql = "SELECT `Message_ID` as id, `name`, `art`
                FROM `Message2001`
                WHERE `Catalogue_ID` = {$catalogue}
                    AND `Checked` = 1
                    AND (`name` LIKE '%{$val}%' OR `art` LIKE '%{$val}%' OR art2 LIKE '%{$val}%' OR `artnull` LIKE '%{$val}%' OR `tags` LIKE '%{$val}%')
                    GROUP BY `name` LIMIT 6";

        $items = ["name" => "Товары", "items" => [], "datatype" => "items"];
        $subs = ["name" => "Разделы", "items" => [], "datatype" => "subs"];
        if (!empty($val)) {
            $itemsArray = $db->get_results($sql, "ARRAY_A");
            if (!empty($itemsArray))
                foreach ($itemsArray as $item) {
                    $itemObj = Class2001::getItemById($item['id']);
                    $items['items'][$item['id']]['name'] = $item['name'];
                    $items['items'][$item['id']]['art'] = $item['art'];
                    $items['items'][$item['id']]['photo'] = $itemObj->photoMain;
                }
        }

        return json_encode(
            array(
                "0" => $items,
                "1" => $subs,
            )
        );
    }

    # Добавление в сравнение
    private function searchlife()
    {
        if (function_exists('userfunction_searchlife')) {
            return userfunction_searchlife($this); // своя функция
        } else {
            global $setting, $catalogue, $db, $current_sub, $AUTH_USER_ID;

            // сайты, где живой поиск в текущем разделе
            if ($setting['searchCurrentSub'] && $_POST['r']) {
                $where = "AND Subdivision_ID IN (" . getallparentsub($_POST['r']) . ")";
            }

            $val = securityForm($_POST['val']);
            $items = array();
            $subs = array();

            if ($val) {
                $itemArray = $db->get_results(
                    "SELECT 
                        Message_ID as id, 
                        name, 
                        art 
                    FROM 
                        Message2001 
                    WHERE 
                        Catalogue_ID = {$catalogue} 
                        AND Checked = 1 
                        AND (
                            name like '%{$val}%' 
                            OR art like '%{$val}%' 
                            OR art2 like '%{$val}%' 
                            OR artnull like '%{$val}%' 
                            OR tags like '%{$val}%'
                        ) 
                        {$where} 
                    GROUP BY 
                        name 
                    LIMIT 6",
                    ARRAY_A
                );
                $subArrayAll = $db->get_results("SELECT Subdivision_ID FROM Subdivision WHERE Checked = 1 AND Hidden_URL LIKE '/catalog/%'", ARRAY_A);
                $func = function ($val) {
                    return $val['Subdivision_ID'];
                };
                $subChacked = array_map($func, $subArrayAll);
                $items['name'] = 'Товары';
                $items['items'] = array();
                $items['datatype'] = 'items';
                if ($itemArray)
                    foreach ($itemArray as $item) {
                        $itemObject = Class2001::getItemById($item['id']);
                        if (in_array($itemObject->Subdivision_ID, $subChacked)) {
                            $items['items'][$item['id']]['name'] = $item['name'];
                            $items['items'][$item['id']]['art'] = $item['art'];
                            $items['items'][$item['id']]['url'] = $itemObject->fullLink;
                            $items['items'][$item['id']]['photo'] = $itemObject->photoMain;
                            $items['items'][$item['id']]['price'] = $itemObject->price;
                            $items['items'][$item['id']]['priceHtml'] = $itemObject->priceHtml;
                        }
                    }

                if (!$_POST['r']) {
                    $sql = "SELECT sub.`Subdivision_Name` AS name, sub.`Hidden_URL` AS url";
                    $sql .= ", sub.`Subdivision_ID` AS id";
                    $sql .= " FROM `Subdivision` AS sub";
                    $sql .= " INNER JOIN `Sub_Class` AS cc ON sub.`Subdivision_ID` = cc.`Subdivision_ID`";
                    $sql .= " WHERE sub.`Catalogue_ID` = {$catalogue}";
                    $sql .= " AND sub.`Checked` = 1 AND cc.`Class_ID` = 2001";
                    $sql .= " AND Subdivision_Name LIKE '%{$val}%'";
                    $sql .= " LIMIT 6";

                    $subArray = $db->get_results($sql, ARRAY_A);
                    $subs['name'] = 'Разделы';
                    $subs['items'] = array();
                    $subs['datatype'] = 'subs';
                    if ($subArray)
                        foreach ($subArray as $sub) {
                            $subs['items'][$sub['id']]['name'] = $sub['name'];
                            $subs['items'][$sub['id']]['url'] = $sub['url'];
                        }
                } else {
                    $subs['name'] = 'Разделы';
                    $subs['items'] = array();
                    $subs['datatype'] = 'subs';
                }
            }
            return json_encode(
                array(
                    "0" => $items,
                    "1" => $subs
                )
            );
        }
    }

    # Статус заказа онлайн оплаты
    private function checkOrderStatus($type)
    {
        global $setting, $catalogue, $db, $currency, $AUTH_USER_ID;
        // var_dump($catalogue);
        $status = false;
        switch (true) {
            case $type == 'sberbank' && $setting['sberLogin'] && $setting['sberPass']: # Сбербанк
                $orderId = securityForm($_GET['orderId']);
                $domainSber = (stristr($setting['sberLogin'], $setting['sberPass']) ? "3dsec.sberbank.ru" : "securepayments.sberbank.ru");

                if ($orderId) {
                    $url = "https://{$domainSber}/payment/rest/getOrderStatus.do?orderId={$orderId}&language=ru&password={$setting['sberPass']}&userName={$setting['sberLogin']}";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    if ($response) {
                        $response = json_decode($response, true);
                        if ($response['ErrorMessage']) {
                            if ($response['OrderStatus'] == 2) {
                                $status = true;
                            }
                        }
                    }
                }
                break;
            case $type == 'yookassa' && $setting['yaScid'] && $setting['yaShopId']: # Яндекс касса
                return true;
                break;
        }

        # Заказ оплачен
        if ($status) {
            $this->updateOrder($orderId);
            
            if(getIP('office')){
                var_dump($status);var_dump($orderId);
                echo'1';
            }
  
        } else { # если старый не сработал, пробуем новый скрипт
            if ($type == 'sberbank' && $setting['sberLogin'] && $setting['sberPass']) {
                $orderId = securityForm($_GET['orderId']);
                if (is_numeric($orderId))
                    $orderId = $db->get_var("SELECT orderId FROM Message2005 WHERE Message_ID = '$orderId'");
                if ($orderId) {
                    $url = "https://{$domainSber}/payment/rest/getOrderStatus.do?orderId={$orderId}&language=ru&password=" . urlencode($setting['sberPass']) . "&userName=" . urlencode($setting['sberLogin']);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    if ($response) {
                        $response = json_decode($response, true);
                        if ($response['ErrorMessage']) {
                            if ($response['OrderStatus'] == 2) {
                                $status = true;
                            }
                        }
                    }
                }
            }
            if ($status)
                $this->updateOrder($orderId);
         
        }
        # Перекинуть на главную
        header("Location: //{$_SERVER[HTTP_HOST]}/");
    }

    function updateOrder($orderId)
    {
        global $db, $catalogue, $setting, $AUTH_USER_ID, $currency, $nc_core;

        if (!$catalogue) {
            $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
            $catalogue = $current_catalogue['Catalogue_ID'];
        }
        $db->query("UPDATE Message2005 SET statusOplaty = '2' WHERE Catalogue_ID = {$catalogue} AND orderId = '{$orderId}'");

        #отправка письма с подтвержеднием оплаты


        // if(getIP('office')){
        //     var_dump($catalogue, $_SERVER['HTTP_HOST']);
        //     exit;
        // }
        
        $order = $db->get_row("SELECT * FROM Message2005 WHERE Catalogue_ID = {$catalogue} AND orderId = '{$orderId}'", ARRAY_A);
        $buyer = json_decode($order['customf'], true)['email']['value'];
        $id = $order['Message_ID'];

        #тело письма
        $orderlist = orderArray($order['orderlist'], 1);
        $body_message = "<p><b>Здравствуйте! По заказу №{$id} была произведена оплата!</b></p>
        <p><b>Дата и время: " . date("d.m.Y H:i") . "</b></p>
        <p><b>Содержимое заказа №{$id}:</b></p>
        <table border=1 cellpadding='2' cellspacing='0'>
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Артикул</th>
                    <th>Цена, {$currency[html]}</th>
                    <th>Кол-во</th>
                    <th>Сумма, {$currency[html]}</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($orderlist[items] as $itemID) {
            $body_message .= "<tr>
                    <td>{$itemID[name]}</td>
                    <td>{$itemID[art]}</td>
                    <td>{$itemID[price]}</td>
                    <td>{$itemID[count]}</td>
                    <td>{$itemID[sum]}</td>
                </tr>";
        }
        $body_message .= "</tbody>
            <tfoot>
                <tr>
                    <td colspan=4><b>Итого:</b></td>
                    <td>{$orderlist[totalsum]}</td>
                </tr>
            <tr><td colspan=4><b>Доставка {$orderlist[delivery][name]}:</b></td><td>{$orderlist[delivery][sum]}</td></tr>
            " . ($orderlist[delivery][sum] > 0 ? "<tr><td colspan=4><b>Итого с доставкой:</b></td><td>" . $orderlist[totaldelsum] . "</td></tr>" : "") . "
            </tfoot>
        </table>";

        if ($setting['email'])
            $mails[] = $setting['email'];
        if ($buyer)
            $mails[] = $buyer;
        if ($mails) {
            $frommail = "info@" . str_replace("www.", "", $_SERVER[HTTP_HOST]);
            $mailer = new CMIMEMail();
            // $mailer->mailbody("Здравствуйте! По заказу №{$id} была произведена оплата!");
            $mailer->mailbody(strip_tags($body_message), $body_message);

            foreach ($mails as $mail) {
                $mailer->send($mail, $frommail, $frommail, "Заказ № {$id} оплачен", $current_catalogue[Catalogue_Name]);
            }
        }
    }

    private function edostCalcDelivery()
    {
        global $db, $setting, $AUTH_USER_ID;
        unset($_SESSION['cart']['delivery']['assist']);
        $itemIds = "";
        foreach ($_SESSION['cart']['items'] as $item) {
            $itemIds .= ($itemIds ? "," : "") . $item['id'];
        }
        $itemsData = $db->get_results("SELECT ves,length,width,height FROM Message2001 WHERE Message_ID in ({$itemIds})", ARRAY_A);

        $width = $height = $length = $weight = 0;
        $defaultParams = explode(';', $setting['edostDefaultParams']);
        foreach ($itemsData as $item) {
            $sitesArr = array(815);
            # сайты,где вес в граммах
            if (in_array($catalogue, $sitesArr)) {
                $item['ves'] = $item['ves'] / 1000;
            }
            $weight += (float) $item['ves'] ? (float) $item['ves'] : $defaultParams[0];
            $length += (float) $item['length'] ? (float) $item['length'] : ($defaultParams[1] ? $defaultParams[1] : 0);
            $width += (float) $item['width'] ? (float) $item['width'] : ($defaultParams[2] ? $defaultParams[2] : 0);
            $height += (float) $item['height'] ? (float) $item['height'] : ($defaultParams[3] ? $defaultParams[3] : 0);
        }

        $_POST['edost_weight'] = $weight;
        $_POST['edost_strah'] = $_SESSION['cart']['totalsum'];
        if ($width)
            $_POST['edost_width'] = $width;
        if ($length)
            $_POST['edost_lenght'] = $length;
        if ($height)
            $_POST['edost_height'] = $height;

        $edost = new edost_class($setting['edostShopId'], $setting['edostPassword']);

        $edostCalcResult = $edost->edost_calc_post();

        $st = '';
        if ($edostCalcResult['qty_company'] == 0) {
            switch ($edostCalcResult['stat']) {
                # коды ошибок из главного запроса на сервер edost
                case 2:
                    $st = "Доступ к расчету заблокирован";
                    break;
                case 3:
                    $st = "Не верные данные магазина (пароль или идентификатор)";
                    break;
                case 4:
                    $st = "Не верные входные параметры";
                    break;
                case 5:
                    $st = "Не верный город или страна";
                    break;
                case 6:
                    $st = "Внутренняя ошибка сервера расчетов";
                    break;
                case 7:
                    $st = "Не заданы компании доставки в настройках магазина";
                    break;
                case 8:
                    $st = "Сервер расчета не отвечает";
                    break;
                case 9:
                    $st = "Превышен лимит расчетов за день";
                    break;
                case 11:
                    $st = "Не указан вес";
                    break;
                case 12:
                    $st = "Не заданы данные магазина (пароль или идентификатор)";
                    break;
                case 14:
                    $st = "Настройки сервера не позволяют отправить запрос на расчет";
                    break;
                case 15:
                    $st = "Не верный город отправки";
                    break;
                case 16:
                    $st = "Ваш тарифный план не поддерживает возможность изменения города отправки";
                    break;
                # коды ошибок из класса edost_class
                case 10:
                    $st = "Не верный формат XML";
                    break;
                default:
                    $st = "В данный город автоматический расчет доставки не осуществляется";
            }
            $result = array('success' => false, 'error' => $st);
        } else {
            $edostStruct = array();
            for ($i = 1; $i <= $edostCalcResult['qty_company']; $i++) {
                $edostStruct[$edostCalcResult['id' . $i]] = array(
                    'company' => $edostCalcResult['company' . $i],
                    'name' => $edostCalcResult['name' . $i],
                    'price' => ($edostCalcResult['price' . $i] > $edostCalcResult['pricecash' . $i] ? $edostCalcResult['price' . $i] : $edostCalcResult['pricecash' . $i]) + $edostCalcResult['transfer' . $i],
                    'days' => $edostCalcResult['day' . $i],
                    'transfer' => $edostCalcResult['transfer' . $i] ? true : false,
                    'description' => "<b>{$edostCalcResult['company' . $i]}</b>:" . ($edostCalcResult['transfer' . $i] ? " <b>Наложенный платеж</b>" : "") . " {$edostCalcResult['name' . $i]}"
                );
            }
            $_SESSION['cart']['delivery']['edost']['list'] = $edostStruct;
            $result = array('success' => true, 'result' => $edostStruct);
        }

        return json_encode($result);
    }

    # курс валют
    private function cbr()
    {
        require_once '/var/www/krza/data/www/krza.ru/bc/modules/default/include_console/include_console.php';
        global $DOCUMENT_ROOT;
        $datt = date("d/m/Y");
        $cod = 'R01235';
        $cod2 = 'R01239';
        $cod3 = 'R01335';

        $filen = @file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=" . $datt);
        preg_match("#<Valute ID=\"" . $cod . "\".*?>(.*?)</Valute>#is", $filen, $nm);
        preg_match("#<Value>(.*?)</Value>#is", $nm[1], $nr);

        preg_match("#<Valute ID=\"" . $cod2 . "\".*?>(.*?)</Valute>#is", $filen, $nm2);
        preg_match("#<Value>(.*?)</Value>#is", $nm2[1], $nr2);

        preg_match("#<Valute ID=\"" . $cod3 . "\".*?>(.*?)</Valute>#is", $filen, $nm3);
        preg_match("#<Value>(.*?)</Value>#is", $nm3[1], $nr3);


        $kurs[update] = str_replace("/", ".", $datt);
        $kurs[dollar] = str_replace(",", ".", $nr[1]);
        $kurs[euro] = str_replace(",", ".", $nr2[1]);
        $kurs[tenge] = str_replace(",", ".", $nr3[1]);

        if (file_put_contents($DOCUMENT_ROOT . "/currency.ini", json_encode($kurs)))
            echo "ok";
        else
            echo "NOT ok";
    }

    #возвращает оригинальное имя города
    private function getOrigCityName($name)
    {
        global $setting, $cityvars;

        foreach ($cityvars as $key => $val) {
            if ($val['name'] == $name) {
                $k = $key;
                break;
            }
        }
        return $setting['lists_targetcity'][$k]['name'];
    }

    #бэкап
    private function backupDisk()
    { // /bc/modules/default/index.php?user_action=backup

        global $setting, $cityvars, $DOCUMENT_ROOT, $current_catalogue, $pathInc, $catalogue;

        $backuppath = $DOCUMENT_ROOT . '/backups/' . $catalogue . '_' . $current_catalogue[login] . '.zip';
        $ignorepath = $DOCUMENT_ROOT . $pathInc . '/1C/';

        @unlink($backuppath);
        if (zipPath($DOCUMENT_ROOT . $pathInc . '/', $backuppath, $ignorepath)) {
            echo 'Бэкап создан';
            flush();
            ob_flush();
            echo sendYandexDisk($backuppath);
        } else {
            echo 'Ошибка создания бэкапа';
        }
    }

    # закачать архив на диск
    private function yaDisk()
    { // /bc/modules/default/index.php?user_action=yadisk

        global $setting, $cityvars, $DOCUMENT_ROOT, $current_catalogue, $pathInc, $catalogue;

        $backuppath = $DOCUMENT_ROOT . '/backups/' . $catalogue . '_' . $current_catalogue[login] . '.zip';

        if (file_exists($backuppath)) {
            echo sendYandexDisk($backuppath);
        } else {
            echo 'файла нет';
        }
    }



    # избранное
    private function favChange()
    {
        global $db, $current_user, $AUTH_USER_ID;

        if (is_numeric($_GET['item_id'])) {
            if (!$current_user) {
                $type = 'session';

                if (isset($_SESSION['favarits'][$_GET['item_id']]))
                    unset($_SESSION['favarits'][$_GET['item_id']]);
                else
                    $_SESSION['favarits'][$_GET['item_id']] = $_GET['item_id'];

                $count = count($_SESSION['favarits']);
            } else {
                $type = 'saveInUser';
                $userFavs = $current_user['favarits'];
                $item_id = $_GET['item_id'];
                $idList = mb_strstr($userFavs, ";{$item_id};") ? str_replace(";{$item_id};", ";", $userFavs) : $userFavs . $item_id . ";";
                $db->query("UPDATE User SET favarits = '" . ($userFavs ? $idList : ";{$item_id};") . "' WHERE User_ID = {$current_user['User_ID']}");
                $favArr = $idList ? explode(';', trim($idList)) : trim($item_id);
                $count = count($favArr) - 2;
            }
            $result = array('status' => 'ok', 'count' => $count, 'type' => $type);
        } else {
            $result = array('status' => 'error');
        }

        return json_encode($result);
    }

    private function filter()
    {
        global $current_user;

        $params = array(
            'bitcat' => isset($current_user) && $current_user['PermissionGroup_ID'] == 1 && ($current_user['Catalogue_ID'] == $this->catID || !$current_user['Catalogue_ID'])
        );

        if (is_array($_REQUEST)) {
            $keyToSub = array(
                'thissub2' => 1,
                'EnglishName2' => 1,
                'thiscid2' => 1
            );
            $keyMap = array(
                'thissub2' => 'id',
                'EnglishName2' => 'engName',
                'thiscid2' => 'classID',
                'searchincur2' => 'searchInCur'
            );
            foreach ($_REQUEST as $key => $value) {
                $inSub = isset($keyToSub[$key]) || substr($key, 0, 4) == 'sub_';
                if (isset($keyMap[$key]))
                    $key = $keyMap[$key];

                if ($inSub) {
                    $params['sub'][$key] = $value;
                } else {
                    $params[$key] = $value;
                }
            }
        }

        $filter = new Class2041($_REQUEST['id'], $this->catID, $params);
        switch ($_REQUEST['method']) {
            case 'getfilter':
                echo $filter->getFilter($_REQUEST['type']);
                break;
            case 'getcount':
                echo $filter->getItemCount();
                break;
        }
        if ($_REQUEST['viewtime'])
            $filter->writeTime();
    }

    private function cdek()
    {
        global $currency,$setting;

        $cdek = Cdek::getInstance();

        switch ($_GET['method']) {
            case 'choose_post':
                return json_encode($cdek->getPvzData());
            case 'get_deivery_price':
                $products = [];
                foreach ($_SESSION['cart']['items'] ?? [] as $item) {
                    $product = ['count' => $item['count']];

                    if ($productObj = Class2001::getItemById($item['id'])) {
                        if ($productObj->ves)
                            $product['weight'] = $productObj->ves;
                        if ($productObj->length)
                            $product['length'] = $productObj->length;
                        if ($productObj->width)
                            $product['width'] = $productObj->width;
                        if ($productObj->height)
                            $product['height'] = $productObj->height;
                    }

                    $products[] = $product;
                }

                $tariffList = $cdek->calculateTariffList((new CdekCalculatorData())->setDeliveryCityCode($_GET['city_code'])->setProducts($products));

                $result = [];
                foreach ($tariffList as $tariff) {
                    $result[$cdek->getTariffType($tariff['tariff_code'], 'type')][] = [
                        'price' => $tariff['delivery_sum'],
                        'period' => $tariff['calendar_min'] == $tariff['calendar_max'] ? $tariff['calendar_min'] : $tariff['calendar_min'] . '-' . $tariff['calendar_max'],
                        'pricehtml' => price($tariff['delivery_sum']) . ' ' . $currency['html'],
                        'id' => $tariff['tariff_code'],
                        'pointType' => $cdek->getTariffType($tariff['tariff_code'], 'pointType'),
                    ];
                }
                return json_encode($result);
            case 'choose':
                $parameters = [
                    'tariffid' => $_GET['tariffid'],
                    'cityCode' => $_GET['cityCode'],
                ];

                if (!empty($_GET['pvzcode']))
                    $parameters['pvzCode'] = $_GET['pvzcode'];

                (new Class2005())->setServiceDelivery('cdek', $parameters);

                $result = [
                    'success' => !isset($_SESSION['cart']['delivery']['assist']['error']),
                    'tariffType' => $cdek->getTariffType($_GET['tariffid'], 'type'),
                    'deliveryType' => !empty($_GET['pvzcode']) ? 1 : 2,
                    'deliversum' => $_SESSION['cart']['delivery']['sum_result'],
                    'totdelsum' => $_SESSION['cart']['totaldelsum'],
                    'address' => $_SESSION['cart']['delivery']['assist']['description'],
                    'all' => $_SESSION['cart'],
                ];

                if ($_SESSION['cart']['delivery']['sum_pay_after']) {
                    $result['delivery_sum_pay_after'] = $_SESSION['cart']['delivery']['sum_pay_after'];
                }

                return json_encode($result);
        }
    }

    public function pochtaRussia()
    {
        $PR = new PochtaRussia();
        switch ($_GET['method']) {
            case 'select_chooser':
                $res = $PR->selectChooser(
                    [
                        'success' => true,
                        'description' => $_GET['address'],
                        'price' => $_GET['price'],
                        'mailType' => $_GET['mailType'],
                        'pvzType' => $_GET['pvzType'],
                        'pvzCode' => $_GET['pvzCode'],
                    ]
                );
                break;
            case 'reCall':
                $res = $PR->recalcDelivery();
                break;
            case 'getParam':
                $res = $PR->getParamItems();
                break;

        }
        return json_encode($res);
    }

    private function getOrgByInn($inn)
    {
        global $setting;

        try {
            $OrganizationByINN = App\modules\Korzilla\DaData\Factory::create('OrganizationByINN', $setting['dadata_token']);
            $result = $OrganizationByINN->get($inn)['result'];
            if ($setting['dadata_okved']) {
                $Okved = App\modules\Korzilla\DaData\Factory::create('Okved', $setting['dadata_token']);
                foreach ($result['suggestions'] ?: [] as $index => $suggestion) {
                    $okvedCode = $suggestion['data']['okved'];
                    if ($okvedCode) {
                        $okvedResult = $Okved->get($okvedCode)['result'];
                        if ($okvedResult['suggestions'][0]['value']) {
                            $result['suggestions'][$index]['data']['okved'] = $okvedResult['suggestions'][0]['value'] . "({$okvedCode})";
                        }
                    }
                }
            }
            
            return json_encode($result);
        } catch (\Exception $e) {
            return json_encode([$e->getMessage()]);
        }
    }

    private function deleteCurrentUser()
    {
        global $AUTH_USER_ID;

        $this->authUser();

        if (!$AUTH_USER_ID) {
            return json_encode([
                'succes' => 'true',
                'submodal' => 1,
                'confirmtext' => '',
                'title' => 'Не удалось найти пользователя',
            ]);
        }

        if (!deleteUserById($AUTH_USER_ID)) {
            return json_encode([
                'succes' => 'true',
                'submodal' => 1,
                'confirmtext' => '',
                'title' => 'Не удалось удалить пользователя',
            ]);
        }

        return json_encode([
            'succes' => 'true',
            'submodal' => 1,
            'confirmtext' => '',
            'title' => 'Пользователь удален',
            'redirect' => '/',
        ]);
    }

}