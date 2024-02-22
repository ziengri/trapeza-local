<?php
    $bannerBlArr = $db->get_results("select a.Message_ID, a.height, a.name, a.Checked from Message2016 as a, Sub_Class as b where a.sub = b.Subdivision_ID AND b.Class_ID = 2004 AND b.Catalogue_ID='$catalogue'", ARRAY_A);
    foreach($bannerBlArr as $v) {
        $bannerBlOpt .= "<option value='".$v['Message_ID']."' ".($v['Message_ID']==$f_block ? "selected" : "")."> № {$v['Message_ID']} {$v['name']} ".(!$v[Checked] ? "[выключен]" : NULL)."</option>";
    }
?>
<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_other">Дополнительно</a></li>
	<?php  if($setting['targeting'] && isField('citytarget',$classID)){ ?>
    	<li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?php  } ?>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
		<div class='colline colline-2'><?=bc_file('f_photo', $f_photo_old, "Фотография-слайд", $f_photo, 142464)?></div>
		<div class='colline colline-2'><?=bc_input("f_photo_alt", $f_photo_alt, "Текст фотографии (alt)", "maxlength='255' size='50'")?></div>
		<div class='colline colline-1'><?=bc_input("f_name", $f_name, "Заголовок", "maxlength='255' size='50'")?></div>
		<div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Дополнительный текст","data-ckeditor='1'")?></div>
		<div class='colline colline-2'><?=bc_select("f_block", $bannerBlOpt, "В каком блоке показывать", "class='ns'")?></div>
		<div class='colline colline-height'><?=bc_listSubsCheckbox('В каких разделах показывать', 'f_inpage', explode(',', trim($f_inpage, ',')))?></div>
		<?=notResize()?>
	</div>
    <div id='tab_other'>
		<div class='colline colline-1'><?=bc_input("f_videolink", $f_videolink, "Ссылка на видео", "maxlength='255' size='50'")?></div>
		<div class='colline colline-1'><?=bc_input("f_link", $f_link, "Ссылка", "maxlength='255' size='50'")?></div>
	</div>
	<?php  if($setting['targeting'] && isField('citytarget',$classID)){ ?>
	    <div id='tab_targeting' class='none'>
			<?=nc_city_field($f_citytarget)?>
	    </div>
    <?php  } ?>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>