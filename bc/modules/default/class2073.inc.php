<?php 

class Class2073{

	private $f = array(); # массив параметров

	public function __construct($fieldsArray = array()){
		# поля объекта
		$this->f = $fieldsArray;
        $this->inputs = $this->inputs ? orderArray($this->inputs) : array();
		$this->bigphoto = array();
		$this->smallphoto = array();

		$this->main();
		$this->setPhoto();
	}

	# main
	private function main(){
		global $setting_texts, $setting;
		# id объекта
		if(!$this->id && $this->RowID) $this->id = $this->RowID;
		# шаблон
		if(!stristr($this->template, "template")) $this->template = "template-1";
	}


	# Объект
	public function getItem(){
		global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $HTTP_HOST, $current_catalogue, $pathInc2, $DOCUMENT_ROOT;

		# кнопка редактирования
		$edit = ($bitcat || $bitLoadMore ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : NULL);


		$class[] = "gencomponent-item";
		$class[] = "obj";
		$class[] = "obj{$this->id}";
		$class[] = objHidden($this->Checked, $this->citytarget, $cityid);


		$connectTemplate = $DOCUMENT_ROOT.$pathInc2."/template/{$this->classID}/template/objects/{$this->template}.php";

		if($current_catalogue['customCode'] && file_exists($connectTemplate)){
			include($connectTemplate);
		}else{
			$html = "<li class='".implode(" ", $class)."'>{$edit}</li>";
		}

		return $html;
	}

	# Новость внутряк
	public function getItemFull(){
		global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $currency, $IMG_HOST, $DOCUMENT_ROOT, $pathInc2, $current_catalogue;

		if(!$this->inModal){
			$edit = ($bitcat || $bitLoadMore ? editObjBut($this->editLink, null, 'объект') : NULL);
		}

		$class[] = 'gencomponent-item-full';
		$class[] = 'cb';
		$class[] = $this->template ? $this->template : "";

		$connectTemplate = $DOCUMENT_ROOT.$pathInc2."/template/{$this->classID}/template/fullObjects/{$this->template}.php";

		if($current_catalogue['customCode'] && file_exists($connectTemplate)){
			include($connectTemplate);
		}else{
			$html = "";
		}

		return $html;
	}


	# photo
	public function setPhoto(){
		global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr, $noimage;


		if($this->full){
			# photo объекта
            foreach (array('images' => $this->images, 'images1' => $this->images1) as $key => $imgs) {
                $bigphoto = $bigphoto2 = $smallphoto = "";

    			if ($imgs->records) {
            		# шаблон вывода картинок
            		$imgs->set_template(array('record' => "%Preview%"));
    				foreach($imgs->records as $i => $f) {
    					$alt = $f[Name] ? $f[Name] : $this->name;
    					$bigphoto .= "<a href='{$IMG_HOST}{$f[Path]}' title='{$f[Name]}' data-rel='lightcase:image-in-gencomponent'><img itemprop='image' src='{$IMG_HOST}{$f[Path]}' alt='{$alt}'></a>";
						$bigphoto2 .= "<a href='{$IMG_HOST}{$f[Path]}' title='{$f[Name]}' data-rel='lightcase:image-in-gencomponent'><img itemprop='image' src='{$IMG_HOST}{$f[Path]}' alt='{$alt}'><span>{$alt}</span></a>";
    					$smallphoto .= "<div class='g_m_img {$this->image_default}' data-val='{$i}'><img src='{$IMG_HOST}{$f[Preview]}' alt='{$alt}'></div>";
    				}
    			}
    			# файла нет
    			if (!$bigphoto) $bigphoto = "<div class='image-default image-contain image-noimg'><img src='".getnoimage("big")."' style='width: 100%;'></div>";

    			$thisbigphoto[$key] = "<div class='gencomponent-photo owl-carousel'>{$bigphoto}</div>";
				$thisbigphoto2[$key] = "<div class='gencomponent-photo'>{$bigphoto2}</div>";
    			$thissmallphoto[$key] = $smallphoto ? "<div class='gencomponent-photos'>{$smallphoto}</div>"  : "";
            }
			$this->bigphoto = $thisbigphoto;
			$this->bigphoto2 = $thisbigphoto2;
			
			$this->smallphoto = $thissmallphoto;


			# photo объекта
            $bigphoto = "";

			if ($this->photogallery->records) {
        		# шаблон вывода картинок
        		$this->photogallery->set_template(array('record' => "%Preview%"));
				foreach($this->photogallery->records as $i => $f) {
					$alt = $f[Name] ? $f[Name] : $this->name;
					$bigphoto .= "<li><div class='image-default image-cover'><a href='{$IMG_HOST}{$f[Path]}' title='{$f[Name]}' data-rel='lightcase:image-in-gencomponent-gallery'><img itemprop='image' src='{$IMG_HOST}{$f[Path]}' alt='{$alt}'></a></div></li>";
				}
			}

			if($bigphoto) $this->galleryhtml = "<ul class='gencomponent-gallery'>{$bigphoto}</div>";

		}else{
			# загруженная картинка
			if($this->image){
				$image = $this->image;
			}
			# нет фото
			if (!$image){
				$nophoto = 1;
				$image = $noimage;
			}
			$photoMain = "<div class='".($nophoto ? image_fit() : $this->image_default)."'>
								<a href='{$this->fullLink}'>
									<img src='{$IMG_HOST}{$image}' ".($nophoto ? "class='nophoto'" : "").">
								</a>
							</div>";

			$this->photoMain = $photoMain;
		}
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