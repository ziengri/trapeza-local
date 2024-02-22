<?php

namespace TelegramBots\Order\Provider;

use \TelegramBots\BotSettingsInterface;

class Settings implements BotSettingsInterface
{
    const TELEGRAM_BOT_KEY_API = '2110718141:AAFvMnHBV5EbhAHlkqIf2mniMz9a28zlgyQ';
    const TELEGRAM_BOT_USER_NAME = 'korzilla_orders_bot';
    const TELEGRAM_BOT_HOOK_URL = 'https://krza.ru/telegram_hooks/order_provider_hook.php';

    const TOKEN = '@sCWSK5d!';

    public function getAPIKey(): string
    {
        return self::TELEGRAM_BOT_KEY_API;
    }

    public function getUserName(): string
    {
        return self::TELEGRAM_BOT_USER_NAME;
    }

    public function getHookUrl(): string
    {
        return self::TELEGRAM_BOT_HOOK_URL.'?token='.$this->getToken();
    }

    public function getToken(): string
    {
        return self::TOKEN;
    }
}

