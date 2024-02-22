<?php

namespace App\modules\Korzilla\Payments\DolyameAPI;

use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\RefundResponse;
// use App\modules\Korzilla\Payments\DolyameAPI\Exceptions\DolyameRequestException;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CreateOrderRequest;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CommitOrderRequest;
use Exception;

class DolyameAPIClient
{
    private const BASE_URL = "https://partner.dolyame.ru/";
    private $v;
    private $apiURL;
    private $credentials;
    private $mtlsCertPath;
    private $sslKeyPath;

    /**
     * @param string $login
     * @param string $password
     * @param string $mtlsCertPath full path to MTLS certificate
     * @param string $sslKeyPath full path to SSL key
     * @param int $v API version
     */
    public function __construct(
        string $login,
        string $password,
        string $mtlsCertPath,
        string $sslKeyPath,
        int $v = 1
    ) {
        $this->v = $v;
        $this->apiURL = self::BASE_URL . "v$v/"; // e.g. https://partner.dolyame.ru/v1/
        $this->credentials = base64_encode("$login:$password");
        $this->mtlsCertPath = $mtlsCertPath;
        $this->sslKeyPath = $sslKeyPath;
    }

    /**
     * @return int
     */
    public function getApiVersion(): int
    {
        return $this->v;
    }


    /**
     * Метод создания заказа
     * https://dolyame.ru/develop/help/api/?method=create
     * @param \App\modules\Korzilla\Payments\DolyameAPI\Requests\CreateOrderRequest $request
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo
     * @throws \App\modules\Korzilla\Payments\DolyameAPI\Exceptions\DolyameRequestException
     */
    public function createOrder(CreateOrderRequest $request): OrderInfo
    {   
        $response = $this->makePostRequest('orders/create', $request->toArray());

        return OrderInfo::fromArray($response);
    }


    /**
     * Метод для подтверждения заказа
     * https://dolyame.ru/develop/help/api/?method=commit
     * @param \App\modules\Korzilla\Payments\DolyameAPI\Requests\CommitOrderRequest $request
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo
     * @throws \App\modules\Korzilla\Payments\DolyameAPI\Exceptions\DolyameRequestException
     */
    public function commitOrder(CommitOrderRequest $request): OrderInfo
    {
        $id = $request->getId();
        $response = $this->makePostRequest("orders/$id/commit", $request->toArray());

        return OrderInfo::fromArray($response);
    }

    /**
     * Метод для отмены заказа
     * https://dolyame.ru/develop/help/api/?method=cancel
     * @param string $id
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo
     * @throws \App\modules\Korzilla\Payments\DolyameAPI\Exceptions\DolyameRequestException
     */
    public function cancelOrder(string $id): OrderInfo
    {
        $response = $this->makePostRequest("orders/$id/cancel");

        return OrderInfo::fromArray($response);
    }


    /**
     * Метод получения актуальной информации по заказу
     * https://dolyame.ru/develop/help/api/?method=info
     * @param string $id
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo
     * @throws \App\modules\Korzilla\Payments\DolyameAPI\Exceptions\DolyameRequestException
     */
    public function orderInfo(string $id): OrderInfo
    {
        $response = $this->makeGetRequest("orders/$id/info");

        return OrderInfo::fromArray($response);
    }


    /**
     * @param string $endpoint
     * @return array
     */
    public function makeGetRequest(string $endpoint): array
    {
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function makePostRequest(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('POST', $endpoint, $data);
    }



    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $uuid = $this->generateUuidv4();

        $ch = curl_init($this->apiURL . $endpoint);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->sslKeyPath);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->mtlsCertPath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "x-correlation-id: " . $uuid,
            "Authorization: Basic " . $this->credentials,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $respone = curl_exec($ch);

        if (curl_errno($ch)) {
            var_dump("Ошибка запроса: ". curl_error($ch));
            throw new Exception(curl_error($ch), 1);
        }
        curl_close($ch);

        $respone = json_decode($respone, true);
        if (!is_array($respone)) {
            var_dump("Ошибка обработки json `" . json_last_error_msg() . "`");
            throw new Exception("Ошибка обработки json `" . json_last_error_msg() . "`");
        }
        if (isset($respone['code'])) {
            print_r($respone);
            throw new Exception($respone['message']);
        }

        return $respone;
    }



    private function generateUuidv4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}