<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";

require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();

$docRoot = $_SERVER[DOCUMENT_ROOT];
$uploadedfile = $file;
$uploadedfile = preg_replace("/[^-\.\_0-9a-zA-Z]/", "_", $file);
$ConvertedFile = $uploadedfile;
$uploadedfile = preg_replace("/\.txt$/i", '.csv', $uploadedfile);

$ConvertCommand = "mv $docRoot/AM/ajax/files/$file $docRoot/AM/ajax/files/$uploadedfile";
$a = passthru($ConvertCommand);
$ConvertCommand = "$docRoot/sips-admin/campaigns/extras/upload/sheet2tab.pl $docRoot/AM/ajax/files/$uploadedfile $docRoot/AM/ajax/files/$ConvertedFile";
$b = passthru($ConvertCommand);

$file = fopen("$docRoot/AM/ajax/files/$ConvertedFile", "r");
$headers = explode("\t", trim(fgets($file, 4096)));

function getResTypeRaw($db) {
    $stmt = $db->prepare("SELECT id_reservations_types, display_text, min_time, max_time FROM `sips_sd_reservations_types` where display_text like '%Exame%'");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_OBJ);
    return array_reduce($row, function($a, $v) {
        return $a[$v->display_text] = (object) array("id" => $v->id_reservations_types, "min" => $v->min_time, "max" => $v->max_time);
    });
}

function getResType($slc, $types) {
    if (preg_match("/B\//", $slc))
        return $types["Exame Consultório"];
    elseif (preg_match("/C\//", $slc))
        return $types["Exame Catos"];
    else
        return $types["Exame Casa"];
}

$tRes = getResTypeRaw($db);

$stmtUpdate = $db->prepare("UPDATE sips_sd_reservations SET extra_id=:navid WHERE id_reservation=:id");
$stmtGetRsc = $db->prepare("SELECT id_resource id FROM `sips_sd_resources` WHERE `alias_code` LIKE :ref");
$stmtSetRes = $db->prepare("INSERT INTO `sips_sd_reservations` (`start_date`, `end_date`,`id_reservation_type`, `id_resource`, `id_user`, `lead_id`, `extra_id`) VALUES (:start, :end, :res_type, :id_rsc, :user, :lead_id, :nav_id)");
$stmtSetClient = $db->prepare("INSERT INTO vicidial_list 
    (entry_date, status, user, list_id, 
    PHONE_NUMBER, extra2, TITLE, FIRST_NAME, MIDDLE_INITIAL, LAST_NAME,
    DATE_OF_BIRTH, ALT_PHONE, ADDRESS3, EMAIL, ADDRESS1, ADDRESS2, extra4,
    POSTAL_CODE, CITY, PROVINCE, STATE, COUNTRY_CODE, extra3, extra1, extra5,
    SECURITY_PHRASE, COMMENTS, extra6) VALUES 
    (NOW(), 'NEW', :user, :list_id,
    :phone, :ref_client, :title, :name, :middle_name, :last_name,
    :date_of_birth, :alt_phone, :alt_phone2, :email, :address1, :address2, :address3,
    :postal, :local, :concelho, :distrito, :cod_pais, :area_code, :cod_mkt, :compart,
    :pref_marc, :comments, :to_issue)");
$total = 0;
$ok = 0;
$notok = 0;
$notoklist = array();
while (!feof($file)) {

    $buffer = trim(fgets($file, 4096));
    if (strlen($buffer) > 0) {

        $buffer = stripslashes($buffer);
        $buffer = explode("\t", $buffer);

        $LineCount++;
        $buffer[$index];
        $total++;
        if ((int) $buffer[39] !== 0) {
            $stmtUpdate->execute(array(":navid" => $buffer[40], ":id" => $buffer[39]));
            if ($stmtUpdate->rowCount()) {
                $ok++;
            } else {
                $notoklist[] = array("line" => $total + 1, "navid" => $buffer[40], "id" => $buffer[39], "error" => 'Update: Reserva não actualizada :"' . $buffer[39] . '"');
            }
            continue;
        }

        $stmtGetRsc->execute(array(":ref" => $buffer[23]));
        if (!($rsc = $stmtGetRsc->fetch(PDO::FETCH_OBJ))) {
            $notok++;
            $notoklist[] = array("line" => $total + 1, "navid" => $buffer[40], "id" => $buffer[39], "error" => 'Import: Calendário não importado :"' . $buffer[23] . '".');
            continue;
        }

        $nc = array(
            ":user" => $u->username,
            ":list_id" => $u->list_id,
            ":phone" => $buffer[15],
            ":ref_client" => $buffer[19],
            ":title" => $buffer[0],
            ":name" => $buffer[2],
            ":middle_name" => $buffer[3],
            ":last_name" => $buffer[4],
            ":date_of_birth" => $buffer[18],
            ":alt_phone" => $buffer[16],
            ":alt_phone2" => $buffer[17],
            ":email" => "",
            ":address1" => $buffer[5],
            ":address2" => $buffer[6],
            ":address3" => $buffer[7],
            ":postal" => $buffer[9],
            ":local" => $buffer[12],
            ":concelho" => $buffer[13],
            ":distrito" => $buffer[8],
            ":cod_pais" => $buffer[14],
            ":area_code" => $buffer[10],
            ":cod_mkt" => $buffer[1],
            ":compart" => $buffer[23],
            ":pref_marc" => $buffer[30],
            ":comments" => $buffer[32],
            ":to_issue" => "YES"
        );
        if ($stmtSetClient->execute($nc)) {

            $start = strtotime($buffer[29] . " " . $buffer[28]);
            $resType = getResType($buffer[23], $tRes);
            $stmtSetRes->execute(array(
                ":start" => date('Y-m-d H:i:s', $start),
                ":end" => date('Y-m-d H:i:s', strtotime($start, "+" . $resType->max . " minutes")),
                ":res_type" => $resType->id,
                ":id_rsc" => $rsc->id,
                ":user" => $u->username,
                ":lead_id" => $db->lastInsertId(),
                ":nav_id" => $buffer[40]
            ));
        } else {
            $notoklist[] = array("line" => $total + 1, "navid" => $buffer[40], "id" => $buffer[39], "error" => 'Import: Client não importado.');
        }
    }
}
echo json_encode(array("total" => $total, "ok" => $ok, "notok" => $notok, "notoklist" => $notoklist));
unlink("$docRoot/AM/ajax/files/$uploadedfile");
unlink("$docRoot/AM/ajax/files/$ConvertedFile");
