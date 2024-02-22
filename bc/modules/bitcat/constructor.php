<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($MODULE_FOLDER."stats/admin.inc.php");
require ($ADMIN_FOLDER."function.inc.php");

$module_keyword = "bitcat";

require_once ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER.$module_keyword."/function.inc.php");

# UI
require ($ADMIN_FOLDER."modules/ui.php");
$UI_CONFIG = new ui_config_module($module_keyword);

# языковые настройки
if (is_file($MODULE_FOLDER.$module_keyword."/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER.$module_keyword."/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER.$module_keyword."/en.lang.php");
}

$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
$MODULE_VARS = $nc_core->modules->get_module_vars();



BeginHtml();

echo "";


EndHtml();
?>