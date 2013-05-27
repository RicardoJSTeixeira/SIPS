

//---------------------------------------------------------------------------
//    Let's load some Stylesheets :
//---------------------------------------------------------------------------
function load_stylesheet(filename) {
  var css = document.createElement('link'); 
  css.rel = 'stylesheet'; 
  css.type = 'text/css'; 
  css.href = filename; 
  parent.body.appendChild(css);
}















