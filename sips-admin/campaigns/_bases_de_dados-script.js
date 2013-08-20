function DBListBuilder(Flag)
{
    DBElemInit();
    $(".span-active-campaign-name").html($("#campaign-name").val()) 
    
    
if( ($("#tbl-dbs tr").length == 0 && Flag == "ALL") || Flag == "DISABLED" || Flag == "REFRESH")
{
    if(Flag != "NEW") { $("#tbl-dbs").hide(); }
    
    $.ajax({
        type: "POST",
        url: "_bases_de_dados-requests.php",
        dataType: "JSON",
        data: { action: "DBListBuilder",
                CampaignID: CampaignID,
                AllowedCampaigns: AllowedCampaigns,
                DBName: $.trim($("#input-new-db-name").val()),
                Flag: Flag
                },
        success: function(json) 
        {
            if(json != null)
            {
            
            var Active;
            
            var DBConfig;
            var DBTrash;
            
            if(Flag == 'ALL' || Flag == "DISABLED" || Flag == "REFRESH"){ $('#tbl-dbs').empty(); }
               
                
            if(typeof json.db_id != "undefined")
            {
                $.each(json.db_id, function(index, value){              
                  
                  if(json.db_active[index] == "Y"){ Active = "checked='checked'"; } else { Active = ""; }
                  if(json.db_leads[index] == null){ json.db_leads[index] = 0; }   
                  
                  
                  
                    if(Flag == "DISABLED")
                    {
                        DBCheckbox = "";
                        DBTrash = "<td class='css-td-list-actions'><img class='css-img-list-actions' style='opacity:0; cursor:default;'  title='' src='icons/mono_trash_16.png'></td>";
                        DBConfig = "<td class='css-td-list-actions'><img class='css-img-list-actions dbs-activate' title='Reactivar' src='icons/mono_plus_16.png'></td>";
                    }
                    else
                    {
                        DBCheckbox = "<td class='css-td-list-check odd-even-ignore'><input "+Active+" class='db-checkbox' type='checkbox' id=''></td>";
                        DBConfig = "<td class='css-td-list-actions'><img class='css-img-list-actions dbs-edit' title='Configurar' src='icons/mono_wrench_16.png'></td>";
                        DBTrash = "<td class='css-td-list-actions'><img class='css-img-list-actions dbs-deactivate'  title='' src='icons/mono_trash_16.png'></td>";
                    }
                  
                  
                  
                  
                   $("#tbl-dbs").prepend("\n\
                        <tr id='"+json.db_id[index]+"' style='height:22px;'>"+DBCheckbox+"<td class='css-td-list-label'><label id=''>"+json.db_name[index]+"</label></td><td class='css-td-list-label'>"+json.db_create[index]+"</td><td class='css-td-list-icon'><img class='css-img-list-icon pointer add-more-leads' title='Nº de Leads' src='icons/mono_db_16.png'></td><td class='css-td-list-text'>"+json.db_leads[index]+"</td><td class='css-td-list-filler'></td>"+DBTrash + DBConfig+"</tr>");
                });
                
            }   
                
                


                if(Flag == "ALL" || Flag == "REFRESH")
                {
                
                if(json.db_leads_manual == "undefined" || json.db_leads_manual == null){json.db_leads_manual = 0;}
                
                
                $("#tbl-dbs").prepend("\n\
                        <tr id='"+json.db_id_manual+"' style='height:22px;'><td class='css-td-list-check odd-even-ignore'><input disabled='disabled' checked='checked' class='db-checkbox-manual' type='checkbox' id=''></td><td class='css-td-list-label'><label id=''><b>Chamadas Manuais da Campanha</b></label></td><td class='css-td-list-label'></td><td class='css-td-list-icon'><img class='css-img-list-icon' title='Nº de Leads' src='icons/mono_db_16.png'></td><td class='css-td-list-text'>"+json.db_leads_manual+"</td><td class='css-td-list-filler'></td><td class='css-td-list-actions'><img class='css-img-list-actions' style='opacity:0.3; cursor:default;'  title='' src='icons/mono_trash_16.png'></td><td class='css-td-list-actions'><img class='css-img-list-actions dbs-edit' title='Configurar' src='icons/mono_wrench_16.png'></td></tr>")
                }
                
                $(".db-checkbox").uniform();
                $(".db-checkbox-manual").uniform();
                OddEvenRefresh("", "tbl-dbs");
              

        if(Flag != "NEW") { $("#tbl-dbs").fadeIn(200); }  
        
            }
        
        }
    });
}

}

