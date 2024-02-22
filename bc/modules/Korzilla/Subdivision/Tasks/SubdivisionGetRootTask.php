<?php

namespace App\modules\Korzilla\Subdivision\Tasks;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionGetRootTaskContract;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Models\SubClassModel;
use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;
use App\modules\Ship\Parent\Tasks\Task;

class SubdivisionGetRootTask extends Task implements SubdivisionGetRootTaskContract
{

    private $SubClassRepository;
    private $SubdivisionRepository;

    public function __construct(SubdivisionRepository $SubdivisionRepository, SubClassRepository $SubClassRepository)
    {

        $this->SubdivisionRepository = $SubdivisionRepository;

        $this->SubClassRepository = $SubClassRepository;
    }

    /**
     * Undocumented function
     *
     * @param integer $catalogueId
     * @param string|null $typePrefix
     * @return SubdivisionDataDTO|null
     */
    public function run(int $catalogueId, int $subdivisionId = null) : SubdivisionDataDTO
    {
        $queryWhere = [];

        if ($subdivisionId === null) {
            $queryWhere[] = ['Catalogue_ID', $catalogueId, '='];
            $queryWhere[] = ['Hidden_URL', "/catalog/", '='];
        } else {
            $queryWhere[] = ['Catalogue_ID', $catalogueId, '='];
            $queryWhere[] = ['Subdivision_ID', $subdivisionId, '='];
        }

        /** @var SubdivisionModel $subDivision*/
        $subDivision = $this->SubdivisionRepository->getRow($queryWhere);

        if (!$subDivision) {
            throw new \Exception("Родительская директория не найдена");
        }

        $queryWhere = [];
        $queryWhere[] = ['Subdivision_ID', $subDivision->Subdivision_ID, '='];
        $queryWhere[] = ['Class_ID', 2001, '='];

        /**  @var SubClassModel $subClass*/
        $subClass = $this->SubClassRepository->getRow($queryWhere);
        if (!$subClass) {
            throw new \Exception("Родительский инфоблок не найден");
        }

        return SubdivisionDataDTO::fromModel($subDivision, $subClass->Sub_Class_ID);


    }
}

