<?php

$NETCAT_FOLDER = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR;
require_once $NETCAT_FOLDER . 'vars.inc.php';
require_once $ROOT_FOLDER . 'connect_io.php';

/** @var nc_core $nc_core */
/** @var nc_db $db */

// -------------------- Обработка запросов к файлам ----------------------------

// Отдача файлов из защищенной ФС
if (preg_match('#^' . preg_quote($nc_core->HTTP_FILES_PATH, '#') . '([0-9uct]+)/([0-9]+/)?h_([0-9A-Z]{32})$#i', $nc_core->url->get_parsed_url('path'), $matches)) {

    if ($matches[1] !== 'u' && $matches[1] !== 'c' && $matches[1] !== 't') {
        $matches[1] = (int)$matches[1];
    }

    $file_path = $matches[1] . '/';

    if (strlen($matches[2])) {
        $file_path .= $matches[2];
    }

    $full_file_path = $nc_core->FILES_FOLDER . $file_path . $matches[3];

    if (file_exists($full_file_path)) {

        while (ob_get_level() && @ob_end_clean());

        // sic (remove header)
        if ($use_gzip_compression) {
            header('Content-Encoding: ');
        }

        // get filetime
        $file_time = filemtime($full_file_path);

        // check If-Modified-Since and REDIRECT 304, if needed
        $nc_core->page->update_last_modified_if_timestamp_is_newer($file_time);
        $nc_core->page->send_and_check_cache_validator_headers();

        $file_data = $db->get_row(
            "SELECT f.`ID`, f.`Real_Name`, f.`File_Type`, f.`Content_Disposition`, fl.`Format`
             FROM `Filetable` as f, `Field` as `fl`
             WHERE `Virt_Name` = '{$matches[3]}'
             AND `File_Path` = '/{$file_path}'
             AND fl.`Field_ID` = f.`Field_ID`
             LIMIT 1",
            ARRAY_N
        );

        if (!empty($file_data)) {
            list ($file_id, $real_name, $file_type, $attachment, $format) = $file_data;
            if (strpos($format, 'download') !== false) {
                $db->query("UPDATE `Filetable` SET `Download` = `Download` + 1 WHERE `ID` = '{$file_id}'");
            }
            nc_send_file($full_file_path, $real_name, $file_type, $attachment);
        }
    }
}

// Отдача сгенерированных обработанных изображений
$path = $nc_core->url->get_parsed_url('path');
if (strpos($path, nc_image_base::generated_images_http_path()) === 0) {
    $generator_parameters = nc_image_generator::get_parameters_from_url($path, $_GET);
    $image_generator = nc_image_generator::from_parameters($generator_parameters);
    if ($image_generator->validate_hash($generator_parameters['result_hash'])) {
        $image_absolute_path = $image_generator->generate();
        nc_send_file($image_absolute_path);
    }
}

// --------------------- Обработка запросов к robots.txt -----------------------

if ($nc_core->url->get_parsed_url('path') === '/robots.txt') {
    $robots = $nc_core->catalogue->get_current('Robots');
    $robots = str_ireplace(
        array('%SCHEME', '%HOST'),
        array(nc_get_scheme(), $_SERVER['HTTP_HOST'] ?: $nc_core->catalogue->get_current('Domain')),
        $robots
    );
    nc_set_http_response_code(200);
    header('Last-Modified: ' . $nc_core->catalogue->get_current('LastUpdated'));
    header('Content-type: text/plain');
    header('Content-Length: ' . strlen($robots));
    ob_clean();
    echo $robots;
    exit;
}

// --------------------- Обработка запросов к sitemap.xml ----------------------
if ($nc_core->url->get_parsed_url('path') === '/sitemap.xml' && nc_module_check_by_keyword('search')) {
    require_once nc_module_folder('search') . 'sitemap.php';
    exit;
}

// ------------------------------- Переадресации --------------------------------
$client_source_url = $nc_core->url->source_url();

if (!$nc_core->NC_REDIRECT_DISABLED) {
    AttemptToRedirect($client_source_url);
}

// ---------------------------- Глобальные переменные --------------------------

$developer_mode = false;
$admin_mode = false;

$current_catalogue = $nc_core->catalogue->get_by_host_name($nc_core->HTTP_HOST, true); // данные сайта
$catalogue = $nc_core->catalogue->get_current('Catalogue_ID'); // идентификатор сайта

