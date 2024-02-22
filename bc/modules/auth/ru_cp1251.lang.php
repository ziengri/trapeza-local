<?php

global $ADMIN_PATH;

define("NETCAT_MODULE_AUTH_DESCRIPTION", "������ ��� ������ � ��������������. ����������� ����������� ������� ������ �������������, ��������� ����������� ������, ������, �������������� ������. ������ ������ ����� ��������������� � ������� �������� �������.");
define("NETCAT_MODULE_AUTH_REG_OK", "����������� ������������.");
define("NETCAT_MODULE_AUTH_REG_ERROR", "������! ����������� �� ������������.");
define("NETCAT_MODULE_AUTH_REG_INVALIDLINK", "������������ ������.");
define("NETCAT_MODULE_AUTH_ERR_NEEDAUTH", "���������� ��������������.");
define("NETCAT_MODULE_AUTH_CHANGEPASS_NOTEQUAL", "������ �� ���������. ���������� ��� ���.");
define("NETCAT_MODULE_AUTH_ERR_NOFIELDSET", "���� ��� Email �� ����������.");
define("NETCAT_MODULE_AUTH_ERR_NOUSERFOUND", "������������ �� ������");
define("NETCAT_MODULE_AUTH_MSG_FILLFIELD", "��������� ���� �� �����");
define("NETCAT_MODULE_AUTH_MSG_BADEMAIL", "������������ E-mail");
define("NETCAT_MODULE_AUTH_MSG_NEWPASSSENDED", "�������������� ������ ���� ������� �� ��� email. ��� ����������� ��������� �������������� ������ �������� ������, ����������� � ������.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_LOGIN_FORMAT", "�������� ������ ���� &laquo;�����&raquo;, ����������� �����, �����, ���� �������������, ����� ��� ������.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_EMAIL_FORMAT", "�������� ������ ���� &laquo;Email&raquo;, ����������� �����, �����, ���� �������������, ����� � �����.");
define("NETCAT_MODULE_AUTH_NEWPASS_SUCCESS", "������ ������� �������.");
define("NETCAT_MODULE_AUTH_NEWPASS_ERROR", "������ ��� ��������� ������.");

define("NETCAT_MODULE_AUTH_FORM_AND_MAIL_TEMPLATES", "������� ���� � �����");
define("NETCAT_MODULE_AUTH_EXTERNAL_AUTH", "����������� ����� ������� �������");

define("NETCAT_MODULE_AUTH_LOGIN", "�����");
define("NETCAT_MODULE_AUTH_ENTER", "����");
define("NETCAT_MODULE_AUTH_REGISTER", "������������������");
define("NETCAT_MODULE_AUTH_INCORRECT_LOGIN_OR_RASSWORD", "������� ������ ����� ��� ������");
define("NETCAT_MODULE_AUTH_AUTHORIZATION_UPPER", "�����������");
define("NETCAT_MODULE_AUTH_AUTHORIZATION", "�����������");
define("NETCAT_MODULE_AUTH_FORGOT", "������?");
define("NETCAT_MODULE_AUTH_PASSWORD", "������");
define("NETCAT_MODULE_AUTH_PASSWORD_CONFIRMATION", "������� ������ ��� ���");
define("NETCAT_MODULE_AUTH_FIRST_NAME", "���");
define("NETCAT_MODULE_AUTH_LAST_NAME", "�������");
define("NETCAT_MODULE_AUTH_NICKNAME", "���");
define("NETCAT_MODULE_AUTH_PHOTO", "����������");
define("NETCAT_MODULE_AUTH_SAVE", "��������� ����� � ������");
define("NETCAT_MODULE_AUTH_REMEMBER_ME", "��������� ����");
define("NETCAT_MODULE_AUTH_NOT_NEW_MESSAGE", "����� ��������� ���");
define("NETCAT_MODULE_AUTH_NEW_MESSAGE", "����� ���������");
define("NETCAT_MODULE_AUTH_HELLO", "������������");
define("NETCAT_MODULE_AUTH_LOGOUT", "��������� �����");
define("NETCAT_MODULE_AUTH_BY_TOKEN", "����� �� ������");

define("NETCAT_MODULE_AUTH_LOGIN_WAIT", "����������, ���������");
define("NETCAT_MODULE_AUTH_LOGIN_FREE", "����� ��������");
define("NETCAT_MODULE_AUTH_LOGIN_BUSY", "����� �����");
define("NETCAT_MODULE_AUTH_LOGIN_INCORRECT", "����� �������� ����������� �������");

