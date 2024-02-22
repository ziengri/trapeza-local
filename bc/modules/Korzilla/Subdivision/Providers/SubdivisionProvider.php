<?php


namespace App\modules\Korzilla\Subdivision\Providers;
use App\modules\Korzilla\Subdivision\Actions\SubdivisionUploadAction;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionCreateTask;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionGetAllTask;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionGetRootTask;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionSortTask;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionUpdateTask;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Korzilla\Subdivision\Values\Outputs\SubdivisionSetOutput;
use App\modules\Ship\Parent\Providers\Provider;



class SubdivisionProvider extends Provider{

    private $setting;
    private $nc_core;

    public function __construct(\nc_Core $nc_core, array $setting){
        $this->setting = $setting;
        $this->nc_core = $nc_core;
    }

    /**
     * Undocumented function
     *
     * @param SubdivisionSetInput[] $input -- массив разделов которые будут выгружаться
     * @param integer|null $subdivisionId -- ID раздела куда будут выгружаться разделы
     * @return array{'внешний_ключ_раздела': SubdivisionSetOutput}
     */
    public function upload(array $input, int $subdivisionId = null)
    {   


        //*Репозитории
        $subdivisionRepository = new SubdivisionRepository($this->nc_core->db);
        $subClassRepository = new SubClassRepository($this->nc_core->db);


        
        $managerUploaderAction = new SubdivisionUploadAction(
            $this->nc_core,
            (new SubdivisionGetAllTask($subdivisionRepository)),
            (new SubdivisionGetRootTask($subdivisionRepository, $subClassRepository)),
            (new SubdivisionSortTask()),
            (new SubdivisionCreateTask($subdivisionRepository, $subClassRepository)),
            (new SubdivisionUpdateTask($subdivisionRepository,$subClassRepository))
        );
        return $managerUploaderAction->run($input,$subdivisionId);
    }
}