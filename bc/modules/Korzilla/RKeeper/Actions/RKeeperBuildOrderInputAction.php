<?php

namespace App\modules\Korzilla\RKeeper\Actions;

use App\modules\Korzilla\Product\Providers\ProductProvider;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperAddressDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperDishDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperGuestDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRestaurantDTO;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperOrderInput;
use nc_Core;

class RKeeperBuildOrderInputAction
{   
    private $productProvider;

    private $catalogueId;

    public function __construct(ProductProvider $productProvider, int $catalogueId)
    {
        $this->productProvider = $productProvider;
        $this->catalogueId = $catalogueId;
    }

    /**
     * Undocumented function
     * @param array{'totalsum':integer,'discont':integer,'totaldelsum':integer,'items':array,'totalSumDiscont':integer,'delivery':array{'name':string,'id':integer,'sum':float,},} $order
     * @param array $customf
     * @param integer $paymentType
     * @param RKeeperRestaurantDTO $restarans
     * @return RKeeperOrderInput
     */
    public function run(array $order, array $customf, int $paymentType, RKeeperRestaurantDTO $restarans): RKeeperOrderInput
    {
        echo "<pre>";


        $dishList = [];
        foreach ($order['items'] as $item) {
            $product = $this->productProvider->getById($item['id'], $this->catalogueId);
            if (!$product) {
                continue;
            }
            $dish = new RKeeperDishDTO();
            $dish->id = $product->code;
            $dish->name = $product->name;
            $dish->price = $item['price'];
            $dish->quantity = $item['count'];

            $dishList[] = $dish;
        }


        $guest = new RKeeperGuestDTO();
        $guest->firstName = trim(explode(" ", $customf['name']['value'])[0]);
        $guest->phone = str_replace([" ", "-", "+",], "", $customf['phone']['value']);
        $guest->email = $customf['email']['value'];

        $address = new RKeeperAddressDTO();
        $address->cityName = trim($customf['city']['value']);
        $address->street = trim($customf['street']['value']);
        $address->houseNumber = trim($customf['houseNumber']['value']);
        $address->apartmentNumber = trim($customf['apartmentNumber']['value']);
        $address->floor = trim($customf['floor']['value']);
        $address->intercom = trim($customf['intercom']['value']);
        $address->fullAddress = "г ". $address->cityName
            . ($address->street ? ", ул " . $address->street : "")
            . ($address->houseNumber ? ", д " . $address->houseNumber : "")
            . ($address->apartmentNumber ? ", " . $address->apartmentNumber : "");


        $orderInput = new RKeeperOrderInput();
        $orderInput->comment = trim($customf['comments']['value']);
        
        $orderInput->restaurantId = $restarans->id;
        $orderInput->persons = (int) trim($customf['persons']['value']);

        switch ($order['delivery']['id']) {
            case 1:
                $orderInput->expeditionType = "pickup";
                break;
            default:
                $orderInput->expeditionType = "delivery";
                break;
        }

        switch ($paymentType) {
            case 2:
                $orderInput->paymentTypeId = "online";
                break;
            case 3:
                $orderInput->paymentTypeId = "cash";
                break;
            case 4:
                $orderInput->paymentTypeId = "card";
                break;
            default:
                $orderInput->paymentTypeId = "cash";
                break;
        }



        $orderInput->guest = $guest;
        $orderInput->address = $address;
        $orderInput->dishList = $dishList;

        return $orderInput;

    }
}
