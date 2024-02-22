<?php
/**
 * Замена слов в тексте по ключам
 * 
 * при добавлении ключей необхоимо добавить ключ в перемую self::KEYS
 * и описать логику в getValue()
 */

namespace Korzilla;

class Replacer
{   
    /**
     * @var string PREFFIX_KEY строк экранирования перед ключевым словом
     */
    const PREFFIX_KEY = '%';
    /**
     * @var string SUFFIX_KEY строк экранирования после ключевого слова
     */
    const SUFFIX_KEY = '%';
    /**
     * @var string SEPARATE_KEY строк разделения ключа
     */
    const SEPARATE_KEY = '-';
    /**
     * @var array $keys массив возможных ключей
     */
    const KEYS = [
        'PARENTNAME' => [
            'title' => 'Имя родительского раздела'
        ],      
        'PARENT2NAME' => [
            'title' => 'Имя родительского раздела 2 уровня'
        ],     
        'PRICE' => [
            'title' => 'Цена товара'
        ],           
        'PRICE_DESCRIPT' => [
            'title' => 'Цена товара/discription'
        ],
        'ART' => [
            'title' => 'Артикул товара'
        ],             
        'STOCK' => [
            'title' => 'Остаток товара'
        ],           
        'ITEMNAME' => [
            'title' => 'Название товара'
        ],        
        'VENDOR' => [ 
            'title' => 'Производитель товара'
        ],          
        'VARIANT' => [
            'title' => 'Вариант товара'
        ],         
        'ITEMNUM' => [
            'title' => 'ID товара'
        ],         
        'ORDERMAIL' => [
            'title' => 'Ссылка на офромление заказа'
        ],       
        'CONTACTSCATALOG' => [
            'title' => 'Ссылка на контакты'
        ],
        'COMPANY' => [
            'title' => 'Название компании'
        ],        
        'CITYNAME' => [
            'title' => 'Название города'
        ],       
        'NOCITY' => [
            'title' => 'Не добавлять название города в конец текста'
        ],         
        'CATEGORY' => [
            'title' => 'Название раздела'
        ],       
        'NUMITEMS' => [
            'title' => 'Кол-во товаров'
        ],       
        'PMAX' => [
            'title' => 'Максимальная цена'
        ],           
        'PMIN' => [
            'title' => 'Минимальная цена'
        ],           
        'SITE' => [
            'title' => 'Домен'
        ],           
        'ADDRESS' => [
            'title' => 'Адрес контакта текущего города'
        ]
       
    ];
    const MODIFICATORS = [
        'LOW' => [
            'title' => 'Привести к нижнему регистру'
        ]
    ];


    /**
     * @var array $repliceArr массив с заменами
     */
    private static $repliceArr = [];

    public static $item;

    /**
     * Замена ключей в тексте
     * 
     * @param string $text текст в котором нужно заменить ключи
     * 
     * @return string $text замененнный текст
     */
    public static function replaceText($text)
    {
        $keys = self::findKeys($text);
        if (!empty($keys)) {
            $replaceArr = self::getReplaceArr($keys);
            $text = strtr($text, $replaceArr);
			
        }
        return $text;
    }

    /**
     * Поиск ключей в тексте
     * 
     * @param string $text текст в котором нужно найти ключи
     * 
     * @return array массив ключей найденных в тексте
     */
    private static function findKeys($text)
    {
        $patern = '';
        $preffix = self::PREFFIX_KEY;
        $suffix = self::SUFFIX_KEY;
        foreach (array_keys(self::KEYS) as $key) {
            $patern .= ($patern ? '|' : null);
            $patern .= "({$preffix}{$key}[^{$suffix}]*{$suffix})";
        }
        preg_match_all("/{$patern}/", $text, $matches);
        return $matches[0];
    }

    /**
     * Получение массива замены
     * 
     * @param array $keys массив ключей
     * 
     * @return array массив замены
     */
    private static function getReplaceArr($keys)
    {
        $result = [];
        foreach ($keys as $key) {
            if (isset(self::$repliceArr[$key])) {
                $result[$key] = self::$repliceArr[$key];
            } else {                
                $result[$key] = self::$repliceArr[$key] = self::getValue($key);
            }
        }
        return $result;
    }

