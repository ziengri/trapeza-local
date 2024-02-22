<?



define('ZONE_LEFT_ID', 2);
define('ZONE_BEFORE_ID', 3);
define('ZONE_CONTENT_ID', 4);
define('ZONE_AFTER_ID', 5);
define('NO_LINE_ZONE_ARRAY', [ZONE_LEFT_ID, ZONE_BEFORE_ID, ZONE_CONTENT_ID, ZONE_AFTER_ID]);

# Заполнение массива блоками / заполнение jsonparam
function getblocksarr($jsnp = 0)
{
	if (function_exists('function_getblocksarr')) {
		return function_getblocksarr($jsnp); // своя функция
	} else {
		global $current_sub, $AUTH_USER_ID, $zoneblocks, $blockArr, $blockKey, $jsonparam, $deviceID, $db, $catalogue, $mainpage, $sub, $widgetArr, $setting, $nav_templ_0, $nav_templ_0_drop, $nav_templ_0_drop_drop, $nav_templ_2, $nav_templ_2_drop, $nav_templ_2_drop_drop, $nav_templ_4, $nav_templ_4_drop, $nav_templ_4_drop_drop, $nav_templ_20, $nav_templ_20_drop, $nav_templ_20_drop_drop, $nav_templ_4_drop_drop_img, $nav_templ_40, $nav_templ_40_drop, $nav_templ_40_drop_drop, $nav_templ_41, $nav_templ_41_drop, $nav_templ_41_drop_drop, $nav_mob, $nav_templ_30, $nav_templ_31, $nav_templ_0_drop_drop_img, $nav_templ_40_drop_drop_img, $authInBlock, $plugins, $cityid, $current_cc, $action, $ignore_check, $nav_templ_0_img;
		global $nav_templ_42, $nav_templ_42_drop, $nav_templ_42_drop_drop, $isPgSpeed, $parent_sub_tree;
		
		if (!$catalogue && $jsnp) $catalogue = $jsnp['id'];
		if (!$sub && $jsnp) $sub = $jsnp['sub'];

		$requestUrlInfo = new SplFileInfo($_SERVER['REQUEST_URI']);
		$pageIsPhpScript = $requestUrlInfo->getExtension() == 'php';

		$bl_id = $db->get_row("select Subdivision_ID as sub, Sub_Class_ID as cc from Sub_Class where Class_ID = '2016' AND Catalogue_ID='$catalogue' LIMIT 0,1", ARRAY_A);

		$parentSubSql = [];
		foreach ($parent_sub_tree as $parent_sub) {
			if (isset($parent_sub['Subdivision_ID']) && $parent_sub['Subdivision_ID'] != $sub) {
				$parentSubSql[] = $parent_sub['Subdivision_ID'];
			}
		}


		$where = "block.`Checked` = 1
					AND block.`Subdivision_ID` = '{$bl_id['sub']}'
					AND EXISTS (
						SELECT *
						FROM Showing_Blocks AS showB
						WHERE block.`Message_ID` = showB.`Block_ID`
							AND (
								showB.`Subdivision_ID` = {$sub} " . (!empty($parentSubSql) ? "OR showB.`Subdivision_ID` IN (" . implode(',', $parentSubSql) . ") AND showB.`extends` = 1" : null) . " 
							)
					) != block.`fixblock`";

		if ($setting['targeting'] && ($cityid >= 0 || !$cityid)) {
			if (!isset($cityid)) $cityid = 9999;
			$where .= " AND (block.citytarget like '%,{$cityid},%' OR block.citytarget IS null OR block.citytarget = '' OR block.citytarget = ',,')";
		}

		$where = getLangQuery($where, 'block');

		$selectBlsql = "SELECT
							block.`Message_ID`, 
							block.`Subdivision_ID`, 
							block.`Sub_Class_ID`, 
							block.`Checked`, 
							block.`Priority`, 
							block.`notitle`, 
							block.`width`, 
							block.`col`,
							block.`pos`, 
							block.`name`, 
							block.`nolink`, 
							block.`recnum`, 
							block.`rand`,
							block.`substr`, 
							block.`bgimg`, 
							block.`sub`, 
							block.`cc`, 
							block.`msg`, 
							block.`ctpl`,
							block.`noindex`,
							block.`height`,
							block.`inmob`,
							block.`iscache`,
							block.`cache`,
							block.`text`,
							block.`no_in_full_obj`,
							block.`cachetime`, 
							block.`lastcache`, 
							block.`cssclass`, 
							block.`vars`, 
							block.`padding`,
							block.`phpset`, 
							block.`settings`, 
							block.`block_id`
						FROM
							Message2016 as block
						WHERE 
							{$where}
						ORDER BY 
							block.`Priority`";
							


		$blocks = $db->get_results($selectBlsql, 'ARRAY_A');
		
				

		if ($blocks) {
			foreach ($blocks as $b) { // начало блока
				$dopclass = $dopclass2 = $blockHtml = $blockHtmlCont = $classNum = $block = $name = $col = $templ = $pos = $settings = $phpset = $contset = $contsetclass = $menuHtml = $blklr = null;

				# ЭКСПЕРИМЕНТАЛЬНО не выводить блоки в мобилкке, если он только для десктопа (нужно решить вопрос с планшетами, они определеются как мобильные)
				$siteArrNoBlock = array(970);
				if (in_array($catalogue, $siteArrNoBlock) && is_mobile() && $b[inmob] == 2) continue;

				# не выводить блок если он не для мобильного приложения и это приложение
				if (($b['inmob'] == 2 || $b['inmob'] == 3) && $_SESSION['deviceID']) continue;
				# не выводить блок если он для мобильного приложения, но это не приложение
				if ($b['inmob'] == 4 && !$_SESSION['deviceID']) continue;

				if ($b['no_in_full_obj'] && $action == 'full') continue;

				$col = ($b['col'] > 0 ? $b['col'] : 'banner');

				if ($b['recnum'] > 50) $b['recnum'] = 50;

				if ($pageIsPhpScript && in_array($col, NO_LINE_ZONE_ARRAY)) continue;

				if ($b['phpset']) { // настройки php
					$phpset = orderArray($b['phpset']);
					if ($phpset['contsetclass']) {
						$contset = $phpset['contsetclass'];
						foreach ($phpset['contsetclass'] as $k => $v) {
							$contsetclass .= "&{$k}={$v}";
						}
						//if (!$contset[nc_ctpl]) $contsetclass .= "&nc_ctpl=1";
					}
				}
				$settings = orderArray($b['settings']); // настройки визуальные

				if (!$b['iscache'] || !$b['cache'] || ($b['iscache'] && $b['cachetime'] && $b['lastcache'] < time())) { // если нет кэша или не кэшируется или время кэша вышло
					$params = subParams($b['sub']);
					if ($b['cc'] > 0) $classNum = $db->get_var("select Class_ID from Sub_Class where Sub_Class_ID = '" . $b['cc'] . "'");

					$name = ($b['name'] ? $b['name'] : $params['name']);
					$pos = $b['pos'];

					// классы start end
					$grid = ($b['col'] == 2 ? '3' : (in_array($b['col'], array(3, 4, 5)) ? ($setting['htmlShema'] == 1 || $setting['htmlShema'] == 2 ? '9' : '6') : '12'));
					if ($zoneid != $b['col']) {
						$zoneid = $b['col'];
						$zonewnow = 0;
					}
					if ($zonewnow == 0 || ($zonewnow + $b[width]) > $grid) {
						$blklr .= "start ";
						$zonewnow = 0;
					}
					if (($zonewnow + $b['width']) <= $grid) {
						$zonewnow += $b['width'];
						if ($zonewnow == $grid) {
							$blklr .= "end ";
							$zonewnow = 0;
						}
					}

					$dopclass .= (!$_SESSION['cart']['items'] && $classNum == 2005 && $contset['minicarttype'] > 2 ? " none" : null);
					$dopclass2 .= ($classNum == 2005 ? " smallcart" . ($setting['minicart'] > 0 ? " smallcart_type{$setting['minicart']}" : null) : null);
					$dopclass .= ($contset['minicartbord'] == 1 ? " basket_mini_open_border_solid" : null);
					$dopclass .= ($contset['minicartbord'] == 2 ? " basket_mini_open_border_dashed" : null);
					$dopclass .= ($contset['minicartbg'] ? " basket_mini_open_bg" : null);

					$dopclass .= " type-block-" . $phpset['contenttype'];
					$dopclass .= " menu-type-" . $contset['menutpl'];
					$dopclass .= (($phpset['contenttype'] && $contset['menutpl'] == 1) ? " mainmenu" : null);
					$dopclass .= (($phpset['contenttype'] && $contset['menutpl'] >= 20) ? " submenublock" : null);
					$dopclass .= ($contset['menutpl'] > 0 ? " thismenu submenutype" . $contset['menutpl'] : null);
					$dopclass .= ($b['ctpl'] == 2019 ? " searchblock" : null);
					$dopclass .= ($b['inmob'] == 1 ? " mobyes" : null);
					$dopclass .= ($b['inmob'] == 2 || $contset['menutpl'] == 2 || $contset['menutpl'] == 20 ? " nomob" : null);
					$dopclass .= ($b['cssclass'] ? " {$b['cssclass']}" : null);
					$dopclass .= ($b['bgimg'] ? ' imgbg' : null);
					$dopclass .= ($classNum ? " class{$classNum}" : null);
					$dopclass .= ($contset['nc_ctpl'] ? " sdfgdf nc" . $contset['nc_ctpl'] : null);
					$dopclass .= ($b['notitle'] ? " notitle" : null);
					$dopclass .= ($b['height'] && $b['height'] != '100%' ? " heightFix" : null);
					$dopclass .= ($b['height'] == '100%' ? " h100" : null);
					$dopclass .= ($b['margin'] > 0 ? " nomarg" . $b['margin'] : null);
					$dopclass .= ($b['msg'] > 0 ? " msg" . $b['msg'] : null);
					$dopclass .= ($settings['borderwidth'] && $settings['bordercolor'] ? " blk_padding" : null); // bg of header and body is deleted (ломает поиск)
					$dopclass .= ($b['padding'] && (!$settings['headbg'] && (!$settings['borderwidth'] || !$settings['bordercolor']) && !$settings['bg']) ? " blk_nomarg_head" : null);
					$dopclass .= ($b['padding'] ? " blk_nomarg_cont" : null);
					$dopclass .= (!(($settings['borderwidth'] && $settings['bordercolor']) || $settings['bg']) ? " blk_nomarg_cont_lr_b" : null);
					$dopclass .= (!(($settings['borderwidth'] && $settings['bordercolor']) || $settings['bg'] || $settings['headbg']) ? " blk_nomarg_cont_lr_h" : null);
					$dopclass .= ($blklr ? " {$blklr}" : null);

					if (!$jsnp) {
						/* КОНТЕНТ БЛОКА */
						if ($b['text']) {  // содершимое - текст
							# animate
							$animate_text = "";
							$animate_delay = 0;
							if ($phpset['contsetclass']['animate'] && $phpset['contsetclass']['animate_text']) {
								if ($phpset['contsetclass']['animate_title']) $animate_delay = 0.16;
								$animate_text = $phpset['contsetclass']['animate_text'];
							}

							$blockHtmlCont .= "<div class='blockText txt " . ($animate_text ? "wow {$animate_text}" : "") . "' " . ($animate_delay ? "data-wow-delay='" . str_replace(",", ".", $animate_delay) . "s'" : "") . ">" . strtr(replace_lang(\Korzilla\Replacer::replaceText(textTargeting($b['text']))), $widgetArr) . "</div>";
						}

						if ($b['sub'] > 0 && $b['cc'] > 0 && $phpset['contenttype'] == 1) { // содершимое - из раздела
							if ($params['name']) {
								$blockHtmlCont .= "\n\n<!-- параметры width={$b['width']}&vars={$b['vars']}&ssub={$sub}&tsub={$b['sub']}&tcc={$b['cc']}&msg={$b['msg']}&mesid={$b['Message_ID']}&block_id={$b['block_id']}&isTitle=1&recNum={$b['recnum']}&rand={$b['rand']}&link={$params['url']}&name={$name}" . ($b['notitle'] ? "&notitle=1" : null) . "&substr={$b['substr']}" . ($b['noindex'] ? "&noindex=1" : "") . $contsetclass." -->\n\n";
								$blockHtmlCont .= strtr(nc_objects_list($b['sub'], $b['cc'], "&width={$b['width']}&vars={$b['vars']}&ssub={$sub}&tsub={$b['sub']}&tcc={$b['cc']}&msg={$b['msg']}&mesid={$b['Message_ID']}&block_id={$b['block_id']}&isTitle=1&recNum={$b['recnum']}&rand={$b['rand']}&link={$params['url']}&name={$name}" . ($b['notitle'] ? "&notitle=1" : null) . "&substr={$b['substr']}" . ($b['noindex'] ? "&noindex=1" : "") . $contsetclass, 1, false), $widgetArr);
							} else {
								// выключение блока
								$db->query("update Message2016 set Checked = 0 where block_id = '{$b['block_id']}' AND Catalogue_ID = '$catalogue'");
							}
						}
						if ($contset['menutpl'] > 0 && $phpset['contenttype'] == 2) { // содержимое - меню
							$sortsubArr = array(1 => "Subdivision_Name",);
							$templ = $contset['menutpl'];

							if ($templ == 31 || $templ == 30) {
								if ($templ == 30) $contset['list'] = 1;
								// Вывод меню плитки через нашу функцию
								$menuHtml = catalogCategory($b['sub'], $contset);
							} else {
								if ($templ == 1) $templ = '0';
								# шаблон с выподашкой

								$levelPolyfill = [0 => 0, 1 => 1, 2 => 2, 3 => 2];

								if ($contset['dropmenu'] == 3 && $templ == '0') $templ .= '_img';

								$name_nav = ${'nav_templ_' . $templ};
								if ($contset['sortsub'] > 0) $name_nav[0]['sortby'] = $sortsubArr[$contset['sortsub']];
						
								// вызять меню
								if ($templ == '42') {
									if ($current_sub['Parent_Sub_ID'] == 0) continue;

									$sub_extension = getExtensionSub($current_sub['Subdivision_ID']);
							
									if (!in_array($b['sub'], $sub_extension)) continue;
									$menuHtml = kz_browse_sub((int) $current_sub['Parent_Sub_ID'], $name_nav, 0, '', (int) ($levelPolyfill[$contset['dropmenu']] ?: 0));
							
								} else {
									$menuHtml = kz_browse_sub((int) $b['sub'], $name_nav, 0, '', (int) ($levelPolyfill[$contset['dropmenu']] ?: 0));
								}
							

								if ($contset['showicon']) $menuHtml = str_replace("menu-img-no", "menu-img", $menuHtml);
								if ($contset['devidertpl'] == 1) $menuHtml = str_replace("menu-dashed-no", "menu-dashed", $menuHtml);
								if ($contset['devidertpl'] == 2) $menuHtml = str_replace("menu-decoration-no", "menu-decoration", $menuHtml);
								if ($contset['punktwidth100']) $menuHtml = str_replace("nowidth100", "elwidth100", $menuHtml);
								if ($contset['itemsinmenu']) {
									if (!$menuHtml) {
										$menuHtml = "<!--<!--itmscl {$params['url']}-->--><!--itms {$params['url']}-->";
										$menuHtml = itemsinmenu($menuHtml, 1);
									} else {
										$menuHtml = itemsinmenu($menuHtml);
									}
								} else {
									$menuHtml = itemsinmenu($menuHtml, '', 1);
								}

								if ($contset['showicon']) $menuHtml = str_replace("menu-img-no", "menu-img", $menuHtml);
								if ($contset['devidertpl'] == 1) $menuHtml = str_replace("menu-dashed-no", "menu-dashed", $menuHtml);
								if ($contset['devidertpl'] == 2) $menuHtml = str_replace("menu-decoration-no", "menu-decoration", $menuHtml);
								if ($contset['punktwidth100']) $menuHtml = str_replace("nowidth100", "elwidth100", $menuHtml);
								if ($contset['itemsinmenu']) {
									if (!$menuHtml) {
										$menuHtml = "<!--<!--itmscl {$params['url']}-->--><!--itms {$params['url']}-->";
										$menuHtml = itemsinmenu($menuHtml, 1);
									} else {
										$menuHtml = itemsinmenu($menuHtml);
									}
								} else {
									$menuHtml = itemsinmenu($menuHtml, '', 1);
								}
								
								$menuHtml = str_replace("data-o='1'", "target='_blank'", $menuHtml);
								$menuHtml = preg_replace(
									"/<span class='menu-40-second-img none'>\s{0,}<img src='\/images\/nophoto\.png' .+?>\s{0,}<\/span>/mu",
									'', $menuHtml
								);
								$menuHtml = preg_replace(
									"/<span class='menu_img_top'>\s{0,}<img src='\/images\/nophoto\.png' .+?>\s{0,}<\/span>/mu",
									'',
									$menuHtml
								);
								$menuHtml = preg_replace(
									"/<span class='menu_img'>\s{0,}<img src='\/images\/nophoto\.png' .+?>\s{0,}<\/span>/mu", 
									'', 
									$menuHtml
								);
								$menuHtml = preg_replace(
									"/<span class='foot-menu-img'>\s{0,}<img src='\/images\/nophoto\.png' .+?>\s{0,}<\/span>/mu", 
									'', 
									$menuHtml
								);


								# html выподающего меню - 40
								if ($contset['menutpl'] == '40' || $contset['menutpl'] == '41') {
									$menuHtml = $b['sub'] > 0 ? "<div class='menu-button " . ($contset['menubtnclick'] ? "menu-button-click" : "") . "'><div class='menu-button-head icons i_typecat3'><span>" . getLangWord("lang_blk_" . $b['block_id'], $b['name']) . "</span></div><div class='menu-button-body'>{$menuHtml}</div></div>" : "";
									if ($contset['menutpl'] == '41') $plugins['js']["packery.pkgd.min"] = 1;
								}
							}

							$blockHtmlCont .= $menuHtml;
						}

						if ($phpset['contenttype'] == 3) { // содержимое - контакты
							$contactsBlock = smallcontacts($contset);
							$blockHtmlCont .= $contactsBlock;
							if (stristr($contactsBlock, "i_user2")) $authInBlock = 1;
						}
						if ($phpset['contenttype'] == 4 && !$isPgSpeed) { // содержимое - copyright
							$blockHtmlCont .= getcoporight($contset);
						}
						if ($phpset['contenttype'] == 5 && !$isPgSpeed) { // содержимое - разработчик
							$sites = array(749, 750);
							if (in_array($catalogue, $sites)) {
								$blockDev = getdev($contset);
								if ($b['noindex']) {
									// $blockDev = preg_replace("/(<a[\d\D]*(?<=<\/a>))/", "<!--noindex--> $1 <!--/noindex-->", $blockDev);
									$blockDev = preg_replace("/(href=\'[\d\D]*\')/U", "$1 rel='nofollow'", $blockDev);
								}
								$blockHtmlCont .= $blockDev;
							} else {
								$blockHtmlCont .= getdev($contset);
							}
						}
						if ($phpset['contenttype'] == 6) { // модули
							$blockHtmlCont .= checkModule('get', $contset['module'], $b[block_id]);
						}

						if ($phpset['contenttype'] == 7) { // хлеб
							$blockHtmlCont .= getXleb(array("type" => $settings['xlebtype'] ?? 2));
						}
						/* END КОНТЕНТ БЛОКА */
					}

					// Классы блока
					$thisBlkClass = "blocks {$dopclass2} grid_{$b['width']} {$dopclass}";
					// Массив настроек jsonpar
					$jsonparam['blocks'][$b['block_id']]['class'] = $thisBlkClass;

					if (!$jsnp) {
						if ((trim($blockHtmlCont) && ($b['sub'] > 0 && $b['cc'] > 0 && $phpset['contenttype'] == 1)) || (trim($blockHtmlCont) && $phpset['contenttype'] == 7) || ($phpset['contenttype'] != 1 && $phpset['contenttype'] != 7)) { // показ блока, если в нем есть какой-то контент (разела)
							if ($b['noindex']) $blockHtml .= "<!--noindex-->";
							if (!$b['noblock']) {

								$blockHtml .= "<section class='{$thisBlkClass}' data-name='{$name}' data-prior='" . $b['Priority'] . "' data-blockid='" . $b['block_id'] . "' id='block" . $b['block_id'] . "' data-width='" . $b['width'] . "' data-sub='" . $b['Subdivision_ID'] . "' data-admid='" . $b['Message_ID'] . "' data-cc='" . $b['Sub_Class_ID'] . "' " . ($bitcat ? "data-editlink='{$editLink}'" : "") . ">";

								if (!$b['notitle'] && $name) { // заголовок не скрыт
									# animate
									$animate_title = "";
									if ($phpset['contsetclass']['animate'] && $phpset['contsetclass']['animate_title']) {
										$animate_title = $phpset['contsetclass']['animate_title'];
									}

									$blockHtml .= "<header class='blk_head " . ($animate_title ? "wow {$animate_title}" : "") . "'>
														<div class='h2'>
															" . (!$b['notitle'] ? (!$b['nolink'] ? "<a href='" . ($params['exturl'] ?: $params['url']) . "'>" : null)
										. \Korzilla\Replacer::replaceText(getLangWord("lang_blk_{$b['block_id']}", $name))
										. (!$b['nolink'] ? "</a>" : null) . "" : null) . "
														</div>
													</header>";
								}
								$blockHtml .= "<article class='cb blk_body'><div class='blk_body_wrap'>";
							} else {
								$blockHtml .= "<div class='{$dopclass}'>";
							}
							$blockHtml .= $blockHtmlCont;

							if (!$b['noblock']) $blockHtml .= "</div></article></section>";
							else $blockHtml .= "</div>";
							if ($b['noindex']) $blockHtml .= "<!--/noindex-->";

							// кэшируем блок
							if (!$AUTH_USER_ID && (($b['iscache'] && !$b['cache']) || ($b['iscache'] && $b['cachetime'] && $b['lastcache'] < time()))) {
								$db->query("update Message2016 set cache = '" . addslashes($blockHtml) . "' where Message_ID = '" . $b['Message_ID'] . "'");
								if ($b['iscache'] && $b['cachetime']) $db->query("update Message2016 set lastcache = '" . (time() + $b['cachetime'] * 60) . "' where Message_ID = '" . $b['Message_ID'] . "'");
							}
							$blockHtml .= "<!-- /not cache " . $b['block_id'] . " -->";
						}
					}
				} else { // если кэш атуален
					$blockHtml .= $b['cache'] . "<!-- /is cache " . $b['block_id'] . " -->";
				}

				if (strstr($block, 'data-effect')) $slider = 1; // есть слайдеры, подключить js
				$zoneblocks[$col] .= $blockHtml;
			}
		} // конец блока
		if (!$zoneblocks['banner']) $zoneblocks['banner'] = "<div id=noslider></div>"; // если нет банера

		return ($jsnp ? $jsonparam['blocks'] : $zoneblocks);
	}
}





