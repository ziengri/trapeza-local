<?php

namespace App\modules\bitcat\Copy;

use App\modules\bitcat\Copy\Object\CopyObject;
use App\modules\bitcat\Copy\Object\ObjectFactory;
use App\modules\bitcat\Copy\Subdivision\Copy as SubdivisionCopy;

use nc_Core;
use Exception;

class Controller
{
    /**
     * @var nc_Core
     */
    private $nc_core;
    const COPY_OBJECT_LIMIT = 10;

    public function __construct()
    {
        $this->setNcCore();
    }

    /**
     * @param int $subId id раздела
     */
    public function getCopySubdivisionForm(int $subId = null)
    {
        $request = $this->getRequest();

        if (!isset($subId)) $subId = (int) $request['sub_id'];

        $subdivision = $this->nc_core->subdivision->get_by_id($subId);
        $copyObjectsLimit = self::COPY_OBJECT_LIMIT;
        $subTree = $this->convertSubsToTree($this->getAllSubWithoutSystems());

        include 'Subdivision/CopyForm.php';
    }

    /**
     * @param int $subId id раздела который нужно скопировать
     * @param int $parentSubId id раздела в который в который необходимо копировать
     */
    public function copySubdivison(int $subId = null, int $parentSubId = null, bool $isCopyObjects = null)
    {
        $request = $this->getRequest();

        if (!isset($subId)) $subId = (int) $request['sub_id'];
        if (!isset($parentSubId)) $parentSubId = (int) $request['parent_sub_id'];
        if (!isset($isCopyObjects)) $isCopyObjects = (bool) $request['copy_objects'];

        $copy = new SubdivisionCopy();
        $copyedSubId = $copy->copySub($subId, $parentSubId);

        if ($isCopyObjects) {
            $copiedObjects = 0;
            
            $sql = "SELECT `Class_ID`, `Sub_Class_ID` FROM `Sub_Class` WHERE `Subdivision_ID` = {$copyedSubId}";
            $dbRows = $this->nc_core->db->get_results($sql, ARRAY_A) ?: [];
            
            foreach ($dbRows as $dbRow) {
                $sql = "SELECT `Message_ID` FROM `Message{$dbRow['Class_ID']}` WHERE `Subdivision_ID` = {$subId} AND `Checked` = 1";
                $rows = $this->nc_core->db->get_col($sql) ?: [];
                foreach ($rows as $objectId) {
                    try {
                        $this->copyObject((int) $dbRow['Class_ID'], (int) $objectId, (int) $dbRow['Sub_Class_ID']);
                        $copiedObjects++;
                    } catch(Exception $e) {}

                    if ($copiedObjects >= self::COPY_OBJECT_LIMIT) break 2;
                }
            }
        }

        return json_encode([
            'succes' => '<a href="'.$this->nc_core->subdivision->get_by_id($copyedSubId, 'Hidden_URL').'">ссылка на раздел</a>',
            'submodal' => true,
            'title' => 'Раздел успешно скопирован',
        ]);
    }

    /**
     * @param int $classId id компонента
     * @param int $objectId id объекта
     */
    public function getCopyObjectForm(int $classId = null, int $objectId = null)
    {
        $request = $this->getRequest();

        if (!isset($classId)) $classId = (int) $request['class_id'];
        if (!isset($objectId)) $objectId = (int) $request['object_id'];

        $object = ObjectFactory::getObject($classId, $objectId);
        $subTree = $this->convertSubsToTree($this->getSubExistsClassIdWithoutSystems($classId));

        include ObjectFactory::getCpoyFormPath($classId);
    }

    /**
     * @param int $classId id компонента
     * @param int $objectId id объекта
     * @param int $subClassId id инфоблока
     */
    public function copyObject(int $classId = null, int $objectId = null, int $subClassId = null)
    {
        $request = $this->getRequest();

        if (!isset($classId)) $classId = (int) $request['class_id'];
        if (!isset($objectId)) $objectId = (int) $request['object_id'];
        if (!isset($subClassId)) $subClassId = (int) $request['sub_class_id'];

        $object = ObjectFactory::getObject($classId, $objectId);
        $copyObject = ObjectFactory::getCopy($classId);
        $newObjectId = $copyObject->copy($object, $subClassId);
        
        return json_encode([
            'succes' => '<a href="'.nc_message_link($newObjectId, $classId).'">ссылка на объект</a>',
            'submodal' => true,
            'title' => 'Объект успешно скопирован',
        ]);
    }

