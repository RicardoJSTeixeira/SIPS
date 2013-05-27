<?php

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
        <title>Criar IVR</title>

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

if ($ADD==2511)
	{
	##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
	$stmt = "SELECT value FROM vicidial_override_ids where id_table='vicidial_call_menu' and active='1';";
	$rslt=mysql_query($stmt, $link);
	$voi_ct = mysql_num_rows($rslt);
	if ($voi_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$menu_id = ($row[0] + 1);

		$stmt="UPDATE vicidial_override_ids SET value='$menu_id' where id_table='vicidial_call_menu' and active='1';";
		$rslt=mysql_query($stmt, $link);
		}
	##### END ID override optional section #####


	$stmt="SELECT count(*) from vicidial_call_menu where menu_id='$menu_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{
		?>
        <script>
            $(function(){makeAlert("#wr","IVR não criado","Já existe um IVR com este nome.",1,true,false);});
        </script>
            <?php
		}
	else
		{
		if ( (preg_match("/^vicidial$/i",$menu_id)) or (preg_match("/^vicidial-auto$/i",$menu_id)) or (preg_match("/^general$/i",$menu_id)) or (preg_match("/^globals$/i",$menu_id)) or (preg_match("/^default$/i",$menu_id)) or (preg_match("/^trunkinbound$/i",$menu_id)) or (preg_match("/^loopback-no-log$/i",$menu_id)) or (preg_match("/^monitor_exit$/i",$menu_id)) or (preg_match("/^monitor$/i",$menu_id)) )
			{
			?>
        <script>
            $(function(){makeAlert("#wr","IVR não criado","ID Invalido. Por favor escolha outro",1,true,false);});
        </script>
            <?php
			}
		else
			{
			if ( (strlen($menu_id) < 2) or (eregi(' ',$menu_id)) )
				{
				?>
        <script>
            $(function(){makeAlert("#wr","IVR não criado","O ID do IVR tem de conter entre 2 e 50 caracteres e não pode conter espaços.",1,true,false);});
        </script>
            <?php
				}
			else
				{
				$stmt="INSERT INTO vicidial_call_menu (menu_id,menu_name) values('$menu_id','$menu_name');";
				$rslt=mysql_query($stmt, $link);

				
				
				
				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CALLMENUS', event_type='ADD', record_id='$menu_id', event_code='ADMIN ADD CALL MENU', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);

				echo "<script type='text/javascript'> window.location='list_ivr.php?success=1'; </script>";
                                
				}
			}
		}
                
	
		##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
		$stmt = "SELECT count(*) FROM vicidial_override_ids where id_table='vicidial_call_menu' and active='1';";
		$rslt=mysql_query($stmt, $link);
		$voi_ct = mysql_num_rows($rslt);
		if ($voi_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$voi_count = "$row[0]";
			}
		##### END ID override optional section #####
        }
?>
		<div class=content>
                    
                <form action='<?=$PHP_SELF?>' method='POST'>
		<input type=hidden name=ADD value=2511>
                    
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Criar IVR</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
                        <div id='wr'></div>
                        <div class="formRow op fix <?=($voi_count > 0)?"hide":""?>">
                            <label data-t="tooltip" title=""><?=help("vicidial_call_menu-menu_id","ID do Grupo")?>:</label>
                            <div class="formRight">
                                <?=($voi_count > 0)?"":"<input type=text name=menu_id class='span' maxlength=50>"?>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_call_menu-menu_name","Nome do Menu")?>:</label>
                            <div class="formRight">
                                <input type=text name=menu_name class="span" maxlength=100>
                            </div>
                        </div>  
                            <div class="clear"></div>
                            <div class="seperator_dashed"></div>
                            <p class="text-right">
                                <button class="btn btn-success">Gravar</button>
                            </p>
                    </div>    
                </div>
    </body>
</html>
		

		  
		
	

