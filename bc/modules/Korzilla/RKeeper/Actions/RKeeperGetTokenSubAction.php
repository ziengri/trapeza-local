<?php

namespace App\modules\Korzilla\RKeeper\Actions;

use App\modules\Korzilla\RKeeper\Configs\RKeeperConfig;
use App\modules\Korzilla\RKeeper\Data\Repositories\RKeeperRepository;
use App\modules\Korzilla\RKeeper\Models\RKeeperModel;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperSendRequestTask;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRequestDTO;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperAuthInput;
use Exception;

class RKeeperGetTokenSubAction
{
    private $rkeeperRepository;

    private $rkeeperSendRequestTask;

    private $clientSecret;

    private $clientId;

    private $catalogueId;
    public function __construct(
        
        RKeeperRepository $rkeeperRepository,
        RKeeperSendRequestTask $rkeeperSendRequestTask,
        string $clientId,
        string $clientSecret,
        int $catalogueId

        ) {
        $this->rkeeperRepository = $rkeeperRepository;
        $this->rkeeperSendRequestTask = $rkeeperSendRequestTask;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->catalogueId = $catalogueId;

    }

    /**
     * Undocumented function
     *
     * @param integer $catalogueId
     * @param string $clientId
     * @param string $clientSecret
     * @return string
     */
    public function run() :string
    {   
        $query = [
            ['Catalogue_ID', $this->catalogueId ,'=']
        ];

        /** @var RKeeperModel $rkeeper */
        $rkeeper = $this->rkeeperRepository->getRow($query);
        
        if ($rkeeper != null && $rkeeper->expires_at > time() )  {
            return $rkeeper->token;
        }

        if ($rkeeper == null )  {
            $rkeeper = new RKeeperModel();
            $rkeeper->Catalogue_ID = $this->catalogueId;
        }


        $getTokenRequest = new RKeeperRequestDTO();
        $getTokenRequest->url = RKeeperConfig::GET_TOKEN_URL;
        $getTokenRequest->method = "POST";
        $getTokenRequest->body = http_build_query(['client_Id' => $this->clientId, 'client_secret' => $this->clientSecret, 'grant_type' => "client_credentials", 'scopes' => "orders"], '', '&');
        $getTokenRequest->headers = [
            "Content-Type: application/x-www-form-urlencoded"
        ];
        

        $response = $this->rkeeperSendRequestTask->run($getTokenRequest);

        if ($response->statusCode!= 200) {
            var_dump(['client_Id' => $this->clientId, 'client_secret' => $this->clientSecret, 'grant_type' => "client_credentials", 'scopes' => "orders"]);
            var_dump($response->body);
            throw new Exception("statusCode!= 200", 1);
        }

        $token = json_decode($response->body, true);
        if(json_last_error() != JSON_ERROR_NONE){
            throw new Exception("json_decode error", 1);
        }

        $rkeeper->token = $token['access_token'];
        $rkeeper->expires_at = time() + $token['expires_in'];
        $this->rkeeperRepository->save($rkeeper);
        
        return $rkeeper->token;
        
        
    }

}


