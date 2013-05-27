$(function(){
    
    $.extend($.ui.dialog.prototype.options, {
        autoOpen: false,
        modal: true,
        resizable: false,
        closeOnEscape: true
    });
        
    $(":button").button("enable");
    
    $(":input").removeClass("ui-state-error");
        
    $("#sub-ddi").submit(function(){
        $("#sub-ddi-b").button("disable");
        a=$("#did_description").val();
        b=(a.toString().length>9)?a.slice(0,9)+".":a;
        $("#ddi-title").html(b).attr("title",a);
        $("#org").jOrgChart({
            chartElement : '#chart',
            fold  : false
        });
        
        advanced_options("DDI");
    
        $("#first-step").slideUp(1000);
            
        $('#tree').fileTree({
            root: '/var/www/html/client_files/sounds/'
        }, function(file) {
            alert(file);
        }); 
        return false;
    });
    
    $(".as").click(function(){
        $('#selector-audio').dialog('open')
    });
});	
                
var idcount = 0, ddi_set=false, on_fly=false;
	
function erasechild(id) {
	
    $("#lielem_"+id).remove();
    $('#chart').html("");
       
    $("#org").jOrgChart({
        chartElement : '#chart',
        fold  : false
    });
    idcount--;
    if(id==1){
        $("#chart #ddi-add").removeClass("ui-state-disabled");
        ddi_set=false;
    }
	
}
$(".rm").live("click",function(){
    erasechild($(this).attr("ext"))
});
	
function box(a,b,c){
    var img="";
    b=$.trim(b);
    switch (a) {
        case "IVR":
            img="computer";
            d="IVReopt";
            bb="IVR ";
            break;
        case "AGENT":
            img="user_32";
            d="AGNTeopt";
            bb="Agente ";
            break;
        case "PHONE":
            img="phone_32";
            d="PHeopt";
            bb="Telefone ";
            break;
        case "INB":
            img="group_32";
            d="INBeopt";
            bb="Grupo de Inbound ";
            break;
        case "VM":
            img="telephone_go_32";
            d="VMeopt";
            bb="Voicemail ";
            break;
    }
    bb+=b;
    b=(b.toString().length>9)?b.slice(0,9)+".":b;
    return "<li class='"+a.toString().toLowerCase()+"' id='lielem_"+c+"'>\n\
                <span class='title' title='"+bb+"'>"+b+"</span>\n\
                <span class='tools'>\n\
                <img class='edit rm' src='/images/icons/cross_16.png' ext='"+c+"' alt='Remover Elemento' title='Remover Elemento' />\n\
                <img class='edit "+d+"' src='/images/icons/livejournal_16.png' ext='"+c+"' alt='Editar Elemento' title='Editar opções do "+bb+"' />\n\
                <img src='/images/icons/sound_add_16.png' alt='Adicionar Opção' ext='"+c+"'  class='addoption add_child' title='Adicionar nova opção de "+bb+"' >\n\
                </span>\n\
                <span class='icon'>\n\
                <img src='/images/icons/"+img+".png' alt='"+b+"' title='"+bb+"' />\n\
                </span>\n\
                <ul id='elem_"+c+"'>\n\
                </ul>\n\
                </li>"
}
        
function addchild(type,text) {
    if(on_fly == 'ddi'){
        if(ddi_set){
            on_fly=false;
            return false
        }
        $("#ddi-add").addClass("ui-state-disabled");
        ddi_set=true;
        idcount++;
    }else{
        on_fly="elem_"+on_fly;
        idcount++;
    }
    $('#chart').html("");
       
    $("#"+on_fly).append(box(type,text,idcount)) ; 
    
    $("#org").jOrgChart({
        chartElement : '#chart',
        fold  : false
    });
    
    advanced_options(type);
    
    on_fly=false;
    return true;
}
//Incompleta
function advanced_options(v){
    confirm("Quer configurar já as opções avançadas?",function(){
        switch (v) {
            case "INB":
                $('#inbg-setup-edit').dialog('open');
                break;
            case "IVR":
                $('#ivr-setup-edit').dialog('open');
                
                break;
            case "DDI":
                $('#ddi-setup-edit').dialog('open');
                break;
            default:
                break;
        }

    });
}
    
