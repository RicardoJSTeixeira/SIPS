<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
require("../../../ini/functions.php");
ini_set("display_errors", "1");
?>

<?
// PHP ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ALLOWED CAMPAIGNS
$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";
// NEW CAMPAIGN ID
	$query = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_campaigns",$link));
	$num_campaigns = ($query[0]+30);
	while(strlen($num_campaigns) < 5) { $num_campaigns = "0".$num_campaigns; }
	$campaign_id = "C".$num_campaigns;

if(isset($_GET['camp_id'])){ $campaign_id = $_GET['camp_id']; }    

//echo $_GET['camp_id'];
//echo $campaign_id;
    

// BASES DE DADOS 
	$query = mysql_query("SELECT list_id, list_name, active FROM vicidial_lists WHERE campaign_id='$campaign_id'") or die(mysql_error());
    
	if(mysql_num_rows($query) >= 1){

		while($row = mysql_fetch_assoc($query)){
		
        if($row['active']=="Y"){$ppp = "checked";} else {$ppp = "";}
				
		$bd_list .= "<li id='$row[list_id]' class=''>
						<table>
						<tr>
							<td style='width:16px'><input id='$row[list_id]' $ppp type='checkbox'></td><td><label class='' style='display:inline;' for='$row[list_id]'>$row[list_name]</label></td>
							<td width='24px'><img class='mono-icon cursor-pointer load-leads' src='/images/icons/mono_icon_database_16.png'></td>
							<td width='24px'><img class='mono-icon cursor-pointer' src='/images/icons/mono_wrench_16.png'></td>
						</tr>
						</table>
					</li>";	
			
			
			
			
			
			}

		
		
		} else { $bd_list = "<span id='no-dbs'>Sem Bases de Dados associadas.</span>";
		
	
		
		
		  }
	
	
		
// END PHP /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
?>

<!-- TABS -->

<div class="cc-mstyle">
	<table>
		<tr>
			<td id="icon32"><img src='/images/icons/construction_32.png' /></td>
			<td id='submenu-title' style='width:400px'> Criação de Campanhas | <span id="header-status" style="font-weight:normal"></span></td>
			<td style='text-align:left'><span id="message"></span></td>
		</tr>
	</table>
</div>
<br>
<div style='width:97%; margin:0 auto; border:none;'>

	<div id="wizard-tabs" style="display:none"> 
		<ul> 
	<!--		<li id="li-opcoes"><a href="#tab1">Opções Gerais</a></li> 
	<!--		<li id="li-pausas"><a href="#tab2">Pausas</a></li>
	        <li id="li-feeds"><a href="#tab3">Feedbacks</a></li>
            <li id="li-recycle"><a href="#tab8">Reciclagem</a></li> 
			<li id="li-scripts"><a href="#tab4">Scripts</a></li>
			<li id="li-dfields"><a href="#tab5">Campos Dinâmicos</a></li> -->
			<li id="li-dbs"><a href="#tab6">Bases de Dados</a></li>
            <li id="li-ivrout"><a href="#tab7">IVR Out</a></li> 
           
			
		</ul>

        <!-- BASES DE DADOS -->
		<div id="tab6">
        <table class="table-100">
		<tr>
			<td class="td-half-left">
				
				<div class="div-title">Bases de Dados da Campanha</div>
				
				
				<ul id="database-list">
				<? echo $bd_list; ?>	
				</ul>
			</td>
			<td class="td-half-right">
				<div class="div-title">Criar nova Base de Dados</div>
			
				<table>
					<tr>
						<td>
							<input id="add-database-name" class="input-text" style='width:95%' type="text" >
						</td>
						<td width=10px></td>
						
						<td class="td-icon">
							<img id="new-database" class="img-icon" src="/images/icons/database_add_32.png">
						</td>
					</tr>

				</table>
                
				<div class="div-title">Legenda</div>
				<table>
               		 <tr class="height24">
						<td class="td-icon"><img class='mono-icon' src='/images/icons/mono_icon_database_16.png'></td><td>Carregar Contactos</td>				
					</tr>
					
					 <tr class="height24">
						<td class="td-icon"><img class='mono-icon' src='/images/icons/mono_wrench_16.png'></td><td>Ver Detalhes / Configurações</td>				
					</tr>
				</table>
			</td>
		</tr>		
		<tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
		</table>
        
