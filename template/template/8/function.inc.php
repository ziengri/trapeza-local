<?php
class Adminsites {
	# вызов действия
	public function init($action) {
		switch ($action) {
			case 'editsite':
				return $this->editsite($_GET[id]);
				exit;
			break;
			case 'savesite':
				return $this->savesite();
				exit;
			case 'removesite':
				return $this->removesite($_GET[id]);
				exit;
			case 'checkSites':
				return $this->checkSites();
				exit;
			break;
		};
	}
	// форма редактирования сайта
	private function editsite($id){
		global $db;
		if(!is_numeric((int)$id)) exit;

		$site = $db->get_row("SELECT * FROM Catalogue WHERE Catalogue_ID != 1 AND Login != 'sites' AND Catalogue_ID = {$id}", ARRAY_A);
		$sitetypes = $db->get_results("select sitetype_Name, sitetype_ID from Classificator_sitetype", ARRAY_A);
		foreach($sitetypes as $type) $sitetype .= "<option value='{$type[sitetype_ID]}' ".($type[sitetype_ID]==$site[sitetype] ? "selected" : "").">{$type[sitetype_Name]}</option>";

		$conceptPreview = nc_file_path('Catalogue', $id, 'conceptPreview');
		if($conceptPreview) $conceptPreview = str_replace("/sites/", "/{$site[login]}/", $conceptPreview);


		$category = $db->get_results("select category_Name, category_ID from Classificator_category", ARRAY_A);
		$site[conceptCategory] = $site[conceptCategory] ? explode(",", $site[conceptCategory]) : array();

		$html = "<form name='adminForm' class='ajax2' id='adminForm' enctype='multipart/form-data' method='post' action='/?sitesAction=savesite'>";
			$html .= "<input type='hidden' name='template' value=''>";
			$html .= "<input type='hidden' name='id' value='{$id}'>";
			$html .= "<ul class='tabs tabs-border tab-more-tabs'>
						<li class='tab'><a href='#tab_main'>Главное</a></li>
						<li class='tab'><a href='#tab_sett'>Настройки</a></li>
						<li class='tab'><a href='#tab_concept'>Концепт</a></li>
						<li class='tab'><a href='#tab_category'>Категории</a></li>
						<li class='tab'><a href='#tab_other'>Доработки</a></li>
					</ul>";
			$html .= "<div class='modal-body tabs-body'>";
				$html .= "<div id='tab_main'>";
					$html .= "<div class='colline colline-4'>".bc_radio("activePeriod", 1, "Активный", ($site[activePeriod] ? true : false))."</div>";
					$html .= "<div class='colline colline-4'>".bc_radio('activePeriod', 0, "Демо (автоудаление)", (!$site[activePeriod] ? true : false))."</div>";
					$html .= "<div class='colline colline-2'> </div>";
					$html .= "<div class='colline colline-2'>".bc_input("Catalogue_Name", $site[Catalogue_Name], "Название сайта", 1)."</div>";
					$html .= "<div class='colline colline-2'>".bc_select("sitetype", $sitetype, "Тип сайта", "class='ns'")."</div>";
					$html .= "<div class='colline colline-1'>".bc_date('renewalDate', $site[renewalDate], "Продлен до:", 1, 1)."</div>";
				$html .= "</div>";
				$html .= "<div id='tab_sett' class='none'>";
					$html .= "<div class='colline colline-3'>".bc_checkbox("colorScheme", 1, "Цветовая схема", $site[colorScheme])."</div>";
					$html .= "<div class='colline colline-3'>".bc_checkbox("fontScheme", 1, "Шрифтовая схема", $site[fontScheme])."</div>";
					$html .= "<div class='colline colline-3'>".bc_checkbox("loadJSfile", 1, "Свой js файл", $site[loadJSfile])."</div>";
					$html .= "<div class='colline colline-2'>".bc_checkbox("customCode", 1, "Возможность использовать собственный код", $site[customCode])."</div>";
					$html .= "<div class='colline colline-2'>".bc_checkbox("onlyInOffise", 1, "Доступ только из офиса", $site[onlyInOffise])."</div>";
					$html .= "<div class='colblock'>
									<h4>Расширения пользователя</h4>
									<div class='colline colline-4'>".bc_checkbox("zoneAndBlocks", 1, "Зоны и Блоки", $site[zoneAndBlocks])."</div>
									<div class='colline colline-4'>".bc_checkbox("seo", 1, "SEO", $site[seo])."</div>
									<div class='colline colline-4'>".bc_checkbox("design", 1, "Дизайн", $site[design])."</div>
								</div>";
				$html .= "</div>";
				$html .= "<div id='tab_concept' class='none'>";
					$html .= "<div class='colline colline-3'>".bc_checkbox("isConcept", 1, "Использовать как концепт", $site[isConcept])."</div>";
					$html .= "<div class='colline colline-1'>".bc_file("conceptPreview", $conceptPreview, "Превью концепта", $conceptPreview, 2892)."</div>";
       				$html .= "<div class='colline colline-height'>".bc_textarea("conceptText", $site[conceptText], "Описание концепта")."</div>";
				$html .= "</div>";
				$html .= "<div id='tab_other' class='none'>";
					$html .= "<div class='colline colline-height'>".bc_multi_line("dorabotki", $site[dorabotki] ? $site[dorabotki] : '{"cols":[{"col":2,"type":"input","title":"Наименование","name":"name"},{"col":2,"type":"input","title":"Цена","name":"price"}],"values":{}}', "Доработки и цены", 3)."</div>";
				$html .= "</div>";
				$html .= "<div id='tab_category' class='none'>";
					foreach ($category as $ctg) {
						$html .= "<div class='colline colline-3'>".bc_checkbox("category[]", $ctg[category_ID], $ctg[category_Name], in_array($ctg[category_ID], $site[conceptCategory]))."</div>";
					}
				$html .= "</div>";

			$html .= "</div>";
			$html .= "<div class='bc_submitblock'>";
				$html .= "<div class='result'></div>";
				$html .= "<span class='btn-strt'><input type='submit' value='Сохранить изменения'></span>";
				$html .= dropObjBut("", "", 1, $site[Checked]);
				$html .= "<a class='btn-strt-a' title='Удалить сайт #{$id}?<br>{$site[Domain]}' data-rel='lightcase' data-lc-options='{\"maxWidth\":500,\"showTitle\":false}' href='#сonfirm-actions' data-confirm-href='/?sitesAction=removesite&id={$id}'><span>Удалить сайт</span></a>";
			$html .= "</div>";
		$html .= "</form>";

		return $html;
	}
	// сохранение настроек сайта
	private function savesite(){
		global $db, $current_catalogue, $nc_core, $DOCUMENT_ROOT;

		if(is_array($_POST)) foreach ($_POST as $key => $value) ${$key} = securityForm($value);

		$dorabotki = is_array($dorabotki) ? multiToString($dorabotki) : "";
		$conceptCategory = $category ? ",".implode(",", $category)."," : "";

		$values = "";
		// срок активации
		if($renewalDate_day && $renewalDate_month && $renewalDate_year && $renewalDate_hours && $renewalDate_minutes && $renewalDate_seconds){
			$renewalDate = "{$renewalDate_year}-{$renewalDate_month}-{$renewalDate_day} {$renewalDate_hours}:{$renewalDate_minutes}:{$renewalDate_seconds}";
		}
		if($renewalDate && $Catalogue_Name && $sitetype && $renewalDate && $id){


			$fields = array("colorScheme", "fontScheme", "zoneAndBlocks", "seo", "design", "Catalogue_Name", "sitetype", "renewalDate", "Checked", "activePeriod", "loadJSfile", "customCode", "onlyInOffise", "isConcept", "conceptText", "dorabotki", "conceptCategory");
			foreach ($fields as $val) {
				$values .= ($values ? ", " : "")."{$val} = '{${$val}}'";
			}
			$values .= ($values ? ", " : "")."LastUpdated = '".date("Y-m-d H:i:s")."'";
			$sql = "update Catalogue set {$values} where Catalogue_ID = '{$id}'";


			if($_FILES[conceptPreview]){

				$handle = new upload($_FILES[conceptPreview], "RU");
				if($handle->uploaded){
					$handle->file_new_name_body = "previewConcept";
					$handle->file_auto_rename = false;
					$handle->file_overwrite = true;
					$handle->allowed = array('image/*');
					$handle->image_convert = "jpg";
				}
				if($handle->processed){
					$handle->clean();
				}

				$login = $db->get_var("SELECT login FROM Catalogue WHERE Catalogue_ID = '{$id}'");
				$path = $DOCUMENT_ROOT."/a/{$login}/files/previewConcept.jpg";
				$nc_core->files->save_file('Catalogue', 'conceptPreview', $id, array('path' => $path));
			}




			if ($db->query($sql)) { // выполнения запроса
				return json_encode(ARRAY(
					"title" => "ОК",
					"succes" => "Настройки сохранены",
					"modal" => "close",
					"file" => json_encode($_FILES)
					//"reload" => 1
				));
			}else{
				return json_encode(ARRAY(
					"title" => "Ошибка",
					"error" => "Запрос не выполнился",
					"sql" => $sql
				));
			}
		}else{
			return json_encode(ARRAY(
				"title" => "Ошибка",
				"error" => "Заполните все обязательные поля"
			));
		}
	}
	// удаление сайта
	private function removesite($id){
		global $db, $current_catalogue;

		$id = preg_replace('/[^0-9]/', '', $id); # id сайта

		if($id){
			$addsite = new addsite();
			$login = $addsite->checkIdToLogin($id);
			if($login){
				if(!$addsite->getConcept($id)){
					# Удаление площадки
					# функция netcat
					CascadeDeleteCatalogue($id);
					# наш метод (дозачистка)
					$result = $addsite->deteteCatalogue($id, $login);

					return json_encode(ARRAY(
						"title" => "ОК",
						"succes" => "Сайт удален",
						"modal" => "close",
						"reload" => 1
					));
				}else{
					return json_encode(ARRAY(
						"title" => "Ошибка",
						"error" => "Запрет удаления: площадка - концепт"
					));
				}
			}else{
				return json_encode(ARRAY(
					"title" => "Ошибка",
					"error" => "Площадка не найдена",
				));
			}
		}else{
			return json_encode(ARRAY(
				"title" => "Ошибка",
				"error" => "Не указан сайт"
			));
		}
	}
	// выключение сайта
	private function checkedSite($id, $checked){
		global $db, $current_catalogue;
		$addsite = new addsite();
		if(!$addsite->getConcept($id)){
			$db->query("UPDATE Catalogue SET Checked = '{$checked}' WHERE Catalogue_ID = {$id}");
		}
	}

