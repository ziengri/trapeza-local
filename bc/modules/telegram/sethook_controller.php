<?php

require $_SERVER['DOCUMENT_ROOT']."/vars.inc.php";
require $_SERVER['DOCUMENT_ROOT']."/bc/connect_io.php";
require $_SERVER['DOCUMENT_ROOT']."/bc/modules/default/function.inc.php";

global $db;

$botSettings = new TelegramBots\Order\Provider\Settings();
$botAPI = new TelegramBotAPI\LongmanBot\BotAPI($botSettings);
$hookSetter = new TelegramBots\HookSetter($botAPI);

if ($hookSetter->setHook()) {
    $repo = new \TelegramBotRepository\RepositoryBase\Repository($botSettings, $db);
    if (!$repo->isRegistratedBot()) {
        $repo->registrateBot();
    }
    echo 'Хук создан';
} else {
    echo 'Не удалось создать хук';
}