define("NETCAT_MODULE_AUTH_PASS_LOW", "������");
define("NETCAT_MODULE_AUTH_PASS_MIDDLE", "�������");
define("NETCAT_MODULE_AUTH_PASS_HIGH", "�������");
define("NETCAT_MODULE_AUTH_PASS_VHIGH", "����� �������");
define("NETCAT_MODULE_AUTH_PASS_EMPTY", "������ �� ����� ���� ������");
define("NETCAT_MODULE_AUTH_PASS_SHORT", "������ ������� ��������");

define("NETCAT_MODULE_AUTH_PASS_COINCIDE", "������ ���������");
define("NETCAT_MODULE_AUTH_PASS_N_COINCIDE", "������ �� ���������");

define("NETCAT_MODULE_AUTH_PASS_RELIABILITY", "���������:");

define("NETCAT_MODULE_AUTH_CP_NEWPASS", "����� ������");
define("NETCAT_MODULE_AUTH_CP_CONFIRM", "��������� ���� ������");
define("NETCAT_MODULE_AUTH_CP_DOBUTT", "������� ������");

define("NETCAT_MODULE_AUTH_PRF_LOGIN", "������� �����");
define("NETCAT_MODULE_AUTH_PRF_EMAIL", "��� Email");
define("NETCAT_MODULE_AUTH_PRF_EMAIL_2", "Email");
define("NETCAT_MODULE_AUTH_PRF_DOBUTT", "������������ ������");

define("NETCAT_MODULE_AUTH_BUT_AUTORIZE", "��������������");
define("NETCAT_MODULE_AUTH_BUT_BACK", "���������");
define("NETCAT_MODULE_AUTH_MSG_AUTHISOK", "����������� ������ �������.");
define("NETCAT_MODULE_AUTH_MSG_AUTHUPISOK", "����� ��������.");

define('NETCAT_MODULE_AUTH_MSG_SESSION_CLOSED', '����� ��������. <a href=\'%s\'>���������</a>');
define('NETCAT_MODULE_AUTH_MSG_AUTH_SUCCESS', '����������� ������ �������. <a href=\'%s\'>���������</a>');

define('NETCAT_MODULE_AUTH_ADMIN_MAIN_SETTINGS_TITLE', '�������� ���������');
define("NETCAT_MODULE_AUTH_ADMIN_SAVE_OK", "��������� ������� ��������");

define("NETCAT_MODULE_AUTH_ADMIN_INFO", "�� ������ ��������� ����������� ������ �������������, � ����� ��������� ������� \"������������\" � ������� \"<a href=" . $ADMIN_PATH . "field/system.php>��������� �������</a>\".<br/>");

// �������� ��������
define("NETCAT_MODULE_AUTH_ADMIN_TAB_INFO", "����������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_REGANDAUTH", "����������� � �����������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_TEMPLATES", "������� ������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_MAIL", "������� �����");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SETTINGS", "���������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_CLASSIC", "�� ������ � ������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_EXAUTH", "����� ������� �������");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_GENERAL", "�����");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SYSTEM", "���������");

// ����������
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT", "����� ���������� �������������");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCHECKED", "����������� �������������");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCONFIRMED", "���������� �������������, ��� �� ������������� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_NONE", "���");

// �� ������ � ������
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_REG", "��������� ��������������� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_RECOVERY", "��������� �������������� ��������������� ������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CYRILLIC", "��������� ��������� � �������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_SPECIALCHARS", "��������� ����������� � �������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CHANGE_LOGIN", "��������� ������ ����� ����� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_BING_TO_CATALOGUE", "����������� ������������ � �����");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_WITH_SUBDOMAIN", "�������������� �� ����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTH_CAPTCHA", "��������� CAPTCHA ��� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PASS_MIN", "����������� ����� ������ %input ��������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_REGISTRATION_FORM", "����� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_LOGIN", "������������� ��������� ����������� ������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS", "������������� ��������� ������� ������������ ������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS2", "������������� ��������� ���������� �������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_AGREED", "��������� ���������� � ���������������� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM", "���� � ����� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_ALL", "���");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_CUSTOM", "���������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ACTIVATION", "���������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM", "��������� ������������� ����� �����");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_AFTER_MAIL", "���������� �������������� ������ ����� ��������� ������������� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PREMODARATION", "������������ ���������������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_NOTIFY_ADMIN", "���������� ������ �������������� ��� ����������� ������������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTHAUTORIZE", "����������� ������������ ����� ����� �������������");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_TIME", "������� ������������, ���� �� �� ���������� ����������� � ������� %input �����");

