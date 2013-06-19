<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {


    case 'layout':
        $query = "SELECT * from WallBoard_Layout1";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["name"]);
        }
        echo json_encode($js);
        break;

    case 'get_layout':
        $query = "SELECT * from WallBoard_Layout1 where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["Name"]);
        }
        echo json_encode($js);
        break;

    case 'insert_Layout':
        $query = "INSERT INTO WallBoard_Layout1 (name) VALUES ('Layout Nova')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case 'remove_Layout':
        $query = "DELETE  WallBoard_Dataset1 FROM WallBoard_Dataset1 inner join WallBoard1 on WallBoard_Dataset1.id_wallboard=WallBoard1.id WHERE WallBoard1.id_layout=$id_layout";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "DELETE FROM WallBoard1 WHERE id_layout=$id_layout";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "DELETE FROM WallBoard_Layout1 WHERE id=$id_layout";
        $query = mysql_query($query, $link) or die(mysql_error());


        echo json_encode(array(1));
        break;

    case 'edit_Layout':
        $query = "UPDATE WallBoard_Layout1 SET Name='$name'  WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;



    case 'insert_wbe':
        if ($graph_type == "4") {
            $query = "INSERT INTO WallBoard1 (name,id_layout,pos_x,pos_y,width, height, update_time,graph_type) VALUES ('$name',$id_layout,$pos_x,$pos_y,$width,$height,10000,4)";
            $query = mysql_query($query, $link) or die(mysql_error());
            $query = "INSERT INTO `asterisk`.`WallBoard_Dataset1` (id_wallboard, codigo_query,tempo,user,user_group,campaign_id,linha_inbound,mode,status_feedback,chamadas,param1) VALUES (LAST_INSERT_ID(), 0,1,0,0,0,'$update_time',2,0,0,'$id')";
            $query = mysql_query($query, $link) or die(mysql_error());
        } else {


            $query = "INSERT INTO WallBoard1 (name,id_layout,pos_x,pos_y,width, height, update_time,graph_type) VALUES ('$name',$id_layout,$pos_x,$pos_y,$width,$height,$update_time,$graph_type)";
            $query = mysql_query($query, $link) or die(mysql_error());
        }

        echo json_encode(array(1));
        break;


    case 'edit_WBE':
        $query = "UPDATE WallBoard1 SET  pos_x=$pos_x, pos_y=$pos_y, width=$width,height=$height  WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case 'delete_WBE':
        $query = "DELETE FROM WallBoard1 WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "DELETE FROM WallBoard_Dataset1 WHERE id_wallboard=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case 'wbe':
        $query = "SELECT * FROM  WallBoard1  where id_layout='$id_layout'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $query_dataset = "SELECT * FROM `WallBoard_Dataset1` WHERE `id_wallboard`='$row[id]'";
            $query_dataset = mysql_query($query_dataset, $link) or die(mysql_error());
            $dataset = array();
            while ($row1 = mysql_fetch_assoc($query_dataset)) {
                $query2 = "SELECT * FROM  WallBoard_Query1  where codigo=$row1[codigo_query]";
                $query2 = mysql_query($query2, $link) or die(mysql_error());
                $row2 = mysql_fetch_assoc($query2);
                $dataset[] = array(id => $row1["id"], id_wallboard => $row1["id_wallboard"], codigo_query => $row1["codigo_query"], opcao_query => $row2["opcao_query"], tempo => $row1["tempo"], user => $row1["user"], user_group => $row1["user_group"], campaign_id => $row1["campaign_id"], linha_inbound => $row1["linha_inbound"], mode => $row1["mode"], status_feedback => $row1["status_feedback"], chamadas => $row1["chamadas"], param1 => $row1["param1"]);
            }
            $js[] = array(id => $row["id"], id_layout => $row["id_layout"], name => $row["name"], pos_x => $row["pos_x"], pos_y => $row["pos_y"], width => $row["width"], height => $row["height"], update_time => $row["update_time"], graph_type => $row["graph_type"], dataset => $dataset);
        }
        echo json_encode($js);
        break;





    case 'remove_dataset':
        $query = "Delete from WallBoard_Dataset1 where id='$id' ";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case 'insert_dataset':
        $query = "INSERT INTO `asterisk`.`WallBoard_Dataset1` (id_wallboard, codigo_query,tempo,user,user_group,campaign_id,linha_inbound,mode,status_feedback,chamadas,param1) VALUES ($id_wallboard, $codigo_query,$tempo,'$user','$user_group','$campaign_id','$linha_inbound',$mode,'$status_feedback','$chamadas','$param1')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case 'get_dataset':
        $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id_wallboard`=$id_wallboard";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], codigo_query => $row["codigo_query"], id_wallboard => $row["id_wallboard"], tempo => $row["tempo"], user => $row["user"], user_group => $row["user_group"], campaign_id => $row["campaign_id"], linha_inbound => $row["linha_inbound"], mode => $row["mode"], status_feedback => $row["status_feedback"], chamadas => $row["chamadas"], param1 => $row1["param1"]);
        }
        echo json_encode($js);
        break;


    case 'get_dataset_single':
        $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id`=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], codigo_query => $row["codigo_query"], id_wallboard => $row["id_wallboard"], tempo => $row["tempo"], user => $row["user"], user_group => $row["user_group"], campaign_id => $row["campaign_id"], linha_inbound => $row["linha_inbound"], mode => $row["mode"], status_feedback => $row["status_feedback"], chamadas => $row["chamadas"], param1 => $row1["param1"]);
        }
        echo json_encode($js);
        break;

    case 'edit_dataset':
        $query = "UPDATE WallBoard_Dataset1 SET  codigo_query=$codigo_query,user='$user',user_group='$user_group',campaign_id='$campaign_id',linha_inbound='$linha_inbound',mode=$mode,status_feedback='$status_feedback',chamadas='$chamadas',param1='$param1'  WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;






    case 'get_query':
        $query = "SELECT * from WallBoard_Query1 where type_query=$graph_type";

        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], query_text_inbound => $row["query_text_inbound"], query_text_outbound => $row["query_text_outbound"], opcao_query => $row["opcao_query"], type_query => $row["type_query"], codigo => $row["codigo"]);
        }
        echo json_encode($js);
        break;




    case 'get_query_by_code':

        $query = "SELECT * from WallBoard_Query1 where codigo=$codigo";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], query_text_inbound => $row["query_text_inbound"], query_text_outbound => $row["query_text_outbound"], opcao_query => $row["opcao_query"], type_query => $row["type_query"], codigo => $row["codigo"]);
        }
        echo json_encode($js);
        break;




