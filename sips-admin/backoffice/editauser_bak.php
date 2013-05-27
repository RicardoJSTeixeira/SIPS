<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/css/style.css" rel="stylesheet" type="text/css" />
        <title>SIPS - Backoffice</title>

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
        $turno = $a['htrab'];


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


    </head>

    <body>

        <div class=cc-mstyle>
            <table>
                <tr>
                    <td id='icon32'><img src='/images/icons/user_32.png' /></td>
                    <td id='submenu-title'> Detalhes do Colaborador </td>
                    <td style='text-align:left'></td>
                </tr>
            </table>
        </div>

        <div id=work-area>
            <br>
            <br>

            <div class=cc-mstyle style='border:none; width:75%;'>

                <form action="editacolab2.php" name="editacolab" method="post" target="_self" >
                    <input type="hidden" name="user" value="<? echo $user; ?>" />

                    <table >
                        <tbody>
                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Nome </p></div></td>
                            <td><input type="text" name="nome" value="<?= $nome; ?>"/></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Morada </p></div></td>
                            <td><input type="text" name="morada" value="<?= $morada ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Codigo Postal </p></div></td>
                            <td><input type="text" name="codpostal" value="<?= $codpostal ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Telefone </p></div></td>
                            <td><input type="text" name="tlf" id="tlf" value="<?= $tlf ?>" /> </td>
                        </tr>
                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Telemovel </p></div></td>
                            <td><input type="text" name="tlml" id="tlml" value="<?= $tlml ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Email </p></div></td>
                            <td><input name="email" type="text" id="email" value="<?= $email ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> BI </p></div></td>
                            <td><input type="text" name="bi" id="bi" value="<?= $bi ?>" /></td>
                        </tr>
                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> NIF </p></div></td>
                            <td><input type="text" name="nif" id="nif" value="<?= $nif ?>" /></td>
                        </tr>


                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Segurança Social </p></div></td>
                            <td><input type="text" name="segsocial" id="segsocial" value="<?= $segsocial ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Data de Nascimento </p></div></td>
                            <td><input type="text" name="datanasc" id="datanasc" value="<?= $datanasc ?>"/></td>
                        </tr>
                    </tbody>
                    </table>
                    <br>
                    <table >
                        <tbody>
                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Habilitações Literarias </p></div></td>
                            <td>

                                <select name="hablit" id="hablit">
                                    <option <?= ($hablit == '---') ? "selected" : "" ?> value="---">---</option>
                                    <option <?= ($hablit == 'menos9') ? "selected" : "" ?> value="menos9">Inferior 9º Ano</option>
                                    <option <?= ($hablit == '9ano') ? "selected" : "" ?> value="9ano">9º Ano</option>
                                    <option <?= ($hablit == '12ano') ? "selected" : "" ?> value="12ano">12º Ano</option>
                                    <option <?= ($hablit == 'Lic') ? "selected" : "" ?> value="Lic">Licenciatura</option>
                                    <option <?= ($hablit == 'Mestre') ? "selected" : "" ?> value="Mestre">Mestrado</option>
                                </select>

                            </td>
                        </tr>


                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Banco </p></div></td>
                            <td><input name="banco" type="text" id="banco" size="40" value="<?= $banco ?>" /></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> NIB </p></div></td>
                            <td><input name="nib" type="text" id="nib" size="40" value="<?= $nib ?>"/></td>
                        </tr>


                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Estado Civil </p></div></td>
                            <td>

                                <select name="estcivil" id="estcivil">
                                    <option <?= ($estcivil == '---') ? "selected" : "" ?> value="---">---</option>
                                    <option <?= ($estcivil == 'Solteiro(a)') ? "selected" : "" ?> value="Solteiro(a)">Solteiro(a)</option>
                                    <option <?= ($estcivil == 'Casado(a)') ? "selected" : "" ?> value="Casado(a)">Casado(a)</option>
                                    <option <?= ($estcivil == 'União Facto') ? "selected" : "" ?> value="União Facto">União Facto</option>
                                    <option <?= ($estcivil == 'Divorciado(a)') ? "selected" : "" ?> value="Divorciado(a)">Divorciado(a)</option>
                                    <option <?= ($estcivil == 'Viúvo(a)') ? "selected" : "" ?> value="Viúvo(a)">Viúvo(a)</option>
                                </select>

                            </td>
                        </tr>


                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Nº de Dependentes </p></div></td>
                            <td><input name="dependentes" type="text" id="dependentes" value="<?= $dependentes ?>" size="5"/></td>
                        </tr>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Remuneração </p></div></td>
                            <td><input name="valor" type="text" id="valor" value="<?= $valor; ?>" /></td>
                        </tr>


                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Data de Entrada </p></div></td>
                            <td><input type="text" name="dentrada" value="<?= $data ?>" /></td> 
                        </tr>





                        <!--
                         <tr>
                        <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Activo </p></div></td>
                        <td>
                        
                        <select name="activo">
                        <option value="1" <?= ($act == '1') ? "selected=selected" : "" ?> >Activo</option>
                        <option value="0" <?= ($act == '0') ? "selected=selected" : "" ?> >Inactivo</option>
                        </select>
                        
                        </td>
                        </tr>
                        -->
                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Grupo de Colaboradores </p></div></td>
                            <td>


                                <select name='usergroup'>
                                    <?php
                                    $b = mysql_fetch_assoc($user_grp);
                                        while($a = mysql_fetch_assoc($grupos)){
                                        ?>
                                        <option value = '<?= $a['user_group'] ?>' <?= ($a['user_group'] == $b['user_group']) ? "selected" : "" ?>><?= $a['group_name'] ?></option>
                                    <?php } ?>
                                </select>

                            </td>
                        </tr> 


                        <tr><td>&nbsp;</td></tr>
                        <tr><td colspan=2 ><input type=image style='float:right' src='../../images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td></tr>
                    </tbody>
                    </table>
                </form>

                </body>
                </html>
