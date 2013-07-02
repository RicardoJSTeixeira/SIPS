
<?php
require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>




<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery-ui-1.10.2.custom/css/flick/jquery-ui-1.10.2.custom.min.css" />
        <script src="/jquery-ui-1.10.2.custom/js/jquery-1.9.1.js"></script>
        <script src="/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>

        <style>
            .table td{
                text-align: center !important; 
                vertical-align:middle !important;} 
            .formRow .formRight
            {
                width:70%;
            }
        </style>
    </head>
    <body>













        <div class="row-fluid">
            <!--Style forms of validation  Start-->
            <div class="grid span4"  >
                <div class="grid-title">
                    <div class="pull-left">Critérios de pesquisa</div>
                    <div class="pull-right"> <button id="button1" class="btn btn-primary btn-large">Procurar</button></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">


                    <h3> <label class="control-label span">Calendário</label></h3>
                    <div class="formRow">
                        <label>Data de Inicio</label>
                        <div class="formRight">
                            <input type="text" id="from" name="from" />
                        </div>
                    </div>


                    <div class="formRow">
                        <label> Data final </label>
                        <div class="formRight">
                            <input type="text" id="to" name="to"/>
                        </div>
                    </div>










                    <h3> <label class="control-label span">Filtros</label></h3>
                    <div class="formRow">
                        <label>Canal</label>
                        <div class="formRight">
                            <select id="Mv2" class="chzn-select chosen_select" style="width:80%;" ><option value =1>Todos</option></select>
                        </div>
                    </div>

                    <div class="formRow">
                        <label> Campanha</label>
                        <div class="formRight">
                            <select id="Mv3" class="chzn-select chosen_select" style="width:80%;" ><option value =1>Todos</option></select>
                        </div>
                    </div>

                    <div class="formRow">
                        <label> Operadora</label>
                        <div class="formRight">
                            <select  id="Mv4" class="chzn-select chosen_select" style="width:80%;" >
                                <option value =1>Todos</option> 
                                <option value =0>Outros</option> 
                                <option value =21>Fixo</option> 
                                <option value =91>Vodafone</option> 
                                <option value =92>Tmn</option> 
                                <option value =93>Optimus</option> 
                            </select>
                        </div>
                    </div>

                    <div class="formRow">
                        <label>Grupo de User</label>
                        <div class="formRight">
                            <select  id="Mv5" class="chzn-select chosen_select" style="width:80%;" >
                                <option value =1>Todos</option> 

                            </select>
                        </div>
                    </div>







                </div>
            </div>

            <!--Style forms of validation  END-->

            <!--Form help text  Start-->


            <div class="grid Search span7 "><!-----------------------------Tabela de dados-->
                <div class="grid-title">
                    <div class="pull-left ">Tabela de Dados</div>
                    <div class="pull-right   ">
                        <select id="timeScale">
                            <option value =1>Dias</option>
                            <option value =2>Horas</option>
                            <option value =3 selected>Minutos</option>
                        </select>
                    </div>
                </div>
                <table  class="table table-mod">
                    <thead>
                        <tr>
                            <th>Canal</th>
                            <th>Tráfego</th>
                        </tr>
                    </thead>
                    <tbody id="Tabela_Trafego"> </tbody>
                </table>
            </div><!--Tabela de dados-------------------------------------------------------> 




        </div>

























        <!--Ajax loading div -->
        <div id="Ajax_Loader"   style="display: none;    width:70px;    height: 70px;    position: fixed;    top: 46%;    left: 39%;    background:url(/images/icons/big-loader.gif) no-repeat center #fff;    text-align:center;    padding:10px;     margin-left: -50px;    margin-top: -50px;    z-index:2;    overflow: auto;"></div>
















        <script>//SCRIPTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTVTVVTVTVTVT


            $(document).ready(function() {
                $(".Search").hide();
            });
            $(document).ajaxStart(function() {
                $("#Ajax_Loader").show();
            });
            $(document).ajaxStop(function() {
                $(".Search").fadeIn(600);
                $("#Ajax_Loader").hide();
            });













            var ready = false;//para distinguir entre update de informação e renovação.
            var channel = "%SIP%";
            var campanha = "%%";
            var dialledNumber = "REGEXP '[0-9][0-9]'";
            var userGroup = "%%";
            document.getElementById('button1').disabled = true;
            //Update dos pedidos à base de dados··········································································
            function UpdateValues(opcao, dataPrincipale, dataFinale, channel, campanha, dialledNumber, userGroup)
            {


                $.ajax({
                    type: "POST",
                    url: "Requests.php",
                    dataType: "JSON",
                    data: {action: opcao, dataInicio: dataPrincipale, dataFim: dataFinale, channelSearch: channel, campaing: campanha, dialled_Number: dialledNumber, user_Group: userGroup},
                    success: function(data)
                    {
                        if (data === null)
                        {
                            alert("Parametros inseridos sem dados associados");
                            return;
                        }
                        if (opcao === "fromTo" && $("#Mv2").val() === '1')
                        {
                            {
                                var tbody = $("#Tabela_Trafego");
                                tbody.empty();
                                var segTotal = 0;
                                $.each(data, function(index, value) {
                                    tbody.append("<tr><td>" + index + "</td><td>" + secondstotime(value) + "</td></tr>");

                                    segTotal += value;

                                });
                                tbody.append("<tr><td style='text-align: center;' bgcolor='#6D7B8D'><h3>Total</h3></td><td bgcolor='#7D7B8D'>" + secondstotime(segTotal) + "</td></tr>");
                            }
                        }// Para corrigir a procura, ex: canal = fix 1, trazia o fix 1, fix 10, fix 14, fix 13453 etc porque procura do canal escolhido pra frente (%fix1%)
                        if (opcao === "fromTo" && $("#Mv2").val() !== '1')
                        {
                            var tbody = $("#Tabela_Trafego");
                            tbody.empty();
                            $.each(data, function(index, value) {
                                tbody.append("<tr><td>" + index + "</td><td>" + secondstotime(value) + "</td></tr>");
                                return false;
                            });
                        }
                        if (opcao === "Dp2")
                        {
                            var dB2 = document.getElementById("Mv2");
                            var i;
                            for (i = dB2.options.length - 1; i >= 0; i--)
                            {
                                dB2.remove(i);
                            }
                            var opt = document.createElement("option");
                            opt.value = 1;
                            opt.text = "Todos";
                            dB2.options.add(opt); //adiciona all
                            $.each(data, function(index, value) {
                                var opt = document.createElement("option");
                                opt.value = value;
                                opt.text = value;
                                dB2.options.add(opt); //adicionar a opção à dropbox
                            });
                        }
                        if (opcao === "Dp3")
                        {
                            var dB3 = document.getElementById("Mv3");
                            var i;
                            for (i = dB3.options.length - 1; i >= 0; i--)
                            {
                                dB3.remove(i);
                            }
                            var opt = document.createElement("option");
                            opt.value = 1;
                            opt.text = "Todos";
                            dB3.options.add(opt); //adiciona all
                            $.each(data, function(index, value) {
                                var opt = document.createElement("option");
                                opt.value = this.uniqueId;
                                opt.text = this.cp_name;
                                dB3.options.add(opt); //adicionar a opção à dropbox
                            });
                        }

                        if (opcao === "Dp4")
                        {
                            var dB4 = document.getElementById("Mv5");
                            var i;
                            for (i = dB4.options.length - 1; i >= 0; i--)
                            {
                                dB4.remove(i);
                            }
                            var opt = document.createElement("option");
                            opt.value = 1;
                            opt.text = "Todos";
                            dB4.options.add(opt); //adiciona all
                            var i = 0;
                            $.each(data, function(index, value) {
                                var opt = document.createElement("option");
                                if (data[i].value != "")
                                {
                                    opt.value = data[i].value;
                                    opt.text = data[i].name;
                                    dB4.options.add(opt); //adicionar a opção à dropbox
                                }
                                i++;
                            });
                        }
                    }
                });
            }
            //···························································································································






            function secondstotime(seconds)
            {


                if ($("#timeScale").val() === "1") {
                    var numdays = Math.floor(seconds / 86400);
                    var numhours = Math.floor((seconds % 86400) / 3600);
                    var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                    var numseconds = ((seconds % 86400) % 3600) % 60;

                    return numdays + " dias " + numhours + " horas " + numminutes + " minutos " + numseconds + " segundos";
                }


                if ($("#timeScale").val() === "2") {

                    var numhours = Math.floor((seconds) / 3600);
                    var numminutes = Math.floor(((seconds) % 3600) / 60);
                    var numseconds = ((seconds) % 3600) % 60;

                    return numhours + " horas " + numminutes + " minutos " + numseconds + " segundos";
                }

                if ($("#timeScale").val() === "3") {

                    var numminutes = Math.floor(((seconds)) / 60);
                    var numseconds = ((seconds)) % 60;

                    return numminutes + " minutos " + numseconds + " segundos";
                }



            }





            //De (data) a (data)·············································································
            $('#from').datepicker({
                defaultDate: "-1w",
                changeMonth: true,
                numberOfMonths: 3,
                dateFormat: "yy-mm-dd",
                onClose: function(selectedDate) {
                    $("#to").datepicker("option", "minDate", selectedDate);
                },
                onSelect: function() {
                    ready = false;
                }
            });
            $('#to').datepicker({
                defaultDate: "+0w",
                changeMonth: true,
                numberOfMonths: 3,
                dateFormat: "yy-mm-dd",
                onClose: function(selectedDate) {
                    $("#from").datepicker("option", "maxDate", selectedDate);
                },
                onSelect: function() {
                    ready = false;
                    document.getElementById('button1').disabled = false;
                }
            });


            // ···················································································································




            //Check all or diferent choise in dropboxes
            function checkCampos()
            {
                if ($("#Mv2").val() === '1')
                {
                    channel = "%SIP%";
                }
                else
                {
                    channel = "%" + $("#Mv2").val() + "%";
                }
                if ($("#Mv3").val() === '1')
                {
                    campanha = "%%";
                }
                else
                {
                    campanha = "%" + $("#Mv3").val() + "%";
                }
                if ($("#Mv5").val() === '1')
                {
                    userGroup = "%%";
                }
                else
                {
                    userGroup = "%" + $("#Mv5").val() + "%";
                }



                /*<option value =1>All</option> 
                 <option value =0>Outros</option> 
                 <option value =91>Vodafone</option> 
                 <option value =92>Tmn</option> 
                 <option value =93>Optimus</option> 
                 <option value =21>Fixo</option> */
                switch ($("#Mv4").val())
                {
                    case '0':
                        dialledNumber = "NOT REGEXP '(91|92|93|96|21)'";
                        break;
                    case '1':
                        dialledNumber = "REGEXP '[0-9][0-9]'";
                        break;
                    case '91':
                        dialledNumber = "REGEXP '(91)'";
                        break;
                    case '92':
                        dialledNumber = "REGEXP '(92|96)'";
                        break;
                    case '93':
                        dialledNumber = "REGEXP '(93)'";
                        break;
                    case '21':
                        dialledNumber = "REGEXP '(21)'";
                        break;
                }
            }
            //····································································




            //Botao do search------------------------------------------------------------------------------------------
            $('#button1').click(function() {


                var fromDP = $('#from').datepicker({dateFormat: 'yy-mm-dd'}).val();
                var toDP = $('#to').datepicker({dateFormat: 'yy-mm-dd'}).val();
                if (ready)
                {
                    checkCampos();
                    UpdateValues("fromTo", fromDP, toDP, channel, campanha, dialledNumber, userGroup);

                }
                else {
                    UpdateValues("Dp2", fromDP, toDP, 0, 0, 0, 0);
                    UpdateValues("Dp3", fromDP, toDP, 0, 0, 0, 0);
                    UpdateValues("Dp4", fromDP, toDP, 0, 0, 0, 0);
                    checkCampos();
                    UpdateValues("fromTo", fromDP, toDP, channel, campanha, dialledNumber, userGroup);
                    ready = true;
                }
            });
            //--------------------------------------̣̣̣̣̣̣̣̣̣···························································.

        </script>
    </body>
</html>