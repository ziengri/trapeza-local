<?php
if (!class_exists('nc_core')) {
    die;
}
?>

<?php  if (!empty($result['error'])): ?>
    <div class="nc-alert nc--red"><b><?= TOOLS_DATA_BACKUP_IMPORT_ERROR ?></b><br><?=$result['error'] ?></div>
<?php  endif ?>
<?php  if (!empty($result['success'])): ?>
    <div class="nc-alert nc--green"><?=$result['success'] ?></div>
<?php  endif ?>

<?php  if (!empty($result['link'])): ?>
    <a class='nc-btn nc--mini' href="<?=$result['link'] ?>" onclick="nc.root.window.location = this.href"><?=TOOLS_DATA_BACKUP_GOTO_OBJECT ?></a>
    <hr>
<?php  endif ?>

<table class="nc-table nc--bordered nc--striped">
    <?php  if (!empty($result['total_insert_rows'])): ?>
        <tr>
            <td><?= TOOLS_DATA_BACKUP_INSERT_OBJECTS ?></td>
            <td><span class="nc-label nc--green"><?= $result['total_insert_rows'] ?></span></td>
        </tr>
    <?php  endif ?>

    <?php  if (!empty($result['total_create_tables'])): ?>
        <tr>
            <td><?= TOOLS_DATA_BACKUP_CREATE_TABLES ?></td>
            <td><span class="nc-label nc--green"><?= $result['total_create_tables'] ?></span></td>
        </tr>
    <?php  endif ?>

    <?php  if (!empty($result['total_copied_files'])): ?>
        <tr>
            <td><?= TOOLS_DATA_BACKUP_COPIED_FILES ?></td>
            <td><span class="nc-label nc--green"><?= $result['total_copied_files'] ?></span></td>
        </tr>
    <?php  endif ?>

    <?php  if (!empty($result['total_replaced_files'])): ?>
        <tr>
            <td><?= TOOLS_DATA_BACKUP_REPLACED_FILES ?></td>
            <td><span class="nc-label nc--yellow"><?= $result['total_replaced_files'] ?></span></td>
        </tr>
    <?php  endif ?>

    <?php  if (!empty($result['total_skipped_files'])): ?>
        <tr>
            <td><?= TOOLS_DATA_BACKUP_SKIPPED_FILES ?></td>
            <td><span class="nc-label nc--red"><?= $result['total_skipped_files'] ?></span></td>
        </tr>
    <?php  endif ?>
</table>