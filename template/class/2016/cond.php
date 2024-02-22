<?php
$f_settings = valideSettings($settings, "xlebtype,fontcolor,linkcolor,radius,borderwidth,bordercolor,bg,bgimgpos,headbg,headcolor,headupper,headsize,headbold,iconcolor,floathead,floatbody,scrollbutcol,scrollbutfont,textsize,bottmarg,menuBgActive,MenuUppercase,MenuColorActive,MenuColor,menuFontSize,namefont");

$f_phpset = valideSettings($phpset, "contenttype,contsetclass,menutpl,dropmenu,mailform,callform,topmap,showtimework,targeting,reglink,favlink,sravlink,phonekod1,phone1,phonekod2,phone2,showphones,phonetext,showicon,devidertpl,sortsub,scrolling,scrollspeed,nc_ctpl,scrollbut,scrollNav,scrollDots,banereffect,punktwidth100,menubtnclick,captioneffect,minicartbord,minicarttype,itemsinmenu,bottomtext,sizehave,sizeitem_select,sizeitem,sizeitem_margin,sizeitem_image_select,sizeitem_image,sizeitem_fit,noname,template_block,module,objInModal,masonry,type_masonry,bannerNameSize,bannerNameEffect,bannerTextSize,bannerTextEffect,bannerAlign,sizeitem_counts,autoplay,countmenu,langSelect,level,animate,animate_title,animate_text,animate_items");



if ($phpset['contenttype']==1) { // из раздела
	$f_cc = cc_by_subID($f_sub);
	$f_template = '';
	if (!$f_cc) $warnText .= "Не удалось получить данные из раздела. ";
}

if ($phpset['contenttype']==2) { // меню
	$f_cc = ''; $f_ctpl = '';
	if (!$f_sub) $f_sub = 1;
	if (!$f_sub) $warnText .= "Не указан номер раздела. ";
	if (!$phpset['contsetclass']['menutpl']) $warnText .= "Не указан шаблон меню. ";
}


if (!$phpset['contenttype'] || $phpset['contenttype']==3) { // текст, контакты
	$f_sub = ''; $f_cc = ''; $f_template = ''; $f_ctpl = '';
}

if ($f_insub) $f_insub = ",".$f_insub.",";
if ($f_insub==',,') $f_insub='';

if ($f_noinsub) $f_noinsub = ",".$f_noinsub.",";
if ($f_noinsub==',,') $f_noinsub='';

$f_lang = json_encode($f_lang);

$f_Catalogue_ID = $catalogue;

// stop, если есть ошибки
if ($warnText) $posting = 0;

$f_citytarget = ",".implode(",",$f_citytarget).",";
if ($f_citytarget==',,') $f_citytarget='';

?>
