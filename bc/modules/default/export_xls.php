<?php
# EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL EXCEL
function excelPrice($fromxls, $fileURL, $sheetId='0', $classId, $startKey, $baseSub, $groupCol, $xmlfields, $startColumn='1', $update='',$curSubManual='', $curCCManual='', $ncctpl='', $currency=0, $spaceline = '', $updateSub2 = '') {
	clearSeoCach();
	ini_set('memory_limit', '1200M');
	set_time_limit(0);
	global $db, $curSub, $curCC, $reslt, $DOCUMENT_ROOT, $DOCUMENT_ROOT1, $catalogue, $pathInc, $setting, $space, $AUTH_USER_ID, $baseSubURL;

  while (ob_get_level() > 0) {
    ob_end_flush();
  }

  echo " ";
	flush();
	ob_flush();

	$logPath = $DOCUMENT_ROOT.$pathInc.'/files/logExcel.log';

	$customColsParams = array_filter(explode(',', preg_replace('/\s/', '', $xmlfields)));

	$arrNameZam = array("#"=>" ","_"=>" ","="=>" ","!"=>" ");

	if($baseSub) $baseSubURL = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID = '{$baseSub}' AND Catalogue_ID = {$catalogue}");

	$reslt = "$xmlfields | $fileURL | $curSubManual - $curCCManual \n----\n ";
	file_put_contents($logPath, $reslt); unset($reslt);

	require_once $DOCUMENT_ROOT1.'/PHPExcel/reader.php';
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('utf-8');
	$data->setUTFEncoder('mb');
	$data->read($DOCUMENT_ROOT.$fileURL);
	$start=0;
	$clearSub = -1;

	$reslt = "fileURL = $fileURL
		sheetId = $sheetId
		classId = $classId
		startKey = $startKey
		baseSub = $baseSub
		groupCol = $groupCol
		xmlfields = $xmlfields
		startColumn = $startColumn
		update = $update
		curSubManual = $curSubManual
		curCCManual = $curCCManual
		ncctpl = $ncctpl
		spaceline = $spaceline
		updateSub = $updateSub2\n";

	$reslt .= $DOCUMENT_ROOT.$fileURL."\n";
	file_put_contents($logPath, $reslt, FILE_APPEND);unset($reslt);
	// Подготовка массива с параметрами
	foreach ($setting['lists_params'] as $value) {
		$paramsList[$value['keyword']] = $value;
	}

	if(mb_strstr($spaceline,':')){
		$spaceline = ltrim($spaceline, "(");
		$spaceline = rtrim($spaceline, ")");
		$space = explode(":", $spaceline); # массив с отступами для разедлов
	}
	if ($data->sheets[$sheetId]['numRows'] > 1) {
		if(!$update) { // очистим каталог, если не обновление
			$db->query("delete from Message{$classId} where fromxls = '{$fromxls}' AND xlslist = '{$sheetId}' AND Catalogue_ID = '{$catalogue}'");
		}
		$db->query("UPDATE Subdivision SET Checked = 0 WHERE Catalogue_ID = {$catalogue} AND Hidden_URL LIKE '{$baseSubURL}%' AND code1C != ''");

		// очистить множественные разделы (связи создадутся по-новой)
		 $db->query("update Message{$classId} set Subdivision_IDS = '' ".($update!=4 && $update!=5 ? ", Checked = 0" : NULL)." where fromxls = '{$fromxls}' AND xlslist = '{$sheetId}' AND Catalogue_ID = '{$catalogue}'");
		
		$reslt .= "Строки найдены\n";
	} else $reslt .= "\nСТРОКИ НЕ НАЙДЕНЫ!";

	foreach ($data->sheets[$sheetId]['cells'] as  $cell) {
		echo " ";
		flush();
		ob_flush();
		
		// Шапка
		if(mb_stristr_in_array($startKey, $cell)) {
			foreach ($customColsParams as $key => $UploadFields) {
				if (mb_strstr($UploadFields,'param_')) {
					$col = $cell[$key + 1];
					if(!$paramsList[$UploadFields] && $col) {
						$paramsList[$UploadFields] = array('name' => $col, 'keyword' => $UploadFields);
					}
				}
			}
			$setting['lists_params'] = array_values($paramsList);
			setSettings($setting);

			$start=1; $reslt .= "Шапка найдена\n";
			file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
			continue;
		}
		
		if(!$start) continue;

		// это раздел
		if(count($cell) == 1 && trim($cell[$groupCol])) {
			$nameSundivision = trim($cell[$groupCol]);
			if($space){
				$iszag = 1; $zagnum = 0;
				foreach ($space as $key => $sp) {
					if (preg_match("/^(".$sp.")[\s\S]+$/i", $nameSundivision)) {
						$zagnum = $key;
						$zag[$zagnum] = trim(preg_replace("/^(".$space[$zagnum].")([\s\S]+)$/i", '${2}', $nameSundivision));
						break;
					}
				}
				foreach ($zag as $k => $v) if($zagnum<$k) unset($zag[$k]);
			}else{
				$iszag++;
				if ($iszag==2) {
					$zag[1]=$zag[0];
				}
				if ($iszag==3) {
					$zag[2]=$zag[1];
					$zag[1]=$zag[0];
				}
				if ($iszag==4) {
					$zag[3]=$zag[2];
					$zag[2]=$zag[1];
					$zag[1]=$zag[0];
				}
				if ($iszag==5) {
					$zag[4]=$zag[3];
					$zag[3]=$zag[2];
					$zag[2]=$zag[1];
					$zag[1]=$zag[0];
				}
				$zag[0] = $nameSundivision;
			}
		}

		// это товар
		if(count($cell) > 1){
			if ($iszag) {
				$zagAdd = (!$space ? array_reverse($zag) : $zag);
				$reslt .= addSub($zagAdd, $ncctpl, $baseSub, $classId, $updateSub2);
				file_put_contents($logPath, $reslt, FILE_APPEND); unset($reslt);
			}
			$iszag = NULL;
			$item = array();
			$priority++;
			foreach ($customColsParams as $key => $UploadFields) { #перебор колонок, данные полей
				
				$col = addslashes($cell[$key + 1]);

				switch ($UploadFields) {
					case 'name':

						$reslt.= "Наименования товара !!! {$col} !!! ";

						if ($col) {
							$item['name'] = trim(strtr($col,$arrNameZam));
							$tmp_keyw = ($tmp_keyw ? $tmp_keyw."-" : NULL).$col;
						}
						break;

					case 'Message_ID':

						$item['Message_ID'] = (int)$col;
						break;

					case 'art':
						if ($col && trim($col)!='') $item['art'] = $col;
						$tmp_keyw = ($tmp_keyw ? $tmp_keyw."-" : NULL).$col;
						break;

					case 'code':
						if(!$item['art']) $tmp_keyw = ($tmp_keyw ? $tmp_keyw."-" : NULL).$col;
						break;
						
					case 'text':
						if ($col) {
							$col = str_replace("\r\n","<br>\r\n",$col);
							$col = str_replace("\n","<br>\r\n",$col);
							$col = str_replace("\r","<br>\r\n",$col);
							$item[$UploadFields] = $col;
						}
						break;

					case stristr($UploadFields,"price"):

						if (stristr($col,"дог")) $item['dogovor'] = 1;
						$item[$UploadFields] = str_replace(",", ".", preg_replace("([^0-9,\.])", "", $col));
						break;

					case stristr($UploadFields,"stock"):

						if(stristr(trim($col),'Да')) $col = 1;
						if(stristr(trim($col),'Нет')) $col = 0;
						$col = preg_replace("([^0-9,\.])","",$col);
						$item[$UploadFields] = round($col);
						break;

					case stristr($UploadFields,"currency"):
						if (stristr(trim($col),'$') || stristr(trim($col),'dollar') || stristr(trim($col),'Доллар')) $col = 2;
						if (stristr(trim($col),'€') || stristr(trim($col),'euro') || stristr(trim($col),'ЕВРО')) $col = 3;
						if (!is_numeric($col)) $col = '';
						$item[$UploadFields] = $col;
						break;

					# динамические параметры
					case mb_strstr($UploadFields,'param_'):

						$paramItemList[$UploadFields] = str_replace('|', '', $col);
						break;
						
					case stristr($UploadFields,"name_"):
						unset($ie);
						$ie = (int)str_replace('name_', '',$UploadFields);
						$params[$ie]['name'] = $col;
						break;

					case stristr($UploadFields,"var_"):

						$ie = (int)str_replace('var_', '',$UploadFields);
						$params[$ie]['var'] = $col;
						break;

					case stristr($UploadFields,"edizm_"):

						$ie = (int)str_replace('edizm_', '',$UploadFields);
						$params[$ie]['edizm'] = $col;
						break;

					case stristr($UploadFields,"cityprice_"):

						$citypriceArr[$cityprice_cnt]['price'] = $col;
						$cityprice_cnt++;
						break;

					case $UploadFields == "variable":
						if (!stristr($col, "]")) {
							$resvariable = "";
							$rzd = stristr($col, ";") ? ";" : ",";
							foreach (explode($rzd, $col) as $value) {
								$resvariable .= ($resvariable ? "," : "").'{"name":"'.$value.'","price":"","stock":""}';
							}
							$resvariable = "[".$resvariable."]";
						} else {
							$resvariable = $col;
						}
						$item[$UploadFields]  = $resvariable;
						break;
					case 'order_count_price':	
						$orderCountPrice = [];	
						foreach (explode(';', $col) as $val) {	
								if (!$val || strpos($val, '-') === false) continue;	
								$countPrice = explode('-', $val);	
								$orderCountPrice[] = [	
										'count' => (int) $countPrice[0],	
										'price' => round((float) str_replace(',', '.', $countPrice[1]), 2),	
								];	
						}	
						$item['order_count_price'] = $orderCountPrice ? json_encode($orderCountPrice) : null;	
						unset($orderCountPrice);	
						break;	
					default:
						$item[$UploadFields]  = $col ? trim($col) : NULL;
						break;
				}
			}

			// ключевое слово
			$keyw = encodestring($tmp_keyw,1);
			// обрезаем до 255
			$keyw = substr($keyw, 0, 255);
			//Добовляем доп. параметры
			if($paramItemList) {
				$SQLParamList = '';
				foreach ($paramItemList as $key => $value) {
					if(trim($value)) $SQLParamList .= trim($key)."||".trim($value)."|\r\n";
				}
				$item['params'] =  $SQLParamList;
			}
			//Добовляем доп. параметры json // хз зачем если колонка зянята другим форматом
			if($params && !$paramItemList) $item['params'] =  json_encode($params);
			//Цены для городов
			if($citypriceArr) $item['pricecity'] =  json_encode($citypriceArr);

			$item['Keyword'] = $keyw;

			unset($keyw); unset($tmp_keyw);  unset($params); unset($paramItemList); unset($parcitypriceArrams);

			if ($item['Keyword'] && ($item['name'] || ($item['art'] && $update==5))) {
				

				$tovar = $db->get_row("SELECT Message_ID AS id, Keyword, Checked, Subdivision_IDS FROM Message{$classId} WHERE (Keyword = '{$item['Keyword']}' OR Keyword = '".tiredel($item['Keyword'])."') AND Catalogue_ID = '{$catalogue}'", ARRAY_A);

				if (!$tovar['id']) {
					if($update!=5 && $update!=3) {
						$item['User_ID'] = 1;
						$item['Catalogue_ID'] = $catalogue;
						$item['Priority'] = $priority;
						$item['xlslist'] = $sheetId;
						$item['Subdivision_ID'] = ($curSub ? $curSub : $curSubManual);
						$item['Sub_Class_ID'] = ($curCC ? $curCC : $curCCManual);
						$item['fromxls'] = $fromxls;
						$item['Keyword'] = tiredel($item['Keyword']);
						if(!$item['Checked']) $item['Checked'] = 1;

						$key = implode(',', array_keys($item));
						$val = implode(',', escapeArr($item));

						$sql = "INSERT INTO Message{$classId} ({$key}) VALUE ($val)";

						$db->query($sql);
						$reslt .= "\n{$sql}\n";
					}
				} else {
					if ($tovar['Checked'] != 1) { # изменить если товар есть TO-DO

						if($item['art']) $item['artnull'] = preg_replace('/[^a-zA-Zа-яёЁА-Я0-9]/ui', '', $artikulbar);

						if (!$update || $update==1) {
							$item['Subdivision_ID'] = ($curSub ? $curSub : $curSubManual);
							$item['Sub_Class_ID'] = ($curCC ? $curCC : $curCCManual);
						}

						if ($update==3) {
							$item = get_mb_stristr_key('price', $item);
						}

					} else {
						$item['Subdivision_IDS'] = $tovar['Subdivision_IDS'].(!$tovar['Subdivision_IDS'] ? "," : NULL).($curSub ? $curSub : $curSubManual).',';
					}

					$whereUpdate = "Message_ID = '{$tovar['id']}'";
					if($update!=4) $whereUpdate = "xlslist IS NOT NULL AND (fromxls = '".$fromxls."' OR fromxls = '1') AND {$whereUpdate}";
					if ($update==5 && $item['art']) $whereUpdate = "fromxls > 0 AND art = '{$item['art']}'";

					if(!$item['Checked']) $item['Checked'] = 1;
					$item['fromxls'] = $fromxls;
					unset($item['name']);
					$setUpdate = implode(',', escapeUpdateArr($item));
					$sql = "UPDATE Message{$classId} SET {$setUpdate} WHERE {$whereUpdate} AND Catalogue_ID = '{$catalogue}'";
					$db->query($sql);

					$reslt .= "\n{$sql}\n";
				}

				   
			} else {
				$reslt .= "\nОшибка: нет имени или ключевого слова";
			}
			file_put_contents($logPath, $reslt, FILE_APPEND); 
			// file_put_contents($logPath, $db->debug(), FILE_APPEND);
			unset($reslt);
		}
	 
	}

	if ($pathInc) file_put_contents($logPath, $reslt, FILE_APPEND);
	clearCache('','', '', $catalogue);
	return $reslt;
}

