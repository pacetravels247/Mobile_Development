<?php

require_once 'abstract_management_model.php';

/**
 * @package    current domain Application
 * @subpackage Travel Portal
 * @author     Arjun J<arjunjgowda260389@gmail.com>
 * @version    V2
 */
Class Domain_Management_Model extends Abstract_Management_Model {

    private $airline_markup;
    private $hotel_markup;
    private $bus_markup;
    var $verify_domain_balance;

    function __construct() {
        parent::__construct('level_2');
        $this->verify_domain_balance = $this->config->item('verify_domain_balance');
    }

    /**
     * Arjun J Gowda
     * Get markup based on different modules
     * @return array('value' => 0, 'type' => '')
     */
    function get_markup($module_name) {
        $markup_data = '';
        switch ($module_name) {
            case 'b2c_flight' : $markup_data = $this->airline_markup();
                break;
            case 'b2c_hotel' : $markup_data = $this->hotel_markup();
                break;
            case 'b2c_bus' : $markup_data = $this->bus_markup();
                break;
        }
        return $markup_data;
    }

    /**
     * Arjun J Gowda
     * Manage domain markup for current domain - Domain wise and module wise
     */
    function airline_markup() {
        if (empty($this->airline_markup) == true) {
            $response['specific_markup_list'] = $this->specific_airline_markup('b2c_flight');
            $response['generic_markup_list'] = $this->generic_domain_markup('b2c_flight');
            $this->airline_markup = $response;
        } else {
            $response = $this->airline_markup;
        }
        return $response;
    }

    /**
     * Arjun J Gowda
     * Manage domain markup for current domain - Domain wise and module wise
     */
    function hotel_markup() {
        if (empty($this->hotel_markup) == true) {
            $response['specific_markup_list'] = '';
            $response['generic_markup_list'] = $this->generic_domain_markup('b2c_hotel');
            $this->hotel_markup = $response;
        } else {
            $response = $this->hotel_markup;
        }
        return $response;
    }

    /**
     * Arjun J Gowda
     * Manage domain markup for current domain - Domain wise and module wise
     */
    function bus_markup() {
        if (empty($this->bus_markup) == true) {
            $response['specific_markup_list'] = '';
            $response['generic_markup_list'] = $this->generic_domain_markup('b2c_bus');
            $this->bus_markup = $response;
        } else {
            $response = $this->bus_markup;
        }
        return $response;
    }

    /**
     * Arjun J Gowda
     * Get generic markup based on the module type
     * @param $module_type
     * @param $markup_level
     */
    function generic_domain_markup($module_type) {
        $query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.markup_currency AS markup_currency
		FROM markup_list AS ML where ML.value != "" and ML.module_type = "' . $module_type . '" and
		ML.markup_level = "' . $this->markup_level . '" and ML.type="generic" and ML.domain_list_fk=' . get_domain_auth_id();
        $generic_data_list = $this->db->query($query)->result_array();
        return $generic_data_list;
    }

    /**
     *  Arjun J Gowda
     * Get specific markup based on module type
     * @param string $module_type	Name of the module for which the markup has to be returned
     * @param string $markup_level	Level of markup
     */
    function specific_airline_markup($module_type) {
        $markup_list = '';
        $query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
		ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.markup_currency AS markup_currency
		FROM airline_list AS AL JOIN markup_list AS ML where ML.value != "" and
		ML.module_type = "' . $module_type . '" and ML.markup_level = "' . $this->markup_level . '" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk=' . get_domain_auth_id() . ' order by AL.name ASC';
        $specific_data_list = $this->db->query($query)->result_array();
        if (valid_array($specific_data_list)) {
            foreach ($specific_data_list as $__k => $__v) {
                $markup_list[$__v['airline_code']] = $__v;
            }
        }
        return $markup_list;
    }

    /**
     * Check if the Booking Amount is allowed on Client Domain
     */
    function verify_current_balance($amount, $currency) {
        $status = FAILURE_STATUS;
        if ($this->verify_domain_balance == true) {
            if ($amount > 0) {
                $query = 'SELECT DL.balance, CC.country as currency, CC.value as conversion_value from domain_list as DL, currency_converter AS CC where CC.id=DL.currency_converter_fk
			AND DL.status=' . ACTIVE . ' and DL.origin=' . $this->db->escape(get_domain_auth_id()) . ' and DL.domain_key = ' . $this->db->escape(get_domain_key());
                $balance_record = $this->db->query($query)->row_array();
                if ($currency == $balance_record['currency']) {
                    $balance = $balance_record['balance'];
                    if ($balance >= $amount) {
                        $status = SUCCESS_STATUS;
                    } else {
                        //Notify User about current balance problem
                        //FIXME - send email, notification for less balance to domain admin and current domain admin
                        $this->application_logger->balance_status('Your Balance Is Very Low To Make Booking Of ' . $amount . ' ' . $currency);
                    }
                } else {
                    echo 'Under Construction';
                    exit;
                }
            }
        } else {
            $status = SUCCESS_STATUS;
        }
        return $status;
    }

    /**
     *
     * @param $fare
     * @param $domain_markup
     * @param $level_one_markup this is 0 as default as it is not mandatory to keep level one markup
     */
    function update_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup = 0, $currency = '', $currency_conversion_rate = 1) {
        $currency = empty($currency) == true ? get_application_default_currency() : $currency;
        $status = FAILURE_STATUS;
        if ($this->verify_domain_balance == true) {
            $amount = floatval($fare + $level_one_markup);
            if ($amount > 0) {
                //deduct balance and continue
                $this->private_management_model->update_domain_balance(get_domain_auth_id(), (-$amount));
                //Log transaction details
                $remarks = $transaction_type . ' Transaction was Successfully done';
                $this->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $currency, $currency_conversion_rate);
                $this->application_logger->transaction_status($remarks . '(' . $amount . ')', array('app_reference' => $app_reference, 'type' => $transaction_type));
            }
        } else {
            $status = SUCCESS_STATUS;
        }
        return $status;
    }

    /**
     * Save transaction logging for security purpose
     * @param string $transaction_type
     * @param string $app_reference
     * @param number $fare
     * @param number $domain_markup
     * @param number $level_one_markup
     * @param string $remarks
     */
    function save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $currency = 'INR', $currency_conversion_rate = 1) {

        /*
         * Updated for Storing amount in USD for USD customer
         * Balu A
         */
          $domain_origin=get_domain_auth_id();
          if(intval($domain_origin) > 0){
              $domain_details = $this->get_domain_details($domain_origin);
              $domain_base_currency = $domain_details['domain_base_currency'];
          } else {
                $domain_base_currency = domain_base_currency();
          }


          $currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => $domain_base_currency));

           if($transaction_type =='viator_transfer' || $transaction_type =='sightseeing'){
            
                //for transfer and sightseeing no need to convert the price to inr
                   $fare=$fare;
                   $domain_markup=$domain_markup;
                   $level_one_markup=$level_one_markup;
                  # echo "ela";
           }else{
                 #Converting Fare
                  $fare_converted_value = $currency_obj->force_currency_conversion($fare);

                  $fare=$fare_converted_value['default_value'];

                  #Converting Domain Markup As per customer Currency
                  $domain_markup_converted_value = $currency_obj->force_currency_conversion($domain_markup);
                  $domain_markup=$domain_markup_converted_value['default_value'];
                  #Converting One Level Markup As per customer Currency
                  $level_one_markup_converted_value = $currency_obj->force_currency_conversion($level_one_markup);
                  $level_one_markup=$level_one_markup_converted_value['default_value'];
                 
                /* End Upload */
           }
         


        $transaction_log['system_transaction_id'] = date('Ymd-His') . '-S-' . rand(1, 10000);
        $transaction_log['transaction_type'] = $transaction_type;
        $transaction_log['domain_origin'] = get_domain_auth_id();
        $transaction_log['app_reference'] = $app_reference;
        $transaction_log['fare'] = $fare;
        $transaction_log['level_one_markup'] = $level_one_markup;
        $transaction_log['domain_markup'] = $domain_markup;
        $transaction_log['remarks'] = $remarks;
        $transaction_log['created_by_id'] = intval(@$this->entity_user_ids);
        $transaction_log['created_datetime'] = date('Y-m-d H:i:s', time());
        $transaction_log['currency'] = $currency;
        $transaction_log['currency_conversion_rate'] = $currency_conversion_rate;
        //Opening and Closing Balance
        $total_transaction_amount = ($fare + $level_one_markup + $domain_markup);
        $opening_closing_balance_details = $this->get_opening_closing_balance(get_domain_auth_id(), $total_transaction_amount);

        $temp_test_data = $opening_closing_balance_details;
        $temp_test_data['total_transaction_amount'] = $total_transaction_amount;
        $temp_test_data['fare_break'] = 'DomianID-' . get_domain_auth_id() . ' F: ' . $fare . ' L: ' . $level_one_markup . ' D: ' . $domain_markup;
        $this->custom_db->insert_record('test', array('test' => json_encode($temp_test_data), 'description' => ''));

        $transaction_log['opening_balance'] = $opening_closing_balance_details['opening_balance'];
        $transaction_log['closing_balance'] = $opening_closing_balance_details['closing_balance'];
        $this->custom_db->insert_record('transaction_log', $transaction_log);
    }

    /*
     * Get Opening and Closing Balance Details
     */

    function get_opening_closing_balance($domain_origin, $total_transaction_amount) {
        $total_transaction_amount = floatval($total_transaction_amount);
        $query = 'select DL.domain_name,TL.opening_balance,TL.closing_balance from transaction_log TL
					join domain_list DL on DL.origin=TL.domain_origin
					where DL.origin=' . intval($domain_origin) . ' order by TL.origin desc limit 1';
        $current_balance_details = $this->db->query($query)->row_array();
        $opening_balance = $current_balance_details['closing_balance'];
        $total_transaction_amount = ($total_transaction_amount) < 0 ? abs($total_transaction_amount) : -($total_transaction_amount); //if -Ve, convert to +Ve and ViceVersa
        $closing_balance = ($opening_balance + $total_transaction_amount); //Closing Balance
        $data['opening_balance'] = round(floatval($opening_balance), 2);
        $data['closing_balance'] = round(floatval($closing_balance), 2);
        return $data;
    }

    /**
     * log access
     * @param unknown $app_reference
     * @param string $comment
     */
    function create_track_log($app_reference, $comment = '') {
        $track_log ['app_reference'] = $app_reference;
        $track_log ['domain_origin'] = get_domain_auth_id();
        $track_log ['http_host'] = @$_SERVER ['HTTP_HOST'];
        $track_log ['remote_address'] = @$_SERVER ['REMOTE_ADDR'];
        $track_log ['browser'] = @$_SERVER ['HTTP_USER_AGENT'];
        $track_log ['request_url'] = @$_SERVER ['REQUEST_URI'];
        $track_log ['request_method'] = @$_SERVER ['REQUEST_METHOD'];
        $track_log ['server_ip'] = @$_SERVER ['SERVER_ADDR'];
        $track_log ['server_name'] = @$_SERVER ['SERVER_NAME'];
        $track_log ['comments'] = $this->db->escape($comment);
        $track_log ['created_datetime'] = date('Y-m-d H:i:s');
        $track_log ['attr'] = base64_encode($_SERVER);
        // $this->custom_db->insert_record('track_log', $track_log);
    }

    /**
     * Logs booking Amount
     * Enter description here ...
     * @param unknown_type $transaction_type
     * @param unknown_type $app_reference
     * @param unknown_type $transaction_amount
     * @param unknown_type $currency
     * @param unknown_type $currency_conversion_rate
     */
    public function booking_amount_logger($transaction_type, $app_reference, $transaction_amount, $currency = 'INR', $currency_conversion_rate = 1) {
        $booking_amount_logger = array();
        $booking_amount_logger['transaction_type'] = $transaction_type;
        $booking_amount_logger['domain_origin'] = get_domain_auth_id();
        $booking_amount_logger['app_reference'] = $app_reference;
        $booking_amount_logger['transaction_amount'] = $transaction_amount;
        $booking_amount_logger['remarks'] = '';
        $booking_amount_logger['created_by_id'] = intval(@$this->entity_user_id);
        $booking_amount_logger['created_datetime'] = date('Y-m-d H:i:s', time());
        $booking_amount_logger['currency'] = $currency;
        $booking_amount_logger['currency_conversion_rate'] = $currency_conversion_rate;
        $this->custom_db->insert_record('booking_amount_logger', $booking_amount_logger);
    }

    /**
     * Returns Domain Currency Conversion Rate
     * @param string $domain_currency;EX: USD, INR
     */
    public function get_currency_conversion_rate($currency) {

        // get_domain_auth_id
        $query = 'select currency_value as conversion_rate from domain_currency_value where currency="' . trim($currency) . '" and domain_origin=' . get_domain_auth_id() . '';
        $details = $this->db->query($query)->row_array();

        if (valid_array($details) == true && $details['conversion_rate'] > 0) {
            return $details;
        } else {
            $query = 'select value as conversion_rate from domain_currency_converter where country="' . trim($currency) . '"';
            return $this->db->query($query)->row_array();
        }
    }
    /**
    *Elavarasi
    *Return USD to INR updated currency
    * 
    */
    public function get_viator_currency_conversion_rate(){
        $query = "select * from viator_api_currency_converter where origin=1";
        return $this->db->query($query)->row_array();
    }

     /**
     * Returns Domain Currency Conversion Rate
     * @param string $domain_currency;EX: USD, INR
     */
    public function get_currency_conversion_rate_sabre($currency) {

        // get_domain_auth_id
        $query = 'select currency_value as conversion_rate from domain_currency_value where currency="' . trim($currency) . '" and domain_origin=31';
        $details = $this->db->query($query)->row_array();
        
        if (valid_array($details) == true && $details['conversion_rate']>0) {
            return $details;
        } else {
            $query = 'select value as conversion_rate from domain_currency_converter where country="' . trim($currency) . '"';
            return $this->db->query($query)->row_array();
        }
    }
    /**
     * Returns Domain details based on Domain origin
     * @param unknown_type $domain_origin
     */
    public function get_domain_details($domain_origin) {
        return $this->db->query('select DL.*,CC.country as domain_base_currency from domain_list DL
								join currency_converter CC on CC.id=DL.currency_converter_fk
								where DL.origin=' . intval($domain_origin))->row_array();
    }

}
