<?

ini_set("display_errors", "1");

require("../../../ini/dbconnect.php");
//require("../functions/functions.php");



foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


if (isset($pop_din_fields)) {

    $din_fields = "SELECT display_name FROM vicidial_list_ref WHERE campaign_id LIKE '$campaign_id' AND active LIKE '1' ORDER BY field_order ASC";
    $din_fields = mysql_query($din_fields, $link) or die(mysql_error());
    $din_fields_list = "";

    for ($i = 0; $i < mysql_num_rows($din_fields); $i++) {

        $curField = mysql_fetch_assoc($din_fields);
        $din_fields_list .= "<option value=" . $curField['display_name'] . " >" . $curField['display_name'] . "</option>";
    }

    echo $din_fields_list;
}


if (isset($aceitarficheiro)) {

    $repQry = "REPLACE INTO sips_ivr_files (`path`,`descr`,`campaign_id`,`posicao`) VALUES ('" . $fname . "','$ficheiro','" . $campaign_id . "',$posicao)";
    #echo $repQry;
    $repQry = mysql_query($repQry, $link) or die(mysql_error());
}

if (isset($gmd)) {

    $repQry = "REPLACE INTO sips_ivr_files (`descr`,`campaign_id`,`posicao`,`mdin`) VALUES ('$ficheiro','" . $campaign_id . "','2','$md')";
 //   echo $repQry;
    $repQry = mysql_query($repQry, $link) or die(mysql_error());
}

if (isset($recordSelector)) {

    $selAudio = "SELECT * FROM sips_ivr_files where campaign_id='$campaign_id' and posicao IN ('1', '3');";
    $selAudio = mysql_query($selAudio, $link) or die(mysql_error());


    $audioInicial = "<select id='audioinicialselect'>";
    $audioFinal = "<select id='audiofinalselect'>";

    for ($i = 0; $i < mysql_num_rows($selAudio); $i++) {
        $curRow = mysql_fetch_assoc($selAudio);

        if ($curRow['posicao'] == '1') {
            $audioInicial .= "<option value=" . $curRow['path'] . " >" . $curRow['descr'] . "</option>";
        } elseif ($curRow['posicao'] == '3') {
            $audioFinal .= "<option value=" . $curRow['path'] . " >" . $curRow['descr'] . "</option>";
        }
    }

    $audioInicial .= "</select>";
    $audioFinal .= "</select>";

    echo $audioInicial;
    echo "&&&";
    echo $audioFinal;
}


