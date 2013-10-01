<?php

require("../../ini/dbconnect.php");
require("../../ini/user.php");

header('Content-Disposition: attachment; filename=Report_Script_' . date("Y-m-d_H:i:s") . '.csv');
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users;

$js = array();
switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//

    case "get_schedule":
        $query = "SELECT id_scheduler,display_text FROM sips_sd_schedulers where active='1' and user_group='$user->user_group' order by display_text";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id_scheduler"], "text" => $row["display_text"]);
        }
        echo json_encode($js);
        break;


    case "get_schedule_by_id":
        $query = "SELECT id_scheduler,display_text FROM sips_sd_schedulers where id_scheduler in($ids) and active='1' order by display_text";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id_scheduler"], "text" => $row["display_text"]);
        }
        echo json_encode($js);
        break;


    case "get_client_info_by_lead_id":
        $js = array();
        $query = "SELECT first_name,phone_number,alt_phone,address1,address3,postal_code,email,comments from vicidial_list where lead_id='$lead_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $user_temp = $user->getUser($user_logged);
            $js = array("nome" => $row["first_name"], "telefone" => $row["phone_number"], "telefone_alt" => $row["alt_phone"], "morada" => $row["address1"], "telefone_alt2" => $row["address3"], "codigo_postal" => $row["postal_code"], "email" => $row["email"], "comentario" => $row["comments"], "nome_operador" => $user_temp["full_name"]);
        }

        if (sizeof($js) < 1) {
            $js = array("nome" => "//Nome do cliente//", "telefone" => "//telefone do cliente//", "telefone_alt" => "//Telefone alternativo do cliente//", "morada" => "//morada do cliente//", "telefone_alt2" => "//Telemóvel do cliente//", "codigo_postal" => "//codigo postal do cliente//", "email" => "//email do cliente//", "comentario" => "//comentarios//", "nome_operador" => "//nome do operador//");
        }


        echo json_encode($js);
        break;




    case "get_tag_fields":

        $query = "SELECT id_camp_linha FROM script_assoc where id_script='$id_script'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $campaigns[] = $row["id_camp_linha"];
        }

        foreach ($campaigns as $value) {
            $query = "Select campaign_name from vicidial_campaigns where campaign_id = '$value';";
            $query = mysql_query($query, $link) or die(mysql_error());
            $row = mysql_fetch_assoc($query);
            $js[$value]["text"] = $row['campaign_name'];

            $query = "SELECT * FROM `vicidial_list_ref`  where campaign_id ='$value' and active='1'  GROUP BY name";
            $query = mysql_query($query, $link) or die(mysql_error());

            $js["$value"]["lista"] = array();
            while ($row = mysql_fetch_assoc($query)) {
                $js[$row["campaign_id"]]["lista"][] = array("id" => $row["indice"], "value" => $row["Name"], "name" => $row["Display_name"]);
            }
        }



        echo json_encode($js);
        break;

    case "get_camp_linha_by_id_script":
        $query = "SELECT * FROM script_assoc where id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id_script" => $row["id_script"], "id_camp_linha" => $row["id_camp_linha"], "tipo" => $row["tipo"]);
        }
        echo json_encode($js);
        break;



    case "get_scripts":

        $user_group_temp = "where user_group='$user->user_group'";
        if ($user->user_group == "ADMIN")
            $user_group_temp = "";

        $query = "SELECT * FROM script_dinamico_master $user_group_temp";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[$row["id"]] = array("id" => $row["id"], "name" => $row["name"]);
        }
        echo json_encode($js);
        break;


    case "get_scripts_by_campaign":
        $query = "SELECT * FROM script_dinamico_master sdm inner join script_assoc sa on sa.id_script=sdm.id where sa.id_camp_linha='$id_campaign'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        echo json_encode($js);
        break;

    case "get_scripts_by_id_script":
        $query = "SELECT * FROM script_dinamico_master where id='$id_script'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        echo json_encode($js);
        break;




    case "get_results_to_populate":
        $query = "SELECT sr.id_script,lead_id,tag_elemento,valor,type FROM script_result sr inner join script_dinamico sd on sr.tag_elemento=sd.tag  where sr.lead_id=$lead_id order by unique_id desc limit 1 ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id_script" => $row["id_script"], "lead_id" => $row["lead_id"], "tag_elemento" => $row["tag_elemento"], "valor" => $row["valor"], "type" => $row["type"]);
        }
        echo json_encode($js);
        break;

    case "get_reduced_data":
        $query = "SELECT id,tag,type,texto,values_text FROM `script_dinamico` WHERE id in($ids) order by ordem";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"], "values_text" => $row["values_text"]);
        }
        echo json_encode($js);
        break;


    case "get_pages":
        $query = "SELECT * FROM script_dinamico_pages where id_script=$id_script order by pos";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "name" => $row["name"], "pos" => $row["pos"]);
        }
        echo json_encode($js);
        break;


    case "get_data_render":
        $query = "SELECT sd.id,sd.tag,sd.id_script,id_page,type,ordem,dispo,texto,placeholder,max_length,values_text,required,hidden,param1 FROM script_dinamico sd inner join script_dinamico_pages sdp on sd.id_page=sdp.id  WHERE sd.id_script=$id_script  order by sdp.pos,sd.ordem asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        echo json_encode($js);
        break;

    case "get_data":
        $query = "SELECT * FROM `script_dinamico` WHERE id_script=$id_script and id_page=$id_page order by ordem asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        echo json_encode($js);
        break;

    case "get_data_individual":
        $query = "SELECT * FROM `script_dinamico` WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        echo json_encode($js);
        break;


    case "get_rules_by_trigger":
        $query = "SELECT * FROM `script_rules` WHERE tag_trigger=$tag_trigger and id_script='$id_script'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $js = array();
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "id_script" => $row["id_script"], "tipo_elemento" => $row["tipo_elemento"], "tag_trigger" => $row["tag_trigger"], "tag_trigger2" => json_decode($row["tag_trigger2"]), "tag_target" => json_decode($row["tag_target"]), "tipo" => $row["tipo"], "param1" => $row["param1"], "param2" => $row["param2"]);
        }
        echo json_encode($js);
        break;


    case "get_rules":
        $query = "SELECT * FROM `script_rules` WHERE id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "id_script" => $row["id_script"], "tipo_elemento" => $row["tipo_elemento"], "tag_trigger" => $row["tag_trigger"], "tag_trigger2" => json_decode($row["tag_trigger2"]), "tag_target" => json_decode($row["tag_target"]), "tipo" => $row["tipo"], "param1" => $row["param1"], "param2" => json_decode($row["param2"]));
        }
        echo json_encode($js);
        break;


    case 'get_campaign':
        $campaigns = implode("','", $user->allowed_campaigns);

        if ($user->is_all_campaigns)
            $campaigns = "";
        else
            $campaigns = "and campaign_id in('$campaigns')";


        $query = "SELECT  campaign_id,campaign_name  FROM  vicidial_campaigns where active='y' $campaigns  order by campaign_name ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["campaign_id"], "name" => $row["campaign_name"]);
        }
        echo json_encode($js);
        break;

    case 'get_linha_inbound':

        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["group_id"], "name" => $row["group_name"]);
        }
        echo json_encode($js);

        break;

    case 'iscloud':
        $query = "SELECT cloud FROM servers";
        $result = mysql_query($query) or die(mysql_error);
        $row = mysql_fetch_row($result);
        echo json_encode(array("iscloud" => $row[0] == "1"));
        break;


    case 'has_rules':
        $temp = 0;
        $query = "SELECT count(id) as count FROM script_rules where tag_trigger=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $temp = $row["count"];
        }
        echo json_encode($temp);

        break;

    case "get_image_pdf":
        $path = getcwd() . "/files/"; //change this if the script is in a different dir that the files you want 
        $show = array('.jpg', '.gif', '.png','.jpeg','.pdf'); //Type of files to show 

        $select = "<select name=\"select_box\">";

        $dh = @opendir($path);

        while (false !== ( $file = readdir($dh) )) {
            $ext = substr($file, -4, 4);
            if (in_array($ext, $show)) {
                $select .= "<option value='$path/$file'>$file</option>\n";
            }
        }

        $select .= "</select>";
        closedir($dh);

        echo "$select";
        break;

    //------------------------------------------------//
    //-----------------EDIT---------------------------//
    //------------------------------------------------//
    case "edit_script":
        $query = "update script_dinamico_master set name='$name' where id=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());

        $query = "delete from script_assoc where id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());

        foreach ($campaign as $value) {
            $query = "INSERT INTO `script_assoc` values($id_script,'$value','campaign')";
            $query = mysql_query($query, $link) or die(mysql_error());
        }
        foreach ($linha_inbound as $value) {
            $query = "INSERT INTO `script_assoc` values($id_script,'$value','linha_inbound')";
            $query = mysql_query($query, $link) or die(mysql_error());
        }

        echo json_encode(1);
        break;

    case "edit_page":
        $query = "update script_dinamico_pages set pos=$old_pos where pos=$new_pos and  id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());


        $query = "update script_dinamico_pages set name='$name',pos=$new_pos where id=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());


        echo json_encode(1);
        break;


    case "edit_item":
        $values_text = (!isset($values_text)) ? array() : $values_text;
        $query = "UPDATE script_dinamico SET id_script=$id_script,id_page=$id_page,type='$type',ordem=$ordem,dispo='$dispo',texto='$texto',placeholder='" . mysql_real_escape_string(json_encode($placeholder)) . "',max_length=$max_length,values_text='" . mysql_real_escape_string(json_encode($values_text)) . "',required=$required,hidden=$hidden,param1='$param1' WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;



    case "edit_item_order":
        $query = "UPDATE script_dinamico SET ordem=$ordem WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;
    //------------------------------------------------//
    //-----------------ADD----------------------------//
    //------------------------------------------------//
    case "add_page":
        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL,$id_script,'Página nova',$pos)";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;

    case "add_script":
        $query = "INSERT INTO `asterisk`.`script_dinamico_master` (id,name,user_group) VALUES (NULL,'Script novo','$user->user_group')";
        $query = mysql_query($query, $link) or die(mysql_error());

        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL," . mysql_insert_id() . ",'Página nova','1')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;

    case "add_item":
        $query = "UPDATE script_dinamico SET ordem=ordem+1 where ordem>=$ordem and id_page='$id_page' ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $values_text = (!isset($values_text)) ? array() : $values_text;


        //get max tag--------------------------------------------------------
        $tag = 0;
        $query2 = "SELECT max(tag) as max_tag FROM script_dinamico where id_script='$id_script'";
        $query2 = mysql_query($query2, $link) or die(mysql_error());
        $row2 = mysql_fetch_assoc($query2);
        $tag = (!isset($row2["max_tag"])) ? 0 : $row2["max_tag"] + 1;
        ;
        $query = "INSERT INTO `asterisk`.`script_dinamico` (`id`,tag, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,required,hidden,param1) VALUES (NULL,$tag, $id_script,$id_page,'$type',$ordem,'$dispo', '$texto', '" . mysql_real_escape_string(json_encode($placeholder)) . "', $max_length, '" . mysql_real_escape_string(json_encode($values_text)) . "',$required,$hidden,'$param1')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(mysql_insert_id());
        break;

    case "add_rules":

        if (!empty($tag_trigger2) && !empty($tag_target)) {
            $query = "INSERT INTO `asterisk`.`script_rules` (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) VALUES (NULL,$id_script,'$tipo_elemento',$tag_trigger,'" . mysql_real_escape_string(json_encode($tag_trigger2)) . "','" . mysql_real_escape_string(json_encode($tag_target)) . "','$tipo','$param1','$param2')";
            $query = mysql_query($query, $link) or die(mysql_error());
            echo json_encode(1);
        }
        break;


    case "duplicate_script":
        //script
        $query = "INSERT INTO script_dinamico_master (id,name,user_group) VALUES (NULL,'$nome_script duplicado','$user->user_group')";
        $query = mysql_query($query, $link) or die(mysql_error());
        $temp_script_page = mysql_insert_id();
        $query = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,$temp_script_page,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2 from script_rules where id_script='$id_script' and tipo!='goto'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "SELECT id,id_script,name,pos FROM script_dinamico_pages where id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            //pages
            $query1 = "INSERT INTO script_dinamico_pages (id,id_script,name,pos) values(NULL, $temp_script_page,'" . $row['name'] . "','" . $row['pos'] . "')";
            $query1 = mysql_query($query1, $link) or die(mysql_error());
            $temp_page = mysql_insert_id();
            //rules de go-to
            $query1 = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,$temp_script_page,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1," . mysql_insert_id() . " from script_rules where param2='" . $row['id'] . "' and id_script='$id_script' and tipo='goto'";
            $query1 = mysql_query($query1, $link) or die(mysql_error());
            //elements
            $query1 = "INSERT INTO script_dinamico (`id`,tag, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,required,hidden,param1) select NULL,tag,'$temp_script_page','" . $temp_page . "',type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,required,hidden,param1 from script_dinamico where id_script='$id_script' and id_page= '" . $row['id'] . "'  ";
            $query1 = mysql_query($query1, $link) or die(mysql_error());
        }
        echo json_encode(1);
        break;

    //------------------------------------------------//
    //-----------------DELETE-------------------------//
    //------------------------------------------------//
    case "delete_page":


        $query = "update script_dinamico_pages set pos=pos-1 where pos>$pos and id_script=$id_script ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico_pages  where id=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "select sd.id as id from script_dinamico sd inner join script_rules sr on sd.id=sr.tag_trigger";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = "'" . $row["id"] . "'";
        }
        $js = implode(', ', $js);
        if ($js . length > 0) {
            $query = "delete from  script_rules where tag_trigger in($js)";
            $query = mysql_query($query, $link) or die(mysql_error());
        }
        $query = "delete from  script_dinamico where id_page=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;

    case "delete_item":
        $query = "UPDATE script_dinamico SET ordem=ordem-1 where ordem>$ordem and id_page=$id_page ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_result where id_script=$id_script and tag_elemento=(select tag from script_dinamico where id=$id)";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_rules where tag_trigger=$id";
        $query = mysql_query($query, $link) or die(mysql_error());

        echo json_encode(1);
        break;

    case "delete_script":
        $query = "delete from script_assoc where id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico_master where id=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico_pages where id_script=$id_script ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico where id_script=$id_script ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_rules where id_script=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;

    case "delete_rule":
        $query = "delete from script_rules  where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;
    //------------------------------------------------//
    //-----------------FORM---------------------------//
    //------------------------------------------------//
    case "save_form_result":
        $sql = array();
        foreach ($results as $row) {
            if ($row['value'] != "") {
                $temp = explode(",", $row['name']);
                $sql[] = "(null,'" . date('Y-m-d H:i:s') . "',$id_script,'$user_id','$unique_id','$campaign_id','$lead_id','$temp[0]', '" . $row['value'] . "', '$temp[1]')";
            }
        }
        $query = "INSERT INTO `script_result`(`id`,date,id_script,user_id,unique_id,campaign_id,lead_id, `tag_elemento`, `valor`,param_1) VALUES " . implode(',', $sql);
        echo($query);
        $query = mysql_query($query, $link) or die(mysql_error());
        //echo json_encode(1);
        break;





