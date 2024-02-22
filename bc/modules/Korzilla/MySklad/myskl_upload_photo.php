<?php
ini_set('memory_limit', '600M');
set_time_limit(0);

use App\modules\Korzilla\MySklad\Auth;
use App\modules\Korzilla\MySklad\Products;

require_once '/var/www/krza/data/www/krza.ru/bc/modules/default/include_console/include_console.php';

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db, $nc_core, $current_catalogue, $INCLUDE_FOLDER, $waterFile, $dirMySklad, $waterPosition, $pathInc, $pathInc2, $setting;

require_once $INCLUDE_FOLDER . "classes/nc_imagetransform.class.php";

if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
}

while (ob_get_level() > 0) {
    ob_end_flush();
}

$products = new Products();
$offset = 0;
$limit = 100;
$items = [];

$dirMySklad = $ROOTDIR . $pathInc."/mysklad/";
$logPath = $dirMySklad . 'log.txt';
if (file_exists($ROOTDIR . $pathInc . '/images/watermark.png')) $waterFile = $ROOTDIR . $pathInc . '/images/watermark.png';
$waterPosition = ($setting['waterPosition'] ? ($setting['waterPosition'] == 5 ? "0" : $setting['waterPosition']) : 4);

if (!file_exists($dirMySklad)) {
	mkdir($dirMySklad, 0775, true);
}

if (!$setting['login_ms'] || !$setting['password_ms']) die('no password');

Auth::getToken($setting['login_ms'], $setting['password_ms']);

file_put_contents($logPath, "START\n\r".$tocken."\r\n");
do {
    echo '-L-';
    flush();
    ob_flush();
    $assortiment = $products->getAssortimentList(Auth::$token, ['expand' => 'images', 'limit' => $limit, 'offset' => $offset]);
	//echo "\r\n".print_r($assortiment,1);
	flush();
    ob_flush();
	
    $assortimentCount = $assortiment['meta']['size'];
	if (!$assortimentCount) die('no assortimentCount');
	
    $offset = $assortiment['meta']['offset'] + $limit;
    echo "{$assortimentCount} / {$offset}";
	
    foreach ($assortiment['rows'] as $item) {
        $itemDB = $db->get_row(
            "SELECT 
                Message_ID,
                name
            FROM 
                Message2001 
            WHERE 
                code = '{$item['externalCode']}' 
                AND Catalogue_ID = {$current_catalogue['Catalogue_ID']} 
            LIMIT 0,1",
            ARRAY_A
        );
        if (empty($itemDB)) {
            file_put_contents($logPath, print_r(array_merge(['товар не найден'], ['id' => $item['externalCode']]), true), FILE_APPEND);
            continue;
        }

        foreach ($item['images']['rows'] as  $image) {
            $info = new \SplFileInfo($image['filename']);
            $image['filename'] = encodestring($info->getBasename('.' . $info->getExtension()), 1) . '.' . $info->getExtension();
            $imagePath = getImegeMySklad(Auth::$token, ['link' => $image['meta']['downloadHref'], 'filename' => $image['filename']]);
            echo '-p-';
            flush();
            ob_flush();
            if ($imagePath === false) {
                file_put_contents($logPath, print_r(array_merge(['файл не удалось скачать'], ['id' => $item['externalCode']]), true), FILE_APPEND);
                continue;
            }
            $res = uploadPhote($itemDB, ['filename' => $image['filename'], 'size' => filesize($imagePath), 'path' => $imagePath]);
            sleep(1);
            file_put_contents($logPath, print_r(array_merge($res, ['id' => $item['externalCode']]), true), FILE_APPEND);
            unlink($imagePath);
        }
        echo '-i-';
        flush();
        ob_flush();
    }

    sleep(3);
} while ($assortiment['meta']['offset'] < $assortimentCount);


function getImegeMySklad($token, $image)
{
	global $dirMySklad;
    $downloadedPath = $dirMySklad . '/images_tmp/' . $image['filename'];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ],
        CURLOPT_URL => $image['link'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    curl_exec($ch);
    $downloadedLink = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $content = null;
    if ($downloadedLink) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HEADER => false,
            CURLOPT_URL => $downloadedLink,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $res = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code >= 200 && $$http_code  < 300 && $res) $content = $res;
        curl_close($ch);
		if ($content) echo "* "; else echo "X ";
		flush();
        ob_flush();
    }

    if (file_put_contents($downloadedPath, $content)) {
		return $downloadedPath;
		if ($content) echo "P ";
	} else {
		echo "N ";
		
	}
	flush();
    ob_flush();
    return false;
}

function uploadPhote($item, $image)
{
    global $pathInc, $db, $setting, $waterFile, $waterPosition;

    $ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
    $priorPhoto = 0;
    $photoPathDir = $pathInc . "/files/multifile/2353/{$item['Message_ID']}/";
    $photoPathLocal = $photoPathDir . $image['filename'];
    $photoPriviewPathLocal = $photoPathDir . 'preview_' . $image['filename'];
    $photoPath = $ROOTDIR . $photoPathLocal;
    $photoPriviewPath = $ROOTDIR . $photoPriviewPathLocal;
    $res = ['status' => 0, 'message' => 'Ошибка', 'item' => $item];
    $priorPhoto = 0;
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
    @mkdir($ROOTDIR . $photoPathDir, 0777, true);
	
	normalizeImageRotateWithEXIF($image['path']);
	
    if ($image['size'] > 800) {
        @\nc_ImageTransform::imgResize($image['path'], $photoPath, 800, 800, 0, "", 90);
    } else {
        @copy($image['path'], $photoPath);
    }

    $previwSize = $setting['size2001_imagepx'] ? $setting['size2001_imagepx'] : "300";
    if ($image['size'] > $previwSize) {
        @\nc_ImageTransform::imgResize($image['path'], $photoPriviewPath, $previwSize, 900, 0, "", 90);
    } else {
        @copy($image['path'], $photoPriviewPath);
    }

    if (file_exists($photoPath) && $waterFile && $image['size'] >= 400) {
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
                            '{$image['size']}',
                            " . time() . "
                        )"
            );

            if ($db->insert_id) $res = ['status' => 1, 'message' => 'Фото создано'];
            else $res = ['status' => 0, 'message_error' => 'Фото не создано в базе'];
        } else {  // изменить фото в БД
            $update = $db->query(
                "UPDATE
                    Multifield
                SET 
                    Field_ID = '2353', 
                    Path = '{$photoPathLocal}',
                    Preview = '{$photoPriviewPathLocal}',
                    SizeOrig = '{$image['size']}',
                    file1Ctime = " . time() . "
                WHERE
                    Field_ID = '2353'
                    AND ID = '{$curPhoto[$photoPathLocal]['ID']}'
                    AND Message_ID = '{$item['Message_ID']}'"
            );

            if ($update) $res = ['status' => 1, 'message' => 'Фото обновлено'];
            else $res = ['status' => 0, 'message_error' => 'Фото не обновлено в базе'];
        }
    }

    return $res;
}
