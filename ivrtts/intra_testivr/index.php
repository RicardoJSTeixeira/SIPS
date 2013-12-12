
<? $campaign_id = "testeivr"; ?>

<div class="row-fluid">

    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Text to Convert</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">

            <textarea class="span" id="texto" name="texto" style="resize: none; height: 100px"></textarea>    
            <select id="lang">
                <!-- <option value='de'>Alemão</option>
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
                 <option value='pl'>Polaco</option> -->
                <option value='pt' selected>Português</option>
                <!-- <option value='ro'>Romeno</option>
                <option value='ru'>Russo</option>
                <option value='sv'>Sueco</option>
                <option value='tr'>Turco</option>
                <option value='uk'>Ucraniano</option> -->
            </select>
    <!--        <select id='velocidade'>
                <option value=''>Velocidade</option>
                <option value='0.7'>70%</option>
                <option value='0.8'>80%</option>
                <option value='0.9'>90%</option>
                <option value='1'>100%</option>
                <option value='1.1'>110%</option>
                <option value='1.2'>120%</option>
                <option value='1.3'>130%</option>
                <option value='1.4'>140%</option>
            </select>-->
            <button class="right btn" onclick="ajaxgetsound('simple')" >Listen on browser</button> 

            <button data-target="#basic-modal" data-toggle="modal" class="right btn" style='width:132px'  >Listen on phone</button>

            <div id="basic-modal" class="modal hide">
                <div class="modal-body">
                    <label for='ivrtest-phonetocall'>Phone Number</label>
                    <input id='ivrtest-phonetocall' type="text">   
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" onclick="ajaxgetsound('phone')" data-dismiss="modal">Make Call</a>
                    <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                </div>
            </div> 

            <div class="clear"></div>
        </div>
    </div>

    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Audio Result</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <center>

                <div style="min-height: 138px;">  

                    <div id="gravacao" >
                        <h5>Audio Player</h5>  

                        <audio controls="controls" id="wavplayer" name="wavplayer">
                            <source src="" type="audio/wav" >
                            O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
                        </audio>


                    </div>
            </center>
        </div>
        <div class="clear"></div>
    </div>
</div>

</div>

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
            <!--            <option value='de'>Alemão</option>
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
                        <option value='pl'>Polaco</option>-->
            <option value='pt' selected>Português</option>
            <!--            <option value='ro'>Romeno</option>
                        <option value='ru'>Russo</option>
                        <option value='sv'>Sueco</option>
                        <option value='tr'>Turco</option>
                        <option value='uk'>Ucraniano</option>-->
        </select>
<!--        <select id='velocidade'>
            <option value=''>Velocidade</option>
            <option value='0.7'>70%</option>
            <option value='0.8'>80%</option>
            <option value='0.9'>90%</option>
            <option value='1'>100%</option>
            <option value='1.1'>110%</option>
            <option value='1.2'>120%</option>
            <option value='1.3'>130%</option>
            <option value='1.4'>140%</option>
        </select>-->
        <input type="button" value="Ouvir" onclick="ajaxgetsound('simple')" />&nbsp;&nbsp;<input type="button" value="Ligar para o telefone" onclick="ajaxgetsound('phone')" /><br><br>

        <!-- CONTROLO DE FICHEIRO SELECCIONADO -->
        <table width=100% border=0>
            <tr>
                <td width=70%>
                    <!--   <div id="gravacao" style="display:none">
                           <br><br>
                           <audio controls="controls" id="wavplayer" name="wavplayer">
                               <source src="" type="audio/wav" >
                               O seu browser não suporta esta aplicação. Por favor utilize a versão mais recente do Firefox ou Chrome.
                           </audio>
                           
               
                       </div> -->
                </td>
                <td width=30%>
                    <div >


                        <h4>Formas de controlo:</h4>
                        <br>
                        <h5>Pequena Pausa: "Pequena, Pausa."</h5>
                        <h5>Pausa longa: "Pausa. Longa"</h5>
                        <h5>Números: 707, 202, 303</h5>
                        <h5>Números: 707. 202. 303</h5>
                        <h5>Pronúncia: "Colocár" (Colocar)</h5>
                        <h5>Pronúncia: "Bárquelais" (Barclays)</h5>
                        <h5></h5>
                    </div>
                </td>
            </tr>
        </table>
        <!-- CONTROLO DE FICHEIRO SELECCIONADO -->
    </form>
</div>

<!-- DIV construtor de mensagem -->



