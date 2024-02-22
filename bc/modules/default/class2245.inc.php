<?php
class Class2245
{
    private $f = array(); # массив параметров
    private $htmlTemplate = array(
        'prefix' => '',
        'photo' => '',
        'name' => '',
        'suffix' => ''
    );

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
        $edit = ($this->sub == $current_sub['Subdivision_ID'] ? ($bitcat || $bitLoadMore ? editObjBut(nc_message_link($this->id, $this->classID, "edit")) : $this->AdminButtons) : NULL);

        $class[] = "app-btn-antage-item";
        $class[] = "obj";
        $class[] = "obj{$this->id}";
        $class[] = objHidden($this->Checked, $this->citytarget, $cityid);
        if ($this->animateKey) $class[] = "wow {$this->animateKey}";
            
        switch ($this->templatekey) {
            case 'Меню':
                $this->link = '';

                $this->onclick = ($this->onclick ? $this->onclick : 'appMenuClick("menu")');

                $this->htmlTemplate['photo'] = ($this->icon ? $this->getPhoto() : "<div class='{$this->image_default}'>
                    ".file_get_contents(__DIR__.'/mobile_icon/menu.svg')."
                </div>");
                break;

            case 'Профиль':
                $this->link = ($this->link ? $this->link  : '/profile/');

                $this->htmlTemplate['photo'] = ($this->icon ? $this->getPhoto() : "<div class='{$this->image_default}'>
                    ".file_get_contents(__DIR__.'/mobile_icon/profile.svg')."
                </div>");
                break;

            case 'Домой':
                $this->link = ($this->link ? $this->link  : '/');

                $this->htmlTemplate['photo'] = ($this->icon ? $this->getPhoto() : "<div class='{$this->image_default}'>
                    ".file_get_contents(__DIR__.'/mobile_icon/homepage.svg')."
                </div>");
                break;

            case 'Корзина':
                $this->link = ($this->link ? $this->link  : '/cart/');

                $this->htmlTemplate['prefix'] = "<div class='mpanel-cart ".($_SESSION['cart']['items'] ? "mpanel-cart-active" : "mpanel-cart-unactive")."'>
                    <span class='mpanel-cart-count'>".count($_SESSION['cart']['items'])."</span>
                </div>";

                $this->htmlTemplate['photo'] = ($this->icon ? $this->getPhoto() : "<div class='{$this->image_default}'>
                    ".file_get_contents(__DIR__.'/mobile_icon/cart.svg')."
                </div>");
                break;
            default:
                $this->htmlTemplate['photo'] = $this->getPhoto();
                break;
        }

        if ($this->name) {
            $this->htmlTemplate['name'] = "<div class='app-btn-data'>"
                .($this->link ? "<a href='{$this->link}'><div class='app-btn-name'>{$this->name}</div></a>" : "<div class='app-btn-name'>{$this->name}</div>")."
          </div>";
        }

        $html = "<li class='".implode(" ", $class)."' ".($this->onclick ? "onclick='{$this->onclick}'" : '').">
            {$edit}
            <div class='app-btn-info'>
				".($this->link ? "<a href='{$this->link}'>" : null)."
					{$this->htmlTemplate['prefix']}
					{$this->htmlTemplate['photo']}
					{$this->htmlTemplate['name']}
					{$this->htmlTemplate['suffix']}
				".($this->link ? "</a>" : null)."
            </div>
        </li>";

        return $html;
    }

    # get photo
    public function getPhoto()
    {
        global $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $titleArr;

        if ($this->icon) {
            $info = new SplFileInfo($this->icon);
            if('svg' == $info->getExtension()) {
                $html = file_get_contents($DOCUMENT_ROOT.$this->icon);
            } else {
                $html = "<div class='{$this->image_default}'>
                        <img src='{$this->icon}' alt='{$this->name}'>
                </div>";
            }
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
