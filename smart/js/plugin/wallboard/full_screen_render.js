var wbes = [];
var layout;
var graficos = new graph();
var api = new mongo();

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
		vars[key] = value;
	});
	return vars;
}

$(document).ready(function() {
	$("#MainLayout").css("width", "100%").css("height", "100%").css("position", "absolute").css("background-color", "#F5F5F5").css("font-size", "1.35em");
	layout = getUrlVars()["id"];
	//parameter
	$.post("../php/wallboardRequests.php", {action: "wbe", id_layout: layout},
	function(data)
	{
		wbes = [];
		$.each(data, function(index, value) {
			wbes.push([this.id, this.id_layout, this.name, this.pos_x, this.pos_y, this.width, this.height, this.update_time, this.graph_type, this.dataset]);
		});
		var temp_window = $(window);
		$.each(wbes, function(i, value) {
			var left = (wbes[i][3] * temp_window.width()) / $("#MainLayout").width();
			var top = (wbes[i][4] * temp_window.height()) / $("#MainLayout").height();
			var width = (wbes[i][5] * temp_window.width()) / $("#MainLayout").width();
			var height = (wbes[i][6] * temp_window.height()) / $("#MainLayout").height();
			$("#MainLayout")
				.append("<div class='PanelWB ui-widget-content panel panel-primary' id='" + wbes[i][0] + "Main' style='position:absolute;left:" + left + "px;top:" + top + "px;" +
				"width:" + width + "px;height:" + height + "px'>" +
				"<div class='panel-heading'><div class='panel-title'>" + wbes[i][2] + "<div style='float:right'>" +
				"<span class='letter_button'>" +
				"<input style='width:15px' id='letter_size_popover" + wbes[i][0] + "' type='text'>" +
				"</span><span id='right_title" + wbes[i][0] + "'></span>" +
				"</div></div></div>" +
				"<div id='" + wbes[i][0] + "WBEGD'>" +
				"<div id='" + wbes[i][0] + "WBE' style='width:" + (width - 20) + "px;height:" + (height - 75) + "px;padding:0px;' title='Refresh Time: " + (wbes[i][7] / 1000) + " sec.'></div>" +
				"</div></div>");
			var painel = $("#" + wbes[i][0] + "Main");
			painel.data("id", wbes[i][0] + "Main");
			painel.draggable({containment: '#MainLayout'});
			if (wbes[i][8] !== "5") {
				if ($.cookie(wbes[i][0] + "Main") > 0) {
					painel.data("letter_size", $.cookie(wbes[i][0] + "Main"));
					painel.css("font-size", $.cookie(wbes[i][0] + "Main"));
				} else {
					painel.data("letter_size", "18");
					$.cookie(wbes[i][0] + "Main", 18);
				}
			}
			var timep = $("#letter_size_popover" + wbes[i][0]);
			timep.val($.cookie(wbes[i][0] + "Main"));
			timep.spinner({
				min: 0,
				max: 99,
				numberFormat: "C",
				spin: function(evt, ui) {
					var b = $(this).closest(".PanelWB");
					b.data().letter_size = ui.value;
					b.css("font-size", b.data().letter_size);
					$.cookie(b.data().id, b.data().letter_size);
				}
			});
			$(".ui-spinner:last").attr("title", "Select size");
			if (wbes[i][8] === 1)//line
			{
				genericCharts(wbes[i]);
			}
			if (wbes[i][8] === 2)//bar
			{
				genericCharts(wbes[i]);
			}
			if (wbes[i][8] === 3)//pie
			{
				plot_pie(wbes[i]);
			}
			if (wbes[i][8] === 4)//Inbound stuff
			{
				inbound_wallboard(wbes[i]);
			}
			if (wbes[i][8] === 5)//Top agent table
			{
				painel.css("height", "auto");
				dataTable_top(wbes[i]);
			}
		});
	}, "json");
	setInterval("location.reload(true)", 120000);
});

$(document).on("mouseenter", ".PanelWB", function(e) {
	$(".letter_button").stop().fadeIn(600);
});
$(document).on("mouseleave", ".PanelWB", function(e) {
	$(".letter_button").hide();
});

