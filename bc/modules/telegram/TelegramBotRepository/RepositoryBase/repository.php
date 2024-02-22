<?php

namespace TelegramBotRepository\RepositoryBase;

class Repository implements \TelegramBotRepository\RepositoryInterface
{
    const CHAT_BOT_TABLE = 'Message2246';
    const CHAT_BOT_FIELD_NAME = 'user_name';
    const CHAT_BOT_FIELD_ID = 'Message_ID';

    const CHAT_TABLE = 'Message2247';
    const CHAT_FIELD_ID = 'chat_id';
    const CHAT_FIELD_BOT_ID = 'bot_id';
    const CHAT_FIELD_SITE_KEY = 'Catalogue_ID';

    private $db;
    private $botID;
    private $botSettings;

    public function __construct(\TelegramBots\BotSettingsInterface $botSetting, $db)
    {
        $this->db = $db;
        $this->botSettings = $botSetting;
    }

    public function reistrateChat($chatID, $catalogueID)
    {
        try {
            if ($this->isRegistratedChat($chatID, $catalogueID)) {
                return true;
            }

            if (!$this->isRegistratedBot()) {
                $this->registrateBot();
            }
            
            $table = self::CHAT_TABLE;
            $fieldID = self::CHAT_FIELD_ID;
            $fieldBotID = self::CHAT_FIELD_BOT_ID;
            $fieldSiteKey = self::CHAT_FIELD_SITE_KEY;

            $chatID = $this->db->escape($chatID);
            $botID = $this->db->escape($this->getBotID());
            $catalogueID = $this->db->escape($catalogueID);
            
            $sql = "INSERT INTO {$table} (`{$fieldID}`, `{$fieldBotID}`, `{$fieldSiteKey}`) 
                    VALUES ('{$chatID}', '{$botID}', '{$catalogueID}')";

            $this->db->query($sql);

            if ($this->db->is_error) {
                throw new \Exception("Неудалось зарегестрировать чат.\nСообщение от базы данных: {$this->db->errno}.\nSQL-запрос: {$sql}");
            }
        } catch (\Exception $e) {
            $this->regError($e);
            return false;
        }

        return true;
    }

    public function isRegistratedChat($chatID, $catalogueID): bool
    {
        $table = self::CHAT_TABLE;
        $fieldID = self::CHAT_FIELD_ID;
        $filedSiteKey = self::CHAT_FIELD_SITE_KEY;

        $chatID = $this->db->escape($chatID);
        $catalogueID = $this->db->escape($chatID);

        return (bool) $this->db->get_var("SELECT count(*) FROM {$table} WHERE `{$fieldID}` = '{$chatID}' AND `{$filedSiteKey}` = {$catalogueID}");
    }

    public function isRegistratedBot(): bool
    {
        $table = self::CHAT_BOT_TABLE;
        $fieldName = self::CHAT_BOT_FIELD_NAME;
        $name = $this->db->escape($this->botSettings->getUserName());

        return (bool) $this->db->get_var("SELECT count(*) FROM {$table} WHERE `{$fieldName}` = '{$name}'");
    }

    public function registrateBot()
    {
        $table = self::CHAT_BOT_TABLE;
        $fieldName = self::CHAT_BOT_FIELD_NAME;
        $name = $this->db->escape($this->botSettings->getUserName());
        
        $sql = "INSERT INTO {$table} (`{$fieldName}`) VALUES ('{$name}')";

        $this->db->query($sql);

        if ($this->db->is_error) {
            throw new \Exception("Неудалось зарегестрировать бота.\nСообщение от базы данных: {$this->db->errno}.\nSQL-запрос: {$sql}");
        }
    }

    public function getBotID(): int
    {
        if (!isset($this->botID)) {

            $table = self::CHAT_BOT_TABLE;
            $fieldName = self::CHAT_BOT_FIELD_NAME;
            $fieldID = self::CHAT_BOT_FIELD_ID;

            $name = $this->db->escape($this->botSettings->getUserName());

            $sql = "SELECT `{$fieldID}` FROM {$table} WHERE `{$fieldName}` = '{$name}'";

            $id = $this->db->get_var($sql);
            if ($this->db->is_error || !is_numeric($id)) {
                throw new \Exception("Неудалось получить id бота.\nid: {$id}\nСообщение от базы данных: {$this->db->errno}.\nSQL-запрос: {$sql}");
            } else {
                $this->botID = (int) $id;
            }
        }
        return $this->botID;
    }

    private function regError(\Exception $e)
    {
        file_put_contents(__DIR__.'/error_log.txt', "\n\n".$e->getMessage(), FILE_APPEND);
    }
}