<?php
date_default_timezone_set('Europe/Lisbon');
require("../../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function validEmail($email, $skipDNS = false)
    {
       $isValid = true;
       $atIndex = strrpos($email, "@");
       if (is_bool($atIndex) && !$atIndex)
       {
          $isValid = false;
       }
       else
       {
          $domain = substr($email, $atIndex+1);
          $local = substr($email, 0, $atIndex);
          $localLen = strlen($local);
          $domainLen = strlen($domain);
          if ($localLen < 1 || $localLen > 64)
          {
             // local part length exceeded
             $isValid = false;
          }
          else if ($domainLen < 1 || $domainLen > 255)
          {
             // domain part length exceeded
             $isValid = false;
          }
          else if ($local[0] == '.' || $local[$localLen-1] == '.')
          {
             // local part starts or ends with '.'
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $local))
          {
             // local part has two consecutive dots
             $isValid = false;
          }
          else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
          {
             // character not valid in domain part
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $domain))
          {
             // domain part has two consecutive dots
             $isValid = false;
          }
          else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
          {
             // character not valid in local part unless 
             // local part is quoted
             if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
             {
                $isValid = false;
             }
          }

          if(!$skipDNS)
          {
              if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
              {
                 // domain not found in DNS
                 $isValid = false;
              }
          }
       }
       return $isValid;
    }


if($action=="get_email")
{

    $query = "SELECT * FROM vicidial_list WHERE lead_id='$lead_id'";
    $query = mysql_query($query,$link) or die(mysql_error());

    while ($row = mysql_fetch_assoc($query))
    {
        foreach($row as $key=>$value)
        {
            if (validEmail($value)) { $email = $value; }  
        }
    }
    echo $email; 
}

