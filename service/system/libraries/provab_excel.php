<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * provab
 *
 * Travel Portal Application
 *
 * @package provab
 * @author Sachin J<sachin@provab.com>
 * @copyright Copyright (c) 2017
 * @link http://provab.com
 */
require_once("PHPExcel/Classes/PHPExcel.php");
require_once("PHPExcel/Classes/PHPExcel/IOFactory.php");
class Provab_Excel{

    public $CI; // instance of codeigniter super object

    public function __construct($data = '') {

        $this->CI = & get_instance ();
    }

    /**
     * excel export the data 
     */
    public function excel_export($headings, $fields, $export_data, $excel_sheet_properties) {
        
        // Starting the PHPExcel library
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
 
        $objPHPExcel->setActiveSheetIndex(0);
        current($headings);
        $first_cell_label = key($headings);
        end($headings);
        $last_cell_label = key($headings);

        // set header start cell and end cell 
        $header = ''.$first_cell_label.':'.$last_cell_label.'';
        $excel_work_sheet = $objPHPExcel->getSheet(0);

        // set header value 
        foreach ($headings as $key => $value)
        {
            $excel_work_sheet->setCellValue($key, $value); 
        }

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25); // set header height 

        // auto adjust column width depending on content 
        for ($col = ord($first_cell_label); $col <= ord($last_cell_label); $col++)
        {
            $excel_work_sheet->getColumnDimension(chr($col))->setAutoSize(true);
        }


        // set row content 
        $row = 2;
        $col = 1;
        foreach($export_data as $data)
        {   
            $sl_no = 1; 
            foreach ($fields as $key => $field)
            {   
                if($sl_no == 1){
                    $sl_no = 0; 
                    $objPHPExcel->getActiveSheet()->SetCellValue( $key.''.$row, $col);      
                }else{
                    
                    $content = str_replace(array('<br/>','br/>'),"",$data[$field]); // remove </br> tag
                    $objPHPExcel->getActiveSheet()->SetCellValue( $key.''.$row, $content);  
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
                }               
                
            }

            $col++;
            $row++;
        }      

        // set first column text center 
        $objPHPExcel->getActiveSheet()->getStyle('A2:A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  

        // set header color 
        $excel_work_sheet->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
        
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
            );
        $excel_work_sheet->getStyle($header)->applyFromArray($style); 

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');

        // set excel sheet properties 
        $objPHPExcel->getProperties()
           ->setCreator($excel_sheet_properties['creator'])
           ->setTitle($excel_sheet_properties['title'])
           ->setDescription($excel_sheet_properties['description'])
           ->setCategory('programming')
           ;

        // set sheet title 
        $excel_work_sheet->setTitle($excel_sheet_properties['sheet_title']);        

        // set header content type as excel and content deposition as attachment 
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$excel_sheet_properties['title'].'.xls"');
        header('Cache-Control: max-age=0');
 
        $objWriter->save('php://output');
        
    }
    
}
?>
