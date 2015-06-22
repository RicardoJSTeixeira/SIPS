<?php
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header .= "../";
}
define("ROOT", $header);
require(ROOT . 'ini/dbconnect.php');
$today = date("Y-m-d");


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


###########################################################################################

if ($_POST['nc_flag'] == "nc_post") {
    $query = "SELECT list_id, extra1, extra2 FROM vicidial_list WHERE lead_id='$lead_id'";
    $query = mysql_query($query, $link) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));
    $fetch_row = mysql_fetch_row($query);

    $list_id = $fetch_row[0];
    $extra1 = $fetch_row[1];
    $extra2 = $fetch_row[2];
    $marchora = $marchora_horas . ":" . $marchora_minutos . ":00";

    if ($list_id < 1) {
        $list_id = '999';
    }

    $query = "INSERT INTO vicidial_list (entry_date, status, user, vendor_lead_code, list_id, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, date_of_birth, alt_phone, security_phrase, comments, source_id, owner, last_local_call_time, extra1) VALUES 
			('$today','NOVOCL','$operador','$vendor_lead_code','$list_id','$phone_number','" . mysql_real_escape_string($title) . "','" . mysql_real_escape_string($first_name) . "','" . mysql_real_escape_string($middle_initial) . "','" . mysql_real_escape_string($last_name) . "','" . mysql_real_escape_string($address1) . "','" . mysql_real_escape_string($address2) . "','" . mysql_real_escape_string($address3) . "','" . mysql_real_escape_string($city) . "','" . mysql_real_escape_string($state) . "','" . mysql_real_escape_string($province) . "','" . mysql_real_escape_string($postal_code) . "','" . mysql_real_escape_string($country_code) . "','" . mysql_real_escape_string($date_of_birth) . "','" . mysql_real_escape_string($alt_phone) . "','" . mysql_real_escape_string($security_phrase) . "','" . mysql_real_escape_string($comments) . "', '" . mysql_real_escape_string($comments) . "', '" . mysql_real_escape_string($owner) . "', NOW(), '" . mysql_real_escape_string($extra1) . "')";
    mysql_query($query, $link) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));
####################################################
    $last_insert_id = mysql_insert_id($link);
####################################################


    $query = "INSERT INTO custom_" . (($inout == "out") ? strtoupper($campanha) : $campanha) . "(lead_id, tipoconsulta, consultorio, consultoriodois, marchora, obs, marcdata)
			VALUES ('$last_insert_id','$tipoconsulta','$consultorio','$consultoriodois','$marchora','$obs','$marcdata')";
    mysql_query($query, $link) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));

    $query = "UPDATE sips_sd_reservations SET lead_id=$last_insert_id WHERE lead_id=$lead_tmp";
    mysql_query($query, $link) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));

    echo json_encode(array("id" => $last_insert_id));
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Formulário - Novo Cliente</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="/jquery/jquery-1.8.3.js"></script>
    <script src="/jquery/jqueryUI/jquery-ui-1.9.0.custom.min.js"></script>
    <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css"/>
    <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>

    <style>
        .chzn-select {
            width: 350px;
        }
    </style>

