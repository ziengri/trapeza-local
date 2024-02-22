<?php

define("NETCAT_MODULE_REQUESTS", "Заявки");
define("NETCAT_MODULE_REQUESTS_DESCRIPTION", "Данный модуль предназначен для просмотра заявок и создания форм.");

define("NETCAT_MODULE_REQUESTS_FORM_TYPE", "Группа формы");
define("NETCAT_MODULE_REQUESTS_FORM_SETTINGS_FIELDS_HEADER", "Поля формы");
define("NETCAT_MODULE_REQUESTS_FORM_POPUP_BUTTON_SETTINGS_HEADER", "Кнопка, открывающая форму");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_BUTTON_SETTINGS_HEADER", "Кнопка отправки формы");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_DEFAULT_TEXT", "Сделать заявку");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ALTERNATE_TEXT", "Отправить");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_POPUP_FORM", "Открыть форму");
define("NETCAT_MODULE_REQUESTS_FORM_HAS_NO_FIELDS", "Для этой формы не выбрано ни одного поля");
define("NETCAT_MODULE_REQUESTS_FORM_HEADER_CAPTION", "Заголовок формы");
define("NETCAT_MODULE_REQUESTS_FORM_TEXT_AFTER_HEADER_CAPTION", "Подзаголовок формы");
define("NETCAT_MODULE_REQUESTS_FORM_NOTIFICATION_EMAIL_CAPTION", "Отправить данные формы на почту (для всех форм на странице)");
define("NETCAT_MODULE_REQUESTS_FORM_NOTIFICATION_EMAIL_PLACEHOLDER", "Например: mail1@pochta.ru, mail2@pochta.ru...");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_TEXT", "Текст");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_COLOR", "Цвет (если не задан на уровне блока)");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_PRICE", "Показывать цену (если есть)");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_EVENT_CATEGORIES", "Категории событий Google Analytics при отправке формы (можно несколько через запятую),<br>для Яндекс.Метрики цели событий — «<i>категория</i>_submit» для каждой из указанных категорий");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_EVENT_LABELS", "Ярлыки событий для Google Analytics при отправке формы (можно несколько через запятую),<br>для Яндекс.Метрики — дополнительный параметр визита «event_label»");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_EVENT_CATEGORIES", "Категории событий Google Analytics при открытии формы (можно несколько через запятую),<br>для Яндекс.Метрики цели событий — «<i>категория</i>_click» для каждой из указанных категорий");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_EVENT_LABELS", "Ярлыки событий для Google Analytics при открытии формы (можно несколько через запятую),<br>для Яндекс.Метрики — дополнительный параметр визита «event_label»");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_SCOPE_SUBDIVISION", "для всей страницы");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_SCOPE_INFOBLOCK", "дополнительно для этого блока");
define("NETCAT_MODULE_REQUESTS_FORM_SUBDIVISION_SYNC_HINT", "Набор полей будет установлен для всех форм на этой странице.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_OPEN_POPUP", "При нажатии на эту кнопку будет показана форма поверх страницы.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_CREATE_ORDER", "Эта кнопка создаёт заказ в интернет-магазине.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_CREATE_REQUEST", "Эта кнопка создаёт заявку.");
define("NETCAT_MODULE_REQUESTS_FORM_LABEL_FIELD_NAME", "Название поля");
define("NETCAT_MODULE_REQUESTS_FORM_LABEL_FIELD_LABEL", "Подпись");
define("NETCAT_MODULE_REQUESTS_FORM_LABEL_FIELD_PLACEHOLDER", "Подсказка внутри поля");
define("NETCAT_MODULE_REQUESTS_FORM_LABEL_FIELD_VISIBILITY", "Вкл.");
define("NETCAT_MODULE_REQUESTS_FORM_LABEL_FIELD_REQUIRED", "*");

define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_STATS_DISABLED",
    "Модуль «Статистика посещений» отключён, события не будут отправляться в Яндекс.Метрику и Google Analytics.
    <a href='%s' target='_blank'>Изменить настройки модулей</a>"
);
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_ANALYTICS_DISABLED",
    "Интеграция с Яндекс.Метрикой и Google Analytics отключена в настройках модуля «Статистика посещений», информация о событиях не будет отправляться.
    <a href='%s' target='_blank'>Изменить настройки</a>"
);
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_ANALYTICS_NO_COUNTERS",
    "В настройках модуля «Статистика посещений» не указаны коды счётчиков Яндекс.Метрики и Google Analytics, информация о событиях не будет отправляться.
    <a href='/netcat/admin/#module.stats.analytics' target='_blank'>Указать параметры счётчиков</a>"
);

define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER", "Фильтр заявок");

define("NETCAT_MODULE_REQUESTS_REQUEST_NEW", "новый");
define("NETCAT_MODULE_REQUESTS_REQUEST_ANY", "любой");

define("NETCAT_MODULE_REQUESTS_REQUEST_SEARCH", "Номер, имя, телефон, e-mail или источник");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER", "Дата");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER_FROM", "с");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER_TO", "по");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_SUBMIT", "Искать");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_RESET", "Очистить форму");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_RESET_CONFIRM", "Вы уверены, что хотите очистить форму?");
define("NETCAT_MODULE_REQUESTS_REQUEST_DELETE_SELECTED", "Удалить отмеченные");
define("NETCAT_MODULE_REQUESTS_REQUEST_DELETE_SELECTED_CONFIRM", "Удалить отмеченные заявки?");

define("NETCAT_MODULE_REQUESTS_REQUEST_STATUS", "Статус заявки");
define('NETCAT_MODULE_REQUESTS_CONFIRM_STATUS_CHANGE', 'Подтвердите смену заявки');
define('NETCAT_MODULE_REQUESTS_CONFIRM_STATUS_CHANGE_TO', 'Изменить статус заявки на «%s»?');

define("NETCAT_MODULE_REQUESTS_REQUEST_NUMBER", "Заявка №");
define("NETCAT_MODULE_REQUESTS_REQUEST_EDIT", "Редактирование заявки");

define("NETCAT_MODULE_REQUESTS_ITEM_DISCOUNT", "Скидка (промо-страница)");
define("NETCAT_MODULE_REQUESTS_ITEM_DISCOUNT_DESCRIPTION", "Размер скидки взят из настроек промо-страницы");

define("NETCAT_MODULE_REQUESTS_DEFAULT_NOTIFICATION_EMAIL", "Адреса электронной почты по умолчанию для уведомлений о новых заявках (если не заданы в настройках формы)");
define("NETCAT_MODULE_REQUESTS_NOTIFICATION_EMAIL_SUBJECT", "Новая заявка на сайте %s (%s)");
define("NETCAT_MODULE_REQUESTS_FORM_TYPE", "Тип формы");
define("NETCAT_MODULE_REQUESTS_REQUEST_ADMIN_LINK", "Заявки в панели управления");
define("NETCAT_MODULE_REQUESTS_SETTINGS_HEADER", "Настройки");
define("NETCAT_MODULE_REQUESTS_SAVE", "Сохранить");
define("NETCAT_MODULE_REQUESTS_SETTINGS_SAVED", "Настройки сохранены");