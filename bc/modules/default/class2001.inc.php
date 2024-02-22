<?php

class Class2001
{

    public $f = array(); # массив параметров
    public $count = 1; # кол-во при выводе в поле count
    public $incart = 0; # в корзине или нет
    public $full = 0; # внутряк или нет
    public $cart = 0; # возможность добавление в корзину
    public $order = 0; # возможность оформлять заказ
    public $photos = []; # массив фото 

    public $orderCountPriceCoverted;

    public function __construct($fieldsArray = array())
    {
        # поля объекта
        $this->f = $fieldsArray;

        $this->main();
        //$this->setVariable();
        $this->setVariableName();
        $this->setColor();
        $this->setPrice();
        $this->setlabel();
        $this->setDiscontTime();
        $this->setLink();
        $this->setArt();
        $this->setArt2();
        $this->setStock();
        $this->setVendor();
        $this->setParamsItem();
        $this->setParamsItemPreviewHTML();
        $this->setPhoto();
        $this->setTags();
        $this->setdeliveryMap();
        $this->btn();
        $this->buyOneClickLink();
        $this->comparison();
        $this->favoritItem();
    }

    # main
    public function main()
    {

        global $current_catalogue, $setting_texts, $_SESSION, $setting, $bitcat, $AUTH_USER_ID, $currency, $current_sub, $cityname, $db, $settingServiceSupplers;
        # заголовки
        $this->artname = ($setting_texts['full_cat_art']['checked'] ? $setting_texts['full_cat_art']['name'] : "Артикул");
        $this->artname2 = ($setting_texts['full_cat_art2']['checked'] ? $setting_texts['full_cat_art2']['name'] : "Код");
        $this->nalich = ($setting_texts['full_cat_stock']['checked'] ? $setting_texts['full_cat_stock']['name'] : "в наличии");
        $this->notnalich = ($setting_texts['full_cat_notstock']['checked'] ? $setting_texts['full_cat_notstock']['name'] : "нет в наличии");
        $this->podzakaz = ($setting_texts['full_cat_pstock']['checked'] ? $setting_texts['full_cat_pstock']['name'] : "Под заказ");
        $this->incardtext = ($setting_texts['btn_incart']['checked'] ? $setting_texts['btn_incart']['name'] : "В корзину");
        $this->incardtextt = ($setting_texts['btn_incart2']['checked'] ? $setting_texts['btn_incart2']['name'] : "В корзине");
        $this->inordertext = ($setting_texts['btn_gotocart']['checked'] ? $setting_texts['btn_gotocart']['name'] : "Оформить заказ");
        if ($setting_texts['btn_gotocartList']['checked'] && !$this->full) {
            $inordertext = $setting_texts['btn_gotocartList']['name'];
        }
        $this->incardtext2 = "в <a href='/cart/'>" . ($setting_texts['in_cart']['checked'] ? $setting_texts['in_cart']['name'] : "корзине") . "</a>";
        $this->vendorname = ($setting_texts['full_cat_vendor']['checked'] ? $setting_texts['full_cat_vendor']['name'] : "Производитель");
        $this->vkldk_opisitem = ($setting_texts['full_cat_opisitem']['checked'] ? $setting_texts['full_cat_opisitem']['name'] : "Описание");
        $this->vkldk_opisitem2 = ($setting_texts['full_cat_opisitem2']['checked'] ? $setting_texts['full_cat_opisitem2']['name'] : "Характеристики");
        $this->nameVariable = $setting_texts['varianttext']['checked'] ? $setting_texts['varianttext']['name'] : "Вариант товара";
        $this->nameColor = $setting_texts['colortext']['checked'] ? $setting_texts['colortext']['name'] : ($setting[typeSelectVariable] == 1 ? "Вариант товара" : "Цвет товара");
        $this->notovar = $setting_texts['notovar']['checked'] ? $setting_texts['notovar']['name'] : "—";
        $this->ponrav_text = $setting_texts['full_ponravtext']['checked'] ? $setting_texts['full_ponravtext']['name'] : "Вам может понравиться";
        $this->analog_text = $setting_texts['full_analogtext']['checked'] ? $setting_texts['full_analogtext']['name'] : "Аналоги";
        $this->portfolio_text = $setting_texts['full_portfoliotext']['checked'] ? $setting_texts['full_portfoliotext']['name'] : "Портфолио";
        $this->document_text = $setting_texts['full_documenttext']['checked'] ? $setting_texts['full_documenttext']['name'] : "Документы";
        $this->gallery_text = $setting_texts['full_gallerytext']['checked'] ? $setting_texts['full_gallerytext']['name'] : "Фотогалерея";
        $this->ask_question = $setting_texts['ask_question']['checked'] ? $setting_texts['ask_question']['name'] : "Получить консультацию";
        $this->buyoneclick_text = $setting_texts['buyoneclick']['checked'] ? $setting_texts['buyoneclick']['name'] : "Купить в 1 клик";
        $this->sravnit = $setting_texts['sravnit']['checked'] ? $setting_texts['sravnit']['name'] : "Сравнить";
        $this->sravnitGo = $setting_texts['sravnitgo']['checked'] ? $setting_texts['sravnitgo']['name'] : "Перейти в сравнение";
        $this->sravnitAdd = $setting_texts['sravnitadd']['checked'] ? $setting_texts['sravnitadd']['name'] : "Добавить в сравнение";
        $this->discontTitle = $setting_texts['discontTitle']['checked'] ? $setting_texts['discontTitle']['name'] : "Успейте купить";
        $this->numQuant = $setting_texts['numQuant']['checked'] ? $setting_texts['numQuant']['name'] : "Кол-во";
        $this->share = $setting_texts['share']['checked'] ? $setting_texts['share']['name'] : "Поделиться";
        $this->fullInfo = $setting_texts['fullInfo']['checked'] ? $setting_texts['fullInfo']['name'] : "Подробная информация о продукте";
        $this->reviews = $setting_texts['reviews']['checked'] ? $setting_texts['reviews']['name'] : "Отзывы";
        $this->fastview = $setting_texts['fastview']['checked'] ? $setting_texts['fastview']['name'] : "Быстрый просмотр";

        # внутряк или нет
        $this->full = $this->f['full'] ? 1 : 0;
        # id объекта
        if (!$this->id) {
            $this->id = $this->RowID;
        }
        if (!$this->id) {
            $this->id = $this->RowID = $this->Message_ID;
        }
        # id раздела
        if (!$this->sub) {
            $this->sub = $this->Subdivision_ID;
        }
        # кол-во просмотров
        if ($this->full) {
            $this->addViewCart();
        }
        # в корзине или нет
        $this->count = $_SESSION['cart']['items'][$this->id]['count'] > 0 ? $_SESSION['cart']['items'][$this->id]['count'] : 1;
        $this->incart = $_SESSION['cart']['items'][$this->id]['count'] > 0 ? 1 : 0;
        # префикс цены
        if ($this->firstprice) {
            $this->pricePrefix = "<span class='from'>от </span> ";
        }
        # выключить дбавление в корзину
        if (permission("cart") && !$this->nocart) {
            $this->cart = 1;
        }
        # выключить дбавление в order
        if (permission("order") && !$this->noorder) {
            $this->order = 1;
        }

        if ($this->import_source != '') {
            $settingServiceSupplers = getCheckedServiceSuppliers();
            if (in_array($this->import_source, $settingServiceSupplers) && time() - (int) $this->timestamp_export > 3600) {
                if ($this->current_sub['EnglishName'] == 'search') {
                    $this->stock = $this->stock2 = $this->stock3 = $this->stock4 = 0;
                } else {
                    $this->isOutdatedSupplier = true;
                    $this->getPriceLinkSupplier = "<!--noindex-->
                    <span class='find_price'>
                        <a class='card-question' href='/search/?find={$this->art}'><span>Узнать цену</span></a>
                    </span><!--/noindex-->";
                }
            }
        }

        # № компонента
        $this->class = $this->nc_ctpl ? $this->nc_ctpl : "";
        if (!$this->class && is_array($this->current_cc)) {
            $this->class = $this->current_cc['Class_Template_ID'];
        }
        if (!$this->class) {
            $this->class = $this->classID;
        }
        # размер изображения
        if (!$this->image_default) {
            $this->image_default = image_fit($setting["size2001_fit"]);
        }
        # кнопка редактирования
        $this->edit = ($bitcat ? editObjBut(nc_message_link($this->id, $this->classID, "edit"), null, null, $this->id) : null);
        # big кнопка редактирования
        $this->editBig = ($bitcat ? editObjBut($this->editLink, null, 'товар', $this->id) : null);
        # название раздела
        # $this->subname = $setting[itemlistsub] || $this->full ? $this->current_sub[Subdivision_Name] : "";

        # шаблон вывода картинок
        $this->photo->set_template(array('record' => "%Preview%"));
        # Купить в один клик / форма оформить заказ
        $this->orderLinkParam = $setting['orderLinkParam'] ? $setting['orderLinkParam'] : "data-maxwidth='390' data-groupclass='buyoneclick' data-lc-href='/cart/add_cart.html?isNaked=1&itemId={$this->id}' href='#'";
        # параметры ссылки быстрого просмотра
        $this->modalAttrLink = "data-lc-href='{$this->fullLink}' href='#' data-rel='lightcase' data-maxwidth='810' data-groupclass='card-fast-prew'";
		$this->fullLinkFinal = $this->fullLinkFinal();
		
        # СЕО
        $this->SEOitemcardArr = [];
        if ($current_sub['AlterTitleObj'] || $setting['SEOitemcard']) {
            $this->SEOitemcardArr = array(
                "[p]" => "<p>",
                "[/p]" => "</p>",
                "[li]" => "<li>",
                "[/li]" => "</li>",
                "[ol]" => "<ol>",
                "[/ol]" => "</ol>",
                "[ul]" => "<ul>",
                "[/ul]" => "</ul>",
                "[h1]" => "<h1>",
                "[/h1]" => "</h1>",
                "[h2]" => "<h2>",
                "[/h2]" => "</h2>",
                "[h3]" => "<h3>",
                "[/h3]" => "</h3>"
            );
        }
    }
	
    /**
     * ссылка на товар
     *
     * @param null
     *
     * @return string
     */
	public function fullLinkFinal()
    {
        if (function_exists('class2001_fullLinkFinal')) {
            return class2001_fullLinkFinal($this); // своя функция
        } else {
			return $this->extlink ? $this->extlink : $this->fullLink;
		}
	}
	
	
	
    /**
     * Карточки товара
     *
     * @param string $type
     *
     * @return string
     */
    public function getCard($type = '')
    {
        if (function_exists('class2001_getCard')) {
            $html = class2001_getCard($this, $type); // своя функция
        } else {
            global $setting, $AUTH_USER_ID;
            # класс шаблона
            $type = $type ?: $setting['templateItem'] ?: 'type1';
            # html
            switch ($type) {
                case 'type1': // Стандартные карточки
                    $html = "<div class='{$this->attr('item', array($type))}' {$this->attr('data')}>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                                " . ($this->discontText || $this->labelHtml ? "<div class='blk_status'>{$this->discontText}{$this->labelHtml}</div>" : '') . "
                                {$this->photoMain}
                                <div class='blk_info'>
                                    <div class='blk_first'>
                                        " . ($this->subname ? "<div class='blk_subname'>{$this->subname}</div>" : '') . "
                                        <div class='blk_name'>{$this->link}</div>
                                        " . ($setting['itemlistart'] && $this->artHtml ? "<div class='blk_art'>{$this->artHtml}</div>" : '') . "
                                        " . ($setting['itemlistart'] && $this->art2Html ? "<div class='blk_art blk_art2'>{$this->art2Html}</div>" : '') . "
                                        " . (($setting['itemliststock'] || $setting['stockValShow']) && !$this->getPriceLinkSupplier ? "<!--noindex--><div class='blk_stock'>{$this->stockHtml}</div><!--/noindex-->" : '') . "
                                        " . ($setting['itemlistedism'] && $this->edizm ? "<div class='blk_edizm'>Ед.изм: <span>{$this->edizm}</span></div>" : "") . "
                                        " . ($this->variantsHtml ? "<div class='blk_variable'>{$this->variantsHtml}</div>" : "") . "
                                        " . ($this->variantsNameHtml ? "<div class='blk_variableName'>{$this->variantsNameHtml}</div>" : "") . "
                                        " . ($this->colorsHtml ? "<div class='blk_color'>{$this->colorsHtml}</div>" : "") . "
                                        " . ($this->descr ? "<div class='blk_text'>{$this->descr}</div>" : "") . "

                                        <div class='blk_priceblock " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                                            " . ($this->lastPrice ? "<div class='blk_last last_price'>{$this->lastPriceHtml}</div>" : "") .
                        "<div class='blk_price normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>
                                        </div>
                                        " . $this->regPrice() . "
                                        " . $this->getNoPriceText() . "
                                        " . ($setting['ves'] && $this->ves ? "<div class='blk_ves'>{$this->ves}</div>" : "") . "
                                        {$this->parametrsPreview}
                                    </div>
                                    <div class='blk_second " . ($this->discontCount ? "blk_actionmain" : "") . "'>{$this->discontCount}</div>
                                    <div class='blk_third'>
                                    " . ($this->getPriceLinkSupplier ?: null) . "
                                        " . ($this->btn() && !$this->getPriceLinkSupplier ? "<div class='block_incard'>{$this->btn()}</div>" : "") . "
                                    </div>
                                </div>
                                {$this->edit}
                            </div>";
                    break;
                case 'type2':
                    $html = "<div class='22 {$this->attr('item', array($type))}' {$this->attr('data')}>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                                " . ($this->discontText || $this->labelHtml ? "<div class='blk_status'>{$this->discontText}{$this->labelHtml}</div>" : "") . "
                                {$this->photoMain}
                                <div class='blk_info dsfasdf'>
                                    <div class='blk_first'>
                                        " . ($this->subname ? "<div class='blk_subname'>{$this->subname}</div>" : "") . "
                                        <div class='blk_name'>" . $this->link . "</div>
                                        " . ($setting['itemlistart'] && $this->artHtml ? "<div class='blk_art'>{$this->artHtml}</div>" : "") . "
                                        " . ($setting['itemlistart'] && $this->art2Html ? "<div class='blk_art blk_art2'>{$this->art2Html}</div>" : "") . "

                                        " . ($setting['itemliststock'] || $setting['stockValShow'] ? "<!--noindex--><div class='blk_stock'>{$this->stockHtml}</div><!--/noindex-->" : "") . "
                                        " . ($setting['itemlistedism'] && $this->edizm ? "<div class='blk_edizm'>Ед.изм: <span>{$this->edizm}</span></div>" : "") . "
                                        " . ($this->variantsHtml ? "<div class='blk_variable'>{$this->variantsHtml}</div>" : "") . "
                                        " . ($this->variantsNameHtml ? "<div class='blk_variableName'>{$this->variantsNameHtml}</div>" : "") . "
                                        " . ($this->colorsHtml ? "<div class='blk_color'>{$this->colorsHtml}</div>" : "") . "
                                        " . ($this->descr ? "<div class='blk_text'>{$this->descr}</div>" : "") . "
                                        <div class='blk_priceblock " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                                                <div class='blk_price normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>"
                        . ($this->lastPrice ? "<div class='blk_last last_price'>{$this->lastPriceHtml}</div>" : "") . "
                                            </div>
                                        " . $this->regPrice() . "
                                        " . $this->getNoPriceText() . "
                                        " . ($setting['ves'] && $this->ves ? "<div class='blk_ves'>{$this->ves}</div>" : "") . "
                                        {$this->parametrsPreview}
                                    </div>
                                    <div class='blk_second " . ($this->discontCount ? "blk_actionmain" : "") . "'>{$this->discontCount}</div>
                                    <div class='blk_third'>
                                        " . ($this->getPriceLinkSupplier ?: null) . "
                                        " . ($this->btn('type2') && !$this->getPriceLinkSupplier ? "<div class='block_incard'>{$this->btn('type2')}</div>" : "") . "
                                    </div>
                                </div>
                                {$this->edit}
                            </div>";
                    break;
            }
        }
        return $html;
    }

