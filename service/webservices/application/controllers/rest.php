<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rest extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('bus_model');
        $this->load->model('flight_model');
        $this->load->model('sightseeing_model');
        $this->load->model('transferv1_model');
        $this->load->library('currency');
    }

    /**
     * Return current balance of the application user
     */
    private function is_valid_user($login) {
        $domain_filter ['domain_key'] = $login ['domain_key'];

        if ($login ['system'] == "test") {

            $domain_filter ['test_username'] = $login ['username'];
            $domain_filter ['test_password'] = $login ['password'];
        } else if ($login ['system'] == "live") {

            $domain_filter ['live_username'] = $login ['username'];
            $domain_filter ['live_password'] = $login ['password'];
        } else {
            
        }

        $domain_details = $this->custom_db->single_table_records('domain_list', '*', $domain_filter);

        if ($domain_details ['status'] == SUCCESS_STATUS) {
            return true;
        } else {
            return false;
        }
    }

    public function domain_balance() {
        $post_data = $this->input->post();

        $domain_filter ['domain_key'] = $post_data ['domain_key'];
        $domain_filter ['status'] = ACTIVE;

        if ($post_data ['system'] == "test") {

            $domain_filter ['test_username'] = $post_data ['username'];
            $domain_filter ['test_password'] = $post_data ['password'];
        } else if ($post_data ['system'] == "live") {

            $domain_filter ['live_username'] = $post_data ['username'];
            $domain_filter ['live_password'] = $post_data ['password'];
        } else {
            
        }

        // $domain_details=$this->custom_db->single_table_records('domain_list', 'balance', $domain_filter);
        $domain_details = $this->custom_db->multiple_table_cross_records(array(
            'domain_list',
            'currency_converter'
                ), 'domain_list.*, currency_converter.country AS currency_name', array(
            'currency_converter.id' => 'domain_list.currency_converter_fk'
                ), $domain_filter);
        if ($domain_details ['status'] == SUCCESS_STATUS) {

            $response ['status'] = ACTIVE;
            if ($post_data ['system'] == "test") {
                $response ['balance'] = $domain_details ['data'] [0] ['test_balance'];
            } else if ($post_data ['system'] == "live") {
                $response ['balance'] = $domain_details ['data'] [0] ['balance'];
                $response ['credit_limit'] = $domain_details ['data'] [0] ['credit_limit'];
                $response ['due_amount'] = $domain_details ['data'] [0] ['due_amount'];
            }
            $response ['currency'] = $domain_details ['data'] [0] ['currency_name'];
        } else {
            $response ['status'] = INACTIVE;
        }
        echo_json($response);
    }

    /**
     * Returns Domain Currency
     */
    function domain_currency() {
        $post_data = $this->input->post();
        $response = array();
        $domain_filter ['domain_key'] = $post_data ['domain_key'];
        $domain_filter ['status'] = ACTIVE;
        if ($post_data ['system'] == "test") {
            $domain_filter ['test_username'] = $post_data ['username'];
            $domain_filter ['test_password'] = $post_data ['password'];
        } else if ($post_data ['system'] == "live") {
            $domain_filter ['live_username'] = $post_data ['username'];
            $domain_filter ['live_password'] = $post_data ['password'];
        }
        $domain_details = $this->custom_db->multiple_table_cross_records(array(
            'domain_list',
            'currency_converter'
                ), 'currency_converter.country AS currency', array(
            'currency_converter.id' => 'domain_list.currency_converter_fk'
                ), $domain_filter);
        if ($domain_details ['status'] == SUCCESS_STATUS) {
            $response ['status'] = ACTIVE;
            $response ['currency'] = $domain_details ['data'] [0] ['currency'];
        } else {
            $response ['status'] = INACTIVE;
        }
        echo_json($response);
    }

    /**
     * FIXME
     */
    public function domain_balance_request() {
        $post_data = $this->input->post();

        $domain_filter ['domain_key'] = $post_data ['domain_key'];

        if ($post_data ['system'] == "test") {

            $domain_filter ['test_username'] = $post_data ['username'];
            $domain_filter ['test_password'] = $post_data ['password'];
        } else if ($post_data ['system'] == "live") {

            $domain_filter ['live_username'] = $post_data ['username'];
            $domain_filter ['live_password'] = $post_data ['password'];
        } else {
            
        }

        $domain_filter ['status'] = ACTIVE;

        $domain_details = $this->custom_db->single_table_records('domain_list', 'balance', $domain_filter);

        if ($domain_details ['status'] == SUCCESS_STATUS) {

            // process the balance request
        } else {
            $response ['status'] = INACTIVE;
        }

        echo json_encode($response);
    }

    // This function will return the weather related data of a particular city (e.g. city=Delhi)
    public function weather($city = '') {
        $result = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=2de143494c0b295cca9337e1e96b00e0'), true);

        $response ['lon'] = $result ['coord'] ['lon'];
        $response ['lat'] = $result ['coord'] ['lat'];
        $response ['temprature'] = $result ['main'] ['temp'] - 273.15; // This will give us the temprature in degree.
        $response ['pressure'] = @$result ['main'] ['pressure'];
        $response ['humidity'] = @$result ['main'] ['humidity'];
        $response ['temp_max'] = $result ['main'] ['temp_max'] - 273.15;
        $response ['temp_min'] = $result ['main'] ['temp_min'] - 273.15;
        $response ['wind_speed'] = $result ['wind'] ['speed'];
        $response ['visibility'] = @$result ['visibility'];
        $response ['weather_type'] = $result ['weather'] [0] ['main'];
        $response ['weather_description'] = $result ['weather'] [0] ['description'];

        debug($response);

        return $response;
    }

    public function bus_deal_sheet() {
        $post_data = $this->input->post();
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];

        if ($this->is_valid_user($login_details)) {
            $domain_id = $post_data ['domain_id'];
            $bus_deals = $this->bus_model->bus_deals($domain_id);
            if (valid_array($bus_deals)) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $bus_deals;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json($response);
        } else {
            $response ['status'] = INACTIVE;
            echo_json($response);
        }
    }

    /**
     * Jaganath
     */
    public function bus_commission_details() {
        $post_data = $this->input->post();
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];

        if ($this->is_valid_user($login_details)) {
            $domain_id = $post_data ['domain_id'];
            $commission_details = $this->bus_model->bus_commission_details($domain_id);
            if (valid_array($commission_details)) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $commission_details;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json($response);
        } else {
            $response ['status'] = INACTIVE;
            echo_json($response);
        }
    }
    /**
     * Elavarasi
     */
    public function sightseeing_commission_details() 
    {
        $post_data = $this->input->post ();
        //debug($post_data);
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];
        //debug($login_details);

        if ($this->is_valid_user ( $login_details )) {
            $domain_id = $post_data ['domain_id'];
            //$domain_id = 3;
            $commission_details = $this->sightseeing_model->sightseeing_commission_details ( $domain_id );
            if (valid_array ( $commission_details )) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $commission_details;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json ( $response );
        } else {
            $response ['status'] = INACTIVE;
            echo_json ( $response );            
        }
    }
        /*Transfers Commission Details*/
    public function transfer_commission_details(){
        $post_data = $this->input->post ();
        //debug($post_data);
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];
        //debug($login_details);

        if ($this->is_valid_user ( $login_details )) {
            $domain_id = $post_data ['domain_id'];
            //$domain_id = 3;
            $commission_details = $this->transferv1_model->viatortransfer_commission_details ( $domain_id );
            if (valid_array ( $commission_details )) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $commission_details;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json ( $response );
        } else {
            $response ['status'] = INACTIVE;
            echo_json ( $response );            
        }
    }
    public function airline_deal_sheet() {
        $post_data = $this->input->post();
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];

        if ($this->is_valid_user($login_details)) {
            $domain_id = $post_data ['domain_id'];
            $airline_deals = $this->flight_model->airline_deals($domain_id);
            if (valid_array($airline_deals)) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $airline_deals;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json($response);
        } else {
            $response ['status'] = INACTIVE;
            echo_json($response);
        }
    }

    /**
     * Jaganath
     */
    public function airline_commission_details() {

        $post_data = $this->input->post();
        // debug($post_data);exit;
        $login_details ['domain_key'] = $post_data ['domain_key'];
        $login_details ['username'] = $post_data ['username'];
        $login_details ['password'] = $post_data ['password'];
        $login_details ['system'] = $post_data ['system'];

        if ($this->is_valid_user($login_details)) {
            $domain_id = $post_data ['domain_id'];
            $commission_details = $this->flight_model->airline_commission_details($domain_id);
            // echo $this->db->last_query();exit;
            if (valid_array($commission_details)) {
                $response ['status'] = ACTIVE;
                $response ['data'] = $commission_details;
            } else {
                $response ['status'] = INACTIVE;
            }
            echo_json($response);
        } else {
            $response ['status'] = INACTIVE;
            echo_json($response);
        }
    }

    /**
     * Jeevan
     * Update the Domain Balance
     */
    public function UpdateBalance() {
        $response['status'] = INACTIVE;
        $postdata = file_get_contents("php://input");
        $post_data = json_decode($postdata, true);
        if (isset($post_data['Markup'])) {
            $amount = $post_data['Markup'];
            $remarks = "Hotel Markup amount is added into the domain balance";
        } else if (isset($post_data['Discount'])) {
            $amount = $post_data['Discount'];
            $remarks = "Hotel Promocode Discount amount is Deducted from the domain balance";
        }

        $system = $post_data['system'];
        $app_reference = $post_data['AppReference'];
        $domain_markup = 0;
        $level_one_markup = 0;
        if ($this->is_valid_payment_key($post_data)) {

            $currency = domain_base_currency();
            $currency_obj = new Currency(array('from' => get_application_default_currency(), 'to' => domain_base_currency()));
            $currency_conversion_rate = $currency_obj->get_domain_currency_conversion_rate();

            $this->user_model->update_balance($amount, $system);
            $response ['status'] = ACTIVE;
            $transaction_amount = ($amount) > 0 ? -($amount) : abs($amount); //Dont change it
            $this->domain_management_model->save_transaction_details('hotel', $app_reference, $transaction_amount, $domain_markup, $level_one_markup, $remarks, $currency, $currency_conversion_rate);
            echo_json($response);
        } else {
            $response ['status'] = INACTIVE;
            echo_json($response);
        }
    }

    public function is_valid_payment_key($post_data) {
        $domain_key = $post_data ['DomainKey'];
        $username = $post_data ['UserName'];
        $password = $post_data ['Password'];
        $system = $post_data ['system'];
        $travelomatix_payment_key = $post_data['TravelomatixPaymentKey'];

        $this->credential_type = $system;

        $domain_login = $this->user_model->valid_payment_key($domain_key, $username, $password, $system, $travelomatix_payment_key);


        if ($domain_login['status'] == SUCCESS_STATUS) {
            return true;
        } else {
            return false;
        }
    }

   /* function currecny_value_details() {

        $post_data = $this->input->get();

        $from = $post_data['from'];
        $to = $post_data['to'];
        $amount=$post_data['amount'];
        $CI = &get_instance();

        if ($from != $to) {
            $from_Currency = urlencode($from);
            $to_Currency = urlencode($to);
            if(isset($amount) && $amount > 1)
            {
                 $encode_amount = urlencode($amount);
            }
            else {
                  $encode_amount = urlencode(1);
            }
          
            $get = file_get_contents("https://finance.google.com/finance/converter?a=$encode_amount&from=$from_Currency&to=$to_Currency");

            $get = explode("<span class=bld>", $get);
            $get = explode("</span>", $get[1]);
            $converted_currency = preg_replace("/[^0-9\.]/", null, $get[0]);

            if ($converted_currency != '') {
                $data['currency_value'] = $converted_currency;
                echo json_encode($data);
            } else {

                $url = "https://www.xe.com/currencyconverter/convert/?Amount=$encode_amount&From=" . $from_Currency . "&To=" . $to_Currency;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                $strError = curl_error($ch);
                curl_close($ch);
                $currency_data = "";
                if ($strError == "") {
                    $arrData = explode('uccResultAmount', $output);
                    $arrData1 = explode("uccToCurrencyCode", $arrData[1]);
                    $currency_data = preg_replace("/[^0-9\.]/", null, $arrData1[0]);
                    $data['currency_value'] = $currency_data;
                }
                echo json_encode($data);
            }
        } else {
            $data['currency_value'] = 1;
            echo json_encode($data);
        }

      
    } */
    
     function currecny_value_details() {

        $post_data = $this->input->get();

        $from = $post_data['from'];
        $to = $post_data['to'];
        $amount=$post_data['amount'];
        $CI = &get_instance();

        if ($from != $to) {
            $from_Currency = urlencode($from);
            $to_Currency = urlencode($to);
            if(isset($amount) && $amount > 1)
            {
                $encode_amount = urlencode($amount);
            }
            else {
                $encode_amount = urlencode(1);
            }

            $currency_data = $this->custom_db->single_table_records('currency_detail','value',array('f_currency' => $from_Currency, 't_currency' => $to_Currency));
            
            if($currency_data['status'] == 1){
                
                $data['currency_value'] = $currency_data['data'][0]['value'];
                echo json_encode($data); 
            }
            else{

                $url = "https://www.xe.com/currencyconverter/convert/?Amount=$encode_amount&From=" . $from_Currency . "&To=" . $to_Currency;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                
                $strError = curl_error($ch);
                curl_close($ch);
                $currency_data = "";
                if ($strError == "") {
                    $arrData = explode('uccResultAmount', $output);
                    $arrData1 = explode("uccToCurrencyCode", @$arrData[1]);
                    $currency_data = preg_replace("/[^0-9\.]/", null, $arrData1[0]);
                    $data['currency_value'] = $currency_data;
                   
                }
                if($currency_data != ''){
                    echo json_encode($data);
                }
                else{
                	$url = "http://free.currencyconverterapi.com/api/v3/convert?q=".$from_Currency."_".$to_Currency."&compact=ultra";
                	$ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
                    
                    $strError = curl_error($ch);
                   
                    if($strError ==''){
                        $curr = $from_Currency.'_'.$to_Currency;
                        $output = json_decode($output);
                        $data['currency_value'] = (string)$output->$curr;
                        echo json_encode($data); 
                    }
                }
            }
        } else {
            $data['currency_value'] = 1;
            echo json_encode($data);
        }

        // return $details[$from . $to];
    }
    
    
    
    
     function currecny_value_details_for_supervision() {

        $post_data = $this->input->get();

        $from = $post_data['from'];
        $to = $post_data['to'];
        $amount=$post_data['amount'];
        $CI = &get_instance();

        if ($from != $to) {
            $from_Currency = urlencode($from);
            $to_Currency = urlencode($to);
            if(isset($amount) && $amount > 1)
            {
                $encode_amount = urlencode($amount);
            }
            else {
                $encode_amount = urlencode(1);
            }

                $url = "https://www.xe.com/currencyconverter/convert/?Amount=$encode_amount&From=" . $from_Currency . "&To=" . $to_Currency;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                $strError = curl_error($ch);
                curl_close($ch);
                $currency_data = "";
                if ($strError == "") {
                    $arrData = explode('uccResultAmount', $output);
                    $arrData1 = explode("uccToCurrencyCode", @$arrData[1]);
                    $currency_data = preg_replace("/[^0-9\.]/", null, $arrData1[0]);
                    $data['currency_value'] = $currency_data;
                   
                }
                
                if($currency_data != ''){
                    echo json_encode($data);
                }
                else{
                	$url = "http://free.currencyconverterapi.com/api/v3/convert?q=".$from_Currency."_".$to_Currency."&compact=ultra";
                	$ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
                    $strError = curl_error($ch);
                   
                    if($strError ==''){
                        $curr = $from_Currency.'_'.$to_Currency;
                        $output = json_decode($output);
                        $data['currency_value'] = (string)$output->$curr;
                        echo json_encode($data); 
                    }
                }
            
        } else {
            $data['currency_value'] = 1;
            echo json_encode($data);
        }

        // return $details[$from . $to];
    }

}