if (!$catalogue) {
    exit;
}

$sub = 0;              // идентификатор раздела
$classID = 0;          // идентификатор компонента
$user_table_mode = false; // флаг работы с системной таблицей, а не с обычным компонентом
$system_table = 0;     // идентификатор системной таблицы (таблица User — 3)
$system_table_fields = array(); // информация о полях системных таблиц, кое-где до сих пор используется как global
$cc = 0;               // идентификатор инфоблока
$_db_cc = null;        // для зеркальных инфоблоков — идентификатор инфоблока в запрошенном разделе
$cc_keyword = '';      // ключевое слово инфоблока
$cc_array = array();   // массив с идентификаторами включенных инфоблоков в разделе
$cc_in_sub = array();  // массив с данными инфоблоков в разделе
$message = 0;          // идентификатор объекта компонента
$redirect_to_url = ''; // URL, на который следует осуществить переадресацию
//$action = null;      // определяет, какой скрипт будет подключаться. Может передаваться в GET, POST

if (nc_module_check_by_keyword('routing')) {
    //нужно загрузить модуль роутинга
    $nc_core->modules->load_env('', false, true, false, 'routing');
}

$e404_sub = $nc_core->catalogue->get_current('E404_Sub_ID');
$title_sub = $nc_core->catalogue->get_current('Title_Sub_ID');
$rules_sub = $nc_core->catalogue->get_current('Rules_Sub_ID');
$page_not_found = false;


// ------- Определение цели запроса (раздела/инфоблока/объекта/скрипта) --------

