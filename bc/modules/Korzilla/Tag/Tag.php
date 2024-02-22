<?php

namespace App\modules\Korzilla\Tag;

class Tag
{
    /** @var int */
    public $Message_ID;
    /** @var string */
    public $tag;

    /**
     * @param string $tag
     * @param int $Message_ID
     */
    public function __construct($tag, $Message_ID = null)
    {
        $this->tag = $tag;
        $this->Message_ID = $Message_ID;
    }
}
