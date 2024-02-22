<?php

namespace App\modules\Korzilla\Subdivision\Models;

use App\modules\Ship\Parent\Models\Model;

class SubdivisionModel extends Model{

  /** 
   * 
   * @var int */
  public $Subdivision_ID = null;
  /** 
   * 
   * @var int */  
  public $Catalogue_ID;

  /** 
   * 
   * @var int */
  public $Parent_Sub_ID;

  /** @var string */
  public $Subdivision_Name;

  /** @var string */
  public $EnglishName;

  /** @var string */
  public $Hidden_URL;

  /** @var int */
  public $Priority;

  /** @var int */
  public $Checked;


  /** @var int */
  public $subdir;

  /** @var string */
  public $code1C;


}