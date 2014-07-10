<!DOCTYPE html>
<html> 
    <head> 
        <meta charset="utf-8"> 
        <title>Calendário</title>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen-1.min.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/animate.min.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/datetimepicker.css" />


        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/moment.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/moment.langs.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.0.custom.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/bootstrap/js/datetimepicker/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/datetimepicker/locales/bootstrap-datetimepicker.pt.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen-1.jquery.min.js"></script>

        <?php
        require ('../func/reserve_utils.php');
        $users = new user;
        if (isset($_GET["user"])) {
            $id_user = $_GET["user"];
        } else {
            if (!$users->id) {
                header("WWW-Authenticate: Basic realm=\"Go Contact Center\"");
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
            $id_user = $users->id;
        }

        if (isset($_GET["lead"])) {
            $id_lead = $_GET["lead"];
        } else {
            $id_lead = 0;
        }

        if (isset($_GET["id_elemento"])) {
            $id_elemento = $_GET["id_elemento"];
        } else {
            $id_elemento = 0;
        }

        if (isset($_GET["sch"])) {
            $id_scheduler = preg_replace($only_nr, '', $_GET["sch"]);
        } elseif (isset($_GET["rsc"])) {
            $id_resource = preg_replace($only_nr, '', $_GET["rsc"]);
        } elseif (isset($_GET["cp"])) {
            $cp = substr(preg_replace($only_nr, '', $_GET["cp"]), 0, 4);
        } elseif (isset($_GET["ref"])) {
            $ref = $_GET['ref'];
        } else {
            exit;
        }
        $slot2change = (isset($_GET["muda"]) ? $_GET["muda"] : 0);
        $dt = (isset($_GET["dt"]) ? $_GET["dt"] : "");
        if ($users->user_level > 5) {
            if (checkDateTime($dt, "Y-m-d")) {
                $date = (date('D') == 'Mon') ? date("Y-m-d", strtotime($_GET["dt"] . " this monday")) : date("Y-m-d", strtotime($_GET["dt"] . " last monday"));
                $comeca = (date('D') == 'Mon') ? date("d-m-Y", strtotime($_GET["dt"] . " this monday")) : date("d-m-Y", strtotime($_GET["dt"] . " last monday"));
            } else {
                $date = (date('D') == 'Mon') ? date("Y-m-d") : date("Y-m-d", strtotime('last monday'));
                $comeca = (date('D') == 'Mon') ? date("d-m-Y") : date("d-m-Y", strtotime('last monday'));
            }
        } else {


            if (checkDateTime($dt, "Y-m-d")) {
                $date = date("Y-m-d", strtotime($_GET["dt"] . ' last ' . date('l', strtotime('next weekday'))));
                $comeca = date("d-m-Y", strtotime($_GET["dt"] . ' last ' . date('l', strtotime('next weekday'))));
            } else {
                $date = date("Y-m-d", strtotime('next weekday')); //(date('D') == 'Mon') ? date("Y-m-d") : date("Y-m-d", strtotime('last monday'));
                $comeca = date("d-m-Y", strtotime('next weekday')); //(date('D') == 'Mon') ? date("d-m-Y") : date("d-m-Y", strtotime('last monday'));
            }
        }
        ?>
        <style>

            #radio {
                float: right;
                margin-bottom: 15px;
                margin-top: -35px;
            }
            #nav {
                margin: 15px auto 15px;
                width: 255px;
            }
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
                font-size: 8pt;
                table-layout: fixed;
            }
            .dia {
                background: #EFEFEF;
                color: #0063DC;
                padding: 1px 3px;
                width: 110px !important;
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
            .nome > span { color: #da4f49 }
            .slot:not(.past):not(.deles){cursor: pointer}
            .hilite { background: #FFFEC9 }
            .reservavel:hover { background: #0088cc }
            .past {background: #CCCCCC}
            .muda{background: #cae387 !important;}
            .disponivel{
                background: #CCFFCC;
            }
            .imported{background:#FF0000 !important}
            .disponivel:hover {background: #0073ea;}
            .bloqueado{background-image: url("/images/icons/stripes.png");}
            .limbo{background: black;}
            #crm{
                box-shadow: 0 0px 26px 0px rgba(0,0,0,0.5);
                display: none;
                left: 5%;
                padding: 5px 0;
                position: fixed;
                top: 20px;
                width: 90%;    
                z-index:11;}
            #crm .grid-content{overflow-y:auto;
                               height: 430px;}
            .d{color:#ffffff;font-weight:bold;}

            #dialog-form label, #dialog-form input { display:block; }
            #dialog-form input.text { margin-bottom:12px; width:95%; padding: .4em; }
            #dialog-form fieldset { padding:0; border:0; margin-top:25px; }
            #dialog-form h1 { font-size: 1.2em; margin: .6em 0; }
            .validateTips { border: 1px solid transparent; padding: 0.3em; }
            #conteiner{
                box-shadow: 0px 2px 4px -1px rgba(0,0,0,0.5);
                position: relative;
                bottom: 4px;
                margin: auto;
                width: 99%;
            }
            .postal{
                text-align:center;
                display:block;
            }
            <?php
            //start struct
            if (isset($_GET["sch"])) {
                $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days,a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.id_scheduler=$id_scheduler " . (($users->user_level > 5) ? "" : "AND b.active=1 AND a.active=1") . ";";
            } elseif (isset($_GET["rsc"])) {
                $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days,a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.id_resource=$id_resource " . (($users->user_level > 5) ? "" : "AND b.active=1 AND a.active=1") . "";
            } elseif (isset($_GET["cp"])) {
                $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days,a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler INNER JOIN sips_sd_cp c ON a.alias_code=c.tecnico WHERE c.cp='$cp' " . (($users->user_level > 5) ? "" : "AND b.active=1 AND a.active=1") . "";
            } elseif (isset($_GET["ref"])) {
                $query = "SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days,a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE a.alias_code='" . mysql_real_escape_string($ref) . "' " . (($users->user_level > 5) ? "" : "AND b.active=1 AND a.active=1") . ";";
            }


            $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 1");
            $user_groups = "";
            for ($i = 0; $i < mysql_num_rows($result); $i++) {
                $resources[$i] = mysql_fetch_assoc($result);
                $user_groups.="'" . $resources[$i]["user_group"] . "',";
            }
            $user_groups = rtrim($user_groups, ",");

            $query = "SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types where user_group in ($user_groups);";
            $result = mysql_query($query, $link);
            for ($index = 0; $index < mysql_num_rows($result); $index++) {
                $row = mysql_fetch_assoc($result);
                $r_types[$index] = $row;
                echo ".t" . $r_types[$index]["id_reservations_types"] . " {background: " . $r_types[$index]["color"] . ";}\n";
            }

            if ($users->user_level > 5) {

                $acaba = date("d-m-Y", strtotime($comeca . ' next sunday'));
                $begin_sys = $date;
                $end_sys = date("Y-m-d", strtotime($comeca . ' next sunday +1 day'));
            } else {
                $acaba = date("d-m-Y", strtotime($comeca . ' +' . $resources[0]["days_visible"] . 'days '));
                $begin_sys = $date;
                $end_sys = date("Y-m-d", strtotime($comeca . ' +' . $resources[0]["days_visible"] . 'days '));
            }
            ?>
        </style>
    <body>
        <div class='grid' >
            <div class="grid-title">
                <div class="pull-left">Calendário</div>
                <div class="pull-right"><button class="btn btn-success" id="exp-btn"><i class="icon-plus"></i>Excepção</button></div>
            </div>

            <?php
            if (isset($_GET["cp"]) OR isset($_GET["ref"])) {
                $id_scheduler = $resources[0]["id_scheduler"];
            }
            //end struct
            //Reservas
            if (isset($_GET["sch"]) OR isset($_GET["cp"]) OR isset($_GET["ref"])) {
                $query = "SELECT id_reservation, start_date, end_date, a.id_resource,id_user,a.lead_id,id_reservation_type,b.display_text, d.postal_code FROM sips_sd_reservations a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource  INNER JOIN sips_sd_reservations_types c ON a.id_reservation_type=c.id_reservations_types LEFT JOIN vicidial_list d ON a.lead_id = d.lead_id WHERE gone=0 and b.id_scheduler=$id_scheduler And start_date <='$end_sys' And start_date >='$begin_sys';";
            } elseif (isset($_GET["rsc"])) {
                $query = "SELECT id_reservation, start_date, end_date, id_resource,id_user,a.lead_id,id_reservation_type,display_text, c.postal_code FROM sips_sd_reservations a  INNER JOIN sips_sd_reservations_types b ON a.id_reservation_type=b.id_reservations_types LEFT JOIN vicidial_list c ON a.lead_id = c.lead_id WHERE gone=0 and id_resource=$id_resource And start_date <='$end_sys' And start_date >='$begin_sys'";
            }

            $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 2");
            for ($i = 0; $i < mysql_num_rows($result); $i++) {
                $reservas[$i] = mysql_fetch_assoc($result);
            }
            //End Reservas
            //Imported Reservas
            if (isset($_GET["sch"]) OR isset($_GET["cp"]) OR isset($_GET["ref"])) {
                $query = "SELECT id_reservation_acu, start_date, a.id_resource,id_reservation_type,c.display_text FROM sips_sd_reservations_acu a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource INNER JOIN sips_sd_reservations_types c ON a.id_reservation_type=c.id_reservations_types WHERE b.id_scheduler=$id_scheduler And start_date <='$end_sys' And start_date >='$begin_sys';";
            } elseif (isset($_GET["rsc"])) {
                $query = "SELECT id_reservation_acu, start_date, id_resource,id_reservation_type,display_text FROM sips_sd_reservations_acu a  INNER JOIN sips_sd_reservations_types b ON a.id_reservation_type=b.id_reservations_types WHERE id_resource=$id_resource And start_date <='$end_sys' And start_date >='$begin_sys'";
            }


            $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 2.1");
            $i_reservas = array();
            for ($i = 0; $i < mysql_num_rows($result); $i++) {
                $i_reservas[$i] = mysql_fetch_assoc($result);
            }
            //End that shit
            //Series
            if (isset($_GET["sch"]) OR isset($_GET["cp"]) OR isset($_GET["ref"])) {
                $query = "Select a.id_resource,a.start_time,a.end_time,a.day_of_week_start,a.day_of_week_end From sips_sd_series a INNER JOIN sips_sd_resources b Where b.id_scheduler=$id_scheduler";
            } elseif (isset($_GET["rsc"])) {
                $query = "Select id_resource,start_time,end_time,day_of_week_start,day_of_week_end From sips_sd_series Where id_resource=$id_resource";
            }

            $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 3");
            $series = Array();
            for ($i = 0; $i < mysql_num_rows($result); $i++) {
                $series[$i] = mysql_fetch_assoc($result);
            }
            //End Series
            //Execoes
            if (isset($_GET["sch"]) OR isset($_GET["cp"]) OR isset($_GET["ref"])) {
                $query = "SELECT a.id_execao,a.id_resource, a.start_date, a.end_date FROM sips_sd_execoes a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource WHERE b.id_scheduler=$id_scheduler";
            } elseif (isset($_GET["rsc"])) {
                $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=$id_resource";
            }

            $result = mysql_query($query, $link) or die("Não foi encontrado nenhum calendário... 4");
            $execoes = array();
            for ($i = 0; $i < mysql_num_rows($result); $i++) {
                $execoes[$i] = mysql_fetch_assoc($result);
            }
            //End execoes
            ?>
            <div id="nav" class="<?= ($users->user_level < 5 and $resources[0]["days_visible"] < 7) ? 'hide' : '' ?>">
                <span id="anterior" class='btn btn-mini icon-alone'><i class='icon-arrow-left'></i></span>
                <span><?= $comeca . " - " . $acaba ?></span>
                <span id="seguinte" class='btn btn-mini icon-alone'><i class='icon-arrow-right'></i></span>
            </div>
            <div class="grid" id="conteiner">
                <?php
                $resources[0]["days_visible"] = ($users->user_level > 5) ? 7 : $resources[0]["days_visible"];
                for ($ii = 0; $ii < $resources[0]["days_visible"]; $ii++) {
                    $display_date = date("d/m/Y", strtotime($date));
                    $tab = array();
                    $tab[0] = "<table class='reservas table table-mod table-condensed'>\n\t<thead>\n";
                    for ($i = $resources[0]["begin_time"]; $i <= $resources[0]["end_time"]; $i += $resources[0]["blocks"]) {
                        $tab[0] .= ($i == $resources[0]["begin_time"]) ? "\t<tr>\n\t\t<td class='dia" . ((date("Y-m-d", strtotime($date)) == date("Y-m-d", strtotime("today"))) ? " hoje" : "") . "'>" . days2dias(strtotime($date)) . ", $display_date</td>\n" : "";
                        //day in first column
                        $tab[0] .= "\t\t<td class='block'><span>" . m2h($i, true) . "</span></td>\n";
                        //blocks time
                        $tab[0] .= (($i + $resources[0]["blocks"]) > $resources[0]["end_time"]) ? "</tr></thead><tbody>\n" : "";
                        //end of the row

                        for ($iii = 0; $iii < count($resources); $iii++) {
                            if (!isset($tab[$iii + 1])) {
                                $tab[$iii + 1] = "";
                            }
                            $tab[$iii + 1] .= ($i == $resources[0]["begin_time"]) ? "\t<tr class='slots'>\n\t\t<td class='nome'><span>" . $resources[$iii]["display_text"] . "</span></td>\n" : "";
                            //title first column 
                            $beg = date("Y-m-d H:i:s", strtotime($date . "+$i minutes"));
                            $end = date("Y-m-d H:i:s", strtotime($date . "+" . ($i + $resources[0]["blocks"] - 1) . " minutes"));
                            $dados = set_estado($beg, $end, $resources[$iii]["id_resource"], $reservas, $series, $resources[$iii]["restrict_days"], $execoes, $date, $i, $slot2change, $i_reservas, $users);
                            $title = (($dados["type"] != "") ? " title='$dados[type]'" : "");
                            $tab[$iii + 1] .= "\t\t<td class='slot" . $dados["stat"] . "'$title >" . (!is_null($dados["postal"]) ? "<span class='postal'>$dados[postal]</span>" : "") . "
                                                <input type=hidden class='beg' value='" . $beg . "'/>
                                                <input type=hidden class='end' value='" . $end . "'/>
                                                <input type=hidden class='rsc' value='" . $resources[$iii]["id_resource"] . "'/>
                                                <input type=hidden class='lead' value='" . $dados["id"] . "'/>
                                                    " . ((preg_match("/imported/i", $dados["stat"]) AND (preg_match("/deles/i", $dados["stat"]) or preg_match("/reservardo/i", $dados["stat"]))) ? "<span class='d'>Duplicado</span>" : "") . "
                                           </td>\n";
                            //time slots
                            $tab[$iii + 1] .= (($i + $resources[0]["blocks"]) > $resources[0]["end_time"]) ? "</tr>\n" : "";
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
        <div id="loader" style="display: none;"></div>

        <div id="dialog-confirm" title="Confirmação"  style="display: none;">
            <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
        </div>

        <div id="dialog-form" title="Tipo de Reserva">
            <p class="validateTips">Escolha o tipo de reserva.</p>

            <form>
                <fieldset>
                    <label for="rtype">Tipo</label>
                    <select id="rtype" >
                        <?php
                        for ($index1 = 0; $index1 < count($r_types); $index1++) {
                            if ($r_types[$index1]["active"] == 1) {
                                echo "<option value='" . $r_types[$index1]["id_reservations_types"] . "'>" . $r_types[$index1]["display_text"] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </fieldset>
            </form>
        </div>

        <div id="dialog-form-exp" title="Marcação por excepção"  style="display: none;">
            <form id="form-exp">
                <fieldset>
                    <label for="exp-marcData">Data</label>
                    <input type="text" name="exp-marcData" id="exp-marcData" value="" required class="text ui-widget-content ui-corner-all" />

                    <select id="exp-rsc" required>
                        <option value="">Escolha o recurso</option>
                        <?php
                        for ($index1 = 0; $index1 < count($resources); $index1++) {
                            echo "<option value='" . $resources[$index1]["id_resource"] . "'>" . $resources[$index1]["display_text"] . "</option>";
                        }
                        ?>
                    </select>
                    <select id="exp-rtype" required>
                        <option value="">Escolha um tipo de reserva</option>
                        <?php
                        for ($index1 = 0; $index1 < count($r_types); $index1++) {
                            if ($r_types[$index1]["active"] == 1) {
                                echo "<option value='" . $r_types[$index1]["id_reservations_types"] . "'>" . $r_types[$index1]["display_text"] . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button class="btn btn-success">Verificar</button>
                </fieldset>
            </form>
            <form id="form-login"  style="display: none;">
                <fieldset>
                    <h5>A marcar para:</h5>
                    <p id="exp-Data"></p>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" value="" required class="text ui-widget-content ui-corner-all" />
                    <button class="btn btn-primary">Marcar</button>
                    <button style="margin-left: 5px" id="exp-voltar" class="btn icon-alone"><i class="icon-arrow-left"></i></button>
                </fieldset>
            </form>
            <p></p>
        </div>
        <div id="crm" class='grid' style='display:none'>
            <div class="grid-title">
                <div class="pull-left">Gestão de Leads</div>
                <div class="pull-right"><span class='btn' id='crm-close'>Fechar</span></div>
            </div>
            <div class='grid-content'></div>
        </div>

        <script>
            $(function() {
                var
                        lead = '<?= $id_lead; ?>',
                        user = '<?= $id_user; ?>',
                        slot2change = <?= (isset($_GET["muda"]) ? $_GET["muda"] : 0) ?>,
                        bloco_res,
                        rsc_id = <?= (isset($_GET["rsc"]) ? $_GET["rsc"] : 0) ?>,
                        b,
                        time_block =<?= $resources[0]["blocks"] ?>,
                        id_elemento =<?= $id_elemento ?>;

                function ChangeDate(data) {
                    RedirectToSelf("dt", /dt=\d{4}-\d{2}-\d{2}/i, "dt=" + data, slot2change);
                }

                function RedirectToSelf(queryStringParam, regexMatch, substitution, extra) {
                    var val = new RegExp('(\\?|\\&)muda=.*?(?=(&|$))');
                    var url = location.href.replace(val, '');
                    var newUrl = url;
                    if (url.indexOf(queryStringParam + "=") != -1) {
                        newUrl = url.replace(regexMatch, substitution);
                    } else if (url.indexOf("?") != -1) {
                        newUrl = url + "&" + substitution;
                    } else {
                        newUrl = url + "?" + substitution;
                    }
                    newUrl = newUrl.replace("#", "");
                    var mudar = "";
                    if (extra != 0) {
                        mudar = "&muda=" + extra;
                    }
                    location = newUrl + mudar;
                }


                function showDialog(msg) {
                    $('#alertbox').html(msg);
                    $('#dialog-confirm').dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }

                function confirma(msg) {
                    var def = $.Deferred();

                    $('#alertbox').html(msg);
                    $("#dialog-confirm").dialog({
                        resizable: false,
                        height: 170,
                        modal: true,
                        buttons: {
                            Sim: function() {
                                def.resolve();
                                $(this).dialog("close");
                            },
                            Cancelar: function() {
                                def.reject();
                                $(this).dialog("close");
                            }
                        },
                        close: function() {
                        }
                    });
                    return def.promise();
                }
                function confirma_alt(msg) {
                    var def = $.Deferred();

                    $('#alertbox').html(msg);
                    $("#dialog-confirm").dialog({
                        resizable: false,
                        width: "350px",
                        modal: true,
                        buttons: {
                            Sim: function() {
                                $(this).dialog("close");
                                def.resolve();
                            },
                            Cancelar: function() {
                                $(this).dialog("close");
                                def.reject();
                            },
                            Eliminar: function() {
                                $(this).dialog("close");
                                reservation.elimina();
                                def.reject();
                            },
                            Info: function() {
                                $(this).dialog("close");
                                crm.open();
                                def.reject();
                            }
                        },
                        close: function() {
                        }
                    });
                    return def.promise();
                }

                var crm_edit_object;
                var crm = {
                    close: function()
                    {
                        $("#loader").hide();
                        $("#crm").hide();
                        crm_edit_object.destroy();
                    },
                    open: function() {
                        if ($('.lead', bloco_res).val() == 0) {
                            showDialog("Esta reserva não tem lead atribuida...");
                            return false;
                        }
                        $("#loader").show();
                        $("#crm").show();
                        crm_edit_object = new crm_edit($("#crm .grid-content"), "/sips-admin/crm/", $('.lead', bloco_res).val());
                        crm_edit_object.init();

                    }
                };
                var reservation = {
                    //Start reservation
                    create: function(b) {
                        if (opener && $("#marcdata", opener.document).length) {
                            if (opener.marcado) {
                                return false;
                            }
                        }
                        if (id_elemento) {
                            var elemento = opener.window.$("#" + id_elemento + " select");
                            if (elemento.data().live_marc === elemento.data().max_marc) {
                                showDialog("Já completou o Maximo de marcações.");
                                return false;
                            }
                        }
                        if (slot2change != 0) {
                            return false;
                        }
                        var bloco = b;
                        $("#loader").show();
                        var rtype = $("#rtype option:selected");
                        $.post('../ajax/reservas.php', {
                            start: $('.beg', bloco).val(),
                            end: $('.end', bloco).val(),
                            resource: $('.rsc', bloco).val(),
                            user: user,
                            lead: lead,
                            rtype: rtype.val()
                        },
                        function(data) {
                            if (data.sucess == "1") {
                                $(bloco).removeClass("reservavel").addClass("reservado").addClass("t" + rtype.val()).attr("title", rtype.text());
                                if (opener && $("#marcdata", opener.document).length) {
                                    $("#marcdata", opener.document).val($('.beg', bloco).val().substr(0, 10));
                                    $("#HOUR_marchora", opener.document).val($('.beg', bloco).val().substr(11, 2));
                                    $("#MINUTE_marchora", opener.document).val($('.beg', bloco).val().substr(14, 2));
                                    opener.marcado = true;
                                }
                                if (id_elemento) {
                                    var elemento = opener.window.$("#" + id_elemento + " select");
                                    elemento.data().live_marc++;
                                }
                                if (lead) {
                                    close();
                                }
                                $("#loader").hide();
                            } else if (data.sucess == "0") {
                                $("#loader").hide();
                                confirma("Esta slot já se encontra reservada.\n Deseja fazer um reload?").done(function() {
                                    location.reload();
                                }).fail(function() {
                                    $(bloco).removeClass("reservavel").addClass("reservado").addClass("limbo");
                                    $("#loader").hide();
                                });
                            }
                        }, "json").fail(function() {
                            $("#loader").hide();
                            alert('Erro de conecção ao servidor.');
                        });
                    },
                    elimina: function() {
                        $("#loader").show();
                        $.post('../ajax/apaga_reservas.php', {
                            start: $('.beg', bloco_res).val(),
                            end: $('.end', bloco_res).val(),
                            resource: $('.rsc', bloco_res).val()
                        },
                        function(data) {
                            if (data.sucess == "1") {
                                $(bloco_res).removeClass("reservado").removeClass(function(index, css) {
                                    return (css.match(/\bt\S+/g) || []).join(' ');
                                }).removeAttr("title").addClass("reservavel");
                                if (opener && $("#marcdata", opener.document).length) {
                                    opener.marcado = false;
                                }
                                if (id_elemento) {
                                    var elemento = opener.window.$("#" + id_elemento + " select");
                                    elemento.data().live_marc--;
                                }
                                $("#loader").hide();
                            } else if (data.sucess == "0") {
                                $("#loader").hide();
                                alert("Ocorreu-se um erro não identificado...");
                            }
                        }, "json").fail(function() {
                            alert('Erro de conecção ao servidor.');
                            $("#loader").hide();
                        });
                    }

                }
                var alterReservation = {
                    Start: function() {
                        bloco_res = this;
                        if (slot2change != 0) {
                            return false;
                        }
                        confirma_alt("Deseja mudar a marcação?").done(function() {
                            $("#loader").show();
                            $.post('../ajax/altera_reservas.php', {
                                pedido: 1,
                                start: $('.beg', bloco_res).val(),
                                end: $('.end', bloco_res).val(),
                                resource: $('.rsc', bloco_res).val()
                            },
                            function(data) {
                                if (data.sucess == "1") {
                                    cl4ss = $(bloco_res).attr("class");
                                    $('.slot').removeClass("reservavel").not('.reservado').not('.bloqueado').not('.past').addClass("disponivel");
                                    $(bloco_res).removeClass("reservado").addClass("muda");
                                    slot2change = data.id;
                                    $("#loader").hide();
                                }
                                ;
                            }, "json").fail(function() {
                                alert('Erro de conecção ao servidor.');
                                $("#loader").hide();
                            });
                        });
                    },
                    Submit: function() {
                        var bloco = this;
                        $("#loader").show();
                        $.post('../ajax/altera_reservas.php', {
                            pedido: 2,
                            start: $('.beg', bloco).val(),
                            end: $('.end', bloco).val(),
                            resource: $('.rsc', bloco).val(),
                            id: slot2change
                        },
                        function(data) {
                            if (data.sucess == "1") {
                                var muda = $('.muda');
                                var title = muda.attr("title");
                                $(bloco).addClass(cl4ss).attr("title", title);
                                muda.removeClass(function(index, css) {
                                    return (css.match(/\bt\S+/g) || []).join(' ');
                                }).removeAttr("title").removeClass("muda");
                                $('.slot').removeClass("disponivel").not('.reservado').not('.bloqueado').addClass("reservavel");
                                if (opener && $("#marcdata", opener.document).length) {
                                    $("#marcdata", opener.document).val($('.beg', bloco).val().substr(0, 10));
                                    $("#HOUR_marchora", opener.document).val($('.beg', bloco).val().substr(11, 2));
                                    $("#MINUTE_marchora", opener.document).val($('.beg', bloco).val().substr(14, 2));
                                    close();
                                }
                                $("#loader").hide();
                                slot2change = 0;
                            } else if (data.sucess == "0") {
                                $("#loader").hide();
                                confirma("Esta slot já se encontra reservada.\n Deseja fazer um reload?").done(function() {
                                    location.reload();
                                }).fail(function() {
                                    $(bloco).removeClass("disponivel").addClass("reservado").addClass("limbo");
                                    $('.muda').removeClass("muda").addClass("reservado");
                                    $('.disponivel').removeClass("disponivel").addClass("reservavel");
                                    slot2change = 0;
                                    $("#loader").hide();
                                });
                            }
                        }, "json").fail(function() {
                            alert('Erro de conecção ao servidor.');
                            $("#loader").hide();
                        });
                    }
                };

                var excepcao = {
                    btn: $("#exp-btn"),
                    dialog: $("#dialog-form-exp")
                };


                //Start Design

                $('.slot').hover(function() {
                    $(this).siblings('.nome').toggleClass('hilite');
                });

                $("#crm-close").click(crm.close);
                //End Design

                //Start nav
                $("#anterior").click(function() {
                    ChangeDate('<?= last_week($date, $users) ?>');
                });

                $("#seguinte").click(function() {
                    ChangeDate('<?= next_week($date) ?>');
                });


                //End Nav 



                $(document).on("click", '.reservavel', function() {
                    b = this;
                    $("#dialog-form").dialog("open");
                });

                $("#dialog-form").dialog({
                    autoOpen: false,
                    modal: true,
                    buttons: {
                        "Ok": function() {
                            reservation.create(b);
                            $(this).dialog("close");
                        },
                        Cancel: function() {
                            $(this).dialog("close");
                        }
                    },
                    close: function() {
                    }
                });
                //reservation tipe

                $("#exp-marcData").datetimepicker({format: 'yyyy-mm-dd hh:ii', minuteStep: time_block, autoclose: true, language: "pt"});
                //Change reservation first step 

                var cl4ss;
                $(document).on("click", '.reservado:not(.past,.deles)', alterReservation.Start);

                //Change reservation final step
                $(document).on("click", '.disponivel', alterReservation.Submit);

                //Cancel change
                $(document).on("click", '.muda', function() {
                    $('.muda').removeClass("muda").addClass("reservado");
                    $('.disponivel').removeClass("disponivel").addClass("reservavel");
                    slot2change = 0;
                });
                //End reservation 

                $(document).on("click", ".reservado.past, .deles", function() {
                    bloco_res = this;
                    confirma("Deseja ver a informação desta reserva?").done(function() {
                        crm.open();
                    });
                });

                excepcao.btn.click(function() {
                    excepcao.dialog.dialog({modal: true});
                });
                excepcao
                        .dialog
                        .find("#form-exp")
                        .submit(function(e) {
                            e.preventDefault();
                            var dataT = excepcao.dialog.find("#exp-marcData").val() + " - " + moment(excepcao.dialog.find("#exp-marcData").val() + ":00").add('minutes', time_block - 1).format("YYYY-MM-DD HH:mm");
                            console.log(x);
                            $(this)
                                    .hide()
                                    .next()
                                    .find("#exp-Data")
                                    .text(dataT)
                                    .end()
                                    .show();
                        })
                        .next()
                        .submit(function(e) {
                            e.preventDefault();
                            $.post("../ajax/reserva_excepcao.php", {
                                start: excepcao.dialog.find("#exp-marcData").val() + ":00",
                                end: moment(excepcao.dialog.find("#exp-marcData").val() + ":00").add('minutes', time_block - 1).format("YYYY-MM-DD HH:mm:ss"),
                                resource: excepcao.dialog.find("#exp-rsc").val(),
                                user: user,
                                lead: lead,
                                rtype: excepcao.dialog.find("#exp-rtype").val(),
                                pass: excepcao.dialog.find("#password").val()},
                            function(data) {
                                alert(data.message);
                                if (data.success) {
                                    if (id_elemento) {
                                        var elemento = opener.window.$("#" + id_elemento + " select");
                                        elemento.data().live_marc++;
                                    }
                                    if (lead) {
                                        close();
                                    }
                                }
                            }, "json");
                        })
                        .find("#exp-voltar")
                        .click(function(e) {
                            e.preventDefault();
                            $(this)
                                    .parent()
                                    .parent()
                                    .hide()
                                    .prev()
                                    .show()[0].reset();
                        });

            });
            var x;
        </script>

        <script type="text/javascript" src="/sips-admin/crm/crm_edit/crm_edit.js"></script>
    </body>
</html>