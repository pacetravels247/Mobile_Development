<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Cache_Logger_Model extends CI_Model
{
	
	
	function insert_cache_data($data){
		$this->db->insert('cache_remove_logger',$data);
	}

	
}
