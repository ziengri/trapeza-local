<?php

namespace App\modules\bitcat\Copy\Object;

abstract class CopyObjectAbstract
{
    /**
     * @param ObjectAbstract $object копируемый объект
     * @param int $subClassId в какой инфаблок копируем
     * 
     * @return int|bool
     */
    public function copy(ObjectAbstract $object, int $subClassId)
    {
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
        $object->setProperty('name', $object->getProperty('name').' копия');
    }
}
