<?php

namespace App\modules\Korzilla\Subdivision\Contracts;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;

interface SubdivisionCreateTaskContract
{

    /**
     * Создание раздела и инфоблока
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionSetOutput $parentSubdivision
     * @param int $catalogueId
     * @return SubdivisionSetOutput
     */
    public function run(SubdivisionSetInput $input,SubdivisionSetOutput $parentSubdivision,int $catalogueId) : SubdivisionSetOutput;
        
    
}
