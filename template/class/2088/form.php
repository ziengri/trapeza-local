<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_contacts">Контакты</a></li>
    <li class="tab"><a href="#tab_city">Таргетинг</a></li>
    <?=editItemChecked(1, 'system')?>
</ul>

<div class="modal-body modal-dealers tabs-body">
    <div id='tab_main'>
        <?php  if (!$cc_settings[cities_tabs]) { ?>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, $cc_settings[clientmap] ? "Название (*)" : "Название организации (*)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1'><?=bc_input("f_coordinates", $f_coordinates, "Географические координаты (*)", "maxlength='255' size='50'")?></div>
        <?php  } ?>
        <?php  if ($cc_settings[clientmap]) { ?>
        <div class='colline colline-1'><?=bc_file("f_photo", $f_photo_old, "Фотография", $f_photo, 3032)?></div>
        <?php  } ?>
        <?php  if (!$cc_settings[cities_tabs]) { ?>
        <div class='colline colline-1'><?=bc_input("f_site", $f_site, $cc_settings[clientmap] ? "Ссылка для картинки" : "Сайт", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1'><?=bc_input("f_description", $f_description, "Описание", "maxlength='255' size='50'")?></div>
        <div class='colline colline-1'><?=bc_input("f_city", $f_city, "Город (*)")?></div>
        <div class='colline colline-1'><?=bc_input("f_city_group", $f_city_group, "Добавить город в группу")?></div>
        
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_address", $f_address, "Адрес")?></div>
        <div class='colline colline-2 colline-height'><?=bc_textarea("f_time", $f_time, "Время работы")?></div>

        <!--
        <div class='colline colline-3'><?=bc_input("f_skype", $f_skype, "Skype", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3'><?=bc_input("f_icq", $f_icq, "Номер ICQ", "maxlength='255' size='50'")?></div>
        -->
        <div class='colline colline-2'><?=bc_input("f_targetcode", $f_targetcode, "Таргетинг: код города", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_input("f_targetphone", $f_targetphone, "Таргетинг: телефон города", "maxlength='255' size='50'")?></div>
        <?php  }else { ?>
            <div class='colline colline-1'><?=bc_input("f_city", $f_city, "Город (*)")?></div>
            <div class='colline colline-1'><?=bc_input("f_coordinates", $f_coordinates, "Географические координаты (*)", "maxlength='255' size='50'")?></div>
            <div class='colline colline-1'><?=bc_input("f_description", $f_description, "Описание", "maxlength='255' size='50'")?></div>
        <?php  } ?>
    </div>
    <div id='tab_contacts' class="none">
        <div class='colline colline-2 colline-height'><?=bc_multi_line("f_phones", json_encode([
            'cols' => [
                'phone' => [
                    'col' => 1,
                    'type' => 'input',
                    'name' => 'phone',
                    'title' => 'Телефон',
                ]
            ],
            'values' => orderArray($f_phones) ?: [],
        ]), "Телефоны", 3)?></div>
        <div class='colline colline-2 colline-height'><?=bc_multi_line("f_emails", json_encode([
            'cols' => [
                'email' => [
                    'col' => 1,
                    'type' => 'input',
                    'name' => 'email',
                    'title' => 'Почта',
                ]
            ],
            'values' => orderArray($f_emails) ?: [],
        ]), "Почта", 3)?></div>
        <div class='colline colline-1 colline-height'><?=bc_multi_line("f_links", json_encode([
            'cols' => [
                'link' => [
                    'col' => 3,
                    'type' => 'input',
                    'name' => 'link',
                    'title' => 'Ссылка',
                ],
                'title' => [
                    'col' => 3,
                    'type' => 'input',
                    'name' => 'title',
                    'title' => 'Надпись на ссылке',
                ],
                'type' => [
                    'col' => 3,
                    'type' => 'select',
                    'name' => 'type',
                    'title' => 'Тип Ссылки',
                    'options' => [0 => "Без типа", 'vk' => "вк", 'whatsapp' => "ватсап", 'tg' => "телеграм"]
                ],
            ],
            'values' => orderArray($f_links) ?: [],
        ]), "Ссылки", 3)?></div>        
    </div>
    <div id='tab_city' class="none">
        <?= nc_city_field($f_citytarget) ?>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID, 1, $f_lang)?>
</div>