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
  
    public function saveEnquiry($data){
    	$this->db->insert('package_enquiry',$data);
    	return $this->db->insert_id();
    }

    public function getPackageCountries(){
    	$this->db->select('package_country');
    	 $this->db->from('package'); 
    	 $this->db->group_by('package_country'); 
		$query = $this->db->get();
		if($query->num_rows()){
			return $query->result();
		}else{
			return array();
		}
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
    
}
