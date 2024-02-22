<?php

/**
 * kz_browse_sub
 * Вывод разделов меню
 *
 * @param  int $browse_parent_sub ид родительского раздела
 * @param  array $browse_template шаблон вывода раздела. Храниться в template/template/2/Setting.html
 * @param  int $ignore_check игнорировать выключенные
 * @param  string $where_cond дополнительные параметры к SQL запросу
 * @param  int $level до кокого уровня вложености выводить разделы
 * @return string
 */
function kz_browse_sub(int $browse_parent_sub, array $browse_template, int $ignore_check = 0, string $where_cond = "", int $level = 0)
{
    global $REQUEST_URI;
    global $admin_mode, $admin_url_prefix;
    global $current_sub;
    global $parent_sub_tree, $sub_level_count, $system_table_fields;
    global $db, $nc_core, $HTTP_FILES_PATH, $SUB_FOLDER, $cityid, $setting, $current_catalogue;

    if (!$current_sub["Subdivision_ID"])
        return "";

    /** @var string тип сортировки sql */
    $sort_by = nc_array_value($browse_template[0], 'sortby', '`Priority`');
    /** @var array параметры родительского раздела*/
    $parent = ['url' => '/', 'lvl' => 1, 'name' => ''];
    /** @var string тип дисплея пользователя longpage_vertical|shortpage */
    $display_type = $nc_core->get_display_type();

    // * Если Ид родителя меньше 2 значит это корень
    if ($browse_parent_sub < 2)
        $browse_parent_sub = 0;

    if ($browse_parent_sub > 0) {
        $parent = $db->get_row(
            "SELECT 
                Hidden_URL AS url, 
                (LENGTH(Hidden_URL) - LENGTH(REPLACE(Hidden_URL, '/', ''))) AS lvl,
                Subdivision_Name AS name
            FROM 
                Subdivision 
            WHERE 
                Subdivision_ID = '{$browse_parent_sub}'",
            'ARRAY_A'
        );
    }
    /** @var int максимальный уровень вывода раздела начиная от родителя */
    $maxLevelSub = $parent['lvl'] + $level + 1;

    $SQL = "SELECT 
                *, 
                (LENGTH(`Hidden_URL`) - LENGTH(REPLACE(`Hidden_URL`, '/', ''))) AS lvl
            FROM 
                `Subdivision`
            WHERE 
                `Hidden_URL` LIKE '{$parent['url']}%'
                AND (LENGTH(`Hidden_URL`) - LENGTH(REPLACE(`Hidden_URL`, '/', ''))) <= {$maxLevelSub}
                AND `Subdivision_ID` != {$browse_parent_sub}
                AND `Catalogue_ID` = {$current_sub["Catalogue_ID"]}";
    if ($setting['targeting'])
        $SQL .= " AND (`citytarget` LIKE '%,{$cityid},%' OR `citytarget` = ',,' OR `citytarget` IS NULL OR `citytarget` = '')";

    if (!$ignore_check)
        $SQL .= ' AND `Checked` = 1';
    if ($where_cond)
        $SQL .= " AND {$where_cond}";

    switch ($display_type) {
        case 'longpage_vertical':
            $SQL .= " AND `DisplayType` IN ('inherit', 'longpage_vertical')";
            break;
        case 'shortpage':
            $SQL .= " AND `DisplayType` IN ('inherit', 'shortpage')";
            break;
    }

    $SQL = getSubLangQuery($SQL);
    $SQL .= ' ORDER BY lvl,' . $db->escape($sort_by);

    $data = $db->get_results($SQL, 'ARRAY_A') ?: [];

    if (empty($data))
        return null;
    // * Получаем все значения полей — с наследованием, доп. полями для файлов/списков и т.п.
    try {
        $data = $nc_core->subdivision->process_raw_data($data);
    } catch (\Exception $e) {
        $fileDebug = '/var/www/krza/data/logs/subdivision_not_found.log';
        $exists = false;
        $errorText = $_SERVER['HTTP_HOST'] . ' ' . $e->getMessage() . "\r\n";
        // Проверяем ссылку.
        foreach (getLinesDebug($fileDebug) as $line) {
            $exists = $exists || $line === $errorText;
        }
        if (!$exists) {
            file_put_contents($fileDebug, $errorText, FILE_APPEND);
        }
    }
    $current_page_path = urldecode(strtok($REQUEST_URI, '?'));
    $page_sub_id = $nc_core->subdivision->get_current('Subdivision_ID');

    /** @var array  параметры вывода раздела */
    $paramsBrowseSub = [
        'current_sub_path' => ($page_sub_id ? nc_folder_path($page_sub_id) : substr($current_page_path, 0, strrpos($current_page_path, '/') + 1)),
        'current_page_path' => $current_page_path,
        'routing_module_enabled' => nc_module_check_by_keyword('routing'),
        'all_data' => [],
        'browse_template' => $browse_template,
        'parent_sub_name' => $parent['name']
    ];

    foreach ($data as $index => $value) {
        $paramsBrowseSub['all_data'][$value['lvl'] - ($parent['lvl'] + 1)][$value['Parent_Sub_ID']][] = $value;
        unset($data[$index]);
    }

    $result = renderVuewSub($paramsBrowseSub['all_data'][0][$browse_parent_sub], $paramsBrowseSub, 0, $browse_parent_sub);

    return $result;
}


