<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * provab
 *
 * Travel Portal Application
 *
 * @package provab
 * @author Balu A<balu.provab@gmail.com>
 * @copyright Copyright (c) 2013 - 2014
 * @link http://provab.com
 */
class Provab_Sms {
	public $CI; // instance of codeigniter super object
	public $sms_configuration; // sms configurations defined by user
	public function __construct($data = '') {
		if (valid_array ( $data ) == true and intval ( $data ['id'] ) > 0) {
			$id = intval ( $data ['id'] );
		} else {
			$id = GENERAL_SMS;
		}
		$this->CI = & get_instance ();
		$return_data = $this->CI->user_model->sms_configuration(GENERAL_SMS);
		$this->sms_configuration = $return_data;
	}
	/**
	 * switch statement to select sms-gateway
	 */
	public function send_msg($phone, $data, $sms_id) {
		$gateway = $this->sms_configuration->gateway;

		switch ($gateway) {
			case "infisms" :
				$this->infisms ( $phone, $msg );
				break;

			case "gup_shup" :
				$this->gup_shup($phone, $data, $sms_id);
				break;

			default :
				$status = false;
				return array (
						'status' => $status 
				);
				break;
		}
	}
	/**
	 * send sms to the user based on gateway from switch statement
	 */
	public function infisms($phone, $msg) {
		$username = $this->sms_configuration->username;
		$password = $this->sms_configuration->password;
		$msg_link = 'http://ip.infisms.com/smsserver/SMS10N.aspx?Userid=' . $username . '&UserPassword=' . $password . '&PhoneNumber=' . $phone . '&Text=' . $msg;
		
		//FIXME 
		
		/* $curl_handle = curl_init ();
		curl_setopt ( $curl_handle, CURLOPT_URL, $url );
		$status = curl_exec ( $curl_handle );
		curl_close ( $curl_handle ); */
		file_get_contents($url);
	}

