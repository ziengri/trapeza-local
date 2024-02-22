<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Token;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;

class Repository
{
    const FILE_NAME = 'v2_0_token.json';

    /**
     * Сохранить токен
     * 
     * @param Entity $entity
     */
    public function saveToken($entity)
    {
        file_put_contents($this->getFilePath(), json_encode([
            'access_token' => $entity->getToken(),
            'token_type' => $entity->getToken_type(),
            'expires_in' => $entity->getExpires_in(),
            'scope' => $this->convertScope($entity->getScope()),
            'jti' => $entity->getJti(),
            'last_updated' => $entity->getLast_updated(),
        ]));
    }

    /**
     * Получить токен
     * 
     * @return Entity|null
     */
    public function getToken()
    {
        clearstatcache(true, $this->getFilePath());
        
        if (!file_exists($this->getFilePath())) return null;

        if (!$data = file_get_contents($this->getFilePath())) return null;

        if (!$data = json_decode($data, true)) return null;

        return Builder::build($data);
    }

    /**
     * Полить путь до файла
     * 
     * @return string
     */
    private function getFilePath()
    {
        return ToolsAssist::getInstance()->getRepository()->getSiteDir().'/'.self::FILE_NAME;
    }

    /**
     * Конвертировать scope из массива в строку
     * 
     * @param array $scope
     * 
     * @return string
     */
    private function convertScope($scope)
    {
        $result = '';
        foreach ($scope as $key => $val) {
            $result .= $result ? ' ' : '';
            $result .= "{$key}:{$val}";
        }
        return $result;
    }
}