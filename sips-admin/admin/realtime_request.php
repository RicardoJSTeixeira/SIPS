<?php
        require("../dbconnect.php");
        $PHP_AUTH_USER = $_POST['user'];
        $PHP_AUTH_PW = base64_decode($_POST['pass']);

        
#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
        $stmt = "SELECT use_non_latin FROM system_settings;";
        $rslt = mysql_query($stmt, $link);
        if ($DB) {
            echo "$stmt\n";
        }
        $qm_conf_ct = mysql_num_rows($rslt);
        if ($qm_conf_ct > 0) {
            $row = mysql_fetch_row($rslt);
            $non_latin = $row[0];
        }
##### END SETTINGS LOOKUP #####
###########################################

        $PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_USER);
        $PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_PW);

        $stmt = "SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 8 and view_reports='1';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        if ($non_latin > 0) {
            $rslt = mysql_query("SET NAMES 'UTF8'");
        }
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $auth = $row[0];

        if ((strlen($PHP_AUTH_USER) < 2) or (strlen($PHP_AUTH_PW) < 2) or (!$auth)) {
            Header("WWW-Authenticate: Basic realm=\"GOAUTODIAL-PROJECTS\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
            echo "<html><head><title>GoAutoDial - Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
            exit;
        }
        
        
echo json_encode(array("logs"=>system('tail -n 100 /var/log/asterisk/messages')));
?>
