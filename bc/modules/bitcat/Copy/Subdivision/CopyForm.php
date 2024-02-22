<form method="GET" action="/bc/modules/bitcat/index.php" class="ajax2">
    <div class="modal-body">
        <input type="hidden" name="bc_copy_action" value="copySubdivison" />
        <input type="hidden" name="sub_id" value="<?= $subdivision['Subdivision_ID'] ?>" />
        <p><b>Копирование раздела №<?= $subdivision['Subdivision_ID'] ?> - <?= $subdivision['Subdivision_Name'] ?></b></p>
        <?php
            function recurciveOptionsSub($subTree, $level = 0) {
                $options = '';
                $prefix = str_repeat("&emsp;", $level);
                foreach ($subTree as $sub) {
                    $options .= "<option value='{$sub['Subdivision_ID']}'>{$prefix}{$sub['Subdivision_Name']}</option>";
                    if (!empty($sub['children'])) {
                        $options .= recurciveOptionsSub($sub['children'], $level + 1);
                    }
                }
                return $options;
            }
        ?>
        <div class='colline colline-1'><?= bc_select("parent_sub_id", recurciveOptionsSub($subTree), "Куда копируем", "class='ns'") ?></div>
        <div class='colline colline-1'><?= bc_checkbox("copy_objects", 1, 'Копировать вместе с объектами', false) ?></div>
    </div>
    <div class="bc_submitblock">
        <div class='result'></div>
        <span class="btn-strt"><?= nc_submit_button('Копировать') ?></span>    
    </div>
</form>