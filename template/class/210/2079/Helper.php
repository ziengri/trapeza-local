<?php

class Helper {
    public static function getJSON($data){
        return json_decode(json_encode($data));
    }

    public static function getData($api, $jsonToArray, $url = ''){
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => "Authorization: {$api->token}"
            ),
        );
        $context = stream_context_create($opts);
        $data = file_get_contents($api->host.'/catalogs'.$url, false, $context);

        // global $AUTH_USER_ID;
        // if ($AUTH_USER_ID == 2419) {
        //     echo '<pre>';
        //     var_dump($data);
        //     exit;
        // }
        if ($jsonToArray) return (array) json_decode($data);
        else return json_decode($data);

    }

    public static function getImage($api, $url = '', $print=0){
        $headers = array();
        $headers[] = "Authorization: $api->token";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api->host.'/catalogs'.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $imageData = curl_exec($ch);
        $imageType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        if($print){
            ob_end_clean();
            header("Content-Type: $imageType");
            echo $imageData;
            exit();
        }else{
            return $imageData;
        }
    }

}

function redirect($url=""){
    global $hrefPrefix;
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$hrefPrefix}{$url}");
	exit();
}

function subdivisions_acat($models, $link){
        global $setting, $hrefPrefix, $noimage;
        # ширина объекта
        if($setting[sizesub_select]=='count') $sizeitem = $setting[sizesub_counts];
        else if($setting[sizesub] && is_numeric($setting[sizesub])) $sizeitem = $setting[sizesub];
        else $sizeitem = 224;
        # отступ
        if(is_numeric($setting[sizesub_margin])) $margin = $setting[sizesub_margin];
        else $margin = 0;
        # Шаблон вывода
        if($setting[sizesub_template]) $template = $setting[sizesub_template];
        # object fit
        $imagefit = $setting[sizesub_fit];
        # image procent
        $sizeitem_image = $setting["sizesub_image"];

        $data = subdivisionData(array(
            'type' => 'subdivision',
            'sizeitem' => $sizeitem,
            'margin' => $margin,
            'sizeitem_image' => $sizeitem_image,
            'template' => $template
        ));

        $reslt.= "<ul class='{$data['class']}' {$data['attr']}>";

        foreach ($models as $k => $model) {
            $fulllink = "{$link}&modelId={$model->short_name}";
            $imagefitRes = image_fit($model->image ? $imagefit : "");
            $class = !$model->image ? "class='nophoto'" : "";
            $photoUrl = ($model->image ? $model->image : $noimage);
            $photoHtml = "<div class='{$imagefitRes}'><a href='{$fulllink}'><img src='{$photoUrl}' {$class}></a></div>";

            $name = "<div class='name ".($model->modification ? "name-text" : "")."'><a href='{$fulllink}' title='{$model->name_with_mark}'><span>{$model->name_with_mark}</span></a><div class='name-text-second'>".($model->relevance ? 'Актуальность: ' . substr($model->relevance, 5, 2).'.'.substr($model->relevance, 0, 4) : '')."</div>".($model->modification ? "<div class='sub-text'>{$model->modification}</div>" : "")."</div>";

            $reslt.= "<li class='sub'>
                        <div class='wrapper mainmenubg-bord-hov-sh'>
                            {$photoHtml}
                            {$name}
                        </div>
                    </li>";
        }

        $reslt.= "</ul>";

        return $reslt;
}