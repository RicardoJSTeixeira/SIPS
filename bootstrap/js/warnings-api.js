 /**
    * makeAlert
    * 
    * Creates a warning message, prepended to the target param
    * 
    * @param {String} Target target is a css selector like #title or .cabenens > .you
    * @param {String} Title title is the text that appear inside the {strong} 
    * @param {String} Message Message is the message "Capt. Obvious strikes again!"
    * @param {enum-int} Type type 1:error 2:warning 3:info 4:success (!=[1-4])=warning
    * @param {Boolean} Onlyone onlyone doesn't make a warning if theres is another in the target
    * @param {Boolean} Block makes a block warning
    * @return {Boolean}
**/
function makeAlert(Target, Title, Message, Type, Onlyone, Block){
    
    var AlertClass;
    
    switch (Type) {
        case 1:
            AlertClass = "alert-error";
            break;
        case 2:
            AlertClass = "";
            break;
        case 3:
            AlertClass = "alert-info";
            break;
        case 4:
            AlertClass = "alert-success";
            break;
    }
    
    if(Onlyone){
        if($(Target).find(".alert").length > 0){
            return false;
        }
    }
    
    if(Block){
        $(Target).prepend('<div class="alert alert-block '+AlertClass+'">  <button type="button" class="close" data-dismiss="alert">&times;</button>  <h4>'+Title+'</h4> '+Message+' </div>');
    }else{  
        $(Target).prepend('<div class="alert '+AlertClass+'">  <button type="button" class="close" data-dismiss="alert">&times;</button>  <strong>'+Title+'</strong> '+Message+' </div>');
    }
    
    $(".alert").alert();
    
    return true;
}