	/**
	 * send sms to the user based on gateway from switch statement
	 */
	public function gup_shup($phone, $data, $sms_id) {
		$username = $this->sms_configuration->username;
		$password = $this->sms_configuration->password;
		$url = $this->sms_configuration->url;

		$msg = $this->get_msg_from_sms_template($data, $sms_id);
		
		$request =""; 
		$param["method"] = "sendMessage";
		$param["send_to"] = $phone;
		$param["msg"] = $msg; 
		$param["userid"] = $username; 
		$param["password"] = $password; 
		$param["v"] = "1.1";
		$param["msg_type"] = "TEXT";
		foreach($param as $key=>$val) {
			$request.= "&".$key."=".urlencode($val);
		}		
		$request = substr($request, 0, strlen($request));
		$url = $url.ltrim($request, "&");
		if(get_client_ip()=="223.186.12.235"){
			//debug($url); exit;
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$curl_scraped_page = curl_exec($ch);
		curl_close($ch); 
		return $curl_scraped_page;
	}
	function get_bus_booking_cancel_msg($data)
	{
		//Gathering Information about passenger, agent & journey
		$info = $data["data"]["booking_details"][0];
		$pnr = $info["pnr"];
		$agent_phone = $info["phone_number"];
		$agency_name = $GLOBALS["CI"]->agency_name;
		$lead_phone = $info["lead_pax_phone_number"];
		$lead_name = $info["lead_pax_name"];
		$operator = $info["operator"];
		$source = $info["departure_from"];
		$destination = $info["arrival_to"];
		$dep_datetime = $info["departure_datetime"];
		$arr_datetime = $info["arrival_datetime"];
		$sNdt = $source." ".$dep_datetime." to ".$destination." ".$arr_datetime;
		//@variable bid - booking itenerary details
		$bid = $info["booking_itinerary_details"][0];
		$boarding_point = $bid["boarding_from"];
		//@variable bcd - booking itenerary details
		$bcd = $info["booking_customer_details"];
		$seat_nos="";
		foreach($bcd AS $bc)
		{
			$seat_nos .= $bc["seat_no"]." ";
		}
		$need_replace_arr = array("[PASSENGERNAME]", "[OPERATORNAME]", "[SOURCEDESTINATIONTIME]",
								 "[SEAT]", "[PNR]", "[TIME]", "[BOARDING]", "[AGENT NUMBER]", 
								 "[AGENCYNAME]");
		
		$replacement_arr = array($lead_name, $operator, $sNdt, $seat_nos, $pnr, $dep_datetime,
								$boarding_point, $agent_phone, $agency_name);

		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);

	}
	function get_flight_booking_cancel_msg($data,$sms_id)
	{
		//debug($data); exit;
		//Gathering Information about passenger, agent & journey
		if(isset($data["data"]))
			$info = $data["data"]["booking_details"][0];
		else
			$info = $data["booking_details"][0];
		$pnr = $info["pnr"];
		if(!isset($pnr) || empty($pnr)==TRUE){
			$pnr = $info["booking_transaction_details"][0]["book_id"];
		}
		$agent_phone = $info["phone_number"];
		$agent_email = $info["email"];
		$agency_name = $GLOBALS["CI"]->agency_name;
		$lead_phone = $info["lead_pax_phone_number"];
		$lead_name = $info["lead_pax_name"];
		$operator = $info["operator"];
		$trip_type = $info["trip_type_label"];
		$bids = $info["booking_itinerary_details"];
		$sNdt = "";
		if($sms_id == 654507){
			$sup = '';
			if($info['booking_source'] == 'PTBSID0000000012'){ $sup = "Star Air"; }
            if($info['booking_source'] == 'PTBSID0000000009'){ $sup = "ACH"; }
            if($info['booking_source'] == 'PTBSID0000000010'){ $sup = "GDS"; }
            if($info['booking_source'] == 'PTBSID0000000011'){ $sup = "SpiceJet"; }
            if($info['booking_source'] == 'PTBSID0000000013'){ $sup = "Indigo"; }
            if($info['booking_source'] == 'PTBSID0000000045'){ $sup = "TruJet"; }
            if($info['booking_source'] == 'PTBSID0000000002'){ $sup = "TBO"; }
            if(isset($info['booking_transaction_details'][0]['gds_pnr']) && !empty($info['booking_transaction_details'][0]['gds_pnr'])){
            	$pnr = $pnr.' GDS pnr-'.$info['booking_transaction_details'][0]['gds_pnr'];
            }
            $airline_name = $info['booking_itinerary_details'][0]['airline_name'];
            $cancellation_type = 'Full Cancellation';
            $cancel_count = 0;
            $total_pass = count($info['booking_transaction_details'][0]['booking_customer_details']);
            foreach ($info['booking_transaction_details'][0]['booking_customer_details'] as $key => $value) {
            	if($value['status'] == 'BOOKING_INPROGRESS'){
            			$cancel_count++;
            	}
            }
            if($total_pass == $cancel_count){
            	$cancellation_type = 'Full Cancellation';
            }else{
            	$cancellation_type = 'Partial Cancellation';
            }
			$app_ref = $info['app_reference'];
			$agent_id = $GLOBALS["CI"]->entity_uuid;
			$flight_type_sup = $sup.'-'.$airline_name;
			$need_replace_arr = array("[TBO/GDS/GDS void]", "[Full/Partial]", "[AGENTID]", "[PNR]", "[AIRPNR]");
			$replacement_arr = array($flight_type_sup, $cancellation_type, $agent_id, $app_ref, $pnr);
			return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
		}elseif($sms_id == 568350){
			$app_ref = $info['app_reference'];
			$need_replace_arr = array("[PNR]");
			$replacement_arr = array($app_ref);
			return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
		}else{
		foreach($bids AS $bid_key => $bid)
		{
			$airline_name = $bid["airline_name"]." (".$bid["airline_code"]."-".$bid["flight_number"].")";
			$dep_ap = $bid["from_airport_name"]."(".$bid["from_airport_code"].")";
			$arr_ap = $bid["to_airport_name"]."(".$bid["to_airport_code"].")";
			$dep_datetime = $bid["departure_datetime"];
			$arr_datetime = $bid["arrival_datetime"];
			$sNdt .= $airline_name." - ".$dep_ap." ".$dep_datetime." to ".$arr_ap." ".$arr_datetime."";
		}
		$need_replace_arr = array("[PASSENGERNAME]", "[PNR]", "[SOURCEDESTINATIONTIME]", "[AGENCYNAME]",
			"[TBO/GDS/GDS void]", "[AGENTID]", "[AIRPNR]");
		
		$replacement_arr = array($lead_name, $pnr, $sNdt, $agency_name, "flight", $agent_email, $pnr);
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	  }
	}