</div>
		<!-- IVR OUT -->
        <div id="tab7">
                
         <br /><br />       
        <div id="editar-audio">
            <form id='construtortts'>
                <label for="texto">Texto para traduzir:</label>
                <label id="comprimento" ></label>
                <br><br>
                <div id="google_translate_element" align="center" onclick='$("#gravacao").hide()' >
                    <input type="hidden" id="selOpt" value="" />
                    <textarea id="texto" name="texto" onkeyup="calculo();" cols="100" rows="5" ></textarea>
                    <br><br>
                </div>
                <select id="lang">
                    <option value='de'>Alemão</option>
                    <option value='pt-BR'>Brasil</option>
                    <option value='ca'>Catalão</option>
                    <option value='cs'>Checo</option>
                    <option value='zh-CN'>Chinês</option>
                    <option value='zh-TW'>Chinês Tradicional</option>
                    <option value='hr'>Croata</option>
                    <option value='da'>Dinamarquês</option>
                    <option value='es'>Espanhol</option>
                    <option value='fr'>Frances</option>
                    <option value='el'>Grego</option>
                    <option value='nl'>Holandês</option>
                    <option value='hu'>Hungariano</option>
                    <option value='is'>Icelandic</option>
                    <option value='en'>Inglês</option>
                    <option value='ga'>Irlandês</option>
                    <option value='it'>Italiano</option>
                    <option value='ja'>Japonês</option>
                    <option value='no'>Norueguês</option>
                    <option value='pl'>Polaco</option>
                    <option value='pt' selected>Português</option>
                    <option value='ro'>Romeno</option>
                    <option value='ru'>Russo</option>
                    <option value='sv'>Sueco</option>
                    <option value='tr'>Turco</option>
                    <option value='uk'>Ucraniano</option>
                </select>
                <input type="button" value="Ouvir" onclick="ajaxgetsound('simple')" />&nbsp;&nbsp;<input type="button" value="Ligar para o telefone" onclick="ajaxgetsound('phone')" /><br><br>
        
        
        
                <div id="gravacao" style="display:none">
                    <br><br>
                    <audio controls="controls" id="wavplayer" >
                        <source src="" type="audio/wav" >
                        O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
                    </audio>
                    <br><br>
                    <label for="descricao">Introduza um nome para este ficheiro:</label>
                    <input type="text" id="desc" size="20" />
                    <br>
        
                </div>
        
        
            </form>
        </div>
        
        
        
        <div id="construir-mensagem" style="display:none">
            <table border="0">
                <tr>
                    <td width="300px"><label for="texto">Mensagem a construir:</label></td>
                    <td><label id="comprimento_dois" ></label></td>
                </tr>
                <tr>
                <div id="google_translate_element" align="center" onclick='$("#gravacao_din").hide()' >
                    <td>    
                        <textarea id="texto_dois" name="texto_dois" draggable="false" style="float:left" onkeyup="calculo('dois');" cols="70" rows="5" ></textarea> </div> </td>
                <td>    
                    <table border="0" align="center">
                        <tr><td align="center">Campos dinâmicos: <br /><br /></td></tr>
                        <tr><td align="center">
                                <select id="dinfields">
        
                                </select><br /><br />
                            </td>
                        </tr>
                        <tr><td align="center"><input type="button" value="Adicionar campo" id="adddinfield" onclick="addtextfield();" /></td></tr>
                    </table>
                </td>
                </tr>
                <tr>
                    <td>
                        <select id="lang_din">
                            <option value='de'>Alemão</option>
                            <option value='pt-BR'>Brasil</option>
                            <option value='ca'>Catalão</option>
                            <option value='cs'>Checo</option>
                            <option value='zh-CN'>Chinês</option>
                            <option value='zh-TW'>Chinês Tradicional</option>
                            <option value='hr'>Croata</option>
                            <option value='da'>Dinamarquês</option>
                            <option value='es'>Espanhol</option>
                            <option value='fr'>Frances</option>
                            <option value='el'>Grego</option>
                            <option value='nl'>Holandês</option>
                            <option value='hu'>Hungariano</option>
                            <option value='is'>Icelandic</option>
                            <option value='en'>Inglês</option>
                            <option value='ga'>Irlandês</option>
                            <option value='it'>Italiano</option>
                            <option value='ja'>Japonês</option>
                            <option value='no'>Norueguês</option>
                            <option value='pl'>Polaco</option>
                            <option value='pt' selected>Português</option>
                            <option value='ro'>Romeno</option>
                            <option value='ru'>Russo</option>
                            <option value='sv'>Sueco</option>
                            <option value='tr'>Turco</option>
                            <option value='uk'>Ucraniano</option>
                        </select>
                        <input type="button" value="Ouvir" onclick="ajaxgetsound('din')" />&nbsp;&nbsp;<input type="button" value="Ligar para o telefone" onclick="ajaxgetsound('phone_din')" /><br><br>
                    </td>
                </tr>
                <tr>
                    <td><label id='curPhrase'></label></td>
                </tr>    
            </table>
        
        
            <div id="gravacao_din" style="display:none">
                <br><br>
                <audio controls="controls" id="wavplayer_din" name="wavplayer_din">
                    <source src="" type="audio/wav" >
                    O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
                </audio>
                <br><br>
                <label for="desc_din">Introduza um nome para este ficheiro:</label>
                <input type="text" id="desc_din" size="20" />
                <br>
        
            </div>
        
        </div>
        
        
        
        <div id="serverselect">
            <table>
                <tr>
                    <td colspan="4">
                        <h3>Ficheiros disponíveis na campanha <? echo $campaign_id; ?> :</h3>
                        <br /><br />
                    </td>
                <tr>
                    <td><div class=cc-mstyle style='height:28px; float:left;border:none;  '>Para áudio inicial: <div id='audioinicial' style='height:28px; float:right; border:none;'></div> </div></td><td><div><input type="button" style='float:left; width:100px;' value="Ouvir" name="ffouvir" id="ffouvir" onclick="playrecord('ff');" /></div></td>
                    <td><div class=cc-mstyle style='height:28px; float:left; border:none;  '>Para áudio final: <div id='audiofinal' style='height:28px; float:right; border:none;'></div> </div></td><td><div><input type="button" style='float:left; width:100px;' value="Ouvir" name="sfouvir" id="sfouvir" onclick="playrecord('sf');" /></div></td>
                </tr>
            </table>
        
            <table align="center">
                <tr>
                    <td colspan="2" align="center">
                        <div id="gravacao" style="display:block">
                            <br /><br />
                            <div class=cc-mstyle style='height:28px; float:left; border:none;  '> Ficheiro Seleccionado: <h2> <label id='fselect'> </label> </h2> </div>
                            <br /><br />
                            <audio controls="controls" id="recordplayer" name="recordplayer" style="height:50px">
                                <source src="" type="audio/wav" >
                                O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
                            </audio>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        
        
        
        
        <div class=cc-mstyle style='border:none; width:90%;' name='working-area'>
            <form action="setup.php" name="editivr" id="editivr" method="post" target="_self" onsubmit="return validateForm()">
                <input type="hidden" name='campaign_id' value='C00001' />
        
                <table  style="min-width:500px">
                    <tr>
                        <td style='width:200px'>  Áudio de introdução </td>
                        <td><input style='float:left; width:300px;' type="text" name="firstaudio" id="firstaudio" value="<?php echo $nome; ?>"/>
                        <td><input style="float:left;" type="button" name="fatts" id="fatts" value="Criar Áudio TTS" />
                        <td><input style="float:left;" type="button" name="faserver" id="faserver" value="Seleccionar do servidor" />
                        </td>
                    </tr>
                </table>
        
                <table >
                    </tr>
                    <td style='width:200px'> Digito de Interessado </td>
                    <td><input style='float:left; width:60px;' type="text" name="optin" value="1"/></td>
                    <td style='width:200px'> Digito de não interessado </td>
                    <td><input style='float:left; width:60px;' type="text" name="optout" value="2"/></td>
                    <td style='width:200px'> Digito de repetir </td>
                    <td><input style='float:left; width:60px;' type="text" name="optout" value="3"/></td>  
                </table>
        
                <table >
                    <tr>
                        <td style='width:200px'> Acção sem resposta </td>
                        <td><select style='float:left; width:100px;' name="noinp" >
                                <option value="---">---</option>
                                <option value="optin">Interessado</option>
                                <option value="opout">Não Interessado</option>
                            </select>
                        </td>  
                    </tr>
                </table>
        
        
                <table >
                    <tr><td style='width:200px'> Mensagem dinâmica </td>
                        <td><input style='float:left; width:300px;' type="text" name="mprincipal" id="mprincipal" value=""/><td><input style="float:left;" type="button" name="constroimensagem" id="constroimensagem" value="Construir Mensagem" /></td>
                    <tr>
        
                    <tr><td style='width:200px'> Áudio de não interessado </td>
                        <td><input style='float:left; width:300px;' type="text" name="audioni" value=""/>
                        <td>    <input style="float:left;" type="button" name="satts" id="satts" value="Criar Áudio TTS" />
                        <td>    <input style="float:left;" type="button" name="saserver" id="saserver" value="Seleccionar do servidor" />
        
                        </td>
        
                </table>
        
        
        
        
                </table>
                <br /><br />
                <table >
                    <tr>
                        <td><button id="temp-cancelar">Cancelar</button></td>
                        <td><button id="temp-gravar">Gravar</button></td>
                    </tr></table>
        
        </div>
        
               
                
                </div> 


    <!-- DIALOGS -->
    
    <div id="dialog-edit-pause-time" style="display:none">
    <div class="div-title">Novo Tempo de Pausa</div>
    <input id="spinner-new-pause-time" style="width:24px;" type="text"  value =""/>
    </div> 
    
    <div id="dialog-edit-field-name" style="display:none">
    <div class="div-title">Novo Nome para o Campo</div>
    <input id="new-field-name" type="text" value=""/>
    </div>
    
    <div id="dialog-load-leads" style="display:none">

    <div id='div-file-upload'>
    <div class="div-title">Escolha do Ficheiro</div>
    <form enctype="multipart/form-data" method="POST" action="_upload.php">
    <table>
    <tr>
    	<td><input style="width:300px" type="file" name="fileToUpload" id="fileToUpload" /><br /></td>
    	<td><input type="button" onclick="UploadFile()" value="Carregar" /></td>
    </tr>
    </table>
    <div id="progressNumber"></div>
    <br /><br /><br />
    
    <table  id="table-file-loader-progress">
    <tr style="height:28px">
    <td id="file-loader-progress-msg">A carregar o ficheiro com os contactos...</td> <td style="width:50px" id="file-loader-progress-loader"><img style='margin-top:3px' src='/images/icons/loader-arrows.gif'></td>
    </tr>
    </table>
    <br />
    <table id="table-file-converter-progress">
    <tr style="height:28px">
    <td id="file-converter-progress-msg">A converter o ficheiro de contactos...</td> <td style="width:50px" id="file-converter-progress-loader"><img style='margin-top:3px' src='/images/icons/loader-arrows.gif'></td>
    </tr>
    </table>

    
    <center><div><input id="continue-button" type="button" value="Continuar" /></div></center>
    </form>
    </div>


    <div id="div-field-match">
    <div class="div-title">Confirmação da Associação de Campos</div>
   <table id="table-field-match">
    </table>
        
    <table>
    <tr>
    <td>
        <br><br><br>
    <input type="button" id="submit-field-match" value="Aceitar"  />
    </td>
    <br><br>
    <td id="file-insert-progress-msg" style="text-align:right;"></td>
    <td id="file-insert-progress-loader" style="width:16px">
    
    </td>
    </tr>
    </table>
    <table style="opacity:0" id="table-field-match2">
    </table>
    
    </div>

	<div id="dialog-edit-recycle-time" style="display:none">
    <div class="div-title">Novo Tempo de Reciclagem</div>
    <input id="spinner-edit-recycle-time" style="width:24px;" type="text"  value =""/>
    </div> 
    
    <div id="dialog-edit-recycle-tries" style="display:none">
    <div class="div-title">Nº de Tentativas de Reciclagem</div>
    <input id="spinner-edit-recycle-tries" style="width:24px;" type="text"  value =""/>
    </div> 
    
    <div id="dialog-new-campaign-name" style="display:none">
    <div class="div-title">Nome da Campanha</div>
    <input id="new-campaign-name-start" style="width:150px;" type="text"  value=""/>
    </div> 
    
    <div id="all-done-confirmation" style="display:none">
	<br />
    <br />
	Campanha Criada com sucesso!

    </div> 
    


