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
        if (file_exists($destiny . $_FILES["file_upload_all"]["name"])) {
            echo $_FILES["file_upload_all"]["name"] . " Já existe. ";
        } else {
            if (move_uploaded_file($_FILES["file_upload_all"]["tmp_name"], $destiny . $_FILES["file_upload_all"]["name"]))
                echo "Guardado";
            else
                echo "Não Guardado";
        }
        break;

    case "delete":
        if (unlink($destiny . $name))
            echo("ficheiro removido com sucesso");
        else
            echo("Não foi possível remover o ficheiro");
        break;


    case "get_anexos":
        $js = array();
        $dh = @opendir($destiny);

        while (false !== ( $file = readdir($dh) )) {
            if ($file != "dummy.gitignore"&&$file != ".."&&$file != ".")
                $js[] = $file;
        }

        closedir($dh);
        echo json_encode($js);
        break;
}
