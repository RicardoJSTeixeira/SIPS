
        <?php
        $user = $_GET['user'];

        $logged_user = $_SERVER['PHP_AUTH_USER'];
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
            die('Não me consegui ligar' . mysql_error());
        }
        mysql_select_db("sips", $con);

        $dbuser = mysql_query("SELECT * FROM t_colaborador WHERE uservici='$user'") or die(mysql_error());

        $a = mysql_fetch_assoc($dbuser);

        $nome = $a['nome'];
        $morada = $a['morada'];
        $codpostal = $a['codpostal'];
        $tlf = $a['telefone'];
        $tlml = $a['telemovel'];
        $email = $a['email'];
        $bi = $a['bi'];
        $nif = $a['nif'];
        $segsocial = $a['segsocial'];
        $datanasc = $a['datanasc'];
        $hablit = $a['hablit'];
        $banco = $a['banco'];
        $nib = $a['nib'];
        $estcivil = $a['estcivil'];
        $dependentes = $a['ndepend'];
        $act = $a['activo'];
        $data = $a['datainsc'];


        mysql_select_db("asterisk", $con);
        $user_grp = mysql_query("SELECT user_group FROM vicidial_users WHERE user = '$user'") or die(mysql_error());




        $tryADMIN = mysql_query("SELECT user_group FROM vicidial_users WHERE user = '$logged_user'") or die(mysql_error());

        $tryADMIN = mysql_fetch_assoc($tryADMIN);
        if ($tryADMIN['user_group'] == 'ADMIN') {
            $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups") or die(mysql_error());
        } else {
            $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups where user_group = '$tryADMIN[user_group]' ") or die(mysql_error());
        }
        mysql_close($con);
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
                    <div class="pull-left">Detalhes do Colaborador</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form action="editacolab2.php" name="editacolab" method="post" target="_self" >
                        <input type="hidden" name="user" value="<?= $user; ?>" />
                        <div class="formRow op fix">
                            <label>Nome:</label>
                            <div class="formRight">
                                <input type="text" name="nome" class="span" value="<?= $nome; ?>"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Morada:</label>
                            <div class="formRight">
                                <input type="text" name="morada" class="span" value="<?= $morada ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Codigo Postal:</label>
                            <div class="formRight">
                                <input type="text" name="codpostal" class="span" value="<?= $codpostal ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Telefone:</label>
                            <div class="formRight">
                                <input type="text" name="tlf" id="tlf" class="span" value="<?= $tlf ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Telemovel:</label>
                            <div class="formRight">
                                <input type="text" name="tlml" id="tlml" class="span" value="<?= $tlml ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Email:</label>
                            <div class="formRight">
                                <input name="email" type="text" id="email" class="span" value="<?= $email ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Email:</label>
                            <div class="formRight">
                                <input name="email" type="text" id="email" class="span" value="<?= $email ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>BI:</label>
                            <div class="formRight">
                                <input type="text" name="bi" id="bi" class="span" value="<?= $bi ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>NIF:</label>
                            <div class="formRight">
                                <input type="text" name="nif" id="nif" class="span" value="<?= $nif ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Segurança Social:</label>
                            <div class="formRight">
                                <input type="text" name="segsocial" id="segsocial" class="span" value="<?= $segsocial ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Data de Nascimento:</label>
                            <div class="formRight">
                                <input type="text" name="datanasc" id="datanasc" class="span" value="<?= $datanasc ?>"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Habilitações Literarias:</label>
                            <div class="formRight">
                                <select name="hablit" id="hablit">
                                    <option <?= ($hablit == '---') ? "selected" : "" ?> value="---">---</option>
                                    <option <?= ($hablit == 'menos9') ? "selected" : "" ?> value="menos9">Inferior 9º Ano</option>
                                    <option <?= ($hablit == '9ano') ? "selected" : "" ?> value="9ano">9º Ano</option>
                                    <option <?= ($hablit == '12ano') ? "selected" : "" ?> value="12ano">12º Ano</option>
                                    <option <?= ($hablit == 'Lic') ? "selected" : "" ?> value="Lic">Licenciatura</option>
                                    <option <?= ($hablit == 'Mestre') ? "selected" : "" ?> value="Mestre">Mestrado</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Banco:</label>
                            <div class="formRight">
                                <input name="banco" type="text" id="banco" class="span" value="<?= $banco ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>NIB:</label>
                            <div class="formRight">
                                <input name="nib" type="text" id="nib" class="span" value="<?= $nib ?>"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Estado Civil:</label>
                            <div class="formRight">
                                <select name="estcivil" id="estcivil">
                                    <option <?= ($estcivil == '---') ? "selected" : "" ?> value="---">---</option>
                                    <option <?= ($estcivil == 'Solteiro(a)') ? "selected" : "" ?> value="Solteiro(a)">Solteiro(a)</option>
                                    <option <?= ($estcivil == 'Casado(a)') ? "selected" : "" ?> value="Casado(a)">Casado(a)</option>
                                    <option <?= ($estcivil == 'União Facto') ? "selected" : "" ?> value="União Facto">União Facto</option>
                                    <option <?= ($estcivil == 'Divorciado(a)') ? "selected" : "" ?> value="Divorciado(a)">Divorciado(a)</option>
                                    <option <?= ($estcivil == 'Viúvo(a)') ? "selected" : "" ?> value="Viúvo(a)">Viúvo(a)</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Nº de Dependentes:</label>
                            <div class="formRight">
                                <input name="dependentes" type="text" id="dependentes" value="<?= $dependentes ?>" class="span"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Remuneração:</label>
                            <div class="formRight">
                                <input name="valor" type="text" id="valor" class="span" value="<?= $valor; ?>" />
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Data de Entrada:</label>
                            <div class="formRight">
                                <input type="text" name="dentrada" class="span" value="<?= $data ?>" />
                            </div>
                        </div>  
                        <!--<div class="formRow op fix">
                            <label>Activo:</label>
                            <div class="formRight">
                                <select name="activo">
                                    <option value="1" <?= ($act == '1') ? "selected=selected" : "" ?> >Activo</option>
                                    <option value="0" <?= ($act == '0') ? "selected=selected" : "" ?> >Inactivo</option>
                                </select>
                            </div>
                        </div>--> 
                        <div class="formRow op fix">
                            <label>Grupo de Colaboradores:</label>
                            <div class="formRight">
                                <select name='usergroup'>
                                    <?php
                                    $b = mysql_fetch_assoc($user_grp);
                                    while ($a = mysql_fetch_assoc($grupos)) {
                                        ?>
                                        <option value = '<?= $a['user_group'] ?>' <?= ($a['user_group'] == $b['user_group']) ? "selected" : "" ?>><?= $a['group_name'] ?></option>
                                    <?php } ?>
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
