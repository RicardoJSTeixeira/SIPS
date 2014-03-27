$(function()
{

    var requests1 = new requests($("#modal_div"));


//APOIO MARKETING - RASTrEIO
    $("#master_requests_div #button_apoio_marketing").click(function()
    {
        requests1.init_apoio_marketing();
        requests1.new_apoio_marketing($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_apoio_marketing").show("blind");
    });


//RELATORIO CORREIO
    $("#master_requests_div #button_relatorio_correio").click(function()
    {
        requests1.init_relatorio_correio();
        requests1.new_relatorio_correio($("#master_requests_div #div_relatorio_correio"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_correio").show("blind");
    });

//RELATORIO FROTA
    $("#master_requests_div #button_relatorio_frota").click(function()
    {
        requests1.init_relatorio_frota();
        requests1.new_relatorio_frota($("#master_requests_div #div_relatorio_frota"));
        $("#master_requests_div .request_div").hide();
        $("#master_requests_div #div_relatorio_frota").show("blind");
    });

});

