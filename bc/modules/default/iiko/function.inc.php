<?
    global $iiko_authurl; #токен + орг ID вместе для запроса по http access_token=...&organization=...
    define('IIKO_URL', 'https://iiko.biz:9900/api/0/');
    define('PLATIUS_URL', 'https://api.platius.ru:9900/api/0/');

    require_once "checkConditions_class.php";

    function IIKO_get($url, $post='', $debug=0, $json=0, $timeout=60){
    	$process = curl_init($url);
    	if ($json) curl_setopt($process, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));
    	curl_setopt($process, CURLOPT_TIMEOUT, $timeout);
    	curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
    	if ($debug) curl_setopt($process, CURLOPT_HEADER, 1);
    	if ($post){
    		curl_setopt($process, CURLOPT_POST, 1);
    		curl_setopt($process, CURLOPT_POSTFIELDS, $post);
    	}
    	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    	$return = curl_exec($process);
    	curl_close($process);
    	if (substr($return,0,1)=='[' or substr($return,0,1)=='{') return json_decode($return, 1);
    	return $return;
    }

    # get token / organization
    function iiko_getToken($force = 0){
        global $db, $catalogue, $iiko_token, $iiko_orgid, $iiko_authurl;

        $curnt= $db->get_row("SELECT iiko_login, iiko_pass FROM Catalogue WHERE Catalogue_ID = {$catalogue}", ARRAY_A);
        $iiko_login = $curnt['iiko_login'];
        $iiko_pass = $curnt['iiko_pass'];

        $token_path = __DIR__.'/token.ini';
    	$cur_time = time();
    	$file_time = filectime($token_path);
    	if ($cur_time-$file_time>=900 || $force || !$file_time){
    		$token = trim(IIKO_get(IIKO_URL.'auth/access_token?user_id='.$iiko_login.'&user_secret='.$iiko_pass), '"');
    		file_put_contents($token_path, "<?/*{$token}*/?>");
    	}else{
    		$token = trim(trim(file_get_contents($token_path), '*/?>'), '<?/*');
        }
    	#Получение организаций
    	$orgs = IIKO_get(IIKO_URL.'organization/list?access_token='.$token.'&request_timeout=1');
    	#die(print_r($orgs,1));
    	if ($orgs){
    		$org_id = $orgs[0]['id'];
    		#Параметры для авторизации
    		$iiko_token = $token;
    		$iiko_orgid = $org_id;
    		$iiko_authurl = 'access_token='.$token.'&organization='.$org_id;
    	}else{
    		$iiko_token = false;
    		return false;
    	}
    }

    # сортировка групп
    function _sortGroups($arr, $parent=0){
        global $db, $catalogue;
        $_ar = array();
        if(is_array($arr)) foreach($arr as $ar){
            if (!$parent){
                if (empty($ar['parentGroup'])) $_ar[] = $ar;
            }
            else {
                if ($ar['parentGroup']==$parent) $_ar[] = $ar;
            }
        }
        $__ar = array();
        foreach($_ar as $k => $_t){
            $_ar[$k]['childs'] = _sortGroups($arr, $_t['id']);
        }
        return $_ar;
    }
    function _offAll(){
    	global $db;
    	$db->query("UPDATE Subdivision SET Checked=0 WHERE code1C != ''");
    	$db->query("UPDATE Message2001 SET Checked=0");
    }
    function _updateSubs($arr, $parent){
    	global $db, $catalogue, $ROOTDIR, $pathInc;

        # сбрасываем тэги у разделов
        $db->query("UPDATE Subdivision SET tags = ''");

    	if(is_array($arr)) foreach($arr as $group){
    		//if (!$group['isIncludedInMenu']) continue;
    		$img = '';
    		if (!empty($group['images'])) $img = $group['images'];
    		$parent_ = _createSub($group['name'], $parent, $group['id'], $img, $group['order'], $group['isIncludedInMenu']);
    		if (!empty($group['childs']) && isset($parent_['sub'])) _updateSubs($group['childs'], $parent_['sub']);
    	}

        $ids = explode(',', $db->get_var("SELECT ignoreIDs FROM Catalogue WHERE Catalogue_ID={$catalogue}"));
        if(count($ids) > 0) foreach ($ids as $id) {
            if($id) $db->query("UPDATE Subdivision SET Checked=0 WHERE code1C = '{$id}'");
        }
    }
    function _createSub($name, $parent, $code, $img, $priority=0, $isIncludedInMenu){
    	global $db, $catalogue, $ROOTDIR, $pathInc;
    	$engUrl = $engName = encodestring($name, 1);
    	$sub = $db->get_var("SELECT Subdivision_ID FROM Subdivision WHERE code1C='".$code."'");
        $checked = $isIncludedInMenu ? 1 : 0;
    	if ($sub){
    		$db->query("UPDATE Subdivision SET Checked='{$checked}', Parent_Sub_ID=".$parent.", Priority=".$priority.", Subdivision_Name ='{$name}' WHERE Subdivision_ID=".$sub);
    		$cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID=".$sub);
    		if (!$cc){
    			$addccsql = "INSERT INTO Sub_Class
    				(Subdivision_ID, Class_ID, Sub_Class_Name, EnglishName, Checked, Catalogue_ID, AllowTags, DefaultAction, NL2BR, UseCaptcha, CacheForUser, Class_Template_ID)
    			VALUES
    			('".$sub."','2001','".addslashes($name)."','".$engName."',1,'".$catalogue."','-1','index','-1','-1','-1','0')";
    			$db->query($addccsql);
    			$cc = $db->insert_id;
    			if (!$cc) return false;
    		}
    		_setSubImg($sub, $img);
    		return array('sub'=>$sub, 'cc'=>$cc);
    	}

        #check 
        $haveUrl = $db->get_var("SELECT Subdivision_ID FROM Subdivision WHERE EnglishName='".$engName."'");
        if($haveUrl) {
            $rand = rand(1000, 9999);
            $engUrl .= $rand;
            $engName .= $rand;
        }

        
    	$Hidden_URL = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID=".$parent);
    	$Hidden_URL .= $engUrl.'/';
    	$addsubsql = "INSERT INTO Subdivision
    		(Catalogue_ID, Parent_Sub_ID, Subdivision_Name, Priority, Checked, EnglishName, Hidden_URL, code1C, subdir)
    	VALUES
    		('".$catalogue."',".$parent.",'".addslashes($name)."','".$priority."','{$checked}','".$engName."', '".$Hidden_URL."', '".$code."', '3')";
    	$db->query($addsubsql);
    	$sub = $db->insert_id;
    	if (!$sub)
    		return false;
    	$addccsql = "INSERT INTO Sub_Class
    		(Subdivision_ID, Class_ID, Sub_Class_Name, EnglishName, Checked, Catalogue_ID, AllowTags, DefaultAction, NL2BR, UseCaptcha, CacheForUser, Class_Template_ID)
    	VALUES
    	('".$sub."','2001','".addslashes($name)."','".$engName."',1,'".$catalogue."','-1','index','-1','-1','-1','0')";
    	$db->query($addccsql);
    	$cc = $db->insert_id;
    	if (!$cc)
    		return false;
    	_setSubImg($sub, $img);
    	return array('sub'=>$sub, 'cc'=>$cc);
    }
    function _setSubImg($sub, $img){
    	global $db, $ROOTDIR, $pathInc;
    	if ($img){
    		$_img = $img[count($img)-1];
    		$img = $_img['imageId'];
    		if (!file_exists($ROOTDIR.$pathInc.'/files/'.$sub.'/'.$img.'.jpg')){
    			if (!file_exists($ROOTDIR.$pathInc.'/files/'.$sub))
    				mkdir($ROOTDIR.$pathInc.'/files/'.$sub);
    			$file = file_get_contents($_img['imageUrl']);
    			file_put_contents($ROOTDIR.$pathInc.'/files/'.$sub.'/'.$img.'.jpg', $file);
    		}
    		$img = $img.'jpg:image/jpeg:10101:'.$sub.'/'.$img.'.jpg';
    		$db->query("UPDATE Subdivision SET img='".$img."' WHERE Subdivision_ID=".$sub);
    	}
    	else
    		$db->query("UPDATE Subdivision SET img='' WHERE Subdivision_ID=".$sub);
    }
    function _getSubID($code){
    	global $db;
    	$sub = $db->get_var("SELECT Subdivision_ID FROM Subdivision WHERE code1C='".$code."'");
    	if (!$sub)
    		return false;
    	$cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID=".$sub);
    	if (!$cc)
    		return false;
    	return array('sub'=>$sub, 'cc'=>$cc);
    }

    function iiko_updateUser($post=array()){ #как обновление так и создание
    	global $iiko_authurl;
    	iiko_getToken();
    	$post = '{"customer":'.json_encode($post).'}';
    	$user = IIKO_get(IIKO_URL.'customers/create_or_update?'.$iiko_authurl, $post, 0, 1);
        return $user;
    	if (isset($user['id'])) return $user;
    	return false;
    }
    function iiko_getUser($phone='', $id='', $card=''){
    	global $iiko_authurl, $current_user, $AUTH_USER_ID;

    	iiko_getToken();

    	if ($phone) {
    		$word = 'phone';
    		$var = $phone;
    	} elseif ($id) {
    		$word = 'id';
    		$var = $id;
    	} elseif ($card) {
            $word = 'card';
            $var = $card;
        } else {
            return false;
        }
    	$user = IIKO_get(IIKO_URL.'customers/get_customer_by_'.$word.'?'.$iiko_authurl.'&'.$word.'='.$var);
        return $user;
    	if (isset($user['id'])) return $user;
    	return false;
    }
    function iiko_getAddress($city, $street){
    	global $iiko_authurl;

    	if (!$iiko_authurl) return false;

    	$cities = IIKO_URL.'cities/cities?'.$iiko_authurl;
    	$cities = IIKO_get($cities);
    	$r = array();
    	if ($cities){
    		foreach($cities as $_city){
    			if ($city==$_city['city']['name']){
    				$r['cityID'] = $_city['city']['id'];
    				foreach($_city['streets'] as $_street){
    					if ($street==$_street['name']){
    						$r['streetID'] = $_street['id'];
    						break;
    					}
    				}
    				break;
    			}
    		}
    		return $r;
    	}
    	return false;
    }
    function iiko_getBalance($type, $val, $check='')
    {
        global $iiko_token, $iiko_orgid, $iiko_authurl;

        if (!$check && $_SESSION['iikoBonus'] && $_SESSION['iikoBonus']['upTime'] + 300 > time()) return $_SESSION['iikoBonus']['balance'];

        switch ($type) {
            case 'phone':
                $user = iiko_getUser($val);
                break;
            case 'id':
                $user = iiko_getUser(false, $val);
                break;
            default:
                return 'неверный тип';
        }

        if ($user['walletBalances']) {
            foreach ($user['walletBalances'] as $value) {
                if ($value['wallet']['programType'] == 'Bonus') {
                    $balance = $value['balance'];
                    break;
                }
            }
        }
        if (isset($balance)) $_SESSION['iikoBonus'] = array('balance' => $balance, 'upTime' => time());

        return ($balance ? $balance : 0);
    }
    function iiko_withdrawBalance($type, $val, $with_bal)
    {
        global $iiko_token, $iiko_orgid, $iiko_authurl;
        iiko_getToken();

        switch ($type) {
            case 'phone':
                $user = iiko_getUser($val);
                break;
            case 'id':
                $user = iiko_getUser(false, $val);
                break;
            default:
                return 'неверный тип';
        }
        foreach ($user['walletBalances'] as $value) {
            if ($value['wallet']['programType'] == 'Bonus') {
                $walId = $value['wallet']['id'];
                break;
            }
        }
        $post = array(
                    'organizationId' => $iiko_orgid,
                    'customerId'     => $user['id'],
                    'walletId'       => $walId,
                    'sum'            => $with_bal #'sum'=> прибавляет к текущему балансу, не заменяет
                    );
        return IIKO_get(IIKO_URL."customers/withdraw_balance?access_token={$iiko_token}",json_encode($post),0,1);
    }

    function iiko_getHistoryOrders($phone)
    {
        global $iiko_token, $iiko_orgid;
        iiko_getToken();
        return IIKO_get(IIKO_URL."orders/deliveryHistoryByPhone?access_token={$iiko_token}&organization={$iiko_orgid}&phone={$phone}");
    }

    # получение ограничения оплаты бонусами (\/)_i_i_(\/)
    function iiko_getBonusLimit($phone) {
        global $db;
        $programWithPayLimit = $db->get_col("SELECT orderActionCondition FROM Message2096 WHERE orderActionCondition LIKE '%PaymentLimitPercent%'");
        if ($programWithPayLimit) {
            $actionsConds = array();
            foreach ($programWithPayLimit as $program) {
                foreach (orderArray($program) as $actionCondGroup) {
                    foreach ($actionCondGroup['actions'] as $action) {
                        if (isset($action['settings']['PaymentLimitPercent'])) {
                            $actionsConds[] = array(
                                'limit' => $action['settings']['PaymentLimitPercent'],
                                'conditions' => $actionCondGroup['conditions']
                            );
                        }
                    }
                }
            }
            usort($actionsConds, function($a, $b){
                return ($a['limit'] - $b['limit'])*(-1);
            });
            $checkCond = new IikoCheckCondition(array("phone" => $phone));
            foreach ($actionsConds as $group) {
                $success = true;
                foreach ($group['conditions'] as $condition) {
                    if(!$checkCond->checkCondition($condition)['success']) {
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $_SESSION['iiko']['bonusLimit'] = array(
                        'val' => $group['limit'],
                        'time' => (new \DateTime())->format("U")
                    );
                    $limit = $group['limit'];
                    break;
                }
            }
        }
        return $limit ? (int)$limit : 0;
    }
?>
