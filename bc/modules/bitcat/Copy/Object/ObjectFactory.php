<?php

namespace App\modules\bitcat\Copy\Object;

use App\modules\bitcat\Copy\Object\Class182\CopyObject as Class182CopyObject;
use App\modules\bitcat\Copy\Object\Class182\Object182;
use App\modules\bitcat\Copy\Object\Class2001\CopyObject as Class2001CopyObject;
use App\modules\bitcat\Copy\Object\Class2001\Object2001;
use App\modules\bitcat\Copy\Object\Class2003\CopyObject as Class2003CopyObject;
use App\modules\bitcat\Copy\Object\Class2003\Object2003;
use App\modules\bitcat\Copy\Object\Class2009\CopyObject as Class2009CopyObject;
use App\modules\bitcat\Copy\Object\Class2009\Object2009;
use App\modules\bitcat\Copy\Object\Class2010\CopyObject as Class2010CopyObject;
use App\modules\bitcat\Copy\Object\Class2010\Object2010;
use App\modules\bitcat\Copy\Object\Class2012\CopyObject as Class2012CopyObject;
use App\modules\bitcat\Copy\Object\Class2012\Object2012;
use App\modules\bitcat\Copy\Object\Class2020\CopyObject as Class2020CopyObject;
use App\modules\bitcat\Copy\Object\Class2020\Object2020;
use App\modules\bitcat\Copy\Object\Class2021\CopyObject as Class2021CopyObject;
use App\modules\bitcat\Copy\Object\Class2021\Object2021;
use App\modules\bitcat\Copy\Object\Class244\CopyObject as Class244CopyObject;
use App\modules\bitcat\Copy\Object\Class244\Object244;
use Exception;

class ObjectFactory
{
    public static function getObject(int $classId, int $objectId): ObjectAbstract
    {
        switch ($classId) {
            case 182: 
                return new Object182($objectId);
            case 244: 
                return new Object244($objectId);
            case 2001: 
                return new Object2001($objectId);
            case 2003:
                return new Object2003($objectId);
            case 2009:
                return new Object2009($objectId);
            case 2010: 
                return new Object2010($objectId);
            case 2012: 
                return new Object2012($objectId);
            case 2020: 
                return new Object2020($objectId);
            case 2021: 
                return new Object2021($objectId);
            default:
                throw new Exception('Неопрделенный id компонента: '.$classId);
        }
    }

    public static function getCpoyFormPath(int $classId): string
    {
        switch ($classId) {
            case 182: 
                return __DIR__.'/Class182/CopyForm.php';
            case 244: 
                return __DIR__.'/Class244/CopyForm.php';
            case 2001: 
                return __DIR__.'/Class2001/CopyForm.php';
            case 2003:
                return __DIR__.'/Class2003/CopyForm.php';
            case 2009:
                return __DIR__.'/Class2009/CopyForm.php';
            case 2010: 
                return __DIR__.'/Class2010/CopyForm.php';
            case 2012: 
                return __DIR__.'/Class2012/CopyForm.php';
            case 2020: 
                return __DIR__.'/Class2020/CopyForm.php';
            case 2021: 
                return __DIR__.'/Class2021/CopyForm.php';
            default:
                throw new Exception('Неопрделенный id компонента: '.$classId);
        }
    }

    public static function getCopy(int $classId): CopyObjectAbstract
    {
        switch ($classId) {
            case 182: 
                return new Class182CopyObject();
            case 244: 
                return new Class244CopyObject();
            case 2001: 
                return new Class2001CopyObject();
            case 2003:
                return new Class2003CopyObject();
            case 2009:
                return new Class2009CopyObject();
            case 2010: 
                return new Class2010CopyObject();
            case 2012: 
                return new Class2012CopyObject();
            case 2020: 
                return new Class2020CopyObject();
            case 2021: 
                return new Class2021CopyObject();
            default:
                throw new Exception('Неопрделенный id компонента: '.$classId);
        }
    }
}
