<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_full">Полный текст</a></li>
    <li class="tab"><a href="#tab_anons">Анонс для главной страницы</a></li>
    <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
	<div id='tab_full'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'")?></div>
	    <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "", "data-ckeditor='1'")?></div>
        <div class='colline colline-1'><?=bc_checkbox("f_spoler", 1, "Спрятать текст под спойлер", $f_spoler)?></div>
	</div>

	<div class='none' id='tab_anons'>
	    <div class='colline colline-height'><?=bc_textarea("f_anons", $f_anons, "", "data-ckeditor='1'")?></div>
	</div>

	<div class='none' id='tab_targeting'>
		<?= nc_city_field($f_citytarget) ?>
	</div>
	<?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID, '', $f_lang)?>
</div>