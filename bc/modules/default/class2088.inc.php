<?php 

class Class2088{

	private $f = array(); # массив параметров
	// private $count = 1; # кол-во при выводе в поле count

	public function __construct($fieldsArray = array()){
		global $setting_texts;
		
		# поля объекта
		$this->f = $fieldsArray;
		# texts
		$this->time_work = $setting_texts['time_work']['checked'] ? $setting_texts['time_work']['name'] : "Время работы";

		$this->main();
	}

	# main
	private function main(){
		if(function_exists('class2088_main')){
			$html = class2088_main($this); // своя функция
		}else{
			global $setting_texts, $setting;
			# заголовки
			$this->artname = ($setting_texts['full_cat_art']['checked'] ? $setting_texts['full_cat_art']['name'] : "Артикул");

			# id объекта
			if(!$this->id && $this->RowID) $this->id = $this->RowID;

			# шаблон
			if(!stristr($this->template, "template")) $this->template = "template-1";

			if (!stristr($this->map, "2gis")) $this->map = str_replace("width","width=100%&w", $this->map);

			if ($this->email) {
				$this->email = str_replace("  ", " ", $this->email);
				$this->email = str_replace(" ", ",", $this->email);
				$this->email = str_replace("\r\n", ",", $this->email);
				$this->email = str_replace("\n", ",", $this->email);
				foreach(explode(",", $this->email) as $em) {
					if (!$this->isTitle) {
						if ($em) $this->emailHtml .= ($this->emailHtml ? ", " : NULL)."<a ".safeEmailContact($em)." href='' data-metr='contactemail'></a>";
					} else {
						if ($em) $this->emailHtml .= "<div class='ind_e_i mailaj'>E-mail: <a ".safeEmailContact($em)." href='' data-metr='contactemail'></a></div>";
						if ($em) $this->emailAll .= "<div class='contact-email'><a ".safeEmailContact($em)." href='' data-metr='contactemail'></a></div>";
					}
				}
			}
			if ($this->skype) {
				$this->skype = str_replace(" ", ",", $this->skype);
				$this->skype = str_replace(",,", ",", $this->skype);
				foreach(explode(",", $this->skype) as $sk) {
					if ($sk) $this->skp .= ($this->skp ? ", " : NULL)."<a href='skype:{$sk}?call'>{$sk}</a>";
				}
			}

			if ($this->phones) {
				$this->phones = str_replace("\r\n", ",", $this->phones);
				$this->phones = str_replace("\n", ",", $this->phones);
				foreach(explode(",", $this->phones) as $p) {
					$p = replace_lang($p);
					$phones .= "<div class='how_phone_item'><a href='tel:".preg_replace('/[^0-9]/', '', trim($p))."'>".trim($p)."</a></div>";
					$this->phonestitle .= ($this->phonestitle ? "<br>" : NULL)."<a href='tel:".preg_replace('/[^0-9]/', '', trim($p))."'>".trim($p)."</a>";
				}
				$this->phones = $phones ? $phones : "";
			}
		}
	}



	public function getContact(){
		return $this->isTitle ? $this->getContactBlock() : $this->getContactPage();
	}

	# Контакт на странице
	public function getContactPage(){
		if(function_exists('class2088_getContactPage')){
			$html = class2088_getContactPage($this); // своя функция
		}else{
			global $db, $cityid, $bitcat, $catalogue, $AUTH_USER_ID, $cc_settings, $current_cc;

			# people class
			$ppl = $db->get_row("select a.Subdivision_ID as sub, a.Sub_Class_ID as cc from Message201 as a, Sub_Class as b where a.Catalogue_ID = '{$catalogue}' AND a.Subdivision_ID = b.Subdivision_ID AND b.Class_ID = 201 LIMIT 0,1", ARRAY_A);
			$edit = $this->AdminButtons;

			// $this->template = 'template-1';

			$cities_tabs = $current_cc['Sub_Class_Settings']['cities_tabs'] ? 1 : null;
			if ($cities_tabs || $this->template == 'template-2') {
				$objname = $this->city ? "<div class='contact_city'><a class='contact_city_link' href='#dealers'>{$this->city}</a></div>" : NULL;
			}
			else {
				$objname = $this->name ? "<div class='contact_title'>{$this->name} <span class='contact_descr'>[{$this->city} {$this->address}]</span></div>" : NULL;
			}
			switch ($this->template) {
				case 'template-1':
					$html = "<div class='contact_item obj ".objHidden($this->Checked, $this->citytarget, $cityid)." obj{$this->id}'>
								{$edit}
								<div class='contact_item_wrap'>
									<input type='hidden' class='obj_id' value='{$this->id}'>
									{$objname}
								</div>
								".($ppl['sub'] && $ppl['cc'] ? nc_objects_list($ppl['sub'], $ppl['cc'], "&office={$this->id}") : NULL)."
								".($bitcat ? editObjBut($this->editLink) : "")."
							</div>";
					break;
				case 'template-2':
					$html = "<div class='contact_item obj ".objHidden($this->Checked, $this->citytarget, $cityid)." obj{$this->id}'>
								<!--{$edit}-->
								<div class='contact_item_wrap'>
									<input type='hidden' class='obj_id' value='{$this->id}'>
									{$objname}
								</div>
								".($ppl['sub'] && $ppl['cc'] ? nc_objects_list($ppl['sub'], $ppl['cc'], "&office={$this->id}") : NULL)."
								".($bitcat ? editObjBut($this->editLink) : "")."
							</div>";
					break;
			}
		}

		return $html;
	}

	# Контакт в блоке
	public function getContactBlock()
	{
		return $this->getContactPage();
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
	public function test() {
		global $setting;
		return $setting['itemlistsub'];
	}

	public function __set($name, $value) {
		$this->f[$name] = $value;
	}
	public function __get($name) {
		return isset($this->f[$name]) ? $this->f[$name] : "";
	}
	public function __isset($name){
		return isset($this->$name);
	}

}

?>