<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
global $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;
# ############ функция добавления разделов #####################

// print_r(addSubdivision(1077, 315174, "Тест3", "/catalog/", "00000-0"));

// function addSubdivision($catalogueID, $parentSubId, $SubdivisionName, $ParentHiddenURL, $externalId)
// {
//     global $db;


//     $priority = $this->db->get_var("SELECT SQL_NO_CACHE MAX(Priority) FROM Subdivision WHERE Catalogue_ID = '{$catalogueID}' AND  Parent_Sub_ID = '{$parentSubId}'") + 1;
//     $checked = 1;
//     $englishName = encodestring($SubdivisionName, 1);
//     $HiddenUrl = $ParentHiddenURL . $englishName . "/";
//     $SubdivisionName = addslashes($SubdivisionName);
//     $showSubdivision = 1;

//     $classId = 2001;

//     // добавим раздел
//     $this->db->query(
//         "INSERT INTO Subdivision
//             (
//                 Catalogue_ID,
//                 Parent_Sub_ID,
//                 Subdivision_Name,
//                 Priority,
//                 Checked,
//                 EnglishName,
//                 Hidden_URL,
//                 code1C,
//                 subdir
//             ) 
//         VALUES
//             (
//                 {$catalogueID},
//                 {$parentSubId},
//                 '{$SubdivisionName}',
//                 {$priority},
//                 {$checked},
//                 '{$englishName}',
//                 '{$HiddenUrl}',
//                 '{$externalId}',
//                 '{$showSubdivision}'
//             )"
//     );

//     $subdivisionID = $this->db->insert_id;
//     // addslashes()
//     // добавим инфоблок в раздел
//     if ($subdivisionID) {
//         $res = $this->db->query(
//             "INSERT INTO Sub_Class 
//             (
//                 Subdivision_ID,
//                 Class_ID,
//                 Sub_Class_Name,
//                 EnglishName,
//                 Checked,
//                 Class_Template_ID,
//                 Catalogue_ID,
//                 DefaultAction,
//                 AllowTags,
//                 NL2BR,
//                 UseCaptcha,
//                 CacheForUser
//             ) 
//             VALUES
//             (
//                 {$subdivisionID},
//                 {$classId},
//                 '{$englishName}',
//                 '{$englishName}',   
//                 {$checked},
//                 0,
//                 {$catalogueID},
//                 'index',
//                 '-1',
//                 '-1',
//                 '-1',
//                 '-1'
//             )"
//         );
//         if ($res == 0) {
//             echo $this->db->last_error;
//         }
//         $subClassId = $this->db->insert_id;
//         return ["subdivisionId" => $subdivisionID, "subClassId" => $subClassId, "hiddenUrl" => $HiddenUrl];

//     }
//     //!TODO: обработка ошибок
//     return null;
// }


$subManager = new SubdivisionManager($nc_core);


$item2 = new SubdivisionDTO();
$item2->id = 2077;
$item2->name = "test4-1";
$item2->parentId = 1077;

$item = new SubdivisionDTO();
$item->id = 1077;
$item->name = "test4";
$item->parentId = 0;
$item->children = [$item2];


$subManager->run([$item]);



class SubdivisionManager
{


    /** @var \nc_Db*/
    private $db;

    /** @var int*/
    private $catalogueId;

    /** @var \nc_Core*/
    private $core;

    

    private $existingSubdivision = [];

    /** @var NewSubdivisionDTO */
    private $rootSubdivision;

    private $allSiteSubdivision = [];

    function __construct(\nc_Core $core)
    {
        $this->core = $core;
        $this->db = $core->db;
        $this->catalogueId = $core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']))['Catalogue_ID'];
    }

    /** @param SubdivisionDTO[] $SubdivisionDTO */
    public function run(array $SubdivisionDTO, int $subdivisionId = null)
    {
        $this->setAllSiteSubdivision();
        $this->setRootSubdivision($subdivisionId);

        $this->recursiveBuildTreeSubdivision($SubdivisionDTO, $this->rootSubdivision);

    }

