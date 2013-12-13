var ap = new API(),
        graficos = new graph();

var dashboard = function() {
    var me = this;

    this.graph = function() {
        $('#kant').load('../intra_realtime/index.php', function(){
         
        graficos.line('#Graph1', 'data');
        graficos.line('#Graph2', 'data');
        graficos.bar('#Graph3', 'data');
        graficos.bar('#Graph4', 'data');
        graficos.pie('#piechart', 'data');
       });
    };
   
    
};

function dashboardMain() {
   dash.graph();
}