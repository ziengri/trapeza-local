<?php

# Фильтр
class Class2041
{
    public $classID = 2041; # № компонента
    public $itemsClassID = 2001; # № компонента из которого собирать значения    
    /**
     * microtime
     *
     * @var int
     */
    public $microtime = 0;

    /**
     * filter id
     *
     * @var int
     */
    public $id;

    /**
     * catalogue
     *
     * @var int
     */
    public $catalogue;

    /**
     * params
     *
     * @var array|string
     */
    public $params;

    public function __construct($id, $catalogue, $params = array())
    {
        $this->microtime = microtime(true);
        $this->id = (int)$id;
        $this->catalogue = (int)$catalogue;
        $this->params = securityForm($params);
    }
    # html фильтра
    public function getFilter($type = 2242)
    {
        if (function_exists('class2041_getFilter')) {
            return class2041_getFilter($this, $type); // своя функция
        } else {
            global $AUTH_USER_ID;
            
            $fields = $this->fields;
            $data = $this->fltDB['data'];
            $fltStruct = array();
            $structPriority = 5000;
            $hideInputs = "<input type=hidden name='filter' value='1'>
                        " . ($this->fltDB['one_sub_mode'] ? "<input type='hidden' name='one_sub_mode' value='1'/>" : null) . "
                        " . ($this->nc_ctpl ? "<input type='hidden' name='nc_ctpl' value='{$this->nc_ctpl}'/>" : null) . "
                        " . ($this->recNum ? "<input type=hidden name='recNum' value='{$this->recNum}'>" : null) . "
                        " . ($this->sort ? "<input type=hidden name='sort' value='{$this->sort}'>" : null) . "
                        " . ($this->desc ? "<input type=hidden name='desc' value='{$this->desc}'>" : null) . "
                        " . ($this->find ? "<input type='hidden' name='find' value='{$this->find}'/>" : null) . "
                        " . ($this->r ? "<input type='hidden' name='r' value='{$this->r}'/>" : null) . "
                        " . ($this->insub || $this->sub['classID'] == 2001 ? "<input type=hidden name='subr' value='" . ($this->insub ? $this->insub : ($this->sub['engName'] != 'search' ? $this->sub['id'] : $this->subr)) . "'>" : null);

            if ($this->fltDB['live_count']) {
                $itemCount = $this->checkFilterVal ? $this->getItemCount() : null;
            }
            
            switch ($type) {
                case 2243: # Горизонтальный
                
                    if (is_array($this->values['main']['values'])) {
                        foreach ($this->values['main']['values'] as $fID => $values) {
                            $fName = $fields[$fID]['name'];
                            $classificatorList = $fields[$fID]['TypeOfData_ID'] == 4 ? getClassificator($fields[$fID]['Format']) : array();
                            $html = $podrob_title_class = $podrob_body_class = '';

                            if ($data['minimized'][$fID] && !$_GET['flt'][$fields[$fID]['name']] && !$_GET['flt1'][$fields[$fID]['name']][0] && !$_GET['flt1'][$fields[$fID]['name']][1]) { # свернутый изначально
                                $podrob_title_class = "js-acord-none";
                                $podrob_body_class = "none";
                            }

                            if (isset($data['otdo'][$fID])) { # от-до
                                $valMin = strip_tags($_GET['flt1'][$fName][0]);
                                $valMax = strip_tags($_GET['flt1'][$fName][1]);
                                $priority = $data['priority'][$fID] ? $data['priority'][$fID] : $structPriority;

                                $fltStruct[$priority] .= "<div class='filter_m_item cb filter-main-slider'>
                                                            " . ($this->fltDB['namefieldbool'] ? "<div class='filter_m_title'>{$data['name'][$fID]}:</div><div class='filter_m_bodyt'>" : null) . "
                                                                <input type='text' id='filter_sld_start' name='flt1[{$fName}][0]' value='{$valMin}' data-number='' data-def='{$values['min']}'>
                                                                <input type='text' id='filter_sld_end' name='flt1[{$fName}][1]' value='{$valMax}' data-number='' data-def='{$values['max']}'>
                                                                <input type='text' class='filter_sld' data-start='{$values['min']}' data-end='{$values['max']}' data-cur1='{$valMin}' data-cur2='{$valMax}'>
                                                            " . ($this->fltDB['namefieldbool'] ? "</div>" : null) . "
                                                        </div>";
                            } else {
                                switch ($data['view'][$fID]) {
                                    case 1: # вариант в списке
                                        $options = '';
                                        foreach ($values as $val) {
                                            $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                            $options .= "<option " . ($_GET['flt'][$fName] && $val == urldecode($_GET['flt'][$fName]) ? "selected" : null) . " value='" . urlencode($val) . "'>{$title}</option>";
                                        }
                                        $html = "<select name='flt[{$fName}]' class='select-style'><option value=''>- выберите -</option>{$options}</select>";
                                        break;
                                    case 2: # вариант галочками
                                        $chekboxes = '';
                                        $checkValues = is_array($_GET['flt'][$fName]) ? array_flip($_GET['flt'][$fName]) : array();
                                        asort($values);
                                        foreach ($values as $val) {
                                            $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                            $chekboxes .= "<div class='podbor_dch'>" . bc_checkbox_standart("flt[{$fName}][]", urlencode($val), $title, isset($checkValues[urlencode($val)])) . "</div>";
                                        }
                                        $html = "<div class='podbor_checkb cb'>{$chekboxes}</div>";
                                        break;
                                    case 3: # да/нет
                                        $html = "<div class='podbor_checkb scrollbar-inner'>
                                                        <div class='podbor_dch '>" . bc_checkbox_standart("flt3[{$fName}]", 1, "да", $_GET['flt3'][$fName]) . "</div>
                                                    </div>";
                                        break;
                                    default:
                                        break;
                                }
                                if ($html) {
                                    $priority = $data['priority'][$fID] ? $data['priority'][$fID] : $structPriority;
                                    $fltStruct[$priority] .= "<div class='filter_m_item cb'>
                                                                " . ($this->fltDB['namefieldbool'] ? "<div class='filter_m_title'>{$data['name'][$fID]}:</div><div class='filter_m_bodyt'>" : null) . "
                                                                    {$html}
                                                                " . ($this->fltDB['namefieldbool'] ? "</div>" : null) . "
                                                            </div>";
                                }
                            }
                        }
                    }
                    if (is_array($this->values['custom']['values'])) {
                        foreach ($this->values['custom']['values'] as $pName => $values) {
                            $html = '';
                            switch ($this->customFields[$pName]['filter_type']) {
                                case '1': # вариант в списке
                                    $options = '';
                                    foreach ($values as $val) {
                                        $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                        $options .= "<option " . ($_GET['flt']['params'][$pName]['select'] && $val == urldecode($_GET['flt']['params'][$pName]['select']) ? "selected" : null) . " value='" . urlencode($val) . "'>{$title}</option>";
                                    }
                                    $html = "<select name='flt[params][{$pName}][select]' class='select-style'><option value=''>- выберите -</option>{$options}</select>";
                                    break;

                                case '3': # да/нет
                                    $html = "<div class='podbor_checkb scrollbar-inner'>
                                        <div class='podbor_dch '>" . bc_checkbox_standart("flt3[params][{$pName}]", 1, "да", $_GET['flt3']['params'][$pName]) . "</div>
                                    </div>";
                                    break;
                                case '4': # от-до
                                    $valMin = strip_tags($_GET['flt']['params'][$pName][0]);
                                    $valMax = strip_tags($_GET['flt']['params'][$pName][1]);
                                    $priority = $this->customFields[$pName]['priority'] ? $this->customFields[$pName]['priority'] : $structPriority;

                                    $fltStruct[$priority] = "<div class='filter_m_item cb filter-main-slider'>
                                                                " . ($this->fltDB['namefieldbool'] ? "<div class='filter_m_title'>{$this->customFields[$pName]['name']}:</div><div class='filter_m_bodyt'>" : null) . "
                                                                    <input type='hidden'  name='flt[params_range][{$pName}]' value='" . (implode('_', $values['all'])) . "'>
                                                                    <input type='text' id='filter_sld_start' name='flt[params][{$pName}][0]' value='{$valMin}' data-number='' data-def='{$values['min']}'>
                                                                    <input type='text' id='filter_sld_end' name='flt[params][{$pName}][1]' value='{$valMax}' data-number='' data-def='{$values['max']}'>
                                                                    <input type='text' class='filter_sld' data-start='{$values['min']}' data-end='{$values['max']}' data-cur1='{$valMin}' data-cur2='{$valMax}'>
                                                                " . ($this->fltDB['namefieldbool'] ? "</div>" : null) . "
                                                            </div>";
                                    break;
                                case '2': # вариант галочками
                                default:
                                    $chekboxes = '';
                                    foreach ($values as $val) {
                                        $chekboxes .= "<div class='podbor_dch'>" . bc_checkbox_standart("flt[params][{$pName}][{$val}]", urlencode($val), $val, isset($_GET['flt']['params'][$pName][urlencode($val)])) . "</div>";
                                    }
                                    $html = "<div class='podbor_checkb cb'>{$chekboxes}</div>";
                                    break;
                            }

                            if ($html) {
                                $priority = $this->customFields[$pName]['priority'] ? $this->customFields[$pName]['priority'] : $structPriority;
                                $fltStruct[$priority] .= "<div class='filter_m_item cb'>
                                                            " . ($this->fltDB['namefieldbool'] ? "<div class='filter_m_title'>{$this->customFields[$pName]['name']}:</div><div class='filter_m_bodyt'>" : null) . "
                                                                {$html}
                                                            " . ($this->fltDB['namefieldbool'] ? "</div>" : null) . "
                                                            </div>";
                            }
                        }
                    }

                    $fltClasses = 'filter-form';
                    if (!count($fltStruct)) {
                        $fltStruct[] = "<div class='podbor_dch filter-not-have'>В фильтре<br>нет подходящих значений {$subfind}</div>";
                        $fltClasses .= ' no-values';
                    } else {
                        ksort($fltStruct);
                        $fltClasses .= ' ' . ($this->checkFilterVal ? null : 'no-') . 'selected';
                        if ($this->fltDB['live_count']) {
                            $fltClasses .= ' live-count';
                        }
                        if ($this->fltDB['ajax_filter']) {
                            $fltClasses .= ' ajax-filter';
                        }
                    }

                    $filterHtml = "<div class='filter_m_body podbor_tovarov obj obj{$this->id}" . ($this->fltDB['file_newline'] ? ' filter_m_newline' : null) . "'>
                                        <form action='" . ($this->searchInCur ? $this->cururl : "/search/") . "' class='{$fltClasses}'>
                                            {$hideInputs}
                                            " . implode('', $fltStruct) . "
                                            <div class='filter_m_item cb'><input type='submit' value=''>
                                                <a href='#' class='podbor_add button_green btn-strt-a'>
                                                    <span class='btn_green_1 icons i_check'></span>
                                                    <span class='btn_green_2'><!--<span class='btn_green_num'>54</span>-->" . ($this->fltDB['button'] ? $this->fltDB['button'] : 'Применить') . "
                                                    " . ($this->fltDB['live_count'] ? " <span class='live-count-val'>{$itemCount}</span>" : null) . "
                                                    </span>
                                                </a>
                                            </div>
                                        </form>
                                        {$this->editLink}
                                    </div>";

                    if ($this->fltDB['hide_bigfilter']) {
                        $filterHtml = "<div class='filter_m_hide'>
                                            {$filterHtml}
                                            <div class='filter_m_hide_footer'>
                                                <div class='filter_m_tx'>
                                                    <div class='f_m_hide'>Показать фильтр</div>
                                                    <div class='f_m_show'>Скрыть фильтр</div>
                                                </div>
                                            </div>
                                        </div>";
                    }
                    return $filterHtml;
                    break;
                case 2242: # Вертикальный
                default:
                    
                    if (is_array($this->values['main']['values'])) {
                        foreach ($this->values['main']['values'] as $fID => $values) {
                            $fName = $fields[$fID]['name'];
                            $classificatorList = $fields[$fID]['TypeOfData_ID'] == 4 ? getClassificator($fields[$fID]['Format']) : array();
                            $html = $podrob_title_class = $podrob_body_class = '';

                            if ($data['minimized'][$fID] && !$_GET['flt'][$fields[$fID]['name']] && !$_GET['flt1'][$fields[$fID]['name']][0] && !$_GET['flt1'][$fields[$fID]['name']][1]) { # свернутый изначально
                                $podrob_title_class = "js-acord-none";
                                $podrob_body_class = "none";
                            }

                            if (isset($data['otdo'][$fID])) { # от-до
                                $valMin = strip_tags($_GET['flt1'][$fName][0]);
                                $valMax = strip_tags($_GET['flt1'][$fName][1]);
                                $priority = $data['priority'][$fID] ? $data['priority'][$fID] : $structPriority;

                                $fltStruct[$priority] .= "<div class='podbor_block podbor_block_price' data-filterid='{$fID}'>
                                                            <div class='podrob_title js-acord-head {$podrob_title_class}'>
                                                                <div class='podbor_name'>{$data['name'][$fID]}" . ($data['name'][$fID] == "Цена" ? " " . $currency['html'] : "") . "</div>
                                                            </div>
                                                            <div class='podrob_body js-acord-body slider-blue {$podrob_body_class}'>
                                                                <div class='podbor_p_inp hov'>
                                                                    <div class='p_p_inp p_p_inp_1'>
                                                                        <input type='text' class='inp_slider_start' name='flt1[{$fName}][0]' value='{$valMin}' data-number='' data-def='{$values['min']}' placeholder1='{$values['min']}'>
                                                                        <span class='clear_inpsl'>✕</span>
                                                                    </div>
                                                                    <div class='p_p_inp p_p_inp_2'>
                                                                        <input type='text' class='inp_slider_end' name='flt1[{$fName}][1]' value='{$valMax}' data-number='' data-def='{$values['max']}' placeholder1='{$values['max']}'>
                                                                        <span class='clear_inpsl'>✕</span>
                                                                    </div>
                                                                </div>
                                                                <div class='p_p_slider'>
                                                                    <input type='text' class='inp_slider' data-start='{$values['min']}' data-end='{$values['max']}' data-cur1='{$valMin}' data-cur2='{$valMax}'>
                                                                </div>
                                                            </div>
                                                        </div>";
                            } else {
                                global $AUTH_USER_ID;
                                switch ($data['view'][$fID]) {
                                    case 1: # вариант в списке
                                        $options = '';

                                        foreach ($values as $val) {
                                            $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                            $options .= "<option " . ($_GET['flt'][$fName] && $val == urldecode($_GET['flt'][$fName]) ? "selected" : null) . " value='" . urlencode($val) . "'>{$title}</option>";
                                        }
                                        $html = "<select name='flt[{$fName}]' class='select-style'><option value=''>- выберите -</option>{$options}</select>";
                                        break;
                                    case 2: # вариант галочками
                                        $chekboxes = '';
                                        $checkValues = is_array($_GET['flt'][$fName]) ? array_flip($_GET['flt'][$fName]) : array();
                                        asort($values);

                                        foreach ($values as $val) {
                                            $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                            $chekboxes .= "<div class='podbor_dch'>" . bc_checkbox_standart("flt[{$fName}][]", urlencode($val), $title . " <span class='ch-num'>(<span class='ch-n'>{$this->values['main']['count'][$fID][$val]}</span>)</span>", isset($checkValues[urlencode($val)])) . "</div>";
                                        }
                                        $html = "<div class='podbor_checkb cb'>{$chekboxes}</div>";
                                        break;
                                    case 3: # да/нет
                                        $html = "<div class='podbor_checkb scrollbar-inner'>
                                                        <div class='podbor_dch '>" . bc_checkbox_standart("flt3[{$fName}]", 1, "да", $_GET['flt3'][$fName]) . "</div>
                                                    </div>";
                                        break;
                                    default:
                                        break;
                                }
                                if ($html) {
                                    $priority = $data['priority'][$fID] ? $data['priority'][$fID] : $structPriority;
                                    $fltStruct[$priority] .= "<div class='podbor_block pd_close " . encodestring($data['name'][$fID], 1) . "'>
                                                                <div class='podrob_title js-acord-head {$podrob_title_class}'>
                                                                    <div class='podbor_name'>{$data['name'][$fID]} " . ($data['view'][$fID] && $data['view'][$fID] < 3 ? "<a href='' class='clear_filter fr'>Очистить</a>" : null) . "</div>
                                                                </div>
                                                                <div class='podrob_body js-acord-body {$podrob_body_class}'>
                                                                    {$html}
                                                                </div>
                                                            </div>";
                                }
                            }
                        }
                    }
                    
                    // доп параметры товара
                    if (is_array($this->values['custom']['values'])) {
                        foreach ($this->values['custom']['values'] as $pName => $values) {
                            $html = $podrob_title_class = $podrob_body_class = '';

                            if ($this->customFields[$pName]['minimized'] && !$_GET['flt']['params'][$pName]) { # свернутый изначально
                                $podrob_title_class = "js-acord-none";
                                $podrob_body_class = "none";
                            }
                            $priority++;

                            switch ($this->customFields[$pName]['filter_type']) {
                                case '1': # вариант в списке
                                    $options = '';
                                    asort($values);
                                    foreach ($values as $val) {
                                        $title = $fields[$fID]['TypeOfData_ID'] == 4 && $classificatorList[$val] ? $classificatorList[$val] : $val;
                                        $options .= "<option " . ($_GET['flt']['params'][$pName]['select'] && $val == urldecode($_GET['flt']['params'][$pName]['select']) ? "selected" : null) . " value='" . urlencode($val) . "'>{$title}</option>";
                                    }
                                    $html = "<select name='flt[params][{$pName}][select]' class='select-style'><option value=''>- выберите -</option>{$options}</select>";
                                    break;

                                case '3': # да/нет
                                    $html = "<div class='podbor_checkb scrollbar-inner'>
                                        <div class='podbor_dch '>" . bc_checkbox_standart("flt3[params][{$pName}]", 1, "да", $_GET['flt3']['params'][$pName]) . "</div>
                                    </div>";
                                    break;
                                case '4': # от-до
                                    $valMin = strip_tags($_GET['flt']['params'][$pName][0]);
                                    $valMax = strip_tags($_GET['flt']['params'][$pName][1]);
                                    $html = "<div class='podrob_body js-acord-body slider-blue {$podrob_body_class}'>
                                                <input type='hidden' name='flt[params_range][{$pName}]' value='" . (implode('_', $values['all'])) . "'>
                                                <div class='podbor_p_inp hov'>
                                                    <div class='p_p_inp p_p_inp_1'>
                                                        <input type='text' class='inp_slider_start' name='flt[params][{$pName}][0]' value='{$valMin}' data-number='' data-def='{$values['min']}' placeholder='{$values['min']}'>
                                                        <span class='clear_inpsl'>✕</span>
                                                    </div>
                                                    <div class='p_p_inp p_p_inp_2'>
                                                        <input type='text' class='inp_slider_end' name='flt[params][{$pName}][1]' value='{$valMax}' data-number='' data-def='{$values['max']}' placeholder='{$values['max']}'>
                                                        <span class='clear_inpsl'>✕</span>
                                                    </div>
                                                </div>
                                                <div class='p_p_slider'>
                                                    <input type='text' class='inp_slider' data-start='{$values['min']}' data-end='{$values['max']}' data-cur1='{$valMin}' data-cur2='{$valMax}'>
                                                </div>
                                            </div>";
                                    break;
                                case '2': # вариант галочками
                                default:
                                    $chekboxes = '';
                                    asort($values);
                                    foreach ($values as $val) {
                                        $chekboxes .= "<div class='podbor_dch'>" . bc_checkbox_standart("flt[params][{$pName}][{$val}]", urlencode($val), $val . " <span class='ch-num'>(<span class='ch-n'>{$this->values['custom']['count'][$pName][$val]}</span>)</span>", isset($_GET['flt']['params'][$pName][$val])) . "</div>";
                                    }
                                    $html = "<div class='podrob_body js-acord-body {$podrob_body_class}'>
                                                <div class='podbor_checkb cb'>{$chekboxes}</div>
                                            </div>";
                                    break;
                            }
                            if ($html) {
                                $priority = $this->customFields[$pName]['priority'] ? $this->customFields[$pName]['priority'] : $structPriority;
                                $fltStruct[$priority] .= "<div class='podbor_block pd_close params_{$pName}' data-filterid='{$pName}'>
                                                            <div class='podrob_title js-acord-head {$podrob_title_class}'>
                                                                <div class='podbor_name'>{$this->customFields[$pName]['name']}" . ($this->customFields[$pName]['name'] == "Цена" ? " " . $currency['html'] : "")
                                    . " " . ($this->customFields[$pName]['filter_type'] != 4 ? "<a href='' class='clear_filter fr'>Очистить</a>" : null) . "</div>
                                                            </div>
                                                            <div class='podrob_body js-acord-body {$podrob_body_class}'>
                                                                {$html}
                                                            </div>
                                                        </div>";
                            }
                        }
                    }

                    $fltClasses = 'filter-form';
                    if (!count($fltStruct)) {
                        $fltStruct[] = "<div class='podbor_dch filter-not-have'>В фильтре<br>нет подходящих значений {$subfind}</div>";
                        $fltClasses .= ' no-values';
                    } else {
                        ksort($fltStruct);
                        $fltClasses .= ' ' . ($this->checkFilterVal ? null : 'no-') . 'selected';
                        if ($this->fltDB['live_count']) {
                            $fltClasses .= ' live-count';
                        }
                        if ($this->fltDB['ajax_filter']) {
                            $fltClasses .= ' ajax-filter';
                        }
                    }

                    return "<div class='podbor_tovarov obj obj{$this->id}'>
                                <form action='" . ($this->searchInCur ? $this->cururl : "/search/") . "' class='{$fltClasses}'>
                                    {$hideInputs}
                                    " . implode('', $fltStruct) . "
                                    <div class='podbor_click'>
                                        <input type='submit' value=''>
                                        <a href='/' class='podbor_add podbor_input btn-strt-a'>
                                            <span class='podbor_add_t'>" . ($this->fltDB['button'] ? $this->fltDB['button'] : 'Применить') . "
                                            " . ($this->fltDB['live_count'] ? " <span class='live-count-val'>{$itemCount}</span>" : null) . "
                                            </span>
                                        </a>
                                        <a href='' class='clear_filter'>Сбросить</a>
                                    </div>
                                </form>
                            {$this->editLink}
                            </div>";
                    break;
            }
        }
    }
    # колиичество товаров попадающих под условие фильтра
    public function getItemCount()
    {
        global $setting;
        $queryWhere = $this->fltQuery ?  "({$this->queryWhere}) AND {$this->fltQuery}" : $this->queryWhere;
		


        if ($setting['groupItem']) {
            $result = $this->db->get_col("SELECT Message_ID FROM Message{$this->itemsClassID} WHERE {$queryWhere} GROUP BY name, Subdivision_ID");
        } else {
            $result =  $this->db->get_var("SELECT count(*) FROM Message{$this->itemsClassID} WHERE {$queryWhere}");
        }
		
        return $result ? (is_array($result) ? count($result) : $result) : 0;
    }

