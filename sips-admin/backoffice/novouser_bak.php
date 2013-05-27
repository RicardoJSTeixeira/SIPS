<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/css/style.css" rel="stylesheet" type="text/css" />


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

        <title>SIPS - Inserção de novo colaborador</title>

        <script type="text/javascript">




            function validateForm()
            {
                var x = document.forms["novocolab"]["user"].value;
                if (x === null || x === '')
                {
                    alert("Username é obrigatório");
                    return false;
                }


                var z = document.forms["novocolab"]["usergroup"].value;
                if (z === null || z === "---")
                {
                    alert("Escolha o grupo de utilizadores");
                    return false;
                }
                var w = document.forms["novocolab"]["password"].value;
                if (w == null || w == "")
                {
                    alert("Password é obrigatório");
                    return false;
                }

            }
            function alpha(e) {
                var k;
                document.all ? k = e.keyCode : k = e.which;
                return ((k > 64 && k < 91) || (k > 96 && k < 123) || k === 8);
            }
        </script>
    </head>



    <body>


        <div class=cc-mstyle>
            <table>
                <tr>
                    <td id='icon32'><img src='/images/icons/user_add.png' /></td>
                    <td id='submenu-title'> Criar Novo Operador </td>
                    <td style='text-align:left'>Formulário que permite a criação de novos Operadores.</td>
                </tr>
            </table>
        </div>
        <div id=work-area>
            <br>
            <br>

            <div class=cc-mstyle style='border:none; width:75%;'>

                <form action="inserecolab.php" name="novocolab" id="novocolab" method="post" target="_self" onsubmit="return validateForm()">
                    <input type="hidden" name="data" value="<?= $data ?>"  />

                    <table>
                        <tr>
                            <td style='width:225px'> 
                                <div class=cc-mstyle style='height:28px; float:left;  '>
                                    <p> Nome </p>
                                </div>
                            </td>
                            <td>
                                <input style='float:left; width:425px;' type="text" name="nome" value=""/>
                            </td>
                        </tr>
                    </table>


                    <table border=0>

                        <tr><td style='width:225px'> <div class=cc-mstyle style='height:28px; float:left;  '>
                                    <p>Username</p> </div></td>
                            <td><input style='float:left; width:175px;' type="text" name="user" id="user" value="" onkeypress="return alpha(event);" /></td>
                        <tr>

                            <td style='width:225px'> <div class=cc-mstyle style='height:28px; float:left;  '>
                                    <p>Password</p></div></td><td><input style='float:left; width:175px;' value="" type="password" name="password"/></td></tr>


                    </table>
                    <br>
                    <br>
                    <table>

                        <tr>
                            <td style='width:225px'> <div class=cc-mstyle style='height:28px; float:left;  '>
                                    <p> Tipo Utilizador </p></div>
                            </td>
                            <td>


                                <select style='width:275px' name='usertype'>
                                    <option value=admin>Administrador</option>
                                    <option value=operador>Operador</option>

                                </select>

                            </td>
                        </tr>
                        <tr>
                            <td style='width:225px'> 
                                <div class=cc-mstyle style='height:28px; float:left;'>
                                    <p> Grupo de Operadores </p>
                                </div>
                                <input style='float:left; width:175px;' type="hidden" name="dentrada" value="<?= $data ?>" >
                            </td>
                            <td>


                                <select style='width:275px' name='usergroup'>
                                    <?php
                                    for ($i = 0; $i < mysql_num_rows($grupos); $i++) {
                                        $a = mysql_fetch_assoc($grupos);
                                        ?>
                                        <option value = '<?= $a['user_group'] ?>' <?= (($a['user_group'] == $b['user_group']) ? "selected" : "") ?> ><?= $a['group_name'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>

                            </td>
                        </tr>

                    </table>
                    <br/><br/>
                    <table>
                        <tr>
                            <td style=text-align:right>Gravar</td>
                            <td><input type=image style='float:left' src='/images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td>
                        </tr>
                    </table>
                </form>
            </div>
    </body>
</html>