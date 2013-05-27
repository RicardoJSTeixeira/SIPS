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
                    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
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

                              .bla{
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
                                                                      <div class="input-prepend input-append"><span class="add-on">Layout</span>
                                                                                <select id="LayoutSelector" onchange="layout_change();"></select> 
                                                                                <button data-t="tooltip-right" title="Opções da Layout"  class="bla btn btn-warning icon-alone" id="opcao_layout_button"><i class="icon-cogs"></i></button>
                                                                      </div>
                                                            </div>
                                                  </div>

                                                  <div class="grid-content ">
                                                            <div  style="position:relative;width:904px;height:512px; margin: 0 auto;">
                                                                      <div id="MainLayout" class="ui-widget-header "  ></div>
                                                            </div>
                                                            <div class="clear"></div>
                                                  </div>
                                        </div>  



                                        <div class="grid toolBar_span">
                                                  <div class="grid-title">
                                                            <div class="text-center"> 
                                                                      <div class="grid-title-text-2"><a href="#" ><i class="icon-cog "></i></a></div>
                                                            </div>
                                                  </div>
                                                  <div class="grid-content">

                                                            <p><button id="remove_layout_button" data-t="tooltip" title="Remover  Layout" class="btn btn-danger icon-alone " ><i class="icon-remove "></i></button></p>
                                                            <p><button  id="save_button" data-t="tooltip" title="Gravar layout e elementos" class="btn btn-success  icon-alone  "  ><i class="icon-save"></i></button></p>
                                                            <p><button id="fullscreen_button" class="btn btn-inverse icon-alone"  data-t="tooltip" title="Fullscreen"  ><i class="icon-fullscreen"></i></button></p>
                                                            <p><button  class=" btn btn-info diablog_opener icon-alone" onclick="selected_type_graph = 1;"  data-t="tooltip" title="Gráfico de Linhas"  ><i class="icon-picture "></i> </button></p>
                                                            <p><button class ="btn btn-info diablog_opener icon-alone" onclick="selected_type_graph = 2;" data-t="tooltip" title="Gráfico de Barras"  ><i class="icon-bar-chart "></i></button></p>
                                                            <p><button class="btn btn-info diablog_opener icon-alone" onclick="selected_type_graph = 3;" data-t="tooltip" title="Gráfico de Tarte"  > <i class="icon-adjust "></i></button></p>
                                                            <p><button id="graph_inbound" class="btn btn-info icon-alone diablog_opener_inbound" onclick="selected_type_graph = 4;" data-t="tooltip" title="Gráfico de Inbound"  > <i class="icon-list-alt "></i></button></p>
                                                  </div>
                                        </div>
                              </div>


                              <!-----------------------------------------------------------DIALOGS------------------------------------------------------------------------------>
                              <!--DIALOG Da Layout, muda nome e cor de background-->
                              <div id="dialog_layout" title="Opções de Layout" >
                                        <div id="table_name_layout">
                                                  <form class="form-inline">

                                                            <label class="label label-info">ID</label>
                                                            <label id="label_layout_id"></label>
                                                            <br>
                                                            <label class="label label-info">Nome</label>
                                                            <input id="Layout_Input_name" type="text" placeholder="Text input" />
                                                  </form>
                                        </div>
                              </div>
                              <!--DIALOG de confirmação de delete de layout-->
                              <div id="dialog_delete" title="Confirmação" >
                                        Tem a certeza que quer eliminar este Layout?
                              </div>
                              <!--DIALOG DOS WALLBOARDS-->
                              <div id="dialog" title="Criação de Wallboard" >
                                        <label class="label label-info">Nome do Gráfico</label>
                                        <input id='graph_name' type='text' value="Gráfico xpto" />
                                        <label class="label label-info">Opções</label>
                                        <select id="query_type" >            </select>
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
                                        <label class="label label-info">Data de Inicio</label>
                                        <select id="time_span" onchange="flot_extra('user_group');">
                                                  <option value="1" selected>Ultima Hora</option>
                                                  <option value="2" >Dia-a-Dia</option>
                                                  <option value="3" >Ultimas 12h</option>
                                        </select> 
                                        <div id="gao_userGroup"  class="graph_advance_option"></div>
                                        <div id="gao_status" class="graph_advance_option" ></div>
                                        <div id="gao_campaign" class="graph_advance_option" ></div>


                              </div>
                              <!--DIALOG DE INBOUND-->
                              <div id="dialog_inbound" title="Criação de Wallboard Inbound" >
                                        <label class="label label-info">Grupo:</label>
                                        <select id="group_inbound_select">
                                        </select> 
                              </div>
                              <!-----------------------------------------------------------DIALOGS------------------------------------------------------------------------------>







                    </div>

                    <div id="jGrowl" class="top-right jGrowl" ><div class="jGrowl-notification" ></div></div>



                    <script language="javascript">



                                                                                          var wbes = []; //wall board elements em array
                                                                                          var layouts = [];





                                                                                          //real time graph ainda n testado 100%
                                                                                          //to be looked at

                                                                                          //real time vai actualiza os dados todos e nao só o ultimo
                                                                                          ////pode n ser o mais leve, mas talvez seja o mais eficaz

                                                                                          //STARTER!
                                                                                          //http://sipsam.dyndns.org/sips-admin/wallboard/




                                                                                          //inbound wallboard
                                                                                          //(AM servidor)
                                                                                          //_> vicidial_closer log
                                                                                          //-> auto_calls
                                                                                          //dragable sem overlap ->http://sourceforge.net/p/jquidragcollide/wiki/Home/#jquery-ui-draggable-collision



                                                                                          //  http://sipsam.dyndns.org:20000/mysqladmin
                                                                                          //sipsadmin 
                                                                                          //sipsadmin2012

                                                                                          //VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------
                                                                                          var idLayout = 0;
                                                                                          var window_slave;
                                                                                          var selected_type_graph = 0;
                                                                                          var query = "";
                                                                                          var graph = [];
                                                                                          var opcao_graph;
                                                                                          var queries = [];
                                                                                          //FIM---VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS--------VARIAVEIS GERAIS-----------------VARIAVEIS GERAIS-------FIM                 



                                                                                          //-----------------FUNÇÕES DOS BUTTONS----------------------------
                                                                                          $("#opcao_layout_button").click(function()
                                                                                          {
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
                                                                                          $("#save_button").click(function()
                                                                                          {
                                                                                                    save();
                                                                                          });
                                                                                          $("#fullscreen_button").click(function()
                                                                                          {
                                                                                                    fullScreen();
                                                                                          });
                                                                                          //-----------------FUNÇÕES DOS BUTTONS----------------------------



                                                                                          // FUNÇÂO STARTER-----------------------------------------------------------------------------------------99999999----------------------------------------------------------------0000000000000000000000
                                                                                          $(function() {

                                                                                                    $(document).on("click", ".delete_button", function(e) {
                                                                                                              var id = $(this).data("wbe_id");
                                                                                                              sql_basic('delete_WBE', 0, id);
                                                                                                              $("#" + id + "WBE").remove();
                                                                                                              load_dados('wbe', idLayout);

                                                                                                    });//inicia o botao de delete de todos os wallBoards

                                                                                                    //inicia os tooltips
                                                                                                    $("[data-t=tooltip]").tooltip({placement: "left", html: true});
                                                                                                    $("[data-t=tooltip-right]").tooltip({placement: "right", html: true});

                                                                                                    //DIALOGS
                                                                                                    $("#dialog_delete ").dialog({
                                                                                                              autoOpen: false,
                                                                                                              dialogClass: 'dialogButtons',
                                                                                                              resizable: false,
                                                                                                              height: 220,
                                                                                                              modal: true,
                                                                                                              buttons: {
                                                                                                                        "Sim": function() {
                                                                                                                                  sql_basic("remove_Layout", idLayout);
                                                                                                                                  $(this).dialog("close");
                                                                                                                                  load_dados("layout", 0);
                                                                                                                        },
                                                                                                                        "Cancelar": function() {
                                                                                                                                  $(this).dialog("close");
                                                                                                                        }
                                                                                                              }
                                                                                                    });
                                                                                                    $("#dialog").dialog({
                                                                                                              dialogClass: 'dialogButtons',
                                                                                                              autoOpen: false,
                                                                                                              resizable: false,
                                                                                                              modal: true,
                                                                                                              buttons: {
                                                                                                                        "Criar": function() {
                                                                                                                                  var i = 0;
                                                                                                                                  $.each(queries, function(index, value) {
                                                                                                                                            if (queries[i][0] == $("#query_type").val())
                                                                                                                                            {
                                                                                                                                                      query = queries[i][1];
                                                                                                                                                      opcao_graph = queries[i][2];
                                                                                                                                            }

                                                                                                                                            i++;
                                                                                                                                  });
                                                                                                                                  var param1 = "";
                                                                                                                                  //preencher o param1-----------------------------------------------------------------------------------------------------------------------
                                                                                                                                  if (selected_type_graph == 1)
                                                                                                                                  {
                                                                                                                                  }
                                                                                                                                  if (selected_type_graph == 2)
                                                                                                                                  {
                                                                                                                                            if ($('#user_group').val() != 1)
                                                                                                                                                      param1 += ('and user_group="' + $('#user_group').val() + '"');
                                                                                                                                            if ($('#status_venda').val() != 1)
                                                                                                                                                      param1 += ('and vl.status="' + $('#status_venda').val() + '"');
                                                                                                                                  }
                                                                                                                                  if (selected_type_graph == 3)
                                                                                                                                  {
                                                                                                                                            param1 = ('campaign_id="' + $('#campaign_id').val() + '"');
                                                                                                                                  }

                                                                                                                                  query = query.replace("$param1", param1);

                                                                                                                                  time_span = $('#time_span');
                                                                                                                                  if (time_span.val() == 1)
                                                                                                                                            query = query.replace("INTERVAL 1 Day", "INTERVAL 1 Hour");
                                                                                                                                  if (time_span.val() == 2)
                                                                                                                                            query = query.replace("INTERVAL 1 Day", "INTERVAL 1 Day");
                                                                                                                                  if (time_span.val() == 3)
                                                                                                                                            query = query.replace("INTERVAL 1 Day", "INTERVAL 12 Hour");
                                                                                                                                  //preencher o param1----------------FIM-----------------------------------FIM--------------------------------------FIM------------------------------------



                                                                                                                                  manipulate_graph("insert_wbe", 0, $("#graph_name").val(), Math.floor((Math.random() * 300) + 1), Math.floor((Math.random() * 300) + 1), 250, 250, idLayout, query, opcao_graph, $("#update_time").val(), selected_type_graph);
                                                                                                                                  load_dados("wbe", idLayout);

                                                                                                                                  $(this).dialog("close");
                                                                                                                        }
                                                                                                                        ,
                                                                                                                        "Cancelar"
                                                                                                                                : function() {
                                                                                                                                  $(this).dialog("close");
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
                                                                                                                        "Gravar": function() {

                                                                                                                                  save();
                                                                                                                                  $(this).dialog("close");
                                                                                                                        },
                                                                                                                        "Cancelar": function() {
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
                                                                                                                        "Criar": function() {
                                                                                                                                  var i = 0;

                                                                                                                                  $.each(queries, function(index, value) {

                                                                                                                                            if (queries[i][3] == 4)
                                                                                                                                            {
                                                                                                                                                      query = queries[i][1] + ' where campaign_id ="' + $("#group_inbound_select").val() + '"';
                                                                                                                                                      opcao_graph = queries[i][2];
                                                                                                                                            }

                                                                                                                                            i++;
                                                                                                                                  });


                                                                                                                                  //o group_id vai no nome do inbound
                                                                                                                                  manipulate_graph("insert_wbe", 0, $("#group_inbound_select option:selected").text(), Math.floor((Math.random() * 300) + 1), Math.floor((Math.random() * 300) + 1), 250, 250, idLayout, query, $("#group_inbound_select").val(), 10000, selected_type_graph);

                                                                                                                                  load_dados("wbe", idLayout);
                                                                                                                                  $(this).dialog("close");
                                                                                                                        },
                                                                                                                        "Cancelar": function() {
                                                                                                                                  $(this).dialog("close");
                                                                                                                        }
                                                                                                              }
                                                                                                    });
                                                                                                    $("div.dialogButtons div button:nth-child(1)").addClass("btn btn-info");//classe dos botoes das dialogs
                                                                                                    $("div.dialogButtons div button:nth-child(2)").addClass("btn btn-info");
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
                                                                                          function dialog_opener()
                                                                                          {
                                                                                                    //linhas
                                                                                                    if (selected_type_graph === 1) {
                                                                                                              manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                                                                                              $(".graph_advance_option").hide();
                                                                                                    }
                                                                                                    //bar
                                                                                                    if (selected_type_graph === 2) {
                                                                                                              manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                                                                                              $(".graph_advance_option").hide();
                                                                                                              $("#gao_status").show();
                                                                                                              $("#gao_userGroup").show();
                                                                                                    }
                                                                                                    //pie
                                                                                                    if (selected_type_graph === 3) {
                                                                                                              manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                                                                                              $(".graph_advance_option").hide();
                                                                                                              $("#gao_campaign").show();
                                                                                                    }
                                                                                                    //inbound
                                                                                                    if (selected_type_graph === 4) {
                                                                                                              manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph);
                                                                                                    }
                                                                                          }

                                                                                          function fullScreen()
                                                                                          {
                                                                                                    window_slave = window.open("/sips-admin/wallboard/full_screen_render.php", idLayout + ";" + $("#MainLayout").width() + ";" + $("#MainLayout").height(), ' fullscreen=yes');
                                                                                          }

                                                                                          function save()//guarda tuto
                                                                                          {
                                                                                                    var a = get_indice_layout(idLayout);
                                                                                                    layouts[a][1] = $("#Layout_Input_name").val();
                                                                                                    manipulate_dados("edit_Layout", idLayout, layouts[a][1], 0, 0, 0, 0, 0, 0);
                                                                                                    for (var i = 0; i < wbes.length; i++)
                                                                                                    {
                                                                                                              //para fazer update a 1 elemento do wallboard, actualiza no array,nas labels de informação e na Base dadoss
                                                                                                              var painel = $("#" + wbes[i][0] + "WBE");
                                                                                                              wbes[i][1] = painel.css("left").replace("px", "");
                                                                                                              wbes[i][2] = painel.css("top").replace("px", "");
                                                                                                              wbes[i][3] = painel.width();
                                                                                                              wbes[i][4] = painel.height();
                                                                                                              wbes[i][5] = idLayout;
                                                                                                              manipulate_dados("edit_WBE", wbes[i][0], 0, wbes[i][1], wbes[i][2], wbes[i][3], wbes[i][4], wbes[i][5], wbes[i][6], 0);
                                                                                                    }
                                                                                                    update_dropbox_layout();
                                                                                                    $('#label_layout_id').text(layouts[a][0]);
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

                                                                                          function layout_change()
                                                                                          {
                                                                                                    $(".PanelWB").remove();
                                                                                                    idLayout = $('#LayoutSelector').val();
                                                                                                    var a = get_indice_layout(idLayout);
                                                                                                    $('#label_layout_id').text(layouts[a][0]);
                                                                                                    $("#Layout_Input_name").val(layouts[a][1]);
                                                                                                    load_dados('wbe', idLayout);
                                                                                          }

                                                                                          function update_wbe()
                                                                                          {
                                                                                                    $(".PanelWB").remove();
                                                                                                    var i = 0;
                                                                                                    //      wbes
                                                                                                    //0      id,
                                                                                                    // 1       name,
                                                                                                    //   2      posX,
                                                                                                    //     3     posY, 
                                                                                                    //       4   width,
                                                                                                    //       5    height, 
                                                                                                    //       6    layout_Id,
                                                                                                    //      7      query_text, 
                                                                                                    //       8     opcao_query,
                                                                                                    //       9      update_time, 
                                                                                                    //       10      graph_type]);
                                                                                                    $.each(wbes, function(index, value) {
                                                                                                              var ml = $("#MainLayout");
                                                                                                              ml.append($("<div>").addClass("PanelWB ui-widget-content").attr("id", wbes[i][0] + "WBE")
                                                                                                                      .css("left", wbes[i][2] + "px")
                                                                                                                      .css("top", wbes[i][3] + "px")
                                                                                                                      .css("width", wbes[i][4] + "px")
                                                                                                                      .css("height", wbes[i][5] + "px")
                                                                                                                      .append($("<div>").addClass("grid-title")
                                                                                                                      .append($("<div>").addClass("pull-left")
                                                                                                                      .text(wbes[i][1]))
                                                                                                                      .append($("<div>").addClass("pull-right")
                                                                                                                      .append($("<button>").addClass("btn icon-alone btn-danger delete_button").data("wbe_id", wbes[i][0])
                                                                                                                      .append($("<i>").addClass("icon-remove"))))));
                                                                                                              var painel = $("#" + wbes[i][0] + "WBE");
                                                                                                              painel.draggable({containment: "#MainLayout"});
                                                                                                              painel.resizable({
                                                                                                                        maxHeight: 480,
                                                                                                                        maxWidth: 880,
                                                                                                                        minHeight: 240,
                                                                                                                        minWidth: 245});
                                                                                                              var grafico;
                                                                                                              if (wbes[i][10] == 1)
                                                                                                                        grafico = "Linhas";
                                                                                                              if (wbes[i][10] == 2)
                                                                                                                        grafico = "Barras";
                                                                                                              if (wbes[i][10] == 3)
                                                                                                                        grafico = "Tarte";
                                                                                                              if (wbes[i][10] == 4)
                                                                                                                        grafico = "Inbound";
                                                                                                              painel.append($("<table>")
                                                                                                                      .addClass("table table-condensed table-mod")
                                                                                                                      .append($("<tbody>")
                                                                                                                      .append($("<tr>")
                                                                                                                      .append($("<th>")
                                                                                                                      .text("Actualização segundos"))
                                                                                                                      .append($("<td>").text(wbes[i][9] / 1000)))
                                                                                                                      .append($("<tr>")
                                                                                                                      .append($("<th>")
                                                                                                                      .text("Tipo de Gráfico"))
                                                                                                                      .append($("<td>").text(grafico)))
                                                                                                                      .append($("<tr>")
                                                                                                                      .append($("<th>")
                                                                                                                      .text("Dados do Gráfico"))
                                                                                                                      .append($("<td>").text(wbes[i][8])))));
                                                                                                              i++;
                                                                                                    }
                                                                                                    );
                                                                                          }

                                                                                          function update_dropbox_layout() {
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
                                                                                          function load_dados(opcao, layout_Id)
                                                                                          {
                                                                                                    $.post("Requests.php", {action: opcao, layout_Id: layout_Id},
                                                                                                    function(data)
                                                                                                    {
                                                                                                              if (data === null)
                                                                                                              {
                                                                                                                        $(".PanelWB").remove();
                                                                                                                        wbes = []; //limpa o array dos wallboards, senao dps de se passar de uma layout com elementos para esta, como o wbes ainda contem os elementos do layout anterior, eles sao criados outra vez
                                                                                                                        $.jGrowl('Layout/Wallboard inexistente');
                                                                                                                        return false;
                                                                                                              }

                                                                                                              if (opcao === "layout")//Load dados layout
                                                                                                              {
                                                                                                                        layouts = [];
                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  layouts.push([this.id, this.name]);
                                                                                                                        });
                                                                                                                        $("#MainLayout").empty();
                                                                                                                        $('#LayoutSelector').empty();
                                                                                                                        update_dropbox_layout();
                                                                                                                        layout_change();
                                                                                                              }
                                                                                                              if (opcao === 'wbe')//load dados WBElement
                                                                                                              {
                                                                                                                        wbes = [];

                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  wbes.push([this.id, this.name, this.posX, this.posY, this.width, this.height, this.layout_Id, this.query_text, this.opcao_query, this.update_time, this.graph_type]);
                                                                                                                        });
                                                                                                                        update_wbe();
                                                                                                              }
                                                                                                    }, "json");


                                                                                          }
                                                                                          function sql_basic(opcao, layout_Id, id_wbe)
                                                                                          {
                                                                                                    $.post("Requests.php", {action: opcao, layout_Id: layout_Id, id: id_wbe},
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

                                                                                                                        var i = get_indice_layout(idLayout);
                                                                                                                        $('#label_layout_id').text(layouts[i][0]);
                                                                                                                        $("#Layout_Input_name").val(layouts[i][1]);
                                                                                                                        load_dados('layout', 0);
                                                                                                                        layout_change();

                                                                                                                        $(this).dialog("close");

                                                                                                              }


                                                                                                    }, "json");
                                                                                          }
                                                                                          //wallboard
                                                                                          function manipulate_dados(opcao, Id, Name, PosX, PosY, Width, Height, Layout_Id, graph_ID)
                                                                                          {
                                                                                                    $.post("Requests.php", {action: opcao, id: Id, name: Name, posX: PosX, posY: PosY, width: Width, height: Height, layout_Id: Layout_Id, graph_id: graph_ID},
                                                                                                    function(data)
                                                                                                    {
                                                                                                              if (opcao === "edit_Layout") {
                                                                                                                        layout_edit();
                                                                                                              }
                                                                                                    }, "json");
                                                                                          }
                                                                                          //graphs
                                                                                          function manipulate_graph(Opcao, Id, Name, PosX, PosY, Width, Height, Layout_id, Query_text, Opcao_query, Update_time, Graph_type)
                                                                                          {
                                                                                                    $.post("Requests.php", {action: Opcao, id: Id, name: Name, posx: PosX, posy: PosY, width: Width, height: Height, layout_id: Layout_id, query_text: Query_text, opcao_query: Opcao_query, update_time: Update_time, graph_type: Graph_type},
                                                                                                    function(data)
                                                                                                    {
                                                                                                              if (Opcao == 'get_query')
                                                                                                              {
                                                                                                                        var qt = $("#query_type");
                                                                                                                        qt.empty();

                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  qt.append($('<option></option>').val(this.id).html(this.opcao_query));
                                                                                                                                  queries.push([this.id, this.query_text, this.opcao_query, this.type_query]);
                                                                                                                        });

                                                                                                              }


                                                                                                    }, "json");
                                                                                          }
                                                                                          //---Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados------Base de Dados---

                                                                                          //FLOT EXTRA HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHh
                                                                                          function flot_extra_init()
                                                                                          {
                                                                                                    var gao_ug = $("#gao_userGroup");
                                                                                                    var gao_s = $("#gao_status");
                                                                                                    var gao_c = $("#gao_campaign");

                                                                                                    gao_ug.append("<label class='label label-info'>Grupos</label");
                                                                                                    gao_ug.append("<select id='user_group'> </select> ");
                                                                                                    gao_s.append("<label class='label label-info'>Status</label");
                                                                                                    gao_s.append("<select id='status_venda'> </select> ");
                                                                                                    gao_c.append("<label class='label label-info'>Campanha</label");
                                                                                                    gao_c.append("<select id='campaign_id'> </select> ");

                                                                                                    flot_extra("user_group");
                                                                                                    flot_extra("status_venda");
                                                                                                    flot_extra("campaign_id");
                                                                                                    flot_extra("group_inbound");
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
                                                                                                              if (opcao === "user_group")
                                                                                                              {
                                                                                                                        $("#user_group").empty();
                                                                                                                        var db = document.getElementById("user_group");
                                                                                                                        var opt = document.createElement("option");
                                                                                                                        opt.value = "1";
                                                                                                                        opt.text = "all";
                                                                                                                        db.options.add(opt);
                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  var opt1 = document.createElement("option");
                                                                                                                                  opt1.value = value;
                                                                                                                                  opt1.text = value;
                                                                                                                                  db.options.add(opt1);
                                                                                                                        });
                                                                                                              }
                                                                                                              if (opcao === "status_venda")
                                                                                                              {
                                                                                                                        var db = document.getElementById("status_venda");
                                                                                                                        var opt = document.createElement("option");
                                                                                                                        opt.value = "1";
                                                                                                                        opt.text = "all";
                                                                                                                        db.options.add(opt);
                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  var opt1 = document.createElement("option");
                                                                                                                                  opt1.value = this.status_v;
                                                                                                                                  opt1.text = this.status_t;
                                                                                                                                  db.options.add(opt1);
                                                                                                                        });
                                                                                                              }
                                                                                                              if (opcao === "campaign_id")
                                                                                                              {
                                                                                                                        var db = document.getElementById("campaign_id");
                                                                                                                        $.each(data, function(index, value) {
                                                                                                                                  var opt1 = document.createElement("option");
                                                                                                                                  opt1.value = this.campaign_id;
                                                                                                                                  opt1.text = this.campaign_name;
                                                                                                                                  db.options.add(opt1);
                                                                                                                        });
                                                                                                              }
                                                                                                              if (opcao === "group_inbound")
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