<?php

namespace App\modules\bitcat\Copy\Object\Class2010;

use App\modules\bitcat\Copy\Object\CopyObjectAbstract;
use App\modules\bitcat\Copy\Object\ObjectAbstract;

class CopyObject extends CopyObjectAbstract
{
    protected function replaceFields(ObjectAbstract $object)
    {
        if ($name = $object->getProperty('name')) {
            $object->setProperty('name', $name.' копия');
        }
    }
}