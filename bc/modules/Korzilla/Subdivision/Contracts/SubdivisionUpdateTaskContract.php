<?php

namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;

interface SubdivisionUpdateTaskContract
{
    /**
     * Обновление раздела
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionDataDTO $parentSubdivision
     * @param SubdivisionModel $updateModel

     * @return SubdivisionDataDTO
     */
    public function run(SubdivisionSetInput $input,SubdivisionDataDTO $parentSubdivision,SubdivisionModel $updateModel): SubdivisionDataDTO;
}
