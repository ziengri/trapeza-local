<?

class Class2012
{

	private $f = array(); # массив параметров
	// private $count = 1; # кол-во при выводе в поле count

	public function __construct($fieldsArray = array())
	{
		global $setting_texts;

		# поля объекта
		$this->f = $fieldsArray;
		# texts
		$this->time_work = $setting_texts['time_work']['checked'] ? $setting_texts['time_work']['name'] : "Время работы";

		$this->main();
	}

	# main
	private function main()
	{
		if (function_exists('class2012_main')) {
			$html = class2012_main($this); // своя функция
		} else {
			global $setting_texts, $setting, $AUTH_USER_ID, $db, $pathInc, $catalogue;;

			# лого сйта для разметки
			$img_logo = $db->get_var("select SUBSTRING_INDEX(file, ':', -1) from Message2047 where Catalogue_ID = '{$catalogue}' limit 1");
			if ($img_logo) $this->logo =  "<img class='none' itemprop='image' src='{$pathInc}/files/{$img_logo}' content='{$pathInc}/files/{$img_logo}'/>";

			# заголовки
			$this->artname = ($setting_texts['full_cat_art']['checked'] ? $setting_texts['full_cat_art']['name'] : "Артикул");

			# id объекта
			if (!$this->id && $this->RowID) $this->id = $this->RowID;

			# шаблон
			if (!stristr($this->template, "template")) $this->template = "template-1";

			if (!stristr($this->map, "2gis")) $this->map = str_replace("width", "width=100%25&w", $this->map);

			if ($this->photo) {
				foreach (array_reverse($this->photo) as $photo) {
					# реализация title or alt. Для удаления заменить ftitle,falt на f[Name]
					if ($photo['Name']) {
						$photoName = trim($photo['Name']);
						if (preg_match("/\|/", $photoName)) {
							$fname = explode('|', trim($photoName));
							$ftitle = $fname[0] ? trim($fname[0]) : NULL;
							$falt = $fname[1] ? trim($fname[1]) : NULL;
						} else {
							$ftitle = $falt = $photoName;
						}
					}
					$this->photos .= "<li>
							  <div class='image-default image-cover'>
								  <a href='{$IMG_HOST}{$photo['Path']}' " . ($ftitle ? "title='{$ftitle}'" : "") . " data-rel='lightcase:image-in-" . ($mesid ? "block" . $mesid : "sub" . $sub) . "'>
									  <span class='img-preview'><img src='{$IMG_HOST}{$photo['Preview']}' " . ($falt ? "alt='{$falt}'" : "") . "><span>
									  " . ($ftitle ? "<span class='photo-name'>{$ftitle}</span>" : "") . "
								  </a>
							  </div>
							  " . ($f_AdminButtons ? $f_AdminButtons : "") . "
							  " . ($bitcat ? editObjBut($editLink) : NULL) . "
						  </li> ";
				}
			}

			if ($this->email) {
					$this->email = str_replace(["  ", " ", "\r\n", "\n"], [" ", ",", ",", ","], $this->email);
				
					foreach (explode(",", $this->email) as $em) {
						if (!empty($em)) {
							if (!$this->isTitle) {
								$this->emailHtml .= ($this->emailHtml ? ", " : NULL) . "<a " . safeEmailContact($em) . " href='' data-metr='contactemail' ><span itemprop='email' >{$this->email}</span></a>";
							} else {
								$this->emailHtml .= "<div class='ind_e_i mailaj'>E-mail: <a " . safeEmailContact($em) . " href='mailto:{$this->email}' data-metr='contactemail' ><span itemprop='email' >{$this->email}</span></a></div>";
								$this->emailAll .= "<div class='contact-email'><a " . safeEmailContact($em) . " href='mailto:{$em}' data-metr='contactemail' ><span itemprop='email' >{$em}</span></a></div>";
							}
						}
					}
			}
			if ($this->skype) {
				$this->skype = str_replace(['  ', ' '], [' ', ','], $this->skype);
				foreach (explode(",", $this->skype) as $sk) {
					if ($sk) $this->skp .= ($this->skp ? ", " : NULL) . "<a href='skype:{$sk}?call'>{$sk}</a>";
				}
			}

			if ($this->phones) {
				$this->phones = str_replace(["\r\n", "\n"], ",", $this->phones);
				$hidephone = $setting['hidephone'] ? '' : 'hidephone ';
				$phones = '';
				foreach (explode(",", $this->phones) as $phone) {
					if (empty(trim($phone))) continue;

					$phone = trim(replace_lang($phone));
					$phoneNum = preg_replace('/[^0-9]/', '', $phone);
					$classPhoneItem = ['how_phone_item'];
					$showPhoneBtn = '';
					if (strlen($phoneNum) == 11) $classPhoneItem[] = $hidephone;
					if ($hidephone && strlen($phoneNum) == 11) {
						$showPhoneBtn = "<span class='show_phone' data-metr='showphone'>Показать телефон</span>";
					}
					$classPhoneItem = implode(' ', $classPhoneItem);

					if (strlen($phoneNum) < 11) {
						$phones .= "<div class='{$classPhoneItem}'><span>{$phone}</span></div>";
						$this->phonestitle .= "<div class='{$classPhoneItem}'><span>{$phone}</span></div>";
					} else {
						$phones .= "<div class='{$classPhoneItem}'>
										<a href='tel:+{$phoneNum}' data-metr='contactphone'><span itemprop='telephone'>{$phone}</span></a>{$showPhoneBtn}
									</div>";
						$this->phonestitle .= "<div class='{$classPhoneItem}'>
													<a href='tel:+{$phoneNum}' data-metr='contactphone'><span itemprop='telephone'>{$phone}</span></a>{$showPhoneBtn}
												</div>";
					}
				}
				$this->phones = $phones ? $phones : "";
			}
		}
	}



