<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\City;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Builder;
use App\modules\Korzilla\ToolsAssist\SingletonTrait;
use RuntimeException;
use SimpleXMLElement;

class Collection
{
    use SingletonTrait;

    private $list;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new Repository();
        $this->initList();
    }

    /**
     * Получить населенный пункт по коду
     * 
     * @param string $code
     * 
     * @return City|null
     */
    public function get($code)
    {
        if (!isset($this->list[$code])) return null;

        if (!$this->list[$code] instanceof City) {
            $this->list[$code] = new City($this->list[$code]);
        }

        return $this->list[$code];
    }

    /**
     * Получить итерируемый список
     * 
     * @return iterable|City[]
     */
    public function iterator()
    {
        if (!$this->list) return [];

        foreach ($this->list as $code => $unused) {
            yield $code => $this->get($code);
        }
    }

    /**
     * Проинициализировать список
     * 
     * @throws RuntimeException
     */
    private function initList()
    {
        if (isset($this->list)) return;

        if ($this->repository->lastUpdated() + 0 < date('U') || (!$xmlList = $this->repository->get())) {
            $this->setNewList();
        }

        if ($xmlList = $this->repository->get()) {
            $this->buildList($xmlList);
        }
        
        if (!isset($this->list)) {
            throw new RuntimeException('Неудалось создать список городов');
        }
    }

    /**
     * Заполнить лист
     * 
     * @param SimpleXMLElement $list 
     */
    private function buildList($xmlList)
    {
        foreach($xmlList->city ?: [] as $city) {
            $this->list[(string)$city->code] = $city;
        }
    }

    /**
     * Установить новый список пунктов выдачи
     */
    private function setNewList()
    {
        if (!$list = (new Builder())->getCityList()) return;
        
        $this->repository->save($list);
    }
}