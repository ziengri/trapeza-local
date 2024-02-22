<?php
class adminorders {
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
				case 'shopOrderStatus': return $this->shopOrderStatus();
				case 'statusPayment': return $this->statusPayment();
				case 'deleteOrder': return $this->deleteOrder();
				case 'editParamOrder': return $this->editParamOrder();
				case 'editOrder': return $this->editOrder();
                case 'searchItems': return $this->searchItems(securityForm($_POST['value']));
                case 'getproducthtml': return $this->getProductHtml(securityForm($_POST['id']));
			};
		} else {
			return encode_host($_SERVER[HTTP_REFERER])." | ".$_SERVER[HTTP_HOST];
		}
	}


	# Статус заказа
	private function shopOrderStatus() {
		global $db;
		$arrayValues = array();
		$id = intval($_POST['id']);
		$val = intval($_POST['val']);
		$isArray = intval($_POST['isArray']);
		if($id && $val){
			if($isArray){
				foreach ($id as $k => $v) {
					$arrayValues[$v] = $val[$k];
				}
			}else{
				$arrayValues[$id] = $val;
			}

			foreach ($arrayValues as $id => $val) {
				$orderStatus = $db->get_var("SELECT `ShopOrderStatus` FROM `Message2005` WHERE `Message_ID` = {$id}");
				if ($orderStatus != $val) {
					$db->query("UPDATE `Message2005` SET `ShopOrderStatus` = {$val} WHERE Message_ID = {$id}");
					orderStatusChangeAfter($id, $val, $orderStatus);
				}
			}

			echo json_encode(ARRAY(
		        "title" => "ОК",
		        "succes" =>  "Статус заказа изменен",
		        "reloadtab" => ($isArray || $_POST[reloadview] ? 1 : null),
		        "modal" => ($isArray ? "close" : null)
		    ));
		}
	}

	# Статус оплаты заказа
	private function statusPayment() {
		global $db;
		$id = intval($_POST[id]);
		$val = intval($_POST[val]);
		if($id && $val){
			$db->query("UPDATE Message2005 SET statusOplaty = '$val' WHERE Message_ID = '$id' AND Catalogue_ID = '$this->catID'");
			echo json_encode(ARRAY(
		        "title" => "ОК",
		        "succes" =>  "Статус оплаты заказа изменен",
		        "reloadtab" => ($_POST[reloadview] ? 1 : null),
		    ));
		}
	}

	# Удалить заказ
	private function deleteOrder() {
		global $db;
		$id = securityForm($_GET[id]);
		if($id){
			$nc_core = nc_Core::get_object();
			$nc_core->message->delete_by_id($id, 2005);
			echo json_encode(ARRAY(
		        "title" => "ОК",
		        "succes" =>  "Заказы удалены",
		        "reloadtab" => 1,
		        "modal" => "close"
		    ));
		}
	}

	# Удалить заказ
	private function editParamOrder() {
		global $db;
		$id = securityForm($_POST[id]);
		$name = trim(securityForm($_POST[name]));
		$value = securityForm($_POST[value]);
		if($id && $name){
			$customfJson = $db->get_var("SELECT customf FROM Message2005 WHERE Message_ID = '{$id}' AND Catalogue_ID = '{$this->catID}'");

			$customf = orderArray($customfJson);

			if(!$customf[$name]) $customf[$name] = array();
			$customf[$name][value] = $value;

			$db->query("UPDATE Message2005 SET customf = '".json_encode($customf)."' WHERE Message_ID = '{$id}' AND Catalogue_ID = '{$this->catID}'");

			echo json_encode(ARRAY(
		        "title" => "ОК",
		        "succes" =>  "Поле изменено"
		    ));
		}
	}

	private function editOrder()
    {
        if (function_exists('bc_adminorders_editOrder')) {
            return bc_adminorders_editOrder($this);
        } else {
            global $setting;
            $order = $_SESSION['editOrder'];
            $order['totalSumDiscont'] = 0;
            $result = array();
            switch ($_POST['event']) {
                case 'addItem':
                    $id = securityForm($_POST['id']);
                    if (!isset($order['items'][$id])) {
                        $item = Class2001::getItemById($id);
                        if ($item) {
                            $valuta = $setting['lists_texts']['rubl_char']['checked'] ? $setting['lists_texts']['rubl_char']['name'] : "руб.";
                            $order['items'][$id] = array(
                                'id' => $id,
                                'name' => $item->nameFull,
                                'sub' => $item->sub,
                                'count' => 1,
                                'art' => $item->art,
                                'edizm' => $item->edizm,
                                'price' => $item->price,
                                'sum' => $item->price,
                                'variant' => $_POST['variant']
                            );
                            $result['success'] = 1;
                            $result['result'] = "<tr data-id='{$id}' class='ob-product'>
                                <td class='ob-tablePhoto'>{$item->photoMain}</td>
                                <td class='ob-tableName'>
                                    <div class='ob-sun-name ws'>
                                        <a href='{$item->fullLink}' target='_blank'>{$item->nameFull}".($order['items'][$id]['variant'] ? " ({$order['items'][$id]['variant']})" : null)."</a>
                                        ".($item->art ? "<span class='ob-sun-art'>{$item->art}</span>" : null)."
                                    </div>
                                </td>
                                <td class='ob-tablePrice'>
                                    ".($item->price ? "<div class='ob-sun-price'>
                                                            <span class='value'>{$item->price}</span>
                                                            {$valuta}
                                                        </div>" : null)."
                                <td class='ob-tableCount'>
                                    <div class='ob-counter cart-btn'>
                                        <span class='btn_down'></span>
                                        <input class='input-counter' data-name='item{$id}' name='count' type='number' min='1' value='1' data-stock='{$item->stock}'>
                                        <span class='btn_up'></span>
                                    </div>
                                </td>
                                <td class='ob-tableSum'>
                                    <span class='value'>{$item->price}</span>
                                    {$valuta}
                                </td>
                                <td class='ob-tableDel'>
                                    <button type='button' class='delete-item'></button>
                                </td>
                            </tr>";
                        }
                    }
                    if (!$result['success']) $result['success'] = 0;
                    break;
                case 'changeItem':
                    if (isset($order['items'][$_POST['id']])) {
                        $result['itemCount'] = $order['items'][$_POST['id']]['count'] = $_POST['count'];
                        $result['itemSum'] = $order['items'][$_POST['id']]['sum'] = $_POST['count'] * $order['items'][$_POST['id']]['price'];
                        $result['success'] = 1;
                    }
                    break;
                case 'deleteItem':
                    if (isset($order['items'][$_POST['id']])) {
                        unset($order['items'][$_POST['id']]);
                        $result['success'] = 1;
                    }
                    break;
                default:

                    break;
            }

            if (isset($order['items'])
                && is_array($order['items'])
                && count($order['items'])
            ) {
                foreach ($order['items'] as $item) {
                    $order['totalSumDiscont'] += $item['sum'];
                }
                $order['totaldelsum'] = $order['delivery']['sum_result'] + $order['totalSumDiscont'];
            } else {
                $order['items'] = array();
            }


            $result['totsumdiscont'] = $order['totalSumDiscont'];
            $result['deliversum'] = $order['delivery']['sum_result'];
            $result['totdelsum'] = $order['totaldelsum'];

            $_SESSION['editOrder'] = $order;

            return json_encode($result);
        }
    }

	    # поиск ттоваров с сайта
		private function searchItems($value)
		{
			global $db, $setting;
			$result = "";
			if ($value) {
				$ignore = '';
				foreach ($_SESSION['editOrder']['items'] as $item) {
					$ignore .= ($ignore ? "," : null).$item['id'];
				}
				$queryGroup = "";
				$queryWhere = getFindQuery($value, '', '', '').($ignore ? " AND Message_ID NOT IN ({$ignore}) " : null);
	
				if ($setting['groupItem']) {
					$queryWhere .= " AND (variablenameSide IS NULL OR variablenameSide = '' OR variablenameSide = 0) ";
					$queryGroup = 'name, Subdivision_ID';
				}
	
				if ($queryGroup) $queryGroup = " GROUP BY {$queryGroup} ";
	
				$idis = $db->get_col("SELECT Message_ID FROM Message2001
									  WHERE Catalogue_ID = {$this->catID}
									  AND Checked = 1
									  AND {$queryWhere}
									  {$queryGroup}
									  ORDER BY Priority");
				if ($idis) {
					foreach ($idis as $id) {
						$result .= $this->getProductHtml($id);
					}
				}
				if (!$result) $result = "<p>По запросу \"{$value}\" результатов не найдено! SELECT Message_ID FROM Message2001
				WHERE Catalogue_ID = {$this->catID}
				AND Checked = 1
				AND {$queryWhere}
				{$queryGroup}
				ORDER BY Priority</p>";
			}
			return json_encode(array('success' => 1, 'result' => $result));
		}
	
		private function getProductHtml($id)
		{
			$item = Class2001::getItemById($id);
			if (!$item) return null;
			return "<div class='ob-serach-product' data-id='{$id}'>
						{$item->photoMain}
						<span class='blk_name'>{$item->nameFull}</span>
						<div class='blk_art'>{$item->artHtml}</div>
						".($item->variantsNameHtml ? "<div class='blk_variableName'>{$item->variantsNameHtml}</div>": "")."
						<div class='blk_priceblock'>
							<meta itemprop='priceCurrency' content='RUB' />"
							.($item->lastPrice ? "<div class='blk_last last_price'>{$item->lastPriceHtml}</div>" : "").
							"<div class='blk_price normal_price ".($item->lastPrice ? "new_price" : "")."'>{$item->pricePrefix} {$item->priceHtml}</div>
						</div>
						<button type='button' class='add-item-btn' data-id='{$id}' onclick=\"editOrder('addItem', this)\">Добавить</button>
					</div>";
		}


}
?>
