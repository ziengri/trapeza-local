<?php 
ini_set('memory_limit', '400M');

set_time_limit(1000000);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $pathInc, $pathInc2, $catalogue, $current_catalogue, $nc_core, $field_connect, $setting;


if (!$_POST['sites']) die;

$sites1 = explode(",",$_POST['sites']);

$stopPages = array('reviews','otzyvy','partnery','partners','forum','prajs-list','price','news','articles','statii','photogallery','contacts','kontakty','files','documents','info','about','dostavka-i-oplata','dostavka','oplata','o-nas','akcii','spec','akci','new','contact','test1','test2','test3','test4','testovyj-razdel-4','testovyj-razdel-5','testovyj-razdel-6','testovyj-razdel-47','stati','dokumenty','vakansii');

if (count($sites1)>0) {
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/seolog.txt", "");
	foreach($sites1 as $site) {
		$sqlin[] = "'".$site."'";
	}
	$sites = $db->get_results("select Catalogue_ID,Domain,Catalogue_Name,Robots from Catalogue where Domain IN (".implode(",",$sqlin).")", ARRAY_A);
	if ($sites) {
		foreach($sites as $s) {
			$pages = $db->get_results("select a.Subdivision_ID, a.Title, a.Title2, a.Hidden_URL, a.TitleObj, a.Description, a.Description2, a.DescriptionObj, b.Class_ID from Subdivision as a, Sub_Class as b where a.Checked = 1 AND b.Class_ID IN (182,2001) AND a.Subdivision_ID = b.Subdivision_ID AND a.Catalogue_ID = '".$s[Catalogue_ID]."' AND a.EnglishName NOT IN ('".implode("','",$stopPages)."')", ARRAY_A);
			
			/*if (!$ff) {
				echo print_r($pages,1);
				$ff= 1;
			}*/
			$optimize = optimize($pages,$s['Domain']);
			$sitesnew[$s['Domain']]['pages'] = count($pages);
			$sitesnew[$s['Domain']]['optimize'] = $optimize['O'];
			$sitesnew[$s['Domain']]['optimizeT'] = $optimize['U'];
			$sitesnew[$s['Domain']]['robots'] = robots($s['Robots']);
			$sitesnew[$s['Domain']]['v'] = 3;
		}
		if (count($sitesnew)) echo json_encode($sitesnew);
		
	}
}

function rep($zag) {
	$titleArr = array(
		"PARENTNAME-low"=>"название раздела",
		"PARENT2NAME-low"=>"название раздела",
		"PARENTNAME"=>"название раздела",
		"PARENT2NAME"=>"название раздела",
		"PRICE"=>"500",
		"ART"=>"10000",
		"ITEMNAME-low"=>"обычное название товара",
		"ITEMNAME"=>"обычное название товара",
		"VENDOR"=>"производитель",
		"CITYNAME"=>"казань",
		"NOCITY"=>"",
		"NAME-low"=>"название раздела",
		"NAME"=>"название раздела",
		"ITEMNUM"=>1
	);
	return strtr($zag,$titleArr);
}

function optimize($pages, $site) {
	$total = array('O' => 0, 'U' => 0);
	$mintitle = 30;
	$maxtitle = 75;
	$mindescr = 70;
	$maxdescr = 200;
	
	foreach($pages as $page) {
		# Title
		if (!$page['Title'] && !$page['Title2']) {
			logg($site.$page['Hidden_URL'], "no Title & Title2");
		} else {
			if ($page['Title']) {
				if (mb_strlen($page['Title'])<=$maxtitle && mb_strlen($page['Title'])>$mintitle) $total['O']++; else logg($site.$page['Hidden_URL'], "Title govno - primerno ".mb_strlen($page['Title'])." simvolov");
			}
			if ($page['Title2'] && !$page['Title']) {
				if (mb_strlen(rep($page['Title2']))<=$maxtitle && mb_strlen(rep($page['Title2']))>$mintitle) $total['O']++; else logg($site.$page['Hidden_URL'], "Title2 govno - primerno ".mb_strlen(rep($page['Title2']))." simvolov");
			}
		}
		
		# Title Obj
		if ($page['Class_ID']==2001) {
			if (!$page['TitleObj']) {
				logg($site.$page['Hidden_URL'], "no TitleObj (for catalog)");
			} else {
				if (mb_strlen(rep($page['TitleObj']))<=$maxtitle && mb_strlen(rep($page['TitleObj']))>$mintitle) $total['O']++; else logg($site.$page['Hidden_URL'], "TitleObj govno - primerno ".mb_strlen(rep($page['TitleObj']))." simvolov"); 
			}
		}
		
		
		# Description
		if (!$page['Description'] && !$page['Description2']) {
			logg($site.$page['Hidden_URL'], "no Description & Description2");
		} else {
			if ($page['Description']) {
				if (mb_strlen($page['Description'])<=$maxdescr && mb_strlen($page['Description'])>$mindescr) $total['O']++; else logg($site.$page['Hidden_URL'], "Description govno - primerno ".mb_strlen($page['Description'])." simvolov");
			}
			if ($page['Description2'] && !$page['Description']) {
				if (mb_strlen(rep($page['Description2']))<=$maxdescr && mb_strlen(rep($page['Description2']))>$mindescr) $total['O']++; else logg($site.$page['Hidden_URL'], "Description2 govno - primerno ".mb_strlen(rep($page['Description2']))." simvolov");
			}
		}
		
		# Description Obj
		if ($page['Class_ID']==2001) {
			if (!$page['DescriptionObj']) {
				logg($site.$page['Hidden_URL'], "no DescriptionObj (for catalog)");
			} else {
				if (mb_strlen(rep($page['DescriptionObj']))<=$maxdescr && mb_strlen(rep($page['DescriptionObj']))>$mindescr) $total['O']++; else logg($site.$page['Hidden_URL'], "DescriptionObj govno - primerno ".mb_strlen(rep($page['DescriptionObj']))." simvolov"); 
			}
		}

		$total['U'] += 4;
	}
	logSend();
	return $total;
}

function logg($link, $what) {
	global $log;
	$log .= $link." - ".$what."\n";
}

function logSend() {
	global $log;
	if ($_POST['log']==1) file_put_contents($_SERVER['DOCUMENT_ROOT']."/seolog.txt", $log);
}

function robots($rob) {
	$norm = 1;
	if (stristr($rob, "Disallow: /\r\n")) $norm = 0;
	if (stristr($rob, "Disallow: /\n")) $norm = 0;
	if (!stristr($rob, "Disallow: /bc/")) $norm = 0;
	if (!stristr($rob, "Disallow: /cart")) $norm = 0;
	if (stristr($rob, "Host:") && !stristr($rob, "Sitemap:")) $norm = 0;
	return $norm;
}