
<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_right">Полное описание (Справа)</a></li>
    <li class="tab"><a href="#tab_bottom">Полное описание (Внизу)</a></li>
    <? if($setting['targeting']){ ?> <li class="tab"><a href="#tab_targeting">Таргетинг</a></li> <? } ?>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'", 1)?></div>
        <div class='colline colline-1'><?=bc_input("f_alterTitle", $f_alterTitle, "Альтернативный заголовок портфолио", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1 colline-line'><?=bc_text_standart("Превью")?></div>
        <div class='colline colline-1'><?=bc_file("f_photo_preview", $f_photo_preview_old, "Выберите превью", $f_photo_preview, 2934)?></div>
        <? $f_photo->settings->resize(850, 3500)->preview(750, 3500)->use_name('Описание фото'); ?>
        <div class='colline colline-height'><?=gv_multifile_field($f_photo, "Фотографии")?></div>
        <div class='colline colline-2'><?=bc_selectionForm("f_form_id", $f_form_id)?></div>
        <div class='colline colline-2'><?=bc_checkbox("f_bottom_form", 1, "Вывести форму снизу \"Остались вопросы ?\"", $f_bottom_form, "data-twoline")?></div>
        <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Описание в превью", "data-ckeditor='1'")?></div>
        <div class='colline colline-2'><?=bc_input("f_price", $f_price, "Цена (только число)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_checkbox("f_firstprice", 1, "Это нижняя граница<br>цены товара (от)", $f_firstprice, "data-twoline")?></div>
        <div class='colline colline-2'><?=bc_input("f_link", $f_link, "Ссылка", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_input("f_video", $f_video, "Ключ видео YouTube", "maxlength='255' size='50'")?></div>
    </div>
    <div class='none' id='tab_right'>
        <div class='colline colline-height'><?=bc_textarea("f_textfull", $f_textfull, "Полное описание (Справа)", "data-ckeditor='1'")?></div>
    </div>
    <div class='none' id='tab_bottom'>
        <div class='colline colline-height'><?=bc_textarea("f_textfull_bottom", $f_textfull_bottom, "Полное описание (Внизу)", "data-ckeditor='1'")?></div>
    </div>
    <div class='none' id='tab_targeting'>
        <?=($setting['targeting'] ? nc_city_field($f_citytarget) : "")?>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>