//graficos-----------------------------------------
    case '1'://real time - total chamadas inbound/outbound



        for ($i = 0; $i < count($datasets); $i++) {
            $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id`=" . $datasets[$i]["id"];
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $query2 = "SELECT * from WallBoard_Query1 where codigo=" . $row['codigo_query'];
                $query2 = mysql_query($query2, $link) or die(mysql_error());
                while ($row2 = mysql_fetch_assoc($query2)) {
                    $temp = array();
                    $temp1 = array();
                    $temp2 = array();
                    $round_numerator = 60 * 5;
                    $rounded_time = ( round(time() / $round_numerator) * $round_numerator );
                    $rounded_time = date("Y-m-d H:i:s", $rounded_time);
                    if ($row["mode"] == 1 || $row["mode"] == 2) {//INBOUND E OUTBOUND------------------------------------
                        if ($row["mode"] == 1)
                            $selected_query = $row2["query_text_inbound"];
                        if ($row["mode"] == 2)
                            $selected_query = $row2["query_text_outbound"];
                        //SUbstituição das variaveis
                        $selected_query = str_replace("now()", "'" . $rounded_time . "'", $selected_query);
                        $selected_query = str_replace('$hour', $row["tempo"], $selected_query);
                        $selected_query = str_replace('$user', $row["user"], $selected_query);
                        $selected_query = str_replace('$user_group', $row["user_group"], $selected_query);
                        $selected_query = str_replace('$campaign_id', $row["campaign_id"], $selected_query);
                        $selected_query = str_replace('$linha_inbound', $row["linha_inbound"], $selected_query);
                        $selected_query = str_replace('$status', $row["status_feedback"], $selected_query);
                        $selected_query = str_replace('$chamadas', $row["chamadas"], $selected_query);
                        $selected_query = mysql_query($selected_query, $link) or die(mysql_error());
                        while ($row3 = mysql_fetch_assoc($selected_query)) {
                            $temp[] = array(lead_id => $row3["lead_id"], call_date => $row3["call_date"]);
                        }
                    } else {//BLENDED-----------------------------------------------------------
                        $inbound = $row2["query_text_inbound"];
                        $outbound = $row2["query_text_outbound"];
                        //SUbstituição das variaveis
                        $inbound = str_replace("now()", "'" . $rounded_time . "'", $inbound);
                        $inbound = str_replace('$hour', $row["tempo"], $inbound);
                        $inbound = str_replace('$user', $row["user"], $inbound);
                        $inbound = str_replace('$user_group', $row["user_group"], $inbound);
                        $inbound = str_replace('$campaign_id', $row["campaign_id"], $inbound);
                        $inbound = str_replace('$linha_inbound', $row["linha_inbound"], $inbound);
                        $inbound = str_replace('$status', $row["status_feedback"], $inbound);
                        $inbound = str_replace('$chamadas', $row["chamadas"], $inbound);
                        //SUbstituição das variaveis
                        $outbound = str_replace("now()", "'" . $rounded_time . "'", $outbound);
                        $outbound = str_replace('$hour', $row["tempo"], $outbound);
                        $outbound = str_replace('$user', $row["user"], $outbound);
                        $outbound = str_replace('$user_group', $row["user_group"], $outbound);
                        $outbound = str_replace('$campaign_id', $row["campaign_id"], $outbound);
                        $outbound = str_replace('$linha_inbound', $row["linha_inbound"], $outbound);
                        $outbound = str_replace('$status', $row["status_feedback"], $outbound);
                        $outbound = str_replace('$chamadas', $row["chamadas"], $outbound);
                        $inbound = mysql_query($inbound, $link) or die(mysql_error());
                        while ($row3 = mysql_fetch_assoc($inbound)) {
                            $temp1[] = array(lead_id => $row3["lead_id"], call_date => $row3["call_date"]);
                        }
                        $outbound = mysql_query($outbound, $link) or die(mysql_error());
                        while ($row4 = mysql_fetch_assoc($outbound)) {
                            $temp2[] = array(lead_id => $row4["lead_id"], call_date => $row4["call_date"]);
                        }
                        $temp = array_merge($temp1, $temp2);
                    }
                    $t = strtotime($rounded_time);
                    $t2 = strtotime('-1 hours', $t);
                    $begin_time = date("Y-m-d H:i:s", $t2);
                    $t = strtotime($begin_time);
                    $t2 = strtotime('+1 minutes', $t);
                    $end_time = date("Y-m-d H:i:s", $t2);
                    $jc = array();
                    for ($k = 0; $k < 60; $k++) {
                        $leads = 0;
                        foreach ($temp as $kev => $row) {
                            if ($row["call_date"] >= $begin_time && $row["call_date"] <= $end_time)
                                $leads = $leads + 1;
                        }
                        $jc[] = array(leads => $leads, call_date => $begin_time);
                        $t = strtotime($rounded_time);
                        $t2 = strtotime('+1 minutes', $t);
                        $rounded_time = date("Y-m-d H:i:s", $t2);
                        $t = strtotime($rounded_time);
                        $t2 = strtotime('-1 hours', $t);
                        $begin_time = date("Y-m-d H:i:s", $t2);
                        $t = strtotime($begin_time);
                        $t2 = strtotime('+1 minutes', $t);
                        $end_time = date("Y-m-d H:i:s", $t2);
                    }
                    $js[] = $jc;
                }
            }
        }

        echo json_encode(array("datasets" => $js));
        break;




    case '2'://barras - total vendas por user
        $query = $selected_query;
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(user => $row["var1"], status_count => $row["var2"]);
        }
        echo json_encode($js);
        break;

    case '3':// tarte - total feedbacks por user
        $query = $selected_query;
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(status_name => $row["var1"], count => $row["var2"]);
        }
        echo json_encode($js);
        break;

    case '4'://inbound
        $stmtB = "select calls_today,drops_today,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,hold_sec_stat_one,hold_sec_stat_two,hold_sec_answer_calls,hold_sec_drop_calls,hold_sec_queue_calls,campaign_id from vicidial_campaign_stats where campaign_id='$group_id'";

        $rslt = mysql_query($stmtB, $link);
        while ($row = mysql_fetch_row($rslt)) { 

                $callsTODAY = $row[0];
                $dropsTODAY = $row[1];
                $answersTODAY = $row[2];
                $VSCcat1 = $row[3];
                $VSCcat1tally = $row[4];
                $VSCcat2 = $row[5];
                $VSCcat2tally = $row[6];
                $VSCcat3 = $row[7];
                $VSCcat3tally = $row[8];
                $VSCcat4 = $row[9];
                $VSCcat4tally = $row[10];
                $hold_sec_stat_one = $row[11];
                $hold_sec_stat_two = $row[12];
                $hold_sec_answer_calls = $row[13];
                $hold_sec_drop_calls = $row[14];
                $hold_sec_queue_calls = $row[15];
                $ingroupdetail = $row[16];
                if (($dropsTODAY > 0) and ($answersTODAY > 0)) {
                    $drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
                    $drpctTODAY = round($drpctTODAY, 2);
                    $drpctTODAY = sprintf("%01.2f", $drpctTODAY);
                } else {
                    $drpctTODAY = 0;
                }

                if ($callsTODAY > 0) {
                    $AVGhold_sec_queue_calls = ($hold_sec_queue_calls / $callsTODAY);
                    $AVGhold_sec_queue_calls = round($AVGhold_sec_queue_calls, 0);
                } else {
                    $AVGhold_sec_queue_calls = 0;
                }

                if ($dropsTODAY > 0) {
                    $AVGhold_sec_drop_calls = ($hold_sec_drop_calls / $dropsTODAY);
                    $AVGhold_sec_drop_calls = round($AVGhold_sec_drop_calls, 0);
                } else {
                    $AVGhold_sec_drop_calls = 0;
                }

                if ($answersTODAY > 0) {
                    $PCThold_sec_stat_one = ( ($hold_sec_stat_one / $answersTODAY) * 100);
                    $PCThold_sec_stat_one = round($PCThold_sec_stat_one, 2);
                    $PCThold_sec_stat_one = sprintf("%01.2f", $PCThold_sec_stat_one);
                    $PCThold_sec_stat_two = ( ($hold_sec_stat_two / $answersTODAY) * 100);
                    $PCThold_sec_stat_two = round($PCThold_sec_stat_two, 2);
                    $PCThold_sec_stat_two = sprintf("%01.2f", $PCThold_sec_stat_two);
                    $AVGhold_sec_answer_calls = ($hold_sec_answer_calls / $answersTODAY);
                    $AVGhold_sec_answer_calls = round($AVGhold_sec_answer_calls, 0);
                    if ($agent_non_pause_sec > 0) {
                        $AVG_ANSWERagent_non_pause_sec = (($answersTODAY / $agent_non_pause_sec) * 60);
                        $AVG_ANSWERagent_non_pause_sec = round($AVG_ANSWERagent_non_pause_sec, 2);
                        $AVG_ANSWERagent_non_pause_sec = sprintf("%01.2f", $AVG_ANSWERagent_non_pause_sec);
                    } else {
                        $AVG_ANSWERagent_non_pause_sec = 0;
                    }
                } else {
                    $PCThold_sec_stat_one = 0;
                    $PCThold_sec_stat_two = 0;
                    $AVGhold_sec_answer_calls = 0;
                    $AVG_ANSWERagent_non_pause_sec = 0;
                }
 $js[] = array(chamadas_efectuadas => $callsTODAY, chamadas_perdidas => $dropsTODAY,chamadas_atendidas => $answersTODAY,tma1=>$PCThold_sec_stat_one,tma2=>$PCThold_sec_stat_two,tme_chamadas_atendidas=>$AVGhold_sec_answer_calls,tme_chamadas_perdidas=>$AVGhold_sec_drop_calls,tme_todas_chamadas=>$AVGhold_sec_queue_calls);
                      
            }
            echo json_encode($js);
            break;

            case 'get_agents':// Inbound agentes,campaign,status
            $query = $query_text;
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = $row["status"];
            }
            echo json_encode($js);
            break;

            case 'inbound_groups_info':// Inbound agentes,campaign,status
            $query = " SELECT answer_sec_pct_rt_stat_one,answer_sec_pct_rt_stat_two FROM vicidial_inbound_groups WHERE group_id='$group_id'";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = array(answer_sec_pct_rt_stat_one => $row["answer_sec_pct_rt_stat_one"], answer_sec_pct_rt_stat_two => $row["answer_sec_pct_rt_stat_two"]);
            }
            echo json_encode($js);
            break;


//graficos-----------------------------------------
//flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS -------------- 
            case 'campaign':
            $query = "SELECT  a.campaign_id,b.campaign_name  FROM  vicidial_campaign_statuses a inner join vicidial_campaigns b on a.campaign_id=b.campaign_id and active='y'  group by  campaign_id ";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = array(campaign_id => $row["campaign_id"], campaign_name => $row["campaign_name"]);
            }
            echo json_encode($js);
            break;


            case 'user':
            $query = "SELECT `user` FROM `vicidial_inbound_group_agents` where user is not null and user!='' group by user";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = $row["user"];
            }
            echo json_encode($js);
            break;
            case 'user_group':
            $query = "SELECT  user_group  as ug FROM  vicidial_log  where call_date between date_sub(NOW(), INTERVAL $param1) and now() and user_group is not NUll group by  user_group ";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = $row["ug"];
            }
            echo json_encode($js);
            break;

            case 'inbound':
            $query = "SELECT group_id,group_name FROM vicidial_inbound_groups";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = array(id => $row["group_id"], name => $row["group_name"]);
            }
            echo json_encode($js);
            break;



            case 'status_venda':
            $query = "SELECT status ,status_name  FROM vicidial_campaign_statuses  group by status";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = array(status_v => $row["status"], status_t => $row["status_name"]);
            }
            echo json_encode($js);
            break;




//flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------
        }
?> 