    public function setCheckFilterVal()
    {
        global $cityphone;

        $result = false;
        if (isset($_GET['flt'])) {
            $get = securityForm($_GET['flt']);
            foreach ($get as $fName => $fVal) {
                if (empty($fName) || $fName == 1 || $fk == 2) {
                    continue;
                }
                switch ($fName) {
                    case 'colors': # цвета
                        if (is_array($fVal)) {
                            foreach ($fVal as $color) {
                                if ($color) {
                                    $result = true;
                                }
                            }
                        } elseif ($fVal) {
                            $result = true;
                        }
                        break;
                    case 'params': # доп параметры
                        if (!is_array($fVal)) {
                            continue;
                        }
                        foreach ($fVal as $paramKey => $paramVal) {
                            if ($get['params_range'][$paramKey]) { # от-до доппараметров
                                foreach (explode("_", $get['params_range'][$paramKey]) as $val) {
                                    if ((float)$val > 0 && (float)$val >= (float)$paramVal[0] && (float)$val <= (float)$paramVal[1]) {
                                        $result = true;
                                    }
                                }
                            } else {
                                foreach ($paramVal as $val) {
                                    if ($val) {
                                        $result = true;
                                    }
                                }
                            }
                        }
                        break;
                    default: # обычные поля
                        if ($fName == "params_range") {
                            break;
                        }
                        if (is_array($fVal)) {
                            foreach ($fVal as $val) {
                                if (is_numeric($val) || !empty($val)) {
                                    $result = true;
                                }
                            }
                        } elseif (is_numeric($fVal) || !empty($fVal)) {
                            $result = true;
                        }
                        break;
                }
                if ($result) {
                    break;
                }
            }
        }
        if (!$result && isset($_GET['flt1'])) { # от - до
            $get = securityForm($_GET['flt1']);
            foreach ($get as $fName => $fVal) {
                if (is_numeric($fVal[0]) && $fVal[0] || is_numeric($fVal[1]) && $fVal[1]) {
                    $result = true;
                    break;
                }
            }
        }
        if (!$result && isset($_GET['flt3'])) { # да - нет
            $get = securityForm($_GET['flt3']);
            foreach ($get as $fName => $fVal) {
                if (!$fVal) {
                    continue;
                }
                if (stristr($fName, "stock")) {
                    if ($cityphone['sklad1c']) {
                        foreach (explode(",", $cityphone['sklad1c']) as $skl) {
                            if ($skl) {
                                $result = true;
                            }
                        }
                    } else {
                        $result = true;
                    }
                } else {
                    $result = true;
                }
                if ($result) {
                    break;
                }
            }
        }

        $this->checkFilterVal = $result;
    }

