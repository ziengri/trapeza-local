<?php

class Class244{

	private $f = array(); # массив параметров
	// private $count = 1; # кол-во при выводе в поле count

	public function __construct($fieldsArray = array()){
		# поля объекта
		$this->f = $fieldsArray;

		$this->main();
	}

	# main
	private function main(){
		global $setting_texts, $setting;
		# id объекта
		if(!$this->id && $this->RowID) $this->id = $this->RowID;
	}


	# Объект преимцщество
	public function getItem(){
		global $db, $cityid, $bitcat, $bitLoadMore, $catalogue;

		# кнопка редактирования
		$edit = ($bitcat || $bitLoadMore ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : $this->AdminButtons);

		$class[] = "advantage-item";
		$class[] = "obj";
		$class[] = "obj{$this->id}";
		$class[] = objHidden($this->Checked, $this->citytarget, $cityid);
        if($this->animateKey) $class[] = "wow {$this->animateKey}";

		switch ($this->template) {
			case 'template-1':
			case 'template-2':
				$html = "<li class='".implode(" ", $class)."' ".($this->animateKey ? str_replace(",", ".", "data-wow-delay='".($this->animateDelay + ($this->RowNum * $this->animateDelayStep))."s'") : "").">
							{$edit}
							<div class='adv-info'>
								".$this->getPhoto()."
								".($this->name || $this->text ? "
									<div class='adv-data'>
										".($this->name ? "
											".($this->link ? "<a href='{$this->link}'>" : "")."
												<div class='adv-name'>{$this->name}</div>
											".($this->link ? "</a>" : "")."
										" : NULL)."
										".($this->text ? "<div class='adv-subtext'>{$this->text}</div>" : "")."
									</div>
								" : NULL)."
							</div>
						</li>";
				break;
		}



		return $html;
	}

	# get photo
	public function getPhoto(){
		global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr;

		if($this->banner) {
			$html = "<div class='{$this->image_default}'>
						".($this->link ? "<a href='{$this->link}'>" : NULL)."
							<img src='{$this->banner}' alt='{$this->name}'>
						".($this->link ? "</a>" : NULL)."
					</div>";
		}

		return $html;
	}

	# тестовый метод
	public function test() {
		global $setting;
		return $setting[itemlistsub];
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
