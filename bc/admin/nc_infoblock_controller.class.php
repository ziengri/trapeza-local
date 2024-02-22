<?php

class nc_infoblock_controller extends nc_ui_controller {

    protected $is_naked = false;

    /**
     *
     */
    protected function init() {
        $this->is_naked = true;
        $GLOBALS['isNaked'] = 1; // для nc_print_status() внутри Permission::ExitIfNotAccess() :(

        $this->bind('get_component_template_settings', array('component_id'));
    }

    /**
     * @param $result
     * @return string
     */
    protected function after_action($result) {
        if ($this->is_naked) {
            return $result;
        }

        return BeginHtml() . $result . EndHtml();
    }

    /**
     * @param $message
     * @return bool
     */
    protected function make_error_message($message) {
        return nc_print_status($message, 'error', array(), true);
    }

    /**
     * @param nc_a2f $a2f
     * @return string
     */
    protected function render_a2f(nc_a2f $a2f) {
        return $a2f->render(
            false,
            array(
                'checkbox' => '<div class="nc-field"><label>%VALUE %CAPTION</label></div>',
                'default' => '<div class="nc-field"><span class="nc-field-caption">%CAPTION:</span>%VALUE</div>',
            ),
            false,
            false
        );
    }

    /**
     * @param $infoblock_id
     * @param array $data
     * @return nc_db_table
     */
    protected function update($infoblock_id, array $data) {
        $nc_core = nc_core::get_object();
        $site_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Catalogue_ID');
        $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');

        if (isset($data['Checked']) && count($data) == 1 && $nc_core->sub_class->get_by_id($infoblock_id, 'Checked') != $data['Checked']) {
            $event = array(nc_event::BEFORE_INFOBLOCK_ENABLED, nc_event::AFTER_INFOBLOCK_ENABLED);
        }
        else {
            $event = array(nc_event::BEFORE_INFOBLOCK_UPDATED, nc_event::AFTER_INFOBLOCK_UPDATED);
        }

        $nc_core->event->execute($event[0], $site_id, $subdivision_id, $infoblock_id);
        $result = nc_db_table::make('Sub_Class')->where_id($infoblock_id)->update($data);
        $nc_core->event->execute($event[1], $site_id, $subdivision_id, $infoblock_id);

        return $result;
    }

    /**
     * @param $infoblock_id
     * @param int $action
     */
    protected function check_infoblock_permissions($infoblock_id, $action = NC_PERM_ACTION_ADMIN) {
        /** @var Permission $perm */
        global $perm;
        $perm->ExitIfNotAccess(NC_PERM_ITEM_CC, $action, $infoblock_id, null, 1);
    }

    /**
     * @param $subdivision_id
     * @param int $action
     */
    protected function check_subdivision_permissions($subdivision_id, $action = NC_PERM_ACTION_SUBCLASSADD) {
        /** @var Permission $perm */
        global $perm;
        $perm->ExitIfNotAccess(NC_PERM_ITEM_SUB, $action, $subdivision_id, null, 1);
    }

    /**
     *
     */
    protected function check_site_admin_permissions() {
        /** @var Permission $perm */
        global $perm;
        $perm->ExitIfNotAccess(NC_PERM_ITEM_SITE, NC_PERM_ACTION_ADMIN, $this->site_id);
    }

    /**
     * @param int $infoblock_id
     * @param string $main_axis 'vertical', 'horizontal'
     * @return int
     * @throws Exception
     */
    protected function wrap_with_flexbox($infoblock_id, $main_axis) {
        $nc_core = nc_core::get_object();
        $wrapped_block_data = $nc_core->sub_class->get_by_id($infoblock_id);

        $main_axis_is_vertical = $main_axis === 'vertical';

        $mixin_settings = array('' => array('layout' => array(9999 => array(
            'mixin' => 'netcat_layout_flexbox',
            'settings' =>
                array(
                    'flexbox_direction' => ($main_axis_is_vertical ? 'row' : 'column'),
                    'flexbox_wrap' => 'nowrap',
                    'list_min_height' => '',
                    'list_min_height_unit' => 'px',
                    'list_height' => '',
                    'list_height_unit' => 'px',
                    'list_max_height' => '',
                    'list_max_height_unit' => 'px',
                    'flexbox_justify' => ($main_axis_is_vertical ? 'stretch' : 'flex-start'),
                    'horizontal_spacing' => '',
                    'flexbox_align' => ($main_axis_is_vertical ? 'center' : 'stretch'),
                    'vertical_spacing' => '',
                ),
        ))));

        // Данные о расположении, остающиеся неизменными:
        $location = array(
            'Catalogue_ID' => $wrapped_block_data['Catalogue_ID'],
            'Subdivision_ID' => $wrapped_block_data['Subdivision_ID'],
            'AreaKeyword' => $wrapped_block_data['AreaKeyword'],
        );

        // Создание контейнера
        $container_id = $nc_core->sub_class->create(0, $location + array(
            'Parent_Sub_Class_ID' => $wrapped_block_data['Parent_Sub_Class_ID'],
            'Checked' => 1,
            'Index_Mixin_Settings' => nc_array_json($mixin_settings),
        ));

        // Перенос блока в контейнер
        $nc_core->sub_class->move($infoblock_id, $location + array('Parent_Sub_Class_ID' => $container_id), false);

        // Приоритет устанавливаем отдельно после создания, чтобы nc_sub_class::create() не сдвинул приоритеты
        $nc_core->db->query("UPDATE `Sub_Class` SET `Priority` = $wrapped_block_data[Priority] WHERE `Sub_Class_ID` = $container_id");

        return $container_id;
    }

