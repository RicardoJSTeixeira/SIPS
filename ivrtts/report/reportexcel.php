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
    //define a time Zone
    date_default_timezone_set('Europe/London');

    //inclui o ficheiro PHPExcel
    require_once '../../ini/phpexcel/PHPExcel.php';

    //Objecto do tipo PHPExcel
    $toExcel = New PHPExcel();
    // Create a new worksheet called “My Data”
    $rawdata = new PHPExcel_Worksheet($toExcel, 'rawdata');
    // Attach the “My Data” worksheet as the first worksheet in the PHPExcel object
    $toExcel->addSheet($rawdata, 1);
    
    $sheet = $toExcel->getActiveSheet(0);

    //titulo do workbook do excel

    $sheet->setTitle("Report");
    //estilo
    //Background
    $backGround = array('fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'f9f9f9')
        ),
        'borders' => array(
            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
        )
    );
    $toExcel->getActiveSheet()->getStyle('A1:Q60')->applyFromArray($backGround);
    //dummy data 
   
    $toExcel->getSheet(1)->fromArray(
            array(
                array('', 'Total Chamadas', 'Total Úteis'),
                array('Monday', 12, 15),
                array('Thuesday', 56, 73),
                array('Wednesday', 52, 61),
                array('Thursday', 30, 32),
                array('Friday', 60, 32),
                array('Saturday', 47, 77),
                array('sunday', 15, 18),
            )
    );

    //insere a informacao(VALUES) de cada o objecto na sheet
    //$sheet->fromArray( $data, NULL, 'A1');



    $dataseriesLabels = array(
        new PHPExcel_Chart_DataSeriesValues('String', 'rawdata!$B$1', NULL, 1), //	Total chamadas
        new PHPExcel_Chart_DataSeriesValues('String', 'rawdata!$C$1', NULL, 1), //	Total Uteis
    );


    $xAxisTickValues = array(
        new PHPExcel_Chart_DataSeriesValues('String', 'rawdata!$A$2:$A$8', NULL, 8), //	Q1 to Q4
    );


    $dataSeriesValues = array(
        new PHPExcel_Chart_DataSeriesValues('Number', 'rawdata!$B$2:$B$8', NULL, 8),
        new PHPExcel_Chart_DataSeriesValues('Number', 'rawdata!$C$2:$C$8', NULL, 8),
    );



//	Build the dataseries
    $series = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
            PHPExcel_Chart_DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataseriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
    );

    
    
    

//	Set the series in the plot area
    $plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
//	Set the chart legend
    $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_TOPRIGHT, NULL, false);

    $title = new PHPExcel_Chart_Title('Totais+');
    $yAxisLabel = new PHPExcel_Chart_Title('Totais');


   $topr = 1;
   $botl = 22;        
  // for($i=0;$i < 4 ;$i++){
       
       //	Create the chart
    $chart = new PHPExcel_Chart(
            'chart', // name
            $title, // title
            $legend, // legend
            $plotarea, // plotArea
            true, // plotVisibleOnly
            0, // displayBlanksAs
            NULL, // xAxisLabel
            $yAxisLabel  // yAxisLabel
            
    );
    
    //	Set the position where the chart should appear in the worksheet
    $chart->setTopLeftPosition('A'.$topr+22);
    $chart->setBottomRightPosition('Q'.$botl+22);
    //	Add the chart to the worksheet
    $sheet->addChart($chart);
 //  }






    //cor do fundo
    //enviar ficheiro para o browser "save"

    $writeExcel = PHPExcel_IOFactory::createWriter($toExcel, 'Excel2007');
    $writeExcel->setIncludeCharts(TRUE);
    //header para o browser
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=Report.xlsx");
    header("Content-Transfer-Encoding: binary");
    ob_end_clean();
    //envia para o brower
    $writeExcel->save('php://output');
} else {
    
}  