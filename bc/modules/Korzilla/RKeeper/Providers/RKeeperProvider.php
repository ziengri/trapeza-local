<?php

namespace App\modules\Korzilla\RKeeper\Providers;

use App\modules\Korzilla\Product\Providers\ProductProvider;
use App\modules\Korzilla\RKeeper\Actions\RKeeperBuildOrderInputAction;
use App\modules\Korzilla\RKeeper\Actions\RKeeperGetListRestaurantAction;
use App\modules\Korzilla\RKeeper\Actions\RKeeperGetMenuAction;
use App\modules\Korzilla\RKeeper\Actions\RKeeperGetTokenSubAction;
use App\modules\Korzilla\RKeeper\Data\Repositories\RKeeperRepository;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperSendRequestTask;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRestaurantDTO;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperAuthInput;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperOrderInput;
use App\modules\Ship\Parent\Providers\Provider;

class RKeeperProvider extends Provider
{

    private $setting;

    /** @var \nc_Core */
    private $nc_core;

    /** @var RKeeperAuthInput */
    private $auth;

    /** @var integer */
    private $catalogueId;

    private $clientId;

    private $clientSecret;

    public function __construct(\nc_Core $nc_core, array $setting, int $catalogueId)
    {
        $this->setting = $setting;
        $this->nc_core = $nc_core;
        $this->catalogueId = $catalogueId;

        //TODO! ДОСТАВИТЬ ИЗ settings
        $this->clientId = "3c1819c4-417a-4275-bff0-bc5c52efa1f9";
        $this->clientSecret = "ec3986c9-fb01-4686-a590-71091d4c41f5";
    }

    public function getMenu()
    {
        $rkRepository = new RKeeperRepository($this->nc_core->db);
        // var_dump($rkRepository);
        // die();
        $rkSendRequestTask = new RKeeperSendRequestTask();
        //TODO ! ДОСТАВАТЬ ИЗ settings
        $rkGetTokenSubAction = new RKeeperGetTokenSubAction(
            $rkRepository, $rkSendRequestTask, $this->clientId, $this->clientSecret,$this->catalogueId
        );

        $getMenuAction = new RKeeperGetMenuAction(
            $rkGetTokenSubAction,
            $rkSendRequestTask
        );

        return $getMenuAction->run();
    }
    public function getListRestaurantAction()
    {
        $rkRepository = new RKeeperRepository($this->nc_core->db);
        // var_dump($rkRepository);
        // die();
        $rkSendRequestTask = new RKeeperSendRequestTask();

        //TODO ! ДОСТАВАТЬ ИЗ settings
        $rkGetTokenSubAction = new RKeeperGetTokenSubAction(
            $rkRepository, $rkSendRequestTask, $this->clientId, $this->clientSecret,$this->catalogueId
        );
        $getListRestaurantAction = new RKeeperGetListRestaurantAction(
            $rkGetTokenSubAction,
            $rkSendRequestTask
        );

        return $getListRestaurantAction->run($this->clientId);
    }


    /**
     * Undocumented function
     * @param array{'totalsum':integer,'discont':integer,'totaldelsum':integer,'items':array,'totalSumDiscont':integer,'delivery':array{'name':string,'id':integer,'sum':float,},} $order
     * @param array $customf
     * @param integer $paymentType
     * @param RKeeperRestaurantDTO $restarans
     * @return RKeeperOrderInput
     */
    public function buildOrderInput(array $order, array $customf, int $paymentType, RKeeperRestaurantDTO $restarans): RKeeperOrderInput
    {
        $buildOrderInputAction = new RKeeperBuildOrderInputAction(
            new ProductProvider($this->nc_core, $this->setting),
            $this->catalogueId
        );

        return $buildOrderInputAction->run($order, $customf, $paymentType, $restarans);
    }



}
