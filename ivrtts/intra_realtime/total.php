<?php

$tempo = $_POST['temporal'];
$id = $_POST['id'];

$file = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,$tempo,status&database.campaign.oid=$id");

$content = json_decode($file, true);

$p = array('msg' => array(), 'sys' => array(), 'total' => array());
foreach ($content as $value) {

    $p['total'][$value['_id'][$tempo]]+=$value['count'];

    switch (this . _id . status . oid) {
        case "MSG001":
        case "MSG002":
        case "MSG003":
        case "MSG004":
        case "MSG005":
        case "MSG006":
        case "MSG007":
        case "NEW":
            $p['msg'][$value['_id'][$tempo]]+=$value['count'];
            break;
        default :
            $p['sys'][$value['_id'][$tempo]]+=$value['count'];
            break;
    }
}
$msg=array();
foreach ($p['msg'] as $key=>$value) {
    $msg[]=(object)array("x"=>$key,"y"=>$value);
}
$sys=array();
foreach ($p['sys'] as $key=>$value) {
    $sys[]=(object)array("x"=>$key,"y"=>$value);
}
$total=array();
foreach ($p['total'] as $key=>$value) {
    $total[]=(object)array("x"=>$key,"y"=>$value);
}
    

echo json_encode(array('msg'=>$msg,'sys'=>$sys,'total'=>$total));