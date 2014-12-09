<?php

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/swiftemail/lib/swift_required.php";

function send_email($email_address, $email_name, $msg, $assunto)
{
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
        ->setUsername('ccamemail@gmail.com')
        ->setPassword('ccamemail1234');

    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance($assunto)
        ->setFrom(array('ccamemail@gmail.com' => 'Acústica Médica'))
        ->setTo(array($email_address => $email_name));
    $message->setBody($msg, 'text/html');
    $result = $mailer->send($message);
    return ($result >= 1);
}