</head>
<body>
<div class="content">
<form id="form_custom_fields" name="form_custom_fields" action="novo_cliente.php" method=POST>
<div class="grid">
    <div class="grid-title">
        <div class="pull-left">Novo Cliente</div>
        <div class="pull-right">
               <span class="icon-alone" data-toggle="dropdown">
                    <i class="icon-group"></i>
               </span>
        </div>
    </div>
    <div class="grid-content">
        <input type=hidden value='<?= $lead_id ?>' name=lead_id>
        <input type=hidden value='<?= $operador ?>' name=operador>
        <input type=hidden value='<?= $campanha ?>' name=campanha>
        <input type=hidden value='<?= $inout ?>' name=inout>
        <input type=hidden value='nc_post' name="nc_flag">

        <div class="form-horizontal">

            <div class="control-group">
                <label class="control-label">Ref. Cliente</label>

                <div class="controls">
                    <input type=text class=span name=owner id=owner value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Pref. Marcação</label>

                <div class="controls">
                    <input type=text class=span name=security_phrase id=security_phrase value="<?= $security_phrase ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Title</label>

                <div class="controls">
                    <input type=text class=span name=title id=title value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Nome</label>

                <div class="controls">
                    <input type=text class=span name=first_name id=first_name value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Nome(s) do Meio</label>

                <div class="controls">
                    <input type=text class=span name=middle_initial id=middle_initial value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Apelido</label>

                <div class="controls">
                    <input type=text class=span name=last_name id=last_name value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Morada</label>

                <div class="controls">
                    <input type=text class=span name=address1 id=address1 value="<?= $address1 ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Nº da Porta</label>

                <div class="controls">
                    <input type=text class=span name=vendor_lead_code id=vendor_lead_code value="<?= $vendor_lead_code ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Morada 2</label>

                <div class="controls">
                    <input type=text class=span name=address2 id=address2 value="<?= $address2 ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Morada 3</label>

                <div class="controls">
                    <input type=text class=span name=address3 id=address3 value="<?= $address3 ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Localidade</label>

                <div class="controls">
                    <input type=text class=span name=city id=city value="<?= $city ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Concelho</label>

                <div class="controls">
                    <input type=text class=span name=province id=province value="<?= $province ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Distrito</label>

                <div class="controls">
                    <input type=text class=span name=state id=state value="<?= $state ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Codigo Postal</label>

                <div class="controls">
                    <input type=text class=span name=postal_code id=postal_code value="<?= $postal_code ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Cod. País</label>

                <div class="controls">
                    <input type=text class=span name=country_code id=country_code value="<?= $country_code ?>">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Data Nascimento</label>

                <div class="controls">
                    <input type=text class=span name=date_of_birth id=date_of_birth value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Telefone</label>

                <div class="controls">
                    <input type=text class=span name=phone_number id=phone_number value="<?= $phone_number ?>">
                </div>
            </div>

            <div class="control-group"><label class="control-label">Telefone Alternativo</label>

                <div class="controls"><input type=text class=span name=alt_phone id=alt_phone value="<?= $alt_phone ?>">
                </div>
            </div>

            <div class="control-group"><label class="control-label">Cod. Campanha</label>

                <div class="controls"><input type=text class=span name=comments id=comments value="<?= $comments ?>"/>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="grid">
<div class="grid-title">
    <div class="pull-left">Novo Cliente</div>
    <div class="pull-right">
        <button class="btn btn-success"><i class="icon-group"></i>Gravar</button>
    </div>
</div>

<div class="grid-content">

