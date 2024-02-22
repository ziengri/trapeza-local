<?php 

namespace App\modules\bitcat\Copy\Object\Class2001;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2001 extends ObjectAbstract
{
    const CLASS_ID = 2001;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}