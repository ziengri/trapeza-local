<div class="modal-body">
    <div class='colline colline-1'><?=bc_date('f_Created', $f_Created, "Дата", 1, 1)?></div>
    <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Имя", "maxlength='255' size='50'", 1)?></div>
    <div class='colline colline-height'><?=bc_textarea("f_otzyvtext", $f_otzyvtext, "Текст")?></div>
    <div class='colline colline-height'><?=bc_textarea("f_otvet", $f_otvet, "Ответ")?></div>
</div>