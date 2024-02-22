<?php

$module_keyword = "bitcat";
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($MODULE_FOLDER.$module_keyword."/admin.inc.php");
require ($ADMIN_FOLDER."function.inc.php");


require_once ($MODULE_FOLDER.$module_keyword."/function.inc.php");

$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
$MODULE_VARS = $nc_core->modules->get_module_vars();


# UI
require ($ADMIN_FOLDER."modules/ui.php");
require_once ($MODULE_FOLDER.$module_keyword."/ui_config.php");
$UI_CONFIG = new ui_config_module($module_keyword);
global $UI_CONFIG;

# языковые настройки
if (is_file($MODULE_FOLDER.$module_keyword."/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER.$module_keyword."/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER.$module_keyword."/en.lang.php");
}

if (!$phase)  $phase = 1;



if (!$_GET && !$_POST) {
BeginHtml();

echo "Модуль механизма создания сайтов «Биткэт»";

/* $UI_CONFIG->actionButtons[] = array(
    "id"      => "submit",
    "caption" => "подтвердить",
    "action"  => "mainView.submitIframeForm('mainForm')"
); */


EndHtml();
}



?>