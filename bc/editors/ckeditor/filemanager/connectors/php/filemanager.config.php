<?php

/**
 *	Filemanager PHP connector configuration
 *
 *	filemanager.config.php
 *	config for the filemanager.php connector
 *
 *	@license	MIT License
 *	@author		Riaan Los <mail (at) riaanlos (dot) nl>
 *	@author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */

date_default_timezone_set('Europe/Moscow');

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -7 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );

include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ROOT_FOLDER."connect_io.php");
$nc_core->modules->load_env();

$lang = $nc_core->lang->detect_lang();
if ( $lang == 'Russian' ) {
  $lang = $nc_core->NC_UNICODE ? $lang."_utf8" : $lang."_cp1251";
}
require ($ADMIN_FOLDER."lang/".$lang.".php");

//require_once ($NETCAT_FOLDER."index.php");

/**
 *	Check if user is authorized
 *
 *	@return boolean true is access granted, false if no access
 */
function auth() {

	global $perm, $AUTH_USER_ID;

  if (!isset($AUTHORIZE_BY)) $AUTHORIZE_BY = 'User_ID';
	$user = Authorize();

	if (!$user) {
		echo 'Not enough rights or Hack attempt!';
		exit;
  }
  if ( !is_object($perm) || !$perm->accessToCKeditor()  ) {
		echo 'Not enough rights or Hack attempt!';
		exit;
	}

  return $AUTH_USER_ID;
}

/**
 *	Language settings
 */
$config['culture'] = 'ru_cp1251';

/**
 *	PHP date format
 *	see http://www.php.net/date for explanation
 */
$config['date'] = 'd M Y H:i';

/**
 *	Icons settings
 */
$config['icons']['path'] = 'images/fileicons/';
$config['icons']['directory'] = '_Open.png';
$config['icons']['default'] = 'default.png';

/**
 *	Upload settings
 */
$config['upload']['overwrite'] = false; // true or false; Check if filename exists. If false, index will be added
$config['upload']['size'] = false; // integer or false; maximum file size in Mb; please note that every server has got a maximum file upload size as well.
$config['upload']['imagesonly'] = false; // true or false; Only allow images (jpg, gif & png) upload?

/**
 *	Images array
 *	used to display image thumbnails
 */
$config['images'] = array('jpg', 'jpeg','gif','png');


/**
 *	Files and folders
 *	excluded from filtree
 */
$config['unallowed_files']= array('.htaccess');
$config['unallowed_dirs']= array('_thumbs');

/**
 *	FEATURED OPTIONS
 *	for Vhost or outside files folder
 */

$auth = auth();

$Array = $nc_core->get_settings();
if(!is_dir($DOCUMENT_ROOT.$SUB_FOLDER.$HTTP_FILES_PATH."userfiles/")) {
		mkdir($DOCUMENT_ROOT.$SUB_FOLDER.$HTTP_FILES_PATH."userfiles/", 0777);
	}

if(!$Array['CKEditorFileSystem'] || ($auth && $perm->isSupervisor()) ) {
  $config['rel_path'] = $HTTP_FILES_PATH."userfiles";
}

else {

	if( !is_dir($DOCUMENT_ROOT.$SUB_FOLDER.$HTTP_FILES_PATH."userfiles/".$auth) ) {
		mkdir($DOCUMENT_ROOT.$SUB_FOLDER.$HTTP_FILES_PATH."userfiles/".$auth, 0777);
	}
	$config['rel_path'] = $HTTP_FILES_PATH."userfiles/".$auth;
}

$config['doc_root'] = $DOCUMENT_ROOT.$SUB_FOLDER.$config['rel_path'];
//	not working yet
//$config['upload']['suffix'] = '_'; // string; if overwrite is false, the suffix will be added after the filename (before .ext)