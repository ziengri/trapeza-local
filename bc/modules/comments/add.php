<?php
/* $Id: add.php 6155 2012-02-07 09:28:43Z gaika $ */

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
require_once ($NETCAT_FOLDER."vars.inc.php");

// for IE
if ( !isset($NC_CHARSET) ) $NC_CHARSET = "windows-1251";

// header with correct charset
header("Content-type: text/plain; charset=".$NC_CHARSET);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// esoteric method...
ob_start("ob_gzhandler");

$res = "{'id':'0', 'parent_id':'0', 'commentHTML':'', 'error':''}";

if ($_POST['message_cc'] && $_POST['message_id']) {

	// disable auth screen
	define("NC_AUTH_IN_PROGRESS", 1);
	define("NC_ADDED_BY_AJAX", 1);

	// include system
	require ($INCLUDE_FOLDER."index.php");
  global $db, $AUTH_USER_ID;
  
	// componet id must be different as $cc for example $needcc
	$message_cc = intval($_POST['message_cc']);
  $message_id = intval($_POST['message_id']);
  $parent_mess_id = intval($_POST['parent_mess_id']);
  $template_id = intval($_POST['template_id']);
  $last_updated = intval($_POST['last_updated']);
  $comment_edit = intval($_POST['comment_edit']);
  $comment = $_POST['nc_commentTextArea'];
  $nc_comments_guest_name = $nc_core->input->fetch_post('nc_comments_guest_name');
  $nc_comments_guest_email = $nc_core->input->fetch_post('nc_comments_guest_email');
  $settings = $nc_core->get_settings('', 'comments');
  
  
  // CAPTCHA
  if ( $nc_core->modules->get_by_keyword("captcha") ) {
    $nc_captcha_code = $nc_core->input->fetch_post('nc_captcha_code');
    $nc_captcha_hash = $nc_core->input->fetch_post('nc_captcha_hash');
  }
  
  if ($comment && !$NC_UNICODE ) {
    $comment = $nc_core->utf8->utf2win($comment);
    //$nc_comments_guest_name = $nc_core->utf8->utf2win($nc_comments_guest_name);
  }
  
  $user_id = $AUTH_USER_ID ? $AUTH_USER_ID : 0;
  
  // initialize nc_comments
  $nc_comments = new nc_comments($message_cc);

	// get template data
  $templateData = $nc_comments->_getTemplate($template_id);
  
  // CAPTCHA
  if ( $nc_core->modules->get_by_keyword("captcha") && $settings['UseCaptcha']) {
    if ( !$user_id && !nc_captcha_verify_code($nc_captcha_code, $nc_captcha_hash, 0) ) {
      $new_hash = nc_captcha_generate_hash();
      nc_captcha_generate_code($new_hash);
      eval("\$templateData['Warn_Text'] = str_replace(\"%WARNTEXT\", NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_CAPTCHA, \$templateData['Warn_Text']);");
      die("{'error':'enter', 'captchawrong':escape(\"".$nc_comments->commentValidateShow($templateData['Warn_Text'], $template_id, 0, 1)."\"), 'hash' : '".$new_hash."'}");
    }
  }
  
  
  //warnText
  if(!$AUTH_USER_ID) {
    if ($settings['GuestNameForced'] && !$nc_comments_guest_name && (!$settings['GuestEmailForced'] || $settings['GuestEmailForced'] && $nc_comments_guest_email)) {
      eval("\$templateData['Warn_Text'] = str_replace(\"%WARNTEXT\", NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_NAME, \$templateData['Warn_Text']);");
      die("{'error':'enter', 'unset':escape(\"".$nc_comments->commentValidateShow($templateData['Warn_Text'], $template_id, 0, 1)."\")}");
    }
    if ($settings['GuestEmailForced'] && !$nc_comments_guest_email && (!$settings['GuestNameForced'] || $settings['GuestNameForced'] && $nc_comments_guest_name) || $nc_comments_guest_email && !nc_check_email($nc_comments_guest_email)) {
      eval("\$templateData['Warn_Text'] = str_replace(\"%WARNTEXT\", NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_EMAIL, \$templateData['Warn_Text']);");
      die("{'error':'enter', 'unset':escape(\"".$nc_comments->commentValidateShow($templateData['Warn_Text'], $template_id, 0, 1)."\")}");
    }
    if ($settings['GuestNameForced'] && !$nc_comments_guest_name && $settings['GuestEmailForced'] && !$nc_comments_guest_email) {
      eval("\$templateData['Warn_Text'] = str_replace(\"%WARNTEXT\", NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_EMAIL, \$templateData['Warn_Text']);");
      die("{'error':'enter', 'unset':escape(\"".$nc_comments->commentValidateShow($templateData['Warn_Text'], $template_id, 0, 1)."\")}");
    }
  }
  

  if (!$comment_edit && $comment) {
    // append comment into the base
    try {
      $comment_id = $nc_comments->addComment($message_id, $parent_mess_id, $comment, $user_id, $nc_comments_guest_name, $nc_comments_guest_email);
      require_once($MODULE_FOLDER."comments/nc_commsubs.class.php");
      $nc_commsubs = new nc_commsubs();
      $nc_commsubs->new_comment( $comment_id, $nc_comments->getMailTemplate(), $nc_comments->getMailSubject() );
    }
    catch (Exception $e) {
      die("{'error':'".$e->getMessage()."'}");
    }
  }
  else {
    // load array for update
    $nc_comments->loadArrays($message_id);
    $comment_id = $parent_mess_id;
  }
  
  if ($comment_id) {
    // get need comments
    $data = $nc_comments->getNewComments($message_id, $last_updated, (!$comment_edit ? $comment_id : 0 ));
    // compile json result
    if ( !empty($data) ) {
      foreach ($data AS $value) {
        // json string put in array
        if ($value['Updated'] > $last_updated) {
          // if children exist - update block
          if ( !$nc_comments->getChildren($value['id']) ) {
            $CommentData = $nc_comments->getCommentFromArray($value['id']);
            // drop from DOM
            $resArr[] = "{'id':'".$value['id']."', 'update':'-1', 'error':'0'}";
            // get parent refreshaed block
            $commentHTML = $nc_comments->getComment($message_id, $CommentData, $template_id, false);
            // past parent comment block
            $commentHTML = str_replace("%COMMENT_".$message_cc."_".$message_id."_".$CommentData['id']."%", nl2br($CommentData['Comment']), $commentHTML);
            // bbcode processing
            if ($nc_comments->isBBcodes()) $commentHTML = nc_bbcode($commentHTML);
            $resArr[] = "{'id':'".$CommentData['id']."', 'parent_id':'".$CommentData['Parent_Comment_ID']."', 'commentHTML':escape(\"".$nc_comments->commentValidateShow($commentHTML, $template_id)."\"), 'updated':'".$CommentData['LastUpdated']."', 'edit_rule':'".$nc_comments->getEditRule()."', 'delete_rule':'".$nc_comments->getDeleteRule()."', 'error':''}";
          }
          else {
            // update text only
            // bbcode processing
            if ($nc_comments->isBBcodes()) $commentText = nc_bbcode($value['Comment']);
            $resArr[] = "{'id':'".$value['id']."', 'parent_id':'".$value['Parent_Comment_ID']."', 'commentHTML':escape(\"".nl2br(htmlspecialchars_decode($commentText))."\"), 'update':'1', 'updated':'".$value['LastUpdated']."', 'error':''}";
          }
        }
        else {
          $commentHTML = $nc_comments->getComment($message_id, $value, $template_id, false);
          // past comment text
          $commentHTML = str_replace("%COMMENT_".$message_cc."_".$message_id."_".$value['id']."%", nl2br($value['Comment']), $commentHTML);
          // bbcode processing
          if ($nc_comments->isBBcodes()) $commentHTML = nc_bbcode($commentHTML);
          $resArr[] = "{'id':'".$value['id']."', 'parent_id':'".$value['Parent_Comment_ID']."', 'commentHTML':escape(\"".$nc_comments->commentValidateShow($commentHTML, $template_id)."\"), 'updated':'".$value['LastUpdated']."', 'edit_rule':'".$nc_comments->getEditRule()."', 'delete_rule':'".$nc_comments->getDeleteRule()."', 'error':''}";
        }
      }
    }
    // edit
    if ($comment_edit==1) {
      // check update possibility
      try {
        $nc_comments->updateComment($comment_id, $comment);
        $commentData = $nc_comments->getCommentFromArray($comment_id);
        $LastUpdated = $commentData['Updated'] > $commentData['Data'] ? $commentData['Updated'] : $commentData['Data'];
        //echo nl2br($comment);
        $resArr[] = "{'id':'".$comment_id."', 'parent_id':'".$parent_mess_id."', 'commentHTML':escape(\"".nl2br($nc_comments->isBBcodes() ? nc_bbcode($commentData['Comment']) : $commentData['Comment'])."\"), 'update':'1', 'updated':'".$LastUpdated."', 'error':'0'}";
      }
      catch (Exception $e) {
        die("{'error':'".$e->getMessage()."'}");
      }
    }
    // edit get info
    if ($comment_edit==2) {
      // check update possibility
      try {
        $comment = $nc_comments->getCommentFromArray($comment_id);
        $resArr[] = "{'id':'".$comment_id."', 'parent_id':'".$parent_mess_id."', 'commentHTML':escape(\"".$nc_comments->commentValidateShow(htmlspecialchars_decode($comment['Comment']), $template_id)."\"), 'update':'2', 'updated':'".$comment['Updated']."', 'error':'0'}";
      }
      catch (Exception $e) {
        die("{'error':'".$e->getMessage()."'}");
      }
    }
    // delete
    if ($comment_edit==-1) {
      // check delete possibility
      try {
        // get comment data from array
        $commentData = $nc_comments->getCommentFromArray($comment_id);
        // drop from base
        $nc_comments->deleteComment($comment_id);
        // drop from DOM
        $resArr[] = "{'id':'".$comment_id."', 'update':'-1', 'error':'0'}";
        
        // refresh parent block
        // not need it for moderators, they may see all type of links
        if ( ($nc_comments->getEditRule()=="unreplied" || $nc_comments->getDeleteRule()=="unreplied") && !$nc_comments->isModerator() ) {
          // if children exist not need to refresh
          if ( !$nc_comments->getChildren($commentData['Parent_Comment_ID']) ) {
            // get parent comment data from array
            $parentCommentData = $nc_comments->getCommentFromArray($commentData['Parent_Comment_ID']);
            // drop from DOM
            $resArr[] = "{'id':'".$commentData['Parent_Comment_ID']."', 'update':'-1', 'error':'0'}";
            // get parent refreshaed block
            $commentHTML = $nc_comments->getComment($message_id, $parentCommentData, $template_id, false);
            // past parent comment block
            $commentHTML = str_replace("%COMMENT_".$message_cc."_".$message_id."_".$parentCommentData['id']."%", nl2br($parentCommentData['Comment']), $commentHTML);
            // bbcode processing
            if ($nc_comments->isBBcodes()) $commentHTML = nc_bbcode($commentHTML);
            $resArr[] = "{'id':'".$parentCommentData['id']."', 'parent_id':'".$parentCommentData['Parent_Comment_ID']."', 'commentHTML':escape(\"".$nc_comments->commentValidateShow($commentHTML, $template_id)."\"), 'updated':'".$parentCommentData['LastUpdated']."', 'edit_rule':'".$nc_comments->getEditRule()."', 'delete_rule':'".$nc_comments->getDeleteRule()."', 'error':''}";
          }
        }
      }
      catch (Exception $e) {
        die("{'error':'".$e->getMessage()."'}");
      }
    }
    
    $all_comments = $nc_comments->getCommentFromArray();
    if ( !empty($all_comments) ) {
      foreach ($all_comments AS $comment_data) {
        $all_comments_id[] = $comment_data['id'];
      }
      // all comments IDs in this wall
      $resArr[] = "{'all_comments_id':[".join(", ", $all_comments_id)."]}";
    }
    
    // json result
    if ( !empty($resArr) ) {
      $res = "[".join(",", $resArr)."]";
    }
  
  }
  else {
    eval("\$templateData['Warn_Text'] = str_replace(\"%WARNTEXT\", NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_PARENT, \$templateData['Warn_Text']);");
    die("{'error':'enter', 'unset':escape(\"".$nc_comments->commentValidateShow($templateData['Warn_Text'], $template_id, 0, 1)."\")}");
  }

}

if (isset($_POST['redirect_url'])) {
    header('Location: ' . $_POST['redirect_url']);
    exit();
}

// return json result from ajax
echo $res;


?>