	public function getContact()
	{

		return $this->isTitle ? $this->getContactBlock() : $this->getContactPage();
	}

	# Контакт на странице
	public function getContactPage()
	{
		if (function_exists('class2012_getContactPage')) {
			$html = class2012_getContactPage($this); // своя функция
		} else {
			global $db, $cityid, $bitcat, $catalogue, $current_catalogue, $AUTH_USER_ID;

			# people class
			$ppl = $db->get_row("select a.Subdivision_ID as sub, a.Sub_Class_ID as cc from Message201 as a, Sub_Class as b where a.Catalogue_ID = '{$catalogue}' AND a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 201 LIMIT 0,1", ARRAY_A);
			$edit = $this->AdminButtons;
			
			switch ($this->template) {
				case 'template-1':
					$html = "<div itemscope itemtype='http://schema.org/LocalBusiness' id='obj2012-{$this->id}' class='contact_item obj " . objHidden($this->Checked, $this->citytarget, $cityid) . " obj{$this->id}'>
								{$edit}
								<div class='contact_item_wrap'>
									" . ($this->name ? "<div class='contact_title' itemprop='name'>{$this->name}</div>" : "<meta itemprop='name' content='{$current_catalogue['Catalogue_Name']}'/>") . "
									{$this->logo}
									<div class='how_get ".($this->map ? "how_get_map" : "how_get_nomap")."'>
										<div class='how_get_left'>
											" . ($this->phones ? "<div class='how_item how_phone iconsCol icons i_tel cb'>{$this->phones}</div>" : NULL) . "

											" . ($this->adres ? "
												<div class='how_item how_location iconsCol icons i_city' itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'>
													<div class='how_par'>
														<div class='how_name'>{$this->name}</div>
														<div class='how_text' itemprop='streetAddress'>{$this->adres}</div>
													</div>
												</div>
											" : NULL) . "

											" . ($this->emailHtml || $this->skype || $this->icq || $this->site ? "
												<div class='how_item how_email iconsCol " . ($this->emailHtml ? "icons i_email" : NULL) . "'>
													<div class='how_email_fir'>
														<div class='how_email_sec'>
															" . ($this->emailHtml ? "
																<div class='how_par'>
																	<div class='how_name'>E-mail:</div>
																	<div class='how_text mailaj'>{$this->emailHtml}</div>
																</div>
															" : NULL) . "
															" . ($this->skype ? "
																<div class='how_par'>
																	<div class='how_name'>Skype:</div>
																	<div class='how_text'>{$this->skp}</div>
																</div>
															" : NULL) . "
															" . ($this->icq ? "
																<div class='how_par'>
																	<div class='how_name'>ICQ:</div>
																	<div class='how_text'>{$this->icq}</div>
																</div>
															" : NULL) . "
															" . ($this->site ? "
																<div class='how_par'>
																	<div class='how_name'>Сайт:</div>
																	<div class='how_text'><a href='{$this->site}' target='_blank'>{$this->site}</a></div>
																</div>
															" : NULL) . "
														</div>
													</div>
												</div>
											" : NULL) . "
											" . ($this->time ? $this->getTimeFull($this) : NULL) . "
										</div>
										" . ($this->soc_show ? $this->getSoc() : null) . "
									</div>
									" . ($this->photos ? "<ul class='gallery-items' data-sizeitem='270' data-margin='7' data-sizeimage='60' data-calculated='1'>" . $this->photos . "</ul>" : NULL) . "
									" . ($this->map ? "<div class='this_map'>{$this->map}</div>" : NULL) . "
									" . ($bitcat ? editObjBut($this->editLink) . getAddPeople($this->id) : "") . "
								</div>
								" . ($ppl['sub'] && $ppl['cc'] ? nc_objects_list($ppl['sub'], $ppl['cc'], "&office={$this->id}") : NULL) . "
							</div>";
					break;
				case 'template-2':
					$html = "<div itemscope itemtype='http://schema.org/LocalBusiness' id='obj2012-{$this->id}' class='contact_item obj " . objHidden($this->Checked, $this->citytarget, $cityid) . " obj{$this->id}'>
								{$edit}
								<div class='contact_item_wrap'>
									<div class='contact_item_flex'>
										<div class='contact-right'>
											" . ($this->name ? "<div class='contact_title' itemprop='name' >{$this->name}</div>" : "<meta itemprop='name' content='{$current_catalogue['Catalogue_Name']}'/>") . "
											<div class='how_get'>
												<div class='how_get_left'>
													" . ($this->phones ? "<div class='how_item how_phone iconsCol icons i_tel cb'>{$this->phones}</div>" : NULL) . "

													" . ($this->adres ? "
														<div class='how_item how_location iconsCol icons i_city'>
															<div class='how_par'>
																<div class='how_name'>{$this->name}</div>
																<div class='how_text'>{$this->adres}</div>
															</div>
														</div>
													" : NULL) . "

													" . ($this->emailHtml || $this->skype || $this->icq || $this->site ? "
														<div class='how_item how_email iconsCol " . ($this->emailHtml ? "icons i_email" : NULL) . "'>
															<div class='how_email_fir'>
																<div class='how_email_sec'>
																	" . ($this->emailHtml ? "
																		<div class='how_par'>
																			<div class='how_name'>E-mail:</div>
																			<div class='how_text mailaj'>{$this->emailHtml}</div>
																		</div>
																	" : NULL) . "
																	" . ($this->skype ? "
																		<div class='how_par'>
																			<div class='how_name'>Skype:</div>
																			<div class='how_text'>{$this->skp}</div>
																		</div>
																	" : NULL) . "
																	" . ($this->icq ? "
																		<div class='how_par'>
																			<div class='how_name'>ICQ:</div>
																			<div class='how_text'>{$this->icq}</div>
																		</div>
																	" : NULL) . "
																	" . ($this->site ? "
																		<div class='how_par'>
																			<div class='how_name'>Сайт:</div>
																			<div class='how_text'><a href='{$this->site}' target='_blank'>{$this->site}</a></div>
																		</div>
																	" : NULL) . "
																</div>
															</div>
														</div>
													" : NULL) . "
													" . ($this->time ? $this->getTimeFull($this) : NULL) . "
												</div>
												" . ($this->soc_show ? $this->getSoc() : null) . "
											</div>

										</div>
										" . ($this->photos ? "<ul class='gallery-items' data-sizeitem='270' data-margin='7' data-sizeimage='60' data-calculated='1'>" . $this->photos . "</ul>" : NULL) . "
										" . ($this->map ? "<div class='this_map'>{$this->map}</div>" : NULL) . "
									</div>
									" . ($bitcat ? editObjBut($this->editLink) . getAddPeople($this->id) : "") . "
								</div>
								" . ($ppl['sub'] && $ppl['cc'] ? nc_objects_list($ppl['sub'], $ppl['cc'], "&office={$this->id}") : NULL) . "
							</div>";
					break;
			}
		}

		return $html;
	}

