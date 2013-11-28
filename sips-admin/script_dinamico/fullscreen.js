

$(function()
{
      var info = getUrlVars();
      
     
      
      
      var render1 = new render($("#render_placeholder_div"),"/sips-admin/script_dinamico/", info.script_id, undefined, undefined, undefined, undefined, 0);

      render1.init();
      



function getUrlVars() {
      var vars = {};
      var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
      });
      return vars;
}


$("#validate_button").click(function()
{
      
           $("#result_validate").text(render1.validate_manual());
           
                  
});

});