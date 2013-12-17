var api = new API(),
        graficos = new graph();

var dashboard = function() {
    var me = this;

    this.graph = function() {
        $('#kant').load('../intra_realtime/index.php', function() {

            api.get({'datatype': 'min.max'}, function(data) {

                max = moment(data.max);
                min = moment(data.min);
                console.log('Maximo:' + data.max + ' Min:' + data.min);

                dia = max.diff(min, 'day');
                semana = max.diff(min, 'week');
                mes = max.diff(min, 'month');
                ano = max.diff(min, 'year');

                console.log('dia:' + dia + ' semana:' + semana + ' mes:' + mes + ' ano:' + ano);

                if (dia < 1) {
                    console.log('Fazendo por horas');
                    var totalContacts = [];
                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'hour'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        for (var i = 0; i < data.length; i++)
                        {
                            var obj = {
                                'x': data._id.hour,
                                'y': data.count
                            };
                            totalContacts.push(obj);
                        }
                        console.log(totalContacts);
                    });

                } else if (dia < 15) {
                    console.log('Fazemos ao dia');
                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'day'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        console.log(data);
                    });
                } else if (dia > 15) {
                    console.log('Fazemos semana'); //Solicitado ao Pedro a criação de timeline por weeks
                    //Teste

                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'hour'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        var arr = [];

                        $.each(data, function() {
                            arr.push({
                                'x': this._id.hour,
                                'y': this.count
                            });


                        });

                        var total = [{'className': 'totalCalls', 'data': arr}];
                        console.log(total);
                        graficos.line('#Graph1', total);


                    });


                    //
                } else if (ano < 1) {
                    console.log('mesl!');
                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'month'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        console.log(data);
                    });
                } else if (ano < 6) {
                    console.log('Fazemos Semestre');
                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'semester'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        console.log(data);
                    });
                } else if (ano > 6) {
                    console.log('Fazemos anual ');
                    api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'year'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                        console.log(data);
                    });
                }


            });


            //Barras total Chamadas Feedback
            api.get({'datatype': 'contacts', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
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
                        "x": this._id.status.oid,
                        "y": this.count
                    });

                });
                arr.push({
                    "x": "Feedbacks de Sistema",
                    "y": outro
                });
                graficos.bar('#Graph3', arr);
            });

            api.get({'datatype': 'sum', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
                var arr = [],
                        outr = 0;
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
                            outr += this.sum;
                            return;
                    }

                    arr.push({
                        "x": this._id.status.oid,
                        "y":  Math.floor(this.sum / (60 * 60))
                    });

                });
                arr.push({
                    "x": "Feedbacks de Sistema",
                    "y": Math.floor(outr / (60 * 60))
                });
                console.log('Outros:'+ outr);
                graficos.bar('#Graph4', arr);
            });


          

            api.get({'datatype': 'calls', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
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
                        case "S00022":
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
                    "label": "Feedbacks de Sistema",
                    "data": outros / 1000
                });

                graficos.pie('#piechart', arr);
            });


        });
    };


};

function dashboardMain() {
    dash.graph();
}
