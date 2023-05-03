<?php

namespace Application\Utilities;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private const HOST = 'smtp.gmail.com';
    private const USERNAME = 'fitmissive.noreply@gmail.com';
    private const PASSWORD = 'lgfyikkzphdwpdtk';
    private const ALIAS = 'Fitmissive Password Service';

    public static function sendHTMLFile($toAddress, $mailSubject, $filename)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = self::HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::USERNAME;
            $mail->Password   = self::PASSWORD;
            $mail->Port       = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            //Recipients
            $mail->setFrom(self::USERNAME, self::ALIAS);
            $mail->addAddress($toAddress);

            $mail->Subject = $mailSubject;
            $mail->msgHTML(file_get_contents(__DIR__ . '/../' . $filename), __DIR__);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }
}
