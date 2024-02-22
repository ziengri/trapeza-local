<?php
namespace App\modules\Korzilla\Subdivision\Contracts;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;

interface SubdivisionGetAllTaskContract
{
  /**
     * @param int $catalogueId
     * @param string|null $typePrefix
     * @return SubdivisionModel[]|null
     */
    public function run(int $catalogueId, string $typePrefix = null):array;
}
