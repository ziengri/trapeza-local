<?php

namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;

interface SubdivisionUpdateTaskContract
{
    /**
     * Обновление раздела
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionSetOutput $parentSubdivision
     * @param SubdivisionModel $updateModel

     * @return SubdivisionSetOutput
     */
    public function run(SubdivisionSetInput $input,SubdivisionSetOutput $parentSubdivision,SubdivisionModel $updateModel): SubdivisionSetOutput;
}
