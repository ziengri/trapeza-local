<div id='acat-body'>
    <link href="<?=$settings_array[path]?>styles.css" rel="stylesheet">
    <script src="<?=$settings_array[path]?>js/jquery.arcticmodal-0.3.min.js"></script>
    <?php 
        if($breadcrumbs && !$mesid){
            $url = $hrefPrefix;
            $getnames = array(1 => 'type', 2 => 'mark', 3 => 'modelId', 4 => 'groupId');

            echo "<section class='line_info'><ul class='xleb'>";
            foreach ($breadcrumbs as $i => $bread) {
                if($i == count($breadcrumbs)-1){
                    echo "<li class='xleb-item'><span>{$bread->name}</span></li>";
                }else{
                    if($i>0) $url .= ($i==1 ? "?" : "&")."{$getnames[$i]}={$bread->url}";
                    echo "<li class='xleb-item'><a href='{$url}'>{$bread->name}</a></li>";
                }
            }
            echo "</ul></section>";
        }
    ?>