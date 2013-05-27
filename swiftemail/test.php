<? 


  require_once 'lib/swift_required.php';

// Create the Transport
$transport = Swift_SmtpTransport::newInstance('mail.exemplyrigor.pt', 25)
  ->setUsername('isabelferreira@exemplyrigor.pt')      
  ->setPassword('exemplyrigor')
  ;

/*
You could alternatively use a different transport such as Sendmail or Mail:

// Sendmail
$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

// Mail
$transport = Swift_MailTransport::newInstance();
*/
                            
// Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);

// Create a message
$message = Swift_Message::newInstance('Wonderful Subject')
  ->setFrom(array('isabelferreira@exemplyrigor.pt' => 'Isabelinha'))
  ->setTo(array('joao.kant.barreto@gmail.com' => 'Mau'))
  ->setBody('Here is the message itself')
  ->attach(Swift_Attachment::fromPath('README'))
  ;

// Send the message
$result = $mailer->send($message);

echo $result;



?>