if ($nc_core->url->get_parsed_url('path') === '/') {
    // Путь к главной странице: без участия модуля маршрутизации
    $sub = $title_sub;
    $nc_core->subdivision->set_current_by_id($sub);
    $cc_in_sub = $nc_core->sub_class->get_by_subdivision_id($sub);
    foreach ((array)$cc_in_sub as $row) {
        if ($row['Checked'] || $row['sysTbl'] == 3) {
            $cc_array[] = $row['Sub_Class_ID'];
        }
    }
    if (count($cc_array)) {
        $cc = $_db_cc = $cc_array[0];
    }
} else {
    $routing_result = nc_resolve_url($nc_core->url, $_SERVER['REQUEST_METHOD']);

    // принятие решения о необходимости переадресации или добавления канонического адреса
    // (только при включённом модуле маршрутизации)
    if (is_array($routing_result) && nc_module_check_by_keyword('routing')) {
        $routing_duplicate_route_action = nc_routing::get_setting('DuplicateRouteAction', $catalogue);
        if ($routing_duplicate_route_action != nc_routing::DUPLICATE_ROUTE_NO_ACTION) {
            // попробуем получить путь, соответствующий полученным параметрам
            $routing_canonical_request = $routing_result;

            $routing_result_variables = nc_array_value($routing_result, 'variables', array());
            if ($routing_result_variables) {
                // подходящий маршрут должен содержать эти переменные в «дополнительных переменных» (query_variables)
                $routing_canonical_request['route_variables'] = $routing_result_variables;
            }

            // добавим GET-переменные, если они есть
            if ($nc_core->input->fetch_get()) {
                $routing_canonical_request['variables'] = array_merge((array)$nc_core->input->fetch_get(), $routing_result_variables);
            }
            unset($routing_canonical_request['variables']['REQUEST_URI']);

            if ($routing_canonical_request['resource_type'] === 'object') {
                // Для объектов в качестве основного пути могут использоваться пути без даты
                $routing_canonical_request['date_is_optional'] = true;

                // Для объектов проверить наличие и значение поля типа event/event_date
                if (!$routing_canonical_request['date']) {
                    try {
                        $routing_object_component_id = (int)$nc_core->sub_class->get_by_id($routing_canonical_request['infoblock_id'], 'Class_ID');
                        $routing_object_date_field = $nc_core->get_component($routing_object_component_id)->get_date_field();
                        if ($routing_object_date_field) {
                            $routing_canonical_request['date'] = $db->get_var(
                                "SELECT DATE_FORMAT(`$routing_object_date_field`, '%Y-%m-%d')
                                 FROM `Message$routing_object_component_id`
                                 WHERE `Message_ID` = " . (int)$routing_canonical_request['object_id']
                            );
                        }
                    }
                    catch (Exception $e) {}
                }
            }

            $routing_canonical_path = (string)nc_routing::get_resource_path($routing_canonical_request['resource_type'], $routing_canonical_request);

            if (parse_url($routing_canonical_path, PHP_URL_PATH) !== $nc_core->SUB_FOLDER . $nc_core->url->get_parsed_url('path')) {
                // найден альтернативный путь
                if ($routing_duplicate_route_action == nc_routing::DUPLICATE_ROUTE_REDIRECT) {
                    $routing_result['redirect_to_url'] = $routing_canonical_path;
                } else {
                    $nc_core->page->set_canonical_link($routing_canonical_path);
                }
            }

            unset($routing_canonical_path, $routing_canonical_request, $routing_duplicate_route_action, $routing_object_component_id, $routing_object_date_field);
        }
    }
    
    if ($routing_result === false) {
        // Страница не найдена
        $page_not_found = true;
    } elseif (isset($routing_result['redirect_to_url']) && $routing_result['redirect_to_url']) {
        // Нужна переадресация на «правильный» адрес
        $redirect_to_url = $routing_result['redirect_to_url'];
    } elseif (is_array($routing_result)) {
        // Найден подходящий ресурс. Устанавливаем переменные, необходимые
        // для дальнейшей работы.

        // извлечение переменных в глобальную область видимости, добавление в $_GET и input
        if ($routing_result['variables']) {
            $routing_result['variables'] = $nc_core->input->clear_system_vars($routing_result['variables']);

            if (isset($nc_core->security) && $nc_core->SECURITY_XSS_CLEAN) {
                $routing_result['variables'] = array_map_recursive(array($nc_core->security, 'xss_clean'), $routing_result['variables']);
            }

            foreach ($routing_result['variables'] as $_key => $_value) {
                $nc_core->input->set('_GET', $_key, $_value);
                $_GET[$_key] = $_value;
                $$_key = $_value;
            }

            $nc_core->url->set_parsed_url_item('query',
                $nc_core->url->get_parsed_url('query') .
                '&' .
                http_build_query($routing_result['variables'], null, '&')
            );
        }

        // альтернативный адрес скрипта
        if ($routing_result['resource_type'] === 'script') {
            $script_file_name = $nc_core->DOCUMENT_ROOT . '/' . $nc_core->SUB_FOLDER . $routing_result['script_path'];

            if (file_exists($script_file_name)) {
                // значения некоторых переменных могли быть изменены в процессе выполнения скрипта
                define('NC_SCRIPT_FILE_NAME', $script_file_name);
                extract($nc_core->input->prepare_extract(), EXTR_OVERWRITE);
                require NC_SCRIPT_FILE_NAME;
                exit;
            }

            $page_not_found = true;
        } else {
            // инфоблок или объект: установка глобальных переменных
            // тип страницы
            $nc_core->set_page_type($routing_result['format']);

            // информация о разделе
            $sub = $routing_result['folder_id'];
            $nc_core->subdivision->set_current_by_id($sub);

            // информация об инфоблоках в разделе
            $cc_in_sub = $nc_core->sub_class->get_by_subdivision_id($sub);
            foreach ((array)$cc_in_sub as $row) {
                if ($row['Checked'] || $row['sysTbl'] == 3) {
                    $cc_array[] = $row['Sub_Class_ID'];
                }
            }

            // информация об инфоблоке / шаблоне отображения
            $cc = $routing_result['infoblock_id'];
            if ($cc && $routing_result['format'] !== 'html') {
                $cc = $nc_core->sub_class->get_by_id($cc, 'Sub_Class_ID', 0, false, $routing_result['format']);
            }

            $nc_core->sub_class->set_current_by_id($cc);

            $_db_cc = $cc;

            if ($cc) {
                if ($routing_result['resource_type'] !== 'folder') {
                    $cc_keyword = $nc_core->sub_class->get_by_id($cc, 'EnglishName');
                }

                if (!isset($isNaked)) {
                    $isNaked = $nc_core->sub_class->get_by_id($cc, 'isNaked');
                }

                // информация о компоненте
                $classID = $nc_core->sub_class->get_current('Class_ID');
                $system_table = $nc_core->sub_class->get_current('sysTbl');
                $system_table_mode = (bool)$system_table;
            }

            // дата
            $date = $routing_result['date'];

            // действие (определяет подключаемый скрипт)
            if (!isset($action)) { // $action может быть задан в виде переменной
                $action = $routing_result['action'];
            }
        }

        // объект компонента:
        if ($routing_result['resource_type'] === 'object') {
            // информация об объекте
            $message = $routing_result['object_id'];

            switch ($action) {
                case 'full': break;
                case 'subscribe': break;
                case 'edit':    $action = 'message'; break;
                case 'checked': $action = 'message'; $posting = 1; $checked = 1; break;
                case 'delete':  $action = 'message'; $posting = 0; $delete = 1; break;
                case 'drop':    $action = 'message'; $posting = 1; $delete = 1; break;
            }
        }

    }

    $nc_core->page->set_routing_result($routing_result);
    unset($routing_result);
}

