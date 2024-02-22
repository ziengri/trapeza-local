<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global  $db;

switch ($_POST['action']) {
    case 'validate':
        echo json_encode(validate(), JSON_UNESCAPED_UNICODE);
        break;
    case 'create':
        echo json_encode(createSubSeo(), JSON_UNESCAPED_UNICODE);
        break;
    default:
        echo 'Not action';
        break;
}
// $_POST['sub_id'] = 424194;
// var_dump(createSubSeo());
function createSubSeo()
{
    global $db;

    $result = [
        'status' => 0,
        'message' => '',
        'data' => []
    ];

    $parentSub = $db->get_row(
        "SELECT 
            Hidden_URL,
            Subdivision_ID,
            (SELECT MAX(Priority) FROM Subdivision WHERE Parent_Sub_ID in ('{$_POST['sub_id']}')) as priority
        FROM 
            Subdivision 
        WHERE 
            Catalogue_ID = '{$_POST['catalogue']}' 
            AND Subdivision_ID = '{$_POST['sub_id']}'",
        ARRAY_A
    );

    if (empty($parentSub)) {
        $result['message'] = 'Не удалось найти корневой раздел';
        return $result;
    }

    $subIdFind = (!empty($_POST['sub_id_strictFind']) ? $parentSub['Subdivision_ID'] : '');

    foreach ($_POST['subs'] as $sub) {
        $parentSub['priority']++;
        $Hidden_URL = $parentSub['Hidden_URL'] . encodestring($sub, 1) . "/";

        $isSub = $db->get_var("SELECT Subdivision_ID FROM Subdivision WHERE Catalogue_ID = '{$_POST['catalogue']}' AND Hidden_URL = '{$Hidden_URL}'");

        if ($isSub) {
            $db->query(
                "UPDATE
                    Subdivision
                SET
                    Checked = 0,
                    " . ($subIdFind ? "sub_find = '{$subIdFind}'," : null) . "
                    find = '{$sub}'
                    
                WHERE Subdivision_ID = '{$isSub}'"
            );
            $result['data'][] = [$sub . "{$isSub}", 'Обновлен'];
            continue;
        }

        $db->query(
            "INSERT INTO Subdivision
                    (
                        Catalogue_ID,
                        Parent_Sub_ID,
                        Subdivision_Name,
                        Priority,
                        Checked,
                        EnglishName,
                        Hidden_URL,
                        code1C,
                        find,
                        " . ($subIdFind ? "sub_find," : null) . "
                        subdir
                    ) 
                VALUES
                    (
                        '{$_POST['catalogue']}',
                        '{$parentSub['Subdivision_ID']}',
                        '" . addslashes($sub) . "',
                        '{$parentSub['priority']}',
                        '0',
                        '" . encodestring($sub, 1) . "',
                        '{$Hidden_URL}',
                        'multi_create_seo',
                        '$sub',
                        " . ($subIdFind ? "'{$subIdFind}'," : null) . "
                        3
                    )"
        );
        $subID = $db->insert_id;

        if (empty($subID)) {
            $result['data'][] = [$sub, 'Не создан'];
            continue;
        }

        $db->query(
            "INSERT INTO 
                Sub_Class
                    (
                        Subdivision_ID,
                        Class_ID,
                        Sub_Class_Name,
                        EnglishName,
                        Checked,
                        Catalogue_ID,
                        DefaultAction,
                        AllowTags,
                        NL2BR,
                        UseCaptcha,
                        CacheForUser,
                        RecordsPerPage
                    ) 
            VALUES
                    (
                        '{$subID}',
                        2001,
                        '" . encodestring($sub, 1) . "',
                        '" . encodestring($sub, 1) . "',
                        1,
                        {$_POST['catalogue']},
                        'index',
                        '-1',
                        '-1',
                        '-1',
                        '-1',
                        '50')"
        );
        $ccID = $db->insert_id;
        if (empty($ccID)) {
            $result['data'][] = [$sub, 'Не создан'];
        } else {
            $result['data'][] = [$sub . "{$subID}", 'Создан'];
        }
    }
    $result['message'] = 'Совершилось !!!';
    $result['status'] = 1;
    return $result;
}

// $_POST['sub_id_serch'] = 424194;
// var_dump(CollectSubSeo());
function CollectSubSeo()
{
    global $db;

    $result = [
        'status' => 0,
        'message' => '',
        'data' => []
    ];

    $serchSub = $db->get_row(
        "SELECT 
            Subdivision_ID,
            Hidden_URL
        FROM
            Subdivision
        WHERE
            Subdivision_ID = '{$_POST['sub_id_serch']}'",
            ARRAY_A
    );

    if(empty($_POST['sub_id_serch'])){
        $result['message'] = 'пусто!';
        return $result;
    }

    if(empty($serchSub)){
        $result['message'] = 'пусто!';
        return $result;
    }
    
    $result['message'] = 'Совершилось !!!';
    $result['status'] = 1;
    return $result;
}
function validate()
{
    global  $db;

    $result = [
        'status' => 0,
        'message' => '',
        'data' => []
    ];

    if (empty($_FILES['sub_file']['size']) || empty($_FILES['sub_file']['tmp_name'])) {
        $result['message'] = 'Нет файла';
        return $result;
    }

    if (empty($_POST['sub_id'])) {
        $result['message'] = 'Нет ID раздела';
        return $result;
    }
    $sub_id = $_POST['sub_id'] + 0;
    $catalogue = $_POST['catalogue'] + 0;
    $isSub = $db->get_var("SELECT Subdivision_Name as id FROM Subdivision WHERE Catalogue_ID = '{$catalogue}' AND Subdivision_ID = '{$sub_id}'");

    if (empty($isSub)) {
        $result['message'] = 'Такого раздела нет на сайте';
        return $result;
    }

    $content = array_unique(array_filter(explode("\r\n", file_get_contents($_FILES['sub_file']['tmp_name']))));
    $result['data'] = ['subs' => $content, 'sub_id' => $sub_id, 'catalogue' => $catalogue, 'sub_id_strictFind' => $_POST['sub_id_strictFind'], 'sb_sub_find' => $_POST['sub_id_serch']];
    $result['message'] = "В разделе \"{$isSub}\", будут созданы следующие разделы:";
    $result['status'] = 1;
    return $result;
}
