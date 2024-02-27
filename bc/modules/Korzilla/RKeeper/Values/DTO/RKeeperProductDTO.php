<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperProductDTO
{
   /** @var string Уникальный идентификатор продукта */
   public $id;

   /** @var string Внешний идентификатор продукта */
   public $externalId;

   /** @var string Идентификатор категории, к которой относится продукт */
   public $categoryId;

   /** @var string Название продукта */
   public $name;

   /** @var mixed Дополнительные данные о ресторане, связанные с продуктом (может быть null) */
   public $restaurantExtendDatas;

   /** @var float Цена продукта */
   public $price;

   /** @var string|null Идентификатор схемы, к которой относится продукт (может быть null) */
   public $schemeId;

   /** @var bool Флаг, указывающий, является ли продукт неактивным */
   public $disabled;

   /** @var string Описание продукта */
   public $description;

   /** @var array URL-адреса изображений, связанных с продуктом */
   public $imageUrls;

   /** @var string|null Единица измерения продукта (может быть null) */
   public $measure;

   /** @var bool|null Флаг, указывающий, содержится ли продукт в стоп-листе (может быть null) */
   public $isContainInStopList;

   /** @var bool|null Флаг, указывающий, остановлен ли список, содержащий этот продукт (может быть null) */
   public $isListStoped;

   /** @var float|null Калорийность продукта (может быть null) */
   public $calories;

   /** @var float|null Энергетическая ценность продукта (может быть null) */
   public $energyValue;

   /** @var float|null Количество белков в продукте (может быть null) */
   public $proteins;

   /** @var float|null Количество жиров в продукте (может быть null) */
   public $fats;

   /** @var float|null Количество углеводов в продукте (может быть null) */
   public $carbohydrates;

   /** @var float|null Акциз на продукт (может быть null) */
   public $excise;

   /** @var array|null Глобальные торговые идентификационные номера продукта (может быть null) */
   public $globalTradeItemNumbers;
}