<div class="form-horizontal">
<div class="control-group">
    <label class="control-label">Tipo de Consulta:</label>

    <div class="controls">
        <input class="radiocheck-required" id="tipoconsulta1" type="RADIO" value="Home" name="tipoconsulta">
        <label for="tipoconsulta1" class="checkbox inline"><span></span>Casa</label>
        <input class="radiocheck-required" id="tipoconsulta2" type="RADIO" value="CATOS" name="tipoconsulta">
        <label for="tipoconsulta2" class="checkbox inline"><span></span>CATO</label>
        <input class="radiocheck-required" id="tipoconsulta3" type="RADIO" value="Branch" name="tipoconsulta">
        <label for="tipoconsulta3" class="checkbox inline"><span></span>Consultorio</label>
        <input class="radiocheck-required" id="tipoconsulta4" type="RADIO" value="semconsulta" checked name="tipoconsulta">
        <label for="tipoconsulta4" class="checkbox inline"><span></span>Nenhum</label>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="consultorio" >CATO mais perto da área de residência do cliente</label>

    <div class="controls">
        <select class="multiple-requiredconsultorio chzn-select" id="consultorio" name="consultorio">
            <option selected="" value="nenhum">Nenhum</option>
            <option value="C/AGD"> Águeda</option>
            <option value="C/ABF"> Albufeira</option>
            <option value="C/ACT"> Alcoutim</option>
            <option value="C/ALM"> Almeirim</option>
            <option value="C/AMR"> Amarante</option>
            <option value="C/AND"> Anadia</option>
            <option value="C/AVZ"> Arcos de Valdevez</option>
            <option value="C/ARG"> Arganil</option>
            <option value="C/AVC"> Alverca</option>
            <option value="C/BRC"> Barcelos</option>
            <option value="C/BEL"> Bebém</option>
            <option value="C/CTH"> Cantanhede</option>
            <option value="C/CTX"> Cartaxo</option>
            <option value="C/CDX"> Condeixa</option>
            <option value="C/COR"> Coruche</option>
            <option value="C/ENT"> Entroncamento</option>
            <option value="C/ESP"> Espinho</option>
            <option value="C/ETJ"> Estarreja</option>
            <option value="C/VNF"> Famalicão</option>
            <option value="C/FAF"> Fafe</option>
            <option value="C/FDV"> Figueiro dos Vinhos</option>
            <option value="C/GOV"> Gouveia</option>
            <option value="C/GDL"> Grândola</option>
            <option value="C/LGS"> Lagos</option>
            <option value="C/LMG"> Lamego</option>
            <option value="C/LRJ"> Laranjeiro</option>
            <option value="C/LDV"> Linda-a-Velha</option>
            <option value="C/LOL"> Loulé</option>
            <option value="C/LOR"> Loures</option>
            <option value="C/LSA"> Lousã</option>
            <option value="C/ILH"> Ilhavo</option>
            <option value="C/MFA"> Mafra</option>
            <option value="C/MGL"> Mangualde</option>
            <option value="C/MGR"> Marinha Grande</option>
            <option value="C/MED"> Mêda</option>
            <option value="C/MRD"> Mirandela</option>
            <option value="C/MTN"> Montemor-o-Novo</option>
            <option value="C/MTV"> Montemor-o-Velho</option>
            <option value="C/MOC"> Moscavide</option>
            <option value="C/MUR"> Moura</option>
            <option value="C/NIS"> Nisa</option>
            <option value="C/ODM"> Odemira</option>
            <option value="C/OLH"> Olhão</option>
            <option value="C/OLV"> Olivais</option>
            <option value="C/OAZ"> Oliveira de Azeméis</option>
            <option value="C/ODB"> Oliveira do Bairro</option>
            <option value="C/ODH"> Oliveira do Hospital</option>
            <option value="C/ORE"> Ourém</option>
            <option value="C/PDG"> Pedrógão Grande</option>
            <option value="C/PRD"> Parede</option>
            <option value="C/PNE"> Peniche</option>
            <option value="C/PNL"> Pinhel</option>
            <option value="C/PTS"> Ponte de Sor</option>
            <option value="C/RMR"> Rio Maior</option>
            <option value="C/SMC"> Samora Correia</option>
            <option value="C/SCD"> Santa Comba Dão</option>
            <option value="C/STG"> Santiago do Cacém</option>
            <option value="C/SEI"> Seia</option>
            <option value="C/SSB"> Sesimbra</option>
            <option value="C/SIV"> Silves</option>
            <option value="C/SNT"> Sintra</option>
            <option value="C/STA"> Sertã</option>
            <option value="C/STM"> Salvaterra de Magos</option>
            <option value="C/TVR"> Tavira</option>
            <option value="C/TDL"> Tondela</option>
            <option value="C/VCB"> Vale de Cambra</option>
            <option value="C/VNF"> Vila Nova de Famalicão</option>
            <option value="C/VSA"> Vila Real de Santo António</option>
            <option value="C/CDX"> Condeixa</option>
        </select>
    </div>
</div>


