<?php

namespace App\modules\Korzilla\Subdivision\Values\Outputs;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use Exception;

class SubdivisionSetOutput extends SubdivisionModel
{   
    /** @var int */
    public $subClassId; 

    //? Может ли быть null?
    public static function fromModel(SubdivisionModel $model,int $subClassId): self
    {
        $self= new self();

        foreach ($model as $key => $value) {
            $self->$key = $value;
        }
        if (!$subClassId) {
            throw new Exception("subClassId can`t be null in SubdivisionSetOutput", 1);
            
        }
        $self->subClassId = $subClassId;
        return $self;
    }
}
