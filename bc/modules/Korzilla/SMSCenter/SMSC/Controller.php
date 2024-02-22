<?php

namespace App\modules\Korzilla\SMSCenter\SMSC;

use App\modules\Korzilla\SMSCenter\ISMSCenter;

class Controller implements ISMSCenter
{
    private const URL = 'https://smsc.ru/sys/';
    private $login = '';
    private $password = '';
    private $errorsCodes = [
        "1" => "Ошибка в параметрах.",
        "2" => "Неверный логин или пароль. Также возникает при попытке отправки сообщения с IP-адреса, не входящего в список разрешенных Клиентом (если такой список был настроен Клиентом ранее).",
        "3" => "Недостаточно средств на счете Клиента.",
        "4" => "IP-адрес временно заблокирован из-за частых ошибок в запросах. Подробнее",
        "5" => "Неверный формат даты.",
        "6" => "Сообщение запрещено (по тексту или по имени отправителя). Также данная ошибка возникает при попытке отправки массовых и (или) рекламных сообщений без заключенного договора.",
        "7" => "Неверный формат номера телефона.",
        "8" => "Сообщение на указанный номер не может быть доставлено.",
        "9" => "Отправка более одного одинакового запроса на передачу SMS-сообщения либо более пяти одинаковых запросов на получение стоимости сообщения в течение минуты.",
        "Данная ошибка возникает также при попытке отправки пятнадцати и более запросов одновременно с разных подключений под одним логином (too many concurrent requests).",
    ];

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }


    public function push(string $phones, string $message): array
    {
        $data = [
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $phones,
            'mes' => $message,
            'fmt' => 3
        ];

        return $this->parsResult($this->curlPost('send.php', $data));
    }

    public function call(string $phones): array
    {
        $data = [
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $phones,
            'mes' => 'code',
            'call' => 1,
            'fmt' => 3
        ];

        return $this->parsResult($this->curlPost('send.php', $data));
    }

    protected function parsResult(array $res)
    {
        $result = ['status' => false, 'message' => 'Что-то пошло не так. Попробуйте повторить позже.', 'messageMail' => ''];

        if ($res['statusCode'] >= 300) {
            $result['messageMail'] = 'Сервис не доступен';
            return $result;
        }

        if ($res['result']['error_code']) {
            $result['messageMail'] = $this->errorsCodes[$res['result']['error_code']] ?: 'Неизвестная ошибка';
            return $result;
        }

        $result['status'] = true;
        $result['message'] = 'Сообщения отправлено';

        return $result;
    }

    protected function curlPost(string $link, array $data): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => self::URL . $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data, '', '&')
        ]);

        curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = curl_exec($ch);
        curl_close($ch);

        return ['statusCode' => $info['http_code'], 'result' => json_decode($result, 1)];
    }
}
