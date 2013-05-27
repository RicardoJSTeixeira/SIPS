<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");

require_once ('../func/reserve_utils.php');

if (isset($_GET[user])) {
    $id_user = $_GET[user];
} else {
    $id_user = "admin";
}

if (isset($_GET[lead])) {
    $id_lead = $_GET[lead];
} else {
    $id_lead = 0;
}

if (isset($_GET[sch])) {
    $id_scheduler = preg_replace($only_nr, '', $_GET[sch]);
} elseif (isset($_GET[rsc])) {
    $id_resource = preg_replace($only_nr, '', $_GET[rsc]);
} elseif (isset($_GET[cp])) {
    $cp = substr(preg_replace($only_nr, '', $_GET[cp]), 0, 4);
} elseif (isset($_GET[ref])) {
    $ref = $_GET['ref'];
} else {
    exit;
}
$slot2change = (isset($_GET[muda]) ? $_GET[muda] : 0);

if (checkDateTime($_GET[dt], "Y-m-d")) {
    $date = (date('D') == 'Mon') ? date("Y-m-d", strtotime($_GET[dt] . " this monday")) : date("Y-m-d", strtotime($_GET[dt] . " last monday"));
    $comeca = (date('D') == 'Mon') ? date("d-m-Y", strtotime($_GET[dt] . " this monday")) : date("d-m-Y", strtotime($_GET[dt] . " last monday"));
} else {
    $date = (date('D') == 'Mon') ? date("Y-m-d") : date("Y-m-d", strtotime('last monday'));
    $comeca = (date('D') == 'Mon') ? date("d-m-Y") : date("d-m-Y", strtotime('last monday'));
}
$acaba = date("d-m-Y", strtotime($comeca . ' next sunday'));
$begin_sys = $date;
$end_sys = date("Y-m-d", strtotime($comeca . ' next sunday +1 day'));
?>
<style>

    label { display: inline-block !important }
    #radio {
        float: right;
        margin-bottom: 15px;
        margin-top: -35px;
    }
    #nav {
        margin: 15px auto 15px;
        width: 255px;
    }
    #nav > span { 
        vertical-align: middle;
        height: 20px; }
    #table_conteiner {
        clear: both;
        font-size: 10pt;
        font-weight: 400;
        margin: 0 auto;
    }
    #loader{
        top: 0;
        left: 0;
        position: fixed;
        width: 100%;
        height: 100%;
        background: white;
        opacity: 0;
        z-index:10;
    }
    .reservas {
        border: 1px solid #0063DC;
        border-collapse: collapse;
        font-size: 8pt;
        table-layout: fixed;
        width: 100%;
    }
    .reservas td {border: 1px solid #0063DC;}
    .dia {
        background: #EFEFEF;
        color: #0063DC;
        padding: 1px 3px;
        width: 100px !important;
    }
    .hoje{background: #C0C0C0}
    .block {
        background: #F9F9F9;
        padding: 1px 3px;
    }
    .block span { }
    .nome {
        background: #F9F9F9;
        height: 35px;
        padding: 1px 3px;
    }
    .nome > span { color: #FF0083 }
    .slot:not(.past):not(.deles){cursor: pointer}
    .hilite { background: #FFFEC9 }
    .reservavel:hover { background: #0063DC }
    .past {background: #CCCCCC}
    .muda{background: #cae387 !important;}
    .disponivel{
        background: #CCFFCC;
    }
    .imported{background:#FF0000 !important}
    .disponivel:hover {background: #0073ea;}
    .bloqueado{background-image: url("<?php echo ROOT; ?>images/icons/stripes.png");}
    .limbo{background: black;}
    #crm{background: white;
         border-radius: 4px 4px 4px 4px;
         box-shadow: 0 100px 1000px 10px black;
         display: none;
         height: 90%;
         left: 5%;
         overflow-y: scroll;
         padding: 5px 0;
         position: fixed;
         top: 20px;
         width: 90%;    
         z-index:11;}
    .d{color:#ffffff;font-weight:bold;}
</style>
<style>
    #dialog-form label, #dialog-form input { display:block; }
    #dialog-form input.text { margin-bottom:12px; width:95%; padding: .4em; }
    #dialog-form fieldset { padding:0; border:0; margin-top:25px; }
    #dialog-form h1 { font-size: 1.2em; margin: .6em 0; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>
<style>
<?php
$query = "SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types;";
$result = mysql_query($query, $link);
for ($index = 0; $index < mysql_num_rows($result); $index++) {
    $row = mysql_fetch_assoc($result);
    $r_types[$index] = $row;
    echo ".t" . $r_types[$index][id_reservations_types] . " {background: " . $r_types[$index][color] . ";}\n";
}
?>
</style>
<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='/images/icons/calendar_32.png' /></td>
            <td id='submenu-title'> Calendário </td>
            <td style='text-align:right'></td>
        </tr>
    </table>
</div>

<div id=work-area>

    <?php
    //start struct
    if (isset($_GET[sch])) {
        $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource, b.restrict_days FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.id_scheduler=$id_scheduler AND b.active=1 AND a.active=1;";
    } elseif (isset($_GET[rsc])) {
        $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.id_resource=$id_resource AND b.active=1 AND a.active=1;";
    } elseif (isset($_GET[cp])) {
        $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler INNER JOIN sips_sd_cp c ON a.alias_code=c.tecnico WHERE c.cp='$cp' AND b.active=1 AND a.active=1";
    } elseif (isset($_GET[ref])) {
        $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE a.alias_code='" . mysql_real_escape_string($ref) . "' AND b.active=1 AND a.active=1;";
    }


    $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 1");
    for ($i = 0; $i < mysql_num_rows($result); $i++) {
        $resources[$i] = mysql_fetch_assoc($result);
    }

    if (isset($_GET[cp]) OR isset($_GET[ref])) {
        $id_scheduler = $resources[0][id_scheduler];
    }
    //end struct
    //Reservas
    if (isset($_GET[sch]) OR isset($_GET[cp]) OR isset($_GET[ref])) {
        $query = "SELECT id_reservation, start_date, end_date, a.id_resource,id_user,lead_id,id_reservation_type,b.display_text FROM sips_sd_reservations a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource  INNER JOIN sips_sd_reservations_types c ON a.id_reservation_type=c.id_reservations_types WHERE b.id_scheduler=$id_scheduler And start_date <='$end_sys' And start_date >='$begin_sys';";
    } elseif (isset($_GET[rsc])) {
        $query = "SELECT id_reservation, start_date, end_date, id_resource,id_user,lead_id,id_reservation_type,display_text FROM sips_sd_reservations a  INNER JOIN sips_sd_reservations_types b ON a.id_reservation_type=b.id_reservations_types WHERE id_resource=$id_resource And start_date <='$end_sys' And start_date >='$begin_sys'";
    }

    $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 2");
    for ($i = 0; $i < mysql_num_rows($result); $i++) {
        $reservas[$i] = mysql_fetch_assoc($result);
    }
    //End Reservas
    //Imported Reservas
    if (isset($_GET[sch]) OR isset($_GET[cp]) OR isset($_GET[ref])) {
        $query = "SELECT id_reservation_acu, start_date, a.id_resource,id_reservation_type,c.display_text FROM sips_sd_reservations_acu a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource INNER JOIN sips_sd_reservations_types c ON a.id_reservation_type=c.id_reservations_types WHERE b.id_scheduler=$id_scheduler And start_date <='$end_sys' And start_date >='$begin_sys';";
    } elseif (isset($_GET[rsc])) {
        $query = "SELECT id_reservation_acu, start_date, id_resource,id_reservation_type,display_text FROM sips_sd_reservations_acu a  INNER JOIN sips_sd_reservations_types b ON a.id_reservation_type=b.id_reservations_types WHERE id_resource=$id_resource And start_date <='$end_sys' And start_date >='$begin_sys'";
    }


    $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 2.1");
    for ($i = 0; $i < mysql_num_rows($result); $i++) {
        $i_reservas[$i] = mysql_fetch_assoc($result);
    }
    //End that shit
    //Series
    if (isset($_GET[sch]) OR isset($_GET[cp]) OR isset($_GET[ref])) {
        $query = "Select a.id_resource,a.start_time,a.end_time,a.day_of_week_start,a.day_of_week_end From sips_sd_series a INNER JOIN sips_sd_resources b Where b.id_scheduler=$id_scheduler";
    } elseif (isset($_GET[rsc])) {
        $query = "Select id_resource,start_time,end_time,day_of_week_start,day_of_week_end From sips_sd_series Where id_resource=$id_resource";
    }

    $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 3");
    for ($i = 0; $i < mysql_num_rows($result); $i++) {
        $series[$i] = mysql_fetch_assoc($result);
    }
    //End Series
    //Execoes
    if (isset($_GET[sch]) OR isset($_GET[cp]) OR isset($_GET[ref])) {
        $query = "SELECT a.id_execao,a.id_resource, a.start_date, a.end_date FROM sips_sd_execoes a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource WHERE b.id_scheduler=$id_scheduler";
    } elseif (isset($_GET[rsc])) {
        $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=$id_resource";
    }

    $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 4");
    for ($i = 0; $i < mysql_num_rows($result); $i++) {
        $execoes[$i] = mysql_fetch_assoc($result);
    }
    //End execoes
    ?>
    <div id="nav">
        <span id="anterior"></span>
        <span>
<?php
echo $comeca . " - " . $acaba;
?>
        </span>
        <span id="seguinte"></span>
    </div>
    <div id="radio" style="visibility: hidden;">
        <input type="radio" id="radio1" name="radio" />
        <label for="radio1">Agenda</label>
        <input type="radio" id="radio2" name="radio" checked="checked" />
        <label for="radio2">Semanal</label>
        <input type="radio" id="radio3" name="radio" />
        <label for="radio3">Mensal</label>
    </div>
    <div id="table_conteiner" class="shad">
<?php
for ($ii = 0; $ii < $resources[0][days_visible]; $ii++) {
    $display_date = date("d/m/Y", strtotime($date));
    $tab = "";
    $tab[0] = "<table class='reservas'>\n\t<tbody>\n";
    for ($i = $resources[0][begin_time]; $i <= $resources[0][end_time]; $i += $resources[0][blocks]) {
        $tab[0] .= ($i == $resources[0][begin_time]) ? "\t<tr>\n\t\t<td class='dia" . ((date("Y-m-d", strtotime($date)) == date("Y-m-d", strtotime("today"))) ? " hoje" : "") . "'>" . days2dias(strtotime($date)) . ", $display_date</td>\n" : "";
        //day in first column
        $tab[0] .= "\t\t<td class='block'><span>" . m2h($i, true) . "</span></td>\n";
        //blocks time
        $tab[0] .= (($i + $resources[0][blocks]) > $resources[0][end_time]) ? "</tr>\n" : "";
        //end of the row

        for ($iii = 0; $iii < count($resources); $iii++) {
            $tab[$iii + 1] .= ($i == $resources[0][begin_time]) ? "\t<tr class='slots'>\n\t\t<td class='nome'><span>" . $resources[$iii][display_text] . "</span></td>\n" : "";
            //title first column 
            $beg = date("Y-m-d H:i:s", strtotime($date . "+$i minutes"));
            $end = date("Y-m-d H:i:s", strtotime($date . "+" . ($i + $resources[0][blocks] - 1) . " minutes"));
            $dados = set_estado($beg, $end, $resources[$iii][id_resource], $reservas, $series, $resources[$iii][restrict_days], $execoes, $date, $i, $id_user, $slot2change, $i_reservas);
            $title = (($dados[type] != "") ? " title='$dados[type]'" : "");
            $tab[$iii + 1] .= "\t\t<td class='slot" . $dados[stat] . "'$title >
                                                <input type=hidden class='beg' value='" . $beg . "'/>
                                                <input type=hidden class='end' value='" . $end . "'/>
                                                <input type=hidden class='rsc' value='" . $resources[$iii][id_resource] . "'/>
                                                <input type=hidden class='lead' value='" . $dados[id] . "'/>
                                                    " . ((eregi("imported", $dados[stat]) AND (eregi("deles", $dados[stat]) or eregi("reservardo", $dados[stat]))) ? "<span class='d'>Duplicado</span>" : "") . "
                                           </td>\n";
            //time slots
            $tab[$iii + 1] .= (($i + $resources[0][blocks]) > $resources[0][end_time]) ? "</tr>\n" : "";
            //end of the row
        }
    }

    for ($i = 0; $i < count($tab); $i++) {
        echo $tab[$i];
    }
    echo "\t</tbody>\n</table>\n";

    $date = date("Y-m-d", strtotime($date . " +1 day"));
}
?>
    </div>
</div>
<!-- block div -->
<div id="loader" style="display: none;"></div>
<!-- dialog form -->
<div id="dialog-confirm" title="Confirmação"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>
<!-- crm -->
<!-- reservation tipy form -->
<div id="dialog-form" title="Tipo de Reserva">
    <p class="validateTips">Escolha o tipo de reserva.</p>

    <form>
        <fieldset>
            <label for="rtype">Tipo</label>
            <select id="rtype" >
<?php
for ($index1 = 0; $index1 < count($r_types); $index1++) {
    if ($r_types[$index1][active] == 1) {
        echo "<option value='" . $r_types[$index1][id_reservations_types] . "'>" . $r_types[$index1][display_text] . "</option>";
    }
}
?>
            </select>
        </fieldset>
    </form>
</div>
<div id="crm"></div>

<script>
    var lead='<?php echo $id_lead; ?>';
    var user='<?php echo $id_user; ?>';
    var slot2change = <?php echo (isset($_GET[muda]) ? $_GET[muda] : 0) ?>;
    var bloco_res;
    $(document).ready(function () {
        //Start Design
        $("#anterior").button({
            icons: {
                primary: "ui-icon-circle-triangle-w"
            },
            text: false
        });
        $("#seguinte").button({
            icons: {
                primary: "ui-icon-circle-triangle-e"
            },
            text: false
        });

        $(function () {
            $("#radio").buttonset();
        });

        $('.slot').hover(function () {
            $(this).siblings('.nome').toggleClass('hilite');
        });
        
        //End Design

        //Start nav
        $("#anterior").click(function () {
            ChangeDate('<?php echo last_week($date); ?>');
        });

        $("#seguinte").click(function () {
            ChangeDate('<?php echo next_week($date); ?>');
        });

        function ChangeDate(data) {
            RedirectToSelf("dt", /dt=\d{4}-\d{2}-\d{2}/i, "dt=" + data,slot2change);
        }

        function RedirectToSelf(queryStringParam, regexMatch, substitution,extra) {
            var val= new RegExp('(\\?|\\&)muda=.*?(?=(&|$))')
            var url = location.href.replace(val,'');
            var newUrl = url;
            if (url.indexOf(queryStringParam + "=") != -1) {
                newUrl = url.replace(regexMatch, substitution);
            } else if (url.indexOf("?") != -1) {
                newUrl = url + "&" + substitution;
            } else {
                newUrl = url + "?" + substitution;
            }
            newUrl = newUrl.replace("#", "");
            var mudar ="";
            if(extra!=0){
                mudar="&muda="+extra;
            }
            location = newUrl+mudar;
        }
        //End Nav 
       
        //Start reservation
        function create(b) {
<?php if (isset($_GET[cp]) OR isset($_GET[ref])) { ?>
                         if(opener.marcado){
                             return false;
                         }
<?php } ?>
                     if (slot2change != 0) {
                         return false;
                     }
                     var bloco = b;
                     $("#loader").show();
                     var rtype=$("#rtype option:selected");
                     $.post('../ajax/reservas.php', {
                         start: $('.beg', bloco).val(),
                         end: $('.end', bloco).val(),
                         resource: $('.rsc', bloco).val(),
                         user: user,
                         lead: lead,
                         rtype: rtype.val()
                     },
                     function (data) {
                         if (data.sucess == "1") {
                             $(bloco).removeClass("reservavel").addClass("reservado").addClass("t"+rtype.val()).attr("title", rtype.text());
<?php if (isset($_GET[cp]) OR isset($_GET[ref])) { ?>
                                            $("#marcdata",opener.document).val($('.beg', bloco).val().substr(0,10));
                                            $("#HOUR_marchora",opener.document).val($('.beg', bloco).val().substr(11,2));
                                            $("#MINUTE_marchora",opener.document).val($('.beg', bloco).val().substr(14,2));
                                            opener.marcado=true;
                                            close();
<?php } ?>
                                        $("#loader").hide();
                                    } else if (data.sucess == "0") { 
                                        $("#loader").hide();
                                        confirma("Esta slot já se encontra reservada.\n Deseja fazer um reload?").done(function () {
                                            location.reload();
                                        }).fail(function () {
                                            $(bloco).removeClass("reservavel").addClass("reservado").addClass("limbo");
                                            $("#loader").hide();
                                        })
                                    }
                                }, "json").fail(function () {
                                    $("#loader").hide();
                                    alert('Erro de conecção ao servidor.');
                                })
                            };
        
                            var b;
        
                            $('.reservavel').live("click", function(){
                                b = this;
                  
                                $("#dialog-form").dialog("open");
                            });
            
                            $( "#dialog-form" ).dialog({
                                autoOpen: false,
                                height: 200,
                                width: 200,
                                modal: true,
                                buttons: {
                                    "Ok": function() {
                                        create(b); 
                                        $( this ).dialog( "close" );
                        
                                    },
                                    Cancel: function() {
                                        $( this ).dialog( "close" );
                                    }
                                },
                                close: function() {
                                }
                            });
                            //reservation tipe
         
        
                            //Change reservation first step 
        
                            var cl4ss;
                            $('.reservado:not(.past,.deles)').live("click", function () {
                                bloco_res = this;
                                if (slot2change != 0) {
                                    return false;
                                }
                                confirma_alt("Deseja mudar a marcação?").done(function () {
                                    $("#loader").show();
                                    $.post('../ajax/altera_reservas.php', {
                                        pedido: 1,
                                        start: $('.beg', bloco_res).val(),
                                        end: $('.end', bloco_res).val(),
                                        resource: $('.rsc', bloco_res).val()
                                    },
                                    function (data) {
                                        if (data.sucess == "1") {
                                            cl4ss=$(bloco_res).attr("class");
                                            $('.slot').removeClass("reservavel").not('.reservado').not('.bloqueado').not('.past').addClass("disponivel");
                                            $(bloco_res).removeClass("reservado").addClass("muda");
                                            slot2change = data.id;
                                            $("#loader").hide();
                                        };
                                    }, "json").fail(function () {
                                        alert('Erro de conecção ao servidor.');
                                        $("#loader").hide();
                                    })
                                })
                            });
                            //Change reservation final step
                            $('.disponivel').live("click", function () {
                                var bloco = this;
                                $("#loader").show();
                                $.post('../ajax/altera_reservas.php', {
                                    pedido: 2,
                                    start: $('.beg', bloco).val(),
                                    end: $('.end', bloco).val(),
                                    resource: $('.rsc', bloco).val(),
                                    id: slot2change
                                },
                                function (data) {
                                    if (data.sucess == "1") {
                                        var muda=$('.muda');
                                        var title=muda.attr("title");
                                        $(bloco).addClass(cl4ss).attr("title",title);
                                        muda.removeClass(function (index, css) {return (css.match (/\bt\S+/g) || []).join(' ');}).removeAttr("title").removeClass("muda");
                                        $('.slot').removeClass("disponivel").not('.reservado').not('.bloqueado').addClass("reservavel");
<?php if (isset($_GET[cp]) OR isset($_GET[ref])) { ?>
                                            $("#marcdata",opener.document).val($('.beg', bloco).val().substr(0,10));
                                            $("#HOUR_marchora",opener.document).val($('.beg', bloco).val().substr(11,2));
                                            $("#MINUTE_marchora",opener.document).val($('.beg', bloco).val().substr(14,2));
                                            close();
<?php } ?>
                                        $("#loader").hide();
                                        slot2change = 0;
                                    } else if (data.sucess == "0") {
                                        $("#loader").hide();
                                        confirma("Esta slot já se encontra reservada.\n Deseja fazer um reload?").done(function () {
                                            location.reload();
                                        }).fail(function () {
                                            $(bloco).removeClass("disponivel").addClass("reservado").addClass("limbo");
                                            $('.muda').removeClass("muda").addClass("reservado");
                                            $('.disponivel').removeClass("disponivel").addClass("reservavel");
                                            slot2change = 0;
                                            $("#loader").hide();
                                        })
                                    }
                                }, "json").fail(function () {
                                    alert('Erro de conecção ao servidor.')
                                    $("#loader").hide();
                                })
                            })
                            //Cancel change
                            $('.muda').live("click", function () {
                                $('.muda').removeClass("muda").addClass("reservado");
                                $('.disponivel').removeClass("disponivel").addClass("reservavel");
                                slot2change = 0;
                            })
                            //Delete
                            function elimina() {
                                $("#loader").show();
                                $.post('../ajax/apaga_reservas.php', {
                                    start: $('.beg', bloco_res).val(),
                                    end: $('.end', bloco_res).val(),
                                    resource: $('.rsc', bloco_res).val()
                                },
                                function (data) {
                                    if (data.sucess == "1") {
                                        $(bloco_res).removeClass("reservado").removeClass(function (index, css) {return (css.match (/\bt\S+/g) || []).join(' ');}).removeAttr("title").addClass("reservavel");
<?php if (isset($_GET[cp]) OR isset($_GET[ref])) { ?>
                                            opener.marcado=false;
<?php } ?>
                                        $("#loader").hide();
                                    } else if (data.sucess == "0") {
                                        $("#loader").hide();
                                        alert("Ocorreu-se um erro não identificado...");
                                    }
                                }, "json").fail(function () {
                                    alert('Erro de conecção ao servidor.')
                                    $("#loader").hide();
                                })
                            }
                            //End reservation 
        
                            //START CRM
                            function crm() {
                                if ($('.lead', bloco_res).val()==0) {
                                    showDialog("Esta reserva não tem lead atribuida...");
                                    return false;
                                }
                                $("#loader").show();
                                $.post('../../../sips-agente/crm-agent/crm_edit.php', {
                                    lead_id: $('.lead', bloco_res).val()
                                },
                                function (data) {
                                    $("#crm").html(data)
                                    .fadeIn();
                
                                }, "html").fail(function () {
                                    alert('Erro de conecção ao servidor.')
                                    $("#loader").hide();
                                })
                            }
                            //END CRM
        
                            $(".reservado.past, .deles").click(function(){
                                bloco_res=this;
                                confirma("Deseja ver a informação desta reserva?").done(function(){crm();})
                            })
        
                            function showDialog(msg) {
                                $('#alertbox').html(msg);
                                $('#dialog-confirm').dialog({
                                    modal: true,
                                    buttons: {
                                        Ok: function () {
                                            $(this).dialog("close");
                                        }
                                    }
                                })
                            };

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
                            function confirma_alt(msg) {
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
                                        },
                                        Eliminar: function () {
                                            $(this).dialog("close");
                                            elimina();
                                            def.reject();
                                        },
                                        Info: function () {
                                            $(this).dialog("close");
                                            crm();
                                            def.reject();
                                        }
                                    }
                                });
                                return def.promise();
                            }

                        });
    
                        function CloseCRMEdit()
                        {
                            $("#loader").hide();
                            $("#crm").fadeOut();
                            $("#crm").html();
                        }
</script>
<?php
#FOOTER
require (ROOT . "ini/footer.php");
?>