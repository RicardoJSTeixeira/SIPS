<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

function log_admin($topic, $event, $id, $query, $comments = "") {
    global $link, $PHP_AUTH_USER;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='ADD', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    $rslt = mysql_query($stmt, $link);
    if (!$rslt) {
        echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
    }
}

function changespecialchars($string) {
    $string = strtr($string, array(
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ä' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'Eth',
        'Ñ' => 'N', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ä' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'eth',
        'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y',
        'ß' => 'sz', 'þ' => 'thorn', 'ÿ' => 'y','\''=>'',' '=>'','('=>'',')'=>''));
    return $string;
}

$maxFileSize = 10 * pow(1024, 2); #10mb
$ast_sounds_path = "/var/lib/asterisk/sounds/";
if ($_FILES["audio-file"]["error"] > 0) {
    
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(array("error" => $_FILES["audio-file"]["error"]));
    log_admin("UPLOAD", "Fail to upload audio file", "", "");
    die();
    
} else {
    if (file_exists($ast_sounds_path."upload/" . preg_replace('"\.(ogg|wav|mp3|gsm|sln)$"', ".gsm",$_FILES["audio-file"]["name"]))) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array("error" => "f_e", "path"=>$ast_sounds_path."upload/" . $_FILES["audio-file"]["name"]));
        log_admin("UPLOAD", "Fail: File exists", "", "");
        die();
        
    } else {
        if ($_FILES['size'] > $maxFileSize) {
            
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array("error" => "f_h"));
            log_admin("UPLOAD", "Fail: Max size achieved", "", "");
            die();
            
        } else {
            $filename=  changespecialchars(strtolower($_FILES["audio-file"]["name"]));
            move_uploaded_file($_FILES["audio-file"]["tmp_name"], $ast_sounds_path . "upload/" .$filename);
            $original = $ast_sounds_path . "upload/" . $filename;
            $new = preg_replace('"\.(ogg|wav|mp3)$"', ".gsm", $ast_sounds_path . "upload/" . $filename);

            $soxCommand = "sox ".escapeshellarg($original)." -r 8000 -c1 ".escapeshellarg($new)." lowpass 4000 compand 0.02,0.05 -60,-60,-30,-10,-20,-8,-5,-8,-2,-8 -8 -7 0.05 resample -ql";

            //  run SOX command
            exec($soxCommand, $output, $result);
            unlink($ast_sounds_path . "upload/" . $filename);
            //  Deal with result
            if ($result != 0) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array("error" => "c_e"));
                log_admin("UPLOAD", "Fail: Error $result", "", "");
                die();
            }

            $stmt = "UPDATE servers SET sounds_update='Y' where server_ip NOT like '$_SERVER[SERVER_ADDR]';";
            $rslt = mysql_query($stmt, $link);
            if (!$rslt) {
                echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
            }
            
            log_admin("UPLOAD", "Sucess: File moved and converted to gsm", "", "");
            echo json_encode(array("path" => "upload/" . preg_replace('"\.(ogg|wav|mp3|gsm|sln)$"', ".gsm",$filename)));
            
        }
    }
}
?>
