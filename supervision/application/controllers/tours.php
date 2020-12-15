<?php
if (! defined ( 'BASEPATH' ))
  exit ( 'No direct script access allowed' );

class Tours extends CI_Controller {
  public function __construct() {
    parent::__construct ();
    $this->load->model(array('tours_model','custom_db'));
	$this->load->library('provab_mailer');
	$this->load->library('utility/notification','notification');
  }

  public function tour_destinations() {   
   $tour_destinations = $this->tours_model->tour_destinations();
   $page_data['tour_destinations'] = $tour_destinations;
   $this->template->view('tours/tour_destinations',$page_data);
 }
 public function tour_booking_request(){
  $condition = array();
  $get_data = $this->input->get();
  if (valid_array($get_data) == true) {

  } else {
    $c_date = date('Y-m-d');
    /*$condition [] = array(
        'TB.created_datetime',
        '>=',
        $this->db->escape(db_current_datetime($c_date))
        );*/
      }
 /* $condition [] = array(
      'TB.status ',
      '=',
      $this->db->escape('PENDING')
      );*/
      $total_records = $this->tours_model->tour_booking_report($condition, true);
      
      $table_data = $this->tours_model->tour_booking_report($condition, false, $offset, RECORDS_RANGE_2);
      $tour_list = $this->tours_model->tour_list();
      $page_data['tour_list'] = $tour_list;
      $page_data ['request_list'] = $table_data['data'];
      $this->template->view('tours/tour_booking_request', $page_data);
    }
    public function add_tour_destination_save() {
      //echo '<pre>'; print_r($_FILES); exit;   
     $list  = $_FILES['gallery']['name'];
     $total_images = count($list);
     //print_r($list); exit;
     for($i=0;$i<$total_images;$i++)
     {         
       $filename  = basename($list[$i]);
       $extension = pathinfo($filename, PATHINFO_EXTENSION);
       $uniqueno  = substr(uniqid(),0,5);
       $randno    = substr(rand(),0,5);
       $new       = $uniqueno.$randno.'.'.$extension;
       $folder    = $this->template->domain_image_upload_path();
       $folderpath= trim($folder.$new);
       $path      = addslashes($folderpath);
       move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $folderpath);              
       if($i==0)
       { 
         $Gallery_list = $new;
       }
       else
       {
         $Gallery_list = $Gallery_list.",".$new;   
       } 
     }  
     $banner_image = $_FILES['banner_image']['name'];
     $filename     = basename($banner_image);
     $extension    = pathinfo($filename, PATHINFO_EXTENSION);
     $uniqueno     = substr(uniqid(),0,5);
     $randno       = substr(rand(),0,5);
     $new          = $uniqueno.$randno.'.'.$extension;
     $folder       = $this->template->domain_image_upload_path();
     $folderpath   = trim($folder.$new);
     $path         = addslashes($folderpath);
     move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
     $banner_image = $new;         

     $data = $this->input->post();
    //debug($data);exit;
     $pkg_type    = sql_injection($data['pkg_type']);
     $destination = sql_injection($data['destination']);
     $description = sql_injection($data['description']);
     $highlights  = sql_injection($data['highlights']);
     $query       = "insert into tour_destinations set type='$pkg_type',
     destination='$destination', 
     description='$description',
     highlights='$highlights',
     status=1,
     gallery='$Gallery_list',
     banner_image='$banner_image',
     date=now()";
        //echo $query; exit;
     $return      = $this->tours_model->add_tour_destination_save($query);
     if(!$return)
     {
      echo $return; 
    } 
    redirect('tours/tour_destinations');  
  }
  public function delete_tour_destination($id) { //echo 'id'.$id; exit;
  $return = $this->tours_model->delete_tour_destination($id);
  if($return)
  {
   redirect('tours/tour_destinations'); 
 } 
 else
 {
   echo $return;
 }  
}
public function edit_tour_destination($id) {
  $tour_destination_details = $this->tours_model->tour_destination_details($id);
    //debug($tour_destination_details); //exit;     
  $page_data['tour_destination_details'] = $tour_destination_details;
    //debug($page_data); exit;
  $this->template->view('tours/edit_tour_destination',$page_data);
}
public function edit_tour_destination_save() {
  $data = $this->input->post();
    //debug($data);exit;
  $id          = sql_injection($data['id']);
  $pkg_type    = sql_injection($data['pkg_type']);
  $destination = sql_injection($data['destination']);
  $description = sql_injection($data['description']);
  $highlights  = sql_injection($data['highlights']);

  $ppg        = $_REQUEST['gallery_previous'];
  $total_ppg  = count($ppg) ;
  $ppg_list   = '';
  for($c=0;$c<$total_ppg;$c++)
  {
    if($ppg_list=='')
    {
      $ppg_list = $ppg[$c];
    }
    else
    {
      $ppg_list = $ppg_list.','.$ppg[$c];
    }       
  }
  if($total_ppg>0)
  {
    $ppg_list = $ppg_list.',';
  }
  else
  {
    $ppg_list = '';
  } 
  if($_FILES['gallery']['name'][0]!="")
  {       

    $list  = $_FILES['gallery']['name'];
    $total_images = count($list); 
     //print_r($list); exit;
    for($i=0;$i<$total_images;$i++)
    {
         // for setting the unique name of image starts @@@@@@@@@@@@@@@@@@@
      $filename  = basename($list[$i]);
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      $uniqueno  = substr(uniqid(),0,5);
      $randno    = substr(rand(),0,5);
      $new       = $uniqueno.$randno.'.'.$extension;
      $folder    = $this->template->domain_image_upload_path();
      $folderpath= trim($folder.$new);
      $path      = addslashes($folderpath);
      move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $folderpath);  

      if($i==0)
      { 
       $Gallery_list = $new;
     }
     else
     {
       $Gallery_list = $Gallery_list.",".$new;   
     } 
   }  
 }   

 $Gallery_list = $ppg_list.$Gallery_list;

 if(!empty($_FILES['banner_image']['name']))
 {
   $banner_image = $_FILES['banner_image']['name'];
   $filename     = basename($banner_image);
   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
   $uniqueno     = substr(uniqid(),0,5);
   $randno       = substr(rand(),0,5);
   $new          = $uniqueno.$randno.'.'.$extension;
   $folder       = $this->template->domain_image_upload_path();
   $folderpath   = trim($folder.$new);
   $path         = addslashes($folderpath);
   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
   $banner_image = $new; 
   $banner_image_update = ",banner_image='$banner_image'"; 
 }

 $query = "update tour_destinations set type='$pkg_type',
 destination='$destination', 
 description='$description',
 highlights='$highlights',
 gallery='$Gallery_list' ".$banner_image_update."
 where id='$id'";
        //echo $query; exit;
 $return = $this->tours_model->edit_tour_destination_save($query);
 if($return)
 {
  redirect('tours/edit_tour_destination/'.$id); 
} 
else
{
 echo $return;
} 
}

public function send_link_to_user($enquiry_reference_no,$redirect = true){
  $data = $this->tours_model->enquiry_user_details($enquiry_reference_no);

  if(!empty($data[0]->email))
  {
   // $mail_template = $data['enquiry_reference_no'];
    //$this->load->library ( 'provab_mailer' );
   // $s = $this->provab_mailer->send_mail ( $data['user_email'], 'Package Url', $mail_template );
    set_update_message ();
    if($redirect){      
      redirect ( base_url () . 'index.php/tours/tours_enquiry');
    }
    
  }
  /*$return = $this->custom_db->insert_record ( 'tour_booking_details', $data );
 
  if($return['status'])
  {
   // $mail_template = $data['enquiry_reference_no'];
    //$this->load->library ( 'provab_mailer' );
   // $s = $this->provab_mailer->send_mail ( $data['user_email'], 'Package Url', $mail_template );
    set_update_message ();
    redirect ( base_url () . 'index.php/tours/tours_enquiry');
      
  }*/
}
public function send_payment_link($enquiry_reference_no)
{
  $this->load->model('tours_model');
  $booking_fare = $this->input->post('new_price');
  $book_id='ZVZ-'.date('md').'-'.rand(1000,9999);
  $condition = array();
  $condition[] = array('TB.enquiry_reference_no', '=', $this->db->escape($enquiry_reference_no));
  $booking_data = $this->tours_model->tour_booking_report($condition);
  $booking_data=$booking_data['data'][$enquiry_reference_no];
  $tour_booking_details_data=array(
    'app_reference'=>$book_id,
    'basic_fare'=>$booking_fare,
    'currency_code'=>$booking_data['tours_details']['currency'],
    );
  $this->custom_db->update_record('tour_booking_details',$tour_booking_details_data,array('enquiry_reference_no'=>$enquiry_reference_no));
  $firstname = $booking_data['enquiry_details']['name'];
  $email = $booking_data['enquiry_details']['email'];
  $phone = $booking_data['enquiry_details']['phone'];
  $productinfo = 'package_id: '.$booking_data['tours_details']['package_id'];
  $convenience_fees = 0;
  $promocode_discount = 0;
  $promocode = '';
  $this->load->model('transaction_model');
  $this->transaction_model->create_payment_record($book_id, $booking_fare, $firstname, $email, $phone, $productinfo, $convenience_fees, $promocode_discount,$promocode);
  $payment_url = base_url().'index.php/payment_gateway/payment/'.$book_id;
  $payment_url = str_replace('supervision/', '', $payment_url);
  // echo $payment_url; exit('Exit script');
  set_update_message ();
  redirect ( base_url () . 'index.php/tours/tour_booking_request');
}
public function activation_tour_destination($id,$status) {
  $data = $this->input->post();
    //debug($data);exit;
  $return = $this->tours_model->activation_tour_destination($id,$status);
  if($return){redirect('tours/tour_destinations');} 
  else { echo $return;} 
}
public function add_tour() {
	//echo phpinfo();
  $tour_destinations = $this->tours_model->tour_destinations();     
  $tours_continent = $this->tours_model->get_tours_continent();
    //debug($tours_continent); exit;
  $page_data['tours_continent'] = $tours_continent;
  $page_data['tour_type'] = $this->tours_model->get_tour_type();
  $page_data['tour_subtheme'] = $this->tours_model->get_tour_subtheme();
  $this->template->view('tours/add_tour',$page_data);
}

public function no_of_weather($no_of_weather) {
    //echo $no_of_weather; exit;
  for($i=1;$i<=$no_of_weather;$i++)
  {
    echo '<div class="form-group">
    <label class="control-label col-sm-3" for="validation_current">Day '.$i.' </label>
  </div>';
  echo '<div class="form-group">
  <label class="control-label col-sm-3" for="validation_current">Location Name </label>
  <div class="col-sm-4 controls">
   <input type="text" name="weather_loc_name'.$i.'"
   placeholder="Enter location name" data-rule-required="true"
   class="form-control" required>                 
 </div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Temperature </label>
<div class="col-sm-4 controls">
  <select name="temperature'.$i.'" data-rule-required="true" class="form-control" required>';
    for($s=1;$s<=50;$s++)
    {
     echo '<option value="'.$s.'">'.$s.' Degree</option>';
   }  
   echo '</select>                
 </div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Weather Type </label>
<div class="col-sm-4 controls">
  <input type="checkbox" name="weather_type'.$i.'[]" value="1"> Mostly Sunny <br>                 
  <input type="checkbox" name="weather_type'.$i.'[]" value="2"> Partly Cloudy <br>                  
  <input type="checkbox" name="weather_type'.$i.'[]" value="3"> PM Shower <br>              
</div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Weather Details
</label>
<div class="col-sm-4 controls">
  <textarea name="weather_des'.$i.'" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Weather"></textarea>
</div>
</div>';                      
}
}

/*  public function add_tour_save() {
    $data = $this->input->post();
    //debug($data); exit;
    $package_name   = sql_injection($data['package_name']);
    $package_description = sql_injection($data['package_description']);
    $supplier_name   = sql_injection($data['supplier_name']);
        //$destination    = sql_injection($data['destination']);
        $tours_continent= sql_injection($data['tours_continent']);
        //$tours_country  = sql_injection($data['tours_country']);

        $tours_city      = $data['tours_city'];
        $tours_city_new     = $data['tours_city_new'];
        $tours_city = $tours_city_new;
        //debug($tours_city); exit;
        $tours_city     = implode(',',$tours_city);
        $duration       = sql_injection($data['duration']);

        //$tour_type      = sql_injection($data['tour_type']);

        $tour_type          = $data['tour_type'];
        $tour_type          = implode(',',$tour_type);

        $tours_country      = $data['tours_country'];
        $tours_country      = implode(',',$tours_country);


        $theme          = $data['theme'];
        $theme          = implode(',',$theme);
        $admin_approve_status = 1;


        
        $AUTO_INCREMENT = $this->tours_model->AUTO_INCREMENT('tours');
        $package_id     = 'AIRTP'.date('m').date('y').$AUTO_INCREMENT;

        $query = "insert into tours set package_id='$package_id',
        package_name='$package_name',
        package_description='$package_description',
        tour_type='$tour_type',
        theme='$theme', 
        tours_continent='$tours_continent', 
        tours_country='$tours_country',
        tours_city='$tours_city',
        duration='$duration',
        admin_approve_status = '$admin_approve_status',
        added_by = 'Admin',
        supplier_name = '$supplier_name',
        date=now()";        
    //  echo $query; exit;
    $return = $this->tours_model->add_tour_save($query);
    if($return)
    {   //echo 'saved'; exit;
            //redirect('tours/tour_list');
            redirect('tours/tour_dep_dates_p2/'.$package_id); 
        }
        else
        { echo $return; exit; }              
      }*/
      public function add_tour_save() {
        $data = $this->input->post();
        //debug($data);exit();
        $this->session->unset_userdata('edit_itinary');        
        $package_name   = ($data['package_name']);
        $package_description = ($data['package_description']);
        $exprie_date = date('Y-m-d',strtotime($data['tour_expire_date']));
        $supplier_name   = implode(',',$data['supplier']);
		$concerned_person   = implode(',',$data['concerned_supplier']);
        $tours_continent= $data['tours_continent'];
		$tours_continent      = implode(',',$tours_continent);
        $tours_city      = $data['tours_city'];
        //$tours_city_new     = $data['tours_city_new'];
       // $tours_city = $tours_city_new;
	    $tours_city = array_unique($tours_city);
        $tours_city     = implode(',',$tours_city);
        $duration       = ($data['duration']);
        $tour_type          = $data['tour_type_new'];
        $tour_type          = implode(',',$tour_type);
        $tours_country      = $data['tours_country'];
		$tours_country      = array_unique($tours_country);
        $tours_country      = implode(',',$tours_country);
        $theme          = $data['theme'];
        $theme          = implode(',',$theme);
        $admin_approve_status = 1;        
        $AUTO_INCREMENT = $this->tours_model->AUTO_INCREMENT('tours');
       // $package_id     = PROJECT_PREFIX.'-'.PACKAGE_BOOKING.'-'.date('dmY-Hi').'-'.$AUTO_INCREMENT;
		$trip_type=$data['trip_type'];
		$tour_code='#PT'.$AUTO_INCREMENT;
		$package_type=$data['package_type'];
		$valid_frm=implode(',',$data['valid_frm']);
		$valid_to=implode(',',$data['valid_to']);
		$multi_date=$data['multi_date'];
		$multi_date_array=explode(',',$multi_date);
		$inclusions = $data['inclusions'];
		$inclusions = json_encode($inclusions,1);
		//debug($_FILES);exit;
		$banner_image = '';
		  if(!empty($_FILES['banner_image']['name']))
		  {
			  
		   $banner_image = $_FILES['banner_image']['name'];
		   $banner_image = time().$banner_image;
		   $filename     = basename($banner_image);
		   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
		   $uniqueno     = substr(uniqid(),0,5);
		   $randno       = substr(rand(),0,5);
		   $new          = $uniqueno.$randno.'.'.$extension;
		   $folder       = $this->template->domain_image_upload_path();
		   $folderpath   = trim($folder.$new);
		   $path         = addslashes($folderpath);
		   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
		   $banner_image = $new; 
		   $banner_image_update = $banner_image; 
		 }else
		 {
		   $banner_image_update = '';
		  
		 }
        $tours_data = array(
		 'trip_type'=>$trip_type,
		 'tour_code'=>$tour_code,
        // 'package_id'=>$package_id,
         'package_name'=>$package_name,
		 'package_type'=>$package_type,
		 'valid_frm'=>$valid_frm,
		 'valid_to'=>$valid_to,
		 'multi_date'=>$multi_date,
         //'package_description'=>$package_description,
      //   'expire_date' =>$exprie_date,
         'tour_type'=>$tour_type,
         'theme'=>$theme,
         'tours_continent'=>$tours_continent,
         'tours_country'=>$tours_country,
         'tours_city'=>$tours_city,
         'duration'=>$duration,
         'admin_approve_status'=>$admin_approve_status,
         'added_by'=>'Admin',
         'supplier_name'=>$supplier_name,
		 'concerned_person'=>$concerned_person,
		 'banner_image'=>$banner_image_update,
		 'inclusions_checks'=>$inclusions,
         'date'=>date('Y-m-d'));   

        // debug($tours_data);exit;    
        $return = $this->custom_db->insert_record('tours',$tours_data);
        if($return['status'])
        {
			$tour_id = $return['insert_id'];
			if($package_type=='group'){
				
				foreach ($multi_date_array as $val_key => $valid_frm_id) {
					$this->custom_db->insert_record('tour_dep_dates',array('tour_id' => $tour_id, 'dep_date' => date('Y-m-d',strtotime($valid_frm_id))));
				}
			}else{
				
				foreach ($data['valid_frm'] as $val_key => $valid_frm_id) {
					$this->custom_db->insert_record('tour_valid_from_to_date',array('tour_id' => $tour_id, 'valid_from' => $valid_frm_id,'valid_to' => $data['valid_to'][$val_key]));
				}
			}
			foreach ($data['tours_continent'] as $continent_id) {
				$this->custom_db->insert_record('tours_continent_wise',array('tour_id' => $tour_id, 'continent_id' => $continent_id));
			}
			$this->custom_db->delete_record('tours_country_wise', array('tour_id'=>$tour_id));
			foreach ($data['tours_country'] as $country_id) {
				$this->custom_db->insert_record('tours_country_wise',array('tour_id' => $tour_id, 'country_id' => $country_id));
			}
			foreach ($data['tours_city'] as $city_id) {
				$this->custom_db->insert_record('tours_city_wise',array('tour_id' => $tour_id, 'city_id' => $city_id));
			}
			foreach ($data['tour_type_new'] as $tours_type) {
				$this->custom_db->insert_record('tour_package_map',array('tour_id' => $tour_id, 'type_id' => $tours_type));
			}
			foreach ($data['hotel_city'] as $hotel_key =>$hotel_id) {
				$this->custom_db->insert_record('tours_hotel_details',array('tour_id' => $tour_id, 'hotel_id' => $data['hotel_name'][$hotel_key], 'city' => $data['hotel_city'][$hotel_key],'no_of_night' => $data['no_night'][$hotel_key],'star_rating' => $data['star_rating'][$hotel_key],'city_id' => $data['hotel_city_id'][$hotel_key]));
			}
			foreach ($data['hotel_city'] as $hotel_key =>$hotel_id) {
				$this->custom_db->insert_record('tour_visited_cities',array('tour_id' => $tour_id, 'city' => json_encode($data['hotel_city_id'][$hotel_key],1),'no_of_nights' => $data['no_night'][$hotel_key]));
			}
			//debug()
			foreach ($data['optional_tour'] as $optional_key =>$optional_id) {
				$this->custom_db->insert_record('tour_optional_tour_details',array('tour_id' => $tour_id, 'optional_tour' => $optional_id));
			}
       // redirect('tours/tour_itinerary_p2/'.$package_id); 
		 redirect(base_url('tours/tour_itinerary_p2/'.$tour_id));
      }
      else
      { 
       echo $return; exit; 
     }              
   }
   public function tour_dep_date($tour_dep_date) {
    //echo $tour_dep_date; exit;
    echo '<div class="form-group">
    <label class="control-label col-sm-3" for="validation_current">Departure Dates : </label>
  </div>';
  echo '<div class="form-group">
  <label class="control-label col-sm-3" for="validation_current">Date </label>
  <div class="col-sm-4 controls">
   <input type="text" name="weather_loc_name'.$i.'" value="'.$tour_dep_date.'">                 
 </div>
</div>';
}
public function draft_list() { 
 
 $tour_list = $this->tours_model->draft_list();
 $page_data['tour_list'] = $tour_list;

  $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
  $page_data['tour_destinations'] = $tour_destinations; 
  $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
  $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); 
  $tour_dep_dates_list_published_wd = $this->tours_model->tour_dep_dates_list_published_wd(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 
  $page_data['tour_dep_dates_list_published_wd'] = $tour_dep_dates_list_published_wd; 
  foreach($page_data['tour_list'] as $tour_key => $tour_data){

		$from_to_data=$this->custom_db->single_table_records('tour_valid_from_to_date','valid_from,valid_to',array('tour_id'=>$tour_data['id']))['data'];  
		$multiple_date = $this->custom_db->single_table_records('tour_dep_date','dep_date',array('tour_id'=>$tour_data['id']))['data']; 
	//echo $this->db->last_query();
	$page_data['tour_list'][$tour_key]['from_to_data']=json_encode($from_to_data);
	$page_data['tour_list'][$tour_key]['multiple_date']=json_encode($multiple_date);
  }
//	debug($page_data);exit; 
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
  $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
  $page_data['country_code_list'] = $this->db_cache_api->get_country_code_list();
  
  $this->template->view('tours/draft_tour_list',$page_data);
  $array = array(
    'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method(),
    'edit_itinary' => true
    );    
  $this->session->set_userdata( $array );
}
public function tour_list() { 
 
 $tour_list = $this->tours_model->tour_list();
 $page_data['tour_list'] = $tour_list;

  $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
  $page_data['tour_destinations'] = $tour_destinations; 
  $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
  $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); 
  $tour_dep_dates_list_published_wd = $this->tours_model->tour_dep_dates_list_published_wd(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 
  $page_data['tour_dep_dates_list_published_wd'] = $tour_dep_dates_list_published_wd; 
	foreach($page_data['tour_list'] as $tour_key => $tour_data){

		$from_to_data=$this->custom_db->single_table_records('tour_valid_from_to_date','valid_from,valid_to',array('tour_id'=>$tour_data['id']))['data'];  
		$multiple_date = $this->custom_db->single_table_records('tour_dep_date','dep_date',array('tour_id'=>$tour_data['id']))['data']; 
		//echo $this->db->last_query();
		$page_data['tour_list'][$tour_key]['from_to_data']=json_encode($from_to_data);
		$page_data['tour_list'][$tour_key]['multiple_date']=json_encode($multiple_date);
	}
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
  $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
  $page_data['country_code_list'] = $this->db_cache_api->get_country_code_list();
  
  $this->template->view('tours/tour_list',$page_data);
  $array = array(
    'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method(),
    'edit_itinary' => true
    );    
  $this->session->set_userdata( $array );
}
public function verify_tour_list() { 
 
 $tour_list = $this->tours_model->verify_tour_list();
 $page_data['tour_list'] = $tour_list;

  $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
  $page_data['tour_destinations'] = $tour_destinations; 
  $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
  $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); 
  $tour_dep_dates_list_published_wd = $this->tours_model->tour_dep_dates_list_published_wd(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 
  $page_data['tour_dep_dates_list_published_wd'] = $tour_dep_dates_list_published_wd; 
	foreach($page_data['tour_list'] as $tour_key => $tour_data){

		$from_to_data=$this->custom_db->single_table_records('tour_valid_from_to_date','valid_from,valid_to',array('tour_id'=>$tour_data['id']))['data'];  
		$multiple_date = $this->custom_db->single_table_records('tour_dep_date','dep_date',array('tour_id'=>$tour_data['id']))['data']; 
		//echo $this->db->last_query();
		$page_data['tour_list'][$tour_key]['from_to_data']=json_encode($from_to_data);
		$page_data['tour_list'][$tour_key]['multiple_date']=json_encode($multiple_date);
  }
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
  $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
  $page_data['country_code_list'] = $this->db_cache_api->get_country_code_list();
  
  $this->template->view('tours/verify_tour_list',$page_data);
  $array = array(
    'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method(),
    'edit_itinary' => true
    );    
  $this->session->set_userdata( $array );
}
public function published_tour_list() { 
 
 $tour_list = $this->tours_model->verified_tour_list();
 $page_data['tour_list'] = $tour_list;

  $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
  $page_data['tour_destinations'] = $tour_destinations; 
  $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
  $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); 
  $tour_dep_dates_list_published_wd = $this->tours_model->tour_dep_dates_list_published_wd(); //debug($tour_dep_dates_list_all);exit;
  $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 
  $page_data['tour_dep_dates_list_published_wd'] = $tour_dep_dates_list_published_wd; 
	foreach($page_data['tour_list'] as $tour_key => $tour_data){

		$from_to_data=$this->custom_db->single_table_records('tour_valid_from_to_date','valid_from,valid_to',array('tour_id'=>$tour_data['id']))['data'];  
		$multiple_date = $this->custom_db->single_table_records('tour_dep_date','dep_date',array('tour_id'=>$tour_data['id']))['data']; 
		//echo $this->db->last_query();
		$page_data['tour_list'][$tour_key]['from_to_data']=json_encode($from_to_data);
		$page_data['tour_list'][$tour_key]['multiple_date']=json_encode($multiple_date);
  }
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
  $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
  $page_data['country_code_list'] = $this->db_cache_api->get_country_code_list();
  
  $this->template->view('tours/verified_tour_list',$page_data);
  $array = array(
    'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method(),
    'edit_itinary' => true
    );    
  $this->session->set_userdata( $array );
}
      public function agent_tour_list() { 
        $query = 'select * from tours where admin_approve_status = 1 AND agent_id IS NOT NULL  AND status_delete != "1" order by id desc'; 
        $tour_list = $this->custom_db->get_result_by_query($query);
        $page_data['tour_list'] = json_decode(json_encode($tour_list),true);
        $tour_destinations = $this->tours_model->get_tour_destinations();
        $page_data['tour_destinations'] = $tour_destinations; 
        $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all();
        $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
        $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); 
        $tour_dep_dates_list_published_wd = $this->tours_model->tour_dep_dates_list_published_wd();
        $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 
        $page_data['tour_dep_dates_list_published_wd'] = $tour_dep_dates_list_published_wd;
        $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
        $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
        $page_data['country_code_list'] = $this->db_cache_api->get_country_code_list();
        $this->template->view('tours/tour_list_agent',$page_data);
        $array = array(
          'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method(),
          'edit_itinary' => true
          );    
        $this->session->set_userdata( $array );
      }

  public function tour_list_pending() { //echo 'tours'; exit;
  //  echo "hiii";exit();
 $tour_list = $this->tours_model->tour_list_pending();
    //debug($tour_list); exit();
 $page_data['tour_list'] = $tour_list;

    $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
    $page_data['tour_destinations'] = $tour_destinations; 
    $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
    $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
    $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); //debug($tour_dep_dates_list_all);exit;
    $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 

    $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
    $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
        //debug($page_data); exit;
    $this->template->view('tours/tour_list_pending',$page_data);
  }
  public function activation_tour_package($id,$status) {
    $query = "update tours set status='$status' where id='$id'";
    $return = $this->tours_model->activation_tour_package($query);
    if($return){redirect('tours/tour_list_pending');} 
    else { echo $return;} 
  }
	public function delete_tour_package($id,$list) { //echo 'id'.$id; exit;
		$return = $this->tours_model->delete_tour_package($id);
		redirect('tours/'.$list);
	}
  public function tour_dep_dates($tour_id,$list) { //echo 'tour_dep_dates'; exit;
  error_reporting(0);
 $page_data['tour_id'] = $tour_id;
 $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);
 if($page_data['tour_data']['package_type']=='fit'){
	 $tour_dep_dates = $this->tours_model->tour_dep_dates_from_to_list($tour_id);
 }else{
	 $tour_dep_dates = $this->tours_model->tour_dep_dates($tour_id);
 }
 //if($page_data['tour_data']==){
	 
 //}else{
	 
 //}
 $page_data['tour_dep_dates'] = $tour_dep_dates;
  $page_data['list'] = $list;
 $this->template->view('tours/tour_dep_dates',$page_data);
}
public function tour_dep_dates_p2($package_id) { 
  
    //debug($_POST); exit();
    $package_data = $this->tours_model->package_data($package_id); //debug($package_data);exit;
    $tour_id = $package_data['id'];

   $page_data['tour_id'] = $tour_id;
   $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);
   $tour_dep_dates = $this->tours_model->tour_dep_dates($tour_id);
   $page_data['tour_dep_dates'] = $tour_dep_dates;
   if(!empty($tour_dep_dates))
   {
     $page_data['flow'] = 'Next';
   }
    // debug($page_data);exit;   
   $this->template->view('tours/tour_dep_dates_p2',$page_data);
 }
 public function tour_dep_date_save() {
  $data = $this->input->post();
    //debug($data);exit;
	$multi_date_array=explode(',',$data['multi_date']);
 // $dep_date = $data['tour_dep_date'];
  $tour_id  = $data['tour_id'];

  //$check_tour_dep_dates = $this->tours_model->check_tour_dep_dates($tour_id,$dep_date);
  //if($check_tour_dep_dates>0)
 // {
  // redirect('tours/tour_dep_dates/'.$tour_id); exit;
 //}
	$package_type = $data['package_type'];
	if($package_type =='group'){		
		foreach ($multi_date_array as $val_key => $valid_frm_id) {
			$return= $this->custom_db->insert_record('tour_dep_dates',array('tour_id' => $tour_id, 'dep_date' =>date('Y-m-d',strtotime($valid_frm_id))));
		} 
	}else{
		
		foreach ($data['valid_frm'] as $val_key => $valid_frm_id) {
			$return=$this->custom_db->insert_record('tour_valid_from_to_date',array('tour_id' => $tour_id, 'valid_from' => $valid_frm_id,'valid_to' => $data['valid_to'][$val_key]));
		}
	}
// $query  = "insert into tour_dep_dates set tour_id='$tour_id',dep_date='$dep_date'";
 //$return = $this->tours_model->tour_dep_date_save($query);
 if($return)
 {
   redirect('tours/tour_dep_dates/'.$tour_id.'/'.$data['list']); 
 } 
 else { echo $return;}  
}
public function tour_dep_dates_p2_save() {
  $data = $this->input->post();   
    // debug($data); exit();
  $dep_date = $data['tour_dep_date'];
  $tour_id  = $data['tour_id'];
  // echo $tour_id;exit;
    $tour_data  = $this->tours_model->tour_data($tour_id); 
    // debug($tour_data);exit;
    $package_id = $tour_data['package_id'];
    //if(isset($data['ask_for_select']) && $data['ask_for_select']){

   $check_tour_dep_dates = $this->tours_model->check_tour_dep_dates($tour_id,$dep_date);
   if($check_tour_dep_dates>0)
   {
    redirect('tours/tour_dep_dates_p2/'.$package_id); exit;
  }

  $query  = "insert into tour_dep_dates set tour_id='$tour_id',dep_date='$dep_date'";
  $return = $this->tours_model->tour_dep_date_save($query);
  if($return)
  {
    redirect('tours/tour_dep_dates_p2/'.$package_id);
  } 
  else { echo $return;} 
   //   }
    //redirect('tours/tour_dep_dates_p2/'.$package_id);
}
public function delete_tour_dep_date($id,$tour_id,$type,$list) { 
  $return = $this->tours_model->delete_tour_dep_date($id,$tour_id,$type);
  if($return)
  {
   redirect('tours/tour_dep_dates/'.$tour_id.'/'.$list);
 } 
 else
 {
   echo $return;
 }  
}
public function delete_tour_dep_date_p2($id,$tour_id) { 
  $return = $this->tours_model->delete_tour_dep_date($id);
  if($return)
  {
       $tour_data  = $this->tours_model->tour_data($tour_id); //debug($package_data);exit;
       $package_id = $tour_data['package_id'];
       redirect('tours/tour_dep_dates_p2/'.$package_id);
     } 
     else
     {
       echo $return;
     }  
   }
   public function tour_visited_cities($tour_id) {
     $page_data['tour_id'] = $tour_id;
     $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);
     $tour_visited_cities = $this->tours_model->tour_visited_cities($tour_id);
     $page_data['tour_visited_cities'] = $tour_visited_cities;
     $page_data['tours_city_name'] = $this->tours_model->tours_city_name();

     if(!empty($tour_visited_cities))
     {
       $no_of_nights = 0;
       foreach($tour_visited_cities as $tvcKey => $tvcValue)
       {
        $no_of_nights += $tvcValue['no_of_nights'];
      }
      $page_data['total_no_of_nights'] = $no_of_nights;     
    }
    else
    {
     $page_data['total_no_of_nights'] = 0;
   }

   // debug($page_data); exit();

   $this->template->view('tours/tour_visited_cities',$page_data);
 }
  public function tour_visited_cities_p2($tour_id) { //echo $tour_id; exit; 
   $page_data['tour_id']   = $tour_id;
   $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);        
   $tour_visited_cities = $this->tours_model->tour_visited_cities($tour_id);      
   $page_data['tour_visited_cities'] = $tour_visited_cities;
   if(!empty($tour_visited_cities))
   {
     $no_of_nights = 0;
     foreach($tour_visited_cities as $tvcKey => $tvcValue)
     {
      $no_of_nights += $tvcValue['no_of_nights'];
    }
    if($page_data['tour_data']['duration']==$no_of_nights)
    {
      $page_data['flow'] = 'Next';
    }
    $page_data['total_no_of_nights'] = $no_of_nights;     
  }
  else
  {
   $page_data['total_no_of_nights'] = 0;
 }
 $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
    //debug($page_data);exit;
 $this->template->view('tours/tour_visited_cities_p2',$page_data);
}
public function no_of_nights($no_of_nights) {
    //echo 'no_of_nights'.$no_of_nights; exit;
    //$page_data['no_of_nights'] = $no_of_nights;
    //$return = $this->template->view('tours/no_of_nights',$page_data);
    //return $return;

  for($i=1;$i<=$no_of_nights;$i++)
  {
    echo '<hr>';
    echo    '<div class="form-group">
    <label class="control-label col-sm-3" for="validation_current">Day '.$i.' </label>
  </div>';
  echo    '<div class="form-group">
  <label class="control-label col-sm-3" for="validation_current">Day Program Title </label>
  <div class="col-sm-4 controls">
   <input type="text" name="program_title[]"
   placeholder="Enter Program Title" data-rule-required="true"
   class="form-control" required>                 
 </div>
</div>';      
echo    '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Program Description
</label>
<div class="col-sm-8 controls">
  <textarea name="program_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="10" placeholder="Description"></textarea>
</div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
<div class="col-sm-4 controls">
 <input type="text" name="hotel_name[]"
 placeholder="Enter hotel name" data-rule-required="true"
 class="form-control" required>                 
</div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Star Rating </label>
<div class="col-sm-4 controls">
  <select name="rating[]" data-rule-required="true" class="form-control" required>';
    for($s=1;$s<=5;$s++)
    {
     echo '<option value="'.$s.'">'.$s.' Star</option>';
   }  
   echo '</select>                
 </div>
</div>';      
      /*echo '<div class="form-group">
                <label class="control-label col-sm-3" for="validation_current">Hotel Description
                </label>
                <div class="col-sm-4 controls">
                <textarea name="hotel_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Description"></textarea>
                </div>
              </div>';*/          
             echo    '<div class="form-group">
             <label class="control-label col-sm-3" for="validation_current">Accomodation </label>
             <div class="col-sm-4 controls">
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Breakfast"> Breakfast <br>                 
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Lunch"> Lunch <br>                 
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Dinner"> Dinner <br>                 
             </div>
           </div>';

         }    
       }

       public function tour_visited_cities_save() {
        $data = $this->input->post();
    //debug($data); exit;
        $tour_id             = sql_injection($data['tour_id']);
        $city                = sql_injection($data['city']);
        $sightseeing         = sql_injection($data['sightseeing']);
        $no_of_nights        = sql_injection($data['no_of_nights']);
        $includes_city_tours = sql_injection($data['includes_city_tours']);

        $program_title       = $data['program_title'];
        $program_des         = $data['program_des'];
        $hotel_name          = $data['hotel_name'];
        $rating              = $data['rating'];
    //$hotel_des           = $data['hotel_des'];
        $accomodation        = $data['accomodation'];

        $itinerary = array();

        for($i=0;$i<$no_of_nights ;$i++)
        {
         $itinerary[$i]['program_title'] = sql_injection($program_title[$i]);
         $itinerary[$i]['program_des']   = sql_injection($program_des[$i]);
         $itinerary[$i]['hotel_name']    = sql_injection($hotel_name[$i]);
         $itinerary[$i]['rating']        = sql_injection($rating[$i]);
           //$itinerary[$i]['hotel_des']     = sql_injection($hotel_des[$i]);
         $itinerary[$i]['accomodation']  = $accomodation[$i];
       }
    //debug($itinerary); //exit;
       $itinerary = json_encode($itinerary,1);       
        //debug($itinerary);

       $query  = "insert into tour_visited_cities set tour_id='$tour_id',
       city='$city',
       sightseeing='$sightseeing',
       no_of_nights='$no_of_nights',
       includes_city_tours='$includes_city_tours',
       itinerary='$itinerary'";
        //echo $query; exit;
       $return = $this->tours_model->tour_visited_cities_save($query);
       if($return)
       {
         redirect('tours/tour_visited_cities/'.$tour_id);
       } 
       else { echo $return;}  
     }
     public function tour_visited_cities_p2_save() {
      error_reporting(0);
      $data = $this->input->post();
 
      $tour_id             = $data['tour_id'];
      $city                = $data['city'];
      $city                = json_encode($city,1);
      $no_of_nights        = $data['no_of_nights'];
    //$sightseeing         = sql_injection($data['sightseeing']);
    //$includes_city_tours = sql_injection($data['includes_city_tours']);

      $query  = "insert into tour_visited_cities set tour_id='$tour_id',
      city='$city',no_of_nights='$no_of_nights'";
        //echo $query; exit;
      $return = $this->tours_model->query_run($query);
      if($return)
      {
       redirect('tours/tour_visited_cities_p2/'.$tour_id);
     } 
     else { echo $return;}  
   }  
  public function delete_tour_visited_cities($id,$tour_id) { //echo 'id'.$id; exit;
  $return = $this->tours_model->delete_tour_visited_cities($id);
  if($return)
  {
   redirect('tours/tour_visited_cities/'.$tour_id);
 } 
 else
 {
   echo $return;
 }  
}
  public function delete_tour_visited_cities_p2($id,$tour_id) { //echo 'id'.$id; exit;
  $return = $this->tours_model->delete_tour_visited_cities($id);
  if($return)
  {
   redirect('tours/tour_visited_cities_p2/'.$tour_id);
 } 
 else
 {
   echo $return;
 }  
}
public function edit_tour_visited_cities($id,$tour_id) { 
  $tour_visited_cities_details = $this->tours_model->tour_visited_cities_details($id);
    //debug($tour_visited_cities_details); exit;
  $page_data['tour_visited_cities_details'] = $tour_visited_cities_details; 
  $page_data['id'] = $id;
  $page_data['tour_id'] = $tour_id;
  $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();  
  $this->template->view('tours/tour_visited_cities',$page_data);
}
public function edit_tour_visited_cities_p2($id,$tour_id) { 
  $tour_visited_cities_details = $this->tours_model->tour_visited_cities_details($id);
    //debug($tour_visited_cities_details); exit;
  $page_data['tour_visited_cities_details'] = $tour_visited_cities_details; 
  $page_data['id'] = $id;
  $page_data['tour_id'] = $tour_id;
  $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);  
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
  $tour_visited_cities = $this->tours_model->tour_visited_cities($tour_id);
  $page_data['tour_visited_cities'] = $tour_visited_cities;
  if(!empty($tour_visited_cities))
  {
   $no_of_nights = 0;
   foreach($tour_visited_cities as $tvcKey => $tvcValue)
   {
    $no_of_nights += $tvcValue['no_of_nights'];
  }     
  $page_data['total_no_of_nights'] = $no_of_nights;     
}
else
{
 $page_data['total_no_of_nights'] = 0;
}
$this->template->view('tours/edit_tour_visited_cities_p2',$page_data);
}