</div> 


<script>
var campaign_id = '<? echo $campaign_id; ?>';
var new_campaign_name = "";

// NEW CAMPAIGN NAME

<?php if(!isset($_GET['camp_id'])){ ?>

//var new_campaign_name = "";
$("#new-campaign-name-start").focus(function(){$(this).css("border", "1px solid #c0c0c0")})
$( "#dialog-new-campaign-name" ).dialog({
        autoOpen: true,
        height: 200,
        width: 250,
        modal: true,
        resizable: false,
        buttons: 
			{
            "OK": function() { 
				if($("#new-campaign-name-start").val()=="") { $("#new-campaign-name-start").css("border" , "1px solid red")} else {
					$(this).dialog("close"); new_campaign_name = $("#new-campaign-name-start").val();
					
					}
			
			
			
			
				 },
           
            "Cancelar" : function() { $(this).dialog("close"); parent.mbody.location = '../index.php'; }
        	}
});  
    
      
        
          
<?php } ?>









// TEMP FFS

$('#temp-cancelar').button();
$('#temp-gravar').button();


// TAB NAVIGATION
var LastEnabledTab = 0;
$(".next-button").button().click(function()
{ 
	$("#wizard-tabs ul li").each(function(){
		if($(this).hasClass("ui-tabs-active")){ 

			switch($(this).attr("id")){
				case "li-opcoes":{ var submitted = submit_OpcoesGerais(); }
				case "li-pausas":{ var submitted = submit_Pausas(); break;  }
				case "li-feeds" :{ var submitted = submit_Feedbacks(); break; }
				case "li-scripts" :{ var submitted = submit_Scripts(); break; }
				case "li-dfields" :{ var submitted = submit_DFields(); break; }
				case "li-recycle" :{ var submitted = submit_Reciclagem(); break; }
			}
			
			
			if(submitted){
				
			if($(this).next().hasClass("ui-state-disabled")){LastEnabledTab++; $("#wizard-tabs").tabs("enable", LastEnabledTab); }
			//$(this).next().children().click();
			}
	 	} 
	})
	$("#wizard-tabs").tabs("option", "active", LastEnabledTab);  
})



