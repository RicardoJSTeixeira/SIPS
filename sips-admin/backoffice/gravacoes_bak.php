<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <?
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"];//.$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
        function secondsToTime($seconds) {
            // extract hours
            $hours = floor($seconds / (60 * 60));

            // extract minutes
            $divisor_for_minutes = $seconds % (60 * 60);
            $minutes = floor($divisor_for_minutes / 60);

            // extract the remaining seconds
            $divisor_for_seconds = $divisor_for_minutes % 60;
            $seconds = ceil($divisor_for_seconds);

            // return the final array
            $obj = array(
                "h" => (int) $hours,
                "m" => (int) $minutes,
                "s" => (int) $seconds,
            );
            return $obj;
        }

        if (isset($_GET["user"])) {
            $curUser = $_GET["user"];
        } elseif (isset($_POST["user"])) {
            $curUser = $_POST["user"];
        }
        $bypass = $curUser;

        if (file_exists("/etc/astguiclient.conf")) {
            $DBCagc = file("/etc/astguiclient.conf");
            foreach ($DBCagc as $DBCline) {
                $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/", "", $DBCline);
                if (ereg("^VARDB_server", $DBCline)) {
                    $VARDB_server = $DBCline;
                    $VARDB_server = preg_replace("/.*=/", "", $VARDB_server);
                }
            }
        } else {
            #defaults for DB connection
            $VARDB_server = 'localhost';
        }

        $con = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
        if (!$con) {
            die('Não me consegui ligar' . mysql_error());
        }
        mysql_select_db("asterisk", $con);

        $current_admin = $_SERVER['PHP_AUTH_USER'];
        if($current_admin!="AAL" AND $current_admin!="AMB" AND $current_admin!="ANU" AND $current_admin!="FPO" AND $current_admin!="RGE"){
        $query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
        $query = mysql_fetch_assoc($query);
        $usrgrp = $query['user_group'];
        }$current_admin=strtoupper($current_admin);
       switch ($current_admin) {
           case "AAL":$users="'babrantes','baveiro','bcastelobranco','bcoimbra','bcovilha','bfigueira','bguarda','bleiria','bpombal','btomar','bviseu'";
               break;
           case "AMB":$users="'bbraga','bbraganca','bcedofeita','bbolhao','bgaia','bguimaraes','bboavista','bmatosinhos','bpenafiel','bsantamaria','bviana','bvilareal'";
               break;
           case "ANU":$users="'balmada','bamora','bbeja','bbarreiro','bevora','bfaro','bmontijo','bportalegre','bportimao','bsetubal'";
               break;
           case "FPO":$users="'balvalade','bamadora','bbenfica','bbaixa','bcacem','bcascais','bcaldas','bfunchal','balmirante','ambranchmemmartins','bodivelas','boscar','bourique','bsantarem','btorres','bvilafranca'";
               break;
           case "RGE":$users="'babrantes','baveiro','bcastelobranco','bcoimbra','bcovilha','bfigueira','bguarda','bleiria','bpombal','btomar','bviseu','bbraga','bbraganca','bcedofeita','bbolhao','bgaia','bguimaraes','bboavista','bmatosinhos','bpenafiel','bsantamaria','bviana','bvilareal','balmada','bamora','bbeja','bbarreiro','bevora','bfaro','bmontijo','bportalegre','bportimao','bsetubal','balvalade','bamadora','bbenfica','bbaixa','bcacem','bcascais','bcaldas','bfunchal','balmirante','ambranchmemmartins','bodivelas','boscar','bourique','bsantarem','btorres','bvilafranca'";
               break;

           default:
               break;
       }
        
        if ($query['user_group'] == "ADMIN") {

            $activeUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='Y'") or die(mysql_error());
            $inactiveUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='N'") or die(mysql_error());
        } else {
            if($current_admin!="AAL" AND $current_admin!="AMB" AND $current_admin!="ANU" AND $current_admin!="FPO" AND $current_admin!="RGE"){
            $activeUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='Y' AND user_group LIKE '$usrgrp' ") or die(mysql_error());
            $inactiveUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='N' AND user_group LIKE '$usrgrp' ") or die(mysql_error());}
            else{
            $activeUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='Y' AND user IN ($users) ") or die(mysql_error());
            $inactiveUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='N' AND user IN ($users)") or die(mysql_error());}
        }

        $callResults = mysql_query("SELECT DISTINCT(status) FROM vicidial_list") or die(mysql_error());

        if ($curUser != '') {

            $user = "AND recording_log.user = '$curUser' ";
        } else {
            $user = "";
            switch ($current_admin) {
           case "AAL":$users="AND recording_log.user IN('babrantes','baveiro','bcastelobranco','bcoimbra','bcovilha','bfigueira','bguarda','bleiria','bpombal','btomar','bviseu')";
               break;
           case "AMB":$users="AND recording_log.user IN('bbraga','bbraganca','bcedofeita','bbolhao','bgaia','bguimaraes','bboavista','bmatosinhos','bpenafiel','bsantamaria','bviana','bvilareal')";
               break;
           case "ANU":$users="AND recording_log.user IN('balmada','bamora','bbeja','bbarreiro','bevora','bfaro','bmontijo','bportalegre','bportimao','bsetubal')";
               break;
           case "FPO":$users="AND recording_log.user IN('balvalade','bamadora','bbenfica','bbaixa','bcacem','bcascais','bcaldas','bfunchal','balmirante','ambranchmemmartins','bodivelas','boscar','bourique','bsantarem','btorres','bvilafranca')";
               break;
           case "RGE":$users="AND recording_log.user IN('babrantes','baveiro','bcastelobranco','bcoimbra','bcovilha','bfigueira','bguarda','bleiria','bpombal','btomar','bviseu','bbraga','bbraganca','bcedofeita','bbolhao','bgaia','bguimaraes','bboavista','bmatosinhos','bpenafiel','bsantamaria','bviana','bvilareal','balmada','bamora','bbeja','bbarreiro','bevora','bfaro','bmontijo','bportalegre','bportimao','bsetubal','balvalade','bamadora','bbenfica','bbaixa','bcacem','bcascais','bcaldas','bfunchal','balmirante','ambranchmemmartins','bodivelas','boscar','bourique','bsantarem','btorres','bvilafranca')";
               break;

           default:
               break;
       }
        }


        if (isset($_POST['datainicio'])) {
            if ($_POST['datainicio'] == $_POST['datafim']) {
                $_POST['datafim'] = date("o-m-d", strtotime("+1 day" . $_POST['datafim']));
            }
            $datainicio = $_POST['datainicio'];
            $datafim = $_POST['datafim'];
        } else {
            $datainicio = date('o-m-d');
            $datafim = date('o-m-d', strtotime("+1 day"));
        }



        $datainicio = "AND start_time >= '$datainicio'";
        $datafim = "AND end_time <= '$datafim'";



        if ($_POST['ntlf'] != '') {

            $leadQry = mysql_query("SELECT lead_id FROM vicidial_list WHERE phone_number = '$_POST[ntlf]' or alt_phone= '$_POST[ntlf]' or address3= '$_POST[ntlf]' ") or die(mysql_error());

            #for mysql rows concatenar lead id 

            if (mysql_num_rows($leadQry) > 1) {

                for ($i = 0; $i < mysql_num_rows($leadQry); $i++) {

                    $leadID = mysql_fetch_assoc($leadQry);
                    if ($i == (mysql_num_rows($leadQry) - 1)) {
                        $leadSET .= $leadID['lead_id'];
                    } else {
                        $leadSET .= $leadID['lead_id'] . ",";
                    }
                }
                $recordList = mysql_query("SELECT start_time, end_time, length_in_sec, filename, recording_log.lead_id, recording_log.user, vicidial_list.phone_number, location FROM recording_log INNER JOIN vicidial_list ON recording_log.lead_id = vicidial_list.lead_id WHERE recording_log.lead_id IN ($leadSET) AND length_in_sec >= '20'") or die(mysql_error());
                
                    } else {

                $leadID = mysql_fetch_assoc($leadQry);

                $recordList = mysql_query("SELECT start_time, end_time, length_in_sec, filename, recording_log.lead_id, recording_log.user, vicidial_list.phone_number, location FROM recording_log INNER JOIN vicidial_list ON recording_log.lead_id = vicidial_list.lead_id WHERE recording_log.lead_id = '$leadID[lead_id]'") or die(mysql_error());
            }
        } else {



            if ($_POST['curPage'] != '') {
                $pageNumb = $_POST['curPage'];
            } else {
                $pageNumb = 1;
            }
            $totalPages = mysql_query("SELECT count(*) FROM
				recording_log INNER JOIN vicidial_list ON recording_log.lead_id = vicidial_list.lead_id WHERE 				
				length_in_sec >= '20'
				" . $datainicio . " " . $datafim . "
			" . $user . ";");
            $totalPages = mysql_fetch_array($totalPages);
            $totalPages = intval($totalPages[0] / 15 + 1);
            if ($pageNumb < 1) {
                $pageNumb = 1;
            }
            if ($pageNumb > $totalPages) {
                $pageNumb = $totalPages;
            }
            $limitNumb = ($pageNumb - 1) * 15;



            $qryStr = "SELECT start_time, end_time, length_in_sec, filename, recording_log.lead_id, recording_log.user, vicidial_list.phone_number, location FROM recording_log INNER JOIN vicidial_list ON recording_log.lead_id = vicidial_list.lead_id 
				WHERE 				
				length_in_sec >= '20'
				" . $datainicio . " " . $datafim . "
				" . $user . " ORDER BY start_time ASC LIMIT $limitNumb,15";


            $recordList = mysql_query($qryStr) or die(mysql_error());
        }

        mysql_close($con);
        ?>
        <script type="text/javascript">
            function previouspage() {
                var x = parseInt(document.getElementById('curPage').value);
                x = x - 1;
                document.getElementById('curPage').value = x;
                document.forms.item('filtragravacoes').submit();
            }
            function nextpage() {
                var x = parseInt(document.getElementById('curPage').value);
                x = x + 1;
                document.getElementById('curPage').value = x;
                document.forms.item('filtragravacoes').submit();
            }

            function lastpage(y) {
                var x = parseInt(y);
                document.getElementById('curPage').value = x;
                document.forms.item('filtragravacoes').submit();
            }

            function firstpage() {

                document.getElementById('curPage').value = 1;
                document.forms.item('filtragravacoes').submit();
            }

            function enableAudio(y) {
                document.getElementById(y).style.display = 'block';

                var x = y + 'a';
                document.getElementById(x).style.display = 'none';

            }

            $(function() {
                $("#datainicio").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy-mm-dd"
                });
                $("#datafim").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy-mm-dd"
                });
            })
        </script>
    </head>

    <body>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Gravações</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <div class="row-fluid">
                        <form action="gravacoes.php" target="_self" method="post" name='filtragravacoes' id='filtragravacoes' >
                            <div class="span3">
                                <fieldset>
                                    <legend>Pesquisa</legend>
                                    <label>User</label>
                                    <select name='user' id='user' class="chzn-select">
                                        <optgroup label="Activos"><?=$bypass?>
                                            <?php while ($curUser = mysql_fetch_assoc($activeUsers)) { ?>
                                                <option value='<?= $curUser[user] ?>' <?= ($bypass == $curUser['user']) ? "selected" : "" ?> ><?= $curUser['full_name'] ?></option>
                                            <?php } ?>
                                        </optgroup> 
                                        <optgroup label="Inactivos">
                                            <?php while ($curUser = mysql_fetch_assoc($inactiveUsers)) { ?>
                                                <option value='<?= $curUser[user] ?>' <?= ($bypass == $curUser['user']) ? "selected" : "" ?> ><?= $curUser['full_name'] ?></option>
                                            <?php } ?>
                                        </optgroup> 
                                    </select>
                                    <label>Data Inicio</label>
                                    <input type="date" name='datainicio' id='datainicio' value='<?= $_POST['datainicio'] ?>' >
                                    <label>Data Fim</label>
                                    <input type="date" name='datafim' id='datafim' value='<?= $_POST['datafim']; ?>' >
                                    <label>Nº de Telefone</label>
                                    <input type="text" name='ntlf' class="span" />

                                    <input type="submit" value='Filtrar' class="btn btn-primary" />
                                    <input type="hidden" name='curPage' id='curPage' value='<?= $pageNumb ?>'/>
                                </fieldset>
                            </div>
                            <div class="span9">


                                <table class="table table-mod table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Data Chamada</th>
                                            <th>Duração</th>
                                            <th>Telefone</th>
                                            <th>User</th>
                                            <th>Gravação</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $curpage=curPageURL();
                                        While ($curRecord = mysql_fetch_assoc($recordList)){
                                            //$filename = $curRecord['filename'];
                                            $mp3File = "#";
                                            //file_exists("/var/spool/asterisk/monitorDONE/MP3/$filename-all.mp3"

                                            if (strlen($curRecord[location]) > 0) {
                                                $tmp=explode("/",$curRecord[location]);
                                                $ip=$tmp[2];
                                                $tmp=explode(".",$ip);
                                                $ip=$tmp[3];
                                                
                                                switch ($ip) {
                                                    case "248":
                                                        $port=":20248";
                                                        break;
                                                    case "247":
                                                        $port=":20247";
                                                        break;
                                                    default:
                                                        $port="";
                                                        break;
                                                }
                                                
                                                //$mp3File = "/RECORDINGS/MP3/$filename-all.mp3";
                                                $mp3File = $curpage.$port."/RECORDINGS/MP3/$curRecord[filename]-all.mp3";
                                                $audioPlayer = "Há gravação";
                                            } else {
                                                $audioPlayer = "Não há gravação!";
                                            }
                                            $lenghtInMin = secondsToTime($curRecord['length_in_sec']);
                                            ?>
                                            <tr>
                                                <td><?= $curRecord['start_time'] ?></td>
                                                <td><?= $lenghtInMin['m'] . ":" . $lenghtInMin['s'] ?></td>

                                                <td><?= $curRecord['phone_number'] ?></td>
                                                <td><?= $curRecord['user'] ?></td>
                                                <td>
                                                    <div id='<?= $curRecord['filename'] ?>' name='<?= $curRecord['filename'] ?>' style='display:block' ><?= $audioPlayer ?></div>
                                                </td>
                                                <td>
                                                    <a href='<?= $mp3File ?>' style='font-size: 12px;' ><img src='/images/icons/download.png' alt='Fazer Download' ></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <ul class="pager">
                                    <li class="previous"><a href="#"  onclick='lastpage();'>← First</a></li>
                                    <li class="previous"><a href="#"  onclick='previouspage();'>← Older</a></li>
                                    <li>Pag. <?= $pageNumb ?></li>
                                    <li class="next"><a href="#"  onclick='lastpage(<?= $totalPages ?>);'>Last →</a></li>
                                    <li class="next"><a href="#"  onclick='nextpage();'>Newer →</a></li>
                                </ul>
                            </div>

                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
        </script>
    </body>
</html>
                                    