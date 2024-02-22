<?php

namespace App\modules\Korzilla\MySklad;

class Auth
{

    public static $token;

    public static function getToken($login, $password)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode("{$login}:{$password}")
            ],
            CURLOPT_URL => 'https://online.moysklad.ru/api/remap/1.2/security/token',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        if ($result['errors']) {
            throw new \Exception($result[0]['error'], $result[0]['code']);
        }
        self::$token = $result['access_token'];
		return $result;
    }
}
