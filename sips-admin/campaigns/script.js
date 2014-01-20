var User, UserGroup, AllowedCampaigns, CampaignID, CampaignEdit, CampaignLists, LastEnabledTab = 0;
var editedCampaignName, editedCampaignDescription;
var editedPause;
var editedFeed, alteredFeeds = {};
var editedRecycle, EditRecycleTimeSpinner, EditRecycleTriesSpinner, tableEditRecycle, tableRecycleContactDetails, editedTryType;
var hasScript, ScriptToCopy;
var editedField, AllFields = new Array('SOURCE_ID', 'LIST_ID', 'PHONE_CODE', 'GENDER', 'RANK', 'OWNER', 'VENDOR_LEAD_CODE', 'PHONE_NUMBER','TITLE','FIRST_NAME','MIDDLE_INITIAL','LAST_NAME','ADDRESS1','ADDRESS2','ADDRESS3','CITY','STATE','PROVINCE','POSTAL_CODE','COUNTRY_CODE','DATE_OF_BIRTH','ALT_PHONE','EMAIL','SECURITY_PHRASE','COMMENTS', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15'), AvailFields = new Array('PHONE_NUMBER','TITLE','FIRST_NAME','MIDDLE_INITIAL','LAST_NAME','ADDRESS1','ADDRESS2','ADDRESS3','CITY','STATE','PROVINCE','POSTAL_CODE','COUNTRY_CODE','DATE_OF_BIRTH','ALT_PHONE','EMAIL','COMMENTS', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15');
var editedDB, editedDBName, tableEditDB, editedDBFeed, tableDbsContactDetails, editedDBCampaign, createdDB, createdDBName, ConvertedFile, insertedLeadsIDs = new Array(), DBFieldNames = new Array(), DBFieldDisplayNames = new Array();


$(function()
{
	ConstructCampaignMonitor();
});

