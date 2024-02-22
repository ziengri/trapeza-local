<?php

namespace TelegramBotAPI\LongmanBot;

use \TelegramBots\BotSettingsInterface;
use Longman\TelegramBot\Request;

class BotAPI implements \TelegramBotAPI\BotAPIInterface
{
    /**
     * @var \TelegramBots\BotSettingsInterface
     */
    private $botSettings;
    /**
     * @var \Longman\TelegramBot\Telegram
     */
    private $telegram;

    public function __construct(\TelegramBots\BotSettingsInterface $settings)
    {
        $this->botSettings = $settings;
        $this->setBot();
    }

    public function getBotSettings(): \TelegramBots\BotSettingsInterface
    {
        return $this->botSettings;
    }   

    public function setWebHook(string $hookUrl)
    {
        try {
            $result = $this->telegram->setWebhook($hookUrl);
            if ($result->isOk()) {
                return true;
            }
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            return false;
        }
    }

    public function getInput()
    {
        return Request::getInput();
    }

    public function sendMessage($chatID, $message)
    {
        Request::sendMessage([
            'chat_id' => $chatID,
            'text'    => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    private function setBot()
    {
        $this->telegram = new \Longman\TelegramBot\Telegram($this->botSettings->getAPIKey(), $this->botSettings->getUserName());
    }
}