<?php

$temp = explode(".", $_FILES["file"]["name"]);


$extension = end($temp);

$destiny = getcwd() . "/files/";


if ($extension == "pdf") {
              if (file_exists($destiny . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " Já existe. ";
            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $_FILES["file"]["name"]);
                echo "Guardado";
            }
}
else if($extension == "gif"||$extension == "jpeg"||$extension == "jpg"||$extension == "png")
{
            if (file_exists($destiny . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " Já existe. ";
            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], $destiny . $_FILES["file"]["name"]);
                echo "Guardado";
            }
     }
else
     echo "Ficheiro Inválido";
?>