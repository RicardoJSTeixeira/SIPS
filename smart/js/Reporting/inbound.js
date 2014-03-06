function inboundList(today) {


    $('#campaign-table').dataTable({
        "bProcessing": true,
        "aaSorting": [[1, "desc"]],
        "sPaginationType": "bootstrap",
        "bLengthChange": false,
        "bDestroy": true, "aoColumns": [
            {"sTitle": "Campaign Designation", "sClass": "", "sWidth": "250px"},
            {"sTitle": "Total Calls", "sClass": "", "sWidth": "150px"},
            {"sTitle": "Total Drop Calls", "sClass": "", "sWidth": "150px"},
            {"sTitle": "Total Calls Time", "sClass": "", "sWidth": "150px"},
            {"sTitle": "AVG Time", "sClass": "", "sWidth": "150px"},
            {"sTitle": "Total Queue Time", "sClass": "", "sWidth": "150px"},
            {"sTitle": "AVG Queue Time", "sClass": "", "sWidth": "150px"},
            {"sTitle": "AVG Queue Position", "sClass": "", "sWidth": "150px"}
        ]});


    api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=campaign']}}, function(data) {
        var win = data.length;
        var call = {}, time = {}, avg_ar = {}, avg_queue_ar = {}, totalQueue_ar = {}, avg_position_ar = {};
        $.each(data, function() {
            call[this.campaign] = this.calls;
            time[this.campaign] = this.sum_length;
            avg_ar[this.campaign] = Math.round(this.sum_length / this.calls);
            totalQueue_ar[this.campaign] = this.sum_queue_seconds;
            avg_queue_ar[this.campaign] = Math.round(this.sum_queue_seconds / this.calls);
            avg_position_ar[this.campaign] = this.sum_queue_position / this.calls;
            win--;
            if (!win) {
                inboundTable(today, call, time, avg_ar, totalQueue_ar, avg_queue_ar, avg_position_ar);
            }
        });

    });

    $.post('../php/reporting.php', {action: 'inboundLines'}, function(data) {
        $('#inbound-line-1').html(data.active.count + ' / ' + data.total.count);
        $('#inbound-bar-1').css('width', Math.round((data.active.count * 100) / data.total.count) + '%');
    }, 'json');
    $.post('../php/reporting.php', {action: 'inboundAgents'}, function(data) {
        $('#inbound-line-2').html(data.live.length + ' / ' + data.total.length);
        $('#inbound-bar-2').css('width', Math.round((data.live.length * 100) / data.total.length) + '%');
    }, 'json');
    $.post('../php/reporting.php', {action: 'getDID'}, function(data) {
        $('#inbound-line-3').html(data.active.count + ' / ' + data.total.count);
        $('#inbound-bar-3').css('width', Math.round((data.active.count * 100) / data.total.count) + '%');
    }, 'json');
    $.post('../php/reporting.php', {action: 'total_human'}, function(data) {
        var status = data.human;
        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}}, function(data) {
            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['status=' + status.join(',')]}}, function(datas) {
                $('#inbound-line-4').html(datas[0].calls + ' / ' + data[0].calls);
                $('#inbound-bar-4').css('width', Math.round((datas[0].calls * 100) / data[0].calls) + '%');
            });
            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['status=DROP']}}, function(dats) {
                $('#inbound-line-5').html(dats[0].calls + ' / ' + data[0].calls);
                $('#inbound-bar-5').css('width', Math.round((dats[0].calls * 100) / data[0].calls) + '%');
            });
        });
        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=hour']}}, function(data) {
            var calls = [], talk = [], drop = [], final = [];
            $.each(data, function() {
                calls.push([
                    this.hour,
                    this.calls]);
            });
            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=hour&status=' + status.join(',')]}}, function(data) {
                $.each(data, function() {
                    talk.push([
                        this.hour,
                        this.calls
                    ]);
                });
                api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=hour&status=DROP']}}, function(data) {
                    $.each(data, function() {
                        drop.push([
                            this.hour,
                            this.calls
                        ]);
                    });
                    final = [{data: calls, label: 'Total Calls'}, {data: talk, label: 'Total Answer Calls'}, {data: drop, label: 'Total Drop Calls'}];
                    graficos.floatLine('#in1', final);
                });

            });
        });
    }, 'json');


}

