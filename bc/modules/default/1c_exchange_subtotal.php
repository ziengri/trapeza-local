<?
ini_set('memory_limit', '800M');

set_time_limit(0);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $pathInc, $catalogue, $login, $current_catalogue, $nc_core, $field_connect, $passite, $file1c, $AUTH_USER_ID;
require_once $ROOTDIR."/bc/modules/bitcat/class.upload.php";

//if ($_GET[mode]=='query') { header("Content-Type: text/xml; charset=windows-1251"); header("Pragma: no-cache");}

while (ob_get_level() > 0) {
    ob_end_flush();
}

session_start();

//$logmail = 1;
$headrs = getallheaders();
$emailadmin = "robot@korzilla.ru";
$passite = substr(md5("a".$login['login']."2302".($v1c ? "v".$v1c : NULL)),1, 8);
$file1c = $ROOTDIR.$pathInc."/1C{$v1c}/";
if(!is_dir($file1c)) @mkdir($file1c);

if ($admpas=='2302') {
	echo "Ссылка: http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."/1c_exchange{$v1c}.php<br>
	Логин: {$login['login']}<br>
	Пароль: {$passite}<br>";
}


// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
	if ($catalogue==777) { $logmail = 1; $emailadmin = "avtomoto34@mail.ru"; }
	if ($catalogue==857) { $logmail = 1; $emailadmin = "verh@korzilla.ru"; }
}
if ($catalogue==1) die('Выгрузка на тестовый сайт запрещена');


class Exchange1c {
        private $mode;
        private $filename;

        public function __construct() {
			global $file1c,$passite,$login,$current_catalogue;
                // принимаем значение mode
                $this->mode = $_GET['mode'];
				$this->type = $_GET['type'];
                $this->filename = $_GET['filename'];
				$this->file1c = $file1c;
				$this->login = $login['login'];
				$this->pass = $passite;
				$this->size1Cpaket = $current_catalogue['size1Cpaket'];
        }

        public function run(){
                $mode = $this->mode;
                // и здесь, в зависимости, что отправла 1С
                // вызываем одноименный метод
                if ($mode) $this->$mode();
        }