public function edit_tour_visited_cities_save() {
  $data = $this->input->post();
    //debug($data); exit;

  $id                  = sql_injection($data['id']);
  $tour_id             = sql_injection($data['tour_id']);
  $city                = $data['city'];
  $city                = json_encode($city);
  $no_of_nights        = sql_injection($data['no_of_nights']);

  $query  = "update tour_visited_cities set city='$city', no_of_nights='$no_of_nights' where id='$id'";
        //echo $query; exit;
  $return = $this->tours_model->edit_tour_visited_cities_save($query);
  if($return)
  {
   redirect('tours/edit_tour_visited_cities/'.$id.'/'.$tour_id);
 } 
 else { echo $return; } 
}
public function edit_tour_visited_cities_p2_save() {
  $data = $this->input->post();
    //debug($data); exit;

  $id                  = sql_injection($data['id']);
  $tour_id             = sql_injection($data['tour_id']);
  $city                = $data['city'];
  $city                = json_encode($city,1);
  $no_of_nights        = sql_injection($data['no_of_nights']);

  $query  = "update tour_visited_cities set city='$city', no_of_nights='$no_of_nights' where id='$id'";
        //echo $query; exit;
  $return = $this->tours_model->edit_tour_visited_cities_save($query);
  if($return)
  {
   redirect('tours/tour_visited_cities_p2/'.$tour_id);
 } 
 else { echo $return; } 
}
public function no_of_nights2($no_of_nights,$id,$tour_id) {
      //echo $no_of_nights.$id.$tour_id; exit;
  $tour_data = $this->tours_model->tour_data($tour_id);
  $tour_visited_cities_details = $this->tours_model->tour_visited_cities_details($id);

  if($no_of_nights<$tour_visited_cities_details['no_of_nights'])
  {

   $itinerary = $tour_visited_cities_details['itinerary']; 
   $itinerary = json_decode($itinerary,1);
              //echo '<pre>'; print_r($itinerary);
   foreach($itinerary as $key => $value)
   {
     $accomodation = $value['accomodation'];
     if(in_array('Breakfast',$accomodation))
       {$Breakfast='checked';}else{$Breakfast='';}
     if(in_array('Lunch',$accomodation))
       {$Lunch='checked';}else{$Lunch='';}
     if(in_array('Dinner',$accomodation))
       {$Dinner='checked';}else{$Dinner='';}
     echo '<hr>';
     echo    '<div class="form-group">
     <label class="control-label col-sm-3" for="validation_current">Day '.($key+1).' </label>
   </div>';
   echo    '<div class="form-group">
   <label class="control-label col-sm-3" for="validation_current">Day Program Title </label>
   <div class="col-sm-4 controls">
     <input type="text" name="program_title[]" value="'.trim(addslashes($value['program_title'])).'"
     placeholder="Enter Program Title" data-rule-required="true"
     class="form-control" required>                 
   </div>
 </div>';     
 echo    '<div class="form-group">
 <label class="control-label col-sm-3" for="validation_current">Program Description
 </label>
 <div class="col-sm-8 controls">
  <textarea name="program_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="5" placeholder="Description">'.$value['program_des'].'</textarea>
</div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
<div class="col-sm-4 controls">
 <input type="text" name="hotel_name[]" value="'.trim(addslashes($value['hotel_name'])).'"
 placeholder="Enter hotel name" data-rule-required="true"
 class="form-control" required>                 
</div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Star Rating </label>
<div class="col-sm-4 controls">
  <select name="rating[]" data-rule-required="true" class="form-control" required>';
    for($s=1;$s<=5;$s++)
    {
     echo '<option value="'.$s.'">'.$s.' Star</option>';
   }  
   echo '</select>                
 </div>
</div>';      
      /*echo '<div class="form-group">
                <label class="control-label col-sm-3" for="validation_current">Hotel Description
                </label>
                <div class="col-sm-4 controls">
                <textarea name="hotel_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Description">'.$value['hotel_des'].'</textarea>
                </div>
              </div>';*/          
             echo  '<div class="form-group">
             <label class="control-label col-sm-3" for="validation_current">Accomodation </label>
             <div class="col-sm-4 controls">
               <input type="checkbox" name="accomodation['.($key).'][]" value="Breakfast" '.$Breakfast.'> Breakfast <br>                  
               <input type="checkbox" name="accomodation['.($key).'][]" value="Lunch" '.$Lunch.'> Lunch <br>                  
               <input type="checkbox" name="accomodation['.($key).'][]" value="Dinner" '.$Dinner.'> Dinner <br>                 
             </div>
           </div>';
           if($no_of_nights==($key+1))
             {break;}
         }

       }
       else
       {
        $itinerary = $tour_visited_cities_details['itinerary']; 
        $itinerary = json_decode($itinerary,1);
              //echo '<pre>'; print_r($itinerary);
        foreach($itinerary as $key => $value)
        {
         $accomodation = $value['accomodation'];
         if(in_array('Breakfast',$accomodation))
           {$Breakfast='checked';}else{$Breakfast='';}
         if(in_array('Lunch',$accomodation))
           {$Lunch='checked';}else{$Lunch='';}
         if(in_array('Dinner',$accomodation))
           {$Dinner='checked';}else{$Dinner='';}
         echo '<hr>';
         echo    '<div class="form-group">
         <label class="control-label col-sm-3" for="validation_current">Day '.($key+1).' </label>
       </div>';
       echo    '<div class="form-group">
       <label class="control-label col-sm-3" for="validation_current">Day Program Title </label>
       <div class="col-sm-4 controls">
         <input type="text" name="program_title[]" value="'.trim(addslashes($value['program_title'])).'"
         placeholder="Enter Program Title" data-rule-required="true"
         class="form-control" required>                 
       </div>
     </div>';     
     echo    '<div class="form-group">
     <label class="control-label col-sm-3" for="validation_current">Program Description
     </label>
     <div class="col-sm-8 controls">
      <textarea name="program_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="5" placeholder="Description">'.$value['program_des'].'</textarea>
    </div>
  </div>';
  echo '<div class="form-group">
  <label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
  <div class="col-sm-4 controls">
   <input type="text" name="hotel_name[]" value="'.trim(addslashes($value['hotel_name'])).'"
   placeholder="Enter hotel name" data-rule-required="true"
   class="form-control" required>                 
 </div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Star Rating </label>
<div class="col-sm-4 controls">
  <select name="rating[]" data-rule-required="true" class="form-control" required>';
    for($s=1;$s<=5;$s++)
    {
     echo '<option value="'.$s.'">'.$s.' Star</option>';
   }  
   echo '</select>                
 </div>
</div>';      
      /*echo '<div class="form-group">
                <label class="control-label col-sm-3" for="validation_current">Hotel Description
                </label>
                <div class="col-sm-4 controls">
                <textarea name="hotel_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Description">'.$value['hotel_des'].'</textarea>
                </div>
              </div>';*/          
             echo  '<div class="form-group">
             <label class="control-label col-sm-3" for="validation_current">Accomodation </label>
             <div class="col-sm-4 controls">
               <input type="checkbox" name="accomodation['.($key).'][]" value="Breakfast" '.$Breakfast.'> Breakfast <br>                  
               <input type="checkbox" name="accomodation['.($key).'][]" value="Lunch" '.$Lunch.'> Lunch <br>                  
               <input type="checkbox" name="accomodation['.($key).'][]" value="Dinner" '.$Dinner.'> Dinner <br>                 
             </div>
           </div>';             
         }  
       }
       if($no_of_nights>$tour_visited_cities_details['no_of_nights']) {
        for($i=$tour_visited_cities_details['no_of_nights']+1;$i<=$no_of_nights;$i++)
        {
          echo '<hr>';
          echo    '<div class="form-group">
          <label class="control-label col-sm-3" for="validation_current">Day '.$i.' </label>
        </div>';
        echo    '<div class="form-group">
        <label class="control-label col-sm-3" for="validation_current">Day Program Title </label>
        <div class="col-sm-4 controls">
         <input type="text" name="program_title[]"
         placeholder="Enter Program Title" data-rule-required="true"
         class="form-control" required>                 
       </div>
     </div>';     
     echo    '<div class="form-group">
     <label class="control-label col-sm-3" for="validation_current">Program Description
     </label>
     <div class="col-sm-8 controls">
      <textarea name="program_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="5" placeholder="Description"></textarea>
    </div>
  </div>';
  echo '<div class="form-group">
  <label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
  <div class="col-sm-4 controls">
   <input type="text" name="hotel_name[]"
   placeholder="Enter hotel name" data-rule-required="true"
   class="form-control" required>                 
 </div>
</div>';
echo '<div class="form-group">
<label class="control-label col-sm-3" for="validation_current">Star Rating </label>
<div class="col-sm-4 controls">
  <select name="rating[]" data-rule-required="true" class="form-control" required>';
    for($s=1;$s<=5;$s++)
    {
     echo '<option value="'.$s.'">'.$s.' Star</option>';
   }  
   echo '</select>                
 </div>
</div>';      
      /*echo '<div class="form-group">
                <label class="control-label col-sm-3" for="validation_current">Hotel Description
                </label>
                <div class="col-sm-4 controls">
                <textarea name="hotel_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Description"></textarea>
                </div>
              </div>';*/          
             echo  '<div class="form-group">
             <label class="control-label col-sm-3" for="validation_current">Accomodation </label>
             <div class="col-sm-4 controls">
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Breakfast"> Breakfast <br>                 
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Lunch"> Lunch <br>                 
               <input type="checkbox" name="accomodation['.($i-1).'][]" value="Dinner"> Dinner <br>                 
             </div>
           </div>';
         }
       }  
     }
     public function edit_tour_package($tour_id) {
  
		$tour_data = $this->tours_model->tour_data($tour_id); //debug($tour_data); exit; 
		// debug($tour_data);exit; 
		$page_data['tour_data'] = $tour_data;
		$tour_destinations = $this->tours_model->tour_destinations();
		$page_data['tour_destinations'] = $tour_destinations;
		$page_data['tour_id'] = $tour_id;

		$tours_continent = $this->tours_model->tours_continent();
	// echo f"dsfgdgdf";exit;
		$page_data['tours_continent_country'] = $this->tours_model->tours_continent_country($tour_id);
		// debug($page_data);exit;
		$page_data['tours_country_city']      = $this->tours_model->tours_country_city($tour_id);
	   $page_data['tours_country_name']      = $this->tours_model->tour_country();
		$page_data['tours_supplier']      	  = $this->tours_model->tour_supplier($tour_data['tours_country']);
		$page_data['tours_concerned_person']      	  = $this->tours_model->ajax_concerned_persons($tour_data['supplier_name']);
		$page_data['valid_frm_to_date']      	  = $this->tours_model->tour_valid_dates($tour_id);
		
		$tour_city=explode(',',$tour_data['tours_city']);
		$page_data['optional_tours']=array();
		foreach($tour_city as $t_v){
			$page_data['tours_hotels'][$t_v]   = $this->tours_model->tour_hotel($t_v);
			$page_data['optional_tours'][$t_v]=$this->tours_model->optional_tours($t_v);
		}
		
		$tours_sel_hotels  = $this->tours_model->tours_sel_hotels($tour_id);
		$page_data['tours_sel_hotels'] =array();
		
		foreach($tours_sel_hotels as $t_v){
			$page_data['tours_sel_hotels'][$t_v['city_id']][]=$t_v;
		}
		
		$tours_sel_opt_tours     	  = $this->tours_model->tours_sel_opt_tours($tour_id);
		$page_data['tours_sel_opt_tours']=array();
		//debug($tours_sel_opt_tours );
		foreach($tours_sel_opt_tours as $t_v){
			$page_data['tours_sel_opt_tours'][]=$t_v['optional_tour'];
		}
		$page_data['tours_continent'] = $tours_continent;
		$page_data['tour_type'] = $this->tours_model->tour_type();
	   //$page_data['tour_subtheme'] = $this->tours_model->tour_subtheme();
	   // debug($page_data['tours_sel_opt_tours']); exit;
		$this->template->view('tours/edit_tour_package',$page_data);
	}
  public function edit_tour_package_save() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
	//debug( $data );die();
	
		
	
	
	
	
    $tour_id = $data['tour_id'];
        $query_x = "select * from tours where id='$tour_id'"; // echo $query; exit;
        $fetch_x = $this->tours_model->query_run($query_x)->result_array()[0];
        //debug($fetch_x);exit;
        $old_image = $fetch_x['gallery'];
        $package_name          = $data['package_name'];
        $package_description = $data['package_description'];
        $tour_expire_date = $data['tour_expire_date'];
        $supplier_name = implode(',',$data['supplier']);

        $image_description = $data['image_description'];
        $tours_continent       = $data['tours_continent'];
        // $duration = $data['duration'];
        // $tours_city_new     = $data['tours_city_new'];
      // $tours_city = $tours_city_new;
      // $tours_city     = implode(',',$tours_city);
        $duration       = $data['duration'];
        $tour_type          = $data['tour_type'];
        $tour_type          = implode(',',$tour_type);
        $tours_country      = $data['tours_country'];
        $tours_country      = implode(',',$tours_country);
        $tours_city      = $data['tours_city'];
        $tours_city      = implode(',',$tours_city);
        $tours_continent      = $data['tours_continent'];
		$tours_continent      = implode(',',$tours_continent);
		$valid_frm=implode(',',$data['valid_frm']);
		$valid_to=implode(',',$data['valid_to']);
		$trip_type=$data['trip_type'];
		$package_type=$data['package_type'];
		//$valid_frm=$data['valid_frm'];
		//$valid_to=$data['valid_to'];
		$concerned_person   = implode(',',$data['concerned_supplier']);
		$multi_date=$data['multi_date'];
		$inclusions = $data['inclusions_checks'];
		$inclusions =json_encode($inclusions,1);
		$multi_date_array=explode(',',$multi_date);
       // $theme          = $data['theme'];
       // $theme          = implode(',',$theme);
      /* $adult_twin_sharing    = $data['adult_twin_sharing'];
        $adult_tripple_sharing = $data['adult_tripple_sharing'];
        if($adult_tripple_sharing=='')
        {
         $adult_tripple_sharing = 0;
       }
       else
       {
         $adult_tripple_sharing  = $adult_tripple_sharing;
      }*/

      // $highlights            = $data['highlights'];
     //  $inclusions            = $data['inclusions'];
     //  $exclusions            = $data['exclusions'];
      // $terms                 = $data['terms'];
      // $canc_policy           = $data['canc_policy'];
      // $trip_notes           = $data['trip_notes'];
       if(isset($_REQUEST['gallery_previous'])){
        $ppg        = $_REQUEST['gallery_previous'];
         $total_ppg  = count($ppg) ;
         $ppg_list   = '';
         for($c=0;$c<$total_ppg;$c++)
         {
          if($ppg_list=='')
          {
            $ppg_list = $ppg[$c];
          }
          else
          {
            $ppg_list = $ppg_list.','.$ppg[$c];
          }       
        }
        if($total_ppg>0)
        {
          $ppg_list = $ppg_list.',';
        }
        else
        {
          $ppg_list = '';
      } 
       }
       
      $arr=array();
      if($_FILES['gallery']['name'][0]!="")
      {       
        $list  = $_FILES['gallery']['name'];
        $total_images = count($list); 
        for($i=0;$i<$total_images;$i++)
        {
			if($_FILES['gallery']['size'][$i]>614400)
			{
				$this->session->set_flashdata("msg", "<div class='alert alert-danger'>All gallery Images must be within 600KB.</div>");
				redirect(base_url('tours/edit_tour_package/'.$tour_id));
			}
             // for setting the unique name of image starts @@@@@@@@@@@@@@@@@@@
          $filename  = time().basename($list[$i]);
          $extension = pathinfo($filename, PATHINFO_EXTENSION);
          $uniqueno  = substr(uniqid(),0,5);
          $randno    = substr(rand(),0,5);
          $new       = $uniqueno.$randno.'.'.$extension;
          $folder    = $this->template->domain_image_upload_path();
          $folderpath= trim($folder.$new);
          $path      = addslashes($folderpath);
          move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $folderpath);  
          array_push($arr,$new);

        } 
      } 
     
      $banner_image = '';
      if(!empty($_FILES['banner_image']['name']))
      {
		  if($_FILES['banner_image']['size']>614400)
		  {
			  $this->session->set_flashdata("msg", "<div class='alert alert-danger'>Featured Image size must be within 600KB.</div>");
			  redirect(base_url('tours/edit_tour_package/'.$tour_id));
		  }
       $banner_image = $_FILES['banner_image']['name'];
       $banner_image = time().$banner_image;
       $filename     = basename($banner_image);
       $extension    = pathinfo($filename, PATHINFO_EXTENSION);
       $uniqueno     = substr(uniqid(),0,5);
       $randno       = substr(rand(),0,5);
       $new          = $uniqueno.$randno.'.'.$extension;
       $folder       = $this->template->domain_image_upload_path();
       $folderpath   = trim($folder.$new);
       $path         = addslashes($folderpath);
       move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
       $banner_image = $new; 
       $banner_image_update = 'banner_image="'.$banner_image.'" ,'; 
     }else
     {
       $banner_image_update = '';
      
     }
     
     $old_image =explode(',', $old_image);
     $inclusions_checks   = $data['inclusions_checks'];
     $inclusions_checks   = json_encode($inclusions_checks,1);
    
     

     $Gallery_list = $Gallery_list_arr = array_merge($arr,$old_image);
     $Gallery_list = implode(',', $Gallery_list);
     $Gallery_list_arr = array_filter($Gallery_list_arr);
     //debug($Gallery_list_arr);die();
     ///code 2017-11-16 
      $img_desc = strip_tags($data['image_description']);
      // debug($img_desc);exit;
      if($img_desc){
       $desc_array = explode("#", $img_desc);
       $ker_array = [];
       if($banner_image){
        array_push($ker_array, $banner_image);
       }else{
        array_push($ker_array, $fetch_x['banner_image']);
       }
       foreach($Gallery_list_arr as $value) {
          array_push($ker_array, $value);
       }
      // debug($desc_array);exit();
       // unset($desc_array[0]);
        $image_description_new = array_combine($ker_array,$desc_array);
     //  if(count($ker_array)==count($desc_array)){
     //    $image_description_new = array_combine($ker_array,$desc_array);
     // // echo "well";die();
     //  }else{
     //    //echo $fetch_x['image_description'];die();
     //  $image_description_new =   trim($fetch_x['image_description'],'"');
     //  }
      $image_description_new = $desc_array;
      // debug($image_description_new );die();
      $image_description_new = json_encode($image_description_new);
      //debug($image_description_new );die();
     }else{
      $image_description_new='';
     }
      $image_description_new = trim($image_description_new);
     // debug($image_description_new);die();
     //end code
      $child_without_bed = 0;
      $child_with_bed = 0;
      $single_suppliment = 0;
      $joining_directly = 0;
      $service_tax = 0;
      $tcs = 0;
     /* tours_continent='$tours_continent',
      tours_country='$tours_country',
      tours_city='$tours_city',*/
      $query  = "update tours set trip_type=$trip_type,
         package_name='$package_name',
		 package_type='$package_type',
		 valid_frm='$valid_frm',
		 valid_to='$valid_to',
		 multi_date='$multi_date',
         tour_type='$tour_type',
         tours_continent='$tours_continent',
         tours_country='$tours_country',
		 tours_city='$tours_city',
         duration='$duration',
         supplier_name='$supplier_name',
		 concerned_person='$concerned_person',
      ".$banner_image_update."
		 inclusions_checks='$inclusions'
      where id='$tour_id'";
      // echo $query;exit;
      $return = $this->tours_model->query_run($query);
	  //echo $this->db->last_query();exit;
      /*$tours_itinerary_data = array(
       'adult_twin_sharing'=>$adult_twin_sharing,
       'highlights'=>$highlights,
       'inclusions'=>$inclusions,
       'exclusions'=>$exclusions,
       'terms'=>$terms,
       'canc_policy'=>$canc_policy,
       'inclusions_checks'=>$inclusions_checks,
       );
      $this->custom_db->update_record('tours_itinerary',$tours_itinerary_data , array('tour_id'=>$tour_id));*/
	//  debug($return);exit;
	  if($return)
        {
			$this->custom_db->delete_record('tour_valid_from_to_date', array('tour_id'=>$tour_id));
			$this->custom_db->delete_record('tour_dep_dates', array('tour_id'=>$tour_id));
			//foreach ($data['valid_frm'] as $val_key => $valid_frm_id) {
			//	$this->custom_db->insert_record('tour_valid_from_to_date',array('tour_id' => $tour_id, 'valid_from' => $valid_frm_id,'valid_to' => $data['valid_to'][$val_key]));
			//}
			if($package_type=='group'){
				
				foreach ($multi_date_array as $val_key => $valid_frm_id) {
					$this->custom_db->insert_record('tour_dep_dates',array('tour_id' => $tour_id, 'dep_date' => date('Y-m-d',strtotime($valid_frm_id))));
				}
			}else{
				
				foreach ($data['valid_frm'] as $val_key => $valid_frm_id) {
					$this->custom_db->insert_record('tour_valid_from_to_date',array('tour_id' => $tour_id, 'valid_from' => $valid_frm_id,'valid_to' => $data['valid_to'][$val_key]));
				}
			}
			$this->custom_db->delete_record('tour_package_map', array('tour_id'=>$tour_id));
			foreach ($data['tour_type'] as $tours_type) {
				$this->custom_db->insert_record('tour_package_map',array('tour_id' => $tour_id, 'type_id' => $tours_type));
			}
			$this->custom_db->delete_record('tours_continent_wise', array('tour_id'=>$tour_id));
			foreach ($data['tours_continent'] as $continent_id) {
				$this->custom_db->insert_record('tours_continent_wise',array('tour_id' => $tour_id, 'continent_id' => $continent_id));
			}
			$this->custom_db->delete_record('tours_country_wise', array('tour_id'=>$tour_id));
			//echo $this->db->last_query();
			foreach ($data['tours_country'] as $country_id) {
				$this->custom_db->insert_record('tours_country_wise',array('tour_id' => $tour_id, 'country_id' => $country_id));
				//echo $this->db->last_query();
				
			}
			$this->custom_db->delete_record('tours_city_wise', array('tour_id'=>$tour_id));
			foreach ($data['tours_city'] as $city_id) {
				$this->custom_db->insert_record('tours_city_wise',array('tour_id' => $tour_id, 'city_id' => $city_id));
			}
			
			$this->custom_db->delete_record('tours_hotel_details', array('tour_id'=>$tour_id));
			foreach ($data['hotel_city'] as $hotel_key =>$hotel_id) {
				$this->custom_db->insert_record('tours_hotel_details',array('tour_id' => $tour_id, 'hotel_id' => @$data['hotel_name'][$hotel_key], 'city' => $data['hotel_city'][$hotel_key],'no_of_night' => $data['no_night'][$hotel_key],'star_rating' => $data['star_rating'][$hotel_key],'city_id' => $data['hotel_city_id'][$hotel_key]));
			}
			$this->custom_db->delete_record('tour_visited_cities', array('tour_id'=>$tour_id));
			foreach ($data['hotel_city'] as $hotel_key =>$hotel_id) {
				$this->custom_db->insert_record('tour_visited_cities',array('tour_id' => $tour_id, 'city' => json_encode($data['hotel_city_id'][$hotel_key],1),'no_of_nights' => $data['no_night'][$hotel_key]));
			}
			
			
			
			
			$this->custom_db->delete_record('tour_optional_tour_details', array('tour_id'=>$tour_id));
			foreach ($data['optional_tour'] as $optional_key =>$optional_id) {
				$this->custom_db->insert_record('tour_optional_tour_details',array('tour_id' => $tour_id, 'optional_tour' => $optional_id));
			}
     
      }
	  //exit;
      if($return)
      {
       
		if($fetch_x['package_status']=='CREATED'){
			// header('Location: '.base_url().'tours/edit_tour_package/'.$tour_id);
			redirect(base_url('tours/draft_list/'));
		}else if($fetch_x['package_status']=='ITINERARY_ADDED'){
			redirect(base_url('tours/tour_list/'));
		}else if($fetch_x['package_status']=='VERIFICATION'){
			redirect(base_url('tours/verify_tour_list/'));
		}else if($fetch_x['package_status']=='VERIFIED'){
			redirect(base_url('tours/published_tour_list/'));
		}
										
     } 
     else { echo $return; } 
   }
   public function tour_pricing($tour_id) { 
     $tour_data = $this->tours_model->tour_data($tour_id);

     $page_data['tour_data'] = $tour_data;
     $page_data['tour_id']   = $tour_id;
     $this->template->view('tours/tour_pricing',$page_data);
   }
   public function tour_pricing_p2($tour_id) {
     $tour_data = $this->tours_model->tour_data($tour_id);
	 $country=$this->tours_model->tours_country_name();
     $get_tc = $this->tours_model->get_holiday_tc();
     $page_data['terms_n_Conditions'] = $get_tc;
    // debug($get_tc); exit;
    //debug($tour_data); exit;
     $page_data['tour_data'] = $tour_data;
	 $page_data['tour_country']=$country;
     $page_data['tour_id']   = $tour_id;
	// debug($page_data['tour_country']);exit;
     $this->template->view('tours/tour_pricing_p2',$page_data);
   }
   public function tour_pricing_save() {



    $data = $this->input->post();
    //debug($data); exit;
    $tour_id               = sql_injection($data['tour_id']);
    $adult_twin_sharing    = sql_injection($data['adult_twin_sharing']);
    $adult_tripple_sharing = sql_injection($data['adult_tripple_sharing']);
    $child_with_bed        = sql_injection($data['child_with_bed']);
    $child_without_bed     = sql_injection($data['child_without_bed']);
    $joining_directly      = sql_injection($data['joining_directly']);

    $query  = "update tours set adult_twin_sharing='$adult_twin_sharing',
    adult_tripple_sharing='$adult_tripple_sharing',
    child_with_bed='$child_with_bed',
    child_without_bed='$child_without_bed',
    joining_directly='$joining_directly'
    where id='$tour_id'";
    // echo $query; exit;
    $return = $this->tours_model->query_run($query);
    if($return)
    {
     redirect('tours/tour_pricing/'.$tour_id);
   } 
   else { echo $return;}  
 }
 public function tour_pricing_p2_save() {
	$data = $this->input->post();
	//debug($data);exit;
	$tour_id = ($data['tour_id']);
	//$adult_twin_sharing = ($data['adult_twin_sharing']);
	$highlights = ($data['highlights']);
	$inclusions = ($data['inclusions']);
	$exclusions = ($data['exclusions']);
	$terms = ($data['terms']);
	$canc_policy = ($data['canc_policy']);
	$trip_notes = ($data['trip_notes']);  
	$visa_procedures = ($data['visa_procedures']); 
	$b2b_payment_policy = ($data['b2b_payment_policy']);  
	$b2c_payment_policy = ($data['b2c_payment_policy']); 
	$status_update_date = date('Y-m-d H:i:s'); 
	$query  = "update tours set 
	highlights='$highlights',
	inclusions='$inclusions',
	exclusions='$exclusions',
	terms='$terms',
	status_update_date = '$status_update_date',
	canc_policy='$canc_policy',
	trip_notes='$trip_notes',
	visa_procedures='$visa_procedures',
	b2b_payment_policy='$b2b_payment_policy',
	b2c_payment_policy='$b2c_payment_policy',
	status=1
	where id='$tour_id'";
	// echo $query;exit;
	$return = $this->tours_model->query_run($query);
/*$tours_itinerary_data = array(
  'adult_twin_sharing'=>$adult_twin_sharing,
  'highlights'=>$highlights,
  'inclusions'=>$inclusions,
  'exclusions'=>$exclusions,
  'terms'=>$terms,
  'canc_policy'=>$canc_policy,
  );
$this->custom_db->update_record('tours_itinerary',$tours_itinerary_data , array('tour_id'=>$tour_id));*/
if($return)
{
 $ite_data=$this->custom_db->single_table_records('tours','package_status',array('id'=>$tour_id))['data'][0];
	if($ite_data['package_status']=='VERIFIED'){
		redirect('tours/published_tour_list/');
	}else if($ite_data['package_status']=='VERIFICATION'){
		redirect('tours/verify_tour_list/');
	}else if($ite_data['package_status']=='ITINERARY_ADDED'){
		redirect('tours/tour_list/');
	}else if($ite_data['package_status']=='CREATED'){
		redirect('tours/draft_list/');
	}else if($ite_data['package_status']=='PUBLISHED'){
		redirect('tours/published_tour_list/');
	}
} 
else { echo $return;} 
}
public function activation_top_tour_destination($id,$status) {
  $query = "update tour_destinations set cms_status='$status' where id='$id'";
  $return = $this->tours_model->query_run($query);
  if($return){redirect('cms/top_tour_destinations');} 
  else { echo $return;} 
}
public function edit_top_tour_destination($id) { 
  $tour_destinations_details = $this->tours_model->tour_destinations_details($id);
    //debug($tour_destinations_details); exit;
  $page_data['tour_destinations_details'] = $tour_destinations_details; 
  $page_data['id'] = $id;
  $tour_destinations = $this->tours_model->tour_destinations();
  $page_data['tour_destinations'] = $tour_destinations;
    //$page_data['tour_id'] = $tour_id;
    //$page_data['tour_data'] = $this->tours_model->tour_data($tour_id);  
  $this->template->view('tours/edit_top_tour_destination',$page_data);
}
public function itinerary($tour_id) { 
 $page_data['tour_id']   = $tour_id;
 $tour_data = $this->tours_model->tour_data($tour_id);
 $page_data['tour_data'] = $tour_data;
    //debug($page_data); exit()
 $tour_dep_dates_list = $this->tours_model->tour_dep_dates_list($tour_id);
 $page_data['tour_dep_dates_list'] = $tour_dep_dates_list;
 $this->template->view('tours/itinerary',$page_data);
}
public function itinerary_dep_date($tour_id,$dep_date) { 
  $page_data['tour_id']   = $tour_id;
  $tour_data = $this->tours_model->tour_data($tour_id);
  $page_data['tour_data'] = $tour_data;
  $tour_dep_dates_list = $this->tours_model->tour_dep_dates_list($tour_id);
  $page_data['tour_dep_dates_list'] = $tour_dep_dates_list;

  $page_data['dep_date']  = $dep_date;
  $tour_data = $this->tours_model->tour_data($tour_id);
  $tour_visited_cities_list = $this->tours_model->tour_visited_cities_list($tour_id);
  $page_data['tour_visited_cities_list'] = $tour_visited_cities_list;
  $tour_visited_cities_all = $this->tours_model->tour_visited_cities_all();
  $page_data['tour_visited_cities_all'] = $tour_visited_cities_all;
  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();

  $page_data['tours_city_name'] = $this->tours_model->tours_city_name();

  $tours_itinerary = $this->tours_model->tours_itinerary($tour_id,$dep_date);
  if(empty($tours_itinerary))
  {
   $page_data['itinerary_page'] = 'ajax_itinerary';
 }
 else if(!empty($tours_itinerary))
 {
   $page_data['tours_itinerary'] = $tours_itinerary;
   $page_data['tours_itinerary_dw'] = $this->tours_model->tours_itinerary_dw($dep_date,$tour_id);
   $page_data['itinerary_page']  = 'ajax_itinerary_stored';
 }  
  // debug($page_data); exit;
 $this->template->view('tours/itinerary',$page_data);
}
public function ajax_itinerary($dep_date,$tour_id) { 
 $page_data['tour_id']   = $tour_id;
 $page_data['dep_date']  = $dep_date;
 $tour_data = $this->tours_model->tour_data($tour_id);
 $page_data['tour_data'] = $tour_data;
 $tour_visited_cities_list = $this->tours_model->tour_visited_cities_list($tour_id);
 $page_data['tour_visited_cities_list'] = $tour_visited_cities_list;
 $tour_visited_cities_all = $this->tours_model->tour_visited_cities_all();
 $page_data['tour_visited_cities_all'] = $tour_visited_cities_all;
 $page_data['tours_city_name'] = $this->tours_model->tours_city_name();

 $tours_itinerary = $this->tours_model->tours_itinerary($tour_id,$dep_date);
 if(empty($tours_itinerary))
 {
   echo $this->template->isolated_view('tours/ajax_itinerary',$page_data);
 }
 else if(!empty($tours_itinerary))
 {
   $page_data['tours_itinerary']    = $tours_itinerary;
   $page_data['tours_itinerary_dw'] = $this->tours_model->tours_itinerary_dw($dep_date,$tour_id);
   echo $this->template->isolated_view('tours/ajax_itinerary_stored',$page_data);
 }    
}
public function itinerary_save() {
  $data = $this->input->post();
    //debug($data); //exit;

  $tour_id               = sql_injection($data['tour_id']);
  $dep_date              = sql_injection($data['dep_date']);
  $publish_status        = sql_injection($data['publish_status']);

  $reporting             = sql_injection($data['reporting']);
  $reporting_date        = $data['reporting_date'];
  $reporting_desc        = sql_injection($data['reporting_desc']);

  $tour_visited_city_id  = $data['tour_visited_city_id'];
  $no_of_nights          = $data['no_of_nights'];
  $visited_city          = $data['visited_city'];

  $program_title         = $data['program_title'];
  $program_des           = $data['program_des'];
  $hotel_name            = $data['hotel_name'];
  $rating                = $data['rating'];
  $accomodation          = $data['accomodation'];
  $tours_itinerary_dw_id = $data['tours_itinerary_dw_id'];

  $adult_twin_sharing    = sql_injection($data['adult_twin_sharing']);
  $adult_tripple_sharing = sql_injection($data['adult_tripple_sharing']);
    /*$pricing['child_with_bed']        = sql_injection($data['child_with_bed']);
    $pricing['child_without_bed']     = sql_injection($data['child_without_bed']);
    $pricing['joining_directly']      = sql_injection($data['joining_directly']);
    $pricing['single_suppliment']     = sql_injection($data['single_suppliment']);

    $service_tax           = sql_injection($data['service_tax']);
    $tcs                   = sql_injection($data['tcs']);*/

    $highlights            = sql_injection($data['highlights']);
    $inclusions            = sql_injection($data['inclusions']);
    $exclusions            = sql_injection($data['exclusions']);
    $terms                 = sql_injection($data['terms']);
    $canc_policy           = sql_injection($data['canc_policy']);
    $inclusions_checks     = $data['inclusions_checks'];
    $inclusions_checks     = json_encode($inclusions_checks,1);

    $day = 0;
    foreach($no_of_nights as $index => $record)
    {
     for($i=0;$i<$record;$i++)
     {
      $itinerary[$day]['tour_visited_city_id'] = sql_injection($tour_visited_city_id[$index]);
             //$itinerary[$index]['no_of_nights']     = $no_of_nights[$index];
      $itinerary[$day]['visited_city']         = html_entity_decode(sql_injection($visited_city[$index]));
      $day++;
    }
  }
  $itinerary[$day]['visited_city']                = html_entity_decode(sql_injection($visited_city[$index]));
        //debug($itinerary); exit;

  foreach($program_title as $index => $record)
  {
   $itinerary[$index]['program_title'] = sql_injection($program_title[$index]);
   $itinerary[$index]['program_des']   = sql_injection($program_des[$index]);
   $itinerary[$index]['hotel_name']    = sql_injection($hotel_name[$index]);
   $itinerary[$index]['rating']        = sql_injection($rating[$index]);
   $itinerary[$index]['accomodation']  = $accomodation[$index];
   $itinerary[$index]['tours_itinerary_dw_id']  = $tours_itinerary_dw_id[$index]; 
 }
        //debug($itinerary); exit;
        //$json_encode =  json_encode($itinerary,1); debug($json_encode);exit;
        //$json_decode =  json_decode($json_encode,1); debug($json_decode);exit;

    $tour_visited_city_id = json_encode($tour_visited_city_id,1); //debug($sightseeing);
    $no_of_nights         = json_encode($no_of_nights,1);
    //$itinerary            = json_encode($itinerary,1);  //debug($itinerary); //exit;

    $tours_itinerary = $this->tours_model->tours_itinerary($tour_id,$dep_date);
    if(empty($tours_itinerary))
    {
      $AUTO_INCREMENT = $this->tours_model->AUTO_INCREMENT('tours_itinerary');
      $tour_code      = 'AIRHP'.date('m').date('y').$AUTO_INCREMENT;

      $query  = "insert into tours_itinerary set tour_id='$tour_id',
      tour_code='$tour_code',
      dep_date='$dep_date',
      publish_status='$publish_status',
      tour_visited_city_id='$tour_visited_city_id',
      no_of_nights='$no_of_nights',
      adult_twin_sharing='$adult_twin_sharing',
      adult_tripple_sharing='$adult_tripple_sharing',
      reporting='$reporting',
      reporting_date='$reporting_date',
      reporting_desc='$reporting_desc',
      service_tax='$service_tax',
      tcs='$tcs',
      highlights='$highlights',
      inclusions='$inclusions',
      exclusions='$exclusions',
      terms='$terms',
      canc_policy='$canc_policy',
      inclusions_checks='$inclusions_checks',
      date=now()";

      foreach($itinerary as $index => $record)
      {
        $visited_city  = $record['visited_city'];
        $program_title = $record['program_title'];
        $program_des   = $record['program_des'];
        $hotel_name    = $record['hotel_name'];
        $rating        = $record['rating'];
        $accomodation  = json_encode($record['accomodation'],1);

        $query_tours_itinerary_dw  = "insert into tours_itinerary_dw set tour_id='$tour_id',
        tour_code='$tour_code',
        dep_date='$dep_date',
        visited_city='$visited_city',
        program_title='$program_title',
        program_des='$program_des',
        hotel_name='$hotel_name',
        rating='$rating',
        accomodation='$accomodation'";
         //echo '<pre>'.$query_tours_itinerary_dw;
        $this->tours_model->query_run($query_tours_itinerary_dw); 
      }
    }
    else if(!empty($tours_itinerary))
    {
     $id     = $data['id'];
     $query  = "update tours_itinerary set tour_id='$tour_id',
     dep_date='$dep_date',
     publish_status='$publish_status',
     tour_visited_city_id='$tour_visited_city_id',
     no_of_nights='$no_of_nights',
     adult_twin_sharing='$adult_twin_sharing',
     adult_tripple_sharing='$adult_tripple_sharing',
     reporting='$reporting',
     reporting_date='$reporting_date',
     reporting_desc='$reporting_desc',
     service_tax='$service_tax',
     tcs='$tcs',
     highlights='$highlights',
     inclusions='$inclusions',
     exclusions='$exclusions',
     terms='$terms',
     canc_policy='$canc_policy',
     inclusions_checks='$inclusions_checks'
     where id='$id'";

     foreach($itinerary as $index => $record)
     {
      $tours_itinerary_dw_id = $record['tours_itinerary_dw_id'];
      $visited_city  = $record['visited_city'];
      $program_title =  $record['program_title'];
      $program_des   = $record['program_des'];
      $hotel_name    = $record['hotel_name'];
      $rating        = $record['rating'];
      $accomodation  = json_encode($record['accomodation'],1);

      $query_tours_itinerary_dw  = "update tours_itinerary_dw set visited_city='$visited_city',
      program_title='$program_title',
      program_des='$program_des',
      hotel_name='$hotel_name',
      rating='$rating',
      accomodation='$accomodation'
      where id='$tours_itinerary_dw_id'";
         //echo '<pre>'.$query_tours_itinerary_dw;
      $this->tours_model->query_run($query_tours_itinerary_dw); 
    }
  }     
        //echo $query; exit;
  $return = $this->tours_model->query_run($query);
  if($return)
  {
       //redirect('tours/itinerary/'.$tour_id);
   redirect('tours/itinerary_dep_date/'.$tour_id.'/'.$dep_date);
 } 
 else { echo $return;}  
}
public function ajax_tour_publish() {
  $data = $this->input->post();
  // debug($data); exit('');
  $tour_id        = ($data['tour_id']);
  $dep_date       = ($data['dep_date']);
  $publish_status = ($data['publish_status']);
  $publish_for = ($data['publish_for']);
  $query_1  = "select * from tours_itinerary_dw where tour_id=  ".$tour_id." and banner_image != ''";      
  $num_ajax_tour_publish_1 = $this->tours_model->ajax_tour_publish_1($query_1);
  $query3 = "select * from tour_price_management where tour_id=".$tour_id; 
  $num_ajax_tour_publish_3 = $this->tours_model->ajax_tour_publish_1($query3);
  $message = array();
  if($num_ajax_tour_publish_1 == 0)
  { 
   $message['first'][]= "Sorry! Please upload images";
 }
 if($num_ajax_tour_publish_3 == 0)
 { 
   $message['first'][]= "Unable to publish the package as the price info is missing. Please add the price information for the package using Price Management Option";
 }
 if($num_ajax_tour_publish_1 !=0 && $num_ajax_tour_publish_3!=0 && $publish_status == 1)
 {
   $query  = "update tours set publish_for='$publish_for' where id='$tour_id'";
   //echo $query;exit;
   $return = $this->tours_model->query_run($query);
   if($return)
   {
    $message['sec'][]= "Thanks! This tour is successfully published now.";
  }else{
    $message['sec'][]= "Sorry|| some techinal .";
  }
}
if($publish_status != 1){
 $query  = "update tours set publish_for='publish_for' where id='$tour_id'";
 $return = $this->tours_model->query_run($query);
 if($return)
 {
  $message['sec'][]= "Thanks! This tour is successfully unpublished now.";
}else{
  $message['sec'][]= "Sorry|| some techinal .";
}
}
echo json_encode($message); exit(); 
}
public function ajax_tour_topdeals() {
  $data = $this->input->post();
  // debug($data); exit('');
  $tour_id        = sql_injection($data['tour_id']);
  $deals_status   = sql_injection($data['deals_status']);
  $query  = "update tours_itinerary set deals_status='$deals_status' where tour_id='$tour_id'";
  $return = $this->tours_model->query_run($query);
  if($return){
    $message['sec'][]= "Thanks! This deal is successfully updated.";
  }else{
    $message['sec'][]= "Sorry|| some techinal .";
  }
  echo json_encode($message); exit(); 
}

