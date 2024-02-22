<?php 
ini_set('memory_limit', '600M');

set_time_limit(1000000);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
GLOBAL $pathInc, $catalogue, $nc_core, $setting, $DOCUMENT_ROOT;

require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
require_once ($INCLUDE_FOLDER."classes/nc_imagetransform.class.php");

while (ob_get_level() > 0) {
    ob_end_flush();
}

// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

# /import/temp/
var_dump($pathInc);
$pathImportPhoto = $pathInc.'/files/import/temp/';
var_dump($pathImportPhoto);
if (file_exists($ROOTDIR.$pathInc.'/images/watermark.png')) $waterFile = $ROOTDIR.$pathInc.'/images/watermark.png';
$waterPosition = ($setting['waterPosition'] ? ($setting['waterPosition']==5 ? "0" : $setting['waterPosition']) : 4);

$photoArr = scandir($ROOTDIR.$pathImportPhoto);
unset($photoArr[0],$photoArr[1]); // убираем из результата . и ..
$count = 0;

echo "Загрузка:<br>";
unset($reslt);
flush();

if ($action=='import') {
	foreach($photoArr as $photo) { // цикл фото товара

		$photoOrig = $photo;

		$jpgz = array(".JPG"=>".jpg",".JPEG"=>".jpg",".PNG"=>".png");
		foreach ($jpgz as $caps => $low) {
			if (strstr($photo,$caps)) $photo = str_replace($caps, $low, $photo);
		}
		$photo = preg_replace("/ (?=.jpg|.jpeg|.png)/", "", $photo);

		if (strstr($photo, " ")) {
			$photo = str_replace("/","-",$photo);
			$photo = str_replace(" ","-",$photo);
		}

		$photoPathOrig = $pathImportPhoto.$photoOrig;
		$photoSize = @getimagesize($ROOTDIR.$photoPathOrig);
		# /test2/ для теста
		$photoPathNew = $pathInc.'/files/import/'.$photo;
		$photoPathOld = $pathInc.'/files/import/old/'.$photoOrig;

		# conver webp => jpeg
		if (strstr($photo,'webp')) {
			continue;
			// $im = imagecreatefromwebp($ROOTDIR.$photoPathOrig);
			// $convertedimg = str_replace('webp','jpeg',$ROOTDIR.$photoPathNew);
			// $reslt = imagejpeg($im, $convertedimg, 100);
			// if ($reslt) {
			// 	$photoPathOrig = str_replace('webp','jpeg',$photoPathOrig);
			// 	$photoPathNew = str_replace('webp','jpeg',$photoPathNew);
			// 	$photoPathOld = str_replace('webp','jpeg',$photoPathOld);
			// 	imagedestroy($im);
			// }
		}

		// echo print_r($photoPathOrig,1)."<br>".print_r($photoPathNew,1)."<br>".print_r($photoPathOld,1)."<br><br>";
		// return false;

		# включать перезаписывание
		// if (!file_exists($ROOTDIR.$photoPathNew)) { // создать файл
		
			// делаем переворот фотки, если надо
			if (file_exists($ROOTDIR.$photoPathOrig)) normalizeImageRotateWithEXIF($ROOTDIR.$photoPathOrig);

			if ($photoSize[0]>800){
				nc_ImageTransform::imgResize($ROOTDIR.$photoPathOrig, $ROOTDIR.$photoPathNew,800,800, 0, "", 90);
				$prnt = "! ";
			} else {
				copy($ROOTDIR.$photoPathOrig, $ROOTDIR.$photoPathNew);
				$prnt = ". ";
			}
			if (file_exists($ROOTDIR.$photoPathNew) && $waterFile) {
				rename($ROOTDIR.$photoPathOrig,$ROOTDIR.$photoPathOld);
				nc_ImageTransform::putWatermark_file($ROOTDIR.$photoPathNew, $waterFile, $waterPosition);
				$prnt .= "* ";
			}
		// }
		# удалить 
		if (strstr($prnt, '*')) {
			// unlink($ROOTDIR.$photoPathOrig);
			$count++;
		}
		echo $prnt;
		flush();
		ob_flush();
		$prnt = "";
	} // foreach
	if ($count>0) {
		echo "OK. Успешно выгружены ".$count." шт.";
		// $frommail = "info@".$current_catalogue['Catalogue_Name'];
		// $mailer = new CMIMEMail();
		// $mailer->setCharset('utf-8');
	 //    $mailer->mailbody("фото успешно выгружены ".$count." шт.");
	 //    $mailer->send("georgiy@korzilla.ru", $frommail, $frommail, "фото загружены", $current_catalogue['Catalogue_Name']);
	}
	else echo "Фотографии не найдены либо уже есть в конечной папке.";
}
else {
	echo "Ошибка. Неверные параметры.";
}
?>