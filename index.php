<!DOCTYPE html> 
<html>
    <head>
        <title>  
            Go Contact Center | Home Page
        </title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <script type="text/javascript" src="/jquery/jquery-1.8.3.js">
        </script>
    </head>
    <body>
        <?php
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        ini_set('display_errors', '1');
        require("ini/dbconnect.php");
        require("ini/user.php");

        $user = new user;


        if (isset($_GET['logout'])) {
            Header("WWW-Authenticate: Basic realm=\"Go Contact Center\"");
            Header("HTTP/1.0 401 Unauthorized");
            ?>
            <div class='grid-content'>
                <div class='alert alert-info'>Logout com sucesso <i>coloque as novas credencias ou prima <ins>cancelar</ins></i></div>
            </div>
            <script>
                $(function() {
                    top.location = 'index.php';
                })
            </script>
            <?php
            unset($_GET['logout']);
            exit;
        } else {

            if (!$user->id) {
                header("WWW-Authenticate: Basic realm=\"Go Contact Center\"");
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }

        $queryClient = "SELECT server_description from servers limit 1";
        $queryClient = mysql_query($queryClient, $link) or die(mysql_error());
        $curClient = mysql_fetch_row($queryClient);
        unset($curLogo);
        if (file_exists("client_files/$curClient[0]/logo.gif")) {
            $curLogo = "client_files/$curClient[0]/logo.gif";
        }


        if (isset($_POST['first_login'])) {

            $username = $_POST['sips_username'];
            $password = $_POST['sips_password'];
            $user_loggin = new user($username, $password);


            if (!$user_loggin->id) {
                $navigation = 'fail';
            } elseif ($user_loggin->user_level > 5) {
                ?>
                <form id='adminlogin' action='sips-admin/index.php' method='post'>
                    <input type='hidden' name='useradmin' value='<?= $username ?>' />
                    <input type='hidden' name='passadmin' value='<?= $password ?>' />
                    <?
                    if (isset($curLogo) && $curLogo != "") {
                        echo "<input type=hidden name=curlogo value=$curLogo />";
                    }
                    ?>
                </form>

                <script>
                    document.getElementById("adminlogin").submit();
                </script>

            <?php } else { ?>
                <form id='agentlogin' action='sips-agente/agente.php' method='post'>
                    <input type='hidden' name='sips_login' value='<?= $username ?>' />
                    <input type='hidden' name='sips_pass' value='<?= $password ?>' />
                    <?
                    if (isset($curLogo) && $curLogo != "") {
                        echo "<input type=hidden name=curlogo value=$curLogo />";
                    }
                    ?>
                </form>

                <script>
                    document.getElementById("agentlogin").submit();
                </script>
                <?php
            }
        }
        if (isset($_POST['reset_login'])) {
            $navigation = '';
        }

        if ($navigation == '') {
            ?>
            <div style="width: 525px;margin: auto;">
                <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />

                <div class="grid">
                    <div class="grid-content">

                        <form name='sips_login' id=sips_login action='index.php' class="form-horizontal" method=POST>
                            <?php
                            if (isset($curLogo) && $curLogo != "") {
                                echo "<input type=hidden name=curlogo value=$curLogo />";
                            }
                            ?>
                            <input type=hidden value=go name=first_login>
                            <div class="control-group">
                                <label class="control-label">Username: </label>
                                <div class="controls">
                                    <input type='text' name='sips_username' id='sips_username' value=''>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Password: </label>
                                <div class="controls">
                                    <input type='password' name='sips_password' id='sips_password' value=''>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <button class="btn btn-primary"><i class="icon-signin"></i> Log-In </button>
                                </div>
                            </div>
                            <div class="clear"></div>


                        </form>
                    </div>    
                </div>
                <?php
                if (isset($curLogo) && $curLogo != "") {
                    echo "<br><br><img src=$curLogo />";
                }
                ?>
            </div>
            <?php
        }

        if ($navigation == 'fail') {
            ?>
            <div style="width: 490px;margin: auto;">
                <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />

                <div class="grid">
                    <div class="grid-content">

                        <form name=sips_login id=sips_login action=index.php method=POST>
                            <?
                            if (isset($curLogo) && $curLogo != "") {
                                echo "<input type=hidden name=curlogo value=$curLogo />";
                            }
                            ?>
                            <input type=hidden value=go name=reset_login>
                            <div class="formRow">
                                <div class='alert'><b>Log In Errado </b><a href='index.php' class='btn'>Tentar Novamente</a></div>
                            </div>
                            <div class="clear"></div>

                        </form>
                    </div>
                </div>
                <?php
                if (isset($curLogo) && $curLogo != "") {
                    echo "<br><br><img style='width:600px;heigth:200px' src=$curLogo />";
                }
                ?>
            </div>
        <?php } ?>




        <script>
            document.getElementById("sips_username").value = '<?= $user->id ?>';
            document.getElementById("sips_password").value = '<?= $user->password ?>';
        </script>

    </body>
</html>

