<?php

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$destiny = getcwd() . "/files/";

switch ($action) {

    case "upload":
        $temp = explode(".", $_FILES["file"]["name"]);


        $extension = end($temp);
        if ($extension == "pdf") {
            if (file_exists($destiny . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " Já existe. ";
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $_FILES["file"]["name"]))
                    echo "Guardado";
                else
                    echo "Não Guardado";
            }
        } else if ($extension == "gif" || $extension == "jpeg" || $extension == "jpg" || $extension == "png") {
            if (file_exists($destiny . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " Já existe. ";
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $_FILES["file"]["name"]))
                    echo "Guardado";
                else
                    echo "Não Guardado";
            }
        }
        else
            echo "Ficheiro Inválido";


        break;


    case "delete":

        if (unlink($destiny . $name))
            echo("ficheiro removido com sucesso");
        else
            echo("Não foi possível remover o ficheiro");
        break;
}
?>