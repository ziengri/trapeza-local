<?
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $pathInc, $nc_core, $HTTP_HOST, $citylink;

// редирект на https
if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") && $nc_core->catalogue->get_current('https')) {
    $location = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$location);
    exit;
}


$robots = $nc_core->catalogue->get_current('Robots');
$robots = preg_replace("/Sitemap: (.*)\/\/.*\/sitemap.(xml|php)/m", "Sitemap: \\1//".$_SERVER['HTTP_HOST']."/sitemap.xml", $robots);
$robots = preg_replace("/Host: (.*)\/\/.*/m", "Host: \\1//".$_SERVER['HTTP_HOST'], $robots);

//$robots = str_replace("\r","",$robots);
$strs = explode(PHP_EOL,$robots);
// var_dump($strs);
$newstr = array();
if (!$strs) exit;

$ARRAY_HOST = explode(".", $HTTP_HOST);
if(count($ARRAY_HOST)>=3 && $ARRAY_HOST[0]!=$login['login']) $citylink = $ARRAY_HOST[0];
if (!$citylink) $citylink = 'main';

// $disallowAll = $robots = $nc_core->catalogue->get_current('disallow_all');
// $disallowAllText = "Disallow: /\r";

foreach($strs as $str) {
	$not = $i = '';
	// if(strstr($str,$disallowAllText)) continue;
	if ($str[0]=='~') {
		$w = explode(" ",$str);
		if (strstr($w[0],"[not]")) $not = 1;
		$citykeys = explode("|",str_replace(array("[not]","~"),"",$w[0]));
		unset($w[0]);
		
		foreach($citykeys as $citykey) {
			if (($citykey==$citylink && !$not) || (!in_array($citylink,$citykeys) && $not && !$i)) {
				$newstr[] = implode(" ",$w);
				$i++;
			}
		}
	} else {
		$newstr[] = $str;
	}
}
// if ($disallowAll) $newstr[] = $disallowAllText;


$status = $PHP_TYPE == "cgi" ? "Status: 200 OK" : $_SERVER['SERVER_PROTOCOL'] . " 200 OK";

function removeBOM($text="") {
    if(substr($text, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $text= substr($text, 3);
    }
    return $text;
}

# вывод
$textrobots = removeBOM(implode("\n",$newstr));

if (!stristr($textrobots,"Yandex")) $textrobots .= "\r\nClean-param: cur_cc&curPos&share&recNum&sort&index&isNaked&category&utm&clid&etext";

header($status);
header("Last-Modified: ".$nc_core->catalogue->get_current('LastUpdated'));
header("Content-type: text/plain");
header("Content-Length: ".strlen($textrobots));
//if ($_SERVER[REMOTE_ADDR]) echo $not."\n\n----------\r\n";
echo $textrobots;
exit;
