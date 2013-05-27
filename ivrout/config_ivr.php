<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>

<!--Dialog Audio Inicial-->
<div id="editar-audio">
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
    <input type="button" value="Ouvir" onclick="ajaxgetsound()" />&nbsp;&nbsp;<input type="button" value="Ligar para o telefone" onclick="ajaxgetsound('phone')" /><br><br>

    <!-- CONTROLO DE FICHEIRO SELECCIONADO -->

    <div id="gravacao" style="display:none">
        <br><br>
        <audio controls="controls" id="wavplayer" name="wavplayer">
            <source src="" type="audio/wav" >
            O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
        </audio>
        <br><br>
        <label for="descricao">Introduza uma descrição para esta gravação:</label>
        <input type="text" id="desc" size="100" />
        <br>
        <input type="button" value="Aceitar" onclick="process_file('aceitar');" />
        <input type="button" value="Descartar" onclick="process_file('descartar');" />
    </div>

    <!-- CONTROLO DE FICHEIRO SELECCIONADO -->

</div>
<!--Fim Dialog audio Inicial-->

<div id="maindiv">

    <label for="mensageminicial">Áudio Inicial:</label>
    <input type="text" id="mensageminicial" readonly />
    <br><br>
    <label for="press1">Mensagem Personalizada</label>
    <input type="text" id="mensagempersonalizada" readonly />
    <br><br>
    <label for="press2">Áudio Final</label>
    <input type="text" id="mensagemfinal" readonly />
    <br><br>
    <label for="press3">Acção Pressionar 1</label>
    <input type="text" id="press1" value="Repetir novamente" readonly />
    <br><br>
    <label for="press4">Acção Pressionar 2</label>
    <input type="text" id="press2" value="Transfere para Call-Center" readonly />

</div>


<script>
    $(function(){
        $('#mensageminicial').click(function(){
            $("#selOpt").val('1');
            $('#editar-audio').dialog('open');
        })
        $('#mensagemfinal').click(function(){
            $("#selOpt").val('2');
            $('#editar-audio').dialog('open');
        })
	});
   
    var used_files = new Array();
	var texto_ivr;	

	function process_file(valor) {
        if (valor=='aceitar') { 
            posicao = $("#selOpt").val();
            var lingua = $("#lang").val();
            var desc = $("#desc").val();
            $.post('requests_ivr.php',{ficheiro_processar:texto_ivr,lang:lingua,desc:desc,posicao:posicao,campanha:"<?php echo $campanha ?>"}, function(data) {
            });
        } else if(valor=='descartar'){ 
            alert('descartar'); 
        } else {
            
        }	
	
    }


    function calculo() {
        $('#comprimento').html($('#texto').val().length);
    }
	
    function ajaxgetsound(xyz) {
		
		if(xyz=='phone') {
			phone = prompt("Insira o nº de telefone"); 
			
			var texto = $("#texto").val();
       	 var lingua = $("#lang").val();
			
			$.post('requests_ivr.php',{data:texto,lang:lingua,phone:phone}, function(data) {
           
			
        	});
			
			} else {
		
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
        height: 600,
        width: 800,
        modal: true,
        resizable: false,
        buttons: {
            "Aceitar Alterações": function() {
                delete_unused_files();
                $( this ).dialog( "close" );
            },
            "Cancelar" : function() {
                delete_unused_files();
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            delete_unused_files();
        },
        beforeClose: function() {
				
				
        }
    });


</script>


<?php
#FOOTER
$footer = ROOT . "ini/footer.php";
require($footer);
?>