<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Test_Services extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function delete_b2c_cache_files()
	{
		$files = glob(realpath('b2c').'/cache/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	function delete_agent_cache_files()
	{
		$files = glob(realpath('agent/application').'/cache/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	function delete_temp_cache_files()
	{
		$files = glob(realpath('extras').'/custom/wKuTTCM3wqbBLoLkMHp8VIvyA/tmp/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	function delete_temp_pdf_files()
	{
		$files = glob(realpath('extras').'/custom/wKuTTCM3wqbBLoLkMHp8VIvyA/temp_booking_data_pdf/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
}