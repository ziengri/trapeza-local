<?php
/* $Id: ui_config.php 4978 2011-07-11 12:38:26Z andrey $ */
if ( !class_exists("nc_System") ) die("Unable to load file.");
/**
  * ����� ��� ���������� ������������ UI � �������
  */
class ui_config_module_bitcat extends ui_config_module {

  public $headerText = NETCAT_MODULE_BITCAT;
  public $headerImage = 'i_module_bitcat_big.gif';

  public function __construct($active_tab = 'admin', $toolbar_action = 'main', $hash = '') {
    global $db;
    global $MODULE_FOLDER;



    if ($active_tab == 'admin') {
        $this->tabs[] = array(
            'id' => 'admin',
            'caption'=> NETCAT_MODULE_BITCAT,
            'location' => "module.bitcat.".$toolbar_action
        );
    }
    else {
        $this->tabs[] = array(
            'id' => 'admin',
            'caption'=> NETCAT_MODULE_BITCAT,
            'location' => "module.bitcat.main"
        );
    }
    $this->tabs[] = array(
      'id' => 'settings',
      'caption' => TOOLS_MODULES_MOD_PREFS,
      'location' => "module.bitcat.settings"
    );

    $this->activeTab = $active_tab;
    $this->treeMode = "modules";

    if ($active_tab == 'admin') {
      $this->toolbar[] = array(
        'id' => "main",
        'caption' => NETCAT_MODULE_BITCAT_ADMIN_TEMPLATE_LIST_TAB,
        'location' => "module.bitcat.main",
        'group' => "bitcat"
      );
      $this->toolbar[] = array(
        'id' => "constructor",
        'caption' => NETCAT_MODULE_BITCAT_ADMIN_TEMPLATE_CONVERTER_TAB,
        'location' => "module.bitcat.constructor",
        'group' => "bitcat"
      );


    }

    if ( $active_tab == 'settings' ) {
      if ( !$toolbar_action ) $toolbar_action = 'settings';
      $this->toolbar[] = array(
        'id' => "settings",
        'caption' => NETCAT_MODULE_BITCAT_ADMIN_TEMPLATE_TEMPLATE_MAIN,
        'location' => "module.bitcat.settings",
        'group' => "bitcat"
      );
      $this->toolbar[] = array(
        'id' => "template",
        'caption' => NETCAT_MODULE_BITCAT_ADMIN_TEMPLATE_TEMPLATE_TAB,
        'location' => "module.bitcat.template",
        'group' => "bitcat"
      );
    }

    if ($toolbar_action) $this->locationHash = "module.bitcat.".($hash ? $hash : $toolbar_action);
      $this->activeToolbarButtons[] = $toolbar_action;
  }
}

?>
