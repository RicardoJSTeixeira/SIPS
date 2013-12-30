<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
require(ROOT . "ini/user.php");

$user = new user;
$where = "";
if ($user->user_group != "ADMIN") {
    $where = " WHERE b.campaign_id in('" . implode("','", $user->allowed_campaigns) . "')";
}

$today = date("Y-m-d");
##################################################
$query = "	SELECT 	list_id, list_name 
			FROM 	vicidial_lists a left join vicidial_campaigns b on a.campaign_id=b.campaign_id
			$where;";
$query = mysql_query($query, $link)or die(mysql_error());


while ($row = mysql_fetch_assoc($query)) {
    $list_options .= "<option value=$row[list_id]>$row[list_name]</option>";
}
##################################################
?>
</head>
<body>
    <form name="report" action="exportcsv_fc.php" target="_self" method="post">
        <input type="hidden" value='go' name="totais_db2">
        <div class="cc-mstyle">	
            <table>
                <tr>
                    <td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
                    <td id='submenu-title'> Total de Feedbacks por Base de Dados</td>
                    <td style="text-align:right">Obter Report</td>
                    <td id='icon32'><input type="image" src='/images/icons/document_export_32.png'/></td>
                </tr>
            </table>
        </div>


        <div id="work-area" style="min-height:0px"><br><br>
            <div class=cc-mstyle style=border:none>
                <table>
                    <tr>
                        <td>Dia Inicial:</td>
                        <td><input style="width:200px; text-align:center;" type="text" name='data_inicial' id='data_inicial' value='<?php echo $today; ?>' /><td>

                        </td>
                    </tr>

                    <tr>
                        <td>Dia Final:</td>
                        <td><input style="width:200px; text-align:center;" type="text" name='data_final' id='data_final' value='<?php echo $today; ?>' /><td>
                            <script language="JavaScript">
                                $(function() {
                                    $("#data_inicial").datepicker({
                                        changeMonth: true,
                                        changeYear: true,
                                        dateFormat: "yy-mm-dd",
                                        onClose: function(selectedDate) {
                                            $("#data_final").datepicker("option", "minDate", selectedDate);
                                        }
                                    });
                                    $("#data_final").datepicker({
                                        changeMonth: true,
                                        changeYear: true,
                                        dateFormat: "yy-mm-dd",
                                        onClose: function(selectedDate) {
                                            $("#data_inicial").datepicker("option", "maxDate", selectedDate);
                                        }
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                    <tr> 
                        <td>Base de Dados:</td><td><select name="db_options" id="db_options" style="width:200px;"><?php echo $list_options; ?></select></td>	
                    </tr>
                    <tr>
                        <td>Opções do Report:</td><td><select name="flag" id="flag" style="width:315px;"><option value="todos">Todos os Feedbacks em Sistema</option><option value="encontrados">Apenas os Feedbacks Encontrados na Pesquisa</option></select></td>	
                    </tr>
                </table>
                <br><br>
            </div>

        </div>

    <br><br>
</form>


</body>
</html>