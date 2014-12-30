<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />

        <script type="text/javascript">

            function ChangeState(td_id, fooid) {

                var IHTML = document.getElementById(td_id).innerHTML;
                var NewIHTML = "";

                if (document.getElementById(td_id).innerHTML.search('Presente') === 0)
                {
                    NewIHTML = IHTML.replace("Presente", "Falta");
                    document.getElementById(td_id).innerHTML = NewIHTML;
                    document.getElementById(td_id).style.background = "#FF1D12";
                    document.getElementById(fooid).value = "falta";
                    return;
                }

                if (document.getElementById(td_id).innerHTML.search('Justificada') === 0)
                {
                    NewIHTML = IHTML.replace("Justificada", "Presente");
                    document.getElementById(td_id).innerHTML = NewIHTML;
                    document.getElementById(td_id).style.background = "#FFFFFF";
                    document.getElementById(fooid).value = "presente";
                    return;
                }

                if (document.getElementById(td_id).innerHTML.search('Férias') === 0)
                {
                    NewIHTML = IHTML.replace("Férias", "Justificada");
                    document.getElementById(td_id).innerHTML = NewIHTML;
                    document.getElementById(td_id).style.background = " #66ff66";
                    document.getElementById(fooid).value = "justificada";
                    return;
                }

                if (document.getElementById(td_id).innerHTML.search('Falta') === 0)
                {
                    NewIHTML = IHTML.replace("Falta", "Férias");
                    document.getElementById(td_id).innerHTML = NewIHTML;
                    document.getElementById(td_id).style.background = "#ccff66";
                    document.getElementById(fooid).value = "ferias";
                    return;
                }

            }


        </script>
        <style>
            table,td {border:1px solid #c0c0c0;}
        </style>


        <?php

        function get_days_in_month($month, $year) {
            return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
        }

        $mes = $_POST['mes'];


        if ($mes == NULL) {

            $mes = $_GET['mes'];
            if ($mes == NULL) {
                $mes = date('m');
            }
        }

        $user = $_GET['user'];


// Ligações às bases de dados #################################

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
        $con1 = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
        if (!$con1) {
            die('Não me consegui ligar 1' . mysql_error());
        }
        mysql_select_db("sips", $con1);



###########################################################

        $verify = mysql_query("SELECT data FROM t_ferias_justificada WHERE user='$user'") or die(mysql_error());
        $ver_result = mysql_fetch_assoc($verify);
        foreach ($_POST as $key => $value) {
            if (ereg("tipo", $key)) {
                if ($value != NULL || $value != "") {
                    $index = substr($key, 4, 2);
                    $post_data_index = "data" . $index;
                    $post_data = $_POST[$post_data_index];

                    $verify = mysql_query("SELECT user FROM t_ferias_justificada WHERE data='$post_data' AND user='$user'") or die(mysql_error());
                    if (mysql_num_rows($verify) == 1) {
                        mysql_query("UPDATE t_ferias_justificada SET tipo='$value' WHERE data='$post_data' AND user='$user'") or die(mysql_error());
                    } else {
                        mysql_query("INSERT INTO t_ferias_justificada (user, tipo, data) VALUES ('$user', '$value', '$post_data')") or die(mysql_error());
                    }
                }
            }
        }

        mysql_close($con1);
        ?>
    </head>

    <body>

        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left"><i class="icon-calendar" style="font-size:32px"></i> Faltas de <i> <?= $user ?> </i></div>
                    <div class="pull-right">
                    <form id=mudames method="post" target="_self">
                        <select name="mes" onchange="document.getElementById('mudames').submit();">
                            <option value="01" <?= ($mes == '01') ? "selected" : "" ?>>Janeiro</option>
                            <option value="02" <?= ($mes == '02') ? "selected" : "" ?>>Fevereiro</option>
                            <option value="03" <?= ($mes == '03') ? "selected" : "" ?>>Março</option>
                            <option value="04" <?= ($mes == '04') ? "selected" : "" ?>>Abril</option>
                            <option value="05" <?= ($mes == '05') ? "selected" : "" ?>>Maio</option>
                            <option value="06" <?= ($mes == '06') ? "selected" : "" ?>>Junho</option>
                            <option value="07" <?= ($mes == '07') ? "selected" : "" ?>>Julho</option>
                            <option value="08" <?= ($mes == '08') ? "selected" : "" ?>>Agosto</option>
                            <option value="09" <?= ($mes == '09') ? "selected" : "" ?>>Setembro</option>
                            <option value="10" <?= ($mes == '10') ? "selected" : "" ?>>Outubro</option>
                            <option value="11" <?= ($mes == '11') ? "selected" : "" ?>>Novembro</option>
                            <option value="12" <?= ($mes == '12') ? "selected" : "" ?>>Dezembro</option>
                        </select>
                    </form>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form target=_self method=POST name=calendario id=calendario>
                        <input type=hidden name=user value='<?= $user ?>'>

                        <table style='width:90%; margin-top:16px;' align='center'>
                            <tbody>
                                <tr>
                                    <?php
                                    //if ($mes = "") {$mes = date('m');}
                                    $ano = date('Y');
                                    $semidata = $ano . "-" . $mes . "-";
                                    $faltas = 0;

                                    for ($i = 1; $i < get_days_in_month($mes, $ano) + 1; $i++) {

                                        $datainicio = $ano . "-" . $mes . "-" . $i;
                                        $diafim = $i + 1;
                                        $datafim = $ano . "-" . $mes . "-" . $diafim;

                                        $estedia = $semidata . $i;

                                        $con2 = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");

                                        if (!$con2) {
                                            die('Não me consegui ligar' . mysql_error());
                                        }
                                        mysql_select_db("asterisk", $con2);

                                        $login = mysql_query("SELECT * FROM vicidial_user_log WHERE event_date > '$datainicio' AND event_date < '$datafim' AND user = '$user' AND event='LOGIN' ORDER BY event_epoch") or die(mysql_error());
                                        $rlogin = mysql_fetch_assoc($login);

                                        mysql_close($con2);

                                        if ($rlogin['user'] == "") {
                                            $presenca[$i] = "Falta";
                                        } else {
                                            $presenca[$i] = "Presente";
                                        }


                                        $con1 = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");

                                        if (!$con1) {
                                            die('Não me consegui ligar' . mysql_error());
                                        }
                                        mysql_select_db("sips", $con1);


                                        $query = mysql_query("SELECT tipo FROM t_ferias_justificada WHERE user='$user' AND data='$estedia' ") or die(mysql_error());

                                        if (mysql_num_rows($query) > 0) {
                                            $queryresult = mysql_fetch_assoc($query);
                                            if ($queryresult['tipo'] == 'ferias') {
                                                $presenca[$i] = "Férias";
                                            } elseif ($queryresult['tipo'] == 'falta') {
                                                $presenca[$i] = "Falta";
                                            } elseif ($queryresult['tipo'] == 'justificada') {
                                                $presenca[$i] = "Justificada";
                                            } elseif ($queryresult['tipo'] == 'presente') {
                                                $presenca[$i] = "Presente";
                                            }
                                        }

                                        mysql_close($con1);


                                        $h = mktime(0, 0, 0, $mes, date($i), date("Y"));
                                        $d = intval(date("d", $h));

                                        $m = date("F", $h);

                                        $w = date("l", $h);

                                        switch ($m) {
                                            case "January": $m = "de Janeiro";
                                                break;
                                            case "February": $m = "de Fevereiro";
                                                break;
                                            case "March": $m = "de Março";
                                                break;
                                            case "April": $m = "de Abril";
                                                break;
                                            case "May": $m = "de Maio";
                                                break;
                                            case "June": $m = "de Junho";
                                                break;
                                            case "July": $m = "de Julho";
                                                break;
                                            case "August": $m = "de Agosto";
                                                break;
                                            case "September": $m = "de Setembro";
                                                break;
                                            case "October": $m = "de Outubro";
                                                break;
                                            case "November": $m = "de Novembro";
                                                break;
                                            case "December": $m = "de Dezembro";
                                                break;
                                        }


                                        switch ($w) {
                                            case "Monday": $w = "Segunda-Feira, ";
                                                break;
                                            case "Tuesday": $w = "Terça-Feira,";
                                                break;
                                            case "Wednesday": $w = "Quarta-Feira,";
                                                break;
                                            case "Thursday": $w = "Quinta-Feira,";
                                                break;
                                            case "Friday": $w = "Sexta-Feira,";
                                                break;
                                            case "Saturday": $w = "Sábado,";
                                                break;
                                            case "Sunday": $w = "Domingo,";
                                                break;
                                        }


                                        echo "<td class=calendario height='90' width='90'";

                                        if ($w == "Domingo," /*|| $w == "Sábado,"*/) {
                                            echo "style='background:#E3F0F9; border:1px solid #c0c0c0;' ";
                                            echo (" align='center' id=dia" . $i . " onclick=\"ChangeState('dia" . $i . "');\"><br><br>" . $w . "<br>" . $d . " " . $m . "</td> ");
                                        } else {
                                            #  presença = #000 / falta = #FF1D12 / ferias = #ccff66 / justificada = #66ff66         
                                            
                                            $logs=array("LOGIN"=>"0","LOGOUT"=>"0");
                                            
                                            if ($mes == date('m')) {
                                                if ($i > date('d')) {
                                                    echo (" align='center'><br><br>" . $w . "<br>" . $d . " " . $m);
                                                } else {
                                                    if ($presenca[$i] == 'Falta') {
                                                        echo "bgcolor='#FF1D12'";
                                                    }
                                                    if ($presenca[$i] == 'Justificada') {
                                                        echo "bgcolor='#66ff66'";
                                                    }
                                                    if ($presenca[$i] == 'Férias') {
                                                        echo "bgcolor='#ccff66'";
                                                    }
                                                    if ($presenca[$i] == "Presente"){
                                                        
                                                      $query1= "SELECT ifnull(LOGIN,0) LOGIN, ifnull(LOGOUT,0) LOGOUT FROM (SELECT user,`event_date` LOGIN FROM `vicidial_user_log` WHERE `event_date` between '$ano-$mes-".sprintf("%02d",$i)."' AND '$ano-$mes-".sprintf("%02d",($diafim))."' AND event='LOGIN' ORDER BY event_epoch ASC ) c 
                                                        LEFT JOIN (SELECT user,`event_date` LOGOUT FROM `vicidial_user_log` WHERE `event_date` between '$ano-$mes-".sprintf("%02d",$i)."' AND '$ano-$mes-".sprintf("%02d",($diafim))."' AND event='LOGOUT' ORDER BY event_epoch DESC ) d ON c.user=d.user WHERE c.`user`='$user'";
               
                                                      $con6 = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
                                                        if (!$con6) {
                                                            die('Não me consegui ligar' . mysql_error());
                                                        }
                                                        mysql_select_db("asterisk", $con6);

                                                        $result = mysql_query($query1) or die(mysql_error());
                                                        $logs = mysql_fetch_assoc($result);

                                                        mysql_close($con6);
                                                    }
                                                    
                                                        $logins = (($presenca[$i] == "Presente") ? "<br>LOGIN: ".date("G\H:i\M", strtotime($logs[LOGIN]))."<br>LOGOUT:  ".date("G\H:i\M", strtotime($logs[LOGOUT]))."<br>" : "<br><br>");
                                                    echo " align='center' id=dia" . $i . " onclick=\"ChangeState('dia" . $i . "', 'tipo" . $i . "');\">" . $presenca[$i] . $logins . $w . "<br>" . $d . " " . $m ;
                                                }
                                            } else {
                                                if ($presenca[$i] == 'Falta') {
                                                    echo "bgcolor='#FF1D12'";
                                                    $faltas = $faltas + 1;
                                                }
                                                if ($presenca[$i] == 'Justificada') {
                                                    echo "bgcolor='#66ff66'";
                                                }
                                                if ($presenca[$i] == 'Férias') {
                                                    echo "bgcolor='#ccff66'";
                                                }                                
                                                if ($presenca[$i] == "Presente"){
                                                        
                                                      $query1= "SELECT ifnull(LOGIN,0) LOGIN, ifnull(LOGOUT,0) LOGOUT FROM (SELECT user,`event_date` LOGIN FROM `vicidial_user_log` WHERE `event_date` between '$ano-$mes-".sprintf("%02d",$i)."' AND '$ano-$mes-".sprintf("%02d",($diafim))."' AND event='LOGIN' ORDER BY event_epoch ASC ) c 
                                                        LEFT JOIN (SELECT user,`event_date` LOGOUT FROM `vicidial_user_log` WHERE `event_date` between '$ano-$mes-".sprintf("%02d",$i)."' AND '$ano-$mes-".sprintf("%02d",($diafim))."' AND event='LOGOUT' ORDER BY event_epoch DESC ) d ON c.user=d.user WHERE c.`user`='$user'";
               
                                                      $con6 = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
                                                        if (!$con6) {
                                                            die('Não me consegui ligar' . mysql_error());
                                                        }
                                                        mysql_select_db("asterisk", $con6);

                                                        $result = mysql_query($query1) or die(mysql_error());
                                                        $logs = mysql_fetch_assoc($result);

                                                        mysql_close($con6);
                                                        
                                                    }
                                                    $logins = (($presenca[$i] == "Presente") ? "<br>LOGIN: ".date("G\H:i\M", strtotime($logs[LOGIN]))."<br>LOGOUT:  ".date("G\H:i\M", strtotime($logs[LOGOUT]))."<br>" : "<br><br>");
                                                echo " align='center' id=dia" . $i . " onclick=\"ChangeState('dia" . $i . "', 'tipo" . $i . "');\">" . $presenca[$i] . $logins . $w . "<br>" . $d . " " . $m ;
                                            }
                                        }
                                        echo "<input type=hidden id='data$i' name='data$i' value='$ano-$mes-$i' >";
                                        echo "<input type=hidden id='tipo$i' name='tipo$i' value='' >";
                                        echo "<input type=hidden name=mes value='$mes' >";
                                        echo "</td>";
                                        if ($i == 7 || $i == 14 || $i == 21 || $i == 28) {
                                            echo "</tr><tr>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>

                        <div class="clear"></div>
                        <div class="right">
                            <button class="btn btn-primary">Gravar</button>
                            <input type="button" class="btn" value="Voltar" onclick='location.replace("listausers.php");' />
                        </div>
                        <div class="clear"></div>

                    </form> 
                </div>
            </div>
        </div>
    </body>
</html>