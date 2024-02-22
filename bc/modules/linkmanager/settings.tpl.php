<form action="admin.php" method="post">
    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_MODE ?></legend>
        <table border="0" cellpadding="6" cellspacing="0" width="100%">
            <input type="hidden" name="phase" value="2">
            <input type="hidden" name="page" value="settings">
            <tbody>
                <tr>
                    <td colspan=2 width=50%>
                        <input type="radio" name="lm_mode" value=1<?php  echo ($lm_set["Back_Link_Needed"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_BACK_LINK_REQUIRED ?>
                    </td>
                    <td colspan=2 width=50%>
                        <input type="radio" name="lm_mode" value=2<?php  echo (!$lm_set["Back_Link_Needed"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_REDIRECT_IF_NO_LINK ?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td valign="top">
  <?=NETCAT_MODULE_LINKS_WHEN_NO_BACK_LINK ?>:<br>
                        <input type="radio" name="lm_kill_bl" value=1<?php  echo (!$lm_set["Kill_Bad_Link"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_DISABLE_LINK ?><br>
                        <input type="radio" name="lm_kill_bl" value=2<?php  echo ($lm_set["Kill_Bad_Link"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_DELETE_LINK ?>
                        <p>
  <?=NETCAT_MODULE_LINKS_DELETE_DISABLED_LINKS_IN ?> <input type="text" size="3" name="lm_kill_bl_in" value="<?php  echo ($lm_set["Kill_Bad_Link_In"]) ? $lm_set["Kill_Bad_Link_In"] : "3" ?>">
  <?=NETCAT_MODULE_LINKS_IN_DAYS ?>
                    </td>
                    <td>&nbsp;</td>
                    <td valign="top">
  <?=NETCAT_MODULE_LINKS_WHEN_BACK_LINK ?>:<br>
                        <input type="checkbox" name="lm_direct_gl" value="1"<?php  echo ($lm_set["Direct_For_Good_Link"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_DIRECT_LINK ?><br>
                        <input type="checkbox" name="lm_html_gl" value="1"<?php  echo ($lm_set["HTML_In_Good_Link"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_DONT_REMOVE_TAGS ?><br>
                        <input type="checkbox" name="lm_putup_gl" value="1"<?php  echo ($lm_set["Put_Up_Good_Link"]) ? " checked" : "" ?>>
  <?=NETCAT_MODULE_LINKS_MOVE_TO_TOP ?>
                        <p>
  <?=NETCAT_MODULE_LINKS_CAN_MAKE_DIRECT_LINK_EVERY ?> <input type="text" size="3" name="lm_invite_in" value="<?php  echo ($lm_set["Invite_Partner_In"]) ? $lm_set["Invite_Partner_In"] : "0" ?>">
  <?=NETCAT_MODULE_LINKS_EVERY_DAYS_NUL ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>

    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_LINK_CHECK ?></legend>
        <table width="100%" border="0">
            <tr>
                <td valign="top" width=50%>
<?=NETCAT_MODULE_LINKS_CHECK_ON_PARTNER_SITE ?>:<br>
                    <input type="radio" name="lm_check_whole" value=1<?php  echo ($lm_set["Check_Whole_Text"]) ? " checked" : "" ?>> <?=NETCAT_MODULE_LINKS_CHECK_FULL_TEXT ?><br>
                    <input type="radio" name="lm_check_whole" value=2<?php  echo (!$lm_set["Check_Whole_Text"]) ? " checked" : "" ?>> <?=NETCAT_MODULE_LINKS_CHECK_LINK_ONLY ?>
                </td>
                <td valign="top" width=50%>
<?=NETCAT_MODULE_LINKS_BACK_LINK_IS_ON ?>:<br>
                    <input type="radio" name="lm_back_link_at" value=1<?php  echo ($lm_set["Back_Link_At_Site"] == 1) ? " checked" : "" ?>> <?=NETCAT_MODULE_LINKS_BACK_LINK_ON_LINKED_SITE ?><br>
                    <input type="radio" name="lm_back_link_at" value=2<?php  echo ($lm_set["Back_Link_At_Site"] == 2) ? " checked" : "" ?>> <?=NETCAT_MODULE_LINKS_BACK_LINK_ON_OTHER_SITE ?><br>
                    <input type="radio" name="lm_back_link_at" value=3<?php  echo ($lm_set["Back_Link_At_Site"] != 1 && $lm_set["Back_Link_At_Site"] != 2) ? " checked" : "" ?>> <?=NETCAT_MODULE_LINKS_BACK_LINK_ANYWHERE ?>
                </td></tr></table>
        <p>
            <input type="checkbox" name="lm_fail_if_present" value="1"<?php  echo ($lm_set["Fail_If_Back_Link_Present"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_DISALLOW_DUPLICATE_BACK_LINKS ?>
        <p>
            <input type="checkbox" name="lm_fail_if_many" value="1"<?php  echo ($lm_set["Fail_If_Many_Hosts"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_DISALLOW_LINKS_TO_OTHER_SITE ?>
        <p>
            <input type="checkbox" name="lm_fail_if_third" value="1"<?php  echo ($lm_set["Fail_If_Third_Level"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_DISALLOW_NOT_2ND_LEVEL_DOMAINS ?>

    </fieldset>

    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_EMAIL_SEND ?></legend>

<?=NETCAT_MODULE_LINKS_EMAIL_ROBOT_ADDRESS ?>:<br>
        <input type="text" size="25" name="lm_spam_from" value="<?php  echo $lm_set["Spam_From"]; ?>">
        <p>
<?=NETCAT_MODULE_LINKS_EMAIL_ADMIN_ADDRESS ?>:<br>
            <input type="text" size="25" name="lm_admin_mail" value="<?php  echo $lm_set["Admin_Mail"]; ?>">

    </fieldset>
    <?php 
            $UI_CONFIG->actionButtons[] = array("id" => "submit",
                    "caption" => NETCAT_MODULE_LINKS_SAVE_CHANGES,
                    "action" => "mainView.submitIframeForm()"
            );
    ?>
</form>