function ConstructCampaignMonitor()
{
$.ajax({
            type: "GET",
            url: "views/campaign-monitor.html",
            success: function(html) 
            { 
                $("body").html(html);

                $('#tbl_campaign-list').dataTable(
                {
                    "aaSorting": [[1, 'asc'], [2, 'desc']],
                    "iDisplayLength": 10,
                    "sDom": '<"top"f><rt><"bottom"p>',
                    "bSortClasses": false,
                    "bJQueryUI": true,  
                    "bProcessing": true, 
                    "bRetrieve": true,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": 'requests.php',
                    "fnServerParams": function (aoData)  
                    {
                        aoData.push( 
                                    { "name": "action", "value": "dT_campaign-monitor" }
                                   );
                    },
                    "aoColumns": [ 
                                    { "sTitle": "Editar", "sWidth":"16px", "sClass":"dataTables-column-center" },
                                    { "sTitle": "Campanha", "sClass":"dt-column-camp" }, 
                                    { "sTitle": "Activa", "sClass": "dataTables-column-center", "sWidth": "16px", "sType": "string" },
                                    { "sTitle": "Tipo de Campanha", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Rácio", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Pausas Activas", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Feedbacks Activos", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Reciclagem", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Script", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Campos Dinâmicos", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Nº de Contactos", "sClass": "dataTables-column-center", "sWidth": "16px" },
                                    { "sTitle": "Data de Criação", "sClass": "dataTables-column-center", "sWidth": "100px" }
                                 ], 
                    "oLanguage": { "sUrl": "extras/datatables/language.txt" },
                    "fnInitComplete": function(oSettings, json)
                    { 
						CampaignMonitorElemInit();
                        User = json.user;
						UserGroup = json.user_group;
                        AllowedCampaigns = json.allowed_campaigns;
	                    $("#tbl_campaign-list_wrapper .top").append("<div class='css-dt-title'>Gestão de Campanhas <span class='css-orange-caret'>></span> Lista de Campanhas</div>");      								               
                   		$("#table-wrapper").fadeIn(200);
                }
            }); 
        }
    });
}

function CampaignMonitorElemInit()
{
	$("#btn-new-campaign").button();
}

function ConstructNewCampaign() 
{
    $.ajax({
        type: "GET",
        url: "views/campaign-editor.html",
        success: function(html)
        {
            $("body").html(html);
            CampaignEdit = false; 
            CampaignEditorElemInit();
            MiscOptionsBuilder("NEW");
            $.post("requests.php",
            {action:"iscloud"},
            function(data){
                if(data.iscloud){
                    $(".iscloud").hide();
                }
            },"json");
            $.post("requests.php",
            {action:"client"},
            function(data){
                if(data.client==="necomplus"){
                    $(".necomplus").show();
                }
            },"json");
        }
	}); 
};

function ConstructEditCampaign(event)
{
	CampaignID = event.currentTarget.attributes['html-campaign-id'].value;
    $.ajax({
        type: "GET",
        url: "views/campaign-editor.html",
        success: function(html) 
        {
			
            $("body").html(html);
            CampaignEdit = true;
            CampaignEditorElemInit();
            MiscOptionsBuilder("EDIT");
            $.post("requests.php",
            {action:"iscloud"},
            function(data){
                if(data.iscloud){
                    $(".iscloud").hide();
                }
            },"json");
            $.post("requests.php",
            {action:"client"},
            function(data){
                if(data.client==="necomplus"){
                    $(".necomplus").show();
                }
            },"json");
        }
    });
}

function CampaignEditorElemInit() 
{
	
	$(".btn-nav-prev").button(); 
	$(".btn-nav-next").button();    
    
	if(!CampaignEdit){ $(".tr-nav-next").show(); }
	
    $("input[type=checkbox], input[type=radio]").uniform();
     
    $( "#wizard-tabs" ).tabs({
            activate: function( event, ui ) 
            {
                if(ui.newPanel.selector == "#tab2"){ if(CampaignEdit) { PauseListBuilder('EDIT'); } else { PauseListBuilder('ALL'); } }
                if(ui.newPanel.selector == "#tab3"){ if(CampaignEdit) { FeedListBuilder('EDIT'); } else { FeedListBuilder('ALL'); } }
                if(ui.newPanel.selector == "#tab4"){ RecycleElemInit(); RecycleListBuilder('ALL'); }
				if(ui.newPanel.selector == "#tab5"){ ScriptElemInit(); ScriptListBuilder(); }
				if(ui.newPanel.selector == "#tab6"){ FieldsListBuilder("ALL"); }
				if(ui.newPanel.selector == "#tab7"){ DBListBuilder("ALL"); }
				if(ui.newPanel.selector == "#tab8"){ DialerOptionsBuilder(); }
				
            },
            heightStyle: "fill"
    });
}

function NextTab()
{
var CurrentTab = $( "#wizard-tabs" ).tabs( "option", "active" );
    var DisabledTabs = $( "#wizard-tabs" ).tabs( "option", "disabled" );
    var NextDisabledTab = DisabledTabs[0];
    switch(CurrentTab)
    {
        case 0:
        {  
            if(submit_OpcoesGerais())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }
        case 1:
        { 
            if(submit_Pausas())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }
        case 2:
        { 
            if(submit_Feedbacks())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }
        case 3:
        { 
            if(submit_Reciclagem())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }
        case 4:
        { 
            if(submit_Script())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }
        case 5:
        { 
            if(submit_Dfields())
            { 
                if(NextDisabledTab-1 == CurrentTab)
                {
                    $("#wizard-tabs").tabs("enable" , NextDisabledTab);
                    $("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
                }
    
            }
        break;
        }       
        /*case "li-pausas":{ var submitted = submit_Pausas(); console.log("passou pausas"); break;  }
        case "li-feeds" :{ var submitted = submit_Feedbacks(); break; }
        case "li-scripts" :{ var submitted = submit_Scripts(); break; }
        case "li-dfields" :{ var submitted = submit_DFields(); break; }
        case "li-recycle" :{ var submitted = submit_Reciclagem(); break; } */
    }

}

function OddEvenRefresh(Flag, Table)
{
    var oddeven;
	
    if($("#"+Table+" tr").length == 0 && Flag == "REMOVE"){ 
	
    if(Table.match("feeds") && $("#btn-feeds-active-inactive-switch").hasClass("feed-hidden-switch")) { $('#span-no-feeds').html("Não existem Feedbacks disponíveis em sistema."); }  else { $('#span-no-feeds').html("Não existem Feedbacks inactivos em sistema."); }; 
    if(Table.match("pauses") && $("#btn-pauses-active-inactive-switch").hasClass("pause-hidden-switch")) { $('#span-no-pauses').html("Não existem Pausas disponíveis em sistema."); }  else { $('#span-no-pauses').html("Não existem Pausas inactivas em sistema."); }
	
	
	
    }

    $.each($("#"+Table+" tr"), function(index, value){
    
        if( ((index/2) % 1) != 0 )
        {
            oddeven = 0;
        }
        else
        {
            oddeven = 1;
        }
        
        $.each($(this).children(), function(){
            
            $(this).removeClass("odd-even-table-rows")  
            
            if($(this).is("td") && oddeven)
            {
                if(!$(this).hasClass("odd-even-ignore")){ $(this).addClass("odd-even-table-rows") }
            }

        });
    
    });

    
}

function DialogClose()
{
	$(this).dialog("close");
}

function DialogOpen(event)
{
	if(typeof event.currentTarget.attributes["pause-to-edit"] != 'undefined') { editedPause = event.currentTarget.attributes["pause-to-edit"].value; }
	if(typeof event.currentTarget.attributes["feed-to-edit"] != 'undefined') { editedFeed = event.currentTarget.attributes["feed-to-edit"].value; }
	if(typeof event.currentTarget.attributes["recycle-to-edit"] != 'undefined') { editedRecycle = event.currentTarget.attributes["recycle-to-edit"].value; }
	if(typeof event.currentTarget.attributes["inner-try-type"] != 'undefined') { editedTryType = event.currentTarget.attributes["inner-try-type"].value; }
	if(typeof event.currentTarget.attributes["fields-edit"] != 'undefined') { editedField = event.currentTarget.attributes["fields-edit"].value; }
	if(typeof event.data.editDB != 'undefined') { editedDB = event.currentTarget.parentNode.parentElement.attributes["id"].value; }
	if(typeof event.currentTarget.attributes["feedback-id"] != 'undefined') { editedDBFeed = event.currentTarget.attributes["feedback-id"].value; }
	if(typeof event.data.dbwizardedit != 'undefined') { console.log(event.currentTarget.parentNode.parentElement.attributes["id"].value);  editedDB = event.currentTarget.parentNode.parentElement.attributes["id"].value; }
	
	$(event.data.dialog).dialog("open");
}

$(window).on('beforeunload', function()
{

    $.ajax({
            type: "POST",
            url: "requests.php",
            async: false,
            data: 
            { 
                action: "RollbackIncompleteCampaign",
                sent_campaign_id: CampaignID
            },
            success: function(data) {

                }
    });

  
});
 
$("body").on("click", "#img-campaign-edit",  ConstructEditCampaign)
.on("click", "#btn-new-campaign", ConstructNewCampaign )
.on("click", ".btn-nav-prev", ConstructCampaignMonitor )




