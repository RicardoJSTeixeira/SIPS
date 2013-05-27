<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SIPS - Edição de Colaborador</title>


    </head>
    <body>
    <?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

    if (!isset($_POST['user'])) {exit;}
    
    $user = $_POST['user'];
    $nome = $_POST['nome'];
    $morada = $_POST['morada'];
    $codpostal = $_POST['codpostal'];
    $tlf = $_POST['tlf'];
    $tlml = $_POST['tlml'];
    $email = $_POST['email'];
    $bi = $_POST['bi'];
    $nif = $_POST['nif'];
    $segsocial = $_POST['segsocial'];
    $datanasc = $_POST['datanasc'];
    $hablit = $_POST['hablit'];
    $banco = $_POST['banco'];
    $nib = $_POST['nib'];
    $estcivil = $_POST['estcivil'];
    $dependentes = $_POST['dependentes'];
    $data = $_POST['dentrada'];
    $usergroup = $_POST['usergroup'];

    if (file_exists("/etc/astguiclient.conf")) {
        $DBCagc = file("/etc/astguiclient.conf");
        foreach ($DBCagc as $DBCline) {
            $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/", "", $DBCline);
            if (ereg("^VARDB_server", $DBCline)) {
                $VARDB_server = $DBCline;
                $VARDB_server = preg_replace("/.*=/", "", $VARDB_server);
            }
        }
    } else {
        #defaults for DB connection
        $VARDB_server = 'localhost';
    }
    $con = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
    if (!$con) {
        die('Não me consegui ligar 1' . mysql_error());
    }
    mysql_select_db("sips", $con);
    if ($_GET['accao'] == 'delete') {

        mysql_query("DELETE FROM t_colaborador WHERE uservici = '$user'") or die(mysql_error());
        mysql_select_db("asterisk", $con);
        mysql_query("DELETE FROM vicidial_users WHERE user = '$user'") or die(mysql_error());

        echo "<script type='text/javascript'>
		alert('Colaborador apagado com sucesso');
		location.replace('listausers.php');
	</script>";
    } else {

        mysql_query("UPDATE t_colaborador SET
	nome = '" . $nome . "',
	morada = '" . $morada . "',
	codpostal = '$codpostal',
	telefone = '$tlf',
	telemovel = '$tlml',
	email = '$email',
	bi = '$bi',
	nif = '$nif',
	segsocial = '$segsocial',
	datanasc  = '$datanasc',
	hablit = '$hablit',
	banco = '" . $banco . "',
	nib = '" . $nib . "',
	estcivil = '$estcivil',
	ndepend = '$dependentes',
	activo = '1',
	htrab = '$turno',
	datainsc = '$data'
	WHERE uservici = '$user'") or die(mysql_error());

        mysql_select_db("asterisk", $con);
        mysql_query("UPDATE vicidial_users SET full_name = '$nome', user_group = '$usergroup' WHERE user = '$user'") or die(mysql_error());

        /* 	if ($act == '0') {

          mysql_select_db("asterisk", $con);
          mysql_query("UPDATE vicidial_users SET active = 'N' WHERE user = '$user'") or die(mysql_error());

         */
        echo "<script type='text/javascript'>
	location.replace('editauser.php?user=$user')

	</script>";

    }

        mysql_close($con);
        ?>
        
    </body>
</html>