    /**
     * @param int $component_id
     * @param array|null $values
     * @return string
     * @throws nc_Exception_Class_Doesnt_Exist
     */
    protected function get_custom_settings_html($component_id, $values = null) {
        $custom_settings_template = nc_core::get_object()->component->get_by_id($component_id, 'CustomSettingsTemplate');
        if ($custom_settings_template) {
            $a2f = new nc_a2f($custom_settings_template, 'custom_settings');
            $a2f->set_initial_values();
            if ($values) {
                $a2f->set_values($values);
            }
            return $this->render_a2f($a2f);
        }
        return '';
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    protected function action_update_custom_setting() {
        $nc_core = nc_core::get_object();

        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $key = $nc_core->input->fetch_post('key');
        $value = $nc_core->input->fetch_post('value');

        $this->check_infoblock_permissions($infoblock_id);

        $infoblock_data = $nc_core->sub_class->get_by_id($infoblock_id);

        $a2f = new nc_a2f($infoblock_data['CustomSettingsTemplate'], 'CustomSettings');
        $a2f->set_values($infoblock_data['CustomSettings']);
        $a2f->set_values(array($key => $value));

        if (!$a2f->validate($a2f->get_values_as_array())) {
            return $this->make_error_message($a2f->get_validation_errors());
        } else {
            $this->update($infoblock_id, array('CustomSettings' => $a2f->get_values_as_string()));
            return 'OK';
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function action_toggle() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $was_enabled = $nc_core->sub_class->get_by_id($infoblock_id, 'Checked');
        $this->update($infoblock_id, array('Checked' => (int)!$was_enabled));

        return 'OK';
    }

    /**
     * @return nc_ui_view|string|null
     */
    protected function action_show_settings_dialog() {
        $nc_core = nc_core::get_object();
        $db = $nc_core->db;

        $nc_core->inside_admin = false;
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $infoblock_data = $nc_core->sub_class->get_by_id($infoblock_id);

        $is_container = !$infoblock_data['Class_ID'];
        if (!$is_container) {
            $custom_settings_html = $this->get_custom_settings_html(
                $infoblock_data['Class_Template_ID'] ?: $infoblock_data['Class_ID'],
                $infoblock_data['CustomSettings']
            );

            $custom_dialog_path =
                $nc_core->CLASS_TEMPLATE_FOLDER .
                ltrim($infoblock_data['File_Path'], '/') .
                nc_component::FILE_BLOCK_SETTINGS_DIALOG;

            if ($infoblock_data['Class_Template_ID']) {
                $nc_parent_field_path = $nc_core->CLASS_TEMPLATE_FOLDER .
                    ltrim($nc_core->component->get_by_id($infoblock_data['Class_ID'], 'File_Path'), '/') .
                    nc_component::FILE_BLOCK_SETTINGS_DIALOG;

                if (!file_exists($custom_dialog_path)) {
                    $custom_dialog_path = $nc_parent_field_path;
                    $nc_parent_field_path = null;
                }
            } else {
                $nc_parent_field_path = null;
            }

            if (file_exists($custom_dialog_path)) {
                /**
                 * В BlockSettingsDialog.html можно использовать переменные:
                 *   $nc_parent_field_path
                 *   $cc
                 *   $custom_settings_html
                 */
                $cc = $infoblock_id;
                ob_start();
                include $custom_dialog_path;
                $custom_settings_html = ob_get_clean();
            }
        } else {
            $custom_settings_html = '';
        }

        if ($infoblock_data['AreaKeyword']) {
            $visibility_conditions = array(
                'subdivision' => $db->get_results("SELECT * FROM `Sub_Class_AreaCondition_Subdivision` WHERE `Sub_Class_ID` = $infoblock_id", ARRAY_A),
                'subdivision_exception' => $db->get_results("SELECT * FROM `Sub_Class_AreaCondition_Subdivision_Exception` WHERE `Sub_Class_ID` = $infoblock_id", ARRAY_A),
                'component' => $db->get_results("SELECT * FROM `Sub_Class_AreaCondition_Class` WHERE `Sub_Class_ID` = $infoblock_id", ARRAY_A),
                'component_exception' => $db->get_results("SELECT * FROM `Sub_Class_AreaCondition_Class_Exception` WHERE `Sub_Class_ID` = $infoblock_id", ARRAY_A),
                'object' => $db->get_results("SELECT * FROM `Sub_Class_AreaCondition_Message` WHERE `Sub_Class_ID` = $infoblock_id", ARRAY_A),
            );
        } else {
            $visibility_conditions = array();
        }

        return $this->view('infoblock/settings_dialog', array(
            'infoblock_data' => $infoblock_data,
            'custom_settings' => $custom_settings_html,
            'visibility_conditions' => $visibility_conditions,
        ));
    }

    /**
     *
     */
    protected function action_save() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);
        $nc_core->inside_admin = false; // чтобы не получить свойства шаблона inside_admin, например, для CustomSettingsTemplate

        $updated_properties = $nc_core->input->fetch_post('data') ?: array();
        if ($updated_properties) {
            $updated_properties = (array)$updated_properties;
        }

        $custom_settings = $nc_core->input->fetch_post('custom_settings') ?: array();
        if ($custom_settings || $nc_core->input->fetch_files('custom_settings')) {
            $a2f = new nc_a2f($nc_core->sub_class->get_by_id($infoblock_id, 'CustomSettingsTemplate'), 'custom_settings');
            $a2f->set_values($nc_core->sub_class->get_by_id($infoblock_id, 'CustomSettings'));
            $new_custom_settings = array_merge($a2f->get_values_as_array(), $custom_settings);
            if ($a2f->validate($new_custom_settings)) {
                $a2f->save_from_request_data('custom_settings');
                $updated_properties['CustomSettings'] = $a2f->get_values_as_string();
            } else {
                return $this->make_error_message($a2f->get_validation_errors());
            }
        }

        if ($updated_properties['Condition']) {
			$infoblock_condition_translator = new nc_condition_infoblock_translator($updated_properties['Condition'], $infoblock_id);
			$updated_properties['ConditionQuery'] = $infoblock_condition_translator->get_sql_condition();
        }

        $nullable_properties = array('MaxRecordsInInfoblock', 'MinRecordsInInfoblock', 'ConditionLimit');
        foreach ($nullable_properties as $key) {
            if ($updated_properties[$key] === '') {
                $updated_properties[$key] = NULL;
            }
        }

        $integer_properties = array('QueryOffset');
        foreach ($integer_properties as $key) {
            if (array_key_exists($key, $updated_properties)) {
                $updated_properties[$key] = (int)$updated_properties[$key];
            }
        }

        if ($updated_properties) {
            $this->update($infoblock_id, $updated_properties);
        }

        // Условия показа блока, сохраняемые в отдельных таблицах:
        $visibility_conditions = $nc_core->input->fetch_post('visibility') ?: array();

        // (1) Sub_Class_AreaCondition_Subdivision
        // (2) Sub_Class_AreaCondition_Subdivision_Exception
        // (3) Sub_Class_AreaCondition_Class
        // (4) Sub_Class_AreaCondition_Class_Exception
        $visibility_conditions_to_save = array(
            'subdivision'           => array('Sub_Class_AreaCondition_Subdivision',           'Subdivision_ID'),
            'subdivision_exception' => array('Sub_Class_AreaCondition_Subdivision_Exception', 'Subdivision_ID'),
            'component'             => array('Sub_Class_AreaCondition_Class',                 'Class_ID'),
            'component_exception'   => array('Sub_Class_AreaCondition_Class_Exception',       'Class_ID'),
        );
        foreach ($visibility_conditions_to_save as $type => $params) {
            if (isset($visibility_conditions[$type]) && is_array($visibility_conditions[$type])) {
                $this->save_visibility_conditions_with_one_required_field($infoblock_id, $params[0], $params[1], $visibility_conditions[$type]);
            }
        }

        // (5) Sub_Class_AreaCondition_Message
        if (isset($visibility_conditions['object']) && is_array($visibility_conditions['object'])) {
            $this->save_visibility_object_conditions($infoblock_id, $visibility_conditions['object']);
        }

        return "OK\nReloadPage=1\n";
    }

