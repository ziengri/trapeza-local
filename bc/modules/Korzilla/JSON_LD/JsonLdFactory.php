<?php

namespace App\modules\Korzilla\JSON_LD;

class JsonLdFactory
{
    private $domen;

    public function __construct()
    {   if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
            $http_protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        elseif(!empty($_SERVER['REQUEST_SCHEME'])){
            $http_protocol = $_SERVER['REQUEST_SCHEME'];
        }
        else $http_protocol = !empty($_SERVER['HTTPS']) ? "https" : "http";
        $this->domen = $http_protocol."://". $_SERVER['SERVER_NAME'];
    }

    public function BreadcrumbsList(): BreadcrumbsList
    {
        return BreadcrumbsList::getInstance($this->domen);
    }
    public function Product(): Product
    {
        return Product::getInstance($this->domen);
    }
    public function Categories(): Categories
    {
        return Categories::getInstance($this->domen);
    }
    public function News(): News
    {
        return News::getInstance($this->domen);
    }
    public function CollectionNews(): CollectionNews
    {
        return CollectionNews::getInstance($this->domen);
    }

    public function Article(): Article
    {
        return Article::getInstance($this->domen);
    }
    public function CollectionArticles(): CollectionArticles
    {
        return CollectionArticles::getInstance($this->domen);
    }

    
    public function BuildJsonLD(): string
    {   
        $jsonld = '';
        try {
            $jsonld .=  $this->BreadcrumbsList()->getJsonLd();
            $jsonld .=  $this->Product()->getJsonLd();
            $jsonld .=  $this->Categories()->getJsonLd();
            $jsonld .=  $this->News()->getJsonLd();
            $jsonld .=  $this->CollectionNews()->getJsonLd();
            $jsonld .=  $this->Article()->getJsonLd();
            $jsonld .=  $this->CollectionArticles()->getJsonLd();
        } catch (\Throwable $th) {
            return "<script>console.error(`JsonLD error: ". $th->getMessage(). "`)</script>";
        }

        return $jsonld;
    }
}