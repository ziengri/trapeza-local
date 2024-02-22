<?
#Init nc
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
require_once ($INCLUDE_FOLDER."classes/nc_imagetransform.class.php");

global  $db, $pathInc, $nc_core, $field_connect, $current_catalogue, $login, $pass, $catalogue, $sub, $cc, $dop_sub, $dop_cc, $pathImport, $dop_class, $current_user, $AUTH_USER_ID;


$catalogue = 604;
$sub = 147595;		#Номер раздела для выгрузки меню
$cc = 146495;		#Номер инфоблока
$dop_sub = 148096;	#Номер раздела для допов
$dop_cc = 147014;	#Номер инфоблока
$dop_class = 2074;	#Номер компонента
$pathImport = $ROOTDIR.$pathInc.'/files/import/';
// получить параметры
if (!$current_catalogue) $current_catalogue = $nc_core->catalogue->get_by_id($catalogue);

require_once "function.inc.php";
iiko_getToken(); # init token

// отправить заказ в iiko (id Заказа)
//Class2005::sendOrderIIKO(5026);

if ($_GET['key']!='KNG2S6XS') die('Вход без ключа запрещен');
$type = $_GET['type'];
$timeStamp = "last_update.ini"; # Временная отметка обновления меню

echo '<form method="GET">
        <input name="key" value="KNG2S6XS" type="hidden" />
    	<input name="type" value="update_menu" type="hidden" />
    	<input type="submit" value="Запустить выгрузку" /> <span style="font-size: 14px;">Последнее обновление: '.$current_catalogue['time_update_menu'].'</span>
    </form>
    <form method="GET">
    	<input name="key" value="KNG2S6XS" type="hidden" />
    	<input name="type" value="update_stop_list" type="hidden" />
    	<input type="submit" value="Обновить стоп-лист" /> <span style="font-size: 14px;">Последнее обновление: '.$current_catalogue['time_update_stop'].'</span>
    </form>
    <form method="GET">
    	<input name="key" value="KNG2S6XS" type="hidden" />
    	<input name="type" value="update_program" type="hidden" />
    	<input type="submit" value="Обнить программы" /> <span style="font-size: 14px;">Последнее обновление: '.$current_catalogue['time_update_stop'].'</span>
    </form>
    <form method="GET">
    	<input name="key" value="KNG2S6XS" type="hidden" />
    	<input name="type" value="output_menu" type="hidden" />
    	<input type="submit" value="Вывести меню без выгрузки" />
    </form>
    <hr/>';