    private function getRequest()
    {
        return securityForm($_REQUEST);
    }

    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }

    private function convertSubsToTree(array $subdivisions): array
    {
        $subTree = [];

        foreach ($subdivisions as $sub) {
            $subTree[$sub['Subdivision_ID']] = $sub;
        }
        unset($subdivisions, $sub);

        foreach ($subTree as $id => &$sub) {
            if (isset($subTree[$sub['Parent_Sub_ID']])) {
                $sub['isChild'] = true;
                $subTree[$sub['Parent_Sub_ID']]['children'][] = &$sub;
            }
        }
        unset($id, $sub);

        foreach ($subTree as $id => $sub) {
            if (!empty($sub['isChild'])) {
                unset($subTree[$id]);
            }
        }

        return $subTree;
    }

    private function getSubExistsClassIdWithoutSystems(int $classId): array
    {
        $ignoreSql = '';
        foreach ($this->getSystemSubUrls() as $dbRow) {
            $ignoreSql .= $ignoreSql ? ' AND ' : '';
            $ignoreSql .= "`Hidden_URL` NOT LIKE '{$dbRow}%'";
        }
        if ($ignoreSql) $ignoreSql = ' AND '.$ignoreSql;

        $catalogueID = $this->nc_core->catalogue->id();

        $sql = "SELECT Subdivision.`Subdivision_Name`, Sub_Class.`Sub_Class_ID`, Subdivision.`Subdivision_ID`, Subdivision.`Parent_Sub_ID`
                FROM `Sub_Class`
                    INNER JOIN `Subdivision` ON Subdivision.`Subdivision_ID` = Sub_Class.`Subdivision_ID`
                WHERE Subdivision.`Catalogue_ID` = {$catalogueID}
                    AND Sub_Class.`Class_ID` = {$classId}
                    {$ignoreSql}
                ORDER BY Subdivision.`Subdivision_Name`";

        return $this->nc_core->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function getAllSubWithoutSystems(): array
    {
        $ignoreSql = '';
        foreach ($this->getSystemSubUrls() as $dbRow) {
            $ignoreSql .= $ignoreSql ? ' AND ' : '';
            $ignoreSql .= "`Hidden_URL` NOT LIKE '{$dbRow}%'";
        }
        if ($ignoreSql) $ignoreSql = ' AND '.$ignoreSql;

        $catalogueID = $this->nc_core->catalogue->id();

        $sql = "SELECT `Subdivision_ID`, `Subdivision_Name`, `Parent_Sub_ID`
                FROM `Subdivision`
                WHERE `Catalogue_ID` = {$catalogueID}
                    {$ignoreSql}
                ORDER BY `Subdivision_Name`";
        
        return $this->nc_core->db->get_results($sql, ARRAY_A) ?: [];
    }

    private function getSystemSubUrls(): array
    {
        $systemKeywords = implode("','", $this->getSystemSubKeywords());
        if ($systemKeywords) $systemKeywords = "'{$systemKeywords}'";

        $catalogueID = $this->nc_core->catalogue->id();

        $sql = "SELECT `Hidden_URL` 
                FROM `Subdivision`
                WHERE `Catalogue_ID` = {$catalogueID}
                    AND `EnglishName` IN ({$systemKeywords})";

        
        return $this->nc_core->db->get_col($sql) ?: [];
    }

    private function getSystemSubKeywords(): array
    {
        return [
            '404',
            'blockofsite',
            'callme',
            'cart',
            'hits',
            'new',
            'spec',
            'comparison',
            'devlin',
            'dostavka-i-oplata',
            'index',
            'profile',
            'registration',
            'search',
            'settings',
            'system',
            'zone',
        ];
    }
}
