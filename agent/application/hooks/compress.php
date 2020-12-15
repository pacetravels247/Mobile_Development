<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
function compress() {
	ini_set ( "pcre.recursion_limit", "16777" );
	$CI = & get_instance ();
	$buffer = $CI->output->get_output ();
	
	$new_buffer = get_compressed_output ( $buffer );
	
	// We are going to check if processing has working
	if ($new_buffer === null) {
		$new_buffer = $buffer;
	}
	
	$CI->output->set_output ( $new_buffer );
	$CI->output->_display ();
}



/* End of file compress.php */
/* Location: ./system/application/hooks/compress.php */