
//aduarte@finesource.pt




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

//optimizar o window.open(mandar as cenas por array)
//maneira de fazer multiselect no dataSet é pra ser optimizado
//adicionar lista de items selecionados no dataSet


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

$("#dataTable_opcao").change(function()
{
      var select = $("#dataTable_opcao");

      var campaing = $("#campaign_id_dataTable");
      var grupo_inbound = $("#grupo_inbound_dataTable");
      var grupo_user = $("#grupo_user_dataTable");

      $("#campaign_id_dataTable").show();
      if (select.val() === "1")
      {
            campaing.show();
            grupo_inbound.hide();
            grupo_user.hide();
      }
      if (select.val() === "2")
      {
            campaing.hide();
            grupo_inbound.show();
            grupo_user.hide();
      }
      if (select.val() === "3")
      {
            campaing.hide();
            grupo_inbound.hide();
            grupo_user.show();
      }

});
//-----------------FUNÇÕES DOS BUTTONS----------------------------



// FUNÇÂO STARTER-----------------------------------------------------------------------------------------99999999----------------------------------------------------------------0000000000000000000000
$(function() {

      $("#campaign_id_dataTable").show();
      $("#grupo_inbound_dataTable").hide();
      $("#grupo_user_dataTable").hide();

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
                  manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);
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

            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0);

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
                        manipulate_graph("insert_wbe", 0, $("#graph_name").val(), Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 250, 250, idLayout, $("#update_time").val(), selected_type_graph, 0, 0, 0);
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
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "2":
                                                      //Chamadas perdidas por user   
                                                      querie = get_query(2);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "perdidas", 0);
                                                      break;
                                                case "3":
                                                      //Chamadas feitas por user 
                                                      querie = get_query(3);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), 0, "feitas", 0);
                                                      break;
                                          }
                                          break;
                                    case "2":

                                          switch ($("#chamadas").val())
                                          {
                                                case "1":
                                                      //Chamadas atendidas por user_group
                                                      querie = get_query(4);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "2":
                                                      //Chamadas perdidas por user_group   
                                                      querie = get_query(5);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "perdidas", 0);
                                                      break;
                                                case "3":
                                                      //Chamadas feitas por user_group 
                                                      querie = get_query(6);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), 0, "feitas", 0);
                                                      break;
                                          }
                                          break;
                                    case "3":
                                          switch ($("#chamadas").val())
                                          {
                                                case "1":
                                                      //Chamadas atendidas por campanha
                                                      querie = get_query(7);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "2":
                                                      //Chamadas perdidas por campanha   
                                                      querie = get_query(8);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "perdidas", 0);
                                                      break;
                                                case "3":
                                                      //Chamadas feitas por campanha 
                                                      querie = get_query(9);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), 0, "feitas", 0);
                                                      break;
                                          }
                                          break;
                                    case "4":
                                          switch ($("#chamadas").val())
                                          {
                                                case "1":
                                                      //Chamadas atendidas por Total CallCenter
                                                      querie = get_query(10);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "2":
                                                      //Chamadas perdidas por Total CallCenter  
                                                      querie = get_query(11);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "perdidas", 0);
                                                      break;
                                                case "3":
                                                      //Chamadas feitas por Total CallCenter 
                                                      querie = get_query(12);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), 0, "feitas", 0);
                                                      break;
                                          }
                                          break;
                                    case "5":
                                          switch ($("#chamadas").val())
                                          {
                                                case "1":
                                                      //Chamadas atendidas por Inbound
                                                      querie = get_query(18);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "2":
                                                      //Chamadas perdidas por Inbound   
                                                      querie = get_query(19);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas", 0);
                                                      break;
                                                case "3":
                                                      //Chamadas feitas por Inbound 
                                                      querie = get_query(20);
                                                      manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), 0, "atendidas", 0);
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
                                          manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", $("#user option:selected").val(), 0, 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0, $("#status_venda option:selected").text());
                                          break;
                                    case "2":
                                          //feedback por user_group
                                          querie = get_query(14);
                                          manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, $("#user_group option:selected").val(), 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0, $("#status_venda option:selected").text());
                                          break;
                                    case "3":
                                          //feedback por campanha
                                          querie = get_query(15);

                                          manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, $("#campaign option:selected").val(), 0, $("#in_out_bound").val(), $("#status_venda").val(), 0, $("#status_venda option:selected").text());
                                          break;
                                    case "4":
                                          //feedback por call center
                                          querie = get_query(16);
                                          manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, 0, $("#in_out_bound").val(), $("#status_venda").val(), 0, $("#status_venda option:selected").text());
                                          break;
                                    case "5":
                                          //feedback por linha inbound
                                          querie = get_query(17);
                                          manipulate_dataset(opcao, id_dataset, id_wallboard, querie[6], querie[4], "1", 0, 0, 0, $("#inbound").val(), $("#in_out_bound").val(), $("#status_venda").val(), 0, $("#status_venda option:selected").text());

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

                        manipulate_graph("insert_wbe", 0, "Estatistica", Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 429, 242, idLayout, 10000, 4, $("#group_inbound_select").val(), $("#group_inbound_select option:selected").text(), 0);

                        $(this).dialog("close");
                  }
            }
      });
      $("#dialog_dataTable").dialog({
            dialogClass: 'dialogButtons',
            autoOpen: false,
            resizable: false,
            height: 480,
            width: 280,
            modal: true,
            buttons: {
                  "Cancelar": function() {
                        $(this).dialog("close");
                  },
                  "Criar": function() {



                        var feedbacks = $("#dataTable_feedback").val();
                        feedbacks = "'" + feedbacks + "'";


                        switch ($("#dataTable_opcao").val())
                        {
                              case "1":
                                    manipulate_graph("insert_wbe", 1, "Top users", Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 429, 242, idLayout, 10000, 5, $("#campaign_id_dataTable").val(), $("#dataTable_timespan").val(), $("#campaign_id_dataTable option:selected").text(), feedbacks);
                                    break;
                              case "2":
                                    manipulate_graph("insert_wbe", 2, "Top users", Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 429, 242, idLayout, 10000, 5, $("#grupo_inbound_dataTable").val(), $("#dataTable_timespan").val(), $("#grupo_inbound_dataTable option:selected").text(), feedbacks);
                                    break;
                              case "3":
                                    manipulate_graph("insert_wbe", 3, "Top users", Math.floor((Math.random() * 500) + 1), Math.floor((Math.random() * 250) + 1), 429, 242, idLayout, 10000, 5, $("#grupo_user_dataTable").val(), $("#dataTable_timespan").val(), $("#grupo_user_dataTable option:selected").text(), feedbacks);
                                    break;
                        }
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
            $("#dialog_inbound").dialog("open");
      });
      $(".diablog_opener_dataTable").click(function() {
            $("#dialog_dataTable").dialog("open");
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
            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph, 0, 0, 0);
            $("#gao_status").show();
            $("#gao_userGroup").show();
      }
      //pie
      if (selected_type_graph === 3) {
            manipulate_graph("get_query", 0, 0, 0, 0, 0, 0, 0, 0, selected_type_graph, 0, 0, 0);
            $("#gao_campaign").show();
      }

}

function fullScreen()
{
      window_slave = window.open("/sips-admin/wallboard1/full_screen_render.html", idLayout + ";" + $("#MainLayout").width() + ";" + $("#MainLayout").height());
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
            if (wbes[i][8] === "4" || wbes[i][8] === "5")//Todos menos Inbound
            {
                  ml.append($("<div>").addClass("PanelWB ui-widget-content").attr("id", wbes[i][0] + "WBE")
                          .css("left", wbes[i][3] + "px")
                          .css("top", wbes[i][4] + "px")
                          .css("width", wbes[i][5] + "px")
                          .css("height", wbes[i][6] + "px")
                          .append($("<div>").addClass("grid-title")
                          .append($("<div>").addClass("pull-left")
                          .text(wbes[i][2] + " de " + wbes[i][9][0].param1))
                          .append($("<div>").addClass("pull-right")
                          .append($("<button>").addClass("btn icon-alone btn-danger delete_button").data("wbe_id", wbes[i][0]).attr("data-t", "tooltip").attr("title", "Remover Wallboard")
                          .append($("<i>").addClass("icon-remove")))
                          ))
                          .append($("<div>").addClass("grid-content").attr("id", "grid_content")));

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

function manipulate_dataset(Opcao, Id, id_Wallboard, Codigo_query, Opcao_query, Tempo, User, User_group, Campaign_id, Linha_inbound, Mode, Status_feedback, Chamadas, Param1)
{
      $.post("Requests.php", {action: Opcao, id: Id, id_wallboard: id_Wallboard, codigo_query: Codigo_query, opcao_query: Opcao_query, tempo: Tempo, user: User, user_group: User_group, campaign_id: Campaign_id, linha_inbound: Linha_inbound, mode: Mode, status_feedback: Status_feedback, chamadas: Chamadas, param1: Param1},
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
function manipulate_graph(Opcao, Id, Name, Pos_x, Pos_y, Width, Height, id_Layout, Update_time, Graph_type, Param1, Param2, Param3, Param4)
{

      $.post("Requests.php", {action: Opcao, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_Layout, update_time: Update_time, graph_type: Graph_type, param1: Param1, param2: Param2, param3: Param3, param4: Param4},
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
      gao_i.append("<select id='inbound'> </select> ");
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
                  var object = $("#user");
                  object.empty();
                  $.each(data, function(index, value) {
                        object.append(new Option(value, value));
                  });
            }
            if (opcao === "user_group")
            {
                  var object = $("#user_group");
                  var object1 = $("#grupo_user_dataTable");
                  object.empty();
                  $.each(data, function(index, value) {
                        object.append(new Option(value, value));
                        object1.append(new Option(value, value));
                  });
            }

            if (opcao === "campaign")
            {
                  var object = $("#campaign");
                  var object1 = $("#campaign_id_dataTable");
                  $.each(data, function(index, value) {
                        object.append(new Option(this.campaign_name, this.campaign_id));
                        object1.append(new Option(this.campaign_name, this.campaign_id));
                  });
            }
            if (opcao === "status_venda")
            {
                  var object = $("#status_venda");
                  var object1 = $("#dataTable_feedback");
                  $.each(data, function(index, value) {
                        object.append(new Option(this.status_t, this.status_v));
                        object1.append(new Option(this.status_t, this.status_v));
                  });
            }


            if (opcao === "inbound")
            {
                  var object = $([]).add($("#group_inbound_select")).add($("#inbound")).add($("#grupo_inbound_dataTable"));

                  $.each(data, function(index, value) {
                        object.append(new Option(this.name, this.id));
                  });









            }
      }, "json");
}
//FLOT EXTRA HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHh

