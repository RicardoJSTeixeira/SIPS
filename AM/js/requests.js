$(function()
{
 
    var requests = new requests_class($("#modal_div"));


//APOIO MARKETING - RASTrEIO
    $("#master_requests_div #button_apoio_marketing").click(function()
    {
        requests.init_apoio_marketing();
        requests.new_apoio_marketing($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_apoio_marketing").show("blind");
    });


//RELATORIO CORREIO
    $("#master_requests_div #button_relatorio_correio").click(function()
    {
        requests.init_relatorio_correio();
        requests.new_relatorio_correio($("#master_requests_div #div_relatorio_correio"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_correio").show("blind");
    });

//RELATORIO FROTA
    $("#master_requests_div #button_relatorio_frota").click(function()
    {
        requests.init_relatorio_frota();
        requests.new_relatorio_frota($("#master_requests_div #div_relatorio_frota"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_frota").show("blind");
    });

});

