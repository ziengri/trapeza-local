<?php


namespace App\modules\Korzilla\Subdivision\Values\Inputs;

use App\modules\Ship\Helpers\FieldNotSet;
use App\modules\Ship\Parent\Inputs\Input;



class SubdivisionSetInput extends Input
{
    /** @var string */
    public $id;

    /** @var int */
    public $parentId;

    /** @var int|null */
    public $level;

    /** @var string */
    public $Subdivision_Name;

    /** @var string */
    public $Hidden_URL;

    /** @var string */
    public $EnglishName;

    /** @var int */
    public $Checked = 1;

    /** @var int */
    public $Priority;


    /** @var FieldNotSet::CONSTANT|null|string */
    public $text = FieldNotSet::CONSTANT;

    // /** @var int */
    // public $txttoall;

    // /** @var string */
    // public $text2;

    /** @var FieldNotSet::CONSTANT|null|string */
    public $var1 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var2 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var3 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var4 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var5 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var6 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var7 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var8 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var9 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var10 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var11 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var12 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var13 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var14 = FieldNotSet::CONSTANT;

    // /** @var FieldNotSet::CONSTANT|null|string */
    // public $var15 = FieldNotSet::CONSTANT;


}
