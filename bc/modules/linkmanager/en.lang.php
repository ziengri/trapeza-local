<?php

/* $Id: en.lang.php 6265 2012-02-21 13:53:01Z nastya $ */
define("NETCAT_MODULE_LINKS_RULES_PAGE", "<p>Your site may not contain illegal content, pornography, sex-service adverts.
<p>Link exchange with sites on free hostings and sites with citation index higher than 10 is impossible.
<p>Site must be in Russian.
<p>Site must match the chosen cathegory, there may not be more than 20 links on one page.
<p>Site must contain links to named sites. Link format is described on <a href=../codes/>Our links codes</a> page.
<p>Befor filling the form, put the links to our site or to sites with which you want to make a direct exgange on your site.");

define("NETCAT_MODULE_LINKS_DESCRIPTION", "At presence of the back link: raising to the link to the top of the list.");

define('NETCAT_MODULE_LINKS_REPORT_STAT', '
         Links in database: <b>%s</b>. From them:<br>
         <li> turned off: <b>%s</b>
         <li> in redirect mode: <b>%s</b>
       ');

define('NETCAT_MODULE_LINKS_REPORT_LAST_CHECK', '
     Last check: <b>$data[Last_Process]</b>. As a result of its work there have been:
     <li> turned off: <b>$data[Last_Unchecked]</b> links
     <li> turned on: <b>$data[Last_Checked]</b> links
     <li> deleted: <b>$data[Last_Deleted]</b> links');

define('NETCAT_MODULE_LINKS_START_CHECKUP', 'Start check');
define('NETCAT_MODULE_LINKS_NO_LINK', "(no link)");
define('NETCAT_MODULE_LINKS_NO_DOMAIN', 'You must specify domain name in the settings of site %s\n');
define('NETCAT_MODULE_LINKS_MAIL_SUBJ_PROCESSING', "[NetCat] processing of links");
define('NETCAT_MODULE_LINKS_ACHTUNG', 'ATTENTION!');
define('NETCAT_MODULE_LINKS_REPORT_MAKE_AND_SET', 'Generation and sending of the report on email');
define('NETCAT_MODULE_LINKS_REPORT_DISABLED', 'The following sold links are switched off');
define('NETCAT_MODULE_LINKS_CHECKUP_DONE', 'Check is completed.');

define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TEMPLATE', '


Results of check of links on your project
From the time of last check (%s) have been added:

- turned on links:
%s
- turned off links:
%s
During check have been:

- turned off links:
%s
- turned on links:
%s
- deleted links:
%s

Log:
%s

Truly yours,
Links manager module');

define('NETCAT_MODULE_LINKS_ERROR_LINKS_TO_OTHER_SITES', "There should not be links to other sites in HTML-code of the link.");
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_SAME_SITE', 'The back link should be located on the same site, that you have specified.');
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_OTHER_SITE', "The back link should be located not on your site.");
define('NETCAT_MODULE_LINKS_ERROR_DUPLICATE_BACK_LINK', 'The back link from this site already is in our datebase.');
define('NETCAT_MODULE_LINKS_ERROR_NOT_2ND_LEVEL_DOMAIN', "The back link is located not on the second level domain (for example, site.ru).");
define('NETCAT_MODULE_LINKS_ERROR_DOMAIN_IN_STOP_LIST', 'The back link is located on the domain which is included in stop-list of this site. Contact the administrator please.');
define('NETCAT_MODULE_LINKS_ADDED', 'Your link has been added!');
define('NETCAT_MODULE_LINKS_MODE', 'Operating mode of the module');
define('NETCAT_MODULE_LINKS_BACK_LINK_REQUIRED', 'back link is obligatory');
define('NETCAT_MODULE_LINKS_REDIRECT_IF_NO_LINK', 'redirect at absence of the back link');
define('NETCAT_MODULE_LINKS_WHEN_NO_BACK_LINK', 'At absence of the back link');
define('NETCAT_MODULE_LINKS_DISABLE_LINK', 'turn off the link');
define('NETCAT_MODULE_LINKS_DELETE_LINK', 'delete the link');
define('NETCAT_MODULE_LINKS_DELETE_DISABLED_LINKS_IN', 'Delete turned off links');
define('NETCAT_MODULE_LINKS_IN_DAYS', 'days');
define('NETCAT_MODULE_LINKS_WHEN_BACK_LINK', 'At presence of the back link');
define('NETCAT_MODULE_LINKS_DIRECT_LINK', 'the link to a partner site is direct link');
define('NETCAT_MODULE_LINKS_DONT_REMOVE_TAGS', 'do not clean tags in the HTML-code of link');
define('NETCAT_MODULE_LINKS_MOVE_TO_TOP', 'raising tof the link to the top of the list');
define('NETCAT_MODULE_LINKS_CAN_MAKE_DIRECT_LINK_EVERY', 'Send email to the partner on an opportunity to place the direct link every');
define('NETCAT_MODULE_LINKS_EVERY_DAYS_NUL', 'days (zero days - don&#039;t send)');
define('NETCAT_MODULE_LINKS_LINK_CHECK', 'Link Check');
define('NETCAT_MODULE_LINKS_CHECK_ON_PARTNER_SITE', 'Check presence on a partner site');
define('NETCAT_MODULE_LINKS_CHECK_FULL_TEXT', 'full text of the link');
define('NETCAT_MODULE_LINKS_CHECK_LINK_ONLY', 'only presence of the link');
define('NETCAT_MODULE_LINKS_BACK_LINK_IS_ON', 'The back link should be located');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_LINKED_SITE', 'on that site where we refer');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_OTHER_SITE', 'on other site');
define('NETCAT_MODULE_LINKS_BACK_LINK_ANYWHERE', 'does not matter');
define('NETCAT_MODULE_LINKS_DISALLOW_DUPLICATE_BACK_LINKS', 'do not resolve addition if the back link from this site already is present at datebase');
define('NETCAT_MODULE_LINKS_DISALLOW_LINKS_TO_OTHER_SITE', 'do not resolve addition if there are links not on the specified site at the text of the link');
define('NETCAT_MODULE_LINKS_DISALLOW_NOT_2ND_LEVEL_DOMAINS', 'do not resolve addition if the back link is located not on the second level domain');
define('NETCAT_MODULE_LINKS_EMAIL_SEND', 'Sending of emails');
define('NETCAT_MODULE_LINKS_EMAIL_ROBOT_ADDRESS', 'The email address of the sender of automatically generated links');
define('NETCAT_MODULE_LINKS_EMAIL_ADMIN_ADDRESS', 'The email address of the administrator for departure of automatically generated letters');
define('NETCAT_MODULE_LINKS_SAVE_CHANGES', 'Save changes');
define('NETCAT_MODULE_LINKS_CHANGES_SAVED', 'Changes have been saved');
define('NETCAT_MODULE_LINKS_CANCEL', 'Cancel');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_LINK_ADD', 'Send email to administrator if new links added (a theme and a pattern of the email)');
define('NETCAT_MODULE_LINKS_LINK_REQUIRED_MODE', 'Mode of obligatory presence of the link');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ABSENT', 'Send email to partner if the link added (no back link)');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_PRESENT', 'Send email to partner if the link added (back link presents)');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DISABLE', 'Send email to partner if the link de-energized automatically');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ENABLE', 'Send email to partner at automatic inclusion the link');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DELETE', 'Send email to partner at automatic removal the link');
define('NETCAT_MODULE_LINKS_REDIRECT_MODE', 'Direction mode at absence of the link');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_ON', 'Send email to partner at automatic inclusion of a mode of a redirect');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_OFF', 'Send email to partner at automatic de-energizing of a mode of a redirect');
define('NETCAT_MODULE_LINKS_BUY_AND_SELL', 'Purchase/sale of links');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_PURCHASED_LINK_ABSENT', 'Send email to administrator about disappearance of the bought link');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_PURCHASED_LINK_DISABLE', 'Send email to partner about de-energizing the sold link');
define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TO_ADMIN', 'send email to administrator the report on check (the pattern is not adjusted)');