	private function checkSites(){
		global $db, $current_catalogue;

		$sites = array(); # список сайтов для уведомления о выключении
		$daysOff = array(5, 2, 1); # дни для уведомлений
		$dateNow = new DateTime(); # сегодняшняя дата
		$addsite = new addsite();
		$sites = $db->get_results("SELECT * FROM Catalogue WHERE Catalogue_ID != 1 AND Catalogue_Name != 'sites'", ARRAY_A);

		# формирование списка
		/*foreach ($sites as $site) {
			$id = $site['Catalogue_ID'];
			// не обрабатывать сайты из концепта
			if($addsite->getConcept($site['Catalogue_ID'])) continue;

			$param = $this->checkDate($site['Catalogue_ID'], $site['activePeriod'], $site['Checked'], $site['renewalDate'], $site['dataCreation'], $site['sitetype']);
			// выключение / удаление сайтов
			if($param[off]){
				$this->checkedSite($id, 0);
			}else if($param[remove]){
				//$this->removesite($id);
			}

			// массив уже отправленных писем
			$actionChecked = json_decode($site[actionChecked], true);
			$actionChecked = is_array($actionChecked) ? $actionChecked : array('number' => 0);

			// Массив сайтов для уведомления
			if($param[statusAction]=='checked'){
				if($param[day] == 0 && $param[hours] < 2 && (($actionChecked[type]=='checked' && $actionChecked[number] < 1) || )){
					$siteOff[$id][date] = "2 часа";
					$numberType = 1;
				}else if($param[day] < 2 && $actionChecked < 2){
					$siteOff[$id][date] = "2 дня";
					$numberType = 2;
				}else if($param[day] < 5 && $actionChecked < 3){
					$siteOff[$id][date] = "5 дней";
					$numberType = 3;
				}

				$textArr[mainType1] = "выключиться";
				$textArr[mainType2] = "выключен";

			}else if($param[statusAction]=='remove'){
				if($param[day] == 0 && $param[hours] < 2 && $actionChecked < 1){
					$siteOff[$id][date] = "2 часа";
					$numberType = 1;
				}else if($param[day] < 2 && $actionChecked < 2){
					$siteOff[$id][date] = "2 дня";
					$numberType = 2;
				}else if($param[day] < 5 && $actionChecked < 3){
					$siteOff[$id][date] = "5 дней";
					$numberType = 3;
				}

				$textArr[mainType1] = "удалиться";
				$textArr[mainType2] = "удален";
			}
			if($siteOff[$id]){
				$siteOff[$id] = array_merge($siteOff[$id], $textArr, $site);

				 # массив нынешнего письма
				$actionNow = array(
						'type' => $param[statusAction],
						'number' => $numberType,
						'date' => date("Y-m-d H:i:s"),
						'text' => "Будет {$siteOff[$id][mainType2]} через {$siteOff[$id][date]}"
					);
				$db->query("update Catalogue set actionChecked = '".json_encode($actionNow)."' where Catalogue_ID = '{$site[Catalogue_ID]}'");
			}
		}*/
		// Рассылка писем
		if(is_array($siteOff) && $siteOff){
			foreach ($siteOff as $id => $site) {
				$frommail = "robot@korzilla.ru";

				$text = "Ваш сайт \"{$site[Catalogue_Name]}\" на платформе <b>KORZILLA</b>,	будет {$site[mainType2]} через {$site[date]}!
				<br><br>
				Ссылка <a href='//{$site[Domain]}'>{$site[Domain]}</a><br>";


				$tomail = "lebedev@korzilla.ru";
				$tema = "Ваш сайт {$site[Domain]} будет {$site[mainType2]} через {$site[date]}";

				$mailer = new CMIMEMail();
				$mailer->mailbody(strip_tags($text), $text);
				$mailer->send($tomail, $frommail, $frommail, $tema, "Уведомление Корзилла");
			}
		}
		return $re;
	}

