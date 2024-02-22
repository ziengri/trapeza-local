<div class="colblock">
    <h3>Список обменов с 1С</h3>
    <div class="colblock-body">
        <div class="colblock">
            <div class="colline colline-height">
                <? if (count($obmenList)) : ?>
                    <? foreach ($obmenList as $id => $value) : ?>
                        <div class="multi-line multi-list">
                            <div class="colline colline-1 type-checkbox">
                                <div class="switch">
                                    <label>
                                        <input type="checkbox" onchange="checkAutoload(this, '<?= $id ?>')" <?= ($value['setting']['autoload'] ? 'checked' : null) ?>>
                                        <span class="lever"></span>
                                        <span class="sw-text">Автообновления</span>
                                    </label>
                                </div>
                            </div>
                            <div class="colline colline-1 type-text">
                                ID: <?= $id ?> <b><?= $value['name'] ?></b>
                            </div>
                            <div class="colline colline-1 type-text">
                                Дата обновления: <b><?= date('Y-m-d H:i', $value['update_time']) ?></b>
                            </div>
                        </div>
                    <? endforeach; ?>
                <? else : ?>
                    <div class="multi-line multi-list">
                        <span class="txt">Обмен не настроен</span>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    function checkAutoload(e, id) {
        e.disabled = true;
        $.post('/bc/modules/Korzilla/Upload1C/Admin/index.php?action=update_autoload', {version: id, checked: + e.checked}, function(res) {
            e.disabled = false;
            console.log(res);
        }, 'json')
    }
</script>