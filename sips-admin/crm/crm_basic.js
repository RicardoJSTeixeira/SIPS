$(function()
{
    moment.lang("pt");
    var crm_main1 = new crm_main($("#crm_main_zone"), "/sips-admin/crm/");
    var config = new Object();
 
    crm_main1.init(config);
});