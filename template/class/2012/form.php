<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_map">Карта</a></li>
    <li class="tab"><a href="#tab_city">Таргетинг</a></li>
    <?=editItemChecked(1, 'system')?>
</ul>

<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название точки/офиса (необязательно)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_adres", $f_adres, "Адрес")?></div>
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_phones", $f_phones, "Телефоны")?></div>
        <div class='colline colline-1 colline-height'><?=nc_time_work($f_time)?></div>
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_email", $f_email, "Эл. почта")?></div>
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_fax", $f_fax, "Факс")?></div>        
        <div class='colline colline-3'><?=bc_input("f_site", $f_site, "Сайт", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3'><?=bc_input("f_skype", $f_skype, "Skype", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3'><?=bc_input("f_icq", $f_icq, "Номер ICQ", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1 soc_show'><?=bc_checkbox("f_soc_show", 1, "Вывести соц. сети", $f_soc_show)?></div>
    <div class='colline colline-2'><?=bc_input("f_targetcode", $f_targetcode, "Таргетинг: код города", "maxlength='255' size='50'")?></div>
    <div class='colline colline-2'><?=bc_input("f_targetphone", $f_targetphone, "Таргетинг: телефон города", "maxlength='255' size='50'")?></div>
    <div class='colline colline-2'><?=bc_input("f_targetcode2", $f_targetcode2, "Таргетинг: код города 2", "maxlength='255' size='50'")?></div>
	<div class='colline colline-2'><?=bc_input("f_targetphone2", $f_targetphone2, "Таргетинг: телефон города 2", "maxlength='255' size='50'")?></div>
    <? $previwSize = $setting[size2010_imagepx] ? $setting[size2010_imagepx] : "480"; ?>
	<?$f_photo->settings->resize(900, 900)->preview($previwSize, 900)->use_name('Описание фото');?>
    <div class='colline colline-height'><?=gv_multifile_field($f_photo, "Фотографии (максимум по 20 шт. в один объект)")?></div>
    </div>
    <div id='tab_map'>
        <?php  $titlemap = "Код карты <a target=_blank href='https://firmsonmap.api.2gis.ru/'>2GIS</a> | <a target=_blank href='https://tech.yandex.ru/maps/tools/constructor/'>Яндекс</a> | <a target=_blank href='https://www.google.ru/maps?source=tldsi&hl=ru'>Google</a>"; ?>
        <div class='colline colline-height'><?=bc_textarea("f_map", $f_map, $titlemap)?></div>
    </div>
    <div id='tab_city'>

		<div class='bc_setrow'><?= nc_sklad1c_field($f_sklad1c) ?></div>
		<?= nc_city_field($f_citytarget) ?>
    </div>



    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID, 1, $f_lang)?>
</div>
