<?php

/* $Id: admin.inc.php 7799 2012-07-25 12:50:09Z alive $ */

function LM_Save_Set() {

    global $LinkID;
    static $result = NULL;

    if ($result != NULL) return $result;
    $s = "update Links_Settings set ";
    if ($_POST['lm_mode'] == 1) $s .= "Back_Link_Needed=1, "; else
        $s .= "Back_Link_Needed=0, ";
    if ($_POST['lm_kill_bl'] == 2) $s .= "Kill_Bad_Link=1, "; else
        $s .= "Kill_Bad_Link=0, ";
    if (($_POST['lm_kill_bl_in'] + 0) > 0)
        $s .= "Kill_Bad_Link_In=" . $_POST['lm_kill_bl_in'] . ", "; else
        $s .= "Kill_Bad_Link_In=0, ";
    if ($_POST['lm_direct_gl'] == 1) $s .= "Direct_For_Good_Link=1, "; else
        $s .= "Direct_For_Good_Link=0, ";
    if ($_POST['lm_html_gl'] == 1) $s .= "HTML_In_Good_Link=1, "; else
        $s .= "HTML_In_Good_Link=0, ";
    if ($_POST['lm_putup_gl'] == 1) $s .= "Put_Up_Good_Link=1, "; else
        $s .= "Put_Up_Good_Link=0, ";
    if (($_POST['lm_invite_in'] + 0) > 0)
        $s .= "Invite_Partner_In=" . $_POST['lm_invite_in'] . ", "; else
        $s .= "Invite_Partner_In=0, ";
    if ($_POST['lm_check_whole'] == 1) $s .= "Check_Whole_Text=1, "; else
        $s .= "Check_Whole_Text=0, ";
    switch ($_POST['lm_back_link_at']) {
        case 1:
            $s .= "Back_Link_At_Site=1, ";
            break;
        case 2:
            $s .= "Back_Link_At_Site=2, ";
            break;
        default:
            $s .= "Back_Link_At_Site=3, ";
    }
    if ($_POST['lm_fail_if_present'] == 1)
        $s .= "Fail_If_Back_Link_Present=1, "; else
        $s .= "Fail_If_Back_Link_Present=0, ";
    if ($_POST['lm_fail_if_many'] == 1) $s .= "Fail_If_Many_Hosts=1, "; else
        $s .= "Fail_If_Many_Hosts=0, ";
    if ($_POST['lm_fail_if_third'] == 1) $s .= "Fail_If_Third_Level=1, "; else
        $s .= "Fail_If_Third_Level=0, ";
    $s .= "Spam_From=\"" . htmlspecialchars($_POST['lm_spam_from']) . "\", ";
    $s .= "Admin_Mail=\"" . htmlspecialchars($_POST['lm_admin_mail']) . "\" ";

    mysql_query($s, $LinkID);
    echo mysql_error();
}

