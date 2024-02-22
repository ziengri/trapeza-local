<?php

/**
 * Класс поиска товаров по артикулу в Api профит лиги 
 * @author Kornev R.
 */
class ProfitLeague
{
    /**
     * Метод в Api Профит Лиги для поиска товаров по артикулу
     * @var string
     */
    private $findApiMethod = "/search/items";
    /**
     * Адрес Api Профит Лиги
     * @var string
     */
    private $apiUrl = "api.pr-lg.ru";
    /**
     * Api-ключ Профит Лиги
     * @var string
     */
    private $apiSecretKey;
    /**
     * Раздел для сохранения товаров
     * @var int
     */
    private $subToSaveProducts;
    /**
     * Инфоблок раздела в который сохранить товары
     * @var int
     */
    private $ccToSaveProducts;
    /**
     * Ид сайта
     * @var int
     */
    private $catalogue;
    /**
     * Наценка в % на товары
     * @var int
     */
    private $markup;
    /**
     * /bc/modules/default/OnlineCatalogClasses/handlers/Keyword
     * @var Keyword
     */
    private $keywordCreater;
    /**
     * Артикул по которому производится поиск
     * @var string|int @findByArticle
     */
    private $findByArticle;

    
    /**
     * @param string $apiSecretKey
     * @param int $subToSaveProducts
     * @param int $ccToSaveProducts
     * @param int $catalogue
     * @param int $markup
     * @param Keyword $keywordCreater
     * @throws Exception
     */
    public function __construct(
        $apiSecretKey,
        $subToSaveProducts,
        $ccToSaveProducts,
        $catalogue,
        $markup,
        $keywordCreater
    ) {
        if ( !$apiSecretKey || !$subToSaveProducts || !$ccToSaveProducts || !$catalogue) {
            throw new Exception("Invalid params setting", 1);
        } 
        if ( !$keywordCreater ) {
            throw new Exception("Invalid system setting", 1);
        }

        $this->apiSecretKey = $apiSecretKey;
        $this->subToSaveProducts = $subToSaveProducts;
        $this->ccToSaveProducts = $ccToSaveProducts;
        $this->catalogue = $catalogue;
        $this->markup = $markup;
        $this->keywordCreater = $keywordCreater;
    }

    /**
     * Поиск товаров по артикулу внутри api
     * @param string $findByArticle
     * @return array
     */
    public function getSearchResult($findByArticle): array
    {
        $this->findByArticle = $findByArticle;

        $searchRequestResult = $this->searchRequest();

        $resultItems = [];
        foreach ($searchRequestResult as $itemByArticle) {
            if (!empty($productWithLowestPrice = 
                        $this->getProductWithLowestPrice($itemByArticle["products"])
            )) $resultItems[] = $this->getProductClear($productWithLowestPrice);
        }
        
        return $resultItems;
    }

    /**
     * @param array $warehouses
     * @return array
     */
    private function getProductWithLowestPrice($warehouses): array
    {
        $productWithLowestPrice = [];
        
        foreach ($warehouses as $productInWarehouse) {
            if ($productInWarehouse["price"] < $productWithLowestPrice["price"]) {
                $productWithLowestPrice = $productInWarehouse;
            }

            if (!$productWithLowestPrice) $productWithLowestPrice = $productInWarehouse;
        }
        
        if (!$productWithLowestPrice['description'] || !$productWithLowestPrice['price']) return [];
        
        return $productWithLowestPrice;
    }

    /**
     * @param array $product
     * @return array
     */
    private function getProductClear($product): array
    {
        $code = $this->clearField($product['product_code']);
        $name = $this->clearField($product['description']);
        $art = $this->clearField($product['article']);
        $vendor = $this->clearField($product['brand']);

        $price = $this->getPriceWithMarkup($product['price']);
        $keyword = $this->keywordCreater::create("{$name} {$code} {$art}", 1);
        $stock = $this->getClearStock($product['quantity']);

        $item = [
           "Catalogue_ID" => $this->catalogue,
           "Subdivision_ID" => $this->subToSaveProducts,
           "Sub_Class_ID" => $this->ccToSaveProducts,
           "Checked" => 1,
           "code" => $code,
           "name" => $name,
           "price" => $price,
           "stock" => $stock,
           "art" => $art,
           "vendor" => $vendor,
           "Keyword" => $keyword,
           "var15" => $this->findByArticle,
        ];

        return $item;
    }

    /**
     * @param string|int $quantity
     * @return int
     */
    private function getClearStock($quantity): int
    {
        if (!$quantity) return 0;
        
        preg_match("/\d+/", $quantity, $matches);
        
        return $matches[0] ?: 0;
    }

    /**
     * @param int|float $originalPrice
     * @return float
     */
    private function getPriceWithMarkup($originalPrice): float
    {
        if (!$this->markup) return $originalPrice;

        return ((float) $originalPrice * (1 + $this->markup / 100));
    }

    /**
     * @param string $value
     * @return string
     */
    private function clearField($value): string
    {
        return addslashes(trim($value));
    }

    /**
     * Запрос поиска товаров по артикулу
     * @return array
     */
    private function searchRequest(): array
    {
        $params = [
            "secret" => $this->apiSecretKey,
            "article" => strip_tags($this->clearField($this->findByArticle)),
        ];
        
        $curlResult = $this->requestCurl($this->getSearchRequestUrl($params));
        if (!$curlResult["success"]) {
            return [];
        }

        return $curlResult["result"];
    }

    /**
     * @param array $params
     * @return string
     */
    private function getSearchRequestUrl($params): string
    {
        $curlRequestGet = http_build_query($params ?? [], "", "&");

        return "https://" . $this->apiUrl . "/" . $this->findApiMethod . "?{$curlRequestGet}";
    }

    /**
     * Summary of requestCurl
     * @param mixed $url
     * @param mixed $post
     * @return array
     */
    private function requestCurl($url, $post = false): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($post != false) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        
        return array(
            "success" => (mb_stristr($result, "error") ? false : true),
            "result" => json_decode($result, true)
        );
    }
}