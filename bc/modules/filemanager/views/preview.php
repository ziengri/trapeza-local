<?php
if (!class_exists('nc_core')) {
    die;
}
$nc_core = nc_core::get_object();
?>
<script src='<?= nc_add_revision_to_url($self_folder . 'filemanager.js') ?>' type='text/javascript'></script>


<div class='block_manager' style="margin-right:15px">
	<?php  if(count($breadcrumbs)>1): ?>
	<div class="nc-padding-10">
		<?php  foreach ($breadcrumbs as $i => $row): ?>
			<?php  if($i+1 == count($breadcrumbs)): ?>
				<?=$row['title'] ?>
			<?php  else: ?>
				<a href="<?=$row['link'] ?>"><?=$row['title'] ?></a> /
			<?php  endif ?>
		<?php  endforeach ?>
		&nbsp;<a href="#" onclick="nc_filemanagerObj.show_link_panel('<?=trim($dir, '/') ?>', 0); return false;">
			<i class="nc-icon nc--hovered nc--mod-linkmanager"></i>
		</a>
	</div>
	<br>
	<?php  endif ?>
	
	<span class="nc-label"><?=nc_bytes2size( filesize($file) ) ?></span>
	<span class="nc-label"><?=date("d.m.Y H:i:s", filemtime($file)) ?></span>
	
	<?php  if($image): ?>
		<span class="nc-label"><?=$image[0] ?>x<?=$image[1] ?>px</span>
		<span class="nc-label"><?=$image['mime'] ?></span>
		
		<div class="nc-box">
			<img src="<?=$image['path'] ?>" alt="">
		</div>
	<?php  endif ?>

	<?php  if($source): ?>
		<div class="nc-code nc-box">
			<?=$source ?>
		</div>
	<?php  endif ?>
	

</div>
<br>
<br>

<?php  /* Modal: Copy link */ ?>
<div id='nc_filemanager_link_panel' style='display:none; padding-right:25px'>
	<div id='nc_filemanager_link_panel_body' class='nc-form'>
		<br>
		<input type="text" id='nc_filemanager_link_absolute' class="nc--blocked" onfocus='this.select()'>
		<input type="text" id='nc_filemanager_link_global' class="nc--blocked" onfocus='this.select()'>
		<input type="text" id='nc_filemanager_link_server' class="nc--blocked" onfocus='this.select()'>
		<br><br>
	</div>
	<div class='nc_admin_form_buttons'>
		<button type='button' id='nc_filemanager_panel_close' class='nc-btn nc--left' onclick='$nc.modal.close()'><?=NETCAT_MODULE_FILEMANAGER_ADMIN_PANEL_CLOSE ?></button>
	</div>
</div>

<script type='text/javascript'>
	nc_filemanagerObj = new nc_Filemanager({
		MODULE_PATH:'<?=$this->MODULE_PATH ?>',
		url_prefix: '<?=$this->url_prefix ?>',
		DOCUMENT_ROOT: '<?=$nc_core->DOCUMENT_ROOT ?>',
		HTTP_HOST: '<?=$nc_core->HTTP_HOST ?>'
	});
	</script>