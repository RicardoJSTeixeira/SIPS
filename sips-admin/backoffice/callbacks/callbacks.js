
var user = getUrlVars().user;

$(function() {
      $(".chosen-select").chosen(({no_results_text: "Sem resultados"}));
      $(".datetimep").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt"}).keypress(function(e) {
            e.preventDefault();
      }).bind("cut copy paste", function(e) {
            e.preventDefault();
      });
      $.post("requests.php", {action: "get_agent_name_by_user", user: user},
      function(data)
      {
            $("#agente_name").text("Agente->" + data);
      });


      $("#select_agent").empty();
      $("#select_agent_transfer").empty();
      $("#select_agent_transfer_by_c").empty();
      $.post("requests.php", {action: "get_agents"},
      function(data1)
      {
            var temp = "";
            $.each(data1, function() {
                  temp += "<option value='" + this.user + "'>" + this.full_name + "</option>";
            });
            $("#select_agent").append(temp);
            $("#select_agent").trigger("liszt:updated");
            $("#select_agent_transfer").append(temp);
            $("#select_agent_transfer").trigger("liszt:updated");
            $("#select_agent_transfer_by_c").append(temp);
            $("#select_agent_transfer_by_c").trigger("liszt:updated");
      }
      , "json");





      get_campanhas();
      $("#form_search").validationEngine();
});

$("#checkbox1").on("click", function()
{
      $(".time_div").toggle(300);
});



function get_campanhas()
{
      $.post("requests.php", {action: "get_campaign", user: user},
      function(data2)
      {
            $("#campaign_selector").empty();
            var temp = "";
            $.each(data2, function() {

                  temp += "<option value='" + this.id + "'>" + this.name + "</option>";
            });
            $("#campaign_selector").append(temp);
            $("#campaign_selector").trigger("liszt:updated");
      }
      , "json");



      $.post("requests.php", {action: "get_campaign_all", user: user},
      function(data2)
      {

            $("#select_campanha_transfer_by_c").empty();
            $("#select_campanha_edit").empty();
            var temp = "";
            $.each(data2, function() {

                  temp += "<option value='" + this.id + "'>" + this.name + "</option>";
            });

            $("#select_campanha_transfer_by_c").append(temp);
            $("#select_campanha_transfer_by_c").trigger("liszt:updated");
            $("#select_campanha_edit").append(temp);
            $("#select_campanha_edit").trigger("liszt:updated");
      }
      , "json");
}

function get_table_data()
{
      get_campanhas();
      $('#callback_info').dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sAjaxSource": 'requests.php',
            "fnServerParams": function(aoData) {
                  aoData.push(
                          {"name": "action", "value": "get_callback_by_user"},
                  {"name": "all_date", "value": $("#checkbox1").is(":checked")},
                  {"name": "user", "value": user},
                  {"name": "data_inicio", "value": $("#data_inicio").val()},
                  {"name": "data_fim", "value": $("#data_fim").val()}
                  );
            },
            "aoColumns": [
                  {"sTitle": "lead", "mDataProp": "lead_id"},
                  {"sTitle": "Nome Cliente", "mDataProp": "first_name"},
                  {"sTitle": "Campanha", "mDataProp": "campaign_name"},
                  {"sTitle": "Data de entrada", "mDataProp": "entry_time"},
                  {"sTitle": "Data do Callback", "mDataProp": "callback_time"},
                  {"sTitle": "Comentários", "mDataProp": "comments"},
                  {
                        "sTitle": "Opções",
                        "sClass": "center",
                        "mDataProp": "callback_id",
                        "fnRender": function(obj) {

                              var returnButton = "<div class='view-button'><button class='ver_callback btn btn-primary ' type='button' data-callback_id='" + obj.aData.callback_id + "'>Ver</button><button class='apagar_callback btn  btn-inverse  icon-remove btn-small'  data-callback_id='" + obj.aData.callback_id + "'></button></div>";
                              return returnButton;
                        }
                  }],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
      });

}
;

$("#search").on("click", function(e)
{
      e.preventDefault();
      if ($("#form_search").validationEngine('validate'))
      {
            get_table_data();
      }
});