    # Товары списком
    public function getCardList($type = 'typelist_template1')
    {
        if (function_exists('class2001_getCardList')) {
            $html = class2001_getCardList($this); // своя функция
        } else {
            global $bitcat, $setting;

            # класс шаблона
            $param = explode("_", $type);
            $type = $param[0];
            $template = $param[1];

            # btn
            $btn = $setting['templateItem'] == "type2" ? "type2" : "";

            # html
            switch ($type) {
                case 'typelist': // Стандартные карточки
                    $html = "<div class='{$this->attr('item', array($type, $template))}' {$this->attr('data')}>
                                <div class='blklist_main'>
                                    <div class='blklist_photo'>
                                        " . ($this->discontText || $this->labelHtml ? "<div class='blk_status'>{$this->discontText}{$this->labelHtml}</div>" : "") . "
                                        {$this->photoMain}
                                    </div>
                                    <div class='blklist_info'>
                                        <div class='blk_listfirst'>
                                            <div class='blk_name'>" . $this->link . "</div>
                                            " . ($setting['itemlistart'] && $this->artHtml ? "<div class='blk_art'>{$this->artHtml}</div>" : "") . "
                                            " . ($setting['itemlistart'] && $this->art2Html ? "<div class='blk_art blk_art2'>{$this->art2Html}</div>" : "") . "
                                            " . ($this->discontCount ? "<div class='blk_action_card'>" . $this->discontCount . "</div>" : "") . "
                                            " . ($this->variantsHtml ? "<div class='blk_variable'>{$this->variantsHtml}</div>" : "") . "
                                            " . ($this->colorsHtml ? "<div class='blk_color'>{$this->colorsHtml}</div>" : "") . "
                                            " . ($this->descr ? "<div class='blk_text'>{$this->descr}</div>" : "") . "
                                        </div>
                                    </div>
                                    <div class='blklist_price'>
                                        <div class='blk_priceblock " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                                            <div class='blk_price normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>"
                        . ($this->lastPrice ? "<div class='blk_last last_price'>{$this->lastPriceHtml}</div>" : "") . "
                                        </div>
                                        " . ($this->variantsNameHtml ? "<div class='blk_variableName'>{$this->variantsNameHtml}</div>" : "") . "
                                        " . (($setting['itemliststock'] || $setting['stockValShow']) && !$this->getPriceLinkSupplier ? "
											<!--noindex--><div class='blk_stock'>{$this->stockHtml}</div><!--/noindex-->
										" : "") . "
                                        <div class='flags relative'>
                                            {$this->comparisonHtml}
                                            {$this->favoritHtml}
                                        </div>
                                        " . ($this->getPriceLinkSupplier ?: '') . "
                                        " . ($this->btn($btn) && !$this->getPriceLinkSupplier ? "<div class='block_incard'>{$this->btn($btn)}</div>" : "") . "
                                    </div>
                                </div>
                                {$this->edit}
                            </div>";
                    break;
            }
        }
        return $html;
    }