if(!$type || $type=='output_menu'){
	if (file_exists($timeStamp)){
		$timeStamp_time = intval(file_get_contents($timeStamp));
		if($timeStamp_time) echo 'Последнее обновление меню на сервере iiko: '.date('d.m.Y H:i:s', $timeStamp_time);
	}
    # вывод меню
    if($type=='output_menu'){
        $url = IIKO_URL.'nomenclature/'.$iiko_orgid.'?access_token='.$iiko_token;
        echo "<h3>Меню:</h3><textarea style='margin: 0px;width: 100%;height: 160px;'>".json_encode(IIKO_get($url), 1)."</textarea>";
        $url = IIKO_URL."stopLists/getDeliveryStopList?access_token={$iiko_token}&organization={$iiko_orgid}";
        echo "<h3>Стоп-лист:</h3><textarea style='margin: 0px;width: 100%;height: 160px;'>".json_encode(IIKO_get($url), 1)."</textarea>";
    }
}else if($type=='update_menu'){ # запуск выгрузки

    $url = IIKO_URL.'nomenclature/'.$iiko_orgid.'?access_token='.$iiko_token;
    $menu = IIKO_get($url);
//$fh = fopen('/var/www/u0533402/data/httpdocs/xn----vtbkcl2fd.xn--p1acf/iiko/logs/update_menu.log', "a+");
//$fh = fopen('/httpdocs/xn----vtbkcl2fd.xn--p1acf/iiko/logs/update_menu.log', "a+");
$fh = fopen('logs/update_menu.log', "a+");
if(isset($menu['revision']) && isset($menu['uploadDate']) ){
  $success1 = fwrite($fh, '[' . date("d.m.y H:i") . ']:(' . $menu['uploadDate']. ')=' . $menu['revision'] . PHP_EOL);
}
fclose($fh);

    # сортировка разделов
    $menu['groups'] = _sortGroups($menu['groups']);
    #Вырубаю разделы и товары
    _offAll();
    #Обновляю/создаю разделы
    _updateSubs($menu['groups'], $sub);

    $groupTags = array();
    #Обновляю/создаю дополнительные ингредиенты
    if(is_array($menu['products'])) foreach($menu['products'] as $product){
    	if ($product['type'] != 'modifier') continue;

		/* TO DO */
		if ($product['id'] == '80eb5537-02fe-44c3-8357-e5679a99da0f') continue;

    	#Vars
    	$code = $product['id'];
    	$name = addslashes($product['name']);
    	$price = $product['price'];
    	$_art = $product['code'];
        $iikoGroupId = $product['groupId'];
        $weight =  $product['weight'];

    	#Create or update
    	$id = $db->get_var("SELECT Message_ID FROM Message{$dop_class} WHERE code='".$code."'");
    	if ($id) $db->query("UPDATE Message{$dop_class} SET name = '{$name}', iikoGroupId = '{$iikoGroupId}', price = {$price}, weight = {$weight} WHERE Message_ID=".$id);
    	else {
    		$priority = $db->get_var("SELECT COUNT(*) FROM Message{$dop_class}");
    		$priority++;
    		$db->query("INSERT INTO Message{$dop_class} (name, price, code, _art, User_ID, Subdivision_ID, Sub_Class_ID, Priority, iikoGroupId, weight)
                        VALUES ('{$name}', {$price}, '{$code}', '{$_art}', 2298, {$dop_sub}, {$dop_cc}, {$priority}, '{$iikoGroupId}', {$weight})");
    		$id = $db->insert_id;
    	}

    	#Img
    	if ($product['images']){
    		$_img = '';
    		$img = $product['images'][0];
    		$img_name = $img['imageId'].'_iiko.jpg';
    		if (!file_exists($pathImport.$img_name)){
    			$img = file_get_contents($img['imageUrl']);
    			file_put_contents($pathImport.$img_name, $img);
    			$_img = $img_name;
    		}
    		else {
    			$_dateCreate = filectime($pathImport.$img_name);
    			$_dateUpload = strtotime($img['uploadDate']);
    			if ($_dateUpload>$_dateCreate){
    				$img = file_get_contents($img['imageUrl']);
    				file_put_contents($pathImport.$img_name, $img);
    				$_img = $img_name;
    			}
    		}
    		if ($_img){
                $photoSize = @getimagesize($pathImport.$img_name);
                if($photoSize[0] > 800) @nc_ImageTransform::imgResize($pathImport.$img_name, $pathImport.$img_name,800,800, 0, "", 90);
    			$db->query("UPDATE Message{$dop_class} SET photourl='".$_img."' WHERE Message_ID=".$id);
            }
            $imgArr = array();
            foreach ($product['images'] as $key => $imgInfo) {
                $img_name = $imgInfo['imageId']."_iiko_{$key}.jpg";
                $img = file_get_contents($imgInfo['imageUrl']);
                file_put_contents($pathImport.$img_name, $img);
                $imgArr[] = $img_name;
                $imgSize = @getimagesize($pathImport.$img_name);
                if($imgSize[0] > 800) @nc_ImageTransform::imgResize($pathImport.$img_name, $pathImport.$img_name,800,800, 0, "", 90);
            }
            if ($imgArr) $db->query("UPDATE Message{$dop_class} SET imgUrlList = '".addslashes(json_encode($imgArr))."' WHERE Message_ID = {$id}");
    	}
    }
    #Обновляю/создаю товары
    if(is_array($menu['products'])) foreach($menu['products'] as $product){

    	if ($product['type']=='modifier') continue;

    	#Vars
    	$code = $product['id'];
    	$art = $product['code'];
    	$name = addslashes($product['name']);
    	$price = $product['price'];
    	$descr = $product['description']."\n".$product['additionalInfo'];
    	$weight = $product['weight'];

    	$kkal100 = $product['energyAmount'];
    	$kkal = $product['energyFullAmount'];
    	$fat100 = $product['fatAmount'];
    	$fat = $product['fatFullAmount'];
    	$uglevod100 = $product['carbohydrateAmount'];
    	$uglevod = $product['carbohydrateFullAmount'];
        $belki100 = $product['fiberAmount'];
        $belki = $product['fiberFullAmount'];
        $tags = $product['tags'] && $product['tags'][0] ? mb_strtolower(implode(';', $product['tags'])) : '';

    	#Sub and cc
    	$_sub = $_cc = 0;
    	$razd = _getSubID($product['parentGroup']);
    	if ($razd){
    		$_sub = $razd['sub'];
    		$_cc = $razd['cc'];
    	}
    	else
    		continue;

    	# заполняем теги для групп
    	if ($tags) {
    		if (!is_array($groupTags[$_sub])) $groupTags[$_sub] = array();
    		foreach (explode(';', $tags) as $tag) {
	    		if (!in_array($tag, $groupTags[$_sub])) $groupTags[$_sub][] = $tag;
    		}
    	}

    	$priority = $product['order'];
    	#Create or update
    	$id = $db->get_var("SELECT Message_ID FROM Message2001 WHERE code='".$code."'");

        $paramInsert = array();
        $paramInsert['name'] = $name;
        $paramInsert['price'] = $price;
        $paramInsert['descr'] = $descr;
        $paramInsert['Subdivision_ID'] = $_sub;
        $paramInsert['Sub_Class_ID'] = $_cc;
        $paramInsert['Checked'] = 1;
        $paramInsert['code'] = $code;
        $paramInsert['art'] = $art;
        $paramInsert['ves'] = $weight;
        $paramInsert['Priority'] = $priority;

        $paramInsert['kkal100'] = $kkal100;
        $paramInsert['kkal'] = $kkal;
        $paramInsert['fat100'] = $fat100;
        $paramInsert['fat'] = $fat;
        $paramInsert['uglevod100'] = $uglevod100;
        $paramInsert['uglevod'] = $uglevod;
        $paramInsert['belki100'] = $belki100;
        $paramInsert['belki'] = $belki;
        $paramInsert['tags'] = $tags ? ";{$tags};" : '';

    	if ($id){
            $prm = "";
            foreach ($paramInsert as $key => $value) {
                $prm .= ($prm ? "," : "")."{$key} = '{$value}'";
            }
    		$db->query("UPDATE Message2001 SET {$prm} WHERE Message_ID = {$id}");
    	} else {
            $sql_names = $sql_val = "";
            foreach ($paramInsert as $key => $value) {
                $sql_names .= ($sql_names ? "," : "").$key;
                $sql_val .= ($sql_val ? "," : "")."'{$value}'";
            }
    		$sql = "INSERT INTO Message2001 ({$sql_names}) VALUES ({$sql_val})";
    		$db->query($sql);
    		$id = $db->insert_id;
    	}

    	#Img
    	$_img = array();
    	if ($product['images']){
    		$product['images'] = array_reverse($product['images']);
    		$product['images'] = array_slice($product['images'], 0, 1);
    		foreach($product['images'] as $img){

    			$dateUpload = strtotime($img['uploadDate']);
    			$img_name = $img['imageId'].'_'.$dateUpload.'_iiko.jpg';

    			if (!file_exists($pathImport.$img_name)){
    				$img = file_get_contents($img['imageUrl']);
    				file_put_contents($pathImport.$img_name, $img);
                    $photoSize = @getimagesize($pathImport.$img_name);
                    if($photoSize[0] > 1280) @nc_ImageTransform::imgResize($pathImport.$img_name, $pathImport.$img_name,1280,1280, 0, "", 90);
    			}

    			$_img[] = $img_name;
    		}
    		$db->query("UPDATE Message2001 SET photourl='".implode(',', $_img)."' WHERE Message_ID=".$id);
    	}

        # modify
        if ($product['modifiers'] || $product['groupModifiers']) {
            $modify = json_encode(array('modifiers' => $product['modifiers'], 'groupModifiers' => $product['groupModifiers']));
        } else {
            $modify = "";
        }
        // $modify = array(
        //     'modifiers' => $product['modifiers'],
        //     'groupModifiers' => $product['groupModifiers']
        // );
    	$db->query("UPDATE Message2001 SET dops = '{$modify}' WHERE Message_ID = {$id}");
    }

    # обновление тэгов у разделов
    foreach ($groupTags as $key => $tag) {
    	$db->query("UPDATE Subdivision SET tags = '".implode(';', $tag)."' WHERE Subdivision_ID = {$key}");
    }

    $db->query("UPDATE Catalogue SET time_update_menu = '".date("Y-m-d H:i:s")."' WHERE Catalogue_ID = 604");

	echo "Меню обновлено";

}else if($type=='update_stop_list'){
    $stopList = IIKO_get(IIKO_URL."stopLists/getDeliveryStopList?access_token={$iiko_token}&organization={$iiko_orgid}");
	if($stopList){
		$stopItems = "";
		foreach ($stopList[stopList] as $stopListItem) {
			if($stopListItem['organizationId']==$iiko_orgid) $stopItems = $stopListItem['items'];
		}
		$db->query("UPDATE Message2001 SET nohave = 0");
		if($stopItems){
			foreach ($stopItems as $itm) {
				if($itm[balance] <= 0) $db->query("UPDATE Message2001 SET nohave = 1 WHERE code = '{$itm['productId']}'");
			}
		}
	}
    $db->query("UPDATE Catalogue SET time_update_stop = '".date("Y-m-d H:i:s")."' WHERE Catalogue_ID = 604");
	echo "Стоп-лист обновлен";
}else if($type=='BJPVNKRBS0P9NJR6SVQU'){ # удалить все меню

	$subs = $db->get_col("SELECT Subdivision_ID FROM Subdivision WHERE code1C!=''");
	$db->query("DELETE FROM Subdivision WHERE code1C!=''");
	foreach($subs as $_sub){
		$db->query("DELETE FROM Message2001 WHERE Subdivision_ID=".$_sub);
	}
	$db->query("DELETE FROM Message{$dop_class} WHERE code!=''");
	$db->query("DELETE FROM Message2001 WHERE Subdivision_ID=".$sub);
	$imgs = scandir($pathImport);
	unset($imgs[1], $imgs[0]);
	sort($imgs);
	foreach($imgs as $img) unlink($pathImport.'/'.$img);
	echo "Меню удалено";

} elseif ($type == 'update_program') {
    # обновление программ
    $programsFromDd = $db->get_results("SELECT * FROM Message2096", ARRAY_A);
    $programsData = array();
    if ($programsFromDd) {
        foreach ($programsFromDd as $program) {
            $programsData[$program['marketingId']] = $program;
            $programsData[$program['marketingId']]['inIiko'] = false;
        }
    }
    $iikoPrograms = IIKO_get(IIKO_URL."organization/programs?access_token={$iiko_token}&organization={$iiko_orgid}") OR die("не удалось получать программы из iiko");
    foreach ($iikoPrograms as $program) {
    	foreach ($program['marketingCampaigns'] as $marketing) {
            $programStruct = array(
                "Checked" => ($program['isActive'] && $marketing['isActive'] ? 1 : 0),
                "programId" => $program['id'],
                "programName" => $program['name'],
                "serviceFrom" => "{$program['serviceFrom']}",
                "serviceTo" => "{$program['serviceTo']}",
                "programType" => $program['programType'],
                "programActive" => $program['isActive'] ? 1 : 0,
                "marketingId" => $marketing['id'],
                "marketingName" => $marketing['name'],
                "marketingActive" => $marketing['isActive'] ? 1 : 0,
                "marketingFrom" => "{$marketing['periodFrom']}",
                "marketingTo" => "{$marketing['periodTo']}",
                "orderActionCondition" => str_replace(array(":\"{", "}\""), array(":{", "}"), json_encode($marketing['orderActionConditionBindings']))
            );
            if ($programsData[$marketing['id']]) {
                $query = "";
                foreach ($programStruct as $field => $val) {
                    $query .= ($query ? "," : "")."{$field} = '{$val}'";
                }
                $db->query("UPDATE Message2096 SET {$query} WHERE Message_ID = {$programsData[$marketing['id']]['Message_ID']}");
                $programsData[$marketing['id']]['inIiko'] = true;
            } else {
                $queryField = $queryVal = "";
                foreach ($programStruct as $field => $val) {
                    $queryField .= ($queryField ? "," : "").$field;
                    $queryVal .= ($queryVal ? "," : "")."'{$val}'";
                }
                $db->query("INSERT INTO Message2096 ({$queryField}) VALUES ({$queryVal})");
            }
    	}
    }
    foreach ($programsData as $program) {
        if (!$program['inIiko']) $db->query("DELETE FROM Message2096 WHERE Message_ID = {$program['Message_ID']}");
    }
    $db->query("UPDATE Catalogue SET time_update_program = '".date("Y-m-d H:i:s")."' WHERE Catalogue_ID = 604");
    echo "программы обновлены";
}
exit;














/*



#Init iiko
define('N', "\n");
$force = 0;
if ($_GET['force']==1) $force++;
if (!function_exists('IIKO_get')){
	define('IIKO', 'IIKO');
	include 'iiko_lib.php';
	iiko_getToken($login, $pass, $token_path, $force);
}
if (!$iiko_token)
	die($form.'Авторизация не выполнена');
#End init iiko

#################################
include 'fun.php';
#################################


#Получаю меню с разделами
$menu = IIKO_URL.'nomenclature/'.$iiko_orgid.'?access_token='.$iiko_token;
$stopList = IIKO_get(IIKO_URL."stopLists/getDeliveryStopList?access_token={$iiko_token}&organization={$iiko_orgid}");

$menu = IIKO_get($menu);
if (!isset($_GET['debug']) && !isset($menu['uploadDate']))
	die($form.'Номенклатура не доступна<hr>'.$menu);
if ($_GET['debug']=='menu')
	die($form.'<pre>'.json_encode($menu).'</pre><pre>'.json_encode($stopList).'</pre><br>json<br>');
else if($_GET['stopList']){
	# stop-list
	if($stopList){
		$stopItems = "";
		foreach ($stopList[stopList] as $stopListItem) {
			if($stopListItem['organizationId']=="67b4a396-ceae-11e7-80df-d8d38565926f") $stopItems = $stopListItem['items'];
		}
		$db->query("UPDATE Message2001 SET nohave = '0'");
		if($stopItems){
			foreach ($stopItems as $itm) {
				if($itm[balance] <= 0) $db->query("UPDATE Message2001 SET nohave = '1' WHERE code = '{$itm['productId']}'");
			}
		}
	}
	die("Стоп-лист обновлен");
}else {
	if ($_GET['auto']!='no' && file_exists($timeStamp)){
		$timeStamp_time = intval(file_get_contents($timeStamp));
		$timeStamp_new = strtotime($menu['uploadDate']);
		if ($timeStamp_new<=$timeStamp_time)
			die('Выгрузка уже была произведена, дата обновления меню: '.date('d.m.Y H:i:s', $timeStamp_time));
	}
}

*/

/*
$groups = array();
#Сортировка разделов
$menu['groups'] = _sortGroups($menu['groups']);
if ($_GET['drop'])
	die('<pre>'.print_r($menu['groups'],1).'</pre>');
#Обновляю/создаю дополнительные ингредиенты
foreach($menu['products'] as $product){
	if ($product['type']!='modifier')
		continue;
	#Vars
	$code = $product['id'];
	$name = addslashes($product['name']);
	$price = $product['price'];
	$_art = $product['code'];

	#Create or update
	$id = $db->get_var("SELECT Message_ID FROM Message{$dop_class} WHERE code='".$code."'");
	if ($id)
		$db->query("UPDATE Message{$dop_class} SET name = '".$name."', price = ".$price." WHERE Message_ID=".$id);
	else {
		$priority = $db->get_var("SELECT COUNT(*) FROM Message{$dop_class}");
		$priority++;
		$db->query("INSERT INTO Message{$dop_class} (name, price, code, _art, User_ID, Subdivision_ID, Sub_Class_ID, Priority)
			VALUES ('".$name."', ".$price.", '".$code."', '".$_art."', 428, ".$dop_sub.", ".$dop_cc.", ".$priority.")");
		$id = $db->insert_id;
	}

	#Img
	if ($product['images']){
		$_img = '';
		$img = $product['images'][0];
		$img_name = $img['imageId'].'_iiko.jpg';
		if (!file_exists($pathImport.$img_name)){
			$img = file_get_contents($img['imageUrl']);
			file_put_contents($pathImport.$img_name, $img);
			$_img = $img_name;
		}
		else {
			$_dateCreate = filectime($pathImport.$img_name);
			$_dateUpload = strtotime($img['uploadDate']);
			if ($_dateUpload>$_dateCreate){
				$img = file_get_contents($img['imageUrl']);
				file_put_contents($pathImport.$img_name, $img);
				$_img = $img_name;
			}
		}
		if ($_img)
			$db->query("UPDATE Message{$dop_class} SET photourl='".$_img."' WHERE Message_ID=".$id);
	}
}
#Обновляю/создаю товары
foreach($menu['products'] as $product){

	if ($product['type']=='modifier')
		continue;
	#Vars
	$code = $product['id'];
	$art = $product['code'];
	$name = addslashes($product['name']);
	$price = $product['price'];
	$descr = $product['description']."\n".$product['additionalInfo'];
	$weight = $product['weight'];
	$carbohydrateAmount = $product['carbohydrateAmount'];
	$carbohydrateFullAmount = $product['carbohydrateFullAmount'];
	$energyAmount = $product['energyAmount'];
	$energyFullAmount = $product['energyFullAmount'];
	$fatAmount = $product['fatAmount'];
	$fatFullAmount = $product['fatFullAmount'];
	$fiberAmount = $product['fiberAmount'];
	$fiberFullAmount= $product['fiberFullAmount'];

	#Sub and cc
	$_sub = $_cc = 0;
	$razd = _getSubID($product['parentGroup']);
	if ($razd){
		$_sub = $razd['sub'];
		$_cc = $razd['cc'];
	}
	else
		continue;

	$priority = $product['order'];
	#Create or update
	$id = $db->get_var("SELECT Message_ID FROM Message2001 WHERE code='".$code."'");
	if ($id){
		$db->query("UPDATE Message2001 SET
			name = '".$name."',
			descr = '".$descr."',
			price = ".$price.",
			Subdivision_ID = ".$_sub.",
			Sub_Class_ID = ".$_cc.",
			Checked = 1,
			art = '".$art."',
			ves = '".$weight."',
			Priority = ".$priority.",
			uglevodi_1 = '".$carbohydrateAmount."',
			uglevodi_2 = '".$carbohydrateFullAmount."',
			zhiri_1 = '".$fatAmount."',
			zhiri_2 = '".$fatFullAmount."',
			belki_1 = '".$fiberAmount."',
			belki_2 = '".$fiberFullAmount."',
			kkal_1 = '".$energyAmount."',
			kkal_2 = '".$energyFullAmount."'
		WHERE Message_ID=".$id);
	}
	else {
		#$priority = $db->get_var("SELECT COUNT(*) FROM Message2001 WHERE Subdivision_ID=".$_sub);
		#if (!$priority)
		#	$priority = 0;
		$sql = "INSERT INTO Message2001
			(name, price, descr, Subdivision_ID, Sub_Class_ID, Checked, code, art, ves, Priority, uglevodi_1, uglevodi_2, zhiri_1, zhiri_2, belki_1, belki_2, kkal_1, kkal_2)
		VALUES
			('".$name."', ".$price.", '".$descr."', ".$_sub.", ".$_cc.", 1, '".$code."', '".$art."', '".$weight."', ".$priority.", '".$carbohydrateAmount."', '".$carbohydrateFullAmount."', '".$fatAmount."', '".$fatFullAmount."', '".$fiberAmount."', '".$fiberFullAmount."', '".$energyAmount."', '".$energyFullAmount."')";
		$db->query($sql);
		$id = $db->insert_id;
	}

	#Img
	$_img = array();
	if ($product['images']){
		foreach(array_reverse($product['images']) as $img){

			$dateUpload = strtotime($img['uploadDate']);
			$img_name = $img['imageId'].'_'.$dateUpload.'_iiko.jpg';

			if (!file_exists($pathImport.$img_name)){
				$img = file_get_contents($img['imageUrl']);
				file_put_contents($pathImport.$img_name, $img);
			}

			$_img[] = $img_name;
		}
		$db->query("UPDATE Message2001 SET photourl='".implode(',', $_img)."' WHERE Message_ID=".$id);
	}

	#Dops
	if ($product['modifiers'] || $product['groupModifiers']){
		$dops = array();
		foreach($product['modifiers'] as $mod){
			$modID = $db->get_var("SELECT Message_ID FROM Message{$dop_class} WHERE code='".$mod['modifierId']."'");
			$dops[$modID] = array($mod['minAmount'], $mod['maxAmount']);
		}
		foreach($product['groupModifiers'] as $mod){
			foreach($mod['childModifiers'] as $chld){
				$modID = $db->get_var("SELECT Message_ID FROM Message{$dop_class} WHERE code='".$chld['modifierId']."'");
				$_n = 'NoName';
				$dops[$modID] = array($mod['minAmount'], $mod['maxAmount'], $chld['modifierId'], $_n);
			}
		}
		$dops = json_encode($dops);
		$db->query("UPDATE Message2001 SET dops='".$dops."' WHERE Message_ID=".$id);
	}
}*/

/*
$db->query("
	UPDATE
	Subdivision
	SET Checked = '0'
	WHERE
	code1C = 'ffd515e8-a1b7-440e-986c-5a201f13e2e2'
");
*/
/*file_put_contents($timeStamp, strtotime($menu['uploadDate']));
die($form.'Выгрузка произведена');
*/
