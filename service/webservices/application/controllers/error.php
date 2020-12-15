<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Error extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('general/ooops');
	}
	function invalid_domain()
	{
		echo 'invalid_domain';exit;
		$this->output_compressed_data($data);
	}
}
