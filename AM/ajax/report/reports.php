<?php

#error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
#ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";

require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
echo "\xEF\xBB\xBF";
$user = new UserLogin($db);
$user->confirm_login();

$u = $user->getUser();

switch ($action) {
    case "novasMarc":
        include 'includes/novasMarc.php';
        break;
    case "lead_id_follow_up_cc":
        $type = "cc";
        include 'includes/novasLeadsFollowUp.php';
        break;
    case "lead_id_follow_up_dispenser":
        $type = "dispenser";
        include 'includes/novasLeadsFollowUp.php';
        break;
    case "consulta_ftpv":
        include 'includes/consulta_ftpv.php';
        break;
    case "consulta_result_fecho":
        include 'includes/consulta_result_fecho.php';
        break;
    case "consultas":
        include 'includes/consultas.php';
        break;












    case 'get_agents':
        $query = "SELECT user,full_name FROM vicidial_users where user_group='SPICE'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("user" => $row["user"], "full_name" => $row["full_name"]);
        }
        echo json_encode($js);
        break;


    default:
        break;
}