//VER CALLBACK
$(document).on("click", ".ver_callback", function() {
      var that = $(this);
      $.post("requests.php", {action: "get_callback_by_id", callback_id:that.data("callback_id")},
      function(data1)
      {
            var mc = $("#modal_callback");
            mc.modal("show");
            mc.find("#label_cliente_name").text(data1.first_name);
            mc.find("#select_agent").find("option[value='" + user + "']").prop("selected", true).end().trigger("liszt:updated");
            mc.find("#label_bd_name").text(data1.list_name);
            mc.find("#label_cliente_name").text(data1.first_name);
            mc.find("#select_campanha_edit").val(data1.campaign_id).trigger("liszt:updated");
            mc.find("#label_entry_date").text(data1.entry_time);
            mc.find("#callback_datepicker").val(data1.callback_time);
            mc.find("#textarea_comments").text(data1.comments);
            mc.find("#edit_callback").data("callback_id", that.data("callback_id"));
      }
      , "json");
});

//EDITAR CALLBACK
$("#edit_callback").on("click", function()
{
      var that_info = $(this).data("callback_id");
      $.post("requests.php", {
            action: "edit_callbacks_by_id",
            callback_id: that_info,
            user: $("#modal_callback #select_agent option:selected").val(),
            comments: $("#modal_callback #textarea_comments").val(),
            callback_date: $("#modal_callback #callback_datepicker").val(),
            campaign_id: $("#modal_callback #select_campanha_edit").val()},
      function(data1)
      {
            $("#modal_callback").modal("hide");
            get_table_data();
      }
      , "json");
});


$(".radio_option").on("click", function()
{
      $(".reset_option_div").hide();
      if ($(this).val() == "1")
            $("#reset_all_div").show();
      if ($(this).val() == "2")
            $("#reset_campaign_div").show();
      if ($(this).val() == "3")
            $("#transfer_all_div").show();
      if ($(this).val() == "4")
            $("#transfer_by_cd_div").show();
});


// ELIMINATE ALL CALLBACKS ********************************************************************************
$("#reset_all").on("click", function()
{
      $('#eliminar_todos').modal('show');
});
$("#confirm_reset_all_callbacks").on("click", function()
{
      $.post("requests.php", {action: "reset_callbacks_by_user", user: user}, function(data) {
            $('#eliminar_todos').modal('hide');
            get_table_data();
      });
});
// ********************************************************************************************************


// ELIMINATE ALL CALLBACKS By CAMPAIGN AND DATE ***********************************************************
$("#reset_dc").on("click", function(e)
{
      e.preventDefault();
      if ($("#form_search").validationEngine('validate'))
      {
            $('#eliminar_dc').modal('show');
      }
});
$("#confirm_reset_dc_callbacks").on("click", function()
{
      $.post("requests.php", {action: "reset_callbacks_by_dc", all_date: $("#checkbox1").is(":checked"), user: getUrlVars().user, data_inicio: $("#data_inicio").val(), data_fim: $("#data_fim").val(), campaign_id: $("#campaign_selector").val()}, function(data) {
            $('#eliminar_dc').modal('hide');
            get_table_data();
      });
});
// ********************************************************************************************************


// ELIMINATE CALLBACK BY ID ***********************************************************
$(document).on("click", ".apagar_callback", function() {
      $('#eliminar_one').modal('show');
 
      $("#confirm_reset_one_callbacks").data("callback_id",$(this).data("callback_id"));
});

$("#confirm_reset_one_callbacks").on("click", function()
{
      $.post("requests.php", {action: "reset_callbacks_by_id", callback_id: $(this).data("callback_id")},
      function(data1)
      {
            $('#eliminar_one').modal('hide');
            get_table_data();
      }
      , "json");
});
// ********************************************************************************************************


//TRANSFER CALLBACKS TO AGENT*****************************************************************************
$("#transfer_all").on("click", function()
{
      $("#transfer_to_agent").modal("show");
});
$("#confirm_transfer_callbacks").on("click", function()
{
      $.post("requests.php", {action: "transfer_callbacks_to_agent", old_user: user, new_user: $("#select_agent_transfer option:selected").val()},
      function(data1)
      {
            $("#transfer_to_agent").modal('hide');
            get_table_data();
      }
      , "json");
});
//**********************************************************************************************************



//TRANSFER CALLBACKS BY CAMPAIGN TO AGENT*****************************************************************************
$("#transfer_by_c").on("click", function()
{
      $("#transfer_to_agent_by_c").modal("show");
});
$("#confirm_transfer_callbacks_by_c").on("click", function()
{
      $.post("requests.php", {action: "transfer_callbacks_to_agent_by_c", old_user: user, new_user: $("#select_agent_transfer_by_c option:selected").val(), campaign_id: $("#select_campanha_transfer_by_c option:selected").val()},
      function(data1)
      {
            $("#transfer_to_agent_by_c").modal('hide');
            get_table_data();
      }
      , "json");
});
//**********************************************************************************************************


function getUrlVars() {
      var vars = {};
      var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
      });

      return vars;
}