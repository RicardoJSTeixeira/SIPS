var script;
$(function()
{
    var info = getUrlVars();




    script = new render($("#script_placeholder_div"), "/sips-admin/script_dinamico/", info.script_id, info.lead_id, info.unique_id, info.user, info.campaign_id, 1);

    script.init();




    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
        });
        return vars;
    }




});
$("#gravar_alteracoes").click(function()
{

    script.submit_manual(function() {
        $.jGrowl('Script Editado', {life: 3000});
    });

});