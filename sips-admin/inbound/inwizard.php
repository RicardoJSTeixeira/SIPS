<?php
#HEADER
ob_gzhandler();
ini_set("display_errors", "1");


$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");
?>


<style>
    /*@import url("/css/bootstrap.min.css");*/
    @import url("/css/custom.css");
    @import url("/css/jquery.jOrgChart.css"); 
    /*@import url("/css/prettify.css"); */
    @import url("/jquery/jqueryFileTree/jqueryFileTree.css"); 
    .btop {
        border-top: 1px solid #C0C0C0;
        margin-top: 0.5em;
        padding: 0.5em 0 0;
    }
    .a {
        cursor: pointer;
        display: block;
    }
    .dialog label{margin-right: 1em;}
    .dialog label:not(.radio) { display:block; }
    .dialog input {display: inline-block;}
    .dialog label.radio {margin-bottom:12px;}
    .dialog input:not([type=radio]):not(.ui-spinner-input),.dialog select,.ui-spinner {margin-bottom:12px;}
    .dialog input.text { width:90%; padding: .4em; }
    .dialog h1 { font-size: 1.2em; margin: .6em 0; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .fli{position:absolute;float:right;cursor:pointer;margin: 0.5em;}
    fieldset{margin-bottom:18px;}fieldset legend{display:block;font-size:19.5px;line-height:1;color:#404040;padding:0 0 5px 145px;line-height:1.5;}
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .hc > a {float:right;margin-top: 0.4em}
    .luz {border-radius:2px;margin: -0.3em -0.5em 0;padding: 0.3em 0.5em 0;}
</style>
<script src="/jquery/jqueryFileTree/jqueryFileTree.js"></script>
<script src="jquery.jOrgChart.js"></script>
<script>
    $(function(){
        $(":button").button();
        $( document ).tooltip({track: true ,show:{delay:1000}});
        
        $("#sub-ddi-b").button({icons: {primary: "ui-icon-circle-plus"}});
        
        $(".luz").hover(function(){$(this).animate({backgroundColor: "#F0F0F0"},{ queue:false, duration:500 });}, function(){$(this).animate({backgroundColor: "#FFF"},{ queue:false, duration:500 });});
    });  
</script>

<!--Começa Cria DDI-->
<div id="first-step" style="position:absolute;top:20px;width:100%;z-index: 100;background-color: white;" >
    <div class=cc-mstyle>
        <table>
            <tr>
                <td id='icon32'>
                    <img src='/images/icons/table_add.png' />
                </td>
                <td id='submenu-title'>
                    Criar DDI
                </td>
                <td style='text-align:left'>

                </td>
            </tr>
        </table>
    </div>
    <div id=work-area>
        <div class=cc-mstyle style='border:none; width:80%; margin-top: 5em;'>
            <form  id="sub-ddi">
                <table>
                    <tr>
                        <td style='width:225px'>
                            <div class=cc-mstyle style='height:28px;  '>
                                <p> 
                                    Extensão DDI 
                                </p>
                            </div>
                        </td>
                        <td>
                            <input type=text id=did_pattern size=20 maxlength=50 required>
                        </td> 
                    </tr>
                    <tr>
                        <td style='width:225px'>
                            <div class=cc-mstyle style='height:28px;  '>
                                <p>
                                    Descrição da DDI
                                </p>
                            </div>
                        </td>
                        <td>
                            <input type=text id=did_description size=40 maxlength=50 required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 >
                        <td>
                            <button style='float:right' id="sub-ddi-b">Criar</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<!--Acaba Cria DDI-->
<!--Começa dialog de Edição de DDI-->
<div id="ddi-setup-edit" title="Edição de DDI"  style="display:none" class="dialog">
    <form> 
        <fieldset>
            <label>Extenção</label>
            <input type="text" id="" name="" class="text ui-widget-content ui-corner-all"  />

            <label>Descrição</label>
            <input type="text" id="" name="" class="text ui-widget-content ui-corner-all"  />

            <label>Activa</label>
            <label class="radio">
                <input class="radio" type="radio" name="at" id="at1" value="Y" checked>
                Sim
            </label>
            <label class="radio">
                <input class="radio" type="radio" name="at" id="at0" value="N">
                Não
            </label>

            <label>Grupo de Admins</label>
            <select id="" class="text ui-widget-content ui-corner-all">
                <option value="---ALL---">Todos</option>
                <?php
                $query = "SELECT `user_group`,`group_name` FROM `vicidial_user_groups`";
                $rlt = mysql_query($query, $link);
                while ($row = mysql_fetch_row($rlt)) {
                    ?>
                    <option value="<?php echo $row[0] ?>"><?php echo $row[1]; ?></option>
                <?php }
                ?>
            </select>

            <label>Gravação de Chamada</label>
            <label class="radio">
                <input class="radio" type="radio" name="rc" id="rc1" value="Y" checked>
                Sim
            </label>
            <label class="radio">
                <input class="radio" type="radio" name="rc" id="rc0" value="N">
                Não
            </label>
            <label class="radio" class="radio" title="Sim mas para numa fila de Grupo Inbound.">
                <input class="radio" type="radio" name="rc" id="rc2" value="Y_QUEUESTOP">
                Sim mas para numa fila
            </label>


        </fieldset>
    </form>
</div>
<!--Acaba dialog de edição de DDI-->
<!--Começa listagem dummy do diagrama-->
<ul id="org" style="display:none">
    <li class="ddi">
        <span class="title" id="ddi-title"></span>

        <span class='tools'>
            <img src='/images/icons/livejournal_16.png' alt='Editar Elemento' class="ddi-edit" title='Editar opções da DDI' />
            <img src='/images/icons/sound_add_16.png' alt='Adicionar Opção' class='addoption add_child' ext="ddi" title='Adicionar ligação à DDI' id="ddi-add" />
        </span>

        <span class="icon">
            <img src="/images/icons/arrow_down_32.png" alt="DDI" title="DDI" />
        </span>
        <ul id="ddi"></ul>
    </li>
</ul>
<!--Acaba listagem dummy do diagrama-->
<!--Começa diagrama-->
<div id="chart" class="orgChart" align="center"></div>
<!--Acaba diagrama-->
<!--Começa file tree view-->
<div id="tree" title="Seleccione ficheiro a utilizar." style="overflow-y:scroll;height:300px;width:200px;display:none"></div>
<!--Acaba file tree view-->
<!--Começa dialog de Edição de IVR-->
<div id="ivr-setup-edit" title="Edição de IVR"  style="display:none" class="dialog">
    <form> 
        <fieldset>
            <label>Nome do IVR</label>
            <input type="text" id="ivr-setup-edit-name" name="ivrname" class="text ui-widget-content ui-corner-all"  />
            <label>Áudio de Entrada</label>
            <span>
                <input type="text" id="ivr-setup-edit-welcome-audio" name="ivr-setup-edit-welcome-audio"  class="text ui-widget-content ui-corner-all" />
                <img src="/images/icons/comment_add_16.png" onClick="$('#selector-audio').dialog('open')" class="fli" />
            </span>
            <label><em>Timeout</em> para selecção</label>
            <input type="text" id="ivr-setup-edit-timeout" name="ivr-setup-edit-timeout"  class="text ui-widget-content ui-corner-all" />
            <label>Áudio de <em>Timeout</em></label>
            <span>
                <input type="text" id="ivr-setup-edit-timeout-audio" name="ivr-setup-edit-timeout-audio"  class="text ui-widget-content ui-corner-all" />
                <img src="/images/icons/comment_add_16.png" onClick="$('#selector-audio').dialog('open')" class="fli" />
            </span>
            <label>Áudio de Opção Inválida</label>
            <span>
                <input type="text" id="ivr-setup-edit-invalid-audio" name="ivr-setup-edit-invalid-audio"  class="text ui-widget-content ui-corner-all" />
                <img src="/images/icons/comment_add_16.png" onClick="$('#selector-audio').dialog('open')" class="fli" />
            </span>
            <label>Repetir IVR (segundos)</label>
            <input type="text" id="ivr-setup-edit-ivr-repeat" name="ivr-setup-edit-ivr-repeat"  class="text ui-widget-content ui-corner-all" />
            <label>Mostrar IVR no <em>Real-Time Report</em></label>
            <label class="radio">
                <input type="radio" name="ivr-setup-edit-rtrtrack" id="ivr-setup-edit-rtrtrack1" value="1" checked>
                Sim
            </label>
            <label class="radio">
                <input type="radio" name="ivr-setup-edit-rtrtrack" id="ivr-setup-edit-rtrtrack0" value="0">
                Não
            </label>
        </fieldset>
    </form>
</div>
<!--Acaba dialog de edição de IVR-->
<!--Começa dialog TTS-->
<div id="ivroptions" style="display:none">
    <fieldset>
        <legend><h5>Criar Áudio através de TTS (Google<small>&copy;</small> 2012)</h5></legend>
        <label>Label name</label>
        <textarea id="tts" name="tts" style="width:355px;height:55px;resize:none;" placeholder="Insira texto a traduzir para áudio..." /></textarea>
        <input type="button" value="Converter" size="16" />
    </fieldset>
</div>  
<!--Acaba dialog TTS-->
<!--Começa dialog fonte audio-->
<div id="selector-audio" title="Seleccionar fonte de Áudio" style="vertical-align:central;display:none">
    <form>
        <fieldset>
            <div>
                <label>Upload de Áudio</label>
                <input type="file" id="upfrompc" name="upfrompc" class="text ui-widget-content ui-corner-all"  />
                <input type="button" value="OK" size="16" />
            </div>
            <div class="btop">
                <label>Utilizar Existente</label>
                <input type="button" id="selectfromserver" name="selectfromserver" onClick="$('#tree').dialog('open')" value="Seleccionar do Servidor" class="text ui-widget-content ui-corner-all" />
            </div>
            <div class="btop">
                <h5>Criar Áudio através de TTS (Google<small>&copy;</small> 2012)</h5>
                <textarea id="tts" name="tts" style="width:355px;height:55px;resize:none;"  placeholder="Insira texto a traduzir para áudio..." class="text ui-widget-content ui-corner-all" /></textarea>
                <input type="button" value="Converter" size="16" />
            </div>
        </fieldset>
    </form>
</div> 
<!--Acaba dialog fonte audio-->
<!--Começa dialog destino DDI-->
<div id="line-chooser" title="Escolha" style="display:none;">
    <form>
        <fieldset>
            <label>Seleccione o caminho.</label>
            <select id="element_dest" class="text ui-widget-content ui-corner-all">
                <option value="AGENT">Agente</option>
                <option value="VM">VoiceMail</option>
                <option value="PHONE">Telefone</option>
                <option value="INB">Grupo Inbound</option>
                <option value="IVR">IVR</option>
            </select>
        </fieldset>
    </form>
</div>
<!--Acaba dialog destino DDI-->
<!--Começa dialog Primeira config de INBG-->
<div id="inbg-setup" title="Criar Groupo Inbound" style="display:none" class="dialog">
    <p class="validateTips">Todos os campos são obrigatórios.</p>
    <form>
        <fieldset>
            <label>Nome</label>
            <input type="text" id="inbg-setup-name" name="inbg-setup-name"  class="text ui-widget-content ui-corner-all" required/>
            <label>Cor</label>
            <span class="hc">
                <select id="inbg-setup-color" class="text ui-widget-content ui-corner-all">
                    <option value="ffffff">#ffffff</option>
                    <option value="ffccc9">#ffccc9</option>
                    <option value="ffce93">#ffce93</option>
                    <option value="fffc9e">#fffc9e</option>
                    <option value="ffffc7">#ffffc7</option>
                    <option value="9aff99">#9aff99</option>
                    <option value="96fffb">#96fffb</option>
                    <option value="cdffff">#cdffff</option>
                    <option value="cbcefb">#cbcefb</option>
                    <option value="cfcfcf">#cfcfcf</option>
                    <option value="fd6864">#fd6864</option>
                    <option value="fe996b">#fe996b</option>
                    <option value="fffe65">#fffe65</option>
                    <option value="fcff2f">#fcff2f</option>
                    <option value="67fd9a">#67fd9a</option>
                    <option value="38fff8">#38fff8</option>
                    <option value="68fdff">#68fdff</option>
                    <option value="9698ed">#9698ed</option>
                    <option value="c0c0c0" selected="selected">#c0c0c0</option>
                    <option value="fe0000">#fe0000</option>
                    <option value="f8a102">#f8a102</option>
                    <option value="ffcc67">#ffcc67</option>
                    <option value="f8ff00">#f8ff00</option>
                    <option value="34ff34">#34ff34</option>
                    <option value="68cbd0">#68cbd0</option>
                    <option value="34cdf9">#34cdf9</option>
                    <option value="6665cd">#6665cd</option>
                    <option value="9b9b9b">#9b9b9b</option>
                    <option value="cb0000">#cb0000</option>
                    <option value="f56b00">#f56b00</option>
                    <option value="ffcb2f">#ffcb2f</option>
                    <option value="ffc702">#ffc702</option>
                    <option value="32cb00">#32cb00</option>
                    <option value="00d2cb">#00d2cb</option>
                    <option value="3166ff">#3166ff</option>
                    <option value="6434fc">#6434fc</option>
                    <option value="656565">#656565</option>
                    <option value="9a0000">#9a0000</option>
                    <option value="ce6301">#ce6301</option>
                    <option value="cd9934">#cd9934</option>
                    <option value="999903">#999903</option>
                    <option value="009901">#009901</option>
                    <option value="329a9d">#329a9d</option>
                    <option value="3531ff">#3531ff</option>
                    <option value="6200c9">#6200c9</option>
                    <option value="343434">#343434</option>
                    <option value="680100">#680100</option>
                    <option value="963400">#963400</option>
                    <option value="986536">#986536</option>
                    <option value="646809">#646809</option>
                    <option value="036400">#036400</option>
                    <option value="34696d">#34696d</option>
                    <option value="00009b">#00009b</option>
                    <option value="303498">#303498</option>
                    <option value="000000">#000000</option>
                    <option value="330001">#330001</option>
                    <option value="643403">#643403</option>
                    <option value="663234">#663234</option>
                    <option value="343300">#343300</option>
                    <option value="013300">#013300</option>
                    <option value="003532">#003532</option>
                    <option value="010066">#010066</option>
                    <option value="340096">#340096</option>
                </select>
            </span>
        </fieldset>
    </form>
</div>  
<!--Acaba dialog Primeira config de INBG-->
<!--Começa dialog de Edição de INBG-->
<div id="inbg-setup-edit" title="Edição de Grupo Inboud"  style="display:none" class="dialog">
    <form> 
        <fieldset>
            <label>Nome</label>
            <input type="text" id="inbg-setup-edit-name" name="inbg-setup-name"  class="text ui-widget-content ui-corner-all" required/>

            <label>Cor</label>
            <span class="hc">
                <select id="inbg-setup-edit-color" class="text ui-widget-content ui-corner-all">
                    <option value="ffffff">#ffffff</option>
                    <option value="ffccc9">#ffccc9</option>
                    <option value="ffce93">#ffce93</option>
                    <option value="fffc9e">#fffc9e</option>
                    <option value="ffffc7">#ffffc7</option>
                    <option value="9aff99">#9aff99</option>
                    <option value="96fffb">#96fffb</option>
                    <option value="cdffff">#cdffff</option>
                    <option value="cbcefb">#cbcefb</option>
                    <option value="cfcfcf">#cfcfcf</option>
                    <option value="fd6864">#fd6864</option>
                    <option value="fe996b">#fe996b</option>
                    <option value="fffe65">#fffe65</option>
                    <option value="fcff2f">#fcff2f</option>
                    <option value="67fd9a">#67fd9a</option>
                    <option value="38fff8">#38fff8</option>
                    <option value="68fdff">#68fdff</option>
                    <option value="9698ed">#9698ed</option>
                    <option value="c0c0c0" selected="selected">#c0c0c0</option>
                    <option value="fe0000">#fe0000</option>
                    <option value="f8a102">#f8a102</option>
                    <option value="ffcc67">#ffcc67</option>
                    <option value="f8ff00">#f8ff00</option>
                    <option value="34ff34">#34ff34</option>
                    <option value="68cbd0">#68cbd0</option>
                    <option value="34cdf9">#34cdf9</option>
                    <option value="6665cd">#6665cd</option>
                    <option value="9b9b9b">#9b9b9b</option>
                    <option value="cb0000">#cb0000</option>
                    <option value="f56b00">#f56b00</option>
                    <option value="ffcb2f">#ffcb2f</option>
                    <option value="ffc702">#ffc702</option>
                    <option value="32cb00">#32cb00</option>
                    <option value="00d2cb">#00d2cb</option>
                    <option value="3166ff">#3166ff</option>
                    <option value="6434fc">#6434fc</option>
                    <option value="656565">#656565</option>
                    <option value="9a0000">#9a0000</option>
                    <option value="ce6301">#ce6301</option>
                    <option value="cd9934">#cd9934</option>
                    <option value="999903">#999903</option>
                    <option value="009901">#009901</option>
                    <option value="329a9d">#329a9d</option>
                    <option value="3531ff">#3531ff</option>
                    <option value="6200c9">#6200c9</option>
                    <option value="343434">#343434</option>
                    <option value="680100">#680100</option>
                    <option value="963400">#963400</option>
                    <option value="986536">#986536</option>
                    <option value="646809">#646809</option>
                    <option value="036400">#036400</option>
                    <option value="34696d">#34696d</option>
                    <option value="00009b">#00009b</option>
                    <option value="303498">#303498</option>
                    <option value="000000">#000000</option>
                    <option value="330001">#330001</option>
                    <option value="643403">#643403</option>
                    <option value="663234">#663234</option>
                    <option value="343300">#343300</option>
                    <option value="013300">#013300</option>
                    <option value="003532">#003532</option>
                    <option value="010066">#010066</option>
                    <option value="340096">#340096</option>
                </select>
            </span>

            <label>Activa</label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-edit-active" id="inbg-setup-edit-active1" value="Y" checked>
                Sim
            </label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-edit-active" id="inbg-setup-edit-active0" value="N">
                Não
            </label>

            <label>Grupo de Admins</label>
            <select id="inbg-setup-edit-admins" name="inbg-setup-edit-admins" class="text ui-widget-content ui-corner-all">
                <option value="---ALL---">Todos</option>
                <?php
                $query = "SELECT `user_group`,`group_name` FROM `vicidial_user_groups`";
                $rlt = mysql_query($query, $link);
                while ($row = mysql_fetch_row($rlt)) {
                    ?>
                    <option value="<?php echo $row[0] ?>"><?php echo $row[1]; ?></option>
                <?php }
                ?>
            </select>

            <label>Audio de Saudação</label>
            <span>
                <input type="text" id="inbg-setup-edit-welcome-message-filename" name="inbg-setup-edit-welcome-message-filename" class="text ui-widget-content ui-corner-all"  />
                <img src="/images/icons/comment_add_16.png" class="fli as" />
            </span>

            <label>Quando Toca a Saudação</label>
            <select id="inbg-setup-edit-play-welcome-message" name="inbg-setup-edit-play-welcome-message" style="width:15em;" class="text ui-widget-content ui-corner-all">
                <option value="ALWAYS">Sempre</option>
                <option value="NEVER">Nunca</option>
                <option value="IF_WAIT_ONLY">Se o Cliente ficar em fila de espera</option>
                <option value="YES_UNLESS_NODELAY">Só não tocada se a opção "sem espera" estiver activa</option>
            </select>

            <label>Mensagem de espera</label>
            <span>
                <input type="text" id="inbg-setup-edit-on-hold-prompt-filename" name="inbg-setup-edit-on-hold-prompt-filename" class="text ui-widget-content ui-corner-all"  />
                <img src="/images/icons/comment_add_16.png" class="fli as" />
            </span>
            <label>Intervalo da mensagem de espera</label>
            <input type="text" id="inbg-setup-edit-on-hold-prompt-interval" name="inbg-setup-edit-on-hold-prompt-interval" value="0" title="0 é inactivo" /><span style="margin-left:0.5em">Segundos</span>

            <label>Deixar o cliente passar à frente se outro estiver a ouvir a mensagem.</label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-on-hold-prompt-no-block" id="inbg-setup-on-hold-prompt-no-block1" value="Y" checked>
                Sim
            </label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-on-hold-prompt-no-block" id="inbg-setup-on-hold-prompt-no-block0" value="N">
                Não
            </label>

            <label>Atribuição da chamada</label>
            <select id="inbg-setup-edit-next-agent-call" name="inbg-setup-edit-next-agent-call" style="width:15em;" class="text ui-widget-content ui-corner-all">
                <option value="---ALL---">Todos</option>
                <option value="random">Aleatório</option>
                <option value="oldest_call_start">Recebeu chamadas à mais tempo</option>
                <option value="oldest_call_finish">Está mais tempo à espera</option>
                <option value="oldest_inbound_call_start">Recebeu chamadas inbound à mais tempo</option>
                <option value="oldest_inbound_call_finish">Está mais tempo à esperade uma chamada inbound</option>
                <option value="overall_user_level">Tem o nivel superior</option>
                <option value="inbound_group_rank">Tem o rank superior</option>
                <option value="campaign_rank">Tem o rank superior na Campanha</option>
                <option value="ingroup_grade_random">Aleatório com mais possibilidades para um agente com nivel superior(ou rank???)</option>
                <option value="campaign_grade_random">Aleatório com mais possibilidades para um agente com nivel superior(ou rank???) na campanha</option>
                <option value="fewest_calls">O agente que recebeu menos chamadas no grupo</option>
                <option value="fewest_calls_campaign">O agente que recebeu menos chamadas na campanha</option>
                <option value="longest_wait_time">O agente que está à mais tempo à espera</option>
                <option value="ring_all">Toca em todos os disponiveis, e fica com a chamada o primeiro que atender.</option>
            </select>

            <label>Prioridade de Fila</label>
            <input id="inbg-setup-edit-queue-ranking" name="inbg-setup-edit-queue-ranking" value="0"  title="Prioridade de Fila entre Grupos de Inbound"/>

            <label>Script</label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-edit-get-call-launch" id="inbg-setup-edit-get-call-launch1" value="FORM" checked>
                Sim
            </label>
            <label class="radio">
                <input class="radio" type="radio" name="inbg-setup-edit-get-call-launch" id="inbg-setup-edit-get-call-launch0" value="NONE">
                Não
            </label>

            <div id="inbg-setup-edit-drop-call" class="luz">
                <label>Chamada perdida</label>
                <input id="inbg-setup-edit-drop-call-seconds" name="inbg-setup-edit-drop-call-seconds" style="display:inline-block" value="360" title="Tempo que demora a considerar uma chamada que ainda não foi atentida como perdida."/><span style="margin-left: 0.5em;">Segundos</span>

                <label>Acção de Chamada Perdida</label>
                <select id="inbg-setup-edit-drop-action" name="inbg-setup-edit-drop-action" class="text ui-widget-content ui-corner-all">
                    <option value="HANGUP">Desligar</option>
                    <option value="MESSAGE">Passar Mensagem</option>
                    <option value="VOICEMAIL">Voice Mail</option>
                    <option value="IN_GROUP">Grupo Inbound</option>
                    <option value="CALLMENU">IVR</option>
                </select>
            </div>
            <!--drop call action config-->

            <label> Horário de chamada </label>
            <select id="inbg-setup-edit-call-time" name="inbg-setup-edit-call-time" class="text ui-widget-content ui-corner-all">
                <?php
                $query = "SELECT `call_time_id`,`call_time_name` FROM `vicidial_call_times`";
                $rlt = mysql_query($query, $link);
                while ($row = mysql_fetch_row($rlt)) {
                    ?>
                    <option value="<?php echo $row[0] ?>"><?php echo $row[1]; ?></option>
                <?php }
                ?>
            </select>

            <div id="inbg-setup-edit-after-hours" class="luz">
                <label>Acção Fora de Horas</label>
                <select id="inbg-setup-edit-after-hours-action" name="inbg-setup-edit-after-hours-action" class="text ui-widget-content ui-corner-all">
                    <option value="HANGUP">Desligar</option>
                    <option value="MESSAGE">Passar Mensagem</option>
                    <option value="VOICEMAIL">Voice Mail</option>
                    <option value="IN_GROUP">Grupo Inbound</option>
                    <option value="CALLMENU">IVR</option>
                </select>
            </div>
            <!--after hours action config-->
            <div id="inbg-setup-edit-no-agent-no-queue-group" class="luz">
                <label>Sem Fila de espera</label>
                <label class="radio">
                    <input class="radio" type="radio" name="inbg-setup-edit-no-agent-no-queue" id="inbg-setup-edit-no-agent-no-queue1" value="Y" checked>
                    Sim
                </label>
                <label class="radio">
                    <input class="radio" type="radio" name="inbg-setup-edit-no-agent-no-queue" id="inbg-setup-edit-no-agent-no-queue0" value="N">
                    Não
                </label>
                <label class="radio" class="radio">
                    <input class="radio" type="radio" name="inbg-setup-edit-no-agent-no-queue" id="inbg-setup-edit-no-agent-no-queue2" value="NO_PAUSED">
                    Sim mesmo se houver agentes ligados
                </label>

                <label>Acção sem fila de espera</label>
                <select id="inbg-setup-edit-no-agent-no-queue-action" name="inbg-setup-edit-no-agent-no-queue-action" class="text ui-widget-content ui-corner-all">
                    <option value="HANGUP">Desligar</option>
                    <option value="MESSAGE">Passar Mensagem</option>
                    <option value="VOICEMAIL">Voice Mail</option>
                    <option value="IN_GROUP">Grupo Inbound</option>
                    <option value="CALLMENU">IVR</option>
                </select>

                <label>Audio da Mensagem</label>
                <span>
                    <input type="text" id="inbg-setup-edit-no-agent-no-queue-audio-file" name="inbg-setup-edit-no-agent-no-queue-audio-file" class="text ui-widget-content ui-corner-all"  />
                    <img src="/images/icons/comment_add_16.png" class="fli as" />
                </span>
                <!--no agent no queue action config-->
            </div>
            <div id="inbg-setup-edit-max-calls" class="luz">
                <label>Metodo de Maximo de chamadas</label>
                <select id="inbg-setup-edit-max-calls-method" name="inbg-setup-edit-max-calls-method" class="text ui-widget-content ui-corner-all">
                    <option value="TOTAL">Total</option>
                    <option value="IN_QUEUE">Em Espera</option>
                    <option value="DISABLED">Inactivo</option>
                </select>

                <label>Maximo de Chamadas</label>
                <input type="text" id="inbg-setup-edit-max-calls-count" name="inbg-setup-edit-max-calls-count" value="0" title="Tem de ser superior a 0 para ser usado."  />

                <label>Acção de Maximo de Chamadas</label>
                <select id="inbg-setup-edit-max-calls-action" name="inbg-setup-edit-max-calls-action" class="text ui-widget-content ui-corner-all">
                    <option value="DROP">Desliga</option>
                    <option value="AFTERHOURS">Fora de Horas</option>
                    <option value="NO_AGENT_NO_QUEUE">Sem Fila de espera</option>
                </select>
            </div>
            <div id="inbg-setup-edit-wait-time" class="luz">
                <label>Tempo de espera para poder sair</label>
                <input type="text" id="inbg-setup-edit-wait-time-option-seconds" name="inbg-setup-edit-wait-time-option-seconds" value="120" title="Tem de ser superior a 0 para ser usado."  /><span style="margin-left:0.5em"> Segundos</span>

                <label>Audio quando o cliente escolhe uma opção</label>
                <span>
                    <input type="text" id="inbg-setup-edit-wait-time-option-callback-press-filename" name="inbg-setup-edit-wait-time-option-callback-press-filename"  class="text ui-widget-content ui-corner-all"/>
                    <img src="/images/icons/comment_add_16.png" class="fli as" />
                </span>

                <label>Audio quando depois do cliente escolher uma opção</label>
                <span>
                    <input type="text" id="inbg-setup-edit-wait-time-option-callback-after-press-filename" name="inbg-setup-edit-wait-time-option-callback-after-press-filename"  class="text ui-widget-content ui-corner-all"/>
                    <img src="/images/icons/comment_add_16.png" class="fli as" />
                </span>                

                <label>Opção pressionado 1 de espera</label>
                <select id="inbg-setup-edit-wait-time-option" name="inbg-setup-edit-wait-time-option" class="text ui-widget-content ui-corner-all">
                    <option value="NONE">Inactivo</option>
                    <option value="PRESS_STAY">Ficar</option>
                    <option value="PRESS_VMAIL">Voice Mail</option>
                    <option value="PRESS_CALLMENU">IVR</option>
                    <option value="PRESS_CID_CALLBACK">Criar CallBack</option>
                    <option value="PRESS_INGROUP">Grupo Inbound</option>
                </select>

                <label>Opção pressionado 2 de espera</label>
                <select id="inbg-setup-edit-wait-time-option-2" name="inbg-setup-edit-wait-time-option-2" class="text ui-widget-content ui-corner-all">
                    <option value="NONE">Inactivo</option>
                    <option value="PRESS_STAY">Ficar</option>
                    <option value="PRESS_VMAIL">Voice Mail</option>
                    <option value="PRESS_CALLMENU">IVR</option>
                    <option value="PRESS_CID_CALLBACK">Criar CallBack</option>
                    <option value="PRESS_INGROUP">Grupo Inbound</option>
                </select>

                <label>Opção pressionado 3 de espera</label>
                <select id="inbg-setup-edit-wait-time-option-3" name="inbg-setup-edit-wait-time-option-3" class="text ui-widget-content ui-corner-all">
                    <option value="NONE">Inactivo</option>
                    <option value="PRESS_STAY">Ficar</option>
                    <option value="PRESS_VMAIL">Voice Mail</option>
                    <option value="PRESS_CALLMENU">IVR</option>
                    <option value="PRESS_CID_CALLBACK">Criar CallBack</option>
                    <option value="PRESS_INGROUP">Grupo Inbound</option>
                </select>

                <label>Deixar o cliente passar à frente se outro estiver a ouvir a mensagem</label>
                <label class="radio">
                    <input class="radio" type="radio" name="inbg-setup-edit-wait-time-no-block" id="inbg-setup-edit-wait-time-no-block1" value="Y" checked>
                    Sim
                </label>
                <label class="radio">
                    <input class="radio" type="radio" name="inbg-setup-edit-wait-time-no-block" id="inbg-setup-edit-wait-time-no-block0" value="N">
                    Não
                </label>

                <label>ID da lista onde fica guardado o callback</label>
                <input type="text" id="inbg-setup-edit-wait-time-option-callback-list-id" name="inbg-setup-edit-wait-time-option-callback-list-id" value="999" class="text ui-widget-content ui-corner-all" style="width: 3em;"/>
            </div>
            <div id="inbg-setup-edit-agent-alert" class="luz">
                <label>Audio de aviso ao Operador</label>
                <span>
                    <input type="text" id="inbg-setup-edit-agent-alert-filename" name="inbg-setup-edit-agent-alert-filename" value="ding" class="text ui-widget-content ui-corner-all" title="Audio que toca no Operador a avisar que vai receber uma chamada. X para inactivo."/>
                    <img src="/images/icons/comment_add_16.png" class="fli as" />
                </span>

                <label>Espera pos alerta</label>
                <input type="text" id="inbg-setup-edit-agent-alert-delay" name="inbg-setup-edit-agent-alert-delay" value="1000"  title="Tempo em milisegundos entre o Operador ouvir o alerta e receber a chamda."/>

            </div>

        </fieldset>
    </form>
</div>
<!--Acaba dialog de edição de INBG-->
<!--Começa dialog Primeira config de PHONE-->
<div id="phone-setup" title="Escolha de Telefone" style="display:none" class="dialog">
    <fieldset>
        <label>Telefone</label>
        <select id="phone-setup-phone" class="text ui-widget-content ui-corner-all">
            <?php
            $query = "Select extension from phones;";
            $rlt = mysql_query($query, $link);
            while ($row = mysql_fetch_row($rlt)) {
                ?>
                <option><?php echo $row[0]; ?></option>
            <?php }
            ?>
        </select>
    </fieldset>
</div>  
<!--Acaba dialog Primeira config de PHONE-->  
<!--Começa dialog Primeira config de VM-->
<div id="vm-setup" title="Escolha de Voicemail" style="display:none" class="dialog">
    <form>
        <fieldset>
            <label>Voicemail</label>
            <select id="vm-setup-vm" class="text ui-widget-content ui-corner-all">
                <?php
                $query = "Select extension from phones;";
                $rlt = mysql_query($query, $link);
                while ($row = mysql_fetch_row($rlt)) {
                    ?>
                    <option><?php echo $row[0]; ?></option>
                <?php }
                ?>
            </select>
        </fieldset>
    </form>
</div>  
<!--Acaba dialog Primeira config de VM-->
<!--Começa dialog Primeira config de PHONE-->
<div id="agent-setup" title="Escolha de Agente" style="display:none" class="dialog">
    <form>
        <fieldset>
            <label>Agente</label>
            <select id="agent-setup-agent" class="text ui-widget-content ui-corner-all">
                <?php
                $query = "SELECT `user`, `full_name` FROM `vicidial_users` WHERE `active`='Y'";
                $rlt = mysql_query($query, $link);
                while ($row = mysql_fetch_row($rlt)) {
                    ?>
                    <option value="<?php echo $row[0]; ?>"><?php echo $row[1]; ?></option>
                <?php }
                ?>
            </select>
        </fieldset>
    </form>
</div>  
<!--Acaba dialog Primeira config de PHONE-->
<!--Começa dialog Primeira config de IVR-->
<div id="ivr-setup" title="Criar IVR" style="display:none" class="dialog">
    <p class="validateTips">Todos os campos são obrigatórios.</p>
    <form>
        <fieldset>
            <label>Nome</label>
            <input type="text" id="ivr-setup-name" class="text ui-widget-content ui-corner-all" />
            <label>Áudio de Entrada</label>
            <span>
                <input type="text" id="ivr-setup-audio-start" class="text ui-widget-content ui-corner-all"  required/>
                <img src="/images/icons/comment_add_16.png" class="fli as" />
            </span>      
        </fieldset>
    </form>
</div>  
<!--Acaba dialog Primeira config de IVR-->
<script>
    $.each($("form"), function(){
        this.reset();
    });
</script>
<script src="inwizzard.js"></script>

</body>

</html>