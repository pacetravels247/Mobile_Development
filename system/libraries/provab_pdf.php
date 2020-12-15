<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * provab
 *
 * Travel Portal Application
 *
 * @package		provab
 * @author		Balu A<balu.provab@gmail.com>
 * @copyright	Copyright (c) 2013 - 2014
 * @link		http://provab.com
 */
require_once("tcpdf/tcpdf.php");

class Provab_Pdf extends TCPDF {
	
   public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false)
   {
		parent::__construct();
	}
	public function Header1() 
	{  
	$image_file = K_PATH_IMAGES.PDF_HEADER_LOGO;
    $this->Image($image_file, 150, 1, 65, '', 'PNG', '', 'T', false, 300, 'R', false, false, 0,     false, false, false);
    }
     public function Footer() {
         // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 12);
        // Page number
		$address = "ticketing@provab.co.in";
        $this->MultiCell(0, 10, $address, 0, 'C');   
      }    
     /**
	 *creating Pdf
	 *@param $data html to be converted to pdf
	 */
	public function create_pdf($data,$view = 'show', $file_name='')
	{
		
            
		if(strcmp($view, 'D')==0){
			if (empty($file_name) == true) {
				$output_pdf = date('d-M-Y') . ".pdf";
			} else {
				$output_pdf = ucwords(str_replace('_', ' ', strtolower($file_name))).'-'.date('d-M-Y') . ".pdf";
			}
		} else if (is_dir ( DOMAIN_PDF_DIR )) {
			if (empty($file_name) == true) {
				$output_pdf = DOMAIN_PDF_DIR . time () . ".pdf";
			} else {
				$output_pdf = DOMAIN_PDF_DIR . $file_name . ".pdf";
			}
		}
		
		$title = domain_name();
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$title = domain_name();
		$pdf->SetTitle($title);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setHeaderData('','',domain_name());
	        $pdf->SetFont('helvetica','', 6,'',false);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetMargins(5, 20);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		
		
		$pdf->AddPage();
              
		//$img = file_get_contents('http://demo.travelomatix.com/extras/custom/wKuTTCM3wqbBLoLkMHp8VIvyA/images/wKuTTCM3wqbBLoLkMHp8VIvyA1417772201.jpeg');
		//$pdf->Image('@' . $img);
		ob_start();
		$content = ob_get_contents();
                
		ob_clean();
		$pdf->writeHTMLCell(0, 0, '', '', $data, 0, 1, 0, true, '', true);
		//$pdf->Output();
		
		if ($view == 'show') {
			$pdf->Output ( $title, 'I' );
			
		} else if(strcmp($view, 'D') == 0){
			$pdf->Output ( $output_pdf, 'D' ); // D F
		} else {
			$pdf->Output ( $output_pdf, 'F' ); // D F
			return $output_pdf;
		}

		/*if(empty($data) == false) {
			if(empty($path) == true) {
				$output_pdf = PDF_PATH.time().".pdf";
			} else {
				$output_pdf = $path.".pdf";
			}*/
		   /* $pdf = new TCPDF();
			$title = domain_name();
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			//$obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);//
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);//
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);//
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetMargins(5, 30);
			//$obj_pdf->SetMargins(5,5,-1,false);//
			$pdf->setFontSubsetting(false);
			$pdf->SetFont('helvetica', '', 11, '', true);
			//$obj_pdf->SetFont('times', 12, true);
			$pdf->AddPage();
			ob_start();
			$content = ob_get_contents();
			ob_end_clean();
			$pdf->writeHTML($data, true, false, true, false, '');
			$pdf->Output($output_pdf, 'F');// D F
			return $output_pdf;*/
			
		//}
	}
}
