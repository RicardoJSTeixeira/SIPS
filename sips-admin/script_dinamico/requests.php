<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {

    case "get_scripts":
        $query = "SELECT * FROM script_dinamico_pages";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["name"], pages => $row["pages"]);
        }
        echo json_encode($js);

        break;


    case "get_data":
        $query = "SELECT * FROM `script_dinamico` WHERE id_script=$id_script order by ordem asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], id_script => $row["id_script"], id_pagina => $row["id_pagina"], type => $row["type"], ordem => $row["ordem"], texto => $row["texto"], placeholder => $row["placeholder"], max_length => $row["max_length"], values_text => $row["values_text"]);
        }
        echo json_encode($js);
        break;

    case "edit_item":
        $query = "UPDATE script_dinamico SET id_script=$id_script,id_pagina=$id_pagina,type='$type',ordem=$ordem,texto='$texto',placeholder='$placeholder',max_length=$max_length,values_text='$values_text' WHERE id=$id";

        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;

    case "edit_item_order":

        $query = "UPDATE script_dinamico SET ordem=$ordem WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "insert_item":
        $query = "INSERT INTO `asterisk`.`script_dinamico` (`id`, `id_script`, `id_pagina`,type, `ordem`, `texto`, `placeholder`, `max_length`, `values_text`) VALUES (NULL, $id_script,$id_pagina, '$type', $ordem, '$texto', '$placeholder', $max_length, '$values_text')";

        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "delete_item":
        $query = "delete from script_dinamico where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "add_script":
        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,name) VALUES (NULL,'$name')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "edit_script_name":
        $query = "update script_dinamico_pages set name='$name' where id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(array(1));
        break;


    case "add_page":
        break;
}
?>
