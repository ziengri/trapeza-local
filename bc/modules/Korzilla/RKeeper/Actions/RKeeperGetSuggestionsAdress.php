<?php

namespace App\modules\Korzilla\RKeeper\Actions;

use App\modules\Korzilla\RKeeper\Configs\RKeeperConfig;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperSendRequestTask;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRequestDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRestaurantDTO;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperAuthInput;
use Exception;

class RKeeperGetSuggestionsAdress
{

    private $getTokenSubAction;

    private $rkeeperSendRequestTask;


    public function __construct(RKeeperGetTokenSubAction $getTokenSubAction, RKeeperSendRequestTask $rkeeperSendRequestTask)
    {
        $this->getTokenSubAction = $getTokenSubAction;
        $this->rkeeperSendRequestTask = $rkeeperSendRequestTask;
    }

    /**
     * Undocumented function
     *
     * @param RKeeperAuthInput $authInput
     * @return RKeeperRestaurantDTO[]
     */
    public function run(RKeeperAuthInput $authInput)
    {
        return;
        // $token = $this->getTokenSubAction->run($authInput);

        // $getMenuRequest = new RKeeperRequestDTO();

        // $getMenuRequest->url = RKeeperConfig::BASE_URL . "/orderSources/" . $authInput->getClientId() . "/restaurants";
        // $getMenuRequest->method = "GET";
        // $getMenuRequest->headers = [
        //     "Authorization: Bearer " . $token
        // ];

        // $response = $this->rkeeperSendRequestTask->run($getMenuRequest);

        // //TODO ВЫНЕСТИ ЭТО НАФИГ
        // if ($response->statusCode != 200) {
        //     throw new Exception($response->body . $response->statusCode, 1);
        // }

        // $menu = json_decode($response->body, 1);
        // if (json_last_error() != JSON_ERROR_NONE) {
        //     throw new Exception("json_decode error", 1);
        // }

        // $resaurants = [];
        // foreach ($menu['result'] as $key => $resaurant) {
        //     $new = new RKeeperRestaurantDTO();
        //     $new->id = $resaurant['id'];
        //     $new->objectId = $resaurant['objectId'];
        //     $new->name = $resaurant['name'];
        //     $new->actualAddress = $resaurant['actualAddress'];
        //     $new->state = $resaurant['state'];
        //     $new->actualAddressLat = $resaurant['actualAddressLat'];
        //     $new->actualAddressLon = $resaurant['actualAddressLon'];
        //     $new->city = $resaurant['city'];
        //     $new->schedule = $resaurant['schedule'];
        //     $resaurants[] = $new;
        // }

        // return $resaurants;
    }
}


