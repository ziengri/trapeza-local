<?php  require $settings_array[template_path].'top.php';?>

    <script>
        $(document).ready(function () {
            $('.a2d--groups_lvl1 .a2d--groups_name .name:not(.end)').on('click', function() {
                if ($(this).hasClass('on')) {
                    $(this).parent().find('.on').removeClass('on');
                } else {
                    $(this).addClass('on').siblings().addClass('on');
                }
            });
        });
    </script>

    <div class="a2d--model">
        <span class="a2d--model_image">
            <?php  $photoUrl = $model->image ? $model->image : $noimage; ?>
            <img src="<?=$photoUrl?>">
        </span>
        <span class="a2d--model_info">
            <ul>
                <li>Модель: <b><?php echo $model->name ?></b></li>
                <?php if ($model->relevance) { ?>
                <li>Актуальность: <b><?php echo substr($model->relevance, 5, 2).'.'.substr($model->relevance, 0, 4) ?></b></li>
                <?php } ?>
                <?php if ($model->modification) { ?>
                <li>Модификации: <b><?php echo $model->modification ?></b></li>
                <?php } ?>
            </ul>
        </span>
    </div>
    <div style="height: initial;">
        <span class="a2d--groups a2d--groups_lvl1">
            <?php foreach ($groups as $item) {?>
                <span class="a2d--groups_name">
                    <span class="name <?php echo $item->childs ? '' : 'end' ?>">
                        <?=$item->name?>
                        <span class="beforePlus">+</span>
                        <span class="beforeMinus">-</span>
                    </span>
                    <?php if ($item->childs) { ?>
                        <span class="a2d--groups_lvl2">
                            <?php foreach ($item->childs as $subgroup) {?>
                                <span class="a2d--groups_name">
                                    <span class="name <?php echo $subgroup->childs ? '' : 'end' ?>">
                                        <?=$subgroup->name?>
                                        <span class="beforePlus">+</span>
                                        <span class="beforeMinus">-</span>
                                    </span>
                                    <?php if ($subgroup->childs) { ?>
                                    <span class="a2d--groups_lvl3">
                                        <?php foreach ($subgroup->childs as $subgroup2) {?>
                                            <span class="a2d--groups_name">
                                                <span class="name end">
                                                    <a href="<?=$hrefPrefix."?type={$model->type}&mark={$model->mark_short_name}&modelId={$model->short_name}&groupId={$subgroup2->short_name}"?>">
                                                        <?=$subgroup2->name?>
                                                    </a>
                                                </span>
                                            </span>
                                        <?php } ?>
                                    </span>
                                    <?php } ?>
                                </span>
                            <?php } ?>
                        </span>
                    <?php } ?>
                </span>
            <?php } ?>
        </span>
    </div>

<?php  require $settings_array[template_path].'bottom.php';?>