public function tour_itinerary_p2($tour_id) { 
  error_reporting(0);
 // echo phpinfo();
 $page_data['tour_id']   = $tour_id;
 $page_data['tour_data'] = $this->tours_model->tour_data($tour_id);
 $page_data['tour_visited_cities'] = $this->tours_model->tour_visited_cities($tour_id);
 $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
 $page_data['tours_itinerary_dw'] = $this->custom_db->single_table_records('tours_itinerary_dw','*',array('tour_id'=>$tour_id));
// debug($page_data);exit;
 $page_data['tours_itinerary_dw'] = ($page_data['tours_itinerary_dw']['status'])? $page_data['tours_itinerary_dw']['data']: NULL;

   // debug(json_decode($page_data['tour_visited_cities'][0]['itinerary'],1));
   //debug($page_data);exit;
 $this->template->view('tours/tour_itinerary_p2',$page_data);
}
public function tour_itinerary_p2_save() {
	//echo phpinfo();
	$list  = $_FILES['gallery']['name'];
	$data = $this->input->post();
	//$list  = $_FILES['gallery']['name'];
	//$data = $this->input->post();
	//debug($_FILES);exit;
	//foreach($_FILES['gallery']['name'][$itinerary_count] as $img_key => $img_val)
	//{	
	  
	//}
	$tour_id = $data['tour_id'];
	$tours=$this->custom_db->get_result_by_query('SELECT * FROM tours WHERE  id='.$tour_id);
	
	
	$tours=json_decode(json_encode($tours),true);
	//debug($tours);exit;
	if($tours[0]['package_status']=='CREATED'){
		$pack_status ='ITINERARY_ADDED';
	}else{
		$pack_status = $tours[0]['package_status'];
	}
	$id = $data['id'];
	$tour_visited_city_id_arr=array();
	$no_of_nights_arr = array();
	$this->custom_db->delete_record('tours_itinerary_dw',array('tour_id'=>$tour_id));
	$itinerary_count=1;
	$this->custom_db->update_record('tours',array('package_status'=>$pack_status,'package_description'=>$data['program_des']),array('id'=>$tour_id));
	foreach($id as $index => $record)
	{
	
		//$program_des = $data['program_des'][$record];
		$itinerary_des = $data['itinerary_des'][$record];
		$accomodation = $data['accomodation'][$record];
		$visited_city_name = $data['visited_city_name'][$record];
		$images = $data['images'][$record];
		$tour_visited_cities=$this->custom_db->get_result_by_query('SELECT * FROM tour_visited_cities WHERE  id='.$record);
		$tour_visited_cities=json_decode(json_encode($tour_visited_cities),true);   
		$itinerary = array();
     
		foreach ($itinerary_des as $key => $program_des_val) 
		{
			
			if(!empty($_FILES['gallery']['name'][$itinerary_count])){
				foreach($_FILES['gallery']['name'][$itinerary_count] as $img_key => $img_val)
				{	
					if(!empty($list[$itinerary_count][$img_key])){
					
						$filename  = basename($list[$itinerary_count][$img_key]);
						$extension = pathinfo($filename, PATHINFO_EXTENSION);
						$uniqueno  = substr(uniqid(),0,5);
						$randno = substr(rand(),0,5);
						if($_FILES['gallery']['name'][$itinerary_count])
						{
							$new = $uniqueno.$randno.'.'.$extension;
						}
						else
						{
							$new = $extension;   
						}

						$folder = $this->template->domain_image_upload_path();
						$folderpath = trim($folder.$new);
						$path = addslashes($folderpath);
						if($_FILES['gallery']['size'][$itinerary_count][$img_key]>614400)
						{
							$this->session->set_flashdata("msg", "<div class='alert alert-danger'>All gallery Images must be within 600KB.</div>");
							//redirect(base_url('tours/tour_pricing_p2/'.$data['tour_id']));
						}
						move_uploaded_file($_FILES['gallery']['tmp_name'][$itinerary_count][$img_key], $folderpath);            
						if($img_key==0)
						{ 
							$Gallery_list = $new;
						}
						else
						{
							$Gallery_list = $Gallery_list.",".$new;  
						} 
					}else{
						$Gallery_list='';  
					}
					//$itinerary[$key]['program_des'] = $program_des_val;
					$itinerary[$key]['itinerary_des']   = $program_des_val;
					$itinerary[$key]['accomodation']  = $accomodation[$key];
					$itinerary[$key]['visited_city_name']  = $visited_city_name[$key];
					//$itinerary[$key]['images']  = $Gallery_list.''.$images[$key];
					$Gallery_list = $Gallery_list.",".$data['old_images'][$itinerary_count][0];  
				}
			}else{
				$Gallery_list=$data['old_images'][$itinerary_count][0];
			}
			//$Gallery_list=$Gallery_list;
			$tours_itinerary_dw_data  =array( 
				'tour_id'=>$tour_id,
				'tour_code'=>$tours[0]['tour_code'],
				'visited_city'=>$tour_visited_cities[0]['city'],
				'visited_city_name'=>$itinerary[$key]['visited_city_name'],
			//	'program_des'=>$itinerary[$key]['program_des'],
				'itinerary_des'=>$itinerary[$key]['itinerary_des'],
				'banner_image'=>$Gallery_list,
				'accomodation'=>json_encode($itinerary[$key]['accomodation'],1));
				// debug($tours_itinerary_dw_data);exit;
				$this->custom_db->insert_record('tours_itinerary_dw',$tours_itinerary_dw_data); 
				$itinerary_count++;
				################################################
		}
		$itinerary = json_encode($itinerary,1);
		
		$this->custom_db->update_record('tour_visited_cities',array('itinerary'=>$itinerary),array('id'=>$record));
		$tour_visited_city_id_arr[]=$record;
		$no_of_nights_arr[]  = $tour_visited_cities[0]['no_of_nights'];
	}
	
	redirect(base_url('tours/tour_pricing_p2/'.$tour_id));

}
public function tour_itinerary_p2_save_old() {
  $data = $this->input->post();
 
  $tour_id = $data['tour_id'];
  $tours=$this->custom_db->get_result_by_query('SELECT * FROM tours WHERE  id='.$tour_id);
  $tours=json_decode(json_encode($tours),true);
  $id = $data['id'];
  $tour_visited_city_id_arr=array();
  $no_of_nights_arr = array();
  $this->custom_db->delete_record('tours_itinerary_dw',array('tour_id'=>$tour_id));
  foreach($id as $index => $record)
  {
    $program_title = $data['program_title'][$record];
    $program_des = $data['program_des'][$record];
    $hotel_name = $data['hotel_name'][$record];
    $rating = $data['rating'][$record];
    $accomodation = $data['accomodation'][$record];
    $tour_visited_cities=$this->custom_db->get_result_by_query('SELECT * FROM tour_visited_cities WHERE  id='.$record);
    $tour_visited_cities=json_decode(json_encode($tour_visited_cities),true);   
    $itinerary = array();
     // for($i=0;$i<count($program_title);$i++)
    /*Bishnu*/
    // debug($program_title);exit;
    foreach ($program_title as $key => $program_title_val) 
    {
      $itinerary[$key]['program_title'] = $program_title_val;
      $itinerary[$key]['program_des']   = $program_des[$key];
      $itinerary[$key]['hotel_name']    = $hotel_name[$key];
      $itinerary[$key]['rating']        = $rating[$key];
      $itinerary[$key]['accomodation']  = $accomodation[$key];
      
      ##################################
      /*$visited_city  = $tour_visited_cities[0]['city'];
      $program_title = $itinerary[$key]['program_title'];
      $program_des   = $itinerary[$key]['program_des'];
      $hotel_name    = $itinerary[$key]['hotel_name'];
      $rating        = $itinerary[$key]['rating'];
      $accomodation  = json_encode($itinerary[$key]['accomodation'],1);*/
      $tours_itinerary_dw_data  =array( 
       'tour_id'=>$tour_id,
       'tour_code'=>$tours[0]['package_id'],
       'visited_city'=>$tour_visited_cities[0]['city'],
       'program_title'=>$itinerary[$key]['program_title'],
       'program_des'=>$itinerary[$key]['program_des'],
       'hotel_name'=>$itinerary[$key]['hotel_name'],
       'rating'=>$itinerary[$key]['rating'],
       'accomodation'=>json_encode($itinerary[$key]['accomodation'],1));
      // debug($tours_itinerary_dw_data);exit;
      $this->custom_db->insert_record('tours_itinerary_dw',$tours_itinerary_dw_data); 
      ################################################
    }
    $itinerary = json_encode($itinerary,1);
    $this->custom_db->update_record('tour_visited_cities',array('itinerary'=>$itinerary),array('id'=>$record));
    $tour_visited_city_id_arr[]=$record;
    $no_of_nights_arr[]  = $tour_visited_cities[0]['no_of_nights'];
  }
  $inclusions = $data['inclusions'];
  $inclusions = json_encode($inclusions,1);
  $query  = "update tours set inclusions_checks='$inclusions' where id='$tour_id'";
  $return = $this->tours_model->query_run($query);    
  ############################################################
  $tour_visited_city_id = json_encode($tour_visited_city_id_arr,1);
  $no_of_nights = json_encode($no_of_nights_arr,1);
  $AUTO_INCREMENT = $this->tours_model->AUTO_INCREMENT('tours_itinerary');
  $tours_itinerary_data = array(
   'tour_id'=>$tour_id,
   'tour_code'=>$tours[0]['package_id'],
   'tour_visited_city_id'=>$tour_visited_city_id,
   'no_of_nights'=>$no_of_nights,
   'adult_twin_sharing'=>$tours[0]['adult_twin_sharing'],
   'adult_tripple_sharing'=>$tours[0]['adult_tripple_sharing'],
   'highlights'=>$tours[0]['highlights'],
   'inclusions'=>$tours[0]['inclusions'],
   'exclusions'=>$tours[0]['exclusions'],
   'terms'=>$tours[0]['terms'],
   'canc_policy'=>$tours[0]['canc_policy'],
   'inclusions_checks'=>$inclusions,
   'date'=>$tours[0]['date'],
   );
  $check_tours_itinerary = $this->custom_db->single_table_records('tours_itinerary','count(*) total',array('tour_id'=>$tour_id));
  if($check_tours_itinerary['data'][0]['total']!=0){
    $this->custom_db->update_record('tours_itinerary',$tours_itinerary_data,array('tour_id'=>$tour_id));
  }else{
    $this->custom_db->insert_record('tours_itinerary',$tours_itinerary_data);
  }

  //echo "frsdfgdhgf";exit;
      ##############################################################
  if($this->session->userdata('edit_itinary')){
    $this->session->unset_userdata('edit_itinary');
    $this->session->set_flashdata("msg", "<div class='alert alert-success'>Itinerary Updated Successfully.</div>");
    redirect(base_url('tours/tour_list/'));
  }else{
    redirect(base_url('tours/tour_pricing_p2/'.$tour_id));
  }
}

