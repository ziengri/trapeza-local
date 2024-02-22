<?
/* консольный режим (для терминала или cron)
/opt/php71/bin/php /var/www/krza/data/www/krza.ru/export_1c.php kitaigarage.ru [debug|notest|v1c2]
*/
if ($argv[1]) {
	$_SERVER['HTTP_HOST'] = $argv[1];
	$_SERVER['DOCUMENT_ROOT'] = str_replace("/export_1c.php","", $argv[0]);
	$action = 'import';
	if ($argv[3]=='notest' || $argv[2]=='notest' || $argv[2]=='debug') $notest = 1;
	if ($argv[2]=='debug') $debug = 1;
	if ($argv[2]=='v1c2') $v1c = 2;
}

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

ini_set('memory_limit', '1600M');
set_time_limit(1000000);

GLOBAL $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;
//require_once $ROOTDIR."/bc/modules/bitcat/class.upload.php";
require_once ($INCLUDE_FOLDER."classes/nc_imagetransform.class.php");

clearSeoCach();

while (ob_get_level() > 0) {
    ob_end_flush();
}

if (!$v1c && file_exists($ROOTDIR.$pathInc2."/export_1c.php")) {
	if (!$action) die("<meta http-equiv='refresh' content='0; url=".$pathInc2."/export_1c.php'>");
	die('Ошибка: Консоль доступна только из директории проекта');
}

// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}
// Собераем доп параметры товаров 
if(!$setting['lists_params']) $setting['lists_params'] = [];
foreach ($setting['lists_params'] as $value) {
	$paramsList[$value['keyword']] = $value;
}


// переменные

$path1c = $ROOTDIR.$pathInc.'/1C'.$v1c.'/';
$path1c2 = $pathInc.'/1C'.$v1c.'/';


$logPath = $path1c.'log1c.log';
$stopGroupPath = $path1c.'groupstop.ini';
$ignoreGroupPath = $path1c.'groupignore.ini';
$paramPath = $path1c.'itemparam.ini';
$paramPathRekv = $path1c.'itemparamR.ini';
$iniSetPath = $path1c.'expsetting.ini';

$path1cPhoto = $pathInc.'/files/userfiles/images/catalog/';
@mkdir($ROOTDIR.$path1cPhoto, 0775);
if (file_exists($ROOTDIR.$pathInc.'/images/watermark.png')) $waterFile = $ROOTDIR.$pathInc.'/images/watermark.png';
$waterPosition = ($setting['waterPosition'] ? ($setting['waterPosition']==5 ? "0" : $setting['waterPosition']) : 4);

if (file_exists($iniSetPath)) $expSet = parse_ini_file($iniSetPath);
$classNum = ($expSet['classNum'] ? $expSet['classNum'] : 2001);

if ($expSet['onestock']>0) $onestock = $expSet['onestock']; //Только количество, не раpбивать по складам
if ($expSet['catalogid']>0) $catalogid = $expSet['catalogid'];
if ($expSet['noprice']>0) $noprice = $expSet['noprice'];
if ($expSet['nooffers']>0) $nooffers = $expSet['nooffers'];
if ($expSet['debug']>0) $debug = $expSet['debug'];
if ($expSet['notest']>0) $notest = $expSet['notest'];
if ($expSet['noupdcatalog']>0) $noupdcatalog = $expSet['noupdcatalog'];
if ($expSet['zipfile']) $zipfile = strip_tags($expSet['zipfile']);
if (is_array($expSet['fprice'])) $fprice = $expSet['fprice'];
if (is_array($expSet['fstock'])) $fstock = $expSet['fstock'];
if (is_array($expSet['ignoreitem'])) $ignoreitem = $expSet['ignoreitem'];
$nolog = $expSet['nolog'];

// запускать если не скрипт работает 
if (!file_exists($logPath) || (file_exists($logPath) && filemtime($logPath)<time()-120) || $notest) {} else die("Ошибка: Выгрузка уже запущена, нельзя запустить повторно");

$currencyArray = array("RUR"=>1,"USD"=>2,"EUR"=>3);

$catalogSQL = ($catalogid>0 ? "a.Subdivision_ID = '{$catalogid}'" : "a.Hidden_URL = '/catalog/'");
$idsubArr = $db->get_row("select SQL_NO_CACHE b.Subdivision_ID as sub, b.Sub_Class_ID as cc from Subdivision as a, Sub_Class as b where {$catalogSQL} AND a.Catalogue_ID = '{$catalogue}' AND a.Subdivision_ID = b.Subdivision_ID", ARRAY_A);
$idsub = $idsubArr['sub'];
$rootsub = $idsub;
if (is_numeric($_GET[idsub2])) $idsub2 = $_GET[idsub2];
$idcc = $idsubArr['cc'];
$RecordsPerPage = ($expSet['RecordsPerPage'] ? $expSet['RecordsPerPage'] : 50);
if (!$v1c) $v1c = ($expSet['v1c'] ? $expSet['v1c'] : (is_numeric($_GET['v1c']) ? $_GET['v1c'] : null));


if ($idsub) $classNumTemp = $db->get_var("select SQL_NO_CACHE Class_Template_ID from Sub_Class where Subdivision_ID = '{$idsub}' AND Catalogue_ID = '{$catalogue}'");
$priority = 1;
$field_connect = ($_GET['field'] ? strip_tags($_GET['field']) : "code");
// Передать $_GET[apdurl] для смены URL на основе ИД 1С



echo "<form method=get>
".($catalogid>0 ? "<input type='hidden' name='catalogid' value='$catalogid'>" : "")."
".($v1c>0 ? "<input type='hidden' name='v1c' value='$v1c'>" : "")."
".($noprice ? "<input type='hidden' name='noprice' value='1'>" : "")."
".($notest ? "<input type='hidden' name='notest' value='1'>" : "")."
".($nooffers ? "<input type='hidden' name='nooffers' value='1'>" : "")."
".($fprice[0] ? "<input type='hidden' name='fprice[0]' value='{$fprice[0]}'>" : "")."
".($fprice[1] ? "<input type='hidden' name='fprice[1]' value='{$fprice[1]}'>" : "")."
".($fprice[2] ? "<input type='hidden' name='fprice[2]' value='{$fprice[2]}'>" : "")."
".($fprice[3] ? "<input type='hidden' name='fprice[3]' value='{$fprice[3]}'>" : "")."
".($fprice[4] ? "<input type='hidden' name='fprice[4]' value='{$fprice[4]}'>" : "")."
".($fprice[5] ? "<input type='hidden' name='fprice[5]' value='{$fprice[5]}'>" : "")."
".($fprice[6] ? "<input type='hidden' name='fprice[6]' value='{$fprice[6]}'>" : "")."
".($fprice[7] ? "<input type='hidden' name='fprice[7]' value='{$fprice[7]}'>" : "")."
".($fprice[8] ? "<input type='hidden' name='fprice[8]' value='{$fprice[8]}'>" : "")."
".($fprice[9] ? "<input type='hidden' name='fprice[9]' value='{$fprice[9]}'>" : "")."
".($fprice[10] ? "<input type='hidden' name='fprice[10]' value='{$fprice[10]}'>" : "")."
".($fprice[11] ? "<input type='hidden' name='fprice[11]' value='{$fprice[11]}'>" : "")."
".($fprice[12] ? "<input type='hidden' name='fprice[12]' value='{$fprice[12]}'>" : "")."
".($fprice[13] ? "<input type='hidden' name='fprice[13]' value='{$fprice[13]}'>" : "")."
".($fprice[14] ? "<input type='hidden' name='fprice[14]' value='{$fprice[14]}'>" : "")."
".($fprice[15] ? "<input type='hidden' name='fprice[15]' value='{$fprice[15]}'>" : "")."
".($fprice[16] ? "<input type='hidden' name='fprice[16]' value='{$fprice[16]}'>" : "")."
".($fprice[17] ? "<input type='hidden' name='fprice[17]' value='{$fprice[17]}'>" : "")."
".($fprice[18] ? "<input type='hidden' name='fprice[18]' value='{$fprice[18]}'>" : "")."
".($fprice[19] ? "<input type='hidden' name='fprice[19]' value='{$fprice[19]}'>" : "")."
".($fprice[20] ? "<input type='hidden' name='fprice[20]' value='{$fprice[20]}'>" : "")."
".($fprice[21] ? "<input type='hidden' name='fprice[21]' value='{$fprice[21]}'>" : "")."
".($fprice[22] ? "<input type='hidden' name='fprice[22]' value='{$fprice[22]}'>" : "")."
".($fprice[23] ? "<input type='hidden' name='fprice[23]' value='{$fprice[23]}'>" : "")."
".($fprice[24] ? "<input type='hidden' name='fprice[24]' value='{$fprice[24]}'>" : "")."