    # Таблица объекты
    public function getTableItem()
    {
        if (function_exists('class2001_getTableItem')) {
            $html = class2001_getTableItem($this); // своя функция
        } else {
            global $bitcat, $setting, $current_sub;

            $td = "";
            $fileds = ($current_sub['catfields'] ? $current_sub['catfields'] : ($setting['fieldInTable'] ? $setting['fieldInTable'] : "art"));
            $names = $this->setParams("table");
            foreach (explode(",", $fileds) as $c) {
                unset($data);

                if ($c == "stock") {
                    if ($setting['itemliststock'] || $setting['stockValShow']) {
                        $data = $this->stockHtml;
                    } else {
                        continue;
                    }
                }

                if (!$data) {
                    $data = $this->f[$c] ? $this->f[$c] : "—";
                }
                $label = $names[$c] ?: "—";
                $td .= "<td class='s td_{$c} nomob' data-label='{$label}'>{$data}</td>";
            }
            if ($this->comparisonHtml || $this->favoritHtml) {
                $tdFlags = "<td>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                            </td>";
            }
            $html = "<tr class='{$this->attr('item-table')}' {$this->attr('data')}>
                    <td class='td_photo'>{$this->photoMain}</td>
                    <td class='td_name' data-label='{$names['name']}'>{$this->link} {$this->edit}</td>
                    {$td}
                    <td class='td_price " . ($this->price || $this->dogovor ? null : "s") . "' nowrap data-label='{$names['price']}'>
                    " . (($this->price || $this->dogovor) && !$this->isOutdatedSupplier ?
                "<div class='blk_priceblock " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                            " . ($this->lastPrice ? "<div class='last_price'><s>{$this->lastPriceHtml}</s></div>" : "") .
                "<div class='normal_price " . ($this->lastPrice ? "new_price" : "") . "'><b>{$this->pricePrefix} {$this->priceHtml}</b></div>
                        </div>"
                : ($this->getNoPriceText() ?: "—")) . "
                    </td>
                    <td class='td_incart' nowrap>
                        " . ($this->getPriceLinkSupplier ?: '') . "
                        " . ($setting['typeOrder'] != 2 && (($this->stock > 0 && $setting['stockbuy']) || !$setting['stockbuy']) && !$this->getPriceLinkSupplier ? $this->btn() : (!$this->getPriceLinkSupplier ? "<div class='stock notstock'><span>{$this->notovar}</span></div>" : '')) . "
                    </td>
                    {$tdFlags}
                </tr>";
        }
        return $html;
    }

    # Таблица объекты из подразделов
    public function getTableItemSub()
    {
        if (function_exists('class2001_getTableItemSub')) {
            return class2001_getTableItemSub($this); // своя функция
        } else {
            global $bitcat, $setting, $sub_prev_id, $db;

            $td = '';
            $fileds = $setting['fieldInTable'] ?: "art";
            foreach (explode(",", $fileds) as $field) {
                $val = "—";
                if ($field == "stock") {
                    if ($setting['itemliststock'] || $setting['stockValShow']) {
                        $val = $this->stockHtml;
                    } else {
                        continue;
                    }
                }
                if (!empty($this->f[$field])) {
                    $val = $this->f[$field];
                }

                $td .= "<td class='s td_{$field} nomob'>{$val}</td>";
            }

            if ($this->comparisonHtml || $this->favoritHtml) {
                $tdFlags = "<div class='flags'>
                                {$this->comparisonHtml}
                                {$this->favoritHtml}
                            </div>";
            }

            $html = "<tr class='{$this->attr('item-table')}' {$this->attr('data')}>
                        <td class='td_photo'>{$this->photoMain}</td>
                        <td class='td_name'>{$this->link} {$this->edit}</td>
                        {$td}";

            if ($this->comparisonHtml) {
                $html .= "<td class='td_comparison'>{$this->comparisonHtml}</td>";
            }
            $html .= "<td class='td_price " . ($this->price || $this->dogovor ? null : "s") . "' nowrap>";
            $html .= $this->favoritHtml;
            if ($this->price || $this->dogovor) {
                $html .= "<div class='blk_priceblock " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . ">";
                if ($this->lastPrice) {
                    $html .= "<div class='last_price'><s>{$this->lastPriceHtml}</s></div>";
                }
                $html .= "<div class='normal_price " . ($this->lastPrice ? "new_price" : "") . "'>";
                $html .= "<b>{$this->pricePrefix} {$this->priceHtml}</b>";
                $html .= "</div>";
                $html .= "</div>";
            } else {
                $html .= "—";
            }
            $html .= "</td>";
            $html .= "<td class='td_incart' nowrap>";
            if ($setting['typeOrder'] != 2 && (!$setting['stockbuy'] || $this->stock > 0)) {
                $html .= ($this->getPriceLinkSupplier ?: $this->btn());
            } else {
                $html .= (!$this->getPriceLinkSupplier ? "<div class='stock notstock'><span>{$this->notovar}</span></div>" : '');
            }
            $html .= "</td>";
            $html .= $tdFlags;
            $html .= "<tr>";

            # проверка к какому разделу принадлежит
            /**
             * to-do
             * Не учитывается find в подразделах, необходимо доделать
             */
            if (!empty($this->f['Subdivision_IDS'])) {
                foreach (explode(',', $this->f['Subdivision_IDS']) as $id) {
                    if (!empty($id) && in_array($id, $this->childSub)) {
                        $subID = $id;
                        break;
                    }
                }
            } else {
                $subID = $this->Subdivision_ID;
            }
            return ['sub' => $subID, 'html' => $html];
        }
    }

    # Внутряк карточки товара
    public function getCardFull()
    {

        if (function_exists('class2001_getCardFull')) {
            $html = class2001_getCardFull($this); // своя функция
        } else {
            global $bitcat, $db, $setting, $cityname, $AUTH_USER_ID, $current_sub;
            setCanonical($_SERVER['REQUEST_URI'] === nc_message_link($this->id, 2001) ? "" : nc_message_link($this->id, 2001));
            $itemname = $this->h1 ? $this->h1 : ($current_sub['AlterTitleObj'] ? \Korzilla\Replacer::replaceText($current_sub['AlterTitleObj'], $this->SEOitemcardArr) : $this->nameFull);
            if ($current_sub['PrefixProductH1'])
                $itemname = \Korzilla\Replacer::replaceText($current_sub['PrefixProductH1']) . " {$itemname}";
            # тип
            $type = $setting['templateItemFull'] ? $setting['templateItemFull'] : 'type1';

            $this->text = \Korzilla\Replacer::replaceText($this->text);


            # Микроразметка для товара
            $desc = \Korzilla\Replacer::replaceText(
                $this->ncDescription ?: ($setting['SEODescriptionObj'] ?: $current_sub['DescriptionObj'])
            );

            (new App\modules\Korzilla\JSON_LD\JsonLdFactory())->Product()->setProduct(
                $this->__get("Subdivision_ID"),
                $this->name,
                $this->photos[0]["path"],
                $this->vendor,
                $desc ?: '',
                $this->extlink ? $this->extlink : $this->fullLink,
                $this->getPrice(),
                $this->rate,
                $this->ratecount
            );

            # html
            switch ($type) {
                case 'type1':
                    $html = "<div class='" . $this->attr('item', array('template-' . $type)) . "' " . $this->attr('data') . ">
                        {$this->editBig}
                        <div class='content_main'>
                            <div class='gallery'>
                                " . ($this->discontText || $this->labelHtml ? "<div class='blk_status_full'>{$this->discontText}{$this->labelHtml}</div>" : "") . "
                                {$this->bigphoto}
                                " . ($this->smallphoto && stristr($this->smallphoto, "data-val='1'") ? $this->smallphoto : null) . "
                            </div>
                            <div class='content_info'>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                                <div class='card_info_first'>
                                    " . (!$this->fastprew && !$this->oneitem ? "<h1 class='title'>{$itemname}</h1>" : "") . "
                                    " . ($this->art ? "<div class='art_full'>{$this->artHtml}</div>" : "") . "
                                    " . ($this->art2 ? "<div class='art2_full art_full'>{$this->art2Html}</div>" : "") . "

                                    <div class='have_item'>
                                        " . (($setting['itemliststock'] || $setting['stockValShow']) && !$this->getPriceLinkSupplier ? $this->stockHtml : "") . "
                                        " . (!$this->fastprew && !$setting['hideconsultbutton'] ?
                        "<!--noindex--><span class='ask_question'>
                                                <a class='card-question' title='{$this->ask_question}' rel=nofollow data-rel='lightcase' data-metr='mailtoplink' data-maxwidth='380' data-groupclass='feedback modal-form' href='#' data-lc-href='/feedback/add_feedback.html?isNaked=1&itemId={$this->id}'>
                                                    <span>{$this->ask_question}</span>
                                                </a>
                                            </span><!--/noindex-->"
                        : "") . "
                                    </div>
                                </div>
                                <div class='card_variables'>
                                    {$this->variantsNameHtml}
                                    {$this->variantsHtml}
                                    {$this->colorsHtml}
                                </div>
                                " . ($this->getPriceLinkSupplier ?: null) . "
                                <div class='card_buy " . ($this->discontCountFull ? " have-action " : "") . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? " none " : null) . "'>
                                    <link href='{$this->fullLink}'/>
                                    <div class='card_price_info " . (!$this->price && !$this->dogovor ? "none" : "") . "'>
                                        <div class='card_price_first'>
                                            <div class='normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>
                                        </div>
                                        " . ($this->lastPrice ?
                        "<div class='card_price_second'>
                                                <div class='last_price'>{$this->lastPriceHtml}</div>
                                                <div class='difference_price'><span>Экономия: </span>{$this->differencePriceHtml}</div>
                                            </div>"
                        : "") . "
                                    </div>
                                    " . $this->regPrice() . "
                                    " . $this->getNoPriceText() . "
                                    " . ($this->btn('typefull1') ? "<div class='card_btn'>" . $this->btn('typefull1') . "</div>" : "") . "
                                    " . ($this->oneClickHtml && !$setting['typeOrder'] && $this->cart ? "<div class='fast_buy'>" . $this->oneClickHtml . "</div>" : "") . "
                                    {$this->discontCountFull}
                                </div>
                                " . ($this->deliveryDay ? "<div class='cart-param cart-param-deliveryDay'><div class='cart-param-body'>{$this->deliveryDay}</div></div>" : "") . "

                                " . ($this->vendor ?
                        "<div class='cart-param cart-param-vendor'>
                                        <div class='cart-param-name'>{$this->vendorname}:</div>
                                        <div class='cart-param-body'>{$this->vendorLink}</div>
                                        {$this->seoVendorManufacturer}
                                    </div>"
                        : "") . "

                                " . ($this->descr ? "<div class='cart-param cart-param-descr'><div class='cart-param-body'>" . $this->descr . "</div></div>" : ($this->text ? "" : "")) . "

                                " . (!$this->fastprew ? "<!--noindex--><div class='repost'>
                                                            <div class='repost_text'>{$this->share}:</div>
                                                            <script src='//yastatic.net/es5-shims/0.0.2/es5-shims.min.js'></script>
                                                            <script src='//yastatic.net/share2/share.js'></script>
                                                            <div class='ya-share2' data-services='vkontakte,facebook,odnoklassniki,moimir,gplus,viber,whatsapp,skype,telegram'></div>
                                                        </div><!--/noindex-->"
                        : (!$setting['cartopenmodal'] ? "<div class='bottom'>
                                        <a href='{$this->fullLink}' class='mdl_podrobnee'><span>{$this->fullInfo}</span></a>
                                    </div>" : "")) . "
                            </div>
                        </div>

                        " . (!$this->fastprew ? $this->getInfoCardFull(array('nodescr' => 1)) : "") . "
                    </div>" . (!$this->fastprew ? $this->getOtherItems() : "");
                    break;
                case 'type2':
                    $html = "<div class='" . $this->attr('item', array('template-' . $type)) . "' " . $this->attr('data') . ">
                        {$this->editBig}
                        <div class='content_main'>
                            <div class='gallery'>
                                " . (!$this->fastprew && !$this->oneitem ? "<h1 class='title'>{$itemname}</h1>" : "") . "
                                " . ($this->art ? "<div class='art_full'>{$this->artHtml}</div>" : "") . "
                                " . ($this->art2 ? "<div class='art2_full art_full'>{$this->art2Html}</div>" : "") . "
                                " . ($this->discontText || $this->labelHtml ? "<div class='blk_status_full'>{$this->discontText}{$this->labelHtml}</div>" : "") . "
                                {$this->bigphoto}
                                " . ($this->smallphoto && stristr($this->smallphoto, "data-val='1'") ? $this->smallphoto : null) . "
                            </div>

                            <div class='content_info'>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                                <div class='card_info_first'>
                                    <div class='have_item'>
                                        " . (($setting['itemliststock'] || $setting['stockValShow']) && !$this->getPriceLinkSupplier ? $this->stockHtml : "") . "
                                    </div>
                                    " . ($this->getPriceLinkSupplier ?: null) . "
                                    <div class='card_price_info " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                                        <link href='{$this->fullLink}'/>
                                        <div class='card_price_first'>
                                            <div class='normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>
                                        </div>
                                        " . ($this->lastPrice ?
                        "<div class='card_price_second'>
                                                <div class='last_price'>{$this->lastPriceHtml}</div>
                                                <div class='difference_price'><span>Экономия: </span>{$this->differencePriceHtml}</div>
                                            </div>"
                        : "") . "
                                    </div>
                                    " . $this->regPrice() . "
                                    " . $this->getNoPriceText() . "
                                    <div class='card_variables'>
                                        {$this->variantsNameHtml}
                                        {$this->variantsHtml}
                                        {$this->colorsHtml}
                                    </div>
                                    <div class='card_btn'>
                                        " . $this->btn('typefull2') . "
                                        " . ($this->oneClickHtml && !$setting['typeOrder'] && $this->cart ? "<div class='fast_buy fast_buy_first'>" . $this->oneClickHtml . "</div>" : "") . "
                                        " . (!$this->fastprew && !$setting['hideconsultbutton'] ?
                        "<div class='fast_buy fast_buy_second'>
                                                <a title='{$this->ask_question}' data-rel='lightcase' data-metr='mailtoplink' data-maxwidth='380' data-groupclass='feedback modal-form' href='#' data-lc-href='/feedback/add_feedback.html?isNaked=1&itemId={$this->id}'>
                                                    <span class='a_fast'>{$this->ask_question}</span>
                                                </a>
                                            </div>"
                        : "") . "
                                        {$this->discontCountFull}
                                    </div>
                                    " . ($this->deliveryDay ? "<div class='cart-param cart-param-deliveryDay'><div class='cart-param-body'>{$this->deliveryDay}</div></div>" : "") . "

                                    " . ($this->vendor ?
                        "<div class='cart-param cart-param-vendor'>
                                            <div class='cart-param-name'>{$this->vendorname}:</div>
                                            <div class='cart-param-body'>{$this->vendorLink}</div>
                                        {$this->seoVendorManufacturer}
                                        </div>"
                        : "") . "

                                    " . ($this->descr ? "<div class='cart-param cart-param-descr cart-param-text'><div class='cart-param-body'>" . $this->descr . "</div></div>" : ($this->text ? "" : "")) . "

                                    " . (!$this->fastprew ? "<div class='repost'>
                                                                <div class='repost_text'>{$this->share}:</div>
                                                                <script src='//yastatic.net/es5-shims/0.0.2/es5-shims.min.js'></script>
                                                                <script src='//yastatic.net/share2/share.js'></script>
                                                                <div class='ya-share2' data-services='vkontakte,facebook,odnoklassniki,moimir,gplus,viber,whatsapp,skype,telegram'></div>
                                                            </div>"
                        : (!$setting['cartopenmodal'] ? "<div class='bottom'>
                                            <a href='{$this->fullLink}' class='mdl_podrobnee'><span>{$this->fullInfo}</span></a>
                                        </div>" : "")) . "
                                </div>
                            </div>
                        </div>
                        " . (!$this->fastprew ? $this->getInfoCardFull(array('nodescr' => 1)) : "") . "
                    </div>" . (!$this->fastprew ? $this->getOtherItems() : "");
                    break;
                case 'type3':
                    $html = "<div class='" . $this->attr('item', array('template-' . $type)) . "' " . $this->attr('data') . ">
                        {$this->editBig}
                        <div class='content_main'>
                            <div class='gallery'>
                                {$this->bigphoto}
                                " . ($this->smallphoto && stristr($this->smallphoto, "data-val='1'") ? $this->smallphoto : null) . "
                            </div>
                            
                            <div class='content_info'>
                                <div class='flags'>
                                    {$this->comparisonHtml}
                                    {$this->favoritHtml}
                                </div>
                                <div class='card_info_first'>
                                    " . (!$this->fastprew && !$this->oneitem ? "<h1 class='title'>{$itemname}</h1>" : "") . "
                                    <div class='have_item'>
                                        " . (($setting['itemliststock'] || $setting['stockValShow']) && !$this->getPriceLinkSupplier ? $this->stockHtml : "") . "
                                    </div>
                                    " . ($this->art ? "<div class='art_full'>{$this->artHtml}</div>" : "") . "
                                    " . ($this->art2 ? "<div class='art2_full art_full'>{$this->art2Html}</div>" : "") . "
                                    " . ($this->descr ? "<div class='cart-param cart-param-text'><div class='cart-param-body'>" . $this->descr . "</div></div>" : ($this->text ? "" : "")) . "
                                    " . ($this->discontText || $this->labelHtml ? "<div class='blk_status_full'>{$this->discontText}{$this->labelHtml}</div>" : "") . "
                                    " . ($this->getPriceLinkSupplier ?: null) . "
                                    <div class='card_price_info " . ((!$this->price && !$this->dogovor) || $this->isOutdatedSupplier ? "none" : "") . "'>
                                    <link href='{$this->fullLink}'/>
                                        <div class='card_price_first'>
                                            <div class='normal_price " . ($this->lastPrice ? "new_price" : "") . "'>{$this->pricePrefix} {$this->priceHtml}</div>
                                        </div>
                                        " . ($this->lastPrice ?
                        "<div class='card_price_second'>
                                                <div class='last_price'>{$this->lastPriceHtml}</div>
                                                <div class='difference_price'><span>Экономия: </span>{$this->differencePriceHtml}</div>
                                            </div>"
                        : "") . "
                                    </div>
                                    " . $this->regPrice() . "
                                    " . $this->getNoPriceText() . "
                                    <div class='card_variables'>
                                        {$this->variantsNameHtml}
                                        {$this->variantsHtml}
                                        {$this->colorsHtml}
                                    </div>
                                    <div class='card_buy " . ($this->discontCountFull ? "have-action" : "") . "'>
                                        " . ($this->btn('typefull1') ? "<div class='card_btn'>" . $this->btn('typefull1') . "</div>" : "") . "
                                        " . ($this->oneClickHtml && !$setting['typeOrder'] && $this->cart ? "<div class='fast_buy'>" . $this->oneClickHtml . "</div>" : "") . "
                                        {$this->discontCountFull}
                                    </div>
                                    " . ($this->vendor ?
                        "<div class='cart-param'>
                                            <div class='cart-param-name'>{$this->vendorname}:</div>
                                            <div class='cart-param-body'>{$this->vendorLink}</div>
                                        {$this->seoVendorManufacturer}
                                        </div>"
                        : "") . "

                                    " . (!$this->fastprew ? "<div class='repost'>
                                                                <div class='repost_text'>{$this->share}:</div>
                                                                <script src='//yastatic.net/es5-shims/0.0.2/es5-shims.min.js'></script>
                                                                <script src='//yastatic.net/share2/share.js'></script>
                                                                <div class='ya-share2' data-services='vkontakte,facebook,odnoklassniki,moimir,gplus,viber,whatsapp,skype,telegram'></div>
                                                            </div>"
                        : (!$setting['cartopenmodal'] ? "<div class='bottom'>
                                            <a href='{$this->fullLink}' class='mdl_podrobnee'><span>{$this->fullInfo}</span></a>
                                        </div>" : "")) . "
                                </div>
                                " . (!$this->fastprew && !$setting['hideconsultbutton'] ?
                        "<div class='card_info_second'>
                                        <span class='ask_question'>
                                            <a class='card-question' title='{$this->ask_question}' data-rel='lightcase' data-maxwidth='380' data-groupclass='feedback modal-form' data-metr='mailtoplink' href='#' data-lc-href='/feedback/add_feedback.html?isNaked=1&itemId={$this->id}'>
                                                <span>{$this->ask_question}</span>
                                            </a>
                                        </span>
                                    </div>"
                        : "") . "
                            </div>
                        </div>
                        " . (!$this->fastprew ? $this->getInfoCardFull(array('nodescr' => 1)) : "") . "
                    </div>
                    " . (!$this->fastprew ? $this->getOtherItems() : "");
                    break;
            }
        }
        return $html;
    }

    # data атрибуты
    public function getInfoCardFull($param)
    {

        if (function_exists('class2001_getInfoCardFull')) {
            $html = class2001_getInfoCardFull($this, $param); // своя функция
        } else {
            global $cityname, $setting, $setting_texts, $AUTH_USER_ID, $db, $pathInc;

            $tabs = $tabsBody = "";

            $this->setCartText($param);


            $tabSetting = new App\modules\Korzilla\CatalogItem\Tab\Models\ModelSetting($_SERVER['DOCUMENT_ROOT'] . $pathInc);

            $tabs = $tabsBody = "";
            $allTabs = [];
            foreach ($tabSetting->getData() as $id => $value) {
                if (!$value['Checked'])
                    continue;

                if ((int) $value['params']['contenttype'] == 0) {
                    $allTabs[$id]["title"] = $value['name'];
                    $allTabs[$id]["body"] = "<div class='txt'>{$value['params']['f_text']}</div>";
                    continue;
                }

                if ((int) $value['params']['contenttype'] == 1) {
                    $allTabs[$id]["title"] = $value['name'];
                    list($sub, $cc) = explode('|', $value['params']['f_sub']);

                    $allTabs[$id]["body"] = nc_objects_list($sub, $cc, "&ssub={$sub}&tsub={$sub}&tcc={$cc}&msg={$value['params']['f_msg']}&isTitle=1&recNum={$value['params']['f_recnum']}&rand={$value['params']['f_rand']}&name={$value['name']}&substr={$value['params']['f_substr']}", 1, false);
                    continue;
                }

                if ((int) $value['params']['contenttype'] == 2) {
                    switch ($value['params']['default_tab_type']) {
                        # описание и параметры
                        case 'cart-param':
                            if ($this->tagsHtml || $this->firstText) {
                                $allTabs["cart-param"]["title"] = $value['name'] ?: $this->vkldk_opisitem;
                                $allTabs["cart-param"]["body"] = $this->tagsHtml . $this->firstText;
                            }
                            break;
                        # описание 2
                        case 'cart-param-2':
                            if ($this->text2 || $this->parameters) {
                                $allTabs["cart-param-2"]["title"] = $value['name'] ?: $this->vkldk_opisitem2;
                                $allTabs["cart-param-2"]["body"] = "<div class=txt>" . $this->text2 . $this->parameters . "</div>";
                            }
                            break;
                        # отзывы
                        case 'cart-review':
                            $comment = getComments(2054, $this->id);
                            $commentCount = substr_count($comment, 'kz_otz_item');
                            $allTabs["cart-review"]["title"] = ($value['name'] ?: $this->reviews) . ($commentCount > 0 ? "<span class='review-count'>{$commentCount}</span>" : "");
                            $allTabs["cart-review"]["body"] = $comment;
                            break;
                        # Деловые линии
                        case 'cart-citymap':
                            if (stristr($this->deliveryMap, "this_map")) {
                                $allTabs["cart-citymap"]["title"] = getLangWord('full_delline_title', 'Получение заказа в г.') . " <b>" . getLangCityName($cityname) . "</b>";
                                $allTabs["cart-citymap"]["body"] = $this->deliveryMap;
                            } elseif ($setting['targeting'] && $setting['devlintarget']) {
                                $allTabs["select-city"]["title"] = getCityLink(array("title" => "<span>" . getLangWord('full_delline_title', 'Получение заказа:') . "</span> <b>" . getLangWord('full_delline_select_city', 'выберите ваш город') . "</b>"));
                            }
                            break;
                        case 'cart-var1':
                            if ($this->var1) {
                                $allTabs["cart-var1"]["title"] = $value['name'];
                                $allTabs["cart-var1"]["body"] = htmlspecialchars_decode($this->var1);
                            }
                            break;
                    }

                    continue;
                }
            }


            if ($this->analogs_new) {
                $allTabs["analogs-new"]["title"] = "Навесное оборудование";

                $html = "";
                $analogs_decoded = json_decode(htmlspecialchars_decode($this->analogs_new));
                $analogs_ids = implode(",", $analogs_decoded->products);
                $dataObj = "" . nc_objects_list($this->sub, $this->cc, "&isTitle=1&nc_ctpl=2001&scrolling=0&scrollNav=0&recNum=200&notobj=" . $this->id . "&getanalognew=" . $analogs_ids, true, false);

                if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table")) {
                    $analogHtml = $dataObj;
                }

                if ($analogHtml) {
                    $result = "<section class='blocks block_analogi'>
                                                <article class='cb blk_body nopadingLR'>
                                                    <div class='blk_body_wrap'>{$analogHtml}</div>
                                                </article>
                                            </section>";
                }

                $allTabs["analogs-new"]["body"] = $result;
            }

            # перебор
            foreach ($allTabs as $key => $itemTab) {
                if ($key == "select-city") {
                    $tabs .= "<li class='tab'>{$itemTab[title]}</li>";
                } else {
                    $tabs .= "<li class='tab'><a href='#{$key}'>{$itemTab[title]}</a></li>";
                    $tabsBody .= "<div id='{$key}'>{$itemTab[body]}</div>";
                }
            }
            # Вкладки
            if (count($allTabs) > 1) {
                $html = "<div id='cart-info' class='cart-info-type1'>
                            <ul class='tabs tabs-border'>{$tabs}</ul>
                            <div class='tabs-body'>{$tabsBody}</div>
                        </div>";
            } elseif ($tabsBody) {
                $html = "<div id='cart-info' class='cart-info-onetab'>{$tabsBody}</div>";
            }
            # Дополнительный текст
            $html .= ($this->secondText ? "<div id='cart-info-mini'>" . $this->secondText . "</div>" : null);
        }
        return $html;
    }

    public function getParamsArray($all = false, $type = 'full')
    {
        global $setting, $setting_params, $AUTH_USER_ID;
        $itemsParams = [];
        $this->params = trim($this->params);
        $chekedKey = ($type == 'full' ? 'checked' : 'preview');

        if ($this->params && is_array($setting_params) && !empty($setting_params)) {
            $paramsSettingChecked = array_reduce($setting_params, function ($paramsSettingChecked, $param) use ($all, $chekedKey) {
                if ($param[$chekedKey] || $all) {
                    $paramsSettingChecked[$param['keyword']] = $param;
                }
                return $paramsSettingChecked;
            }, []);


            $paramValues = (stristr($this->params, "\r\n") !== false ? explode("\r\n", $this->params) : explode("| ", $this->params));

            $itemsParams = array_reduce($paramValues, function ($itemsParams, $param) use (&$paramsSettingChecked) {
                $paramKeyVal = explode('||', trim($param, '|'));

                if ($paramKeyVal[1] != '' && !empty($paramsSettingChecked[$paramKeyVal[0]])) {

                    $itemsParams[$paramKeyVal[0]] = [
                        'name' => $paramsSettingChecked[$paramKeyVal[0]]['name'],
                        'value' => $paramKeyVal[1]
                    ];
                }

                return $itemsParams;
            }, []);
        }
        return $itemsParams;
    }

    # доп параметры товара
    public function getParams($oneParamValue = '')
    {
        $itemParams = $this->getParamsArray();

        if ($oneParamValue) {
            return (isset($itemParams[$oneParamValue]) ? $itemParams[$oneParamValue] : '');
        }

        $params = [];
        foreach ($itemParams as $itemParam) {
            $params[] = "<tr>
                            <th>{$itemParam['name']}</th>
                            <td>{$itemParam['value']}</td>
                        </tr>";
        }
        return "<table>" . implode("", $params) . "</table>";
    }

    # data атрибуты
    public function attr($name, $p = array())
    {
        if (function_exists('class2001_attr')) {
            return class2001_attr($this, $name, $p); # своя функция
        } else {
            global $setting, $cityid;
            switch ($name) {
                case 'data':
                    $attr[] = "data-id='{$this->id}'";
                    $attr[] = "data-origname='" . addslashes($this->name) . "'";
                    $attr[] = "data-name='" . addslashes($this->name) . "'";
                    $attr[] = "data-sub='{$this->sub}'";
                    if ($this->lastPrice) {
                        $attr[] = "data-oldprice='{$this->lastPrice}'";
                    }
                    $attr[] = "data-origprice='{$this->price}'";
                    $attr[] = "data-price='{$this->price}'";
                    $attr[] = "data-count='{$this->count}'";
                    $attr[] = "data-origstock='{$this->stock}'";
                    $attr[] = "data-stock='{$this->stock}'";
                    $attr[] = "data-hex='" . hexprice($this->price) . "'";
                    $attr[] = "data-orighex='" . hexprice($this->price) . "'";
                    $attr[] = "data-ves='{$this->ves}'";
                    $attr[] = $this->variablename ? "data-variant='{$this->variablename}'" : "";
                    if ($this->animateKey) {
                        $attr[] = str_replace(",", ".", "data-wow-delay='" . ($this->animateDelay + ($this->RowNum * $this->animateDelayStep)) . "s'");
                    }
                    break;

                case 'item':
                    if ($this->animateKey) {
                        $attr[] = "wow {$this->animateKey}";
                    }
                case 'item-table':
                    if ($this->full) {
                        $attr[] = "itemcard";
                        $attr[] = "catalog-item-full";
                        if ($this->fastprew) {
                            $attr[] = "item-full-fastprew";
                        }
                    } else {
                        if ($name == "item") {
                            $attr[] = "catalog-item";
                        }
                        if ($name == "item-table") {
                            $attr[] = "table-item";
                        }
                        $attr[] = "obj obj{$this->id} " . objHidden($this->Checked, $this->citytarget, $cityid);
                        $attr[] = ($this->spec ? "spec-item" : null);
                        $attr[] = (!$this->cart ? "nocart" : null);
                    }
                    $attr[] = "item-obj";
                    if ($setting['stockbuy']) {
                        $attr[] = "stockbuy";
                    }
                    break;

                case 'card':
                    $attr[] = ($this->cart ? "incart-js" : "inorder-js"); # в корзину или оформить заказ
                    if ($this->buyvariable) {
                        $attr[] = "variable-required"; # обязательный выбор варианта
                    }
                    if ($this->buycolors) {
                        $attr[] = "colors-required"; # обязательный выбор цвета
                    }
                    // $attr[] = (!$this->cart && ($this->buycolors || $this->buyvariable) ? "none" : ""); # кнопка скрыта пока не выбран вариант (оформить заказ)
                    break;
            }

            if ($p) {
                foreach ($p as $value) {
                    $attr[] = $value;
                }
            }

            return implode(" ", $attr);
        }
    }

    # Кнопка в корзину
    public function btn($type = '')
    {
        if (function_exists('class2001_btn')) {
            return class2001_btn($this, $type);
        } else {
            global $setting, $AUTH_USER_ID;
            # нужно ли кнопка
            $permission = $this->cart || $this->order;
            $haveItem = $this->stock > 0 || $this->totalStock > 0;
            if ($this->isOutdatedSupplier && $this->full) {
                return "<div class='cart-btn'>
                    <a href='/search/?find={$this->art}'>
                        <span>Узнать цену</span>
                    </a>
                </div>";
            }


            if (!$permission || $setting['stockbuy'] && !$haveItem) {
                return;
            }
            # текст в кнопке
            $btn_text = $this->cart ? ($this->incart ? $this->incardtextt : $this->incardtext) : $this->inordertext;

            if (!$this->cart) { # оформить заказ
                $html = "<div class='cart-btn  " . ($this->full ? 'inorder-typefull1' : 'inorder-type1') . "'>
                    <a href='javascript:void(0);' title='{$btn_text}' data-title='{$btn_text}' class='" . $this->attr('card') . " buy_one_click mainmenubg icons i_cart'>
                        <span>{$btn_text}</span>
                    </a>
                </div>";
            } else { # в корзину
                if ($this->full) { # кнопки внутряков
                    switch ($type) {
                        case 'typefull1':
                            $html = "<div class='cart-btn incart-{$type} " . ($this->incart ? "active" : null) . "'>
                                            <div class='incart-num'>
                                                <input name='count' value='{$this->count}' type='number'>
                                                <span class='icons i_plus incart_up'></span>
                                                <span class='icons i_minus incart_down'></span>
                                            </div>
                                            <a href='{$this->fullLink}' title='{$btn_text}' data-title='{$btn_text}' class='" . $this->attr('card') . " mainmenubg icons i_cart' data-metr='addincart'>
                                                <span>{$btn_text}</span>
                                            </a>
                                        </div>";
                            break;
                        case 'typefull2':
                            $html = "<div class='cart-btn incart-{$type} " . ($this->incart ? "active" : null) . "'>
                                            <div class='cart-line line-count'>
                                                <div class='cart-line-title'>{$this->numQuant}:</div>
                                                <div class='cart-line-body'>
                                                    <div class='cart-line-count'>
                                                        <input name='count' value='{$this->count}' type='number'>
                                                        <span class='icons i_plus incart_up'></span>
                                                        <span class='icons i_minus incart_down'></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href='{$this->fullLink}' title='{$btn_text}' data-title='{$btn_text}' class='" . $this->attr('card') . " mainmenubg icons i_cart' data-metr='addincart'>
                                                <span>{$btn_text}</span>
                                            </a>
                                        </div>";
                            break;
                    }
                } else { # кнопки товаров в списке
                    # тип кнопки
                    if (!$type) {
                        $type = "type1";
                    }

                    switch ($type) {
                        case 'type1':
                            $html = "<div class='cart-btn incart-{$type} mainmenubg " . ($this->incart ? "active" : null) . "'>
                                            <a href='{$this->fullLink}' title='{$btn_text}' class='" . $this->attr('card') . " icons i_cart' data-metr='addincart'>
                                                <span>{$btn_text}</span>
                                            </a>
                                            <div class='incart-num'>
                                                <input name='count' value='{$this->count}' type='number'>
                                                <span class='icons i_plus incart_up'></span>
                                                <span class='icons i_minus incart_down'></span>
                                            </div>
                                        </div>";
                        case 'type2':
                            $html = "<div class='cart-btn incart-{$type} mainmenubg " . ($this->incart ? "active" : null) . "'>
                                            <div class='incart-num'>
                                                <input name='count' value='{$this->count}' type='number'>
                                                <span class='icons i_plus incart_up'></span>
                                                <span class='icons i_minus incart_down'></span>
                                            </div>
                                            <a href='{$this->fullLink}' title='{$btn_text}' class='" . $this->attr('card') . " icons i_cart' data-metr='addincart'>
                                                <span>{$btn_text}</span>
                                            </a>
                                        </div>";
                            break;
                    }
                }
            }

            return $html;
        }
        /*if ($setting['showListItemCount']) { // поле количества
            $result .= "<div class='blk_i_num '><input type='number' name='count' value='1'><span class='blk_i_up'></span><span class='blk_i_down'></span></div>";
        } else { // нет поле количества
            $result .= "<div class='blk_incard_num mainmenubg".($this->incart ? " block" : NULL)."'>
                            <input name='count' value='{$this->count}' type='number'>
                            <span class='icons i_plus blk_incard_up'></span>
                            <span class='icons i_minus blk_incard_down'></span>
                        </div>";
        }

        if (!$setting['showListItemCount'] && $setting[typeCartBut]==1) {
            $result .= "<a href='{$this->fullLink}' title='купить' class='".$this->attr('card')." blk_incard mainmenubg ".($incart && !$setting['showListItemCount'] ? "none" : NULL)." icons i_cart1'>
                            <span>".(!$this->nocart && $setting[typeOrder]<1 ? $this->incardtext : $this->inordertext)."</span>
                        </a>";
        } else {
            $result .= "<a href='{$this->fullLink}' class='".$this->attr('card')." blk_i_btn mainmenubg'><span></span></a>";
        }*/
    }

    # Аналоги и Рекоменуемые товары
    public function getOtherItems()
    {
        if (function_exists('class2001_getOtherItems')) {
            $result = class2001_getOtherItems($this); // своя функция
        } else {
            global $setting, $db, $catalogue, $AUTH_USER_ID, $cityid;

            $result = "";

            # portfolio
            $outItems = orderArray($this->outItems);
            if ($outItems[portfolio]) {
                $subInfo = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class WHERE Class_ID = 2021 AND Catalogue_ID = {$catalogue} LIMIT 1", ARRAY_A);
                if ($subInfo[sub]) {
                    $portfolioHtml = nc_objects_list($subInfo[sub], $subInfo[cc], "&isTitle=1&scrolling=1&scrollNav=1&recNum=200&notobj={$this->id}&getportfolio=" . implode(",", $outItems[portfolio]), true, false);
                }
            }
            if ($portfolioHtml) {
                $result .= "<section class='blocks block_portfolio'>
                                            <header class='blk_head nopadingLR'>
                                                <div class='h2'>{$this->portfolio_text}</div>
                                            </header>
                                            <article class='cb blk_body nopadingLR'>
                                                <div class='blk_body_wrap'>{$portfolioHtml}</div>
                                            </article>
                                        </section>";
            }

            # documents
            $outItems = orderArray($this->outItems);
            if ($outItems['documents']) {
                $subInfo = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class WHERE Class_ID = 2009 AND Catalogue_ID = {$catalogue} LIMIT 1", ARRAY_A);
                if ($subInfo[sub]) {
                    $documentHtml = nc_objects_list($subInfo['sub'], $subInfo['cc'], "&isTitle=1&scrolling=1&scrollNav=1&recNum=200&notobj={$this->id}&getdocuments=" . implode(",", $outItems['documents']), true, false);
                }
            }
            if ($documentHtml) {
                $result .= "<section class='blocks block_portfolio'>
                                            <header class='blk_head nopadingLR'>
                                                <div class='h2'>{$this->document_text}</div>
                                            </header>
                                            <article class='cb blk_body nopadingLR'>
                                                <div class='blk_body_wrap'>{$documentHtml}</div>
                                            </article>
                                        </section>";
            }

            # analog
            if (trim($this->analog)) {
                # если в артикуле запятая, сначала меняем ее на ;3:'-; (ужас) и в Settings исправляем на запятую
                # чтобы артикул полностью сохранился
                $this->analog = str_replace(",", ";-:'-;", $this->analog);

                $this->analog = str_replace("\r\n", ",", $this->analog);
                $this->analog = str_replace("\n", ",", $this->analog);
                $this->analog = str_replace("\r", ",", $this->analog);

                $dataObj = "" . nc_objects_list($this->sub, $this->cc, "&isTitle=1&nc_ctpl=2001&scrolling=1&scrollNav=1&recNum=200&notobj=" . $this->id . "&getanalog=" . $this->analog, true, false);
                if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table")) {
                    $analogHtml = $dataObj;
                }
            }

            if ($analogHtml) {
                $result .= "<section class='blocks block_analogi'>
                                            <header class='blk_head nopadingLR'>
                                                <div class='h2' data-keyword='full_analogtext'>{$this->analog_text}</div>
                                            </header>
                                            <article class='cb blk_body nopadingLR'>
                                                <div class='blk_body_wrap'>{$analogHtml}</div>
                                            </article>
                                        </section>";
            }

            //Рекомендуем посмотреть
            if ($setting['powerseo_super'] || $setting['analogFormName']) {
                $replaceParam = [];
                if ($this->art)
                    $replaceParam[] = $this->art;
                if ($this->vendor)
                    $replaceParam[] = $this->vendor;
                $clearName = array_reduce(explode(' ', str_replace($replaceParam, '', $this->name)), function ($a, $item) {
                    $item = preg_replace('/[^a-zA-Zа-яА-Я]/ui', '_', $item);
                    if (mb_strlen(trim(str_replace('_', '', $item))) > 3 && count($a) < 3) {
                        $a[] = trim($item, '_');
                    }
                    return $a;
                }, []);
                $dataAn = nc_objects_list($this->sub, $this->cc, "&isTitle=1&nc_ctpl=2001&scrolling=1&scrollNav=1&recNum=8&notobj=" . $this->id . "&getanalogname=" . implode(' ', $clearName));

                if ($clearName) {
                    $dataObj = "" . $dataAn;
                    if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table"))
                        $analogNameHtml = $dataObj;
                }


                if ($analogNameHtml) {
                    $result .= "<section class='blocks start end this block-default analog_for_name'>
                                                <header class='blk_head nopadingLR'>
                                                    <div class='h2'>" . getLangWord('recommended_watch', 'Рекомендуем посмотреть') . "</div>
                                                </header>
                                                <article class='cb blk_body nopadingLR'>
                                                    <div class='blk_body_wrap'>{$analogNameHtml}</div>
                                                </article>
                                            </section>";
                }
            }

            if (!empty($setting['seo_unique_item_list'])) {
                $uniqueItemsTml = 2261;

                $sql = "SELECT Subdivision.`Subdivision_ID` AS sub, Sub_Class.`Sub_Class_ID` AS cc
                        FROM `Subdivision` INNER JOIN `Sub_Class` ON Subdivision.`Subdivision_ID` = Sub_Class.`Subdivision_ID`
                        WHERE Subdivision.`Catalogue_ID` = {$catalogue} 
                            AND Sub_Class.`Class_ID` = 2001 
                            AND Sub_Class.`Class_Template_ID` = {$uniqueItemsTml}
                        LIMIT 0,1";

                if ($uniqueItemsSub = $db->get_row($sql, ARRAY_A)) {
                    $result .= nc_objects_list($uniqueItemsSub['sub'], $uniqueItemsSub['cc'], "&id={$this->id}&city_id={$cityid}&similar_percent=50", false, false);
                }
            }


            # buy with
            if (trim($this->buywith)) {
                $this->buywith = str_replace(["\r\n", "\n", "\r"], ",", $this->buywith);
                $dataObj = "" . nc_objects_list($this->sub, $this->cc, "&isTitle=1&nc_ctpl=2001&scrolling=1&scrollNav=1&notobj=" . $this->id . "&getanalog=" . $this->buywith, true, false);
                if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table")) {
                    $buywithHtml = $dataObj;
                }
            }

            # random other item
            if (!$analogHtml && !$buywithHtml && !strstr($this->otherItem, "none")) {
                //if ($setting['showAdditionalItems']) {
                //$delaultItems = 1;
                //$catalogParentSub = $db->get_row("
                //SELECT Subdivision.Subdivision_ID, Sub_Class.Sub_Class_ID
                //FROM Subdivision
                //JOIN Sub_Class ON Subdivision.Subdivision_ID = Sub_Class.Subdivision_ID
                //WHERE Subdivision.EnglishName = 'catalog' AND Subdivision.Catalogue_ID = $catalogue
                //");
                //if ($catalogParentSub->Subdivision_ID && $catalogParentSub->Sub_Class_ID) {
                //$dataObj = nc_objects_list(
                //$catalogParentSub->Subdivision_ID,
                //$catalogParentSub->Sub_Class_ID,
                //"&isTitle=1&nc_ctpl=2001&recNum=15&scrolling=1&randomSort=1&outallitem=1&scrollNav=1&showMoreProducts=1"
                //);
                //if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table")) {
                //$buywithHtml = $dataObj;
                //}
                //}
                //} else {
                $delaultItems = 1;
                $dataObj = nc_objects_list($this->sub, $this->cc, "&isTitle=1&rand=1&recNum=5&scrolling=1&getOther=1&scrollNav=1&desc=" . $this->desc . "&notobj=" . $this->id . "&find=" . $this->current_sub['find'] . "&otherItem=" . $this->otherItem, true, false);
                if (strstr($dataObj, "catalog-item") || strstr($dataObj, "catalog-table")) {
                    $buywithHtml = $dataObj;
                }
                //}
            }

            if ($buywithHtml) {
                $result .= "<section class='blocks  start 11 end this " . ($delaultItems ? "block-default" : "") . " block_buywith'>
                                            <header class='blk_head nopadingLR'>
                                                <div class='h2' data-keyword='full_ponravtext'>{$this->ponrav_text}</div>
                                            </header>
                                            <article class='cb blk_body nopadingLR'>
                                                <div class='blk_body_wrap'>{$buywithHtml}</div>
                                            </article>
                                        </section>";
            }
        }
        return $result;
    }

    # Описание (текст)
    public function setCartText($param)
    {
        if (function_exists('class2001_setCartText')) {
            return class2001_setCartText($this, $param);
        } else {
            global $setting, $cityname, $catalogue, $db, $current_sub, $login;
            if ($this->full) {
                # SEO text
                $seoText = $this->current_sub['seotext'] ?: $setting['SEOitemcard'];
                if ($seoText != 'нет') {
                    $seoText = getLangWord($seoText);
                    if (!empty($seoText)) {
                        $SEOitemcard = BBcode(strtr(\Korzilla\Replacer::replaceText($seoText), $this->SEOitemcardArr));
                    } elseif (!$this->noorder) {
                        $SEOitemcard = "" . ($this->cart ?
                            getLangWord("Купить")
                            : getLangWord("Оформить заказ на")
                        )
                            . " {$this->name} " .
                            getLangWord("вы можете в компании")
                            . " {$this->current_catalogue['Catalogue_Name']}" .
                            ($this->cart ?
                                getLangWord(", оформив заказ в интернет магазине, или") : "")
                            . " <a class='buy_one_click dotted' href='javascript:void(0);' data-title='{$this->buyoneclick_text}' 
                            title='$this->buyoneclick_text'>
                                " . getLangWord("отправив заявку") . "</a> 
                                " . getLangWord("по почте, а также по телефону") . "
                                <span class='text-offis'> 
                                " . getLangWord("или в") . " 
                                <a href='/contacts/' target='_blank'>
                                " . getLangWord("офисе компании") . "
                                </a></span>.";
                    }
                }
                unset($seoText);

                # text
                $mainText = ($this->current_sub['txttoall'] ? $this->current_sub['text'] : null) . $this->text;
                if (!$mainText && !$param['nodescr']) {
                    $mainText = $this->descr;
                }



                if (trim($mainText)) {
                    $this->firstText = "<div class='txt'>" . textTargeting($mainText) . "</div>";
                    if ($login['login'] != 'zl') {
                        $this->secondText = $SEOitemcard;
                    }
                    if (strlen($mainText) > 50 && $this->secondText) {
                        $this->secondText = "<!--noindex-->" . $this->secondText . "<!--/noindex-->";
                    }
                } else {
                    $this->firstText = $SEOitemcard;
                }
            }
        }
    }

    public function getPhoto()
    {

        if (function_exists('class2001_getPhoto')) {
            return class2001_getPhoto($this);
        } else {
            global $db, $nc_core, $HTTP_FILES_PATH, $pathInc, $DOCUMENT_ROOT, $IMG_HOST, $noimage, $setting, $catalogue, $AUTH_USER_ID;

            $result = [];
            $alt = ($this->current_sub['TitleImg'] ? \Korzilla\Replacer::replaceText($this->current_sub['TitleImg']) : $this->name);

            $import = $HTTP_FILES_PATH . "import/";
            $import1C = $pathInc . "/1C/import_files/";



            // Загруженые фото
            if ($this->photo->records) {
                foreach ($this->photo->records as $photo) {
                    $result[] = [
                        'alt' => ($setting['requirePhotoAlt']?$alt:($photo['Name'] ?: $alt)),
                        'path' => $IMG_HOST . $photo['Path'],
                        'preview' => $IMG_HOST . $photo['Preview'],
                    ];
                }
            }

            // По photourl
            if ($this->photourl) {
                $delimiter = strstr($this->photourl, ";") ? ';' : ',';
                foreach (explode($delimiter, $this->photourl) as $photo) {
                    if (!strstr($photo, "://")) {
                        $photo = preg_replace_callback('#://([^/]+)/([^?]+)#', function ($match) {
                            return '://' . $match[1] . '/' . join('/', array_map('rawurlencode', explode('/', $match[2])));
                        }, trim($photo));

                        if (substr($photo, 0, 1) !== '/') {
                            $photo = $import . $photo;
                        }
                    }

                    $extensions = end(explode('.', $photo));
                    if (!in_array(mb_strtolower($extensions), ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                        $photo . '.jpg';
                    }

                    $result[] = [
                        'alt' => $alt,
                        'path' => $photo,
                        'preview' => $photo,
                    ];
                }
            }


            // По art
            if (empty($result) && $this->art) {

                if (count(scandir($DOCUMENT_ROOT . $import)) > 2) {

                    $photo = $this->getGlobPhoto($DOCUMENT_ROOT . $import . $this->art);
                    if ($photo) {
                        $result[] = [
                            'alt' => $alt,
                            'path' => $photo,
                            'preview' => $photo,
                        ];
                    }

                    for ($i = 1; $i <= 10; $i++) {
                        $photo = $this->getGlobPhoto($DOCUMENT_ROOT . $import . $this->art . "_" . $i);
                        if ($photo) {
                            $result[] = [
                                'alt' => $alt,
                                'path' => $photo,
                                'preview' => $photo,
                            ];
                        } elseif ($i > 1) {
                            break;
                        }
                    }
                }
                // 1C
                if (empty($result) && file_exists($DOCUMENT_ROOT . $import1C . normArtFile($this->art))) {
                    $photo = $import1C . normArtFile($this->art);
                    $result[] = [
                        'alt' => $alt,
                        'path' => $photo,
                        'preview' => $photo,
                    ];
                }
            }
            // по коду (art2)
            if (empty($result) && $this->art2) {

                if (count(scandir($DOCUMENT_ROOT . $import)) > 2) {
                    $photo = $this->getGlobPhoto($DOCUMENT_ROOT . $import . $this->art2);

                    if ($photo) {
                        $result[] = [
                            'alt' => $alt,
                            'path' => $photo,
                            'preview' => $photo,
                        ];
                    }

                    for ($i = 1; $i <= 10; $i++) {
                        $photo = $this->getGlobPhoto($DOCUMENT_ROOT . $import . $this->art2 . "_" . $i);
                        if ($photo) {
                            $result[] = [
                                'alt' => $alt,
                                'path' => $photo,
                                'preview' => $photo,
                            ];
                        } elseif ($i > 1) {
                            break;
                        }
                    }
                }
            }
            // По code
            if (empty($result) && $this->code && file_exists($DOCUMENT_ROOT . $import . normArtFile($this->code))) {
                $photo = $import . normArtFile($this->code);
                $result[] = [
                    'alt' => $alt,
                    'path' => $photo,
                    'preview' => $photo,
                ];
            }
            // Фото у основного товара
            if (empty($result) && $setting['groupItem']) {
                $mainItemID = $db->get_var("SELECT Message_ID FROM Message2001 WHERE variablenameSide = '0' AND `name` = '{$this->name}' AND Catalogue_ID = '{$catalogue}'");
                if ($mainItemID > 0) {
                    $photodb = $db->get_results("SELECT Path FROM Multifield WHERE Field_ID = 2353 AND Message_ID = {$mainItemID} ORDER BY `Priority`", ARRAY_A);
                    if ($photodb) {
                        foreach ($photodb as $photo) {
                            $result[] = [
                                'alt' => $alt,
                                'path' => $photo['Path'],
                                'preview' => $photo['Preview']
                            ];
                        }
                    }
                }
            }
            // есть общее изображение из раздела
            if (empty($result) && $nc_core->subdivision->get_by_id($this->sub, "imgtoall")) {
                $temporary = $nc_core->subdivision->get_by_id($this->sub, "imgBig") ?: $nc_core->subdivision->get_by_id($this->sub, "img");
                if (@file_exists($DOCUMENT_ROOT . $temporary)) {
                    $result[] = [
                        'alt' => $alt,
                        'path' => $IMG_HOST . $temporary,
                        'preview' => $IMG_HOST . $temporary
                    ];
                }
            }


            return $result;
        }
    }
    # photo
    public function setPhoto()
    {
        if (function_exists('class2001_setPhoto')) {
            $html = class2001_setPhoto($this); // своя функция
        } else {
            global $IMG_HOST, $noimage, $setting, $current_catalogue, $AUTH_USER_ID;

            $this->photos = $this->getPhoto();
            if ($this->full) {
                $bigphoto = $smallphoto = '';
                $modalViewType = $this->fastprew ? 'lightcase' : 'lightcase:image-in-cart';

                if (!empty($this->photos)) {
                    foreach ($this->photos as $index => $photo) {
                        $aRelNoFollow = "";
                        $parsedPhotoPath = parse_url($photo['path']);
                        if (!empty($parsedPhotoPath['host']) && $parsedPhotoPath['host'] != $current_catalogue['Domain']) {
                            $aRelNoFollow = "rel='nofollow'";
                        }
                        $bigphoto .= "<a {$aRelNoFollow} href='{$photo['path']}' title='{$photo['alt']}' class='{$this->image_default}' data-rel='{$modalViewType}'><img itemprop='image' src='{$photo['path']}' alt='{$photo['alt']}'>" . $this->getPhotoText($photo['path']) . "</a>";
                        $smallphoto .= "<div class='g_m_img {$this->image_default}' data-val='{$index}'>
                                <img src='{$photo['preview']}' alt='{$photo['alt']}'>
                            </div>";
                    }
                } else {
                    $bigphoto = "<div class='image-default image-contain image-noimg'>
                            <img alt='no photo' src='" . getnoimage("big") . "' style='width: 100%;' class='nophoto'>
                        </div>";
                }

                $this->bigphoto = "<div class='owl-carousel owl-incard'>{$bigphoto}</div>";
                $this->smallphoto = $smallphoto ? "<div class='gallery-mini'>{$smallphoto}</div>" : "";
            } else {
                if (!empty($this->photos)) {
                    $this->image = $this->photos['0']['preview'];
                    $imageFull = $this->photos['0']['path'];
                }
                # ссылка
                $link = $this->fullLinkFinal();
                $photo_noimage = $noimage;

                # таблица
                if ($this->class == 2025 || $this->class == 2031) {
                    if ($imageFull) {
                        $this->image = $imageFull;
                    }
                    if ($this->image) {
                        $link = $this->image;
                        $linkAttr = "data-rel=\"lightcase\"";
                    } else {
                        $cls[] = "event-none";
                    }
                    $cls[] = "table-image";
                }
                # таблица без фото
                if ($this->class == 2025) {
                    $photo_noimage = "/images/table-nophoto.svg";
                    if ($this->image) {
                        $this->image = "/images/table-photo.svg";
                    }
                    $cls[] = "table-photo";
                }


                # нет фото
                if (!$this->image) {
                    $this->nophoto = 1;
                    $this->image = $photo_noimage;
                }

                # классы img
                $cls[] = stristr($this->image, "nophoto.") ? "nophoto" : "";
                $cls[] = 'lazy';
                if ($cls)
                    $class = "class='" . implode(" ", $cls) . "'";


//<div class='image-default'><!--noindex--><a class='fast_prew' title='(Alize) Baby wool batik 7543' data-lc-href='/catalog/pryazha-Alize/pryazha-Alize-Baby-wool-batik/Alize-Baby-wool-batik-7543-cbb44ca6.html' href='#' data-rel='lightcase' data-maxwidth='810' data-groupclass='card-fast-prew'>1111Быстрый просмотр</a><!--/noindex--><a href='/catalog/pryazha-Alize/pryazha-Alize-Baby-wool-batik/Alize-Baby-wool-batik-7543-cbb44ca6.html' >
    //     <img alt='' data-photo='1' src='/a/idearuk/files/userfiles/images/catalog/cbb44ca6-4c3c-11ec-9d16-5413793c9fb8_bf772b86-61b3-11ed-9dbf-5413793c9fb8.jpeg'>
// </a></div>

                $photoMain = "<div class='" . ($this->nophoto ? image_fit() : $this->image_default) . "'>";
                if (!$this->extlink && $this->class != 2031 && $this->class != 2025) {
                    $photoMain .= "<!--noindex--><a class='fast_prew' title='" . wrap($this->name) . "' {$this->modalAttrLink}>{$this->fastview}</a><!--/noindex-->";
                }
                $dm = !strstr($this->image, "http://") && !$current_catalogue['https'] ? $IMG_HOST : "";

                $photoMain .= "<a " . ($setting['cartopenmodal'] ? $this->modalAttrLink : "href='".$link."'") . " {$linkAttr}>
                                    " . ($this->colorphotos ? $this->colorphotos : "<img src='{$dm}{$this->image}' alt='{$this->name}' {$class}>") . "
                                </a>";
                $photoMain .= "</div>";

                if($AUTH_USER_ID== 2780){
                    // echo '<pre>';
                    // var_dump($this->colorphotos);
                    // echo '</pre>';

                    // exit;
                }

                $this->photoMain = $photoMain;
            }
        }
    }
	

	
	
    # текст фото
    static function getPhotoText($url)
    {
        $text = "";
        if (stristr($url, "/ggl/")) {
            $text = "<div class='gallery-photo-text'>Демонстрационное изображение подобрано автоматически и может отличаться от реального вида товара</div>";
        }
        return $text;
    }

    # Объект товара
    static function getItemById($id)
    {
        global $db;

        $param = $db->get_row("select * from Message2001 where Message_ID = '{$id}'", ARRAY_A);
        if ($param) {
            # Фотография
            $param['photo'] = new nc_multifield('photo', 'Фотографии', 0);
            $photo_data = $db->get_results("SELECT Name, Size, Path, Field_ID, Preview, ID, Priority FROM Multifield WHERE Field_ID = 2353 AND Message_ID = {$id} ORDER BY `Priority`", ARRAY_A);
            if ($photo_data) {
                $param['photo']->set_data($photo_data);
            }
            # Ссылка на товар
            $param['fullLink'] = nc_message_link($id, 2001);
            # Ссылка на товар
            $param['currency_id'] = $param['currency'];
            # объект товара
            $param['orgName'] = $param['name'];
            $param['classID'] = 2001;
            $itemObject = new Class2001($param);

            return $itemObject;
        }

        return false;
    }

    # Цвета товара
    public function setLabel()
    {
        if (function_exists('class2001_setLabel')) {
            class2001_setLabel($this); // своя функция
        } else {
            global $s_label;
            $labelHtml = "";

            if (($this->itemlabel || is_numeric($this->itemlabel)) && $this->itemlabel != 'null') {
                $itemlabel = explode(",", $this->itemlabel);
                foreach ($itemlabel as $key) {
                    $key++;
                    $labelHtml .= "<div class='blk_st" . ($this->full ? "_full" : "") . " blk_st_{$key} " . ($s_label[$key]['text'] ? "cursor" : "") . "'>
                                        <span style='background: " . $s_label[$key]['color1'] . "; color:" . $s_label[$key]['color2'] . ";'>
                                            " . $s_label[$key]['name'] . "
                                        </span>
                                        " . ($s_label[$key]['text'] ? "<div class='blk_st_text" . ($this->full ? "_full" : "") . "'>{$s_label[$key]['text']}</div>" : "") . "
                                    </div>";
                }
            }
            $this->labelHtml = $labelHtml;
        }
    }

    # Варианты товара
    public function setVariable()
    {
        global $setting;

        if ($this->variable) {
            foreach (orderArray($this->variable) as $variantid => $variant) {
                if (!$variant['price'] && $this->price) {
                    $variant['price'] = $this->price;
                }

                # общая наценка
                if (is_numeric($setting['markup']) && $variant['price'] && !$this->notmarkup) {
                    $variant['newprice'] = number_format($variant['price'] + $variant['price'] * $setting['markup'] / 100, 2, '.', '');
                }

                # скидка
                if (($this->discont > 0 || $this->pricediscont > 0) && $this->disconttime && dateCompare(date("Y-m-d H:i:s"), $this->disconttime, "minutes", 1) > 0) {
                    if ($this->discont > 0) {
                        $variant['newprice'] = $variant['price'] - $variant['price'] * $this->discont / 100;
                    }
                    if ($this->pricediscont > 0) {
                        $variant['newprice'] = $this->pricediscont;
                    }
                }
                # минимальная цена варианта
                if ($variant['price'] > 0 && ($variantprice > $variant['price'] || !$variantprice)) {
                    $variantprice = $variant['price'];
                }

                $variantdataprice = str_replace(",", ".", ($variant['newprice'] ? $variant['newprice'] : ($variant['price'] ? $variant['price'] : $this->price)));
                if ($variant['newprice'] > 0) {
                    $oldprice = ($variant['price'] ? $variant['price'] : $this->price);
                }

                # остатки
                $vrStocks = $vrStocks + $variant['stock'];

                if ($variant['name']) {
                    $variants .= "<option value=''
                                            data-stock='" . ($variant['stock'] ? $variant['stock'] : 0) . "'
                                            data-num='{$variantid}'
                                            data-oldprice='" . $oldprice . "'
                                            data-price='" . $variantdataprice . "'
                                            data-name='" . (!$setting['selfvariablename'] ? $this->name . " " : "") . "{$variant['name']}'
                                            data-hex='" . hexprice($variantdataprice) . "'
                                        >{$variant['name']}</option>";
                }
            }
            if ($variantprice > 0) {
                $this->price = $variantprice;
            }
            if ($vrStocks > 0) {
                $this->stock = $vrStocks;
            }

            if ($variants) {
                $this->variantsHtml = "<select class='select-style select-variable js-variable'>
                                            <option value=''>- выберите -</option>
                                            $variants
                                        </select>";
                if ($this->full) {
                    $this->variantsHtml = "<div class='cart-line line-varibale'>
                                                <div class='cart-line-title'>{$this->nameVariable}:</div>
                                                <div class='cart-line-body'>{$this->variantsHtml} </div>
                                            </div>";
                }
            }
        }
    }

    # Варианты товара по названию
    public function setVariableName()
    {
        if (function_exists('class2001_setVariableName')) {
            class2001_setVariableName($this); // своя функция
        } else {
            global $catalogue, $setting, $db, $AUTH_USER_ID,$canonicalLink,$action;

            # одноименные товары, размеры
            if (
                $setting['groupItem']
                || ($this->full && $setting['variableNameInFullCart'])
                || (!$this->full && $setting['variableNameInCart'])
            ) {
                $nam = str_replace(['&quot;', '&lt;', '&gt;'], ['\"', '<', '>'], $this->orgName);
                
            
                $oneNameItem = $db->get_results("SELECT Message_ID, variablename
                                                 FROM Message{$this->classID}
                                                 WHERE name = '{$nam}'
                                                    AND Catalogue_ID = '{$catalogue}'
                                                    AND Subdivision_ID = '{$this->Subdivision_ID}'
                                                    AND Checked = 1
                                                ORDER BY Priority", ARRAY_A);

                $url = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID = {$this->sub}");
                
                if($this->full){
                    $canonicalLink = nc_message_link($oneNameItem[0]['Message_ID'], 2001);

                }

                $oneitemHtml = '';
                if (count($oneNameItem) > 1) {
                    foreach ($oneNameItem as $num => $oneni) {
                        $data = $this->full ? "data-url='" . nc_message_link($oneni['Message_ID'], 2001) . "'" : "data-url='{$url}' data-id='{$oneni['Message_ID']}' data-ncctpl='{$this->nc_ctpl}'";
                        $selected = $this->id == $oneni['Message_ID'] ? "selected" : "";
                        $oneitemHtml .= "<option {$selected} {$data}>" . ($oneni['variablename'] ? $oneni['variablename'] : "вариант № " . ($num + 1)) . "</option>";
                    }
                    if ($oneitemHtml) {
                        $this->variantsNameHtml = "<select class='select-style select-variable js-variable' {$nam}>
                                                        {$oneitemHtml}
                                                    </select>";

                        // Если полное отображение товара
                        if ($this->full) {
                            $this->variantsNameHtml = "<div class='cart-line line-varibalename'>
                                                            <div class='cart-line-title'>{$this->nameVariable}:</div>
                                                            <div class='cart-line-body'>{$this->variantsNameHtml}</div>
                                                        </div>";
                        }
                    }
                }
            }
        }
    }

    # Цвета товара
    public function setColor()
    {
        if (function_exists('class2001_setColor')) {
            class2001_setColor($this); // своя функция
        } else {
            global $setting, $kurs,$AUTH_USER_ID;

            $colorsArr = orderArray($this->colors);
            if($AUTH_USER_ID == 2780){
                // echo '<pre>';
                // var_dump($this->photo->to_array()[0]->Name);
                // exit;
            }
            if (count($colorsArr) > 0) {
                $pi = 0;
                $this->colorphotos = '';
     
                // Если есть первое фото товара вывести его. Потом уже по цветам.
                if ($this->photo->get_record(1)) {
                    $pi++;
                    $this->colorphotos .= "<img alt='1{$this->photo->to_array()[0]->Name}' data-photo='1' src='{$this->photo->get_record(1)}'>";
                }
                foreach ($colorsArr as $colorid => $color) {
                    $color['code'] = str_replace("#", "", $color['code']);

                    if ($color['price']) {
                        $price = $color['price'];
                        if ($this->currency_id == 2 && ($kurs['dollar'] > 0 || $setting['dollar'] > 0)) {
                            $price = $price * ($setting['dollar'] ?: $kurs['dollar']);
                        }
                        if ($this->currency_id == 3 && ($kurs['euro'] > 0 || $setting['euro'] > 0)) {

                            $price = $price * ($setting['euro'] ?: $kurs['euro']);
                        }
                        if ($this->currency_id == 4 && ($kurs['tenge'] > 0 || $setting['tenge'] > 0)) {
                            $price = $price * ($setting['tenge'] ?: $kurs['tenge']);
                        }
                    } else {
                        $price = $this->getPrice();
                    }

                    # минимальная товара
                    if ($price > 0 && ($this->minPrice > $price || !$this->minPrice)) {
                        $this->minPrice = $price;
                    }

                    # обработка цены (скидки и т.д.)
                    $priceResult = $this->actionsPrice($price);


                    if ($color['name']) {
                        $colors .= "<span class='color-item'
                                        data-num='{$colorid}'
                                        data-colorphoto='{$color['photo']}'
                                        data-colorname='{$color['name']}'
                                        data-colorcode='{$color['code']}'
                                        title='{$color['name']}'
                                        data-stock='" . ($color['stock'] ? $color['stock'] : 0) . "'
                                        data-stockhave='" . ($color['stock'] > 0 || $setting['itemliststockall'] ? 1 : 0) . "'
                                        data-stockname='" . ($color['stock'] > 0 || $setting['itemliststockall'] ? $this->nalich : $this->podzakaz) . "'
                                        data-oldprice='{$priceResult['priceBefore']}'
                                        data-price='{$priceResult['price']}'
                                        data-name='{$this->name} {$color['name']}'
                                        data-hex='" . hexprice($priceResult['price']) . "'
                                    >
                                        <span class='color-item-child' style='background:#{$color['code']};'></span>
                                        <span class='variable-item-child'>{$color['name']}</span>
                                    </span> ";
                    }

                    if (is_numeric($color['photo']) && $color['photo'] !== 1 && $this->photo->get_record($color['photo']) && $this->class != 2025) {
                        $pi++;
                  
                        $this->colorphotos .= "<img alt='{$this->name} {$color['name']}' data-photo='{$color['photo']}' class=none src='{$this->photo->get_record($color['photo'])}'>";
                    }
                    $clrStocks = $clrStocks + $color['stock'];
                }

                if ($clrStocks > 0 && !$vrStocks) {
                    $this->stock = $clrStocks;
                }
                if ($colors) {
                    $this->colorsHtml = "<div class='color-body select-color js-variable " . ($setting['typeSelectVariable'] ? "variable-type-" . $setting['typeSelectVariable'] : "") . "' data-name='{$this->nameColor}'>{$colors}</div>";

                    if ($this->full) {
                        $this->colorsHtml = "<div class='cart-line line-colors'>
                                                <div class='cart-line-title'>{$this->nameColor}:</div>
                                                <div class='cart-line-body'>{$this->colorsHtml}</div>
                                            </div>";
                    }
                }
            }
        }
    }

    # Цена
    public function setPrice()
    {
        if (function_exists('class2001_setPrice')) {
            $price = class2001_setPrice($this); // своя функция
        } else {
            global $setting, $cityid, $AUTH_USER_ID, $currency;
            if ($this->dogovor) {
                $this->price = 0;
                $this->priceHtml = "<span class='price-dogovor'>" . getLangWord('cart_contPrice', 'договорная цена') . "</span>";
            } else {
                $this->pricefirst = $this->price;
                # вывод ед. у цены
                if ($setting['edizinprice']) {
                    $edizminprice = "<span class='price-edizm'> / " . ($this->edizm ? $this->edizm : 'шт') . "</span>";
                }
                # взять цену товара
                $price = $this->getPrice();
                $r = $price;
                # мин. цена варианта
                $price = $this->minPrice > 0 && ($this->minPrice < $price || !$price) ? $this->minPrice : $price;

                # обработка цены (скидки и т.д.)
                $priceResult = $this->actionsPrice($price);

                # конечная цена
                $this->price = $priceResult['price'];
                $this->priceHtml = "<span class='cen' >" . price($priceResult['price']) . "</span> {$currency['html']}{$edizminprice}";
                if ($this->full) {
                    $this->priceHtml .= "<meta itemprop='price' content='{$r}'>";
                }

                if ($priceResult['priceBefore'] != $priceResult['price'] && $priceResult['priceBefore'] > 0) {
                    $this->lastPrice = $priceResult['priceBefore'];
                    $this->lastPriceHtml = "<span class='cen'>" . price($priceResult['priceBefore']) . "</span> {$currency['html']}{$edizminprice}";

                    $this->differencePrice = $priceResult['priceBefore'] - $priceResult['price'];
                    $this->differencePriceHtml = "<span class='cen'>" . price($this->differencePrice) . "</span> {$currency['html']}{$edizminprice}";
                    if ($this->full) {
                        $this->differencePriceHtml .= "<meta itemprop='price' content='{$this->differencePrice}'>";
                    }
                }

                if ($this->getOrderCountPrice()) {
                    $countPriceHtml = "<div class='spoiler count-price-info'>";
                    $countPriceHtml .= "<span class='spoiler-icon'></span>";
                    $countPriceHtml .= "<ul class='spoiler-body'>";
                    foreach (array_reverse($this->getOrderCountPriceConverted()) as $countPrice) {
                        $countPriceHtml .= "<li>от {$countPrice['count']} " . $this->getShortEdzim($this->edizm) . " — " . price($countPrice['price']) . " {$currency['html']}</li>";
                    }
                    $countPriceHtml .= "</ul>";
                    $countPriceHtml .= "</div>";

                    $this->priceHtml .= $countPriceHtml;
                }
                # нет цены
                if (!$this->price || !$this->Checked) {
                    $this->price = 0;
                }
                # Если нет вналичие
            }
        }
    }

    # взять цену товара без обработки
    public function getPrice()
    {
        if (function_exists('class2001_getPrice')) {
            $price = class2001_getPrice($this); // своя функция
        } else {
            global $cityid;
            # текущая колонка цен
            $price = groupPrice(
                array(
                    0 => $this->price,
                    1 => $this->price2,
                    6 => $this->price3,
                    7 => $this->price4
                )
            );

            # цена для города
            if ($this->pricecity) {
                $pricecity = orderArray($this->pricecity);
            }
            if (is_numeric($cityid) && $pricecity[$cityid]['price']) {
                $price = $pricecity[$cityid]['price'];
            }

            $price = $this->convertPrice($price);
        }
        return $price;
    }

    /**
     * Конвертация цены в рубли
     * 
     * @param float $price
     * 
     * @return float
     */
    public function convertPrice($price)
    {
        global $kurs, $setting;

        switch ($this->currency_id) {
            case 2:
                if (!empty($kurs['dollar']) || !empty($setting['dollar']))
                    $price = round($price * ($setting['dollar'] ?: $kurs['dollar']), 2);
                break;
            case 3:
                if (!empty($kurs['euro']) || !empty($setting['euro']))
                    $price = round($price * ($setting['euro'] ?: $kurs['euro']), 2);
                break;
            case 4:
                if (!empty($kurs['tenge']) || !empty($setting['tenge']))
                    $price = round($price * ($setting['tenge'] ?: $kurs['tenge']), 2);
                break;
        }

        return $price;
    }

    # обработка цены
    public function actionsPrice($price)
    {
        if (function_exists('class2001_actionsPrice')) {
            return class2001_actionsPrice($price, $this);
        }

        global $AUTH_USER_ID, $setting;
        if ($price) {
            # общая наценка
            $price = $this->markup($price);
            # цена до изменений
            $this->priceBefore = $price;
            # скидочные цены
            $price = $this->discont($price);
            # финал цена
            $price = $this->finalPrice($price);
        }

        if ($setting['kopeik'] == false) {
            $price = round($price);
            $this->priceBefore = round($this->priceBefore);
        }

        return array(
            'price' => $price > 0 ? $price : "",
            'priceBefore' => $price > 0 && $this->priceBefore != $price ? $this->priceBefore : ""
        );
    }
    # наценка
    public function markup($price)
    {
        if (function_exists('class2001_markup')) {
            return class2001_markup($this, $price);
        } else {
            global $setting, $cityid;

            # общая наценка в настройках сайта
            if (is_numeric($setting['markup']) && $price && !$this->notmarkup) {
                $price = $price + $price * ($setting['markup'] / 100);
                $price = number_format($price, 2, '.', '');
            }

            return str_replace(",", ".", $price);
        }
    }
    # скидка
    public function discont($price)
    {
        if (function_exists('class2001_discont')) {
            return class2001_discont($this, $price);
        } else {
            if (($this->discont > 0 || $this->pricediscont > 0) && $this->disconttime && dateCompare(date("Y-m-d H:i:s"), $this->disconttime, "minutes", 1) > 0) {
                if ($this->discont > 0) {
                    $discontPrice = $price - $price * $this->discont / 100;
                }
                if ($this->pricediscont > 0) {
                    $discontPrice = $this->pricediscont;
                }
            }
            return $discontPrice ? $discontPrice : $price;
        }
    }
    # скидка
    public function finalPrice($price)
    {
        global $setting, $AUTH_USER_ID;
        return (!$this->dogovor && (!$setting[priceForAuth] || ($AUTH_USER_ID && $setting[priceForAuth])) && $price ? str_replace(",", ".", $price) : 0);
    }


    # время акции
    public function setDiscontTime()
    {
        if ($this->lastPrice && $this->timer && $this->disconttime) {
            $this->discontText = "<div class='blk_st" . ($this->full ? "_full" : "") . " blk_st_action'><span style='background:#FF4343;'>{$this->discontTitle}</span></div>";

            $this->discontCount = "
            <div class='blk_action'>
                <span class='blk_act_icon blk_act_lr'></span>
                <span class='countdown_time' data-countdown='{$this->disconttime}'></span>
                <span class='blk_act_number blk_act_lr'>
                    " . ($this->stock > 0 ? "<span class='blk_act_n'>{$this->stock}</span><span class='blk_act_t'>" . ($this->edizm ? $this->edizm : "шт") . "</span>" : null) . "
                </span>
            </div>
            <div class='blk_action_bottom'></div>";

            $this->discontCountFull = "
                <div class='blk_action_card'>
                    <span class='blk_act_icon_card blk_act_lr'></span>
                    <span class='blk_act_text'>{$this->discontTitle}</span>
                    <span class='countdown_time' data-countdown='{$this->disconttime}'></span>
                    <span class='blk_act_number_card blk_act_lr'>
                        " . ($this->stock > 0 ? "<span class='blk_act_n'>{$this->stock}</span><span class='blk_act_t'>" . ($this->edizm ? $this->edizm : "шт") . "</span>" : null) . "
                    </span>
                </div>";
        }
    }

    # ссылка
    public function setLink()
    {
        if (function_exists('class2001_setLink')) {
            class2001_setLink($this); // своя функция
        } else {
            global $catalogue, $setting;

            $this->nameFull = wrap($this->name) . " " . ($setting['vendorItemName'] ? $this->vendor : null) . " " . ($setting['variableItemName'] ? $this->variablename : null);
            $link = $this->fullLinkFinal;

            $attrLink = $setting['cartopenmodal'] ? $this->modalAttrLink : " href='{$link}'";

            $this->link = "<a {$attrLink}><span>" . trim(wrap($this->nameFull)) . "</span></a>";
        }
    }

    # артикул
    public function setArt()
    {
        if ($this->art) {
            $this->artHtml = "<span class='art_title" . ($this->full ? "_full" : "") . "'>{$this->artname}: </span><span class='art_value" . ($this->full ? "_full" : "") . "'>{$this->art}</span>";
        }
    }
    public function setArt2()
    {
        if ($this->art2) {
            $this->art2Html .= "<span class='art2_title_full art_title_full'>{$this->artname2}: </span><span class='art2_value_full art_value_full'>{$this->art2}</span>";
        }
    }

    # в наличии / под заказ
    public function setStock()
    {
        if (function_exists('class2001_setStock')) {
            $this->stockHtml = class2001_setStock($this); // своя функция
        } else {
            global $setting, $cityphone, $AUTH_USER_ID;

            $this->totalStock = 0;
            if ($this->stock) {
                $this->totalStock += $this->stock;
            }
            if ($this->stock2) {
                $this->totalStock += $this->stock2;
            }
            if ($this->stock3) {
                $this->totalStock += $this->stock3;
            }
            if ($this->stock4) {
                $this->totalStock += $this->stock4;
            }

            if ($setting['nostock_noprice'] && !$this->totalStock) {
                $this->price = 0;
            }

            if ($setting['itemliststock']) {
                # склад текущий
                if ($cityphone['sklad1c']) {
                    $stock = 0;
                    foreach (explode(",", $cityphone['sklad1c']) as $skl) {
                        if ($skl) {
                            $stock .= $stock + $this->{$skl};
                        }
                    }
                    $this->stock = $stock;
                }
                // Ilsur Изменил условия $this->stock поменял на $this->totalStock для drovosek-nk.ru 06.08.2021
                // verh добавлена настройка Разделения остатков по складам skladstock, с ней totalStock не учитывается
                if ($this->Checked && ((!$setting['skladstock'] && $this->totalStock > 0) || ($setting['skladstock'] && $this->stock > 0) || $setting['itemliststockall'])) {
                    $this->stockHtml = "<span class='instock icons i_check'>$this->nalich";
                } else {
                    $this->stockHtml = "<span class='nostock icons i_del3'>$this->podzakaz";
                }
            }
            if ($setting['stockValShow']) {
                $this->stockHtml .= ($this->stock > 0 ? (($this->stockHtml ? ': ' : '<span>') . "<span>" . $this->stock . " " . $this->getShortEdzim($this->edizm) . '</span>') : '');
            }

            $this->stockHtml .= "</span>";
        }
        // $this->stockHtml = $this->stock>0 || $setting[itemliststockall] ? "<span class='instock icons i_check'>$this->nalich</span>" : "<span class='nostock icons i_del3'>$this->podzakaz</span>";
    }

    # производители
    public function setVendor()
    {
        if (function_exists('class2001_setVendor')) {
            class2001_setVendor($this); // своя функция
        } else {
            global $catalogue, $db;

            if ($this->vendor) {
                if ($this->full) {
                    $vendorID = $db->get_var("select a.Message_ID from Message2030 as a, Subdivision as b where a.Subdivision_ID = b.Subdivision_ID AND b.Catalogue_ID = '{$catalogue}' AND a.name = '" . (!$this->brand ? $this->vendor : $this->brand) . "'");
                    if ($vendorID) {
                        $link = nc_message_link($vendorID, 2030);
                        $this->seoVendorManufacturer = "<link itemprop='manufacturer' href='{$link}'/>";
                    }
                    $this->vendorLink = ($vendorID ? "<a href='{$link}' target='_blank' ><span>{$this->vendor}</span></a>" : $this->vendor);
                } else {
                    $this->vendorHtml = "<span class='vendor_title'>{$this->vendorname}: </span><span class='vendor_value'>{$this->vendor}</span>";
                }
            }
        }
    }

    # теги
    public function setTags()
    {
        $tags = "";

        if ($this->tags) {
            foreach (explode(",", $this->tags) as $t) {
                if (trim($t)) {
                    $tags .= "<a href='/search/?tag=" . strip_tags(trim($t)) . "'>" . strip_tags(trim($t)) . "</a>";
                }
            }
            if ($tags) {
                $this->tagsHtml = "<div class='cart-tags'>{$tags}</div>";
            }
        }
    }


    # Параметры товара
    public function setParams($param = '')
    {
        global $setting_texts;
        $p = array(
            "variablename" => $setting_texts['full_cat_prodOpt']['checked'] ? $setting_texts['full_cat_prodOpt']['name'] : "Вариант товара",
            "vendorLink" => $setting_texts['full_cat_vendor']['checked'] ? $setting_texts['full_cat_vendor']['name'] : "Производитель",
            "edizm" => $setting_texts['edizm']['checked'] ? $setting_texts['edizm']['name'] : "Единица измерения",
            "ves" => $setting_texts['ves']['checked'] ? $setting_texts['ves']['name'] : "Вес",
            "capacity" => $setting_texts['capacity']['checked'] ? $setting_texts['capacity']['name'] : "Объем",
            "sizes_item" => $setting_texts['sizes_item']['checked'] ? $setting_texts['sizes_item']['name'] : "Размеры",
            "width" => $setting_texts['width']['checked'] ? $setting_texts['width']['name'] : "Ширина",
            "length" => $setting_texts['length']['checked'] ? $setting_texts['length']['name'] : "Длина",
            "height" => $setting_texts['height']['checked'] ? $setting_texts['height']['name'] : "Высота",
            "depth" => $setting_texts['depth']['checked'] ? $setting_texts['depth']['name'] : "Глубина",
            "var1" => $setting_texts['var1']['checked'] ? $setting_texts['var1']['name'] : "",
            "var2" => $setting_texts['var2']['checked'] ? $setting_texts['var2']['name'] : "",
            "var3" => $setting_texts['var3']['checked'] ? $setting_texts['var3']['name'] : "",
            "var4" => $setting_texts['var4']['checked'] ? $setting_texts['var4']['name'] : "",
            "var5" => $setting_texts['var5']['checked'] ? $setting_texts['var5']['name'] : "",
            "var6" => $setting_texts['var6']['checked'] ? $setting_texts['var6']['name'] : "",
            "var7" => $setting_texts['var7']['checked'] ? $setting_texts['var7']['name'] : "",
            "var8" => $setting_texts['var8']['checked'] ? $setting_texts['var8']['name'] : "",
            "var9" => $setting_texts['var9']['checked'] ? $setting_texts['var9']['name'] : "",
            "var10" => $setting_texts['var10']['checked'] ? $setting_texts['var10']['name'] : "",
            "var11" => $setting_texts['var11']['checked'] ? $setting_texts['var11']['name'] : "",
            "var12" => $setting_texts['var12']['checked'] ? $setting_texts['var12']['name'] : "",
            "var13" => $setting_texts['var13']['checked'] ? $setting_texts['var13']['name'] : "",
            "var14" => $setting_texts['var14']['checked'] ? $setting_texts['var14']['name'] : "",
            "var15" => $setting_texts['var15']['checked'] ? $setting_texts['var15']['name'] : "",
        );
        if ($param == "full") {
            $p['stock'] = $setting_texts['stock']['checked'] ? $setting_texts['stock']['name'] : "Наличие";
            $p['text'] = $setting_texts['text']['checked'] ? $setting_texts['text']['name'] : "Описание";
            $p['art'] = $setting_texts['art']['checked'] ? $setting_texts['art']['name'] : "Артикул";
            $p['comparison'] = "Сравнить";
        }

        if ($param == 'table') {
            $p['stock'] = $setting_texts['tbl_cat_stock']['checked'] ? $setting_texts['tbl_cat_stock']['name'] : "Наличие";
            $p['text'] = $setting_texts['tbl_cat_text']['checked'] ? $setting_texts['tbl_cat_text']['name'] : "Описание";
            $p['photo'] = $setting_texts['tbl_cat_photo']['checked'] ? $setting_texts['tbl_cat_photo']['name'] : "Фото";
            $p['name'] = $setting_texts['tbl_cat_name']['checked'] ? $setting_texts['tbl_cat_name']['name'] : "Название";
            $p['vendor'] = $setting_texts['tbl_cat_vendor']['checked'] ? $setting_texts['tbl_cat_vendor']['name'] : "Произ-ль";
            $p['art'] = $setting_texts['tbl_cat_art']['checked'] ? $setting_texts['tbl_cat_art']['name'] : "Артикул";
            $p['price'] = $setting_texts['tbl_cat_price']['checked'] ? $setting_texts['tbl_cat_price']['name'] : "Цена";
            $p['buy'] = $setting_texts['tbl_cat_buy']['checked'] ? $setting_texts['tbl_cat_buy']['name'] : "Купить";
            $p['var2'] = $setting_texts['tbl_cat_var2']['checked'] ? $setting_texts['tbl_cat_var2']['name'] : "Дата доставки";
        }
        return $p;
    }

    public function setParamsItemPreviewHTML()
    {
        if (function_exists('setParamsItemPreviewHTML_class2001')) {
            setParamsItemPreviewHTML_class2001($this);
        } else {
            global $action;

            if ($action == 'full')
                return;

            $result = '';
            foreach ($this->getParamsArray(false, 'preview') as $keyword => $param) {
                $result .= "<div class='cart-param-item-preview cart-param-{$keyword}'>
                                <span class='cartp-name'>{$param['name']}:</span>
                                <span class='cartp-value'>" . str_replace(";", "; ", $param['value']) . "</span>
                            </div>";
            }

            $this->parametrsPreview = ($result ? "<div class='cart-params-all'>{$result}</div>" : '');
        }
    }

    /**
     * Параметры товара HTML
     * 
     * @return void
     */
    public function setParamsItem()
    {
        if (function_exists('class2001_setParamsItem')) {
            $this->parameters = class2001_setParamsItem($this); // своя функция
        } else {
            global $setting_texts, $setting, $AUTH_USER_ID;
            # выйти, если мы не во внутряке
            if (!$this->full)
                return;

            foreach ($this->setParams() as $key => $name) {
                $val = $this->__get($key);
                if (($val || is_numeric($val)) && $name) {
                    $paramArr[$key]['name'] = $name;
                    $paramArr[$key]['value'] = $val;
                }
            }
            // * Не показывать единици измерения в полной каточке товара.
            if ($setting_texts['dont_show_edizm_in_full']['checked'] && $paramArr['edizm']) {
                unset($paramArr['edizm']);
            }

            foreach ($this->getParamsArray() as $keyword => $param) {
                $newKeyword = ($paramArr[$keyword]) ? $keyword . '_dop' : $keyword;
                $paramArr[$newKeyword] = ['name' => $param['name'], 'value' => $param['value']];
            }

            if ($paramArr) {
                $parameters = $parameter = "";
                $i = $paramsCount = 0;
                foreach ($paramArr as $paramName => $param) {
                    $i++;
                    if ($param['name'] && $param['value']) {
                        $paramsCount++;
                        $parameter .= "
                            <div class='cart-param-item cart-param-{$paramName}'>
                                <span class='cartp-name'>{$param['name']}:</span>
                                <span class='cartp-value'>" . str_replace(";", "; ", $param['value']) . "</span>
                            </div>
                        ";
                    }
                    if ($parameter && ($paramsCount % 2 == 0 || count($paramArr) == $i)) {
                        $parameters .= "<div class='cart-param-line'>{$parameter}</div>";
                        unset($parameter);
                    }
                }
            }

            $this->parameters = $parameters ? "<div class='cart-params-all'>" . $parameters . "</div>" : "";
        }
    }

    # деловые линии contacts
    public function setdeliveryMap()
    {
        global $cityid, $db, $catalogue, $cityname, $citymain, $setting, $AUTH_USER_ID, $cityphone;
        if ($setting['devlintarget']) {
            if (is_numeric($cityid)) {
                if ($cityphone['Message_ID']) {
                    $this->deliveryMap = "<div id='getInCity'>" . nc_objects_list($cityphone['Subdivision_ID'], $cityphone['Sub_Class_ID'], "&msg=" . $cityphone['Message_ID'], true, false) . "</div>";
                } else {
                    $s = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class where Catalogue_ID = '730' AND EnglishName = 'devlin' AND Class_ID = 2040 LIMIT 0,1", ARRAY_A);
                    # карта деловых линий
                    if ($s['sub'] && $s['cc']) {
                        $this->deliveryMap = "<div id='getInCity'>" . nc_objects_list($s['sub'], $s['cc'], "&isItem=1", true, false) . "</div>";
                    }
                }
            }
            # кол-во дней доставки
            if ($setting['devlinday']) {
                $this->deliveryDay = "<div class='delivery-days' data-cityname='{$cityname}' data-citymain='{$citymain}'>" . deliveryDays($citymain, ($cityname ? $cityname : $citymain)) . "</div>";
            }
        }
    }

    # купить в 1 клик
    public function buyOneClickLink()
    {
        global $setting;
        if (
            $this->cart
            && $this->order
            && $setting['buyoneclick']
            && !$this->fastprew
            && (!$setting['stockbuy']
                || $this->stock > 0
                || $this->totalStock > 0
            )
        ) {
            // data-rel='lightcase' {$this->orderLinkParam}
            $this->oneClickHtml = "<a href='javascript:void(0);' class='buy_one_click " . ($this->buycolors ? 'colors-required' : '') . "' data-title='{$this->buyoneclick_text}' title='{$this->buyoneclick_text}'>";
            $this->oneClickHtml .= "<span class='a_fast'>{$this->buyoneclick_text}</span>";
            $this->oneClickHtml .= "</a>";
        }
    }

    function setStar($itemname = '', $type = 'withSeo')
    {
        if (function_exists('class2001_setStar')) {
            class2001_setStar($this, $itemname, $type = 'withSeo'); // своя функция
        } else {
            global $current_sub, $AUTH_USER_ID;
            if ($current_sub['PrefixProductH1'])
                $itemname = \Korzilla\Replacer::replaceText($current_sub['PrefixProductH1']) . " {$itemname}";

            $starnum = ($this->id % 11) + 40;
            $stars = '';
            switch ($type) {
                case 'withSeo':
                    $stars .= "<div class='card_stars big_stars' itemprop='aggregateRating' itemtype='http://schema.org/AggregateRating'>
                                <meta itemprop='itemReviewed' content='" . $itemname . "' />
                                <meta itemprop='worstRating' content='0' />
                                <meta itemprop='bestRating' content='5' />
                                <meta itemprop='ratingValue' content='" . ($starnum / 10) . "' />
                                <meta itemprop='reviewCount' content='" . ($this->id % 130) . "' />
                                <div class='stars_select big_stars_select star_{$starnum}_now'></div>
                            </div>";
                    break;
                case 'noSeo':
                    $stars .= "<div class='card_stars big_stars'>
                                <div class='stars_select big_stars_select star_{$starnum}_now'></div>
                            </div>";
                    break;
            }
            return $stars;
        }
    }

    # Цена доступная после регистрации
    public function regPrice()
    {
        global $setting, $AUTH_USER_ID, $currency, $setting_texts;

        $text = "";
        if ($setting['registrationSale'] && $setting['registrationSale'] != "_empty_") {
            if (!$AUTH_USER_ID) {
                if ($this->full) {
                    if ($setting['registrationSale'] == 1 && $this->price2 > 0) {
                        $text = "<div class='req-price req-price-1'><span class='req-price-this'>{$this->price2} {$currency['html']}</span> цена доступна после <a href='/registration/'>регистрации</a></div>";
                    } elseif ($setting['registrationSale'] == 2) {
                        $text = "<div class='req-price req-price-2'>После <a href='/registration/'>регистрации</a> на сайте доступна оптовая цена.</div>";
                    }
                } else {
                    if ($setting['registrationSale'] == 1 && $this->price2 > 0) {
                        $text = "<div class='blk_salereg'><span class='req-price-this'>{$this->price2} {$currency['html']}</span> цена доступна после <a href='/registration/'>регистрации</a></div>";
                    } elseif ($setting['registrationSale'] == 2) {
                        $text = "<div class='blk_salereg'>Персональная скидка доступна после <a href='/registration/'>регистрации</a></div>";
                    }
                }
            } else {
                if ($this->full) {
                    $youLoggedIn = $setting_texts['you_logged_in']['checked'] ? $setting_texts['you_logged_in']['name'] : "Вы авторизованы";
                    $pastPriceText = $setting_texts['past_price_text']['checked'] ? $setting_texts['past_price_text']['name'] : "прошлая цена";

                    $textlast = $this->price2 > 0 && $this->price2 < $this->pricefirst ? "<b>{$this->pricefirst} {$currency['html']}</b> {$pastPriceText}" : "";
                    $text = "<div class='req-price req-price-2'>{$youLoggedIn}<br>{$textlast}</div>";
                }
            }
        }
        return $text;
    }

    # просмотры
    public function addViewCart()
    {
        global $db;

        if (!strstr($_COOKIE['views'], "-{$this->id}-")) {
            setcookie("views", ($_COOKIE['views'] ? $_COOKIE['views'] : "-") . "{$this->id}-", time() + 3600 * 24, "/");
            /*if ($this->view > 0) {
                $db->query("update Message{$this->classID} set view = view+1 where Message_ID = '{$this->id}'");
            } else {
                $db->query("update Message{$this->classID} set view = 1 where Message_ID = '{$this->id}'");
            }*/
        }
        if (!strstr($_COOKIE['myviews'], "-{$this->id}-")) {
            $myviews = $_COOKIE['myviews'];
            if (substr_count($myviews, '-') > 15) {
                $myviewsArr = explode("-", $myviews);
                unset($myviewsArr[0]);
                unset($myviewsArr[1]);
                $myviews = "-" . implode("-", $myviewsArr);
            }
            setcookie("myviews", ($myviews ? $myviews : "-") . "{$this->id}-", time() + 3600 * 24 * 14, "/");
        }
    }

    # сравнение товаров
    public function comparison()
    {
        if (function_exists('class2001_comparison')) {
            $this->comparisonHtml = class2001_comparison($this); // своя функция
        } else {
            global $setting, $plugins, $AUTH_USER_ID;

            # сравнение
            if ($setting['itemComparison']) {
                $this->comparisonHtml = "<div class='comparison " . ($_SESSION['comparison'][$this->id] ? "active" : "") . "'>
                                            <div class='comparison-add'>
                                                <div class='icons admin_icon_5'></div>
                                                <!--<div class='comparison-name'>{$this->sravnit}<span class='comparison-count'> (<span></span>)</span></div>-->
                                            </div>
                                            <div class='comparison-info'>
                                                <span class='comparison-add-text'>{$this->sravnitAdd}</span>
                                                <a class='comparison-link' href='/comparison/' target='_blank'>{$this->sravnitGo}</a>
                                            </div>
                                        </div>";
            }
        }
    }

    public function getAdditionalProducts()
    {
        global $setting, $db, $catalogue;
        $result = "";

        if ($setting['showAdditionalItems']) {
            $shopParentCategoryKeyword = $setting["shopParentCategoryKeyword"];
            if (!$shopParentCategoryKeyword) {
                $shopParentCategoryKeyword = "catalog";
            }
            $catalogParentSub = $db->get_row("
            SELECT Subdivision.Subdivision_ID, Sub_Class.Sub_Class_ID
            FROM Subdivision
            JOIN Sub_Class ON Subdivision.Subdivision_ID = Sub_Class.Subdivision_ID
            WHERE Subdivision.EnglishName = '$shopParentCategoryKeyword' AND Subdivision.Catalogue_ID = $catalogue
            ");
            if ($catalogParentSub->Subdivision_ID && $catalogParentSub->Sub_Class_ID) {
                $products = nc_objects_list(
                    $catalogParentSub->Subdivision_ID,
                    $catalogParentSub->Sub_Class_ID,
                    "&isTitle=1&nc_ctpl=2001&recNum=40&scrolling=1&randomSort=1&outallitem=1&scrollNav=1&showMoreProducts=1"
                );
                $result = "
                <section class='blocks  start end this block_buywith additional-items'>
                    <header class='blk_head nopadingLR' data-name-block='full_ponravtext'>
                        <div class='h2'>Рекомендуемые товары</div>
                    </header>
                    <article class='cb blk_body nopadingLR'>
                        <div class='blk_body_wrap'>
                            $products
                        </div>
                    </article>
                </section>
                ";
            }
        }
        return $result;
    }


    # сокращение едениц измерения
    public function getShortEdzim($name)
    {
        if (!$name) {
            return "шт.";
        }

        $wordsArr = array(
            'штук' => 'шт.',
            'кило' => 'кг.',
            'грам' => 'гр.',
            'литр' => 'л.'
        );

        foreach ($wordsArr as $key => $val) {
            if (mb_stristr($name, $key)) {
                $short = $val;
                break;
            }
        }
        return $short ? $short : $name;
    }

    # избранное
    public function favoritItem()
    {
        if (function_exists('class2001_favoritItem')) {
            $this->favoritHtml = class2001_favoritItem($this); // своя функция
        } else {
            global $setting;

            if ($setting['itemFavorite']) {
                $link = $this->extlink ? $this->extlink : $this->fullLink;
                $data = array(
                    "data-id='{$this->id}'",
                    "data-href='{$link}'",
                    "data-name='{$this->nameFull}'"
                );
                $checked = $this->favarit ? true : false;
                //. ($this->full ? "<label class='favorit_title' for='favItem{$this->id}'>" . ($this->favarit ? "В закладках" : "Добавить в закладки") . "</label>" : "")
                $this->favoritHtml = "<span class='favorit-flag" . ($this->favarit ? " active" : '') . "'>
                                        <input  type='checkbox'
                                                name='favItem{$this->id}' 
                                                id='favItem{$this->id}' 
                                                " . implode(' ', $data)
                    . ($this->favarit ? ' checked' : '') . ">
                                        <label for='favItem{$this->id}' class='icon'></label>
                                        </span>";
            }
        }
    }
    /**
     * @param string $path
     * @return string|false
     */
    public function getGlobPhoto($path)
    {
        global $AUTH_USER_ID, $DOCUMENT_ROOT;
        $path_parts = pathinfo($path);
        $extensions = ['jpg', 'JPG', 'png', 'PNG', 'jpeg', 'JPEG', 'gif'];
        $pathVariants = [
            $path_parts['dirname'] . '/' . $path_parts['basename'],
            $path_parts['dirname'] . '/' . str_replace(["/", " "], ["-", "-"], $path_parts['basename']),
            $path_parts['dirname'] . '/' . str_replace(["/"], [""], $path_parts['basename'])
        ];

        foreach ($pathVariants as $path) {
            foreach ($extensions as $extension) {
                $filePath = $path . '.' . $extension;
                if (file_exists($filePath)) {
                    $photoUrl = str_replace($DOCUMENT_ROOT, '', $filePath);
                    return str_replace(array("%2F", "+"), array("/", "%20"), urlencode($photoUrl));
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getNoPriceText()
    {
        if (function_exists('class2001_getNoPriceText')) {
            return class2001_getNoPriceText($this); // своя функция
        } else {
            global $setting;
            if (!$this->price && $setting['noprice_text']) {
                return "<span class='noprice-text'>{$setting['noprice_text']}</span>";
            }
            return '';
        }
    }

    /**
     * Возвращает цену за единицу товара 
     * в зависимости от количества товара
     * в корзине
     * 
     * @param int $countInOrder
     * 
     * @return float
     */
    public function getPriceByCountInBasket($countInOrder)
    {
        if (function_exists('class2001_getPriceByCountInBasket')) {
            return class2001_getPriceByCountInBasket($this, $countInOrder); // своя функция
        } else {
            foreach ($this->getOrderCountPriceConverted() as $data) {

                if ($countInOrder >= $data['count'])
                    return $data['price'];
            }

            return $this->price;
        }
    }

    /**
     * Возвращает массив цен для количества товара в корзине с применением модификаций
     * 
     * структура: ['количество' => ['price' => 'цена', 'count' => 'кол-во']] 
     *      отсортировано в порядке убывания ключа (количество)
     *      произведена наценка согласно курсу валют
     * 
     * @return array 
     */
    public function getOrderCountPriceConverted()
    {
        if (!isset($this->orderCountPriceCoverted)) {
            $orderCountPrice = [];

            foreach ($this->getOrderCountPrice() as $val) {
                $val['price'] = $this->convertPrice($val['price']);
                $orderCountPrice[$val['count']] = $val;
            }

            krsort($orderCountPrice, SORT_NUMERIC);

            $this->orderCountPriceCoverted = $orderCountPrice;
        }

        return $this->orderCountPriceCoverted;
    }

    /**
     * Возвращает массив цен для количества товара в корзине
     * 
     * Название поля в БД - order_count_price, структура в базе [{"count":"1","price":"1"},{"count":"2","price":"2.2"}]
     * 
     * @return array 
     */
    public function getOrderCountPrice()
    {
        return !empty($this->f['order_count_price']) ? json_decode($this->f['order_count_price'], true) : [];
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