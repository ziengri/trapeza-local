<?php 
class Tradesoft
{
	private $trade_login, 
            $trade_pass, 
            $vendors_arr;

	public function __construct($trade_login, $trade_pass, $vendors_arr)
    {
		$this->trade_login = $trade_login;
		$this->trade_pass = $trade_pass;
		$this->vendors_arr = $vendors_arr;
	}

	public function getGoods($code, $vendor) # получение списка товаров(ор/зам/анал)
	{
		$price = $this->getPriceList($code,$vendor);
		// global $current_user;
		// if ($current_user['PermissionGroup_ID'] == 1 ) {
		// 	return $price;
		// }
		if ($price['error'] || $price['container'][0]['error']) return $price;
		foreach ($price['container'] as $container) {
			foreach ($container['data'] as $val) {
				if ($val['caption'] && $val['price'] && $val['itemtype']) {
					$item = array(
						'name'      => $val['caption'],
						'art'		=> $val['code'],
						'price'		=> $val['price'],
						'stock' 	=> $val['rest'],
						'develery'  => str_replace(' / ','-',$val['deliverydays']),
						'direction' => $val['direction'],
						'existence' => (!$val['direction'] ? true : false),
						'provider' 	=> $price['container'][0]['provider'],
						'vendor'	=> $val['producer'],
						'id'		=> $val['itemHash'],
						'service'	=> 'tradeSoft',
						);

					if ($val['itemtype'] == "original") {
			    		$goods[$val['itemtype']][$val['producer']]["art.{$code}"][] = $item;			    		
			    	} else {
			    		$goods[$val['itemtype']][$val['producer']]["art.".$val['code']][] = $item;
			    	}
			    }
			}
		}
		return $goods;
	}

	public function getCatalog($code) # получение списка каталогов
	{
		$price = $this->getPriceList($code);
		global $current_user;
		// if ($current_user['PermissionGroup_ID'] == 1 ) {
		// 	return $price;
		// }
		if ($price['error'] || (count($price['container']) == 1 && $price['container'][0]['error'])) return $price;
		foreach ($price['container'] as $vendor) {
			foreach ($vendor['data'] as $val) {
				if ($vendor['provider'] && $val['caption'] && $val['price'] && $val['producer'] && $val['itemtype'] == 'original') {
					if (!$catalog[$val['producer']] || $catalog[$val['producer']]['price'] > $val['price']) {
						$catalog[$val['producer']] = [
													'price'   => ceil($val['price']),
													'name'    => $val['caption'],
	                                                'vendor'  => $val['producer'],
	                                                'provider'=> $vendor['provider']
													];
					}
				}
			}
		}
		return $catalog;
	}

	private function getPriceList($code, $vendor = '')
	{
		if ($vendor) {
            $providers = $this->getCurl('GetProviderList');
            foreach ($providers['data'] as $val) {
                $cont = $this->constructContainer($val['name'], ['code'=> $code,'producer' => $vendor]);
                if ($cont) $container[] = $cont;
            }
		} else {
			$venodrs = $this->getVendors($code);
			if ($venodrs['error'] || $venodrs['container'][0]['error']) return $venodrs;
			
			foreach ($venodrs['container'] as $provider) {
                foreach ($provider['data'] as $val) {
                    $cont = $this->constructContainer($provider['provider'], ['code'=> $code,'producer' => $val['producer']]);
                    if ($cont) $container[] = $cont;
                }	
			}
		}
		return $this->getCurl('getPriceList',$container);
	}

	private function getVendors($code)
	{
		$providers = $this->getCurl('GetProviderList');
		if ($providers['error']) return $providers;
    
		foreach ($providers['data'] as $val) {
            $cont = $this->constructContainer($val['name'], ['code' => $code]);
            if ($cont) $container[] = $cont;
		}
		return $this->getCurl('GetProducerList',$container);
	}

	private function getCurl($action, $container = '')
	{
		$request = $this->constructRequest($action,$container);
		$ch = curl_init('https://service.tradesoft.ru/3/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
		$data = curl_exec($ch);
		curl_close($ch);

		return json_decode($data,true);
	}

    private function constructContainer($provider,$arr)
    {
        if ($this->vendors_arr[$provider]) {
            $container = [
                        'provider'  => $provider,
                        'login'     => $this->vendors_arr[$provider]['login'],
                        'password'  => $this->vendors_arr[$provider]['pass'],
                        ];
            foreach ($arr as $key => $val) {
                $container[$key] = $val;
            }
        }
        return ($container ? $container : false);
    }

    private function constructRequest($action, $container = '')
    {
        $request = [
                    "service"   => "provider",
                    "user"      => $this->trade_login,
                    "password"  => $this->trade_pass,
                    "action"    => $action
                    ];
        if ($container) $request['container'] = $container;
        return $request;
    }
}