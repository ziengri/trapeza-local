<?php
$main_section = 'settings';
$item_id = 3;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($MODULE_FOLDER."linkmanager/function.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");
require_once ($MODULE_FOLDER."linkmanager/admin.inc.php");
require ($ADMIN_FOLDER."modules/ui.php");
require ($MODULE_FOLDER."linkmanager/ui_config.php");
if (!($perm->isSupervisor() || $perm->isGuest())) {
    nc_print_status($NO_RIGHTS_MESSAGE, 'error');
    exit;
}

//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();

$UI_CONFIG = new ui_config_module_linkmanager('admin', 'stats');
$UI_CONFIG->actionButtons = array();
?>
<html>
    <head>
        <title><?=NETCAT_MODULE_LINKS_LINK_CHECK
?></title>
        <link type='text/css' rel='Stylesheet' href='<?=$SUB_FOLDER.$ADMIN_TEMPLATE
?>css/admin.css'>
        <script type='text/javascript' src='<?=$ADMIN_PATH
?>js/lib.js'></script>
        <script type='text/javascript' src='<?=$ADMIN_PATH
?>js/forms.js'></script>
        <script type='text/javascript' src='<?=$ADMIN_PATH
?>js/sitemap.js'></script>
        <script>var formAsyncSaveEnabled = false;</script>
        <script src='check_links.js'></script>
    </head>
    <body bgcolor="#FFFFFF">

        <ol id='results'>

        </ol>

        <script>
            var NETCAT_MODULE_LINKS_CHECKUP_DONE = '<?=NETCAT_MODULE_LINKS_CHECKUP_DONE
?>';
            check_links();
        </script>

<?php 
EndHtml();
?>