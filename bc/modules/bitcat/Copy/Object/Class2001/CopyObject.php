<?php

namespace App\modules\bitcat\Copy\Object\Class2001;

use App\modules\bitcat\Copy\Object\CopyObjectAbstract;
use App\modules\bitcat\Copy\Object\ObjectAbstract;
use nc_Core;

class CopyObject extends CopyObjectAbstract
{
    private $copyedObject;

    public function copy(ObjectAbstract $object, int $subClassId)
    {
        $this->copyedObject = $object;

        $messageId = @nc_copy_message($object->classId, (int) $object->getProperty('Message_ID'), $subClassId);

        if ($messageId === false) {
            return false;
        }

        $messageId = (int) $messageId;

        $className = get_class($object);
        $newObject = new $className($messageId);

        $this->replaceFields($newObject);
        $newObject->save();

        return $messageId;
    }

    protected function replaceFields(ObjectAbstract $object)
    {
        if (
            ((int) $object->getProperty('variablenameSide')) === 1 
            && $object->getProperty('Subdivision_ID') == $this->copyedObject->getProperty('Subdivision_ID')
        ) {
            # если это вариант товара и он копируется в тот же раздел
            $object->setProperty('variablename', $object->getProperty('variablename').' копия №'.time());
            $object->setProperty('variablenameSide', 1);
        } else {
            $object->setProperty('name', $object->getProperty('name').' копия №'.time());
            $object->setProperty('variablename', '');
            $object->setProperty('variablenameSide', 0);
        }
        
        $object->setProperty('Subdivision_IDS', '');
        $object->setProperty('id1c', '');
        $object->setProperty('xlslist', '');
        $object->setProperty('fromxls', '');
    }
}
