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
    $where = " WHERE campaign_id in('" . implode("','", $user->allowed_campaigns) . "')";
}

$today = date("Y-m-d");
$query = "	SELECT	campaign_name, campaign_id 
			FROM 	vicidial_campaigns $where;";
$query=  mysql_query($query)or die(mysql_error());

while ($row = mysql_fetch_assoc($query)) {
    $camp_options .= "<option value=$row[campaign_id]>$row[campaign_name]</option>";
}
?>
</head>
<body>
    <form name="totais_db" action="exportcsv_fc.php" target="_self" method="post">
        <input type="hidden" value="go" name="totais_camp">
        <div class="cc-mstyle">	
            <table>
                <tr>
                    <td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
                    <td id='submenu-title'> Total de Feedbacks por Campanha </td>
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
                        <td>Campanha:</td><td><select name="camp_options" id="camp_options" style="width:202px;"><?php echo $camp_options; ?></select></td>	
                    </tr>
                    <tr>
                        <td>Opções do Report:</td><td><select name="flag" id="flag" style="width:315px;"><option value="todos">Todos os Feedbacks em Sistema</option><option value="encontrados">Apenas os Feedbacks Encontrados na Pesquisa</option></select></td>	
                    </tr>
                </table> 
            </div>
        </div>
    </form>	
    <br><br>
</body>
</html>