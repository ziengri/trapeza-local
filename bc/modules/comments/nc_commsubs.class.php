<?php

class nc_commsubs {
    protected $db;

    public function __construct () {
        global $db;
        $this->db = $db;
    }
    public function is_subscribe ( $user_id, $cc_id, $message_id, $comment_id = 0 ) {
        return $this->db->get_var("SELECT `ID` FROM `Comments_Subscribe`
                                   WHERE `Sub_Class_ID` = '".intval($cc_id)."' AND
                                         `Message_ID` = '".intval($message_id)."' AND
                                         `User_ID` = '".intval($user_id)."' AND
                                         `Comment_ID` = '0' ");
    }

    public function unsubscribe ( $user_id, $cc_id, $message_id, $comment_id = 0 ) {
        return $this->db->get_var("DELETE FROM `Comments_Subscribe`
                                   WHERE `Sub_Class_ID` = '".intval($cc_id)."' AND
                                         `Message_ID` = '".intval($message_id)."' AND
                                         `User_ID` = '".intval($user_id)."' AND
                                         `Comment_ID` = '0' ");
    }

    public function new_comment( $comment_id, $mail_template, $subject ) {

      $nc_core = nc_Core::get_object();
      $system_env = $nc_core->get_settings();

      $comment = $this->db->get_row("SELECT ct.*,
                                            sub.Subdivision_ID, sub.Subdivision_Name,
                                            cs.Sub_Class_ID, cs.Sub_Class_Name, cs.Class_ID
                                            FROM `Comments_Text` as `ct`, `Subdivision` as `sub`, `Sub_Class` as `cs`
                                             WHERE `id` = '".intval($comment_id)."'
                                             AND ct.Sub_Class_ID = cs.Sub_Class_ID
                                             AND cs.Subdivision_ID = sub.Subdivision_ID", ARRAY_A);
      if ( !$comment ) return false;
      $subscribes = $this->db->get_results("SELECT cs.`User_ID`, u.`".$system_env['UserEmailField']."` as `email`
                                            FROM `Comments_Subscribe` as `cs`,
                                            `User` AS `u`
                                            WHERE cs.`Sub_Class_ID` = '".$comment['Sub_Class_ID']."'
                                            AND cs.`Message_ID` = '".$comment['Message_ID']."'
                                            AND cs.`Comment_ID` = 0
                                            AND cs.`User_ID` <> '".$comment['User_ID']."'
                                            AND u.`User_ID` = cs.`User_ID` ", ARRAY_A);
      $mailer = new CMIMEMail();

      /* доступные переменные  */
      $comment_text = str_replace('"', '&quot;', $comment['Comment']);
      $comment_text = str_replace( array('$','{', '}'), '', $comment_text );
      $comment_id;
      $message_id = $comment['Message_ID'];
      $subdivision_name = $comment['Subdivision_Name'];
      $subdivision_id  = $comment['Subdivision_ID'];
      $sub_class_name = $comment['Sub_Class_Name'];
      $sub_class_id = $comment['Sub_Class_ID'];
      $fullLink = "http://".$_SERVER['HTTP_HOST'].nc_message_link($comment['Message_ID'], $comment['Class_ID'] );

      $subject = str_replace( array("%SUBDIVISION_NAME", "%COMMENT_TEXT", "%FULL_LINK"), array($subdivision_name, $comment_text, $fullLink), $subject);
      $mail_template = str_replace( array("%SUBDIVISION_NAME", "%COMMENT_TEXT", "%FULL_LINK"), array($subdivision_name, $comment_text, $fullLink), $mail_template);

      $mail_template = nc_mail_attachment_attach($mailer, $mail_template, 'comments_subscribe');

      if ( !empty($subscribes) ) foreach ( $subscribes as $v ) {
        $user_id = $v['User_ID'];
        eval ("\$result = \"".$subject."\";");
        $subject = $result;

        eval ("\$result = \"".$mail_template."\";");

        $mailer->mailbody($result);
        $mailer->send($v['email'], $system_env['SpamFromEmail'], $system_env['SpamFromEmail'], $subject, $system_env['SpamFromName']);
      }
    }
}