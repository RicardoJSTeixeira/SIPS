<?php

$telefono = $_POST['phone'];
$client = new SoapClient("http://pi.necomplus.pt/centralitaSIPS/CentralitaSIPSWs.asmx?WSDL",array('login' => 'usrSIPS', 'password' => "SD03495j0f", "connection_timeout"=>30,'trace'=>true,'keep_alive'=>false,'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
$req = Array();
$req['telefono'] = '289310000';
$val = $client->ObtenerDatosComercio($req);
$xmlResult = $val->ObtenerDatosComercioResult->any;
$xmlResult = simplexml_load_string($xmlResult);
$jsonResult = json_encode($xmlResult);
echo $jsonResult;

?>