// BASES DE DADOS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

$("#add-database-name").focus(function(){$(this).css("border", "1px solid #c0c0c0")});
$("#new-database").click(function(){
	
	if($("#add-database-name").val()==""){ $("#add-database-name").css("border", "1px solid red"); return false; }
	
	var database_name = $("#add-database-name").val(); 
	var new_campaign = 0;
	
	
	if($("ul[id='database-list'] li").length==0) {new_campaign = 1} 
	
	$.ajax({
			type: "POST",
			url: "_requests.php",
			data: {	action: "add_new_database_campaign",
					sent_new_campaign: new_campaign,
					sent_campaign_id: campaign_id,
					sent_database_name: database_name,
					sent_campaign_name: new_campaign_name
					},
			success: function(list_id)	{ 
			
			$("#dialog-testing").html(list_id);
				$("#database-list").append("<li id='"+list_id+"' class=''><table><tr><td style='width:16px'><input name='i-"+list_id+"' id='i-"+list_id+"' type='checkbox' checked='checked'></td><td><label class='' style='display:inline;' for='i-"+list_id+"'>"+database_name+"</label></td><td width='24px'><img class='mono-icon cursor-pointer load-leads' src='/images/icons/mono_icon_database_16.png'></td><td width='24px'><img class='mono-icon cursor-pointer' src='/images/icons/mono_wrench_16.png'></td></tr></table></li>"); 
				$("#add-database-name").val("");
				$("input[name=i-" +list_id+ "]").uniform();
				$( "#wizard-tabs" ).tabs( "refresh" ); 
				}
			}); 
	$("#no-dbs").html("");
	})


