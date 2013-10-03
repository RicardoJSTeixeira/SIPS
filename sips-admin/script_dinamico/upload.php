<?php

$temp = explode(".", $_FILES["file"]["name"]);




$extension = end($temp);

$destiny = getcwd() . "/files/";

if ($extension == "pdf") {
  

            
            /* echo "Upload: " . $_FILES["file"]["name"] . "<br>";
              echo "Type: " . $_FILES["file"]["type"] . "<br>";
              echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
              echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>"; */

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