<?php

/* $Id$ */
define('NETCAT_MODULE_LINKS_RULES_PAGE', '<p>На Вашем сайте не должно быть материалов нарушающих законодательство,
эротики, порнографии, рекламы интим-услуг.
<p>Обмен ссылок не производим с сайтами с тИЦ&nbsp;&lt;&nbsp;10, а также находящимися
на бесплатных хостингах.
<p>Сайт должен быть русскоязычным.
<p>Сайт должен соответствовать выбранной вами категории, на странице не
должно быть более 20 ссылок.
<p>На сайте должны располагаться ссылки на указанные выше сайты. Формат
ссылок указан на странице <a href=../codes/>Коды наших ссылок</a>.

<p>Прежде чем заполнить форму, разместите на своем сайте ссылки на
наш сайт или на те сайты, с которыми хотите произвести прямой обмен.');

define('NETCAT_MODULE_LINKS_DESCRIPTION', 'Данный модуль предназначен для организации обмена ссылками.');

define('NETCAT_MODULE_LINKS_REPORT_STAT', '
         Ссылок в базе: <b>%s</b>. Из них:<br>
         <li> выключено: <b>%s</b>
         <li> в режиме редиректа: <b>%s</b>
       ');

define('NETCAT_MODULE_LINKS_REPORT_LAST_CHECK', '
     Последняя проверка ссылок проводилась <b>$data[Last_Process]</b>. В результате ее работы было:
     <li> выключено: <b>$data[Last_Unchecked]</b> ссылок
     <li> включено: <b>$data[Last_Checked]</b> ссылок
     <li> удалено: <b>$data[Last_Deleted]</b> ссылок');

define('NETCAT_MODULE_LINKS_START_CHECKUP', 'Запустить проверку');
define('NETCAT_MODULE_LINKS_NO_LINK', "(ссылка не указана)");
define('NETCAT_MODULE_LINKS_NO_DOMAIN', 'В настройках сайта не указан домен, невозможна проверка ссылок на сайте %s\n');
define('NETCAT_MODULE_LINKS_MAIL_SUBJ_PROCESSING', "[NetCat] обработка ссылок");
define('NETCAT_MODULE_LINKS_ACHTUNG', 'ВНИМАНИЕ!');
define('NETCAT_MODULE_LINKS_REPORT_MAKE_AND_SET', 'Генерация и отправка отчета на email');
define('NETCAT_MODULE_LINKS_REPORT_DISABLED', 'Выключены следующие проданные ссылки');
define('NETCAT_MODULE_LINKS_CHECKUP_DONE', 'Проверка завершена.');

define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TEMPLATE', '


Результаты проверки ссылок на Вашем проекте
С момента последней проверки (%s) были добавлены ссылки:

- включенные ссылки:
%s
- выключенные ссылки:
%s
В процессе проверки были:

- выключены ссылки:
%s
- включены ссылки:
%s
- удалены ссылки:
%s

Лог проверки:
%s

С уважением,
Модуль управления ссылками');

define('NETCAT_MODULE_LINKS_ERROR_LINKS_TO_OTHER_SITES', "В HTML-коде ссылки не должны встречаться ссылки на другие сайты.");
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_SAME_SITE', 'Обратная ссылка должна быть расположена на том же сайте, что вы указали как ваш сайт.');
define('NETCAT_MODULE_LINKS_ERROR_MUST_BE_ON_OTHER_SITE', "Обратная ссылка должна располагаться не на вашем сайте, а на другом.");
define('NETCAT_MODULE_LINKS_ERROR_DUPLICATE_BACK_LINK', 'Обратная ссылка с этого сайта уже есть в нашей базе.');
define('NETCAT_MODULE_LINKS_ERROR_NOT_2ND_LEVEL_DOMAIN', "Обратная ссылка расположена не на домене второго уровня (например, site.ru)");
define('NETCAT_MODULE_LINKS_ERROR_DOMAIN_IN_STOP_LIST', 'Обратная ссылка расположена на домене, который включен в стоп-лист этого сайта. Для решения этого вопроса свяжитесь с <a href=mailto:%s>администратором</a>.');
define('NETCAT_MODULE_LINKS_ADDED', 'Ваша ссылка добавлена!');
define('NETCAT_MODULE_LINKS_MODE', 'Режим работы модуля');
define('NETCAT_MODULE_LINKS_BACK_LINK_REQUIRED', 'обязательно наличие обратной ссылки');
define('NETCAT_MODULE_LINKS_REDIRECT_IF_NO_LINK', 'редирект при отсутствии обратной ссылки');
define('NETCAT_MODULE_LINKS_WHEN_NO_BACK_LINK', 'При отсутствии обратной ссылки');
define('NETCAT_MODULE_LINKS_DISABLE_LINK', 'выключать ссылку');
define('NETCAT_MODULE_LINKS_DELETE_LINK', 'удалять ссылку');
define('NETCAT_MODULE_LINKS_DELETE_DISABLED_LINKS_IN', 'Удалять выключенные ссылки через');
define('NETCAT_MODULE_LINKS_IN_DAYS', 'дней');
define('NETCAT_MODULE_LINKS_WHEN_BACK_LINK', 'При наличии обратной ссылки');
define('NETCAT_MODULE_LINKS_DIRECT_LINK', 'ссылка на сайт партнера прямая');
define('NETCAT_MODULE_LINKS_DONT_REMOVE_TAGS', 'не убирать теги в HTML-коде ссылки текста');
define('NETCAT_MODULE_LINKS_MOVE_TO_TOP', 'поднятие ссылки наверх списка');
define('NETCAT_MODULE_LINKS_CAN_MAKE_DIRECT_LINK_EVERY', 'Отправлять партнеру письмо о возможости разместить прямую ссылку через каждые');
define('NETCAT_MODULE_LINKS_EVERY_DAYS_NUL', 'дней (ноль - не отправлять)');
define('NETCAT_MODULE_LINKS_LINK_CHECK', 'Проверка ссылок');
define('NETCAT_MODULE_LINKS_CHECK_ON_PARTNER_SITE', 'Проверять наличие на сайте партнера');
define('NETCAT_MODULE_LINKS_CHECK_FULL_TEXT', 'полный текст ссылки');
define('NETCAT_MODULE_LINKS_CHECK_LINK_ONLY', 'только наличие ссылки');
define('NETCAT_MODULE_LINKS_BACK_LINK_IS_ON', 'Обратная ссылка должна быть расположена');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_LINKED_SITE', 'на том сайте, куда ссылаемся мы');
define('NETCAT_MODULE_LINKS_BACK_LINK_ON_OTHER_SITE', 'на другом сайте');
define('NETCAT_MODULE_LINKS_BACK_LINK_ANYWHERE', 'не важно');
define('NETCAT_MODULE_LINKS_DISALLOW_DUPLICATE_BACK_LINKS', 'не разрешать добавление, если обратная ссылка с этого сайта уже присутствует в базе');
define('NETCAT_MODULE_LINKS_DISALLOW_LINKS_TO_OTHER_SITE', 'не разрешать добавление, если в тексте ссылки присутствуют ссылки не на указанный сайт');
define('NETCAT_MODULE_LINKS_DISALLOW_NOT_2ND_LEVEL_DOMAINS', 'не разрешать добавление, если обратная ссылка расположена не на домене второго уровня');
define('NETCAT_MODULE_LINKS_EMAIL_SEND', 'Отправка писем');
define('NETCAT_MODULE_LINKS_EMAIL_ROBOT_ADDRESS', 'Email отправителя автоматически генерируемых ссылок');
define('NETCAT_MODULE_LINKS_EMAIL_ADMIN_ADDRESS', 'Email администратора для отправления автоматически генерируемых писем');
define('NETCAT_MODULE_LINKS_SAVE_CHANGES', 'Сохранить изменения');
define('NETCAT_MODULE_LINKS_CHANGES_SAVED', 'Изменения внесены');
define('NETCAT_MODULE_LINKS_CANCEL', 'Отменить');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_LINK_ADD', 'отправлять администратору письмо при добавлении новой ссылки (тема и шаблон письма)');
define('NETCAT_MODULE_LINKS_LINK_REQUIRED_MODE', 'Режим обязательного наличия ссылки');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ABSENT', 'отправлять партнеру письмо при добавлении ссылки, если обратной ссылки нет');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_PRESENT', 'отправлять партнеру письмо при добавлении, если обратная ссылка есть');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DISABLE', 'отправлять партнеру письмо при автоматическом выключении ссылки');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_ENABLE', 'отправлять партнеру письмо при автоматическом включении ссылки');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_LINK_DELETE', 'отправлять партнеру письмо при автоматическом удалении ссылки');
define('NETCAT_MODULE_LINKS_REDIRECT_MODE', 'Режим редиректа при отсутствии ссылки');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_ON', 'отправлять партнеру письмо при автоматическом включении режима редиректа');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_REDIRECT_OFF', 'отправлять партнеру письмо при автоматическом выключении режима редиректа');
define('NETCAT_MODULE_LINKS_BUY_AND_SELL', 'Покупка/продажа ссылок');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_ADMIN_ON_PURCHASED_LINK_ABSENT', 'отправлять письмо администратору об исчезновении купленной ссылки');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATE_TO_PARTNER_ON_PURCHASED_LINK_DISABLE', 'отправлять письмо партнеру о выключении проданной ему ссылки');
define('NETCAT_MODULE_LINKS_REPORT_EMAIL_TO_ADMIN', 'отправлять администратору отчет о проверке (шаблон не настраивается)');

