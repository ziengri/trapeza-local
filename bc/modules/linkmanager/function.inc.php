<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
@include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($GLOBALS['ADMIN_FOLDER']."mail.inc.php");

/**
 * Возвращает html с текстом ссылки на информацию о сайте
 *
 * @return string
 */
function LM_PopupLink($url1, $url2='') {
    global $SUB_FOLDER, $ADMIN_PATH;
    if ($url1) {
        preg_match("!^(?:http://)?(?:www\.)?([^/]+)!i", $url1, $regs);
        $site1 = $regs[1];
        $href1 = $ADMIN_PATH."siteinfo/site_info.php?url=".urlencode($url1);
    }

    if ($url2) {
        preg_match("!^(?:http://)?(?:www\.)?([^/]+)!i", $url2, $regs);
        $site2 = $regs[1];
        if ($site2 != $site1) {
            if (!$href1) {
                $href1 = $ADMIN_PATH."siteinfo/site_info.php?url=".urlencode($url2);
            } else {
                $href2 = $ADMIN_PATH."siteinfo/site_info.php?url=".urlencode($url2);
            }
        }
    }

    $open_params = "toolbar=no,directories=no,status=yes,menubar=yes,scrollbars=yes,location=no,resizable=yes,copyhistory=no,width=350,height=450";
    $ret = "[<a href='$href1' target=_blank onclick='open(\"$href1\", \"site1info\", \"$open_params\");";
    if ($href2) $ret .= "open(\"$href2\", \"site2info\", \"$open_params\");";
    $ret .= "; return false;'>?</a>]";

    return $ret;
}

function LM_Get_Set() {
    global $LinkID;
    static $result = NULL;

    if ($result != NULL) return $result;

    $q = mysql_query("SELECT * FROM Links_Settings LIMIT 1", $LinkID);
    if (!mysql_num_rows($q)) return NULL;

    $result = mysql_fetch_assoc($q);
    @mysql_free_result($q);

    return $result;
}

function LM_Verify_Link($siteurl, $sitecode, $backlink, $message, $siteemail, $action) {
    global $MODULE_VARS, $catalogue, $_SERVER;
    static $result = NULL;

    if ($result != NULL) return $result;

    $lm_set = LM_Get_Set();

    # Проверять, нет ли в $sitecode ссылок на другие хосты, нежели $siteurl

    $site_host = @parse_url($siteurl);
    if (!$site_host) return false;
    if ($lm_set[Fail_If_Many_Hosts]) {
        nc_preg_match_all("/(http?[\D]:\/\/[^\/\"'>]+)/i", $sitecode, $matches);
        foreach ($matches[0] as $k => $v) {
            $tm_site = parse_url($v);
            if ($tm_site[host] != $site_host[host])
                    $warn = NETCAT_MODULE_LINKS_ERROR_LINKS_TO_OTHER_SITES;
        }
    }

    if ($backlink && $backlink != "http://") {
        $bl_h = parse_url($backlink);
        $site_h = parse_url($siteurl);
        $bl_host = $bl_h[host];

        if (strpos($bl_host, "www.") === 0) $bl_host1 = substr($bl_host, 4);
        else $bl_host1 = "www.".$bl_host;

        $site_host = $site_h[host];
        if ($lm_set[Back_Link_At_Site] == 1 && ($bl_host != $site_host))
                $warn = NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_SAME_SITE;
        if ($lm_set[Back_Link_At_Site] == 2 && ($bl_host == $site_host))
                $warn = NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_OTHER_SITE;

        if ($action == "add") {
            if (listQuery("select count(a.Message_ID) as count from Message".$MODULE_VARS[linkmanager][LINKS_CLASS]." as a, Subdivision as b where SiteBackURL like 'http://".$bl_host."%' and b.Subdivision_ID=a.Subdivision_ID and b.Catalogue_ID=$catalogue and a.Checked=1", "\$data[count]"))
                    $warn = NETCAT_MODULE_LINKS_ERROR_DUPLICATE_BACK_LINK;
        }
        else {
            if (listQuery("select count(a.Message_ID) as count from Message".$MODULE_VARS[linkmanager][LINKS_CLASS]." as a, Subdivision as b where SiteBackURL like 'http://".$bl_host."%' and b.Subdivision_ID=a.Subdivision_ID and b.Catalogue_ID=$catalogue and a.Message_ID<>$message and a.Checked=1", "\$data[count]"))
                    $warn = NETCAT_MODULE_LINKS_ERROR_DUPLICATE_BACK_LINK;
        }

        if ($lm_set[Fail_If_Third_Level])
                if (substr_count($bl_host, ".") > 2 || (substr_count($bl_host, ".") == 2 && !(strpos($bl_host, "www.") === 0)))
                    $warn = NETCAT_MODULE_LINKS_ERROR_NOT_2ND_LEVEL_DOMAIN;

        if (listQuery("select count(Message_ID) as count from Message".$MODULE_VARS[linkmanager][STOP_CLASS]." where StopDomain='$bl_host' or StopDomain='$bl_host1'", "\$data[count]"))
                $warn = sprintf(NETCAT_MODULE_LINKS_ERROR_DOMAIN_IN_STOP_LIST, $lm_set['Admin_Mail']);
    }
    if ($warn) return $warn;
    else return NULL;
}

