<?php

/**
 * Provab Template Class
 *
 * Handle all the Page loads in the application
 *
 * @package		Provab
 * @subpackage	provab View
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Template {
	var $CI;			//Access Codeigniter Object
	var $page_name;		//Name of the page being loaded Default to current url
	var $template;	//Set page template details template_v1.php
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->set_default_page_details();

	}

	/**
	 * Default to current uri segment 2 - method name and page name are same
	 */
	private function set_default_page_details($template_name='', $page_name='')
	{
		$this->set_page_name($page_name);
		//set up template details
		$this->set_page_template($template_name);
	}

	/**
	 * Default to current uri segment 2 - method name and page name are same
	 * @param $page_name string page name to be set, default to url
	 */
	private function set_page_name($page_name)
	{
		if (empty($page_name) == false) {
			$this->page_name = $page_name;//if page name is passed then override
		} elseif (empty($this->page_name) == true) {
			$this->page_name = $this->CI->uri->segment(2);
		}
	}

	/**
	 * Template to be set for current page load
	 * @param string $template_name name of the template to be set
	 */
	public function set_page_template($template_name='')
	{
		$app_default_template = $this->get_application_template();
		if (is_string($template_name) == true) {
			if (empty($template_name) == false) {
				$this->template = $template_name;
			} elseif (empty($GLOBALS['CI']->application_default_template) == false) {
				$this->template = $GLOBALS['CI']->application_default_template; // lets set this from hook
			} elseif (empty($app_default_template) == true) {
				$this->template = DEFAULT_TEMPLATE;
			}
		}
	}

	/**
	 * Access template name
	 */
	public function get_application_template()
	{
		return $this->template;
	}
	
	/**
	 * app template details for Audio
	 */
	public function template_audio_dir($file_name='')
	{
		if (empty($file_name) == true) {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_AUDIO_DIR)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_AUDIO_DIR;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_AUDIO_DIR;
			}
		} else {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_AUDIO_DIR.$file_name)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_AUDIO_DIR.$file_name;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_AUDIO_DIR.$file_name;
			}
		}
	}

	/**
	 * app template details for css
	 */
	public function template_css_dir($file_name='')
	{
		if (empty($file_name) == true) {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_CSS_DIR)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_CSS_DIR;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_CSS_DIR;
			}
		} else {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_CSS_DIR.$file_name)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_CSS_DIR.$file_name;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_CSS_DIR.$file_name;
			}
		}
	}

	/**
	 * app template details for js
	 */
	public function template_js_dir($file_name='')
	{
		if (empty($file_name) == true) {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_JS_DIR)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_JS_DIR;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_JS_DIR;
			}
		} else {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_JS_DIR.$file_name)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_JS_DIR.$file_name;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_JS_DIR.$file_name;
			}
		}
	}

	/**
	 * app template details for image
	 */
	public function template_images($file_name='', $full=0)
	{
		if($full)
		{
			if (empty($file_name) == true) {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR)) {
				return SYSTEM_TEMPLATE_LIST_URL.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR;
			} else {
				return SYSTEM_TEMPLATE_LIST_URL.'/'.DEFAULT_TEMPLATE.TEMPLATE_IMAGE_DIR;
			}

		}
		else {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR.$file_name)) {
				return SYSTEM_TEMPLATE_LIST_URL.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR.$file_name;
			} else {
				return SYSTEM_TEMPLATE_LIST_URL.'/'.DEFAULT_TEMPLATE.TEMPLATE_IMAGE_DIR.$file_name;
			}
		}
		}
		if (empty($file_name) == true) {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_IMAGE_DIR;
			}

		} else {
			if (file_exists(SYSTEM_TEMPLATE_LIST_RELATIVE_PATH.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR.$file_name)) {
				return SYSTEM_TEMPLATE_LIST.'/'.$this->get_application_template().TEMPLATE_IMAGE_DIR.$file_name;
			} else {
				return SYSTEM_TEMPLATE_LIST.'/'.DEFAULT_TEMPLATE.TEMPLATE_IMAGE_DIR.$file_name;
			}
		}
	}


	/**
	 * get domain specific image dir path details
	 */
	public function domain_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_IMAGE_DIR;
		} else 	{
			return DOMAIN_IMAGE_DIR.$image_name;
		}
	}
	/**
	 * get domain specific image full browser access root path details
	 */
	public function domain_full_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return FULL_DOMAIN_IMAGE_DIR;
		} else 	{
			return FULL_DOMAIN_IMAGE_DIR.$image_name;
		}
	}
	/**
	 * get domain specific image dir path details
	 */
	public function domain_ban_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_BAN_IMAGE_DIR;
		} else 	{
			return DOMAIN_BAN_IMAGE_DIR.$image_name;
		}
	}
	public function domain_ban_image_full_path($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return '../..'.DOMAIN_BAN_IMAGE_DIR;
		} else 	{
			return '../..'.DOMAIN_BAN_IMAGE_DIR.$image_name;
		}
	}
	/**
	 * get domain specific image dir path details
	 */
	public function domain_image_full_path($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return '..'.DOMAIN_IMAGE_DIR;
		} else 	{
			return '..'.DOMAIN_IMAGE_DIR.$image_name;
		}
	}
	
	public function domain_image_upload_path()
	{
		return DOMAIN_IMAGE_UPLOAD_DIR;
	}
	public function domain_gst_certificate_upload_path()
	{
		return DOMAIN_IMAGE_UPLOAD_DIR.'/certificates/';
	}
	public function domain_ban_image_upload_path()
	{
		return DOMAIN_BAN_UPLOAD_DIR;
	}
	/**
	 * get domain path details for upload dir
	 */
	public function domain_uploads()
	{
		return DOMAIN_UPLOAD_DIR; // no fall back needed as this will create issue later
	}

	/**
	 * get domain path details for upload dir
	 */
	public function domain_uploads_packages($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return '../..'.DOMAIN_PCKG_UPLOAD_DIR;
		} else 	{
			return '../..'.DOMAIN_PCKG_UPLOAD_DIR.$image_name;
		}
	}

	/**
	 * get domain specific image dir path details
	 */
	public function domain_upload_pckg_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_PCKG_UPLOAD_DIR;
		} else 	{
			return DOMAIN_PCKG_UPLOAD_DIR.$image_name;
		}
	}

	/**
	 * check if the theme view file exists
	 * @param $file_name string name of the file including path irrespective of template
	 */
	private function theme_body_view($file_locator)
	{
		//check if the file exists
		$temp_template_dir = $this->get_application_template();
		$theme_view_file = APPPATH.'views/template_list/'.$temp_template_dir.'/'.$file_locator.'.php';
		if (file_exists($theme_view_file) == true) {
			$theme_file_locator = 'template_list/'.$temp_template_dir.'/'.$file_locator;
		} else {
			$theme_file_locator = 'template_list/'.DEFAULT_TEMPLATE.'/'.$file_locator;
		}
		//otherwise get from default template list
		return $theme_file_locator;
	}

	/**
	 * check if the theme view file exists
	 * @param $file_name string name of the file including path irrespective of template
	 */
	private function theme_template_view($file_locator)
	{
		//check if the file exists
		$temp_template_dir = $this->get_application_template();
		$theme_view_file = APPPATH.'views/template_list/'.$temp_template_dir.'/'.$file_locator.'.php';
		if (file_exists($theme_view_file) == true) {
			$theme_file_locator = 'template_list/'.$temp_template_dir.'/'.$file_locator;
		} else {
			$theme_file_locator = 'template_list/'.DEFAULT_TEMPLATE.'/'.DEFAULT_TEMPLATE;
		}
		//otherwise get from default template list
		return $theme_file_locator;
	}

	/**
	 * Load View to the user
	 *
	 * @param $template_name string name of the template which has to be loaded
	 * @param $page_data	 array	data which has to be passed to the view page
	 * @param $body_name	 string name of the page which has to be loaded
	 *
	 */
	public function view($body_name='', $page_data='', $template_name='')
	{
		if (is_string($body_name)) {
			$this->set_default_page_details($template_name, $body_name);
			//Internally call isolated view page
			$page_data['body'] = $this->isolated_view($this->page_name, $page_data, '');
		} else {
			$page_data['body'] = $body_name['body_data'];
		}
		$this->CI->load->view($this->theme_template_view($this->get_application_template()), $page_data);
		//$this->CI->load->view('template_list/'.$this->template, $page_data);
	}

	/**
	 * Load complete pure view of a web page by considering theme
	 * @param string $page_name Name of the page which has to be loaded
	 * @param array $page_data  Data that has to be passed to the view page
	 */
	public function isolated_view($page_name='', $page_data='', $template_name='')
	{
		$this->set_default_page_details($template_name, $page_name);
		return $this->CI->load->view($this->theme_body_view($this->page_name), $page_data, true);//passing
	}
	/**
	 * GET THE DOMAIN LOGO
	 Balu A (26-05-2015) - 27-05-2015
	 */
	public function get_domain_logo()
	{
		$CI = &get_instance();
		//check if b2b user
		$domain_auth_id = get_domain_auth_id();
		$domain_auth_key = get_domain_key();
		if(intval($domain_auth_id) >0 && empty($domain_auth_key) == false) {
			//B2B Can Override Logo
			if (isset($CI->entity_user_type) == true && $CI->entity_user_type == B2B_USER) {
				$logo_details = $CI->custom_db->single_table_records('b2b_user_details', 'logo as domain_logo', array('user_oid' => intval($this->CI->entity_user_id)));
			}
			if (@$logo_details['data'][0]['domain_logo'] == '') {
				$domain_logo = $CI->application_domain_logo;
			} else {
				$domain_logo = $logo_details['data'][0]['domain_logo'];
			}
			if (empty($domain_logo) == false) {
				return $domain_logo;
			}
		}
	}
	/*
	for uploading the promo code images
	*/
	public function domain_promo_image_upload_path()
	{
		return DOMAIN_PROMO_UPLOAD_DIR;
	}
	/**
	 * get domain specific image dir path details
	 */
	public function domain_promo_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_PROMO_IMAGE_DIR;
		} else 	{
			return DOMAIN_PROMO_IMAGE_DIR.$image_name;
		}
	}
	/*for uploading the top airline logo*/
	public function domain_top_airline_upload_path(){
		return DOMAIN_TOP_AIRLINE_UPLOAD_DIR;
	}
	/**
	 * get domain specific image dir path details
	 */
	public function domain_top_airline_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_TOP_AIRLINE_IMAGE_DIR;
		} else 	{
			return DOMAIN_TOP_AIRLINE_IMAGE_DIR.$image_name;
		}
	}
	/*for uploading the tour style image*/
	public function domain_tour_style_upload_path(){
		return DOMAIN_TOUR_STYLE_UPLOAD_DIR;
	}
	/**
	 * get domain specific image dir path details
	 */
	public function domain_tour_style_images($image_name='')
	{
		if (empty($image_name) == true || is_string($image_name) == false) {
			return DOMAIN_TOUR_STYLE_IMAGE_DIR;
		} else 	{
			return DOMAIN_TOUR_STYLE_IMAGE_DIR.$image_name;
		}
	}
}
?>
