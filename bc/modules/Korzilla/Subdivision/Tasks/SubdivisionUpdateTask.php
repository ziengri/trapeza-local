<?php

namespace App\modules\Korzilla\Subdivision\Tasks;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionUpdateTaskContract;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Models\SubClassModel;
use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;
use App\modules\Ship\Parent\Tasks\Task;
use Exception;

class SubdivisionUpdateTask extends Task implements SubdivisionUpdateTaskContract
{

    /** @var SubdivisionRepository */
    private $subdivisionRepository;


    /** @var SubClassRepository */
    private $subClassRepository;


    public function __construct(SubdivisionRepository $subdivisionRepository, SubClassRepository $subClassRepository)
    {
        $this->subdivisionRepository = $subdivisionRepository;
        $this->subClassRepository = $subClassRepository;

        
    }

    /**
     * Обновление раздела
     *
     * @param SubdivisionSetInput $input
     * @param SubdivisionSetOutput $parentSubdivision
     * @param SubdivisionModel $updateModel

     * @return SubdivisionSetOutput
     */
    public function run(SubdivisionSetInput $input,SubdivisionSetOutput $parentSubdivision,SubdivisionModel $updateModel) : SubdivisionSetOutput{
        // echo "<br>UPDATE<br>";
        
        $updateModel->Parent_Sub_ID= $parentSubdivision->Subdivision_ID;
        $updateModel->Subdivision_Name = $input->Subdivision_Name;
        $updateModel->EnglishName = $input->EnglishName;
        $updateModel->Hidden_URL= $input->Hidden_URL;
        $updateModel->Checked = $input->Checked;
        $updateModel->Priority= $input->Priority;

        $queryWhere =[];
        $queryWhere[] = ['Subdivision_ID', $updateModel->Subdivision_ID, '='];
        $queryWhere[] = ['Class_ID', "2001", '='];


        /** @var SubClassModel $subClassModel */
        $subClassModel = $this->subClassRepository->getRow($queryWhere);
        if(empty($subClassModel)){
            throw new Exception("НЕ ДОЛЖЕН БЫТЬ ПУСТОЙ ОШИБКА", 1);
            
        }


        if(!$this->subdivisionRepository->save($updateModel)){
            throw new Exception($this->subdivisionRepository->getLastError(), 1);                
        };

        return SubdivisionSetOutput::fromModel($updateModel,$subClassModel->Sub_Class_ID);
    }
}
