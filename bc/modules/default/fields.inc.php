<?php

/**
 * @param array $data
 */
function bc_export_form($data, $patern)
{
    $input = '';
    foreach ($patern as $key => $paternRow) {
        $value = $data[$key];
        $val = '';
        switch ($paternRow['type']) {
            case 'input':
                $val = kz_input($key, $value, $paternRow['name'], $paternRow['params']);
                break;
            case 'checkbox':
                $val = bc_checkbox($key, 1, $paternRow['name'], $value);
                break;
            case 'select':
                $option = getOptionsFromArray($paternRow['data'], $value);
                $val = bc_select($key, $option, $paternRow['name'], "class='ns'");
                break;
            case 'hidden':
                $input .= "<input type='hidden' name='{$key}' value='$value'>";
                break;
        }

        if ($val) {
            $input .= "<div class='colline colline-{$paternRow['colline']} type-{$paternRow['type']} name-{$key}'>{$val}</div>";
        }
    }
    $form = "<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/index.php?bc_action=save_export_setting' method='post' data-id='{$data['id']}'>
				<h3>Выгрузка {$data['id']}</h3>
				<div class='formwrap'>
					{$input}
				</div>
				<div class='v-btn'>
						<span class='bc-btn'>
							<input type='submit' value='Сохранить изменения'>
						</span>
						<a class='bc-btn red ml-5' href='javascript:void(0);' onclick='Export1C.deleteFormSettingExport(this); return false;'>
							Удалить
						</a>
						<div class='result respadding'></div>
				</div>
			</form>";

    return $form;
}
/**
 * @param string $setkey name input
 * @param string $setvalue value input
 * @param string $title title lable
 * @param array $params string|attrs, string|type, bool|reg, bool|readonly, string|class
 *
 * @return string
 */
function kz_input($setkey, $setvalue = '', $title = '', $params = [])
{

    $attrs = (isset($params['attrs']) ? $params['attrs'] : '');
    $type = (isset($params['type']) ? $params['type'] : 'text');
    $reg = (isset($params['reg']) && $params['reg'] ? "<div class='red'>*</div>" : null);
    $readonly = (isset($params['readonly']) && $params['readonly'] ? 'readonly="readonly"' : null);
    $class = ($setvalue || (is_numeric($setvalue) && $setvalue == 0) ? "active " : '');
    $class .= (isset($params['class']) ? $params['class'] : '');


    if ($setkey) {
        $str = "<div class='input-field'>
					<input type='{$type}' name='{$setkey}' value='{$setvalue}' {$attrs} {$readonly}>
					<label class='{$class}'>{$title}{$reg}<b></b></label>
					<span></span>
				</div>";
    } else {
        $str = "";
    }
    return $str;
}

function bc_droplist($type, $setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    $result = '';
    $listContent = '';
    switch ($type) {
        case 'checkbox':
            if (is_array($setvalue)) {
                foreach ($setvalue as $item) {
                    $key = $item['name'] ? $item['name'] : $setkey;
                    $listContent .= "<li class='drop-list-item'>" . bc_checkbox($key, $item['value'], $item['title'], $item['checked']) . "</li>";
                }
            } else $listContent = $setvalue;
            $result = "<div class='drop-list-block checkbox-list outside-close' data-closeclass='active'>
                           <button class='bc-btn-green drop-list-btn toggle' data-toggle='active' data-parents='.drop-list-block' type='button'>{$title}</button>
                           <div class='drop-list'>
                                <ul>{$listContent}</ul>
                                <button type='button' class='bc-btn-green btn-small all-select'>Выбрать все</button>
                                <button type='button' class='bc-btn-green btn-small all-unselect'>Убрать все</button>
                           </div>
                       </div>";
            break;
    }
    return $result;
}

function bc_form($set_data)
{

    foreach ($set_data as $value => $key) {
        switch ($key['type']) {
            case 'text': # строка
                $input_str .= bc_input($key['name'], $key['value'], $key['label']);
                break;
            case 'textarea': # строка
                $input_str .= bc_textarea($key['name'], $key['value'], $key['label']);
                break;
        }
    }
    $str = $input_str;
    return $str;
}

function bc_info($text = '')
{
    $str = "<div class='info-filed none'>
				<div class='info-filed-img'></div>
				<div class='info-filed-text'>{$text}</div>
			</div>";
    return $str;
}

# INPUT
function bc_text($title = '', $attrs = '', $req = 0)
{
    if ($title) {
        $str = "<div class='input-field input-text' {$attrs}>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</div>";
    } else {
        $str = "";
    }
    return $str;
}

# INPUT
function bc_input($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0, $type = 'text')
{
    if ($setkey) {
        $str = "<div class='input-field'>
					<input type='{$type}' name='{$setkey}' value='{$setvalue}' {$attrs}>
					<label " . ($setvalue || (is_numeric($setvalue) && $setvalue == 0) ? "class='active'" : null) . ">{$title}" . ($req ? "<div class='red'>*</div>" : null) . " <b></b></label>
					<span></span>
				</div>";
    } else {
        $str = "";
    }
    return $str;
}

# TEXTAREA
function bc_textarea($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    if ($setkey) {
        $str = "<div class='textarea-field'>
							" . ($title ? "<div class='textarea-title'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . " <b></b></div>" : "") . "
							" . (stristr($attrs, "bbcode") ? nc_bbcode_bar('this', 'adminForm', $setkey, 1) : "") . "
							<textarea cols=501 rows=6 name='{$setkey}' {$attrs}>{$setvalue}</textarea>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}

