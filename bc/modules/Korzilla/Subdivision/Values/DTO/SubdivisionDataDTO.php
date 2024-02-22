<?php

namespace App\modules\Korzilla\Subdivision\Values\DTO;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;

class SubdivisionDataDTO extends SubdivisionModel
{

    /** @var int|null */
    public $subclassId = null; 

    public static function fromModel(SubdivisionModel $model,int $subClassId = null): self
    {
        $self= new self();

        foreach ($model as $key => $value) {
            $self->$key = $value;
        }
        $self->subclassId = $subClassId;
        return $self;
    }
}

