<?php

namespace App\modules\Korzilla\CRM\Frontpad\Request;

use App\modules\Korzilla\ToolsAssist\Request\PostConvertor\PostConvertorInterface;

class PostConvertor implements PostConvertorInterface
{
    public function convert(array $post): string
    {
        return http_build_query($post);
    }
}