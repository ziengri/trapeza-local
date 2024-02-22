<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <!-- <li class="tab"><a href="#tab_files">Файлы</a></li> -->
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Заголовок", "maxlength='255' size='50'", 1)?></div>
    
    <!-- <div id='tab_files' style='font-size: 15px;'> -->
        <div class='sub-item-main' style='font-size: 15px;'>
            <div class='sub-item-items'>
            <?php 
            $dir = "/var/www/krza/data/www/krza.ru/a/speakenglish/lessons_files/"; // изменить папку
            $i = 0;
            $response = scan($dir);
            function scan($dir) {
                $files = array();
                if(file_exists($dir)){
                    $i = 0;
                    foreach(scandir($dir) as $f) {
                        if(!$f || $f[0] == '.') {
                            continue; 
                        }
                        if(is_dir($dir . '/' . $f)) {
                            $files[] = array(
                                "name" => $f,
                                "type" => "folder",
                                "path" => $dir . '/' . $f,
                                "items" => scan($dir . '/' . $f)
                            );
                        }
                        else {
                            $files[] = array(
                                "id" => uniqid(),
                                "name" => $f,
                                "type" => "file",
                                "path" => $dir . '/' . $f,
                                "size" => filesize($dir . '/' . $f) 
                            );
                        }
                        $i++;
                    }
                }
                return $files;
            }
            function re_make_menu($data) {
                if($data['type'] == 'folder') {
                    $m = "<div class='switch'>
                        <label>
                            <span class='sw-text'>{$data['name']}</span>
                        </label>";
                } else {
                    $m = "<div class='switch'>
                        <label>
                            <input type='checkbox' value='{$data['path']}' name='files[{$data['id']}][path]' {$data['checked']}>
                            <span class='lever'></span>
                            <span class='sw-text'>{$data['name']}</span>
                        </label>";
                }
                if(isset($data['items'])) {
                    $m .= create_child($data['items']);
                }
                $m .= "</div>";
            
                return $m;
            }
            function create_child($items) {
                $levers = "<div class='sub-item-child'>";
                
                foreach($items as $item) {
                    if($item['type'] == 'folder') {
                        $levers .= "<div class='sub-item' style='padding-left:10px;'>
                                        <div class='switch'>
                                            <label>
                                                <span class='sw-text'>{$item['name']}</span>
                                            </label>
                                        </div>";
                    } else {
                        $levers .= "<div class='sub-item' style='padding-left:10px;'>
                                        <div class='switch'>
                                            <label>
                                                <input type='checkbox' value='{$item['path']}' name='files[{$item['id']}][path]' {$item['checked']}>
                                                <span class='lever'></span>
                                                <span class='sw-text'>{$item['name']}</span>
                                            </label>
                                            <label>
                                                <input class='priority_text' type='text' value='' placeholder='Приоритет файла' name='files[{$item['id']}][priority]'>
                                            </label>
                                        </div>";
                    }
                    if(isset($item['items'])) {
                        $levers .= create_child($item['items']);
                    }
                    $levers .= "</div>";
                }
                $levers .= "</div>";
                return $levers;
            }
            $checkedAr = explode(",", trim($f_files, ","));

            function rec($array, $checked) {
                foreach($array as &$ar) {
                    if(in_array($ar['path'], $checked)) {
                        $ar['checked'] = 'checked';
                    }
                    if($ar['items']) {
                        $ar['items'] = rec($ar['items'], $checked);
                    }
                }

                return $array;
            }
            $response = rec($response, $checkedAr);

            foreach($response as $resp) {
                echo(re_make_menu($resp));
            }
            // var_dump();
            ?>
            </div>
            </div>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>