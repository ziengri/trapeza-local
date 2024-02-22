<?php


class nc_ui_controls extends nc_ui_common {

    protected static $obj;

    public function render() {
        return "";
    }

    public static function get() {
        if (is_null(self::$obj)) {
            self::$obj = new self();
        }
        self::$obj->reset();
        return self::$obj;
    }


    /**
     * Кнопка/индикатор ВКЛ/ВЫКЛ с параметрами для POST-запроса.
     * В параметрах запроса устанавливает:
     *  — значение "action" = "toggle" (если не задано другое значение);
     *  — значение "enable", равное инвертированному аргументу $is_enabled (0 или 1).
     *
     * @param bool $is_enabled   Текущее состояние объекта
     * @param array $parameters  Параметры POST-запроса для включения/выключения
     * @return string
     */
    public function toggle_button($is_enabled, $parameters) {
        if (!isset($parameters['action'])) { $parameters['action'] = 'toggle'; }
        $parameters['enable'] = intval(!$is_enabled);

        $color = ($is_enabled ? 'green' : 'red');

        /** @var nc_ui_label $result */
        $result = nc_ui_label::get($is_enabled ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF)
                    ->post_vars($parameters)
                    ->$color();

        return $result->render(); // :-(
    }

    /**
     * Кнопка удаления с параметрами для POST-запроса.
     * В параметрах запроса устанавливает "action" = "remove", если не указано другое значение.
     *
     * @param string $confirmation_text
     * @param array $parameters
     * @return string
     */
    public function delete_button($confirmation_text, array $parameters) {
        if (!isset($parameters['action'])) { $parameters['action'] = 'remove'; }

        /** @var nc_ui_html $result */
        $result = nc_ui_html::get('a')
                    ->icon('remove')
                    ->post_vars($parameters)
                    ->attr('data-confirm-message', $confirmation_text);

        return $result->render();
    }

    /**
     * Селектор сайта
     * @param int $current_site_id
     * @param bool $include_all_sites_option
     * @return string
     */
    static public function site_select($current_site_id, $include_all_sites_option = false) {
        // @todo remove that 'helper' when global site selector is introduced
        $sites = nc_core('catalogue')->get_all();
        $options = array();

        if ($include_all_sites_option) {
            $options[0] = CONTROL_USER_SELECTSITEALL;
        }

        foreach ($sites as $id => $row) {
            $options[$id] = $id . '. ' . $row['Catalogue_Name'];
        }

        $site_selector_id = 'nc_admin_site_select';
        $site_selector = nc_core('ui')->html
                              ->select('site_id', $options, $current_site_id)
                              ->attr('id', $site_selector_id);

        $nc = '$nc';
        $site_selector .= "<script>
            (function() {
                \$nc('#{$site_selector_id}').change(function() {
                    var re = /site_id=\d+/,
                        loc = location.href,
                        new_site_id = \$nc(this).val(),
                        new_site_loc = 'site_id=' + new_site_id;
                    if (re.test(loc)) {
                        location.href = loc.replace(/site_id=\d+/, new_site_loc);
                    }
                    else {
                        location.href += (loc.indexOf('?')>=0 ? '&' : '?' ) + new_site_loc;
                    }
                });
            })();
        </script>";

        return $site_selector;
    }

}