<div class="control-group">
    <label class="control-label" for="consultoriodois" >Consultorio mais perto da área de residencia do cliente</label>

    <div class="controls">
        <select class="multiple-requiredconsultoriodois chzn-select" id="consultoriodois" name="consultoriodois">
            <option selected="" value="nenhum">Nenhum</option>
            <option value="B/ABN"> Consultório Abrantes</option>
            <option value="B/BALC"> Consultório Alcobaça</option>
            <option value="B/BALG"> Consultório Algés</option>
            <option value="B/ALM"> Consultório Almada</option>
            <option value="B/ALV"> Consultório Lisboa - Alvalade</option>
            <option value="B/AMD"> Consultório Amadora</option>
            <option value="B/AMS"> Consultório Amora</option>
            <option value="B/AVE"> Consultório Aveiro</option>
            <option value="B/BEJ"> Consultório Beja</option>
            <option value="B/BEN"> Consultório Lisboa - Benfica</option>
            <option value="B/BIO"> Consultório Ponta Delgada</option>
            <option value="B/BLX"> Consultório Lisboa - Baixa</option>
            <option value="B/BRA"> Consultório Braga</option>
            <option value="B/BRN"> Consultório Bragança</option>
            <option value="B/BRR"> Consultório Barreiro</option>
            <option value="B/CAB"> Consultório Castelo Branco</option>
            <option value="B/CAC"> Consultório Cacém</option>
            <option value="B/CDR"> Consultório Caldas da Rainha</option>
            <option value="B/CAS"> Consultório Cascais</option>
            <option value="B/CHV"> Consultório Chaves</option>
            <option value="B/CED"> Consultório Porto - Cedofeita</option>
            <option value="B/COI"> Consultório Coimbra</option>
            <option value="B/COP"> Consultório Porto - Bolhão</option>
            <option value="B/COV"> Consultório Covilhã</option>
            <option value="B/BREL"> Consultório Elvas</option>
            <option value="B/EVO"> Consultório Évora</option>
            <option value="B/FAR"> Consultório Faro</option>
            <option value="B/FDF"> Consultório Figueira da Foz</option>
            <option value="B/BFUN"> Consultório Fundão</option>
            <option value="B/GAI"> Consultório Gaia</option>
            <option value="B/GRD"> Consultório Guarda</option>
            <option value="B/GUI"> Consultório Guimarães</option>
            <option value="B/BGON"> Consultório Gondomar</option>
            <option value="B/HHPOR"> Consultório Porto - Boavista</option>
            <option value="B/LEI"> Consultório Leiria</option>
            <option value="B/MAD"> Consultório Funchal</option>
            <option value="B/MIC"> Consultório Lisboa - Arroios</option>
            <option value="B/BMAI"> Consultório Maia</option>
            <option value="B/MMT"> Consultório Mem Martins</option>
            <option value="B/MTJ"> Consultório Montijo</option>
            <option value="B/MTS"> Consultório Matosinhos</option>
            <option value="B/ODI"> Consultório Odivelas</option>
            <option value="B/BROE"> Consultório Oeiras</option>
            <option value="B/OMT"> Consultório Lisboa - Avenidas Novas</option>
            <option value="B/OUR"> Consultório Lisboa - Campo de Ourique</option>
            <option value="B/BOVR"> Consultório Ovar</option>
            <option value="B/PEN"> Consultório Penafiel</option>
            <option value="B/PMB"> Consultório Pombal</option>
            <option value="B/PTL"> Consultório Portalegre</option>
            <option value="B/PTM"> Consultório Portimão</option>
            <option value="B/BPOV"> Consultório Póvoa de Varzim</option>
            <option value="B/BQLZ"> Consultório Queluz</option>
            <option value="B/SAN"> Consultório Santarém</option>
            <option value="B/SMF"> Consultório Santa Maria da Feira</option>
            <option value="B/BRSA"> Consultório Santo Tirso</option>
            <option value="B/STB"> Consultório Setúbal</option>
            <option value="B/TMR"> Consultório Tomar</option>
            <option value="B/BRTO"> Consultório Torres Novas</option>
            <option value="B/TVD"> Consultório Torres Vedras</option>
            <option value="B/VCA"> Consultório Viana do Castelo</option>
            <option value="B/VFX"> Consultório Vila Franca de Xira</option>
            <option value="B/VIS"> Consultório Viseu</option>
            <option value="B/VLR"> Consultório Vila Real</option>
        </select>
    </div>
</div>


<div class="control-group">
    <label class="control-label">Data em que se realizará o Rastreio Gratuito:</label>

    <div class="controls">
        <div class="input-append">
            <input class="date-required" id="marcdata" type="text" value="" name="marcdata">
            <span class="btn" onclick='calendarOpener();'><i class="icon-time"></i>Calendário</span>
        </div>
    </div>
</div>

<ul>
    <li>Casa: a partir de 5 dias úteis / máximo 2 semanas</li>
    <li>Consultório: a partir de 3 dias úteis / máximo 2 semanas</li>
</ul>


<ul>
    <li>Casa: das 9:00 às 19:00</li>
    <li>Consultório: das 10:00 às 17:30</li>
</ul>


