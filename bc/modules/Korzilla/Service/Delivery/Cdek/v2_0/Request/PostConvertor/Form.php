<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Request\PostConvertor;

use App\modules\Korzilla\ToolsAssist\Request\PostConvertor\PostConvertorInterface;

class Form implements PostConvertorInterface
{
    public function convert(array $post): string
    {
        return http_build_query($post);
    }
}