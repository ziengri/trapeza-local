<?php

namespace App\modules\Korzilla\Subdivision\Tasks;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionGetAllTaskContract;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Ship\Parent\Tasks\Task;

class SubdivisionGetAllTask extends Task implements SubdivisionGetAllTaskContract{


    private $repository; 

    public function __construct(SubdivisionRepository $repository){
        $this->repository = $repository;
    }

    /**
     * Undocumented function
     *
     * @param int $catalogueId
     * @param string|null $typePrefix
     * @return SubdivisionModel[]|null
     */
    public function run(int $catalogueId, string $typePrefix = null) :array
    {   

        $queryWhere = [];
        $queryWhere[]= ['Catalogue_ID', $catalogueId ,'='];
        if($typePrefix){
            $queryWhere[]= ['code1C', $typePrefix."%" ,'LIKE'];
        }
        $queryWhere[]= ['code1C', NULL ,'IS NOT '];
        $queryWhere[]= ['code1C', '' ,'!='];

        $subdivision=[];
        foreach ($this->repository->getAll($queryWhere) as $site) {
            $subdivision[$site->code1C] = $site;
        }
        


        return $subdivision;
    }
}