function DBElemInit()
{
    $("#btn-new-db").button();
    $("#btn-select-all-dbs").button();
    $("#btn-select-no-dbs").button();
    $("#btn-dbs-active-inactive-switch").button();
    
    $("#btn-db-wizard-load-file").button();
    
    $("#btn-db-wizard-match-fields").button({disabled: true});
    $("#btn-db-wizard-match-fields-restart").button({disabled: true});
    
    $("#btn-db-wizard-restart").button();
    
    $("#btn-db-wizard-accept-leads").button({disabled: true});
    $("#btn-db-wizard-deny-leads").button({disabled: true});
    $("#btn-db-wizard-view-details").button({disabled: true});
    
    
    $("#btn-dbs-reset").button();
    
    
    $("#btn-reset-dbs-select-all").button();
    $("#btn-reset-dbs-select-none").button();
    
    
       $("#btn-db-force-duplicate").button();
    
    
    $("#btn-dialog-dbs-download-db").button();
    
    $("#dialog-dbs-edit").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_wrench_16.png'></td><td><span class='dialog-title'> Configurar > Base de Dados: </span><span style='color:#0073ea' class='span-dialog-dbs-contact-details-db'></td></tr></table>",
    autoOpen: false,
    height: 650, 
    width: 550,
    resizable: false,
    buttons: { "Gravar" : DialogDBEditOnSave, "Fechar" : DialogClose },
    open: DialogDBEditOnOpen
    }); 
    
    $("#dialog-edit-dbs-contact-details").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_right_expand_16.png'></td><td><span class='dialog-title'>Ver Detalhes > Base de Dados: </span><span style='color:#0073ea' class='span-dialog-dbs-contact-details-db'></span><span class='dialog-title'> > Feedback: </span><span style='color:#0073ea' id='span-dialog-dbs-contact-details-feed'></span></td></tr></table>",
    autoOpen: false,
    height: 480,
    width: 950,
    resizable: false,
    position: { my: "center+10%", of: "#dialog-dbs-edit" },
    buttons: { "Fechar" : DialogClose },
    open: DialogDBEditContactDetailsOnOpen
    }); 
    
    $("#dialog-db-wizard").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_db_16.png'></td><td><span class='dialog-title'>Carregar Novos Contactos > Base de Dados: </span><span style='color:#0073ea' id='span-dialog-wizard-db-name-random'></span></td></tr></table>",
    autoOpen: false,
    height: 650,
    width: 950,
    resizable: false,
    buttons: { "Fechar" : DialogClose },
    open: DialogWizardDBOnOpen,
    close: DialogWizardDBOnClose
    });
    
    
    $("#dialog-db-wizard-view-details").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_zoom_16.png'></td><td><span class='dialog-title'> Ver Detalhes </span></td></tr></table>",
    autoOpen: false,
    height: 600,
    width: 800,
    resizable: false,
    buttons: { "Fechar" : DialogClose },
    open: function(){}
    });
    
    
    
    $("#dialog-dbs-reset").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_cancel_16.png'></td><td><span class='dialog-title'> Reset a Bases de Dados </span></td></tr></table>",
    autoOpen: false,
    height: 600,
    width: 400,
    resizable: false,
    buttons: { "Reset": DialogDbsOnReset, "Fechar" : DialogClose },
    open: DialogDbsOnOpen
    }); 
     
    
     
    
}

function DBSwitch()
{
    var Active;
    if($(this).parent().hasClass("checked")) Active = "Y"; else Active = "N";
    $.ajax({
        type: "POST",
        url: "_bases_de_dados-requests.php",
        dataType: "JSON",
        data: { action: "DBSwitch",
                DBID: $(this).closest("tr").attr("id"),
                Active: Active
                },
        success: function(json) 
        {}
    });
    
}

function SelectAllDBs()
{
    $.post("_bases_de_dados-requests.php", {action: "SelectAllDBs", CampaignID: CampaignID }, function(){ $(".db-checkbox").attr("checked", "checked").parent().addClass("checked").uniform.update(".db-checkbox"); })
}

function UnselectAllDBs()
{
    $.post("_bases_de_dados-requests.php", {action: "UnselectAllDBs", CampaignID: CampaignID }, function(){  $(".db-checkbox").removeAttr("checked").parent().removeClass("checked").uniform.update(".db-checkbox"); })
}

