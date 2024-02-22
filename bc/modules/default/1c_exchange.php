<?php
ini_set('memory_limit', '1200M');
ini_set('max_input_time', '1800');
ini_set('post_max_size', '1024M');
ini_set('upload_max_filesize', '1024M');
ini_set('max_execution_time', '3600');
set_time_limit(0);

use App\modules\Korzilla\Upload1C\Exchange\Models\CheckForUpdates;
use App\modules\bitcat\Cron\Controller as Cron;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $pathInc, $catalogue, $login, $current_catalogue, $nc_core, $field_connect, $passite, $file1c, $AUTH_USER_ID, $v1c;
require_once $ROOTDIR."/bc/modules/bitcat/class.upload.php";


# настройки
$path1c = $ROOTDIR.$pathInc.'/1C'.$v1c.'/';

$iniSetPath = $path1c.'expsetting.ini';

if (file_exists($iniSetPath)) $expSet = parse_ini_file($iniSetPath);

//print_r($iniSetPath);

if ($_GET['mode']=='query' && $_GET['type']=='sale') {
	if (!$expSet['utf8']) {
		header("Content-Type: text/xml; charset=windows-1251");
	} else {
		//header("Content-Type: text/xml; charset=utf-8");
	}
	header("Pragma: no-cache");
}


if($_GET['Angola'] !== 'lessgo') {
	while (ob_get_level() > 0) {
		ob_end_flush();
	}
}

session_start();


$headrs = getallheaders();
$emailadmin = $emailadmin;
$passite = substr(md5("a".$login['login']."2302".($v1c ? "v".$v1c : NULL)),1, 8);
$file1c = $ROOTDIR.$pathInc."/1C{$v1c}/";
if(!is_dir($file1c)) @mkdir($file1c);


// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}
if ($catalogue==1) die('Выгрузка на тестовый сайт запрещена');

if ($admpas=='2302') {
	echo "Ссылка: http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."/1c_exchange{$v1c}.php<br>
	Логин: {$login['login']}<br>
	Пароль: {$passite}<br>
	Ссылка на просмотр загруженных файлов на сайт: http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."/search1c.php?key=".md5($login.$catalogue)."<br>";
}

if ($_GET['Angola'] == 'lessgo') {
	header('Content-type: application/json');
	echo json_encode([
		'url' => "http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."/1c_exchange{$v1c}.php",
		'login' => $login['login'],
		'pass' => $passite,
		'longUrl' => "http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."/search1c.php?key=".md5($login.$catalogue),
	]);exit;
}




class Exchange1c {
        private $mode;
        private $filename;

        public function __construct()
		{
			global $file1c,$passite,$login,$current_catalogue,$expSet;
                // принимаем значение mode
                $this->mode = $_GET['mode'];
				$this->type = $_GET['type'];
                $this->filename = $_GET['filename'];
				$this->file1c = $file1c;
				$this->login = $login['login'];
				$this->pass = $passite;
				$this->size1Cpaket = ($expSet['size1Cpaket'] ? $expSet['size1Cpaket'] : $current_catalogue['size1Cpaket']);
        }

        public function run()
		{
                $mode = $this->mode;
                // и здесь, в зависимости, что отправла 1С
                // вызываем одноименный метод
                if ($mode) $this->$mode();
        }