var clicked_db = "";
$(".load-leads").live("click", function(){
	clicked_db = $(this).parent().parent().parent().parent().parent().attr("id");

	$("#dialog-load-leads").dialog("open");
	})

$("#dialog-load-leads").dialog({ 
	title: ' <span style="font-size:13px; color:black">Carregar Contactos</span> ',
	autoOpen: false,
	height: 500,
	width: 800,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close"); CancelAddLeadsToDatabase();   } },
	open: function(){ 
		$("button").blur(); 
		$("#div-field-match").hide(); 
		$("#table-file-loader-progress").hide(); 
		$("#table-file-converter-progress").hide(); 
		$("#continue-button").hide();
		$("#file-loader-progress-msg").html("A carregar o ficheiro com os contactos...");
		$("#file-loader-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>")
		
		$("#file-converter-progress-msg").html("A converter o ficheiro de contactos...");
		$("#file-converter-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>")
		
	}
	
	
	
	
	
	
});

function CancelAddLeadsToDatabase(){
    $("#fileToUpload").val("");
	$("#div-field-match").hide(); 
	$('#div-file-upload').show(); 
	$('.filename').html(""); 
	$(".select-field-match").empty(); 
	$("#table-field-match").html("");
	$("#file-insert-progress-loader").html("");
	$("#file-insert-progress-msg").html("");
}

// GET DINAMIC FIELDS

