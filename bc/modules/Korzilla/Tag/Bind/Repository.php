<?php

namespace App\modules\Korzilla\Tag\Bind;

use App\modules\Korzilla\Tag\Filter;
use App\modules\Korzilla\Tag\Repository as TagRepository;

class Repository
{
    const TABLE_NAME = 'Message2263';
    /** @var int */
    private $catalogue;
    /** @var \nc_Db */
    private $db;

    public function __construct()
    {
        global $db, $catalogue;

        $this->db = $db;
        $this->catalogue = (int) $catalogue;
    }

    /**
     * Получить бинд по id
     * 
     * @param int $Message_ID
     * 
     * @return Bind|null
     */
    public function get($Message_ID)
    {
        $sql = sprintf(
            'SELECT `Message_ID`, `object_id`, `object_type`, `tag_id` FROM `%s` WHERE `Message_ID` = %d AND `Catalogue_ID` = %d',
            self::TABLE_NAME,
            (int) $Message_ID,
            $this->catalogue

        );

        if (!$row = $this->db->get_row($sql)) {
            return null;
        }

        return $this->buildBind($row);
    }

    /**
     * Сохранить бинд
     * 
     * @param Bind $bind
     * 
     * @return void
     */
    public function save($bind)
    {
        if (!$bind->Message_ID) {
            $this->insert($bind);
            return;
        }

        $this->update($bind);
    }

    /**
     * Удалить тэг
     * 
     * @param Bind $tag
     * 
     * @return void
     */
    public function delete($bind)
    {
        $sql = sprintf(
            'DELETE FROM `%s` WHERE `Message_ID` = %d AND `Catalogue_ID` = %d', 
            self::TABLE_NAME,
            $bind->Message_ID,
            $this->catalogue
        );

        $this->db->query($sql);
    }

    /**
     * Поиск тэгов по параметрам
     * 
     * @param Filter|null $filter
     * 
     * @return Bind[]
     */
    public function search($filter = null)
    {
        $sql = sprintf(
            'SELECT bind.`Message_ID`, 
                    bind.`object_id`, 
                    bind.`object_type`, 
                    bind.`tag_id`
            FROM `%s` AS bind
            WHERE bind.`Catalogue_ID` = %d
                %s
            ORDER BY bind.`Message_ID`',
            self::TABLE_NAME,
            $this->catalogue,
            $this->filterParse($filter)
        );

        return $this->buildList($this->db->get_results($sql) ?: []);
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
    public function getSiblings($objectId, $objectType, $filter = null)
    {
        // TO-DO сделать оптимизированную выборку
    }

    /** @param Bind $bind */
    private function insert($bind)
    {
        $sql = sprintf(
            'INSERT INTO `%s` (`object_id`, `object_type`, `tag_id`, `Catalogue_ID`) VALUES (%d, "%s", %d, %d)',
            self::TABLE_NAME,
            $bind->object_id,
            $this->db->escape($bind->object_type),
            $bind->tag_id,
            $this->catalogue
        );

        $this->db->query($sql);

        if (!$this->db->is_error) {
            $bind->Catalogue_ID = $this->catalogue;
            $bind->Message_ID = (int) $this->db->insert_id;
        }
    }

    /** @param Bind $bind */
    private function update($bind)
    {
        $sql = sprintf(
            'UPDATE `%s` SET `object_id` = %d, `object_type` = "%s", `tag_id` = %d WHERE `Message_ID` = %d AND `Catalogue_ID` = %d ', 
            self::TABLE_NAME, 
            $bind->object_id,
            $this->db->escape($bind->object_type), 
            $bind->tag_id,
            $bind->Message_ID,
            $this->catalogue
        );

        $this->db->query($sql);
    }

    /** @return Bind */
    private function buildBind($row)
    {
        return new Bind((int) $row->tag_id, (int) $row->object_id, $row->object_type, $row->Message_ID);
    }

    /**
     * @param object[] $rows
     * 
     * @return Bind[]
     */
    private function buildList($rows)
    {
        $bindList = [];
        foreach ($rows as $row) {
            $bind = $this->buildBind($row);
            $bindList[$bind->Message_ID] = $bind;
        }

        return $bindList;
    }

    /**
     * @param Filter|null $filter
     * 
     * @return string
     */
    private function filterParse($filter = null)
    {
        if (!$filter) return '';

        $bindIdParse = function() use ($filter) {
            if (!$filter->bindId) return '';

            $in = array_reduce($filter->bindId, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= (int) $item;
            }, '');

            return sprintf(' AND bind.`Message_ID` IN (%s)', $in);
        };

        $objectIdParse = function() use ($filter) {
            if (!$filter->objectId) return '';

            $in = array_reduce($filter->objectId, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= (int) $item;
            }, '');

            return sprintf(' AND bind.`object_id` IN (%s)', $in);
        };

        $objectTypeParse = function() use ($filter) {
            if (!$filter->objectType) return '';

            $in = array_reduce($filter->objectType, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= sprintf('"%s"', $this->db->escape($item));
            }, '');

            return sprintf(' AND bind.`object_type` IN (%s)', $in);
        };

        $tagIdParse = function() use ($filter) {
            if (!$filter->tagId) return '';

            $in = array_reduce($filter->tagId, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= (int) $item;
            }, '');

            return sprintf(' AND bind.`tag_id` IN (%s)', $in);
        };

        $tagParse = function() use ($filter) {
            if (!$filter->tag) return '';

            $in = array_reduce($filter->tag, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= sprintf('"%s"', $this->db->escape($item));
            }, '');

            return sprintf(
                ' AND EXISTS (SELECT * FROM `%s` AS tag WHERE tag.`Message_ID` = bind.`tag_id` AND tag.`tag` IN (%s))', 
                TagRepository::TABLE_NAME,
                $in
            );
        };

        return $bindIdParse().$objectIdParse().$objectTypeParse().$tagIdParse().$tagParse();
    }
}
