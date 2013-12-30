<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


        <style type="text/css">
            .cc-mstyle {

                font-family: "Calibri";
                font-size: 12px; 
                background-color:#FFF;
                border: 2px solid #90C0E8;
                border-right: 2px solid #90C0E8;
                border-bottom: 2px solid #90C0E8;
                border-radius: 15px;


                margin-top:0;
                cursor:default;
            }


            .cc-mstyle label {
                color:#333;
            }
            .cc-mstyle table { 
                border:none; 
                padding:0;

                border-collapse: collapse;
                margin-left:auto;
                margin-right:auto;
            }
            .cc-mstyle tr:hover td {
                /* background-color:#e8edff; */
            }
            .cc-mstyle th {
                color: #000;
                font-size:12px;
                padding:10px 3px 10px 3px;
                width:auto;
            }
            .cc-mstyle th a {
                color: #000;
                font-size:12px;
                padding:2px 3px 2px 3px;
                text-decoration:none;
            }
            .cc-mstyle td {
                color: #000;
                font-size:12px;
                padding:2px 3px 2px 3px;
                width:auto;
                text-align:center;
            }
            .cc-mstyle td a {
                color: #000;
                font-size:12px;
                padding:0;
                text-decoration:none;
            }


            .cc-mstyle label {
                display:block;
                margin:0;
                padding:0px 0 0 0;
                margin:6px 0 -2px 0px; 
            }
            .cc-mstyle textarea {
                width:561px;
                border:1px solid #c0c0c0;
                margin:0;
                height:200px;
                background-color:#fff;
                resize:none;
                font:normal 12px/1.5em "Liberation sans", Arial, Helvetica, sans-serif;
            }
            .cc-mstyle input[type="text"] {
                width:400px;
                border:1px solid #c0c0c0;
                margin:0;
                height:28px;
                background-color:#fff;


            }
            .cc-mstyle input[type="password"] { 
                width:396px;
                border:1px solid #c0c0c0;
                margin:0;
                height:20px;
                background-color:#fff;


            }

            .cc-mstyle input[type="email"] { 
                width:396px;
                border:1px solid #c0c0c0;
                margin:0;
                height:20px;
                background-color:#fff;


            }


            .cc-mstyle select { 
                width:400px;
                padding: 4px;
                background-color: #FFF;
                border: 1px solid #c0c0c0;
                color: #000;
                height: 28px;
                width: 145px;
                margin-top:0px;
                text-align:center;
                font-size:12px;

            }  
            .cc-mstyle .checkbox { 
                margin: 4px 0; 
                padding: 0; 
                width: 14px;
                border: 0;
                background: none;
            }
        </style>
        <?
        require('../../sips-admin/dbconnect.php');

        $lead_id = $_GET['id'];

    
        $datafim = date("Y-m-d", strtotime("+1 day" . $_POST['data']));

        require '../../ini/user.php';
        $user=new user;


        $custom_allowed = $user->allowed_campaigns;



        for ($f = 1; $f < count($custom_allowed); $f++) {
            if (($f + 1) == count($custom_allowed)) {
                $build_query .= " (SELECT * FROM custom_$custom_allowed[$f]) ";
            } else {
                $build_query .= " (SELECT * FROM custom_$custom_allowed[$f]) UNION ALL ";
            }
        }


        $query = "SELECT * FROM vicidial_list A LEFT JOIN ( $build_query ) B ON B.lead_id=A.lead_id WHERE A.lead_id='$lead_id'";
        $query = mysql_query($query, $link);
        $row = mysql_fetch_assoc($query);


        $query = "SELECT full_name FROM vicidial_users WHERE user='$row[user]'";
        $query = mysql_query($query, $link);
        $row_user = mysql_fetch_row($query);

        $exp_data = explode(" ", $row['last_local_call_time']);
        $exp_data = explode("-", $exp_data[0]);
        $data_re = $exp_data[2] . "-" . $exp_data[1] . "-" . $exp_data[0];

        $exp_reuniao = explode("-", $row['reuniao']);
        $reuniao_re = $exp_reuniao[2] . "-" . $exp_reuniao[1] . "-" . $exp_reuniao[0];


        
        ?>  
        <title>Reunião - <?= $row['first_name']; ?></title>
    </head>

    <body> 

        <table border="1" class="cc-mstyle" style='border:none; width:635px; table-layout:fixed'>
            <tr>
                <td colspan="8"><b>Folha de Reunião</b></td>
            </tr>

            <tr>
                <td style='background-color:lightgrey'>Letra:</td>
                <td><?= $row['letra']; ?></td>
                <td style='background-color:lightgrey'>Comercial</td>
                <td colspan="2"></td>
                <td style='background-color:lightgrey'>Operador:</td>
                <td colspan="2"><?= $row_user[0]; ?></td>
            </tr>
            <tr>
                <td style='background-color:lightgrey'>Data:</td>
                <td><?= $data_re; ?></td>
                <td style='background-color:lightgrey'>Reunião:</td>
                <td colspan="2"><?= $reuniao_re; ?></td>
                <td style='background-color:lightgrey'>Hora:</td>
                <td colspan="2"><?= $row['hora']; ?></td>
            </tr>
            <tr>
                <td style='background-color:lightgrey'>Empresa:</td>
                <td colspan="7"><?= $row['first_name']; ?></td>
            </tr>
            <tr>
                <td style='background-color:lightgrey'>Falei com:</td>
                <td colspan="3"><?= $row['faleicom']; ?></td>
                <td style='background-color:lightgrey'>Decisor:</td>
                <td colspan="3"><?= $row['decisor']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Contacto Fixo:</td>
                <td colspan="2"><?= $row['phone_number']; ?></td>
                <td colspan="2" style='background-color:lightgrey'>Contacto Móvel:</td>
                <td colspan="2"><?= $row['contactomovel']; ?></td>
            </tr>
            <tr>
                <td style='background-color:lightgrey'>Rua:</td>
                <td  colspan="7"><?= $row['address1']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Código Postal:</td>
                <td colspan="2"><?= $row['postal_code']; ?></td>
                <td style='background-color:lightgrey'>Localidade:</td>
                <td colspan="3"><?= $row['city']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Ponto Ref.:</td>
                <td colspan="6"><?= $row['pontoref']; ?></td>
            </tr>
            <tr>
                <td colspan="8"><b>Serviço do Cliente</b></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Operador Fixo:</td>
                <td colspan="2"><?= $row['operadorfixo']; ?></td>
                <td colspan="2" style='background-color:lightgrey'>Mensalidade:</td>
                <td colspan="2"><?= $row['operadorfixovalor']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Operador Móvel:</td>
                <td colspan="2"><?= $row['operadormovel']; ?></td>
                <td colspan="2" style='background-color:lightgrey'>Mensalidade:</td>
                <td colspan="2"><?= $row['operadormovelvalor']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Net Fixa:</td>
                <td colspan="2"><?= $row['operadornetfixa']; ?></td>
                <td colspan="2" style='background-color:lightgrey'>Mensalidade:</td>
                <td colspan="2"><?= $row['operadornetfixavalor']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Net Móvel:</td>
                <td colspan="2"><?= $row['operadornetmovel']; ?></td>
                <td colspan="2" style='background-color:lightgrey'>Mensalidade:</td>
                <td colspan="2"><?= $row['operadornetmovelvalor']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style='background-color:lightgrey'>Fidelização até.:</td>
                <td colspan="6"><?= $row['fidelizacao']; ?></td>
            </tr>
            <tr>
                <td colspan="2"  style='background-color:lightgrey'><br/>Observações:<br/><br/></td>
                <td colspan="6"><?= $row['obs']; ?></td>
            </tr>
            <tr>
                <td colspan="8"><b>Reagendamento</b></td>
            </tr>
            <tr>
                <td>Primeiro:</td>
                <td>Data:</td>
                <td></td>
                <td>OB:</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Segundo:</td>
                <td>Data:</td>
                <td></td>
                <td>OB:</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Terceiro:</td>
                <td>Data:</td>
                <td></td>
                <td>OB:</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Quarto:</td>
                <td>Data:</td>
                <td></td>
                <td>OB:</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="8"><b>Comercial</b></td>
            </tr>
            <tr>
                <td colspan="8"><br/><br/><br/><br/><br/><br/><br/></td></tr>
            <tr>
                <td colspan="8"><b>Proposta</b>
                </td>
            </tr>
            <tr>
                <td colspan="8"><br/><br/><br/><br/><br/><br/><br/></td>
            </tr>
            <tr>
                <td colspan="2">NIF</td>
                <td colspan="2">CRC</td>
                <td colspan="2">Produto</td>
                <td colspan="2">Anulado</td>
            </tr>
            <tr>
                <td colspan="2"><br/><br/></td>
                <td colspan="2"><br/><br/></td>
                <td colspan="2"><br/><br/></td>
                <td colspan="2"><br/><br/></td>
            </tr>
        </table>


        <?php
        $seleccao = explode(',', $curM['servico']);

        //print_r($seleccao);
        for ($i = 1; $i < 15; $i++) {

            $cs = explode('-', $seleccao[$i - 1]);
            $finalarray[$cs[0]] = $cs[1];
            if ($finalarray[$i] != NULL) {

                $total = $total + $finalarray[$i];

                echo "<tr><td>" . $finalarray[$i] . " € </td><td>Sugerido</td></tr>";
            } else {
                echo "<tr><td></td><td>&nbsp;</td></tr>";
            }
        }
        ?>


    </body>
</html>