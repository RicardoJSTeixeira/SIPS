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
    protected $currentSheet;
    protected $currentSheetNr;
    protected $options = array();
    protected $letter = 'A', $number = 1;

    public function __construct($excel, $options) {
         $this->phpexcel = $excel;
    }

    protected function maketable($data) {


        $this->fromArray($data, NULL, 'A' . $number);
    }

    protected function battleshiprow() {

        for ($row = $number; $this->getCell('A' . $row)->getValue() != NULL; $row++) {
            
        }
        $number = $row;
    }

    protected function battleshipcol() {

        for ($col = $letter; $this->getCell($col . '' . $number)->getValue() != NULL; $col++) {
            
        }
        $letter = $col;
    }

    public function addsheet($title) {
        $this->phpexcel->addSheet(new PHPExcel_Worksheet($this, $title));
        $this->currentSheet=$this->phpexcel->getActiveSheet();
    }

    public function selectsheet($nr) {
        $this->currentSheet=$this->phpexcel->getActiveSheet($nr);
    }
    
    public function save($name) {
        $writeExcel = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
    $writeExcel->setIncludeCharts(TRUE);
    
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
        $writeExcel->save('php://output');
    }

}
