<?php if ( ! defined('NC')) exit ?>
<?php /*------------------------------------------------------------------------*/?>

<?php  example('nc-text-left') ?>
<?php  $elem = $ui->html('p')->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.') ?>
<?=$elem->text_left() ?>

<?php  example('nc-text-center') ?>
<?=$elem->text_center() ?>


<?php  example('nc-text-right') ?>
<?=$elem->text_right() ?>


<?php  example('nc--left, nc--right') ?>
<?=$ui->btn('#', 'Кнопка слева 1')->red()->left() ?>
<?=$ui->btn('#', 'Кнопка слева 2')->left() ?>

<?=$ui->btn('#', 'Кнопка справа 1')->blue()->right() ?>
<?=$ui->btn('#', 'Кнопка справа 2')->right() ?>

<?=$ui->helper->clearfix() ?>



<?php  example('nc-padding-(0-25) nc-margin-(0-25) nc-bg-*') ?>

<?php  $elem->text_left() ?>

<?=$elem->padding_0()->bg_lighten() ?>

<?=$elem->padding_5()->bg_light() ?>

<?=$elem->padding_10()->bg_grey() ?>

<?=$elem->padding_15()->bg_dark() ?>

<?=$elem->padding_20()->bg_darken()->text_light() ?>

<?=$elem->padding_25()->bg_black()->text_light() ?>

<?php  $elem->text_darken()->style('display:inline') ?>

<?=$elem->padding_5()->bg_red()->text('red') ?>

<?=$elem->padding_5()->bg_green()->text('green') ?>

<?=$elem->padding_5()->bg_blue()->text('blue') ?>

<?=$elem->padding_5()->bg_yellow()->text('yellow') ?>



<?php  example('nc-shadow, nc-shadow-small, nc-shadow-large') ?>

<?php  $elem->reset()->padding_10()->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.') ?>

<?=$elem->class_name('nc-shadow-small') ?>

<?=$elem->class_name('nc-shadow') ?>

<?=$elem->class_name('nc-shadow-large') ?>


<?php  example('nc-long-shadow') ?>

<?php  //$elem->reset()->padding_10()->text('')->class_name('nc-btn nc--blue') ?>

<?=$ui->btn('#', 'Long shadow button')->blue()->class_name('nc-long-shadow') ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?=$ui->btn('#', 'Long shadow button')->red()->class_name('nc-long-shadow') ?>

<br><br><br><br><br><br><br>

<div class="nc-box nc-long-shadow">box</div>

<br><br><br>

<?php  example('Разное | Смотрите вкладки "PHP" и "HTML"') ?>

<?=$ui->helper->clearfix() ?>

<?=$elem->reset()->class_name('nc-shadow')->hide() ?>

<?=$elem->class_name('nc-shadow')->show() ?>