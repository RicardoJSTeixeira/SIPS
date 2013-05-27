<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
?>



<?php
if ( file_exists("/etc/astguiclient.conf") )
	{
	$DBCagc = file("/etc/astguiclient.conf");
	foreach ($DBCagc as $DBCline) 
		{
		$DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
		if (ereg("^VARDB_server", $DBCline))
			{$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
		
		}
	}
else
	{
	#defaults for DB connection
	$VARDB_server = 'localhost';
	}
        
$estado = $_POST[estado];
$user = $_SERVER['PHP_AUTH_USER'];

$con = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
if (!$con) {
    die('Não me consegui ligar' . mysql_error());
}


if ($estado == NULL) {
    $estado = 'activo';
};


mysql_select_db("asterisk", $con);

$grupo = mysql_query("SELECT user_group from vicidial_users where user = '$user'") or die(mysql_query());
$grupo = mysql_fetch_assoc($grupo);
$grupo = $grupo['user_group'];



if ($grupo == 'ADMIN') {

    if ($estado == 'activo') {
        $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'Y' ") or die(mysql_error());
    } else {
        if ($estado == 'inactivo') {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'N' ") or die(mysql_error());
        } else {
            $colab = mysql_query("SELECT * FROM vicidial_users") or die(mysql_error());
        }
    }
} else {
if ($grupo == 'Agentes') {
    if ($estado == 'activo') {
        $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'Y' AND user_group = '$grupo'") or die(mysql_error());
    } else {
        if ($estado == 'inactivo') {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'N' AND user_group = '$grupo'") or die(mysql_error());
        } else {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE user_group = '$grupo'") or die(mysql_error());
        }
    }
} else {
	
	if ($estado == 'activo') {
	$colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'Y' AND user_group = '$grupo'") or die(mysql_error()); 
	
	} else {
		if ($estado == 'inactivo') {
			$colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'N' AND user_group = '$grupo'") or die(mysql_error());
		}
		else { 
        		$colab = mysql_query("SELECT * FROM vicidial_users WHERE user_group = '$grupo'") or die(mysql_error());
                } 
		} }
}
mysql_close($con);
?>



<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='/images/icons/group.png' /></td>
            <td id='submenu-title'> Colaboradores </td>
            <td style='text-align:left'> </td>
            <td>
                <form name="festado" action="listausers.php" target="_self" method="post">
                    <label style="display: inline;vertical-align:middle">
                        <input type="radio" name="estado" value="activo" style="vertical-align:middle" <?= ($estado == 'activo') ? "checked" : "" ?>>
                        Activos</label>
                    <label style="display: inline;vertical-align:middle">
                        <input type="radio" name="estado" value="inactivo" style="vertical-align:middle" <?= ($estado == 'inactivo') ? "checked" : "" ?>>
                        Inactivos</label>
                    <label style="display: inline;vertical-align:middle">
                        <input type="radio" name="estado"  value="todos" style="vertical-align:middle" <?= ($estado == 'todos') ? "checked" : "" ?>>
                        Todos</label>
                    <input type="submit" value="Submeter" />
                </form>
            </td>
        </tr>
    </table>
</div>
<br>
<div id=work-area style=margin-top:8px;>
    <table id='lists' >
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Nome</th>
                <th>Username</th>
                <th>Faltas</th>
                <th>Estatísticas</th>
                <th>Gravações</th>
                <th>Acções</th>
            </tr>
        </thead>
        <tbody>	
            <?php
            for ($i = 0; $i < mysql_num_rows($colab); $i++) {
                $rcolab = mysql_fetch_assoc($colab);
                ?>		
                <tr>
                    <td><?= $rcolab['user_group'] ?></td>
                    <td><?= $rcolab['full_name'] ?></td>
                    <td><a href='editauser.php?user=<?= $rcolab['user'] ?>' target='_self' ><?= $rcolab['user'] ?></a></td>
                    <td>
                        <a href='presencas.php?user=<?= $rcolab['user'] ?>' target='_self' ><img src='/images/icons/calendar.png' /></a>
                    </td>
                    <td>
                        <a href='../user_stats.php?user=<?= $rcolab['user'] ?>' target='_self' ><img src='/images/icons/chart_pie.png' /></a>
                    </td>
                    <td>
                        <a href='gravacoes.php?user=<?= $rcolab['user'] ?>' target='_self' ><img src='/images/icons/headphone.png' /></a>
                    </td>
                    <td>
                        <a href='editauser.php?user=<?= $rcolab['user'] ?>' target='_self' ><img src='/images/icons/livejournal.png' alt='Apagar' title='Editar Utilizador'  /></a>
                        <img class='ai' onclick=ChangeUserStatus('<?= $rcolab['user_id'] ?>') id='<?= $rcolab['user_id'] ?>' src='/images/icons/<?= ($rcolab['active'] == "Y") ? "tick" : "cross" ?>.png' alt='Activo' title='Activo' style='cursor:pointer' > 
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


</div>   
<script>
    var otable = $('#lists').dataTable({
        "bJQueryUI": true,
        "sDom": 'l<"top"f>rt<"bottom"p>',
        "sPaginationType": "full_numbers",
        "aoColumns": [{
                "bSortable": true
            }, {
                "bSortable": true
            }, {
                "bSortable": true
            }, {
                "bSortable": false
            }, {
                "bSortable": false
            }, {
                "bSortable": false
            }, {
                "bSortable": false
            }],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });


    function ChangeUserStatus(user_id) {

        if ($('#' + user_id).prop("src").match("cross")) {
            var active = "Y";
        } else {
            var active = "N";
        }

        $.ajax({
            type: "POST",
            url: "_requests.php",
            data: {action: "user_change_status", user: user_id, active: active},
            success: function(aData) {
                if (active == "N") {
                    $('#' + user_id).prop("src", "/images/icons/cross.png")
                } else {
                    $('#' + user_id).prop("src", "/images/icons/tick.png")
                }
            }
        });
    }
</script>
</body>
</html>