//BAR GRAPH ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
function genericCharts(wb_data)
{
	//mode=1 (inbound) ,mode=2 (outbound) ,mode=3(blended)
	$.each(wb_data[9], function(i, value) {
		var CampaignID = 0;
		switch (value.codigo_query) {
			case 1: //User answered
				switch (value.mode) {
					case "1":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&user=' + value.param1]}}, function(data1) {
								if (data1.length > 0) {
									var arr;
									$.each(data1, function(i, obj) {
										arr.push({
											x: value.param2 + " Answered Calls",
											y: obj.calls
										});
									});
									$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
									if (wb_data[8] === 1) { //line chart
										graficos.line("fig_" + value.id, arr);
									} else { //bar chart
										graficos.bar("fig_" + value.id, arr);
									}
								}
							});
						}, 'json');
						break;
					case "2":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&user=' + value.param1]}}, function(data1) {
								if (data1.length > 0) {
									var arr;
									$.each(data1, function(i, obj) {
										arr.push({
											x: value.param2 + " Answered Calls",
											y: obj.calls
										});
									});
									$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
									if (wb_data[8] === 1) { //line chart
										graficos.line("fig_" + value.id, arr);
									} else { //bar chart
										graficos.bar("fig_" + value.id, arr);
									}
								}
							});
						});
						break;
					case "3":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							var statuses = data.human.join(',');
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&user=' + value.param1]}}, function(data1) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&user=' + value.param1]}}, function(data2) {
									if (data2.length > 0) {
										var arr;
										//HOW TO BLEND?
//									$.each(data1, function(i, obj) {
//										arr.push({
//											x: value.param2 + " Answered Calls",
//											y: obj.calls
//										});
//									});
										$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
										if (wb_data[8] === 1) { //line chart
											graficos.line("fig_" + value.id, arr);
										} else { //bar chart
											graficos.bar("fig_" + value.id, arr);
										}
									}
								});
							});
						});
				}
				break;
			case 2: //User lost calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&user=' + value.param1]}}, function(data2) {
									//missing way to compare user and subtract total calls to answered calls
									if (data2.length > 0) {
										var arr;
										$.each(data2, function(i, obj) {
											arr.push({
												x: value.param2 + " Lost Calls",
												y: obj.calls
											});
										});
										$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
										if (wb_data[8] === 1) { //line chart
											graficos.line("fig_" + value.id, arr);
										} else { //bar chart
											graficos.bar("fig_" + value.id, arr);
										}
									}
								});
							}, 'json');
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&user=' + value.param1]}}, function(data2) {
									//missing way to compare user and subtract total calls to answered calls
									if (data2.length > 0) {
										var arr;
										$.each(data2, function(i, obj) {
											arr.push({
												x: value.param2 + " Lost Calls",
												y: obj.calls
											});
										});
										$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
										if (wb_data[8] === 1) { //line chart
											graficos.line("fig_" + value.id, arr);
										} else { //bar chart
											graficos.bar("fig_" + value.id, arr);
										}
									}
								});
							});
						});

						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data3) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								var statuses = data.human.join(',');
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&user=' + value.param1]}}, function(data1) {
									//callback com os dados
									api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&user=' + value.param1]}}, function(data2) {
										if (data2.length > 0) {
											var arr;
											//HOW TO BLEND?
//									$.each(data1, function(i, obj) {
//										arr.push({
//											x: value.param2 + " Lost Calls",
//											y: obj.calls
//										});
//									});
											$("#" + wb_data[0] + "WBE").append("<figure id='fig_" + value.id + "'></figure>");
											if (wb_data[8] === 1) { //line chart
												graficos.line("fig_" + value.id, arr);
											} else { //bar chart
												graficos.bar("fig_" + value.id, arr);
											}
										}
									});
								});
							});
						});
				}
				break;
			case 3: //User made calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user=' + value.param1]}}, function(data2) {
								//callback com os dados
							});
						});
				}
				break;
			case 4: //User group calls answered
				switch (value.mode) {
					case "1":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&group=' + value.param1]}}, function(data1) {
								//callback com os dados
							});
						}, 'json');
						break;
					case "2":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&group=' + value.param1]}}, function(data1) {
								//callback com os dados
							});
						});
						break;
					case "3":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							var statuses = data.human.join(',');
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&group=' + value.param1]}}, function(data1) {
								//callback com os dados
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&group=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});
				}
				break;
			case 5: //User group calls lost
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user_group=' + value.param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&group=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							}, 'json');
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user_group=' + value.param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&group=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});

						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['user_group=' + value.param1]}}, function(data3) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								var statuses = data.human.join(',');
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&group=' + value.param1]}}, function(data1) {
									//callback com os dados
									api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&group=' + value.param1]}}, function(data2) {
										//callback com os dados
									});
								});
							});
						});
				}
				break;
			case 6: //User group calls made
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['group=' + value.param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['group=' + value.param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['group=' + value.param1]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['group=' + value.param1]}}, function(data2) {
								//callback com os dados
							});
						});
				}
				break;
			case 7: //Campaign answered calls
				var CampaignID = value.param1;
				switch (value.mode) {
					case "1":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + value.param1]}}, function(data1) {
								//callback com os dados
							});
						}, 'json');
						break;
					case "2":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + value.param1]}}, function(data1) {
								//callback com os dados
							});
						});
						break;
					case "3":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							var statuses = data.human.join(',');
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + value.param1]}}, function(data1) {
								//callback com os dados
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});
				}
				break;
			case 8: //Campaign lost calls
				var CampaignID = value.param1;
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + value.param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							}, 'json');
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + value.param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});

						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + value.param1]}}, function(data3) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								var statuses = data.human.join(',');
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + value.param1]}}, function(data1) {
									//callback com os dados
									api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + value.param1]}}, function(data2) {
										//callback com os dados
									});
								});
							});
						});
				}
				break;
			case 9: //Campaign made calls
				var CampaignID = value.param1;
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data2) {
								//callback com os dados
							});
						});
				}
				break;
			case 10: //Call Center answered calls
				switch (value.mode) {
					case "1":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',')]}}, function(data1) {
								//callback com os dados
							});
						}, 'json');
						break;
					case "2":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',')]}}, function(data1) {
								//callback com os dados
							});
						});
						break;
					case "3":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							var statuses = data.human.join(',');
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: "by=status=" + statuses}}, function(data1) {
								//callback com os dados
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: "by=status=" + statuses}}, function(data2) {
									//callback com os dados
								});
							});
						});
				}
				break;
			case 11: //Call Center lost calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',')]}}, function(data2) {
									//callback com os dados
								});
							}, 'json');
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',')]}}, function(data2) {
									//callback com os dados
								});
							});
						});

						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data3) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								var statuses = data.human.join(',');
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses]}}, function(data1) {
									//callback com os dados
									api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses]}}, function(data2) {
										//callback com os dados
									});
								});
							});
						});
				}
				break;
			case 12: //Call Center made calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ""}}, function(data2) {
								//callback com os dados
							});
						});
				}
				break;
			case 13: //Inbound answered calls
				switch (value.mode) {
					case "1":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + wb_data[9].param1]}}, function(data1) {
								//callback com os dados
							});
						}, 'json');
						break;
					case "2":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + wb_data[9].param1]}}, function(data1) {
								//callback com os dados
							});
						});
						break;
					case "3":
						$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
							var statuses = data.human.join(',');
							api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + wb_data[9].param1]}}, function(data1) {
								//callback com os dados
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + 'campaign=' + wb_data[9].param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});
				}
				break;
			case 14: //Inbound lost calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + wb_data[9].param1]}}, function(data2) {
									//callback com os dados
								});
							}, 'json');
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + data.human.join(',') + '&campaign=' + wb_data[9].param1]}}, function(data2) {
									//callback com os dados
								});
							});
						});

						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data3) {
							$.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
								var statuses = data.human.join(',');
								api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + wb_data[9].param1]}}, function(data1) {
									//callback com os dados
									api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + statuses + '&campaign=' + wb_data[9].param1]}}, function(data2) {
										//callback com os dados
									});
								});
							});
						});
				}
				break;
			case 15: //Inbound made calls
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['campaign=' + wb_data[9].param1]}}, function(data2) {
								//callback com os dados
							});
						});
				}
				break;
			case 16: //User outcomes
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&user=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&user=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&user=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&user=' + wb_data[9].param2]}}, function(data2) {
								//callback com os dados
							});
						});
						break;
				}
				break;
			case 17: //User group outcomes
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&group=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&group=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&group=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&group=' + wb_data[9].param2]}}, function(data2) {
								//callback com os dados
							});
						});
						break;
				}
				break;
			case 18: //Campaign outcomes
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data2) {
								//callback com os dados
							});
						});
						break;
				}
				break;
			case 19: //Call center outcomes
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1]}}, function(data2) {
								//callback com os dados
							});
						});
						break;
				}
				break;
			case 20: //Inbound outcomes
				switch (value.mode) {
					case "1":
						api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "2":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
						});
						break;
					case "3":
						api.get({datatype: 'calls', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data1) {
							//callback com os dados
							api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: '1890-01-04T00:00', end: '3014-02-04T23:00'}, by: {calls: ['by=status=' + wb_data[9].param1 + '&campaign=' + wb_data[9].param2]}}, function(data2) {
								//callback com os dados
							});
						});
						break;
				}
				break;
		}
	});
}

//øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø
//PIE GRAPH ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««

function plot_pie(data) {
	var wbe = data;
	var updation;
	var painel = $("#" + wbe[0] + "WBE");
	var painer_content = $("#" + wbe[0] + "WBEGD");
	painer_content.css("overflow-y", "auto").css("overflow-x", "hidden");
	if (wbe[9][0].status_feedback === "1")
		var feedbacks_string = "1";
	else
	{
		var feedbacks = wbe[9][0].status_feedback.split(',');
		var feedbacks_string = "";
		if (feedbacks.length > 1) {
			feedbacks_string = "status='" + feedbacks[0] + "'";
			for (var i = 1; i < feedbacks.length; i++) {
				feedbacks_string = feedbacks_string + " or status='" + feedbacks[i] + "'";
			}
		}
		else
			feedbacks_string = "status='" + feedbacks[0] + "'";
	}
	var right_title = $("#right_title" + wbe[0]);
	right_title.text(wbe[9][0].param2 || " Outcome" + " / " + wbe[9][0].param1);
	get_values_pie();
	function get_values_pie()
	{
		var data1 = [];
		$.post("../php/wallboardRequests.php", {action: "3", status: feedbacks_string, opcao: wbe[9][0].codigo_query, tempo: wbe[9][0].tempo, campaign_id: wbe[9][0].campaign_id,
			user_group: wbe[9][0].user_group, linha_inbound: wbe[9][0].linha_inbound, user: wbe[9][0].user},
		function(data)
		{
			if (data === null)
			{
				clearTimeout(updation);
				painel.remove();
				$("#" + wbe[0] + "Main").remove();
				//$.jGrowl("Pie Graph " + wbe[2] + " doesn't present results", {life: 20000});
				return false;
			}
			var i = 0;
			$.each(data, function(index, value) {
				data1.push({label: ((this.count) + " -- " + this.status_name), data: +this.count});
				i++;
			});
			if (i === 0)//se so houver 1 resultado ele n faz render, entao adiciona-se 1 elemento infimo
				data1.push({label: ("zero"), data: 0.001});
			$.plot(painel, data1, {
				series: {
					pie: {
						show: true,
						radius: 1,
						combine: {
							color: '#999',
							threshold: 0.01,
							label: "Others"},
						label: {
							show: true,
							radius: 2 / 3,
							formatter: function(label, series) {
								return '<div style="font-size:11px;text-align:center;color:black;"><label class="label label-info">' + Math.round(series.percent) + '%</label></div>';
							},
							threshold: 0.02
						}
					}
				},
				legend: {
					show: true
				},
				grid: {
					hoverable: false,
					clickable: false
				}});
			updation = setTimeout(get_values_pie, wbe[7]);
		}, "json");
	}
}
//øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø
//INBOUND WALLBOARD  »»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»
function inbound_wallboard(data)
{
	var ready = 0;
	var queue = 0;
	var paused = 0;
	var incall = 0;
	var updation;
	var wbe = data;
	var id = data[0];
	var panel = $("#" + wbe[0] + "Main");
	var font_size = ((panel.width() / 50) + (panel.height() / 110));
	panel.empty();
	panel.append("<div style='height:98%;font-size:" + font_size + "px;background-color:rgb(210, 215, 215);padding-left:1%;padding-right:1%;padding-top:1%'" +
		" class='legend_inbound' title='Refresh Time: " + (wbe[7] / 1000) + " sec.'>" +
		"<div><label class='inbound_title'>" + wbe[2] + "</label></div>" + //titulo do inbound
		"<table style='height:80%;width:100%;'>" +
		//top                    
		"<tr><td>" +
		"<div class='inbound_grid_div'>" +
		"<div class='inbound_grid_title'><label>Received Calls</label></div>" +
		"<div class='inbound_grid_content'><label id='chamadas_totais" + id + "'></label></div>" +
		"</div></td>" +
		"<td>" +
		"<div class='inbound_grid_div'>" +
		"<div class='inbound_grid_title'><label>Answered Calls</label></div>" +
		"<div class='inbound_grid_content'><label id='chamadas_atendidas" + id + "'></label></div>" +
		"</div</td>" +
		"<td>" +
		"<div class='inbound_gri_div'>" +
		"<div class='inbound_grid_title'><label>Lost Calls</label></div>" +
		"<div class='inbound_grid_content'><label id='chamadas_perdidas" + id + "'></label></div>" +
		"</div></td>" +
		"<td>" +
		"<div class='inbound_grid_div'>" +
		"<div class='inbound_grid_title'><label>Holded Calls</label></div>" +
		"<div class='inbound_grid_content'><label id='chamadas_espera" + id + "'></label></div>" +
		"</div></td></tr>" +
		//left/right    
		"<tr><td>" +
		//gra"<td>"
		"<div class='inbound_grid_div'>" +
		"<div class='inbound_grid_title'><label id='sla1_title" + id + "'>SLA1</label></div>" +
		"<div class='inbound_grid_content'><label id='sla1" + id + "'></label></div>" +
		"</div></td>" +
		"<td style='vertical-align:top'>" +
		"<div style='top:80%;right:1%;position:absolute;z-index:10;background-color:#FFFFFF;opacity:0.75' id='legend_div" + id + "'></div>" +
		"<div style='width:70%;height:55%;position:absolute;' id='plot_inbound" + id + "'></div>" +
		"</td></tr>" +
		"<tr><td>" +
		"<div class='inbound_grid_div'>" +
		"<div class='inbound_grid_title'><label>TMA</label></div>" +
		"<div class='inbound_grid_content'><label id='tma1" + id + "'></label>/div>" +
		"</td></tr></table></div>");
	get_values_inbound();
	function get_values_inbound()
	{
		$.post("../php/wallboardRequests.php", {action: "get_agents", linha_inbound: wbe[9][0].linha_inbound},
		function(data1)
		{
			ready = 0;
			queue = 0;
			paused = 0;
			incall = 0;
			$.each(data1, function(a, value)
			{
				switch (data1[a])
				{
					case "READY":
						ready++;
						break;
					case "CLOSER":
						ready++;
						break;
					case"PAUSED":
						paused++;
						break;
					case"INCALL":
						incall++;
						break;
				}
			});
			$.post("../php/wallboardRequests.php", {action: "get_calls_queue", linha_inbound: wbe[9][0].linha_inbound},
			function(data4)
			{
				$.each(data4, function(index, value)
				{
					queue = +data4[0];
				});
				$.post("../php/wallboardRequests.php", {action: "4", linha_inbound: wbe[9][0].linha_inbound},
				function(data3)
				{
					var chamadas_recebidas = data3[0].chamadas_recebidas;
					var tma1 = data3[0].tma1;
					var chamadas_atendidas_val = data3[0].chamadas_atendidas;
					var chamadas_perdidas_val = data3[0].chamadas_perdidas;
					var tma_todas_chamadas = 0;
					if (data3[0].tma > 0)
					{
						var totalSec = data3[0].tma;
						totalSec = Math.round(totalSec / chamadas_atendidas_val);
						if (/^\d+$/.test(totalSec))
							tma_todas_chamadas = secondstotime(totalSec);
					}
					$.post("../php/wallboardRequests.php", {action: "inbound_groups_info", group_id: wbe[9][0].linha_inbound},
					function(data5)
					{
						answer_sec_pct_rt_stat_one = +data5[0].answer_sec_pct_rt_stat_one;
						answer_sec_pct_rt_stat_two = +data5[0].answer_sec_pct_rt_stat_two;
//update dos valores na table
						var chamadas_totais_obj = $("chamadas_totais" + id);
						chamadas_totais_obj.html(chamadas_totais_obj.html() + chamadas_recebidas);

						var chamadas_atendidas_obj = $("chamadas_atendidas" + id);
						chamadas_atendidas_obj.html(chamadas_atendidas_obj.html() + chamadas_atendidas_val)
						var chamadas_perdidas_obj = $("chamadas_perdidas" + id);

						if (chamadas_perdidas_val !== "0" && (chamadas_perdidas_val / chamadas_recebidas) > 0)
							chamadas_perdidas_obj.html(chamadas_perdidas_val + "-" + Math.round((chamadas_perdidas_val / chamadas_recebidas) * 100) + "%");
						else
							chamadas_perdidas_obj.html(chamadas_perdidas_val + "- 0%");
						var chamadas_espera_obj = $("chamadas_espera" + id);
						chamadas_espera_obj.html(queue);
						var tma1_element = $("tma1" + id);
						tma1_element.html(tma_todas_chamadas);
						var sla1 = $("sla1" + id);
						var sla1_title = $("sla1_title" + id);
						if (tma1 > 0)
						{
							sla1.html(Math.round(tma1) + "%");
							sla1_title.html("SLA1->" + Math.round(answer_sec_pct_rt_stat_one) + "sec");
						} else {
							sla1.html(0);
						}
						var painel = $("#plot_inbound" + id);
						var data_array = [];
						data_array.push({label: ready + " - Available Agents", data: ready});
						data_array.push({label: paused + " - Unavailable Agents", data: paused});
						data_array.push({label: incall + " - Agents in Call", data: incall});
						if ((ready + queue + paused + incall) == "0")
						{
							data_array = [];
							data_array.push({label: ("No agents online"), data: 0});
						}
						var temp = 0;
						$.plot(painel, data_array, {
							series: {
								pie: {
									innerRadius: 0.06,
									show: true,
									radius: ($("#MainLayout").width() - $("#MainLayout").height()),
									label: {
										show: true,
										formatter: function(label, series) {
											return '<div style="float:rigth;font-size:18px;color:black;">' + Math.round(series.percent) + '%</div>';
										},
										background: {
											opacity: 0.5,
											color: '#FFFFFF'
										}
									}
								}
							},
							legend: {
								show: true,
								container: $("#legend_div" + wbe[0])
							},
							grid: {
								hoverable: false,
								clickable: false
							}});
					}, "json");
				}, "json");
			}, "json");
			updation = setTimeout(get_values_inbound, wbe[7]);
		}, "json");
	}
}
//øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø

