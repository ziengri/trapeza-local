<?php

namespace App\modules\Korzilla\RKeeper\Tasks;

use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperRequestDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperResponseDTO;


class RKeeperSendRequestTask
{

    /**
     * @param RKeeperRequestDTO $request
     * @return RKeeperResponseDTO
     */
    public function run(RKeeperRequestDTO $request): RKeeperResponseDTO
    {

        $ch = curl_init($request->url);
        curl_setopt_array(
            $ch,
            [
                CURLOPT_CUSTOMREQUEST => $request->method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $request->body,
                CURLOPT_HTTPHEADER => $request->headers,
            ]
        );
        $response = new RKeeperResponseDTO();
        $response->body = curl_exec($ch);
        $response->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);


        return $response;

    }
}
