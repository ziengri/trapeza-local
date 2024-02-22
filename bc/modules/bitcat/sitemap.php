<?php

define('MAXURL', 400);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
$test = $_GET['test'] == 1;

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";

global $db, $pathInc, $DOCUMENT_ROOT, $catalogue, $current_catalogue, $nc_core, $lastmod, $urls;

$HOST = str_replace("www.", "", $_SERVER['HTTP_HOST']);

// получить ID сайта и параметры
if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name($HOST);
    if (!$catalogue) {
        $catalogue = $current_catalogue['Catalogue_ID'];
    }
}

if (!$catalogue) {
    echo "not catalogue";
    exit;
}

$urlSite = ($current_catalogue['https'] ? "https" : "http") . "://" . $HOST;
$subs = $fortxt = $urls = [];
$lastmod = date("Y-m-d");

$urls[] = ['loc' => $urlSite, 'priority' => 1.0];

getCategoriesSiteMap();
getItemSiteMap();

$countUrls = count($urls);

if ($countUrls > MAXURL) {
    $sitemapindex = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex />');
    $sitemapindex->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    for ($i = 0; $i < ceil($countUrls / MAXURL); $i++) {
        $sitemap = $sitemapindex->addChild('sitemap');
        $sitemap->addChild('loc', "{$urlSite}/sitemap{$i}.xml");
        $sitemap->addChild('lastmod', $lastmod);
    }
    $xml = $sitemapindex->asXML();
    if (!file_put_contents($ROOTDIR . $pathInc . "/sitemap.xml", $xml)) die($ROOTDIR . $pathInc . "/sitemap.xml  --- Not save");
    $pathNum = 0;
}

$a = 0;
while ($a < $countUrls) {
    if ($a !== 0 && $a % MAXURL === 0) {
        $xml = $urlset->asXML();
        if (!file_put_contents($ROOTDIR . $pathInc . "/sitemap{$pathNum}.xml", $xml)) die($ROOTDIR . $pathInc . "/sitemap{$pathNum}.xml  --- Not save");
        $pathNum++;
        unset($urlset);
    }

    if (!isset($urlset)) {
        $urlset = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    $thisURL = $urls[$a];
    $usr = $urlset->addChild('url');
    $usr->addChild('loc', $thisURL['loc']);
    $usr->addChild('lastmod', $lastmod);
    $usr->addChild('changefreq', 'monthly');
    $usr->addChild('priority', $thisURL['priority']);
    $a++;
}

if (isset($urlset)) {
    $xml = $urlset->asXML();
    if (!file_put_contents($ROOTDIR . $pathInc . "/sitemap{$pathNum}.xml", $xml)) die($ROOTDIR . $pathInc . "/sitemap{$pathNum}.xml  --- Not save");
}

if ($fortxt) file_put_contents($ROOTDIR . $pathInc . "/sitemap.txt", implode("\n", $fortxt));

echo "Готово. <a target=_blank href='{$pathInc}/sitemap.xml'>Ссылка</a>:<br><textarea cols=60 rows=2>{$urlSite}/sitemap.php</textarea>";

function getItemSiteMap()
{
    global $subs, $db, $fortxt, $urlSite, $urls;

    $classes = [2001, 2003];

    foreach ($classes as $class) {
        if ($subs[$class] > 0) {
            $items = $db->get_results(
                "SELECT 
                    Message_ID AS mesid, 
                    Subdivision_ID AS sub, 
                    Keyword 
                FROM 
                    Message{$class} 
                WHERE 
                    Checked = 1 AND 
                    Subdivision_ID IN (" . implode(",", array_keys($subs[$class])) . ")",
                ARRAY_A
            );

            if ($items) {
                $catigory = $subs[$class];

                foreach ($items as $item) {
                    $urlItem = $urlSite
                        . $catigory[$item['sub']]['hidurl']
                        . ($item['Keyword'] ?: $catigory[$item['sub']]['engName'] . "_" . $item['mesid'])
                        . ".html";
                    $urls[] = [
                        'loc' => $urlItem,
                        'priority' => 0.7
                    ];
                    $fortxt[] = $urlItem;
                }
            }
        }
    }
}

function getCategoriesSiteMap()
{
    global $db, $catalogue, $urlSite, $subs, $fortxt, $urls, $current_catalogue;

    $balckListSubName = ['Поиск по каталогу'];

    $disallowHiddenUrlLine = [];

    foreach (explode("\r\n", $current_catalogue['Robots']) as $line) {
        if (strpos($line, 'Disallow') === false || str_replace(['?', '=', '.', "'"], '', $line) !== $line) continue;
        $val = explode(" ", $line);
        $disallowHiddenUrlLine[] = "a.Hidden_URL NOT LIKE '" . str_replace('*', '%', rtrim(trim($val[1]), '*')) . "%'";
    }

    if (!empty($disallowHiddenUrlLine)) $disallowHiddenUrl = "(" . implode(' OR ', $disallowHiddenUrlLine) . ")";


    $categoriesArr = $db->get_results(
        "SELECT 
            a.Subdivision_ID AS sub, 
            a.Subdivision_Name AS name, 
            a.Parent_Sub_ID AS parsub, 
            a.Hidden_URL AS hidurl,
            a.no_follow_seo AS no_follow,
            a.no_index_seo AS no_index,
            b.EnglishName, 
            b.Class_ID 
        FROM 
            Subdivision AS a, 
            Sub_Class as b 
        WHERE 
            (a.Checked = 1 OR a.inSitemap = 1)
            AND a.Subdivision_ID = b.Subdivision_ID 
            " . ($disallowHiddenUrl ? " AND {$disallowHiddenUrl}" : '') . "
            AND a.Catalogue_ID = '{$catalogue}'",
        ARRAY_A
    );

    if (!$categoriesArr) return;
    foreach ($categoriesArr as $c) {
        if (in_array($c['name'], $balckListSubName) || strstr($c['hidurl'], '/index/')) continue;

        if ($c['no_follow'] == '1' && $c['no_index'] == '1') continue;

        if ($c['no_index'] != '1') {
            $urls[] = [
                'loc' => $urlSite . $c['hidurl'],
                'priority' => ($c['parsub'] == 0 ? 0.9 : 0.8)
            ];
        }

        if ($c['Class_ID'] && $c['sub']) {
            $subs[$c['Class_ID']][$c['sub']] = [
                'hidurl' => $c['hidurl'],
                'engName' => $c['EnglishName'],
            ];
        }

        $fortxt[] = $urlSite . $c['hidurl'];
    }
}
