<?php

/* $Id: nc_comments.class.php 8300 2012-10-29 14:42:06Z vadim $ */

class nc_comments {

    public $template, $last_updated, $new_comments_id;
    protected $core;
    private $_message_cc, $_current_catalogue, $_current_sub, $_parent_sub_tree, $_current_cc;
    private $_commentsData, $_mainArr, $_nestArr, $_showCommentsForm, $_showCommentsQuantity;
    private $_comment_rules, $_accessibilityAdd, $_accessibilityEdit, $_accessibilityDelete, $_accessibilitySubscribe;
    private $db, $_perm, $_accessibility;
    // поле из таблицы User для %USER_NAME%
            protected $_user_field, $avatar_path_array;
    // номер используемого шаблона
    protected $_template_id;
    // настройки
    protected $settings;

    /**
     * Constructor function
     * @param `Sub_Class_ID` value
     */
    public function __construct($message_cc) {
        global $MODULE_FOLDER, $DOCUMENT_ROOT;
        global $db, $nc_core, $perm, $AUTH_USER_ID, $ADMIN_PATH;
        global $current_catalogue, $current_sub, $parent_sub_tree, $current_cc;

        // system superior object
        if (get_class($nc_core) == "nc_Core")
            $this->core = $nc_core;

        // start values
        $this->db = $db;
        $this->_perm = $perm;
        $this->AUTH_USER_ID = $AUTH_USER_ID;
        $this->_message_cc = (int) $message_cc;
        $this->MODULE_PATH = str_replace($DOCUMENT_ROOT, "", $MODULE_FOLDER) . "comments/";
        $this->ADMIN_PATH = $ADMIN_PATH;
        $this->last_updated = time();
        // this may useful when get inherited cc settings
        $this->_current_catalogue = $current_catalogue;
        $this->_current_sub = $current_sub;
        $this->_parent_sub_tree = $parent_sub_tree;
        $this->_current_cc = $current_cc;
        $this->settings = $this->core->get_settings('', 'comments');

        // accessibility initialize, init only once because on action new object creating
        $this->_comment_rules = $this->_getInheritedRules();
        // user name field
        $this->_user_field     = $this->settings['UserName'] ? $this->settings['UserName'] : 'Login';
        $this->_enable_rating  = (bool)(isset($this->settings['Rating']) ? $this->settings['Rating'] : false);
        $this->new_comments_id = $new_comments_id;
    }

