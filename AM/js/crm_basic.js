

$(function()
{
    moment.lang("pt");
    var crm_main1 = new crm_main($("#crm_main_zone"), "/sips-admin/crm/");

    $.post("ajax/general_functions.php", {action: "get_user_level"},
    function(data)
    {
        var config = new Object();
        if (data < 10)
        {
            config.date = false;
        }
 
        crm_main1.init(config);
    }, "json");


});