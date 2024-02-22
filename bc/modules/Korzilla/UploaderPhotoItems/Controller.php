<?php

namespace App\modules\Korzilla\UploaderPhotoItems;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $current_catalogue, $catalogue, $nc_core, $INCLUDE_FOLDER;

require_once $INCLUDE_FOLDER . "classes/nc_imagetransform.class.php";

if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

class Controller
{
    public static function getTemplateSetting()
    {
        global $db;
        $mimeType = $db->get_var(
            "SELECT 
                SUBSTRING_INDEX(SUBSTRING_INDEX(Format, ';', 1), ':', -1)
            FROM 
                Field
            WHERE 
                Class_ID = 2001
                AND Field_Name = 'photo'"
        );
        return k_renderTemplate(__DIR__ . '/templates/index.html', ['mimeType' => $mimeType]);
    }

    public static function uploadPhotes()
    {
        global $catalogue, $db, $pathInc, $ROOTDIR, $setting, $INCLUDE_FOLDER;

        $ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
        $countPhoto = count($_FILES['photoItem']['name']);
        $res = [];
        if (file_exists($ROOTDIR . $pathInc . '/images/watermark.png')) $waterFile = $ROOTDIR . $pathInc . '/images/watermark.png';
        $waterPosition = ($setting['waterPosition'] ? ($setting['waterPosition'] == 5 ? "0" : $setting['waterPosition']) : 4);

        for ($i = 0; $i < $countPhoto; $i++) {

            $filename = $_FILES['photoItem']['name'][$i];
            $photoSize = $_FILES['photoItem']['size'][$i];
            $photoPathOrig = $_FILES['photoItem']['tmp_name'][$i];

            if ($_FILES['photoItem']['error'][$i] != 0) {
                $phpFileUploadErrors = [
                    1 => 'Загруженный файл превышает директиву upload_max_filesize в php.ini.',
                    2 => 'Загруженный файл превышает директиву MAX_FILE_SIZE, указанную в HTML-форме.',
                    3 => 'Загруженный файл был загружен только частично',
                    4 => 'Файл не загружен',
                    6 => 'Отсутствует временная папка',
                    7 => 'Не удалось записать файл на диск.',
                    8 => 'Расширение PHP остановило загрузку файла.',
                ];
                $res[$i] = ['status' => 0, 'message_error' => $phpFileUploadErrors[$_FILES['photoItem']['error'][$i]]];
                continue;
            }

            preg_match('/(?<name>.+?)(?<prior>_\d){0,1}$/m', pathinfo($filename, PATHINFO_FILENAME), $attrValue);

            $res[$i] = ['status' => 0, 'message_error' => 'Ошибка' . print_r($attrValue, 1)];

            $items = $db->get_results(
                "SELECT
                    Message_ID,
                    name
                FROM 
                    Message2001
                WHERE 
                    Catalogue_ID = {$catalogue}
                    AND {$_POST['attr']} = '{$attrValue['name']}'",
                ARRAY_A
            );


            if (empty($items)) {
                $res[$i] = ['status' => 0, 'message_error' => 'Товар не найден'];
                continue;
            }

            foreach ($items as $item) {
                $priorPhoto = trim($attrValue['prior'], '_') ?: 0;
                $photoPathDir = $pathInc . "/files/multifile/2353/{$item['Message_ID']}/";
                $photoPathLocal = $photoPathDir . $filename;
                $photoPriviewPathLocal = $photoPathDir . 'preview_' . $filename;
                $photoPath = $ROOTDIR . $photoPathLocal;
                $photoPriviewPath = $ROOTDIR . $photoPriviewPathLocal;

                $curPhotoArr = $db->get_results(
                    "SELECT
                        `ID`,
                        `Path`,
                        `SizeOrig`,
                        `file1Ctime`,
                        `Priority`
                    FROM
                        Multifield
                    WHERE
                        Field_ID = 2353 
                        AND Message_ID = '{$item['Message_ID']}'",
                    ARRAY_A
                );
                $curPhoto = [];
                if ($curPhotoArr) {
                    foreach ($curPhotoArr as $p) {
                        if ($p['Priority'] > $priorPhoto) $priorPhoto = $p['Priority'];
                        $curPhoto[$p['Path']]['ID'] = $p['ID'];
                        $curPhoto[$p['Path']]['SizeOrig'] = $p['SizeOrig'];
                        $curPhoto[$p['Path']]['file1Ctime'] = $p['file1Ctime'];
                    }
                }
                $log = [];
                @mkdir($ROOTDIR . $photoPathDir, 0777, true);
                if ($photoSize > 800) {
                    try {
                        $log = \nc_ImageTransform::imgResize($photoPathOrig, $photoPath, 800, 800, 0, "", 90);
                    } catch (\Exception $e) {
                    }
                } else {
                    @copy($photoPathOrig, $photoPath);
                }

                $previwSize = $setting['size2001_imagepx'] ? $setting['size2001_imagepx'] : "300";
                if ($photoSize > $previwSize) {
                    try {
                        \nc_ImageTransform::imgResize($photoPathOrig, $photoPriviewPath, $previwSize, 900, 0, "", 90);
                    } catch (\Exception $e) {
                    }
                } else {
                    try {
                        copy($photoPathOrig, $photoPriviewPath);
                    } catch (\Exception $e) {
                    }
                }

                if (file_exists($photoPath) && $waterFile && $photoSize >= 400) {
                    @\nc_ImageTransform::putWatermark_file($photoPath, $waterFile, $waterPosition);
                }

                if (file_exists($photoPath)) {
                    if (!$curPhoto[$photoPathLocal]['ID']) { // добавить фото в БД
                        $db->query(
                            "INSERT INTO 
                                Multifield 
                                    (
                                        Field_ID,
                                        Message_ID,
                                        Priority,
                                        Name,
                                        Size,
                                        Path,
                                        Preview,
                                        SizeOrig,
                                        file1Ctime
                                    )
                            VALUES 
                                    (
                                        2353,
                                        '{$item['Message_ID']}',
                                        '{$priorPhoto}',
                                        '{$item['name']}',
                                        '2',
                                        '{$photoPathLocal}',
                                        '{$photoPriviewPathLocal}',
                                        '{$photoSize}',
                                        " . time() . "
                                    )"
                        );

                        if ($db->insert_id) $res[$i] = ['status' => 1, 'message' => 'Фото создано'];
                        else $res[$i] = ['status' => 0, 'message_error' => 'Фото не создано в базе'];
                    } else {  // изменить фото в БД
                        $update = $db->query(
                            "UPDATE
                                Multifield
                            SET 
                                Field_ID = '2353', 
                                Path = '{$photoPathLocal}',
                                Preview = '{$photoPriviewPathLocal}',
                                SizeOrig = '{$photoSize}',
                                file1Ctime = " . time() . "
                            WHERE
                                Field_ID = '2353'
                                AND ID = '{$curPhoto[$photoPathLocal]['ID']}'
                                AND Message_ID = '{$item['Message_ID']}'"
                        );

                        if ($update) $res[$i] = ['status' => 1, 'message' => 'Фото обновлено'];
                        else $res[$i] = ['status' => 0, 'message_error' => 'Фото не обновлено в базе'];
                    }
                } else {
                    $res[$i] = ['status' => 0, 'message_error' => 'Не удалось записать фото на сервер' . $log];
                }
            }
        }
        return json_encode($res);
    }
}
