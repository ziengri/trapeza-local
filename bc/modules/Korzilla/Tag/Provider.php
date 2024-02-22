<?php

namespace App\modules\Korzilla\Tag;

use App\modules\Korzilla\Tag\Bind\Bind;
use App\modules\Korzilla\Tag\Bind\Repository as BindRepository;

class Provider
{
    private $tagRepository;
    private $bindRepository;

    public function __construct()
    {
        $this->tagRepository = new Repository();
        $this->bindRepository = new BindRepository();
    }

    /**
     * Содать тэг
     * 
     * @param string $tag
     * 
     * @return Tag
     */
    public function tagCreate($tag)
    {
        return new Tag($tag);
    }

    /**
     * Сохраниеть тэг
     * 
     * @param Tag $tag
     * 
     * @return void
     */
    public function tagSave($tag)
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Удалить тэг
     * 
     * Удаляет тэг и связки этого тэга
     * 
     * @param Tag|int $tag тэг или id тэга
     * 
     * @return void
     */
    public function tagRemove($tag)
    {
        if (!$tag = $this->tagGetReal($tag)) {
            return;
        }

        $fileter = $this->filterGet();
        $fileter->tagId[] = $tag->Message_ID;

        foreach ($this->bindGetList($fileter) as $bind) {
            $this->bindRemove($bind);
        }

        $this->tagRepository->delete($tag);
    }

    /**
     * Получить список тэгов
     * 
     * @param Filter|null $filter 
     * 
     * @return Tag[]
     */
    public function tagGetList($filter = null)
    {
        return $this->tagRepository->search($filter);
    }

    /**
     * Создать связку тэга с объектом
     * 
     * @param Tag|int $tag тэг или id тэга
     * @param int $objectId
     * @param string $objectType
     * 
     * @return Bind|null
     */
    public function bindCreate($tag, $objectId, $objectType)
    {
        if (!$tag = $this->tagGetReal($tag)) {
            return null;
        }

        return new Bind($tag->Message_ID, (int) $objectId, $objectType);
    }

    /**
     * Сохраниеть связь
     * 
     * @param Bind $bind
     * 
     * @return void
     */
    public function bindSave($bind)
    {
        $this->bindRepository->save($bind);
    }

    /**
     * Удалить связку
     * 
     * @param Bind|int $bind 
     */
    public function bindRemove($bind)
    {
        if (!$bind = $this->bindGetReal($bind)) {
            return;
        }

        $this->bindRepository->delete($bind);
    }

    /**
     * Получить список связей тэгов с объектами
     * 
     * @param Filter|null $filter
     * 
     * @return Bind[]
     */
    public function bindGetList($filter = null)
    {
        return $this->bindRepository->search($filter);
    }

    /**
     * Получить список связей объекта с другими объектами
     * 
     * Возвращает список связей объекта с другими объектами
     * имеющие общие тэги
     * 
     * @param int $objectId id объекта для которого ищем
     * @param string $objectType тип объекта для которого ищем
     * @param Filter|null $filter
     * 
     * @return Bind[]
     */
    public function bindGetSiblings($objectId, $objectType, $filter = null)
    {
        $result = [];

        $tagFilter = $this->filterGet();
        $tagFilter->objectId[] = $objectId;
        $tagFilter->objectType[] = $objectType;

        if (!$tagList = $this->tagRepository->search($tagFilter)) {
            return $result;
        }

        $filter = $filter ?? $this->filterGet();

        foreach ($tagList as $tag) {
            $filter->tagId[] = $tag->Message_ID;
        }

        if (!$bindList = $this->bindGetList($filter)) {
            return $result;
        }

        foreach ($bindList as $bind) {
            if ($bind->object_id === (int) $objectId && $bind->object_type === (string) $objectType) {
                continue;
            }

            $result[$bind->Message_ID] = $bind;
        }

        return $result;
        // return $this->bindRepository->getSiblings($objectId, $objectType, $filter);
    }

    /**
     * Получить фильтр
     * 
     * @return Filter
     */
    public function filterGet()
    {
        return new Filter();
    }

    /** 
     * @param Tag|int 
     * 
     * @param Tag|null
     */
    private function tagGetReal($tag)
    {
        if ($tag instanceof Tag) {
            return $tag;
        }

        if ($tag = $this->tagRepository->get($tag)) {
            return $tag;
        }

        return null;
    }

    /** 
     * @param Bind|int 
     * 
     * @param Bind|null
     */
    private function bindGetReal($bind)
    {
        if ($bind instanceof Bind) {
            return $bind;
        }

        if ($bind = $this->bindRepository->get($bind)) {
            return $bind;
        }

        return null;
    }
}