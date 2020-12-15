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
            // debug($data);exit;   
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
    
    
}
