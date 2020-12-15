<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Multi_Curl
{
	function __construct()
	{
		$this->CI = &get_instance();
	}
	/**
	 * 
	 * Enter description here ...
	 */
        
        
        
    public function execute_multi_curl($curl_params)
	{
		//debug($curl_params); exit; 
		$curl_response = array();
		$request_insert_id = array();
        $curl_response1=array();
		$booking_sources = $curl_params['booking_source'];
      
		$requests = $curl_params['request'];
		$urls     = $curl_params['url'];
		$headers  = $curl_params['header'];
		$remarks  = $curl_params['remarks'];

        $search_id= $curl_params['search_id'];
		if(valid_array($booking_sources) == true && valid_array($requests) == true && 
			valid_array($urls) == true && valid_array($headers) == true){

			foreach ($booking_sources as $k => $v){

					$api_url = $urls[$k];
					$api_request = $requests[$k];
                    
					$api_header = $headers[$k];
					/* debug($api_url);
					debug($api_request);
					exit; */
					// create both cURL resources
					${"ch" .$k} = curl_init();
					// set URL and other appropriate options
					curl_setopt(${"ch" .$k}, CURLOPT_URL, $api_url);
					curl_setopt(${"ch" .$k}, CURLOPT_TIMEOUT, 180);
					curl_setopt(${"ch" .$k}, CURLOPT_HEADER, 0);
					curl_setopt(${"ch" .$k}, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt(${"ch" .$k}, CURLOPT_POST, 1);
					curl_setopt(${"ch" .$k}, CURLOPT_POSTFIELDS, $api_request);
					curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYPEER, FALSE);
	                                
	                if($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE || $v == TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE){
						curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, FALSE);                 
					}
                    else if($v != SABARE_FLIGHT_BOOKING_SOURCE){
						// curl_setopt(${"ch" .$k}, CURLOPT_SSLVERSION, 3);
						curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, TRUE);
					}
					
					curl_setopt(${"ch" .$k}, CURLOPT_HTTPHEADER, $api_header);
					curl_setopt(${"ch" .$k}, CURLOPT_ENCODING, "gzip,deflate");
					if($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE || $v == TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE){
                          curl_setopt(${"ch" .$k}, CURLOPT_SSLVERSION, 6);
                    }
					//Store API Request
					$backtrace = debug_backtrace();
					$method_name = $backtrace[1]['function'];
					$api_remarks = $method_name.'('.$remarks[$k].')';
                    
					$temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $api_request, $api_remarks,'',$search_id);

					$request_insert_id[$k] = intval(@$temp_request_insert_id['insert_id']);
			}
			//create the multiple cURL handle
			$mh = curl_multi_init();
			//add the handles
			foreach ($booking_sources as $k => $v){
					curl_multi_add_handle($mh,${"ch" .$k});
			}
			// execute all queries simultaneously, and continue when all are complete
	  		$running = null;
	  		do {
	    		curl_multi_exec($mh, $running);
	  		} while ($running);
			//close the handles
			foreach ($booking_sources as $k => $v){
				curl_multi_remove_handle($mh, ${"ch" . $k});
			}
			curl_multi_close($mh);          
			//Storing the Response
            $current_response =  '';
			foreach ($booking_sources as $k => $v){
                if($v==TRAVELPORT_FLIGHT_BOOKING_SOURCE || $v==SABARE_FLIGHT_BOOKING_SOURCE || $v==TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE){
                    $booking_source_origin = $curl_params['booking_source_origin'];
                    $current_response = $curl_response[$booking_sources[$k]][$booking_source_origin[$k]][] = curl_multi_getcontent(${"ch" . $k});
                 }else if($v==SPICEJET_FLIGHT_BOOKING_SOURCE){
                    $current_response = $curl_response[$booking_sources[$k]][] = curl_multi_getcontent(${"ch" . $k});
                 }else{
                     $current_response = $curl_response[$booking_sources[$k]] = curl_multi_getcontent(${"ch" . $k});
                 }
                $curl_details=curl_getinfo(${"ch" .$k});
                // debug($current_response);exit;
				// $this->CI->api_model->update_api_response($current_response, $request_insert_id[$k],$curl_details['total_time']);
                $this->CI->api_model->update_api_response("", $request_insert_id[$k],$curl_details['total_time']);
			}
		}
		//debug($curl_response); exit; 
		return $curl_response;
	}
        
        
        
	public function execute_multi_curl_BALU($curl_params)
	{
		$curl_response = array();
		$request_insert_id = array();
		$booking_sources = $curl_params['booking_source'];
		$requests = $curl_params['request'];
		$urls = $curl_params['url'];
		$headers = $curl_params['header'];
		$remarks = $curl_params['remarks'];
		if(valid_array($booking_sources) == true && valid_array($requests) == true && 
			valid_array($urls) == true && valid_array($headers) == true){
			foreach ($booking_sources as $k => $v){
                if($v != TRAVELPORT_FLIGHT_BOOKING_SOURCE){
					$api_url = $urls[$k];
					$api_request = $requests[$k];
					$api_header = $headers[$k];
					// create both cURL resources
					${"ch" .$k} = curl_init();
					// set URL and other appropriate options
					curl_setopt(${"ch" .$k}, CURLOPT_URL, $api_url);
					curl_setopt(${"ch" .$k}, CURLOPT_TIMEOUT, 180);
					curl_setopt( ${"ch" .$k}, CURLOPT_HEADER, 0);
					curl_setopt(${"ch" .$k}, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt(${"ch" .$k}, CURLOPT_POST, 1);
					curl_setopt(${"ch" .$k}, CURLOPT_POSTFIELDS, $api_request);
					curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYPEER, FALSE);
	                                
	                if($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE){
						curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, FALSE);
					}
					else if($v != SABARE_FLIGHT_BOOKING_SOURCE){
						curl_setopt(${"ch" .$k}, CURLOPT_SSLVERSION, 3);
						curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, TRUE);
					}
					
					curl_setopt(${"ch" .$k}, CURLOPT_HTTPHEADER, $api_header);
					curl_setopt(${"ch" .$k}, CURLOPT_ENCODING, "gzip,deflate");
					
					//Store API Request
					$backtrace = debug_backtrace();
					$method_name = $backtrace[1]['function'];
					$api_remarks = $method_name.'('.$remarks[$k].')';
					$temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $api_request, $api_remarks);
					$request_insert_id[$k] = intval(@$temp_request_insert_id['insert_id']);
				}
			}
			//create the multiple cURL handle
			$mh = curl_multi_init();
			//add the handles
			foreach ($booking_sources as $k => $v){
				if($v != TRAVELPORT_FLIGHT_BOOKING_SOURCE){
					curl_multi_add_handle($mh,${"ch" .$k});
				}
			}
			// execute all queries simultaneously, and continue when all are complete
	  		$running = null;
	  		do {
	    		curl_multi_exec($mh, $running);
	  		} while ($running);
			//close the handles
			foreach ($booking_sources as $k => $v){
				if($v != TRAVELPORT_FLIGHT_BOOKING_SOURCE){
					curl_multi_remove_handle($mh, ${"ch" . $k});
				}
			}
			curl_multi_close($mh);
			//Storing the Response
			foreach ($booking_sources as $k => $v){
				if($v != TRAVELPORT_FLIGHT_BOOKING_SOURCE){
					$curl_response[$booking_sources[$k]] = curl_multi_getcontent(${"ch" . $k});
					$this->CI->api_model->update_api_response($curl_response[$booking_sources[$k]], $request_insert_id[$k]);
					//$error = curl_getinfo (${"ch" . $k});
				}
			}
		}
		return $curl_response;
	}
        
     /**
     * 
     * Enter description here ...
     */
    public function execute_multi_curl_sightseeing($curl_params) {
        
        // debug($curl_params);exit;
        
        $curl_response = array();
        $request_insert_id = array();
        $booking_sources = $curl_params['booking_source'];
        $requests = $curl_params['request'];
        $urls = $curl_params['url'];
        $headers = $curl_params['header'];
        $remarks = $curl_params['remarks'];
        $cookie_file = @$curl_params['cookie'];

        if (valid_array($booking_sources) == true && valid_array($requests) == true &&
                valid_array($urls) == true && valid_array($headers) == true) {
            foreach ($booking_sources as $k => $v) {
                if($v != AGODA_HOTEL_BOOKING_SOURCE){
                    $api_url = $urls[$k];
                    $api_request = $requests[$k];
                    $api_header = $headers[$k];
                    $cookie_file = $cookie_file[$k];


                    // create both cURL resources
                    ${"ch" . $k} = curl_init();
                    // set URL and other appropriate options
                   
                     curl_setopt(${"ch" . $k}, CURLOPT_URL, $api_url); 
                    curl_setopt(${"ch" . $k}, CURLOPT_TIMEOUT, 180);
                    curl_setopt(${"ch" . $k}, CURLOPT_HEADER, 0);
                    curl_setopt(${"ch" . $k}, CURLOPT_RETURNTRANSFER, 1);
                     curl_setopt(${"ch" . $k}, CURLOPT_POST, 1);
                        curl_setopt(${"ch" . $k}, CURLOPT_POSTFIELDS, $api_request);
                    if ($v!= SIGHTSEEING_BOOKING_SOURCE && $v!=VIATOR_TRANSFER_BOOKING_SOURCE) {

                        curl_setopt(${"ch" . $k}, CURLOPT_SSL_VERIFYHOST, 2);
                        curl_setopt(${"ch" . $k}, CURLOPT_SSL_VERIFYPEER, FALSE);
                    }

                    if ($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE) {
                        curl_setopt(${"ch" . $k}, CURLOPT_FOLLOWLOCATION, FALSE);
                    }

                    
                    curl_setopt(${"ch" . $k}, CURLOPT_HTTPHEADER, $api_header);
                  
                    if ($v != GRN_CONNECT_HOTEL_BOOKING_SOURCE) {
                        curl_setopt(${"ch" . $k}, CURLOPT_ENCODING, "gzip,deflate");
                    }

                    //Store API Request
                    $backtrace = debug_backtrace();
                    $method_name = $backtrace[1]['function'];
                    $api_remarks = $method_name . '(' . $remarks[$k] . ')';
                    $temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $api_request, $api_remarks);
                    $request_insert_id[$k] = intval(@$temp_request_insert_id['insert_id']);  
                }
               
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            //add the handles
            foreach ($booking_sources as $k => $v) {
                if($v != AGODA_HOTEL_BOOKING_SOURCE){
                    curl_multi_add_handle($mh, ${"ch" . $k});
                }
            }
            // execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
            //close the handles
           
            curl_multi_close($mh);
            //Storing the Response
            //debug(curl_getinfo(${"ch" . $k}));
           // debug(curl_error(${"ch" . $k}));exit;
            foreach ($booking_sources as $k => $v) {
                if($v != AGODA_HOTEL_BOOKING_SOURCE){
                    $curl_response[$booking_sources[$k]] = curl_multi_getcontent(${"ch" . $k});
                  //  $this->CI->api_model->update_api_response($curl_response[$booking_sources[$k]], $request_insert_id[$k]);
                      $this->CI->api_model->update_api_response("NULL", $request_insert_id[$k]);
                    //$error = curl_getinfo (${"ch" . $k});
                }
            }
        }
        // debug($curl_response);exit;
        return $curl_response;
    }
  	  public function execute_multi_curl1($curl_params) {
        
        // debug($curl_params);exit;
        
        $curl_response = array();
        $request_insert_id = array();
        $booking_sources = $curl_params['booking_source'];
        $requests = $curl_params['request'];
        $urls = $curl_params['url'];
        $headers = $curl_params['header'];
        $remarks = $curl_params['remarks'];
        $cookie_file = @$curl_params['cookie'];
        // debug($requests);exit;
        if (valid_array($booking_sources) == true && valid_array($requests) == true &&
                valid_array($urls) == true && valid_array($headers) == true) {
            foreach ($booking_sources as $k => $v) {
                if($v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    $api_url = $urls[$k];
                    $api_request = $requests[$k];
                    // debug($api_request);exit;
                    $api_header = $headers[$k];
                    $cookie_file = $cookie_file[$k];

                    foreach($api_request as $key1 => $request){
                    ${"ch" . $key1} = curl_init();
                    // set URL and other appropriate options
                    curl_setopt(${"ch" . $key1}, CURLOPT_URL, $api_url);
                    curl_setopt(${"ch" . $key1}, CURLOPT_TIMEOUT, 180);
                    curl_setopt(${"ch" . $key1}, CURLOPT_HEADER, 0);
                    curl_setopt(${"ch" . $key1}, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt(${"ch" . $key1}, CURLOPT_POST, 1);
                    curl_setopt(${"ch" . $key1}, CURLOPT_POSTFIELDS, $request);
                  	curl_setopt(${"ch" . $key1}, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt(${"ch" . $key1}, CURLOPT_SSL_VERIFYPEER, FALSE);
                   	if ($v != GRN_CONNECT_HOTEL_BOOKING_SOURCE) {
                        curl_setopt(${"ch" . $key1}, CURLOPT_SSLVERSION, 3);
                        curl_setopt(${"ch" . $key1}, CURLOPT_FOLLOWLOCATION, TRUE);
                    }
                    curl_setopt(${"ch" . $key1}, CURLOPT_HTTPHEADER, $api_header);
                   
                    if ($v != GRN_CONNECT_HOTEL_BOOKING_SOURCE) {
                        curl_setopt(${"ch" . $key1}, CURLOPT_ENCODING, "gzip,deflate");
                    }

                    //Store API Request
                    $backtrace = debug_backtrace();
                    $method_name = $backtrace[1]['function'];
                    $api_remarks = $method_name . '(' . $remarks[$k] . ')';
                    $temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $request, $api_remarks);
                    $request_insert_id[$key1] = intval(@$temp_request_insert_id['insert_id']);  
                    }
                    // create both cURL resources
                 
                }
               
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            //add the handles
             foreach($api_request as $key1 => $request){
                //if($v != HB_HOTEL_BOOKING_SOURCE &&$v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    curl_multi_add_handle($mh, ${"ch" . $key1});
               // }
            }
            // execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
            //close the handles
           foreach($api_request as $key1 => $request){
                // if($v != HB_HOTEL_BOOKING_SOURCE &&$v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    curl_multi_remove_handle($mh, ${"ch" . $key1});
                // }
            }
            curl_multi_close($mh);
            //Storing the Response
            // debug(curl_getinfo(${"ch" . $k}));
            // debug(curl_error(${"ch" . $k}));exit;
             foreach($api_request as $key1 => $request){
                // if($v != HB_HOTEL_BOOKING_SOURCE &&$v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    $curl_response[AGODA_HOTEL_BOOKING_SOURCE][$key1] = curl_multi_getcontent(${"ch" . $key1});
                    $this->CI->api_model->update_api_response($curl_response[AGODA_HOTEL_BOOKING_SOURCE][$key1], $request_insert_id[$key1]);
                // }
                $error = curl_getinfo (${"ch" . $key1});
            }
        }
        // debug($curl_response);exit;

        return $curl_response;
    }    
        
    public function execute_multi_curl_hotel($curl_params)
	{
		$curl_response = array();
		$request_insert_id = array();
		$booking_sources = $curl_params['booking_source'];
		$requests = $curl_params['request'];
		$urls = $curl_params['url'];
		$headers = $curl_params['header'];
		$remarks = $curl_params['remarks'];
                $cookie_file= @$curl_params['cookie'];
                
		if(valid_array($booking_sources) == true && valid_array($requests) == true && 
			valid_array($urls) == true && valid_array($headers) == true){
			foreach ($booking_sources as $k => $v){
				if($v != AGODA_HOTEL_BOOKING_SOURCE){
				$api_url = $urls[$k];
				$api_request = $requests[$k];
				$api_header = $headers[$k];
                                $cookie_file=$cookie_file[$k];
				// create both cURL resources
				${"ch" .$k} = curl_init();
				// set URL and other appropriate options
				curl_setopt(${"ch" .$k}, CURLOPT_URL, $api_url);
				curl_setopt(${"ch" .$k}, CURLOPT_TIMEOUT, 180);
				curl_setopt( ${"ch" .$k}, CURLOPT_HEADER, 0);
				curl_setopt(${"ch" .$k}, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt(${"ch" .$k}, CURLOPT_POST, 1);
				curl_setopt(${"ch" .$k}, CURLOPT_POSTFIELDS, $api_request);
				curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt(${"ch" .$k}, CURLOPT_SSL_VERIFYPEER, FALSE);
				if($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE){
					curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, FALSE);
				}


                if($v !=GRN_CONNECT_HOTEL_BOOKING_SOURCE){
					curl_setopt(${"ch" .$k}, CURLOPT_SSLVERSION, 3);
				        curl_setopt(${"ch" .$k}, CURLOPT_FOLLOWLOCATION, TRUE);
				}
                                curl_setopt(${"ch" .$k}, CURLOPT_HTTPHEADER, $api_header);
                               
				 if($v !=GRN_CONNECT_HOTEL_BOOKING_SOURCE){
					curl_setopt(${"ch" .$k}, CURLOPT_ENCODING, "gzip,deflate");
				  }				
				
				//Store API Request
				$backtrace = debug_backtrace();
				$method_name = $backtrace[1]['function'];
				$api_remarks = $method_name.'('.$remarks[$k].')';
				$temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $api_request, $api_remarks);
				$request_insert_id[$k] = intval(@$temp_request_insert_id['insert_id']);
			}
			}
			//create the multiple cURL handle
			$mh = curl_multi_init();
			//add the handles
            //debug($api_request);exit;
			foreach ($booking_sources as $k => $v){
				if($v != AGODA_HOTEL_BOOKING_SOURCE){
					curl_multi_add_handle($mh,${"ch" .$k});
				}
			}
			// execute all queries simultaneously, and continue when all are complete
	  		$running = null;
	  		do {
	    		curl_multi_exec($mh, $running);
	  		} while ($running);
			//close the handles
			foreach ($booking_sources as $k => $v){
				if($v != AGODA_HOTEL_BOOKING_SOURCE){
					curl_multi_remove_handle($mh, ${"ch" . $k});
				}
			}
			curl_multi_close($mh);
			//Storing the Response
			//debug(curl_getinfo(${"ch" . $k}));
			//debug(curl_error(${"ch" . $k}));
                       // exit;
			foreach ($booking_sources as $k => $v){
				if($v != AGODA_HOTEL_BOOKING_SOURCE){
					$curl_response[$booking_sources[$k]] = curl_multi_getcontent(${"ch" . $k});
					//$this->CI->api_model->update_api_response("NULL", $request_insert_id[$k]);
                    $this->CI->api_model->update_api_response($curl_response[$booking_sources[$k]], $request_insert_id[$k]);
					//$error = curl_getinfo (${"ch" . $k});
				}
			}
		}
		
		return $curl_response;
	}
    public function execute_multi_curl_bus($curl_params) {
        $curl_response = array();
        $request_insert_id = array();
        $booking_sources = $curl_params['booking_source'];
        $requests = $curl_params['request'];
        $urls = $curl_params['url'];
        $headers = $curl_params['header'];
        $remarks = $curl_params['remarks'];
        $cookie_file = @$curl_params['cookie'];

        if (valid_array($booking_sources) == true && valid_array($requests) == true &&
                valid_array($urls) == true && valid_array($headers) == true) {
            foreach ($booking_sources as $k => $v) {
              
                $api_url = $urls[$k];
                $api_request = $requests[$k];
                $api_header = $headers[$k];
                $cookie_file = $cookie_file[$k];


                // create both cURL resources
                ${"ch" . $k} = curl_init();
                // set URL and other appropriate options
                if($v != ETRAVELSMART_BUS_BOOKING_SOURCE){
                    curl_setopt(${"ch" . $k}, CURLOPT_URL, $api_url.''.$api_request);
                    curl_setopt(${"ch" . $k}, CURLOPT_ENCODING, "gzip");
                    curl_setopt(${"ch" . $k}, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt(${"ch" . $k}, CURLOPT_HTTPGET, TRUE);
                    curl_setopt(${"ch" . $k}, CURLOPT_HTTPHEADER, $api_header);
                }
                else{
                    $username = $api_header[0];
                    $password = $api_header[1];
                   
                    curl_setopt(${"ch" . $k}, CURLOPT_URL, $api_url.''.$api_request);
                    curl_setopt(${"ch" . $k}, CURLOPT_ENCODING, "gzip");
                    curl_setopt(${"ch" . $k}, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt(${"ch" . $k}, CURLOPT_USERPWD, "$username:$password");
                    curl_setopt(${"ch" . $k}, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
                }  
              
                //Store API Request
                $backtrace = debug_backtrace();
                $method_name = $backtrace[1]['function'];
                $api_remarks = $method_name . '(' . $remarks[$k] . ')';
                $temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $api_request, $api_remarks);
                $request_insert_id[$k] = intval(@$temp_request_insert_id['insert_id']);
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            //add the handles
            foreach ($booking_sources as $k => $v) {
                curl_multi_add_handle($mh, ${"ch" . $k});
            }
            // execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
            //close the handles
            foreach ($booking_sources as $k => $v) {
                curl_multi_remove_handle($mh, ${"ch" . $k});
            }
            curl_multi_close($mh);
            //Storing the Response
            //debug(curl_getinfo(${"ch" . $k}));
            //debug(curl_error(${"ch" . $k}));
            foreach ($booking_sources as $k => $v) {
                $curl_response[$booking_sources[$k]] = curl_multi_getcontent(${"ch" . $k});
                //$this->CI->api_model->update_api_response($curl_response[$booking_sources[$k]], $request_insert_id[$k]);
                  $this->CI->api_model->update_api_response("NULL", $request_insert_id[$k]);
                //$error = curl_getinfo (${"ch" . $k});
            }
        }

        return $curl_response;
    }
     public function execute_multi_curl_travelport($curl_params) {
        
        $curl_response = array();
        $request_insert_id = array();
        $booking_sources = $curl_params['booking_source'];
        $requests = $curl_params['request'];
        $urls = $curl_params['url'];
        $headers = $curl_params['header'];
        $remarks = $curl_params['remarks'];
        if (valid_array($booking_sources) == true && valid_array($requests) == true && valid_array($urls) == true && valid_array($headers) == true) {
            foreach ($booking_sources as $k => $v) {
                
                if($v == TRAVELPORT_FLIGHT_BOOKING_SOURCE || $v == TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE){
                    $api_url = $urls[$k];

                    $api_request = $requests[$k];
                    // debug($api_request);exit;
                    $api_header = $headers[$k];
                   // debug($api_header);
                    foreach($api_request as $key1 => $request){
                        if($key1 == 1){
                            $api_header[4] = "Content-length: " . strlen($request);
                             // debug($api_header);exit;
                        }
                        // debug($api_header);
                        // create both cURL resources
                        ${"ch" .$key1} = curl_init();
                        // set URL and other appropriate options
                        curl_setopt(${"ch" .$key1}, CURLOPT_URL, $api_url);
                        curl_setopt(${"ch" .$key1}, CURLOPT_TIMEOUT, 180);
                        // curl_setopt(${"ch" .$key1}, CURLOPT_HEADER, 0);
                        curl_setopt(${"ch" .$key1}, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt(${"ch" .$key1}, CURLOPT_POST, 1);
                        curl_setopt(${"ch" .$key1}, CURLOPT_POSTFIELDS, $request);
                        curl_setopt(${"ch" .$key1}, CURLOPT_SSL_VERIFYHOST, 2);
                        curl_setopt(${"ch" .$key1}, CURLOPT_SSL_VERIFYPEER, FALSE);
                                        
                        curl_setopt(${"ch" .$key1}, CURLOPT_FOLLOWLOCATION, FALSE);
                       
                        
                        curl_setopt(${"ch" .$key1}, CURLOPT_HTTPHEADER, $api_header);
                        curl_setopt(${"ch" .$key1}, CURLOPT_ENCODING, "gzip,deflate");
                        curl_setopt(${"ch" .$key1}, CURLOPT_SSLVERSION, 6);
                        

                        //Store API Request
                        $backtrace = debug_backtrace();
                        $method_name = $backtrace[1]['function'];
                        $api_remarks = $method_name . '(' . $remarks[$k] . ')';
                        $server_info = $_SERVER;
                        $temp_request_insert_id = $this->CI->api_model->store_api_request($api_url, $request, $api_remarks,$server_info);
                        $request_insert_id[$key1] = intval(@$temp_request_insert_id['insert_id']);  
                   }
                }
                   // exit;
                    // create both cURL resources
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            //add the handles
             foreach($api_request as $key1 => $request){
                //if($v != HB_HOTEL_BOOKING_SOURCE &&$v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    curl_multi_add_handle($mh, ${"ch" . $key1});
               // }
            }
            // execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
            //close the handles
           foreach($api_request as $key1 => $request){
                // if($v != HB_HOTEL_BOOKING_SOURCE &&$v != GRN_CONNECT_HOTEL_BOOKING_SOURCE ){
                    curl_multi_remove_handle($mh, ${"ch" . $key1});
                // }
            }
            curl_multi_close($mh);
           
            foreach($api_request as $key1 => $request){
                $curl_response[TRAVELPORT_FLIGHT_BOOKING_SOURCE][$key1] = curl_multi_getcontent(${"ch" . $key1});
                $this->CI->api_model->update_api_response($curl_response[TRAVELPORT_FLIGHT_BOOKING_SOURCE][$key1], $request_insert_id[$key1]);
                // debug($curl_response);exit;
                
              $error = curl_getinfo (${"ch" . $key1});
            }
        }
      

        return $curl_response;
    } 
	/**
	 * Assigns the Curl Parameters(URL,Header info.,Request)
	 * @param unknown_type $request_params
	 * @param unknown_type $curl_request
	 * @param unknown_type $curl_url
	 * @param unknown_type $curl_header
	 * @param unknown_type $curl_booking_source
	 */
	/*
	public function assign_curl_params($request_params, & $curl_request, & $curl_url, & $curl_header, & $curl_booking_source)
	{
		$request = array($request_params['request']);
		$url = array($request_params['url']);
		$header = array($request_params['header']);
		$booking_source = array($request_params['booking_source']);
		$curl_remarks = (isset($request_params['remarks']) == true ? array(trim($request_params['remarks'])) : array(''));
		
		$curl_request = array_merge($curl_request, $request);
		$curl_url = array_merge($curl_url, $url);
		$curl_header = array_merge($curl_header, $header);
		$curl_booking_source = array_merge($curl_booking_source, $booking_source);
	}*/
}