<div class="control-group">
    <label class="control-label">Hora em que se realizará o Rastreio Gratuito:</label>

    <div class="controls">
        <select class="hour-required" name='marchora_horas' id='HOUR_marchora'>
            <option>09</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
            <option>13</option>
            <option>14</option>
            <option>15</option>
            <option>16</option>
            <option>17</option>
            <option>18</option>
            <option>19</option>
            <option>20</option>
        </select>
        <select class="minute-required" name='marchora_minutos' id='MINUTE_marchora'>
            <option>00</option>
            <option>05</option>
            <option>10</option>
            <option>15</option>
            <option>20</option>
            <option>25</option>
            <option>30</option>
            <option>35</option>
            <option>40</option>
            <option>45</option>
            <option>50</option>
            <option>55</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label">Campo de Observações (nome de quem irá acompanhar durante a consulta/referências da morada/outros)</label>

    <div class="controls">
        <textarea id="obs" rows="5" name="obs"></textarea>
    </div>
</div>

</div>
</div>
</div>

</form>
</div>
<script>

    function getRadioValue() {
        var colRadio = document.getElementsByName('tipoconsulta');
        for (var i = 0; i < colRadio.length; i++) {
            if (colRadio[i].checked == true) {
                return colRadio[i].value;
            }
        }
        return null;
    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function calendarOpener() {
        var
            mtd = getRadioValue(),
            url = '../../../sips-admin/reservas/views/calendar_container.php',
            params={},
            cp,
            ref;

        if (mtd === 'semconsulta')
            return false;

        switch (mtd){
            case 'Home':
                cp = parent.document.getElementById('postal_code').value;
                params['cp'] = cp;
                break;
            case 'Branch':
                ref = $('#consultoriodois').val();
                params['ref'] = ref;
                break;
            case 'CATOS':
                ref = $('#consultorio').val();
                params['ref'] = ref;
                break;
        }

        params['user'] = user;
        params['lead'] = lead_tmp;

        window.open(url +"?"+ $.param(params), 'Calendario', 'fullscreen=yes, scrollbars=auto,status=1');

    }

    function grava() {


        if ($(".radiocheck-required:checked").length) {

            if ($(".radiocheck-required:checked").val() === "CATOS") {
                if ($(".multiple-requiredconsultorio option:selected").val() === "nenhum" || typeof $(".multiple-requiredconsultorio option:selected").html() === "undefined") {
                    alert("Campo do CATO com erros de preenchimento ou vazio.");
                    return false;
                }
            }
            if ($(".radiocheck-required:checked").val() === "Branch") {
                if ($(".multiple-requiredconsultoriodois option:selected").val() === "nenhum" || typeof $(".multiple-requiredconsultoriodois option:selected").html() === "undefined") {
                    alert("Campo do Consultório com erros de preenchimento ou vazio.");
                    return false;
                }
            }

        } else {
            alert("Campo do tipo de consulta com erros de preenchimento.");
            return false;
        }


        if ($(".hour-required").val().length === "---") {
            alert("Campo da Hora da Marcação com erros de preenchimento ou vazio.");
            $("#DispoMinimizeButton").click();
            return false;
        }
        if ($(".minute-required").val().length === "---") {
            alert("Campo dos Minutos da Marcação com erros de preenchimento ou vazio.");
            $("#DispoMinimizeButton").click();
            return false;
        }
        if ($(".date-required").val().length <= 0) {
            alert("Campo da Data da Marcação com erros de preenchimento ou vazio.");
            $("#DispoMinimizeButton").click();
            return false;
        }


        var params = $(this).serializeArray();
        params.push({name: "lead_tmp", value: lead_tmp})
        $.post(
            '<?= $_SERVER['PHP_SELF'] ?>',
            $.param(params),
            function (data) {
                opener.nc_live = true;
                if (opener.nc_live_id == undefined) {
                    opener.nc_live_id = [];
                }
                opener.nc_live_id.push(data.id);
                self.close();
            }, "json"
        ).fail(function () {
                alert("Ocorreu um erro na gravação. Pergunte ao coordenador se esta campanha tem o script criado.");
            });
        return false;
    }


    var
        user = '<?= $_GET["operador"] ?>',
        lead_tmp = getRandomInt(100000000, 999999999);
    marcado = false;

    $(function () {


        console.log(lead_tmp)

        $("#form_custom_fields").submit(grava);

        $("#marcdata").datepicker();

        $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
    });
</script>
</body>
</html>
