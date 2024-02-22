<?php

/**
 * Класс для реализации поля типа "Файл - Изображение"
 */
class nc_a2f_field_file_image extends nc_a2f_field_file {

    protected $resize_w;
    protected $resize_h;

    public function get_extend_parameters() {
        return array('resize_w' => array('type' => 'int', 'caption' => NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_W),
            'resize_h' => array('type' => 'int', 'caption' => NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_H));
    }

    public function save($value) {
        $nc_core = nc_Core::get_object();

        parent::save($value);

        if ($this->upload && $this->resize_w && $this->resize_h) {
            nc_ImageTransform::imgResize($this->filepath, $this->filepath, $this->resize_w, $this->resize_h);
            clearstatcache(true, $this->filepath);
            $this->filesize = filesize($this->filepath);
            if ($this->filesize) {
                $paths = explode('/', $this->filepath);
                $put_file_name = $paths[count($paths) - 1];
                $this->value = $put_file_name . ':' . $this->filetype . ':' . $this->filesize . ':cs/' . $put_file_name;
                $this->filename = $put_file_name;
            }
            else {
                $this->value = '';
            }
        }
    }

    public function render_value_field($html = true) {
        $ret = "<input name='" . $this->get_field_name() . "' type='file' style='width:100%;'/>";
        // старый файл
        if ($this->value) {
            $ret = parent::render_value_field(FALSE);
            $ret .= "<input type='hidden' name='subtype' value='image' /><br/>\r\n";
        }
        if ($html) {
            $ret = "<div class='ncf_value'>" . $ret . "</div>\n";
        }

        return $ret;
    }
}