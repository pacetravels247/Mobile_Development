<?php
class Package_Model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function getAllPackages(){
		$this->db->select('*');
		$this->db->where('status', '1');
		$query = $this->db->get('package');
		if ( $query->num_rows > 0 ) {
			return $query->result();
		}else{
			return array();
		}
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
	
	/**
	 *@param Top Destination Packages
	 */
	public function get_package_top_destination()
	{
		$this->db->select('*');
		$this->db->where('top_destination',ACTIVE);
		$query = $this->db->get('package');
		if ( $query->num_rows > 0 ) {
			$data['data'] = $query->result();
			$data['total'] = $query->num_rows;
			return $data;
		}else{
			return array('data' => '', 'total' => 0);
		}
	}
	public function getPageCaption($page_name) {
		$this->db->where('page_name', $page_name);
		return $this->db->get('page_captions');
	}
	public function get_contact(){
		$contact = $this->db->get('contact_details');
		return $contact->row();
	}
	/**
	 *get country name
	 **/
	public function getCountryName($id){
		$this->db->select("*");
		$this->db->from("country");
		$this->db->where('country_id',$id);
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->row();
		}else{
			return array();
		}
	}

	/**
	 * get package itinerary
	 */
	public function getPackageItinerary($package_id){
		$this->db->select("*");
		$this->db->from("package_itinerary");
		$this->db->where('package_id',$package_id);
		$this->db->order_by('day','ASC');
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}
	}

	/**
	 * get package pricing policy
	 */
	public function getPackagePricePolicy($package_id){
		$this->db->select("*");
		$this->db->from("package_pricing_policy");
		$this->db->where('package_id',$package_id);
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->row();
		}else{
			return array();
		}
	}

	/**
	 * get package traveller photos
	 */
	public function getTravellerPhotos($package_id){
		$this->db->select("*");
		$this->db->from("package_traveller_photos");
		$this->db->where('package_id',$package_id);
		$this->db->where('status','1');
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}
	}
	/*8
	 * get getPackageCancelPolicy
	 */
	public function getPackageCancelPolicy($package_id){
		$this->db->select("*");
		$this->db->from("package_cancellation");
		$this->db->where('package_id',$package_id);
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->row();
		}else{
			return array();
		}
	}
	/**
	 * getPackage
	 */
	public function getPackage($package_id){
		$this->db->select("*");
		$this->db->from("package");
		$this->db->where('package_id',$package_id);
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->row();
		}else{
			return array();
		}
	}
	public function getPackageCountries_new(){
    	$data = 'select C.name AS country_name, C.country_id as country_id FROM country C';
    
    	return $this->db->query($data)->result();
    	/*$this->db->select('package_country');
    	 $this->db->from('package'); 
    	 $this->db->group_by('package_country'); 
		$query = $this->db->get();
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}*/
    }

	public function saveEnquiry($data){
		$this->db->insert('package_enquiry',$data);
		return $this->db->insert_id();
	}

	public function getPackageCountries(){
		$data = 'select package_country, C.name AS country_name FROM package P, country C WHERE P.package_country=C.country_id';
    	return $this->db->query($data)->result();
	}
	public function gerEnquiryPackages($user_id){
		$data = 'select * from package_enquiry WHERE user_id='.$user_id;
    	return $this->db->query($data)->result();
	}
	public function getPackageTypes(){
		$this->db->select("*");
		$this->db->from("package_types");
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}
	}
	public function search($c,$p,$d,$b,$dmn_list_fk){
		$this->db->select("*");
		$this->db->from("package");
		$this->db->like('package_country', $c,'both');
		$this->db->like('package_type', $p,'both');
		if($d){
			$this->db->where($d);
		}else{
			$this->db->like('duration', $d,'both');
		}
		if($b){
			$this->db->where($b);
		}else{
			$this->db->like('price', $b,'both');
		}
		$this->db->where('domain_list_fk',$dmn_list_fk);
		$query=$this->db->get();
		//echo $this->db->last_query();
		//exit;
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}
	}


	function add_user_rating($arr_data)
	{
		$pkg_id=$arr_data['package_id'];
		$res=$this->db->insert('package_rating',$arr_data);

		if($res==true){
			 
			$this->db->select('rating');
			$this->db->where('package_id',$pkg_id);
			$res1=$this->db->get('package_rating');
			if($res1->num_rows()>0)
			{  // print_r($res1);
				$tot_no=count($res1->result());
				$results=$res1->result();
				//   sum=0;
				foreach($results as $r)
				{
					$sum+=$r->rating;
				}
				$rating=$sum/$tot_no;

				$da=array('rating'=> ceil($rating));
				$this->db->where('package_id',$pkg_id);
				$this->db->update('package',$da);

			}
			 
		}

	}

	//*--Group booking request start--*//
	public function add_group_request($grouprequest) {
		$this->db->insert ('group_request',$grouprequest);
		return $this->db->insert_id ();
	}
	//*--Group booking request end--*//
	public function get_tour_type()
	{

		$query = 'select *, tt.id as tour_type_id from tour_type AS tt LEFT JOIN tour_package_map AS tpm ON tt.id=tpm.type_id INNER JOIN tours AS t ON tpm.tour_id=t.id where tt.status = 1 GROUP BY tt.id order by tt.tour_type_name '; 
		
		
		//$query = 'SELECT * FROM `tours` AS tr INNER JOIN `tour_type` AS tt ON tt.id IN (tr.tour_type) ;'
		//echo $query; exit;
	$exe   = $this->db->query ( $query )->result_array ();
		// $exe   = mysql_query($query);
		$result = array();
		foreach($exe as $fetch)
		{
			$result[] = $fetch;
		}
	//	debug($query); 
		//debug($result); exit("kkk");
		return $result;
	}
	
	public function get_package_list($tour_type,$order='',$search_val){
		
		$type=rtrim($tour_type);
		$search_val=trim($search_val);
		//echo $type.'|'.$search_val;exit;
		$today=date("Y-m-d");
		
		if($order=='Name'){
			$order_text="ORDER BY t.package_name";
			//echo $order_text;exit('test');
		}else if($order=='No. of Days'){
			$order_text="ORDER BY t.duration";
			//echo $order_text;exit('test');
		}else if($order=='Price'){
			$order_text="ORDER BY tprm.airliner_price";
			//echo $order_text;exit('test');
		}
		
		if($type=='City'){
			$city='SELECT * FROM `tours_city` AS tc WHERE tc.CityName="'.$search_val.'"';
			$city_result= $this->db->query($city)->result_array();
			$city_id=$city_result[0]['id'];
			$data = 'SELECT *,t.id as pack_id FROM `tours` AS t LEFT JOIN tours_city_wise as tcw ON tcw.tour_id=t.id INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE  tcw.city_id = '.$city_id.'  GROUP BY tprm.tour_id '.$order_text.' ASC';
			//echo $data;exit;
		}else if($type=='Country'){
			$country='SELECT * FROM `tours_country` AS tc WHERE tc.name="'.$type[0].'"';
			$country_result= $this->db->query($country)->result_array();
			$country_id=$country_result[0]['id'];
			$data = 'SELECT *,t.id as pack_id FROM `tours` AS t LEFT JOIN tours_country_wise as tcw ON tcw.tour_id=t.id  INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE (tprm.from_date<= "'.$today.'" AND tprm.to_date>= "'.$today.'") AND tcw.country_id = '.$country_id.'  GROUP BY tprm.tour_id '.$order_text.' ASC';
		}else if($type=='Package'){
			$data = 'SELECT *,t.id as pack_id FROM `tours` AS t  INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE t.package_name LIKE "%'.$search_val.'%" GROUP BY tprm.tour_id';
			//echo $data;exit;
		}else{
			$data = 'SELECT *,t.id as pack_id FROM `tours` AS t INNER JOIN `tour_package_map` AS tpm ON tpm.tour_id=t.id INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id  WHERE (tprm.from_date<= "'.$today.'" AND tprm.to_date>= "'.$today.'") AND tpm.type_id='.$tour_type.'  GROUP BY tprm.tour_id '.$order_text.' ASC';
		}
		//echo $data;
		$result= $this->db->query($data)->result_array();
		return $result;
		//debug($result);exit;
	}
	public function get_package_list_auto_fill($search_params){
		$query1="SELECT CityName as name FROM `tours_city` WHERE `CityName` LIKE '%".$search_params."%' ";
		//echo $query1;
		$exe1   = $this->db->query ( $query1 )->result_array ();
		$result1 = array();
		$count=0;
		foreach($exe1 as $fetch)
		{
			$result1[] = $fetch;
			$result1[$count]['type']="City";
			$count++;
		}
		$query2="SELECT name as name FROM `tours_country` WHERE `continent`!= 'NULL' and `name` LIKE '%".$search_params."%' ";
		//echo $query2;
		$exe2   = $this->db->query ( $query2 )->result_array ();
		$result2 = array();
		$count=0;
		foreach($exe2 as $fetch)
		{
			$result2[] = $fetch;
			$result2[$count]['type']="Country";
			$count++;
		}
		//debug($result2); exit("kkk");
		
		$query3="SELECT package_name as name FROM `tours` WHERE `package_name` LIKE '%".$search_params."%' AND (publish_for='B2B_B2C' OR publish_for='B2B') AND package_status = 'VERIFIED'";
		//echo $query2;
		$exe3   = $this->db->query ( $query3 )->result_array ();
		$result3 = array();
		$count=0;
		foreach($exe3 as $fetch)
		{
			$result3[] = $fetch;
			$result3[$count]['type']="Package";
			$count++;
		}
		
		$result=array_merge($result1,$result2,$result3);
		return $result;
	}
	public function get_basic_details($id){
		$data = 'SELECT * FROM `tours` WHERE id = '.$id.'';
		return $this->db->query($data)->result_array();
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
	public function get_price_details($id){
		$data = 'SELECT * FROM `tour_price_management` WHERE tour_id = '.$id.'';
		return $this->db->query($data)->result_array();
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
	public function get_price_details_new($id,$module)
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
	public function tour_data($id) {

		$query = 'select * from tours where id='.$id.'';
		$result = $this->db->query ( $query )->result_array ();
		//echo $this->db->last_query();
		// $exe   = mysql_query($query);
		$fetch = array();
		$fetch = $result[0];
		
		return $fetch;
		
	}
	public function dep_date_data($id) {

		$query = 'select * from tour_dep_dates where tour_id='.$id.'';
		return $this->db->query($query)->result_array();
		
	}
	public function tour_types($id) {

		$query = 'select tt.tour_type_name,tt.id from tour_type tt INNER JOIN tour_package_map tpm ON tpm.type_id=tt.id INNER JOIN tours t ON t.id=tpm.tour_id where t.id='.$id.'';
		//echo $query;exit;
		return $this->db->query($query)->result_array();
		
	}
	public function  get_international_tour() {

		$query = 'select * from tours where trip_type="1"';
		//echo $query;exit;
		return $this->db->query($query)->result_array();
		
	}
	public function get_domestic_tour() {

		$query = 'select * from tours where trip_type="2"';
		//echo $query;exit;
		return $this->db->query($query)->result_array();
		
	}
	public function tour_country($id) {

		//$query = 'select tc.name,tc.id from tours_country tc INNER JOIN tours_country_wise tcw ON tcw.country_id=tc.id INNER JOIN tours t ON t.id=tcw.tour_id where t.id='.$id.'';
		$query = 'select tc.name,tc.id from tours_country tc where tc.id IN ('.$id.')';
		//echo $query;exit;
		return $this->db->query($query)->result_array();
		
	}
	public function tour_city($id) {

		//$query = 'select tc.CityName from tours_city tc INNER JOIN tours_city_wise tcw ON tcw.city_id=tc.id INNER JOIN tours t ON t.id=tcw.tour_id where t.id='.$id.'';
		$query = 'select tc.CityName from tours_city tc where tc.id IN ('.$id.')';
		
		//echo $query;exit;
		return $this->db->query($query)->result_array();
		
	}
	public function tour_ite_details($id) {

		$query = 'select * from tours_itinerary_dw where tour_id='.$id.'';
		//echo $query;exit;
		$result= $this->db->query($query)->result_array();
		$res=array();
		foreach($result as $res_key => $res_val){
		$city_name='';
			$city=json_decode($res_val['visited_city']);
			//$city=implode(',',$city);
			$res[$res_key]=$res_val;
			foreach($city as $k => $v){
				$query = 'select CityName from tours_city tc  where id='.$v.'';
				$result_city= $this->db->query($query)->result_array();
				$city_name.=$result_city[0]['CityName'].', ';
			}
			$res[$res_key]['city_name']=$city_name;
		
		}
		
			return $res;
	}
	public function top_attraction_package(){
		$today=date("Y-m-d");
		$query = "SELECT *,t.package_type as tour_pack_type,t.id as pack_id FROM `tours` AS t  INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id  WHERE   t.top_deal='1'  AND tprm.occupancy='10' AND tprm.package_type='B2B' AND (t.publish_for='B2B_B2C' OR t.publish_for='B2B') AND t.package_status = 'VERIFIED'";
		$result= $this->db->query($query)->result_array();
		return $result;
	}
	public function tours_enquiry_list($agent_id){
		$query = 'SELECT te.*, u.first_name AS u_fname, u.last_name AS u_lname, 
				u.uuid AS u_agency_id  FROM tours_enquiry te, user u WHERE u.user_id=te.created_by_id AND 
				te.created_by_id = '.$agent_id.' AND 
				te.status != "CONFIRMED" order by te.id DESC';
		$result= $this->db->query($query)->result_array();
		return $result;
	}
	public function custom_enquiry_list($agent_id){
		$query = 'SELECT te.*, u.first_name AS u_fname, u.last_name AS u_lname, u.phone AS u_phone,
				u.uuid AS u_agency_id  FROM custom_package_enquiry te, user u WHERE u.user_id=te.agent_id AND 
				te.agent_id = '.$agent_id.' AND 
				te.status != "CONFIRMED" order by te.id DESC';
		$result= $this->db->query($query)->result_array();
		return $result;
	}
	public function confirmed_tours_enquiry_list($agent_id,$condition=array()){
		$query = 'SELECT te.*, u.first_name AS u_fname, u.last_name AS u_lname, 
				u.uuid AS u_agency_id  FROM tours_enquiry te, user u WHERE u.user_id=te.created_by_id AND 
				te.created_by_id = '.$agent_id.' AND 
				te.status = "CONFIRMED" '.$condition.' order by te.id DESC';
				//echo $query;
		$result= $this->db->query($query)->result_array();
		return $result;
	}
	public function confirmed_custom_enquiry_list($agent_id,$condition=array()){
		$query = 'SELECT te.*, u.first_name AS u_fname, u.last_name AS u_lname, 
				u.uuid AS u_agency_id ,u.phone AS u_phone FROM custom_package_enquiry te, user u WHERE u.user_id=te.agent_id AND 
				te.agent_id = '.$agent_id.' AND 
				te.status = "CONFIRMED" '.$condition.' order by te.id DESC';
			
		$result= $this->db->query($query)->result_array();
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
	public function related_packages($country_id,$pack_id,$type)
	{
		if($type=='country'){
		
			$query = 'SELECT *,t.id as pack_id,tc.name as country_name,t.banner_image as pack_banner FROM `tours` AS t LEFT JOIN tours_country_wise as tcw ON tcw.tour_id=t.id INNER JOIN tours_country as tc ON tcw.country_id=tc.id INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE tprm.package_type="B2B" and tprm.occupancy="10" and tcw.country_id = '.$country_id.'  AND (t.publish_for="B2B_B2C" OR t.publish_for="B2B") AND t.package_status = "VERIFIED" AND t.id!='.$pack_id.' GROUP BY tprm.tour_id ORDER BY t.package_name ASC';
			//echo $query;exit;
			$result= $this->db->query($query)->result_array();
		}else{
			$query = 'SELECT *,t.id as pack_id,tc.name as country_name,t.banner_image as pack_banner FROM `tours` AS t LEFT JOIN tours_country_wise as tcw ON tcw.tour_id=t.id LEFT JOIN tour_package_map as tpm ON tpm.tour_id=t.id INNER JOIN tours_country as tc ON tcw.country_id=tc.id INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE tprm.package_type="B2B" and tprm.occupancy="10" and tpm.type_id = '.$country_id.'  AND (t.publish_for="B2B_B2C" OR t.publish_for="B2B") AND t.package_status = "VERIFIED" AND t.id!='.$pack_id.' GROUP BY tprm.tour_id ORDER BY t.package_name ASC';
			//echo $query;exit;
			$result= $this->db->query($query)->result_array();
		}
		
		return $result;
	}	
	public function opt_tour_details($pack_id)
	{
		$query = 'SELECT *
						,t.id as pack_id
						,tc.CityName as city_name
						,t.banner_image as pack_banner 
						,totm.id as opt_id 
				FROM `tours` AS t 
				LEFT JOIN tour_optional_tour_details as totd ON totd.tour_id=t.id 
				INNER JOIN tour_optional_tours_master as totm ON totm.id=totd.optional_tour  
				LEFT JOIN tours_city as tc ON totm.city=tc.id where t.id= '.$pack_id.'
				ORDER BY t.package_name ASC';
		//	echo $query;exit;
			$result= $this->db->query($query)->result_array();
		
		return $result;
	}	
	public function package_markup($module_type){
		$query = 'SELECT 
						module_type 
						,value
				FROM `markup_list` where user_oid= '.$this->entity_user_id.' and module_type="'.$module_type.'"';
		//echo $query;exit;
			$result= $this->db->query($query)->result_array();
		
		return $result;
	}
	public function selected_optional_tour($opt_id){
		$query = 'SELECT *  
					FROM tour_optional_tours_master where id IN ('.$opt_id.')';
		//echo $query;exit;
			$result= $this->db->query($query)->result_array();
		
		return $result;
	}
	
	public function save_booking($book_id, $temp_booking, $current_module){
		//debug($book_id);
		//debug($temp_booking);exit;
		//debug($current_module);
		
		$valid_temp_token = (array)json_decode(base64_decode($temp_booking['book_attributes']['pre_booking_params']));
		$price_details = (array)$valid_temp_token['package_price_details'];
		//debug($temp_booking);
		//exit;
		$attributes = array(
            'enquiry_reference_no' => @$temp_booking['book_id'],
            'price_type' => @$temp_booking['book_attributes']['pay1'],
            // 'billing_country' => @$country_name [$params ['booking_params'] ['billing_country']],
            // 'billing_city' => $city_name[$params['booking_params']['billing_city']],
            'adult_count' => array_sum($valid_temp_token['adult']),
            'child_count' => array_sum($valid_temp_token['child_with_bed'])+ array_sum($valid_temp_token['child_without_bed']),
            'infant_count' => array_sum($valid_temp_token['infant']),
            'new_price' =>@$temp_booking['book_attributes']['markup_price_summary']['RoomPrice'],
            'adult_price' => @$temp_booking['book_attributes']['tour_adult_price'],
            'child_price' => @$temp_booking['book_attributes']['tour_child_wb_price']+$temp_booking['book_attributes']['tour_child_wob_price'],
			'child_wb_price' => @$temp_booking['book_attributes']['tour_child_wb_price'],
			'child_wob_price' => @$temp_booking['book_attributes']['tour_child_wob_price'],
            'infant_price' => @$temp_booking['book_attributes']['tour_infant_price'],
			'optour_adult_price' => @$temp_booking['book_attributes']['opt_tour_adult'],
            'optour_child_price' => @$temp_booking['book_attributes']['opt_tour_child'],
            'optour_infant_price' => @$temp_booking['book_attributes']['opt_tour_infant'],
            'quote_type' => 'final_quote',
            'currency' => 'INR',
            'total' => @$temp_booking['book_attributes']['markup_price_summary']['RoomPrice'],
            'tour_id' => $valid_temp_token['package_details'][0]->id,
            'departure_date' => @$valid_temp_token['dep_date'],
            'pendingAmount' => 0,
			'roomCount' => @$valid_temp_token['no_rooms']
        );
		$attr=json_encode ( $attributes );
		
		
		$insert_array =array(
			'enquiry_reference_no' 	=> 	@$temp_booking['book_id'],
			'booking_source'		=>	@$temp_booking['booking_source'],
			'app_reference'		   	=>	@$temp_booking['book_id'],
			'status' 				=>	'BOOKING_CONFIRMED',
			'remarks'				=>	'',
			'basic_fare'			=>	@$temp_booking['book_attributes']['markup_price_summary']['RoomPrice'],
			'currency_code'			=>	'INR',
			'service_tax'			=>	0,
			'discount'				=>	0,
			'promocode'				=>	0,
			'payment_status'		=>  'paid',
			'created_by_id'			=>  $this->entity_user_id,
			'booked_by_id'			=>	$this->entity_user_id,
			'created_by'			=>	'agent',
			'attributes'			=>	$attr,
			'agent_markup'			=>	@$temp_booking['book_attributes']['agent_markup'],
			'user_attributes'		=>	'',
			'email'					=> 'test_pack@gmail.com',
			
		);
		$res=$this->db->insert('tour_booking_details',$insert_array);
		//echo $this->db->last_query();
		//debug($res);exit;
		if($res==1){
			$response ['booking_status'] = 'PROCESSING';
		}else{
			$response ['booking_status'] = 0;
		}
		$response ['fare'] = $temp_booking['book_attributes']['markup_price_summary']['RoomPrice'];
        $response ['admin_markup'] = 0;
        $response ['agent_markup'] = 30;
        $response ['convinence'] = 0;
        $response ['discount'] =0;
        return $response;
	}
	function get_booking_details($app_reference, $booking_source, $booking_status='')
    {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $bd_query = 'select * from tour_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
        if (empty($booking_source) == false) {
            $bd_query .= '  AND BD.booking_source = '.$this->db->escape($booking_source);
        }
        //if (empty($booking_status) == false) {
         //   $bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
      //  }
        $id_query = 'select * from tour_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
        $cd_query = 'select * from tour_booking_pax_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference);
		$tb_query = 'select * from  temp_booking AS TL WHERE TL.book_id ='.$this->db->escape($app_reference);
       // $cancellation_details_query = 'select HCD.* from sightseeing_cancellation_details AS HCD WHERE HCD.app_reference='.$this->db->escape($app_reference);
        $response['data']['booking_details']            = $this->db->query($bd_query)->result_array();
		//echo $this->db->last_query();exit;
        $response['data']['booking_itinerary_details']  = $this->db->query($id_query)->result_array();
        $response['data']['booking_customer_details']   = $this->db->query($cd_query)->result_array();
		$response['data']['temp_booking_details']		= $this->db->query($tb_query)->result_array();
        //$response['data']['cancellation_details']   = $this->db->query($cancellation_details_query)->result_array();
		//echo $this->db->last_query();exit;
        if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			
            $response['status'] = SUCCESS_STATUS;
        }
		if (valid_array($response['data']['booking_details']) == true) {
            $response['status'] = SUCCESS_STATUS;
        }
       // $response['data']['booking_details'][0]['attributes']= (array)json_decode($response['data']['booking_details'][0]['attributes']);
       // debug($response);die('88');
        return $response;
    }
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//$condition = $this->custom_db->get_custom_condition($condition);
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from tour_booking_details BD
					Left join tour_booking_pax_details BBCD on BD.app_reference=BBCD.app_reference 
					Left join tour_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
					where domain_origin='.get_domain_auth_id().' '.$condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id;
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
						WHERE BD.domain_origin='.get_domain_auth_id().''.$condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.'
						order by BD.origin desc limit '.$offset.', '.$limit;
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
							
				//Payment gateway history 
				$pg_query = 'select * from  payment_gateway_details AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
							
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$tl_details = $this->db->query($tl_query)->result_array();
				$tb_details = $this->db->query($tb_query)->result_array();
				$ph_details = $this->db->query($ph_query)->result_array();
				$pg_details = $this->db->query($pg_query)->result_array();
				//echo $this->db->last_query();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] = $tl_details;
			$response['data']['temp_booking_details'] = $tb_details;
			$response['data']['payment_history_details'] 	= $ph_details;
			$response['data']['payment_details'] 	= $pg_details;
			//debug($response);exit;
			return $response;
		}
	}
	   function upload_image_lgm($image_info,$name, $old_image_name='',$index){
		error_reporting(E_ALL);
		//debug($image_info);
		$image_info_name  = $old_image_name;
		if(!empty($image_info[$name]['name'][$index])){	
		//echo "one";
		    $new_str = str_replace(' ', '', $image_info[$name]['name'][$index]);
			if(is_uploaded_file($image_info[$name]['tmp_name'][$index])) {
				//echo "two";
				if($image_info_name !=''){
				//	echo "three";
					$targetPath = $this->template->domain_image_upload_path();
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
					$img_Name	= time().$index.$name.$new_str;
					$targetPath = $this->template->domain_image_upload_path();
					$folderpath   = trim($targetPath.$img_Name);
					$path         = addslashes($folderpath);
					if(move_uploaded_file($sourcePath,$folderpath)){
						//echo "five";
						$image_info_name = $img_Name;
					}else{
						//echo "gdsfgdf";exit;
					}
				}else{
					$sourcePath = $image_info[$name]['tmp_name'][$index];
					$img_Name	= time().$index.$name.$new_str;
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
	public function save_payment_details($book_id, $temp_booking, $module){
		$insert_array =array(
			'enquiry_reference_no' 	=> 	@$book_id,
			'total_trip_cost'		=>	@$temp_booking['book_attributes']['total_trip_with_gst_cost'],
			'paid_amount'		   	=>	@$temp_booking['book_attributes']['paid'],
			'Remaining' 			=>	@$temp_booking['book_attributes']['remaining_amount'],
			'payment_mode'			=>	@$temp_booking['book_attributes']['selected_pm'],
			'payment_type'			=>	@$temp_booking['book_attributes']['pay1'],
			'created_by'			=>	$this->entity_user_id,
			'payment_date'			=>	date('d-m-Y H:i:s'),
			'module'				=>	$module
			
		);
		$res=$this->db->insert('tour_payment_slab_details',$insert_array);
		//debug($res);exit;
	}
	public function update_payment_details($book_id, $temp_booking, $ref){
		$insert_array =array(
			'payment_mode'			=>	@$temp_booking['pg_name'],
			'payment_date'			=>	date('d-m-Y H:i:s'),
			
		);
		$con = array(
			'enquiry_reference_no'			=>	@$book_id,
			'id'			=>	$ref,
		);
		$res=$this->db->update('tour_payment_slab_details',$insert_array,$con);
		//debug($res);exit;
	}
}
