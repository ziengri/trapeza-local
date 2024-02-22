<?php

namespace App\modules\Korzilla\Tag\Bind;

class Bind
{
    /** @var int */
    public $Message_ID;
    /** @var int */
    public $tag_id;
    /** @var int */
    public $object_id;
    /** @var string */
    public $object_type;
    
    /**
     * @param int $tag_id
     * @param int $object_id
     * @param string $object_type
     * @param int|null $Message_ID
     */
    public function __construct($tag_id, $object_id, $object_type, $Message_ID = null)
    {
        $this->tag_id = $tag_id;
        $this->object_id = $object_id;
        $this->object_type = $object_type;
        $this->Message_ID = $Message_ID;
    }
}