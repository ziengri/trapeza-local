<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название документа", "maxlength='255' size='50'", 1)?></div>
        <div class='colline colline-1'><?=bc_file("f_file", $f_file_old, "Закачать документ", $f_file, 2385)?></div>
        <div class='colline colline-1'><?=bc_date('f_date', $f_date, "Дата документа:", 1, 1)?></div>
        <div class='colline colline-1'><?=bc_input("f_filelink", $f_filelink, "Ссылка на файл с другого сайта", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1'><?=bc_file("f_img", $f_img_old, "Изображение", $f_img, 2662)?></div>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>
