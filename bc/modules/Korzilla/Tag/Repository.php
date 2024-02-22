<?php

namespace App\modules\Korzilla\Tag;

use App\modules\Korzilla\Tag\Bind\Repository as BindRepository;

class Repository
{
    const TABLE_NAME = 'Message2266';
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
     * Получить тэг по id
     * 
     * @param int $Message_ID
     * 
     * @return Tag|null
     */
    public function get($Message_ID)
    {
        $sql = sprintf(
            'SELECT `Message_ID`, `tag` FROM `%s` WHERE `Message_ID` = %d AND `Catalogue_ID` = %d',
            self::TABLE_NAME,
            (int) $Message_ID,
            $this->catalogue
        );

        if (!$row = $this->db->get_row($sql)) {
            return null;
        }

        return $this->buildTag($row);
    }

    /**
     * Сохранить тэг
     * 
     * @param Tag $tag
     * 
     * @return void
     */
    public function save($tag)
    {
        if (!$tag->Message_ID) {
            $this->insert($tag);
            return;
        }

        $this->update($tag);
    }

    /**
     * Удалить тэг
     * 
     * @param Tag $tag
     * 
     * @return void
     */
    public function delete($tag)
    {
        $sql = sprintf(
            'DELETE FROM `%s` WHERE `Message_ID` = %d AND `Catalogue_ID` = %d', 
            self::TABLE_NAME,
            $tag->Message_ID,
            $this->catalogue
        );

        $this->db->query($sql);
    }

    /**
     * Поиск тэгов по параметрам
     * 
     * @param Filter|null $filter
     * 
     * @return Tag[]
     */
    public function search($filter = null)
    {
        $sql = sprintf(
            'SELECT tag.`Message_ID`, 
                    tag.`tag`
            FROM `%s` AS tag
            WHERE tag.`Catalogue_ID` = %d
                %s
            ORDER BY tag.`Message_ID`',
            self::TABLE_NAME,
            $this->catalogue,
            $this->filterParse($filter)
        );

        $tagList = [];
        foreach ($this->db->get_results($sql) ?: [] as $row) {
            $tag = $this->buildTag($row);
            $tagList[$tag->Message_ID] = $tag;
        }

        return $tagList;
    }

    /** @param Tag $tag */
    private function insert($tag)
    {
        $sql = sprintf(
            'INSERT INTO `%s` (`tag`, `Catalogue_ID`) VALUES ("%s", %d)',
            self::TABLE_NAME,
            $this->db->escape($tag->tag),
            $this->catalogue
        );

        $this->db->query($sql);

        if (!$this->db->is_error) {
            $tag->Message_ID = (int) $this->db->insert_id;
        }
    }

    /** @param Tag $tag */
    private function update($tag)
    {
        $sql = sprintf(
            'UPDATE `%s` SET `tag` = "%s" WHERE `Message_ID` = %d AND `Catalogue_ID` = %d ', 
            self::TABLE_NAME, 
            $this->db->escape($tag->tag), 
            $tag->Message_ID,
            $this->catalogue
        );

        $this->db->query($sql);
    }

    /** @return Tag */
    private function buildTag($row)
    {
        return new Tag($row->tag, (int) $row->Message_ID);
    }

    /**
     * @param Filter|null $filter
     * 
     * @return string
     */
    private function filterParse($filter = null)
    {
        if (!$filter) return '';

        $tagIdParse = function() use ($filter) {
            if (!$filter->tagId) return '';

            $in = array_reduce($filter->tagId, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= (int) $item;
            }, '');

            return sprintf(' AND tag.`Message_ID` IN (%s)', $in);
        };

        $tagParse = function() use ($filter) {
            if (!$filter->tag) return '';

            $in = array_reduce($filter->tag, function($carry, $item) {
                $carry .= $carry ? ',' : '';
                return $carry .= sprintf('"%s"', $this->db->escape($item));
            }, '');

            return sprintf(' AND tag.`tag` IN (%s)', $in);
        };

        $bindParse = function() use ($filter) {
            if (!$filter->bindId && !$filter->objectId && !$filter->objectType) {
                return '';
            }

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

            return sprintf(
                ' AND EXISTS (SELECT * FROM `%s` AS bind WHERE tag.`Message_ID` = bind.`tag_id` %s)', 
                BindRepository::TABLE_NAME,
                $bindIdParse().$objectIdParse().$objectTypeParse()
            );
        };

        return $tagIdParse().$tagParse().$bindParse();
    }
}
