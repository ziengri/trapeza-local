<?php 

namespace App\modules\bitcat\Copy\Object\Class2003;

use App\modules\bitcat\Copy\Object\ObjectAbstract;

class Object2003 extends ObjectAbstract
{
    const CLASS_ID = 2003;
    
    protected function setClassId()
    {
        $this->classId = self::CLASS_ID;
    }
}