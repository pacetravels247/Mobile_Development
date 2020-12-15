<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tours extends CI_Controller {
	public function __construct(){
        parent::__construct();       
        $current_url = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';
        $current_url = $this->config->site_url().$this->uri->uri_string(). $current_url;
        $url =  array(
            'continue' => $current_url,
        );
        $this->session->set_userdata($url);
        $this->helpMenuLink = "";
        $this->load->model('Help_Model');
		$this->helpMenuLink = $this->Help_Model->fetchHelpLinks();						
		$this->load->model('Package_Model');		
		$this->load->library('provab_mailer');
		$this->load->library('api_balance_manager');
		$this->current_module = $this->config->item('current_module');
		$this->load->library('utility/notification', '', 'notification');
    }
    /**
    * get all tours
    **/
    public function index(){        
        $data['packages'] = $this->Package_Model->getAllPackages();
        $data['countries'] = $this->Package_Model->getPackageCountries();
        $data['package_types'] = $this->Package_Model->getPackageTypes();
        if(!empty($data['packages'])){            
            $this->template->view('holiday/tours', $data);
        }else{
            redirect();
        }
    }
    /**
     * get the package details
     */
    public function details($package_id){
       
        $data['package'] = $this->Package_Model->getPackage($package_id);
        $data['package_itinerary'] = $this->Package_Model->getPackageItinerary($package_id);
        $data['package_price_policy'] = $this->Package_Model->getPackagePricePolicy($package_id);
        $data['package_cancel_policy'] = $this->Package_Model->getPackageCancelPolicy($package_id);
        $data['package_traveller_photos'] = $this->Package_Model->getTravellerPhotos($package_id);
        if(!empty($data['package'])){
            $this->template->view('holiday/tours_detail', $data);
        }else{
            redirect("tours/");
        }
    }
    public function enquiry() {
        // echo 'herer I am';exit;
        $data = $this->input->post ();
        //debug($data);die();
        $package_id = $data['package_id'];
    
        if ($package_id !='') {
            $package = $this->Package_Model->getPackage ( $package_id );
            $data ['package_name'] = $package->package_name;
            $data ['package_duration'] = $package->duration;
            $data ['package_type'] = $package->package_type;
            $data ['with_or_without'] = $package->price_includes;
            $data ['package_description'] = $package->package_description;
            $data ['ip_address'] = $this->session->userdata ( 'ip_address' );
            $data ['status'] = '0';
            $data ['user_id'] = $this->entity_user_id;
            $data ['date'] = date ( 'Y-m-d H:i:s' );
            $data ['domain_list_fk'] = get_domain_auth_id ();

            //Added all field for PDO connection
            $data ['last_name'] = '';
            $data ['address'] = '';
            $data ['nationality'] = 'India';
            //debug($data);exit('////');   
            $result = $this->Package_Model->saveEnquiry ( $data );
            $status = true;
            $message = "Thank you for submitting your enquiry for this package, will get back to soon";
            header('content-type:application/json');
            echo json_encode(array('status' => $status, 'message' => $message));
            exit;
            
        } 
    }

    public function search(){
            $data = $this->input->get();
                $currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
            if(!empty($data)){
                    $country = $data['country'];
                    $packagetype = $data['package_type'];
                    if($data['duration']){
                        $duration = explode('-', $data['duration']);
                        if(count($duration)>1){
                            $duration = "duration between ".$duration['0']." AND ".$duration['1'];
                        }else{
                             $duration = "duration >".$duration['0'];
                        }
                    }else{
                        $duration = $data['duration'];
                    }
                    if($data['budget']){
                        $budget = explode('-', $data['budget']);

                        if(count($budget)>1){
                            $budget = "price between ".$budget['0']." AND ".$budget['1'];
                        }else if($budget[0]){
                            $budget = "price >".$budget['0'];
                        }
                    }else{
                        $budget = $data['budget'];
                    }
                    $domail_list_pk = get_domain_auth_id();
                    $data['currency_obj'] = $currency_obj;
                    $data['scountry'] = $country;
                    $data['spackage_type'] = $packagetype;
                    $data['sduration'] = $data['duration'];
                    $data['sbudget'] = $data['budget'];
                    $data['packages'] = $this->Package_Model->search($country,$packagetype,$duration,$budget,$domail_list_pk,$domail_list_pk);
                    $data['caption'] = $this->Package_Model->getPageCaption('tours_packages')->row();
                    $data['countries'] = $this->Package_Model->getPackageCountries();
                    $data['package_types'] = $this->Package_Model->getPackageTypes();
                    $this->template->view('holiday/tours', $data);

                    
            }else{
                redirect('tours/all_tours');
            }
    }
    
    
			function package_user_rating()
			 {  
				$rate_data=explode(',',$_POST['rate']);
				$pkg_id=$rate_data[0];
				$rating=$rate_data[1];

				$arr_data=array(
					'package_id'=> $pkg_id,
					'rating'=> $rating
				);
				$res=$this->Package_Model->add_user_rating($arr_data);
			  }


       public function all_tours(){
        $data['caption'] = $this->Package_Model->getPageCaption('tours_packages')->row();
        $data['packages'] = $this->Package_Model->getAllPackages();
        $data['countries'] = $this->Package_Model->getPackageCountries();
        $data['package_types'] = $this->Package_Model->getPackageTypes();
        if(!empty($data['packages'])){
            $this->template->view('holiday/tours', $data);
        }else{
            redirect();
        }
    }
	
	public function holiday_package_list($tour_type){
		
		$package_list= $this->Package_Model->get_package_list($tour_type,'Name');
		$data['package_list']=$package_list;
		$data['search_type']=$tour_type;
		$data['heading']='Category Result';
		//debug($data);
		$this->template->view('holiday/package_list', $data);
	}
	public function holiday_city_package_list($city){
		$city=str_replace('%20',' ',$city);
		$city_name=$city;
		$query='SELECT * FROM `tours_city` AS tc WHERE tc.CityName="'.$city.'"';
		$city_result= $this->db->query($query)->result_array();
		
		$city_id=$city_result[0]['id'];
		$city_image=$city_result[0]['banner_image'];

		//tprm.occupancy="10" and
		$query = 'SELECT *,t.id as pack_id,t.package_type as pack_type FROM `tours` AS t LEFT JOIN tours_city_wise as tcw ON tcw.tour_id=t.id INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE  tprm.occupancy="10" and (t.publish_for ="B2B" or t.publish_for ="B2B_B2C") and (t.package_status = "VERIFIED" or t.package_status = "PUBLISHED") and tcw.city_id = '.$city_id.'  GROUP BY tprm.tour_id ORDER BY t.package_name ASC';
		//echo $query;exit;
		$result= $this->db->query($query)->result_array();
		
		foreach($result as $res_key => $res_val){
			$data['package_list'][$res_key] = $res_val;
			$data['package_list'][$res_key]['country_name'] = $this->Package_Model->tour_country($res_val['tours_country']);
			$data['package_list'][$res_key]['city_name'] = $this->Package_Model->tour_city($res_val['tours_city']);
			if($res_val['trip_type']==1){
				$data['package_list'][$res_key]['markup'] = $this->Package_Model->package_markup('international_package')[0]['value'];
			}else{
				$data['package_list'][$res_key]['markup'] = $this->Package_Model->package_markup('domestic_package')[0]['value'];
			}
		}
		$data['package_list']=$data['package_list'];
		$data['search_type']=$tour_type;
		$data['heading']='city-'.$city;
		$data['search_dest']=$city_name;
		$data['search_dest_img']=$city_image;
		$data['user_id'] =$this->entity_user_id;
		$data['user_name'] =$this->entity_firstname;
		$data['user_email'] =$this->entity_email;
		$data['user_phone'] =$this->entity_phone;
		//debug($data);exit;
		$this->template->view('holiday/new_package_list', $data);
	}
	public function holiday_country_package_list($city){
		$city=str_replace('%20',' ',$city);
		//debug($city);
		$country='SELECT * FROM `tours_country` AS tc WHERE `continent`!= "NULL" and tc.name="'.$city.'"';
		$country_result= $this->db->query($country)->result_array();
		//debug($country_result);exit;
		$country_id=$country_result[0]['id'];
		$country_image=$country_result[0]['banner_image'];
		$query = 'SELECT *,t.id as pack_id,t.package_type as pack_type FROM `tours` AS t LEFT JOIN tours_country_wise as tcw ON tcw.tour_id=t.id  INNER JOIN tour_price_management  as tprm ON tprm.tour_id=t.id WHERE tprm.occupancy="10" and (t.publish_for ="B2B" or t.publish_for ="B2B_B2C") and (t.package_status = "VERIFIED" or t.package_status = "PUBLISHED") and tcw.country_id = '.$country_id.'  GROUP BY tprm.tour_id ORDER BY t.package_name ASC';
		//echo $query;exit;
		$result= $this->db->query($query)->result_array();
		//debug($result);exit;
		foreach($result as $res_key => $res_val){
			$data['package_list'][$res_key] = $res_val;
			$data['package_list'][$res_key]['country_name'] = $this->Package_Model->tour_country($res_val['tours_country']);
			$data['package_list'][$res_key]['city_name'] = $this->Package_Model->tour_city($res_val['tours_city']);
			if($res_val['trip_type']==1){
				$data['package_list'][$res_key]['markup'] = $this->Package_Model->package_markup('international_package')[0]['value'];
			}else{
				$data['package_list'][$res_key]['markup'] = $this->Package_Model->package_markup('domestic_package')[0]['value'];
			}
		}
		//debug($data['package_list']);exit;
		$data['package_list']=$data['package_list'];
		//$data['search_type']=$tour_type;
		$data['heading']='country-'.$city;
		$data['search_dest']=$city;
		$data['search_dest_img']=$country_image;
		$data['user_id'] =$this->entity_user_id;
		$data['user_name'] =$this->entity_firstname;
		$data['user_email'] =$this->entity_email;
		$data['user_phone'] =$this->entity_phone;
		//debug($data);exit;
		$this->template->view('holiday/new_package_list', $data);
	}
	public function holiday_package_listt(){
		//error_reporting(E_ALL);
		$search_params = $this->input->post();
		$search_val=explode(':',$search_params['sel_value']);
		$package_list= $this->Package_Model->get_package_list($search_val[0],'Name',$search_val[1]);
		//debug($package_list);exit;
		$data['package_list']=$package_list;
		$data['search_type']=$search_val[0];
		$data['heading']='Search Result';
		if($search_val[0]=="Package "){
			echo base_url().'tours/holiday_package_detail/'.$package_list[0]['pack_id'];exit;
			//redirect('tours/holiday_package_detail/'.$package_list[0]['pack_id']);
			//return $package_list[0]['pack_id'];
		}else if($search_val[0]=="City "){
		//	debug($data);exit;
			echo base_url().'tours/holiday_city_package_list/'.trim($search_val[1]);exit;
			//$this->template->view('holiday/package_list', $data);
			//$this->template->view('holiday/new_package_list', $data);
			
		}else if($search_val[0]=="Country "){
		//	debug($data);exit;
			echo base_url().'tours/holiday_country_package_list/'.trim($search_val[1]);exit;
			//$this->template->view('holiday/package_list', $data);
			//$this->template->view('holiday/new_package_list', $data);
			
		}
	}
	
	public function holiday_package_list_sort(){
		$search_params = $this->input->post();
		
		$package_list= $this->Package_Model->get_package_list($search_params['tour_type'],$search_params['sort_type']);
	
		
		$sort_html='';
		foreach($package_list as $pack_key => $pack_val){
			$day_duration=$pack_val['duration']+1;
			$sort_html.= '<div class="col-md-4 each_grid">
						<input type="hidden" class="price_val" value="'.$pack_val['airliner_price'].'">
							<div class="thumbnail">
								<a href="'.base_url().'index.php/tours/holiday_package_detail/'.$pack_val['pack_id'].'" target="_blank">
								  <img src="'.$GLOBALS['CI']->template->domain_images($pack_val['banner_image']).'" alt="Lights">
								  <div class="caption">
									<h4>'.$pack_val['package_name'].' ('.$day_duration.' Days | '.$pack_val['duration'].' Nights)</h4>
									<p>'.$pack_val['currency'].'  '.$pack_val['airliner_price'].'</p>
								  </div>
								</a>
							</div>
						</div>';
		}
		echo $sort_html;exit;
	}
    public function get_holiday_package_auto_fill(){
		$search_params = $this->input->get();
		$search_params=$search_params['search_val'];
		$package_list= $this->Package_Model->get_package_list_auto_fill($search_params);
		$package_result=array();
		//debug($package_list);
		$array_len=count($package_list);
		//echo $array_len;
		foreach ($package_list as $pack_key => $pack_val){
			$package_result[$pack_key]['label']=$pack_val['name'];
			$package_result[$pack_key]['category']=$pack_val['type'];
		}
		//debug($package_result);
		$json_result=json_encode($package_result);
		echo $json_result;
		//$fff= json_encode($package_list);
		//debug($fff);exit;
		
	}
	public function holiday_package_detail($pack_id,$prev_page=""){
		
		$page_data['package_details']= $this->Package_Model->get_basic_details($pack_id);
		$page_data['prev_page']= $prev_page;
		//$page_data['itinerary_details']= $this->Package_Model->get_basic_details($pack_id);
		$page_data['package_price_details']= $this->Package_Model->get_price_details_new($pack_id,'B2B');
		$page_data['dep_dates'] = $this->Package_Model->dep_date_data($pack_id);
		$page_data['tour_types'] = $this->Package_Model->tour_types($pack_id);
		$page_data['tour_visited_cities'] = $this->Package_Model->tour_visited_cities($pack_id);
		$page_data['tours_city_name'] = $this->Package_Model->tours_city_name();
		$page_data['tours_itinerary_dw'] = $this->custom_db->single_table_records('tours_itinerary_dw','*',array('tour_id'=>$pack_id));
		$page_data ['tours_hotel_det']   		= @$this->Package_Model->tour_hotel_city_data($pack_id);
		$b2b_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$pack_id." and package_type ='B2B' ");
		$page_data['b2b_tour_price'] = json_decode(json_encode($b2b_tour_data),true);
		
		
		$page_data['inter_markup'] = $this->Package_Model->package_markup('international_package');
		$page_data['domestic_markup'] = $this->Package_Model->package_markup('domestic_package');
		if($page_data['package_details'][0]['trip_type']=='1'){
			$page_data['markup_val'] = $this->Package_Model->package_markup('international_package');
		}else{
			$page_data['markup_val'] = $this->Package_Model->package_markup('domestic_package');
		}
		
		
		
		//debug($page_data);exit;
		$page_data['tours_itinerary_dw'] = ($page_data['tours_itinerary_dw']['status'])? $page_data['tours_itinerary_dw']['data']: NULL;
		$result=array();
		//foreach($page_data['tour_types'] as $tour_key =>$tour_val){
		//	$query=$this->Package_Model->get_package_list($tour_val['id']);
			
		//	foreach($query as $q_key => $q_val){
		//		$result[$q_val['pack_id']] = $q_val;
		//	}
			
		//}
		//debug($result);exit;
		$page_data['interested_package'] = $result;
		$country = $this->Package_Model->tour_country($page_data['package_details'][0]['tours_country']);
		$city = $this->Package_Model->tour_city($page_data['package_details'][0]['tours_city']);
		$countries=array();
		$related_packages=array();
		foreach($country as $c_val){
		   $countries[]=$c_val['name'];
		   $related_packages[]=$this->Package_Model->related_packages($c_val['id'],$pack_id,'country');
		} 
		//debug($countries); exit;
		
		$page_data['country']= implode(', ',$countries);
		$page_data['related_packages']=array();
		foreach($related_packages as $rel_val){
			foreach($rel_val as $rel_key => $related_val){
				$page_data['related_packages'][$rel_key]= $related_val;
			}
		}
		if(empty($page_data['related_packages'])){
			foreach($page_data['tour_types'] as $c_val){
			   $related_packages[]=$this->Package_Model->related_packages($c_val['id'],$pack_id,'category');
			} 
		
		}
		foreach($related_packages as $rel_val){
			foreach($rel_val as $rel_key => $related_val){
				$page_data['related_packages'][$rel_key]= $related_val;
			}
		}
		//debug($page_data['related_packages']);exit;
		$cities=array();
		foreach($city as $c_val){
		   $cities[]=$c_val['CityName'];
		} 
		
		
		$page_data['city']= implode(', ',$cities);
		$page_data['ite_details']=$this->Package_Model->tour_ite_details($pack_id);
		$page_data['optional_tour_details']=$this->Package_Model->opt_tour_details($pack_id);
		//debug($page_data['optional_tour_details']);exit;
		$this->template->view('holiday/package_details', $page_data);
	}
	
	public function optional_tour_details($pack_id){
		$post_data=$this->input->post();
		$page_data = $post_data;
		
		$page_data['optional_tour_details']=$this->Package_Model->opt_tour_details($pack_id);
		$page_data['package_details']= $this->Package_Model->get_basic_details($pack_id);
		$page_data['prev_page']= $post_data['prev_page'];
		$city = $this->Package_Model->tour_city($page_data['package_details'][0]['tours_city']);
		//debug($city);exit;
		$cities=array();
		foreach($city as $c_val){
		   $cities[]=$c_val['CityName'];
		} 
		
		
		$page_data['city']= implode(',',$cities);
		//debug($page_data);exit;
		$this->template->view('holiday/optional_tour_details', $page_data);
	}
    public function send_enquiry($view){
		
		
		$post_data=$this->input->post();
		$ref_no='PT-'.rand(10,100).'-'.$post_data['pack_id'];
		$tours_data = array(
		'enquiry_reference_no' =>$ref_no,
         'tour_id'=>$post_data['pack_id'],
		 'tour_code'=>$post_data['pack_code'],
		 'p_name'=>$post_data['pack_name'],
		 'title'=>'3',
         'name'=>$post_data['name'],
		 'lname'=>'lname',
         'Email' =>$post_data['Email'],
         'phone'=>$post_data['phone'],
         'departure_date'=>$post_data['dep_date'],
		 'message'=>$post_data['message'],
		 'number_of_passengers'=>0,
		 'adult'=>$post_data['adult'],
		 'child'=>$post_data['child'],
		 'infant'=>$post_data['infant'],
         'date'=>date('Y-m-d'),
		 'agent_remark'=>'',
		 'created_by_id'=>$GLOBALS ['CI']->entity_user_id,
		 'created_by_name' =>$GLOBALS ['CI']->agency_name,
		 'created_by' =>'agent'
		); 
      
        $return = $this->custom_db->insert_record('tours_enquiry',$tours_data);
		$this->load->library('provab_pdf');
		$create_pdf = new Provab_Pdf();
		$mail_template = $this->template->isolated_view('voucher/holiday_enquiry', $tours_data);
		$pdf = ''; //$pdf = $create_pdf->create_pdf($mail_template,'');
		$to_email=$GLOBALS ['CI']->entity_domain_mail;
		//$to_email='sanchithasv.provab@gmail.com';
		
		$this->provab_mailer->send_mail($to_email,domain_name().' - Holiday Enquiry',$mail_template ,$pdf);
		
		$this->session->set_flashdata("msg", "<div class='alert alert-success'>Your enquiry submitted successfully, our executive will get back to you shortly.</div>");
		$c_view=explode('-',$view);
		if($view=='home'){
			redirect('menu/dashboard/package?default_view=VHCID1433498322');
		}else if($c_view[0]=='country'){
			redirect('tours/holiday_country_package_list/'.$c_view[1]);
		}else if($c_view[0]=='city'){
			redirect('tours/holiday_city_package_list/'.$c_view[1]);
		}elseif($c_view[0]=='detail'){
			redirect('tours/holiday_package_detail/'.$post_data['pack_id'].'/'.$c_view[1].'-'.$c_view[2]);
		}else{
			redirect('menu/dashboard/package?default_view=VHCID1433498322');
		}
	
		
	}
	
	public function tours_enquiry() {
	  $agent_id = $GLOBALS["CI"]->entity_user_id;
	  $total_records = $this->Package_Model->tours_enquiry_list($agent_id);
	  $tours_enquiry = $this->Package_Model->tours_enquiry_list($agent_id);
	  $page_data['tours_enquiry'] = $tours_enquiry;
	  $this->template->view('holiday/enquiries',$page_data);
	}
	public function custom_enquiry_report() {
		$agent_id = $GLOBALS["CI"]->entity_user_id;
		$country_list=$this->Package_Model->tours_country_name();
		$city_list=$this->Package_Model->tours_city_name();
		$total_records = $this->Package_Model->custom_enquiry_list($agent_id);
		$page_data['table_data'] = $this->Package_Model->custom_enquiry_list($agent_id);
		
		foreach($page_data['table_data'] as $enq_key =>$enq_val){
			$page_data['table_data'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['table_data'][$enq_key]['country_name']=''; 
			$page_data['table_data'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0]; 
			foreach($country_array as $c_arr){
				$page_data['table_data'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		
		
		
		
		$page_data['total_rows']=count($page_data['table_data']);
		$this->template->view('holiday/custom_enquiry_report',$page_data);
	}
	public function confirmed_tours_enquiry() {
		
		$get_data = $this->input->get();
		//debug($get_data);exit;
		$page_data = array();
		if(isset($get_data['system_transaction_id'])){
			
			$condition = 'AND te.enquiry_reference_no ="'.$get_data['system_transaction_id'].'"';
		}else{
			$condition = '';
		}
		
		$agent_id = $GLOBALS["CI"]->entity_user_id;
		$total_records = $this->Package_Model->confirmed_tours_enquiry_list($agent_id,$condition);
		$page_data['tours_enquiry'] = $this->Package_Model->confirmed_tours_enquiry_list($agent_id,$condition);
		foreach($page_data['tours_enquiry']  as $tour_key => $tour_val){
			$page_data['tours_enquiry'][$tour_key]['attributes']['adult_count'] = $tour_val['adult'];
			$page_data['tours_enquiry'][$tour_key]['attributes']['child_count'] = $tour_val['child'];
			$page_data['tours_enquiry'][$tour_key]['attributes']['infant_count'] = $tour_val['infant'];
			$page_data['tours_enquiry'][$tour_key]['customer_details']= $this->custom_db->single_table_records('tour_booking_pax_details','*',array('app_reference'=>$tour_val['enquiry_reference_no']))['data'];
			
			$page_data['tours_enquiry'][$tour_key]['app_reference'] = $tour_val['enquiry_reference_no'];
			
			$page_data['tours_enquiry'][$tour_key]['payment_history'] = $this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$tour_val['enquiry_reference_no']))['data'];
		}
		//$page_data['tours_enquiry'] = $tours_enquiry;
		//debug($page_data);exit;
		$this->template->view('holiday/confirmed_enquiries',$page_data);
	}
	public function confirmed_custom_enquiry() {
		$get_data = $this->input->get();
		//debug($get_data);exit;
		$page_data = array();
		if(isset($get_data['system_transaction_id'])){
			
			$condition = 'AND te.enquiry_reference_no ="'.$get_data['system_transaction_id'].'"';
		}else{
			$condition = '';
		}
		
		$agent_id = $GLOBALS["CI"]->entity_user_id;
		$country_list=$this->Package_Model->tours_country_name();
		$city_list=$this->Package_Model->tours_city_name();
		$total_records = $this->Package_Model->confirmed_custom_enquiry_list($agent_id,$condition);
		$page_data['tours_enquiry'] = $this->Package_Model->confirmed_custom_enquiry_list($agent_id,$condition);
		foreach($page_data['tours_enquiry']  as $tour_key => $tour_val){
			$page_data['tours_enquiry'][$tour_key]['city'] = $city_list[$tour_val['departure_city']]; 
			$country_array=explode(',',$tour_val['destination']);
			$page_data['tours_enquiry'][$tour_key]['country_name']='';
			$page_data['tours_enquiry'][$tour_key]['attributes']['adult_count'] = $tour_val['adult'];
			$page_data['tours_enquiry'][$tour_key]['attributes']['child_count'] = $tour_val['child'];
			$page_data['tours_enquiry'][$tour_key]['attributes']['infant_count'] = $tour_val['infant'];
			$page_data['tours_enquiry'][$tour_key]['customer_details']= $this->custom_db->single_table_records('tour_booking_pax_details','*',array('app_reference'=>$tour_val['enquiry_reference_no']))['data'];
			$page_data['tours_enquiry'][$tour_key]['app_reference'] = $tour_val['enquiry_reference_no'];
			
			$page_data['tours_enquiry'][$tour_key]['payment_history'] = $this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$tour_val['enquiry_reference_no']))['data'];
			foreach($country_array as $c_arr){
				$page_data['tours_enquiry'][$tour_key]['country_name'].=$country_list[$c_arr].' ,';
			}
		}
		//$page_data['tours_enquiry'] = $tours_enquiry;
		//debug($page_data);exit;
		$this->template->view('holiday/confirmed_custom_enquiry',$page_data);
	}
	 public function voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
     {
      // echo $email;exit;
      error_reporting(0);
      $page_data['tour_id'] = $tour_id;
      $page_data['menu'] = false;
      $page_data ['tour_data']            = $this->Package_Model->tour_data($tour_id);
      $page_data ['tours_itinerary']      = $this->Package_Model->tours_itinerary($tour_id);
     // debug($dep_date); exit;
      $page_data ['tours_itinerary_dw']   = @$this->Package_Model->tours_itinerary_dw($tour_id);

      #debug($page_data ['tours_itinerary_dw']); exit;
      $page_data ['tours_itinerary_wd']   = $this->Package_Model->tours_itinerary_dw($tour_id);
      $page_data ['tours_date_price']     = $this->Package_Model->tours_date_price($tour_id);
	  $page_data['dep_dates'] = $this->custom_db->single_table_records('tour_dep_dates', '*', array('tour_id'=>$tour_id))['data'];
      $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ,final_airliner_price,markup,group_concat(markup) markup ,tour_id, from_date, to_date , currency from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");



      $page_data['tour_price'] = json_decode(json_encode($tour_data),true);
      // debug($page_data['tour_price']); exit('');
	  $visited_city = array();
      $tour_cities = $page_data['tour_data']['tours_city'];
      $tour_cities_array = explode(",", $tour_cities);
      foreach ($tour_cities_array as $t_city) {
       $visited_city[]   = $this->custom_db->single_table_records('tours_city', '*', array('id'=>$t_city))['data'][0];
     }
	 $categories = array();
      $tour_types = $page_data['tour_data']['tour_type'];
      $tour_types_array = explode(",", $tour_types);
      foreach ($tour_types_array as $tt_id) {
       $categories[]   = $this->custom_db->single_table_records('tour_type', '*', array('id'=>$tt_id))['data'][0];
     }
	 $activities = array();
      $tour_themes = $page_data['tour_data']['theme'];
      $tour_themes_array = explode(",", $tour_themes);
      foreach ($tour_themes_array as $tth_id) {
       $activities[]   = $this->custom_db->single_table_records('tour_subtheme', '*', array('id'=>$tth_id))['data'][0];
     }
     $page_data['visited_city'] = $visited_city;
	 $page_data['categories'] = $categories;
	 $page_data['activities'] = $activities;
	 
     if ($quotation_id!='') {
       $quotation_details = $this->Package_Model->quotation_details($quotation_id);
       if ($quotation_details['status']==1) {
        $page_data['quotation_details'] = $quotation_details['data'];
      }
    }
    if ($app_reference!='') {
     $booking_details = $this->Package_Model->booking_details($app_reference);
     if ($booking_details['status']==1) {
      $page_data['booking_details'] = $booking_details['data'];
    }
  }
  // echo $mail;exit;
  if($mail == 'mail') { 
   $operation="mail";
   if(!empty($email)){
    $email = $email;
   }
   else{
      if($this->input->post('email')){  
        $email = $this->input->post('email');
      }
   }
	   // echo $email;exit;
	  
	}
	// echo $operation;exit;
	switch ($operation) {
	  case 'show_broucher' : 
	  $page_data['menu'] = true;
	  //debug($page_data);exit();
	  $this->template->view('holiday/broucher',$page_data);
	  break;
	  case 'show_pdf_voucher' :
	  $get_view = $this->template->isolated_view ( 'holiday/broucher_pdf',$page_data );
	  $this->load->library ( 'provab_pdf' );
	  $this->provab_pdf->create_pdf ( $get_view, 'D');   
	  break;
	  case 'mail' :
	  $mail_template =$this->template->isolated_view('holiday/broucher',$page_data);   
	  $this->load->library ( 'provab_mailer' ); 

	 // $pdf = $this->provab_pdf->create_pdf($mail_template,'F');
	  if(count($ex_data)>0){       
	   $message = '<strong style="line-height:25px; font-size:16px;">Good day '.$ex_data['name'].',</strong><br>
	   <span style="line-height:25px; font-size:15px;">Please find the Holiday Package below. </span>';
	   if($ex_data['booking_url']){  
		$message .= '<a style="line-height:25px; font-size:16px;" href="'.$ex_data['booking_url'].'" target="_blank">Click here to pay</a><br><br>';
	  }
	}
	$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $message.$mail_template); 
	if(!empty($redirect)){
	 return true;
	}else{
	  redirect(base_url().'tours/voucher/'.$tour_id,'refresh');
	}
	break;
	}
	}
	public function b2b_voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
    {
		//echo $tour_id;  echo $email;exit;
		error_reporting(0);
		$page_data['tour_id'] = $tour_id;
		//$this->load->model('tours_model');
		$page_data['menu'] = false;
		$page_data ['tour_data']            = $this->Package_Model->tour_data($tour_id);
		$page_data ['tours_itinerary']      = $this->Package_Model->tours_itinerary($tour_id);
		// debug($dep_date); exit;
		$page_data ['tours_itinerary_dw']   = @$this->Package_Model->tours_itinerary_dw($tour_id);
		$page_data ['tours_hotel_det']   		= @$this->Package_Model->tour_hotel_city_data($tour_id);
		//debug($page_data ['tours_hotel_det']); exit;
		$page_data ['tours_itinerary_wd']   = $this->Package_Model->tours_itinerary_dw($tour_id);
		$page_data ['tours_date_price']     = $this->Package_Model->tours_date_price($tour_id);
		if($page_data['tour_data']['package_type']=='fit'){
			$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_valid_from_to_date', '*', array('tour_id'=>$tour_id))['data'];
		 
		}else{
			$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_dep_dates', '*', array('tour_id'=>$tour_id))['data'];
		}
     // $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ,final_airliner_price,markup,group_concat(markup) markup ,tour_id, from_date, to_date , currency from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
		$b2b_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2B' ");
		$b2c_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2C' ");
	//echo $this->db->last_query();
		$page_data['tours_city_name'] = $this->Package_Model->tours_city_name();
		$page_data['b2b_tour_price'] = json_decode(json_encode($b2b_tour_data),true);
		$page_data['b2c_tour_price'] = json_decode(json_encode($b2c_tour_data),true);
     // debug($page_data); exit('');
		$visited_city = array();
		$tour_cities = $page_data['tour_data']['tours_city'];
		$tour_cities_array = explode(",", $tour_cities);
		foreach ($tour_cities_array as $t_city) {
		$visited_city[]   = $this->custom_db->single_table_records('tours_city', '*', array('id'=>$t_city))['data'][0];
		}
		$categories = array();
		$tour_types = $page_data['tour_data']['tour_type'];
		$tour_types_array = explode(",", $tour_types);
		foreach ($tour_types_array as $tt_id) {
			$categories[]   = $this->custom_db->single_table_records('tour_type', '*', array('id'=>$tt_id))['data'][0];
		}
		$activities = array();
		$tour_themes = $page_data['tour_data']['theme'];
		$tour_themes_array = explode(",", $tour_themes);
		foreach ($tour_themes_array as $tth_id) {
			$activities[]   = $this->custom_db->single_table_records('tour_subtheme', '*', array('id'=>$tth_id))['data'][0];
		}
		$page_data['visited_city'] = $visited_city;
		$page_data['categories'] = $categories;
		$page_data['activities'] = $activities;
	 
		if ($quotation_id!='') {
			$quotation_details = $this->Package_Model->quotation_details($quotation_id);
			if ($quotation_details['status']==1) {
				$page_data['quotation_details'] = $quotation_details['data'];
			}
		}
		if ($app_reference!='') {
			$booking_details = $this->Package_Model->booking_details($app_reference);
			if ($booking_details['status']==1) {
				$page_data['booking_details'] = $booking_details['data'];
			}
		}
		// echo $mail;exit;
		if($mail == 'mail') { 
			$operation="mail";
			if(!empty($email)){
				$email = $email;
			}
			else{
				if($this->input->post('email')){  
					$email = $this->input->post('email');
					$email_body = $this->input->post('email_body');
				}
			}
			// echo $email;exit;
  
		}
		// echo $operation;exit;
		switch ($operation) {
			case 'show_broucher' : 
				$page_data['menu'] = true;
				//debug($page_data);exit();
				$this->template->view('holiday/b2b_broucher',$page_data);
				break;
			case 'show_pdf_voucher' :
				$get_view = $this->template->isolated_view ( 'holiday/b2b_broucher_pdf',$page_data );	
				//echo $get_view;exit;	
				$this->load->library ( 'provab_pdf' );	
				$this->provab_pdf->create_pdf ( $get_view, 'show');   	
				break;	
			case 'show_download_pf' :	
				$get_view = $this->template->isolated_view ( 'holiday/b2b_broucher_pdf',$page_data );	
				//echo $get_view;exit;	
				$this->load->library ( 'provab_pdf' );	
				$this->provab_pdf->create_pdf ( $get_view, 'D');   	
				break;
			case 'mail' :
			$this->load->library ( 'provab_pdf' );
				$create_pdf = new Provab_Pdf();
				$get_view = $this->template->isolated_view ( 'holiday/b2b_broucher_pdf',$page_data );
				$pdf = $create_pdf->create_pdf($get_view,'');
				$this->load->library ( 'provab_mailer' ); 
				//exit;
		  
				if(count($ex_data)>0){        
					$message = '<strong style="line-height:25px; font-size:16px;">Good day '.$ex_data['name'].',</strong><br>
						<span style="line-height:25px; font-size:15px;">Please find the Holiday Package below. </span>';
						if($ex_data['booking_url']){  
							$message .= '<a style="line-height:25px; font-size:16px;" href="'.$ex_data['booking_url'].'" target="_blank">Click here to pay</a><br><br>';
						}
				}
				//$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $email_body); 
				$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $email_body,$pdf); 
				if(!empty($redirect)){
					return true;
				}else{
					redirect(base_url().'tours/b2b_voucher/'.$tour_id,'refresh');
				}
				break;
		}
	}
	public function fare_breakup_details(){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		$post_data=$this->input->post();
		$adult_array=explode('|',$post_data['sel_adult_count']);
		$child_wb_array=explode('|',$post_data['sel_child_wb_count']);
		$child_wob_array=explode('|',$post_data['sel_child_wob_count']);
		$infant_array=explode('|',$post_data['sel_infant_count']);
		$optional_tour=implode(',',$post_data['sel_opt_tour']);
		
		
		$page_data['sel_adult_count']= $post_data['sel_adult_count'];
		$page_data['sel_child_wb_count']= $post_data['sel_child_wb_count'];
		$page_data['sel_child_wob_count']=$post_data['sel_child_wob_count'];
		$page_data['sel_infant_count']= $post_data['sel_infant_count'];
		
		//debug($post_data);
		array_shift($adult_array);
		array_shift($child_wb_array);
		array_shift($child_wob_array);
		array_shift($infant_array);
		
		
		
		
		$page_data['package_details']= $this->Package_Model->get_basic_details($post_data['pack_id']);
		$package_price_details= $this->Package_Model->get_price_details_new($post_data['pack_id'],'B2B');
		$page_data['optional_tour_details']= $this->Package_Model->selected_optional_tour($optional_tour);
		$page_data['package_price_details']=array();
		foreach($package_price_details as $pack_key =>$pack_val){
			$page_data['package_price_details'][$pack_val['occupancy']]= $pack_val;
		}
		$city = $this->Package_Model->tour_city($page_data['package_details'][0]['tours_city']);
		$cities=array();
		//debug($city);
		foreach($city as $c_val){
		   $cities[]=$c_val['CityName'];
		} 
		$page_data['city']= implode(',',$cities);
		$page_data['no_rooms']=$post_data['sel_room_count'];
		$page_data['adult']= $adult_array;
		$page_data['child_with_bed']=$child_wb_array;
		$page_data['child_without_bed']=$child_wob_array;
		$page_data['infant']=$infant_array;
		$page_data['dep_date']=$post_data['sel_departure_date'];
		$page_data['agent_markup']=$post_data['agent_markup'];
		$page_data['prev_page']=$post_data['prev_page'];
		
		$page_data['pre_booking_params']=base64_encode(json_encode($page_data,true));
		$page_data['prev_page_params']=base64_encode(json_encode($post_data,true));
	//debug($page_data);exit("Fasdfdsf");
		$this->template->view('holiday/pre_booking_page', $page_data);
	}
	public function pre_booking(){
		//ini_set('display_errors', 1);
			//ini_set('display_startup_errors', 1);
			//error_reporting(E_ALL);
		//$post_data=$this->input->post();
		//debug($post_data);exit;
		//$this->template->view('holiday/pre_booking_page', $page_data);
		
		// redirect(base_url().'index.php/general/booking_not_allowed');		
		// exit;
		$post_params = $this->input->post();
		//debug($post_params);exit;
		if($post_params['pay1']=='full_pay'){
			$temp_token['markup_price_summary']=$post_params['total_trip_with_gst_cost'];
		}else if($post_params['pay1']=='advance_pay'){
			$temp_token['markup_price_summary']=$post_params['b2b_adv_amount'];
		}else if($post_params['pay1']=='wish_pay'){
			$temp_token['markup_price_summary']=$post_params['user_amount'];
		}
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))
		{
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');
			exit;
		}
		$post_params['remaining_amount'] = $post_params['total_trip_with_gst_cost'] - $temp_token['markup_price_summary'];
		$post_params['billing_city'] = 'Bangalore';
		$post_params['billing_zipcode'] = '560100';
		$selected_pm=$post_params ['selected_pm'];
		$post_params['payment_method'] = PAY_NOW;
		//$this->custom_db->generate_static_response(json_encode($post_params));
		//Insert To temp_booking and proceed
		/*$post_params = $this->hotel_model->get_static_response($static_search_result_id);*/

		//Make sure token and temp token matches
		$valid_temp_token = json_decode(base64_decode($post_params['pre_booking_params']));
		
		if ($valid_temp_token != false) {
			
			$post_params['markup_price_summary'] ['RoomPrice'] = $temp_token['markup_price_summary'];
			$post_params['markup_price_summary'] ['PublishedPrice'] = $temp_token['markup_price_summary'];
			$post_params['markup_price_summary'] ['PublishedPriceRoundedOff'] = $temp_token['markup_price_summary'];
			$post_params['markup_price_summary'] ['OfferedPrice'] = $temp_token['markup_price_summary'];
			$post_params['markup_price_summary'] ['OfferedPriceRoundedOff'] = $temp_token['markup_price_summary'];
			$post_params['markup_price_summary'] ['ServiceTax'] = 0;
			$post_params['markup_price_summary'] ['Tax'] = 0;
			$post_params['markup_price_summary'] ['ExtraGuestCharge'] = 0;
			$post_params['markup_price_summary'] ['ChildCharge'] = 0;
			$post_params['markup_price_summary'] ['OtherCharges'] = 0;
			$post_params['markup_price_summary'] ['TDS'] = 0;
				
			$temp_booking = $this->module_model->serialize_temp_booking_record($post_params, PACKAGE_BOOKING);
			//debug($temp_booking); exit;
			//debug($temp_booking);die('45');
			$book_id = $temp_booking['book_id'];
			$book_origin = $temp_booking['temp_booking_origin'];
			
			if ($post_params['booking_source'] == PROVAB_PACKAGE_BOOKING_SOURCE) {
				$amount	  = $temp_token['markup_price_summary'];
				
			}

			$currency_obj = new Currency ( array (
						'module_type' => 'hotel',
						'from' => admin_base_currency (),
						'to' => admin_base_currency () 
			) );
			/********* Convinence Fees End ********/
			 	
			/********* Promocode Start ********/
			$promocode_discount = 0;
			/********* Promocode End ********/

			//details for PGI
			$email = $this->entity_email;
			$phone = $this->entity_phone;
			$verification_amount = ceil($amount+$convenience_fees-$promocode_discount);
			$firstname = $this->entity_firstname;
			$productinfo = META_PACKAGE_COURSE;
			//check current balance before proceeding further
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);
			$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);
			
			$selected_pm=$post_params ['selected_pm'];
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){
                $bank_code = $post_params ['bank_code'];
            }
            else
                $bank_code = 0;
            $selected_pm_array = explode("_", $selected_pm);
            $selected_pm = $selected_pm_array[0];
            $method = $selected_pm_array[1];
            if($selected_pm == "WALLET")
            	$method = "wallet";
            if($method=="CC")
                $payment_mode = "credit_card";
            else if($method=="DC")
                $payment_mode = "debit_card";
            else if($method=="PPI")
                $payment_mode = "paytm_wallet";
            else if($selected_pm=="TECHP")
                $payment_mode = "net_banking";
            else
                $payment_mode = "wallet";
            $con_row = $this->master_currency->get_instant_recharge_convenience_fees($amount, $method, $bank_code);
			if ($domain_balance_status == true || $selected_pm) {
				switch($post_params['payment_method']) {
					case PAY_NOW :
						$this->load->model('transaction');
						$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
						$this->transaction->create_payment_record($book_id, $amount, $firstname, $email, $phone, $productinfo, $con_row['cf'], $promocode_discount, $pg_currency_conversion_rate, $selected_pm, $payment_mode);
						//redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);
						redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin.'/'.$selected_pm);
						break;
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;
					break;
				}
			} else {
				redirect(base_url().'index.php/hotel/exception?op=Amount Hotel Booking&notification=insufficient_balance');
			}
		} else {
			die('eeeee');
			redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Hotel Booking&notification=validation');
		}
	}
	/*
		process booking in backend until show loader 
	*/
	function process_booking($book_id, $temp_book_origin, $is_paid_by_pg=0){
		
		if($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0){

			$page_data ['form_url'] = base_url () . 'index.php/tours/secure_booking';
			$page_data ['form_method'] = 'POST';
			$page_data ['form_params'] ['book_id'] = $book_id;
			$page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;
			$page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;
			$this->template->view('share/loader/booking_process_loader', $page_data);	

		}else{
			redirect(base_url().'index.php/hotel/exception?op=Invalid request&notification=validation');
		}
		
	}
	function secure_booking()
	{	
		//ini_set('display_errors', 1);
			//ini_set('display_startup_errors', 1);
			//error_reporting(E_ALL);
		$post_data = $this->input->post();
		//debug($post_data);die('22');
		if(valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
			empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0){
			//verify payment status and continue
			$book_id = trim($post_data['book_id']);
			$temp_book_origin = intval($post_data['temp_book_origin']);
		} else{
			redirect(base_url().'index.php/hotel/exception?op=InvalidBooking&notification=invalid');
		}
		
		//Check whether amount is paid through PG
		$is_paid_by_pg=$post_data['is_paid_by_pg'];
		//run booking request and do booking
		$temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);

		//debug($temp_booking);die('222');
		//Delete the temp_booking record, after accessing
		//$this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);
		
		//load_hotel_lib($temp_booking['booking_source']);
		//verify payment status and continue
		if($temp_booking['book_attributes']['pay1']=='full_pay'){
			$temp_token['markup_price_summary']=$temp_booking['book_attributes']['total_trip_with_gst_cost'];
		}else if($temp_booking['book_attributes']['pay1']=='advance_pay'){
			$temp_token['markup_price_summary']=$temp_booking['book_attributes']['b2b_adv_amount'];
		}else if($temp_booking['book_attributes']['pay1']=='wish_pay'){
			$temp_token['markup_price_summary']=$temp_booking['book_attributes']['user_amount'];
		}
		$total_booking_price = $temp_token['markup_price_summary'];
		$api_amount = $total_booking_price;

		$currency = 'INR';
		$currency_obj = new Currency(array('module_type' => 'package', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
		//also verify provab balance
		//check current balance before proceeding further
		$agent_paybleamount = $currency_obj->get_agent_paybleamount($total_booking_price);
		$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);
		
		$selected_pm = $temp_booking['book_attributes']['selected_pm'];
         //debug($temp_booking); exit;
        $selected_pm_array = explode("_", $selected_pm);
        $selected_pm = $selected_pm_array[0];
        $method = $selected_pm_array[1];
        //debug($selected_pm_array); exit;
        if($method=="CC"){
            $temp_booking['book_attributes']['payment_method'] = "credit_card";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($method=="DC"){
            $temp_booking['book_attributes']['payment_method'] = "debit_card";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($method=="PPI"){
            $temp_booking['book_attributes']['payment_method'] = "paytm_wallet";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($selected_pm == "TECHP"){
            $temp_booking['book_attributes']['payment_method'] = "net_banking";
        }
        else
        {
        	$temp_booking['book_attributes']['payment_method'] = "wallet";
        }
		//debug($booking);die('dsdsds'); 
		if ($domain_balance_status || $is_paid_by_pg) {
			//lock table
		
			if ($temp_booking != false) {
				switch ($temp_booking['booking_source']) {
					case PROVAB_PACKAGE_BOOKING_SOURCE :
					case REZLIVE_HOTEL :
						//FIXME : COntinue from here - Booking request
						//$booking = $this->hotel_lib->process_booking($book_id, $temp_booking['book_attributes']);
						//Save booking based on booking status and book id
						break;
				}
				//debug($booking);die('78'); 
				$booking['status'] = SUCCESS_STATUS;
				if ($booking['status'] == SUCCESS_STATUS) {
					//exit($api_amount);
					$booking['data']['currency_obj'] = $currency_obj;
					//debug($booking['data']); exit;
					//Save booking based on booking status and book id					
                	$api_amount = 0 - $api_amount;
					//debug($temp_booking['booking_source']);debug($api_amount);
					$this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);

					$data = $this->Package_Model->save_booking($book_id, $temp_booking, $this->current_module);
					//debug($data);debug($temp_booking);debug($this->current_module);exit;
					$payment_data = $this->Package_Model->save_payment_details($book_id, $temp_booking, $this->current_module);
					if($is_paid_by_pg)
					{
						$agent_earning = $data["agent_markup"];
						$remarks = "Your ernings on Package booking credited to wallet";
						$crdit_towards = "Package booking";
						$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $agent_earning, 0, $remarks);
					}
					
					//debug($data);exit;
					$data['transaction_currency'] = 'INR';
					$this->domain_management_model->update_transaction_details('package', $book_id, $data['fare'], $data['admin_markup'], $data['agent_markup'], $data['convinence'], $data['discount'],$data['transaction_currency'], $data['currency_conversion_rate'],$is_paid_by_pg);
					
					
					//deduct balance and continue

					//save to accounting software
                    /*$this->load->library('xlpro');
                    $this->xlpro->get_hotel_booking_details($booking,$temp_booking);*/

					//redirect(base_url().'index.php/voucher/hotel/'.$book_id.'/'.$temp_booking['booking_source'].'/'.$data['booking_status'].'/show_voucher/0/1');
					redirect(base_url().'index.php/voucher/package/'.$book_id.'/'.$temp_booking['booking_source'].'/'.$data['booking_status'].'/show_voucher/0/1');
				}
				if ($is_paid_by_pg && in_array($booking['status'], array(BOOKING_ERROR, FAILURE_STATUS))){
					$pg_name = $temp_booking['book_attributes']['selected_pm'];
					redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
					//exit;
				}
				else {
					redirect(base_url().'index.php/hotel/exception?op=booking_exception&notification='.$booking['msg']);
				}
			}
			//release table lock
		} else {
			//debug($booking);die('dddddddddd');
			redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Insufficient&notification=validation');
		}
	}
	public function add_tour_pax_details(){
		$post_data = $this->input->post();
		//debug($_FILES);exit;
		foreach($post_data['first_name'] as $p_key => $p_val){
			
			$passport_first_page = $post_data['old_pass_first_page'][$p_key]; 
			$passport_first_page = $this->Package_Model->upload_image_lgm($_FILES,'pass_first_page',$passport_first_page,$p_key);
			
			$passport_second_page = $post_data['old_pass_second_page'][$p_key]; 
			$passport_second_page = $this->Package_Model->upload_image_lgm($_FILES,'pass_second_page',$passport_second_page,$p_key);
			$visa_photo = $post_data['old_visa_photo'][$p_key]; 
			$visa_photo = $this->Package_Model->upload_image_lgm($_FILES,'photo_as_pass',$visa_photo,$p_key);
			
			
			
			
			$data_array = array(
				'app_reference'     	=> $post_data['app_reference'],
				'first_name'     		=> $post_data['first_name'][$p_key],
				'middle_name'     		=> $post_data['middle_name'][$p_key],
				'last_name'     		=> $post_data['last_name'][$p_key],
				'comments'     			=> $post_data['comments'][$p_key],
				'passport_first_page'   => $passport_first_page,
				'passport_second_page'  => $passport_second_page,
				'phone'					=>$this->entity_phone,
				'email'					=>$this->entity_email,
				'pax_type'				=>$post_data['pax_type'][$p_key],
				'visa_photo'     		=> $visa_photo
			
			);
			
			if($post_data['update_id'][$p_key]!=''){
				$return = $this->custom_db->update_record('tour_booking_pax_details',$data_array,array('origin'=>$post_data['update_id'][$p_key]));		
			}else{
				$return = $this->custom_db->insert_record('tour_booking_pax_details',$data_array);				
			}
			
		
		}
		if(substr($post_data['app_reference'],0,2)=='PB'){	
			redirect(base_url().'index.php/report/package_enquiry_report');	
		}else if(substr($post_data['app_ref'],0,2)=='PT'){	
			redirect(base_url().'index.php/tours/confirmed_tours_enquiry');	
		}else{	
			redirect(base_url().'index.php/tours/confirmed_custom_enquiry');	
		}	
		
	}
	public function send_custom_enquiry(){
		$post_data = $this->input->post();
		//debug($post_data);exit;
		$destination=implode(',',$post_data['destination']);
		$ref_no='CPE-'.time(10,100);
		$data_array=array(
			'agent_name' => $post_data['agent_name'],
			'enquiry_reference_no' =>$ref_no,
			'agent_id' => $post_data['agent_id'],
			'travel_type' => $post_data['travel_type'],
			'destination' => $destination,
			'departure_city' => $post_data['departure_city'],
			'fr_date' => $post_data['fr_date'],
			'to_date' => $post_data['to_date'],
			'night' => $post_data['night'],
			'adult' => $post_data['adult'],
			'child' => $post_data['child'],
			'infant' => $post_data['infant'],
			'remark' => $post_data['remarks'],	
			'created_date' => date('Y-m-d'),
		);
		$return = $this->custom_db->insert_record('custom_package_enquiry',$data_array);
		redirect(base_url().'index.php/general/quick_form');
	}
	/**	
	 *SANCHITHA	
	 */	
	function exception()	
	{	
		$module = META_PACKAGE_COURSE;	
		$op = (empty($_GET['op']) == true ? '' : $_GET['op']);	
		$notification = (empty($_GET['notification']) == true ? '' : $_GET['notification']);	
			
		if($op == 'Some Problem Occured. Please Search Again to continue'){	
			$op = 'Some Problem Occured. ';	
		}	
		if($notification == 'Invalid CommitBooking Request'){	
			$message = 'Session is Expired';	
		}	
		else if($notification == 'Some Problem Occured. Please Search Again to continue' ){	
			$message = 'Some Problem Occured';	
		}	
		else if($notification == 'locked_msg' ){	
			$message = 'Your Account have been locked. Please contact admin to Unlock it.';	
		}	
		else{	
			$message = $notification;	
		}	
		$exception = $this->module_model->flight_log_exception($module, $op, $message);	
		$exception = base64_encode(json_encode($exception));	
		// debug($exception);exit;	
		//set ip log session before redirection	
		$this->session->set_flashdata(array('log_ip_info' => true));	
		$is_session = false;	
			
		if($notification=='session'){	
			$is_session =true;	
		}	
			
		redirect(base_url().'index.php/hotel/event_logger/'.$exception.'/'.$is_session.'/'.$op);	
	}	
	public function pay_pending_amount(){	
		
		$post_params = $this->input->post();	
			
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))	
		{	
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');	
			exit;	
		}	
		$selected_pm=$post_params ['selected_pm'];	
		$post_params['payment_method'] = PAY_NOW;	
		$promocode_discount = 0;	
		//details for PGI	
			$email = $this->entity_email;	
			$phone = $this->entity_phone;	
			$verification_amount = ceil($post_params ['amount']+$convenience_fees-$promocode_discount);	
			$firstname = $this->entity_firstname;	
			$productinfo = META_PACKAGE_COURSE;	
			//check current balance before proceeding further	
			$currency = 'INR';	
			$currency_obj = new Currency(array('module_type' => 'package', 'from' => admin_base_currency(), 'to' => admin_base_currency()));	
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);	
			$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);	
				
			$selected_pm=$post_params ['selected_pm'];	
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){	
                $bank_code = $post_params ['bank_code'];	
            }	
            else	
                $bank_code = 0;	
            $selected_pm_array = explode("_", $selected_pm);	
            $selected_pm = $selected_pm_array[0];	
            $method = $selected_pm_array[1];	
            if($selected_pm == "WALLET")	
            	$method = "wallet";	
            if($method=="CC")	
                $payment_mode = "credit_card";	
            else if($method=="DC")	
                $payment_mode = "debit_card";	
            else if($method=="PPI")	
                $payment_mode = "paytm_wallet";	
            else if($selected_pm=="TECHP")	
                $payment_mode = "net_banking";	
            else	
                $payment_mode = "wallet";	
            $con_row = $this->master_currency->get_instant_recharge_convenience_fees($post_params ['amount'], $method, $bank_code);	
				
			if ($domain_balance_status == true || $selected_pm) {	
				switch($post_params['payment_method']) {	
					case PAY_NOW :	
						$this->load->model('transaction');	
						//$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();	
						$this->transaction->update_payment_record($post_params['app_reference'],$selected_pm,$payment_mode,$post_params['pay_ref_id']);	
						//redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);	
						redirect(base_url().'index.php/payment_gateway/payment/'.$post_params['app_reference'].'/'.$post_params['temp_booking_id'].'/'.$selected_pm.'/'.$post_params['pay_ref_id']);	
						break;	
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;	
					break;	
				}	
			} else {	
				redirect(base_url().'index.php/hotel/exception?op=Amount Hotel Booking&notification=insufficient_balance');	
			}	
	}	
	public function process_balance_pay($book_id,$temp_book_origin,$is_paid_by_pg){	
		$exp_value=explode('-',$temp_book_origin);	
		//debug($exp_value);exit;	
		$payment_ref = $exp_value[1];	
		$temp_book_origin = $exp_value[0];	
		if($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0){	
			$page_data ['form_url'] = base_url () . 'index.php/tours/secure_balance_pay';	
			$page_data ['form_method'] = 'POST';	
			$page_data ['form_params'] ['book_id'] = $book_id;	
			$page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;	
			$page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;	
			$page_data ['form_params'] ['payment_ref'] = $payment_ref;	
			$this->template->view('share/loader/booking_process_loader', $page_data);		
		}else{	
			debug($book_id, $temp_book_origin, $temp_book_origin);exit("Ff");	
			redirect(base_url().'index.php/hotel/exception?op=Invalid request&notification=validation');	
		}	
	}	
	function secure_balance_pay()	
	{		
				
		$post_data = $this->input->post();	
			
		if(valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&	
			empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0){	
			//verify payment status and continue	
			$book_id = trim($post_data['book_id']);	
			$temp_book_origin = intval($post_data['temp_book_origin']);	
		} else{	
			exit("np");	
			redirect(base_url().'index.php/hotel/exception?op=InvalidBooking&notification=invalid');	
		}	
			
				
			
		//Check whether amount is paid through PG	
		$is_paid_by_pg=$post_data['is_paid_by_pg'];	
		//run booking request and do booking	
		$temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);	
		//echo $this->db->last_query();	
		//debug($temp_booking);exit('dasdas');	
		$payment_details=$this->custom_db->single_table_records('payment_gateway_details','*',array('payment_history_ref'=>$post_data['payment_ref'],'app_reference'=>$book_id))['data'][0];	
			
		$total_booking_price = $payment_details['amount'];	
		$api_amount = $total_booking_price;	
		$currency = 'INR';	
		$currency_obj = new Currency(array('module_type' => 'package', 'from' => admin_base_currency(), 'to' => admin_base_currency()));	
		//also verify provab balance	
		//check current balance before proceeding further	
		$agent_paybleamount = $currency_obj->get_agent_paybleamount($total_booking_price);	
		$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);	
			
		$selected_pm = $payment_details['payment_mode'];	
         //debug($temp_booking); exit;	
        $selected_pm_array = explode("_", $selected_pm);	
        $selected_pm = $selected_pm_array[0];	
        $method = $selected_pm_array[1];	
        //debug($selected_pm_array); exit;	
        if($method=="CC"){	
            $temp_booking['book_attributes']['payment_method'] = "credit_card";	
            $temp_booking['book_attributes']['bank_code'] = 0;	
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;	
        }	
        else if($method=="DC"){	
            $temp_booking['book_attributes']['payment_method'] = "debit_card";	
            $temp_booking['book_attributes']['bank_code'] = 0;	
            $temp_booking['book_attributes']['bank_code'] = 0;	
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;	
        }	
        else if($method=="PPI"){	
            $temp_booking['book_attributes']['payment_method'] = "paytm_wallet";	
            $temp_booking['book_attributes']['bank_code'] = 0;	
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;	
        }	
        else if($selected_pm == "TECHP"){	
            $temp_booking['book_attributes']['payment_method'] = "net_banking";	
        }	
        else	
        {	
        	$temp_booking['book_attributes']['payment_method'] = "wallet";	
        }	
		if ($domain_balance_status || $is_paid_by_pg) {	
			//lock table	
			
			if ($temp_booking != false) {	
				switch ($temp_booking['booking_source']) {	
					case PROVAB_PACKAGE_BOOKING_SOURCE :	
					case REZLIVE_HOTEL :	
							
						break;	
				}	
					
				$booking['status'] = SUCCESS_STATUS;	
					
				if ($booking['status'] == SUCCESS_STATUS) {	
						
					$booking['data']['currency_obj'] = $currency_obj;	
									
                	$api_amount = 0 - $api_amount;	
					//debug($temp_booking['booking_source']);debug($api_amount);exit;	
					$this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);	
					$payment_data = $this->Package_Model->update_payment_details($book_id,$payment_details,$post_data['payment_ref']);	
					//echo $this->db->last_query();	
					//debug($payment_data);exit;	
					/*if($is_paid_by_pg)	
					{	
						$agent_earning = $data["agent_markup"];	
						$remarks = "Your ernings on Package booking credited to wallet";	
						$crdit_towards = "Package booking";	
						$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $agent_earning, 0, $remarks);	
					}*/	
						
						
					$data['transaction_currency'] = 'INR';	
					$this->domain_management_model->update_transaction_details('package_due_amount', $book_id,$payment_details['amount'],0,0,0,0,$data['transaction_currency'],1,$is_paid_by_pg);	
					$this->load->model('transaction');	
					$this->transaction->update_payment_status($book_id,$post_data['payment_ref']);	
					//echo $this->db->last_query();exit;	
					//debug($post_data);exit;	
					if(substr($post_data['book_id'],0,2)=='PT'){	
						redirect(base_url().'index.php/tours/confirmed_tours_enquiry');	
					}else if(substr($post_data['book_id'],0,2)=='CP'){	
						redirect(base_url().'index.php/tours/confirmed_custom_enquiry');	
					}else{	
						redirect(base_url().'report/package_enquiry_report');						
					}	
						
					//redirect(base_url().'index.php/voucher/package/'.$book_id.'/'.$temp_booking['booking_source'].'/'.$data['booking_status'].'/show_voucher/0/1');	
				}	
				if ($is_paid_by_pg && in_array($booking['status'], array(BOOKING_ERROR, FAILURE_STATUS))){	
					$pg_name = $temp_booking['book_attributes']['selected_pm'];	
					redirect ( base_url () . 'report/package_enquiry_report');	
					//exit;	
				}	
				else {	
					redirect(base_url().'index.php/hotel/exception?op=booking_exception&notification='.$booking['msg']);	
				}	
			}	
		} else {	
			redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Insufficient&notification=validation');	
		}	
	}	
	function pay_enquiry_amount(){	
		$post_params = $this->input->post();	
		$post_params['pay1']='wish_pay';	
		$post_params['booking_source']='PTBSID0000000014';	
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))	
		{	
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');	
			exit;	
		}	
		$post_params['remaining_amount'] = $post_params['total_trip_with_gst_cost'] - $post_params['amount'];	
		$post_params['billing_city'] = 'Bangalore';	
		$post_params['billing_zipcode'] = '560100';	
		$post_params['paid_for'] = 'PACKAGE_ENQUIRY';	
		$selected_pm=$post_params ['selected_pm'];	
		$post_params['payment_method'] = PAY_NOW;	
		$valid_temp_token = json_decode(base64_decode($post_params['pre_booking_params']));	
			
		$post_params['markup_price_summary'] ['RoomPrice'] = $post_params['amount'];	
		$post_params['markup_price_summary'] ['PublishedPrice'] = $post_params['amount'];	
		$post_params['markup_price_summary'] ['PublishedPriceRoundedOff'] = $post_params['amount'];	
		$post_params['markup_price_summary'] ['OfferedPrice'] = $post_params['amount'];	
		$post_params['markup_price_summary'] ['OfferedPriceRoundedOff'] = $post_params['amount'];	
		$post_params['markup_price_summary'] ['ServiceTax'] = 0;	
		$post_params['markup_price_summary'] ['Tax'] = 0;	
		$post_params['markup_price_summary'] ['ExtraGuestCharge'] = 0;	
		$post_params['markup_price_summary'] ['ChildCharge'] = 0;	
		$post_params['markup_price_summary'] ['OtherCharges'] = 0;	
		$post_params['markup_price_summary'] ['TDS'] = 0;	
			//debug($post_params);exit;	
		$temp_booking = $this->module_model->serialize_enquiry_temp_booking_record($post_params, PACKAGE_BOOKING);	
		//debug($temp_booking); exit;	
		//debug($temp_booking);die('45');	
		$book_id = $temp_booking['book_id'];	
		$book_origin = $temp_booking['temp_booking_origin'];	
			
			
			
		$promocode_discount = 0;	
		//details for PGI	
			$email = $this->entity_email;	
			$phone = $this->entity_phone;	
			$verification_amount = ceil($post_params ['amount']+$convenience_fees-$promocode_discount);	
			$firstname = $this->entity_firstname;	
			$productinfo = META_PACKAGE_COURSE;	
			//check current balance before proceeding further	
			$currency = 'INR';	
			$currency_obj = new Currency(array('module_type' => 'package', 'from' => admin_base_currency(), 'to' => admin_base_currency()));	
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);	
			$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);	
				
			$selected_pm=$post_params ['selected_pm'];	
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){	
                $bank_code = $post_params ['bank_code'];	
            }	
            else	
                $bank_code = 0;	
            $selected_pm_array = explode("_", $selected_pm);	
            $selected_pm = $selected_pm_array[0];	
            $method = $selected_pm_array[1];	
            if($selected_pm == "WALLET")	
            	$method = "wallet";	
            if($method=="CC")	
                $payment_mode = "credit_card";	
            else if($method=="DC")	
                $payment_mode = "debit_card";	
            else if($method=="PPI")	
                $payment_mode = "paytm_wallet";	
            else if($selected_pm=="TECHP")	
                $payment_mode = "net_banking";	
            else	
                $payment_mode = "wallet";	
            $con_row = $this->master_currency->get_instant_recharge_convenience_fees($post_params ['amount'], $method, $bank_code);	
			//debug($post_params['payment_method']);debug($selected_pm);exit;	
			if ($domain_balance_status == true || $selected_pm) {	
				switch($post_params['payment_method']) {	
					case PAY_NOW : 	
						$this->load->model('transaction');	
						//$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();	
							
						$this->transaction->update_payment_record($post_params['app_reference'],$selected_pm,$payment_mode,$post_params['pay_ref_id']);	
						//echo $selected_pm;exit("Ggg");	
						//redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);	
						redirect(base_url().'index.php/payment_gateway/payment/'.$post_params['app_reference'].'/'.$post_params['temp_booking_id'].'/'.$selected_pm.'/'.$post_params['pay_ref_id']);	
						break;	
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;	
					break;	
				}	
			} else {	
				redirect(base_url().'index.php/hotel/exception?op=Amount Hotel Booking&notification=insufficient_balance');	
			}	
	}	
}

