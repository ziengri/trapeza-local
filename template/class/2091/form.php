<?php 
    global $db, $catalogue;

    $tableHtml = "<div class='multi-line multi-list multi-disable' data-num='{$i}'>
        <div class='colline colline-3 colline-modelname'>".bc_input('f_mo', $f_mo, 'Округ')."</div>
        <div class='colline colline-3 colline-cold'>".bc_input('f_name', $f_name, 'Название лагеря')."</div>
        <div class='colline colline-3 colline-warm'>".bc_input('f_shift', $f_shift, 'Номер смены')."</div>
        <div class='colline colline-2 colline-date'>".bc_date('f_date1', $f_date1, 'Дата заезда', 0,1,1)."</div>
        <div class='colline colline-2 colline-date'>".bc_date('f_date2', $f_date2, 'Дата выезда',0,1,1)."</div>
    </div></div>";

    echo "<ul class='tabs tabs-border tab-more-tabs'>
                <li class='tab'><a href='#tab_main'>Главное</a></li>
                ".editItemChecked(1)."
            </ul>
            <div class='modal-body tabs-body'>
                <div id='tab_main'>
                    <div class='colblock'>
                        <h4>Таблица</h4>
                        {$tableHtml}
                        <!--<a class='add-btn' href='' onclick='add_line(\"camptable\"); return false;'>добавить еще</a>-->
                    </div>                    
                </div>
                ".editItemChecked(0, $f_Priority, $f_Keyword, $f_ncTitle, $f_ncKeywords, $f_ncDescription, $classID)."
            </div>";
?>