function dataTable_top(data)
{
	var wbe = data;
	var updation;
	if (wbe[9][0].status_feedback === "1") {
		var feedbacks_string = "1";
	} else {
		var feedbacks = wbe[9][0].status_feedback.split(',');
		var feedbacks_string = "";
		if (feedbacks.length > 1) {
			feedbacks_string = "status='" + feedbacks[0] + "'";
			for (var i = 1; i < feedbacks.length; i++) {
				feedbacks_string = feedbacks_string + " or status='" + feedbacks[i] + "'";
			}
		} else {
			feedbacks_string = "status='" + feedbacks[0] + "'";
		}
	}
	var panel = $("#" + wbe[0] + "WBE");
	$("#right_title" + wbe[0]).html(" " + wbe[9][0].param1);
	panel.empty();
	panel.append(
		"<table class='table table-striped table-mod' style='height:100%;width:100%'>" +
		"<thead>" +
		"<tr>" +
		"<td>Nome</td>" +
		"<td>" + wbe[9][0].custom_colum_name + "</td>" +
		"<td>TMA</td>" +
		"</tr></thead><tbody id='tbody_id" + wbe[0] + "'></tbody></table>");
	if ($.cookie(wbe[0] + "Main") > 0) {
		panel.data("letter_size_datatop", $.cookie(wbe[0] + "Main"));
	} else {
		panel.data("letter_size_datatop", "1.2");
		$.cookie($.cookie(wbe[0] + "Main"), 1.2);
	}
	var timep = $("#letter_size_popover" + wbe[0])
	timep.val($.cookie(wbe[0] + "Main"));
	timep.spinner({
		min: 0,
		max: 99,
		numberFormat: "C",
		spin: function(evt, ui) {
			var b = $(this).closest(".PanelWB").closest("div");
			b.data().letter_size_datatop = ui.value;
			$.cookie(b.data().id, b.data().letter_size_datatop);
			var temp = b.data().letter_size_datatop;
			b.find('tbody tr').each(function() {
				$(this).css("font-size", temp + "em");
				temp = temp - 0.1;
			});
		}
	});
	var Opcao = 0;
	if (wbe[9][0].campanha != "0")
		Opcao = 1;
	if (wbe[9][0].grupo_user != "0")
		Opcao = 2;
	if (wbe[9][0].grupo_inbound != "0")
		Opcao = 3;
	$('#letter_size_popover' + wbe[0]).popover({html: true});
	get_values_dataTop();
	function get_values_dataTop()
	{
		$.post("../php/wallboardRequests.php",
			{action: "5", status: feedbacks_string, opcao: Opcao, tempo: wbe[9][0].tempo, campaign_id: wbe[9][0].campanha, user_group: wbe[9][0].grupo_user, linha_inbound: wbe[9][0].grupo_inbound, limit: wbe[9][0].limit, mode: wbe[9][0].mode},
		function(data)
		{
			if (data === null)
			{
				if (updation != "")
					clearTimeout(updation);
				panel.remove();
				$("#" + wbe[0] + "Main").remove();
				//$.jGrowl("A tabela " + wbe[2] + " não apresenta resultados", {life: 5000});
				return false;
			}
			var tbody = $("#tbody_id" + wbe[0]);
			tbody.empty();
			var letter_size = +panel.data().letter_size_datatop;
			$.each(data, function(index, value) {
//calculo do TMA de segundos para hora:minuto:segundo
				var totalSec = +data[index].tma;
				var total_feedbacks = +data[index].count_feedbacks;
				totalSec = Math.round(totalSec / total_feedbacks);
				var hours = parseInt(totalSec / 3600) % 24;
				var minutes = parseInt(totalSec / 60) % 60;
				var seconds = totalSec % 60;
				if (hours === 0)
					var result = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
				else if (minutes === 0 && hours === 0)
					var result = (seconds < 10 ? "0" + seconds : seconds);
				else
					var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
				if (data[index].count_feedbacks > 0)
				{
					tbody.append("<tr style='font-size:" + letter_size + "em'>" +
						"<td>" + data[index].user + "</td>" +
						"<td style='text-align:center'>" + data[index].count_feedbacks + "</td>" +
						"<td>" + result + "</td></tr>");
					letter_size = letter_size - 0.03;
				}
			});
		}
		, "json");
		updation = setTimeout(get_values_dataTop, wbe[7]);
	}
}

function secondstotime(seconds)
{
	var numminutes = Math.round(((seconds) % 3600) / 60);
	var numseconds = ((seconds) % 3600) % 60;
	if (numminutes < 10)
		numminutes = "0" + numminutes;
	if (numseconds < 10)
		numseconds = "0" + numseconds;
	return numminutes + ":" + numseconds;
}