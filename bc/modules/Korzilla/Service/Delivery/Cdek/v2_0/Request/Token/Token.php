<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Token;

use App\modules\Korzilla\Loker\Loker;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Builder as RequestBuider;
use App\modules\Korzilla\ToolsAssist\SingletonTrait;

class Token
{
    use SingletonTrait;

    /**
     * @var Entity
     */
    private $entity;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Loker
     */
    private $locker;


    public function __construct()
    {
        $this->repository = new Repository();
        $this->locker = new Loker('cdekToken');
    }

    /**
     * Получить токен
     * 
     * @return string
     */
    public function getToken()
    {
        if (!isset($this->entity)) $this->setToken();

        return $this->entity->getToken();
    }

    /**
     * Получить тип токена
     * 
     * @return string
     */
    public function getType()
    {
        if (!isset($this->entity)) $this->setToken();

        return $this->entity->getToken_type();
    }

    /**
     * Обновить токен
     * 
     * @return self
     */
    public function refresh()
    {
        $oldToken = $this->entity;

        $this->setToken();

        if ($oldToken->getToken() === $this->entity->getToken()) {
            $this->setNewToken();
        }

        return $this;
    }

    /**
     * Не истекло ли время жизни токена?
     * 
     * @return bool
     */
    public function alive()
    {
        if (!isset($this->entity)) $this->setToken();

        return $this->entity->getLast_updated() 
            && $this->entity->getLast_updated() + $this->entity->getExpires_in() - 3 > date('U')
        ;
    }

    /**
     * Установить токен
     * 
     * @return void
     */
    private function setToken()
    {
        while ($this->locker->isLocked(5)) {
            usleep(100000); # ожидать 0.1 секунду
        }

        $this->entity = $this->repository->getToken();

        if (!$this->entity || !$this->alive()) $this->setNewToken(); 
    }

    /**
     * Установить новый токен
     */
    private function setNewToken()
    {
        $this->locker->lock();

        $this->entity = Builder::build(array_merge(['last_updated' => date('U')], $this->getNewToken()));

        $this->repository->saveToken($this->entity);

        $this->locker->unlock();
    }

    /**
     * Получить новый токен
     * 
     * @return array
     */
    private function getNewToken()
    {
        return (new RequestBuider())->getToken();
    }
}