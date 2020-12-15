<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab - Provab Application
 * @subpackage Travel Portal
 * @author Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version V2
 */
class Supplier extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model ( 'Supplierpackage_Model' );
	}
	public function view_packages_types() {
		$data ['package_view_data'] = $this->Supplierpackage_Model->package_view_data_types ()->result ();
		// print_r($data['package_view_data']); die();
		$this->template->view ( 'suppliers/view_package_types', $data );
	}
	public function delete_package_type($id) {
		$this->Supplierpackage_Model->delete_package_type ( $id );
		redirect ( 'supplier/view_packages_types' );
	}
	
	public function delete_traveller_img($pack_id,$img_id) {
		$this->Supplierpackage_Model->delete_traveller_img ( $pack_id,$img_id );
		redirect ( 'supplier/images/'.$img_id.'/w' );
	}
	public function delete_package($id) {
		$this->Supplierpackage_Model->delete_package ( $id );
		redirect ( 'supplier/view_with_price' );
	}
	public function add_package_type($id = '') {
		$data = array();
		if ($id != '') {
			$data ['pack_data'] = $this->Supplierpackage_Model->get_pack_id ( $id );
			$this->template->view ( 'suppliers/add_package_type', $data );
		} else {
			$this->template->view ( 'suppliers/add_package_type', $data );
		}
	}
	public function save_packages_type() {
		$pack_id = $this->input->post ( 'package_types_id' );
		$package_name = $this->input->post ( 'name' );
		$domain_list_fk = get_domain_auth_id ();
		// echo "<pre>";print_r($domain_list_fk);exit;
		$add_package_data = array (
				'package_types_name' => $package_name,
				'domain_list_fk' => $domain_list_fk 
		);
		if ($pack_id > 0) {
			$this->Supplierpackage_Model->update_package_type ( $add_package_data, $pack_id );
		} else {
			$this->db->insert ( "package_types", $add_package_data );
		}
		redirect ( 'supplier/view_packages_types' );
	}
	public function add_with_price() {
		$data ['package_type_data'] = $this->Supplierpackage_Model->package_view_data_types ()->result ();
		$data ["country"] = $this->Supplierpackage_Model->get_countries ();
		$this->template->view ( 'suppliers/add_package', $data );
	}
	public function get_countries() {
		$this->db->limit ( 1000 );
		$this->db->order_by ( "name", "asc" );
		$qur = $this->db->get ( "country" );
		return $qur->result ();
	}
	public function add_package_new() {
		$photo = $_FILES;
		$cpt1 = count ( $_FILES ['photo'] ['name'] );
		if ($_FILES ['photo'] ['name'] != '') {
			$_FILES ['photo'] ['name'] = $photo ['photo'] ['name'];
			$_FILES ['photo'] ['type'] = $photo ['photo'] ['type'];
			$_FILES ['photo'] ['tmp_name'] = $photo ['photo'] ['tmp_name'];
			$_FILES ['photo'] ['error'] = $photo ['photo'] ['error'];
			$_FILES ['photo'] ['size'] = $photo ['photo'] ['size'];
			
			move_uploaded_file ( $_FILES ['photo'] ['tmp_name'], $this->template->domain_uploads_packages ( $_FILES ['photo'] ['name'] ) );
			$photo = $_FILES ['photo'] ['name'];
		}
		
		$disn = $this->input->post ( 'disn' );
		$name = $this->input->post ( 'name' );
		$country = $this->input->post ( 'country' );
		$city = $this->input->post ( 'cityname' );
		$duration = $this->input->post ( 'duration' );
		$description = $this->input->post ( 'Description' );
		$location = $this->input->post ( 'location' );
		$state = $this->input->post ( 'state' );
		$deal = $this->input->post ( 'deal' );
		$homepage = $this->input->post ( 'homepage' );
		$rating = $this->input->post ( 'rating' );
		$tourss = $this->input->post ( 'tourss' );
		$with_price = $this->input->post ( 'with_price' );
		$p_price = $this->input->post ( 'p_price' );
		// itinerary
		$itinerarydesc = $this->input->post ( 'desc' );
		$days = $this->input->post ( 'days' );
		$place = $this->input->post ( 'place' );
		$price = $this->input->post ( 'price' );
		// price includes
		$includes = $this->input->post ( 'includes' );
		$excludes = $this->input->post ( 'excludes' );
		// cancellation
		$advance = $this->input->post ( 'advance' );
		$penality = $this->input->post ( 'penality' );
		// question and answer
		$no_questions = $this->input->post ( 'no_of_questions' );
		$quest = $this->input->post ( 'quest' );
		$answer = $this->input->post ( 'answer' );
		$sup_id = $this->session->userdata ( 'sup_id' );
		$sup_type = $this->session->userdata ( 'sup_type' );
		// price
		$sd = $this->input->post ( 'sd' );
		$ed = $this->input->post ( 'ed' );
		$adult = $this->input->post ( 'adult' );
		$child = $this->input->post ( 'child' );
		$pricee = $this->input->post ( 'pricee' );
		// deals
		$value = $this->input->post ( 'value' );
		$discount = $this->input->post ( 'discount' );
		$save = $this->input->post ( 'save' );
		$seats = $this->input->post ( 'seats' );
		$time = $this->input->post ( 'time' );
		$packageaw = $this->input->post ( 'a_wo_p' );
		$domain_list_fk = get_domain_auth_id ();
		$top_destination = $this->input->post ( 'top_destination' );
		$newpackage = array (
				'package_type' => $disn,
				'package_name' => $name,
				'supplier_id' => $sup_id,
				'price_includes' => $pricee,
				'package_country' => $country,
				'package_city' => $city,
				'package_location' => $location,
				'package_description' => $description,
				'deals' => $deal,
				'duration' => $duration,
				'image' => $photo,
				'no_que' => $no_questions,
				'home_page' => $homepage,
				'rating' => $rating,
				'tour_types' => $tourss,
				'price' => $p_price,
				'top_destination' => $top_destination,
				'domain_list_fk' => $domain_list_fk,
				//Set Default value due to PDO connection
				'package_code' => '',
				'package_tour_code' => '',
				'package_state' => '',
				'status' => '',  
		);
		// echo "<pre>"; print_r($newpackage);exit;
		// print_r($country);exit;
		$package = $this->Supplierpackage_Model->add_new_package ( $newpackage );
		if ($package != 0) {
			$packcode = array (
					'package_code' => 'SKY' . $package 
			);
			$this->Supplierpackage_Model->update_code_package ( $packcode, $package );
		}
		$img = $_FILES;
		$cp = count ( $_FILES ['image'] ['name'] );
		if ($_FILES ['image'] ['name'] [0] != '') {
			for($i = 0; $i < $duration; $i ++) {
				$_FILES ['image'] ['name'] = $img ['image'] ['name'] [$i];
				$_FILES ['image'] ['type'] = $img ['image'] ['type'] [$i];
				$_FILES ['image'] ['tmp_name'] = $img ['image'] ['tmp_name'] [$i];
				$_FILES ['image'] ['error'] = $img ['image'] ['error'] [$i];
				$_FILES ['image'] ['size'] = $img ['image'] ['size'] [$i];
				$itenary_imge = 'itenary-' . time () . $_FILES ['image'] ['name'];
				move_uploaded_file ( $_FILES ['image'] ['tmp_name'], $this->template->domain_uploads_packages ( $itenary_imge ) );
				$image = $itenary_imge;
				$itinerary = array (
						'itinerary_description' => $itinerarydesc [$i],
						'day' => $days [$i],
						'place' => $place [$i],
						'itinerary_image' => $image,
						'package_id' => $package 
				);
				$itineraryid = $this->Supplierpackage_Model->itinerary ( $itinerary );
			}
		}
		for($i = 0; $i < $no_questions; $i ++) {
			if (! empty ( $quest [$i] )) {
				$qus_ans = array (
						'question' => $quest [$i],
						'answer' => $answer [$i],
						'package_id' => $package,
						'user_id' => $sup_id,
						'usertype' => $sup_type 
				);
				$queans = $this->Supplierpackage_Model->que_ans ( $qus_ans );
			}
		}
		$pricingpolicy = array (
				'package_id' => $package,
				'price_includes' => $includes,
				'price_excludes' => $excludes 
		);
		$policy = $this->Supplierpackage_Model->pricing_policy ( $pricingpolicy );
		$cancellation = array (
				'package_id' => $package,
				'cancellation_advance' => $advance,
				'cancellation_penality' => $penality 
		);
		$cancel = $this->Supplierpackage_Model->cancellation_penality ( $cancellation );
		if (! empty ( $value )) {
			$deals = array (
					'value' => $value,
					'discount' => $discount,
					'you_save' => $save,
					'seats' => $seats,
					'time' => $time,
					'package_id' => $package 
			);
			$deall = $this->Supplierpackage_Model->deals ( $deals );
		}
		for($i = 0; $i < $duration; $i ++) {
			if (! empty ( $sd [$i] )) {
				$incl = array (
						'from_date' => $sd [$i],
						'to_date' => $ed [$i],
						'duration' => $duration,
						'adult_price' => $adult [$i],
						'child_price' => $child [$i],
						'package_id' => $package 
				);
				$in = $this->Supplierpackage_Model->incl ( $incl );
			}
		}
		$tra = $_FILES;
		$c = count ( $_FILES ['traveller'] ['name'] );
		if ($_FILES ['traveller'] ['name'] [0] != '') {
			for($i = 0; $i < $c; $i ++) {
				$_FILES ['traveller'] ['name'] = $tra ['traveller'] ['name'] [$i];
				$_FILES ['traveller'] ['type'] = $tra ['traveller'] ['type'] [$i];
				$_FILES ['traveller'] ['tmp_name'] = $tra ['traveller'] ['tmp_name'] [$i];
				$_FILES ['traveller'] ['error'] = $tra ['traveller'] ['error'] [$i];
				$_FILES ['traveller'] ['size'] = $tra ['traveller'] ['size'] [$i];
				$name_of_traveller_img = 'traveller-' . time () . $_FILES ['traveller'] ['name'];
				move_uploaded_file ( $_FILES ['traveller'] ['tmp_name'], $this->template->domain_uploads_packages ( $name_of_traveller_img ) );
				$traveller_img = $name_of_traveller_img;
				$sup_id = $this->entity_user_id;
				$traveller = array (
						'traveller_image' => $traveller_img,
						'user_id' => $sup_id,
						'package_id' => $package 
				);
				$travel = $this->Supplierpackage_Model->travel_images ( $traveller );
			}
		}
		redirect ( 'supplier/view_with_price' );
	}
	public function view_without_price() {
		$data ['newpackage'] = $this->Supplierpackage_Model->without_price ();
		$this->template->view ( 'suppliers/view_without_price', $data );
	}
	public function view_with_price() {
		$data ['newpackage'] = $this->Supplierpackage_Model->with_price ();
		$this->template->view ( 'suppliers/view_with_price', $data );
	}
	public function update_deal_status($package_id, $deals) {
		$this->Supplierpackage_Model->update_deal_status ( $package_id, $deals );
		if ($deals == '1') {
			$data ['package_id'] = $package_id;
			$this->template->view ( 'suppliers/view_city_request', $data );
		} else if ($deals == '0') {
			$data ['package_id'] = $package_id;
			$this->Supplierpackage_Model->delete_deal_id ( $package_id );
			redirect ( 'suppliers/view_city_request', 'refresh' );
		} else {
			redirect ( 'supplier/view_deals', 'refresh' );
		}
	}
	public function update_status($package_id, $status) {
		$this->Supplierpackage_Model->update_status ( $package_id, $status );
		redirect ( 'supplier/view_with_price/', 'refresh' );
	}
	public function update_top_destination($package_id, $status) {
		$this->Supplierpackage_Model->update_top_destination ( $package_id, $status );
		redirect ( 'supplier/view_with_price/', 'refresh' );
	}
	public function read_enquiry($package_id) {
		$status=ACTIVE;
		$this->Supplierpackage_Model->update_enquiry_status ( $package_id, $status );
		redirect ( 'supplier/enquiries/', 'refresh' );
	}
	
	public function view_deals() {
		$data ['newpackage'] = $this->Supplierpackage_Model->get_supplier ();
		$this->template->view ( 'suppliers/suppliers', $data );
	}
	public function update_homepage_status2($package_id, $home_page) {
		$this->Supplierpackage_Model->update_homepage_status ( $package_id, $home_page );
		redirect ( 'supplier/view_without_price/' . $package_id, 'refresh' );
	}
	public function edit_without_price($package_id) {
		$data ['packdata'] = $this->Supplierpackage_Model->get_package_id ( $package_id );
		$data ['price'] = $this->Supplierpackage_Model->get_price ( $package_id );
		$data ['countries'] = $this->Supplierpackage_Model->get_country_city_list ();
		// print_r($data);exit;
		$this->template->view ( 'suppliers/edit_without_price', $data );
	}
	public function edit_itinerary($package_id) {
		$data ['pack_data'] = $this->Supplierpackage_Model->get_itinerary_id ( $package_id );
		// print_r($data);die;
		$data ['package_id'] = $package_id;
		$this->template->view ( 'suppliers/edit_itinerary', $data );
	}
	public function quesans_view($package_id) {
		$data ['que_ans'] = $this->Supplierpackage_Model->get_que_ans ( $package_id );
		$data ['package_id'] = $package_id;
		$this->template->view ( 'suppliers/quesans_view', $data );
	}
	public function enquiries() {
		$data ['enquiries'] = $this->Supplierpackage_Model->enquiries ();
		//debug($data);exit;
		$this->template->view ( 'suppliers/enquiries', $data );
	}
	public function delete_enquiry($id, $package_id) {
		$this->Supplierpackage_Model->delete_enquiry ( $id );
		redirect ( 'supplier/enquiries/' );
	}
	public function itinerary() {
		// print_r($itinerarydesc);exit;
		$itinerary = array (
				'itinerary_description' => $itinerarydesc,
				'day' => $days 
		);
		$it = $this->Supplierpackage_Model->itinerary ( $itinerary );
		if ($it) {
			redirect ( 'supplier/view_deals', 'refresh' );
		}
	}
	public function itinerary_loop($duration) {
		$data ['duration'] = $duration;
		echo $this->template->isolated_view ( 'suppliers/duration_itinerary', $data );
	}
	public function itinerary_loop1($questions) {
		$data ['questions'] = $questions;
		$this->template->isolated_view ( 'suppliers/question', $data );
	}
	public function itinerary_loop2($pricee) {
		$data ['pricee'] = $pricee;
		$this->template->isolated_view ( 'suppliers/withprice', $data );
	}
	public function get_crs_city($city_id) {
		$city = $this->Supplierpackage_Model->get_crs_city_list ( $city_id );
		$sHtml = "";
		$sHtml .= '<option value="">Select City</option>';
		if (! empty ( $city )) {
			foreach ( $city as $key => $value ) {
				$sHtml .= '<option value="' . $value->city . '">' . $value->city . '</option>';
			}
		}
		echo json_encode ( array (
				'result' => $sHtml 
		) );
		exit ();
	}
	public function get_tour($tour_id) {
		// echo "hi";
		// echo $tour_id;die;
		$tours = $this->Supplierpackage_Model->get_tour_list ( $tour_id );
		$sHtml = "";
		$sHtml .= '<option value="">Select Tours</option>';
		if (! empty ( $tours )) {
			// debug($tours);exit;
			foreach ( $tours as $key => $value ) {
				$sHtml .= '<option value="' . $value->package_types_id . '">' . $value->package_types_name . '</option>';
			}
		}
		echo json_encode ( array (
				'result' => $sHtml 
		) );
		exit ();
	}
	public function edit_with_price($package_id) {
		$data ['packdata'] = $this->Supplierpackage_Model->get_package_id ( $package_id );
		$data ['price'] = $this->Supplierpackage_Model->get_price ( $package_id );
		$data ['countries'] = $this->Supplierpackage_Model->get_country_city_list ();
		// print_r($data);exit;
		$this->template->view ( 'suppliers/edit_with_price', $data );
	}
	public function update_package($package_id) {
		$name = $this->input->post ( 'name' );
		$location = $this->input->post ( 'location' );
		$description = $this->input->post ( 'Description' );
		$photo = $_FILES;
		if ($_FILES ['photo'] ['name'] != '') {
			$_FILES ['photo'] ['name'] = $photo ['photo'] ['name'];
			$_FILES ['photo'] ['type'] = $photo ['photo'] ['type'];
			$_FILES ['photo'] ['tmp_name'] = $photo ['photo'] ['tmp_name'];
			$_FILES ['photo'] ['error'] = $photo ['photo'] ['error'];
			$_FILES ['photo'] ['size'] = $photo ['photo'] ['size'];
			
			move_uploaded_file ( $_FILES ['photo'] ['tmp_name'], $this->template->domain_uploads_packages ( $_FILES ['photo'] ['name'] ) );
			$photo = $_FILES ['photo'] ['name'];
		} else {
			$photo = $this->input->post ( 'hidephoto' );
		}
		$includes = $this->input->post ( 'includes' );
		$excludes = $this->input->post ( 'excludes' );
		$price = $this->input->post ( 'price' );
		$advance = $this->input->post ( 'advance' );
		$penality = $this->input->post ( 'penality' );
		$value = $this->input->post ( 'value' );
		$discount = $this->input->post ( 'discount' );
		$save = $this->input->post ( 'save' );
		$seats = $this->input->post ( 'seats' );
		$time = $this->input->post ( 'time' );
		$sd = $this->input->post ( 'sd' );
		$ed = $this->input->post ( 'ed' );
		$adult = $this->input->post ( 'adult' );
		$child = $this->input->post ( 'child' );
		$packtypew = $this->input->post ( 'w_wo_d' );
		$rating = $this->input->post ( 'rating' );
		$p_price = $this->input->post ( 'p_price' );
		$data = array (
				'package_name' => $name,
				'package_location' => $location,
				'package_description' => $description,
				'image' => $photo,
				'rating' => $rating,
				'price' => $p_price 
		);
		// print_r($data);exit;
		$policy = array (
				'price_includes' => $includes,
				'price_excludes' => $excludes 
		);
		$can = array (
				'cancellation_advance' => $advance,
				'cancellation_penality' => $penality 
		);
		$dea = array (
				'value' => $value,
				'discount' => $discount,
				'you_save' => $save,
				'seats' => $seats,
				'time' => $time 
		);
		if (! empty ( $sd )) {
			for($i = 0; $i < count ( $sd ); $i ++) {
				$pri = array (
						'from_date' => $sd [$i],
						'to_date' => $ed [$i],
						'adult_price' => $adult [$i],
						'child_price' => $child [$i] 
				);
				$this->Supplierpackage_Model->update_edit_pri ( $package_id, $pri );
			}
		}
		// print_r($data);die;
		$this->Supplierpackage_Model->update_edit_package ( $package_id, $data );
		$this->Supplierpackage_Model->update_edit_policy ( $package_id, $policy );
		$this->Supplierpackage_Model->update_edit_can ( $package_id, $can );
		$this->Supplierpackage_Model->update_edit_dea ( $package_id, $dea );
		
		if ($packtypew == "w") {
			redirect ( 'supplier/view_with_price' );
		} elseif ($packtypew == 'wo') {
			redirect ( 'supplier/view_without_price' );
		} else {
			redirect ( 'supplier/view_deals' );
		}
		
		redirect ( 'supplier/view_with_price' );
	}
	public function images($package_id) {
		if ($_FILES) {
			$tra = $_FILES;
			$_FILES ['traveller'] ['name'] = $tra ['traveller'] ['name'];
			$_FILES ['traveller'] ['type'] = $tra ['traveller'] ['type'];
			$_FILES ['traveller'] ['tmp_name'] = $tra ['traveller'] ['tmp_name'];
			$_FILES ['traveller'] ['error'] = $tra ['traveller'] ['error'];
			$_FILES ['traveller'] ['size'] = $tra ['traveller'] ['size'];
			$name_of_traveller_img = 'traveller-' . time () . $_FILES ['traveller'] ['name'];
			move_uploaded_file ( $_FILES ['traveller'] ['tmp_name'], $this->template->domain_uploads_packages ( $name_of_traveller_img ) );
			$traveller_img = $name_of_traveller_img;
			$sup_id = $this->entity_user_id;
			$pckge_id = $this->input->post ( 'pckge_id' );
			$traveller = array (
					'traveller_image' => $traveller_img,
					'user_id' => $sup_id,
					'package_id' => $pckge_id 
			);
			$travel = $this->Supplierpackage_Model->travel_images ( $traveller );
			redirect ( base_url () . 'supplier/images/' . $package_id . '/w' );
		}
		$data ['traveller'] = $this->Supplierpackage_Model->get_image ( $package_id );
		$data ['package_id'] = $package_id;
		$this->template->view ( 'suppliers/images', $data );
	}
	public function update_itinerary() {
		$package_id = $this->input->post ( 'package_id' );
		$itinerary_id = $this->input->post ( 'itinerary_id' );
		$itinerarydesc = $this->input->post ( 'desc' );
		$days = $this->input->post ( 'days' );
		$place = $this->input->post ( 'place' );
		$hiddenimage = $this->input->post ( 'hiddenimage' );
		// print_r($hiddenimage);
		// exit;
		$img = $_FILES;
		$cp = count ( $itinerary_id );
		// print_r($cp);exit;
		for($i = 0; $i < $cp; $i ++) {
			
			if (! empty ( $_FILES ['imagelable' . $i] ['name'] )) {
				$_FILES ['imagelable'] ['name'] = $img ['imagelable' . $i] ['name'];
				$_FILES ['imagelable'] ['type'] = $img ['imagelable' . $i] ['type'];
				$_FILES ['imagelable'] ['tmp_name'] = $img ['imagelable' . $i] ['tmp_name'];
				$_FILES ['imagelable'] ['error'] = $img ['imagelable' . $i] ['error'];
				$_FILES ['imagelable'] ['size'] = $img ['imagelable' . $i] ['size'];
				move_uploaded_file ( $_FILES ['imagelable'] ['tmp_name'], $this->template->domain_uploads_packages ( $_FILES ['imagelable' . $i] ['name'] ) );
				$image = $this->template->domain_uploads_packages ( $_FILES ['imagelable' . $i] ['name'] );
			} else {
				$image = $hiddenimage [$i];
			}
			
			$data = array (
					'itinerary_description' => $itinerarydesc [$i],
					'place' => $place [$i],
					'day' => $days [$i],
					'itinerary_image' => $image 
			);
			
			$this->Supplierpackage_Model->update_itinerary ( $package_id, $itinerary_id [$i], $data );
		}
		redirect ( 'supplier/edit_itinerary/' . $package_id, 'refresh' );
	}
	public function view_enquiries($package_id) {
		$data ['enquiries'] = $this->Supplierpackage_Model->view_enqur ( $package_id );
		$data ['package_id'] = $package_id;
		$this->template->view ( 'suppliers/enquiry_package', $data );
	}
}