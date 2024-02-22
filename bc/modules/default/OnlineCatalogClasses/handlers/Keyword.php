<?php

class Keyword
{
    /**
     * Создает Keyword
     * @param string $originalString
     * @param string $url
     */
    public static function create($originalString, $url = ''): string
    {
        $table = array(
             'А' => 'a', 'Б' => 'b', 'В' => 'v',
             'Г' => 'g', 'Д' => 'd', 'Е' => 'e',
             'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z',
             'И' => 'i', 'Й' => 'j', 'К' => 'k',
             'Л' => 'l', 'М' => 'm', 'Н' => 'n',
             'О' => 'o', 'П' => 'p', 'Р' => 'r',
             'С' => 's', 'Т' => 't', 'У' => 'u',
             'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c',
             'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'csh',
             'Ь' => '', 'Ы' => 'y', 'Ъ' => '',
             'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
             'а' => 'a', 'б' => 'b', 'в' => 'v',
             'г' => 'g', 'д' => 'd', 'е' => 'e',
             'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
             'и' => 'i', 'й' => 'j', 'к' => 'k',
             'л' => 'l', 'м' => 'm', 'н' => 'n',
             'о' => 'o', 'п' => 'p', 'р' => 'r',
             'с' => 's', 'т' => 't', 'у' => 'u',
             'ф' => 'f', 'х' => 'h', 'ц' => 'c',
             'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh',
             'ь' => '', 'ы' => 'y', 'ъ' => '',
             'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '*' => 'x'
        );
    
       $output = str_replace(array_keys($table), array_values($table),trim($originalString));
       
       if($url!=2) $output = str_replace("_", "-", $output);
       
       if ($url) {
            if(!stristr($output, "http://") && !stristr($output, "https://")) $output = str_replace(" ","-",trim($output));
            if ($url==1) { // ссылки
                $output = preg_replace("/[^a-zA-Z0-9-]/","",$output);
                $output = str_replace("--","-",$output);
                $output = str_replace("--","-",$output);
                $output = preg_replace("/[^a-zA-Z0-9-]/","",$output);
                if (is_numeric($output)) $output = "s".$output;
            }
            if ($url==2) { // картинки
                if(!stristr($output, "http://") && !stristr($output, "https://")) $output = preg_replace("/[^a-zA-Z0-9-_\.\,]/","",$output);
            }
            $output = trim($output, "-");
       }

       return $output;
    }
}