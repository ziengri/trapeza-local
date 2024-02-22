<?php
namespace App\modules\Korzilla\Canonical;
class Canonical {
    private $canonical_url;
    protected static $_instance;
    private function __construct() {        
    }
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;  
        }
 
        return self::$_instance;
    }  
    public function setCanonical($url){
        $this->canonical_url = $url;
    }
    public function getCanonical(){
        return $this->canonical_url;
    }
}
?>