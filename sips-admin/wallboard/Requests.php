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
                    $query = "SELECT * from WallBoard_Layout";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = array(id => $row["id"], name => $row["Name"]);
                    }
                    echo json_encode($js);
                    break;

          case 'get_layout':
                    $query = "SELECT * from WallBoard_Layout where id=$id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = array(id => $row["id"], name => $row["Name"]);
                    }
                    echo json_encode($js);
                    break;

          case 'insert_Layout':
                    $query = "INSERT INTO WallBoard_Layout (name) VALUES ('Layout Nova')";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    echo json_encode(array(1));
                    break;

          case 'remove_Layout':
                    $query = "DELETE FROM WallBoard_Layout WHERE id=$layout_Id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    $query = "DELETE FROM WallBoard WHERE layout_id=$layout_Id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    break;

          case 'edit_Layout':
                    $query = "UPDATE WallBoard_Layout SET Name='$name'  WHERE id=$id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    echo json_encode(array(1));
                    break;

          case 'insert_wbe':
                    $query = "INSERT INTO WallBoard (name, posX ,  posY ,  width ,  height ,  layout_id ,  query_text , opcao_query, update_time , graph_type, param1) VALUES ('$name',$posx,$posy,$width,$height,$layout_id,'$query_text','$opcao_query',$update_time,$graph_type,'$param1');";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    echo json_encode(array(1));
                    break;

          case 'edit_WBE':
                    $query = "UPDATE WallBoard SET  posX=$posX, posY=$posY, width=$width,height=$height, layout_id=$layout_Id  WHERE id=$id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    echo json_encode(array(1));
                    break;

          case 'delete_WBE':
                    $query = "DELETE FROM WallBoard WHERE id=$id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    echo json_encode(array(1));
                    break;

          case 'wbe':
                    $query = "SELECT * FROM  WallBoard  where layout_id=$layout_Id";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
                              $js[] = array(id => $row["id"], name => $row["name"], posX => $row["posX"], posY => $row["posY"], width => $row["width"], height => $row["height"], layout_Id => $row["layout_id"], query_text => $row["query_text"], opcao_query => $row["opcao_query"], update_time => $row["update_time"], graph_type => $row["graph_type"], param1 => $row["param1"]);
                    }
                    echo json_encode($js);
                    break;

          case 'get_query':
                    $query = "SELECT * from WallBoard_Query where type_query=$graph_type ";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = array(id => $row["id"], query_text => $row["query_text"], opcao_query => $row["opcao_query"], type_query => $row["type_query"]);
                    }
                    echo json_encode($js);
                    break;

          //graficos-----------------------------------------
          case '1'://real time - total chamadas inbound/outbound
                    $round_numerator = 60 * 5;
                    $rounded_time = ( round(time() / $round_numerator) * $round_numerator );
                    $rounded_time = date("Y-m-d H:i:s", $rounded_time);
                    $selected_query = str_replace("now()", "'" . $rounded_time . "'", $selected_query);
                    $query = $selected_query;


                    $query = mysql_query($query, $link) or die(mysql_error());

                    while ($row = mysql_fetch_assoc($query)) {

                              $temp[] = array(lead_id => $row["lead_id"], call_date => $row["call_date"]);
                    }


                    $t = strtotime($rounded_time);
                    $t2 = strtotime('-1 hours', $t);
                    $begin_time = date("Y-m-d H:i:s", $t2);




                    $t = strtotime($begin_time);
                    $t2 = strtotime('+5 minutes', $t);
                    $efnd_time = date("Y-m-d H:i:s", $t2);

                    $leads = 0;

                    for ($i = 0; $i < 60; $i = $i + 5) {
                              foreach ($temp as $kev => $aaa) {
                                        if ($aaa["call_date"] >= $begin_time && $aaa["call_date"] <= $end_time)
                                                  $leads = $leads + 1;
                              }

                              $js[] = array(lead_id => $leads, call_date => $begin_time);
                              $leads = 0;

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
          case 'campaign_id':
                    $query = "SELECT  a.campaign_id,b.campaign_name  FROM  vicidial_campaign_statuses a inner join vicidial_campaigns b on a.campaign_id=b.campaign_id and active='y'  group by  campaign_id ";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = array(campaign_id => $row["campaign_id"], campaign_name => $row["campaign_name"]);
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

          case 'user_group':
                    $query = "SELECT  user_group  as ug FROM  vicidial_log  where call_date between date_sub(NOW(), INTERVAL $param1) and now() and user_group is not NUll group by  user_group ";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = $row["ug"];
                    }
                    echo json_encode($js);
                    break;

          case 'group_inbound':
                    $query = "SELECT group_id,group_name FROM vicidial_inbound_groups";
                    $query = mysql_query($query, $link) or die(mysql_error());
                    while ($row = mysql_fetch_assoc($query)) {
                              $js[] = array(id => $row["group_id"], name => $row["group_name"]);
                    }
                    echo json_encode($js);
                    break;
//flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------flot EXTRAS --------------
}
?>