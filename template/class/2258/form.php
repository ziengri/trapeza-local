<?php
$price = getOptionsFromArray(['price' => 'Цена 1', 'price2' => 'Цена 2', 'price3' => 'Цена 3', 'price4' => 'Цена 4'], $f_price);
$stock = getOptionsFromArray(['stock' => 'Склад 1', 'stock2' => 'Склад 2', 'stock3' => 'Склад 3', 'stock4' => 'Склад 4'], $f_stock);
?>
<ul class="tabs tabs-border">
    <li class="tab"><a href="#tab_t1">Основное</a></li>
    <li class="tab"><a href="#tab_t2">Системные</a></li>
</ul>
<div class="modal-body tabs-body yml_export">
    <div id='tab_t1'>
        <div class='colline colline-1'>
            <?=bc_input("f_name", $f_name, 'Название (если нужно)')?>
        </div>
        <div class='colline colline-1'>
            <?=bc_checkbox("f_all_item", 1, 'Брать все товары', $f_all_item)?>
            <p class="note">* или с разделов с включеным "Выгружать в Яндекс.Маркет"</p>
        </div>
        <div class='colline colline-1'><?=bc_checkbox("f_one_photo", 1, 'Брать только 1 фото товара', $f_one_photo)?></div>
        
        <div class='colline colline-1'><?=bc_checkbox("f_delivery", 1, 'Возможность курьерской доставки', $f_delivery)?></div>

        <div class='colline colline-2'>
            <?=bc_input("f_cost",$f_cost,"Стоимость доставки" )?>
        </div>

        <div class='colline colline-2'>
            <?=bc_input("f_days",$f_days,"Срок доставки" )?>
        </div>

        <div class='colline colline-1'>
            <?=bc_checkbox("f_presence_retail_store", 1, 'Возможность забрать товар из розничных магазинов', $f_presence_retail_store)?>
        </div>
        <div class='colline colline-1'><?=bc_checkbox("f_pickup", 1, 'Возможность забрать товар из пункта самовывоза', $f_pickup)?></div>
        
        <div class='colline colline-1'><?=bc_select("f_price", $price, "Поле с ценой", "class='ns'")?></div>
        <fieldset>
            <legend>Наличие</legend>
            <div class='colline colline-1'>
                <?=bc_checkbox("f_all_items_in_stock", 1, 'Все товары в наличии. Вне зависимости от склада', $f_all_items_in_stock)?>
            </div>
            <div class='colline colline-1'><?=bc_select("f_stock", $stock, "Поле со складом", "class='ns'")?></div>
        </fieldset>
        <fieldset>
        <legend>Описание</legend>
            <div class='colline colline-height'><?=bc_textarea("f_def_description", $f_def_description, "Описание товара по умолчанию")?></div>
        </fieldset>
        <fieldset>
            <legend>Примечание</legend>
            <div class='colline colline-1'><?=bc_checkbox("f_sales_notes_on", 1, 'Включить примечание', $f_sales_notes_on)?></div>
            <div class='colline colline-height'><?=bc_textarea("f_sales_notes", $f_sales_notes ?: 'Необходима предоплата.', "Текст примечание", "")?></div>
        </fieldset>
    </div>
    <div id='tab_t2' class='none'>
        <div class='colline colline-1'><?=bc_checkbox("f_turbo", 1, 'Формат турбо страниц', $f_turbo)?></div>
        <div class='colline colline-1'><?=bc_checkbox("f_cron_on", 1, 'Включить автоматическое обновление', $f_cron_on)?></div>
        <div class='colline colline-1'><?=bc_checkbox("update", 1, 'Принудительно обновления', 0)?></div>
        <?php if (getIP('office')): ?>
        <div class='colline colline-1'>
            <?=bc_input(
                "interval_minutes",
                ($db->get_var("SELECT interval_minutes FROM Cron_Tasks WHERE cron_id = {$f_cron_id}") ?: 60),
                'Интервал обновлений в минутах'
                )?>
        </div>
        <?php endif; ?>
    </div>
</div>