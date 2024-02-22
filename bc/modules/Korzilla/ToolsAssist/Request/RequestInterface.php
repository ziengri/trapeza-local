<?php

namespace App\modules\Korzilla\ToolsAssist\Request;

use App\modules\Korzilla\ToolsAssist\Request\Response\ResponseInterface;

interface RequestInterface
{
    /**
     * Добавить адрес запроса
     * 
     * @return self
     */
    public function setUrl(string $url);
    
    /**
     * Добавить заголовок в запрос
     * 
     * @param array|string $headers
     * 
     * @return self
     */
    public function addHeaders($headers);

    /**
     * Добавить GET параметр в запрос
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return self
     */
    public function addGet($key, $value);

    /**
     * Выполнить запрос
     * 
     * @return ResponseInterface
     */
    public function handle(): ResponseInterface;
}