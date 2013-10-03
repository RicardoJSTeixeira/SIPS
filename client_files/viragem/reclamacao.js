////open_page("../client_files/goviragem/reclamacao.html")




$(function()
{


      $("#tabs").tabs();
      $(".datetime_range").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt"}).keypress(function(e) {
            e.preventDefault();
      }).bind("cut copy paste", function(e) {
            e.preventDefault();
      });
      $("#dateform").validationEngine();



      var modal_pa =
              {
                    nome: $("#modal_por_abrir #nome_cliente"),
                    campanha: $("#modal_por_abrir #campanha"),
                    data: $("#modal_por_abrir #data"),
                    select_c: $("#modal_por_abrir #select_comentario"),
                    textarea_c_valor: $("#modal_por_abrir #textarea_comentario_valor"),
                    t_reclamacao: $("#modal_por_abrir #tipo_reclamacao"),
                    tp_reclamacao: $("#modal_por_abrir #tipificacao_reclamacao"),
                    r_enviar: $("#modal_por_abrir #radio1"),
                    r_fechar: $("#modal_por_abrir #radio2")
              };

      var modal_af =
              {
                    nome: $("#modal_abertos_fechados #nome_cliente_af"),
                    campanha: $("#modal_abertos_fechados #campanha_af"),
                    data: $("#modal_abertos_fechados #data_af"),
                    comentario: $("#modal_abertos_fechados #comentario_af"),
                    estado: $("#modal_abertos_fechados #checkbox1"),
                    t_reclamacao: $("#modal_abertos_fechados #tipo_reclamacao_af"),
                    tp_reclamacao: $("#modal_abertos_fechados #tipificacao_reclamacao_af"),
              };


      $("#button_send_mail").on("click", function() {
            var info = $(this).data("info");
            var comentario = modal_pa.textarea_c_valor.val();
            var email = "";
            var t_reclamacao = modal_pa.t_reclamacao.find("option:selected").text();
            var tp_reclamacao = modal_pa.tp_reclamacao.find("option:selected").text();
            $.post("requests.php", {action: "save_mail", lead_id: info.lead_id, nome: info.nome, campanha: info.campanha, comentario: comentario, email: email, data: info.data, tipo: $("#radio2").is(":checked"), tipo_reclamacao: t_reclamacao, tipificacao_reclamacao: tp_reclamacao}, function(data) {
                  $("#modal_por_abrir").modal('hide');
            }, "json")
                    .fail(function() {
                  return false;
            });
      });





      $(document).on("click", ".ver_reclamacao", function()
      {
            var info = $(this).data("info");
            switch ($(this).data("tipo"))
            {
                  case ("por_abrir"):
                        modal_pa.nome.text(info.nome);
                        modal_pa.campanha.text(info.campanha);
                        modal_pa.data.text(info.data);
                        $("#button_send_mail").data("info", $(this).data("info"));
                        modal_pa.select_c.empty();
                        $.post("requests.php", {action: "get_script_fields", campaign_id: info.campaign_id, lead_id: info.lead_id}, function(data) {
                              modal_pa.textarea_c_valor.text("Não existe texto de reclamação");
                              $.each(data, function() {
                                    modal_pa.select_c.append("<option data-valor='" + this.valor + "' value=" + this.id + " >Tag:" + this.tag + "---" + this.type + "</option>");
                                    modal_pa.textarea_c_valor.text(this.valor);
                              });
                              modal_pa.select_c.find("option:last-child").prop("selected", true);
                        }, "json")
                                .fail(function() {
                              return false;
                        });
                        $("#modal_por_abrir").modal('show');
                        break;

                  case ("abertos"):
                        modal_af.nome.text(info.nome);
                        modal_af.campanha.text(info.campanha);
                        modal_af.data.text(info.data);
                        $("#checkbox1").prop("checked", false);
                        $("#modal_abertos_fechados").modal('show');
                        break;

                  case ("fechados"):
                        $("#checkbox1").prop("checked", true);
                        $("#modal_abertos_fechados").modal('show');
                        break;

                  case ("expirados"):
                        $("#modal_expirados").modal('show');
                        break;
            }


      });

      $(document).on("change", "#select_comentario", function()
      {
            modal_pa.textarea_c_valor.text(modal_pa.select_c.find("option:selected").data("valor"));
      });
});


$("#button_pesquisa").on("click", function()
{
      $("#report_reclamacao").removeClass("hidden");
      if ($("#dateform").validationEngine('validate'))
      {
            $(".table_tbody").empty();
            var table_body = "";
            $.post("requests.php", {action: "get_table_data_new", data_inicio: $("#datetime_from").val(), data_fim: $("#datetime_to").val()}, function(data) {
                  $.each(data, function() {
                        $("#table_por_abrir .table_tbody").append($("<tr>")
                                .append($("<td>").text(this.nome))
                                .append($("<td>").text(this.campanha))
                                .append($("<td>").text(this.data)
                                .append($("<div>").addClass("view-button").append($("<button>").addClass("btn icon-reorder ver_reclamacao").data("tipo", "por_abrir").data("info", this).text(" Ver"))))
                                );
                  });
                  $.post("requests.php", {action: "get_table_data_resolved", data_inicio: $("#datetime_from").val(), data_fim: $("#datetime_to").val()}, function(data) {
                        $.each(data, function() {

                              if (this.tipo)
                                    table_body = $("#table_fechados .table_tbody");
                              else
                                    table_body = $("#table_abertos .table_tbody");

                              table_body.append($("<tr>")
                                      .append($("<td>").text(this.nome))
                                      .append($("<td>").text(this.campanha))
                                      .append($("<td>").text(this.tipo_reclamacao))
                                      .append($("<td>").text(this.tipificacao_reclamacao))
                                      .append($("<td>").text(this.data)
                                      .append($("<div>").addClass("view-button").append($("<button>").addClass("btn icon-reorder ver_reclamacao").data("tipo", (this.tipo) ? "abertos" : "fechados").data("info", this).text(" Ver"))))
                                      );
                        });
                        $.post("requests.php", {action: "get_table_data_expired"}, function(data) {
                              table_body = $("#table_expirados .table_tbody");
                              $.each(data, function() {
                                    table_body.append($("<tr>")
                                            .append($("<td>").text(this.nome))
                                            .append($("<td>").text(this.campanha))
                                            .append($("<td>").text(this.tipo_reclamacao))
                                            .append($("<td>").text(this.tipificacao_reclamacao))
                                            .append($("<td>").text(this.data))
                                            .append($("<td>").text((this.tipo) ? "aberto" : "fechado")
                                            .append($("<div>").addClass("view-button").append($("<button>").addClass("btn icon-reorder  ver_reclamacao").data("tipo", "expirado").data("info", this).text(" Ver"))))
                                            );
                              });
                        }, "json")
                                .fail(function() {
                              return false;
                        });
                  }, "json")
                          .fail(function() {
                        return false;
                  });
            }, "json")
                    .fail(function() {
                  return false;
            });
      }


});