define('NETCAT_MODULE_LINKS_STATS', 'Статистика');
define('NETCAT_MODULE_LINKS_SETTINGS', 'Настройки модуля');
define('NETCAT_MODULE_LINKS_EMAIL_TEMPLATES', 'Шаблоны писем');

define('NETCAT_MODULE_LINKS_TURNED_OFF', 'выключено');
define('NETCAT_MODULE_LINKS_DO_SEARCH', 'искать');
define('NETCAT_MODULE_LINKS_CODE_VARIANTS', "Вы можете установить на своем сайте один из предложенных ниже вариантов:");
define('NETCAT_MODULE_LINKS_CODE_NO_VARIANTS', "HTML-код для размещения на вашем сайте:");
define('NETCAT_MODULE_LINKS_LINK_PREVIEW', 'Внешний вид ссылки:');
define('NETCAT_MODULE_LINKS_LINK_HTML', 'HTML-код ссылки:');
define('NETCAT_MODULE_LINKS_DISABLE_DATE', 'дата отключения');
define('NETCAT_MODULE_LINKS_PURCHASED', 'выкуп заканчивается');

define('NETCAT_MODULE_LINKS_SUB', 'Обмен ссылками');
define('NETCAT_MODULE_LINKS_CODES_SUB', 'Коды ссылок');
define('NETCAT_MODULE_LINKS_SOLD_SUB', 'Продажа ссылок');
define('NETCAT_MODULE_LINKS_PURCHASED_SUB', 'Покупка ссылок');
define('NETCAT_MODULE_LINKS_STOPLIST_SUB', 'Стоп-лист');
define('NETCAT_MODULE_LINKS_RULES', 'Условия добавления сайта');

define('NETCAT_MODULE_LINKS_CODES2', 'Коды наших ссылок');
define('NETCAT_MODULE_LINKS_ADD_SITE', 'добавить сайт');
define('NETCAT_MODULE_LINKS_GO_UP', 'на уровень выше');