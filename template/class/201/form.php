<?php 
$ofices = $db->get_results("select Message_ID, name, adres from Message2012 where Catalogue_ID = '$catalogue'",ARRAY_A);
if ($ofices) {
    $officeOpt = "<option>- не выбрано -</option>";
	foreach($ofices as $of) {
		$officeOpt.="<option ".($f_office==$of[Message_ID] || $contactid==$of[Message_ID] ? "selected" : NULL)." value='{$of[Message_ID]}'>".($of[name] ? $of[name] : $of[adres])."</option>";
	}
}
?>
<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_description">Описание</a></li>
    <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id="tab_main">
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Имя", "maxlength='255' size='50'", 1)?></div>
        <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Описание / должность")?></div>
        <div class='colline colline-height colline-3'><?=bc_textarea("f_phone", $f_phone, "Телефон (с новой строки)")?></div>
        <div class='colline colline-3'><?=bc_input("f_email", $f_email, "E-mail", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3'><?=bc_checkbox("f_mailsend", 1, "Форма связи с менеджером", $f_mailsend)?></div>
        <div class='colline colline-2'><?=bc_file("f_photo", $f_photo_old, "Фотография", $f_photo, 2736)?></div>
        <div class='colline colline-2'><?=bc_select("f_office", $officeOpt, "Точка / офис", "class='ns'")?></div>
    </div>
    <div class='none' id='tab_description'>
        <div class='colline colline-height'><?=bc_textarea("f_description_full", $f_description_full, "Полное описание", "data-ckeditor='1'")?></div>
    </div>
    <div id='tab_targeting' class='none'>
		<?=nc_city_field($f_citytarget); ?>
	</div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription)?>
</div>