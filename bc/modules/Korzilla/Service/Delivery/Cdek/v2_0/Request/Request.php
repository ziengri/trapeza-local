<?php 

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request;

use App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\Token\Token;
use App\modules\Korzilla\ToolsAssist\Request\RequestAbstract;
use Exception;

class Request extends RequestAbstract
{
    const API_URL = 'https://api.cdek.ru/v2/';
    const API_URL_TEST = 'https://api.edu.cdek.ru/v2/';

    public $withToken = true;

    protected function curlPrepare($curl): RequestAbstract
    {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        if ($this->withToken) {
            $token = Token::getInstance();
            switch ($token->getType()) {
                case 'bearer':
                    $this->addHeaders('Authorization: Bearer '.$token->getToken());
                    break;
                default:
                    throw new Exception(sprintf("Неизвестный тип токена: %s", $token->getType()));
            }
        }

        $this
            ->curlSetUrl($curl)
            ->curlSetHeaders($curl)
            ->curlSetPost($curl)
        ;

        return $this;
    }
}