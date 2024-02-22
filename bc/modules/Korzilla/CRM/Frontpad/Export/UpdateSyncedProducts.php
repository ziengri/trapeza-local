<?php

namespace App\modules\Korzilla\CRM\Frontpad\Export;

use nc_Core;

class UpdateSyncedProducts
{
    const FIELD_DB_EXTERNAL_KEY = 'code';

    private $numenclature;
    private $catalogueID;

    /**
     * @var nc_Core
     */
    private $nc_core;

    public function __construct(array $numenclature, int $catalogueID)
    {
        $this->numenclature = $numenclature;
        $this->catalogueID = $catalogueID;
        $this->nc_core = nc_Core::get_object();
    }

    public function handle()
    {
        foreach ($this->getFrontpadProducts() as $fronpadProduct) {
            if ($dbProduct = $this->getDbProductByKey($fronpadProduct['key'])) {
                $this->updateDbProduct($fronpadProduct, $dbProduct['Message_ID']);
            }
        }
        
        $this->switchOffUnexportedProducts();
    }

    private function getFrontpadProducts()
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
    private function getDbProductByKey(string $key)
    {
        if (empty($key)) return null;

        $key = $this->nc_core->db->escape($key);

        $sql = "SELECT `Message_ID`, `Checked`
                FROM `Message2001` 
                WHERE `Catalogue_ID` = {$this->catalogueID} 
                    AND `".self::FIELD_DB_EXTERNAL_KEY."` = '{$key}'";

        return $this->nc_core->db->get_row($sql, ARRAY_A);
    }

    private function updateDbProduct(array $frontpadProduct, int $dbProductId)
    {
		global $setting;
        $obj = [
            'Checked' => 1,
            'name' => $frontpadProduct['name'],
            'price' => $frontpadProduct['price'] ?: 0,
        ];
		if ($setting['frontpadNoNameUpd']) unset($obj['name']);

        $set = '';
        foreach ($obj as $field => $value) {
            $set .= $set ? ',' : '';
            $set .= "`{$field}` = '".$this->nc_core->db->escape($value)."'";
        }

        $sql = "UPDATE `Message2001` SET {$set} WHERE `Message_ID` = {$dbProductId}";

        $this->nc_core->db->query($sql);
    }

    private function switchOffUnexportedProducts()
    {
        $keyField = self::FIELD_DB_EXTERNAL_KEY;

        $keys = "'".implode("','", $this->numenclature['product_id'])."'";

        $sql = "UPDATE `Message2001`
                SET `Checked` = 0 , `price` = 0
                WHERE `Catalogue_ID` = {$this->catalogueID}
                    AND `{$keyField}` IS NOT NULL
                    AND `{$keyField}` != ''
                    AND `{$keyField}` NOT IN ({$keys})";

        $this->nc_core->db->query($sql);
    }
}