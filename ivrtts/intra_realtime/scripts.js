var api = new API(),
        graficos = new graph();

var dashboard = function() {
    var me = this;
    
    this.graph = function() {
        $('#kant').load('../intra_realtime/index.php', function() {

            api.get({'datatype': 'min.max','by':{ 'calls':['database.campaign'],'filter':['database.campaign.oid='+ CurrentCampaignID] }}, function(data) {
                var total =[], total1=[], tempo;
                
                if (data.lenght){
                data =data[0];
                max = moment(data.max);
                min = moment(data.min);
            
                minuto = max.diff(min, 'minute');
                horas = max.diff(min, 'hour');
                dia = max.diff(min, 'day');
                semana = max.diff(min, 'week');
                mes = max.diff(min, 'month');
                ano = max.diff(min, 'year');
              //console.log(horas);
               // console.log('dia:' + dia + ' semana:' + semana + ' mes:' + mes + ' ano:' + ano);
                if(horas<0){
                    tempo=['minute'];
                }else if(dia<0){
                    tempo = ['hour'];
                }else if(mes < 0){
                    tempo =['day'];
                }else if(ano<2){
                    tempo=['year','month'];
                }else if(ano<6){
                    tempo=['year','trimester'];
                }else {
                    tempo =['year'];
                }
            }
                
                $.post('../intra_realtime/total.php', {tempo: tempo, id: CurrentCampaignID}, function(data) { //CurrentCampaignID
                    //console.log(data);
                    total.push({'className':'.total', 'data': data.total},{'className':'.msg', 'data': data.msg}, {'className':'.sys', 'data':data.sys});
                    //console.log(total);
                    graficos.line('#Graph1', total);
                    
                }, 'json');
                
                $.post('../intra_realtime/avg.php', {tempo: tempo, id: CurrentCampaignID}, function(data1) {
                    //console.log(data);
                    total1.push({'className':'.total', 'data': data1.total},{'className':'.msg', 'data': data1.msg}, {'className':'.sys', 'data':data1.sys});
                    //console.log(total1);
                    graficos.line('#Graph2', total1);
                    
                }, 'json');
                
                
            });






            //Barras total Chamadas Feedback
            api.get({'datatype': 'calls', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid='+CurrentCampaignID]}}, function(data) {
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
                    "x": "Outros",
                    "y": outro
                });
                graficos.bar('#Graph3', arr);
            });

            //barra total temporal Chamadas Feedback
            api.get({'datatype': 'sum', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid='+ CurrentCampaignID]}}, function(data) {
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
                        "y": Math.floor(this.sum / (60 * 60))
                    });

                });
                arr.push({
                    "x": "Outros",
                    "y": Math.floor(outr / (60 * 60))
                });

                graficos.bar('#Graph4', arr);
            });




            api.get({'datatype': 'calls', 'by': {'calls': ['database.campaign', 'status'], 'filter': ['database.campaign.oid='+ CurrentCampaignID]}}, function(data) {
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
