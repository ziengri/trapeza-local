<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Builders;

use App\modules\Korzilla\Payments\DolyameAPI\Entities\Item;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\ClientInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CreateOrderRequest;

class CreateOrderBuilder
{

    private $id;
    private $order;
    private $fio;
    private $phone;
    private $email;



    /**
     * @param int $id ИД заказа в БЛ
     * @param array $order Позиции заказа в виде массива
     * @param string $f_fio
     * @param string $f_phone
     * @param string $f_email
     */
    public function __construct(
        int $id,
        array $order,
        string $f_fio,
        string $f_phone,
        string $f_email
    ) {
        $this->id = $id;
        $this->order = $order;
        $this->fio = explode(" ", $f_fio);
        $this->phone = $f_phone;
        $this->email = $f_email;
    }


    public function build(): CreateOrderRequest
    {

        $orderRequest = new CreateOrderRequest($this->id, $this->order['totaldelsum'], $this->getOrderItems());
        $orderRequest->setClientInfo($this->getClientInfo());
        $hostUrl = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $orderRequest->setNotificationURL($hostUrl . "/bc/modules/Korzilla/Payments/DolyameAPI/WebHooks/WebhookReceiver.php");
        $orderRequest->setFailURL($hostUrl);
        $orderRequest->setSuccessURL($hostUrl);
        return $orderRequest;
    }

    private function getOrderItems(): OrderItems
    {

        $orderItems = new OrderItems();

        foreach ($this->order['items'] as $item) {
            $item = new Item($item['name'], $item['count'], number_format($item['price'], 2, '.', ''), $item['art']);
            $orderItems->addItem($item);
        }

        return $orderItems;
    }

    private function getClientInfo(): ClientInfo
    {
        $clientInfo = new ClientInfo();



        $clientInfo->setFirstName($this->fio[0] ?: '');
        $clientInfo->setLastName($this->fio[2] ?: '');
        $clientInfo->setMiddleName($this->fio[1] ?: '');
        $clientInfo->setEmail($this->email);
        $clientInfo->setPhone($this->phone);

        return $clientInfo;
    }
}






