<?php
class excelwraper {

    protected $phpexcel;
    protected $options = array();
    protected $letter = 66, $number = 10;
    protected $graphSize=0;
    protected $writeExcel;
    protected $dataseriesLabels,
            $dataSeriesValues;
    protected $axis = array(
        'r' => 'PHPExcel_Chart_Legend::POSITION_RIGHT',
        'l' => 'PHPExcel_Chart_Legend::POSITION_LEFT',
        'b' => 'PHPExcel_Chart_Legend::POSITION_TOPRIGHT',
        't' => 'PHPExcel_Chart_Legend::POSITION_TOPRIGHT',
        'tr' => 'PHPExcel_Chart_Legend::POSITION_TOPRIGHT'
    );
    protected $graphType = array(
        'bars' => 'PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D',
        'lines' => 'PHPExcel_Chart_DataSeries::TYPE_LINECHART_3D',      
        'pie' => 'PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D'
    );
    protected $graphGrouping = array(
        'bars' => 'PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED',
        'lines' => 'PHPExcel_Chart_DataSeries::GROUPING_STANDARD',
        'pie' => 'PHPExcel_Chart_DataSeries::GROUPING_STANDARD'
    );
    protected $board = array(
        'borders' => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
            'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
        )
    );

    public function __construct($excel, $zeroSheet) {

        $this->phpexcel = $excel;
        $this->phpexcel->getActiveSheet()->setTitle($zeroSheet);
    }

    public function maketable($data) {


        $this->phpexcel->getActiveSheet()->fromArray($data, '0', 'A' .$this->number);

        $this->autoSizeCol();
        $this->tableBoardBolt();
    }

    protected function tableBoardBolt() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($col = 65; $activeSheet->getCell(chr($col) . '' . ($this->number ))->getValue() != NULL; $col++) {
           
            for ($row = $this->number; $activeSheet->getCell(chr($col) . $row)->getValue() != NULL; $row++) {
                
                
                $activeSheet->getStyle('A' . $this->number . ':'.chr($col) . ($row) )->applyFromArray($this->board);
                
            }
            
           
            
            $activeSheet->getStyle('A' . ($this->number + 1). ':A' . ($row - 1))->getFont()->setBold(true);
            
            
        }
        
  
         
        
       //var_dump('A' . $this->number . ':'.chr($col-1) . ($row-1) );
        
        $activeSheet->getStyle(chr($this->letter) . $this->number . ':'.chr($col) . $this->number)->getFont()->setBold(true);
        
    }
    

    protected function autoSizeCol() {

        $activeSheet = $this->phpexcel->getActiveSheet();


        for ($col = 66; $activeSheet->getCell(chr($col) . '' . ($this->number + 1))->getValue() != NULL; $col++) {

            $activeSheet->getColumnDimension(chr($col))->setAutoSize(true);
        }
    }

    public function makegraph($title, $yLabel,$xLabel, $charName, $legendPosition, $graphType, $graphGrouping,$ShowVal,$ShowPerc) {
        

        $activeSheet = $this->phpexcel->getActiveSheet();



        //	Build the dataseries
        $series = new PHPExcel_Chart_DataSeries(
                constant($this->graphType[$graphType]), // plotType
                constant($this->graphGrouping[$graphGrouping]), // plotGrouping
                range(0, count($this->dataSeriesValues()) - 1), // plotOrder
                $this->dataseriesLabels(), // plotLabel
              $this->xAxisTickValues(), // plotCategory
                $this->dataSeriesValues()  // plotValues
               
                
        );
       
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
  
//	Set the series in the plot area
        $plotarea = new PHPExcel_Chart_PlotArea($layout1 = new PHPExcel_Chart_Layout(), array($series));
        
         $layout1->setShowVal($ShowVal);
         $layout1->setShowPercent($ShowPerc);
         
        //	Create the chart
        $chart = new PHPExcel_Chart(
                $charName, // name
                new PHPExcel_Chart_Title($title), // title
                new PHPExcel_Chart_Legend(constant($this->axis[$legendPosition]), NULL, false), // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                new PHPExcel_Chart_Title($xLabel), // xAxisLabel
                new PHPExcel_Chart_Title($yLabel)  // yAxisLabel
        );


        //	Set the position where the chart should appear in the worksheet

        $chart->setTopLeftPosition($this->battlesheetcol() . ($this->number-8));
        $this->battlesheetrow();
        $chart->setBottomRightPosition('X' . ($this->number+8));
     
        $this->number +=18;
        
        //	Add the chart to the worksheet
        $activeSheet->addChart($chart);
         
    }
     
    protected function dataseriesLabels() {

        $activeSheet = $this->phpexcel->getActiveSheet();


        for ($col = $this->letter; $activeSheet->getCell(chr($col) . '' . $this->number)->getValue() != NULL; $col++) {

            $dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', $this->phpexcel->getActiveSheet()->getTitle() . '!$' . chr($col) . '$' . $this->number, NULL);
        }
        
        return $dataseriesLabels;
        
    }
    
    protected function dataSeriesValues() {
        $activeSheet = $this->phpexcel->getActiveSheet();



        for ($col = $this->letter; $activeSheet->getCell(chr($col) . '' . ($this->number + 1))->getValue() != NULL; $col++) {
            for ($row = $this->number; $activeSheet->getCell(chr($col) . $row)->getValue() != NULL; $row++) {
                
            }


            $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', $this->phpexcel->getActiveSheet()->getTitle() . '!$' . chr($col) . '$' . ($this->number + 1) . ':$' . chr($col) . '$' . ($row - 1), NULL);
        }
        
        return $dataSeriesValues;
        
    }

    protected function xAxisTickValues() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($row = ($this->number + 1); $activeSheet->getCell('A' . $row)->getValue() != NULL; $row++) {
            
        }

        return array(new PHPExcel_Chart_DataSeriesValues('String', $this->phpexcel->getActiveSheet()->getTitle() . '!$A$' . ($this->number + 1) . ':$A$' . ($row - 1), NULL)); //dias da semana
    }

    protected function battlesheetrow() {
        $activeSheet = $this->phpexcel->getActiveSheet();

        for ($row = $this->number; $activeSheet->getCell('B' . $row)->getValue() != NULL; $row++) {
                    
        }
              
        $this->number = $row;
    }

    protected function battlesheetcol() {
        $activeSheet = $this->phpexcel->getActiveSheet();


        for ($col = $this->letter; $activeSheet->getCell(chr($col) . '' . $this->number)->getValue() != NULL; $col++) {
            
        }
        return chr($col);
    }

    public function addsheet($title) {

        $this->phpexcel->addSheet(new PHPExcel_Worksheet($this->phpexcel, $title));

        $this->phpexcel->setActiveSheetIndexByName($title);
    }

    public function selectsheet($nr) {
        $this->phpexcel->setActiveSheetIndex($nr);
    }

    public function backGroundStyle($color) {

        $activeSheet = $this->phpexcel->getActiveSheet();



        $backGround = array('fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => $color)
            ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
        );


        $activeSheet->getStyle('A1:X' . $this->number)->applyFromArray($backGround);
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