	protected function getSoc()
	{
		global $db, $catalogue;

		if (!$this->soc_show) return '';

		$subSoc = $db->get_row(
			"SELECT 
				Subdivision_ID as sub,
				Sub_Class_ID as cc
			FROM 
				Sub_Class
			WHERE 
				Catalogue_ID = {$catalogue}
				AND Class_ID = 2011
			LIMIT 0,1",
			ARRAY_A
		);
		if (empty($subSoc)) return '';
		return nc_objects_list($subSoc['sub'], $subSoc['cc'], "&recNum=200");
	}

	// Время с микро разметкой
	public function getTimeFull()
	{
		if (function_exists('class2012_getTimeFull')) {
			$result = class2012_getTimeFull($this); // своя функция
		} else {
			global $AUTH_USER_ID;
			$timeArr = orderArray($this->time);
			$isJson = (json_last_error() === JSON_ERROR_NONE ? true : false);
			// if($AUTH_USER_ID == 6) echo $this->time;
			$text = str_replace(["\n", "\r"], ["</br>", ""], ($isJson ? $timeArr['text'] : $this->time));

			if (!$text) return false;

			$mitTime = ($isJson ? $timeArr['met_text'] : '');
			$result = " <div class='how_item how_time iconsCol icons i_time'>
				<div class='how_par'>
					<div class='how_name'>" . getLangWord('time_work', 'Время работы:') . "</div>
					<div class='how_text' itemprop='openingHours'
					datetime='{$mitTime}'>{$text}</div>
				</div>
			</div>";
		}
		return $result;
	}
	public function getTime()
	{
		if (function_exists('class2012_getTime')) {
			$result = class2012_getTime($this); // своя функция
		} else {
			$timeArr = orderArray($this->time);
			$isJson = (json_last_error() === JSON_ERROR_NONE ? true : false);
			$text = str_replace(["\n", "\r"], ["</br>", ""], ($isJson ? $timeArr['text'] : $this->time));

			if (!$text) return false;

			$mitTime = ($isJson ? $timeArr['met_text'] : '');
			$result = "<div class='item-contact-time'  itemprop='openingHours'
			datetime='{$mitTime}'>{$text}</div>";
		}

		return $result;
	}
	# Контакт в блоке
	public function getContactBlock()
	{
		if (function_exists('class2012_getContactBlock')) {
			$html = class2012_getContactBlock($this); // своя функция
		} else {
			global $db, $cityid, $bitcat, $catalogue, $current_catalogue;

			if (!$this->type) $this->type = 'template-1';
			$class[] = "obj";
			$class[] = objHidden($this->Checked, $this->citytarget, $cityid);
			$class[] = "obj{$this->id}";
			$class[] = "item-contact";
			$class[] = $this->type;

			switch ($this->type) {
				case 'template-1': // Стандартный шаблон
					$html = "<div itemscope itemtype='http://schema.org/LocalBusiness' class='" . implode(" ", $class) . "'>
								" . ($this->name ? "<div style='margin: 13px 0 13px;' itemprop='name'><b>{$this->name}</b></div>" : "<meta itemprop='name' content='{$current_catalogue['Catalogue_Name']}'/>") . "
								{$this->logo}
								" . ($this->adres ? "
									<div class='i_c_item iconsCol icons i_city' itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'>
										<span class='ind_c' itemprop='streetAddress'>{$this->adres}</span>
										" . ($this->map ? "
											<div class='i_c_map'>
												<a title='Схема проезда' data-rel='lightcase' href='{$this->fullLink}?isNaked=1' data-lc-options='{\"type\":\"iframe\",\"maxHeight\":600,\"groupClass\":\"modal-obj\"}'>Посмотреть на карте</a>
											</div>
										" : "") . "
									</div>
								" : "") . "
								" . ($this->phonestitle ? "<div class='i_c_item iconsCol icons i_tel cb'>{$this->phonestitle}</div>" : "") . "
								" . ($this->fax ? "<div class='i_c_item iconsCol icons i_tel' itemprop='faxNumber'>{$this->fax}</div>" : "") . "
								" . ($this->getTime() ? "<div class='i_c_item iconsCol icons i_time'>" . $this->getTime() . "</div>" : "") . "
								" . ($this->emailHtml || $this->skp || $this->icq ? "<div class='i_c_item iconsCol " . ($this->emailHtml ? "icons i_email" : "") . "'>
									" . ($this->emailHtml ? $this->emailHtml : "") . "
									" . ($this->skp ? "<div class='ind_e_i'>Skype: {$this->skp}</div>" : "") . "
									" . ($this->icq ? "<div class='ind_e_i'>ICQ: {$this->icq}</div>" : "") . "
								</div>" : "") . "
								" . ($bitcat ? editObjBut($this->editLink) : "") . "
							</div>";
					break;
				case 'template-2': // Шаблон с картой и данными
					$class[] = $this->map ? "contact-map-have" : "";

					
					//if ($_SERVER[REMOTE_ADDR]=='31.13.133.138') echo "SELECT IF(citytarget LIKE '%,{$cityid},%',1,0) as currentContact, Message_ID, name FROM Message2012 WHERE name not like '%Пункт%' AND Catalogue_ID = {$catalogue} AND Subdivision_ID = {$this->sub} ORDER BY currentContact DESC";
					$contacts = $db->get_results("SELECT IF(citytarget LIKE '%,{$cityid},%',1,0) as currentContact, Message_ID, name FROM Message2012 WHERE name not like '%Пункт%' AND Catalogue_ID = {$catalogue} AND Subdivision_ID = {$this->sub} ORDER BY currentContact DESC", ARRAY_A);
					$Hidden_URL = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Catalogue_ID = {$catalogue} AND Subdivision_ID = {$this->sub}");
					if (count($contacts) > 1) {
						foreach ($contacts as $key => $contact) {
							$option[$Hidden_URL . "?isNaked=1&isTitle=1&template_block=template-2&msg=" . $contact['Message_ID']] = $contact['name'] ? $contact['name'] : "Контакт " . (++$key);
						}
						$options = getOptionsFromArray($option, $Hidden_URL . "?isNaked=1&isTitle=1&template_block=template-2&msg=" . $this->id);
						$select = bc_select_standart("contact-select", $options, "", 'class="select-style"');
					}
					$html = "<div itemscope itemtype='http://schema.org/LocalBusiness' class='" . implode(" ", $class) . "'>
								" . ($this->map ? "<div class='item-contact-map'>
										<iframe frameborder='no' width='640' height='600' src='{$Hidden_URL}?isNaked=1&nc_ctpl=2068&msg=" . ($this->msg ? $this->msg : $this->id) . "'></iframe>
										<a title='Схема проезда' class='item-contact-open' data-rel='lightcase' href='{$this->fullLink}?isNaked=1' data-lc-options='{\"type\":\"iframe\",\"maxHeight\":600,\"groupClass\":\"modal-obj\"}' alt='Схема проезда'></a>
								</div>" : NULL) . "
								<div class='item-contact-item'>
									" . ($select ? "<div class='item-contact-select'>{$select}</div>" : "") . "
									" . ($this->name ? "<div class='none' itemprop='name'>{$this->name}</div>" : "") . "
									" . ($this->adres ? "<div class='item-contact-address' itemprop='address'>{$this->adres}</div>" : "") . "
									" . ($this->phonestitle ? "<div class='item-contact-phone'>{$this->phonestitle}</div>" : "") . "
									" . ($this->fax ? "<div class='item-contact-fax'>{$this->fax}</div>" : "") . "
									" . ($this->getTime() ? $this->getTime() : "") . "
									" . ($this->emailAll ? "<div class='item-contact-email'>{$this->emailAll}</div>" : "") . "
									" . ($this->skp ? "<div class='item-contact-skype'>Skype: {$this->skp}</div>" : "") . "
									" . ($this->icq ? "<div class='item-contact-icq'>ICQ: {$this->icq}</div>" : "") . "
									" . ($bitcat ? editObjBut($this->editLink) : "") . "
								</div>
							</div>";
					break;
				case 'template-3': // Шаблон с картой
					$class[] = $this->map ? "contact-map-have" : "";

					$html = "<li><div class='" . implode(" ", $class) . "'>
								" . ($this->map ? "<div class='item-map'>
										{$this->map}
										<a title='Схема проезда' class='item-contact-open' data-rel='lightcase' href='{$this->fullLink}?isNaked=1' data-lc-options='{\"type\":\"iframe\",\"maxHeight\":600,\"groupClass\":\"modal-obj\"}'></a>
								</div>" : NULL) . "
							</div></li>";
					break;
			}
		}
		return $html;
	}

	# Контакт внутряк
	public function getContactFull()
	{
		global $db, $cityid, $bitcat, $catalogue;

		$html = "<html>
					<body>
						{$this->map}
						<style> body {padding:0;margin:0;} #rmap {height: 450px} iframe {width: 100%;height: 100%;border: 0 !important;} </style>
					</body>
				</html>";
		return $html;
	}




	# тестовый метод
	public function test()
	{
		global $setting;
		return $setting['itemlistsub'];
	}

	public function __set($name, $value)
	{
		$this->f[$name] = $value;
	}
	public function __get($name)
	{
		return isset($this->f[$name]) ? $this->f[$name] : "";
	}
	public function __isset($name)
	{
		return isset($this->$name);
	}
}
