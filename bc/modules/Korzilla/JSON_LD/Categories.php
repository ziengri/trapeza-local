<?php

namespace App\modules\Korzilla\JSON_LD;

class Categories
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
    public static function getCategoryRates($Sub_ID){
        global $db;
        return $db->get_row("SELECT SUM(`ratecount`) AS `ratecount`, AVG(`rate`) AS `rate` FROM `Message2001` WHERE `Subdivision_ID` = '$Sub_ID'",ARRAY_A); 
    }

    /**
     * @param int $Sub_ID - 
     */
    public function setCategory($Sub_ID,$name,$img,$description,$url,$rates=0) : self //Добавить : Ссылка на това 
    {   
        global $db;
        if(empty($name)) throw new \Exception("Отсутсвует название товара", 1);
        if(empty($img)) $img='/images/nophoto.png';

        $rate=number_format((float)($rates['rate']>0?$rates['rate']:(float)number_format((float)(mt_rand(4.7 * 1000000,5.0 * 1000000)/1000000), 1, '.', '')), 1, '.', '');
        $ratecount=($rates['ratecount']>0?$rates['ratecount']:max(15,min(substr((string)($Sub_ID/71/150), -2),71)));

        
        $this->data='<script type="application/ld+json">' 
        .json_encode([
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
                '@type'=>'AggregateOffer',
                'url'=>$this->domain.$url,
                'offerCount'=>seoWordsRazdel($Sub_ID, 'NUM'),
                'lowPrice'=>seoWordsRazdel($Sub_ID, 'ASC').".00",
                'highPrice'=>seoWordsRazdel($Sub_ID, 'DESC').".00",
                "priceCurrency"=>"RUB"
                
            ],
        ]
        , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        .'</script>';
    return $this;
    }


    public function getJsonLd()
    {
        if (empty($this->data)) return;

        return $this->data;
    }
}
