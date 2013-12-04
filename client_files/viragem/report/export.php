<?php
        require('../../../ini/dbconnect.php');
        require('../../../ini/user.php');
        
        $user=new user;
    $curTime = date("Y-m-d H:i:s");
    $filename = "report_marc_operador_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" ", "Report:", "Contactos Uteis por Agente"), ";");
    fputcsv($output, array(" ", "De:", $data_inicial), ";");
    fputcsv($output, array(" ", "A:", $data_final), ";");

    foreach ($_POST['camp_options'] as $campanha) {
        
        $q="select campaign_name from vicidial_campaigns where campaign_id='$campanha'";
        $camp_name= mysql_fetch_row(mysql_query($q));
        $camp_name=$camp_name[0];
        
        fputcsv($output, array(" ", "", ""), ";");
        fputcsv($output, array(" ", "Campanha", $camp_name), ";");
        fputcsv($output, array(" ", "Operador", "N Total Uteis", "N Total Registos Trabalhados", "Taxa de Conversao"), ";");

        $qry_user = "select a.user,b.full_name from vicidial_log a inner join vicidial_users b on a.user = b.user where call_date between '$data_inicial 01:00:00' and '$data_final 23:00:00' and campaign_id = '$campanha' and b.user_group = 'Agentes' group by a.user";

        $goUsers = mysql_query($qry_user, $link) or die(mysql_error());

        while ( $curUser = mysql_fetch_row($goUsers)) {


            $curUserid = $curUser[0];

            $qry_status = "select count(status) from (select * from (select * from (select status, lead_id, user from vicidial_log where call_date between '$data_inicial 01:00:00' and '$data_final 23:00:00' and campaign_id = '$campanha' order by call_date DESC) b group by lead_id) c where user LIKE '$curUserid') d";
            //echo $qry_status;
            $qry_status = mysql_query($qry_status, $link) or die(mysql_error());
            $qry_status = mysql_fetch_row($qry_status);
            $total = $qry_status[0];


            $query_marc = "select count(*) from "
                    . "(select * from "
                        . "(select user, status, campaign_id, lead_id "
                            . "from vicidial_log "
                            . "where"
                                . " campaign_id LIKE '$campanha' and"
                                . " user like '$curUserid' AND"
                                . " call_date BETWEEN '$data_inicial 01:00:00' AND"
                                . " '$data_final 23:00:00' order by call_date DESC)"
                            . " a group by lead_id)"
                            . " a inner join "
                    . "(select * "
                        . "from vicidial_campaign_statuses "
                        . "where "
                            . "campaign_id LIKE '$campanha' and"
                            . " customer_contact='Y' ) b "
                            . "on a.status=b.status ";
        //echo $query_marc;
            $query_marc = mysql_query($query_marc, $link) or die(mysql_error());
            $query_marc = mysql_fetch_row($query_marc);
            $contactos_uteis = $query_marc[0];


            $conv_rate = round(($contactos_uteis / $total), 4) * 100;
            //if ($tot_marc > 0) {
                fputcsv($output, array(" ", $curUser[1], $contactos_uteis, $total, $conv_rate . "%"), ";");
            //}


        }
    }

