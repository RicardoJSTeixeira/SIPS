$(function()
{
    var requests = new request();

    requests.init();

//APOIO MARKETING - RASTrEIO
    $("#master_requests_div #button_apoio_marketing").click(function()
    {
        requests.apoio_marketing($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_apoio_marketing").show("blind");
    });


//RELATORIO CORREIO
    $("#master_requests_div #button_relatorio_correio").click(function()
    {
        requests.relatorio_correio($("#master_requests_div #div_relatorio_correio"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_correio").show("blind");
    });

//RELATORIO FROTA
    $("#master_requests_div #button_relatorio_frota").click(function()
    {
        requests.relatorio_frota($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_apoio_marketing").show("blind");
    });

});