public function tour_destinations_banner() { 
 $tour_destinations = $this->tours_model->tour_destinations();
    //debug($tour_destinations); exit;
 $page_data['tour_destinations'] = $tour_destinations;
 //debug($page_data);exit;
 $this->template->view('tours/tour_destinations_banner',$page_data);
}

public function tour_destinations_banner_save() {
  $data = $this->input->post();
    //debug($data); //exit;
  $id           = sql_injection($data['id']);
  $banner_image = sql_injection($data['radio'.$id]);

    $query  = "update tour_destinations set banner_image='$banner_image' where id='$id'"; //echo $query; exit;       
    $return = $this->tours_model->query_run($query);
    if($return)
    {
     redirect('tours/tour_destinations_banner');
   } 
   else { echo $return;}  
 }
 public function tour_date_list() { 

   $tour_date_list = $this->tours_model->tour_date_list();
   $page_data['tour_date_list'] = $tour_date_list;  
   $tour_list = $this->tours_model->tour_list();
   $page_data['tour_list'] = $tour_list;
   $tour_destinations = $this->tours_model->tour_destinations();
   $page_data['tour_destinations'] = $tour_destinations;
    //debug($page_data); exit;
   $this->template->view('tours/tour_date_list',$page_data);
 }
 public function publish_tours_itinerary($id,$status) {
  $query = "update tours_itinerary set publish_status='$status' where id='$id'";
  $return = $this->tours_model->activation_tour_package($query);
  if($return){redirect('tours/tour_date_list');} 
  else { echo $return;} 
}
public function delete_tours_itinerary($id) {
  $query = "delete from tours_itinerary where id='$id'";
  $return = $this->tours_model->query_run($query);
  if($return){redirect('tours/tour_date_list');} 
  else { echo $return;} 
}
public function seats_tours_itinerary($id,$tour_id,$dep_date) { 
  $page_data['id'] = $id;  
  $tours_itinerary = $this->tours_model->tours_itinerary($tour_id,$dep_date);
  $page_data['tours_itinerary'] = $tours_itinerary;  
    //debug($page_data); exit;
  $this->template->view('tours/seats_tours_itinerary',$page_data);
}
public function seats_tours_itinerary_save() {
  $data = $this->input->post();
    //debug($data); exit;
  $id              = sql_injection($data['id']);
  $tour_id         = sql_injection($data['tour_id']);
  $dep_date        = sql_injection($data['dep_date']);
  $no_of_seats     = sql_injection($data['no_of_seats']);
  $total_booked    = sql_injection($data['total_booked']);
  $available_seats = sql_injection($data['available_seats']);
  $booking_hold    = sql_injection($data['booking_hold']);

  $query  = "update tours_itinerary set no_of_seats='$no_of_seats',
  total_booked='$total_booked',
  available_seats='$available_seats',
  booking_hold='$booking_hold' where id='$id'"; 
    //echo $query; exit;       
  $return = $this->tours_model->query_run($query);
  if($return)
  {
   redirect('tours/seats_tours_itinerary/'.$id.'/'.$tour_id.'/'.$dep_date);
 } 
 else { echo $return;}  
}
public function tours_enquiry($module) { 
	error_reporting(0);
	//if (!check_user_previlege('p250')) {
	// set_update_message("You Don't have permission to do this action.", WARNING_MESSAGE, array(
	//   'override_app_msg' => true
	//   ));
	// redirect(base_url());
	//}
	$get_data = $this->input->get();
	if($get_data)
	{
		$package_name = $get_data['package_name'];
		$get_package_id = $this->tours_model->get_package_id($package_name);
		$package_id = $get_package_id[0][0];
	}
	$page_data = array();
	$condition = array(
		'tour_id' => trim($this->input->get('phone')), 
		'phone' => trim($this->input->get('phone')),
		'email' => trim($this->input->get('email')),
		'module' => $module
    );
	$total_records = $this->tours_model->tours_enquiry($condition);
	$tours_enquiry = $this->tours_model->tours_enquiry($condition);
	
	
	$page_data['tours_enquiry'] = $tours_enquiry['tours_enquiry'];
	$page_data['tour_list']          = $this->tours_model->verified_tour_list();
	$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
	$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
	$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
	//debug($page_data);
	$this->template->view('tours/tours_enquiry',$page_data);
	// $array = array(
	//   'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()
	//   );    
	//$this->session->set_userdata( $array );
}
public function activation_enquiry($id,$status) {
  $query = "update tours_enquiry set status='$status' where id='$id'";
  $return = $this->tours_model->query_run($query);
  if($return){redirect('tours/tours_enquiry');} 
  else { echo $return;} 
}
public function delete_enquiry($id) {
  $query = "delete from tours_enquiry where id='$id'";
  $return = $this->tours_model->query_run($query);
  if($return){redirect('tours/tours_enquiry');} 
  else { echo $return;} 
}
public function assign_delete_enquiry($id) {
  $query = "delete from tours_enquiry where id='$id'";
  $return = $this->tours_model->query_run($query);
  if($return){redirect('tours/assigned_tours_enquiry');} 
  else { echo $return;} 
}
public function tour_type() {   
 $tour_type = $this->tours_model->tour_type();
    // debug($tour_type); exit;    
 $page_data['tour_type'] = $tour_type;
 $this->template->view('tours/tour_type',$page_data);
}
public function tour_type_save() {
	$data = $this->input->post();
    //debug($data); exit;
	$tour_type_name   = sql_injection($data['tour_type_name']);
	$banner_image = '';
	if(!empty($_FILES['banner_image']['name']))
	{
	   $banner_image = $_FILES['banner_image']['name'];
	   $banner_image = time().$banner_image;
	   $filename     = basename($banner_image);
	   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
	   $uniqueno     = substr(uniqid(),0,5);
	   $randno       = substr(rand(),0,5);
	   $new          = $uniqueno.$randno.'.'.$extension;
	   $folder       = $this->template->domain_image_upload_path();
	   $folderpath   = trim($folder.$new);
	   $path         = addslashes($folderpath);
	   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
	   $banner_image = $new; 
	   $banner_image_update = $banner_image; 
	}else{
	   $banner_image_update = '';
	}
  $query = "insert into tour_type set tour_type_name='$tour_type_name',banner_image='$banner_image_update', status=1 ";        
        //echo $query; //exit;
  $return = $this->tours_model->query_run($query);
  if($return)
    {   redirect('tours/tour_type/'); }
  else
    { echo $return; exit; }              
}
public function edit_tour_type($id) {
  $tour_type_details = $this->tours_model->tour_type_details($id);
    // debug($tour_type_details); //exit;      
  $page_data['tour_type_details'] = $tour_type_details[0];
    //debug($page_data); exit;
  $this->template->view('tours/edit_tour_type',$page_data);
}
public function edit_tour_type_save() {
  $data = $this->input->post();
  $id             = $data['id'];
  $tour_type_name = sql_injection($data['tour_type_name']);
	$banner_image = '';
    if(!empty($_FILES['banner_image']['name']))
    {
		if($_FILES['banner_image']['size']>614400)
		{
			$this->session->set_flashdata("msg", "<div class='alert alert-danger'>Featured Image size must be within 600KB.</div>");
			redirect(base_url('tours/edit_tour_package/'.$tour_id));
		}
		$banner_image = $_FILES['banner_image']['name'];
		$banner_image = time().$banner_image;
		$filename     = basename($banner_image);
		$extension    = pathinfo($filename, PATHINFO_EXTENSION);
		$uniqueno     = substr(uniqid(),0,5);
		$randno       = substr(rand(),0,5);
		$new          = $uniqueno.$randno.'.'.$extension;
		$folder       = $this->template->domain_image_upload_path();
		$folderpath   = trim($folder.$new);
		$path         = addslashes($folderpath);
		move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
		$banner_image = $new; 
		$banner_image_update = 'banner_image="'.$banner_image.'" '; 
	}else
    {
       $banner_image_update = '';
      
    }
  $query = "update tour_type set tour_type_name='$tour_type_name',".$banner_image_update." where id='$id'";        
      //  echo $query; exit;
  $return = $this->tours_model->query_run($query);
  if($return)
    {   redirect('tours/tour_type/'); }
  else
    { echo $return; exit; }              
}
public function tour_inclusions() {   
 $tour_inclusions = $this->tours_model->tour_inclusions();
 $page_data['tour_inclusions'] = $tour_inclusions;
 $this->template->view('tours/tour_inclusions',$page_data);
}
public function tour_inclusions_save() {
      //echo '<pre>'; print_r($_FILES); exit;   

 $banner_image = $_FILES['inclusion_image']['name'];
 $filename     = basename($banner_image);
 $extension    = pathinfo($filename, PATHINFO_EXTENSION);
 $uniqueno     = substr(uniqid(),0,5);
 $randno       = substr(rand(),0,5);
 $new          = $uniqueno.$randno.'.'.$extension;
 $folder       = $this->template->domain_image_upload_path();
 $folderpath   = trim($folder.$new);
 $path         = addslashes($folderpath);
 move_uploaded_file($_FILES['inclusion_image']['tmp_name'], $folderpath);              
 $inclusion_image = $new;         

 $data = $this->input->post();
    //debug($data);exit;
 $inclusion    = sql_injection($data['inclusion']);
 $query       = "insert into tour_inclusions set inclusion='$inclusion',
 status=1,
 inclusion_image='$inclusion_image'";
        //echo $query; exit;
 $return      = $this->tours_model->query_run($query);
 if(!$return)
 {
  echo $return; 
} 
redirect('tours/tour_inclusions');  
}
public function activation_tour_inclusion($id,$status) {
  $return = $this->tours_model->record_activation('tour_inclusions',$id,$status);
  if($return){redirect('tours/tour_inclusions');} 
  else { echo $return;} 
}
public function delete_tour_inclusion($id) {
  $return = $this->tours_model->record_delete('tour_inclusions',$id);
  if($return){redirect('tours/tour_inclusions');} 
  else { echo $return;} 
}
public function activation_tour_type($id,$status) {
  $return = $this->tours_model->record_activation('tour_type',$id,$status);

  if($return){redirect('tours/tour_type');} 
  else { echo $return;} 
}
public function delete_tour_type($id) {
  $return = $this->tours_model->record_delete('tour_type',$id);
  if($return){redirect('tours/tour_type');} 
  else { echo $return;} 
}
public function edit_tour_inclusion($id) {
  $tour_inclusions_details = $this->tours_model->table_record_details('tour_inclusions',$id);
    //debug($tour_inclusions_details); //exit;      
  $page_data['tour_inclusions_details'] = $tour_inclusions_details;
    //debug($page_data); exit;
  $this->template->view('tours/edit_tour_inclusion',$page_data);
}
public function edit_tour_inclusion_save() {
  $data = $this->input->post();
    //debug($data);exit;
  $id          = sql_injection($data['id']);
  $inclusion   = sql_injection($data['inclusion']);

  if(!empty($_FILES['inclusion_image']['name']))
  {
   $banner_image = $_FILES['inclusion_image']['name'];
   $filename     = basename($banner_image);
   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
   $uniqueno     = substr(uniqid(),0,5);
   $randno       = substr(rand(),0,5);
   $new          = $uniqueno.$randno.'.'.$extension;
   $folder       = $this->template->domain_image_upload_path();
   $folderpath   = trim($folder.$new);
   $path         = addslashes($folderpath);
   move_uploaded_file($_FILES['inclusion_image']['tmp_name'], $folderpath);              
   $inclusion_image = $new; 
   $inclusion_image_update = ",inclusion_image='$inclusion_image'"; 
 }

 $query = "update tour_inclusions set inclusion='$inclusion' ".$inclusion_image_update." where id='$id'";
        //echo $query; exit;
 $return = $this->tours_model->query_run($query);
 if($return)
 {
  redirect('tours/edit_tour_inclusion/'.$id); 
} 
else
{
 echo $return;
} 
}
  /*public function tour_country() {  
      $tour_country = $this->tours_model->table_records('tour_country','country_name','asc');
    $page_data['tour_country'] = $tour_country;
    $this->template->view('tours/tour_country',$page_data);
  }
  public function tour_country_save() {
    $data = $this->input->post();
    //debug($data); exit;
    $country_name   = sql_injection($data['country_name']);
        $query = "insert into tour_country set country_name='$country_name', status=1 ";        
        //echo $query; exit;
    $return = $this->tours_model->query_run($query);
    if($return)
    { redirect('tours/tour_country/'); }
        else
        { echo $return; exit; }              
      }*/
      public function tour_subtheme() {   
       $tour_subtheme = $this->tours_model->tour_subtheme();
    //debug($tour_subtheme); exit;    
       $page_data['tour_subtheme'] = $tour_subtheme;
       $this->template->view('tours/tour_subtheme',$page_data);
     }
     public function tour_subtheme_save() {
      $data = $this->input->post();
    //debug($data); exit;
      $tour_subtheme   = sql_injection($data['tour_subtheme']);
      $query = "insert into tour_subtheme set tour_subtheme='$tour_subtheme', status=1 ";        
        //echo $query; //exit;
      $return = $this->tours_model->query_run($query);
      if($return)
        {   redirect('tours/tour_subtheme/'); }
      else
        { echo $return; exit; }              
    }
    public function activation_tour_subtheme($id,$status) {
      $return = $this->tours_model->record_activation('tour_subtheme',$id,$status);
      if($return){redirect('tours/tour_subtheme');} 
      else { echo $return;} 
    }
    public function delete_tour_subtheme($id) {
      $return = $this->tours_model->record_delete('tour_subtheme',$id);
      if($return){redirect('tours/tour_subtheme');} 
      else { echo $return;} 
    }
    public function edit_tour_subtheme($id) {
      $tour_subtheme_details = $this->tours_model->table_record_details('tour_subtheme',$id);
    //debug($tour_subtheme_details); //exit;      
      $page_data['tour_subtheme_details'] = $tour_subtheme_details[0];
    // debug($page_data); exit;
      $this->template->view('tours/edit_tour_subtheme',$page_data);
    }
    public function edit_tour_subtheme_save() {
      $data = $this->input->post();
      $id             = $data['id'];
      $tour_subtheme  = sql_injection($data['tour_subtheme']);
      $query = "update tour_subtheme set tour_subtheme='$tour_subtheme' where id=$id";        
        //echo $query; //exit;
      $return = $this->tours_model->query_run($query);
      if($return)
        {   redirect('tours/edit_tour_subtheme/'.$id); }
      else
        { echo $return; exit; }              
    }


    public function tour_activity() {   
     $tour_activity = $this->tours_model->tour_activity();
    //debug($tour_activity); exit;    
     $page_data['tour_activity'] = $tour_activity;
     $this->template->view('tours/tour_activity',$page_data);
   }
   public function tour_activity_save() {
    $data = $this->input->post();
    //debug($data); exit;
    $tour_activity   = sql_injection($data['tour_activity']);
    $query = "insert into tour_activity set tour_activity='$tour_activity', status=1 ";        
        //echo $query; //exit;
    $return = $this->tours_model->query_run($query);
    if($return)
      {   redirect('tours/tour_activity/'); }
    else
      { echo $return; exit; }              
  }
  public function activation_tour_activity($id,$status) {
    $return = $this->tours_model->record_activation('tour_activity',$id,$status);
    if($return){redirect('tours/tour_activity');} 
    else { echo $return;} 
  }
  public function delete_tour_activity($id) {
    $return = $this->tours_model->record_delete('tour_activity',$id);
    if($return){redirect('tours/tour_activity');} 
    else { echo $return;} 
  }
  public function edit_tour_activity($id) {
    $tour_activity_details = $this->tours_model->table_record_details('tour_activity',$id);
    //debug($tour_activity_details); //exit;      
    $page_data['tour_activity_details'] = $tour_activity_details;
    //debug($page_data); exit;
    $this->template->view('tours/edit_tour_activity',$page_data);
  }
  public function edit_tour_activity_save() {
    $data = $this->input->post();
    //debug($data); exit;
    $id             = $data['id'];
    $tour_activity  = sql_injection($data['tour_activity']);
    $query = "update tour_activity set tour_activity='$tour_activity' where id='$id'";        
        //echo $query; //exit;
    $return = $this->tours_model->query_run($query);
    if($return)
      {   redirect('tours/edit_tour_activity/'.$id); }
    else
      { echo $return; exit; }              
  }
  public function ajax_tours_continent() {
    // error_reporting(E_ALL);
    $data = $this->input->post();

    $tours_continent = $data['tours_continent'];      
    $tours_continent = $this->tours_model->ajax_tours_continent($tours_continent);          
        //debug($tours_continent); exit; 
    $options = '';
    foreach($tours_continent as $key => $value)
    {
      $options .=  '<option value="'.$value['id'].'">'.$value['name'].'</option>';
    } 
    echo $options;      
  }
  public function ajax_tours_country() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
    //debug($data); exit;
    $tours_country = $data['tours_country'];      
    $tours_country = $this->tours_model->ajax_tours_country($tours_country);          
        //debug($tours_country); exit; 
    $options = '';
    foreach($tours_country as $key => $value)
    {
      $options .=  '<option value="'.$value['id'].'">'.$value['CityName'].'</option>';
    } 
    echo $options;           
  }
  public function ajax_tours_supplier() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
    //debug($data); exit;
    $tours_country = $data['tours_country'];      
    $tours_country = $this->tours_model->ajax_tours_supplier($tours_country);          
        //debug($tours_country); exit; 
	if(!empty($tours_country)){
	
		$options = '';
		foreach($tours_country as $key => $value)
		{
		  $options .=  '<option value="'.$value['id'].'">'.$value['supplier_name'].'</option>';
		} 
	}
    echo $options;      exit;     
  }
  
   public function ajax_concerned_persons() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
    //debug($data); exit;
    $tours_country = $data['supplier_id'];      
    $tours_country = $this->tours_model->ajax_concerned_persons($tours_country);          
        //debug($tours_country); exit; 
    if(!empty($tours_country)){
	
		$options = '';
		foreach($tours_country as $key => $value)
		{
		  $options .=  '<option value="'.$value['id'].'">'.$value['contact_person'].'</option>';
		} 
	}
    echo $options;      exit;     
  }
  public function ajax_tours_hotels() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
    //debug($data); exit;
    $tours_city = $data['tours_city'];      
    $tours_hotel = $this->tours_model->ajax_tours_hotels($tours_city);          
       // debug($tours_hotel); exit; 
    $options = '';
	//if(!empty($tours_hotel)){
		$no_night='';
		for($dno=0;$dno<=30;$dno++)
		{
			if($dno==1) { 
				$DayNight = ($dno+1).' Days | '.($dno).' Night';
			}else 
			{
				$DayNight = ($dno+1).' Days | '.($dno).' Nights';
			}
			$no_night.= '<option value="'.$dno.'">'.$DayNight.'</option>';
		}
    $options .=  '<div class="controls_new"><div class="controls" style="padding: 0px; margin: 13px;">
      <div class="mode add_city_hotel_here">
      <label class="control-label col-sm-12" style="text-align: left;margin-bottom: 6px;">'.$tours_hotel[0]['CityName'].' Hotels <a class="btn add_hotel">Add hotel <i class="fa fa-plus" aria-hidden="true"></i></a></label><div class="this_city_hotel"><div class="col-sm-6 controls"><input type="hidden" name="hotel_city[]" placeholder="City Name" value="'.$tours_hotel[0]['CityName'].'" class="form-control" ><input type="hidden" name="hotel_city_id[]"value="'.$tours_hotel[0]['city_id'].'" class="form-control" ></div><div class="col-sm-12 controls"><select class="select2 form-control" data-rule-required="true" name="hotel_name[]"  data-rule-required="true" ><option value="">Select Hotel</option>';
    foreach($tours_hotel as $key => $value)
    {
      $options.='<option value="'.$value['id'].'">'.$value['hotel_name'].'</option>';
    } 
    $options.='</select></div><div class="clearfix form-group"></div><div class="col-sm-6 controls"><input type="text" name="star_rating[]" placeholder="Star rating" value="'.$value['star_rating'].'" class="form-control"></div><div class="col-sm-6 controls"><select class="select2 form-control htl_no_of_nyt" data-rule-required="true" name="no_night[]"  data-rule-required="true" required> <option value="">No of Nights</option>'.$no_night.'</select>  </div><div class="clearfix form-group"></div></div></div></div>';
    
 // }
    echo $options;      exit;     
  }
  public function ajax_optional_tours() {
    // error_reporting(E_ALL);
    $data = $this->input->post();
    //debug($data); exit;
    $tours_city = $data['tours_city'];      
    $tours_hotel = $this->tours_model->ajax_optional_tours($tours_city);          
       
	$options = "";
	//if(!empty($tours_hotel)){ 
    $options = "<div class='controls_new city_ref_".$tours_city."'><div class='controls ' style='padding: 0px; margin: 13px;'><div class='mode'><label class='control-label col-sm-12' style='text-align: left;margin-bottom: 6px;'>".$tours_hotel[0]['CityName']." Optional Tours </label><div class='col-sm-12 col-md-12 controls'><select class='select2 form-control'  name='optional_tour[]' id='optional_tour' multiple >";
    foreach($tours_hotel as $key => $value)
    {
      $options .=  '<option value="'.$value['id'].'">'.$value['tour_name'].'</option>';
    } 
    $option.='</select></div></div></div></div><div class="clearfix"></div><br/>"';
 // }
    echo $options;      exit;     
  }
  public function reviews() {
    $page_data['reviews']            = $this->tours_model->reviews();
    $page_data['tour_list']          = $this->tours_model->tour_list();
    $page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
    $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
  //  debug($page_data); exit;
    $this->template->view('tours/reviews',$page_data);          
  }
  public function activation_review($id,$status) {
    $query = "update user_review set status='$status' where origin='$id'";
    $return = $this->tours_model->query_run($query);
    if($return){redirect('tours/reviews');} 
    else { echo $return;} 
  }
  public function delete_review($id) {
    $query = "delete from user_review where origin='$id'";
    $return = $this->tours_model->query_run($query);
    if($return){redirect('tours/reviews');} 
    else { echo $return;} 
  }
  public function hotel_reviews() {
    $page_data['hotel_reviews']            = $this->tours_model->hotel_reviews();
   $this->template->view('tours/hotel_reviews',$page_data);          
  }
  public function activation_hotel_review($id,$status) {
    $query = "update user_review set status='$status' where origin='$id'";
    $return = $this->tours_model->query_run($query);
    if($return){redirect('tours/hotel_reviews');} 
    else { echo $return;} 
  }
  public function delete_hotel_review($id) {
    $query = "delete from user_review where origin='$id'";
    $return = $this->tours_model->query_run($query);
    if($return){redirect('tours/hotel_reviews');} 
    else { echo $return;} 
  }
  public function perfect_holidays($user_type_idp) {    
   $tour_list = $this->tours_model->tour_list();
   $page_data['tour_list'] = $tour_list;

    $tour_destinations = $this->tours_model->get_tour_destinations(); //debug($tour_destinations);exit;
    $page_data['tour_destinations'] = $tour_destinations; 
    $tour_dep_dates_list_all = $this->tours_model->tour_dep_dates_list_all(); //debug($tour_dep_dates_list_all);exit;
    $page_data['tour_dep_dates_list_all'] = $tour_dep_dates_list_all; 
    $tour_dep_dates_list_published = $this->tours_model->tour_dep_dates_list_published(); //debug($tour_dep_dates_list_all);exit;
    $page_data['tour_dep_dates_list_published'] = $tour_dep_dates_list_published; 

    $page_data['tours_city_name'] = $this->tours_model->tours_city_name();
    $page_data['tours_country_name'] = $this->tours_model->tours_country_name();
        //debug($page_data); exit;
    $this->template->view('tours/perfect_holidays',$page_data);
  }
  public function publish_perfect_holidays($id,$status) {
    $query = "update tours set perfect_holidays='$status' where id='$id'";
    $return = $this->tours_model->query_run($query);
    if($return){redirect('tours/perfect_holidays');} 
    else { echo $return;} 
  }

  public function tour_region() {   
   $tour_region = $this->tours_model->tour_region();
  //  debug($tour_region); exit;    
   $page_data['tour_region'] = $tour_region;
   $this->template->view('tours/tour_region',$page_data);
 }

 public function tour_region_save() {
  $data = $this->input->post();
  $tour_region   = sql_injection($data['tour_region']);
  $check_availibility = $this->tours_model->check_region_exist($tour_region);
  if(!$check_availibility)
  {
    $query = "insert into tours_continent set name='$tour_region', status=1 ";        
        //echo $query; //exit;
    $return = $this->tours_model->query_run($query);
    if($return)
      {   redirect('tours/tour_region/'); }
    else
      { echo $return; exit; } 
  }
  else
  {
   $this->session->set_flashdata('region_msg','Region is already exist');
   redirect('tours/tour_region');
 }

}

