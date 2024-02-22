<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Фото</a></li>
	<li class="tab"><a href="#tab_video">Видео</a></li>
    <li class="tab"><a href="#tab_text">Описание</a></li>
    <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
	    <!--<div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название альбома", "maxlength='255' size='50'")?></div>-->
        <?php  $previwSize = $setting[size2010_imagepx] ? $setting[size2010_imagepx] : "480"; ?>
		<?php $f_photo->settings->resize(900, 900)->preview($previwSize, 900)->use_name('Описание фото');?>
	    <div class='colline colline-height'><?=gv_multifile_field($f_photo, "Фотографии (максимум по 20 шт. в один объект)")?></div>
		<?=waterAccept()?>
	</div>
	<div id='tab_video' class='none'>
		<div class='colline colline-1'><?=bc_input("f_video_link", $f_video_link, "Ссылка на ютуб видео", "maxlength='255' size='50'")?></div>
		<div class='colline colline-2'><?=bc_file("f_video", $f_video_old, "Видео", $f_video, 3325)?></div>
		<div class='colline colline-2'><?=bc_file("f_video_preview", $f_video_preview_old, "Изображение", $f_video_preview, 3326)?></div>
	</div>
    <div id='tab_text' class='none'>
	    <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Описание в альбоме (сверху)", "data-ckeditor='1'")?></div>
	    <div class='colline colline-height'><?=bc_textarea("f_text2", $f_text2, "Описание в альбоме (снизу)", "data-ckeditor='1'")?></div>
	</div>
    <div id='tab_targeting' class='none'>
		<?=nc_city_field($f_citytarget); ?>
	</div>

    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID)?>
</div>