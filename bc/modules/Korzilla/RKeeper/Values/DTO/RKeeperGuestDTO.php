<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperGuestDTO
{
   /** @var string Email гостя */
   public $email;

   /** @var int Телефон гостя */
   public $phone;

   /** @var string Имя гостя */
   public $firstName;

   public function toArray() : array {
      $array = [];
      foreach ($this as $key => $value) {
          $array[$key] = $value;
      }
      return $array;
  }
}
