<?
function help($where, $text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
}
$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Criar DDI</title>

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
    </head>
    <body>
<?php
if ($ADD==2311)
	{
	$stmt="SELECT count(*) from vicidial_inbound_dids where did_pattern='$did_pattern';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{ ?>
        <script>
            $(function(){makeAlert("#wr","DID não adicionado","Já existe um DDI com este Nº.",1,true,false);});
        </script>
            <?php }
	else
		{
			if ( (strlen($did_pattern) < 2) or (eregi(' ',$did_pattern)) or (eregi('-',$did_pattern)) or (eregi("\+",$did_pattern)) )
				{ ?>
                        <script>
                            $(function(){makeAlert("#wr","DDI não adicionado","Verifique os dados inseridos.\nA Extensão deve conter no minimo 2 caracteres.",1,true,true);});
                        </script>
                            <?php }
			else
				{
				$stmt="INSERT INTO vicidial_inbound_dids (did_pattern,did_description) values('$did_pattern','$did_description');";
				$rslt=mysql_query($stmt, $link);

				$stmt="SELECT did_id from vicidial_inbound_dids where did_pattern='$did_pattern';";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$did_id = $row[0];

				

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='DIDS', event_type='ADD', record_id='$did_id', event_code='ADMIN ADD DID', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				
				
				echo "<script type='text/javascript'>  
				window.location = 'list_did.php?success=1';
				</script>";
				
				
				}
			}
		}
	
	
?>
 
		 <div class=content>

		<form action=new_did.php method=POST>
		<input type=hidden name=ADD value=2311>
                
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Edita Grupo Inbound</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
                        <div id="wr"></div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_dids-did_pattern","Extensão DDI")?>:</label>
                            <div class="formRight">
                                <input type=text name=did_pattern class="span" maxlength=50>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_dids-did_description","Descrição da DDI")?>:</label>
                            <div class="formRight">
                                <input type=text name=did_description class="span" maxlength=50>
                            </div>
                        </div> 
                         <div class="clear"></div>
                    <div class="seperator_dashed"></div>
                    <div class="grid-content">
                    <p class="text-right">
                        <button class="btn btn-success">Adicionar</button>
                    </p>
                    </div>


                    </FORM>
                </div>
        </div>
    </div>
	
	<?php	
		
#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>	