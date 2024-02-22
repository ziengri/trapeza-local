<?php

namespace App\modules\Korzilla\Product\Models;

use App\modules\Ship\Parent\Models\Model;

class ProductModel extends Model
{

  /** @var int */
  public $Message_ID = null;

  /** @var int */ 
  public $User_ID = 0;

  /** @var int */
  public $Subdivision_ID;

  /** @var int */
  public $Sub_Class_ID;


  /** @var int */
  public $Catalogue_ID;

  /**
   * @var int 
   */
  public $Priority = 0;

  /** @var string|null */
  public $Keyword = null;

  /**
   * @var int 
   */
  public $Checked = 1;

  /** @var int */
  public $Parent_Message_ID = 0;

  //TODO ПЕРЕНЕСТИ СБОРКУ В ACTION или в test.php
  /**
   * @var \DateTime
   */
  public $Created ;

  /**
   * @var \DateTime
   */
  public $LastUpdated ;

  /** @var int */
  public $LastUser_ID = 0;


  /** @var string *-* */ 
  public $name;

  /** @var string|null */
  public $text = null;

  /**
   * @var float
   */
  public $price = 0;

  /** @var int */
  public $firstprice = 0;

  /** @var int */
  public $dogovor = 0;

  /** @var int */
  public $torg = 0;

  /** @var int */
  public $nocart = 0;

  /** @var int */
  public $stock = 0;

  /** @var int */
  public $discont = 0;

  /**
   * @var \DateTime|null
   */
  public $disconttime = null;

  /**
   * Работает только вместе с полем discounttime
   *  @var int */
  public $timer = 0;

  /** @var string|null */
  public $art = null;

  /** @var string|null  Код товара с выгрузки*/
  public $code = null;

  /** @var string|null */
  public $analog = null;

  /** @var int */
  public $buyvariable = 0;


  /** @var string|null */
  public $vendor = null;

  /** @var int|null */
  public $xlslist = null;

  /**
   * @var int 
   */ 
  public $fromxls = 0;

  /** @var int */
  public $notmarkup = 0;

  /** @var string|null */
  public $extlink = null;

  /** @var int */
  public $noorder = 0;

  /**
   * @var float|null
   */
  public $pricediscont = null;

  /** @var string|null */
  public $buywith = null;

  /** @var string|null */
  public $itemlabel = null;

  /**
   * @var string|null  
   */
  public $variablename = null;

  /** @var string|null */
  public $colors = null;

  /** @var int */
  public $buycolors = 0;

  /** @var int */
  public $spec = 0;

  /**
   * @var int  
   */
  public $new = 0;

  /** @var int */
  public $action = 0;

  /** @var string|null */
  public $citytarget = null;

  /** @var string|null */
  public $ves = null;

  /** @var string|null */
  public $capacity = null;

  /**
   * ?Возможно не используется
   *  @var string|null */
  public $sizes_item = null;

  /** @var string|null */
  public $edizm = null;

  /** @var string|null 
   * Код разделла из выгрузки
  */
  public $id1c = null;

  /** @var string|null */
  public $height = null;

  /** @var string|null */
  public $width = null;

  /** @var string|null */
  public $depth = null;

  /** @var
   * *Текстовое поле в БД
   * *Остальные var это string255
   *  string|null */
  public $var1 = null;

  /** @var string|null */
  public $var2 = null;

  /** 
   * @var string|null
   */
  public $var3 = null;

  /** @var int */
  public $oneitem = 0;

  /**
   * @var string|null 
   */
  public $var4 = null;

  /** @var string|null */
  public $descr = null;

  /**
   * * Строка через запятую
   *  @var string|null */
  public $tags = null;

  /**
   * @var float
   */
  public $price2 = 0;

  /**
   * @var float
   */
  public $price3 = 0;

  /** @var string|null */
  public $var5 = null;

  /** @var string|null */ 
  public $var6 = null;

  /** @var string|null */
  public $var7 = null;

  /** @var string|null */
  public $var8 = null;

  /** @var string|null */
  public $var9 = null;

  /** @var string|null */
  public $var10 = null;

  /** @var string|null */
  public $var11 = null;

  /** @var string|null */
  public $var12 = null;

  /** @var string|null */
  public $var13 = null;

  /** @var string|null */
  public $var14 = null;

  /** @var string|null */
  public $var15 = null;

  /** @var float */
  public $rate = 0;

  /** @var int */
  public $ratecount = 0;

  /**
   * @var int 
   */
  public $view = 0;

  /**
   * @var float
   */
  public $price4 = 0;

  /** @var int */
  public $stock2 = 0;

  /** @var int */
  public $stock3 = 0;

  /** @var int */
  public $stock4 = 0;

  /** @var string|null */
  public $art2 = null;

  /**
   * *Эталонные артикулы
   *  @var string|null */
  public $artnull = null;

  /** 
   * * Строка формата 
   *  id||value|\r\n
   * @var string|null */
  public $params = null;

  /**
   * Строка id через запятую
   *  @var string|null */
  public $Subdivision_IDS = null;

  /** @var string|null */
  public $h1 = null;

  /**
   * * Связанно с таблицей Classificator_currency по id
   *  @var int|null */
  public $currency = null;

  /** @var string|null */
  public $text2 = null;

  /** @var string|null */
  public $outItems = null;

  /** @var int|null
   * 0 или 1
   */
  public $variablenameSide = null;

  /** @var string|null */
  public $length = null;

  /**
   * *Строка id через запятую
   *  @var string|null */
  public $lang = null;

  /**
   * * UNIX TIME
   *  @var int|null */
  public $timestamp_export = null;

  /** @var string|null */
  public $import_source = null;

  /** @var string|null */
  public $keyword_find = null;

  /**
   * * Строка json
   *  @var string|null */
  public $order_count_price = null;

  /** @var string|null */
  public $analogs_new = null;

}