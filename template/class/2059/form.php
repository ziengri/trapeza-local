<ul class="tabs tabs-border">
    <li class="tab"><a href="#tab_t1">Основное</a></li>
    <li class="tab"><a href="#tab_t2">Дополнительно</a></li>
</ul>
<div class="modal-body tabs-body">
	<div id='tab_t1'>
		<div class='colline colline-2'><?=bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'", 1)?></div>
		<div class='colline colline-2'><?=bc_checkbox("f_politika", 1, "Политика конфиденциальности", $f_politika)?></div>
		<div class='colline colline-height'><?=bc_textarea("f_data", $f_data, "Поля формы", "data-formbuilder")?></div>
	</div>
	<div id='tab_t2'>
		<div class='colline colline-2'><?=bc_input("f_email", $f_email, "Почта для уведомлений", "maxlength='255' size='50'")?></div>
		<div class='colline colline-2'><?=bc_input("f_sendtext", $f_sendtext, "Надпись на кнопке отправить", "maxlength='255' size='50'")?></div>
		<div class='colline colline-1'><?=bc_input("f_titleformail", $f_titleformail, "Заголовок входящего письма (письмо приходит клиенту)", "maxlength='255' size='50'")?></div>
		<div class='colline colline-height'><?=bc_textarea("f_succestext", $f_succestext, "Ответ удачной отправки формы", "", 1)?></div>
    	<div class='colline colline-2 none'><?=bc_input("f_keyid", $f_keyid, "Ключ формы", "maxlength='255' size='50'", 1)?></div>
	</div>
</div>