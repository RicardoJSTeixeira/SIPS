<?php
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$path.="../";}
print'
<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Go Contact Center</title>
<link type="text/css" rel="stylesheet" title="sipsdefault" href="'.$path.'css/style.css" />
<link type="text/css" rel="stylesheet" href="'.$path.'jquery/themes/flick/flick.css" />
<link type="text/css" rel="stylesheet" href="'.$path.'jquery/jsdatatable/css/jquery.dataTables_themeroller.css" />
<link type="text/css" rel="stylesheet" href="'.$path.'jquery/colourPicker/colourPicker.css" />
<link rel="stylesheet" href="'.$path.'jquery/uniform/css/uniform.default.css" type="text/css" media="screen" charset="utf-8" />

<script type="text/javascript" src="'.$path.'jquery/jquery-1.8.3.js"></script>
<script type="text/javascript" src="'.$path.'jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="'.$path.'jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
<script type="text/javascript" src="'.$path.'jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="'.$path.'jquery/jqueryUI/plugins/datetimepicker.js"></script>
<script type="text/javascript" src="'.$path.'jquery/jqueryUI/language/pt-pt.js"></script>
<script type="text/javascript" src="'.$path.'jquery/colourPicker/colourPicker.js"></script>
<script type="text/javascript" src="'.$path.'functions/functions.js"></script>

<script src="'.$path.'jquery/uniform/jquery.uniform.js" type="text/javascript"></script>

<script src="'.$path.'jquery/flot/jquery.flot.js" type="text/javascript"></script>


</head>
<body>
';
require("dbconnect.php");
//require("functions.php"); <--- não funca :\
date_default_timezone_set('Europe/Lisbon');
?>



<?
### CÓDIGO DE INICIALIZAÇÃO
/*

<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>

<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>

*/
?>
