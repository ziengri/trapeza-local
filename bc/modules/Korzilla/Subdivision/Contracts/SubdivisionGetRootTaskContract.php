<?php

namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;

interface SubdivisionGetRootTaskContract
{
    /**
     * Undocumented function
     *
     * @param int $catalogueId
     * @param string|null $typePrefix
     * @return SubdivisionSetOutput|null
     */
    public function run(int $catalogueId, int $subdivisionId = null): SubdivisionSetOutput;
}
