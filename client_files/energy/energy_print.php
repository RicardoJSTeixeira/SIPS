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


            .full-table {border:none; border-radius:0px;  width:100%;height:100%; table-layout:fixed; border-collapse:collapse; page-break-after:always;}

            .left {text-align:left !important; text-indent:10px;}
            .right {text-align:right !important;}
            .black {border: 1px solid black;}
            .blacker {border: 2px solid black;}
            .black-bottom {border-bottom:1px solid black !important;}
            img {
                max-width: 100%;
                max-height: 100%;
            }
        </style>
    </head>
    <body> 

        <?
        require("../../ini/dbconnect.php");

        $lead_id = $_GET['id'];

        $query = "SELECT * FROM vicidial_list WHERE lead_id='$lead_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $lead_info = mysql_fetch_assoc($query);
        
        $query="SELECT start_epoch, full_name FROM  `vicidial_log` a INNER JOIN  `vicidial_users` b ON a.user = b.user WHERE  `lead_id` =$lead_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        $call_info = mysql_fetch_assoc($query);
        
        if(preg_match("/^[23]\d\d\d\d\d\d\d\d$/",$lead_info[phone_number])){
            $fixo=$lead_info[phone_number];
            $movel=$lead_info[alt_phone];
        }else{
          $fixo=$lead_info[alt_phone];
            $movel=$lead_info[phone_number];  
        }
        ?>

        <table  class="cc-mstyle full-table">
            <tbody>
                <tr>
                    <td colspan="4" class="left"><img src="energy_icon2.png" /><img src="energy_icon3.png" /></td>
                    <td colspan="1"><h3>REGISTO DE VENDA</h3></td>
                    <td colspan="4" class="right"><b>Em certificação</b><img src="iso2000_.jpg" /><img src="iso14001_.jpg" /></td>
                </tr>

                <tr>
                    <td class="left" colspan="9" ><br/></td>
                </tr>
                <tr>
                    <td COLSPAN=9 HEIGHT="21" class="left" ><B>Dados do Cliente:</B></td>
                </tr>
                <tr>
                    <td HEIGHT="21" colspan="2" class="right" >Nome:</td>
                    <td colspan="7" class="left black-bottom" ><?=$lead_info[first_name]?><br/></td>

                </tr>
                <tr>
                    <td HEIGHT="21" class="right" colspan="2" >BI:</td>
                    <td  class="left black-bottom" colspan="2"><br/></td>
                    <td class="right" ><br/></td>
                    <td class="right" >Nif:</td>
                    <td  class="left black-bottom" colspan="3" ><?=$lead_info[middle_initial]?><br/></td>
 </tr>
                <tr>
                    <td HEIGHT="13" class="right" colspan="9" ><br/></td>

                </tr>
                <tr>
                    <td COLSPAN=9 HEIGHT="21" class="left" ><B>Dados da Factura / Firma / Estabelecimento:</B></td>

                </tr>
                <tr>
                    <td HEIGHT="21" colspan="2" class="right" >Nome:</td>
                    <td  colspan="7" class="left black-bottom" ><?=$lead_info[extra9]?><br/></td>

                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" >Av. / Rua / Praça:</td>
                    <td colspan="7" class="left black-bottom" ><?=$lead_info[address1]?><br/></td>

                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" >Contato Móvel:</td>
                    <td  class="left black-bottom" ><?=$movel?><br/></td>
                    <td  class="right" >Contato Fixo:</td>
                    <td  class="left black-bottom" ><?=$fixo?><br/></td>
                    <td class="right" >Mail:</td>
                    <td class="left black-bottom" colspan="3" ><br/></td>

                </tr>
                <tr>
                    <td HEIGHT="21" colspan="2" class="right" >Nif:</td>
                    <td  class="left black-bottom" colspan="3" ><?=$lead_info[middle_initial]?><br/></td>
                    <td colspan="4" ><br/></td>

                </tr>
                <tr>
                    <td HEIGHT="13" class="left" colspan="9" ><br/></td>

                </tr>
                <tr>
                    <td  HEIGHT="21" class="left" colspan="9" ><B>Morada de Instalação:</B></td>
                </tr>
                <tr>
                    <td COLSPAN=2 HEIGHT="21" class="right" >Av. / Rua / Praça:</td>
                    <td  colspan="7" class="left black-bottom" ><?=$lead_info[address1]?><br/></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" ><br/></td>
                    <td class="right" >Cod. Postal:</td>
                    <td class="left black-bottom" colspan="2" ><?=$lead_info[postal_code]?><br/></td>
                    <td COLSPAN=2 class="right" >Localidade:</td>
                    <td class="left black-bottom" colspan="3" ><?=$lead_info[city]?><br/></td>
                </tr>
                <tr>
                    <td HEIGHT="13" class="left" colspan="9" ><br/></td>
                </tr>
                <tr>
                    <td HEIGHT="21" class="left" colspan="9" ><B>Outras Informações:</B></td>
                </tr>
                <tr>
                    <td COLSPAN=9 HEIGHT="21" ><B>RETIFIC POWER®</B></td>
                </tr>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 HEIGHT="21" ><B>CONSUMO MENSAL:</B></td>
                    <td class="left" ><br/></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 ><B>PRODUTO:</B></td>
                </tr>
                <?php
                $tipo_comprador="";
                if(strlen(trim($lead_info[province]))){
                    $tipo_comprador="Residencial";
                }elseif(strlen(trim($lead_info[email]))){
                    $tipo_comprador="Empresarial";
                }
                
                $tipo_sistema="";
                if(strlen(trim($lead_info[extra1]))){
                    $tipo_sistema="Trifásico";
                }elseif(strlen(trim($lead_info[security_phrase]))){
                    $tipo_sistema="Monofásico";
                }
                
                ?>
                <tr>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=4 ><?=  str_replace("€", "", $lead_info[title])?>€</td>
                    <td class="left" ><br/></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=2 ><B><?=$tipo_comprador?></B></td>
                    <td STYLE="border: 3px solid #000000;" COLSPAN=2 ><B><?=$tipo_sistema?></B></td>
                </tr>
                <tr>
                    <td HEIGHT="13" class="right" colspan="9" ><br/></td>
                    <tr>
                        <td COLSPAN=2 HEIGHT="21" class="right" >Companhia Electrica:</td>
                        <td  class="left black-bottom" colspan="3" ><br/></td>
                        <td COLSPAN=2 class="right" >Potencia Contratada:</td>
                        <td  class="left black-bottom" colspan="2" ><?=$lead_info[extra3]?><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="21" class="right" colspan="2" >CPE* (Cod Ponto Entrega):</td>
                        <td  class="left black-bottom" colspan="7" ><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="13" class="right" colspan="9" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=2 HEIGHT="21" ><B>Referencia</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-left: 3px solid #000000" ><B>Quantidade</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=5 ><B>Designação</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-right: 3px solid #000000" ><B>Valor</B></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=2 HEIGHT="21" ><B>RETIFIC POWER  <?=(strlen(trim($lead_info[vendor_lead_code]))>0)?$lead_info[vendor_lead_code]:"____"?></B></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000" ><br/></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=5 ><B>RETIFIC POWER® <?=(strlen(trim($lead_info[vendor_lead_code]))>0)?$lead_info[vendor_lead_code]:"____"?>Kw</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000; border-right: 3px solid #000000" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=2 HEIGHT="21" ><B>DESL + ADJU + DEM</B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000" ><br/></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=5 ><B>Deslocação + Adjudicação + Demonstração </B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 3px solid #000000" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=2 HEIGHT="21" ><B>INSTALAÇÃO</B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000" ><br/></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=5 ><B>Instalação Técnica</B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-right: 3px solid #000000" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=2 HEIGHT="21" ><B>RELATÓRIO TÉCNICO</B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000" ><br/></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" COLSPAN=5 ><B>Relatório Técnico</B></td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 3px solid #000000; border-right: 3px solid #000000" ><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="21" class="right" colspan="6" ><br/></td>
                        <td ><br/></td>
                        <td ><B>TOTAL:</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" class="left" ><br/></td>
                    </tr>
                    <tr>
                        <td COLSPAN=2 HEIGHT="21" >A quantia de:</td>
                        <td  colspan="7" class="left black-bottom" ><br/></td>
                    </tr>
            </tbody>
        </table>
        <br/>
        <table  class="cc-mstyle full-table">
            <tbody>
                    <tr>
                        <td HEIGHT="13" colspan="9"><br/></td>
                    </tr>
                    <tr STYLE="border: 3px solid #000000;">
                        <td COLSPAN=2 STYLE="border: 3px solid #000000;" BGCOLOR="#BFBFBF" class="right" ><b>Tipo Pagamento:</b></td> 
                        <td HEIGHT="21" BGCOLOR="#BFBFBF" class="left" ><?=ucwords($lead_info[extra5])?></td>
                        <td COLSPAN=2 class="right" BGCOLOR="#BFBFBF" ><B>Nº Prestações:</B></td>
                        <td class="left" BGCOLOR="#BFBFBF" ><?=ucwords($lead_info[extra6])?></td>
                        <td COLSPAN=2 class="right" BGCOLOR="#BFBFBF" ><B>Valor Prestação:</B></td>
                        <td class="left" BGCOLOR="#BFBFBF" ><?=  str_replace("€", "", $lead_info[extra7])?>€</td>
                    </tr>
                    <tr>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=9 HEIGHT="21" ><B>Forma de Pagamento</B></td>
                    </tr>
                    <tr>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 HEIGHT="21" ><B>Dinheiro</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 class="left" ><B></B></td>
                        <td STYLE="border: 3px solid #000000;" ><B>Cheque</B></td>
                        <td class="left" ><B> Nº Prest:</B></td>
                        <td class="left" ><B></B></td>
                        <td class="left" ><B>V. Prest:</B></td>
                        <td STYLE="border-right: 3px solid #000000;" class="left" ><B></B></td>
                    </tr>
                    <tr STYLE="border: 3px solid #000000;">
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 HEIGHT="21" ><B>Multibanco</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 class="left" ><B></B></td>
                        <td STYLE="border: 3px solid #000000;" ><B>Credito</B></td>
                        <td class="left" ><B> Nº Prest:</B></td>
                        <td class="left" ><B></B></td>
                        <td class="left" ><B>V. Prest:</B></td>
                        <td STYLE="border-right: 3px solid #000000;" class="left" ><B></B></td>
                    </tr>
                    <tr>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 HEIGHT="21" ><B>Cartão Credito</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 class="left" ><B></B></td>
                        <td STYLE="border: 3px solid #000000;" ><B>Outros</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=4 class="left" ><B></B></td>
                    </tr>
                    <tr>
                        <td STYLE="border: 3px solid #000000;" HEIGHT="21" ><B>SDD</B></td>
                        <td STYLE="border: 3px solid #000000;" ><B></B></td>
                        <td STYLE="border: 3px solid #000000;" ><B>BANCO</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=2 ><br/></td>
                        <td STYLE="border: 3px solid #000000;" ><B>NIB</B></td>
                        <td STYLE="border: 3px solid #000000;" COLSPAN=3 ><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="13" colspan="9" ><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="21" class="left" colspan="9" ><B>Observações</B></td>
                    </tr>
                    <tr class="blacker">
                        <td rowspan="2" colspan="9" class="left" ><?=$lead_info[comments]?><br/></td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td HEIGHT="13" colspan="9" class="left" ><br/></td>
                    </tr>
                    <tr>
                        <td COLSPAN=5 HEIGHT="21" class="left" ><B>Registo da Venda:</B></td>
                        <td class="left" colspan="4" ><br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="21" class="right" >Hora:</td>
                        <td  class="left black-bottom" ><?=date("H:i",$call_info[start_epoch])?><br/></td>
                        <td class="right" >Dia:</td>
                        <td  class="left black-bottom" ><?=date("d / m / o",$call_info[start_epoch])?><br/></td>
                        <td class="left" ><br/></td>
                        <td class="right" ><B>Comercial</B></td>
                        <td  class="left black-bottom" ><?=ucwords($call_info[full_name])?><br/></td>
                        <td class="right" ><B>Coordenador</B></td>
                        <td  class="left black-bottom" >João Cravo<br/></td>
                    </tr>
                    <tr>
                        <td HEIGHT="13" class="right" ><br/></td>
                        <td class="left" colspan="8" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-top: 3px solid #000000; border-left: 3px solid #000000" COLSPAN=2 HEIGHT="21" class="left" BGCOLOR="#EAEAEA" ><B>INSTALAÇÃO</B></td>
                        <td STYLE="border-top: 3px solid #000000" class="left" BGCOLOR="#EAEAEA" colspan="3" ><br/></td>
                        <td STYLE="border-top: 3px solid #000000" BGCOLOR="#EAEAEA" ><B>Técnico:</B></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000" class="right" colspan="2" BGCOLOR="#EAEAEA" ><br/></td>
                        <td STYLE="border-top: 3px solid #000000; border-bottom: 1px solid #000000; border-right: 3px solid #000000" class="left" BGCOLOR="#EAEAEA" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-left: 3px solid #000000" HEIGHT="21" class="right" BGCOLOR="#EAEAEA" >Dia:</td>
                        <td  class="left black-bottom" BGCOLOR="#EAEAEA" colspan="2" ><br/></td>
                        <td class="right" BGCOLOR="#EAEAEA" >Hora:</td>
                        <td  class="left black-bottom" BGCOLOR="#EAEAEA" colspan="2" ><br/></td>
                        <td class="right" BGCOLOR="#EAEAEA" >Local:</td>
                        <td STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000" class="left" BGCOLOR="#EAEAEA" ><br/></td>
                        <td STYLE="border-bottom: 1px solid #000000; border-right: 3px solid #000000" class="left" BGCOLOR="#EAEAEA" ><br/></td>
                    </tr>
                    <tr>
                        <td STYLE="border-left: 3px solid #000000" COLSPAN=2 HEIGHT="21" BGCOLOR="#EAEAEA" ><B>Pontos Referencia / Obs: </B></td>
                        <td STYLE="border-right: 3px solid #000000; border-bottom: 1px solid #000000" COLSPAN=7 BGCOLOR="#EAEAEA" ></td>
                    </tr>
                    <tr>
                        <td STYLE="border-left: 3px solid #000000; border-right: 3px solid #000000; border-bottom: 1px solid #000000" COLSPAN=9 HEIGHT="21" BGCOLOR="#EAEAEA" ></td>
                    </tr>
                    <tr>
                        <td STYLE="border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000;" COLSPAN=9 HEIGHT="21" BGCOLOR="#EAEAEA" ></td>
                    </tr>
            </tbody>
        </table>
        <BR CLEAR=LEFT>
        <!--<pre>
        <?php var_dump($lead_info)?>
        </pre>-->
        <!-- ************************************************************************** -->
    </body>
</html>