<?php

/* $Id: ui_config.php 6207 2012-02-10 10:14:50Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");

/**
 * Класс для облегчения формирования UI в модулях
 */
class ui_config_module_linkmanager extends ui_config_module {

    public function __construct($active_tab = 'admin', $toolbar_action = 'stats') {
        global $db;
        global $MODULE_FOLDER;

        $this->ui_config_module('linkmanager', $active_tab);

        if ($active_tab = 'admin') {

            $this->toolbar[] = array('id' => "stats",
                    'caption' => NETCAT_MODULE_LINKS_STATS,
                    'location' => "module.linkmanager(stats)",
                    'group' => "grp1"
            );
            $this->toolbar[] = array('id' => "settings",
                    'caption' => NETCAT_MODULE_LINKS_SETTINGS,
                    'location' => "module.linkmanager(settings)",
                    'group' => "grp1"
            );
            $this->toolbar[] = array('id' => "templates",
                    'caption' => NETCAT_MODULE_LINKS_EMAIL_TEMPLATES,
                    'location' => "module.linkmanager(templates)",
                    'group' => "grp1"
            );

            $this->locationHash = "module.linkmanager(".$toolbar_action.")";
            $this->activeToolbarButtons[] = $toolbar_action;
        }
    }

}
?>
