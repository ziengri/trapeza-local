<?
namespace App\modules\Korzilla\UnicCart;
use nc_Core;
class UnicCart {
    private $nc_core;
    public $Message, $Subdivision, $city;
    public function __construct(array $Message,array $Subdivision, $city){
        foreach($Subdivision as $i => $s){$Subdivision[$i] = trim($s);}
        foreach($Message as $i => $m){$Message[$i] = trim($m);}
        $this->city = $city;
        $this->Message = $Message;
        $this->Subdivision = $Subdivision;
        $this->nc_core = nc_Core::get_object();
        echo $this->city;
    }
}