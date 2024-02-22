<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-2'><?=bc_file("f_banner", $f_banner_old, "Изображение", $f_banner, 2745)?></div>
        <div class='colline colline-2'><?=bc_input("f_name", $f_name, "Заголовок", "maxlength='255' size='50'")?></div>
        <div class='colline colline-height colline-2'><?=bc_textarea("f_text", $f_text, "Текст")?></div>
        <div class='colline colline-2'><?=bc_input("f_link", $f_link, "Ссылка", "maxlength='255' size='50'")?></div>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>