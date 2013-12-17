<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of excelwraper
 *
 * @author pedro.pedroso
 */
class excelwraper {

    protected $phpexcel;
    protected $options = array();
    protected $letter = 'B', $number = 1;
    protected $writeExcel;
    protected $dataseriesLabels,
            $xAxisTickValues,
            $dataSeriesValues;
    protected  $axis=array(
        "r"=>'PHPExcel_Chart_Legend::POSITION_RIGHT',
        "l"=>"PHPExcel_Chart_Legend::POSITION_LEFT",
        "b"=>"PHPExcel_Chart_Legend::POSITION_TOPRIGHT",
        "t"=>"PHPExcel_Chart_Legend::POSITION_TOPRIGHT",
        "tr"=>"PHPExcel_Chart_Legend::POSITION_TOPRIGHT"
    );

    public function __construct($excel, $zeroSheet) {


        $this->phpexcel = $excel;
        $this->phpexcel->getActiveSheet()->setTitle($zeroSheet);
    }

    public function maketable($data) {


        $this->phpexcel->getActiveSheet()->fromArray($data, NULL, 'A' . (string) $this->number);
    }

    public function makegraph($title, $yLabel, $charName, $legendPosition) {
        var_dump(constant($this->axis[$legendPosition]));
        exit();

        $activeSheet = $this->phpexcel->getActiveSheet();

        $this->dataSeriesValues();

        $this->xAxisTickValues();

        $this->battlesheetcol();
        //	Build the dataseries
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
                PHPExcel_Chart_DataSeries::GROUPING_STACKED, // plotGrouping
                range(0, count($this->dataSeriesValues) - 1), // plotOrder
                $this->dataseriesLabels, // plotLabel
                $this->xAxisTickValues, // plotCategory
                $this->dataSeriesValues        // plotValues
        );


//	Set the series in the plot area
        $plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));

        //	Create the chart
        $chart = new PHPExcel_Chart(
                $charName, // name
                new PHPExcel_Chart_Title($title), // title
                new PHPExcel_Chart_Legend(constant($this->axis[$legendPosition]), NULL, false), // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                NULL, // xAxisLabel
                new PHPExcel_Chart_Title($yLabel)  // yAxisLabel
        );


        //	Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition($this->letter . $this->number);
        $this->battlesheetrow();
        $chart->setBottomRightPosition('Q' . $this->number);
        $this->number +=5;


        //	Add the chart to the worksheet

        $activeSheet->addChart($chart);
    }

    protected function dataSeriesValues() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($col = $this->letter; $activeSheet->getCell($col . '' . ($this->number + 1))->getValue() != NULL; $col++) {
            for ($row = $this->number; $activeSheet->getCell($col . $row)->getValue() != NULL; $row++) {
                
            }
            $this->dataSeriesValues [] = new PHPExcel_Chart_DataSeriesValues('Number', $this->phpexcel->getActiveSheet()->getTitle() . '!$' . $col . '$' . ($this->number + 1) . ':$' . $col . '$' . $row, NULL);
        }
    }

    protected function xAxisTickValues() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($row = $this->number; $activeSheet->getCell('A' . ($row + 1))->getValue() != NULL; $row++) {
            
        }
        $this->xAxisTickValues [] = new PHPExcel_Chart_DataSeriesValues('String', $this->phpexcel->getActiveSheet()->getTitle() . '!$A$' . $this->number . ':$A$' . $row, NULL); //dias da semana
    }

    protected function battlesheetrow() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($row = $this->number; $activeSheet->getCell('B' . $row)->getValue() != NULL; $row++) {
            
        }

        $this->number = $row;
    }

    protected function battlesheetcol() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($col = $this->letter; $activeSheet->getCell($col . '' . $this->number)->getValue() != NULL; $col++) {

            $this->dataseriesLabels [] = new PHPExcel_Chart_DataSeriesValues('String', $this->phpexcel->getActiveSheet()->getTitle() . '!$' . $col . '$' . $this->number, NULL);
        }

        //var_dump($this->dataseriesLabels);
        // exit();

        $this->letter = $col;
    }

    public function addsheet($title) {



        $this->phpexcel->addSheet(new PHPExcel_Worksheet($this->phpexcel, $title));

        $this->phpexcel->setActiveSheetIndexByName($title);
    }

    public function selectsheet($nr) {
        $this->phpexcel->setActiveSheetIndex($nr);
    }

    public function save($name, $includeCharts) {

        $this->writeExcel = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $this->writeExcel->setIncludeCharts($includeCharts);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$name.xlsx");
        header('Content-Transfer-Encoding: binary');
    }

    public function send() {

        $this->writeExcel->save('php://output');
    }

}
