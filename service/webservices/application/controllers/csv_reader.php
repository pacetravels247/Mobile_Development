<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Csv_reader extends CI_Controller
{
	function update_airports()
	{
		$query = 'SELECT CA.city_code as airport_code, CA.city as airport_city,CA.country,CA.offset as timezone_offset FROM flight_airport_list FA
				 right join `city_code_amadeus_test` CA on FA.airport_code=CA.city_code 
				 where FA.airport_code is null ';
		$new_airports = $this->db->query($query)->result_array();
		debug($new_airports);exit;
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
		/*$airport_data = $this->db->query('select origin,airport_code from flight_airport_list')->result_array();
		$airport_list = array();
		foreach($airport_data as $k => $v) {
			$airport_list[$v['airport_code']] = $v['origin'];
		}*/
		include 'excel_reader/PHPExcel.php';//place excel_reader folder in controllers folder
		include 'excel_reader/PHPExcel/IOFactory.php';
	   	$inputFileName = '../extras/flight_airport_list_2016.xls';
	   //$inputFileName = 'http://192.168.0.63/provab/extras/flight_airport_list_2016';
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
		debug($allDataInSheet);exit;
	}
	function delete_airport()
	{
		 for($i=2;$i<=$arrayCount;$i++) {
		 	//Airport Code
		 	$airport_code = trim($allDataInSheet[$i]['B']);
		 	//TimeZones
			$jan_dec_tzo = trim($allDataInSheet[$i]['H']);
			$jan_mar_tzo = trim($allDataInSheet[$i]['I']);
			$apr_sep_tzo = trim($allDataInSheet[$i]['J']);
			$oct_dec_tzo = trim($allDataInSheet[$i]['K']);
			
			if(isset($airport_list[$airport_code]) == true) {
				$flight_airport_list_fk = $airport_list[$airport_code];
				//Jan to Dec
				if(empty($jan_dec_tzo) == false) {
					$jan_dec_tzo = preg_replace('~[^0-9?:+-.!]~','',$jan_dec_tzo);
					$start_month = 1;
					$end_month = 12;
					$timezone_offset = $jan_dec_tzo;
					$query = 'insert into flight_airport_timezone_offset (flight_airport_list_fk,start_month,end_month,timezone_offset) 
							values('.$flight_airport_list_fk.', '.$start_month.', '.$end_month.', "'.$timezone_offset.'")';
					$this->db->query($query);
				}
				//Jan to March
			 	if(empty($jan_mar_tzo) == false) {
					$jan_mar_tzo = preg_replace('~[^0-9?:+-.!]~','',$jan_mar_tzo);
					$start_month = 1;
					$end_month = 3;
					$timezone_offset = $jan_mar_tzo;
					$query = 'insert into flight_airport_timezone_offset (flight_airport_list_fk,start_month,end_month,timezone_offset) 
							values('.$flight_airport_list_fk.', '.$start_month.', '.$end_month.', "'.$timezone_offset.'")';
					$this->db->query($query);
				}
				//Apr to Sept
			 	if(empty($apr_sep_tzo) == false) {
					$apr_sep_tzo = preg_replace('~[^0-9?:+-.!]~','',$apr_sep_tzo);
					$start_month = 4;
					$end_month = 9;
					$timezone_offset = $apr_sep_tzo;
					$query = 'insert into flight_airport_timezone_offset (flight_airport_list_fk,start_month,end_month,timezone_offset) 
							values('.$flight_airport_list_fk.', '.$start_month.', '.$end_month.', "'.$timezone_offset.'")';
					$this->db->query($query);
				}
				//Oct to Dec
			 	if(empty($oct_dec_tzo) == false) {
					$oct_dec_tzo = preg_replace('~[^0-9?:+-.!]~','',$oct_dec_tzo);
					$start_month = 10;
					$end_month = 12;
					$timezone_offset = $oct_dec_tzo;
					$query = 'insert into flight_airport_timezone_offset (flight_airport_list_fk,start_month,end_month,timezone_offset) 
							values('.$flight_airport_list_fk.', '.$start_month.', '.$end_month.', "'.$timezone_offset.'")';
					$this->db->query($query);
				}
		 	}
		}
	}
	function write_into_file()
	{
		include '../extras/excel_reader/PHPExcel.php';//place excel_reader folder in controllers folder
		include '../extras/excel_reader/PHPExcel/IOFactory.php';
		$query = 'SELECT FBTD.pnr, FBTD.book_id,FBTD.total_fare,(FBTD.admin_commission+FBTD.agent_commission) as commission,(FBTD.admin_tds+FBTD.agent_tds) as tds
					from flight_booking_transaction_details FBTD where FBTD.pnr!="" limit 5';
		$flight_booking_details = $this->db->query($query)->result_array();
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		foreach($flight_booking_details as $k => $v) {
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.($k+2), trim($v['pnr']));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.($k+2), trim($v['book_id']));
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.($k+2), (float)$v['total_fare']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.($k+2), (float)$v['commission']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.($k+2), (float)$v['tds']);
		}
		echo 'save';exit;
		$writeFileName = '../extras/accentric_flight_booking_fare_details.xlsx';
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($writeFileName);
		 echo 'Data written into the file';exit;
	}
}
