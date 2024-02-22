<?php 
$f_setting = valideSettings($settingzone, "width,fixheight,fixwidth,padd_top,padd_bottom,mrgn_top,mrgn_bottom,bgcolor,height,footer,header,textcolor,linkcolor,iconcolor,bgimgpos,fixed,blkmarginbot0,blkvertmid,fixedZone,parallaxZone,alignblocks");

$f_Catalogue_ID = $catalogue;

// stop, если есть ошибки
if ($warnText) $posting = 0;

?>