function inbound_by(start, end, callback) {
    var minuto, hora, dias, semanas, mes, ano, a, b;
    if (start === '' && end === '') {
        //http://10.0.0.113:10000/ccstats/v0/total/calls_inbound/2010-01-01T10:00/2020-01-01T00:00
        api.get({'datatype': 'calls_inbound', 'type': 'total', timeline: {start: '1010-01-01T00:00', end: '3020-01-01T00:00'}}, function(data) {
            $.each(data, function() {
                end = this.max_stamp;
                start = this.min_stamp;
            });
            a = moment(end);
            b = moment(start);
            minuto = a.diff(b, 'minutes');
            hora = a.diff(b, 'hours');
            dias = a.diff(b, 'days');
            semanas = a.diff(b, 'weeks');
            mes = a.diff(b, 'month');
            ano = a.diff(b, 'year');
            if (minuto < 60) {
                by = 'by_minute';
            } else if (hora < 24) {
                by = 'by_hour';
            } else if (dias < 31) {
                by = 'by_day';
                end = end.substr(0, 11) + '00:00';
                start = start.substr(0, 11) + '00:00';
            } else if (mes < 13) {
                by = 'by_month';
                end = end.substr(0, 11) + '00:00';
                start = start.substr(0, 11) + '00:00';
            } else if (ano < 4) {
                by = 'by_year';
                end = end.substr(0, 11) + '00:00';
                start = start.substr(0, 11) + '00:00';
            }

            callback(by, start, end, hora);
        });
    } else {

        a = moment(end);
        b = moment(start);
        minuto = a.diff(b, 'minutes');
        hora = a.diff(b, 'hours');
        dias = a.diff(b, 'days');
        semanas = a.diff(b, 'weeks');
        mes = a.diff(b, 'month');
        ano = a.diff(b, 'year');
        if (minuto < 60) {
            by = 'by_minute';
        } else if (hora < 24) {
            by = 'by_hour';
        } else if (dias < 31) {
            by = 'by_day';
            end = end.substr(0, 11) + '00:00';
            start = start.substr(0, 11) + '00:00';
        } else if (mes < 13) {
            by = 'by_month';
            end = end.substr(0, 11) + '00:00';
            start = start.substr(0, 11) + '00:00';
        } else if (ano < 4) {
            by = 'by_year';
            end = end.substr(0, 11) + '00:00';
            start = start.substr(0, 11) + '00:00';
        }

        callback(by, start, end, hora);
    }
}

