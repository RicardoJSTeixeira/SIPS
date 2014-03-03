
var script,unique_id;
$(function()
{
      var info = getUrlVars();




       script = new render($("#render_zone"), "/sips-admin/script_dinamico/", info.script_id, info.lead_id, undefined, info.user_id, info.campaign_id, 0);

      script.init();



 
      function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
                  vars[key] = value;
            });
            return vars;
      }




});


function validate_manual(validado,nao_validado)
{
      return  script.validate_manual(validado,nao_validado);
}

function submit_manual(callback)
{
     return  script.submit_manual(callback);
}