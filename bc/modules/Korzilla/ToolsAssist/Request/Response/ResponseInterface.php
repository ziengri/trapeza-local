<?php

namespace App\modules\Korzilla\ToolsAssist\Request\Response;

interface ResponseInterface
{
    /**
     * Установить информацию о запросе
     * - curl: curl_getinfo
     */
    public function setRequestInfo($info);

    /**
     * Установить полученный ответ
     */
    public function setResponse($response);

    /**
     * Вернуть ответ в удобном для работы формате
     */
    public function normalize();

    /**
     * Ответ вернул ошибку
     */
    public function isError(): bool;

    /**
     * Получить сообщение ошибки
     */
    public function getErrorMessage(): string;
}