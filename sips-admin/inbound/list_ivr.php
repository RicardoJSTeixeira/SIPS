<?
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");


$stmt="SELECT menu_id,menu_name,menu_prompt,menu_timeout from vicidial_call_menu order by menu_id";
	$rslt=mysql_query($stmt, $link);
	$menus_to_print = mysql_num_rows($rslt);
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
                        <div class="pull-left">Listas de IVR's</div>
                        <div class="pull-right"><a href="new_ivr.php" class="btn btn-large btn-primary">Novo</a></div>
                        <div class="clear"></div>
                    </div>
             <table class="table table-mod">
                        <thead>
                            <tr>
                                <th>MENU ID</th>
                                <th>Nome</th>
                                <th>PROMPT</th>
                                <th>Time Out</th>
                                <th>Opções</th>
                            </tr>
                        </thead>
                        <tbody>
	

<?php
	$o=0;
	$menu_id = $MT;

	while ($menus_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$menu_id[$o] =		$row[0];
		$menu_name[$o] =	$row[1];
		$menu_prompt[$o] =	$row[2];
		$menu_timeout[$o] =	$row[3];
		$o++;
		}

	$o=0;
	while ($menus_to_print > $o) 
		{
		$stmt="SELECT count(*) from vicidial_call_menu_options where menu_id=\"$menu_id[$o]\";";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
?>
                    <tr>
                    <td><a href="edit_ivr.php?menu_id=<?=$menu_id[$o]?>"><i class="icon-pencil"></i> <?=$menu_id[$o]?></a></td>
                    <td> <?=$menu_name[$o]?></td>
                    <td> <?=$menu_prompt[$o]?></td>
                    <td> <?=$menu_timeout[$o]?></td>
                    <td> <?=$row[0]?></td>
                    </tr>
		<?php
                $o++;
		}
?>
	 </tbody>
             </table>
            </div>
            </div>
<?php if (isset($_GET["success"])) { ?> 
            <script>
                $(function() {
                    makeAlert("#wr", "Successo", "Agora pode configurar ao pormenor o seu novo IVR :-).", 4, false, false);
                });
            </script>
<?php } 
	
#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>		