function renderVuewSub($data, $paramsBrowseSub, $level, $browse_parent_sub)
{


    global $REQUEST_URI;
    global $admin_mode, $admin_url_prefix;
    global $current_sub;
    global $parent_sub_tree, $sub_level_count, $system_table_fields;
    global $db, $nc_core, $HTTP_FILES_PATH, $SUB_FOLDER;

    if (!count($data))
        return '';

    if (is_array($paramsBrowseSub['browse_template'][$level]) && !empty($paramsBrowseSub['browse_template'][$level])) {
        $browse_template = $paramsBrowseSub['browse_template'][$level];
    } elseif (is_array($paramsBrowseSub['browse_template']) && !empty($paramsBrowseSub['browse_template'])) {
        $browse_template = $paramsBrowseSub['browse_template'];
    } else {
        return '';
    }
    $current_page_path = $paramsBrowseSub['current_page_path'];
    $current_sub_path = $paramsBrowseSub['current_sub_path'];
    $routing_module_enabled = $paramsBrowseSub['routing_module_enabled'];


    $result = str_replace(
        [
            '%PARENT_SUB',
            '%PARENT_NAME'
        ],
        [
            $browse_parent_sub,
            $paramsBrowseSub['parent_sub_name']
        ],
        $browse_template['prefix']
    );

    // Проход по всем подразделам
    $array_result = [];
    $i = 0;
    foreach ($data as $row) {



        $is_active_sub = 0;
        $nav_name = nc_quote_convert($row['Subdivision_Name']);

        switch (true) {
            case $admin_mode:
                $nav_url = $admin_url_prefix . '?' . http_build_query([
                    'catalogue' => $current_sub['Catalogue_ID'],
                    'sub' => $row['Subdivision_ID']
                ], null, '&');
                break;
            case $ext_url = $row['ExternalURL']:
                $nav_url = (strpos($ext_url, ':') !== false || $ext_url[0] === '/') ? $ext_url : $SUB_FOLDER . $row['Hidden_URL'] . $ext_url;
                break;
            case $routing_module_enabled:
                $nav_url = (string) nc_routing::get_folder_path($row['Subdivision_ID']);
                break;
            default:
                $nav_url = $SUB_FOLDER . $row['Hidden_URL'];
                break;
        }

        for ($j = 0; $j < $sub_level_count; $j++) {
            if ($parent_sub_tree[$j]['Subdivision_ID'] == $row['Subdivision_ID']) {
                $is_active_sub = 1;
                break;
            }
        }

        if ($nav_url === $REQUEST_URI || $nav_url === $current_page_path || ($SUB_FOLDER . $row['ExternalURL']) === $current_page_path) {
            $current_template = $browse_template['active_link'] ?: $browse_template['active'];
        } elseif ($is_active_sub || ($SUB_FOLDER . $row['ExternalURL']) === $current_sub_path) {
            $current_template = $browse_template['active'];
        } else {
            $current_template = $browse_template['unactive'];
        }

        $current_template = str_replace(
            [
                '%NAME',
                '%URL',
                '%PARENT_SUB',
                '%KEYWORD',
                '%SUB',
                '%COUNTER',
                '%PARENT_NAME'
            ],
            [
                getLangWord("lang_sub_{$row['EnglishName']}", $nav_name),
                $nav_url,
                $browse_parent_sub,
                $row['EnglishName'],
                $row['Subdivision_ID'],
                $i,
                $paramsBrowseSub['parent_sub_name']
            ],
            $current_template
        );

        $data[$i]['icon'] = $data[$i]['icon'] ?: '/images/nophoto.png';

        $current_template = nc_replace_macro_variables($current_template, $data[$i]);

        $paramsBrowseSub['parent_sub_name'] = $nav_name;
        // Если не делать проверки, будет холостой вызов nc_browse_sub
        if (strpos($current_template, '%NEXT_LEVEL') !== false && !empty($paramsBrowseSub['all_data'][$level + 1][$row['Subdivision_ID']])) {
            $current_template = str_replace('%DROPING_CLASS', $browse_template['droping_class'], $current_template);
            $current_template = str_replace('%NEXT_LEVEL', renderVuewSub($paramsBrowseSub['all_data'][$level + 1][$row['Subdivision_ID']], $paramsBrowseSub, $level + 1, $row['Subdivision_ID']), $current_template);
        } else {
            $current_template = str_replace('%DROPING_CLASS', '', $current_template);
            $current_template = str_replace('%NEXT_LEVEL', '', $current_template);
        }
        $array_result[] = $current_template;
        $i++;
    }

    $result .= implode($browse_template['divider'], $array_result);
    $result .= $browse_template['suffix'];

    return $result;
}

function getLinesDebug($file)
{
    $f = fopen($file, 'r');
    try {
        while ($line = fgets($f)) {
            yield $line;
        }
    } finally {
        fclose($f);
    }
}