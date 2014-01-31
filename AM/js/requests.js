$(function()
{
    var requests = new request();

    requests.init();

    $("#master_requests_div #button_apoio_marketing").click(function()
    {
        requests.apoio_marketing($("#master_requests_div #div_apoio_marketing"));
        $("#master_requests_div #div_apoio_marketing").toggle("blind");
    });

});

