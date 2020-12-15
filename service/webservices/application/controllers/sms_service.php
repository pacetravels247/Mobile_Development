<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms_service extends CI_Controller {

    function __construct() {
        error_reporting(E_ALL);
        parent::__construct();
        $this->load->library('sms/sms');
    }

    private function is_valid_user($login) {

        $domain_filter ['user_name'] = $login ['user'];
        $domain_filter ['passcode'] = $login ['password'];
        $domain_details = $this->custom_db->single_table_records('sms_management', '*', $domain_filter);
        if ($domain_details ['status'] == SUCCESS_STATUS) {
            return $domain_details;
        } else {
            return false;
        }
    }

    public function sendsms() {

        if ($this->input->post()) {
            $post_data = $this->input->post();
        } else {
            $post_data = $this->input->get();
        }
        $login_details['user'] = $post_data['user'];
        $login_details['password'] = $post_data['password'];
        $is_valid_domain = $this->is_valid_user($login_details);
        
        if ($is_valid_domain['status']==true) {
             $sms_user_details=$is_valid_domain['data'][0];
            
            if($sms_user_details['limit']<=$sms_user_details['used'])
            {
                 $response ['status'] = INACTIVE;
                 $response ['message'] = "insufficient Fund To Make this Transaction, Please Contact Administrator";
                 echo_json($response);exit;
                 
            }

            $post_data['domain_origin']=$sms_user_details['domain_origin'];
            
           // $__response = $this->sms->send_sms_users($post_data['mobile'], $post_data['text']);
            $__response = $this->sms->send_sms_users($post_data);

            if ($__response['result']['status'] == 0) {

                # Deduct Balance limit from user account
               
                
                $user_data['used'] = $sms_user_details['used']+1;
                $user_data['remaining'] = $sms_user_details['limit']-($sms_user_details['used']+1);
                $condition = array(
                    'user_name' => $post_data['user'],
                    'passcode' => $post_data['password']
                );
                $this->custom_db->update_record('sms_management', $user_data, $condition);


                $data_res ['status'] = SUCCESS_MESSAGE;
                $data_res ['Message'] = "Success";
                $data_res ['ReferenceID'] = $__response['result']['messageid'];
                $data_res ['destination'] = $__response['result']['destination'];
                echo json_encode($data_res);
                exit;
            } else {
                $data_res ['status'] = ERROR_MESSAGE;
                $data_res ['Message'] = "SEND ERROR";
                $data_res ['Description'] = "Error in processing the request";
                $data_res ['ReferenceID'] = $__response['result']['messageid'];
                $data_res ['destination'] = $__response['result']['destination'];
                echo json_encode($data_res);
                exit;
            }
        } else {
            $response ['status'] = INACTIVE;
            $response ['message'] = "Invalid Account Details, Please Contact Administrator";
            echo_json($response);
        }
    }

}
