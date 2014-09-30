<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header .= "../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");
?>
<!DOCTYPE html>
<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <title>SMS</title>

    <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css"/>
    <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css"/>

    <script>
        function send_sms() {

            if ($('input[name=modo]:checked').val() == "uni") {
                if ($('#NR').val().length < 9) {
                    makeAlert("#wr", "SMS não Envida(s)", "O nº não pode ser inferior a 9 caracteres.", 1, true, false);
                    return false;
                }
            }

            if ($('#MSG').val() == "" && !$("#msg_comments").prop("checked")) {
                makeAlert("#wr", "SMS não Envida(s)", "A mensagem não pode ir em vazio.", 1, true, false);
                return false;
            }
            $('#load').slideToggle();
            $('#nonc').fadeIn();

            $.post("sms_send.php", {
                NR: $('#NR').val(),
                MSG: $('#MSG').val(),
                gateway: $('#gateway').val(),
                lista: $('#lista').val(),
                operador: $('#operador').val(),
                modo: $('input[name=modo]:checked').val(),
                msg_comments: $('input[name=msg_comments]:checked').val(),
                LIM: $('#LIM').val()
            }, function (data) {

                if ($('input[name=modo]:checked').val() == 'camp') {
                    makeAlert("#wr", "Enviadas com sucesso.", "", 4, true, false);
                    window.location = 'sms_campaigns_list.php';
                }
                makeAlert("#wr", "Enviada com sucesso.", "", 4, true, false);
                $('#load').slideToggle();
                $('#nonc').fadeOut();
            }, "json").fail(function () {
                makeAlert("#wr", "SMS não Envida(s)", "Sucedeu-se um erro.", 1, true, false);
                $('#load').slideToggle();
                $('#nonc').fadeOut();
            });
        }

        function get_contactos() {
            $.post("sms_aux.php", {
                info: $('#operador').val()
            }, function (data) {
                $('#lista').empty();
                $.each(data, function () {
                    $('#lista').append("<option value='" + this.list_id + "'>" + this.list_name + " - " + this.rows + " Nºs</option>");

                });

                $(".chzn-select").trigger("liszt:updated");
            }, "json");

        }


    </script>
    <style>
        .counter {
            right: 0;
            top: 0;
            font-size: 20px;
            font-weight: bold;
            color: #ccc;
        }

        .chzn-select {
            width: 350px;
        }


    </style>
    <script type="text/javascript" src="/jquery/charCount.js"></script>
</head>
<body>

<div id=load class=cc-mstyle style=display:none;width:600px;position:absolute;z-index:1000;margin-left:-300px;left:50%;border-top-left-radius:0px;border-top-right-radius:0px;>
    <img src="/images/icons/loading.gif" alt=Espera style="margin-right:auto;margin-left:auto;display:block;"/>

    <p style=text-align:center>A enviar...

    <p>
</div>
<div id=nonc style=display:none;background-color:black;opacity:0.5;width:100%;height:100%;position:absolute;z-index:100;></div>

