<?php

//ob_start();
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

    $campaign_id = 'W00003';

    
    
    $dataTotal = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");
    
    $dataTotalPie = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");

    $dataTotalHora = file_get_contents("http://localhost:10000/ccstats/v0/sum/calls/length_in_sec?by=database.campaign,status&database.campaign.oid=$campaign_id");
    
    $datalinha1 =file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/count/calls?by=database.campaign,".implode($tempo,',').",status&database.campaign.oid=$id");
            
    $datalinha2 =file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/avg/calls/length_in_sec?by=database.campaign,status,".implode($tempo,',')."&database.campaign.oid=$id");

    $dataTotal = json_decode($dataTotal, true);

    
    $dataTotalHora = json_decode($dataTotalHora, true);
    
   
    $toExcel = new excelwraper(New PHPExcel(), "report");

    
    foreach ($dataTotal as $value) {

        // $p['total']+=$value['count'];//total de chamadas da campanha

       
        switch ($value['_id']['status']['oid']) {
            case "MSG001":
            case "MSG002":
            case "MSG003":
            case "MSG004":
            case "MSG005":
            case "MSG006":
            case "MSG007":
            case "NEW":
                $p[$value['_id']['status']['designation']] = $value['count'];

                break;

            default :
                $p['outros']+= $value['count'];
                break;
        }
    }
    
    
    if ($p['NEW'] === NULL) {

        $p['NEW'] = 'n\a';
    }
    
       $dataExcel = array(
        array('', 'total'),
        array('outros', $p['outros']),
        array('New', $p['NEW']),

    );
    

    $toExcel->maketable( $dataExcel);

    $toExcel->makegraph("title", '', "chart1", "r", 'bars', 'bars', TRUE, TRUE);
    
    unset($p);
    unset($dataExcel);
    foreach ($dataTotalHora as $value) {

        // $p['total']+=$value['count'];//total de chamadas da campanha

       
        switch ($value['_id']['status']['oid']) {
            case "MSG001":
            case "MSG002":
            case "MSG003":
            case "MSG004":
            case "MSG005":
            case "MSG006":
            case "MSG007":
            case "NEW":
                $p[$value['_id']['status']['designation']] = $value['count'];

                break;

            default :
                $p['outros']+= round(($value['sum']/3600));
                break;
        }
    }
    
   
    
    if ($p['NEW'] === NULL) {

        $p['NEW'] = 'n\a';
    }
    
       $dataExcel = array(
        array('', 'total'),
        array('outros', $p['outros']),
        array('New', $p['NEW']),

    );
    
    
    $toExcel->maketable($dataExcel);

    $toExcel->makegraph("title", '', "chart2", "r", 'bars', 'bars', TRUE, TRUE);
    
    unset($p);
    unset($dataExcel);
    
    foreach ($dataTotalpie as $value) {

        // $p['total']+=$value['count'];//total de chamadas da campanha

       
        switch ($value['_id']['status']['oid']) {
            case "MSG001":
            case "MSG002":
            case "MSG003":
            case "MSG004":
            case "MSG005":
            case "MSG006":
            case "MSG007":
            case "NEW":
                $p[$value['_id']['status']['designation']] = $value['count'];

                break;

            default :
                $p['outros']+= $value['count'];
                break;
        }
    }
    
   
    
    if ($p['NEW'] === NULL) {

        $p['NEW'] = 'n\a';
    }
    
       $dataExcel = array(
        array('', 'total'),
        array('outros', $p['outros']),
        array('New', $p['NEW']),

    );
    
   
    
    $toExcel->maketable($dataExcel);

    $toExcel->makegraph("title",'',"chart2",'t','pie','pie',TRUE,TRUE);
    
    /*
      $toExcel->makegraph("title",'',"chart0","r",'lines','lines',TRUE,TRUE);

      $toExcel->maketable($data2);

      $toExcel->makegraph("title",'',"chart1","r",'lines','lines',TRUE,TRUE);

      $toExcel->maketable($data);

      $toExcel->makegraph("title",'',"chart2","r",'bars','bars',TRUE,TRUE);


      $toExcel->maketable($data2);

      $toExcel->makegraph("title",'',"chart3","r",'bars','bars',TRUE,TRUE);


      $toExcel->maketable($data3);

      $toExcel->makegraph("title",'',"chart4",'t','pie','pie',TRUE,TRUE);
     */
    $toExcel->backGroundStyle('FFFFFF');

    $toExcel->save('Report', TRUE);
    //ob_end_clean();
    $toExcel->send();
} else {
    
}  

