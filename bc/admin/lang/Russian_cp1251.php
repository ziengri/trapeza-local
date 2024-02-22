<?php

/**
 * ������� �������� ������� ���� � ��������
 *
 * @param string $text ������;
 * @param bool $use_url_rules ������������ ������ ��� URL;
 * @return string ������;
 */
function nc_transliterate($text, $use_url_rules = false) {

    $tr = array("�" => "A", "�" => "a", "�" => "B", "�" => "b",
            "�" => "V", "�" => "v", "�" => "G", "�" => "g",
            "�" => "D", "�" => "d", "�" => "E", "�" => "e",
            "�" => "E", "�" => "e", "�" => "Zh", "�" => "zh",
            "�" => "Z", "�" => "z", "�" => "I", "�" => "i",
            "�" => "Y", "�" => "y", "��" => "X", "��" => "x",
            "�" => "K", "�" => "k", "�" => "L", "�" => "l",
            "�" => "M", "�" => "m", "�" => "N", "�" => "n",
            "�" => "O", "�" => "o", "�" => "P", "�" => "p",
            "�" => "R", "�" => "r", "�" => "S", "�" => "s",
            "�" => "T", "�" => "t", "�" => "U", "�" => "u",
            "�" => "F", "�" => "f", "�" => "H", "�" => "h",
            "�" => "Ts", "�" => "ts", "�" => "Ch", "�" => "ch",
            "�" => "Sh", "�" => "sh", "�" => "Sch", "�" => "sch",
            "�" => "Y", "�" => "y", "�" => "'", "�" => "'",
            "�" => "E", "�" => "e", "�" => "'", "�" => "'",
            "�" => "Yu", "�" => "yu", "�" => "Ya", "�" => "ya");

    $tr_text = strtr($text, $tr);
    if ($use_url_rules) {
      $tr_text = strtolower(trim($tr_text));
      $tr_text = preg_replace('/[^\w\-\ ]/', '', $tr_text);
      $tr_text = str_replace(' ', '-', $tr_text);
      $tr_text = preg_replace('/\-{2,}/', '-', $tr_text);
    }

    return $tr_text;
}

// Include deprecated strings if $NC_DEPRECATED_DISABLED is set to 0
if (isset($NC_DEPRECATED_DISABLED) && $NC_DEPRECATED_DISABLED==0) {
	$deprecated_file = preg_replace('/\.php/', '_old.php', __FILE__);
	if (file_exists($deprecated_file)) {
		include_once $deprecated_file;
	}
}
# MAIN
define("MAIN_DIR", "ltr");
define("MAIN_LANG", "ru");
define("MAIN_NAME", "Russian");
define("MAIN_ENCODING", $nc_core->NC_CHARSET);
define("MAIN_EMAIL_ENCODING", $nc_core->NC_CHARSET);
define("NETCAT_RUALPHABET", "�-��-߸�");

define("NETCAT_TREE_SITEMAP", "����� �����");
define("NETCAT_TREE_MODULES", "������ � �������");
define("NETCAT_TREE_USERS", "������������");

define("NETCAT_TREE_PLUS_TITLE", "�������� ������");
define("NETCAT_TREE_MINUS_TITLE", "�������� ������");

define("NETCAT_TREE_QUICK_SEARCH", "������� �����");

// Tabs
define("NETCAT_TAB_REFRESH", "��������");

define("STRUCTURE_TAB_SUBCLASS_ADD", "�������� ��������");
define("STRUCTURE_TAB_INFO", "����������");
define("STRUCTURE_TAB_SETTINGS", "���������");
define("STRUCTURE_TAB_USED_SUBCLASSES", "���������");
define("STRUCTURE_TAB_EDIT", "��������������");
define("STRUCTURE_TAB_PREVIEW", "�������� &rarr;");
define("STRUCTURE_TAB_PREVIEW_SITE", "������� �� ���� &rarr;");


define("CLASS_TAB_INFO", "���������");
define("CLASS_TAB_EDIT", "�������������� ����������");
define("CLASS_TAB_CUSTOM_ACTION", "������� ��������");
define("CLASS_TAB_CUSTOM_ADD", "����������");
define("CLASS_TAB_CUSTOM_EDIT", "���������");
define("CLASS_TAB_CUSTOM_DELETE", "��������");
define("CLASS_TAB_CUSTOM_SEARCH", "�����");

# BeginHtml
define("BEGINHTML_TITLE", "�����������������");
define("BEGINHTML_USER", "������������");
define("BEGINHTML_VERSION", "������");
define("BEGINHTML_PERM_GUEST", "�������� ������");
define("BEGINHTML_PERM_DIRECTOR", "��������");
define("BEGINHTML_PERM_SUPERVISOR", "����������");
define("BEGINHTML_PERM_CATALOGUEADMIN", "������������� �����");
define("BEGINHTML_PERM_SUBDIVISIONADMIN", "������������� �������");
define("BEGINHTML_PERM_SUBCLASSADMIN", "������������� ���������� �������");
define("BEGINHTML_PERM_CLASSIFICATORADMIN", "������������� ������");
define("BEGINHTML_PERM_MODERATOR", "���������");

define("BEGINHTML_LOGOUT", "����� �� �������");
define("BEGINHTML_LOGOUT_OK", "����� ��������.");
define("BEGINHTML_LOGOUT_RELOGIN", "����� ��� ������ ������");
define("BEGINHTML_LOGOUT_IE", "��� ���������� ������ �������� ��� ���� ��������.");


define("BEGINHTML_ALARMON", "������������� ��������� ���������");
define("BEGINHTML_ALARMOFF", "��������� ���������: ������������� ���");
define("BEGINHTML_ALARMVIEW", "�������� ���������� ���������");
define("BEGINHTML_HELPNOTE", "���������");

# EndHTML
define("ENDHTML_NETCAT", "������");

# Common
define("NETCAT_ADMIN_DELETE_SELECTED", "������� ���������");
define("NETCAT_SELECT_SUBCLASS_DESCRIPTION", "� ������� &laquo;%s&raquo;, ������� ��������� ����������� ���� &laquo;%s&raquo;.<br />
  �������� ��������� �������, � ������� ����� ��������� ������, ����� �� �������� ����������.");

# INDEX PAGE
define("SECTION_INDEX_SITES_SETTINGS", "��������� ������");
define("SECTION_INDEX_MODULES_MUSTHAVE", "�� �������������");
define("SECTION_INDEX_MODULES_DESCRIPTION", "��������");
define("SECTION_INDEX_MODULES_TRANSITION", "������� �� ������� ��������");
define("DASHBOARD_WIDGET", "������ ��������");
define("DASHBOARD_ADD_WIDGET", "�������� ������");
define("DASHBOARD_DEFAULT_WIDGET", "������� �� ���������");
define("DASHBOARD_WIDGET_SYS_NETCAT", "� �������");
define("DASHBOARD_WIDGET_MOD_AUTH", "���������� ��");
define("DASHBOARD_UPDATES_EXISTS", "���� ����������");
define("DASHBOARD_UPDATES_DONT_EXISTS", "��� ����������");
define("DASHBOARD_DONT_ACTIVE", "����������������");
define("DASHBOARD_TODAY", "�������");
define("DASHBOARD_YESTERDAY", "�����");
define("DASHBOARD_PER_WEEK", "� ������");
define("DASHBOARD_WAITING", "����");


# MODULES LIST
define("NETCAT_MODULE_DEFAULT", "��������� ������������");
define("NETCAT_MODULE_AUTH", "������ �������");
define("NETCAT_MODULE_SEARCH", "����� �� �����");
define("NETCAT_MODULE_SERCH", "����� �� ����� (������ ������)");
define("NETCAT_MODULE_POLL", "����������� (��������)");
define("NETCAT_MODULE_ESHOP", "��������-������� (������)");
define("NETCAT_MODULE_STATS", "���������� ���������");
define("NETCAT_MODULE_SUBSCRIBE", "�������� � ��������");
define("NETCAT_MODULE_BANNER", "���������� ��������");
define("NETCAT_MODULE_FORUM", "�����");
define("NETCAT_MODULE_FORUM2", "����� v2");
define("NETCAT_MODULE_NETSHOP", "��������-�������");
define("NETCAT_MODULE_LINKS", "���������� ��������");
define("NETCAT_MODULE_CAPTCHA", "������ ���� ���������");
define("NETCAT_MODULE_TAGSCLOUD", "������ �����");
define("NETCAT_MODULE_BLOG", "���� � ����������");
define("NETCAT_MODULE_CALENDAR", "���������");
define("NETCAT_MODULE_COMMENTS", "�����������");
define("NETCAT_MODULE_LOGGING", "�����������");
define("NETCAT_MODULE_FILEMANAGER", "����-��������");
define("NETCAT_MODULE_CACHE", "�����������");
define("NETCAT_MODULE_MINISHOP", "�����������");
define("NETCAT_MODULE_ROUTING", "�������������");
define('NETCAT_MODULE_AIREE', '���� CDN');

define("NETCAT_MODULE_NETSHOP_MODULEUNCHECKED", "������ \"��������-�������\" �� ���������� ��� ��������!");
# /MODULES LIST

define("SECTION_INDEX_USER_STRUCT_CLASSIFICATOR", "������");

define("SECTION_INDEX_USER_RIGHTS_TYPE", "��� ����");
define("SECTION_INDEX_USER_RIGHTS_RIGHTS", "�����");

define("SECTION_INDEX_USER_USER_MAIL", "�������� �� ����");
define("SECTION_INDEX_USER_SUBSCRIBERS", "�������� ������������");

define("SECTION_INDEX_DEV_CLASSES", "����������");
define("SECTION_INDEX_DEV_CLASS_TEMPLATES", "������� ����������");
define("SECTION_INDEX_DEV_TEMPLATES", "������ �������");


define("SECTION_INDEX_ADMIN_PATCHES_INFO", "��������� ����������");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_VERSION", "������ �������");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_REDACTION", "�������� �������");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH", "��������� ����������");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH_DATE", "��������� �������� ����������");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_CHECK_PATCH", "��������� ������� ����������");

define("SECTION_INDEX_REPORTS_STATS", "����� ���������� �������");
define("SECTION_INDEX_REPORTS_SYSTEM", "��������� ���������");



# SECTION CONTROL
define("SECTION_CONTROL_CONTENT_CATALOGUE", "�����");
define("SECTION_CONTROL_CONTENT_FAVORITES", "������� ��������������");
define("SECTION_CONTROL_CONTENT_CLASSIFICATOR", "������");

# SECTION USER
define("SECTION_CONTROL_USER", "������������");
define("SECTION_CONTROL_USER_LIST", "������ �������������");
define("SECTION_CONTROL_USER_PERMISSIONS", "������������ � �����");
define("SECTION_CONTROL_USER_GROUP", "������ �������������");
define("SECTION_CONTROL_USER_MAIL", "�������� �� ����");

# SECTION CLASS
define("SECTION_CONTROL_CLASS", "����������");
define("CONTROL_CLASS_USE_CAPTCHA", "�������� ����� ���������� ���������");
define("CONTROL_CLASS_CACHE_FOR_AUTH", "����������� �� �����������");
define("CONTROL_CLASS_CACHE_FOR_AUTH_NONE", "�� ������������");
define("CONTROL_CLASS_CACHE_FOR_AUTH_USER", "��������� ������� ������������");
define("CONTROL_CLASS_CACHE_FOR_AUTH_GROUP", "��������� �������� ������ ������������");
define("CONTROL_CLASS_CACHE_FOR_AUTH_DESCRIPTION", "���� � ���������� ����� �������� ������ ���������� ��� ������� ������������, ��� ��������� �������� ������� ��������� �������.");
define("CONTROL_CLASS_ADMIN", "�����������������");
define("CONTROL_CLASS_CONTROL", "����������");
define("CONTROL_CLASS_FIELDSLIST", "������ �����");
define("CONTROL_CLASS_CLASS_GOTOFIELDS", "������� � ������ ����� ����������");
define("CONTROL_CLASS_CLASSFORM_ADDITIONAL_INFO", "�������������� ����������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORTNOTE", "��������_����_1[ DESC][, ��������_����_2[ DESC]][, ...]<br>DESC - ���������� �� ��������");
define("CONTROL_CLASS_CLASS_SHOW_VAR_FUNC_LIST", "�������� ������ ���������� � �������");
define("CONTROL_CLASS_CLASS_SHOW_VAR_LIST", "�������� ������ ����������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODEL", "������� ������� �����");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODELEND", "���� ����� ����������");
define("CONTROL_CLASS_CLASS_FORMS_YES", "��");
define("CONTROL_CLASS_CLASS_FORMS_NO", "���");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM", "�������������� ����� ���������� �������");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN", "������������� ��� �����");
define("CONTROL_CLASS_CLASS_FORMS_ADDRULES", "������� ���������� �������");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN", "������������� ��� �������");
define("CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION", "�������� ����� ���������� �������");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN", "������������� ��� ��������");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM", "�������������� ����� ��������� �������");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN", "������������� ��� �����");
define("CONTROL_CLASS_CLASS_FORMS_EDITRULES", "������� ��������� �������");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN", "������������� ��� �������");
define("CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION", "�������� ����� ��������� �������");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN", "������������� ��� ��������");
define("CONTROL_CLASS_CLASS_FORMS_ONONACTION", "�������� ����� ��������� � ���������� �������");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN", "������������� ��� ��������");
define("CONTROL_CLASS_CLASS_FORMS_DELETEFORM", "�������������� ����� �������� �������");
define("CONTROL_CLASS_CLASS_FORMS_DELETERULES", "������� �������� �������");
define("CONTROL_CLASS_CLASS_FORMS_ONDELACTION", "�������� ����� �������� �������");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN", "������������� ��� ��������");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH", "����� ������ ����� ������� ��������");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN", "������������� ��� �����");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH", "����� ������������ ������ (�� ��������� ��������)");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN", "������������� ��� �����");
define("CONTROL_CLASS_CLASS_FORMS_MAILRULES", "������� ��� ��������");
define("CONTROL_CLASS_CLASS_FORMS_MAILTEXT", "������ ������ ��� �����������");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_QSEARCH."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_SEARCH."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_ADDFORM."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_EDITFORM."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_ADDRULES."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_EDITRULES."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_ONONACTION."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN_WARN", "���� \\\"".CONTROL_CLASS_CLASS_FORMS_ONDELACTION."\\\" �� ������! �������� ����� � ���� ���� �� �����?");
define("CONTROL_CLASS_CUSTOM_SETTINGS_ISNOTSET", "��������� ����������� ���������� ������� �����������.");
define("CONTROL_CLASS_CUSTOM_SETTINGS_INHERIT_FROM_PARENT", "��������� ����������� ������� ���������� �������� � ����� ����������.");

# SECTION WIDGET
define("WIDGETS", "�������");
define("WIDGETS_LIST_IMPORT", "������");
define("WIDGETS_LIST_ADD", "��������");
define("WIDGETS_PARAMS", "���������");
define("SECTION_INDEX_DEV_WIDGET", "������-����������");
define("CONTROL_WIDGETCLASS_ADD", "�������� ������");
define("WIDGET_LIST_NAME", "��������");
define("WIDGET_LIST_CATEGORY", "���������");
define("WIDGET_LIST_ALL", "���");
define("WIDGET_LIST_GO", "�������");
define("WIDGET_LIST_FIELDS", "����");
define("WIDGET_LIST_DELETE", "�������");
define("WIDGET_LIST_DELETE_WIDGETCLASS", "������-���������:");
define("WIDGET_LIST_DELETE_WIDGET", "�������:");
define("WIDGET_LIST_EDIT", "��������������");
define("WIDGET_LIST_AT", "������� ��������");
define("WIDGET_LIST_ADDWIDGET", "�������� ������-���������");
define("WIDGET_LIST_DELETE_SELECTED", "������� ���������");
define("WIDGET_LIST_ERROR_DELETE", "������� �������� ������-���������� ��� ��������");
define("WIDGET_LIST_INSERT_CODE", "��� ��� �������");
define("WIDGET_LIST_INSERT_CODE_CLASS", "��� ��� ������� � �����/���������");
define("WIDGET_LIST_INSERT_CODE_TEXT", "��� ��� ������� � �����");
define("WIDGET_LIST_LOAD", "��������...");
define("WIDGET_LIST_PREVIEW", "������");
define("WIDGET_LIST_EXPORT", "�������������� ������-��������� � ����");
define("WIDGET_ADD_CREATENEW", "������� ����� ������-��������� &quot;� ����&quot;");
define("WIDGET_ADD_CONTINUE", "����������");
define("WIDGET_ADD_CREATENEW_BASICOLD", "������� ����� ������-��������� �� ������ �������������");
define("WIDGET_ADD_NAME", "��������");
define("WIDGET_ADD_KEYWORD", "�������� �����");
define("WIDGET_ADD_UPDATE", "��������� ������� ������ N ����� (0 - �� ���������)");
define("WIDGET_ADD_NEWGROUP", "����� ������");
define("WIDGET_ADD_DESCRIPTION", "�������� ������-����������");
define("WIDGET_ADD_OBJECTVIEW", "������ �����������");
define("WIDGET_ADD_PAGEBODY", "����������� �������");
define("WIDGET_ADD_DOPL", "�������������");
define("WIDGET_ADD_DEVELOP", "� ����������");
define("WIDGET_ADD_SYSTEM", "��������� ���������");
define("WIDGETCLASS_ADD_ADD", "�������� ������-���������");
define("WIDGET_ADD_ADD", "�������� ������");
define("WIDGET_ADD_ERROR_NAME", "������� �������� ������-����������");
define("WIDGET_ADD_ERROR_KEYWORD", "������� �������� �����");
define("WIDGET_ADD_ERROR_KEYWORD_EXIST", "�������� ����� ������ ���� ����������");
define("WIDGET_ADD_WK", "������-���������");
define("WIDGET_ADD_OK", "������ ������� ��������");
define("WIDGET_ADD_DISALLOW", "��������� ����������� � ������");
define("WIDGET_IS_STATIC", "��������� ������");
define("WIDGET_EDIT_SAVE", "��������� ���������");
define("WIDGET_EDIT_OK", "��������� ���������");
define("WIDGET_INFO_DESCRIPTION", "�������� ������-����������");
define("WIDGET_INFO_DESCRIPTION_NONE", "�������� �����������");
define("WIDGET_INFO_PREVIEW", "������");
define("WIDGET_INFO_INSERT", "��� ��� ������� � �����/���������");
define("WIDGET_INFO_INSERT_TEXT", "��� ��� ������� � �����");
define("WIDGET_INFO_GENERATE", "������ ���������� ��� ������������ ������� � �����/���������");
define("WIDGET_DELETE_WARNING", "��������: ������-���������%s � ��� � ���%s ��������� ����� �������.");
define("WIDGET_DELETE_CONFIRMDELETE", "����������� ��������");
define("WIDGET_DELETE", "��������: ������ ����� �����.");
define("WIDGET_ACTION_ADDFORM", "�������������� ����� ���������� �������");
define("WIDGET_ACTION_EDITFORM", "�������������� ����� ��������� �������");
define("WIDGET_ACTION_BEFORE_SAVE", "�������� ����� ����������� �������");
define("WIDGET_ACTION_AFTER_SAVE", "�������� ����� ���������� �������");
define("WIDGET_IMPORT", "�������������");
define("WIDGET_IMPORT_TAB", "������");
define("WIDGET_IMPORT_CHOICE", "�������� ����");
define("WIDGET_IMPORT_ERROR", "������ ���������� �����");
define("WIDGET_IMPORT_OK", "������-��������� ������� ������������");

define("SECTION_CONTROL_WIDGET", "�������");
define("SECTION_CONTROL_WIDGETCLASS", "������-����������");
define("SECTION_CONTROL_WIDGET_LIST", "������ ��������");
define("CONTROL_WIDGET_ACTIONS_EDIT", "��������������");
define("CONTROL_WIDGET_NONE", "� ������� ��� �� ������ ������-����������");
define("TOOLS_WIDGET", "�������");
define("CONTROL_WIDGET_ADD_ACTION", "���������� �������");
define("CONTROL_WIDGETCLASS_ADD_ACTION", "���������� ������-����������");
define("SECTION_INDEX_DEV_WIDGETS", "�������");
define("CONTROL_WIDGETCLASS_IMPORT", "������ �������");
define("CONTROL_WIDGETCLASS_FILES_PATH", "����� ������-���������� ��������� � ����� <a href='%s'>%s</a>");

define("WIDGET_TAB_INFO", "����������");
define("WIDGET_TAB_EDIT", "�������������� ������-����������");
define("WIDGET_TAB_CUSTOM_ACTION", "������� ��������");
define("NETCAT_REMIND_SAVE_TEXT", "����� ��� ����������?");
define("NETCAT_REMIND_SAVE_SAVE", "���������");
define("SECTION_SECTIONS_INSTRUMENTS_WIDGETS", "�������");

# SECTION TEMPLATE
define("SECTION_CONTROL_TEMPLATE_SHOW", "������ �������");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_OPTIONS", "��������� �������");
define("SECTION_SECTIONS_OPTIONS_MODULE_LIST", "���������� ��������");
define("SECTION_SECTIONS_OPTIONS_WYSIWYG", "��������� WYSIWYG");
define("SECTION_SECTIONS_OPTIONS_SYSTEM", "��������� �������");
define("SECTION_SECTIONS_OPTIONS_SECURITY", "������������");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_INSTRUMENTS_SQL", "��������� ������ SQL");
define("SECTION_SECTIONS_INSTRUMENTS_TRASH", "������� ��������� ��������");
define("SECTION_SECTIONS_INSTRUMENTS_CRON", "���������� ��������");
define("SECTION_SECTIONS_INSTRUMENTS_HTML", "HTML-��������");

# SECTIONS MODDING
define("SECTION_SECTIONS_MODDING_ARHIVES", "������ �������");

# REPORTS
define("SECTION_REPORTS_TOTAL", "����� ���������� �������");
define("SECTION_REPORTS_SYSMESSAGES", "��������� ���������");

# SUPPORT

# ABOUT
define("SECTION_ABOUT_TITLE", "� ���������");
define("SECTION_ABOUT_HEADER", "� ���������");
define("SECTION_ABOUT_BODY", "������� ���������� ������� NetCat <font color=%s>%s</font> ������ %s. ��� ����� ��������.<br><br>\n���-���� ������� NetCat: <a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>\nEmail ������ ���������: <a href=mailto:support@netcat.ru>support@netcat.ru</a>\n<br><br>\n�����������: ��� &laquo;������&raquo;<br>\nEmail: <a href=mailto:info@netcat.ru>info@netcat.ru</a><br>\n+7 (495) 783-6021<br>\n<a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>");
define("SECTION_ABOUT_DEVELOPER", "����������� �������");

// ARRAY-2-FORMS


# INDEX
define("CONTROL_CONTENT_CATALOUGE_SITE", "�����");
define("CONTROL_CONTENT_CATALOUGE_ONESITE", "����");
define("CONTROL_CONTENT_CATALOUGE_ADD", "����������");
define("CONTROL_CONTENT_CATALOUGE_SITEDELCONFIRM", "������������� �������� �����");
define("CONTROL_CONTENT_CATALOUGE_ADDSECTION", "���������� �������");
define("CONTROL_CONTENT_CATALOUGE_ADDSITE", "���������� �����");
define("CONTROL_CONTENT_CATALOUGE_SITEOPTIONS", "��������� �����");

define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_ONE", "�������� ����� �� ����� ���� ������!");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DUPLICATE_DOMAIN", "���� � ����� �������� ������ ��� ���������� � �������. ������� ������ �������� ���.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_THREE", "�������� ��� ����� ��������� ������ ��������� �����, �����, �������������, ����� � �����! ����� ������ ����������� � �������. �������� �������� �����.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DOMAIN_NOT_SET", "�������� ��� �� �������");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN", "��������� �����");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN_FULLTEXT", "���������, ��������� �� ������ �����. NetCat ������ ���� ���������� � �������� ����� ����� ������ (��� ��������)!");

define("CONTROL_CONTENT_CATALOUGE_SUCCESS_ADD", "���� ������� ��������!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_EDIT", "��������� ����� ������� ��������!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_DELETE", "���� ������� ������!");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MAININFO", "�������� ����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NAME", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DOMAIN", "�����");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CATALOGUEFORM_LANG", "���� ����� (ISO 639-1)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MIRRORS", "������� (�� ������ �� �������)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_OFFLINE", "����������, ����� ���� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS", "���������� ����� Robots.txt");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS_DONT_CHANGE", "�� ��������� ���������� ����� �������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS_FILE_EXIST", "��������! ���� robots.txt ������������ � ����� �����. ������� ��� ���������� ��������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TEMPLATE", "����� �������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE", "��������� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE_PAGE", "��������� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND", "�������� �� ������� (������ 404)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND_PAGE", "�������� �� �������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_PRIORITY", "���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ON", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_HTTPS_ENABLED", "������������ HTTPS");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_LABEL_COLOR", "���� �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DEFAULT_CLASS", "��������� �� ���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_POLICY", "���������� �� ������������� �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SEARCH", "�����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_AUTH_PROFILE", "������ �������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_AUTH_PROFILE_MODIFY", "��� �������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_AUTH_PROFILE_SIGNUP", "�����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_CART", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_ORDER_SUCCESS", "����� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_ORDER_LIST", "��� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_COMPARE", "��������� �������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_FAVORITES", "��������� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NETSHOP_DELIVERY", "������� �������� � ��������");

define("CONTROL_CONTENT_SITE_ADD_EMPTY", "����� ������ ����");
define("CONTROL_CONTENT_SITE_ADD_WITH_CONTENT", "������� ����");
define("CONTROL_CONTENT_SITE_CATEGORY", "���������");
define("CONTROL_CONTENT_SITE_CATEGORY_ANY", "�����");
define("CONTROL_CONTENT_SITE_ADD_DATA_ERROR", "�� ������� ��������� ������ ��������� ������� ������");
define("CONTROL_CONTENT_SITE_ADD_PREVIEW", "����");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING", "������������ ���������� � ������������ �����");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING_ERROR", "�� ������� ��������� ����� � ������");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE", "������ �����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_TRADITIONAL", "������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage ��������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ACCESS", "������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USERS", "������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_VIEW", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT", "���������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CHANGE", "���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SUBSCRIBE", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_EXTFIELDS", "�������������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE", "���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_I", "�");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_U", "�");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE", "��������: ����%s � ��� � ���%s ��������� ����� �������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CONFIRMDELETE", "����������� ��������");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SETTINGS", "��������� �����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SIMPLE", "������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE", "��������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ADAPTIVE", "���������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USE_RESS", "������������ ���������� RESS");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR", "��������� ������ ��� �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR_NOTICE", "�������� ������ ��� ��������� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_REDIRECT", "������������ �������������� �������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_NONE", "[���]");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_DELETE", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CHANGE", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CRITERION", "���������� ����������� ��: ");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_USERAGENT", "User-agent");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SCREEN_RESOLUTION", "���������� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_ALL_CRITERION", "��� ��������������");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_CREATED", "���� �������� �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_UPDATED", "���� ��������� ���������� � �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SECTIONSCOUNT", "���������� �����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SITESTATUS", "������ �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ON", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_OFF", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADD", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS", "������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_READACCESS", "������ �� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDACCESS", "������ �� ����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDITACCESS", "������ �� ���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBEACCESS", "������ �� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_PUBLISHACCESS", "���������� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_VIEW", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDING", "����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SEARCHING", "�����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBING", "��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDIT", "��������������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_DELETE", "������� ����");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SITE", "����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SUBSECTIONS", "����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_PRIORITY", "���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_GOTO", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE", "�������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_LIST", "������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_TOOPTIONS", "�������� ��������� �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SHOW", "���������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_EDIT", "�������� ����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_NONE", "� ������� ��� �� ������ �����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE", "�������� ����");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SAVE", "��������� ���������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR", "������ ������� �� ����!");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SECTIONWASCREATED", "������ ������ %s<br>");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_FAVORITES_TITLE", "������� ��������������");
define("CONTROL_CONTENT_SUBDIVISION_FULL_TITLE", "����� �����");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SITES", "�����");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS", "�������");
define("CONTROL_CONTENT_SUBDIVISION_CLASS", "��������");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ADDSECTION", "���������� �������");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_OPTIONSECTION", "��������� �������");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_DELETECONFIRMATION", "������������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_MOVESECTION", "������� �������");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_NAME", "������� �������� �������!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_KEYWORD", "������ �������� ����� ��� ������������. ������� ������ �������� �����.");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_PARENTSUB", "�� ������ ������������ ������!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR", "������ ���������� �������");

