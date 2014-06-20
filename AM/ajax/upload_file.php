<?php

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}



require '../lib_php/db.php';

require '../lib_php/user.php';
$user = new UserLogin($db);
$user->confirm_login();






$destiny = getcwd() . "/files/";

switch ($action) {

    case "upload":
        $fileName = $_FILES["file"]["name"];
        if (file_exists($destiny . $fileName)) {
            echo $fileName . " Já existe. ";
            return false;
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $id . "_-_" . $fileName))
                echo "$fileName Guardado";
            else
                echo "$fileName Não Guardado";
        }
        break;


    case "move_files_to_new_folder":
        if (!file_exists($destiny . $new_id . "_encomenda")) {
            mkdir($destiny . $new_id . "_encomenda", 0777, true);
            $srcDir = $destiny;
            $destDir = $destiny . $new_id . "_encomenda";
            if (file_exists($destDir)) {
                if (is_dir($destDir)) {
                    if (is_writable($destDir)) {
                        if ($handle = opendir($srcDir)) {
                            while (false !== ($file = readdir($handle))) {
                                if (is_file($srcDir . '/' . $file)) {
                                    if (strstr($file, $old_id . "_-_")) {
                                        $temp_file = str_replace($old_id . "_-_", "", $file);
                                        rename($srcDir . '/' . $file, $destDir . '/' . $temp_file);
                                    }
                                }
                            }
                            closedir($handle);
                        } else {
                            echo(json_encode("$srcDir não pode ser aberto."));
                        }
                    } else {
                        echo(json_encode("$destDir não permite escrita"));
                    }
                } else {
                    echo(json_encode("$destDir não é um caminho valido!"));
                }
            } else {
                echo(json_encode("$destDir não existe"));
            }
        } else
            echo(json_encode("A pasta escolhida ja existe"));
        echo(json_encode("Pasta de anexos criada"));
        break;

    case "delete":
        if (unlink($destiny . $name))
            echo("ficheiro removido com sucesso");
        else
            echo("Não foi possível remover o ficheiro");
        break;

    case "get_anexos":
        $js = array();
        $dh = @opendir($destiny . $folder);

        while (false !== ( $file = readdir($dh) )) {
            if ($file != "dummy.gitignore" && $file != ".." && $file != ".")
                $js[] = $file;
        }

        closedir($dh);
        echo json_encode($js);
        break;


    case "upload_report":
        $fileName = $_FILES["file"]["name"];
        if (file_exists($destiny . $fileName)) {
            echo $fileName . " Já existe. ";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $fileName))
                echo 1;
            else {
                echo "$fileName não carregado";
            }
        }
        break;
}