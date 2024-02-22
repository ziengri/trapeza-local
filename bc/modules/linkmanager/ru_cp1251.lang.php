<?php

/* $Id$ */
define('NETCAT_MODULE_LINKS_RULES_PAGE', '<p>�� ����� ����� �� ������ ���� ���������� ���������� ����������������,
�������, �����������, ������� �����-�����.
<p>����� ������ �� ���������� � ������� � ���&nbsp;&lt;&nbsp;10, � ����� ������������
�� ���������� ���������.
<p>���� ������ ���� �������������.
<p>���� ������ ��������������� ��������� ���� ���������, �� �������� ��
������ ���� ����� 20 ������.
<p>�� ����� ������ ������������� ������ �� ��������� ���� �����. ������
������ ������ �� �������� <a href=../codes/>���� ����� ������</a>.

<p>������ ��� ��������� �����, ���������� �� ����� ����� ������ ��
��� ���� ��� �� �� �����, � �������� ������ ���������� ������ �����.');

define('NETCAT_MODULE_LINKS_DESCRIPTION', '������ ������ ������������ ��� ����������� ������ ��������.');

define('NETCAT_MODULE_LINKS_REPORT_STAT', '
         ������ � ����: <b>%s</b>. �� ���:<br>
         <li> ���������: <b>%s</b>
         <li> � ������ ���������: <b>%s</b>
       ');

define('NETCAT_MODULE_LINKS_REPORT_LAST_CHECK', '
     ��������� �������� ������ ����������� <b>$data[Last_Process]</b>. � ���������� �� ������ ����:
     <li> ���������: <b>$data[Last_Unchecked]</b> ������
     <li> ��������: <b>$data[Last_Checked]</b> ������
     <li> �������: <b>$data[Last_Deleted]</b> ������');

define('NETCAT_MODULE_LINKS_START_CHECKUP', '��������� ��������');
define('NETCAT_MODULE_LINKS_NO_LINK', "(������ �� �������)");
define('NETCAT_MODULE_LINKS_NO_DOMAIN', '� ���������� ����� �� ������ �����, ���������� �������� ������ �� ����� %s\n');
define('NETCAT_MODULE_LINKS_MAIL_SUBJ_PROCESSING', "[NetCat] ��������� ������");
define('NETCAT_MODULE_LINKS_ACHTUNG', '��������!');
define('NETCAT_MODULE_LINKS_REPORT_MAKE_AND_SET', '��������� � �������� ������ �� email');
define('NETCAT_MODULE_LINKS_REPORT_DISABLED', '��������� ��������� ��������� ������');
define('NETCAT_MODULE_LINKS_CHECKUP_DONE', '�������� ���������.');

define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TEMPLATE', '


���������� �������� ������ �� ����� �������
� ������� ��������� �������� (%s) ���� ��������� ������:

- ���������� ������:
%s
- ����������� ������:
%s
� �������� �������� ����:

- ��������� ������:
%s
- �������� ������:
%s
- ������� ������:
%s

��� ��������:
%s

� ���������,
������ ���������� ��������');

define('NETCAT_MODULE_LINKS_ERROR_LINKS_TO_OTHER_SITES', "� HTML-���� ������ �� ������ ����������� ������ �� ������ �����.");
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_SAME_SITE', '�������� ������ ������ ���� ����������� �� ��� �� �����, ��� �� ������� ��� ��� ����.');
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_OTHER_SITE', "�������� ������ ������ ������������� �� �� ����� �����, � �� ������.");
define('NETCAT_MODULE_LINKS_ERROR_DUPLICATE_BACK_LINK', '�������� ������ � ����� ����� ��� ���� � ����� ����.');
define('NETCAT_MODULE_LINKS_ERROR_NOT_2ND_LEVEL_DOMAIN', "�������� ������ ����������� �� �� ������ ������� ������ (��������, site.ru)");
define('NETCAT_MODULE_LINKS_ERROR_DOMAIN_IN_STOP_LIST', '�������� ������ ����������� �� ������, ������� ������� � ����-���� ����� �����. ��� ������� ����� ������� ��������� � <a href=mailto:%s>���������������</a>.');
define('NETCAT_MODULE_LINKS_ADDED', '���� ������ ���������!');
define('NETCAT_MODULE_LINKS_MODE', '����� ������ ������');
define('NETCAT_MODULE_LINKS_BACK_LINK_REQUIRED', '����������� ������� �������� ������');
define('NETCAT_MODULE_LINKS_REDIRECT_IF_NO_LINK', '�������� ��� ���������� �������� ������');
define('NETCAT_MODULE_LINKS_WHEN_NO_BACK_LINK', '��� ���������� �������� ������');
define('NETCAT_MODULE_LINKS_DISABLE_LINK', '��������� ������');
define('NETCAT_MODULE_LINKS_DELETE_LINK', '������� ������');
define('NETCAT_MODULE_LINKS_DELETE_DISABLED_LINKS_IN', '������� ����������� ������ �����');
define('NETCAT_MODULE_LINKS_IN_DAYS', '����');
define('NETCAT_MODULE_LINKS_WHEN_BACK_LINK', '��� ������� �������� ������');
define('NETCAT_MODULE_LINKS_DIRECT_LINK', '������ �� ���� �������� ������');
define('NETCAT_MODULE_LINKS_DONT_REMOVE_TAGS', '�� ������� ���� � HTML-���� ������ ������');
define('NETCAT_MODULE_LINKS_MOVE_TO_TOP', '�������� ������ ������ ������');
define('NETCAT_MODULE_LINKS_CAN_MAKE_DIRECT_LINK_EVERY', '���������� �������� ������ � ���������� ���������� ������ ������ ����� ������');
define('NETCAT_MODULE_LINKS_EVERY_DAYS_NUL', '���� (���� - �� ����������)');
define('NETCAT_MODULE_LINKS_LINK_CHECK', '�������� ������');
define('NETCAT_MODULE_LINKS_CHECK_ON_PARTNER_SITE', '��������� ������� �� ����� ��������');
define('NETCAT_MODULE_LINKS_CHECK_FULL_TEXT', '������ ����� ������');
define('NETCAT_MODULE_LINKS_CHECK_LINK_ONLY', '������ ������� ������');
define('NETCAT_MODULE_LINKS_BACK_LINK_IS_ON', '�������� ������ ������ ���� �����������');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_LINKED_SITE', '�� ��� �����, ���� ��������� ��');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_OTHER_SITE', '�� ������ �����');
define('NETCAT_MODULE_LINKS_BACK_LINK_ANYWHERE', '�� �����');
define('NETCAT_MODULE_LINKS_DISALLOW_DUPLICATE_BACK_LINKS', '�� ��������� ����������, ���� �������� ������ � ����� ����� ��� ������������ � ����');
define('NETCAT_MODULE_LINKS_DISALLOW_LINKS_TO_OTHER_SITE', '�� ��������� ����������, ���� � ������ ������ ������������ ������ �� �� ��������� ����');
define('NETCAT_MODULE_LINKS_DISALLOW_NOT_2ND_LEVEL_DOMAINS', '�� ��������� ����������, ���� �������� ������ ����������� �� �� ������ ������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_SEND', '�������� �����');
define('NETCAT_MODULE_LINKS_EMAIL_ROBOT_ADDRESS', 'Email ����������� ������������� ������������ ������');
define('NETCAT_MODULE_LINKS_EMAIL_ADMIN_ADDRESS', 'Email �������������� ��� ����������� ������������� ������������ �����');
define('NETCAT_MODULE_LINKS_SAVE_CHANGES', '��������� ���������');
define('NETCAT_MODULE_LINKS_CHANGES_SAVED', '��������� �������');
define('NETCAT_MODULE_LINKS_CANCEL', '��������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_LINK_ADD', '���������� �������������� ������ ��� ���������� ����� ������ (���� � ������ ������)');
define('NETCAT_MODULE_LINKS_LINK_REQUIRED_MODE', '����� ������������� ������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ABSENT', '���������� �������� ������ ��� ���������� ������, ���� �������� ������ ���');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_PRESENT', '���������� �������� ������ ��� ����������, ���� �������� ������ ����');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DISABLE', '���������� �������� ������ ��� �������������� ���������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ENABLE', '���������� �������� ������ ��� �������������� ��������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DELETE', '���������� �������� ������ ��� �������������� �������� ������');
define('NETCAT_MODULE_LINKS_REDIRECT_MODE', '����� ��������� ��� ���������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_ON', '���������� �������� ������ ��� �������������� ��������� ������ ���������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_OFF', '���������� �������� ������ ��� �������������� ���������� ������ ���������');
define('NETCAT_MODULE_LINKS_BUY_AND_SELL', '�������/������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_PURCHASED_LINK_ABSENT', '���������� ������ �������������� �� ������������ ��������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_PURCHASED_LINK_DISABLE', '���������� ������ �������� � ���������� ��������� ��� ������');
define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TO_ADMIN', '���������� �������������� ����� � �������� (������ �� �������������)');

define('NETCAT_MODULE_LINKS_STATS', '����������');
define('NETCAT_MODULE_LINKS_SETTINGS', '��������� ������');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATES', '������� �����');

define('NETCAT_MODULE_LINKS_TURNED_OFF', '���������');
define('NETCAT_MODULE_LINKS_DO_SEARCH', '������');
define('NETCAT_MODULE_LINKS_CODE_VARIANTS', "�� ������ ���������� �� ����� ����� ���� �� ������������ ���� ���������:");
define('NETCAT_MODULE_LINKS_CODE_NO_VARIANTS', "HTML-��� ��� ���������� �� ����� �����:");
define('NETCAT_MODULE_LINKS_LINK_PREVIEW', '������� ��� ������:');
define('NETCAT_MODULE_LINKS_LINK_HTML', 'HTML-��� ������:');
define('NETCAT_MODULE_LINKS_DISABLE_DATE', '���� ����������');
define('NETCAT_MODULE_LINKS_PURCHASED', '����� �������������');

define('NETCAT_MODULE_LINKS_SUB', '����� ��������');
define('NETCAT_MODULE_LINKS_CODES_SUB', '���� ������');
define('NETCAT_MODULE_LINKS_SOLD_SUB', '������� ������');
define('NETCAT_MODULE_LINKS_PURCHASED_SUB', '������� ������');
define('NETCAT_MODULE_LINKS_STOPLIST_SUB', '����-����');
define('NETCAT_MODULE_LINKS_RULES', '������� ���������� �����');

define('NETCAT_MODULE_LINKS_CODES2', '���� ����� ������');
define('NETCAT_MODULE_LINKS_ADD_SITE', '�������� ����');
define('NETCAT_MODULE_LINKS_GO_UP', '�� ������� ����');
?>