if($action=="send_email")
{
    
    if(!validEmail($email_address)) { echo "Por favor preencha um E-mail válido."; exit; }
    
    
    
    require_once 'lib/swift_required.php';
    $transport = Swift_SmtpTransport::newInstance('mail.exemplyrigor.com', 25)
      ->setUsername('info@exemplyrigor.com')      
      ->setPassword('exemplyrigor')
      ;

    $mailer = Swift_Mailer::newInstance($transport);

   
    
       
    $message = Swift_Message::newInstance('Eficiência Energética - Proposta Retific Power®')
        ->setFrom(array('info@exemplyrigor.com' => 'Sónia Ferreira'))
        ->setTo(array($email_address => $email_name));
    
    $message->attach(Swift_Attachment::fromPath('attachments/EmpresaEenergy.pdf'));
  /*  $message->attach(Swift_Attachment::fromPath('attachments/EcoEnergy_Empresa_02.jpg'));
    $message->attach(Swift_Attachment::fromPath('attachments/EcoEnergy_Empresa_03.jpg'));
    $message->attach(Swift_Attachment::fromPath('attachments/EcoEnergy_Empresa_05.jpg'));
    
    $message->attach(Swift_Attachment::fromPath('attachments/Apresentação Retificador Eenergy.pdf'));
    $message->attach(Swift_Attachment::fromPath('attachments/Pasta Técnica Controladores.pdf'));   */
    
    $cid1 = $message->embed(Swift_Image::fromPath('energy_logo_new.png'));
    $cid2 = $message->embed(Swift_Image::fromPath('energy_ambiente_new.png'));

    
    $message->setBody('
        
       <span style="font-size:14px; font-family:Calibri;"> Exmo(a). Senhor(a), <br><br>
        
		A eficiência energética é, hoje, um tema de discussão no qual merece atenção de todos. <br>
		A Eenergy – Alternative Energy Solutions, é uma empresa que apresenta soluções na área de gestão de energia. Disponibilizamos assim, os nossos serviços com uma campanha de sensibilização dirigida a toda população no qual apresenta alternativas na área da redução energética. 
		<br><br>
		Temos alvará por parte do INCI 66584, estamos em processo de certificação (<u>ISO9001</u> e <u>ISO140001</u>), e técnicos  instaladores credenciados DGEG – Direção Geral de Energia e Geologia.
		<br><br>
		Neste sentido, enviamos em anexo, uma breve apresentação do nosso produto e empresa.<br>
		Deixa-mos o nosso site onde poderá ver todos os serviços que prestamos. <br>
		<a href="www.exeplyrigot.com">www.exemplyrigor.com</a>
		<br><br>
		Agradeço desde já a atenção, estando ao dispor para qualquer esclarecimento que entenda necessário.
		<br><br>
		Os melhores cumprimentos,
		<br><br>

        <span style="font-size:12px; font-family:Arial;"><b><font color="#404040">Sónia Ferreira</font></b></span> <br>
        <span style="font-size:11px; font-family:Arial;"><b><font color="#404040">Departamento Comercial</font></b></span> <br>

       	<table><tr><td><img src="'.$cid1.'"></td></tr></table>
        <a href="www.exemplyrigor.com"><b>www.exemplyrigor.com</b></a><br>
        <a href="mailto:sferreira@exemplyrigor.com">sferreira@exemplyrigor.com</a> <br>
        
        <span style="font-size:16px; font-family:Arial;"><b><font color="#404040">Nº ÚNICO +351 707 200 220</font></b></span> <br>
        <span style="font-size:11px; font-family:Arial;"><b><font color="#404040">Tel/Fax: &nbsp;&nbsp;&nbsp;&nbsp;         +351 214 759 910 </font></b></span> <br>
        
        <span style="font-size:11px; font-family:Arial;"><b><font color="#404040">Facebook: <a href="www.facebook.com/Exemplyrigor">Eenergy - Exemplyrigor</a></font></b></span> <br>  <br>
      
	  
	  
	  
	  
	  	<span style="font-size:11px; font-family:Arial;"><b><font color="#404040">ESPANHA -  Calle Diagonal nº 48 1º Planta, 2ª Puerta-  08420 - Canovelles -  Barcelona  -  Spain  - LISBOA -  Rua Josefa de Óbidos, 5 B  -  2650 - 210 Alfornelos  -  Amadora  -  Portugal</font></b></span><br>
	  
        <span style="font-size:11px; font-family:Arial;"><b><font color="#404040">ESPANHA - PORTUGAL</font></b></span>
		
		<br><br>
                
		<span style="font-size:9px; font-family:Arial;">
			<b><font color="#1F497D">
			AVISO DE CONFIDENCIALIDADE
			</font></b>
		</span><br>
        
        <span style="font-size:8px; font-family:Arial;">
			<b><font color="#1F497D">Este e-mail e quaisquer ficheiros informáticos com ele transmitidos são confidenciais e destinados ao conhecimento e uso exclusivo do respectivo destinatário, não podendo o conteúdo dos mesmos ser alterado. Caso tenha recebido este e-mail indevidamente, queira informar de imediato o remetente e proceder à destruição da mensagem. O correio electrónico não garante a confidencialidade dos conteúdos das mensagens, nem a recepção adequada dos mesmos. Caso o destinatário deste e-mail tenha qualquer objecção à utilização deste meio deverá contactar de imediato o remetente.</font></b>
		</span><br>
         
        <span style="font-size:9px; font-family:Arial;">
			<b><font color="#1F497D">
				CONFIDENTIALITY WARNING
			</font></b>
		</span><br> 
        
        <span style="font-size:8px; font-family:Arial;">
			<b><font color="#1F497D">
			This e-mail and any files transmitted with it are confidential and intended solely for the use of the individual or entity to which they are addressed. Their contents may not be altered. If you have received this e-mail in error please notify the sender and destroy it immediately. Please note that Internet e-mail neither guarantees the confidentiality of the messages sent using this method of communication nor the proper receipt of the said messages. If the addressee of this message objects to the use of Internet e-mail, please communicate it to the sender.
			</font></b></span>
         
        <center> 
		<span style="font-size:12px; font-family:Verdana; color:#006600">
			<img border=0 width=30 height=19 src="'.$cid2.'" >
			<b>Antes de imprimir pense em sua responsabilidade e compromisso com o</b> 
		</span>
		<span style="font-size:12px; font-family:Verdana; color:#009900">
			<b>MEIO AMBIENTE</b>
		</span>                                                                                                                                                
        
        ', 'text/html')
       ;

       
       
       
    $result = $mailer->send($message, $failures);
    if( $result>=1 && count($failures)<1 )
    { 
        $NOW_TIME = date("Y-m-d H:i:s");
        $query = "INSERT INTO email_log VALUES ('', NOW(), '$sent_by_user', '$sent_by_campaign', '$email_address', '$email_name', 'SENT')";
        $query = mysql_query($query,$link) or die(mysql_error());
    
        echo "Email enviado com sucesso.<br><br>";
        
        
    
    } else 
    { 
        $NOW_TIME = date("Y-m-d H:i:s");
        $query = "INSERT INTO email_log VALUES ('', '$NOW_TIME', '$sent_by_user', '$sent_by_campaign', '$email_address', '$email_name', 'ERROR')";
        $query = mysql_query($query,$link) or die(mysql_error());
        echo "Erro a enviar.";
        print_r($failures); 
    }
}

?>