	public function checkDate($id, $activePeriod, $checked, $renewal, $creation, $sitetype){
		global $db;
		$remove = $off = $active = 0;
		$type = $db->get_var("select sitetype_Name from Classificator_sitetype where sitetype_ID = {$sitetype}"); # Тип пакета сайта
		$dayRemove = 30; # кол-во дней, после которых сайт удаляеться
		$checkedArray = array(0 => "Выключен", 1 => "Включен");
		$statusArray = array(0 => "Демо", 1 => "Активный"); #статусы
		$dateNow = new DateTime(); # сегодняшняя дата
		$renewalDate = new DateTime($renewal); # до какого продлен
		$removeDate = new DateTime($renewal); $removeDate->add(new DateInterval("P{$dayRemove}D")); # до какого продлен
		$creationDate = new DateTime($creation); # дата создания

		$status = $activePeriod ? 1 : 0;  #  Активный / Демо

		# это концепт
		$addsite = new addsite();
		$concept = $addsite->getConcept($id) ? 1 : 0;

		# статусы активности
		if($dateNow < $renewalDate || $concept){ # еще не просрочен
			$active = 1; # активный
			$statusAction = 'checked';
		}else{
			if($checked) $off = 1; # выключить
			if(!$status){ # просрочен демонстрационный сайт
				$statusAction = 'remove';
				if($dateNow > $removeDate) $remove = 1; # удалить
			}
		}

		if(!$concept){
			if($off) $dataText = "<span class='sd'>На стадии выключения</span>";
			else if($remove) $dataText = "<span class='sd'>На стадии удаления</span>";
			else if(!$status && !$active){
				$interval = $this->intervalDate($dateNow, $removeDate);
				$dataText = "До удаления: ".$interval['dataText'];
			}
			else if(!$dataText){
				$interval = $this->intervalDate($dateNow, $renewalDate);
				$dataText = "Осталось: ".$interval['dataText'];
			}

			if($active) $renewalText = "Продлен до: <b>{$renewalDate->format('d.m.Y')}</b> {$renewalDate->format('H:i:s')}";
			else if(!$status) $renewalText = "Дата удаления: <b>{$removeDate->format('d.m.Y')}</b> {$removeDate->format('H:i:s')}";
			else $renewalText = "";
		}

		return array(
			"status" => $status,
			"statusAction" => $statusAction,
			"statusText" => $statusArray[$status],
			"checkedText" => $checkedArray[$checked],
			"type" => $type,
			"active" => $active,
			"off" => $off,
			"remove" => $remove,
			"dataText" => $dataText,
			"day" => $interval['day'],
			"hours" => $interval['hours'],
			"renewalText" => $renewalText,
			"creationText" => "Создан: <b>{$creationDate->format('d.m.Y')}</b> {$creationDate->format('H:i:s')}",
			"todayText" => !$concept ? "Сегодня: <b>{$dateNow->format('d.m.Y')}</b> {$dateNow->format('H:i:s')}" : "<span class='sd'>КОНЦЕПТ/СИСТЕМНЫЙ САЙТ</span>"
		);
	}

	public function intervalDate($date1, $date2){
		$intervalYear = $date1->diff($date2)->format('%y');
		$intervalDay = $date1->diff($date2)->format('%a');
		$intervalHours = $date1->diff($date2)->format('%h');
		$intervalMinuts = $date1->diff($date2)->format('%i');
		$intervalTextYear = "<b>".$intervalYear."</b> ".pluralForm($intervalYear, "год", "года", "лет");
		$intervalTextDay = "<b>".$intervalDay."</b> д";
		$intervalTextHours = "<b>".$intervalHours."</b> ч";
		$intervalTextMinuts = "<b>".$intervalMinuts."</b> м";
		//if($intervalYear) $text = $intervalTextYear;
		if($intervalDay) $text = $intervalTextDay." ".$intervalTextHours." ".$intervalTextMinuts;
		else if($intervalHours) $text = $intervalTextHours." ".$intervalTextMinuts;
		else if($intervalMinuts) $text = $intervalTextMinuts;

		return array(
			"status" => $text ? 1 : 0,
			"day" => $intervalDay,
			"hours" => $intervalHours,
			"dataText" => $text
		);
	}


}
?>