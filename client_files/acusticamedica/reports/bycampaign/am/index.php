<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>

<?php

$today = date("d/m/o");

$query = "SELECT campaign_id, campaign_name FROM vicidial_campaigns";
$query = mysql_query($query, $link);
for ($i=0;$i<mysql_num_rows($query);$i++)
{
    if ($i == 0) 
    {
    $row = mysql_fetch_assoc($query);
    $camp_options .= "<option selected value=$row[campaign_id]>$row[campaign_name]</option>";
    }
    else
    {
    $row = mysql_fetch_assoc($query);
    $camp_options .= "<option value=$row[campaign_id]>$row[campaign_name]</option>";
    }
}

$query = "SELECT DISTINCT status, status_name FROM vicidial_campaign_statuses ORDER BY status_name;"; 
$query = mysql_query($query, $link) or die(mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
    if ($i==0)
    {
    $row = mysql_fetch_assoc($query);
    $statuses_options .= "<option selected value=$row[status]>$row[status_name]</option>";
    }
    else
    {
    $row = mysql_fetch_assoc($query);
    $statuses_options .= "<option value=$row[status]>$row[status_name]</option>";
    }
 }

$query = "SELECT DISTINCT status, status_name FROM vicidial_statuses WHERE status<>'CALLBK' ORDER BY status_name;"; 
$query = mysql_query($query, $link) or die(mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
    $row = mysql_fetch_assoc($query);
    $statuses_options .= "<option value=$row[status]>$row[status_name]</option>";
}
?>
    
<form name="report" action="csv_builder.php" target="_self" method="post">
    <div class="cc-mstyle">    
        <table>
            <tr>
                <td id='icon32'><img src='../../images/am-icon.jpg' /></td>
                <td id='submenu-title'> Report AM por Campanha </td>
                <td></td>
            </tr>
        </table>
    </div>
    <div id="work-area">
    <br><br>
        
        <div class=cc-mstyle style="border:none;">
            <table border=0>
                <tr>
                    <td></td><td style="text-align:left; padding-left:6px"><b>Data Inicial</b></td><td></td><td style="text-align:left; padding-left:6px"><b>Data Final</b></td>
                </tr>
                <tr>
                    <td style="width:50px" class="right"></td>
                    <td class="left" ><input style="width:168px; text-align:center;" type="text" name='data_inicial' id='data_inicial' value='<?php echo $today; ?>'/></td>
                    <td style="width:100px" class="right"></td>
                    <td class="" style="text-align:left" ><input style="width:168px; text-align:center;" type="text" name='data_final' id='data_final' value='<?php echo $today; ?>' /></td>
                    <td style="width:50px" class="right"></td>
                </tr>        
                <tr style="height:32px"><td></td><td style="text-align:center"><div style="width:212px; border-top:1px dotted gray"></div></td><td></td><td style="text-align:center"><div style="width:212px; border-top:1px dotted grey"></tr>
                <tr><td></td><td style="text-align:left; padding-left:6px"><b>Campanha</b></td><td></td><td style="text-align:left; padding-left:6px"><b>Feedback</b></td></tr>
                <tr><td></td><td style="text-align:left"><select name="camp_options[]" id="camp_options[]" style="height:260px; width:212px;" multiple><option onclick="SelectAll('camp_options[]');">Todas as Campanhas</option><?php echo $camp_options; ?></select></td><td></td><td style="text-align:left"><select name="feed_options[]" id="feed_options[]" style="height:260px; width:212px;" multiple><option onclick="SelectAll('feed_options[]');">Todos os Feedbacks</option><?php echo $statuses_options; ?></select></td></tr>
                <tr style="height:16px"></tr>
                <tr><td colspan="3"></td><td>   <table style="width:212px; float:left" border=0><tr><td style="text-align:right">Exportar para Excel</td><td style="width:32px"><input type="image" height="32px" src="../../images/export_excel_32.png"></td></tr></table>    </td></tr>
            </table>
        </div>
        <br><br>
    </div>
</form>

<style>
.left { text-align:left !important; }
.right { text-align:right !important; }
</style>
<script>
function SelectAll(elemvar) 
{
    var aSelect = document.getElementById(elemvar);        
    var aSelectLen = aSelect.length;
    for(i = 0; i < aSelectLen; i++) {
        aSelect.options[i].selected = true;
    }
    aSelect.options[0].selected = false;
}

$( "#data_inicial" ).datepicker({
    showOn: "button",
    buttonImage: "../../images/calendar_view_day_32.png",
    buttonImageOnly: true
});
$( "#data_final" ).datepicker({
    showOn: "button",
    buttonImage: "../../images/calendar_view_day_32.png",
    buttonImageOnly: true
});
</script>
<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>