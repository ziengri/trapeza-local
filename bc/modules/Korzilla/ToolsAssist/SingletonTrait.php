<?php

namespace App\modules\Korzilla\ToolsAssist;

trait SingletonTrait
{
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance ?? static::$instance = new static();
    }
}