<?php

namespace TelegramBots;

interface HookInterface
{
    /**
     * метод тригерящй вызов дальнейших инструкций
     * 
     * @param array $data массив полученных данных при выхове хука
     */
    public function handler(array $data);
}
