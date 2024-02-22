<?php
/**
* mailAsisit
* 
* Класс для отправки писем через php mail() или SMTP в зависимости от настроек сайта
* 
* @author Ilsur
* @version 1.0
*/

require_once $DOCUMENT_ROOT.'/bc/modules/default/SendMailSmtpClass.php';

class MailAssist
{
    public function __construct()
    {
        global $setting;
        if ($setting['emailsend'] && $setting['emailpass']) {
            $this->smtpOn = true;
            $setting['emailsmtp'] = (!empty($setting['emailsmtp']) ? $setting['emailsmtp'] : 'ssl://smtp.yandex.ru');
            $setting['emailport'] = (!empty($setting['emailport']) ? $setting['emailport'] : '465');

            $this->mailer = new \SendMailSmtpClass($setting['emailsend'], $setting['emailpass'], $setting['emailsmtp'], $setting['emailport'], "UTF-8");
        } else {
            $this->mailer = new \CMIMEMail();
        }
    }

    public function send($to, $from, $body, $title, $name)
    {
        global $setting;

        if ($this->smtpOn) {
            $from = [
                "", /* должно быть: пусто или латиница, иначе не шлется письмо */
                $setting['emailsend']
            ];
            return $this->mailer->send($to, $title, $body, $from);
        } else {
            $this->mailer->mailbody(strip_tags($body), $body);
            return $this->mailer->send($to, $from, $from, $title, $name);
        }
    }

    public function addFile($filePath, $fileName, $fileType)
    {
        if ($this->smtpOn) {
            $this->mailer->addFile($filePath, $fileName);
        } else {
            $this->mailer->attachFile($filePath, $fileName, $fileType);
        }
    }
}
