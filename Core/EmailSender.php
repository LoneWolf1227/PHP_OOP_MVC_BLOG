<?php


namespace Core;


use MVC\Models\Users\User;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    public static function send(User $receiver, string $subject, string $templateName, array $templateVars = [])
    {
        extract($templateVars);

        ob_start();
        require __DIR__ . '/../MVC/Views/Temp/mail/' . $templateName;
        $body = ob_get_contents();
        ob_end_clean();
        require __DIR__ . '/../vendor/autoload.php';

        $mail = new PHPMailer;

        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Host = 'smtp.mail.ru';
        $mail->Username = 'manalonewolf@mail.ru';
        $mail->Password = 'HfEkW8#)4$';

        $mail->setFrom('manalonewolf@mail.ru', 'Wolf_Proger');
        $mail->addReplyTo('manalonewolf@gmail.com', 'First Last');
        $mail->addAddress($receiver->getEmail());

        $mail->Subject = $subject;
        $mail->msgHTML($body);

        if(!$mail->Send()) {
            $error = 'Message was not sent.';
            $error .= 'Mailer error: ' . $mail->ErrorInfo;
        }

    }

}