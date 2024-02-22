<ul class="tabs tabs-border tab-more-tabs">
    <li class="tab"><a href="#tab_main">Главное</a></li>
    <li class="tab"><a href="#tab_photo">Фотографии</a></li>
    <li class="tab"><a href="#tab_other">Источник</a></li>
    <li class="tab"><a href="#tab_tags">Тэги</a></li>
    <li class="tab"><a href="#tab_targeting">Таргетинг</a></li>
    <?=editItemChecked(1)?>
</ul>
<div class="modal-body tabs-body">
    <div id='tab_main'>
        <div class='colline colline-1'><?=bc_input("f_name", $f_name, "Заголовок", "maxlength='255' size='50'", 1)?></div>
        <div class='colline colline-1'><?=bc_date('f_date', $f_date, "Дата:", 1, 1, 1)?></div>
        <div class='colline colline-height'><?=bc_textarea("f_text", $f_text, "Анонс (краткое содержание)")?></div>
        <div class='colline colline-height'><?=bc_textarea("f_textfull", $f_textfull, "Полный текст внутри новости", "data-ckeditor='1'")?></div>
    </div>
    <div id='tab_photo' class="none">
        <div class='colline colline-1 colline-line'><?=bc_text_standart("Превью")?></div>
        <div class='colline colline-1'><?=bc_file("f_photo_preview", $f_photo_preview_old, "Выберите превью", $f_photo_preview, 2845)?></div>
        <?php  $f_photo->settings->resize(900, 900)->preview(450, 900)->use_name('Описание фото');?>
        <div class='colline colline-height'><?=gv_multifile_field($f_photo, "Фотографии")?></div>
    </div>
    <div id='tab_other' class="none">
        <div class='colline colline-2'><?=bc_input("f_autor", $f_autor, "Автор", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_input("f_source", $f_source, "Источник", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_input("f_url", $f_url, "Ссылка на источник", "maxlength='255' size='50'")?></div>
        <div class='colline colline-2'><?=bc_checkbox("f_openlink", 1, "Перейти по ссылке источника при открытии", $f_openlink)?></div>
		<div class='colline colline-2'><?=bc_checkbox("f_main", 1, "Вывести в отдельном блоке", $f_main)?></div>
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
    <div id='tab_targeting' class="none">
        <?=($setting['targeting'] ? nc_city_field($f_citytarget) : "")?>
    </div>
    <?=editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, '', '', $f_lang)?>
</div>