<?php
/**
 * ��� ���� ��������� ������.
 */
$php_as_cgi     = substr(PHP_SAPI, 0, 3) == 'cgi';
$step           = 0;
$for_activation = isset($for_activation) ? $for_activation : false;
?>

<style>
.nc-tools-path-info h2 {margin:20px 0 10px 0; padding:0; font-weight:normal; font-size:21px; color:#333;}
.nc-tools-path-info h2 span {display:inline-block; background:#1a87c2; width:30px; padding:5px 0; line-height:20px; text-align:center; color:#FFF; margin-right:10px}
.nc-tools-path-info em {color:#d62c1a; font-style: normal;}
code {font-family: }
</style>

<div class="nc-tools-path-info">

    <?php  if ( ! $for_activation): ?>
        <h2><span><?=++$step ?></span> �����</h2>
        <p>���������� � ������������ ������� ������� ����� ��� ������ ����� �������, ��� � ���� ������. ����� ����, ������� � ���������������� ������ ����� ������� ��� ������ ����������� ������� ������� � <a href='<?=$ADMIN_PATH ?>dump.php?phase='>����������� / ������ �������</a>. ������ ����� ����� ������� ����� ���������� ���� ������ ������� ����� FTP � ����������� ����� ���� ������ ����� ������ ���������� ��������� (�������� phpMyAdmin).</p>
        <hr>
    <?php  endif ?>

    <?php  if ( ! $php_as_cgi): ?>
        <h2><span><?=++$step ?></span> ����� �� ����� � �����</h2>
        <p>����� ���������� ����� ���������� ��������� �� ��� ���������� � ����� ����� ����� <code class='nc-code'>0777</code>. ��� ������������ (��������� � ������ ��������� ��������) ��������� ���� �� ����� � ����� �� FTP ����� ��������������� ���������� FTP-�������� <a href="http://filezilla-project.org/download.php?type=client" target="_blank">FileZilla</a></p>
        ����� ��������, �� ������� ����� ������� ��� �������������, �������� ����� ������� �� ������ SSH (Secure Shell). ���� �������� ������ ��� ������� ����� SSH (�� ��� FTP):
        <pre class='nc-code'>chmod -R 0777 ./�����</pre>
        <p>"�����" � ����������, ��� ��������� �����</p>
        <p>��� ������� � ����� �� SSH ������� �������� ��������������� ��������������� ������ � �������� (�����, ������ � ����� �������). ��� �� Windows ����� ������������ ���������� ��������� SSH-������ <a href="http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html" target="_blank">PuTTY</a></p>
        <hr>
    <?php  endif ?>

    <?php  if ($for_activation): ?>
        <h2><span><?=++$step ?></span> �������� ��������������� ������</h2>
        <p>��� ��������� ������� ��� ����� ������ ���� ��������������� ��� � ���� ���������, ������� �� �������� ����� �������
.</p>
        <hr>
    <?php  else: ?>
        <h2><span><?=++$step ?></span> ��������� ����</h2>
        <p>� ������� <a href="<?=$ADMIN_PATH ?>patch/index.php?phase=1">����������� / ���������� �������</a> ������� ���� � ���������� ����� ����������� ������ "�����", ����� ���� ������ ������ "��������".</p>
        <hr>
    <?php  endif ?>

    <?php  if ( ! $php_as_cgi): ?>
        <h2><span><?=++$step ?></span> �������������� ���� �� ����� � �����</h2>
        <p>���������� ������������ ����� �� ��� ����� � ����� (������������� <code class='nc-code'>0755</code> ��� ����� � <code class='nc-code'>0644</code> ��� <code class='nc-code'>0666</code> ��� ������, ����������� �� ������ �� ����� �������� ������� �������� � ������ ��������� �������-����������).</p>
        <p>�� ����� <code class='nc-code'><?= nc_core('HTTP_ROOT_PATH') ?>tmp/</code>, <code class='nc-code'><?= nc_core('HTTP_DUMP_PATH') ?></code>, <code class='nc-code'><?= nc_core('HTTP_FILES_PATH') ?></code>, <code class='nc-code'><?= nc_core('HTTP_TRASH_PATH') ?></code>, <code class='nc-code'><?= nc_core('HTTP_TEMPLATE_PATH') ?></code>, <code class='nc-code'><?= nc_core('HTTP_CACHE_PATH') ?></code> ������� �������� ����� � <b>0777</b>.</p>
        <p>����� �� ���� <code class='nc-code'>/netcat/admin/crontab.php</code> � <b>0755</b>.</p>
        ������� ��� �������� ������� ���� �� SSH:
        <pre class='nc-code'>cd <?=$DOCUMENT_ROOT ?>

find ./ -type f -exec chmod 0644 {} \;
find ./ -type d -exec chmod 0755 {} \;
chmod -R 0777 .<?= nc_core('HTTP_ROOT_PATH') ?>tmp/ .<?= nc_core('HTTP_DUMP_PATH') ?> .<?= nc_core('HTTP_FILES_PATH') ?> .<?= nc_core('HTTP_TRASH_PATH') ?> .<?= nc_core('HTTP_TEMPLATE_PATH') ?> .<?= nc_core('HTTP_CACHE_PATH') ?> 
chmod 0755 .<?= nc_core('HTTP_ROOT_PATH') ?>admin/crontab.php</pre>
        <hr>
    <?php  endif ?>

</div>