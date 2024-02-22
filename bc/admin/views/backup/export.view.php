<?php
if (!class_exists('nc_core')) {
    die;
}

$form = $ui->form($action_url . 'export_run')->multipart()->horizontal()->id('export');
$form->add_row(TOOLS_DATA_BACKUP_DATATYPE)->select('type', $types)->id('export_type');
$form[] = "<div id='type_form'></div>";
?>

<?=$form ?>

<script>
nc('#export_type').change(function(){
    nc('#type_form').html('');
    if (!this.value) return;
    nc.process_start('backup.export_form');
    nc.$.ajax({
        url: '<?=$action_url ?>export_form&type=' + this.value,
    }).done(function(data){
        nc.process_stop('backup.export_form');
        nc('#type_form').html(data);
    });
});
</script>

<hr>

<?php  if ($export_files): ?>
<table class="nc-table nc--bordered nc--striped">
    <tr>
        <th><?=NETCAT_MODERATION_ID ?></th>
        <th><?=TOOLS_DATA_BACKUP_DATATYPE ?></th>
        <th><?=TOOLS_DATA_BACKUP_EXPORT_DATE ?></th>
        <th><?=TOOLS_DUMP_INC_SIZE ?></th>
        <th><?=TOOLS_DOWNLOAD ?></th>
    </tr>
    <?php  $i = 0 ?>
    <?php  $total_size = 0 ?>
    <?php  foreach ($export_files as $file): ?>
    <?php  $total_size += $file['size'] ?>
    <tr>
        <td>
            <?php  if ($file['netcat_link']): ?>
                <a href="<?=$file['netcat_link'] ?>">
            <?php  endif ?>
                <?=$file['id'] ? $file['id'] : '-' ?>. <?=$file['title'] ?>
            <?php  if ($file['netcat_link']): ?>
                </a>
            <?php  endif ?>
        </td>
        <td class="nc-text-grey">
            <?php  if ($file['sprite']): ?><i class='nc-icon <?=$file['sprite'] ?>'></i><?php  endif ?>
            <?=$file['type_name'] ?>
        </td>
        <td><?=date('d.m.Y - H:i:s', $file['time']) ?></td>
        <td class="nc-text-right">
            <span class="nc-label nc--small nc--<?=$file['size'] < 1024 * 1024 ? 'grey' : ($file['size'] < 1024 * 1024 * 10 ? 'dark' : 'darken') ?>"><?=$file['size_formated'] ?> </span>
        </td>
        <td><a class='nc-btn nc--mini nc--blue' href='<?=$file['link'] ?>'><i class="nc-icon nc--white nc--download"></i> <?=TOOLS_DOWNLOAD ?></a></td>
    </tr>
    <?php  endforeach ?>
    <?php  $size_percent = ceil(($total_size / $export_limit_size) * 100) ?>
    <tr>
        <td colspan="1">
            <div class="nc-progress nc--small nc--<?=$size_percent > 80 ? 'yellow' : 'blue' ?>" style='margin:10px 0'>
                <div class="nc-progress-bar" style='width:<?=$size_percent ?>%'></div>
            </div>
        </td>
        <td colspan="3">
            <?=nc_bytes2size($total_size) ?> <?=TOOLS_DATA_BACKUP_SPACE_FROM ?> <?=nc_bytes2size($export_limit_size) ?> <?=TOOLS_DATA_BACKUP_USED_SPACE ?>
        </td>
        <td>
            <a class='nc-text-red' href='<?=$action_url ?>remove_export_files' onclick='return confirm("<?=TOOLS_DATA_BACKUP_DELETE_ALL_CONFIRMATION ?>")'><i class="nc-icon nc--remove"></i> <?=NETCAT_MODERATION_REMALL ?></a>
        </td>
    </tr>
</table>
<?php  endif ?>