function LM_Save_Tem() {

    global $LinkID;
    static $result = NULL;

    if ($result != NULL) return $result;
    $s = "update Links_Settings set ";

    if ($_POST['lm_admin_added'] == 1) $s .= "Send_Admin_Added=1, "; else
        $s .= "Send_Admin_Added=0, ";
    if ($_POST['lm_partner_bl'] == 1) $s .= "Send_Partner_Added_Bad_Link=1, "; else
        $s .= "Send_Partner_Added_Bad_Link=0, ";
    if ($_POST['lm_partner_gl'] == 1) $s .= "Send_Partner_Added_Good_Link=1, "; else
        $s .= "Send_Partner_Added_Good_Link=0, ";
    if ($_POST['lm_partner_turnoff'] == 1) $s .= "Send_Partner_Turnoff=1, "; else
        $s .= "Send_Partner_Turnoff=0, ";
    if ($_POST['lm_partner_turnon'] == 1) $s .= "Send_Partner_Turnon=1, "; else
        $s .= "Send_Partner_Turnon=0, ";
    if ($_POST['lm_partner_kill'] == 1) $s .= "Send_Partner_Kill=1, "; else
        $s .= "Send_Partner_Kill=0, ";

    if ($_POST['lm_partner_redirect'] == 1)
        $s .= "Send_Partner_Added_Redirect_Link=1, "; else
        $s .= "Send_Partner_Added_Redirect_Link=0, ";
    if ($_POST['lm_partner_direct'] == 1)
        $s .= "Send_Partner_Added_Direct_Link=1, "; else
        $s .= "Send_Partner_Added_Direct_Link=0, ";
    if ($_POST['lm_partner_redirect_on'] == 1)
        $s .= "Send_Partner_Redirect_On=1, "; else
        $s .= "Send_Partner_Redirect_On=0, ";
    if ($_POST['lm_partner_redirect_off'] == 1)
        $s .= "Send_Partner_Redirect_Off=1, "; else
        $s .= "Send_Partner_Redirect_Off=0, ";

    if ($_POST['lm_admin_report'] == 1) $s .= "Send_Admin_Report=1, "; else
        $s .= "Send_Admin_Report=0, ";
    if ($_POST['lm_admin_no_purchased'] == 1)
        $s .= "Send_Admin_No_Purchased=1, "; else
        $s .= "Send_Admin_No_Purchased=0, ";
    if ($_POST['lm_partner_sold_off'] == 1)
        $s .= "Send_Partner_Sold_Turnoff=1, "; else
        $s .= "Send_Partner_Sold_Turnoff=0, ";

    $s .= "Send_Admin_Added_Subject=\"" . htmlspecialchars($_POST['lm_admin_added_s']) . "\", ";
    $s .= "Send_Partner_Added_Bad_Link_Subject=\"" . htmlspecialchars($_POST['lm_partner_bl_s']) . "\", ";
    $s .= "Send_Partner_Added_Good_Link_Subject=\"" . htmlspecialchars($_POST['lm_partner_gl_s']) . "\", ";
    $s .= "Send_Partner_Turnoff_Subject=\"" . htmlspecialchars($_POST['lm_partner_turnoff_s']) . "\", ";
    $s .= "Send_Partner_Turnon_Subject=\"" . htmlspecialchars($_POST['lm_partner_turnon_s']) . "\", ";
    $s .= "Send_Partner_Kill_Subject=\"" . htmlspecialchars($_POST['lm_partner_kill_s']) . "\", ";

    $s .= "Send_Partner_Added_Redirect_Link_Subject=\"" . htmlspecialchars($_POST['lm_partner_redirect_s']) . "\", ";
    $s .= "Send_Partner_Added_Direct_Link_Subject=\"" . htmlspecialchars($_POST['lm_partner_direct_s']) . "\", ";
    $s .= "Send_Partner_Redirect_On_Subject=\"" . htmlspecialchars($_POST['lm_partner_redirect_on_s']) . "\", ";
    $s .= "Send_Partner_Redirect_Off_Subject=\"" . htmlspecialchars($_POST['lm_partner_redirect_off_s']) . "\", ";

    $s .= "Send_Admin_No_Purchased_Subject=\"" . htmlspecialchars($_POST['lm_admin_no_purchased_s']) . "\", ";
    $s .= "Send_Partner_Sold_Turnoff_Subject=\"" . htmlspecialchars($_POST['lm_partner_sold_off_s']) . "\", ";

    $s .= "Send_Admin_Added_Template=\"" . htmlspecialchars($_POST['lm_admin_added_t']) . "\", ";
    $s .= "Send_Partner_Added_Bad_Link_Template=\"" . htmlspecialchars($_POST['lm_partner_bl_t']) . "\", ";
    $s .= "Send_Partner_Added_Good_Link_Template=\"" . htmlspecialchars($_POST['lm_partner_gl_t']) . "\", ";
    $s .= "Send_Partner_Turnoff_Template=\"" . htmlspecialchars($_POST['lm_partner_turnoff_t']) . "\", ";
    $s .= "Send_Partner_Turnon_Template=\"" . htmlspecialchars($_POST['lm_partner_turnon_t']) . "\", ";
    $s .= "Send_Partner_Kill_Template=\"" . htmlspecialchars($_POST['lm_partner_kill_t']) . "\", ";

    $s .= "Send_Partner_Added_Redirect_Link_Template=\"" . htmlspecialchars($_POST['lm_partner_redirect_t']) . "\", ";
    $s .= "Send_Partner_Added_Direct_Link_Template=\"" . htmlspecialchars($_POST['lm_partner_direct_t']) . "\", ";
    $s .= "Send_Partner_Redirect_On_Template=\"" . htmlspecialchars($_POST['lm_partner_redireсt_on_t']) . "\", ";
    $s .= "Send_Partner_Redirect_Off_Template=\"" . htmlspecialchars($_POST['lm_partner_redireсt_off_t']) . "\", ";

    $s .= "Send_Admin_No_Purchased_Template=\"" . htmlspecialchars($_POST['lm_admin_no_purchased_t']) . "\", ";
    $s .= "Send_Partner_Sold_Turnoff_Template=\"" . htmlspecialchars($_POST['lm_partner_sold_off_t']) . "\"";

    mysql_query($s, $LinkID);
    # echo $s;
    echo mysql_error();

    $templates = array(
        'linkmanager_admin_added',
        'linkmanager_partner_added_bad_link',
        'linkmanager_partner_added_good_link',
        'linkmanager_partner_turnoff',
        'linkmanager_partner_turnon',
        'linkmanager_partner_kill',
        'linkmanager_partner_added_redirect_link',
        'linkmanager_partner_added_direct_link',
        'linkmanager_partner_redirect_on',
        'linkmanager_partner_redirect_off',
        'linkmanager_admin_no_purchased',
        'linkmanager_partner_sold_turnoff',
    );

    foreach ($templates as $template) {
        nc_mail_attachment_form_save($template);
    }
}

