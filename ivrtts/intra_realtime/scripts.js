var api = new API(),
        graficos = new graph();

var dashboard = function() {
    var me = this;

    this.graph = function() {
        $('#kant').load('../intra_realtime/index.php', function() {

            api.get({'datatype': 'min.max'}, function(data) {

                max = moment(this.max).format('YYYY-MM-DD');
                min = moment(this.min).format('YYYY-MM-DD');
                
                dia = moment(max).diff(min,'day');
                semana = moment(max).diff(min,'week');
                mes = moment(max).diff(min,'month');
                ano = moment(max).diff(min,'year');
                console.log('Maximo:'+max);
                console.log('Min:'+min);
                console.log('dia' + dia);
                console.log('semana' + semana);
                console.log('mes' + mes);
                console.log('year' + ano);
//
//                if (dia < 15) {
//                    console.log('Fazemos dia!');
//                } else if (dia > 15) {
//                    console.log('Fazemos semana');
//                } else if (ano < 1) {
//                    console.log('mesl!');
//                } else if (ano < 6) {
//                    console.log('Fazemos Semestre');
//                } else if (ano > 6) {
//                    console.log('Fazemos anual ');
//                }

//           $.each(data,function(){
//           numero = this._id.month;
//           mes= moment(String(numero), 'M').format('MMMM');
//           console.log(numero);
//           console.log(mes);
//           });
//           
//           if (time === 'dia'){
//               
//           }else if (time === 'mes'){
//               
//           }else if(time === 'semestre'){
//               
//           }else if(time ==='ano'){
//               
//           }
//           

            });


            graficos.line('#Graph1', 'data');
            graficos.line('#Graph2', 'data');
            graficos.bar('#Graph3', 'data');
            graficos.bar('#Graph4', 'data');

            api.get({'datatype': 'calls', 'by': {'calls': ['data.campaign', 'status'], 'filter': ['database.campaign.oid=W00003']}}, function(data) {
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
                    "data": outros/1000
                });

                graficos.pie('#piechart', arr);
            });


        });
    };


};

function dashboardMain() {
    dash.graph();
}
