<?php

namespace App\modules\Korzilla\Subdivision\Values\DTO;

class SubdivisionUploadDataDTO
{
    /**
     * Наше поля в БД  - code1C
     *  @var string*/
    public $id;

    /**
     * Наше поля в БД  - Subdivision_Name
     *  @var string*/
    public $name;

    /** @var string*/
    public $parentId;
 
    /** @var int*/
    public $checked;

    /**
     * @var SubdivisionUploadDataDTO[]
     */
    public $children;
}