		/* авторизация */
        public function checkauth()
		{
				$headrs = getallheaders();
                echo "success\n";
				echo $this->login."\n";
				echo $this->pass."\n";
				if ($_GET['mode']=='query' && $_GET['type']=='sale') {} else {
					@unlink($this->file1c."1c_old2.zip");
					@rename($this->file1c."1c_old1.zip", $this->file1c."1c_old2.zip");
					@rename($this->file1c."1c.zip", $this->file1c."1c_old1.zip");
				}
				$this->lastLog($start);
				$this->sendLogEmail("1C авторизация \nLogin:{$this->login}\nPass:{$this->pass}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
                exit;
        }

		/* инициализация: отдача ответа в 1С с размером тома архива */
        public function init()
		{
                //$zip = extension_loaded('zip') ? 'yes' : 'no';
				$zip = ($this->type=='sale' ? 'no' : 'yes');
                echo 'zip='.$zip."\n";
                echo "file_limit=".($this->type=='sale' ? "0" : ($this->size1Cpaket ? $this->size1Cpaket : "15000000"))."\n";
				
				$this->lastLog();
				$this->sendLogEmail("1C инициализация \nLogin:{$this->login}\nPass:{$this->pass}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
                exit;
        }

		/* сохранение файлов */
        public function file()
		{
				$data = file_get_contents('php://input');
				$filen = ($this->type=='sale' || stristr($this->filename,"order") ? "orders1c/orders1c.zip" : "1c.zip");

				@file_put_contents($this->file1c.$filen, $data, FILE_APPEND);

				if (file_exists($this->file1c."orders1c/orders1c.zip")) { // распаковка заказов из 1С
					$zip1 = new ZipArchive;
					if ($zip1->open($this->file1c."orders1c/orders1c.zip") === TRUE) {
						$zip1->extractTo($this->file1c."orders1c/");
						//$zip1->close();
					}
					@unlink($this->file1c."orders1c/orders1c.zip");
				}
				
				if ($data) {
					//$this->import();
					echo "success\n";
					$this->sendLogEmail("1C файл загружен {$filen} ({$this->filename}).\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					exit;
				} else {
					echo "failure\n";
					$this->sendLogEmail("1C файл НЕ загружен {$filen} ({$this->filename}).\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					exit;
				}
				$this->lastLog();
        }

		/* распаковка архива */
		public function import()
		{
			if (file_exists($this->file1c."1c.zip")) {
				$zip = new ZipArchive;

				if ($zip->open($this->file1c."1c.zip") === TRUE) { // распаковка товаров из 1С
					//recursiveRemoveDir($this->file1c."import_files/");
					
					if (stristr($this->filename,'import')) {
						@unlink($this->file1c."import_old.xml");
						@copy($this->file1c."import.xml", $this->file1c."import_old.xml");
						//@rename($this->file1c."import.xml", $this->file1c."import_old.xml");
					}
					if (stristr($this->filename,'offers')) {
						@unlink($this->file1c."offers_old.xml");
						@copy($this->file1c."offers.xml", $this->file1c."offers_old.xml");
						//@rename($this->file1c."offers.xml", $this->file1c."offers_old.xml");
					}

					if ($zip->extractTo($this->file1c)) {
						//@unlink($this->file1c."1c.zip");
					}
					$zip->close();
					if (stristr($this->filename,'import')) {
						@rename($this->file1c."import0_1.xml", $this->file1c."import.xml");
						@rename($this->file1c."import1_1.xml", $this->file1c."import.xml");
					}
					if (stristr($this->filename,'offers')) {
						@rename($this->file1c."offers0_1.xml", $this->file1c."offers.xml");
						@rename($this->file1c."offers1_1.xml", $this->file1c."offers.xml");
					}

					$this->sendLogEmail("1C файл товары пришли {$this->filename}, распакованы.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					
				} else {
					$this->sendLogEmail("1C ошибка распаковки {$this->filename}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
				}
				//@unlink($this->file1c."1c.zip");
			}

			$this->updateSetting1C();
			$this->lastLog();
			echo "success\n";
			
			if (file_exists($this->file1c."import.xml") || file_exists($this->file1c.$this->filename)) {
				$this->sendLogEmail("1C запуск выгрузки import.xml.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
                exit;
			} else {
				echo "failure\n";
                exit;
			}
        }

		/* отдача заказов в XML */
		public function query()
		{
			global $expSet;
			$xmlorder = import1C('','',1);
			$this->lastLog();
			if ($xmlorder) {
				$this->sendLogEmail("1C запрос заказов\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));

				if (!$expSet['utf8']) {
					echo mb_convert_encoding($xmlorder,"windows-1251","utf-8");
				} else {
					echo $xmlorder;
				}

			} else {
				echo "failure\n";
                exit;
			}


        }

		/* отдача успешного статуса получения файлов */
		public function success()
		{
			$this->lastLog();
			if ($this->type=='sale') { // 
				/* успешная отправка заказов в 1С */
				$this->import1Csuccess();
				$this->sendLogEmail("1C заказы выгружены\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));

			} else {
				/* успешно получили каталог */
				$this->sendLogEmail("1C успешно каталог\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
			}
			echo "success\n";
        }
		
		/* пока не используем */
		public function deactivate()
		{
			$this->lastLog();
			echo "success\n";
		}
		
		/* отдача успешного статуса завершения процесса */
		public function complete()
		{
			$this->lastLog();
			echo "success\n";
        }

		/* Добовления тригера выгрузки */
		private function updateSetting1C()
		{
			global $v1c, $catalogue;
			$numberUpload = ($v1c ?: '1');
			
			$CheckForUpdates = new CheckForUpdates($catalogue, $numberUpload, ['update_time' => time(), 'path' => $this->file1c]);
			$CheckForUpdates->setCheckCron((new Cron()));
		}
		
		/* смена статусов заказов после отдачи их в 1С (Новый->В обработке) */
		private function import1Csuccess()
		{
			global $db, $catalogue, $pathInc, $DOCUMENT_ROOT, $v1c;
			$file_orders_send = @file_get_contents($DOCUMENT_ROOT.$pathInc.'/1C/orders_'.hexsite().'/last1Cquery.log');
			if (trim($file_orders_send)!='' && trim($file_orders_send)!=',' && trim($file_orders_send)!=',,') {
				$db->query("update Message2005 SET ShopOrderStatus = 4 where (ShopOrderStatus = '1' OR ShopOrderStatus IS NULL OR ShopOrderStatus = '') AND Message_ID IN (".$file_orders_send.")");
			}
			@file_put_contents($DOCUMENT_ROOT.$pathInc.'/1C/orders_'.hexsite().'/last1Cquery.log',"");
		}
		
		/* отправка запросов 1С на почту */
		private function sendLogEmail($text)
		{
			global $expSet;
			if (!$_GET[test] && $this->email) {
				$mailer = new CMIMEMail();
				$mailer->setCharset('utf-8');
				$mailer->mailbody($text);
				$mailer->send($expSet['email'], $emailadmin, $emailadmin, "{$this->login} 1C", "korzilla 1C");
				return true;
			}
			return false;
		}
		
		/* логгирование запросов от 1С */
		private function lastLog($start='')
		{
			global $pathInc, $DOCUMENT_ROOT, $v1c, $headrs;
			$logPath = $DOCUMENT_ROOT.$pathInc."/1C".$v1c."/last_exchange.log";
			$logSize = @filesize($logPath);
			$logText = "\r\n###################################### ".date("d.m.Y H:i:s")." | mode: ".$this->mode." | type: ".$this->type."  ######################################\r\n\r\n".print_r($_SERVER,1)."\r\n".print_r($headrs,1)."\r\n".print_r($_REQUEST,1)."\r\n";
			
			if ($start && $logSize>1*1024*1024) @unlink($logPath);
			@file_put_contents($logPath,$logText,FILE_APPEND);
			return true;
		}

}


if ($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) {
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
}

// if ($_SERVER['REDIRECT_REMOTE_USER']) {
// 	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['REDIRECT_REMOTE_USER'], 6)));
// }

if (($_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW']) || $_GET['test']) {
	if (($_SERVER['PHP_AUTH_USER']==$login['login'] && $_SERVER['PHP_AUTH_PW']==$passite) || $_GET['test']) {
		/* подключение успешно */
		$load1c = new Exchange1c();
		$load1c->run();

	} else {
		/* неверный пароль */
		echo "failure";
	}
} else {
	/* подключение не прошло */
	echo "failure";
	
}
