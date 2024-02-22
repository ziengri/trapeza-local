<?php
class Partcom
{
	private $login, 
			$pass;

    public function __construct($login,$pass){
    	$this->login = $login;
    	$this->pass = $pass;
    }
    
	public function getGoods($code,$vendor)
	{
		$data = pc_get("http://www.part-kom.ru/engine/api/v3/search/parts?number={$code}&maker_id={$vendor}&find_substitutes=1");
		if (!$data)
	    	return 'Нет результата';
        $type = ['Original' => 'original', 'ReplacementOriginal' => 'replace', 'ReplacementNonOriginal' => 'analog'];
	    foreach ($data as $val) { //собираем массив товаров
		    if ($val['description'] && $val['price'] && $val['detailGroup'] && $val['maker']) {
		        $partItems[$type[$val['detailGroup']]][$val['maker']][] = [
        		                                                            'name'                => $val['description'],
        		                                                            'art'                 => $val['number'],
        		                                                            'quantity'            => $val['quantity'],
        		                                                            'price'               => ceil($val['price']),
        		                                                            'guaranteedDays'      => $val['guaranteedDays'],
        		                                                            'providerDescription' => $val['providerDescription'],
        		                                                            'makerId'             => $val['makerId'],
        		                                                            'providerId'          => $val['providerId']
    		                                                              ];
		    }
		}
        return $partItems;
	}
	public function getCatalog($code)
	{
		$data = $this->getCurl("http://www.part-kom.ru/engine/api/v3/search/parts?number={$code}");
		if (isset($data['detail']) and $data['detail']=='Access is denied')
			return '<!--нет доступа к partcom-->';
		if (isset($data['status']) and $data['status']=='403')
			return '<!--нет доступа к partcom: '.$data['detail'].'-->';
		if (empty($data))
			return 'Нет результата';

		foreach ($data as $val) { //собираем массив категорий 
	        if ($val['description'] && $val['maker'] && $val['makerId']) {
	            if ($val['price'] || $partCat[$val['makerId']]['price'] > $val['price'] ) {
	                $partCat[$val['makerId']] = [
                                                'vendor' => $val['maker'], 
                                                'name'   => $val['description'], 
                                                'price'  => ($val['price'] ? ceil($val['price']) : '')
                                                ];
	            }
	        }
	    }
	    return $partCat;
	}

	private function getCurl($url)
	{
		$headers = [
					"Authorization: Basic ".base64_encode($this->login.':'.$this->pass),
				    "Content-Type: application/json",
				    "Accept: application/json"
					];
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$data = curl_exec($ch);
		curl_close($ch);
		return json_decode($data, true);
	}
}