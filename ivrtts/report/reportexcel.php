<?php

ob_start();
//vai dissecar a váriaveis  que vêm do Post e Get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

//PHPExcel/Classes/Writer/Excel2007/Chart.php file ficheiro alterado(linha 109 -119)hardcoded
//caso a variavel seja 1 entra no if e devolve o excell,se não:exit;
if ($key === 1) {
    //inclui o ficheiro PHPExcel
    require '../../ini/phpexcel/PHPExcel.php';

    require("./excelwraper.php");



    $data = array(
        array('', 'Total Chamadas', 'Total Úteis', 'Total Chamadas', 'Total Úteis'),
        array('Monday', 87, 15, 46, 25),
        array('Thuesday', 56, 73, 34, 13),
        array('Wednesday', 52, 61, 25, 26),
        array('Thursday', 30, 32, 25, 78),
        array('Friday', 60, 32, 54, 37),
        array('Saturday', 47, 77, 24, 26),
        array('sunday', 15, 18, 26, 37),
    );


    $toExcel = new excelwraper(New PHPExcel(), "report");

    $toExcel->maketable($data);
    
    $toExcel->makegraph("title","legenda","chart1","l");
    
    $toExcel->maketable($data);
    
    
    $toExcel->makegraph("title","legenda","chart1",'l');
    
    $toExcel->maketable($data);
    
    
    $toExcel->makegraph("title","legenda","chart2",'l');
    
    $toExcel->maketable($data);
    
    
    $toExcel->makegraph("title","legenda","chart3",'l');
    
    
    $toExcel->save('Report',TRUE);
    ob_end_clean();
    $toExcel->send();
} else {
    
}  