    /**
     *
     * @param null|int $subdivisionId
     * @return void
     */
    private function setRootSubdivision($subdivisionId)
    {
        $rootSubDivision = new NewSubdivisionDTO();
        if ($subdivisionId === null) {
            $subDivision = $this->db->get_row("SELECT `Subdivision_ID`,`Hidden_URL` FROM Subdivision WHERE `Catalogue_ID` = '{$this->catalogueId}'  AND `Hidden_URL` = '/catalog/'",ARRAY_A);
            if (empty($subDivision)) {
                throw new \Exception("Родительская директория не найдена");
            }
        } else {
            $subDivision = $this->db->get_row("SELECT `Subdivision_ID`,`Hidden_URL` FROM Subdivision WHERE `Catalogue_ID` = '{$this->catalogueId}'  AND `Subdivision_ID` = '{$subdivisionId}'",ARRAY_A);
            if (empty($subDivision)) {
                throw new \Exception("Родительская директория не найдена");
            }
        }
        $subClass = $this->db->get_row("SELECT `Sub_Class_ID` FROM Sub_Class WHERE `Subdivision_ID` = '{$subDivision['Subdivision_ID']}'  AND `Class_ID` = 2001",ARRAY_A);
        if (empty($subClass)) {
            throw new \Exception("Родительский инфоблок не найден");
        }
        $rootSubDivision->subdivisionId = $subDivision['Subdivision_ID'];
        $rootSubDivision->hiddenUrl = $subDivision['Hidden_URL'];
        $rootSubDivision->subClassId = $subClass['Sub_Class_ID'];
        $this->rootSubdivision = $rootSubDivision;

    }

    private function setAllSiteSubdivision()
    {
        $data = $this->db->get_results("SELECT `Subdivision_ID`,`Parent_Sub_ID`,`Hidden_URL`,`code1C`,`Checked` FROM `Subdivision` WHERE `Catalogue_ID` = '{$this->catalogueId}' AND `code1C` != '' AND `code1C` IS NOT NULL", ARRAY_A);
        $this->allSiteSubdivision = [];

        foreach ($data as $item) {

            $existingSubdivision = new ExistingSubdivisionDTO();
            $existingSubdivision->subdivisionId = $item['Subdivision_ID'];
            $existingSubdivision->parentSubId = $item['Parent_Sub_ID'];
            $existingSubdivision->hiddenUrl = $item['Hidden_URL'];
            $existingSubdivision->checked = $item['Checked'];
            $this->allSiteSubdivision[$item['code1C']] = $existingSubdivision;
        }
    }

    /**
     * Undocumented function
     *
     * @param SubdivisionDTO[] $SubdivisionDTO
     * @param NewSubdivisionDTO $parentSubdivision
     * @return void
     */
    private function recursiveBuildTreeSubdivision(array $SubdivisionDTO, NewSubdivisionDTO $parentSubdivision)
    {
        foreach ($SubdivisionDTO as $data) {
            if (isset($this->allSiteSubdivision[$data->id])) {
                $this->updateSubdivision($data, $this->allSiteSubdivision[$data->id]);
            } else {
                $this->addSubdivision($data, $parentSubdivision);
            }

            if (!empty($data->children)) {
                $this->recursiveBuildTreeSubdivision($data->children, $this->existingSubdivision[$data->id]);
            }
        }

    }

    private function addSubdivision(SubdivisionDTO $data, NewSubdivisionDTO $parentSubdivision)
    {
        $newSubdivision = new NewSubdivisionDTO();
        $this->addSubdivisionToDB($data, $parentSubdivision, $newSubdivision);
        $this->addSubClassToDB($data, $newSubdivision);
        $this->existingSubdivision[$data->id] = $newSubdivision;
    }

