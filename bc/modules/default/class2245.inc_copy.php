<?php
class Class2245
{
    private $f = array(); # массив параметров
    // private $count = 1; # кол-во при выводе в поле count

    public function __construct($fieldsArray = array())
    {
        # поля объекта
        $this->f = $fieldsArray;

        $this->main();
    }

    # main
    private function main()
    {
        global $setting_texts, $setting;
        # id объекта
        if (!$this->id && $this->RowID) {
            $this->id = $this->RowID;
        }
    }
        # Объект преимцщество
    public function getItem()
    {
        global $db, $cityid, $bitcat, $bitLoadMore, $catalogue,$current_sub;
        # кнопка редактирования
        $edit = ($bitcat || $bitLoadMore ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : $this->AdminButtons);

        $class[] = "app-btn-antage-item";
        $class[] = "obj";
        $class[] = "obj{$this->id}";
        $class[] = objHidden($this->Checked, $this->citytarget, $cityid);
        if ($this->animateKey) {
            $class[] = "wow {$this->animateKey}";
        }


        switch ($this->template) {
            case 'template-1':
            case 'template-2':
                $html = "<li class='".implode(" ", $class)." ' ".($this->animateKey ? str_replace(",", ".", "data-wow-delay='".($this->animateDelay + ($this->RowNum * $this->animateDelayStep))."s'") : "")." ".($this->onclick ? "onclick='{$this->onclick}'" : '').">
							".($this->sub == $current_sub['Subdivision_ID'] ? $edit : '')."
                            <div class='app-btn-info'>
                            ".($this->link == '/cart/' ? "<div class='mpanel-item mpanel-cart ".($_SESSION['cart']['items'] ? "mpanel-cart-active" : NULL)."'>
                            <span class='mpanel-cart-count'>".count($_SESSION['cart']['items'])."</span>
                        </div>" : NULL)."
								".$this->getPhoto()."
								".($this->name || $this->text ? "
                                    <div class='app-btn-data'>
										".($this->name ? "
											".($this->link ? "<a href='{$this->link}'>" : "")."
												<div class='app-btn-name'>{$this->name}</div>
											".($this->link ? "</a>" : "")."
										" : null)."
									</div>
								" : null)."
							</div>
						</li>";
                break;
        }


        return $html;
    }

    # get photo
    public function getPhoto()
    {
        global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr;

        if ($this->icon) {
            $html = "<div class='{$this->image_default}'>
						".($this->link ? "<a href='{$this->link}'>" : null)."
							<img src='{$this->icon}' alt='{$this->name}'>
						".($this->link ? "</a>" : null)."
					</div>";
        }

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
