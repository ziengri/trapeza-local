<?php

$main_section = 'settings';
$item_id = 3;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($MODULE_FOLDER."linkmanager/function.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");
require_once ($MODULE_FOLDER."linkmanager/admin.inc.php");

if (!($perm->isSupervisor() || $perm->isGuest())) {
    if ($_GET['link_to_check']) die("0");
    nc_print_status($NO_RIGHTS_MESSAGE, 'error');
    exit;
}

//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();

if (isset($_GET['link_to_check'])) LM_Process($_GET['link_to_check']);
else LM_Process();
?>