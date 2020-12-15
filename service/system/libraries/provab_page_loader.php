<?php
include 'js_loader.php';
/**
 * Provab Provab_Page_Loader Class
 *
 * Handle all the Forms in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Arjun J<arjun.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Provab_Page_Loader extends Js_Loader {

	public function __construct($page_configuration='')
	{
		parent::__construct($page_configuration);
	}

	/**
	 * generate form
	 * @param array $conf page configuration
	 * @param string $name name of form
	 */
	function generate_form($name='', $default_data='', $disabled='')
	{	
		if (empty($name)) {
			$name = $this->name;
		}
		if (empty($disabled)) {
			$disabled = (isset($this->disabled[$name]) ? $this->disabled[$name] : false);
		}

		if (isset($this->config['form'][$name]['sections']) == false and valid_array($this->config['form'][$name]['sections']) == false) {
			echo get_message();
			exit;
		} else {
			$this->enable_javascript($name);
		}
		
		//echo print_r($this->config['form'][$name]['sections']);exit;
		/********************************************* form attribute Start *********************************************/
		/**
		 * label 	   : default true , false to remove
		 * placeholder : default true , false to remove
		 * help_text   : default true , false to remove
		 * method	   : default POST , GET   to change
		 * autocomplete: default false, true to enable
		 * action	   : default post to current method, pass string to override
		 */
		$label 		 = isset($this->config['form'][$name]['form_header']['label']) 			   ? trim($this->config['form'][$name]['form_header']['label'])		  : true;
		$placeholder = isset($this->config['form'][$name]['form_header']['placeholder']) 	   ? trim($this->config['form'][$name]['form_header']['placeholder']) : true;
		$help_text   = isset($this->config['form'][$name]['form_header']['help_text']) 		   ? trim($this->config['form'][$name]['form_header']['help_text'])   : true;
		$action   	 = (empty($this->config['form'][$name]['form_header']['action']) == false) ? trim($this->config['form'][$name]['form_header']['action']) 	  : $_SERVER['REQUEST_URI'];
		$autocomp    = isset($this->config['form'][$name]['form_header']['autocomplete']) 	   ? trim($this->config['form'][$name]['form_header']['autocomplete']): 'off';
		$target   	 = isset($this->config['form'][$name]['form_header']['target']) 		   ? trim($this->config['form'][$name]['form_header']['target']) 	  : '_self';
		$form_class  = isset($this->config['form'][$name]['form_header']['class']) 			   ? $this->config['form'][$name]['form_header']['class']		 	  : array('form-horizontal');
		$form_class = is_array($form_class) ? $form_class : (array)$form_class;
		if (isset($this->config['form'][$name]['form_header']['method']) == false || strcasecmp($this->config['form'][$name]['form_header']['method'], 'POST') == 0) {
			$method  = 'POST';
			$enctype = 'multipart/form-data';
		} else {
			$method  = 'GET';
			$enctype = 'application/x-www-form-urlencoded';
		}
		$FID = $GLOBALS['CI']->encrypt->encode($name);
		$form_open   = '<form name="'.$name.'" autocomplete="'.$autocomp.'" action="'.$action.'" method="'.$method.'" enctype="'.$enctype.'" id="'.$name.'" role="form" class="'.implode($form_class).'">
						<input type="hidden" name="FID" value="'.$FID.'">';
		$form_close  = '</form>';
		/********************************************* form attribute End *********************************************/

		/********************************************* form section start *********************************************/
		/**
		 * type : default text, pass to override("text", "hidden", "date", "file", "number")
		 * lang_line_code : indicates label, placeholder, help text code(all 3 should be same and integer) - -1 to remove label
		 * class : default name of element, array to add additional class
		 */
		$sections = '';
		foreach ($this->config['form'][$name]['sections'] as $sec_key => $sec_val) {
			$input = '';
			foreach ($sec_val['elements'] as $ele_key => $ele_val) {
				$tmp_input = '';
				if (isset($this->config['inputs'][$ele_val]) == false) {
					$this->config['inputs'][$ele_val] = '';
				}
				if (isset($this->config['inputs'][$ele_val]['type']) == false) {
					$this->config['inputs'][$ele_val]['type'] = 'text';
				}
				/** Input element start	 **/
				if (in_array($this->config['inputs'][$ele_val]['type'], array('text', 'hidden', 'radio', 'email', 'password', 'date', 'number', 'file', 'textarea', 'select', 'checkbox')) == true) {
					if(isset($this->config['inputs'][$ele_val]['mandatory']) == false || $this->config['inputs'][$ele_val]['mandatory'] == true) {
						$ip_required 	   = 'required';
						$ip_required_label = '<span class="text-danger">*</span>';
					} else {
						$ip_required	   = '';
						$ip_required_label = '';
					}
					if(isset($this->config['inputs'][$ele_val]['readonly']) == false || $this->config['inputs'][$ele_val]['readonly'] == false) {
						$ip_readonly = ' ';
					} else {
						$ip_readonly = 'readonly';
					}
					/** For Float Numbers**/
					if(isset($this->config['inputs'][$ele_val]['step']) == TRUE) {
						$ip_decimal = "step='any'";
					} else {
						$ip_decimal = ' ';
					}
					$this->config['inputs'][$ele_val]['class'][] = $ele_val; 									// add default class
					/** Label start	make -1 to ignore below section **/
					$lang_line_code  = isset($this->config['inputs'][$ele_val]['label_line_code']) ? intval($this->config['inputs'][$ele_val]['label_line_code']) : 0;
					if ($lang_line_code > -1) {
						$ip_label 		  = ($label == true) 	  ? ($lang_line_code > 0) ? get_label($lang_line_code) 		 : $ele_val : false;
						$ip_placeholder   = ($placeholder == true) ? ($lang_line_code > 0) ? get_placeholder($lang_line_code) : $ele_val : false;
						$ip_help_text 	  = ($help_text == true)   ? ($lang_line_code > 0) ? get_help_text($lang_line_code)   : $ele_val : false;
						if ($ip_help_text) {
							$ip_help_text = generate_help_text($ip_help_text);
						}
					} else {
						$ip_label = $ip_placeholder = $ip_help_text = false;
					}
					/** Label End make -1 to ignore below section **/
					/** Start Seperate Element Type Handling **/
					switch (strtolower($this->config['inputs'][$ele_val]['type']))
					{
						case 'text':
						case 'email':
						case 'password':
						case 'date':
						case 'number':
						case 'file':
							if ($this->config['inputs'][$ele_val]['type'] == 'file') {
								$this->config['inputs'][$ele_val]['class'][] = $ele_val; 									// add default class
								$ip_spl_attributes = ' accept="'.(isset($this->config['inputs'][$ele_val]['accept']) ? $this->config['inputs'][$ele_val]['accept'] : 'image/*').'" ';
							} else {
								$ip_spl_attributes = '';
								$this->config['inputs'][$ele_val]['class'][] = 'form-control'; 								// add default class
								//$this->config['inputs'][$ele_val]['class'][] = ''; 									// add default class
							}
							if(empty($this->config['inputs'][$ele_val]['DT']) == false){
								$ip_spl_attributes = ' DT= "'.$this->config['inputs'][$ele_val]['DT'].'"';
							}
							//CHECK IP VALIDATION STATUS
							$current_form_validation = $this->get_current_form_validation($name);
							if (form_error($ele_val) != false && $current_form_validation == true) {
								$invalidIp = 'invalid-ip';
								$form_error = form_error($ele_val, '<p class="text-danger">', '</p>');
							} else {
								$invalidIp = '';
								$form_error = '';
							}
							$tmp_input = '
							<div class="form-group">
								<label class="col-sm-3 control-label" for="'.$ele_val.'" form="'.$name.'">'.$ip_label.$ip_required_label.'</label>
								<div class="col-sm-6">
								<input '.$this->check_ip_status($ele_val, $disabled).' value="'.$this->get_default_data($ele_val, $default_data).'"  name="'.$ele_val.'" '.$ip_spl_attributes.' '.$ip_readonly.' '.$ip_required.'  type="'.$this->config['inputs'][$ele_val]['type'].'"  placeholder="'.$ip_placeholder.'" class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'"  id="'.$ele_val.'"  '.$ip_help_text.' '.$ip_spl_attributes.' '.$ip_decimal.'>
								'.$form_error.'</div>
								
							</div>';
							break;
						case 'hidden' :
							$invalidIp = (form_error($ele_val) != false) ? 'invalid-ip' : '';
							/** HIDDEN ELEMENTS **/
							$this->config['inputs'][$ele_val]['class'][] = 'hiddenIp';
							$tmp_input = '<input name="'.$ele_val.'"  type="'.$this->config['inputs'][$ele_val]['type'].'"   id="'.$ele_val.'"  class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'" '.$ip_readonly.' '.$ip_required.' value="'.$this->get_default_data($ele_val, $default_data).'" />';
							break;
						case 'radio':
							$invalidIp = (form_error($ele_val) != false) ? 'invalid-ip' : '';
							/** RADIO **/
							if ($ip_label) {
								$rad_description = '<label class="col-sm-3 control-label" for="'.$ele_val.'" form="'.$name.'">'.$ip_label.$ip_required_label.'</label>';
							} else {
								$rad_description = '';
							}

							$this->config['inputs'][$ele_val]['class'][] = 'radioIp'; 									// add default class
							/** CHECK DATA SOURCE **/
							$sel_data_list = $this->get_option_list((isset($this->config['inputs'][$ele_val]['source']) ? $this->config['inputs'][$ele_val] : false));
							if (empty($sel_data_list) == false and valid_array($sel_data_list) == true) {
								$rads = '';
								$default_rad = (isset($default_data[$ele_val]) ? $default_data[$ele_val] : false);
								foreach($sel_data_list as $rad_key => $rad_val) {
									if ($default_rad == $rad_key) {
										$checked = 'checked="checked"';
									} else {
										$checked = '';
									}
									$rads .= '
									<label class="radio-inline" for="'.$name.$ele_val.$rad_key.'">
			  							<input '.$ip_required.' '.$checked.' DT="'.(isset($this->config['inputs'][$ele_val]['DT']) ? $this->config['inputs'][$ele_val]['DT'] : '').'"  class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'" type="radio"  name="'.$ele_val.'" id="'.$name.$ele_val.$rad_key.'" value="'.$rad_key.'">'.get_label($rad_val).'
									</label>';
								}
								$tmp_input = '<div class="radio">'.$rad_description.$rads.form_error($ele_val, '<div class="col-sm-6 pull-right"><p class="text-danger">', '</p></div>').'</div>';
							}
							break;
						case 'checkbox':
							$config_inputs_check_box = strtolower($this->config['inputs'][$ele_val]['type']);
							$invalidIp = (form_error($ele_val) != false) ? 'invalid-ip' : '';
							/** Check Box **/
							if ($ip_label) {
								$check_description = '<label class="col-sm-3 control-label" for="'.$ele_val.'" form="'.$name.'">'.$ip_label.$ip_required_label.'</label>';
							} else {
								$check_description = '';
							}
							$this->config['inputs'][$ele_val]['class'][] = 'checkboxIp'; 									// add default class
							/** CHECK DATA SOURCE **/
							$sel_data_list = $this->get_option_list((isset($this->config['inputs'][$ele_val]['source']) ? $this->config['inputs'][$ele_val] : false));
							if (empty($sel_data_list) == false and valid_array($sel_data_list) == true) {
								$checks = '';
								$default_check = (isset($default_data[$ele_val]) ? $default_data[$ele_val] : false);
								/** Multiple Select **/
								if(isset($default_data[$ele_val]) == true and is_array($default_data[$ele_val])) {
									foreach($sel_data_list as $check_key => $check_val) {
										if (in_array($check_key, $default_check)) {
											$checked = 'checked="checked"';
										} else {
											$checked = '';
										}
										$checks .= '
											<label class="radio-inline" for="'.$name.$ele_val.$check_key.'">
					  							<input  '.$this->check_ip_status($ele_val, $disabled, $config_inputs_check_box).' '.$ip_required.' '.$checked.' DT="'.(isset($this->config['inputs'][$ele_val]['DT']) ? $this->config['inputs'][$ele_val]['DT'] : '').'"  class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'" type="checkbox"  name="'.$ele_val.'[]" id="'.$name.$ele_val.$check_key.'" value="'.$check_key.'">'.get_label($check_val).'
											</label>';
									}

								} else { /** Single Select **/
										
									foreach($sel_data_list as $check_key => $check_val) {
										if ($default_check == $check_key) {
											$checked = 'checked="checked"';
										} else {
											$checked = '';
										}
										$checks .= '
											<label class="radio-inline" for="'.$name.$ele_val.$check_key.'">
					  							<input  '.$this->check_ip_status($ele_val, $disabled, $config_inputs_check_box).' '.$ip_required.' '.$checked.' DT="'.(isset($this->config['inputs'][$ele_val]['DT']) ? $this->config['inputs'][$ele_val]['DT'] : '').'"  class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'" type="checkbox"  name="'.$ele_val.'[]" id="'.$name.$ele_val.$check_key.'" value="'.$check_key.'">'.get_label($check_val).'
											</label>';
									}
								}



								$tmp_input = '<div class="checkbox">'.$check_description.$checks.form_error($ele_val, '<div class="col-sm-6 pull-right"><p class="text-danger">', '</p></div>').'</div>';
							}
							break;
						case 'select' :
							$invalidIp = (form_error($ele_val) != false) ? 'invalid-ip' : '';
							/** SELECT **/
							if ($ip_label) {
								$sel_description = '<label class="col-sm-3 control-label" for="'.$ele_val.'" form="'.$name.'">'.$ip_label.$ip_required_label.'</label>';
							} else {
								$sel_description = '';
							}
							$this->config['inputs'][$ele_val]['class'][] = 'form-control';									// add default class
							/** CHECK DATA SOURCE **/
							$sel_data_list = $this->get_option_list((isset($this->config['inputs'][$ele_val]['source']) ? $this->config['inputs'][$ele_val] : false));
							$select = '<select '.$this->check_ip_status($ele_val, $disabled).'  '.$ip_required.' '.$ip_readonly.' DT="'.(isset($this->config['inputs'][$ele_val]['DT']) ? $this->config['inputs'][$ele_val]['DT'] : '').'"  name="'.$ele_val.'" class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'" id="'.$ele_val.'"  '.$ip_help_text.'>';
							$select .= '<option value="INVALIDIP">'.get_label(24).'</option>';
							$select .= generate_options($sel_data_list, (isset($default_data[$ele_val]) ? array($default_data[$ele_val]) : array()), true);
							$select .= '</select>';
							if($ele_val == 'state'){
								$class = 'stateclass';
							}
							else{
								$class = '';
							}
							$tmp_input = '<div class="form-group '.$class.'">
							'.$sel_description.'<div class="col-sm-6">'.$select.form_error($ele_val, '<p class="text-danger">', '</p>').'</div>
						</div>';
							break;
						case 'textarea' :
							$invalidIp = (form_error($ele_val) != false) ? 'invalid-ip' : '';
							$this->config['inputs'][$ele_val]['class'][] = 'form-control'; 									// add default class
							$tmp_input = '<div class="form-group">
						<label class="col-sm-3 control-label" for="'.$ele_val.'" form="'.$name.'">'.$ip_label.$ip_required_label.'</label>
						<div class="col-sm-6"><textarea '.$this->check_ip_status($ele_val, $disabled).' '.$ip_required.' '.$ip_readonly.' DT="'.(isset($this->config['inputs'][$ele_val]['DT']) ? $this->config['inputs'][$ele_val]['DT'] : '').'"  name="'.$ele_val.'" 
						id="'.$ele_val.'" rows="3" class="'.$invalidIp.' '.(implode(' ', $this->config['inputs'][$ele_val]['class'])).'"
						>'.$this->get_default_data($ele_val, $default_data).'</textarea>'.form_error($ele_val, '<p class="text-danger">', '</p>').'</div>
						</div>';
							break;

					}
					if (in_array('form-vertical', $form_class)) {
						$input .= '<div class="row">'.$tmp_input.'</div>';
					} else {
						$input .= $tmp_input;
					}
					/** End Seperate Element Type Handling **/
				}
				/*} else {
				 echo get_message();
				 exit;
				 }*/
			}
			if (isset($sec_val['fieldset']) == true) {
				$form_fieldset_open = '<fieldset form="'.$name.'"><legend class="">'.get_legend($sec_val['fieldset']).'</legend>';
				$form_fieldset_close = '</fieldset>';
			} else {
				$form_fieldset_open = $form_fieldset_close = '';
			}
			$sections .= $form_fieldset_open.$input.$form_fieldset_close;

		}
		/********************************************* form section End   *********************************************/
		/********************************************* form button Start   *********************************************/
		//form button footer
		if (is_array($this->config['form'][$name]['form_footer']) == true) {
			$buttons = '';
			foreach ($this->config['form'][$name]['form_footer'] as $b_k => $b_v) {
				$key = is_array($b_v) ? $b_k : $b_v ;
				switch ($key) {
					case 'submit' :
						$b_type = 'submit';
						$b_id = $name.'_submit';
						$b_class = ' btn btn-success ';
						$b_label = (isset($b_v['lang_line_code']) && intval($b_v['lang_line_code']) > 0) ? $b_v['lang_line_code'] : 27;
						break;
					case 'update' :
						$b_type = 'submit';
						$b_id = $name.'_submit';
						$b_class = ' btn btn-success ';
						$b_label = (isset($b_v['lang_line_code']) && intval($b_v['lang_line_code']) > 0) ? $b_v['lang_line_code'] : 28;
						break;
					case 'reset' :
						$b_type = 'reset';
						$b_class = ' btn btn-warning ';
						$b_id = $name.'_reset';
						$b_label = (isset($b_v['lang_line_code']) && intval($b_v['lang_line_code']) > 0) ? $b_v['lang_line_code'] : 7;
						break;
					case 'cancel' :
						$b_type = 'button';
						$b_class = ' btn btn-danger ';
						$b_id = $name.'_cancel';
						$b_label = (isset($b_v['lang_line_code']) && intval($b['lang_line_code']) > 0) ? $b_v['lang_line_code'] : 8;
						break;
					default :
						$b_type = 'button';
						$b_class = ' btn btn-info ';
						$b_id = $name.'_custom';
						$b_label = intval($b_v['lang_line_code']);
						break;
				}
				$b_class_cust = (isset($b_v['class']) == true && valid_array($b_v['class']) == true) ? implode($b_v['class']) : '';
				$buttons .= '<button type="'.$b_type.'" id="'.$b_id.'" class="'.$b_class.$b_class_cust.'">'.get_label($b_label).'</button>';
			}
			$buttons = '<div class="form-group"><div class="col-sm-8 col-sm-offset-4">'.$buttons.'</div></div>';

		} else {
			$buttons = '';
		}
		/********************************************* form button End   *********************************************/

		//Final Form
		$current_form_validation = $this->get_current_form_validation($name);
		if (validation_errors() != false && $current_form_validation == true) {
			echo $form_status = '
			<div class="panel panel-danger clearfix">
				<div class="panel-heading danger">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					'.get_legend('FFL004').'
					<div class="pull-right">
					'.provab_help_text(get_legend('FFL005')).'
					</div>
				</div>
			</div>';
		}
		return get_compressed_output($form_open.$sections.$buttons.$form_close);
	}

	/**
	 * get the default data
	 */
	function get_default_data($name, $data)
	{
		return (isset($data[$name]) ? $data[$name] : set_value($name));
	}

	/**
	 * get the default data
	 */
	function check_ip_status($name, $data, $config_inputs_type='')
	{
		if($config_inputs_type === 'checkbox'){
			$ip_status = ((valid_array($data) && in_array($name, $data)) ? 'readonly="readonly" disabled="disabled" style="box-shadow:none; background:#e4e4e4"' : '');
		} else {
			$ip_status = ((valid_array($data) && in_array($name, $data)) ? 'readonly="readonly" style="box-shadow:none; background:#e4e4e4"' : '');
		}
		return $ip_status;
	}

	/**
	 * get option list for form
	 * @param array $attributes
	 */
	function get_option_list($attributes)
	{

		switch($attributes['source'])
		{
			case 'enum' :
				/**
				 *  'source' => 'enum', 'source_id' => 'status'
				 **/
				$sel_data_list = get_enum_list($attributes['source_id']);
				break;
			case 'db':
				/**
				 * data from class
				 *  'source' => 'db', 'source_location'  => 'class_name::method_name'
				 * data from function
				 *  'source' => 'db', 'source_location'  => 'function_name'
				 **/
				if (isset($attributes['source_location']) == true) {
					$sel_data_list = $this->get_db_data($attributes['source_location']);
				} else {
					$sel_data_list = '';
				}
				break;
			case 'custom':
				/**
				 * data from class
				 *  'source' => 'custom', 'source_location'  => 'class_name::method_name'
				 * data from function
				 *  'source' => 'custom', 'source_location'  => 'function_name', 'params => array()
				 **/
				if (isset($attributes['source_location']) == true) {
					$params = isset($attributes['params']) ? $attributes['params'] : array();
					$sel_data_list = $this->get_custom_data($attributes['source_location'], $params);
				} else {
					$sel_data_list = '';
				}
				break;
			default :
				/**
				 * only for Radio and checkbox
				 * CUSTOM LIST
				 *  'options' => array(array(ACTIVE => 11), array(INACTIVE => 12))
				 *
				 *  ENUM
				 *  'source' => 'enum', 'source_id' => 'status'
				 *
				 **/
				$sel_data_list = $attributes['options'];
				break;
		}
		return $sel_data_list;
	}

	/** ADD/REMOVE VALIDATOR to automatic list
	 *  $this->current_page->auto_validator['test'] = 'required';
	 *
	 *  //unset particular validation
	 *  unset($this->current_page->auto_validator['test'])
	 **/
	function set_auto_validator($form_name='', $disabled_list=array())
	{
		$form_name = empty($form_name) ? $GLOBALS['CI']->encrypt->decode($_POST['FID']) : '';
		if (valid_array($disabled_list) == false and empty($this->disabled[$form_name]) == false) {
			$disabled_list = $this->disabled[$form_name];
		}
		/** ADD/REMOVE VALIDATOR to automatic list for form input elements
		 *  $this->current_page->auto_validator['test'] = 'required';
		 **/
		if (isset($this->auto_validator) == true and valid_array($this->auto_validator) == true) {
			if (valid_array($this->config['form'][$form_name]) == true) {
				foreach ($this->config['form'][$form_name]['sections'] as $k => $v) {
					if (valid_array($v['elements'])) {
						foreach ($v['elements'] as $i_k => $i_v) {
							//SKIP DISABLED ELEMENTS
							if (in_array($i_v, $disabled_list) == false) {
								$rules = (isset($this->auto_validator[$i_v]) ? $this->auto_validator[$i_v] : false);
								if (empty($rules) == false) {
									//get custom validation rules
									if (isset($this->config['inputs']) == true && isset($this->config['inputs'][$i_v]) == true && isset($this->config['inputs'][$i_v]['label_line_code'])) {
										$label = get_label($this->config['inputs'][$i_v]['label_line_code']);
									} else {
										$label = $k;
										$help_text = '';
									}
								} elseif(isset($this->config['inputs'][$i_v]['mandatory']) == false || $this->config['inputs'][$i_v]['mandatory'] != false) {
									//if mandatory just check out required
									$label = $k;
									$help_text = '';
									$rules = 'trim|required|max_length[45]';
								} else {
									//LETS NOT VALIDATE WHEN THE ELEMENT IS NOT MANDATORY
									continue;
								}
								$GLOBALS['CI']->form_validation->set_rules($i_v, $label, $rules);
							}
						}
					}

				}
			} else {
				//LETS LOG THE EVENT
				redirect('security/log_event?ops=invalid form');
			}
		}
	}

	/**
	 * get data from db calls
	 * @param $source_location db call location string ex: table_api::get_users
	 */
	function get_db_data($source_location)
	{
		// Dynamic calling of functions
		$source_list = explode('::', $source_location);
		if (valid_array($source_list) == true and count($source_list) == 2) {
			$data_list = $GLOBALS['CI']->$source_list[0]->$source_list[1]();
		} else {
			$data_list = $source_list[0]();
		}
		return $data_list;
	}

	/**
	 * get data from custom calls
	 * @param unknown_type $source_location
	 * @param unknown_type $params
	 */
	
	function get_custom_data($source_location, $params)
	{
		// Dynamic calling of functions
		$source_list = explode('::', $source_location);
		if (valid_array($source_list) == true and count($source_list) == 2) {
			$data_list = $GLOBALS['CI']->$source_list[0]->$source_list[1]($params);
		} else {
			$data_list = $source_list[0]($params);
		}
		return $data_list;
	}

	function get_current_form_validation($name='') {
		if((isset($_POST['FID']) == true && strcmp($name, $GLOBALS['CI']->encrypt->decode($_POST['FID'])) == 0)==TRUE ){
			return TRUE;
		} else {
			return FALSE;
		}

	}

	/**
	 * show help text on page so user know what to do on page
	 */
	function create_page_usage_helper()
	{
		$help_text = func_get_args();
		if (is_string($help_text[0]) && strlen($help_text[0]) > 0) {
			echo '
			<div class="media alert panel-sky">
				<div class="media-left">
					<img class="media-object" src="'.$GLOBALS['CI']->template->template_images('FAQ.gif').'" alt="HELP">
				</div>
				<div class="media-body">
					<h4 class="media-heading"><i class="glyphicon glyphicon-info-sign"></i> Read Me</h4>';
			foreach ($help_text as $k => $v) {
				if (is_string($v) && strlen($v) > 0) {
					echo '<p class="">'.strtoupper($v).'</p>';
				}
			}
			echo '</div>
			</div>';
		}
	}
	/**
	 * Jaganath
	 * Includes Basic CSS Files
	 */
	function header_css_resource()
	{
		include COMMON_SHARED_CSS_RESOURCE;
	}
/**
	 * Jaganath
	 * Includes Basic JS Files
	 */
	function header_js_resource()
	{
		include COMMON_SHARED_JS_RESOURCE;
	}
}
