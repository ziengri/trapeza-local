<?php

class nc_landing_preset_netcat_landing_goods_2_block_gallery extends nc_landing_preset_block {

    protected $component = 'netcat_page_block_gallery';
    protected $default_component_template = 'grid';
    protected $default_infoblock_keyword = 'gallery';
    protected $default_infoblock_name = 'Галерея';
    protected $ignore_user_objects = true;

    
    protected function get_objects_properties($infoblock_id, $settings, array $landing_data) {
        $item = $landing_data['item'];
        $objects = array();

        if ($item) {
            if ($item['Slider']) {
                foreach ($item['Slider']->records as $image) {
                    $objects[] = array(
                        'SlideDescription' => $image['Name'],
                        'Slide' => $image['Path'],
                    );
                }
            }
            else {
                $nc_core = nc_core::get_object();
                $component = $nc_core->get_component($item['Class_ID']);
                foreach ($item as $property => $value) {
                    if (isset($item[$property . '_url'])) {
                        $file = $nc_core->DOCUMENT_ROOT . $item[$property . '_url'];
                        if (file_exists($file) && substr(nc_file_mime_type($file), 0, 5) == 'image') {
                            $objects[] = array(
                                'SlideDescription' => $component->get_field($property, 'Field_Name'),
                                'Slide' => $item[$property . '_url'],
                            );
                        }
                    }
                }
            }
        }
        else {
            $objects = array(
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone3.jpg')),
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone4.jpg')),
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone5.jpg')),
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone6.jpg')),
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone7.jpg')),
                array('SlideDescription' => '', 'Slide' => $this->get_image_path('phone8.jpg')),
            );
        }

        return $objects;
    }
    

    protected function get_default_infoblock_settings($subdivision_id, $settings, array $landing_data) {
        $item = $landing_data['item'];

        if ($item) {
            $header = $item['Name'];
        }
        else {
            $header = "NetPhone";
        }

        return array(
            'show_header' => 0,
            'header' => $header,
            'show_text' => 0,
            'text' => '',
        );
    }
    
}