    public function setValues()
    {
        if (function_exists('class2041_setValues')) {
            return class2041_setValues($this); // своя функция
        } else {
            global $AUTH_USER_ID;
            
            $this->values = array();
            
            if (!is_array($this->items)) {
                return;
            }

            $data = $this->fltDB['data'];

            
            # заполняем значения
            foreach ($this->items as $item) {
                foreach ($data['checked'] as $fID) {
                    $fName = $this->fields[$fID]['name'];
                    $val = trim($item[$fName]);
                    $useArr = array();
                    if ($fName == 'colors') {
                        $colors = orderArray($val);
                        if (is_array($colors)) {
                            foreach ($colors as $color) {
                                if (!$color['name']) {
                                    continue;
                                }
                                $this->values['main']['values'][$fID][$color['name']] = true;
                                $this->values['main']['count'][$fID][$color['name']]++;
                            }
                        }
                    } elseif (isset($data['otdo'][$fID])) { # от-до

                        foreach (explode(';', $val) as $v) {
                            if (!$v) {
                                continue;
                            }
                            $v = str_replace(',', '.', trim($v));
                            if (!is_numeric($v)) {
                                continue;
                            }
                            if (!isset($this->values['main']['values'][$fID]) || $this->values['main']['values'][$fID]['min'] > $v) {
                                $this->values['main']['values'][$fID]['min'] = $v;
                            }
                            if (!isset($this->values['main']['values'][$fID]) || $this->values['main']['values'][$fID]['max'] < $v) {
                                $this->values['main']['values'][$fID]['max'] = $v;
                            }
                        }
                    } else {
                        if (!is_numeric($val) && empty($val)) {
                            continue;
                        }
                    
                        switch ($data['view'][$fID]) {
                            case 1: # вариант в списке
                            case 2: # вариант галочками
                                $val = htmlspecialchars_decode($val);
                                foreach (explode(';', $val) as $v) {
                                    if (!$v) continue;
                                    $v = htmlspecialchars($v);

                                    $this->values['main']['values'][$fID][$v] = true;
                                    if (!isset($useArr[$fID][$v])) {
                                        $this->values['main']['count'][$fID][$v]++;
                                        $useArr[$fID][$v] = true;
                                    }
                                }
                                break;
                            case 3: # да/нет
                                $this->values['main']['values'][$fID] = true;
                                break;
                            default:
                                break;
                        }
                    }
                }
                # дпоплнительный парамертры товаров включенные в фильтр
                if (count($this->customFields) && $item['params']) {
                    $paramArr = explode("\r\n", trim($item['params']));
                    if (count($paramArr)) {
                        foreach ($paramArr as $paramRow) {
                            $paramRow = explode("||", $paramRow);
                            if (!isset($this->customFields[$paramRow[0]])) {
                                continue;
                            }
                            $val = trim(str_replace('|', '', $paramRow[1]));
                            if (empty($val)) continue;
                            $useArr = [];
                            # от-до
                            if ($this->customFields[$paramRow[0]]['filter_type'] == 4) {
                                $val = str_replace(',', '.', $val);
                                if (!is_numeric($val)) continue;

                                if (!isset($this->values['custom']['values'][$paramRow[0]]) || $this->values['custom']['values'][$paramRow[0]]['min'] > $val) {
                                    $this->values['custom']['values'][$paramRow[0]]['min'] = $val;
                                }

                                if (!isset($this->values['custom']['values'][$paramRow[0]]) || $this->values['custom']['values'][$paramRow[0]]['max'] < $val) {
                                    $this->values['custom']['values'][$paramRow[0]]['max'] = $val;
                                }
                                $this->values['custom']['values'][$paramRow[0]]['all'][$val] = true;
                            } else {
                                // перебор нескольких значений в одно параметре
                                foreach (explode(';', $val) as $v) {
                                    $v = trim($v);
                                    if (!$v) {
                                        continue;
                                    }
                                    $this->values['custom']['values'][$paramRow[0]][$v] = true;
                                    if (!isset($useArr[$paramRow[0]][$v])) {
                                        $this->values['custom']['count'][$paramRow[0]][$v]++;
                                        $useArr[$paramRow[0]][$v] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            # обработка условий (основные)
            if (is_array($this->values['main']['values'])) {
                foreach ($this->values['main']['values'] as $fID => $values) {
                    $fName = $fields[$fID]['name'];
                    if (isset($data['otdo'][$fID])) {
                        # если запросы вернули 0, то удалить
                        if ($values['min'] == 0 && $values['max'] == 0) {
                            unset($this->values[$fID]);
                            continue;
                        }
                        # округление
                        if ($values['max'] <= 100) {
                            $this->values['main']['values'][$fID]['min'] = ceil($values['min']);
                            $this->values['main']['values'][$fID]['max'] = ceil($values['max']);
                        }
                    } else {
                        switch ($data['view'][$fID]) {
                            case 1: # вариант в списке
                            case 2: # вариант галочками
                                $this->values['main']['values'][$fID] = array();
                                foreach ($values as $val => $checked) {
                                    $this->values['main']['values'][$fID][] = $val;
                                }
                                fltSort($this->values['main']['values'][$fID]);
                                break;
                            case 3:
                                break;
                            default:
                                break;
                        }
                    }
                }
            }

            # обработка условий (params)
            if (is_array($this->values['custom']['values'])) {
                foreach ($this->values['custom']['values'] as $pName => $values) {
                    if ($this->customFields[$pName]['filter_type'] == 4) {
                        # если запросы вернули 0, то удалить
                        if ($values['min'] == 0 && $values['max'] == 0) {
                            unset($this->values['custom']['values'][$pName]);
                            continue;
                        }
                        # округление
                        $this->values['custom']['values'][$pName]['min'] = floor($values['min']);
                        $this->values['custom']['values'][$pName]['max'] = ceil($values['max']);

                        # переводим все уникальные значеня
                        $this->values['custom']['values'][$pName]['all'] = array();
                        foreach ($values['all'] as $val => $checked) {
                            $this->values['custom']['values'][$pName]['all'][] = $val;
                        }
                    } else {
                        $this->values['custom']['values'][$pName] = array();
                        foreach ($values as $val => $checked) {
                            $this->values['custom']['values'][$pName][] = $val;
                        }
                        fltSort($this->values['custom']['values'][$pName]);
                    }
                }
            }


        }
    }

    public function setItems()
    {
        global $AUTH_USER_ID;
        $fNames = $this->getFiledsNames();
        
        $this->items = $this->db->get_results("SELECT {$fNames} FROM Message{$this->itemsClassID} WHERE {$this->queryWhere}", ARRAY_A);
        //if ($_SERVER['REMOTE_ADDR']=='31.13.133.138') echo "Тест из офиса: SELECT {$fNames} FROM Message{$this->itemsClassID} WHERE {$this->queryWhere}";
        
    }

    public function setQueryWhere()
    {
		
        if (function_exists('class2041_setQueryWhere')) {
            $this->queryWhere = class2041_setQueryWhere($this); // своя функция
        } else {
            global $setting, $AUTH_USER_ID, $current_sub;
            $queryWhere = $subWhere = "";
            $subQuery = array();

            if ($this->sub['engName'] == 'search' && !empty($this->params['find'])) {
                $queryWhere = getFindQuery($this->find, $this->r, 0, '');
            } elseif ($this->fltDB['subs']) {
                $subQuery = $this->getSubQuery($this->fltDB['subs']);
            } elseif ($this->sub['classID'] == 2001) {
                $subQuery = $this->getSubQuery($this->params['subr'] ?? $this->sub['id']);
            }
            
            if ($subQuery['ids']) {
                $subWhere = "Subdivision_ID IN ({$subQuery['ids']})";
                if ($setting['itemMoreSub']) {
                    foreach (explode(',', $subQuery['ids']) as $val) {
                        $subWhere .= " OR Subdivision_IDS LIKE '%,{$val},%'";
                    }
                }
                $queryWhere .= ($queryWhere ? " AND " : null) . "({$subWhere})";
            }
			
			

            $IMPORTANT_WHERE = "Catalogue_ID = {$this->catalogue} AND Checked = 1";
            if ($queryWhere) {
                $queryWhere = "{$IMPORTANT_WHERE} AND {$queryWhere}";
            }

            if ($subQuery['subSearch']) {
                $queryWhere = ($queryWhere ? "{$queryWhere} OR " : null) . "{$IMPORTANT_WHERE} AND ({$subQuery['subSearch']})";
            }
            if (!$queryWhere) {
                $queryWhere = "0";
            }

            # если включен адаптивный фильтр
            if ($this->fltDB['adaptiv'] && $_GET['filter']) {
                $_GET['adaptiv'] = true;
                if ($this->fltQuery) {
                    $queryWhere = "({$queryWhere}) AND {$this->fltQuery}";
                }
            }
            # добавляем таргетинг и мультиязычность
            $queryWhere = " ({$queryWhere}) " . targeting($queryWhere, '', '');
            $queryWhere = getLangQuery($queryWhere);

            $this->queryWhere = $queryWhere;
			
			
        }
    }

    public function setFltQuery()
    {
        $this->fltQuery = getFilterQuery('');
    }

    public function getSubQuery($ids)
    {
        $subSearch = '';

        if (!$this->fltDB['one_sub_mode'] || $this->sub['sub_outallitem']) {
            $this->insub = $ids = $this->getChildSubs($ids);
        }

        $query  = "SELECT `find`, `sub_find`, `strictFind`, `view_obj_by_param`, `Subdivision_ID` AS id";
        $query .= " FROM `Subdivision` WHERE `Subdivision_ID` IN ({$ids})";
        $subs = $this->db->get_results($query, ARRAY_A);

        if (is_array($subs)) {
            $idsArr = array_flip(explode(',', $ids));
            $useFind = array();
            foreach ($subs as $sub) {
                if (
                    isset($useFind['find'][$sub['find']])
                    || isset($useFind['strictFind'][$sub['strictFind']])
                ) {
                    unset($idsArr[$sub['id']]);
                } elseif (!empty($sub['find']) || !empty($sub['strictFind'])) {
                    if (!empty($sub['find'])) {
                        $useFind['find'][$sub['find']] = true;
                    }
                    if (!empty($sub['strictFind'])) {
                        $useFind['strictFind'][$sub['strictFind']] = true;
                    }
                    $subFindSql = getFindQuery($sub['find'], 0, $sub['sub_find'], '', $sub['strictFind']);
                    if (!empty($subFindSql)) {
                        $subSearch .= ($subSearch ? " OR " : null) . $subFindSql;
                        # удаляем id раздела из запроса, если у раздела есть find
                        unset($idsArr[$sub['id']]);
                    }
                }

                if (!empty($sub['view_obj_by_param'])) {
                    $viewQuery = getQuryBySubViewParam($this->itemsClassID, $sub['view_obj_by_param']);
                    if (!empty($viewQuery)) {
                        $subSearch .= ($subSearch ? " AND " : null) . "({$viewQuery})";
                        unset($idsArr[$sub['id']]);
                    }
                }
            }
            $ids = implode(',', array_keys($idsArr));
        }
        return array('ids' => $ids, 'subSearch' => $subSearch);
    }

    public function getChildSubs($ids)
    {
        global $AUTH_USER_ID;
        $result = $subs = $ids;
        do {
            $next = false;
            $get_checked_inner_subs = ($this->sub['sub_outallitem'] ? ("") : (" AND Checked = 1"));
            $subs = ($this->db->get_col("SELECT Subdivision_ID FROM Subdivision WHERE Parent_Sub_ID IN ({$subs}) {$get_checked_inner_subs}"));

            if (is_array($subs) && count($subs)) {
                $subs = implode(',', $subs);
                $result .= ',' . $subs;
                $next = true;
            }
        } while ($next);

        return $result;
    }

    public function getFiledsNames()
    {
        $fNames = '';
        if (count($this->customFields)) {
            $fNames = 'params';
        }

        $ignoreFields = ['Email',];
        foreach ($this->fields as $field) {
            if (!in_array($field['name'], $ignoreFields)) {
                $fNames .= ($fNames ? ',' : null) . $field['name'];
            }
        }
        return $fNames;
    }

    public function setCustomFields()
    {
        if (function_exists('class2041_setCustomFields')) {
            $this->customFields = class2041_setCustomFields($this); // своя функция
        } else {
            global $setting_params;
            $this->customFields = [];
            if (is_array($setting_params) && is_array($this->fltDB['data']['checked'])) {
                foreach ($setting_params as $customField) {
                    if ($this->fltDB['data']['checked'][$customField['keyword']]) {
                        $filterType = ($this->fltDB['data']['otdo'][$customField['keyword']] ? 4 : $this->fltDB['data']['view'][$customField['keyword']]);
                        $this->customFields[$customField['keyword']] = [
                            'keyword' => $customField['keyword'],
                            'name' => $this->fltDB['data']['name'][$customField['keyword']],
                            'filter_type' => $filterType,
                            'priority' => $this->fltDB['data']['priority'][$customField['keyword']],
                            'minimized' => $this->fltDB['data']['minimized'][$customField['keyword']],
                        ];
                    }
                }
            }
        }
    }

    public function setFields()
    {
        if (function_exists('class2041_setFields')) {
            $this->fields = class2041_setFields($this); // своя функция
        } else {
            $fields = array();
            if (is_array($this->fltDB['data']['checked'])) {
                $fieldsSelct = '';
				
				//if ($_SERVER['REMOTE_ADDR']=='31.13.133.138') echo "<p>".print_r($this->fltDB,1)."</p>";
				
                foreach ($this->fltDB['data']['checked'] as $fID) {
                    $fieldsSelct .= ($fieldsSelct ? ',' : '') . "'" . $fID . "'";
                }
                $fieldsDB = $fieldsSelct ? $this->db->get_results("SELECT Field_ID AS id, Field_Name AS name, Description, TypeOfData_ID, Format FROM Field WHERE Class_ID = 2001 AND Field_ID IN ({$fieldsSelct})", ARRAY_A) : null;
				
				
				
                if (is_array($fieldsDB)) {
                    foreach ($fieldsDB as $field) {
                        $fields[$field['id']] = $field;
                    }
                }
            }
            $this->fields = $fields;
        }
    }

    public function setFltDB()
    {
        $this->fltDB = $this->db->get_row("SELECT * FROM Message{$this->classID} WHERE Message_ID = {$this->id}", ARRAY_A);
        if (!is_array($this->fltDB)) {
            $this->fltDB = array();
        }
        $this->setData();
    }

    public function setData()
    {
        $data = isset($this->fltDB['data']) ? orderArray($this->fltDB['data']) : array();
        $this->fltDB['data'] = is_array($data) ? $data : array();
    }

    public function setFilterSub()
    {
        $sub = $this->db->get_row("SELECT * FROM Subdivision AS a INNER JOIN Sub_Class AS b ON a.Subdivision_ID = b.Subdivision_ID
                                   WHERE a.Catalogue_ID = {$this->catalogue} AND b.Class_ID = {$this->classID} LIMIT 0, 1", ARRAY_A);

        $this->fltSub = is_array($sub) ? $sub : array();
    }

    public function setDB()
    {
        global $db;
        $this->db = $db;
    }

    public function setEditLink()
    {
        $this->editLink = $this->bitcat ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : null;
    }

    public function writeTime()
    {
        echo microtime(true) - $this->microtime;
    }

    public function __get($name)
    {
        if (!isset($this->$name)) {
            switch ($name) {
                case 'db':
                    $this->setDB();
                    break;
                case 'fltSub':
                    $this->setFilterSub();
                    break;
                case 'fltDB':
                    $this->setFltDB();
                    break;
                case 'fields':
                    $this->setFields();
                    break;
                case 'customFields':
                    $this->setCustomFields();
                    break;
                case 'queryWhere':
                    $this->setQueryWhere();
                    break;
                case 'fltQuery':
                    $this->setFltQuery();
                    break;
                case 'items':
                    $this->setItems();
                    break;
                case 'values':
                    $this->setValues();
                    break;
                case 'checkFilterVal':
                    $this->setCheckFilterVal();
                    break;
                case 'editLink':
                    $this->setEditLink();
                    break;
                default:
                    if (isset($this->params[$name])) {
                        $this->$name = $this->params[$name];
                    }
                    break;
            }
        }
        return isset($this->$name) ? $this->$name : null;
    }
}
