<?php  require $settings_array[template_path].'top.php';?>

    <table class="table">
        <thead class="table-head">
        <tr class="table-row bottom-line">
            <td class="table-cell">Номер</td>
            <td class="table-cell">Название</td>
            <td class="table-cell">Описание</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($numbers as $k => $number) { ?>
            <tr class="table-row bottom-line">
                <td class="table-cell">
                    <a href="<?=$hrefPrefix."?type={$number->type}&mark={$number->mark_short_name}&modelId={$number->model_short_name}&groupId={$number->group_short_name}&number={$number->number}"?>">
                        <?=$number->number?>
                    </a>
                </td>
                <td class="table-cell"><?php echo $number->name ?></td>
                <td class="table-cell"><?php echo $number->modification ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php  require $settings_array[template_path].'bottom.php';?>