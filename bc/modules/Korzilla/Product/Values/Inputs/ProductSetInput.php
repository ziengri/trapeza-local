<?php

namespace App\modules\Korzilla\Product\Values\Inputs;

use App\modules\Ship\Helpers\FieldNotSet;
use App\modules\Ship\Parent\Inputs\Input;

class ProductSetInput extends Input
{

    /** @var string */
    public $Created;

    /** @var int */
    public $User_ID = 0;

    /** @var int */
    public $Catalogue_ID;

    /** @var int */
    public $Subdivision_ID;

    /** @var int */
    public $Sub_Class_ID;

    /** @var int */
    public $Checked = 1;

    /** @var string */
    public $Keyword;

    /** @var string */
    public $name;


    /**
     * * UNIX TIME
     *  @var int */
    public $timestamp_export;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $text = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null 
     * Код разделла из выгрузки
     */
    public $id1c = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null  Код товара с выгрузки*/
    public $code = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $vendor = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $art = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|float */
    public $price = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|float */
    public $price2 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|float */
    public $price3 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|float */
    public $price4 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int */
    public $action = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int */
    public $new = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $stock = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $stock2 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $stock3 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $stock4 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $analog = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $itemlabel = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $ves = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $currency = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $edizm = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $tags = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $art2 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $artnull = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $colors = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $variablename = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|int|null */
    public $discont = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $disconttime = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var1 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var2 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var3 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var4 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var5 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var6 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var7 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var8 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var9 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var10 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var11 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var12 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var13 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var14 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $var15 = FieldNotSet::CONSTANT;

    /** @var FieldNotSet::CONSTANT|string|null */
    public $params = FieldNotSet::CONSTANT;

}