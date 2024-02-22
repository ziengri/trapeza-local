<?php

/**Добавление надписи в корзине.

 */
 function  class2005_getCart($self, $type = "")
	{
		
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
				$cartItems = $self->getItems();
				$bigcart[] = $cartItems['table'];

				// Минимальный заказ
				if ($setting['minOrderSum']) {
					$bigcart[] = "<div class='minOrderFail warnText " . ($cartItems['totalSum'] > $setting['minOrderSum'] ? "none" : NULL) . "'>
									<b>Внимание!</b> Минимальный заказ от <span class='sum'>{$setting['minOrderSum']}</span> {$currency['html']}
								</div>";
				}

				$delivery = $self->getLists('delivery', 'req');
				$payment = $self->getLists('payment', 'req');
				$freeDelivery = $self->getFreeDelivery();

				$data = $setting['cartForm'] ? orderArray($setting['cartForm']) : "";

				#значения для заполнения полей формы
				$defaulValArr = array(
					'name' 	  => trim("{$current_user['fam']} {$current_user['ForumName']} {$current_user['otch']}"),
					'phone'   => $current_user['phone'],
					'email'   => $current_user['Email'],
					'city'	  => $current_user['city'],
					'address' => $current_user['org'],
					'company' => $current_user['company'],
					'inn' 	  => $current_user['inn'],
					'kpp' 	  => $current_user['kpp']
				);
				$formRem = $current_user["User_ID"] ? orderArray($current_user["formRem"]) : array();
				$deliveryItem = $_SESSION['cart']['delivery']['id'] > 0 ? $self->getListName("delivery", $_SESSION['cart']['delivery']['id']) : null;
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
					if ($param['required']['value']) $hiddenRequired .= "<input name='req_customf[]' type='hidden' value='{$param['name']['value']}'>";
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
							foreach ($param["selectGroup"]["values"] as $i => $select)
								$options .= "<option value=" . getLangWord($select['value']) . " " . ($defaulVal && $defaulVal == $select['value'] ? "selected" : "") . ">" . getLangWord($select['value']) . "</option>";
							$fields = "<div class='person_line person_type_select person_{$param['name']['value']} {$dopclass}' {$filedattr}>" . bc_select_standart($customName, $options, $title, "data-oneline class='select-style'", $param['required']['value']) . "</div>";
							break;
						case 'checkbox':
							$checkboxs = "";
							foreach ($param["checkboxGroup"]["values"] as $i => $checkbox) $checkboxs .= "<div class='person_line {$param['name']['value']}_{$i}'>" . bc_checkbox_standart($customName . "[]", $checkbox["value"], $checkbox["value"]) . "</div>";
							$fields = "<div class='person_line person_type_checkbox person_{$param['name']['value']} {$dopclass}' {$filedattr}><div class='input-fields-cart input-oneline'>" . bc_label_standart($title, "", $param['required']['value']) . "<div class='field-second'>{$checkboxs}</div></div></div>";
							break;
						case 'radio':
							$radios = "";
							foreach ($param["radioGroup"]["values"] as $i => $radio) $radios .= "<div class='person_line {$param['name']['value']}_{$i}'>" . bc_radio_standart($customName,  $radio["value"],  $radio["value"], ($i == 0 ? 1 : 0)) . "</div>";
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
									<input name='cc' type='hidden' value='{$self->cc}'>
									<input name='sub' type='hidden' value='{$self->sub}'>
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
												" . bc_radio_standart("f_usertype", 0,  getLangWord("cart_individual", "Физ.лицо"), 1, "data-opt='1'") . "
												" . bc_radio_standart("f_usertype", 1,  getLangWord("cart_entity", "Юр.лицо"), 0, "data-opt='2'") . "
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
					<div class='order-right {$self->typelist}'>
						<div class='basket_blks'>
							{$delivery}
							{$freeDelivery}
							{$payment}
							<div class='total_blk'>
								" . ($delivery ? "<div class='tot_item_all'>
                                <div class='attention'>
                                Перед оплатой счета дождитесь подтверждения заказа менеджером!
                               </div>
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
				$bigcart[] = "<p class='no-obj'>" . getLangWord('cart_empty', 'Ваша корзина пуста') . "</p>";
			}

			return implode("", $bigcart);
		}
	