$use_multi_sub_class = $sub ? $nc_core->subdivision->get_by_id($sub, 'UseMultiSubClass') : false;

// ---------- Действия в зависимости от результата разбора адреса  -------------

// *** Редирект ***
if ($redirect_to_url && $e404_sub != $sub && in_array($_SERVER['REQUEST_METHOD'], array('GET', 'HEAD'), true)) {
    if ($nc_core->REDIRECT_STATUS === 'on') {
        if ($nc_core->AUTHORIZATION_TYPE === 'session') {
            $redirect_to_url .= strpos($redirect_to_url, '?') ? '&' : '?';
            $redirect_to_url .= session_name() . '=' . session_id();
        }
        header('Location: ' . $redirect_to_url, true, 301);
        exit;
    }
}

// старый способ работы с настройками модулей
$MODULE_VARS = $nc_core->modules->load_env('', false, true);

// *** Подключение файла для обработки выбранного действия с инфоблоком или объектом ***

// Front user mode
if (!in_array($action, array('index', 'full', 'add', 'search', 'subscribe', 'message'), true)) {
    $action = 'index';
}

if ($cc && in_array($sub, nc_preg_split("/\s*,\s*/", $nc_core->get_settings('modify_sub', 'auth')), true)) {
    $action = 'message';
    $user_table_mode = true;
}

if (!$sub || ($sub == $e404_sub && $title_sub != $sub)) {
    $page_not_found = true;
}
if ($page_not_found) {
    $sub = $e404_sub;
    $nc_core->subdivision->set_current_by_id($sub);
    $use_multi_sub_class = $nc_core->subdivision->get_by_id($sub, 'UseMultiSubClass');
    // get 404 cc's
    $cc_in_sub = $nc_core->sub_class->get_by_subdivision_id($sub);
    $cc_array = array();
    if (!empty($cc_in_sub)) {
        foreach ($cc_in_sub as $row) {
            $cc_array[] = $row['Sub_Class_ID'];
        }

        $classID = $cc_in_sub[0]['Class_ID'];
        // reset variables
        $nc_core->sub_class->set_current_by_id($cc);
        if (!$use_multi_sub_class) {
            $cc = $cc_array[0];
            $cc_keyword = $cc_in_sub[0]['EnglishName'];
        }
        // isNaked
        if (!$isNaked) {
            $isNaked = $cc_in_sub[0]['isNaked'];
        }
    }
    // определение сс
    // 404 header
    nc_set_http_response_code(404);
    unset($date);
    $action = 'index';
} else {
    // 200 OK
    nc_set_http_response_code(200);
    header('Content-Type: ' . $nc_core->get_content_type());
}

// Предусмотрены следующие «штатные» случаи обработки запросов методом HEAD:
// 1) Модуль поиска методом HEAD проверяет ссылки — для ускорения заканчиваем работу здесь,
//    не генерируя страницу.
// 2) Сеошников очень сильно волнует результат проверки сервисами типа last-modified.com,
//    которые почему-то могут запросить страницу методом HEAD. Чтобы посчитать для них значение
//    заголовка Last-Modified, нам нужно будет собрать всю страницу.
if (
    $_SERVER['REQUEST_METHOD'] === 'HEAD' &&
    class_exists('nc_search', false) &&
    $_SERVER['HTTP_USER_AGENT'] === nc_search::get_setting('CrawlerUserAgent')
) {
    exit;
}

$passed_thru_404 = true;

if (!(isset($this) && $this instanceof nc_partial_loader)) {
    require $nc_core->ROOT_FOLDER . $action . '.php';
}