// Processador para WAV
if (isset($_POST['data'])) {
    $frase = $_POST['data'];
    $lingua = $_POST['lang'];


    if (isset($din)) {
        $split = array();
        $t = preg_match_all('/\[(.*?)\]/s', $frase, $split);


        $total_fields = max(array_map('count', $split));

        $qryStr = "SELECT Name,Display_name from vicidial_list_ref WHERE campaign_id LIKE '$campaign_id' AND Display_name IN (";

        for ($i = 0; $i < $total_fields; $i++) {

            $qryStr .= "'" . $split[1][$i] . "'";
            if ($i != $total_fields - 1) {
                $qryStr .= ",";
            } else {
                $qryStr .= ");";
            }
        }

        $qryName = mysql_query($qryStr, $link);


        $name_ref = array();

        $selVal = "SELECT ";

        for ($i = 0; $i < mysql_num_rows($qryName); $i++) {

            $curRow = mysql_fetch_assoc($qryName);

            $selVal .= $curRow['Name'];
            $name_ref[$curRow['Display_name']] = $curRow['Name'];

            if ($i != mysql_num_rows($qryName) - 1) {
                $selVal .= ",";
            }
        }


        $selVal .= " FROM vicidial_list a inner join vicidial_lists b on a.list_id = b.list_id WHERE b.campaign_id LIKE '$campaign_id' LIMIT 1";
        $retLead = mysql_query($selVal, $link);

        #print_r($split);
        #print_r($name_ref);

        $curLead = mysql_fetch_assoc($retLead);

        for ($i = 0; $i < $total_fields; $i++) {

            $frase = str_replace("[" . $split[1][$i] . "]", $curLead[$name_ref[$split[1][$i]]], $frase);
        }

        echo $frase;
        echo "###";
    }
    
    $var = system("/srv/www/htdocs/sips-admin/campaign_manager/wizard_intra_justicia/google2.pl '" . $frase . "', '" . $lingua . "' '' '".$velocidade."'", $retval);
   
   
    if (isset($phone)) {
        
        
        
        
        //$campaign_id = "testeivr";

        $curPath = "/srv/www/htdocs/ivr/" . $var . ".wav";
        $destPath = "/srv/www/htdocs/ivr/" . $campaign_id . "/" . $var . ".wav";



        if (!file_exists("/srv/www/htdocs/ivr/" . $campaign_id)) {
            mkdir("/srv/www/htdocs/ivr/" . $campaign_id, 0777);
        }
 
        if (copy($curPath, $destPath)) {
            unlink($curPath);
        }

        // !!!!!!!!!!!!!!!!!!!!FALTA FAZER AQUI A CONVERSÃO PARA SLN 8000 com o truque 850!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $destfile = "/srv/www/htdocs/ivr/" . $campaign_id . "/" . $var . ".sln";
        
     
        
        $sox = "sox $destPath -q -r 8000 -t wav $destfile";

        #echo $sox;

        $ret = system($sox); 
        
                

                $path = "/srv/www/htdocs/ivr/".$campaign_id."/".$var;
                $NOW_TIME = date("Y-m-d H:i:s");
                
                //// Processo de introduzir uma chamada real no servidor (para efeitos de tempos, estatisticas, lead record, etc)
                
                //Vamos verificar se a lead existe em sistema
                $stmt = "SELECT lead_id FROM vicidial_list where phone_number='$phone' order by modify_date desc LIMIT 1;";
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
                $stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','192.168.1.101','','Originate','$MqueryCID','Exten: 0155$phone','Context: default','Channel: Local/0155" . $phone . "@default','Priority: 1','Callerid: $CIDstring','Timeout: 120000','','Variable: var_teste=$path','','');";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Insert na auto calls
                $stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('192.168.1.101','$campaign_id','SENT','$lead_id','$MqueryCID','1','$phone','$NOW_TIME','OUT')";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Insert na user call log para efeitos de logging
                $stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('adminij','$NOW_TIME','MANUAL_DIALNOW','192.168.1.101','$phone','$phone','$lead_id','','')";
                $updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Faz update ao vicidial_log    
                
                $stmt = "INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','998','$campaign_id','$NOW_TIME','$epoch_sec','MSG001','1','$phone','adminij','MANUAL','N','demoij','NONE');";
                //$updateQRY = mysql_query($stmt, $link) or die(mysql_error());
                // Faz update a auto_calls para lhe dar um unique-id
                $stmt = "UPDATE vicidial_auto_calls SET uniqueid='$uniqueid' where lead_id='$lead_id';";
                //$updateQRY = mysql_query($stmt, $link) or die(mysql_error());
              
            
    }
}
// Processador para WAV
// Apagar Ficheiros Inutilizados
if (isset($_POST['del_files'])) {

    foreach ($del_files as $key) {

        if (file_exists("../ivr/" . $key . ".wav")) {
            unlink("../ivr/" . $key . ".wav");
        }
    }
}
// Apagar Ficheiros Inutilizados
// Guardar ficheiro a utilizar

if (isset($_POST['ficheiro_processar'])) {



    $curPath = "/srv/www/htdocs/ivr/" . $ficheiro_processar . ".wav";
    $destPath = "/srv/www/htdocs/ivr/" . $campaign_id . "/" . $ficheiro_processar . ".wav";



    if (!file_exists("/srv/www/htdocs/ivr/" . $campaign_id)) {
        mkdir("/srv/www/htdocs/ivr/" . $campaign_id, 0777);
    }

    if (copy($curPath, $destPath)) {
        unlink($curPath);
    }
    
    $curPath = "/srv/www/htdocs/ivr/" . $ficheiro_processar . ".sln";
    $destPath = "/srv/www/htdocs/ivr/" . $campaign_id . "/" . $ficheiro_processar . ".sln";
    
    if (copy($curPath, $destPath)) {
        unlink($curPath);
    }

    $qryIns = "REPLACE INTO sips_ivr_files (`path`,`descr`,`campaign_id`, `posicao`) VALUES ('" . $campaign_id . "/" . $ficheiro_processar . "','" . $desc . "','" . $campaign_id . "',$posicao)";
    $qry = mysql_query($qryIns, $link) or die(mysql_error());
}


// Guardar ficheiro a utilizar
?>