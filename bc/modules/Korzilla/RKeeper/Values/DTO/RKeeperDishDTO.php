<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperDishDTO
{
   /** @var string Идентификатор блюда */
   public $id;

   /** @var string Название блюда */
   public $name;

   /** @var float Цена блюда */
   public $price;

   // /** @var array{'value':float,'unit':string} Информация о мере блюда */
   // public $measure;

   /** @var int Количество порций блюда */
   public $quantity;

   public function toArray() : array {
      $array = [];
      foreach ($this as $key => $value) {
          $array[$key] = $value;
      }
      return $array;
  }
}
