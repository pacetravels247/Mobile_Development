<?php

/**
 * Provab Currency Class
 *
 * Handle all the currency conversion in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Master_currency {

    public $to_currency;   //currency to which the conversion is done
    var $conversion_rate; // default conversion rate
    protected $from_currency; //currency from which the conversion is done
    //private $CI;		//access CI object
    protected $markup_type; //type of the markup
    protected $module_name; //name of the module for which the current object is used
    protected $level_one_markup; //LEVEL ODD(1, 3...) nobody(in b2c always set to false), admin(in b2b set true to add admin markup)
    protected $current_domain_markup; //LEVEL EVEN(2, 4...) admin(in b2c set true to add admin markup), agent(in b2b set true to add agent markup)
    public $conversion_cache;
    protected $currency_symbol;
    protected $convinence_fees_row;

    public function __construct($params = array()) {
        $this->to_currency = isset($params['to']) ? $params['to'] : UNIVERSAL_DEFAULT_CURRENCY;
        $this->from_currency = isset($params['from']) ? $params['from'] : COURSE_LIST_DEFAULT_CURRENCY_VALUE;
        $this->markup_type = isset($params['markup_type']) ? $params['markup_type'] : MARKUP_VALUE_PERCENTAGE;
        $this->module_name = isset($params['module_type']) ? $params['module_type'] : 'flight';
        $this->conversion_cache[$this->from_currency . $this->to_currency] = (($this->from_currency == $this->to_currency) ? 1 : $this->getConversionRate());
        // debug($this->conversion_cache);exit;
        //$this->set_currency_symbol(array($this->from_currency));
        //$this->getConversionRate();
    }

    /**
     * set currency conversion symbol
     */
    function set_currency_symbol($currency_code) {
        $CI = &get_instance();
        $cond = '';
        if (is_array($currency_code) == true) {
            //set multiple
            $currency = '';
            foreach ($currency_code as $k => $v) {
                if (isset($this->currency_symbol[$v]) == false) {
                    $currency .= $CI->db->escape($v) . ',';
                }
            }
            if (empty($currency) == false) {
                $cond = 'country IN (' . substr($currency, 0, -1) . ')';
            }
        } else {
            //single force to multiple
            if (isset($this->currency_symbol[$currency_code]) == false) {
                $cond = 'country = ' . $CI->db->escape($currency_code);
            }
        }

        if (empty($cond) == false) {
            $currency_data = $CI->db->query($query = ' SELECT * FROM currency_converter WHERE ' . $cond)->result_array();
            foreach ($currency_data as $k => $v) {
                if (empty($v['currency_symbol']) == false) {
                    $this->currency_symbol[$v['country']] = $v['currency_symbol'];
                } else {
                    $this->currency_symbol[$v['country']] = $v['country'];
                }
            }
        }
    }

    /**
     * return currency symbol for currency
     */
    function get_currency_symbol($currency) {
        if (is_string($currency)) {
            if (isset($this->currency_symbol[$currency]) == false) {
                $this->set_currency_symbol($currency);
            }
            return $this->currency_symbol[$currency];
        } else {
            return false;
        }
    }

    /**
     *  Balu A
     * forcefully change conversion Rate
     * @param unknown_type $conversion_rate
     */
    public function setConversionRate($conversion_rate) {
        $this->conversion_cache[$this->from_currency . $this->to_currency] = $conversion_rate;
    }

    /**
     *  Balu A
     * get the conversion rate
     * @param $from from currency
     * @param $to   to currency
     */
    public function getConversionRate($override_conversion_rate = false, $from_currency = '', $to_currency = '') {
        if (empty($from_currency) == false) {
            $from = $from_currency;
            $this->from_currency = $from;
        } else {
            $from = $this->from_currency;
        }

        if (empty($to_currency) == false) {
            $to = $to_currency;
            $this->to_currency = $to;
        } else {
            $to = $this->to_currency;
        }

        /* if ($override_conversion_rate == true || empty($this->conversion_cache[$from.$to]) == true) {
          $url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$from.$to.'=X&f=nl1';
          $handle = fopen($url, 'r');
          if ($handle) {
          $currency_data = fgetcsv($handle);
          fclose($handle);
          }
          if ($currency_data != '') {
          if (isset($currency_data[0]) == true and empty($currency_data[0]) == false and isset($currency_data[1]) == true and empty($currency_data[1]) == false) {
          $this->conversion_cache[$from.$to] = floatval($currency_data[1]);
          }
          } else {
          //get it from database
          $this->conversion_cache[$from.$to] = 1;
          }
          } */
        $currency_conversion_value = $this->currency_conversion_value($override_conversion_rate, $from, $to);
        return $currency_conversion_value;
    }

    /**
     * Balu A
     * @param unknown_type $override_conversion_rate
     * @param unknown_type $from_currency
     * @param unknown_type $to_currency
     */
    public function currency_conversion_value($override_conversion_rate = false, $from, $to) {
        if ($override_conversion_rate == true || empty($this->conversion_cache[$from . $to]) == true) {
            if ($from != $to) {
                $from_Currency = urlencode($from);
                $to_Currency = urlencode($to);
                $encode_amount = urlencode(1);

                $get = file_get_contents("http://prod.services.travelomatix.com/webservices/index.php/rest/currecny_value_details?amount=$encode_amount&from=$from_Currency&to=$to_Currency");
                // debug($get);exit;
                $details = json_decode($get);

                $converted_currency = $details->currency_value;
                if ($converted_currency != '') {

                    $this->conversion_cache[$from . $to] = floatval($converted_currency);
                } else {
                    //get it from database		
                    $this->conversion_cache[$from . $to] = 1;
                }
            } else {
                $this->conversion_cache[$from . $to] = 1;
            }
        }
        return $this->conversion_cache[$from . $to];
    }

    /**
     * Balu A
     * @param $value
     */
    function force_currency_conversion($value, $from = '', $to = '') {
      
     # echo $from.$to;exit;
        $from = (empty($from) == false ? $from : $this->from_currency);
        $to = (empty($to) == false ? $to : $this->to_currency);
        return array('default_value' => ($value * $this->conversion_cache[$from . $to]), 'default_currency' => $to);
    }

    /**
     * Balu A
     * get currency
     * @param number $value    money which has to be converted
     * @param number $currency Currency to which we have to convert
     * @param number $markup   percentage of markup which has to be added
     *
     * @return array having country list
     */
    function get_currency($value, $add_markup = true, $parent_markup = false, $seller_markup = true, $markup_multiplier = 1, $specific_markup_config = array(), $is_domestic='') {

        $this->set_markup_details($parent_markup, $seller_markup, $is_domestic);
        $value = $value * $this->conversion_cache[$this->from_currency . $this->to_currency];
        if ($add_markup) {
         
            $value = $this->add_markup($value, $markup_multiplier, $parent_markup, $seller_markup, $specific_markup_config);
        }
        //add level one markup and then level two markup
        return array('default_value' => number_format($value['value'], 2, '.', ''), 'default_currency' => $this->to_currency,'original_markup' => $value['original_markup'],'markup_type' => $value['markup_type'], 'hotel_agent_total_markup'=>$value["hotel_agent_total_markup"]);
    }

    /**
     * Add markup to the value passed
     * @param number $value
     */
    protected function add_markup($value, $markup_multiplier = 1, $parent_markup, $seller_markup, $specific_markup_config = array()) {
        // echo $markup_multiplier;exit;
        $hotel_agent_total_markup = 0;
        if (floatval($value) > 0) {
            $markup_list = array();
            if ($parent_markup == true && valid_array($this->level_one_markup) == true) {
                $markup_list[] = $this->level_one_markup;
            }
            if ($seller_markup == true && valid_array($this->current_domain_markup) == true) {
                $markup_list[] = $this->current_domain_markup;
                $hotel_agent_total_markup = $this->current_domain_markup["generic_markup_list"][0]["value"]*$markup_multiplier;
            }
            if (valid_array($markup_list) == true) {
                $specific_markup_config = @$specific_markup_config[0]; //FIXME
                foreach ($markup_list as $__k => $__v) {
                    $temp_markup_list = array();
                    if (valid_array($__v['specific_markup_list']) == true) {
                        if (valid_array($specific_markup_config) == true && isset($specific_markup_config['category']) == true && isset($specific_markup_config['ref_id']) == true && empty($specific_markup_config['ref_id']) == false) {
                            if (isset($__v['specific_markup_list'][$specific_markup_config['ref_id']]) == true && intval(($__v['specific_markup_list'][$specific_markup_config['ref_id']])) >= 0) {
                                $temp_markup_list = array($__v['specific_markup_list'][$specific_markup_config['ref_id']]);
                            }
                        }
                    }

                    if (valid_array($temp_markup_list) == false && valid_array($__v['generic_markup_list']) == true) {
                        $temp_markup_list = $__v['generic_markup_list'];
                    }
                    //  if( get_client_ip() == '27.59.145.131'){
                    //   echo $markup_multiplier;
                    //     debug($temp_markup_list);exit;
                    // }
                    // debug($temp_markup_list);exit;
                    if (valid_array($temp_markup_list)) {
                        foreach ($temp_markup_list as $__ik => $__iv) {
                          // echo $value;exit;
                         // debug($__iv);
                            $markup_value = 0;
                            switch ($__iv['value_type']) {
                                case 'percentage' :
                                    //Just need to calculate percentage of the values
                                    $markup_value = (($value / 100) * $__iv['value']);
                                    $original_markup = $__iv['value'];
                                    $markup_type = 'Pecentage';
                                    break;
                                case 'plus' :
                                    $original_markup = $__iv['value'];
                                    //convert value to required currency and then add the value
                                    $temp_conversion = $this->currency_conversion_value(false, $__iv['markup_currency'], $this->to_currency);
                                    $markup_value = (($__iv['value'] * $temp_conversion) * $markup_multiplier);
                                    $markup_type = 'Plus';
                                    break;
                            }
                            
                            // if( get_client_ip() == '27.59.145.131'){
                            //   echo $markup_value;exit;
                            // }
                           
                            $value = $value + $markup_value;
                        }
                    }
                }
            }
        }
		
        return array('original_markup' => $original_markup,'markup_type' => $markup_type, 'value' => $value, "hotel_agent_total_markup"=>$hotel_agent_total_markup);
        
    }

    /**
     * Balu A
     * Set markup details for the current module
     * level_1 Markup is not there
     * level_2 ----- Admin markup for B2C
     * level_3 ----- Admin markup for B2B(Agent)
     * level_4 ----- Agent markup for Himself
     */
    protected function set_markup_details($parent_markup = true, $seller_markup = true, $is_domestic='') {
        $CI = &get_instance();
        if ($parent_markup == true and empty($this->level_one_markup)) {
         
            //LEVEL EVEN(1, 3...)
            //odd nobody(b2c), admin(b2b)b2c user           
            $this->level_one_markup = $CI->private_management_model->get_markup($this->module_name, $is_domestic);            
        }

        if ($seller_markup == true and empty($this->current_domain_markup)) {
            
            //LEVEL EVEN(2, 4...)
            //even admin(b2c), agent(b2b)b2b user
          
            $this->current_domain_markup = $CI->domain_management_model->get_markup($this->module_name, $is_domestic);
            // debug($this->current_domain_markup);exit;
        }
    }

    /**
     * get entity markup
     */
    function get_entity_markup() {
        $CI = &get_instance();
        //b2b_markup
        $markup_data = $CI->general_model->get_entity_markup();
        if (valid_array($markup_data['data']) == true) {
            foreach ($markup_data['data'] as $k => $v) {
                $this->user_markup[$v['type']] = array('value' => $v['value'], 'value_type' => $v['value_type']);
            }
        } else {
            $this->user_markup = false;
        }
    }

    /**
     * Set Convenience Fees
     */
    function set_convenience_fees($search_id) {
        $CI = &get_instance();

        $this->convinence_fees_row = $CI->private_management_model->get_convinence_fees($this->module_name, $search_id);
       return  $this->convinence_fees_row;
        // debug( $this->convinence_fees_row);

    }

    /**
     * Get Convenience Fees
     */
    function get_convenience_fees() {
        return $this->convinence_fees_row;
    }

    /**
     * Convenience Fees
     * @param number $amount	Amount on which convinence fees should be added
     * @param number $search_id	Search ID to be used
     */
    function convenience_fees($amount, $search_id, $pax_count ='') {

        //Based on module name we have to add convenience fees
        $convinence_fees = 0;

        $this->set_convenience_fees($search_id);
       
        switch ($this->module_name) {
            case 'flight' : $convinence_fees = $this->airline_convinence_fees($amount, $this->convinence_fees_row, $search_id);
                //Calculate Convenience fees
                break;
            case 'hotel' : $convinence_fees = $this->hotel_convinence_fees($amount, $this->convinence_fees_row, $search_id);
                break;
            case 'sightseeing':
                $convinence_fees = $this->sightseeing_convinence_fees($amount, $this->convinence_fees_row, $search_id);
                break;
            case 'transferv1':
              $convinence_fees = $this->transfers_convinence_fees($amount, $this->convinence_fees_row, $search_id);
              break;
            case 'car':
                $convinence_fees = $this->car_convinence_fees($amount, $this->convinence_fees_row, $search_id);
                break;
            case 'bus':
                $convinence_fees = $this->bus_convinence_fees($amount, $this->convinence_fees_row, $search_id, $pax_count);
                break;
        }
        // echo "hererre".$convinence_fees;

        return $convinence_fees;
    }

    /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function airline_convinence_fees($amount, $convinence_fees_row, $search_id) {
        $CI = &get_instance();
        $convinence_fees = 0;
        $search_data = $CI->flight_model->get_safe_search_data($search_id);

        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            $convenience_fee_currency = $convinence_fees_row['convenience_fee_currency'];

            $pax_count = intval($search_data['data']['adult_config'] + $search_data['data']['child_config'] + $search_data['data']['infant_config']);

            if ($search_data['data']['trip_type'] == 'multicity') {
                $trip_type_multiplier = intval(count($search_data['data']['from']));
            } else if ($search_data['data']['trip_type'] == 'oneway') {
                $trip_type_multiplier = 1;
            } else {
                $trip_type_multiplier = 2;
            }
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count, $convenience_fee_currency);
            if ($convenience_value_type == 'plus') {
                $convinence_fees = $trip_type_multiplier * $convinence_fees;
            }
        }
        return $convinence_fees;
    }

    /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function hotel_convinence_fees($amount, $convinence_fees_row, $search_id) {
        $CI = &get_instance();
        $convinence_fees = 0;
        $search_data = $CI->hotel_model->get_safe_search_data($search_id);
        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            $pax_count = intval(array_sum($search_data['data']['adult_config']) + array_sum($search_data['data']['child_config']));
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
        }
        return $convinence_fees;
    }
    /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function sightseeing_convinence_fees($amount, $convinence_fees_row, $search_id) {
        $CI = &get_instance();
        $convinence_fees = 0;
        $pax_data = $search_id;
        
        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
        
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            $pax_count = 0;
            foreach ($pax_data as $key => $value) {
              $pax_count +=$value['count'];
            }           
            
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
        }
       
        return $convinence_fees;
    }

    /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function transfers_convinence_fees($amount, $convinence_fees_row, $search_id) {
        $CI = &get_instance();
        $convinence_fees = 0;
        $pax_data = $search_id;
        
        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
        
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            $pax_count = 0;
            foreach ($pax_data as $key => $value) {
              $pax_count +=$value['count'];
            }           
            
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
        }
      
        return $convinence_fees;
    }

       /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function car_convinence_fees($amount, $convinence_fees_row, $search_id) {

        $CI = &get_instance();
        $convinence_fees = 0;
        // $search_data = $CI->hotel_model->get_safe_search_data($search_id);
        // debug($pax_data);exit;
        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
        
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            $pax_count = 1;
            
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
        }
      
        return $convinence_fees;
    }

       /**
     * Calculate convenience fees sum according to search and settings
     * @param array $convinence_fees_row
     * @param number $search_id
     */
    protected function bus_convinence_fees($amount, $convinence_fees_row, $search_id, $pax_count) {
     
      // debug($convinence_fees_row);exit;
        $CI = &get_instance();
        $convinence_fees = 0;
        // $search_data = $CI->hotel_model->get_safe_search_data($search_id);
        // debug($pax_data);exit;
        if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
        
            $convenience_value_type = $convinence_fees_row['type'];
            $convenience_value = $convinence_fees_row['value']; //
            $per_pax_charge = $convinence_fees_row['per_pax']; //Used as final multiplier
            
            $convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
          // echo 'ZSDasdsa'.$convinence_fees;
       }
      
        return $convinence_fees;
    }

    /**
     * Update Convenience Fees in application
    */
    function get_instant_recharge_convenience_fees($amount, $method, $bank_code)
    {
       $CI = &get_instance();
      $condition = array("from_amount <=" => $amount, "to_amount >=" => $amount);
      if($method=="CC")
        $pm="credit_card";
      else if($method=="DC")
        $pm="debit_card";
      else if($method=="PPI")
        $pm="paytm_wallet";
      else{
        $pm="net_banking";
        $condition["bank_code"] = $bank_code;
      }
      if($method=="wallet")
      {
        $data["sf"] = 0;
        $data["pf"] = 0;
        $data["cf"] = 0;
        $data["total"] = 0;
        return $data;
      }
      $condition["payment_mode"] = $pm;

      $data = $CI->custom_db->single_table_records('instant_recharge_con_fees', "*",  $condition);
      // debug($data);
      // echo $CI->custom_db->db->last_query(); exit;

      if(isset($data["data"][0]))
      {
        $data = $data["data"][0];
        //Var @cf - convenience_fees
        if($data["value_type"] == "percentage"){
          $sf = ($amount/100)*$data['value'];
          $pf = ($amount/100)*$data['pace_fees'];
          $cf = $sf+$pf;
        }
        else{
          $sf = $data['value'];
          $pf = ($amount/100)*$data['pace_fees'];
          $cf = $sf+$pf;
        }

        $total = $amount+$cf;
        $data["sf"] = $sf;
        $data["pf"] = $pf;
        $data["cf"] = $cf;
        $data["total"] = $total;
      }
      
      return $data;
    }

    /**
     * Calculate Convenience fees for all the modules
     */
    protected function calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count = 1, $convenience_fee_currency = '') {
        if
         (empty($convenience_fee_currency) == true) {
            $convenience_fee_currency = get_application_default_currency();
        }
        $convinence_fees = 0;
        if ($convenience_value_type == 'plus') {
            $convinence_fees = ($convenience_value * $this->currency_conversion_value(false, $convenience_fee_currency, $this->to_currency));
        }
        
        elseif ($convenience_value_type == 'percentage') {
            $convinence_fees = (($amount / 100) * $convenience_value);
        }
        if ($per_pax_charge == true && $convenience_value_type == 'plus') {
            $convinence_fees = $convinence_fees * $pax_count;
        }
        return roundoff_number($convinence_fees);
    }

    /**
     * Balu A
     * Calculate TDS
     * @param $commission
     */
    public function calculate_tds($commission) {
        $commission = floatval($commission);
        //FIXME:Make it Dynamic: Admin will Manage
        $tds = 0;
        if ($commission > 0) {
            $commission_tds_deduction_percentage = 5;
            $tds = ($commission * $commission_tds_deduction_percentage) / 100;
            $tds = number_format($tds, 2, '.', '');
        }
        return $tds;
    }

    /**
     * Balu A
     * Transaction Currency COnversion Rate 
     */
    public function transaction_currency_conversion_rate() {
        $transaction_currency = get_application_currency_preference();
        $application_currency = admin_base_currency();
        $currency_conversion_rate = $this->currency_conversion_value(true, $application_currency, $transaction_currency);
        return $currency_conversion_rate;
    }

    /**
     * Balu A
     * Payment Gateway Currency COnversion Rate 
     */
    public function payment_gateway_currency_conversion_rate() {
        $CI = &get_instance();
        $payment_gateway_currency = $CI->config->item('payment_gateway_currency');
        $application_currency = admin_base_currency();
        $currency_conversion_rate = $this->currency_conversion_value(true, $application_currency, $payment_gateway_currency);
        return $currency_conversion_rate;
    }

}
