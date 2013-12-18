<?php

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$file = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/avg/calls/length_in_sec?by=database.campaign,status,".implode($tempo,',')."&database.campaign.oid=$id");

$content = json_decode($file, true);

$p = array('msg' => array(), 'sys' => array(), 'total' => array());
foreach ($content as $value) {
    $ref="";
    foreach ($tempo as $tempinho) {
        $ref.="-".$value['_id'][$tempinho];
    }
    $ref=ltrim($ref, '-');
    
    $p['total'][$ref]+=intval($value['avg'])/60;

    switch (this . _id . status . oid) {
        case "MSG001":
        case "MSG002":
        case "MSG003":
        case "MSG004":
        case "MSG005":
        case "MSG006":
        case "MSG007":
        case "NEW":
            $p['msg'][$ref]+= intval($value['avg'])/60;
            break;
        default :
            $p['sys'][$ref]+= intval($value['avg'])/60;
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