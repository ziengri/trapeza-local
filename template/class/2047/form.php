<?php  for ($i=10; $i <= 32; $i++) $size[$i] = $i + "px"; ?>

<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Основное</a></li>
    <li class="tab"><a href="#tab_text">Доп. текст</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
	    <div class='colline colline-2'><?=bc_file("f_file", $f_file_old, "Логотип", $f_file, 2676)?></div>
	    <div class='colline colline-2'><?=bc_input("f_altImg", $f_altImg, "Наименования фото", "maxlength='255' size='50'")?></div>
	    <div class='colline colline-8-3'><?=bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'")?></div>
		
	    <div class='colline colline-8'><?=bc_select("f_nameSize", getOptionsFromArray($size, $f_nameSize ? $f_nameSize : 16), "Размер", "class='ns select-w100'")?></div>
        <div class='colline colline-8-3'><?=bc_input("f_subname", $f_subname, "Слоган", "maxlength='255' size='50'")?></div>
	    <div class='colline colline-8'><?=bc_select("f_subnameSize", getOptionsFromArray($size, $f_subnameSize ? $f_subnameSize : 14), "Размер", "class='ns select-w100'")?></div>
		<div class='colline colline-8-3'><?=bc_input("f_url", $f_url, "Ссылка с логотипа", "maxlength='255' size='50'")?></div>
    </div>
    <div id='tab_text'>
	    <div class='colline colline-height'><?=bc_textarea("f_text1", $f_text1, "Текст над логотипом")?></div>
	    <div class='colline colline-height'><?=bc_textarea("f_text2", $f_text2, "Текст под логотипом")?></div>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>