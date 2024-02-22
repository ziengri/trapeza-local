<?php

namespace App\modules\Korzilla\JSON_LD;

use Class2003;
use DateTime;
use DateTimeZone;
class CollectionArticles
{

    private static $instance;

    private $data = [];

    private $itemListElement = [];
    private $domain;

    private function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public static function getInstance($domain): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($domain);
        }
        return self::$instance;
    }


    public function addArticle(Class2003 $news): self
    {   
        // Основна колекции новостей (заполняеться при первом вызове)
        if (empty($this->data)){
            $this->data = [
                "@context"=> "http://schema.org",
                "@type"=> "CollectionPage",
                "name"=> $news->current_sub['Subdivision_Name'],
                "description"=> strip_tags($news->current_sub['text']),
                "url"=> $this->domain . $news->current_sub['Hidden_URL'],
                "aggregateRating"=> [
                    "@type"=> "AggregateRating",
                    "ratingValue"=> (float)number_format((float)(mt_rand(4.7 * 1000000,5.0 * 1000000)/1000000), 1, '.', ''),
                    "bestRating"=> "5",
                    "worstRating"=> "1",
                    "ratingCount"=> max(15,min(substr((string)($news->current_sub['Subdivision_ID']/71/150), -2),71))
                ],
                "mainEntity"=> [
                    "@type"=> "ItemList",
                    "itemListElement" => []
                ]
            ];
        }

        $datePublished = new DateTime($news->date, new DateTimeZone('Europe/Moscow'));
        $datePublished = $datePublished->format('Y-m-d\TH:i:sP');

        $this -> itemListElement[] = [
            "@type" => "ListItem",
            "position" => (count($this->itemListElement) + 1),
            "item" => [
                "@type" => "Article",
                "@id" => $this->domain . $news->fullLink,
                "headline" => $news->name,
                "url" => $this->domain . $news->fullLink,
                "datePublished" => $datePublished,
                "image" => $this->domain . ($news->photo_preview?:"/images/nophoto.png")
            ]
        ];

        return $this;

    }


    public function getJsonLd()
    {   

        // if (empty($this->data)) throw new \Exception("Пустой массив", 1);
        if (empty($this->data))
            return;

        $this->data['mainEntity']['itemListElement'] = $this->itemListElement;

        return '<script type="application/ld+json">' 
        .json_encode( $this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        .'</script>';
    }
}
