<?php

/* $Id: admin.php 6207 2012-02-10 10:14:50Z denis $ */

$main_section = 'settings';
$item_id = 3;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($MODULE_FOLDER."linkmanager/admin.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");


require_once ($ADMIN_FOLDER."modules/ui.php");
//$UI_CONFIG = new ui_config_module('linkmanager');
if ($page) {
    $UI_CONFIG->locationHash .= "($page)";
}

if (is_file($MODULE_FOLDER."linkmanager/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."linkmanager/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER."linkmanager/en.lang.php");
}
require ($MODULE_FOLDER."linkmanager/ui_config.php");

$Delimeter = " &gt ";
$Title1 = NETCAT_MODULE_LINKS;
$Title2 = "<a href=".$ADMIN_PATH."modules/>".NETCAT_MODULES."</a>";

// check permission
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();

if (!isset($phase)) $phase = 1;

BeginHtml($Title1, $Title2.$Delimeter.$Title1, "http://".$DOC_DOMAIN."/settings/modules/linkmanager/");

if (!$page) $page = 'stats';
$UI_CONFIG = new ui_config_module_linkmanager('admin', $page);

/*
  switch($page) {
  case "templates":
  echo "<a href=".$_SERVER[PHP_SELF]."?>".NETCAT_MODULE_LINKS_STATS."</a> | <a href=".$_SERVER[PHP_SELF]."?page=settings>".NETCAT_MODULE_LINKS_SETTINGS."</a> | ".NETCAT_MODULE_LINKS_EMAIL_TEMPLATES."<p>";
  break;
  case "settings":
  echo "<a href=".$_SERVER[PHP_SELF]."?>".NETCAT_MODULE_LINKS_STATS."</a> | ".NETCAT_MODULE_LINKS_SETTINGS." | <a href=".$_SERVER[PHP_SELF]."?page=templates>".NETCAT_MODULE_LINKS_EMAIL_TEMPLATES."</a><p>";
  break;
  default:
  echo NETCAT_MODULE_LINKS_STATS." | <a href=".$_SERVER[PHP_SELF]."?page=settings>".NETCAT_MODULE_LINKS_SETTINGS."</a> | <a href=".$_SERVER[PHP_SELF]."?page=templates>".NETCAT_MODULE_LINKS_EMAIL_TEMPLATES."</a><p>";
  }

  echo "<hr>";
 */
if ($phase == 2) {
    switch ($page) {
        case "templates":
            LM_Save_Tem();
            nc_print_status(NETCAT_MODULE_LINKS_CHANGES_SAVED, 'ok');
            break;
        case "settings":
            LM_Save_Set();
            nc_print_status(NETCAT_MODULE_LINKS_CHANGES_SAVED, 'ok');
            break;
        default:
            nc_print_status(NETCAT_MODULE_LINKS_CHANGES_SAVED, 'ok');
    }
}

$lm_set = LM_Get_Set();
switch ($page) {
    case "templates":
        require_once("templates.tpl.php");
        break;
    case "settings":
        require_once("settings.tpl.php");
        break;
    default:
        LM_Show_Stat();
}

EndHtml();
?>