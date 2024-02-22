<?php

namespace App\modules\Korzilla\Subdivision\Tasks;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionCreateTaskContract;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Models\SubClassModel;
use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Ship\Parent\Tasks\Task;
use Exception;

class SubdivisionCreateTask extends Task implements SubdivisionCreateTaskContract
{



    /** @var SubdivisionRepository */
    private $subdivisionRepository;

    /** @var SubClassRepository */
    private $subClassRepository;

    /** @var int */
    private $catalogueId;


    public function __construct($subdivisionRepository, $subClassRepository)
    {
        $this->subdivisionRepository = $subdivisionRepository;
        $this->subClassRepository = $subClassRepository;
    }


    /**
     * Создание раздела и инфоблока
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionDataDTO $parentSubdivision
     * @param int $catalogueId
     * @return SubdivisionDataDTO
     */
    public function run(SubdivisionSetInput $input,SubdivisionDataDTO $parentSubdivision,int $catalogueId): SubdivisionDataDTO{
        // echo "<br>CREATE<br>";
        $newSubdivisionModel = new SubdivisionModel();

        $newSubdivisionModel->Parent_Sub_ID= $parentSubdivision->Subdivision_ID;
        $newSubdivisionModel->Subdivision_Name= addslashes($input->Subdivision_Name);
        $newSubdivisionModel->Priority= $input->Priority;
        $newSubdivisionModel->Checked= $input->Checked;
        $newSubdivisionModel->EnglishName= $input->EnglishName;
        $newSubdivisionModel->Hidden_URL= $input->Hidden_URL;
        $newSubdivisionModel->Catalogue_ID= $catalogueId;

        $newSubdivisionModel->code1C= $input->id;
        $newSubdivisionModel->subdir= 1;

        if(!$this->subdivisionRepository->save($newSubdivisionModel)){
            throw new Exception($this->subdivisionRepository->getLastError(), 1);   
        };

        $newSubClass = new SubClassModel();
        $newSubClass->Subdivision_ID = $newSubdivisionModel->Subdivision_ID;
        $newSubClass->Class_ID = 2001;
        $newSubClass->Sub_Class_Name = $input->EnglishName;
        $newSubClass->EnglishName = $input->EnglishName;
        $newSubClass->Checked = 1;
        $newSubClass->Class_Template_ID = 0;
        $newSubClass->Catalogue_ID = $catalogueId;

        if(!$this->subClassRepository->save($newSubClass)){
            throw new Exception($this->subClassRepository->getLastError(), 1);                
        };
        return SubdivisionDataDTO::fromModel($newSubdivisionModel,$newSubClass->Sub_Class_ID);
    }
}
