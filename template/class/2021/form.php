<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_description">Полное описание</a></li>
    <li class="tab"><a href="#tab_tags">Тэги</a></li>
    <?php if($setting['targeting']){ ?>
        <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?php  } ?>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Название", "maxlength='255' size='50'", 1)?></div>
        <div class='colline colline-1 colline-line'><?=bc_text_standart("Превью")?></div>
        <div class='colline colline-1'><?=bc_file("f_photo_preview", $f_photo_preview_old, "Выберите превью", $f_photo_preview, 2934)?></div>
        <?php  $f_photo->settings->resize(1000, 800)->preview(300, 900)->use_name('Описание фото'); ?>
        <div class='colline colline-height'><?=gv_multifile_field($f_photo, "Фотографии")?></div>
        <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Описание в превью", "data-ckeditor='1'")?></div>
        <div class='colline colline-3'><?=bc_input("f_price", $f_price, "Цена (только число)", "maxlength='255' size='50'")?></div>
        <div class='colline colline-3'><?=bc_checkbox("f_firstprice", 1, "Это нижняя граница<br>цены товара (от)", $f_firstprice, "data-twoline")?></div>
        <div class='colline colline-3'><?=bc_input("f_link", $f_link, "Ссылка", "maxlength='255' size='50'")?></div>
    </div>
    <div class='none' id='tab_description'>
        <div class='colline colline-height'><?=bc_textarea("f_textfull", $f_textfull, "Полное описание (Справа)", "data-ckeditor='1'")?></div>
        <div class='colline colline-height'><?=bc_textarea("f_textfull_bottom", $f_textfull_bottom, "Полное описание (Внизу)", "data-ckeditor='1'")?></div>
    </div>
    <div class='none' id='tab_tags'>
        <?php $tagProvider = new \App\modules\Korzilla\Tag\Provider(); ?>
        <?php if ($tagList = $tagProvider->tagGetList()) : ?>
            <?php 
                $objectTagList = [];
                if (!empty($message)) {
                    $filter = $tagProvider->filterGet();
                    $filter->objectId[] = $message;
                    $filter->objectType[] = $classID;
                    $objectTagList = $tagProvider->tagGetList($filter);
                }
            ?>
            <?php foreach($tagList as $tag) : ?>
                <div class='colline colline-5'>
                    <?= bc_checkbox("tag_list[{$tag->Message_ID}]", 1, $tag->tag, isset($objectTagList[$tag->Message_ID])) ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="txt">Нет тэгов</p>
        <?php endif; ?>
    </div>
    <div class='none' id='tab_targeting'>
        <?=($setting['targeting'] ? nc_city_field($f_citytarget) : "")?>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>