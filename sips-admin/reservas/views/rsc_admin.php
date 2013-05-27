<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {$header.="../";}
define("ROOT", $header);
require(ROOT . "ini/header.php");

require_once('../func/reserve_utils.php');
if (isset($_GET['sch'])) {
    $id_scheduler = preg_replace($only_nr, '', $_GET['sch']);
} else {
    exit;
}
?>
<style>
    .alert {
        border: solid 1px red !important;
        box-shadow:0 0 3px 1px rgba(255, 0, 0, 0.5) !important;
    }
    .cc-mstyle > p {
        text-align: center;
    }
    ul {
        margin:0 auto;
    }
    ul, li {
        max-width:800px
    }
    li > .cc-mstyle {
        display: inline-block;
        width:40%;
        margin:2px 0;
    }	
    li > .styled-button {
        display: block;
        margin:10px auto;
    }
    #table_conteiner{
        width:80%;
        margin:20px auto 0;
    }
    #schedulers td:nth-child(3) { text-align: center }

</style>
<?php
$query = "Select `display_text` From sips_sd_schedulers Where id_scheduler=$id_scheduler;";
$result = mysql_query($query, $link);
$row = mysql_fetch_assoc($result);
?>
<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='<?php echo ROOT; ?>images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Criar recurso </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='sch_admin.php'"><img src='<?php echo ROOT; ?>images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>

<div id=work-area>
    <div style="width:400px;margin:20px auto 0;" class="cc-mstyle"><h1 style="text-align: center;">Calendário: <strong><?php echo $row[display_text]; ?><img src="<?php echo ROOT; ?>images/icons/livejournal_16.png" title="Editar" style="cursor:pointer;" onclick="location='sch_edita.php?sch=<?php echo $id_scheduler; ?>'"></strong></h1></div>

    <div id="table_conteiner" style="opacity: 0">
        <table id="schedulers">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cód. Ref.</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT id_resource,b.display_text,b.alias_code,a.days_visible,a.blocks,a.begin_time,a.end_time,b.active FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.active=1 AND a.id_scheduler=$id_scheduler;";
                $result = mysql_query($query, $link);
                while ($row = mysql_fetch_assoc($result)) {
                    echo"	<tr>
				<td><img src='".ROOT."images/icons/livejournal_16.png' title='Editar' style='cursor:pointer;' onclick=location='rsc_edita.php?rsc=$row[id_resource]' ><a href='calendar_container.php?rsc=$row[id_resource]'>$row[display_text]</a></td>
				<td>$row[alias_code]</td>
                                <td>".(($row[active]==1)?" <img src='".ROOT."images/icons/tick_16.png'  >":"<img src='".ROOT."images/icons/cross_16.png' >")."</td>				
                                </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <form id="sch">
        <div id="main" class="cc-mstyle" style='border: none;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Nome
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="" id="display_text"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Codigo de Referência
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="" id="alias_code"/>
                </li>
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Criar">
                </li>
            </ul>
        </div>
    </form>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>
<script>
    var rsc=<?php echo $id_scheduler; ?>;
    $(function() {
        $("input:button").button();
    });
    var otable=$('#schedulers').dataTable( {
        "bJQueryUI": true,
        "sDom": '<"top"f>rt<"bottom"p>',
        "sPaginationType": "full_numbers",
        "aoColumns": [
        { "bSortable": true},
        { "bSortable": true},
        { "bSortable": true, "sType": "string" }
    ],
        "oLanguage": {
            "sUrl": "<?php echo ROOT; ?>jquery/jsdatatable/language/pt-pt.txt"
        }
    } );
    
    $('#saveForm').click(function() {if (verify()){
            $.post("../ajax/rsc_admin_do.php", {
                id : rsc,
                display_text: $('#display_text').val(),
                alias_code: $('#alias_code').val()
            },
            function(data) {
                $("#sch :input:text").val('');
                otable.dataTable().fnAddData(['<img src="<?php echo ROOT;?>images/icons/livejournal_16.png" title="Editar" style="cursor:pointer;" onclick=location="rsc_edita.php?rsc='+ data.id +'" ><a href=calendar_container.php?rsc='+data.id+'>'+data.display_text+'</a>',data.alias_code,'<img src="<?php echo ROOT ?>images/icons/tick_16.png"  >']);					
            },"json").fail(function(){
                showDialog("Sucedeu-se um erro.");})
        }
    });
    function showDialog(msg){
        $('#alertbox').html(msg);
        $('#dialog-confirm').dialog({
            modal : true,
            buttons : {
                Ok : function() {
                    $(this).dialog("close");
                }
            }
        })};
    function verify(){
        return $("#sch :input:text,textarea,select").removeClass('alert').filter(function() {return !/\S+/.test($(this).val());}).addClass('alert').size() == 0;
    }
	
    $(document).ready(function() {
        $("#table_conteiner").animate({opacity:1});
        $(".num").keydown(function(event) {
            if((!event.shiftKey && !event.ctrlKey && !event.altKey) && ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
            } else if(event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39) {
                event.preventDefault();
            }
        });
    });
	
</script>


<?php
#FOOTER
require(ROOT . "ini/footer.php");
?>