define("CONTROL_CONTENT_SUBDIVISION_SUCCESS_EDIT", "��������� ������� ���������");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SECTION", "������ ����������� �������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SITE", "������ ����������� �� �����");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASS", "���������� ���������� � ������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_OPTIONSCLASS", "��������� ���������� �������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASSSITE", "���������� ���������� �� ����");
define("CONTROL_CONTENT_AREA_SUBCLASS_SETTINGS_TOOLTIP", "�������� ��������� ���������");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_NAME", "�������� ��������� �� ����� ���� ������!");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID", "�������� ����� �������� ������������ �������, ���� ������� �������. ��� ����� ��������� ������ �����, ����� � ������ �������������, � �� ����� ���� ������� 64 ��������.");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD", "������ �������� ����� ��� ������ ����� �� ����������. ������� ������ �������� �����.");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_ADD", "�������� ������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_ADD", "������ ���������� ��������� � ������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_EDIT", "�������� ������� �������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_EDIT", "������ �������������� ���������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_DELETE", "������ �������� ���������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_SUCCESS_EDIT", "������ ���������� ������� �������");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_ERROR_EDIT", "������ �������������� ������ ����������");

define("CONTROL_CONTENT_SUBDIVISION_FIRST_SUBCLASS", "� ������ ������� ��� �� ������ ���������.<br />��� ����, ����� ��������� ���������� � ������, ���������� �������� � ���� ���� �� ���� ��������.");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SECTION", "������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SUBSECTIONS", "����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_GOTO", "�������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOONEFAVORITES", "��� ��������� ��������.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONS", "�������� ��������� �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONSSUBCLASS", "�������� ��������� ���������� � �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOVIEW", "���������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOEDIT", "�������� ����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_PRIORITY", "���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_DELETE", "�������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NONE", "���");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LIST", "������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADD", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSECTIONS", "� ������� ����� ��� �� ������ �������.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSUBSECTIONS", "� ������ ������� ��� �����������.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDSECTION", "�������� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CONTINUE", "����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SELECT_ROOT_SECTION", "�������� ������, � ������� ������ �������� �����");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SAVE", "��������� ���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDFAVOTITES", "���������� ������ � &quot;��������� ��������&quot;");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_USEEDITDESIGNTEMPLATE", "������������ ���� ����� ��� �������������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA", "�������� ����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_NAME", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD", "�������� �����");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_EXTURL", "������� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_LANG", "���� ������� (ISO 639-1)");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE", "����� �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_CS", "��������� ������ �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_EDIT", "������������� ��� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_N", "�����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_MAINAREA_MIXIN_SETTINGS", "��������� ����������� ������� �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNON", "�������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNOFF", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_ADDSUBSECTION", "�������� ���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_REMSITE", "������� ����");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_MULTI_SUB_CLASS", "��������� ���������� � �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE", "������ �����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_INHERIT", "�����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_TRADITIONAL", "������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage ��������������");

define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_NOT_AVAILABLE", "������ ����� ������� �� ����� �������������� ��������.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS", "��������� ����������� ������ ������� � �������");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_ISNOTSET", "��������� ����������� ������ ������� � ������� �����������");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_SITE", "�������� ����������, ������� �� ������ � ���������� ����� �������,
        ����� ����� �� <a href='%s' target='_top'>�������� ������ �����</a>.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_FOLDER", "�������� ����������, ������� �� ������ � ���������� ����� �������,
        ����� ����� �� <a href='%s' target='_top'>�������� ������ ������� �%s�</a>.");

define("CONTROL_CUSTOM_SETTINGS_INHERIT", "������������ ��������, �������� � ������������ �������");
define("CONTROL_CUSTOM_SETTINGS_OFF", "���");
define("CONTROL_CUSTOM_SETTINGS_ON", "��");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_EDIT", "�������� ����������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_KILL", "������� ���� ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW", "���������� ��������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_MSG_OK", "������ ������� ��������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_ADDCLASSTOSECTION", "�������� ��������� � ������");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_BACKTOSECTIONLIST", "��������� � ������ ��������");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOCATALOGUE", "���� �� ����������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBDIVISION", "������ �� ����������.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBCLASS", "��������� � ������� �� ����������.");

define("CLASSIFICATOR_COMMENTS_DISABLE", "���������");
define("CLASSIFICATOR_COMMENTS_ENABLE", "���������");
define("CLASSIFICATOR_COMMENTS_NOREPLIED", "���������, ���� ��� �������");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_COMMENTS", "���������������");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS", "���������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_ADD", "���������� ������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_EDIT", "�������������� ����� ������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_DELETE", "�������� ����� ������������");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE", "����-�����");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE_CHECKBOX", "���������������� ����� ������ �����");

define("CONTROL_CONTENT_SUBCLASS_FUNCS_COMMENTS", "���������������");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS", "������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INHERIT", "�����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_PUBLISH", "���������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_READ", "������ �� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_ADD", "������ �� ����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_EDIT", "������ �� ���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_SUBSCRIBE", "������ �� ��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_PUBLISH", "���������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_USERS", "������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_VIEW", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_READ", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_COMMENT", "���������������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADD", "����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_WRITE", "����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_EDIT", "���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_CHECKED", "���������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_DELETE", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_SUBSCRIBE", "��������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADVANCEDFIELDS", "�������������� ����");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_HOWSHOW", "��������� �����������");
define("CONTROL_CONTENT_SUBDIVISION_CUSTOM_SETTINGS_TEMPLATE", "��������� ����������� ����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_YES", "��");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_NO", "���");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_UPDATED", "���� ��������� ���������� � �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_CLASS_COUNT", "���������� �����������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_STATUS", "������ �������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_SUBSECTIONS_COUNT", "���������� �����������");


define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE", "������� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ROOT", "�������� ������");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_CONFIRMATION", "����������� ��������");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING", "��������: ������%s � ��� � �%s ��������� ����� �������.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_MANY", "�");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_ONE", "");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_MANY", "���");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_ONE", "��");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ERR_NOONESITE", "���������� ����� �� ����������.");

define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS", "���������");
define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS_NO", "� ��������� ������� \"�������\" ��� �������������� �����");

define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_ALWAYS", "������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_HOURLY", "��������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_DAILY", "���������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_WEEKLY", "�����������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_MONTHLY", "����������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_YEARLY", "��������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_NEVER", "�������");

define("CONTROL_CONTENT_SUBDIVISION_SEO_META", "����-���� ��� SEO");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SMO_META", "����-���� ��� ���������� �����");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INDEXING", "��������������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_CURRENT_VALUE", "������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_VALUE_NOT_SETTINGS", "�������� %s �� �������� �������� �� ����, ��� �� �������. <a target='_blank' href='https://netcat.ru/developers/docs/seo/title-keywords-and-description/'>���������</a>.");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HEADER", "��������� Last-Modified");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_NONE", "�� ��������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_YESTERDAY", "���������� ����");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HOUR", "���������� ���");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_CURRENT", "������� ����");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_ACTUAL", "���������� ����");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING", "��������� ��������������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_YES", "��");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_NO", "���");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INCLUDE_IN_SITEMAP", "�������� ������ � Sitemap");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_PRIORITY", "Sitemap: ��������� ��������");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ", "Sitemap: ������� ��������� ��������");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_SUCCESS", "�������� ��������� �������.");

define("CONTROL_CONTENT_CLASS", "���������");
define("CONTROL_CONTENT_SUBCLASS_CLASSNAME", "�������� �����");
define("CONTROL_CONTENT_SUBCLASS_SHOW_ALL", "�������� ���");
define("CONTROL_CONTENT_SUBCLASS_ONSECTION", "� �������");
define("CONTROL_CONTENT_SUBCLASS_ONSITE", "�� �����");
define("CONTROL_CONTENT_SUBCLASS_MSG_NONE", "� ������ ������� ��� ����������.");
define("CONTROL_CONTENT_SUBCLASS_DEFAULTACTION", "�������� �� ���������");
define("CONTROL_CONTENT_SUBCLASS_CREATIONDATE", "���� �������� ���������� %s");
define("CONTROL_CONTENT_SUBCLASS_UPDATEDATE", "���� ��������� ���������� � ���������� %s");
define("CONTROL_CONTENT_SUBCLASS_TOTALOBJECTS", "��������");
define("CONTROL_CONTENT_SUBCLASS_CLASSSTATUS", "������ ����������");
define("CONTROL_CONTENT_SUBCLASS_CHANGEPREFS", "�������� ��������� ���������� %s");
define("CONTROL_CONTENT_SUBCLASS_DELETECLASS", "������� ��������� %s");
define("CONTROL_CONTENT_SUBCLASS_ISNAKED", "�� ������������ ����� �������");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR", "�������� ������");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_NONE", "[���]");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_EDIT", "��������");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_DELETE", "�������");
define("CONTROL_CONTENT_SUBCLASS_TYPE", "��� ���������");
define("CONTROL_CONTENT_SUBCLASS_TYPE_SIMPLE", "�������");
define("CONTROL_CONTENT_SUBCLASS_TYPE_MIRROR", "����������");
define("CONTROL_CONTENT_SUBCLASS_MIRROR", "���������� ��������");
define("CONTROL_CONTENT_SUBCLASS_MULTI_TITLE", "������ ����������� ���������� �� ��������");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONONEPAGE", "�������� �� ����� ��������");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONTABS", "�������� �� ��������");
define("CONTROL_CONTENT_SUBCLASS_MULTI_NONE", "�������� ������ ������ ��������");
define("CONTROL_CONTENT_SUBCLASS_EDIT_IN_PLACE", "������ ����� ��������� ���������� ������������� �� �������� \"<a href='%s'>%s</a>\"");
define("CONTROL_CONTENT_SUBCLASS_CONDITION_OFFSET", "������� �������� ���������� �� ������ �������");
define("CONTROL_CONTENT_SUBCLASS_CONDITION_LIMIT", "������������ ���������� ������� � �������");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_SETTINGS_GOTO", "�������");
define("CONTROL_CONTENT_SUBCLASS_CONTAINER", "���������");
define("CONTROL_CONTENT_SUBCLASS_AREA", "�������");

define("CONTROL_SETTINGSFILE_TITLE_ADD", "����������");
define("CONTROL_SETTINGSFILE_TITLE_EDIT", "��������������");
define("CONTROL_SETTINGSFILE_BASIC_REGCODE", "����� ��������");
define("CONTROL_SETTINGSFILE_BASIC_MAIN", "�������� ����������");
define("CONTROL_SETTINGSFILE_BASIC_MAIN_NAME", "�������� �������");

define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE", "����� �������, ������������ ��� �������������� ��������");
define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE_DEFAULT", "����� �������������� �������");

define("CONTROL_SETTINGSFILE_SHOW_EXCURSION", "���������� ��������� ��� �������� ������������");

define("CONTROL_SETTINGSFILE_BASIC_EMAILS", "��������");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FILELD", "���� (� �������� email) � ������� �������������");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMNAME", "��� �����������");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMEMAIL", "Email �����������");
define("CONTROL_SETTINGSFILE_BASIC_CHANGEDATA", "�������� ��������� �������");


define("CONTROL_SETTINGSFILE_BASIC_USE_SMTP", "������������ SMTP");
define("CONTROL_SETTINGSFILE_BASIC_USE_SENDMAIL", "������������ Sendmail");
define("CONTROL_SETTINGSFILE_BASIC_USE_MAIL", "������������ ������� mail");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_PARAMETERS", "�������������� ��������� ��� sendmail (<code>%s</code> ��� ����������� ������ �����������)");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_HOST", "SMTP ������");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PORT", "����");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_AUTH_USE", "������������ �����������");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_USERNAME", "��� ������������");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PASSWORD", "������");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_ENCRYPTION", "����������");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_NOENCRYPTION", "���");
define("CONTROL_SETTINGSFILE_BASIC_SENDMAIL_COMMAND", "Sendmail ������� (�������� '/usr/sbin/sendmail -bs')");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_TRANSPORT_HEADER", "��� ����������");

define("CONTROL_SETTINGSFILE_AUTOSAVE", "��������� ������� \"��������\"");
define("CONTROL_SETTINGSFILE_AUTOSAVE_USE", "������������ ������� \"��������\"");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_KEYBOARD", "��������� �� ������� ������");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_TIMER", "��������� ������������");
define("CONTROL_SETTINGSFILE_AUTOSAVE_PERIOD", "�������������, ���");
define("CONTROL_SETTINGSFILE_AUTOSAVE_NO_ACTIVE", "��������� ������ ��� �����������");

define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP", "��������� �������������� �����������");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_USE", "������������ ������� �������������� �����������");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_DIMENSIONS", "����������������� ���������� (������ x ������)");

define("CONTROL_SETTINGSFILE_DOCHANGE_ERROR_NAME", "�������� ������� �� ����� ���� ������!");

define("NETCAT_AUTH_TYPE_LOGINPASSWORD", "���� �� ������/������");
define("NETCAT_AUTH_TYPE_TOKEN", "���� �� e-token");
define("CONTROL_AUTH_HTML_CMS", "������� ���������� �������");
define("CONTROL_AUTH_ON_ONE_SITE", "�������������� �� �����");
define("CONTROL_AUTH_ON_ALL_SITES", "�� ���� ������");
define("CONTROL_AUTH_HTML_LOGIN", "�����");
define("CONTROL_AUTH_HTML_PASSWORD", "������");
define("CONTROL_AUTH_HTML_PASSWORDCONFIRM", "������ ��� ���");
define("CONTROL_AUTH_HTML_SAVELOGIN", "��������� ����� � ������");
define("CONTROL_AUTH_HTML_LANG", "����");
define("CONTROL_AUTH_HTML_AUTH", "��������������");
define("CONTROL_AUTH_HTML_BACK", "���������");
define("CONTROL_AUTH_FIELDS_NOT_EMPTY", "���� \"".CONTROL_AUTH_HTML_LOGIN."\" � \"".CONTROL_AUTH_HTML_PASSWORD."\" �� ����� ���� �������!");
define("CONTROL_AUTH_LOGIN_NOT_EMPTY", "���� \"".CONTROL_AUTH_HTML_LOGIN."\" �� ����� ���� ������!");
define("CONTROL_AUTH_LOGIN_OR_PASSWORD_INCORRECT", "��������������� ������ �������!");
define("CONTROL_AUTH_PIN_INCORRECT", "������ �������� PIN ���!");
define("CONTROL_AUTH_TOKEN_PLUGIN_DONT_INSTALL", "������ �� ����������");
define("CONTROL_AUTH_KEYPAIR_INCORRECT", "������ ��� �������� �������� ����");
define("CONTROL_AUTH_USB_TOKEN_NOT_INSERTED", "USB-����� �����������");
define("CONTROL_AUTH_TOKEN_CURRENT_TOKENS", "������� ����������� ������ ������������");
define("CONTROL_AUTH_TOKEN_NEW", "��������� ����� �����");
define("CONTROL_AUTH_TOKEN_PLUGIN_ERROR", "� �������� �� ���������� <a href='http://www.rutoken.ru/hotline/download/' target='_blank'>������ ��� ������ � �������</a>");
define("CONTROL_AUTH_TOKEN_MISS", "����� �����������");
define("CONTROL_AUTH_TOKEN_NEW_BUTTON", "���������");

define("CONTROL_AUTH_JS_REQUIRED", "��� ������ � ������� ����������������� ���������� �������� ��������� javascript");

define("NETCAT_MODULE_AUTH_INSIDE_ADMIN_ACCESS", "������ � ���� �����������������");
define("CONTROL_AUTH_MSG_MUSTAUTH", "��� ����������� ���������� ������ ����� � ������.");


define("CONTROL_FS_NAME_SIMPLE", "�������");
define("CONTROL_FS_NAME_ORIGINAL", "�����������");
define("CONTROL_FS_NAME_PROTECTED", "����������");

define("CONTROL_CLASS_CLASS_TEMPLATE", "������ ������ ���������");
define("CONTROL_CLASS_CLASS_TEMPLATE_CHANGE_LATER", "������ ��������� ��������� �� ������� �������� ����� ���������� �������.");
define("CONTROL_CLASS_CLASS_TEMPLATE_DEFAULT", "������ �� ���������");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE", "������ ������ � ������ ��������������");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADMIN_MODE", "������ ������ � ������ �����������������");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE_DONT_USE", "-- �� ������������ ��������� ������ --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADD", "�������� ������");
define("CONTROL_CLASS_CLASS_DONT_USE_TEMPLATE", "-- �� ������������ ������ --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NAME", "������� �������� ������� ����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NOT_FOUND", "������� ���������� �����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_DELETE_WARNING", "��������: ������ �������� ����� �������������� �������� ��������� \"%s\".");
define("CONTROL_CLASS_CLASS_TEMPLATE_NOT_FOUND", "������ � ��������������� %s �� ������!");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_ADD", "������ ���������� ������� ����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_EDIT", "������ �������������� ������� ����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_ADD", "������ ���������� ������� ��������");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_EDIT", "������ ���������� ������� �������");
define("CONTROL_CLASS_CLASS_TEMPLATE_GROUP", "������� �����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_BUTTON_EDIT", "�������������");
define("CONTROL_CLASS_CLASS_TEMPLATES", "������� ����������");
define("CONTROL_CLASS_CLASS_TEMPLATE_RECORD_TEMPLATE_WARNING", "��������! ���� �� ������ ��������� � ���� ���� ��������, � �� �������� � ��� �������� �� ������ ������, �� �� ������� ������� �� �������� ������� ������ �������.<br>�������, ��� ������ ����������?");
define("CLASS_TEMPLATE_TAB_EDIT", "�������������� �������");
define("CLASS_TEMPLATE_TAB_DELETE", "�������� �������");
define("CLASS_TEMPLATE_TAB_INFO", "���������");

define("CONTROL_CLASS", "����������");
define("CONTROL_CLASS_ADD_ACTION", "���������� ����������");
define("CONTROL_CLASS_DELETECOMMIT", "������������� �������� ����������");
define("CONTROL_CLASS_DOEDIT", "�������������� ����������");
define("CONTROL_CLASS_CONTINUE", "����������");
define("CONTROL_CLASS_NONE", "���������� �����������.");
define("CONTROL_CLASS_ADD", "�������� ���������");
define("CONTROL_CLASS_ADD_FS", "�������� ��������� 5.0");
define("CONTROL_CLASS_CLASS", "���������");
define("CONTROL_CLASS_SYSTEM_TABLE", "��������� �������");
define("CONTROL_CLASS_ACTIONS", "������� ��������");
define("CONTROL_CLASS_FIELD", "����");
define("CONTROL_CLASS_FIELDS", "����");
define("CONTROL_CLASS_FIELDS_COUNT", "�����");
define("CONTROL_CLASS_CUSTOM", "���������������� ���������");
define("CONTROL_CLASS_DELETE", "�������");
define("CONTROL_CLASS_NEWCLASS", "����� ���������");
define("CONTROL_CLASS_NEWTEMPLATE", "����� ������");
define("CONTROL_CLASS_TO_FS", "����� � �������� �������");

define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_ADDCLASS", "�������� ���������");
define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_IMPORTCLASS", "������������� ���������");

define("CONTROL_CLASS_ACTIONS_VIEW", "��������");
define("CONTROL_CLASS_ACTIONS_ADD", "����������");
define("CONTROL_CLASS_ACTIONS_EDIT", "���������");
define("CONTROL_CLASS_ACTIONS_CHECKED", "���������");
define("CONTROL_CLASS_ACTIONS_SEARCH", "�����");
define("CONTROL_CLASS_ACTIONS_MAIL", "��������");
define("CONTROL_CLASS_ACTIONS_DELETE", "��������");
define("CONTROL_CLASS_ACTIONS_MODERATE", "�������������");
define("CONTROL_CLASS_ACTIONS_ADMIN", "�����������������");

define("CONTROL_CLASS_INFO_ADDSLASHES", "���������� ���������, �� �������� <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes'); return false;\">������������ �����������</a>.");
define("CONTROL_CLASS_ERRORS_DB", "������ ������� �� ����!");
define("CONTROL_CLASS_CLASS_NAME", "��������");
define("CONTROL_CLASS_CLASS_KEYWORD", "�������� �����");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_LABEL", "����, ���������� �������� �������");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_NOT_SELECTED", "�� �������");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_SINGULAR", "�������� ������� � ������������ ����� ������������ ������ (��������� <em>���</em>?�)");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_PLURAL", "�������� ������� �� ������������� ����� ������������ ������ (�������� ��� <em>���</em>?�)");
define("CONTROL_CLASS_CLASS_MAIN_CLASSTEMPLATE_LABEL", "�������� ������ ����������");
define("CONTROL_CLASS_CLASS_GROUPS", "������ �����������");
define("CONTROL_CLASS_CLASS_NO_GROUP", "��� ������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST", "������ ����������� ������ ��������");
define("CONTROL_CLASS_CLASS_DESCRIPTION", "�������� ���������");
define("CONTROL_CLASS_CLASS_SETTINGS", "��������� ���������");
define("CONTROL_SCLASS_ACTION", "������� ��������");
define("CONTROL_SCLASS_TABLE", "�������");
define("CONTROL_SCLASS_TABLE_NAME", "�������� �������");
define("CONTROL_SCLASS_LISTING_NAME", "�������� ������");
define("CONTROL_CLASS_CLASSFORM_INFO_FOR_NEWCLASS", "���������� � ����������");
define("CONTROL_CLASS_CLASSFORM_MAININFO", "�������� ����������");
define("CONTROL_CLASS_CLASSFORM_TEMPLATE_PATH", "����� ���������� ��������� � ����� <a href='%s'>%s</a>");
define("CONTROL_CLASS_SITE_STYLES", "����� ��� �����");
define("CONTROL_CLASS_SITE_STYLES_DISABLED_WARNING", "������ ��������� �������� � ������ ������������� � NetCat 5.6, ���������� CSS-������ � ������� ����������.");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_BUTTON", "�������� ����� ������� ����������");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_WARNING",
    "����� ���������� ������ ������������� � NetCat 5.6 ����� ����������� �������������� ��������
    (����-������ <code>&lt;div&gt;</code>) ��� ������ ������ � �������������� ������� �������:
    <ul><li>������� �������� �� ����������, 
    <li>�������� ����� �������� ������� ������ �������, 
    <li>���� ����������, ��������� � ������.</ul>
    � ����������� �� ������������ ������������ �� ������������ ������ CSS-������ ����� 
    ������������ ��������������� ��������� CSS-������.");
define("CONTROL_CLASS_SITE_STYLES_DOCS_LINK", "��������� � ������ ����������� ��. <a href='%s' target='_blank'>� ������������</a>.");
define("CONTROL_CLASS_MULTIPLE_MODE_SWITCH", "������������� ��� ������������� � ������ ����������� ���������� ������ �� ��������");
define("CONTROL_CLASS_TEMPLATE_MULTIPLE_MODE_SWITCH", "������ ������������� ��� ������������� � ������ ����������� ���������� ������ �� ��������");
define("CONTROL_CLASS_LIST_PREVIEW", "����� ����������� ������ �������� (.png)");
define("CONTROL_CLASS_LIST_PREVIEW_NONE", "����� �����������");

define("CONTROL_CLASS_KEYWORD_ONLY_DIGITS", "�������� ����� �� ����� �������� ������ �� ����");
define("CONTROL_CLASS_KEYWORD_TOO_LONG", "����� ��������� ����� �� ����� ���� ����� %d ��������");
define("CONTROL_CLASS_KEYWORD_INVALID_CHARACTERS", "�������� ����� ����� ��������� ������ ����� ���������� ��������, ����� � ������� �������������");
define("CONTROL_CLASS_KEYWORD_NON_UNIQUE", "�������� ����� �%s� ��� ��������� ���������� �%s�");
define("CONTROL_CLASS_KEYWORD_TEMPLATE_NON_UNIQUE", "�������� ����� �%s� ��� ��������� ������� �%s�");
define("CONTROL_CLASS_KEYWORD_RESERVED", "���������� ������������ �%s� � �������� ��������� �����, ��� ��� ��� �������� �����������������");

define("CONTROL_CLASS_CLASSFORM_CHECK_ERROR", "<div style='color: red;'>������ ���� � ���� &laquo;<i>%s</i>&raquo; ����������.</div>");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_PREFIX", "������� ������ ��������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_BODY", "������ � ������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SUFFIX", "������� ������ ��������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW", "���������� ��");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ", "�������� �� ��������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW_NUM", "���������� �������� �� ��������");
define("CONTROL_CLASS_CLASS_MIN_RECORDS", "����������� ���������� �������� � ���������");
define("CONTROL_CLASS_CLASS_MAX_RECORDS", "������������ ���������� �������� � ���������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORT", "����������� ������� �� ���� (�����)");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_TITLE", "��������� ��������");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_WRONG_NC_CTPL", "� nc_object_list(%s, %s) ������� ��������� nc_ctpl - %s. ");
define("CONTROL_CLASS_CLASS_OBJECTFULL_WRONG_NC_CTPL", "������� ��������� nc_ctpl - %s. ");

define("CONTROL_CLASS_CLASS_OBJECTVIEW", "������ ����������� ������ ������� �� ��������� ��������");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_DOPL", "�������������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_CACHE", "�����������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_SYSTEM", "��������� ���������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_BR", "������� ������ � &lt;BR&gt;");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_HTML", "��������� HTML-����");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGETITLE", "��������� ��������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_USEASALT", "������������ ��� ��������� �������������� ���������");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGEBODY", "����������� �������");
define("CONTROL_CLASS_CLASS_CREATENEW_BASICOLD", "������� ����� ��������� �� ������ �������������");
define("CONTROL_CLASS_CLASS_CREATENEW_CLEARNEW", "������� ����� ��������� &quot;� ����&quot;");
define("CONTROL_CLASS_CLASS_DELETE_WARNING", "��������: ���������%s � ��� � ���%s ��������� ����� �������.");
define("CONTROL_CLASS_CLASS_NOT_FOUND", "��������� � ��������������� %s �� ������!");

define("CONTROL_CLASS_CUSTOM_SETTINGS_TEMPLATE", "��������� ����������� ���������� �������");
define("CONTROL_CLASS_CUSTOM_SETTINGS_PARAMETER", "��������");
define("CONTROL_CLASS_CUSTOM_SETTINGS_DEFAULT", "�� ���������");
define("CONTROL_CLASS_CUSTOM_SETTINGS_VALUE", "��������");
define("CONTROL_CLASS_CUSTOM_SETTINGS_HAS_ERROR", "���� ��� ��������� �������� ������� �����������. ����������, ��������� ������.");

define("CONTROL_CLASS_IMPORT", "������ ����������");
define("CONTROL_CLASS_IMPORTS", "������ �����������");
define("CONTROL_CLASS_IMPORT_UPLOAD", "��������");
define("CONTROL_CLASS_IMPORT_ERROR_NOTUPLOADED", "���� �� �������.");
define("CONTROL_CLASS_IMPORT_ERROR_CANNOTBEINSTALLED", "��������� �� ����� ���� ����������.");
define("CONTROL_CLASS_IMPORT_ERROR_VERSION_ID", "��������� ��� ������ %s, ������� ������ %s.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_VERSION_ID", "������ ������� �� ������� ��� �������� ������ �����.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_FILES", "����������� ������ ��� �������� ������ �������� ����������.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_IMPORT", "������ �������� ����������, ������ ���������� �� ���������.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_TEMPLATE_IMPORT", "������ �������� �������� ����������, ������ �������� �� ���������.");
define("CONTROL_CLASS_IMPORT_ERROR_MESSAGE_TABLE", "������ �������� ������� ������ ����������.");
define("CONTROL_CLASS_IMPORT_ERROR_FIELD", "������ �������� ����� ����������.");

