<?
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");
	
	
	$stmt="SELECT did_id,did_pattern,did_description,did_active,did_route,record_call from vicidial_inbound_dids order by did_pattern";
	$rslt=mysql_query($stmt, $link);
	$dids_to_print = mysql_num_rows($rslt);

?>
	<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />	
        <style>
            table tbody td{
               vertical-align: middle !important; 
            }
        </style>
    </head>
    <body>
            <div class=content>
            <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Lista de DID's</div>
                        <div class="pull-right"><a href="new_did.php" class="btn btn-large btn-primary">Novo</a></div>
                        <div class="clear"></div>
                    </div>
             <table class="table table-mod">
                        <thead>
                            <tr>
                                <th>Nome da Lista</th>
                                <th>Descrição</th>
                                <th>Activa</th>
                                <th>Rota </th>
                                <th>Gravação</th>
                            </tr>
                        </thead>
                        <tbody>
                            
	<?php
	

	while ($row=mysql_fetch_row($rslt)) 
		{
                
                switch ($row[4]) {
                    case "AGENT": $rota="Agente";
                        break;
                    case "EXTEN": $rota="Extenção";
                        break;
                    case "VOICEMAIL": $rota="VoiceMail";
                        break;
                    case "PHONE": $rota="Licença";
                        break;
                    case "IN_GROUP": $rota="Grupo Inbound";
                        break;
                    case "CALLMENU": $rota="IVR";
                        break;
                    default:
                        break;
                }
		#echo "<tr $bgcolor><td><a href=\"$PHP_SELF?ADD=3311&did_id=$row[0]\">$row[0]</a></td>";
                ?>
                            <tr>
		<td><a href="edit_did.php?did_id=<?=$row[0]?>" ><i class="icon-pencil"></i> <?=$row[1]?></a></td>
		<td> <?=$row[2]?></td>
		<td> <?=($row[3]=="Y")?"Sim":"Não"?></td>
		<td> <?=$rota?></td>
		<td> <?=($row[5]=="Y")?"Sim":"Não"?></td>
                </tr>
		<?php
		}
?>
            
                        </tbody>
             </table>
            </div>
            </div>
           <?php if (isset($_GET["success"])) { ?> 
            <script>
                $(function() {
                    makeAlert("#wr", "Successo", "Agora pode configurar ao pormenor o seu novo DDI :-).", 4, false, false);
                });
            </script>
<?php } 
	
#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>	