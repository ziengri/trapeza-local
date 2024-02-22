<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Token;

class Builder
{
    /**
     * Пострить Entity токен
     * 
     * @param array $data
     * 
     * @return Entity
     */
    public static function build($data = [])
    {
        return (new Entity())
            ->setToken($data['access_token'])
            ->setToken_type($data['token_type'])
            ->setExpires_in($data['expires_in'])
            ->setScope(self::convertScope($data['scope']))
            ->setJti($data['jti'])
            ->setLast_updated($data['last_updated'])
        ;
    }

    /**
     * Перевести scope из строки в массив
     * 
     * @return array
     */
    private static function convertScope($scope)
    {
        $result = [];
        
        foreach (explode(' ', $scope) as $item) {
            if (!$item) continue;

            $item = explode(':', $item);

            if (empty($item[0]) || empty($item[1])) continue;

            $result[$item[0]] = $item[1];
        }

        return $result;
    }
}