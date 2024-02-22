<?php

function nc_area($area_keyword) {
    global $current_cc;
    $nc_core = nc_core::get_object();

    $original_cc = $current_cc;
    $blocks_in_area = $nc_core->sub_class->get_by_area_keyword($area_keyword, $nc_core->page->get_routing_result());

    if (!$nc_core->admin_mode) {
        $blocks_in_area = array_filter($blocks_in_area, function($block) {
            return (bool)$block['Checked'];
        });
    }

    $area_css_classes = 'nc-area tpl-area-' . nc_camelcase_to_dashcase($area_keyword);
    $list_css_classes = 'tpl-block-list';

    if ($area_keyword === 'main') {
        $list_css_classes .= ' tpl-area-main-list'; // см. nc_page::add_page_mixins()
    }

    $result = '';
    $result .= "<div class='$area_css_classes'>";

    if ($blocks_in_area) {
        $result .= "<div class='$list_css_classes'>";
        foreach ($blocks_in_area as $block) {
            $current_cc = $nc_core->sub_class->set_current_by_id($block['Sub_Class_ID']);
            $result .= nc_objects_list($area_keyword, $block['Sub_Class_ID'], '');
        }
        $result .= '</div>';
    } else if ($nc_core->admin_mode) {
        $result .= "<div class='nc-container nc--empty'>";
        $result .= nc_admin_infoblock_insert_toolbar(0, $area_keyword, 0, 'first', 0);
        $result .= "</div>";
    }

    $result .= '</div>';

    $current_cc = $original_cc;

    return $result;
}