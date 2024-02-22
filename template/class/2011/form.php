<?php 
    $socArray = $db->get_results("select social_ID, social_Name from Classificator_social", ARRAY_A);
    foreach($socArray as $v) {
        $social .= "<option value='".$v['social_ID']."' ".($v['social_ID']==$f_social ? "selected" : "").">{$v['social_Name']}</option>";
    }
?>
<div class="modal-body">
    <div id='tab_main'>
        <div class='colline colline-2'><?=bc_select("f_social", $social, "Социальная сеть", "class='ns'")?></div>
        <div class='colline colline-2'><?=bc_input("f_url", $f_url, "Ссылка на страницу", "maxlength='255' size='50'", 1)?></div>
    	<?=($setting['targeting'] && isField('citytarget',$classID) ? nc_city_field($f_citytarget) : "")?>
    </div>
</div>