    /**
     * Select last visit of user.
     *
     * @access public
     * @param int $message_id
     * @return void
     */
    public function last_visit($message_id) {
        if ($this->AUTH_USER_ID) {
            $last_visit = $this->db->get_var("SELECT LastUpdated FROM `Comments_LastVisit` WHERE `User_ID` = '" . $this->AUTH_USER_ID . "'
                                                                                       AND `Class_ID` = '" . $this->_current_cc['Class_ID'] . "'
                                                                                       AND `Message_ID` = '" . $this->db->escape($message_id) . "'");
            if (!$last_visit) {
                $this->db->query("INSERT INTO `Comments_LastVisit` (`User_ID`, `Class_ID`, `Message_ID`, `LastUpdated`)
                            VALUES ('" . $this->db->escape($this->AUTH_USER_ID) . "', '" . $this->_current_cc['Class_ID'] . "', '" . $this->db->escape($message_id) . "', NOW())");
            } else {
                $this->db->query("UPDATE `Comments_LastVisit` SET `LastUpdated` = NOW()
                            WHERE `User_ID` = '" . $this->AUTH_USER_ID . "'
                            AND `Class_ID` = '" . $this->_current_cc['Class_ID'] . "'
                            AND `Message_ID` = '" . $this->db->escape($message_id) . "'");
            }
        }
    }

    /**
     * Get comments wall HTML text
     * @param `Message_ID` value
     * @param "from" value, start from N element
     * @param "quantity" value, how many comments display
     * @param "reset" value, need to reinitialize arrays
     * @return comments wall HTML text
     */
    public function wall($message_id, $template = 0, $from = 0, $quantity = 0, $reset = 0, $ignore = 0, $show_all = 0) {
        global $AUTH_USER_ID, $MODULE_FOLDER;

        if ($show_all)
            $this->settings['Qty'] = 0;
        // set parameters
        if ($this->settings['Qty']) {
            $this->_showCommentsFrom = (int) $from;
            $this->_showCommentsQuantity = (int) $quantity > 0 ? (int) $quantity : $this->settings['Qty'];
        }

        // template
        $templateData = $this->_getTemplate($template);
        $this->_template_id = $templateData['ID'];

        // set internal comments arrays, lezy instantiation
        if (!isset($this->_commentsData) || $reset) {
            $this->loadArrays($message_id);
        }

        // start values
        $result = "";
        $message_id = (int) $message_id;
        $template = (int) $template;

        // refresh accessibility
        $this->_getAccessibility();


        // subscribe ability
        $this->_accessibilitySubscribe = $this->_accessibilityAdd && $this->settings['Subscribe_Allow'] && $AUTH_USER_ID;
        if ($this->_accessibilitySubscribe) {
            require_once($MODULE_FOLDER . "comments/nc_commsubs.class.php");
            $nc_commsubs = new nc_commsubs();
            $is_subscribe = $nc_commsubs->is_subscribe($AUTH_USER_ID, $this->_message_cc, $message_id);
        }
        if (!empty($this->_commentsData)) {
            $this->new_comments_id = $this->getNewCommentsIds($message_id);
            // get comments wall
            $commentsBlock = $this->_sorting($message_id);
            // past comments
            foreach ($this->_commentsData AS $comment) {
                // заменять bb-коды надо именно здесь из-за возможных незакрытых тегов
                if ($this->isBBcodes())
                    $comment['Comment'] = nc_bbcode($comment['Comment']);
                // past comment text
                $commentsBlock = str_replace("%COMMENT_" . $this->_message_cc . "_" . $message_id . "_" . $comment['id'] . "%", nl2br($comment['Comment']), $commentsBlock);
                // all comments ids to array for javascript
                $all_comments_id[] = $comment['id'];
                if ($comment['Parent_Comment_ID'] == 0)
                    $parent_comments[] = $comment['id'];
            }
            //$new_comments_id = $this->getNewCommentsIds($message_id);
        }

        // phpjavascript class construct
        if (!$ignore) {

            // demountable values in comment HTML forms
            $replaceKeys = array("%ISBBCODES");
            $replaceValues = array($this->isBBcodes());
            // this eval need to prefer localisation constants
            eval("\$addBlock = \"" . str_replace($replaceKeys, $replaceValues, $templateData['Add_Block']) . "\";");
            eval("\$editBlock = \"" . str_replace($replaceKeys, $replaceValues, $templateData['Edit_Block']) . "\";");
            eval("\$deleteBlock = \"" . str_replace($replaceKeys, $replaceValues, $templateData['Delete_Block']) . "\";");
            eval("\$templateData['Premod_Text'] = \"" . $templateData['Premod_Text'] . "\";");
            // compile initialize json "array"
            $json_parameters = "{
        'message_cc':'" . $this->_message_cc . "',
        'message_id':'" . $message_id . "',
        'template_id':'" . $template . "',
        'add_block':escape(\"" . $this->commentValidateShow($addBlock, $template, 0, 1) . "\"),
        'edit_block':escape(\"" . $this->commentValidateShow($editBlock, $template, 0, 1) . "\"),
        'delete_block':escape(\"" . $this->commentValidateShow($deleteBlock, $template, 0, 1) . "\"),
        'last_updated':'" . $this->last_updated . "',
        'MODULE_PATH':'" . $this->MODULE_PATH . "',
        'LOADING':'" . NETCAT_MODULE_COMMENTS_ADD_FORM_LOADING_TEXT . "',
        'SUBSCRIBE_TO_ALL':'" . NETCAT_MODULE_COMMENTS_SUBSCRIBE_TO_ALL . "',
        'UNSUBSCRIBE_FROM_ALL':'" . NETCAT_MODULE_COMMENTS_UNSUBSCRIBE_FROM_ALL . "',
        'edit_access':'" . $this->_comment_rules['Edit_Rule'] . "',
        'delete_access':'" . $this->_comment_rules['Delete_Rule'] . "',
        'all_comments_id':[" . join(", ", (array) $all_comments_id) . "],
        'show_addform':'" . ($this->settings['ShowAddBlock'] ? 1 : 0) . "',
        'show_name':'" . (($this->settings['GuestName'] && !$AUTH_USER_ID) ? 1 : 0) . "',
        'show_email':'" . (($this->settings['GuestEmail'] && !$AUTH_USER_ID) ? 1 : 0) . "',
        'premoderation':'" . $this->checkRights() . "',
        'sorting':'" . $this->settings['Order'] . "',
        'premodtext':escape(\"" . $this->commentValidateShow($templateData['Premod_Text'], $template, 0, 1) . "\"),
        'new_comments_id':" . (!empty($this->new_comments_id) ? "[" . join(", ", $this->new_comments_id) . "]" : 0) . "
      }";

            // load need scripts
            $result.= "<script src='" . $this->MODULE_PATH . "comments.js' type='text/javascript' language='JavaScript'></script>\r\n";
            if ($this->isBBcodes())
                $result.= "<script language='JavaScript' type='text/javascript' src='" . $this->ADMIN_PATH . "js/bbcode.js'></script>\r\n";
            $result.= "<script type='text/javascript'>\r\n";
            $result.= "nc_commentsObj" . $this->_message_cc . "_" . $message_id . " = new nc_Comments(" . $json_parameters . ");\r\n";
            $result.= "</script>\r\n";
        }
        // prefix
        $demountable = array("%ID");
        $replacing = array("nc_commentID" . $this->_message_cc . "_" . $message_id . "_0");
        if (!$ignore)
            $result.= str_replace($demountable, $replacing, $templateData['Prefix']);

        // subscribe
        if ($this->_accessibilitySubscribe) {
            $demountable = array("%ACTION", "%ID", "%TEXT");
            $replacing = array("nc_commentsObj" . $this->_message_cc . "_" . $message_id . "." . ( $is_subscribe ? "Unsubscribe(0);" : "Subscribe(0);") . " return false;",
                "nc_comments_subscribe" . $this->_message_cc . "_" . $message_id . "_0",
                $is_subscribe ? NETCAT_MODULE_COMMENTS_UNSUBSCRIBE_FROM_ALL : NETCAT_MODULE_COMMENTS_SUBSCRIBE_TO_ALL);
            $result.= str_replace($demountable, $replacing, $this->settings['Subscribe_Block']);
        }
        $result.= $commentsBlock;
        // comments link
        if ($this->_accessibilityAdd) {
            if (!$this->settings['ShowAddBlock']) {
                // demountable values in comment HTML text
                $demountable = array("%ID", "%ACTION", "%TEXT");
                // replacing values
                $replacing = array(
                    "nc_commentsReply" . $this->_message_cc . "_" . $message_id . "_0",
                    "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".Form(0); return false;",
                    NETCAT_MODULE_COMMENTS_LINK_COMMENT
                );
                $result.= str_replace($demountable, $replacing, $templateData['Comment_Link']);
            } else {
                $result .= "<script type='text/javascript'>
      	        jQuery(document).ready(function() {
      	nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".Form(0);
                        });
      	</script>";
            }
        }
        // suffix
        if (!$ignore)
            $result.= $templateData['Suffix'];

        $curPos = $from;
        $maxRows = $this->settings['Qty'];
        $totRows = count($parent_comments);

        if ($maxRows && $totRows > $maxRows) {

            global $browse_comments, $browse_msg, $env;

            $env['maxRows'] = $maxRows;
            $env['totRows'] = $totRows;
            $env['curPos'] = $curPos;
            $env['LocalQuery'] = $this->core->url->get_parsed_url("path");

            if ($browse_comments) {
                $demountable = array("%NAV_ACTION");
                $replacing = array("nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".navComments(%PAGE*" . $maxRows . "); return false;");
                $browse_comments['unactive'] = str_replace($demountable, $replacing, $browse_comments['unactive']);
            }

            $browse_msg['prefix'] = "";
            $browse_msg['suffix'] = "";
            $browse_msg['active'] = "<b>%PAGE</b>";
            $browse_msg['unactive'] = "<a href='#' onclick='nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".navComments(%PAGE*" . $maxRows . "); return false;'>%PAGE</a>";
            $browse_msg['divider'] = " | ";

            $last_pos = ( ceil($totRows / $maxRows) * $maxRows );
            $last_page = ( $curPos != $last_pos && $last_pos != $curPos + $maxRows );


            // pagination data
            $pagination = $this->listing($message_id);

            if ($pagination && !$ignore) {
                // pagination prefix
                $result.= "<div id='nc_comments_nav' class='pagination'>";

                $result.= $pagination;

                // pagination suffix
                $result.= "</div>";
            }
            // show all comments
            if ($this->settings['ShowAll'] && !$ignore) {
                $demountable = array("%SHOW_ALL");
                $replacing = array("nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".showAll(" . $template . "); return false;");
                $result.= "<div id='show_all'>";
                $result.= str_replace($demountable, $replacing, $templateData['Show_All']);
                ;
                $result.="</div>";
            }
        }

        // new comment button
        if ($this->settings['ShowButton'] && count($this->new_comments_id) && !$this->settings['Qty']) {
            $demountable = array("%NEW_COMMENT_BUTTON_ACTION");
            $replacing = array("nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".showNewComment(); return false;");
            $result.= str_replace($demountable, $replacing, $templateData['New_Comment_Button']);
        }

        //update LastVisit in Comments_LastVisit
        if (!(!$this->settings['ShowButton'] && !$this->settings['Highlight']))
            $this->last_visit($message_id);

        // return comments wall HTML text
        return $result;
    }

    /**
     * listing function.
     *
     * @access public
     * @param int $message_id
     * @return html text
     */
    public function listing($message_id) {

        if (!$message_id)
            return;

        global $curPos, $last_pos, $last_page, $browse_comments, $browse_msg, $env;
        $curPos = $env['curPos'];
        $maxRows = $env['maxRows'];
        $totRows = $env['totRows'];
        $last_pos = ( ceil($totRows / $maxRows) * $maxRows );
        $last_page = ( $curPos != $last_pos && $last_pos != $curPos + $maxRows );

        // template
        $templateData = $this->_getTemplate($template);

        if (!$templateData['Pagination']) {
            // first link
            $result = $curPos ? "<a href='#' onclick='%FIRST_ONCLICK'>&raquo;&raquo;</a>&nbsp;&nbsp;" : "";

            // pagination
            $result.= browse_messages($env, 10, $browse_comments);

            // last link
            $result.= $last_page ? "&nbsp;&nbsp;<a href='#' onclick='%LAST_ONCLICK'>&raquo;&raquo;</a>" : "";
        } else {
            eval("\$templateData['Pagination'] = \"" . $templateData['Pagination'] . "\";");
            $result = $templateData['Pagination'];
        }

        $demountable = array("%FIRST_ONCLICK", "%LAST_ONCLICK");
        $replacing = array("nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".navComments(" . $maxRows . "); return false;", "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".navComments(" . $last_pos . "); return false;");
        $result = str_replace($demountable, $replacing, $result);

        return $result;
    }

    /**
     * Get comments and compile internal private arrays
     * _mainArr, _nestArr and last_comment_id value
     * @param `Message_ID` value
     * @return true if comments exist or false
     */
    public function loadArrays($message_id) {
        // start values
        $this->_mainArr = array();
        $this->_nestArr = array();
        $templateData = $this->_getTemplate($this->_template_id);
        $from = $this->_showCommentsFrom && $this->settings['Qty'] ? $this->_showCommentsFrom : 0;
        $quantity = $this->_showCommentsQuantity !== false && $this->settings['Qty'] ? $this->_showCommentsQuantity : 0;
        $message_id = (int) $message_id;
        // get comments data
        if ($this->settings['Order'])
            $order = 'DESC';
        else
            $order = 'ASC';
        $this->_commentsData = $this->db->get_results(
                "SELECT c.*, IF( UNIX_TIMESTAMP(c.`Updated`) > UNIX_TIMESTAMP(c.`Date`),
             UNIX_TIMESTAMP(c.`Updated`), UNIX_TIMESTAMP(c.`Date`) ) AS LastUpdated,
             UNIX_TIMESTAMP(c.`Date`) AS Date, UNIX_TIMESTAMP(c.`Updated`) AS Updated,
             u.`" . $this->_user_field . "` AS `User_Name`" . ($this->settings['UserAvatar'] ? ", u.`" . $this->settings['UserAvatar'] . "` AS `User_Avatar`" : "") . "
      FROM `Comments_Text` AS `c`
      LEFT JOIN `User` as `u` ON u.`User_ID` = c.`User_ID`
      WHERE c.`Sub_Class_ID` = '" . $this->_message_cc . "' AND c.`Message_ID` = '" . $message_id . "' AND c.`Checked` = 1
      ORDER BY c.`id` " . $order . " ", ARRAY_A);
        $total_rows = $this->db->num_rows;

        // comments data sorting
        if (!empty($this->_commentsData)) {

            //get avatars
            if ($this->settings['UserAvatar']) {

                global $HTTP_FILES_PATH;
                $sys_fields = $this->core->get_system_table_fields('User');
                foreach ($sys_fields as $field) {
                    if ($this->settings['UserAvatar'] == $field['name']) {
                        $field_id = $field['id'];
                        break;
                    }
                }
                // for simple and standard file systems
                $user_avatar = array();
                foreach ($this->_commentsData as $com) {
                    if ($com['User_ID']) {
                        $user_avatar[$com['User_ID']] = $com['User_Avatar'];
                    }
                }
                if (!empty($user_avatar)) {
                    foreach ($user_avatar as $ku => $vu) {
                        $data_array = explode(":", $vu);
                        if (count($data_array) == 4) {
                            $this->avatar_path_array[$ku] = $HTTP_FILES_PATH . $data_array[3];
                        }
                        if (count($data_array) == 3) {
                            $ext = strrpos($data_array[0], ".");
                            $ext = substr($data_array[0], $ext + 1);
                            $this->avatar_path_array[$ku] = $HTTP_FILES_PATH . $field_id . "_" . $ku . "." . $ext;
                        }
                    }

                    $protected_avs = $this->db->get_results("SELECT `Message_ID`, `Virt_Name`, `File_Path` FROM `Filetable` WHERE `Field_ID` = " . $field_id . " AND `Message_ID` IN (" . join(',', array_keys($user_avatar)) . ")", ARRAY_A);

                    if (!empty($protected_avs)) {
                        foreach ($protected_avs as $avs) {
                            $this->avatar_path_array[$avs['Message_ID']] = $HTTP_FILES_PATH . ltrim($avs['File_Path'], "/") . $avs['Virt_Name'];
                        }
                    }
                }
            }

            if (false && $this->settings['Order']) {
                $temp = array();
                $p_exists = array();
                while (!empty($this->_commentsData)) {
                    foreach ($this->_commentsData as $k => $v) {
                        if (in_array($v['Parent_Comment_ID'], $p_exists) || !$v['Parent_Comment_ID']) {
                            $temp[$k] = $v;
                            unset($this->_commentsData[$k]);
                        }
                        $p_exists[] = $v['id'];
                    }
                }

                $this->_commentsData = $temp;
                unset($temp);
            }

            $from_count = 0;

            if ($from < 1)
                $from = 0;

            if ($quantity <= 0)
                $quantity = $total_rows;

            foreach ($this->_commentsData AS $value) {
                if ($value['Parent_Comment_ID'] == 0)
                    $from_count++;

                if (($from <= $from_count) && ($quantity >= ($from_count - $from)) || isset($this->_nestArr[$value['Parent_Comment_ID']])) {
                    $this->_nestArr[$value['id']] = $value['Parent_Comment_ID'];
                    $this->_mainArr[$value['Parent_Comment_ID']][] = $value;
                }

                $this->last_updated = max($this->last_updated, $value['Updated']);
            }
            $result = true;
        } else {
            $result = false;
        }

        // return true or false
        return $result;
    }

    /**
     * getCommentFromArray function.
     *
     * @access public
     * @param int $id (default: 0)
     * @return comment data
     */
    public function getCommentFromArray($id = 0) {

        if (empty($this->_commentsData))
            return false;

        if (!$id)
            return $this->_commentsData;

        foreach ($this->_commentsData AS $value) {
            if ($value['id'] == $id) {
                $result = $value;
                break;
            }
        }
        return $result ? $result : false;
    }

    /**
     * isNew function.
     *
     * @access public
     * @param mixed $data
     * @return true or false
     */
    public function isNew($data) {

        if (!empty($this->new_comments_id)) {
            foreach ($this->new_comments_id as $kc => $vc) {
                if ($vc == $data['id'])
                    $is_new = ($this->AUTH_USER_ID && $this->AUTH_USER_ID != $data['User_ID'] && $this->settings['Highlight']) ? 1 : 0;
            }
        }
        return $is_new;
    }

    /**
     * Get comment with children public method
     * @param `Message_ID` value
     * @param comment data
     * @param with children or not parameter
     * @return comment HTML text
     */
    public function getComment($message_id, $data, $template_id = 0, $recurse = true, $eval = true) {
        // refresh accessibility
        $this->_getAccessibility($data['id']);
        // template
        $templateData = $this->_getTemplate($template_id);
        // for eval code set ezSQL variable
        if ($eval)
            $db = $this->db;
        // level for this comment
        $level = $this->_getNesting($data['id']);
        // demountable values in comment HTML text
        $replaceKeys = array("%ID", "%COMMENT", "%VALUE", "%IS_NEW", "%USER_AVATAR", "%USER_ID", "%USER_NAME", "%GUEST_NAME", "%GUEST_EMAIL", "%DATE", "%UPDATED", "%REPLY_LINK", "%EDIT_LINK", "%DELETE_LINK", "%REPLY_BLOCK", "%LEVEL", "%RATING_BLOCK");
        // comment reply link
        $reply_link = "";
        if ($this->_accessibilityAdd) {
            // demountable values in comment HTML text
            $demountable = array("%ID", "%ACTION", "%TEXT");

            // replacing values for reply link
            $replacing = array(
                "nc_commentsReply" . $this->_message_cc . "_" . $message_id . "_" . $data['id'],
                "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".Form(" . $data['id'] . "); return false;",
                NETCAT_MODULE_COMMENTS_LINK_REPLY
            );
            $reply_link = "<!-- nocache -->" . str_replace($demountable, $replacing, $templateData['Reply_Link']) . "<!-- /nocache -->";
        }
        // replacing values for edit link
        $edit_link = "";
        if ($this->_accessibilityEdit) {
            $replacing = array(
                "nc_commentsEdit" . $this->_message_cc . "_" . $message_id . "_" . $data['id'],
                "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".Form(" . $data['id'] . ", 2); return false;",
                NETCAT_MODULE_COMMENTS_LINK_EDIT
            );
            $edit_link = "<!-- nocache -->" . str_replace($demountable, $replacing, $templateData['Edit_Link']) . "<!-- /nocache -->";
        }

        // replacing values fordelete link
        $delete_link = "";
        if ($this->_accessibilityDelete) {
            $replacing = array(
                "nc_commentsDelete" . $this->_message_cc . "_" . $message_id . "_" . $data['id'],
                "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".Form(" . $data['id'] . ", -1); return false;",
                NETCAT_MODULE_COMMENTS_LINK_DELETE
            );
            $delete_link = "<!-- nocache -->" . str_replace($demountable, $replacing, $templateData['Delete_Link']) . "<!-- /nocache -->";
        }

        // Rating block
        $rating_block_tpl = "";
        if ($this->_enable_rating) {
            $replacing = array(
                '%RATING_ID'      => "nc_commentsRating" . $this->_message_cc . "_" . $message_id . "_" . $data['id'],
                "%LIKE_ACTION"    => "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".like(" . $data['id'] . "); return false;",
                "%DISLIKE_ACTION" => "nc_commentsObj" . $this->_message_cc . "_" . $message_id . ".dislike(" . $data['id'] . "); return false;",
                "%LIKE"           => $data['Like'],
                "%DISLIKE"        => $data['Dislike'],
                "%RATING"         => $data['Like'] - $data['Dislike'],
            );
            $rating_block_tpl = str_replace(array_keys($replacing), $replacing, $templateData['Rating_Block']);
        }

        // template
        $commentTemplate = $templateData[($data['Parent_Comment_ID'] ? 'Reply_Block' : 'Comment_Block')];

        // avatar path
        $file_path = $this->avatar_path_array[$data['User_ID']];


        //new comment
        $is_new = $this->isNew($data);

        // replacing values
        $replaceValues = array(
            "nc_commentID" . $this->_message_cc . "_" . $message_id . "_" . $data['id'],
            "<span id='nc_commentText" . $this->_message_cc . "_" . $message_id . "_" . $data['id'] . "'>%COMMENT_" . $this->_message_cc . "_" . $message_id . "_" . $data['id'] . "%</span>", // comment or reply text eval is dangerous!
            $data['Value'],
            $is_new ? $is_new : "",
            $file_path ? $file_path : "",
            $data['User_ID'] + 0,
                str_replace('$', '&#36;', $data['User_Name']),
                $data['Guest_Name'] ? str_replace('$', '&#36;', $data['Guest_Name']) : CONTROL_USER_RIGHTS_GUESTONE,
            $data['Guest_Email'],
            $data['Date'],
            intval($data['Updated']),
            $reply_link,
            $edit_link,
            $delete_link,
            $recurse ? $this->_sorting($message_id, $data['id']) : "",
            $level,
            $rating_block_tpl,
        );
        // comment HTML text
        $commentHTMLText = str_replace($replaceKeys, $replaceValues, $commentTemplate);
        // eval this block without comment text
        if ($eval)
            eval("\$commentHTMLText = \"" . $commentHTMLText . "\";");
        // return comment HTML text
        return $commentHTMLText;
    }

    /**
     * Recursive sorting private method
     * @param `Message_ID` value
     * @param `Parent_Comment_ID` from `Comments_Text`
     * @return comment HTML text
     */
    private function _sorting($message_id, $pid = 0) {
        // get data for this comment
        if (empty($this->_mainArr[$pid]))
            return;
        // walk on this subarray
        foreach ($this->_mainArr[$pid] AS $data) {
            // add comment to result string
            $result.= $this->getComment($message_id, $data, $this->_template_id);
        }
        // return result
        return $result;
    }

    /**
     * Get nesting count private method
     * @param comment `id` in _nestArr from `Comments_Text`
     * @return counted value
     */
    private function _getNesting($id) {
        // count nesting
        for ($count = 0; $id = $this->_nestArr[$id]; $count++)
            ;
        // return result value
        return $count;
    }

    /**
     * Get number of children for this comment
     * @param comment `id` value
     * @return array of children or false
     */
    public function getChildren($id) {
        // validate
        $id = (int) $id;
        // get children from base (only with `Parent_Comment_ID`, level + 1)
        //$result = $this->db->get_col("SELECT `id` FROM `Comments_Text` WHERE `Parent_Comment_ID` = '".(int)$id."'");
        if (!empty($this->_commentsData))
            foreach ($this->_commentsData as $v) {
                if ($v['Parent_Comment_ID'] == $id)
                    $result[] = $v['id'];
            }
        // return result
        return !empty($result) ? $result : false;
    }

    /**
     * Append new comment into the base
     * @param `Message_ID` value
     * @param `Parent_Message_ID` value
     * @param `Comment` comment text value
     * @param `User_ID` authorized user id
     * @return inserted value
     */
    public function addComment($message, $parent_message, $comment, $user = 0, $nc_comments_guest_name = '', $nc_comments_guest_email = '') {
        // refresh accessibility
        $this->_getAccessibility();
        // clear HTML tags
        //$comment = strip_tags($comment);
        $comment = htmlspecialchars($comment);
        $nc_comments_guest_name = nc_quote_convert(strip_tags($nc_comments_guest_name));
        $nc_comments_guest_email = nc_quote_convert(strip_tags($nc_comments_guest_email));
        // check accessibility
        if (!$this->_accessibilityAdd) {
            throw new Exception(NETCAT_MODULE_COMMENTS_NO_ACCESS);
        }
        // check other parameters
        if (
                !is_numeric($message) ||
                !is_numeric($parent_message) ||
                !$comment ||
                ( $user && !is_numeric($user) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }

        $checked = $this->checkRights();

        if (!$this->core->NC_UNICODE) {
            $nc_comments_guest_name = $this->core->utf8->utf2win($nc_comments_guest_name);
            $nc_comments_guest_email = $this->core->utf8->utf2win($nc_comments_guest_email);
        }
        if ($parent_message) {
            $has_parent = $this->db->get_var("SELECT `id` FROM `Comments_Text` WHERE `id` = " . $parent_message);
            if (!$has_parent)
                return 0;
        }

        $cc_data = $this->core->sub_class->get_by_id($this->_message_cc);

        $this->core->event->execute("addCommentPrep", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message, 0);

        // append comments into the base
        $this->db->query("INSERT INTO `Comments_Text`
      (`Parent_Comment_ID`, `Sub_Class_ID`, `Message_ID`, `Date`, `Comment`, `User_ID`, `IP`, `Guest_Name`, `Guest_Email`, `Checked`)
      VALUES
      ('" . $parent_message . "', '" . $this->_message_cc . "', '" . $message . "', NOW(), '" . $this->db->escape($comment) . "', '" . (int) $user . "', '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', '" . $this->db->escape($nc_comments_guest_name) . "', '" . $this->db->escape($nc_comments_guest_email) . "', '" . (int) $checked . "' )");
        // inserted comment id
        $result = $this->db->insert_id;
        // refresh
        $this->loadArrays($message);
        // refresh count for this comment
        if ($result)
            $this->refreshCount($message, $parent_message);

        $this->core->event->execute("addComment", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message, $result);

        // return  inserted comment id
        return $result;
    }

    /**
     * Update exist comment into the base
     * @param `id` comment id value
     * @param `Comment` comment text value
     * @param `User_ID` authorized user id
     * @return true or false
     */
    public function updateComment($id, $comment, $user = 0) {
        // refresh accessibility
        $this->_getAccessibility($id);
        // clear HTML tags
        //$comment = strip_tags($comment);
        $comment = htmlspecialchars($comment);
        // check accessibility
        if (!$this->_accessibilityEdit) {
            throw new Exception(NETCAT_MODULE_COMMENTS_NO_ACCESS);
        }
        // check other parameters
        if (
                !is_numeric($id) ||
                !$comment
        //|| ( $user && !is_numeric($user) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }

        $checked = $this->checkRights();

        $cc_data = $this->core->sub_class->get_by_id($this->_message_cc);

        $message_id = $this->db->get_var("SELECT `Message_ID` FROM `Comments_Text` WHERE `id` = '" . $id . "'");

        $this->core->event->execute("updateCommentPrep", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message_id, $id);

        // append comments into the base and return result
        $this->db->query("UPDATE `Comments_Text`
      SET `Comment` = '" . $this->db->escape($comment) . "',
        `Updated` = NOW(),
        `Checked` = '" . $checked . "'
      WHERE `id` = '" . (int) $id . "'"); //`User_ID` = '".$user."'

        $result = $this->db->affected_rows;

        $this->core->event->execute("updateComment", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message_id, $id);

        // refresh arrays
        foreach ($this->_commentsData AS $comment) {
            if ($comment['id'] == $id) {
                $this->loadArrays($comment['Message_ID']);
                break;
            }
        }

        return $result;
    }

    /**
     * Delete comment from the base
     * @param `id` comment id value
     * @return true or false
     */
    public function deleteComment($id) {
        // save deleting ids in this array
        static $accessibilityDeleteArray;
        // refresh accessibility
        if (empty($accessibilityDeleteArray)) {
            $this->_getAccessibility($id);
            $accessibilityDeleteArray = array();
        }
        // check accessibility
        if (!$this->_accessibilityDelete) {
            throw new Exception(NETCAT_MODULE_COMMENTS_NO_ACCESS);
        }
        // check other parameters
        if (
                !is_numeric($id)
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }

        // get data for this comment
        $comment_data = $this->db->get_row("SELECT `Message_ID`, `Parent_Comment_ID`
      FROM `Comments_Text` WHERE `id` = '" . (int) $id . "'", ARRAY_A);

        $this->db->query("UPDATE `Comments_Text` SET `Updated` = NOW() WHERE `id` = '" . (int) $comment_data['Parent_Comment_ID'] . "'");

        $message_id = $this->db->get_var("SELECT `Message_ID` FROM `Comments_Text` WHERE `id` = '" . $id . "'");

        $cc_data = $this->core->sub_class->get_by_id($this->_message_cc);

        $this->core->event->execute("dropCommentPrep", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message_id, $id);

        // Помещяем комментарий в корзину
        $trash = new nc_Trash;
        $trash->add_comment($id);

        // delete comment from base
        $this->db->query("DELETE FROM `Comments_Text` WHERE `id` = '" . (int) $id . "'");

        $this->core->event->execute("dropComment", $cc_data['Catalogue_ID'], $cc_data['Subdivision_ID'], $cc_data['Sub_Class_ID'], $cc_data['Class_ID'], $message_id, $id);

        // this allow delete children with other `User_ID`
        array_push($accessibilityDeleteArray, $id);

        // update count value
        $this->refreshCount($comment_data['Message_ID'], $comment_data['Parent_Comment_ID'], "-");

        if (!isset($this->_commentsData)) {
            $this->loadArrays($message_id);
        }

        // get children
        $childrenExist = $this->getChildren($id);
        // if comments with children - drop recursive
        if (!empty($childrenExist)) {
            foreach ($childrenExist AS $child_comment_id) {
                $this->deleteComment($child_comment_id);
            }
        }

        // pop value from temp array
        array_pop($accessibilityDeleteArray);

        // refresh arrays
        if (!empty($this->_commentsData)) {
            foreach ($this->_commentsData AS $comment) {
                if ($comment['id'] == $id) {
                    $this->loadArrays($comment['Message_ID']);
                    break;
                }
            }
        }

        // return result
        return true;
    }

    /**
     * Validate comment text
     *
     * @param string comment text
     * @param int template id
     * @param bool strip uncorrect tags
     * @param bool clear new line symbols
     *
     * @return string validated comment text
     */
    public function commentValidateShow($comment, $template, $strip = true, $clear = false) {
        // check magic quotes
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($comment);
        }
        // strip uncorrect tags
        if ($strip) {
            // clear HTML tags
            //$comment = strip_tags($comment, "<div><b><i><u><img><span><a><br><br/>");
            //$comment = htmlspecialchars_decode($comment);
        }
        // drop new line symbols
        if ($clear)
            $comment = str_replace(array("\r", "\n"), "", $comment);
        // return slashed result
        $comment = addslashes($comment);
        $comment = preg_replace('/<\/script>/', '<\/scri"+"pt>', $comment);
        return $comment;
        //return addslashes($comment);
    }

    /**
     * Refresh `Count` value in `Comments_Count`
     * @param `Message_ID` value
     * @param `Parent_Comment_ID` value
     * @return true if executed or false
     */
    private function refreshCount($message, $parent = 0, $sign = "+") {
        // validate
        $message = (int) $message;
        if (
                !in_array($sign, array("+", "-"))
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // check existion
        $exist = $this->db->get_var("SELECT `id` FROM `Comments_Count`
      WHERE `Sub_Class_ID` = '" . $this->_message_cc . "' AND `Message_ID` = '" . $message . "'");
        // insert or update count
        if ($exist) {
            // comment or reply
            $suffix = $parent ? "Replies" : "Comments";
            // query
            $result = $this->db->query("UPDATE `Comments_Count`
        SET `Count" . $suffix . "` = `Count" . $suffix . "` " . $sign . " 1
        WHERE `Sub_Class_ID` = '" . $this->_message_cc . "' AND `Message_ID` = '" . $message . "'");
        } else {
            if ($sign != "-") {
                $result = $this->db->query("INSERT INTO `Comments_Count`
          (`Sub_Class_ID`, `Message_ID`, `CountComments`)
          VALUES
          ('" . $this->_message_cc . "', '" . $message . "', 1)");
            }
        }
        // return result
        return $result;
    }

    /**
     * Get comments count public method
     * @param `Message_ID`
     * @param 0 - count comments and replies, 1 - only comments, 2 - only replies
     * @return counted value
     */
    public function count($message_id, $selector = 0) {
        static $table = array();
        $message_id = (int) $message_id;

        if (!isset($table[$this->_message_cc])) {
            $res = $this->db->get_results("SELECT * FROM `Comments_Count`
                                      WHERE `Sub_Class_ID` = '" . $this->_message_cc . "'", ARRAY_A);
            $table[$this->_message_cc] = array();
            if (!empty($res))
                foreach ($res as $v) {
                    $table[$this->_message_cc][$v['Message_ID']]
                            = array($v['CountComments'] + $v['CountReplies'], $v['CountComments'], $v['CountReplies']);
                }
        }
        return intval($table[$this->_message_cc][$message_id][$selector]);
    }


    public function rating($comment_id, $rating = 0, $message_id=0) {
        if ( ! $this->_enable_rating) {
            return;
        }

        $comment_id  = (int)$comment_id;
        $message_id  = (int)$message_id;
        $rating      = (int)$rating;
        $like        = (int)($rating > 0);
        $dislike     = (int)($rating < 0);
        $cookie_name = "nc_mod_comment_" . $comment_id;
        $cookie_value = $this->core->input->fetch_cookie($cookie_name);
        $res = 0;

        if (! $cookie_value ) {
            setcookie($cookie_name, ( $like ? '2' : '1' ) , time() + 3600 * 24, "/");
            $res = 1;
        } elseif ( ($dislike AND $cookie_value == 2) OR ($like AND $cookie_value == 1) ) { //return "dislike".$cookie_value;
			setcookie($cookie_name, false, time() -1, "/");
            $res = 1;
        }

        if ( $res ) {
            $query_where = ' WHERE `id`=' . $comment_id
                . ($message_id ? ' AND Message_ID='.$message_id : '')
                . (' AND User_ID!='.(int)$this->AUTH_USER_ID);
            $this->db->query('UPDATE `Comments_Text` SET `Like`=`Like`+' . $like . ', `Dislike`=`Dislike`+' . $dislike . $query_where);
            return $this->db->get_var('SELECT (`Like` - `Dislike`) AS `Rating` FROM `Comments_Text`' . $query_where);
        } else {
            return false;
        }
    }

    public function getNewComments($message_id, $last_updated, $comment_id = 0) {
        $res = array();

        if (!empty($this->_commentsData))
            foreach ($this->_commentsData as $v) {
                if ($v['Updated'] > $last_updated || $v['Date'] > $last_updated) {
                    if ($comment_id && $v['id'] > $comment_id)
                        continue;
                    $res[] = $v;
                }
            }

        return $res;
    }

    /**
     * getNewCommentsIds function.
     *
     * @access public
     * @param mixed $message_id
     * @param int $comment_id (default: 0)
     * @return void
     */
    public function getNewCommentsIds($message_id, $comment_id = 0) {

        $last_visit = $this->db->get_var("SELECT `LastUpdated` FROM `Comments_LastVisit`
    										WHERE `User_ID` = '" . $this->AUTH_USER_ID . "'
    										AND `Class_ID` = '" . $this->_current_cc['Class_ID'] . "'
    										AND `Message_ID` = '" . $message_id . "'");
        $res = array();
        $last_visit = strtotime($last_visit);

        if (!empty($this->_commentsData))
            foreach ($this->_commentsData as $v) {
                if (($v['Updated'] > $last_visit || $v['Date'] > $last_visit) && $v['User_ID'] != $this->AUTH_USER_ID) {
                    if ($comment_id && $v['id'] > $comment_id)
                        continue;

                    $res[] = $v['id'];
                }
            }

        return $res;
    }

    public function isRightsToSubscribe() {
        $this->_getAccessibility();
        return $this->_accessibilityAdd;
    }

    /**
     * Static function, returns rule ID by `Catalogue_ID` and `Catalogue_Type`
     * @param link to ezSQL object
     * @param `Catalogue_ID` if set `Catalogue_Type` (second parameter), or direct rule id
     * @param `Catalogue_Type`, value in array("Catalogue", "Subdivision", "Sub_Class")
     * @return rule id if exist
     */
    static function getRuleData(&$db, $id) {
        if (!is_object($db) || !is_array($id)) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }

        $id = array_map("intval", $id);
        $id = array_pad($id, 3, 0);

        list ($catalogue, $sub, $cc) = $id;
        $where_str = "`Catalogue_ID` = '" . $catalogue . "'";
        $where_str.= " AND " . ($sub ? "`Subdivision_ID` = '" . $sub . "'" : "NOT `Subdivision_ID`");
        $where_str.= " AND " . ($cc ? "`Sub_Class_ID` = '" . $cc . "'" : "NOT `Sub_Class_ID`");
        return $db->get_row("SELECT * FROM `Comments_Rules` WHERE " . $where_str, ARRAY_A);
    }

    public function getEditRule() {
        return $this->_comment_rules['Edit_Rule'];
    }

    public function getDeleteRule() {
        return $this->_comment_rules['Delete_Rule'];
    }

    public function isBBcodes() {
        $res = $this->core->get_settings('BBcode', 'comments');
        return $res;
    }

    public function isModerator() {
        // check for moderation
        if (is_object($this->_perm) && $this->_message_cc) {
            return $this->_perm->isSubClass($this->_message_cc, MASK_MODERATE);
        }
        // false
        return false;
    }

    public function getMailTemplate() {
        $res = $this->core->get_settings('Mail_Template', 'comments');
        return $res;
    }

    public function getMailSubject() {
        $res = $this->core->get_settings('Mail_Subject', 'comments');
        return $res;
    }

    /**
     * Static function, append rule into the base
     * @param link to ezSQL object
     * @param `Catalogue_ID` if set `Catalogue_Type` (second parameter), or direct rule id
     * @param `Catalogue_Type`, value in array("Catalogue", "Subdivision", "Sub_Class")
     * @param `Access_ID` value in array(1, 2, 3)
     * @param `Edit_Rule` value in array("disable", "enable", "unreplied")
     * @param `Delete_Rule` value in array("disable", "enable", "unreplied")
     * @return inserted rule id
     */
    static function addRule(&$db, $id, $access, $edit, $delete) {
        if (
                !is_object($db) ||
                !is_array($id) ||
                !in_array($access, array(1, 2, 3, 4)) ||
                !in_array($edit, array("disable", "enable", "unreplied")) ||
                !in_array($delete, array("disable", "enable", "unreplied"))
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // get args
        $id = array_map("intval", $id);
        $id = array_pad($id, 3, 0);
        list ($catalogue, $sub, $cc) = $id;
        // add comment relation
        $db->query("INSERT INTO `Comments_Rules`
    (`Catalogue_ID`, `Subdivision_ID`, `Sub_Class_ID`, `Access_ID`, `Edit_Rule`, `Delete_Rule`)
    VALUES
    ('" . $catalogue . "', '" . $sub . "', '" . $cc . "', '" . $access . "', '" . $edit . "', '" . $delete . "')");
        // return result
        return $db->insert_id;
    }

    /**
     * Static function, update rule into the base
     * @param link to ezSQL object
     * @param `Catalogue_ID` if set `Catalogue_Type` (second parameter), or direct rule id
     * @param `Catalogue_Type`, value in array("Catalogue", "Subdivision", "Sub_Class")
     * @param `Access_ID` value in array(1, 2, 3)
     * @param `Edit_Rule` value in array("disable", "enable", "unreplied")
     * @param `Delete_Rule` value in array("disable", "enable", "unreplied")
     * @return true if executable or false
     */
    static function updateRule(&$db, $id, $access, $edit, $delete) {
        if (
                !is_object($db) ||
                !is_array($id) ||
                !in_array($access, array(1, 2, 3, 4)) ||
                !in_array($edit, array("disable", "enable", "unreplied")) ||
                !in_array($delete, array("disable", "enable", "unreplied"))
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // get args
        $id = array_map("intval", $id);
        $id = array_pad($id, 3, 0);
        list ($catalogue, $sub, $cc) = $id;
        // where string
        $where_str = "`Catalogue_ID` = '" . $catalogue . "'";
        $where_str.= " AND " . ($sub ? "`Subdivision_ID` = '" . $sub . "'" : "NOT `Subdivision_ID`");
        $where_str.= " AND " . ($cc ? "`Sub_Class_ID` = '" . $cc . "'" : "NOT `Sub_Class_ID`");
        // drop comment rule from base and return result
        return $db->query("UPDATE `Comments_Rules` SET
        `Access_ID` = '" . $access . "',
        `Edit_Rule` = '" . $edit . "',
        `Delete_Rule` = '" . $delete . "'
      WHERE " . $where_str);
    }

    /**
     * Static function, delete rule from the base
     * call static dropComments function with similar parameter
     * @param link to ezSQL object
     * @param mixed `Catalogue_ID` if set `Catalogue_Type` (second parameter), or direct rule id
     * @param `Catalogue_Type`, value in array("Catalogue", "Subdivision", "Sub_Class")
     * @return true if executable or false
     */
    static function dropRule(&$db, $id) {
        if (
                !is_object($db) ||
                !is_array($id)
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // get args
        $id = array_map("intval", $id);
        $id = array_pad($id, 3, 0);
        list ($catalogue, $sub, $cc) = $id;
        // where string
        $where_str = "`Catalogue_ID` = '" . $catalogue . "'";
        $where_str.= " AND " . ($sub ? "`Subdivision_ID` = '" . $sub . "'" : "NOT `Subdivision_ID`");
        $where_str.= " AND " . ($cc ? "`Sub_Class_ID` = '" . $cc . "'" : "NOT `Sub_Class_ID`");
        // drop comment rule from base and return result
        $db->query("DELETE FROM `Comments_Rules` WHERE " . $where_str);
    }

    static function dropRuleCatalogue(&$db, $id) {
        if (
                !is_object($db) ||
                !( is_numeric($id) || is_array($id) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // where string
        if (is_array($id)) {
            $id = array_map("intval", $id);
            $where_str = "`Catalogue_ID` IN (" . join(", ", $id) . ") AND `Subdivision_ID` = 0 AND `Sub_Class_ID` = 0";
        } else {
            $where_str = "`Catalogue_ID` = '" . $id . "' AND `Subdivision_ID` = 0 AND `Sub_Class_ID` = 0";
        }
        // drop comment rule from base and return result
        $db->query("DELETE FROM `Comments_Rules` WHERE " . $where_str);
    }

    static function dropRuleSubdivision(&$db, $id) {
        if (
                !is_object($db) ||
                !( is_numeric($id) || is_array($id) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // where string
        if (is_array($id)) {
            $id = array_map("intval", $id);
            $where_str = "`Subdivision_ID` IN (" . join(", ", $id) . ") AND `Sub_Class_ID` = 0";
        } else {
            $where_str = "`Subdivision_ID` = '" . $id . "' AND `Sub_Class_ID` = 0";
        }
        // drop comment rule from base and return result
        $db->query("DELETE FROM `Comments_Rules` WHERE " . $where_str);
    }

    static function dropRuleSubClass(&$db, $id) {
        if (
                !is_object($db) ||
                !( is_numeric($id) || is_array($id) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // where string
        if (is_array($id)) {
            $id = array_map("intval", $id);
            $where_str = "`Sub_Class_ID` IN (" . join(", ", $id) . ")";
        } else {
            $where_str = "`Sub_Class_ID` = '" . $id . "'";
        }
        // drop comment rule from base and return result
        $db->query("DELETE FROM `Comments_Rules` WHERE " . $where_str);
    }

    /**
     * Static function, delete comments and their counted values from the base
     * @param link to ezSQL object
     * @param mixed `Catalogue_ID` if set `Catalogue_Type` (second parameter), or direct rule id
     * @param `Catalogue_Type`, value in array("Catalogue", "Subdivision", "Sub_Class")
     * @param mixed `Message_ID` value(s)
     * @return true if executable or false
     */
    static function dropComments(&$db, $id, $type, $message = 0) {
        if (
                !is_object($db) ||
                !( is_numeric($id) || is_array($id) ) ||
                !in_array($type, array("Catalogue", "Subdivision", "Sub_Class")) ||
                !( is_numeric($message) || is_array($message) )
        ) {
            throw new Exception(NETCAT_MODULE_COMMENTS_UNCORRECT_DATA);
        }
        // `Sub_Class_ID`
        if (is_array($id)) {
            $id = array_map("intval", $id);
            $sub_class_arr = $db->get_col("SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE `" . $type . "_ID` IN (" . join(", ", $id) . ")");
        } else {
            $sub_class_arr = $db->get_col("SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE `" . $type . "_ID` = '" . $id . "'");
        }

        // where string
        if (!empty($sub_class_arr)) {
            $where_str = "`Comments_Text`.`Sub_Class_ID` IN (" . join(", ", $sub_class_arr) . ")";
        }

        // `Message_ID`, where string
        if (is_array($message) && !empty($message)) {
            $message = array_map("intval", $message);
            $where_str.= ($where_str ? " AND " : "") . "`Comments_Text`.`Message_ID` IN (" . join(", ", $message) . ")";
        } else {
            $where_str.= $message > 0 ? ($where_str ? " AND " : "") . "`Comments_Text`.`Message_ID` = '" . $message . "'" : "";
        }

        // drop comment rule from base and return result
        if ($where_str) {
            $result = $db->query("DELETE `Comments_Text`, `Comments_Count`
        FROM `Comments_Text` INNER JOIN `Comments_Count`
        WHERE `Comments_Text`.`Sub_Class_ID` = `Comments_Count`.`Sub_Class_ID`
          AND `Comments_Text`.`Message_ID` = `Comments_Count`.`Message_ID`
          AND " . $where_str);
        }
        // return result
        return $result ? $result : false;
    }

    public function _getTemplate($id = 0) {
        // all templates
        static $TemplatesData = array();
        // get all data once from base
        if (empty($TemplatesData)) {
            $TemplatesData = $this->db->get_results("SELECT * FROM `Comments_Template`", ARRAY_A);
        }
        // return if no result
        if (empty($TemplatesData))
            return false;
        // walk
        foreach ($TemplatesData AS $value) {
            if ($value['Default'])
                $default = $value;
            if ($value['ID'] != $id)
                continue;
            $settings = $value;
        }
        // if no comparison set default
        if (empty($settings) && !empty($default))
            $settings = $default;
        // if no default set first in array
        if (empty($settings))
            $settings = $TemplatesData[1];

        $comments_editor = new nc_module_tpl_editor();
        $comments_editor->load('comments', $settings['ID'])->fill();
        $file_settings = $comments_editor->get_all_fields();

        $result = array_merge($settings, $file_settings);

        // return result array
        return $result;
    }

    /**
     * Accessibility check function
     * set internal class object access variables for current cc
     * @return true or false
     */
    private function _getInheritedRules() {
        static $cc_rules = array();

        // moderation freedom
        if ($this->isModerator()) {
            // for moderators
            // access - registered, edit - enable, delete - enable
            return array(
                'Access_ID' => 2,
                'Edit_Rule' => 'enable',
                'Delete_Rule' => 'enable'
            );
        }

        // got result in static array
        if (isset($cc_rules[$this->_message_cc]))
            return $cc_rules[$this->_message_cc];

        // get result from global arrays
        if ($this->_current_cc['Sub_Class_ID'] == $this->_message_cc) {
            $comment_rule_id = $this->_current_cc['Comment_Rule_ID'];
            if (!$comment_rule_id) {
                $comment_rule_id = $this->_current_sub['Comment_Rule_ID'];
            }
            $i = 0;
            while (!$comment_rule_id) {
                if (empty($this->_parent_sub_tree[$i]))
                    break;
                $comment_rule_id = $this->_parent_sub_tree[$i]['Comment_Rule_ID'];
                $i++;
            }
        }

        if (!$comment_rule_id) {
            // get from cc
            list($comment_rule_id, $sub_id, $cat_id) = $this->db->get_row("SELECT `Comment_Rule_ID`, `Subdivision_ID`, `Catalogue_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` = '" . $this->_message_cc . "'", ARRAY_N);
        }

        if (!$comment_rule_id) {
            // walk top
            while (!$comment_rule_id) {
                // get from sub
                list($comment_rule_id, $parent_sub_id) = $this->db->get_row("SELECT `Comment_Rule_ID`, `Parent_Sub_ID` FROM `Subdivision` WHERE `Subdivision_ID` = '" . $sub_id . "'", ARRAY_N);
                // got result or root subdivision node
                if ($comment_rule_id || $parent_sub_id == 0)
                    break;
                // for next iteration
                $sub_id = $parent_sub_id;
            }
        }

        if (!$comment_rule_id) {
            // get from catalogue
            $comment_rule_id = $this->db->get_var("SELECT `Comment_Rule_ID` FROM `Catalogue` WHERE `Catalogue_ID` = '" . $cat_id . "'");
        }

        // get rule data
        $comment_permissions = $this->db->get_row("SELECT `Access_ID`, `Edit_Rule`, `Delete_Rule` FROM `Comments_Rules` WHERE `ID` = '" . $comment_rule_id . "'", ARRAY_A);

        $result = !empty($comment_permissions) ? $comment_permissions : false;

        // to static array
        $cc_rules[$this->_message_cc] = $result;

        // return true if set array or false
        return $result;
    }

    private function _getAccessibility($id = 0) {
        // start values
        $this->_accessibilityAdd = false;
        $this->_accessibilityEdit = false;
        $this->_accessibilityDelete = false;

        // moderation freedom
        if ($this->isModerator()) {
            $this->_accessibilityAdd = true;
            $this->_accessibilityEdit = true;
            $this->_accessibilityDelete = true;
            return;
        }

        if (empty($this->_comment_rules))
            return;

        // check add access
        switch ($this->_comment_rules['Access_ID']) {
            // 1 - all
            case 1:
                $this->_accessibilityAdd = true;
                break;
            // 2 - authorized
            case 2:
                if ($this->AUTH_USER_ID)
                    $this->_accessibilityAdd = true;
                break;
            // 3 - permitted
            case 3:
                if (CheckUserRights($this->_message_cc, 'comment', 1))
                    $this->_accessibilityAdd = true;
                break;
            // 4 - nobody
        }

        if (!$id)
            return;

        // check edit and delete access
        if (!empty($this->_commentsData)) {
            foreach ($this->_commentsData AS $comment) {
                if ($comment['id'] == $id)
                    break;
            }
            // check
            switch ($this->_comment_rules['Access_ID']) {
                // 1 - all
                case 1:
                // 2 - authorized
                case 2:
                    if ($this->AUTH_USER_ID != 0 && $this->AUTH_USER_ID == $comment['User_ID']) {
                        $this->_accessibilityEdit = true;
                        $this->_accessibilityDelete = true;
                    }
                    break;
                // 3 - permitted
                case 3:
                    if (CheckUserRights($this->_message_cc, 'comment', 1) && $this->AUTH_USER_ID == $comment['User_ID']) {
                        $this->_accessibilityEdit = true;
                        $this->_accessibilityDelete = true;
                    }
                    break;
                // 4 - nobody
            }
        }

        // check children
        $childrenExist = $this->getChildren($id);
        // eidt accessibility
        if ($this->_comment_rules['Edit_Rule'] == "disable")
            $this->_accessibilityEdit = false;
        if ($this->_comment_rules['Edit_Rule'] == "unreplied" && $childrenExist)
            $this->_accessibilityEdit = false;
        // delete accessibility
        if ($this->_comment_rules['Delete_Rule'] == "disable")
            $this->_accessibilityDelete = false;
        if ($this->_comment_rules['Delete_Rule'] == "unreplied" && $childrenExist)
            $this->_accessibilityDelete = false;
    }

    /**
     * changeChecked function.
     *
     * @access public
     * @param mixed $comment
     * @param string $action (default: '')
     * @return true or false
     */
    public function changeChecked($comment, $action = '') {

        $checked = '';
        switch ($action) {
            case 'Check':
                $checked = '1';
                break;

            case 'Uncheck':
                $checked = '0';
                break;

            case '':
                $checked = '1 - `Checked`';
                break;
        }

        $data = $this->db->get_row("SELECT c.*,  sc.`Class_ID`, sc.`Subdivision_ID`, sc.`Catalogue_ID`
													FROM `Comments_Text` AS c
													LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = c.`Sub_Class_ID`
  												WHERE `id` = '" . (int) $comment . "'", ARRAY_A);

        $this->core->event->execute(($checked ? "checkCommentPrep" : "checkCommentPrep"), $data['Catalogue_ID'], $data['Subdivision_ID'], $data['Sub_Class_ID'], $data['Class_ID'], $data['Message_ID'], $comment);

        $this->db->query("UPDATE `Comments_Text` SET `Checked` = " . $checked . " WHERE `id` ='" . (int) $comment . "'");
		
		// update comments or replies count
		if ($action) {
			$field = "Count" . (!$data['Parent_Comment_ID'] ? "Comments" : "Replies");
			$this->db->query("UPDATE `Comments_Count`
				SET `" . $field . "` = `" . $field . "` " . ($checked ? '+' : '-') . " 1
				WHERE `Sub_Class_ID` = " . intval($data['Sub_Class_ID']) . " AND `Message_ID` = " . intval($data['Message_ID']));
		}
		
        if (empty($data))
            return false;

        $this->core->event->execute(($data['Checked'] ? "checkComment" : "uncheckComment"), $data['Catalogue_ID'], $data['Subdivision_ID'], $data['Sub_Class_ID'], $data['Class_ID'], $data['Message_ID'], $comment);

        return true;
    }

    /**
     * editCommentForm function.
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function editCommentForm($id) {
		global $ADMIN_PATH;
        $comment_data = $this->db->get_row("SELECT c.`Comment`, c.`User_ID`, c.`Guest_Email`,	c.`Guest_Name`, u.`Login`
     																		FROM `Comments_Text` as c
     																		LEFT JOIN `User` as u ON u.`User_ID` = c.`User_ID`
     																		WHERE c.`id` = '" . (int) $id . "'", ARRAY_A);

        $result = "<form id='adminForm' class='nc-form' action='admin.php' method='post'>";
        // author info
        $result .= "<fieldset>\n" .
                "<legend>\n" .
                "<b><font color='gray'>" . NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR . "</font></b>\n" .
                "</legend>\n";
        if ($comment_data['User_ID'])
            $result .= NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_USER . " <a href='" . $ADMIN_PATH . "user/index.php?phase=4&UserID=" . $comment_data['User_ID'] . "'>" . $comment_data['Login'] . "</a>";

        else {
            $result .= NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_NAME;
            $result .= "<input type='text' name='guest_name' value='" . $comment_data['Guest_Name'] . "' style='margin: 5px 11px;'/><br/>";
            $result .= NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_EMAIL;
            $result .= "<input type='text' name='guest_email' value='" . $comment_data['Guest_Email'] . "' style='margin: 0px 5px;' /><br/>";
        }
        $result .= "</fieldset>\n";
        // comment text
        $result .= "<fieldset>\n" .
                "<legend>\n" .
                "<b><font color='gray'>" . NETCAT_MODULE_COMMENTS_ADMIN_EDIT_TEXT . "</font></b>\n" .
                "</legend>\n";
        if ($this->isBBcodes())
            $result .= nc_bbcode_bar('this', 'adminForm', 'text', 1);
        $result .= "<textarea id='text' name='text' style='width:50%'>" . htmlspecialchars_decode($comment_data['Comment']) . "</textarea>";
        $result .= "</fieldset><br/>\n";

        $result .= "<input type='hidden' name='comment' value='" . $id . "' />";
        $result .= "<input type='hidden' name='action' value='save' />";
        $result .= "<input type='hidden' name='phase' value='151' />";
        $result .= "</form>";

        $result .= "<form name='deleteForm' id='deleteForm' method='post' action='admin.php'>\n" .
            "<input type='hidden' name='phase' value='131'>\n" .
            "<input type='hidden' name='comment{$id}' value='{$this->_message_cc}'>\n" .
            "</form>";

        echo $result;
    }

    /**
     * editComment function.
     *
     * @access public
     * @param string $postdata (default: '')
     * @return void
     */
    public function editComment($postdata = '') {

        $comment_data = $this->db->get_results("SELECT c.*,  sc.`Class_ID`, sc.`Subdivision_ID`, sc.`Catalogue_ID`
													FROM `Comments_Text` AS c
													LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = c.`Sub_Class_ID`
													WHERE `id` = " . (int) $postdata['comment'] . " ", ARRAY_A);

        if ($postdata['guest_email'] && !nc_check_email($postdata['guest_email'])) {
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_EDIT_SAVE_EMAIL_ERROR, 'error');
            return;
        }

        $this->core->event->execute("updateCommentPrep", $comment_data['Catalogue_ID'], $comment_data['Subdivision_ID'], $comment_data['Sub_Class_ID'], $comment_data['Class_ID'], $comment_data['Message_ID'], $postdata['comment']);

        //update
        $this->db->query("UPDATE `Comments_Text` SET `Updated` = NOW(), `Comment` = '" . $this->db->escape(htmlspecialchars($postdata['text'])) . "', `Guest_Name` = '" . $postdata['guest_name'] . "',  `Guest_Email` = '" . $postdata['guest_email'] . "'  WHERE `id` = '" . (int) $postdata['comment'] . "'");

        //logging
        $this->core->event->execute("updateComment", $comment_data['Catalogue_ID'], $comment_data['Subdivision_ID'], $comment_data['Sub_Class_ID'], $comment_data['Class_ID'], $comment_data['Message_ID'], $postdata['comment']);

        nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_EDIT_SAVE_OK, 'ok');
    }

    /**
     * checkRights function.
     *
     * @access public
     * @return true or false
     */
    public function checkRights() {

        switch ($this->settings['Premoderation']) {
            case 1:
                return $checked = $this->AUTH_USER_ID ? 1 : 0;
                break;
            case 2:
                return $checked = $this->isModerator() ? 1 : 0;
                break;
            default:
                return $checked = 1;
                break;
        }
    }

}

/**
 * Confirm to delete comments from the base
 * @param `action` all comments or selected
 * @return count of comments for $action='Selected' and 1 for 'All'
 */
function confirm_deleteComment($action) {
    global $db;

    $nc_core = nc_Core::get_object();

    $html = "<ul>\n<form action ='admin.php' method = 'post'>\n";
    if ($action == 'Selected') {

        while (list($key, $val) = each($_POST))
            if (substr($key, 0, 7) == "comment") {
                $id[] = substr($key, 7);
                $html .="<input type='hidden' name='" . $key . "' value='" . $val . "'>\n";
            }

        $num_comment = count($id);

        if (!$num_comment) {
            return false;
        }

        $data = $db->get_results("SELECT c.`id`, c.`Comment`, c.`User_ID`, c.`Guest_Email`,	c.`Guest_Name`, u.`Login`
  	   												FROM `Comments_Text` as c
  	   												LEFT JOIN `User` as u ON u.`User_ID` = c.`User_ID`", ARRAY_A);
        if (empty($data))
            return false;

        $html .= "<input type='hidden' name='phase' value='14'>\n";
        foreach ($data as $value) {
            if (in_array($value['id'], $id)) {

                $value['Comment'] = nc_substr($value['Comment'], 0, 80) . (strlen($value['Comment']) > 80 ? "..." : "");
                $html .= "<li>" . $value['id'] . ": " . $value['Comment'] . " (" . ($value['User_ID'] ? $value['Login'] : ($value['Guest_Name'] ? $value['Guest_Name'] : NETCAT_MODULE_COMMENTS_GUEST)) . ")</li>\n";
            }
        }

        $html .= $nc_core->token->get_input();
        $html .= "</form></ul>";
        if ($num_comment) {
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONFIRM_DEL, 'info');
            echo $html;
        } else {
            nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_NO_COMMENTS, 'error');
        }
    }

    if ($action == 'All') {

        $num_comment = 1;
        $html .= "<input type='hidden' name='phase' value='141'>\n";
        $html .= "</form>";
        nc_print_status(NETCAT_MODULE_COMMENTS_ADMIN_CONFIRM_DEL_ALL, 'info');
        echo $html;
    }
    return $num_comment;
}

/**
 * Delete all comments from the base
 * @return true or false
 */
function delete_allComments() {

    global $db;
    $nc_core = nc_Core::get_object();
    $data = $db->get_results("SELECT c.*,  sc.`Class_ID`, sc.`Subdivision_ID`, sc.`Catalogue_ID`
													FROM `Comments_Text` AS c
													LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = c.`Sub_Class_ID`", ARRAY_A);
    if (empty($data))
        return false;
    // logging
    foreach ($data as $value) {
        $id = (int)$value['id'];

        $nc_core->event->execute("dropCommentPrep", $value['Catalogue_ID'], $value['Subdivision_ID'], $value['Sub_Class_ID'], $value['Class_ID'], $value['Message_ID'], $id);

        // delete comment from base
        $db->query("DELETE FROM `Comments_Text` WHERE `id` = {$id}");
        // zero count
        $db->query("DELETE FROM `Comments_Count` WHERE `id` = {$id}");

        $nc_core->event->execute("dropComment", $value['Catalogue_ID'], $value['Subdivision_ID'], $value['Sub_Class_ID'], $value['Class_ID'], $value['Message_ID'], $value['id']);
    }

    // return result
    return true;
}

?>