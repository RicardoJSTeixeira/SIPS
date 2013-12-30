<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>

        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />

        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        
        <style>
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
        .view-button {
            margin-left: 0.5em;
        }
        </style>
        
    </head>

    <body>
        
        
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        
        <?php
        $self = count(explode('/', $_SERVER['PHP_SELF']));
        for ($i = 0; $i < $self - 2; $i++) {
            $path.="../";
        }
        define("ROOT", $path);

        require(ROOT . "ini/dbconnect.php");

        if (isset($_GET["user"])) {
            $curUser = $_GET["user"];
        } elseif (isset($_POST["user"])) {
            $curUser = $_POST["user"];
        }
        $bypass = $curUser;



        $current_admin = $_SERVER['PHP_AUTH_USER'];
             
        //Users INICIO
$query = "select a.user_group,allowed_campaigns,user_level from vicidial_users a inner join `vicidial_user_groups` b on a.user_group=b.user_group where user='$current_admin'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);


$user_level = $row['user_level'];
$allowed_camps_regex = str_replace(" ", "|", trim(rtrim($row['allowed_campaigns'], " -")));

if ($row['user_group'] != "ADMIN") {
        $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


$user_groups = "";
$result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
while ($row1 = mysql_fetch_assoc($result)) {
    $user_groups .= "'$row1[user_group]',";
}
$user_groups = rtrim($user_groups, ","); 

$users_regex = "";
$result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_level") or die(mysql_error());
while ($rugroups = mysql_fetch_assoc($result)) {
    $users_regex .= "$rugroups[user]|";
}
$users_regex = rtrim($users_regex, "|"); 
$users_regex = "AND user REGEXP '^$users_regex$'";
}
//Users FIM
        
        
        if ($query['user_group'] == "ADMIN") {

            $activeUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='Y'") or die(mysql_error());
            $inactiveUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='N'") or die(mysql_error());
        } else {
            
                $activeUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='Y' $users_regex ") or die(mysql_error());
                $inactiveUsers = mysql_query("SELECT user,full_name FROM vicidial_users WHERE active='N' $users_regex") or die(mysql_error());
        }


       
        ?>
        
        
        
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Gravações</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <div class="row-fluid">
                        <div class="span3">
                            <form  id='filtragravacoes' >
                                <fieldset>
                                    <legend>Pesquisa</legend>
                                    <label>User</label>
                                    <select id='user' class="chzn-select">
                                        <option value="--ALL--">Todos</option>
                                        <optgroup label="Activos">
                                            <?php while ($curUser = mysql_fetch_assoc($activeUsers)) { ?>
                                                <option value='<?= $curUser[user] ?>' <?= ($bypass == $curUser['user']) ? "selected" : "" ?> ><?= $curUser['full_name'] ?></option>
                                            <?php } ?>
                                        </optgroup> 
                                        <optgroup label="Inactivos">
                                            <?php while ($curUser = mysql_fetch_assoc($inactiveUsers)) { ?>
                                                <option value='<?= $curUser[user] ?>' <?= ($bypass == $curUser['user']) ? "selected" : "" ?> ><?= $curUser['full_name'] ?></option>
                                            <?php } ?>
                                        </optgroup> 
                                    </select>
                                    <label>Data Inicio</label>
                                    <input type="text" id='datainicio' value='<?= date("Y-m-d", strtotime("-1 day")) ?>' >
                                    <label>Data Fim</label>
                                    <input type="text" id='datafim' value='<?= date('Y-m-d') ?>' >
                                    <label>Nº de Telefone</label>
                                    <div class="input-append">
                                        <input type="text" id='ntlf' /><button class="btn btn-primary" >Go!</button>
                                    </div>
                                        <span class="help-inline">Por questões de performance, esta pesquisa encontra-se limitada a 1000 entradas.</span>      
                                </fieldset>
                            </form>
                        </div>
                        <div class="span9">


                            <table class="table table-mod-2" id="grav">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function dadoscli(valor)
                {
                    var url = "../admin_modify_lead.php?lead_id=" + valor;
                    window.open(url, '_blank');
                }
                
                 $(document).on("click", ".crm", function() {
                            var id = $(this).data("lead_id");
                            dadoscli(id);
                  });
                
            var datainicio = $("#datainicio"), datafim = $("#datafim"), ntlf = $("#ntlf"), user = $("#user"), oTable;
            $(function() {

                $("#datainicio").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy-mm-dd",
                    onClose: function(selectedDate) {
                        $("#datafim").datepicker("option", "minDate", selectedDate);
                    }
                });
                $("#datafim").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy-mm-dd",
                    onClose: function(selectedDate) {
                        $("#datainicio").datepicker("option", "maxDate", selectedDate);
                    }
                });
                $(".chzn-select").chosen({
                    no_results_text: "Não foi encontrado."
                });

                oTable = $('#grav').dataTable({
                    "aaSorting": [[0, 'asc']],
                    "bProcessing": true,
                    "bDestroy": true,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": 'gravacoes_ajax.php',
                    "fnServerParams": function(aoData)
                    {
                        aoData.push(
                                {"name": "datainicio", "value": datainicio.val()},
                        {"name": "datafim", "value": datafim.val()},
                        {"name": "ntlf", "value": ntlf.val()},
                        {"name": "user", "value": "<?=$_SERVER['PHP_AUTH_USER']?>"},
                        {"name": "user_selected", "value": user.val()}
                        );
                    },
                    "aoColumns": [
                        {"sTitle": "Utilizador"},
                        {"sTitle": "Telefone"},
                        {"sTitle": "Hora de Inicio"},
                        {"sTitle": "Duração"}
                    ],
                    "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
                });
            $('#filtragravacoes').submit(function(e) {
                e.preventDefault();
                oTable.fnClearTable(0);
                oTable.fnReloadAjax();
            });
                
                $("#loader").fadeOut("slow");
            });

        </script>
    </body>
</html>
