<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
if(!isset($_POST["file"]))exit;

$apache_path="/srv/www/htdocs/";
$ast_sounds_path="/var/lib/asterisk/sounds/";

$file=$_POST["file"];

$original=$ast_sounds_path.$file;
$new=  preg_replace('"\.(gsm|wav|sln)$"', ".ogg", $apache_path."sips-admin/inbound/tmp/".basename($file));

 $soxCommand="sox ".escapeshellarg($original)." ".escapeshellarg($new)." pad 0 0.25";
 exec($soxCommand, $output, $result);

            //  Deal with result
            if ($result != 0) {
                echo json_encode(array("error" => $result, "output"=>$output));
                header('HTTP/1.1 500 Internal Server Error');
              die();
            }
 
echo json_encode(array("path"=>preg_replace("|" . preg_quote($apache_path."sips-admin/inbound/") . "|","",$new)));
?>


