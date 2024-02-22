<?php

namespace TelegramBots;

interface BotSettingsInterface
{
    public function getAPIKey(): string;
    public function getUserName(): string;
    public function getHookUrl(): string;
}
