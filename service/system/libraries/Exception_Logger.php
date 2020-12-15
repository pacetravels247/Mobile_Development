<?php
/**
 * System exception logs 
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Jaganath N<jaganath.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Exception_Logger {
	protected $meta_course;
	protected $log_file_path; 
	/**
	 *
	 * @param array $params: array('meta_course' => META_AIRLINE_COURSE)
	 */
	public function __construct($params = array())
	{
		$this->CI = &get_instance();
		if(isset($params['meta_course']) == true && empty($params['meta_course']) == false){
			$this->meta_course = trim($params['meta_course']);
		} else {
			$this->meta_course = META_AIRLINE_COURSE;
		}
		$this->set_log_file_path();
	}
	/**
	 * Log path based on course
	 * 
	 */
	private function set_log_file_path()
	{
		switch ($this->meta_course){
			case META_AIRLINE_COURSE:
				$log_file_path = realpath('../temp').'/exception_logs/flight/';
				break;
			case META_ACCOMODATION_COURSE:
				$log_file_path = realpath('../temp').'/exception_logs/hotel/';
				break;
			case META_SIGHTSEEING_COURSE:
				$log_file_path = realpath('../temp').'/exception_logs/sightseeing/';
				break;
			case META_VIATOR_TRANSFER_COURSE:
				$log_file_path = realpath('../temp').'/exception_logs/transfers/';
				break;				
				
			default:
				$log_file_path = '';
		}
		$this->log_file_path = $log_file_path;
	}
	/**
	 * Logs the exception
	 * Enter description here ...
	 */
	public function log_exception($app_reference, $operation, $notification = '', $log_details='')
	{
		if(is_array($log_details) == true){
			$log_details = json_encode($log_details);
		}
		$log_details = trim($log_details);
		if(empty($log_details) == false){
			$log_file_name = (time().'-'.rand(10, 999));
			$this->log_into_file($log_file_name, $log_details);
		} else {
			$log_file_name = '';
		}
		$this->CI->module_model->log_exception($this->meta_course, $operation, $notification, $app_reference, $log_file_name);
	}
	/**
	 * Write the log into file
	 * Enter description here ...
	 * @param unknown_type $app_reference
	 * @param unknown_type $log_details
	 */
	private function log_into_file($file_name, $data)
	{
		$file_name = trim($file_name);
		if(empty($file_name) == false && empty($data) == false) {
			$file = fopen($this->log_file_path. $file_name . '.json', "w" );
			fwrite($file, $data);
			fclose($file);
		}
	}
	/**
	 * Read the log file
	 * @param unknown_type $file_name
	 */
	private function read_log_file($file_name)
	{
		$data =array();
		$data['status'] = FAILURE_STATUS;
		$data['message'] = '';
		$file_name = trim($file_name);
		if(empty($file_name) == false) {
			$file_pointer = @fopen($this->log_file_path.$file_name.'.json', "r" );
			if($file_pointer) {
				$data['status'] = SUCCESS_STATUS;
				$log_data = fread($file_pointer, filesize($this->log_file_path.$file_name.'.json'));
				fclose($file_pointer);
				$data['data'] = $log_data;
			} else {
				$data['message'] = 'File Not Found';
			}
		}
		return $data;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $app_reference
	 */
	public function get_exception_log_details($app_reference)
	{
		$data['status'] = FAILURE_STATUS;
		$data['message'] = '';
		$condition = array();
		$condition[] = array('EL.app_reference', '=', '"'.$app_reference.'"');
		$exception_log_details = $this->CI->module_model->get_exception_log_details($condition);
		if(valid_array($exception_log_details) == true){
			foreach ($exception_log_details as $ek => $ev) {
				$log_details[$ek] = $ev;
				$log_file = $this->read_log_file($ev['log_file']);
				if($log_file['status'] == SUCCESS_STATUS && empty($log_file['data']) == false){
					$log_details[$ek]['log_file'] = $log_file['data'];
				} else {
					$log_details[$ek]['log_file'] = $ev['notification'];
				}
			}
			$data['status'] = SUCCESS_STATUS;
			$data['data']['log_details'] = $log_details;
		}
		return $data;
	}
}