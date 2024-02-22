<?php

namespace App\modules\Korzilla\Subdivision\Models;
use App\modules\Ship\Parent\Models\Model;

class SubClassModel extends Model
{

    /** @var int|null */
    public $Sub_Class_ID = null;

    /** @var int */
    public $Subdivision_ID;
  
    /** @var int */
    public $Class_ID;
  
    /** @var string */
    public $Sub_Class_Name;
  
    /** @var string */
    public $EnglishName;
  
    /** @var int */
    public $Checked;
  
    /** @var int */
    public $Catalogue_ID;

    /** @var int */
    public $AllowTags = -1;
  
    /** @var int */
    public $CacheForUser = -1;

    /** @var string */
    public $DefaultAction = "index";
  
    /** @var int */
    public $NL2BR = -1;
  
    /** @var int */
    public $UseCaptcha = -1;

    /** @var int */
    public $Class_Template_ID;
  

}