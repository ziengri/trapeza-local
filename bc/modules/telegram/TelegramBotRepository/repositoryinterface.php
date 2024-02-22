<?php

namespace TelegramBotRepository;

interface RepositoryInterface
{
    public function reistrateChat($chatID, $catalogueID);
}