function LM_Show_Stat() {
    global $UI_CONFIG;
    global $MODULE_VARS;
    $lm_set = LM_Get_Set();
    echo "<br/>";
    printf(NETCAT_MODULE_LINKS_REPORT_STAT,
        listQuery("select count(Message_ID) as count from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS], "\$data[count]"),
        listQuery("select count(Message_ID) as count from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Checked=0", "\$data[count]"),
        listQuery("select count(Message_ID) as count from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where SiteURLCom<>''", "\$data[count]"));
    print "<p>" . listQuery("SELECT Last_Process, Last_Unchecked, Last_Checked, Last_Deleted FROM Links_Settings limit 1",
        NETCAT_MODULE_LINKS_REPORT_LAST_CHECK)
        . "</p>
  <form method='get' action='check_links.php'></form>";
    $UI_CONFIG->actionButtons[] = array("id" => "submit",
        "caption" => NETCAT_MODULE_LINKS_START_CHECKUP,
        "action" => "mainView.submitIframeForm()"
    );
}

/**
 * Проверка ссылок
 * Если НЕ указан $link_to_check - проверка всех ссылок подряд
 * Если указан $link_to_check - только проверка обратных ссылок, оповещения по email НЕ высылаются
 *
 * @param integer номер ссылки, которую мы сейчас проверяем
 * @return nothing
 */
function LM_Process($link_to_check = -1) {

    global $LinkID, $MODULE_VARS, $SUB_FOLDER, $HTTP_ROOT_PATH;

    $lm_set = LM_Get_Set();

    $lm_stat_checked = 0;
    $lm_stat_unchecked = 0;
    $lm_stat_deleted = 0;


    $lm_regulars[AdminEmail] = $lm_set[Admin_Mail];


    if ($link_to_check == -1) echo "<html><body>\n<ol>\n";

    $q = "SELECT Message_ID, Sub_Class_ID, Subdivision_ID, Checked, SiteName, SiteURL, SiteCode, SiteBackURL, SiteEmail, SiteURLCom, SiteCodeCom, SiteUnchecked
         FROM Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . "
         WHERE SiteNotProcess<>1 " .
        ($link_to_check != -1 ? "LIMIT " . (int)$link_to_check . ",1" : "");

    $res = mysql_query($q, $LinkID);

    if (!mysql_num_rows($res) && $link_to_check != -1) die("<!--EOF-->");

    while ($row = mysql_fetch_assoc($res)) {
        $lm_regulars[LinkName] = $row[SiteName];
        $lm_regulars[LinkURL] = $row[SiteURL];
        $lm_regulars[BackLink] = $row[SiteBackURL];
        if (!$lm_regulars[BackLink])
            $lm_regulars[BackLink] = NETCAT_MODULE_LINKS_NO_LINK;

        // Проверяем, для всех ли сайтов со ссылками указан домен
        $q = "select a.Catalogue_Name as name, a.Catalogue_ID as id, a.Domain as domain from Catalogue as a, Subdivision as b where b.Catalogue_ID=a.Catalogue_ID and b.Subdivision_ID=" . $row[Subdivision_ID] . "";

        $res1 = mysql_query($q, $LinkID);
        $row1 = mysql_fetch_assoc($res1);
        if (!$row1[domain]) {
            $lm_regulars[OurSite] = $row1[name];
            $lm_msg_no_domain[$row1[id]] = sprintf(NETCAT_MODULE_LINKS_NO_DOMAIN, $row1[name]);
            if ($link_to_check == 0) {
                print str_replace('\n', "<br>", $lm_msg_no_domain[$row1[id]]) . "<!--EOF-->";
            }
        } else {
            $lm_site_id = $row1[id];
            $lm_regulars[OurSite] = $row1[domain];
            if (strpos($lm_regulars[OurSite], "www.") === 0)
                $lm_regulars[OurSite1] = substr($lm_regulars[OurSite], 4);
            else $lm_regulars[OurSite1] = "www." . $lm_regulars[OurSite];

            $q = "select Subdivision_ID from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Message_ID=" . $row[Message_ID];
            $res1 = mysql_query($q, $LinkID);
            $row1 = mysql_fetch_assoc($res1);
            $tm_id = $row1[Subdivision_ID];
            $j = 1;
            while ($j) {
                $q = "select count(a.Sub_Class_ID) as count, b.Parent_Sub_ID as parent from Sub_Class as a, Subdivision as b where b.Subdivision_ID=$tm_id and a.Subdivision_ID=b.Subdivision_ID group by parent";
                $res2 = mysql_query($q, $LinkID);
                if (mysql_num_rows($res2)) {
                    $row2 = mysql_fetch_array($res2);
                    $right_id = $tm_id;
                    $tm_id = $row2[parent];
                } else {
                    $j = 0;
                    $lm_regulars[OurCatalog] = "http://" . $lm_regulars[OurSite] . listQuery("select Hidden_URL from Subdivision where Subdivision_ID=$right_id", "\$data[Hidden_URL]");
                }
                mysql_free_result($res2);
            }

            $sub_u = listQuery("select Hidden_URL from Subdivision where Subdivision_ID=" . $row[Subdivision_ID], "\$data[Hidden_URL]");
            $cc_u = listQuery("select EnglishName from Sub_Class where Sub_Class_ID=" . $row[Sub_Class_ID], "\$data[EnglishName]");
            $lm_regulars[StaticLink] = $lm_regulars[OurSite] . $sub_u . $cc_u . "_" . $row[Message_ID] . ".html";

            $lm_regulars[LinkEmail] = $row[SiteEmail];

            // Проверяем, есть ли обратная ссылка или текст
            $back_page = LM_Get_Page($row['SiteBackURL']);
            $bad_link = 1;
            if ($lm_set[Check_Whole_Text]) {
                $bad_link = LM_CheckOurCodesOnPage($back_page, $lm_site_id);
            } else {
                // Ищем ссылку на домен текущего сайта на странице
                nc_preg_match_all("/(http?[\D]:\/\/[^\/\"'>]+)/i", $back_page, $matches);
                foreach ($matches[0] as $k => $v) {
                    $tm_site = parse_url($v);
                    if ($tm_site[host] == $lm_regulars[OurSite] || $tm_site[host] == $lm_regulars[OurSite1])
                        $bad_link = 0;
                }
            }

            // Удаляем просроченные ссылки

            $tm_time = strtotime($row[SiteUnchecked]);
            $lm_regulars[DeleteTime] =
                date("d.m.Y", mktime(
                    date("h", $tm_time), date("i", $tm_time), date("s", $tm_time), date("m", $tm_time), (date("d", $tm_time) + $lm_set[Kill_Bad_Link_In]), date("Y", $tm_time)));

            if ($lm_set[Back_Link_Needed] && $lm_regulars[DeleteTime] < date("Y-m-d h:m:i")) {
                $res1 = "delete from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Message_ID=" . $row[Message_ID];
                if ($lm_set[Send_Partner_Kill]) {
                    $mail_body = $lm_set[Send_Partner_Kill_Template];
                    $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                    nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Kill_Subject], $mail_body, $mail_body, 'linkmanager_partner_kill');
                }
                $lm_msg_deleted .= $row[SiteURL] . "\n";
                $lm_stat_deleted++;

                $tmp_m = $lm_regulars[BackLink] . " - deleted";
                $lm_stat_log .= $tmp_m . "\n";
                echo "<li> " . $tmp_m . "\n";
            }

            $tmp_m = $lm_regulars[BackLink];
            $lm_stat_log .= $tmp_m;
            echo "<li> " . $tmp_m;

            // Отправляем письмо с предложением разместить ссылку для редиректа

            if (!$lm_set[Back_Link_Needed] && $lm_set[Invite_Partner_In] &&
                !fmod(round((mktime() - strtotime($row[SiteUnchecked])) / 86400), $lm_set[Invite_Partner_In]) &&
                $lm_set[Send_Partner_Redirect_On]
            ) {
                $mail_body = $lm_set[Send_Partner_Redirect_On_Template];
                $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Redirect_On_Subject], $mail_body, $mail_body, 'linkmanager_partner_redirect_on');
            }

            if ($bad_link) {
                // Ссылки нет
                if ($lm_set[Back_Link_Needed]) {
                    if ($lm_set[Kill_Bad_Link]) {
                        $res1 = mysql_query("delete from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Message_ID=" . $row[Message_ID], $LinkID);
                        if ($lm_set[Send_Partner_Kill]) {
                            $mail_body = $lm_set[Send_Partner_Kill_Template];
                            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Kill_Subject], $mail_body, $mail_body, 'linkmanager_partner_kill');
                        }
                        $lm_msg_deleted .= $row[SiteURL] . "\n";
                        $lm_stat_deleted++;
                        $tmp_m = "fail, deleted";
                        $lm_stat_log .= " - " . $tmp_m . "\n";
                        echo "<br>" . $tmp_m . "\n";
                    } else {
                        if ($row[Checked])
                            $res1 = mysql_query("update Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " set Checked=0, SiteUnchecked='" . date("Y-m-d h:m:s") . "', SiteURLCom='', SiteCodeCom='' where Message_ID=" . $row[Message_ID], $LinkID);
                        else
                            $res1 = mysql_query("update Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " set Checked=0, SiteURLCom='', SiteCodeCom='' where Message_ID=" . $row[Message_ID], $LinkID);
                        if ($lm_set[Send_Partner_Turnoff]) {
                            $mail_body = $lm_set[Send_Partner_Turnoff_Template];
                            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Turnoff_Subject], $mail_body, $mail_body, 'linkmanager_partner_turnoff');
                        }
                        $lm_msg_unchecked .= $row[SiteURL] . "\n";
                        $lm_stat_unchecked++;
                        $tmp_m = "fail, turned off";
                        $lm_stat_log .= " - " . $tmp_m . "\n";
                        echo "<br>" . $tmp_m . "\n";
                    }
                } else {
                    $q1 = "";
                    $q1 .= "SiteURLCom='" . $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/linkmanager/redirect.php?url=" . $row[SiteURL] . "', ";
                    $q1 .= "SiteCodeCom='" . strip_tags($row[SiteCode]) . "', ";
                    $q1 .= "Priority=1, ";
                    if (!$row[SiteURLCom])
                        $q1 .= "SiteUnchecked='" . date("Y-m-d h:m:s") . "', ";
                    $q1 .= "Checked=1";
                    $res1 = mysql_query("update Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " set $q1 where Message_ID=" . $row[Message_ID], $LinkID);
                    if ($lm_set[Send_Partner_Redirect_On] && !$row[SiteURLCom]) {
                        $mail_body = $lm_set[Send_Partner_Redirect_On_Template];
                        $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                        nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Redirect_On_Subject], $mail_body, $mail_body, 'linkmanager_partner_redirect_on');
                    }
                    $tmp_m = "fail, redirect on";
                    $lm_stat_log .= " - " . $tmp_m . "\n";
                    echo "<br>" . $tmp_m . "\n";
                }
            } else {
                // Ссылка есть
                if ($lm_set[Back_Link_Needed]) {
                    $res1 = mysql_query("update Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " set Checked=1, SiteURLCom='', SiteCodeCom='' where Message_ID=" . $row[Message_ID], $LinkID);
                    if (!$row[Checked]) {
                        if ($lm_set[Send_Partner_Turnon] && !$row[Checked]) {
                            $mail_body = $lm_set[Send_Partner_Turnon_Template];
                            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Turnon_Subject], $mail_body, $mail_body, 'linkmanager_partner_turnon');
                        }
                        $lm_msg_checked .= $row[SiteURL] . "\n";
                        $lm_stat_checked++;
                    }
                    $tmp_m = "ok, turned on";
                    $lm_stat_log .= " - " . $tmp_m . "\n";
                    echo "<br>" . $tmp_m . "\n";
                } else {
                    $q1 = "";
                    if (!$lm_set[Direct_For_Good_Link])
                        $q1 .= "SiteURLCom='" . $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/linkmanager/redirect.php?url=" . $row[SiteURL] . "', ";
                    else $q1 .= "SiteURLCom='', ";
                    if (!$lm_set[HTML_In_Good_Link])
                        $q1 .= "SiteCodeCom='" . strip_tags($row[SiteCode]) . "', ";
                    else $q1 .= "SiteCodeCom='', ";
                    if (!$lm_set[Put_Up_Good_Link]) $q1 .= "Priority=1, ";
                    else $q1 .= "Priority=0, ";
                    $q1 .= "Checked=1";
                    $res1 = mysql_query("update Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " set $q1 where Message_ID=" . $row[Message_ID], $LinkID);
                    if ($lm_set[Send_Partner_Redirect_Off] && $row[SiteURLCom]) {
                        $mail_body = $lm_set[Send_Partner_Redirect_Off_Template];
                        $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                        nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Redirect_Off_Subject], $mail_body, $mail_body, 'linkmanager_partner_redirect_off');
                    }
                    $tmp_m = "ok, redirect off";
                    $lm_stat_log .= " - " . $tmp_m . "\n";
                    echo "<br>" . $tmp_m . "\n";
                }
            }
        }
    }

    if ($link_to_check != -1) die();

    // Проверяем купленные ссылки

    $q = "SELECT SiteName, SiteText, SiteEmail, SiteBackLink, SiteTurnoff
         FROM Message" . $MODULE_VARS[linkmanager][PURCHASED_LINKS_CLASS] . "
         WHERE Checked=1 and SiteTurnoff>NOW()";

    if ($link_to_check != -1) $q .= " LIMIT " . (int)$link_to_check . ",1";

    $res = mysql_query($q, $LinkID);

    if ($link_to_check != -1 && !mysql_num_rows($res)) {
        die("<!--EOF-->");
    }

    while ($row = mysql_fetch_assoc($res)) {
        $lm_regulars[LinkURL] = $row[SiteName];
        $lm_regulars[BackLink] = $row[SiteBackLink];
        $lm_regulars[LinkEmail] = $row[SiteEmail];
        $lm_regulars[LinkTurnoff] = $row[SiteTurnoff];

        $back_page = LM_Get_Page($row['SiteBackLink']);

        $bad_link = 1;
        if (LM_StringContains($back_page, $row['SiteText'])) $bad_link = 0;

        if ($bad_link && $lm_set[Send_Admin_No_Purchased] && ($link_to_check == -1)) {
            $mail_body = $lm_set[Send_Admin_No_Purchased_Template];
            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
            nc_mail2queue($lm_set[Admin_Mail], $lm_set[Spam_From], $lm_set[Send_Admin_No_Purchased_Subject], $mail_body, $mail_body, 'linkmanager_admin_no_purchased');
        }
    }

    // Проверяем проданные ссылки

    $res1 = mysql_query("select Message_ID, SiteName, SiteText, SiteEmail, SiteTurnoff from Message" . $MODULE_VARS[linkmanager][SOLD_LINKS_CLASS] . " where Checked=1 and SiteTurnoff < NOW()", $LinkID);
    if (mysql_num_rows($res1))
        while ($row = mysql_fetch_assoc($res1)) {

            $lm_regulars[LinkEmail] = $row[SiteEmail];
            $lm_regulars[LinkTurnoff] = $row[SiteTurnoff];

            $res = mysql_query("update Message" . $MODULE_VARS[linkmanager][SOLD_LINKS_CLASS] . " set Checked=0 where Message_ID=" . $row[Message_ID], $LinkID);
            if ($lm_set[Send_Partner_Sold_Turnoff] && ($link_to_check == -1)) {
                $mail_body = $lm_set[Send_Partner_Sold_Turnoff_Template];
                $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
                nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Sold_Turnoff_Subject], $mail_body, $mail_body, 'linkmanager_partner_sold_turnoff');
            }
            $lm_msg_sold_turnoff .= $row[SiteName] . " (" . $row[SiteEmail] . ")\n";
        }

    // Генерируем отчет для администратора, записываем статистику отчета
    if ($lm_set[Send_Admin_Report]) {
        $res = mysql_query("select SiteURL from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Created>'" . $lm_set[Last_Process] . "' where Checked=1", $LinkID);
        $lm_msg_new_checked .= $row[SiteURL] . "\n";
        $res = mysql_query("select SiteURL from Message" . $MODULE_VARS[linkmanager][LINKS_CLASS] . " where Created>'" . $lm_set[Last_Process] . "' where Checked=0", $LinkID);
        $lm_msg_new_unchecked .= $row[SiteURL] . "\n";

        $subj = NETCAT_MODULE_LINKS_MAIL_SUBJ_PROCESSING;

        if (count($lm_msg_no_domain)) {
            $msg .= NETCAT_MODULE_LINKS_ACHTUNG . "\n";
            foreach ($lm_msg_no_domain as $value)
                $msg .= $value . "\n";
        }

        echo "</ol>\n<br>" . NETCAT_MODULE_LINKS_REPORT_MAKE_AND_SET . " " . $lm_set[Admin_Mail];

        if ($lm_msg_sold_turnoff)
            $msg .= NETCAT_MODULE_LINKS_REPORT_DISABLED . ":\n" . $lm_msg_sold_turnoff;

        $msg .= sprintf(NETCAT_MODULE_LINKS_REPORT_EMAIL_TEMPLATE,
            $lm_set['Last_Process'],
            $lm_msg_new_checked,
            $lm_msg_new_unchecked,
            $lm_msg_unchecked,
            $lm_msg_checked,
            $lm_msg_deleted,
            $lm_stat_log
        );

        if ($link_to_check == -1)
            nc_mail2queue($lm_set[Admin_Mail], $lm_set[Spam_From], $subj, $msg);

        $q = "update Links_Settings set Last_Checked=$lm_stat_checked,
         Last_Unchecked=$lm_stat_unchecked,
         Last_Deleted=$lm_stat_deleted,
         Last_Added=" . substr_count($lm_msg_new_checked, "\n") . ",
         Last_Added_Unchecked=" . substr_count($lm_msg_new_unchecked, "\n") . ",
         Last_Process=NOW()";
        $res = mysql_query($q, $LinkID);
    }
}

?>