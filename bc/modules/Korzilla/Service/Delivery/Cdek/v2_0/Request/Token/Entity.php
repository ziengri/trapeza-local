<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Token;

class Entity
{
    /**
     * @var string jwt-токен
     */
    private $token;
    /**
     * @var string тип токена
     */
    private $token_type;
    /**
     * @var int срок действия токена в секундах
     */
    private $expires_in;
    /**
     * @var array область действия токена
     */
    private $scope;
    /**
     * @var string уникальный идентификатор токена
     */
    private $jti;
    /**
     * @var int
     */
    private $last_updated;

    /**
     * @param string $token
     * 
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token_type
     * 
     * @return self
     */
    public function setToken_type($token_type)
    {
        $this->token_type = $token_type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken_type()
    {
        return $this->token_type;
    }

    /**
     * @param int $expires_in
     * 
     * @return self
     */
    public function setExpires_in($expires_in)
    {
        $this->expires_in = $expires_in;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpires_in()
    {
        return $this->expires_in;
    }

    /**
     * @param array $scope
     * 
     * @return self
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $jti
     * 
     * @return self
     */
    public function setJti($jti)
    {
        $this->jti = $jti;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getJti()
    {
        return $this->jti;
    }

    /**
     * @param int $last_updated
     * 
     * @return self
     */
    public function setLast_updated($last_updated)
    {
        $this->last_updated = $last_updated;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLast_updated()
    {
        return $this->last_updated;
    }
}