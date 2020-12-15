<?php
	
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	$this->load->library('export');
	$this->load->model('mymodel');
	}



	function toExcel($sql){
		
		$sql = $this->mymodel->myqueryfunction();
		$this->export->to_excel($sql, 'nameForFile'); 

	}



}