    /**
     * Получение значения по ключу
     * 
     * @param string $key ключ
     * 
     * @return string значение ключа
     */
    private static function getValue($key)
    {        
        $key = substr($key, strlen(self::PREFFIX_KEY));
        $key = substr($key, 0, -strlen(self::SUFFIX_KEY));
        $keyStruct = explode(self::SEPARATE_KEY, $key);

        switch ($keyStruct[0]) {
            case 'PARENTNAME':
                $tree = self::getParentSubTree();
                $value = isset($tree[1]) ? $tree[1]['Subdivision_Name'] : null;
                break;
            case 'PARENT2NAME':
                $tree = self::getParentSubTree();
                $value = isset($tree[2]) ? $tree[2]['Subdivision_Name'] : null;
                break;
            case 'COMPANY':
                $current_catalogue = self::getCurrentCatalogue();
                $value = isset($current_catalogue['Catalogue_Name']) ? $current_catalogue['Catalogue_Name'] : null;
                break;
            case 'PRICE':
                $item = self::getItem();
                if (!empty($item)) {
                    $value = $item->price ?: 'договорной';                 
                } else {
                    $value = '';
                }
                break;
            // case 'PRICE_DESCRIPT':
            //     $item = self::getItem();
            //     if (!empty($item)) {
            //         $value = $item->price ?: 'договорной';                 
            //     } else {
            //         $value = '';
            //     }
            //     break;
            case 'ART':
                $item = self::getItem();
                $value = !empty($item) ? $item->art : '';
                break;
            case 'STOCK':
                $item = self::getItem();
                $value = !empty($item) ? $item->stock : '';
                break;
            case 'ITEMNAME':
                $item = self::getItem();
                $value = !empty($item) ? $item->name : '';
               
                break;
                
            case 'VENDOR':
                $item = self::getItem();
                $value = !empty($item) ? $item->vendor : '';
                break;
            case 'VARIANT':
                $item = self::getItem();
                $value = !empty($item) ? $item->variablename : '';
                break;
            case 'ITEMNUM':
                $item = self::getItem();
                $value = !empty($item) ? $item->id : '';
                break;
            case 'ORDERMAIL':
                $item = self::getItem();
                if (!empty($item)) {
                    $href = "/cart/add_cart.html?itemId={$item->id}";
                    $title = getLangWord('seo_txt_orderMail', 'Оформить заявку');
                    $attr = [
                        "data-rel='lightcase'",
                        "data-maxwidth='390'",
                        "data-groupclass='buyoneclick'",
                        "href='{$href}'",
                        "data-lc-href='{$href}&isNacked=1'",
                        "title='{$title}'",
                        "class='dotted'"
                    ];
                    $value = "<a ".implode(' ', $attr).">{$title}</a>";
                }
                break;
            case 'CONTACTSCATALOG':
                $current_catalogue = self::getCurrentCatalogue();
                $db = self::getDB();
                $contName = $db->get_var("SELECT `Subdivision_Name` FROM `Subdivision` WHERE `Catalogue_ID` = {$current_catalogue['Catalogue_ID']} AND `Hidden_URL` = '/contacts/'");
                $contName = getLangWord('lang_sub_contacts', $contName);
                $value = "\"<a href='/contacts/' target='_blank'>{$contName}</a>\"";
                break;
            case 'CITYNAME':
                $value = self::getCity('name');
                break;
            case 'NOCITY':
                $value = '';
                break;
            case 'CATEGORY':
                $subdivision = self::getSubdivision();
                $value = $subdivision['Subdivision_Name'];
                break;
            case 'NUMITEMS':
                $subdivision = self::getSubdivision();
                $value = seoWordsRazdel($subdivision["Subdivision_ID"], 'NUM');
                break;
            case 'PMAX':
                $subdivision = self::getSubdivision();
                $value = seoWordsRazdel($subdivision["Subdivision_ID"], 'DESC');
                break;
            case 'PMIN':
                $subdivision = self::getSubdivision();
                $value = seoWordsRazdel($subdivision["Subdivision_ID"], 'ASC');
                break;
            case 'SITE':
                $value = $_SERVER['HTTP_HOST'];
                break;
            case 'ADDRESS':
                $current_catalogue = self::getCurrentCatalogue();
                $cityID = self::getCity('id');
                $mainID = self::getCity('mainID');                

                $sql  = "SELECT `adres` FROM `Message2012` WHERE `Catalogue_ID` = {$current_catalogue['Catalogue_ID']}";
                $sql .= " AND `adres` != '' AND `adres` IS NOT NULL";

                $sql .= " AND (";
                    $sql .= "`citytarget` IS NULL OR `citytarget` = '' OR `citytarget` = ',,'";
                    if (!empty($cityID)) {
                        $sql .= "OR `citytarget` LIKE '%,{$cityID},%'";
                    }
                    if (!empty($mainID)) {
                        $sql .= "OR `citytarget` LIKE '%,{$mainID},%'";
                    }
                $sql .= ")";

                $sql .= "ORDER BY ";
                if (!empty($cityID) && !empty($mainID)) {
                    $sql .= "(CASE";
                    if (!empty($cityID)) {
                        $sql .= " WHEN `citytarget` LIKE '%,{$cityID},%' THEN 1";
                    }
                    if (!empty($mainID)) {
                        $sql .= " WHEN `citytarget` LIKE '%,{$mainID},%' THEN 2";
                    }
                    $sql .= " ELSE 3";
                    $sql .= " END),";
                }
                $sql .= "`Priority` LIMIT 1";
                
                $db = self::getDB();
                $value = $db->get_var($sql) ?: '';
                
                break;
            default:
                $value = '';
                break;
        }

        if (!empty($value) && count($keyStruct) > 1) {
            $isLow = false;
            for ($i = 1; $i < count($keyStruct); $i++) {
                if ($keyStruct[$i] === 'LOW') {
                    $isLow = true;
                } else {
                    $value = \Korzilla\Morpher::convert($value, $keyStruct[$i]) ?: $value;
                }
            }
            if ($isLow) $value = mb_strtolower($value);
        }

        return $value;
    }

