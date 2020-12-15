<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Csv_reader extends CI_Controller
{
	function update_airports()
	{
		$query = 'SELECT CA.city_code as airport_code, CA.city as airport_city,CA.country,CA.offset as timezone_offset FROM flight_airport_list FA
				 right join `city_code_amadeus_test` CA on FA.airport_code=CA.city_code 
				 where FA.airport_code is null ';
		$new_airports = $this->db->query($query)->result_array();
		
		foreach($new_airports as $k => $v){
			//1.Adding Airport
			$flight_airport_list = array();
			$flight_airport_list['airport_code'] = trim($v['airport_code']);
			$flight_airport_list['airport_city'] = trim($v['airport_city']);
			$flight_airport_list['airport_name'] = trim($v['airport_city']);
			$flight_airport_list['country'] = trim($v['country']);
			$timezone_offset = trim($v['timezone_offset']);
			$insert_id = $this->custom_db->insert_record('flight_airport_lit', $flight_airport_list);
			//2.Updating Time Zone offset
			$timezone_offset = preg_replace('~[^0-9?:+-.!]~','',$timezone_offset);
			$flight_airport_list_fk = $insert_id['insert_id'];
			$start_month = 1;
			$end_month = 12;
			$query = 'insert into flight_airport_timezone_offse (flight_airport_list_fk,start_month,end_month,timezone_offset) 
					values('.$flight_airport_list_fk.', '.$start_month.', '.$end_month.', "'.$timezone_offset.'")';
			$this->db->query($query);
		}
		echo 'updated';
	}
	
	function parse_file()
	{	
		include '../extras/excel_reader/PHPExcel.php';//place excel_reader folder in controllers folder
		include '../extras/excel_reader/PHPExcel/IOFactory.php';
	   	$inputFileName = '../extras/AirlineCode.xls';
	   //$inputFileName = 'http://192.168.0.63/provab/extras/flight_airport_list_2016';
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
		
		foreach ($allDataInSheet as $k => $v){
			if($k > 1){
				$code = trim($v['A']);
				$name = trim($v['B']);
				$airline_data = $this->db->query('select code from airline_list where code="'.$code.'"')->row_array();
				if(valid_array($airline_data) == false){
					$airline_insert_data = array();
					$airline_insert_data['name'] = $name;
					$airline_insert_data['code'] = $code;
					$this->custom_db->insert_record('airline_lis', $airline_insert_data);
				}
			}
		}
		echo 'Added';exit;
	}	
	function import_flight_booking_data()
	{
		include '../extras/excel_reader/PHPExcel.php';//place excel_reader folder in controllers folder
		include '../extras/excel_reader/PHPExcel/IOFactory.php';
		$query = 'SELECT FBTD.pnr, FBTD.book_id,(FBTD.admin_commission+FBTD.agent_commission) as commission,(FBTD.admin_tds+FBTD.agent_tds) as tds
					from flight_booking_transaction_details FBTD where FBTD.pnr!=""';
		$flight_booking_details = $this->db->query($query)->result_array();
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		foreach($flight_booking_details as $k => $v) {
			$admin_base_currency = admin_base_currency();
			if($admin_base_currency != 'INR'){
				echo 'different currency';exit;
				$commission =	floatval($v['commission'])*(67.06);
				$tds = 			floatval($v['tds'])*(67.06);
				echo $commission.'||'.$tds;exit;
			} else {
				$commission =	floatval($v['commission']);
				$tds = 			floatval($v['tds']);
			}
			$commission =	round($v['commission'], 4);
			$tds = 			round($v['tds'], 4);
				
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.($k+2), trim($v['pnr']));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.($k+2), trim($v['book_id']));
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.($k+2), $commission);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.($k+2), $tds);
		}
		$writeFileName = '../extras/accentric_flight_booking_fare_details.xlsx';
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($writeFileName);
		$objPHPExcel->disconnectWorksheets();
		unset($objWriter, $objPHPExcel);
		 echo 'Data written into the file';exit;
	}
}
