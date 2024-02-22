<?php
if (!$vendor)
    echo ($inside_admin ? $f_AdminCommon : addObjBut($addLink, $isTitle, null, 'товар'));

if (!$getanalog) {
    if ($nc_ctpl == 2025) {
        // фото раздела для всех товаров
        if ($current_sub['imgtoall'] && $current_sub['img']) {

            $subPhoto = ($current_sub['imgBig'] ? "<a href='" . $current_sub['imgBig'] . "' rel='photo[a]'>" : "") .
                "<img class=fl style='margin: 0 20px 20px 0;' src='" . $current_sub['img'] . "'>" . ($current_sub['imgBig'] ? "</a>" : "");
        }
    }
}

$paramsSettingChecked = array_reduce($setting['lists_params'], function ($paramsSettingChecked, $param) {
    if ($param['checked']) {
        $paramsSettingChecked[$param['keyword']] = $param;
    }
    return $paramsSettingChecked;
}, []);

$paramKeys = [];
foreach ($fetch_row as $row) {
    $arrParams[] = $row['params'];
}

foreach ($arrParams as $params) {
    foreach (explode("\r\n", $params) as $explParam) {
        $paramKey = explode("||", $explParam)[0];
        if ($paramsSettingChecked[$paramKey]) {
            $paramKeys[$paramKey] = $paramsSettingChecked[$paramKey]['name'];
        }
    }
}

# GET
$vars_str2 = vars_str($_GET, "nc_ctpl,find,tag,filter,flt,flt1,sort,cur_cc", 1);
$ncctpl = $nc_ctpl > 1 ? $nc_ctpl : $current_cc['Class_Template_ID'];
# PAGE
// чтобы в гет параметрах не было cur_cc, копируем cc_env и убераем в новом массиве cur_cc, 
// который передаем в функцию browse_messages
$cc_env_without_cur_cc = $cc_env;
// unset($cc_env_without_cur_cc['cur_cc']);

if (browse_messages($cc_env_without_cur_cc, 15) && !$isTitle) {
    $pagination = "<div class='pgn-line'>"
        . ($prevLink ? "<a rel='nofollow' href='$prevLink' class='icons i_left pag_prev'></a>" : NULL)
        . browse_messages($cc_env_without_cur_cc, 15)
        . "<span class='pag_text'>" . getLangWord('paginatyion1', 'из') . "</span>
            <a href='?curPos=" . (ceil($totRows / $recNum) * $recNum - $recNum) . "{$vars_str2}'>"
        . ceil($totRows / $recNum) . "</a>
        </div>"
        . ($nextLink ? "<div class='next_page'><a rel='nofollow' href='$nextLink' class=' icons i_right'>" . getLangWord('paginatyion2', 'Следующая страница') . "</a></div>" : NULL);
}

if (!$isTitle)
    echo $subPhoto;

if ($totRows) {
    $top_pagination = "<div class='top_pagination pagination'>{$pagination}</div>";
    echo $top_pagination;
}

// if(getIP('office')){
//     print_r($message_select);
// }
?>