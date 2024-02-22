<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$DOCUMENT_ROOT1 = $DOCUMENT_ROOT;
$_SERVER['HTTP_HOST'] = str_replace(":443", '', $_SERVER['HTTP_HOST']);
$HTTP_HOST = $_SERVER['HTTP_HOST'];

// require_once $DOCUMENT_ROOT . "/.logins.php";


# подпапка в которой стоит NetCat
$SUB_FOLDER = '';
# Если NetCat стоит в подпапке, то раскомментируйте следующую строчку
#$SUB_FOLDER = str_replace( str_replace("\\", "/", $DOCUMENT_ROOT), "", str_replace("\\", "/", dirname(__FILE__)) );
@$deviceID = (isset($_SESSION['deviceID']) ? $_SESSION['deviceID'] : $_COOKIE['deviceID']);
if (!$deviceID)
	$deviceID = (isset($_GET['device_id']) ? $_GET['device_id'] : (isset($_GET['deviceId']) ? $_GET['deviceId'] : ""));


// if ($deviceID) $_COOKIE['deviceID'] = $deviceID;

# установка переменных окружения
@ini_set("session.auto_start", "0");
@ini_set("session.use_trans_sid", "0");
@ini_set("session.use_cookies", "1");
@ini_set("session.use_only_cookies", "1");
@ini_set("url_rewriter.tags", ""); // to disable trans_sid on PHP < 5.0
@ini_set('session.cookie_domain', (strpos(str_replace("www.", "", $HTTP_HOST), '.') !== false) ? str_replace("www.", "", $HTTP_HOST) : '');
@ini_set("session.gc_probability", "1");
@ini_set("session.gc_maxlifetime", "648000");
@ini_set("session.hash_bits_per_character", "5");
@ini_set("mbstring.internal_encoding", "UTF-8");
@ini_set("default_charset", "UTF-8");
@ini_set("session.name", ini_get("session.hash_bits_per_character") >= 5 ? "sid" : "ced");

@ini_set('error_reporting', E_ALL ^ (E_NOTICE | E_WARNING));
@ini_set('display_errors', 1);
@ini_set('display_startup_errors', 1);


# параметры доступа к базе данных
$MYSQL_HOST = "localhost";
$MYSQL_CHARSET = "utf8";
$MYSQL_ENCRYPT = "MD5";

$MYSQL_USER = "root";
$MYSQL_PASSWORD = "qweqwe";
$MYSQL_DB_NAME = "trapeza";
$MYSQL_PORT = "";
$MYSQL_SOCKET = "";


# кодировка
$NC_UNICODE = 1;
$NC_CHARSET = "utf-8";

# настройки авторизации
$AUTHORIZE_BY = "Login";
$AUTHORIZATION_TYPE = "cookie"; # 'http', 'session' or 'cookie'

#разрешить вход только по https
$NC_ADMIN_HTTPS = 0; # 0 или 1

# серверные настройки
$PHP_TYPE = "cgi"; # 'module' or 'cgi'
$REDIRECT_STATUS = "on"; # 'on' or 'off'

# настройки безопасности
$SECURITY_XSS_CLEAN = false;

# инструмент "Переадресация" не доступен
$NC_REDIRECT_DISABLED = 0; # 0 или 1

# не загружать устаревшие файлы и функции
$NC_DEPRECATED_DISABLED = 1; # 0 или 1

$ADMIN_LANGUAGE = "Russian"; # Язык административной части NetCat "по-умолчанию"
$FILECHMOD = 0644; # Права на файл при добавлении через систему
$DIRCHMOD = 0755; # Права на директории для закачки пользовательских файлов
$SHOW_MYSQL_ERRORS = 'off'; # Показ ошибок MySQL на страницах сайта
$ADMIN_AUTHTIME = 648000; # Время жизни авторизации в секундах (при $AUTHORIZATION_TYPE = session, cookie)
$ADMIN_AUTHTYPE = "manual"; # Выбор типа авторизации: 'session', 'always' or 'manual'
$use_gzip_compression = false; # Для включения сжатия вывода установите true

# настройки проекта
$DOMAIN_NAME = $HTTP_HOST; # $HTTP_HOST is server environment variable

$HTTP_IMAGES_PATH = "/images/";
$HTTP_ROOT_PATH = "/bc/";
$HTTP_FILES_PATH = "/files/";
$HTTP_DUMP_PATH = "/dump/";
$HTTP_CACHE_PATH = "/cache/";
$HTTP_TEMPLATE_PATH = "/template/";
$HTTP_TRASH_PATH = "/trash/";
$HTTP_COLORS_PATH = "/colors/";
$HTTP_ADDSITE_PATH = "/createsite/";

# относительный путь в админку сайта, для ссылок
$ADMIN_PATH = $SUB_FOLDER . $HTTP_ROOT_PATH . "admin/";
# относительный путь к теме админки, для изображений и .css файлов
$ADMIN_TEMPLATE = $ADMIN_PATH . "skins/default/";
# полный путь к теме сайта, например для функции file_exists()
$ADMIN_TEMPLATE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $ADMIN_TEMPLATE;

$SYSTEM_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . "system/";
$ROOT_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH;
$FILES_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_FILES_PATH;
$DUMP_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_DUMP_PATH;
$CACHE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_CACHE_PATH;
$DELIVCACHE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_CACHE_PATH . "deliveryday/";
$TRASH_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TRASH_PATH;
$COLORS_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_COLORS_PATH;
$ADDSITE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ADDSITE_PATH;
$INCLUDE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . "require/";
$TMP_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . "tmp/";
$MODULE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/";
$ADMIN_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . "admin/";
$EDIT_DOMAIN = $DOMAIN_NAME;
$DOC_DOMAIN = "docs.netcat.ru/24";

$TEMPLATE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TEMPLATE_PATH . "template/";
$CLASS_TEMPLATE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TEMPLATE_PATH . "class/";
$WIDGET_TEMPLATE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TEMPLATE_PATH . "widget/";
$JQUERY_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TEMPLATE_PATH . "jquery/";
$MODULE_TEMPLATE_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_TEMPLATE_PATH . "module/";



$login['login'] = "trapeza";

// if (!$login['login']) $login['login'] = "xdomo";

if ($login['login']) {
	$_SESSION["login" . $_SERVER['HTTP_HOST']] = $login['login'];
	$pathInc = "/a/" . $login['login'];
	$pathInc2 = "/b/" . $login['login'];
	$HTTP_FILES_PATH = $pathInc . $HTTP_FILES_PATH;
	$FILES_FOLDER = $DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_FILES_PATH;
} else {
	header("HTTP/1.0 404 Not Found");
	if (strstr($_SERVER['HTTP_HOST'], "www."))
		header("Location: http://" . str_replace("www.", "", $_SERVER['HTTP_HOST']));
	die("Страница не найдена");

}

# wazzup
$KORZILLA_WAZZUP_API_KEY = 'd64bfa464ed948049083ff917e5eba72';
$KORZILLA_WAZZUP_CHANNEL_ID = '73fb4ac6-c3f6-4503-a762-2fb50ee91ea4';



# add require/lib folder (PEAR libraries) to the include_path
ini_set("include_path", "{$INCLUDE_FOLDER}lib/") . (substr_count(strtolower(php_uname()), 'windows') ? ';' : ':') . ini_get("include_path");

# название разработчика, отображаемое на странице О программе
$DEVELOPER_NAME = 'Korzilla';
$DEVELOPER_URL = '//korzilla.ru/';

$PHP_PATH = '/opt/php71/bin/php';