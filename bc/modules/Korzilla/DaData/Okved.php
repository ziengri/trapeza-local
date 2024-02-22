<?php

namespace App\modules\Korzilla\DaData;

class Okved
{
    private $token = '';
        
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function get(string $okved)
    {
        $resurl = ['result' => [], 'status' => 500];
        $ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/okved2',
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode(['query' => $okved]),
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