<table class="nc-table nc--bordered nc--small">

<?php  if ($result['import_table']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_NEW_TABLES ?></th>
    </tr>
    <?php  foreach ($result['import_table'] as $table => $new_table): ?>
    <tr>
        <td><?=$table ?></td>
        <td><span class="nc-label nc--green"><?=$new_table ?></span></td>
    </tr>
    <?php  endforeach ?>
<?php  endif ?>



<?php  if ($result['import_data']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_STEP_DATA ?></th>
    </tr>
    <?php  foreach ($result['import_data'] as $table => $count): ?>
    <tr>
        <td><?=$table ?></td>
        <td><span class="nc-label nc--green"><?=$count ?></span></td>
    </tr>
    <?php  endforeach ?>
<?php  endif ?>



<?php  if ($result['import_file']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_STEP_FILES ?></th>
    </tr>
    <?php  foreach ($result['import_file'] as $path => $files): ?>
    <?php  if ($path): ?>
        <tr>
            <td colspan="2"><span class="nc-label"><?=$path ?></span></td>
        </tr>
    <?php  endif ?>
    <?php  foreach ($files as $file => $status): ?>
    <tr>
        <td><?=$file ?></td>
        <td><span class="nc-label nc--<?=$status=='OK' ? 'green' : ($status =='SKIP' ? 'yellow' : 'red') ?>"><?=$status ?></span></td>
    </tr>
    <?php  endforeach ?>
    <?php  endforeach ?>
<?php  endif ?>

</table>