<div class=content>
    <form id="form_sms" method="post" action="sms_send.php">
        <div class="grid">
            <div class="grid-title">
                <div class="pull-left">Enviar ou Criar Campanha</div>
                <div class="pull-right"></div>
                <div class="clear"></div>
            </div>
            <div class="grid-content">
                <div id="wr"></div>
                <div class="formRow">
                    <label data-t="tooltip" title="">Modo:</label>

                    <div class="formRight distance">
                        <p>
                            <input id="campanha" type="radio" name="modo" value="camp" checked>
                            <label for="campanha"><span></span>Por Campanha</label>
                        </p>

                        <p>
                            <input id="unidade" type="radio" name="modo" value="uni">
                            <label for="unidade"><span></span>Unidade</label>
                        </p>
                    </div>
                </div>
                <div class="formRow" id="uni">
                    <label for="NR" data-t="tooltip" title="">Número:</label>

                    <div class="formRight">
                        <input class=num name="NR" id="NR" type="text" maxlength="255" value="9"/>
                    </div>
                </div>
                <div class="formRow camp">
                    <label for="operador" data-t="tooltip" title="">Operador:</label>

                    <div class="formRight">
                        <select name="operador" id="operador">
                            <option value='9'>Todos</option>
                            <option value='96'>TMN</option>
                            <option value='91'>Vodafone</option>
                            <option value='93'>Optimus</option>
                            <option value='929'>Zon</option>
                        </select>
                    </div>
                </div>
                <div class="formRow camp">
                    <label for="lista" data-t="tooltip" title="">Listas:</label>

                    <div class="formRight">
                        <select class="chzn-select" name="lista" id="lista"></select>
                    </div>
                </div>
                <div class="formRow camp">
                    <label for="LIM" data-t="tooltip" title="">Limite:</label>

                    <div class="formRight">
                        <input class="num span2" name="LIM" id="LIM" type="text" maxlength="4" value="100"/>
                    </div>
                </div>
                <div class="formRow">
                    <label for="gateway" data-t="tooltip" title="">Cartão:</label>

                    <div class="formRight">
                        <select name="gateway" id="gateway">
                            <?php
                            $gateways_brute = mysql_query("SELECT ID_gsm,descricao FROM gsm_gateways WHERE active=1 ORDER BY descricao ASC;", $link) or die(mysql_error());
                            while ($gateways_ports = mysql_fetch_assoc($gateways_brute)) {
                                ?>
                                <option value='<?= $gateways_ports['ID_gsm'] ?>'><?= $gateways_ports['descricao'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="formRow"><label for="MSG" data-t="tooltip" title="">Mensagem:</label>

                    <div class="formRight">
                        <div style=margin-top:20px>
                            <span class="counter" id="cmsg" style="float:left;">SMS:0</span>

                            <p style="margin-left:40%" class="camp" id="msgc">
                                <input type="checkbox" value="1" name="msg_comments" id="msg_comments"/><label for="msg_comments"><span></span> Mensagem no campo
                                    <i>Comentários</i></label></p>

                            <span class="counter" id="ccar" style="float:right;">Caracteres:0</span>
                        </div>
                        <textarea name="MSG" id="MSG" maxlength="300" class="span"></textarea>

                        <div class="clear"></div>
                        <div style="font-size:12px;width:80%;margin:0pt auto 20px auto;color:#6D6D6D;">
                            Quando usa a campanha pode usar a tag: "--A--first_name--B--", para substituir pelo primeiro nome. Ex: "Ola --A--first_name--B--, tudo bem?" e aparecerá "Ola João, tudo bem?".
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="seperator_dashed"></div>
                <div class="grid-content">
                    <p class="text-right">
                        <button class="btn btn-primary" id="saveForm" name="submit">Enviar</button>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function () {

        $("#campanha").change(function () {
            $('.camp').toggle();
            $('#uni').toggle();
            $("#msg_comments").prop("checked", false).trigger("change");
        });
        $("#unidade").change(function () {
            $('#uni').toggle();
            $('.camp').toggle();
            $("#msg_comments").prop("checked", false).trigger("change");
        });
        $("#operador").change(function () {
            get_contactos();
        });

        $("#form_sms").submit(function (e) {
            e.preventDefault();
            send_sms();
        });

        $(".num").keydown(function (event) {
            if ((!event.shiftKey && !event.ctrlKey && !event.altKey) && ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
            } else if (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39) {
                event.preventDefault();
            }
        });

        get_contactos();
        $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});

        if ($('input[name=modo]:checked').val() == "camp") {
            $('#uni').hide();
        } else {
            $('.camp').hide();
        }

        $("#MSG").charCount();

        $("#msg_comments").change(function () {

            if (this.checked) {
                $("#MSG").attr("disabled", "");
            } else {
                $("#MSG").removeAttr("disabled");
            }
        });
    });
</script>

</body>
</html>