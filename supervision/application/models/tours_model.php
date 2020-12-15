<?php

class tours_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
	}

	function tour_booking_report($condition = array(), $count = false, $offset = 0, $limit = 100000000000) {
		$response ['status'] = SUCCESS_STATUS;
		$response ['data'] = array ();		
		if (! empty ( $condition )) {
			$condition_static = 'TB.status="PENDING" ';
			$condition = $condition_static.$this->custom_db->get_custom_condition ( $condition );
		} else {
			$condition = 'TB.status="PENDING" ';
		}	

		// BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(TB.enquiry_reference_no)) AS total_records from tour_booking_details AS TB WHERE ' . $condition;
			$data = $this->db->query ( $query )->row_array ();
			return $data ['total_records'];
		} else {			
			// Booking Details
			// $bd_query = 'select TB.*,TI.* from tour_booking_details AS TB join holiday_package_pax_details as PD using(enquiry_reference_no) join tours_enquiry as TI using(enquiry_reference_no) WHERE ' . $condition . ' order by TB.origin desc limit ' . $limit;
			// debug($condition); exit('');
			$bd_query = 'select TB.* from tour_booking_details AS TB  WHERE ' . $condition . ' order by TB.origin desc limit ' . $limit;
			$booking_details = $this->db->query ( $bd_query )->result_array ();
			$result=array();
			foreach ($booking_details as $value) {
				$result[$value['enquiry_reference_no']]['booking_details'] = $value;

				$id_query = 'select TI.*,T.package_name from tours_enquiry AS TI LEFT JOIN tours AS T ON TI.tour_id=T.id WHERE TI.enquiry_reference_no="'.$value['enquiry_reference_no'].'" order by TI.id desc ';
				$enquiry_details = $this->db->query ( $id_query )->result_array ();
				$result[$value['enquiry_reference_no']]['enquiry_details'] = $enquiry_details[0];

				$td_query = 'select T.*,TC.name AS country_name from tours AS T LEFT JOIN tours_country AS TC ON TC.id=T.tours_country WHERE T.id="'.$enquiry_details[0]['tour_id'].'" ';
				$tours_details = $this->db->query ( $td_query )->result_array ();
				$result[$value['enquiry_reference_no']]['tours_details'] = $tours_details[0];
				$tc_query = 'select CityName AS city_name from tours_city WHERE id IN ('.$tours_details[0]['tours_city'].') ';
				$tours_city_details = $this->db->query ( $tc_query )->result_array ();
				$result[$value['enquiry_reference_no']]['tours_details']['city_name'] = array_column($tours_city_details, 'city_name');
				$tp_query = 'select *  from tour_price_management WHERE tour_id='.$enquiry_details[0]['tour_id'].' AND from_date<="'.$enquiry_details[0]['departure_date'].'" AND to_date>="'.$enquiry_details[0]['departure_date'].'"  ';
				$tours_price_details = $this->db->query ( $tp_query )->result_array ();
				$result[$value['enquiry_reference_no']]['tours_details']['price'] = $tours_price_details[0]['final_airliner_price'];
				$result[$value['enquiry_reference_no']]['tours_details']['currency'] = $tours_price_details[0]['currency'];

				$pd_query = 'select * from holiday_package_pax_details WHERE app_reference="'.$value['app_reference'].'" order by origin asc ';
				$pax_details = $this->db->query ( $pd_query )->result_array ();
				$result[$value['enquiry_reference_no']]['pax_details'] = $pax_details;

				/*$adult_count_query = 'select COUNT(*) AS total_records from holiday_package_pax_details WHERE app_reference="'.$value['app_reference'].'" AND pax_type="adult" ';
				$adult_count = $this->db->query ( $adult_count_query )->row_array ();
				$result[$value['enquiry_reference_no']]['pax_details']['adult_count'] = $adult_count ['total_records'];

				$child_count_query = 'select COUNT(*) AS total_records from holiday_package_pax_details WHERE app_reference="'.$value['app_reference'].'" AND pax_type="child" ';
				$child_count = $this->db->query ( $child_count_query )->row_array ();
				$result[$value['enquiry_reference_no']]['pax_details']['child_count'] = $child_count ['total_records'];

				$infant_count_query = 'select COUNT(*) AS total_records from holiday_package_pax_details WHERE app_reference="'.$value['app_reference'].'" AND pax_type="infant" ';
				$infant_count = $this->db->query ( $infant_count_query )->row_array ();
				$result[$value['enquiry_reference_no']]['pax_details']['infant_count'] = $infant_count ['total_records'];*/
			}
			$response ['data'] = $result;
			// debug($response); exit('');
			return $response;
		}
	}

	function booking_old($condition = array(), $count = false, $offset = 0, $limit = 100000000000) {		
		$response ['status'] = SUCCESS_STATUS;
		$response ['data'] = array ();		
		$condition = $this->custom_db->get_custom_condition ( $condition );
		if ($count) {
			$query = 'select COUNT(*) AS total_records from tour_booking_details AS BD WHERE 1=1 ' . $condition;
			$data = $this->db->query ( $query )->row_array ();
			return $data ['total_records'];
		} else {		
			$bd_query = 'select BD.* from tour_booking_details AS BD  WHERE 1=1 ' . $condition . ' order by BD.origin desc limit ' . $limit;
			$booking_details = $this->db->query ( $bd_query )->result_array ();
			$result=array();
			foreach ($booking_details as $value) {
				$app_reference = $value['app_reference'];
				$user_attributes=$value['user_attributes'];
				$user_attributes=json_decode($user_attributes,true);
				$attributes=$value['attributes'];
				$attributes=json_decode($attributes,true);
				$c_query = 'SELECT name AS country_name FROM api_country_list WHERE iso_country_code="'.$user_attributes['country'].'"';
				$country_name = $this->db->query ( $c_query )->result_array ();
				$user_attributes['country_name'] = $country_name[0]['country_name'];
				$value['user_attributes']=json_encode($user_attributes);
				$result[$app_reference]['booking_details'] = $value;

				$id_query = 'select TI.*,T.package_name from tours_enquiry AS TI LEFT JOIN tours AS T ON TI.tour_id=T.id WHERE TI.enquiry_reference_no="'.$value['enquiry_reference_no'].'" order by TI.id desc ';
				$enquiry_details = $this->db->query ( $id_query )->result_array ();
				$result[$app_reference]['enquiry_details'] = $enquiry_details[0];

				$tour_id = $enquiry_details[0]['tour_id'];
				if(count($enquiry_details)<1){
					$tour_id = $attributes['tour_id'];
				}

				$td_query = 'select T.*,TC.name AS country_name from tours AS T LEFT JOIN tours_country AS TC ON TC.id=T.tours_country WHERE T.id="'.$tour_id.'" ';
				$tours_details = $this->db->query ( $td_query )->result_array ();
				$result[$app_reference]['tours_details'] = $tours_details[0];
				if(!empty($tours_details[0]['tours_city'])){
					$tc_query = 'select CityName AS city_name from tours_city WHERE id IN ('.$tours_details[0]['tours_city'].') ';
					$tours_city_details = $this->db->query ( $tc_query )->result_array ();
					$result[$app_reference]['tours_details']['city_name'] = array_column($tours_city_details, 'city_name');
				}
				if(!empty($tours_details[0]['tour_id']) && !empty($tours_details[0]['departure_date']) ){
					$tp_query = 'select *  from tour_price_management WHERE tour_id='.$tour_id.' AND from_date<="'.$enquiry_details[0]['departure_date'].'" AND to_date>="'.$enquiry_details[0]['departure_date'].'"  ';
					$tours_price_details = $this->db->query ( $tp_query )->result_array ();
					$result[$app_reference]['tours_details']['price'] = $tours_price_details[0]['final_airliner_price'];
					$result[$app_reference]['tours_details']['currency'] = $tours_price_details[0]['currency'];
				}
				$pd_query = 'select * from holiday_package_pax_details WHERE app_reference="'.$value['app_reference'].'" order by origin asc '; 
				$pax_details = $this->db->query ( $pd_query )->result_array ();
				$result[$app_reference]['pax_details'] = $pax_details;
				//to fetch the pricemanagment
				// $this->where(array('tours.app_reference'=>$value['app_reference']));
				// $this->db->select('');
				// $this->db->get('tour_price_management');
			}
			$response ['data'] = $result;
			return $response;
		}
	}

	public function tour_destinations()
	{
		$query = 'select * from tour_destinations order by destination'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function get_tour_destinations()
	{
		$query = 'select id,destination from tour_destinations order by destination'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[$fetch['id']] = $fetch['destination'];
		}
		return $result;
	}
	public function tour_destinations_details($id)
	{
		$query = "select * from tour_destinations where id='$id'"; //echo $query; exit;
		$exe   = mysql_query($query);
		$fetch = mysql_fetch_assoc($exe);
		return $fetch;
	}
	public function add_tour_destination_save($query) {
		//debug($query);exit;
		$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function delete_tour_destination($id) {
		$query = "delete from tour_destinations where id='$id'";
		$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function tour_destination_details($id)
	{
		$query = "select * from tour_destinations where id='$id'";
		$exe   = mysql_query($query);
		$fetch = mysql_fetch_assoc($exe);
		return $fetch;
	}
	public function edit_tour_destination_save($query) {
		//debug($query);exit;
		$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function activation_tour_destination($id,$status) {
		//echo 'model add_tour_destination_save';
		//debug($data);exit;
		$query = "update tour_destinations set status='$status' where id='$id'";
		$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function AUTO_INCREMENT($table) {
		/*$HTTP_HOST = '192.168.0.63';
        if(($_SERVER['HTTP_HOST']==$HTTP_HOST) || ($_SERVER['HTTP_HOST']=='localhost'))
	    {
				$db = 'neptune';	 
	    }
	    else
	    {
				$db = 'developm_airlinersv2';
        } 
		$query    = "SELECT AUTO_INCREMENT FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$db' AND TABLE_NAME= '$table'";	
        $exe      = mysql_query($query);
        if(!$exe) { die(mysql_error());}
        else
        { 
           $fetch          = mysql_fetch_array($exe);
	       $AUTO_INCREMENT = $fetch['AUTO_INCREMENT'];
	       return $AUTO_INCREMENT; 
	      }*/
	      $auto_increment  = rand(1000,9999);
	      return $auto_increment; 
	     }
	     public function add_tour_save($query) {
	     	$exe   = $this->db->query ( $query )->result_array ();
	     	if(!$exe) { die('okk');}
	     	else{ return true;}
	     }
		 public function draft_list() {
	     	$query = 'select * from tours where admin_approve_status = 1 AND agent_id IS NULL AND status_delete != "1" AND package_status = "CREATED" order by id desc'; 
	     	$exe   = $this->db->query ( $query )->result_array ();
	     	// $exe   = mysql_query($query);
	     	$result = array();
	     	foreach($exe as $fetch)
			{
	     		$result[] = $fetch;
	     	}
	     	return $result;
	     }
	     public function tour_list() {
	     	$query = 'select * from tours where admin_approve_status = 1 AND agent_id IS NULL AND status_delete != "1" AND package_status = "ITINERARY_ADDED" order by id desc'; 
	     	$exe   = $this->db->query ( $query )->result_array ();
	     	// $exe   = mysql_query($query);
	     	$result = array();
	     	foreach($exe as $fetch)
			{
	     		$result[] = $fetch;
	     	}
	     	return $result;
	     }
		 public function verify_tour_list() {
	     	$query = 'select * from tours where admin_approve_status = 1 AND agent_id IS NULL AND status_delete != "1" AND package_status = "VERIFICATION" order by status_update_date desc'; 
	     	$exe   = $this->db->query ( $query )->result_array ();
	     	// $exe   = mysql_query($query);
	     	$result = array();
	     	foreach($exe as $fetch)
			{
	     		$result[] = $fetch;
	     	}
	     	return $result;
	     }
		public function verified_tour_list() {
	     	$query = 'select * from tours where admin_approve_status = 1 AND agent_id IS NULL AND status_delete != "1" AND package_status = "VERIFIED" order by status_update_date desc'; 
	     	$exe   = $this->db->query ( $query )->result_array ();
	     	// $exe   = mysql_query($query);
	     	$result = array();
	     	foreach($exe as $fetch)
			{
	     		$result[] = $fetch;
	     	}
	     	return $result;
	     }
	public function tour_list_pending() {
		$query = 'select * from tours where agent_request_status = 1 and admin_approve_status = 0 AND status_delete != "1" order by id desc'; //echo $query; exit;
	// /echo $query; exit();
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{

			$result[] = $fetch;
		}
		return $result;
	}
	public function activation_tour_package($query) {
		$exe   = $this->db->query ( $query )->result_array ();
		if(!$exe) { die('error');}
		else{ return true;}
	}
		public function delete_tour_package($id) {
		// delete_tour_visited_cities delete_tour_dep_date
		// $query = "delete from tours where id='$id'";
		// $query1 = "delete from tour_visited_cities where tour_id='$id'";
		// $query2 = "delete from tour_dep_dates where tour_id='$id'";
		// $exe1   = mysql_query($query1);
		// $exe2   = mysql_query($query2);
		// $exe   = mysql_query($query);
		// if(!$exe) { die(mysql_error());}
		// else{ return true;}
		$query = 'UPDATE tours SET status = 0, status_delete = "1" where id='.$id;
		$result = $this->db->query($query);
		if(!$result)
		{
			return FALSE;		
		}else
		{
			return TRUE;
		}
	}	
	public function tour_dep_dates($tour_id) {
		$query = "select * from tour_dep_dates where tour_id='$tour_id' order by dep_date asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			@$result[] = $fetch;
		}
		return @$result;
	}
	public function tour_data($tour_id) {

		$query = "select * from tours where id='$tour_id'";
		$result = $this->db->query ( $query )->result_array ();
		
		// $exe   = mysql_query($query);
		$fetch = array();
		$fetch = $result[0];
		return $fetch;
		
	}
	public function package_data($package_id) {
		$fetch ='';
		if(empty($package_id) == false){
			$query = "select * from tours where package_id='$package_id'";
			$exe   = $this->db->query ( $query )->result_array ();
			$fetch = $exe[0];
		}
		
		
		// $exe   = mysql_query($query);
		// $result = array();
		// foreach($exe as $fetch)
		// {
		// if(!$exe) { die(mysql_error());}
		// else
		// { 
		// 	$fetch = mysql_fetch_assoc($exe);
			return $fetch;
		// }
	}
	public function tour_dep_date_save($query) {
		// echo $query;exit;
		$exe   = $this->db->query ( $query );
		// echo $exe->num_rows();exit;
		// debug($exe);exit;
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function delete_tour_dep_date($id,$tour_id,$type) {
		//echo $id.'|'.$tour_id.'|'.$type;exit;
		if($type=='group'){
			$query = "delete from tour_dep_dates where id='$id'";
			
		}else{
			$query = "delete from tour_valid_from_to_date where id='$id'";
		}
		$exe   = $this->db->query ( $query );
		//$exe   = mysql_query($query);
		if(!$exe) { die('error');}
		else{ return true;}
	}
	public function tour_visited_cities($tour_id) {
		$query = "select * from tour_visited_cities where tour_id='$tour_id'"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_visited_cities_save($query) {
		$exe   = $this->db->query ( $query );
		//$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function delete_tour_visited_cities($id) {
		$query = "delete from tour_visited_cities where id='$id'";
		$exe   = $this->db->query ( $query );
		//$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function tour_visited_cities_details($id) {
		$query = "select * from tour_visited_cities where id='$id'"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function edit_tour_visited_cities_save($query) {
		$exe   = $this->db->query ( $query );
		//$exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function query_run($query) {
		$result = $this->db->query ( $query );
		 return $result;
		// debug($result);exit;
		// $exe   = mysql_query($query);
		// if(!$exe) { die(mysql_error());}
		// else{}
	}
	public function top_tour_destinations()
	{
		$query = 'select * from tour_destinations order by cms_status desc'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_dep_dates_list($tour_id)
	{
		$query = "select * from tour_dep_dates where tour_id='$tour_id' order by dep_date asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_dep_dates_from_to_list($tour_id)
	{
		$query = "select * from tour_valid_from_to_date where tour_id='$tour_id'"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_visited_cities_list($tour_id)
	{
		$query = "select * from tour_visited_cities where tour_id='$tour_id' order by id asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_itinerary($tour_id)
	{
		$query = "select * from tours_itinerary where tour_id='$tour_id'"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $num   = mysql_num_rows($exe);
		$fetch = $exe[0];
		return $fetch; exit;
	}
	public function tour_visited_cities_all()
	{
		$query = "select * from tour_visited_cities order by id asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[$fetch['id']] = $fetch['city'];
		}
		return $result;

		
	}
	public function tour_dep_dates_list_all()
	{
		$query = "select * from tour_dep_dates order by dep_date asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[$fetch['tour_id']][] = $fetch['dep_date'];
		}
		return $result;
	}
	public function tour_dep_dates_list_published()
	{
		$query = "select * from tours_itinerary where publish_status=1 order by dep_date asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[$fetch['tour_id']][] = $fetch['dep_date'];
			//$result[$fetch['tour_id']][] = $fetch;
		}
		return $result;
	}
	public function tour_dep_dates_list_published_wd()
	{
		$query = "select * from tours_itinerary  order by dep_date asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			//$result[$fetch['tour_id']][] = $fetch['dep_date'];
			$result[$fetch['tour_id']][] = $fetch;
		}
		return $result;
	}
	public function check_tour_dep_dates($tour_id,$dep_date)
	{
		$query = "select * from tour_dep_dates where tour_id='$tour_id' and dep_date='$dep_date'"; //echo $query; exit;
		$exe   = $this->db->query ( $query );
		
		$num   = $exe->num_rows();
		// debug($num);exit;
		return $num;
	}
	public function ajax_tour_publish($query)
	{
		$exe   = $this->db->query ( $query );
		$num   = $exe->num_rows();
		return $num;
	}

	public function ajax_tour_publish_1($query)
	{
		$exe   = $this->db->query ( $query );
		$num   = $exe->num_rows();
		return $num;
	}
	public function tour_date_list()
	{
		$query = "select * from tours_itinerary order by tour_id asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
     	$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_enquiry($condition)
	{
		error_reporting(E_ALL);
		/*if($condition['status'] && strtolower($condition['status']) != 'all'){
			$this->db->where('status',$condition['status']);
		}
		if($condition['phone']){
			$this->db->where('phone',$condition['phone']);
		}
		if($condition['email']){
			$this->db->where('email',$condition['email']);
		}
		if($condition['tour_id']){
			$this->db->where('tour_id',$condition['tour_id']);
		}
		if($condition['common_date']){
			$this->db->where('common_date',$condition['common_date']);
		}*/
		if($condition['module']){
			$this->db->where('created_by',$condition['module']);
		}
		$this->db->order_by('id','desc');
		$query = $this->db->get("tours_enquiry");
		if($query->num_rows > 0)
		{
			$result['tours_enquiry']=$query->result_array();
		}
		else
		{
			$result['tours_enquiry']= array(); 
		}
		//echo $this->db->last_query();
		// debug($result);exit;
		foreach ($result['tours_enquiry'] as $key => $value) {
			$tp_query = 'select *  from tour_price_management WHERE tour_id='.$value['id'].' AND from_date<="'.$value['departure_date'].'" AND to_date>="'.$value['departure_date'].'"  ';
			$tours_price_details = $this->db->query ( $tp_query )->result_array ();
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['final_airliner_price'];
			
			$tp_query1 = 'select *  from tour_booking_details WHERE enquiry_reference_no="'.$value['enquiry_reference_no'].'"';
			$tours_new_price_details = $this->db->query ( $tp_query1 )->result_array ();
			// debug($tours_price_details);die;
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['airliner_price'];
			$result['tours_enquiry'][$key]['updated_price'] = @$tours_new_price_details[0]['final_airliner_price'];
			$result['tours_enquiry'][$key]['currency'] = @$tours_price_details[0]['currency'];
		}
			return $result;
	}
	public function tours_itinerary_all()
	{
		$query = "select * from tours_itinerary order by id asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
	     	// $exe   = mysql_query($query);
	     	$result = array();
	     	
		// $exe   = mysql_query($query);
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_type()
	{
		$query = 'select * from tour_type order by tour_type_name'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function get_tour_type()
	{
		$query = 'select * from tour_type  where status = 1 order by tour_type_name '; //echo $query; exit;
	$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
	//	debug($query); 
		//debug($result); exit();
		return $result;
	}
	public function tour_type_details($id)
	{
		$query = "select * from tour_type where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_inclusions()
	{
		$query = 'select * from tour_inclusions order by id desc'; //echo $query; exit;
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function record_activation($table,$id,$status) {
		$query = "update ".$table." set status='$status' where id='$id'";
		$exe   = $this->db->query ( $query );
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function record_delete($table,$id) {
		$query = "delete from ".$table." where id='$id'";
		$exe   = $this->db->query ( $query );
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}
	public function table_record_details($table,$id)
	{
		$query = "select * from ".$table." where id='$id'"; //echo $query; exit;
		return  $this->db->query ( $query )->result_array ();
	}
	public function table_records($table,$order_by,$order)
	{
		$query = 'select * from '.$table.$order_by.$order; //echo $query; exit;
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_subtheme()
	{
		$query = 'select * from tour_subtheme order by tour_subtheme'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function get_tour_subtheme()
	{
		$query = 'select * from tour_subtheme where status = 1 order by tour_subtheme'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_activity()
	{
		$query = 'select * from tour_activity order by tour_activity'; //echo $query; exit;
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_continent()
	{
		$query = 'select * from tours_continent order by name'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function get_tours_continent()
	{
		$query = 'select * from tours_continent where status = 1 order by name'; //echo $query; exit;
		//$exe   = mysql_query($query);
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_continent($tours_continent)
	{
		$query = "select * from tours_country where continent='$tours_continent' order by name"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_country($tours_country)
	{
		$query = "select * from tours_city where country_id='$tours_country' group by CityName order by CityName"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_city_name()
	{
		$query = "select * from tours_city order by CityName"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query) or die(mysql_error());
		foreach($exe as $fetch)
		{
			$result[$fetch['id']] = $fetch['CityName'];
		}
		//debug($result); exit;
		return $result;
	}
	public function tours_country_name()
	{
		$query = "select * from tours_country order by name"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[$fetch['id']] = $fetch['name'];
		}
		return $result;
	}
	public function tours_continent_country($tour_id)
	{
		$query = "select * from tours where id='$tour_id'"; // echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// debug($exe);exit;
		$fetch = $exe[0];
		$query = "select * from tours_country where continent IN (".$fetch['tours_continent'].") order by name"; 

		// echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_country_city($tour_id)
	{
		$query = "select * from tours where id='$tour_id'"; // echo $query; exit;
		// $exe   = mysql_query($query);
		$exe   = $this->db->query ( $query )->result_array ();
		$fetch = $exe[0];

		$query = "select * from tours_city where country_id IN (".$fetch['tours_country'].") order by CityName	"; // echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_itinerary_dw($tour_id)
	{
		$query = "select * from tours_itinerary_dw where tour_id='$tour_id' order by id asc"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $num   = mysql_num_rows($exe);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_itinerary_wd($tour_id)
	{
		$query = "select * from tours_itinerary where tour_id='$tour_id' "; //echo $query; exit;
		$exe   = mysql_query($query);
		$num   = mysql_num_rows($exe);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function reviews()
	{
		$query = "select * from user_review where module='holiday' order by origin desc"; //echo $query; exit;
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function hotel_reviews()
	{
		$query = "select * from user_review where module='hotel' order by origin desc"; //echo $query; exit;
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function tour_region()
	{
		$query = 'select * from tours_continent order by name'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function check_region_exist($tours_continent)
	{
		
		$this->db->select('*');
		$this->db->where('name',$tours_continent);
		$query = $this->db->get('tours_continent');
		if ( $query->num_rows > 0 ) {
			return $query->result();
		}else{
			return array();
		}		
	}
	public function enquiry_user_details($enquiry_reference_no)
	{
		$this->db->select('*');
		$this->db->where('enquiry_reference_no',$enquiry_reference_no);
		$query = $this->db->get('tours_enquiry');
		if ( $query->num_rows > 0 ) {
			return $query->result();
		}else{
			return array();
		}		
	}

	public function tour_country()
	{
		$query = 'select  tours_country.*,tours_country.name as country_name,tours_continent.name as continent_name from tours_country join tours_continent on tours_country.continent = tours_continent.id order by tours_country.name'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function approve_package($p_id)
	{
		$query = "update tours set admin_approve_status = 1 where id='$p_id'";
      //echo $query; exit();
		$exe   = $this->db->query ( $query )->result_array ();
		
		if(!$exe) { die("error");}
		else{ return true;}
	}
	
	public function check_exist_tc() 
	{
		$query = 'select * from holiday_terms_n_condition '; //echo $query; exit;
	//	echo $query;exit();
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function update_tc($data)
	{
	//debug($data);exit;
		
		//$tc = $data[0][1];
		$update_data = array('terms_n_conditions' => $data['terms_n_conditions'],
			'cancellation_policy' => $data['cancellation_policy']
			);

	//	$query = "update holiday_terms_n_condition set terms_n_conditions = '$data' where id=1";
    //echo $query; exit();
		$exe   =      $this->db->update('holiday_terms_n_condition', $update_data);
		//debug($this->db->last_query()); exit;
		if(!$exe) { die('error');}
		else{ return true;}
	}
	
	public function get_holiday_tc()
	{
		$query = 'select * from terms_n_condition where module_name = "Holiday" '; //echo $query; exit;
	//	echo $query;exit();
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function get_package_id($package_name)
	{
		$query = 'select * from tours where package_name like \'%'.$package_name.'%\' '; //echo $query; exit;
		//echo $query;exit();
		$exe   = $this->db->query ( $query )->result_array ();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function get_occupancy()
	{
		$this->db->select('*');
		$query = $this->db->get('occupancy_managment');
		if ( $query->num_rows > 0 ) {
			return $query->result_array();
		}else{
			return array();
		}		
	}

	public function get_price_details($id,$module)
	{
		
		$this->db->select('*');
		$array = array('tour_id' => $id, 'package_type' => $module);
		$this->db->where($array);
		$query = $this->db->get('tour_price_management');
		//echo $this->db->last_query();exit;
		// debug($query);exit;
		if ( $query->num_rows > 0 ) {
			// debug($query->result_array());exit;
			return $query->result_array();
		}else{
			return array();
		}	
		/*$query = 'select * from tour_price_management join tours on tours.id = tour_price_management.tour_id  where tour_price_management.id = '$id'' ; //echo $query; exit;
		echo $query; exit();
		$exe   = mysql_query($query);
		while($fetch = mysql_fetch_assoc($exe))
		{
			$result[] = $fetch;
		}
		return $result;*/
	}

	public function get_price_details_single($id)
	{
		
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get('tour_price_management');
		if ( $query->num_rows > 0 ) {
			return $query->result_array();
		}else{
			return array();
		}	
		
	}

	public function tours_date_price($tour_id)
	{
		$query = "select * from tours_itinerary where tour_id='$tour_id' order by dep_date"; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}

	public function check_price_avilability($from,$to,$occupancy,$tour_id)
	{
		error_reporting(E_ALL);
		$query = "select * from tour_price_management where (('".$from."' between from_date and to_date) or ('".$to."' between from_date and to_date)) AND tour_id = '".$tour_id."' AND occupancy = '".$occupancy."'";
		//echo $query; exit();

		$exe   = $this->db->query ( $query );
		// debug($exe);exit;
		if($exe->num_rows() > 0){
			return true;
		}
		else
		{ 
			
			return false;
			
		}

	}

	public function get_currency_list()
	{
		$this->db->select('*');
		//$this->db->where('name',$tours_continent);
		$query = $this->db->get('currency_converter');
		if ( $query->num_rows > 0 ) {
			return $query->result_array();
		}else{
			return array();
		}	
	}

	public function update_tours_images($image_data, $deleteid) {
		$this->db->where ( 'id', $deleteid );
		$this->db->update ( 'tours', $image_data );
	}

	public function delete_tour_price($id)
	{
		
		$query = "delete from tour_price_management where id='$id'";
      // echo $query;exit();
		$exe   = $this->db->query ( $query );
		// debug($exe);exit;
		// $exe   = mysql_query($query);
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}

	public function delete_occupancy_managment($id)
	{
		
		$query = "delete from occupancy_managment where id='$id'";
      // echo $query;exit();
		$exe   = $this->db->query ( $query );
		$query1 = "delete from tour_price_management where occupancy='$id'";
      // echo $query;exit();
		$exe1   = $this->db->query ( $query1 );
		if(!$exe) { die(mysql_error());}
		else{ return true;}
	}

	

	public function tour_data_temp($tour_id) {
		$query = "select * from tours_temp where id='$tour_id'";
		$exe   = $this->db->query ( $query )->result_array ();
		if(!$exe) { die(mysql_error());}
		else
		{ 
			foreach($exe as $fetch)
			{
				$result[] = $fetch;
			}
			return $result;
		}
	}
	public function booking_details($app_reference)
	{
		$response ['status'] = QUERY_FAILURE;
		$response ['data'] = array ();
		$bd_query = 'select BD.* from tour_booking_details AS BD  WHERE app_reference="'.$app_reference.'" ';
		$booking_details = $this->db->query ( $bd_query )->result_array ();
		if(valid_array($booking_details) && count($booking_details)>0){
			$response ['status'] = QUERY_SUCCESS;
			$response ['data'] = $booking_details[0];
		}
		return $response;
	}
	public function quotation_details($quote_reference)
	{
		$response ['status'] = QUERY_FAILURE;
		$response ['data'] = array ();
		$quote_query = 'select * from tours_quotation_log  WHERE quote_reference="'.$quote_reference.'" ';
		$quotation_details = $this->db->query ( $quote_query )->result_array ();
		if(valid_array($quotation_details) && count($quotation_details)>0){
			$response ['status'] = QUERY_SUCCESS;
			$response ['data'] = $quotation_details[0];
		}
		return $response;
	}
	public function fetch_price(){

        $res = $this->db->select('final_airliner_price,calculated_markup,id,sessional_price,currency');
		$query = $this->db->get('tour_price_management');
		//debug($query->result_array());exit();
		//echo $this->db->last_query();
		return  $query->result_array();
		
	}

	public function update_final_price($id,$data){
		//echo $id.",".$data);exit;
		$this->db->where(array('id'=>$id));
		if($this->db->update('tour_price_management',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
		// echo  $this->db->last_query();exit;

	}
	
	/* hotel conents*/
	public function hotel_list()
	{
		$query = 'select tc.name as name,tci.CityName as cityname,hm.* from tour_hotel_master as hm INNER JOIN tours_country as tc ON tc.id=hm.country INNER JOIN tours_city as tci ON tci.id=hm.city order by hm.hotel_name'; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function hotel_details($id)
	{
		$query = "select * from tour_hotel_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	
	/* Supplier conents*/
	public function supplier_list()
	{
		$query = 'select tsm.id as s_id,tsm.status as s_status , tsm.*,tc.* from tour_supplier_master as tsm INNER JOIN tours_country as tc ON tc.id=tsm.country order by tsm.supplier_name '; //echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function supplier_details($id)
	{
		$query = "select tsm.id as s_id,tsm.*,tscd.*  from tour_supplier_master as tsm  
			LEFT JOIN tour_supplier_contact_details as tscd ON tsm.id=tscd.supplier_id where tsm.id='$id'"; //echo $query; exit;
		
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* inclusion conents*/
	public function inclusion_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_inclusion_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function inclusion_details($id)
	{
		$query = "select * from tour_inclusion_master where id='$id'"; //echo $query; exit;
		
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* exclusion conents*/
	public function exclusion_list()
	{
	
		$query = 'select tc.name as name,tim.* from tour_exclusions_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function exclusion_details($id)
	{
		$query = "select * from tour_exclusions_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	
	/* Highlight contents*/
	public function highlight_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_highlight_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function highlight_details($id)
	{
		$query = "select * from tour_highlight_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* cancellation contents*/
	public function cancellation_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_cancellation_policy_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function cancellation_details($id)
	{
		$query = "select * from tour_cancellation_policy_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* PAyment policy contents*/
	public function payment_policy_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_payment_policy_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function payment_policy_details($id)
	{
		$query = "select * from tour_payment_policy_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* trip_note contents*/
	public function trip_note_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_trip_notes_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function trip_note_details($id)
	{
		$query = "select * from tour_trip_notes_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* Optional TOur contents*/
	public function optional_tour_list()
	{
		
		//$query = 'select tc.name as name,tim.* from tour_optional_tours_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$query = 'select tc.name as name,tci.CityName as cityname,hm.* from tour_optional_tours_master as hm INNER JOIN tours_country as tc ON tc.id=hm.country INNER JOIN tours_city as tci ON tci.id=hm.city order by hm.tour_name'; 
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function optional_tour_details($id)
	{
		$query = "select * from tour_optional_tours_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	/* terns conditions contents*/
	public function terms_conditions_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_terms_conditions_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function terms_conditions_details($id)
	{
		$query = "select * from tour_terms_conditions_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	
	/* tour_visa_procedure_master */
	public function visa_procedures_list()
	{
		
		$query = 'select tc.name as name,tim.* from tour_visa_procedure_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country';//echo $query; exit;
		$exe   = $this->db->query ( $query )->result_array ();
		$result=array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function visa_procedures_details($id)
	{
		$query = "select * from tour_visa_procedure_master where id='$id'"; //echo $query; exit;
		// $exe   = mysql_query($query);
		// $fetch = mysql_fetch_assoc($exe);
		// return $fetch;
		$exe   = $this->db->query ( $query )->result_array ();
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_supplier($tours_country)
	{
		$query = "select * from tour_supplier_master where country='$tours_country' AND status='1' order by supplier_name"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_hotels($tours_country)
	{
		$query = "select thm.*,tci.CityName,tci.id as city_id from tours_city as tci  LEFT JOIN tour_hotel_master as thm ON tci.id=thm.city where tci.id='$tours_country' order by thm.hotel_name"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch; 
		}
		return $result;
	}
	public function ajax_optional_tours($tours_country)
	{
		$query = "select thm.*,tci.CityName,tci.id as city_id  from tours_city as tci  LEFT JOIN tour_optional_tours_master as thm ON tci.id=thm.city where thm.status=1 and tci.id='$tours_country' order by thm.tour_name"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_supplier($tours_city){
		$query = "select * from tour_supplier_master where country IN (".$tours_city.") order by supplier_name"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_concerned_persons($suppl_id)
	{
		$query = "select * from tour_supplier_contact_details where supplier_id IN (".$suppl_id.") order by contact_person"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_hotel($tours_city){
		$query = "select thm.*,tci.CityName from tour_hotel_master thm INNER JOIN tours_city as tci ON tci.id=thm.city  where thm.city = ".$tours_city."  order by thm.hotel_name"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function optional_tours($tours_city){
		$query = "select thm.*,tci.CityName ,tci.id as city_id from tours_city as tci  LEFT JOIN tour_optional_tours_master thm ON tci.id=thm.city where tci.id  = ".$tours_city."  order by thm.tour_name"; //echo $query; exit("Gsdfg");
		
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_sel_hotels($tour_id){
		$query = "select * from tours_hotel_details where tour_id = ".$tour_id.""; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tours_sel_opt_tours($tour_id){
		$query = "select optional_tour from tour_optional_tour_details where tour_id = ".$tour_id.""; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	
	public function ajax_tours_highlights($tours_country)
	{
		$query = "select tim.*,tc.name from tour_highlight_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_inclusions($tours_country)
	{
		$query = "select tim.*,tc.name from tour_inclusion_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_exclusions($tours_country)
	{
		$query = "select tim.*,tc.name from tour_exclusions_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_terms_conditions($tours_country)
	{
		$query = "select tim.*,tc.name from tour_terms_conditions_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_cancelation_policy($tours_country)
	{
		$query = "select tim.*,tc.name from tour_cancellation_policy_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_payment_policy($tours_country)
	{
		$query = "select tim.*,tc.name from tour_payment_policy_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	
	public function ajax_tours_trip_note($tours_country)
	{
		$query = "select tim.*,tc.name from tour_trip_notes_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function ajax_tours_visa_procedures($tours_country)
	{
		$query = "select tim.*,tc.name  from tour_visa_procedure_master as tim INNER JOIN tours_country as tc ON tc.id=tim.country where tim.country='$tours_country'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_valid_dates($tour_id){
		$query = "select * from tour_valid_from_to_date where tour_id='$tour_id'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function tour_hotel_city_data($tour_id){
		$query = "select * from tours_hotel_details as thd INNER JOIN tour_hotel_master as hm ON hm.id=thd.hotel_id where tour_id = ".$tour_id.""; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function get_package_manager_list(){
		$query = "select user_id,first_name,last_name from user where user_type = '12'"; //echo $query; exit("Gsdfg");
		$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
		return $result;
	}
	public function assigned_tours_enquiry($condition)
	{
			//debug($condition);
		if($condition['user_id']){
			$this->db->where('alloted_to',$condition['user_id']);
		}
		if($condition['status']){
			$this->db->where('status',$condition['status']);
		}
		if($condition['']){
			$this->db->where('status',$condition['status']);
		}
		if($condition['module']){
			$this->db->where('created_by',$condition['module']);
		}
		$this->db->order_by('id','desc');
		$query = $this->db->get("tours_enquiry");
		//echo $this->db->last_query();exit;
		if($query->num_rows > 0)
		{
			$result['tours_enquiry']=$query->result_array();
		}
		else
		{
			$result['tours_enquiry']= array();
		}
		
		foreach ($result['tours_enquiry'] as $key => $value) {
			$tp_query = 'select *  from tour_price_management WHERE tour_id='.$value['id'].' AND from_date<="'.$value['departure_date'].'" AND to_date>="'.$value['departure_date'].'"  ';
			$tours_price_details = $this->db->query ( $tp_query )->result_array ();
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['final_airliner_price'];
			
			$tp_query1 = 'select *  from tour_booking_details WHERE enquiry_reference_no="'.$value['enquiry_reference_no'].'"';
			$tours_new_price_details = $this->db->query ( $tp_query1 )->result_array ();
			// debug($tours_price_details);die;
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['airliner_price'];
			$result['tours_enquiry'][$key]['updated_price'] = @$tours_new_price_details[0]['final_airliner_price'];
			$result['tours_enquiry'][$key]['currency'] = @$tours_price_details[0]['currency'];
		}
		return $result;
	}
	public function assigned_custom_enquiry($condition)
	{
		if($condition['user_id']){
			$this->db->where('alloted_to',$condition['user_id']);
		}
		if($condition['status']){
			$this->db->where('status',$condition['status']);
		}
		$this->db->order_by('id','desc');
		$query = $this->db->get("custom_package_enquiry");
		if($query->num_rows > 0)
		{
			$result['tours_enquiry']=$query->result_array();
		}
		else
		{
			$result['tours_enquiry']= array();
		}
		
		/*foreach ($result['tours_enquiry'] as $key => $value) {
			$tp_query = 'select *  from tour_price_management WHERE tour_id='.$value['id'].' AND from_date<="'.$value['departure_date'].'" AND to_date>="'.$value['departure_date'].'"  ';
			$tours_price_details = $this->db->query ( $tp_query )->result_array ();
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['final_airliner_price'];
			
			$tp_query1 = 'select *  from tour_booking_details WHERE enquiry_reference_no="'.$value['enquiry_reference_no'].'"';
			$tours_new_price_details = $this->db->query ( $tp_query1 )->result_array ();
			// debug($tours_price_details);die;
			$result['tours_enquiry'][$key]['price'] = @$tours_price_details[0]['airliner_price'];
			$result['tours_enquiry'][$key]['updated_price'] = @$tours_new_price_details[0]['final_airliner_price'];
			$result['tours_enquiry'][$key]['currency'] = @$tours_price_details[0]['currency'];
		}*/
		return $result;
	}
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$cond='';
		if($condition['created_by']){
			$cond.=' AND BD.created_by="'.$condition['created_by'].'"';
		}
		//$condition = $this->custom_db->get_custom_condition($condition);
		//debug($condition);
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from tour_booking_details BD
					where BD.domain_origin='.get_domain_auth_id().' '.$cond;
					//echo $query;exit;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select * from tour_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().' '.$cond.'
						order by BD.origin desc limit '.$offset.', '.$limit;
			//debug($bd_query);
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from tour_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from tour_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				//temp booking details
				$tb_query = 'select * from  temp_booking AS TL
							WHERE TL.book_id IN ('.$app_reference_ids.')';
							
				//Transaction Details
				$tl_query = 'select * from  transaction_log AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
				//Payment history 
				$ph_query = 'select * from  tour_payment_slab_details AS TL
							WHERE TL.enquiry_reference_no IN ('.$app_reference_ids.')';
							
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$tl_details = $this->db->query($tl_query)->result_array();
				$tb_details = $this->db->query($tb_query)->result_array();
				$ph_details = $this->db->query($ph_query)->result_array();
				//echo $this->db->last_query();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] 				= $tl_details;
			$response['data']['temp_booking_details'] 		= $tb_details;
			$response['data']['payment_history_details'] 	= $ph_details;
			//debug($response);exit;
			return $response;
		}
	}
	function assigned_booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//$condition = $this->custom_db->get_custom_condition($condition);
		$con='';
		if($this->entity_user_type != '1'){
			$con.='AND BD.alloted_to ='.$this->entity_user_id;
		}
		if($condition['created_by']){
			$con.=' AND BD.created_by="'.$condition['created_by'].'"';
		}
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from tour_booking_details BD
					Left join tour_booking_pax_details BBCD on BD.app_reference=BBCD.app_reference 
					Left join tour_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
					where domain_origin='.get_domain_auth_id().' '.$con;
					//echo $query;exit;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select * from tour_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().' '.$con.'
						order by BD.origin desc limit '.$offset.', '.$limit;
						//echo $bd_query;exit;
			$booking_details = $this->db->query($bd_query)->result_array();
			//debug($booking_details);exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from tour_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from tour_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				//temp booking details
				$tb_query = 'select * from  temp_booking AS TL
							WHERE TL.book_id IN ('.$app_reference_ids.')';
							
				//Transaction Details
				$tl_query = 'select * from  transaction_log AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
							
				//Payment history 
				$ph_query = 'select * from  tour_payment_slab_details AS TL
							WHERE TL.enquiry_reference_no IN ('.$app_reference_ids.')';
							
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$tl_details = $this->db->query($tl_query)->result_array();
				$tb_details = $this->db->query($tb_query)->result_array();
				$ph_details = $this->db->query($ph_query)->result_array();
				//echo $this->db->last_query();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] 				= $tl_details;
			$response['data']['temp_booking_details'] 		= $tb_details;
			$response['data']['payment_history_details'] 	= $ph_details;
			//debug($response);exit;
			return $response;
		}
	}
	function upload_image_lgm($image_info,$name,$old_image_name='',$index,$ref){
		//echo $this->template->domain_image_upload_path();exit;
		//error_reporting(E_ALL);
		//debug($image_info);
		//echo $name;
		if($name=='hotel_voucher'){
			$doc='hv';
		}else if($name=='flight_ticket'){
			$doc='ft';
		}else if($name=='visa'){
			$doc='vs';
		}else if($name=='final_itinary'){
			$doc='fi';
		}else{
			$doc='tour';
		}
		
		$image_info_name  = $old_image_name;
		if(!empty($image_info[$name]['name'][$index])){	
		//echo "one";
		    $new_str = str_replace(' ', '', $image_info[$name]['name'][$index]);
			$new_str = str_replace(',', '_', $new_str);
			if(is_uploaded_file($image_info[$name]['tmp_name'][$index])) {
				//echo "two";
				if($image_info_name !=''){
				//	echo "three";
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$image_info_name);
					$path         = addslashes($folderpath);
					$oldImage = $folderpath;
					unlink($oldImage);
					//echo $oldImage;exit;
				}
				 
				$image_type = explode("/",$image_info[$name]['type'][$index]);
				if($image_type[0] == "image"){
					//echo "four";					
					$sourcePath = $image_info[$name]['tmp_name'][$index];
					$img_Name	= $doc.$index.$new_str;
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$img_Name);
					$path         = addslashes($folderpath);
					if(move_uploaded_file($sourcePath,$folderpath)){
						echo $path;
						$image_info_name = $img_Name;
					}else{
						echo "gdsfgdf";exit;
					}
				}else{
					$sourcePath = $image_info[$name]['tmp_name'][$index];
					$img_Name	= $doc.$index.$new_str;
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$img_Name);
					$path         = addslashes($folderpath);
					if(move_uploaded_file($sourcePath,$folderpath)){
						echo $path;
						$image_info_name = $img_Name;
					}else{
						echo "gdsfgdf";exit;
					}
				}
			}				
		}
		//echo $image_info_name;exit("end");
		return $image_info_name;
	}
	function upload_single_image($image_info,$name,$old_image_name=''){

		$image_info_name  = $old_image_name;
		if(!empty($image_info[$name]['name'])){	
		//echo "one";
		    $new_str = str_replace(' ', '', $image_info[$name]['name']);
			$new_str = str_replace(',', '_', $new_str);
			if(is_uploaded_file($image_info[$name]['tmp_name'])) {
				//echo "two";
				if($image_info_name !=''){
				//	echo "three";
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$image_info_name);
					$path         = addslashes($folderpath);
					$oldImage = $folderpath;
					unlink($oldImage);
					//echo $oldImage;exit;
				}
				 
				$image_type = explode("/",$image_info[$name]['type']);
				if($image_type[0] == "image"){
					//echo "four";					
					$sourcePath = $image_info[$name]['tmp_name'];
					$img_Name	= time().$new_str;
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$img_Name);
					$path         = addslashes($folderpath);
					if(move_uploaded_file($sourcePath,$folderpath)){
						echo $path;
						$image_info_name = $img_Name;
					}
					/*else{
						echo "gdsfgdf";
					}*/
				}else{
					$sourcePath = $image_info[$name]['tmp_name'];
					$img_Name	= time().$new_str;
					$targetPath = '/var/www/html/extras/custom/TMX1512291534825461/images/tour_uploads/';
					$folderpath   = trim($targetPath.$img_Name);
					$path         = addslashes($folderpath);
					if(move_uploaded_file($sourcePath,$folderpath)){
						echo $path;
						$image_info_name = $img_Name;
					}
					/*else{
						echo "gdsfgdf";exit;
					}*/
				}
			}				
		}
		//echo $image_info_name;exit("end");
		return $image_info_name;
	}
	
}