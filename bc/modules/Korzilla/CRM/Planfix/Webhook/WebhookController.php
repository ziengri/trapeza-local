<?php

namespace App\modules\Korzilla\CRM\Planfix\Webhook;

class WebhookController
{
    public function getCreateOrderLink(): string
    {   
        return sprintf('https://%s/webhook/json/%s', $this->getDomen(), $this->getCreateOrderHook());
    }

    public function getCreateFormLink(): string
    {   
        return sprintf('https://%s/webhook/json/%s', $this->getDomen(), $this->getCreateFormHook());
    }
    
    public function getDomen(): string
    {
        global $setting;
        return $setting['planfix_webhook_domen'] ?? '';
    }

    public function getCreateOrderHook(): string
    {
        global $setting;
        return $setting['planfix_webhook_create_order'] ?? '';
    }

    public function getCreateFormHook(): string
    {
        global $setting;
        return $setting['planfix_webhook_create_form'] ?? '';
    }
}