<?

require_once $_SERVER['DOCUMENT_ROOT'].'/bc/modules/default/SendMailSmtpClass.php';

$setting = array(
	'emailsend'=>'help-y-help@yandex.ru',
	'emailpass' => 'diktpckeodfhtvqy',
	'emailsmtp' => 'ssl://smtp.yandex.ru',
	'emailport' => 465
);
$tomail = "wultrex@yandex.ru, vsegta@mail.ru";

$mailSMTP = new SendMailSmtpClass($setting['emailsend'], $setting['emailpass'], $setting['emailsmtp'], $setting['emailport'], "UTF-8");

$tema = "fffffff";
$text = "Добрый день. цена и срок поставки на светильник GSTO 60/24 14шт";

$from = array(
	"",
	$setting['emailsend']
);

foreach(explode(",",$tomail) as $tomail1) {
            $ressend[] =  $mailSMTP->send(trim($tomail1), $tema, $text, $from);
		}

print_r($ressend);