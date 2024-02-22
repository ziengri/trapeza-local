<?php

namespace App\modules\Korzilla\CRM\Frontpad\Export;

use Exception;
use nc_Core;

class ExportUsynchronicProducts
{
    const FIELD_DB_EXTERNAL_KEY = 'code';

    private $numenclature;
    private $rootSub;
    private $catalogueID;

    /**
     * @var nc_Core
     */
    private $nc_core;

    public function __construct(array $numenclature, int $catalogueID, int $rootSubId)
    {
        // file_put_contents('/var/www/krza/data/www/krza.ru/a/kiwigifts/log/frontpad/test2.log', print_r($numenclature, 1));
        $this->numenclature = $numenclature;
        $this->catalogueID = $catalogueID;
        $this->nc_core = nc_Core::get_object();
        $this->rootSub = $this->getRootSub($rootSubId);
    }

    public function handle()
    {
        foreach ($this->getFrondtpadProducts() as $frontpadProduct) {
            $dbProduct = $this->getDBProductByKey($frontpadProduct['key']);
            switch (true) {
                case !$dbProduct: 
                    $this->insertProduct($frontpadProduct);    
                    break;
                case !$dbProduct['Checked']:
                    $this->updateUncheckedProduct($frontpadProduct, (int) $dbProduct['Message_ID']);
                    break;
            }
        }
    }

    private function getRootSub(int $rootSubId): array
    {
        $result = ['sub' => $rootSubId];

        if (!$subdivision = $this->nc_core->subdivision->get_by_id($rootSubId)) {
            throw new Exception('Не удалось определить раздел');
        }

        if ($this->catalogueID !== (int) $subdivision['Catalogue_ID']) {
            throw new Exception('Раздел не пренадлежит сайту');
        }

        foreach ($this->nc_core->sub_class->get_all_by_subdivision_id($rootSubId) as $subClass) {
            if (2001 === (int) $subClass['Class_ID']) {
                $result['cc'] = $subClass['Sub_Class_ID'];
                break;
            }
        }

        if (empty($result['cc'])) {
            throw new Exception('Раздел должен быть типа каталог');
        }

        return $result;
    }

    private function getFrondtpadProducts()
    {
        foreach ($this->numenclature['product_id'] as $productNum => $productID) {
            yield [
                'key' => $productID,
                'name' => $this->numenclature['name'][$productNum],
                'price' => $this->numenclature['price'][$productNum],
            ];
        }
    }

    /**
     * @return array|null
     */
    private function getDBProductByKey(string $key)
    {
        if (empty($key)) return null;

        $key = $this->nc_core->db->escape($key);

        $sql = "SELECT `Message_ID`, `Checked`
                FROM `Message2001` 
                WHERE `Catalogue_ID` = {$this->catalogueID} 
                    AND `".self::FIELD_DB_EXTERNAL_KEY."` = '{$key}'";

        return $this->nc_core->db->get_row($sql, ARRAY_A);
    }

    private function insertProduct(array $frontpadProduct)
    {
        $obj = [
            'Subdivision_ID' => $this->rootSub['sub'],
            'Sub_Class_ID' => $this->rootSub['cc'],
            'Catalogue_ID' => $this->catalogueID,
            'Checked' => 1,
            'name' => $frontpadProduct['name'],
            'price' => $frontpadProduct['price'] ?: 0,
            self::FIELD_DB_EXTERNAL_KEY => $frontpadProduct['key'],
        ];

        $fileds = $values = '';
        foreach ($obj as $field => $value) {
            $fileds .= $fileds ? ',' : '';
            $fileds .= "`{$field}`";

            $values .= $values ? ',' : '';
            $values .= "'".$this->nc_core->db->escape($value)."'";
        }

        $sql = "INSERT INTO `Message2001` ({$fileds}) VALUES ({$values})";

        $this->nc_core->db->query($sql);
    }

    private function updateUncheckedProduct(array $frontpadProduct, int $dbProductId)
    {
        $obj = [
            'Checked' => 1,
            'name' => $frontpadProduct['name'],
            'price' => $frontpadProduct['price'] ?: 0,
        ];

        $set = '';
        foreach ($obj as $field => $value) {
            $set .= $set ? ',' : '';
            $set .= "`{$field}` = '".$this->nc_core->db->escape($value)."'";
        }

        $sql = "UPDATE`Message2001` SET {$set} WHERE `Message_ID` = {$dbProductId}";

        $this->nc_core->db->query($sql);
    }  
}