<?php

namespace App\modules\bitcat\Copy\SubClass;

use nc_Core;

class Copy
{
    /**
     * @var nc_Core
     */
    private $nc_core;

    public function __construct()
    {
        $this->setNcCore();    
    }

    /**
     * @param int $subClassId id копируемого инфоблока
     * @param int $subdivisionID id раздела в который нужно скопировать инфоблок
     * 
     * @return int id нового инфоблока
     */
    public function copySubClass(int $subClassId, int $subdivisionID): int
    {
        $cc = $this->getSubClassById($subClassId);

        $this->replaceFields($cc, $subdivisionID);

        unset($cc['Sub_Class_ID']);

        return $this->insertSubClass($cc);
    }

    private function getSubClassById(int $id): array
    {
        $sql = "SELECT * FROM `Sub_Class` WHERE `Sub_Class_ID` = {$id}";
        return $this->nc_core->db->get_row($sql, ARRAY_A) ?: [];
    }

    private function replaceFields(array &$cc, int $subdivisionID)
    {
        $cc['Subdivision_ID'] = $subdivisionID;

        $level = -1;
        do {
            $level++;
            $keyword = $cc['EnglishName'].($level ?: '');
        } while (!$this->isFreeKeyword($keyword, $subdivisionID));

        $cc['EnglishName'] = $keyword;
    }

    private function isFreeKeyword(string $keyword, int $subdivisionID): bool
    {
        $sql = "SELECT EXISTS 
                (
                    SELECT * 
                    FROM `Sub_Class` 
                    WHERE `Subdivision_ID` = {$subdivisionID}
                        AND `EnglishName` = '{$keyword}'
                )";
        return !$this->nc_core->db->get_var($sql);
    }

    private function insertSubClass(array $cc): int
    {
        $fields = $values = '';

        foreach ($cc as $field => $value) {
            $fields .= $fields ? ',' : '';
            $fields .= "`{$field}`";

            $values .= $values ? ',' : '';
            $values .= "\"".$this->nc_core->db->escape($value)."\"";
        }

        $sql = "INSERT INTO `Sub_Class` ($fields) VALUES ($values)";
        $this->nc_core->db->query($sql);
        
        return (int) $this->nc_core->db->insert_id;
    }

    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }
}