<input type='hidden' name='action' value='import'>
<input type='submit' value='Обновить каталог'></form>";
if (!$action) die;

//echo "$idsub $idcc $catalogue $action $pathInc $field_connect";

if (!$idsub || !$idcc || !$catalogue || !$action || !$pathInc || !$field_connect) die('Нет параметров: '."$idsub $idcc $catalogue $action $pathInc $field_connect");

echo "Загрузка:<br>";
file_put_contents($logPath, "####### Загрузка\n\n"); unset($reslt);
flush();

if ($action=='import') {
	global $tovar;

	#если все файлы
	if (!$zipfile) {
		if ((!$nooffers && (!file_exists($ROOTDIR.$pathInc."/1C{$v1c}/import.xml") || !file_exists($ROOTDIR.$pathInc."/1C{$v1c}/offers.xml"))) || ($nooffers && !file_exists($ROOTDIR.$pathInc."/1C{$v1c}/import.xml"))) {
			$reslt = "### Ошибка: есть не все файлы ###";
			if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
			echo "Ошибка: есть не все файлы";
			exit;
		} else {
			$reslt = "### OK: файлы найдены ###";
			if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
		}
	}

	# есть ли новые файлы?
	if (!strstr($zipfile,";")) {
		$testFile = ($zipfile && file_exists($ROOTDIR.$pathInc."/1C{$v1c}/{$zipfile}") ? $zipfile : "import.xml");
		$file1Ctime = filemtime($ROOTDIR.$pathInc."/1C{$v1c}/{$testFile}");

		if (file_exists($ROOTDIR.$pathInc."/1C{$v1c}/offers.xml")) $file1Ctime2 = filemtime($ROOTDIR.$pathInc."/1C{$v1c}/offers.xml");
		if ($file1Ctime2 && $file1Ctime2>$file1Ctime) $file1Ctime = $file1Ctime2;
		if ($db->get_var("select SQL_NO_CACHE file1Ctime{$v1c} from Catalogue where Catalogue_ID = '".$catalogue."'")==$file1Ctime && !$notest) {
			$reslt = "Файлы выгрузки ".($zipfile ? "ZIP" : "XML")." не обновлены. Текущие файлы уже были загружены в каталог ранее.";
			echo $reslt;
			if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
			die;
		}
	}

	# извлечение из архива

	if ($zipfile) {
		if (strstr($zipfile,";")) {
			foreach(explode(";",$zipfile) as $zip1) {
				if (file_exists($ROOTDIR.$pathInc."/1C{$v1c}/{$zip1}")) unzip1($zip1);
			}
		} else {
			if (file_exists($ROOTDIR.$pathInc."/1C{$v1c}/{$zipfile}")) unzip1($zipfile);
		}
	}


	if ($pathInc) $url = file_get_contents($ROOTDIR.$pathInc."/1C{$v1c}/import.xml",0);
	$xml = new SimplexmlElement($url);
	$url = '';
	echo "-1-\n";
	flush();
	ob_flush();

	# ############ СодержитТолькоИзменения ############
	global $updatecatalog;
	if ($xml->Каталог->attributes()) {
			foreach($xml->Каталог->attributes() as $k => $v) {
				if ($k == 'СодержитТолькоИзменения' && $v=='true') $updatecatalog = 1;
			}
	}
	if ($noupdcatalog) $updatecatalog = "";

	# ############ группы #################

	if ($xml->Каталог->Товары->Товар) {
		$groupArr = ($xml->Каталог->Группы ? $xml->Каталог->Группы : $xml->Классификатор->Группы);
		if (!$groupArr) $groupArr = $xml->Группы;
		
		if ($expSet['catgroupname']) $groupArr = $xml->Категории->Категория;
		
		//if (!$nolog) file_put_contents($logPath, print_r($groupArr,1), FILE_APPEND);
		if ($groupArr) {
			if (!$updatecatalog) $db->query("update Subdivision set Checked = '0' where v1c = '".$v1c."' AND code1C != '' AND Catalogue_ID = '".$catalogue."'");
			podrazd($groupArr,$idsub,$v1c,'');
		}

		echo "-2-\n";
		flush();
		ob_flush();

		if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
		//$db->query("UPDATE Message$classNum SET Checked = '0'");
		# добавляем товары
		//$db->query("TRUNCATE table Message$classNum");
		//$db->query("DELETE FROM Multifield where Size = '2'");
	} else {
		die('Нет раздела Товар');
	}


	# ############ свойства #################
	if ($xml->Классификатор->Свойства) {

		foreach($xml->Классификатор->Свойства->Свойство as $svstvo) {
			$idSvoistva = (string)$svstvo->Ид;
			$nameSvoistva = (string)$svstvo->Наименование;
			if(!$paramsList[$idSvoistva]) $paramsList[$idSvoistva] = ["keyword" => $idSvoistva, "name" => $nameSvoistva, "priority" => count($paramsList) + 1, 'checked' => 1];
			
			if ($expSet['svgroupname'] && $expSet['svgroupcatalogid']>0 && $nameSvoistva==$expSet['svgroupname']) {
				$reslt .= podrazd($svstvo->ВариантыЗначений,$expSet['svgroupcatalogid'],$v1c,1);
				if ($svstvo->ВариантыЗначений) {
					foreach($svstvo->ВариантыЗначений->Справочник as $svstvoV) { unset($ids);
						$ids = (string)$svstvoV->ИдЗначения;
						$svstvoArr[$ids]['val'] = str_replace("\xC2\xA0", " ", (string) $svstvoV->Значение);
						$svstvoArr[$ids]['ids'] = $ids;
					}
				}
			}

			if ($svstvo->ВариантыЗначений) {
				foreach($svstvo->ВариантыЗначений->Справочник as $svstvoV) {
					$svoistva[(string)$svstvoV->ИдЗначения]['name'] = str_replace("\xC2\xA0", " ", (string) $svstvoV->Значение);
					$svoistva[(string)$svstvoV->ИдЗначения]['type'] = (string)$svstvo->Наименование;
					$svoistva[(string)$svstvoV->ИдЗначения]['id'] = (string)$svstvo->Ид;
				}
			} elseif ($svstvo->Наименование) {
				$svoistva[(string)$svstvo->Ид]['type'] = (string)$svstvo->Наименование;
				$svoistva[(string)$svstvo->Ид]['id'] = (string)$svstvo->Ид;
			}

		
			$setting['lists_params'] = array_values($paramsList);
			setSettings($setting);
		}
		if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
	}
	echo "-3-\n";
	flush();
	ob_flush();

	# массив сопоставление id свойств полям сайта
	if (file_exists($paramPath)) {
		$svArr = explode(PHP_EOL,file_get_contents($paramPath));
		if ($svArr && is_array($svArr)) {
			foreach($svArr as $svArr2) {
				$svVar = NULL;
				if (strstr($svArr2,";")) {
					$svVar = explode(";",$svArr2);
					if ($svVar[0] && $svVar[1]) $svvo[trim($svVar[0])] = trim($svVar[1]);
				}
			}
		}
	}

	# массив сопоставление id реквизитов полям сайта
	if (file_exists($paramPathRekv)) {
		$rekArr = explode(PHP_EOL,file_get_contents($paramPathRekv));
		if ($rekArr && is_array($rekArr)) {
			foreach($rekArr as $rekArr2) {
				$rekVar = NULL;
				if (strstr($rekArr2,";")) {
					$rekVar = explode(";",$rekArr2);
					if ($rekVar[0] && $rekVar[1]) $rekv[trim($rekVar[0])] = trim($rekVar[1]);
				}
			}
		}
	}

	echo print_r($rekv,1);
	
	echo print_r($ignoreitem,1);
	
	# ############ обработка файла import.xml - структура, товары. #################

	foreach($xml->Каталог->Товары->Товар as $tov) {
		$name = $code = $art = $art2 = $photo = $idgrup = $text = $ves = $analogi = $kod = $tags = $vendor = $var1 = $var2 = $var3 = $var4 = $akcia = $novinka = $ignoreitm = NULL;

		$code = (string)$tov->Ид;
		$art = (string)$tov->Артикул;
		if ($tov->Производитель) (string)$vendor = htmlspecialchars($tov->Производитель);
		if ($tov->Изготовитель->Наименование && !$vendor) (string)$vendor = htmlspecialchars($tov->Изготовитель->Наименование);
		if ($tov->Вес) $ves = (string)$tov->Вес;
		if ($tov->Код) { $kod = (string)$tov->Код; $art2 = $kod; }
		//if (!$art) $art = (string)$tov->Код;
		if (!$art2) $art2 = mb_substr($code,0,8); # если нет артикула - взять из кода
		$idgrup = (string)$tov->Группы->Ид;
		if ($tov->Группа) $idgrup = (string)$tov->Группа; else $idgrup = (string)$tov->Группы->Ид;

		$name = (string)strip_tags(preg_replace("/\s{2,}/", " ", $tov->Наименование));
			$name = addslashes(htmlspecialchars_decode(str_replace("&amp;","&",$name)));
		$text = (string)addslashes(htmlspecialchars_decode(str_replace("&amp;","&",$tov->Описание)));


		if ($tov->БазоваяЕдиница) $edizm = (string)$tov->БазоваяЕдиница;
		if ($tov->БазоваяЕдиница['НаименованиеКраткое'] && !trim($edizm)) $edizm = (string)$tov->БазоваяЕдиница['НаименованиеКраткое'];
		if ($tov->БазоваяЕдиница['НаименованиеПолное'] && !trim($edizm)) $edizm = (string)$tov->БазоваяЕдиница['НаименованиеПолное'];
		if ($tov->БазоваяЕдиница['МеждународноеСокращение'] && !trim($edizm)) $edizm = str_replace(".","",$tov->БазоваяЕдиница['МеждународноеСокращение']);

		if ($tov->Аналоги->Аналог) { # аналоги
			$analogiArr =NULL;
			foreach($tov->Аналоги->Аналог as $ana) {
				$analogiArr[] = (string)$ana->Ид;
			}
			if ($analogiArr) $analogi = implode("\n",$analogiArr);
		}

		#Mir
		/*
		$countLabels = $db->get_var("select * from Message2034 where Catalogue_ID=".$catalogue." and keyw='itemlabel'");
		$countLabels = json_decode($countLabels, 1);
		$countLabels = count($countLabels);*/

		if ($tov->ЗначенияРеквизитов->ЗначениеРеквизита) { # обработка реквизитов
			foreach($tov->ЗначенияРеквизитов->ЗначениеРеквизита as $re) { $zn = $znname = NULL;
				$zn = (string)$re->Значение;
				if ($zn=='true') $zn = '1';
				if ($zn=='false') $zn = '0';
				$znname = (string)$re->Наименование;
				if ($znname == 'Размеры' || $re->Наименование == 'Размер') (string)$size = $zn;
				if ($znname == 'Вес' && !$ves) (string)$ves = $zn;
				if ($znname == 'Производитель' && !$vendor) (string)$vendor = htmlspecialchars($zn);
				if ($znname == 'Название варианта') (string)$variablename = htmlspecialchars($zn);
				if ($znname == 'Соответствия') (string)$analogi = $zn;
				if ($znname == 'ОписаниеВФорматеHTML' && !$text) (string)$text = addslashes(htmlspecialchars_decode(str_replace("&amp;","&",$zn)));
				if ($znname == 'colors') (string)$colors = $zn;
				if ($znname == 'Акция') { (string)$akcia = $zn; $useAkcia = 1; }
				if ($znname == 'Новинка') { (string)$novinka = $zn; $useNovinka = 1; }

				if ($znname == 'Спецпредложение') { $useSpec = 1; }
				if ($znname == 'Не учитывать общую наценку') { $useNotmarkup = 1; }
				if ($znname == 'Возможен торг') { $useTorg = 1; }
				if ($znname == 'Таймер обратного счета') { $useTimer = 1; }
				if ($znname == 'Запретить добавлять товар в корзину') { $useNocart = 1; }

				#if ($znname == 'label' and $zn>=0 and $zn<=$countLabels) { $label = $zn; $useLabel = 1; }
				if ($rekv[$znname]) $tovar[$code][$rekv[$znname]] = (string)$zn;
				if ($tovar[$code]['name']) {
					$name = (string)strip_tags(preg_replace("/\s{2,}/", " ", $tovar[$code]['name']));
						$name = addslashes(htmlspecialchars_decode(str_replace("&amp;","&",$name)));
				}
			}
		}
		if ($tov->ЗначенияСвойств->ЗначенияСвойства) { # обработка свойств
			foreach($tov->ЗначенияСвойств->ЗначенияСвойства as $re) { $zn = $znid = NULL;
				$zn = (string)$re->Значение;
				$znid = (string)$re->Ид;
				if (strtolower($svoistva[$zn]['type']) == 'Применямость' || strtolower($svoistva[$znid]['type']) == 'Применямость') (string)$var1 = ($svoistva[$zn]['name'] ? $svoistva[$zn]['name'] : ($svoistva[$znid]['name'] ? $svoistva[$znid]['name'] : $zn));
				if (strtolower($svoistva[$zn]['type']) == 'Аналог' || strtolower($svoistva[$znid]['type']) == 'Аналог') (string)$analogi = ($svoistva[$zn]['name'] ? $svoistva[$zn]['name'] : ($svoistva[$znid]['name'] ? $svoistva[$znid]['name'] : $zn));

				$valueSvoistvo = trim(($svoistva[$zn]['name'] ? $svoistva[$zn]['name'] : ($svoistva[$znid]['name'] ? $svoistva[$znid]['name'] : $zn)));

				// если это число, то приводит его к формату js
				if(!preg_match_all('/[^0-9,\. ]/', $valueSvoistvo)) {
					$valueSvoistvo = str_replace([',', ' '], ['.', ''], $valueSvoistvo);
				}

				if ($svvo[$znid]) $tovar[$code][$svvo[$znid]] = $valueSvoistvo;

				$tovar[$code]['params'] = $tovar[$code]['params'].= trim($znid)."||".trim($valueSvoistvo)."|\r\n";
				//if (md5($re->Значение)==md5($svstvoArr[$zn]['ids'])) $tags = $svstvoArr[$zn]['val'];
				
				// игнор товара по свойству из expsetting.ini
				if ($ignoreitem && $ignoreitem[$znid]==$zn) $ignoreitm =1;
			}
		}
		
		
		// пропускаем товар, если есть флаг игнора
		if ($ignoreitm) {
			//echo "<p>{$code}</p>";
			unset($tovar[$code]);
			continue;
		}
		
		if ($tov->ХарактеристикиТовара->ХарактеристикаТовара) { # обработка характеристик
			foreach($tov->ХарактеристикиТовара->ХарактеристикаТовара as $re) { $zn = $znid = $znname = NULL;
				$zn = (string)$re->Значение;
				$znid = (string)$re->Ид;
				$znname = (string)$re->Наименование;
				$paramsList[$znid] = ["keyword" => $znid, "name" => $znname, "priority" => count($paramsList) + 1, 'checked' => 1];
				
				// если это число, то приводит его к формату js
				if(!preg_match_all('/[^0-9,\. ]/', $zn)) {
					$zn = str_replace([',', ' '], ['.', ''], $zn);
				}
				$tovar[$code]['params'] = $tovar[$code]['params'].= trim($znid)."||".trim($zn)."|\r\n";

			}
		
		}

		if ($tov->Картинка) { # массив картинок товара
			foreach($tov->Картинка as $pic) {
				if (trim($pic)) $photo[] = (string)(stristr($pic,"import_files") ? trim($pic) : "import_files/".trim($pic));
			}
		}

		if (!$photo && (@file_exists($path1c."import_files/".$code.".jpeg") || @file_exists($path1c."import_files/".$code.".jpg") || @file_exists($path1c."import_files/".$code.".JPG"))) { # есть ли файл картинки в папке
			$photo[] = (string)(@file_exists($path1c."import_files/".$code.".jpeg") ? "import_files/".$code.".jpeg" : (@file_exists($path1c."import_files/".$code.".JPG") ? "import_files/".$code.".JPG" : "import_files/".$code.".jpg"));
		}

		$text = str_replace(PHP_EOL.PHP_EOL,"<p>",$text);
		$text = str_replace(PHP_EOL,"<br>",$text);
		if ($expSet['vesvariable']) $variablename = $ves;

		if (trim($name) && $code) { # создаем массив товаров
			if ($tov->ПометкаУдаления=='true' || $tov->Статус=='true' || $tov['Статус']=='Удален') $tovar["$code"]['delete'] = 1;
			$tovar["$code"]['code'] = $code;
			$tovar["$code"]['kod'] = $kod;
			$tovar["$code"]['artnull'] = preg_replace('/[^a-zA-Zа-яёЁА-Я0-9]/ui', '',($art ? $art : ($code ? $code : $kod)));
			$tovar["$code"]['Keyword'] = encodestring(trim($name)." ".trim(($art2 ? $art2 : $art)),1);
			$tovar["$code"]['art'] = trim($art);
			if (!$tovar["$code"]['name']) $tovar["$code"]['name'] = trim($name);
			$tovar["$code"]['size'] = trim($size);
			if ($text) $tovar["$code"]['text'] = $text;
			if ($edizm) $tovar["$code"]['edizm'] = $edizm;
			$tovar["$code"]['id1c'] = $idgrup;
			if ($ves) $tovar["$code"]['ves'] = $ves;
			if ($analogi) $tovar["$code"]['analogi'] = $analogi;
			if ($photo) $tovar["$code"]['photo'] = $photo;
			if ($vendor) $tovar["$code"]['vendor'] = addslashes(trim($vendor));
			if ($colors) $tovar["$code"]['colors'] = trim($colors);
			if ($tags) $tovar["$code"]['tags'] = trim($tags);
			if ($var1) $tovar["$code"]['var1'] = trim($var1);
			if ($var2) $tovar["$code"]['var2'] = trim($var2);
			if ($akcia>0) $tovar["$code"]['akcia'] = 1;
			if ($novinka>0) $tovar["$code"]['novinka'] = 1;
			#if ($userLabel) $tovar["$code"]['itemlabel'] = $label;
			if ($variablename) $tovar["$code"]['variablename'] = trim($variablename);
		}
	}
	echo "-4-\n";
	flush();
	ob_flush();
	$xml = $tov = '';


	# ############ обработка файла offers.xml - цена, валюта, колво #################
	if (file_exists($ROOTDIR.$pathInc."/1C{$v1c}/offers.xml")) {
		if ($pathInc) $url2 = file_get_contents($ROOTDIR.$pathInc."/1C{$v1c}/offers.xml",0);
		$xml2 = new SimplexmlElement($url2);
		$url2 = '';
		
		if ($xml2->ИзмененияПакетаПредложений) $mainOffers = $xml2->ИзмененияПакетаПредложений;
		if ($xml2->Классификатор) $mainOffers = $xml2->Классификатор;
		if ($xml2->ПакетПредложений) $mainOffers = $xml2->ПакетПредложений;

		if (count($mainOffers->ТипыЦен->ТипЦены)>0) $arrPricesType = $mainOffers->ТипыЦен->ТипЦены;
		if (count($mainOffers->ТипыЦен->ТипЦены)>0) $arrPricesType = $mainOffers->ТипыЦен->ТипЦены;
		if($arrPricesType) {
			$cenii = '0';
			foreach($arrPricesType as $cen) {
				$idcen = (string)$cen->Ид;
				if (!$firstPrice) $firstPrice = $idcen;
				if ($fprice[$cenii]) $typePrice[$idcen] = $fprice[$cenii];
				if ($fprice[$idcen]) $typePrice[$idcen] = $fprice[$idcen];
				$cenii++;

			}

			// если нет определены GET[fprice] брать первую группу цен
			if (count($typePrice)>0) {} else {
				$typePrice[$firstPrice] = 'price';
			}
		} else {
			$typePrice = $fprice;
		}
		echo print_r($typePrice,1);
		
		
		// склады
		if (count($mainOffers->Склады->Склад)>0) $arrStockType = $mainOffers->Склады->Склад;
		if($arrStockType) {
			$stkii = '0';
			foreach($arrStockType as $stk) {
				$idstk = (string)$stk->Ид;
				echo $idstk."<br>";
				if (!$firstStock) $firstStock = $idstk;
				if ($fstock[$stkii]) $typeStock[$idstk] = $fstock[$stkii];
				if ($fstock[$idstk]) $typeStock[$idstk] = $fstock[$idstk];
				$stkii++;
			}

			// если нет определены GET[fstock] брать первый склад
			if (count($typeStock)>0) {} else {
				$typeStock[$firstStock] = 'stock';
			}
		} else {
			$typeStock = $fstock;
		}
		echo '<p>Склады: '.print_r($typeStock,1).'</p>';
		

		if($mainOffers->Предложения->Предложение) {
			foreach($mainOffers->Предложения->Предложение as $tov) {
				unset($name); unset($idcen); 
				$code = (string)$tov->Ид;
				/*if (!$tovar["$code"]['name']) {
					$name = (string)strip_tags(preg_replace("/\s{2,}/", " ", $tov->Наименование));
					if (trim($name)) $tovar["$code"]['name'] = trim($name);
				}*/

				$priceNum = 0;
				if (count($tov->Цены->Цена)>0) {
					foreach($tov->Цены->Цена as $cena) {
						//if ((!$pricevar && $priceNum=='0') || $pricevar[$priceNum]=='price') $priceField = "price"; else $priceField = $pricevar[$priceNum];
						$idcn = (string)$cena->ИдТипаЦены;
						if ($typePrice[$idcn]) {
							$priceField = $typePrice[$idcn];
						} elseif (!$arrPricesType) {
							$priceField = "price";
						}

						if ($priceField) {
							if ($cena->ЦенаЗаЕдиницу) $tovar["$code"][$priceField] = ($noprice ? "0": preg_replace("([^0-9,\.])","",str_replace(",",".",$cena->ЦенаЗаЕдиницу)));
								settype($tovar["$code"][$priceField], "double");
							if (!$tovar["$code"]['currency'] && $cena->Валюта) {
								$tovar["$code"]['currency'] = $currencyArray[(string)$cena->Валюта];
							}
							if ((!$tovar["$code"]['stock'] && !$arrStockType) || $onestock) $tovar["$code"]['stock'] = ($tov->Количество ? $tov->Количество : 0);
								settype($tovar["$code"]['stock'], "integer");
							$priceNum++; unset($priceField);
						}
					}
				}
				
				if (count($tov->Склад)>0 && !$onestock) { 
					foreach($tov->Склад as $ostatok) {
						$idost = (string)$ostatok['ИдСклада'];
						if ($typeStock[$idost]) {
							$stockField = $typeStock[$idost];
						} elseif (!$arrStockType) {
							$stockField = "stock";
						}

						if ($stockField) {
							if ($ostatok['КоличествоНаСкладе']) $tovar["$code"][$stockField] = ($nostock ? "0": $ostatok['КоличествоНаСкладе']);
							settype($tovar["$code"][$stockField], "integer");
						}
					}
				}
				if (count($tov->Остатки)>0) {
					if (!$tovar["$code"]['stock']) $tovar["$code"]['stock'] = ($tov->Остатки->Остаток->Количество ? $tov->Остатки->Остаток->Количество : 0);
				}


			}
		}
	}

	echo "-15-\n";
	flush();
	ob_flush();
	$xml2 = $tov = $mainOffers = '';



	# debug array tovar
	if ($debug==1) {
		if ($pathInc && !$nolog) file_put_contents($ROOTDIR.$pathInc.'/1C'.$v1c.'/log1c.log',  print_r($svoistva, true).print_r($tovar, true), FILE_APPEND);
		exit;
	}

	# выключим разделы каталога и товары в них
	if ($tovar && count($tovar)>0 && !$updatecatalog) {
		$cat1C = $db->get_results("select SQL_NO_CACHE Subdivision_ID from Subdivision where v1c = '".$v1c."' AND code1C != '' AND Catalogue_ID = '".$catalogue."'", ARRAY_A);
		if ($cat1C) {
			foreach($cat1C as $c1) {
				$db->query("UPDATE Message$classNum set Checked = '0', Subdivision_IDS = '', stock = '0', stock2 = '0', stock3 = '0', stock4 = '0', price = '', price2 = '', price3 = '', price4 = '' where Subdivision_ID = '".$c1['Subdivision_ID']."'");
				if (!$nolog) file_put_contents($ROOTDIR.$pathInc.'/1C'.$v1c.'/log1c.log', "\nВыключение товаров раздела ".$c1['Subdivision_ID']."\n", FILE_APPEND);
			}
			$db->query("UPDATE Message$classNum set Checked = '0', Subdivision_IDS = '', stock = '0', stock2 = '0', stock3 = '0', stock4 = '0', price = '', price2 = '', price3 = '', price4 = '' where Subdivision_ID = '".$idsub."'");
			//$db->query("update Subdivision set Checked = '0' where v1c = '".$v1c."' AND code1C != '' AND Catalogue_ID = '".$catalogue."'");
		}
		echo "-t-\n";
		flush();
		ob_flush();
	}
	
	
	# запись характеристик в настройки
	if ($paramsList) {
		//echo print_r(array_values($paramsList),1);
		//die();
		$setting['lists_params'] = array_values($paramsList);
		setSettings($setting);
		echo "-h-\n";
		flush();
		ob_flush();
	}

	# все товары на сайте массив
	$itemsInDB = $db->get_results("select SQL_NO_CACHE code,Message_ID,Keyword,Checked,Subdivision_IDS from Message2001 where Catalogue_ID = '".$catalogue."' AND code != '' AND code IS NOT NULL");
	if ($itemsInDB) {
		foreach($itemsInDB as $itemInDB){
			if ($itemInDB->code) $isObjDB[$itemInDB->code] = $itemInDB;
			if ($itemInDB->Keyword) $isObjDB2[$itemInDB->Keyword] = $itemInDB;
		}
	}

	# ############ обработка массива товаров #################
	echo "==".count($tovar)."==";
	// echo "<pre>";
	foreach($tovar as $id => $item) {
		
		if (zapretName($item['name'])) { $reslt.="\nТовар пропущен - нет на складе: ".$id."\n\n"; continue; }
		
		if ($expSet['onlystock'] && (($item['stock'] + $item['stock2'] + $item['stock3'] + $item['stock4'])<1)) { $reslt.="\nТовар пропущен - нет на складе: ".$id."\n\n"; continue; }
		if ($expSet['onlyprice'] && ($item[price]=='' || $item[price]==0 || !$item[price])) { $reslt.="\nТовар пропущен - нет цены: ".$id."\n\n"; continue; }
		if ($item['delete']) { $reslt.="\nТовар удален: ".$id."\n\n"; $db->query("update Message{$classNum} set Checked = 0 where code = '{$id}' AND Catalogue_ID = '{$catalogue}'"); continue;}

		$isObj = $arrSubIn = $updPhotoArr = '';
		$razdel = [];
		// получить раздел и компонент
		if ($item['id1c']) {
			$razdel = $db->get_row("select a.Subdivision_ID as sub, b.Sub_Class_ID as cc from Subdivision as a, Sub_Class as b where
					a.Subdivision_ID = b.Subdivision_ID AND a.code1C = '".$item['id1c']."' AND b.Class_ID = '".$classNum."' AND a.Catalogue_ID = '{$catalogue}' LIMIT 0,1",ARRAY_A);
		} else if ($item['name'] && $item['Keyword']) {
			$razdel['sub'] = $idsub;
			$razdel['cc'] = $idcc;
		} else {
			$reslt.= "\nТовар без раздела: ".$id." \n\n";
			echo "? ";
			if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
			continue;
		}
		// var_dump($razdel);
		// добавить товар если все ок
		$reslt.="\nsub: {$razdel[sub]} cc:{$razdel[cc]} name:{$item['name']}  keyword:{$item['Keyword']} id1c: {$id}\n";
		if ($razdel[sub] && $razdel[cc] && $item['name'] && $item['Keyword']) { // ЗАКАЧКА ТОВАРОВ
			/*if ($pricevar) {
				$pvi=0;
				foreach($pricevar as $pv) {
					if ($pv!='price') {
						$forUpd = "{$pv} = '".$item[$pv]."',";
						$forInsF = ",{$pv}"; $forInsV = ",'".$item[$pv]."'";
					}
					$pvi++;
				}
			}*/

			//$isObj = $db->get_var("select a.Message_ID from Message$classNum as a, Subdivision as b where a.".$field_connect." = '".$item[$field_connect]."' AND a.Subdivision_ID=b.Subdivision_ID AND b.Catalogue_ID = '{$catalogue}' limit 0,1");
			if (($item['Keyword'] || $id) && $catalogue>0) $isObj = ($isObjDB[$id] ? $isObjDB[$id] : $isObjDB2[$item['Keyword']]);

			if ($isObj->Message_ID || $isObj->Keyword) { // если товар есть
				if ($isObj->Checked!=1 || ($updatecatalog && $isObj->Checked==1)) { // товар встретился в первый раз в прайсе
					$sql = "UPDATE Message$classNum set 
								Subdivision_ID = '".$razdel[sub]."',
								Sub_Class_ID = '".$razdel[cc]."',
								Checked = '".($item['delete'] ? "0" : "1")."',
								name = '".$item['name']."',
								id1c = '".$item['id1c']."',
								price = '".$item['price']."', ".$forUpd."
								price2 = '".$item['price2']."',
								price3 = '".$item['price3']."',
								price4 = '".$item['price4']."',
								currency = '".$item['currency']."',
								width = '".$item['width']."',
								height = '".$item['height']."',
								".(is_numeric($item['itemlabel']) ? "itemlabel = '".$item['itemlabel']."'," : NULL)."
								".($useAkcia ? "action = '".($item['akcia'] ? "1" : "0")."'," : NULL)."
								".($useNovinka ? "new = '".($item['novinka'] ? "1" : "0")."'," : NULL)."
								".($useSpec ? "spec = '".($item['spec'] ? "1" : "0")."'," : NULL)."
								".($useNotmarkup ? "notmarkup = '".($item['notmarkup'] ? "1" : "0")."'," : NULL)."
								".($useTorg ? "torg = '".($item['torg'] ? "1" : "0")."'," : NULL)."
								".($useTimer ? "timer = '".($item['timer'] ? "1" : "0")."'," : NULL)."
								".($useNocart ? "nocart = '".($item['nocart'] ? "1" : "0")."'," : NULL)."
								".($item['ncTitle'] ? "ncTitle = '".$item['ncTitle']."'," : NULL)."
								".($item['ncDescription'] ? "ncDescription = '".$item['ncDescription']."'," : NULL)."
								".($item['ncKeywords'] ? "ncKeywords = '".$item['ncKeywords']."'," : NULL)."
								".($item['descr'] ? "descr = '".$item['descr']."'," : NULL)."
								".($item['text'] ? "text = '".$item['text']."'," : NULL)."
								".($item['text2'] ? "text2 = '".$item['text2']."'," : NULL)."
								".($item['edizm'] ? "edizm = '".$item['edizm']."'," : NULL)."
								vendor = '".$item['vendor']."',
								art = '".$item['art']."', ".($_GET[apdurl] ? "Keyword = '".$item['Keyword']."'," : "")."
								".($item['analogi'] ? "analog = '".$item['analogi']."'," : "").
								($item['buywith'] ? "buywith = '".$item['buywith']."'," : "").
								($item['kod'] ? "art2 = '".$item['kod']."'," : "").
								($item['artnull'] ? "artnull = '".$item['artnull']."'," : "").
								($item['size'] ? "size = '".$item['size']."'," : "").
								($item['colors'] ? "colors = '".$item['colors']."'," : "").
								($item['ves'] ? "ves = '".$item['ves']."'," : "").
								($item['variablename'] ? "variablename = '".$item['variablename']."'," : "").
								($item['discont'] ? "discont = '".$item['discont']."'," : "").
								($item['disconttime'] ? "disconttime = '".datetomysql($item['disconttime'])."'," : "").
								($item['var1'] ? "var1 = '".$item['var1']."'," : "").
								($item['var2'] ? "var2 = '".$item['var2']."'," : "").
								($item['var3'] ? "var3 = '".$item['var3']."'," : "").
								($item['var4'] ? "var4 = '".$item['var4']."'," : "").
								($item['var5'] ? "var5 = '".$item['var5']."'," : "").
								($item['var6'] ? "var6 = '".$item['var6']."'," : "").
								($item['var7'] ? "var7 = '".$item['var7']."'," : "").
								($item['var8'] ? "var8 = '".$item['var8']."'," : "").
								($item['var9'] ? "var9 = '".$item['var9']."'," : "").
								($item['var10'] ? "var10 = '".$item['var10']."'," : "").
								($item['var11'] ? "var11 = '".$item['var11']."'," : "").
								($item['var12'] ? "var12 = '".$item['var12']."'," : "").
								($item['var13'] ? "var13 = '".$item['var13']."'," : "").
								($item['var14'] ? "var14 = '".$item['var14']."'," : "").
								($item['var15'] ? "var15 = '".$item['var15']."'," : "").
								($item['params'] ? "params = '".$item['params']."'," : "").
								($item['tags'] ? "tags = '".$item['tags']."'," : "")."
								stock = '".($item['stock']>0 ? $item['stock'] : "")."',
								stock2 = '".($item['stock2']>0 ? $item['stock2'] : "")."',
								stock3 = '".($item['stock3']>0 ? $item['stock3'] : "")."',
								stock4 = '".($item['stock4']>0 ? $item['stock4'] : "")."'
						where ".($isObj->Keyword ? "Keyword = '".$isObj->Keyword."'" : "Message_ID = '".$isObj->Message_ID."'")." AND Catalogue_ID = '{$catalogue}'";
				} else { // товар  встретился уже не в первый раз и уже включен был ранее. и не режим обновления
					$arrSubIn = explode(",",$isObj->Subdivision_IDS);
					$arrSubIn[] = $razdel[sub];
					$arrSubIn = array_unique($arrSubIn);
					$sql = "UPDATE Message$classNum set name = '".$item['name']."', 
						Subdivision_IDS = '".implode(",",array_diff($arrSubIn, array('')))."'
						where ".($isObj->Keyword ? "Keyword = '".$isObj->Keyword."'" : "Message_ID = '".$isObj->Message_ID."'")." AND Catalogue_ID = '{$catalogue}'";
				}
				$db->query($sql);

                # групировка товара
                variableItems(array('id' => $isObj->Message_ID, 'name' => $item['name'], 'sub' => $razdel['sub']), "import");

			} else { // если товара нет
				$sql = "INSERT INTO Message$classNum (
					Created,
					User_ID,
					Catalogue_ID,
					Subdivision_ID,
					Sub_Class_ID,
					Checked,
					Keyword,
					name,
					text,
					id1c,
					code,
					vendor,
					art,
					price,
					action,
					new,
					stock,
					stock2,
					stock3,
					stock4".
					($item['analogi'] ? ",analog" : "").
					($item['price2'] ? ",price2" : "").
					($item['price3'] ? ",price3" : "").
					($item['price4'] ? ",price4" : "").
					(is_numeric($item['itemlabel']) ? ",itemlabel" : "").
					($item['ves'] ? ",ves" : "").
					($item['currency'] ? ",currency" : "").
					($item['edizm'] ? ",edizm" : "").
					($item['tags'] ? ",tags" : "").
					($item['kod'] ? ",art2" : "").
					($item['artnull'] ? ",artnull" : "").
					($item['size'] ? ",size" : "").
					($item['colors'] ? ",colors" : "").
					($item['variablename'] ? ",variablename" : "").
					($item['discont'] ? ",discont" : "").
					($item['disconttime'] ? ",disconttime" : "").
					($item['var1'] ? ",var1" : "").
					($item['var2'] ? ",var2" : "").
					($item['var3'] ? ",var3" : "").
					($item['var4'] ? ",var4" : "").
					($item['var5'] ? ",var5" : "").
					($item['var6'] ? ",var6" : "").
					($item['var7'] ? ",var7" : "").
					($item['var8'] ? ",var8" : "").
					($item['var9'] ? ",var9" : "").
					($item['var10'] ? ",var10" : "").
					($item['var11'] ? ",var11" : "").
					($item['var12'] ? ",var12" : "").
					($item['var13'] ? ",var13" : "").
					($item['var14'] ? ",var14" : "").
					($item['var15'] ? ",var15" : "").
					($item['params'] ? ",params" : "").
					$forInsF.")
				VALUES
					(NOW(),1,'".$catalogue."','".$razdel[sub]."','".$razdel[cc]."','".($item['delete'] ? "0" : "1")."','".$item['Keyword']."',
					'".$item['name']."','".$item['text']."','".$item['id1c']."','".$id."','".$item['vendor']."','".$item['art']."','".$item['price']."',
                    '".($item['akcia'] ? '1' : '0')."',
                    '".($item['novinka'] ? '1' : '0')."',
					'".($item['stock']>0 ? $item['stock'] : "")."',
					'".($item['stock2']>0 ? $item['stock2'] : "")."',
					'".($item['stock3']>0 ? $item['stock3'] : "")."',
					'".($item['stock4']>0 ? $item['stock4'] : "")."'
					".($item['analogi'] ? ",'".$item['analogi']."'" : "").
					($item['price2'] ? ",'".$item['price2']."'" : "").
					($item['price3'] ? ",'".$item['price3']."'" : "").
					($item['price4'] ? ",'".$item['price4']."'" : "").
					(is_numeric($item['itemlabel']) ? ",'".$item['itemlabel']."'" : "").
					($item['ves'] ? ",'".$item['ves']."'" : "").
					($item['currency'] ? ",'".$item['currency']."'" : "").
					($item['edizm'] ? ",'".$item['edizm']."'" : "").
					($item['tags'] ? ",'".$item['tags']."'" : "").
					($item['kod'] ? ",'".$item['kod']."'" : "").
					($item['artnull'] ? ",'".$item['artnull']."'" : "").
					($item['size'] ? ",'".$item['size']."'" : "").
					($item['colors'] ? ",'".$item['colors']."'" : "").
					($item['variablename'] ? ",'".$item['variablename']."'" : "").
					($item['discont'] ? ",'".$item['discont']."'" : "").
					($item['disconttime'] ? ",'".datetomysql($item['disconttime'])."'" : "").
					($item['var1'] ? ",'".$item['var1']."'" : "").
					($item['var2'] ? ",'".$item['var2']."'" : "").
					($item['var3'] ? ",'".$item['var3']."'" : "").
					($item['var4'] ? ",'".$item['var4']."'" : "").
					($item['var5'] ? ",'".$item['var5']."'" : "").
					($item['var6'] ? ",'".$item['var6']."'" : "").
					($item['var7'] ? ",'".$item['var7']."'" : "").
					($item['var8'] ? ",'".$item['var8']."'" : "").
					($item['var9'] ? ",'".$item['var9']."'" : "").
					($item['var10'] ? ",'".$item['var10']."'" : "").
					($item['var11'] ? ",'".$item['var11']."'" : "").
					($item['var12'] ? ",'".$item['var12']."'" : "").
					($item['var13'] ? ",'".$item['var13']."'" : "").
					($item['var14'] ? ",'".$item['var14']."'" : "").
					($item['var15'] ? ",'".$item['var15']."'" : "").
					($item['params'] ? ",'".$item['params']."'" : "").
					" ".$forInsV."
				)";
				if ($item['delete']!='true') {
					$db->query($sql);

				    //$isObj = $db->get_var("select a.Message_ID from Message$classNum as a, Subdivision as b where a.".$field_connect." = '".$item[$field_connect]."' AND a.Subdivision_ID=b.Subdivision_ID AND b.Catalogue_ID = '{$catalogue}' limit 0,1");
					if ($item['Keyword'] && $catalogue>0) $isObj = $db->get_row("select Message_ID, Keyword from Message$classNum where Keyword = '".$item['Keyword']."' AND Catalogue_ID = '{$catalogue}' limit 0,1");

                    # групировка товара
                	if($isObj->Message_ID){
                        variableItems(array('id' => $isObj->Message_ID, 'sub' => $razdel['sub'], 'name' => $item['name']), "import");
                    }
				}
			}

			# фотки
			if ($isObj->Message_ID && $item['photo']) {
				loadphoto($isObj->Message_ID, $item['photo'],($item['art'] ? $item['art']." " : NULL).$item['name']);
			}
			// если фотки удалили в 1С, обновить не зависимо от того была ли фотка у товара.
			$db->query("UPDATE
							Multifield 
						SET 
							Field_ID = '2353000' 
						WHERE
							file1Ctime IS NOT NULL 
							AND file1Ctime != '' 
							AND file1Ctime != '{$file1Ctime}' 
							AND Message_ID = '{$isObj->Message_ID}'
							AND Path LIKE '%{$path1cPhoto}%'
							AND Field_ID = '2353'");
			// end фотки

			$aa = $sqlcount/1000;
			$bb = intval($aa);
			echo ($aa==$bb ? $sqlcount : ".")." ";
			flush();
			ob_flush();
			$reslt.="\nТовар: ".$isObj->Message_ID." (".$id.")\n\n";
			$reslt .= "\n\n$sql<br>\n\n";

			$tovii++;
			if ($tovii==50) { if (!$nolog) { file_put_contents($logPath, $reslt, FILE_APPEND); } unset($reslt); $tovii=0; }

			$sqlcount++;
		} else {
			$reslt = "\nТОВАР НЕ ЗАКАЧАН sub - {$razdel[sub]}, cc-{$razdel[cc]}, name: {$item['name']}, key: {$item['Keyword']}\n";
			if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
		}

	}
	$reslt .= "".date("d.m.Y H:i:s")." номенклатура обновлена. Позиций: $sqlcount";
	if ($pathInc && !$nolog) file_put_contents($logPath, $reslt, FILE_APPEND);

	$db->query("update Catalogue set file1Ctime".$v1c." = '$file1Ctime' where Catalogue_ID = '".$catalogue."'");
	
	// отключение разделов, если в них нет товаров
	if ($expSet['hideEmptySub']) unCheckEmptySub();
	
	
	clearCache('','', '', $catalogue);
	
	echo "Свершилось. Номенклатура обновлена. Всего обновлено и добавлено позиций в каталог: $sqlcount";
	if ($expSet['email']){
        $frommail = "info@".str_replace("www.","",$_SERVER[HTTP_HOST]);
		$mailer = new CMIMEMail();
		$mailer->setCharset('utf-8');
	    $mailer->mailbody('Выгрузка 1С успешно обработана на сайте');
	    $mailer->send($expSet['email'], $frommail, $frommail, "Отчет выгрузки товаров из 1С", $current_catalogue['Catalogue_Name']);
	}
	unset($xml); unset($xml2); unset($reslt); unset($tovar); unset($logPath); unset($isObjDB); unset($isObjDB2); unset($itemsInDB);
} // end offers



# ############ функция обновления фото
function loadphoto($messID, $photoArr, $messName='') {
	global $ROOTDIR, $setting, $db, $catalogue, $file1Ctime, $waterFile, $pathInc, $path1c2, $path1c, $path1cPhoto, $v1c, $waterPosition;

	if (!$messID || count($photoArr)==0) return;

	// массив с существующими фотками товара
	$curPhotoArr = $db->get_results("select SQL_NO_CACHE `ID`, `Path`, `SizeOrig`, `file1Ctime`, `Priority` from Multifield where Field_ID = 2353 AND Message_ID = '{$messID}' AND Path LIKE '%{$pathInc}%'",ARRAY_A);
	if ($curPhotoArr) {
		foreach($curPhotoArr as $p) {
			if ($p['Priority']>$priorPhoto) $priorPhoto = $p['Priority'];
			$curPhoto[$p['Path']]['ID'] = $p['ID'];
			$curPhoto[$p['Path']]['SizeOrig'] = $p['SizeOrig'];
			$curPhoto[$p['Path']]['file1Ctime'] = $p['file1Ctime'];
		}
	}
	if ($messID && $path1c2) $db->query("delete from Multifield where Field_ID = 2353 AND Message_ID = '{$messID}' AND Path like '%{$path1c2}%'");

	$priorPhoto = 10;

	foreach($photoArr as $photo) { // цикл фото товара
		$photoPart = array_reverse(explode("/",str_replace("\\","/",$photo)));

		$photoPathOrig = $path1c2.str_replace("\\","/",$photo);
		if (!file_exists($ROOTDIR.$photoPathOrig)) { //если нет файла проверить без 'import_files/'
			$changePath = str_replace('import_files/', '', $photoPathOrig);
			if (file_exists($ROOTDIR.$changePath)) {
				$photoPathOrig = $changePath;
			}
		}
		$jpgz = array(".JPG"=>".jpg",".JPEG"=>".jpg",".jpeg"=>".jpg",".png"=>".jpg",".PNG"=>".jpg",".GIF"=>".jpg",".gif"=>".jpg");
		$photoPathOrig2 = strtr($photoPathOrig,$jpgz);

		$photoSize = @getimagesize($ROOTDIR.$photoPathOrig);
		$photoFileSize = @filesize($ROOTDIR.$photoPathOrig);
		$photoPathNew = $path1cPhoto.$photoPart[0];
		// echo $ROOTDIR.$photoPathOrig.'<br/>';

		if (!file_exists($ROOTDIR.$photoPathNew) || !$curPhoto[$photoPathNew]['ID'] || ($curPhoto[$photoPathNew]['ID'] && ($curPhoto[$photoPathNew]['file1Ctime']!=$file1Ctime || $curPhoto[$photoPathNew]['SizeOrig']!=$photoFileSize))) { // создать файл
			if ($photoSize[0]>800){
				@\nc_ImageTransform::imgResize($ROOTDIR.$photoPathOrig, $ROOTDIR.$photoPathNew,800,800, 0, "", 90);
			} else {
				@copy($ROOTDIR.$photoPathOrig, $ROOTDIR.$photoPathNew);
			}
			if (file_exists($ROOTDIR.$photoPathNew) && $waterFile && $photoSize[0]>=400) \nc_ImageTransform::putWatermark_file($ROOTDIR.$photoPathNew, $waterFile, $waterPosition);
		}
		if (file_exists($ROOTDIR.$photoPathNew)) {
			if (!$curPhoto[$photoPathNew]['ID']) { // добавить фото в БД
				$db->query("insert into Multifield (Field_ID,Message_ID,Priority,Name,Size,Path,Preview,SizeOrig,file1Ctime) VALUES (2353,'".$messID."','".($priorPhoto)."','".$messName."','2','{$photoPathNew}','{$photoPathNew}','".$photoFileSize."','{$file1Ctime}')");
				echo "* ";
				flush();
				ob_flush();
			} else {  // изменить фото в БД
				$db->query("update Multifield SET Field_ID = '2353', Path = '{$photoPathNew}', Preview = '{$photoPathNew}', SizeOrig = '".$photoFileSize."', file1Ctime = '{$file1Ctime}' where Field_ID = '2353' AND ID = '".$curPhoto[$photoPathNew]['ID']."' AND Message_ID = '{$messID}'");
				echo "- ";
				flush();
				ob_flush();
			}
		}


		$priorPhoto++;
	} // foreach
	// $db->query("update Multifield SET Field_ID = '2353000' where file1Ctime IS NOT NULL AND file1Ctime != '' AND file1Ctime != '{$file1Ctime}' AND Message_ID = '{$messID}' AND Field_ID = '2353'");
	echo " ";
	flush();
	ob_flush();
}




# ############ функция добавления разделов #####################
function podrazd($gr,$idroditel,$v1c='',$sv='',$cat='') {
  GLOBAL $priority, $db, $rootsub, $classNum, $classNumTemp, $catalogue, $RecordsPerPage, $logPath, $updatecatalog, $stopGroupPath, $ignoreGroupPath, $expSet;

  # STOP и IGNORE группы
  if (file_exists($stopGroupPath)) $stopGroup = explode("\r\n",file_get_contents($stopGroupPath));
  if (file_exists($ignoreGroupPath)) $ignoreGroup = explode("\r\n",file_get_contents($ignoreGroupPath));
  

  $grArray = (!$sv ? (!$cat ? $gr->Группа : $gr->Категория) : $gr->Справочник);
  //$grArray22 = $gr->Группа;

  echo "-! ".count($gr->Группа)." !-\n";
  
  if ($grArray) {

	  foreach($grArray as $gr) { unset($grID); unset($grName);
			$grID = (!$sv ? $gr->Ид : $gr->ИдЗначения);
			$grName = (!$sv ? $gr->Наименование : $gr->Значение);
			$grName = str_replace("_"," ",$grName);
			//$grName = trim(preg_replace("/ +/", " ", $grName));

			$idsub++;
			$priority++;
			if ($grID && !$grName) $grName = $grID;
			//if (!$grName) continue;
			
			
			
			if (zapretName($grName)) $ignoreGroup[] = $grID;
			if (in_array($grID,$stopGroup)) continue;
			if (in_array($grID,$ignoreGroup)) {
				$inCat = $idroditel;
			}
			
			
			
			if (encodestring($grName,1)=="" || is_numeric(encodestring($grName,1))) continue;

			if ($catalogue && $idroditel && !in_array($grID,$ignoreGroup)) {
				if (!$nolog) file_put_contents($logPath, "!!!!!!!!!!!!!!!!!!!!!!!!\n", FILE_APPEND);

				if ($grID) {
					$inCatArr = $db->get_row("select SQL_NO_CACHE Subdivision_ID, Hidden_URL from Subdivision where code1C = '".$grID."' AND Catalogue_ID = '{$catalogue}'",ARRAY_A);
					$inCat = $inCatArr['Subdivision_ID'];
				} else {
					return "\nНет ID раздела для проверки\n";
				}

				// ссылка на новый раздел
				$Hidden_URL = $db->get_var("select SQL_NO_CACHE Hidden_URL from Subdivision where Subdivision_ID = '".$idroditel."'").encodestring($grName,1)."/";

				if ($inCat) { // если раздел уже есть
					$reslts.= "Есть $inCat\n";

					// если поменялась структура, сменить путь
					if ($Hidden_URL && $inCatArr['Hidden_URL'] && md5($Hidden_URL)!=md5($inCatArr['Hidden_URL']) && !$expSet['nouphiddenurl']) {
						$hiddenurls = $db->get_results("select SQL_NO_CACHE Hidden_URL as url, Subdivision_ID as sub from Subdivision where Subdivision_ID IN (".substr(getChildSub($inCat), 0, -1).") AND Catalogue_ID = '".$catalogue."'", ARRAY_A);
						if ($hiddenurls) {
							foreach($hiddenurls as $hid) {
								unset($new_hidurl);
								$new_hidurl = str_replace($inCatArr['Hidden_URL'],$Hidden_URL,$hid['url']);
								if ($new_hidurl) $db->query("update Subdivision set Hidden_URL = '{$new_hidurl}' where Subdivision_ID = '{$hid['sub']}' AND Catalogue_ID = '".$catalogue."'");
							}
						}
						$new_hiddenurl = str_replace("","",$hiddenurl); // что это?
					}

					//$reslts.= "update Subdivision set v1c = '$v1c', ".($sv ? "find ='".addslashes($grName)."'," : NULL)." Checked = '1', Subdivision_Name = '".addslashes($grName)."', Hidden_URL = '".$Hidden_URL."', Parent_Sub_ID = '".$idroditel."' where Subdivision_ID = '".$inCat."' AND Catalogue_ID = '".$catalogue."'";

					// включить раздел и поменять название/ссылку/путь (вдруг сменилось)
					$sql1 = "update Subdivision set v1c = '$v1c',
					".($sv ? "find ='".addslashes($grName)."'," : NULL)."
					Checked = '1'
					".(!$expSet['noupdgroupname'] ? ", Subdivision_Name = '".addslashes($grName)."'" : NULL)."
					".(!$expSet['nouphiddenurl'] ? ", Hidden_URL = '".$Hidden_URL."'" : NULL)."
					".(!$expSet['nouphiddenurl'] ? ", EnglishName = '".encodestring($grName,1)."'" : NULL)."
					".(!$expSet['nouphiddenurl'] ? ", Parent_Sub_ID = '".$idroditel."'" : NULL)."
					where Subdivision_ID = '".$inCat."' AND Catalogue_ID = '".$catalogue."'";

					$db->query($sql1);
					// и выключить все товары, актуальные включатся при обработке товаров
					//$db->query("UPDATE Message$classNum set Checked = '0' where Subdivision_ID = '".$inCat."'");
					if (!$nolog) file_put_contents($logPath, "обновлен раздел {$inCat}.{$grName} Ид: {$grID} {$sql1}\n", FILE_APPEND);
				} else {
					// добавим раздел
					$db->query("INSERT INTO Subdivision
								  (Catalogue_ID,Parent_Sub_ID,Subdivision_Name,Priority,Checked,EnglishName,Hidden_URL,code1C,subdir, v1c, find) VALUES
								  ({$catalogue},'".$idroditel."','".addslashes($grName)."','".$priority."','1','".encodestring($grName,1)."','".$Hidden_URL."','".$grID."',3,'".$v1c."','".$grFind."')");
					$inCat = $db->get_var("select SQL_NO_CACHE Subdivision_ID from Subdivision where code1C = '".$grID."' AND Catalogue_ID = '{$catalogue}' limit 0,1");
					// добавим инфоблок в раздел
					if ($inCat) {
						$db->query("INSERT INTO Sub_Class (Subdivision_ID,Class_ID,Sub_Class_Name,EnglishName,Checked,Class_Template_ID,Catalogue_ID,DefaultAction,AllowTags,NL2BR,UseCaptcha,CacheForUser,RecordsPerPage) VALUES
						('".$inCat."',{$classNum},'".$grID."','".encodestring($grName,1)."',1,'{$classNumTemp}',{$catalogue},'index','-1','-1','-1','-1','{$RecordsPerPage}')");
					}
					$reslts.= "Создан $inCat\n";
					if (!$nolog) file_put_contents($logPath, "INSERT INTO Subdivision
								  (Catalogue_ID,Parent_Sub_ID,Subdivision_Name,Priority,Checked,EnglishName,Hidden_URL,code1C,subdir, v1c, find) VALUES
								  ({$catalogue},'".$idroditel."','".addslashes($grName)."','".$priority."','1','".encodestring($grName,1)."','".$Hidden_URL."','".$grID."',0,'".$v1c."','".$grFind."') создан раздел {$inCat}.{$grName}\n", FILE_APPEND);
				}
			}
			// рекурсия, если есть группы
			if ($gr->Группы && $inCat>0) podrazd($gr->Группы, $inCat, $v1c, $sv);
			echo ". ";
			flush();
			ob_flush();


	  }
  }
  return $reslts;
}


# ############ отключение пустых групп
function unCheckEmptySub() {
	global $db, $catalogue;
	$allsubr = $db->get_results("select a.Hidden_URL, a.Subdivision_ID from Sub_Class as b, Subdivision as a where a.code1C != '' AND a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 2001 AND a.Catalogue_ID = '".$catalogue."' ORDER BY a.Hidden_URL DESC", ARRAY_A);
	if ($allsubr) {
		foreach($allsubr as $subr) {
			$cnt1 = $db->get_var("SELECT count(Message_ID) as cnt from Message2001 where Checked=1 AND Subdivision_ID = '".$subr['Subdivision_ID']."' AND Catalogue_ID = '".$catalogue."'");
			$cnt2 = $db->get_var("SELECT Subdivision_ID from Subdivision where Checked=1 AND Parent_Sub_ID = '".$subr['Subdivision_ID']."' AND Catalogue_ID = '".$catalogue."' AND Subdivision_Name != 'OLD' limit 0,1");
			echo "\n".$subr['Hidden_URL']." - {$cnt1} {$cnt2}";
			if ($cnt1>0 || $cnt2>0) {
				// ok
				echo " - check!";
				$db->query("update Subdivision SET Checked = 1 where Subdivision_ID = '".$subr['Subdivision_ID']."' AND Catalogue_ID = '".$catalogue."'");
			} else {
				// uncheck
				echo " - hide!";
				$db->query("update Subdivision SET Checked = 0 where Subdivision_ID = '".$subr['Subdivision_ID']."' AND Catalogue_ID = '".$catalogue."'");
			}
			echo " ";
			flush(); ob_flush();
		}
	}
}



# РАЗДЕЛ: список дочерних разделов
function getChildSub($subdiv) {
		global $db, $catalogue;
		$subArr = $db->get_results("select SQL_NO_CACHE Subdivision_ID as sub from Subdivision where Parent_Sub_ID = '$subdiv' AND Catalogue_ID = '{$catalogue}'", ARRAY_A);
		if ($subArr) {
			foreach($subArr as $sd) {
				$sddiv = $sd[sub];
				$reslt .= "{$sddiv},".getChildSub($sddiv);
			}
		}
		return $reslt;
}

function not1bitrix($path1c) {
	$arrFolders = array("import_files/","import.xml","offers.xml");
	foreach($arrFolders as $arrF) {
		echo ".";
		flush();
		ob_flush();
		copy_folder($path1c.'1cbitrix/'.$arrF, $path1c.$arrF, 1, 1);
	}
}


function copy_folder($d1, $d2, $upd = true, $force = true) {
    if ( is_dir( $d1 ) ) {
        $d2 = mkdir_safe( $d2, $force );
        if (!$d2) {fs_log("!!fail $d2"); return;}
        $d = dir( $d1 );
        while ( false !== ( $entry = $d->read() ) ) {
            if ( $entry != '.' && $entry != '..' )
                copy_folder( "$d1/$entry", "$d2/$entry", $upd, $force );
        }
        $d->close();
    }
    else {
        $ok = copy_safe( $d1, $d2, $upd );
        $ok = ($ok) ? "ok-- " : " -- ";
        //fs_log("{$ok}$d1");
    }
} //function copy_folder

function mkdir_safe( $dir, $force ) {
    if (file_exists($dir)) {
        if (is_dir($dir)) return $dir;
        else if (!$force) return false;
        unlink($dir);
    }
    return (mkdir($dir, 0775, true)) ? $dir : false;
} //function mkdir_safe

function copy_safe ($f1, $f2, $upd) {
    $time1 = filemtime($f1);
    if (file_exists($f2)) {
        $time2 = filemtime($f2);
        if ($time2 >= $time1 && $upd) return false;
    }
    $ok = copy($f1, $f2);
    if ($ok) touch($f2, $time1);
    return $ok;
} //function copy_safe

function fs_log($str) {
    $log = fopen("./fs_log.txt", "a");
    $time = date("Y-m-d H:i:s");
}


function datetomysql($dat) {
    $d = date('Y-m-d H:i:s', strtotime($dat));
    return $d;
}


function unzip1($zipfile) {
	global $ROOTDIR, $v1c, $path1c, $pathInc, $nolog, $logPath;
	$zip = new ZipArchive;
	$resZip = $zip->open($ROOTDIR.$pathInc."/1C{$v1c}/{$zipfile}");
	if ($resZip === TRUE) {
		$zip->extractTo($ROOTDIR.$pathInc."/1C{$v1c}/");
		$zip->close();
		if (file_exists($path1c.'1cbitrix/import.xml')) not1bitrix($path1c);
	} else {
		$reslt = 'Не получилось из-за ошибки #'.$resZip;
		//echo $reslt;
		if (!$nolog) file_put_contents($logPath, $reslt, FILE_APPEND);
		unset($reslt); die;
	}

}


function zapretName($name) {
	$zapret = array('Капкан','капкан','рыболовная сеть');

	foreach($zapret as $z) {
		if(mb_stristr($name,$z)) return true;
	}
	return false;
}