    /**
     * Сохраняет условия показа блока по разделам и компонентам
     * @param int $infoblock_id
     * @param string $table_name
     * @param string $required_field
     * @param array $visibility_conditions
     */
    protected function save_visibility_conditions_with_one_required_field($infoblock_id, $table_name, $required_field, array $visibility_conditions) {
        $table = nc_db_table::make($table_name, 'Condition_ID');
        $saved_ids = array();

        foreach ($visibility_conditions as $condition) {
            $is_new = empty($condition['Condition_ID']);
            $is_deleted = !empty($condition['_remove']);

            // сохраняем данные только для указанного инфоблока; не пытаемся сохранить повторно
            $required_value = $condition[$required_field];
            if ((int)$condition['Sub_Class_ID'] !== (int)$infoblock_id || (!$is_deleted && !empty($saved_ids[$required_value]))) {
                continue;
            }

            if ($is_new && !$is_deleted && !empty($condition[$required_field])) {
                $table->insert($condition);
            } else if (!$is_new) {
                if ($is_deleted) {
                    $table->where_id($condition['Condition_ID'])->delete();
                } else { // IncludeChildren мог поменяться
                    $table->where_id($condition['Condition_ID'])->update($condition);
                }
            }

            if (!$is_deleted) {
                $saved_ids[$required_value] = true;
            }
        }
    }

    /**
     * @param int $infoblock_id
     * @param string $table_name
     * @param string $required_field
     * @param array $visibility_conditions
     */
    protected function save_visibility_object_conditions($infoblock_id, array $visibility_conditions) {
        $table_name = 'Sub_Class_AreaCondition_Message';
        $table = nc_db_table::make($table_name, 'Condition_ID');
        $saved_ids = array();

        foreach ($visibility_conditions as $condition) {
            $component_id = $condition['Class_ID'];
            $object_id = $condition['Message_ID'];
            $key = "$component_id:$object_id";

            $is_new = empty($condition['Condition_ID']);
            $is_deleted = !empty($condition['_remove']);

            // сохраняем данные только для указанного инфоблока; не пытаемся сохранить повторно
            if ((int)$condition['Sub_Class_ID'] !== (int)$infoblock_id || (!$is_deleted && !empty($saved_ids[$key]))) {
                continue;
            }

            if ($is_new && !$is_deleted && $component_id && $object_id) {
                $table->insert($condition);
            } else if (!$is_new) {
                if ($is_deleted) {
                    $table->where_id($condition['Condition_ID'])->delete();
                } else { // IncludeChildren мог поменяться
                    $table->where_id($condition['Condition_ID'])->update($condition);
                }
            }

            if (!$is_deleted) {
                $saved_ids[$key] = true;
            }
        }
    }

