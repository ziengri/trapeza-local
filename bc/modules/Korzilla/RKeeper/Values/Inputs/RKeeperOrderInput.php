<?php

namespace App\modules\Korzilla\RKeeper\Values\Inputs;

use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperAddressDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperDishDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperGuestDTO;
use App\modules\Ship\Parent\Inputs\Input;

class RKeeperOrderInput extends Input
{
    /** @var string Комментарий к заказу */
    public $comment;

    /** @var string Идентификатор ресторана */
    public $restaurantId;

    /** @var string Количество персон */
    public $persons;

    /** @var RKeeperDishDTO[] Список блюд в заказе */
    public $dishList;

    /** @var string Тип доставки (delivery - доставка, pickup - самовывоз) */
    public $expeditionType;

    // /** @var string Ожидаемое время доставки в формате ISO 8601 */
    // public $expectedAt;

    /** @var bool Ожидаемое время доставки в формате ISO 8601 */
    public $soonest = true;

    /**
     * Идентификатор типа оплаты
     * "cash" - оплата наличными
     * "card" - оплата картой
     * "online" - онлайн оплата.
     *  @var string  */
    public $paymentTypeId;

    // /** @var float Сдача с указанной суммы */
    // public $changeFrom;

    /** @var RKeeperGuestDTO Информация о госте */
    public $guest;

    /** @var RKeeperAddressDTO Информация о госте */
    public $address;


    public function toArray(){

        $dishList = [];
        foreach ($this->dishList as $dish) {
            $dishList[] = $dish->toArray();
        }

        return [
            'comment' => $this->comment,
           'restaurantId' => $this->restaurantId,
            'persons' => $this->persons,
            'dishList' => $dishList,
            'expeditionType' => $this->expeditionType,
            'paymentTypeId' => $this->paymentTypeId,
            'guest' => $this->guest->toArray(),
            'address' => $this->address->toArray(),
            'soonest' => $this->soonest,

        ];
    }
}