function open_add_form(element){
    if(element=="ddi" && ddi_set){
        return false
    }
    on_fly=element;
    $('#line-chooser').dialog('open');
    return true;
}
	
 
function confirm(message, callback) {
    $('body').append('<div id="confirm" style="display:none">'+message+'</div>'); // dont forget to hide this!
    $( "#confirm" ).dialog({
        title: 'Confirme',
        buttons: [
        {
            text: "Sim",
            click: function() {
                $(this).dialog("close");
                if ($.isFunction(callback)) {  
                    callback.apply();
                }
               
            }
        },{
            text: "Não",
            click: function() {
                $(this).dialog("close");
            }
        }
        ],
        close: function(event, ui) {
            $('#confirm').remove();
        }
    });
    $( "#confirm" ).dialog("open");
}

        
$(function() {
   
                
    $(".add_child").live("click",function(){
        open_add_form($(this).attr("ext"))
    });
    
    $(".ddi-edit").live("click",function(){
        $('#ddi-setup-edit').dialog('open');
    });
    
    $(".IVReopt").live("click",function(){
        $('#ivr-setup-edit').dialog('open');
    });
    
    $(".INBeopt").live("click",function(){
        $('#inbg-setup-edit').dialog('open');
    });


    $("#inbg-setup-edit-queue-ranking").spinner({
        spin: function( event, ui ) {
            if ( ui.value > 99 ) {
                $( this ).spinner( "value", -99 );
                return false;
            } else if ( ui.value < -99 ) {
                $( this ).spinner( "value", 99 );
                return false;
            }
        }
    });
       
    $("#inbg-setup-edit-agent-alert-delay ").spinner({
        min:0,
        max:20000
    })   

    $("#inbg-setup-edit-wait-time-option-seconds").spinner({
        min:0,
        max:1000
    })   

    $("#inbg-setup-edit-on-hold-prompt-interval").spinner({
        min:0,
        max:1000
    })     

    $("#inbg-setup-edit-drop-call-seconds").spinner({
        min:0,
        max:1000
    })
        
    $("#inbg-setup-edit-max-calls-count").spinner({
        min:0,
        max:1000
    })       
    //DIALOG FORM EDITA IVR
    $( "#ivr-setup-edit" ).dialog({
        height: 400,
        width: 400,
        buttons: {
            "Guardar": function() {
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });
        
        
    //DIALOG ESCOLHA DE AUDIO
    $( "#selector-audio" ).dialog({
        height: 370,
        width: 420,
        buttons: {
            "Guardar": function() {
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });
                
    //DIALOG TREE VIEW DE FICHEIROS
    $( "#tree" ).dialog({
        height: 400,
        width: 400,
        buttons: {
            "Cancelar" : function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });
        
    //DIALOG FORM OPÇÕES DE IVR
    $( "#ivroptions" ).dialog({
        height: 400,
        width: 400,
        buttons: {
            "Cancelar" : function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });
        
        
    //COMEÇO DIALOG EDIÇÃO DE DDI
    $( "#ddi-setup-edit" ).dialog({
        height: 400,
        width: 400,
        buttons: {
            "Gravar" : function(){
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });  
    //FIM DIALOG EDIÇÃO DE DDI
    
    //DIALOG ESCOLHA DE OPÇÃO SEGUINTE
    $( "#line-chooser" ).dialog({
        height: 160,
        width: 300,
        buttons: {
            "Criar" : function() {
                var a=$("#element_dest option:selected");
                switch (a.val()) {
                    case "AGENT":
                        $( "#agent-setup" ).dialog("open");
                        break;
                    case "VM":
                        $( "#vm-setup" ).dialog("open");
                        break;
                    case "PHONE":
                        $( "#phone-setup" ).dialog("open");
                        break;
                    case "INB":
                        $( "#inbg-setup" ).dialog("open");
                        break;
                    case "IVR":
                        $( "#ivr-setup" ).dialog("open");
                        break;
                    default:
                        break;
                }
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                on_fly=false;
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    var inbg_setup_name = $( "#inbg-setup-name" ),
    inbg_setup_color = $( "#inbg-setup-color" ),
    inbg_setup_fields = $( [] ).add( inbg_setup_name ).add( inbg_setup_color ),
    inbg_setup_tip=$("#inbg-setup.dialog p.validateTips");
    //BEGING DIALOG ESCOLHA DE INBG
    $( "#inbg-setup" ).dialog({
        height: 300,
        width: 380,
        buttons: {
            "Criar" : function() {
                var bValid = true;
                inbg_setup_fields.removeClass( "ui-state-error" );
 
                bValid = bValid && checkLength( inbg_setup_name, "nome", 2, 0,inbg_setup_tip );
                bValid = bValid && checkLength( inbg_setup_color, "audio", 0, 0,inbg_setup_tip );
                if ( bValid ) {
                    addchild("INB",inbg_setup_name.val());
                    $( this ).dialog( "close" );
                }
            },
            "Cancelar" : function() {
                $( this ).dialog( "close" );
                on_fly=false;
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    //-----BEGING COLOR PICKER
    $('#inbg-setup-color').colourPicker({
        ico:'/jquery/colourPicker/colourPicker.gif', 
        title:false
    });  
    //END DIALOG ESCOLHA DE INBG
    
    //BEGIN DIALOG EDIT INBG
    var inbg_setup_edit_name = $( "#inbg-setup-edit-name" ),
    inbg_setup_edit_color = $( "#inbg-setup-edit-color" ),
    inbg_setup_edit_fields = $( [] ).add( inbg_setup_edit_name ).add( inbg_setup_edit_color ),
    inbg_setup_edit_tip=$("#inbg-setup-edit.dialog p.validateTips");
    //BEGING DIALOG ESCOLHA DE INBG
    $( "#inbg-setup-edit" ).dialog({
        height: 500,
        width: 400,
        buttons: {
            "Guardar" : function() {
                var bValid = true;
                inbg_setup_edit_fields.removeClass( "ui-state-error" );
 
                bValid = bValid && checkLength( inbg_setup_edit_name, "nome", 2, 0,inbg_setup_edit_tip );
                bValid = bValid && checkLength( inbg_setup_edit_color, "audio", 0, 0,inbg_setup_edit_tip );
                if ( bValid ) {
                    addchild("INB",inbg_setup_edit_name.val());
                    $( this ).dialog( "close" );
                }
            },
            "Cancelar" : function() {
                $( this ).dialog( "close" );
                on_fly=false;
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    //-----BEGING COLOR PICKER
    $('#inbg-setup-edit-color').colourPicker({
        ico:'/jquery/colourPicker/colourPicker.gif', 
        title:false
    });  
    //END DIALOG EDIT INBG
    	
    //BEGING DIALOG SELECÇÃO DE AGENT
    $( "#agent-setup" ).dialog({
        height: 160,
        width: 300,
        buttons: {
            "Criar" : function() {
                var a=$("#agent-setup-agent  option:selected");
                addchild("AGENT",a.text());
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                on_fly=false;
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });	
    //END DIALOG SELECÇÃO DE AGENT
    
    //BEGIN DIALOG SELECÇÃO DE PHONE
    $( "#phone-setup" ).dialog({
        height: 160,
        width: 300,
        buttons: {
            "Criar" : function() {
                var a=$("#phone-setup-phone option:selected");
                addchild("PHONE",a.val());
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                on_fly=false;
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    //END DIALOG SELECÇÃO DE PHONE  
       	
    //BEGIN DIALOG SELECÇÃO DE VM
    $( "#vm-setup" ).dialog({
        height: 160,
        width: 300,
        buttons: {
            "Criar" : function() {
                var a=$("#vm-setup-vm option:selected");
                addchild("VM",a.val());
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                on_fly=false;
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    //END DIALOG SELECÇÃO DE VM
       
    var ivr_setup_name = $( "#ivr-setup-name" ),
    ivr_setup_audio = $( "#ivr-setup-audio-start" ),
    ivr_setup_fields = $( [] ).add( ivr_setup_name ).add( ivr_setup_audio ),
    ivr_setup_tip=$("#ivr-setup.dialog p.validateTips");
    //BEGIN DIALOG CRIAÇÂO DE IVR
    $( "#ivr-setup" ).dialog({
        height: 300,
        width: 300,
        buttons: {
            "Criar" : function() {
                var bValid = true;
                ivr_setup_fields.removeClass( "ui-state-error" );
 
                bValid = bValid && checkLength( ivr_setup_name, "nome", 2, 0,ivr_setup_tip );
                bValid = bValid && checkLength( ivr_setup_audio, "audio", 0, 0,ivr_setup_tip );
                if ( bValid ) {
                    addchild("IVR",ivr_setup_name.val());
                    $( this ).dialog( "close" );
                }
            },
            "Cancelar" : function() {
                on_fly=false;
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            on_fly=false;
        }
    }).parent().find(".ui-dialog-titlebar-close").click(function() {
        on_fly=false;
    });
    //END DIALOG CRIAÇÂO DE IVR 
    
    
    function updateTips( t,tip ) {
        tip
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tip.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }
 
    function checkLength( o, n, min, max,tip ) {
        if ( (o.val().length > max && max!=0) || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            if(max!=0){
                updateTips( "Comprimento de " + n + " deve ter entre " +
                    min + " e " + max + ".",tip );
            }
            else{
                updateTips( "Comprimento de " + n + " deve ter mais de " +
                    min + " caractéres.",tip );
            }
                
            return false;
        } else {
            return true;
        }
    }
 
    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }

});


//Constructors
function ddi(extention,description,active,admin_groups,call_rec){
    this.extention=extention;
    this.description=description;
    this.active=active;
    this.admin_groups=admin_groups;
    this.call_rec=call_rec;
}

function inbg(name,color,active,admin_groups,welcome_message,welcome_message_filename,play_welcome_message,on_hold_promt_message,on_hold_promt_interval,on_hold_prompt_no_block,edit_next_agent_call,queue_ranking,get_call_launch,drop_call_seconds,drop_action,call_time,after_hours_action,no_agent_no_queue,no_agent_no_queue_action){
    this.name=name;
}