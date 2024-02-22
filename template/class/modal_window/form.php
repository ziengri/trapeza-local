<ul class="tabs tabs-border">
    <li class="tab"><a href="#tab_t1">Контент</a></li>
    <li class="tab"><a href="#tab_t2">Вывод</a></li>
    <li class="tab"><a href="#tab_t3">Интервал вывода</a></li>
</ul>
<div class="modal-body tabs-body excel_export">
	<div id='tab_t1'>
		<div class='colline colline-1'><?=bc_input("f_Title", $f_Title, "Заголовок:", "maxlength='255' size='50'")?></div>
        <div class='colline colline-height'><?=bc_textarea('f_content', $f_content, 'Контент', "data-ckeditor='1'", 1)?></div>
	</div>
	<div id='tab_t2'>
		<div class='colline colline-1'><?=bc_checkbox("f_all_page", 1, "Выводить на всех страницах", $f_all_page)?></div>
		<div class='colline colline-height'><?=bc_listSubsCheckbox('В каких разделах показывать', 'f_id_page', explode(',', trim($f_id_page, ',')))?></div>
        <div class='colline colline-1'><?=bc_input('f_delay', $f_delay, 'Задержка перед появлениям в секундах', "maxlength='255' size='50'")?></div>
	</div>
	<div id='tab_t3'>
        <div class='colline colline-1'><?=bc_input('f_interval_show', $f_interval_show, 'Интервал показа в днях от 0 до 365', "maxlength='255' size='50'")?></div>
	</div>
</div>