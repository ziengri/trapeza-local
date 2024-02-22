<?php

namespace App\modules\Korzilla\Subdivision\Contracts;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;

interface SubdivisionCreateTaskContract
{

    /**
     * Создание раздела и инфоблока
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionDataDTO $parentSubdivision
     * @param int $catalogueId
     * @return SubdivisionDataDTO
     */
    public function run(SubdivisionSetInput $input,SubdivisionDataDTO $parentSubdivision,int $catalogueId) : SubdivisionDataDTO;
        
    
}
