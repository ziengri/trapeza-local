<?php  require $settings_array[template_path].'top.php';?>

<form class="catalog_search" method='GET' action='<?=$hrefPrefix?>'>
    <input required class="search_vim" id="number" type='text' name='number' placeholder=' ' style="width: 50%;">
    <label class="form__label" for='search_vim'>Поиск по номеру (артикулу) детали</label>
    <input class="button button--green" type='submit' value="Найти">
    <input type='hidden' name='search' value='1'>
    <input type='hidden' name='type' value='<?=$breadcrumbs[1]->url?>'>
    <input type='hidden' name='mark' value='<?=$breadcrumbs[2]->url?>'>
</form>

<?php foreach ($models as $k => $model) { ?>
    <a href="<?=$hrefPrefix."?type={$model->type}&mark={$model->brand_short_name}&model={$model->short_name}"?>">
        <span class="catalog--mark drop-down drop-down-fix">
            <div class="fiat--mark_image">
                <?php if ($model->image) { ?>
                    <img src="<?php echo $model->image ?>">
                <?php } else { ?>
                    <img src="https://212709.selcdn.ru/autocatalog-online/public/images/avtodiler.png">
                <?php } ?>
            </div>
            <div class="catalog--mark_name"><?php echo $model->full_name ?></div>
        </span>
    </a>
<?php } ?>


<?php  require $settings_array[template_path].'bottom.php';?>