function LM_generate_mail_text($text, $vars) {
    $text = str_replace("%LinkURL", $vars[LinkURL], $text);
    $text = str_replace("%LinkName", $vars[LinkName], $text);
    $text = str_replace("%BackLink", $vars[BackLink], $text);
    $text = str_replace("%OurSite", $vars[OurSite], $text);
    $text = str_replace("%DeleteTime", $vars[DeleteTime], $text);
    $text = str_replace("%OurCatalog", $vars[OurCatalog], $text);
    $text = str_replace("%StaticLink", $vars[StaticLink], $text);
    $text = str_replace("%LinkEmail", $vars[LinkEmail], $text);
    $text = str_replace("%LinkTurnoff", $vars[LinkTurnoff], $text);
    $text = str_replace("%AdminEmail", $vars[AdminEmail], $text);
    $text = str_replace("%LinkEditMode", $vars[LinkEditMode], $text);

    return $text;
}

/**
 * Проверить содержимое страницы (back_page) на наличие ссылок
 *
 * @param string body of the page
 * @param integer catalogue_id
 * @return integer returns 0 (!) if link pattern found, 1 otherwise
 */
function LM_CheckOurCodesOnPage($back_page, $lm_site_id=false) {
    global $MODULE_VARS;
    $bad_link = 1;

    if (!$lm_site_id) $lm_site_id = $GLOBALS["catalogue"];

    $q = "SELECT a.SiteText as pattern
           FROM `Message".intval($MODULE_VARS['linkmanager']['OUR_CODES_CLASS'])."` as a,
                Subdivision as b
          WHERE a.Subdivision_ID=b.Subdivision_ID
            AND b.Catalogue_ID=".intval($lm_site_id);
    $res2 = mysql_query($q);
    while ($row2 = mysql_fetch_assoc($res2)) {
        if (LM_StringContains($back_page, $row2['pattern'])) {
            $bad_link = 0;
            break;
        }
    }
    return $bad_link;
}

/**
 * Возвращает истину, если $haystack содержит $needle. Проверяется в win1251, koi8
 *
 * @param string $needle
 * @param string $haystack
 * @return boolean
 */
function LM_StringContains($haystack, $needle) {
    if (strpos($haystack, $needle) !== FALSE) {
        return true;
    }
    // check for string in KOI8
    if (strpos($haystack, convert_cyr_string($needle, "w", "k")) !== FALSE) {
        return true;
    }
    return false;
}

/**
 * Получить страницу
 * @param string URL
 * @return string Page Body
 */
function LM_Get_Page($url) {
    static $ua;

    if (!$url) return;

    // create UA
    require_once("HTTP/Client.php");
    if (!$ua)
            $ua = new HTTP_Client(null, array("Accept" => "*/*", "Accept-Language" => "ru,en", "Accept-Encoding" => "gzip,deflate", "User-Agent" => $_SERVER["HTTP_USER_AGENT"]));

    $ua->get($url, null, true);
    $response = $ua->currentResponse();

    return $response["body"];
}

