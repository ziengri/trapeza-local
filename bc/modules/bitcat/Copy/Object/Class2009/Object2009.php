<?php 

namespace App\modules\bitcat\Copy\Object\Class2009;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2009 extends ObjectAbstract
{
    const CLASS_ID = 2009;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}