define("CONTROL_CLASS_CONVERT", "��������������� ����������");
define("CONTROL_CLASS_CONVERT_BUTTON", "�������������� � 5.0");
define("CONTROL_CLASS_CONVERT_BUTTON_UNDO", "�������� ���������������");
define("CONTROL_CLASS_CONVERT_DB_ERROR", "������ ��������� ����������� � ����");
define("CONTROL_CLASS_CONVERT_OK", "����������� �������");
define("CONTROL_CLASS_CONVERT_OK_GOEDIT", "������� � �������������� ����������");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE", "����� ��������������� ��������� ���������� � �� �������");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE_UNDO", "����� �������� ����������� ��������� ����������� � �� ��������");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE", "����� ������� ����� � ������� �������� v5, ������� ����� ������� v4 � ������ class_40_backup.html");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE_UNDO", "���������� ����� ������� ����� � ������� �������� 5.0(�������������)");
define("CONTROL_CLASS_CONVERT_NOTICE", "����� ����������� ���������� ����� ���������� ������ ���������� � ��� ��������!
                    ����������� ������� ���� �� ����� ���������.");
define("CONTROL_CLASS_CONVERT_NOTICE_UNDO", "����� ������ ����������� ��������� �������� � ��������� �� �����������, ��� ��������� � ������ 5.0 ����������!");
define("CONTROL_CLASS_CONVERT_UNDO_FILE_ERROR","��� ������ ��� ��������������");

define("CONTROL_CLASS_NEWGROUP", "����� ������");
define("CONTROL_CLASS_EXPORT", "�������������� ��������� � ����");
define("CONTROL_CLASS_AUXILIARY_SWITCH", "��������� ���������");
define("CONTROL_CLASS_AUXILIARY", "(���������)");
define("CONTROL_CLASS_BLOCK_MARKUP_SWITCH", "��������� <a href='https://netcat.ru/developers/docs/components/stylesheets/' target='_blank'>�������������� ��������</a>");
define("CONTROL_CLASS_BLOCK_LIST_MARKUP_SWITCH", "��������� �������� ������ ������ �������� (����������� ����������� ���������� ������ ����� ����������)");
define("CONTROL_CLASS_BLOCK_MARKUP_SWITCH_WARNING", "�������������� �������� ���������� ��� ��������� ������ ������� ���������� � ���������� ������ ���������� � ������ ��������������.");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_RSS_DOESNT_EXIST", "Rss-����� %s�� ��������, ��������� ����������� ������ ���������� ��� rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_XML_DOESNT_EXIST", "Xml-�������� %s�� ��������, ��������� ����������� ������ ���������� ��� xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_TRASH_DOESNT_EXIST", "����� ������� �� ��������, ��������� ����������� ������ ���������� ��� �������");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE", "���");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_CLASSTEMPLATE", "��� ������� ����������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MULTI_EDIT", "������������� ��������������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RSS", "RSS");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_XML", "XML-��������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TRASH", "��� ������� ��������� ��������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_USEFUL", "�������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_INSIDE_ADMIN", "����� �����������������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_ADMIN_MODE", "����� ��������������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TITLE", "��� ��������� ��������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MOBILE", "���������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RESPONSIVE", "����������");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_AUTO", "�������������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_EMPTY", "������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_ADD_PARAMETRS", "��������� ���������� ������� ����������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_BASE", "������� ������ ���������� �� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_TRASH", "������� ������ ��� �������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_RSS", "������� ������ ��� rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_XML", "������� ������ ��� xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_RSS", "�������� rss-�����");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_XML", "�������� xml-��������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_VIEW", "����������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_EDIT", "���������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ERROR", "������ �������� ������� ����������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_USEFUL", "������ ���������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RSS", "������ ���������� ��� RSS ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_XML", "������ ���������� ������� ��� XML ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TRASH", "������ ���������� ��� ������� ��������� �������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_INSIDE_ADMIN", "������ ���������� ��� ������ �������������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ADMIN_MODE", "������ ���������� ��� ������ ����������������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TITLE", "������ ���������� ��� ��������� �������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MOBILE", "������ ���������� ��� ���������� ����� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MULTI_EDIT", "������ ���������� ��� �������������� �������������� ������� ������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RESPONSIVE", "������ ���������� ��� ����������� ����� ������� ������");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_SUB", "���������</a> � ��������� �������");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_TRASH", "���������</a> � �������");
define("CONTROL_CLASS_SHOW_AUX", "�������� ��������� ����������");
define("CONTROL_CLASS_DEFAULT_CHANGE", "��������� �� ��������� ����� �������� � ���������� �����.");

define("CONTROL_CONTENT_CLASS_SUCCESS_ADD", "��������� ������� ��������");
define("CONTROL_CONTENT_CLASS_ERROR_ADD", "������ ���������� ����������");
define("CONTROL_CONTENT_CLASS_ERROR_NAME", "������� �������� ����������");
define("CONTROL_CONTENT_CLASS_GROUP_ERROR_NAME", "�������� ������ �� ������ ���������� � �����");
define("CONTROL_CONTENT_CLASS_SUCCESS_EDIT", "��������� ������� �������");
define("CONTROL_CONTENT_CLASS_ERROR_EDIT", "������ �������������� ����������");

#TYPE OF DATA
define("CLASSIFICATOR_TYPEOFDATA_STRING", "������");
define("CLASSIFICATOR_TYPEOFDATA_INTEGER", "����� �����");
define("CLASSIFICATOR_TYPEOFDATA_TEXTBOX", "��������� ����");
define("CLASSIFICATOR_TYPEOFDATA_LIST", "������");
define("CLASSIFICATOR_TYPEOFDATA_BOOLEAN", "���������� ���������� (������ ��� ����)");
define("CLASSIFICATOR_TYPEOFDATA_FILE", "����");
define("CLASSIFICATOR_TYPEOFDATA_FLOAT", "����� � ��������� �������");
define("CLASSIFICATOR_TYPEOFDATA_DATETIME", "���� � �����");
define("CLASSIFICATOR_TYPEOFDATA_RELATION", "����� � ������ ��������");
define("CLASSIFICATOR_TYPEOFDATA_MULTILIST", "������������� �����");
define("CLASSIFICATOR_TYPEOFDATA_MULTIFILE", "������������� �������� ������");

define("CLASSIFICATOR_TYPEOFFILESYSTEM", "��� �������� �������");

define("CLASSIFICATOR_TYPEOFEDIT_ALL", "�������� ����");
define("CLASSIFICATOR_TYPEOFEDIT_ADMINS", "�������� ������ ���������������");
define("CLASSIFICATOR_TYPEOFEDIT_NOONE", "���������� ������");

define("CLASSIFICATOR_TYPEOFMODERATION_RIGHTAWAY", "����� ����������");
define("CLASSIFICATOR_TYPEOFMODERATION_MODERATION", "����� �������� ���������������");

define("CLASSIFICATOR_USERGROUP_ALL", "���");
define("CLASSIFICATOR_USERGROUP_REGISTERED", "������������������");
define("CLASSIFICATOR_USERGROUP_AUTHORIZED", "��������������");

define("CONTROL_TEMPLATE_CLASSIFICATOR", "������������� ������������");
define("CONTROL_TEMPLATE_CLASSIFICATOR_EKRAN", "������������");
define("CONTROL_TEMPLATE_CLASSIFICATOR_RES", "���������");

define("CONTROL_FIELD_LIST_NAME", "�������� ����");
define("CONTROL_FIELD_LIST_NAMELAT", "�������� ���� (���������� �������)");
define("CONTROL_FIELD_LIST_DESCRIPTION", "��������");
define("CONTROL_FIELD_LIST_ADD", "�������� ����");
define("CONTROL_FIELD_LIST_CHANGE", "��������� ���������");
define("CONTROL_FIELD_LIST_DELETE", "������� ����");
define("CONTROL_FIELD_ADDING", "���������� ����");
define("CONTROL_FIELD_EDITING", "�������������� ����");
define("CONTROL_FIELD_DELETING", "�������� ����");
define("CONTROL_FIELD_FIELDS", "����");
define("CONTROL_FIELD_LIST_NONE", "� ������ ���������� ��� �� ������ ����.");
define("CONTROL_FIELD_ONE_FORMAT", "������");
define("CONTROL_FIELD_ONE_FORMAT_NONE", "���");
define("CONTROL_FIELD_ONE_FORMAT_EMAIL", "email");
define("CONTROL_FIELD_ONE_FORMAT_URL", "URL");
define("CONTROL_FIELD_ONE_FORMAT_HTML", "HTML-������");
define("CONTROL_FIELD_ONE_FORMAT_PASSWORD", "������");
define("CONTROL_FIELD_ONE_FORMAT_PHONE", "�������");
define("CONTROL_FIELD_ONE_FORMAT_TAGS", "����");
define("CONTROL_FIELD_ONE_PROTECT_EMAIL", "�������� ��� ������");
define("CONTROL_FIELD_ONE_EXTENSION", "��������� ����");
define("CONTROL_FIELD_ONE_MUSTBE", "����������� ��� ����������");
define("CONTROL_FIELD_ONE_INDEX", "�������� ����� �� ������� ����");
define("CONTROL_FIELD_ONE_IN_TABLE_VIEW", "������������ � ��������� ������ ��������");
define("CONTROL_FIELD_ONE_INHERITANCE", "����������� �������� ����");
define("CONTROL_FIELD_ONE_DEFAULT", "�������� �� ��������� (��������������� ��� ������, ���� ���� �� ���� ���������)");
define("CONTROL_FIELD_ONE_DEFAULT_NOTE", "��� ���� ����� ����� ����� &quot;".CLASSIFICATOR_TYPEOFDATA_TEXTBOX."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_FILE."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_DATETIME."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_MULTILIST."&quot;");
define("CONTROL_FIELD_ONE_FTYPE", "��� ����");
define("CONTROL_FIELD_ONE_ACCESS", "��� ������� � ����");
define("CONTROL_FIELD_ONE_RESERVED", "������ �������� ���� ���������������!");
define('CONTROL_FIELD_NAME_ERROR', '�������� ���� ������ ��������� ������ ��������� ����� � �����!');
define('CONTROL_FIELD_DIGIT_ERROR', '�������� ���� �� ����� ���������� � �����.');
define('CONTROL_FIELD_DB_ERROR', '������ ������ � ��.');
define('CONTROL_FIELD_EXITS_ERROR', '����� ���� ��� ����������.');
define('CONTROL_FIELD_FORMAT_ERROR', '����� ������ ���� �� ��������.');
define("CONTROL_FIELD_MSG_ADDED", "���� ��������� �������.");
define("CONTROL_FIELD_MSG_EDITED", "���� ������� ��������.");
define("CONTROL_FIELD_MSG_DELETED_ONE", "���� ������� �������.");
define("CONTROL_FIELD_MSG_DELETED_MANY", "���� ������� �������.");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_ONE", "��������: ���� ����� �������.");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_MANY", "��������: ���� ����� �������.");
define("CONTROL_FIELD_MSG_FIELDS_CHANGED", "���������� ����� ��������.");
define("CONTROL_FIELD_CONFIRM_REMOVAL", "����������� ��������");
define('CONTROL_FIELD__EDITOR_EMBED_TO_FIELD', '�������� �������� � ���� ��� ��������������');
define('CONTROL_FIELD__TEXTAREA_SIZE', '������ ���������� �����');
define('CONTROL_FIELD_HEIGHT', '������');
define('CONTROL_FIELD_WIDTH', '������');
define('CONTROL_FIELD_ATTACHMENT', '������������');
define('CONTROL_FIELD_DOWNLOAD_COUNT', '������� ���������� ����������');
define('CONTROL_FIELD_CAN_BE_AN_ICON', '����� ���� �������');
define('CONTROL_FIELD_CAN_BE_ONLY_ICON', '������ �������');
define('CONTROL_FIELD_PANELS', '������������ ����� ������� CKEditor');
define('CONTROL_FIELD_PANELS_DEFAULT', '�� ���������');
define('CONTROL_FIELD_TYPO', '���������������');
define('CONTROL_FIELD_TYPO_BUTTON', '��������������� �����');
define('CONTROL_FIELD_BBCODE_ENABLED', '��������� bb-����');
define('CONTROL_FIELD_USE_CALENDAR', '������������ ��������� ��� ������ ����');
define('CONTROL_FIELD_FILE_UPLOADS_LIMITS', '���� ������������ PHP ����� ��������� ����������� �� �������� ������:');
define('CONTROL_FIELD_FILE_POSTMAXSIZE', '����������� ���������� ������ ������, ������������ ������� POST');
define('CONTROL_FIELD_FILE_UPLOADMAXFILESIZE', '������������ ������ ������������� �����');
define('CONTROL_FIELD_FILE_MAXFILEUPLOADS', '����������� ���������� ������������ ������������ ������');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_RESIZE', '������������ ���������� �����������');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_CROP', '������������ ������� �����������');
define('CONTROL_FIELD_MULTIFIELD_CROP_IGNORE', '�� ��������, ���� ����������� ������ ���������� �������');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_PREVIEW', '��������� ��������-������������(������)');
define('CONTROL_FIELD_MULTIFIELD_USE_PREVIEW_RESIZE', '������������ ���������� ������');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_USE_IMAGE_CROP', '������������ ������� ������');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_CROP_IGNORE', '�� ��������, ���� ������ ������ ���������� �������');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_WIDTH', '������');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_HEIGHT', '������');
define('CONTROL_FIELD_MULTIFIELD_CROP_CENTER', '�� ������');
define('CONTROL_FIELD_MULTIFIELD_CROP_COORD', '�� �����������');
define('CONTROL_FIELD_MULTIFIELD_MIN', '�������');
define('CONTROL_FIELD_MULTIFIELD_MAX', '��������');
define('CONTROL_FIELD_MULTIFIELD_MINMAX', '���������� ���������� ������ ��������� ��� ��������');
define('CONTROL_FIELD_USE_TRANSLITERATION', '��������������');
define('CONTROL_FIELD_TRANSLITERATION_FIELD', '���� ��� ������ ���������� ��������������');
define('CONTROL_FIELD_USE_URL_RULES', '������������ ������� ��� URL');
define('CONTROL_FIELD_FILE_WRONG_GD', '�� ������� �� �������� ���������� GD2, ���������� � ������� ����������� �������� �� �����');

# SYS
define("TOOLS_SYSTABLE_SITES", "�����");
define("TOOLS_SYSTABLE_SECTIONS", "�������");
define("TOOLS_SYSTABLE_USERS", "������������");
define("TOOLS_SYSTABLE_TEMPLATE", "������ �������");


#DATABACKUP
define("TOOLS_DATA_BACKUP",                            "�������/������ ������");
define("TOOLS_DATA_BACKUP_IMPORT_FILE",                "���� ������� (*.tgz)");
define("TOOLS_DATA_BACKUP_IMPORT_COMPLETE",            "������ ������ ��������!");
define("TOOLS_DATA_BACKUP_IMPORT_ERROR",               "�� ����� ������� ������ ��������� ������!");
define("TOOLS_DATA_BACKUP_IMPORT_DUPLICATE_KEY_ERROR", "������� � ������ ���������������� ��� ����������.");
define("TOOLS_DATA_BACKUP_EXPORT_COMPLETE",            "������� ������ ��������!");
define("TOOLS_DATA_BACKUP_INCOMPATIBLE_VERSION",       "���� ������� ����� ������, ������� �� �������������� � ������� ������ NetCat. ����������, �������� ���� ����� �������.");
define("TOOLS_DATA_SAVE_IDS",                          "��������� �������������� ������������� ��������");
define("TOOLS_DATA_BACKUP_SYSTEM",                     "���������");
define("TOOLS_DATA_BACKUP_DATATYPE",                   "��� ������");
define("TOOLS_DATA_BACKUP_INSERT_OBJECTS",             "��������� ������� � ��");
define("TOOLS_DATA_BACKUP_CREATE_TABLES",              "������� ������ � ��");
define("TOOLS_DATA_BACKUP_COPIED_FILES",               "��������� ������/�����");
define("TOOLS_DATA_BACKUP_SKIPPED_FILES",              "��������� ������/�����");
define("TOOLS_DATA_BACKUP_REPLACED_FILES",             "�������� ������/�����");
define("TOOLS_DATA_BACKUP_EXPORT_DATE",                "���� ��������");
define("TOOLS_DATA_BACKUP_USED_SPACE",                 "������������");
define("TOOLS_DATA_BACKUP_SPACE_FROM",                 "��");

define("TOOLS_DATA_BACKUP_DELETE_ALL_CONFIRMATION", "������� ��� �����?");

define("TOOLS_EXPORT",                  "�������");
define("TOOLS_IMPORT",                  "������");
define("TOOLS_DOWNLOAD",                "���������");
define("TOOLS_DATA_BACKUP_GOTO_OBJECT", "������� � ���������������� �������");


define("TOOLS_MODULES", "������");
define("TOOLS_MODULES_LIST", "������ �������");
define("TOOLS_MODULES_INSTALLEDMODULE", "���������� ������");
define("TOOLS_MODULES_ERR_INSTALL", "��������� ������ ����������");
define("TOOLS_MODULES_ERR_UNINSTALL", "�������� ������ ����������");
define("TOOLS_MODULES_ERR_CANTOPEN", "���������� ������� ����");
define("TOOLS_MODULES_ERR_PATCH", "�� ���������� ����������� ���� � �������");
define("TOOLS_MODULES_ERR_VERSION", "������ �� ��� ������������ ������");
define("TOOLS_MODULES_ERR_INSTALLED", "������ ��� ����������");
define("TOOLS_MODULES_ERR_ITEMS", "������: ��������� �� ��� ����������� �������");
define("TOOLS_MODULES_ERR_DURINGINSTALL", "������ ��� �����������");
define("TOOLS_MODULES_ERR_NOTUPLOADED", "���� �� �������");
define("TOOLS_MODULES_ERR_EXTRACT", "������ ��� ���������� ������ c �������.<br />���������� ����������� ���������� ������ � ������� � ����� $TMP_FOLDER �� ����� ������� � ����� ��������� ��������� ��������� ������.");

define("TOOLS_MODULES_MOD_NAME", "�������� ������");
define("TOOLS_MODULES_MOD_PREFS", "���������");
define("TOOLS_MODULES_MOD_GOINSTALL", "��������� ���������");
define("TOOLS_MODULES_MOD_EDIT", "�������� ��������� ������");
define("TOOLS_MODULES_MOD_LOCAL", "��������� ������ � ���������� �����");
define("TOOLS_MODULES_MOD_INSTALL", "��������� ������");
define("TOOLS_MODULES_MSG_CHOISESECTION", "��� ���������� ��������� ������ ���������� ������� �������������� �������. ��� ���������� ������� ������������ ������, ��� ����� ������� ����������� ����������.");
define("TOOLS_MODULES_PREFS_SAVED", "��������� ������� ���������");
define("TOOLS_MODULES_PREFS_ERROR", "������ �� ����� ���������� �������� ������");

# PATCH
define("TOOLS_PATCH", "���������� �������");
define("TOOLS_PATCH_INSTRUCTION_TAB", "����������");
define("TOOLS_PATCH_INSTRUCTION", "���������� �� ��������� ����������");
define("TOOLS_PATCH_CHEKING", "�������� ������� ����� ����������");
define("TOOLS_PATCH_MSG_OK", "��� ����������� ���������� �����������.");
define("TOOLS_PATCH_MSG_NOCONNECTION", "�� ������� ����������� � �������� ����������. � ������� ����� ���������� �� ������ ������ �� <a href='https://partners.netcat.ru/forclients/update/' target='_blank'>����� �����</a>.");
define("TOOLS_PATCH_ERR_CANTINSTALL", "����������� ����� ����������.");
define("TOOLS_PATCH_INSTALL_LOCAL", "��������� ���������� � ���������� �����");
define("TOOLS_PATCH_INSTALL_ONLINE", "��������� ���������� � ������������ �����");
define("TOOLS_PATCH_INFO_NOTINSTALLED", "�� ����������� ����������");
define("TOOLS_PATCH_INFO_LASTCHECK", "��������� �������� ���� ������������");
define("TOOLS_PATCH_INFO_REFRESH", "�������� ��������");
define("TOOLS_PATCH_INFO_DOWNLOAD", "�������");
define("TOOLS_PATCH_ERR_EXTRACT", "������ ��� ���������� ������ c �����������.<br />���������� ����������� ���������� ������ � ����������� � ����� $TMP_FOLDER �� ����� ������� � ����� ��������� ��������� ����������.");
define("TOOLS_PATCH_ERROR_TMP_FOLDER_NOT_WRITABLE", "���������� ����� �� ������ ��� ����� %s.<br />(%s ���������� ��� ������)");
define("TOOLS_PATCH_ERROR_FILELIST_NOT_WRITABLE", "��������� �����, ��������� ����������, ������ ����� ������������� ��������.");
define("TOOLS_PATCH_ERROR_AUTOINSTALL", "�������������� ��������� ���������� ����������, ���������� ���������� �������, �������� ������������� ������������ ��� ������������ �� �����.");
define("TOOLS_PATCH_ERROR_UPDATE_SERVER_NOT_AVAILABLE", "�� ������� ����������� � �������� ����������, ��������� ������� �����.<br />" .
    "���� ������ � ���������� ���� ������ �������������� ����� ������-������, " .
    "<a href='{$nc_core->ADMIN_PATH}#system.edit' target='_top'>��������� ��� ���������</a>.");
define("TOOLS_PATCH_ERROR_UPDATE_FILE_NOT_AVAILABLE", "���� ���������� �� ����� ���� �������, ��������� ������� �����. ���� ������ ����������, ���������� � ������ ���������.");
define("TOOLS_PATCH_DOWNLOAD_LINK_DESCRIPTION", "������ �� ���� ����������");
define("TOOLS_PATCH_IS_WRITABLE", "������ �� ������");

# patch after install information
define("TOOLS_PATCH_INFO_FILES_COPIED", "[%COUNT] ������ �����������.");
define("TOOLS_PATCH_INFO_QUERIES_EXEC", "[%COUNT] MySQL �������� ����������.");
define("TOOLS_PATCH_INFO_SYMLINKS_EXEC", "[%COUNT] ������������� ������ �������.");

define("TOOLS_PATCH_LIST_DATE", "���� ���������");
define("TOOLS_PATCH_ERROR", "������");
define("TOOLS_PATCH_ERROR_DURINGINSTALL", "������ ��� �����������");
define("TOOLS_PATCH_INSTALLED", "���� ����������");
define("TOOLS_PATCH_INVALIDVERSION", "���� �� ������������ ��� ������������ ������ ������� NetCat, ������� ������ %EXIST, ���� ��� ������ %REQUIRE.");
define("TOOLS_PATCH_ALREDYINSTALLED", "���� ��� ����������");

