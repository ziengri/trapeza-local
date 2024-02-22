<?php 

namespace App\modules\bitcat\Copy\Object\Class2021;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2021 extends ObjectAbstract
{
    const CLASS_ID = 2021;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}