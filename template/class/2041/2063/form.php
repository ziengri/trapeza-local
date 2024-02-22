<?php 
global $altTexts;
$allField = $db->get_results("select Field_ID, Field_Name, Description from Field where Class_ID = 2001 AND Checked = 1 ORDER BY Priority", ARRAY_A);
if ($allField) {
    
    $_f = $db->get_var("SELECT data FROM Message2041 WHERE Catalogue_ID = 343 LIMIT 0,1");
    $_f = json_decode($_f, true);
    $allField1 = array();
    for($_ii=1; $_ii <= count($allField); $_ii++){
        $_t = 0;
        foreach($_f['priority'] as $k => $v){
            if ($v==$_ii){
                $_t = 1;
                foreach($allField as $f){
                    if ($f['Field_ID']==$k){
                        $allField1[$_ii-1] = $f;
                        break;
                    }
                }
                break;
            }
        }
        if (!$_t){
            $allField1[$_ii-1] = '-';
        }
    }
    for($_ii=0;$_ii<=count($allField);$_ii++){
        if ($allField1[$_ii]=='-' and !in_array($allField[$_ii], $allField1)){
            $allField1[$_ii] = $allField[$_ii];
        }
    }
    $allField = $allField1;

    foreach($allField as $f) { $ii++;
		$fid = $fname = NULL;
        $fid = $f['Field_ID'];
		$fname = $f['Field_Name'];
        $fhtml .= "<div class='bc_setrow'><h4><label><input type=checkbox ".($arr['checked'][$fid] ? "checked" : "")." name='field[checked][{$fid}]' value='{$fid}'> 
        ".($altTexts[$fname] ? $altTexts[$fname] : $f['Description'])."</label></h4>
        Показывать как: <input type=text size=50 name='field[name][{$fid}]' value='".($arr['name'][$fid] ? $arr['name'][$fid] : ($altTexts[$fname] ? $altTexts[$fname] : $f['Description']))."'><br>
        Приоритет: <input size=4 type=number value='{$ii}' min=1 name='fieldTmp[priority][{$fid}]'> 
        <br>Вид: <select name='field[view][{$fid}]'>
            <option value=''>-- не выбрано --</option>
            <option value='1' ".($arr[view][$fid]==1 ? "selected" : NULL)."> варианты в списке</option>
            <option value='2' ".($arr[view][$fid]==2 ? "selected" : NULL)."> варианты галочками</option>
			<option value='3' ".($arr[view][$fid]==3 ? "selected" : NULL)."> есть/нет (одна галочка)</option>
        </select>
        <label><input ".($arr['otdo'][$fid] ? "checked" : "")." type=checkbox name='field[otdo][{$fid}]' value='1'> 2 поля (от-до)</label> 
        </div>";
     }           
}
echo $fhtml;
?>

<div class='colline colline-1'><?=bc_checkbox("f_namefieldbool", 1, "Выводить название полей", $f_namefieldbool)?></div>
<div class='colline colline-1'><?=bc_checkbox("f_file_newline", 1, "Выводить параметры с новой строки", $f_file_newline)?></div>
<div class='colline colline-1'><?=bc_checkbox("f_hide_bigfilter", 1, "Скрыть фильтр (не полностью)", $f_hide_bigfilter)?></div>
<div class='colline colline-1'><?=bc_checkbox("f_adaptiv", 1, "Учитывать значения других выбранных параметров", $f_adaptiv)?></div>
<div class='colline colline-1'><?=bc_input("f_button", $f_button, "Надпись на кнопке", "maxlength='255' size='50'")?></div>
<div class='colline colline-1'><?=bc_textarea("f_subs", $f_subs, "Поиск только по разделам № (через запятую, без вложенных разделов)") ?></div>