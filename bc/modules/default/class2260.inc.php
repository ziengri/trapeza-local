<?php

class Class2260
{
	private $f = array(); # массив параметров
	
	public function __construct()
	{
		
	}
	public function cleanArray($files)
	{
		$new_files = array_filter($files, function($element) {
			if(!empty($element['path'])) return $element;
		});
		usort($new_files, function($a, $b){
			return ($a['priority'] - $b['priority']);
		});
		$nn = [];
		foreach($new_files as $f) {
			$nn[] = $f['path'];
		}
		$f_files = $nn ? ",".implode(",", $nn)."," : "";
		return $f_files;
	}

	public function main($fieldsArray) 
	{
		$this->f = $fieldsArray;
		$this->editButtons();
	}

	public function editButtons()
	{
		global $bitcat;
		# id объекта
		if (!$this->id) {
			$this->id = $this->RowID;
		}
		if (!$this->id) {
			$this->id = $this->RowID = $this->Message_ID;
		}
		# кнопка редактирования
		$this->edit = ($bitcat ? editObjBut(nc_message_link($this->id, $this->classID, "edit"), null, null, $this->id) : null);
		# big кнопка редактирования
		$this->editBig = ($bitcat ? editObjBut($this->editLink, null, 'товар', $this->id) : null);
	}

	public function s($tmpl = 'tmpl-2')
    {
		$html = '';
		
		switch ($tmpl) {
			case "tmpl-1":
				$htmlbody = "<div class='lesson_photo-block'>".$this->setPhoto()."</div>
							<div class='lesson_body-block'>
								<div class='lesson_title-block'>
									<div class='lesson_title-text'>{$this->name}</div>
								</div>
								<div class='lesson_smdescr-block'>
									<div class='lesson_smdescr-text'>{$this->text}</div>
								</div>
							</div>";
				break;
			case "tmpl-2":
				$htmlbody = "<a href='".$this->fullLink."'>
								<div class='lesson_title-block'>
									<div class='lesson_title-text'>{$this->name}</div>
								</div>
							</a>";
				break;
		}

		$html .= "<div class='lesson_ obj' style='font-size: 15px;'>";
		$html .= $this->edit;
		$html .= $htmlbody;
		$html .= "</div>";

		return $html;
    }

	public function getItem()
    {
		$html = '';
		$html .= "<div class='lesson_ obj' style='font-size: 15px;'>";
		$html .= $this->editBig;
		$html .= "<div class='lesson_body-block'>
					<div class='lesson_lgdescr-block'>
						<div class='lesson_lgdescr-text'>{$this->textfull}</div>
					</div>
				</div>";
		$html .= $this->createContentByFilesStroke();
		$html .= "</div>";

		

		return $html;
    }

	function createContentByFilesStroke()
	{
		$fileNames = explode(',', $this->files);
		$struct = "";
		// echo "<pre>";
		// var_dump($fileNames);
		// echo "</pre>";
		foreach($fileNames as $file) {
			$filePath = str_replace('/var/www/krza/data/www/krza.ru', '', $file);
			// echo "<pre>";
			// var_dump(mime_content_type($file));
			// echo "</pre>";
			switch (mime_content_type($file)) {
				case "video/mp4" :
					$struct .= $this->createContent('video', $filePath, 'video/mp4');
					break;
				case "audio/mpeg" :
					$struct .= $this->createContent('audio', $filePath);
					break;
			}
		}
		return $struct;
	}

	function createContent($tag, $filePath, $type='')
	{
		$content = "<span>".basename($filePath)."</span>";
		$content .= "<{$tag} controls>";
		$content .= "<source src='{$filePath}' type='{$type}'>";
		$content .= "</{$tag}>";

		return $content;
	}

	function setPhoto() {
		global $IMG_HOST, $noimage;

		if($this->preview){
			$image = $this->preview;
		}

		# нет фото
		if (!$image){
			$nophoto = 1;
			$image = $noimage;
		}

		$photoMain = "<div class='".($nophoto ? image_fit() : $this->image_default)."'>
							".($this->fullLink ? "<a ".$this->fullLink.">" : "")."
								<img src='{$IMG_HOST}{$image}' alt='{$this->name}' ".($nophoto ? "class='nophoto'" : "").">
							".($this->fullLink ? "</a>" : "")."
						</div>";

		return $photoMain;
	}
	
	public function __get($name)
	{
		return $this->f[$name] ?: NULL;
	}
}
?>
