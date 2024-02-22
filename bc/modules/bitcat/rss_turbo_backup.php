<?php

global $db, $catalogue, $current_catalogue, $nc_core, $login;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

try {
    if (!$current_catalogue) {
        $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        if (!$catalogue) {
            $catalogue = $current_catalogue['Catalogue_ID'];
        }
    }
    $domain = ($current_catalogue['https'] ? 'https://' : 'http://') . $current_catalogue['Domain'];
    $fileLink = "/a/{$login['login']}/rss_turbo.xml";
    $filePath = $ROOTDIR . $fileLink;

    $sqlSubNews = " SELECT 
                sub.Subdivision_Name,
                sub.Subdivision_ID
            FROM 
                Subdivision as sub, 
                Sub_Class as cc 
            WHERE 
                sub.Catalogue_ID = '{$catalogue}' AND 
                sub.Subdivision_ID = cc.Subdivision_ID AND 
                cc.Class_ID = 2003 AND sub.Checked = 1 AND
                sub.rss_turbo_yandex = 1";

    $sqlSubText = " SELECT 
                        sub.Subdivision_Name,
                        sub.Subdivision_ID,
                        sub.Hidden_URL,
                        sub.LastUpdated
                    FROM 
                        Subdivision as sub, 
                        Sub_Class as cc 
                    WHERE 
                        sub.Catalogue_ID = '{$catalogue}' AND 
                        sub.Subdivision_ID = cc.Subdivision_ID AND 
                        cc.Class_ID = 182 AND sub.Checked = 1 AND
                        sub.rss_turbo_yandex = 1";

    $newsSubs = $db->get_results($sqlSubNews, 'ARRAY_A');
    $textSubs = $db->get_results($sqlSubText, 'ARRAY_A');

    if (!$newsSubs && !$textSubs) {
        throw new Exception("Нет разделов", 1);
    }

    $rss = new SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><rss xmlns:media="http://search.yahoo.com/mrss/" xmlns:yandex="http://news.yandex.ru" xmlns:turbo="http://turbo.yandex.ru"/>');
    $rss->addAttribute('version', '2.0');

    $channel = $rss->addChild('channel');
    $channel->addChild('title', $current_catalogue['Catalogue_Name']);
    $channel->addChild('link', $domain);
    $channel->addChild('language', 'ru');
    

    foreach ($newsSubs as $newsSub) {
        $links = $relatedLinks = [];
        $a = '';
        $sqlNews = "SELECT
                    *
                FROM
                    Message2003
                WHERE
                    Subdivision_ID = '{$newsSub['Subdivision_ID']}' AND 
                    Checked = 1 AND
                    (`textfull` != '' OR `text` != '')";

        $news = $db->get_results($sqlNews, 'ARRAY_A');
        $messagsID = array_column($news, 'name', 'Message_ID');

        foreach ($messagsID as $id => $name) {
            $links[$id] = $domain . nc_message_link($id, 2003);
            $relatedLinks[] = ['link' => $links[$id], 'name' => $name];
            $a .= "<a href='{$links[$id]}'>{$name}</a>";
        }
        if ($a) {
            $menu = "<menu>{$a}</menu>";
        }
        foreach ($news as $newsRow) {
            $imgPath = $db->get_var("SELECT `Preview` FROM Multifield WHERE Message_ID = '{$newsRow['Message_ID']}'");
        
            $imgUrl = ($imgPath ? $domain . $imgPath : '');
       
            $content = itemContent(
                [
                    'title' => $newsRow['name'],
                    'text' => ($newsRow['textfull'] ? $newsRow['textfull'] : "<div>{$newsRow['text']}</div>"),
                    'img' => $imgUrl,
                    'menu' => $menu
                ]
            );

            $item = $channel->addChild('item');
            $item->addAttribute('turbo', 'true');
            $item->addChild('hack:turbo:extendedHtml', 'true');
            $item->addChild('link', $links[$newsRow['Message_ID']]);

            $item->addChild('title', $newsRow['name']);

            $guid = $item->addChild('guid', $links[$newsRow['Message_ID']]);
            $guid->addAttribute('isPermaLink', 'true');
            if ($imgUrl) {
                $enclosure = $item->addChild('enclosure');
                $enclosure->addAttribute('url', $imgUrl);
                $enclosure->addAttribute('length', 400);
                $enclosure->addAttribute('type', 'image/jpeg');
            }
            $item->addChild('pubDate', date('D, d M Y H:i:s O', strtotime($newsRow['date'])));
            $item->addChild('category', $newsSub['Subdivision_Name']);
            $item->addChild('hack:turbo:source', $newsRow['url']);
            $item->addChild('hack:turbo:topic', $newsRow['name']);
            $item->addChild('author', replaceSpecialCharacter(($newsRow['autor'] ?: $current_catalogue['Catalogue_Name'])));

            $item->addChild('hack:turbo:content', '')->addCData($content);
        }
    }

    foreach ($textSubs as $textSub) {
        $text = '';
        $contentMessages = $db->get_results("SELECT `Message_ID` AS id, `name`, `text` FROM Message182 WHERE Subdivision_ID = '{$textSub['Subdivision_ID']}' AND Checked = 1", 'ARRAY_A');

        

        foreach ($contentMessages as $contentMessage) {
            if ($contentMessage['name']) {
                $link = $domain . nc_message_link($contentMessage['id'], 182);
                $relatedLinks[] = ['link' => $link, 'name' => $contentMessage['name']];
                $text .= "<h2>{$contentMessage['name']}</h2>";
            }
            $text .= $contentMessage['text'];
        }

        if (!$text) {
            continue;
        }
   
        $content = itemContent(['title' => $textSub['Subdivision_Name'], 'text' => $text]);

        $item = $channel->addChild('item');
        $item->addAttribute('turbo', 'true');
        $item->addChild('hack:turbo:extendedHtml', 'true');
        $item->addChild('link', $domain . $textSub['Hidden_URL']);
        $item->addChild('pubDate', date('D, d M Y H:i:s O', strtotime($textSub['LastUpdated'])));
        $item->addChild('category', $textSub['Subdivision_Name']);
        $item->addChild('hack:turbo:content', '')->addCData($content);
    }

    if (count($relatedLinks) > 0) {
        $related = $channel->addChild('hack:yandex:related');
        $related->addAttribute('type', 'infinity');
        foreach ($relatedLinks as $relatedLink) {
            $link = $related->addChild('link', $relatedLink['name']);
            $link->addAttribute('url', $relatedLink['link']);
        }
    }

    if (!file_put_contents($filePath, $rss->asXML())) {
        throw new Exception("Не удалось записать файл!!!", 1);
    } else {
        echo "Ссылка на файл <a href='{$fileLink}' target='_blank'>rss_turbo.xml</a>";
    }
} catch (\Exception $error) {
    echo $error->getMessage();
}

function replaceSpecialCharacter($string)
{
    return str_replace(['&', '>', '<', '"', "'"], ['&amp;', '&gt;', '&lt;', '&quot;', '&apos;'], $string);
}
/**
 * Получения контента новости
 *
 * @param array $params
 * @return string
 */
function itemContent(array $params)
{
    $itemContent = "
    <header>
        <h1>{$params['title']}</h1>
        " . ($params['img'] ? "<figure>
            <img src='{$params['img']}'>
        </figure>" : null) . "
        " . ($params['menu'] ? $params['menu'] : null) . "
    </header>
    " . normalizeLinkContent($params['text']);

    return $itemContent;
}
/**
 * нормализует ссылки
 *
 * @param string $str
 * @return string
 */
function normalizeLinkContent($str)
{
    $pattern = '/(?:src|href)=[\'"][^http].+?[\'"]/';
    return preg_replace_callback($pattern, function ($matches) {
        global $domain;
        return preg_replace('/[\'"](.+?)[\'"]/', $domain . '/$1', $matches[0]);
    }, $str);
}


class SimpleXMLExtended extends \SimpleXMLElement
{
    /**
     * addCData in xml
     *
     * @param string $cdata_text
     * @return void
     */
    public function addCData($cdata_text)
    {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
}