    /**
     * Undocumented function
     *
     * @param SubdivisionDTO $data
     * @param NewSubdivisionDTO $parentSubdivision
     * @return void
     */
    private function addSubdivisionToDB(SubdivisionDTO $data, NewSubdivisionDTO $parentSubdivision, NewSubdivisionDTO $newSubdivision)
    {
        $priority = $this->db->get_var("SELECT SQL_NO_CACHE MAX(Priority) FROM Subdivision WHERE Catalogue_ID = '{$this->catalogueId}' AND  Parent_Sub_ID = '{$parentSubdivision->subdivisionId}'") + 1;
        $checked = 1;
        $englishName = encodestring($data->name, 1);
        $HiddenUrl = $parentSubdivision->hiddenUrl . $englishName . "/";
        $SubdivisionName = addslashes($data->name);
        $showSubdivision = 1;

        // добавим раздел
        $res = $this->db->query(
            "INSERT INTO Subdivision
                (
                    Catalogue_ID,
                    Parent_Sub_ID,
                    Subdivision_Name,
                    Priority,
                    Checked,
                    EnglishName,
                    Hidden_URL,
                    code1C,
                    subdir
                ) 
            VALUES
                (
                    {$this->catalogueId},
                    {$parentSubdivision->subdivisionId},
                    '{$SubdivisionName}',
                    {$priority},
                    {$checked},
                    '{$englishName}',
                    '{$HiddenUrl}',
                    '{$data->id}',
                    '{$showSubdivision}'
                )"
        );
        if ($res == 0) {
            throw new Exception($this->db->last_error, 1);
        }

        $newSubdivision->subdivisionId = $this->db->insert_id;
        $newSubdivision->hiddenUrl = $HiddenUrl;
    }

    /**
     * Undocumented function
     *
     * @param SubdivisionDTO $data
     * @param NewSubdivisionDTO $newSubdivisionId
     * @return void
     */
    private function addSubClassToDB(SubdivisionDTO $data, NewSubdivisionDTO $newSubdivision)
    {

        $englishName = encodestring($data->name, 1);
        $checked = 1;

        // добавим инфоблок в раздел
        $res = $this->db->query(
            "INSERT INTO Sub_Class 
        (
            Subdivision_ID,
            Class_ID,
            Sub_Class_Name,
            EnglishName,
            Checked,
            Class_Template_ID,
            Catalogue_ID,
            DefaultAction,
            AllowTags,
            NL2BR,
            UseCaptcha,
            CacheForUser
        ) 
        VALUES
        (
            {$newSubdivision->subdivisionId},
            2001,
            '{$englishName}',
            '{$englishName}',   
            {$checked},
            0,
            {$this->catalogueId},
            'index',
            '-1',
            '-1',
            '-1',
            '-1'
        )"
        );
        if ($res == 0) {
            throw new Exception($this->db->last_error, 1);
        }

        $newSubdivision->subClassId = $this->db->insert_id;
    }


    private function updateSubdivision(SubdivisionDTO $data, ExistingSubdivisionDTO $siteSubdivision)
    {
        // if (!isset($this->allSiteSubdivision[$data->parentId]) || $this->allSiteSubdivision[$data->parentId]->subdivisionId != $siteSubdivision->parentSubId) {


        // }
        return;

        // $newSubdivision = new NewSubdivisionDTO();
        // $newSubdivision->subdivisionId = $siteSubdivision->subdivisionId;
        // $newSubdivision->subClassId = $siteSubdivision->;
        // $newSubdivision->hiddenUrl = $siteSubdivision['Hidden_URL'];
        // $this->existingSubdivision[$data->id] = $newSubdivision;
    }


}

class ssSubdivisionDTO
{
    /**
     * Наше поля в БД  - code1C
     *  @var string*/
    public $id;

    /**
     * Наше поля в БД  - Subdivision_Name
     *  @var string*/
    public $name;

    /** @var string*/
    public $parentId;



    /**
     * @var SubdivisionDTO[]
     */
    public $children;
}




class ssNewSubdivisionDTO
{
    /**
     *  @var int*/
    public $subdivisionId;

    /**
     *  @var int*/
    public $subClassId;

    /** @var string*/
    public $hiddenUrl;

}

class ssExistingSubdivisionDTO
{
    /**
     *  @var int*/
    public $subdivisionId;

    /**
     *  @var int*/
    public $parentSubId;

    /** @var string*/
    public $hiddenUrl;

    /** @var int*/
    public $checked;

    /** @var bool*/
    public $is_updating = false;

}
