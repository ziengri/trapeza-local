<?php

namespace App\modules\Korzilla\Yandex\RSS;

use \DOMDocument;
use \DOMElement;

class Creater
{
    /** Максимальный размер файла
     * 
     * @var int
     */
    protected const MAX_SIZE = 14.5;
    /**
     * data
     *
     * @var array
     */
    private $data = [];
    /**
     * dom
     *
     * @var DOMDocument
     */
    private $dom;

    /**
     * xmlnsUrl
     *
     * @var string
     */
    private $xmlnsUrl = 'http://www.w3.org/2000/xmlns/';
    /**
     * mediaUrl
     *
     * @var string
     */
    private $mediaUrl = 'http://search.yahoo.com/mrss/';
    /**
     * yandexUrl
     *
     * @var string
     */
    private $yandexUrl = 'http://news.yandex.ru';
    /**
     * turboUrl
     *
     * @var string
     */
    private $turboUrl = 'http://turbo.yandex.ru';

    /**
     * related
     *
     * @var array
     */
    private $related = [];

    private $relatedDom = null;


    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function create(): ?string
    {
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $rss = $this->dom->createElement('rss');
        $this->dom->appendChild($rss);
        $rss->setAttributeNS($this->xmlnsUrl, 'xmlns:media', $this->mediaUrl);
        $rss->setAttributeNS($this->xmlnsUrl, 'xmlns:yandex', $this->yandexUrl);
        $rss->setAttributeNS($this->xmlnsUrl, 'xmlns:turbo', $this->turboUrl);
        $rss->setAttribute('version', '2.0');

        $channel = $this->dom->createElement('channel');
        $rss->appendChild($channel);

        $channel->appendChild($this->dom->createElement('title', $this->data['title']));
        $channel->appendChild($this->dom->createElement('link', $this->data['link']));
        $channel->appendChild($this->dom->createElement('language', 'ru'));

        $this->createItems($this->data['items'], $channel);

        // if (count($this->related) > 0) {
        //     $related = $this->dom->createElementNS($this->yandexUrl, 'yandex:related');
        //     $channel->appendChild($related);
        //     $related->setAttribute('type', 'infinity');
        //     foreach ($this->related as $value) {
        //         $link = $this->dom->createElement('link', $value['name']);
        //         $link->setAttribute('url', $value['link']);
        //         $related->appendChild($link);
        //     }
        // }

        $this->dom->formatOutput = true;
        return $this->dom->saveXML();
    }

    private function createItems(array $items, DOMElement $channel): void
    {
        if (!empty($items) && !$this->relatedDom) {
            $this->relatedDom = $this->dom->createElementNS($this->yandexUrl, 'yandex:related');
            $channel->appendChild($this->relatedDom);
            $this->relatedDom->setAttribute('type', 'infinity');
        }

        foreach ($items as $item) {
            $itemDom = $this->dom->createElement('item');
            $channel->appendChild($itemDom);
            $itemDom->setAttribute('turbo', 'true');
            $itemDom->appendChild($this->dom->createElementNS($this->turboUrl, 'turbo:extendedHtml', 'true'));
            $itemDom->appendChild($this->dom->createElement('link', $this->replaceSpecialCharacter($item['link'])));
            $itemDom->appendChild($this->dom->createElement('title', $this->replaceSpecialCharacter($item['title'])));

            $guid = $this->dom->createElement('guid', $this->replaceSpecialCharacter($item['link']));
            $guid->setAttribute('isPermaLink', 'true');
            $itemDom->appendChild($guid);

            $itemDom->appendChild($this->dom->createElementNS($this->turboUrl, 'turbo:source', $this->replaceSpecialCharacter($item['link'])));
            $itemDom->appendChild($this->dom->createElementNS($this->turboUrl, 'turbo:topic', $this->replaceSpecialCharacter($item['title'])));
            $itemDom->appendChild($this->dom->createElement('pubDate', date('D, d M Y H:i:s O', $item['timestamp'])));
            $itemDom->appendChild($this->dom->createElement('category', $item['category']));

            $text = $this->itemContent(
                [
                    'img' => $item['img'],
                    'menu' => $item['menu'],
                    'text' => $item['text'],
                    'title' => $item['title']
                ]
            );
            $content = $this->dom->createElementNS($this->turboUrl, 'turbo:content');
            $content->appendChild($this->dom->createCDATASection($text));
            $itemDom->appendChild($content);

            $itemDom->appendChild($this->dom->createElement('author', $this->replaceSpecialCharacter($item['author'] ?: $this->data['title'])));

            $link = $this->dom->createElement('link', $item['title']);
            $link->setAttribute('url', $item['link']);
            $this->relatedDom->appendChild($link);

            if ($this->getSizeStringMb($this->dom->saveXML()) > self::MAX_SIZE) {
                return;
            }
        }
    }

    private function replaceSpecialCharacter(string $string): string
    {
        return str_replace(['&', '>', '<', '"', "'"], ['&amp;', '&gt;', '&lt;', '&quot;', '&apos;'], $string);
    }

    private function normalizeLinkContent(string $str): string
    {
        $pattern = '/(?:src|href)=[\'"][^http].+?[\'"]/';
        return preg_replace_callback($pattern, function ($matches) {
            global $domain;
            return preg_replace('/[\'"](.+?)[\'"]/', $domain . '/$1', $matches[0]);
        }, $str);
    }
    /**
     * getSizeMb
     * 
     * Получения размера файла в мб
     *
     * @return int
     */
    protected function getSizeStringMb(string $string): float
    {
        return (float) number_format(mb_strlen($string, '8bit') / 1024 / 1024, 2);
    }

    private function itemContent(array $params): string
    {
        return "
                <header>
                    <h1>{$params['title']}</h1>
                    " . ($params['img'] ? "<figure>
                        <img src='{$params['img']}'>
                    </figure>" : null) . "
                    " . ($params['menu'] ? $params['menu'] : null) . "
                </header>
                " . $this->normalizeLinkContent($params['text']);
    }
}