public function delete_tour_region($id) {
  $return = $this->tours_model->record_delete('tours_continent',$id);

  if($return){redirect('tours/tour_region');} 
  else { echo $return;} 
}
public function tour_city() {  
  $tour_country = $this->tours_model->tour_country();
  $page_data['tour_country'] = $tour_country;
  if ($this->input->post()) {
   $post_data = $this->input->post();
   $post_data['CountryName'] = $this->custom_db->single_table_records('tours_country','name',array('id'=>$post_data['country_id']));
   $post_data['CountryName'] = $post_data['CountryName']['data'][0]['name'];
   $city_arr=explode(',',$post_data['CityName']);
   $city_arr=array_map('trim', $city_arr);
   
   $banner_image = '';
	if(!empty($_FILES['banner_image']['name']))
	{
	   $banner_image = $_FILES['banner_image']['name'];
	   $banner_image = time().$banner_image;
	   $filename     = basename($banner_image);
	   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
	   $uniqueno     = substr(uniqid(),0,5);
	   $randno       = substr(rand(),0,5);
	   $new          = $uniqueno.$randno.'.'.$extension;
	   $folder       = $this->template->domain_image_upload_path();
	   $folderpath   = trim($folder.$new);
	   $path         = addslashes($folderpath);
	   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
	   $banner_image = $new; 
	   $banner_image_update = $banner_image; 
	}else{
	   $banner_image_update = '';
	}
 
  $query = "insert into tours_country set name='$tour_country',banner_image='$banner_image_update', status=1 , continent = '$tours_continent'";
   
   
   
   
   
   
   foreach ($city_arr as $city) {
    $tours_city_data=array(
     'country_id'=>$post_data['country_id'],
     'CountryName'=>$post_data['CountryName'],
     'CityName'=>$city,
	 'banner_image'=>$banner_image_update
     );
    $this->custom_db->insert_record('tours_city',$tours_city_data);
  }
  set_insert_message();
  refresh ();
}
$tour_city = $this->custom_db->single_table_records('tours_city','*',array(),0, 100000000,array('CityName'=>'ASC'));
$page_data['tour_city'] = $tour_city['data'];

$this->template->view('tours/tour_city',$page_data);
}
public function edit_tour_city($id) {  
	if ($this->input->post()) {
		//ini_set('display_errors', 1);
		//	ini_set('display_startup_errors', 1);
		//	error_reporting(E_ALL);
		$post_data = $this->input->post();
		$CityName=$post_data['CityName'];
		$Country_id=$post_data['country_id'];
		//$this->custom_db->update_record('tours_city',$post_data,array('id'=>$id));
		
		$post_data['CountryName'] = $this->custom_db->single_table_records('tours_country','name',array('id'=>$Country_id));
		$CountryName = $post_data['CountryName']['data'][0]['name'];
		
		
		
		$banner_image = '';
		if(!empty($_FILES['banner_image']['name']))
		{
			if($_FILES['banner_image']['size']>614400)
			{
				$this->session->set_flashdata("msg", "<div class='alert alert-danger'>Featured Image size must be within 600KB.</div>");
				redirect(base_url('tours/edit_tour_package/'.$tour_id));
			}
			$banner_image = $_FILES['banner_image']['name'];
			$banner_image = time().$banner_image;
			$filename     = basename($banner_image);
			$extension    = pathinfo($filename, PATHINFO_EXTENSION);
			$uniqueno     = substr(uniqid(),0,5);
			$randno       = substr(rand(),0,5);
			$new          = $uniqueno.$randno.'.'.$extension;
			$folder       = $this->template->domain_image_upload_path();
			$folderpath   = trim($folder.$new);
			$path         = addslashes($folderpath);
			move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
			$banner_image = $new; 
			$banner_image_update = ',banner_image="'.$banner_image.'" '; 
		}else{
		   $banner_image_update = '';
		  
		}
		$query = "update tours_city set CityName='$CityName',country_id='$Country_id',CountryName='$CountryName' ".$banner_image_update." where id='$id'";        
		//debug($query);exit;
		$return = $this->tours_model->query_run($query);
		//set_update_message();
		if($return){   
			redirect('tours/tour_city/'); 
		}else{ 
			echo $return; exit; 
		} 
	}
	$data = $this->custom_db->single_table_records('tours_city','*',array('id'=>$id));
	$tour_country = $this->tours_model->tour_country();
	$page_data['tour_country'] = $tour_country;
	$page_data['data'] = $data['data'][0];
	$page_data['id'] = $id;
	//debug($page_data);
	$this->template->view('tours/edit_tour_city',$page_data);
	
}
public function delete_tour_city($id) {
  $return = $this->custom_db->delete_record('tours_city',array('id'=>$id));
 // set_update_message('UL0100');
  if($return){redirect('tours/tour_city');} 
  //redirect('tours/tour_city');
}
public function activation_tour_region($id,$status) {
  $return = $this->tours_model->record_activation('tours_continent',$id,$status);
  if($return){redirect('tours/tour_region');} 
  else { echo $return;} 
}

public function edit_tour_region($id) {
  $tour_region_details = $this->tours_model->table_record_details('tours_continent',$id);
   // debug($tour_region_details); exit;      
  $page_data['tour_region_details'] = $tour_region_details[0];
    //debug($page_data); exit;
  $this->template->view('tours/edit_tour_region',$page_data);
}
public function edit_tour_region_save() {
  $data = $this->input->post();
    //debug($data); exit;
  $id             = $data['id'];
  $tour_region  = sql_injection($data['tour_region']);
  $query = "update tours_continent set name='$tour_region' where id='$id'";        
        //echo $query; //exit;
  $return = $this->tours_model->query_run($query);
  if($return)
    {   redirect('tours/tour_region/'); }
  else
    { echo $return; exit; }              
}

public function tour_country() {  
 $tour_country = $this->tours_model->tour_country();
     //debug($tour_country); exit;
 $tour_region = $this->tours_model->tour_region();
  //  debug($tour_region); exit;
 $page_data['tour_region'] = $tour_region;
 $page_data['tour_country'] = $tour_country;
 $this->template->view('tours/tour_country',$page_data);
}

public function tour_country_save() {
  $data = $this->input->post();
  $tour_country   = sql_injection($data['tour_country']);
  $tours_continent = sql_injection($data['continent']);
      //debug($tours_continent); exit;
       // $check_availibility = $this->tours_model->check_region_exist($tour_country);
        //debug($check_availibility); exit();
       // if(!$check_availibility)
       // {
	$banner_image = '';
	if(!empty($_FILES['banner_image']['name']))
	{
	   $banner_image = $_FILES['banner_image']['name'];
	   $banner_image = time().$banner_image;
	   $filename     = basename($banner_image);
	   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
	   $uniqueno     = substr(uniqid(),0,5);
	   $randno       = substr(rand(),0,5);
	   $new          = $uniqueno.$randno.'.'.$extension;
	   $folder       = $this->template->domain_image_upload_path();
	   $folderpath   = trim($folder.$new);
	   $path         = addslashes($folderpath);
	   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
	   $banner_image = $new; 
	   $banner_image_update = $banner_image; 
	}else{
	   $banner_image_update = '';
	}
 
  $query = "insert into tours_country set name='$tour_country',banner_image='$banner_image_update', status=1 , continent = '$tours_continent'";        
        //echo $query; //exit;
  $return = $this->tours_model->query_run($query);
  if($return)
    {   redirect('tours/tour_country/'); }
  else
    { echo $return; exit; } 
      //  }
      /*  else
        {
          $this->session->set_flashdata('region_msg','Region is already exist');
          redirect('tours/tour_country');
        }*/

       }

       public function approve_package($p_id)
       {
        $approve_status = $this->tours_model->approve_package($p_id);
        redirect('tours/tour_list_pending');

      }

      public function holiday_terms_n_condition()
      {
        error_reporting(0);
    //echo "hiii";exit();
        $page_data['tours_data'] =  $this->tours_model->check_exist_tc();
        $this->template->view('tours/tours_terms_n_conditions',$page_data);
      }

      public function save_terms_n_conditions()
      {
        error_reporting(0);
        $t_c = $this->input->post('terms_n_conditions');
        $insert_data['terms_n_conditions'] =  $this->input->post('terms_n_conditions');
        $insert_data['cancellation_policy'] = $this->input->post('cancellation_policy');
    //debug($_POST); exit;
        $check_exist = $this->tours_model->check_exist_tc();
        if($check_exist)
        {

         $this->tours_model->update_tc($insert_data);
         redirect('tours/holiday_terms_n_condition');
       }
       $this->db->insert('holiday_terms_n_condition',$insert_data);
       redirect('tours/holiday_terms_n_condition');
    //debug($_POST); exit();
     }

     public function holiday_cancellation_policy()
     {
      error_reporting(0); 
    //echo "hiii";exit();
    //$page_data['tours_data'] =  $this->tours_model->check_exist_tc();
      $this->template->view('tours/tours_cancellation');
    }

    public function save_cancellation_policy()
    {
      error_reporting(0);
      $t_c = $this->input->post('terms_n_conditions');
      $insert_data['terms_n_conditions'] =  $this->input->post('terms_n_conditions');
    //debug($_POST); exit;
      $check_exist = $this->tours_model->check_exist_tc();
      if($check_exist)
      {

       $this->tours_model->update_tc($t_c);
       redirect('tours/holiday_terms_n_condition');
     }
     $this->db->insert('holiday_terms_n_condition',$insert_data);
     redirect('tours/holiday_terms_n_condition');
    //debug($_POST); exit();
   }

   public function activation_country($id,$status) {
    $return = $this->tours_model->record_activation('tours_country',$id,$status);
    if($return){redirect('tours/tour_country');} 
    else { echo $return;} 
  }

  public function edit_tour_country($id) {
    $tour_country_details = $this->tours_model->table_record_details('tours_country',$id);
   // debug($tour_country_details); exit;      
    $page_data['tour_country_details'] = $tour_country_details[0];
    //debug($page_data); exit;
    $this->template->view('tours/edit_tour_country',$page_data);
  }
  public function edit_tour_country_save() {
    $data = $this->input->post();
    //debug($data); exit;
    $id             = $data['id'];
    $tour_country  = sql_injection($data['tour_country']);
	$banner_image = '';
    if(!empty($_FILES['banner_image']['name']))
    {
		if($_FILES['banner_image']['size']>614400)
		{
			$this->session->set_flashdata("msg", "<div class='alert alert-danger'>Featured Image size must be within 600KB.</div>");
			redirect(base_url('tours/edit_tour_package/'.$tour_id));
		}
		$banner_image = $_FILES['banner_image']['name'];
		$banner_image = time().$banner_image;
		$filename     = basename($banner_image);
		$extension    = pathinfo($filename, PATHINFO_EXTENSION);
		$uniqueno     = substr(uniqid(),0,5);
		$randno       = substr(rand(),0,5);
		$new          = $uniqueno.$randno.'.'.$extension;
		$folder       = $this->template->domain_image_upload_path();
		$folderpath   = trim($folder.$new);
		$path         = addslashes($folderpath);
		move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
		$banner_image = $new; 
		$banner_image_update = 'banner_image="'.$banner_image.'" '; 
	}else
    {
       $banner_image_update = '';
      
    }
    $query = "update tours_country set name='$tour_country',".$banner_image_update." where id='$id'";        

    $return = $this->tours_model->query_run($query);
    if($return)
      {   redirect('tours/tour_country/'); }
    else
      { echo $return; exit; }              
  }



  public function occupancy_managment()
  {
    $page_data['occupancy_details'] = $this->tours_model->get_occupancy();
    $this->template->view('tours/occupancy_managment',$page_data);
  }
	public function edit_occ_management($id)
	{
		$occupancy_details = $this->custom_db->single_table_records('occupancy_managment','*',array('id'=>$id));
		$page_data['occupancy'] = $occupancy_details['data'][0];
	 
	 
		//debug($page_data); //exit;      
		$this->template->view('tours/edit_occ_management',$page_data);
	}
	public function edit_occ_save()
	{
		$data = $this->input->post();
   // debug($data); exit;   
		$id             = $data['id'];
		$occupancy_name  = $data['occupancy_name'];
		$query = "update occupancy_managment set occupancy_name='$occupancy_name' where id='$id'";        
		//echo $query;exit;
		$return = $this->tours_model->query_run($query);
		if($return)
		  {   redirect('tours/occupancy_managment/'); }
		else
		  { echo $return; exit; }  
	}
  public function delete_occupancy_managment($id)
  {

    $return = $this->tours_model->delete_occupancy_managment($id);
    if($return)
    {
     header('Location: '.base_url().'tours/occupancy_managment/');
   } 
   else
   {
     echo $return;
   }  
 }
public function activation_occ($id,$status) {
    $return = $this->tours_model->record_activation('occupancy_managment',$id,$status);
    if($return){redirect('tours/occupancy_managment');} 
    else { echo $return;} 
  }

 public function save_occupancy()
 {
  $insert_data['occupancy_name']  = $this->input->post('occupancy_name');
  $this->db->insert('occupancy_managment',$insert_data);
  redirect('tours/occupancy_managment');
}

public function price_management($id,$module,$list)
{
  //error_reporting(E_ALL);
  $page_data['occupancy_details'] = $this->tours_model->get_occupancy();
  $page_data['price_details']   = $this->tours_model->get_price_details($id,$module);
	if($module=='B2B'){
		$page_data['adv_pay']   = $this->custom_db->single_table_records('tours','b2b_adv_pay',array('id'=>$id));
	}else{
		$page_data['adv_pay']   = $this->custom_db->single_table_records('tours','b2c_adv_pay',array('id'=>$id));
	}
  $page_data['tour_id'] = $id;
  $currency  = $this->tours_model->get_currency_list(); 
  // debug($page_data);exit;
  $page_data['currency'] = $currency;
  $page_data['module']=$module;
  $page_data['list']=$list;
  $this->template->view('tours/price_management',$page_data);
}

public function save_price_management()
{
 // $from_date = $this->input->post('from_date');
 // $to_date = $this->input->post('to_date');
 // $from_date = date("Y-m-d", strtotime($from_date) );
 // $to_date = date("Y-m-d", strtotime($to_date) );
 // $depature_price = $this->input->post('depature_price');
 // $sessional_price = $this->input->post('sessional_price');
 // $value_type = $this->input->post('value_type');
 // $markup = $this->input->post('markup');

  $occupancy = $this->input->post('occupancy');
  $tour_id = $this->input->post('tour_id');
  $currency = $this->input->post('currency');
  $package_type = $this->input->post('package_type');
  $purchase_price = $this->input->post('purchase_price');
  $netprice_price = $this->input->post('netprice_price');
  $market_price = $this->input->post('market_price');
  $adv_pay = $this->input->post('advance_pay');
  $list = $this->input->post('list_type');
  $currency_obj = new Currency(array('module_type' => 'Holiday','from' => $currency , 'to' => get_application_default_currency())); 
  $converted_currency_rate = $currency_obj->getConversionRate(true);
// debug($converted_currency_rate);exit;
  #$currency_converter = $this->custom_db->single_table_records('currency_converter','country,original_value',array('country'=>$currency));
  //debug($currency_converter);exit();
  if($package_type=='B2B'){
	  $adv_data =array(
		'b2b_adv_pay'=> $adv_pay 
	  );
  }else{
	  $adv_data =array(
		'b2c_adv_pay'=> $adv_pay 
	  );
  }
  
  $this->custom_db->update_record('tours',$adv_data,array('id'=>$tour_id));
  
  
  if($currency!='NGN'){
    $converted_currency_rate= $converted_currency_rate;
  }else{
    $converted_currency_rate= 1;  
  }
  $airliner_price = $sessional_price/$converted_currency_rate;
  $final_airliner_price = $airliner_price;
  if($value_type == 'percentage')
  {
   $calculated_markup = ($airliner_price*$markup)/100;
  }
  else
  {
   $calculated_markup = $markup;
  }
  $airliner_price += $calculated_markup;
 // $calculated_markup = $markup;
 $insert_data = array(//'from_date' => $from_date,
   //'to_date'   => $to_date,
   'package_type' => $package_type,
   'occupancy' => $occupancy,
   'purchase_price' => $purchase_price,
   'market_price' => $market_price,
   'netprice_price' => $netprice_price,
   'currency' =>$currency,
  // 'sessional_price' => $sessional_price,
   //'final_airliner_price' => $final_airliner_price,
  // 'markup'     => $markup,
  // 'value_type' => $value_type,
   //'calculated_markup' => $calculated_markup,
 //  'airliner_price' => $airliner_price,
   'tour_id' => $tour_id);

 // debug($insert_data);exit();
 $result = $this->db->insert('tour_price_management',$insert_data);
 
 
 if($result)
 {
   header('Location: '.base_url().'tours/price_management/'.$tour_id.'/'.$package_type.'/'.$list);
 }else{
	 echo $this->db->last_query();exit;
 }
}
public function edit_price($id,$list)
{
  $page_data['price_details_single']  = $this->tours_model->get_price_details_single($id);
  $page_data['occupancy_details'] = $this->tours_model->get_occupancy();
  $currency  = $this->tours_model->get_currency_list(); 
  $page_data['currency'] = $currency;
  $page_data['list'] = $list;
	if($page_data['price_details_single'][0]['package_type']=='B2B'){
		$page_data['adv_pay']   = $this->custom_db->single_table_records('tours','b2b_adv_pay',array('id'=>$page_data['price_details_single'][0]['tour_id']))['data'][0];
	}else{
		$page_data['adv_pay']   = $this->custom_db->single_table_records('tours','b2c_adv_pay',array('id'=>$page_data['price_details_single'][0]['tour_id']))['data'][0];
	}
	//echo $this->db->last_query();
	//debug($page_data);exit; 
  $this->template->view('tours/price_management_edit',$page_data);
}
public function delete_price($id,$tour_id,$module,$list)
{

  $return = $this->tours_model->delete_tour_price($id);
  if($return)
  {
	header('Location: '.base_url().'tours/price_management/'.$tour_id.'/'.$module.'/'.$list);
 } 
 else
 {
   echo $return;
 }  
}

public function price_management_pending($id)
{
  $page_data['occupancy_details'] = $this->tours_model->get_occupancy();
  $page_data['price_details']   = $this->tours_model->get_price_details($id);
  $page_data['tour_id'] = $id;
  $this->template->view('tours/price_management_pending',$page_data);
}

public function save_edit_price_management($id='')
{
  $id = $this->input->post('id');
  $all_post = $this->input->post();
  //debug($all_post);exit;
  $from_date = $this->input->post('from_date');
  $to_date = $this->input->post('to_date');
  $from_date = date("Y-m-d", strtotime($from_date) );
  $to_date = date("Y-m-d", strtotime($to_date) );
  $occupancy = $this->input->post('occupancy');
        // $depature_price = $this->input->post('depature_price');
  $sessional_price = $this->input->post('airliner_price');
  $tour_id = $this->input->post('tour_id');
  $currency = $this->input->post('currency');
  $value_type = $this->input->post('value_type');
  $markup = $this->input->post('markup');
  $purchase_price = $this->input->post('purchase_price');
  $netprice_price = $this->input->post('netprice_price');
  $market_price = $this->input->post('market_price');
  $package_type = $this->input->post('package_type');
  $adv_pay = $this->input->post('advance_pay');
  $currency_obj = new Currency(array('module_type' => 'Holiday','from' => $currency , 'to' => get_application_default_currency())); 
  $converted_currency_rate = $currency_obj->getConversionRate(true);
  // debug($currency_obj);exit;
  /*$currency_converter = $this->custom_db->single_table_records('currency_converter','country,original_value',array('country'=>$currency));
  $converted_currency_rate= $currency_converter['data'][0]['original_value'];*/

       // $final_airliner_price = $sessional_price+$calculated_markup;
   if($currency!='NGN'){
  $converted_currency_rate= $currency_converter['data'][0]['original_value'];
  }else{
  $converted_currency_rate= 1;  
  }
  $converted_currency_rate= 1;
  // echo $converted_currency_rate;exit;
  $airliner_price = $sessional_price/$converted_currency_rate;
  $final_airliner_price = $airliner_price;
    // echo $airliner_price;exit;
  if($value_type == 'percentage')
  {
   $calculated_markup = ($airliner_price*$markup)/100;
 }
 else
 {
   $calculated_markup = $markup;
 }
 $airliner_price += $calculated_markup;
 $data_arr=array(
   //'occupancy'=>$occupancy,
  // 'sessional_price'=>$sessional_price,
   //'airliner_price'=>$airliner_price,
   'tour_id'=>$tour_id,
   'purchase_price' => $purchase_price,
   'market_price' => $market_price,
   'netprice_price' => $netprice_price,
   //'markup'=>$markup,
   //'calculated_markup'=>$calculated_markup,
   //'final_airliner_price'=>$final_airliner_price,
  // 'value_type'=>$value_type,
  // 'from_date'=>$from_date,
  // 'to_date'=>$to_date,
   'currency'=>$currency
   );
 // debug($data_arr);exit;
 $result = $this->custom_db->update_record('tour_price_management',$data_arr,array('id' => $id));
  if($package_type=='B2B'){
	  $adv_data =array(
		'b2b_adv_pay'=> $adv_pay 
	  );
  }else{
	  $adv_data =array(
		'b2c_adv_pay'=> $adv_pay 
	  );
  }
  
  $this->custom_db->update_record('tours',$adv_data,array('id'=>$tour_id));
 //set_update_message();
 //header('Location: '.base_url().'tours/price_management/'.$tour_id);
  redirect(base_url().'tours/price_management/'.$tour_id.'/'.$package_type.'/'.$all_post['list_type']);
       /*if($result)
       {
       }*/
     }
	public function b2b_voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
    {
		
      error_reporting(0);
      $page_data['tour_id'] = $tour_id;
      $this->load->model('tours_model');
      $page_data['menu'] = false;
      $page_data ['tour_data']            = $this->tours_model->tour_data($tour_id);
      $page_data ['tours_itinerary']      = $this->tours_model->tours_itinerary($tour_id);
     // debug($dep_date); exit;
      $page_data ['tours_itinerary_dw']   = @$this->tours_model->tours_itinerary_dw($tour_id);
	  $page_data ['tours_hotel_det']   		= @$this->tours_model->tour_hotel_city_data($tour_id);
     //debug($page_data ['tours_hotel_det']); exit;
      $page_data ['tours_itinerary_wd']   = $this->tours_model->tours_itinerary_dw($tour_id);
      $page_data ['tours_date_price']     = $this->tours_model->tours_date_price($tour_id);
	 if($page_data['tour_data']['package_type']=='fit'){
		$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_valid_from_to_date', '*', array('tour_id'=>$tour_id))['data'];
		 
	 }else{
		 $page_data['dep_dates'] = $this->custom_db->single_table_records('tour_dep_dates', '*', array('tour_id'=>$tour_id))['data'];
	 }
     // $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ,final_airliner_price,markup,group_concat(markup) markup ,tour_id, from_date, to_date , currency from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
	$b2b_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2B' ");
	$b2c_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2C' ");
	//echo $this->db->last_query();
	$page_data['tours_city_name'] = $this->tours_model->tours_city_name();
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
       $quotation_details = $this->tours_model->quotation_details($quotation_id);
       if ($quotation_details['status']==1) {
        $page_data['quotation_details'] = $quotation_details['data'];
      }
    }
    if ($app_reference!='') {
     $booking_details = $this->tours_model->booking_details($app_reference);
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
  $this->template->view('tours/b2b_broucher',$page_data);
  break;
  case 'show_pdf_voucher' :
  $get_view = $this->template->isolated_view ( 'tours/b2b_broucher_pdf',$page_data );
 
  $this->load->library ( 'provab_pdf' );
  $this->provab_pdf->create_pdf ( $get_view, 'show');   
  break;
  
  case 'mail' :
 // $mail_template =$this->template->isolated_view('tours/b2c_broucher',$page_data);   
  $this->load->library ( 'provab_mailer' ); 
	$get_view = $this->template->isolated_view ( 'tours/b2b_broucher_pdf',$page_data );
  $this->load->library ( 'provab_pdf' );
  //$pdf = $this->provab_pdf->create_pdf($get_view,'F');
	$create_pdf = new Provab_Pdf();
	$pdf = $create_pdf->create_pdf($get_view,'');
						//$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						//$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
  
  
	if(count($ex_data)>0){        
		$message = '<strong style="line-height:25px; font-size:16px;">Good day '.$ex_data['name'].',</strong><br>
			<span style="line-height:25px; font-size:15px;">Please find the Holiday Package below. </span>';
			if($ex_data['booking_url']){  
				$message .= '<a style="line-height:25px; font-size:16px;" href="'.$ex_data['booking_url'].'" target="_blank">Click here to pay</a><br><br>';
			}
	}
	$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $email_body,$pdf); 
if(!empty($redirect)){
 return true;
}else{
  redirect(base_url().'tours/b2b_voucher/'.$tour_id,'refresh');
}
break;
}
}
     public function b2c_voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
     {
      // echo $email;exit;
      error_reporting(0);
      $page_data['tour_id'] = $tour_id;
      $this->load->model('tours_model');
      $page_data['menu'] = false;
      $page_data ['tour_data']            = $this->tours_model->tour_data($tour_id);
      $page_data ['tours_itinerary']      = $this->tours_model->tours_itinerary($tour_id);
     // debug($dep_date); exit;
      $page_data ['tours_itinerary_dw']   = @$this->tours_model->tours_itinerary_dw($tour_id);
	  $page_data ['tours_hotel_det']   		= @$this->tours_model->tour_hotel_city_data($tour_id);
     //debug($page_data ['tours_hotel_det']); exit;
      $page_data ['tours_itinerary_wd']   = $this->tours_model->tours_itinerary_dw($tour_id);
      $page_data ['tours_date_price']     = $this->tours_model->tours_date_price($tour_id);
	 if($page_data['tour_data']['package_type']=='fit'){
		$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_valid_from_to_date', '*', array('tour_id'=>$tour_id))['data'];
		 
	 }else{
		 $page_data['dep_dates'] = $this->custom_db->single_table_records('tour_dep_dates', '*', array('tour_id'=>$tour_id))['data'];
	 }
     // $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ,final_airliner_price,markup,group_concat(markup) markup ,tour_id, from_date, to_date , currency from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
	$b2b_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2B' ");
	$b2c_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2C' ");
	//echo $this->db->last_query();
	$page_data['tours_city_name'] = $this->tours_model->tours_city_name();
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
       $quotation_details = $this->tours_model->quotation_details($quotation_id);
       if ($quotation_details['status']==1) {
        $page_data['quotation_details'] = $quotation_details['data'];
      }
    }
    if ($app_reference!='') {
     $booking_details = $this->tours_model->booking_details($app_reference);
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
  $this->template->view('tours/b2c_broucher',$page_data);
  break;
  case 'show_pdf' :
  $get_view = $this->template->isolated_view ( 'tours/b2c_broucher_pdf',$page_data );
  $this->load->library ( 'provab_pdf' );
  $this->provab_pdf->create_pdf ( $get_view, 'D');   
  break;
  case 'mail' :
  $mail_template =$this->template->isolated_view('tours/b2c_broucher',$page_data);   
  $this->load->library ( 'provab_mailer' ); 
	$get_view = $this->template->isolated_view ( 'tours/b2c_broucher_pdf',$page_data );
  $this->load->library ( 'provab_pdf' );
  //$pdf = $this->provab_pdf->create_pdf($get_view,'F');
	$create_pdf = new Provab_Pdf();
	$pdf = $create_pdf->create_pdf($mail_template,'');
						//$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						//$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
  
  
	if(count($ex_data)>0){        
		$message = '<strong style="line-height:25px; font-size:16px;">Good day '.$ex_data['name'].',</strong><br>
			<span style="line-height:25px; font-size:15px;">Please find the Holiday Package below. </span>';
			if($ex_data['booking_url']){  
				$message .= '<a style="line-height:25px; font-size:16px;" href="'.$ex_data['booking_url'].'" target="_blank">Click here to pay</a><br><br>';
			}
	}
	$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $email_body,$pdf); 
		//$this->provab_mailer->send_mail($to_email, domain_name().' - Bus Ticket',$mail_template ,$pdf);
