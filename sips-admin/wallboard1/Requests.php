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
        $query = "DELETE FROM WallBoard_Layout1 WHERE id=$id_layout";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "DELETE FROM WallBoard1 WHERE id_layout=$id_layout";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case 'edit_Layout':
        $query = "UPDATE WallBoard_Layout1 SET Name='$name'  WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;



    case 'insert_wbe':
        $query = "INSERT INTO WallBoard1 (name,id_layout,pos_x,pos_y,width, height, update_time,graph_type) VALUES ('$name',$id_layout,$pos_x,$pos_y,$width,$height,$update_time,$graph_type)";
        $query = mysql_query($query, $link) or die(mysql_error());
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
                $dataset[] = array(id => $row1["id"], id_wallboard => $row1["id_wallboard"], codigo_query => $row1["codigo_query"], opcao_query => $row2["opcao_query"], tempo => $row1["tempo"], user => $row1["user"], user_group => $row1["user_group"], campaign_id => $row1["campaign_id"], linha_inbound => $row1["linha_inbound"], mode => $row1["mode"], status_feedback => $row1["status_feedback"], chamadas => $row1["chamadas"]);
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
        $query = "INSERT INTO `asterisk`.`WallBoard_Dataset1` (id_wallboard, codigo_query,tempo,user,user_group,campaign_id,linha_inbound,mode,status_feedback,chamadas) VALUES ($id_wallboard, $codigo_query,$tempo,'$user','$user_group','$campaign_id','$linha_inbound',$mode,'$status_feedback','$chamadas')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case 'get_dataset':
        $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id_wallboard`=$id_wallboard";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], codigo_query => $row["codigo_query"], id_wallboard => $row["id_wallboard"], tempo => $row["tempo"], user => $row["user"], user_group => $row["user_group"], campaign_id => $row["campaign_id"], linha_inbound => $row["linha_inbound"], mode => $row["mode"], status_feedback => $row["status_feedback"], chamadas => $row["chamadas"]);
        }
        echo json_encode($js);
        break;


    case 'get_dataset_single':
        $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id`=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], codigo_query => $row["codigo_query"], id_wallboard => $row["id_wallboard"], tempo => $row["tempo"], user => $row["user"], user_group => $row["user_group"], campaign_id => $row["campaign_id"], linha_inbound => $row["linha_inbound"], mode => $row["mode"], status_feedback => $row["status_feedback"], chamadas => $row["chamadas"]);
        }
        echo json_encode($js);
        break;


 





    case 'get_query':
        $query = "SELECT * from WallBoard_Query1 where type_query=$graph_type";

        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], query_text_inbound => $row["query_text_inbound"], query_text_outbound => $row["query_text_outbound"], opcao_query => $row["opcao_query"], type_query => $row["type_query"], codigo => $row["codigo"]);
        }
        echo json_encode($js);
        break;

        


//graficos-----------------------------------------
    case '1'://real time - total chamadas inbound/outbound
//falta blended(inbound+outbound)
        


        for ($i = 0; $i < count($datasets); $i++) {


            $query = "SELECT * FROM `WallBoard_Dataset1` WHERE `id`=" . $datasets[$i]["id"];

            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {

                $temp = array();
                $query2 = "SELECT * from WallBoard_Query1 where codigo=" . $row['codigo_query'];

                $query2 = mysql_query($query2, $link) or die(mysql_error());
                while ($row2 = mysql_fetch_assoc($query2)) {


                    if ($row["mode"] == 1)
                        $selected_query = $row2["query_text_inbound"];
                    if ($row["mode"] == 2)
                        $selected_query = $row2["query_text_outbound"];




                    $round_numerator = 60 * 5;
                    $rounded_time = ( round(time() / $round_numerator) * $round_numerator );
                    $rounded_time = date("Y-m-d H:i:s", $rounded_time);
                    $selected_query = str_replace("now()", "'" . $rounded_time . "'", $selected_query);






                    //SUbstituição das variaveis
                    $selected_query = str_replace('$hour', $row["tempo"], $selected_query);
                    $selected_query = str_replace('$user', $row["user"], $selected_query);
                    $selected_query = str_replace('$user_group', $row["user_group"], $selected_query);
                    $selected_query = str_replace('$campaign_id', $row["campaign_id"], $selected_query);
                    $selected_query = str_replace('$linha_inbound', $row["linha_inbound"], $selected_query);
                    $selected_query = str_replace('$status', $row["status_feedback"], $selected_query);
                    $selected_query = str_replace('$chamadas', $row["chamadas"], $selected_query);





                    $query3 = $selected_query;

                    $query3 = mysql_query($query3, $link) or die(mysql_error());

                    while ($row3 = mysql_fetch_assoc($query3)) {

                        $temp[] = array(lead_id => $row3["lead_id"], call_date => $row3["call_date"]);
                    }


                    $t = strtotime($rounded_time);
                    $t2 = strtotime('-1 hours', $t);
                    $begin_time = date("Y-m-d H:i:s", $t2);




                    $t = strtotime($begin_time);
                    $t2 = strtotime('+5 minutes', $t);
                    $end_time = date("Y-m-d H:i:s", $t2);


                    for ($k = 0; $k < 60; $k = $k + 5) {
                        $leads = 0;

                        foreach ($temp as $kev => $row) {
                            if ($row["call_date"] >= $begin_time && $row["call_date"] <= $end_time)
                                $leads = $row["lead_id"];
                        }

                        $js[] = array(leads => $leads, call_date => $begin_time);

                        $t = strtotime($rounded_time);
                        $t2 = strtotime('+5 minutes', $t);
                        $rounded_time = date("Y-m-d H:i:s", $t2);



                        $t = strtotime($rounded_time);
                        $t2 = strtotime('-1 hours', $t);
                        $begin_time = date("Y-m-d H:i:s", $t2);

                        $t = strtotime($begin_time);
                        $t2 = strtotime('+5 minutes', $t);
                        $end_time = date("Y-m-d H:i:s", $t2);
                    }
                }
            }
        }
        echo json_encode($js);
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
        $query = $selected_query;

        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_array($query)) {
            $js[] = array(
                callsToday => $row[0],
                dropsToday => $row[1],
                answersToday => $row[2],
                hold_sec_stat_one => $row[3],
                hold_sec_stat_two => $row[4],
                hold_sec_answer_calls => $row[5],
                hold_sec_drop_calls => $row[6],
                hold_sec_queue_calls => $row[7],
                inGroupDetail => $row[8],
                agent_non_pause_sec => $row[9]);
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