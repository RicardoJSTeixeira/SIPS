<?php
require ("dbconnect.php");

$vicidial_list_fields = array(array('VENDOR_LEAD_CODE', 'PHONE_NUMBER', 'TITLE', 'FIRST_NAME', 'MIDDLE_INITIAL', 'LAST_NAME', 'ADDRESS1', 'ADDRESS2', 'ADDRESS3', 'CITY', 'STATE', 'PROVINCE', 'POSTAL_CODE', 'COUNTRY_CODE', 'DATE_OF_BIRTH', 'ALT_PHONE', 'EMAIL', 'SECURITY_PHRASE', 'COMMENTS', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15'),
    array('Cod. Venda', 'Número de Telefone', 'Título', 'Primeiro Nome', 'Nr de Contribuinte', 'Ultimo Nome', 'Morada1', 'Morada2', 'Telfone Alt. 2', 'Localidade', 'Distrito', 'Freguesia', 'Código Postal', 'Codigo do País', 'Data de Nascimento', 'Telefone Alt.', 'Email', 'Frase de Segurança', 'Comentarios', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15'));



for ($i = 0; $i < count($vicidial_list_fields[0]); $i++) {
    if (isset($_POST['t' . $vicidial_list_fields[0][$i]])) {
        ${'t' . $vicidial_list_fields[0][$i]} = $_POST['t' . $vicidial_list_fields[0][$i]];
    }
    if (isset($_POST['c' . $vicidial_list_fields[0][$i]])) {
        ${'c' . $vicidial_list_fields[0][$i]} = 1;
    } else {
        ${'c' . $vicidial_list_fields[0][$i]} = 0;
    }
    if (isset($_POST['r' . $vicidial_list_fields[0][$i]])) {
        ${'r' . $vicidial_list_fields[0][$i]} = 1;
    } else {
        ${'r' . $vicidial_list_fields[0][$i]} = 0;
    }
}


if (isset($_POST["campaign_id"])) {
    $campaign_id = $_POST["campaign_id"];
}
if (isset($_POST["order"])) {
    $order = explode(",", $_POST["order"]);
}

if (isset($_GET["DB"])) {
    $DB = $_GET["DB"];
} elseif (isset($_POST["DB"])) {
    $DB = $_POST["DB"];
}



$insertsucceed = FALSE;

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");
?>

<style>

    .selected {
        border:1px solid #90C0E8;
        -webkit-box-shadow:0 0 6px 1px #90C0E8 !important;
        -moz-box-shadow:0 0 6px 1px #90C0E8 !important;
        box-shadow:0 0 6px 1px #90C0E8 !important;
        outline: none;
    }

    #loader {
        position: absolute;
        margin: 5px;
    }

    #nonc {
        background-color:black;
        opacity:0.5;
        width:100%;
        height:100%;
        position:fixed;
        z-index:100;
        top:0;
        left:0;
    }

    #copcamp {
        width:600px;
        height:250px;
        position:fixed;
        z-index:1000;
        margin-left:-300px;
        margin-top:-125px;
        left:50%;
        top:50%;
    }

    .tac {
        text-align: center;
        margin-bottom: 7%;
        margin-top:	7%;
    }


    .mr {
        display:inline-block;
        margin-right:14%;
        width:30%;
    }

    .mr select{
        width: 100%;
    }

    .mc {
        display:inline-block;
        margin:0 2%;
        width:5%;
    }

    .ml {
        display:inline-block;
        margin-left:15%;
        width:30%;
    }

    .ml select{
        width: 100%;
    }

    .vc {
        vertical-align: middle;
    }

    .fr {
        float: right;
        margin-top: 8%;
        width: 33%;
    }

    .fr span {
        margin:0 2%;
        cursor:pointer;
    }

    #fields tbody{min-height: 960px;cursor:n-resize}
    #fields tbody tr:hover {
        background: #CADCEA;}
    </style>

    <script type="text/javascript">
        String.prototype.format = function() {
            var args = arguments;
            return this.replace(/\{(\d+)\}/g, function(m, n) {
                return args[n];
            });
        };


        $(function() {
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            };

            $("#fields tbody").sortable({
                helper: fixHelper,
                update: function() {
                    $("#order").val($("#fields tbody").sortable("toArray"));
                }
            });

            $(".drag").disableSelection();
        });
        function glowStyle(id)
        {
            $('#' + id).addClass('selected');
        }

        function removeStyle(id)
        {
            $('#' + id).removeClass('selected');
        }


        function changestyle(th, ob)
        {
            if (th.checked)
            {
                glowStyle('t' + ob);
            } else
            {
                removeStyle('t' + ob);
            }
        }
        var rT = "<tr id='rr{0}'><td><input type='text' align='middle' value='{1}' maxlength='40' size='40' id='t{0}' name='t{0}'></td><td><input type='checkbox' id='r{0}' name='r{0}'></td><td><input type='checkbox' onclick='changestyle(this,\"{0}\")' class='ck'  id='c{0}' name='c{0}'></td><td><img class='drag' src='/images/icons/node-tree_32.png'></td></tr>";
        var aT = "<tr id='rr{0}'><td><input type='text' align='middle' class='selected' value='{1}' maxlength='40' size='40' readonly='readonly'id='t{0}' name='t{0}'></td><td><input type='checkbox' id='r{0}' name='r{0}'></td><td><input type='checkbox' checked='true' disabled='disabled' id='c{0}' name='c{0}'><input type='hidden' value='true' name='c{0}'></td><td><img class='drag' src='/images/icons/node-tree_32.png'></td></tr>";

        function get_campaign(camp)
        {
            var tab = $("#fields tbody");
            tab.animate({opacity: "0"}, "10", function() {
                tab.css("display", "block");
            });
            $.getJSON("admin_list_ref_q.php",
                    {q: camp},
            function(data) {
                tab.html("");
                $.each(data, function() {
                    var valort = "";
                    var valorc = true;
                    var dummy = 0;

                    switch (this.Name) {
                        case 'ALT_PHONE':
                            valort = "Telefone Alternativo";
                            break;
                        case 'ADDRESS1':
                            valort = "Morada";
                            break;
                        case 'ADDRESS3':
                            valort = "Telefone Alternativo 2";
                            break;
                        case 'FIRST_NAME':
                            valort = "Nome";
                            break;
                        case 'POSTAL_CODE':
                            valort = "Codigo Postal";
                            break;
                        case 'CITY':
                            valort = "Localidade";
                            break;
                        case 'STATE':
                            valort = "Distrito";
                            break;
                        case 'COMMENTS':
                            valort = "Comentários";
                            break;
                        case 'PHONE_NUMBER':
                            valort = "Nº Telefone";
                            break;
                        default:
                            valort = this.Display_name;
                            valorc = this.active == 1;
                            dummy = 1;
                            break;
                    }
                    if (dummy === 1) {
                        tab.append(rT.format(this.Name, valort));
                    } else {
                        tab.append(aT.format(this.Name, valort));
                    }
                    $("#r" + this.Name).attr('checked', this.readonly == 1);
                    $("#c" + this.Name).attr('checked', valorc);
                    var che = $("#c" + this.Name)[0];
                    changestyle(che, this.Name);


                });
                tab.css("display", "").stop().animate({opacity: 1}, "500");
                $("#order").val($("#fields tbody").sortable("toArray"));

            });
        }

        function copy_camp()
        {
            if ($("#campaign_id_ori option:selected").val() === "---" || $("#campaign_id_des option:selected").val() === "---")
            {
                alert("Tem de escolher as Campanhas.");
            }
            else
            {
                var ori = $("#campaign_id_ori option:selected").val();
                var des = $("#campaign_id_des option:selected").val();
                $.post("admin_list_ref_trans.php", {camp_ori: ori, camp_des: des}, function() {
                    alert("Copiado com sucesso.");
                })
                        .error(function() {
                    alert("Sucedeu-se um erro, tente novamente mais tarde.");
                })
                        .complete(function() {
                    $("#copcamp").hide();
                    $("#nonc").hide();
                });
            }
        }
    </script>



    <form action="admin_lists_ref.php" method=post enctype="multipart/form-data" id="fm">
    <input type="hidden" value="rrVENDOR_LEAD_CODE,rrPHONE_NUMBER,rrTITLE,rrFIRST_NAME,rrMIDDLE_INITIAL,rrLAST_NAME,rrADDRESS1,rrADDRESS2,rrADDRESS3,rrCITY,rrSTATE,rrPROVINCE,rrPOSTAL_CODE,rrCOUNTRY_CODE,rrDATE_OF_BIRTH,rrALT_PHONE,rrEMAIL,rrSECURITY_PHRASE,rrCOMMENTS,rrextra1,rrextra2,rrextra3,rrextra4,rrextra5,rrextra6,rrextra7,rrextra8,rrextra9,rrextra10,rrextra11,rrextra12,rrextra13,rrextra14,rrextra15" name="order" id="order">
    <?php
    if (isset($campaign_id)) {
        if ($campaign_id != "- - -") {

            $insert = "INSERT INTO vicidial_list_ref (`Name`,`Display_name`,`readonly`,`active`,`campaign_id`,`field_order`) VALUES 
                                    ('SOURCE_ID','',0,0,'$campaign_id',1), ('LIST_ID','',0,0,'$campaign_id',1), ('PHONE_CODE','',0,0,'$campaign_id',1), ('GENDER','',0,0,'$campaign_id',1), ('RANK','',0,0,'$campaign_id',1), ('OWNER','',0,0,'$campaign_id',1), ";

            for ($i = 0; $i < count($vicidial_list_fields[0]); $i++) {
                $insert.="('" . $vicidial_list_fields[0][$i] . "','" . mysql_real_escape_string(${'t' . $vicidial_list_fields[0][$i]}) . "'," . ${'r' . $vicidial_list_fields[0][$i]} . "," . ${'c' . $vicidial_list_fields[0][$i]} . ",'" . $campaign_id . "',";
                for ($index = 0; $index < count($order); $index++) {
                    if (preg_match("/^".ltrim($order[$index], "rr")."$/i",$vicidial_list_fields[0][$i])) {
                        $insert.="$index)";
                    }
                }
                $insert.=($i == (count($vicidial_list_fields[0]) - 1)) ? ";" : ",\n";
            }
            mysql_query("DELETE FROM vicidial_list_ref WHERE campaign_id LIKE '$campaign_id';", $link) OR die(mysql_error());
            mysql_query($insert, $link) OR die(mysql_error());
            $insertsucceed = TRUE;
        }
    }
    ?>

    <div class='cc-mstyle'>
        <table>
            <tr>
                <td id='icon32'><img src='/images/icons/database_edit_32.png' /></td>
                <td id='submenu-title'> Configuração dos Campos das BDs </td>
                <td style='text-align:left'></td>
            </tr>
        </table>
    </div>

    <div id='work-area' >
        <br><br>
        <div class=cc-mstyle style='border:none'> 
            <table style='margin-left:auto; margin-right:auto; width:90%; margin-bottom: 30px;'>
                <thead>	
                    <tr>
                        <th>Campanha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>

                            <select style='width:50%' name="campaign_id" id="campaign_id">
                                <option>- - -</option>
                                <?php
                                $allowed_camp = "";
                                $current_admin = $_SERVER['PHP_AUTH_USER'];
                                $query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
                                $query = mysql_fetch_assoc($query);
                                $usrgrp = $query['user_group'];
                                $stmt = "SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$usrgrp';";
                                $rslt = mysql_query($stmt, $link);
                                $row = mysql_fetch_row($rslt);
                                $LOGallowed_campaigns = $row[0];
                                $LOGallowed_reports = $row[1];

                                $LOGallowed_campaignsSQL = '';
                                $whereLOGallowed_campaignsSQL = '';
                                if ((!eregi("-ALL", $LOGallowed_campaigns))) {
                                    $rawLOGallowed_campaignsSQL = preg_replace("/ -/", '', $LOGallowed_campaigns);
                                    $rawLOGallowed_campaignsSQL = preg_replace("/ /", "','", $rawLOGallowed_campaignsSQL);
                                    $LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
                                    $whereLOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
                                }
                                $allowed_camp = $whereLOGallowed_campaignsSQL;



                                if (eregi("-ALL-CAMPAIGNS-", $allowed_camp)) {
                                    $allowed_camp = "";
                                }



                                $opt = "";

                                $query = mysql_query("SELECT campaign_id, campaign_name FROM `vicidial_campaigns` WHERE active='Y' $allowed_camp;", $link) or die(mysql_error());

                                while ($row = mysql_fetch_assoc($query)) {

                                    $SL = ($campaign_id == $row['campaign_id']) ? " SELECTED " : "";
                                    $opt.="<option value=$row[campaign_id] $SL>$row[campaign_name]</option>\n";
                                }
                                echo $opt;
                                ?>
                            </select>
                            <img src="/images/icons/ajax-loader.gif" id="loader" style="display: inline;">
                        </td>
                        <td style=width:200px;>
                            <span id="todos"  style=cursor:pointer;float:left; >
                                <img src="../images/icons/check_box_16.png" class=vc />
                                <span id="act" style="margin-right:0.3em;">Ativar</span>todos</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table id="fields" style='margin-left:auto; margin-right:auto; width:90%;'>
                <thead>
                    <tr>
                        <th>Titulo Pretendido</th>
                        <th>Só leitura</th>
                        <th>Activo</th>
                        <th>Ordenar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($campaign_id)) {
                        $query = "Select Name,Display_name,readonly,active from vicidial_list_ref WHERE `campaign_id` LIKE  '$campaign_id' AND Name not in ('SOURCE_ID', 'LIST_ID', 'PHONE_CODE', 'GENDER', 'RANK', 'OWNER') Order by field_order Asc ;";
                        $sips_fields_brute = mysql_query($query, $link) or die(mysql_error());

                        while ($linha = mysql_fetch_assoc($sips_fields_brute)) {
                            if ($linha[Name] == 'FIRST_NAME' OR $linha[Name] == 'PHONE_NUMBER' OR $linha[Name] == 'ADDRESS1' OR $linha[Name] == 'ADDRESS3' OR $linha[Name] == 'CITY' OR $linha[Name] == 'STATE' OR $linha[Name] == 'POSTAL_CODE' OR $linha[Name] == 'ALT_PHONE' OR $linha[Name] == 'COMMENTS') {
                                switch ($linha[Name]) {
                                    case 'PHONE_NUMBER':
                                        $valort = "Nº Telefone";
                                        break;
                                    case 'ALT_PHONE':
                                        $valort = "Telefone Alternativo";
                                        break;
                                    case 'ADDRESS1':
                                        $valort = "Morada";
                                        break;
                                    case 'ADDRESS3':
                                        $valort = "Telefone Alternativo 2";
                                        break;
                                    case 'FIRST_NAME':
                                        $valort = "Nome";
                                        break;
                                    case 'POSTAL_CODE':
                                        $valort = "Codigo Postal";
                                        break;
                                    case 'CITY':
                                        $valort = "Localidade";
                                        break;
                                    case 'STATE':
                                        $valort = "Distrito";
                                        break;
                                    case 'COMMENTS':
                                        $valort = "Comentários";
                                        break;
                                    default:
                                }
                                echo "<tr id='rr" . $linha[Name] . "'>\n";
                                #echo "<td><div class='cc-mstyle' style='height:28px;  ' ><p id='l".$linha[Name]."'>".$vicidial_list_fields[1][$i]."</p></div></td>\n";
                                echo "<td><input align=center type='text' Name='t" . $linha[Name] . "' id='t" . $linha[Name] . "'  readonly='readonly'  size=40 maxlength=40 value='$valort' class='selected' /></td>\n";
                                echo "<td><input type='checkbox' Name='r" . $linha[Name] . "' id='r" . $linha[Name] . "'  " . ($linha['readonly'] == 1 ? "checked='true'" : "") . " /></td>\n";
                                echo "<td><input type='checkbox' Name='c" . $linha[Name] . "' id='c" . $linha[Name] . "' disabled='disabled' checked='true' /><input type='hidden' Name='c" . $linha[Name] . "' Value='true' /></td>\n";
                                echo "<td><img src='/images/icons/node-tree_32.png' class='drag' /></td>";
                                echo "</tr>\n";
                            } else {
                                echo "<tr id='rr" . $linha[Name] . "'>\n";
                                #echo "<td><div class='cc-mstyle' style='height:28px;  ' ><p id='l".$linha[Name]."'>".$vicidial_list_fields[1][$i]."</p></div></td>\n";
                                echo "<td><input align=center type='text' Name='t" . $linha[Name] . "' id='t" . $linha[Name] . "' size=40 maxlength=40 value='" . $linha[Display_name] . "' " . ($linha['active'] == 1 ? "class=selected" : "") . " /></td>\n";
                                echo "<td><input type='checkbox' Name='r" . $linha[Name] . "' id='r" . $linha[Name] . "'  " . ($linha['readonly'] == 1 ? "checked='true'" : "") . " /></td>\n";
                                echo "<td><input type='checkbox' Name='c" . $linha[Name] . "' id='c" . $linha[Name] . "' class='ck' onclick=\"changestyle(this,'" . $linha[Name] . "')\" " . ($linha['active'] == 1 ? "checked" : "") . " /></td>\n";
                                echo "<td><img src='/images/icons/node-tree_32.png' class='drag' /></td>";
                                echo "</tr>\n";
                            }
                        }
                    } else {
                        for ($i = 0; $i < count($vicidial_list_fields[0]); $i++) {

                            if ($vicidial_list_fields[0][$i] == 'FIRST_NAME' OR $vicidial_list_fields[0][$i] == 'PHONE_NUMBER' OR $vicidial_list_fields[0][$i] == 'ADDRESS1' OR $vicidial_list_fields[0][$i] == 'ADDRESS3' OR $vicidial_list_fields[0][$i] == 'CITY' OR $vicidial_list_fields[0][$i] == 'STATE' OR $vicidial_list_fields[0][$i] == 'POSTAL_CODE' OR $vicidial_list_fields[0][$i] == 'ALT_PHONE' OR $vicidial_list_fields[0][$i] == 'COMMENTS') {
                                switch ($vicidial_list_fields[0][$i]) {
                                    case 'PHONE_NUMBER':
                                        $valort = "Nº Telefone";
                                        break;
                                    case 'ALT_PHONE':
                                        $valort = "Telefone Alternativo";
                                        break;
                                    case 'ADDRESS1':
                                        $valort = "Morada";
                                        break;
                                    case 'ADDRESS3':
                                        $valort = "Telefone Alternativo 2";
                                        break;
                                    case 'FIRST_NAME':
                                        $valort = "Nome";
                                        break;
                                    case 'POSTAL_CODE':
                                        $valort = "Codigo Postal";
                                        break;
                                    case 'CITY':
                                        $valort = "Localidade";
                                        break;
                                    case 'STATE':
                                        $valort = "Distrito";
                                        break;
                                    case 'COMMENTS':
                                        $valort = "Comentários";
                                        break;
                                }
                                echo "<tr id='rr" . $vicidial_list_fields[0][$i] . "'>\n";
                                #echo "<td><div class='cc-mstyle' style='height:28px;  ' ><p id='l".$vicidial_list_fields[0][$i]."'>".$vicidial_list_fields[1][$i]."</p></div></td>\n";
                                echo "<td><input align=center type='text' name='t" . $vicidial_list_fields[0][$i] . "' id='t" . $vicidial_list_fields[0][$i] . "'  readonly='readonly'  size=40 maxlength=40 value='$valort' class=selected /></td>\n";
                                echo "<td><input type='checkbox' name='r" . $vicidial_list_fields[0][$i] . "' id='r" . $vicidial_list_fields[0][$i] . "'  " . ($vicidial_list_fields[0][$i] == "PHONE_NUMBER" ? "checked='true'" : "") . " /></td>\n";
                                echo "<td><input type='checkbox' name='c" . $vicidial_list_fields[0][$i] . "' id='c" . $vicidial_list_fields[0][$i] . "' disabled='disabled' checked='true' /><input type='hidden' name='c" . $vicidial_list_fields[0][$i] . "' Value='true' /></td>\n";
                                echo "<td><img src='/images/icons/node-tree_32.png' class='drag' /></td>";
                                echo "</tr>\n";
                            } else {
                                echo "<tr id='rr" . $vicidial_list_fields[0][$i] . "'>\n";
                                #echo "<td><div class='cc-mstyle' style='height:28px;  ' ><p id='l".$vicidial_list_fields[0][$i]."'>".$vicidial_list_fields[1][$i]."</p></div></td>\n";
                                echo "<td><input align=center type='text' name='t" . $vicidial_list_fields[0][$i] . "' id='t" . $vicidial_list_fields[0][$i] . "' size=40 maxlength=40 value='' /></td>\n";
                                echo "<td><input type='checkbox' name='r" . $vicidial_list_fields[0][$i] . "' id='r" . $vicidial_list_fields[0][$i] . "'   /></td>\n";
                                echo "<td><input type='checkbox' name='c" . $vicidial_list_fields[0][$i] . "' id='c" . $vicidial_list_fields[0][$i] . "' class='ck' onclick=\"changestyle(this,'" . $vicidial_list_fields[0][$i] . "')\"  /></td>\n";
                                echo "<td><img src='/images/icons/node-tree_32.png' class='drag' /></td>";
                                echo "</tr>\n";
                            }
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan=4>
                            <div style='height:28px;'>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=3><span style="float:right;cursor: pointer;" id="copcampsh"><img src='../images/icons/shape_square_edit_32.png' style="vertical-align:middle" >Copiar Definições desta Campanha para outra</span></td>
                        <td><span style="float:right;cursor: pointer;" id="submit" ><img alt='Gravar' src='../images/icons/shape_square_add_32.png' style="vertical-align:middle">Gravar Definições</span></td>
                    </tr>
                </tfoot>
            </table>
        </div> <!--fim do wrapper-->
    </div>

                <?php
                if ($insertsucceed) {
                    echo "<script type='text/javascript'>alert('Campos actualizados com sucesso.')</script>\n";
                }
                ?>
    </form>

    <div id=copcamp class=cc-mstyle style="display:none;background-color: white">
        <h2 class=tac>Escolha a campanha de origem e a de destino.</h2>
        <div class="ml">
            <label for="campaign_id_ori">Campanha de Origem</label>
            <select name="campaign_id_ori" id="campaign_id_ori">
                <option>---</option>
                <?= $opt ?>
            </select>
        </div>
        <div class=mc>
            <img src="../images/icons/arrow_right_32.png" class=vc />
        </div>
        <div class="mr">
            <label for="campaign_id_des">Campanha de Destino</label>
            <select name="campaign_id_des" id="campaign_id_des">
                <option>---</option>
                <?= preg_replace("/Selected/i", "", $opt) ?>
            </select>
        </div>
        <div class='mr fr'>
            <div id='ff' style="display:inline;cursor:pointer;"><img src='/images/icons/shape_square_delete_32.png'  class='vc' /> Sair</div>
            <div id='conc' style="display:inline;cursor:pointer;margin-left: 1em;"><img src='/images/icons/shape_square_add_32.png'  class='vc' /> Copiar Campos</div>
        </div>
    </div>

    <div id=nonc style="display:none"></div>

    <script>
        $(document).ready(function() {
            $("#loader").hide();

            $("#copcampsh").click(function() {
                var ind = $("#campaign_id option:selected").index();
                $('#campaign_id_ori option').eq(ind).attr('selected', 'selected');
                $("#nonc").fadeIn("Slow");
                $("#copcamp").show();
            });

            $("#submit").click(function() {
                if ($("#campaign_id option:selected").val() === "- - -")
                {
                    alert("Não tem a Campanha selecionada.");
                }
                else
                {
                    $('#fm').submit();
                }
            });

            $("#ff").click(function() {
                $("#copcamp").hide();
                $("#nonc").hide();
            });

            $("#campaign_id").change(function() {
                $("#loader").css('display', 'inline-block');
                var str = $("#campaign_id option:selected").val();
                get_campaign(str);
                $("#loader").hide();
            });

            $("#conc").click(function() {
                copy_camp();
            });

            $("#todos").toggle(
                    function() {
                        $('.ck').each(function() {
                            $(this).attr('checked', true);
                            changestyle(this, this.id.replace("c", ""));
                        });
                        $("#act").text("Desativar");
                    },
                    function() {
                        $('.ck').each(function() {
                            $(this).attr('checked', false);
                            changestyle(this, this.id.replace("c", ""));
                        });
                        $("#act").text("Ativar");
                    }
            );
        });
    </script>

</body>
</html>