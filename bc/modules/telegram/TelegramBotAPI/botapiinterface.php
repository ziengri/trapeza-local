<?php

namespace TelegramBotAPI;

interface BotAPIInterface
{
    public function getBotSettings(): \TelegramBots\BotSettingsInterface;
    public function setWebHook(string $hookUrl);
}