<script>
    $(function() {

        $('#fatts').click(function() {
            $("#selOpt").val('1');
            $('#editar-audio').dialog('open');
        })

    });

    var used_files = new Array();
    var texto_ivr;
    var curPath;

    function addtextfield() {

        var curText = $("#texto_dois").val();
        var insertVar = $("#dinfields option:selected").text();

        $("#texto_dois").val(curText + "[" + insertVar + "] ");
        $("#texto_dois").focus();
    }


    function playrecord(curPlay) {
        if (curPlay == 'ff') {

            curPath = $("#audioinicialselect").val();

            $("#fselect").html($("#audioinicialselect option:selected").text());
            var audioplayer = $('#recordplayer');
            audioplayer.empty();
            $("<source>").attr("src", "/ivr/" + curPath + ".wav").appendTo(audioplayer);
            audioplayer[0].load();


        } else if (curPlay == 'sf') {

            curPath = $("#audiofinalselect").val();

            $("#fselect").html($("#audiofinalselect option:selected").text());
            var audioplayer = $('#recordplayer');
            audioplayer.empty();
            $("<source>").attr("src", "/ivr/" + curPath + ".wav").appendTo(audioplayer);
            audioplayer[0].load();


        }

    }

    function process_file(valor) {
        if (valor == 'aceitar') {
            posicao = $("#selOpt").val();
            var lingua = $("#lang").val();
            var desc = $("#desc").val();

            if (desc == null || desc == '') {
                alert('O nome do ficheiro é obrigatório');
                return false;
            } else {
                $.post('../intra_testivr/requests_ivr.php', {ficheiro_processar: texto_ivr, lang: lingua, desc: desc, posicao: posicao, campaign_id: "<?php echo $campaign_id ?>"}, function(data) {
                });
                return true;
            }
        } else if (valor == 'descartar') {
            alert('descartar');
            return true;
        }

    }


    function calculo(curT) {

        if (curT == 'dois') {
            $('#comprimento_dois').html($('#texto_dois').val().length);
        } else {
            $('#comprimento').html($('#texto').val().length);
        }


    }

    function ajaxgetsound(xyz) {

        if (xyz == 'din') {
            var texto = $("#texto_dois").val();
            var lingua = $("#lang_din").val();
            var velocidade = $("#velocidade").val()
            $.post('../dixi/requests.php', {data: texto, lang: lingua, din: "yes", campaign_id: "<? echo $campaign_id; ?>", velocidade: velocidade}, function(data) {


                var retStr = data.split("###");
                var wav_file = retStr[1];
                var curP = retStr[0];

                $("#curPhrase").html(curP);




                used_files[used_files.length] = wav_file;
                texto_ivr = wav_file;
                var audioplayer = $('#wavplayer_din');
                audioplayer.empty();

                $("<source>").attr("src", "/ivr/" + wav_file + ".wav").appendTo(audioplayer);

                audioplayer[0].load();
                $("#gravacao_din").show();

            });
        } else if (xyz == 'phone_din') {
            phone = prompt("Insira o nº de telefone");
            var velocidade = $("#velocidade").val()
            var texto = $("#texto_dois").val();
            var lingua = $("#lang_din").val();

            $.post('../intra_testivr/requests_ivr.php', {gmd: "yes", data: texto, lang: lingua, phone: phone, din: "yes", campaign_id: "<? echo $campaign_id; ?>", velocidade: velocidade}, function(data) {


            });
        }

        if (xyz == 'phone') {
            //    phone = prompt("Insira o nº de telefone"); 
            var phone = $("#ivrtest-phonetocall").val();
            var velocidade = $("#velocidade").val()
            var texto = $("#texto").val();
            var lingua = $("#lang").val();

            $.post('../dixi/requests.php', {action: 'browser-phone', data: texto, lang: lingua, phone: phone, campaign_id: "<? echo $campaign_id; ?>", velocidade: velocidade}, function(data) {


            });

        } else if (xyz == 'simple') {
            var velocidade = $("#velocidade").val()
            var texto = $("#texto").val();
            var lingua = $("#lang").val();

            $.post('../dixi/requests.php', {data: texto, lang: lingua, velocidade: velocidade, action: 'browser-listen', user: "web"}, function(data) {
                $('.result').html(data);

                used_files[used_files.length] = data;
                texto_ivr = data;
                //alert(texto_ivr);
                var audioplayer = $('#wavplayer');
                audioplayer.empty();

                $("<source>").attr("src", "../dixi/files/" + data + ".mp3").appendTo(audioplayer);

                audioplayer[0].load();
                $("#gravacao").show();

            });
        }
    }

    function delete_unused_files() {
        $.post('../intra_testivr/requests_ivr.php', {del_files: used_files});
    }

    $("#editar-audio").dialog({
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        resizable: false,
        beforeClose: function() {


        }
    });

    $("#construir-mensagem").dialog({
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
                $.post('../intra_testivr/requests_ivr.php', {gmd: "yes", md: texto, ficheiro: ficheiro, campaign_id: "<? echo $campaign_id; ?>"}, function(data) {

                });
                $(this).dialog("close");
            },
            "Cancelar": function() {

                $(this).dialog("close");
            }
        },
        open: function() {

            $.post("../intra_testivr/requests_ivr.php", {pop_din_fields: "yes", campaign_id: "<? echo $campaign_id; ?>"}, function(list) {

                $("#dinfields").html(list);

            });
        },
        close: function() {
            //  $("#form")[0].reset();

            delete_unused_files();
        },
        beforeClose: function() {


        }
    });

    $("#serverselect").dialog({
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        resizable: false,
        open: function() {

            $.post("../intra_testivr/requests_ivr.php", {recordSelector: 'yes', campaign_id: "<? echo $campaign_id; ?>"}, function(data) {

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
                $.post("../intra_testivr/requests_ivr.php", {aceitarficheiro: 'yes', campaign_id: "<? echo $campaign_id; ?>", ficheiro: ficheiro, posicao: posicao, fname: fname}, function(data) {

                });

                $(this).dialog("close");
            },
            "Cancelar": function() {

                $(this).dialog("close");
            }
        },
        close: function() {

        },
        beforeClose: function() {


        }
    });


    function validateForm()
    {
        return true;
    }

</script>    


