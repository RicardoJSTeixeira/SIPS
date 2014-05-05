$(function()
{

    var requests1 = new requests($("#modal_div"));


//APOIO MARKETING - RASTrEIO
    $("#master_requests_div #button_apoio_marketing").click(function()
    {
        requests1.apoio_marketing.init();
        requests1.apoio_marketing.new($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_apoio_marketing").show();
    });


//RELATORIO CORREIO
    $("#master_requests_div #button_relatorio_correio").click(function()
    {
        requests1.relatorio_correio.init();
        requests1.relatorio_correio.new($("#master_requests_div #div_relatorio_correio"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_correio").show();
    });

//RELATORIO FROTA
    $("#master_requests_div #button_relatorio_frota").click(function()
    {
        requests1.relatorio_frota.init();
        requests1.relatorio_frota.new($("#master_requests_div #div_relatorio_frota"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_frota").show();
    });
    
//RELATORIO Mensal de Stock
    $("#master_requests_div #button_relatorio_mensal_stock").click(function()
    {
        requests1.init();
        requests1.new($("#master_requests_div #div_relatorio_mensal_stock"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_mensal_stock").show();
    });

//RELATORIO Movimentação de Stock
    $("#master_requests_div #button_relatorio_movimentação_stock").click(function()
    {
        requests1.init();
        requests1.new($("#master_requests_div #div_relatorio_movimentação_stock"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_movimentação_stock").show();
    });

});

