<?php

namespace App\modules\Korzilla\SMSCenter\SMS;

use App\modules\Korzilla\SMSCenter\ISMSCenter;

class Controller implements ISMSCenter
{
    private const URL = 'https://sms.ru/sms/';
    private $apiKey = '';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function push(string $phones, string $message): array
    {
        $data = [
            'api_id' => $this->apiKey,
            'phones' => $phones,
            'mes' => iconv("windows-1251", "utf-8", $message),
            'json' => 1
        ];

        return $this->curlPost('send', $data);
    }

    public function call($phones): array
    {
        $data = [
            'api_id' => $this->apiKey,
            'to' => $phones,
            'mes' => 'code',
            'call' => 1
        ];

        return $this->curlPost('send.php', $data);
    }

    protected function curlPost(string $link, array $data): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => self::URL . $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($data, '', '&')
        ]);

        curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = curl_exec($ch);
        curl_close($ch);

        return ['statusCode' => $info['http_code'], 'result' => $result];
    }
}