define("TOOLS_PATCH_NOTAVAIL_DEMO", "�� �������� � ����-������");
define("NETCAT_DEMO_NOTICE", "������� ���������� ������� NetCat %s DEMO");
define("NETCAT_PERSONAL_MODULE_DESCRIPTION", "����������� ����������� �������������� ������� ���������� ������ � ����������� ������.<br />
                                              ������� ���������� ������������ ��� ������ �� ������ ����� ���������� ��� ��������, ��� �� �����������.<br />
                                              <a href='https://netcat.ru/products/editions/compare/' target='_blank'>�������</a> ��������� ��������. ");

#UPGRADE
define("TOOLS_UPGRADE_ERR_NO_PRODUCTNUMBER", "� ������� ����������� ����� ��������");
define("TOOLS_UPGRADE_ERR_INVALID_PRODUCTNUMBER", "����� �� ������ �������� �� �������������. ������������� ������������ ������ ����� ��������");
define("TOOLS_UPGRADE_ERR_NO_MATCH_HOST", "������������ � ������� ���� ��������� �� ������ ��������. ����������� ������� �� ������ ������ �� �����������.");
define("TOOLS_UPGRADE_ERR_NO_ORDER", "��� ������ �������� �� ��������� ������ ��� �������� ������� �� ������� ��������.");
define("TOOLS_UPGRADE_ERR_NOT_PAID", "����� �� ������� ������� �� ������� �������� �� ������� �� netcat.ru.");

#ACTIVATION
define("TOOLS_ACTIVATION", "��������� �������");
define("TOOLS_ACTIVATION_INSTRUCTION", '���������� ��������� �������');
define("TOOLS_ACTIVATION_VERB", "������������");
define("TOOLS_ACTIVATION_OK", "��������� ������ �������");
define("TOOLS_ACTIVATION_OK1", "��������� ������ �������. �������� ������ ����-����!<br /><ul style='list-style-type:none'>");
define("TOOLS_ACTIVATION_OK2", "<li>- <a href='https://netcat.ru/' target='_blank'>�����������������</a> �� ����� netcat.ru</li>");
define("TOOLS_ACTIVATION_OK3", "<li>- <a href='https://netcat.ru/' target='_blank'>������� � ��� �������</a> �� ����� netcat.ru</li>");
define("TOOLS_ACTIVATION_OK4", "<li>- <a href='https://netcat.ru/forclients/want/zaregistrirovat-litsenziyu/?f_RegNum=%REGNUM&f_code=%REGCODE&f_SystemName=%SYSID' target='_blank'>��������� ��������</a> � ������ ��������, ������ ��������� ������:
 <ul style='list-style-type:none'><li>����� ��������: %REGNUM</li>
  <li>���� ���������: %REGCODE</li></ul></li></ul>
��� ���������� ��� ������������ (���� ��� ��� �����������), ��������� ������ ���������, ��������� ������������ � ���������� ������� �� ���������� ������.<br /><br />
� �������, ��� �� ������� ������!");
define("TOOLS_ACTIVATION_OWNER", "�������� ��������");
define("TOOLS_ACTIVATION_LICENSE", "����� ��������");
define("TOOLS_ACTIVATION_CODE", "���� ���������");
define("TOOLS_ACTIVATION_ALREADY_ACTIVE", "������� ��� ������������");
define("TOOLS_ACTIVATION_INPUT_KEY_CODE", "���������� ������ ����� �������� � ���� ���������");
define("TOOLS_ACTIVATION_INVALID_KEY_CODE", "�������� ��� ���� ��������� �� ������ ��������");
define("TOOLS_ACTIVATION_DAY", "���� �������� ����-������ �������� ����� %DAY ��.");
define("TOOLS_ACTIVATION_FORM", "��� ��������� ������� ��� ����� ������ ����� �������� � ���� ���������, ������� �� �������� ����� <a href='https://netcat.ru/products/editions/' target='_blank'>�������</a>");
define("TOOLS_ACTIVATION_DESC", "� ����������� ������:
<ul>
<li> ������ ���;</li>
<li> �������������� ���� �������� ��������;</li>
<li> ����������� ��������� �������� ����������� ������������ ����� �������� �� ������ ��������;</li>
<li> �������������� ��������� ����������;</li>
<li>������� ���������� ����������� ����������� ���������.</li>
</ul>");
//define("TOOLS_ACTIVATION_DEMO_DISABLED", "����������� ���������� ���������� ������ � ����������� ������.<br />");
define("TOOLS_ACTIVATION_REMIND_UNCOMPLETED", "������� ������ � ��������. ��������� ������� ��������� � ������� &laquo;<a href='%s'>��������� �������</a>&raquo;.");
define("TOOLS_ACTIVATION_LIC_DATA", "<h3>��������� ��������</h3>");
define("TOOLS_ACTIVATION_LIC_OWNER", "<h3>�������� ��������</h3>");

define("TOOLS_ACTIVATION_FORM_ERR_MANDATORY", "��������� ��� ����������� ����");
define("TOOLS_ACTIVATION_FORM_ERR_ORG_EMAIL", "�������� ������ email �����������");
define("TOOLS_ACTIVATION_FORM_ERR_PERSON_EMAIL", "�������� ������ email ����������� ����");
define("TOOLS_ACTIVATION_FORM_ERR_PRIMARY_EMAIL", "�������� ������ email");
define("TOOLS_ACTIVATION_FORM_ERR_ADDIT_EMAIL", "�������� ������ ��������������� email");
define("TOOLS_ACTIVATION_FORM_ERR_INN", "��� ������ ��������� 10 ��� 12 ����");

define("TOOLS_ACTIVATION_PLEASE_CHECK", "<p style='color: #ce655d;'>��������! �������� ���������� �������������� �� ��������� ������������ - ��������� �����.<br />���� �� ��������� ��� ��������� ��������-���������, ������� ��������� ��������� ���������/���������. �������� ��������� �������� ����� ��������� ����������.</p>");
define("TOOLS_ACTIVATION_FLD_OWNER", "�������� ��������");
define("TOOLS_ACTIVATION_FLD_PHIS", "���������� ����");
define("TOOLS_ACTIVATION_FLD_UR", "����������� ����");
define("TOOLS_ACTIVATION_FLD_NAME", "���");
define("TOOLS_ACTIVATION_FLD_PHIS_PHONE", "���������� �������");
define("TOOLS_ACTIVATION_FLD_PRIMARY_EMAIL", "Email");
define("TOOLS_ACTIVATION_FLD_ADDIT_EMAIL", "�������������� email");
define("TOOLS_ACTIVATION_FLD_ORGANIZATION", "�������� �����������");
define("TOOLS_ACTIVATION_FLD_INN", "���");
define("TOOLS_ACTIVATION_FLD_ORG_EMAIL", "Email �����������");
define("TOOLS_ACTIVATION_FLD_PHONE", "������� ��������");
define("TOOLS_ACTIVATION_FLD_DOMAINS", "������ �������� (������� ��������, ����� �������)");

define("REPORTS", "����� ���������� �������");
define("REPORTS_SECTIONS", "%d ������(��) (���������: %d)");
define("REPORTS_USERS", "%d ������������� (���������: %d)");
define("REPORTS_LAST_NAME", "�������� �������");
define("REPORTS_CLASS", "���������� �����������");
define("REPORTS_STAT_CLASS_SHOW", "�������� ����������");
define("REPORTS_STAT_CLASS_ALL", "���");
define("REPORTS_STAT_CLASS_DOGET", "�������");
define("REPORTS_STAT_CLASS_CLEAR", "��������");
define("REPORTS_STAT_CLASS_CLEARED", "������� �������");
define("REPORTS_STAT_CLASS_CONFIRM", "���������� �������� �������� �� ����������� �������");
define("REPORTS_STAT_CLASS_CONFIRM_OK", "�����");
define("REPORTS_STAT_CLASS_NOT_CC", "�� ������� ���������� � �������");
define("REPORTS_STAT_CLASS_USE", "������������");
define("REPORTS_STAT_CLASS_NOTUSE", "��������������");

define("REPORTS_SYSMSG_MSG", "���������");
define("REPORTS_SYSMSG_DATE", "����");
define("REPORTS_SYSMSG_NONE", "��� �� ������ ���������� ���������.");
define("REPORTS_SYSMSG_MARK", "�������� ��� �����������");
define("REPORTS_SYSMSG_TOTAL", "�����");
define("REPORTS_SYSMSG_BACK", "��������� � ������");

define("SUPPORT", "��������� � ������������");
define("SUPPORT_HELP_MESSAGE", "
����������� ��������� �������� ������ ������������������ ������������� ������� NetCat.<br />
��� ����, ����� ���������� � ������������:
<ol>
 <li style='padding-bottom:10px'><a target=_blank href='https://netcat.ru/forclients/my/copies/add_copies.html'>��������������� ���� ����� �������</a>.
 <li style='padding-bottom:10px'>����� �������� ��������� ���� ������ �� ������ ��������� � ����������� ���������<br> � ����������� ���������
   �� �������� &laquo;<a target=_blank href='https://netcat.ru/forclients/support/tickets/'>��������� ������</a>&raquo;.
 </li>
</ol>
");

define("TOOLS_SQL", "��������� ������ SQL");
define("TOOLS_SQL_ERR_NOQUERY", "������� ������!");
define("TOOLS_SQL_SEND", "��������� ������");
define("TOOLS_SQL_OK", "������ �������� �������");
define("TOOLS_SQL_TOTROWS", "�����, ��������������� �������");
define("TOOLS_SQL_HELP", "������� ��������");
define("TOOLS_SQL_HISTORY", "��������� 15 ��������");
define("TOOLS_SQL_HELP_EXPLAIN", "�������� ������ ����� �� ������� %s");
define("TOOLS_SQL_HELP_SELECT", "�������� ���������� ����� � ������� %s");
define("TOOLS_SQL_HELP_SHOW", "�������� ������ ������");
define("TOOLS_SQL_HELP_DOCS", "� ��������� ������������� �� �� MySQL �� ������ ������������ �� ������:<br>\n<a target=_blank href=http://dev.mysql.com/doc/refman/5.1/en/>http://dev.mysql.com/doc/refman/5.1/en/</a>");
define("TOOLS_SQL_BENCHMARK", "����� ���������� �������");
define("TOOLS_SQL_ERR_OPEN_FILE", "�� ������� ������� sql-����: %s");
define("TOOLS_SQL_ERR_FILE_QUERY", "��������� ���������� ������� � ����� %s MySQL ������: %s");

define("NETCAT_TRASH_SIZEINFO", "�� ������ ������ � ������� - %s. <br />����� ������� - %s ��.");
define("NETCAT_TRASH_NOMESSAGES", "������� �����.");
define("NETCAT_TRASH_MESSAGES_SK1", "������");
define("NETCAT_TRASH_MESSAGES_SK2", "��������");
define("NETCAT_TRASH_MESSAGES_SK3", "�������");
define("NETCAT_TRASH_RECOVERED_SK1", "������������");
define("NETCAT_TRASH_RECOVERED_SK2", "�������������");
define("NETCAT_TRASH_RECOVERED_SK3", "�������������");
define("NETCAT_TRASH_RECOVERY", "������������");
define("NETCAT_TRASH_DELETE_FROM_TRASH", "������� �� �������");
define("NETCAT_TRASH_OBJECT_WERE_DELETED_TRASHBIN_FULL", "������� �� ���� �������� � �������, ��� ��� ��� ���������");
define("NETCAT_TRASH_OBJECT_IN_TRASHBIN_AND_CANCEL", "������� ���������� � <a href='%s'>�������</a>. <a href='%s'>��������</a>");
define("NETCAT_TRASH_TRASHBIN_DISABLED", "������� ��������� �������� ���������");
define("NETCAT_TRASH_EDIT_SETTINGS", "�������� ���������");
define("NETCAT_TRASH_OBJECT_NOT_FOUND", "�� ������� ��������, ��������������� �������");
define("NETCAT_TRASH_TRASHBIN", "�������");
define("NETCAT_TRASH_PRERECOVERYSUB_INFO", "��������� �� ����������������� �������� ���������� � ��������, ������� ������ ��� ���. NetCat ����� ������������ ��� ������� � ���� �����������, ������� ���� �� ������ �������� ��������. �� ������ ������� ��� ��������.");
define("NETCAT_TRASH_PRERECOVERYSUB_CHECKED", "�������");
define("NETCAT_TRASH_PRERECOVERYSUB_NAME", "��������");
define("NETCAT_TRASH_PRERECOVERYSUB_KEYWORD", "�������� �����");
define("NETCAT_TRASH_PRERECOVERYSUB_PARENT", "������������ ������");
define("NETCAT_TRASH_PRERECOVERYSUB_ROOT", "�������� ������ �����");
define("NETCAT_TRASH_PRERECOVERYSUB_NEXT", "�����");
define("NETCAT_TRASH_FILTER", "������� ��������� ��������");
define("NETCAT_TRASH_FILTER_DATE_FROM", "���� �������� �");
define("NETCAT_TRASH_FILTER_DATE_TO", "���� �������� ��");
define("NETCAT_TRASH_FILTER_DATE_FORMAT", "��-��-���� ��:��");
define("NETCAT_TRASH_FILTER_SUBDIVISION", "������");
define("NETCAT_TRASH_FILTER_COMPONENT", "���������");
define("NETCAT_TRASH_FILTER_ALL", "���");
define("NETCAT_TRASH_FILTER_APPLY", "�������");
define("NETCAT_TRASH_FILE_DOEST_EXIST", "���� %s �� ������");
define("NETCAT_TRASH_FOLDER_FAIL", "���������� %s �� ���������� ��� �� �������� ��� ������");
define("NETCAT_TRASH_ERROR_RELOAD_PAGE", "���������� ������. ���������� <a href='index.php'>������������� ��������</a>");
define("NETCAT_TRASH_TRASHBIN_IS_FULL", "������� �����������");
define("NETCAT_TRASH_TEMPLATE_DOESNT_EXIST", "� ������� ���������� ��� ������� ��� ������� ��������� ��������");
define("NETCAT_TRASH_IDENTIFICATOR", "�������������");
define("NETCAT_TRASH_USER_IDENTIFICATOR", "ID ����������� ������������");

# USERS
define("CONTROL_USER_GROUPS", "������ �������������");
define("CONTROL_USER_FUNCS_ALLUSERS", "���");
define("CONTROL_USER_FUNCS_ONUSERS", "����������");
define("CONTROL_USER_FUNCS_OFFUSERS", "�����������");
define("CONTROL_USER_FUNCS_DOGET", "�������");
define("CONTROL_USER_FUNCS_VIEWCONTROL", "��������� ������");
define("CONTROL_USER_FUNCS_SORTBY", "����������� �� ����");
define("CONTROL_USER_FUNCS_USER_NUMBER_ON_THE_PAGE", "������������� �� ��������.");
define("CONTROL_USER_FUNCS_SORT_ORDER", "������� ����������");
define("CONTROL_USER_FUNCS_SORT_ORDER_ACS", "�� �����������");
define("CONTROL_USER_FUNCS_SORT_ORDER_DESC", "�� ��������");
define("CONTROL_USER_FUNCS_PREV_PAGE", "���������� ��������");
define("CONTROL_USER_FUNC_CONFIRM_DEL", "����������� ��������");
define("CONTROL_USER_FUNC_CONFIRM_DEL_OK", "�����������");
define("CONTROL_USER_FUNC_CONFIRM_DEL_NOT_USER", "�� ������� ������������");
define("CONTROL_USER_FUNC_GROUP_ERROR", "�� �������� ������");
define("CONTROL_USER", "������������");
define("CONTROL_USER_FUNCS_EDITACCESSRIGHT", "������������� ����� �������");
define("CONTROL_USER_ACTIONS", "��������");
define("CONTROL_USER_RIGHTS_ITEM", "��������");
define("CONTROL_USER_RIGHTS_SELECT_ITEM", "�������� ��������");
define("CONTROL_USER_RIGHTS_TYPE_OF_RIGHT", "��� ����");
define("CONTROL_USER_RIGHTS", "�����");
define("CONTROL_USER_ERROR_NEWPASS_IS_CURRENT", "����� ������ ��������� � �������!");
define("CONTROL_USER_CHANGEPASS", "������� ������");
define("CONTROL_USER_EDIT", "�������������");
define("CONTROL_USER_REG", "����������� ������������");
define("CONTROL_USER_NEWPASSWORD", "����� ������");
define("CONTROL_USER_NEWPASSWORDAGAIN", "����� ������ ��� ���");
define("CONTROL_USER_MSG_USERNOTFOUND", "�� ������� �� ������ ������������, ���������������� ������ �������.");
define("CONTROL_USER_GROUP", "������");
define("CONTROL_USER_GROUP_MEMBERS", "���������");
define("CONTROL_USER_GROUP_NOMEMBERS", "���������� ���.");
define("CONTROL_USER_GROUP_TOTAL", "�����");
define("CONTROL_USER_GROUPNAME", "�������� ������");
define("CONTROL_USER_ERROR_GROUPNAME_IS_EMPTY", "�������� ������ �� ����� ���� ������!");
define("CONTROL_USER_ADDNEWGROUP", "�������� ������");
define("CONTROL_USER_CHANGERIGHTS", "��������� ����� �������");
define("CONTROL_USER_NEW_ADDED", "������������ ��������");
define("CONTROL_USER_NEW_NOTADDED", "������������ �� ��������");
define("CONTROL_USER_EDITSUCCESS", "������������ ������� �������");
define("CONTROL_USER_REGISTER", "����������� ������ ������������");
define("CONTROL_USER_REGISTER_ERROR_NO_LOGIN_FIELD_VALUE", "�� �������� �������� ��� ������");
define("CONTROL_USER_REGISTER_ERROR_LOGIN_ALREADY_EXIST", "����� ��� �����");
define("CONTROL_USER_REGISTER_ERROR_LOGIN_INCORRECT", "����� �������� ������������ �������");
define("CONTROL_USER_GROUPS_ADD", "���������� ������");
define("CONTROL_USER_GROUPS_EDIT", "�������������� ������");
define("CONTROL_USER_ACESSRIGHTS", "����� �������");
define("CONTROL_USER_USERSANDRIGHTS", "������������ � �����");
define("CONTROL_USER_PASSCHANGE", "����� ������");
define("CONTROL_USER_CATALOGUESWITCH", "����� ��������");
define("CONTROL_USER_SECTIONSWITCH", "����� �������");
define("CONTROL_USER_TITLE_USERINFOEDIT", "�������������� ���������� � ������������");
define("CONTROL_USER_TITLE_PASSWORDCHANGE", "����� ������ ������������");
define("CONTROL_USER_ERROR_EMPTYPASS", "������ �� ����� ���� ������!");
define("CONTROL_USER_ERROR_NOTCANGEPASS", "������ �� �������. ������!");
define("CONTROL_USER_OK_CHANGEDPASS", "������ ������� �������.");
define("CONTROL_USER_ERROR_RETRY", "���������� �����!");
define("CONTROL_USER_ERROR_PASSDIFF", "��������� ������ �� ���������!");
define("CONTROL_USER_MAIL", "�������� �� ����");
define("CONTROL_USER_MAIL_TITLE_COMPOSE", "����������� ������");
define("CONTROL_USER_MAIL_GROUP", "������ �������������");
define("CONTROL_USER_MAIL_ALLGROUPS", "��� ������");
define("CONTROL_USER_MAIL_FROM", "�����������");
define("CONTROL_USER_MAIL_BODY", "����� ������");
define("CONTROL_USER_MAIL_ADDATTACHMENT", "������� ����");
define("CONTROL_USER_MAIL_SEND", "��������� ���������");
define("CONTROL_USER_MAIL_ERROR_EMAILFIELD", "�� ���������� ���� ���������� Email �������������.");
define("CONTROL_USER_MAIL_OK", "������ ���������� ���� �������������");
define("CONTROL_USER_MAIL_ERROR_NOONEEMAIL", "� ��������� ���� �� ������� �� ������ ������������ ������.");
define("CONTROL_USER_MAIL_ATTCHAMENT", "������������ ����");
define("CONTROL_USER_MAIL_ERROR_ONE", "�������� ����������, ��� ��� � <a href=".$ADMIN_PATH."settings.php?phase=1>��������� ����������</a> �� ������� ���� ��� ��������.");
define("CONTROL_USER_MAIL_ERROR_TWO", "�������� ����������, ��� ��� � <a href=".$ADMIN_PATH."settings.php?phase=1>��������� ����������</a> �� ������� ��� ����������� �����.");
define("CONTROL_USER_MAIL_ERROR_THREE", "�������� ����������, ��� ��� � <a href=".$ADMIN_PATH."settings.php?phase=1>��������� ����������</a> �� ������ Email ����������� �����.");
define("CONTROL_USER_MAIL_ERROR_NOBODY", "����������� ����� ������.");
define("CONTROL_USER_MAIL_CHANGE", "��������");
define("CONTROL_USER_MAIL_CONTENT", "���������� ������");
define("CONTROL_USER_MAIL_SUBJECT", "���� ������");
define("CONTROL_USER_MAIL_RULES", "������� ��������");
define("CONTROL_USER_FUNCS_USERSGET", "������� �������������");
define("CONTROL_USER_FUNCS_USERSGET_EXT", "����������� �����");
define("CONTROL_USER_FUNCS_SEARCHEDUSER", "������� �������������");
define("CONTROL_USER_FUNCS_USERCOUNT", "����� �������������");
define("CONTROL_USER_FUNCS_ADDUSER", "�������� ������������");
define("CONTROL_USER_FUNCS_NORIGHTS", "������� ������������ �� ��������� �����.");
define("CONTROL_USER_FUNCS_GROUP_NORIGHTS", "� ������ ������ ��� ����.");
define("CONTROL_USER_RIGHTS_GUESTONE", "�����");
define("CONTROL_USER_RIGHTS_DIRECTOR", "��������");
define("CONTROL_USER_RIGHTS_SUPERVISOR", "����������");
define("CONTROL_USER_RIGHTS_SITEADMIN", "�������� �����");
define("CONTROL_USER_RIGHTS_CATALOGUEADMINALL", "�������� ���� ������");
define("CONTROL_USER_RIGHTS_SUBDIVISIONADMIN", "�������� �������");
define("CONTROL_USER_RIGHTS_SUBCLASSADMIN", "�������� ����������");
define("CONTROL_USER_RIGHTS_SUBCLASSADMINS", "�������� ���������� �������");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMIN", "������������� ������");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMINALL", "������������� ���� �������");
define("CONTROL_USER_RIGHTS_EDITOR", "��������");
define("CONTROL_USER_RIGHTS_SUBSCRIBER", "���������");
define("CONTROL_USER_RIGHTS_MODERATOR", "���������� ��������������");
define("CONTROL_USER_RIGHTS_BAN", "����������� � ������");
define("CONTROL_USER_RIGHTS_SITE", "����������� � ������ �����");
define("CONTROL_USER_RIGHTS_SITEALL", "����������� � ������ �� ���� ������");
define("CONTROL_USER_RIGHTS_SUB", "����������� � ������ �������");
define("CONTROL_USER_RIGHTS_CC", "����������� � ������ ����������");
define("CONTROL_USER_RIGHTS_LOAD", "��������");
define("CONTROL_USER_RIGHT_ADDNEWRIGHTS", "��������� �����");
define("CONTROL_USER_RIGHT_ADDPERM", "���������� ����� ������������");
define("CONTROL_USER_RIGHT_ADDPERM_GROUP", "���������� ����� ������");
define("CONTROL_USER_FUNCS_FROMCAT", "�� ��������");
define("CONTROL_USER_FUNCS_FROMSEC", "�� �������");
define("CONTROL_USER_FUNCS_ADDNEWRIGHTS", "��������� ����� �����");
define("CONTROL_USER_FUNCS_ERR_CANTREMGROUP", "�� ������� ������� ������ %s. ������!");
define("CONTROL_USER_SELECTSITE", "�������� ����");
define("CONTROL_USER_SELECTSECTION", "�������� ������");
define("CONTROL_USER_NOONESECSINSITE", "� ������ ����� ��� �� ������ �������.");
define("CONTROL_USER_FUNCS_CLASSINSECTION", "������ �������� �������");
define("CONTROL_USER_RIGHTS_ERR_CANTREMPRIV", "�� ������� ������� ����������. ������!");
define("CONTROL_USER_RIGHTS_UPDATED_OK", "����� ������������ ���������.");
define("CONTROL_USER_RIGHTS_ERROR_NOSELECTED", "�� ������� ��������");
define("CONTROL_USER_RIGHTS_ERROR_DATA", "������ � ����");
define("CONTROL_USER_RIGHTS_ERROR_DB", "������ ������ � ��");
define("CONTROL_USER_RIGHTS_ERROR_POSSIBILITY", "�� ������� �����������");
define("CONTROL_USER_RIGHTS_ERROR_NOTSITE", "�� ������ ����");
define("CONTROL_USER_RIGHTS_ERROR_NOTSUB", "�� ������ ������");
define("CONTROL_USER_RIGHTS_ERROR_NOTCCINSUB", "� ��������� ������� ��� �����������");
define("CONTROL_USER_RIGHTS_ERROR_NOTTYPEOFRIGHT", "�� ������ ��� ����");
define("CONTROL_USER_RIGHTS_ERROR_START", "������ � ���� ������ �������� �����");
define("CONTROL_USER_RIGHTS_ERROR_END", "������ � ���� ��������� �������� �����");
define("CONTROL_USER_RIGHTS_ERROR_STARTEND", "����� ��������� �������� ���� �� ����� ���� ������ ������� ������");
define("CONTROL_USER_RIGHTS_ERROR_GUEST", "������ ��������� ����� \"�����\" ������ ����");
define("CONTROL_USER_RIGHTS_ADDED", "����� ���������");
define("CONTROL_USER_RIGHTS_LIVETIME", "���� ��������");
define("CONTROL_USER_RIGHTS_UNLIMITED", "�� ���������");
define("CONTROL_USER_RIGHTS_NONLIMITED", "��� �����������");
define("CONTROL_USER_RIGHTS_LIMITED", "���������");
define("CONTROL_USER_RIGHTS_STARTING_OPERATIONS", "������ ��������");
define("CONTROL_USER_RIGHTS_FINISHING_OPERATIONS", "����� ��������");
define("CONTROL_USER_RIGHTS_NOW", "������");
define("CONTROL_USER_RIGHTS_ACROSS", "�����");
define("CONTROL_USER_RIGHTS_ACROSS_MINUTES", "�����");
define("CONTROL_USER_RIGHTS_ACROSS_HOURS", "�����");
define("CONTROL_USER_RIGHTS_ACROSS_DAYS", "����");
define("CONTROL_USER_RIGHTS_ACROSS_MONTHS", "�������");
define("CONTROL_USER_RIGHTS_RIGHT", "�����");
define("CONTROL_USER_RIGHTS_CONTROL_ADD", "����������");
define("CONTROL_USER_RIGHTS_CONTROL_EDIT", "��������������");
define("CONTROL_USER_RIGHTS_CONTROL_DELETE", "��������");
define("CONTROL_USER_RIGHTS_CONTROL_HELP", "������");
define("CONTROL_USER_USERS_MOVED_SUCCESSFULLY", "������������ ������� ����������");
define("CONTROL_USER_SELECT_GROUP_TO_MOVE", "�������� ������, � ������� ����� ����������� ��������� �������������");
define("CONTROL_USER_SELECTSITEALL", "��� �����");

# TEMPLATE
define("CONTROL_TEMPLATE", "������ �������");
define("CONTROL_TEMPLATE_ADD", "���������� ������");
define("CONTROL_TEMPLATE_EDIT", "�������������� ������");
define("CONTROL_TEMPLATE_DELETE", "�������� ������");
define("CONTROL_TEMPLATE_OPT_ADD", "���������� ���������");
define("CONTROL_TEMPLATE_OPT_EDIT", "�������������� ���������");
define("CONTROL_TEMPLATE_ERR_NAME", "������� �������� ������.");
define("CONTROL_TEMPLATE_INFO_CONVERT", "���������� ����� �������, �� �������� <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes');\">������������ �����������</a>.");
define("CONTROL_TEMPLATE_TEPL_NAME", "�������� ������");
define("CONTROL_TEMPLATE_TEPL_MENU", "������� ������ ���������");
define("CONTROL_TEMPLATE_TEPL_HEADER", "������� ����� �������� (Header)");
define("CONTROL_TEMPLATE_TEPL_FOOTER", "������ ����� �������� (Footer)");
define("CONTROL_TEMPLATE_TEPL_CREATE", "�������� �����");
define("CONTROL_TEMPLATE_NOT_FOUND", "����� ������� � ��������������� %s �� ������!");
define("CONTROL_TEMPLATE_ERR_USED_IN_SITE", "������ ����� ������� ������������ � ��������� ������:");
define("CONTROL_TEMPLATE_ERR_USED_IN_SUB", "������ ����� ������� ������������ � ��������� ��������:");
define("CONTROL_TEMPLATE_ERR_CANTDEL", "�� ������� ������� �����");
define("CONTROL_TEMPLATE_INFO_DELETE", "�� ����������� ������� �����");
define("CONTROL_TEMPLATE_INFO_DELETE_SOME", "��� ������ ����� �������");
define("CONTROL_TEMPLATE_DELETED", "����� ������");
define("CONTROL_TEMPLATE_ADDLINK", "�������� ����� �������");
define("CONTROL_TEMPLATE_REMOVETHIS", "������� ���� ����� �������");
define("CONTROL_TEMPLATE_PREF_EDIT", "�������� ���������");
define("CONTROL_TEMPLATE_NONE", "� ������� ��� �� ������ ������");
define("CONTROL_TEMPLATE_TEPL_IMPORT", "������ ������");
define("CONTROL_TEMPLATE_IMPORT", "������ ������");
define("CONTROL_TEMPLATE_IMPORT_UPLOAD", "���������");
define("CONTROL_TEMPLATE_IMPORT_SELECT", "�������� ������ ��� ������� (������������� ����� �������� �������)");
define("CONTROL_TEMPLATE_IMPORT_CONTINUE", "�����");
define("CONTROL_TEMPLATE_IMPORT_ERROR_NOTUPLOADED", "������ ������� ������");
define("CONTROL_TEMPLATE_IMPORT_ERROR_SQL", "������ ��� ���������� ������ � ���� ������");
define("CONTROL_TEMPLATE_IMPORT_ERROR_EXTRACT", "������ ��� ���������� ������ ������ %s � ���������� %s");
define("CONTROL_TEMPLATE_IMPORT_ERROR_MOVE", "������ ����������� ������ �� %s � %s");
define("CONTROL_TEMPLATE_IMPORT_SUCCESS", "����� ������� ������������");
define("CONTROL_TEMPLATE_EXPORT", "�������������� ����� � ����");
define("CONTROL_TEMPLATE_FILES_PATH", "����� ������ ��������� � ����� <a href='%s'>%s</a>");
define("CONTROL_TEMPLATE_PARTIALS", "������");
define("CONTROL_TEMPLATE_PARTIALS_NEW", "����� ������");
define("CONTROL_TEMPLATE_PARTIALS_ADD", "�������� ������");
define("CONTROL_TEMPLATE_PARTIALS_REMOVE", "������� ������");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD", "�������� ����� ������ (���������� �������)");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_ERROR", "�������� ����� ������ ����� ��������� ������ ��������� �����, ����� � ���� �������������");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_REQUIRED_ERROR", "�������� ����� ������ ����������� ��� ����������");
define("CONTROL_TEMPLATE_PARTIALS_DESCRIPTION_FIELD", "��������");
define("CONTROL_TEMPLATE_PARTIALS_ENABLE_ASYNC_LOAD_FIELD", "��������� ����������� ��������");
define("CONTROL_TEMPLATE_PARTIALS_SOURCE_FIELD", "������ ������");
define("CONTROL_TEMPLATE_PARTIALS_EXISTS_ERROR", "������ � ����� �������� ������ ��� ����������");
define("CONTROL_TEMPLATE_PARTIALS_NOT_EXISTS", "� ������ ������ ��� �� ����� ������");
define("CONTROL_TEMPLATE_BASE_TEMPLATE", "������� ����� ������� �� ������ �������������");
define("CONTROL_TEMPLATE_BASE_TEMPLATE_CREATE_FROM_SCRATCH", "������� ����� ������� \"� ����\"");
define("CONTROL_TEMPLATE_CONTINUE", "����������");

define("CONTROL_TEMPLATE_KEYWORD", "�������� �����");
define("CONTROL_TEMPLATE_KEYWORD_ONLY_DIGITS", "�������� ����� �� ����� �������� ������ �� ����");
define("CONTROL_TEMPLATE_KEYWORD_TOO_LONG", "����� ��������� ����� �� ����� ���� ����� %d ��������");
define("CONTROL_TEMPLATE_KEYWORD_INVALID_CHARACTERS", "�������� ����� ����� ��������� ������ ����� ���������� ��������, ����� � ������� �������������");
define("CONTROL_TEMPLATE_KEYWORD_NON_UNIQUE", "�������� ����� �%s� ��� ��������� ������ ������� �%d. %s�");
define("CONTROL_TEMPLATE_KEYWORD_RESERVED", "���������� ������������ �%s� � �������� ��������� �����, ��� ��� ��� �������� �����������������");
define("CONTROL_TEMPLATE_KEYWORD_PATH_EXISTS", "���������� ������������ �%s� � �������� ��������� �����, ��� ��� ��� ���������� ����� � ����� ���������");

define("CONTROL_TEMPLATE_SUBDIVISION_CONTENT_OUTPUT_LOCATION", "����� ������ ����������� �������");
define("CONTROL_TEMPLATE_SUBDIVISION_CONTENT_OUTPUT_LOCATION_BETWEEN_HEADER_AND_FOOTER", "����� ������� � ������ ������ ��������");
define("CONTROL_TEMPLATE_SUBDIVISION_CONTENT_OUTPUT_LOCATION_IN_MAINAREA", "� ����� &quot;�������� ���������� �������&quot;");

# CLASSIFICATORS
define("CONTENT_CLASSIFICATORS", "������");
define("CONTENT_CLASSIFICATORS_NAMEONE", "������");
define("CONTENT_CLASSIFICATORS_NAMEALL", "��� ������");
define("CONTENT_CLASSIFICATORS_ELEMENTS", "��������");
define("CONTENT_CLASSIFICATORS_ELEMENT", "�������");
define("CONTENT_CLASSIFICATORS_ELEMENT_NAME", "�������� ��������");
define("CONTENT_CLASSIFICATORS_ELEMENT_VALUE", "�������������� ��������");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADDONE", "�������� �������");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD", "���������� ��������");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD_SUCCESS", "������� ��������");
define("CONTENT_CLASSIFICATORS_ELEMENTS_EDIT", "�������������� ��������");
define("CONTENT_CLASSIFICATORS_LIST_ADD", "���������� ������");
define("CONTENT_CLASSIFICATORS_LIST_EDIT", "�������������� ������");
define("CONTENT_CLASSIFICATORS_LIST_DELETE", "�������� ������");
define("CONTENT_CLASSIFICATORS_LIST_DELETE_SELECTED", "������� ���������");
define("CONTENT_CLASSIFICATORS_ERR_NONE", "� ������ ������� ��� �� ������ ������.");
define("CONTENT_CLASSIFICATORS_ERR_ELEMENTNONE", "� ������ ������ ��� �� ������ ��������.");
define("CONTENT_CLASSIFICATORS_ERR_SYSDEL", "���������� ������� ������� �� ���������� ��������������");
define("CONTENT_CLASSIFICATORS_ERR_EDITI_GUESTRIGHTS", "��������� ������ � �������������� ���������� � ��������� �������!");
define("CONTENT_CLASSIFICATORS_ERROR_NAME", "������� ������� �������� ��������������!");
define("CONTENT_CLASSIFICATORS_ERROR_FILE_NAME", "�������� CSV-���� ��� ��������������!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORD", "������� ���������� �������� �������������� (�������� �������)!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDINV", "���������� �������� (�������� �������) ������ ��������� ������ ��������� ����� � �����!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDFL", "���������� �������� (�������� �������) ������ ���������� � ��������� �����!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDAE", "������������� � ����� ���������� ��������� (��������� �������) ��� ����������!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDREZ", "������ ��� ���������������!");
define("CONTENT_CLASSIFICATORS_ADDLIST", "�������� ������");
define("CONTENT_CLASSIFICATORS_ADD_KEYWORD", "�������� ������� (���������� �������)");
define("CONTENT_CLASSIFICATORS_SAVE", "��������� ���������");
define("CONTENT_CLASSIFICATORS_NO_NAME", "(��� ��������)");
define("CLASSIFICATORS_SORT_HEADER", "��� ����������");
define("CLASSIFICATORS_SORT_PRIORITY_HEADER", "���������");
define("CLASSIFICATORS_SORT_TYPE_ID", "ID");
define("CLASSIFICATORS_SORT_TYPE_NAME", "�������");
define("CLASSIFICATORS_SORT_TYPE_PRIORITY", "���������");
define("CLASSIFICATORS_SORT_DIRECTION", "����������� ����������");
define("CLASSIFICATORS_SORT_ASCENDING", "����������");
define("CLASSIFICATORS_SORT_DESCENDING", "����������");
define("CLASSIFICATORS_IMPORT_HEADER", "������ ������");
define("CLASSIFICATORS_IMPORT_BUTTON", "�������������");
define("CLASSIFICATORS_IMPORT_FILE", "CSV-���� (*)");
define("CLASSIFICATORS_IMPORT_DESCRIPTION", "���� � ������������� ����� ������ ���� �������, �� ��� ��������� ����� �������, ���� ��� - ������ ������� ��� �������, � ������ ���������.");
define("CLASSIFICATORS_SUCCESS_DELETEONE", "������ ������� ������.");
define("CLASSIFICATORS_SUCCESS_DELETE", "������ ������� �������.");
define("CLASSIFICATORS_SUCCESS_ADD", "������ ������� ��������.");
define("CLASSIFICATORS_SUCCESS_EDIT", "������ ������� �������.");
define("CLASSIFICATORS_ERROR_DELETEONE_SYS", "������ %s - ���������, �������� ���������.");
define("CLASSIFICATORS_ERROR_ADD", "������ ���������� ������.");
define("CLASSIFICATORS_ERROR_EDIT", "������ ��������� ������.");

# TOOLS HTML
define("TOOLS_HTML", "HTML-��������");
define("TOOLS_HTML_INFO", "������������� � ���������� ���������");

define("TOOLS_DUMP", "������ �������");
define("TOOLS_DUMP_CREATE", "�������� ������");
define("TOOLS_DUMP_CREATED", "����� ������� ������ %FILE.");
define("TOOLS_DUMP_CREATION_FAILED", "������ �������� ������.");
define("TOOLS_DUMP_DELETED", "���� %FILE �����.");
define("TOOLS_DUMP_RESTORE", "�������������� ������");
define("TOOLS_DUMP_MSG_RESTORED", "����� ������������.");
define("TOOLS_DUMP_INC_TITLE", "�������������� ������ � ���������� �����");
define("TOOLS_DUMP_INC_DORESTORE", "������������");
define("TOOLS_DUMP_INC_DBDUMP", "���� ���� ������");
define("TOOLS_DUMP_INC_FOLDER", "���������� �����");
define("TOOLS_DUMP_ERROR_CANTDELETE", "������! �� ���� ������� %FILE.");
define("TOOLS_DUMP_INC_ARCHIVE", "�����");
define("TOOLS_DUMP_INC_DATE", "����");
define("TOOLS_DUMP_INC_SIZE", "������");
define("TOOLS_DUMP_INC_DOWNLOAD", "�������");
define("TOOLS_DUMP_NOONE", "������ ������� �����������.");
define("TOOLS_DUMP_DATE", "���� ������");
define("TOOLS_DUMP_SIZE", "������, ����");
define("TOOLS_DUMP_CREATEAP", "������� ����� �������");
define("TOOLS_DUMP_CONFIRM", "����������� �������� ������ �������");
define("TOOLS_DUMP_BACKUPLIST_HEADER", "��������� ������ �������");
define("TOOLS_DUMP_CREATE_HEADER", "�������� ������");
define("TOOLS_DUMP_CREATE_OPT_FULL", "������ ����� (�������� ��� �����, ���� ������ � ������ ��������������)");
define("TOOLS_DUMP_CREATE_OPT_DATA", "����� ������ (���������� images, netcat_templates, modules, netcat_files � ���� ������)");
define("TOOLS_DUMP_CREATE_OPT_SQL", "������ ���� ������");
define("TOOLS_DUMP_CREATE_SUBMIT", "������� ��������� �����");
define("TOOLS_DUMP_REMOVE_SELECTED", "������� ��������� ������");
define("TOOLS_DUMP_CREATE_WAIT", "������������ �������� ������. ����������, ���������.");
define("TOOLS_DUMP_RESTORE_WAIT", "������������ �������������� ������ �� ������. ����������, ���������.");
define("TOOLS_DUMP_CONNECTION_LOST", "�������� ����� � ��������. ���� ����������� �������� �� ���� ���������, %s.");
define("TOOLS_DUMP_CONNECTION_LOST_SYSTEM_TAR", "���������� ��������� ���������� ��������� ������� tar �� PHP");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_PHP_LIMITS", "��������� ������ ������ PHP, � ���������� ��������� ����� ������ � PHP, �������� � � ������������ ���-�������, � ����� ������ ������������� �������� �� �������");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_SERVER_LIMITS", "���������� ��������� �������� � � ������������ ���-������� � ������ ������������� �������� �� �������");
define("TOOLS_DUMP_CONNECTION_LOST_GO_BACK", "��������� �����");

