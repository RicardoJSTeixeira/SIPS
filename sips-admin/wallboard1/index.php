<?php
require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>


<!DOCTYPE HTML>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title> 
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/jquery.jgrowl.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">


        <script type="text/javascript" src="/jquery/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.jgrowl.js"></script>


        <style>



            .draggablePanel{ width: auto; height: auto; margin: 0 auto; overflow: hidden;}
            /* Default color for links inside the tabbed box */ 
            .ui-widget-content a, .ui-widget-content a:hover, .ui-widget-content a:visited, .ui-widget-content a:active {
                color: #2954d1 !important; 
                text-decoration: underline !important; 
            }
            /* Default styling for selected tab titles */ 
            .ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
                color: #212121 !important;
                text-decoration: none !important;
                cursor: text !important;
            }
            /* Default styling for unselected tab titles */ 
            .ui-state-default a:hover, .ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited {
                color: #555555 !important;
                text-decoration: none !important;
                cursor: text !important;
            }

            .row-fluid .toolBar_span{
                margin-top: 50px;
                width: 51px;
                margin-left: 0;
                border-bottom-left-radius: 0;
                border-top-left-radius: 0;
                border-left: 0;
            }
            .board
            {
                min-width: 926px;
            }
            #MainLayout
            {
                position:absolute;
                width:904px;
                height:512px;
                background-color: #F2F2F2;
                border: 1px solid #ccc;
                border: 1px solid rgba(0, 0, 0, 0.15);
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 6px;
                color: #333333;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            }
            .PanelWB{position:absolute;
                     -webkit-border-radius: 3px;
                     -moz-border-radius: 3px;

                     border-radius: 3px;
                     box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
            }

            .opções_layout{
                margin-left: -1px !important;
                border-top-right-radius: 3px !important;
                border-bottom-right-radius: 3px !important
            }

        </style>  
    </head>

    <body>
        <div class="grid-content">
            <div class="row-fluid">
                <div class="grid span11 board" >
                    <div class="grid-title">
                        <div class="pull-left">
                            <button id="add_layout_button" data-t="tooltip-right" title="Adiciona um novo ecrã" class="btn btn-info  btn-large" > <i class="icon-plus "></i>Novo</button>
                        </div>
                        <div class="pull-right">

                            <div class="input-prepend input-append"><span  class="add-on">Layout</span>
                                <select id="LayoutSelector" onchange="layout_change();"></select>
                                <button data-t="tooltip-right" title="Opções da Layout"  class="opções_layout  btn btn-warning icon-alone" id="opcao_layout_button"><i class="icon-cogs"></i></button>
                            </div>

                        </div>
                    </div>



                    <div class="grid-content ">
                        <div  style="position:relative;width:904px;height:512px; margin: 0 auto;">
                            <div id="MainLayout" class="ui-widget-header " style="position:relative;" ></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>  

                <div class="grid toolBar_span" id="toolBar">
                    <div class="grid-title">
                        <div class="text-center"> 
                            <div class="grid-title-text-2"><a href="#" ><i class="icon-cog "></i></a></div>
                        </div>
                    </div>
                    <div class="grid-content">
                        <p><button id="remove_layout_button" data-t="tooltip" title="Remover  Layout" class="btn btn-danger icon-alone toolbar_button " ><i class="icon-remove "></i></button></p>
                        <p><button id="fullscreen_button" class="btn btn-inverse icon-alone toolbar_button "  data-t="tooltip" title="Fullscreen"  ><i class="icon-fullscreen"></i></button></p>
                        <p><button id="linhas_button"  class=" btn btn-info diablog_opener icon-alone toolbar_button" onclick="selected_type_graph = 1;"  data-t="tooltip" title="Gráfico de Linhas"  ><i class="icon-picture "></i> </button></p>
                        <p><button id="barras_button" class ="btn btn-info diablog_opener icon-alone toolbar_button" onclick="selected_type_graph = 2;" data-t="tooltip" title="Gráfico de Barras"  ><i class="icon-bar-chart "></i></button></p>
                        <p><button id="pie_button" class="btn btn-info diablog_opener icon-alone toolbar_button" onclick="selected_type_graph = 3;" data-t="tooltip" title="Gráfico de Tarte"  > <i class="icon-adjust "></i></button></p>
                        <p><button id="graph_inbound" class="btn btn-info icon-alone diablog_opener_inbound toolbar_button" onclick="selected_type_graph = 4;" data-t="tooltip" title="Gráfico de Inbound"  > <i class="icon-list-alt "></i></button></p>
                    </div>
                </div>
            </div>


            <!-----------------------------------------------------------DIALOGS------------------------------------------------------------------------------>
            <!--DIALOG Da Layout, muda nome -->
            <div id="dialog_layout" title="Opções de Layout" style="display: none;" >
                <div id="table_name_layout">
                    <label class="label label-info">Nome</label>
                    <input id="Layout_Input_name" type="text" placeholder="Text input" />
                </div>
            </div>
            <!--DIALOG de confirmação de delete de layout-->
            <div id="dialog_delete" title="Confirmação" style="display: none;" >
                Tem a certeza que quer eliminar este Layout?
            </div>



            <!--DIALOG DOS WALLBOARDS-->
            <div id="dialog" title="Criação de Wallboard" style="display: none;">
                <label class="label label-info">Nome do Gráfico</label>
                <input id='graph_name' type='text'  />
                <label class="label label-info">Tempo de actualização:</label>
                <select id="update_time">
                    <option value="5000" >5 sec</option>
                    <option value="10000" selected>10 sec</option>
                    <option value="20000">20 sec</option>
                    <option value="40000">40 sec</option>   
                    <option value="60000" >1 min</option>
                    <option value="120000" >2 min</option>
                    <option value="360000" >5 min</option>
                </select> 



            </div>

        </div>









        <!--DIALOG Dataset->LINHAS-->
        <div id="dialog_dataset_linhas" title="Criação de dataset" style="display: none;">
            <table>
                <tr>
                    <td colspan="2">
                        <label class="label label-info">Tipo de dados</label>
                        <select id="in_out_bound">
                            <option value="1" >Inbound</option>
                            <option value="2" >Outbound</option>
                            <option value="3">Blended</option>
                        </select> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label label-success">Série:</label>
                        <select id="linhas_serie">
                            <option value="1" >user</option>
                            <option value="2" >User_group</option>
                            <option value="3">campanha</option>
                            <option value="4">total cc</option>   
                            <option value="5" >linha inbound</option>
                        </select> 
                    </td>
                    <td>
                        <label class="label label-info">Filtro:</label>
                        <select id="linhas_filtro">
                            <option value="1" >Chamadas</option>
                            <option value="2" >Feedback</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="gao_user"  class="graph_advance_option"></div>
                        <div id="gao_userGroup"  class="graph_advance_option" style="display: none;"></div>
                        <div id="gao_campaign" class="graph_advance_option" style="display: none;" ></div>
                        <div id="gao_inbound"  class="graph_advance_option" style="display: none;"></div>
                    </td>
                    <td>
                        <div id="gao_chamadas" class="option_filtro" ></div>
                        <div id="gao_status" class="option_filtro"  style="display: none;"></div>    
                    </td>
                </tr>
            </table>




        </div>


        <!--DIALOG DE INBOUND-->
        <div id="dialog_inbound" title="Criação de Wallboard Inbound" style="display: none;" >
            <label class="label label-info">Grupo:</label>
            <select id="group_inbound_select">
            </select> 
        </div>
        <!-----------------------------------------------------------DIALOGS------------------------------------------------------------------------------>





        <div id="jGrowl" class="top-right jGrowl" ><div class="jGrowl-notification" ></div></div>



        <script language="javascript">

                                    //aduarte@finesource.pt


//dataTable
// top 
//5 ou 10
//top man tem q ser maior
//status, tma, chamadas

                                    //--------------wbes
                                    //0      id,
                                    // 1       id_layout,
                                    //   2      name,
                                    //     3     pos_x, 
                                    //       4   pos_y,
                                    //       5    width, 
                                    //       6    height,
                                    //       7      update_time, 
                                    //       8      graph_type;
                                    //   9      Array[8]
                                    //0: id
                                    //1: id_wallboard
                                    //2: codigo_query
                                    //3: opcao_query
                                    //4: tempo
                                    //5: user
                                    //6: user_group
                                    //7: campaign_id
                                    //8:linha_inbound
                                    //9: mode
                                    //10: status_feedback
                                    //11: chamadas


//verificar todas as queries

//depois de apagar uma layout ainda aparece mensagesns


                                    var wbes = [];
                                    var layouts = [];
                                    //VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------
                                    var idLayout = 0;
                                    var window_slave;
                                    var selected_type_graph = 0;
                                    var query = "";
                                    var graph = [];
                                    var opcao_graph;
                                    var queries = [];
                                    var layouts = [];
                                    var id_wallboard;
                                    var id_dataset;
                                    var edit_dataset = false;

                                    //FIM---VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS-------FIM                 


                                    //-----------------FUNÇÕES DOS BUTTONS----------------------------
                                    $("#opcao_layout_button").click(function()
                                    {
                                        $("div.dialogButtons div button:nth-child(2)").text("gravar");//alterar texto do botao;
                                        $("#dialog_layout").dialog("open");
                                    });
                                    $("#add_layout_button").click(function()
                                    {
                                        sql_basic("insert_Layout", 0, 0);
                                        load_dados("layout", 0);
                                    });
                                    $("#remove_layout_button").click(function()
                                    {
                                        $('#dialog_delete').dialog('open');
                                    });
                                    $("#fullscreen_button").click(function()
                                    {
                                        fullScreen();
                                    });
                                    $("#linhas_serie").change(function()
                                    {
                                        $(".graph_advance_option").hide();
                                        switch ($("#linhas_serie").val())
                                        {
                                            case '1':
                                                $("#gao_user").show();
                                                break;
                                            case '2':
                                                $("#gao_userGroup").show();
                                                break;
                                            case '3':
                                                $("#gao_campaign").show();
                                                break;
                                            case '5':
                                                $("#gao_inbound").show();
                                                break;
                                        }
                                    });
                                    $("#linhas_filtro").change(function()
                                    {
                                        $(".option_filtro").hide();
                                        switch ($("#linhas_filtro").val())
                                        {
                                            case '1':
                                                $("#gao_chamadas").show();
                                                break;
                                            case '2':
                                                $("#gao_status").show();
                                                break;
                                        }
                                    });
                                    //-----------------FUNÇÕES DOS BUTTONS----------------------------



                                    // FUNÇÂO STARTER-----------------------------------------------------------------------------------------99999999----------------------------------------------------------------0000000000000000000000
                                    $(function() {
                                        $(document).on("click", ".delete_button", function(e) {

                                            var id = $(this).data("wbe_id");
                                            sql_basic('delete_WBE', 0, id);
                                            $("#" + id + "WBE").remove();
                                            load_dados('wbe', idLayout);
                                        });
                                        $(document).on("click", ".add_dataset_button", function(e) {
                                            $("div.dialogButtons div button:nth-child(2)").text("criar");//alterar texto do botao;                                 
                                            if (wbes[ get_indice_wbe($(this).data("id"))][9].length >= 5)
                                            {
                                                $.jGrowl('Capacidade de datasets por wallboard atingida (5 max).', {life: 6000});
                                            }
                                            else {
                                                id_wallboard = $(this).data("wbe_id");
                                                manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 1);
                                                $("#dialog_dataset_linhas").dialog("open");
                                            }
                                        });
                                        $(document).on("click", ".edit_dataset_button", function(e) {
                                            edit_dataset = true;
                                            id_dataset = $(this).data("dataset_id");
                                            id_wallboard = get_indice_wbe($(this).data("id"));
                                            $("div.dialogButtons div button:nth-child(2)").text("gravar alterações");//alterar texto do botao;
                                            var a = 0;

                                            $(".graph_advance_option").hide();
                                            $(".option_filtro").hide();

                                            $.each(wbes[id_wallboard][9], function(index, value)
                                            {
                                                if (wbes[id_wallboard][9][a].id == id_dataset)
                                                {
                                                    //Inbound,Outbound,Blended

                                                    if (wbes[id_wallboard][9][a].mode === "1")
                                                        $("#in_out_bound").val(1);
                                                    if (wbes[id_wallboard][9][a].mode === "2")
                                                        $("#in_out_bound").val(2);
                                                    if (wbes[id_wallboard][9][a].mode === "3")
                                                        $("#in_out_bound").val(3);
                                                    //PARAMETROS
                                                    if (wbes[id_wallboard][9][a].user != 0)
                                                    {
                                                        $("#linhas_serie").val(1);

                                                        $("#user").val(wbes[id_wallboard][9][a].user);
                                                    }
                                                    else if (wbes[id_wallboard][9][a].user_group != 0)
                                                    {
                                                        $("#gao_userGroup").show();
                                                        $("#linhas_serie").val(2);
                                                        $("#user_group").val(wbes[id_wallboard][9][a].user_group);
                                                    }
                                                    else if (wbes[id_wallboard][9][a].campaign_id != 0)
                                                    {
                                                        $("#gao_campaign").show();
                                                        $("#linhas_serie").val(3);
                                                        $("#campaign").val(wbes[id_wallboard][9][a].campaign_id);
                                                    }
                                                    else if (wbes[id_wallboard][9][a].linha_inbound != 0)
                                                    {
                                                        $("#gao_inbound").show();
                                                        $("#linhas_serie").val(5);
                                                        $("#inbound").val(wbes[id_wallboard][9][a].linha_inbound);
                                                    }
                                                    else
                                                        $("#linhas_serie").val(4);
//FILTRO
                                                    if (wbes[id_wallboard][9][a].status_feedback != 0)
                                                    {
                                                        $("#gao_status").show();
                                                        $("#linhas_filtro").val(2);
                                                        $("#status_venda").val(wbes[id_wallboard][9][a].status_feedback);
                                                    }
                                                    else if (wbes[id_wallboard][9][a].chamadas != 0)
                                                    {
                                                        $("#gao_chamadas").show();
                                                        $("#linhas_filtro").val(1);
                                                        if (wbes[id_wallboard][9][a].chamadas == "atendidas")
                                                            $("#chamadas").val(1);
                                                        if (wbes[id_wallboard][9][a].chamadas == "perdidas")
                                                            $("#chamadas").val(2);
                                                        if (wbes[id_wallboard][9][a].chamadas == "feitas")
                                                            $("#chamadas").val(3);

                                                    }
                                                }

                                                a++;
                                            });

                                            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 1);

                                            $("#dialog_dataset_linhas").dialog("open");

                                        });
                                        $(document).on("click", ".delete_dataset_button", function(e) {
                                            manipulate_dataset("remove_dataset", $(this).data("dataset_id"), 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

                                            load_dados("wbe", idLayout);
                                        });
                                        //inicia os tooltips
                                        $("[data-t=tooltip]").tooltip({placement: "left", html: true});
                                        $("[data-t=tooltip-right]").tooltip({placement: "right", html: true});
                                        //DIALOGS
                                        $("#dialog").dialog({
                                            dialogClass: 'dialogButtons',
                                            autoOpen: false,
                                            resizable: false,
                                            modal: true,
                                            buttons: {
                                                "Cancelar": function() {
                                                    $(this).dialog("close");
                                                },
                                                "Criar": function() {
                                                    //preencher o param1----------------FIM-----------------------------------FIM--------------------------------------FIM------------------------------------
                                                    manipulate_graph("insert_wbe", 0, $("#graph_name").val(), Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 250, 250, idLayout, $("#update_time").val(), selected_type_graph);

                                                    $(this).dialog("close");
                                                }
                                            }

                                        });
                                        $("#dialog_dataset_linhas").dialog({
                                            dialogClass: 'dialogButtons',
                                            autoOpen: false,
                                            resizable: false,
                                            width: 470,
                                            modal: true,
                                            buttons: {
                                                "Cancelar": function() {
                                                    $("div.dialogButtons div button:nth-child(2)").text("Criar");//alterar texto do botao;
                                                    $(this).dialog("close");
                                                },
                                                "Criar": function() {
                                                    function get_random_color() {
                                                        var letters = '0123456789ABCDEF'.split('');
                                                        var color = '#';
                                                        for (var i = 0; i < 6; i++) {
                                                            color += letters[Math.round(Math.random() * 15)];
                                                        }
                                                        return color;
                                                    }
                                                    var opcao = "insert_dataset";

                                                    if (edit_dataset) {
                                                        edit_dataset = false;
                                                        opcao = "edit_dataset";
                                                        $("div.dialogButtons div button:nth-child(2)").text("Criar");//alterar texto do botao;
                                                    }
                                                    else
                                                    {
                                                        opcao = "insert_dataset";
                                                    }
                                                    var querie = [];

                                                    if ($("#linhas_filtro").val() === "1")
                                                        switch ($("#linhas_serie").val())
                                                        {
                                                            case "1":

                                                                switch ($("#chamadas").val())
                                                                {
                                                                    case "1":
                                                                        //Chamadas atendidas por user
                                                                        querie = get_query(1);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "2":
                                                                        //Chamadas perdidas por user   
                                                                        querie = get_query(2);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "perdidas");
                                                                        break;
                                                                    case "3":
                                                                        //Chamadas feitas por user 
                                                                        querie = get_query(3);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "feitas");
                                                                        break;
                                                                }
                                                                break;
                                                            case "2":

                                                                switch ($("#chamadas").val())
                                                                {
                                                                    case "1":
                                                                        //Chamadas atendidas por user_group
                                                                        querie = get_query(4);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "2":
                                                                        //Chamadas perdidas por user_group   
                                                                        querie = get_query(5);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "perdidas");
                                                                        break;
                                                                    case "3":
                                                                        //Chamadas feitas por user_group 
                                                                        querie = get_query(6);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "feitas");
                                                                        break;
                                                                }
                                                                break;
                                                            case "3":
                                                                switch ($("#chamadas").val())
                                                                {
                                                                    case "1":
                                                                        //Chamadas atendidas por campanha
                                                                        querie = get_query(7);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "2":
                                                                        //Chamadas perdidas por campanha   
                                                                        querie = get_query(8);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "perdidas");
                                                                        break;
                                                                    case "3":
                                                                        //Chamadas feitas por campanha 
                                                                        querie = get_query(9);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "feitas");
                                                                        break;
                                                                }
                                                                break;
                                                            case "4":
                                                                switch ($("#chamadas").val())
                                                                {
                                                                    case "1":
                                                                        //Chamadas atendidas por Total CallCenter
                                                                        querie = get_query(10);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "2":
                                                                        //Chamadas perdidas por Total CallCenter  
                                                                        querie = get_query(11);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "perdidas");
                                                                        break;
                                                                    case "3":
                                                                        //Chamadas feitas por Total CallCenter 
                                                                        querie = get_query(12);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "feitas");
                                                                        break;
                                                                }
                                                                break;
                                                            case "5":
                                                                switch ($("#chamadas").val())
                                                                {
                                                                    case "1":
                                                                        //Chamadas atendidas por Inbound
                                                                        querie = get_query(18);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "2":
                                                                        //Chamadas perdidas por Inbound   
                                                                        querie = get_query(19);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                    case "3":
                                                                        //Chamadas feitas por Inbound 
                                                                        querie = get_query(20);
                                                                        manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas");
                                                                        break;
                                                                }
                                                                break;
                                                        }

                                                    if ($("#linhas_filtro").val() === "2")
                                                        switch ($("#linhas_serie").val())
                                                        {
                                                            case "1":
                                                                //feedback por user
                                                                querie = get_query(13);
                                                                manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0);
                                                                break;
                                                            case "2":
                                                                //feedback por user_group
                                                                querie = get_query(14);
                                                                manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0);
                                                                break;
                                                            case "3":
                                                                //feedback por campanha
                                                                querie = get_query(15);

                                                                manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), $("#status_venda").val(), 0);
                                                                break;
                                                            case "4":
                                                                //feedback por call center
                                                                querie = get_query(16);
                                                                manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0);
                                                                break;
                                                            case "5":
                                                                //feedback por linha inbound
                                                                querie = get_query(17);
                                                                manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), $("#status_venda").val(), 0);

                                                                break;
                                                        }

                                                    $(this).dialog("close");
                                                }
                                            }

                                        });
                                        $("#dialog_delete ").dialog({
                                            autoOpen: false,
                                            dialogClass: 'dialogButtons',
                                            resizable: false,
                                            height: 220,
                                            modal: true,
                                            buttons: {
                                                "Cancelar": function() {
                                                    $(this).dialog("close");
                                                },
                                                "Sim": function() {
                                                    sql_basic("remove_Layout", idLayout);
                                                    $(this).dialog("close");
                                                    load_dados("layout", 0);
                                                    var i = 0;
                                                    $.each(layouts, function(index, value)
                                                    {

                                                        if (layouts[i][0] === idLayout) {
                                                            layouts.splice(i, 1);
                                                        }
                                                        i++;
                                                    });
                                                }

                                            }
                                        });
                                        $("#dialog_layout").dialog({
                                            dialogClass: 'dialogButtons',
                                            autoOpen: false,
                                            resizable: false,
                                            height: 200,
                                            modal: true,
                                            buttons: {
                                                "Cancelar": function() {
                                                    $(this).dialog("close");
                                                },
                                                "Gravar": function() {

                                                    save_layout();
                                                    $(this).dialog("close");
                                                }
                                            }
                                        });
                                        $("#dialog_inbound").dialog({
                                            dialogClass: 'dialogButtons',
                                            autoOpen: false,
                                            resizable: false,
                                            height: 220,
                                            width: 310,
                                            modal: true,
                                            buttons: {
                                                "Cancelar": function() {
                                                    $(this).dialog("close");
                                                },
                                                "Criar": function() {
                                                    manipulate_graph("insert_wbe", 0, $("#graph_name").val(), Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 429, 242, idLayout, $("#group_inbound_select").val(), 4);
                                            
                                                    $(this).dialog("close");
                                                }
                                            }
                                        });
                                        $("div.dialogButtons div button:nth-child(1)").addClass("btn btn"); //classe dos botoes das dialogs
                                        $("div.dialogButtons div button:nth-child(2)").addClass("btn btn-primary");
                                        $(".diablog_opener").click(function() {
                                            dialog_opener();
                                            $("#dialog").dialog("open");
                                        });
                                        $(".diablog_opener_inbound").click(function() {
                                            dialog_opener();
                                            $("#dialog_inbound").dialog("open");
                                        });
                                        //loada da dropbox com os indices e do painel do layout
                                        load_dados("layout", 0);
                                        //para fazer load das options extra
                                        flot_extra_init();
                                    });
                                    // FUNÇÂO STARTER--------------------------------------------------------------------------------------------99999999------------------------------------------------------------- 00000000000000000000 



                                    //"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS
                                    function get_query(codigo)
                                    {
                                        var i = 0;
                                        var querie;
                                        $.each(queries, function(index, value) {

                                            if (queries[i][6] == codigo)
                                            {

                                                querie = queries[i];
                                                return false;
                                            }
                                            i++;
                                        });
                                        return querie;
                                    }

                                    function dialog_opener()
                                    {
                                        $("div.dialogButtons div button:nth-child(2)").text("Criar");//alterar texto do botao;                  
                                        $("#graph_name").val("Gráfico Novo");
                                        $(".graph_advance_option").hide(); //esconde a classe e so mostra por id(em baixo)

                                        //linhas

                                        if (selected_type_graph === 1) {
                                            $("#gao_user").show();
                                            $("#gao_chamadas").show();
                                        }
                                        //bar
                                        if (selected_type_graph === 2) {
                                            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                            $("#gao_status").show();
                                            $("#gao_userGroup").show();
                                        }
                                        //pie
                                        if (selected_type_graph === 3) {
                                            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                            $("#gao_campaign").show();
                                        }
                                        //inbound
                                        if (selected_type_graph === 4) {
                                            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                        }
                                    }

                                    function fullScreen()
                                    {
                                        window_slave = window.open("/sips-admin/wallboard1/full_screen_render.php", idLayout + ";" + $("#MainLayout").width() + ";" + $("#MainLayout").height());
                                    }

                                    function check_save()
                                    {
                                        save();
                                    }

                                    function save_layout()
                                    {
                                        var a = get_indice_layout(idLayout);
                                        layouts[a][1] = $("#Layout_Input_name").val();
                                        manipulate_dados("edit_Layout", idLayout, layouts[a][1], 0, 0, 0, 0, 0, 0);
                                        update_dropbox_layout();
                                        $('#label_id_layout').text(layouts[a][0]);
                                        $("#Layout_Input_name").val(layouts[a][1]);
                                    }

                                    function save()//guarda tuto
                                    {
                                        var a = get_indice_layout(idLayout);
                                        for (var i = 0; i < wbes.length; i++)
                                        {
                                            //para fazer update a 1 elemento do wallboard, actualiza no array,nas labels de informação e na Base dadoss
                                            var painel = $("#" + wbes[i][0] + "WBE");
                                            wbes[i][1] = painel.css("left").replace("px", "");
                                            wbes[i][2] = painel.css("top").replace("px", "");
                                            wbes[i][3] = painel.width();
                                            wbes[i][4] = painel.height();
                                            manipulate_dados("edit_WBE", wbes[i][0], 0, wbes[i][1], wbes[i][2], wbes[i][3], wbes[i][4], wbes[i][6], 0);
                                        }
                                        update_dropbox_layout();
                                        $('#label_id_layout').text(layouts[a][0]);
                                        $("#Layout_Input_name").val(layouts[a][1]);
                                    }

                                    function get_indice_layout(id)//pega no id do WallBoardElement e passa para indice de array
                                    {
                                        var i = 0;
                                        var indice = 0;
                                        $.each(layouts, function(index, value) {
                                            if (layouts[i][0] == id) {
                                                indice = i;
                                            }
                                            i++;
                                        });
                                        return indice;
                                    }
                                    function get_indice_wbe(id)//pega no id do WallBoardElement e passa para indice de array
                                    {
                                        var i = 0;
                                        var indice = 0;
                                        $.each(wbes, function(index, value) {
                                            if (wbes[i][0] == id) {
                                                indice = i;
                                            }
                                            i++;
                                        });
                                        return indice;
                                    }

                                    function layout_change()
                                    {
                                        $("#MainLayout .PanelWB").remove();
                                        idLayout = $('#LayoutSelector').val();
                                        var a = get_indice_layout(idLayout);
                                        $('#label_id_layout').text(layouts[a][0]);
                                        $("#Layout_Input_name").val(layouts[a][1]);
                                        load_dados('wbe', idLayout);
                                    }

                                    function update_wbe()
                                    {
                                        $(".PanelWB").remove();
                                        var i = 0;
                                        //--------------wbes
                                        //0      id,
                                        // 1       id_layout,
                                        //   2      name,
                                        //     3     pos_x, 
                                        //       4   pos_y,
                                        //       5    width, 
                                        //       6    height,
                                        //       7      update_time, 
                                        //       8      graph_type;
                                        //   9      Array[8]
                                        //0: id
                                        //1: id_wallboard
                                        //2: codigo_query
                                        //3: opcao_query
                                        //4: tempo
                                        //5: user
                                        //6: user_group
                                        //7: campaign_id
                                        //8:linha_inbound
                                        //9: mode
                                        //10: status_feedback
                                        //11: chamadas
                                        $.each(wbes, function(index, value) {
                                            var ml = $("#MainLayout");

                                            if (wbes[i][8] !== "4") {//Todos menos Inbound
                                                ml.append($("<div>").addClass("PanelWB ui-widget-content").attr("id", wbes[i][0] + "WBE")
                                                        .css("left", wbes[i][3] + "px")
                                                        .css("top", wbes[i][4] + "px")
                                                        .css("width", wbes[i][5] + "px")
                                                        .css("height", wbes[i][6] + "px")
                                                        .append($("<div>").addClass("grid-title")
                                                        .append($("<div>").addClass("pull-left")
                                                        .text(wbes[i][2]))
                                                        .append($("<div>").addClass("pull-right")
                                                        .append($("<button>").addClass("btn icon-alone btn-info add_dataset_button").data("wbe_id", wbes[i][0]).attr("data-t", "tooltip").attr("title", "Adicionar dataset")
                                                        .append($("<i>").addClass("icon-plus-sign")))
                                                        .append($("<button>").addClass("btn icon-alone btn-danger delete_button").data("wbe_id", wbes[i][0]).attr("data-t", "tooltip").attr("title", "Remover Wallboard")
                                                        .append($("<i>").addClass("icon-remove")))
                                                        ))
                                                        .append($("<div>").addClass("grid-content").attr("id", "grid_content")));
                                                var banana = $("#" + wbes[i][0] + "WBE #grid_content");
                                                var a = 0;
                                                $.each(wbes[i][9], function(index, value) {

                                                    banana.append($("<div>")
                                                            .append($("<div>").addClass("btn-group")
                                                            .append($("<label>").addClass("btn btn-mini dropdown-toggle icon-cog").attr("data-toggle", "dropdown").text(" " + wbes[i][9][a].opcao_query).append($("<span>").addClass("caret")))
                                                            .append($("<div>").addClass("dropdown-menu")
                                                            .append($("<ul>")
                                                            .append($("<li>")
                                                            .append($("<a>").attr("href", "#").addClass("edit_dataset_button").data("dataset_id", wbes[i][9][a].id).data("id", wbes[i][0])
                                                            .append($("<i>").addClass("icon-edit").text(" Editar dataset"))))
                                                            .append($("<li>")
                                                            .append($("<a>").attr("href", "#").addClass("delete_dataset_button").data("dataset_id", wbes[i][9][a].id)
                                                            .append($("<i>").addClass("icon-trash").text(" Eliminar dataset"))))
                                                            ))));


                                                    a++;
                                                });
                                            }
                                            else//Inbound
                                            {
                                                ml.append($("<div>").addClass("PanelWB ui-widget-content").attr("id", wbes[i][0] + "WBE")
                                                        .css("left", wbes[i][3] + "px")
                                                        .css("top", wbes[i][4] + "px")
                                                        .css("width", wbes[i][5] + "px")
                                                        .css("height", wbes[i][6] + "px")
                                                        .append($("<div>").addClass("grid-title")
                                                        .append($("<div>").addClass("pull-left")
                                                        .text(wbes[i][2]))
                                                        .append($("<div>").addClass("pull-right")
                                             
                                                        .append($("<button>").addClass("btn icon-alone btn-danger delete_button").data("wbe_id", wbes[i][0]).attr("data-t", "tooltip").attr("title", "Remover Wallboard")
                                                        .append($("<i>").addClass("icon-remove")))
                                                        ))
                                                        .append($("<div>").addClass("grid-content").attr("id", "grid_content")));

                                            }




                                            var painel = $("#MainLayout  #" + wbes[i][0] + "WBE");
                                            painel.draggable({containment: "#MainLayout", stop: check_save});
                                            if (wbes[i][8] == "4") {
                                                painel.resizable({
                                                    containment: "#MainLayout",
                                                    aspectRatio: 16 / 9,
                                                    maxHeight: 480,
                                                    maxWidth: 880,
                                                    minHeight: 240,
                                                    minWidth: 245, stop: check_save});
                                            } else
                                            {

                                                painel.resizable({
                                                    containment: "#MainLayout",
                                                    maxHeight: 480,
                                                    maxWidth: 880,
                                                    minHeight: 240,
                                                    minWidth: 245, stop: check_save});
                                            }
                                            i++;
                                        }

                                        );
                                    }

                                    function update_dropbox_layout()
                                    {
                                        var layoutSelector = $('#LayoutSelector');
                                        layoutSelector.empty();
                                        var i = 0;
                                        $.each(layouts, function(index, value) {

                                            if (layouts[i][0] == idLayout)
                                                layoutSelector.append("<option selected value=" + layouts[i][0] + ">" + layouts[i][1] + "</option>");
                                            else
                                                layoutSelector.append("<option value=" + layouts[i][0] + ">" + layouts[i][1] + "</option>");
                                            i++;
                                        });
                                    }
                                    //"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS"""""""""""""""""FUNÇÔES BASICAS

                                    //---Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados---

                                    function manipulate_dataset(Opcao, Id, id_Wallboard, Codigo_query, Opcao_query, Tempo, User, User_group, Campaign_id, Linha_inbound, Mode, Status_feedback, Chamadas)
                                    {
                                        $.post("Requests.php", {action: Opcao, id: Id, id_wallboard: id_Wallboard, codigo_query: Codigo_query, opcao_query: Opcao_query, tempo: Tempo, user: User, user_group: User_group, campaign_id: Campaign_id, linha_inbound: Linha_inbound, mode: Mode, status_feedback: Status_feedback, chamadas: Chamadas},
                                        function(data)
                                        {
                                            if (Opcao === "insert_dataset")
                                            {
                                                load_dados("wbe", idLayout);
                                            }
                                            if (Opcao === "edit_dataset")
                                            {
                                                load_dados("wbe", idLayout);
                                            }

                                        }, "json");
                                    }
                                    function load_dados(opcao, id_layouT)
                                    {
                                        $.post("Requests.php", {action: opcao, id_layout: id_layouT},
                                        function(data)
                                        {
                                            if (data === null)
                                            {
                                                $("#MainLayout .PanelWB").remove();
                                                wbes = []; //limpa o array dos wallboards, senao dps de se passar de uma layout com elementos para esta, como o wbes ainda contem os elementos do layout anterior, eles sao criados outra vez

                                                if (layouts.length <= 0)//se n ha layout, bloqueia os botoes e limpa a dropbox
                                                {
                                                    $('#LayoutSelector').empty();
                                                    $("#toolBar .toolbar_button").prop("disabled", true);
                                                    $.jGrowl('Layout inexistente', {life: 6000});
                                                }
                                                else
                                                {
                                                    $.jGrowl('Wallboard inexistente');
                                                    $("#toolBar .toolbar_button").prop("disabled", false);
                                                }
                                                return false;
                                            }
                                            if (opcao === "layout")//Load dados layout
                                            {
                                                layouts = [];
                                                $.each(data, function(index, value) {
                                                    layouts.push([this.id, this.name]);
                                                });
                                                $("#MainLayout").empty();
                                                update_dropbox_layout();
                                                layout_change();
                                            }
                                            if (opcao === 'wbe')//load dados WBElement
                                            {
                                                wbes = [];
                                                $.each(data, function(index, value) {
                                                    wbes.push([this.id, this.id_layout, this.name, this.pos_x, this.pos_y, this.width, this.height, this.update_time, this.graph_type, this.dataset]);
                                                });
                                                update_wbe();
                                            }
                                        }, "json");
                                    }
                                    function sql_basic(opcao, id_layout, id_wbe)
                                    {
                                        $.post("Requests.php", {action: opcao, id_layout: id_layout, id: id_wbe},
                                        function(data)
                                        {
                                            if (opcao === "insert_Layout")
                                            {
                                                load_dados("layout", 0);
                                                idLayout = $("#LayoutSelector").val();
                                                layout_change();
                                            }

                                            if (opcao === "remove_Layout")
                                            {
                                                load_dados('layout', 0);
                                                layout_change();
                                                var i = get_indice_layout(idLayout);
                                                $('#label_id_layout').text(layouts[i][0]);
                                                $("#Layout_Input_name").val(layouts[i][1]);
                                                load_dados('layout', 0);
                                                layout_change();
                                                $(this).dialog("close");
                                            }
                                        }, "json");
                                    }
                                    //wallboard
                                    function manipulate_dados(opcao, Id, Name, Pos_x, Pos_y, Width, Height, id_layout)
                                    {
                                        $.post("Requests.php", {action: opcao, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_layout},
                                        function(data)
                                        {
                                        }, "json");
                                    }
                                    //graphs
                                    function manipulate_graph(Opcao, Id, Name, Pos_x, Pos_y, Width, Height, id_Layout, Update_time, Graph_type)
                                    {

                                        $.post("Requests.php", {action: Opcao, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_Layout, update_time: Update_time, graph_type: Graph_type},
                                        function(data)
                                        {
                                            if (Opcao === 'get_query')
                                            {
                                                $.each(data, function(index, value) {
                                                    queries.push([this.id, this.query_text_inbound, this.query_text_outbound, this.query_text_blended, this.opcao_query, this.type_query, this.codigo]);
                                                });

                                            }

                                            if (Opcao === "insert_wbe")
                                                load_dados("wbe", idLayout);

                                        }, "json");
                                    }
                                    //---Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados---

                                    //FLOT EXTRA HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHh
                                    function flot_extra_init()
                                    {




                                        var gao_u = $("#gao_user");
                                        var gao_ug = $("#gao_userGroup");
                                        var gao_c = $("#gao_campaign");
                                        var gao_total_cc = $("#gao_total_cc");
                                        var gao_i = $("#gao_inbound");
                                        var gao_sv = $("#gao_status");
                                        var gao_chamadas = $("#gao_chamadas");
                                        gao_u.append("<label class='label label-success'>Users</label");
                                        gao_u.append("<select id='user' > </select> ");
                                        flot_extra("user");
                                        gao_ug.append("<label class='label label-success'>Grupos</label");
                                        gao_ug.append("<select id='user_group' > </select> ");
                                        flot_extra("user_group");
                                        gao_c.append("<label class='label label-success'>Campanha</label");
                                        gao_c.append("<select id='campaign' > </select> ");
                                        flot_extra("campaign");
                                        gao_total_cc.append("<label class='label label-success'>total_cc</label");
                                        gao_total_cc.append("<select id='total_cc' > </select> ");
                                        gao_i.append("<label class='label label-success'>Inbound</label");
                                        gao_i.append("<select id='inbound' > </select> ");
                                        flot_extra("inbound");
                                        gao_sv.append("<label class='label label-info'>Feedback</label");
                                        gao_sv.append("<select id='status_venda'> </select> ");
                                        flot_extra("status_venda");
                                        gao_chamadas.append("<label class='label label-info'>Chamadas</label");
                                        gao_chamadas.append($("<select>").attr("id", "chamadas")
                                                .append("<option value='1'>atendidas</option> ")
                                                .append("<option value='2'>perdidas</option> ")
                                                .append("<option value='3'>feitas</option>"));
                                    }
                                    function flot_extra(opcao)
                                    {
                                        var time_span = $("#time_span");
                                        var Param1 = "1 day";
                                        if (time_span.val() == 1)
                                            Param1 = "1 hour";
                                        if (time_span.val() == 2)
                                            Param1 = "1 day";
                                        if (time_span.val() == 3)
                                            Param1 = "12 hour";
                                        $.post("Requests.php", {action: opcao, param1: Param1},
                                        function(data)
                                        {
                                            if (opcao === "user")
                                            {
                                                $("#user").empty();
                                                var db = document.getElementById("user");
                                                $.each(data, function(index, value) {
                                                    var opt1 = document.createElement("option");
                                                    opt1.value = value;
                                                    opt1.text = value;
                                                    db.options.add(opt1);
                                                });
                                            }
                                            if (opcao === "user_group")
                                            {
                                                $("#user_group").empty();
                                                var db = document.getElementById("user_group");
                                                $.each(data, function(index, value) {
                                                    var opt1 = document.createElement("option");
                                                    opt1.value = value;
                                                    opt1.text = value;
                                                    db.options.add(opt1);
                                                });
                                            }
                                            if (opcao === "campaign")
                                            {
                                                var db = document.getElementById("campaign");
                                                $.each(data, function(index, value) {
                                                    var opt1 = document.createElement("option");
                                                    opt1.value = this.campaign_id;
                                                    opt1.text = this.campaign_name;
                                                    db.options.add(opt1);
                                                });
                                            }
                                            if (opcao === "status_venda")
                                            {
                                                var db = document.getElementById("status_venda");
                                                $.each(data, function(index, value) {
                                                    var opt1 = document.createElement("option");
                                                    opt1.value = this.status_v;
                                                    opt1.text = this.status_t;
                                                    db.options.add(opt1);
                                                });
                                            }


                                            if (opcao === "inbound")
                                            {
                                                var db = document.getElementById("group_inbound_select");
                                                $.each(data, function(index, value) {
                                                    var opt1 = document.createElement("option");
                                                    opt1.value = this.id;
                                                    opt1.text = this.name;
                                                    db.options.add(opt1);
                                                });
                                            }
                                        }, "json");
                                    }
                                    //FLOT EXTRA HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHh

        </script>
    </body>
</html>