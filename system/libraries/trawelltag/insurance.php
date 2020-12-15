<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

/**
 *
 * @package    Provab
 * @subpackage API
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class insurance {

    // protected $ClientId;
    // protected $UserName;


    public function __construct() {
        // parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->library('Api_Interface');
    }

    function create_policy($request) {

        $request = $this->create_policy_request($request);
        $response['status'] = true;
        if (valid_array($request) == true) {
            $request_enc = $this->mcryptEncryptString($request['request']);
            $policy_response = $this->CI->api_interface->get_json_insurance('POST', $request['URL'], array('Data' => $request_enc, 'Ref' => $request['sign']));
            $xml = simplexml_load_string($policy_response);
            $json = json_encode($xml);
            $response['response'] = json_decode($json, TRUE);
            if ($response['response']['status'] == "Ok") {
                return $response;
            } else {

                $response['status'] = false;
                $response['response'] = $json;
                return $response;
            }
        }
    }

    function create_policy_request($request) {

        $data = array();
        
        if(isset($request['token']['token'][0]['SegmentSummary']))
        {
            $dep_details=min($request['token']['token'][0]['SegmentSummary']);
            $arr_details=max($request['token']['token'][0]['SegmentSummary']);
         
            $dep_dete=date("d-M-Y", strtotime($dep_details['OriginDetails']['date']));
            $arr_date=date("d-M-Y", strtotime($arr_details['OriginDetails']['date']));
            
//Convert them to timestamps.
$date1Timestamp = strtotime($dep_details['OriginDetails']['date']);
$date2Timestamp = strtotime($arr_details['OriginDetails']['date']);
 
//Calculate the difference.
$difference = $date2Timestamp - $date1Timestamp;
 

            
        } 
        
        foreach ($request['first_name'] as $key => $value) {
          
            $request[]= '<policy>
        <identity>
            <sign>9f55823a-2320-40c0-b729-244125e455a5</sign>
            <branchsign>
      00fc2fc1-ad17-4bee-9921-597a74d56141
        </branchsign>
            <username>
       Accentria
        </username>
            <reference>FB-4251-45782-24578</reference>
        </identity>
        <plan>
            <categorycode>DE5EE71C-098F-4CC0-B486-E69391CC9FA8</categorycode>
            <plancode>52af0742-808b-4c3b-ba38-b1286a5c56bd</plancode>
            <basecharges>135</basecharges>
            <riders>
                <ridercode percent=""></ridercode>
            </riders>
           <totalbasecharges>115</totalbasecharges>
            <servicetax>20.7</servicetax>
            <totalcharges>135</totalcharges>
        </plan>
        <traveldetails>
            <departuredate>'.$dep_dete.'</departuredate>
            <days>'.$difference.'</days>
            <arrivaldate>'.$dep_dete.'</arrivaldate>
        </traveldetails>
        <insured>
            <passport></passport>
            <contactdetails>
                <address1>'.$request['billing_address_1'].'</address1>
                <address2></address2>
                <city>'.$request['billing_city'].'</city>
                <district>'.$request['billing_city'].'</district>
                <state>Karnataka</state>
                <pincode>'.$request['billing_zipcode'].'</pincode>
                <country>'.$request['country_code'].'</country>
                <phoneno>'.$request['passenger_contact'].'</phoneno>
                <mobile>'.$request['passenger_contact'].'</mobile>
                <emailaddress>'.$request['billing_email'].'</emailaddress>
            </contactdetails>
            <name>'.$request['first_name'][$key].' '.$request['last_name'][$key].'</name>
            <dateofbirth>25-Sep-1949</dateofbirth>
            <age>70</age>
            <trawelltagnumber></trawelltagnumber>
            <nominee>Legal heir</nominee>
            <relation>Legal heir</relation>
            <pastillness></pastillness>
        </insured>
        <otherdetails>
            <policycomment></policycomment>
            <universityname></universityname>
            <universityaddress></universityaddress>
        </otherdetails>
    </policy>';
        }
        // debug($request);
        // exit;
        $data['request'] = $request;
        $data['URL'] = 'http://karvatgroup.org/trawelltag/v2/CreatePolicy.aspx';
        $data['sign'] = "ceada47e-16fa-45ba-b802-122a0888c122";
        return $data;
    }

    function mcryptEncryptString($stringToEncrypt, $base64encoded = true) {

        $iv = "ceada47e-16fa-45";
        // Encrypt the data, use first 32 characters for key
        $encryptedData = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, "9f55823a-2320-40c0-b729-244125e4", $stringToEncrypt, MCRYPT_MODE_CBC, $iv);
        // Data may need to be passed through a non-binary safe medium so base64_encode it if necessary. (makes data about 33% larger)
        $encryptedData = base64_encode($encryptedData);
        return $encryptedData;
    }

}