//falta tableradios

    case "write_to_file":



        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        echo "\xEF\xBB\xBF";
        header('Content-Disposition: attachment; filename=data_new.csv');

        $output = fopen('php://output', 'w');


        $titles = array('ID', 'Data', 'Script', 'Nome agente', 'Unique id', 'Campanha', 'Lead id', "feedback");
        $campos = array();
        $ref_name = array();

        $query = "SELECT Name,Display_name  FROM vicidial_list_ref where campaign_id = '$campaign_id' and active='1' ";

        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            array_push($titles, $row['Display_name']);
            $campos[$row['Name']] = "";
            array_push($ref_name, $row['Name']);
        }


        $tags = array();

        $query = "SELECT tag,type,texto,values_text  FROM `script_dinamico` where type not in ('pagination','textfield') and texto!='' and id_script='$id_script' order by tag asc ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {

            if ($row['type'] == "tableradio") {


                $temp = json_decode($row['values_text']);
                foreach ($temp as $value) {
                    array_push($titles, $row['texto'] . "-" . $value);
                    $tags["m" . $row['tag'] . $value] = "";
                }
            } else {
                array_push($titles, $row['texto']);
                $tags["m" . $row['tag']] = "";
            }
        }

        fputcsv($output, $titles, ";", '"');


        $query = "SELECT a.lead_id, " . implode(",", $ref_name) . " from vicidial_list a left join `script_result` b on a.lead_id=b.lead_id where id_script='$id_script' and  b.campaign_id = '$campaign_id'  group by b.lead_id";
        $result = mysql_query($query, $link) or die(mysql_error());
        $lead_info = array();
        while ($row3 = mysql_fetch_assoc($result)) {
            $lead_info[$row3["lead_id"]] = $row3;
        }

        $query = "SELECT sr.id,sr.date, sdm.name, vu.full_name, sr.unique_id, vc.campaign_name, sr.lead_id,sr.param_1,vcs.status_name, sr.tag_elemento,sr.valor,sd.param1,sd.type FROM `script_result` sr