$("#continue-button").click(function(){

	
	$.ajax({
			type: "POST",
			url: "upload.php",
			dataType: "JSON",
			data: {	action: "get_campaign_fields",
					sent_campaign_id: campaign_id,
					sent_db_id: clicked_db,
					sent_file_name: converted_file
					},
			success: function(result){
            $("#table-field-match").append("<tr><td><b>Telefone</b></td><td><b>Mensagem Inicial</b></td><td><b>Mensagem</b></td></tr>");    
            $.each(result.phone, function(index, value){
            
            if(result.msgi[index]===null){result.msgi[index] = "O ficheiro tem de estar na codificação UTF-8.";}
            if(result.msg[index]===null){result.msg[index] = "O ficheiro tem de estar na codificação UTF-8.";}
            
            
                    
    		$("#table-field-match").append("<tr style='height:28px'><td>"+ result.phone[index] +"</td><td>"+ result.msgi[index] +"</td><td>"+ result.msg[index] +"</td></tr>");
    
                
            })    
            var html_select = "";
				$.each(result.name, function(index, value){
					html_select = "<select class='select-field-match' id='"+value+"'><option value='-1'>---</option>";
					//$.each(result.headers, function(index1, value1){
					//html_select += "<option value='"+index1+"'>"+value1+"</option>";
					//})
                                        if(result.display_name[index] === 'Telefone') { html_select += "<option value='0' selected >Telefone</option>"; } else { html_select += "<option value='0' >Telefone</option>"; }
                                        if(result.display_name[index] === 'Mensagem Inicial') { html_select += "<option value='2' selected >Mensagem Inicial</option>"; } else { html_select += "<option value='2' >Mensagem Inicial</option>"; }
                                        if(result.display_name[index] === 'Mensagem') { html_select += "<option value='1' selected >Mensagem</option>"; } else { html_select += "<option value='1' >Mensagem</option>"; }
					html_select += "</select>";
                   
					$("#table-field-match2").append("<tr style='height:28px'><td>"+ result.display_name[index] +"</td><td>"+html_select+"</td></tr>");
	
				});	    
            }
            });
   
            
            

                
                           

        
        
        				
			
	
	$("#div-file-upload").hide();
	$("#div-field-match").show();
});

// SUBMIT MATCHING FIELDS AND UPLOAD DATA



$('#submit-field-match').click(function(){
	
	$("#file-insert-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>");
	$("#file-insert-progress-msg").html("A inserir contactos na base de dados...");
	
	var field_match_ids = new Array();
	var field_match_headers = new Array();

	$.each($('.select-field-match option:selected '), function(index, value){
		
		
		field_match_ids.push($(this).parent().attr("id"));
		field_match_headers.push($(this).val())
		
		
		
		} )	
	
	//alert(field_match_ids);
	//alert(field_match_headers);
	
	$.ajax({
			type: "POST",
			url: "upload.php",
			dataType: "JSON",
			data: {	action: "submit_campaign_fields",
					sent_campaign_id: campaign_id,
					sent_list_id: clicked_db,
					sent_file_name: converted_file,
					sent_match_ids: field_match_ids,
					sent_match_headers: field_match_headers
					},
			success: function(result){ 
				$("#file-insert-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");
				$("#file-insert-progress-msg").html("Contactos inseridos com sucesso na base de dados.");
				
				
			}
	})
	
})



$("#all-done-confirmation").dialog({ 
	title: ' <span style="font-size:13px; color:black"></span> ',
	autoOpen: false,
	height: 250,
	width: 300,
	resizable: false,
	buttons: { "OK" : function() { $(this).dialog("close"); parent.mbody.location = "../index.php"; } }

});




// LOADER ENGINE

var converted_file = "";

function UploadFile() {
	
	$("#table-file-loader-progress").show();
	
	var FileData = new FormData();
	FileData.append("fileToUpload", document.getElementById('fileToUpload').files[0]);
	var xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", uploadProgress, false);
	xhr.addEventListener("load", uploadComplete, false);
	xhr.addEventListener("error", uploadFailed, false);
	xhr.addEventListener("abort", uploadCanceled, false); 
	xhr.open("POST", "upload.php");
	xhr.send(FileData);
      }

function uploadProgress(evt) {
if (evt.lengthComputable) {
  var percentComplete = Math.round(evt.loaded * 100 / evt.total);
  if(percentComplete > 80){
	  
	$("#file-loader-progress-msg").html("Ficheiro carregado com sucesso.");
	$("#file-loader-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");  
	$("#table-file-converter-progress").show();
	
	  
	  
	  }
  //document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
}
else {
  //document.getElementById('progressNumber').innerHTML = 'unable to compute';
} 
} 

function uploadComplete(evt) {
/* This event is raised when the server send back a response */
//alert(evt.target.responseText);
	$("#file-converter-progress-msg").html("Ficheiro convertido com sucesso.");
	$("#file-converter-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");
	
	$("#continue-button").show();

$('#continue-button').attr("disabled", false);

	  
	  
		
		

converted_file = evt.target.responseText;

}

function uploadFailed(evt) {
alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
alert("The upload has been canceled by the user or the browser dropped the connection.");
}








 



