<?php

namespace App\modules\Korzilla\DaData;

class OrganizationByINN {
    private $token = '';
        
    /**
     * __construct
     *
     * @param  mixed $token
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
    
    /**
     * get
     *
     * @param  mixed $inn
     * @return array
     */
    public function get(string $inn): array
    {
        $resurl = ['result' => [], 'status' => 500];
        $ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party',
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode(['query' => $inn]),
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Accept: application/json',
				"Authorization: Token {$this->token}"
				]
		]);
		$resurl['result'] = json_decode(curl_exec($ch), 1);
        $resurl['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
        
        return $resurl;
    }
}