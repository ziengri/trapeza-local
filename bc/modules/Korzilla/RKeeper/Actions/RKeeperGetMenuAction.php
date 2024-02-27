<?php

namespace App\modules\Korzilla\RKeeper\Actions;

use App\modules\Korzilla\RKeeper\Configs\RKeeperConfig;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperSendRequestTask;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRequestDTO;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperAuthInput;
use App\modules\Korzilla\RKeeper\Values\Outputs\RKeeperMenuOutput;
use Exception;

class RKeeperGetMenuAction
{

    private $getTokenSubAction;

    private $rkeeperSendRequestTask;


    public function __construct(RKeeperGetTokenSubAction $getTokenSubAction, RKeeperSendRequestTask $rkeeperSendRequestTask )
    {
        $this->getTokenSubAction = $getTokenSubAction;
        $this->rkeeperSendRequestTask = $rkeeperSendRequestTask;
    }

    /**
     * @return array{'name': string, 'categories': array, 'products': array}
     */
    public function run() {
        $token = $this->getTokenSubAction->run();

        $getMenuRequest = new RKeeperRequestDTO();

        $getMenuRequest->url = RKeeperConfig::BASE_URL . RKeeperConfig::GET_MENU_ROUTE;
        $getMenuRequest->method = "GET";
        $getMenuRequest->headers = [
            "Authorization: Bearer ". $token
        ];
            
        $response = $this->rkeeperSendRequestTask->run($getMenuRequest);

        //TODO ВЫНЕСТИ ЭТО НАФИГ
        if ($response->statusCode!= 200) {
            throw new Exception($response->body . $response->statusCode, 1);
        }

        $menu = json_decode($response->body,1);
        if(json_last_error() != JSON_ERROR_NONE){
            throw new Exception("json_decode error", 1);
        }

        return $menu['result']; 
    }
}
