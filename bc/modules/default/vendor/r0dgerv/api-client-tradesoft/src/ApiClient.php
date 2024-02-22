<?php

namespace R0dgerV\ApiClientTradesoft;

use GuzzleHttp\Client;
use R0dgerV\ApiClientTradesoft\exceptions\ApiErrorException;

/**
 * Class ApiClient
 * @package R0dgerV\ApiClientTradesoft
 */
class ApiClient
{

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $versionApi = 3;

    /**
     * @var int
     */
    protected $timeLimit = 10;

    /**
     * @var string
     */
    protected $service = 'provider';

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @param string $username
     * @param string $password
     * @param string $baseUrl
     */
    public function __construct($username, $password, $baseUrl = 'https://service.tradesoft.ru/')
    {
        $this->username = $username;
        $this->password = $password;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Кидать исключение ошибки если один при запросе к одному из провайдеров произошла ошибка (или время превышено)
     * @param bool|true $status
     */
    public function setError($status = true) {
        $this->error = $status;
    }

    /**
     * Запрос возвращает список подключенных к учетной записи поставщиков.
     * @return array
     */
    public function getProviderList()
    {
        $data = array_merge($this->getBaseData(),
            [
                'action' => 'GetProviderList',
            ]
        );

        return $this->getQuery($data);
    }

    /**
     * Запрос возвращает список опций доступных поставщику.
     * @return array
     */
    public function getOptionsList()
    {
        $data = array_merge($this->getBaseData(),
            [
                'action' => 'GetOptionsList',
                'container' => $this->container
            ]
        );

        return $this->getQuery($data, true);
    }

    /**
     * @param string $name
     * @param string $login
     * @param string $password
     * @return $this
     */
    public function generateProviderContentForOptionsList($name, $login, $password)
    {
        $this->container[] = $this->generateBaseProviderContent($name, $login, $password);

        return $this;
    }

    /**
     * @param string $name
     * @param string $login
     * @param string $password
     * @param string $code
     * @param bool $normalize
     * @return $this
     */
    public function generateProviderContentForProducerList($name, $login, $password, $code, $normalize = false)
    {
        $this->container[] = array_merge($this->generateBaseProviderContent($name, $login, $password),
            [
                'code' => $normalize ? $this->normalizeText($code) : $code,
            ]);

        return $this;
    }

    /**
     * @param string $name
     * @param string $login
     * @param string $password
     * @param string $code
     * @param string $producer
     * @param array $options
     * @param bool $normalize
     * @return $this
     */
    public function generateProviderContentForPriceList(
        $name,
        $login,
        $password,
        $code,
        $producer,
        array $options = [],
        $normalize = false
    )
    {
        $this->container[] = array_merge($this->generateBaseProviderContent($name, $login, $password),
            [
                'code' => $normalize ? $this->normalizeText($code) : $code,
                'producer' => $producer,
                'options' => $options,
            ]);

        return $this;
    }

    /**
     * Запрос возвращает список предложений по коду и производителю.
     * @return array
     */
    public function getPriceList()
    {
        $data = array_merge($this->getBaseData(),
            [
                'action' => 'getPriceList',
                'timeLimit' => $this->timeLimit,
                'container' => $this->container
            ]
        );

        return $this->getQuery($data);
    }

    /**
     * Запрос возвращает список производителей по коду.
     * @return array
     */
    public function getProducerList()
    {
        $data = array_merge($this->getBaseData(),
            [
                'action' => 'getProducerList',
                'timeLimit' => $this->timeLimit,
                'container' => $this->container
            ]
        );
        return $this->getQuery($data);
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client(['base_uri' => $this->baseUrl]);
        }
        return $this->client;
    }

    /**
     * @param string $name
     * @param string $login
     * @param string $password
     * @return array
     */
    protected function generateBaseProviderContent($name, $login, $password)
    {
        return [
            'provider' => $name,
            'login' => $login,
            'password' => $password
        ];
    }

    /**
     * @param array $data
     * @param bool $indexKey
     * @return array
     */
    protected function getQuery(array $data, $indexKey = false)
    {
        $response = $this->getClient()->request('POST', $this->getUrlQuery(), [
            'headers' => [
                'User-Agent' => 'rodger-api-client-tradesoft/1.0',
                'Accept'     => 'application/json',
            ],
            'body' => \GuzzleHttp\json_encode($data)
        ]);

        $result = \GuzzleHttp\json_decode($response->getBody(), true);
        if (!empty($result['error'])) {
            throw new ApiErrorException($result['error']);
        }

        $array = $this->convertResponse($result, $indexKey);

        return $array;
    }


    /**
     * @param array $result
     * @param bool $indexKey
     * @return array
     */
    protected function convertResponse(array $result, $indexKey)
    {
        $array = [];
        if (isset($result['data'])) {
            return $result['data'];
        }

        if (isset($result['container'])) {
            foreach ($result['container'] as $providers) {
                if ($this->error && !empty($providers['error'])) {
                    throw new ApiErrorException($providers['provider'] . ' - ' .$providers['error']);
                }
                if ($indexKey) {
                    $array[$providers['provider']] = $providers['data'];
                } else {
                    if (isset($providers['data'])) {
                        foreach ($providers['data'] as $model) {
                            $array[] = array_merge($model, ['provider' => $providers['provider']]);
                        }
                    }
                }
            }
        }

        return $array;

    }

    /**
     * @return string
     */
    protected function getUrlQuery()
    {
        return $this->versionApi . '/';
    }

    /**
     * @return array
     */
    protected function getBaseData()
    {
        return [
            'service' => $this->service,
            'user' => $this->username,
            'password' => $this->password
        ];
    }

    /**
     * Убираем не нужные символы
     * @param string $text
     * @return string mixed
     */
    protected function normalizeText($text) {
        return preg_replace('/[\s+\.\/\\_-]+/', '', $text);
    }
}