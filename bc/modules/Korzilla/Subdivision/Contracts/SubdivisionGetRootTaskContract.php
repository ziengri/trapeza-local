<?php

namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;

interface SubdivisionGetRootTaskContract
{
    /**
     * Undocumented function
     *
     * @param int $catalogueId
     * @param string|null $typePrefix
     * @return SubdivisionDataDTO|null
     */
    public function run(int $catalogueId, int $subdivisionId = null): SubdivisionDataDTO;
}
