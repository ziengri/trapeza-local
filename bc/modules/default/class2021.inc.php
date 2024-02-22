<?php

use App\modules\Korzilla\Tag\Provider as TagProvider;
use App\modules\Korzilla\Tag\Tag;

class Class2021
{
	private $f = array(); # массив параметров
	// private $count = 1; # кол-во при выводе в поле count
	/** @var array<int,>*/
	private $tagList;
	private $tagListInited = false;

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
	}


	# Объект портфолио
	public function getItem(){
		if(function_exists('class2021_getItem')){
			$html = class2021_getItem($this); // своя функция
		}else{
			global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $currency;

			# кнопка редактирования
			$edit = ($bitcat || $bitLoadMore  ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : NULL);

			$class[] = "portfolio-item";
			$class[] = "obj";
			$class[] = "obj{$this->id}";
			$class[] = objHidden($this->Checked, $this->citytarget, $cityid);
			$class[] = $this->type;
            if($this->animateKey) $class[] = "wow {$this->animateKey}";

			$html = "<li class='".implode(" ", $class)."' ".($this->animateKey ? str_replace(",", ".", "data-wow-delay='".($this->animateDelay + ($this->RowNum * $this->animateDelayStep))."s'") : "").">
						{$edit}
						{$this->photoMain}
						".($this->name || $this->text || $this->price ? "<div class='port-item-data'>
							".($this->name ? "<div class='port-item-name'><a href='{$this->fullLink}'>{$this->name}</a></div>" : NULL)."
							".($this->text ? "<div class='port-item-text'>{$this->text}</div>" : NULL)."
							".($this->price ? "<div class='port-item-price'>".($this->firstprice ? "от " : "")."<span class='cen' itemprop='price'>".price($this->price)."</span> {$currency[html]}</div>" : NULL)."
						</div>": "")."
						{$this->adminButtons}
					</li>";
		}
		return $html;
	}

	# внутряк
	public function getItemFull(){
		if(function_exists('class2021_getItemFull')){
			$html = class2021_getItemFull($this); // своя функция
		}else{
			global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $currency, $setting_texts;

			$callformlink = $setting_texts['portfolio_link_call']['checked'] ? $setting_texts['portfolio_link_call']['name'] : "Заказать обратный звонок";
			$likedwork = $setting_texts['liked_work']['checked'] ? $setting_texts['liked_work']['name'] : "Понравилась работа?";

			# кнопка редактирования
			$edit = ($bitcat || $bitLoadMore ? editObjBut($this->editLink, null, 'портфолио') : NULL);
			$this->textfull_bottom = \Korzilla\Replacer::replaceText($this->textfull_bottom);
			$text = \Korzilla\Replacer::replaceText(($this->textfull ? $this->textfull : $this->text));
			 
			$class[] = 'portfolio-item-full';

			$html = "{$edit}
					<div class='".implode(" ", $class)."'>
						<div class='portfolio-image'>
							{$this->bigphoto}
						</div>
						<div class='portfolio-data'>
							<div class='portfolio-nav'>
								".($this->nc_prev_object ? "<a href='{$this->nc_prev_object}' class='portfolio-next icons i_left'>Предыдущий</a>" : "")."
								".($this->nc_next_object ? "<a href='{$this->nc_next_object}' class='portfolio-prev icons i_right'>Следующий</a>" : "")."
							</div>
							".($this->link ? "<div class='portfolio-link'><span class='portfolio-link-text'>Подробнее: </span><a target='_blank' href='{$this->link}'>{$this->link}</a></div>" : NULL)."
							".($this->price ? "<div class='portfolio-price'>".($this->firstprice ? "от " : "")."<span class='cen' itemprop='price'>".price($this->price)."</span> {$currency[html]}</div>" : NULL)."
							".($text ? "<div class='portfolio-text txt'>{$text}</div>" : NULL)."
							<h3>{$likedwork}</h3>
							<a class='btn-strt-a portfolio-call' href='/callme/add_callme.html' data-lc-href='/callme/add_callme.html?isNaked=1' id='link-callme' title='{$callformlink}' data-rel='lightcase' data-maxwidth='390' data-groupClass='callme modal-form'>{$callformlink}</a>
						</div>
						".($this->textfull_bottom ? "<div class='portfolio-text-bottom txt'>{$this->textfull_bottom}</div>" : "")."

					</div>";
		}
		return $html;
	}

	# photo
	public function setPhoto(){
		if(function_exists('class2021_setPhoto')){
			class2021_setPhoto($this); // своя функция
		}else{
			global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr;

			# шаблон вывода картинок
			$this->photo->set_template(array('record' => "%Preview%"));

			if($this->full){

				# photo объекта
				if ($this->photo->records) {
					$bigphoto = $smallphoto = '';
					foreach($this->photo->records as $i => $f) {
						$alt = $f['Name'] ? $f['Name'] : $this->name;
						$bigphoto .= "<a href='{$IMG_HOST}{$f['Path']}' title='{$f['Name']}' data-rel='lightcase:image-in-portfolio'><img itemprop='image' loading='lazy' src='{$IMG_HOST}{$f['Path']}' alt='{$alt}'></a>";
						$smallphoto .= "<div class='g_m_img {$this->image_default}' data-val='{$i}'><img loading='lazy' src='{$IMG_HOST}{$f['Preview']}' alt='{$alt}'></div>";
					}
				}

				# файла нет
				if (!$bigphoto) $bigphoto = "<div class='image-default image-contain image-noimg'><img loading='lazy' src='".getnoimage("big")."' style='width: 100%;'></div>";

				$this->bigphoto = "<div class='portfolio-photo owl-carousel'>{$bigphoto}</div>";
				$this->smallphoto = $smallphoto ? "<div class='portfolio-photos'>{$smallphoto}</div>"  : "";

			}else{
				# загруженная картинка
				if($this->photo_preview){
					$image = $this->photo_preview;
				}
				# внутрение картинки
				if(!$image && $this->photo->get_record(1)){
					$image = $this->photo->get_record(1);
				}

				# файла нет
				if (!$image) $image = getnoimage("big");

				$photoMain = "<div class='{$this->image_default}'>
									<a href='{$this->fullLink}'>
										<img src='{$image}' alt='{$this->name}'>
									</a>
								</div>";

				$this->photoMain = $photoMain;
			}
		}
	}
	
	/**
	 * Получить список тэгов объекта
	 * 
	 * @return array<int,Tag>
	 */
	public function getTagList()
	{
		if ($this->tagListInited) {
			return $this->tagList;
		}
		
		$provider = new TagProvider();

		$filter = $provider->filterGet();
		$filter->objectType[] = $this->classID;
		$filter->objectId[] = $this->id;

		$this->tagListInited = true;

		return $this->tagList = $provider->tagGetList($filter);		
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
