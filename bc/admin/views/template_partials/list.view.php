<?php
if (!class_exists('nc_core')) {
    die;
}
?>
<?php  if ($partials): ?>
    <table class='nc-table nc--bordered nc--striped nc--wide'>
    <?php  foreach ($partials as $partial => $partial_data): ?>
    	<tr>
    		<td><a href='<?=$action_url ?>edit&partial=<?=$partial ?>'><?=
                $partial_data['Description'] ? "$partial_data[Description] ($partial)" : $partial
        ?></a></td>
    		<td width=1>
    			<a href='<?=$action_url ?>edit&partial=<?=$partial ?>'><i class='nc-icon nc--edit'></i></a>
    		</td>
    		<td width=1>
    			<a href='<?=$action_url ?>remove&partial=<?=$partial ?>'><i class='nc-icon nc--remove'></i></a>
    		</td>
    	</tr>
    <?php  endforeach ?>
    </table>
<?php  else: ?>
    <?php  nc_print_status(CONTROL_TEMPLATE_PARTIALS_NOT_EXISTS, 'info') ?>
<?php  endif ?>