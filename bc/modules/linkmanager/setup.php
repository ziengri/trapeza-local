<?php

/* $Id: setup.php 4051 2010-10-08 12:04:52Z denis $ */

$module_keyword = "linkmanager";
$main_section = "settings";
//$item_id = 3;

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

        $sub_id = InsertSub(NETCAT_MODULE_LINKS_SUB, 'LinkExchange', '', 0, 0, 0, 0, 0, $MODULE_VARS["LINKS_CLASS"], $SubdivisionID, $CatalogueID, 0, 1);

        $rules_sub_id = InsertSub(NETCAT_MODULE_LINKS_RULES, 'rules', '', 0, 0, 0, 0, 0, 1, $sub_id, $CatalogueID, 0, 0);
        add_objects_to_sub($CatalogueID, $rules_sub_id, 1, 'rules', NETCAT_MODULE_LINKS_RULES, 0, 0, 0, 0, array(array(
                        'Checked' => 1,
                        'TextContent' => NETCAT_MODULE_LINKS_RULES_PAGE
                )));

        InsertSub(NETCAT_MODULE_LINKS_CODES_SUB, 'codes', '', 0, 0, 0, 0, 0, $MODULE_VARS["OUR_CODES_CLASS"], $sub_id, $CatalogueID, 0, 0);
        InsertSub(NETCAT_MODULE_LINKS_SOLD_SUB, 'sold', '', 0, 0, 0, 0, 0, $MODULE_VARS["SOLD_LINKS_CLASS"], $sub_id, $CatalogueID, 0, 0);
        InsertSub(NETCAT_MODULE_LINKS_PURCHASED_SUB, 'purchased', '', 0, 0, 0, 0, 0, $MODULE_VARS["PURCHASED_LINKS_CLASS"], $sub_id, $CatalogueID, 0, 0);
        InsertSub(NETCAT_MODULE_LINKS_STOPLIST_SUB, 'stoplist', '', 0, 0, 0, 0, 0, $MODULE_VARS["STOP_CLASS"], $sub_id, $CatalogueID, 0, 0);

        $db->query("INSERT INTO CronTasks SET Cron_Minutes = 0, Cron_Hours = 0, Cron_Days = 1, Cron_Months = 0, Cron_Weekdays = 0,
               Cron_Script_URL = '" . $db->escape($SUB_FOLDER . $HTTP_ROOT_PATH) . "modules/linkmanager/process.php?param=secretkey'");

        UpdateHiddenURL("/", 0, $CatalogueID);

        $db->query("UPDATE `Module` SET `Installed` = 1 WHERE `Module_ID` = '".intval($module_data["Module_ID"])."'");
        echo "<br/><br/>";
        nc_print_status(NETCAT_MODULE_INSTALLCOMPLIED, 'ok');
        break;
}

EndHtml();

// ==================================================================================

/**
 * adds object of a given class to the section (and class to section if it's not here yet)
 * @return integer class_id_in_section
 */
function add_objects_to_sub($catalogue_id, $sub_id, $class_id, $class_name_in_section, $class_keyword, $read_access=0, $write_access=0, $edit_access=0, $subscribe_access=0, $values=array()
) {
    global $db;
    $GLOBALS['PRINT_QUERIES'] = 1;

    // check if class is in sub
    $class_id_in_sub = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = $sub_id AND Class_ID = $class_id");

    if (!$class_id_in_sub) { // the class isn't linked to this sub yet
        $class_priority = $db->get_var("SELECT MAX(Priority) FROM Sub_Class WHERE Subdivision_ID=$sub_id") + 1;

        // add class to the sub
        $db->query("INSERT INTO Sub_Class
            SET Subdivision_ID=$sub_id,
                Class_ID=$class_id,
                Sub_Class_Name='".mysql_real_escape_string($class_name_in_section)."',
                Priority=$class_priority,
                Read_Access_ID=$read_access,
                Write_Access_ID=$write_access,
                EnglishName='".mysql_real_escape_string($class_keyword)."',
                Checked=0,
                Catalogue_ID=$catalogue_id,
                Edit_Access_ID=$edit_access");
        $class_id_in_sub = mysql_insert_id();
    }

    // add object, fill values
    if ($values) {
        $object_priority = $db->get_var("SELECT MAX(Priority) FROM Message$class_id WHERE Subdivision_ID=$sub_id") + 1;
        foreach ($values as $object_properties) {
            $qry = array();
            foreach ($object_properties as $k => $v) {
                $qry[] = "`$k` = '".mysql_real_escape_string($v)."'";
            }

            if (!sizeof($qry)) {
                continue;
            }

            $qry[] = "Priority = $object_priority";

            $db->query("INSERT INTO Message$class_id
               SET Subdivision_ID = $sub_id,
                   Sub_Class_ID=$class_id_in_sub, ".join(", ", $qry));

            $object_priority++;
        }
    }

    return $class_id_in_sub;
}