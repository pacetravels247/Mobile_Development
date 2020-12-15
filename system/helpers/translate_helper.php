<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* CodeIgniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package	CodeIgniter
* @author	Anil Kumar Panigrahi
* @link	http://codeigniter.com
* @since	Version 1.0
* @filesource
*/
// ------------------------------------------------------------------------
/**
* CodeIgniter Translate Helpers
*
* @package	CodeIgniter
* @subpackage	Helpers
* @category	Helpers
* @author	Anil Kumar Panigrahi
// On date 12-05-2010
*/
// ------------------------------------------------------------------------
/**
* Translate
*
* $s - default source language ( English )
* $d - Destination language ( French , Spanish ... )
* Converted the string using google translate API.
* @return	string
*/
		if ( ! function_exists('translate'))
		{
		function translate($text, $d = '')
		{
				$CI = & get_instance();  //get instance, access the CI superobject
				$get_current_lang='';
				if($CI->session->userdata('lang') == true){
				$get_current_lang = $CI->session->userdata('lang');
				}
		
				$s = 'en';
				$d = $get_current_lang;
				if($d=='') { $d = 'en'; }
				if($d !='en'){
					
				$lang_pair = urlencode($s.'|'.$d);
				
				$q = rawurlencode($text);
				// Google's API translator URL
				
				//$url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=".$q."&langpair=".$lang_pair;
				 $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=". $s."&tl=".$d."&dt=t&q=".$q;
				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $url);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//curl_setopt($ch, CURLOPT_REFERER, "http://www.yoursite.com/translate.php");
				 $body = curl_exec($ch);
				 $name_en = explode('"',$body);	
				
				 if(isset($name_en[1])){
				 	echo $name_en[1];
				 }else{
				 	echo $name_en[3];
				 }
				 
				} else {
				echo $text;
				}
		}
}
// ------------------------------------------------------------------------
/* End of file translate_helper.php */
/* Location: ./system/helpers/translate_helper.php */