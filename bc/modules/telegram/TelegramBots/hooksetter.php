<?php

namespace TelegramBots;

class HookSetter
{
    /**
     * @var \TelegramBotAPI\BotAPIInterface
     */
    private $botApi;

    public function __construct(\TelegramBotAPI\BotAPIInterface $botApi)
    {
        $this->botApi = $botApi;
    }

    public function setHook(): bool
    {
        return $this->botApi->setWebHook($this->botApi->getBotSettings()->getHookUrl());
    }
}