define("TOOLS_REDIRECT", "�������������");
define("TOOLS_REDIRECT_OLDURL", "������ URL");
define("TOOLS_REDIRECT_NEWURL", "����� URL");
define("TOOLS_REDIRECT_OLDLINK", "������ ������");
define("TOOLS_REDIRECT_NEWLINK", "����� ������");
define("TOOLS_REDIRECT_HEADER", "���������");
define("TOOLS_REDIRECT_HEADERSEND", "���������� ���������");
define("TOOLS_REDIRECT_SETTINGS", "���������");
define("TOOLS_REDIRECT_CHANGEINFO", "�������� ����������");
define("TOOLS_REDIRECT_NONE", "� ������ ������ ��� �������������.");
define("TOOLS_REDIRECT_ADD", "�������� �������������");
define("TOOLS_REDIRECT_EDIT", "�������� �������������");
define("TOOLS_REDIRECT_ADDONLY", "��������");
define("TOOLS_REDIRECT_CANTBEEMPTY", "���� �� ����� ���� �������!");
define("TOOLS_REDIRECT_OLDURL_MUST_BE_UNIQUE", "��� ���� ������������� � ����� &quot;������ �������&quot; - <a href='".nc_core('NETCAT_FOLDER')."action.php?ctrl=admin.redirect&action=edit&id=%s'>�������</a>");
define("TOOLS_REDIRECT_DISABLED", "� ���������������� ����� ���������� \"�������������\" ��������.<br/>����� ��� �������, ��������� � ����� vars.inc.php �������� ��������� \$NC_REDIRECT_DISABLED �� 0. ");
define("TOOLS_REDIRECT_GROUP", "������");
define("TOOLS_REDIRECT_GROUP_NAME", "�������� ������");
define("TOOLS_REDIRECT_GROUP_ADD", "�������� ������");
define("TOOLS_REDIRECT_GROUP_EDIT", "�������� ������");
define("TOOLS_REDIRECT_GROUP_DELETE", "������� ������");
define("TOOLS_REDIRECT_BACK", "�����");
define("TOOLS_REDIRECT_SAVE_OK", "������������� ���������");
define("TOOLS_REDIRECT_GROUP_SAVE_OK", "������ ���������");
define("TOOLS_REDIRECT_SAVE_ERROR", "������ ����������");
define("TOOLS_REDIRECT_DELETE", "�������");
define("TOOLS_REDIRECT_DELETE_CONFIRM_REDIRECTS", "����� ������� ��������� �������������:");
define("TOOLS_REDIRECT_DELETE_CONFIRM_GROUP", "����� ������� ������ &quot;%s&quot; ������� ��������� �������������:");
define("TOOLS_REDIRECT_DELETE_OK", "�������� ���������");
define("TOOLS_REDIRECT_STATUS", "������");
define("TOOLS_REDIRECT_SAVE", "���������");
define("TOOLS_REDIRECT_MOVE", "��������� � ������");
define("TOOLS_REDIRECT_MOVE_CONFIRM_REDIRECTS", "����� ���������� ��������� �������������:");
define("TOOLS_REDIRECT_MOVE_OK", "����������� ���������");
define("TOOLS_REDIRECT_NOTHING_SELECTED", "�� ������� �� ����� �������������");
define("TOOLS_REDIRECT_IMPORT", "������");
define("TOOLS_REDIRECT_FIELDS", "���� �������������");
define("TOOLS_REDIRECT_CONTINUE", "����������");
define("TOOLS_REDIRECT_DO_IMPORT", "�������������");
define("TOOLS_REDIRECT_MOVE_TITLE", "����������� �������������");
define("TOOLS_REDIRECT_DELETE_TITLE", "�������� �������������");
define("TOOLS_REDIRECT_IMPORT_TITLE", "�������������� �������������");

define("TOOLS_CRON", "���������� ��������");
define("TOOLS_CRON_INTERVAL", "�������� (�:�:�)");
define("TOOLS_CRON_MINUTES", "������");
define("TOOLS_CRON_HOURS", "����");
define("TOOLS_CRON_DAYS", "���");
define("TOOLS_CRON_MONTHS", "������");
define("TOOLS_CRON_LAUNCHED", "��������� ������");
define("TOOLS_CRON_NEXT", "��������� ������");
define("TOOLS_CRON_SCRIPTURL", "������ �� ������");
define("TOOLS_CRON_ADDLINK", "�������� ������");
define("TOOLS_CRON_CHANGE", "��������");
define("TOOLS_CRON_NOTASKS", "� ������ ������� ��� �� ����� ������.");
define("TOOLS_CRON_WRONG_DOMAIN", "�����, ��������� � ����� crontab.php (%s), �� ������������� �������� (%s), ������ ����� �� �����������! ��������� ��������� � ������������ � <a href='https://netcat.ru/developers/docs/system-tools/task-management/' TARGET='_blank'>�������������</a>.");

define("TOOLS_COPYSUB", "����������� ��������");
define("TOOLS_COPYSUB_COPY", "����������");
define("TOOLS_COPYSUB_COPY_SUCCESS", "����������� ������� ���������");
define("TOOLS_COPYSUB_SOURCE", "��������");
define("TOOLS_COPYSUB_DESTINATION", "��������");
define("TOOLS_COPYSUB_ACTION", "��������");
define("TOOLS_COPYSUB_COPY_SITE", "���������� ����");
define("TOOLS_COPYSUB_COPY_SUB", "���������� ������");
define("TOOLS_COPYSUB_COPY_SUB_LOWER", "���������� ������");
define("TOOLS_COPYSUB_SITE", "����");
define("TOOLS_COPYSUB_SUB", "�������");
define("TOOLS_COPYSUB_KEYWORD_SUB", "�������� ����� �������");
define("TOOLS_COPYSUB_NAME_CC", "�������� ����������");
define("TOOLS_COPYSUB_KEYWORD_CC", "�������� ����� ����������");
define("TOOLS_COPYSUB_TEMPLATE_NAME", "������� ���");
define("TOOLS_COPYSUB_SETTINGS", "��������� �����������");
define("TOOLS_COPYSUB_COPY_WITH_CHILD", "���������� ����������");
define("TOOLS_COPYSUB_COPY_WITH_CC", "���������� ���������� � �������");
define("TOOLS_COPYSUB_COPY_WITH_OBJECT", "���������� �������");
define("TOOLS_COPYSUB_ERROR_KEYWORD_EXIST", "������ � ����� �������� ������ ��� ����������");
define("TOOLS_COPYSUB_ERROR_LEVEL_COUNT", "������ ����������� ������ � ������������ ���������");
define("TOOLS_COPYSUB_ERROR_PARAM", "�������� ���������");
define("TOOLS_COPYSUB_ERROR_SITE_NOT_FOUND", "���� �� ������");

# TOOLS TRASH
define("TOOLS_TRASH", "������� ��������� ��������");
define("TOOLS_TRASH_CLEAN", "�������� �������");

# MODERATION SECTION
define("NETCAT_MODERATION_NO_OBJECTS_IN_SUBCLASS", "� ������ ��������� ������� ��� ������ ��� ������.");

define("NETCAT_MODERATION_ERROR_NORIGHTS", "� ��� ��� ������� ��� ������������� ��������.");
define("NETCAT_MODERATION_ERROR_NORIGHT", "� ��� ��� ���� �� ��� ��������");
define("NETCAT_MODERATION_ERROR_NORIGHTGUEST", "�������� ����� �� ��������� ��������� ��� ��������");
define("NETCAT_MODERATION_ERROR_NOOBJADD", "������ ���������� �������.");
define("NETCAT_MODERATION_ERROR_NOOBJCHANGE", "������ ��������� �������.");
define("NETCAT_MODERATION_MSG_OBJADD", "������ ��������.");
define("NETCAT_MODERATION_MSG_OBJADDMOD", "������ ����� �������� ����� �������� ���������������.");
define("NETCAT_MODERATION_MSG_OBJCHANGED", "������ �������.");
define("NETCAT_MODERATION_MSG_OBJDELETED", "������ ������.");
define("NETCAT_MODERATION_FILES_UPLOADED", "�������");
define("NETCAT_MODERATION_FILES_DELETE", "������� ����");
define("NETCAT_MODERATION_LISTS_CHOOSE", "-- ������� --");
define("NETCAT_MODERATION_RADIO_EMPTY", "�� ��������");
define("NETCAT_MODERATION_PRIORITY", "��������� �������");
define("NETCAT_MODERATION_TURNON", "��������");
define("NETCAT_MODERATION_OBJADDED", "���������� �������");
define("NETCAT_MODERATION_OBJUPDATED", "��������� �������");
define("NETCAT_MODERATION_MSG_OBJSDELETED", "������� �������");
define("NETCAT_MODERATION_OBJ_ON", "���");
define("NETCAT_MODERATION_OBJ_OFF", "����");
define("NETCAT_MODERATION_OBJECT", "������");
define("NETCAT_MODERATION_MORE", "���");
define("NETCAT_MODERATION_MORE_CONTAINER", "�������� � �����������...");
define("NETCAT_MODERATION_MORE_BLOCK", "�������� � ������...");
define("NETCAT_MODERATION_MORE_OBJECT", "�������� � ��������...");
define("NETCAT_MODERATION_BLOCK_SETTINGS", "��������� �����");
define("NETCAT_MODERATION_DELETE_BLOCK", "������� ����");
define("NETCAT_MODERATION_ADD_BLOCK", "�������� ����");
define("NETCAT_MODERATION_ADD_BLOCK_BEFORE", "��");
define("NETCAT_MODERATION_ADD_BLOCK_INSIDE", "������");
define("NETCAT_MODERATION_ADD_BLOCK_AFTER", "�����");
define("NETCAT_MODERATION_ADD_BLOCK_RELATIVE_TO_THIS_CONTAINER", "����������");
define("NETCAT_MODERATION_ADD_BLOCK_RELATIVE_TO_CONTAINER", "���������� �%s�");
define("NETCAT_MODERATION_ADD_BLOCK_RELATIVE_TO_BLOCK", "����� �%s�");
define("NETCAT_MODERATION_ADD_BLOCK_RELATIVE_TO_THIS_BLOCK", "����� �����");
define("NETCAT_MODERATION_ADD_BLOCK_TITLE", "���������� �����");
define("NETCAT_MODERATION_ADD_BLOCK_WRAP_BLOCK", "����� ���� � ���� �%s� ����� �������� � ����� ���������.");
define("NETCAT_MODERATION_ADD_OBJECT", "��������");
define("NETCAT_MODERATION_ADD_OBJECT_DEFAULT", "�������");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_HEADER", "������� ������������?");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_BODY", "���� �%s� � ��� ���������� ����� ������� �� ��������. ��� ������������� ������� ���������.");
define("NETCAT_MODERATION_COMPONENT_SEARCH_BY_NAME", "����� �� ��������");
define("NETCAT_MODERATION_CLEAR_FIELD", "��������");
define("NETCAT_MODERATION_COMPONENT_NO_TEMPLATE", "�������� ������ ����������");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE", "������");
define("NETCAT_MODERATION_COMPONENT_TEMPLATES", "�������");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_PREV", "���������� ������");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_NEXT", "��������� ������");
define("NETCAT_MODERATION_COPY_BLOCK", "����������");
define("NETCAT_MODERATION_CUT_BLOCK", "��������");
define("NETCAT_MODERATION_PASTE_BLOCK", "�������� ������������� (����������) ����");
define("NETCAT_MODERATION_CONTAINER", "���������");
define("NETCAT_MODERATION_MAIN_CONTAINER", "���������� �������");
define("NETCAT_MODERATION_ADD_CONTAINER", "�������� ���������");
define("NETCAT_MODERATION_REMOVE_IMAGE", "������� �����������");
define("NETCAT_MODERATION_REPLACE_IMAGE", "�������� �����������");

define("NETCAT_MODERATION_WARN_COMMITDELETION", "����������� �������� ������� #%s");
define("NETCAT_MODERATION_WARN_COMMITDELETIONINCLASS", "����������� �������� �������� ��������� #%s");

define("NETCAT_MODERATION_PASSWORD", "������ (*)");
define("NETCAT_MODERATION_PASSWORDAGAIN", "������� ������ ��� ���");
define("NETCAT_MODERATION_INFO_REQFIELDS", "���������� (*) �������� ����, ������������ ��� ����������.");
define("NETCAT_MODERATION_BUTTON_ADD", "��������");
define("NETCAT_MODERATION_BUTTON_CHANGE", "��������� ���������");
define("NETCAT_MODERATION_BUTTON_RESET", "�����");

define("NETCAT_MODERATION_COMMON_KILLALL", "������� �������");
define("NETCAT_MODERATION_COMMON_KILLONE", "������� ������");

define("NETCAT_MODERATION_MULTIFILE_SIZE", "� ���� �%NAME� ��������� ����� � ��������, ����������� ���������� (%SIZE)");
define("NETCAT_MODERATION_MULTIFILE_TYPE", "� ���� �%NAME� ��������� ����� ������������� ����");
define("NETCAT_MODERATION_MULTIFILE_MIN_COUNT", "� ���� �%NAME� ������ ���� ��������� �� ����� %FILES.");
define("NETCAT_MODERATION_MULTIFILE_MAX_COUNT", "� ���� �%NAME� ����� ���� ��������� �� ����� %FILES.");
define("NETCAT_MODERATION_MULTIFILE_COUNT_FILES", "�����,������,������");
define("NETCAT_MODERATION_MULTIFILE_DEFAULT_CUSTOM_NAME_CAPTION", "�������� �����");
define("NETCAT_MODERATION_ADD", "�������� ���");

define("NETCAT_MODERATION_MSG_ONE", "���� �%NAME� �������� ������������ ��� ����������.");
define("NETCAT_MODERATION_MSG_TWO", "� ���� �%NAME� ������� �������� ������������� ����.");
define("NETCAT_MODERATION_MSG_SIX", "���������� �������� ���� �%NAME�.");
define("NETCAT_MODERATION_MSG_SEVEN", "���� �%NAME� ��������� ���������� ������.");
define("NETCAT_MODERATION_MSG_EIGHT", "������������ ������ ����� �%NAME�.");
define("NETCAT_MODERATION_MSG_TWENTYONE", "������� ������������ �������� �����.");
define("NETCAT_MODERATION_MSG_RETRYPASS", "��������� ������ �� ���������");
define("NETCAT_MODERATION_MSG_PASSMIN", "������ ������� ��������. ����������� ����� ������ %s ��������.");
define("NETCAT_MODERATION_MSG_NEED_AGREED", "���������� ����������� � ���������������� �����������");
define("NETCAT_MODERATION_MSG_LOGINALREADY", "����� %s ����� ������ �������������");
define("NETCAT_MODERATION_MSG_LOGININCORRECT", "����� �������� ����������� �������");
define("NETCAT_MODERATION_BACKTOSECTION", "��������� � ������");

define("NETCAT_MODERATION_ISON", "�������");
define("NETCAT_MODERATION_ISOFF", "��������");
define("NETCAT_MODERATION_OBJISON", "������ �������");
define("NETCAT_MODERATION_OBJISOFF", "������ ��������");
define("NETCAT_MODERATION_OBJSAREON", "������� ��������");
define("NETCAT_MODERATION_OBJSAREOFF", "������� ���������");
define("NETCAT_MODERATION_CHANGED", "ID ����������� ������������");
define("NETCAT_MODERATION_CHANGE", "��������");
define("NETCAT_MODERATION_DELETE", "�������");
define("NETCAT_MODERATION_TURNTOON", "��������");
define("NETCAT_MODERATION_TURNTOOFF", "���������");
define("NETCAT_MODERATION_ID", "�������������");
define("NETCAT_MODERATION_COPY_OBJECT", "���������� / ���������");

define("NETCAT_MODERATION_REMALL", "������� ���");
define("NETCAT_MODERATION_DELETESELECTED", "������� ���������");
define("NETCAT_MODERATION_SELECTEDON", "�������� ���������");
define("NETCAT_MODERATION_SELECTEDOFF", "��������� ���������");
define("NETCAT_MODERATION_NOTSELECTEDOBJ", "�� ������� �� ������ �������");
define("NETCAT_MODERATION_APPLY_CHANGES_TITLE", "��������� ���������?");
define("NETCAT_MODERATION_APPLY_CHANGES_TEXT", "�� ������������� ������ ��������� ���������?");
define("NETCAT_MODERATION_CLASSID", "����� ���������� �������");
define("NETCAT_MODERATION_ADDEDON", "ID ����������� ������������");

define("NETCAT_MODERATION_MOD_NOANSWER", "�� �����");
define("NETCAT_MODERATION_MOD_DON", " �� ");
define("NETCAT_MODERATION_MOD_FROM", " �� ");
define("NETCAT_MODERATION_MODA", "--------- �� ����� ---------");

define("NETCAT_MODERATION_FILTER", "������");
define("NETCAT_MODERATION_TITLE", "���������");
define("NETCAT_MODERATION_DESCRIPTION", "��������");

define("NETCAT_MODERATION_TRASHED_OBJECTS", "��������� �������");
define("NETCAT_MODERATION_TRASHED_OBJECTS_RESTORE", "������������ ������");

define("NETCAT_MODERATION_NO_RELATED", "[���]");
define("NETCAT_MODERATION_RELATED_INEXISTENT", "[�������������� ������ ID=%s]");
define("NETCAT_MODERATION_CHANGE_RELATED", "��������");
define("NETCAT_MODERATION_REMOVE_RELATED", "�������");
define("NETCAT_MODERATION_SELECT_RELATED", "�������");
define("NETCAT_MODERATION_COPY_HERE_RELATED", "���������� ����");
define("NETCAT_MODERATION_MOVE_HERE_RELATED", "����������� ����");
define("NETCAT_MODERATION_CONFIRM_COPY_RELATED", "����������� ��������");
define("NETCAT_MODERATION_RELATED_POPUP_TITLE", "����� ���������� ������� (���� &quot;%s&quot;)");
define("NETCAT_MODERATION_RELATED_NO_CONCRETE_CLASS_IN_SUB", "� ������ ������� ��� ���������� &laquo;%s&raquo;.");
define("NETCAT_MODERATION_RELATED_NO_ANY_CLASS_IN_SUB", "� ������ ������� ��� �� ������ ����������� ���������.");
define("NETCAT_MODERATION_RELATED_ERROR_SAVING", "�� ������� ��������� ��������� �������� (��������, ����� �������������� ��������� ������� ���� �������). ���������� ������� ��������� �������� ��� ���.");
define("NETCAT_MODERATION_COPY_SUCCESS", "����������� ������� ������� ���������");
define("NETCAT_MODERATION_MOVE_SUCCESS", "����������� ������� ������� ���������");


define("NETCAT_MODERATION_SEO_TITLE", "��������� �������� (Title)");
define("NETCAT_MODERATION_SEO_H1", "��������� �� �������� (H1)");
define("NETCAT_MODERATION_SEO_KEYWORDS", "�������� ����� ��� �����������");
define("NETCAT_MODERATION_SEO_DESCRIPTION", "�������� �������� ��� �����������");

define("NETCAT_MODERATION_SMO_TITLE", "��������� ��� ���������� �����");
define("NETCAT_MODERATION_SMO_TITLE_HELPER", "������ ���������� ������ ��� ���������� ������ �� �������� � �������� ��� ���������");
define("NETCAT_MODERATION_SMO_DESCRIPTION", "�������� ��� ���������� �����");
define("NETCAT_MODERATION_SMO_DESCRIPTION_HELPER", "������ ������� ������ ��� ���������� ������ �� �������� � �������� ��� ���������");
define("NETCAT_MODERATION_SMO_IMAGE", "����������� ��� ���������� �����");

define("NETCAT_MODERATION_STANDART_FIELD_USER_ID", "ID ������������");
define("NETCAT_MODERATION_STANDART_FIELD_USER", "������������");
define("NETCAT_MODERATION_STANDART_FIELD_PRIORITY", "���������");
define("NETCAT_MODERATION_STANDART_FIELD_KEYWORD", "�������� �����");
define("NETCAT_MODERATION_STANDART_FIELD_NC_TITLE", "SEO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_KEYWORDS", "SEO Meta Keywords");
define("NETCAT_MODERATION_STANDART_FIELD_NC_DESCRIPTION", "SEO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_IMAGE", "�����������");
define("NETCAT_MODERATION_STANDART_FIELD_NC_ICON", "������");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_TITLE", "SMO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_DESCRIPTION", "SMO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_IMAGE", "SMO Meta Image");
define("NETCAT_MODERATION_STANDART_FIELD_IP", "IP");
define("NETCAT_MODERATION_STANDART_FIELD_USER_AGENT", "�������");
define("NETCAT_MODERATION_STANDART_FIELD_CREATED", "������");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_UPDATED", "��������");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_ID", "����. ID ������������");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER", "����. ������������");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_IP", "����. IP");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_AGENT", "����. �������");

define("NETCAT_MODERATION_VERSION", "��������");
define("NETCAT_MODERATION_VERSION_NOT_FOUND", "�������� �����������");
define("NETCAT_SAVE_DRAFT", "��������� ��������");

# MODULE
define("NETCAT_MODULES", "������");
define("NETCAT_MODULES_TUNING", "��������� ������");
define("NETCAT_MODULES_PARAM", "��������");
define("NETCAT_MODULES_VALUE", "��������");
define("NETCAT_MODULES_ADDPARAM", "�������� ��������");
define("NETCAT_MODULE_INSTALLCOMPLIED", "��������� ������ ���������.");
define("NETCAT_MODULE_ALWAYS_LOAD", "��������� ������");
define("NETCAT_MODULE_ONOFF", "���/����");
define("NETCAT_MODULE_MODULE_UNCHECKED", "������ ��������, ��� ��������� ����������. �������� ������ ����� � <a href='".$ADMIN_PATH."modules/index.php'>������ �������.</a>");

# MODULE DEFAULT
define("NETCAT_MODULE_DEFAULT_DESCRIPTION", "������ ������ ������������ ��� �������� ��������������� �������� � �������. �� ������ ���������� ����������� ������� � " . nc_module_path('default') . "function.inc.php � ��������� ����������� �������, ��������������� � �������� �� �������� � " . nc_module_path('default') . "index.php. �����, �� ������ �������� ���������� ��������� ������� ������ � ������������� ���� ����.<br><br>���������� �� �������� ����������� ������� �� ������� ����� � &quot;����������� ������������&quot; � ������� &quot;���������� �������&quot;.");

#CODE MIRROR
define('NETCAT_SETTINGS_CODEMIRROR', '��������� ����������');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED', '��������');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_ON', '��');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT', '��������� �� ���������');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_ON', '��');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE', '��������������');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_ON', '��');
define('NETCAT_SETTINGS_CODEMIRROR_HELP', '���� ���������');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_ON', '��');
define('NETCAT_SETTINGS_CODEMIRROR_ENABLE', '�������� ��������');
define('NETCAT_SETTINGS_CODEMIRROR_SWITCH', '����������� ��������');
define('NETCAT_SETTINGS_CODEMIRROR_WRAP', '���������� ������');
define('NETCAT_SETTINGS_CODEMIRROR_FULLSCREEN', '�� ���� �����');

define('NETCAT_SETTINGS_DRAG', '�������������� �������� (��������, ����������, ��������, ����������� � �. �.)');
define('NETCAT_SETTINGS_DRAG_SILENT', '���������� ��� �������������');
define('NETCAT_SETTINGS_DRAG_CONFIRM', '���������� ������������� ����� ���������');
define('NETCAT_SETTINGS_DRAG_DISABLED', '��������� ��������������');

