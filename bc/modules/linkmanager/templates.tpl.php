<form action="admin.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="phase" value="2">
    <input type="hidden" name="page" value="templates">

    <input type="checkbox" name="lm_admin_added" value="1"<?php  echo ($lm_set["Send_Admin_Added"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_LINK_ADD ?>:<br>
    <input type=text size="100" name="lm_admin_added_s" value="<?php  echo $lm_set["Send_Admin_Added_Subject"]; ?>"><br>
    <textarea rows="5" cols="60" name="lm_admin_added_t"><?php  echo $lm_set["Send_Admin_Added_Template"]; ?></textarea>
    <?= nc_mail_attachment_form('linkmanager_admin_added'); ?>
    <br><br><br>
    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_LINK_REQUIRED_MODE ?></legend>

        <input type="checkbox" name="lm_partner_bl" value="1"<?php  echo ($lm_set["Send_Partner_Added_Bad_Link"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ABSENT ?>:<br>
        <input type=text size="100" name="lm_partner_bl_s" value="<?php  echo $lm_set["Send_Partner_Added_Bad_Link_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_bl_t"><?php  echo $lm_set["Send_Partner_Added_Bad_Link_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_added_bad_link'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_gl" value="1"<?php  echo ($lm_set["Send_Partner_Added_Good_Link"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_PRESENT ?>:<br>
        <input type=text size="100" name="lm_partner_gl_s" value="<?php  echo $lm_set["Send_Partner_Added_Good_Link_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_gl_t"><?php  echo $lm_set["Send_Partner_Added_Good_Link_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_added_good_link'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_turnoff" value="1"<?php  echo ($lm_set["Send_Partner_Turnoff"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DISABLE ?>:<br>
        <input type=text size="100" name="lm_partner_turnoff_s" value="<?php  echo $lm_set["Send_Partner_Turnoff_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_turnoff_t"><?php  echo $lm_set["Send_Partner_Turnoff_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_turnoff'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_turnon" value="1"<?php  echo ($lm_set["Send_Partner_Turnon"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ENABLE ?>:<br>
        <input type=text size="100" name="lm_partner_turnon_s" value="<?php  echo $lm_set["Send_Partner_Turnon_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_turnon_t"><?php  echo $lm_set["Send_Partner_Turnon_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_turnon'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_kill" value="1"<?php  echo ($lm_set["Send_Partner_Kill"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DELETE ?>:<br>
        <input type=text size="100" name="lm_partner_kill_s" value="<?php  echo $lm_set["Send_Partner_Kill_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_kill_t"><?php  echo $lm_set["Send_Partner_Kill_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_kill'); ?>

    </fieldset>

    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_REDIRECT_MODE ?></legend>

        <input type="checkbox" name="lm_partner_redirect" value="1"<?php  echo ($lm_set["Send_Partner_Added_Redirect_Link"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ABSENT ?>:<br>
        <input type=text size="100" name="lm_partner_redirect_s" value="<?php  echo $lm_set["Send_Partner_Added_Redirect_Link_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_redirect_t"><?php  echo $lm_set["Send_Partner_Added_Redirect_Link_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_added_redirect_link'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_direct" value="1"<?php  echo ($lm_set["Send_Partner_Added_Direct_Link"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_PRESENT ?>:<br>
        <input type=text size="100" name="lm_partner_direct_s" value="<?php  echo $lm_set["Send_Partner_Added_Direct_Link_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_direct_t"><?php  echo $lm_set["Send_Partner_Added_Direct_Link_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_added_direct_link'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_redirect_on" value="1"<?php  echo ($lm_set["Send_Partner_Redirect_On"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_ON ?>:<br>
        <input type=text size="100" name="lm_partner_redirect_on_s" value="<?php  echo $lm_set["Send_Partner_Redirect_On_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_redireсt_on_t"><?php  echo $lm_set["Send_Partner_Redirect_On_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_redirect_on'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_redirect_off" value="1"<?php  echo ($lm_set["Send_Partner_Redirect_Off"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_OFF ?>:<br>
        <input type=text size="100" name="lm_partner_redirect_off_s" value="<?php  echo $lm_set["Send_Partner_Redirect_Off_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_redireсt_off_t"><?php  echo $lm_set["Send_Partner_Redirect_Off_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_redirect_off'); ?>

    </fieldset>

    <fieldset>
        <legend><?=NETCAT_MODULE_LINKS_BUY_AND_SELL ?></legend>

        <input type="checkbox" name="lm_admin_no_purchased" value="1"<?php  echo ($lm_set["Send_Admin_No_Purchased"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_PURCHASED_LINK_ABSENT ?>:<br>
        <input type=text size="100" name="lm_admin_no_purchased_s" value="<?php  echo $lm_set["Send_Admin_No_Purchased_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_admin_no_purchased_t"><?php  echo $lm_set["Send_Admin_No_Purchased_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_admin_no_purchased'); ?>

        <br><br>
        <input type="checkbox" name="lm_partner_sold_off" value="1"<?php  echo ($lm_set["Send_Partner_Sold_Turnoff"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_PURCHASED_LINK_DISABLE ?>:<br>
        <input type=text size="100" name="lm_partner_sold_off_s" value="<?php  echo $lm_set["Send_Partner_Sold_Turnoff_Subject"]; ?>"><br>
        <textarea rows="5" cols="60" name="lm_partner_sold_off_t"><?php  echo $lm_set["Send_Partner_Sold_Turnoff_Template"]; ?></textarea>
        <?= nc_mail_attachment_form('linkmanager_partner_sold_turnoff'); ?>

    </fieldset>
    <p>
        <input type="checkbox" name="lm_admin_report" value="1"<?php  echo ($lm_set["Send_Admin_Report"]) ? " checked" : "" ?>>
<?=NETCAT_MODULE_LINKS_REPORT_EMAIL_TO_ADMIN ?>

        <?php 
        $UI_CONFIG->actionButtons[] = array("id" => "submit",
                "caption" => NETCAT_MODULE_LINKS_SAVE_CHANGES,
                "action" => "mainView.submitIframeForm()"
        );
        ?>
</form>