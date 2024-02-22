<?php

namespace App\modules\Korzilla\Product\Values\Outputs;

use App\modules\Korzilla\Product\Models\ProductModel;
use App\modules\Ship\Parent\Outputs\Output;

class ProductSetOutput extends Output
{
    public $Message_ID;  

    static function fromModel(ProductModel $model): self{
        $output = new self();
        $output->Message_ID = $model->Message_ID;
        return $output;
        // $output->User_ID = $model->User_ID;
        // $output->Subdivision_ID = $model->Subdivision_ID;
        // $output->Sub_Class_ID = $model->Sub_Class_ID;
        // $output->Catalogue_ID = $model->Catalogue_ID;
        // $output->Priority = $model->Priority;
        // $output->Keyword = $model->Keyword;
        // $output->Checked = $model->Checked;
        // $output->Parent_Message_ID = $model->Parent_Message_ID;
        // $output->Created = $model->Created;
        // $output->LastUpdated = $model->LastUpdated;
        // $output->name = $model->name;
        // $output->text = $model->text;
        // $output->id1c = $model->id1c;
        // $output->code = $model->code;
        // $output->vendor = $model->vendor;
        // $output->art = $model->art;
    }
}