function selectAllDBs()
{
    var obj = $(this);
    
    if(obj.hasClass("dbs-hidden-switch"))
    {
        DBListBuilder("DISABLED");
        obj.removeClass("dbs-hidden-switch").addClass("dbs-visible-switch").children().children().attr("src", "icons/mono_connected_16.png");
    
        $("#btn-select-all-dbs").addClass("ui-state-disabled").attr("disabled", "disabled");
        $("#btn-select-no-dbs").addClass("ui-state-disabled").attr("disabled", "disabled");
        $("#span-dbs-active-inactive").html("activas")
    
    }
    else 
    {
        DBListBuilder("ALL");
        obj.removeClass("dbs-visible-switch").addClass("dbs-hidden-switch").children().children().attr("src", "icons/mono_notconnected_16.png");
        
        $("#btn-select-all-dbs").removeClass("ui-state-disabled").removeAttr("disabled");
        $("#btn-select-no-dbs").removeClass("ui-state-disabled").removeAttr("disabled");
        $("#span-dbs-active-inactive").html("inactivas");

    }
}

function DeactivateDB()
{
    var that = $(this);
    $.post("_bases_de_dados-requests.php", {action: "DeactivateDB", DBID: $(this).closest("tr").prop("id")}, function(){ $("tr[id="+that.closest("tr").prop("id")+"]").remove(); OddEvenRefresh("", "tbl-dbs"); });
}

function ActivateDB()
{
    var that = $(this);
    $.post("_bases_de_dados-requests.php", {action: "ActivateDB", DBID: $(this).closest("tr").attr("id")}, function(){ 
        $("tr[id="+that.closest("tr").attr("id")+"]").remove(); 
        OddEvenRefresh("", "tbl-dbs"); 
    });
}

// edit DB

