<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Go Contact Center - Alterar Password</title>


        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />

        <?php
        require("dbconnect.php");
        $user = $_GET['user'];

        $curPass = mysql_query("SELECT pass from vicidial_users where user = '$user'") or die(mysql_error());

        $curPass = mysql_fetch_assoc($curPass);

        $newpass = $_POST['newpass_one'];

        if (isset($_POST['user'])) {

            $user = $_POST['user'];
            mysql_query("UPDATE vicidial_users set pass = '$newpass' WHERE user = '$user'") or die(mysql_error());
            ?>
            <script type='text/javascript'>
                alert('Password alterada com sucesso!');
                window.close();
            </script>
        <?php } ?>



        <script type="text/javascript">
            function validaform() {
                var curPass = "<?= $curPass['pass']; ?>";
                if (document.getElementById('oldpass').value != curPass) {
                    alert('A password actual está errada');
                    return false;
                } else {

                    if (document.getElementById('newpass_one').value == "") {
                        alert('Preencha a nova password');
                        return false;
                    } else {

                        if (document.getElementById('newpass_one').value != document.getElementById('newpass_two').value) {

                            alert('As passwords novas não correspondem');
                            return false;

                        } else {
                            return true;
                        }
                    }
                }
            }
        </script>


    </head>

    <body>

        <div class="grid-transparent">
            <div class="grid-title">Alterar Password</div>
            <div class="grid-content">
                <form target="_self" action="alterarpassword.php" method="post" onsubmit="return validaform();" class="cc-mstyle">
                    <input type="hidden" name="user" value="<?= $user; ?>" />

                    <table>
                        <tbody>
                            <tr>
                                <td>Insira a sua password actual:</td>
                                <td><input type="password" name='oldpass' id='oldpass' /></td>
                            </tr>
                            <tr>
                                <td>Escolha a sua nova password:</td>
                                <td><input type="password" name='newpass_one' id='newpass_one' /></td>
                            </tr>
                            <tr>
                                <td>Repita a sua nova password:</td>
                                <td><input type="password" name='newpass_two' id='newpass_two' /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" class="btn btn-primary" value='Alterar' /></td>
                                <td><input type="reset" class="btn" value="Cancelar" /></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>

    </form>
</body>
</html>