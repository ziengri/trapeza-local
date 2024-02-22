<?php

namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;

interface SubdivisionSortTaskContract
{
    /**
     * Undocumented function
     *
     * @param SubdivisionSetInput[] $input
     * @return SubdivisionSetInput[]
     */
    public function run(array $input) : array;
}
