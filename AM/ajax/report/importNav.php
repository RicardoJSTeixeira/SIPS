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

$docRoot = $_SERVER[DOCUMENT_ROOT];

$ConvertCommand = "$docRoot/sips-admin/campaigns/extras/upload/sheet2tab.pl $docRoot/AM/ajax/files/$UploadedFile $docRoot/AM/ajax/files/$UploadedFile";
passthru($ConvertCommand);

$file = fopen("$docRoot/AM/ajax/files/$ConvertedFile", "r");
$headers = explode("\t", trim(fgets($file, 4096)));

$stmt = $db->prepare("UPDATE sips_sd_reservations SET extra_id=:navid WHERE id_reservation=:id");
while (!feof($file)) {

    $buffer = trim(fgets($file, 4096));
    if (strlen($buffer) > 0) {

        $buffer = stripslashes($buffer);
        $buffer = explode("\t", $buffer);

        $LineCount++;
        $buffer[$index];

        $stmt->execute(array(":navid" => $buffer[40], ":id" => $buffer[39]));
    }
    
}
echo json_encode(true);