function inbound(start, end, CampaignID, inboundName, campaigns, statuses, agents, databases) {
    graficos.performance('.inbound', 45);
    inbound_by(start, end, function(by, start, end, hora) {
        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['campaign=' + CampaignID]}}, function(data) {
            if (data.length) {
                $('#inbound .jarviswidget').show();
                $('#inbound').fadeIn(400, function() {
                    $('#inbound-campaign-title').html('<h6> ' + CampaignID + ' - ' + inboundName + '</h6>');

                    inboundAgents(start, end, CampaignID);
                    inboundFeedbacks(start, end, CampaignID, statuses);
                    inboundTotaisAndInfo(start, end, CampaignID);



                    //timeline e hours
                    $.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
                        var status = data.human, calls = [], talk = [], drop = [], timeCalls = [], timeTalk = [], timePause = [];
                        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=hour&campaign=' + CampaignID]}}, function(data) {
                            $.each(data, function() {
                                calls.push({
                                    x: this.hour,
                                    y: this.calls,
                                    label: ' Calls',
                                    type: ' hours'
                                });
                                timeCalls.push({
                                    x: this.hour,
                                    y: this.sum_length,
                                    label: 'Total Calls Time',
                                    type: ' hours',
                                    unit: ' sec'
                                });
                            });
                            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=hour&status=' + status.join(',') + '&campaign=' + CampaignID]}}, function(data) {
                                $.each(data, function() {
                                    talk.push({
                                        x: this.hour,
                                        y: this.calls,
                                        label: ' Calls',
                                        type: ' hours'
                                    });
                                    timeTalk.push({
                                        x: this.hour,
                                        y: this.sum_length,
                                        label: 'Total Answer Time',
                                        type: ' hours',
                                        unit: ' sec'
                                    });
                                });
                                api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=hour&status=DROP&campaign=' + CampaignID]}}, function(data) {
                                    $.each(data, function() {
                                        drop.push({
                                            x: this.hour,
                                            y: this.calls,
                                            label: ' Calls',
                                            type: ' hours'
                                        });
                                    });
                                    final = [{"className": ".cs", "data": calls}, {"className": ".calls", "data": talk}, {"className": ".call", "data": drop}];
                                    //////////console.log(final);
                                    graficos.line('#inbound2', final);
                                    api.get({datatype: 'agent_log', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=hour&campaign=' + CampaignID]}}, function(data) {
                                        $.each(data, function() {
                                            timePause.push({
                                                x: this.hour,
                                                y: this.sum_pause,
                                                label: 'Total Pause Time',
                                                type: ' hours',
                                                unit: ' sec'
                                            });
                                        });
                                        graficos.dualBar('#inbound3', timeCalls, timeTalk, timePause);
                                    });
                                });
                            });
                        });
                    }, 'json');
                    $(document).on('click', '#inbound-agents tr', function() {
                        var agents = $(this)[0].cells[0].childNodes[0].dataset.id;
                        $('#inbound-agents-detail').fadeIn(400, function() {
                            $('#inbound-agents-feedback').dataTable().fnClearTable();
                            $('#inbound-agents-feedback').dataTable({"sPaginationType": "bootstrap", "bLengthChange": false, "bDestroy": true, "aaSorting": [[2, "desc"]]});
                            //http://gonecomplus.dyndns.org:10000/ccstats/v0/total/calls_inbound/2014-02-19T00:01/2014-02-19T23:59?by=agent&agent=mmaltez&campaign=Ingenico
                            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=status&agent=' + agents + '&campaign=' + CampaignID]}}, function(data) {
                                var ar = [];
                                $.each(data, function() {
                                    var status = this.status;
                                    if (statuses[this.status]) {
                                        status = statuses[this.status];
                                    }
                                    ar.push([this.status, status, this.calls]);
                                });
                                $('#inbound-agents-feedback').dataTable().fnAddData(ar);
                            });
                        });
                    });
                    $(document).on('click', '#inbound-feedbacks tr', function() {
                        var outcomes = $(this)[0].cells[0].childNodes[0].dataset.id;
                        $('#inbound-outcomes-detail').fadeIn(400, function() {
                            $('#inbound-outcomes-agents').dataTable().fnClearTable();
                            $('#inbound-outcomes-agents').dataTable({"sPaginationType": "bootstrap", "bLengthChange": false, "bDestroy": true, "aaSorting": [[2, "desc"]]});
                            //http://gonecomplus.dyndns.org:10000/ccstats/v0/total/calls_inbound/2014-02-19T00:01/2014-02-19T23:59?by=agent&status=S00129&campaign=Ingenico
                            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=agent&status=' + outcomes + '&campaign=' + CampaignID]}}, function(data) {
                                var ar = [];
                                $.each(data, function() {
                                    var agent = this.agent;
                                    if (agents[this.agent]) {
                                        agent = agents[this.agent];
                                    }
                                    ar.push([this.agent, agent, this.calls]);
                                });
                                $('#inbound-outcomes-agents').dataTable().fnAddData(ar);
                            });
                        });
                    });
                });
            } else {
                $('#inbound').hide();
                $.smallBox({
                    title: "Reporting",
                    content: "Date range without information!",
                    color: "#5384AF",
                    timeout: 3600,
                    icon: "fa fa-calendar"
                });
            }
        });
    });
}




function inboundTable(today, call, time, avg_ar, totalQueue_ar, avg_queue_ar, avg_position_ar) {
    var drop_ar = {};
    api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=campaign&status=DROP']}}, function(data) {
        $.each(data, function() {
            drop_ar[this.campaign] = this.calls;
        });
        $.post('../php/reporting.php', {action: 'getInbound'}, function(data) {
            var won = data.inbound.length, ar = [], name = '';

            $.each(data.inbound, function() {
                var calls = 0, drop = 0, totalTime = 0, avg = 0, totalQueue = 0, avg_queue = 0, avg_position = 0;

                name = '<span class="table-value cursor-pointer" data-oid="' + this.group_id + '" data-id="' + this.group_name + '">' + this.group_id + '</span>';

                if (call[this.group_id]) {
                    calls = call[this.group_id];
                }
                if (time[this.group_id]) {
                    totalTime = time[this.group_id];
                }
                if (avg_ar[this.group_id]) {
                    avg = avg_ar[this.group_id];
                }
                if (totalQueue_ar[this.group_id]) {
                    totalQueue = totalQueue_ar[this.group_id];
                }
                if (avg_queue_ar[this.group_id]) {
                    avg_queue = avg_queue_ar[this.group_id];
                }
                if (avg_position_ar[this.group_id]) {
                    avg_position = avg_position_ar[this.group_id];
                }
                if (drop_ar[this.group_id]) {
                    drop = drop_ar[this.group_id];
                }

                ar.push([name, calls, drop, totalTime, avg, totalQueue, avg_queue, avg_position]);

                won--;


                if (!won) {

                    $('#campaign-table').dataTable().fnClearTable();
                    $('#campaign-table').dataTable().fnAddData(ar);
                }
            });
        }, 'json');
    });

}