# EDITOR
define('NETCAT_SETTINGS_EDITOR', '������� ��������������');
define('NETCAT_SETTINGS_EDITOR_TYPE', '��� HTML-���������');
define('NETCAT_SETTINGS_EDITOR_FCKEDITOR', 'FCKeditor');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR', 'CKEditor');
define('NETCAT_SETTINGS_EDITOR_TINYMCE', 'TinyMCE');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_FILE_SYSTEM', '��������� ������������ ����� �� ������ ������ ������������� (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CYRILIC_FOLDER', '��������� ������� ��������� � ������ ����� ��������� ��������� (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CONTENT_FILTER', '�������� <a href="http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter" target="_blank">���������� ��������</a> (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_EMBED_ON', '��');
define('NETCAT_SETTINGS_EDITOR_EMBED_TO_FIELD', '�������� �������� � ���� ��� ��������������');
define('NETCAT_SETTINGS_EDITOR_SEND', '���������');
define('NETCAT_SETTINGS_EDITOR_STYLES_SAVE', '��������� ���������');
define('NETCAT_SETTINGS_EDITOR_STYLES', '����� ������ ��� FCKeditor');
define('NETCAT_SETTINGS_EDITOR_SKINS', '����������');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE', '����� ������� Enter');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_P', '��� P');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_BR', '��� BR');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_DIV', '��� DIV');
define('NETCAT_SETTINGS_EDITOR_SAVE', '��������� ������� ��������');
define('NETCAT_SETTINGS_EDITOR_KEYCODE', '���������� ������ �� Ctrl + %s, ��������� ���������� �������� Ctrl + F5');

define('NETCAT_SEARCH_FIND_IT', '������');
define('NETCAT_SEARCH_ERROR', '���������� ����� �� ������� ����������.');

# JS settings
define('NETCAT_SETTINGS_JS', '�������� �������� ��������');
define('NETCAT_SETTINGS_JS_FUNC_NC_JS', '������� nc_js()');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_DOLLAR', '��������� jQuery ������ $');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_EXTENSIONS_ALWAYS', '������ ��������� ���������� jQuery');
define('NETCAT_SETTINGS_JS_LOAD_MODULES_SCRIPTS', '��������� ��������� �������');
define('NETCAT_SETTINGS_MINIFY_STATIC_FILES', '�������������� CSS � JS ����� � �����-������');

define('NETCAT_SETTINGS_TRASHBIN', '������� ��������� ��������');
define('NETCAT_SETTINGS_TRASHBIN_USE', '������������ �������');

#Components
define('NETCAT_SETTINGS_COMPONENTS', '����������');
define('NETCAT_SETTINGS_REMIND_SAVE', '���������� � ���������� (��������� ���������� �������� Ctrl + F5)');
define('NETCAT_SETTINGS_PACKET_OPERATIONS', '�������� ��������� �������� ��� ���������');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE', '�������� ����������� �������� ������ ���������� ���� ��� �������������� ����������');

define('NETCAT_SETTINGS_QUICKBAR', '������ �������� ��������������');
define('NETCAT_SETTINGS_QUICKBAR_ENABLE', '�������� �������������� � �������');
define('NETCAT_SETTINGS_QUICKBAR_ON', '��');

# ALT ADMIN BLOCKS
define('NETCAT_SETTINGS_ALTBLOCKS', '�������������� ����� �����������������');
define('NETCAT_SETTINGS_ALTBLOCKS_ON', '��');
define('NETCAT_SETTINGS_ALTBLOCKS_TEXT', '������������ �������������� ����� �����������������');
define('NETCAT_SETTINGS_ALTBLOCKS_PARAMS', '�������������� ��������� ��� �������� (������� � &)');

define('NETCAT_SETTINGS_HTTP_PROXY', '������������ HTTP-������-������ ��� ������� � ������� ����������');
define('NETCAT_SETTINGS_HTTP_PROXY_HOST', 'IP-����� ������-�������');
define('NETCAT_SETTINGS_HTTP_PROXY_PORT', '����');
define('NETCAT_SETTINGS_HTTP_PROXY_USER', '������������');
define('NETCAT_SETTINGS_HTTP_PROXY_PASSWORD', '������');

define('NETCAT_SETTINGS_USETOKEN', '������������� ����� ������������� ��������');
define('NETCAT_SETTINGS_USETOKEN_ADD', '��� ����������');
define('NETCAT_SETTINGS_USETOKEN_EDIT', '��� ���������');
define('NETCAT_SETTINGS_USETOKEN_DROP', '��� ��������');
define('NETCAT_SETTINGS_OBJECTS_FULLINK', '������ ����������� ��������');
define("CONTROL_SETTINGSFILE_BASIC_VERSION", "������ �������");
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_FIELD", "���� (� �������� email) � ������� �������������");
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_NONE", "���� �����������");
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_OFF', '���');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_OFF', '���');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_OFF', '���');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_OFF', '���');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION', '���������� ������������� ���������� inline-���������');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_ON', '������������� ���������� inline-��������� ��������');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_OFF', '������������� ���������� inline-��������� ���������');
define('NETCAT_SETTINGS_EDITOR_EMBEDED', '�������� ������� � ���� ��� ��������������');
define('NETCAT_SETTINGS_EDITOR_EMBED_OFF', '���');
define('NETCAT_SETTINGS_EDITOR_STYLES_CANCEL', '������');
define('NETCAT_SETTINGS_TRASHBIN_MAXSIZE', '������������ ������ �������');
define('NETCAT_SETTINGS_REMIND_SAVE_INFO', '���������� � ������������� ����������');
define('NETCAT_SETTINGS_PACKET_OPERATIONS_INFO', '�������� ��������� �������� ��� ���������');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE_INFO', '�������� ����������� �������� ������ ���������� ���� ��� �������������� ����������');
define('NETCAT_SETTINGS_DISABLE_BLOCK_MARKUP_INFO', '��������� <a href="https://netcat.ru/developers/docs/components/stylesheets/" target="_blank">�������������� ��������</a> ��� ����������� �����������');
define("CONTROL_CLASS_IS_MULTIPURPOSE_SWITCH", "������������ ������");
define("CONTROL_CLASS_COMPATIBLE_FIELDS", "������ ������������ ����� � ����������� ����������� (�� ������ �� �������)");
define('NETCAT_SETTINGS_QUICKBAR_OFF', '���');
define('NETCAT_SETTINGS_ALTBLOCKS_OFF', '���');

# Export / Import
define('NETCAT_IMPORT_FIELD', 'XML-���� �������');

define('NETCAT_FILEUPLOAD_ERROR', '������! � ��� ��� ���� �� ���������� %s �� ���� �������.');


define("NETCAT_HTTP_REQUEST_SAVING", "����������...");
define("NETCAT_HTTP_REQUEST_SAVED", "��������� ���������");
define("NETCAT_HTTP_REQUEST_ERROR", "������ ��� ����������");
define("NETCAT_HTTP_REQUEST_HINT", "�� ������ ��������� ��� �����, ����� Ctrl + %s");

# Index page menu
define("SECTION_INDEX_MENU_TITLE", "������� ����");
define("SECTION_INDEX_MENU_HOME", "�������");
define("SECTION_INDEX_MENU_SITE", "����");
define("SECTION_INDEX_MENU_DEVELOPMENT", "����������");
define("SECTION_INDEX_MENU_TOOLS", "�����������");
define("SECTION_INDEX_MENU_SETTINGS", "���������");
define("SECTION_INDEX_MENU_HELP", "�������");

define("SECTION_INDEX_HELP_SUBMENU_HELP", "������� NetCat");
define("SECTION_INDEX_HELP_SUBMENU_DOC", "������������");
define("SECTION_INDEX_HELP_SUBMENU_HELPDESC", "������-���������");
define("SECTION_INDEX_HELP_SUBMENU_FORUM", "�����");
define("SECTION_INDEX_HELP_SUBMENU_BASE", "���� ������");
define("SECTION_INDEX_HELP_SUBMENU_ABOUT", "� ���������");

define("SECTION_INDEX_SITE_LIST", "������ ������");

define("SECTION_INDEX_WIZARD_SUBMENU_CLASS", "������ �������� ����������");
define("SECTION_INDEX_WIZARD_SUBMENU_SITE", "������ �������� �����");

define("SECTION_INDEX_FAVORITE_ANOTHER_SUB", "������ ������...");
define("SECTION_INDEX_FAVORITE_ADD", "�������� � ��� ����");
define("SECTION_INDEX_FAVORITE_LIST", "������������� ��� ����");
define("SECTION_INDEX_FAVORITE_SETTINGS", "���������");

define("SECTION_INDEX_WELCOME", "����� ����������");
define("SECTION_INDEX_WELCOME_MESSAGE", "������������, %s!<br />�� ���������� � ������� ���������� �������� &laquo;%s&raquo;.<br />��� ��������� �����: %s.");
define("SECTION_INDEX_TITLE", "������� ���������� NetCat");

# SITE
## TABS
define("SITE_TAB_SITEMAP", "����� �����");
define("SITE_TAB_SETTINGS", "���������");
define("SITE_TAB_STATS", "����������");
define("SITE_TAB_AREA_INFOBLOCKS", "�������� ���������");
## TOOLBAR
define("SITE_TOOLBAR_INFO", "����� ����������");
define("SITE_TOOLBAR_SUBLIST", "������ ��������");


#SUBDIVISION
## TABS
## TOOLBAR
define("SUBDIVISION_TAB_INFO_TOOLBAR_INFO", "���������� � �������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_SUBLIST", "������ ��������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_CCLIST", "������ ����������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST", "������������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_EDIT", "��������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_DESIGN", "����������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SEO", "SEO/SMO");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SYSTEM", "���������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_FIELDS", "�������������� ���������");


## BUTTONS
define("SUBDIVISION_TAB_PREVIEW_BUTTON_PREVIEW", "�������� � ����� ����");

define("SITE_SITEMAP_SEARCH", "����� �� ����� �����");
define("SITE_SITEMAP_SEARCH_NOT_FOUND", "�� �������");

## TEXT
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_READ_ACCESS", "������ �� ������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_COMMENT_ACCESS", "������ �� ���������������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_WRITE_ACCESS", "������ �� ������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_EDIT_ACCESS", "������ �� ��������������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_SUBSCRIBE_ACCESS", "������ � ��������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_MODERATORS", "����������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ADMINS", "��������������");

define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ALL_USERS", "��� ������������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_REGISTERED_USERS", "������������������ ������������");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_PRIVILEGED_USERS", "����������������� ������������");

# CLASS WIZARD

define("WIZARD_CLASS_FORM_SUBDIVISION_PARENTSUB", "������������ ������");

define("WIZARD_PARENTSUB_SELECT_POPUP_TITLE", "����� ������������� �������");

define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_PARENTSUB", "������� ������������ ������");
define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_SUBDIVISION", "������� ������");
define("WIZARD_CLASS_FORM_SUBDIVISION_DELETE", "�������");

define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE", "��� �������");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SINGLE", "������������ ������ �� ��������");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_LIST", "������ ��������");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SEARCH", "����� �� ������ ��������");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_FORM", "���-�����");

define("WIZARD_CLASS_FORM_SETTINGS_NO_SETTINGS", "��� ������� ���� ������� �������� �� ��������������.");
define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_LIST", "���� ��� ������ ��������");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_OBJECT_BY_FIELD", "����������� ������� �� ����");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_ASC", "�� �����������");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_DESC", "�� ��������");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION", "��������� �� ���������");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_NEXT_PREV", "������� �� ������ �������� ������ &laquo;���������-����������&raquo;");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_PAGE_NUMBER", "�� ������� �������");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_BOTH", "��� ��������");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_OF_NAVIGATION", "��������� ������ ���������");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_TOP", "������ ��������");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTTOM", "����� ��������");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTH", "��� ��������");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_TYPE", "������");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE", "�������");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_DELIMITER_TYPE", "��� �����������");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE_SETTINGS", "��������� ���� �������");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BACKGROUND", "���������� ���");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BORDER", "������� �������");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE", "�������� � ��������� �����������");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE_LINK_TYPE", "������ �������� �� �������� ����������� �������");
define("WIZARD_CLASS_FORM_SETTINGS_CHECK_FIELDS_TO_FULL_PAGE", "�������� ���� ��� ������� �� ������� ����� ������������� ������� �� �������� ����������� �������");

define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_SEARCH", "����, �� ������� ����� ������������� �����");

define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_FIELDS_SETTINGS", "��������� ����� �������� �����");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_NAME", "��� �����������");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_MAIL", "Email �����������");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SUBJECT", "���� ������");

## TABS
define("WIZARD_CLASS_TAB_SELECT_TEMPLATE_TYPE", "����� ���� �������");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_FIELDS", "���������� ����� �������");
define("WIZARD_CLASS_TAB_TEMPLATE_SETTINGS", "��������� �������");
define("WIZARD_CLASS_TAB_SUBSEQUENT_ACTION", "���������� ��������");
define("WIZARD_CLASS_TAB_CREATION_SUBDIVISION_WITH_NEW_TEMPLATE", "�������� ������� � ����� ��������");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_TO_EXISTENT_SUBDIVISION", "���������� ������� � ������������� �������");

## BUTTONS
define("WIZARD_CLASS_BUTTON_NEXT_TO_ADDING_FIELDS", "������� � ���������� �����");
define("WIZARD_CLASS_BUTTON_FINISH_ADDING_FIELDS", "��������� ���������� �����");
define("WIZARD_CLASS_BUTTON_SAVE_SETTINGS", "��������� ���������");
define("WIZARD_CLASS_BUTTON_ADDING_SUBDIVISION_WITH_NEW_TEMPLATE", "�������� ������ � ����� �����������");
define("WIZARD_CLASS_BUTTON_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "������� ������ � ����� �����������");
define("WIZARD_CLASS_BUTTON_NEXT_TO_SUBDIVISION_SELECTION", "������� � ������ �������");

## LINKS
define("WIZARD_CLASS_LINKS_VIEW_TEMPLATE_CODE", "���������� ����������� ��� �������");
define("WIZARD_CLASS_LINKS_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "������� ������ � ���� ��������");
define("WIZARD_CLASS_LINKS_ADD_TEMPLATE_TO_EXISTENT_SUBDIVISION", "���������� ������ � ������������� �������");
define("WIZARD_CLASS_LINKS_CREATE_NEW_TEMPLATE", "������� ����� ������");

define("WIZARD_CLASS_LINKS_RETURN_TO_FIELDS_ADDING", "��������� � ���������� �����");

## COMMON
define("WIZARD_CLASS_STEP", "���");
define("WIZARD_CLASS_STEP_FROM", "��");

define("WIZARD_CLASS_DEFAULT", "�� ���������");

define("WIZARD_CLASS_ERROR_NO_FIELDS", "� ������ ���������� �������� ���� �� ���� ����!");

# SITE WIZARD
define("WIZARD_SITE_FORM_WHICH_MODULES", "����� ������ �� ������ ������������� �� �����?");

## TABS
define("WIZARD_SITE_TAB_NEW_SITE_CREATION", "�������� ������ �����");
define("WIZARD_SITE_TAB_NEW_SITE_ADD_SUB", "���������� �������� �����");

## BUTTONS
define("WIZARD_SITE_BUTTON_ADD_SUBS", "�������� ����������");
define("WIZARD_SITE_BUTTON_FINISH_ADD_SUBS", "���������");

## COMMON
define("WIZARD_SITE_STEP", "���");
define("WIZARD_SITE_STEP_FROM", "��");
define("WIZARD_SITE_STEP_TWO_DESCRIPTION", "�������� ��������� ��������. ������ ���� ������ ����� ��������� �������� � �������� 404. ������ �������� ��� ���� ��� ���������.");

#DEMO MODE
define("DEMO_MODE_ADMIN_INDEX_MESSAGE", "���� \"%s\" ��������  ����-������, �� ������ ��������� ��� � <a href='%s'>��������� ���������� �����</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_GUEST", "��� �� ��������� ����, �� ������������ ������ ��� ������������.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_ADMIN", "���� � ����-������, ����� ��� ����� <a href='%s'>� ����������</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_CLOSE", "�������");

# FAVORITE
## HEADER TEXT
define("FAVORITE_HEADERTEXT", "��������� �������");


# CRONTAB
##TABS
define("CRONTAB_TAB_LIST", "������ �����");
define("CRONTAB_TAB_ADD", "���������� ������");
define("CRONTAB_TAB_EDIT", "�������������� ������");

##TRASH
define("TRASH_TAB_LIST", "������� ��������� ��������");
define("TRASH_TAB_TITLE", "������ ��������� ��������");
define("TRASH_TAB_SETTINGS", "���������");

# REDIRECT
##TABS
define("REDIRECT_TAB_LIST", "������ �������������");
define("REDIRECT_TAB_ADD", "���������� �������������");
define("REDIRECT_TAB_EDIT", "�������������� �������������");


# SYSTEM SETTINGS
##TABS
define("SYSTEMSETTINGS_TAB_LIST", "������� ��������� �������");
define("SYSTEMSETTINGS_TAB_ADD", "�������������� ������� ��������");
define("SYSTEMSETTINGS_SAVE_OK", "��������� ������� ���������");
define("SYSTEMSETTINGS_SAVE_ERROR", "������ ���������� �������� �������");

# WYSIWYG SETTINGS
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_SETTINGS", "���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_PANELS", "������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_SETTINGS", "��������� WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_PANELS", "������ ��������� WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_DELETE_CONFIRMATION", "������������� �������� ������� WYSIWYG ���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_EDIT_FORM", " - �������������� ������ WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_ADD_FORM", "���������� ������ WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_NOT_EXISTS", "����� ������ �� ����������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_NO_PANELS", "��� �� ����� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_EDIT_SUCCESSFUL", "������ ������� ��������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_ADD_SUCCESSFUL", "������ ������� ���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_NOT_SELECTED", "�� ������� �� ����� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETED", "������ ������� �������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETE_ERROR", "������ ��� �������� �������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_FILL_PANEL_NAME", "������� ��� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_SELECT_TOOLBAR", "�������� ���� �� ���� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_DELETE_SELECTED", "������� ���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CONFIRM_DELETE", "����������� ��������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CANCEL", "������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_EDIT_PANEL", "�������� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_ADD_PANEL", "�������� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_PANEL_NAME", "�������� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_DELETE", "�������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_ARE_YOU_REALLY_WANT_TO_DELETE_PANELS", "�� ������������� ������� ������� ������?");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_FULL", "������ ������� ��������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_INLINE", "������ inline ��������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_NAME", "�������� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_PREVIEW", "������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_SETTINGS", "��������� ������ ������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_MODE", "������������ ���� ���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCUMENT", "�������� � ����������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_TOOLS", "�����������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCTOOLS", "�������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLIPBOARD", "����� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_UNDO", "������ ��������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FIND", "����� � ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SELECTION", "���������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SPELLCHECKER", "�������� ����������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FORMS", "�����");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BASICSTYLES", "������� �����");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLEANUP", "������� ��������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LIST", "������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INDENT", "�������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BLOCKS", "����� ������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_ALIGN", "������������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LINKS", "������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INSERT", "������� ��������");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_STYLES", "�����");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_COLORS", "�����");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_OTHERS", "������ �����������");

define("NETCAT_WYSIWYG_FCKEDITOR_SETTINGS_TITLE_SETTINGS", "��������� WYSIWYG");

define("NETCAT_WYSIWYG_SETTINGS_PANEL_SETTINGS", "��������� �������");
define("NETCAT_WYSIWYG_SETTINGS_THIS_EDITOR_IS_USED_BY_DEFAULT", "���� �������� ������������ �� ���������");
define("NETCAT_WYSIWYG_SETTINGS_USE_BY_DEFAULT", "������������ ���� �������� �� ���������");
define("NETCAT_WYSIWYG_SETTINGS_BASIC_SETTINGS", "�������� ���������");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_EDITOR_ACTIVATED", "�������� ������� �����������");
define("NETCAT_WYSIWYG_SETTINGS_PANEL_NOT_SELECTED", "�� �������");
define("NETCAT_WYSIWYG_SETTINGS_BUTTON_SAVE", "���������");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVED", "��������� WYSIWYG ���������");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVE_ERROR", "��������� ������ ��� ���������� WYSIWYG ��������");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_SETTINGS", "��������� config.js");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_FILE", "���� config.js");

define("NETCAT_WYSIWYG_EDITOR_OUTWORN", "���� �������� �������, ����������� ������������ �� ������ �������� � ������� ���������� %s � �������");

# Not Elsewhere Specified
define("NOT_ELSEWHERE_SPECIFIED", "�� ���������");
define("NOT_ADD_CLASS", "�� ��������� �������� � ������");

# BBcodes
define("NETCAT_BBCODE_SIZE", "������ ������");
define("NETCAT_BBCODE_COLOR", "���� ������");
define("NETCAT_BBCODE_SMILE", "������");
define("NETCAT_BBCODE_B", "������");
define("NETCAT_BBCODE_I", "������");
define("NETCAT_BBCODE_U", "������������");
define("NETCAT_BBCODE_S", "�����������");
define("NETCAT_BBCODE_LIST", "������� ������");
define("NETCAT_BBCODE_QUOTE", "������");
define("NETCAT_BBCODE_CODE", "���");
define("NETCAT_BBCODE_IMG", "�����������");
define("NETCAT_BBCODE_URL", "������");
define("NETCAT_BBCODE_CUT", "��������� �����");

define("NETCAT_BBCODE_QUOTE_USER", "�����(�)");
define("NETCAT_BBCODE_CUT_MORE", "���������");
define("NETCAT_BBCODE_SIZE_DEF", "������");
define("NETCAT_BBCODE_ERROR_1", "����� BB-��� ������������� �������:");
define("NETCAT_BBCODE_ERROR_2", "������� BB-���� ������������� �������:");

# Help messages for BBcode
define("NETCAT_BBCODE_HELP_SIZE", "������ ������: [SIZE=8]��������� �����[/SIZE]");
define("NETCAT_BBCODE_HELP_COLOR", "���� ������: [COLOR=FF0000]�����[/COLOR]");
define("NETCAT_BBCODE_HELP_SMILE", "�������� �������");
define("NETCAT_BBCODE_HELP_B", "������ �����: [B]�����[/B]");
define("NETCAT_BBCODE_HELP_I", "��������� �����: [I]�����[/I]");
define("NETCAT_BBCODE_HELP_U", "������������ �����: [U]�����[/U]");
define("NETCAT_BBCODE_HELP_S", "����������� �����: [S]�����[/S]");
define("NETCAT_BBCODE_HELP_LIST", "������� ������: [LIST]�����[/LIST]");
define("NETCAT_BBCODE_HELP_QUOTE", "������: [QUOTE]�����[/QUOTE]");
define("NETCAT_BBCODE_HELP_CODE", "���: [CODE]���[/CODE]");
define("NETCAT_BBCODE_HELP_IMG", "�������� ��������");
define("NETCAT_BBCODE_HELP_IMG_URL", "����� ��������");
define("NETCAT_BBCODE_HELP_URL", "�������� ������");
define("NETCAT_BBCODE_HELP_URL_URL", "������");
define("NETCAT_BBCODE_HELP_URL_DESC", "��������");
define("NETCAT_BBCODE_HELP_CUT", "��������� ����� � ��������: [CUT=���������]�����[/CUT]");
define("NETCAT_BBCODE_HELP", "���������: ���� ����������� ������ �������� ��������������");

# Smiles
define("NETCAT_SMILE_SMILE", "������");
define("NETCAT_SMILE_BIGSMILE", "������� ������");
define("NETCAT_SMILE_GRIN", "�������");
define("NETCAT_SMILE_LAUGH", "����");
define("NETCAT_SMILE_PROUD", "������");
#
define("NETCAT_SMILE_YES", "��");
define("NETCAT_SMILE_WINK", "�����������");
define("NETCAT_SMILE_COOL", "�����");
define("NETCAT_SMILE_ROLLEYES", "��������");
define("NETCAT_SMILE_LOOKDOWN", "������");
#
define("NETCAT_SMILE_SAD", "��������");
define("NETCAT_SMILE_SUSPICIOUS", "��������������");
define("NETCAT_SMILE_ANGRY", "��������");
define("NETCAT_SMILE_SHAKEFIST", "��������");
define("NETCAT_SMILE_STERN", "�������");
#
define("NETCAT_SMILE_KISS", "�������");
define("NETCAT_SMILE_THINK", "������");
define("NETCAT_SMILE_THUMBSUP", "�����");
define("NETCAT_SMILE_SICK", "������");
define("NETCAT_SMILE_NO", "���");
#
define("NETCAT_SMILE_CANTLOOK", "�� ���� ��������");
define("NETCAT_SMILE_DOH", "���");
define("NETCAT_SMILE_KNOCKEDOUT", "� ����");
define("NETCAT_SMILE_EYEUP", "����");
define("NETCAT_SMILE_QUIET", "����");
#
define("NETCAT_SMILE_EVIL", "����");
define("NETCAT_SMILE_UPSET", "�������");
define("NETCAT_SMILE_UNDECIDED", "�����������");
define("NETCAT_SMILE_CRY", "������");
define("NETCAT_SMILE_UNSURE", "�� ������");

# nc_bytes2size
define("NETCAT_SIZE_BYTES", " ����");
define("NETCAT_SIZE_KBYTES", " ��");
define("NETCAT_SIZE_MBYTES", " ��");
define("NETCAT_SIZE_GBYTES", " ��");

// quickBar
define("NETCAT_QUICKBAR_BUTTON_VIEWMODE", "��������");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE", "��������������");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE_UNAVAILABLE_FOR_LONGPAGE", "�������������� ���������� � ������ longpage");
define("NETCAT_QUICKBAR_BUTTON_MORE", "���");
define("NETCAT_QUICKBAR_BUTTON_SUBDIVISION_SETTINGS", "��������� ��������");
define("NETCAT_QUICKBAR_BUTTON_TEMPLATE_SETTINGS", "����� �������");
define("NETCAT_QUICKBAR_BUTTON_ADMIN", "�����������������");

#SYNTAX EDITOR
define('NETCAT_SETTINGS_SYNTAXEDITOR', '��-���� ��������� ����������');
define('NETCAT_SETTINGS_SYNTAXEDITOR_ENABLE', '�������� ������������� ��������� ���������� � ������� (��������� ������������ ������� Ctrl+F5)');

#SYNTAX CHECK

# LICENSE
define('NETCAT_SETTINGS_LICENSE', '��������');
define('NETCAT_SETTINGS_LICENSE_PRODUCT', '��� ��������');

# NETCAT_DEBUG
define("NETCAT_DEBUG_ERROR_INSTRING", "������ � ������ : ");
define("NETCAT_DEBUG_BUTTON_CAPTION", "�������");

# NETCAT_PREVIEW
define("NETCAT_PREVIEW_BUTTON_CAPTIONCLASS", "������������ ����������");
define("NETCAT_PREVIEW_BUTTON_CAPTIONTEMPLATE", "������������ ������");

define("NETCAT_PREVIEW_BUTTON_CAPTIONADDFORM", "������������ ����� ����������");
define("NETCAT_PREVIEW_BUTTON_CAPTIONEDITFORM", "������������ ����� ��������������");
define("NETCAT_PREVIEW_BUTTON_CAPTIONSEARCHFORM", "������������ ����� ������");

define("NETCAT_PREVIEW_ERROR_NODATA", "������! �� �������� ������ ��� ������ ������������� ��� ������ ��������");
define("NETCAT_PREVIEW_ERROR_WRONGDATA", "������ � ������ ��� ������ �������������");
define("NETCAT_PREVIEW_ERROR_NOSUB", " ��� �� ������ ������� � ����� �����������. �������� ���� ��������� � ������ � ����� ������������� ������ ��������.");
define("NETCAT_PREVIEW_ERROR_NOMESSAGE", " ��� �� ������ ������� ������ ����������. �������� ���� �� ���� ������ ������ ���������� � ����� ������������� ������ ��������.");
define("NETCAT_PREVIEW_INFO_MORESUB", " ���� ��������� �������� � ����� �����������. �������� ������ ��� �������������.");
define("NETCAT_PREVIEW_INFO_CHOOSESUB", " �������� ������ ��� ������������� ������.");

# objects
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_SUPERVISOR", "������ SQL ������� � ������� nc_objects_list(%s, %s, \"%s\"), %s");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_USER", "������ � ������� ������ ��������.");
define("NETCAT_FUNCTION_OBJECTS_LIST_CLASSIFICATOR_ERROR", "������ \"%s\" �� ������");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_UNKNOWN", "���� \"%s\" �� �������");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_CLAUSE", "���� \"%s\" �� ������� � �������");
define("NETCAT_FUNCTION_OBJECTS_LIST_CC_ERROR", "��������� �������� \$cc � ������� nc_objects_list(XX, %s, \"...\")");
define("NETCAT_FUNCTION_LISTCLASSVARS_ERROR_SUPERVISOR", "��������� �������� \$cc � ������� ListClassVars(%s)");
define("NETCAT_FUNCTION_FULL_SQL_ERROR_USER", "������ � ������� ������� ����������� �������.");