function escapeUpdateArr($arr) {
	$resulte = array();
	foreach ($arr as $key => $value) {
		$resulte[$key] = $key." = '".str_replace("'", '"', $value)."'";
	}

	return $resulte;
}
function escapeArr($arr) {
	$resulte = array();
	foreach ($arr as $key => $value) {
		$resulte[$key] = "'".str_replace("'", '"', $value)."'";
	}

	return $resulte;
}

function mb_stristr_in_array($needle, $arr) {
	foreach ($arr as $value) {
		if(mb_stristr($value,$needle)) return true;
	}
	return false;
}
function get_mb_stristr_key($needle, $arr) {
	$resulte = array();
	foreach ($arr as $key => $value) {
		if(mb_stristr($key,$needle)) $resulte[$key] = $value;
	}
	return $resulte;
}

function addSub($grArr, $ncctpl, $baseSub, $classId, $updateSub) {
	global $db, $catalogue, $baseSubURL, $curSub, $curCC, $sheetId, $fromxls;

	$parentID = $baseSub;
	$parentHidURL = $baseSubURL;
	$subkey = '';

	foreach ($grArr as $key => $subName) {
		$curCC = $curSub = '';

		$url = $parentHidURL.encodestring($subName,1)."/";
		$subkey .= ($subkey ? "/" : NULL).$subName;
		$lowerMD5 = md5(mb_strtolower($subName));
		$MD5 = md5($subName);
		

		//Есть ли раздел
		$isSub = $db->get_row("SELECT Subdivision_ID, Hidden_URL FROM Subdivision WHERE Catalogue_ID = '{$catalogue}' AND (code1C like '{$subkey}' OR code1C = '".encodestring($subkey)."' OR Hidden_URL = '{$url}') OR code1C like '{$lowerMD5}' OR code1C like '{$MD5}'", ARRAY_A);


		if($isSub) {
			$resltt .= "\n\nРаздел уже создан {$parentID} {$subName} ".encodestring($subName,1)." {$url} {$lowerMD5}";
			$updateSub = 1;
			if($updateSub == 1 || $updateSub == 2) {
				//Есть ли такой url уже на сайте
				$isURL = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Hidden_URL = '{$url}' AND Catalogue_ID = {$catalogue}");

				$updateSql = "UPDATE  Subdivision  
							SET ".($updateSub == 1 ? "Subdivision_Name = '{$subName}', " : NULL)."
								".($isURL ? NULL : "Hidden_URL = '{$url}', ")."
								Parent_Sub_ID = {$parentID},
								Checked = 1
							WHERE Subdivision_ID = {$isSub['Subdivision_ID']} AND Catalogue_ID = '{$catalogue}'";
				$db->query($updateSql);
				$resltt .= "$updateSql\n".$updateSub;
			}

			$parentID = $curSub = $isSub['Subdivision_ID'];
			$parentHidURL = $isSub['Hidden_URL'];
			$curCC = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = '{$parentID}' AND Class_ID = {$classId}");
			$db->query("UPDATE Message2001 SET Checked = 0 WHERE Catalogue_ID = '{$catalogue}' AND Subdivision_ID = {$isSub['Subdivision_ID']} AND fromxls = '{$fromxls}' AND xlslist = '{$sheetId}'");
		} else {
			# добавить раздел, если он не существует
			if (!$isSub && $parentID && $lowerMD5 && $subName && $url) {
				$maxPriority =  $db->get_var("SELECT MAX(priority) FROM Subdivision WHERE Parent_Sub_ID = '{$parentID}'");
				$priority = $maxPriority ? $maxPriority : 0;

				$resltt .= "\n\nДобавить раздел {$parentID} {$subName} ".encodestring($subName,1)." {$url} {$lowerMD5}";
				$priority++;
				$addsubsql = "INSERT INTO Subdivision
						 (Catalogue_ID, Parent_Sub_ID, Subdivision_Name, Priority, Checked, EnglishName, Hidden_URL, code1C, subdir) VALUES
						 ('{$catalogue}', {$parentID}, '".addslashes($subName)."', '{$priority}', '1' ,'".encodestring($subName,1)."', '".$url."', '".($subkey ? $subkey : $lowerMD5)."', '3')";
						 
				$db->query($addsubsql);
				$resltt .= "$addsubsql\n";
				$curSub = $db->insert_id;
				$Hidden_UrlNew = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID = '{$curSub}'");

			}  
			# добавить компонент в раздел
			if (!$isSub && $classId && $curSub) {
				$addCCsql = "INSERT INTO Sub_Class (Subdivision_ID,Class_ID,Sub_Class_Name,EnglishName,Checked,Catalogue_ID,AllowTags,DefaultAction,NL2BR,UseCaptcha,CacheForUser,Class_Template_ID) VALUES
							('".$curSub."','".$classId."','{$lowerMD5}','item',1,'{$catalogue}','-1','index','-1','-1','-1','".$ncctpl."')";
				$db->query($addCCsql);
				$resltt .= "\n$addCCsql\n";
				$curCC =  $db->insert_id;
			}

			
			if ($curCC && $Hidden_UrlNew && $curSub) {
				$parentID = $curSub;
				$parentHidURL = $Hidden_UrlNew;
			} else {
				$parentID = $baseSub;
				$parentHidURL = $baseSubURL;
			}
		}
	}
 
	return $resltt;
}


#
function tiredel($word) {
	$word = str_replace("--","-",$word);
	if (strstr($word,"--")) {
		$word = tiredel($word);
	}
	return $word;
}


# ############ функция превода текста с кириллицы в траскрипт
function encodestring($string,$url='') {
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

   $output = str_replace(array_keys($table), array_values($table),trim($string));
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
