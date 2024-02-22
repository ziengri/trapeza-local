<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require ($ADMIN_FOLDER."user/function.inc.php");
require_once ($INCLUDE_FOLDER."s_common.inc.php");

$main_section = "control";
$Delimeter = " &gt ";
$item_id = 5;
$Title0 = CONTROL_USER_REGISTER;
$Title2 = CONTROL_USER_REGISTER;
$Title3 = "<a href=\"".$ADMIN_PATH."user/register.php\">".CONTROL_USER_REG."</a>";


BeginHtml($Title0, $Title2, "http://".$DOC_DOMAIN."/management/users/add/");
$perm->ExitIfNotAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_ADD, 0, 0, 1);

$UI_CONFIG = new ui_config_user();
$UI_CONFIG->new_user_page();

if (!isset($phase)) $phase = 1;

switch ($phase) {
    case 1: # покажем форму регистрации пользователя
        UserForm(0, "register.php", 2, 1);
        break;

    case 2:  # собственно зарегистрируем пользователя
        if ($warnText = ActionUserCompleted("register.php", 1)) {
            nc_print_status($warnText, 'error');
            UserForm(0, "register.php", 2, 1);
        } else {
            $UI_CONFIG->user_list_page();
            unset($UserID);
            unset($Checked);
            SearchUserResult();
        }

        break;
}
EndHtml ();
?>