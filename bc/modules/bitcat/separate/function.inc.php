<?php
class separate {
	private $curCat, $catID;

	public function __construct() {
		global $db, $nc_core, $perm, $AUTH_USER_ID, $ADMIN_PATH, $perm;
		$this->curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
		$this->catID = $this->curCat['Catalogue_ID'];
	}

	# вызов действия
	public function init($action) {
		global $catID;

		if (strstr($_SERVER[HTTP_REFERER],"рф")) $refer = encode_host($_SERVER[HTTP_REFERER]); else $refer = $_SERVER[HTTP_REFERER];
		if (strstr($refer,($this->curCat['https'] ? "https://":"http://").$_SERVER[HTTP_HOST])) {
			switch ($action) {
				case 'getBody': return $this->getBody();
				case 'getContent': return $this->getContent();
				case 'getInComponent': return $this->getInComponent();
				case 'saveParam': return $this->saveParam();
				case 'getFileObjectData': return $this->getFileObjectData($_POST[id], $_POST[i]);
			};
		} else {
			return encode_host($_SERVER[HTTP_REFERER])." | ".$_SERVER[HTTP_HOST];
		}
	}


	# блок основных настроек
	private function getComponents($id = '') {
		$components = array(
			'2001' => array(
					'name' => 'Каталог',
					'template' => array(
						'preffix' => "FormPrefix.html",
						'suffix' => "FormSuffix.html",
						'system' => "Settings.html"
					),
					'templateAdd' => array(
						'addForm' => "AddTemplate.html",
						'addCount' => "AddCond.html",
						'addAction' => "AddActionTemplate.html"
					),
					'templateEdit' => array(
						'addForm' => "EditTemplate.html",
						'addCount' => "EditCond.html",
						'addAction' => "EditActionTemplate.html"
					)
				),
			'2003' => array(
					'name' => 'Новости',
					'template' => array(
						'preffix' => "FormPrefix.html",
						'objects' => 1,
						'suffix' => "FormSuffix.html",
						'fullObjects' => 1,
						'system' => "Settings.html"
					),
					'templateAdd' => array(
						'addForm' => "AddTemplate.html",
						'addCount' => "AddCond.html",
						'addAction' => "AddActionTemplate.html"
					),
					'templateEdit' => array(
						'addForm' => "EditTemplate.html",
						'addCount' => "EditCond.html",
						'addAction' => "EditActionTemplate.html"
					)
				),
			'2005' => array(
					'name' => 'Корзина',
					'template' => array(
						'preffix' => "FormPrefix.html",
						//'objects' => 1,
						'suffix' => "FormSuffix.html",
						//'fullObjects' => 1,
						'system' => "Settings.html"
					),
					'templateAdd' => array(
						'addForm' => "AddTemplate.html",
						'addCount' => "AddCond.html",
						'addAction' => "AddActionTemplate.html"
					),
					'templateEdit' => array(
						'addForm' => "EditTemplate.html",
						'addCount' => "EditCond.html",
						'addAction' => "EditActionTemplate.html"
					)
				),
			'2021' => array(
					'name' => 'Портфолио',
					'templateAdd' => array(
						'addAction' => "AddActionTemplate.html"
					),
					'templateEdit' => array(
						'addAction' => "EditActionTemplate.html"
					)
				),
			'2073' => array(
					'name' => 'Индивидуальный',
					'template' => array(
						'preffix' => "FormPrefix.html",
						//'objects' => 1,
						'suffix' => "FormSuffix.html",
						//'fullObjects' => 1,
						'system' => "Settings.html"
					),
					'templateAdd' => array(
						'addForm' => "AddTemplate.html",
						'addCount' => "AddCond.html",
						'addAction' => "AddActionTemplate.html"
					),
					'templateEdit' => array(
						'addForm' => "EditTemplate.html",
						'addCount' => "EditCond.html",
						'addAction' => "EditActionTemplate.html"
					)
				),
		);
		return is_numeric($id) ? $components[$id] : $components;
	}