    /**
     * @return nc_ui_view
     */
    protected function action_show_delete_confirm_dialog() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post_get('infoblock_id');
        $infoblock = $nc_core->sub_class->get_by_id($infoblock_id);
        $infoblock_name = $infoblock['Sub_Class_Name'];
        $this->check_infoblock_permissions($infoblock_id);

        if (!$infoblock_name) {
            if ($infoblock['Class_ID']) {
                // Обычный инфоблок
                $infoblock_name = CONTROL_CONTENT_SUBDIVISION_CLASS;
            } else {
                // Контейнер
                $infoblock_name = NETCAT_MODERATION_CONTAINER;
            }

            $infoblock_name .= ' #' . $infoblock_id;
        }

        return $this->view('infoblock/delete_confirm_dialog', compact('infoblock_id', 'infoblock_name'));
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function action_delete() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');

        $this->check_subdivision_permissions($subdivision_id, NC_PERM_ACTION_SUBCLASSDEL);

        require_once $nc_core->ADMIN_FOLDER . 'subdivision/subclass.inc.php';

        if ($nc_core->sub_class->delete($infoblock_id) === false) {
            return $this->make_error_message(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_DELETE);
        }
        else {
            return "\nReloadPage=1\n";
        }
    }

    /**
     * @return nc_ui_view
     */
    protected function action_show_new_infoblock_dialog() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');
        $area_keyword = $nc_core->input->fetch_get_post('area_keyword');
        $container_id = (int)$nc_core->input->fetch_get_post('container_id');
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');
        $position = $nc_core->input->fetch_get_post('position');

        $this->check_subdivision_permissions($subdivision_id);

        $components = (array)nc_db()->get_results(
            "SELECT *
              FROM `Class`
             WHERE `ClassTemplate` = 0
               AND `IsAuxiliary` = 0
             ORDER BY `Class_Group`, `Priority`, `Class_ID`",
            ARRAY_A
        );

        return $this->view('infoblock/new_infoblock_dialog', array(
            'subdivision_id' => $subdivision_id,
            'area_keyword' => $area_keyword,
            'container_id' => $container_id,
            'position' => $position,
            'infoblock_id' => $infoblock_id,
            'components' => $components,
        ));
    }


    /**
     * @return nc_ui_view
     */
    protected function action_show_new_infoblock_simple_dialog() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');
        $area_keyword = $nc_core->input->fetch_get_post('area_keyword');
        $container_id = (int)$nc_core->input->fetch_get_post('container_id');
        $position = $nc_core->input->fetch_get_post('position');
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');

        if (!empty($area_keyword)) {
            $this->check_site_admin_permissions();
        } else {
            $this->check_subdivision_permissions($subdivision_id);
        }

        $component_templates = (array)nc_db()->get_results(
            "SELECT `Class_ID`, `Class_Group`, `Class_Name`, `ClassTemplate`, `IsOptimizedForMultipleMode`, `IsMultipurpose`, `File_Path`, `File_Hash`
              FROM `Class` AS component
             WHERE `File_Mode` = 1
               AND `IsAuxiliary` = 0
               AND (`IsOptimizedForMultipleMode` = 1 OR (
                        SELECT COUNT(*)
                          FROM `Class` AS template
                         WHERE template.ClassTemplate = component.Class_ID
                           AND template.IsOptimizedForMultipleMode = 1
                   ))
               AND `IsMultipurpose` != 1   
             ORDER BY `Class_Group`, `ClassTemplate` = 0, `Priority`, `Class_ID`",
            ARRAY_A
        );

        $multipurpose_templates = (array)nc_db()->get_results(
            'SELECT `Class_Multipurpose_Template_Cache`.`Class_ID`, `Class_Group`, `Class_Name`,
             `Compatible_Class_ID` AS "ClassTemplate", `IsOptimizedForMultipleMode`, `IsMultipurpose`
             FROM `Class_Multipurpose_Template_Cache`
             LEFT JOIN `Class` ON `Class_Multipurpose_Template_Cache`.`Class_ID` = `Class`.`Class_ID`
             WHERE `IsOptimizedForMultipleMode` = 1
             ORDER BY `Class_Group`, `ClassTemplate` = 0, `Priority`, `Class_ID`',
            ARRAY_A
        );

        if (!$component_templates) {
            return $this->action_show_new_infoblock_dialog();
        }

        $component_templates = array_merge($component_templates, $multipurpose_templates);

        $footer_notice = '';
        if (strpos($position, 'wrap_') === 0) {
            try {
                $reference_infoblock_is_container = !$nc_core->sub_class->get_by_id($infoblock_id, 'Class_ID');
                $reference_infoblock_name = $nc_core->sub_class->get_by_id($infoblock_id, 'Sub_Class_Name');
            } catch (Exception $e) {
                $reference_infoblock_is_container = false;
                $reference_infoblock_name = false;
            }
            if ($reference_infoblock_name) {
                $footer_notice = sprintf(
                    $reference_infoblock_is_container ? NETCAT_MODERATION_ADD_BLOCK_WRAP_CONTAINER : NETCAT_MODERATION_ADD_BLOCK_WRAP_BLOCK,
                    $reference_infoblock_name
                );
            } else {
                $footer_notice = NETCAT_MODERATION_ADD_BLOCK_WRAP;
            }
        }

        return $this->view('infoblock/new_infoblock_simple_dialog', array(
            'subdivision_id' => $subdivision_id,
            'area_keyword' => $area_keyword,
            'container_id' => $container_id,
            'position' => $position,
            'infoblock_id' => $infoblock_id,
            'component_templates' => $component_templates,
            'main_axis' => $nc_core->input->fetch_get_post('main_axis') ?: 'vertical',
            'footer_notice' => $footer_notice,
        ));
    }


    /**
     * @return string
     */
    protected function action_create() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');
        $area_keyword = $nc_core->input->fetch_get_post('area_keyword');

        // Свойства создаваемого инфоблока
        $infoblock_properties = (array)$nc_core->input->fetch_post('data');

        $parent_is_main_container = false;
        $parent_sub_class_id = (int)nc_array_value($infoblock_properties, 'Parent_Sub_Class_ID', 0);

        try {
            if ($parent_sub_class_id && $nc_core->sub_class->get_by_id($parent_sub_class_id, 'IsMainContainer')) {
                $parent_is_main_container = true;
            }
        } catch (Exception $e) {
            $parent_is_main_container = false;
        }

        if ($parent_is_main_container) {
            $infoblock_properties['Parent_Sub_Class_ID'] = 0;
            $area_keyword = null;
        }

        if ($area_keyword) {
            $this->check_site_admin_permissions();
        } else {
            $this->check_subdivision_permissions($subdivision_id);
        }

        if (!empty($area_keyword)) {
            // @todo fix Catalogue_ID? ↓
            $infoblock_properties['Catalogue_ID'] = $nc_core->catalogue->get_current('Catalogue_ID');
            $infoblock_properties['Subdivision_ID'] = 0;
            $infoblock_properties['AreaKeyword'] = $area_keyword;
        } else {
            $infoblock_properties['Subdivision_ID'] = $subdivision_id;
            $infoblock_properties['AreaKeyword'] = '';
        }
        $component_id = (int)nc_array_value($infoblock_properties, 'Class_ID');
        $custom_settings = $nc_core->input->fetch_post('custom_settings') ?: array();

        $position = $nc_core->input->fetch_post('position');
        $position_infoblock_id = (int)$nc_core->input->fetch_post('position_infoblock_id');

        // Положение инфоблока относительно другого инфоблока
        if ($position_infoblock_id) {
            $other_infoblock_priority = $nc_core->sub_class->get_by_id($position_infoblock_id, 'Priority');
            if ($position === 'wrap_before' || $position === 'wrap_after') {
                // Оборачивание в контейнер
                $main_axis = $nc_core->input->fetch_post('main_axis') ?: 'vertical';
                $infoblock_properties['Parent_Sub_Class_ID'] = $this->wrap_with_flexbox($position_infoblock_id, $main_axis);
                $infoblock_properties['Priority'] = ($position === 'wrap_before' ? 1 : 2);
            } else {
                // Блок рядом с уже существующим блоком
                $infoblock_properties['Priority'] = $other_infoblock_priority + ($position === 'before' ? 0 : 1);
            }
        }

        if ($component_id && !nc_array_value($infoblock_properties, 'Sub_Class_Name')) {
            $infoblock_properties['Sub_Class_Name'] = $nc_core->component->get_by_id($component_id, 'Class_Name');
        }

        try {
            $infoblock_id = $nc_core->sub_class->create($component_id, $infoblock_properties, $custom_settings);
            $nc_core->sub_class->create_mock_objects($infoblock_id);

            if ($area_keyword) {
                return "\nReloadPage=1";
            }

            $infoblock_english_name = $nc_core->sub_class->get_by_id($infoblock_id, 'EnglishName');
            return "\nReloadPage=1\nSetLocationHash=$infoblock_english_name\n";
        }
        catch (Exception $e) {
            return $this->make_error_message($e->getMessage());
        }
    }

    /**
     * @param int $component_id
     * @return string (JSON)
     */
    protected function action_get_component_template_settings($component_id) {
        $nc_core = nc_core::get_object();
        if (!$nc_core->user->get_by_id($GLOBALS['AUTH_USER_ID'], 'InsideAdminAccess')) {
            return $this->make_error_message(NETCAT_MODERATION_ERROR_NORIGHT);
        }

        $template_info = array();

        $template_info[] = array(
            'id' => 0,
            'name' => NETCAT_MODERATION_COMPONENT_NO_TEMPLATE,
            'preview' => $nc_core->component->get_list_preview_relative_path($component_id, true),
            'settings' => $this->get_custom_settings_html($component_id),
            'multiple_mode' => $nc_core->component->get_by_id($component_id, 'IsOptimizedForMultipleMode'),
        );

        $templates = $nc_core->component->get_component_templates($component_id, 'useful');
        if ($templates) {
            foreach ($templates as $template) {
                $template_id = $template['Class_ID'];
                $template_info[] = array(
                    'id' => $template_id,
                    'name' => $template['Class_Name'],
                    'preview' => $nc_core->component->get_list_preview_relative_path($template_id, true),
                    'settings' => $this->get_custom_settings_html($template_id),
                    'multiple_mode' => $template['IsOptimizedForMultipleMode'],
                );
            }
        }

        return nc_array_json($template_info);
    }

    /**
     *
     */
    protected function action_set_component_template() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');

        $is_area_block = $nc_core->sub_class->get_by_id($infoblock_id, 'AreaKeyword');
        if ($is_area_block) {
            $this->check_site_admin_permissions();
            $subdivision_id = (int)$nc_core->input->fetch_post('subdivision_id');
        } else {
            $this->check_infoblock_permissions($infoblock_id);
            $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');
        }

        $template_id = (int)$nc_core->input->fetch_post('template_id');
        $this->update($infoblock_id, array('Class_Template_ID' => $template_id));

        ob_end_clean();
        header(
            'Location: ' .
            $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH .
            '?isNaked=1' .
            '&sub=' . $subdivision_id .
            '&cc_only=' . $infoblock_id .
            '&include_component_style_tag=1'
        );
        exit;
    }

    /**
     *
     */
    protected function action_paste() {
        $nc_core = nc_core::get_object();

        $reference_infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        if ($reference_infoblock_id) {
            $destination_subdivision_id = $nc_core->sub_class->get_by_id($reference_infoblock_id, 'Subdivision_ID');
        } else {
            $destination_subdivision_id = $nc_core->input->fetch_post('subdivision_id');
        }

        $paste_mode = $nc_core->input->fetch_post('paste_mode'); // 'copy', 'cut'
        $pasted_infoblock_id = (int)$nc_core->input->fetch_post('pasted_infoblock_id');
        $position = $nc_core->input->fetch_post('position');
        $container_id = (int)$nc_core->input->fetch_post('container_id');
        $area_keyword = $nc_core->input->fetch_post('area_keyword');
        $site_id = $nc_core->input->fetch_post('site_id') ?: $this->determine_site_id();

        if (!$pasted_infoblock_id) {
            return 'ERROR: no source infoblock ID';
        }

        $this->check_infoblock_permissions($pasted_infoblock_id);
        if ($destination_subdivision_id) {
            $this->check_subdivision_permissions($destination_subdivision_id);
        } else {
            $this->check_infoblock_permissions($reference_infoblock_id);
        }

        if ($position === 'wrap_before' || $position === 'wrap_after') {
            $main_axis = $nc_core->input->fetch_post('main_axis') ?: 'vertical';
            $container_id = $this->wrap_with_flexbox($reference_infoblock_id, $main_axis);
        }

        $destination = array(
            'Catalogue_ID' => $site_id,
            'Subdivision_ID' => $destination_subdivision_id,
            'AreaKeyword' => $area_keyword,
            'Parent_Sub_Class_ID' => $container_id,
        );

        if ($reference_infoblock_id && ($position === 'before' || $position === 'after')) {
            $destination['Priority'] = array(
                'Position' => $position,
                'Sub_Class_ID' => $reference_infoblock_id,
            );
        }

        if ($paste_mode === 'cut') {
            if ($pasted_infoblock_id === $reference_infoblock_id) {
                return 'OK';
            }
            $nc_core->sub_class->move($pasted_infoblock_id, $destination);
        } else { // paste_mode = copy
            $nc_core->sub_class->duplicate($pasted_infoblock_id, $destination);
        }

        return 'OK';
    }

    protected function action_show_area_infoblocks(){
        require_once $this->nc_core->ADMIN_FOLDER . 'catalogue/function.inc.php';
        $this->is_naked = false;
        $GLOBALS['isNaked'] = 0;
        $catalogue_id = $this->site_id;
        $sub_class_tree = $this->get_area_infoblocks_tree($catalogue_id);

        $this->ui_config = new ui_config_catalogue('area', $catalogue_id);

        return $this->view('infoblock/area_infoblocks_tree', array(
            'data'=>$sub_class_tree
        ));
    }

    /**
     * возвращает дерево сквозных инфоблоков. В ключе 'Child' содержатся потомки
     */
    protected function get_area_infoblocks_tree($catalogue_id, $parent_sub_class_id = 0){
        $db = nc_core::get_object()->db;
        $result = array();
        $sub_classes = $db->get_results("
            SELECT `Sub_Class`.*, `Class`.`Class_Name`
            FROM `Sub_Class`
            LEFT JOIN `Class` ON `Sub_Class`.`Class_ID` = `Class`.`Class_ID`
            WHERE `Catalogue_ID` = " . (int)$catalogue_id . " AND `Subdivision_ID` = 0 AND `Parent_Sub_Class_ID` = " . (int)$parent_sub_class_id . "
            ORDER BY " . ($parent_sub_class_id == 0 ? '`AreaKeyword`, ' : NULL) . "`Priority`
        ", ARRAY_A);
        if ($sub_classes) {
            foreach ($sub_classes AS $i => $sub_class) {
                $result[$i] = $sub_class;
                $result[$i]['Child'] = $this->get_area_infoblocks_tree($catalogue_id, $sub_class['Sub_Class_ID']);
            }
        }

        return $result;
    }
	
	
    protected function action_show_mixin_editor($scope) {
        $nc_core = nc_core::get_object();
        $infoblock_id = $nc_core->input->fetch_post_get('SubClassID');
        $infoblock_data = $nc_core->sub_class->get_by_id($infoblock_id);
        return $this->view('mixin/mixin_editor', array(
            'show_preset_select' => true,
            'show_breakpoint_type_select' => true,
            'field_name_prefix' => $scope,
            'data' => $infoblock_data,
        ));
    }
}