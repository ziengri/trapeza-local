<form method="GET" action="/bc/modules/bitcat/index.php" class="ajax2">
    <div class="modal-body">
        <input type="hidden" name="bc_copy_action" value="copyObject" />
        <input type="hidden" name="class_id" value="<?= $classId ?>" />
        <input type="hidden" name="object_id" value="<?= $object->getProperty('Message_ID') ?>" />
        <p><b>Копирование объекта №<?= $object->getProperty('Message_ID') ?> - <?= $object->getProperty('name') ?></b></p>
        <?php
                function recurciveOptionsSub($subTree, $level = 0) {
                    $options = '';
                    $prefix = str_repeat("&emsp;", $level);
                    foreach ($subTree as $sub) {
                        $options .= "<option value='{$sub['Sub_Class_ID']}'>{$prefix}{$sub['Subdivision_Name']}</option>";
                        if (!empty($sub['children'])) {
                            $options .= recurciveOptionsSub($sub['children'], $level + 1);
                        }
                    }
                    return $options;
                }
            ?>
        <div class='colline colline-1'><?= bc_select("sub_class_id", recurciveOptionsSub($subTree), "Куда копируем", "class='ns'") ?></div>        
    </div>
    <div class="bc_submitblock">
        <div class='result'></div>
        <span class="btn-strt"><?= nc_submit_button('Копировать') ?></span>    
    </div>
</form>