function inboundAgents(start, end, CampaignID) {
    $('#inbound-agents').dataTable().fnClearTable();
    $('#inbound-agents').dataTable({"sPaginationType": "bootstrap", "bLengthChange": false, "bDestroy": true, "aaSorting": [[3, "desc"]]});
    api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=agent&campaign=' + CampaignID]}}, function(data) {
        var ar = [], win = data.length;
        $.each(data, function() {
            var avg = this.sum_length / this.calls, agent = '<span data-id=' + this.agent + '>' + this.agent + '<span>';
            if (agents[this.agent]) {
                agent = '<span data-id=' + this.agent + '>' + agents[this.agent] + '<span>';
            }
            ar.push([agent, this.agent, this.sum_length, this.calls, avg.toFixed(2)]);
            win--;
            if (!win) {
                $('#inbound-agents').dataTable().fnClearTable();
                $('#inbound-agents').dataTable().fnAddData(ar);
            }
        });
    });
}

function inboundFeedbacks(start, end, CampaignID, statuses) {
    $('#inbound-feedbacks').dataTable().fnClearTable();
    $('#inbound-feedbacks').dataTable({"sPaginationType": "bootstrap", "bLengthChange": false, "bDestroy": true, "aaSorting": [[2, "desc"]]});
    api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['by=status&campaign=' + CampaignID]}}, function(data) {
        var ar = [], win = data.length;
        $.each(data, function() {
            var avg, status = this.status, id = '<span data-id=' + this.status + '>' + this.status + '</span>';
            if (statuses[this.status]) {
                status = statuses[this.status];
            }
            ar.push([id, status, this.calls, this.sum_length, Math.round(this.sum_length / this.calls), '0']);
            win--;
            if (!win) {
                $('#inbound-feedbacks').dataTable().fnClearTable();
                $('#inbound-feedbacks').dataTable().fnAddData(ar);
            }
        });
    });
}

function inboundTotaisAndInfo(start, end, CampaignID) {
    $.post('../php/reporting.php', {action: 'human', id: CampaignID}, function(data) {
        var status = data.human, totalCalls = 0, calls = 0, drop = 0, fora = 0, noagents = 0, ar = [];
        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['campaign=' + CampaignID]}}, function(data) {
            var avgLength = 0, avgQueue = 0, avgPosition = 0;
            totalCalls = data[0].calls;
            avgLength = data[0].sum_length / data[0].calls;
            avgQueue = data[0].sum_queue_seconds / data[0].calls;
            avgPosition = data[0].sum_queue_position / data[0].calls;
            $('#inInfo1').html(avgLength.toFixed(2) + ' s');
            $('#inInfo2').html(data[0].max_length + ' s');
            $('#inInfo3').html(data[0].sum_length + ' s');
            $('#inInfo4').html(avgQueue.toFixed(2) + ' s');
            $('#inInfo5').html(data[0].max_queue_seconds + ' s');
            $('#inInfo6').html(data[0].sum_queue_seconds + ' s');
            $('#inInfo7').html(avgPosition.toFixed(2));
            $('#inInfo8').html(data[0].max_queue_position);
            //$('#inInfo9').html(data[0].sum_queue_position);
            api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['status=' + status.join(',') + '&campaign=' + CampaignID]}}, function(data) {
                if (data.length) {
                    calls = data[0].calls;
                }
                api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['status=DROP&campaign=' + CampaignID]}}, function(data) {
                    if (data.length) {
                        drop = data[0].calls;
                    }
                    api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['status=AFTHRS&campaign=' + CampaignID]}}, function(data) {
                        if (data.length) {
                            fora = data[0].calls;
                        }
                        api.get({datatype: 'calls_inbound', type: 'total', timeline: {start: start, end: end}, by: {calls: ['status=NANQUE&campaign=' + CampaignID]}}, function(data) {
                            if(data.length){
                                noagents=data[0].calls;
                            }

                            ar.push([0,calls],[1,],[],[],[]);
                            graficos.bar('#inbound1', ar);
                        });
                    });
                });
            });
        });
    }, 'json');
}