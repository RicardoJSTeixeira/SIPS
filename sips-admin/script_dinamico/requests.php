<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {
    //------------------------------------------------//
//-----------------GET-------------------------  
    //------------------------------------------------//
    case "get_tag_fields":
        $query = "SELECT * FROM `vicidial_list_ref` GROUP BY name";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["indice"], value => $row["Name"], name => $row["Display_name"]);
        }
        echo json_encode($js);
        break;

    case "get_scripts":
        $query = "SELECT * FROM script_dinamico_master";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["name"]);
        }
        echo json_encode($js);
        break;

    case "get_results":
        $query = "SELECT * FROM script_result order by id_elemento ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], id_script => $row["id_script"], id_elemento => $row["id_elemento"], valor => $row["valor"]);
        }
        echo json_encode($js);
        break;

    case "get_reduced_data":
        $query = "SELECT id,type,texto,values_text FROM `script_dinamico` WHERE id in($ids) order by ordem";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], type => $row["type"], texto => $row["texto"], values_text => $row["values_text"]);
        }
        echo json_encode($js);
        break;


    case "get_pages":
        $query = "SELECT * FROM script_dinamico_pages where id_script=$id_script order by name";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["name"]);
        }
        echo json_encode($js);
        break;

    case "get_data":
        $query = "SELECT * FROM `script_dinamico` WHERE id_script=$id_script and id_page=$id_page order by ordem asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], id_script => $row["id_script"], id_page => $row["id_page"], type => $row["type"], ordem => $row["ordem"], dispo => $row["dispo"], texto => $row["texto"], placeholder => $row["placeholder"], max_length => $row["max_length"], values_text => $row["values_text"], required => $row["required"], hidden => $row["hidden"]);
        }
        echo json_encode($js);
        break;

         case "get_data_individual":
        $query = "SELECT * FROM `script_dinamico` WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], id_script => $row["id_script"], id_page => $row["id_page"], type => $row["type"], ordem => $row["ordem"], dispo => $row["dispo"], texto => $row["texto"], placeholder => $row["placeholder"], max_length => $row["max_length"], values_text => $row["values_text"], required => $row["required"], hidden => $row["hidden"]);
        }
        echo json_encode($js);
        break;
        

    case "get_rules_by_trigger":
        $query = "SELECT * FROM `script_rules` WHERE id_trigger=$id_trigger";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"],id_script => $row["id_script"], tipo_elemento => $row["tipo_elemento"], id_trigger => $row["id_trigger"], id_trigger2 => $row["id_trigger2"], id_target => $row["id_target"], tipo => $row["tipo"], param1 => $row["param1"], param2 => $row["param2"]);
        }
        echo json_encode($js);
        break;


    case "get_rules":
        $query = "SELECT * FROM `script_rules` WHERE id_script=$id_script";
  
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"],id_script => $row["id_script"], tipo_elemento => $row["tipo_elemento"], id_trigger => $row["id_trigger"], id_trigger2 => $row["id_trigger2"], id_target => $row["id_target"], tipo => $row["tipo"], param1 => $row["param1"], param2 => $row["param2"]);
        }
        echo json_encode($js);
        break;
    //------------------------------------------------//
//-----------------EDIT-------------------------
    //------------------------------------------------//
    case "edit_script_name":
        $query = "update script_dinamico_master set name='$name' where id=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "edit_page_name":
        $query = "update script_dinamico_pages set name='$name' where id=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "edit_item":
        $query = "UPDATE script_dinamico SET id_script=$id_script,id_page=$id_page,type='$type',ordem=$ordem,dispo='$dispo',texto='$texto',placeholder='$placeholder',max_length=$max_length,values_text='$values_text',required=$required,hidden=$hidden WHERE id=$id";

        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "edit_item_order":

        $query = "UPDATE script_dinamico SET ordem=$ordem WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;



    //------------------------------------------------//
//-----------------ADD-------------------------
    //------------------------------------------------//
    case "add_page":
        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,id_script,name) VALUES (NULL,$id_script,'Página nova')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "add_script":
        $query = "INSERT INTO `asterisk`.`script_dinamico_master` (id,name) VALUES (NULL,'Script novo')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "add_item":
        $query = "UPDATE script_dinamico SET ordem=ordem+1 where ordem>=$ordem";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "INSERT INTO `asterisk`.`script_dinamico` (`id`, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,required,hidden) VALUES (NULL, $id_script,$id_page,'$type',$ordem,'$dispo', '$texto', '$placeholder', $max_length, '$values_text',$required,$hidden)";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(mysql_insert_id());
        break;

    case "add_rules":
        $query = "INSERT INTO `asterisk`.`script_rules` (id,id_script,tipo_elemento,id_trigger,id_trigger2,id_target,tipo,param1,param2) VALUES (NULL,$id_script,'$tipo_elemento',$id_trigger,'$id_trigger2','$id_target','$tipo','$param1','$param2')";

        $query = mysql_query($query, $link) or die(mysql_error());
     echo json_encode(array(1));
        break;

    //------------------------------------------------//
    //-----------------DELETE-------------------------
    //------------------------------------------------//
    case "delete_page":
        $query = "delete from script_dinamico_pages  where id=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico where id_page=$id_pagina";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "delete_item":
        $query = "UPDATE script_dinamico SET ordem=ordem-1 where ordem>$ordem";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "delete_script":
        $query = "delete from script_dinamico_master where id=$id_script";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico_pages where id_script=$id_script ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "delete from script_dinamico where id_script=$id_script ";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

 case "delete_rule":
        $query = "delete from script_rules  where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
      
        echo json_encode(array(1));
        break;
    
    
    case "save_form_result":

        $sql = array();
        foreach ($results as $row) {

            if ($row['value'] != "")
                $sql[] = "(null,$id_script,'" . $row['name'] . "', '" . $row['value'] . "')";
        }
        $query = "INSERT INTO `script_result`(`id`,id_script, `id_elemento`, `valor`) VALUES " . implode(',', $sql);
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;
}
?>