// IVR OUT /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(function(){
        $('#satts').click(function(){
            $("#selOpt").val('3');
            $('#editar-audio').dialog('open');
        })
        $('#constroimensagem').click(function(){
            $("#selOpt").val('2');
            $('#construir-mensagem').dialog('open');
        })
        $('#fatts').click(function(){
            $("#selOpt").val('1');
            $('#editar-audio').dialog('open');
        })
        $('#saserver').click(function(){
            $("#selOpt").val('1');
            $('#serverselect').dialog('open');
        })
        $('#faserver').click(function(){
            $("#selOpt").val('2');
            $('#serverselect').dialog('open');
        })
    });

    var used_files = new Array();
    var texto_ivr;	
    var curPath;

    function addtextfield() {
	
        var curText = $("#texto_dois").val();
        var insertVar = $("#dinfields option:selected").text();
	
        $("#texto_dois").val( curText + "[" + insertVar + "] " );
        $("#texto_dois").focus();
    }


    function playrecord(curPlay) {
        if (curPlay=='ff') {
		
            curPath = $("#audioinicialselect").val();
		
            $("#fselect").html($("#audioinicialselect option:selected").text());
            var audioplayer = $('#recordplayer');
            audioplayer.empty();
            $("<source>").attr("src", "/ivr/"+curPath+".wav").appendTo(audioplayer);
            audioplayer[0].load();
           
		
        } else if (curPlay=='sf') {
		
            curPath = $("#audiofinalselect").val();
		
            $("#fselect").html($("#audiofinalselect option:selected").text());
            var audioplayer = $('#recordplayer');
            audioplayer.empty();
            $("<source>").attr("src", "/ivr/"+curPath+".wav").appendTo(audioplayer);
            audioplayer[0].load();
       
		
        }
	
    }

    function process_file(valor) {
        if (valor=='aceitar') { 
            posicao = $("#selOpt").val();
            var lingua = $("#lang").val();
            var desc = $("#desc").val();
			
            if (desc==null || desc=='') { alert ('O nome do ficheiro é obrigatório'); return false; } else { 
                $.post('requests_ivr.php',{ficheiro_processar:texto_ivr,lang:lingua,desc:desc,posicao:posicao,campaign_id:"<?php echo $campaign_id ?>"}, function(data) {
                }); return true; }
        } else if(valor=='descartar'){ 
            alert('descartar'); return true; 
        }	
	
    }


    function calculo(curT) {
        
        if (curT=='dois') { $('#comprimento_dois').html($('#texto_dois').val().length); } else {
            $('#comprimento').html($('#texto').val().length); }
		
		
    }
	
    function ajaxgetsound(xyz) {
		
        if (xyz=='din') {
            var texto = $("#texto_dois").val();
            var lingua = $("#lang_din").val();
	
            $.post('requests_ivr.php',{data:texto,lang:lingua,din:"yes",campaign_id:"<? echo $campaign_id; ?>"}, function(data) {
				
        
            var retStr = data.split("###");
            var wav_file = retStr[1];
            var curP = retStr[0];
            
            $("#curPhrase").html(curP);
        
        
        
        
                used_files[used_files.length] = wav_file ;
                texto_ivr = wav_file;
                var audioplayer = $('#wavplayer_din');           
                audioplayer.empty();
		
                $("<source>").attr("src", "/ivr/"+wav_file+".wav").appendTo(audioplayer);
		
                audioplayer[0].load();
                $("#gravacao_din").show();
			
            });
        } else if (xyz=='phone_din') {
             phone = prompt("Insira o nº de telefone"); 
			
            var texto = $("#texto_dois").val();
            var lingua = $("#lang_din").val();
			
            $.post('requests_ivr.php',{gmd:"yes",data:texto,lang:lingua,phone:phone,din:"yes",campaign_id:"<? echo $campaign_id; ?>"}, function(data) {
           
			
            });
        }
		
        if(xyz=='phone') {
            phone = prompt("Insira o nº de telefone"); 
			
            var texto = $("#texto").val();
            var lingua = $("#lang").val();
			
            $.post('requests_ivr.php',{data:texto,lang:lingua,phone:phone,campaign_id:"<? echo $campaign_id; ?>"}, function(data) {
            
			
            });
			
        } else if (xyz=='simple') {
		
            var texto = $("#texto").val();
            var lingua = $("#lang").val();
	
            $.post('requests_ivr.php',{data:texto,lang:lingua}, function(data) {
                $('.result').html(data);
            
                used_files[used_files.length] = data ;
                texto_ivr = data;
                //alert(texto_ivr);
                var audioplayer = $('#wavplayer');
                audioplayer.empty();
		
                $("<source>").attr("src", "/ivr/"+data+".wav").appendTo(audioplayer);
		
                audioplayer[0].load();
                $("#gravacao").show();
			
            }); }
    }

    function delete_unused_files() {
        $.post('requests_ivr.php',{del_files:used_files});
    }

    $( "#editar-audio" ).dialog({
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        resizable: false,
        buttons: {
            "Gravar ficheiro": function() {
                var desc = $("#desc").val();
                $("#firstaudio").val(desc);
                if (process_file('aceitar')) {
                    delete_unused_files();
                    
            
                    
                    $( this ).dialog( "close" ); }
            },
            "Cancelar" : function() {
                process_file('descartar');
				
                delete_unused_files();
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            $("#construtortts")[0].reset();
            $('#comprimento').html('0');
            delete_unused_files();
        },
        beforeClose: function() {
				
				
        }
    });
	
    $( "#construir-mensagem" ).dialog({
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        resizable: false,
        buttons: {
            "Guardar Mensagem": function() {
                var desc = $("#desc").val();
                $("#mprincipal").val(desc);
		var texto = $("#texto_dois").val();
                var ficheiro = $("#desc_din").val();
                $.post('requests_ivr.php',{gmd:"yes",md:texto,ficheiro:ficheiro,campaign_id:"<? echo $campaign_id; ?>"}, function(data) {
              
                });
                $( this ).dialog( "close" );
                },
            "Cancelar" : function() {
				
                $( this ).dialog( "close" );
            }
        },
        open: function() {
			
            $.post("requests_ivr.php", {pop_din_fields:"yes",campaign_id:"<? echo $campaign_id; ?>"}, function(list) {
				
                $("#dinfields").html(list);
				
            });
        },
        close: function() {
            //	$("#form")[0].reset();
	
            delete_unused_files();
        },
        beforeClose: function() {
				
				
        }
    });
	
    $( "#serverselect" ).dialog({
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        resizable: false,
        open: function(){ 
			
            $.post("requests_ivr.php", {recordSelector:'yes',campaign_id:"<? echo $campaign_id; ?>"}, function(data) {
			
                var Selectors = data.split("&&&");
			
                $("#audioinicial").html(Selectors[0])
                $("#audiofinal").html(Selectors[1])
			
            });
			
        },
        buttons: {
            "Aceitar Ficheiro": function() {
				
                var ficheiro = $("#fselect").html();
                var posicao = $("#selOpt").val();
                var fname = curPath;
                $.post("requests_ivr.php", {aceitarficheiro:'yes',campaign_id:"<? echo $campaign_id; ?>",ficheiro:ficheiro,posicao:posicao,fname:fname}, function(data) {
				
                });
				
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
			
                $( this ).dialog( "close" );
            }
        },
        close: function() {
		
        },
        beforeClose: function() {
				
				
        }
    });


    function validateForm()
    { return true; }




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
// STYLE + FOOTER
$(function(){
	$("input[type=checkbox], input[type=radio], select ").uniform();
	//$("input[type=file]").uniform({"size": "400"});
	$( "#wizard-tabs" ).tabs({ heightStyle: "auto"}).show().tabs("refresh"); //$('#wizard-tabs').tabs("option", "disabled", [1, 2, 3, 4, 5] ) 
	})

</script>


<style>
label {
-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
}

.input-text-label { color: rgb(68,68,68); font-weight: bold; margin: 8px 0px 2px 0px; display:block; }
.input-text { background-color: #FFFFFF; border: 1px solid #C0C0C0; margin: 0; height: 24px; padding: 0 5px; width: 200px;}
.input-textarea { background-color: #FFFFFF; border: 1px solid #C0C0C0; margin: 0; height: 50px; width: 300px; resize:none; padding: 5px 0px 0px 5px; }	


.td-half-left { text-align: left !important; padding:0 16px 0 16px !important; width:50% !important; vertical-align:top !important; border-right: 1px solid #DDDDDD !important; }
.td-half-right { text-align: left !important; padding:0 16px 0 16px !important; width:50% !important; vertical-align:top !important; }
.div-title { color: rgb(68,68,68); width:100%; border-bottom: 1px solid #DDDDDD; font-weight:bold; margin:8px 0 10px 0; padding-bottom:2px; }
.radio-wrapper { float:right; }

.spacer64 { height:64px; }
.tr-next-button { height:30px; }
.table-100 { height:100%; }
.next-button { float:right; margin-right:3px; }

.mono-icon { height: 16px; width: 16px; opacity: 0.75; margin-top:1px; margin-right:3px; float:right; }

.tr-icon { height: 22px; }
.td-icon { width:18px; }
.img-icon { cursor:pointer; margin: 6px 0px 0px 0px; }

.cursor-move { cursor:move; }
.cursor-pointer {cursor:pointer; }

.tright {text-align: right; }
.tr-options {height:24px;}

.height24 {height:24px;}

.text-spinner { text-align: center; color: #444444; }

.hover-callback { cursor:pointer;  }
.hover-ishuman { cursor: pointer; }

/*div.uploader { width: 290px; }
div.uploader input { left:10px; }
div.uploader span.filename { width: 182px; }
div.uploader span.action { cursor:pointer; } */
</style>



<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>