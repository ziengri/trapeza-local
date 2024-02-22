<?php  require $settings_array[template_path].'top.php';?>

    <h1 class="title">Основные подгруппы деталей для <?php echo "{$breadcrumbs[2]->name} {$breadcrumbs[3]->name} {$breadcrumbs[4]->name} ({$breadcrumbs[5]->name})" ?></h1>
    <div class="etka_groups">
        <?php foreach ($subgroups as $subgroup) { ?>
            <a href="<?=$hrefPrefix."?type={$type}&mark={$mark}&series={$series}&model={$model}&rule={$rule}&transmission={$transmission}&group={$group}&subgroup={$subgroup->short_name}".($queryDate ? "&date={$queryDate}" : '')?>">
                <span class="bmw_group bmw_group-fix">
                    <div class="bmw_group-image">
                        <?php if ($subgroup->image) { ?>
                            <img src="<?php echo $subgroup->image ?>">
                        <?php } else { ?>
                            <img src="https://212709.selcdn.ru/autocatalog-online/public/images/avtodiler.png">
                        <?php } ?>
                    </div>
                    <div class="bmw_group-name"><?php echo $subgroup->name ?></div>
                </span>
            </a>
        <?php } ?>
    </div>

<?php  require $settings_array[template_path].'bottom.php';?>