// ����� ������� �������
define("NETCAT_MODULE_AUTH_ADMIN_EX_CURL_REQUIRED", "��� ����������� ����� ������� ������� ���������� ���������� <a href='http://www.php.net/manual/ru/book.curl.php'>cURL</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_JSON_REQUIRED", "��� ����������� ����� ������� ������� ���������� ���������� <a href='http://ru2.php.net/manual/en/book.json.php'>JSON</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK", "���������");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB", "Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER", "Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID", "OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH", "OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK_ENABLED", "�������� ����������� ����� vkontakte.ru");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB_ENABLED", "�������� ����������� ����� facebook.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID_ENABLED", "�������� ����������� ����� OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH_ENABLED", "�������� ����������� ����� OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER_ENABLED", "�������� ����������� ����� twitter.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_VK", "������ �� ���������");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_FB", "������ �� Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_TWITTER", "������ �� Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OPENID", "������ �� OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OAUTH", "������ �� OAuth");

// ����� ���������
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_SITE", "������� ����������� �� �����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_ADMIN", "������� ����������� � ������� �����������������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_LOGIN", "�� ������ � ������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_TOKEN", "�� usb-������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_HASH", "�� ����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_EX", "����� ������� �������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM", "������ ���������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_ALLOW", "��������� ���������� ������ ���������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_NOTIFY", "��������� ������������ �� email � ����� ���������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_BANNED", "������ � �����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_ALLOW", "��������� ��������� ������������� � ������");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_BANNED_ALLOW", "��������� ��������� ������������� �� �����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA", "������ ����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_ALLOW", "������ ������ ����");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_CURRENCY", "������� ��������� %input");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_START", "��������� ����� ������������������ %input ������");

// ������
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_SUBJECT", "��������� ������");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_BODY", "���� ������");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_HTML", "HTML-������");

// ��������� ���������
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENTS_SUBS", "����������, ������� � ����");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_FRIENDS", "��������� \"������ ������\"");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PM", "��������� ��� ������ ����������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PA", "��������� ��� ������� �����");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_FIELD_PA", "���� c �������� ��� ������� �����");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MATERIALS", "������ � ����������� ������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MODIFY", "������� ��� ��������� ������, ����� �������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_CC_USER_LIST", "�������� � ������� �� ������� �������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO", "������������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_ALLOW", "�������� ������������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_CHECK_IP", "��������� IP");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_GROUP", "������ �������������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_FIELD", "����, �� �������� ���� �������������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH", "���-�����������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DELETE", "������� ��� ����� �����������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_EXPIRE", "����� ����� ���� � ����� %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DISABLED_SUBS", "������ ��������, ��� ��������� ���-�����������, ����� ������� %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER", "������");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_ONLINE", "�����, � ������� �������� ������������ ��������� online � �������� %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_IP", "������� �������� IP-������ (0-4) %input");

define("NETCAT_MODULE_AUTH_SELFREG_DISABLED", "��������������� ����������� ���������");



define("NETCAT_MODULE_AUTH_FORM_TEMPLATES", "������� ����");
define("NETCAT_MODULE_AUTH_FORM_AUTH", "����� �����������");
define("NETCAT_MODULE_AUTH_RESTORE_DEF", "������������ �������� �� ���������");
define("NETCAT_MODULE_AUTH_FORM_DISABLED", "�� ���������� ����� ��� ��������� ������� �����������");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS", "����� ��������� ������");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_AFTER", "�����, ��������� ����� ����� ������");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS_AFTER", "�����, ��������� ����� �������� ������");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_WARNBLOCK", "���� ������ ������");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_DENY", "�����, ��������� ��� ������� �������������� ������");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS", "����� �������������� ������");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER", "�����, ��������� ��� �������� ������������� �����������");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER_WARNBLOCK", "���� ������ ������ ��� ������������� �����������");
define("NETCAT_MODULE_AUTH_MAIL_TEMPLATES", "������� �����");
define("NETCAT_MODULE_AUTH_REG_CONFIRM", "������������� �����������");
define("NETCAT_MODULE_AUTH_REG_CONFIRM_AFTER", "����������� ����� ������������� �����������");
define("NETCAT_MODULE_AUTH_AS_HTML", "HTML �����");
define("NETCAT_MODULE_AUTH_RECOVERY", "�������������� ������");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_NOTIFY", "���������� �������������� � ����������� ������������");

define("NETCAT_MODULE_AUTH_ADD_FRIEND", "�������� � ������");
define("NETCAT_MODULE_AUTH_REMOVE_FRIEND", "������� �� ������");

// OpenID
define("NETCAT_MODULE_AUTH_OPEN_ID_ERROR", "����������� OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_INVALID", "������������ OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_ALREADY_EXIST_IN_BASE", "����� OpenID ��� ��������������� � ����");
define("NETCAT_MODULE_AUTH_OPEN_ID_COULD_NOT_REDIRECT_TO_SERVER", "Could not redirect to server: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_CHECK_CANCELED", "�������� ��������");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_FAILED", "OpenID ����������� �� �������: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_NAME", "�� ������� �������������� ��� <a href='%s'>%s</a> ");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_LOGIN", "��� ����� �� �����: %s");