if(!empty($redirect)){
 return true;
}else{
  redirect(base_url().'tours/b2c_voucher/'.$tour_id,'refresh');
}
break;
}
}

public function email_voucher($email,$tour_id)
{

    //$tour_id = 1;
  
  error_reporting(0);
  $this->load->model('tours_model');
  $page_data ['tour_data']            = $this->tours_model->tour_data($tour_id);
  $page_data ['tours_itinerary']      = $this->tours_model->tours_itinerary($tour_id,$dep_date);
  $page_data ['tours_itinerary_dw']   = $this->tours_model->tours_itinerary_dw($tour_id,$dep_date);
  $page_data ['tours_itinerary_wd']   = $this->tours_model->tours_itinerary_dw($tour_id);
  $page_data ['tours_date_price']     = $this->tours_model->tours_date_price($tour_id);
  $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ, group_concat(markup) markup ,tour_id, from_date, to_date from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
  $page_data['tour_price'] = json_decode(json_encode($tour_data),true);

    //debug($page_data); exit();
  $tour_cities =  $page_data['tour_data']['tours_city'];
  $tour_cities_array = json_decode($tour_cities);

  foreach ($tour_cities_array as $t_city) {
      $query_x = "select * from tours_city where id='$t_city'"; // echo $query; exit;
      $exe_x   = mysql_query($query_x);
      $visited_city[] = mysql_fetch_assoc($exe_x);

    }
    //debug($page_data); exit();  
    $page_data['visited_city'] = $visited_city;
    $page_data['tour_id'] = $tour_id;
    $page_data['menu'] = false;
    $mail_template =$this->template->isolated_view('tours/broucher',$page_data);
    //debug($mail_template); exit();
    //$mail_template = '<h1>hello</h1>';
  //  $email =
    $this->load->library ( 'provab_pdf' );
    $this->load->library ( 'provab_mailer' ); 
    $pdf = $this->provab_pdf->create_pdf($mail_template,'F');


    $s = $this->provab_mailer->send_mail (18, $email, 'Activity Confirmation', 'Content',$pdf);
    //debug($s); exit();
    $status = true;
    echo 'success';

    //$this->template->view('tours/broucher',$page_data);

  }

  public function check_price_avilability()
  { //error_reporting(E_ALL);
    //debug($_POST); exit();
    $from = $this->input->post('from');
    $to = $this->input->post('to');
    $from = date("Y-m-d", strtotime($from));
    $to = date("Y-m-d", strtotime($to) );
    $occupency = $this->input->post('occupency');
    $tour_id = $this->input->post('tour_id');
    $price_avilability = $this->tours_model->check_price_avilability($from,$to,$occupency,$tour_id);
    //debug($price_avilability); exit();
    if($price_avilability)
    {
     echo json_encode(array('status'=>false));
   }
   else
   {
     echo json_encode(array('status'=>true));
   }
 }

 public function tours_delete_image_id()
 {

  $deletename = $this->input->post('image_name');
  $deleteid   = $this->input->post('image_id');
  $tours_data = $this->tours_model->tour_data($deleteid);
    //debug($tours_data); exit();
  $images   = $tours_data['gallery'];
  $image_data = explode(',', $images);
  foreach($image_data as $key => $images_values)
  {
    if($images_values == $deletename)
    {

      unset($image_data[$key]);
    }
  }

  $new_data = implode(',', $image_data);
  $new_data = array('gallery' => $new_data);


  $info = $this->tours_model->update_tours_images($new_data, $deleteid);
  echo "1";
}

public function update_tour_voucher($tour_id) {
    $tour_data = $this->tours_model->tour_data_temp($tour_id); //debug($tour_data); exit; 
    $page_data['tour_data'] = $tour_data;
    $tour_destinations = $this->tours_model->tour_destinations();
    $page_data['tour_destinations'] = $tour_destinations;
    $page_data['tour_id'] = $tour_id;

    $tours_continent = $this->tours_model->tours_continent();

    $page_data['tours_continent_country'] = $this->tours_model->tours_continent_country($tour_id);
    $page_data['tours_country_city']      = $this->tours_model->tours_country_city($tour_id);
    $page_data['tours_country_name']      = $this->tours_model->tour_country();

    $page_data['tours_continent'] = $tours_continent;
    $page_data['tour_type'] = $this->tours_model->tour_type();
    $page_data['tour_subtheme'] = $this->tours_model->tour_subtheme();
    // /debug($page_data); exit;
    $this->template->view('tours/update_tour_package',$page_data);
  }

  public function update_tour_voucher_save()
  {
    $data = $this->input->post();
    //debug($data); exit();
    $tour_id               = sql_injection($data['tour_id']);
    $query_x = "select * from tours_temp where id='$tour_id'"; // echo $query; exit;
    $exe_x   = mysql_query($query_x);
    $fetch_x = mysql_fetch_array($exe_x);
    //debug($fetch_x); exit();
    if($fetch_x)
    {
      $old_image = $fetch_x['gallery'];
    }
    $package_name          = sql_injection($data['package_name']);
    $tours_continent       = sql_injection($data['tours_continent']);
    $tours_city_new     = $data['tours_city_new'];
    $tours_city = $tours_city_new;
    $tours_city     = implode(',',$tours_city);
    $duration       = sql_injection($data['duration']);
    $tour_type          = $data['tour_type'];
    $tour_type          = implode(',',$tour_type);
    $tours_country      = $data['tours_country'];
    $tours_country      = implode(',',$tours_country);
    $theme          = $data['theme'];
    $theme          = implode(',',$theme);
    $adult_twin_sharing    = sql_injection($data['adult_twin_sharing']);
    $adult_tripple_sharing = $data['adult_tripple_sharing'];
    if($adult_tripple_sharing=='')
    {
     $adult_tripple_sharing = 0;
   }
   else
   {
     $adult_tripple_sharing = sql_injection($adult_tripple_sharing);
   }

   $highlights            = sql_injection($data['highlights']);
   $inclusions            = sql_injection($data['inclusions']);
   $exclusions            = sql_injection($data['exclusions']);
   $terms                 = sql_injection($data['terms']);
   $canc_policy           = sql_injection($data['canc_policy']);

   $ppg        = $_REQUEST['gallery_previous'];
   $total_ppg  = count($ppg) ;
   $ppg_list   = '';
   for($c=0;$c<$total_ppg;$c++)
   {
    if($ppg_list=='')
    {
      $ppg_list = $ppg[$c];
    }
    else
    {
      $ppg_list = $ppg_list.','.$ppg[$c];
    }       
  }
  if($total_ppg>0)
  {
    $ppg_list = $ppg_list.',';
  }
  else
  {
    $ppg_list = '';
  } 
  $arr=array();
  if($_FILES['gallery']['name'][0]!="")
  {       
    $list  = $_FILES['gallery']['name'];
    $total_images = count($list); 
    for($i=0;$i<$total_images;$i++)
    {
         // for setting the unique name of image starts @@@@@@@@@@@@@@@@@@@
      $filename  = basename($list[$i]);
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      $uniqueno  = substr(uniqid(),0,5);
      $randno    = substr(rand(),0,5);
      $new       = $uniqueno.$randno.'.'.$extension;
      $folder    = $this->template->domain_image_upload_path();
      $folderpath= trim($folder.$new);
      $path      = addslashes($folderpath);
      move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $folderpath);  
      array_push($arr,$new);

    } 
  }   
  if(!empty($_FILES['banner_image']['name']))
  {
   $banner_image = $_FILES['banner_image']['name'];
   $filename     = basename($banner_image);
   $extension    = pathinfo($filename, PATHINFO_EXTENSION);
   $uniqueno     = substr(uniqid(),0,5);
   $randno       = substr(rand(),0,5);
   $new          = $uniqueno.$randno.'.'.$extension;
   $folder       = $this->template->domain_image_upload_path();
   $folderpath   = trim($folder.$new);
   $path         = addslashes($folderpath);
   move_uploaded_file($_FILES['banner_image']['tmp_name'], $folderpath);             
   $banner_image = $new; 
   $banner_image_update = 'banner_image="'.$banner_image.'",'; 
 }else
 {
   $banner_image_update = '';
 }
 
 $old_image = explode(',', $old_image);
 $inclusions_checks   = $data['inclusions_checks'];
 $inclusions_checks   = json_encode($inclusions_checks,1);
 $Gallery_list = array_merge($arr,$old_image);
 $Gallery_list = implode(',', $Gallery_list);

 if($fetch_x)
 {
      //echo "update";exit();
  $query  = "update tours_temp set package_name='$package_name',
  tours_continent='$tours_continent',
  tours_country='$tours_country',
  tours_city='$tours_city',
  duration='$duration',
  tour_type='$tour_type',
  theme='$theme',
  adult_twin_sharing='$adult_twin_sharing',
  adult_tripple_sharing='$adult_tripple_sharing',
  child_with_bed='$child_with_bed',
  child_without_bed='$child_without_bed',
  joining_directly='$joining_directly',
  single_suppliment='$single_suppliment',
  service_tax='$service_tax',
  tcs='$tcs',
  highlights='$highlights',
  inclusions='$inclusions',
  exclusions='$exclusions',
  terms='$terms',
  canc_policy='$canc_policy',
  inclusions_checks='$inclusions_checks',
  ".$banner_image_update."
  gallery='$Gallery_list'
  where id='$tour_id'";
}
else
{

  $query  = "insert into tours_temp set package_name='$package_name',
  tours_continent='$tours_continent',
  tours_country='$tours_country',
  tours_city='$tours_city',
  duration='$duration',
  tour_type='$tour_type',
  theme='$theme',
  adult_twin_sharing='$adult_twin_sharing',
  adult_tripple_sharing='$adult_tripple_sharing',
  child_with_bed='$child_with_bed',
  child_without_bed='$child_without_bed',
  joining_directly='$joining_directly',
  single_suppliment='$single_suppliment',
  service_tax='$service_tax',
  tcs='$tcs',
  highlights='$highlights',
  inclusions='$inclusions',
  exclusions='$exclusions',
  terms='$terms',
  canc_policy='$canc_policy',
  inclusions_checks='$inclusions_checks',
  ".$banner_image_update."
  gallery='$Gallery_list'
  where id='$tour_id'";
}

        // echo $query; exit;
$return = $this->tours_model->query_run($query);
if($return)
{
 header('Location: '.base_url().'tours/edit_tour_package/'.$tour_id);

} 
else { echo $return; }        
}