# CHECKBOX
function bc_checkbox($setkey, $setvalue = '', $title = '', $checked = false, $attrs = '', $req = 0, $type = '')
{
    if ($setkey) {
        $str = "<div class='switch'>
							<label>
								<input type='checkbox' {$attrs} value='{$setvalue}' " . ($checked ? "checked" : null) . " " . (stristr($title, "<br>") ? "data-twoline" : "") . " name='{$setkey}'>
								<span class='lever" . ($type ? "-" . $type : "") . "'></span>
								" . ($title ? "<span class='sw-text'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</span>" : null) . "
							</label>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}

# RADIO
function bc_radio($setkey, $setvalue = '', $title = '', $checked = false)
{
    if ($setkey) {
        $str = "<div class='input-radio'>
							<label>
								<input name='{$setkey}' type='radio' value='{$setvalue}' " . ($checked ? "checked" : null) . ">
								<span>{$title}</span>
							</label>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}

# FILE
function bc_file($setkey, $data = '', $title = '', $link = '', $type = '', $req = '', $attr = '')
{
    global $HTTP_FILES_PATH, $DOCUMENT_ROOT, $AUTH_USER_ID;
    if ($setkey) {
        if ($type == 'bc') {
            $delkey = str_replace("bc_", "", $setkey);
            $delkey = "nofile[{$delkey}]";
            $name = $data;
        } elseif ($type == 'fl') {
            $delkey = str_replace("fl_", "", $setkey);
            $delkey = "nofile[{$delkey}]";
            $dataArr = $tmpf = explode(":", $data);
            $name = $dataArr[0];
        } elseif ($type == 'f') {
            $delkey = str_replace("f_", "", $setkey);
            $delkey = "nofile[{$delkey}]";
            $dataArr = $tmpf = explode(":", $data);
            $name = $dataArr[0];
        } else {
            if (is_numeric($type)) {
                $delkey = "f_KILL" . $type;
            }
            $dataArr = explode(":", $data);
            $name = $dataArr[0];
        }

        $img = $HTTP_FILES_PATH . $subdiv . "/" . $tmpf[0];
        $str = "<div class='input-file " . ($name ? "active acive-del" : "") . "'>
    				<input name='{$setkey}' size='50' type='file' title='{$title}' multiple='multiple' {$attr}>
    				<div class='file-rectangle'>
						<div class='file-rectangle-name'>Файл</div>
    				</div>
    				<div class='file-text'>
						<div class='file-title ws'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</div>
						<div class='file-name file-name-this ws'>Выберите файл</div>
						<div class='file-link ws'><a href='{$link}' " . (is_image($DOCUMENT_ROOT . $link) ? "data-rel='lightcase'" : "") . ">{$name}</a></div>
    				</div>
    				" . (is_numeric($type) || $type == 'f' ? "<input type='hidden' name='{$setkey}_old' value='{$data}'>" : "") . "
    				" . ($name && isset($delkey) ? "<div class='file-del'>" . bc_checkbox($delkey, 1, 'удалить', '', '', 0, "mark") . "</div>" : "") . "
				</div>";
    } else {
        $str = "";
    }
    return $str;
}

# COLOR
function bc_color($setkey, $setvalue = '', $title = '', $attr = '')
{
    global $HTTP_FILES_PATH;
    if ($setkey) {
        $str = "<div class='input-color " . ($setvalue ? "active" : null) . "'>
							<input class='color' type='text' size='35' name='{$setkey}' value='{$setvalue}' {$attr} " . (stristr($title, "<br>") ? "data-twoline" : "") . " style='" . ($setvalue ? "background:{$setvalue}" : null) . "'>
							<span class='color-text'>
								<span class='color-top'>{$title}</span>
								<span class='color-bottom'>" . ($setvalue ? $setvalue : null) . "</span>
							</span>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}

# SELECT
function bc_select($setkey, $setvalue = '', $title = '', $attrs = '')
{
    global $HTTP_FILES_PATH;
    if ($setkey) {
        $str = "<div class='input-field input-select'>
							<select name='{$setkey}' {$attrs}>{$setvalue}</select>
							<label>{$title}</label>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}

# ALIGN
function bc_align($setkey, $setvalue = '', $title = '', $attr = '')
{
    global $HTTP_FILES_PATH;
    if ($setkey) {
        $str = "<div class='input-field input-align' {$attr}>
		                    <div class='align-name'>{$title}</div>
		                    <div class='align-body'>
	                            <label class='align-body-left'><input name='{$setkey}' type=radio value='left' " . ($setvalue == 'left' || !$settings['floatbody'] ? "checked" : null) . "><span></span></label>
	                            <label class='align-body-center'><input name='{$setkey}' type=radio value='center' " . ($setvalue == 'center' ? "checked" : null) . "><span></span></label>
	                            <label class='align-body-right'><input name='{$setkey}' type=radio value='right' " . ($setvalue == 'right' ? "checked" : null) . "><span></span></label>
		                    </div>
		                </div>";
    } else {
        $str = "";
    }
    return $str;
}

# MULTI INTUTS
function bc_multi_line($setkey, $setvalue, $title = '', $type = 1, $params = [])
{
    global $HTTP_FILES_PATH, $db, $langs, $catalogue, $AUTH_USER_ID;

    if ($type == 3) { // стандартные списки
        if ($setvalue && isJson($setvalue)) {
            $param = orderArray($setvalue);
            if (!$param[values]) {
                $options = getOptionStandart(array("id" => 0, "key" => $setkey), $param['cols']);
            } else {
                foreach ($param[values] as $key => $item) {
                    $options .= getOptionStandart(array("id" => $key, "key" => $setkey), $param['cols'], $item);
                }
            }
        }
        $html = "<div class='multi-lines'>
					<h4>{$title}</h4>
					<input name='gsgerg' value='{$setvalue}' type='hidden'>
					<input name='{$setkey}[default]' value='" . json_encode($param['cols']) . "' type='hidden'>
					<div id='{$setkey}'>{$options}</div>
					<a class='add-btn' href=\"\" onclick=\"add_line('{$setkey}'); return false;\">добавить еще</a>
				</div>";
    } elseif ($type == 2) { // вывод кол-ва объектов
        if ($setvalue) {
            $valArray = explode(".", $setvalue);
            foreach ($valArray as $key => $value) {
                if (stristr($value, ":")) {
                    $options .= getOptionSize($key, explode(":", $value));
                }
            }
        }
        if (!$options) {
            $options = getOptionSize(0, array(1 => 4));
        }

        $id = "count" . rand();
        $html = "<div class='multi-counts none'>
					<input value='" . ($setvalue ? $setvalue : "1280:4") . "' name='{$setkey}' hidden>
					<div class='multi-body'>
						<div id='{$id}'>{$options}</div>
						<a class='add-btn' href=\"\" onclick=\"add_line('{$id}'); return false;\">добавить еще</a>
					</div>
				</div>";
    } else { // стандартные списки
        if ($setkey == 'bc_lists_language_sub') { # поля мультиязычности
            $count = count($langs['lang']) + 1;
            $options = '';
            # Разделы
            $subdivisions = $db->get_results("SELECT a.Subdivision_Name as name, a.EnglishName as keyword, a.AlterTitle as alt
												FROM Subdivision as a, Sub_Class as b
												WHERE a.Subdivision_ID = b.Subdivision_ID
												        AND a.Catalogue_ID = '{$catalogue}' AND a.Hidden_URL NOT LIKE '%search%'
												        AND a.Hidden_URL NOT LIKE '%404%'  AND a.Hidden_URL NOT LIKE '%zone%'
												        AND a.Hidden_URL NOT LIKE '%blockofsite%' AND a.Hidden_URL NOT LIKE '%sitemap%'
												        AND a.Hidden_URL NOT LIKE '%excel%' AND a.Hidden_URL NOT LIKE '%profile%'
												        AND a.Hidden_URL NOT LIKE '%settings%' AND a.Hidden_URL NOT LIKE '%cart/success%'
												        AND a.Hidden_URL NOT LIKE '%cart/fail%' AND (a.Hidden_URL NOT LIKE '%system%' OR a.Hidden_URL LIKE '%system/politika%')
														AND a.Hidden_URL NOT LIKE '%forms%'
												ORDER BY a.Hidden_URL, a.Priority", ARRAY_A);
            $subdivisions = array_merge([0 => ['keyword' => 'index', 'name' => 'Главная'], 1 => ['keyword' => 'sitemap', 'name' => 'Карта сайта']], $subdivisions);
            $i = 1000;
            if ($subdivisions) {
                foreach ($subdivisions as $s) {
                    $keyName = "lang_sub_{$s['keyword']}";

                    $options .= "<div class='multi-line' data-num='{$i}'>";
                    $options .= "<div class='colline none'>" . bc_input("bc_lists_language_sub[{$i}][keyword]", $keyName) . "</div>";
                    $options .= "<div class='colline colline-{$count}'>" . bc_text("<b>{$s['name']}</b>") . "</div>";

                    foreach ($langs['lang'] as $key => $lang) {
                        $name = ($langs['keys'][$keyName][$key] ?: ($langs['main']['keyword'] == $key ? $s['name'] : ""));
                        $options .= "<div class='colline colline-{$count}' {$keyName} {$key} {$langs['keys'][$keyName]}>
                                        " . bc_input("bc_lists_language_sub[{$i}][{$key}]", $name, $lang['name'], "maxlength='255' size='50'") . "
                                    </div>";
                    }
                    $options .= "</div>";
                    $i++;

                    if ($s['alt']) {
                        $keyName = "lang_sub_alt{$s['keyword']}";

                        $options .= "<div class='multi-line' data-num='{$i}'>";
                        $options .= "<div class='colline none'>" . bc_input("bc_lists_language_sub[{$i}][keyword]", $keyName) . "</div>";
                        $options .= "<div class='colline colline-{$count}'>" . bc_text("<b>{$s['name']} (Альтер.)</b>") . "</div>";

                        foreach ($langs['lang'] as $key => $lang) {
                            $name = ($langs['keys'][$keyName][$key] ?: ($langs['main']['keyword'] == $key ? $s['alt'] : ""));
                            $options .= "<div class='colline colline-{$count}' {$keyName} {$key} {$langs['keys'][$keyName]}>
                                            " . bc_input("bc_lists_language_sub[{$i}][{$key}]", $name, $lang['name'], "maxlength='255' size='50'") . "
                                        </div>";
                        }
                        $options .= "</div>";
                        $i++;
                    }
                }
            }
        } elseif ($setkey == 'bc_lists_language_blk') { # поля мультиязычности
            $count = count($langs['lang']) + 1;
            # Разделы
            $blocks = $db->get_results("SELECT block_id as id, name FROM Message2016 WHERE Catalogue_ID = {$catalogue}", ARRAY_A);
            $i = 2000;
            if ($blocks) {
                foreach ($blocks as $b) {
                    $options .= "<div class='multi-line' data-num='{$i}'>";
                    $keyName = "lang_blk_{$b[id]}";
                    $options .= "<div class='colline none'>" . bc_input("bc_lists_language_blk[{$i}][keyword]", $keyName) . "</div>";
                    $options .= "<div class='colline colline-{$count}'>" . bc_text("<b>{$b[name]}</b>") . "</div>";
                    foreach ($langs[lang] as $key => $lang) {
                        $name = $langs['keys'][$keyName][$key] ? $langs['keys'][$keyName][$key] : ($langs['main']['keyword'] == $key ? $b['name'] : "");
                        $options .= "<div class='colline colline-{$count}' {$keyName} {$key} {$langs['keys'][$keyName]}>" . bc_input("bc_lists_language_blk[{$i}][{$key}]", $name, $lang['name'], "maxlength='255' size='50'") . "</div>";
                    }
                    $options .= "</div>";
                    $i++;
                }
            }
        } else {    # обычные поля
            if ($setvalue) {
                foreach ($setvalue as $dataid => $data) {
                    if ($data || $setkey == 'bc_lists_language_keys' || $setkey == 'bc_lists_cdekTarifId') {
                        $options .= getOptionMulti(array('id' => $dataid, "key" => $setkey, 'template_multi_line' => $params['template_multi_line']), $data);
                    }
                }
            }
            if (!$options) {
                $options .= getOptionMulti(array('id' => 0, "key" => $setkey, 'template_multi_line' => $params['template_multi_line']));
            }
        }

        $html = "<div class='multi-lines'>
					" . ($title ? "<h4>{$title}</h4>" : null) . "
                    " . ($params['delete_all'] ? "<a class='delete-btn 'href='javascript:void(0)' onclick=\"remove_all_line('{$setkey}')\">удалить все</a>" : null) . "
                    <template id='template_{$setkey}'>
                        " . getOptionMulti(array('id' => 0, "key" => $setkey, 'template_multi_line' => $params['template_multi_line'])) . "
                    </template>
                    <input type='hidden' name='{$setkey}' value=''/>
					<div id='{$setkey}'>{$options}</div>
					<a class='add-btn' href=\"\" onclick=\"add_line('{$setkey}'); return false;\">добавить еще</a>
				</div>";
    }

    return $html;
}

/**
 * bc_multi_line_v2
 *
 * @param  string $setkey
 * @param  array $value
 * @param  string $title
 * @param  array $params
 * @var bool $params['delete_all'] добовляет кнопку удалить все
 * @var array $params['template'] шаблон
 * @return string
 */
function bc_multi_line_v2($setkey, $value, $title = '', $params = [])
{
    $initTemplate = [];
    foreach ($params['template'] as $name => $input) {
        foreach ($input as $keyInput => $valueInput) {
            switch ($keyInput) {
                case 'data':
                    $initTemplate[$name][$keyInput] = (is_string($valueInput) && function_exists($valueInput) ? $valueInput($setkey) : $valueInput);
                    break;
                default:
                    $initTemplate[$name][$keyInput] = $valueInput;
                    break;
            }
        }
    }

    $options = '';
    foreach ($value as $valueID => $data) {
        $options .= getOptionMultiV2(['id' => $valueID, "key" => $setkey, 'template' => $initTemplate], $data);
    }

    return "<div class='multi-lines'>
                " . ($title ? "<h4>{$title}</h4>" : null) . "
                " . ($params['delete_all'] ? "<button class='delete-btn' onclick=\"remove_all_line('{$setkey}')\">удалить все</button>" : null) . "
                <template id='template_{$setkey}'>
                    " . getOptionMultiV2(['id' => 'replace_key_js', "key" => $setkey, 'template' => $initTemplate]) . "
                </template>
                <input type='hidden' name='{$setkey}' value=''/>
                <div id='{$setkey}'>{$options}</div>
                <a class='add-btn' href=\"\" onclick=\"multiLineV2.addLine('{$setkey}'); return false;\">добавить еще</a>
            </div>";
}

/**
 * getOptionMultiV2
 *
 * @param  array $parms
 * @var string $parms[id] - ид параметра
 * @var string $parms[key] - ключ мультилайна
 * @var array $parms[template] - шаблон мультилайна
 * @param array $data
 * @return string
 */
// function bc_input($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0, $type = 'text')
function getOptionMultiV2($params, $data = [])
{
    $result = '';
    foreach ($params['template'] as $title => $paramsInput) {
        $field = '';
        $fieldName = $paramsInput['attr']['name'];
        $name = "{$params['key']}[{$params['id']}][{$fieldName}]";
        $class = (is_array($paramsInput['classList']) ? implode(' ', $paramsInput['classList']) : '');

        unset($paramsInput['attr']['name']);

        $attr = '';
        if (!empty($paramsInput['attr'])) {
            foreach ($paramsInput['attr'] as $key => $value) {
                $attr .= "{$key}=\"{$value}\"";
            }
        }

        switch ($paramsInput['type']) {
            case 'input':
                $field = bc_input($name, $data[$fieldName], $title, $attr, $paramsInput['req'], ($paramsInput['attr']['type'] ?: 'text'));
                break;
            case 'checkbox':
                $field = bc_checkbox($name, 1, $title, $data[$fieldName]);
                break;
            case 'color':
                $field = bc_color($name, $data[$fieldName], $title) . "</div>";
                break;
            case 'select':
                $option = getOptionsFromArray($paramsInput['data'], $data[$fieldName]);
                $field = bc_select($name, $option, $title, "class='ns'");
                break;
            case 'textarea':
                $field = bc_textarea($name, $data[$fieldName], $title);
                break;
            case 'droplist':
                $field = bc_droplist($paramsInput['data'], $name, $data[$fieldName], $title);
                break;
        }

        $result .= "<div class='colline colline-{$paramsInput['colline']} colline-{$fieldName} {$class}'>{$field}</div>";
    }

    return "<div class='multi-line multi-list " . ($data["checked"] ? "" : "multi-disable") . "' data-num='{$params['id']}'>
                {$result}
                " . (!$data['no_remove'] ? "<div class='multi-line-remove icons i_del3' onclick='remove_line(this); return false;'></div>" : '') . "
            </div>";
}

function getOptionMulti($param, $data = array())
{
    global $setting, $langs, $db, $cityvars;
    $listname = $setting['language'] ? $langs['main']['name'] : 'Название';
    $count = $setting['language'] ? count($langs['lang']) + 1 : 2;
    $intuts = array(
        'bc_setting_DSO' => array(
            'Сумма от' => array('input', 'sum', '3', 1, 'number'),
            'Скидка в %' => array('input', 'discount', '3', 1, 'number'),
            'Включить' => array('checkbox', 'checked', '3', 1),
        ),
        'bc_lists_texts' => array(
            'Ключевое слово' => array('input', 'keyword', $count, 1),
            $listname => array('input', 'name', $count, 1),
            'Включить' => array('checkbox', 'checked', '1', 1),
        ),
        'bc_lists_targetcity' => array(
            'Название' => array('input', 'name', '3', 1),
            'Название на латинице без пробелов' => array('input', 'keyword', '3', 1),
            'Включить' => array('checkbox', 'checked', '3-1-2', 1),
            'Основной' => array('checkbox', 'main', '3-1-2', 1),
        ),
        'bc_lists_delivery' => array(
            'Название' => array('input', 'name', '5-2', 1),
            'Цена' => array('input', 'price', '5', 2),
            'Тип доставки' => array('select', 'delivery_type', '5', 3),
            'Включить' => array('checkbox', 'checked', '5', 4),
            'Описание' => array('input', 'text', '5-4', 5),
            'Таргетинг' => array('droplist', 'targeting', 5, 1, 'checkbox'),
            'Артикул Frontpad' => array('input', 'art', '2', 6),
            'Сумма до бесплатной доставки' => array('input', 'totsumfree', '1', 7),
            'id терминала iiko' => array('input', 'iikoTerminalId', '2', 8),
            'Тип пользователя' => array('select', 'userType', 1, 9, array(1 => 'любой', 2 => 'физ лицо', 3 => 'юр лицо')),
            'Номер WhatsApp для уведомлений (10 цифр в международном формате, например, 79270001234)' => array('input', 'whatsappNumberForNotification', '1', 10),
            'Почта для уведомлений' => array('input', 'emailForNotification', '1', 11),
        ),
        'bc_lists_payment' => array(
            'Название' => array('input', 'name', '5-4', 1),
            'Включить' => array('checkbox', 'checked', '5', 1),
            'Описание' => array('input', 'text', '1', 1),
            'Тип оплаты в iiko' => array('input', 'iikoType', '1', 1),
            'Тип оплаты в Frontpad' => array('input', 'frontpadType', '1', 1),
            'Тип оплаты' => array('select', 'servise_payment', '1', 4, getOptionPaymentServis()),
            'Тип пользователя' => array('select', 'userType', 1, 5, array(1 => 'любой', 2 => 'физ лицо', 3 => 'юр лицо')),
        ),
        'bc_lists_itemlabel' => array(
            'Название' => array('input', 'name', '2', 1),
            'Текст' => array('input', 'text', '2', 1),
            'Цвет фона' => array('color', 'color1', '5-2', 1),
            'Цвет текста' => array('color', 'color2', '5-2', 1),
            'Включить' => array('checkbox', 'checked', '5', 1)
        ),
        'bc_lists_language' => array(
            'Обозначение (поддомен)' => array('input', 'keyword', '2', 1),
            'Язык' => array('input', 'name', '2', 1),
            'Включить' => array('checkbox', 'checked', '1', 1),
        ),
        'bc_lists_params' => array(
            'Ключевое слово' => array('input', 'keyword', 2, 1),
            'Название' => array('input', 'name', 2, 1),
            // 'В фильтре' => array('checkbox', 'in_filter', '3-1-2', 1),
            // 'Тип вывода в фильтре' => ['select', 'filter_type', '3', 1, [2 => 'варианты галочками', 1 => 'варианты в списке', 4 => 'от-до', 3 => 'есть/нет (одна галочка)']],
            // 'от-до' => array('checkbox', 'otdo_filter', '3-1-2', 1),
            'В товаре' => array('checkbox', 'checked', '3-1-2', 1),
            'В превью товара' => array('checkbox', 'preview', '3', 1),
            // 'Приоритет' => array('input', 'priority', '3-1-2', 1)
        ),
        'bc_lists_edizm' => array(
            'Цифровой код' => array('input', 'keyword', 2, 1, 'number'),
            'Название' => array('input', 'name', 2, 1)
        ),
        'bc_lists_tradesoft' => array(
            'Имя поставщика' => array('input', 'name', '3', 1),
            'Логин' => array('input', 'login', '3', 1),
            'Пароль' => array('input', 'password', '3', 1),
            'Наценка' => array('textarea', 'rate', '1', 1),
        ),
        'bc_lists_cdekTarifId' => array(
            'ID доставки' => array('input', 'id', '2', 1),
            'Тип доставки' => array('select', 'type', '2', 4, array(0 => "Выключены", 1 => "склад-склад", 2 => "склад-дверь", 3 => "дверь-склад", 4 => "дверь-дверь"))
        ),
        'sb_tags_links' => array(
            'Название' => array('input', 'name', '2', 1),
            'Ссылка' => array('input', 'link', '2', 2)
        ),
        'sb_tags_links_bottom' => array(
            'Название' => array('input', 'name', '2', 1),
            'Ссылка' => array('input', 'link', '2', 2)
        ),
        'bc_turbo_tokens' => array(
            'Наименования домена' => array('input', 'name', '2', 1),
            'Token' => array('input', 'token', '2', 2)
        ),
        'f_links' => array(
            'Ссылка' => array('input', 'link', 3, 1),
            'надпись на ссылке' => array('input', 'title', 3, 2),
            'Тип ссылки' => array('select', 'type', 3, 4, array(0 => "Без типа", 'vk' => "вк", 'whatsapp' => "ватсап", 'tg' => "телеграм"))
        ),
        'sb_filter_for_name' => [
            'В поиске' => ['input', 'name', '2', 1],
            'Название' => ['input', 'text', '2', 1],
            'Включить' => ['checkbox', 'on', '2', 1],
        ],
        'f_order_count_price' => [
            'Количество' => ['input', 'count', 2, 1],
            'Цена' => ['input', 'price', 2, 2],
        ],
        'bc_lists_order_status' => [
            'id' => ['input', 'id', 0, 0, 'hidden'],
            'Название' => ['input', 'name', '1', 1],
        ],
        'bc_lists_order_status_email_template' => [
            'Статус' => ['select', 'status_id', '1', 1, ['' => 'Статус не выбран']],
            'Шаблон письма' => ['textarea', 'email_template', '1', 2],
        ],
    );

    if (isset($param['template_multi_line']) && is_array($param['template_multi_line'])) {
        $intuts[$param['template_multi_line']['key']] = $param['template_multi_line']['template'];
    }

    if ($setting['language']) {
        foreach ($setting['lists_language'] as $key => $lng) {
            if ($key == 0) {
                continue;
            }
            $keys[$lng['name']] = array('input', $lng['keyword'], $count, $lng['checked']);
        }
        $intuts['bc_lists_language_keys'] = $keys;
    }

    $result = "";
    foreach ($intuts[$param['key']] as $title => $p) {
        /**
         * $p[0] - тип интупа select|textarea|input|checkbox|color|droplist
         * $p[1] - значения параметра name у инпута
         * $p[2] - сколько колонок займет инпут
         * $p[3] - флаг видимости поля, если нету то добовляеться класс none
         * $p[4] - данные интупа, для droplist тип инпутов
         */

        $name = "{$param['key']}[{$param['id']}][{$p[1]}]";
        $typeInput = (isset($p[4]) ? $p[4] : 'text');
        $class = $p[3] ? "" : "none";

        if (
            $p[1] == 'art' && !$setting['frontpadSecret'] # артикул Frontpad
            || $p[1] == 'totsumfree' && !$setting['freedelivery'] # расчет до минимальной доставки
            || (($p[1] == 'iikoType' || $p[1] == 'iikoTerminalId') && !$setting['iikoCheck']) # поля с типами для iiko
            || ($p[1] == 'frontpadType' && !$setting['frontpadSecret']) # поля с типами для iiko
            || $p[1] == 'targeting' && !$setting['targeting'] # таргетинг
        ) {
            $class .= " none";
        }
        if ($p[1] == 'targeting') {
            $targetValues = array();
            if (is_array($cityvars)) {
                foreach ($cityvars as $cityID => $city) {
                    if ($city['checked']) $targetValues[] = array('name' => "{$name}[{$cityID}]", 'value' => $cityID, 'title' => $city['name'], 'checked' => isset($data[$p[1]][$cityID]));
                }
            }
            $data[$p[1]] = $targetValues;
        }

        switch ($p[0]) {
            case 'input':
                $result .= "<div class='colline colline-{$p[2]} colline-{$p[1]} {$class}'>" . bc_input($name, $data[$p[1]], $title, '', 0, $typeInput) . "</div>";
                break;
            case 'checkbox':
                $result .= "<div class='colline colline-{$p[2]} colline-{$p[1]} {$class}'>" . bc_checkbox($name, 1, $title, $data[$p[1]]) . "</div>";
                break;
            case 'color':
                $result .= "<div class='colline colline-{$p[2]} colline-{$p[1]} {$class}'>" . bc_color($name, $data[$p[1]], $title) . "</div>";
                break;
            case 'select':
                switch (true) {
                    case 'delivery_type' === $p[1]:
                        $sql = "SELECT `delivery_type_Name` as name, `delivery_type_ID` as id FROM `Classificator_delivery_type`";
                        $p[4] = [];
                        foreach ($db->get_results($sql, ARRAY_A) ?: [] as $dbRow) {
                            $p[4][$dbRow['id']] = $dbRow['name'];
                        }
                        unset($sql, $dbRow);
                        break;
                    case 'bc_lists_order_status_email_template' === $param['key'] && 'status_id' === $p[1]:
                        $p[4] = $p[4] ?? [];
                        foreach ((new Class2005())->getOrderStatusList() as $orderStatus) {
                            $p[4][$orderStatus['id']] = $orderStatus['name'];
                        }
                        break;
                }
                $option = getOptionsFromArray($p[4], $data[$p[1]]);
                $result .= "<div class='colline colline-{$p[2]} colline-{$p[1]} {$class}'>" . bc_select($name, $option, $title, "class='ns'") . "</div>";
                break;
            case 'textarea':
                $result .= "<div class='colline colline-{$p[0]} colline-{$p[1]} colline-{$p[1]} {$class}'>" . bc_textarea($name, $data[$p[1]], $title) . "</div>";
                break;
            case 'droplist':
                $result .= "<div class='colline colline-{$p[2]} colline-{$p[1]} colline-{$p[1]} {$class}'>" . bc_droplist($p[4], $name, $data[$p[1]], $title) . "</div>";
                break;
        }
    }

    return "<div class='multi-line multi-list " . ($data["checked"] ? "" : "multi-disable") . "' data-num='{$param['id']}'>{$result}<div class='multi-line-remove icons i_del3' onclick='remove_line(this); return false;'></div></div>";
}
function getOptionSize($num, $data = array())
{
    $widths = array('1700' => 'до 1700', '1440' => 'до 1440', '1279' => 'до 1280', '780' => 'до 780', '650' => 'до 650', '550' => 'до 550', '450' => 'до 450', '320' => 'до 320');
    return "<div class='multi-line' data-num='{$num}'>
				<div class='colline colline-2'>" . bc_select("countwidth[{$num}][width]", getOptionsFromArray($widths, $data[0]), "", "class='ns'") . "</div>
				<div class='colline colline-2'>" . bc_input("countwidth[{$num}][count]", $data[1]) . "</div>
			</div>";
}
function getOptionStandart($param, $cols, $data = array())
{
    foreach ($cols as $key => $item) {
        $name = "{$param[key]}[{$param[id]}][{$item[name]}]";
        switch ($item[type]) {
            case 'input':
                $result .= "<div class='colline colline-{$item[col]} colline-{$item[name]}'>" . bc_input($name, $data[$item[name]], $item[title]) . "</div>";
                break;
            case 'checkbox':
                $result .= "";
                break;
            case 'color':
                $result .= "";
                break;
            case 'select':
                $option = getOptionsFromArray($item['options'], $data[$item['name']] ?? null);
                $result .= "<div class='colline colline-{$item['col']} colline-{$item['name']}'>" . bc_select($name, $option, $item['title'], "class='ns'") . "</div>";
        }
    }
    return "<div class='multi-line multi-lines-default' data-num='{$param[id]}'>
		{$result}
		<div class='multi-line-remove icons i_del3' onclick='remove_line(this); return false;'></div>
	</div>";
}

# загрузка файлов (переписанная)
function gv_multifile_field($field_name, $name)
{
    if ($field_name) {
        $max_priority = 0;
        $gv_settings = $field_name->settings;

        $result .= "<div class='none'>
                        <input type='hidden' name='settings_photo[use_name]' value='{$gv_settings->__get('use_name')}'>
                        <input type='hidden' name='settings_photo[path]' value='{$gv_settings->__get('path')}'>
                        <input type='hidden' name='settings_photo[use_preview]' value='{$gv_settings->__get('use_preview')}'>
                        <input type='hidden' name='settings_photo[preview_width]' value='{$gv_settings->__get('preview_width')}'>
                        <input type='hidden' name='settings_photo[preview_height]' value='{$gv_settings->__get('preview_height')}'>
                        <input type='hidden' name='settings_photo[preview_mode]' value='{$gv_settings->__get('preview_mode')}'>
                        <input type='hidden' name='settings_photo[resize_width]' value='{$gv_settings->__get('resize_width')}'>
                        <input type='hidden' name='settings_photo[resize_height]' value='{$gv_settings->__get('resize_height')}'>
                        <input type='hidden' name='settings_photo[resize_mode]' value='{$gv_settings->__get('resize_mode')}'>
                        <input type='hidden' name='settings_photo[crop_x0]' value='{$gv_settings->__get('crop_x0')}'>
                        <input type='hidden' name='settings_photo[crop_y0]' value='{$gv_settings->__get('crop_y0')}'>
                        <input type='hidden' name='settings_photo[crop_x1]' value='{$gv_settings->__get('crop_x1')}'>
                        <input type='hidden' name='settings_photo[crop_y1]' value='{$gv_settings->__get('crop_y1')}'>
                        <input type='hidden' name='settings_photo[crop_mode]' value='{$gv_settings->__get('crop_mode')}'>
                        <input type='hidden' name='settings_photo[crop_width]' value='{$gv_settings->__get('crop_width')}'>
                        <input type='hidden' name='settings_photo[crop_height]' value='{$gv_settings->__get('crop_height')}'>
                        <input type='hidden' name='settings_photo[crop_ignore_width]' value='{$gv_settings->__get('crop_ignore_width')}'>
                        <input type='hidden' name='settings_photo[crop_ignore_height]' value='{$gv_settings->__get('crop_ignore_height')}'>
                        <input type='hidden' name='settings_photo[preview_crop_x0]' value='{$gv_settings->__get('preview_crop_x0')}'>
                        <input type='hidden' name='settings_photo[preview_crop_y0]' value='{$gv_settings->__get('preview_crop_y0')}'>
                        <input type='hidden' name='settings_photo[preview_crop_x1]' value='{$gv_settings->__get('preview_crop_x1')}'>
                        <input type='hidden' name='settings_photo[preview_crop_y1]' value='{$gv_settings->__get('preview_crop_y1')}'>
                        <input type='hidden' name='settings_photo[preview_crop_mode]' value='{$gv_settings->__get('preview_crop_mode')}'>
                        <input type='hidden' name='settings_photo[preview_crop_width]' value='{$gv_settings->__get('preview_crop_width')}'>
                        <input type='hidden' name='settings_photo[preview_crop_height]' value='{$gv_settings->__get('preview_crop_height')}'>
                        <input type='hidden' name='settings_photo[preview_crop_ignore_width]' value='{$gv_settings->__get('preview_crop_ignore_width')}'>
                        <input type='hidden' name='settings_photo[preview_crop_ignore_height]' value='{$gv_settings->__get('preview_crop_ignore_height')}'>
                        <input type='hidden' name='settings_photo[min]' value='{$gv_settings->__get('min')}'>
                        <input type='hidden' name='settings_photo[max]' value='{$gv_settings->__get('max')}'>
                        <input type='hidden' name='settings_photo_hash' value='{$gv_settings->get_setting_hash()}'>
                        <input type='hidden' name='multifile_js[$field_name->name]' value='1'>
                    </div>
                    <script type='text/javascript'>
                            \$nc(document).ready(function() {
                                \$nc('#table{$field_name->name}').tableDnD({
                                    onDragClass: 'DTDClass',
                                    dragHandle: '.DTD'
                                });
                            });
                    </script>";

        $photos = '';
        $max_priority = -1;

        if (isset($field_name->records[0])) {
            foreach ($field_name->records as $record) {
                if ($max_priority < $properties->Priority) {
                    $max_priority = $properties->Priority;
                }

                $photos .= "<tr>
							    <td class='DTD icons i_threedots'></td>
							    <td class='DTD-photo'>
							    	<div class='image-default image-contain'><a href='{$record->Path}' target='_blank'><img src='{$record->Path}'></a></div>
							    </td>
							    <td class='DTD-text'>
                                    " . ($field_name->settings->use_name ? bc_input("multifile_name[{$field_name->name}][]", $record->Name, $field_name->settings->custom_name)  : null) . "
							    </td>
							    <td class='DTD-del'>
                                    <input type='hidden' name='multifile_id[{$field_name->name}][]' value='{$record->ID}'/>
                                    <input type='hidden' name='multifile_upload_index[{$field_name->name}][]' value='-1'/>
                                    <input type='hidden' name='multifile_delete[{$field_name->name}][]' value='0'/>
							    	" . bc_checkbox("multi_delete", 0, "удалить", "", "", 0, "mark") . "
							    </td>
						    </tr>";
            }
        }
        $accept = explode(';', $field_name->format)[0];
        if ($accept && strpos($accept, ':') !== false) {
            $accept = preg_replace("/^[^:]*:/", '', $accept);
        }

        $result .= "<table cellspacing='0' cellpadding='2' class='table-files' id='table{$field_name->name}' data-num='-1' data-priority='{$max_priority}'>
                        <tbody>
                            {$photos}
                            <tr class='none multy-tmp'>
                                <td class='DTD icons i_threedots'></td>
                                <td class='DTD-photo'>
                                    <div class='image-default image-contain'><img src=''></div>
                                </td>
                                <td class='DTD-text'>
                                    " . ($field_name->settings->use_name ? bc_input("multifile_name[{$field_name->name}][]", '', $field_name->settings->custom_name)  : null) . "
                                </td>
                                <td class='DTD-del'>
                                    <input type='hidden' name='multifile_id[{$field_name->name}][]' value=''/>
                                    <input type='hidden' name='multifile_upload_index[{$field_name->name}][]' value=''/>
                                    <input type='hidden' name='multifile_delete[{$field_name->name}][]' value='0'/>
                                    " . bc_checkbox("multi_delete", 0, "удалить", "", "", 0, "mark") . "
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class='multi-inp multi-file'>" . bc_file("f_{$field_name->name}_file[]", '', "", "", "multi", '', "accept='{$accept}'") . "</div>";
    }
    $result = "<div class='input-photos'>
					<div class='photos-name'>{$name}</div>
					<div class='photos-body'>{$result}</div>
				</div>";
    return $result;
}

# изменение объекта: цены в разных городах
function nc_city_prices($pricecity = '')
{
    global $db, $catalogue, $setting;
    foreach ($setting['lists_targetcity'] as $trgID => $trg) {
        $citypricefield .= "<div class='multi-line'>
						        <div class='multi-inp'>" . bc_text($trg['name']) . "</div>
						        <div class='multi-inp'>" . bc_input("pricecity[{$trgID}][price]", $pricecity[$trgID][price], "Цена") . "</div>
						        <div class='multi-inp'>" . bc_input("pricecity[{$trgID}][count]", $pricecity[$trgID][count], "Количество") . "</div>
							</div>";
    }
    $citypricefield = "<div class='colblock colblock-cityprice'>
							<h4>Цены по городам</h4>
							{$citypricefield}
						</div>";
    return $citypricefield;
}

# выбор даты (переписанная)
function bc_date($setkey, $setvalue = '', $title = '', $time = 0, $calendar = 0, $req = 0)
{
    $day = $month = $year = $hours = $minutes = $seconds = "";
    if ($setvalue == '0000-00-00 00:00:00') {
        $setvalue = '';
    }
    if ($setvalue) {
        $d = new DateTime($setvalue);
        $day = $d->format('d');
        $month = $d->format('m');
        $year = $d->format('Y');
        $hours = $d->format('H');
        $minutes = $d->format('i');
        $seconds = $d->format('s');
    }

    if ($title) {
        $result .= "<div class='multi-inp multi-text'>" . bc_text($title, "", $req) . "</div>";
    }
    $result .= "<div class='multi-inp'>" . bc_input($setkey . "_day", $day, "День", "maxlength='2'") . "</div>";
    $result .= "<div class='multi-inp multi-element'>" . bc_text("-") . "</div>";
    $result .= "<div class='multi-inp'>" . bc_input($setkey . "_month", $month, "Месяц", "maxlength='2'") . "</div>";
    $result .= "<div class='multi-inp multi-element'>" . bc_text("-") . "</div>";
    $result .= "<div class='multi-inp multi-year'>" . bc_input($setkey . "_year", $year, "Год", "maxlength='4'") . "</div>";

    if ($time || $calendar) {
        $calendarHtml = "";
        if ($calendar) {
            $calendarHtml = "<div class='datapicker'><input type='text' " . ($time ? "data-timepicker='true'" : "") . " value='" . ($setvalue ? $setvalue : date("Y-m-d H:i:s")) . "'></div>";
        }
        $result .= "<div class='multi-inp multi-calendar'>{$calendarHtml}</div>";
    }

    if ($time) {
        $result .= "<div class='multi-inp'>" . bc_input($setkey . "_hours", $hours, "Час", "maxlength='2'") . "</div>";
        $result .= "<div class='multi-inp multi-element'>" . bc_text(":") . "</div>";
        $result .= "<div class='multi-inp'>" . bc_input($setkey . "_minutes", $minutes, "Минут", "maxlength='2'") . "</div>";
        $result .= "<div class='multi-inp multi-element'>" . bc_text(":") . "</div>";
        $result .= "<div class='multi-inp'>" . bc_input($setkey . "_seconds", $seconds, "Секунд", "maxlength='4'") . "</div>";
    }

    return "<div class='multi-line multi-date date-calendar'>{$result}</div>";
}



# TEXT (обычный)
function bc_text_standart($text)
{
    $class[] = "text-field-standart";

    if ($text) {
        $str = "<div class='" . implode(" ", $class) . "'>
							<label class='field-title'>&nbsp;</label>
							<div class='text-field'>{$text}</div>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}
# LABEL (обычный)
function bc_label_standart($title = '', $attrs = '', $req = 0)
{
    if ($title) {
        $str = "<label class='field-title' {$attrs}>{$title}" . ($req ? "<span class='red'>*</span>" : null) . "</label>";
    } else {
        $str = "";
    }
    return $str;
}
# INPUT
function bc_input_standart($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    $class[] = "input-field-standart";
    $class[] = stristr($attrs, 'data-oneline') ? "input-oneline" : "";

    if ($setkey) {
        if (!stristr($attrs, 'type=')) {
            $type = "type='" . (stristr($setkey, 'pass') || stristr($setkey, 'PW') ? 'password' : 'text') . "'";
        }
        $str = "<div class='" . implode(" ", $class) . "'>
					<label class='field-title " . ($setvalue || is_numeric($setvalue) ? "active" : "") . "'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</label>
					<input {$type} name='{$setkey}' value='{$setvalue}' {$attrs}>
					" . (stristr($attrs, 'data-eye') ? "<span class='eye'></span>" : null) . "
				</div>";
    } else {
        $str = "";
    }
    return $str;
}
# TEXTAREA
function bc_textarea_standart($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    $class[] = "input-field-standart";
    $class[] = stristr($attrs, 'data-oneline') ? "input-oneline" : "";

    if ($setkey) {
        $str = "<div class='" . implode(" ", $class) . "'>
							<label class='field-title'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</label>
							<textarea cols=501 rows=6 name='{$setkey}' value='{$setvalue}' {$attrs}>{$setvalue}</textarea>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}
# SELECT
function bc_select_standart($setkey, $setvalue = '', $title = '', $attrs = '', $req = '')
{
    global $HTTP_FILES_PATH;
    $class[] = "input-field-standart";
    $class[] = stristr($attrs, 'data-oneline') ? "input-oneline" : "";


    if ($setkey) {
        $str = "<div class='" . implode(" ", $class) . "'>
							" . ($title ? "<label class='field-title'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</label>" : "") . "
							" . (stristr($setvalue, '<select') ? $setvalue : "<select name='{$setkey}' {$attrs}>{$setvalue}</select>") . "
						</div>";
    } else {
        $str = "";
    }
    return $str;
}
# CHECKBOX
function bc_checkbox_standart($setkey, $setvalue = '', $title = '', $checked = false, $attrs = '', $req = 0)
{
    if ($setkey) {
        $str = "<div class='chb-standart'>
							<label>
								<input type='checkbox' {$attrs} value='{$setvalue}' " . ($checked ? "checked" : null) . " name='{$setkey}'>
								<span class='chb-lever" . ($type ? "-" . $type : "") . "'></span>
								" . ($title ? "<span class='chb-text'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</span>" : null) . "
							</label>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}
# RADIO
function bc_radio_standart($setkey, $setvalue = '', $title = '', $checked = false, $attr = "")
{
    if ($setkey) {
        $str = "<div class='radio-standart' {$attr}>
							<label>
								<input name='{$setkey}' type='radio' value='{$setvalue}' " . ($checked ? "checked" : null) . ">
								<span class='rdo-st'></span>
								<span class='rdo-name'>{$title}</span>
							</label>
						</div>";
    } else {
        $str = "";
    }
    return $str;
}
# FILE
function bc_file_standart($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    $class[] = "file-standart";
    $class[] = stristr($attrs, 'data-oneline') ? "input-oneline" : "";

    if ($setkey) {
        $str = "<div class='" . implode(" ", $class) . "'>
					<label class='field-title active'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</label>
					<div class='file-field'>
						<input name='{$setkey}' value='{$setvalue}' type='file'>
					</div>
				</div>";
    } else {
        $str = "";
    }
    return $str;
}
# DATE
function bc_date_standart($setkey, $setvalue = '', $title = '', $attrs = '', $req = 0)
{
    $class[] = "date-standart";

    if ($setkey) {
        $str = "<div class='" . implode(" ", $class) . "'>
					<label class='field-title active'>{$title}" . ($req ? "<div class='red'>*</div>" : null) . "</label>
					<input name='{$setkey}' value='{$setvalue}' type='date'>
				</div>";
    } else {
        $str = "";
    }
    return $str;
}






// опеределить картинка или нет
function is_image($filename)
{
    $is = @getimagesize($filename);
    if (!$is) {
        return false;
    } elseif (!in_array($is[2], array(1, 2, 3))) {
        return false;
    } else {
        return true;
    }
}

//Время работы
// ================================================
function time_input($setkey, $title, $checked = '', $val = '')
{
    if ($setkey !== '') {
        $str = "<div class='time-work-standart'>
							<label>
								<input type='checkbox'  value='{$setkey}'  name='f_time[day][checked][{$setkey}]' " . ($checked ? "checked" : null) . " >
								<span class='chb-text'>{$title}</span>
							</label>
						<input type='text' class='time' value='{$val}' name='f_time[day][value][{$setkey}]'>
					</div>";
    } else {
        $str = "";
    }

    return $str;
}

function nc_time_work($time)
{
    global $time_days;
    // Проверка для строки
    $timeArr = orderArray($time);
    $isJson = (json_last_error() === JSON_ERROR_NONE ? true : false);
    $text = str_replace('\\n', "\n", ($isJson ? $timeArr['text'] : $time));
    foreach ($time_days as $key => $day) {
        $chek = ($isJson && isset($timeArr['day']["checked"]["$key"]) ? true : false);
        $val = ($isJson ? $timeArr['day']["value"]["$key"]  : '');
        $inputTime .= time_input($key, $day, $chek, $val);
    }
    $html = "<div class='box-time-work colline colline-2 colline-height'>
				<div class='time-work-standart none-time'>
					<label>
						<input type='radio'  value='none'  name='f_time[type]' " . ($timeArr['type'] == 'none' ? 'checked' : null) . ">
						<span class='chb-text'>Выключить</span>
					</label>
				</div>
				<div class='time-work-standart all-day'>
					<label>
						<input type='radio'  value='All'  name='f_time[type]' " . ($timeArr['type'] == 'All' ? 'checked' : null) . ">
						<span class='chb-text'>Каждый день</span>
					</label>
					<input type='text' class='time' value='{$timeArr['All']['value']}' name='f_time[All][value]'>
				</div>
				<div class='time-work-standart days-week'>
					<label>
						<input type='radio'  value='days'  name='f_time[type]' " . ($timeArr['type'] == 'days' ? 'checked' : null) . ">
						<span class='chb-text'>По дням недели</span>
					</label>
					<div class='box-days-week' " . ($timeArr['type'] == 'days' ? '' : "style='visibility: hidden'") . ">
						" . $inputTime . "
					</div>
				</div>

			</div><div class='colline colline-2 colline-height'>" . bc_textarea('f_time[text]', $text, 'Альтернативный текст времени') . "</div>";
    return $html;
}

/**
 * Получения списка форм
 *
 * @param string $setkey Ключь в MySQL
 * @param string $valueInput Значения в MySQL
 *
 * @param array $option
 * @param array $option[name] Наименованя для списка, стандартое значения "Формы для вывода"
 * @param array $option[class] Класc для списка, стандартое значения "ns"
 *
 * @return string
 *
 */

function bc_selectionForm($setkey, $valueInput, $option = array())
{

    global $db, $catalogue;

    $name = $option['name'] ? $option['name'] : 'Формы для вывода';
    $class = $option['class'] ? $option['class'] : 'ns';

    $allForm = $db->get_results("   SELECT  Sub_Class_ID as cc,
                                            Subdivision_ID as sub,
                                            Class_ID as id,
                                            Sub_Class_Name as name

                                    FROM    `Sub_Class`

                                    WHERE   `Class_ID` IN (2013,197) AND `Catalogue_ID` = {$catalogue}

                                    UNION

                                    SELECT  Sub_Class_ID as cc,
                                            Subdivision_ID as sub,
                                            Message_ID as id,
                                            name

                                    FROM    `Message2059`

                                    WHERE   `Catalogue_ID` = {$catalogue}", ARRAY_A);

    if ($allForm) {
        $opt = "<option value=''>- выбрать -</option>";
        foreach ($allForm as $form) {
            // если это стандартная форма
            switch ($form['id']) {
                case 197:
                    $ttcpl = '&nc_ctpl=2254';
                    $id = $form['id'];
                    break;
                case 2013:
                    $ttcpl = '&nc_ctpl=2256';
                    $id = $form['id'];
                    break;
                default:
                    $ttcpl = '&nc_ctpl=2059&msg=' . $form['id'];
                    $id = "2059/" . $form['id'];
                    break;
            }
            $value = $form['sub'] . '/' . $form['cc'] . '/' . $ttcpl . '/' . $id;
            $opt .= "<option data-id='{$valueInput}' value='{$value}' " . ($valueInput == $value ? 'selected' : '') . ">{$form['id']} {$form['name']}</option>";
        }
        $htmlForm = bc_select($setkey, $opt, $name, "class='{$class}'");
    }
    return $htmlForm;
}
// ================================================

function bc_getChildSub($subs, $paretn = 0)
{
    $list = [];
    foreach ($subs as $sb) {
        if ($sb['par'] == $paretn) {
            $list[$sb['sub']] = [
                'name' => $sb['name'],
                'full_name' => $sb['full_name'],
                'subs' => bc_getChildSub($subs, $sb['sub'])
            ];
        }
    }
    return $list;
}

function bc_getSubFullName($subs)
{
    foreach ($subs as $sid => $ar) {
        $full_name = array($ar['name']);
        $parid = $ar['par'];
        while ($parid > 0) {
            $par_item = $subs[$parid];
            $parid = $par_item['par'];
            if ($parid) {
                $full_name[] = $par_item['name'];
            }
        }
        $full_name = array_reverse($full_name);
        $subs[$sid]['full_name'] = implode(" -> ", $full_name);
    }
    return $subs;
}

function getListSubsCheckbox($array, $active, $key)
{
    $html = "";
    foreach ($array as $id => $ar) {
        $html .= "<div class='sub-item' data-name='" . mb_strtolower($ar['name']) . "' data-fullname='{$ar['full_name']}'>";
        $html .= bc_checkbox("{$key}[]", $id, $ar['name'], in_array($id, $active));
        if ($ar['subs']) {
            $html .= "<div class='sub-item-child'>" . getListSubsCheckbox($ar['subs'], $active, $key) . "</div>";
        }
        $html .= "</div>";
    }
    return $html;
}

function bc_listSubsCheckbox($name, $key, $selectSubsId = [], $classId = '')
{
    $subs = bc_getSubFullName(arrayValuesKeyA(get_subs_class($classId), 'sub'));
    $subsChild = bc_getChildSub($subs);
    $selected_list = "";
    foreach ($selectSubsId as $id_sub) {
        if ($subs[$id_sub]) {
            $selected_list .= "<div class='selected-item' data-id='{$id_sub}'>{$subs[$id_sub]['full_name']}</div>";
        }
    }
    return "<label>{$name}</label>
    <div class='selected-list'>{$selected_list}</div>
    <div class='sub-item-main'><input type='text' name='srch' placeholder='Поиск разделов'>
    <!--<div class='check-all'>" . bc_checkbox("check-all", 0, 'Включить всё', 0) . "</div>-->
    <div class='sub-item-items'>" . getListSubsCheckbox($subsChild, $selectSubsId, $key) . "</div></div>";
}

function bc_button_a($name, $params = [])
{
    return "<a " . implode(' ', $params) . " href='javascript:void(0);'>
                <span>{$name}</span>
            </a>";
}


function siteMapExtendetList($inputSort, $chekedInputs)
{
    $html = '<ul>';
    $currentLvl = null;
    $checkedExpendet = null;
    foreach ($inputSort as $id => $value) {

        if ($currentLvl !== null &&  $value['lvl'] > $currentLvl) {
            $disabled = ($checkedExpendet ? 'disabled' : null);
        } else {
            $disabled = null;
            $checkedExpendet = null;
            $currentLvl = null;
        }

        if (isset($chekedInputs[$id])) {
            $currentLvl = $value['lvl'];
            $checked = 'checked';
            $checkedExpendet = ($chekedInputs[$id] ? 'checked' : null);
        } else {
            $checked = '';
        }

        $disabledExpendet = (!$checked || $disabled ? 'disabled' : null);

        $html .= "<li data-id='{$id}' class='lvl-{$value['lvl']} li-map " . ($disabled ? 'expendet' : null) . "' data-lvl='{$value['lvl']}'>
                <div>
                    <div class='switch'>
                        <label>
                            <input type='checkbox' name='map[{$id}][sub]' {$checked} {$disabled}>
                            <span class='lever'></span>
                            <span class='sw-text'>{$id} {$value['name']}</span>
                        </label>
                    </div>
                </div>
                
                <div class='switch-expendet {$checkedExpendet}'>
                    <label title='Наследовать'>
                        <input type='checkbox' name='map[{$id}][expendet]' {$checkedExpendet} {$disabledExpendet}>
                        <span class='lever'></span>
                    </label>
                </div>
            </li>";
    }
    $html .= '</ul>';

    return $html;
}