# Заполнение массива зонами / заполнение jsonparam
function getzonearr($zoneblocks, $jsnp = 0)
{
	global $catalogue, $db, $jsonparam, $mobileMenu, $HTTP_FILES_PATH, $bitcat, $AUTH_USER_ID;
	if (!$catalogue && $jsnp) $catalogue = $jsnp[id];

	$selectBlp = "setting,zone_id" . (!$jsnp ? ",Message_ID,Subdivision_ID,Sub_Class_ID,Priority,Keyword,Checked,Catalogue_ID,name,zone_position,zone_priority,mobilemenu,SUBSTRING_INDEX(bgimg, ':', -1) as bgimg" : null);
	$selectBlsql = "select {$selectBlp} from Message2000 where Checked = 1 AND Catalogue_ID = '{$catalogue}' AND zone_position != 0 ORDER BY zone_position, zone_priority";
	$zones = $db->get_results($selectBlsql, 'ARRAY_A');

	$cssb .= ($settingszone['blkvertmid'] ? "
						#zone{$zn['zone_id']} .container { display: table; }
						#zone{$zn['zone_id']} .blocks { display: table-cell; vertical-align: middle; float: none; }
						" : null);
	if ($zones) {
		foreach ($zones as $z) { // начало блока
			if ($z['zone_position'] == 6 && $z['mobilemenu']) $mobileMenu = 1; # меню только в моб. версии
			$col = $pos = $classbg = null;
			unset($classZone);
			unset($classBg);
			unset($classBgData);

			$col = ($z['zone_id'] ? $z['zone_id'] : 1);
			$setzones = orderArray($z['setting']);

			# массив статичных зон
			$nolinezone = NO_LINE_ZONE_ARRAY;

			$classBg[] = "zone-bg";
			$classBg[] = $setzones['fixwidth'] && !in_array($col, $nolinezone) ? "containerbg_12" : "";
			if ($setzones['parallaxZone'] && $z['bgimg']) {
				$classBg[] = "zone-parallax";
				$classBgData = "data-parallax='scroll' data-image-src='{$HTTP_FILES_PATH}{$z[bgimg]}'";
			}

			if (!$jsnp) {
				$pos = ($z['zone_position'] ? $z['zone_position'] : 1);
				$name = ($z['name'] ? $z['name'] : "Без названия");
				$message_id = ($z['Message_ID'] ? $z['Message_ID'] : 1);
				$class = "container " . ($setzones['width'] != 1 ? 'container_100 ' : 'container_12 ') . " cb";

				if ($zoneblocks[$col]) {
					$classZone[] = "zone";
					$classZone[] = "cb";
					if ($setzones['blkvertmid']) $classZone[] = "blk-middle";
					if ($setzones['fixedZone']) $classZone[] = "zone-fixtop";

					$zone[$pos] .= ($setzones['footer'] ? "<footer>" : null) .
						"<section data-zone='{$message_id}' data-name='{$name}' data-keyw='" . $z['Keyword'] . "' id='zone{$col}' " . ($bitcat ? "data-editlink='" . nc_message_link($message_id, 2000, "edit") . "'" : null) . " data-id='{$col}' class='" . implode(" ", $classZone) . "'>" .
						"<div class='" . implode(" ", $classBg) . "' {$classBgData}></div>" .
						($setzones['header'] ? "<header id='header' class='{$class}'>" : "<div class='{$class}'>") .
						$zoneblocks[$col] .
						($setzones['header'] ? "</header>" : "</div>") .
						"</section>" .
						($setzones['footer'] ? "</footer>" : null);
				}
			}

			// Массив настроек jsonparam
			$jsonparam['zone'][$col]['classbg'] = ($classbg ? $classbg : "");
		}
	}

	if ($jsnp) $jsonparam['blocks'] = getblocksarr($jsnp);

	return ($jsnp ? $jsonparam : $zone);
}