define("NETCAT_MODULE_AUTH_SETUP_PROFILE", "������ �������");
define("NETCAT_MODULE_AUTH_SETUP_REGISTRATION", "�����������");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD_RECOVERY", "�������������� ������");
define("NETCAT_MODULE_AUTH_SETUP_MODIFY", "��������� ������");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD", "��������� ������");
define("NETCAT_MODULE_AUTH_SETUP_PM", "������ ���������");


define("NETCAT_MODULE_AUTH_APPLICATION_ID", "ID ����������");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_VK", "ID ����������");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_FB", "ID ���������� (Application ID)");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_TWITTER", "ID ���������� (Consumer key)");
define("NETCAT_MODULE_AUTH_SECRET_KEY", "���������� ����");
define("NETCAT_MODULE_AUTH_PUBLIC_KEY", "��������� ����");
define("NETCAT_MODULE_AUTH_SECRET_KEY_VK", "���������� ����");
define("NETCAT_MODULE_AUTH_SECRET_KEY_FB", "���������� ���� (Application Secret)");
define("NETCAT_MODULE_AUTH_SECRET_KEY_TWITTER", "���������� ���� (Consumer secret)");

define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE", "������, ���� ������ ������������");
define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE_EMPTY", "�� ������� �� ����� ������!");
define("NETCAT_MODULE_AUTH_ACTION_BEFORE_FIRST_AUTHORIZATION", "�������� �� ������ ����������� ������������");
define("NETCAT_MODULE_AUTH_ACTION_AFTER_FIRST_AUTHORIZATION", "�������� ����� ������ ����������� ������������");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING", "������������ �����");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING_ADD", "�������� ������������");
define("NETCAT_MODULE_AUTH_PROVIDER_ADD", "�������� ����������");
define("NETCAT_MODULE_AUTH_PROVIDER", "���������");
define("NETCAT_MODULE_AUTH_PROVIDER_ICON", "������ ����������");


define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_SUBJECT", "������������� ����������� �� ����� %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_BODY",
        "������������, %USER_LOGIN

�� ������� ������������������ �� ����� <a href='%SITE_URL'>%SITE_NAME</a>
��� �����: 	%USER_LOGIN
��� ������: 	%PASSWORD

����� ������������ ��� ������� ��������, ����������, ������ ������:

<a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>

�� �������� ��� ���������, ������ ��� ��� email ����� ��� ��������������� �� ����� %SITE_URL
���� �� �� ���������������� �� ���� �����, ����������, �������������� ��� ������.

� ���������� �����������, ������������� ����� <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_SUBJECT", "����������� �� ����� %SITE_NAME ������� ������������");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_BODY",
        "������������, %USER_LOGIN

��� ������� ������� �����������, ������ �� ������ � ������ ���� ������������ ������� ������ �����.

� ���������� �����������, ������������� ����� <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_SUBJECT", "�������������� ������ �� ����� %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_BODY",
        "������������, %USER_LOGIN

��� �������������� ������ ��� ������������ %USER_LOGIN �� ����� <a href='%SITE_URL'>%SITE_NAME</a> ��������, ����������, ������ ������:

<a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>

���� �� �� ����������� �������������� ������, ����������, �������������� ��� ������.

� ���������� �����������, ������������� ����� <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_SUBJECT", "����� ������������ �� ����� %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_BODY",
        "������������, �������������.

�� ����� <a href='%SITE_URL'>%SITE_NAME</a> ��������������� ����� ������������ � ������� %USER_LOGIN

� ���������� �����������, ���� <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_USER_AGREEMENT", "� �������� ������� <a href='%USER_AGR' target='_blank'>����������������� ����������</a>");
define("NETCAT_MODULE_AUTH_AUTHENTICATION_FAILED", "�������� ������ �� ����� ��������������.");
define("NETCAT_MODULE_AUTH_RETRY", "����������, �������� �������� � ���������� ��� ���, ������ ��� �����.");

// �������� ����������� ��������
define("NETCAT_MODULE_AUTH_PROFILE_SUBDIVISION_NAME", "������ �������");
define("NETCAT_MODULE_AUTH_EDIT_PROFILE_SUBDIVISION_NAME", "��� �������");
define("NETCAT_MODULE_AUTH_CHANGE_PASS_SUBDIVISION_NAME", "������� ������");
define("NETCAT_MODULE_AUTH_RECOVERY_PASS_SUBDIVISION_NAME", "�������������� ������");
define("NETCAT_MODULE_AUTH_REGISTRATION_SUBDIVISION_NAME", "�����������");