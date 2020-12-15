<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Cron_job
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class Cron_job extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->model('cron_job_model');
		$this->load->model('flight_model');
		$this->load->library('booking_data_formatter');
	}
	function index()
	{
		redirect(base_url());
	}
	/**
	 * Update the flight booking details
	 */
	public function update_pnr_details()
	{
		$not_confirmed_tickets = $this->cron_job_model->not_confirmed_tickets();
		//log the details
		$cron_job_logger = array();
		$cron_job_logger['service_method'] = __FUNCTION__.' - Not confirmed bookings('.count($not_confirmed_tickets).')';
		$cron_job_logger['created_datetime'] = db_current_datetime();
		$this->custom_db->insert_record('cron_job_logger', $cron_job_logger);
		
		if(valid_array($not_confirmed_tickets) == true){
			load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
			foreach($not_confirmed_tickets as $k => $v){
				$app_reference = trim($v['app_reference']);
				$response = $this->flight_lib->update_pnr_details($app_reference);
				
				if(isset($response['status']) == true && $response['status'] == SUCCESS_STATUS && valid_array($response['data']) == true){
					$get_pnr_updated_status = $this->flight_model->update_pnr_details($response,$app_reference);
				}
			}
		}
	}
}