<?php  require $settings_array[template_path].'top.php';?>

<table class="table active">
    <thead class="table-head">
        <tr class="table-row bottom-line">
            <td class="table-cell">Группа</td>
            <td class="table-cell">
                <span style="width: 50%; display: block; float: left;">Подгруппа</span>
                <span style="width: 50%; display: block; float: left;">Примечание</span>
            </td>
        </tr>
    </thead>
    <tbody class="table-body">
    <?php foreach ($groups as $group) {?>
        <tr class="table-row bottom-line">
            <td class="table-cell"><?php echo $group->full_name?></td>
            <td class="table-cell" style="vertical-align: top; padding: 0;">
                <table class="table table-child">
                    <tbody class="table-body">
                    <?php foreach ($group->subgroups as $part) {
                        $url = $hrefPrefix."?type={$type}&mark={$mark}&model={$model}&modification={$modification}&group={$breadcrumbs[5]->url}&subgroup={$part->id}&variant={$part->variant}"; ?>
                        <tr class="table-row bottom-line">
                            <td class="table-cell" style="width: 50%;">
                                <a href="<?=$url?>"><?php echo $part->description ?></a>
                            </td>
                            <td class="table-cell" style="width: 50%;"><?php echo $part->applicability ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php if ($abbreviations && is_array($abbreviations) && count($abbreviations) > 0) { ?>
    <table class="table active">
        <thead class="table-head">
            <tr class="table-row bottom-line">
                <td class="table-cell">Сокращение</td>
                <td class="table-cell">Расшифровка</td>
            </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($abbreviations as $abbreviation) {?>
            <tr class="table-row bottom-line">
                <td class="table-cell"><?php echo $abbreviation->abbreviation?></td>
                <td class="table-cell"><?php echo $abbreviation->description?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php  require $settings_array[template_path].'bottom.php';?>