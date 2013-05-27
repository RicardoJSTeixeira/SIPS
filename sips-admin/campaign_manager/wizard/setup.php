<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>


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

        <!-- CONTROLO DE FICHEIRO SELECCIONADO -->

        <div id="gravacao" style="display:none">
            <br><br>
            <audio controls="controls" id="wavplayer" name="wavplayer">
                <source src="" type="audio/wav" >
                O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
            </audio>
            <br><br>
            <label for="descricao">Introduza um nome para este ficheiro:</label>
            <input type="text" id="desc" size="20" />
            <br>

        </div>

        <!-- CONTROLO DE FICHEIRO SELECCIONADO -->
    </form>
</div>

<!-- DIV construtor de mensagem -->

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
    <!-- CONTROLO DE FICHEIRO SELECCIONADO -->

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

<!-- DIV construtor de mensagem -->



<!-- DIV selector do servidor -->

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

<!-- DIV selector do servidor -->



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


// IVR OUT
<script>
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
    
</script>    


<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>