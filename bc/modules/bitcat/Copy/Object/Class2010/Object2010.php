<?php 

namespace App\modules\bitcat\Copy\Object\Class2010;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2010 extends ObjectAbstract
{
    const CLASS_ID = 2010;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}