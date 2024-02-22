<?php  require $settings_array[template_path].'top.php';?>

    <form class="catalog_search" method='GET' action='<?=$hrefPrefix?>'>
        <input required class="search_vim" id="number" type='text' name='number' placeholder=' ' style="width: 50%;">
        <label class="form__label" for='search_vim'>Поиск по номеру (артикулу) детали</label>
        <input class="button button--green" type='submit' value="Найти">
        <input type='hidden' name='search' value='1'>
        <input type='hidden' name='type' value='<?=$breadcrumbs[1]->url?>'>
        <input type='hidden' name='mark' value='<?=$breadcrumbs[2]->url?>'>
    </form>

    <?php foreach ($series as $k => $seria) {?>
        <span class="tile-block">
            <a href="<?=$hrefPrefix."?type={$seria->type}&mark={$seria->mark_short_name}&series={$seria->short_name}"?>">
                <div class="tile-block-image">
                    <?php if ($seria->image) { ?>
                        <img src="<?php echo $seria->image ?>">
                    <?php } else { ?>
                        <img src="https://212709.selcdn.ru/autocatalog-online/public/images/avtodiler.png">
                    <?php } ?>
                </div>
                <div class="tile-block-name" style="min-height: 75px;"><?php echo $seria->name ?></div>
                <div class="tile-block-option" style="<?php echo $seria->catalog_type == 'VT' ? 'background: white;' : '' ?>">
                    <span><?=$seria->catalog_type == 'VT' ? '' : 'Живая традиция' ?></span>
                </div>
            </a>
        </span>
    <?php } ?>

<?php  require $settings_array[template_path].'bottom.php';?>