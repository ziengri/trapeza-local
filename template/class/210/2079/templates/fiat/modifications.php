<?php  require $settings_array[template_path].'top.php';?>

<div class="block-list">
    <p class="fiat-row" style="position: relative; display: block; height: auto;">
        <?php foreach ($modifications as $modification) { ?>
            <a href="<?=$hrefPrefix."?type={$breadcrumbs[1]->url}&mark={$breadcrumbs[2]->url}&model={$modification->model_short_name}&modification={$modification->short_name}"?>">
                <span class="list-item"><?php echo $modification->full_name ?></span>
            </a>
        <?php } ?>
    </p>
</div>

<?php  require $settings_array[template_path].'bottom.php';?>