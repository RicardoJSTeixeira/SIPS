<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>
    
<div class="cc-mstyle">
    <table>
        <tr>
            <td id="icon32"><img src="images/am-icon.jpg" /></td>
            <td id='submenu-title'> Relatórios Acustica Médica </td>
            <td style='text-align:left'></td>
        </tr>
    </table> 
</div>
    
<div id="work-area">
<br>
<br>
        
    <div class="cc-mstyle" style="border:none">
    <table border=1>
    <tr><td style="width:50%"><b>Campanhas</b></td><td style="width:50%"><b>Bases de Dados</b></td></tr>
    </table>    
    <br>    
    <table border=1>
    <tr><td style="width:50%">
    
    <table>
    <tr><td id='icon32'><img onclick="window.location='bycampaign/am/index.php'" src='images/chart_stock_32.png' /></td><td style="text-align:left">Report AM</td></tr>
    </table>
    
    </td><td style="width:50%"></td></tr>
    
   
    
    
    <tr><td style="width:50%">
    
    <table>
    <tr><td id='icon32'><img onclick="window.location='bycampaign/operador/index.php'" src='images/chart_stock_32.png' /></td><td style="text-align:left">Feedbacks por Operadores</td></tr>
    </table>
    
    </td><td style="width:50%"></td></tr>
    
    
    </table>    
        
        
        
        <table border=0>
                <tr onclick="window.location='reports_am_geral_camp.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Report AM Marcações e Novos Clientes por Campanha - ACABADO</td>
                
            </tr>
                <tr onclick="window.location='reports_am_geral_db.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Report Geral AM por Base de Dados - EM REVISÃO</td>
                
            </tr>
            <tr><td>&nbsp;</td></tr>
                <tr onclick="window.location='reports_am_total_camps.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Total de Feedbacks de Todas as Campanhas - EM REVISÃO</td>
                
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr onclick="window.location='reports_am_s_feeds_camps.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Total de Feedbacks por Campanha - EM REVISÃO</td>
                
            </tr>
        
            <tr onclick="window.location='reports_am_s_feeds_dbs.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Total de Feedbacks por Base de Dados - EM REVISÃO</td>
                
            </tr>
            <tr><td>&nbsp;</td></tr>
                <tr onclick="window.location='reports_am_t_feeds_camps.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'>Total de Feedbacks por Campanha e Operador - EM REVISÃO</td>
                
            </tr>
            <tr onclick="window.location='reports_am_t_feeds_dbs.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Total de Feedbacks por Base de Dados e Operador - EM REVISÃO</td>
                
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr onclick="window.location='reports_am_resumo_camp.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Resumo Geral por Campanha - EM REVISÃO</td>
                
            </tr>

            <tr onclick="window.location='reports_am_resumo_db.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> Resumo Geral por Base de Dados - EM REVISÃO</td>
                
            </tr>
            
            <tr onclick="window.location='teste_reports.php'">
                
                <td id='icon32'><img src='../images/icons/document_move_32.png' /></td>
                
                <td style='text-align:left; cursor:pointer;'> TESTES REPORT GERAL </td>
                
            </tr>
            

            
        
        </table>
        
        
        
    </div>    
    </div>
    
    
    
    
<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>