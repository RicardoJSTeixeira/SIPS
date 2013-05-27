<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {$header.="../";}
define("ROOT", $header);
require(ROOT . "ini/header.php");

require_once('../func/reserve_utils.php');

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

<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='/images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Tipos de Reserva </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='sch_admin.php'"><img src='/images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>

<div id=work-area>
    
    <div id="table_conteiner" style="opacity: 0">
        <table id="schedulers">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cor</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types;";
                $result = mysql_query($query, $link);
                while ($row = mysql_fetch_assoc($result)) {
                    echo"	<tr>
				<td><img src='".ROOT."images/icons/cross_16.png' title='Eliminar' style='cursor:pointer;' onclick=del('$row[id_reservations_types]',this); >$row[display_text]</td>
				<td><div style='background:$row[color]'>&nbsp;</div></td>
                                <td>".(($row[active]==1)?"<img style='cursor:pointer' src='/images/icons/tick_16.png' onclick=change('$row[id_reservations_types]',this,0) />":"<img style='cursor:pointer' src='/images/icons/cross_16.png' onclick=change('$row[id_reservations_types]',this,1) />")."</td>
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
                            Cor
                        </p>
                    </div>
                    <select id="colour_picker">
                        <option value="ffffff">#ffffff</option>
                        <option value="ffccc9">#ffccc9</option>
                        <option value="ffce93">#ffce93</option>
                        <option value="fffc9e">#fffc9e</option>
                        <option value="ffffc7">#ffffc7</option>
                        <option value="9aff99">#9aff99</option>
                        <option value="96fffb">#96fffb</option>
                        <option value="cdffff">#cdffff</option>
                        <option value="cbcefb">#cbcefb</option>
                        <option value="cfcfcf">#cfcfcf</option>
                        <option value="fd6864">#fd6864</option>
                        <option value="fe996b">#fe996b</option>
                        <option value="fffe65">#fffe65</option>
                        <option value="fcff2f">#fcff2f</option>
                        <option value="67fd9a">#67fd9a</option>
                        <option value="38fff8">#38fff8</option>
                        <option value="68fdff">#68fdff</option>
                        <option value="9698ed">#9698ed</option>
                        <option value="c0c0c0">#c0c0c0</option>
                        <option value="fe0000">#fe0000</option>
                        <option value="f8a102">#f8a102</option>
                        <option value="ffcc67">#ffcc67</option>
                        <option value="f8ff00">#f8ff00</option>
                        <option value="34ff34">#34ff34</option>
                        <option value="68cbd0">#68cbd0</option>
                        <option value="34cdf9">#34cdf9</option>
                        <option value="6665cd">#6665cd</option>
                        <option value="9b9b9b">#9b9b9b</option>
                        <option value="cb0000">#cb0000</option>
                        <option value="f56b00">#f56b00</option>
                        <option value="ffcb2f">#ffcb2f</option>
                        <option value="ffc702">#ffc702</option>
                        <option value="32cb00">#32cb00</option>
                        <option value="00d2cb">#00d2cb</option>
                        <option value="3166ff">#3166ff</option>
                        <option value="6434fc">#6434fc</option>
                        <option value="656565">#656565</option>
                        <option value="9a0000">#9a0000</option>
                        <option value="ce6301">#ce6301</option>
                        <option value="cd9934">#cd9934</option>
                        <option value="999903">#999903</option>
                        <option value="009901">#009901</option>
                        <option value="329a9d">#329a9d</option>
                        <option value="3531ff">#3531ff</option>
                        <option value="6200c9">#6200c9</option>
                        <option value="343434">#343434</option>
                        <option value="680100">#680100</option>
                        <option value="963400">#963400</option>
                        <option value="986536" selected="selected">#986536</option>
                        <option value="646809">#646809</option>
                        <option value="036400">#036400</option>
                        <option value="34696d">#34696d</option>
                        <option value="00009b">#00009b</option>
                        <option value="303498">#303498</option>
                        <option value="000000">#000000</option>
                        <option value="330001">#330001</option>
                        <option value="643403">#643403</option>
                        <option value="663234">#663234</option>
                        <option value="343300">#343300</option>
                        <option value="013300">#013300</option>
                        <option value="003532">#003532</option>
                        <option value="010066">#010066</option>
                        <option value="340096">#340096</option>
                    </select>
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
    $(function() {
        $("input:button").button();
    });
    var otable=$('#schedulers').dataTable( {
        "bJQueryUI": true,
        "sDom": '<"top"f>rt<"bottom"p>',
        "sPaginationType": "full_numbers",
        "aoColumns": [
        { "bSortable": true},
        { "bSortable": true, "sType": "string" },
        { "bSortable": true, "sType": "string" },
    ],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    } );
    
    $('#saveForm').click(function() {if (verify()){
            $.post("../ajax/reserv_types_do.php", {
                display_text: $('#display_text').val(),
                color: "#"+$('#colour_picker').val()
            },
            function(data) {
                $("#sch :input:text").val('');
                otable.dataTable().fnAddData(['<img src="/images/icons/cross_16.png" title="Eliminar" style="cursor:pointer;" onclick=del('+ data.id +',this) >'+data.text,"<div style='background:"+data.color+"'>&nbsp;</div>","<img style='cursor:pointer' src='/images/icons/tick_16.png' onclick=change('"+data.id+"',this,0) />"]);					
            },"json").fail(function(){
                showDialog("Sucedeu-se um erro.");})
        }
    });
     function del(nr, r) {
            (confirma("Deseja eliminar a serie?").done(function () {
                var nTr = otable.fnGetPosition($(r).closest("tr").get(0));
                $.post("../ajax/reserv_types_do.php", {
                    nr: nr
                },
                function (data) {
                    if (data.sucess == 1) {
                        otable.fnDeleteRow(nTr);
                    } else if(data.sucess == 2) {
                        showDialog("Não pode eliminar este 'Tipo' pois existem reservas feitas com esta associação.");
                    }else {showDialog("Sucedeu-se um erro.");}
                }, "json").fail(function () {
                    showDialog("Sucedeu-se um erro.");
                })
            }))
        } 
        
        function change(nr,i,f){
            var nTr = i;
                $.post("../ajax/reserv_types_do.php", {
                    id: nr,
                    act: f
                },
                function (data) {
                    if (data.sucess == 1) {
                        $(nTr).parent().html(((f==1)?"<img style='cursor:pointer' src='/images/icons/tick_16.png' onclick=change('"+nr+"',this,0) />":"<img style='cursor:pointer' src='/images/icons/cross_16.png' onclick=change('"+nr+"',this,1) />"));
                    }else {showDialog("Sucedeu-se um erro.");}
                }, "json").fail(function () {
                    showDialog("Sucedeu-se um erro.");
                })
        }
        
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
        function confirma(msg) {
            var def = $.Deferred();

            $('#alertbox').html(msg);
            $("#dialog-confirm").dialog({
                resizable: false,
                height: 160,
                modal: true,
                buttons: {
                    Sim: function () {
                        $(this).dialog("close");
                        def.resolve();
                    },
                    Cancelar: function () {
                        $(this).dialog("close");
                        def.reject();
                    }
                }
            });
            return def.promise();
        }
    function verify(){
        return $("#sch :input:text,textarea,select").removeClass('alert').filter(function() {return !/\S+/.test($(this).val());}).addClass('alert').size() == 0;
    }
	
    $(document).ready(function() {
        $("#table_conteiner").animate({opacity:1});
    });
    
    $('#colour_picker').colourPicker({
        ico:  '/jquery/colourPicker/colourPicker.gif', 
        title:false
});
</script>


<?php
#FOOTER
require(ROOT . "ini/footer.php");
?>