define('NETCAT_MODULE_LINKS_STATS', 'Statistics');
define('NETCAT_MODULE_LINKS_SETTINGS', 'Settings');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATES', 'Email templates');

define('NETCAT_MODULE_LINKS_TURNED_OFF', 'disabled');
define('NETCAT_MODULE_LINKS_DO_SEARCH', 'search');
define('NETCAT_MODULE_LINKS_CODE_VARIANTS', "You can put one of the following links on your site:");
define('NETCAT_MODULE_LINKS_CODE_NO_VARIANTS', "HTML-code to put on your site:");
define('NETCAT_MODULE_LINKS_LINK_PREVIEW', 'Link preview:');
define('NETCAT_MODULE_LINKS_LINK_HTML', 'HTML-code:');
define('NETCAT_MODULE_LINKS_DISABLE_DATE', 'will be disabled on');
define('NETCAT_MODULE_LINKS_PURCHASED', 'purchased till');

define('NETCAT_MODULE_LINKS_SUB', 'Link exchange');
define('NETCAT_MODULE_LINKS_CODES_SUB', 'Link codes');
define('NETCAT_MODULE_LINKS_SOLD_SUB', 'Sold links');
define('NETCAT_MODULE_LINKS_PURCHASED_SUB', 'Purchased links');
define('NETCAT_MODULE_LINKS_STOPLIST_SUB', 'Stoplist');
define('NETCAT_MODULE_LINKS_RULES', 'Rules');

define('NETCAT_MODULE_LINKS_CODES2', 'Link codes');
define('NETCAT_MODULE_LINKS_ADD_SITE', 'add site');
define('NETCAT_MODULE_LINKS_GO_UP', 'Level up');
?>