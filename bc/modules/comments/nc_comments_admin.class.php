<?php

/* $Id: nc_comments_admin.class.php 8113 2012-09-10 10:51:23Z ewind $ */

class nc_comments_admin {

    protected $db, $UI_CONFIG, $ADMIN_TEMPLATE;

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        // global variables
        global $db, $UI_CONFIG, $ADMIN_TEMPLATE, $MODULE_FOLDER;

        // global variables to internal
        $this->db = & $db;
        $this->ADMIN_TEMPLATE = $ADMIN_TEMPLATE;
        $this->MODULE_FOLDER = $MODULE_FOLDER;
        // superglobal variable
        $this->POST = $_POST;

        return;
    }

    /**
     * Return url of main settings.
     *
     * @access public
     * @return string
     */
    public function get_mainsettings_url() {
        return "#module.comments.settings";
    }

    /**
     * Select catalogue, subdivision and subclass that contain comments to convert.
     *
     * @access public
     * @param int $phase (default: 4)
     * @param int $catalogue (default: 0)
     * @param int $subdivision (default: 0)
     * @return void
     */
    public function converter($phase = 4, $catalogue = 0, $subdivision = 0) {

        $catalogue = intval($catalogue);
        $subdivision = intval($subdivision);

        $error = false;

        if (($phase == 5 && !$catalogue) || ($phase == 6 && !$subdivision))
            $phase--;

        echo "<form method='post' action='admin.php' id='ConvertComments' style='padding:0; margin:0;'>\n" .
            "<input type='hidden' name='phase' value='" . ($phase + 1) . "'>\n" .
            "<input type='hidden' name='page' value='converter'>\n" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_DIALOG . "\n" .
            "</legend>\n";

        if ($phase == 4) {
            $catalogues = $this->db->get_results("SELECT `Catalogue_ID`, `Catalogue_Name` FROM `Catalogue`", ARRAY_A);
            if (!empty($catalogues)) {
                echo "<div style='padding:10px 0 5px'>" .
                    "" . NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_CATALOGUE . "<br>" .
                    "<select name='ConverterCatalogue' style='width:50%'>";
                foreach ($catalogues AS $value) {
                    echo "<option value='" . $value['Catalogue_ID'] . "'>" . $value['Catalogue_Name'] . "</option>";
                }
                echo "</select>";
            } else {
                nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_CATALOGUE_ERROR, "error");
                $error = true;
            }
        }

        if ($phase == 5) {
            $subdivisions = $this->db->get_results("SELECT `Subdivision_ID` AS value,
          CONCAT(`Subdivision_ID`, '. ', `Subdivision_Name`) AS description, `Parent_Sub_ID` AS parent
          FROM `Subdivision`
          WHERE `Catalogue_ID` = '" . $catalogue . "'
          ORDER BY `Subdivision_ID`", ARRAY_A);
            if (!empty($subdivisions)) {
                echo "<div style='padding:10px 0 5px'>" .
                    "" . NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_SUBDIVISION . "<br>" .
                    "<input type='hidden' name='ConverterCatalogue' value='" . $catalogue . "'>" .
                    "<select name='ConverterSubdivision' style='width:50%'>";
                echo nc_select_options($subdivisions);
                echo "</select>";
            } else {
                nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_SUBDIVISION, "info");
                $error = true;
            }
        }

        if ($phase == 6) {
            $subclasses = $this->db->get_results("SELECT `Sub_Class_ID`, `Sub_Class_Name`
          FROM `Sub_Class`
          WHERE `Subdivision_ID` = '" . $subdivision . "'
          ORDER BY `Sub_Class_ID`", ARRAY_A);
            if (!empty($subclasses)) {
                echo "<div style='padding:10px 0 5px'>" .
                    "" . NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_SUBCLASS . "<br>" .
                    "<input type='hidden' name='ConverterCatalogue' value='" . $catalogue . "'>" .
                    "<input type='hidden' name='ConverterSubdivision' value='" . $subdivision . "'>" .
                    "<select name='ConverterSubClass' style='width:50%'>";
                foreach ($subclasses AS $value) {
                    echo "<option value='" . $value['Sub_Class_ID'] . "'>" . $value['Sub_Class_Name'] . "</option>";
                }
                echo "</select>";
            } else {
                nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_SUBCLASS, "info");
                $error = true;
            }
        }

        echo "</legend>\n" .
            "</fieldset>\n";

        global $UI_CONFIG;
        // admin buttons
        if (!$error) {
            $UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => constant("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SAVE_BUTTON_" . $phase),
                "action" => "mainView.submitIframeForm('ConvertComments')"
            );
        } else {
            $UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_RETURN_BUTTON,
                "location" => "module.comments.converter(4)"
            );
        }

        echo "</form><br/>\n";

        return;
    }

    /**
     * Convert chosen comments (by catalogue, sub and subclass) to module form.
     *
     * @access public
     * @return void
     */
    public function converterSave() {

        $catalogue = intval($_POST['ConverterCatalogue']);
        $subdivision = intval($_POST['ConverterSubdivision']);
        $subclass = intval($_POST['ConverterSubClass']);

        if (!$catalogue && !$subdivision && !$subclass) {
            // wrong data
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_DATA_ERROR, "error");
            return false;
        }

        $classid = $this->db->get_var("SELECT `Class_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` = '" . $subclass . "'");

        if (!$classid) {
            // wrong data
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_CLASS_ERROR, "error");
            return false;
        }

        // error descriptor
        $error = false;

        $fields = array(
            "Object_Sub_Class_ID" => "Sub_Class_ID",
            "Object_ID" => "Message_ID",
            "Object_Parent_ID" => "Parent_Comment_ID",
            "Created" => array("Date", "Updated"),
            "Message" => "Comment",
            "User_ID" => "User_ID"
        );

        // move data from MessageXX to Comments_Text and get ids
        $converted_ids = nc_copy_data("Message" . $classid, "Comments_Text", $fields, true, "`Sub_Class_ID` = '" . $subclass . "'");

        if (!empty($converted_ids)) {
            // array of correspondence old ids of parents to new ids
            $parent_relation = array();

            // choose comments with oldest ids of parents from Comments_text
            $children = $this->db->get_results("SELECT m.`Object_Parent_ID` AS old_parent, c.`id` AS children_with_old_parent
        FROM `Message" . $classid . "` AS m
        LEFT JOIN `Comments_Text` AS c ON m.`Object_ID` = c.`Message_ID`
          AND m.`Object_Sub_Class_ID` = c.`Sub_Class_ID`
          AND m.`Object_Parent_ID` = c.`Parent_Comment_ID`
        WHERE m.`Object_Parent_ID` > 0 AND c.`id` IN (" . join(",", $converted_ids) . ")", ARRAY_A);

            $old_parent = array();
            //$children_with_old_parent = array();
            if (!empty($children)) {
                foreach ($children as $value) {
                    $old_parent[] = $value['old_parent'];
                    //$children_with_old_parent[] = $value['children_with_old_parent'];
                }
            }
            $old_parent = array_unique($old_parent);
            if (!empty($old_parent)) {
                // choose old ids of parents from MessageXX and their new ids from Comments_Text
                $parents = $this->db->get_results("SELECT m.`Message_ID` AS old_parent_id, c.`id` AS new_parent_id
          FROM `Message" . $classid . "` AS m
          LEFT JOIN `Comments_Text` AS c ON m.`Object_ID` = c.`Message_ID`
            AND m.`Object_Sub_Class_ID` = c.`Sub_Class_ID`
            AND m.`Object_Parent_ID` = c.`Parent_Comment_ID`
            AND m.`Message` = c.`Comment`
            AND m.`Created` = c.`Date`
            AND m.`User_ID` = c.`User_ID`
          WHERE m.`Message_ID` IN (" . join(",", $old_parent) . ")", ARRAY_A);

                if (count($old_parent) == count($parents)) {

                    if (!empty($parents)) {
                        // sort: old id from MessageXX => new id from Comments_Text
                        foreach ($parents as $value) {
                            $parent_relation[$value['old_parent_id']] = $value['new_parent_id'];
                        }
                    }

                    foreach ($children as $value) {
                        // new Parent id for comment
                        $new_parent = $parent_relation[$value['old_parent']];
                        // refresh data
                        $this->db->query("UPDATE `Comments_Text`
              SET `Parent_Comment_ID` = '" . $new_parent . "'
              WHERE `id` = '" . $value['children_with_old_parent'] . "'");
                    }
                } else {
                    // количественные данные не совпадают, это может быть из-за повторного конвертирования
                    nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_PARENT_ERROR, "error");
                    // удалим то, что добавили в этот раз
                    $this->db->query("DELETE FROM `Comments_Text` WHERE `id` IN (" . join(",", $converted_ids) . ")");
                    // дескриптор ошибки
                    $error = true;
                }
            }
            // count comments and replies
            $this->optimizeSave();
        } else {
            // wrong data
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_DATA, "info");
            // error descriptor
            $error = true;
        }

        return !$error;
    }

    /**
     * Print form to optimize comments data.
     *
     * @access public
     * @return void
     */
    public function optimize() {

        echo "<form method='post' id='DoOptimize' action='admin.php' style='padding:0; margin:0;'>\n" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE . "\n" .
            "</legend>\n" .
            // use default
            "<div style='margin:10px 0; _padding:0;'>\n" .
            "<input id='opt_comments' type='checkbox' name='OptimizeComments' value='1'/><label for='opt_comments'>" . NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_DO .
            "</label></div>\n" .
            "</fieldset><br/>\n";

        // admin buttons
        global $UI_CONFIG;
        $UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_DO_BUTTON,
            "action" => "mainView.submitIframeForm('DoOptimize')"
        );

        echo "<input type='hidden' name='phase' value='81'>\n" .
            "<input type='hidden' name='page' value='optimize'>\n" .
            "</form><br/>\n";

        return;
    }

    /**
     * Optimize comments data.
     *
     * @access public
     * @return int total inserted rows
     */
    public function optimizeSave() {
        // get counted data
        $data = $this->db->get_results("SELECT COUNT(`id`) AS Total, COUNT( IF(`Parent_Comment_ID`, 1, NULL) ) AS Replies, `Sub_Class_ID`, `Message_ID`
      FROM `Comments_Text`
      GROUP BY `Sub_Class_ID`, `Message_ID`", ARRAY_A);

        if (empty($data))
            return false;

        // clear table with old counted data
        $this->db->query("TRUNCATE TABLE `Comments_Count`");

        $i = 0;
        foreach ($data as $value) {
            // insert counted row data
            $this->db->query("INSERT INTO `Comments_Count`
        (`Sub_Class_ID`, `Message_ID`, `CountComments`, `CountReplies`)
        VALUES
        ('" . intval($value['Sub_Class_ID']) . "', '" . intval($value['Message_ID']) . "', '" . (intval($value['Total']) - intval($value['Replies'])) . "', '" . intval($value['Replies']) . "')");
            if ($this->db->insert_id)
                $i++;
        }

        // return total inserted rows
        return $i;
    }

    /**
     * Return search form.
     *
     * @access public
     * @return html text
     */
    public function search_comments_form() {

        global $com_sub, $com_sub_class, $checked, $qty, $order_by, $sort_by;
        $html = "";
        $html = "<div>
 		<fieldset>
 		<legend>
		" . NETCAT_MODULE_COMMENTS_ADMIN_COMMENTS_LIST_SELECT . "
		</legend>";

        $html .= "
  	<form method='post' action='admin.php' id='CommentSearchForm'>
  	  <table border='0' cellpadding='0' cellspacing='0' width='100%'>
  	    <tr>
  	      <td>";


        $subdivisions = $this->db->get_results("SELECT s.`Subdivision_ID` AS value,
                                                        CONCAT(s.`Subdivision_ID`, '. ', s.`Subdivision_Name`) AS description
                                                        FROM `Subdivision` AS s, `Sub_Class` as sc, `Comments_Text` as c
                                                        WHERE s.`Subdivision_ID` = sc.`Subdivision_ID` AND c.`Sub_Class_ID` = sc.`Sub_Class_ID`
                                                        GROUP BY s.`Subdivision_ID`
                                                        ORDER BY s.`Subdivision_ID`", ARRAY_A);
        if (!empty($subdivisions)) {
            $html .= "<div style='padding:10px 10px'>" .
                "" . NETCAT_MODULE_COMMENTS_ADMIN_SUBDIVISION . "<br>" .
                "<select name='com_sub' style='width: 80%;'>";
            $html .= "<option value='0'>" . NETCAT_MODERATION_MOD_NOANSWER . "</option>";
            $html .= nc_select_options($subdivisions, $com_sub);
            $html .= "</select>";
        }


        $html .= "</fieldset></div></td><td>";

        $subclasses = $this->db->get_results("SELECT sc.`Sub_Class_ID` AS value,
                                            CONCAT(sc.`Sub_Class_ID`, '. ', sc.`Sub_Class_Name`) AS description,
                                            c.`Sub_Class_ID`
                                            FROM `Sub_Class` as sc, `Comments_Text` as c
                                            WHERE c.`Sub_Class_ID` = sc.`Sub_Class_ID`
                                            GROUP BY sc.`Sub_Class_ID`
                                            ORDER BY sc.`Sub_Class_ID`", ARRAY_A);
        if (!empty($subclasses)) {
            $html .= "<div style='padding:10px 10px'>" .
                "" . NETCAT_MODULE_COMMENTS_ADMIN_CLASS . "<br>" .
                "<select name='com_sub_class' style='width: 80%;'>";
            $html .= "<option value='0'>" . NETCAT_MODERATION_MOD_NOANSWER . "</option>";
            $html .= nc_select_options($subclasses, $com_sub_class);
            $html .= "</select>";
        }

        $html .= "</div></td>
            </tr>
            <tr>
              <td colspan='2' style='padding:0 5px'><nobr><font color=gray>
              <input checked id=chk1 type='radio' name=checked value='' " . (!$checked ? "checked" : "") . ">
                <label for=chk1>" . NETCAT_MODULE_COMMENTS_ADMIN_ALLUSERS . "</label>
              <input id=chk2 type='radio' name=checked value=1 " . ($checked == 1 ? "checked" : "") . ">
              <label for=chk2>" . NETCAT_MODULE_COMMENTS_ADMIN_ONUSERS . "</label>
              <input id=chk3 type='radio' name=checked value=2 " . ($checked == 2 ? "checked" : "") . ">
              <label for=chk3>" . NETCAT_MODULE_COMMENTS_ADMIN_OFFUSERS . "</label>
              </nobr>
              </td>
            </tr>
            <tr>
              <td colspan='2' style='padding:5px 10px'>
							" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_QTY . "
              <input type='text' name='qty' size='3' style='margin: 0 0 0 5px;' value=" . ($qty ? htmlentities($qty, ENT_QUOTES, MAIN_ENCODING) : "") . ">
              </td>
            </tr>";

        $html .= "<tr><td valign='bottom' align='right' colspan='2'>
      <input type='submit' title='" . NETCAT_MODULE_COMMENTS_ADMIN_DOGET . "' value='" . NETCAT_MODULE_COMMENTS_ADMIN_DOGET . "' style='margin: 0 10px;'>
      <input type='hidden' name=phase value=''>
	    <input type='hidden' name='is_search' value='1'>
      </form></td></tr></table>";

        $html .= "</fieldset></div><br>";

        return $html;
    }

    /**
     * Form pagination html text
     *
     * @access public
     * @param array $cc_env
     * @param int $range
     * @return html text
     */
    public function listing($cc_env, $range) {

        $nc_core = nc_Core::get_object();
        global $browse_msg, $order_by, $sort_by;

        $curPos = $cc_env['curPos'] + 0;
        $maxRows = $cc_env['maxRows'];
        $totRows = $cc_env['totRows'];

        $page_count = ceil($totRows / $maxRows);
        $half_range = ceil($range / 2);
        $cur_page = ceil($curPos / $maxRows) + 1;

        if ($page_count < 2)
            return;

        $maybe_from = $cur_page - $half_range;
        $maybe_to = $cur_page + $half_range;

        if ($maybe_from < 0) {
            $maybe_to = $maybe_to - $maybe_from;
            $maybe_from = 0;

            if ($maybe_to > $page_count)
                $maybe_to = $page_count;
        }

        if ($maybe_to > $page_count) {
            $maybe_from = $page_count - $range;
            $maybe_to = $page_count;

            if ($maybe_from < 0)
                $maybe_from = 0;
        }

        // prefix
        eval("\$result = \"" . $browse_msg['prefix'] . "\";");

        // формируем ссылку
        // const_url не меняется для каждой страницы
        $const_url = $cc_env['LocalQuery'] . ($order_by ? "&order_by=" . (int)$order_by : "&order_by=0") . ($sort_by == 1 ? "&sort_by=1" : $sort_by == 2 ? "&sort_by=2" : "");
        if ($const_url == '?')
            $const_url = '';

        for ($i = $maybe_from; $i < $maybe_to; $i++) {
            $page_number = $i + 1;
            $page_from = $i * $maxRows;
            $page_to = $page_from + $maxRows;

            // ссылка не на первую страницу
            if ($page_from) {
                $url = $const_url . (strpos($const_url, "?") !== false ? "&" : "?") . "curPos=" . $page_from;
            } else { // ссылка на первую страницу, curPos не нужен
                $url = $const_url ? $const_url : $nc_core->url->get_parsed_url('path');
            }

            // clear already existance &amp; and replace all & to &amp; view
            $url = nc_preg_replace(array("/&amp;/", "/&/"), array("&", "&amp;"), $url);

            if ($curPos == $page_from) {
                eval("\$result .= \"" . $browse_msg['active'] . "\";");
            } else {
                eval("\$result .= \"" . $browse_msg['unactive'] . "\";");
            }

            $result = str_replace("%URL", $url, $result);
            $result = str_replace("%PAGE", $page_number, $result);
            $result = str_replace("%FROM", $page_from + 1, $result);
            $result = str_replace("%TO", $page_to, $result);

            if ($i != ($maybe_to - 1))
                eval("\$result .= \"" . $browse_msg['divider'] . "\";");
        }

        eval("\$result .= \"" . $browse_msg['suffix'] . "\";");

        return $result;
    }

    /**
     * Print comments list
     *
     * @access public
     * @return void
     */
    public function comments_list() {

        global $com_sub, $com_sub_class, $checked, $qty, $is_search, $order_by, $sort_by, $curPos;

        $nc_core = nc_Core::get_object();

        $where = "";
        $url = "";
        if ($com_sub) {
            $where .= "AND sc.`Subdivision_ID` = " . (int)$com_sub . " ";
            $url .= "&com_sub=" . (int)$com_sub;
        }
        if ($com_sub_class) {
            $where .= "AND c.`Sub_Class_ID` = " . (int)$com_sub_class . " ";
            $url .= "&com_sub_class=" . (int)$com_sub_class;
        }

        if ($checked) {
            $where .= " AND c.Checked = '" . ($checked == 1 ? 1 : 0) . "'  ";
            $url .= "&checked=" . (int)$checked;
        }

        $order_by_db = $order_by == 1 ? "ASC" : "DESC";
        $sort_by_db = !$sort_by ? "c.`id` " . $order_by_db : ($sort_by == 1 ? "c.`id` " . $order_by_db : "`User_Login`" . $order_by_db . ", c.`id`");

        $maxRows = $this->db->get_row("SELECT COUNT(*) AS count
                            FROM `Comments_Text` as c
                            LEFT JOIN `User` as u ON u.`User_ID`= c.`User_ID`
                            LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = c.`Sub_Class_ID`
                            WHERE 1 " . $where . "
                            ORDER BY " . $sort_by_db . " ", ARRAY_A);
        $maxRows = $maxRows['count'];

        $qty = $qty ? $qty : 20;
        $curPos += 0;

        if ($qty || $curPos) {
            $totRows = $maxRows;
            $current_url = $nc_core->url->get_parsed_url("path");
            $maxRows = ($qty ? $qty : $maxRows);
            $range = 10;
            $env['maxRows'] = $maxRows;
            $env['totRows'] = $totRows;
            $env['curPos'] = $curPos;
            $env['LocalQuery'] = $current_url . "?is_search=1&qty=" . $qty . $url;
            // DEPRECATED
            global $browse_msg;
            $browse_msg['prefix'] = "";
            $browse_msg['suffix'] = "";
            $browse_msg['active'] = "<b>%PAGE</b>";
            $browse_msg['unactive'] = "<a href=%URL>%PAGE</a>";
            $browse_msg['divider'] = " | ";
            $last_pos = (ceil($totRows / $maxRows) * $maxRows - $maxRows);
        }
        $data = $this->db->get_results("SELECT c.*, u.`Login` as User_Login, u.`User_ID`,
  					sc.`Class_ID`, sc.`Subdivision_ID`, sc.`Catalogue_ID`
                                        FROM `Comments_Text` as c
                                        LEFT JOIN `User` as u ON u.`User_ID`= c.`User_ID`
                                        LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = c.`Sub_Class_ID`
                                        WHERE 1 " . $where . "
                                        ORDER BY " . $sort_by_db . "
                                        LIMIT " . intval($curPos) . ", " . intval($maxRows) . " ", ARRAY_A);
        if (empty($data)) {
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_NO_COMMENTS, 'info');
            return false;
        }

        $searchForm = $this->search_comments_form($search_params);
        echo $searchForm;


        echo "<form method='post' action='admin.php' name='mainForm' id='mainForm'>
			<input type='hidden' id='phase' name='phase' value=''>
  		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
  			<tr>
  				<td>
  					<table class='admin_table' width=100%>
  						<tr>
  							<td><a href='admin.php?phase=1&sort_by=1&order_by=" . (!$order_by ? !$order_by : 0) . "&qty=" . $qty . $url . "'>ID</a>" . (($order_by && $sort_by == 1) ? "&nbsp;&nbsp;&#x2191;" : ($sort_by == 1 ? "&nbsp;&nbsp;&#x2193;" : "")) . "</td>
  							<td><a href='admin.php?phase=1&sort_by=2&order_by=" . (!$order_by ? !$order_by : 0) . "&qty=" . $qty . $url . "'>" . NETCAT_MODULE_COMMENTS_ADMIN_LIST_AUTHOR . "</a>" . (($order_by && $sort_by == 2) ? "&nbsp;&nbsp;&#x2191;" : ($sort_by == 2 ? "&nbsp;&nbsp;&#x2193;" : "")) . "</td>
  							<td width=50%>" . NETCAT_MODULE_COMMENTS_ADMIN_EDIT_TEXT . "</td>
  							<td align=right width=20%>" . CONTROL_USER_ACTIONS . "</td>
  							<td align=center>" . ($nc_core->get_settings('PacketOperations') ? "<div class='icons icon_type_bool' title='" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE . "'></div>" : "") . "</td>
  						</tr>";


        foreach ($data as $comment) {
            echo "<tr>
    	<td><font " . (!$comment['Checked'] ? "style='color:#cccccc;'" : "") . ">" . $comment['id'] . "</font></td>
    	<td><font " . (!$comment['Checked'] ? "style='color:#cccccc;'" : "") . ">" . ($comment['User_Login'] ? "<a href='" . $nc_core->ADMIN_PATH . "user/index.php?phase=4&UserID=" . $comment['User_ID'] . "' " . (!$comment['Checked'] ? "style='color:#cccccc;'" : "") . ">" . $comment['User_Login'] . "</a>" : ($comment['Guest_Name'] ? ($comment['Guest_Email'] ? $comment['Guest_Name'] . " (" . $comment['Guest_Email'] . ")" : $comment['Guest_Name']) : ($comment['Guest_Email'] ? "Гость (" . $comment['Guest_Email'] . ")" : NETCAT_MODULE_COMMENTS_GUEST))) . "</font></td>
    	<td><font
" . (!$comment['Checked'] ? "style='color:#cccccc;'>" . nc_bbcode($comment['Comment']) :
                "><a target='_blank'href='" . nc_message_link($comment['Message_ID'], $comment['Class_ID']) . "#nc_commentID" . $comment['Sub_Class_ID'] . "_" . $comment['Message_ID'] . "_" . $comment['id'] . "' " . (!$comment['Checked'] ? "style='color:#cccccc;'" : "") . ">" . nc_bbcode($comment['Comment']) . "</a>") . "</font></td>
    	<td align='right'>

    			<a href=admin.php?phase=11&comment=" . $comment['id'] . "&sort_by=" . intval($sort_by) . "&order_by=" . intval($order_by) . "&qty=" . intval($qty) . $url . "&curPos=" . $curPos . "&action=" . ($comment['Checked'] ? "Uncheck>" . NETCAT_MODULE_COMMENTS_ADMIN_UNCHECK : "Check>" . NETCAT_MODULE_COMMENTS_ADMIN_CHECK) . "</a> |
    			<a href=admin.php?phase=15&message_cc=" . $comment['Sub_Class_ID'] . "&comment=" . $comment['id'] . ">" . NETCAT_MODULE_COMMENTS_ADMIN_EDIT . "</a>
    		</td>
    	<td align=center>" . ($nc_core->get_settings('PacketOperations') ? "<input type=checkbox name='comment" . $comment['id'] . "' value=" . $comment['Sub_Class_ID'] . ">" : "") . "</td>
    	</tr>
    	";
        }

        echo "</table>
    </td>
    </tr>
    </table>
    </form>";

        if ($qty || $curPos) {

            // pagination data
            $pagination = $this->listing($env, $range);
            if ($pagination) {
                // pagination prefix
                echo "<div style='margin:10px 0'>";

                // first link
                if ($curPos) {
                    echo "<a href='" . $env['LocalQuery'] . "&curPos=0'>&laquo;&laquo;</a>&nbsp;&nbsp;";
                }

                // pagination
                echo $pagination;

                // last link
                if ($curPos != $last_pos && (($range + 1) * $maxRows) < $totRows) {
                    echo "&nbsp;&nbsp;<a href='" . $env['LocalQuery'] . "&curPos=" . $last_pos . "'>&raquo;&raquo;</a>";
                }

                // pagination suffix
                echo "</div>";
            }
        }

        echo "<script type='text/javascript'>\n
   	function sumbit_form ( phase ) {\n
   	  document.getElementById('mainForm').phase.value =  phase;\n
   	  parent.mainView.submitIframeForm('mainForm');\n
   	  return 0;\n
   	}\n
   	</script>\n";

        global $UI_CONFIG;

        // button "Удалить все"
        $UI_CONFIG->actionButtons[] = array(
            "id" => "deleteAll",
            "caption" => NETCAT_MODERATION_REMALL,
            "align" => "left",
            "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(13)",
            "red_border" => true,
        );
        if ($nc_core->get_settings('PacketOperations')) {
            // button "Удалить выбранные"
            $UI_CONFIG->actionButtons[] = array(
                "id" => "deleteChecked",
                "caption" => NETCAT_MODERATION_DELETESELECTED,
                "align" => "left",
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(131)",
                "red_border" => true,
            );
            // button "Включить выбранные"
            $UI_CONFIG->actionButtons[] = array(
                "id" => "checkOn",
                "caption" => NETCAT_MODERATION_SELECTEDON,
                "align" => "left",
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(12)"
            );
            // button "Выключить выбранные"
            $UI_CONFIG->actionButtons[] = array(
                "id" => "checkOff",
                "caption" => NETCAT_MODERATION_SELECTEDOFF,
                "align" => "left",
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(121)"
            );
            // button "Отметить всё"
            $UI_CONFIG->actionButtons[] = array(
                "id" => "check_all",
                "caption" => NETCAT_MODULE_COMMENTS_ADMIN_LIST_CHECK_ALL,
                "action" => "nc_check_all()",
            );
        }
    }

    /**
     * Print form of subscribe settings.
     *
     * @access public
     * @return void
     */
    public function subscribe_settings() {

        $nc_core = nc_Core::get_object();
        // get settings
        $settings = $nc_core->get_settings('', 'comments');

        echo "<form method='post' enctype='multipart/form-data' action='admin.php' id='adminForm' class='nc-form' style='padding:0; margin:0;'>\n" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SUBSCRIBE_TAB . "\n" .
            "</legend>\n";
        // subscribe allow
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ALLOW, 'Subscribe_Allow', $settings['Subscribe_Allow']);
        // subscribe link
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_BLOCK, 'Subscribe_Block', $settings['Subscribe_Block'], 1, 0, 'width:100%; height: 50px; line-height:1.1em');
        // mail subject
        echo nc_admin_input(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_SUBJECT, 'Mail_Subject', $settings['Mail_Subject'], 0, 'width:100%;');
        // mail template
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_TEMPLATE, 'Mail_Template', $settings['Mail_Template'], 1, 0, 'width:100%; height: 200px; line-height:1.1em');
        echo nc_mail_attachment_form('comments_subscribe');
        echo "</fieldset>\n";

        // admin notification
        /*
          echo "<fieldset style='margin-top:10px;'>\n".
          "<legend>\n".
          "".NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN."\n".
          "</legend>\n";
          // subscribe allow
          echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN_ALLOW, 'Subscribe_Admin', $settings['Subscribe_Admin']);
          global $ADMIN_PATH, $SPAM_FROM;
          echo NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN_EMAIL.": <b>".$SPAM_FROM."</b> <a href=".$ADMIN_PATH."settings.php?phase=1>".CONTROL_USER_MAIL_CHANGE."</a>";
          // mail subject
          echo nc_admin_input(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_SUBJECT, 'Mail_Subject_Admin', $settings['Mail_Subject_Admin'], 0, 'width:100%;');
          // mail template
          echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_TEMPLATE, 'Mail_Template_Admin', $settings['Mail_Template_Admin'], 1, 0, 'width:100%; height:7em; line-height:1.1em');
          echo "</fieldset>\n";
         */

        echo "<input type='hidden' value='31' name='phase' />";
        echo "</form>\n";

        global $UI_CONFIG;
        $UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODERATION_BUTTON_CHANGE,
            "action" => "mainView.submitIframeForm('adminForm')"
        );

        return;
    }

    /**
     * Save subscribe settings.
     *
     * @access public
     * @return void
     */
    public function subscribe_settings_save() {

        $nc_core = nc_Core::get_object();
        $params = array('Subscribe_Allow', 'Subscribe_Block', 'Mail_Subject', 'Mail_Template');
        //$params = array('Subscribe_Allow', 'Subscribe_Block', 'Mail_Subject', 'Mail_Template', 'Subscribe_Admin', 'Mail_Subject_Admin', 'Mail_Template_Admin');

        foreach ($params as $v) {
            $nc_core->set_settings($v, $nc_core->input->fetch_get_post($v), 'comments');
        }

        nc_mail_attachment_form_save('comments_subscribe');

        return;
    }

    /**
     * Print settings form.
     *
     * @access public
     * @return void
     */
    public function settings() {
        $nc_core = nc_Core::get_object();
        // Настройки из базы
        $settings = $nc_core->get_settings('', 'comments');

        // Основные настройки

        echo "<form method='post' action='admin.php' id='adminForm' class='nc-form' style='padding:0; margin:0;'>\n" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_MAIN_SETTINGS . "\n" .
            "</legend>\n";

        $user_name = $this->db->get_col("SELECT `Field_Name` FROM `Field` WHERE `System_Table_ID` = 3 AND TypeOfData_ID = 1");
        $user_avatar = $this->db->get_col("SELECT `Field_Name` FROM `Field` WHERE `System_Table_ID` = 3 AND TypeOfData_ID = 6");

        echo "<div style='margin:10px 0; _padding:0;'>\n" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USER_NAME . ":<br/>" .
            "<select name='user_name_selector' style='width:30%'>\n";
        if (!empty($user_name)) {
            foreach ($user_name AS $value) {
                echo "<option value='" . $value . "' " . ($value == $settings['UserName'] ? "selected" : "") . ">" . $value . "</option>\n";
            }
        }
        echo "</select>\n</div>";

        echo "<div style='margin:10px 0; _padding:0;'>\n" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USER_AVATAR . ":<br/>" .
            "<select name='user_avatar_selector' style='width:30%'>\n
              <option value='0' " . (!$settings['UserAvatar'] ? "selected" : "") . ">" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USER_AVATAR_NO . "</option>\n";
        if (!empty($user_avatar)) {
            foreach ($user_avatar AS $value) {
                echo "<option value='" . $value . "' " . ($value == $settings['UserAvatar'] ? "selected" : "") . ">" . $value . "</option>\n";
            }
        }
        echo "</select>\n </div>";

        echo "<script language=Javascript>
		function show(a,b,c) {
		if (a.checked) {b.disabled=false; c.style.color='#505050';}
		else {b.disabled=true; c.style.color='#cccccc';}
		}

		function listVSbutt(a, b, bdiv, c, cdiv) {
			if(a.value <= 0) {
				b.disabled=true;
				bdiv.style.color='#cccccc';
				c.disabled=false;
				cdiv.style.color='#505050';
			}
			else {
				b.disabled = false;
				bdiv.style.color='#505050';
				c.disabled=true;
				cdiv.style.color='#cccccc';
			}
		}
		</script> ";

        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_NAME, 'GuestName', $settings, "onclick='show(this, GuestNameForced, GuestNameForcedDiv);'");
        echo "<div style='padding-left:20px;" . (!$settings['GuestName'] ? "color:#cccccc" : "") . "' id='GuestNameForcedDiv'>" . nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_NEED, 'GuestNameForced', $settings['GuestNameForced'], (!$settings['GuestName'] ? "disabled" : "")) . "</div>";
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_EMAIL, 'GuestEmail', $settings['GuestEmail'], "onclick='show(this, GuestEmailForced, GuestEmailForcedDiv);'");
        echo "<div style='padding-left:20px; " . (!$settings['GuestEmail'] ? "color:#cccccc" : "") . "' id='GuestEmailForcedDiv'>" . nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_NEED, 'GuestEmailForced', $settings['GuestEmailForced'], (!$settings['GuestEmail'] ? "disabled" : "")) . "</div>";
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_ADD_BLOCK, 'ShowAddBlock', $settings['ShowAddBlock']);

        echo "<div style='margin: 5px 0;'>" .
            NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_QTY . "<input type='text' name='Qty' id='Qty' size='3' style='margin: 0 0 0 5px;' value='" . htmlentities($settings['Qty'], ENT_QUOTES, MAIN_ENCODING) . "' onchange='listVSbutt(this, ShowAll, ShowAllDiv, ShowButton, ShowButtonDiv );'/>" .
            "</div>";
        echo "<div id='ShowAllDiv' " . ($settings['Qty'] ? "" : "style='color:#cccccc;'") . ">" . nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_ALL, 'ShowAll', $settings['ShowAll'], (!$settings['Qty'] ? "disabled" : "")) . "</div>";

        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_ORDER_DESC, 'Order', $settings['Order']);
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_BBCODE, 'BBcode', $settings['BBcode']);
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USE_CAPTCHA, 'UseCaptcha', $settings['UseCaptcha']);

        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_RATING, 'Rating', $settings['Rating']);

        echo "</fieldset><br/>\n";

        // Премодерация
        echo
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION . "\n" .
            "</legend>\n";

        echo "<div style='margin:10px 0; _padding:0;'>\n" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_ALLOW_PREMODERATION . ":<br/>";
        echo "<input type='radio' name='premod' id='premod_no' value='0' " . (!$settings['Premoderation'] ? "checked='checked'" : "") . "><label for='premod_no'>" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_NO . "</label><br/>";
        echo "<input type='radio' name='premod' id='premod_unregister' value='1' " . ($settings['Premoderation'] == 1 ? "checked='checked'" : "") . "><label for='premod_unregister'>" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_GUEST . "</label><br/>";
        echo "<input type='radio' name='premod' id='premod_always' value='2' " . ($settings['Premoderation'] == 2 ? "checked='checked'" : "") . "><label for='premod_always'>" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_ALWAYS . "</label>";

        echo "</div></fieldset><br/>\n";

        // Новые комментарии
        echo
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_NEW_COMMENTS . "\n" .
            "</legend>\n";
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_HIGHLIGHT_NEW_COMMENTS, 'Highlight', $settings['Highlight']);
        echo "<div id='ShowButtonDiv' " . (!$settings['Qty'] ? "" : "style='color:#cccccc;'") . ">" . nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_BUTTON_NEW_COMMENTS, 'ShowButton', $settings['ShowButton'], ($settings['Qty'] ? "disabled" : "")) . "</div>";
        echo "</fieldset><br/>\n";

        echo "<input type='hidden' value='91' name='phase' />" . "</form>\n";

        global $UI_CONFIG;
        $UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODERATION_BUTTON_CHANGE,
            "action" => "mainView.submitIframeForm('adminForm')"
        );

        return;
    }

    /**
     * Save comments settings.
     *
     * @access public
     * @return void
     */
    public function settings_save() {

        $nc_core = nc_Core::get_object();
        $params = array('GuestName', 'GuestNameForced', 'GuestEmail', 'GuestEmailForced',
            'ShowAddBlock', 'Highlight', 'ShowButton', 'Order', 'BBcode', 'UseCaptcha', 'Qty', 'ShowAll', 'Rating');

        foreach ($params as $v) {
            $nc_core->set_settings($v, intval($nc_core->input->fetch_get_post($v)), 'comments');
        }

        $p = $n = $a = array();
        foreach ($nc_core->input->fetch_get_post() as $k => $v) {
            if (nc_preg_match('/\bpremod\b/', $k))
                $p = $v;
            if (nc_preg_match('/\buser_name_selector\b/', $k))
                $n = $v;
            if (nc_preg_match('/\buser_avatar_selector\b/', $k))
                $a = $v;
        }
        $nc_core->set_settings('Premoderation', $p, 'comments');
        $nc_core->set_settings('UserName', $n, 'comments');
        $nc_core->set_settings('UserAvatar', $a, 'comments');

        return;
    }

    /**
     * Print template from.
     *
     * @access public
     * @return void
     */
    public function template() {
        $nc_core = nc_Core::get_object();
        $Template = $this->POST['Template'];

        $TemplatesData = $this->db->get_results("SELECT * FROM `Comments_Template`", ARRAY_A);

        $default = array();
        $settings = array();

        if (!empty($TemplatesData)) {
            foreach ($TemplatesData AS $value) {
                if ($value['Default'])
                    $default = $value;
                if ($value['ID'] != $Template)
                    continue;
                $settings = $value;
            }
            if ($Template != "new") {
                if (empty($settings) && !empty($default))
                    $settings = $default;
                if (empty($settings))
                    $settings = $TemplatesData[0];
            }
        }

        if (!$Template)
            $Template = $settings['ID'];

        $user_fields = $this->db->get_col("SELECT `Field_Name` FROM `Field` WHERE `System_Table_ID` = 3 AND TypeOfData_ID = 1");

        echo "<form method='post' action='admin.php' style='padding:0; margin:0;'>\n" .
            "<input type='hidden' name='phase' value='2'>" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_TEMPLATE . "\n" .
            "</legend>\n" .
            "<div style='margin:10px 0; _padding:0;'>\n" .
            "<select name='Template' onchange='this.form.submit();' style='width:50%'>\n";
        if (!empty($TemplatesData)) {
            foreach ($TemplatesData AS $value) {
                echo "<option value='" . $value['ID'] . "' " . ($value['ID'] == $Template && $Template != "new" ? "selected" : "") . ">" . $value['ID'] . ": " . $value['Name'] . "</option>\n";
            }
        }
        echo "<option value='new'" . (empty($TemplatesData) || $Template == "new" ? " selected" : "") . ">" . NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_TEMPLATE_NEW . "</option>\n";
        echo "</select>\n" .
            "</div>\n" .
            "</fieldset>\n" .
            "</form><br/>\n";

        echo "<form method='post' action='admin.php' id='SetCommentsSettings' style='padding:0; margin:0;'>\n" .
            "<input type='hidden' name='Template' value='" . ($Template && $Template != "new" ? $Template : 0) . "'>" .
            "<fieldset>\n" .
            "<legend>\n" .
            "" . NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS . "\n" .
            "</legend>\n";
        // use default
        echo nc_admin_checkbox(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_USE_DEFAULT, 'Default', $settings['Default']);

        // template name
        echo nc_admin_input(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_NAME, 'Name', $settings['Name'], 0, 'width:100%');

        $comments_editor = new nc_module_tpl_editor();

        if ('new' == $Template) {
            $settings = $comments_editor->load('comments', 0)->get_default_fields();
        } else {
            $comments_editor->load('comments', $Template)->fill();
            $settings = $comments_editor->get_all_fields();
            // print_r( array_keys($settings) );
        }
        // wall prefix
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PREFIX, 'Prefix', $settings['Prefix'], 1, 0, 'height:7em; line-height:1.1em');
        // comments block
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_COMMENT_BLOCK, 'Comment_Block', $settings['Comment_Block'], 1, 0, 'height:7em; line-height:1.1em');
        // reply block
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_REPLY_BLOCK, 'Reply_Block', $settings['Reply_Block'], 1, 0, 'height:7em; line-height:1.1em');
        // comment link
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_COMMENT, 'Comment_Link', $settings['Comment_Link'], 1, 0, 'height:7em; line-height:1.1em');
        // reply link
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_REPLY, 'Reply_Link', $settings['Reply_Link'], 1, 0, 'height:7em; line-height:1.1em');
        // edit link
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_EDIT, 'Edit_Link', $settings['Edit_Link'], 1, 0, 'height:7em; line-height:1.1em');
        // delete link
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_DROP, 'Delete_Link', $settings['Delete_Link'], 1, 0, 'height:7em; line-height:1.1em');
        // add comments
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_APPEND_BLOCK, 'Add_Block', $settings['Add_Block'], 1, 0, 'height:7em; line-height:1.1em');
        // edit comments
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_EDIT_BLOCK, 'Edit_Block', $settings['Edit_Block'], 1, 0, 'height:7em; line-height:1.1em');
        // delete comments
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_DROP_BLOCK, 'Delete_Block', $settings['Delete_Block'], 1, 0, 'height:7em; line-height:1.1em');
        // wall suffix
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_SUFFIX, 'Suffix', $settings['Suffix'], 1, 0, 'height:7em; line-height:1.1em');
        // pagination
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PAGINATION, 'Pagination', $settings['Pagination'], 1, 0, 'height:7em; line-height:1.1em');
        //show all button
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_SHOW_ALL, 'Show_All', $settings['Show_All'], 1, 0, 'height:7em; line-height:1.1em');
        // warntext
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT, 'Warn_Text', $settings['Warn_Text'], 1, 0, 'height:7em; line-height:1.1em');
        // premoderation
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PREMODERATION, 'Premod_Text', $settings['Premod_Text'], 1, 0, 'height:7em; line-height:1.1em');
        // new comments button
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_NEW_COMMENT_BUTTON, 'New_Comment_Button', $settings['New_Comment_Button'], 1, 0, 'height:7em; line-height:1.1em');
        // Rating block
        echo nc_admin_textarea(NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_RATING_BLOCK, 'Rating_Block', $settings['Rating_Block'], 1, 0, 'height:7em; line-height:1.1em');

        echo "</fieldset><br/>\n";

        echo nc_admin_js_resize();

        global $UI_CONFIG;
        // admin buttons
        $UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_COMMENTS_ADMIN_MAINSETTINGS_SAVE_BUTTON,
            "action" => "mainView.submitIframeForm('SetCommentsSettings')"
        );

        echo "<input type='hidden' name='phase' value='21'>\n" .
            //$nc_core->token->get_input().
            "</form><br/>\n";

        return;
    }

    /**
     * Save template.
     *
     * @access public
     * @return changed rows
     */
    public function template_save() {

        $Template = $this->POST['Template'];

        // simple check
        if (!(is_numeric($Template) || $Template == "new"))
            return false;

        $_fields = $this->db->get_col("SHOW COLUMNS FROM `Comments_Template`");

        if (!empty($_fields) && !empty($this->POST)) {
            $default = false;
            $query_arr = array();
            foreach ($this->POST as $key => $value) {
                // only need fields
                if (!in_array($key, $_fields) || $key == 'ID')
                    continue;
                // append to query array
                $query_arr[] = "`" . $key . "` = '" . $this->db->escape($value) . "'";
                // check default
                if ($key == "Default")
                    $default = true;
            }
            if (empty($query_arr))
                return false;
            // reset not posted fields
            if (sizeof($_fields) != sizeof($query_arr)) {
                foreach ($_fields as $field_name) {
                    if (!in_array($field_name, array_keys($this->POST)) && $field_name != 'ID') {
                        $query_arr[] = "`" . $field_name . "` = DEFAULT(`" . $field_name . "`)";
                    }
                }
            }
            // existence
            if ($Template != "new")
                $SettingsExist = $this->db->get_var("SELECT `ID` FROM `Comments_Template` WHERE `ID` = '" . intval($Template) . "'");
            // clear default
            if ($default)
                $this->db->query("UPDATE `Comments_Template` SET `Default` = DEFAULT(`Default`)");
            // save settings
            if ($Template && $Template != "new" && $SettingsExist) {
                $comments_editor = new nc_module_tpl_editor();
                $comments_editor->load('comments', $Template)->save($_POST);

                $this->db->query("UPDATE `Comments_Template` SET " . join(", ", $query_arr) . " WHERE `ID` = '" . intval($Template) . "'");
            } else {
                $this->db->query("INSERT INTO `Comments_Template` SET " . join(", ", $query_arr));
                $id = $this->db->get_results("SELECT LAST_INSERT_ID() AS `ID`");
                $Template = $id[0]->{'ID'};

                $comments_editor = new nc_module_tpl_editor();
                $settings = $comments_editor->load('comments', 0)->get_default_fields();

                foreach ($settings as $setting => $value) {
                    $settings[$setting] = $this->POST[$setting];
                }

                $comments_editor->create($Template, $settings);
            }
        }

        // return changes status
        return $this->db->rows_affected;
    }

}

?>