<?

class Class2253{

	private $f = array(); # массив параметров
	// private $count = 1; # кол-во при выводе в поле count

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
			$html = class2110_getItem($this); // своя функция
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
					</li>";
		}
		return $html;
	}
	# внутряк
	public function getItemFull(){
		if(function_exists('class2021_getItemFull')){
			$html = class2110_getItemFull($this); // своя функция
		}else{
			global $db, $cityid, $bitcat, $bitLoadMore, $catalogue, $currency, $setting_texts, $current_sub;

			$callformlink = $setting_texts['portfolio_link_call']['checked'] ? $setting_texts['portfolio_link_call']['name'] : "Заказать обратный звонок";
            if($this->form_id) {
				$htmlForm = htmlAnyForm($this->form_id, ['moreRequest' => "&t_item_name={$this->name}"]);
				// $formParam = explode('/', $this->form_id);
                // $htmlForm  = nc_objects_list($formParam[0], $formParam[1], str_replace('&amp;', '&', "{$formParam[2]}"));
            }
			# кнопка редактирования
			$edit = ($bitcat || $bitLoadMore ? editObjBut($this->editLink, null, 'портфолио') : NULL);

			$class[] = 'portfolio-item-full';

			$html = "{$edit}
					<div class='".implode(" ", $class)."'>
						<div class='portfolio-image'>
							{$this->bigphoto}
						</div>
						<div class='portfolio-data'>
							<!--<div class='portfolio-nav'>
								".($this->nc_prev_object ? "<a href='{$this->nc_prev_object}' class='portfolio-next icons i_left'>Предыдущий</a>" : "")."
								".($this->nc_next_object ? "<a href='{$this->nc_next_object}' class='portfolio-prev icons i_right'>Следующий</a>" : "")."
							</div>-->
							".($this->link ? "<div class='portfolio-link'><span class='portfolio-link-text'>Подробнее: </span><a target='_blank' href='{$this->link}'>{$this->link}</a></div>" : NULL)."
							".($this->price ? "<div class='portfolio-price' itemprop='offers' itemscope itemtype='http://schema.org/AggregateOffer'>".($this->firstprice ? "от " : "")."<span class='cen' ".($this->firstprice ? "itemprop='lowPrice'" : "itemprop='price'")." content='{$this->price}'>".price($this->price)."</span> {$currency[html]}</div>" : NULL)."
							".($this->textfull || $this->text ? "<div class='portfolio-text txt'>".($this->textfull ? $this->textfull : $this->text)."</div>" : NULL)."
							<h3>{$likedwork}</h3>
                            ".($htmlForm != '' ? $htmlForm : "<a class='btn-strt-a portfolio-call' href='/callme/?isNaked=1' id='link-callme' title='{$callformlink}' data-rel='lightcase' data-maxwidth='390' data-groupClass='callme modal-form'>{$callformlink}</a>")."
						</div>
						".$this->getVideo()."
						".($this->textfull_bottom ? "<div class='portfolio-text-bottom txt'  itemprop='description'>{$this->textfull_bottom}</div>" : "")."
						".$this->bottomFormHtml()."
					</div>";
		}
		return $html;
	}

	#bottom_form
	public function bottomFormHtml() {
		if (!$this->bottom_form) return '';
		global $db, $catalogue;
		$formParam = $db->get_row("SELECT Sub_Class_ID as cc, Subdivision_ID as sub FROM `Sub_Class` WHERE `Class_ID` IN (2013) AND `Catalogue_ID` = {$catalogue}", ARRAY_A);
		$htmlForm  = nc_objects_list($formParam['sub'], $formParam['cc'], "&nc_ctpl=2256&t_item_name={$this->name}");
		
		$html = "
		<div id='bottom-form'>
			<span class='bottom-form-name'>Остались вопросы ?</span>
			{$htmlForm}
		</div>";
		return $html;
	}
	
	#video
	public function getVideo() {
		if (!$this->video) return '';
		$html = "
		<div class='portfolio-video'>
				<iframe 
						width='100%' 
						height='100%' 
						src='https://www.youtube.com/embed/$this->video' 
						frameborder='0' 
						allow='	accelerometer; 
								autoplay; 
								clipboard-write; 
								encrypted-media; 
								gyroscope; 
								picture-in-picture' 
						allowfullscreen>
					</iframe>
		</div>";
		return $html;
	}

	# photo
	public function setPhoto(){
		global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr;

		# шаблон вывода картинок
		$this->photo->set_template(array('record' => "%Preview%"));

		if($this->full){

			# photo объекта
			if ($this->photo->records) {
				foreach($this->photo->records as $i => $f) {
					$alt = $f[Name] ? $f[Name] : $this->name;
					$bigphoto .= "<a href='{$IMG_HOST}{$f[Path]}' title='{$f[Name]}' data-rel='lightcase:image-in-portfolio'><img itemprop='image' src='{$IMG_HOST}{$f[Path]}' alt='{$alt}'></a>";
					$smallphoto .= "<div class='g_m_img {$this->image_default}' data-val='{$i}'><img src='{$IMG_HOST}{$f[Preview]}' alt='{$alt}'></div>";
				}
			}

			# файла нет
			if (!$bigphoto) $bigphoto = "<div class='image-default image-contain image-noimg'><img src='".getnoimage("big")."' style='width: 100%;'></div>";

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
