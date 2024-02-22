<?php

define('NETCAT_MODULE_CAPTCHA_DESCRIPTION', '������ ���� ���������');

if (nc_core::get_object()->get_settings('Provider', 'captcha') !== 'nc_captcha_provider_image') {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', '����������, �����������, ��� �� �� �����');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', '�����������, ��� �� �� �����');
}
else {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', '����������� ������� �������, ������������ �� ��������');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', '����������� ������� �������');
}

define('NETCAT_MODERATION_CAPTCHA', '������� �������, ������������ �� ��������');
define('NETCAT_MODERATION_CAPTCHA_SMALL', '�������<br/>�� ��������');
define("NETCAT_MODULE_CAPTCHA_AUDIO_LISTEN", "����������");
define('NETCAT_MODULE_CAPTCHA_REFRESH', "��������");

define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVE', '���������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVED', '��������� ���������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_USE_DEFAULT', '������������ <a href="%s" target="_top">����� ��������� ��� ���� ������</a>');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER', '��� CAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_IMAGE', '�������� � �����');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_RECAPTCHA', 'reCAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_CHARACTERS', '�������, ������������ �� ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LENGTH', '���������� �������� �� ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_EXPIRES', '���� �������� ���� �� �������� � ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_WIDTH', '������ ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_HEIGHT', '������ ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LINES', '����� ����� �� ��������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_ENABLED', '�������� �����������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_VOICE', '����� ��� �����������');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_NO_GD', '���������� GD �� �������� � PHP, ��������� �������� ��� ������ ���� ����������.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SITE_KEY', '����');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SECRET_KEY', '��������� ����');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_ADD_KEYS', '������� ��������� � ��������� �����. �������� ����� ����� �� �������� <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a>.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_INVALID_SECRET', '<a href="https://www.google.com/recaptcha/admin" target="_blank" title="������ ���������� reCAPTCHA">���������</a>, ��������� �� ������ ��������� ����.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CURL', '���������� cURL �� �������� � PHP, ������������� reCAPTCHA ����������.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CONNECTION', '�� ������� ������������ � ������� Google ��� �������� ����������.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_LEGACY_MODE', '����� ������������� � ���������, ���������������� ��� ������������� �� ���������� ������� Netcat');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_TEXT', '�����, ��������� � ����� (�� ����� ����� � ������)');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_BLOCKS', '�����, ��������� � ����� (�� ������ CSS-��������� ����� � ������)');