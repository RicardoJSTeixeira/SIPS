<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
if(!isset($_POST["file"]))exit;

$apache_path=$_SERVER['DOCUMENT_ROOT'];
$ast_sounds_path="/var/lib/asterisk/sounds/";

$file=$_POST["file"];

$original=$ast_sounds_path.$file;
$new=  preg_replace('"\.(gsm|wav|sln)$"', ".ogg", $apache_path."sips-admin/inbound/tmp/".basename($file));

 $soxCommand="sox ".escapeshellarg($original)." ".escapeshellarg($new)." pad 0 0.25";
 system($soxCommand, $result);

            //  Deal with result
            if ($result != 0) {
                echo json_encode(array("error" => $result));
                header('HTTP/1.1 500 Internal Server Error');
              die();
            }
 
echo json_encode(array("path"=>preg_replace("|" . preg_quote($apache_path."sips-admin/inbound/") . "|","",$new)));
?>