# events





// widgets events

define("NETCAT_TOKEN_INVALID", "�������� ���� �������������");

// ��������� � ���������� �����
define("NETCAT_HINT_COMPONENT_FIELD", "���� ����������");
define("NETCAT_HINT_COMPONENT_SIZE", "������");
define("NETCAT_HINT_COMPONENT_TYPE", "���");
define("NETCAT_HINT_COMPONENT_ID", "�����");
define("NETCAT_HINT_COMPONENT_DAY", "�������� �������� ���");
define("NETCAT_HINT_COMPONENT_MONTH", "�������� �������� ������");
define("NETCAT_HINT_COMPONENT_YEAR", "�������� �������� ����");
define("NETCAT_HINT_COMPONENT_HOUR", "�������� �������� ����");
define("NETCAT_HINT_COMPONENT_MINUTE", "�������� �������� ������");
define("NETCAT_HINT_COMPONENT_SECONDS", "�������� �������� �������");
define("NETCAT_HINT_OBJECT_PARAMS", "����������, ���������� �������� �������� �������");
define("NETCAT_HINT_CREATED_DESC", "���������  ������� ���������� ������� � ������� &laquo;����-��-�� ��:��:��&raquo;");
define("NETCAT_HINT_LASTUPDATED_DESC", "��������� ������� ���������� ��������� ������� � ������� &laquo;��������������&raquo;");
define("NETCAT_HINT_MESSAGE_ID", "����� (ID) �������");
define("NETCAT_HINT_USER_ID", "����� (ID) ������������, ����������� ������");
define("NETCAT_HINT_CHECKED", "������� ��� �������� ������ (0/1)");
define("NETCAT_HINT_IP", "IP-����� ������������, ����������� ������");
define("NETCAT_HINT_USER_AGENT", "�������� ���������� \$HTTP_USER_AGENT ��� ������������, ����������� ������");
define("NETCAT_HINT_LAST_USER_ID", "����� (ID) ���������� ������������, ����������� ������");
define("NETCAT_HINT_LAST_USER_IP", "IP-����� ���������� ������������, ����������� ������");
define("NETCAT_HINT_LAST_USER_AGENT", "�������� ���������� \$HTTP_USER_AGENT ��� ���������� ������������, ����������� ������");
define("NETCAT_HINT_ADMIN_BUTTONS", "� ������ ����������������� �������� ���� ��������� ���������� � ������ � ������ �� �������� ��� ������ ������ &laquo;��������&raquo;, &laquo;�������&raquo;, &laquo;��������/���������&raquo; (������ � ���� &laquo;������ � ������&raquo;)");
define("NETCAT_HINT_ADMIN_COMMONS", "� ������ ����������������� �������� ���� ��������� ���������� � ������� � ������ �� ���������� ������� � ������ ������ � ������ � �������� ���� �������� �� ����� �� ������� (������ � ���� &laquo;������ � ������&raquo;)");
define("NETCAT_HINT_FULL_LINK", "������ �� ����� ������� ������ ������ ������");
define("NETCAT_HINT_FULL_DATE_LINK", "������ �� ����� ������� ������ � ��������� ���� � ���� &laquo;.../2002/02/02/message_2.html&raquo; (��������������� � ������ ���� � ������� ������� ���� ���� &laquo;���� � �����&raquo; � �������� &laquo;event&raquo;, ����� �������� ���������� ��������� �������� \$fullLink)");
define("NETCAT_HINT_EDIT_LINK", "������ �� �������������� �������");
define("NETCAT_HINT_DELETE_LINK", "������ �� �������� �������");
define("NETCAT_HINT_DROP_LINK", "������ �� �������� ������� ��� �������������");
define("NETCAT_HINT_CHECKED_LINK", "������ �� ���������/���������� �������");
define("NETCAT_HINT_PREV_LINK", "������ �� ���������� �������� � �������� ������� (���� ������� ��������� � ������ � ��� ������, �� ���������� ������)");
define("NETCAT_HINT_NEXT_LINK", "������ �� ��������� �������� � �������� ������� (���� ������� ��������� � ������ � ��� �����, �� ���������� ������)");
define("NETCAT_HINT_ROW_NUM", "����� ������ �� ������� � ������ �� ������� ��������");
define("NETCAT_HINT_REC_NUM", "������������ ���������� �������, ��������� � ������");
define("NETCAT_HINT_TOT_ROWS", "����� ���������� ������� � ������");
define("NETCAT_HINT_BEG_ROW", "����� ������ (�� �������), � ������� ���������� ������� ������ �� ������ ��������");
define("NETCAT_HINT_END_ROW", "����� ������ (�� �������), ������� ������������� ������� ������ �� ������ ��������");
define("NETCAT_HINT_ADMIN_MODE", "�������, ���� ������������ ��������� � ������ �����������������");
define("NETCAT_HINT_SUB_HOST", "����� �������� ������ ���� &laquo;www.example.com&raquo;");
define("NETCAT_HINT_SUB_LINK", "���� � �������� ������� ���� &laquo;/about/pr/&raquo;");
define("NETCAT_HINT_CC_LINK", "������ ��� �������� ���������� � ������� ���� &laquo;news.html&raquo;");
define("NETCAT_HINT_CATALOGUE_ID", "����� �������� ��������");
define("NETCAT_HINT_SUB_ID", "����� �������� �������");
define("NETCAT_HINT_CC_ID", "����� �������� ���������� � �������");
define("NETCAT_HINT_CURRENT_CATALOGUE", "�������� �������� ������� �������� ��������");
define("NETCAT_HINT_CURRENT_SUB", "�������� �������� ������� �������� �������");
define("NETCAT_HINT_CURRENT_CC", "�������� �������� ������� �������� ���������� � �������");
define("NETCAT_HINT_CURRENT_USER", "�������� �������� ������� �������� ��������������� ������������.");
define("NETCAT_HINT_IS_EVEN", "��������� �������� �� ��������");
define("NETCAT_HINT_OPT", "������� opt() ������� \$string � ������, ���� \$flag � ������");
define("NETCAT_HINT_OPT_CAES", "������� opt_case() ������� \$string1 � ������, ���� \$flag ������, � \$string2, ���� \$flag ����");
define("NETCAT_HINT_LIST_QUERY", "������� ������������� ��� ���������� SQL-������� \$sql_query. ��� ������� ���� SELECT (��� ��� ������ �������, ����������� �������������) ������������ \$output_template ��� ������ ����������� �������. \$output_template �������� �������������� ����������.<br>��������� �������� ������ ��������� ����� ���-������� \$data, ������� �������� ������������� ����� ������� (���� ������� � ������� ������� ���������� �����������). \$divider ������ ��� ���������� ����������� ������.");
define("NETCAT_HINT_NC_LIST_SELECT", "������� ��������� ������������ HTML ������ �� ������� NetCat");
define("NETCAT_HINT_NC_MESSAGE_LINK", "������� ��������� �������� ������������� ���� � ������� (��� ������) �� ������ (ID) ����� ������� � ������ (ID) ����������, �������� �� �����������");
define("NETCAT_HINT_NC_FILE_PATH", "������� ��������� �������� ���� � �����, ���������� � ������������ ����, �� ������ (ID) ����� ������� � ������ (ID) ����������, �������� �� �����������");
define("NETCAT_HINT_BROWSE_MESSAGE", "������� ���������� ���� ��������� �� ��������� ������ ������� � �������");
define("NETCAT_HINT_NC_OBJECTS_LIST", "������� ���������� ���������� � ������� \$cc ������� \$sub � ����������� \$params � ���� ����������, ���������� �� ������� � ������ URL");
define("NETCAT_HINT_RTFM", "��� ��������� ���������� � ������� ����� ���������� � ����������� ������������.");
define("NETCAT_HINT_FUNCTION", "�������.");
define("NETCAT_HINT_ARRAY", "���-�������");
define("NETCAT_HINT_VARS_IN_COMPONENT_SCOPE", "����������, ��������� �� ���� �����");
define("NETCAT_HINT_VARS_IN_LIST_SCOPE", "����������, ��������� � ������ �������� �������");
define("NETCAT_HINT_FIELD_D", "��");
define("NETCAT_HINT_FIELD_M", "��");
define("NETCAT_HINT_FIELD_Y", "����");
define("NETCAT_HINT_FIELD_H", "��");
define("NETCAT_HINT_FIELD_MIN", "��");
define("NETCAT_HINT_FIELD_S", "��");

define("NETCAT_CUSTOM_ERROR_REQUIRED_INT", "���������� ������ ����� �����.");
define("NETCAT_CUSTOM_ERROR_REQUIRED_FLOAT", "���������� ������ �����.");
define("NETCAT_CUSTOM_ERROR_MIN_VALUE", "���������� ����� ��� �����: %s.");
define("NETCAT_CUSTOM_ERROR_MAX_VALUE", "������������ ����� ��� �����: %s.");
define("NETCAT_CUSTOM_PARAMETR_UPDATED", "��������� ������� ���������");
define("NETCAT_CUSTOM_PARAMETR_ADDED", "�������� ������� ��������");
define("NETCAT_CUSTOM_KEY", "����");
define("NETCAT_CUSTOM_VALUE", "��������");
define("NETCAT_CUSTOM_USETTINGS", "���������������� ���������");
define("NETCAT_CUSTOM_NONE_SETTINGS", "��� ���������������� ��������.");
define("NETCAT_CUSTOM_ONCE_MAIN_SETTINGS", "�������� ���������");
define("NETCAT_CUSTOM_ONCE_FIELD_NAME", "�������� ����");
define("NETCAT_CUSTOM_ONCE_FIELD_DESC", "��������");
define("NETCAT_CUSTOM_ONCE_DEFAULT", "�������� �� ��������� (����� �������� �� �������)");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_INFOBLOCK", "��������� �������� ��� �������� ���������");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_SUBDIVISION", "��������� �������� ��� �������� �������");
define("NETCAT_CUSTOM_ONCE_EXTEND", "�������������� ���������");
define("NETCAT_CUSTOM_ONCE_EXTEND_REGEXP", "���������� ��������� ��� ��������");
define("NETCAT_CUSTOM_ONCE_EXTEND_ERROR", "����� � ������ �������������� ����������� ���������");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_L", "����� ���� ��� �����");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_W", "������ ��� �����������");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_H", "������ ��� �����������");
define("NETCAT_CUSTOM_ONCE_EXTEND_VIZRED", "��������� �������������� � ���������� ���������");
define("NETCAT_CUSTOM_ONCE_EXTEND_BR", "������� ������ - &lt;br>");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_H", "������ ���� ��� �����");
define("NETCAT_CUSTOM_ONCE_SAVE", "���������");
define("NETCAT_CUSTOM_ONCE_ADD", "��������");
define("NETCAT_CUSTOM_ONCE_DROP", "�������");
define("NETCAT_CUSTOM_ONCE_DROP_SELECTED", "������� ���������");
define("NETCAT_CUSTOM_ONCE_MANUAL_EDIT", "������������� �������");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_NAME", "�������� ���� ������ ��������� ������ ��������� �����, ����� � ���� �������������");
define("NETCAT_CUSTOM_ONCE_ERROR_CAPTION", "���������� ��������� ���� \"��������\"");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_EXISTS", "����� �������� ��� ����");
define("NETCAT_CUSTOM_ONCE_ERROR_QUERY", "������ � sql-�������");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR", "������������� %s �� ������");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR_EMPTY", "������������� %s ����");
define("NETCAT_CUSTOM_TYPE", "���");
define("NETCAT_CUSTOM_SUBTYPE", "������");
define("NETCAT_CUSTOM_EX_MIN", "����������� ��������");
define("NETCAT_CUSTOM_EX_MAX", "������������ �������");
define("NETCAT_CUSTOM_EX_QUERY", "SQL-������");
define("NETCAT_CUSTOM_EX_CLASSIFICATOR", "������");
define("NETCAT_CUSTOM_EX_ELEMENTS", "��������");
define("NETCAT_CUSTOM_TYPENAME_STRING", "������");
define("NETCAT_CUSTOM_TYPENAME_TEXTAREA", "�����");
define("NETCAT_CUSTOM_TYPENAME_CHECKBOX", "���������� ����������");
define("NETCAT_CUSTOM_TYPENAME_SELECT", "������");
define("NETCAT_CUSTOM_TYPENAME_SELECT_SQL", "������������");
define("NETCAT_CUSTOM_TYPENAME_SELECT_STATIC", "�����������");
define("NETCAT_CUSTOM_TYPENAME_SELECT_CLASSIFICATOR", "�������������");
define("NETCAT_CUSTOM_TYPENAME_DIVIDER", "�����������");
define("NETCAT_CUSTOM_TYPENAME_INT", "����� �����");
define("NETCAT_CUSTOM_TYPENAME_FLOAT", "������� �����");
define("NETCAT_CUSTOM_TYPENAME_DATETIME", "���� � �����");
define("NETCAT_CUSTOM_TYPENAME_REL", "����� � ������ ���������");
define("NETCAT_CUSTOM_TYPENAME_REL_SUB", "����� � ��������");
define("NETCAT_CUSTOM_TYPENAME_REL_CC", "����� � ����������� � �������");
define("NETCAT_CUSTOM_TYPENAME_REL_USER", "����� � �������������");
define("NETCAT_CUSTOM_TYPENAME_REL_CLASS", "����� � �����������");
define("NETCAT_CUSTOM_TYPENAME_FILE", "����");
define("NETCAT_CUSTOM_TYPENAME_FILE_ANY", "������������ ����");
define("NETCAT_CUSTOM_TYPENAME_FILE_IMAGE", "�����������");
define("NETCAT_CUSTOM_TYPENAME_COLOR", "����");
define("NETCAT_CUSTOM_TYPENAME_COLOR_TRANSPARENT", "��� �����");
define("NETCAT_CUSTOM_TYPENAME_CUSTOM", "HTML-����");

#exceptions
define("NETCAT_EXCEPTION_CLASS_DOESNT_EXIST", "��������� %s �� ������");
# errors
define("NETCAT_ERROR_SQL", "������ � SQL-�������.<br/>������:<br/>%s<br/>������:<br/>%s");
define("NETCAT_ERROR_DB_CONNECT", "��������� ������: ���������� �������� ��������� �������. ���������, ��������� �� ������� ��������� ������� � ���� ������.");
define("NETCAT_ERROR_UNABLE_TO_DELETE_FILES", "�� ������� ������� ���� ��� ���������� %s");

#openstat

# admin notice
define("NETCAT_ADMIN_NOTICE_MORE", "���������.");
define("NETCAT_ADMIN_NOTICE_PROLONG", "��������.");
define("NETCAT_ADMIN_NOTICE_LICENSE_ILLEGAL", "������ ����� NetCat �� �������� ������������.");
define("NETCAT_ADMIN_NOTICE_LICENSE_MAYBE_ILLEGAL", "��������, � ��� ������������ �������������� ����� NetCat.");
define("NETCAT_ADMIN_NOTICE_SECURITY_UPDATE_SYSTEM", "����� ������ ���������� ������������ �������.");
define("NETCAT_ADMIN_NOTICE_SUPPORT_EXPIRED", "���� ����������� ��������� ��� �������� %s �����.");
define("NETCAT_ADMIN_NOTICE_CRON", "�� ����� �� ������������ ���������� \"���������� ��������\". <a href='https://netcat.ru/developers/docs/system-tools/task-management/' target='_blank'>��� ���?</a>");
define("NETCAT_ADMIN_NOTICE_RIGHTS", "������� ���������� ����� �� ����������");
define("NETCAT_ADMIN_NOTICE_SAFE_MODE", "������� ����� php safe_mode. <a href='https://netcat.ru/adminhelp/safe-mode/' target='_blank'>��� ���?</a>");
define('NETCAT_ADMIN_DOMDocument_NOT_FOUND', 'PHP ���������� DOMDocument �� �������, ������ ������� ����������');
define('NETCAT_ADMIN_TRASH_OBJECT_HAS_BEEN_REMOVED', '������ ������');
define('NETCAT_ADMIN_TRASH_OBJECTS_REMOVED', '�������� �������');
define('NETCAT_ADMIN_TRASH_OBJECT_IS_REMOVED', '������� �������');
define('NETCAT_ADMIN_TRASH_TRASH_HAS_BEEN_SUCCESSFULLY_CLEARNED', '������� ���� ������� �������');

define('NETCAT_FILE_NOT_FOUND', '���� %s �� ������');
define('NETCAT_DIR_NOT_FOUND', '���������� %s �� �������');

define('NETCAT_TEMPLATE_FILE_NOT_FOUND', '���� ������� �� ������');
define('NETCAT_TEMPLATE_DIR_DELETE_ERROR', '������ ������� ��� ����� %s');
define('NETCAT_CAN_NOT_WRITE_FILE', "�� ���� �������� ����");
define('NETCAT_CAN_NOT_CREATE_FOLDER', '�� ���� ������� ����� ��� �������');


define('NETCAT_ADMIN_AUTH_PERM', '���� �����:');
define('NETCAT_ADMIN_AUTH_CHANGE_PASS', '�������� ������');
define('NETCAT_ADMIN_AUTH_LOGOUT', '�����');

define("CONTROL_BUTTON_CANCEL", "������");

define("NETCAT_MESSAGE_FORM_MAIN", "��������");
define("NETCAT_MESSAGE_FORM_ADDITIONAL", "�������������");
define("NETCAT_EVENT_IMPORTCATALOGUE", "������ �����");
define("NETCAT_EVENT_ADDCATALOGUE", "���������� �����");
define("NETCAT_EVENT_ADDSUBDIVISION", "���������� �������");
define("NETCAT_EVENT_ADDSUBCLASS", "���������� ���������� � ������");
define("NETCAT_EVENT_ADDCLASS", "���������� ����������");
define("NETCAT_EVENT_ADDCLASSTEMPLATE", "���������� ������� ����������");
define("NETCAT_EVENT_ADDMESSAGE", "���������� �������");
define("NETCAT_EVENT_ADDSYSTEMTABLE", "���������� ��������� �������");
define("NETCAT_EVENT_ADDTEMPLATE", "���������� ������");
define("NETCAT_EVENT_ADDUSER", "���������� ������������");
define("NETCAT_EVENT_ADDCOMMENT", "���������� �����������");
define("NETCAT_EVENT_UPDATECATALOGUE", "�������������� �����");
define("NETCAT_EVENT_UPDATESUBDIVISION", "�������������� �������");
define("NETCAT_EVENT_UPDATESUBCLASS", "�������������� ���������� � �������");
define("NETCAT_EVENT_UPDATECLASS", "�������������� ����������");
define("NETCAT_EVENT_UPDATECLASSTEMPLATE", "�������������� ������� ����������");
define("NETCAT_EVENT_UPDATEMESSAGE", "�������������� �������");
define("NETCAT_EVENT_UPDATESYSTEMTABLE", "�������������� ��������� �������");
define("NETCAT_EVENT_UPDATETEMPLATE", "�������������� ������");
define("NETCAT_EVENT_UPDATEUSER", "�������������� ������������");
define("NETCAT_EVENT_UPDATECOMMENT", "�������������� �����������");
define("NETCAT_EVENT_DROPCATALOGUE", "�������� �����");
define("NETCAT_EVENT_DROPSUBDIVISION", "�������� �������");
define("NETCAT_EVENT_DROPSUBCLASS", "�������� ���������� � �������");
define("NETCAT_EVENT_DROPCLASS", "�������� ����������");
define("NETCAT_EVENT_DROPCLASSTEMPLATE", "�������� ������� ����������");
define("NETCAT_EVENT_DROPMESSAGE", "�������� ���������");
define("NETCAT_EVENT_DROPSYSTEMTABLE", "�������� ��������� �������");
define("NETCAT_EVENT_DROPTEMPLATE", "�������� ������");
define("NETCAT_EVENT_DROPUSER", "�������� ������������");
define("NETCAT_EVENT_DROPCOMMENT", "�������� �����������");
define("NETCAT_EVENT_CHECKCOMMENT", "��������� �����������");
define("NETCAT_EVENT_UNCHECKCOMMENT", "���������� �����������");
define("NETCAT_EVENT_CHECKMESSAGE", "��������� �������");
define("NETCAT_EVENT_UNCHECKMESSAGE", "���������� �������");
define("NETCAT_EVENT_CHECKUSER", "��������� ������������");
define("NETCAT_EVENT_UNCHECKUSER", "���������� ������������");
define("NETCAT_EVENT_CHECKSUBDIVISION", "��������� �������");
define("NETCAT_EVENT_UNCHECKSUBDIVISION", "���������� �������");
define("NETCAT_EVENT_CHECKCATALOGUE", "��������� �����");
define("NETCAT_EVENT_UNCHECKCATALOGUE", "���������� �����");
define("NETCAT_EVENT_CHECKSUBCLASS", "��������� ���������� � �������");
define("NETCAT_EVENT_UNCHECKSUBCLASS", "���������� ���������� � �������");
define("NETCAT_EVENT_CHECKMODULE", "��������� ������");
define("NETCAT_EVENT_UNCHECKMODULE", "���������� ������");
define("NETCAT_EVENT_AUTHORIZEUSER", "����������� ������������");
define("NETCAT_EVENT_ADDWIDGETCLASS", "���������� ������-����������");
define("NETCAT_EVENT_EDITWIDGETCLASS", "�������������� ������-����������");
define("NETCAT_EVENT_DROPWIDGETCLASS", "�������� ������-����������");
define("NETCAT_EVENT_ADDWIDGET", "���������� �������");
define("NETCAT_EVENT_EDITWIDGET", "�������������� �������");
define("NETCAT_EVENT_DROPWIDGET", "�������� �������");

define("NETCAT_EVENT_IMPORTCATALOGUEPREP", "���������� � ������� �����");
define("NETCAT_EVENT_ADDCATALOGUEPREP", "���������� � ���������� �����");
define("NETCAT_EVENT_ADDSUBDIVISIONPREP", "���������� � ���������� �������");
define("NETCAT_EVENT_ADDSUBCLASSPREP", "���������� � ���������� ���������� � ������");
define("NETCAT_EVENT_ADDCLASSPREP", "���������� � ���������� ����������");
define("NETCAT_EVENT_ADDCLASSTEMPLATEPREP", "���������� � ���������� ������� ����������");
define("NETCAT_EVENT_ADDMESSAGEPREP", "���������� � ���������� �������");
define("NETCAT_EVENT_ADDSYSTEMTABLEPREP", "���������� � ���������� ��������� �������");
define("NETCAT_EVENT_ADDTEMPLATEPREP", "���������� � ���������� ������");
define("NETCAT_EVENT_ADDUSERPREP", "���������� � ���������� ������������");
define("NETCAT_EVENT_ADDCOMMENTPREP", "���������� � ���������� �����������");
define("NETCAT_EVENT_UPDATECATALOGUEPREP", "���������� � �������������� �����");
define("NETCAT_EVENT_UPDATESUBDIVISIONPREP", "���������� � �������������� �������");
define("NETCAT_EVENT_UPDATESUBCLASSPREP", "���������� � �������������� ���������� � �������");
define("NETCAT_EVENT_UPDATECLASSPREP", "���������� � �������������� ����������");
define("NETCAT_EVENT_UPDATECLASSTEMPLATEPREP", "���������� � �������������� ������� ����������");
define("NETCAT_EVENT_UPDATEMESSAGEPREP", "���������� � �������������� �������");
define("NETCAT_EVENT_UPDATESYSTEMTABLEPREP", "���������� � �������������� ��������� �������");
define("NETCAT_EVENT_UPDATETEMPLATEPREP", "���������� � �������������� ������");
define("NETCAT_EVENT_UPDATEUSERPREP", "���������� � �������������� ������������");
define("NETCAT_EVENT_UPDATECOMMENTPREP", "���������� � �������������� �����������");
define("NETCAT_EVENT_DROPCATALOGUEPREP", "���������� � �������� �����");
define("NETCAT_EVENT_DROPSUBDIVISIONPREP", "���������� � �������� �������");
define("NETCAT_EVENT_DROPSUBCLASSPREP", "���������� � �������� ���������� � �������");
define("NETCAT_EVENT_DROPCLASSPREP", "���������� � �������� ����������");
define("NETCAT_EVENT_DROPCLASSTEMPLATEPREP", "���������� � �������� ������� ����������");
define("NETCAT_EVENT_DROPMESSAGEPREP", "���������� � �������� ���������");
define("NETCAT_EVENT_DROPSYSTEMTABLEPREP", "���������� � �������� ��������� �������");
define("NETCAT_EVENT_DROPTEMPLATEPREP", "���������� � �������� ������");
define("NETCAT_EVENT_DROPUSERPREP", "���������� � �������� ������������");
define("NETCAT_EVENT_DROPCOMMENTPREP", "���������� � �������� �����������");
define("NETCAT_EVENT_CHECKCOMMENTPREP", "���������� � ��������� �����������");
define("NETCAT_EVENT_UNCHECKCOMMENTPREP", "���������� � ���������� �����������");
define("NETCAT_EVENT_CHECKMESSAGEPREP", "���������� � ��������� �������");
define("NETCAT_EVENT_UNCHECKMESSAGEPREP", "���������� � ���������� �������");
define("NETCAT_EVENT_CHECKUSERPREP", "���������� � ��������� ������������");
define("NETCAT_EVENT_UNCHECKUSERPREP", "���������� � ���������� ������������");
define("NETCAT_EVENT_CHECKSUBDIVISIONPREP", "���������� � ��������� �������");
define("NETCAT_EVENT_UNCHECKSUBDIVISIONPREP", "���������� � ���������� �������");
define("NETCAT_EVENT_CHECKCATALOGUEPREP", "���������� � ��������� �����");
define("NETCAT_EVENT_UNCHECKCATALOGUEPREP", "���������� � ���������� �����");
define("NETCAT_EVENT_CHECKSUBCLASSPREP", "���������� � ��������� ���������� � �������");
define("NETCAT_EVENT_UNCHECKSUBCLASSPREP", "���������� � ���������� ���������� � �������");
define("NETCAT_EVENT_CHECKMODULEPREP", "���������� � ��������� ������");
define("NETCAT_EVENT_UNCHECKMODULEPREP", "���������� � ���������� ������");
define("NETCAT_EVENT_AUTHORIZEUSERPREP", "���������� � ����������� ������������");
define("NETCAT_EVENT_ADDWIDGETCLASSPREP", "���������� � ���������� ������-����������");
define("NETCAT_EVENT_EDITWIDGETCLASSPREP", "���������� � �������������� ������-����������");
define("NETCAT_EVENT_DROPWIDGETCLASSPREP", "���������� � �������� ������-����������");
define("NETCAT_EVENT_ADDWIDGETPREP", "���������� � ���������� �������");
define("NETCAT_EVENT_EDITWIDGETPREP", "���������� � �������������� �������");
define("NETCAT_EVENT_DROPWIDGETPREP", "���������� � �������� �������");

define("TITLE_WEB", "������� ������");
define("TITLE_MOBILE", "��������� ������");
define("TITLE_RESPONSIVE", "���������� ������");
define("TITLE_OLD", "������� ������ v4");

define("TOOLS_PATCH_INSTALL_ONLINE_NOTIFY", "����� ���������� ���������� ������������ ������������� ������� ��������� ����� �������. ��������� ������� ����������?");
define("TOOLS_PATCH_INFO_NEW", "������������ ����������");
define("TOOLS_PATCH_INFO_NONEW", "���������� �� ����������.");
define("TOOLS_PATCH_BACKTOLIST", "��������� � ������ ������������� ����������");
define("TOOLS_PATCH_INFO_INSTALL", "���������� ����������");
define("TOOLS_PATCH_INFO_SYSTEM_MESSAGE", "��������� ����� ��������� ���������, <a href='%LINK'>������ ���������</a>.");
define("TOOLS_PATCH_ERROR_SET_FILEPERM_IN_HTTP_ROOT_PATH", "���������� ����� �� ������ ��� ���� ������ � ����� $HTTP_ROOT_PATH.<br />(%FILE ���������� ��� ������)");
define("TOOLS_PATCH_ERROR_SET_DIRPERM_IN_HTTP_ROOT_PATH", "���������� ����� �� ������ ��� ����� $HTTP_ROOT_PATH � ���� �������������.<br />(%DIR ���������� ��� ������)");
define("TOOLS_PATCH_FOR_CP1251", "���� ��� ����������� ������ NetCat, � �� �����, ��� � ��� ������������ utf-������");
define("TOOLS_PATCH_FOR_UTF", "���� ��� utf-������ NetCat, � �� �����, ��� � ��� ����������� ������");
define("TOOLS_PATCH_ERROR_UNCORRECT_PHP_VERSION", "��� ������ ������� ����� ���������� ��������� ������ PHP %NEED, ������� ������ PHP %CURRENT.");
define("TOOLS_PATCH_INSTALEXT", "��������� ������ ������������ ����� ������� ���������");