	# блок основных настроек
	private function getBody() {
		return json_encode(ARRAY(
			"status" => "ok",
			"leftMenu" => $this->getLeftMenu(),
			"rightContent" => $this->rightContent()
		));
	}

	# взять getLeftMenu
	private function getLeftMenu() {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting;

		foreach ($this->getComponents() as $id => $component) {
			$li[] = "<li class='sep-first' onclick='load.clickItem(\".sep-load-{$id}\", this)' data-id='{$id}' data-callback='separateCallback'>
						<div class='sep-first-name'><span>#{$id}</span>{$component[name]}</div>
					</li>";
		}

		$leftMenu = "<div class='separate_wrap'>
						<div class='separate_menubody'>
							<div class='separate_menubody_second'>
								<div class='sep-component'>Компоненты</div>
								<ul id='separate_adminmenu'>".implode("", $li)."</ul>
							</div>
						</div>
					</div>";

		return $leftMenu;
	}

	# взять rightContent
	private function rightContent() {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting;

		foreach ($this->getComponents() as $id => $component) {
			$views[] = "<div class='sep-view sep-load-{$id}' data-loaditem='/bc/modules/bitcat/separate/index.php?action=getContent&id={$id}' data-nobg='1'>
						</div>";
		}

		return implode("", $views);
	}