function LM_Send_Mail_Added() {
    global $LinkID, $_SERVER, $MODULE_VARS, $catalogue, $sub, $cc, $HTTP_HOST, $SUB_FOLDER, $HTTP_ROOT_PATH;

    $lm_set = LM_Get_Set();

    $q = "select * from Message".$MODULE_VARS[linkmanager][LINKS_CLASS]." order by Message_ID desc limit 1";
    $res = mysql_query($q);
    $row = mysql_fetch_assoc($res);

    // Проверяем, есть ли обратная ссылка или текст
    $back_page = LM_Get_Page($row['SiteBackURL'] ? $row['SiteBackURL'] : $row['SiteURL']);

    $bad_link = 1;
    if ($lm_set['Check_Whole_Text']) {

        $bad_link = LM_CheckOurCodesOnPage($back_page, $catalogue);
    } else {
        // Ищем ссылку на домен текущего сайта на странице
        $our_host = $HTTP_HOST.$SUB_FOLDER;
        if (strpos($our_host, "www.") === 0) $our_host1 = substr($our_host, 4);
        else $our_host1 = "www.".$our_host;
        nc_preg_match_all("/(http?[\D]:\/\/[^\/\"'>]+)/i", $back_page, $matches);
        foreach ($matches[0] as $k => $v) {
            $tm_site = parse_url($v);
            if ($tm_site[host] == $our_host || $tm_site[host] == $our_host1)
                    $bad_link = 0;
        }
    }

    // Отправляем письмо админу и партнеру

    $lm_regulars['OurSite'] = $HTTP_HOST;
    $lm_regulars['LinkURL'] = $row['SiteURL'];
    $lm_regulars['LinkName'] = $row['SiteName'];
    $lm_regulars['BackLink'] = $row['SiteBackURL'];
    $lm_regulars['AdminEmail'] = $lm_set['Admin_Mail'];
    $lm_regulars['LinkEditMode'] = "http://".$lm_regulars['OurSite'].$SUB_FOLDER.$HTTP_ROOT_PATH."message.php?catalogue=$catalogue&sub=$sub&cc=$cc&message=".$row[Message_ID];

    $sub_u = listQuery("select Hidden_URL from Subdivision where Subdivision_ID=$sub", "\$data[Hidden_URL]");
    $cc_u = listQuery("select EnglishName from Sub_Class where Sub_Class_ID=$cc", "\$data[EnglishName]");
    $lm_regulars[StaticLink] = $lm_regulars[OurSite].$sub_u.$cc_u."_".$row[Message_ID].".html";

    $q = "select Subdivision_ID from Message".$MODULE_VARS[linkmanager][LINKS_CLASS]." where Message_ID=".$row[Message_ID];
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
            $lm_regulars[OurCatalog] = $lm_regulars[OurSite].listQuery("select Hidden_URL from Subdivision where Subdivision_ID=$right_id", "\$data[Hidden_URL]");
        }
    }

    if ($lm_set[Send_Admin_Added]) {
        $mail_body = $lm_set[Send_Admin_Added_Template];
        $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
        nc_mail2queue($lm_set[Admin_Mail], $lm_set[Spam_From], $lm_set[Send_Admin_Added_Subject], $mail_body);
    }

    if ($lm_set[Back_Link_Needed]) {
        if ($bad_link && $lm_set[Send_Partner_Added_Bad_Link]) {
            $mail_body = $lm_set[Send_Partner_Added_Bad_Link_Template];
            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Added_Bad_Link_Subject], $mail_body);
        }
        if (!$bad_link && $lm_set[Send_Partner_Added_Good_Link]) {
            $mail_body = $lm_set[Send_Partner_Added_Good_Link_Template];
            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Added_Good_Link_Subject], $mail_body);
        }
    } else {
        if ($bad_link && $lm_set[Send_Partner_Added_Redirect_Link]) {
            $mail_body = $lm_set[Send_Partner_Added_Redirect_Link_Template];
            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Added_Redirect_Link_Subject], $mail_body);
        }
        if (!$bad_link && $lm_set[Send_Partner_Added_Direct_Link]) {
            $mail_body = $lm_set[Send_Partner_Added_Direct_Link_Template];
            $mail_body = LM_generate_mail_text($mail_body, $lm_regulars);
            nc_mail2queue($row[SiteEmail], $lm_set[Spam_From], $lm_set[Send_Partner_Added_Direct_Link_Subject], $mail_body);
        }
    }
    return NETCAT_MODULE_LINKS_ADDED;
}
?>