function TableEditDBInit()
{

    tableEditDB = $('#dbs-state').dataTable( {
        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 8,
        "sDom": '<"top"><"dt-fixed-8lines-with-icon"rt><"bottom"p>',
        "bSortClasses": false,
        "bJQueryUI": true,  
        "bProcessing": true, 
        "bRetrieve": false,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_bases_de_dados-requests.php',
        "fnServerParams": function (aoData)  
        {
            aoData.push( 
                        { "name": "action", "value": "GetTableEditDB" },
                        { "name": "DBID", "value": editedDB }
                       );
        },
        "aoColumnDefs": [
                      /*  { "bSearchable": false, "bVisible": false, "aTargets": [ 4 ] },
                        { "bSearchable": false, "bSortable" : false, "bVisible": true, "aTargets": [ 0, 1, 2, 3 ] } */
                    ],
        "aoColumns": [ 
                        
                        { "sTitle": "Feedback", "sWidth":"200px", "sClass":"css-dt-column-align-left" },
                        { "sTitle": "Nº de Contactos", "sWidth": "16px", "sClass":"css-dt-column-align-center" }, 
                        { "sTitle": "Ver Detalhes", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
                     ], 
        "oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
        "fnDrawCallback":   function()
                            { 
                                $("#dbs-state").css("width", "100%");                     
                            }
        });
    

    
}


var dT_errordetails;

function TableErrorDetailsInit()
{

    dT_errordetails = $('#tbl-db-wizard-view-details').dataTable( {
        "iDisplayLength": 10,
        "sDom": '<"top"><"dt-fixed-12lines-with-icon"rt><"bottom"p>',
        "bJQueryUI": true,  
        "bProcessing": true, 
        "bRetrieve": false,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
         
        "oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
        "fnDrawCallback":   function()
                            { 
                                $("#tbl-db-wizard-view-details").css("width", "100%");                     
                            }
        });
    

    
}

function DialogDBEditOnOpen()
{
    $("#div-dbs-state-container").hide();
    $(".span-dialog-dbs-contact-details-db").html( $("tr[id='"+editedDB+"']").find("label").text() );
    
    $.post("_bases_de_dados-requests.php", {action: "DialogDBEditOnOpen", DBID: editedDB, AllowedCampaigns: AllowedCampaigns}, function(json)
    {
        $("#input-edit-db-name").val($("tr[id="+editedDB+"]").find("label").text());

        $("#sel-dbs-change-campaign").empty();

        $.each(json.campaign_id, function(index, value)
        {
            if(json.selected[index].length > 1){ editedDBCampaign = json.campaign_id[index]; }
            $("#sel-dbs-change-campaign").append("<option value='"+json.campaign_id[index]+"' "+json.selected[index]+">"+json.campaign_name[index]+"</option>")
        });
        if(tableEditDB === undefined)
            {
                TableEditDBInit();
                $("#div-dbs-state-container").fadeIn(200);
            }
            else
            {
                tableEditDB.fnReloadAjax();
                $("#div-dbs-state-container").fadeIn(200);
            }        
    }, "json");

    
}

function DialogDBEditOnSave()
{
    var changeName;
    var changeCampaign;
    var needQuery = false;
    
    if($("#input-edit-db-name").val() != $("tr[id='"+editedDB+"']").find("label").text())
    {
        changeName = $("#input-edit-db-name").val();
        needQuery = true;
        $("tr[id='"+editedDB+"']").remove();
    }
    else
    { changeName = $("tr[id='"+editedDB+"']").find("label").text(); }
    
    if($("#sel-dbs-change-campaign option:selected").attr("value") != CampaignID)
    {
        changeCampaign = $("#sel-dbs-change-campaign option:selected").attr("value");
        needQuery = true;
    }
    else
    { changeCampaign = CampaignID; }
    
    if(needQuery) $.post("_bases_de_dados-requests.php", {action: "DialogDBEditOnSave", DBID: editedDB, changeName: changeName,  changeCampaign: changeCampaign }, function(){})
    
    $("#dialog-dbs-edit").dialog("close");
    
}

// Dbs Contact Details

function TableDbsContactDetailsInit()
{
    tableDbsContactDetails = $('#dbs-contact-details').dataTable( {
        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 14,
        "sDom": '<"top"f><"dt-fixed-10lines-with-icon"rt><"bottom"p>',
        "bSortClasses": false,
        "bJQueryUI": true,  
        "bProcessing": true, 
        "bRetrieve": false,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_bases_de_dados-requests.php',
        "fnServerParams": function (aoData)  
        {
            aoData.push( 
                        { "name": "action", "value": "TableDbsContactDetailsInit" },
                        { "name": "DBID", "value": editedDB },
                        { "name": "editedDBFeed", "value": editedDBFeed }
                       )
        },
        "aoColumnDefs": [
                        { "bSearchable": false, "bVisible": false, "aTargets": [ 4 ] },
                        { "iDataSort": 4, "aTargets": [ 3 ] }
                    ],
        "aoColumns": [ 
                        
                        { "sTitle": "Nome", "sClass":"css-dt-column-align-left" },
                        { "sTitle": "Telefone", "sWidth": "16px", "sClass":"css-dt-column-align-center" }, 
                        { "sTitle": "Total de Chamadas", "sWidth": "125px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Última Chamada", "sWidth": "125px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "epoch", "sWidth": "125px", "sClass":"css-dt-column-align-center" }
                     ], 
        "fnDrawCallback":   function()
                            { 
                            if(typeof $("#div-dbs-contact-details-title").html() == 'undefined') $("#dbs-contact-details_wrapper .top").append("<div class='css-dt-title' id='div-dbs-contact-details-title'>Ver Detalhes<span class='css-orange-caret'>></span>Contactos</div>"); 
                            },
        "oLanguage": { "sUrl": "extras/datatables/language.txt" }
        });
    
    
}

function DialogDBEditContactDetailsOnOpen()
{

    

    $("#span-dialog-dbs-contact-details-feed").html( $("img[feedback-id='"+editedDBFeed+"']").parent().parent().find("td").html() )
    
    if(tableDbsContactDetails === undefined)
            {
                TableDbsContactDetailsInit();
                $("#div-dbs-contact-details-container").fadeIn(200);
            }
            else
            {
                tableDbsContactDetails.fnReloadAjax();
                $("#div-dbs-contact-details-container").fadeIn(200);
            }       
    
}

function NewDB()
{
    if($("#input-new-db").val() == "" || $("#input-new-db").val() == null)
    {
        $("#td-new-db-error").html("<b>Por favor escolha um nome para a Base de Dados.</b>")
        $("#input-new-db").css("border-color", "red");
    }
    else
    {
        $.post("_bases_de_dados-requests.php", {action: "NewDB", CampaignID: CampaignID, DBName: $("#input-new-db").val()}, function(json){
            
        $("#input-new-db").val("");
            
        editedDB = json.db;
        editedDBName = json.name;
        
        $("#dialog-db-wizard").dialog("open");
        
        DBListBuilder("REFRESH");
        
        }, "json");
        
    }

}

function DialogWizardDBOnOpen()
{
    $("#span-dialog-wizard-db-name-random").html(editedDBName);
}

function DialogWizardDBOnClose()
{
    DBWizardRestart();
    DBListBuilder("REFRESH")
    $(this).dialog("close");
}

function DBWizardRestart()
{
    $("#input-db-wizard-upload").val("");
    $("#input-db-wizard-upload").removeAttr("disabled");
    
    $("#td-db-wizard-file-error").html(""); 
    $("#td-db-wizard-file-icon").html("");
    
    $("#btn-db-wizard-load-file").button("enable");
    $("#btn-db-wizard-match-fields").button("disable");
    $("#btn-db-wizard-match-fields-restart").button("disable");
    
    $("#btn-db-wizard-restart").button("enable");
    
    $("#td-db-wizard-match-fields-error").html("");
    $("#td-db-wizard-match-fields-error-icon").html("") 
    
    DBFieldNames = new Array();
    DBFieldDisplayNames = new Array();
    
    $("#table-db-wizard-match-fields").empty()
    
    $("#btn-db-wizard-accept-leads").button("disable");
    $("#btn-db-wizard-deny-leads").button("disable");
    $("#btn-db-wizard-view-details").button("disable");
    
    $("#td-db-wizard-match-fields-duplicates").html("");
    $("#td-db-wizard-match-fields-duplicates-button").html("");
                
    
    
}

// LOADER ENGINE
function DBWizardUpload() 
{
    
    if($("#input-db-wizard-upload").val() == "" || $("#input-db-wizard-upload").val() == null)
    {
        $("#td-db-wizard-file-error").html("Por favor escolha um ficheiro para carregar.");
        $("#td-db-wizard-file-icon").html("<img class='mono-icon' src='icons/mono_alert_16.png'>");  
        return false;
    }
    else if(!/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$|\.txt$/i.test($("#input-db-wizard-upload").val()))
    {  
        $("#td-db-wizard-file-error").html("Por favor escolha um tipo de ficheiro compativel para carregar."); 
        $("#td-db-wizard-file-icon").html("<img class='mono-icon' src='icons/mono_alert_16.png'>"); 
        return false;
    }
    else
    {
        $("#td-db-wizard-file-error").html("A carregar o ficheiro..."); 
        $("#td-db-wizard-file-icon").html("<img class='mono-icon' src='icons/loader2.gif'>");
        
        
        var FileData = new FormData();
        FileData.append("input-db-wizard-upload", document.getElementById('input-db-wizard-upload').files[0]);
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", uploadProgress, false);
        xhr.addEventListener("load", dbwizardComplete, false);
        xhr.addEventListener("error", uploadFailed, false);
        xhr.addEventListener("abort", uploadCanceled, false); 
        xhr.open("POST", "extras/upload/upload.php");
        xhr.send(FileData);
        
    }
    
    
    
    

}


function uploadProgress(evt) 
{
}


function uploadFailed(evt) 
{
    alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) 
{
    alert("The upload has been canceled by the user or the browser dropped the connection.");
} 

function dbwizardComplete(evt) 
{
    ConvertedFile = evt.target.responseText;
    
    $("#input-db-wizard-upload").attr("disabled", "disabled");
    
    $("#td-db-wizard-file-error").html("Ficheiro carregado com sucesso!"); 
    $("#td-db-wizard-file-icon").html("<img class='mono-icon' src='icons/mono_check_16.png'>");

    $("#btn-db-wizard-load-file").button("disable");
    $("#btn-db-wizard-match-fields").button("enable");
    $("#btn-db-wizard-match-fields-restart").button("enable");
    
    DBMatchFields();
    
    
} 

function DBMatchFields()
{
    $.post("_bases_de_dados-requests.php", {action: "DBMatchFields", ConvertedFile: ConvertedFile, CampaignID: CampaignID}, function(json)
    {
        $.each(json.headers, function(index, value){
    
            $("#table-db-wizard-match-fields").append("<tr><td style='width:40%'>"+json.headers[index]+"</td><td class='td-db-wizard-middle-graf' style='width:20%; text-align:center'><img class='mono-icon' style='float:left' src='icons/mono_right_tag_16.png'></td><td style='width:40%'><select id='"+json.headers[index]+"' style='float:right; margin: 6px 0px; width:250px; margin-right:8px;' class='sel-db-wizard-fields'></select></td></tr>")
        
        });
        
        if(typeof json.name == 'undefined'){
            $("#td-db-wizard-file-error").html("<span style='color:red'>Não estão configurados Campos Dinamicos, por favor configure os campos necessários.</span>")
            $("#td-db-wizard-file-icon").html("<img class='mono-icon' src='icons/mono_attention_16.png'>")
        }
        else {
            $.each(json.name, function(index, value){
        
            DBFieldNames.push(json.name[index]);
            DBFieldDisplayNames.push(json.display_name[index]);
            
        });
        
        DBSelBuilder();
        }
        
        
    }, "json");
}

function DBSelBuilder()
{
    $(".sel-db-wizard-fields").empty();
    $(".sel-db-wizard-fields").append("<option value='---'>---</option>")
    $.each(DBFieldNames, function(index, value){
        $(".sel-db-wizard-fields").append("<option value='"+DBFieldNames[index]+"'>"+DBFieldDisplayNames[index]+"</option>");
    });
}

function DBWizardFieldChange()
{
    
    
    $("#td-db-wizard-match-fields-error").html("");
    $("#td-db-wizard-match-fields-error-icon").html("");
    
    var clickedSelect = $(this).attr("id");

    
    var chosenField = $(this).attr("value");


    $.each($(".sel-db-wizard-fields"), function(index, value){

        if($(this).attr("id") != clickedSelect)
        {
            for(var i=0; i < $(this)[0].options.length;i++)
            {
                if($(this)[0].options[i].value == chosenField)
                {
                    $(this)[0].options[i] = null;
                }
            }
        }
        else
        {
            console.log($(this).attr("disabled", "disabled").closest("tr").find(".td-db-wizard-middle-graf").html("<img style='float:left' class='mono-icon' src='icons/mono_round_checkmark_16.png'><img style='float:left; cursor:pointer' class='mono-icon db-wizard-clear-single' src='icons/mono_brush_16.png'>"));
            
            
        }
    
    }) 

}


var DBLoadErrorLine;
var DBLoadErrorText;
var DBLoadErrorPhone;
var DBLoadKeepDuplicates;


function DBWizardMatchFields() 
{
    
    var validateSubmit = false;
    var errorCode = 0;
    
    insertedLeadsIDs = new Array();
    
    
    $.each($(".sel-db-wizard-fields option:selected"), function(index, value){
    
        if($(this).attr("value") == "PHONE_NUMBER")
        {
            validateSubmit = true;
            errorCode = 0;
            return false;
        }
        else
        {
            validateSubmit = false;
            errorCode = 1;
        } 
    
    /*  if($(this).attr("value") != "---")
        {
            validateSubmit = true;
            errorCode = 0;
            return false;
        }
        else
        {
            validateSubmit = false;
            errorCode = 2;
        } */
    
    
    })
    
    
    if(!validateSubmit)
    {
        switch(errorCode)
        {
            case 1: 
                {
                $("#td-db-wizard-match-fields-error").html("Pelo menos uma das colunas do ficheiro que foi carregado tem de ser associada ao campo \"Telefone\".<br> A associação de campos foi recomeçada.")
                $("#td-db-wizard-match-fields-error-icon").html("<img style='float:right' class='mono-icon' src='icons/mono_alert_16.png'>") 
                DBWizardMatchFieldsRestart();
                break;
                };
    /*      case 2:
                {
                $("#td-db-wizard-match-fields-error").html("Tem de escolher pelo menos um campo para receber valores do ficheiro que escolheu.")
                $("#td-db-wizard-match-fields-error-icon").html("<img style='float:right' class='mono-icon' src='icons/mono_alert_16.png'>")    
                break;
                }    */
        }
    }
    else
    {
        
        var arrayListFields = new Array();
        var arrayMatchFields = new Array();
        
        $.each($(".sel-db-wizard-fields"), function(){
        
        arrayMatchFields.push($(this).attr("id"));
        arrayListFields.push($(this).val())
    
        });

        
        $("#td-db-wizard-match-fields-error").html("A carregar contactos...");
        $("#td-db-wizard-match-fields-error-icon").html("<img style='float:right' class='mono-icon' src='icons/loader2.gif'>");
        
        $.post("_bases_de_dados-requests.php", {action: "DBWizardMatchFields", DBID: editedDB, MatchFields: arrayMatchFields, ListFields: arrayListFields, ConvertedFile: ConvertedFile, CampaignID: CampaignID }, function(json){



            

            if(json.totalloaded == null)
            {
                json.totalloaded = 0;
            }
            
            if(json.totalinserted == null)
            {
                json.totalinserted = 0;
            }
            
            if(json.totalerrors == null)
            {
                json.totalerrors = 0;
            }


            if(json.totalerrors == 0)
            {
                $("#td-db-wizard-match-fields-error").html("Contactos Inseridos com Sucesso: <b>"+json.totalinserted+"</b>");
                $("#td-db-wizard-match-fields-error-icon").html("<img style='float:right' class='mono-icon' src='icons/mono_check_16.png'>");
                

                if(json.totalloaded > 0) $("#btn-db-wizard-deny-leads").button("enable");
                
                $("#btn-db-wizard-match-fields").button("disable");
                $("#btn-db-wizard-match-fields-restart").button("disable");
                //$("#btn-db-wizard-restart").button("disable");

                if(json.insert_id != null) insertedLeadsIDs = json.insert_id;
                
            }
            else
            {


                DBLoadErrorLine = json.error_line;
                DBLoadErrorText = json.error_text;
                DBLoadErrorPhone = json.error_phone;
                DBLoadKeepDuplicates = json.keep_duplicates;

                
                if(json.insert_id != null) insertedLeadsIDs = json.insert_id;
                
                
                $("#td-db-wizard-match-fields-error").html("Total de Contactos Inseridos com Sucesso: <b>"+json.totalinserted+"</b><br><br>Total de Contactos com Erros: <b>"+json.totalerrors+"</b>");
                $("#td-db-wizard-match-fields-error-icon").html("<img style='float:right' class='mono-icon' src='icons/mono_alert_16.png'>");
                
            //    $("#td-db-wizard-match-fields-duplicates").html("Contactos Duplicados: " + json.totalduplicates);
            //    $("#td-db-wizard-match-fields-duplicates-button").html("<table><tr><td class='td-action'><button class='btn-action' id='btn-db-wizard-match-fields-duplicates-button'><img class='img-action' src='icons/mono_doc_import.png'></button></td><td id='td-db-wizard-load-duplicates-msg'>Carregar campos duplicados.</td></tr></table>")
                
                
                $("#btn-db-wizard-match-fields-duplicates-button").button();
                
                
                /*
                 * <table>
                    <tr>
                    <td class="td-action"><button class="btn-action" id="btn-dialog-fields-copy-from-campaign"><img class="img-action" src='icons/mono_indent_increase_16.png'></button></td>
                    <td>Copiar Campos para a Campanha actual.</td>
                    </tr>
               <!--      <tr>
                    <td class="td-action td-action-next"><button class="btn-action" id="btn-fields-copy-from-campaign"><img class="img-action" src='icons/mono_indent_increase_16.png'></button></td>
                    <td class="td-action-next">Copiar definições de Campos Dinamicos de outra Campanha para a Campanha actual.</td>
                    </tr> -->
            </table>
                 * 
                 * 
                 *  */
                
                
                
                //$("#btn-db-wizard-accept-leads").button("enable");
                $("#btn-db-wizard-deny-leads").button("enable");
                $("#btn-db-wizard-view-details").button("enable");
                
                $("#btn-db-wizard-match-fields").button("disable");
                $("#btn-db-wizard-match-fields-restart").button("disable");
                //$("#btn-db-wizard-restart").button("disable");
                
                

                
            }


        

        }, "json");
        
    }
    
    
    
}


function DBWizardClearSingle() 
{
	console.log($(this).closest("tr").find("select option:selected").val());
	
	
	var clickedElement = $(this).closest("tr").find("select option:selected").val();
	
	$(this).closest("tr").find("select").removeAttr("disabled"); 
	
	$(this).closest("tr").find("select  option[value=" + clickedElement + "]").remove();
	
	$(this).closest("td").html("<img class='mono-icon' style='float:left' src='icons/mono_right_tag_16.png'>");
	
	
	$.each(DBFieldNames, function(index, value){ 

 
   

		if( clickedElement == DBFieldNames[index]){  

				$(".sel-db-wizard-fields").append("<option value='"+DBFieldNames[index]+"'>"+DBFieldDisplayNames[index]+"</option>");
			}
		
		
    });
	
	
}


function DBWizardMatchFieldsRestart()
{
    $(".sel-db-wizard-fields").removeAttr("disabled");
    $(".td-db-wizard-middle-graf").html("<img class='mono-icon' style='float:left' src='icons/mono_right_tag_16.png'>");
    DBSelBuilder();
}

function InputNewDBClearError()
{
    $("#td-new-db-error").html("");
    $("#input-new-db").css("border-color", "#C0C0C0");
}

function DBWizardDenyLeads()
{
    $.post("_bases_de_dados-requests.php", {action: "DBWizardDenyLeads", DBID: editedDB, LeadsToDelete: insertedLeadsIDs }, function(json){
    
    DBWizardRestart();
    
    
    }, "json");
}

function DialogDbsOnReset()
{
    var Lists2Reset = new Array();

   $.each($(".checkbox-dbs-reset"), function(index, value){
       if($(this).parent().hasClass("checked"))
       {
            Lists2Reset.push($(this).attr("id"))
       }
   }) 
   
$.post("_bases_de_dados-requests.php", {action: "DBResetLists", Lists2Reset: Lists2Reset }, function(json){}, "json")

}

function DialogDbsOnOpen()
{

    $.post("_bases_de_dados-requests.php", {action: "DBResetGetDBList", CampaignID: CampaignID }, function(json){
        
        
      $.each(json.list_id, function(index, value){
          
          
        if( ((index/2) % 1) != 0 )
        {
            $("#table-dbs-reset-2").append("<tr><td><input class='checkbox-dbs-reset' type='checkbox' id='"+json.list_id[index]+"'></td><td style='padding-top:1px'><label style='display:inline;' for='"+json.list_id[index]+"'>" + json.list_name[index] + "</label></td></tr>");
        }
        else
        {
            $("#table-dbs-reset-1").append("<tr><td><input class='checkbox-dbs-reset' type='checkbox' id='"+json.list_id[index]+"'></td><td style='padding-top:1px'><label style='display:inline;' for='"+json.list_id[index]+"'>" + json.list_name[index] + "</label></td></tr>")
        }    
         
      })  
    
    
    $(".checkbox-dbs-reset").uniform();
    
    }, "json");
    
    
    
}

function ResetDbsSelectAll()
{

        $.each($(".checkbox-dbs-reset").parent(), function(){
        $(this).addClass("checked");
    })
}

function ResetDbsSelectNone()
{

        $.each($(".checkbox-dbs-reset").parent(), function(){
        $(this).removeClass("checked");
    })
}

function onsubmittest(that)
{
    
    $("#hidden_error_line").val(DBLoadErrorLine);
        $("#hidden_error_text").val(DBLoadErrorText);
        $("#hidden_error_phone").val(DBLoadErrorPhone);
        
    

}

function ForceLoadDuplicates()
{
    $(this).html("<img class='img-action' src='icons/mono_check_16.png'>");
    $("#btn-db-wizard-match-fields-duplicates-button").button({disabled: true});

    
     $.post("_bases_de_dados-requests.php", {action: "ForceLoadDuplicates", DuplicatesInserts: DBLoadKeepDuplicates, DBID: editedDB }, function(json){
        

        
        $.each(json.inserted_ids, function(index, value){
            insertedLeadsIDs.push(value);
        })
        
        console.log(insertedLeadsIDs);
        console.log(json.inserted_ids);
         
     }, "json")
    
    
    
    $('#td-db-wizard-load-duplicates-msg').html("Contactos duplicados carregados com sucesso.")
    
}

function DownloadDB()
{
    $("#download-db-id").val(editedDB)
}


$("body")
.on("click", ".db-checkbox", DBSwitch)
.on("click", "#btn-select-all-dbs", SelectAllDBs)
.on("click", "#btn-select-no-dbs", UnselectAllDBs)
.on("click", "#btn-dbs-active-inactive-switch", selectAllDBs)
.on("click", ".dbs-deactivate", DeactivateDB)
.on("click", ".dbs-activate", ActivateDB)
.on("click", ".dbs-edit", {dialog: "#dialog-dbs-edit", editDB: true}, DialogOpen)
.on("click", ".editdbs-view-details", {dialog: "#dialog-edit-dbs-contact-details"}, DialogOpen)
.on("click", "#btn-new-db", NewDB)
.on("click", "#btn-db-wizard-load-file", DBWizardUpload)
.on("click", "#btn-db-wizard-restart", DBWizardRestart)
.on("change", ".sel-db-wizard-fields", DBWizardFieldChange)
.on("click", "#btn-db-wizard-match-fields", DBWizardMatchFields)
.on("click", "#btn-db-wizard-match-fields-restart", DBWizardMatchFieldsRestart)
.on("input focus", "#input-new-db", InputNewDBClearError)
.on("click", "#btn-db-wizard-deny-leads", DBWizardDenyLeads)
//.on("click", "#btn-db-wizard-view-details", LoadDBDownloadDetails)
.on("click", ".add-more-leads", {dialog: "#dialog-db-wizard", dbwizardedit: true}, DialogOpen)
.on("click", "#btn-dbs-reset", {dialog: "#dialog-dbs-reset"}, DialogOpen)
.on("click", "#btn-reset-dbs-select-all", ResetDbsSelectAll)
.on("click", "#btn-reset-dbs-select-none", ResetDbsSelectNone)
.on("click", "#btn-db-wizard-match-fields-duplicates-button", ForceLoadDuplicates)
.on("click", "#btn-dialog-dbs-download-db", DownloadDB)
.on("click", ".db-wizard-clear-single", DBWizardClearSingle)

