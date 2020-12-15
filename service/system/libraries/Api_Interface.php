<?php
/**
 * Provab XML Class
 *
 * Handle XML Details
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Arjun J<arjun.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Api_Interface {
	/**
	 *
	 * @param array $query_details - array having details of query
	 */
	public function __construct()
	{
	}
	/**
	 * get response from server for the request
	 *
	 * @param $request 	   request which has to be processed
	 * @param $url	   	   url to which the request has to be sent
	 * @param $soap_action
	 *
	 * @return xml response
	 */
	public function get_json_response($url, $request=array(), $header_details){
		$header=array(
			'Content-Type:application/json',
			'Accept-Encoding:gzip, deflate',
			'x-Username:'.$header_details['UserName'],
			'x-DomainKey:'.$header_details['DomainKey'],
			'x-system:'.$header_details['system'],
			'x-Password:'.$header_details['Password']
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$res = curl_exec($ch);
		$res = json_decode($res, true);
		curl_close($ch);
		return $res;
	}


	/**
	 * Get xml response from URL for the request
	 * @param string $url
	 * @param xml	 $request
	 */
	public function get_xml_response($url, $request, $convert_to_array=true)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$request");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$xml = curl_exec($ch);

		//debug($xml);exit;
		if ($convert_to_array) {
			$data = Converter::createArray($xml);
		} else {
			$data = $xml;
		}
		return $data;
	}

	public function get_object_response($request_type, $request, $header_details)
	{
		$header = $header_details['header'];
		$credintials = $header_details['credintials'];
		
		$_header[] = new SoapHeader("http://provab.com/soap/", 'AuthenticationData', $header, "");
		$client = new SoapClient(NULL, array('location' => $credintials['URL'],
                  'uri' => 'http://provab.com/soap/','trace' => 1, 'exceptions' => 0));
		try {
			$result = $client->provab_api($request_type, $request, $_header);
			//debug($result);exit;
		} catch(Exception $err) {
			$err->getMessage();
		}
		//return $result;
		//echo $result;exit;
		return $result;
	}
}