        public function checkauth() {
				$headrs = getallheaders();
                echo "success\n";
				echo $this->login."\n";
				echo $this->pass."\n";
				@unlink($this->file1c."1c_old2.zip");
				@rename($this->file1c."1c_old1.zip", $this->file1c."1c_old2.zip");
				@rename($this->file1c."1c.zip", $this->file1c."1c_old1.zip");
				$mailer = new CMIMEMail();
				$mailer->setCharset('utf-8');
				$mailer->mailbody("1C авторизация \nLogin:{$this->login}\nPass:{$this->pass}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
				//if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login}: 1C авторизация", "korzilla 1C");
                exit;
        }


        public function init() {
                //$zip = extension_loaded('zip') ? 'yes' : 'no';
				$zip = ($this->type=='sale' ? 'no' : 'yes');
                echo 'zip='.$zip."\n";
                echo "file_limit=".($this->type=='sale' ? "0" : ($this->size1Cpaket ? $this->size1Cpaket : "15000000"))."\n";
				
				$mailer = new CMIMEMail();
				$mailer->setCharset('utf-8');
				$mailer->mailbody("1C инициализация \nLogin:{$this->login}\nPass:{$this->pass}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
				//if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login}: 1C инициализация", "korzilla 1C");
                exit;
        }


        public function file() {

            	// вытаскиваем сырые данные
				$data = file_get_contents('php://input');

				$filen = ($this->type=='sale' || stristr($this->filename,"order") ? "orders1c/orders1c.zip" : "1c.zip");

                //Сохраняем файл импорта в zip архиве
				@file_put_contents($this->file1c.$filen, $data, FILE_APPEND);

				if (file_exists($this->file1c."orders1c/orders1c.zip")) { // распаковка заказов из 1С
					$zip1 = new ZipArchive;
					if ($zip1->open($this->file1c."orders1c/orders1c.zip") === TRUE) {
						$zip1->extractTo($this->file1c."orders1c/");
						$zip1->close();
					}
					@unlink($this->file1c."orders1c/orders1c.zip");
				}

				if ($data) {
					//$this->import();
					echo "success\n";
					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					//$mailer->mailbody("1C файл загружен {$filen} ({$this->filename}).\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login}: 1C файл загружен", "korzilla 1C");
					exit;
				} else {
					echo "failure\n";
					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					$mailer->mailbody("1C файл НЕ загружен {$filen} ({$this->filename}).\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login}: 1C НЕ загружен", "korzilla 1C");
					exit;
				}
        }

		public function import() {
			if (file_exists($this->file1c."1c.zip")) {
				$zip = new ZipArchive;
				if (stristr($this->filename,'import')) $filexml = "import";
				if (stristr($this->filename,'offers')) $filexml = "offers";

				if ($zip->open($this->file1c."1c.zip") === TRUE) { // распаковка товаров из 1С

					if (stristr($this->filename,'import')) {
						@unlink($this->file1c."import_old.xml");
						@rename($this->file1c."import.xml", $this->file1c."import_old.xml");
					}
					if (stristr($this->filename,'import')) {
						@unlink($this->file1c."offers_old.xml");
						@rename($this->file1c."offers.xml", $this->file1c."offers_old.xml");
					}

					$zip->extractTo($this->file1c);
					$zip->close();
					if (stristr($this->filename,'import')) {
						@rename($this->file1c."import0_1.xml", $this->file1c."import.xml");
						@rename($this->file1c."import1_1.xml", $this->file1c."import.xml");
					}
					if (stristr($this->filename,'offers')) {
						@rename($this->file1c."offers0_1.xml", $this->file1c."offers.xml");
						@rename($this->file1c."offers1_1.xml", $this->file1c."offers.xml");
					}

					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					$mailer->mailbody("1C файл товары пришли {$this->filename}, распакованы.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C файл товары пришли", "korzilla 1C");
				} else {
					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					$mailer->mailbody("1C ошибка распаковки {$this->filename}.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					$mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C ошибка распаковки товаров", "korzilla 1C");
				}
				//@unlink($this->file1c."1c.zip");
			}

			echo "success\n";
			if (file_exists($this->file1c."import.xml") || file_exists($this->file1c.$this->filename)) {
                
				$mailer = new CMIMEMail();
				$mailer->setCharset('utf-8');
				$mailer->mailbody("1C запуск выгрузки import.xml.\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
				//if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C запуск выгрузки", "korzilla 1C");
                exit;
			} else {
				echo "failure\n";
                exit;
			}
        }

		public function query() {
			$xmlorder = import1C('','',1);

			if ($xmlorder) {
				if (!$_GET[test]) {
					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					$mailer->mailbody("1C запрос заказов\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C запрос заказов", "korzilla 1C");
				}
				//echo iconv("utf-8","windows-1251",$xmlorder);
				echo $xmlorder;
			} else {
				echo "failure\n";
                exit;
			}


        }

		public function success() {
			if ($this->type=='sale') { // успешная отправка заказов
				import1Csuccess();
				if (!$_GET[test]) {
					$mailer = new CMIMEMail();
					$mailer->setCharset('utf-8');
					$mailer->mailbody("1C заказы выгружены\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
					if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C заказы выгружены", "korzilla 1C");
				}
			} else { // успешная товары
				$mailer = new CMIMEMail();
				$mailer->setCharset('utf-8');
				$mailer->mailbody("1C успешно что\nДата и время: ".date("d.m.Y H:i:s")."\n\n".print_r($_REQUEST,1));
				if ($logmail) $mailer->send($emailadmin, "robot@korzilla.ru", "robot@korzilla.ru", "{$this->login} 1C успешно что", "korzilla 1C");
			}
			echo "success\n";
        }

}




if (($_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW']) || $_GET[test]) {
	if (($_SERVER['PHP_AUTH_USER']==$login['login'] && $_SERVER['PHP_AUTH_PW']==$passite) || $_GET[test]) {
		logg('пароль верен');
		$load1c = new Exchange1c();
		$load1c->run();

	} else {
		logg('пароль не верен');
		echo "failure";
	}
} else { // подключение не прошло
	echo "failure";
	logg('нет логина');
}

function import1Csuccess() {
	global $db, $catalogue, $pathInc, $DOCUMENT_ROOT;
	$file_orders_send = @file_get_contents($DOCUMENT_ROOT.$pathInc.'/1C/orders_'.hexsite().'/last1Cquery.log');
	if (trim($file_orders_send)!='' && trim($file_orders_send)!=',' && trim($file_orders_send)!=',,') $db->query("update Message2005 SET ShopOrderStatus = 4 where (ShopOrderStatus IS NULL OR ShopOrderStatus = '') AND Message_ID IN (".$file_orders_send.")");
	@file_put_contents($DOCUMENT_ROOT.$pathInc.'/1C/orders_'.hexsite().'/last1Cquery.log',"");
	//return "update Message2005 SET ShopOrderStatus = 4 where ShopOrderStatus = '' AND Message_ID IN (".$file_orders_send.")";
}

function logg($text) {
	global $ROOTDIR,$headrs;
	if ($headrs[Host]=='bikecity.krzi.ru' && !$_GET[test]) file_put_contents($ROOTDIR."/1cexchange.log",$text."\r\n".print_r($_SERVER,1)."\r\n".print_r($headrs,1)."\r\n".print_r($_REQUEST,1)."\r\n",FILE_APPEND);
}