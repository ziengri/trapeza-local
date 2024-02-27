<?php

namespace App\modules\Korzilla\Subdivision\Actions;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionCreateTaskContract;
use App\modules\Korzilla\Subdivision\Contracts\SubdivisionGetAllTaskContract;
use App\modules\Korzilla\Subdivision\Contracts\SubdivisionGetRootTaskContract;
use App\modules\Korzilla\Subdivision\Contracts\SubdivisionSortTaskContract;
use App\modules\Korzilla\Subdivision\Contracts\SubdivisionUpdateTaskContract;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionRootDTO;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;
use Exception;

class SubdivisionUploadAction
{


    /** @var \nc_Db*/
    private $db;

    /** @var int*/
    private $catalogueId;


    /** @var \nc_Core*/
    private $core;

    /**
     *  Раздел куда будет выгружаться новые разделы 
     *  @var SubdivisionSetOutput */
    private $rootSubdivision;

    /** @var SubdivisionModel[] */
    private $allSubdivision = [];

    /** 
     * [
     *  "{InputID - code1C}" => {SubdivisionSetOutput},
     * ]
     * 
     * @var SubdivisionSetOutput[] */
    private $subdivisionSetOutput = [];


    //*Репозитории
    /** @var SubdivisionRepository  */
    private $subdivisionRepository;

    /** @var SubClassRepository  */
    private $subClassRepository;

    //*Задачи
    /** @var SubdivisionGetAllTaskContract  */
    private $subdivisionGetAllTask;

    /** @var SubdivisionGetRootTaskContract  */
    private $subdivisionGetRootTask;

    /** @var SubdivisionSortTaskContract  */
    private $subdivisionSortTask;

    /** @var SubdivisionCreateTaskContract  */
    private $subdivisionCreateTask;

    /** @var SubdivisionUpdateTaskContract  */
    private $subdivisionUpdateTask;

    function __construct(
        \nc_Core $core,
        SubdivisionGetAllTaskContract $subdivisionGetAllTask,
        SubdivisionGetRootTaskContract $subdivisionGetRootTask,
        SubdivisionSortTaskContract $subdivisionSortTask,
        SubdivisionCreateTaskContract $subdivisionCreateTask,
        SubdivisionUpdateTaskContract $subdivisionUpdateTask
    ) {
        $this->core = $core;
        $this->db = $core->db;
        $this->catalogueId = $core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']))['Catalogue_ID'];


        //*Задачи
        $this->subdivisionGetAllTask =  $subdivisionGetAllTask;
        $this->subdivisionGetRootTask = $subdivisionGetRootTask;
        $this->subdivisionSortTask =    $subdivisionSortTask;
        $this->subdivisionCreateTask =  $subdivisionCreateTask;
        $this->subdivisionUpdateTask =  $subdivisionUpdateTask;
    }

    /**
     * Undocumented function
     *
     * @param SubdivisionSetInput[] $input -- массив разделов которые будут выгружаться
     * @param int|null $subdivisionId -- ID раздела куда будут выгружаться разделы
     * @return array{'внешний_ключ_раздела': SubdivisionSetOutput}
     */
    public function run(array $input, int $subdivisionId = null)
    {

        $this->allSubdivision = $this->subdivisionGetAllTask->run($this->catalogueId);

        $this->rootSubdivision = $this->subdivisionGetRootTask->run($this->catalogueId, $subdivisionId);

        $sortedSubdivisionSetInputs = $this->subdivisionSortTask->run($input);



        foreach ($sortedSubdivisionSetInputs as $subdivisionSetInput) {

            /** @var SubdivisionSetOutput $parentSubdivision */
            $parentSubdivision = $this->getParent($subdivisionSetInput);



            $subdivisionSetInput->Priority = $this->db->get_var("SELECT SQL_NO_CACHE MAX(Priority) FROM Subdivision WHERE Catalogue_ID = '{$this->catalogueId}' AND  Parent_Sub_ID = '{$parentSubdivision->Subdivision_ID}'") + 1;
            $subdivisionSetInput->EnglishName = encodestring($subdivisionSetInput->Subdivision_Name, 1);
            $subdivisionSetInput->Hidden_URL = $parentSubdivision->Hidden_URL . $subdivisionSetInput->EnglishName . "/";


            if (isset($this->allSubdivision[$subdivisionSetInput->id])) {
                //*UPDAT
                $this->subdivisionSetOutput[$subdivisionSetInput->id] = $this->subdivisionUpdateTask->run(
                    $subdivisionSetInput,
                    $parentSubdivision,
                    $this->allSubdivision[$subdivisionSetInput->id]
                );
            } else {
                //*CREATE
                $this->subdivisionSetOutput[$subdivisionSetInput->id] = $this->subdivisionCreateTask->run(
                    $subdivisionSetInput,
                    $parentSubdivision,
                    $this->catalogueId
                );
            }
        }

        return $this->subdivisionSetOutput;
    }



    private function getParent(SubdivisionSetInput $input)
    {
        if (isset($this->subdivisionSetOutput[$input->parentId])) {
            return $this->subdivisionSetOutput[$input->parentId];
        } else {
            return $this->rootSubdivision;
        }
    }
}
