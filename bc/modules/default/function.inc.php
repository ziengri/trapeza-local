<?php
global $time_days, $time_days_en, $AUTH_USER_ID, $MODULE_FOLDER, $DOCUMENT_ROOT, $setting, $db, $bitcat, $arrayDom, $pathDom, $pathInc, $pathInc2, $catalogue, $HTTP_FILES_PATH, $s_label, $setting_params, $setting_texts, $setting_delivery, $cityvars, $city_link_a, $cityname, $citymain, $citymainkey, $setting_payment, $currency, $widgetArr, $login, $current_catalogue, $current_user, $mobileMenu, $authInBlock, $noimage, $plugins, $domainexp, $mainpage, $langs, $redirect_url, $isPgSpeed, $office;

include_once($DOCUMENT_ROOT . '/autoload.php');
include_once($MODULE_FOLDER . "default/kz_browse.inc.php");
include_once($MODULE_FOLDER . "default/fields.inc.php"); # standart fields
include_once($MODULE_FOLDER . "default/zoneblocks.inc.php"); # zone & blocks
// include_once($MODULE_FOLDER."default/user_function.inc.php");

if ($_GET['debugsql'] && $_SERVER['REMOTE_ADDR'] == '31.13.133.138') {
    $nc_core->db->debug_all = true;
}
if (getIP('office')) {
    // ini_set('error_reporting', E_ALL);
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
}
# user function
$thisFile = $DOCUMENT_ROOT . $pathInc2 . "/user_function.php";
if ($current_catalogue['customCode'] && file_exists($thisFile)) {
    include_once($thisFile);
} else {
    include_once($MODULE_FOLDER . "default/user_function.inc.php");
}
# excel
$thisFile = $DOCUMENT_ROOT . $pathInc2 . "/export_xls.php";

if ($current_catalogue['customCode'] && file_exists($thisFile)) {
    include($thisFile);
} else {
    include_once($MODULE_FOLDER . "default/export_xls.php");
}

include_once($MODULE_FOLDER . "bitcat/function.inc.php"); # bitcat

if ($current_catalogue['customCode']) {
    foreach (array("2001", "2005", "2012", "2021", "2041") as $id) {
        $thisFile = $DOCUMENT_ROOT . $pathInc2 . "/class/class{$id}.php";
        if (file_exists($thisFile)) include($thisFile);
    }
}
include_once($MODULE_FOLDER . "default/class244.inc.php"); # Преимущества
include_once($MODULE_FOLDER . "default/class2001.inc.php"); # Каталог
include_once($MODULE_FOLDER . "default/class2003.inc.php"); # Новости
include_once($MODULE_FOLDER . "default/class2005.inc.php"); # Корзина
include_once($MODULE_FOLDER . "default/class2012.inc.php"); # Контакты
include_once($MODULE_FOLDER . "default/class2021.inc.php"); # Портфолио
include_once($MODULE_FOLDER . "default/class2041.inc.php"); # Фильтр
include_once($MODULE_FOLDER . "default/class2073.inc.php"); # Генирируемый компонент
include_once($MODULE_FOLDER . "default/class2088.inc.php"); # Дилерская сеть
include_once($MODULE_FOLDER . "default/class2245.inc.php"); # Меню мобильного приложения
include_once($MODULE_FOLDER . "default/class2253.inc.php"); # Портфолио SEO
include_once($MODULE_FOLDER . "default/class2260.inc.php"); # Преимущества
include_once($MODULE_FOLDER . "default/delivery_classes/edost_class.php"); #eDost класс
include_once($MODULE_FOLDER . "default/delivery_classes/cdek_class.php"); #eDost класс
include_once($MODULE_FOLDER . "default/delivery_classes/pochtaRussia/PochtaRussia.class.php"); #PochtaRussia класс
include_once($MODULE_FOLDER . "default/korzilla/replacer.php"); # замена ключевых слов
include_once($MODULE_FOLDER . "default/korzilla/morpher.php"); # склнятор
include_once($MODULE_FOLDER . "default/vendor/autoload.php"); # подключение классов из composer
include_once($MODULE_FOLDER . "telegram/autoload.php"); # подключение telegram
include_once($MODULE_FOLDER . "default/PaymentMethod/Cart/cart.class.php"); # шабланизатор писма корзины;
include_once($MODULE_FOLDER . "default/mailAsisit.class.php"); # отправка писем через смтп или mailer

include_once($MODULE_FOLDER . "default/amocrm/AmoCrmFunction.php"); #AmoCrm


if (file_exists($DOCUMENT_ROOT . $pathInc2 . "/function.php")) {
    include($DOCUMENT_ROOT . $pathInc2 . "/function.php");
} # Свои функции


// Проверка принимаемых параметров
$req = array('warnText', 'query_where', 'query_select', 'query_order', 'query_from', 'query_group', 'query_limit', 'includeSettings', 'thisFile', 'politika', 'bigcart', 'bitcat', 'superdamin', 'seo', 'design', 'filtersql', 'html404');
foreach ($req as $r) {
    if (isset($_POST[$r]) || isset($_GET[$r])) {
        if (currentUrl()) header('Location: ' . currentUrl());
        else header('Location: //' . $_SERVER['HTTP_HOST']);
        die;
    }
}
if (isset($_GET['recNum']) && !is_numeric($_GET['recNum'])) {
    header("HTTP/1.0 404 Not Found");
    die;
}
if ($_GET['recNum'] > 1000 || $_GET['curPos'] > 10000) {
    header("HTTP/1.0 404 Not Found");
    die;
}
if ($_GET['PAGEN_1'] || stristr($_SERVER['REQUEST_URI'], 'select*from') || stristr($_SERVER['REQUEST_URI'], 'tel:') || stristr($_SERVER['REQUEST_URI'], 'mailto:')) {
    header("HTTP/1.0 404 Not Found");
    die;
}

$redirect_url = explode("?", $_SERVER['REQUEST_URI']);
$redirect_url = $redirect_url[0];

# если это мобильное приложение
$deviceID = ($_GET['device_id'] ? $_GET['device_id'] : $_GET['deviceId']);
if ($deviceID) {
	$_SESSION['deviceID'] = $deviceID;
	setcookie("deviceID", $deviceID, [
		'expires' => time()+3600*24*365,
		'path' => '/',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => true,
		'httponly' => false,
		'samesite' => 'None',
	]);
}
if ($deviceID == 'none') {
	unset($_SESSION['deviceID']);
	setcookie("deviceID", "", [
		'expires' => time()-3600*24*365,
		'path' => '/',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => true,
		'httponly' => false,
		'samesite' => 'None',
	]);
}


if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse')) $isPgSpeed = 1;


// Доступ сайта только в фоисе
if ($current_catalogue['onlyInOffise'] && !permission('office')) {
    header('Location: //korzilla.ru');
    die;
}



function compress_js($file)
{
    $fileArr = explode('/', str_replace(".js", "", $file));
    $newfile = $fileArr[0] . '/' . $fileArr[1] . '_min.js';

    $buffer = @file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $file);

    $buffer = preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
        $buffer
    );

    $buffer = str_replace(array("\t", "  ", "    ", "    "), "", $buffer);

    if ($buffer) file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $newfile, $buffer);
}


function normalizePriceSber($price)
{
    if (!is_numeric($price)) {
        $price = str_replace(',', '.', $price);
    }
    if (!is_numeric($price)) {
        return 0;
    }
    return round($price, 2) * 100;
}


if (is_array($login) && in_array($login['login'], array('sites', 'preview'))) {
} else {
    construct();
}

function construct()
{
    global $catalogue, $db, $AUTH_USER_ID, $setting, $widgetArr, $s_label, $setting_texts, $setting_params, $setting_delivery, $cityvars, $citymain, $citymainkey, $citymainid, $city_link_a, $setting_payment, $currency, $noimage, $cityname, $domainexp, $mainpage, $current_sub, $plugins, $kurs, $DOCUMENT_ROOT, $langs, $citys;

    if (isset($_GET['warnText']) || isset($_POST['warnText'])) {
        header('Location: ' . currentUrl());
        die;
    }

    # получить настройки сайта
    $setting = getSettings();
    $reload_sett = 0;

    if ($setting && !is_numeric($setting)) {
        $settingDB = $db->get_results("select * from Bitcat", 'ARRAY_A');
        $noSetField = array("sitename", "meta", "robot");
        if ($settingDB) {
            foreach ($settingDB as $key => $set) {
                if (!isset($setting[$set['key']]) && !array_key_exists($set['key'], $setting) && !in_array($set['key'], $noSetField)) {
                    $setting[$set['key']] = isJson($set['value']) && !stristr($set['key'], "form") ? orderArray($set['value']) : ($set['value'] ? $set['value'] : "");
                    $reload_sett = 1;
                    notification(20, $_SERVER['HTTP_HOST'], "Not have field in settings file \"{$set['key']}\"");
                }
            }
        }
    } else {
        echo "Файл settings.ini не корректный";
        exit;
    }
    // сохранить json и css
    if ($reload_sett && setSettings($setting)) {
        $bc = new bc();
        $bc->savecss();
    }


    $s_label = array_combine(range(1, count($setting['lists_itemlabel'])), $setting['lists_itemlabel']);
    # ключевые слова
    $setting_texts = $setting['lists_texts'];
    # доставка
    $setting_delivery = $setting['lists_delivery'];
    # доставка
    $setting_langs = $setting['lists_language'];
    # Параметры товара
    $setting_params = $setting['lists_params'];

    # список городов
    $cityvars = $setting['lists_targetcity'];


    foreach ($cityvars as $key => $city) {
        # включенные города
        if (isset($city['checked'])) {
            $citys[$city['keyword']] = $city['name'];
        } else {
            unset($cityvars[$key]);
        }
        # основной город
        if (isset($city['main'])) {
            $citymain = getLangWord($city['keyword'], $city['name']);
            $citymainkey = $city['keyword'];
            $citymainid = $key;
            unset($cityvars[$key]['main']);
        }
    }
    # способы оплат
    $setting_payment = $setting['lists_payment'];

    # языки
    $langs = array(); # lang, main, langnow

    if ($setting['language']) {
        foreach ($setting['lists_language'] as $lng) {
            if ($lng['checked']) {
                # формируем массив со включенными языками
                $langs['lang'][$lng['keyword']]['keyword'] = $lng['keyword'];
                $langs['lang'][$lng['keyword']]['name'] = $lng['name'];
                # основной язык
                if (!$langs['main']) {
                    $langs['main']['keyword'] = $lng['keyword'];
                    $langs['main']['name'] = $lng['name'];
                }
            }
        }
        # получить язык сайта
        if ($langs) {
            # название уровеня поддомена поддомена
            $lang = explode('.', $_SERVER['HTTP_HOST']);
            $lang = $lang[0];
            # язык на данный момент
            $langs['langnow'] = isset($langs['lang'][$lang]) && $langs['lang'][$lang] ? $lang : $langs['main']['keyword'];
        }
        # получить ключевые слова
        $langs['keys'] = getKeywordLang();
        // if($catalogue==453) print_r($langs['keys']);
    }

    # currency
    if (!isset($_COOKIE['curTypeShow'])) $_COOKIE['curTypeShow'] = 'rub';
    switch ($_COOKIE['curTypeShow']) {
        case 'usd':
            $simbol_char = getLangWord('usd_char', 'usd');
            break;
        case 'euro':
            $simbol_char = getLangWord('euro_char', 'евро');
            break;

        case 'tenge':
            $simbol_char = getLangWord('kzt_char', 'KZT');
            break;
        default:
            $simbol_char = getLangWord('rubl_char', 'Р');
            break;
    }
    $currency = array();
    $currency['symbol'] = $simbol_char;
    $currency['html'] = "<span class='" . ($currency['symbol'] && $currency['symbol'] == "Р" ? "rubl" : "currency") . "'>{$currency['symbol']}</span>";

    # валюта
    $settingspath = $DOCUMENT_ROOT . "/currency.ini";
    $settingFile = @file_get_contents($settingspath);
    $kurs = $settingFile ? orderArray($settingFile) : array();

    # url noimage
    function getnoimage($param = "")
    {
        global $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $current_catalogue, $setting;
        $v_img = $current_catalogue['colorid'] ? "?c={$current_catalogue['colorid']}" : "";
        $defaultImage = $param == "big" ? "{$IMG_HOST}/images/nophotoBig.png" : "/images/nophoto.png";
        if ($setting['nophoto']) {
            $userImagePng = $pathInc . "/files/nophoto.png";
            $userImageJpg = $pathInc . "/files/nophoto.jpg";
            return file_exists($DOCUMENT_ROOT . $userImagePng) ? $userImagePng . $v_img : (file_exists($DOCUMENT_ROOT . $userImageJpg) ? $userImageJpg . $v_img : $defaultImage);
        } else {
            return $defaultImage;
        }
    }
    $noimage = getnoimage();



    # главная иль нет
    $domainexp = explode("?", $_SERVER['REQUEST_URI']);
    $mainpage = (!str_replace("/", "", $domainexp[0]) ? 1 : '');

    # WIDGETS
    $mailformlink = "<a class=dotted href='#nk-feedback' rel=pop data-metr='mailtoplink' data-okno='feedback' data-loads='/feedback/add_feedback.html?isNaked=1'>" . ($setting['lists_texts']['link_mail']['checked'] ? $setting['lists_texts']['link_mail']['name'] : "Получить консультацию") . "</a>";
    $callformlink = "<a class=dotted href='#nk-callme' rel=pop data-metr='calltoplink' data-okno='callme' data-loads='/callme/add_callme.html?isNaked=1'>" . ($setting['lists_texts']['link_call']['checked'] ? $setting['lists_texts']['link_call']['name'] : "Обратный звонок") . "</a>";
    $maplink = "<a href='#nk-adresmap' rel=pop data-metr='maptoplink' data-okno='adresmap' data-iframe='" . nc_message_link($contactid->id, "2012") . "?isNaked=1'>" . ($setting['lists_texts']['proezd_map']['checked'] ? $setting['lists_texts']['proezd_map']['name'] : "Схема проезда") . "</a>";

    if (!$widgetArr) {
        $widgetArr = array(
            "_MAILFORMLINK_" => $mailformlink,
            "_CALLFORMLINK_" => $callformlink,
            "_MAPLINK_" => $maplink,
            "_PHONE_" => $contactSet['phonekod'] . " " . $contactSet['phone'],
            "_PHONE2_" => $contactSet['phonekod2'] . " " . $contactSet['phone2']
        );
    }
}


# Мультиязычность: поиск по ключевомк слову
function getLangWord($name, $default = '')
{
    global $setting, $langs, $setting_texts, $catalogue, $AUTH_USER_ID;

    //$setting['lists_texts']
    if ($setting['language']) {
        return $langs['keys'][$name][$langs['langnow']] ? $langs['keys'][$name][$langs['langnow']] : ($default ? $default : $name);
    } else {
        # языки выключены
        if ($catalogue == 730) {
            if (!isset($setting_texts[$name])) {
                $setting['lists_texts'][$name] = ['checked' => 0, 'name' => $default];
                setSettings($setting);
            }
        }
        $text = $setting_texts[$name]['checked'] ? $setting_texts[$name]['name'] : $default;
        return $text ? $text : $name;
    }
}
# Мультиязычность: формулировка массива
function getKeywordLang()
{
    global $setting, $setting_texts, $langs, $catalogue, $AUTH_USER_ID;
    $keys = array();
    # список названий разделов
    if ($setting['lists_language_sub']) {
        foreach ($setting['lists_language_sub'] as $langsub) {
            $keys[$langsub['keyword']] = $langsub;
            unset($keys[$langsub['keyword']]['keyword']);
        }
    }
    # список названий блоков
    if ($setting['lists_language_blk']) {
        foreach ($setting['lists_language_blk'] as $langblk) {
            $keys[$langblk['keyword']] = $langblk;
            unset($keys[$langblk['keyword']]['keyword']);
        }
    }
    # settings
    if ($setting['lists_texts'] && $setting['lists_language_keys']) {
        $i = 1;
        foreach ($setting['lists_texts'] as $key => $langblk) {
            if (!$key) continue;
            if ($langs['langnow'] != $langs['main']['keyword']) {
                $text = $setting['lists_language_keys'][$i][$langs['langnow']];
                $setting['lists_texts'][$key]['name'] = $text;
            }

            $setting_texts[$key]['name'] = $setting['lists_texts'][$key]['name'];;
            $keys[$key][$langs['langnow']] = $setting['lists_texts'][$key]['name'];
            $i++;
        }
    }
    return $keys;
}
# замена констант в тексте
function replace_lang($text)
{
    global $setting;
    if ($setting['language']) {
        return preg_replace_callback("#((lang_|kz_|sub_|blk_)[a-z0-9_-]+)#", function ($f) {
            return getLangWord($f[0]);
        }, $text);
    } else {
        return $text;
    }
}
# Мультиязычность: query_where
function getLangQuery($query_where, $prefix = '')
{
    global $setting, $langs;
    # lang content
    if ($prefix) $prefix .= '.';
    if ($setting['language']) {
        $query_where = ($query_where && $query_where != 1 ? $query_where : "") . ($query_where ? " AND " : NULL) . "({$prefix}lang like '%\"{$langs['langnow']}\"%' OR {$prefix}lang is NULL OR {$prefix}lang = 'null' OR {$prefix}lang = '[]' OR {$prefix}lang = '')";
    }
    return $query_where;
}
# Мультиязычность для разделов: query_where 
function getSubLangQuery($query_where, $tablePref = '')
{
    global $setting, $langs;
    if ($tablePref && substr($tablePref, -1) != '.') $tablePref .= '.';
    # lang content
    if ($setting['language']) {
        $query_where .= ($query_where ? " AND " : NULL) . "({$tablePref}`sub_lang` IS NULL OR {$tablePref}`sub_lang` LIKE '%\"{$langs['langnow']}\"%')";
    }
    return $query_where;
}
# Мультиязычность: выбор языка
function nc_lang_field($langjson = '{}')
{
    global $catalogue, $langs;
    $langonj = orderArray($langjson);
    if (!is_array($langonj)) $langonj = array();
    foreach ($langs['lang'] as $keyword => $lng) {
        $langselect .= "<div class='colline colline-3' {$langjson}>" . bc_checkbox("f_lang[]", $keyword, $lng['name'], (in_array($keyword, $langonj) ? 1 : 0)) . "</div>";
    }
    return $langselect;
}
# Мультиязычность: название города
function getLangCityName($name, $id = '')
{
    global $cityvars, $setting;
    if ($setting['language']) {
        if ($id) {
            return getLangWord('lang_city_' . $cityvars[$id]['keyword'], $name);
        } else {
            foreach ($cityvars as $val) {
                if ($val['name'] == $name) return getLangWord('lang_city_' . $val['keyword'], $name);
            }
        }
    }
    return $name;
}
# лейблы перевод
if ($setting['language']) {
    # лейблы
    $s_label_assist = array();
    foreach ($setting['lists_itemlabel'] as $val) {
        $val['name'] = getLangWord($val['name']);
        $s_label_assist[] = $val;
    }
    $s_label = array_combine(range(1, count($setting['lists_itemlabel'])), $s_label_assist);
}



# ссылка выбор города
function getCityLink($param = array())
{
    global $setting, $cityvars, $cityname;
    $text = $param['title'] ? $param['title'] : ($cityname ? $cityname : getLangWord('select_city', 'Выберите город'));
    return "<a
                href='/bc/modules/default/index.php?user_action=citylist'
                title='" . getLangWord('select_city', 'Выберите город') . "'
                data-rel='lightcase'
                class='targeting-a " . ($setting['targReq'] ? "targReq" : NULL) . "'
                data-maxwidth='" . (count($cityvars) > 10 ? 880 : 300) . "'
                data-groupclass='modal-targeting " . (count($cityvars) > 10 ? "targeting-big" : "targeting-min modal-nopadding") . "'
            >{$text}</a>" . getCityListSEO();
}
# Доступы и разрешения
function permission($name, $only = false)
{
    global $current_catalogue, $setting, $nc_core, $current_user, $AUTH_USER_ID, $db, $office;

    $office = (in_array($_SERVER['REMOTE_ADDR'], ['31.13.133.138', '92.255.204.119']) ? 1 : "");


    // получить параметры сайта
    if (!$current_catalogue) {
        $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    }
    // была ли выгрузка через старый excel
    if ($name == "excel_old") {
        return boolval($db->get_var("SELECT count(*) FROM Message2029 WHERE Catalogue_ID = {$current_catalogue['Catalogue_ID']}"));
    }
    // получить параметры пользователя
    if (!$current_user) {
        $current_user = $nc_core->user->get_by_id($AUTH_USER_ID);
    }
    // каталок
    if ($name == "catalogue") {
        if ($current_catalogue['sitetype_id'] >= 4) return true;
        else return false;
    }
    // мобильное приложения
    if ($name == "mobail_app") {
        if ($setting['company_id']) return true;
        else return false;
    }
    // корзина
    if ($name == "cart") {
        if (!$setting['typeOrder'] && $current_catalogue['sitetype_id'] >= 5) return true;
        else return false;
    }
    // оформить заказ
    if ($name == "order") {
        if ($setting['typeOrder'] != 2 && $current_catalogue['sitetype_id'] >= 4) return true;
        else return false;
    }
    // Цветовая схема
    if ($name == "colorScheme") {
        if ($current_catalogue['colorScheme'] || $setting['colorscheme']) return true;
        else return false;
    }
    // Шрифты
    if ($name == "fontScheme") {
        if ($current_catalogue['fontScheme']) return true;
        else return false;
    }
    // PRO
    if ($name == "PRO") {
        if ($current_catalogue['sitetype_id'] == 6) return true;
        else return false;
    }

    // Зоны и блоки
    if ($name == "zoneAndBlocks") {
        if ($current_catalogue['zoneAndBlocks'] || $current_user['superadmin'] || $current_user['design'] || $setting['powerdesign'] || $office) return true;
        else return false;
    }
    // Доступы только дизайн
    if ($name == "design" && $only) {
        if ($current_user['design']) return true;
        else return false;
    }
    // Дизайн
    if ($name == "design") {
        if ($current_catalogue['design'] || $setting['powerdesign'] || $current_user['superadmin'] || $current_user['design'] || $office) return true;
        else return false;
    }
    // SEO
    if ($name == "seo") {
        if ($current_catalogue['seo'] || $setting['powerseo'] || $current_user['superadmin'] || $current_user['seo']) return true;
        else return false;
    }
    // SEO SUPER
    if ($name == "seo_super") {
        if ($current_catalogue['seo'] || $setting['powerseo_super'] || $current_user['superadmin'] || $current_user['seo']) return true;
        else return false;
    }

    // admin
    if ($name == "admin") {
        if (stristr($current_user['Login'], "admin") || $AUTH_USER_ID == 1 || $current_user['showcontact']) return true;
        else return false;
    }

    // dev
    if ($name == "dev") {
        if ($current_user['seo'] || $current_user['design'] || $current_user['superadmin']) return true;
        else return false;
    }
    // Полный доступ
    if ($name == "superadmin") {
        if ($current_user['superadmin']) return true;
        else return false;
    }
    // MONSTER
    if ($name == "monster") {
        if ($current_user['megamonster']) return true;
        else return false;
    }
    if ($name == "korzilla_admin") {
        $userIDs = [154 => 1];
        return isset($userIDs[$AUTH_USER_ID]);
    }

    // File
    if ($name == "file") {
        if ($current_user['superadmin'] || $current_user['fileManager']) return true;
        else return false;
    }

    if ($name == 'developer') {
        return App\modules\Korzilla\Permission\Controller::isDeveloper($AUTH_USER_ID);
    }

    if ($name == 'seo-developer') {
        return App\modules\Korzilla\Permission\Controller::isSeoDeveloper($AUTH_USER_ID);
    }

    // $office
    if ($name == "office") {
        if ($office) return true;
        else return false;
    }

    return false;
}

# актуальное поле цены для текущей группы пользователя
function politika($t = '', $align = '')
{
    if (function_exists('class2005_politika')) {
        return class2005_politika($t = '', $align = ''); // своя функция
    } else {
        global $db, $catalogue, $setting_texts;
        $br = ($t != 1 ? "<br>" : " ");
        $politika = "";

        $submittingFormYouAgree = $setting_texts['politika_1']['checked'] ? $setting_texts['politika_1']['name'] : "Отправляя форму, вы соглашаетесь";
        $with = $setting_texts['politika_2']['checked'] ? $setting_texts['politika_2']['name'] : "c";
        $privacyPolicy = $setting_texts['politika_3']['checked'] ? $setting_texts['politika_3']['name'] : "политикой конфиденциальности";

        if ($db->get_var("select Subdivision_ID from Subdivision where Hidden_URL = '/system/politika/' AND Catalogue_ID = '$catalogue'")) {
            $politika = "<div class='politika {$align}'>
                            {$submittingFormYouAgree} 
                            {$br}
                            {$with} 
                            <a target=_blank href='/system/politika/'>
                                {$privacyPolicy}
                            </a>
                        </div>";
        }

        return $politika;
    }
}



# актуальное поле цены для текущей группы пользователя
function groupPrice($prices)
{
    global $current_user;
    $perm = ($current_user[PermissionGroup_ID] ? $current_user[PermissionGroup_ID] : 0);
    if ($perm == 2) $perm = 1;
    if ($prices) {
        foreach ($prices as $gr => $pr) {
            if ($perm == $gr && $pr > 0) return $pr;
            $lastpr = $pr;
        }
    }
    return $prices[0];
}


# принудительный перенос текста
function wrap($str, $num = '')
{
    $str1 = wordwrap($str, 15, " ");
    if (md5($str) == md5($str1) && $num) {
        $z = array("/" => "/ ", "." => ". ");
        $str1 = strtr($str, $z);
    }
    return $str1;
}

function vars_str($get, $vars, $amp = '')
{
    global $AUTH_USER_ID;

    foreach (explode(",", str_replace(" ", "", $vars)) as $v) {
        if (!is_array($get[$v])) {
            if ($get[$v]) $vars_str .= ($vars_str || $amp ? "&" : "?") . "{$v}=" . strip_tags($get[$v]);
        } else {
            foreach ($get[$v] as $kk => $vv) {
                if (!is_array($get[$v][$kk])) {
                    if ($get[$v][$kk]) $vars_str .= ($vars_str || $amp ? "&" : "?") . "{$v}[{$kk}]=" . strip_tags($get[$v][$kk]);
                } else {

                    foreach ($get[$v][$kk] as $kkk => $vvv) {
                        if (is_array($vvv)) {
                            if ($vvv[0] && !$vvv[1]) {
                                $ar = explode("_", $get['flt']['params_range'][$kkk]);
                                $vvv[1] = max($ar);
                            } else if ($vvv[1] && !$vvv[0]) {
                                $ar = explode("_", $get['flt']['params_range'][$kkk]);
                                $vvv[0] = min($ar);
                            }

                            foreach ($vvv as $kkkk => $vvvv) {
                                if ($get[$v][$kk][$kkk][$kkkk]) {
                                    $vars_str .= ($vars_str || $amp ? "&" : "?") . "{$v}[{$kk}][{$kkk}][$kkkk]=" . strip_tags($get[$v][$kk][$kkk][$kkkk]);
                                }
                            }
                        } else {
                            if ($get[$v][$kk][$kkk]) $vars_str .= ($vars_str || $amp ? "&" : "?") . "{$v}[{$kk}][{$kkk}]=" . strip_tags($get[$v][$kk][$kkk]);
                        }
                    }
                }
            }
        }
    }

    return $vars_str;
}


#
function sitemapTovar($sub)
{
    global $db, $catalogue;
    if ($db->get_var("select count(Message_ID) from Message2001 where Checked=1 AND Catalogue_ID = '$catalogue'") < 100) {
        $tovars = $db->get_results("select name, Message_ID from Message2001 where Checked=1 AND Subdivision_ID = '$sub' AND Catalogue_ID = '$catalogue'", ARRAY_A);
        if ($tovars) {
            foreach ($tovars as $t) {
                $tt[] = "<li><a href='" . nc_message_link($t[Message_ID], 2001) . "'>{$t[name]}</a></li>";
            }
            if ($tt) return "<ul class=sitemap>" . implode("", $tt) . "</ul>";
        }
    }
}

# получить ID поля по его названию и ID компонента
function isField($name, $classid)
{
    global $db;
    return $db->get_var("select Field_ID from Field where Field_Name = '$name' AND Class_ID = '$classid' AND TypeOfEdit_ID = 1");
}

# хэш цены
function hexprice($price = '')
{
    global $CONSULT_PASSWORD;
    if (is_numeric($price)) return md5(md5($price . $CONSULT_PASSWORD));
}

# хэш заказа
function hexorder($orderid)
{
    return md5(md5("ordEr*{$orderid}%"));
}

# хэш сайта
function hexsite()
{
    global $catalogue, $CONSULT_PASSWORD;
    return substr(md5($catalogue . $CONSULT_PASSWORD), 0, 4);
}


# комментарии
function getComments($classid, $msgid)
{
    global $db, $catalogue;
    $commentID = $db->get_row("select Subdivision_ID, Sub_Class_ID from Sub_Class where Class_ID = '{$classid}' AND Catalogue_ID = '$catalogue' LIMIT 0,1", ARRAY_A);
    if ($commentID['Subdivision_ID'] && $commentID['Sub_Class_ID']) return nc_objects_list($commentID['Subdivision_ID'], $commentID['Sub_Class_ID'], "&recNum=100&tovar={$msgid}");
}



# json в массив с исправлением  ошибочных записей
function orderArray($orderlist, $file = '')
{
    $z = array();
    if (strstr($orderlist, "&quot;")) {
        if ($file) $z["&quot;"] = "\u0022";
    }
    if ((strstr($orderlist, "u04") && !strstr($orderlist, "\u04")) || (strstr($orderlist, "u00") && !strstr($orderlist, "\u00"))) {
        $z["u04"] = "\u04";
        $z["u00"] = "\u00";
        $z["u21"] = "\u21";
        $z["u20"] = "\u20";
    }

    if ($z) $orderlist = strtr($orderlist, $z);

    $orderlist = html_entity_decode($orderlist, ENT_QUOTES); # html-код ковычек -> нормальные ковычки

    //$array = json_decode(html_entity_decode($orderlist),1); # так не работает, если в названии товара есть кавычки &quot;
    $array = json_decode($orderlist, 1);

    return $array;
}


# получить IP пользователя
function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // return "77.94.99.9"; // башкирия
    return $ip;
}


# любое поле пользователя
function userField($uid, $field)
{
    global $db;
    return $db->get_var("select $field from User where User_ID = '$uid'");
}


# запись логов поиска на сайте
function findlogs($text, $tot = '')
{
    global $db, $catalogue;
    if ($db->query("SHOW TABLES LIKE 'Message2033'") && !$db->get_var("select Message_ID from Message2033 where Created = '" . date("Y-m-d H:i:s") . "' AND text = '" . $text . "'") && $text) {
        $r = $db->get_row("select a.Subdivision_ID as sub, a.Sub_Class_ID as cc from Sub_Class as a, Subdivision as b where b.Catalogue_ID = '$catalogue' AND a.Subdivision_ID = b.Subdivision_ID AND a.Class_ID = 2033 AND b.Hidden_URL = '/search/'", ARRAY_A);
        if ($r['sub'] && $r['cc']) {
            $db->query("insert into Message2033 (`Subdivision_ID`, `Sub_Class_ID`, `Checked`, `Created`, `text`, `result`, `IP`) values (" . $r['sub'] . "," . $r['cc'] . ",1,'" . date("Y-m-d H:i:s") . "','" . $text . "','" . $tot . "','" . get_ip() . "')");
            $db->query("DELETE FROM Message2033 WHERE date < DATE_SUB(NOW(), INTERVAL 60 DAY) AND Subdivision_ID = '" . $r['sub'] . "'");
        }
    }
}

# цена в читаемом формате с учетом/ без учета копеек
function price($price, $kop = '', $separator = ',')
{
    if (function_exists('class2001_price')) {
        return class2001_price($price, $kop, $separator);
    } else {
        global $setting;
        return number_format(floatval($price), ($setting['kopeik'] || $kop ? 2 : 0), $separator, ' ');
    }
}

# число словами
function num2strmy($num)
{
    $nul = 'ноль';
    $ten = array(
        array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
    );
    $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    $unit = array( // Units
        array('копейка', 'копейки', 'копеек',     1),
        array('рубль', 'рубля', 'рублей', 0),
        array('тысяча', 'тысячи', 'тысяч', 1),
        array('миллион', 'миллиона', 'миллионов', 0),
        array('миллиард', 'милиарда', 'миллиардов', 0),
    );
    //
    list($rub, $kop) = explode('.', str_replace(",", ".", (sprintf("%015.2f", floatval($num)))));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit) - $uk - 1; // unit key
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
            else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk > 1) $out[] = pluralForm($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        } //foreach
    } else $out[] = $nul;
    $out[] = pluralForm(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . pluralForm($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
}


# очистка html
function normalHtml($text)
{
    $z = array(
        "width:" => "width:100%; wwidth:",
        "width=" => "width=100% wwidth=",
        "<td style" => "<td sstyle"
    );
    $newtext = strtr($text, $z);
    return $newtext;
}

# sub и сс по URL раздела
function isSub($sublink)
{
    global $db;
    $subcc = $db->get_row("select a.Subdivision_ID as sub, b.Sub_Class_ID as cc from Subdivision as a, Sub_Class as b where a.Hidden_URL = '{$sublink}' AND a.Subdivision_ID = b.Subdivision_ID", ARRAY_A);
    if ($subcc[sub] && $subcc[cc]) return array('sub' => $subcc[sub], 'cc' => $subcc[cc]);
}


/* api деловые линии */
global $devlin_url, $devlin_package;
$devlin_key = $setting['devlin_key'];

$devlin_url = array(
    "terminals" => "https://api.dellin.ru/v1/public/terminals.json"
);

$devlin_package = array(
    "onlyKey" => array("appKey" => $devlin_key)
);


function DevLinReloadCity()
{
    global $devlin_key, $devlin_url, $devlin_package, $db, $setting;
	
	
    $dataTerminals = DevLinRequest($devlin_url['terminals'], $devlin_package['onlyKey']);
    if ($dataTerminals && $devlin_key) {
        $db->query("TRUNCATE table Classificator_citytarget");
        $db->query("TRUNCATE table Message2040");
        $subcc = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class where EnglishName = 'devlin' AND Class_ID = '2040'");
        if ($subcc['sub'] && $subcc['cc']) {
            foreach ($dataTerminals['city'] as $city) {
                $cityid = $city['id'];
                $cities .= $city['name'] . " - " . encodestring($city['name'], 1) . "<br>";
                $db->query("INSERT INTO Classificator_citytarget (citytarget_ID, citytarget_Name, Value, citytarget_Priority, Checked) VALUES
                            (" . $cityid . ", '" . $city['name'] . "', '" . encodestring($city['name'], 1) . "', '" . $cityid . "', 1)");
                foreach ($city['terminals']['terminal'] as $terminal) {
                    $db->query("INSERT INTO Message2047 (Message_ID, Subdivision_ID, Sub_Class_ID, name, address, fullAddress, lat, lon, city, Checked) VALUES
                                (" . $terminal['id'] . ", {$subcc['sub']}, {$subcc['cc']}, '" . $terminal['name'] . "', '" . $terminal['address'] . "', '" . $terminal['fullAddress'] . "', '" . $terminal['latitude'] . "', '" . $terminal['longitude'] . "', '" . $cityid . "', 1)");
                }
            }
        }
    }
    if ($cities) return $cities;
}

function DevLinRequest($url, $data)
{
    $client = curl_init($url);
    curl_setopt_array($client, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HEADER => false,
        CURLOPT_POSTFIELDS => json_encode($data)
    ));
    curl_setopt(
        $client,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        )
    );
    $body = curl_exec($client);
    curl_close($client);
    if (empty($body)) {
        $err = 1;
    }
    $decodedBody = json_decode($body, true);
    if (is_null($decodedBody)) {
        $err = 1;
    }
    if (!$err) return $decodedBody;
}

/* end api деловые линии */



$nc_core = nc_Core::get_object();
$system_env = $nc_core->get_settings();
//print_r(nc_usergroup_get_group_by_user(1));

# сравнение 2-х дат
function dateCompare($date1, $date2 = '0000-00-00 00:00:00', $f, $noabs = '')
{
    $form = array("week" => 604800, "days" => 86400, "hours" => 3600, "minutes" => 60);
    $timestamp2 = strtotime($date2);
    $timestamp1 = strtotime($date1);
    $difference = floor(($timestamp2 - $timestamp1) / $form[$f]);
    if ($difference) {
        if ($noabs) {
            return $difference;
        } else {
            return abs($difference);
        }
    }
}


# код цвета из HEX в RGB
function HEXNaRGB($cvet, $arr = '')
{
    if ($cvet[0] == '#') {
        $cvet = substr($cvet, 1);
    }
    if (strlen($cvet) == 6) {
        list($r, $g, $b) = array(
            $cvet[0] . $cvet[1],
            $cvet[2] . $cvet[3],
            $cvet[4] . $cvet[5]
        );
    } elseif (strlen($cvet) == 3) {
        list($r, $g, $b) = array(
            $cvet[0] . $cvet[0],
            $cvet[1] . $cvet[1],
            $cvet[2] . $cvet[2]
        );
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    if ($arr) {
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    } else {
        return $r . "," . $g . "," . $b;
    }
}


# правильно добавить параметр в стоку с get переменными
function add_param($param)
{
    if ($_GET) {
        $arr = explode("&", $param);
        foreach ($arr as $key => $value) {
            $value = explode("=", $value);
            $_GET["{$value[0]}"] = $value[1];
        }

        return currentUrl() . "?" . http_build_query($_GET);
    } else {
        return currentUrl() . "?$param";
    }
    return;
}




# списать фото в указанные размеры
function fotosize($url, $wi, $hi, $res = '', $minVpis = '', $center = '')
{
    $margin = '';
    $url2 = $_SERVER[DOCUMENT_ROOT] . str_replace("h_", "", $url);
    $size = @getimagesize($url2);
    if ($size) {
        $sootn = round($size[1] / $size[0], 10);
        if (!$minVpis) {
            while ($size[0] > $wi || $size[1] > $hi) {
                $size[0] = $size[0] - 1;
                $size[1] = $size[0] * $sootn;
            }

            if ($center) {
                if ($size[0] < $wi) {
                    $ost1 = round(($wi - $size[0]) / 2);
                    $margin .= "margin-left:{$ost1}px;";
                }
                if ($size[1] < $hi) {
                    $ost2 = round(($hi - $size[1]) / 2);
                    $margin .= "margin-top:{$ost2}px;";
                }
            }
        } else {
            while ($size[0] < $wi || $size[1] < $hi) {
                $size[0] = $size[0] + 1;
                $size[1] = $size[0] * $sootn;
            }

            if ($center) {
                if ($size[0] > $wi) {
                    $ost1 = round(($size[0] - $wi) / 2);
                    $margin .= "margin-left:-{$ost1}px;";
                }
                if ($size[1] > $hi) {
                    $ost2 = round(($size[1] - $hi) / 2);
                    $margin .= "margin-top:-{$ost2}px;";
                }
            }
        }
        if ($res == 1) return "width:" . round($size[0]) . "px;  $margin";
        if ($res == 2) return "height:" . round($size[1]) . "px;  $margin";
        if (!$res) return "width:" . round($size[0]) . "px; height:" . round($size[1]) . "px;  $margin";
    }
}







# список select из списка объектов другого компонента
function listFromObject($field, $table, $fieldObj, $fieldID, $selected = "", $nosel = "", $where = "", $attr = "")
{
    global $db;
    $objArr = $db->get_results("select {$fieldObj} as nameopt, {$fieldID} as idopt from {$table} where " . (!$where ? "Checked = '1'" : $where) . "", ARRAY_A);
    if ($objArr) {
        foreach ($objArr as $obj) {
            $options .= "<option " . ($obj[idopt] == $selected ? "selected" : "") . " value='{$obj[idopt]}'>" . $obj[nameopt] . "</option>";
        }
        return "<select {$attr} name='f_{$field}'>" . (!$nosel ? "<option value=''>- выберите -</option>" : "") . $options . "<select>";
    }
}


# список radio из списка объектов другого компонента
function radioFromObject($field, $table, $fieldObj, $fieldID, $selected = "", $nosel = "", $where = "", $attr = "", $dev = "")
{
    global $db;
    $objArr = $db->get_results("select {$fieldObj} as nameopt, {$fieldID} as idopt " . ($attr ? ", {$attr} as attr" : "") . " from {$table} where " . (!$where ? "Checked = '1'" : $where) . "", ARRAY_A);
    if ($objArr) {
        foreach ($objArr as $obj) {
            $options .= "<label><input name='f_{$field}' type=radio " . ($obj[idopt] == $selected ? "checked" : "") . " value='{$obj[idopt]}'> <b>" . $obj[nameopt] . "</b> " . ($obj[attr] ? " &mdash; {$obj[attr]}" : "") . "</label>" . $dev;
        }
        return (!$nosel ? "<label><input name='f_{$field}' type=radio value=''> выберите </label>" . $dev : "") . $options . "";
    }
}



# массив в строку
function array_to_string($arr, $dev = ",", $templ = "%EL")
{
    foreach ($arr as $key => $el) {
        unset($z);
        $z = array("%EL" => $el, "%NUM" => $key);
        $res .= strtr($templ, $z) . (next($arr) ? $dev : "");
    }
    return $res;
}



# предыдущий объект при сортировке по приоритету
function prev_obj($priority, $class, $sub, $cc, $sortby = "")
{
    global $db;
    return $db->get_var("SELECT Message_ID FROM Message$class WHERE Subdivision_ID=$sub AND Parent_Message_ID=0 AND Sub_Class_ID=$cc AND Priority>$priority ORDER BY Priority LIMIT 1");
}


# следующий объект при сортировке по приоритету
function next_obj($priority, $class, $sub, $cc, $sortby = "")
{
    global $db;
    return $db->get_var("SELECT Message_ID FROM Message$class WHERE Subdivision_ID=$sub AND Parent_Message_ID=0 AND Sub_Class_ID=$cc AND Priority<$priority ORDER BY Priority DESC LIMIT 1");
}


# размер файла
function SizeOfFile($size, $dev = " ")
{
    $sizes = array('байт', 'кб', 'мб', 'гб', 'тб', 'пб', 'еб');
    $ext = $sizes[0];
    for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
        $size = $size / 1024;
        $ext  = $sizes[$i];
    }
    return round($size, 1) . $dev . $ext;
}





# месяц словом по номеру
function month($month)
{
    $months = array(1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    $current_month =  intval($month);
    return $months[$current_month];
}



# поле или несколько полей раздела по ID
function subParams($idsub = "", $field = "")
{
    global $db;
    if ($idsub > 0) {
        if (!$field) {
            return $db->get_row("select Subdivision_Name as name, Hidden_URL as url, EnglishName as link, ExternalURL as exturl from Subdivision where Subdivision_ID = '$idsub'", ARRAY_A);
        } else {
            return $db->get_row("select $field from Subdivision where Subdivision_ID = '$idsub'", ARRAY_A);
        }
    }
}


# данные раздела по ID компонента
function subcc_by_class($class)
{
    global $db, $catalogue;
    if ($class > 0) $subcc = $db->get_row("select b.Subdivision_ID as sub, b.Sub_Class_ID as cc from Subdivision as a, Sub_Class as b where a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = '{$class}' AND a.Catalogue_ID = '{$catalogue}'", ARRAY_A);
    return $subcc;
}

# включен ли блок
function blockIsCheck($var, $field)
{
    global $db, $catalogue;
    if ($db->get_var("select Checked from Message2016 where {$field} = '{$var}' AND Catalogue_ID = '{$catalogue}'")) return true;
}


# образать строку с учетом слов 1
function crop_str($string, $limit)
{
    $substring_limited = mb_substr($string, 0, $limit);
    if ($string != $substring_limited) {
        return mb_substr($substring_limited, 0, strrpos($substring_limited, ' '));
    } else {
        return $string;
    }
}

# образать строку с учетом слов 2
function crop_str2($string, $length)
{
    return preg_replace('/\s[^\s]+$/', '', substr($string, 0, $length));
}


# склонение числа
function pluralForm($n, $form1, $form2, $form5)
{
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}



# образать фото по размерам
function crop($file_input, $file_output, $kachestvo = 100, $crop = 'square', $percent = false)
{

    list($w_i, $h_i, $type) = getimagesize($file_input);
    if (!$w_i || !$h_i) {
        return;
    }
    $img = imagecreatefromjpeg($file_input);

    if ($crop == 'square') {
        $min = ($w_i > $h_i) ? $h_i : $w_i;
        $w_o = $h_o = $min;
        // Выравнивание по центру:
        $x_o = intval(($w_i - $min) / 2);
        $y_o = intval(($h_i - $min) / 2);
        /*
        // Выравнивание по правой стороне
        $x_o = $w_i - $min;
        // Выравнивание по низу
        $y_o = $h_i - $min;
        // Выравнивание по левой стороне
        $x_o = 0;
        // выравнивание по верху
        $y_o = 0;
        */
    } else {
        list($x_o, $y_o, $w_o, $h_o) = $crop;
        if ($percent) {
            $w_o *= $w_i / 100;
            $h_o *= $h_i / 100;
            $x_o *= $w_i / 100;
            $y_o *= $h_i / 100;
        }
        if ($w_o < 0) $w_o += $w_i;
        $w_o -= $x_o;
        if ($h_o < 0) $h_o += $h_i;
        $h_o -= $y_o;
    }
    $img_o = imagecreatetruecolor($w_o, $h_o);
    imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
    imagejpeg($img_o, $file_output, $kachestvo);
}



# яндекс погода
function ya_pogoda($city_id)
{
    $data_file = $_SERVER[DOCUMENT_ROOT] . '/pogoda/ya-pogoga_' . $city_id . '.xml';
    $time_file = $_SERVER[DOCUMENT_ROOT] . '/pogoda/ya-pogoga-time_' . $city_id . '.php';
    $id_file = $_SERVER[DOCUMENT_ROOT] . '/pogoda/ya-pogoga-id_' . $city_id . '.php';
    $time_new = time();
    $time_old = 0;
    function get_xml($city_id)
    {
        $data_file = $_SERVER[DOCUMENT_ROOT] . '/pogoda/ya-pogoga_' . $city_id . '.xml';
        $time_file = $_SERVER[DOCUMENT_ROOT] . '/pogoda/ya-pogoga-time_' . $city_id . '.php';
        $time_new = time();
        $adr_xml = 'http://export.yandex.ru/weather-ng/forecasts/' . $city_id . '.xml';
        $str_error = "Город с кодом <span class='red'>" . $city_id . "</span> не найден";
        $cont_xml = @file_get_contents($adr_xml) or exit($str_error);
        $xml_tmp = @file_put_contents($data_file, $cont_xml);
        @file_put_contents($time_file, $time_new);
    }
    if (!file_exists($id_file)) {
        @file_put_contents($id_file, $city_id);
        get_xml($city_id);
    } else {
        $city_id_old = (int)@file_get_contents($id_file);
        if ($city_id != $city_id_old) {
            @file_put_contents($id_file, $city_id);
            get_xml($city_id);
        }
    }
    if (!file_exists($time_file)) {
        get_xml($city_id);
    } else {
        $time_old = (int)@file_get_contents($time_file);
        $time_dif = (int)(($time_new - $time_old) / 60);
        if ($time_dif > 30) get_xml($city_id);
    }
    $xml = simplexml_load_file($data_file);
    $city = $xml->attributes()->city;
    $link = $xml->attributes()->link;

    $temp = $xml->fact->temperature;
    $img = 'image-v3';
    $pic = $xml->fact->$img;
    $type = $xml->fact->weather_type;

    $press = $xml->fact->pressure;
    $humid = $xml->fact->humidity;
    $wind = $xml->fact->wind_speed;
    $inf_t = $xml->informer->temperature;
    $wnd_dir = $xml->fact->wind_direction;
    if ($wnd_dir == calm) $wnd_dir = e;
    if ($temp > 0) {
        $temp = '+' . $temp;
    }
    if ($inf_t[0] > 0) {
        $inf_t[0] = '+' . $inf_t[0];
    }
    if ($inf_t[1] > 0) {
        $inf_t[1] = '+' . $inf_t[1];
    }

    if ($temp) {
        $informer = "Сейчас: $temp, $type <img alt='' src='http://yandex.st/weather/1.1.76.1/i/icons/22x22/{$pic}.png'>
Ночью: {$inf_t[1]} °C<br>
Завтра: {$inf_t[2]} °C<br>
Ветер: $wind м/сек. <img alt='' src='http://yandex.st/weather/1.1.76.1/i/wind/{$wnd_dir}.gif'><br>
Давление: $press мм.рт.ст.<br>
Влажность: {$humid}%";
    }
    return $informer;
}




# получить имя поля по ID
function field_name($fieldID)
{
    global $db;
    return $db->get_var("select Description from Field where Field_ID = '$fieldID'");
}


# определение устройства
function is_mobile()
{
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $accept = strtolower(getenv('HTTP_ACCEPT'));

    if ((strpos($accept, 'text/vnd.wap.wml') !== false) ||
        (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)
    ) {
        return 1; // Мобильный браузер обнаружен по HTTP-заголовкам
    }

    if (
        isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
        isset($_SERVER['HTTP_PROFILE'])
    ) {
        return 2; // Мобильный браузер обнаружен по установкам сервера
    }

    if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|' .
        'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|' .
        'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|' .
        'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|' .
        'm881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|' .
        'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|' .
        'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|' .
        'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|' .
        'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|' .
        'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|' .
        '_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|' .
        's800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|' .
        'd736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |' .
        'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|' .
        'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|' .
        'pocket|kindle|mobile|psp|treo|android|iphone|ipod|webos|wp7|wp8|' .
        'fennec|blackberry|htc_|opera m|windowsphone)/', $user_agent)) {
        return 3; // Мобильный браузер обнаружен по сигнатуре User Agent
    }

    if (in_array(
        substr($user_agent, 0, 4),
        array(
            "1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
            "6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
            "aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
            "aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
            "aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
            "brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
            "cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
            "dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
            "el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
            "ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
            "g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
            "hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
            "hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
            "htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
            "iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
            "iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
            "kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
            "lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
            "lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
            "lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
            "m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
            "meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
            "mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
            "mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
            "n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
            "neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
            "o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
            "pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
            "phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
            "psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
            "qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
            "rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
            "sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
            "sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
            "sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
            "sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
            "t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
            "tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
            "up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
            "vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
            "vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
            "w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
            "waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
            "winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
            "zeto", "zte-"
        )
    )) {
        return 4; // Мобильный браузер обнаружен по сигнатуре User Agent
    }

    return false; // Мобильный браузер не обнаружен
}


# сгененировать реферальный код
function ref_code($uid, $length = 4)
{
    global $db;
    $code = '';
    $symbols = '0123456789ABCDFGHJKLMNOPRSTQUVXWZ';
    for ($i = 0; $i < (int)$length - strlen($uid); $i++) {
        $num = rand(1, strlen($symbols));
        $code .= substr($symbols, $num, 1);
    }
    @$db->query("update User set refcode = '" . (strlen($uid) == 1 ? 0 : "") . $uid . $code . "' where User_ID = '" . $uid . "'");
}



# простая защита email
function safeEmail($textt)
{
    if (strstr($textt, "mailto")) {
        $res = preg_replace("~(<a[^>]+href=)([\"']?)mailto:([\\w_\\.\\-]+)([\\w_\\.\\-])@" . "([\\w_\\.\\-])([\\w_\\.\\-]+\\.[a-z]{2,4})\\2([ >])~i", "\\1\"mailto:spamux@nospam.ru\" onMouseover=\"this.href='mai' + 'lto:\\3' + '\\4' + '%40' + '\\5' + '\\6';\"\\7", $textt);
        $varr = array(
            array("fghhj", "drv", "jkk", "93", "29"),
            array("wtb", "ttgbq", "mng", "145", "81"),
            array("klhg", "gdfs", "vbcx", "225", "161"),
            array("cv", "re", "ertuy", "574", "510")
        );
        $rnd = array_rand($varr);

        return str_replace("@", "<script>" . $varr[$rnd][1] . " = " . $varr[$rnd][3] . "; " . $varr[$rnd][0] . "='#'; " . $varr[$rnd][2] . " = " . $varr[$rnd][4] . "; document.write('&'+" . $varr[$rnd][0] . "+(" . $varr[$rnd][1] . "-" . $varr[$rnd][2] . ")+';')</script>", $res);
    } else {
        return $textt;
    }
}

# простая защита email в контактах
function safeEmailContact($email)
{
    preg_match("/(.*)@(.*)\.(.*)/", $email, $matches);
    $str = "data-a1='{$matches[1]}' data-a2='{$matches[3]}' data-a3='{$matches[2]}'";
    return $str;
}

# получить поля пользователя
function getUser($userid)
{
    global $db, $HTTP_FILES_PATH;
    if ($userid > 0) $userArr = $db->get_row("select Email, ForumName as name, SUBSTRING_INDEX(ForumAvatar,':',-1) as photo, fam,phone,adres,city,otch,refcode,refdiscont from User where User_ID = '$userid'", ARRAY_A);
    if ($userArr[photo]) $userArr[photo] = $HTTP_FILES_PATH . $userArr[photo];
    return $userArr;
}


# таргетинг: ссылка на товар в разных городах
function replaceTargCat($menu)
{
    global $citylinkPath;
    return ($citylinkPath ? str_replace("/catalog/", "{$citylinkPath}/catalog/", $menu) : $menu);
}

# подразделы как фильтр
function catalogFilter($sub_array)
{
    global $db, $nc_core;

    $parent = $sub_array;

    $filtermenu = $db->get_var("SELECT filtermenu FROM Subdivision WHERE Subdivision_ID = {$sub_array[Subdivision_ID]}");

    if (!$filtermenu) {
        $filtermenu = $db->get_var("SELECT filtermenu FROM Subdivision WHERE Subdivision_ID = {$sub_array[Parent_Sub_ID]}");
        $sub_array = $nc_core->subdivision->get_by_id($sub_array[Parent_Sub_ID]);
    }
    if ($filtermenu) {
        $sub_array[Subdivision_Name] = "Все";

        $podrsub = $db->get_results("SELECT a.Subdivision_ID, a.Subdivision_Name, b.Sub_Class_ID, a.ExternalURL, a.Hidden_URL FROM Subdivision as a, Sub_Class as b WHERE Parent_Sub_ID = {$sub_array[Subdivision_ID]} AND a.Subdivision_ID = b.Subdivision_ID AND a.Checked = 1 ORDER BY a.Priority", ARRAY_A);

        array_unshift($podrsub, $sub_array);

        $reslt = array();
        foreach ($podrsub as $podr) {
            $suburl = ($inside_admin ? "/bc/?inside_admin=1&cc={$podr[Sub_Class_ID]}" : ($podr[ExternalURL] ? $podr[ExternalURL] : $podr[Hidden_URL]));
            $name = wrap($podr[Subdivision_Name]);
            $reslt[] = "<li class='subfilter-item sub{$podr[Subdivision_ID]} " . ($parent[Subdivision_ID] == $podr[Subdivision_ID] ? "active" : "") . "'>
                            <div class='subfilter-name'><a href='{$suburl}'><span>{$name}</span></a></div>
                        </li>";
        }
        if ($reslt) $html = "<ul class='subfilter-items'>" . implode("", $reslt) . "</ul>";
    }

    return $html ? $html : "";
}
# подразделы компонентов с фото
function catalogCategory($sub,  $params = array())
{
    if (function_exists('function_catalogCategory')) {
        $reslt = function_catalogCategory($sub, $params); // своя функция
    } else {
        global $db, $inside_admin, $cityid, $HTTP_FILES_PATH, $setting_texts, $setting, $noimage, $AUTH_USER_ID, $bitcat, $current_sub, $catalogue, $parent_sub_tree;

        if ($current_sub['Read_Access_ID'] > 1 && !$AUTH_USER_ID) return "";

        $padrSubLvl = array();

        if (is_numeric($params['level'])) {
            $level = $db->get_var("SELECT `Value` FROM `Classificator_subdirLvl` WHERE `subdirLvl_ID` = {$params['level']}");
        } else $level = 0;

        $podrsub = getPodrSub($sub, $params);

        if ($level) {
            switch ($level) {
                case 1:
                    $subsID = '';
                    foreach ($podrsub as $pod) {
                        $subsID .= ($subsID ? ',' : '') . $pod['Subdivision_ID'];
                    }
                    $padrSubLvlDb = getPodrSub($subsID, $params);

                    foreach ($padrSubLvlDb as $pod) {
                        $padrSubLvl[$pod['Parent_Sub_ID']][] = $pod;
                    }
                    break;
            }
        }

        if ($podrsub) {
            if ($params['list']) {
                $reslt .= "<ul class='" . ($level ? 'subivision-ul-level' : 'subivision-ul') . "'>";
            } else {
                # ширина объекта
                if ($params['sizehave']) {
                    if ($params['sizeitem_select'] == 'count') $sizeitem = $params['sizeitem_counts'];
                    elseif ($params['sizeitem'] && is_numeric($params['sizeitem'])) $sizeitem = $params['sizeitem'];
                }

                if (!$sizeitem) {
                    if ($setting['sizesub_select'] == 'count') $sizeitem = $setting['sizesub_counts'];
                    elseif ($setting['sizesub'] && is_numeric($setting['sizesub'])) $sizeitem = $setting['sizesub'];
                }

                if (!$sizeitem) $sizeitem = 224;

                # отступ
                if ($params['sizehave'] && is_numeric($params['sizeitem_margin'])) $margin = $params['sizeitem_margin'];
                elseif (is_numeric($setting['sizesub_margin'])) $margin = $setting['sizesub_margin'];
                else $margin = 0;

                # Шаблон вывода
                if ($params['template_block']) $template = $params['template_block'];
                elseif ($setting['sizesub_template']) $template = $setting['sizesub_template'];

                # object fit
                if ($params['sizehave'] && $params['sizeitem_fit']) $imagefit = $params['sizeitem_fit'];
                else $imagefit = $setting['sizesub_fit'];

                # image procent
                if ($params['sizehave'] && $params['sizeitem_image'])  $sizeitem_image = $params['sizeitem_image'];
                else $sizeitem_image = $setting["sizesub_image"];

                if ($level) $reslt .= "<ul class='sub-ul-level'>";
                else {
                    $data = subdivisionData(array(
                        'type' => 'subdivision',
                        'sizeitem' => $sizeitem,
                        'margin' => $margin,
                        'sizeitem_image' => $sizeitem_image,
                        'scrolling' => $params['scrolling'],
                        'scrollspeed' => $params['scrollspeed'],
                        'scrollNav' => $params['scrollNav'],
                        'scrollDots' => $params['scrollDots'],
                        'template' => $template,
                        'masonry' => $params['masonry'],
                        'type_masonry' => $params['type_masonry'],
                        'autoplay' => $params['autoplay'],
                        'level' => $level
                    ));
                    $reslt .= "<ul class='{$data['class']}' {$data['attr']}>";
                }

                # animate
                $animateKey = "";
                $animateDelay = 0;
                $animateDelayStep = 0.16;
                if ($params['animate']) {
                    if ($params["animate_title"] && $params["animate_title"] != "_empty_") $animateDelay += $animateDelayStep;
                    if ($params["animate_text"] && $params["animate_text"] != "_empty_") $animateDelay += $animateDelayStep;
                    if ($params["animate_items"] && $params["animate_items"] != "_empty_") $animateKey = $params["animate_items"];
                }
                if (!$animateKey && $setting["sizesub_animate"] && $setting["sizesub_animate"] != "_empty_") $animateKey = $setting["sizesub_animate"];
                if ($animateKey && $animateKey != "_empty_") $animateKey = "wow {$animateKey}";
            }
            $have = array();
            foreach ($podrsub as $podr) {
                # если несколбко инфоблоков
                if ($have[$podr['Subdivision_ID']]) continue;
                else $have[$podr['Subdivision_ID']] = 1;

                $suburl = ($inside_admin ? "/bc/?inside_admin=1&cc={$podr['Sub_Class_ID']}" : ($podr['ExternalURL'] ? $podr['ExternalURL'] : $podr['Hidden_URL']));
                $title = getLangWord("lang_sub_" . $podr['EnglishName'], wrap($podr['Subdivision_Name']));

                if ($params['list']) {
                    $reslt .= "<li class='sub{$podr['Subdivision_ID']}" . ($level ? " sub-li-level" . ($padrSubLvl[$podr['Subdivision_ID']] ? " with-level-{$level}" : '') : ' sub-li') . "'><a " . ($level ? '' : "class='btn-strt-a'") . " href='{$suburl}'><span>{$title}</span></a>";

                    if ($level && $padrSubLvl[$podr['Subdivision_ID']]) {
                        $reslt .= "<div class='sub-level-wrapper'><ul class='sub-ul-level-{$level}'>";
                        foreach ($padrSubLvl[$podr['Subdivision_ID']] as $subLvl) {
                            $subUrlLvl = ($inside_admin ? "/bc/?inside_admin=1&cc={$subLvl['Sub_Class_ID']}" : ($subLvl['ExternalURL'] ? $subLvl['ExternalURL'] : $subLvl['Hidden_URL']));
                            $titleLvl = $title = getLangWord("lang_sub_" . $subLvl['EnglishName'], wrap($subLvl['Subdivision_Name']));
                            $reslt .= "<li class='sub-li-level-{$level} sub{$subLvl['Subdivision_ID']}'><a href='{$subUrlLvl}'><span>{$titleLvl}</span></a>";
                        }
                        $reslt .= "</ul></div>";
                    }

                    $reslt .= "</li>";
                } else {
                    unset($photo);
                    # фото у самого раздела
                    if ($podr['img']) $photo = $HTTP_FILES_PATH . $podr['img'];
                    else { # взять фото у первого объекта...
                        switch ($podr['Class_ID']) {
                            case '2001':
                                $query = "SELECT a.Preview
                                          FROM Multifield as a, Message2001 as b
                                          WHERE b.Subdivision_ID = '{$podr['Subdivision_ID']}'
                                            AND a.Field_ID = '2353' AND b.Checked = 1
                                            AND a.Message_ID = b.Message_ID
                                          ORDER BY b.Priority, a.Priority
                                          LIMIT 0,1";
                                break;

                            case '2010':
                                $query = "SELECT a.Preview
                                          FROM Multifield as a, Message2010 as b
                                          WHERE b.Subdivision_ID = '{$podr['Subdivision_ID']}'
                                            AND a.Field_ID = '2388'
                                            AND b.Checked = 1
                                            AND b.Message_ID = a.Message_ID
                                          ORDER BY a.Priority DESC
                                          LIMIT 0,1";
                                break;

                            case '2021':
                                $query = "SELECT a.Preview
                                          FROM Multifield as a, Message2010 as b
                                          WHERE b.Subdivision_ID = '{$podr['Subdivision_ID']}'
                                            AND a.Field_ID = '2462'
                                            AND b.Checked = 1
                                            AND b.Message_ID = a.Message_ID
                                          ORDER BY a.Priority DESC
                                          LIMIT 0,1";
                                break;

                            default:
                                $query = false;
                                break;
                        }
                        $photo = $query ? $db->get_var($query) : null;
                    }

                    $imagefitRes = image_fit($photo ? $imagefit : "");
                    $class = !$photo ? "class='nophoto'" : "";
                    $photoUrl = ($photo ? $photo : $noimage);
                    $edit = $bitcat ? editObjBut("/bc/modules/bitcat/index.php?bc_action=editsub&subdiv={$podr['Subdivision_ID']}&reload=1") : "";
                    $name = "<div class='name " . ($podr['descr'] ? "name-text" : "") . "'>
                                <a href='{$suburl}' title='{$title}'><span>{$title}</span></a>
                                " . ($podr['descr'] ? "<div class='sub-text'>{$podr['descr']}</div>" : "") . "
                            </div>";

                    if ($level) {
                        $photoHtml = "<div class='sub-level-img-wrapper {$imagefitRes}'><a href='{$suburl}'><img alt='{$title}' src='{$photoUrl}' {$class}></a></div>";

                        $reslt .= "<li class='sub sub-level" . ($padrSubLvl[$podr['Subdivision_ID']] ? " sub-with-level-{$level}" : '') . "'>
                                        {$edit}
                                        <div class='wrapper-level'>
                                            {$photoHtml}
                                            <div class='name-wrapper'>
                                                {$name}";

                        if ($padrSubLvl[$podr['Subdivision_ID']]) {
                            $reslt .= "<div class='sub-level-wrapper'><ul class='sub-level-1'>";
                            foreach ($padrSubLvl[$podr['Subdivision_ID']] as $subLvl) {
                                $subUrlLvl = ($inside_admin ? "/bc/?inside_admin=1&cc={$subLvl['Sub_Class_ID']}" : ($subLvl['ExternalURL'] ? $subLvl['ExternalURL'] : $subLvl['Hidden_URL']));
                                $titleLvl = $title = getLangWord("lang_sub_" . $subLvl['EnglishName'], wrap($subLvl['Subdivision_Name']));
                                $reslt .= "<li class='sub-level-1 sub{$subLvl['Subdivision_ID']} '><a href='{$subUrlLvl}'><span>{$titleLvl}</span></a>";
                            }
                            $reslt .= "</ul></div>";
                        }

                        $reslt .= "</div></div></li>";
                    } else {
                        $photoHtml = "<div class='sub-img-wrapper {$imagefitRes}'><a href='{$suburl}'><img alt='{$title}' src='{$photoUrl}' {$class}></a></div>";
                        $wow_delay = $animateKey ? str_replace(",", ".", "data-wow-delay='" . ($animateDelay + ($i * $animateDelayStep)) . "s'") : "";
                        switch ($template) {
                            case 'template-2':
                                $reslt .= "<li class='sub sub{$podr['Subdivision_ID']}" . ($podr['Subdivision_ID'] == $sub ? ' active-filter-sub' : '') . " {$animateKey}' {$wow_delay}>
                                                {$edit}
                                                <div class='wrapper'>
                                                    {$photoHtml}
                                                    {$name}
                                                </div>
                                            </li>";
                                break;
                            default:
                                $reslt .= "<li class='sub sub{$podr['Subdivision_ID']}" . ($podr['Subdivision_ID'] == $sub ? ' active-filter-sub' : '') . " {$animateKey}' {$wow_delay}>
                                                {$edit}
                                                <div class='wrapper mainmenubg-bord-hov-sh'>
                                                    {$photoHtml}
                                                    {$name}
                                                </div>
                                            </li>";
                                break;
                        }
                    }
                }
            }
            $reslt .=  "</ul>";
        }
    }
    return $reslt;
}

function getPodrSub($id, $params)
{
    global $cityid, $db, $AUTH_USER_ID;

    if (!$cityid) $cityid = "0";

    $sort = $params['sortsub'] > 0 ? "Subdivision_Name" : "Priority";

    $sql = "SELECT sub.`descr`";
    $sql .= ", sub.`Hidden_URL`";
    $sql .= ", sub.`ExternalURL`";
    $sql .= ", sub.`EnglishName`";
    $sql .= ", sub.`Parent_Sub_ID`";
    $sql .= ", sub.`Subdivision_ID`";
    $sql .= ", sub.`Subdivision_Name`";
    $sql .= ", SUBSTRING_INDEX(sub.`img`,':',-1) AS img";
    $sql .= ", cc.`Class_ID`";
    $sql .= ", cc.`Sub_Class_ID`";
    $sql .= " FROM `Subdivision` AS sub";
    $sql .= " INNER JOIN `Sub_Class` AS cc";
    $sql .= " ON sub.`Subdivision_ID` = cc.`Subdivision_ID`";
    $sql .= " WHERE sub.`Parent_Sub_ID` IN ({$id})";
    $sql .= " AND sub.`Checked` = 1";
    $sql .= " AND cc.`Class_ID` IN (182, 2001, 2003, 2009, 210, 2010, 2021, 2012, 244, 2020, 2030, 2073, 2260)";
    $sql .= " AND (sub.`citytarget` LIKE '%,{$cityid},%' OR sub.`citytarget` = ',,' OR sub.`citytarget` IS NULL OR sub.`citytarget` = '')";
    $sql = getSubLangQuery($sql, 'sub.');
    $sql .= " ORDER BY sub.`{$sort}`";
    if ($params['countmenu'] > 0) $sql .= " LIMIT {$params['countmenu']}";

    return $db->get_results($sql, ARRAY_A);
}

# data attribute subdivision
function subdivisionData($p)
{
    global $AUTH_USER_ID;
    $css[] = "{$p[type]}-items";
    # size card
    $data[] = "data-sizeitem='{$p[sizeitem]}'";
    $data[] = "data-margin='{$p[margin]}'";
    if ($p[sizeitem_counts]) $data[] = "data-sizeitem-counts='{$p[sizeitem_counts]}'";
    # image fit
    if ($p[sizeitem_image]) $data[] = "data-sizeimage='{$p[sizeitem_image]}'";
    # другое
    if ($p[scrolling]) {
        $data[] = "data-owl-scrollspeed='{$p[scrollspeed]}'";
        $data[] = "data-owl-nav='{$p[scrollNav]}'";
        $data[] = "data-owl-dots='{$p[scrollDots]}'";
        $data[] = "data-owl-autoplay='{$p[autoplay]}'";
        $css[] = "owl-carousel";
    }
    # masonry
    if ($p[masonry]) {
        $data[] = "data-masonry='1'";
        if ($p[type_masonry]) $css[] = "masonry-{$p[type_masonry]}";
    }
    # find
    if ($p[find]) $data[] = "data-find='{$p[find]}'";

    if ($p[ctpl] > 1) $css[] = "{$p[type]}-template-{$p[ctpl]}";

    if (!stristr($p[template], 'template')) $p[template] = "template-1"; # default
    $css[] = "{$p[template]}";

    return array(
        'class' => implode(" ", $css),
        'attr' => implode(" ", $data)
    );
}

# object-fit image
function image_fit($param = "")
{
    $image_fit = "image-default";
    $image_fit .= ($param && $param != 'standart' ? " image-" . $param : "");
    return $image_fit;
}



# кнопка добавления объекта
function addObjBut($link, $isTitle, $a = '', $name = '', $inPanel = '')
{
    global $bitcat, $sub, $subset, $action, $AUTH_USER_ID;
    if (!$isTitle && ($bitcat || $a)) {
        if (!$inPanel) {
            if ($name == 'сравнение' || $name == 'acat') {
                return "<div class='block-edit-content'>
                            <a class='btn-a btn-a-sett' data-rel='lightcase' data-maxwidth='950' data-groupclass='modal-edit' title='Настройки раздела №{$sub}' href='/bc/modules/bitcat/index.php?bc_action=editsub&amp;subdiv={$sub}&reload=1'><span>Настройки раздела</span></a>
                        </div>";
            } else if (!$subset["nosettings"]) {
                return "<div class='block-edit-content'>
                            <div class='btn-add-plus krzl-color'>
                                <div class='btn-add-body krzl-color'>
                                    <a data-rel='lightcase' data-maxwidth='950' data-groupclass='modal-edit' title='Добавить " . ($name ? $name : 'объект') . "' href='$link?isNaked=1&amp;btninsub=1'><span>Добавить " . ($name ? $name : 'объект') . "</span></a>
                                    <a data-rel='lightcase' data-maxwidth='624' data-groupclass='modal-edit' title='Добавление подраздела' href='/bc/modules/bitcat/index.php?bc_action=addsub&subdiv={$sub}&reload=1'><span>Добавить подраздел</span></a>
                                </div>
                            </div>
                            <a class='btn-a btn-a-sett' data-rel='lightcase' data-maxwidth='950' data-groupclass='modal-edit' title='Настройки раздела №{$sub}' href='/bc/modules/bitcat/index.php?bc_action=editsub&amp;subdiv={$sub}&reload=1'><span>Настройки раздела</span></a>
                        </div>";
            }
        } else {
            return "<div class='view-btn'>
                        <a class='add-s2-btn' data-rel='lightcase' data-maxwidth='950' data-groupclass='modal-edit' title='Добавить " . ($name ? $name : 'объект') . "' href='$link?isNaked=1&amp;btninsub=1'><span>Добавить " . ($name ? $name : 'объект') . "</span></a>
                    </div>";
        }
    }
}
# кнопка изменения объекта
function editObjBut($link, $fix = '', $name = '', $idmes = '')
{
    if ($link) {
        $link = str_replace(" ", "+", preg_replace("/\/([\w]{1,}\.html)(edit_[\w]{1,}\.html)/m", '/$2', $link));
        return "<div class='block-edit-content " . ($name ? 'block-edit-obj' : 'block-edit-obj-btn') . "'>
                    <a class='btn-a " . (!$name ? 'btn-a-edit icons admin_icon_7' : null) . "' title='Редактировать " . ($name ? $name : 'объект') . ' ' . $idmes . "' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' href='$link?template=-1' data-lc-href='{$link}?template=-1&isNaked=1'>
                        <span>Редактировать " . ($name ? $name : 'объект') . "</span>
                    </a>
                </div>";
    }
}





# изменение объекта: генерация поля input с различным типом
function nc_custom_field($field, $param, $class, $title, $custom)
{
    return str_replace("'text'", "'$custom'", nc_string_field($field, $param, $class, $title));
}

function copyObjButton($classID, $objectId)
{
    $url = "/bc/modules/bitcat/index.php?bc_copy_action=getCopyObjectForm&class_id={$classID}&object_id={$objectId}";

    $html = "<a class='btn-strt-a' 
				title='Копировать объект' 
				data-rel='lightcase' 
				data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' 
				href='{$url}'
			>
				<span>Копировать объект</span>
			</a>";

    return $html;
}

# кнопка удаления объекта
function dropObjBut($msg, $class, $sys = '', $Checked = '')
{
    global $nc_core, $current_sub, $current_cc, $db;
    $result = "";
    $date_update = $db->get_var("SELECT LastUpdated FROM Message{$class} WHERE Message_ID = {$msg}");
    if (isset($Checked)) {
        $result .= "<div class='check-obj'>"
            . bc_checkbox("Checked", 1, "<span class='check-first'>Выключен</span><span class='check-second'>Включен<span>", $Checked)
            . ($date_update ? "Дата обновления:<br> {$date_update}" : "")
        . "</div>";
    }
    if (!$sys) {
        $sql = "SELECT `Keyword`
                FROM `Message{$class}`
                WHERE `Message_ID` = '" . $db->escape($msg) . "'";
        $keyword = $db->get_var($sql) ?: $current_cc['EnglishName'] . "_{$msg}";
        $result .= "<a class='btn-strt-a' title='Удалить объект?' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"showTitle\":false}' href='#сonfirm-actions' data-confirm-href='" . $current_sub['Hidden_URL'] . "drop_{$keyword}.html?" . $nc_core->token->get_url() . "'><span>Удалить объект</span></a>";
    }

    return $result;
}
# поле не создавать вод знак
function waterAccept()
{
    return "<div class='colline colline-2'>" . bc_checkbox("notWater", 1, "Без водяного знака (если он есть) на фото") . "</div>";
}

# поле не уменьшать фото
function notResize()
{
    return "<div class='colline colline-1'>" . bc_checkbox("notResize", 1, "Не изменять ширину исходного изображения") . "</div>";
}

# изменение объекта: системные и seo поля во вкладках
function editItemChecked($tab = 0, $Priority = '', $Keyword = '', $ncTitle = '', $ncKeywords = '', $ncDescription = '', $classID = '', $system = '', $lang = '')
{
    global $setting;

    if ($tab == 1) return ($setting['language'] ? "<li class='tab'><a href='#tab_lang'>Язык вывода</a></li>" : "") . "<li class='tab'><a href='#tab_system'>Системное</a></li>" . ($Priority != 'system' && permission("seo") ? "<li class='tab'><a href='#tab_seo'>SEO</a></li>" : "");
    $class = $tab == 2 ? '' : 'none';
    $html = "";

    if ($setting['language']) {
        $html .= ($tab == 2 ? "<div class='colblock'><h4>Язык вывода</h4>" : "<div class='none' id='tab_lang'>") . "
                    " . nc_lang_field($lang) . "
                </div>";
    }

    $html .= ($tab == 2 ? "<div class='colblock'><h4>Системное</h4>" : "<div class='none' id='tab_system'>") . "
                <div class='colline colline-2'>" . bc_input("bc_objsys[Priority]", $Priority, "Приоритет", "size='5'") . "</div>
                " . waterAccept() . "
                " . ($classID ? "<div class='colline colline-2'>" . removeObjHtml($classID) . "</div>" : NULL) . "
                </div>";

    if (!$system) {
        $html .= ($tab == 2 ? "<div class='colblock'><h4>SEO</h4>" : "<div class='none' id='tab_seo'>") . "
                    <div class='colline colline-1'>" . bc_input("bc_objsys[Keyword]", ($Keyword == 'NULL' || !$Keyword ? "" : $Keyword), "Название в ссылке (латиница, без пробелов)", "size='50'") . "</div>
                    <div class='colline colline-1'>" . bc_input("bc_objsys[ncTitle]", $ncTitle, "Заголовок страницы (Title)", "size='50'") . "</div>
                    <div class='colline colline-1'>" . bc_input("bc_objsys[ncDescription]", $ncDescription, "Описание страницы (Description)", "size='50'") . "</div>
                    <div class='colline colline-1'>" . bc_input("bc_objsys[ncKeywords]", $ncKeywords, "Ключевые слова страницы (Keywords)", "size='50'") . "</div>
                </div>";
    }

    return $html;
}

# перемещение объекта: поле
function removeObjHtml($class)
{
    global $db, $catalogue;
    $systemSubdivisionEnglishName = [
        'spec', 'hits', 'new', 'recommend', 'actions', 'search', 'vendors', 'favorites', 'comparison'
    ];
    $subs = $db->get_results(
        "SELECT 
            a.Subdivision_Name as name, 
            a.Subdivision_ID as sub, 
            a.Parent_Sub_ID as par, 
            b.Sub_Class_ID as cc,
            b.EnglishName
        FROM 
            Subdivision as a, 
            Sub_Class as b 
        WHERE 
            a.Subdivision_ID = b.Subdivision_ID 
            AND a.Catalogue_ID = '$catalogue' 
            AND b.Class_ID = '$class' 
        ORDER BY 
            a.Parent_Sub_ID,a.Priority",
        ARRAY_A
    );

    if ($subs) {
        $opt = "<option value=''>- выбрать -</option>";
        foreach ($subs as $s) {
            if (in_array($s['EnglishName'], $systemSubdivisionEnglishName)) continue;
            $opt .= "<option value='" . $s['sub'] . "-" . $s['cc'] . "'>" . ($s['par'] > 0 ? "- " : NULL) . $s['sub'] . ". " . $s['name'] . "</option>";
        }
        $sel = bc_select("removeID", $opt, "Переместить в:", "class='ns'");
        return $sel;
    }
}

function get_subs_class($class = '')
{
    global $db, $catalogue;

    return array_merge(
        (!$class ? ($db->get_results(
            "SELECT 
                a.Subdivision_Name AS `name`, 
                a.Subdivision_ID AS sub, 
                a.Parent_Sub_ID AS par
            FROM
                Subdivision AS a
            WHERE
                a.Catalogue_ID = '{$catalogue}'
                AND a.Hidden_URL = '/index/'",
            ARRAY_A
        ) ?: []) : []),
        ($db->get_results(
            "SELECT 
			a.Subdivision_Name AS `name`, 
			a.Subdivision_ID AS sub, 
			a.Parent_Sub_ID AS par, 
			b.Sub_Class_ID AS cc
		FROM
			Subdivision AS a, 
			Sub_Class AS b 
		WHERE
            a.Subdivision_ID = b.Subdivision_ID
			AND a.Catalogue_ID = '{$catalogue}'
			AND a.Hidden_URL NOT LIKE '/index/[a-z]%'
			" . ($class ? "AND b.Class_ID = '{$class}'" : "AND (a.systemsub != 1  OR (a.EnglishName = 'index' AND a.Parent_Sub_ID = '0'))") . "
		ORDER BY
			a.Hidden_URL,a.Priority",
            ARRAY_A
        ) ?: [])
    );
}

/**
 * Возващяет массив разделов отсортированый по вложености
 * 
 * @param int $class номер шаблон
 * @param string $lvlSeparator Разделитель уровней разделов в наименовнии
 * 
 * @return array
 */
function getSubSelectOption($class = 2001, $lvlSeparator = '-')
{
    return ['- Не выбрано -'] + sortChildsSub(get_subs_class($class), 0, $lvlSeparator);
}

function sortChildsSub($subs, $parentID, $lvlSeparator = '')
{

    $list = [];
    $nextLvlSeparator = $lvlSeparator . substr($lvlSeparator, 0, 1);
    foreach ($subs as $key => $sub) {
        if ($sub['par'] == $parentID) {
            $subID =  $sub['sub'];
            $list[$subID] = trim(substr($lvlSeparator, 1) . " {$subID} {$sub['name']}");
            unset($subs[$key]);
            $list = $list + sortChildsSub($subs, $subID, $nextLvlSeparator);
        }
    }
    return $list;
}

# перемещение объекта: логика
function removeObj($idmes, $class, $kuda)
{
    global $db, $setting, $db, $catalogue;
    $k = explode("-", $kuda);
    if ($k[0] > 0 && $k[1] > 0 && $class > 0 && $idmes > 0) {
        # if have group items
        if ($setting[groupItem] && $class == 2001) {
            $item = $db->get_row("SELECT Subdivision_ID, name FROM Message2001 WHERE Message_ID = '{$idmes}'", ARRAY_A);
            if ($item) $db->query("UPDATE Message2001 SET Subdivision_ID = '" . $k[0] . "', Sub_Class_ID = '" . $k[1] . "' WHERE Catalogue_ID = '{$catalogue}' AND Subdivision_ID = '{$item[Subdivision_ID]}' AND name = '{$item[name]}' AND Message_ID != '{$idmes}'");
        }

        $db->query("UPDATE Message{$class} SET Subdivision_ID = '" . $k[0] . "', Sub_Class_ID = '" . $k[1] . "' WHERE Message_ID = '{$idmes}'");
    }
}

# изменение объекта: обновление системных полей
function objsys($sys, $class, $obj)
{
    global $db;
    if ($sys && is_array($sys)) {
        foreach ($sys as $k => $f) {
            $sqlf .= ($sqlf ? ", " : "") . "{$k} = " . (addslashes(strip_tags($f)) ? "'" . addslashes(strip_tags($f)) . "'" : "NULL");
        }
        if (is_numeric($class) && is_numeric($obj)) {
            $sql = "update Message{$class} set {$sqlf} where Message_ID = '{$obj}'";
            $db->query($sql);
        }
    }
}


# изменение объекта: таргетинг: поле выбора города объекта
function nc_city_field($citysel = '')
{
    global $db, $catalogue, $setting;
    foreach ($setting['lists_targetcity'] as $trgID => $trg) {
        $citylistselect .= "<div class='colline colline-3'>" . bc_checkbox("f_citytarget[$trgID]", $trgID, $trg['name'], (strstr($citysel, "," . $trgID . ",") ? 1 : 0)) . " </div>";
    }
    $citylistselect .= "<div class='colline colline-3'>" . bc_checkbox("f_citytarget[9999]", 9999, "Для не выбранного города", (strstr($citysel, ",9999,") ? 1 : 0)) . " </div>";
    $citylistselect = "<div class='colblock colblock-target'>
                            <h4>Содержание доступно только в городах (по-умолчанию - везде)</h4>
                            <div class='multi-body'>{$citylistselect}</div>
                        </div>";
    return $citylistselect;
}

# ID сайта
function siteid()
{
    global $nc_core;
    $curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    $catalogue = $curCat['Catalogue_ID'];
    if ($catalogue > 0) return $catalogue;
}


# объект: подсветка недоступных и выключенных на данной странице объектов
function objHidden($checked = 0, $cities = '', $city = '')
{
    global $setting;
    return ($checked == 0 || ($setting['targeting'] && $city >= 0 && $cities && $cities != ',,' && !strstr($cities, ",{$city},")) ? "uncheck" : NULL);
}

# выбор времени перезвона
function callTimeOpt($start = '9', $end = '16', $param = 1)
{
    $startMinute = "00";
    $endMinute = "00";
    $e = 0;

    $start = str_replace(".", ":", trim($start));
    $end = str_replace(".", ":", trim($end));
    if (stristr($start, ":")) {
        $startArr = explode(":", $start);
        $start = $startArr[0];
        $startMinute = $startArr[1];
    }
    if (stristr($end, ":")) {
        $endArr = explode(":", $end);
        $end = $endArr[0];
        $endMinute = $endArr[1];
    }


    if ($start < $end) {
        $minute = $startMinute;
        for ($i = $start; $i <= $end; $i++) {
            if ($minute == "00") {
                $opt .= "<option {$startMinute} value='{$i}:00' " . ($param == 2 && "{$end}:{$endMinute}" == "{$i}:00" ? "selected" : NULL) . ">{$i}:00</option>";
                $minute = "30";
            }

            $next = "{$end}:{$endMinute}" == "{$i}:00" ? false : true;

            if ($minute == "30" && $next) {
                $opt .= "<option value='{$i}:30' " . ($param == 2 && "{$end}:{$endMinute}" == "{$i}:30" ? "selected" : NULL) . ">{$i}:30</option>";
                $minute = "00";
            }
        }
    }


    return $opt;
}


# товары в вертикальном меню
function itemsinmenu($menu, $level = 2, $clear = '')
{
    global $db, $current_sub, $catalogue, $setting;
    preg_match_all("/(<!--itmscl (.*)-->)/Um", $menu, $res);
    if ($res[2]) {
        foreach ($res[2] as $url) {
            $subr = $subH = $zm = $open = '';
            $i++;
            if (!$clear) {
                $cursub = $db->get_row("select b.Subdivision_ID as id, b.Sub_Class_ID as ccid, b.EnglishName as ccname, a.Hidden_URL, a.Parent_Sub_ID as parnt from Subdivision as a, Sub_Class as b where a.Hidden_URL = '" . $url . "' AND b.Catalogue_ID = '$catalogue' AND a.Subdivision_ID = b.Subdivision_ID", ARRAY_A);
                # 2001
                $itemArr = $db->get_results("select Message_ID, name, Keyword, extlink from Message2001 where Subdivision_ID = '{$cursub[id]}' AND oneitem!= 1 AND Checked = 1 AND Catalogue_ID = '$catalogue' ORDER BY Priority", ARRAY_A);
                # 2001
                if (!$itemArr && $setting['message2073_check']) $itemArr = $db->get_results("select Message_ID, name, Keyword from Message2073 where Subdivision_ID = '{$cursub[id]}' AND Checked = 1 AND Catalogue_ID = '$catalogue' ORDER BY Priority", ARRAY_A);
            }
            if (count($itemArr) > 0) { // есть товары
                foreach ($itemArr as $s) {
                    $urls = '';
                    $urls = "{$cursub[Hidden_URL]}" . ($s[Keyword] ? $s[Keyword] : "{$cursub[ccname]}_{$s[Message_ID]}") . ".html";
                    if ($level == 1) $subH .= "<li class='menu_open menu_open_item " . (currentUrl() == $urls ? "active" : NULL) . "'><a href='" . ($s[extlink] ? $s[extlink] : $urls) . "'><span class='menu_title'>{$s[name]}<span class='menu-sub'></span></span></a></li>";
                    if ($level == 2) $subH .= "<li " . (currentUrl() == $urls ? "class=active" : NULL) . "><a href='" . ($s[extlink] ? $s[extlink] : $urls) . "'>{$s[name]}</a></li>";
                }

                if (strstr(currentUrl(), $url)) $open = 'active';
                if ($subH) {
                    if ($level == 1) $subH = "<ul class='menu-dashed-no menu-decoration menu_catalog'>$subH</ul>";
                    if ($level == 2) $subH = "<ul class='left_m_sec left_m_tovar' " . ($open ? "style='display:block;'" : "") . ">$subH</ul>";
                }
                $menu = str_replace("<!--itms {$url}-->", $subH, $menu);
                $menu = str_replace("<!--itmscl {$url}-->", "menu_open {$open}", $menu);
            } else {
                $menu = str_replace("<!--itms {$url}-->", "", $menu);
                $menu = str_replace("<!--itmscl {$url}-->", "", $menu);
            }
        }
    }
    return $menu;
}

#
function normArtFile($name, $end = '')
{
    $zamen = array("/" => "-", " " => "-");
    if (!$end) $end = "jpg";
    $tmpname = (stristr($name, $end) ? $name : $name . "." . $end);
    $tmpname = str_replace("/", "-", $tmpname);
    $tmpname = str_replace(" ", "-", $tmpname);
    return $tmpname;
}

function normArtFile2($name, $end = '')
{
    $zamen = array("/" => "");
    if (!$end) $end = "jpg";
    $tmpname = (stristr($name, $end) ? $name : $name . "." . $end);
    $tmpname = str_replace("/", "", $tmpname);
    return $tmpname;
}

# записать лог
function logs($text)
{
    global $DOCUMENT_ROOT, $pathInc;
    if ($pathInc) file_put_contents($DOCUMENT_ROOT . $pathInc . '/log.txt', $text);
}

# сбросить кэш блоков
function clearCache($class = '', $subdiv = '', $nav = '', $cat = '')
{
    global $db, $catalogue;
    $bl_id = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class where Class_ID = '2016' AND Catalogue_ID='" . ($cat ? $cat : $catalogue) . "' LIMIT 0,1", ARRAY_A);
    if ($bl_id[sub] > 0) {
        if ($class && $subdiv > 0) {
            $db->query("update Message2016 set cache = '' where Subdivision_ID = '" . $bl_id[sub] . "' AND sub = '" . $subdiv . "'");
            $reslt = 1;
        }
        if ($nav > 0) {
            $db->query("update Message2016 set cache = '' where Subdivision_ID = '" . $bl_id[sub] . "' AND sub > 0 AND (cc = '' OR cc = '0' OR cc IS NULL) AND template > 0");
            $reslt = 2;
        }
        if (!$reslt) {
            $db->query("update Message2016 set cache = '' where Subdivision_ID = '" . $bl_id[sub] . "'");
            $reslt = 3;
        }
    }
    return $reslt;
}

# кнопки сортировки в таблицах по полям
function sortbutton($field, $name, $desc = false)
{
    global $setting, $current_cc, $current_catalogue, $AUTH_USER_ID;
    $btn = '';
    $defOrder = $current_cc['SortBy'] ? explode(' ', mb_strtolower($current_cc['SortBy'])) : ($setting['defaultSortSite'] ? explode(';', $setting['defaultSortSite']) : array('priority'));
    if ($_SERVER['REQUEST_URI'] != '/') {
        $vars_str = vars_str($_GET, "recNum,nc_ctpl,curPos,find,tag,filter,flt,flt1,flt3,cur_cc,subr,r", 1);
        $sort = "sort={$field}" . ($desc ? '&desc=1' : '');
        $url = explode('?', $_SERVER['REQUEST_URI']);
        if (!isset($_GET['sort']) && $defOrder[0] == $field && $defOrder[1] == $desc) $selected = 1;
        if ($_GET['sort'] == $field && isset($_GET['desc']) == $desc && !$selected) $selected = 1;

        $btn = "<option data-link='{$url[0]}?{$sort}{$vars_str}' " . ($selected ? "selected" : null) . ">{$name}</option>";
    }

    return $btn;
}



# текущий url без параметров
function currentUrl()
{
    $uri_parts = explode('?', $_SERVER['REQUEST_URI']);
    return $uri_parts[0];
}


# валидация настроек блоков
function valideSettings($arrSet, $vars)
{
    $allow = explode(",", str_replace(" ", "", $vars));
    $arrayDel = array("'" => "", "\"" => "", ";" => "", "}" => "", "{" => "", "<" => "", ">" => "", "[" => "", "]" => "", "–" => "-");
    if ($arrSet) {
        foreach ($arrSet as $k => $v) { // 1nd
            $isNum = false;
            if (!is_array($v)) {
                if (!in_array($k, $allow)) {
                    unset($arrSet[$k]);
                } else {
                    // is only number
                    if (preg_match("/\d+%/i", $arrSet[$k][$kk]) || preg_match("/\d+px/i", $arrSet[$k][$kk])) {
                        $arrSet[$k][$kk] = preg_replace("/[^0-9]/", '', $arrSet[$k][$kk]);
                        $isNum = true;
                    }

                    $arrSet[$k] = addslashes(strip_tags(strtr($arrSet[$k], $arrayDel)));
                    if (!$isNum) if ($arrSet[$k] == '0') $arrSet[$k] = '';
                }
            } else {
                foreach ($v as $kk => $vv) { // 2nd
                    if (!in_array($kk, $allow)) {
                        unset($arrSet[$k][$kk]);
                    } else {
                        // is only number
                        if (preg_match("/\d+%/i", $arrSet[$k][$kk]) || preg_match("/\d+px/i", $arrSet[$k][$kk]) || is_numeric($arrSet[$k][$kk])) {
                            $arrSet[$k][$kk] = preg_replace("/[^0-9]/", '', $arrSet[$k][$kk]);
                            $isNum = true;
                        }

                        $arrSet[$k][$kk] = addslashes(strip_tags(strtr($arrSet[$k][$kk], $arrayDel)));
                        if (!$isNum) if ($arrSet[$k][$kk] == '0') $arrSet[$k][$kk] = '';
                    }
                }
            }
        }
        return json_encode($arrSet);
    }
}

# настройки компонента в разделе
function setClassBlock($arrSet = '', $classid = '', $contenttype = '', $subidthisblk = '')
{
    global $db, $catalogue, $setting, $DOCUMENT_ROOT, $pathInc, $pathInc2, $current_catalogue;
    unset($setBl);
    if (!$arrSet) {
        $st = $db->get_row("SELECT phpset, settings FROM Message2016 WHERE Message_ID = '{$subidthisblk}'", ARRAY_A);
        $phpset = orderArray($st[phpset]);
        $settings = orderArray($st[settings]);
        if (is_array($phpset['contsetclass'])) $arrSet = array_merge($phpset['contsetclass'], $settings);
    }

    if (!$contenttype) { # текст
        $setBl .= "<div class='colheap' data-jsopenmain='setAnimate'>";
        $setBl .= "<h4 data-jsopen='setAnimate'>Настройка анимации</h4>";
        $setBl .= "<div data-jsopenthis='setAnimate' class='" . (!$arrSet['animate'] ? "none" : "") . "'>";
        $setBl .= "<div class='colline colline-1 colline-animate' data-jsopen='settAnimate'>" . bc_checkbox("phpset[contsetclass][animate]", 1, "Анимация", $arrSet['animate']) . "</div>";
        $setBl .= "<div class='" . (!$arrSet['animate'] ? "none" : "") . "' data-jsopenthis='settAnimate'>";
        $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_title]", getOptionSelect("animate_title", $arrSet['animate_title']), "Анимация заголовка", "class='ns'") . "</div>";
        $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_text]", getOptionSelect("animate_text", $arrSet['animate_text']), "Анимация описания", "class='ns'") . "</div>";
        $setBl .= "</div>";
        $setBl .= "</div>";
        $setBl .= "</div>";
    }
    if ($contenttype == 1 && $classid) { // настройки вывода из компонента

        if ($classid) $ctplarr = $db->get_results("select Class_ID, Class_Name from Class where ClassTemplate = {$classid} AND Class_Name NOT LIKE '%KORZILLA%' order by Class_Name", ARRAY_A);
        # Шаблоны карточек (дублируеться)
        $template = $db->get_var("SELECT data FROM Bitcat WHERE `key` = 'size{$classid}_template'");
        if ($template) {
            $template = array_merge(array('' => "Не выбрано"), json_decode($template, 1));

            # модули шаблонов компонентов
            if (!$current_catalogue) $current_catalogue = $nc_core->catalogue->get_by_id($this->catID);
            if ($current_catalogue['customCode']) {
                $templatePath = $DOCUMENT_ROOT . $pathInc2 . "/template/{$classid}/template/objects/";
                if (is_dir($templatePath)) {
                    if ($handle = opendir($templatePath)) {
                        while (false !== ($file = readdir($handle))) {
                            if ($file != '.' && $file != '..') {
                                $pathFile = $templatePath . $file;
                                if (is_file($pathFile)) {
                                    $name = str_replace(".php", "", $file);
                                    $template = array_merge($template, array($name => $name));
                                }
                            }
                        }
                        closedir($handle);
                    }
                }
            }

            $templateOption = getOptionsFromArray($template, $arrSet[template_block]);

            $setBl .= "<div class='colline colline-4 colline-template_block'>" . bc_select("phpset[contsetclass][template_block]", $templateOption, "Шаблон карточки", "class='ns'") . "</div>";
        } else if ($ctplarr && $classid != 2012 && $classid != 2088) {
            foreach ($ctplarr as $ctpl) {
                $ctpls .= "<option value='{$ctpl[Class_ID]}' " . ($ctpl[Class_ID] == $arrSet[nc_ctpl] ? "selected" : NULL) . ">{$ctpl[Class_Name]}</option>";
            }
            $ctplHtml = "<option value=''>- не выбран -</option>" . $ctpls;

            $setBl .= "<div class='colline colline-3 colline-nc_ctpl'>" . bc_select("phpset[contsetclass][nc_ctpl]", $ctplHtml, "Вариант отображения", "class='ns'") . "</div>";
        }

        // новости
        if ($classid == 2003) {
            $setBl .= "<div class='colline colline-1 colline-objInModal'>" . bc_checkbox("phpset[contsetclass][objInModal]", 1, "Открывать новость в модальном окне", $arrSet[objInModal], "") . "</div>";
        }


        if ($classid == 2030) {
            $setBl .= "<div class='colline colline-4 colline-noname'>" . bc_checkbox("phpset[contsetclass][noname]", 1, "Без наименований", $arrSet[noname], "") . "</div>";
        }

        if ($classid == 2001 || $classid == 2003 || $classid == 2010 || $classid == 2030 || $classid == 244 || $classid == 2021) { // Размеры объектов

            // Размеры карточек (дублируеться)
            $size = getsizeclass(array(
                "id" => $classid,
                "sizeitem_select" => ($arrSet["sizeitem_select"] ? $arrSet["sizeitem_select"] : $setting["size{$classid}_select"]),
                "sizeitem" => ($arrSet["sizeitem"] ? $arrSet["sizeitem"] : $setting["size{$classid}"]),
                "sizeitem_counts" => ($arrSet["sizeitem_counts"] ? $arrSet["sizeitem_counts"] : ""),
                "sizeitem_margin" => (is_numeric($arrSet["sizeitem_margin"]) ? $arrSet["sizeitem_margin"] : $setting["size{$classid}_margin"]),
                "sizeitem_image_select" => ($arrSet["sizeitem_image_select"] ? $arrSet["sizeitem_image_select"] : $setting["size{$classid}_image_select"]),
                "sizeitem_image" => ($arrSet["sizeitem_image"] ? $arrSet["sizeitem_image"] : $setting["size{$classid}_image"]),
                "sizeitem_fit" => ($arrSet["sizeitem_fit"] ? $arrSet["sizeitem_fit"] : $setting["size{$classid}_fit"])
            ));
            $setBl .= "<div class='colheap' data-jsopenmain='settCart'>";
            $setBl .= "<h4 data-jsopen='settCart'>Настройка размеров объектов</h4>";
            $setBl .= "<div data-jsopenthis='settCart' class='" . (!$arrSet[sizehave] && !$arrSet[masonry] ? "none" : "") . "'>";
            if ($classid == 2010) {
                $setBl .= "<div class='colline colline-1 colline-masonry' data-jsopen='settMasonry'>" . bc_checkbox("phpset[contsetclass][masonry]", 1, "Включить Masonry", $arrSet[masonry]) . "</div>";
                $setBl .= "<div class='" . (!$arrSet[masonry] ? "none" : "") . "' data-jsopenthis='settMasonry'>";
                $setBl .= "<div class='colline colline-3 colline-type_masonry'>" . bc_select("phpset[contsetclass][type_masonry]", getOptionSelect("type_masonry", $arrSet[type_masonry]), "Порядок размеров", "class='ns'") . "</div>";
                $setBl .= "</div>";
            }

            $setBl .= "<div class='colline colline-1 colline-sizehave' data-jsopen='settCartSize'>" . bc_checkbox("phpset[contsetclass][sizehave]", 1, "Индивидуальное отображение в блоке", $arrSet[sizehave]) . "</div>";
            $setBl .= "<div class='" . (!$arrSet[sizehave] ? "none" : "") . "' data-jsopenthis='settCartSize'>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_select'>" . bc_select("phpset[contsetclass][sizeitem_select]", $size["sizeitem_select"], "Размер карточек", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem'>" . bc_input("phpset[contsetclass][sizeitem]", $size["sizeitem"] . "px", "px") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_counts'>" . bc_multi_line("phpset[contsetclass][sizeitem_counts]", $size["sizeitem_counts"], "", 2) . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_margin'>" . bc_input("phpset[contsetclass][sizeitem_margin]", $size["sizeitem_margin"], "Отступ справа (px)") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_image_select'>" . bc_select("phpset[contsetclass][sizeitem_image_select]", $size["sizeitem_image_select"], "Пропорции изображения", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_image'>" . bc_input("phpset[contsetclass][sizeitem_image]", $size["sizeitem_image"] . "%", "%") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-sizeitem_fit'>" . bc_select("phpset[contsetclass][sizeitem_fit]", $size["sizeitem_fit"], "Отображение", "class='ns'") . "</div>";
            $setBl .= "</div>";
            $setBl .= "<div class='colline colline-1 colline-animate' data-jsopen='settAnimate'>" . bc_checkbox("phpset[contsetclass][animate]", 1, "Анимация", $arrSet[animate]) . "</div>";
            $setBl .= "<div class='" . (!$arrSet[animate] ? "none" : "") . "' data-jsopenthis='settAnimate'>";
            $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_title]", getOptionSelect("animate_title", $arrSet[animate_title]), "Анимация заголовка", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_text]", getOptionSelect("animate_text", $arrSet[animate_text]), "Анимация описания", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_items]", getOptionSelect("animate_items", $arrSet[animate_items]), "Анимация объектов", "class='ns'") . "</div>";
            $setBl .= "</div>";
            $setBl .= "</div>";
            $setBl .= "</div>";

            // Прокрутка карточек (дублируеться)
            $sliderNavOption = array(
                0 => "Выключены",
                1 => "По бокам",
                2 => "В углу",
                3 => "По краям заголовка"
            );
            $sliderNav = getOptionsFromArray($sliderNavOption, $arrSet[scrollNav]);
            $setBl .= "<div class='colheap " . ($arrSet[scrolling] ? "active" : "") . "' data-jsopenmain='scrollSett'>
                            <h4 data-jsopen='scrollSett'>Прокрутка</h4>
                            <div data-jsopenthis='scrollSett' " . (!$arrSet[scrolling] ? "class='none'" : "") . ">
                                <div class='colline colline-4 colline-scrolling'>" . bc_checkbox("phpset[contsetclass][scrolling]", 1, "Прокрутка<br>объектов", $arrSet[scrolling], "class='switch-twoline'") . "</div>
                                <div class='colline colline-4 colline-scrollNav'>" . bc_select("phpset[contsetclass][scrollNav]", $sliderNav, "Стрелочки", "class='ns'") . "</div>
                                <div class='colline colline-4 colline-scrollDots'>" . bc_checkbox("phpset[contsetclass][scrollDots]", 1, "Пагинация", $arrSet[scrollDots]) . "</div>
                                <div class='colline colline-4 colline-scrollspeed'>" . bc_input("phpset[contsetclass][scrollspeed]", $arrSet[scrollspeed], "Скорость смены (ms)") . "</div>
                                <div class='colline colline-4 colline-scrollbutcol'>" . bc_color("settings[scrollbutcol]", $arrSet['scrollbutcol'], "Фон") . "</div>
                                <div class='colline colline-4 colline-scrollbutfont'>" . bc_color("settings[scrollbutfont]", $arrSet['scrollbutfont'], "Иконка") . "</div>
                                <div class='colline colline-4 colline-autoplay'>" . bc_input("phpset[contsetclass][autoplay]", $arrSet['autoplay'], "Автопрокрутка (ms)") . "</div>
                            </div>
                        </div>";
        }


        if ($classid == 2005) { // корзина
            $minicartTypeArr = array(
                1 => "Кнопка: с выпадающим списком",
                2 => "Кнопка: без выпадающего списка",
                3 => "В контентной области: показывать содержимое корзины",
                4 => "В контентной области: скрыть содержимое корзины"
            );
            $minicartType = getOptionsFromArray($minicartTypeArr, $arrSet[minicarttype]);

            $minicartBordArr = array(0 => "- нет -", 1 => "Сплошная", 2 => "Пунктир");
            $minicartBord = getOptionsFromArray($minicartBordArr, $arrSet[minicartbord]);

            $setBl .= "<div class='colline colline-3 colline-minicarttype'>" . bc_select("phpset[contsetclass][minicarttype]", $minicartType, "Вид мини-корзины", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-3 colline-minicartbord'>" . bc_select("phpset[contsetclass][minicartbord]", $minicartBord, "Граница миникорзины", "class='ns'") . "</div>";
        }

        if ($classid == 2012) { // контакты
            $contactTypeArr = array(
                'template-1' => "Контакты",
                'template-2' => "Карта и Контакты",
                'template-3' => "Карта"
            );
            $contactType = getOptionsFromArray($contactTypeArr, $arrSet[template_block]);

            $setBl .= "<div class='colline colline-3 colline-template_block'>" . bc_select("phpset[contsetclass][template_block]", $contactType, "Тип", "class='ns'") . "</div>";
        }

        if ($classid == 2088) { // дилерская сеть
            $contactTypeArr = array(
                'template-1' => "Контакты",
                'template-2' => "Карта и Контакты",
                'template-3' => "Карта"
            );
            $contactType = getOptionsFromArray($contactTypeArr, $arrSet[template_block]);

            $setBl .= "<div class='colline colline-3 colline-template_block'>" . bc_select("phpset[contsetclass][template_block]", $contactType, "Тип", "class='ns'") . "</div>";
        }


        if ($classid == 2004) { // баннеры
            $banereffectArr = array(
                "" => "default",
                "fadeIn|fadeOut" => "fade",
                "fadeInLeft|fadeOutRight" => "fadeLeft",
                "fadeInRight|fadeOutLeft" => "fadeRight",
                "fadeInDown|fadeOutDown" => "fadeDown",
                "fadeInUp|fadeOutUp" => "fadeUp",
                "rollIn|rollOut" => "rollIn",
                "slideInDown|slideOutDown" => "slideDown",
                "slideInUp|slideOutUp" => "slideUp",
                "rotateIn|rotateOut" => "rotate",
                "rotateInDownLeft|rotateOutDownLeft" => "rotateDownLeft",
                "rotateInDownRight|rotateOutDownRight" => "rotateDownRight",
                "rotateInUpLeft|rotateOutUpLeft" => "rotateUpLeft",
                "rotateInUpRight|rotateOutUpRight" => "rotateUpRight"
            );
            foreach ($banereffectArr as $k => $v) {
                $banereffectOpt .= "<option value='$k' " . ($k == $arrSet[banereffect] ? "selected" : "") . "> $v</option>";
            }
            /*$captioneffectArr = array("move"=>"move", "fade"=>"fade", "parallax"=>"parallax", "slide"=>"slide", "traces"=>"traces");
            $captioneffectOpt = "<option value=''>- нет -</option>";
            foreach($captioneffectArr as $k => $v) {
                $captioneffectOpt .= "<option value='$k' ".($k==$arrSet[captioneffect] ? "selected" : "")."> $v</option>";
            }*/

            // Пагинация
            $sliderDotsOption = array(
                0 => "Выключены",
                1 => "Точки",
                2 => "Кнопки"
            );
            $scrollDots = getOptionsFromArray($sliderDotsOption, $arrSet[scrollDots]);

            $setBl .= "<div class='colline colline-4 colline-banereffect'>" . bc_select("phpset[contsetclass][banereffect]", $banereffectOpt, "Эффект смены слайда", "class='ns'") . "</div>";
            /*$setBl .= "<div class='colline colline-1'>".bc_select("phpset[contsetclass][captioneffect]", $captioneffectOpt, "Эффект смены текста на слайде", "class='ns'")."</div>";*/
            // Прокрутка карточек
            $setBl .= "<div class='colline colline-4 colline-scrollNav'>" . bc_checkbox("phpset[contsetclass][scrollNav]", 1, "Стрелочки (ms)", $arrSet[scrollNav]) . "</div>";
            $setBl .= "<div class='colline colline-4 colline-scrollDots'>" . bc_select("phpset[contsetclass][scrollDots]", $scrollDots, "Пагинация", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-4 colline-scrollspeed'>" . bc_input("phpset[contsetclass][scrollspeed]", ($arrSet[scrollspeed] ? $arrSet[scrollspeed] : 800), "Скорость смены (ms)") . "</div>";
            $setBl .= "<div class='colline colline-4 colline-autoplay'>" . bc_input("phpset[contsetclass][autoplay]", $arrSet['autoplay'], "Автопрокрутка (ms)") . "</div>";

            $arrayEffect = array(
                '' => 'none',
                'fadeIn|fadeOut' => 'fadeIn',
                'fadeInDown|fadeOut' => 'fadeInDown',
                'fadeInDownBig|fadeOut' => 'fadeInDownBig',
                'fadeInLeft|fadeOut' => 'fadeInLeft',
                'fadeInLeftBig|fadeOut' => 'fadeInLeftBig',
                'fadeInRight|fadeOut' => 'fadeInRight',
                'fadeInRightBig|fadeOut' => 'fadeInRightBig',
                'fadeInUp|fadeOut' => 'fadeInUp',
                'fadeInUpBig|fadeOut' => 'fadeInUpBig',
                'rotateIn|fadeOut' => 'rotateIn',
                'rotateInDownLeft|fadeOut' => 'rotateInDownLeft',
                'rotateInDownRight|fadeOut' => 'rotateInDownRight',
                'rotateInUpLeft|fadeOut' => 'rotateInUpLeft',
                'rotateInUpRight|fadeOut' => 'rotateInUpRight',
                'zoomIn|zoomOut' => 'fadeOut',
                'zoomInDown|fadeOut' => 'zoomInDown',
                'zoomInLeft|fadeOut' => 'zoomInLeft',
                'zoomInRight|fadeOut' => 'zoomInRight',
                'zoomInUp|fadeOut' => 'zoomInUp',
                'slideInDown|fadeOut' => 'slideInDown',
                'slideInLeft|fadeOut' => 'slideInLeft',
                'slideInRight|fadeOut' => 'slideInRight',
                'slideInUp|fadeOut' => 'slideInUp',
                'rollIn|fadeOut' => 'rollIn',
                'flipInX|fadeOut' => 'flipInX',
                'flipInY|fadeOut' => 'flipInY',
                'jackInTheBox|fadeOut' => 'jackInTheBox'
            );
            $setBl .= "<div class='colheap' data-jsopenmain='settCart'>";
            $setBl .= "<h4 data-jsopen='settBannerText'>Настройка текстов</h4>";
            $setBl .= "<div data-jsopenthis='settBannerText'>";
            $setBl .= "<div class='colline colline-4 colline-bannerNameSize'>" . bc_input("phpset[contsetclass][bannerNameSize]", $arrSet["bannerNameSize"], "Размер заголовка (px)") . "</div>";
            $setBl .= "<div class='colline colline-4 colline-bannerNameEffect'>" . bc_select("phpset[contsetclass][bannerNameEffect]", getOptionsFromArray($arrayEffect, $arrSet[bannerNameEffect]), "Эффект появления заголовка", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-4 colline-bannerTextSize'>" . bc_input("phpset[contsetclass][bannerTextSize]", $arrSet["bannerTextSize"], "Размер текста (px)") . "</div>";
            $setBl .= "<div class='colline colline-4 colline-bannerTextEffect'>" . bc_select("phpset[contsetclass][bannerTextEffect]", getOptionsFromArray($arrayEffect, $arrSet[bannerTextEffect]), "Эффект появления текст", "class='ns'") . "</div>";
            $setBl .= "<div class='colline colline-5 colline-bannerAlign'>" . bc_align("phpset[contsetclass][bannerAlign]", $arrSet["bannerAlign"], "Выравнивание") . "</div>";
            $setBl .= "</div>";
            $setBl .= "</div>";
        }
    }
    if ($contenttype == 2) { // настройки меню
        // типы меню
        $menuTplArr = array(
            31 => "Плитки",
            1 => "Горизонтальное #1",
            2 => "Горизонтальное #2",
            20 => "Вертикальное",
            30 => "Списки",
            40 => "Кнопка (выпадашка) #1",
            41 => "Кнопка (выпадашка) #2",
            42 => "Кнопка пред. раздела #1",
        );
        $menuTplOpt = getOptionsFromArray($menuTplArr, $arrSet['menutpl']);
        $setBl .= "<div class='colline colline-3 colline-menutpl'>" . bc_select("phpset[contsetclass][menutpl]", $menuTplOpt, "Вариант вывода меню", "class='ns'") . "</div>";
        # Шаблоны карточек (дублируеться)
        $template = $db->get_var("SELECT data FROM Bitcat WHERE `key` = 'sizesub_template'");
        if ($template) {
            $templateOption = getOptionsFromArray(json_decode($template, 1), $arrSet[template_block]);
            $setBl .= "<div class='colline colline-3 colline-template_block menu-show menu-show-31 " . ($arrSet[menutpl] == 31 ? '' : 'none') . "'>" . bc_select("phpset[contsetclass][template_block]", $templateOption, "Шаблон карточки", "class='ns'") . "</div>";
        }
        // Сортировка разделов
        $sortArr = array("" => "По приоритету", 1 => "По алфавиту");
        $sortOpt = getOptionsFromArray($sortArr, $arrSet[sortsub]);
        $setBl .= "<div class='colline colline-3 colline-sortsub'>" . bc_select("phpset[contsetclass][sortsub]", $sortOpt, "Сортировка разделов", "class='ns'") . "</div>";
        // Раскрываюшееся
        $levelArr = array("" => "нет", 1 => "1 уровень", 2 => "2 уровня", 3 => "2 уровня с картинками");
        $levelOpt = getOptionsFromArray($levelArr, $arrSet[dropmenu]);
        $setBl .= "<div class='colline colline-3 colline-dropmenu menu-show menu-show-1 menu-show-2 menu-show-20 menu-show-40 menu-show-41 " . (in_array($arrSet[menutpl], array(1, 2, 20, 40, 41)) ? '' : 'none') . "'>" . bc_select("phpset[contsetclass][dropmenu]", $levelOpt, "Выпадашка", "class='ns'") . "</div>";

        // Размеры карточек (дублируеться)
        $size = getsizeclass(array(
            "id" => "sub",
            "sizeitem_select" => ($arrSet["sizeitem_select"] ? $arrSet["sizeitem_select"] : $setting["sizesub_select"]),
            "sizeitem" => ($arrSet["sizeitem"] ? $arrSet["sizeitem"] : $setting["sizesub"]),
            "sizeitem_counts" => ($arrSet["sizeitem_counts"] ? $arrSet["sizeitem_counts"] : ""),
            "sizeitem_margin" => (is_numeric($arrSet["sizeitem_margin"]) ? $arrSet["sizeitem_margin"] : $setting["sizesub_margin"]),
            "sizeitem_image_select" => ($arrSet["sizeitem_image_select"] ? $arrSet["sizeitem_image_select"] : $setting["sizesub_image_select"]),
            "sizeitem_image" => ($arrSet["sizeitem_image"] ? $arrSet["sizeitem_image"] : $setting["sizesub_image"]),
            "sizeitem_fit" => ($arrSet["sizeitem_fit"] ? $arrSet["sizeitem_fit"] : $setting["sizesub_fit"])
        ));

        $subdirLvlList = "";
        foreach ($db->get_results("SELECT * from Classificator_subdirLvl where Checked = 1 ORDER BY subdirLvl_Priority", ARRAY_A) as $field) {
            $subdirLvlList .= "1111<option " . ($field['subdirLvl_ID'] == $arrSet['level'] ? "selected" : NULL) . " value='{$field['subdirLvl_ID']}'>{$field['subdirLvl_Name']}</option>";
        }
        $setBl .= "<div class='colline colline-3 colline-menulevel menu-show menu-show-31 " . ($arrSet[menutpl] == 31 ? '' : 'none') . "'>" . bc_select("phpset[contsetclass][level]", $subdirLvlList, "Уровень подразделов", "class='ns'") . "</div>";
        $setBl .= "<div class='colheap menu-show menu-show-31 " . ($arrSet[menutpl] == 31 ? '' : 'none') . "' data-jsopenmain='settCart'>";
        $setBl .= "<h4 data-jsopen='settCart'>Настройка размеров объектов</h4>";
        $setBl .= "<div data-jsopenthis='settCart' class='" . (!$arrSet[sizehave] && !$arrSet[masonry] ? "none" : "") . "'>";
        $setBl .= "<div class='colline colline-1 colline-masonry' data-jsopen='settMasonry'>" . bc_checkbox("phpset[contsetclass][masonry]", 1, "Включить Masonry", $arrSet[masonry]) . "</div>";
        $setBl .= "<div class='" . (!$arrSet[masonry] ? "none" : "") . "' data-jsopenthis='settMasonry'>";
        $setBl .= "<div class='colline colline-3 colline-type_masonry'>" . bc_select("phpset[contsetclass][type_masonry]", getOptionSelect("type_masonry", $arrSet[type_masonry]), "Порядок размеров", "class='ns'") . "</div>";
        $setBl .= "</div>";

        $setBl .= "<div class='colline colline-1' " . $size["sizeitem_counts"] . " data-jsopen='settCartSize'>" . bc_checkbox("phpset[contsetclass][sizehave]", 1, "Индивидуальное отображение в блоке", $arrSet[sizehave]) . "</div>";
        $setBl .= "<div class='" . (!$arrSet[sizehave] ? "none" : "") . "' data-jsopenthis='settCartSize'>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_select'>" . bc_select("phpset[contsetclass][sizeitem_select]", $size["sizeitem_select"], "Размер карточек", "class='ns'") . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem'>" . bc_input("phpset[contsetclass][sizeitem]", $size["sizeitem"] . "px", "px") . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_counts'>" . bc_multi_line("phpset[contsetclass][sizeitem_counts]", $size["sizeitem_counts"], "", 2) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_margin'>" . bc_input("phpset[contsetclass][sizeitem_margin]", $size["sizeitem_margin"], "Отступ справа (px)") . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_image_select'>" . bc_select("phpset[contsetclass][sizeitem_image_select]", $size["sizeitem_image_select"], "Пропорции изображения", "class='ns'") . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_image'>" . bc_input("phpset[contsetclass][sizeitem_image]", $size["sizeitem_image"] . "%", "%") . "</div>";
        $setBl .= "<div class='colline colline-3 colline-sizeitem_fit'>" . bc_select("phpset[contsetclass][sizeitem_fit]", $size["sizeitem_fit"], "Отображение", "class='ns'") . "</div>";
        $setBl .= "</div>";
        $setBl .= "<div class='colline colline-1 colline-animate' data-jsopen='settAnimate'>" . bc_checkbox("phpset[contsetclass][animate]", 1, "Анимация", $arrSet[animate]) . "</div>";
        $setBl .= "<div class='" . (!$arrSet[animate] ? "none" : "") . "' data-jsopenthis='settAnimate'>";
        $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_title]", getOptionSelect("animate_title", $arrSet[animate_title]), "Анимация заголовка", "class='ns'") . "</div>";
        $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_text]", getOptionSelect("animate_text", $arrSet[animate_text]), "Анимация описания", "class='ns'") . "</div>";
        $setBl .= "<div class='colline colline-3'>" . bc_select("phpset[contsetclass][animate_items]", getOptionSelect("animate_items", $arrSet[animate_items]), "Анимация объектов", "class='ns'") . "</div>";
        $setBl .= "</div>";
        $setBl .= "</div>";
        $setBl .= "</div>";
        // Прокрутка карточек (дублируеться)
        $sliderNavOption = array(
            0 => "Выключены",
            1 => "По бокам",
            2 => "В углу",
            3 => "По краям заголовка"
        );
        $sliderNav = getOptionsFromArray($sliderNavOption, $arrSet[scrollNav]);
        $setBl .= "<div class='colheap menu-show menu-show-31 " . ($arrSet[menutpl] == 31 ? '' : 'none') . " " . ($arrSet[scrolling] ? "active" : "") . "' data-jsopenmain='scrollSett'>
                        <h4 data-jsopen='scrollSett'>Прокрутка</h4>
                        <div data-jsopenthis='scrollSett' " . (!$arrSet[scrolling] ? "class='none'" : "") . ">
                            <div class='colline colline-4 colline-scrolling'>" . bc_checkbox("phpset[contsetclass][scrolling]", 1, "Прокрутка<br>объектов", $arrSet[scrolling], "class='switch-twoline'") . "</div>
                            <div class='colline colline-4 colline-scrollNav'>" . bc_select("phpset[contsetclass][scrollNav]", $sliderNav, "Стрелочки (ms)", "class='ns'") . "</div>
                            <div class='colline colline-4 colline-scrollDots'>" . bc_checkbox("phpset[contsetclass][scrollDots]", 1, "Пагинация", $arrSet[scrollDots]) . "</div>
                            <div class='colline colline-4 colline-scrollspeed'>" . bc_input("phpset[contsetclass][scrollspeed]", $arrSet[scrollspeed], "Скорость прокрутки") . "</div>
                            <div class='colline colline-4 colline-scrollbutcol'>" . bc_color("settings[scrollbutcol]", $arrSet['scrollbutcol'], "Фон") . "</div>
                            <div class='colline colline-4 colline-scrollbutfont'>" . bc_color("settings[scrollbutfont]", $arrSet['scrollbutfont'], "Иконка") . "</div>
                            <div class='colline colline-4 colline-autoplay'>" . bc_input("phpset[contsetclass][autoplay]", $arrSet['autoplay'], "Автопрокрутка (ms)") . "</div>
                        </div>
                    </div>";
        // Настройки меню (кнока-выподашка)
        $setBl .= "<div class='colheap menu-show menu-show-40 menu-show-41 " . ($arrSet[menutpl] == 40 || $arrSet[menutpl] == 41 ? '' : 'none') . "' data-jsopenmain='menuBtn'>
                        <h4 data-jsopen='menuBtn'>Настройки меню (кнока-выподашка)</h4>
                        <div data-jsopenthis='menuBtn' class='none'>
                            <div class='colline colline-2 colline-menubtnclick'>" . bc_checkbox("phpset[contsetclass][menubtnclick]", 1, "Раскрывать по клику", $arrSet[menubtnclick]) . "</div>
                        </div>
                    </div>";
        // Настройки горизонтального меню
        $setBl .= "<div class='colheap menu-show menu-show-1 menu-show-2 " . ($arrSet[menutpl] == 1 || $arrSet[menutpl] == 2 ? '' : 'none') . "' data-jsopenmain='menuGorizont'>
                        <h4 data-jsopen='menuGorizont'>Настройки горизонтального меню</h4>
                        <div data-jsopenthis='menuGorizont' class='none'>
                            <div class='colline colline-2 colline-itemsinmenu'>" . bc_checkbox("phpset[contsetclass][itemsinmenu]", 1, "Вывод товаров в меню", $arrSet[itemsinmenu]) . "</div>
                            <div class='colline colline-2 colline-punktwidth100'>" . bc_checkbox("phpset[contsetclass][punktwidth100]", 1, "Меню на всю ширину", $arrSet[punktwidth100]) . "</div>
                        </div>
                    </div>";
        // Настройки вертикального меню
        $devidertplArr = array(1 => "Пунктирная линия", 2 => "Сплошная линия");
        $devidertplOpt .= "<option value=''>- не выбран -</option>";
        foreach ($devidertplArr as $k => $v) {
            $devidertplOpt .= "<option value='$k' " . ($k == $arrSet[devidertpl] ? "selected" : "") . "> $v</option>";
        }
        $setBl .= "<div class='colheap menu-show menu-show-20 " . ($arrSet[menutpl] == 20 ? '' : 'none') . "' data-jsopenmain='menuVertical'>
                        <h4 data-jsopen='menuVertical'>Настройки вертикального меню</h4>
                        <div data-jsopenthis='menuVertical' class='none'>
                            <div class='colline colline-2 colline-itemsinmenu'>" . bc_checkbox("phpset[contsetclass][itemsinmenu]", 1, "Вывод товаров в меню", $arrSet[itemsinmenu]) . "</div>
                            <div class='colline colline-2 colline-showicon'>" . bc_checkbox("phpset[contsetclass][showicon]", 1, "Иконки", $arrSet[showicon]) . "</div>
                            <div class='colline colline-1 colline-devidertpl'>" . bc_select("phpset[contsetclass][devidertpl]", $devidertplOpt, "Разделитель разделов", "class='ns'") . "</div>
                        </div>
                    </div>";
        // Настройки меню плиток
        $setBl .= "<div class='colheap menu-show menu-show-31 " . ($arrSet[menutpl] == 31 ? '' : 'none') . "' data-jsopenmain='menuObj'>
                        <h4 data-jsopen='menuObj'>Настройки меню плиток</h4>
                        <div data-jsopenthis='menuObj' class='none'>
                            <div class='colline colline-4 colline-countmenu'>" . bc_input("phpset[contsetclass][countmenu]", $arrSet[countmenu], "Кол-во разделов") . "</div>
                        </div>
                    </div>";
    }
    if ($contenttype == 3) { // настройки контактов
        $setBl .= "<div class='colline colline-3 colline-mailform'>" . bc_checkbox("phpset[contsetclass][mailform]", 1, "Написать нам", $arrSet[mailform]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-callform'>" . bc_checkbox("phpset[contsetclass][callform]", 1, "Обратный звонок", $arrSet[callform]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-topmap'>" . bc_checkbox("phpset[contsetclass][topmap]", 1, "Показывать карту", $arrSet[topmap]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-showtimework'>" . bc_checkbox("phpset[contsetclass][showtimework]", 1, "Время работы", $arrSet[showtimework]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-targeting'>" . bc_checkbox("phpset[contsetclass][targeting]", 1, "Выбор города", $arrSet[targeting]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-reglink'>" . bc_checkbox("phpset[contsetclass][reglink]", 1, "Форма авторизации", $arrSet[reglink]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-reglink'>" . bc_checkbox("phpset[contsetclass][favlink]", 1, "Избранные товары", $arrSet[favlink]) . "</div>";
        $setBl .= "<div class='colline colline-3 colline-reglink'>" . bc_checkbox("phpset[contsetclass][sravlink]", 1, "Сравнение товаров", $arrSet[sravlink]) . "</div>";
        # вывести выбор языка
        if ($arrSet[langSelect] || $setting[language]) $setBl .= "<div class='colline colline-3 colline-langSelect'>" . bc_checkbox("phpset[contsetclass][langSelect]", 1, "Выбор языка", $arrSet[langSelect]) . "</div>";

        $setBl .= "<div class='colblock'>
                        <h4>Телефон</h4>
                        <div class='colline colline-1 colline-showphones'>" . bc_checkbox("phpset[contsetclass][showphones]", 1, "Показать телефоны", $arrSet[showphones]) . "</div>
                        <div class='colline colline-4 colline-phonekod1'>" . bc_input("phpset[contsetclass][phonekod1]", $arrSet[phonekod1], "Код (первый)") . "</div>
                        <div class='colline colline-4 colline-phone1'>" . bc_input("phpset[contsetclass][phone1]", $arrSet[phone1], "Номер (первый)") . "</div>
                        <div class='colline colline-4 colline-phonekod2'>" . bc_input("phpset[contsetclass][phonekod2]", $arrSet[phonekod2], "Код (второй)") . "</div>
                        <div class='colline colline-4 colline-phone2'>" . bc_input("phpset[contsetclass][phone2]", $arrSet[phone2], "Номер (второй)") . "</div>
                    </div>";
        $setBl .= "<div class='colline colline-height colline-phonetext'>" . bc_textarea("phpset[contsetclass][phonetext]", str_replace("rn", "\n", $arrSet['phonetext']), "Текст") . "</div>";
    }
    if ($contenttype == 4) { // настройки контактов
        $setBl .= "<div class='colline colline-height colline-bottomtext'>" . bc_textarea("phpset[contsetclass][bottomtext]", str_replace("rn", "\n", $arrSet['bottomtext']), "Текст") . "</div>";
    }
    if ($contenttype == 6) { // модули
        $options = checkModule("options", $arrSet['module']);
        $setBl .= "<div class='colline colline-3 colline-module'>" . bc_select("phpset[contsetclass][module]", $options, "Список модулей", "class='ns'") . "</div>";
    }
    if ($contenttype == 7) { // настройки хлебных крошек
        $xlebarray = ['1' => 'Тип 1', '2' => 'Тип 2'];
        $xlebselect = getOptionsFromArray($xlebarray, $arrSet['xlebtype']);
        $setBl .= "<div class='colline colline-3'>" . bc_select("settings[xlebtype]", $xlebselect, "Тип", "class='ns'") . "</div>";
    }
    return $setBl;
}


# Размеры объектов из настроек сайта
function getsizeclass($p)
{
    global $db, $catalogue, $setting;

    $size = $db->get_results("SELECT `key`, `value`, `data` FROM Bitcat WHERE `key` like '%size{$p[id]}%'", ARRAY_A);
    foreach ($size as $key => $value) $size[$value['key']] = $size[$key];

    $sizeitem_select_data = orderArray($size["size{$p[id]}_select"]["data"]);
    $p['sizeitem_select'] = $p['sizeitem_select'] ? $p['sizeitem_select'] : $size["size{$p[id]}_select"]["value"];
    foreach ($sizeitem_select_data as $k => $v) $sizeitem_select .= "<option value='{$k}' " . ($k == $p['sizeitem_select'] ? "selected" : "") . ">{$v}</option>";

    $sizeitem_image_select_data = orderArray($size["size{$p[id]}_image_select"]["data"]);
    $p['sizeitem_image_select'] = $p['sizeitem_image_select'] ? $p['sizeitem_image_select'] : $size["size{$p[id]}_image_select"]["value"];
    foreach ($sizeitem_image_select_data as $k => $v) $sizeitem_image_select .= "<option value='{$k}' " . ($k == $p['sizeitem_image_select'] ? "selected" : "") . ">{$v}</option>";

    $sizeitem_fit_data = orderArray($size["size{$p[id]}_fit"]["data"]);
    $p['sizeitem_fit'] = $p['sizeitem_fit'] ? $p['sizeitem_fit'] : $size["size{$p[id]}_fit"]["value"];
    foreach ($sizeitem_fit_data as $k => $v) $sizeitem_fit .= "<option value='{$k}' " . ($k == $p['sizeitem_fit'] ? "selected" : "") . ">{$v}</option>";

    return array(
        "sizeitem_select" => $sizeitem_select,
        "sizeitem" => $p[sizeitem],
        "sizeitem_margin" => $p[sizeitem_margin],
        "sizeitem_image_select" => $sizeitem_image_select,
        "sizeitem_image" => $p[sizeitem_image],
        "sizeitem_fit" => $sizeitem_fit,
        "sizeitem_counts" => $p[sizeitem_counts]
    );
}

/**
 * Опции select'ов
 * @param string $name
 * @param string $val Значения выбраного option
 *
 * @return string  Возвращяет HTML options tags
 */

function getOptionSelect($name, $val)
{
    if ($name == "type_masonry") return getOptionsFromArray(array("" => "Свой порядок", "1" => "Порядок #1", "2" => "Порядок #2", "3" => "Порядок #3"), $val);

    $types = ["animate_title", "animate_text", "animate_items"];
    if (in_array($name, $types)) {
        $array = ["" => "нет", "fadeIn" => "Прозрачность", "fadeInDown" => "Прозрачность (сверху)", "fadeInUp" => "Прозрачность (снизу)", "fadeInLeft" => "Прозрачность (слева)", "fadeInRight" => "Прозрачность (справа)", "zoomIn" => "Прозрачность (увеличение)"];
        return getOptionsFromArray($array, $val);
    }
}

# вывод coporight в блоке
function getcoporight($arrSet)
{
    global $db, $catalogue, $citylistselect, $wincityname, $cityphone, $setting, $setting_texts, $AUTH_USER_ID, $current_catalogue;
    $reslt = "<div class='copyright-block'>
                " . ($setting['counter'] ? "<div class=counter>{$setting['counter']}</div>" : NULL) . "
                    <div class='copyright'>
                        <div class='copy1'>" . date("Y") . " © “" . $current_catalogue['Catalogue_Name'] . "”</div>
                        <div class='copy2'>
                            " . ($arrSet['bottomtext'] ? "<span class='sitebottomtext'>" . $arrSet['bottomtext'] . "</span>" : NULL) . "
                            <span class='sitemaplink'>" . ($arrSet['bottomtext'] ? " | " : "") . "<a href='/system/politika/'>" . ($setting_texts['site_politika']['checked'] ? $setting_texts['site_politika']['name'] : getLangWord('site_politika', "Политика конфиденциальности")) . "</a> | <a href='/index/sitemap/'>" . ($setting_texts['sitemap']['checked'] ? $setting_texts['sitemap']['name'] : getLangWord('sitemap', "Карта сайта")) . "</a></span>
                        </div>
                    </div>
                </div>";
    return $reslt;
}
# вывод разработчика в блоке
function getdev($arrSet)
{
    global $db, $catalogue, $citylistselect, $wincityname, $cityphone, $setting, $setting_texts, $AUTH_USER_ID, $current_catalogue, $HTTP_FILES_PATH, $DEVELOPER_URL;
    $url = $setting['createrLink'] ? $setting['createrLink'] : $DEVELOPER_URL;
    if ($setting['powerseo_super']) {
        $nofollow = "rel='nofollow'";
        $noindex1 = "<!--noindex-->";
        $noindex2 = "<!--/noindex-->";
    }
    $v_img = $current_catalogue['colorid'] ? "?v={$current_catalogue[colorid]}" : "";
    $reslt = $noindex1 . "<div class='devK' " . ($setting['createrLogo'] ? "style='background-image:url({$HTTP_FILES_PATH}{$setting['createrLogo']}{$v_img});'" : "") . ">
                  " . ($setting['createrText'] && !stristr($setting['createrText'], "korzill") ? "
                  <div class='devK-name'><a " . $nofollow . " target=_blank href='{$url}'>" . $setting['createrText'] . "</a></div>
                  " : "
                  <div class='devK-name1'></div>
                  <div class='devK-text'>
                      <a " . $nofollow . " target=_blank href='{$url}'>" . getLangWord("dev_creat_1", (is_mobile() ? "создание приложений" : "создание сайтов")) . "</a>
                      <span>" . getLangWord("dev_creat_2", "и") . "</span>
                      <a " . $nofollow . " target=_blank class='logo' href='{$url}'>" . getLangWord("dev_creat_3", "продвижение сайтов") . "</a>
                  </div>
                  ") . "
              </div>" . $noindex2;
    return $reslt;
}

# вывод контактной информации в блоке
function smallcontacts($arrSet, $mobile = false)
{
    global $db, $catalogue, $wincityname, $cityphone, $setting, $setting_texts, $AUTH_USER_ID, $cityname, $cityvars, $city_link_a, $langs, $current_catalogue, $current_user, $inside_admin, $bitcat;
    if ($arrSet[topmap] || $arrSet[showtimework]) {
        $targeting = targeting("", true);
        $contactid = $db->get_row("select a.Message_ID as id, a.time from Message2012 as a, Subdivision as b where a.Checked = 1 AND a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue' AND  b.Hidden_URL like '%/contacts/%' " . ($targeting ? "AND {$targeting}" : "") . " ORDER BY a.Priority LIMIT 0,1");
        if (!$contactid) $contactid = $db->get_row("select a.Message_ID as id, a.time from Message2012 as a, Subdivision as b where a.Checked = 1 AND a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue' " . ($targeting ? "AND {$targeting}" : "") . " ORDER BY a.Priority LIMIT 0,1");
    }

    if (!empty($contactid->time)) {
        $timeArr = orderArray($contactid->time);
        $isJson = (json_last_error() === JSON_ERROR_NONE ? true : false);
        $contactid->time = str_replace(
            '\\n',
            "\n",
            ($isJson ? $timeArr['text'] : $contactid->time)
        );
    }

    if ($setting['language']) {
        # выбор языка
        foreach ($langs[lang] as $key => $lng) {
            $link = ($key != $langs['main']['keyword'] ? "{$key}." : "") . $current_catalogue[Domain] . $_SERVER[REQUEST_URI];
            $langselect .= "<option " . ($key == $langs['langnow'] ? "selected" : "") . " data-link='//{$link}'>" . ($setting['language_select'] == 2 ? $lng['name'] : $key) . "</option>";
        }
        $langselect = "<select class='select-style'>{$langselect}</select>";
    }

    $kod1 = $cityphone['targetcode'] ?: $arrSet['phonekod1'];
    $phone1 = $cityphone['targetphone'] ?: $arrSet['phone1'];
    $kod2 = (!$cityphone['targetcode2'] ? $arrSet['phonekod2'] : $cityphone['targetcode2']);
    $phone2 = (!$cityphone['targetphone'] ? $arrSet['phone2'] : $cityphone['targetphone2']);
    if ($phone1 && !$phone2 && !$arrSet[mailform] && !$arrSet[callform] && !$arrSet[targeting] && !$arrSet[reglink]) $phoneIcon = "icons iconsCol i_tel";

    $keyLinks = $mobile ? "mobl" : "links";
    $keyLink = $mobile ? "mobl" : "link";
    $keyLnk = $mobile ? "mobl" : "lnk";
    $keyLp = $mobile ? "mobl" : "lp";

    $userName = ($current_user['ForumName'] ? $current_user['ForumName'] : $current_user['Login']);

    $hidephone = $setting['hidephone'] ? '' : 'hidephone ';

    $result = "";
    if ($arrSet['showtimework'] || $arrSet['phonetext']) {
        $result .= "<div class='tel_{$keyLnk}_text'>" . ($arrSet['phonetext'] ? str_replace("rn", "<br>", replace_lang($arrSet['phonetext'])) : $contactid->time) . "</div>";
    }
    if ($arrSet['showphones'] && ($phone1 || $phone2)) {
        $result .= "<div class='{$hidephone}tel_{$keyLp}_item {$phoneIcon}'><a href='tel:" . clearPhone($kod1 . " " . $phone1) . "' id='link-tel-1' data-metr='headphone'>{$kod1} {$phone1}</a>" .
            ($hidephone ? "<span class='show_phone' data-metr='showphone'>Показать телефон</span>" : null) .
            ($phone2 ? "<span class='semicolon'>;</span>" : null) . "
                    </div>
                    " . ($phone2 ? "<div class='{$hidephone}tel_{$keyLp}_item'>
                                    <a href='tel:" . clearPhone($kod2 . " " . $phone2) . "' id='link-tel-2' data-metr='headphone'>{$kod2} {$phone2}</a>" .
                ($hidephone ? "<span class='show_phone' data-metr='showphone'>Показать телефон</span>" : "") . "
                                 </div>" : NULL);
    }

    $links = '';
    if ($arrSet['mailform']) {
        // $feedbackAddLink = nc_infoblock_path($db->get_var("SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE `Catalogue_ID` = {$catalogue} AND `EnglishName` = 'feedback' LIMIT 0,1"), 'add');
        $feedbackAddLink = '/feedback/add_feedback.html';
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_sendmail'>
                    <a href='{$feedbackAddLink}' data-lc-href='{$feedbackAddLink}?isNaked=1' id='link-feedback' title='" . ($setting_texts['link_mail']['checked'] ? $setting_texts['link_mail']['name'] : getLangWord('link_mail', "Напишите нам")) . "' data-rel='lightcase' data-maxwidth='380' data-groupclass='feedback modal-form' data-metr='mailtoplink'>" . ($setting_texts['link_mail']['checked'] ? $setting_texts['link_mail']['name'] : getLangWord('link_mail', "Напишите нам")) . "</a>
                 </div>";
    }
    if ($arrSet['callform']) {
        // $callmeAddLink = nc_infoblock_path($db->get_var("SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE `Catalogue_ID` = {$catalogue} AND `EnglishName` = 'callme' LIMIT 0,1"), 'add');
        $callmeAddLink = '/callme/add_callme.html';
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_call'>
                        <a href='{$callmeAddLink}' data-lc-href='{$callmeAddLink}?isNaked=1' id='link-callme' title='" . ($setting_texts['link_call']['checked'] ? $setting_texts['link_call']['name'] : getLangWord('link_call', "Обратный звонок")) . "' data-rel='lightcase' data-maxwidth='390' data-groupclass='callme modal-form' data-metr='calltoplink'>" . ($setting_texts['link_call']['checked'] ? $setting_texts['link_call']['name'] : getLangWord('link_call', "Обратный звонок")) . "</a>
                    </div>";
    }
    if ($arrSet['targeting'] && $setting['targeting']) {
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_city'>" . getCityLink() . "</div>";
    }
    if ($arrSet['reglink'] && $setting['allowreg']) {
        if (function_exists('user_auth_link_smallcontacts')) {
            $links .= user_auth_link_smallcontacts($userName, $keyLink);
        } else {
            $links .= "<div class='regper_{$keyLink} iconsCol icons i_user2'>
                    " . ($AUTH_USER_ID ? "<a href='/profile/' title='{$userName}'>{$userName}</a>" : "<a href='/profile/' data-lc-href='/profile/?isNaked=1' title='Вход' data-rel='lightcase' data-maxwidth='320' data-groupclass='login'>" . ($setting_texts['auth_text']['checked'] ? $setting_texts['auth_text']['name'] : getLangWord('auth_text', "Авторизация")) . "</a>") . "
                </div>";
        }
    }

    if ($arrSet['favlink']) {
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_favorite'>
                        <a href='/favorites/' id='link-favorite' title='" . ($setting_texts['link_favorite']['checked'] ? $setting_texts['link_favorite']['name'] : getLangWord('link_favorite', "Избранное")) . "'>" . ($setting_texts['link_favorite']['checked'] ? $setting_texts['link_favorite']['name'] : getLangWord('link_favorite', "Избранное")) . " <span>" . count(get_favorites()) . "</span></a>
                    </div>";
    }

    if ($arrSet['sravlink']) {
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_comparison'>
                        <a href='/comparison/' id='link-comparison' title='" . ($setting_texts['link_comparison']['checked'] ? $setting_texts['link_comparison']['name'] : getLangWord('link_comparison', "Сравнение товаров")) . "'>" . ($setting_texts['link_comparison']['checked'] ? $setting_texts['link_comparison']['name'] : getLangWord('link_comparison', "Сравнение товаров")) . "<span></span></a>
                    </div>";
    }

    if ($arrSet['topmap'] && $contactid) {
        $links .= "<div class='regper_{$keyLink} iconsCol icons i_map'>
                    <a title='" . ($setting_texts['proezd_map']['checked'] ? $setting_texts['proezd_map']['name'] : getLangWord('link_map', "Схема проезда")) . "'  id='link-map' data-rel='lightcase' href='" . nc_message_link($contactid->id, "2012") . "?isNaked=1' data-type='iframe' data-maxheight='600' data-groupclass='modal-obj'>" . ($setting_texts['proezd_map']['checked'] ? $setting_texts['proezd_map']['name'] : getLangWord('link_map', 'Схема проезда')) . "</a>
                </div>";
    }
    if ($arrSet['langSelect'] && $langselect) {
        $links .= "<div class='regper_{$keyLink} lang-list'>{$langselect}</div>";
    }

    if ($links) $result .= "<div class='tel_{$keyLnk}_btn'>{$links}</div>";
    if ($result) $result = "<div class='cb tel_{$keyLinks}'>{$result}</div>";

    return $result;
}

# очистка телефона
function clearPhone($phone)
{
    return preg_replace("/[^+0-9]/", "", $phone);
}

# получение полей товара
function tovarByID($msgID, $fields = '', $link = '')
{
    global $db, $catalogue;
    if (!$fields) $fields = 'name, Keyword, Subdivision_ID, Sub_Class_ID, price, Checked';
    if ($msgID > 0) {
        $tovar = $db->get_row("SELECT {$fields} FROM Message2001 where Message_ID = '$msgID'", ARRAY_A);
        if ($link) $tovar['url'] = nc_message_link($msgID, 2001);
    }
    return $tovar;
}

# обновление оценок товара
function rateUpdate($mesCom, $msgID = '')
{
    global $db, $catalogue;
    if (!$msgID) $msgID = $db->get_var("select tovar from Message2054 where Message_ID = '{$mesCom}'");
    if ($msgID > 0) {
        $ratecount = $db->get_var("select count(Message_ID) as cnt from Message2054 where tovar = '{$msgID}' AND Checked = 1");
        $ratesum = $db->get_var("select sum(rate) as sum from Message2054 where tovar = '{$msgID}' AND Checked = 1");
        $rate = round05($ratesum / $ratecount, 1);
        $db->query("update Message2001 set rate = '{$rate}', ratecount = '{$ratecount}' where Message_ID = '{$msgID}'");
        return $rate . " " . $ratecount;
    }
}

# округление до 0.5
function round05($num, $p = '')
{
    $b = $num - floor($num);
    if ($b >= 0.35 && $b < 0.65) $r = floor($num) + 0.5;
    if ($b < 0.35 || $b >= 0.65) $r = round($num);
    if (!$p) return $r;
    else return str_replace(",", ".", $r);
}

# получить номер инфоблока по номеру раздела
function cc_by_subID($sub)
{
    global $db, $catalogue;
    return $db->get_var("select Sub_Class_ID from Sub_Class where Subdivision_ID = '$sub' order by Priority LIMIT 0,1");
}




# BB code

function BBcode($var)
{

    $search = array(
        '/\[b\](.*?)\[\/b\]/is',
        '/\[strong\](.*?)\[\/strong\]/is',
        '/\[i\](.*?)\[\/i\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[img\](.*?)\[\/img\]/is',
        '/\[url\](.*?)\[\/url\]/is',
        '/\[url\=(.*?)\](.*?)\[\/url\]/is',
        '/\[br\]/is'
    );

    $replace = array(
        '<strong>$1</strong>',
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<u>$1</u>',
        '<img src="$1" />',
        '<a href="$1">$1</a>',
        '<a href="$1">$2</a>',
        '<br>'
    );


    $var = preg_replace($search, $replace, $var);
    return $var;
}

/* Хэш-функция купиВкредит */
function signMessage($message, $secretPhrase)
{
    $message = $message . $secretPhrase;
    $result = md5($message) . sha1($message);
    for ($i = 0; $i < 1102; $i++) {
        $result = md5($result);
    }
    return $result;
}

/* IP */
function getIP($param = null)
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if ($param == 'office') {
        $allowIP = array("31.13.133.138", "149.126.23.234", "178.206.233.83", "31.13.133.22", "178.204.129.5", "92.255.204.119", "178.206.122.138");
        return in_array($ip, $allowIP) ? true : false;
    }
    return $ip;
}


function pre_dump($data, $USER_ID = ''){
	global $AUTH_USER_ID;
	if($AUTH_USER_ID == $USER_ID || $USER_ID === ''){
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
}
function console_log($data, $key='', $USER_ID = ''){
    (\App\modules\Korzilla\ConsoleLog\ConsoleLog::getInstance())->setLog($data, $key);
}
function setCanonical($url){
    (\App\modules\Korzilla\Canonical\Canonical::getInstance())->setCanonical($url);
}

/* order to file for 1C */
function import1C($date = '', $orderid = '', $is1C = '', $utf8 = '')
{
    global $db, $curSub, $curCC, $reslt, $DOCUMENT_ROOT, $DOCUMENT_ROOT1, $catalogue, $pathInc, $setting, $v1c;

    if (function_exists('import1C_separated')) {
        return import1C_separated($date, $orderid, $is1C); // своя функция
    } else {

        if (!$date) $date = date("Y.m.d");
        $date = addslashes(strip_tags($date));

        $dateA = explode(".", $date);

        if (!$orderid && !$is1C) {
            $ordersSQL = "SELECT a.* from Message2005 as a, Subdivision as b where a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue' AND DATE_FORMAT(a.Created, '%Y.%m.%d')='$date' ORDER BY Message_ID DESC limit 0,15";
        } elseif ($is1C) {
            /*if ($_SERVER[REMOTE_ADDR]=='31.13.133.138') {
                $ordersSQL = "SELECT a.* from Message2005 as a, Subdivision as b where a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue' ORDER BY Message_ID DESC LIMIT 0,10";
            } else {*/
            $ordersSQL = "SELECT a.* from Message2005 as a, Subdivision as b where a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue' AND (a.ShopOrderStatus = '1' OR a.ShopOrderStatus = '' OR a.ShopOrderStatus IS NULL) ORDER BY Message_ID DESC limit 0,15";
            /*}*/
        } else {
            $ordersSQL = "SELECT * from Message2005 where Message_ID = '" . $orderid . "' ORDER BY Message_ID DESC";
        }

        $orders = $db->get_results($ordersSQL, ARRAY_A);



        // орг формы в массив
        $orgArr = $db->get_results("SELECT * from Classificator_org where Checked = 1", ARRAY_A);
        if ($orgArr) {
            foreach ($orgArr as $org1) {
                $idorg = $org1[org_ID];
                $orgs[$idorg] = $org1[org_Name];
            }
        }

        if ($orders) {
            foreach ($orders as $o) {
                $itogSum = $skidkaSum = $delivery = $payment = $customf = NULL;
                unset($items);
                unset($itemsArr);
                unset($totSum);
                unset($u);
                unset($user);
                unset($ia);
                unset($auth);
                unset($orgform);
                unset($items_xml);
                unset($fioArr);

                $customf = orderArray($o['customf'], 'file');
                //print_r($customf);
                $user['fio'] = ($o['fio'] ? $o['fio'] : $customf['fio']['value']);
                $user['phone'] = ($o['phone'] ? $o['phone'] : $customf['phone']['value']);
                $user['email'] = ($o['email'] ? $o['email'] : $customf['email']['value']);
                $user['adres'] = ($o['adres'] ? $o['adres'] : $customf['address']['value']);
                $user['city'] = ($o['city'] ? $o['city'] : $customf['city']['value']);
                $user['id'] = md5($o['phone']);
                $orgform = $o['org'];
                $user['company'] = $o['company'];
                $user['inn'] = $o['inn'];

                if ($o['User_ID'] > 0) {
                    $auth = 1;
                    $u = $db->get_row("select * from User where User_ID= '{$o['User_ID']}'", ARRAY_A);
                    //if ($u[fam] && !$user[fam]) $user[fam] = $u[fam];
                    //if ($u[ForumName] && !$user[name]) $user[name] = $u[ForumName];
                    //if ($u[otch] && !$user[otch]) $user[otch] = $u[otch];
                    //if (($user[fam] || $user[name] || $user[otch]) && !$user[fio]) $user[fio] = trim($user[fam]." ".$user[name]." ".$user[otch]);
                    if ($u['Email'] && !$user['email']) $user['email'] = $u['Email'];
                    if ($u['phone'] && !$user['phone']) $user['phone'] = $u['phone'];
                    if ($u['city'] && !$user['city']) $user['city'] = $u['city'];
                    if ($u['adres'] && !$user['adres']) $user['adres'] = $u['adres'];
                    if ($u['org'] && !$orgform) $orgform = $u['org'];
                    if ($u['company'] && !$user['company']) $user['company'] = $u['company'];
                    if ($u['inn'] && !$user['inn']) $user['inn'] = $u['inn'];
                    $user['id'] = md5(($user['inn'] ? $user['inn'] : $u['phone']));
                }
                if (!$user['id']) $user['id'] = md5($user['fio']);
                if ($user['inn']) $user['inn'] = preg_replace("/[^0-9]/", "", $user['inn']);
                if (!$user['name'] && !$user['otch'] && $user['fio']) {
                    $fioArr = explode(" ", str_replace("  ", " ", $user['fio']));
                    $user['fam'] = $fioArr[0];
                    $user['name'] = $fioArr[1];
                    $user['otch'] = $fioArr[2];
                }

                $itemsArr = orderArray($o['orderlist'], 'file');
                if (count($itemsArr['items']) > 0) {
                    foreach ($itemsArr['items'] as $i) {
                        $ia = $db->get_row("select * from Message2001 where Message_ID= '{$i['id']}'", ARRAY_A);
                        $ia['name'] = str_replace("\r\n", " ", $ia['name']);
                        $ia['name'] = str_replace("\n", " ", $ia['name']);
                        $ia['name'] = str_replace("×", "x", $ia['name']);
                        $ia['name'] = str_replace("\"", "", $ia['name']);
                        $ia['name'] = str_replace("&", "", $ia['name']);
                        $totSum = $totSum + $i['sum'];
                        $items_xml .= '<Товар>
                                <Ид>' . ($ia['code'] ? $ia['code'] : $ia['art']) . '</Ид>
                                <Наименование>' . $ia['name'] . '</Наименование>
                                <БазоваяЕдиница Код="796" НаименованиеПолное="Штука" МеждународноеСокращение="PCE">шт</БазоваяЕдиница>
                                <ЦенаЗаЕдиницу>' . $i['price'] . '</ЦенаЗаЕдиницу>
                                <Количество>' . $i['count'] . '</Количество>
                                <Сумма>' . $i['sum'] . '</Сумма>
                                <ЗначенияРеквизитов>
                                    <ЗначениеРеквизита>
                                        <Наименование>ВидНоменклатуры</Наименование>
                                        <Значение>Товар</Значение>
                                    </ЗначениеРеквизита>
                                    <ЗначениеРеквизита>
                                        <Наименование>ТипНоменклатуры</Наименование>
                                        <Значение>Товар</Значение>
                                    </ЗначениеРеквизита>
                                </ЗначенияРеквизитов>
                        </Товар>
                        ';
                        $items_txt .= "" . str_replace(";", ",", ($ia[code] ? $ia[code] : $ia[art])) . ";" . str_replace(";", ",", $ia[art]) . ";" . str_replace(";", ",", $ia[name]) . ";{$i[price]};{$i[count]};{$i[sum]}\n";
                    }
                }

                $dat = explode(" ", $o['Created']);
                $orderIDArr[] = $o['Message_ID'];

                $itogSum = str_replace(",", ".", ($o['totalSum'] ? $o['totalSum'] : $totSum));
                if (!$setting['kopeik']) $itogSum = ceil($itogSum);



                if ((int)$totSum * 100 > (int)$itogSum * 100) { //скидка
                    $skidkaSum = round(((int)$totSum * 100 - (int)$itogSum * 100) / 100, 2);
                    //$testt = '<test1>'.($totSum*100).'---'.($itogSum*100).'----'.((int)$totSum*100-(int)$itogSum*100).'</test1>';
                    if (!$setting['kopeik']) $skidkaSum = round($skidkaSum);
                }

                $delivery = Class2005::getListName('delivery', $o['delivery'], 'name');
                $deliveryText = Class2005::getListName('delivery', $o['delivery'], 'text');
                $payment = Class2005::getListName('payment', $o['payment'], 'name');

                $docs_xml .= '<Документ>
                    <Ид>' . $o['Message_ID'] . '</Ид>
					' . $testt . '
                    <Номер>' . $o['Message_ID'] . '</Номер>
                    <Дата>' . $dat[0] . '</Дата>
                    <ХозОперация>Заказ товара</ХозОперация>
                    <Роль>Продавец</Роль>
                    <Валюта>RUB</Валюта>
                    <Курс>1</Курс>
                    <Сумма>' . str_replace(",", ".", $itogSum) . '</Сумма>
                    ' . ($skidkaSum > 0 ? "
                    <Скидки>
                        <Скидка>
                            <Наименование>Скидка на заказ</Наименование>
                            <Сумма>" . str_replace(",", ".", $skidkaSum) . "</Сумма>
                            <УчтеноВСумме>false</УчтеноВСумме>
                        </Скидка>
                    </Скидки>" : NULL) . '
                    <Комментарий>Заказ № ' . $o['Message_ID'] . ' с сайта ' . $current_catalogue['Catalogue_Name'] . '
                    Покупатель: ' . ($user['company'] ? trim($orgs[$orgform] . ' ' . $user['company']) : $user['fio']) . '
                    Телефон: ' . $user['phone'] . '
                    E-mail: ' . $user['email'] . '
                    Адрес: ' . trim($user['adres']) . '
                    Город: ' . $user['city'] . '
                    ' . ($user['inn'] ? "ИНН: {$user['inn']}" : NULL) . '
                    Способ доставки: ' . $delivery . ($deliveryText ? " (" . $deliveryText . ")" : NULL) . '
                    Метод оплаты: ' . $payment . '
                    ' . ($o['textcomment'] ? "Комментарий: " . preg_replace('/[^А-Яа-яёЁA-Za-z0-9 !:;\.,\-\+]/iu', '', $o['textcomment']) : NULL) . '</Комментарий>
                    <Контрагенты>
                        <Контрагент>
                            <Ид>' . $user['id'] . '</Ид>
                            <Наименование>' . ($user['company'] ? trim($orgs[$orgform] . ' ' . $user['company']) : $user['fio']) . '</Наименование>
                            <Роль>Покупатель</Роль>
                            <ПолноеНаименование>' . ($user['company'] ? trim($orgs[$orgform] . ' ' . $user['company']) : $user['fio']) . '</ПолноеНаименование>
                            <Фамилия>' . $user['fam'] . '</Фамилия>
                            ' . ($user['name'] ? '<Имя>' . $user['name'] . '</Имя>' : NULL) . '
                            ' . ($user['otch'] ? '<Отчество>' . $user['otch'] . '</Отчество>' : NULL) . '
                            ' . ($user['company'] ? '<Организация>' . $user['company'] . '</Организация>' : NULL) . '
                            ' . ($orgs[$orgform] ? '<ОрганизацияФорма>' . $orgs[$orgform] . '</ОрганизацияФорма>' : NULL) . '
                            ' . ($user['company'] ? '<ОрганизацияПолное>' . trim($orgs[$orgform] . ' ' . $user['company']) . '</ОрганизацияПолное>' : NULL) . '
                            ' . (mb_strlen($user['inn']) == 12 || mb_strlen($user['inn']) == 10 ? '<ИНН>' . $user['inn'] . '</ИНН>' : NULL) . '

                            <АдресРегистрации>
                                ' . ($user[city] && $user[adres] ? '<Представление>' . trim((!mb_stristr($user[adres], $user[city]) ? $user[city] : NULL) . ($user[city] && $user[adres] && !mb_stristr($user[adres], $user[city]) ? "," : "") . $user[adres]) . '</Представление>'
                    : '<Представление>адрес не указан</Представление>') . '
                                ' . ($user[city] ? '<АдресноеПоле>
                                    <Тип>Город</Тип>
                                    <Значение>' . $user[city] . '</Значение>
                                </АдресноеПоле>' : NULL) . '

                                ' . ($user[adres] ? '<АдресноеПоле>
                                    <Тип>Адрес</Тип>
                                    <Значение>' . trim($user[adres]) . '</Значение>
                                </АдресноеПоле>' : NULL) . '
                                <Контакты>
                                    ' . ($user[phone] ? '<Контакт>
                                        <Тип>Телефон Мобильный</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Телефон</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>ТелефонРабочий</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Телефон Рабочий</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>' : NULL) . '

                                    ' . ($user[email] ? '<Контакт>
                                        <Тип>Электронная почта</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Почта</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>E-Mail</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>
                                    ' : NULL) . '
                                </Контакты>
                            </АдресРегистрации>
                            <КонтактнаяИнформация>
                                    ' . ($user[phone] ? '<Контакт>
                                        <Тип>Телефон Мобильный</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>ТелефонРабочий</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Телефон Рабочий</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Телефон</Тип>
                                        <Значение>' . $user[phone] . '</Значение>
                                    </Контакт>' : NULL) . '

                                    ' . ($user[email] ? '<Контакт>
                                        <Тип>Почта</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>Электронная почта</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>
                                    <Контакт>
                                        <Тип>E-Mail</Тип>
                                        <Значение>' . $user[email] . '</Значение>
                                    </Контакт>' : NULL) . '
                            </КонтактнаяИнформация>

                            <Представители>
                                <Представитель>
                                    <Контрагент>
                                        <Отношение>Контактное лицо</Отношение>
                                        ' . ($user[id] ? '<Ид>' . $user[id] . '</Ид>' : NULL) . '
                                        <Наименование>Покупатель</Наименование>
                                    </Контрагент>
                                </Представитель>
                            </Представители>
                        </Контрагент>
                    </Контрагенты>
                    ' . ($dat[1] ? '<Время>' . $dat[1] . '</Время>' : NULL) . '
                    <Товары>
                        ' . $items_xml . '
                    </Товары>
                    <ЗначенияРеквизитов>
                        <ЗначениеРеквизита>
                            <Наименование>Метод оплаты</Наименование>
                            <Значение>' . strip_tags($payment) . '</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Заказ оплачен</Наименование>
                            <Значение>false</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Способ доставки</Наименование>
                            <Значение>' . $delivery . '</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Отменен</Наименование>
                            <Значение>false</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Финальный статус</Наименование>
                            <Значение>false</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Статус заказа</Наименование>
                            <Значение>[N] Принят</Значение>
                        </ЗначениеРеквизита>
                        <ЗначениеРеквизита>
                            <Наименование>Дата изменения статуса</Наименование>
                            <Значение>' . $dat[1] . '</Значение>
                        </ЗначениеРеквизита>
                    </ЗначенияРеквизитов>
                </Документ>
                ';

                $docs_txt = "{$user[fio]};{$user[phone]};" . trim($user[city] . " " . $user[adres]) . ";{$user[email]};;\n\n{$items_txt}";
            }

            $xml = '<?xml version="1.0" encoding="windows-1251"?>
            <КоммерческаяИнформация ВерсияСхемы="2.08" ДатаФормирования="' . str_replace(".", "-", $date) . '">
                ' . $docs_xml . '
            </КоммерческаяИнформация>';
            //if ($pathInc) $order_path = $DOCUMENT_ROOT.$pathInc.'/1C/orders_'.hexsite().'/';
            if ($pathInc) $order_path = $DOCUMENT_ROOT . $pathInc . '/1C' . $v1c . '/orders/';

            if (!$is1C) { // после заказа
                if (!$orderid) {
                    $order_name = 'document_' . $dateA[2] . $dateA[1] . $dateA[0];
                } else {
                    $order_name = 'document_last';
                }
                @mkdir($order_path, 0775, true);
                $putfile_xml = file_put_contents($order_path . $order_name . '.xml', iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $xml));
                //$putfile_txt = file_put_contents($order_path.$order_name.'.txt',iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $docs_txt));

                if ($putfile_xml) return $order_path . $order_name;
            } else { // запрос из 1С
                if ($orderIDArr) file_put_contents($order_path . 'last1Cquery.log', implode(",", $orderIDArr));
                return $xml;
            }
        }
    }
}



# получить поле списка по ID
function getListName($nameList, $listID, $field = '')
{
    global $db, $catalogue, $setting;
    $data = $db->get_var("select data from Message2034 as a, Subdivision as b where a.keyw = '" . $nameList . "' AND a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '$catalogue'");
    if ($data) $dataArr = orderArray($data);

    if (is_array($dataArr)) {
        foreach ($dataArr as $item) {
            $ii++;
            if ($listID == $ii) {
                if ($field) {
                    return $item[$field];
                } else {
                    $reslt['name'] = $item['name'];
                    $reslt['sum'] = $item['price'];
                    $reslt['keyword'] = $item['keyword'];
                    $reslt['id'] = $ii;
                    return $reslt;
                }
            }
        }
    }
}


function nameOptList($list, $opt)
{
    global $db;
    $name = $db->get_var("SELECT {$list}_Name from Classificator_{$list} where {$list}_ID = '{$opt}'");
    return $name;
}

function file_get_contents_timeout($filename, $timeout = 3)
{
    if (strpos($filename, "://") === false) return file_get_contents($filename);
    if (!function_exists("curl_init")) return false;
    $session = curl_init($filename);
    //curl_setopt($session,CURLOPT_MUTE,true);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($session, CURLOPT_TIMEOUT, ($timeout + 10));
    curl_setopt($session, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible)");
    $result = curl_exec($session);
    curl_close($session);
    return $result;
}

function curl_get_contents($page_url, $base_url = '', $pause_time, $retry)
{
    if (!stristr($page_url, "http")) $page_url = "http://" . str_replace("//", "", $page_url);
    $error_page = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_URL, $page_url);
    if ($base_url) curl_setopt($ch, CURLOPT_REFERER, (stristr($base_url, "http") ? $base_url : "http://" . str_replace("/", "", $base_url)));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response['html'] = curl_exec($ch);
    $info = curl_getinfo($ch);
    if ($info['http_code'] != 200 && $info['http_code'] != 404) {
        $error_page[] = array(1, $page_url, $info['http_code']);
        if ($retry) {
            sleep($pause_time);
            $response['html'] = curl_exec($ch);
            $info = curl_getinfo($ch);
            if ($info['http_code'] != 200 && $info['http_code'] != 404) $error_page[] = array(2, $page_url, $info['http_code']);
        }
    }
    $response['code'] = $info['http_code'];
    $response['errors'] = $error_page;
    curl_close($ch);
    return $response;
}


// массив дерева разделов в формат CML2
function subCML($subArr)
{
    foreach ($subArr as $id => $s) {
        $razdelXML .= "
        <Группа>
                    <Ид>$id</Ид>
                    <Наименование>{$s[name]}</Наименование>
                    " . ($s[groups] ? "<Группы>
                        " . subCML($s[groups]) . "
                    </Группы>" : NULL) . "
        </Группа>";
    }
    return $razdelXML;
}

function checkEmail($email)
{
    $email = trim($email);
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
        return true;
    }
    return false;
}

// Определение размера изобр. w или h
function image_hw($image, $type)
{
    if (file_exists('http://' . $_SERVER[SERVER_NAME] . $image)) $imsize = getimagesize('http://' . $_SERVER[SERVER_NAME] . $image);

    if ($type) {
        list($w, $h) = $imsize;
        if ($type == 1) return ($w / $h) > (1.777) ? "image_w" : "image_h";
        if ($type == 2) return ($w / $h) > (1.333) ? "image_w" : "image_h";
        if ($type == 10) return "image_notstretch";
    }
    return 'image_h';
    list($w, $h) = $imsize;
    if ($w > $h) {
        return 'image_w';
    } else {
        return 'image_h';
    }
}

// Вставляет массив между другого массива
function array_insert(&$array, $position, $insert_array)
{
    $first_array = array_splice($array, 0, $position);
    $array = array_merge($first_array, $insert_array, $array);
}

# структура пути разделов, в списке выгруженных экселек (Каталок -> Сандали -> Заниженные)
function get_namesub_link($subt, $r = null)
{
    global $db, $catalogue;
    $link_p = $db->get_row("SELECT Parent_Sub_ID as parent, Subdivision_Name as name FROM Subdivision where Subdivision_ID = '{$subt}' AND Catalogue_ID = '{$catalogue}'", ARRAY_A);

    return ($link_p[parent] != 0 ? get_namesub_link($link_p[parent], 1) : null) . $link_p[name] . ($r == 1 ? ' <span>»</span> ' : null);
}

// security
function securityForm($a, $param = "")
{
    if (is_array($a)) {
        $result = array();
        foreach ($a as $key => $value) $result[$key] = securityForm($value, $param);
        return $result; // Возвращаемый "защищённый" массив
    }
    return securityFormThis($a, $param);
}
function securityFormThis($getvalue, $param)
{
    if (!stristr($param, "noslashes")) $getvalue = stripcslashes($getvalue);
    $stringValue = htmlspecialchars($getvalue, ENT_QUOTES);
    $result = addcslashes($stringValue, '$');
    return $result;
}
// Это чеза пиздец - Ильсур
// stripcslashes Array
function stripcslashesAll($a)
{
    return $a;
    /*if(is_array($a)){
          $result = array();
          foreach ($a as $key => $value) $result[$key] = stripcslashesAll($value);
          return $result; // Возвращаемый "защищённый" массив
    }
    return stripcslashes($a);*/
}

// взять настройки с файла settings.ini
function getSettings($var = null)
{
    global $pathInc, $DOCUMENT_ROOT;
    if ($pathInc) $settingspath = $DOCUMENT_ROOT . $pathInc . "/settings.ini";
    $settingFile = @file_get_contents($settingspath);
    $setting = orderArray($settingFile, 'file');

    // фича (временная)
    if (!is_array($setting)) {
        notification(10, $_SERVER[HTTP_HOST], "settings.ini not correctly");

        $settings_back = $DOCUMENT_ROOT . $pathInc . "/settings_back";
        if (is_dir($settings_back)) {
            $settings_back_files = scandir($settings_back, 1);
            $last_file_back = $settings_back . "/" . $settings_back_files[0];
            if (file_exists($last_file_back)) {
                $settingFile = @file_get_contents($last_file_back);
                $setting = orderArray($settingFile, 'file');
                # сохранить в главный файл
                if (is_array($setting)) setSettings($setting);
            }
        }
    }
    
	$setting['Email'] = $current_catalogue['Email'];
    return ($var && $setting[$var] ? $setting[$var] : $setting);
}

/**
 * получить настроек сайта
 * 
 * @param string $siteLogin;
 * @param string|array|null $vars ключ или список ключей
 * 
 * @return mixed
 */
function getSitesSettings($siteLogin, $vars = [])
{
    global $DOCUMENT_ROOT;

    $settingsPath = $DOCUMENT_ROOT . '/a/' . $siteLogin . '/settings.ini;';

    if (!file_exists($settingsPath)) return null;

    $content = file_get_contents($settingsPath);
    unset($settingsPath);

    $settings = orderArray($content, 'file');
    if (!is_array($settings)) return null;
    unset($content);

    if (empty($vars)) {
        $result = $settings;
    } elseif (is_array($vars)) {
        $result = [];
        foreach ($vars as $key) {
            $result[$key] = $settings[$key] ?? null;
        }
    } else {
        $result[$vars] = $settings[$vars] ?? null;
    }

    return $result;
}

// записать настройки с файла settings.ini
function setSettings($data)
{
    global $pathInc, $DOCUMENT_ROOT;
    if ($pathInc) $settingspath = $DOCUMENT_ROOT . $pathInc . "/settings.ini";
    creatfileSettings();
    if ($settingspath && $data && file_put_contents($settingspath, json_encode($data, JSON_HEX_QUOT)) > 0) {
        return true;
    }
    return false;
}
// взять настройки с файла settingsExport.ini
function getSettingsExport($var = null)
{
    global $pathInc, $DOCUMENT_ROOT;
    if ($pathInc) $settingspath = $DOCUMENT_ROOT . $pathInc . "/settingsExport.ini";
    $settingFile = @file_get_contents($settingspath);
    $setting = orderArray($settingFile, 'file');

    return ($var && $setting[$var] ? $setting[$var] : $setting);
}

// записать настройки с файла settingsExport.ini
function setSettingsExport($data)
{
    global $pathInc, $DOCUMENT_ROOT;
    if ($pathInc) $settingspath = $DOCUMENT_ROOT . $pathInc . "/settingsExport.ini";
    if ($settingspath && is_array($data) && file_put_contents($settingspath, json_encode($data, JSON_HEX_QUOT)) > 0) {
        return true;
    }
    return false;
}
// Создать дубликат settings.ini
function creatfileSettings()
{
    global $pathInc, $DOCUMENT_ROOT, $catalogue;
    //if($pathInc == "/a/test2"){
    $path = $DOCUMENT_ROOT . $pathInc;
    $path_back = $path . "/settings_back";
    if (!is_dir($path_back)) mkdir($path_back);

    $settings_path = $path . "/settings.ini";
    $settings_path_back = $path_back . "/" . date("Y.m.d_H-i") . ".ini";

    # создать копию
    if (!file_exists($settings_path_back)) file_put_contents($settings_path_back, file_get_contents($settings_path));

    # Удалить лишние файлы
    $count = 1000; // количество разрешенных файлов
    $files_remove = scandir($path_back, 1);
    foreach ($files_remove as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            $file = $path_back . "/" . $value;
            if (is_file($file)) {
                if ($count == 0) unlink($file);
                else $count--;
            }
        }
    }
}
# Уведомление ошибок
function notification($id, $domain, $text)
{
    global $db;

    # была ли запись до этого (в течении 5 мин)
    $havesql = $db->get_var("SELECT COUNT(*) FROM Message2070 WHERE id = {$id} AND domain = '{$domain}' AND Created >= now() - INTERVAL 5 MINUTE");

    if ($havesql) {

        $db->query("UPDATE Message2070 SET count = count + 1 where id = '{$id}' AND domain = '{$domain}' ORDER by Message_ID DESC LIMIT 1");
    } else {
        # записать уведомление в базу
        $db->query("INSERT INTO Message2070 (id, domain, `text`, Created) VALUES ('{$id}', '{$domain}', '{$text}', '" . date("Y-m-d H:i:s") . "')");
        if ($id != 20) {
            # уведомление на почту
            $title = "#{$id} {$domain}: {$text}";
            $mailtext = "#{$id} <a href='http://{$domain}/'>{$domain}</a>: {$text}";
            // отправка письма на почту
            $frommail = getDomenMail();
            $mailer = new CMIMEMail();
            $mailer->mailbody(strip_tags($mailtext), $mailtext);
            //$mailer->send("vityaran@mail.ru", $frommail, $frommail, $title, "KRZI notification");
        }
    }
}

function getAddPeople($id)
{
    global $db, $catalogue;
    $arrData = $db->get_row("select b.Sub_Class_ID, b.Subdivision_ID, a.EnglishName, a.Hidden_URL from Subdivision as a, Sub_Class as b where a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 201 AND b.Catalogue_ID = " . $catalogue . " LIMIT 1", ARRAY_A);
    return "<div class='block-edit-content'>
                <a data-rel='lightcase' data-maxwidth='950' data-groupclass='modal-edit' title='Добавить сотрудника' class='btn-a btn-a-add' href='{$arrData['Hidden_URL']}add_{$arrData['EnglishName']}.html?template=-1&contactid={$id}&isNaked=1'><span>Добавить сотрудника</span></a>
            </div>";
}

function setDateOrders($name)
{
    global $db, $catalogue;
    $arrData = array();
    $arrDataJson = $db->get_var("select ordersletter from Catalogue where Catalogue_ID = " . $catalogue);
    if (is_array(json_decode($arrDataJson, true))) $arrData = json_decode($arrDataJson, true);
    $arrData[$name] = date("Y-m-d H:i:s");
    $db->query("update Catalogue set ordersletter = '" . json_encode($arrData) . "' where Catalogue_ID = '{$catalogue}'");
}

function getCountOrders($name, $date, $catalogue)
{
    global $db;
    $classID = $db->get_var("select Class_ID from Sub_Class where Subdivision_ID = (select Subdivision_ID from Subdivision where Hidden_URL = '/{$name}/' AND Catalogue_ID = '{$catalogue}')");
    $count = $db->get_var("select COUNT(*) from Message" . $classID . " where Created >= '{$date}' AND Catalogue_ID = '{$catalogue}'");
    return ($count ? $count : "0");
}

# выбор позиций для bg (используеться в формах 2000, 2016)
function position_img($val)
{
    $variable = array(
        1 => 'Сверху - слева',
        2 => 'Сверху - центр',
        3 => 'Сверху - справа',
        4 => 'Центр - слева',
        5 => 'Центр - центр',
        6 => 'Центр - справа',
        7 => 'Снизу - слева',
        8 => 'Снизу - центр',
        9 => 'Снизу - справа',
        10 => 'Размножить',
        11 => 'Размножить сверху',
        12 => 'Размножить снизу',
        13 => 'Размножить слева',
        14 => 'Размножить справа',
        15 => 'Растянуть',
        16 => 'Заполнить'
    );
    foreach ($variable as $key => $value) {
        $option .= "<option value='$key' " . ($val == $key ? "selected" : null) . ">$value</option>";
    }
    return $option;
}
# сохранение позиции в css
function position_img_css($id)
{
    $css = array(
        1 => 'background-position: left top;background-repeat: no-repeat;',
        2 => 'background-position: center top;background-repeat: no-repeat;',
        3 => 'background-position: right top;background-repeat: no-repeat;',
        4 => 'background-position: left center;background-repeat: no-repeat;',
        5 => 'background-position: center center;background-repeat: no-repeat;',
        6 => 'background-position: right center;background-repeat: no-repeat;',
        7 => 'background-position: left bottom;background-repeat: no-repeat;',
        8 => 'background-position: center bottom;background-repeat: no-repeat;',
        9 => 'background-position: right bottom;background-repeat: no-repeat;',
        10 => 'background-position: left top;background-repeat: repeat;',
        11 => 'background-position: left top;background-repeat: repeat no-repeat;',
        12 => 'background-position: left bottom;background-repeat: repeat no-repeat;',
        13 => 'background-position: left top;background-repeat: no-repeat repeat;',
        14 => 'background-position: right top;background-repeat: no-repeat repeat;',
        15 => 'background-size: contain;background-repeat: no-repeat;',
        16 => 'background-size: cover;background-repeat: no-repeat;'
    );
    return $css[$id];
}

# вывод полей с нужной структурой
function gv_filed($name, $type, $html, $red = 0, $class = '', $w100 = 0)
{
    if ($html) {
        $html = "
        <div class='bc_setrow " . (!$name || $w100 ? 'bc_setrow_100' : '') . " " . ((!$name || $w100) && $type == 'text' ? 'bc_setrow_top' : '') . " {$class}'>
            " . ($type == 'bool' ? "<label class='bc_checkbox'>" : null) . "
                " . ($name ? "<div class='bc_setname'>{$name}" . ($red ? " <span class='red'>*</span>" : null) . "</div>" : "") . "
                <div class='bc_setvalue'>
                    {$html}
                    " . ($type == 'bool' ? "<span class='bc_chck'></span>" : null) . "
                </div>
            " . ($type == 'bool' ? "</label>" : null) . "
        </div>";
        return $html;
    }
    return "<div class='bc_setrow'>Не указано поле</div>";
}

function dismount($object)
{
    $reflectionClass = new ReflectionClass(get_class($object));
    $array = array();
    foreach ($reflectionClass->getProperties() as $property) {
        $property->setAccessible(true);
        $array[$property->getName()] = $property->getValue($object);
        $property->setAccessible(false);
    }
    return $array;
}

//Есть втроеная фукция path_info, нахуя это - Ильсур
# получить название файла по пути до нее (/www/domain/name.ini -> name.ini)
function get_file_name($path)
{
    $file_name = explode('/', $path);
    return $file_name[count($file_name) - 1];
}

# сохранение настроек зон
function savethiszone($zone, $block_id, $arrayCheck)
{
    global $db, $catalogue;

    if (is_array($arrayCheck)) {
        $blocks = $db->get_results("select settings,block_id from Message2016 where col = '$zone' AND Catalogue_ID = '$catalogue' AND block_id != '$block_id'", ARRAY_A);
        $thisblock = $db->get_row("select notitle,nolink,settings,padding from Message2016 where block_id = '$block_id' AND Catalogue_ID = '$catalogue'", ARRAY_A);
        // обновить поле settings в компоненте
        foreach ($blocks as $k => $blk) {
            $blksettings = orderArray($blk[settings]);
            $thisblockset = orderArray($thisblock[settings]);
            if ($arrayCheck[head]) {
                $blksettings[headupper] = $thisblockset[headupper] ? $thisblockset[headupper] : "";
                $blksettings[headbold] = $thisblockset[headbold] ? $thisblockset[headbold] : "";
                $blksettings[floathead] = $thisblockset[floathead] ? $thisblockset[floathead] : "";
                $blksettings[headsize] = $thisblockset[headsize] ? $thisblockset[headsize] : "";
                $blksettings[headcolor] = $thisblockset[headcolor] ? $thisblockset[headcolor] : "";
                $blksettings[headbg] = $thisblockset[headbg] ? $thisblockset[headbg] : "";
            }
            if ($arrayCheck[body]) {
                $blksettings[floatbody] = $thisblockset[floatbody] ? $thisblockset[floatbody] : "";
                $blksettings[fontcolor] = $thisblockset[fontcolor] ? $thisblockset[fontcolor] : "";
                $blksettings[linkcolor] = $thisblockset[linkcolor] ? $thisblockset[linkcolor] : "";
                $blksettings[iconcolor] = $thisblockset[iconcolor] ? $thisblockset[iconcolor] : "";
                $blksettings[menuFontSize] = $thisblockset[menuFontSize] ? $thisblockset[menuFontSize] : "";
                $blksettings[namefont] = $thisblockset[namefont] ? $thisblockset[namefont] : "";
                $blksettings[MenuUppercase] = $thisblockset[MenuUppercase] ? $thisblockset[MenuUppercase] : "";
                $blksettings[MenuColor] = $thisblockset[MenuColor] ? $thisblockset[MenuColor] : "";
                $blksettings[MenuColorActive] = $thisblockset[MenuColorActive] ? $thisblockset[MenuColorActive] : "";
            }
            if ($arrayCheck[border]) {
                $blksettings[borderwidth] = $thisblockset[borderwidth] ? $thisblockset[borderwidth] : "";
                $blksettings[bordercolor] = $thisblockset[bordercolor] ? $thisblockset[bordercolor] : "";
                $blksettings[radius] = $thisblockset[radius] ? $thisblockset[radius] : "";
            }
            if ($arrayCheck[background]) {
                $blksettings[bg] = $thisblockset[bg] ? $thisblockset[bg] : "";
                $blksettings[fixed] = $thisblockset[fixed] ? $thisblockset[fixed] : "";
                $blksettings[bgimgpos] = $thisblockset[bgimgpos] ? $thisblockset[bgimgpos] : "";
            }
            $ressett = json_encode($blksettings);
            $db->query("update Message2016 set settings = '{$ressett}' where block_id = '{$blk[block_id]}' AND Catalogue_ID = '$catalogue'");
        }
        // обновить поля в компоненте
        foreach ($arrayCheck as $key => $value) {
            if ($key == 'head') {
                $db->query("update Message2016 set notitle = '{$thisblock[notitle]}', nolink = '{$thisblock[nolink]}' where col = '$zone' AND Catalogue_ID = '$catalogue'");
            }
            if ($key == 'border') {
                $db->query("update Message2016 set padding = '{$thisblock[padding]}' where col = '$zone' AND Catalogue_ID = '$catalogue'");
            }
        }
    }
}



# доступные шрифты для выбора
function getFonts($name = null, $par = null, $select = 0)
{
    $fonts = array(
        'clear' => array('text' => 'Стандартный шрифт'),
        'Open Sans' => array('dop' => 'sans-serif', 'customize' => '300,300i,400,400i,600,600i,700,700i,800,800i', 'text' => 'Алая вспышка осветила силуэт зазубренного крыла.', 'posimg' => '-130px 0px'),
        'Roboto' => array('dop' => 'sans-serif', 'customize' => '100,100i,300,300i,400,400i,500,500i,700,700i,900,900i', 'text' => 'В вечернем свете волны отчаянно бились о берег.', 'posimg' => '-260px 0px'),
        'Roboto Condensed' => array('dop' => 'sans-serif', 'customize' => '300,300i,400,400i,700,700i', 'text' => 'Возвращаться назад предстояло в одиночку.', 'posimg' => '-390px 0px'),
        'Roboto Slab' => array('dop' => 'serif', 'customize' => '100,300,400,700', 'text' => 'Едва осознав, что происходит, мы оторвались от земли.', 'posimg' => '-130px 0px'),
        'PT Sans' => array('dop' => 'sans-serif', 'customize' => '400,400i,700,700i', 'text' => 'Всё их оборудование и инструменты были живыми, в той или иной форме.', 'posimg' => '-260px 0px'),
        'Lora' => array('dop' => 'serif', 'customize' => '400,400i,700,700i', 'text' => 'Лик луны был скрыт тенью.', 'posimg' => '-390px 0px'),
        'Ubuntu' => array('dop' => 'sans-serif', 'customize' => '400', 'text' => 'На бархатно-синем небе не было ни облачка.', 'posimg' => '-130px 0px'),
        'Lobster' => array('dop' => 'cursive', 'text' => 'Это был лишь вопрос времени.', 'posimg' => '-260px 0px'),
        //'Poiret One' => array('dop' => 'cursive', 'customize' => '400', 'text' => 'В вечернем свете волны отчаянно бились о берег.', 'posimg' => '-158'),
        'Cuprum' => array('dop' => 'sans-serif', 'customize' => '400,400i,700,700i', 'text' => 'Возвращаться назад предстояло в одиночку.', 'posimg' => '-390px 0px'),
        //'Russo One' => array('dop' => 'sans-serif', 'customize' => '400', 'text' => 'Развернувшееся зрелище и впрямь было грандиозным.', 'posimg' => '-158'),
        'Rubik' => array('dop' => 'sans-serif', 'customize' => '300,300i,400,400i,500,500i,700,700i,900,900i', 'text' => 'На бархатно-синем небе не было ни облачка.', 'posimg' => '-130px 0px'),
        //'Comfortaa' => array('dop' => 'cursive', 'customize' => '300,400,700', 'text' => 'Обе стороны моей натуры обладали общей памятью.', 'posimg' => '-158'),
        'Philosopher' => array('dop' => 'sans-serif', 'text' => 'Серебряный туман затопил палубу корабля.', 'posimg' => '-260px 0px'),
        'Marck Script' => array('dop' => 'cursive', 'text' => 'Из динамика над дверью раздался скрежещущий голос.', 'posimg' => '-390px 0px'),
        'Marmelad' => array('dop' => 'sans-serif', 'text' => 'Развернувшееся зрелище и впрямь было грандиозным.', 'posimg' => '-130px 0px'),
        //'Press Start 2P' => array('dop' => 'cursive', 'text' => 'В вечернем свете волны отчаянно бились о берег.', 'posimg' => '-158'),
        //'Ruslan Display' => array('dop' => 'cursive', 'text' => 'С корабля Земля казалась сверкающим серпом, лежащим далеко внизу.', 'posimg' => '-158'),
        //'Kurale' => array('dop' => 'serif', 'text' => 'С корабля Земля казалась сверкающим серпом, лежащим далеко внизу.', 'posimg' => '-158'),
        //'PT Sans Caption' => array('dop' => 'sans-serif', 'customize' => '400,700', 'text' => 'Всё их оборудование и инструменты были живыми, в той или иной форме.'),
        //'PT Sans Narrow' => array('dop' => 'sans-serif', 'customize' => '400,700', 'text' => 'Развернувшееся зрелище и впрямь было грандиозным.'),
        //'PT Serif' => array('dop' => 'serif', 'customize' => '400,400i,700,700i', 'text' => 'Серебряный туман затопил палубу корабля.'),
        'Ubuntu Condensed' => array('dop' => 'sans-serif', 'customize' => '300,300i,400,400i,500,500i,700,700i', 'text' => 'Алая вспышка осветила силуэт зазубренного крыла.', 'posimg' => '-260px 0px'),
        //'Merriweather' => array('dop' => 'serif', 'customize' => '300,300i,400,400i,700,700i,900,900i', 'text' => 'Настала ночь первой упавшей звезды.'),
        //'Playfair Display' => array('dop' => 'serif', 'customize' => '400,400i,700,700i,900,900i', 'text' => 'С корабля Земля казалась сверкающим серпом, лежащим далеко внизу.'),
        //'Noto Serif' => array('dop' => 'serif', 'customize' => '400,400i,700,700i', 'text' => 'Туман окутал корабль через три часа после выхода из порта.'),
        'Exo 2' => array('dop' => 'sans-serif', 'customize' => '400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'text' => 'Я любовался штормом, прекрасным, но пугающим.', 'posimg' => '-390px 0px'),
        //'Comfortaa' => array('dop' => 'cursive', 'customize' => '300,400,700', 'text' => 'Обе стороны моей натуры обладали общей памятью.'),
        //'Cormorant Infant' => array('dop' => 'serif', 'customize' => '300,300i,400,400i,500,500i,600,600i,700,700i', 'text' => 'Пламя угасло, и он глядел в окно на звезды.'),
        //'Tinos' => array('dop' => 'serif', 'customize' => '400,400i,700,700i', 'text' => 'Серебряный туман затопил палубу корабля.'),
        'Bad Script' => array('dop' => 'cursive', 'customize' => '400', 'text' => 'В вечернем свете волны отчаянно бились о берег.', 'posimg' => '-130px 0px'),
        //'Cormorant Unicase' => array('dop' => 'serif', 'customize' => '300,400,500,600,700', 'text' => 'Лик луны был скрыт тенью.'),
        //'Jura' => array('dop' => 'sans-serif', 'customize' => '300,400,500,600', 'text' => 'Настала ночь первой упавшей звезды.'),
        'Neucha' => array('dop' => 'cursive', 'customize' => '400', 'text' => 'С корабля Земля казалась сверкающим серпом, лежащим далеко внизу.', 'posimg' => '-260px 0px'),
        //'Pattaya' => array('dop' => 'sans-serif', 'customize' => '400', 'text' => 'Туман окутал корабль через три часа после выхода из порта.'),
        'El Messiri' => array('dop' => 'sans-serif', 'customize' => '400,500,600,700', 'text' => 'Это был лишь вопрос времени.', 'posimg' => '-390px 0px'),
        //'Yeseva One' => array('dop' => 'cursive', 'customize' => '400', 'text' => 'Пламя угасло, и он глядел в окно на звезды.')
    );
    if ($select) {
        foreach ($fonts as $nm => $val) {
            if ($nm == 'clear') {
                $option .= "<option value=''>- Не выбрано -</option>";
                continue;
            }
            $option .= "<option value='{$nm}' " . ($nm == $name ? 'selected' : '') . ">{$nm}</option>";
        }
        return $option;
    }
    if ($name && $par) return $fonts[$name][$par];
    if ($name) {
        return ($fonts[$name] ? $fonts[$name] : false);
    }
    return $fonts;
}

# группировка настроек блоками в нашей админке
function paramArr($id = null)
{
    $paramArr = array();
    $paramArr[1] = array("'rkassaLogin'", "'rkassaPass1'", "'rkassaPass2'", "'rrIDtovar'", "'vsevcredit'");
    $paramArr[2] = array("'noprice_text'", "'nostock_noprice'", "'edizinprice'", "'markup'", "'minicart'", "'minOrderSum'", "'typeOrder'", "'typeSelectVariable'", "'buyoneclick'", "'itemMoreSub'", "'itemlistcolor'", "'itemlistselect'", "'kopeik'", "'priceForAuth'", "'selfvariablename'", "'showCardItemCount'", "'showCardItemCount'", "'showListItemCount'", "'stockbuy'", "'cartopenmodal'", "'groupItem'", "'itemliststock'", "'skladstock'", "'itemliststockall'", "'iconcart'", "'cardbutbg'", "'colorcard_title'", "'cardcolorprice1'", "'cardcolorprice2'", "'cardcolorprice3'", "'itemComparison'", "'stockValShow'", "'ignoresublink'");
    $paramArr[3] = array("'email'", "'noticewhatsapp'", "'wazzupAPIKey'", "'wazzupChannelId'");
    $paramArr[4] = array("'sitename'", "'allowreg'", "'editortype'", "'hidephone'");
    $paramArr[5] = array("'counter'", "'meta'", "'SEOitemcard'", "'robot'");
    $paramArr[6] = array("'lists_targetcity'");
    $paramArr[7] = array("'lists_texts'");
    $paramArr[8] = array("'lists_delivery'");
    $paramArr[9] = array("'lists_payment'");
    $paramArr[10] = array("'call_time1'", "'call_time2'");
    $paramArr[11] = array("'itemlistart'", "'ves'", "'itemlistedism'", "'vendorItemName'", "'variableItemName'", "'itemlistsub'", "'specbg'", "'templateItem'", "'cardcolorname'", "'cardcolorbord'", "'cardbg'", "'cardcolortext'", "'itemborder_title'", "'cardborderwidthpx'", "'cardborderradiuspx'", "'itemtitle_title'", "'itemtitlecolor'", "'itemtitlebold'", "'itemtitleupper'", "'itemtitleborder'");
    $paramArr[12] = array("'templateItemFull'", "'reviewsItemFull'");
    $paramArr[13] = array("'css'", "'css1280'", "'css780'", "'cssColor'", "'mobileApp'");
    $paramArr[14] = array("'targeting'", "'targReq'", "'targdomen'");
    $paramArr[15] = array("'alfaLogin'", "'alfaPass'");
    $paramArr[16] = array("'sberLogin'", "'sberPass'");
    $paramArr[17] = array("'yaScid'", "'yaShopId'");
    $paramArr[18] = array("'orderLinkParam'");
    $paramArr[19] = array("'partkomLogin'", "'partkomPass'", "'partkomPrice'");
    $paramArr[20] = array("'lists_itemlabel'");
    $paramArr[21] = array("'fieldInTable'", "'showImageHover'");
    $paramArr[22] = array("'sitewidth'", "'notmobile'", "'сolorscheme'", "'powerdesign'", "'powerseo'", "'powerseo_super'", "'waterPosition'", "'nophoto'", "'payWithConfirm'", "'priorityInOtherSub'");
    $paramArr[23] = array("'createrLogo'", "'createrLink'", "'createrText'");
    $paramArr[24] = array("'devlintarget'", "'devlinday'", "'devlinkKey'");
    $paramArr[25] = array("'registrationSale'", "'freedelivery'");
    $paramArr[26] = array("'acatLogin'", "'acatPass'", "'acatToken'");
    $paramArr[27] = array("'cartForm'", "'cartListType'", "'cartusertype'");
    $paramArr[28] = array("'noSearchInText'", "'searchLife'");
    $paramArr[29] = array("'fastbuy_deliv'", "'oneClickForm'");
    $paramArr[30] = array("'message2073_name'", "'message2073_check'");
    $paramArr[31] = array("'size2073_image_select'", "'size2073'", "'size2073_fit'", "'size2073_margin'", "'size2073_select'", "'size2073_image'", "'size2073_counts'");
    $paramArr[32] = array("'message2073_fullTest'");
    $paramArr[33] = array("'language'", "'language_select'", "'lists_language'");
    $paramArr[35] = array("'lists_language_sub'");
    $paramArr[36] = array("'lists_language_blk'");
    $paramArr[37] = array("'lists_texts'", "'lists_language_keys'");
    $paramArr[38] = array("'frontpadSecret'");
    $paramArr[39] = array("'best2pay-sector'", "'best2pay-pass'", "'best2pay-test'");
    $paramArr[40] = array("'lists_params'");
    $paramArr[41] = array("'tradesoftLogin'", "'tradesoftPassword'", "'lists_tradesoft'");
    $paramArr[42] = array("'cdekCheck'", "'cdekLogin'", "'cdekPassword'", "'cdek_parameter_weight_default'", "'cdek_parameter_height_default'", "'cdek_parameter_width_default'", "'cdek_parameter_length_default'", "'lists_cdekTarifId'", "'cdekMainCity'", "'cdek_shipment_point'","'сdek_type_weight'","'сdek_type_size'","'cdek_add_days'","'cdek_add_price'","'cdek_auto_register'","'cdek_package_type'", "'cdek_from_location_address'", "'cdek_delivery_sum_in_order'");
    $paramArr[43] = array("'tinkoffLogin'", "'tinkoffPassword'");
    $paramArr[44] = array("'armtekCheck'", "'armtekLogin'", "'armtekPassword'", "'armtekMarkUp'", "'armtekLP'", "'armtekGP'");
    $paramArr[45] = array("'omegaCheck'", "'omegaLogin'", "'omegaPassword'", "'omegaMarkUp'");
    $paramArr[46] = array("'siteOff'", "'siteOffText'");
    $paramArr[47] = array("'edostCheck'", "'edostShopId'", "'edostPassword'", "'edostDefaultParams'");
    $paramArr[48] = array("'payAnyWayLogin'");
    $paramArr[49] = array("'bitrixWorkTypeCompany', 'bitrixUser'", "'bitrixOtv'", "'bitrixKey'", "'bitrixCompany'", "'bitrixSource'");
    $paramArr[50] = array("'iikoCheck'", "'iikoLogin'", "'iikoPassword'");
    $paramArr[51] = array("'dcPartnerId'", "'dcCodeTT'");
    $paramArr[52] = array("'unitellerUPID'", "'unitellerPass'", "'unitellerTax'", "'unitellerVat'", "'unitellerPayattr'", "'unitellerLineattr'");
    $paramArr[53] = array("'avangardLogin'", "'avangardPassword'");
    $paramArr[54] = array("'sbisLogin'", "'sbisPass'");
    $paramArr[55] = array("'appText'");
    $paramArr[56] = array("'start_utl'");
    $paramArr[57] = array("'pushForm'");
    $paramArr[58] = array("'aclink'", "'acsubdomen'", "'actime'", "'actoken'", "'acreftoken'");
    $paramArr[59] = array("'emailsend'", "'emailpass'", "'emailsmtp'", "'emailport'");
    $paramArr[60] = array("'telegram_bot_order_provider_checked'", "'telegram_bot_order_provider_token'");
    $paramArr[61] = array("'planfix_webhook_domen'", "'planfix_webhook_create_order'", "'planfix_webhook_create_form'");
    $paramArr[63] = ["'lists_order_status_email_template'", "'lists_order_status'"];
    $paramArr[64] = ["'lists_edzim'"];
    $paramArr[65] = array("'PR_checked'", "'PR_widgetID'", "'PR_defaultWeight'");


    if ($id > 0) return $paramArr[$id];

    $allParGet = array();
    foreach ($paramArr as $key => $value) {
        $allParGet = array_merge($allParGet, $value);
    }
    return $allParGet;
}

# получить Message_ID зоны по его id
function getMessId($id)
{
    global $db, $catalogue;
    if ($id) $messid = $db->get_var("select Message_ID from Message2000 where zone_id = {$id} AND Catalogue_ID = '{$catalogue}'");
    return ($messid ? $messid : 0);
}

# Ответ результата запроса
function echoResult($status, $text, $arr = array())
{
    $st = array("1" => "success", "2" => "error");
    $res = array($st[$status] => $text);
    if ($arr) $res = array_merge($res, $arr);
    echo json_encode($res);
    exit;
}

function saGetSettparam()
{
    $html = "";
    return $html;
}

# Проверка это строка json или нет
function isJson($string)
{
    if (!is_numeric($string)) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    return false;
}


function getallparentsub($sub, $subsec = '')
{
    global $db, $catalogue;
    $result = $subs = null;

    $sql = "SELECT Subdivision_ID FROM Subdivision where Catalogue_ID = '{$catalogue}' AND Parent_Sub_ID IN (" . ($subsec ? $subsec : $sub) . ")";
    
    $subs = $db->get_col($sql);
    $result = implode(',', $subs);

    return (!$subsec ? $sub : null) . ($result ? ',' . $result . getallparentsub($sub, $result) : null);
}


function actionObject($classID, $message, $f_Checked, $Checked = 0)
{
    global $db, $catalogue;
    if ($f_Checked != $Checked) $db->query("UPDATE Message{$classID} SET Checked = '" . ($Checked ? 1 : 0) . "' WHERE Message_ID = '{$message}' AND Catalogue_ID = '{$catalogue}'");
}


# карта сайта каталог товаров (если его нет в основной карте)
function getCatalogSitemap()
{
    global $db, $catalogue, $browse_map;
    // выбрать включенный раздел каталога ближайший к корню и получить id родителя
    $catid = $db->get_var(
        "SELECT
            Parent_Sub_ID
        FROM
            Subdivision
        WHERE
            Hidden_URL LIKE '/catalog/%'
            AND Checked = 1
            AND Hidden_URL != '/catalog/'
            AND Catalogue_ID = '{$catalogue}'
        ORDER BY 
            Hidden_URL
        LIMIT 0,1"
    );
    if ($catid > 0) {
        $result = "<h4>Каталог</h4><div class='sitemap'>";
        $result .= s_browse_sub($catid, $browse_map);
        $result .= "</div>";
        return $result;
    }
}


# карта сайта таргетинг городов
function getCitySitemap()
{
    global $db, $catalogue, $cityvars, $setting, $current_catalogue, $citymain;
    if ($setting['targeting'] && $setting['targdomen'] && $setting['cities_links_sitemap']) {

        $result = "<h4>Города</h4><ul class='sitemap'>";

        foreach ($cityvars as $key => $city) {
            if ($city['checked']) {
                if ($setting['targdomen']) {
                    $citlink = ($current_catalogue['https'] ? "https" : "http") . "://" . ($city['name'] == $citymain ? "" : "{$city['keyword']}.") . $current_catalogue['Domain'];
                } else {
                    $citlink = "/city/{$city['keyword']}{$contactUrl}";
                }
                $result .= "<li><a rel=nofollow href='{$citlink}'>{$city['name']}</a></li>";
            }
        }
        $result .= "</ul>";
    }
    return $result ?: "";
}


function getElements()
{
    if (function_exists('separated_getElementsMobail')) {
        return separated_getElementsMobail();
    }
    global $db, $catalogue, $setting, $current_catalogue, $mobileMenu;
    if ($setting['notmobile']) return;

    $userFunc = new userfunction();

    // info
    $infoHtml = is_mobile() ? "<div id='mobile-info' class='none'>" . $userFunc->init('mobileInfo') . "</div>" : "";
    $info = "<div class='mpanel-item mpanel-info'></div>";

    if (permission("cart")) { # cart
        $cart = "<div class='mpanel-item mpanel-cart " . ($_SESSION['cart']['items'] ? "mpanel-cart-active" : NULL) . "'>
                    <span class='mpanel-cart-count'>" . count($_SESSION['cart']['items']) . "</span>
                </div>";
    }

    if (permission("catalogue")) { # поиск
        $search = "<div class='mpanel-item mpanel-search' onclick='load.clickItem(\"#mobile-search\")'></div>";
        $searchHtml = "<section id='mobile-search' class='mainmenubg mobyes'>
                            <form action='/search/' method='get' " . ($setting['searchLife'] ? "class='search-life'" : "") . ">
                                <div class='msearch-clear " . ($_GET['find'] ? "active" : "") . "'>✕</div>
                                <span class='msearch-input'>
                                    <input value='" . ($_GET['find'] ? $_GET['find'] : "") . "' type='text' name='find' placeholder='" . getLangWord('mob_search_placeholder', 'Искать товары') . "' autocomplete='off'>
                                </span>
                                <span class='msearch-btn'><input type='submit' class='submit' value='Поиск'></span>
                            </form>
                        </section>";
        # фильтр
        //$filter = "<div class='mpanel-item mpanel-filter'></div>";
    }

    # left menu (mobile)
    $mobile_menu = is_mobile() ? $userFunc->init('mobile_menu') : "";
    $menuHtml = "<section id='mobile-menu' class='mobyes'>
                    <div class='menu-close' onclick='load.itemClose(\"#mobile-menu\")'>✕</div>
                    <div class='mobile-menu-body' " . (!is_mobile() ? "data-loaditem='/bc/modules/default/index.php?user_action=mobile_menu'" : "") . ">
                        {$mobile_menu}
                    </div>
                </section>";

    # left menu (desktop)
    if (!$mobileMenu) {
        $desktop_menu = !is_mobile() ? $userFunc->init('mobile_menu') : "";
        $menuHtmlDesktop = "<div class='open-menu nomob icons i_menu' onclick='load.clickItem(\"#desktop-menu\")'></div>
                            <section id='desktop-menu' class='nomob'>
                                <div class='menu-desktop-close' onclick='load.itemClose(\"#desktop-menu\")'>✕</div>
                                <div class='desktop-menu-body' " . (is_mobile() ? "data-loaditem='/bc/modules/default/index.php?user_action=mobile_menu'" : "") . ">
                                    {$desktop_menu}
                                </div>
                            </section>";
    }

    # panel
    $html = "<section id='mobile-panel' class='mobyes mainmenubg " . (!$info || !$search || !$filter || !$cart ? "mpanel-linemenu" : NULL) . "'>
                <div class='mpanel-first'>
                    <div class='mpanel-item mpanel-menu' onclick='load.clickItem(\"#mobile-menu\")'><span>" . getLangWord('mob_menu_title', 'меню') . "</span></div>
                </div>
                <div class='mpanel-second'>
                    {$info}
                    {$search}
                    {$filter}
                    {$cart}
                </div>
            </section>
            {$menuHtml}
            {$menuHtmlDesktop}
            {$infoHtml}
            {$searchHtml}";

    return $html;
}

# сортировка каталога
function getCatalogSort()
{
    if (function_exists('getCatalogSort_separated')) {
        return getCatalogSort_separated(); // своя функция
    } else {
        global $nc_ctpl, $current_cc, $current_sub, $current_catalogue;
        $vars_str = vars_str($_GET, "nc_ctpl,curPos,find,tag,filter,flt,flt1,flt3,sort,cur_cc,subr", 1);
        $vars_str3 = vars_str($_GET, "curPos,find,tag,filter,flt,flt1,flt3,sort,subr", 1);
        $vars_str4 = vars_str($_GET, "recNum,nc_ctpl,curPos,find,tag,filter,flt,flt1,flt3,cur_cc,subr", 1);

        foreach (array(24, 36, 72) as $col) $view_option .= "<option data-link='" . currentUrl() . "?recNum={$col}{$vars_str}' " . ($_GET[recNum] == $col ? "selected" : "") . ">" . getLangWord('catSort_showBy', 'Показывать по') . ": {$col}</option>";
        $ncctpl = $nc_ctpl > 1 ? $nc_ctpl : $current_cc[Class_Template_ID];

        $sortBntArr = array(
            'priority' => [
                'asc' => ['name' => 'По приоритету', 'getLang' => 'catSort_byPriority']
            ],
            'price' => [
                'asc' => ['name' => 'Сначала дешевле', 'getLang' => 'catSort_cheapFr'],
                'desc' => ['name' => 'Сначала дороже', 'getLang' => 'catSort_cheapFr']
            ],
            'rate' => [
                'asc' => ['name' => 'По популярности', 'getLang' => 'catSort_byPopular']
            ],
            'name' => [
                'asc' => ['name' => 'По названию А-Я', 'getLang' => 'catSort_byName1'],
                'desc' => ['name' => 'По названию Я-А', 'getLang' => 'catSort_byName2']
            ],
            'stock' => [
                'asc' => ['name' => 'Сначала под заказ', 'getLang' => 'catSort_byStock1'],
                'desc' => ['name' => 'Сначала в наличии', 'getLang' => 'catSort_byStock2']
            ]
        );

        $defOrder = $current_sub['defaultOrder'] ? explode(';', $current_sub['defaultOrder']) : ($current_catalogue['defaultOrder'] ? explode(';', $current_catalogue['defaultOrder']) : array());
        $sortList = '';
        # дефолтная сортировка
        if (isset($defOrder[0])) {
            $defaultBtn = $sortBntArr[$defOrder[0]];
            $type = array(
                'first' => isset($defOrder[1]) ? 'desc' : 'asc',
                'second' => !isset($defOrder[1]) ? 'desc' : 'asc'
            );
            $sortList .= sortbutton($defOrder[0], getLangWord($defaultBtn[$type['first']]['getLang'], $defaultBtn[$type['first']]['name']), $type['first'] == 'desc');
            if (isset($defaultBtn[$type['second']])) {
                $sortList .= sortbutton($defOrder[0], getLangWord($defaultBtn[$type['second']]['getLang'], $defaultBtn[$type['second']]['name']), $type['second'] == 'desc');
            }
        }

        foreach ($sortBntArr as $key => $btns) {
            if (isset($defOrder[0]) && $key == $defOrder[0]) continue;
            foreach ($btns as $type => $btnVal) {
                $sortList .= sortbutton($key, getLangWord($btnVal['getLang'], $btnVal['name']), $type == 'desc');
            }
        }

        $html = "<!-- noindex --><div class='filter-items cb'>
                    <div class='filter-item filter-item-1'>
                        <select class='select-style select-filter-item'>{$sortList}</select>
                    </div>
                    <div class='filter-item filter-item-2'>
                        <select class='select-style select-filter-item'>{$view_option}</select>
                        <span class='filter-item-count'> " . getLangWord('catSort_ofN', 'из') . " <span class='filter-item-total'></span></span>
                    </div>
                    <div class='filter-item-type'>
                        <a onclick=\"location='?nc_ctpl=2001{$vars_str3}'; return false;\" href='' class='" . (!$ncctpl || $ncctpl == 1 || $ncctpl == 2001 ? "active " : NULL) . " icons i_typecat1'></a>
                        <a onclick=\"location='?nc_ctpl=2052{$vars_str3}'; return false;\" href='' class='" . ($ncctpl == 2052 ? "active " : NULL) . " icons i_typecat2'></a>
                        <a onclick=\"location='?nc_ctpl=2025{$vars_str3}'; return false;\" href='' class='" . ($ncctpl == 2025 || $ncctpl == 2031 ? "active " : NULL) . " icons i_typecat3'></a>
                    </div>
                </div><!-- /noindex -->";

        return $html ? $html : "";
    }
}
/**
 * Преобразование массива в options (Select)
 * @param array $array
 * @param string $select Выбраный элемент
 * @return string
 */

function getOptionsFromArray($array, $select)
{
    $options = "";
    foreach ($array as $k => $v) {
        if ($v) $options .= "<option value='{$k}' " . ($k == $select ? "selected" : "") . ">{$v}</option>";
    }
    return $options;
}

# список доступных модулей каждого сайта
function checkModule($type, $checked = '', $blkid = '')
{
    global $DOCUMENT_ROOT, $pathInc2;

    $modulesPath = $DOCUMENT_ROOT . $pathInc2 . "/modules/";

    if ($type == 'get' && $checked) {
        $path = $modulesPath . $checked . "/index.php";
        if (file_exists($path)) return include($path);
    } else {
        $option = array();
        $module = false;
        if ($pathInc2 && is_dir($modulesPath)) {
            $dirs = scandir($modulesPath);
            foreach ($dirs as $dir) {
                if ($dir != '.' && $dir != '..' && file_exists($modulesPath . $dir . "/index.php")) {
                    $option[$dir] = $dir;
                    $module = true;
                }
            }
            $options = getOptionsFromArray($option, $checked);
        }
        if (!$options) $options = "<option value=''>Нету модулей</option";
        if ($type == 'check') return $module;
        if ($type == 'options') return $options;
    }
}

# вывод объектов для выбранного города
function targeting($q, $with_admin = false, $tablePref = 'a.')
{
    global $setting, $cityid, $bitcat, $inside_admin;

    $query_where = "";
    $cityID = isset($cityid) ? $cityid : 9999;
    if ($tablePref && substr($tablePref, -1) != '.') $tablePref .= '.';

    if ($setting['targeting'] && is_numeric($cityID) && !$inside_admin && ($with_admin || !$bitcat)) {
        if (!isset($cityid)) $cityid = 9999;
        $query_where = ($q ? " AND " : NULL) . "({$tablePref}citytarget like '%,{$cityID},%' OR {$tablePref}citytarget IS NULL OR {$tablePref}citytarget = '' OR {$tablePref}citytarget = ',,')";
    }
    return $query_where;
}


# шаблоны вывода дат
function dateType($date, $type = '')
{
    if (!$type) $type = "date-type-1";
    $d = new DateTime($date);
    $day = "<span class='day'>" . $d->format('d') . "</span>";
    $month = "<span class='month'>" . $d->format('m') . "</span>";
    $year = "<span class='year'>" . $d->format('Y') . "</span>";
    $hours = "<span class='hours'>" . $d->format('H') . "</span>";
    $minutes = "<span class='minutes'>" . $d->format('i') . "</span>";
    $seconds = "<span class='second'>" . $d->format('s') . "</span>";
    $dot = "<span class='dot'>.</span>";
    $colon = "<span class='colon'>:</span>";
    //date_format($d, "Y-m-d H:i:s")
    switch ($type) {
        case 'date-type-1':
        case 'date-type-news':
            $html = "<div class='{$type}'>{$day}{$dot}{$month}{$dot}{$year}</div>";
            break;
        case 'date-type-2':
            $html = "<div class='{$type}'><div class='time'>{$hours}{$colon}{$seconds}</div><div class='days'>{$day}{$dot}{$month}{$dot}{$year}</div></div>";
            break;
    }
    return $html;
}

# замена города в тексте
function cityreplace($text)
{
    global $cityname;
    return str_replace("CITYNAME", $cityname, $text);
}


/**
 * query_where фильтров
 * 
 * @param string $find строка с телом запроса
 * @param int $r поиск в текущем раздели и в дочерних
 * @param string $sub_find поиск в разделах по id
 * @param string $tablePref префикс таблицы
 * @param string $strictFind строгий поиск по телу запроса
 * 
 * @return string
 */
function getFindQuery($find, $r = 0, $sub_find = '', $tablePref = 'a', $strictFind = '')
{
    global $current_cc, $current_sub, $catalogue, $setting, $db, $AUTH_USER_ID;

    $stopStr = '';

    if ($tablePref && substr($tablePref, -1) != '.') $tablePref .= '.';

    if ($strictFind) $find = $current_sub['find'] = $strictFind;

    if (function_exists('getFindQuery_separated')) return getFindQuery_separated($find, $r, $sub_find, $tablePref, $strictFind);
    if ($current_cc['Class_ID'] == 2041 || $current_cc['EnglishName'] == 'find' || $current_sub['find']) { # стоп слова

        $findvar = $current_cc['Class_ID'] == 2041 ? $find : ($current_sub['find'] ? $current_sub['find'] : $current_cc['EnglishName']);

        $findArr = explode("|", $findvar);

        if ($current_cc['EnglishName'] != 'search') {
            $find = trim($findArr[0]);
        }

        if ($findArr[1]) {
            $stop = explode(" ", trim($findArr[1]));
            foreach ($stop as $stp) { # stop words
                $stopStr .= !empty($stopStr) ? ' AND ' : null;
                $stopStr .= "{$tablePref}`name` not like '%{$stp}%'";
                $stopStr .= " AND ({$tablePref}`art` not like '%{$stp}%' OR {$tablePref}`art` IS NULL)";
                $stopStr .= " AND ({$tablePref}`vendor` not like '%{$stp}%' OR {$tablePref}`vendor` IS NULL)";
                $stopStr .= " AND ({$tablePref}`code` not like '%{$stp}%' OR {$tablePref}`code` IS NULL)";
                $stopStr .= " AND ({$tablePref}`tags` not like '%{$stp}%' OR {$tablePref}`tags` IS NULL)";
            }
        }
    } # end стоп слова

    $zamArr = [
        "\\\'" => "", "\'" => "", "'" => "", "\\" => " ", "\"" => "",
        "/" => " ", "," => " ", ", " => " ", "." => " ", " и " => " ",
        " на " => " ", "=" => " ", " за " => " ", " под " => " ",
        " над " => " ", " с " => " ", " из " => " ", " в " => " ",
        " к " => " ", " у " => " "
    ];

    $find = strtr($find, $zamArr);

    $find = htmlspecialchars(strip_tags(addslashes($find)));

    $find = str_replace("  ", " ", $find);

    $findVars = explode(" или ", $find);

    if ($strictFind) {
        foreach ($findVars as $fvar) {
            $fvar = strtr($fvar, $zamArr);
            $fvar = str_replace("  ", " ", $fvar);
            $findArr = explode(" ", $fvar);
            $stringsearch = '';
            foreach ($findArr as $word) {
                unset($wordF);
                if ($word) {
                    $word = trim($word);
                    $wordF = $word;
                    $stringsearch .= !empty($stringsearch) ? ' AND ' : null;
                    $stringsearch .= "(";
                    $stringsearch .= "{$tablePref}`name` LIKE '%{$word}%'";
                    $stringsearch .= " OR {$tablePref}`analog` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var2` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var3` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var4` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var5` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var7` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var8` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var9` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var10` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var11` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var12` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var13` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var14` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var15` LIKE '%{$wordF}%'";

                    $stringsearch .= " OR {$tablePref}`params` LIKE '%||%{$wordF}%|%'";
                    $stringsearch .= " OR {$tablePref}`keyword_find` LIKE '%|" . preg_replace('/[^a-zа-я0-9]/ui', '', mb_strtolower($wordF)) . "|%'";

                    if (strlen($word) >= 1) {
                        $stringsearch .= " OR {$tablePref}`art` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`art2` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`artnull` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`artnull` LIKE '%" . str_replace(array("-", ".", "\\", "/"), "", $wordF) . "%'";
                    }

                    if ($setting['noSearchInText']) {
                        $stringsearch .= " OR {$tablePref}`var1` LIKE '%{$wordF}%'";
                    }
                    if ($setting['noSearchInText'] && strlen($word) > 3) {
                        $stringsearch .= " OR {$tablePref}`text` LIKE '%{$word}%'";
                    }
                    if (strlen($word) > 3) {
                        $stringsearch .= " OR {$tablePref}`vendor` LIKE '%{$word}%'";
                    }
                    $stringsearch .= ")";
                }
            }
            $stringsearchAll .= ($stringsearchAll ? " OR " : NULL) . $stringsearch;
        }
    } else {
        foreach ($findVars as $fvar) {
            $fvar = strtr($fvar, $zamArr);
            $fvar = str_replace("  ", " ", $fvar);
            $findArr = explode(" ", $fvar);
            $stringsearch = '';
            foreach ($findArr as $word) {
                unset($wordF);
                if ($word) {
                    $word = trim($word);
                    $wordF = $word;
                    if (!is_numeric($word) && mb_strlen($word) > 5 && mb_strlen($word) < 8 && preg_match('/[а-яА-Я]+/', $word)) $word = mb_substr($word, 0, -1);
                    if (!is_numeric($word) && mb_strlen($word) >= 8 && mb_strlen($word) < 12 && preg_match('/[а-яА-Я]+/', $word)) $word = mb_substr($word, 0, -2);
                    if (!is_numeric($word) && mb_strlen($word) >= 12 && preg_match('/[а-яА-Я]+/', $word)) $word = mb_substr($word, 0, -3);
                    $stringsearch .= !empty($stringsearch) ? ' AND ' : null;
                    $stringsearch .= "(";
                    $stringsearch .= "{$tablePref}`name` LIKE '%{$word}%'";
                    $stringsearch .= " OR {$tablePref}`analog` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var2` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var3` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var4` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var5` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var7` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var8` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var9` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var10` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var11` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var12` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var13` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var14` LIKE '%{$wordF}%'";
                    $stringsearch .= " OR {$tablePref}`var15` LIKE '%{$wordF}%'";

                    $stringsearch .= " OR {$tablePref}`params` LIKE '%||%{$wordF}%|%'";
                    $stringsearch .= " OR {$tablePref}`keyword_find` LIKE '%|" . preg_replace('/[^a-zа-я0-9]/ui', '', mb_strtolower($wordF)) . "|%'";

                    $stringsearch .= " OR {$tablePref}`tags` LIKE '{$wordF}'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '{$wordF},%'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '{$wordF} %'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '% {$wordF} %'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '% {$wordF},%'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '%,{$wordF},%'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '% {$wordF}'";
                    $stringsearch .= " OR {$tablePref}`tags` LIKE '%,{$wordF}'";

                    if (strlen($word) >= 1) {
                        $stringsearch .= " OR {$tablePref}`art` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`art2` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`artnull` LIKE '%{$wordF}%'";
                        $stringsearch .= " OR {$tablePref}`artnull` LIKE '%" . str_replace(array("-", ".", "\\", "/"), "", $wordF) . "%'";
                    }

                    if ($setting['noSearchInText']) {
                        $stringsearch .= " OR {$tablePref}`var1` LIKE '%{$wordF}%'";
                    }

                    if ($setting['noSearchInText'] && mb_strlen($word) >= 2) {
                        $stringsearch .= " OR {$tablePref}`text` LIKE '%{$word}%'";
                    }

                    if (strlen($word) > 3) {
                        $stringsearch .= " OR {$tablePref}`vendor` LIKE '%{$word}%'";
                    }
                    if ($setting['noSearchInCode'] && strlen($word) > 3) {
                        $stringsearch .= " OR {$tablePref}`code` LIKE '%{$wordF}%'";
                    }
                    $stringsearch .= ")";
                }
            }

            $stringsearchAll .= ($stringsearchAll ? " OR " : NULL) . $stringsearch;
        }
    }

    if (!empty($stringsearchAll)) {
        $stringsearchAll = "({$stringsearchAll})";
    }

    if ($r > 0) { // искать в текущем разделе
        $sitesArr = array(732, 760, 770);
        if (in_array($catalogue, $sitesArr)) {
            $podrrArr = getallparentsub($r);
            if ($podrrArr) $podrr = "{$tablePref}Subdivision_ID IN (" . $podrrArr . ")";
        }

        $insub = "({$tablePref}Subdivision_ID = '$r' OR {$tablePref}Subdivision_IDS like '%,$r,%'" . ($podrr ? " OR $podrr" : NULL) . ")";
    }

    if ($sub_find != '') {
        $sub_find = explode(',', $sub_find);
        foreach ($sub_find as $key) {
            $insub_where .= ($insub_where ? "OR ({$tablePref}Subdivision_ID = '$key' OR {$tablePref}Subdivision_IDS like '%,$key,%') " : " ({$tablePref}Subdivision_ID = '$key' OR {$tablePref}Subdivision_IDS like '%,$key,%') ");
        }
        $insub = "($insub_where)";
    }

    $query_where = '';

    if (!empty($stringsearchAll)) {
        $query_where .= !empty($query_where) ? " AND " : NULL;
        $query_where .= $stringsearchAll;
    }
    if (!empty($stopStr)) {
        $query_where .= !empty($query_where) ? " AND " : NULL;
        $query_where .= $stopStr;
    }
    if (!empty($insub)) {
        $query_where .= !empty($query_where) ? " AND " : NULL;
        $query_where .= $insub;
    }

    return $query_where;
}

# обработка вариантов товара 
# новая версия
function variableItems($p, $type)
{
    global $setting, $db, $catalogue, $AUTH_USER_ID;

    if ($setting['groupItem']) {
        # удаление товара
        if ($type == 'remove' && $p['variablenameSide'] == 1) {
            $db->query("UPDATE Message2001 set variablenameSide = 0 where Subdivision_ID = '{$p['sub']}' AND Catalogue_ID = '{$catalogue}' AND name = '{$p['name']}' AND Checked = 1 LIMIT 1");
        }
        # варианты товаров - обновление
        if ($type == 'edit') {
            $saveKeyword = '';
            foreach ($p['variable'] as $id => $value) {
                if (isset($p['newvariable'][$id]) && $p['id']) {
                    $cc = $db->get_var("SELECT Sub_Class_ID FROM Message2001 where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
                    if ($cc) {
                        if (empty($saveKeyword)) {
                            #сохраям и удалям уникальное поле keyword для создания варианта товара
                            $saveKeyword = $db->get_var("SELECT Keyword FROM Message2001 where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
                            // if (!empty($saveKeyword)) 
                            $db->query("UPDATE Message2001 SET Keyword = NULL WHERE Message_ID = {$p['id']} AND Catalogue_ID = '{$catalogue}'");
                        }
                        unset($p['variable'][$id]);
                        $id = nc_copy_message(2001, $p['id'], $cc);
                        $current_idkey = $db->get_var("SELECT Keyword FROM Message2001 where Message_ID = {$id} AND Catalogue_ID = {$catalogue}");
                        if ($current_idkey == '') {
                            $db->query("UPDATE Message2001 SET Keyword = NULL WHERE Message_ID = {$id} AND Catalogue_ID = '{$catalogue}'");
                        }
                        if ($value['side'] != '0') {
                            $value['side'] = '1';
                            $db->query("UPDATE Message2001 SET `variablenameSide` = '1'  WHERE Message_ID = {$id} AND Catalogue_ID = '{$catalogue}'");
                        } else {
                            $value['side'] = '0';
                            $db->query("UPDATE Message2001 SET `variablenameSide` = '0'  WHERE Message_ID = {$id} AND Catalogue_ID = '{$catalogue}'");
                        }
                        $p['variable'][$id] = $value;
                    }
                }
                $db->query("UPDATE `Message2001` SET `variablename` = '{$value['name']}' WHERE `Message_ID` = {$id} AND `Catalogue_ID` = {$catalogue}");
            }

            if (empty($saveKeyword)) {
                $saveKeyword = $db->get_var("SELECT Keyword FROM Message2001 where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
                if (empty($saveKeyword)) {
                    $db->query("UPDATE Message2001 SET Keyword = NULL where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
                } else {
                    $db->query("UPDATE Message2001 SET Keyword = '{$saveKeyword}' where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
                }
            } else {
                $db->query("UPDATE Message2001 SET Keyword = '{$saveKeyword}' where Message_ID = {$p['id']} AND Catalogue_ID = {$catalogue}");
            }

            // $side = $db->get_var("SELECT COUNT(*) FROM `Message2001` 
            //                         WHERE `name` = '{$p['main_name']}' AND `Subdivision_ID` = {$p['sub_id']} AND `variablenameSide` = '0'
            //                         AND `Catalogue_ID` = {$catalogue} AND `Message_ID` != {$p['id']}") == 0; #true - 0 false - > 1
            // if($side) {
            //     $db->query("UPDATE `Message2001` SET `variablenameSide` = 0 WHERE `Message_ID` = {$p[id]} AND `Catalogue_ID` = {$catalogue}");
            // }

            // Изменения наименования все варианта если нет настройки changeAll (Самостоятельное изменения названия товара)
            if (false !== ($p['changeAll'] ?? true) && count($p['variable']) > 1 && $p['main_name'] != $p['old_name']) {
                // $sidenum = $side ? 0 : 1;
                // $db->query("UPDATE `Message2001` SET `variablenameSide` = {$sidenum} WHERE `Message_ID` = {$p[id]} AND `Catalogue_ID` = {$catalogue}");

                $db->query("UPDATE `Message2001` SET `name` = '{$p['main_name']}' WHERE `Message_ID` IN (" . implode(',', array_keys($p['variable'])) . ")");
                return $p;
            }

            $mainProduct = $p['variable'][$p['id']]['side'] == 0;

            if ($mainProduct && count($p['variable']) > 1 && $p['main_name'] != $p['old_name']) {
                #меняем варианту товара side на 0 чтобы он заменил уходящий главный товар
                $arKeys = array_keys($p['variable']);
                $variableSetNull = $arKeys[0] == $p[id] ? $arKeys[1] : $arKeys[0];

                $db->query("UPDATE `Message2001` SET `variablenameSide` = 0 WHERE `Message_ID` = {$variableSetNull} AND `Catalogue_ID` = {$catalogue}");
            }

            # true - таких имен 0, false - таких имен > 0
            $isUniqueName = $db->get_var("SELECT COUNT(*) FROM `Message2001` 
                                            WHERE `name` = '{$p['main_name']}' AND `Subdivision_ID` = {$p['sub_id']} 
                                            AND `Catalogue_ID` = {$catalogue} AND `Message_ID` != {$p['id']}") == 0;

            if (!$isUniqueName && $mainProduct && $p['main_name'] != $p['old_name']) {
                #меняем на 1 side
                // $sidenum = $side ? 0 : 1;
                $db->query("UPDATE `Message2001` SET `variablenameSide` = {$sidenum} WHERE `Message_ID` = {$p[id]} AND `Catalogue_ID` = {$catalogue}");

                return $p;
            }

            if ($isUniqueName && !$mainProduct) {
                #меняем на 0 side
                $db->query("UPDATE `Message2001` SET `variablenameSide` = 0 WHERE `Message_ID` = {$p['id']} AND `Catalogue_ID` = {$catalogue}");
                return $p;
            }

            return $p;
        }
        if ($type == 'add' || $type == 'import') {
            # проверка существования данных
            if ($p['id'] && $p['name'] && $p['sub']) {
                $mainHave = $db->get_var(
                    "SELECT 
                        count(*) 
                    FROM 
                        Message2001 
                    WHERE 
                        Catalogue_ID = '{$catalogue}' 
                        AND Subdivision_ID = '{$p['sub']}' 
                        AND name = '{$p['name']}' 
                        AND Message_ID != '{$p['id']}' 
                        AND Checked = 1 
                        AND (
                                variablenameSide IS NULL 
                                OR variablenameSide = '' 
                                OR variablenameSide = 0
                            )"
                );
                # Если есть главный, делаем нынешний объект доп. вариантом
                if ($mainHave > 0) {
                    $db->query("UPDATE Message2001 set variablenameSide = 1 where Message_ID = '{$p['id']}' AND Catalogue_ID = '{$catalogue}'");
                } else {
                    $db->query("UPDATE Message2001 set variablenameSide = 0 where Message_ID = '{$p['id']}' AND Catalogue_ID = '{$catalogue}'");
                }
            }
        }
    }
}
#конец тест


# обработка вариантов товара 
# старая версия
function variableItems__old($p, $type)
{
    global $setting, $db, $catalogue, $AUTH_USER_ID;
    if ($setting[groupItem]) {
        # удаление товара
        if ($type == 'remove' && $p[variablenameSide] == 1) {
            $db->query("UPDATE Message2001 set variablenameSide = 0 where Subdivision_ID = '{$p[sub]}' AND Catalogue_ID = '{$catalogue}' AND name = '{$p[name]}' AND Checked = 1 LIMIT 1");
        }
        # варианты товаров - обновление
        if ($type == 'edit' && count($p[variable]) > 1) {
            $variableitemSide = 0;
            # id от объекта для дублирования
            $mainItemID = $p[id];
            #сохраям и удалям уникальное поле keyword для создания варианта товара
            $saveKeyword = $db->get_var("SELECT Keyword FROM Message2001 where Message_ID = '{$mainItemID}' AND Catalogue_ID = '{$catalogue}'");
            $db->query("UPDATE Message2001 SET Keyword = NULL where Message_ID = '{$mainItemID}' AND Catalogue_ID = '{$catalogue}'");
            foreach ($p[variable] as $id => $variableItem) {
                # создание нового объекта
                if (isset($p[newvariable][$id]) && $mainItemID) {
                    $cc = $db->get_var("SELECT Sub_Class_ID FROM Message2001 where Message_ID = '{$mainItemID}' AND Catalogue_ID = '{$catalogue}'");
                    if ($cc) {
                        $id = nc_copy_message(2001, $mainItemID, $cc);
                        $db->query("UPDATE Message2001 SET Keyword = NULL WHERE Keyword = '' AND Message_ID = '{$id}' AND Catalogue_ID = '{$catalogue}'");
                    }
                }
                # проверка checked
                $checked = $db->get_var("SELECT Checked FROM Message2001 WHERE Message_ID = '{$id}' AND Catalogue_ID = '{$catalogue}'");
                # Название варианта и определяем главный или нет
                if ($variableItem[name]) $db->query("UPDATE Message2001 set variablename = '{$variableItem[name]}', variablenameSide = {$variableitemSide} " . ($p[main_name] ? ", name = '{$p[main_name]}'" : "") . " where Message_ID = '{$id}' AND Catalogue_ID = '{$catalogue}'");
                # след товар скрытый для видимости при группировке
                if ($checked) $variableitemSide = 1;
            }


            #возвращаем уникальное поле keyword товару
            if ($saveKeyword) $db->query("UPDATE Message2001 SET Keyword = '{$saveKeyword}' where Message_ID = '{$mainItemID}' AND Catalogue_ID = '{$catalogue}'");
            return $p;
        }
        if ($type == 'add' || $type == 'import') {
            # проверка существования данных
            if ($p[id] && $p[name] && $p[sub]) {
                $mainHave = $db->get_var("SELECT count(*) FROM Message2001 where Catalogue_ID = '{$catalogue}' AND Subdivision_ID = '{$p[sub]}' AND name = '{$p[name]}' AND Message_ID != '{$p[id]}' AND Checked = 1 AND (variablenameSide IS NULL OR variablenameSide = '' OR variablenameSide = 0)");
                # Если есть главный, делаем нынешний объект доп. вариантом
                if ($mainHave > 0) $db->query("UPDATE Message2001 set variablenameSide = 1 where Message_ID = '{$p[id]}' AND Catalogue_ID = '{$catalogue}'");
            }
        }
    }
}

# приведение параметра в нужный вид (шины)
function sVar($var)
{
    $arrzam = array(".0" => "", "." => ",");
    return strtr(trim($var), $arrzam);
}




function deliveryDays($citymain, $cityname)
{
    global $setting;
	
	return false;

    if ($setting['devlinday']) {
        # Берем из cookie
        if ($_COOKIE['citydays']) {
            $daysArray = explode(":", $_COOKIE['citydays']);
            if ($daysArray[0] == $citymain && $daysArray[1] == $cityname) $days = $daysArray[2];
        }
        # Выполняем запрос
        if (!$days) {
            if ($citymain == $cityname) {
                $days = "1 день";
            } else {
                $days = getDeliveryTime($citymain, $cityname);
            }
            //if($days) setcookie("citydays", "{$citymain}:{$cityname}:{$days}", time()+3600*24*365, "/", $_SERVER['HTTP_HOST']);
        }
        if ($citymain && $cityname && $days) {
            if (getLangWord('day_word') == 'day') {
                $days = preg_replace('/[^\d]+/', '', $days);
                $days .= ($days == '1' ? ' day' : ' days');
            }
            $deliveryDay = "<span class='deliveryDay-1'>" . getLangWord('delline_1', 'Срок доставки в') . " " . getCityLink(array("title" => getLangCityName($cityname))) . ": <b>{$days}</b></span>";
        } else {
            $deliveryDay = "<span class='deliveryDay-2'>" . getLangWord('delline_1', 'Срок доставки в') . "</span> <span class='deliveryDay-2'>" . getCityLink(array("title" => getLangWord('city', 'город'))) . "</span>";
        }
    }
    return $deliveryDay;
}



function getDeliveryTime($city1, $city2)
{
	
	return false;
	
    global $setting, $DELIVCACHE_FOLDER, $AUTH_USER_ID;
    if ($city1 && $city2) {

        $filecache = $DELIVCACHE_FOLDER . md5($city1 . $city2);

        if (file_exists($filecache) && filemtime($filecache) > time() - 60 * 60 * 24 * 7) { // берем из кеша
            return file_get_contents($filecache);
        } else { // получаем новые данные

            $curl = curl_init();

            $url = 'https://api.dellin.ru/v1/public/cities.json';
            $appKey = $setting['devlinkKey'] ?? "B0922856-4439-11E7-9897-00505683A6D3";

            $post = '{"appKey":"' . $appKey . '"}';
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            $out = curl_exec($curl);

            if (strpos($out, '"errors": "Unauthorized"') !== false) return '';

            $out = json_decode($out, 1);
            if ($out['url']) {

                $csv = file($out['url']);
                for ($i = 1; $i < count($csv); $i++) {
                    if ($_c1 != '' and $_c2 != '') break;
                    $temp = explode(',', $csv[$i]);
                    if (mb_strpos($temp[1], $city1) !== false) $_c1 = $temp[2];
                    elseif (mb_strpos($temp[1], $city2) !== false) $_c2 = $temp[2];
                }

                $url = 'https://api.dellin.ru/v1/public/calculator.json';
                $post = '{
                    "appKey":"' . $appKey . '",
                    "derivalPoint": ' . $_c1 . ',
                    "arrivalPoint": ' . $_c2 . ',
                    "sizedVolume": "1",
                    "sizedWeight": "2"
                }';
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_TIMEOUT, 5);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                $out = curl_exec($curl);
                $out = json_decode($out, 1);

                curl_close($curl);


                if ($out['time']['nominative']) {
                    file_put_contents($filecache, $out['time']['nominative']); //update cache
                    return $out['time']['nominative'];
                }
            }
        }
    }
    return '';
}


function getXleb($p)
{
    if (function_exists('separare_getXleb')) {
        return separare_getXleb($p);
    } else {
        global $mainpage, $classID, $sub_level_count, $browse_xleb, $is404, $browse_xleb_2, $action, $current_sub, $parent_sub_tree, $filter, $find, $xlebhave, $AUTH_UESR_ID;
        # евли уже выводиться в блоке
        if ($xlebhave && $p['main']) return "";

        if (!$mainpage) {
            if ($sub_level_count > 0 || ($sub_level_count > 0 && $action == 'full') || $filter || $find) {
                $backlink = ($action == 'full' ? $current_sub[Hidden_URL] : $parent_sub_tree[1][Hidden_URL]);
                if ($backlink == '/index/') $backlink = '/';
                switch ($p['type']) {
                    case '1':
                        $li = ($classID != 2001 ?
                            "<li class='xleb-item'>
                                        <span>
                                            <span>
                                                <a href='/'>" . getLangWord("lang_sub_index", "Главная") . "</a>
                                            </span>
                                        </span></li>" : "")
                            . xlebMicromarkingLevel(nc_browse_path_range_krz(-1, $sub_level_count - ($is404 ? 2 : 1), $browse_xleb, 0, 2), ($classID != 2001 ? 1 : 0));

                        $back = (($classID == 2001 && $action == 'full') || $classID != 2001 ?
                            "<span>
                                <a href='{$backlink}' class='back_catalog icons i_left'>
                                    " . ($classID == 2001 ? getLangWord("xleb_back_ctlg", "Назад в каталог") : getLangWord("xleb_back", "Назад")) . "
                                </a>
                            </span>" : null);

                        $html = "<section class='line_info'><ul class='xleb'>{$li}</ul>{$back}</section>";

                        break;
                    case '2':
                        $li = "<li class='xleb-default-item'>
                                    <span>
                                        <span>
                                            <a href='/'>" . getLangWord("lang_sub_index", "Главная") . "</a>
                                        </span>
                                    </span>
                                </li>" . xlebMicromarkingLevel(nc_browse_path_range_krz(-1, $sub_level_count - ($is404 ? 2 : 1), $browse_xleb_2, 0, 2));

                        $html = "<div><ul class='xleb-default'>{$li}</ul></div>";
                        break;
                }
            }
        }
        $xlebhave = 1;
        return $html ? $html : "";
    }
}
/**
 * 
 * @param string $str
 * 
 * @return string
 */
function xlebMicromarkingLevel($str, $add = 1)
{
    $resulte = '';
    foreach (explode('%i', $str) as $level => $subString) {
        if ($level == 0) $resulte = $subString;
        else $resulte .= ($level + $add) . $subString;
    }

    return $resulte;
}
// Проверка формата файла формы
function fileForm($file)
{
    $allowfile = array("doc", "docx", "xls", "xlsx", "pdf", "zip", "rar", "7z", "txt", "jpeg", "jpg", "gif", "png", "cdr", "ai", "psd", "eps", "ppt");
    $filepart = array_reverse(explode(".", $file['name']));
    if ($filepart[0] && in_array($filepart[0], $allowfile)) {
        return true;
    } else {
        return false;
    }
}


function multiToString($array)
{
    $result = array();
    if ($array && $array['default']) {
        $result[cols] = orderArray($array['default']);
        unset($array['default']);
        $result[values] = $array;
    }
    return json_encode($result);
}


function getLoadMore($param)
{
    if ($param[totRows] > $param[recNum]) {
        return "<div class='load-more' data-sub='{$param[sub]}' data-cc='{$param[cc]}' data-totRows='{$param[totRows]}' data-recNum='{$param[recNum]}'><a href='#'><span>" . getLangWord('load_more_button_title', 'Показать еще') . "</span></a></div>";
    }
}


# save Photo By Google
function savePhotoByGoogle($msg, $photos)
{
    global $db, $pathInc, $pathInc2, $catalogue, $DOCUMENT_ROOT;
    $messName = $db->get_row("select name,art,code from Message2001 where Message_ID = '{$msg}'", ARRAY_A);
    $imgpath = $pathInc . "/files/userfiles/images/";
    $imgpathR = $DOCUMENT_ROOT . $imgpath;
    @mkdir($imgpathR, 0775);
    @mkdir($imgpathR . "ggl/", 0775);
    $priorPhoto = 50;
    foreach ($photos as $ph) {
        $photoFileSize = $phpath = $phpathR = $filecontent = NULL;
        $phname = encodestring($messName['name'], 1) . "_" . time() . ".jpg";
        $phpath = $imgpath . "ggl/" . $phname;
        $phpathR = $imgpathR . "ggl/" . $phname;

        $urlArr = parse_url($ph);
        $filecontentArr = @curl_get_contents($ph, $urlArr[scheme] . "://" . $urlArr[host], 1, 1);
        $filecontent = $filecontentArr[html];
        if ($filecontent && strlen($filecontent) > 100) { // точно картинка
            if (@file_put_contents($phpathR, $filecontent)) {
                $photoFileSize = @filesize($phpathR);
                $db->query("insert into Multifield (Field_ID,Message_ID,Priority,Name,Size,Path,Preview,SizeOrig) VALUES (2353,'" . $msg . "','" . ($priorPhoto + 1) . "','" . $messName['name'] . "','3','{$phpath}','{$phpath}','" . $photoFileSize . "')");

                if (!stristr($ph, "korzilla.ru")) {
                    $curl1 = curl_init();
                    curl_setopt($curl1, CURLOPT_URL, 'http://seo.korzilla.ru/kz/add.php');
                    curl_setopt($curl1, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl1, CURLOPT_POST, true);
                    curl_setopt($curl1, CURLOPT_POSTFIELDS, "catalogue=6&cc=68&sub=73&posting=1&f_Checked=1&f_name=" . $messName['name'] . "&f_art=" . ($messName['art'] ? $messName['art'] : $messName['code']) . "&f_phname={$ph}");
                    $out = curl_exec($curl1);
                    curl_close($curl1);
                }

                $okload++;
            }
        } else {
            $out = print_r($filecontentArr, 1);
        }
        $priorPhoto++;
        sleep(1);
    }
    if ($okload > 0) @file_get_contents("http://seo.korzilla.ru/photocount/?dom={$_SERVER[HTTP_HOST]}&key={$_SERVER[HTTP_HOST][1]}&cnt={$okload}");
    return $okload;
}

function getPagination($p)
{
    $pagination = "";
    if (!$p['isTitle']) {
        if ($p['inside_admin']) { # BTN
            $btn = browse_messages($p['cc_env'], 15);
            if ($btn) {

                $btn = str_replace("href=?", "href={$p['url']}?", $btn);
                $pagination = "<div class='pagination'>
                    <div class='pgn-line'>
                        {$btn}
                        <span class='pag_text'>из</span> <a rel='nofollow' href='{$p['url']}?curPos=" . (ceil($p['totRows'] / $p['recNum']) * $p['recNum'] - $p['recNum']) . "{$p['vars_str2']}'>" . ceil($p['totRows'] / $p['recNum']) . "</a>
                    </div>
                </div>";
            }
        } else { # load ajax
            $pagination = getLoadMore(array("totRows" => $p['totRows'], "recNum" => $p['recNum'], "sub" => $p['sub'], "cc" => $p['cc']));
        }
    }
    return $pagination;
}

# Генерация пароля
function generatePass($length = 8)
{
    $code = '';
    $symbols = '0123456789ABCDFGHJKLMNOPRSTQUVXWZabcdfghjkmnoprstquvxwz';

    for ($i = 0; $i < (int)$length; $i++) {
        $num = rand(1, strlen($symbols));
        $code .= substr($symbols, $num, 1);
    }
    return $code;
}

/**
 * Получить массив заказа
 * 
 * @param int $orderId
 * 
 * @return array|null
 */
function get_order($orderId)
{
    global $db;

    $orderId = (int) $orderId;

    return $db->get_row("SELECT * FROM `Message2005` WHERE `Message_ID` = {$orderId}", ARRAY_A) ?: null;
}

// заказ оплачен
function orderWasPayd($orderid, $systemPay = '')
{
    global $db, $settingCont, $setting, $catalogue;
    $ord = get_order($orderid);

    $db->query("update Message2005 set ShopOrderStatus = 3 where Message_ID = '" . $orderid . "' AND Catalogue_ID = '$catalogue'");

    $frommail = getDomenMail();

    $mailbody = "Добрый день!<br>
Заказ №" . $orderid . " успешно оплачен" . ($systemPay ? " через систему {$systemPay}" : NULL) . ".<br>
<br>
--<br>
С уважением,<br>
" . $current_catalogue['Catalogue_Name'] . "<br>
" . $_SERVER['HTTP_HOST'] . "";

    $mailer = new CMIMEMail();
    $mailer->setCharset('utf-8');
    $mailer->mailbody(strip_tags($mailbody), $mailbody);
    if ($ord['email']) $mailer->send($order['email'], $frommail, $frommail, "Ваш заказ № {$orderid} оплачен", $current_catalogue['Catalogue_Name']);
    if ($settingCont['email']) $mailer->send($settingCont['email'], $frommail, $frommail, "Ваш заказ № {$orderid} оплачен", $current_catalogue['Catalogue_Name']);
}

// CURL (используется для best2pay и uniteller)
function file_get_contents1($url, $gg = '', $context)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($context, '', '&'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}


# изменение контактов: список складов
function nc_sklad1c_field($sklads = '')
{
    global $db, $catalogue;
    $skladArr = array('stock' => 'Склад 1', 'stock2' => 'Склад 2', 'stock3' => 'Склад 3', 'stock4' => 'Склад 4');
    $res .= "<div class='colblock colblock-target'><h4>Соответствующие склады (остатки):</h4>";
    foreach ($skladArr as $sID => $sklad) {
        $res .= "<div class='colline colline-1'><input " . (strstr($sklads, "," . $sID . ",") ? "checked" : "") . " type='checkbox' value='$sID' name='f_sklad1c[$sID]'> {$sklad}</div>";
    }
    $res .= "</div>";
    return $res;
}

function array_first_key($arr)
{
    reset($arr);
    return key($arr);
}




// интеграция с bitrix24
function bitrixNewLead($data = array(), $tovars = array())
{
    global $setting, $DOCUMENT_ROOT, $pathInc, $AUTH_USER_ID;

    if ($setting['bitrixCompany'] && $setting['bitrixUser'] && $setting['bitrixKey'] && $setting['bitrixOtv'] && $data) {
        if ($setting['bitrixWorkTypeCompany'] == true) {
            new \App\modules\Korzilla\CRM\Bitrix24\Company\BitrixCompanyController(
                $setting['bitrixCompany'],
                $setting['bitrixUser'],
                $setting['bitrixKey'],
                $setting['bitrixOtv'],
                $data,
                $tovars
            );
        } else {
            $url = 'https://' . $setting['bitrixCompany'] . '.bitrix24.ru/rest/' . $setting['bitrixUser'] . '/' . $setting['bitrixKey'] . '/crm.lead.add.json?';
            $_data = 'fields[ASSIGNED_BY_ID]=' . $setting['bitrixOtv'];
            $_name = '';
            foreach ($data as $k => $v) {
                if (!$v)
                    continue;
                if ($k == 'EMAIL') {
                    $_data .= '&fields[EMAIL][0][VALUE]=' . (trim($v));
                    $_data .= '&fields[EMAIL][0][VALUE_TYPE]=WORK';
                    $_data .= '&fields[EMAIL][0][TYPE_ID]=EMAIL';
                } elseif ($k == 'PHONE') {
                    $_data .= '&fields[PHONE][0][VALUE]=' . (trim($v));
                    $_data .= '&fields[PHONE][0][VALUE_TYPE]=WORK';
                    $_data .= '&fields[PHONE][0][TYPE_ID]=PHONE';
                } else {
                    if ($k == 'NAME') {
                        if (!$_name)
                            $_data .= '&fields[' . trim($k) . ']=' . (trim($v));
                    } else
                        $_data .= '&fields[' . trim($k) . ']=' . (trim($v));
                }
            }
            $_data .= '&fields[SOURCE_ID]=' . ($setting['bitrixSource'] ? $setting['bitrixSource'] : "WEB");
            $_data = str_replace(array("\n", '%', '\\'), '', $_data); #защита от инъекции в http запрос
            $url .= str_replace(" ", "%20", $_data);
            $headers = [
                'Content-Type: application/json'
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $response = curl_exec($ch);
            curl_close($ch);
            $return = json_decode($response, 1);
            file_put_contents($DOCUMENT_ROOT . "/" . $pathInc . '/bitrixLog.log', print_r([
                "url" => $url,
                "response" => $response,
                "decoded" => $return,
            ], 1), FILE_APPEND);
            if ($tovars && $return['result']) {
                $id = $return['result'];
                $url = 'https://' . $setting['bitrixCompany'] . '.bitrix24.ru/rest/' . $setting['bitrixUser'] . '/' . $setting['bitrixKey'] . '/crm.lead.productrows.set.json?id=' . $id;
                $_data = '';
                $i = 0;
                foreach ($tovars as $tovar) {
                    $_data .= '&rows[' . $i . '][OWNER_ID]=' . $id;
                    $_data .= '&rows[' . $i . '][PRODUCT_NAME]=' . (trim($tovar['name']));
                    $_data .= '&rows[' . $i . '][PRICE]=' . $tovar['price'];
                    $_data .= '&rows[' . $i . '][QUANTITY]=' . $tovar['count'];
                    $_data .= '&rows[' . $i . '][MEASURE_CODE]=' . $tovar['edizm'];
                    $i++;
                }
                $_data = str_replace(array("\n", '%', '\\'), '', $_data);
                if ($_data) {
                    $url .= $_data;
                    file_put_contents($DOCUMENT_ROOT . "/" . $pathInc . '/bitrixLog.log', print_r([
                        "tovar url" => $url
                    ], 1), FILE_APPEND);
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
            return $return;
        }
    } else
        return false;
}

function clearStreet($street, $clear)
{
    if (stristr(mb_strtolower($street), "улица")) {
        $street = str_replace("улица", "", $street);
        $street = str_replace("Улица", "", $street);
        $street = trim($street);
    }
    if (stristr(mb_strtolower($street), "проспект")) {
        $street = str_replace("проспект", "", $street);
        $street = str_replace("Проспект", "", $street);
        if (!$clear) $street = trim($street) . " проспект";
    }
    if (stristr(mb_strtolower($street), "бульвар")) {
        $street = str_replace("бульвар", "", $street);
        $street = str_replace("Бульвар", "", $street);
        if (!$clear) $street = trim($street) . " бульвар";
    }
    if (stristr(mb_strtolower($street), "переулок")) {
        $street = str_replace("переулок", "", $street);
        $street = str_replace("Переулок", "", $street);
        if (!$clear) $street = trim($street) . " переулок";
    }
    return trim($street);
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


// По возрастанию:
function cmp_function($a, $b)
{
    return ($a['name'] > $b['name']);
}


// По убыванию:
function cmp_function_desc($a, $b)
{
    return ($a['name'] < $b['name']);
}

function unitellerGetSinatere($array, $needly, $pass, $delimer = '')
{
    $result = '';
    foreach ($needly as $key) {
        $val = isset($array[$key]) ? $array[$key] : '';
        $result .= ($result ? $delimer : '') . md5($val);
    }
    return strtoupper(md5($result . $delimer . md5($pass)));
}

function clearPhoneSNG($phone)
{
    $phone = preg_replace("/\D/", "", $phone);
    if (strlen($phone) >= 11 && strlen($phone) <= 12) return $phone;
    return false;
}

function cityKomu($city)
{
    $gde = array(
        "Абакан" => "Абакану",
        "Астана" => "Астане",
        "Адлер" => "Адлеру",
        "Альметьевск" => "Альметьевску",
        "Архангельск" => "Архангельску",
        "Астрахань" => "Астрахани",
        "Балаково" => "Балаково",
        "Барнаул" => "Барнаулу",
        "Белгород" => "Белгороду",
        "Бийск" => "Бийску",
        "Бишкек" => "Бишкеку",
        "Благовещенск" => "Благовещенску",
        "Братск" => "Братску",
        "Брянск" => "Брянску",
        "Бугульма" => "Бугульме",
        "Великие Луки" => "Великим Лукам",
        "Великий Новгород" => "Великому Новгороду",
        "Владивосток" => "Владивостоку",
        "Владимир" => "Владимиру",
        "Волгоград" => "Волгограду",
        "Волгодонск" => "Волгодонску",
        "Волжский" => "Волжскому",
        "Волжск" => "Волжску",
        "Вологда" => "Вологде",
        "Воронеж" => "Воронежу",
        "Дзержинск" => "Дзержинску",
        "Димитровград" => "Димитровграду",
        "Екатеринбург" => "Екатеринбургу",
        "Елабуга" => "Елабуге",
        "Железнодорожный" => "Железнодорожному",
        "Забайкальск" => "Забайкальску",
        "Заинск" => "Заинску",
        "Иваново" => "Иваново",
        "Ижевск" => "Ижевску",
        "Иркутск" => "Иркутску",
        "Йошкар-Ола" => "Йошкар-Оле",
        "Казань" => "Казани",
        "Калининград" => "Калининграду",
        "Калуга" => "Калугу",
        "Казахстан" => "Казахстану",
        "Камышин" => "Камышину",
        "Кемерово" => "Кемерово",
        "Киров" => "Кирову",
        "Коломна" => "Коломне",
        "Кострома" => "Кострому",
        "Котлас" => "Котласу",
        "Краснодар" => "Краснодару",
        "Красноярск" => "Красноярску",
        "Курган" => "Кургану",
        "Курск" => "Курску",
        "Липецк" => "Липецку",
        "Магнитогорск" => "Магнитогорску",
        "Миасс" => "Миассу",
        "Москва" => "Москве",
        "Мурманск" => "Мурманску",
        "Минзелинск" => "Минзелинску",
        "Менделеевск" => "Менделеевску",
        "Набережные Челны" => "Набережным Челнам",
        "Нижневартовск" => "Нижневартовску",
        "Нижний Новгород" => "Нижнему Новгороду",
        "Нижний Тагил" => "Нижнему Тагилу",
        "Новокузнецк" => "Новокузнецку",
        "Новомосковск" => "Новомосковску",
        "Новороссийск" => "Новороссийску",
        "Новосибирск" => "Новосибирску",
        "Ногинск" => "Ногинску",
        "Нурлат" => "Нурлату",
        "Нур-Султан" => "Нур-Султану",
        "Обнинск" => "Обнинску",
        "Омск" => "Омску",
        "Орел" => "Орлу",
        "Оренбург" => "Оренбургу",
        "Орск" => "Орску",
        "Пенза" => "Пензе",
        "Пермь" => "Перми",
        "Петрозаводск" => "Петрозаводску",
        "Подольск" => "Подольску",
        "Псков" => "Пскову",
        "Пушкино" => "Пушкино",
        "Ростов-на-Дону" => "Ростову-на-Дону",
        "Рыбинск" => "Рыбинску",
        "Рязань" => "Рязани",
        "Самара" => "Самаре",
        "Санкт-Петербург" => "Санкт-Петербургу",
        "Саранск" => "Саранску",
        "Саратов" => "Саратову",
        "Северодвинск" => "Северодвинску",
        "Серпухов" => "Серпухову",
        "Смоленск" => "Смоленску",
        "Солнечногорск" => "Солнечногорску",
        "Сочи" => "Сочи",
        "Ставрополь" => "Ставрополи",
        "Старый Оскол" => "Старому Осколу",
        "Стерлитамак" => "Стерлитамаку",
        "Сургут" => "Сургуту",
        "Сызрань" => "Сызрани",
        "Сыктывкар" => "Сыктывкару",
        "Тамбов" => "Тамбову",
        "Тверь" => "Твери",
        "Тольятти" => "Тольятти",
        "Томилино" => "Томилино",
        "Томск" => "Томску",
        "Тула" => "Туле",
        "Тюмень" => "Тюмени",
        "Улан-Удэ" => "Улан-Удэ",
        "Ульяновск" => "Ульяновску",
        "Уфа" => "Уфе",
        "Ухта" => "Ухте",
        "Хабаровск" => "Хабаровску",
        "Чебоксары" => "Чебоксарам",
        "Челябинск" => "Челябинску",
        "Череповец" => "Череповцу",
        "Чита" => "Чите",
        "Энгельс" => "Энгельсу",
        "Ярославль" => "Ярославлю",
        "Севастополь" => "Севастополю",
        "Симферополь" => "Симферополю"
    );
    return ($gde[$city] ? $gde[$city] : $city);
}

function cityGde($city)
{
    $gde = array(
        "Абакан" => "Абакане",
        "Астана" => "Астане",
        "Адлер" => "Адлере",
        "Альметьевск" => "Альметьевске",
        "Архангельск" => "Архангельске",
        "Астрахань" => "Астрахани",
        "Балаково" => "Балаково",
        "Барнаул" => "Барнауле",
        "Белгород" => "Белгороде",
        "Бийск" => "Бийске",
        "Бишкек" => "Бишкеке",
        "Благовещенск" => "Благовещенске",
        "Братск" => "Братске",
        "Брянск" => "Брянске",
        "Бугульма" => "Бугульме",
        "Великие Луки" => "Великих Луках",
        "Великий Новгород" => "Великом Новгороде",
        "Владивосток" => "Владивостоке",
        "Владимир" => "Владимире",
        "Волгоград" => "Волгограде",
        "Волгодонск" => "Волгодонске",
        "Волжский" => "Волжском",
        "Волжск" => "Волжске",
        "Вологда" => "Вологде",
        "Воронеж" => "Воронеже",
        "Дзержинск" => "Дзержинске",
        "Димитровград" => "Димитровграде",
        "Екатеринбург" => "Екатеринбурге",
        "Елабуга" => "Елабуге",
        "Железнодорожный" => "Железнодорожном",
        "Забайкальск" => "Забайкальске",
        "Заинск" => "Заинске",
        "Иваново" => "Иваново",
        "Ижевск" => "Ижевске",
        "Иркутск" => "Иркутске",
        "Йошкар-Ола" => "Йошкар-Оле",
        "Казань" => "Казани",
        "Калининград" => "Калининграде",
        "Калуга" => "Калуге",
        "Казахстан" => "Казахстане",
        "Камышин" => "Камышине",
        "Кемерово" => "Кемерово",
        "Киров" => "Кирове",
        "Коломна" => "Коломне",
        "Кострома" => "Костроме",
        "Котлас" => "Котласе",
        "Краснодар" => "Краснодаре",
        "Красноярск" => "Красноярске",
        "Курган" => "Кургане",
        "Курск" => "Курске",
        "Липецк" => "Липецке",
        "Магнитогорск" => "Магнитогорске",
        "Миасс" => "Миассе",
        "Москва" => "Москве",
        "Мурманск" => "Мурманске",
        "Минзелинск" => "Минзелинске",
        "Менделеевск" => "Менделеевске",
        "Набережные Челны" => "Набережных Челнах",
        "Нижневартовск" => "Нижневартовске",
        "Нижний Новгород" => "Нижнем Новгороде",
        "Нижний Тагил" => "Нижнем Тагиле",
        "Новокузнецк" => "Новокузнецке",
        "Новомосковск" => "Новомосковске",
        "Новороссийск" => "Новороссийске",
        "Новосибирск" => "Новосибирске",
        "Ногинск" => "Ногинске",
        "Нурлат" => "Нурлате",
        "Нур-Султан" => "Нур-Султане",
        "Обнинск" => "Обнинске",
        "Омск" => "Омске",
        "Орел" => "Орле",
        "Оренбург" => "Оренбурге",
        "Орск" => "Орске",
        "Пенза" => "Пензе",
        "Пермь" => "Перми",
        "Петрозаводск" => "Петрозаводске",
        "Подольск" => "Подольске",
        "Псков" => "Пскове",
        "Пушкино" => "Пушкино",
        "Ростов-на-Дону" => "Ростове-на-Дону",
        "Рыбинск" => "Рыбинске",
        "Рязань" => "Рязани",
        "Самара" => "Самаре",
        "Санкт-Петербург" => "Санкт-Петербурге",
        "Саранск" => "Саранске",
        "Саратов" => "Саратове",
        "Северодвинск" => "Северодвинске",
        "Серпухов" => "Серпухове",
        "Смоленск" => "Смоленске",
        "Солнечногорск" => "Солнечногорске",
        "Сочи" => "Сочи",
        "Ставрополь" => "Ставрополи",
        "Старый Оскол" => "Старый Осколе",
        "Стерлитамак" => "Стерлитамаке",
        "Сургут" => "Сургуте",
        "Сызрань" => "Сызрани",
        "Сыктывкар" => "Сыктывкаре",
        "Тамбов" => "Тамбове",
        "Тверь" => "Твери",
        "Тольятти" => "Тольятти",
        "Томилино" => "Томилино",
        "Томск" => "Томске",
        "Тула" => "Туле",
        "Тюмень" => "Тюмени",
        "Улан-Удэ" => "Улан-Удэ",
        "Ульяновск" => "Ульяновске",
        "Уфа" => "Уфе",
        "Ухта" => "Ухте",
        "Хабаровск" => "Хабаровске",
        "Чебоксары" => "Чебоксарах",
        "Челябинск" => "Челябинске",
        "Череповец" => "Череповце",
        "Чита" => "Чите",
        "Энгельс" => "Энгельсе",
        "Ярославль" => "Ярославле",
        "Севастополь" => "Севастополе",
        "Симферополь" => "Симферополе"
    );
    return ($gde[$city] ? $gde[$city] : $city);
}
if (!function_exists('array_key_first')) {
    function array_key_first(array $arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

function fltSort(&$arr)
{
    usort($arr, function ($a, $b) {
        $checkA = is_numeric($a['fld']);
        $checkB = is_numeric($b['fld']);

        if ($checkA && $checkB) return $a['fld'] - $b['fld'];
        elseif ($checkA || $checkB) return $checkA ? 0 : 1;
        else return mb_strtoupper($a['fld']) > mb_strtoupper($b['fld']);
    });
}

/**
 * setFillterForName
 * Кеширует фильтр по алфавиту из первого слова в названии товара. 
 * @param  string $query_where
 * @param  bool $ignore_sub
 * @return void|false
 */
function setFillterForName($query_where, $ignore_sub)
{
    global $setting, $current_sub, $action, $db, $AUTH_USER_ID, $catalogue;

    if (!$setting['fillterForNameOn'] || $action == 'full' || $current_sub['fillter_for_name_off']) return false;

    $hiddenFillterInSub = ['/search/'];
    $cacheItemsParams = $current_sub['cachItemParam'] ? orderArray($current_sub['cachItemParam']) : [];

    if (
        in_array($current_sub['Hidden_URL'], $hiddenFillterInSub)
        || ($cacheItemsParams['fillterCache'] == 1
            && count(orderArray($current_sub['filter_for_name'])) > 0)
    ) return false;

    $itemsNameFilter = $db->get_col(
        "SELECT 	
            SUBSTRING_INDEX(LTRIM(a.name), ' ', 1) as name
        FROM
            Message2001 as a
        WHERE
        a.Catalogue_ID = {$catalogue}
        AND a.Checked = 1
        " . (!$ignore_sub ? "AND a.Subdivision_ID = {$current_sub['Subdivision_ID']}" : null) . "
        " . ($query_where ? "AND {$query_where}" : null)
            . " GROUP BY SUBSTRING_INDEX(LTRIM(a.name), ' ', 1)"
    );

    $subFilterForName = $current_sub['filter_for_name'] ? orderArray($current_sub['filter_for_name']) : [];
    foreach ($itemsNameFilter as $name) {
        $name = str_replace(["'", '"', '`'], '', $name);
        if (!is_numeric(substr($name, 0, 1)) && !isset($subFilterForName[$name])) {
            $subFilterForName[$name] = ['on' => "1", 'text' => $name];
        }
    }
    $current_sub['filter_for_name'] = json_encode($subFilterForName);
    $db->query(
        "UPDATE 
            Subdivision 
        SET 
            `filter_for_name` = '" . addslashes($current_sub['filter_for_name']) . "' 
        WHERE 
            Subdivision_ID = {$current_sub['Subdivision_ID']}"
    );
    setSeoCache($current_sub['Subdivision_ID'], ['fillterCache' => 1]);
}

function seoWordsRazdel($sub, $cases)
{
    if (function_exists('function_seoWordsRazdel')) {
        return function_seoWordsRazdel($sub, $cases); // своя функция
    } else {
        // $start = microtime(true);
        global $catalogue, $db, $current_sub;

        $cach_seo = orderArray($current_sub['cachItemParam']);

        if (empty($cach_seo[$cases])) {

            $sql = "SELECT 
                        ROUND(MAX(price)) as `max`,
                        ROUND(MIN(NULLIF(price,0))) as `min`,
                        count(*) as `count` FROM Message2001 as a
                    WHERE
                        Catalogue_ID = '{$catalogue}'
                        AND Checked = 1
                        AND (" . queryWhereSubdivision((int) $sub, true) . ")";

            $res = $db->get_row($sql, ARRAY_A);

            $cach = ["ASC" => $res['min'], "DESC" => $res['max'], "NUM" => $res['count']];

            setSeoCache($sub, $cach);

            $result = $cach[$cases];
        } else {
            $result = $cach_seo[$cases];
        }
        // if (getIP('office')) {
        //     echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';
        // }
        return $result;
    }
}

function setSeoCache($subID, $cache = [])
{
    global $current_sub, $catalogue, $db;

    if ($subID == $current_sub['Subdivision_ID']) {
        $cachItemParam = $current_sub['cachItemParam'] ? orderArray($current_sub['cachItemParam']) : [];
    } else {
        $cachItemParam = $db->get_var("SELECT `cachItemParam` FROM Subdivision WHERE Subdivision_ID = '{$subID}' AND Catalogue_ID = '{$catalogue}'");
        $cachItemParam = $cachItemParam ? orderArray($cachItemParam) : [];
    }

    $cachItemParam = $cache + $cachItemParam;

    $res = $db->query(
        "UPDATE 
            Subdivision 
        SET 
            cachItemParam = '" . addslashes(json_encode($cachItemParam)) . "'
        WHERE 
            Catalogue_ID = {$catalogue}  
            AND Subdivision_ID = '{$subID}'"
    );

    if ($subID == $current_sub['Subdivision_ID']) {
        $current_sub['cachItemParam'] = json_encode($cachItemParam);
    }

    return $res;
}
/**
 * Удаления сео кеша с параметрами товаров
 * 
 * @param int $sub Subdivision_ID
 * @return void
 */
function clearSeoCach($sub = 0)
{
    global $catalogue, $db, $AUTH_USER_ID;

    $query_where = ($sub > 0 ? "AND Subdivision_ID = '{$sub}'" : '');
    $db->query("UPDATE Subdivision SET cachItemParam = '' where Catalogue_ID = '{$catalogue}' " . $query_where);
}



/*function ZipDirectory($src_dir, $zip, $dir_in_archive='') {
    $src_dir = str_replace("\\","/",$src_dir);
    $dir_in_archive = str_replace("\\","/",$dir_in_archive);
    $dirHandle = opendir($src_dir);
    $notzip = array('import.xml','offers.xml','offers_old.xml','import_old.xml','import_files','settings_back'); // исключения
    while (false !== ($file = readdir($dirHandle))) {
        if (($file != '.') && ($file != '..') && !in_array($file,$notzip) && !stristr($file,'.tgz') && !stristr($file,'.zip') && !stristr($file,'.rar') && !stristr($file,'.tar')) {
                if (!is_dir($src_dir.$file)) {
                    echo ".\n"; flush(); ob_flush();
                    $zip->addFile($src_dir.$file, $dir_in_archive.$file);
                } else {
                    echo ",\n"; flush(); ob_flush();
                    $zip->addEmptyDir($dir_in_archive.$file);
                    $zip = ZipDirectory($src_dir.$file.DIRECTORY_SEPARATOR,$zip,$dir_in_archive.$file.DIRECTORY_SEPARATOR);
                }
        }
        echo "-\n"; flush(); ob_flush();
    }
    return $zip;
}

function ZipFull($src_dir, $archive_path) {
    echo ".\n"; flush(); ob_flush();
    $zip = new ZipArchive();
    if($zip->open($archive_path, ZIPARCHIVE::CREATE) !== true) {
        return false;
    }
    $zip = ZipDirectory($src_dir,$zip);
    echo ".\n"; flush(); ob_flush();
    $zip->close();
    return true;
}*/

function zipPath($src_dir, $archive_path, $ignore_path)
{
    echo ".\n";
    flush();
    ob_flush();
    system("zip {$archive_path} -x -q {$ignore_path} -r -1 {$src_dir}");
    return true;
}

function sendYandexDisk($file)
{
    echo ",\n";
    flush();
    ob_flush();
    $tt = 'AgAAAAAaa1vbAAZmnp3Ap4uWKkmGoffz33R6yr8';
    $path = '/krzi/sites/';

    $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/upload?overwrite=true&path=' . urlencode($path . basename($file)));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $tt));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($res, true);
    echo ".\n";
    flush();
    ob_flush();

    if (empty($res['error'])) {
        $fp = fopen($file, 'r');
        $ch = curl_init($res['href']);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo ".\n";
        flush();
        ob_flush();

        if ($http_code == 201) {
            @unlink($file);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getFilterQuery($prev = '')
{
    global $cityphone, $kurs, $AUTH_USER_ID;
    $result = '';

    if (isset($_GET['flt'])) {
        $get = securityForm($_GET['flt']);
        foreach ($get as $fName => $fVal) {
            if (empty($fName) || $fName == 1 || $fk == 2) continue;
            switch ($fName) {
                case 'colors': # цвета
                    if (!$fVal) continue;
                    $colorQuery = '';
                    if (is_array($fVal)) {
                        $colorQuery = '';
                        foreach ($fVal as $color) {
                            if (!$color) continue;
                            if ($color) $colorQuery .= ($colorQuery ? " OR " : null) . "{$prev}`colors` LIKE '%\"name\":" . str_replace("\\", "\\\\\\\\", json_encode($color)) . "%'";
                        }
                        if ($colorQuery) $result .= ($result ? " AND " : null) . " ({$colorQuery}) ";
                    } else {
                        $result .= ($result ? " AND " : null) . "{$prev}`colors` LIKE '%\"name\":" . str_replace("\\", "\\\\\\\\", json_encode($fVal)) . "%'";
                    }
                    break;
                case 'params': # доп параметры
                    if (!is_array($fVal)) continue;
                    foreach ($fVal as $paramKey => $paramVal) {
                        if ($get['params_range'][$paramKey]) { # от-до доппараметров
                            $paramQuery = '';
                            # должен быть параметр второй, если нет до или от
                            if ($paramVal[0] && !$paramVal[1]) {
                                $ar = explode("_", $get['params_range'][$paramKey]);
                                $paramVal[1] = max($ar);
                            } else if ($paramVal[1] && !$paramVal[0]) {
                                $ar = explode("_", $get['params_range'][$paramKey]);
                                $paramVal[0] = min($ar);
                            }
                            foreach (explode("_", $get['params_range'][$paramKey]) as $val) {
                                if ((float)$val > 0 && (float)$val >= (float)$paramVal[0] && (float)$val <= (float)$paramVal[1]) {
                                    $paramQuery .= ($paramQuery ? " OR " : NULL) . "{$prev}`params` like '%{$paramKey}||{$val}|%'";
                                }
                            }
                            if ($paramQuery) $result .= ($result ? " AND " : null) . " ({$paramQuery}) ";
                        } else {
                            $paramQuery = '';
                            foreach ($paramVal as $val) {
                                $val = urldecode($val);
                                if ($val) $paramQuery .= ($paramQuery ? " OR " : NULL)
                                    . "(
                                        {$prev}`params` LIKE '%{$paramKey}||{$val}|%'
                                        OR {$prev}`params` LIKE '%{$paramKey}||%;{$val}|%' 
                                        OR {$prev}`params` LIKE '%{$paramKey}||{$val};%|%'
                                        OR {$prev}`params` LIKE '%{$paramKey}||%;{$val};%|%'
                                        )";
                            }
                            if ($paramQuery) $result .= ($result ? " AND " : null) . " ({$paramQuery}) ";
                        }
                    }
                    break;
                default: # обычные поля
                    if ($fName == "params_range") break;
                    $valuesArr = array();
                    $query = '';
                    if (is_array($fVal)) {
                        foreach ($fVal as $val) if (is_numeric($val) || !empty($val)) $valuesArr[] = $val;
                    } elseif (is_numeric($fVal) || !empty($fVal)) {
                        $valuesArr[] = $fVal;
                    }
                    foreach ($valuesArr as $val) {
                        $val = urldecode($val);
                        $query .= ($query ? " OR " : null)
                            . "({$prev}`{$fName}` = '{$val}'"
                            . " OR {$prev}`{$fName}` LIKE '%;{$val}'"
                            . " OR {$prev}`{$fName}` LIKE '{$val};%'"
                            . " OR {$prev}`{$fName}` LIKE '%;{$val};%')";
                    }
                    if ($query) $result .= ($result ? " AND " : null) . "({$query})";
                    break;
            }
        }
    }
    if (isset($_GET['flt1']) && !$_GET['adaptiv']) { # от - до
        $get = securityForm($_GET['flt1']);
        foreach ($get as $fName => $fVal) {
            if ($fName == 'price') {
                $fVal[0] = (float) $fVal[0];
                $fVal[1] = (float) $fVal[1];

                # если установлен курс
                if ($_GET['currenc'] == 2 && $kurs['dollar'] > 0) {
                    $fVal[0] = $fVal[0] / $kurs['dollar'];
                    $fVal[1] = $fVal[1] / $kurs['dollar'];
                }

                if ($_GET['currenc'] == 3 && $kurs['euro'] > 0) {
                    $fVal[0] = $fVal[0] / $kurs['euro'];
                    $fVal[1] = $fVal[1] / $kurs['euro'];
                }
            }
            if (is_numeric($fVal[0]) && $fVal[0]) $result .= ($result ? " AND " : null) . " {$prev}`{$fName}` >= '{$fVal[0]}' ";
            if (is_numeric($fVal[1]) && $fVal[1]) $result .= ($result ? " AND " : null) . " {$prev}`{$fName}` <= '{$fVal[1]}' ";
        }
    }
    if (isset($_GET['flt3'])) { # да - нет
        $get = securityForm($_GET['flt3']);
        foreach ($get as $fName => $fVal) {
            if (!$fVal) continue;
            $fVal = urldecode($fVal);
            if (stristr($fName, "stock")) {
                $stocksql = '';
                if ($cityphone['sklad1c']) {
                    foreach (explode(",", $cityphone['sklad1c']) as $skl) {
                        if ($skl) $stocksql .= ($stocksql ? " OR " : NULL) . "{$prev}`{$skl}` > 0";
                    }
                    $result .= ($result ? " AND " : null) . "({$stocksql})";
                } else {
                    $result .= ($result ? " AND " : null) . "({$prev}`stock` > 0 OR {$prev}`stock2` > 0 OR {$prev}`stock3` > 0 OR {$prev}`stock4` > 0)";
                }
            } else {
                $result .= ($result ? " AND " : null) . "({$prev}`{$fName}` != '' AND {$prev}`{$fName}` IS NOT NULL)";
            }
        }
    }
    if (isset($_GET['flt4'])) { # доп.параметры
        $get = securityForm($_GET['flt4']);
        foreach ($get as $fName => $fVal) {
            if (!$fVal) continue;
            $fVal = urldecode($fVal);
            $result .= ($result ? " AND " : null)
                . "(
                {$prev}`params` LIKE '%{$fName}||{$fVal}|%'
                OR {$prev}`params` LIKE '%{$fName}||%;{$fVal}|%' 
                OR {$prev}`params` LIKE '%{$fName}||{$fVal};%|%'
                OR {$prev}`params` LIKE '%{$fName}||%;{$fVal};%|%'
                )";
        }
    }

    return $result;
}

function getClassificator($format)
{
    global $classificators, $db;
    if (!isset($classificators[$format])) {
        $list = $db->get_results("SELECT `{$format}_Name` as name, `{$format}_ID` as id FROM Classificator_{$format} WHERE Checked = 1 ORDER by `{$format}_Name`", ARRAY_A);
        foreach ($list as $item) $classificators[$format][$item['id']] = $item['name'];
    }
    return $classificators[$format];
}

function subTargeting($subTarget)
{
    global $cityid;

    if (strpos($subTarget, ",{$cityid},") !== false || $subTarget == '' || $subTarget == ',,') return true;
    else return false;
}

function getExtensionSub($sub)
{
    global $db, $catalogue;
    $result = $subParent = [];

    $sql = "SELECT Parent_Sub_ID FROM Subdivision where Catalogue_ID = '{$catalogue}' AND Subdivision_ID = '{$sub}'";
    $subParent = $db->get_var($sql);
    $result = ($subParent ? [$subParent] : []);
    return array_merge($result, ($subParent ? getExtensionSub($subParent) : []));
}


function textTargeting($text)
{
    global $sub_targeting, $AUTH_USER_ID, $setting, $cityvars, $citylink, $cityid;

    if ($setting['targdomen'])
        $pdomen = ($sub_targeting ? $sub_targeting : "main");
    else {
        if (isset($setting['lists_targetcity'][$cityid]['main']) && $setting['lists_targetcity'][$cityid]['main'] == 1)
            $pdomen = 'main';
        else
            $pdomen = $citylink ?: 'main';
    }

    $re = '/(<p>|<div>|){{city_[a-z\-\d]+}}.*{{\/city_[a-z\-\d]+}}(<\/p>|<\/div>|)/sU';
    $text = str_replace("\r\n", "", $text);

    preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $m) {
        if (!strstr($m[0], "{{city_" . $pdomen . "}}")) $text = str_replace($m[0], "", $text);
    }
    $text = preg_replace('#(\s*<br\s*/?>)*\s*$#i', '', str_replace(array("{{city_" . $pdomen . "}}", "{{/city_" . $pdomen . "}}"), "", $text));

    return $text;
}

/**
 * Действие полсе изменения заказа
 * 
 * @param int $orderID
 * @param int $status id нового статуса
 * @param int|null $statusOld id старого статуса
 * 
 * @return void
 */
function orderStatusChangeAfter($orderID, $status, $statusOld = null)
{
    global $db, $current_catalogue, $setting;
    if ($current_catalogue['customCode'] && function_exists('orderStatusChangeAfter_separated')) return orderStatusChangeAfter_separated($orderID, $status, $statusOld);
    else {
        sendEmailNotificationAfterOrderStatusChange($orderID, $status);
    }
}

/**
 * Отправить уведомление о смене статуса заказа на почту
 * 
 * @param int $orderId 
 * @param int $status id нового статуса
 * 
 * @return void
 */
function sendEmailNotificationAfterOrderStatusChange($orderId, $status)
{
    global $setting, $current_catalogue, $db;

    if ($setting['wazzupAPIKey'] && $setting['wazzupChannelId']) {
        $statusMessage = $db->get_var("SELECT ShopOrderStatus_Name FROM Classificator_ShopOrderStatus WHERE ShopOrderStatus_ID = $status");
        $customerPhone = $db->get_var("SELECT phone FROM Message2005 WHERE Message_ID = $orderId");
        $whatsappMessage = "Добрый день! Ваш заказ №" . $orderId . " на сайте " . $_SERVER['HTTP_HOST'] . " в статусе " . $statusMessage . "";
        sendWhatsappMessage($customerPhone, $whatsappMessage, $setting['wazzupAPIKey'], $setting['wazzupChannelId']);
    }

    $isEmptyEmailTemplates = !is_array($setting['lists_order_status_email_template'] ?? null);

    if ($isEmptyEmailTemplates || !$order = get_order($orderId)) return;

    if (empty($order['email'])) return;

    $class2005 = new Class2005();

    foreach ($setting['lists_order_status_email_template'] as $template) {
        if ($template['status_id'] != $status || empty($template['email_template'])) continue;

        $emailBody = strtr($template['email_template'], [
            '%FIO%' => $order['fio'],
            '%ORDER_ID%' => $orderId,
            '%STATUS%' => $class2005->getOrderStatusList()[$status]['name'],
        ]);

        $mailer = new MailAssist();
        $mailer->send(
            $order['email'],
            getDomenMail(),
            $emailBody,
            "Изменен статус заказа № {$orderId}",
            $current_catalogue['Catalogue_Name']
        );
    }
}

$time_days = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');
$time_days_en = array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');


function getAvatarName($name)
{
    $num = mb_substr(preg_replace("/[^0-9]/", '', md5($name)), 0, 2);
    $fisrt = mb_substr(trim($name), 0, 1);
    $second = count(explode(" ", $name)) > 1 ? mb_substr(trim(explode(" ", $name)[1]), 0, 1) : "";
    $colors = array('#fbb034', '#c1d82f', '#00a4e4', '#00a4e4', '#8a7967', '#6a737b', '#d20962', '#00a78e', '#7d3f98', '#52565e', '#8db9ca', '#da1884', '#0077c8');
    for ($i = 0; $i < 100; $i++) {
        if ($colors[$num]) break;
        $num -= count($colors);
    }
    if (!$colors[$num]) $num = 0;

    $words = mb_strtoupper($fisrt . $second);
    echo "<span class='avatar-icon' style='width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 21px; color: #fff;background: {$colors[$num]};'>{$words}</span>";
}

function noPjax($idSub)
{
    global $db;
    $res = $db->get_var("SELECT noPjax FROM Subdivision WHERE Subdivision_ID = '{$idSub}'");
    if ($res) return 'noPjax';
}

/**
 * Вывод тэгов
 * @param string $type top|bot
 */
function subTags($type = 'top')
{
    global $current_sub, $action;

    if ($action == 'full') return;
    $tags = null;
    if ($type == 'top') $tags = json_decode(htmlspecialchars_decode($current_sub['tags_links']), true);
    elseif ($type == 'bot') $tags = json_decode(htmlspecialchars_decode($current_sub['tags_links_bottom']), true);

    if (!is_array($tags)) return;

    $tagsList = '';
    foreach ($tags as $tag) {
        if ($tag['name'] && $tag['link']) {
            $tagsList .= "<li class='sub-tag-wrapper'>";
            $tagsList .= "<a class='sub-tag btn-strt-a' href='{$tag['link']}'>";
            $tagsList .= "<span>{$tag['name']}</span>";
            $tagsList .= "</a>";
            $tagsList .= "</li>";
        }
    }
    if (!$tagsList) return;

    $tagsHtml = "<div class='sub-tags-block targs-{$type}'>";
    $tagsHtml .= "<div class='sub-tags-slider-wrapper'>";
    $tagsHtml .= "<button class='sub-tags-btn btn-prev disabled'></button>";
    $tagsHtml .= "<div class='sub-tags-wrapper'>";
    $tagsHtml .= "<div class='sub-tags-track'>";
    $tagsHtml .= "<ul class='sub-tag-list'>{$tagsList}</ul>";
    $tagsHtml .= "</div>";
    $tagsHtml .= "</div>";
    $tagsHtml .= "<button class='sub-tags-btn btn-next'></button>";
    $tagsHtml .= "</div>";
    $tagsHtml .= "<button class='sub-tags-show-more' data-textopen='скрыть' data-textclose='Показать все'>Показать все</button>";
    $tagsHtml .= "</div>";
    return $tagsHtml;
}

//punycode
function punycode_encode($url)
{
    $parts = parse_url($url);

    $out = '';
    if (!empty($parts['scheme']))   $out .= $parts['scheme'] . ':';
    if (!empty($parts['host']))     $out .= '//';
    if (!empty($parts['user']))     $out .= $parts['user'];
    if (!empty($parts['pass']))     $out .= ':' . $parts['pass'];
    if (!empty($parts['user']))     $out .= '@';
    if (!empty($parts['host']))     $out .= idn_to_ascii($parts['host']);
    if (!empty($parts['port']))     $out .= ':' . $parts['port'];
    if (!empty($parts['path']))     $out .= $parts['path'];
    if (!empty($parts['query']))    $out .= '?' . $parts['query'];
    if (!empty($parts['fragment'])) $out .= '#' . $parts['fragment'];

    return $out;
}

function setIconMenu($img)
{
    if (!$img) return '/images/nophoto.png';
    return $img;
}
/**
 * Собирает массив включных способов оплат для списка типов оплат
 *
 * @return array
 */
function getOptionPaymentServis()
{
    global $setting;
    $options = [0 => "Наличными", 'check' => 'Счёт на оплату'];
    $menuServis = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bc/modules/bitcat/manifestes/service_payment.json'), 1);
    foreach ($menuServis as $key => $val) {
        if ($setting[$key]) $options[$key] = $val['name'];
    }

    return $options;
}

function getCheckedServiceSuppliers()
{
    global $setting;
    $servise = [];
    $menuServis = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bc/modules/bitcat/manifestes/service_suppliers.json'), 1);
    foreach ($menuServis as $key => $val) {
        if ($setting[$key]) $servise[] = $key;
    }

    return $servise;
}

/**
 * sql where для вывода объеков в разделе по параметрам
 *
 * создает where для sql запроса на основе указанных в разделе параметров
 *
 * @param int $classID - id компонента
 * @param array|string $params - выбранные параметры
 * @param null|string $prev - префикс для используемой таблицы
 */
function getQuryBySubViewParam($classID, $params, $prev = null)
{
    global $db, $setting_params;

    if (!is_array($params) && !empty($params)) {
        $params = orderArray($params);
    }
    if (!is_array($params)) return null;

    if (!empty($prev) && substr($prev, -1) != '.') $prev .= '.';

    # собираем поля из таблицы чтобы в запрос не полпали несуществующие поля
    $dbRows = $db->get_results("EXPLAIN `Message{$classID}`", ARRAY_A);
    $fields = array();
    if (is_array($dbRows)) {
        foreach ($dbRows as $dbRow) {
            $fields[$dbRow['Field']] = true;
        }
    }

    $query = '';
    foreach ($params as $key => $values) {
        $queryVal = '';
        if ($classID == 2001 && substr($key, 0, 7) == 'params_') {
            $key = substr($key, 7);
            foreach ($values as $value) {
                $queryVal .= ($queryVal ? ' OR ' : null) . "{$prev}`params` LIKE '%{$key}||{$value}|%'";
            }
        } elseif (isset($fields[$key])) {
            foreach ($values as $value) {
                $queryVal .= ($queryVal ? ' OR ' : null) . "{$prev}`{$key}` = '{$value}'";
            }
        }

        if (!empty($queryVal)) {
            $query .= ($query ? ' AND ' : null) . "({$queryVal})";
        }
    }
    return $query;
}

if (!function_exists('array_key_last')) {
    function array_key_last($array)
    {
        return is_array($array) ? array_keys($array)[count($array) - 1] : null;
    }
}

function updateStatusOplaty($orderId, $status)
{
    global $db;
    $db->query("UPDATE Message2005 SET statusOplaty = '{$status}' WHERE Message_ID = '{$orderId}'");
    if (function_exists('updateStatusOplatyCustom')) {
        updateStatusOplatyCustom($orderId, $status);
    }
}

/**
 * Получить настройки блока манифеста
 *
 * @param string $manifestName
 * @param string $blockName
 * @return array|bool
 */
function getSettingsManifestBlock($manifestName, $blockName)
{
    global $setting, $ROOTDIR;
    $pathToManifest = $ROOTDIR . '/bc/modules/bitcat/manifestes/' . $manifestName . '.json';
    $block = json_decode(file_get_contents($pathToManifest), 1);
    $result = [];
    if (is_array($block) && isset($block[$blockName]['row'])) {
        foreach ($block[$blockName]['row'] as $row) {
            $result[$row['name']] = $setting[$row['name']];
        }

        return $result;
    }

    return false;
}



/**
 * релевантность в сортировка товаров при поиске
 *
 * @param string $find - строка поиска 
 * @param string $tablePref - префикс таблицы, например "a."
 * @return string
 */
function getSort($find, $tablePref = null)
{
    if ($tablePref && substr($tablePref, -1) != '.') $tablePref .= '.';

    $find = trim($find);
    $zamArr = array(
        "\\\'" => "",
        "\'" => "",
        "'" => "",
        "\\" => " ",
        "\"" => "",
        "/" => " ",
        ", " => " ",
        ". " => " ",
        " и " => " ",
        " на " => " ",
        "=" => " ",
        " за " => " ",
        " под " => " ",
        " над " => " ",
        " с " => " ",
        " из " => " ",
        " в " => " ",
        " к " => " ",
        " у " => " "
    );
    $find = strtr($find, $zamArr);
    $find = htmlspecialchars(strip_tags(addslashes($find)));
    if ($v == 1) {
        $sort = array();
        foreach (explode(" ", $find) as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            $sort[] = array(
                'name=' => "{$tablePref}`name` = '{$word}'",
                'name_%' => "{$tablePref}`name` LIKE '{$word} %'",
                'name%' => "{$tablePref}`name` LIKE '{$word}%'",
                '%_name_%' => "{$tablePref}`name` LIKE '% {$word} %'",
                '%name%' => "{$tablePref}`name` LIKE '%{$word}%'",
                'art=' => "{$tablePref}`art` = '{$word}'",
                'art%' => "{$tablePref}`art` LIKE '{$word}%'",
                '%art%' => "{$tablePref}`art` LIKE '%{$word}%'"
            );
        }
        $result = '';
        foreach ($sort as $num => $types) {
            $i = 0;
            $case = '';
            foreach ($types as $key => $type) {
                if (!empty($type)) $case .= " WHEN {$type} THEN {$i} ";
                $i++;
            }
            if (!empty($case)) {
                $result .= ($result ? ',' : null) . "(CASE {$case} ELSE {$i} END)";
            }
        }
    } else {
        $sort = array(
            'name=' => '',
            'name_%' => '',
            'name%' => '',
            '%_name_%' => '',
            '%name%' => '',
            'art=' => '',
            'art%' => '',
            '%art%' => ''
        );
        foreach (explode(" ", $find) as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            $sort['name=']    .= ($sort['name=']    ? ' OR ' : null) . "{$tablePref}`name` = '{$word}'";
            $sort['name_%']   .= ($sort['name_%']   ? ' OR ' : null) . "{$tablePref}`name` LIKE '{$word} %'";
            $sort['name%']    .= ($sort['name%']    ? ' OR ' : null) . "{$tablePref}`name` LIKE '{$word}%'";
            $sort['%_name_%'] .= ($sort['%_name_%'] ? ' OR ' : null) . "{$tablePref}`name` LIKE '% {$word} %'";
            $sort['%name%']   .= ($sort['%name%']   ? ' OR ' : null) . "{$tablePref}`name` LIKE '%{$word}%'";
            $sort['art=']     .= ($sort['art=']     ? ' OR ' : null) . "{$tablePref}`art` = '{$word}'";
            $sort['art%']     .= ($sort['art%']     ? ' OR ' : null) . "{$tablePref}`art` LIKE '{$word}%'";
            $sort['%art%']    .= ($sort['%art%']    ? ' OR ' : null) . "{$tablePref}`art` LIKE '%{$word}%'";
        }

        $i = 0;
        $result = '';
        foreach ($sort as $key => $val) {
            if (!empty($val)) $result .= " WHEN {$val} THEN $i ";
            $i++;
        }
        if (!empty($result)) $result = "(CASE {$result} ELSE {$i} END)";
    }

    return $result;
}


/**
 * рекурсивное удаление папок на сервере
 *
 * @param string dir - абсолютный путь к удаляемому каталогу
 * @return null
 */
function recursiveRemoveDir($dir)
{
    $includes = new FilesystemIterator($dir);
    foreach ($includes as $include) {
        if (is_dir($include) && !is_link($include)) {
            recursiveRemoveDir($include);
        } else {
            unlink($include);
        }
    }
    rmdir($dir);
}

/**
 * Очистка данных Multilines
 * 
 * удаляет из массива элементы, у которых не заполнены проверяемые поля
 * 
 * @param array $values данные которые нужно проверить
 * @param array $chekcFields ключи которые обязательно должны быть заполнены
 * 
 * @return array 
 */
function clearMultilinesData($values, $chekcFields)
{
    foreach ($values as $key => $value) {
        $unset = false;

        foreach ($chekcFields as $field) {
            if (empty($value[$field])) {
                $unset = true;
                break;
            }
        }

        if ($unset) {
            unset($values[$key]);
        }
    }
    return $values;
}

function decoderRusSymbolsEmail($emil)
{
    $mailPatern = explode('@', $emil);
    if (preg_match('/[а-я\.]+/msiu', $mailPatern[1])) {
        $emil = $mailPatern[0] . '@' . idn_to_ascii($mailPatern[1]);
    }
    return $emil;
}

/**
 * @param array $option
 * @param string $option['moreRequest']
 * @param bool $option[btn]
 * @return string
 */
function htmlAnyForm($value, $option = array())
{
    global $db;
    $moreRequest = $option['moreRequest'] ? $option['moreRequest'] : '';
    $formParam = explode('/', $value);
    if ($option['btn']) {
        switch ($formParam[3]) {
            case 197:
                $htmlForm = "<a href='/feedback/?isNaked=1{$moreRequest}' id='link-feedback' title='" . getLangWord('link_mail', 'Напишите нам') . "' data-rel='lightcase' data-maxwidth='380' data-groupclass='feedback modal-form' data-metr='mailtoplink'>" . getLangWord('link_mail', 'Напишите нам') . "</a>";
                break;
            case 2013:
                $htmlForm = "<a href='/callme/?isNaked=1{$moreRequest}' id='link-callme' title='" . getLangWord('link_call', 'Обратный звонок') . "' data-rel='lightcase' data-maxwidth='390' data-groupclass='callme modal-form' data-metr='calltoplink'>" . getLangWord('link_call', 'Обратный звонок') . "</a>";
                break;
            default:
                $formDB = $db->get_row("SELECT * FROM Message2059 WHERE Message_ID = {$formParam[4]}", ARRAY_A);

                $request = $formDB['keyid'] ? "&keyid={$formDB['keyid']}" : "&msg={$formDB['Message_ID']}";

                $htmlForm = "<a href='/system/forms/?isNaked=1{$request}{$moreRequest}' title='{$formDB['name']}' data-rel='lightcase' data-maxwidth='650' data-groupclass='form-generated-id{$formDB['Message_ID']}' data-metr='genform-{$formDB['Message_ID']}-open'>{$formDB['name']}</a>";
                break;
        }
    } else {
        $htmlForm  = nc_objects_list($formParam[0], $formParam[1], "&recNum=1&nc_ctpl=2112" . str_replace('&amp;', '&', "{$formParam[2]}{$moreRequest}"));
    }

    return $htmlForm;
}
/**
 * Вывод ссылок городов для индексации
 * 
 * @return string
 */
function getCityListSEO()
{
    global $cityvars, $current_catalogue, $setting;

    $domen = $current_catalogue['Domain'];
    $current_domen = explode('.', $_SERVER['HTTP_HOST']);

    if (!$setting['cities_links_seo'] || (count($current_domen) > 2 && $current_domen[0] != 'www')) return '';

    $html = array_reduce($cityvars, function ($acum, $city) use ($domen, $current_catalogue) {
        $alias = $city['keyword'] . '.' . $domen;
        if (stristr($current_catalogue['Mirrors'], $alias)) {
            $acum .= "<a rel='nofollow' href='//{$city['keyword']}.{$domen}'>{$city['name']}</a>";
        } else {
            $acum .= "<a rel='nofollow' href='//{$domen}'>{$city['name']}</a>";
        }
        return $acum;
    }, '');

    return $html ? "<div class='seo target_cities none'>{$html}</div>" : '';
}

function k_renderTemplate($view, $params = [])
{
    if (!empty($params)) extract($params);
    if (file_exists($view)) {
        ob_start();
        include $view;
        $result = ob_get_flush();
        ob_end_clean();
        return $result;
    } else {
        throw new \Exception(sprintf('Файл %s не найден.', $view));
    }
}

function getFillterForName()
{
    global $current_sub, $AUTH_USER_ID, $setting;

    $hiddenFillterInSub = ['/search/']; // TO_DO перенести в глобальную видемость
    if (
        in_array($current_sub['Hidden_URL'], $hiddenFillterInSub)
        || $current_sub['fillter_for_name_off']
        || !$setting['fillterForNameOn']
        || !$current_sub['filter_for_name']
    ) return '';

    $fillterVal = orderArray($current_sub['filter_for_name']) ?: [];
    ksort($fillterVal);

    $en = 0;
    $litterOld = $filter = '';
    foreach ($fillterVal as $name => $params) {
        if (!isset($params['on']) || $params['on'] != '1') continue;
        $name =  mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
        $letter = mb_substr($name, 0, 1);
        if (preg_match('/[А-ЯЁA-Z]/', $letter) == 0) continue;
        if ($setting['fillterForNameOnlyRu'] && preg_match('/[А-ЯЁ]/', $letter) == 0) continue;

        if ($letter != $litterOld) {
            $litterOld = $letter;

            if ($filter != '') $filter .= "</ul></li>";
            preg_match('/[A-Z]/', $letter, $match);
            if (empty($match) && $en != 0) {
                $filter .= "<span class='delimiter'>/</span>";
                $en = 0;
            }
            $en = (!empty($match) ? 1 : 0);
            $filter .= "<li class='filter-letter'>{$letter}<ul class='filter-names'>";
        }
        $filter .= "<li class='filter-name'><a 1 href='{$current_sub['Hidden_URL']}?name={$name}'>" . ($params['text'] ?: $name) . "</a></li>";
    }

    $res = "<div class='filter-for-name-box'>
            <ul class='filter-for-name'>
                {$filter}
                    </ul>
                </li>
            </ul>
    </div>";

    return $res;
}
/**
 * Кеш фильтра для сео
 * 
 * @param string $link ссылка на сборку фильтра из раздела
 * @return string
 */
function setCacheFilter($link)
{
    global $catalogue, $pathInc, $DOCUMENT_ROOT, $current_sub, $AUTH_USER_ID;

    if ($AUTH_USER_ID) return '';

    parse_str(parse_url($link)['query'], $query);

    if (is_array($query)) {
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
        foreach ($query as $key => $value) {
            $inSub = isset($keyToSub[$key]) || substr($key, 0, 4) == 'sub_';
            if (isset($keyMap[$key])) $key = $keyMap[$key];

            if ($inSub) {
                $params['sub'][$key] = $value;
            } else {
                $params[$key] = $value;
            }
        }
    }

    if (empty($params['searchInCur'])) return '';

    $pathCache = $DOCUMENT_ROOT . $pathInc . '/files/' . $params['searchInCur'] . '/';
    $pathFileCache = $pathCache . 'filter.cache.html';

    if ($current_sub['Subdivision_ID'] == $params['searchInCur']) {
        $cache = $current_sub['cachItemParam'] ? orderArray($current_sub['cachItemParam']) : [];
    } else {
        $params['searchInCur'] += 0;
        $cachItemParam = $db->get_var("SELECT `cachItemParam` FROM Subdivision WHERE Subdivision_ID = '{$params['searchInCur']}' AND Catalogue_ID = '{$catalogue}'");
        $cache = $cachItemParam ? orderArray($cachItemParam) : [];
    }

    $result = '';
    if (isset($cache['filter_items_cache']) && file_exists($pathFileCache)) {
        $result = file_get_contents($pathFileCache);
    } else {
        $filter = new Class2041($params['id'], $catalogue, $params);
        foreach ($filter->values['main']['values'] as $id => $fields) {
            $result .= "<h4>{$filter->fltDB['data']['name'][$id]}</h4>";
            if ($filter->fltDB['data']['otdo'][$id]) {
                $result .= "<span>от {$fields['min']} до {$fields['max']}</span>";
            } else {
                $result .= "<ul>";
                foreach ($fields as $value) {
                    $result .= "<li><label><input type='checkbox' name='{$id}[]' value='{$value}'>{$value}</label></li>";
                }
                $result .= "</ul>";
            }
        }
        foreach ($filter->values['custom']['values'] as $id => $fields) {
            $result .= "<h4>{$filter->customFields[$id]['name']}</h4>";
            if ($filter->customFields[$id]['otdo_filter']) {
                $result .= "<span>от {$fields['min']} до {$fields['max']}</span>";
            } else {
                $result .= "<ul>";
                foreach ($fields as $value) {
                    $result .= "<li>{$value}</li>";
                }
                $result .= "</ul>";
            }
        }
        $result = $result ? "<div class='none'>{$result}</div>" : '';
        @mkdir($pathCache, 0775, true);
        if (file_put_contents($pathFileCache, $result)) {
            setSeoCache($params['searchInCur'], ['filter_items_cache' => 1]);
        }
    }
    return $result;
}

/**
 * Переиндексирует массив согласно выбраному значению ключа с сохранениям данных
 * @param array $array
 * @param string $key ключ выбраного со значениям для индексации 
 * 
 * @return array
 */
function arrayValuesKeyA($array, $key)
{
    $result = [];
    if (is_array($array)) {
        foreach ($array as $val) {
            $result[$val[$key]] = $val;
        }
    }
    return $result;
}



/**
 * Переворачивает массив фотографий в массиве $_FILES согласно данным EXIF
 * @param null
 * @return null
 */
function normalizeImageRotateFromFiles()
{
    if (!empty($_FILES)) {
        foreach ($_FILES as $filed) {
            $count = count($filed['tmp_name']);
            for ($i = 0; $i < $count; $i++) {
                if ($filed['type'][$i] == 'image/jpeg') {
                    normalizeImageRotateWithEXIF($filed['tmp_name'][$i]);
                }
            }
        }
    }
}

/**
 * Переворачивает одну фотографию согласно данным EXIF
 * @param string $imagePath
 * @return bool
 */
function normalizeImageRotateWithEXIF(string $imagePath): bool
{
    $image = imagecreatefromjpeg($imagePath);

    if ($image === false) return false;

    $orientation = exif_read_data($imagePath)['Orientation'] ?? null;

    $rotates = [
        3 => 180,
        6 => -90,
        8 => 90,
    ];

    if (isset($rotates[$orientation])) {
        $rotatedImg = imagerotate($image, $rotates[$orientation], 0);
        imagejpeg($rotatedImg, $imagePath);
        imagedestroy($image);
        imagedestroy($rotatedImg);
    }

    return true;
}

function openGraph($params = [])
{
    global $current_catalogue, $classID, $action, $f_Message_ID, $f_title, $current_sub, $pathInc, $db, $HTTP_HOST, $catalogue;

    $data = [
        'og:locale' => 'ru_RU',
        'og:type' => 'website',
        'og:site_name' => $current_catalogue['Catalogue_Name'],
        'og:description' => $params['description'],
        'og:url' => ($current_catalogue['https'] ? "https" : "http") . ':/' . $HTTP_HOST . $_SERVER['REQUEST_URI']
    ];

    if ((($classID == 2001 || $classID == 2021) && $action == 'full')) {
        $itemObj = Class2001::getItemById($f_Message_ID);
        $data['og:image'] = $itemObj->photos[0]['path'];
        $data['og:title'] = $itemObj->name;
    } else {
        $data['og:title'] = $f_title;
        if (!empty($current_sub['img_url'])) {
            $data['og:image'] = $current_sub['img_url'];
        } else {
            $img_logo = $db->get_var("SELECT SUBSTRING_INDEX(file, ':', -1) FROM Message2047 WHERE Catalogue_ID = '{$catalogue}' limit 1");
            if ($img_logo) {
                $data['og:image'] = $pathInc . '/files/' . $img_logo;
            }
        }
    }

    if ($current_catalogue['https'] && $data['og:image']) {
        $data['og:image:secure_url'] = 'https://' . $HTTP_HOST . $data['og:image'];
    }

    $html = '';

    foreach ($data as $property => $content) {
        $html .= "<meta property='{$property}' content='{$content}' />";
    }

    return $html;
}

/**
 * Отправить сообщение в WhatsApp
 * @param string $phone
 * @param string $message
 */
function sendWhatsappMessage($phones, $message, $APIKey = NULL, $channelId = NULL)
{
    global $KORZILLA_WAZZUP_API_KEY, $KORZILLA_WAZZUP_CHANNEL_ID;

    $APIKey = is_null($APIKey) ? $KORZILLA_WAZZUP_API_KEY : $APIKey;
    $channelId = is_null($channelId) ? $KORZILLA_WAZZUP_CHANNEL_ID : $channelId;

    //define("WAZZUP_API_KEY", "d64bfa464ed948049083ff917e5eba72");
    //define("WAZZUP_CHANNEL_ID", "d310edec-576c-4f32-b82a-765128349d9f");

    $headers = [
        "Authorization: Bearer " . $APIKey,
        "Content-Type: application/json",
    ];

    foreach (explode(',', $phones) as $phone) {
        $requestData = [
            "channelId" => $channelId,
            "chatId" => trim($phone),
            "chatType" => "whatsapp",
            "text" => $message,
        ];

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, "https://api.wazzup24.com/v3/message");
        curl_setopt($connection, CURLOPT_POST, true);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($requestData));

        $response = curl_exec($connection);
        //лог чтобы доказывать клиентам что сообщение было отправлено
        file_put_contents('/var/www/krza/data/www/krza.ru/b/ruslan/wathsapp.log', print_r(array(date('Y-m-d H:i:s'), $response, $requestData, $headers), 1), FILE_APPEND);

        curl_close($connection);
    }
};

function initPopUp()
{
    global $db, $catalogue, $current_sub, $settings;
    if (!$settings) $settings =  getSettings();
    if (!$settings['popup_window']) return '';
    $popUp = $db->get_results(
        "SELECT
            Title,
            content,
            Message_ID AS id,
            interval_show,
            `delay`
        FROM 
            Message2259
        WHERE 
            Catalogue_ID = {$catalogue}
            AND (
                    all_page = 1 
                    OR id_page LIKE '%,{$current_sub['Subdivision_ID']},%'
                )
            AND Checked = 1",
        ARRAY_A
    );

    if (empty($popUp)) return '';
    $popUpData = [];
    foreach ($popUp as $data) {
        $popUpData[$data['id']] = $data;
    }
    $popUpData = json_encode($popUpData);
    return "
        <link href='/css/popup.css' rel='Stylesheet' type='text/css'>
        <script type='text/javascript' src='/js/micromodal.min.js'></script>
        <script type='text/javascript'>
            window.popup_kz = {$popUpData}
        </script>
        <script type='text/javascript' src='/js/popup.js'></script>
    ";
}

/**
 * Сортировка по наличию в корзине 
 * @param array $items Массив товаров
 */
function sortBasket(&$items)
{
    uasort($items, function ($item1, $item2) {
        if ($item1['stock'] == 0 || $item2['stock'] == 0) {
            return ($item2["stock"] - $item1["stock"]);
        }
        return 0;
    });
}
/**
 * @param array $file значения $_FILES проверяемого файла
 * @param string $field поле в базе
 * @param int $class_id Message id
 * 
 * @return bool
 */
function fileSecurityCheck($file, $field, $class_id)
{
    global $db;
    $field = str_replace(['f_', 'bc_'], '', $field);
    $fields = array_column($db->get_results(
        "SELECT 
            `Format`,
            `Field_Name`
        FROM 
            Field
        WHERE 
            (
                TypeOfData_ID = 6
                OR TypeOfData_ID = 11
            )
            AND Class_ID = {$class_id}",
        ARRAY_A
    ), 'Format', 'Field_Name');

    if (!isset($fields[$field])) return false;
    if (empty($fields[$field])) return true;

    $mimeTypes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bc/modules/default/mime.types.json'), 1);
    $mime = $mimeTypes[pathinfo($file['name'], PATHINFO_EXTENSION)];

    return (strpos($fields[$field], $mime[0]) !== false || strpos($fields[$field], $mime[1]) !== false);
}

/**
 * Получить список id объектов имеющих тэг
 * 
 * @param int|string $classID;
 * @param array|string $tag названия тэгов
 * 
 * @return int[]|null
 */
function getObjectIdsHasTag($classID, $tag)
{
    $tagProvider = new \App\modules\Korzilla\Tag\Provider();

    $filter = $tagProvider->filterGet();
    $filter->objectType[] = $classID;
    $filter->tag = is_array($tag) ? $tag : [$tag];

    if (!$bindList = $tagProvider->bindGetList($filter)) {
        return null;
    }

    $result = [];
    foreach ($bindList as $bind) {
        $result[] = $bind->object_id;
    }

    return $result;
}

/**
 * Получить список тэгов содержащихся в разделе
 * 
 * @param int|string $classID
 * @param int $subID
 * 
 * @return array|null
 */
function getTagsFromSubdivision($classID, $subID)
{
    global $db;

    $tagProvider = new \App\modules\Korzilla\Tag\Provider();

    $filter = $tagProvider->filterGet();
    $filter->objectType[] = $classID;

    if (!$bindList = $tagProvider->bindGetList($filter)) {
        return null;
    }

    /** @var \App\modules\Korzilla\Tag\Bind\Bind $bind*/
    $groupBinds = array_reduce($bindList, function ($result, $bind) {
        $result[$bind->object_id][] = $bind;
        return $result;
    }, []);
    $sql = sprintf(
        "SELECT `Message_ID` FROM `%s` WHERE `Subdivision_ID` = %d AND `Message_ID` IN (%s)",
        'Message' . $classID,
        $subID,
        implode(',', array_keys($groupBinds))
    );

    $objectExists = array_flip($db->get_col($sql) ?: []);

    $tags = [];
    foreach ($groupBinds as $objectId => $binds) {
        if (!isset($objectExists[$objectId])) {
            continue;
        }

        /** @var \App\modules\Korzilla\Tag\Bind\Bind $bind*/
        foreach ($binds as $bind) {
            if (!isset($tags[$bind->tag_id])) {
                $tags[$bind->tag_id]['count'] = 1;
                continue;
            }
            $tags[$bind->tag_id]['count']++;
        }
    }

    if (!$tags) {
        return null;
    }

    $filter = $tagProvider->filterGet();
    $filter->tagId = array_keys($tags);

    $tagsObjects = $tagProvider->tagGetList($filter);

    foreach ($tagsObjects as $tag) {
        $tags[$tag->Message_ID]['tag'] = $tag;
    }

    return $tags;
}

/** 
 * Получить html тэгов раздела
 * 
 * @param array $tags ['tag' => \App\modules\Korzilla\Tag\Tag, 'count' => int]
 * @param string $pageUrl 
 * @param string|null $activeTag 
 * @param int|null $totalObjects
 * 
 * @return string|null
 */
function getSubdivisionTagsHtml($tags, $pageUrl, $activeTag = null, $totalObjects = null)
{
    if (empty($tags)) {
        return null;
    }

    $urlSeporator = strpos($pageUrl, '?') === false ? '?' : '&';

    $totalObjectsHtml = '';
    if ($totalObjects) {
        $totalObjectsHtml = "<span class='tag-objects-count'>{$totalObjects}</span>";
    }

    $activeClass = !$activeTag ? 'active' : '';

    $html = "<div class='tag-objects-list'>";
    $html .= "<a class='btn-strt-a tag-objects {$activeClass}' href='{$pageUrl}'>";
    $html .= "<span class='tag-objects-title'>Все</span>";
    if ($totalObjects) {
        $html .= "<span class='tag-objects-count'>{$totalObjects}</span>";
    }
    $html .= "</a>";

    foreach ($tags as $tag) {
        if ($tag instanceof \App\modules\Korzilla\Tag\Tag) {
            $count = null;
        } else {
            $count = $tag['count'];
            $tag = $tag['tag'];
        }

        $href = $pageUrl . $urlSeporator . "tag={$tag->tag}";

        $activeClass = $activeTag === $tag->tag ? 'active' : '';

        $html .= "<a class='btn-strt-a tag-objects {$activeClass}' href='{$href}'>";
        $html .= "<span class='tag-objects-title'>{$tag->tag}";
        if ($count) {
            $html .= "<b class='tag-objects-count'>{$count}</b>";
        }
        $html .= "</span></a>";
    }

    $html .= "</div>";

    return $html;
}


/** 
 * присвоить объекту "Ключевое слово" (если создан вручную)
 * 
 * @param int $message ID объекта
 * @param int $classID ID компонента
 * @param string $name имя объекта
 * @param array $keys массив свойств (имя объекта, цвет, размер, артикул, название варианта, дата т.д.)
 * 
 * @return null
 */
function setKeywordObj($message, $class_id, $variables)
{
    global $db;
    if (!$variables) return false;

    $name = implode(" ", $variables);
    $keyword = encodestring(trim($name), 1);
    if (!$db->query("update Message{$class_id} set Keyword = '{$keyword}' where Message_ID = '{$message}'")) {
        for ($i = 2; $i <= 20; $i++) {
            if ($db->query("update Message{$class_id} set Keyword = '{$keyword}_{$i}' where Message_ID = '{$message}'")) break;
        }
    }
}


/**
 * Возвращает доменную почту
 *
 * @param  string $mailName
 * @return string
 */
function getDomenMail(string $mailName = 'info'): string
{
    $hostParts = array_reverse(explode('.', $_SERVER['HTTP_HOST']));
    $host = $hostParts[1] . '.' . $hostParts[0];
    return $mailName . '@' . $host;
}


/** 
 * получить код единиц измерения по наименованию единиц измерения
 * 
 * @param string $edzim наименование ед.измерения
 * 
 * @return int[]|bool
 */
function getEdzimCode($edzim)
{
    global $setting;

    if (!is_array($setting['lists_edizm'])) return false;
    foreach ($setting['lists_edizm'] as $e) {
        if (trim($e['name']) == trim($edzim)) return $e['keyword'];
    }
    return false;
}

function genPass($length = 6)
{
    $chars = "1234567890";
    $size = strlen($chars) - 1;
    $password = '';
    while ($length--) {
        $password .= $chars[rand(0, $size)];
    }
    return $password;
}



/**
 * Возвращает массив изранных товаров
 *
 * @param  string $mailName
 * @return string
 */
function get_favorites()
{
    global $current_user, $AUTH_USER_ID;
    $favarits = array();
    if ($AUTH_USER_ID) {
        if ($current_user['favarits']) {
            $favarits = explode(';', mb_substr($current_user['favarits'], 1, -1));
        }
    } elseif ($_SESSION['favarits']) {
        $favarits = $_SESSION['favarits'];
    }
    return $favarits;
}

function getMapSubdivision()
{
    global $db, $catalogue;
    $subdivisions = $db->get_results(
        "SELECT 
            Subdivision_ID, 
            Parent_Sub_ID, 
            Subdivision_Name, 
            Catalogue_ID, 
            Priority 
        FROM 
            Subdivision 
        WHERE 
            Catalogue_ID = {$catalogue} 
			AND Hidden_URL NOT LIKE '/index/[a-z]%'
			AND (systemsub != 1  OR (EnglishName = 'index' AND Parent_Sub_ID = '0'))
        ORDER BY 
            Hidden_URL
		",
        ARRAY_A
    );
    $subdivisionsSort = [];
    foreach ($subdivisions as  $subdivision) {
        $subdivisionsSort[$subdivision['Subdivision_ID']] = [
            'parent_id' => $subdivision['Parent_Sub_ID'],
            'name' => $subdivision['Subdivision_Name'],
            'priority' => $subdivision['Priority'],
        ];

        if ($subdivision['Parent_Sub_ID'] == 0) continue;

        if (!isset($subdivisionsSort[$subdivision['Parent_Sub_ID']])) {
            unset($subdivisionsSort[$subdivision['Subdivision_ID']]);
            continue;
        }

        $subdivisionsSort[$subdivision['Parent_Sub_ID']]['childrens'][$subdivision['Subdivision_ID']] = (int) $subdivision['Priority'];
    }
    unset($subdivisions);

    uasort($subdivisionsSort, function ($a, $b) {
        return ($a['priority'] - $b['priority']);
    });
    $subdivisionsSort2 = [];
    foreach ($subdivisionsSort as $index => $sub) {
        if (isset($sub['parent_id']) && $sub['parent_id'] == 0) {
            $subdivisionsSort2[$index] = ['name' => $sub['name'], 'lvl' => 0];
            if (!empty($sub['childrens'])) {
                $subdivisionsSort2[$index]['haveChilds'] = 1;
                $subdivisionsSort2 += getSortSubdivision($sub['childrens'], 1, $subdivisionsSort);
            }
        }
    }

    return $subdivisionsSort2;
}

function getSortSubdivision($childrenSub, $lvl = 1, $subdivisionsSort)
{
    $result = [];
    asort($childrenSub);
    foreach ($childrenSub as $subID => $priority) {
        $sub = $subdivisionsSort[$subID];
        $result[$subID] = ['name' => $sub['name'], 'lvl' => $lvl];
        if (!empty($sub['childrens'])) {
            $result[$subID]['haveChilds'] = 1;
            $result += getSortSubdivision($sub['childrens'], ($lvl + 1), $subdivisionsSort);
        }
    }

    return $result;
}


function conclusionFromSectionTab()
{
    global $db, $catalogue;

    $catsubArr = $db->get_results("select a.Subdivision_Name as subname, a.Subdivision_ID as subid, a.Hidden_URL as psubid,  a.Checked
    from Subdivision as a, Sub_Class as b
    where a.Subdivision_ID = b.Subdivision_ID
        AND a.Catalogue_ID = '$catalogue'
        AND a.Hidden_URL NOT LIKE '%search%'
        AND a.Hidden_URL NOT LIKE '%404%'
        AND a.Hidden_URL NOT LIKE '%zone%'
        AND a.Hidden_URL NOT LIKE '%blockofsite%'
        AND a.Hidden_URL NOT LIKE '%sitemap%'
        AND a.Hidden_URL NOT LIKE '%excel%'
        AND a.Hidden_URL NOT LIKE '%profile%'
        AND a.Hidden_URL NOT LIKE '%settings%'
        AND a.Hidden_URL NOT LIKE '%cart/success%'
        AND a.Hidden_URL NOT LIKE '%cart/fail%'
        ORDER BY a.Hidden_URL, a.Priority", ARRAY_A);

    $data = ['' => '- не выбран -'];
    foreach ($catsubArr as $cs) {
        $o = '';
        $c = substr_count($cs['psubid'], "/") - 2;
        for ($i = 1; $i <= $c; $i++) {
            $o .= "-";
        }
        $data[$cs['subid']] = "{$o} {$cs['subid']}. {$cs['subname']}";
    }

    return $data;
}

function queryWhereSubdivision($id, $all = 0, $resultSub = [], $recursionID = []): string
{
    global $db, $classID, $catalogue, $AUTH_USER_ID;

    // if (isset($resultSub[$id])) return $resultSub[$id];
    // if ($AUTH_USER_ID == 6) {
    //     file_put_contents('/var/www/krza/data/www/krza.ru/a/xdomo/query123.txt', print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1), 1), FILE_APPEND);
    // }
    $result = [];

    $res = "a.Subdivision_ID = {$id} OR a.Subdivision_IDS LIKE '%,{$id},%'";

    $sub = $db->get_row(
        "SELECT
            `Subdivision_ID` AS id,
            `find`,
            `sub_find`,
            `strictFind`,
            `view_obj_by_param`,
            `sub_find`,
            `outallitem`
        FROM
            Subdivision
        WHERE
            Subdivision_ID = ({$id})",
        ARRAY_A
    );


    if ($sub['sub_find']) {
        if (in_array($id, $recursionID)) return $res;

        $recursionID[] = $id;
        $subFindResult = [];
        foreach (explode(',', trim($sub['sub_find'], ',')) as $sub_id) {
            $standardSubID = (int) trim($sub_id);
            if (!$standardSubID) continue;

            $respons = queryWhereSubdivision((int) $standardSubID, 0, $resultSub, $recursionID);
            if (isset($respons) && !empty($respons)) $subFindResult[] = '(' . $respons . ')';
        }
        if (isset($subFindResult) && !empty($subFindResult)) $result[] =  '(' . implode(' OR ', $subFindResult) . ')';
    }

    // Вывод по параметрам
    if (!empty($sub['view_obj_by_param'])) {
        $result[] = getQuryBySubViewParam($classID, $sub['view_obj_by_param'], 'a');
    }
    // Вывод по фразе
    if ($sub['find'] || $sub['strictFind']) {
        $result[] = getFindQuery($sub['find'], 0, 0, 'a.', $sub['strictFind']);
    }

    if (count($result)  > 0) $res .=  " OR (" . implode(' AND ', $result) . ")";

    if ($all || $sub['outallitem']) {
        $childrenResult = [$res];
        $childrenSub = $db->get_col(
            "SELECT 
                Subdivision_ID 
            FROM 
                Subdivision 
            WHERE 
                Catalogue_ID = '{$catalogue}' 
                AND Parent_Sub_ID = {$id}"
        ) ?: [];
        foreach ($childrenSub as $childrenID) {
            $childrenResult[] = '(' . queryWhereSubdivision((int) $childrenID, $all, $resultSub, $recursionID) . ')';
        }

        $res = implode(' OR ', $childrenResult);
    }

    $resultSub[$id] = $res;
    return $res;
}


/**
 * Удалить пользователя по ID
 * 
 * @param int $userId
 * 
 * @return bool
 */
function deleteUserById($userId)
{
    global $nc_core;

    $nc_core->user->delete_by_id($userId);

    $user = $nc_core->user->get_by_id($userId, 'User_ID', true);

    return empty($user);
}

/**
 * Рендер html письма
 *
 * @param array $params
 * @param string $componentSrc
 * @return string
 */
function renderHTMLComponent($params, $componentSrc)
{
    global $AUTH_USER_ID;
    ob_start();
    if (is_array($params)) {
        extract($params);
    }

    if (!file_exists($componentSrc)) {
        throw new Exception(printf("File not exist %s", $componentSrc), 500);
    }
    require $componentSrc;
    return ob_get_clean();
}

function injectionTestString(string $haystack)
{
    $blackList = [
        'concat', 'require', 'socket', 'gethostbyname', '${', 'response', 'write', '../', 'passwd', '/etc/', '%c0%af', 'echo', 'eval', 'exec',
        'bxss.me', 'sleep'
    ];

    foreach ($blackList as $value) {
        if (mb_strripos($haystack, $value) !== false) return true;
    }

    return false;
}
function nc_browse_path_range_krz($from, $to, $browse_template, $reverse = 0, $show = 0) {
    global $REQUEST_URI, $f_title;
    global $admin_mode, $admin_url_prefix;
    global $current_catalogue, $current_sub, $current_cc, $cc_array;
    global $parent_sub_tree, $sub_level_count;
    global $titleTemplate, $action, $message, $classID;
    global $user_table_mode, $db, $SUB_FOLDER, $_db_cc, $nc_core;

    $routing_module_enabled = nc_module_check_by_keyword('routing');

    $current_page_path = urldecode(strtok($REQUEST_URI, '?'));

    //FIXME удалить если для полного отображения по ключевому слову будет определен $current_cc не по источнику зеркала
    if ($action == 'full' && $_db_cc != $current_cc['Sub_Class_ID']) {
        $current_cc_old = $current_cc;
        $current_cc = $nc_core->sub_class->get_by_id($_db_cc);
    }

    if ($to > $sub_level_count) {
        $to = $sub_level_count;
    }

    if ($from < -1) {
        $from = -1;
    }

    $result = $browse_template['prefix'];

    $result_array_name = array();
    $result_array_url = array();

    if ($show == 0 && $current_catalogue['Title_Sub_ID'] == $current_sub['Subdivision_ID']) {
        $from++;
    }

    for ($i = $to; $i > $from; $i--) {
        $result_array_name[] = $parent_sub_tree[$i]['Subdivision_Name'];
        if ($admin_mode) {
            $result_array_url[] = $admin_url_prefix . "?catalogue=" . $parent_sub_tree[$i]['Catalogue_ID']
                . ($parent_sub_tree[$i]["Subdivision_ID"] ? "&amp;sub=" . $parent_sub_tree[$i]["Subdivision_ID"] : "");
        }
        else {
            if (isset($parent_sub_tree[$i]["ExternalURL"]) && ($ext_url = $parent_sub_tree[$i]["ExternalURL"])) {
                $result_array_url[] = (strchr($ext_url, ":") || substr($ext_url, 0, 1) == "/")
                    ? $ext_url
                    : $SUB_FOLDER . $parent_sub_tree[$i]['Hidden_URL'] . $ext_url;
            }
            else if ($routing_module_enabled && isset($parent_sub_tree[$i]['Subdivision_ID'])) {
                $result_array_url[] = (string)nc_routing::get_folder_path($parent_sub_tree[$i]['Subdivision_ID']);
            }
            else {
                $result_array_url[] = $SUB_FOLDER . $parent_sub_tree[$i]['Hidden_URL'];
            }
        }
    }

    switch ($show) {
        case 0:
            if ($current_cc['Sub_Class_ID'] != $cc_array[0] && $current_cc['Checked']) {
                $result_array_name[] = $current_cc['Sub_Class_Name'];
                if (isset($current_cc["ExternalURL"]) && ($ext_url = $current_cc["ExternalURL"])) {
                    $result_array_url[] = ((strchr($ext_url, ":") || substr($ext_url, 0, 1) == "/")
                            ? $ext_url
                            : $SUB_FOLDER . $current_cc[$i]['Hidden_URL'] . $ext_url) . ".html";
                }
                else if ($routing_module_enabled) {
                    $result_array_url[] = (string)nc_routing::get_infoblock_path($current_cc['Sub_Class_ID']);
                }
                else {
                    $result_array_url[] = $SUB_FOLDER . $current_sub['Hidden_URL'] . $current_cc['EnglishName'] . ".html";
                }
            }
            break;
        case 1:
            if ($current_cc['Checked']) {
                $result_array_name[] = $current_cc['Sub_Class_Name'];
                if (isset($current_cc["ExternalURL"]) && ($ext_url = $current_cc["ExternalURL"])) {
                    $result_array_url[] = ((strchr($ext_url, ":") || substr($ext_url, 0, 1) == "/")
                            ? $ext_url
                            : $SUB_FOLDER . $current_cc[$i]['Hidden_URL'] . $ext_url) . ".html";
                }
                else if ($routing_module_enabled) {
                    $result_array_url[] = (string)nc_routing::get_infoblock_path($current_cc['Sub_Class_ID']);
                }
                else {
                    $result_array_url[] = $SUB_FOLDER . $current_sub['Hidden_URL'] . $current_cc['EnglishName'] . ".html";
                }
            }
            break;
    }

    if ($titleTemplate && $action == 'full') {
        $result_array_name[] = $f_title;
        $result_array_url[] = $current_page_path;
    }

    //BREADCUMB
    (new App\modules\Korzilla\JSON_LD\JsonLdFactory())->BreadcrumbsList()->setData($result_array_url,  $result_array_name);


    if (!$reverse) {
        $result_array_name = array_reverse($result_array_name);
        $result_array_url = array_reverse($result_array_url);
    }

    $array_result = array();
    for ($j = $from, $i = count($result_array_name) - 1; $i > -1; $i--) {

        if ($reverse) {
            $j++;
        }
        else {
            $j = $i + ($from + 1);
        }

        if (isset($parent_sub_tree[$j]["Subdivision_ID"]) && $current_sub["Subdivision_ID"] == $parent_sub_tree[$j]["Subdivision_ID"]) {
            if ($browse_template['active_link'] && ($result_array_url[$j] == $current_page_path)) {
                $array_result[$j] = $browse_template['active_link'];
            }
            else {
                $array_result[$j] = $browse_template['active'];
            }
        }
        else {
            $array_result[$j] = $browse_template['unactive'];
        }

        // $array_result[$j] = str_replace("%NAME", $result_array_name[$i], $array_result[$j]);
        $array_result[$j] = str_replace("%URL", $result_array_url[$i], $array_result[$j]);

        # проверка на мультиязычность   код говна, кому не лень напишите по нормальному.
        if (!$parent_sub_tree[count($result_array_name)-1]['EnglishName']) {
            if ($j == 0) {
                $array_result[$j] = str_replace("%NAME", $result_array_name[$i], $array_result[$j]);
            } else {
                $array_result[$j] = str_replace("%NAME", getLangWord("lang_sub_".$parent_sub_tree[$j-1]['EnglishName'], $result_array_name[$i]), $array_result[$j]);
            }
        } else {
            $array_result[$j] = str_replace("%NAME", getLangWord("lang_sub_".$parent_sub_tree[$j]['EnglishName'], $result_array_name[$i]), $array_result[$j]);
        }
    }

    $result .= implode($browse_template['divider'], $array_result);

    if (isset($browse_template['suffix'])) {
        $result .= $browse_template['suffix'];
    }
    //FIXME удалить если для полного отображения по ключевому слову будет определен $current_cc не по источнику зеркала
    if (isset($current_cc_old)) {
        $current_cc = $current_cc_old;
    }
    return $result;
}

function get_description() {
    global $setting, $nc_core, $current_sub, $action, $f_ncDescription, $classID, $itemObj, $cityname;

    switch (true) {
        case $action != 'full' && empty($current_sub['Description']) && !empty($current_sub['Description2']):
            $descrpath = $current_sub['Description2'];
            break;
        case $action != 'full' && !empty($nc_core->page->get_description()) || $action == 'full' && !empty($f_ncDescription):
            $descrpath = $nc_core->page->get_description();
            break;
        case $action == 'full' && !empty($current_sub['DescriptionObj']):
            $descrpath = $current_sub['DescriptionObj'];
            break;
        case $action == 'full' && $classID == 2001 && !empty(trim($setting['SEODescriptionObj'])):
            $descrpath = $setting['SEODescriptionObj'];
            break;
        case $action == 'full' && $classID == 2001 && empty($current_sub['DescriptionObj']):
            $descrpath = $itemObj->text;
            break;
        default:
            $descrpath = '';
            break;
    }
    if (!empty($descrpath) && !empty($cityname) && !preg_match("/(%CITYNAME)|(%NOCITY%)/", $descrpath)) {
        $descrpath .= " %CITYNAME%";
    }
    if (!empty($descrpath)) {
        $descrpath = \Korzilla\Replacer::replaceText(strip_tags($descrpath));
    }
    return $descrpath;
}

function cdekConvertSize($value){
    if($setting['сdek_type_size'] == 'mm'){
        return ($value/10);
    }
    else{
        return $value;
    }
}


function cdekConvertWeight($value){
    if($setting['сdek_type_weight'] == 'kg'){
        return ($value*1000);
    }
    else{
        return $value;
    }
}

function removeWords($word,$removeWord){
    $wordArr =  explode(' ', $word);
    $filteredWords = array_diff($wordArr,$removeWord);
    $filter = implode(' ',$filteredWords);
    return $filter;
}