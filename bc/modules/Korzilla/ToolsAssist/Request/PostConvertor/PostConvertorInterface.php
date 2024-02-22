<?php

namespace App\modules\Korzilla\ToolsAssist\Request\PostConvertor;

use App\modules\Korzilla\ToolsAssist\Request\PostRequestInterface;

interface PostConvertorInterface
{
    public function convert(array $post): string;
}