    private static function getCurrentCatalogue()
    {
        global $current_catalogue;
        return $current_catalogue;
    }

    private static function getParentSubTree()
    {
        global $parent_sub_tree;
        return $parent_sub_tree;
    }

    private static function getSubdivision()
    {
        global $current_sub;
        return $current_sub;
    }

    private static function getItem()
    {
        global $nc_core, $message, $action, $classID;

        $db = self::getDB();

        if (!isset(self::$item)) {
            if ($action == 'full' && $classID == 2001) {
                self::$item = \Class2001::getItemById($message);
            } elseif ($action == 'full' && $classID == 2030) {
                self::$item = $db->get_row("SELECT `name` FROM Message2030 WHERE Message_ID = {$message}");
            } elseif ($action == 'full' && $classID == 2003) {
                self::$item = $db->get_row("SELECT `name` FROM Message2003 WHERE Message_ID = {$message}");
            }
            elseif ($action == 'full' && $classID == 2021) {
                self::$item = $db->get_row("SELECT `name` FROM Message2021 WHERE Message_ID = {$message}");
            }
            else {
                self::$item = false;
            }
        }

        return self::$item;
    }

    private static function getCity($key)
    {
        global $cityid, $cityname, $citymainid;
        switch ($key) {
            case 'id': return $cityid;
            case 'mainID': return $citymainid;
            case 'name': return $cityname;
        }        
    }

    private static function getDB()
    {
        global $db;
        return $db;
    }
}

// function replaceTextToNewKeys($text) {
//     $keys = [
//         'CATEGORY-low' => '%CATEGORY-LOW%',
//         'CATEGORY' => '%CATEGORY%',
//         'PARENTNAME-low' => '%PARENTNAME-LOW%',
//         'PARENT2NAME-low' => '%PARENT2NAME-LOW%',
//         'PRICE-low' => '%PRICE-LOW%',
//         'ART-low' => '%ART-LOW%',
//         'STOCK-low' => '%STOCK-LOW%',
//         'ITEMNAME-low' => '%ITEMNAME-LOW%',
//         'VENDOR-low' => '%VENDOR-LOW%',
//         'VARIANT-low' => '%VARIANT-LOW%',
//         'ITEMNUM-low' => '%ITEMNUM-LOW%',
//         'COMPANY-low' => '%COMPANY-LOW%',
//         'CITYNAME-low' => '%CITYNAME-LOW%',
//         'в CITYGDE-low' => '%CITYNAME-LOW-GDE%',
//         'CITYGDE-low' => '%CITYNAME-LOW-GDE%',
//         'NOCITY-low' => '%NOCITY-LOW%',
//         'NAME-low' => '%CATEGORY-LOW%',
//         'NUMITEMS-low' => '%NUMITEMS-LOW%',
//         'PMAX-low' => '%PMAX-LOW%',
//         'PMIN-low' => '%PMIN-LOW%',
//         'PARENTNAME' => '%PARENTNAME%',  
//         'PARENT2NAME' => '%PARENT2NAME%', 
//         'PRICE' => '%PRICE%',       
//         'ART' => '%ART%',         
//         'STOCK' => '%STOCK%',       
//         'ITEMNAME' => '%ITEMNAME%',    
//         'VENDOR' => '%VENDOR%',      
//         'VARIANT' => '%VARIANT%',     
//         'ITEMNUM' => '%ITEMNUM%',     
//         'COMPANY' => '%COMPANY%',     
//         'CITYNAME' => '%CITYNAME%',
//         'в CITYGDE' => '%CITYNAME-GDE%',
//         'CITYGDE' => '%CITYNAME-GDE%',
//         'NOCITY' => '%NOCITY%',      
//         'NAME' => '%CATEGORY%',        
//         'NUMITEMS' => '%NUMITEMS%',    
//         'PMAX' => '%PMAX%',        
//         'PMIN' => '%PMIN%',        
//     ];
//     return strtr($text, $keys);
// }