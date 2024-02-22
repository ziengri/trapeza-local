<?php

namespace App\modules\Korzilla\JSON_LD;

class Product
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

    public function setProduct($Sub_ID,$name,$img,$brand,$description,$url,$price,$rate,$ratecount) : self 
    {
        if(empty($name)) throw new \Exception("Отсутсвует название товара", 1);
        if(empty($img)) $img='/images/nophoto.png';
        // if(empty($description)) throw new \Exception("Отсутсвует описание товара", 1);
        if(empty($url)) throw new \Exception("Отсутсвует ссылка на товар", 1);


        $rate=($rate>0?$rate:(float)number_format((float)(mt_rand(4.7 * 1000000,5.0 * 1000000)/1000000), 1, '.', ''));
        $ratecount=($ratecount>0?$ratecount:max(15,min(substr((string)($Sub_ID/71/150), -2),71)));


        $jsonLd=[
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name'=>$name,
            'image'=> $this->domain.$img,
            'description'=>$description,
            'aggregateRating'=>[
                '@type'=>'AggregateRating',
                'ratingValue'=>$rate,
                'reviewCount'=>$ratecount
            ],
            'offers'=>[
                '@type'=>'Offer',
                'url'=>$this->domain.$url,
                'priceCurrency'=>'RUB',
                'price'=> (!empty($price)?$price:0).'.00',
                "availability"=> "https://schema.org/InStock"
            ],
        ];
        if($brand){
            $jsonLd['brand']= [
                "@type"=> "Brand",
                "name"=> $brand
            ];
        }
        $this->data='<script type="application/ld+json">' 
        .json_encode($jsonLd
        , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        .'</script>';
    return $this;
    }


    public function getJsonLd()
    {
        // if (empty($this->data)) throw new \Exception("Пустой массив", 1);
        if (empty($this->data))return;

        return $this->data;
    }
}
