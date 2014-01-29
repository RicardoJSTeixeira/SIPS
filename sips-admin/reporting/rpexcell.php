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

    //Vai buscar informção ao ficheiro json 
    $fileData = file_get_contents('./rphandle.json');
    //Converte para php, se tiver a true converte num array
    $data = json_decode($fileData, true);

    //inclui o ficheiro PHPExcel
    require_once '../../ini/phpexcel/PHPExcel.php';

    //Objecto do tipo PHPExcel
    $toExcel = New PHPExcel();
    
    $sheet = $toExcel->getActiveSheet(0);
 
    //titulo do workbook do excel
    $sheet->setTitle("Report");

    //array com as definições de estilo do Conteúdo das células do excell
    //values
    $styleArrayVal = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => 'fa603d'),
            'size' => 10,
            'name' => 'Verdana'
    ));
    //names
    $styleArrayWord = array(
        'font' => array(
            'bold' => FALSE,
            'color' => array('rgb' => '2b2b2b'),
            'size' => 12,
            'name' => 'Verdana'
    ));
    //perc%
    $styleArrayPerc = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '2b2b2b'),
            'size' => 10,
            'name' => 'Verdana'
    ));

    //Background
    $backGround = array('fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'FFFFFF')
        ),
        'borders' => array(
            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
        )
    );
    $board = array(
        'borders' => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
        )
    );

   /* //logo do cliente na report sheet
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setPath('./graph/logo.png');
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setCoordinates('A1');
    $objDrawing->setOffsetX(1);
    $objDrawing->setOffsetY(1);
    $objDrawing->setHeight(120);
    $objDrawing->setWorksheet($toExcel->getActiveSheet());
*/
    //define o estilo da row com o array de estilo($styleArray)
    // $sheet->getStyle('A1:' . $col . '1')->applyFromArray($styleArray);
    //var de coordenada do chart
    $lp = 1;
    //codigo achii A=65 Z=95
    $char=80;
    $count = 10;
    //
    foreach ($data as $value) {
        //cria a tebela comentar mais tarde
        //
        //mete a negrito
        $sheet->getStyle('A' . $count . ':C' . $count)->getFont()->setBold(true);
        ////insere a informacao de cada o objecto na sheet
        $sheet->fromArray(array($value["name"], $value["total"], $value["perc"]), NULL, 'A' . $count);

        $xAxisTickValues[] = new PHPExcel_Chart_DataSeriesValues('String', 'Report!$A$' . $count, NULL, 1);
        $count++;

        //insere a informacao(VALUES) de cada o objecto na sheet
        $sheet->fromArray($value["values"], NULL, 'A' . $count);

        //cria a grafico comentar mais tarde 

        for ($row = $count; $sheet->getCell('A' . $row)->getValue() != NULL; $row++) {


            
            //nome de cada barra no chart
            $dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', 'Report!$A$' . $row, NULL, 1);


            //valor de cada barra no chart
            $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', 'Report!$B$' . $row, NULL, 1);

            
            //aplicar estilo a celulas
            $sheet->getStyle('B' . $row)->applyFromArray($styleArrayVal);

            $sheet->getStyle('A' . $row)->applyFromArray($styleArrayWord);

            $sheet->getStyle('C' . $row)->applyFromArray($styleArrayPerc);
            
            
        }

        //estilo do workbook 
        $toExcel->getActiveSheet()->getStyle('A' . $count . ':C' . ($row - 1))->applyFromArray($board);


        //	Build the dataseries
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D, // plotType
                PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
                range(0, count($dataSeriesValues) - 1), // plotOrder
                $dataseriesLabels, // plotLabel
                $xAxisTickValues, // plotCategory
                $dataSeriesValues        // plotValues
        );
        //	Set additional dataseries parameters
        //		Make it a vertical column rather than a horizontal bar graph
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);

        //	Set the series in the plot area
        $plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));

        //	Set the chart legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //cria o chart
        $chart = new PHPExcel_Chart(
                'Graph' . $count, // name
                NULL, // title
                $legend, // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                NULL, // xAxisLabel
                NULL  // yAxisLabel
        );

        //coordenada onde o chart deve aparecer
        $chart->setTopLeftPosition('D' . $lp);

        $rp = $count;
        $rp+=(count($value["values"]) + 8);
        
        $chart->setBottomRightPosition(chr($char) . $rp);

        $count+= count($value["values"]) + 15;
        $lp = $count - 2;
        //adiciona o chart a sheet
        $sheet->addChart($chart);

        //limpa os array com a informação de cada grafico, depois de adicionado a sheet
        unset($xAxisTickValues);
        unset($dataseriesLabels);
        unset($dataSeriesValues);        
    }

   /* //dog tag da fine source
    $gdImage = @imagecreatetruecolor(120, 20) or die('Cannot Initialize new GD image stream');
    $textColor = imagecolorallocate($gdImage, 255, 255, 255);
    imagestring($gdImage, 1, 5, 5, 'Powered by:FineSource', $textColor);

    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Sample image');
    $objDrawing->setDescription('Sample image');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(32);
    $objDrawing->setCoordinates('A' . ($count - 2));
*/
    $objDrawing->setWorksheet($toExcel->getActiveSheet());
    //no phpexcel o autosize e feito de coluna a coluna.
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    //cor do fundo
    $toExcel->getActiveSheet()->getStyle('A1:'.chr($char) . $count)->applyFromArray($backGround);
    
  
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
} else {} 