<?php

namespace App\modules\Korzilla\CRM\Planfix\Request\PostConvertor;

use App\modules\Korzilla\ToolsAssist\Request\PostConvertor\PostConvertorInterface;

class JsonConvertor implements PostConvertorInterface
{
    public function convert(array $post): string
    {
        return json_encode($post);
    }
}