<?php  require $settings_array[template_path].'top.php';?>

    <script>
        $(document).ready(function () {
            $('.main_catalog--types').on('click', '.main_catalog--type', function() {
                $('.main_catalog--marks.on').removeClass('on');
                $(this).addClass('on').siblings().removeClass('on').removeClass('noborder');
                $(this).closest('.main_catalog').find('.main_catalog--marks').eq($(this).index()).addClass('on');
                $('.main_catalog--type.on').prev().addClass('noborder');
            });
        });
    </script>

    <?php  require $settings_array[template_path].'search_top.php';?>


    <?php if ($error) { ?>
        <h1 style="color: red;"><?php echo $error ?></h1>
    <?php } else { ?>
        <ul class="main_catalog">
            <li class="main_catalog--types">
                <?php foreach ($types as $k => $type) { ?>
                    <div class="main_catalog--type <?php echo $k === 0 ? 'on' : ''?>" data-type="<?php echo $type->value?>">
                        <div class="main_catalog--type_name">
                            <div class="main_catalog--type_title">
                                <span><?php echo $type->name?></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </li>
            <li class="main_catalog--marks_all">
                <?php foreach ($types as $k => $type) { ?>
                    <div class="main_catalog--marks <?php echo $k === 0 ? 'on' : '' ?>">
                        <div class="marks-inline">
                            <?php foreach ($type->marks as $mark) {?>
                                <a href="<?=$hrefPrefix."?type={$type->value}&mark={$mark->value}"?>">
                                <span class="main_catalog--mark">
                                    <?php if ($mark->vin) {?>
                                        <span class="mark-vin" title="Можно искать по VIN"></span>
                                    <?php } ?>
                                    <div class="main_catalog--mark_image">
                                        <img src="<?php echo $mark->image?>" alt="<?php $mark->name ?>"/>
                                    </div>
                                    <div class="main_catalog--mark_name">
                                        <?php echo $mark->name.(($mark->archival === true) ? ' (архивный)' : '') ?>
                                        <?php echo $mark->engine === true ? ' (двигатель)' : ''; ?>
                                    </div>
                                </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </li>
        </ul>
    <?php } ?>

<?php  require $settings_array[template_path].'bottom.php';?>