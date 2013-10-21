<!DOCTYPE html>
<html>
    <head>

        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Script dinamico</title> 
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/jquery.jgrowl.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/datetimepicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/validationEngine.jquery.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.jgrowl.js"></script>
        <script type="text/javascript" src="/bootstrap/js/datetimepicker/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/datetimepicker/locales/bootstrap-datetimepicker.pt.js"></script>


        <script type="text/javascript" src="/bootstrap/js/validation/jquery.validationEngine.js"></script>
        <script type="text/javascript" src="/bootstrap/js/validation/languages/jquery.validationEngine-pt.js"></script>
        <script type="text/javascript" src="/bootstrap/js/validation/contrib/other-validations.js"></script>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <style> 

            .modal-backdrop {background: none;}
            .view-button{
                margin-left: 0.5em;
            }
            #contact_list{
                width:100%!important;
            }
        </style>
    </head>
    <body>
        <?php
        require("../../ini/dbconnect.php");


        $query = "SELECT cloud FROM servers";
        $query = mysql_query($query, $link);
        $is_cloud = mysql_fetch_row($query);
        $is_cloud = $is_cloud[0];
        $user = $_SERVER['PHP_AUTH_USER'];


        # Campanhas

        $current_admin = $_SERVER['PHP_AUTH_USER'];
        $query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
        $query = mysql_fetch_assoc($query);
        $usrgrp = $query['user_group'];
        $stmt = "SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$usrgrp';";
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $LOGallowed_campaigns = $row[0];
        $LOGallowed_reports = $row[1];

        $LOGallowed_campaignsSQL = '';
        $whereLOGallowed_campaignsSQL = '';
        if ((!eregi("-ALL", $LOGallowed_campaigns))) {
            $rawLOGallowed_campaignsSQL = preg_replace("/ -/", '', $LOGallowed_campaigns);
            $rawLOGallowed_campaignsSQL = preg_replace("/ /", "','", $rawLOGallowed_campaignsSQL);
            $LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
            $whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
        }

        $query = "SELECT campaign_name, campaign_id FROM vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_name";
        $query = mysql_query($query);
        while ($row = mysql_fetch_assoc($query)) {
            $select_campaigns .= "<option value='$row[campaign_id]'>$row[campaign_name]</option>";
        }
        #Bases de Dados
        $query = "SELECT list_name, list_id FROM vicidial_lists";
        $query = mysql_query($query);
        $select_bds = "<option value='all'>Todas</option>";
        while ($row = mysql_fetch_assoc($query)) {
            $select_bds .= "<option value='$row[list_id]'>$row[list_name]</option>";
        }
        #Operadores
        if ($usrgrp == 'ADMIN') {
            $query = "SELECT full_name, user FROM vicidial_users WHERE user NOT IN('VDAD', '1000') order by full_name asc";
        } else {
            $query = "SELECT full_name, user FROM vicidial_users WHERE user NOT IN('VDAD', '1000') AND user_group = '$usrgrp' order by full_name asc ";
        }
        $query = mysql_query($query);

        $select_operadores = "<option value='all'>Todos</option>";
        while ($row = mysql_fetch_assoc($query)) {
            $tExplode = explode(" ", $row['full_name']);
            $t = count($tExplode);
            if ($t == 1) {
                $full_name = $tExplode[0];
            } else {
                $full_name = $tExplode[0] . " " . $tExplode[$t - 1];
            }
            $select_operadores .= "<option value='$row[user]'>$full_name</option>";
        }
        #Feedbacks
        $query = "SELECT status, status_name FROM vicidial_campaign_statuses";
        $query = mysql_query($query);

        $select_feedback = "<option value='all'>Todos</option>";
        while ($row = mysql_fetch_assoc($query)) {
            $select_feedback .= "<option value='$row[status]'>$row[status_name]</option>";
        }
        ?>


        <div class="content" id="main_content">
            <div class="row-fluid">
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Procura de Contactos</div>
                        <div class="pull-right">       
                        </div>
                    </div>
                    <div class="grid-content">


                        <form class="form-horizontal" id="form_pesquisa">
                            <div class="span5">
                                <div class="control-group">
                                    <label class="control-label" for="inputEmail">Data Inicial</label>
                                    <div class="controls">
                                        <input readonly="readonly" class="datepicker-input" type="text" id="datai" value="<?= $daystart; ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Data Final</label>
                                    <div class="controls">
                                        <input readonly="readonly" class="datepicker-input" type="text" id="dataf" value="<?= $dayend; ?>">
                                    </div>
                                </div>

                                <div class="control-group">


                                    <div class="controls">
                                        <input  type="radio" id="dcarregamento" name="data-search-type" /><label for='dcarregamento'>Por data de carregamento<span></span></label>
                                    </div>
                                </div>

                                <div class="control-group">

                                    <div class="controls">
                                        <input type="radio" id="dultima" checked="checked" name="data-search-type" /><label for="dultima">Por data da ultima chamada<span></span></label>
                                    </div>
                                </div>

                            </div>




                            <div class="span5">
                                <div class="control-group">
                                    <label class="control-label" for="inputEmail">Campanha</label>
                                    <div class="controls">
                                        <select  class="chosen-select" id='filtro_campanha'><?= $select_campaigns; ?></select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Base de Dados</label>
                                    <div class="controls">
                                        <select class="chosen-select"  id="filtro_dbs"><?= $select_bds; ?></select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Operador:</label>

                                    <div class="controls">
                                        <select class="chosen-select"  id='filtro_operador'><?= $select_operadores; ?></select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Feedback:</label>
                                    <div class="controls">
                                        <select class="chosen-select"  id='filtro_feedback'><?= $select_feedback; ?></select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">ID do Contacto:</label>
                                    <div class="controls">
                                        <input type="text" id='crm-contact-id'>
                                        <div class="help-inline ">
                                            (O uso desta opção invalída os outros filtros)
                                        </div>

                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Telefone do Contacto:</label>
                                    <div class="controls">
                                        <input type="text" id='crm-contact-phone'>
                                        <div class="help-inline ">
                                            (O uso desta opção invalída os outros filtros)
                                        </div>

                                    </div>
                                </div>
                                <button class="btn btn-success right" >Pesquisa</button>
                            </div>


                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="grid-transparent">
                <table class="table table-mod table-bordered table-striped" id='contact_list'>
                    <thead></thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>

            </div>
        </div>

        <!-- Lista de Contactos -->






        <div id="crm" class='grid' style='display:none'>
            <div class="grid-title">
                <div class="pull-left">Gestão de Leads</div>
                <div class="pull-right"><span class='btn btn-danger icon-remove' id='crm-close'></span></div>
            </div>
            <div class='grid-content' id="html_loader"></div>
        </div>




        <script>

            /* Função que actualiza as dropdowns qnd se muda de campanha */
            $('#filtro_campanha').change(function()
            {
                var campaign = $('#filtro_campanha').val();
                var db_list = "";
                var feed_list = "";
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "_requests.php",
                    data: {action: "campaign_change_db", sent_campaign: campaign},
                    success: function(data)
                    {
                        if (data == "") {
                            $("#filtro_dbs").html("<option value=''>Nenhuma Base de Dados Associada</option>").prop("disabled", true);
                        } else {
                            db_list = "<option value='all'>Todas</option>";
                            $.each(data.db_list, function(key, obj) {
                                db_list += "<option value='" + obj.list_id + "'>" + obj.list_name + "</option>";
                            });
                            $("#filtro_dbs").html(db_list).prop("disabled", false);
                             $("#filtro_dbs").val("").trigger("liszt:updated");
                        }
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "_requests.php",
                            data: {action: "campaign_change_feedback", sent_campaign: campaign},
                            success: function(data)
                            {
                                if (data == "") {
                                    $("#filtro_feedback").html("<option value=''>Nenhum Feedback Associado</option>").prop("disabled", true);
                                } else {
                                    feed_list = "<option value='all'>Todos</option>";
                                    $.each(data.feed_list, function(key, obj) {
                                        feed_list += "<option value='" + obj.status + "'>" + obj.status_name + "</option>";
                                    });
                                    $("#filtro_feedback").html(feed_list).prop("disabled", false);
                                     $("#filtro_feedback").val("").trigger("liszt:updated");
                                }
                            }
                        });

                    }
                });
            });
            /* Função que realiza a pesquisa e que mostra a tabela com os resultados */
            $("#form_pesquisa").submit(function(e) {
                e.preventDefault();
                $('#contact_list').hide();
                var datai = $("#datai").val();
                var dataf = $("#dataf").val();
                var filtro_campanha = $("#filtro_campanha").val();
                var filtro_dbs = $("#filtro_dbs").val();
                var filtro_operador = $("#filtro_operador").val();
                var filtro_feedback = $("#filtro_feedback").val();
                var dataflag = "";
                if ($("input[name='data-search-type']:checked").attr("id") == 'dcarregamento') {
                    dataflag = 0;
                } else {
                    dataflag = 1;
                }

                var oTable = $('#contact_list').dataTable({
                    "bSortClasses": false,
                    "bProcessing": true,
                    "bDestroy": true,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": '_requests.php',
                    "fnServerParams": function(aoData) {
                        aoData.push(
                                {"name": "action", "value": "get_table_data"},
                        {"name": "datai", "value": datai},
                        {"name": "dataf", "value": dataf},
                        {"name": "filtro_campanha", "value": filtro_campanha},
                        {"name": "filtro_dbs", "value": filtro_dbs},
                        {"name": "filtro_operador", "value": filtro_operador},
                        {"name": "filtro_feedback", "value": filtro_feedback},
                        {"name": "dataflag", "value": dataflag},
                        {"name": "contact_id", "value": $("#crm-contact-id").val()},
                        {"name": "phone_number", "value": $("#crm-contact-phone").val()}
                        )
                    },
                    "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Telefone"}, {"sTitle": "Morada"}, {"sTitle": "Ultima Chamada"}],
                    "fnDrawCallback": function(oSettings, json) {
                        $('#contact_list').show();
                    },
                    "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
                });

            });
            /* Função que força uma pesquisa "Todas" quando se filtra por data de carregamento */
            $("input:radio[name=data-search-type]").change(function() {
                if ($("input:radio[name=data-search-type]:checked").attr("id") == "dcarregamento")
                {
                    $("#filtro_dbs").val("all").prop("disabled", true);
                } else {
                    $("#filtro_dbs").val("all").prop("disabled", false);
                }

            });



            $("#crm-close").on("click", function()

            {
                $('#crm').hide();
                $("#main_content").show();

            });

            function LoadHTML(lead_id)
            {


                $.post("crm_edit.php", {lead_id: lead_id, campaign_id: $("#filtro_campanha option:selected").val()},
                function(msg)
                {
                    $('#crm').show().find(".grid-content").html(msg);
                    $("#main_content").hide();
                }
                );

            }


            $(/* Inicialização da página conforme a primeira campanha da dropdown */
                    function()
                    {

                        $("#datai").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).keypress(function(e) {
                            e.preventDefault();
                        }).bind("cut copy paste", function(e) {
                            e.preventDefault();
                        });
                        $("#dataf").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).keypress(function(e) {
                            e.preventDefault();
                        }).bind("cut copy paste", function(e) {
                            e.preventDefault();
                        });


                        $(".chosen-select").chosen({no_results_text: "Sem resultados"});



                        var campaign = $('#filtro_campanha').val();
                        var db_list = "";
                        var feed_list = "";
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "_requests.php",
                            data: {action: "campaign_change_db", sent_campaign: campaign},
                            success: function(data)
                            {
                                if (data == "") {
                                    $("#filtro_dbs").html("<option value=''>Nenhuma Base de Dados Associada</option>").prop("disabled", true);
                                } else {
                                    db_list = "<option value='all'>Todas</option>";
                                    $.each(data.db_list, function(key, obj) {
                                        db_list += "<option value='" + obj.list_id + "'>" + obj.list_name + "</option>";
                                    });
                                    $("#filtro_dbs").html(db_list).prop("disabled", false);
                                    $("#filtro_dbs").val("").trigger("liszt:updated");
                                }
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    url: "_requests.php",
                                    data: {action: "campaign_change_feedback", sent_campaign: campaign},
                                    success: function(data)
                                    {
                                        if (data == "") {
                                            $("#filtro_feedback").html("<option value=''>Nenhum Feedback Associado</option>").prop("disabled", true);
                                        } else {
                                            feed_list = "<option value='all'>Todos</option>";
                                            $.each(data.feed_list, function(key, obj) {
                                                feed_list += "<option value='" + obj.status + "'>" + obj.status_name + "</option>";
                                            });
                                            $("#filtro_feedback").html(feed_list).prop("disabled", false);
                                             $("#filtro_feedback").val("").trigger("liszt:updated");
                                        }
                                    }
                                });

                            }
                        });
                    }
            );

        </script>
    </body>
</html>

