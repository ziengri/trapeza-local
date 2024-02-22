<?php
namespace App\modules\Korzilla\ConsoleLog;
class ConsoleLog {
    private $data;
    protected static $_instance;
    private function __construct() {        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;  
        }
 
        return self::$_instance;
    }
 
    private function __clone() {
    }

    private function __wakeup() {
    }    

    public function setLog($data, $key=''){
        $key = $key ? $key. "_".random_int(100, 1000) : '';
        $this->data[$key] = $data;
    }
    public function getLog($USER_ID = ''){
        global $AUTH_USER_ID;
        return ($AUTH_USER_ID == $USER_ID || $USER_ID === '') ? $this->data : "";
    }
}
?>