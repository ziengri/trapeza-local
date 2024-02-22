<?php

/* $Id: setup.php 6209 2012-02-10 10:28:29Z denis $ */

$module_keyword = "poll";
$main_section = "settings";
$item_id = 3;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER."modules/ui.php");

$Title1 = NETCAT_MODULES_TUNING;
$Title2 = NETCAT_MODULES;

$UI_CONFIG = new ui_config_tool(TOOLS_MODULES_LIST, TOOLS_MODULES_LIST, 'i_modules_big.gif', 'module.list');

if (!($perm->isSupervisor() || $perm->isGuest())) {
    BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");
    nc_print_status($NO_RIGHTS_MESSAGE, 'error');
    EndHtml();
    exit;
}

// проверка, установлен этот модуль или нет
$res = $db->get_row("SELECT * FROM `Module` WHERE `Keyword` = '".$db->escape($module_keyword)."' AND `Installed` = 0", ARRAY_A);

if (!$db->num_rows) {
    BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");
    nc_print_status(NETCAT_MODULE_INSTALLCOMPLIED, 'ok');
    EndHtml();
    exit;
} else {
    $module_data = $res;
}

// load modules env
$lang = $nc_core->lang->detect_lang(1);
$MODULE_VARS = $nc_core->modules->load_env($lang);

if (!isset($phase)) $phase = 2;

switch ($phase) {
    case 1:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");
        break;

    case 2:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");
        SelectParentSub();
        break;

    case 3:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");

        InsertSub(NETCAT_MODULE_SETUP_POLL, "poll", "", 0, 0, 0, 0, 0, $MODULE_VARS['poll']['POLL_TABLE'], $SubdivisionID, $CatalogueID, "index", 1);
        UpdateHiddenURL("/", 0, $CatalogueID);

        $db->query("UPDATE `Module` SET `Installed` = 1 WHERE `Module_ID` = '".intval($module_data["Module_ID"])."'");
        echo "<br/><br/>";
        nc_print_status(NETCAT_MODULE_INSTALLCOMPLIED, 'ok');
        break;
}

EndHtml ();
?>