	# взять getContent
	private function getContent() {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting;

 		$id = securityForm($_GET[id]);

 		if($id){
 			$component = $this->getComponents($id);

 			$preffix = $component[template][preffix] ? $this->getFileData($id, "template", $component[template][preffix]) : "";
 			$suffix = $component[template][suffix] ? $this->getFileData($id, "template", $component[template][suffix]) : "";
 			$system = $component[template][system] ? $this->getFileData($id, "template", $component[template][system]) : "";

			$objects = $component[template][objects] ? $this->getFileObjectsData($id, 'objects') : "";
			$fullObjects = $component[template][objects] ? $this->getFileObjectsData($id, 'fullObjects') : "";

 			$templateAddForm = $component[templateAdd][addForm] ? $this->getFileData($id, "templateAdd", $component[templateAdd][addForm]) : "";
 			$templateAddCount = $component[templateAdd][addCount] ? $this->getFileData($id, "templateAdd", $component[templateAdd][addCount]) : "";
 			$templateAddAction = $component[templateAdd][addAction] ? $this->getFileData($id, "templateAdd", $component[templateAdd][addAction]) : "";

 			$templateEditForm = $component[templateEdit][addForm] ? $this->getFileData($id, "templateEdit", $component[templateEdit][addForm]) : "";
 			$templateEditCount = $component[templateEdit][addCount] ? $this->getFileData($id, "templateEdit", $component[templateEdit][addCount]) : "";
 			$templateEditAction = $component[templateEdit][addAction] ? $this->getFileData($id, "templateEdit", $component[templateEdit][addAction]) : "";

			return "<div class='component-item'>
						<div class='component-name'><span>#{$id}</span>{$component[name]}</div>
						<div class='component-body'>
							<ul class='tabs'>
						    	".($component[template] ? "<li class='tab'><a href='#template-{$id}' class='active'>Редактирование компонента</a></li>" : "")."
							    ".($component[templateAdd] ? "<li class='tab'><a href='#addActions-{$id}'>Добавления</a></li>" : "")."
							    ".($component[templateEdit] ? "<li class='tab'><a href='#editActions-{$id}'>Редактирования</a></li>" : "")."
							</ul>
							<div class='tabs-body'>


								".($component[template] ? "<div id='template-{$id}'>
									<ul class='tabs'>
								    	".($preffix ? "<li class='tab'><a href='#preff-{$id}' class='active'>Префикс списка объектов</a></li>" : "")."
									    ".($objects ? "<li class='tab'><a href='#objects-{$id}'>Вывод объекта</a></li>" : "")."
									    ".($suffix ? "<li class='tab'><a href='#suff-{$id}'>Суффикс списка объектов</a></li>" : "")."
									    ".($fullObjects ? "<li class='tab'><a href='#fullObjects-{$id}'>Полное отображение объекта</a></li>" : "")."
									    ".($system ? "<li class='tab'><a href='#sys-{$id}'>Системные настройки</a></li>" : "")."
									</ul>
									<div class='tabs-body'>
										".($preffix ? "<div id='preff-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($preffix[checked] ? "" : "no-use")."' data-idc='{$id}-a'>".bc_textarea("textarea[{$id}][template][FormPrefix][text]", $preffix[data], "<a href='#' class='get-component' data-id='{$id}' data-get='FormPrefix'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][template][FormPrefix][checked]", 1, "использовать код", $preffix[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($objects ? "<div id='objects-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												{$objects}
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($suffix ? "<div id='suff-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($suffix[checked] ? "" : "no-use")."' data-idc='{$id}-b'>".bc_textarea("textarea[{$id}][template][FormSuffix][text]", $suffix[data], "<a href='#' class='get-component' data-id='{$id}' data-get='FormSuffix'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][template][FormSuffix][checked]", 1, "использовать код", $suffix[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($fullObjects ? "<div id='fullObjects-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												{$fullObjects}
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($system ? "<div id='sys-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($system[checked] ? "" : "no-use")."' data-idc='{$id}-c'>".bc_textarea("textarea[{$id}][template][Settings][text]", $system[data], "<a href='#' class='get-component' data-id='{$id}' data-get='Settings'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][template][Settings][checked]", 1, "использовать код", $system[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
									</div>
								</div>" : "")."
								".($component[templateAdd] ? "<div id='addActions-{$id}' data-idc='{$id}' class='none'>
									<ul class='tabs'>
								    	".($templateAddForm ? "<li class='tab'><a href='#templateAddForm-{$id}' class='active'>Альтернативная форма добавления объекта</a></li>" : "")."
									    ".($templateAddCount ? "<li class='tab'><a href='#templateAddCount-{$id}'>Условия добавления объекта</a></li>" : "")."
									    ".($templateAddAction ? "<li class='tab'><a href='#templateAddAction-{$id}'>Действие после добавления объекта</a></li>" : "")."
									</ul>
									<div class='tabs-body'>
										".($templateAddForm ? "<div id='templateAddForm-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateAddForm[checked] ? "" : "no-use")."' data-idc='{$id}-d'>".bc_textarea("textarea[{$id}][templateAdd][AddTemplate][text]", $templateAddForm[data], "<a href='#' class='get-component' data-id='{$id}' data-get='AddTemplate'>скопировать из компнента</a>:".bc_checkbox("textarea[{$id}][templateAdd][AddTemplate][checked]", 1, "использовать код", $templateAddForm[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($templateAddCount ? "<div id='templateAddCount-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateAddCount[checked] ? "" : "no-use")."' data-idc='{$id}-e'>".bc_textarea("textarea[{$id}][templateAdd][AddCond][text]", $templateAddCount[data], "<a href='#' class='get-component' data-id='{$id}' data-get='AddCond'>скопировать из компнента</a>:".bc_checkbox("textarea[{$id}][templateAdd][AddCond][checked]", 1, "использовать код", $templateAddCount[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($templateAddAction ? "<div id='templateAddAction-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateAddAction[checked] ? "" : "no-use")."' data-idc='{$id}-f'>".bc_textarea("textarea[{$id}][templateAdd][AddActionTemplate][text]", $templateAddAction[data], "<a href='#' class='get-component' data-id='{$id}' data-get='AddActionTemplate'>скопировать из компнента</a>:".bc_checkbox("textarea[{$id}][templateAdd][AddActionTemplate][checked]", 1, "использовать код", $templateAddAction[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
									</div>
								</div>" : "")."
								".($component[templateEdit] ? "<div id='editActions-{$id}' data-idc='{$id}' class='none'>
									<ul class='tabs'>
								    	".($templateEditForm ? "<li class='tab'><a href='#templateEditForm-{$id}' class='active'>Альтернативная форма изменения объекта</a></li>" : "")."
									    ".($templateAddCount ? "<li class='tab'><a href='#templateAddCount-{$id}'>Условия изменения объекта</a></li>" : "")."
									    ".($templateAddAction ? "<li class='tab'><a href='#templateAddAction-{$id}'>Действие после изменения объекта</a></li>" : "")."
									</ul>
									<div class='tabs-body'>
										".($templateEditForm ? "<div id='templateEditForm-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateEditForm[checked] ? "" : "no-use")."' data-idc='{$id}-i'>".bc_textarea("textarea[{$id}][templateEdit][EditTemplate][text]", $templateEditForm[data], "<a href='#' class='get-component' data-id='{$id}' data-get='EditTemplate'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][templateEdit][EditTemplate][checked]", 1, "использовать код", $templateEditForm[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($templateEditCount ? "<div id='templateEditCount-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateEditCount[checked] ? "" : "no-use")."' data-idc='{$id}-j'>".bc_textarea("textarea[{$id}][templateEdit][EditCond][text]", $templateEditCount[data], "<a href='#' class='get-component' data-id='{$id}' data-get='EditCond'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][templateEdit][EditCond][checked]", 1, "использовать код", $templateEditCount[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
										".($templateEditAction ? "<div id='templateEditAction-{$id}' data-idc='{$id}'>
											<form class='ajax2' enctype='multipart/form-data' action='/bc/modules/bitcat/separate/index.php?action=saveParam' method='post'>
												<div class='colline colline-height ".($templateEditAction[checked] ? "" : "no-use")."' data-idc='{$id}-k'>".bc_textarea("textarea[{$id}][templateEdit][EditActionTemplate][text]", $templateEditAction[data], "<a href='#' class='get-component' data-id='{$id}' data-get='EditActionTemplate'>скопировать из компнента</a>".bc_checkbox("textarea[{$id}][templateEdit][EditActionTemplate][checked]", 1, "использовать код", $templateEditAction[checked]))."</div>
												<div class='bc_submitblock'><div class='bc_btnbody'><span class='bc-btn'><input type='submit' value='Сохранить изменения''></span></div></div>
											</form>
										</div>" : "")."
									</div>
								</div>" : "")."
							</div>
						</div>
					</div>";
 		}

	}

	private function getFileObjectsData($id, $type) {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $DOCUMENT_ROOT, $pathInc2;

 		$path = $DOCUMENT_ROOT.$pathInc2."/template/{$id}/template/{$type}/";
 		$i = 1;

		if(is_dir($path)){
			if($handle = opendir($path)){
				while(false !== ($file = readdir($handle))){
					if($file != '.' && $file != '..'){
						$pathFile = $path.$file;
						if(is_file($pathFile)){
							$d = @file_get_contents($pathFile);
							$lines .= $this->getFileObjectData($id, $i, $type, $d, str_replace(".php", "", $file));
							$i++;
						}
					 }
		        }
		        closedir($handle);
		    }
		}

		if(!$lines) $lines = $this->getFileObjectData($id, $i, $type);

		$html = "<div class='separate-objects'>
					<div id='separate-{$type}-{$id}'>
						{$lines}
					</div>
					<a class='add-btn' href='' onclick='addline_separate(\"separate-{$type}-{$id}\"); return false;'>добавить еще</a>
				</div>";

		return $html;

	}
	private function getFileObjectData($id, $i = 1, $type, $data = "", $nameInput = "") {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $DOCUMENT_ROOT, $pathInc2;
		$name = "{$id}-{$type}-{$i}";
		$lines = "<div class='textarea-body' data-name='vs{$name}' data-num='{$i}' data-id='{$id}' data-idc='{$name}'>
						<span class='remove-template'>удалить</span>
						<div class='colline colline-2'>".bc_input("textarea[{$id}][template][{$type}][{$i}][nameEng]", $nameInput, "Имя файла (латинскими символы и цифры)", "onKeyUp=\"if(/[^a-zA-Z0-9-]/i.test(this.value)){this.value='';}\"", 1)."</div>
						<div class='colline colline-height'>
							<div class='textarea-field'>
								<textarea cols='501' rows='6' name='textarea[{$id}][template][{$type}][{$i}][text]'>{$data}</textarea>
							</div>
						</div>
					</div>";
		return $lines;
	}

	private function getFileData($id, $dirname, $name) {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $DOCUMENT_ROOT, $pathInc2;

 		$path = $DOCUMENT_ROOT.$pathInc2."/template/{$id}/{$dirname}/{$name}";
		$checked = is_file($path) ? true : false;
		$data = "";

 		$path_backup = $DOCUMENT_ROOT.$pathInc2."/template/{$id}/{$dirname}/backup_{$name}";

 		if(is_file($path)){
 			$data = @file_get_contents($path);
 		}else if(is_file($path_backup)){
 			$data = @file_get_contents($path_backup);
 		}

		return array(
			'checked' => $checked,
			'data' => $data
		);

	}

	# взять getInComponent
	private function getInComponent() {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $DOCUMENT_ROOT;

 		$id = securityForm($_POST[id]);
 		$get = securityForm($_POST[get]);

 		if($id && $get){

			$filepath = $DOCUMENT_ROOT."/template/class/{$id}/{$get}.html";
			if(is_file($filepath)) $file = @file_get_contents($filepath);
			if($file){
				return $file;
			}
 		}

	}
	# сохранение
	private function saveParam() {
		global $db, $perm, $settingCont, $catID, $AUTH_USER_ID, $setting, $DOCUMENT_ROOT, $pathInc2;

 		$textarea = securityForm($_POST[textarea]);
 		if(is_array($textarea)){
 			$b = $DOCUMENT_ROOT.$pathInc2;
			if(!is_dir($b)) mkdir($b);
 			$path = $DOCUMENT_ROOT.$pathInc2."/template";
			if(!is_dir($path)) mkdir($path);

 			foreach ($textarea as $class => $tab) {
				$pathClass = $path."/{$class}";
				if(!is_dir($pathClass)) mkdir($pathClass);

 				foreach ($tab as $nameTab => $templates) {
					$pathNameTab = $pathClass."/{$nameTab}";
					if(!is_dir($pathNameTab)) mkdir($pathNameTab);

 					foreach ($templates as $name => $v) {
 						if($name == 'objects'){
							$pathNameTabObjects = $pathNameTab."/objects";
							if(!is_dir($pathNameTabObjects)){
								mkdir($pathNameTabObjects);
							}else{
								# удаление файлов
								if (file_exists($pathNameTabObjects)) {
									foreach (glob($pathNameTabObjects.'/*') as $file) {
										unlink($file);
									}
								}
							}

							# создание
							foreach ($v as $n => $value) {

								if(!$value[nameEng]) $value[nameEng] = "name".rand();

								$file = $pathNameTabObjects."/{$value[nameEng]}.php";
								file_put_contents($file, html_entity_decode(stripslashes($value[text]), ENT_QUOTES | ENT_XML1, 'UTF-8'));
 							}
 						}else{
							$file_backup = $pathNameTab."/backup_{$name}.html";
							$file = $pathNameTab."/{$name}.html";

							file_put_contents($file_backup, html_entity_decode(stripslashes($v[text]), ENT_QUOTES | ENT_XML1, 'UTF-8'));

	 						if($v[checked]){
								file_put_contents($file, html_entity_decode(stripslashes($v[text]), ENT_QUOTES | ENT_XML1, 'UTF-8'));
	 						}else{
	 							if(is_file($file)) unlink($file);
	 						}
 						}
 					}
 				}
 			}
 		}


		return json_encode(ARRAY(
			"title" => "ОК"
		));

	}



}
?>
