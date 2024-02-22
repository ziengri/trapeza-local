<?php

class Class2003{

	private $f = array(); # массив параметров

	public function __construct($fieldsArray = array()){
		# поля объекта
		$this->f = $fieldsArray;

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


	# Объект новости
	public function getItem(){
		global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $HTTP_HOST, $current_catalogue, $pathInc2, $DOCUMENT_ROOT;

		# кнопка редактирования
		$edit = ($bitcat || $bitLoadMore ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : $this->AdminButtons);


		$class[] = "news-item";
		$class[] = "obj";
		$class[] = "obj{$this->id}";
		$class[] = objHidden($this->Checked, $this->citytarget, $cityid);
        if($this->animateKey) $class[] = "wow {$this->animateKey}";

		$connectTemplate = $DOCUMENT_ROOT.$pathInc2."/template/{$this->classID}/template/objects/{$this->template}.php";
		
		if($current_catalogue['customCode'] && file_exists($connectTemplate)){
			include($connectTemplate);
		}else{
			switch ($this->template) {
				case 'template-1':
				case 'template-2':
					$html = "<li class='".implode(" ", $class)."' ".($this->animateKey ? str_replace(",", ".", "data-wow-delay='".($this->animateDelay + ($this->RowNum * $this->animateDelayStep))."s'") : "").">
								{$edit}
								<div class='news-body'>
									{$this->photoMain}
									<div class='news-date'>".dateType($this->date)."</div>
									<div class='news-data'>
										<div class='news-name'>
											".($this->getlinkParam("link") ? "<a ".$this->getlinkParam().">" : "")."
												{$this->name}
											".($this->getlinkParam("link") ? "</a>" : "")."
										</div>
										".($this->text ? "<div class='news-text'>{$this->text}</div>" : "")."
									</div>
								</div>
							</li>";
					break;
				case 'template-3':
					$html = "<li class='".implode(" ", $class)."' ".($this->animateKey ? str_replace(",", ".", "data-wow-delay='".($this->animateDelay + ($this->RowNum * $this->animateDelayStep))."s'") : "").">
								{$edit}
								<div class='news-body'>
									<div class='news-date'>".dateType($this->date)."</div>
									<div class='news-data'>
										<div class='news-name'>
											".($this->getlinkParam("link") ? "<a ".$this->getlinkParam().">" : "")."
												{$this->name}
											".($this->getlinkParam("link") ? "</a>" : "")."
										</div>
										".($this->text ? "<div class='news-text'>{$this->text}</div>" : "")."
									</div>
								</div>
							</li>";
					break;
			}
		}

		return $html;
	}

	# Новость внутряк
	public function getItemFull(){
		global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $currency, $IMG_HOST, $DOCUMENT_ROOT, $pathInc2, $current_catalogue;

		if(!$this->inModal){
			$edit = ($bitcat || $bitLoadMore ? editObjBut($this->editLink, null, 'новость') : NULL);
		}

		$class[] = 'news-item-full';
		$class[] = 'cb';
		$class[] = $this->template ? $this->template : "";

		$connectTemplate = $DOCUMENT_ROOT.$pathInc2."/template/{$this->classID}/template/fullObjects/{$this->template}.php";

		if($current_catalogue['customCode'] && file_exists($connectTemplate)){
			include($connectTemplate);
		}else{
			switch ($this->template) {
				case 'template-1':
				case 'inModal':
					$html = "{$edit}
							<div class='".implode(" ", $class)."'>
								<div class='news-main cb'>
									".($this->bigphoto ? "
										<div class='news-photo-all'>
											{$this->bigphoto}
											{$this->smallphoto}
										</div>" : "")."
									<div class='news-textfull txt'>
										".($this->inModal ? "<h3>{$this->name}</h3>" : "")."
										".($this->textfull ? $this->textfull : $this->text)."
									</div>
								</div>
								<div class='news-info'>
								    <div class='date'>".getLangWord('news_date','Дата').": ".dateType($this->date, 'date-type-news')."</div>
								    ".($this->autor ? "<div class='autor'>".getLangWord('news_author','Автор').": $this->autor</div>" : NULL)."
								    ".($this->source ? "<div class='source'>".getLangWord('news_date','Источник').": ".($this->url ? "<a href='$this->url' target='_blank'>$this->source</a>" : $this->source)."</div>" : "")."
								</div>
							</div>";
					break;
			}
		}

		return $html;
	}

	# Параметры ссылки
	public function getlinkParam($p=''){
		# ссылка
		$link = $this->openlink || $this->textfull ? ($this->openlink ? $this->url : $this->fullLink) : NULL;
		if($p=='link') return $link;

		# открыть в новой вкладке
		$urllink = parse_url($this->url);
		if($this->openlink && ($urllink['host'] && ($HTTP_HOST != $urllink['host'] || "www.".$HTTP_HOST != $urllink['host']))) $blank = 1;

		if($this->objInModal){
			$class[] = "modal-obj";
			$class[] = "modal-news";
			$class[] = "modal-sub{$this->sub}";
			if($this->block_id){
				$class[] = "modal-blk";
				$class[] = "modal-blk{$this->block_id}";
			}
			$paramLink = "title='{$this->name}' data-rel='lightcase' data-maxwidth='950' data-groupClass='".implode(" ", $class)."'";
		}else{
			$paramLink = $blank ? "target='_blank'" : "";
		}
		
		if ($this->objInModal) {
			$href = $link.(strpos($link, '?') !== false ? '&' : '?')."inModal=1&isNaked=1";
			$paramLink .= " data-lc-href='{$href}'";
			unset($href);
		}
		$paramLink .= " href='{$link}'";

		return $paramLink;
	}

	# photo
	public function setPhoto(){
		global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr, $noimage;

		# шаблон вывода картинок
		$this->photo->set_template(array('record' => "%Preview%"));
		# ссылка
		$link = $this->openlink || $this->textfull ? ($this->openlink ? $this->url : $this->fullLink) : NULL;

		if($this->full){
			# photo объекта
			if ($this->photo->records) {
				foreach($this->photo->records as $i => $f) {
					$alt = $f[Name] ? $f[Name] : $this->name;
					$bigphoto .= "<a href='{$IMG_HOST}{$f[Path]}' class='{$this->image_default}' title='{$f[Name]}' data-rel='lightcase:image-in-news'><img itemprop='image' src='{$IMG_HOST}{$f[Path]}' alt='{$alt}'></a>";
					$smallphoto .= "<div class='g_m_img {$this->image_default}' data-val='{$i}'><img src='{$IMG_HOST}{$f[Preview]}' alt='{$alt}'></div>";
				}
			}

			if($bigphoto)$this->bigphoto = "<div class='news-photo owl-carousel nav-type-1'>{$bigphoto}</div>";
			if($this->photo->count()>1) $this->smallphoto = "<div class='news-photos'>{$smallphoto}</div>";

		}else{
			# загруженная картинка
			if($this->photo_preview){
				$image = $this->photo_preview;
			}
			# внутрение картинки
			if(!$image && $this->photo->get_record(1)){
				$image = $this->photo->get_record(1);
			}

			# нет фото
			if (!$image){
				$nophoto = 1;
				$image = $noimage;
			}

			$photoMain = "<div class='".($nophoto ? image_fit() : $this->image_default)."'>
								".($this->getlinkParam("link") ? "<a ".$this->getlinkParam().">" : "")."
									<img src='{$IMG_HOST}{$image}' alt='{$this->name}' ".($nophoto ? "class='nophoto'" : "").">
								".($this->getlinkParam("link") ? "</a>" : "")."
							</div>";

			$this->photoMain = $photoMain;
		}
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
