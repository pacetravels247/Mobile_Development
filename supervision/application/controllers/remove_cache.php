<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Remove_Cache extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Delete B2C Module Cache Files
	 */
	function delete_b2c_cache_files()
	{	
		$expire_time = 86400; //seconds for 1 day	
		$files = glob(realpath('../b2c').'/cache/*');		
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {			  	
				 unlink($file); // delete file 
			  }
			}
		}
		
		$insert_data = array(
			'deleted_file_count'=>count($files),
			'module'=>'b2c_cache',
			'created_datetime'=>date('Y-m-d H:i:s')
			);
		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
	}
	/**
	 * Delete Agent Module Cache Files
	 */
	function delete_agent_cache_files()
	{
		$expire_time = 86400; //seconds for 1 day
		$files = glob(realpath('../agent/application').'/cache/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {
				 unlink($file); // delete file 
			  }
			}
		}
		$insert_data = array(
			'deleted_file_count'=>count($files),
			'module'=>'agent_cache',
			'created_datetime'=>date('Y-m-d H:i:s')
			);
		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
	}
	/**
	 * Delete Domain Realted Cache Files
	 */
	function delete_temp_cache_files()
	{
		$expire_time = 86400; //seconds for 1 day		
		$files = glob(realpath('../extras').'/custom/'.CURRENT_DOMAIN_KEY.'/tmp/*');		
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {
				 unlink($file); // delete file 
			  }
			}
		}

		$insert_data = array(
			'deleted_file_count'=>count($files),
			'module'=>'app_temp_cache',
			'created_datetime'=>date('Y-m-d H:i:s')
			);
		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
	}
	/**
	 * Delete Domain Related pdf Cache Files
	 */
	function delete_temp_pdf_files()
	{
		$expire_time = 86400; //seconds for 1 day		
		$files = glob(realpath('../extras').'/custom/'.CURRENT_DOMAIN_KEY.'/temp_booking_data_pdf/*');		
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {
				 unlink($file); // delete file 
			  }
			}
		}
		$insert_data = array(
			'deleted_file_count'=>count($files),
			'module'=>'app_temp_pdf',
			'created_datetime'=>date('Y-m-d H:i:s')
			);
		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
	}
}