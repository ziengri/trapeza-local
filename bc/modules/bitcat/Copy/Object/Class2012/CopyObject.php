<?php

namespace App\modules\bitcat\Copy\Object\Class2012;

use App\modules\bitcat\Copy\Object\CopyObjectAbstract;
use App\modules\bitcat\Copy\Object\ObjectAbstract;

use nc_Core;

class CopyObject extends CopyObjectAbstract
{
    public function copy(ObjectAbstract $object, int $subClassId)
    {
        $messageId = @nc_copy_message($object->classId, (int) $object->getProperty('Message_ID'), $subClassId);

        if ($messageId === false) {
            return false;
        }

        $this->copyPepoples((int) $object->getProperty('Message_ID'), $messageId);

        $messageId = (int) $messageId;

        $className = get_class($object);
        $newObject = new $className($messageId);

        $this->replaceFields($newObject);
        $newObject->save();

        return $messageId;
    }

    private function copyPepoples(int $fromOffceId, int $toOfficeId)
    {
        $nc_core = nc_Core::get_object();

        $sql = "SELECT `Message_ID`, `Sub_Class_ID` FROM `Message201` WHERE `office` = {$fromOffceId}";
        $dbRows = $nc_core->db->get_results($sql, ARRAY_A) ?: [];

        foreach ($dbRows as $dbRow) {
            $messageId = @nc_copy_message(201, $dbRow['Message_ID'], $dbRow['Sub_Class_ID']);

            if ($messageId === false) continue;

            $sql = "UPDATE `Message201` SET `office` = {$toOfficeId} WHERE `Message_ID` = {$messageId}";
            $nc_core->db->query($sql);
        }
    }
}