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


    case "get_pages":
        $query = "SELECT * FROM script_dinamico_pages where id_script=$id_script";
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
            $js[] = array(id => $row["id"], id_script => $row["id_script"], id_page => $row["id_page"], type => $row["type"], ordem => $row["ordem"], dispo => $row["dispo"], texto => $row["texto"], placeholder => $row["placeholder"], max_length => $row["max_length"], values_text => $row["values_text"], required => $row["required"]);
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
        $query = "UPDATE script_dinamico SET id_script=$id_script,id_page=$id_page,type='$type',ordem=$ordem,dispo='$dispo',texto='$texto',placeholder='$placeholder',max_length=$max_length,values_text='$values_text',required=$required WHERE id=$id";

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
        $query = "INSERT INTO `asterisk`.`script_dinamico` (`id`, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,required) VALUES (NULL, $id_script,$id_page,'$type',$ordem,'$dispo', '$texto', '$placeholder', $max_length, '$values_text',$required)";

        $query = mysql_query($query, $link) or die(mysql_error());

        echo json_encode(mysql_insert_id());
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

        echo json_encode(array(1));
        break;


    case "save_form_result":



        $sql = array();
        foreach ($results as $row) {

            $sql[] = "(null,$id_script,'" . $row['name'] . "', '" . $row['value'] . "')";
        }
        $query = "INSERT INTO `script_result`(`id`,id_script, `id_elemento`, `valor`) VALUES " . implode(',', $sql);
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;
}
?>
