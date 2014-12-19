<?php

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');
header('Content-type: application/json');

require("$root/ini/db.php");

$id = filter_var($_POST['id']);
$oValues = filter_var($_POST['ovalues']);
$oValues = json_decode($oValues);


$stmt = $db->prepare("UPDATE vicidial_list SET
                            phone_number=:phone_number,
                            title=:title,
                            first_name=:first_name,
                            middle_initial=:middle_initial,
                            last_name=:last_name,
                            address1=:address1,
                            address2=:address2,
                            address3=:address3,
                            city=:city,
                            state=:state,
                            province=:province,
                            postal_code=:postal_code,
                            country_code=:country_code,
                            date_of_birth=:date_of_birth,
                            alt_phone=:alt_phone,
                            email=:email,
                            comments=:comments,
                            extra1=:extra1,
                            extra2=:extra2,
                            extra3=:extra3,
                            extra4=:extra4,
                            extra5=:extra5,
                            extra6=:extra6,
                            extra7=:extra7,
                            extra8=:extra8,
                            extra9=:extra9,
                            extra10=:extra10,
                            extra11=:extra11,
                            extra12=:extra12,
                            extra13=:extra13,
                            extra14=:extra14,
                            extra15=:extra15
                            WHERE lead_id=:lead_id");

$result = $stmt->execute(
    array(
        'phone_number' => $oValues->phone_number,
        'title' => $oValues->title,
        'first_name' => $oValues->first_name,
        'middle_initial' => $oValues->middle_initial,
        'last_name' => $oValues->last_name,
        'address1' => $oValues->address1,
        'address2' => $oValues->address2,
        'address3' => $oValues->address3,
        'city' => $oValues->city,
        'state' => $oValues->state,
        'province' => $oValues->province,
        'postal_code' => $oValues->postal_code,
        'country_code' => $oValues->country_code,
        'date_of_birth' => $oValues->date_of_birth,
        'alt_phone' => $oValues->alt_phone,
        'email' => $oValues->email,
        'comments' => $oValues->comments,
        'extra1' => $oValues->extra1,
        'extra2' => $oValues->extra2,
        'extra3' => $oValues->extra3,
        'extra4' => $oValues->extra4,
        'extra5' => $oValues->extra5,
        'extra6' => $oValues->extra6,
        'extra7' => $oValues->extra7,
        'extra8' => $oValues->extra8,
        'extra9' => $oValues->extra9,
        'extra10' => $oValues->extra10,
        'extra11' => $oValues->extra11,
        'extra12' => $oValues->extra12,
        'extra13' => $oValues->extra13,
        'extra14' => $oValues->extra14,
        'extra15' => $oValues->extra15,
        'lead_id' => $id)
);

echo json_encode($result);