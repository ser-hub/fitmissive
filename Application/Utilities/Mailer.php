<?php

namespace Application\Utilities;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private static function prepareMailer()
    {
        $mailer = new PHPMailer(true);

        $mailer->SMTPDebug = SMTP::DEBUG_OFF;
        $mailer->isSMTP();
        $mailer->Host       = Config::get('mailer/host');
        $mailer->SMTPAuth   = true;
        $mailer->Username   = Config::get('mailer/username');
        $mailer->Password   = Config::get('mailer/password');
        $mailer->Port       = 465;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->CharSet = 'UTF-8';

        $mailer->setFrom(Config::get('mailer/username'), Config::get('mailer/alias'));

        return $mailer;
    }

    public static function sendHTMLFile($toAddress, $mailSubject, $filename)
    {
        $mail = self::prepareMailer();

        try {
            $mail->addAddress($toAddress);

            $mail->Subject = $mailSubject;
            $mail->msgHTML(file_get_contents(__DIR__ . '/../' . $filename), __DIR__);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }

    public static function sendPasswordRecoveryMail($toAddress, $key)
    {
        $mail = self::prepareMailer();

        $link = 'http://fitmissive.localhost/index/recoverpassword/' . $key;

        $mailContent = '<div style="display:flex;flex-direction:column;border:10px solid #227697;align-items:center;padding:1rem">';
        $mailContent .= '<div style="margin-right: .5rem"><img src="cid:site_logo" width="50px" height="50px"></div>';
        $mailContent .= '<div>Линк за възстановяване на паролата ви:<br>';
        $mailContent .= '<a href="' . $link . '">' . $link . '</a>';
        $mailContent .= '<div style="font-size:small">*Този линк ще стане невалиден след 1 час или след като го използвате.*</div>';
        $mailContent .= '</div></div>';

        try {
            $mail->addAddress($toAddress);

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Забравена парола';
            $mail->AddEmbeddedImage('img/profiles/default.png', 'site_logo');
            $mail->Body    = $mailContent;
            $mail->AltBody = 'Линк за възстановяване на паролата ви: ' . $link;

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo $mail->ErrorInfo;
        }
    }
}