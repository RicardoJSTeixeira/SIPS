//update na layout
var layouts = [];
var idLayout = 0;
var window_slave;
var selected_type_graph = 0;
var queries = [];
var layouts = [];
var id_wallboard;
var id_dataset;
var edit_dataset = false;
var new_layout_flag;

function loadWallboard() {
	$.post("../php/wallboardRequests.php", {action: "get_queries"},
	function(data)
	{
		if (data !== null) {
			$.each(data, function(index, value) {
				queries[this.codigo] = [this.id, this.query_text_inbound, this.query_text_outbound, this.opcao_query];
			});
		}
	}, "json");

	$(".dataTable_options").hide();
	$(".pie_select").hide();
	$("#campaign_id_dataTable_div").show();
	$("#campaign_id_pie_div").show();
	$("#MainLayout").on("click", ".delete_button", function(e) {
		var id = $(this).data("wbe_id");
		sql_basic('delete_WBE', 0, id);
	});
	$("#MainLayout").on("click", ".add_dataset_button", function(e) {
		edit_dataset = false;
		//reset dataset   
		$("#resultado_dataset_1").attr("checked", "checked");
		radio_checks();
		$("#linhas_serie").val(1);
		$("#linhas_filtro").val(1);
		$("#chamadas").val(1);
		$(".graph_advance_option").hide();
		$(".option_filtro").hide();
		$("#gao_user").show();
		$("#gao_chamadas").show();
		$('#user').val("").trigger('liszt:updated');
		//fim do reset

		$("#create_button_dataset").text("Create");
		if (wbes[get_indice_wbe($(this).data("wbe_id"))][9].length >= 5)
		{
			$.smallBox({
				title: "Datasets limit per wallboard reached (5 max)",
				color: "#D8C74E",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		}
		else {
			id_wallboard = $(this).data("wbe_id");
			$('#dialog_dataset_linhas').modal('show');
		}
	});
	$("#MainLayout").on("click", ".edit_dataset_button", function(e) {
		edit_dataset = true;
		$("#create_button_dataset").text("Save changes");
		id_dataset = $(this).data("dataset_id");
		id_wallboard = get_indice_wbe($(this).data("id"));
		console.log(id_wallboard);
		$(".graph_advance_option").hide();
		$(".option_filtro").hide();
		$.each(wbes[id_wallboard][9], function(a, value)
		{
			if (wbes[id_wallboard][9][a].id == id_dataset)
			{
//Inbound,Outbound,Blended
				if (wbes[id_wallboard][9][a].mode == "1") {
					$("#resultado_dataset_1").attr("checked", "checked");
				}
				if (wbes[id_wallboard][9][a].mode == "2")
					$("#resultado_dataset_2").attr("checked", "checked");
				if (wbes[id_wallboard][9][a].mode == "3")
					$("#resultado_dataset_3").attr("checked", "checked");
				radio_checks();
				//PARAMETROS
				if (wbes[id_wallboard][9][a].user != 0)
				{
					$("#linhas_serie").val(1);
					$('#user').val(wbes[id_wallboard][9][a].user).trigger('liszt:updated');
					$("#gao_user").show();
				}
				else if (wbes[id_wallboard][9][a].user_group != 0)
				{
					$("#gao_userGroup").show();
					$("#linhas_serie").val(2);
					$('#user_group').val(wbes[id_wallboard][9][a].user_group).trigger('liszt:updated');
				}
				else if (wbes[id_wallboard][9][a].campaign_id != 0)
				{
					$("#gao_campaign").show();
					$("#linhas_serie").val(3);
					$('#campaign').val(wbes[id_wallboard][9][a].campaign_id).trigger('liszt:updated');
				}
				else if (wbes[id_wallboard][9][a].linha_inbound != 0)
				{
					$("#gao_inbound").show();
					$("#linhas_serie").val(5);
					$('#inbound').val(wbes[id_wallboard][9][a].linha_inbound).trigger('liszt:updated');
				} else {
					$("#linhas_serie").val(4);
				}
				//FILTRO
				if (wbes[id_wallboard][9][a].status_feedback != 0)
				{
					$("#gao_status").show();
					$("#linhas_filtro").val(2);
					$('#status_venda').val(wbes[id_wallboard][9][a].status_feedback).trigger('liszt:updated');
				}
				else if (wbes[id_wallboard][9][a].chamadas != 0)
				{
					$("#gao_chamadas").show();
					$("#linhas_filtro").val(1);
					if (wbes[id_wallboard][9][a].chamadas == "Answered")
						$("#chamadas").val(1);
					if (wbes[id_wallboard][9][a].chamadas == "Lost")
						$("#chamadas").val(2);
					if (wbes[id_wallboard][9][a].chamadas == "Made")
						$("#chamadas").val(3);
				}
			}
		});
		$('#dialog_dataset_linhas').modal('show');
	});
	$("#MainLayout").on("click", ".delete_dataset_button", function(e) {
		manipulate_dataset("remove_dataset", $(this).data("dataset_id"), 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	});
	$("#checkbox_feedback").on("click", function(e) {
		if (this.checked)
		{
			$(this).closest(".modal-body").css("overflow", "visible");
			$("#dataTable_status_select_div").hide();
		}
		else
		{
			$("#dataTable_status_select_div").show();
			$(this).closest(".modal-body").css("overflow", "auto");
		}
	});
	$("#checkbox_feedback_pie").on("click", function(e) {
		if (this.checked)
		{
			$(this).closest(".modal-body").css("overflow", "visible");
			$("#pie_status_select_div").hide();
		}
		else
		{
			$(this).closest(".modal-body").css("overflow", "auto");
			$("#pie_status_select_div").show();
		}
	});
	$("#topTable_button").on("click", function(e) {
		$('#dataTable_status_select').val('').trigger('liszt:updated');
	}); //GRAFICO DE TOP
	$("#pie_button").on("click", function(e) {
		$('#pie_status_select').val('').trigger('liszt:updated');
	}); //GRAFICO DE PIES
	$("#graph_inbound").on("click", function(e) {//GRAFICO DE BARRAS
		$('#group_inbound_select').val("").trigger('liszt:updated');
	}); //INBOUND

	$("#save_button_layout").on("click", function(e) {//ALTERAR O NOME DA LAYOUT
		var layout_name = $("#Layout_Input_name").val();
		if (layout_name === "") {
			$.smallBox({
				title: "Error. Fill the layout name",
				color: "#B22222",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		} else {
			if (new_layout_flag) {
				sql_basic("insert_layout", 0, 0, layout_name);
			} else {
				save_layout();
			}
			$('#dialog_layout').modal('hide');
		}
	});
	$("#delete_button_layout").on("click", function(e) {//APAGAR A LAYOUT E SEUS WALLBOARDS
		sql_basic("remove_layout", idLayout);
	});
	$("#create_button_dialog").on("click", function(e) {//CRIAR OS WALLBOARDS PARA LINHAS E BARRAS
		var chart_name = $("#graph_name").val();
		if (chart_name === "") {
			$.smallBox({
				title: "Error. Fill the chart name",
				color: "#B22222",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		} else {
			manipulate_graph("insert_wbe", 0, chart_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 250, 250, idLayout, $("#update_time").val(), selected_type_graph, 0, 0);
		}
	});
	$("#create_button_pie").on("click", function(e) {//CRIAR OS WALLBOARDS PARA LINHAS E BARRAS
		var feedbacks = "";
		if (!$("#pie_status_select").val() && !($("#checkbox_feedback_pie").is(':checked')))
			$.smallBox({
				title: "Error. No outcome choose",
				color: "#B22222",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		else
		{
			var pie_name = $("#pie_name").val();
			if (pie_name === "") {
				$.smallBox({
					title: "Error. No Pie Chart name choose",
					color: "#B22222",
					timeout: 4000,
					iconSmall: "fa fa-exclamation animated"
				});
			} else {
				if ($("#checkbox_feedback_pie").is(':checked'))
					feedbacks = 1;
				else
					feedbacks = $("#pie_status_select").val();
				feedbacks = "'" + feedbacks + "'";
				switch ($("#pie_opcao").val())
				{
					case "1":
						manipulate_pie("insert_pie", 0, 0, 1, "Pie Chart", $("#pie_timespan").val(), 0, 0, $("#campaign_id_pie option:selected").val(), 0, 0, feedbacks, 0, $("#campaign_id_pie option:selected").text(), $("#pie_feedback_colum_name").val(), pie_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 450, 242, idLayout, 10000, 3);
						break;
					case "2":
						manipulate_pie("insert_pie", 0, 0, 2, "Pie Chart", $("#pie_timespan").val(), 0, $("#grupo_user_pie option:selected").val(), 0, 0, 0, feedbacks, 0, $("#grupo_user_pie option:selected").text(), $("#pie_feedback_colum_name").val(), pie_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 450, 242, idLayout, 10000, 3);
						break;
					case "3":
						manipulate_pie("insert_pie", 0, 0, 3, "Pie Chart", $("#pie_timespan").val(), 0, 0, 0, $("#grupo_inbound_pie option:selected").val(), 0, feedbacks, 0, $("#grupo_inbound_pie option:selected").text(), $("#pie_feedback_colum_name").val(), pie_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 450, 242, idLayout, 10000, 3);
						break;
					case "4":
						manipulate_pie("insert_pie", 0, 0, 4, "Pie Chart", $("#pie_timespan").val(), $("#user_pie option:selected").val(), 0, 0, 0, 0, feedbacks, 0, $("#user_pie option:selected").text(), $("#pie_feedback_colum_name").val(), pie_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 450, 242, idLayout, 10000, 3);
						break;
				}
				$('#dialog_pie').modal('hide');
			}
		}
	});
	$("#create_button_dataTable").on("click", function(e) {//CRIAR OS WALLBOARDS PARA LINHAS E BARRAS
		if (!$("#dataTable_status_select").val() && !($("#checkbox_feedback").is(':checked')))
			$.smallBox({
				title: "Error. No outcome choose",
				color: "#B22222",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		else
		{
			var dt_name = $("#dataTable_name").val();
			if (dt_name === "") {
				$.smallBox({
					title: "Error. No name for datatable",
					color: "#B22222",
					timeout: 4000,
					iconSmall: "fa fa-exclamation animated"
				});
			} else {
				if ($("#checkbox_feedback").is(':checked')) {
					var feedbacks = 1;
				} else {
					var feedbacks = $("#dataTable_status_select").val();
				}
				feedbacks = "'" + feedbacks + "'";
				var limit = 10;
				if ($("#resultado_datatable_1").is(':checked'))
					limit = 15;
				if ($("#resultado_datatable_2").is(':checked'))
					limit = 10;
				if ($("#resultado_datatable_3").is(':checked'))
					limit = 5;
				var mode = 1;
				if ($("#resultado_datatable_1_tipo").is(':checked'))
					mode = 1;
				else if ($("#resultado_datatable_2_tipo").is(':checked'))
					mode = 2;
				else if ($("#resultado_datatable_3_tipo").is(':checked'))
					mode = 3;
				switch ($("#dataTable_opcao").val())
				{
					case "1":
						manipulate_dataTable_top("insert_dataTop", id_wallboard, $("#dataTable_timespan").val(), $("#campaign_id_dataTable").val(), 0, 0, feedbacks, limit, $("#coluna_feedback").val(), 0, dt_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 250, 250, idLayout, 10000, selected_type_graph, $("#campaign_id_dataTable option:selected").text(), 0, mode);
						break;
					case "2":
						manipulate_dataTable_top("insert_dataTop", id_wallboard, $("#dataTable_timespan").val(), 0, $("#grupo_inbound_dataTable").val(), 0, feedbacks, limit, $("#coluna_feedback").val(), 0, dt_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 250, 250, idLayout, 10000, selected_type_graph, $("#grupo_inbound_dataTable option:selected").text(), 0, mode);
						break;
					case "3":
						manipulate_dataTable_top("insert_dataTop", id_wallboard, $("#dataTable_timespan").val(), 0, 0, $("#grupo_user_dataTable").val(), feedbacks, limit, $("#coluna_feedback").val(), 0, dt_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 250, 250, idLayout, 10000, selected_type_graph, $("#grupo_user_dataTable option:selected").text(), 0, mode);
						break;
				}
				$('#dialog_dataTable').modal('hide');
			}
		}
	});
	$("#create_button_inbound").on("click", function(e) {
		var inbound_name = $("#inbound_name").val();
		if (inbound_name === "") {
			$.smallBox({
				title: "Error. No name for Inbound Chart",
				color: "#B22222",
				timeout: 4000,
				iconSmall: "fa fa-exclamation animated"
			});
		} else {
			var linhas_inbound = "";
			linhas_inbound = $("#group_inbound_select").val();
			linhas_inbound = "'" + linhas_inbound + "'";
			if (linhas_inbound)
			{
				manipulate_graph("insert_wbe", 0, inbound_name, Math.round((Math.random() * 500) + 1), Math.round((Math.random() * 250) + 1), 429, 242, idLayout, 10000, 4, linhas_inbound, 0);
				$('#dialog_inbound').modal('hide');
			} else {
				$.smallBox({
					title: "Error. Select one or more Inbound Group(s)",
					color: "#B22222",
					timeout: 4000,
					iconSmall: "fa fa-exclamation animated"
				});
			}
		}
	});
	$("#create_button_dataset").on("click", function(e) {
		var opcao = "insert_dataset";
		if (edit_dataset) {
			edit_dataset = false;
			opcao = "edit_dataset";
		}
		var mode = 1;
		if ($("#resultado_dataset_1").is(':checked'))
			mode = 1;
		if ($("#resultado_dataset_2").is(':checked'))
			mode = 2;
		if ($("#resultado_dataset_3").is(':checked'))
			mode = 3;
		if ($("#linhas_filtro").val() === "1")
			switch ($("#linhas_serie").val())
			{
				case "1":
					switch ($("#chamadas").val())
					{
						case "1":
							//Chamadas Atendidas por user
							manipulate_dataset(opcao, id_dataset, id_wallboard, 1, queries[1][3], "24", $("#user option:selected").val(), 0, 0, 0, mode, 0, "Answered",  $("#user option:selected").attr("value")+"@"+domain, $("#user option:selected").text());
							break;
						case "2":
							//Chamadas Perdidas por user
							manipulate_dataset(opcao, id_dataset, id_wallboard, 2, queries[2][3], "24", $("#user option:selected").val(), 0, 0, 0, mode, 0, "Lost",  $("#user option:selected").attr("value")+"@"+domain, $("#user option:selected").text());
							break;
						case "3":
							//Chamadas Feitas por user
							manipulate_dataset(opcao, id_dataset, id_wallboard, 3, queries[3][3], "24", $("#user option:selected").val(), 0, 0, 0, mode, 0, "Made", $("#user option:selected").attr("value")+"@"+domain, $("#user option:selected").text());
							break;
					}
					break;
				case "2":
					switch ($("#chamadas").val())
					{
						case "1":
							//Chamadas Atendidas por user_group
							manipulate_dataset(opcao, id_dataset, id_wallboard, 4, queries[4][3], "24", 0, $("#user_group option:selected").val(), 0, 0, mode, 0, "Answered", $("#user_group option:selected").attr("value"), $("#user_group option:selected").text());
							break;
						case "2":
							//Chamadas Perdidas por user_group   
							manipulate_dataset(opcao, id_dataset, id_wallboard, 5, queries[5][3], "24", 0, $("#user_group option:selected").val(), 0, 0, mode, 0, "Lost",$("#user_group option:selected").attr("value"), $("#user_group option:selected").text());
							break;
						case "3":
							//Chamadas Feitas por user_group 
							manipulate_dataset(opcao, id_dataset, id_wallboard, 5, queries[6][3], "24", 0, $("#user_group option:selected").val(), 0, 0, mode, 0, "Made", $("#user_group option:selected").attr("value"), $("#user_group option:selected").text());
							break;
					}
					break;
				case "3":
					switch ($("#chamadas").val())
					{
						case "1":
							//Chamadas Atendidas por campanha
							manipulate_dataset(opcao, id_dataset, id_wallboard, 7, queries[7][3], "24", 0, 0, $("#campaign option:selected").val(), 0, mode, 0, "Answered", $("#campaign option:selected").attr("value"), $("#campaign option:selected").text());
							break;
						case "2":
							//Chamadas Perdidas por campanha   
							manipulate_dataset(opcao, id_dataset, id_wallboard, 8, queries[8][3], "24", 0, 0, $("#campaign option:selected").val(), 0, mode, 0, "Lost", $("#campaign option:selected").attr("value"), $("#campaign option:selected").text());
							break;
						case "3":
							//Chamadas Feitas por campanha
							manipulate_dataset(opcao, id_dataset, id_wallboard, 9, queries[9][3], "24", 0, 0, $("#campaign option:selected").val(), 0, mode, 0, "Made", $("#campaign option:selected").attr("value"), $("#campaign option:selected").text());
							break;
					}
					break;
				case "4":
					switch ($("#chamadas").val())
					{
						case "1":
							//Chamadas Atendidas por Total CallCenter
							manipulate_dataset(opcao, id_dataset, id_wallboard, 10, queries[10][3], "24", 0, 0, 0, 0, mode, 0, "Answered", "total call center", 0);
							break;
						case "2":
							//Chamadas Perdidas por Total CallCenter  
							manipulate_dataset(opcao, id_dataset, id_wallboard, 11, queries[11][3], "24", 0, 0, 0, 0, mode, 0, "Lost", "total call center", 0);
							break;
						case "3":
							//Chamadas Feitas por Total CallCenter 
							manipulate_dataset(opcao, id_dataset, id_wallboard, 12, queries[12][3], "24", 0, 0, 0, 0, mode, 0, "Made", "total call center", 0);
							break;
					}
					break;
				case "5":
					switch ($("#chamadas").val())
					{
						case "1":
							//Chamadas Atendidas por Inbound
							manipulate_dataset(opcao, id_dataset, id_wallboard, 13, queries[13][3], "24", 0, 0, 0, $("#inbound").val(), mode, 0, "Answered", $("#inbound option:selected").text(), 0);
							break;
						case "2":
							//Chamadas Perdidas por Inbound
							manipulate_dataset(opcao, id_dataset, id_wallboard, 14, queries[14][3], "24", 0, 0, 0, $("#inbound").val(), mode, 0, "Lost", $("#inbound option:selected").text(), 0);
							break;
						case "3":
							//Chamadas Feitas por Inbound
							manipulate_dataset(opcao, id_dataset, id_wallboard, 15, queries[15][3], "24", 0, 0, 0, $("#inbound").val(), mode, 0, "Made", $("#inbound option:selected").text(), 0);
							break;
					}
					break;
			}

		if ($("#linhas_filtro").val() === "2")
			switch ($("#linhas_serie").val())
			{
				case "1":
					//feedback por user
					manipulate_dataset(opcao, id_dataset, id_wallboard, 16, queries[16][3], "24", $("#user option:selected").val(), 0, 0, 0, mode, $("#status_venda").val(), 0, $("#status_venda option:selected").text(), $("#user option:selected").text()+";"+$("#user option:selected").attr("value")+"@"+domain);
					break;
				case "2":
					//feedback por user_group
					manipulate_dataset(opcao, id_dataset, id_wallboard, 17, queries[17][3], "24", 0, $("#user_group option:selected").val(), 0, 0, mode, $("#status_venda").val(), 0, $("#status_venda option:selected").text(), $("#user_group option:selected").text());
					break;
				case "3":
					//feedback por campanha
					manipulate_dataset(opcao, id_dataset, id_wallboard, 18, queries[18][3], "24", 0, 0, $("#campaign option:selected").val(), 0, mode, $("#status_venda").val(), 0, $("#status_venda option:selected").text(), $("#campaign option:selected").text());
					break;
				case "4":
					//feedback por call center
					manipulate_dataset(opcao, id_dataset, id_wallboard, 19, queries[19][3], "24", 0, 0, 0, 0, mode, $("#status_venda").val(), 0, $("#status_venda option:selected").text(), "Call Center");
					break;
				case "5":
					//feedback por linha inbound
					manipulate_dataset(opcao, id_dataset, id_wallboard, 20, queries[20][3], "24", 0, 0, 0, $("#inbound").val(), mode, $("#status_venda").val(), 0, $("#status_venda option:selected").text(), $("#inbound option:selected").text());
					break;
			}
	});
	load_dados("layout", 0);
	flot_extra_init();
}

$("#add_layout_button").click(function()
{
	$("#Layout_Input_name").val('');
	$("#fullscreen_link").html('');
	new_layout_flag = true;
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
			$("#gao_status").hide();
			$("#gao_chamadas").show();
			break;
		case '2':
			$("#gao_chamadas").hide();
			$("#gao_status").show();
			break;
	}
});
$("#dataTable_opcao").change(function()
{
	$(".dataTable_options").hide();
	switch ($("#dataTable_opcao").val())
	{
		case "1":
			$("#campaign_id_dataTable_div").show();
			break;
		case "2":
			$("#grupo_inbound_dataTable_div").show();
			break;
		case "3":
			$("#grupo_user_dataTable_div").show();
			$("#radio_datatable_div").show();
			break;
	}
});
$("#pie_opcao").change(function()
{
	$(".pie_select").hide();
	switch ($("#pie_opcao").val())
	{
		case "1":
			$("#campaign_id_pie_div").show();
			break;
		case "3":
			$("#grupo_inbound_pie_div").show();
			break;
	}
});
$("#opcao_layout_button").click(function() {
	$("#fullscreen_link").html((document.URL).split(".org")[0] + "/ajax/wallboard_fullscreen.html?id=" + idLayout);
	new_layout_flag = false;
});
function radio_checks() {
	var chamadas = $("#chamadas");
	//INBOUND
	if ($("#resultado_dataset_1").is(':checked'))
	{
		if ($("#linhas_serie").val() === "3")
		{
			$("#linhas_serie").val(0);
			$(".graph_advance_option").hide();
			$("#gao_user").show();
		}
		if (chamadas.val() === "3")
		{
			chamadas.val(0);
		}
		$("#linhas_serie option[value='3']").addClass('hidden');
		$("#linhas_serie option[value='5']").removeClass('hidden');
		$("#chamadas option[value='3']").addClass('hidden');
		$("#chamadas option[value='1']").removeClass('hidden');
	}

//OUTBOUND
	if ($("#resultado_dataset_2").is(':checked'))
	{
		if ($("#linhas_serie").val() === "5")
		{
			$("#linhas_serie").val(0);
			$(".graph_advance_option").hide();
			$("#gao_user").show();
		}
		if (chamadas.val() === "1")
		{
			chamadas.val(2);
		}
		$("#linhas_serie option[value='5']").addClass('hidden');
		$("#linhas_serie option[value='3']").removeClass('hidden');
		$("#chamadas option[value='1']").addClass('hidden');
		$("#chamadas option[value='3']").removeClass('hidden');
	}

	//BLENDED
	if ($("#resultado_dataset_3").is(':checked'))
	{
		if ($("#linhas_serie").val() === "3" || ($("#linhas_serie").val() === "5"))
		{
			$("#linhas_serie").val(0);
			$(".graph_advance_option").hide();
			$("#gao_user").show();
		}
		$("#linhas_serie option[value='3']").addClass('hidden');
		$("#linhas_serie option[value='5']").addClass('hidden');
		$("#chamadas option[value='1']").removeClass('hidden');
		$("#chamadas option[value='3']").removeClass('hidden');
	}
}

$("#resultado_dataset_1").change(function()
{
	radio_checks();
});

$("#resultado_dataset_2").change(function()
{
	radio_checks();
});

$("#resultado_dataset_3").change(function()
{
	radio_checks();
});

function fullScreen()
{
	var window_slave = window.open("../ajax/wallboard_fullscreen.html?id=" + idLayout);
}

function check_save()
{
	save();
}

function save_layout()
{
	var a = get_indice_layout(idLayout);
	layouts[a][1] = $("#Layout_Input_name").val();
	manipulate_dados("edit_layout", idLayout, layouts[a][1], 0, 0, 0, 0, 0, 0);
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
		wbes[i][1] = Math.round(painel.css("left").replace("px", ""));
		wbes[i][2] = Math.round(painel.css("top").replace("px", ""));
		wbes[i][3] = Math.round(painel.width());
		wbes[i][4] = Math.round(painel.height());
		manipulate_dados("edit_WBE", wbes[i][0], 0, wbes[i][1], wbes[i][2], wbes[i][3], wbes[i][4], wbes[i][6], 0);
	}
	update_dropbox_layout();
	$('#label_id_layout').text(layouts[a][0]);
	$("#Layout_Input_name").val(layouts[a][1]);
}

function get_indice_layout(id)
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

function get_indice_wbe(id)
{
	var indice = 0;
	$.each(wbes, function(i, value) {
		if (wbes[i][0] == id) {
			indice = i;
		}
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
	$.each(wbes, function(i, value) {
		var ml = $("#MainLayout");
		switch ((wbes[i][8]))
		{
			case 4://Inbound
				ml.append("<div class='PanelWB ui-widget-content' id='" + wbes[i][0] + "WBE' style='left:" + wbes[i][3] + "px;top:" + wbes[i][4] + "px;" +
					"width:" + wbes[i][5] + "px;height:" + wbes[i][6] + "px'>" +
					"<div class='smart-form'>" +
					"<header style='border-bottom:solid 1px'>" +
					"<i class='fa fa-rss fa-flip-vertical'></i> " + wbes[i][2] +
					"<div class='pull-right'>" +
					"<a href='javascript:void(0);' style='padding:1px 4px' class='btn btn-danger delete_button' title='Delete'>Delete <i class='fa fa-times'></i></a>" +
					"</div></header>" +
					"<fieldset><section id='grid_content'></section></fieldset></div>");
				$("#" + wbes[i][0] + "WBE .delete_button:last").data("wbe_id", wbes[i][0]);
				break;
			case 5://dataTop
				ml.append("<div class='PanelWB ui-widget-content' id='" + wbes[i][0] + "WBE' style='left:" + wbes[i][3] + "px;top:" + wbes[i][4] + "px;" +
					"width:" + wbes[i][5] + "px;height:" + wbes[i][6] + "px'>" +
					"<div class='smart-form'>" +
					"<header style='border-bottom:solid 1px'>" +
					"<i class='fa fa-user'></i> " + wbes[i][2] +
					"<div class='pull-right'>" +
					"<a href='javascript:void(0);' style='padding:1px 4px' class='btn btn-danger delete_button' title='Delete'>Delete <i class='fa fa-times'></i></a>" +
					"</div></header>" +
					"<fieldset id='grid_content" + wbes[i][0] + "'></fieldset></div>");
				$("#" + wbes[i][0] + "WBE .delete_button:last").data("wbe_id", wbes[i][0]);
				$("#grid_content" + wbes[i][0]).append("<section><div class='col-xs-12'>Filter -> " + wbes[i][9][0].param1 + "</div>" +
					"<div class='col-xs-12'>Column Name -> " + wbes[i][9][0].custom_colum_name + "</div></section>");
				break;
			case 3://Pie
				ml.append("<div class='PanelWB ui-widget-content' id='" + wbes[i][0] + "WBE' style='left:" + wbes[i][3] + "px;top:" + wbes[i][4] + "px;" +
					"width:" + wbes[i][5] + "px;height:" + wbes[i][6] + "px'>" +
					"<div class='smart-form'>" +
					"<header style='border-bottom:solid 1px'>" +
					"<i class='fa fa-renren'></i> " + wbes[i][2] +
					"<div class='pull-right'>" +
					"<a href='javascript:void(0);' style='padding:1px 4px' class='btn btn-danger delete_button' title='Delete'>Delete <i class='fa fa-times'></i></a>" +
					"</div></header>" +
					"<fieldset id='grid_content" + wbes[i][0] + "'></fieldset></div>");
				var param1 = "";
				var param2 = "";
				if (wbes[i][9][0]) {
					param1 = wbes[i][9][0].param1;
					param2 = wbes[i][9][0].param2;
				}
				$("#" + wbes[i][0] + "WBE .delete_button:last").data("wbe_id", wbes[i][0]);
				$("#grid_content" + wbes[i][0]).append("<section><div class='col-xs-12'>Filter -> " + param1 + "</div>" +
					"<div class='col-xs-12'>Column Name -> " + param2 || "" + "</div></section>");
				break;
			default:
				var icon;
				if (wbes[i][8] === 1) {
					icon = "fa fa-align-left";
				} else {
					icon = "fa fa-bar-chart-o";
				}
				ml.append("<div class='PanelWB ui-widget-content' id='" + wbes[i][0] + "WBE' style='left:" + wbes[i][3] + "px;top:" + wbes[i][4] + "px;" +
					"width:" + wbes[i][5] + "px;height:" + wbes[i][6] + "px'>" +
					"<div class='smart-form'>" +
					"<header style='border-bottom:solid 1px'>" +
					"<i class='" + icon + "'></i> " + wbes[i][2] +
					"<div class='pull-right'>" +
					"<a href='javascript:void(0);' style='padding:1px 4px; margin-right:0.3em' class='btn btn-info add_dataset_button' title='Add new series'>Add new series <i class='fa fa-plus txt-color-white'></i></a>" +
					"<a href='javascript:void(0);' style='padding:1px 4px' class='btn btn-danger delete_button' title='Delete'>Delete <i class='fa fa-times txt-color-white'></i></a>" +
					"</div></header>" +
					"<fieldset id='grid_content'></fieldset></div>");
				$("#" + wbes[i][0] + "WBE .add_dataset_button:last").data("wbe_id", wbes[i][0]);
				$("#" + wbes[i][0] + "WBE .delete_button:last").data("wbe_id", wbes[i][0]);
				var grid_content = $("#" + wbes[i][0] + "WBE #grid_content");
				$.each(wbes[i][9], function(a, value) {
					grid_content.append("<section style='margin-bottom:5px'><div class='btn-group dropdown'>" +
						"<button  style='padding:1px 4px' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>" +
						"<i class='fa fa-cog'></i><span style='margin-left:3px;margin-right:3px'>" + wbes[i][9][a].opcao_query + "</span><span class='caret'></span></button>" +
						"<ul class='dropdown-menu'>" +
						"<li><a href='javascript:void(0);' class='edit_dataset_button'><i class='fa fa-edit'></i> Edit dataset</a></li>" +
						"<li><a href='javascript:void(0);' class='delete_dataset_button'><i class='fa fa-trash-o'></i> Remove dataset</a></li>" +
						"</ul></div></section>");
					grid_content.find(".edit_dataset_button:last").data("dataset_id", wbes[i][9][a].id).data("id", wbes[i][0]);
					grid_content.find(".delete_dataset_button:last").data("dataset_id", wbes[i][9][a].id);
				});
				break;
		}
		var painel = $("#MainLayout  #" + wbes[i][0] + "WBE");
		painel.draggable({containment: "#MainLayout", stop: check_save});
		switch ((wbes[i][8]))
		{
			case "4"://inbound
				painel.resizable({
					containment: "#MainLayout",
					aspectRatio: 16 / 9,
					maxHeight: 480,
					maxWidth: 880,
					minHeight: 240,
					minWidth: 245, stop: check_save});
				break;
			case "3"://Pie
				painel.resizable({
					containment: "#MainLayout",
					maxHeight: 480,
					maxWidth: 880,
					minHeight: 220,
					minWidth: 325, stop: check_save});
				break;
			case "5"://Tabela Top
				painel.resizable({
					containment: "#MainLayout",
					maxHeight: 480,
					maxWidth: 880,
					minHeight: 230,
					minWidth: 240, stop: check_save});
				break;
			default://Linhas e Barras
				painel.resizable({
					containment: "#MainLayout",
					maxHeight: 480,
					maxWidth: 880,
					minHeight: 220,
					minWidth: 262, stop: check_save});
				break;
		}
	});
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

function manipulate_dataset(Opcao, Id, id_Wallboard, Codigo_query, Opcao_query, Tempo, User, User_group, Campaign_id, Linha_inbound, Mode, Status_feedback, Chamadas, Param1, Param2)
{
	$.post("../php/wallboardRequests.php", {action: Opcao, id: Id, id_wallboard: id_Wallboard, codigo_query: Codigo_query, opcao_query: Opcao_query, tempo: Tempo, user: User, user_group: User_group, campaign_id: Campaign_id, linha_inbound: Linha_inbound, mode: Mode, status_feedback: Status_feedback, chamadas: Chamadas, param1: Param1, param2: Param2},
	function(data)
	{
		load_dados("wbe", idLayout);
	}, "json");
}

function load_dados(opcao, id_layouT)
{
	$.post("../php/wallboardRequests.php", {action: opcao, id_layout: id_layouT},
	function(data)
	{
		$("#MainLayout").empty();
		if (data === null)
		{
			wbes = []; //limpa o array dos wallboards, senao dps de se passar de uma layout com elementos para esta, como o wbes ainda contem os elementos do layout anterior, eles sao criados outra vez
			if (layouts.length <= 0)//se n ha layout, bloqueia os botoes e limpa a dropbox
			{
				$("#opcao_layout_button").addClass("disabled");
				$("#toolbar .toolbar_button").addClass("disabled");
				$.smallBox({
					title: "No Layout",
					color: "#D8C74E",
					timeout: 4000,
					iconSmall: "fa fa-exclamation animated"
				});
			}
			else
			{
				$.smallBox({
					title: "No Wallboard",
					color: "#D8C74E",
					timeout: 4000,
					iconSmall: "fa fa-exclamation animated"
				});
				$("#opcao_layout_button").removeClass("disabled");
				$("#toolbar .toolbar_button").removeClass("disabled");
			}
			return false;
		}
		if (opcao === "layout")//Load dados layout
		{
			layouts = [];
			$.each(data, function(index, value) {
				layouts.push([this[0], this[1]]);
			});
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
function sql_basic(opcao, id_layout, id_wbe, name)
{
	var params = {action: opcao, id_layout: id_layout, id: id_wbe};
	if (name !== null) {
		params["name"] = name;
	}
	$.post("../php/wallboardRequests.php", params,
		function(data)
		{
			if (opcao === "insert_layout")
			{
				load_dados("layout", 0);
				idLayout = $("#LayoutSelector").val();
			}
			if (opcao === "remove_layout")
			{
				load_dados("layout", 0);
				$.each(layouts, function(i, value)
				{
					if (layouts[i][0] === idLayout) {
						layouts.splice(i, 1);
					}
				});
			}
			if (opcao === "delete_WBE")
			{
				$("#" + id_wbe + "WBE").remove();
				load_dados('wbe', idLayout);
			}
		}, "json");
}
//wallboard
function manipulate_dados(opcao, Id, Name, Pos_x, Pos_y, Width, Height, id_layout)
{
	$.post("../php/wallboardRequests.php", {action: opcao, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_layout},
	function(data)
	{
		if (opcao === "edit_layout")
			update_dropbox_layout();
	}, "json");
}
//graphs
function manipulate_graph(Opcao, Id, Name, Pos_x, Pos_y, Width, Height, id_Layout, Update_time, Graph_type, Param1, Param2)
{
	$.post("../php/wallboardRequests.php", {action: Opcao, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_Layout, update_time: Update_time, graph_type: Graph_type, param1: Param1, param2: Param2},
	function(data)
	{
		load_dados("wbe", idLayout);
	}, "json");
}

function manipulate_dataTable_top(Opcao, Id_wallboard, Tempo, Campanha, Grupo_inbound, Grupo_user, Status_feedback, Limit, Custom_colum_name, Id, Name, Pos_x, Pos_y, Width, Height, id_Layout, Update_time, Graph_type, Param1, Param2, Mode)
{
	$.post("../php/wallboardRequests.php", {action: Opcao, id_wallboard: Id_wallboard, tempo: Tempo, campanha: Campanha, grupo_inbound: Grupo_inbound, grupo_user: Grupo_user, status_feedback: Status_feedback, limit: Limit, custom_colum_name: Custom_colum_name, id: Id, name: Name, pos_x: Pos_x, pos_y: Pos_y, width: Width, height: Height, id_layout: id_Layout, update_time: Update_time, graph_type: Graph_type, param1: Param1, param2: Param2, mode: Mode},
	function(data)
	{
		if (Opcao === "insert_dataTop") {
			load_dados("wbe", idLayout);
		}
	}, "json");
}

function manipulate_pie(Opcao, Id, id_Wallboard, Codigo_query, Opcao_query, Tempo, User, User_group, Campaign_id, Linha_inbound, Mode, Status_feedback, Chamadas, Param1, Param2, Name, Pos_x, Pos_y, Width, Height, id_Layout, Update_time, Graph_type)
{
	$.post("../php/wallboardRequests.php", {action: Opcao,
		id: Id,
		id_wallboard: id_Wallboard,
		codigo_query: Codigo_query,
		opcao_query: Opcao_query,
		tempo: Tempo,
		user: User,
		user_group: User_group,
		campaign_id: Campaign_id,
		linha_inbound: Linha_inbound,
		mode: Mode,
		status_feedback: Status_feedback,
		chamadas: Chamadas,
		param1: Param1,
		param2: Param2,
		name: Name,
		pos_x: Pos_x,
		pos_y: Pos_y,
		width: Width,
		height: Height,
		id_layout: id_Layout,
		update_time: Update_time,
		graph_type: Graph_type},
	function(data)
	{
		if (Opcao === "insert_pie")
			load_dados("wbe", idLayout);
	}, "json");
}

function flot_extra_init()
{
	var gao_u = $("#gao_user");
	var gao_ug = $("#gao_userGroup");
	var gao_c = $("#gao_campaign");
	var gao_total_cc = $("#gao_total_cc");
	var gao_i = $("#gao_inbound");
	var gao_sv = $("#gao_status");
	var gao_chamadas = $("#gao_chamadas");
	gao_u.append("<label class='select'><select id='user'></select><i></i></label>");
	flot_extra("user");
	gao_ug.append("<label class='select'><select id='user_group'></select><i></i></label>");
	flot_extra("user_group");
	gao_c.append("<label class='select'><select id='campaign'></select><i></i></label>");
	flot_extra("campaign");
	gao_total_cc.append("<label class='select'><select id='total_cc'></select><i></i></label>");
	gao_i.append("<label class='select'><select id='inbound'></select><i></i></label>");
	flot_extra("inbound");
	gao_chamadas.append("<label class='select'><select id='chamadas'>" +
		"<option value='1'>Answered</option>" +
		"<option value='2'>Lost</option>" +
		"<option value='3'>Made</option></select><i></i></label>");
	$("#linhas_serie option[value='3']").addClass("hidden");
	$("#chamadas option[value='3']").addClass("hidden");
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
	$.post("../php/wallboardRequests.php", {action: opcao, param1: Param1, domain: domain},
	function(data)
	{
		if (opcao === "user")
		{
			var object = $([]).add($("#user"));
			$.each(data, function(index, value) {
				object.append(new Option(data[index].full_name, data[index].user));
			});
			object.data("placeholder", "Choose one or more options");
		}
		if (opcao === "user_group")
		{
			var object = $([]).add($("#user_group")).add($("#grupo_user_dataTable"));
			$.each(data, function(index, value) {
				if (value !== "") {
					object.append(new Option(data[index].group_name, data[index].user_group));
				}
			});
			object.data("placeholder", "Choose one or more options");
		}
		if (opcao === "campaign")
		{
			var object = $([]).add($("#campaign")).add($("#campaign_id_dataTable")).add($("#campaign_id_pie"));
			$.each(data, function(index, value) {
				object.append(new Option(this.campaign_name, this.campaign_id));
			});
			object.data("placeholder", "Choose one or more options");
		}
		if (opcao === "inbound")
		{
			var object = $([]).add($("#group_inbound_select")).add($("#inbound")).add($("#grupo_inbound_dataTable")).add($("#grupo_inbound_pie"));
			$.each(data, function(index, value) {
				object.append(new Option(this.name, this.id));
			});
			object.data("placeholder", "Choose one or more options");
		}
	}, "json");
}