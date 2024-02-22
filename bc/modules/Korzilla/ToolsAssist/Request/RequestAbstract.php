<?php

namespace App\modules\Korzilla\ToolsAssist\Request;

use App\modules\Korzilla\ToolsAssist\Request\PostConvertor\PostConvertorInterface;
use App\modules\Korzilla\ToolsAssist\Request\Response\ResponseInterface;
use Exception;

abstract class RequestAbstract implements RequestInterface, PostRequestInterface
{
    protected $headers = [];
    protected $post = [];
    protected $get = [];

    protected $url;

    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @var PostConvertorInterface
     */
    protected $postConvertor;

    /**
     * @param ResponseInterface $response
     * @param PostConvertorInterface|null $postConvertor
     */
    public function __construct(ResponseInterface $response, $postConvertor = null)
    {
        $this->response = $response;
        $this->postConvertor = $postConvertor;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function addHeaders($headers): self
    {
        $headers = is_array($headers) ? $headers : [$headers];
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    public function addGet($key, $value): self
    {
        $this->get[$key] = $value;
        return $this;
    }

    public function addPost($key, $value): self
    {
        $this->post[$key] = $value;
        return $this;
    }

    public function handle(): ResponseInterface
    {
        $curl = $this->curlInit();

        $this->curlPrepare($curl);

        $this->response->setResponse(curl_exec($curl));
        $this->response->setRequestInfo(curl_getinfo($curl));
        
        curl_close($curl);

        return $this->response;
    }

    public function getPost(): array
    {
        return $this->post;
    }

    protected function curlInit()
    {
        $curl = curl_init();

        if ($curl === false) {
            throw new Exception('Не удалось создать объект curl');            
        }

        return $curl;
    }

    protected function curlPrepare($curl): self
    {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $this
            ->curlSetUrl($curl)
            ->curlSetHeaders($curl)
            ->curlSetPost($curl)
        ;

        return $this;
    }

    protected function curlSetUrl($curl): self
    {
        curl_setopt($curl, CURLOPT_URL, $this->getFullUrl());
        return $this;
    }

    protected function getFullUrl(): string
    {
        if (empty($this->url)) {
            throw new Exception('Не установлен адрес запроса');
        }

        $url = $this->url;

        if (!empty($this->get)) {
            $url .= strpos($url, '?') !== false ? '&' : '?';
            $url .= http_build_query($this->get, '', '&');
        }

        return $url;
    }

    protected function curlSetHeaders($curl): self
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        return $this;
    }
    
    protected function curlSetPost($curl): self
    {
        if (empty($this->post)) {
            return $this;
        }

        if (empty($this->postConvertor)) {
            throw new Exception('Не установлен postConvertor');
        }

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->postConvertor->convert($this->post));

        return $this;
    }
}