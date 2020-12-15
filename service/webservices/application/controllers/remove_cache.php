<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Remove_Cache extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
    }
	/**
     * Clear all cache files
     */
	function clear_all_cache_data()
	{
		$this->delete_flight_cache_files();
		$this->delete_bus_cache_files();
		$this->delete_hotel_cache_files();
		$this->truncate_cache_tables();
	}
	/**
	 * Delete Flight Related Cache Files
	 */
	function delete_flight_cache_files()
	{
		$expire_time = 86400; //seconds for 1 day
		$files = glob(realpath('../temp').TBO_FLIGHT_CACHE_PATH.'*');		
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {			  		
				 unlink($file); // delete file 			 

			  }
			}
		}
		
		$insert_data = array(
    		'deleted_file_count'=>count($files),
    		'module'=>'flight',
    		'created_datetime'=>date('Y-m-d H:i:s')
    	);
   		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
    

	}

	/**
	 * Delete Hotel Related Cache Files
	 */

	function delete_hotel_cache_files()
	{
		$expire_time = 86400; //seconds for 1 day
		$files = glob(realpath('../temp').TBO_HOTEL_CACHE_PATH.'*');		
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {
				 unlink($file); // delete file 				 
			  }
			}
		}
		
		$insert_data = array(
    		'deleted_file_count'=>count($files),
    		'module'=>'hotel',
    		'created_datetime'=>date('Y-m-d H:i:s')
    	);
   		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
		
	}

	/**
	 * Delete Bus Related Cache Files
	 */

	function delete_bus_cache_files()
	{
		$expire_time = 86400; //seconds for 1 day
		$files = glob(realpath('../temp').TRAVELYAARI_BUS_CACHE_PATH.'*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - $expire_time) {
				 unlink($file); // delete file 
				 
			  }
			}
		}
		
		$insert_data = array(
    		'deleted_file_count'=>count($files),
    		'module'=>'bus',
    		'created_datetime'=>date('Y-m-d H:i:s')
    	);
   		$this->custom_db->insert_record('cache_remove_logger', $insert_data);
		
	}
	/**
	 * Truncate Cache Files
	 */
	function truncate_cache_tables()
	{
		//provab_api_response_history table
		//Delete the rows
		$this->db->query('delete from provab_api_response_history where date(created_datetime)<curdate()');
               // $this->db->query('delete from provab_api_request_history where date(created_datetime)<curdate()');
                $this->db->query('delete from travelport_price_xml where date(created_date)<curdate()');
	}
	
	
}
