<?php

namespace App\modules\Korzilla\JSON_LD;

use DateTime;
use DateTimeZone;
use Class2003;

class Article
{
    private static $instance;

    private $data = '';

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

    public function setArticle(Class2003 $news): self
    {

        $datePublished = new DateTime($news->date, new DateTimeZone('Europe/Moscow'));
        $datePublished = $datePublished->format('Y-m-d\TH:i:sP');

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            "headline" => $news->name,
            "articleSection" => $this->domain . $news->current_sub['Hidden_URL'],
            "datePublished" => $datePublished,
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $this->domain . $news->fullLink
            ],
            "aggregateRating"=> [
                "@type"=> "AggregateRating",
                "ratingValue"=> (float)number_format((float)(mt_rand(4.7 * 1000000,5.0 * 1000000)/1000000), 1, '.', ''),
                "bestRating"=> "5",
                "worstRating"=> "1",
                "ratingCount"=> max(15,min(substr((string)($news->current_sub['Subdivision_ID']/71/150), -2),71))
            ],
        ];

        // Проверка на наличие картинки
        if ($news->photo->records) {
            foreach ($news->photo->records as $i => $f) {
                $jsonLd['image'][] = $this->domain . $f['Preview'];
            }
        }

        //Проверка на наличие автора
        if ($news->autor) {
            $jsonLd['author'] = [
                "@type" => "Person",
                "name" => $news->autor,
            ];
        }

        // Проверка на наличение описания
        if ($news->text) {
            $jsonLd['description'] = $news->text;
        }

        $this->data = '<script type="application/ld+json">'
            . json_encode(
                $jsonLd,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            )
            . '</script>';
        return $this;
    }


    public function getJsonLd()
    {
        // if (empty($this->data)) throw new \Exception("Пустой массив", 1);
        if (empty($this->data)) {
            return;
        }

        return $this->data;
    }
}

