<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Pvz;

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
     * Получить pvz по коду
     * 
     * @param string $code
     * 
     * @return Pvz|null
     */
    public function get($code)
    {
        if (!isset($this->list[$code])) return null;

        return new Pvz($this->list[$code]);
    }

    /**
     * Получить итерируемый список
     * 
     * @return iterable|Pvz[]
     */
    public function iterator()
    {
        if (!$this->list) return [];

        foreach ($this->list as $code => $unused) {
            yield $code => $this->get($code);
        }
    }

    /**
     * Получить коды всех пвз
     * 
     * @return array
     */
    public function getAllCodes()
    {
        return array_keys($this->list);
    }

    /**
     * Проинициализировать список
     * 
     * @throws RuntimeException
     */
    private function initList()
    {
        if (isset($this->list)) return;

        if ($this->repository->lastUpdated() + 86400 < date('U') || (!$xmlList = $this->repository->get())) {
            $this->setNewList();
        }

        if ($xmlList = $this->repository->get()) {
            $this->buildList($xmlList);
        }

        if (!isset($this->list)) {
            throw new RuntimeException('Неудалось создать список пуктов выдачи');
        }
    }

    /**
     * Заполнить лист
     * 
     * @param SimpleXMLElement $list 
     */
    private function buildList($xmlList)
    {
        foreach($xmlList->pvz ?: [] as $pvz) {
            $this->list[(string)$pvz->code] = $pvz;
        }
    }

    /**
     * Установить новый список пунктов выдачи
     */
    private function setNewList()
    {
        if (!$list = (new Builder())->getPvzList()) return;
    
        $this->repository->save($list);
    }
}