<?php
require("../dbconnect.php");

$user = $_SERVER['PHP_AUTH_USER'];

$stmt = "SELECT user_group FROM vicidial_users WHERE user LIKE '$user'";

$grupos = mysql_query($stmt, $link);

$grupos = mysql_fetch_assoc($grupos);

$grupos_a = $grupos['user_group'];


$data = date('o-m-d');




if ($grupos_a == 'ADMIN') {
    $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups", $link) or die(mysql_error());
} else {
    $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups WHERE user_group='$grupos_a'", $link) or die(mysql_error());
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <style>
            .chzn-select{
                width: 350px;
            }
            #loader{
                background: #f9f9f9;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
        </style>
    </head>

    <body>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Criar Novo Operador</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form action="inserecolab.php" name="novocolab" id="novocolab" method="post" target="_self" onsubmit="return validateForm()">
                        <input type="hidden" name="data" value="<?= $data ?>"  /><input type="hidden" name="dentrada" value="<?= $data ?>" >
                        <div class="formRow op fix">
                            <label>Nome:</label>
                            <div class="formRight">
                                <input type="text" name="nome" required class="span"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Username:</label>
                            <div class="formRight">
                                <input type="text" name="user" required class="span"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Password:</label>
                            <div class="formRight">
                                <input type="password" name="password" required class="span"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Tipo Utilizador:</label>
                            <div class="formRight">
                                <select  name='usertype'>
                                    <option value=admin>Administrador</option>
                                    <option value=operador>Operador</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Grupo de Operadores:</label>
                            <div class="formRight">
                                <select name='usergroup'>
                                    <?php while ($a = mysql_fetch_assoc($grupos)) { ?>
                                        <option value = '<?= $a['user_group'] ?>' <?= (($a['user_group'] == $b['user_group']) ? "selected" : "") ?> ><?= $a['group_name'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> 
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>