define("SQL_CONSTRUCT_TITLE", "����������� ��������");
define("SQL_CONSTRUCT_CHOOSE_OP", "�������� ��������");
define("SQL_CONSTRUCT_SELECT_TABLE", "������� ������ �� �������");
define("SQL_CONSTRUCT_SELECT_CC", "������� ������ �� ����������");
define("SQL_CONSTRUCT_ENTER_CODE", "������ ��� ��������� � ����� ��������");
define("SQL_CONSTRUCT_VIEW_SETTINGS", "���������� ��������� �������");
define("SQL_CONSTRUCT_TABLE_NAME", "�������� �������");
define("SQL_CONSTRUCT_FIELDS", "����");
define("SQL_CONSTRUCT_FIELDS_NOTE", "(�����������, ����� �������, ��� ��������)");
define("SQL_CONSTRUCT_CC_ID", "ID ����������");
define("SQL_CONSTRUCT_REGNUM", "����� ��������");
define("SQL_CONSTRUCT_REGCODE", "������������� ���");
define("SQL_CONSTRUCT_CHOOSE_MOD", "�������� ������");
define("SQL_CONSTRUCT_GENERATE", "������������� ������");

define("NETCAT_MAIL_ATTACHMENT_FORM_ATTACHMENTS", "��������:");
define("NETCAT_MAIL_ATTACHMENT_FORM_DELETE", "�������");
define("NETCAT_MAIL_ATTACHMENT_FORM_FILENAME", "�������� �����:");
define("NETCAT_MAIL_ATTACHMENT_FORM_ADD", "�������� ���");

define('NETCAT_DATEPICKER_CALENDAR_DATE_FORMAT', 'dd.mm.yyyy');
define('NETCAT_DATEPICKER_CALENDAR_DAYS', '����������� ����������� ������� ����� ������� ������� ������� �����������');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_SHORT', '��� ��� ��� ��� ��� ��� ��� ���');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_MIN', '�� �� �� �� �� �� �� ��');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS', '������ ������� ���� ������ ��� ���� ���� ������ �������� ������� ������ �������');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS_SHORT', '��� ��� ��� ��� ��� ��� ��� ��� ��� ��� ��� ���');
define('NETCAT_DATEPICKER_CALENDAR_TODAY', '�������');

define('TOOLS_CSV', '�������/������ CSV');
define('TOOLS_CSV_EXPORT', '������� CSV');
define('TOOLS_CSV_IMPORT', '������ CSV');
define('TOOLS_CSV_EXPORT_TYPE', '��� ��������');
define('TOOLS_CSV_EXPORT_TYPE_SUBCLASS', '�� ���������');
define('TOOLS_CSV_EXPORT_TYPE_COMPONENT', '�� ����������');
define('TOOLS_CSV_SELECT_SITE', '�������� ����');
define('TOOLS_CSV_SELECT_SUBDIVISION', '�������� ���������');
define('TOOLS_CSV_SELECT_SUBCLASS', '�������� ��������');
define('TOOLS_CSV_SELECT_COMPONENT', '�������� ���������');
define('TOOLS_CSV_SUBCLASSES_NOT_FOUND', '�� ������� ���������� ����������');
define('TOOLS_CSV_NOT_SELECTED', '�� �������');
define('TOOLS_CSV_CREATE_EXPORT', '�������');
define('TOOLS_CSV_RECORD_ID', '������������� ������ � �����');
define('TOOLS_CSV_PARENT_RECORD_ID', '������������� ������-��������');

define('TOOLS_CSV_SELECT_SETTINGS', '��������� CSV');

define('TOOLS_CSV_OPT_ENCLOSED', '�������� ����� ���������');
define('TOOLS_CSV_OPT_ESCAPED', '������ �������������');
define('TOOLS_CSV_OPT_SEPARATOR', '����������� �����');
define('TOOLS_CSV_OPT_CHARSET', '���������');
define('TOOLS_CSV_OPT_CHARSET_UTF8', '������ (utf-8)');
define('TOOLS_CSV_OPT_CHARSET_CP1251', 'Microsoft Excel (windows-1251)');
define('TOOLS_CSV_OPT_NULL', '�������� NULL ��');
define('TOOLS_CSV_OPT_LISTS', '<nobr>�������� ����� ���� �������</nobr>');
define('TOOLS_CSV_OPT_LISTS_NAME', '�������� ��������');
define('TOOLS_CSV_OPT_LISTS_VALUE', '�������������� �������� (����.Value)');
define('TOOLS_CSV_EXPORT_DONE', '������� ��������. �� ������ ������� ���� �� ������ <a href="%s" target="_blank">%s</a>. ����� ������� ����, ������� <a href="%s" target="_top">�����</a>.');

define('TOOLS_CSV_MAPPING_HEADER', '������������ �����');
define('TOOLS_CSV_IMPORT_FILE', '���� ������� (*.csv)');
define('TOOLS_CSV_IMPORT_RUN', '�������������');
define('TOOLS_CSV_IMPORT_FILE_NOT_FOUND', '���� ��� ������� �� ������');
define('TOOLS_CSV_IMPORT_COLUMN_COUNT_MISMATCH', '������ %d �� ���� ������������� ��-�� ������������� ���������� ������� (� ��������� �����&nbsp;&mdash; %d, � ����������� ������&nbsp;&mdash; %d).');

define('TOOLS_CSV_COMPONENT_FIELD', '���� ����������');
define('TOOLS_CSV_FILE_FIELD', '���� CSV-�����');
define('TOOLS_CSV_FINISHED_HEADER', '������ ����������');
define('TOOLS_CSV_EXPORT_FINISHED_HEADER', '������� ����������');
define('TOOLS_CSV_IMPORT_SUCCESS', '������ ����������, ������������� �������: ');
define('TOOLS_CSV_DELETE_FINISHED_HEADER', '�������� �����');
define('TOOLS_CSV_DELETE_FINISHED', '���� ������. <a href="%s" target="_top">�������, ����� ���������</a>');
define('TOOLS_CSV_IMPORT_HISTORY', '������� �������');
define('TOOLS_CSV_HISTORY_ID', 'ID');
define('TOOLS_CSV_HISTORY_CREATED', '������');
define('TOOLS_CSV_HISTORY_ROLLBACK', '��������');
define('TOOLS_CSV_HISTORY_EMPTY', '������� ������� �����');
define('TOOLS_CSV_HISTORY_CLASS_NAME', '������');
define('TOOLS_CSV_HISTORY_ROWS', '�������');
define('TOOLS_CSV_HISTORY_ROLLBACKED', '��������');
define('TOOLS_CSV_ROLLBACK_FINISHED_HEADER', '������ ������� ���������');
define('TOOLS_CSV_ROLLBACK_SUCCESS', '������ ������� ��������� �������. �������� �������: ');


define('WELCOME_SCREEN_TOOLTIP_SUPPORT',      '� ������ �����������, ����� ���������� � ������������ ��� �������� ����� �� ������������.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR',      '�������� ��������� ����� �������������� ����� ������ ���������� ������.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR_SUBS', '����� �� <a href="#site.add">��������� ����</a>, ����� ����� �������� �������, �� ������� �� �������. ������ ��������� ��������� �����, � ����� ����� ��������� �������� � �������� �� ������������ �� �����.');
define('WELCOME_SCREEN_TOOLTIP_TRASH_WIDGET', '��� ��������� ������ �� ������ ����������� �������. ��������, � �������� ����� ������������ ��������� �������.');
define('WELCOME_SCREEN_MODAL_TEXT', '<h2>����� ���������� � ������� ���������� ������� NetCat!</h2>
    <p>��� ������ �������� �� ������� ����� ������ �������� �� ��������� �������� � <b>������ ���������� ������.</b> ������� �� ��� ����� ������� �� �������� ������ ����� � ������� �����.</p>
    <p>����� �������� ��������� ������������ � ��������������� �������� ����������������� ����������.</p>
    <p>������� ������� �� ������������� ����� ������� � <b>����� � ������.</b></p>');
define('WELCOME_SCREEN_BTN_START', '������ ������');

define('NETCAT_FILTER_FIELD_MESSAGE_ID', 'ID ������');
define('NETCAT_FILTER_FIELD_CREATED', '����� ��������');
define('NETCAT_FILTER_FIELD_LAST_UPDATED', '����� ��������������');

define('NETCAT_FIELD_VALUE_INHERITED_FROM_SUBDIVISION', '�������� ���� ����������� �� ������� �%s�');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_CATALOGUE', '�������� ���� ����������� �� <a href="%s" target="_top">������� �����</a>');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_TEMPLATE', '�������� ���� ����������� �� ������ �%s�');
define('NETCAT_FIELD_FILE_ICON_SELECT', '�������');
define('NETCAT_FIELD_FILE_ICON_ICON', '������');
define('NETCAT_FIELD_FILE_ICON_OR', '���');
define('NETCAT_FIELD_FILE_ICON_FILE', '����');

define('NETCAT_USER_BREAK_ATTRIBUTE_NAMING_CONVENTION', '��������� �� ���� ��������� �������� <a href="https://www.w3.org/TR/html-markup/syntax.html#syntax-attributes" target="_blank">��������� ����������</a> � ���� ���������������: %s.');

define('NETCAT_SECURITY_SETTINGS', '��������� ������ �����');
define('NETCAT_SECURITY_SETTINGS_SAVE', '���������');
define('NETCAT_SECURITY_SETTINGS_SAVED', '��������� ���������');
define('NETCAT_SECURITY_SETTINGS_USE_DEFAULT', '������������ <a href="%s" target="_top">����� ��������� ��� ���� ������</a>');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER', '������ �������� ������');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE', '�������� ��� ����������� ��������� ���������, ������������� ���&nbsp;��������');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_DISABLED', '��������� (�� ��������� �������� ������)');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_LOG_ONLY', '�� ��������� �������� �� ��������');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_ESCAPE_INPUT', '������������ �������� � ������������� ��������');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_REMOVE_INPUT', '�������� �������� � ������������� ��������');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_EXCEPTION', '���������� ���������� �������');

define('NETCAT_SECURITY_FILTER_NO_TOKENIZER', '��� PHP �� ����� �����������, ��� ��� ��������� ���������� <i>tokenizer</i>.');
define('NETCAT_SECURITY_FILTER_EMAIL_ENABLED', '�������� ������ ��� ������������ ������� �� ����������� �����');
define('NETCAT_SECURITY_FILTER_EMAIL_PLACEHOLDER', '����� ����������� �����');
define('NETCAT_SECURITY_FILTER_EMAIL_SUBJECT', '������������ ������� �������� ������');
define('NETCAT_SECURITY_FILTER_EMAIL_PREFIX', '�� �������� %s �������� ������ �������� ������ (%s).');
define('NETCAT_SECURITY_FILTER_EMAIL_INPUT_VALUE', '�������� ��������� ��������� %s');
define('NETCAT_SECURITY_FILTER_EMAIL_CHECKED_STRING', '������, � ������� ������� ���������������� ��������');
define('NETCAT_SECURITY_FILTER_EMAIL_IP', 'IP-�����, � �������� �������� ������');
define('NETCAT_SECURITY_FILTER_EMAIL_URL', '����� ��������');
define('NETCAT_SECURITY_FILTER_EMAIL_REFERER', '����� ����������� ��������');
define('NETCAT_SECURITY_FILTER_EMAIL_GET', 'GET-���������');
define('NETCAT_SECURITY_FILTER_EMAIL_POST', 'POST-���������');
define('NETCAT_SECURITY_FILTER_EMAIL_BACKTRACE', '���� �������');
define('NETCAT_SECURITY_FILTER_EMAIL_SUFFIX', '����������� ��������������� ����������� ���� ��� ����������� ������ ��������, ����� ��������� ������ ������ ����� ����� ������ ����������.');
define('NETCAT_SECURITY_FILTERS_DISABLED', '������� �������� ������ ���������.');

define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA', '������ ����� ����� � ������� ��� ������ CAPTCHA');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_RECOMMEND_DEFAULT', '(����������� ������������ ���������� ��������� �� ���� ������)');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_DISABLED', '���������');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_ALWAYS', '���������� CAPTCHA ������');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_COUNT', '���������� CAPTCHA ����� ������������� ����� ������ ��� ������');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_ATTEMPTS', '����� ������� ��� CAPTCHA');

// _CONDITION_
define('NETCAT_CONDITION_DATETIME_FORMAT', 'd.m.Y H:i');
define('NETCAT_CONDITION_DATE_FORMAT', 'd.m.Y');

// ��������� ��� ����������� ���������� �������� �������
define('NETCAT_COND_OP_EQ', '%s');
define('NETCAT_COND_OP_EQ_IS', '� %s');
define('NETCAT_COND_OP_NE', '�� %s');
define('NETCAT_COND_OP_GT', '����� %s');
define('NETCAT_COND_OP_GE', '�� ����� %s');
define('NETCAT_COND_OP_LT', '����� %s');
define('NETCAT_COND_OP_LE', '�� ����� %s');
define('NETCAT_COND_OP_GT_DATE', '������� %s');
define('NETCAT_COND_OP_GE_DATE', '�� ����� %s');
define('NETCAT_COND_OP_LT_DATE', '����� %s');
define('NETCAT_COND_OP_LE_DATE', '������� %s');
define('NETCAT_COND_OP_CONTAINS', '�������� �%s�');
define('NETCAT_COND_OP_NOTCONTAINS', '�� �������� �%s�');
define('NETCAT_COND_OP_BEGINS', '���������� � �%s�');

define('NETCAT_COND_QUOTED_VALUE', '�%s�');
define('NETCAT_COND_OR', ', ��� '); // spaces are important
define('NETCAT_COND_AND', '; ');
define('NETCAT_COND_OR_SAME', ', ');
define('NETCAT_COND_AND_SAME', ' � ');
define('NETCAT_COND_DUMMY', '(��� �������, ����������� � ������� ��������)');
define('NETCAT_COND_ITEM', '�� �����');
define('NETCAT_COND_ITEM_COMPONENT', '�� ������');
define('NETCAT_COND_ITEM_PARENTSUB', '�� ������ �������');
define('NETCAT_COND_ITEM_PARENTSUB_NE', '�� ������ �� �� �������');
define('NETCAT_COND_ITEM_PARENTSUB_DESCENDANTS', '� ��� �����������');
define('NETCAT_COND_ITEM_PROPERTY', '�� ������, � �������');
define('NETCAT_COND_DATE_FROM', '�');
define('NETCAT_COND_DATE_TO', '��');
define('NETCAT_COND_TIME_INTERVAL', '%s&#x200A;�&#x200A;%s');
define('NETCAT_COND_BOOLEAN_TRUE', '�������');
define('NETCAT_COND_BOOLEAN_FALSE', '������');
define('NETCAT_COND_DAYOFWEEK_ON_LIST', '� �����������/�� �������/� �����/� �������/� �������/� �������/� �����������');
define('NETCAT_COND_DAYOFWEEK_EXCEPT_LIST', '����� ������������/����� ��������/����� �����/����� ��������/����� �������/����� �������/����� �����������');
define('NETCAT_COND', '�������: ');

define('NETCAT_COND_NONEXISTENT_COMPONENT', '[�������������� ���������]');
define('NETCAT_COND_NONEXISTENT_FIELD', '[������ � �������: ���� �� ����������]');
define('NETCAT_COND_NONEXISTENT_VALUE', '[�������������� ��������]');
define('NETCAT_COND_NONEXISTENT_SUB', '[�������������� ������]');
define('NETCAT_COND_NONEXISTENT_ITEM', '[�������������� ������]');

// ������, ������������ � ��������� �������
define('NETCAT_CONDITION_FIELD', '������� ������� �� ������ ������');
define('NETCAT_CONDITION_AND', '�');
define('NETCAT_CONDITION_OR', '���');
define('NETCAT_CONDITION_AND_DESCRIPTION', '��� ������� �����:');
define('NETCAT_CONDITION_OR_DESCRIPTION', '����� �� ������� �����:');
define('NETCAT_CONDITION_REMOVE_GROUP', '������� ������ �������');
define('NETCAT_CONDITION_REMOVE_CONDITION', '������� �������');
define('NETCAT_CONDITION_REMOVE_ALL_CONFIRMATION', '������� ��� �������?');
define('NETCAT_CONDITION_REMOVE_GROUP_CONFIRMATION', '������� ������ �������?');
define('NETCAT_CONDITION_REMOVE_CONDITION_CONFIRMATION', '������� ������� �%s�?');
define('NETCAT_CONDITION_ADD', '��������...');
define('NETCAT_CONDITION_ADD_GROUP', '�������� ������ �������');

define('NETCAT_CONDITION_EQUALS', '�����');
define('NETCAT_CONDITION_NOT_EQUALS', '�� �����');
define('NETCAT_CONDITION_LESS_THAN', '�����');
define('NETCAT_CONDITION_LESS_OR_EQUALS', '�� �����');
define('NETCAT_CONDITION_GREATER_THAN', '�����');
define('NETCAT_CONDITION_GREATER_OR_EQUALS', '�� �����');
define('NETCAT_CONDITION_CONTAINS', '��������');
define('NETCAT_CONDITION_NOT_CONTAINS', '�� ��������');
define('NETCAT_CONDITION_BEGINS_WITH', '���������� �');
define('NETCAT_CONDITION_TRUE', '��');
define('NETCAT_CONDITION_FALSE', '���');
define('NETCAT_CONDITION_INCLUSIVE', '������������');

define('NETCAT_CONDITION_SELECT_CONDITION_TYPE', '�������� ��� �������');
define('NETCAT_CONDITION_SEARCH_NO_RESULTS', '�� �������: ');

define('NETCAT_CONDITION_GROUP_OBJECTS', '��������� �������'); // '�������� �������'

define('NETCAT_CONDITION_TYPE_OBJECT', '������');
define('NETCAT_CONDITION_SELECT_OBJECT', '�������� ������');
define('NETCAT_CONDITION_NONEXISTENT_ITEM', '(�������������� ������)');
define('NETCAT_CONDITION_ITEM_WITHOUT_NAME', '������ ��� ��������');
define('NETCAT_CONDITION_ITEM_SELECTION', '����� �������');
define('NETCAT_CONDITION_DIALOG_CANCEL_BUTTON', '������');
define('NETCAT_CONDITION_DIALOG_SELECT_BUTTON', '�������');
define('NETCAT_CONDITION_SUBDIVISION_HAS_LIST_NO_COMPONENTS_OR_OBJECTS', '� ��������� ������� ����������� ���������� ���������� ��� �������.');
define('NETCAT_CONDITION_TYPE_SUBDIVISION', '������');
define('NETCAT_CONDITION_TYPE_SUBDIVISION_DESCENDANTS', '������ � ��� ����������');
define('NETCAT_CONDITION_SELECT_SUBDIVISION', '�������� ������');
define('NETCAT_CONDITION_TYPE_OBJECT_FIELD', '�������� �������');
define('NETCAT_CONDITION_COMMON_FIELDS', '��� ����������');
define('NETCAT_CONDITION_SELECT_OBJECT_FIELD', '�������� �������� �������');
define('NETCAT_CONDITION_SELECT_VALUE', '...'); // sic

define('NETCAT_CONDITION_VALUE_REQUIRED', '���������� ������� �������� ������� ��� ������� ������� �%s�');

// Infoblock settings dialog; mixin editor
define('NETCAT_INFOBLOCK_SETTINGS_CONTAINER', '��������� ����������');
define('NETCAT_INFOBLOCK_DELETE_CONTAINER', '������� ���������');
define('NETCAT_INFOBLOCK_SETTINGS_TITLE_CONTAINER', '��������� ���������� ������');
define('NETCAT_INFOBLOCK_SETTINGS_TAB_CUSTOM', '���������');
define('NETCAT_INFOBLOCK_SETTINGS_TAB_VISIBILITY', '��������');
define('NETCAT_INFOBLOCK_SETTINGS_TAB_OTHERS', '������');
define('NETCAT_INFOBLOCK_VISIBILITY_SHOW_BLOCK', '���������� ����');
define('NETCAT_INFOBLOCK_VISIBILITY_SHOW_CONTAINER', '���������� ���������');
define('NETCAT_INFOBLOCK_VISIBILITY_ALL_PAGES', '�����');
define('NETCAT_INFOBLOCK_VISIBILITY_THIS_PAGE', '������ �� ���� ��������');
define('NETCAT_INFOBLOCK_VISIBILITY_SOME_PAGES', '������� ��������');
define('NETCAT_INFOBLOCK_VISIBILITY_REMOVE_CONDITION', '�������');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISIONS', '�������, � ������� ������������ ����');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISIONS_EXCLUDED', '�������, � ������� ���� �� ������������');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISIONS_ANY', '����� �������');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISION_NOT_SELECTED', '(������ �� ������)');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISION_INCLUDE_CHILDREN', '������� ����������');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISION_DOESNT_EXIST', '�������������� ������');
define('NETCAT_INFOBLOCK_VISIBILITY_SUBDIVISION_SELECT', '������� ������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTIONS', '��� �������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_INDEX', '������ ��������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_FULL', '�������� �������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_ADD', '����������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_DELETE', '��������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_EDIT', '��������������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_SEARCH', '������� ��������');
define('NETCAT_INFOBLOCK_VISIBILITY_ACTION_SUBSCRIBE', '��������');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENTS', '���������� � �������� ���������� �������, ������� ������ �������������� �� ��������');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENTS_EXCLUDED', '���������� � �������� ���������� �������, ��� ������� ������� ���� �� ���������');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENTS_ANY', '����� ����������');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENT_NOT_SELECTED', '(��������� �� ������)');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENT_DOESNT_EXIST', '�������������� ���������');
define('NETCAT_INFOBLOCK_VISIBILITY_COMPONENT_SELECT', '������� ���������');
define('NETCAT_INFOBLOCK_VISIBILITY_OBJECTS', '�������, �� ��������� ������� ��������� ����');
define('NETCAT_INFOBLOCK_VISIBILITY_OBJECTS_ANY', '����� �������');
define('NETCAT_INFOBLOCK_VISIBILITY_OBJECT_NOT_SELECTED', '(������ �� ������)');
define('NETCAT_MIXIN_TITLE', '����������');
define('NETCAT_MIXIN_TITLE_INDEX', '���������� ������ ��������');
define('NETCAT_MIXIN_TITLE_INDEX_ITEM', '���������� ������� � ������');
define('NETCAT_MIXIN_INDEX', '������ ��������');
define('NETCAT_MIXIN_INDEX_ITEM', '������� � ������');
define('NETCAT_MIXIN_BREAKPOINT_TYPE', '��������� ����� ��������');
define('NETCAT_MIXIN_BREAKPOINT_TYPE_BLOCK', '� ������ �����');
define('NETCAT_MIXIN_BREAKPOINT_TYPE_VIEWPORT', '� ������ ��������');
define('NETCAT_MIXIN_BREAKPOINT_ADD', '�������� �������� ������');
define('NETCAT_MIXIN_BREAKPOINT_ADD_PROMPT', '����� ������� ������ �����');
define('NETCAT_MIXIN_BREAKPOINT_ADD_PROMPT_RANGE', '(������� �������� �� %from �� %to ����.)');
define('NETCAT_MIXIN_BREAKPOINT_CHANGE', '�������� ������� ���������');
define('NETCAT_MIXIN_BREAKPOINT_CHANGE_PROMPT', '�������� ������� ��������� (0 ��� ������ ������ ��� ��������):');
define('NETCAT_MIXIN_FOR_WIDTH_FROM', '��� ������ �� %from ����.');
define('NETCAT_MIXIN_FOR_WIDTH_TO', '��� ������ �� %to ����.');
define('NETCAT_MIXIN_FOR_WIDTH_RANGE', '��� ������ �� %from �� %to ����.');
define('NETCAT_MIXIN_FOR_WIDTH_ANY', '');
define('NETCAT_MIXIN_FOR_VIEWPORT_WIDTH_FROM', '��� ������ �������� �� %from ����.');
define('NETCAT_MIXIN_FOR_VIEWPORT_WIDTH_TO', '��� ������ �������� �� %to ����.');
define('NETCAT_MIXIN_FOR_VIEWPORT_WIDTH_RANGE', '��� ������ �������� �� %from �� %to ����.');
define('NETCAT_MIXIN_FOR_VIEWPORT_WIDTH_ANY', '�� �������� � ����� �������');
define('NETCAT_MIXIN_FOR_BLOCK_WIDTH_FROM', '��� ������ ������� �� %from ����.');
define('NETCAT_MIXIN_FOR_BLOCK_WIDTH_TO', '��� ������ ������� �� %to ����.');
define('NETCAT_MIXIN_FOR_BLOCK_WIDTH_RANGE', '��� ������ ������� %from�%to ����.');
define('NETCAT_MIXIN_FOR_BLOCK_WIDTH_ANY', '��� ������ � ����� �������');
define('NETCAT_MIXIN_PRESET_REMOVE_BUTTON', '�������');
define('NETCAT_MIXIN_NONE', '���');
define('NETCAT_MIXIN_WIDTH', '������');
define('NETCAT_MIXIN_SELECTOR', '�������������� CSS-��������');
define('NETCAT_MIXIN_SELECTOR_ADD', '-- �������� �������� --');
define('NETCAT_MIXIN_SELECTOR_ADD_PROMPT', '�������� CSS-��������:');
define('NETCAT_MIXIN_SETTINGS_REMOVE', '������� ���������');
define('NETCAT_MIXIN_PRESET_SELECT', '������� ��������� ����������');
define('NETCAT_MIXIN_PRESET_DEFAULT', '�� ��������� (�%s�)');
define('NETCAT_MIXIN_PRESET_DEFAULT_NONE', '�� ��������� (���)');
define('NETCAT_MIXIN_PRESET_NONE_EXPLICIT', '�� ������������ ��������� �� ���������');
define('NETCAT_MIXIN_PRESET_CREATE', '-- ������� ����� ����� �������� --');
define('NETCAT_MIXIN_PRESET_EDIT_BUTTON', '�������������');
define('NETCAT_MIXIN_PRESET_TITLE_EDIT', '�������������� ������ ��������');
define('NETCAT_MIXIN_PRESET_TITLE_ADD', '���������� ������ ��������');
define('NETCAT_MIXIN_PRESET_NAME', '�������� ������ ��������');
define('NETCAT_MIXIN_PRESET_AVAILABILITY', '��������� �������� ���');
define('NETCAT_MIXIN_PRESET_FOR_ANY_COMPONENT', '�������� ���� �����������');
define('NETCAT_MIXIN_PRESET_FOR_COMPONENT_TEMPLATE_PREFIX', '������� �%s�');
define('NETCAT_MIXIN_PRESET_FOR_COMPONENT', '���������� �%s�');
define('NETCAT_MIXIN_PRESET_USE_AS_DEFAULT_FOR', '��������� �� ��������� ���');
define('NETCAT_MIXIN_PRESET_TITLE_DELETE', '�������� ������ ��������');
define('NETCAT_MIXIN_PRESET_DELETE_WARNING', '����� �������� �%s� ����� �����.');
define('NETCAT_MIXIN_PRESET_USED_FOR_COMPONENT_TEMPLATES', '������ ����� �������� ������������ �� ��������� ���');
define('NETCAT_MIXIN_PRESET_COMPONENT_TEMPLATES_COUNT_FORMS', '������� ����������/�������� �����������/�������� �����������');
define('NETCAT_MIXIN_PRESET_USED_FOR_BLOCKS', '������ ����� �������� ������������ �');
define('NETCAT_MIXIN_PRESET_BLOCKS_COUNT_FORMS', '�����/������/������');

define('NETCAT_MODAL_DIALOG_IMAGE_HEADER', '���������� ��������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_EDIT_CAPTION', '�������������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_EDIT_COLORPICKER_INPUT_PLACEHOLDER', '�������� RGB');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_ICONS_CAPTION', '������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_ICONS_ICONS_NOT_FOUND', '�� �������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_ICONS_ICONS_SEARCH_INPUT_PLACEHOLDER', '�����...');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_ICONS_LIBRARY_CHOOSE', '��� ������');
define('NETCAT_MODAL_DIALOG_IMAGE_BUTTON_SAVE', '���������');
define('NETCAT_MODAL_DIALOG_IMAGE_BUTTON_CLOSE', '������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_UPLOAD_CAPTION', '��������');
define('NETCAT_MODAL_DIALOG_IMAGE_TAB_WEB_CAPTION', '�� ����');