left join vicidial_campaigns vc on vc.campaign_id=sr.campaign_id
left join vicidial_users vu on sr.user_id=vu.user_id
left join script_dinamico_master sdm on sdm.id=sr.id_script
left join vicidial_list vl on vl.lead_id=sr.lead_id
left join vicidial_log vlg on vlg.uniqueid=sr.unique_id
left join vicidial_campaign_statuses vcs on vcs.status=vlg.status
left join script_dinamico sd on sd.tag=sr.tag_elemento and sd.id_script=sr.id_script 
where sr.id_script='$id_script' and sr.campaign_id = '$campaign_id' order by sr.lead_id,tag_elemento";
        $result = mysql_query($query, $link) or die(mysql_error());



        $final_row = array("id" => "", "date" => "", "name" => "", "full_name" => "", "unique_id" => "", "campaign_name" => "", "lead_id" => "", "status_name" => "");

        $lead_id = false;
        $client = array();
        while ($row1 = mysql_fetch_assoc($result)) {
            if ($lead_id != $row1["lead_id"]) {
                if ($lead_id) {
                    fputcsv($output, $client, ";", '"');
                }
                $lead_id = $row1["lead_id"];
                $client = array_merge($final_row, $lead_info[$lead_id], $tags);
                $client["id"] = $row1["id"];
                $client["date"] = $row1["date"];
                $client["name"] = $row1["name"];
                $client["full_name"] = $row1["full_name"];
                $client["unique_id"] = $row1["unique_id"];
                $client["campaign_name"] = $row1["campaign_name"];
                $client["lead_id"] = $row1["lead_id"];
                $client["status_name"] = $row1["status_name"];
            }

            if ($row1["type"] == "tableradio")
                $client["m" . $row1["tag_elemento"] . $row1["param_1"]] = $row1["valor"];
            else
                $client["m" . $row1["tag_elemento"]] = ($row1["param1"] == "nib") ? "" . $row1["valor"] . "" : $row1["valor"];
        }
        fputcsv($output, $client, ";", '"'); // necessário para imprimir a info da ultima lead0



        fclose($output);

        break;
}
?>
