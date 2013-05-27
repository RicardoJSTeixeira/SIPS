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

    $var = system("/srv/www/htdocs/ivrout/google2.pl '" . $frase . "', '" . $lingua . "' '' '1'", $retval);


    if (isset($phone)) {
        
        //$campaign_id = "teste";

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

                $originateCall = "INSERT INTO vicidial_manager (entry_date, status, response, server_ip, channel, action, callerid, cmd_line_b, cmd_line_c, cmd_line_d, cmd_line_e, cmd_line_f, cmd_line_g, cmd_line_h)
                    VALUES 
                    (NOW(),
                    'NEW',
                    'N',
                    '192.168.1.3',
                    '',
                    'Originate',
                    '',
                    'Context: default',
                    'Priority: 1',
                    'Timeout: 50000',
                    'Channel: Local/01" . $phone . "@default',
                    'Variable: var_teste=$path',
                    '',
                    '')";

                $callIT = mysql_query($originateCall, $link) or die(mysql_error());
            
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