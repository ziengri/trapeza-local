<?php 

namespace App\modules\bitcat\Copy\Subdivision;

use App\modules\bitcat\Copy\SubClass\Copy as SubClassCopy;
use Exception;
use nc_Core;

class Copy
{
    /**
     * @var nc_core
     */
    private $nc_core;

    public function __construct()
    {
        $this->setNcCore();
    }

    /**
     * @param int $subdivisionID id копируемого раздела
     * @param int $parentSubID id раздела в который нужно скопировать
     * 
     * @return int id нового раздела
     */
    public function copySub(int $subdivisionID, int $parentSubID): int
    {
        $sub = $this->getSubdivisionById($subdivisionID);
        $parent = $this->getSubdivisionById($parentSubID);

        $this->replaceFileds($sub, $parent);
        
        try {
            unset($sub['Subdivision_ID']);
            $sub['Subdivision_ID'] = $this->insertSub($sub);
        } catch (Exception $e) {
            throw new Exception('Неудалось создать новый раздел: '.$e->message);
        }

        $subClasses = $this->getSubClassesIdBySubId($subdivisionID);

        $copy = new SubClassCopy();
        foreach ($subClasses as $cc) {
            $copy->copySubClass((int) $cc, (int) $sub['Subdivision_ID']);
        }

        if ($this->copyFiles($sub)) {
            $this->updateSub($sub);
        }

        return $sub['Subdivision_ID'];
    }

    private function getSubdivisionById(int $id): array
    {
        $sql = "SELECT * FROM `Subdivision` WHERE `Subdivision_ID` = {$id}";
        return $this->nc_core->db->get_row($sql, ARRAY_A) ?: [];
    }

    private function replaceFileds(array &$sub, array $parent)
    {
        $level = -1;
        do {
            $level++;
            $keyword = $sub['EnglishName'].'-copy'.($level ?: '');
        } while (!$this->isFreeKeyword($keyword));

        $sub['EnglishName'] = $keyword;
        $sub['Subdivision_Name'] .= ' копия';
        $sub['Hidden_URL'] = $parent['Hidden_URL'].$sub['EnglishName'].'/';
        $sub['Parent_Sub_ID'] = $parent['Subdivision_ID'];
        $sub['code1C'] = '';
        $sub['v1c'] = '';
        $sub['nodeletesub'] = '';
    }

    private function isFreeKeyword($keyword): bool
    {
        $sql = "SELECT EXISTS (SELECT * FROM `Subdivision` WHERE `EnglishName` = '{$keyword}')";
        return !$this->nc_core->db->get_var($sql);
    }

    private function insertSub($sub): int
    {
        $fields = $values = '';
        
        foreach ($sub as $field => $value) {
            $fields .= $fields ? ',' : '';
            $fields .= "`{$field}`";

            $values .= $values ? ',' : '';
            $values .= "'{$value}'";
        }

        $sql = "INSERT INTO `Subdivision` ($fields) VALUES ($values)";
        $this->nc_core->db->query($sql);

        if ($this->nc_core->db->is_error) {
            throw new Exception($this->nc_core->db->errno);
        }

        return (int) $this->nc_core->db->insert_id;
    }

    private function updateSub(array $sub)
    {
        $set = '';
        foreach ($sub as $field => $value) {
            $set .= $set ? ',' : '';
            $set .= "`{$field}` = '{$value}'";
        }

        $sql = "UPDATE `Subdivision` SET {$set} WHERE `Subdivision_ID` = {$sub['Subdivision_ID']}";

        $this->nc_core->db->query($sql);
    }

    private function getSubClassesIdBySubId(int $id): array
    {
        $sql = "SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE `Subdivision_ID` = {$id}";
        return $this->nc_core->db->get_col($sql) ?: [];
    }

    private function copyFiles(array &$sub): bool
    {
        $someCopy = false;

        $fileFields = [
            'img',
            'icon',
            'imgBig',
            'var_file_1',
        ];

        foreach ($fileFields as $fileField) {
            if (!empty($sub[$fileField])) {
                $this->copyFile($fileField, $sub);
                $someCopy = true;
            }
        }
        
        return $someCopy;
    }

    private function copyFile($fileField, &$sub)
    {
        global $FILES_FOLDER;

        if (!file_exists($FILES_FOLDER.$sub['Subdivision_ID'])) {
            mkdir($FILES_FOLDER.$sub['Subdivision_ID']);
        }

        $data = explode(':', $sub[$fileField]);
        $fileName = basename($data[3]);

        $oldFile = $FILES_FOLDER.$data[3];
        $newFile = $FILES_FOLDER.$sub['Subdivision_ID'].'/'.$fileName;

        if (!copy($oldFile, $newFile)) {
            $sub[$fileField] = '';
            return;
        }
 
        $data[3] = $sub['Subdivision_ID'].'/'.$fileName;
        $sub[$fileField] = implode(':', $data);
    }

    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }
}