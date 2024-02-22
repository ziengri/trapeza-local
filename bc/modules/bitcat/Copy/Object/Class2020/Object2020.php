<?php 

namespace App\modules\bitcat\Copy\Object\Class2020;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2020 extends ObjectAbstract
{
    const CLASS_ID = 2020;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}