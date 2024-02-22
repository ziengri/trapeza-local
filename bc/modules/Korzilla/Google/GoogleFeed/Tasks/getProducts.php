<?

class getProducts
{
    public string $products;
    public function products()
    {
        global $db;
        $products =
            "SELECT `Message_id`,`name`,`descr`,`Keyword`,`Subdivision_ID`
            FROM Message2001
            WHERE 
            NOT `name` = '' AND
            NOT `descr` = '' AND
        ";
        $this->products = $db->get_results($products, "ARRAY_A");
    }
    public function setPhotos()
    {
        global $db;
        $products =
            "SELECT `Message_id`,`name`,`descr`,`Keyword`,`Subdivision_ID`
            FROM Multifield
            WHERE 
            NOT `preview` = '/a/gruztech/files/userfiles/images/catalog/default.jpg' AND 
            NOT `preview` = '' AND
        ";
        $this->products = $db->get_results($products, "ARRAY_A");
    }
    public function setLink()
    {
        global $db;
        $products =
            "SELECT `Hidden_URL`,
            FROM Subdivision
            WHERE 
            NOT `name` = '' AND
            NOT `descr` = '' AND
        ";
        $this->products = $db->get_results($products, "ARRAY_A");
    }
}