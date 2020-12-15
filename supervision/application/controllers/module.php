<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */

class Module extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('module_model');
	}
	function module_management()
	{
		$page_data['form_data'] = $this->input->post();
		$get_data = $this->input->get();
		if (isset($get_data['eid']) == true) {
			$page_data['eid'] = intval($get_data['eid']);
			$page_data['course_id'] = $get_data['course_id'];
		}
		if (valid_array($page_data['form_data']) == false && isset($page_data['eid']) == true) {
			/**
			 * EDIT DATA
			 */
			$edit_data = $this->module_model->module_management($page_data['eid'], $page_data['course_id']);
			if (valid_array( $edit_data['data']) == true) {
				$page_data['form_data'] = $edit_data['data'][0];
				$page_data['form_data']['booking_source']=array();
				for($i=0; $i<count( $edit_data['data'] ); $i++) {
					if( isset($edit_data['data'][$i]['booking_source']) ) {
						array_push($page_data['form_data']['booking_source'],$edit_data['data'][$i]['booking_source']);
						//$page_data['form_data']['booking_source']=$edit_data['data'][$i]['booking_source'];
					}
				}
			} else {
				redirect('security/log_event?event=Invalid Course Source edit');
			}
			//debug($page_data['form_data']);exit;
		} elseif (valid_array($page_data['form_data']) == true) {
			/** AUTOMATE VALIDATOR **/
			$this->current_page->set_auto_validator();
			if ($this->form_validation->run()) {
				//LETS UNSET DATA WHICH ARE NOT NEEDED FOR DB
				if( isset($page_data['form_data']['booking_source'])  && count($page_data['form_data']['booking_source'])>0) {
					$map_data['booking_source']=$page_data['form_data']['booking_source'];
					unset($page_data['form_data']['booking_source']);
				}
				unset($page_data['form_data']['FID']);
				if (intval($page_data['form_data']['origin']) > 0) {
					//Update Data
					$this->custom_db->update_record('meta_course_list', $page_data['form_data'], array('origin' => $page_data['eid'], 'course_id' => $page_data['course_id']));
					/** Update booking_source and activity reference **/
					$this->custom_db->delete_record('activity_source_map',array('meta_course_list_fk' => $page_data['eid']));
					if( isset($map_data['booking_source']) ) {
						/** Inserting booking_source and activity reference **/
						$insert_map_data['meta_course_list_fk']=$page_data['eid'];
						$insert_map_data['domain_origin'] = get_domain_auth_id();
						for($i=0; $i<count( $map_data['booking_source'] ); $i++) {
							$insert_map_data['booking_source_fk']=$map_data['booking_source'][$i];
							$this->custom_db->insert_record('activity_source_map', $insert_map_data);
						}
							
					}
					set_update_message();
				} elseif (intval($page_data['form_data']['origin']) == 0) {
					//Insert Data
					$page_data['form_data']['created_datetime'] = date('Y-m-d H:i:s');
					$page_data['form_data']['created_by_id'] = $this->entity_user_id;
					$page_data['form_data']['course_id'] = PROJECT_PREFIX.'CID'.time();
					$insert_id = $this->custom_db->insert_record('meta_course_list', $page_data['form_data']);

					if( isset($map_data['booking_source']) ) {
						/** Inserting booking_source and activity reference **/
						$insert_map_data['domain_origin'] = get_domain_auth_id();
						$insert_map_data['meta_course_list_fk']=$insert_id['insert_id'];
						for($i=0; $i<count( $map_data['booking_source'] ); $i++) {
							$insert_map_data['booking_source_fk']=$map_data['booking_source'][$i];
							$this->custom_db->insert_record('activity_source_map', $insert_map_data);
						}
							
					}
					set_insert_message();
				} else {
					redirect('security/log_event?event=COURSE/ACTIVITY Invalid CRUD');
				}
				redirect('module/module_management');
			}
		}
		/** TABLE PAGINATION */
		$page_data['table_data'] = $this->module_model->get_course_list();
		//echo debug($page_data['table_data']);exit;
		/** TABLE PAGINATION */
		$this->template->view('module/module_management', $page_data);
	}
}