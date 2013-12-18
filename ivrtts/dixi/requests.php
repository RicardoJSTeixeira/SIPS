<?php

ini_set("display_errors", "1");
require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
       
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}



switch ($action) {
    case 'browser-listen' : { browserListen($data, $user, $lang); break; }
    case 'browser-phone' : { browserPhone($data, $user, $phone, $link, $lang); break; }
}


function browserListen($data, $user, $lang) {
    
    switch ($lang) {
        case 'pt-male' : $voice = 'Vicente'; break;
        case 'pt-female' : $voice = 'Violeta'; break;
    }
    
    $fileName = $user. '_' . date("Y-m-d_H-i-s") . '_web-listen';
    system("python /usr/share/Dixi/tts.py $voice '$data' > /srv/www/htdocs/ivrtts/dixi/files/$fileName.mp3", $retval);
    echo $fileName;
}

function browserPhone($data, $user, $phone, $link, $lang) {
    switch ($lang) {
        case 'pt-male' : $voice = 'Vicente'; break;
        case 'pt-female' : $voice = 'Violeta'; break;
    }
    
    $fileName = $user. '_' . date("Y-m-d_H-i-s") . '_web-phone';
    system("python /usr/share/Dixi/tts.py $voice '$data' > /srv/www/htdocs/ivrtts/dixi/files/$fileName.mp3", $retval);
    $path = "/srv/www/htdocs/ivrtts/dixi/files/$fileName.mp3";
    $NOW_TIME = date("Y-m-d H:i:s");
                
                //// Processo de introduzir uma chamada real no servidor (para efeitos de tempos, estatisticas, lead record, etc)
                
                //Vamos verificar se a lead existe em sistema
                $stmt = "SELECT lead_id FROM vicidial_list where phone_number LIKE '$phone' order by modify_date desc LIMIT 1;";
                
                $fetch_lead = mysql_query($stmt, $link) or die(mysql_error());
                if (mysql_num_rows($fetch_lead)<1) {
                //Se a lead não existir vamos criar
                $stmt = "INSERT INTO vicidial_list SET phone_code='1',phone_number='$phone',list_id='998',status='QUEUE',user='adminij',called_since_last_reset='Y',entry_date='$NOW_TIME',last_local_call_time='$NOW_TIME';"; 
                $fetch_lead = mysql_query($stmt, $link) or die(mysql_error());
                $lead_id = mysql_insert_id($fetch_lead);
                } else {
                    $lead_id = mysql_fetch_assoc($fetch_lead);
                    $lead_id = $lead_id['lead_id'];
                } 
                
                //Põe a lead incall
                $stmt = "UPDATE vicidial_list set status='MSG001', called_since_last_reset='Y', called_count = called_count + 1 ,user='adminij',last_local_call_time='$NOW_TIME' where lead_id='$lead_id';";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                
                // Vamos gerar um unique caller ID para tracking da chamada
                $PADlead_id = sprintf("%010s", $lead_id);
                    while (strlen($PADlead_id) > 10) {$PADlead_id = substr("$PADlead_id", 1);
			}
                $CIDdate = date("mdHis");
                while (strlen($CIDdate) > 9) {$CIDdate = substr("$CIDdate", 1);
                }
                $MqueryCID = "M$CIDdate$PADlead_id";
                $CIDstring = "\"$MqueryCID$EAC\" <0000000000>";
                
                // Vamos gerar um Unique ID
                $random = (rand(1000000, 9999999) + 10000000);
                $epoch_sec = date("U");
                $uniqueid = $epoch_sec.".".$random;
                
                // Insert na manager para iniciar a chamada
                $stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$VARDB_server','','Originate','$MqueryCID','Exten: 0155$phone','Context: default','Channel: Local/0155" . $phone . "@default','Priority: 1','Callerid: $CIDstring','Timeout: 120000','','Variable: var_teste=$path','','');";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Insert na auto calls
                $stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$VARDB_server','$campaign_id','SENT','$lead_id','$MqueryCID','1','$phone','$NOW_TIME','OUT')";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Insert na user call log para efeitos de logging
                $stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('adminij','$NOW_TIME','MANUAL_DIALNOW','$VARDB_server','$phone','$phone','$lead_id','','')";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Faz update ao vicidial_log    
                
                $stmt = "INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','998','$campaign_id','$NOW_TIME','$epoch_sec','MSG001','1','$phone','adminij','MANUAL','N','demoij','NONE');";
                //$updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Faz update a auto_calls para lhe dar um unique-id
                $stmt = "UPDATE vicidial_auto_calls SET uniqueid='$uniqueid' where lead_id='$lead_id';";
                //$updateQRY = mysql_query($stmt, $link) or die(mysql_error());
    
    
}

//system("python /usr/share/Dixi/tts.py '$data' > /srv/www/htdocs/ivrtts/dixi/files/teste.mp3", $retval);



?>