public function updated_voucher($tour_id)
{

    //$tour_id = 1;

  error_reporting(0);
  $this->load->model('tours_model');
  $page_data ['tour_data']            = $this->tours_model->tour_data_temp($tour_id);
  $page_data ['tours_itinerary']      = $this->tours_model->tours_itinerary($tour_id,$dep_date);
  $page_data ['tours_itinerary_dw']   = $this->tours_model->tours_itinerary_dw($tour_id,$dep_date);
  $page_data ['tours_itinerary_wd']   = $this->tours_model->tours_itinerary_dw($tour_id);
  $page_data ['tours_date_price']     = $this->tours_model->tours_date_price($tour_id);
  $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ, group_concat(markup) markup ,tour_id, from_date, to_date from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
  $page_data['tour_price'] = json_decode(json_encode($tour_data),true);

    //debug($page_data); exit();
  $tour_cities =  $page_data['tour_data']['tours_city'];
  $tour_cities_array = json_decode($tour_cities);

  foreach ($tour_cities_array as $t_city) {
      $query_x = "select * from tours_city where id='$t_city'"; // echo $query; exit;
      $exe_x   = mysql_query($query_x);
      $visited_city[] = mysql_fetch_assoc($exe_x);

    }
    //debug($page_data); exit();  
    $page_data['visited_city'] = $visited_city;
    $page_data['tour_id'] = $tour_id;
    $page_data['menu'] = false;
    $mail_template =$this->template->isolated_view('tours/broucher',$page_data);
    //debug($mail_template); exit();
    //$mail_template = '<h1>hello</h1>';
  //  $email =
    $this->load->library ( 'provab_pdf' );
    $this->load->library ( 'provab_mailer' ); 
    $pdf = $this->provab_pdf->create_pdf($mail_template,'F');


    
    
    $this->template->view('tours/broucher',$page_data);
    
  }
  public function cancel_booking($app_reference)
  {
    $this->load->model('custom_db');   
    $condition[]=array(
      'app_reference','=','"'.$app_reference.'"'
      );
    $booking_details = $this->tours_model->booking($condition);
    $this->load->library('provab_mailer'); 
    $page_data['data'] = $booking_details['data'][$app_reference];
    // debug($page_data);die;
   $this->template->view('tours/pre_cancellation', $page_data);
 }
 public function cancel_full_booking($app_reference)
  {
    $this->load->model('custom_db');
    $this->custom_db->update_record('tour_booking_details',array('status'=>'CANCELLED','final_cancel_date'=>date("Y-m-d h:i:sa")),array('app_reference'=>$app_reference));    
    $condition[]=array(
      'app_reference','=','"'.$app_reference.'"'
      );
    $page_data['app_reference'] = $app_reference;
    $page_data['status'] = 'CANCELLED';
    $booking_details = $this->tours_model->booking($condition);
    $this->load->library('provab_mailer'); 
    foreach ($booking_details['data'] as $key => $data) {
     $enquiry_reference_no=$key;
   }
   $voucher_data = $data;
   $attributes = json_decode($data['booking_details']['attributes'], true);
   $user_attributes = json_decode($data['booking_details']['user_attributes'], true);
   $voucher_data ['tours_itinerary_dw']   = $this->tours_model->tours_itinerary_dw($attributes['tour_id'],$attributes['departure_date']);
   $email = $user_attributes['email'];
   $voucher_data['menu'] = false;
   $mail_template =$this->template->isolated_view('voucher/holiday_pdf',$voucher_data);
    // echo $mail_template; exit('');
   $this->load->library ( 'provab_pdf' );
   $pdf = $this->provab_pdf->create_pdf($mail_template,'F', $app_reference);

   $this->provab_mailer->send_mail(21, $email, 'Holiday Booking Cancelled', $mail_template,$pdf);
  $this->template->view('tours/cancellation_details',$page_data);
 }
 public function request_booking()
 {
  $post_data = $this->input->post();
  $enquiry_reference_no = generate_holiday_reference_number('ZHI');
  $post_data['enquiry_reference_no']=$enquiry_reference_no;
    // $post_data['tour_id']=$tour_id;
  $post_data['created_by']='supervision';
  $post_data['date']=date('Y-m-d H:i:s');
  $post_data['status']=1;
  $post_data['created_by_id']=$this->entity_user_id;
  $this->load->model('custom_db');
  $return = $this->custom_db->insert_record('tours_enquiry',$post_data);
  $this->send_link_to_user($enquiry_reference_no,false);
  redirect(base_url().'tours/tour_list','refresh');
}
/*public function send_booking_link($redirect=true)
{
  error_reporting(E_ALL);
  $post_data=$this->input->post();
  debug($post_data); exit;
  if ($post_data['enquiry_reference_no']) {
   $enquiry_data = $this->tours_model->enquiry_user_details($post_data['enquiry_reference_no']);
   $enquiry_data = json_decode(json_encode($enquiry_data[0]),true);
 }else{
   $post_data['departure_date']=date('Y-m-d',strtotime($post_data['departure_date']));
   $enquiry_data = $post_data;
 }
 $enquiry_data['tour_id'] = ($post_data['tour_id'])? $post_data['tour_id'] : $enquiry_data['tour_id'];

 $quote_reference = generate_holiday_reference_number('ZVQ');
 $tours_quotation_log_data=array();
 $tours_quotation_log_data['quote_reference']=$quote_reference;
 $tours_quotation_log_data['enquiry_reference_no']=$post_data['enquiry_reference_no'];
 $tours_quotation_log_data['tour_id']=$enquiry_data['tour_id'];
 $tours_quotation_log_data['departure_date']=$enquiry_data['departure_date'];
 $tours_quotation_log_data['title']=$enquiry_data['title'];
 $tours_quotation_log_data['first_name']=$enquiry_data['name'];
 $tours_quotation_log_data['middle_name']=$enquiry_data['mname'];
 $tours_quotation_log_data['last_name']=$enquiry_data['lname'];
 $tours_quotation_log_data['email']=$enquiry_data['email'];
 $tours_quotation_log_data['phone']=$enquiry_data['pn_country_code'].' '.$enquiry_data['phone'];
 $tours_quotation_log_data['quoted_price']=$post_data['total'];
 $tours_quotation_log_data['currency_code']=get_application_currency_preference();     
 $tours_quotation_log_data['user_attributes']=json_encode($post_data);
 $tours_quotation_log_data['created_by_id']=$this->entity_user_id;
 $tours_quotation_log_data['created_datetime']=date('Y-m-d H:i:s');
 $this->custom_db->insert_record('tours_quotation_log',$tours_quotation_log_data);
 
 if($post_data['quote_type']=='request_quote'){     
     // debug($enquiry_data['email']); exit('xxx');
   if(!empty($enquiry_data['email']))
   {
    $ex_data['name']=$enquiry_data['name'];
    $res = $this->voucher($enquiry_data['tour_id'],'mail','mail',$quote_reference,'',$enquiry_data['email'],'redirect',$ex_data);      
  }
}else{
  if($post_data['enquiry_reference_no']){
    $tours_enquiry = $this->custom_db->get_result_by_query('SELECT * FROM tours_enquiry WHERE enquiry_reference_no = "'.$post_data['enquiry_reference_no'].'" ');
    if($tours_enquiry){
      $tours_enquiry = json_decode(json_encode($tours_enquiry),1);
      $post_data['tour_id'] = $tours_enquiry[0]['tour_id'];
      $post_data['departure_date'] = $tours_enquiry[0]['departure_date'];
    }
  }
  $app_reference = generate_holiday_reference_number('ZVZ');
  $tour_booking_details_data=array();
  $tour_booking_details_data['enquiry_reference_no']=$post_data['enquiry_reference_no'];
  $tour_booking_details_data['app_reference']=$app_reference;
  $tour_booking_details_data['status']='PROCESSING';
  $tour_booking_details_data['basic_fare']=$post_data['total'];
  $tour_booking_details_data['currency_code']=$post_data['currency'];
  $tour_booking_details_data['payment_status']='unpaid';
  $tour_booking_details_data['created_datetime']=date('Y-m-d H:i:s');
  $tour_booking_details_data['created_by_id']=$this->entity_user_id;
  $tour_booking_details_data['attributes']=json_encode($post_data);
  $this->custom_db->insert_record('tour_booking_details',$tour_booking_details_data);
  $booking_url = base_url().'index.php/tours/pre_booking/'.$app_reference;
  $booking_url = str_replace('supervision/', '', $booking_url);
  if(!empty($enquiry_data['email']))
  {
    $ex_data['booking_url']=$booking_url;
    $ex_data['name']=$enquiry_data['name'];
    $res = $this->voucher($enquiry_data['tour_id'],'mail','mail','',$app_reference,$enquiry_data['email'],'redirect',$ex_data);
  }
}
set_update_message ();
if($res){
 $this->load->library('user_agent');
 if ($this->agent->is_referral())
 {
  redirect ( $this->agent->referrer());
}else{
  redirect ( base_url () . 'index.php/tours/tours_enquiry');
}
}
}*/
public function send_booking_link($redirect=true)
{
  error_reporting(E_ALL);
  $post_data=$this->input->post();
  // debug($post_data);
  if ($post_data['enquiry_reference_no']) {
   $enquiry_data = $this->tours_model->enquiry_user_details($post_data['enquiry_reference_no']);
   $enquiry_data = json_decode(json_encode($enquiry_data[0]),true);
 }else{
   $post_data['departure_date']=date('Y-m-d',strtotime($post_data['departure_date']));
   $enquiry_data = $post_data;
 }
 $enquiry_data['tour_id'] = ($post_data['tour_id'])? $post_data['tour_id'] : $enquiry_data['tour_id'];

 $quote_reference =  PROJECT_PREFIX.'-'.PACKAGE_BOOKING.'-'.date('dmY-Hi').'-'.$enquiry_data['tour_id'];
 $tours_quotation_log_data=array();
 $tours_quotation_log_data['quote_reference']=$quote_reference;
 $tours_quotation_log_data['enquiry_reference_no']=$post_data['enquiry_reference_no'];
 $tours_quotation_log_data['tour_id']=$enquiry_data['tour_id'];
 $tours_quotation_log_data['departure_date']=$enquiry_data['departure_date'];
 $tours_quotation_log_data['title']=$enquiry_data['title'];
 $tours_quotation_log_data['first_name']=$enquiry_data['name'];
 //$tours_quotation_log_data['middle_name']=$enquiry_data['mname'];
 $tours_quotation_log_data['last_name']=$enquiry_data['lname'];
 $tours_quotation_log_data['email']=$enquiry_data['email'];
 $tours_quotation_log_data['phone']=$enquiry_data['pn_country_code'].' '.$enquiry_data['phone'];
 $tours_quotation_log_data['quoted_price']=$post_data['total'];
 $tours_quotation_log_data['currency_code']=get_application_currency_preference();     
 $tours_quotation_log_data['user_attributes']=json_encode($post_data);
 $tours_quotation_log_data['created_by_id']=$this->entity_user_id;
 $tours_quotation_log_data['created_datetime']=date('Y-m-d H:i:s');

 $this->custom_db->insert_record('tours_quotation_log',$tours_quotation_log_data);

 if($post_data['quote_type']=='request_quote'){     
      //debug($enquiry_data['email']); exit('xxx');
   if(!empty($enquiry_data['email']))
   {
    $ex_data['name']=$enquiry_data['name'];
     //debug($ex_data['name']); exit;  
    $res = $this->voucher($enquiry_data['tour_id'],'mail','mail',$quote_reference,'',$enquiry_data['email'],'redirect',$ex_data);  
     
  }
}else{
  if($post_data['enquiry_reference_no']){
    $tours_enquiry = $this->custom_db->get_result_by_query('SELECT * FROM tours_enquiry WHERE enquiry_reference_no = "'.$post_data['enquiry_reference_no'].'" ');
    if($tours_enquiry){
      $tours_enquiry = json_decode(json_encode($tours_enquiry),1);
      $post_data['tour_id'] = $tours_enquiry[0]['tour_id'];
      $post_data['departure_date'] = $tours_enquiry[0]['departure_date'];
    }
  }
  $app_reference = "PHM-".time();//generate_holiday_reference_number('ZVZ');
  
  $tour_booking_details_data=array();
  $tour_booking_details_data['enquiry_reference_no']=$post_data['enquiry_reference_no'];
  $tour_booking_details_data['app_reference']=$app_reference;
  $tour_booking_details_data['status']='PROCESSING';
  $tour_booking_details_data['basic_fare']=$post_data['total'];
  $tour_booking_details_data['currency_code']=$post_data['currency'];
  $tour_booking_details_data['payment_status']='unpaid';
  $tour_booking_details_data['created_datetime']=date('Y-m-d H:i:s');
  $tour_booking_details_data['created_by_id']=$this->entity_user_id;
  $tour_booking_details_data['attributes']=json_encode($post_data);
  $tour_booking_details_data['service_tax']=0;
  $tour_booking_details_data['discount']=0;
  $tour_booking_details_data['promocode']=0;
  $tour_booking_details_data['email']=$enquiry_data['email'];
  $tour_booking_details_data['remarks']=$post_data['quote_type'];
  $this->custom_db->insert_record('tour_booking_details',$tour_booking_details_data);
  $booking_url = base_url().'index.php/tours/pre_booking/'.$app_reference;
  $booking_url = str_replace('supervision/', '', $booking_url);
   debug($enquiry_data);exit;
  if(!empty($enquiry_data['email']))
  {
    $ex_data['booking_url']=$booking_url;
    $ex_data['name']=$enquiry_data['name'];
    $res = $this->voucher($enquiry_data['tour_id'],'mail','mail','',$app_reference,$enquiry_data['email'],'redirect',$ex_data);
  }
}

//set_update_message ();
if(1){
 $this->load->library('user_agent');
 if ($this->agent->is_referral())
 {
  redirect ( $this->agent->referrer());
}else{
  redirect ( base_url () . 'index.php/tours/tours_enquiry');
}
}
}
public function quotation_list()
{
  if (!check_user_previlege('p250')) {
   set_update_message("You Don't have permission to do this action.", WARNING_MESSAGE, array(
    'override_app_msg' => true
    ));
   redirect(base_url());
 }
    // $order_by = array('id' => 'DESC');
    // $quotation_list = $this->custom_db->single_table_records('tours_quotation_log', $cols = '*', $condition = array(), $offset = 0, $limit = 100000000,$order_by);
    // $page_data['quotation_list'] = $quotation_list['data'];
 $query = 'SELECT tql.*, u.title as a_title,u.first_name as a_f_name,u.last_name as a_l_name,t.package_name FROM tours_quotation_log AS tql LEFT JOIN tours AS t ON tql.tour_id = t.id  LEFT JOIN user AS u ON tql.created_by_id = u.user_id ORDER BY tql.id DESC';
 $quotation_list = $this->custom_db->get_result_by_query($query);    
 $page_data['quotation_list'] = json_decode(json_encode($quotation_list),true);
 $this->template->view('tours/quotation_list',$page_data);
 $array = array(
  'back_link' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()
  );    
 $this->session->set_userdata( $array );
}

  //for adding agent remark
  public function add_agent_remark(){
    

     $this->db->where(array('uuid'=>$this->session->userdata('AID')));
     $this->db->select('title,first_name,last_name');
     $re = $this->db->get('user');
    // debug($re);exit();
     if($re){
     
     
     $name = $re->result_array(); 
     $title = get_enum_list('title',$name[0]['title']);
     $name = $title." ".$name[0]['first_name']." ".$name[0]['last_name'];
     $r_id = $this->input->post('r_id');
     $agent_remark = $this->input->post('agent_remark');
     ///select current agent remark
     // $this->db->where(array('tours_itinerary_id'=>$r_id));
     // $this->db->select('agent_remark');
     // $re = $this->db->get('tours_enquiry');
     // $re = $re->result_array();
    
     $this->db->where(array('id'=>$r_id));
     // if( $this->db->update('tours_enquiry',array('agent_remark'=> $re[0]['agent_remark']."|".$agent_remark,
      if( $this->db->update('tours_enquiry',array('agent_remark'=>$agent_remark,

        'status'=>1,'created_by_name'=>$name))){
       echo json_encode(TRUE);
      }else{
       echo json_encode(FALSE); }

     }else{
      echo json_encode(FALSE);
     }

       }

       //to update price
       public function update_price(){

        //echo "dregregref";exit();
        $res = $this->tours_model->fetch_price();
    $i=0;     
foreach($res as $value){
  
   $total = $value['final_airliner_price']+$value['calculated_markup'];
   $data = array('airliner_price'=>$total);
   $id = $value['id'];
   $res = $this->tours_model->update_final_price($id,$data);
   if($res){
    $i++;
   }
  }
     echo "Total ".$i." value changed";
       }


         public function update_price_QAR(){

        //echo "dregregref";exit();
        $res = $this->tours_model->fetch_price();
        //debug($res);exit();
    $i=0;     
    
    foreach($res as $value){
  if($value['currency']=='NGN'){
   $total = $value['sessional_price']+$value['calculated_markup'];
   $data = array('airliner_price'=>$total);
   $id = $value['id'];
   $res = $this->tours_model->update_final_price($id,$data);
   if($res){
    $i++;
   }
  }}
     echo "Total ".$i." value changed";
       
     } 
	
	
	//Sanchitha 
	
	/* hotel Master contents*/
	public function hotel_list(){
		ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		$hotel_list = $this->tours_model->hotel_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['hotel_list'] = $hotel_list;
		//debug($page_data);exit;
		$this->template->view('tours/hotel_list',$page_data);
	}
	function get_city_name(){
		$post_data = $this->input->post('tour_country');
		$country = $post_data;
		//debug($post_data);exit;
		$options = '<option value="">Select City</option>';
		if (($country) != "") {
		//	echo $country;
			$result = $this->tours_model->ajax_tours_country($post_data);
			//debug($result);
			if (valid_array($result)) {
				foreach ($result as $c_key => $row) {
					$selected = $row['id'] == $selected_city ? 'selected' : '';
					$options .= '<option value="' . $row['id'] . '"' . $selected . '>' . $row['CityName'] . '</option>';
				}//for
			}//if 
		}
		echo $options;
	}
	public function hotel_data_save() {
	  $data = $this->input->post();
		//debug($data); exit;
		$hotel_data = array(
			'hotel_name' => sql_injection($data['hotel_name']),
			'country' => sql_injection($data['tour_country']),
			'city' => sql_injection($data['tour_city']),
			'star_rating' => sql_injection($data['star_rating']),
			'status' => '1'
		);
	
		$status=$this->custom_db->insert_record('tour_hotel_master',$hotel_data);
		
		debug($status);
	  if($status)
		{   redirect('tours/hotel_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_hotel_data($id) {
	  $hotel_details = $this->tours_model->hotel_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['hotel_details'] = $hotel_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 // debug($page_data);
	  $page_data['tour_city'] = $this->tours_model->ajax_tours_country($page_data['hotel_details']['country']);
		
	  $this->template->view('tours/edit_hotel_data',$page_data);
	}
	public function edit_hotel_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$hotel_data = array(
			'hotel_name' => sql_injection($data['hotel_name']),
			'country' => sql_injection($data['tour_country']),
			'city' => sql_injection($data['tour_city']),
			'star_rating' => sql_injection($data['star_rating']),
		);
	
		$status=$this->custom_db->update_record('tour_hotel_master',$hotel_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/hotel_list/');
		
	}
	public function delete_hotel($id) {
	  $return = $this->tours_model->record_delete('tour_hotel_master',$id);
	  if($return){redirect('tours/hotel_list');} 
	  else { echo $return;} 
	}
	public function activation_hotel($id,$status) {
	  $return = $this->tours_model->record_activation('tour_hotel_master',$id,$status);

	  if($return){redirect('tours/hotel_list');} 
	  else { echo $return;} 
	}
	 
	/* Supplier Master content */
	
	public function supplier_list(){
		
		$supplier_list = $this->tours_model->supplier_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['supplier_list'] = $supplier_list;
		//debug($page_data);exit;
		$this->template->view('tours/supplier_list',$page_data);
	}
	
	public function supplier_data_save() {
	  $data = $this->input->post();
		//debug($data); exit;
		$supplier_data = array(
			'supplier_name' => sql_injection($data['supplier_name']),
			'country' => sql_injection($data['tour_country']),
			'office_contact_number' => sql_injection($data['office_contact_number'])
		);
	
		$result=$this->custom_db->insert_record('tour_supplier_master',$supplier_data);
	//	debug($insert_id);exit;
		$this->custom_db->delete_record('tour_supplier_contact_details',array('supplier_id'=>$id));
		foreach($data['contact_person'] as $key =>$val){
			$contact_data = array(
				'supplier_id' => $result['insert_id'],
				'contact_person' => sql_injection($data['contact_person'][$key]),
				'email' => sql_injection($data['email'][$key]),
				'phone' => sql_injection($data['phone'][$key])
			);
			$this->custom_db->insert_record('tour_supplier_contact_details',$contact_data);
		}
		
	  if($result)
		{   redirect('tours/supplier_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_supplier_data($id) {
	  $supplier_details = $this->tours_model->supplier_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['supplier_details'] = $supplier_details;
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		//debug($page_data);exit;
	  $this->template->view('tours/edit_supplier_data',$page_data);
	}
	public function edit_supplier_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['s_id'];
		$supplier_data = array(
			'supplier_name' => sql_injection($data['supplier_name']),
			'country' => sql_injection($data['tour_country']),
			'office_contact_number' => sql_injection($data['office_contact_number'])
		);
	
		$result=$this->custom_db->update_record('tour_supplier_master',$supplier_data,array('id'=>$id));
	
		$this->custom_db->delete_record('tour_supplier_contact_details',array('supplier_id'=>$id));
		if(!empty($data['contact_person'])){
			foreach($data['contact_person'] as $key =>$val){
				$contact_data = array(
					'supplier_id' => $data['s_id'],
					'contact_person' => sql_injection($data['contact_person'][$key]),
					'email' => sql_injection($data['email'][$key]),
					'phone' => sql_injection($data['phone'][$key])
				);
				$result=$this->custom_db->insert_record('tour_supplier_contact_details',$contact_data);
			}
			
		}
	
		
		//echo $this->db->last_query();exit;
		  redirect('tours/supplier_list');
		
	}
	public function delete_supplier($id) {
	  $return = $this->tours_model->record_delete('tour_supplier_master',$id);
	  if($return){redirect('tours/supplier_list');} 
	  else { echo $return;} 
	}
	public function delete_country($id) {
	  $return = $this->tours_model->record_delete('tours_country',$id);
	  if($return){redirect('tours/tour_country');} 
	  else { echo $return;} 
	}
	public function delete_city($id) {
	  $return = $this->tours_model->record_delete('tours_city',$id);
	  if($return){redirect('tours/tour_city');} 
	  else { echo $return;} 
	}
	public function activation_supplier($id,$status) {
	  $return = $this->tours_model->record_activation('tour_supplier_master',$id,$status);

	  if($return){redirect('tours/supplier_list');} 
	  else { echo $return;} 
	}
	
	
	/* Inclusion Master contents*/
	public function inclusions_list(){
		$inclusion_list = $this->tours_model->inclusion_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['inclusion_list'] = $inclusion_list;
		//debug($page_data);exit;
		$this->template->view('tours/inclusion_list',$page_data);
	}
	
	public function inclusion_data_save() {
	  $data = $this->input->post();
		
		
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		//debug($data);
		$inclusion_data = array(
			'desctiption' =>$des,
			'country' =>$data['tour_country'],
			
		);
		//debug($inclusion_data);exit;
		$status=$this->custom_db->insert_record('tour_inclusion_master',$inclusion_data);
		
		
	  if($status)
		{   redirect('tours/inclusions_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_inclusion_data($id) {
	  $inclusion_details = $this->tours_model->inclusion_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['inclusion_details'] = $inclusion_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_inclusion',$page_data);
	}
	public function edit_inclusion_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		
		$inclusion_data = array(
			'desctiption' =>$des,
			'country' =>$data['tour_country'],
			
		);
		$status=$this->custom_db->update_record('tour_inclusion_master',$inclusion_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/inclusions_list/');
		
	}
	public function delete_inclusion($id) {
	  $return = $this->tours_model->record_delete('tour_inclusion_master',$id);
	  if($return){redirect('tours/inclusions_list');} 
	  else { echo $return;} 
	}
	public function activation_inclusion($id,$status) {
	  $return = $this->tours_model->record_activation('tour_inclusion_master',$id,$status);

	  if($return){redirect('tours/inclusions_list');} 
	  else { echo $return;} 
	}
	
	
	
	/* Exclusion Master contents*/
	public function exclusions_list(){
		$exclusion_list = $this->tours_model->exclusion_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['exclusion_list'] = $exclusion_list;
		//debug($page_data);exit;
		$this->template->view('tours/exclusions_list',$page_data);
	}
	
	public function exclusion_data_save() {
	  $data = $this->input->post();
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$exclusion_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_exclusions_master',$exclusion_data);
		
		
	  if($status)
		{   redirect('tours/exclusions_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_exclusion_data($id) {
	  $exclusion_details = $this->tours_model->exclusion_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['exclusion_details'] = $exclusion_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_exclusion',$page_data);
	}
	public function edit_exclusion_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$exclusion_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->update_record('tour_exclusions_master',$exclusion_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/exclusions_list/');
		
	}
	public function delete_exclusion($id) {
	  $return = $this->tours_model->record_delete('tour_exclusions_master',$id);
	  if($return){redirect('tours/exclusions_list');} 
	  else { echo $return;} 
	}
	public function activation_exclusion($id,$status) {
	  $return = $this->tours_model->record_activation('tour_exclusions_master',$id,$status);

	  if($return){redirect('tours/exclusions_list');} 
	  else { echo $return;} 
	}
	
	/* Highlights Master contents*/
	public function highlight_list(){
		$highlight_list = $this->tours_model->highlight_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['highlight_list'] = $highlight_list;
		//debug($page_data);exit;
		$this->template->view('tours/highlight_list',$page_data);
	}
	
	public function highlight_data_save() {
	  $data = $this->input->post();
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$highlight_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_highlight_master',$highlight_data);
		
		
	  if($status)
		{   redirect('tours/highlight_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_highlight_data($id) {
	  $highlight_details = $this->tours_model->highlight_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['highlight_details'] = $highlight_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_highlight',$page_data);
	}
	public function edit_highlight_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$highlight_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->update_record('tour_highlight_master',$highlight_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/highlight_list/');
		
	}
	public function delete_highlight($id) {
	  $return = $this->tours_model->record_delete('tour_highlight_master',$id);
	  if($return){redirect('tours/highlight_list');} 
	  else { echo $return;} 
	}
	public function activation_highlight($id,$status) {
	  $return = $this->tours_model->record_activation('tour_highlight_master',$id,$status);

	  if($return){redirect('tours/highlight_list');} 
	  else { echo $return;} 
	}
	
	/* cancellation Master contents*/
	public function cancellation_list(){
		$cancellation_list = $this->tours_model->cancellation_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['cancellation_list'] = $cancellation_list;
		//debug($page_data);exit;
		$this->template->view('tours/cancellation_list',$page_data);
	}
	
	public function cancellation_data_save() {
	  $data = $this->input->post();
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$cancellation_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_cancellation_policy_master',$cancellation_data);
		
		
	  if($status)
		{   redirect('tours/cancellation_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_cancellation_data($id) {
	  $cancellation_details = $this->tours_model->cancellation_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['cancellation_details'] = $cancellation_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_cancellation',$page_data);
	}
	public function edit_cancellation_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$cancellation_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->update_record('tour_cancellation_policy_master',$cancellation_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/cancellation_list/');
		
	}
	public function delete_cancellation($id) {
	  $return = $this->tours_model->record_delete('tour_cancellation_policy_master',$id);
	  if($return){redirect('tours/cancellation_list');} 
	  else { echo $return;} 
	}
	public function activation_cancellation($id,$status) {
	  $return = $this->tours_model->record_activation('tour_cancellation_policy_master',$id,$status);

	  if($return){redirect('tours/cancellation_list');} 
	  else { echo $return;} 
	}
	
	/* Trip notes Master contents*/
	public function trip_notes_list(){
		$trip_note_list = $this->tours_model->trip_note_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['trip_note_list'] = $trip_note_list;
		//debug($page_data);exit;
		$this->template->view('tours/trip_note_list',$page_data);
	}
	
	public function trip_note_data_save() {
	  $data = $this->input->post();
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		
		$trip_note_data = array(
			'desctiption' =>$des,
			'country' =>$data['tour_country'],
			
		);
		//$country=$data['tour_country'];
	//	$sql="INSERT INTO tour_trip_notes_master (desctiption, country) VALUES ('".$des."', '".$country."')";
		//$status=$this->db->query ( $sql );
		//echo $this->db->last_query();
		//exit;
		$status=$this->custom_db->insert_record('tour_trip_notes_master',$trip_note_data);
		//$status=$this->db->insert('tour_trip_notes_master',$trip_note_data);
		//debug($status);exit;
	  if($status)
		{   redirect('tours/trip_notes_list'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_trip_note_data($id) {
	  $trip_note_details = $this->tours_model->trip_note_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['trip_note_details'] = $trip_note_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_trip_note',$page_data);
	}
	public function edit_trip_note_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$des= str_replace("selected","chosen",$data['description']);
		$des= str_replace("Selected","chosen",$data['description']);
		$des= str_replace("select","choose",$des);
		$des= str_replace("Select","choose",$des);
		$trip_note_data = array(
			'desctiption' => sql_injection($des),
			'country' => sql_injection($data['tour_country']),
			
		);
		debug($trip_note_data);
//exit;		
$status=$this->custom_db->update_record('tour_trip_notes_master',$trip_note_data,array('id'=>$id));
//echo $this->db->last_query();exit;
		//echo $this->db->last_query();exit;
		  redirect('tours/trip_notes_list/');
		
	}
	public function delete_trip_note($id) {
	  $return = $this->tours_model->record_delete('tour_trip_notes_master',$id);
	  if($return){redirect('tours/trip_notes_list');} 
	  else { echo $return;} 
	}
	public function activation_trip_note($id,$status) {
	  $return = $this->tours_model->record_activation('tour_trip_notes_master',$id,$status);

	  if($return){redirect('tours/trip_notes_list');} 
	  else { echo $return;} 
	}
	
	
	
	/* optional_tour_list Master contents*/
	public function optional_tour_list(){
		$trip_note_list = $this->tours_model->optional_tour_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['trip_note_list'] = $trip_note_list;
		//debug($page_data);exit;
		$this->template->view('tours/optional_tour_list',$page_data);
	}
	
	public function optional_tour_list_save() {
	  $data = $this->input->post();
		
		$trip_note_data = array(
			'tour_name' => sql_injection($data['tour_name']),
			'country' => sql_injection($data['tour_country']),
			'city' => sql_injection($data['tour_city']),
			'adult_price' => sql_injection($data['adult_price']),
			'child_price' => sql_injection($data['child_price']),
			'infant_price' => sql_injection($data['infant_price'])
			
		);
		//debug($trip_note_data);exit;
		$status=$this->custom_db->insert_record('tour_optional_tours_master',$trip_note_data);
		
		
	  if($status)
		{   redirect('tours/optional_tour_list'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_optional_tour_data($id) {
	  $trip_note_details = $this->tours_model->optional_tour_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['trip_note_details'] = $trip_note_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	  $page_data['tour_city'] = $this->tours_model->ajax_tours_country($page_data['trip_note_details']['country']);
	 
		
	  $this->template->view('tours/edit_optional_tour_data',$page_data);
	}
	public function optional_tour_note_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$trip_note_data = array(
			'tour_name' => sql_injection($data['tour_name']),
			'country' => sql_injection($data['tour_country']),
			'city' => sql_injection($data['tour_city']),
			'adult_price' => sql_injection($data['adult_price']),
			'child_price' => sql_injection($data['child_price']),
			'infant_price' => sql_injection($data['infant_price'])
			
		);
		////debug($trip_note_data);
//exit;		
$status=$this->custom_db->update_record('tour_optional_tours_master',$trip_note_data,array('id'=>$id));
//echo $this->db->last_query();exit;
		//echo $this->db->last_query();exit;
		  redirect('tours/optional_tour_list/');
		
	}
	public function delete_optional_tour($id) {
	  $return = $this->tours_model->record_delete('tour_optional_tours_master',$id);
	  if($return){redirect('tours/optional_tour_list');} 
	  else { echo $return;} 
	}
	public function activation_optional_tour($id,$status) {
	  $return = $this->tours_model->record_activation('tour_optional_tours_master',$id,$status);

	  if($return){redirect('tours/optional_tour_list');} 
	  else { echo $return;} 
	}
	/* terms and conditions */
	public function terms_conditions_list(){
		$trip_note_list = $this->tours_model->terms_conditions_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['trip_note_list'] = $trip_note_list;
		//debug($page_data);exit;
		$this->template->view('tours/terms_conditions_list',$page_data);
	}
	
	public function terms_conditions_data_save() {
	  $data = $this->input->post();
		
		$trip_note_data = array(
			'desctiption' => sql_injection($data['description']),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_terms_conditions_master',$trip_note_data);
		
		
	  if($status)
		{   redirect('tours/terms_conditions_list'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_terms_conditions_data($id) {
	  $trip_note_details = $this->tours_model->terms_conditions_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['trip_note_details'] = $trip_note_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_terms_conditions',$page_data);
	}
	public function edit_terms_conditions_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$trip_note_data = array(
			'desctiption' => sql_injection($data['description']),
			'country' => sql_injection($data['tour_country']),
			
		);
		//debug($trip_note_data);
//exit;		
$status=$this->custom_db->update_record('tour_terms_conditions_master',$trip_note_data,array('id'=>$id));
//echo $this->db->last_query();exit;
		//echo $this->db->last_query();exit;
		  redirect('tours/terms_conditions_list/');
		
	}
	public function delete_terms_conditions($id) {
	  $return = $this->tours_model->record_delete('tour_terms_conditions_master',$id);
	  if($return){redirect('tours/terms_conditions_list');} 
	  else { echo $return;} 
	}
	public function activation_terms_conditions($id,$status) {
	  $return = $this->tours_model->record_activation('tour_terms_conditions_master',$id,$status);

	  if($return){redirect('tours/terms_conditions_list');} 
	  else { echo $return;} 
	}
	
	/* Visa Procedures List */
	public function visa_procedures_list(){
		$trip_note_list = $this->tours_model->visa_procedures_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['trip_note_list'] = $trip_note_list;
		//debug($page_data);exit;
		$this->template->view('tours/visa_procedures_list',$page_data);
	}
	
	public function visa_procedures_data_save() {
	  $data = $this->input->post();
		
		$trip_note_data = array(
			'desctiption' => sql_injection($data['description']),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_visa_procedure_master',$trip_note_data);
		
		
	  if($status)
		{   redirect('tours/visa_procedures_list'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_visa_procedures($id) {
	  $trip_note_details = $this->tours_model->visa_procedures_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['trip_note_details'] = $trip_note_details[0];
	  $page_data['tour_country']      = $this->tours_model->tour_country();
	 
		
	  $this->template->view('tours/edit_visa_procedures',$page_data);
	}
	public function edit_visa_procedures_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$trip_note_data = array(
			'desctiption' => sql_injection($data['description']),
			'country' => sql_injection($data['tour_country']),
			
		); 
		//debug($trip_note_data);
//exit;		
$status=$this->custom_db->update_record('tour_visa_procedure_master',$trip_note_data,array('id'=>$id));
//echo $this->db->last_query();exit;
		//echo $this->db->last_query();exit;
		  redirect('tours/visa_procedures_list/');
		
	}
	public function delete_visa_procedures($id) {
	  $return = $this->tours_model->record_delete('tour_visa_procedure_master',$id);
	  if($return){redirect('tours/visa_procedures_list');} 
	  else { echo $return;} 
	}
	public function activation_visa_procedures($id,$status) {
	  $return = $this->tours_model->record_activation('tour_visa_procedure_master',$id,$status);

	  if($return){redirect('tours/visa_procedures_list');} 
	  else { echo $return;} 
	}
	public function ajax_tours_highlights(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_highlights($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_inclusions(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_inclusions($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_exclusions(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_exclusions($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_terms_conditions(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_terms_conditions($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_cancelation_policy(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_cancelation_policy($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_trip_note(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_trip_note($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_visa_procedures(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_visa_procedures($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['desctiption'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_b2b_payment_policy(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_payment_policy($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['b2b_description'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tours_b2c_payment_policy(){
		$data = $this->input->post();
		//debug($data);exit;
		$text='';
		foreach($data['tours_country'] as $country_val){
			$tours_hotel = $this->tours_model->ajax_tours_payment_policy($country_val);  
			foreach($tours_hotel as $high_key =>$high_val){
				$text.='<h2><strong>'.$high_val['name'].'</strong></h2><br/>'.$high_val['b2c_description'].'<br/>';
			}
		}
		echo $text;
	}
	public function ajax_tour_send_verification(){
		$data = $this->input->post();
		
		$tour_id        = ($data['tour_id']);
  
		$query_1  = "select * from tours_itinerary_dw where tour_id=  ".$tour_id." and banner_image != ''";        
		$num_ajax_tour_publish_1 = $this->tours_model->ajax_tour_publish_1($query_1);
		$query3 = "select * from tour_price_management where tour_id=".$tour_id; 
		$num_ajax_tour_publish_3 = $this->tours_model->ajax_tour_publish_1($query3);
		$message = array();
		if($num_ajax_tour_publish_1 == 0)
		{ 
			$message['first'][]= "Sorry! Please upload images";
		}
		if($num_ajax_tour_publish_3 == 0)
		{ 
			$message['first'][]= "Unable to send the package for verification  as the price info is missing. Please add the price information for the package using Price Management Option";
		}
		if($num_ajax_tour_publish_1 !=0 && $num_ajax_tour_publish_3!=0 )
		{
			$query  = "update tours set package_status='VERIFICATION',status_update_date = '".date('Y-m-d H:i:s')."' where id='$tour_id'";
			$return = $this->tours_model->query_run($query);
			//$status=$this->custom_db->update_record('tours',array('package_status'=>'VERIFICATION'),array('id'=>$data['tour_id']));
			if($return)
			{
				$message['first'][]= "Thanks! This tour is successfully sent for verification now.";
			}else{
				$message['first'][]= "Sorry|| some techinal .";
			}
		}
		//debug($message);
		echo json_encode($message); exit(); 
	}
	public function ajax_tour_verified(){
		$data = $this->input->post();
		$tour_id        = ($data['tour_id']);
  
		$query_1  = "select * from tours_itinerary_dw where tour_id=  ".$tour_id." and banner_image != ''"; 
		$num_ajax_tour_publish_1 = $this->tours_model->ajax_tour_publish_1($query_1);
		$query3 = "select * from tour_price_management where tour_id=".$tour_id; 
		$num_ajax_tour_publish_3 = $this->tours_model->ajax_tour_publish_1($query3);
		$message = array();
		if($num_ajax_tour_publish_1 == 0)
		{ 
			$message['first'][]= "Sorry! Please upload images";
		}
		if($num_ajax_tour_publish_3 == 0)
		{ 
			$message['first'][]= "Unable to send the package to publish  as the price info is missing. Please add the price information for the package using Price Management Option";
		}
		if($num_ajax_tour_publish_1 !=0 && $num_ajax_tour_publish_3!=0 )
		{
			$query  = "update tours set package_status='VERIFIED',status_update_date = '".date('Y-m-d H:i:s')."' where id='$tour_id'";
			$return = $this->tours_model->query_run($query);
			//$status=$this->custom_db->update_record('tours',array('package_status'=>'VERIFICATION'),array('id'=>$data['tour_id']));
			if($return)
			{
				$message['first'][]= "Thanks! This tour is successfully sent for publish now.";
			}else{
				$message['first'][]= "Sorry!! some techinal .";
			}
		}
		//debug($message);
		echo json_encode($message); exit(); 
		
		
	}
	public function ajax_tour_published(){
		$data = $this->input->post();
		$status=$this->custom_db->update_record('tours',array('package_status'=>'PUBLISHED'),array('id'=>$data['tour_id']));
		
	}
	
	public function check_unique_data(){
		$data = $this->input->post();
		//echo $data['name'];echo $data['table'];exit;
		if($data['table']=='tour_type'){
			$result=$this->custom_db->single_table_records($data['table'],'*',array('tour_type_name'=>$data['name']));		
		}else if($data['table']=='tours_country' || $data['table']=='tours_continent'){
			$result=$this->custom_db->single_table_records($data['table'],'*',array('name'=>$data['name']));	
		}else if($data['table']=='tours_city'){
			$result=$this->custom_db->single_table_records($data['table'],'*',array('CityName'=>$data['name']));	
		}
			//echo $this->db->last_query();exit;
		$result = count($result['data']);
		echo $result;
	}
	public function unlink_itinerary_image($id,$img){
	//	echo $id, $img;exit;
		$ite_data=$this->custom_db->single_table_records('tours_itinerary_dw','banner_image',array('id'=>$id))['data'][0];
		//debug($ite_data);exit;
		$bnr_img=str_replace($img,"",$ite_data['banner_image']);
		$this->custom_db->update_record('tours_itinerary_dw',array('banner_image'=>$bnr_img),array('id'=>$id));
		$image_location = '../extras/custom/TMX1512291534825461/images/'.$img;
		//echo $image_location;
			if(file_exists($image_location)){
				echo $image_location;
				unlink($image_location);
			}
			exit;
	}
	
	public function payment_policy_list(){
		$payment_policy_list = $this->tours_model->payment_policy_list();
		$page_data['tour_country']      = $this->tours_model->tour_country();
		$page_data['payment_policy_list'] = $payment_policy_list;
		//debug($page_data);exit;
		$this->template->view('tours/tours_payment_policy',$page_data);
	}
	
	public function payment_policy_data_save() {
		$data = $this->input->post();
	//	debug($data);exit;
		$b2b_des= str_replace("selected","chosen",$data['b2b_description']);
		$b2c_des= str_replace("Selected","chosen",$data['b2c_description']);
		$b2b_des= str_replace("select","choose",$b2b_des);
		$b2c_des= str_replace("Select","choose",$b2c_des);
		$cancellation_data = array(
			'b2b_description' => sql_injection($b2b_des),
			'b2c_description' => sql_injection($b2c_des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->insert_record('tour_payment_policy_master',$cancellation_data);
		
		
	  if($status)
		{   redirect('tours/payment_policy_list/'); }
	  else
		{ echo $return; exit; }              
	}
	
	public function edit_payment_policy_data($id) {
	  $cancellation_details = $this->tours_model->payment_policy_details($id);
		//debug($hotel_details); //exit;      
	  $page_data['cancellation_details'] = $cancellation_details[0];
	  $page_data['tour_country']      	 = $this->tours_model->tour_country();
	 
		//debug($page_data);exit;
	  $this->template->view('tours/edit_payment_policy',$page_data); 
	}
	public function edit_payment_policy_save(){
		$data = $this->input->post();
		//debug($data);exit;
		$id = $data['id'];
		$b2b_des= str_replace("selected","chosen",$data['b2b_description']);
		$b2c_des= str_replace("Selected","chosen",$data['b2c_description']);
		$b2b_des= str_replace("select","choose",$b2b_des);
		$b2c_des= str_replace("Select","choose",$b2c_des);
		$cancellation_data = array(
			'b2b_description' => sql_injection($b2b_des),
			'b2c_description' => sql_injection($b2c_des),
			'country' => sql_injection($data['tour_country']),
			
		);
	
		$status=$this->custom_db->update_record('tour_payment_policy_master',$cancellation_data,array('id'=>$id));
		//echo $this->db->last_query();exit;
		  redirect('tours/payment_policy_list/');
		
	}
	public function delete_payment_policy($id) {
	  $return = $this->tours_model->record_delete('tour_payment_policy_master',$id);
	  if($return){redirect('tours/payment_policy_list');} 
	  else { echo $return;} 
	}
	public function activation_payment_policy($id,$status) {
	  $return = $this->tours_model->record_activation('tour_payment_policy_master',$id,$status);

	  if($return){redirect('tours/payment_policy_list');} 
	  else { echo $return;} 
	}
	
	public function set_top_deal($id,$status){
	
		$this->custom_db->update_record('tours',array('top_deal'=>$status),array('id'=>$id));
		//echo $this->db->last_query();
	}
	function custom_enquiry_report(){
		
		$page_data['table_data'] = $this->custom_db->single_table_records('custom_package_enquiry','*',array(),0,100000000,array('id'=>'desc'))['data']; 
		$country_list=$this->tours_model->tours_country_name();
		$city_list=$this->tours_model->tours_city_name();
		
		foreach($page_data['table_data'] as $enq_key =>$enq_val){
			$page_data['table_data'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['table_data'][$enq_key]['country_name']=''; 
			$page_data['table_data'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0]; 
			foreach($country_array as $c_arr){
				$page_data['table_data'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		//debug($page_data);exit;
		$page_data['total_rows']=count($page_data['table_data']);
		 $page_data['package_manager']    = $this->tours_model->get_package_manager_list();
		$this->template->view('tours/custom_enquiry_report', $page_data);
	}
	function assign_enquiry($user,$enquiry,$module){
		
		$enq_details=$this->custom_db->single_table_records('tours_enquiry','status',array('id'=>$enquiry))['data'][0];
		//echo $this->db->last_query();
		//debug($enq_details);exit;
		if($enq_details['status']=='PENDING' || $enq_details['status']==''){
			$status='INPROGRESS';
		}else{
			$status=$enq_details['status'];
		}
		
		
		
		$res = $this->custom_db->update_record('tours_enquiry',array('alloted_to'=>$user,'status'=>$status),array('id'=>$enquiry));
		
		echo $this->db->last_query();
		
		if($res){
			redirect('tours/tours_enquiry/'.$module);
			} 
		else { 
			echo $res;
		} 
	}
	function assign_custom_enquiry($user,$enquiry){
		$enq_details=$this->custom_db->single_table_records('custom_package_enquiry','status',array('id'=>$enquiry))['data'][0];
		//echo $this->db->last_query();
		//debug($enq_details);exit;
		if($enq_details['status']=='PENDING' || $enq_details['status']==''){
			$status='INPROGRESS';
		}else if($user == 0){
			$status='PENDING';
		}else{
			$status=$enq_details['status'];
		}
		$res = $this->custom_db->update_record('custom_package_enquiry',array('alloted_to'=>$user,'status'=>$status),array('id'=>$enquiry));
		echo $this->db->last_query();
		
		if($res){
			redirect('tours/custom_enquiry_report');
			} 
		else { 
			echo $res;
		} 
	}
	function assigned_tours_enquiry($module){
		//echo $this->entity_user_type;
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id,
				'tour_id' => trim($this->input->get('phone')), 
				'phone' => trim($this->input->get('phone')),
				'email' => trim($this->input->get('email')),
				'module' => $module
			);	
		}else{
			$condition = array(
				'tour_id' => trim($this->input->get('phone')), 
				'phone' => trim($this->input->get('phone')),
				'email' => trim($this->input->get('email')),
				'module' => $module
			);
			
		}
		//debug($condition);exit("vzxc");
		$total_records = $this->tours_model->assigned_tours_enquiry($condition);
		$tours_enquiry = $this->tours_model->assigned_tours_enquiry($condition);
		$page_data['tours_enquiry'] = $tours_enquiry['tours_enquiry'];
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
	  //debug($page_data);
		$this->template->view('tours/assigned_tours_enquiry',$page_data);
	}
	function assigned_custom_enquiries(){
		
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id
			);	
		}
		//exit("vzxc");
		$total_records = $this->tours_model->assigned_custom_enquiry($condition);
		$enquiry = $this->tours_model->assigned_custom_enquiry($condition);
		$country_list=$this->tours_model->tours_country_name();
		$city_list=$this->tours_model->tours_city_name();
		
		foreach($enquiry['tours_enquiry'] as $enq_key =>$enq_val){
			$page_data['table_data'][$enq_key] = $enq_val; 
			$page_data['table_data'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['table_data'][$enq_key]['country_name']=''; 
			$page_data['table_data'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0]; 
			foreach($country_array as $c_arr){
				$page_data['table_data'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
	  //debug($page_data);
		$this->template->view('tours/assigned_custom_enquiry',$page_data);
	}
	function tour_bookings($module,$offset=0)
	{
	
		$page_data = array();
		$condition = array(
		'created_by'=>$module
		);
		//debug($condition);
		$this->load->library('booking_data_formatter');
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.pnr like "%'.$filter_report_data.'%")';
			$total_records = $this->tours_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->tours_model->filter_booking_report($search_filter_condition, false, $offset, RECORDS_RANGE_2);
		} else {
			$total_records = $this->tours_model->booking($condition, true);
			$table_data = $this->tours_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}
		//debug($table_data);
	 	//debug(count($table_data['data']['booking_details']));exit;
		$table_data = $this->booking_data_formatter->format_package_booking_data($table_data, 'b2b');
	//	debug(count($table_data['data']['booking_details']));
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/bus/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = count($table_data['data']['booking_details']);
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		
		$page_data['customer_email'] = $this->entity_email;
		$page_data['user_details'] = $this->custom_db->single_table_records('user','*',array('user_id'=>$this->entity_user_id))['data'][0];
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
		//debug($page_data);
		$this->template->view('tours/tour_bookings', $page_data);
	}	
	function assigned_tours_bookings($module,$offset=0)
	{
		$page_data = array();
		$condition = array();
		$condition = array(
		'created_by'=>$module
		);
		$this->load->library('booking_data_formatter');
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.pnr like "%'.$filter_report_data.'%")';
			$total_records = $this->tours_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->tours_model->filter_booking_report($search_filter_condition, false, $offset, RECORDS_RANGE_2);
		} else {
			$total_records = $this->tours_model->assigned_booking($condition, true);
			$table_data = $this->tours_model->assigned_booking($condition, false, $offset);
		}
		//echo $this->db->last_query();exit;
	 //	debug($table_data);exit;
		$table_data = $this->booking_data_formatter->format_package_booking_data($table_data, 'b2b');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/tours/assigned_tours_bookings/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = count($table_data['data']['booking_details']);
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['user_details'] = $this->custom_db->single_table_records('user','*',array('user_id'=>$this->entity_user_id))['data'][0];
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
		
		//debug($page_data);exit;
		$this->template->view('tours/assigned_tours_bookings', $page_data);
	}
	function assign_package_bookinga($user,$enquiry,$module){
		//echo $user;
		//echo $enquiry;exit;
		$res = $this->custom_db->update_record('tour_booking_details',array('alloted_to'=>$user),array('enquiry_reference_no'=>$enquiry));
		if($res){
			redirect('tours/tour_bookings/'.$module);
			} 
		else { 
			echo $res;
		} 
	}
	function upload_tour_details(){
		//debug($_FILES);
		$post_data = $this->input->post();
		//debug($post_data);exit;
		$hotel_voucher_arr = explode(',',$post_data['old_hotel_voucher']); 
		foreach($hotel_voucher_arr as $hotel_voucher_img){
			$hotel_voucher .= $hotel_voucher_img.',';
		}
		$flight_ticket_arr = explode(',',$post_data['old_flight_ticket']); 
		foreach($flight_ticket_arr as $flight_ticket_img){
			$flight_ticket .= $flight_ticket_img.',';
		}
		$visa_arr = explode(',',$post_data['old_visa']); 
		foreach($visa_arr as $visa_img){
			$visa .= $visa_img.',';
		}
		$final_itinery_arr = explode(',',$post_data['old_final_itinery']); 
		foreach($final_itinery_arr as $final_itinery_img){
			$final_itinery .= $final_itinery_img.',';
		}
		if(!empty($_FILES['hotel_voucher']['name'][0])){
			for($i=0; $i< count($_FILES['hotel_voucher']['name']); $i++){
				$hotel_voucher.= $this->tours_model->upload_image_lgm($_FILES,'hotel_voucher','',$i,$post_data['app_ref']);	
				$hotel_voucher.= ",";
			}
		}
		
		if(!empty($_FILES['flight_ticket']['name'][0])){
			for($i=0; $i< count($_FILES['flight_ticket']['name']); $i++){
				$flight_ticket.= $this->tours_model->upload_image_lgm($_FILES,'flight_ticket','',$i,$post_data['app_ref']);	
				$flight_ticket.= ",";
			}
		}
		if(!empty($_FILES['visa']['name'][0])){
			for($i=0; $i< count($_FILES['visa']['name']); $i++){
				$visa.= $this->tours_model->upload_image_lgm($_FILES,'visa','',$i,$post_data['app_ref']);	
				$visa.= ",";
			}
		}
		if(!empty($_FILES['final_itinary']['name'][0])){
			for($i=0; $i< count($_FILES['final_itinary']['name']); $i++){
				$final_itinery.= $this->tours_model->upload_image_lgm($_FILES,'final_itinary','',$i,$post_data['app_ref']);	
				$final_itinery.= ",";
			}
		}
		$hotel_voucher = rtrim($hotel_voucher,",");
		$flight_ticket = rtrim($flight_ticket,",");
		$visa = rtrim($visa,",");
		$final_itinery = rtrim($final_itinery,",");
		//echo $hotel_voucher.'|'.$flight_ticket.'|'.$visa.'|'.$final_itinery.'}}}}}}}';debug($post_data);exit;
		$hotel_voucher_image =$hotel_voucher;
		$flight_ticket_image =$flight_ticket;
		$visa_image =$visa;
		$final_itinery_image =$final_itinery;
		if($post_data['update_table']=='tour_booking_details'){
			$res = $this->custom_db->update_record('tour_booking_details',array('hotel_voucher'=>$hotel_voucher_image,'flight_ticket'=>$flight_ticket_image,'visa'=>$visa_image,'final_itinery'=>$final_itinery_image),array('enquiry_reference_no'=>$post_data['app_ref']));
			if($res){
				redirect('tours/assigned_tours_bookings/'.$post_data['module']);
			}else { 
				redirect('tours/assigned_tours_bookings/'.$post_data['module']);
				//redirect('tours/assigned_tours_bookings');
			} 
		}else if($post_data['update_table']=='tours_enquiry'){
			$res = $this->custom_db->update_record('tours_enquiry',array('hotel_voucher'=>$hotel_voucher_image,'flight_ticket'=>$flight_ticket_image,'visa'=>$visa_image,'final_itinery'=>$final_itinery_image),array('enquiry_reference_no'=>$post_data['app_ref']));
			if($res){
				redirect('tours/confirmed_enq_list/'.$post_data['module']);
			}else { 
				redirect('tours/confirmed_enq_list/'.$post_data['module']);
				//redirect('tours/assigned_tours_bookings');
			} 
		}else if($post_data['update_table']=='custom_package_enquiry'){
			$res = $this->custom_db->update_record('custom_package_enquiry',array('hotel_voucher'=>$hotel_voucher_image,'flight_ticket'=>$flight_ticket_image,'visa'=>$visa_image,'final_itinery'=>$final_itinery_image),array('enquiry_reference_no'=>$post_data['app_ref']));
			if($res){
				redirect('tours/confirmed_custom_enq_list');
			}else { 
				redirect('tours/confirmed_custom_enq_list');
				//redirect('tours/assigned_tours_bookings');
			} 
		}
		
		if($res){
			$type=explode('-',$post_data['app_ref']);
			//$this->session->set_flashdata("msg", "<div class='alert alert-success'>Payment link sent to agent successfully.</div>");
			if(substr($post_data['app_ref'],0,2)=='PB'){
				redirect('tours/assigned_tours_bookings/'.$post_data['module']);
			}else if(substr($post_data['app_ref'],0,2)=='PT'){
				redirect('tours/confirmed_enq_list/'.$post_data['module']);
			}else{
				redirect('tours/confirmed_custom_enq_list'); 
			} 
			
		} 
		else { 
			echo $this->db->last_query();
			//redirect('tours/assigned_tours_bookings');
		} 
		
	}
	public function unlink_tour_uploads($id,$img,$type,$upload_for=''){
		//echo $id.'|'.$img.'|'.$type;
		$ite_data=$this->custom_db->single_table_records('tour_booking_details',$type,array('app_reference'=>$id))['data'][0];
		//debug($ite_data);
		$bnr_img=str_replace(','.$img,"",$ite_data[$type]);
		//debug($bnr_img);exit;
		if($upload_for=='tour_booking_details'){
			$this->custom_db->update_record($upload_for,array($type=>$bnr_img),array('app_reference'=>$id));			
		}else{
			$this->custom_db->update_record($upload_for,array($type=>$bnr_img),array('enquiry_reference_no'=>$id));	
		}
		$image_location = '../extras/custom/TMX1512291534825461/images/tour_uploads/'.$img;
		//echo $image_location;
			if(file_exists($image_location)){
				echo $image_location;
				unlink($image_location);
			}
		//echo $this->db->last_query();exit;
		//	exit;
	}
	public function payment_slab(){
		$post_data = $this->input->post();
		$payment_history=$this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$post_data['ref_no']))['data'][0];
		$payment_GW_details=$this->custom_db->single_table_records('payment_gateway_details','*',array('app_reference'=>$post_data['ref_no']))['data'][0];
		$request_param=(array) json_decode($payment_GW_details['request_params']);
		//debug($payment_GW_details);
		//debug($request_param);exit;
		$payment_slab =array(
			'enquiry_reference_no'=> $payment_history['enquiry_reference_no'],
			'total_trip_cost'=> $payment_history['total_trip_cost'],
			'paid_amount'=> $post_data['amount'],
			'remaining'=> $post_data['pending_amount'],
			'payment_mode'=> $payment_history['payment_mode'],
			'payment_type'=> 'balance_pay',
			'created_by'=> $payment_history['created_by'],
			'payment_date'=> date('d-m-Y H:i:s'),
			'module'=> $payment_history['module'],
			'status'=> 'PENDING',
			'description' =>$post_data['pay_description'],
		);
		
		$status=$this->custom_db->insert_record('tour_payment_slab_details',$payment_slab);
		$request_params = array('txnid' => $payment_GW_details['app_reference'],
				'booking_fare' => $post_data['amount'],
				'convenience_amount' => 0,
				'promocode_discount' => 0,
				'firstname' => $request_param['firstname'],
				'email'=>  $request_param['email'],
				'phone'=> $request_param['phone'],
				'productinfo'=>'PACKAGE_BALANCE_AMOUNT');
		$payment_details =array(
			'domain_origin'=> $payment_GW_details['domain_origin'],
			'app_reference'=> $payment_GW_details['app_reference'],
			'status'=>'pending',
			'amount'=>$post_data['amount'],
			'currency'=> $payment_GW_details['currency'],
			'currency_conversion_rate'=> $payment_GW_details['currency_conversion_rate'],
			'request_params'=> json_encode($request_params),
			'response_params'=> '',
			'refund_params'=> $payment_GW_details['refund_params'],
			'transaction_owner_id'=> $payment_GW_details['transaction_owner_id'],
			'pg_name'=>'PAYTM',
			'payment_mode'=> 'paytm_wallet',
			'payment_history_ref'=>$status['insert_id'],
			'created_datetime'=>  date('Y-m-d H:i:s'),
		);
		$payment_id=$this->custom_db->insert_record('payment_gateway_details',$payment_details);
		//echo $payment_id; echo $this->db->last_query();exit;
		$agent_earning =$post_data['amount'];
		$remarks = "Package balance request";
		$crdit_towards = "Package booking";
		$this->notification->package_balance($payment_history['created_by'], $payment_history['enquiry_reference_no'], $crdit_towards, $agent_earning, 0, $remarks);
		if($payment_id){
			$this->session->set_flashdata("msg", "<div class='alert alert-success'>Payment link sent to agent successfully.</div>");
			redirect('tours/assigned_tours_bookings/'.$post_data['module']);
		}
	}
	public function enq_payment_slab(){
		$post_data = $this->input->post();
		//debug($post_data);exit;
		$payment_history=$this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$post_data['ref_no']))['data'][0];
		$user_details=$this->custom_db->single_table_records('user','*',array('user_id'=>$post_data['created_by']))['data'][0];
		$payment_GW_details=$this->custom_db->single_table_records('payment_gateway_details','*',array('app_reference'=>$post_data['ref_no']))['data'][0];
		$request_param=(array) json_decode($payment_GW_details['request_params']);
		//debug($payment_GW_details);
		//debug($request_param);exit;
		if(!empty($payment_history)){
			$payment_slab =array(
				'enquiry_reference_no'=> $payment_history['enquiry_reference_no'],
				'total_trip_cost'=> $payment_history['total_trip_cost'],
				'paid_amount'=> $post_data['amount'],
				'remaining'=> $post_data['pending_amount'],
				'description' =>$post_data['pay_description'],
				'status'=> 'PENDING',
				'payment_mode'=> $payment_history['payment_mode'],
				'payment_type'=> 'balance_pay',
				'created_by'=> $payment_history['created_by'],
				'payment_date'=> date('d-m-Y H:i:s'),
				'module'=> $payment_history['module'],
				'status'=> 'PENDING',
				
			);		
		}else{
			$payment_slab =array(
				'enquiry_reference_no'=> $post_data['ref_no'],
				'total_trip_cost'=> $post_data['package_cost'],
				'paid_amount'=> $post_data['amount'],
				'remaining'=> $post_data['pending_amount'],
				'description' =>$post_data['pay_description'],
				'status'=> 'PENDING',
				'payment_mode'=> 'WALLET',
				'payment_type'=> 'enquiry_pay',
				'created_by'=> $post_data['created_by'],
				'payment_date'=> date('d-m-Y H:i:s'),
				'module'=> 'b2b',
				'status'=> 'PENDING',
				
			);		
			
		}
		
		$status=$this->custom_db->insert_record('tour_payment_slab_details',$payment_slab);
		
		
		
		if(!empty($payment_GW_details)){
			$request_params = array(
				'txnid' => $payment_GW_details['app_reference'],
				'booking_fare' => $post_data['amount'],
				'convenience_amount' => 0,
				'promocode_discount' => 0,
				'firstname' => $request_param['firstname'],
				'email'=>  $request_param['email'],
				'phone'=> $request_param['phone'],
				'productinfo'=>'PACKAGE_BALANCE_AMOUNT');
			$payment_details =array(
				'domain_origin'=> $payment_GW_details['domain_origin'],
				'app_reference'=> $payment_GW_details['app_reference'],
				'status'=>'pending',
				'amount'=>$post_data['amount'],
				'currency'=> $payment_GW_details['currency'],
				'currency_conversion_rate'=> $payment_GW_details['currency_conversion_rate'],
				'request_params'=> json_encode($request_params),
				'response_params'=> '',
				'refund_params'=> $payment_GW_details['refund_params'],
				'transaction_owner_id'=> $payment_GW_details['transaction_owner_id'],
				'pg_name'=>'PAYTM',
				'payment_mode'=> 'paytm_wallet',
				'payment_history_ref'=>$status['insert_id'],
				'created_datetime'=>  date('Y-m-d H:i:s'),
			);	
		}else{
			$request_params = array(
				'txnid' => $post_data['ref_no'],
				'booking_fare' => $post_data['amount'],
				'convenience_amount' => 0,
				'promocode_discount' => 0,
				'firstname' => $user_details['firstname'],
				'email'=> 'mailtosamisal@gmail.com',
				'phone'=> $user_details['phone'],
				'productinfo'=>'PACKAGE_ENQUIRY_AMOUNT');
			$payment_details =array(
				'domain_origin'=>'1',
				'app_reference'=> $post_data['ref_no'],
				'status'=>'pending',
				'amount'=>$post_data['amount'],
				'currency'=> 'INR',
				'currency_conversion_rate'=> 1,
				'request_params'=> json_encode($request_params),
				'response_params'=> '',
				'refund_params'=> 'No refund initiated',
				'transaction_owner_id'=> $post_data['created_by'],
				'pg_name'=>'PAYTM',
				'payment_mode'=> 'paytm_wallet',
				'payment_history_ref'=>$status['insert_id'],
				'created_datetime'=>  date('Y-m-d H:i:s'),
			);
		}
		
		$payment_id=$this->custom_db->insert_record('payment_gateway_details',$payment_details);
		//echo $payment_id; echo $this->db->last_query();exit;
		$agent_earning =$post_data['amount'];
		$remarks = "Package balance request";
		$crdit_towards = "Package booking";
		$this->notification->package_balance($post_data['created_by'], $post_data['ref_no'], $crdit_towards, $agent_earning, 0, $remarks);
		if($payment_id){
			$type=explode('-',$post_data['ref_no']);
			$this->session->set_flashdata("msg", "<div class='alert alert-success'>Payment link sent to agent successfully.</div>");
			if($type[0]=='PT'){
				redirect('tours/confirmed_enq_list/'.$post_data['module']);	
			}else{
				redirect('tours/confirmed_custom_enq_list/'.$post_data['module']);
			}
		}
	}
	function upload_enq_quot(){
		//debug($_FILES);
		$post_data = $this->input->post();
		//debug($post_data);exit;
		$enquiry_quotation = $post_data['old_enquiry_quotation']; 
		if($_FILES['enquiry_quotation']['size']!=0){
			$enquiry_quotation= $this->tours_model->upload_single_image($_FILES,'enquiry_quotation','');	
		}
		$ite_data=$this->custom_db->single_table_records('tours_enquiry','*',array('id'=>$post_data['app_ref']))['data'][0];
		//debug($ite_data);exit;
		if($ite_data['status']=='INPROGRESS' || $ite_data['status']==''){
			$status='QUOTED';
		}else{
			$status=$ite_data['status'];
		}
		
		$res = $this->custom_db->update_record('tours_enquiry',array('quotation'=>$enquiry_quotation,'status'=>$status),array('id'=>$post_data['app_ref']));
		$ite_data=$this->custom_db->single_table_records('tours_enquiry','*',array('id'=>$post_data['app_ref']))['data'][0];
		if($ite_data['status']=="CONFIRMED"){
			redirect('tours/confirmed_enq_list/'.$ite_data['created_by']);
		}else if($ite_data['status']=="CANCELLED"){
			redirect('tours/cancelled_enq_list/'.$ite_data['created_by']);
		}else{
			redirect('tours/assigned_tours_enquiry/'.$ite_data['created_by']);
		}
		
	}
	function upload_custom_enq_quot(){
		//debug($_FILES);
		$post_data = $this->input->post();
		//debug($post_data);exit;
		$enquiry_quotation = $post_data['old_enquiry_quotation']; 
		if(!empty($_FILES['cust_enq_quotation']['name'])){
			$enquiry_quotation= $this->tours_model->upload_single_image($_FILES,'cust_enq_quotation','');	
		}
		$ite_data=$this->custom_db->single_table_records('custom_package_enquiry','status',array('id'=>$post_data['app_ref']))['data'][0];
		if($ite_data['status']=='INPROGRESS' || $ite_data['status']==''){
			$status='QUOTED';
		}else{
			$status=$ite_data['status'];
		}
		
		$res = $this->custom_db->update_record('custom_package_enquiry',array('quotation'=>$enquiry_quotation,'status'=>$status),array('id'=>$post_data['app_ref']));
		if($res){
			//echo $this->db->last_query();exit;
			redirect('tours/assigned_custom_enquiries');
			} 
		else { 
			echo $this->db->last_query();
			//redirect('tours/assigned_tours_bookings');
		} 
		
	}
	function unlink_enq_quot($id,$img,$type){
		$ite_data=$this->custom_db->single_table_records('tours_enquiry','quotation',array('id'=>$id))['data'][0];
		$this->custom_db->update_record('tours_enquiry',array('quotation'=>''),array('id'=>$id));
		//echo $this->db->last_query();
		$image_location = '../extras/custom/TMX1512291534825461/images/tour_uploads/'.$img;
		//echo $image_location;
			if(file_exists($image_location)){
				echo $image_location;
				unlink($image_location);
			}
			//exit;
	}
	function unlink_cust_enq_quot($id,$img,$type){
		$ite_data=$this->custom_db->single_table_records('custom_package_enquiry','quotation',array('id'=>$id))['data'][0];
		$this->custom_db->update_record('custom_package_enquiry',array('quotation'=>''),array('id'=>$id));
		//echo $this->db->last_query();
		$image_location = '../extras/custom/TMX1512291534825461/images/tour_uploads/'.$img;
		//echo $image_location;
			if(file_exists($image_location)){
				echo $image_location;
				unlink($image_location);
			}
			//exit;
	}
	function add_pack_enquiry_amount($id,$amount){
		$this->custom_db->update_record('tours_enquiry',array('amount'=>$amount),array('id'=>$id));
		
	}
	function add_cust_enquiry_amount($id,$amount){
		$this->custom_db->update_record('custom_package_enquiry',array('amount'=>$amount),array('id'=>$id));
		
	}
	function change_enquiry_status($status,$id,$module){
		$this->custom_db->update_record('tours_enquiry',array('status'=>$status),array('id'=>$id));
		//echo $this->db->last_query();
		//if($res){
			redirect('tours/assigned_tours_enquiry/'.$module);
		//	} 
	}
	function change_custom_enquiry_status($status,$id){
		//error_reporting(E_ALL);
		$this->custom_db->update_record('custom_package_enquiry',array('status'=>$status),array('id'=>$id));
		//echo $this->db->last_query();exit;
		//if($res){
			redirect('tours/assigned_custom_enquiries');
		//	} 
	}
	function cancelled_custom_enq_list(){
		
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id
			);	
		}
		$condition = array(
			'status'=>'CANCELLED'
		);	
		
		$total_records = $this->tours_model->assigned_custom_enquiry($condition);
		$enquiry = $this->tours_model->assigned_custom_enquiry($condition);
		$country_list=$this->tours_model->tours_country_name();
		$city_list=$this->tours_model->tours_city_name();
		
		foreach($enquiry['tours_enquiry'] as $enq_key =>$enq_val){
			$page_data['tours_enquiry'][$enq_key] = $enq_val; 
			$page_data['tours_enquiry'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['tours_enquiry'][$enq_key]['country_name']=''; 
			$page_data['tours_enquiry'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0]; 
			$page_data['tours_enquiry'][$enq_key]['customer_details']= $this->custom_db->single_table_records('tour_booking_pax_details','*',array('app_reference'=>$enq_val['id']))['data'];
			$page_data['tours_enquiry'][$enq_key]['payment_history'] = $this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$enq_val['enquiry_reference_no']))['data'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['adult_count'] = $enq_val['adult'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['child_count'] = $enq_val['child'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['infant_count'] = $enq_val['infant'];
			$page_data['tours_enquiry'][$enq_key]['created_by_id']	= $enq_val['agent_id'];
			foreach($country_array as $c_arr){
				$page_data['tours_enquiry'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
		$this->template->view('tours/cancelled_custom_enquiry',$page_data);
	}
	function confirmed_enq_list($module){
		
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id
			);	
		}
		$condition = array(
			'status'=>'CONFIRMED',
			'module'=>$module
		);	
		
		//exit("vzxc");
		$total_records = $this->tours_model->assigned_tours_enquiry($condition);
		$tours_enquiry = $this->tours_model->assigned_tours_enquiry($condition);
		$page_data['tours_enquiry'] = $tours_enquiry['tours_enquiry'];
		foreach($page_data['tours_enquiry'] as $enq_key => $enq_val){
			$page_data['tours_enquiry'][$enq_key]=$enq_val;
			$page_data['tours_enquiry'][$enq_key]['update_table']='tours_enquiry';
			$page_data['tours_enquiry'][$enq_key]['customer_details'] =$this->custom_db->single_table_records('tour_booking_pax_details','*',array('app_reference'=>$enq_val['enquiry_reference_no']))['data'];
			
			$page_data['tours_enquiry'][$enq_key]['payment_history'] = $this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$enq_val['enquiry_reference_no']))['data'];
			$page_data['tours_enquiry'][$enq_key]['package_details'] = $this->custom_db->single_table_records('tours','*',array('id'=>$enq_val['tour_id']))['data'];
			
			$page_data['tours_enquiry'][$enq_key]['attributes']['adult_count'] = $enq_val['adult'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['child_count'] = $enq_val['child'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['infant_count'] = $enq_val['infant'];
			
			
			
			$page_data['tours_enquiry'][$enq_key]['app_reference'] = $enq_val['enquiry_reference_no'];
		}
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
	
		$this->template->view('tours/confirmed_tours_enquiry',$page_data);
	}
	function cancelled_enq_list($module){
		
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id,
				'status'=>'CANCELLED',
				'module'=>$module
			);	
		}
		$condition = array(
			'status'=>'CANCELLED',
			'module'=>$module
		);	
		
		//exit("vzxc");
		$total_records = $this->tours_model->assigned_tours_enquiry($condition);
		$tours_enquiry = $this->tours_model->assigned_tours_enquiry($condition);
		$page_data['tours_enquiry'] = $tours_enquiry['tours_enquiry'];
		
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
	  
		$this->template->view('tours/cancelled_tours_enquiry',$page_data);
	}
	function confirmed_custom_enq_list(){
		
		if($this->entity_user_type != '1'){
			$condition = array(
				'user_id'=>$this->entity_user_id
			);	
		}
		$condition = array(
			'status'=>'CONFIRMED'
		);	
		
		$total_records = $this->tours_model->assigned_custom_enquiry($condition);
		$enquiry = $this->tours_model->assigned_custom_enquiry($condition);
		$country_list=$this->tours_model->tours_country_name();
		$city_list=$this->tours_model->tours_city_name();
		
		foreach($enquiry['tours_enquiry'] as $enq_key =>$enq_val){
			$page_data['tours_enquiry'][$enq_key] = $enq_val; 
			$page_data['tours_enquiry'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['tours_enquiry'][$enq_key]['country_name']=''; 
			$page_data['tours_enquiry'][$enq_key]['update_table']='custom_package_enquiry';
			$page_data['tours_enquiry'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0];  
			$page_data['tours_enquiry'][$enq_key]['customer_details']= $this->custom_db->single_table_records('tour_booking_pax_details','*',array('app_reference'=>$enq_val['enquiry_reference_no']))['data'];
			$page_data['tours_enquiry'][$enq_key]['payment_history'] = $this->custom_db->single_table_records('tour_payment_slab_details','*',array('enquiry_reference_no'=>$enq_val['enquiry_reference_no']))['data'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['adult_count'] = $enq_val['adult'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['child_count'] = $enq_val['child'];
			$page_data['tours_enquiry'][$enq_key]['attributes']['infant_count'] = $enq_val['infant'];
			$page_data['tours_enquiry'][$enq_key]['created_by_id']	= $enq_val['agent_id'];
			foreach($country_array as $c_arr){
				$page_data['tours_enquiry'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		$page_data['tour_list']          = $this->tours_model->verified_tour_list();
		$page_data['tours_itinerary']    = $this->tours_model->tours_itinerary_all();
		$page_data['tours_country_name'] = $this->tours_model->tours_country_name();
		$page_data['package_manager']    = $this->tours_model->get_package_manager_list();
		//debug($page_data);exit;
		$this->template->view('tours/confirmed_custom_enquiry',$page_data);
	}
	
	
}
