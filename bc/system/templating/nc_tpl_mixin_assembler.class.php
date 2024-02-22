<?php

class nc_tpl_mixin_assembler {

    static protected $mixin_settings;
    static protected $already_assembled = array();

    /**
     * @param $mixin_preset_id
     * @return array
     */
    protected static function get_mixin_preset_settings($mixin_preset_id) {
        $mixin_preset_id = (int)$mixin_preset_id;
        if (!$mixin_preset_id) {
            return array();
        }

        if (!isset(self::$mixin_settings[$mixin_preset_id])) {
            $settings = nc_db()->get_var(
                "SELECT `Mixin_Settings` FROM `Mixin_Preset` WHERE `Mixin_Preset_ID` = $mixin_preset_id"
            ) ?: '[]';
            self::$mixin_settings[$mixin_preset_id] = json_decode($settings, true) ?: array();
        }

        return self::$mixin_settings[$mixin_preset_id];
    }

    /**
     * @param string $block_selector
     * @param string $list_selector
     * @param string $scope_prefix префикс названий элементов в $block_properties, в правильном
     *    регистре, например: 'Index', 'MainArea'
     * @param array $block_properties массив с элементами:
     *      (ScopePrefix)_Mixin_Preset_ID
     *      (ScopePrefix)_Mixin_Settings
     *      (ScopePrefix)_Mixin_BreakpointType
     * @param int $priority
     * @return null|string
     */
    public static function assemble($block_selector, $list_selector, $scope_prefix, array $block_properties, $priority = nc_page::STYLE_PRIORITY_BLOCK_INSIDE_MAIN_AREA) {
        // Миксины для каждого $block_selector добавляются только один (первый) раз; последующие
        // вызовы ничего не делают. Если понадобится добавлять миксины несколько раз, можно добавить к ключу
        // в $already_assembled, например, "/" . crc32("$mixin_preset_id/$mixin_settings_json")
        if (isset(self::$already_assembled[$block_selector])) {
            return null;
        }
        self::$already_assembled[$block_selector] = true;

        $mixin_preset_id = nc_array_value($block_properties, $scope_prefix . '_Mixin_Preset_ID');
        $mixin_settings_json = nc_array_value($block_properties, $scope_prefix . '_Mixin_Settings');
        $breakpoint_type = nc_array_value($block_properties, $scope_prefix . '_Mixin_BreakpointType', 'viewport');

        $preset_settings = self::get_mixin_preset_settings($mixin_preset_id);
        $mixin_settings = json_decode($mixin_settings_json, true);
        if (!$preset_settings && !$mixin_settings) {
            return null;
        }

        $nc_core = nc_core::get_object();

        $styles = '';
        $scripts = '';

        $combined_settings = self::combine_own_settings_with_preset($mixin_settings ?: array(), $preset_settings ?: array());

        // selector[type][breakpoint] = { mixin: 'keyword', settings: {} }
        foreach ($combined_settings as $mixin_selector => $mixin_type_settings) {
            foreach ($mixin_type_settings as $mixin_type => $mixin_block_settings) {
                $prev_breakpoint = 0;
                ksort($mixin_block_settings, SORT_NUMERIC);
                foreach ($mixin_block_settings as $breakpoint => $mixin_breakpoint_settings) {
                    $mixin_keyword = $mixin_breakpoint_settings['mixin'];

                    $mixin = nc_tpl_mixin::by_keyword($mixin_type, $mixin_keyword);
                    if (!$mixin) {
                        continue;
                    }

                    $selector_suffix = $breakpoint_type === 'block' ? self::get_block_query($prev_breakpoint, $breakpoint) : '';
                    $mixin_settings = nc_array_value($mixin_breakpoint_settings, 'settings', array());

                    $block_styles = $mixin->get_block_styles(
                        $block_selector . $mixin_selector . $selector_suffix,
                        $list_selector . $mixin_selector . $selector_suffix,
                        (array)$mixin_settings
                    );

                    if ($breakpoint_type === 'viewport') {
                        $media_query = self::get_media_query($prev_breakpoint, $breakpoint);
                        if ($media_query) {
                            $block_styles = "@media $media_query {\n$block_styles}\n";
                        }
                    }

                    $styles .= $block_styles;

                    $init_js = $mixin->get('init_js');
                    $destruct_js = $mixin->get('destruct_js');
                    if ($init_js || $destruct_js) {
                        $scripts .=
                            "nc_mixin_init('$block_selector', '$list_selector', '$mixin_selector', $prev_breakpoint, $breakpoint, " .
                            (trim($init_js, " \r\n\t;") ?: 'null') . ', ' .
                            (trim($destruct_js, " \r\n\t;") ?: 'null') . ', ' .
                            nc_array_json($mixin_settings) . ", '$breakpoint_type');\n";
                    }

                    $assets = $mixin->get('assets');
                    if (is_array($assets)) {
                        $nc_core->page->require_assets($assets);
                    }

                    $prev_breakpoint = $breakpoint;
                }
            }
        }

        $nc_core->page->add_styles($styles, $priority);

        if ($breakpoint_type === 'block') {
            $nc_core->page->require_asset_once('css_element_queries', array('defer' => false));
        }

        if ($scripts) {
            $nc_core->page->require_asset_once('nc_mixin_init', array('embed' => true));
            $nc_core->page->add_javascript($scripts);
        }

        return $styles;
    }

    /**
     * @param int $prev_breakpoint
     * @param int $breakpoint
     * @return string
     */
    protected static function get_block_query($prev_breakpoint, $breakpoint) {
        $block_selector = '';
        if ($prev_breakpoint != 0) {
            $block_selector .= '[min-width~="' . $prev_breakpoint . 'px"]';
        }
        if ($breakpoint != nc_tpl_mixin::MAX_BLOCK_WIDTH) {
            $block_selector .= '[max-width~="' . sprintf('%0.2F', $breakpoint - 0.01) . 'px"]';
        }
        return $block_selector;
    }

    /**
     * @param int $prev_breakpoint
     * @param int $breakpoint
     * @return string
     */
    protected static function get_media_query($prev_breakpoint, $breakpoint) {
        $media_queries = array();
        if ($prev_breakpoint != 0) {
            $media_queries[] = '(min-width: ' . $prev_breakpoint . 'px)';
        }
        if ($breakpoint != nc_tpl_mixin::MAX_BLOCK_WIDTH) {
            $media_queries[] = '(max-width: ' . sprintf('%0.2F', $breakpoint - 0.01) . 'px)';
        }
        return join(' and ', $media_queries);
    }

    /**
     * @param array $own_settings
     * @param array $preset_settings
     * @return array
     */
    protected static function combine_own_settings_with_preset(array $own_settings, array $preset_settings) {
        foreach ($preset_settings as $key => $value) {
            if (is_array($value)) {
                if (!isset($own_settings[$key])) {
                    $own_settings[$key] = array();
                }
                $own_settings[$key] = self::combine_own_settings_with_preset($own_settings[$key], $preset_settings[$key]);
            } else if (!isset($own_settings[$key])) {
                $own_settings[$key] = $preset_settings[$key];
            }
        }
        return $own_settings;
    }

}