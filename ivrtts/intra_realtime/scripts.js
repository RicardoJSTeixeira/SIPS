//var api = new API();
var graficos = new graph(),
        info = new GetMongoInfo();

var dashboard = function() {
    var me = this;

    this.graph = function() {
        $('#kant').load('../intra_realtime/index.php', function() {

            //http://goviragem.dyndns.org:10000/ccstats/v0/total/calls/1900-01-01T00:01/2450-01-01T23:59?campaign=CT0020
            info.get({datatype: 'calls', type: 'total', timeline: {start: '1900-01-01T00:01', end: '2450-01-01T23:59'}, by: {calls: ['campaign=' + CurrentCampaignID]}}, function(data) {
                var start, end, by, minuto, hora, dias, semanas, mes, ano, a, b, momentos, mostring;

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
                    momentos = 'mm';
                    mostring = ' minutos';
                    $('#intervaloTotais').html('Minutos [ MM ]');
                    $('#intervaloAVG').html('Minutos [ MM ]');
                } else if (hora < 24) {
                    by = 'by_hour';
                    momentos = 'DD-hh';
                    mostring = ' Dia-Hora';
                    $('#intervaloTotais').html('Dia Hora [ DD-HH ]');
                    $('#intervaloAVG').html('Dia Hora [ DD-HH ]');
                } else if (dias < 31) {
                    by = 'by_day';
                    end = end.substr(0, 11) + '00:00';
                    start = start.substr(0, 11) + '00:00';
                    momentos = 'YYYY-MM-DD';
                    mostring = ' ';
                    $('#intervaloTotais').html('Ano-Mes-Dia [ YYYY-MM-DD ]');
                    $('#intervaloAVG').html('Ano-Mes-Dia [ YYYY-MM-DD ]');
                } else if (mes < 13) {
                    momentos = 'YYYY-MM';
                    $('#intervaloTotais').html('Ano-Mes [ YYYY-MM ]');
                    $('#intervaloAVG').html('Ano-Mes [ YYYY-MM ]');
                    mostring = ' Ano-Mes';
                    by = 'by_month';
                    end = end.substr(0, 11) + '00:00';
                    start = start.substr(0, 11) + '00:00';
                } else if (ano < 4) {
                    by = 'by_year';
                    $('#intervaloTotais').html('Ano [ YYYY ]');
                    $('#intervaloAVG').html('Ano [ YYYY ]');
                    mostring = ' Ano';
                    momentos = 'YYYY';
                    end = end.substr(0, 11) + '00:00';
                    start = start.substr(0, 11) + '00:00';
                }

                //console.log(dias);
                info.get({datatype: 'statuses', type: 'datatype'}, function(data) {
                    var status = ['MSG001', 'MSG002', 'MSG003', 'MSG004', 'MSG005', 'MSG006', 'MSG007', 'NEW'], sys = [];
                    $.each(data, function() {
                        if (status.indexOf(this.oid) < 0) {
                            sys.push(this.oid);
                        }
                    });
                    //http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/by_year/2012-01-01T00:01/2014-01-01T23:59?campaign=CT0020
                    info.get({datatype: 'calls', type: 'timeline', timeline: {start: start, end: end, by: by}, by: {calls: ['campaign=' + CurrentCampaignID]}}, function(data) {
                        var totalCalls = [], calls = [], totalMsg = [], msg = [], totalSys = [], timeSYS = [];
                        $.each(data, function() {
                            totalCalls.push({
                                x: moment(this.stamp).format(momentos),
                                y: this.calls,
                                label: ' Chamadas',
                                type: mostring
                            });
                            calls.push({
                                x: moment(this.stamp).format(momentos),
                                y: this.length,
                                label: ' Segundos',
                                type: mostring
                            });
                            //console.log(calls);
                        });
                        //http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/by_year/2012-01-01T00:01/2014-01-01T23:59?campaign=CT0020&status=MSG001,MSG002
                        info.get({datatype: 'calls', type: 'timeline', timeline: {start: start, end: end, by: by}, by: {calls: ['status=' + status.join(',') + '&campaign=' + CurrentCampaignID]}}, function(data) {
                            $.each(data, function() {
                                totalMsg.push({
                                    x: moment(this.stamp).format(momentos),
                                    y: this.calls,
                                    label: ' Chamadas',
                                    type: mostring
                                });
                                msg.push({
                                    x: moment(this.stamp).format(momentos),
                                    y: this.length,
                                    label: ' Segundos',
                                    type: mostring
                                });
                            });
                            //http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/by_year/2012-01-01T00:01/2014-01-01T23:59?campaign=CT0020&by=status
                            info.get({datatype: 'calls', type: 'timeline', timeline: {start: start, end: end, by: by}, by: {calls: ['campaign=' + CurrentCampaignID + '&status=' + sys.join(',')]}}, function(data) {
                                $.each(data, function() {
                                    totalSys.push({
                                        x: moment(this.stamp).format(momentos),
                                        y: this.calls,
                                        label: ' Chamadas',
                                        type: mostring
                                    });
                                    timeSYS.push({
                                        x: moment(this.stamp).format(momentos),
                                        y: this.length,
                                        label: ' Segundos',
                                        type: mostring
                                    });
                                });
                                final = [{"className": ".cs", "data": totalCalls}, {"className": ".calls", "data": totalMsg}, {"className": ".call", "data": totalSys}];
                                final1 = [{"className": ".c", "data": calls}, {"className": ".ca", "data": msg}, {"className": ".cas", "data": timeSYS}];
                                graficos.line('#Graph1', final);
                                graficos.line('#Graph2', final1);
                            });
                        });
                    });

                });
            });


            //Barras total Chamadas Feedback
            info.get({'datatype': 'calls', 'type': 'count', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=' + CurrentCampaignID]}}, function(data) {

                var arr = [],
                        outro = 0;
                $.each(data, function() {
                    switch (this._id.status.oid) {
                        case "MSG001":
                        case "MSG002":
                        case "MSG003":
                        case "MSG004":
                        case "MSG005":
                        case "MSG006":
                        case "MSG007":
                        case "NEW":
                            break;
                        default :
                            outro += this.count;
                            return;
                    }

                    arr.push({
                        "x": this._id.status.designation,
                        "y": this.count
                    });

                });
                if (outro > 0) {
                    arr.push({
                        "x": "Outros",
                        "y": outro
                    });
                }
                graficos.bar('#Graph3', arr);
            });

            //barra total temporal Chamadas Feedback
            //http://goviragem.dyndns.org:10000/ccstats/v0/sum/calls?by=status&database.campaign.oid=CT0020
            info.get({datatype: 'calls', type: 'sum', by: {calls: ['status&database.campaign.oid=' + CurrentCampaignID]}}, function(data) {
                var arr = [], outro = 0;
                $.each(data, function() {
                    switch (this._id.status.oid) {
                        case "MSG001":
                        case "MSG002":
                        case "MSG003":
                        case "MSG004":
                        case "MSG005":
                        case "MSG006":
                        case "MSG007":
                        case "NEW":
                            break;
                        default :
                            outro += this.sum;
                            return;
                    }

                    arr.push({
                        "x": this._id.status.designation,
                        "y": this.sum//Math.floor(this.sum / (60 * 60))
                    });
                });
                if (outro > 0) {
                    arr.push({
                        "x": "Outros",
                        "y": outro//Math.floor(outr / (60 * 60))
                    });
                }
                 graficos.bar('#Graph4', arr);
            });
//            info.get({'datatype': 'calls', 'type': 'sum', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=' + CurrentCampaignID]}}, function(data) {
//                console.log(data);
//                var arr = [],
//                        outro = 0;
//                $.each(data, function() {
//                    switch (this._id.status.oid) {
//                        case "MSG001":
//                        case "MSG002":
//                        case "MSG003":
//                        case "MSG004":
//                        case "MSG005":
//                        case "MSG006":
//                        case "MSG007":
//                        case "NEW":
//                            break;
//                        default :
//                            outro += this.sum;
//                            return;
//                    }
//
//                    arr.push({
//                        "x": this._id.status.designation,
//                        "y": this.sum//Math.floor(this.sum / (60 * 60))
//                    });
//
//                });
//                if (outro > 0) {
//                    arr.push({
//                        "x": "Outros",
//                        "y": outro//Math.floor(outr / (60 * 60))
//                    });
//                }
//
//                graficos.bar('#Graph4', arr);
//            });



            info.get({'datatype': 'calls', 'type': 'count', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=' + CurrentCampaignID]}}, function(data) {

                var
                        arr = [],
                        outros = 0;

                $.each(data, function() {
                    switch (this._id.status.oid) {
                        case "MSG001":
                        case "MSG002":
                        case "MSG003":
                        case "MSG004":
                        case "MSG005":
                        case "MSG006":
                        case "MSG007":
                        case "NEW":
                            break;
                        default :
                            outros += this.count;
                            return;
                    }

                    arr.push({
                        "label": this._id.status.designation,
                        "data": this.count
                    });
                });

                arr.push({
                    "label": "Outros",
                    "data": outros
                });

                graficos.pie('#piechart', arr);
            });


        });
    };


};

function dashboardMain() {
    dash.graph();
}
