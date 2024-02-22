<?php  require $settings_array[template_path].'top.php';?>

<h1 class="title">Основные группы деталей для <?php echo "{$breadcrumbs[2]->name} {$breadcrumbs[3]->name} ({$breadcrumbs[4]->name})" ?></h1>
<div class="etka_groups">
    <?php foreach ($groups as $group) { ?>
        <a href="<?=$hrefPrefix."?type={$breadcrumbs[1]->url}&mark={$breadcrumbs[2]->url}&series={$breadcrumbs[3]->url}&model={$model}&rule={$rule}&transmission={$transmission}&group={$group->number}".($queryDate ? "&date={$queryDate}" : '')?>">
            <span class="bmw_group bmw_group-fix">
                <div class="bmw_group-image">
                    <?php if ($group->image) { ?>
                        <img src="<?php echo $group->image ?>">
                    <?php } else { ?>
                        <img src="https://212709.selcdn.ru/autocatalog-online/public/images/avtodiler.png">
                    <?php } ?>
                </div>
                <div class="bmw_group-name"><?php echo $group->name ?></div>
            </span>
        </a>
    <?php } ?>
</div>

<?php  require $settings_array[template_path].'bottom.php';?>