	function get_flight_booking_agent_msg($data)
	{
		//debug($data); exit;
		//Gathering Information about passenger, agent & journey
		if(isset($data["data"]))
			$info = $data["data"]["booking_details"][0];
		else
			$info = $data["booking_details"][0];
		$pnr = $info["pnr"];
		if(!isset($pnr) || empty($pnr)==TRUE){
			$pnr = $info["booking_transaction_details"][0]["book_id"];
		}
		//**************************New SMS Template***************************
		$agent_phone = $info["phone_number"];
		$agent_email = $info["email"];
		$agency_name = $GLOBALS["CI"]->agency_name;
		$lead_phone = $info["lead_pax_phone_number"];
		$lead_name = $info["lead_pax_name"];
		$operator = $info["operator"];
		$trip_type = $info["trip_type_label"];
		$bids = $info["booking_itinerary_details"];
		$sNdt = "";
		$guest = "";
		foreach($info['booking_transaction_details'][0]['booking_customer_details'] AS $guest_key => $guest_val){
			$num = $guest_key + 1;
			$guest .= $num.". ".$guest_val['title']." ".$guest_val['first_name']." ".$guest_val['last_name']." ";
		}
		$guest_text = $guest.", Happy Journey From ".$agency_name;
		$guest = trim($guest_text);
		$source = $destination = $via = '';
		foreach($bids AS $bid_key => $bid)
		{
			$count = count($bids);
			$attr_details = json_decode($bid['attributes'],true);
			if($trip_type == 'Oneway' || $trip_type == 'oneway'){ //oneway
				if($count < 2){
					//**********************************single flight******************************************
					$from_to = $attr_details['ws_val']['OriginDetails']['CityName']." to ".$attr_details['ws_val']['DestinationDetails']['CityName'];
					$airline_name = "Departure Airline is ".$bid["airline_name"]." (".$bid["airline_code"].")"."-".$bid["flight_number"];
					$dep_ap = "(".$bid["from_airport_code"].")";
					$arr_ap = "(".$bid["to_airport_code"].")";

					$dep_datetime = explode(" ",$bid["departure_datetime"]);
					$dep_time1 = explode(":",$dep_datetime[1]);
					$dep_time = $dep_time1[0].":".$dep_time1[1];
					$dep_date = date('d-M-Y',strtotime($dep_datetime[0]));
					$arr_datetime = explode(" ",$bid["arrival_datetime"]);
					$arr_time1 = explode(":",$arr_datetime[1]);
					$arr_time = $arr_time1[0].":".$arr_time1[1];
					$arr_date = date('d-M-Y',strtotime($arr_datetime[0]));
				}else{
					//**********************************Connecting flight******************************************
					if($bid_key == $count-1){
						$destination = $attr_details['ws_val']['DestinationDetails']['CityName'];
						$from_to = $source." to ".$destination.$via;
					}else{
						$via .= " via ".$attr_details['ws_val']['DestinationDetails']['CityName'];
					}
					if($bid_key == 0){
						$source = $attr_details['ws_val']['OriginDetails']['CityName'];
						$airline_name = "Departure Airline is ".$bid["airline_name"]." (".$bid["airline_code"].")"."-".$bid["flight_number"];
						$dep_ap = "(".$bid["from_airport_code"].")";
						$arr_ap = "(".$bid["to_airport_code"].")";

						$dep_datetime = explode(" ",$bid["departure_datetime"]);
						$dep_time1 = explode(":",$dep_datetime[1]);
						$dep_time = $dep_time1[0].":".$dep_time1[1];
						$dep_date = date('d-M-Y',strtotime($dep_datetime[0]));
						$arr_datetime = explode(" ",$bid["arrival_datetime"]);
						$arr_time1 = explode(":",$arr_datetime[1]);
						$arr_time = $arr_time1[0].":".$arr_time1[1];
						$arr_date = date('d-M-Y',strtotime($arr_datetime[0]));
					}

				}
			}else{ 
				//**********************************Round flight******************************************
				if($bid_key == $count-1){
						$source1 = $attr_details['ws_val']['OriginDetails']['CityName'];
						$destination1 = $attr_details['ws_val']['DestinationDetails']['CityName'];
						$from_to1 = "Outward: ".$source1." to ".$destination1;
						$airline_name1 = " Departure Airline is ".$bid["airline_name"]." (".$bid["airline_code"].")"."-".$bid["flight_number"];
						$dep_ap1 = "(".$bid["from_airport_code"].")";
						$arr_ap1 = "(".$bid["to_airport_code"].")";

						$dep_datetime1 = explode(" ",$bid["departure_datetime"]);
						$dep_time11 = explode(":",$dep_datetime[1]);
						$dep_time1 = $dep_time11[0].":".$dep_time11[1];
						$dep_date1 = date('d-M-Y',strtotime($dep_datetime[0]));
						$arr_datetime1 = explode(" ",$bid["arrival_datetime"]);
						$arr_time11 = explode(":",$arr_datetime[1]);
						$arr_time1 = $arr_time11[0].":".$arr_time11[1];
						$arr_date1 = date('d-M-Y',strtotime($arr_datetime[0]));
						$guest = $from_to1." Dep : ".$dep_time1.", ".$dep_date1.", Arr: ".$arr_time1.", ".$arr_date1.",".$airline_name1." ".$guest;
				}else{
					if($bid_key == 0){
						$source = $attr_details['ws_val']['OriginDetails']['CityName'];
						$destination = $attr_details['ws_val']['DestinationDetails']['CityName'];
						$from_to = "Inward: ".$source." to ".$destination;
						$airline_name = "Departure Airline is ".$bid["airline_name"]." (".$bid["airline_code"].")"."-".$bid["flight_number"];
						$dep_ap = "(".$bid["from_airport_code"].")";
						$arr_ap = "(".$bid["to_airport_code"].")";

						$dep_datetime = explode(" ",$bid["departure_datetime"]);
						$dep_time1 = explode(":",$dep_datetime[1]);
						$dep_time = $dep_time1[0].":".$dep_time1[1];
						$dep_date = date('d-M-Y',strtotime($dep_datetime[0]));
						$arr_datetime = explode(" ",$bid["arrival_datetime"]);
						$arr_time1 = explode(":",$arr_datetime[1]);
						$arr_time = $arr_time1[0].":".$arr_time1[1];
						$arr_date = date('d-M-Y',strtotime($arr_datetime[0]));
					}
				}
			}
		} 
		$need_replace_arr = array("[SOURCEDESTINATIONTIME]","[TIME]","[DATE]","[ARRTIME]","[ARRDATE]","[FLIGHTDETAILS]","[PNR]","[PASSENGERNAME]"); //new
		//$need_replace_arr = array("[PASSENGERNAME]", "[PNR]", "[SOURCEDESTINATIONTIME]", "[AGENCYNAME]","[TBO/GDS/GDS void]", "[AGENTID]", "[AIRPNR]");//old
		$replacement_arr = array($from_to, $dep_time, $dep_date, $arr_time, $arr_date, $airline_name, $pnr, $guest);//new
		//$replacement_arr = array($lead_name, $pnr, $sNdt, $agency_name, "flight", $agent_email, $pnr); //old
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_flight_booking_admin_msg($data)
	{
		//debug($data); exit;
		//Gathering Information about passenger, agent & journey
		if(isset($data["data"]))
			$info = $data["data"]["booking_details"][0];
		else
			$info = $data["booking_details"][0];
		$pnr = $info["pnr"];
		if(!isset($pnr) || empty($pnr)==TRUE){
			$pnr = $info["booking_transaction_details"][0]["book_id"];
		}
		$agent_phone = $info["phone_number"];
		$agent_email = $info["email"];
		$agency_name = $GLOBALS["CI"]->agency_name;
		$lead_phone = $info["lead_pax_phone_number"];
		$lead_name = $info["lead_pax_name"];
		$operator = $info["operator"];
		$trip_type = $info["trip_type_label"];
		$bids = $info["booking_itinerary_details"];
		$sNdt = "";
		
		foreach($bids AS $bid_key => $bid)
		{
			// debug($bid);exit;
			$airline_name .= $bid["airline_name"]." (".$bid["airline_code"]."-".$bid["flight_number"]."), ";
			$dep_ap = "(".$bid["from_airport_code"].")";
			$arr_ap = "(".$bid["to_airport_code"].")";
			$dep_datetime = explode(" ", $bid["departure_datetime"]);
			$arr_datetime = explode(" ", $bid["arrival_datetime"]);
			$sNdt .= $dep_ap." to ".$arr_ap.", ";
			$dep_time .= $dep_datetime[0].", ";
			$arr_time .= $arr_datetime[0].", ";
			$dep_date .= $dep_datetime[1].", ";
			$arr_date .= $arr_datetime[1].", ";
		
		}
		$sNdt = substr($sNdt, 0, -2);
		$dep_time = substr($dep_time, 0, -2);
		$arr_time = substr($arr_time, 0, -2);
		$dep_date = substr($dep_date, 0, -2);
		$arr_date = substr($arr_date, 0, -2);
		$airline_name = substr($airline_name, 0, -2);
		
		$need_replace_arr = array("[SOURCEDESTINATIONTIME]", "[TIME]","[DATE]", "[ARRTIME]", "[ARRDATE]", "[FLIGHTDETAILS]", "[PNR]", "[PASSENGERNAME]");
		// debug($bids);exit;
		$replacement_arr = array($sNdt, $dep_time, $dep_date, $arr_time, $arr_date, $airline_name, $pnr, $lead_name);
		
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	
	function get_hotel_booking_cancel_msg($data)
	{
		//debug($data); exit;
		//Gathering Information about passenger, agent & journey
		if(isset($data["data"]))
			$info = $data["data"]["booking_details"][0];
		else
			$info = $data["booking_details"][0];
		$pnr = $info["booking_id"];
		$agent_phone = $info["phone_number"];
		$agent_email = $info["email"];
		$agency_name = $GLOBALS["CI"]->agency_name;
		$lead_phone = $info["lead_pax_phone_number"];
		$lead_name = $info["lead_pax_name"];
		$hotel_name = $info["hotel_name"];

		$bid = $info["itinerary_details"][0];
		$location = $bid["location"];
		$check_in = $bid["check_in"];
		$check_out = $bid["check_out"];
		$room_type_name = $bid["room_type_name"];
		$hotel_name_location .= $hotel_name.", ".$bid["location"];

		$need_replace_arr = array("[PASSENGERNAME]", "[HOTELNAME]", "[CHECKINDATE]", "[CHECKOUTDATE]", 
		"[PNR]", "[AGENCYNAME]", "[AGENTNUMBER]");
		
		$replacement_arr = array($lead_name, $hotel_name_location, $check_in, $check_out, $pnr, 
			$agency_name, $agent_phone);

		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_booking_failed_auto_refund_msg($data)
	{
		//debug($data);
		$amount = $data["pgi_amount"];
		$app_ref = $data["txnid"];
		$pg_txnid = $data["pg_txnid"];
		$course_id = $data["productinfo"];
		$product = $this->CI->custom_db->single_table_records("meta_course_list", "*", array("course_id" 
			=> $course_id))["data"][0];
		$type = $product["name"];
		//debug($product); exit;
		$need_replace_arr = array("[TYPE]", "[AMOUNT]", "[BANKTXNID]");
		
		$replacement_arr = array($type, $amount, $pg_txnid); 

		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_otp_msg($otp)
	{
		$need_replace_arr = array("[OTP]");
		$replacement_arr = array($otp);
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_deposit($data)
	{
		$need_replace_arr = array("[AMOUNT]","[AGENT_ID]");
		$replacement_arr = array($data['amount'], $data['agency']);
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_supplier_low_balance_msg($data){
		$need_replace_arr = array("[AGENTID]", "[AIRLINENAME]", "[SECTOR]","[NOOFPASSENGERS]","[BOOKINGAMOUNT]","[BALANCEAMOUNT]");
		$replacement_arr = array($data['agent_id'], $data['airline'], $data['sector'], $data['no_of_passenger'], $data['ticket_amount'],$data['balance_amount']); 
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_flight_ticket_agent_block($data){
		$need_replace_arr = array("[TYPE]");
		$replacement_arr = array("GDS"); 
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_flight_ticket_admin_block($data){
		$need_replace_arr = array("[AGENTID]", "[GDSPNR]", "[PNR]");
		$replacement_arr = array($data['AgentId'], $data['GDSPNR'], $data['PACEPNR']); 
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	function get_flight_booking_agent($data){
		$need_replace_arr = array("[AGENTID]", "[GDSPNR]", "[PNR]");
		$replacement_arr = array($data['AgentId'], $data['GDSPNR'], $data['PACEPNR']); 
		return array("replace_these" => $need_replace_arr, "with_these" => $replacement_arr);
	}
	
	function get_msg_from_sms_template($data, $sms_id)
	{
		//@variable st - sms_template
		$st = $this->CI->user_model->sms_template($sms_id);
		//debug($st);die;
		$replacement_arr = array();
		//Switching the sms generators depending on types

		switch($st->category)
		{
			case "otp":
				$replacement_arr = $this->get_otp_msg($data);
				break;
		    case "deposit":
				$replacement_arr = $this->get_deposit($data);
				break;
			case "bus":
				$replacement_arr = $this->get_bus_booking_cancel_msg($data);
				break;
			case "flight":
				$replacement_arr = $this->get_flight_booking_cancel_msg($data,$sms_id);
				break;
			case "hotel":
				$replacement_arr = $this->get_hotel_booking_cancel_msg($data);
				break;
			case "refund":
				$replacement_arr = $this->get_booking_failed_auto_refund_msg($data);
				break;
			case "supplier_low_balance":
				$replacement_arr = $this->get_supplier_low_balance_msg($data);
				break;
			case "flight_ticket_agent_block":
				$replacement_arr = $this->get_flight_ticket_agent_block($data);
				break;
			case "flight_ticket_admin_block":
				$replacement_arr = $this->get_flight_ticket_admin_block($data);
				break;
			case "agent_flight_booking":
				$replacement_arr = $this->get_flight_booking_agent_msg($data);
				break;
			case "admin_flight_booking":
				$replacement_arr = $this->get_flight_booking_admin_msg($data);
				break;

		}

		$replace_these = $replacement_arr["replace_these"];
		$with_these = $replacement_arr["with_these"];
		$msg = str_replace($replace_these, $with_these, $st->template);
		//debug($msg); exit;
		return trim($msg);
	}

	function fire_sms_to_ccs($data, $sms_id)
	{
		$list = $this->CI->user_model->get_sms_cc_user_list($sms_id);
		foreach ($list as $key => $value) {
			$msg = $this->gup_shup($value["phone"], $data, $sms_id);
		}
	}

	function check_for_low_balance_n_alert()
	{
		$balance = agent_current_application_balance();
		if($balance["value"]<=AGENT_MINIMUM_BALANCE_AMOUNT)
		{
			$name = $GLOBALS['CI']->entity_name;
			$phone = $GLOBALS['CI']->entity_phone;
			$balance = $balance["value"];
			$msg = "Dear ".$name.", Your B2B Account is On Low Balance (".$balance["value"]."). Please recharge.";
			$this->send_msg($phone, $msg);
		}
	}
}
?>
