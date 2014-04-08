<?php

require '../../../ini/db.php';

$VARDB_server='goviragem.dyndns.org';
$server = "http://$VARDB_server:10000/ccstats/v0/";
$links = filter_var($_POST['q']);

$get = file_get_contents($server.$links);
//$content = json_decode($get);

echo $get;
