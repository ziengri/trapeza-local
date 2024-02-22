<?php




namespace App\modules\Korzilla\Payments\DolyameAPI\Requests;

use App\modules\Korzilla\Payments\DolyameAPI\Entities\ClientInfo;

class CreateOrderRequest extends AbstractOrderRequest
{
    // TODO: what is it?
    private $fiscalizationSettings = null;
    /**
     * @var \App\modules\Korzilla\Payments\DolyameAPI\Entities\ClientInfo|null
     */
    private $clientInfo = null;
    private $notificationURL = null;
    private $failURL;
    private $successURL;

    public function setFiscalizationSettings(array $fiscalizationSettings): CreateOrderRequest
    {
        $this->fiscalizationSettings = $fiscalizationSettings;
        return $this;
    }

    public function setClientInfo(ClientInfo $clientInfo): CreateOrderRequest
    {
        $this->clientInfo = $clientInfo;
        return $this;
    }

    public function setNotificationURL(string $notificationURL): CreateOrderRequest
    {
        $this->notificationURL = $notificationURL;
        return $this;
    }

    public function setFailURL(string $failURL): CreateOrderRequest
    {
        $this->failURL = $failURL;
        return $this;
    }

    public function setSuccessURL(string $successURL): CreateOrderRequest
    {
        $this->successURL = $successURL;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'order' => [
                'id' => $this->id,
                'amount' => number_format(floatval($this->amount), 2, '.', ''),
                'items' => $this->items->toArray(),
            ],
        ];

        if ($this->clientInfo !== null) {
            $data['client_info'] = $this->clientInfo->toArray();
        }

        if ($this->notificationURL !== null) {
            $data['notification_url'] = $this->notificationURL;
        }

        if ($this->failURL !== null) {
            $data['fail_url'] = $this->failURL;
        }

        if ($this->successURL !== null) {
            $data['success_url'] = $this->successURL;
        }

        return $data;

    }
}