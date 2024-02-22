<?php  require $settings_array[template_path].'top.php';?>

<div class="fiat_units">
    <?php foreach ($units as $group) { ?>
        <a href="<?=$hrefPrefix."?type={$type}&mark={$mark}&model={$model}&modification={$modification}&group={$group->short_name}"?>">
            <span class="fiat_unit">
                <div class="fiat_units_image">
                    <?php if ($group->image) { ?>
                        <img src="<?php echo $group->image ?>">
                    <?php } else { ?>
                        <img src="https://212709.selcdn.ru/autocatalog-online/public/images/avtodiler.png">
                    <?php } ?>
                </div>
                <div class="fiat_units_name"><?php echo $group->full_name ?></div>
            </span>
        </a>
    <?php } ?>
</div>


<?php  require $settings_array[template_path].'bottom.php';?>