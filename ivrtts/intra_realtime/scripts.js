var ap = new API(),
        graficos = new graph();



var Dashboard = function() {
    var me = this;

    this.ini = function() {
        $('#kant').load('../intra_realtime/index.php', function(){
        graficos.line('#Graph1', 'data');
        graficos.line('#Graph2', 'data');
        graficos.bar('#Graph3', 'data');
        graficos.bar('#Graph4', 'data');
        graficos.pie('#piechart', 'data');
       });
    };
    
    this.refresh = function(){
        $('#kant').load('../intra_realtime/index.php', function(){
        graficos.line('#Graph1', 'data');
        graficos.line('#Graph2', 'data');
        graficos.bar('#Graph3', 'data');
        graficos.bar('#Graph4', 'data');
        graficos.pie('#piechart', 'data');
       });
    };
    
    
};

function dashboard() {
    $("#kant").load('../intra_realtime/index.php', function() {
        graficos.line('#Graph1', 'data');
        graficos.line('#Graph2', 'data');
        graficos.bar('#Graph3', 'data');
        graficos.bar('#Graph4', 'data');
        graficos.pie('#piechart', 'data');

    });

}