<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Registo de Venda</title>


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
                padding:1px 1px 1px 1px;
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
            .full-table {border:none; border-radius:0px;  width:100%;height:100%; table-layout:fixed; border-collapse: collapse; page-break-after:always;}
            .left {text-align:left !important; text-indent:10px;}
            .right {text-align:right !important;}
            .black {border: 1px solid black;}
            .blacker {border: 2px solid black;}
            .black-bottom {border-bottom:1px solid black !important;}
            img {
                max-width: 100%;
                max-height: 100%;
            }
            pre {margin:0;display:inline}


        </style>
    </head>
    <body> 

        <?
        require("../../ini/dbconnect.php");

        $lead_id = $_GET['id'];

        $query = "SELECT * FROM vicidial_list WHERE lead_id='$lead_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $lead_info = mysql_fetch_assoc($query);



        if (preg_match("/^[23]\d\d\d\d\d\d\d\d$/", $lead_info[phone_number])) {
            $fixo = $lead_info[phone_number];
            $movel = $lead_info[alt_phone];
        } else {
            $fixo = $lead_info[alt_phone];
            $movel = $lead_info[phone_number];
        }
        ?>

        <table  class="cc-mstyle full-table">
            <tbody>
                <tr>
                    <td colspan="4" class="left"><img src="energy_icon2.png" /><img src="energy_icon3.png" /></td>
                    <td colspan="5" class="right"><h2>Relatório Visita Técnica</h2></td>
                </tr>
                <tr>
                    <td colspan="9"  ><BR/></td>
                </tr>
                <tr>
                    <td COLSPAN=9 HEIGHT="21" class="left" ><B>Dados do Cliente / Instalação:</B></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Nome: </B></td>
                    <td COLSPAN=7 HEIGHT="21" class="left black-bottom" ><?= $lead_info[first_name] ?></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Morada:</B></td>
                    <td COLSPAN=7 HEIGHT="21" class="left black-bottom" ><?= $lead_info[address1] ?></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>CP:</B></td>
                    <td COLSPAN=2 HEIGHT="21" class="left black-bottom"><?= $lead_info[postal_code] ?></td>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Localidade:</B></td>
                    <td COLSPAN=3 HEIGHT="21" class="left black-bottom" ><?= $lead_info[city] ?></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Contacto Fixo:</B></td>
                    <td COLSPAN=2 HEIGHT="21" class="left black-bottom" ><?= $fixo ?></td>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Contacto Movél:</B></td>
                    <td COLSPAN=3 HEIGHT="21" class="left black-bottom" ><?= $movel ?></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>Nome Factura:</B></td>
                    <td COLSPAN=2 HEIGHT="21" class="left black-bottom" ><?= $lead_info[extra9] ?></td>
                    <td COLSPAN=2 HEIGHT="21" class="right" ><B>NIF Factura:</B></td>
                    <td COLSPAN=3 HEIGHT="21" class="left black-bottom" ><?= $lead_info[middle_initial] ?></td>
                </tr>
                <tr>
                    <td HEIGHT="13" class="left" colspan="9" ><BR/></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 HEIGHT="21" class="left" ><B>CPE:</B></td>
                    <td><BR/></td>
                    <td COLSPAN=4  ><B>Retific Power®</B></td>
                </tr>
                <tr>
                    <td STYLE="border-left: 3px solid #000000;border-top: 3px solid #000000;border-bottom: 3px solid #000000;" COLSPAN=2 HEIGHT="21" class="left" ><B>Pot. Contratada:</B></td>
                    <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="left" COLSPAN=1  ><b>KW</b><BR/></td>
                    <td style="border-right: 3px solid #000000;border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="left" COLSPAN=1  ><b>KVA</b><BR/></td>
                    <td class="left" ><BR/></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 class="left" ><B>Nº Série:</B></td>
                </tr>
                <tr>
                    <td STYLE="border-left: 3px solid #000000;border-top: 3px solid #000000;border-bottom: 3px solid #000000;" COLSPAN=2 HEIGHT="21" class="left" ><B>Pot. Instalada:</B></td>
                    <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="left" COLSPAN=1  ><b>KW</b><BR/></td>
                    <td style="border-right: 3px solid #000000;border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="left" COLSPAN=1  ><b>KVA</b><BR/></td>
                    <td class="left" ><BR/></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 class="left" ><B>Potência Equipamento:</B></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 HEIGHT="21" class="left" ><B>Tarifa Contratada:</B><pre> <b>BT( ) BTE( ) MT( ) AT( )</b></pre></td>
                    <td class="left" ><BR/></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 class="left" ><B>Forma Pagamento:</B></td>
                </tr>
                <tr>
                    <td HEIGHT="13" colspan="9" ><BR/></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="21"  ><B>Resumo da Instalação - Material Utilizado</B></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="84"  ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="13" colspan="9" ><BR/></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="21"  ><B>Local e Características da Fixação</B></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="84"  ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="13" colspan="9" ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><BR></td>
                    <td COLSPAN=7  ><B>Teste - Registo Eficiência</B></td>
                    <td class="left" ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><BR></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=3  ><B>Registo - Valores</B></td>
                    <td  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><B>Fase 1</B></td>
                    <td STYLE="border: 3px solid #000000;"  ><B>Fase 2</B></td>
                    <td STYLE="border: 3px solid #000000;"  ><B>Fase 3</B></td>
                    <td class="left" ><BR></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><BR></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=3  ><B>Antes Instalação</B></td>
                    <td  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td class="left" ><BR></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><BR></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=3  ><B>Após Instalação</B></td>
                    <td  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td  ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><BR></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=3  ><B>Eficiência / % Poupança</B></td>
                    <td  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td STYLE="border: 3px solid #000000;"  ><BR/></td>
                    <td  ><BR/></td>
                </tr>
                <tr>
                    <td HEIGHT="13" colspan="9"  ><BR/></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="21"  ><B>Observações</B></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=9 ROWSPAN=3 HEIGHT="63"  ><BR/></td>
                </tr>
                <tr>
                </tr>
                <tr>
                </tr>
            </tbody>
        </table>
        <table class="cc-mstyle full-table">
            <tbody>
            <tr>
                <td COLSPAN=9 ROWSPAN=2 HEIGHT="34"   ><B>CONTADOR CONTAGEM:<pre>      /   /   </pre></B></td>
            </tr>
            <tr>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="21"  ><B>Questionário de Satisfação - Cliente</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Cliente</B></td>
                <td STYLE="border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=6 class="left" ><B>Grau Satisfação                                                                            0 a 5 Valores</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Abordagem Comercial</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Aconselharia Equipamento / Empresa</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Preço Equipamento</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Instalação Técnico</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Teste Eficiência Equipamento</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 HEIGHT="21" class="left" ><B>Pontualidade / Limpeza</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>Nota:</B></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=3 class="left" ><B>OBS:</B></td>
            </tr>
            <tr>
                <td HEIGHT="13" class="left" colspan="9" ><BR/></td>
            </tr>
            <tr>
                <td STYLE="border: 3px solid #000000;" COLSPAN=4 HEIGHT="21" class="left" ><B>Técnico Responsável:</B></td>
                <td class="left" ><BR></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=4 class="left" ><B>Tomei Conhecimento - Cliente:</B></td>
            </tr>
            <tr>
                <td STYLE="border-top: 3px solid #000000; border-left: 3px solid #000000" HEIGHT="21" class="left" ><B>Data:</B></td>
                <td STYLE="border-top: 3px solid #000000" class="left" ><BR></td>
                <td STYLE="border-top: 3px solid #000000" class="left" ><BR></td>
                <td STYLE="border-top: 3px solid #000000; border-right: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-top: 3px solid #000000; border-left: 3px solid #000000" class="left" ><B>Data:</B></td>
                <td STYLE="border-top: 3px solid #000000" class="left" ><BR></td>
                <td STYLE="border-top: 3px solid #000000" class="left" ><BR></td>
                <td STYLE="border-top: 3px solid #000000; border-right: 3px solid #000000" class="left" ><BR></td>
            </tr>
            <tr>
                <td STYLE="border-left: 3px solid #000000" HEIGHT="21" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-right: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-left: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-right: 3px solid #000000" class="left" ><BR></td>
            </tr>
            <tr>
                <td STYLE="border-left: 3px solid #000000" HEIGHT="21" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-right: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-left: 3px solid #000000" class="left" ><B>Assinatura:</B></td>
                <td class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-right: 3px solid #000000" class="left" ><BR></td>
            </tr>
            <tr>
                <td STYLE="border-left: 3px solid #000000" HEIGHT="21" class="left" ><B>Assinatura:</B></td>
                <td class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border-right: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
                <td STYLE="border: 3px solid #000000;" COLSPAN=4 ROWSPAN=2  ><B><I><U><FONT SIZE=1 COLOR="#000000">Autorizo Gravações Vídeos e Fotos da Instalação para ações publicitarias e comprovação da Eficiência Energética / Poupança</U></I></B></td>
            </tr>
            <tr>
                <td STYLE="border-bottom: 3px solid #000000; border-left: 3px solid #000000" HEIGHT="21" class="left" colspan="3" ><B>Nº Carteira Eletricista / DGEG:</B></td>
                <td STYLE="border-bottom: 3px solid #000000; border-right: 3px solid #000000" class="left" ><BR></td>
                <td class="left" ><BR></td>
            </tr>
            <tr>
                <td HEIGHT="21" colspan="9" class="left" ><B>EENEGY - Exemplyrigor Energy Sl.</B></td>

            </tr>
            <tr>
                <td COLSPAN=9 HEIGHT="21" class="left" ><B>Calle Diagonal nº 48, 1ª Planta, 2ª Puerta, 08420 Canovelles - Barcelona - Espanha</B></td>
            </tr>
            <tr>
                <td COLSPAN=5 HEIGHT="21" class="left" ><B>Rua Josefa de Óbidos nº 5B, 2650-210 Alfornelos - Portugal - Nº ÚNICO 707 200 220 - geral@exemplyrigor.com</B></td>
                <td colspan="4" class="right"><b>Em certificação</b><img src="iso2000_.jpg" /><img src="iso14001_.jpg" /></td>
            </tr>
        </tbody>
    </table>
</body>
</html>