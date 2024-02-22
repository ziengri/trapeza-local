<div class="modal-body">
    <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название компании", "maxlength='255' size='50'", 1)?></div>
    <div class='colline colline-1'><?=bc_file("f_logo", $f_logo_old, "Логотип", $f_logo, 2528)?></div>
    <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Описание", "data-ckeditor='1'")?></div>
    <div class='colline colline-3'><?=bc_input("f_link", $f_link, "Ссылка на сайт", "maxlength='255' size='50'")?></div>
    <div class='colline colline-3'><?=bc_checkbox("f_nolink", 1, "Без внутренней страницы", $f_nolink)?></div>
    <div class='colline colline-3'><?=bc_input("f_logolink", $f_logolink, "Ссылка с логотипа", "maxlength='255' size='50'")?></div>
    <div class='colline colline-height'><?=bc_textarea("f_text2", $f_text2, "Доп. описание (снизу)", "data-ckeditor='1'")?></div>
    <?=editItemChecked(2, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID, '', $f_lang)?>
</div>