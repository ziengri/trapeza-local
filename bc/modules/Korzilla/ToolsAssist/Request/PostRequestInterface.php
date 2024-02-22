<?php

namespace App\modules\Korzilla\ToolsAssist\Request;

interface PostRequestInterface
{
    /**
     * Добавить POST параметр в запрос
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return self
     */
    public function addPost($key, $value);
}