<?php

use App\modules\Korzilla\Service\Delivery\Cdek\CalculatorData as CdekCalculatorData;
use App\modules\Korzilla\Service\Delivery\Cdek\Cdek;

class Class2005
{
	protected $orderStatusList;

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
		# заголовки
		$this->artname = ($setting_texts['full_cat_art']['checked'] ? $setting_texts['full_cat_art']['name'] : "Артикул");
		$this->cart_catalog_name = ($setting_texts['cart_catalog_name']['checked'] ? $setting_texts['cart_catalog_name']['name'] : "Каталог");

		# id объекта
		if (!$this->id && $this->RowID)
			$this->id = $this->RowID;
		# размер изображения
		if (!$this->image_default)
			$this->image_default = image_fit($setting["size2001_fit"]);

		# тип списков
		$this->typelist = $setting['cartListType'] ?: 'cartlistype-1';
		if ($this->typelist == "typecart1")
			$this->typelist = "cartlistype-1";
	}

	public function getCart($type = "")
	{
		if (function_exists('class2005_getCart')) {
			return class2005_getCart($this, $type); // своя функция
		} else {
			global $setting, $db, $SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $current_user, $nc_core, $currency, $AUTH_USER_ID;

			unset($_SESSION['cart']['delivery']);
			$_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['totalSumDiscont'];

			$org = "";
			$orgAll = $db->get_results("SELECT * FROM Classificator_org", ARRAY_A) ?: [];
			foreach ($orgAll as $orgItem) {
				$org .= "<option value='{$orgItem['org_ID']}' " . ($orgItem['org_ID'] == 2 ? "checked" : "") . ">{$orgItem['org_Name']}</option>";
			}

			if ($_SESSION['cart']['items']) {

				$bigcart[] = '<div class="cartclear_wrap none"><a href="#" class="cartclear"><span>Очистить корзину</span></a></div>';

				// Таблица товаров
				$cartItems = $this->getItems();
				$bigcart[] = $cartItems['table'];

				// Минимальный заказ
				if ($setting['minOrderSum']) {
					$bigcart[] = "<div class='minOrderFail warnText " . ($cartItems['totalSum'] > $setting['minOrderSum'] ? "none" : NULL) . "'>
									<b>Внимание!</b> Минимальный заказ от <span class='sum'>{$setting['minOrderSum']}</span> {$currency['html']}
								</div>";
				}

				$delivery = $this->getLists('delivery', 'req');
				$payment = $this->getLists('payment', 'req');
				$freeDelivery = $this->getFreeDelivery();

				$data = $setting['cartForm'] ? orderArray($setting['cartForm']) : "";

				#значения для заполнения полей формы
				$defaulValArr = array(
					'name' => trim("{$current_user['fam']} {$current_user['ForumName']} {$current_user['otch']}"),
					'phone' => $current_user['phone'],
					'email' => $current_user['Email'],
					'city' => $current_user['city'],
					'address' => $current_user['org'],
					'company' => $current_user['company'],
					'inn' => $current_user['inn'],
					'kpp' => $current_user['kpp']
				);
				$formRem = $current_user["User_ID"] ? orderArray($current_user["formRem"]) : array();
				$deliveryItem = $_SESSION['cart']['delivery']['id'] > 0 ? $this->getListName("delivery", $_SESSION['cart']['delivery']['id']) : null;
				foreach ($data as $name => $param) {

					$usertype = 1;
					$title = $param['label']['value'];
					$filedattr = "";
					$dopclass = "";

					if (
						stristr($title, "(Физ.лицо)")
						|| $param['userType']['value'] === 'физ лицо'
					) {
						$filedattr = "data-optbody='1'";
						$title = trim(str_replace("(Физ.лицо)", "", $title));
						$param['userType']['value'] = 'физ лицо';
					}

					if (
						stristr($title, "(Юр.лицо)")
						|| $param['userType']['value'] === 'юр лицо'
					) {
						$filedattr = "data-optbody='2'";
						$dopclass = "none";
						$title = trim(str_replace("(Юр.лицо)", "", $title));
						$param['userType']['value'] = 'юр лицо';
					}

					if ($deliveryItem && $deliveryItem['delivery_type'] != 2 && in_array($param['name']['value'], array('city', 'address', 'street', 'home', 'housing', 'porch', 'floor', 'apartment'))) {
						$dopclass .= " none-important ";
					}
					$name = explode("|", $name);
					$key = $name[0];

					$hiddenNames .= "<input name='customf[{$param['name']['value']}][name]' type='hidden' value='{$param['label']['value']}'>";
					if (!empty($param['userType']['value'])) {
						$hiddenNames .= "<input name='customf[{$param['name']['value']}][userType]' type='hidden' value='{$param['userType']['value']}'>";
					}
					if ($param['required']['value'])
						$hiddenRequired .= "<input name='req_customf[]' type='hidden' value='{$param['name']['value']}'>";
					$customName = $key == 'file' ? "customfile_{$param['name']['value']}" : "customf[{$param['name']['value']}][value]";
					$placeholder = $param['placeholder']['value'] ? "placeholder='{$param['placeholder']['value']}'" : "";
					$defaulVal = ($defaulValArr[$param['name']['value']] ? $defaulValArr[$param['name']['value']] : ($formRem[$param['name']['value']] ? $formRem[$param['name']['value']] : ""));

					# мультиязычность
					if ($setting['language']) {
						$title = getLangWord("cart_oform_" . ($param['name']['value'] ? $param['name']['value'] : $name[1]), $param['label']['value']);
						if ($placeholder) {
							$placeholder = "placeholder='" . getLangWord("cart_oform_placeholder_" . ($param['name']['value'] ? $param['name']['value'] : $name[1]), $param['placeholder']['value']) . "'";
						}
					}

					switch ($key) {
						case 'input':
							$type = array("текст" => "text", "число" => "number", "скрытый" => "hidden");
							$attrs = "maxlength='255' size='50' data-oneline {$placeholder} " . ($param['typeInput']['value'] ? "type='" . $type[$param['typeInput']['value']] . "'" : "");
							$fields = "<div class='person_line person_type_input person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_input_standart($customName, $defaulVal, $title, $attrs, $param['required']['value']) . "</div>";
							break;
						case 'textarea':
							$fields = "<div class='person_line person_type_textarea person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_textarea_standart($customName, $defaulVal, $title, "data-oneline " . $placeholder, $param['required']['value']) . "</div>";
							break;
						case 'select':
							$options = "";
							foreach ($param["selectGroup"]["values"] as $i => $select) {
								$options .= "<option value=" . getLangWord($select['value']) . " " . ($defaulVal && $defaulVal == $select['value'] ? "selected" : "") . ">" . getLangWord($select['value']) . "</option>";
							}
							$fields = "<div class='person_line person_type_select person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_select_standart($customName, $options, $title, "data-oneline class='select-style'", $param['required']['value']) . "</div>";
							break;
						case 'checkbox':
							$checkboxs = "";
							foreach ($param["checkboxGroup"]["values"] as $i => $checkbox)
								$checkboxs .= "<div class='person_line {$param['name']['value']}_{$i}'>" . bc_checkbox_standart($customName . "[]", $checkbox["value"], $checkbox["value"]) . "</div>";
							$fields = "<div class='person_line person_type_checkbox person_{$param['name']['value']} {$dopclass}' {$filedattr}><div class='input-fields-cart input-oneline'>" . bc_label_standart($title, "", $param['required']['value']) . "<div class='field-second'>{$checkboxs}</div></div></div>";
							break;
						case 'radio':
							$radios = "";
							foreach ($param["radioGroup"]["values"] as $i => $radio)
								$radios .= "<div class='person_line {$param['name']['value']}_{$i}'>" . bc_radio_standart($customName, $radio["value"], $radio["value"], ($i == 0 ? 1 : 0)) . "</div>";
							$fields = "<div class='person_line person_type_radio person_{$param['name']['value']} {$dopclass}' {$filedattr}><div class='input-fields-cart input-oneline'>" . bc_label_standart($title, "", $param['required']['value']) . "<div class='field-second'>{$radios}</div></div></div>";
							break;
						case 'title':
							$fields = "<div class='person_line person_type_title' {$filedattr}><h3>{$param['label']['value']}</h3></div>";
							break;
						case 'file':
							$fields = "<div class='person_line person_type_file person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_file_standart($customName, "", $title, "data-oneline") . "</div>";
							break;
						case 'date':
							$attrs = "maxlength='255' size='50' data-oneline {$placeholder} type='date'";
							$fields = "<div class='person_line person_type_input person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_input_standart($customName, $defaulVal, $title, $attrs, $param['required']['value']) . "</div>";
							break;
					}


					$allFields .= $fields;
				}
				$bigcart[] = "<form name='order' id='order' class='ajax2 json form hov' enctype='multipart/form-data' method='post' action='{$SUB_FOLDER}{$HTTP_ROOT_PATH}add.php' data-metr='cart'>
					<div class='order-left'>
						<div class='basket_blks'>
							<div class='person_body'>
								<div id='nc_moderate_form'>
									" . $nc_core->token->get_input() . "
									<input name='catalogue' type='hidden' value='{$catalogue}'>
									<input name='cc' type='hidden' value='{$this->cc}'>
									<input name='sub' type='hidden' value='{$this->sub}'>
									<input type='hidden' name='f_Checked' value='1'>
									<input type='hidden' name='hex_item' value='" . md5(implode('', array_keys($_SESSION['cart']['items']))) . "'>
									{$hiddenNames}
									{$hiddenRequired}
								</div>

								" . ($setting[cartusertype] ? "
									<div class='person_line person_usertype'>
										<div class='input-oneline'>
											<div class='field-first'></div>
											<div class='field-second'>
												" . bc_radio_standart("f_usertype", 0, getLangWord("cart_individual", "Физ.лицо"), 1, "data-opt='1'") . "
												" . bc_radio_standart("f_usertype", 1, getLangWord("cart_entity", "Юр.лицо"), 0, "data-opt='2'") . "
											</div>
										</div>
									</div>
								" : "") . "
								{$allFields}
								<div class='person_line person_politika'>
									<div class='input-oneline'>
										<div class='field-first'></div>
										<div class='field-second'>" . politika(0, 'left') . "</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='order-right {$this->typelist}'>
						<div class='basket_blks'>
							{$delivery}
							{$freeDelivery}
							{$payment}
							<div class='total_blk'>
								" . ($delivery ? "<div class='tot_item_all'>
									<div class='tot_item cb discontSumTr'>
										<div class='tot_item_1'>" . getLangWord('cart_cost', 'Стоимость') . ":</div>
										<div class='bor_line'></div>
										<div class='tot_item_2 discontSum'><span>" . number_format($_SESSION['cart']['totalSumDiscont'], 2, ',', ' ') . "</span> {$currency['html']}</div>
									</div>
									<div class='tot_item cb deliverSumTr'>
										<div class='tot_item_1'>" . getLangWord('cart_delivery', 'Доставка') . ":</div>
										<div class='bor_line'></div>
										<div class='tot_item_2 deliverSum'><span>" . number_format($_SESSION['cart']['delivery']['sum_result'], 2, ',', ' ') . "</span> {$currency['html']}</div>
									</div>
									<div class='tot_item cb deliverySumPayAfterTr none-important'>
										<div class='tot_item_1'>" . getLangWord('cart_pay_delivery_after_title', 'Оплата доставки при получении') . ":</div>
										<div class='bor_line'></div>
										<div class='tot_item_2 deliverySumPayAfter'><span>" . number_format($_SESSION['cart']['delivery']['sum'], 2, ',', ' ') . "</span> {$currency['html']}</div>
									</div>
								</div>" : "") . "
								<div class='total_sum cb'>
									<div class='total_sum_text'>" . getLangWord('cart_total', 'Итого') . ":</div>
									<div class='bor_line'></div>
									<div class='total_sum_price' data-totaldelsum='{$_SESSION['cart']['totaldelsum']}'>
										<span>" . number_format($_SESSION['cart']['totaldelsum'], 2, ',', ' ') . "</span>
										{$currency['html']}
									</div>
								</div>
								<div class='result'></div>
								<span class='btn-strt'><input type=submit class='blue_payment' id='big-checkout' value='" . getLangWord('btn_buy', 'Оформить заказ') . "'></span>
								<a href='/catalog/' class='back-catalog'>" . getLangWord('cart_backToShop', 'Вернуться к покупкам') . "</a>
							</div>
						</div>
					</div>
				</form>";
			} else {
				$bigcart[] = "<p class='no-obj'>" . getLangWord('cart_empty', 'Ваша корзина пуста.') . "</p>";
			}

			return implode("", $bigcart);
		}
	}

	# Все товары корзины
	public function getItems()
	{
		if (function_exists('class2005_getItems')) {
			return class2005_getItems($this); // своя функция
		} else {
			global $db, $currency, $catalogue, $AUTH_USER_ID;

			$totalSum = 0;
			#echo '<!--|||'.print_r($_SESSION,1).'-->';
			if ($_SESSION['cart']['items']) {
				foreach ($_SESSION['cart']['items'] as $itemid => $item) {

					# объект товара
					$itemObject = Class2001::getItemById($item[id]);
					if (!$itemObject && $item['sum'])
						continue;

					if (strstr($itemid, "_vr") || strstr($itemid, "_clr")) { # если есть варианты или цвета, получить id
						$vrIDarr = explode("_", $itemid);
						if (strstr($vrIDarr[1], "vr"))
							$vrID = str_replace("vr", "", $vrIDarr[1]);
						if (strstr($vrIDarr[1], "clr"))
							$clrID = str_replace("clr", "", $vrIDarr[1]);
						if (strstr($vrIDarr[2], "clr"))
							$clrID = str_replace("clr", "", $vrIDarr[2]);
					}

					if ($vrID && $itemObject->variable) { # узнать количество, если есть вариант товара
						$vrIDarr = explode("_vr", $itemid);
						$vrID = $vrIDarr[1];
						$variableArr = orderArray($itemObject->variable);
						$vrStock = $variableArr[$vrID]['stock'];
					}
					if ($clrID && !$vrID && $itemObject->colors) { # узнать количество, если есть цвета и нет вариантов (варианты в приоритете)
						$colorsArr = orderArray($itemObject->colors);
						$vrStock = $colorsArr[$clrID]['stock'];
					}
					#Mir
					#Не допустить переход в раздел partcomsys
					if (mb_strpos($itemObject->fullLink, 'partcomsys') !== false)
						$itemObject->fullLink = '#';
					#End Mir
					#ACAT v1
					if (strpos($item['l'], ':') !== false && $acat = $db->get_var("SELECT Subdivision_ID FROM Sub_Class WHERE Class_ID=210 AND Catalogue_ID=" . $catalogue)) {
						$acat = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID=" . $acat);
						$link = explode(':', $item['l']);
						$link = $acat . '?modelID=' . intval($link[0]) . '&treeID=' . intval($link[1]) . '#l' . $itemid;
					}
					#ACAT v2
					elseif (strpos($item['l'], '/') !== false && $acat = $db->get_var("SELECT Subdivision_ID FROM Sub_Class WHERE Class_ID=210  AND Class_Template_ID=2078 AND Catalogue_ID=" . $catalogue)) {
						$link = $db->get_var("SELECT Hidden_URL FROM Subdivision WHERE Subdivision_ID=" . $acat);
						$link .= '?id=' . $item['l'];
					} else
						$link = $itemObject->fullLink;
					#$itemObject->fullLink
					$items .= "<tr data-id='{$itemid}' data-stock='" . ($vrStock ? $vrStock : $itemObject->stock) . "'>
									<td class='bt_img nomob'>{$itemObject->photoMain}</td>
									<td class='bt_link'>
										<a href='{$link}" . ($vrID ? "#v_{$vrID}" : NULL) . "'>" . wrap(str_replace("\\", "", $item[name])) . ($item[variant] ? " (" . $item[variant] . ")" : ($item['variablename'] ? " {$item['variablename']}" : "")) . "</a>"
						. ($itemObject->art ? "<div class='mobyes'>Арт.: {$itemObject->art}</div>" : "")
						. ($item[colorname] ? "<div class='item-color-cart'>" . getLangWord('color', 'Цвет') . ": <span class='color' style='background: #{$item[colorcode]}' title='{$item[colorname]}'></span></div>" : "") . "
									</td>
									<td class='bt_art nomob'>" . ($itemObject->art ? $itemObject->art : "&mdash;") . "</td>
									<td class='price bt_price'>" . ($item[price] ? price($item[price]) : "&mdash;") . " {$currency['html']}</td>
									<td class='count bt_count'>
										<div class='bt_incard_num'>
											<input data-name='item{$itemid}' name=count type=number min=1 value='{$item[count]}' data-stock='{$item[stock]}'>
											<span class='mainmenubg-font-hov-bf bt_incard_up'></span>
											<span class='mainmenubg-font-hov-bf bt_incard_down'></span>
										</div>" . ($itemObject->edizm_XXX ? "<div class=edizm>{$itemObject->edizm}</div>" : "") . "
									</td>
									<td class='sum bt_pricesum'>" . ($item[sum] ? "<span class='totalsumItem'>" . price($item[sum]) . "</span> " . $currency["html"] : "&mdash; ") . "</td>
									<td class=bt_del><a href='' class='delitem'></a></td>
								</tr>";

					$totalSum = $totalSum + $item[sum];
				}

				$itemsHtml = "<div id='bigcart' class='cb basked_tab_blk'>
								    <table class='basked_table'>
									    <thead>
										    <tr>
											    <th class='bsk-th-photo nomob'>" . getLangWord('tbl_cat_photo', 'Фото') . "</th>
											    <th class='bsk-th-name tl padd'>" . getLangWord('tbl_cat_name', 'Наименование') . "</th>
											    <th class='bsk-th-art nomob'>" . getLangWord('tbl_cat_art', 'Артикул') . "</th>
											    <th class='bsk-th-price price'>" . getLangWord('tbl_cat_price', 'Цена') . "</th>
											    <th class='bsk-th-count kol-vo'>" . getLangWord('tbl_cat_quant', 'Кол-во') . "</th>
											    <th class='bsk-th-sum sum'>" . getLangWord('tbl_cat_sum', 'Сумма') . "</th>
											    <th></th>
										    </tr>
									    </thead>
									    <tbody>{$items}</tbody>
							    	</table>
							    </div>";
			}


			return array(
				'table' => $itemsHtml,
				'totalSum' => $totalSum
			);
		}
	}

	# получить информацию добора до бесплатной доставки
	public function getFreeDelivery()
	{
		if (function_exists('class2005_getFreeDelivery')) {
			return class2005_getFreeDelivery($this); // своя функция
		} else {
			global $setting, $currency, $AUTH_USER_ID;

			$html = "";

			if ($setting['freedelivery']) {
				$html = "<div class='delivery_free_info " . ($_SESSION['cart']['delivery']['sum_freevisible'] ? "" : "none") . "'>
							<div class='method_items'>
								<div class='delivery_free_text " . ($_SESSION['cart']['delivery']['sum_free'] ? "active" : "") . "'>
									<div class='delivery_free_1'>
										<span class='df_text1'>Для бесплатной доставки осталось: <b><span class='df_price'>" . ($_SESSION['cart']['delivery']['sum_nothave'] ? $_SESSION['cart']['delivery']['sum_nothave'] : 0) . "</span> {$currency['html']}</b>.</span>
										<span class='df_text2'>Вы можете вернуться в <a href='/catalog/' target='_blank'>{$this->cart_catalog_name}</a> и выбрать что-нибудь еще :)<span>
									</div>
									<div class='delivery_free_2'>Вам доставка бесплатная!</div>
								</div>
							</div>
						</div>";
			}

			return $html;
		}
	}

	public function getSettingsList($nameList)
	{
		global $setting, $catalogue;
		$result = $setting['lists_' . $nameList];
		switch ($nameList) {
			case 'delivery':
				if (Cdek::getInstance()->isOn()) {
					$result[] = [
						'name' => 'Доставка СДЭК',
						'checked' => 1,
						'price' => 0,
						'totsumfree' => 0,
						'type' => 'cdek',
						'delivery_type' => 2,
					];
				}
				if ($setting['edostCheck']) {
					$catalogs = array(716);
					if (in_array($catalogue, $catalogs))
						$result[] = array('name' => 'Расчет доставки до моего города', 'type' => 'edost', 'checked' => 1);
					else
						$result[] = array('name' => 'Расчет доставки eDost', 'type' => 'edost', 'checked' => 1);
				}
				if (PochtaRussia::on()) {
					$result[] = array('name' => 'Доставка "Почта России"', 'type' => 'PR', 'checked' => 1, 'price' => 0, 'totsumfree' => 0);
				}
				break;
		}
		return $result;
	}

	# получить список в html
	public function getLists($nameList, $req = '')
	{
		if (function_exists('class2005_getLists')) {
			return class2005_getLists($this, $nameList, $req); // своя функция
		} else {
			global $db, $catalogue, $setting, $currency, $cityid, $DOCUMENT_ROOT, $AUTH_USER_ID;
			$dataArr = $this->getSettingsList($nameList); # весь список
			$goodItems = array(); # чистый список
			$oneItem = 0; # один пункт
			$roboYes = 0; # есть робокасса
			$titleList = ($nameList == "delivery" ? getLangWord('cart_methDelivery', "Способы доставки") : ($nameList == "payment" ? getLangWord('cart_methPay', "Способы оплаты") : "")) . ($req ? "<span class='red'>*</span>" : ""); # заголовок блока
			$check = $nameList == 'delivery' && $_SESSION['cart']['delivery']['id'] > 0 ? $_SESSION['cart']['delivery']['id'] : ""; # id выбраного элемента
			if ($dataArr) {
				# формирует массив с актуальным списком
				foreach ($dataArr as $id => $item) {
					$id++;
					if (
						!$item['checked']
						|| $setting['targeting'] && is_array($item['targeting']) && $cityid && !isset($item['targeting'][$cityid])
					)
						continue; # пункт выключен или у пунтка указан таргетинг и выбран город, далее

					if ($setting['rkassaLogin'] && $setting['rkassaPass1'] && $setting['rkassaPass2'] && stristr($item['name'], "robokassa"))
						$roboYes = 1;

					# checked или нет
					$item['checked'] = $check && $check == $id ? 1 : 0;

					# замена текста
					if (
						!$item[text]
						&& (trim($item['name']) == 'Самовывоз'
							|| trim($item['name']) == 'самовывоз')
					) {
						if ($cityid)
							$somovyv_where = " AND citytarget like '%,{$cityid},%'"; // если выбран город, искать адрес в этом городе
						$item[text] = $db->get_var("select adres from Message2012 where Catalogue_ID = '$catalogue' AND Checked = 1 {$somovyv_where} order by Priority limit 0,1");
					}

					$goodItems[$id] = $item;
				}
				if ($goodItems) {
					# добавление в массив 'Выберите'
					if ((count($goodItems) == 1 && $nameList != 'payment') || (count($goodItems) == 1 && !$roboYes && $nameList == 'payment')) {
						$id = array_key_first($goodItems);
						if (!$id)
							$id = 0;
						$goodItems[$id][checked] = 1;
					} else {
						$goodItems[0] = array('name' => getLangWord("not_selected", 'не выбрано'), 'checked' => (!$check ? 1 : 0));
						ksort($goodItems);
					}
				}
			}

			if ($goodItems) {
				$edostChecked = $cdekChecked = false;
				switch ($this->typelist) {
					case 'cartlistype-1': // Список
						foreach ($goodItems as $id => $item) {
							if ($item['type'] == 'edost')
								$edostChecked = true;
							if ($item['type'] == 'cdek')
								$cdekChecked = true;
							if ($item['type'] == 'PR') {
								$PR = new PochtaRussia();
							}
							unset($data);
							unset($dopclass);

							if (
								stristr($item['name'], "(Физ.лицо)")
								|| $item['userType'] == 2
							) {
								$data[] = "data-optbody='1'";
								$item['name'] = trim(str_replace("(Физ.лицо)", "", $item['name']));
							}
							if (
								stristr($item['name'], "(Юр.лицо)")
								|| $item['userType'] == 3
							) {
								$data[] = "data-optbody='2'";
								$dopclass = "none";
								$item['name'] = trim(str_replace("(Юр.лицо)", "", $item['name']));
							}

							$data[] = "data-name='" . getLangWord($item[name]) . "'";
							$data[] = "data-type='" . ($item[type] ? $item[type] : '') . "'";
							$data[] = "data-price='" . ($item['price'] ? $item['price'] . " " . htmlspecialchars($currency[html], ENT_QUOTES) : "") . "'";
							$data[] = "data-text='" . ($setting['freedelivery'] && $nameList == "delivery" && $item[totsumfree] > 0 ? "при покупке на сумму от {$item[totsumfree]} руб. - доставка бесплатно " : "") . "" . ($item[text] ? getLangWord($item[text]) : "") . "'";
							if ($nameList == "delivery") {
								$data[] = "data-deliverytype='{$item['delivery_type']}'";
							}

							$items .= "<option value='{$id}' " . ($item[checked] ? "selected" : "") . " " . implode(" ", $data) . " class='{$dopclass}'>"
								. getLangWord($item[name])
								. "</option>";
						}

						if ($items) {
							$html = "<div class='method_{$nameList}'>
										<div class='blk_title f_{$nameList}'>{$titleList}</div>
										<div class='method_items'>
											<select class='select-style select-lists' name='f_" . $nameList . "'>{$items}</select>
										</div>
									</div>";
						}
						if ($cdekChecked) {
							$html .= "<div class='delivery-assist-blk cdek none-important'>";
							$html .= "<span class='cdek-post-name'>" . Cdek::getInstance()->getTexts()['deliveryNoSelect'] . "</span>";
							$html .= " <a href='#' class='cdek-selected-change' onclick='cdekStart(); return false;'>изменить</a>";
							$html .= "</div>";
						}

						if (isset($PR)) {
							if (isset($_SESSION['cart']['delivery']['assist']['description'])) {
								$PR->recalcDelivery();
								$PRDescriptions = $_SESSION['cart']['delivery']['assist']['description'];
							} else {
								$PRDescriptions = $PR->text['noSelectChooser'];
							}
							$html .= "<div class='delivery-assist-blk PR none-important'>
                                        <span class='PR-post-name'>{$PRDescriptions}</span> <a href='#' class='PR-selected-change' onclick='PothtaRussia.start(); return false;'>изменить</a>
                                     </div>";
						}

						if ($edostChecked) {
							$edostBody = "";
							if (file_exists($DOCUMENT_ROOT . "/template/class/2005/resurs/edostCities.json")) {
								$edostCities = json_decode(file_get_contents($DOCUMENT_ROOT . "/template/class/2005/resurs/edostCities.json"), true);
								if ($edostCities && is_array($edostCities)) {
									$edostCitiesList = "";
									foreach ($edostCities as $groupKey => $group) {
										if (!$group['options'])
											continue;
										$edostCitiesList .= "<optgroup label='{$group['grouptitle']}'>";
										foreach ($group['options'] as $cityKey => $city) {
											$edostCitiesList .= "<option value='{$city['value']}'>{$city['title']}</option>";
										}
										$edostCitiesList .= "</optgroup>";
									}
									$edostBody = "<div class='edost-cities-wrapper'>
                                                        <select name='edostCities' class='select-style select-search'>
                                                            <option value='' selected>Выберите город</option>
                                                            {$edostCitiesList}
                                                        </select>
                                                    </div>
                                                    <div class='edost-result'></div>";
								}
							}
							if (!$edostBody)
								$edostBody = "<span class='txt'>не получилось получить список доступных городов</span>";
							$html .= "<div class='delivery-assist-blk edost method_items none-important'>
                                        {$edostBody}
                                     </div>";
						}
						break;
					case 'cartlistype-2': // Стандартная корзина
						foreach ($goodItems as $id => $item) {
							unset($data);
							unset($dopclass);
							if (!trim($item[name]))
								continue;

							if (
								stristr($item['name'], "(Физ.лицо)")
								|| $item['userType'] == 2
							) {
								$data[] = "data-optbody='1'";
								$item['name'] = trim(str_replace("(Физ.лицо)", "", $item['name']));
							}
							if (
								stristr($item['name'], "(Юр.лицо)")
								|| $item['userType'] == 3
							) {
								$data[] = "data-optbody='2'";
								$dopclass = "none";
								$item['name'] = trim(str_replace("(Юр.лицо)", "", $item['name']));
							}
							$text_opis = ($setting['freedelivery'] && $nameList == "delivery" && $item[totsumfree] > 0 ? "при покупке на сумму от {$item[totsumfree]} руб. - доставка бесплатно<br>" : "") . "{$item[text]}";

							$text = "<div class='mi_rt_info'>
											<span class='sposob_text'>{$item[name]}</span>
											" . ($item['price'] ? "<span class='sposob_price'>- {$item['price']} {$currency[html]}</span>" : "") . "
										</div>
										" . ($text_opis ? "<div class='mi_rt_text'>{$text_opis}</div>" : "");
							$items .= "<div class='method_item {$dopclass} " . ($item['type'] == 'cdek' ? 'radio-cdek' : '') . "' " . implode(" ", $data) . ">"
								. bc_radio_standart("f_{$nameList}", $id, $text, $item['checked'], "data-type='{$item['type']}'")
								. ($item['type'] == 'cdek' ? "<div class='input-field-standart cdek none-important'>
																<input id='codePost' name='codePost' value='' maxlength='255' size='50' placeholder='Почтовый индекс*' type='text' class='inp'>
															</div>
															<div class='cdekDelType none-important'>
																<select id='cdekDelType' class='select-style select-lists' name='cdekDelType'></select>
															</div>" : '') . "</div>";
						}
						if ($items) {
							$html = "<div class='method_{$nameList}'>
											<div class='blk_title f_{$nameList}'>{$titleList}</div>
											<div class='method_items'>{$items}</div>
										</div>";
						}
						break;
				}

				if ($req && $html)
					$html = "<input type=hidden name='is_reqlist[]' value='f_" . $nameList . "'>{$html}"; # обязательный выбор
				return $html;
			}

			return "";
		}
	}

	# получить поле списка по ID
	public static function getListName($nameList, $listID, $field = '')
	{
		global $db, $catalogue, $setting;
		$dataArr = $setting['lists_' . $nameList];

		if ($nameList === "delivery" && Cdek::getInstance()->isOn()) {
			$dataArr[] = [
				'name' => 'Доставка СДЭК',
				'checked' => 1,
				'price' => 0,
				'totsumfree' => 0,
				'type' => 'cdek',
				'delivery_type' => 2,
			];
		}
		if ($nameList == "delivery" && $setting['edostCheck']) {
			$dataArr[] = ['name' => 'Расчет доставки eDost', 'checked' => 1, 'price' => 0, 'totsumfree' => 0, 'type' => 'edost'];
		}
		if ($nameList == "delivery" && PochtaRussia::on()) {
			$dataArr[] = array('name' => 'Доставка "Почта России"', 'checked' => 1, 'price' => 0, 'totsumfree' => 0, 'type' => 'PR');
		}
		if ($dataArr) {
			foreach ($dataArr as $id => $item) {
				$ii = $id; #Mir не выставлялся счет на оплату (сделал конструкцию схожей с this->getLists а то id разные получались) # разраб-2: ясно, спасибо
				$ii++;
				if (!$item['checked'])
					continue; #Mir
				if ($listID == $ii) {
					if ($field)
						return $item[$field];
					else
						return array_merge($item, ['sum' => $item['price'], 'id' => $ii]);
				}
			}
		}
		if ($field == "name")
			return getLangWord("not_selected", 'не выбрано');
		if ($field == "price")
			return 0;
	}

	/**
	 * Получить максимальный id из списка статусов заказов
	 * 
	 * @return int
	 */
	public function getOrderStatusListMaxId()
	{
		if (!$list = $this->getOrderStatusList())
			return 0;

		return max(array_keys($list));
	}

	/**
	 * Получить список статусов заказов
	 * 
	 * @return array
	 */
	public function getOrderStatusList()
	{
		if (!isset($this->orderStatusList))
			$this->setOrderStatusList();

		return $this->orderStatusList;
	}

	/**
	 * Устанавить список статусов заказов
	 * 
	 * @return void
	 */
	public function setOrderStatusList()
	{
		global $db, $setting;

		$this->orderStatusList = [];

		$sql = "SELECT `ShopOrderStatus_ID` AS id, 
					   `ShopOrderStatus_Name` AS name
				FROM `Classificator_ShopOrderStatus`
				WHERE `Checked` = 1
				ORDER BY `ShopOrderStatus_Priority`";

		foreach ($db->get_results($sql, ARRAY_A) ?: [] as $orderStatus) {
			$orderStatus['type'] = 'default';
			$this->orderStatusList[$orderStatus['id']] = $orderStatus;
		}

		if (!isset($setting['lists_order_status']))
			return;

		foreach ($setting['lists_order_status'] ?: [] as $orderStatus) {
			if ('default' === ($this->orderStatusList[$orderStatus['id']]['type'] ?? ''))
				continue;
			$orderStatus['type'] = 'custom';
			$this->orderStatusList[$orderStatus['id']] = $orderStatus;
		}
	}

	/**
	 * Установить сервис доставки
	 * 
	 * @param string $type тип сервиса
	 * @param array $parameters
	 * 
	 * @return void
	 */
	public function setServiceDelivery($type, $parameters = [])
	{
		// global $AUTH_USER_ID;
		// if ($AUTH_USER_ID == 4) {
		// 	var_dump($parameters, $_SESSION['cart']['delivery']);
		// }
		switch ($type) {
			case 'cdek':
				unset($_SESSION['cart']['delivery']['assist']);

				$basketSum = $_SESSION['cart']['totalSumDiscont'] ?: $_SESSION['cart']['totalsum'] ?: 0;

				$_SESSION['cart']['totaldelsum'] = $basketSum;
				$_SESSION['cart']['delivery']['sum'] = 0;
				$_SESSION['cart']['delivery']['sum_pay_after'] = 0;
				$_SESSION['cart']['delivery']['sum_result'] = 0;
				$_SESSION['cart']['delivery']['sum_free'] = 0;
				$_SESSION['cart']['delivery']['sum_freevisible'] = 0;

				$products = [];
				foreach ($_SESSION['cart']['items'] ?? [] as $item) {
					$productObj = Class2001::getItemById($item['id']);
					$package = ['count' => $item['count']];

					if ($productObj->ves)
						$package['weight'] = $productObj->ves;
					if ($productObj->length)
						$package['length'] = $productObj->length;
					if ($productObj->width)
						$package['width'] = $productObj->width;
					if ($productObj->height)
						$package['height'] = $productObj->height;

					$products[] = $package;
				}

				$cdek = Cdek::getInstance();

				try {
					$tariff = $cdek->calculateTariff(
						(new CdekCalculatorData())->setDeliveryCityCode($parameters['cityCode'])->setProducts($products),
						$parameters['tariffid']
					);
				} catch (Exception $e) {
					$_SESSION['cart']['delivery']['assist'] = [
						'type' => 'cdek',
						'tariffId' => $parameters['tariffid'],
						'cityCode' => $parameters['cityCode'],
						'error' => true,
						'description' => !empty($parameters['pvzCode']) ? $cdek->getTexts()['cantDEliveryPickup'] : $cdek->getTexts()['cantDeliveryCourier'],
					];

					if (!empty($parameters['pvzCode']))
						$_SESSION['cart']['delivery']['assist']['pvzCode'] = $parameters['pvzCode'];

					break;
				}

				$address = !empty($parameters['pvzCode']) ? $cdek->getPvz($parameters['pvzCode'], 'addressFull') : "Доставка курьером в город: <b>" . $cdek->getCity($parameters['cityCode'], 'name') . "</b>";

				$_SESSION['cart']['delivery']['assist'] = [
					'type' => 'cdek',
					'tariffId' => $parameters['tariffid'],
					'cityCode' => $parameters['cityCode'],
					'price' => $tariff['total_sum'],
					'description' => $address,
				];
				if (!empty($parameters['pvzCode']))
					$_SESSION['cart']['delivery']['assist']['pvzCode'] = $parameters['pvzCode'];

				if ($cdek->isUseSumInOrder()) {
					$_SESSION['cart']['delivery']['sum'] = $tariff['total_sum'];
					$_SESSION['cart']['delivery']['sum_result'] = $tariff['total_sum'];
				} else {
					$_SESSION['cart']['delivery']['sum_pay_after'] = $tariff['total_sum'];
					$_SESSION['cart']['delivery']['sum'] = 0;
				}
				$_SESSION['cart']['totaldelsum'] = $_SESSION['cart']['delivery']['sum_result'] + $basketSum;
				break;
			default:
				throw new Exception('Неизвестный тип сервиса доставки: ' . $type);
		}
	}

	# тестовый метод
	public function test()
	{
		global $setting;
		return $setting[itemlistsub];
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