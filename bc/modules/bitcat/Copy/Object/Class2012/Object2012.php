<?php 

namespace App\modules\bitcat\Copy\Object\Class2012;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2012 extends ObjectAbstract
{
    const CLASS_ID = 2012;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}