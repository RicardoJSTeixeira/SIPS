$(function(){
    var Rq = new Requests($("#modal_div"));

//RELATORIO CORREIO
    $("#master_requests_div #button_relatorio_correio").click(function()
    {
        Rq.relatorio_correio.new($("#master_requests_div #div_relatorio_correio"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_correio").show();
    });

//RELATORIO FROTA
    $("#master_requests_div #button_relatorio_frota").click(function()
    {
         Rq.relatorio_frota.new($("#master_requests_div #div_relatorio_frota"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_frota").show();
    });
    
//RELATORIO Mensal de Stock
    $("#master_requests_div #button_relatorio_mensal_stock").click(function()
    {
        Rq.relatorio_mensal_stock.new($("#master_requests_div #div_relatorio_mensal_stock"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_mensal_stock").show();
    });

//RELATORIO Movimentação de Stock
    $("#master_requests_div #button_relatorio_movimentacao_stock").click(function()
    {
        Rq.relatorio_movimentacao_stock.new($("#master_requests_div #div_relatorio_movimentacao_stock"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_movimentacao_stock").show();
    });

});