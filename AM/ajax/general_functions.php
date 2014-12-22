<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/msg_alerts.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();
$message = new messages($db, $u->username);
$alert = new alerts($db, $u->username);

switch ($action) {

    case "get_unread_messages":
        echo json_encode($message->getAll());
        break;

    case "edit_message_status":
        echo json_encode($message->setReaded($id_msg));
        break;

    case "edit_message_status_by_user":
        echo json_encode($message->setAllReaded($u->username));
        break;

    case "get_alerts":
        echo json_encode($alert->getAll());
        break;

    case "set_readed":
        echo json_encode($alert->setReaded($id_msg));
        break;

    case "set_all_readed":
        echo json_encode($alert->setAllReaded($u->username));
        break;

    case "make_alert":
        echo json_encode($alert->make($is_for, $alert, $section, $record_id, $cancel));
        break;

    case "send_email":
        include("../lib_php/sendmail.php");
        $UserC = new UserControler($db, $u);
        echo json_encode(SendEmail::fnSendEmailAlert($db, $UserC, $u, $tres, $seis));
        break;
}

class SendEmail
{

    static function fnSendEmailAlert(PDO $db, UserControler $UserC, $user, $tres, $seis)
    {
        $BossUser = (object) array("user" => "RGE", "full_name" => "rge@acusticamedica.pt");
        $aoParents = $UserC->getAll($UserC::ASM, $user->alias);

        if ($seis)
            $aoParents[] = $BossUser;

        foreach ($aoParents AS $oParent) {

            $email = $oParent->full_name;

            if (!SendEmail::fnIsEmail($email))
                continue;

            if (SendEmail::fnWasSended($db, $user->alias, $oParent->user))
                continue;

            $msg = SendEmail::fnMakeMsg($user, $tres, $seis);

            if (send_email($email, "SPICE ALERTA Acústica Médica", $msg, "ALERTA, USER $user->alias PREGUIÇOSO"))
                SendEmail::fnLogSendMail($db, $user->alias, $oParent->user);
            else
                break;

        }

        return true;

    }

    static function fnWasSended(PDO $db, $username, $parent)
    {

        $stmt = $db->prepare("SELECT id FROM spice_email_alert WHERE user=:username AND parent=:parent AND send_date=DATE(NOW())");
        $stmt->execute(array(":username" => $username,":parent" => $parent));

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    static function fnLogSendMail(PDO $db, $username, $parent)
    {
        $stmt = $db->prepare("INSERT INTO spice_email_alert (user, send_date, send_time, parent) VALUES (:username, DATE(NOW()), TIME(NOW()), :parent)");
        return $stmt->execute(array(":username" => $username,":parent" => $parent));
    }

    static function fnMakeMsg($user, $tres, $seis)
    {
        return "<h3>ALERTA, USER $user->alias</h3>

<strong>Consultas com mais de 3dias:</strong> $tres
<br>
<br>

<strong>Consultas com mais de 6dias:</strong> $seis
<br>
<br>

<strong>Submetido por:</strong> $user->username - $user->name";
    }

    static function fnIsEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}