<?php
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require ($ADMIN_FOLDER."report/function.inc.php");
require ($ADMIN_FOLDER."class/function.inc.php");



$Delimeter = " &gt ";
$main_section = "report";
$item_id = 1;
$Title1 = REPORTS;

if (!isset($phase) || !$phase) $phase = 1;

if (in_array($phase, array(4))) {
    if (!$nc_core->token->verify()) {
        BeginHtml($Title1, $Title1, "http://".$DOC_DOMAIN."/reports/general/");
        nc_print_status(NETCAT_TOKEN_INVALID, 'error');
        EndHtml();
        exit;
    }
}

BeginHtml($Title1, $Title1, "http://".$DOC_DOMAIN."/reports/general/");
$perm->ExitIfNotAccess(NC_PERM_REPORT, 0, 0, 0, 1);

switch ($phase) {
    case 1: #по проекту
        $UI_CONFIG = new ui_config_general_stat ();

        echo "<fieldset>\n
          <legend>".SECTION_CONTROL_CONTENT_CATALOGUE."</legend>\n
          <table  border='0' cellpadding='6' cellspacing='0' width='100%'>\n";

        if ($SelectResult = $db->get_results("SELECT `Catalogue_Name`, `Catalogue_ID` FROM `Catalogue` ORDER BY `Catalogue_ID`")) {
            foreach ($SelectResult as $Row) {
                $CatName[] = $Row->Catalogue_Name;
                $CatArray[] = $Row->Catalogue_ID;
            }
            $count_cat = $db->num_rows;
        }
        for ($i = 0; $i < $count_cat; $i++) {
            $count_sub = $db->get_var("SELECT count(*) FROM `Subdivision` WHERE `Catalogue_ID`='".$CatArray[$i]."'");

            echo "<tr><td width='50%'>".$CatName[$i].":</td><td>";
            printf(REPORTS_SECTIONS, $count_sub, $count_sub_off);
            echo "</td></tr>";
            $count_sub_sum += $count_sub;
            $count_sub_off_sum += $count_sub_off;
        }
        echo "<tr><td colspan='2'><hr size='1'></td></tr>\n";
        echo "<tr><td align='right'><b>".REPORTS_SYSMSG_TOTAL.":</td><td>\n";
        printf(REPORTS_SECTIONS, $count_sub_sum, $count_sub_off_sum);
        echo "</td></tr>\n";
?>
        </table>
        </fieldset>
        <fieldset>
            <legend><?=CONTROL_USER_GROUPS ?></legend>
            <table border='0' cellpadding='6' cellspacing='0' width='100%'>
        <?php 
        if ($SelectResult = $db->get_results("SELECT `PermissionGroup_Name`,`PermissionGroup_ID` FROM `PermissionGroup` ORDER BY `PermissionGroup_ID`")) {
            foreach ($SelectResult as $Row) {
                $GroupName[] = $Row->PermissionGroup_Name;
                $GroupArray[] = $Row->PermissionGroup_ID;
            }
            $count_group = $db->num_rows;
        }
        for ($i = 0; $i < $count_group; $i++) {
            $count_user = $db->get_var("SELECT count(*) FROM User WHERE PermissionGroup_ID=".$GroupArray[$i]);
            $count_user_off = $db->get_var("SELECT count(*) FROM User WHERE PermissionGroup_ID=".$GroupArray[$i]." AND Checked=0");

            echo "<tr><td width='50%'>".$GroupName[$i].":</td><td>\n";
            printf(REPORTS_USERS, $count_user, $count_user_off);
            echo "</td></tr>\n";
            $count_user_sum += $count_user;
            $count_user_off_sum += $count_user_off;
        }

        echo "<tr><td colspan='2'><hr size='1'>\n</td></tr>\n";
        echo "<tr>\n<td align=right>".REPORTS_SYSMSG_TOTAL.":</td><td>\n";
        printf(REPORTS_USERS, $count_user_sum, $count_user_off_sum);
        echo "</td></tr>\n</table></fieldset>\n";

        break;

    case 2: #class, sub_class
        $UI_CONFIG = new ui_config_general_stat('class_stat');

        show_form_for_select($type);
        nc_list_class_use($a, $type);

        break;

    case 3: #Подтверждение удаления
        $UI_CONFIG = new ui_config_general_stat('class_stat');
        confim_delete_sub_class_object($_POST);
        break;

    case 4: #Delete
        $UI_CONFIG = new ui_config_general_stat('class_stat');
        nc_print_status(REPORTS_STAT_CLASS_CLEARED, 'ok');
        show_form_for_select($type);
        nc_list_class_use($a, $type);
        if ($sub_class) $del_sub_class = explode(',', $sub_class);
        foreach ($del_sub_class as $v)
            SubClassClear((int) $v);

        break;
}
EndHtml ();
        ?>