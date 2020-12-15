<?php
require_once BASEPATH . 'libraries/Common_Api_Grind.php';
/**
 * Provab Common Functionality For API Class
 *
 *
 * @package Provab
 * @subpackage provab
 * @category Libraries
 * @author Himani<himani.provab@gmail.com>
 * @link http://www.provab.com
 */

abstract class Common_Api_Hotel extends Common_Api_Grind {
	function __construct($module, $api) {
		parent::__construct ( $module, $api );
	}
	
	/**
	 * Format hotel summary details
	 * 
	 * @param integer $hotel_code
	 * @param string $hotel_name
	 * @param char $destination_code
	 * @param string $destination_name
	 * @param string $star_rating
	 * @param float $lat
	 * @param float $lon
	 * @param float $min_rate
	 * @param float $max_rate
	 * @param string $currency
	 * 
	 * @return array hotel summary details
	 */
	protected function format_hotel_summary($hotel_code, $hotel_name, $destination_code, $destination_name, $star_rating, $lat, $lon, $min_rate, $max_rate, $price, $currency, $facilities='', $description='',  $address='', $image=array(), $primary_image='', $email='', $website='', $contact='', $postal='', $accomodation_type='') {
		$hotel_summary = array();
		$hotel_summary['hotel_code'] = $hotel_code;
		$hotel_summary['hotel_name'] = $hotel_name;
		$hotel_summary['destination_code'] = $destination_code;
		$hotel_summary['destination_name'] = $destination_name; 
		$hotel_summary['star_rating'] = $star_rating; 
		$hotel_summary['facility'] = $facilities;
		$hotel_summary['description'] = $description;
		$hotel_summary['address'] = $address;
		$hotel_summary['postal'] = $postal;
		$hotel_summary['primary_image'] = $primary_image;
		$hotel_summary['image'] = $image;
		$hotel_summary['email'] = $email;
		$hotel_summary['website'] = $website;
		$hotel_summary['accomodation_type'] = $accomodation_type;
		$hotel_summary['contact'] = $contact;
		$hotel_summary['lat'] = $lat; 
		$hotel_summary['lon'] = $lon; 
		$hotel_summary['min_rate'] = $min_rate; 
		$hotel_summary['max_rate'] = $max_rate;
		$hotel_summary['price'] = $price; 
		$hotel_summary['currency'] = $currency; 
		 
		return $hotel_summary;
	}
	
	/**
	 * 
	 * @param array $hl
	 * @param object $cobj
	 * @param number $sid
	 * @param string $module
	 * @param array $fltr
	 * 
	 * @return Hotel list, search result count and filter result count
	 */
	function format_search_response($hl, $cobj, $sid, $module = 'b2c', $fltr = array()) { 
		$level_one = true;
		$current_domain = true;
		if ($module == 'b2c') {
			$level_one = false;
			$current_domain = true;
		} else if ($module == 'b2b') {
			$level_one = true;
			$current_domain = true;
		}
		$h_count = 0;
		$HotelResults = array ();
		if (isset ( $fltr ['hl'] ) == true) {
			foreach ( $fltr ['hl'] as $tk => $tv ) {
				$fltr ['hl'] [urldecode ( $tk )] = strtolower ( urldecode ( $tv ) );
			}
		}

		// Creating closures to filter data
		$check_filters = function ($hd) use($fltr) {

			//_acc type
			$any_facility = function ($cstr, $c_list)
			{
				foreach ($c_list as $k => $v) {
					if (stripos(($cstr), ($v)) > -1) {
						return true;
					}
				}
			};

			if (
			(valid_array ( @$fltr ['hl'] ) == false ||
			(valid_array ( @$fltr ['hl'] ) == true && in_array ( strtolower ( $hd ['location'] ), $fltr ['hl'] ))) &&

			(valid_array ( @$fltr ['_sf'] ) == false || (valid_array ( @$fltr ['_sf'] ) == true && in_array ( $hd ['star_rating'], $fltr ['_sf'] ))) &&

			(@$fltr ['min_price'] <= ceil ( $hd ['price']) && (@$fltr ['max_price'] != 0 && @$fltr ['max_price'] >= floor ( $hd ['price']))) &&

			(( string ) $fltr ['dealf'] == 'false' || empty ($hd ['HotelPromotion'] ) == false) &&

			(empty ( $fltr ['hn_val'] ) == true || (empty ( $fltr ['hn_val'] ) == false &&
			stripos ( strtolower ( $hd ['name'] ), (urldecode ( $fltr ['hn_val'] )) ) > - 1)) &&

			(valid_array ( @$fltr ['_fac'] ) == false ||
			(valid_array( @$fltr ['_fac'] ) == true && $any_facility($hd ['facility_cstr'], $fltr ['_fac']))
			) &&

			(valid_array ( @$fltr ['at'] ) == false ||
			(valid_array ( @$fltr ['at'] ) == true &&  in_array ( ( $hd ['accomodation_cstr'] ), $fltr ['at'] )))
			) {
				return true;
			} else {
				return false;
			}
		};
		//debug($check_filters);
		$hc = 0;
		$frc = 0;
		foreach ( $hl['hotel_list'] as $hr => $hd ) {
			//debug($hd);
			$hc ++;
			// markup
			//debug($hd ['price']);
			$price = $this->update_search_markup_currency ( $hd ['price'], $cobj, $sid, $level_one, $current_domain );
			//debug($price);exit;
			//$hd ['price'] = $price['value'];
			//$hd ['currency'] = $price['currency'];
			//$hd ['price'] = $hd ['price'];
			// filter after initializing default data and adding markup
			if (valid_array ( $fltr ) == true && $check_filters ( $hd ) == false) {
				continue;
				//debug($check_filters);exit;
			}
			$HotelResults [$hr] = $hd;
			$frc ++;
		}
		//debug($HotelResults);exit;
		$hl ['hotel_list'] = $HotelResults;
		$hl ['source_result_count'] = $hc;
		$hl ['filter_result_count'] = $frc;
		return $hl;
	}
	
	/**
	 * Get Filter Summary of the data list
	 *
	 * @param array $hl
	 */
	function filter_summary($hl) {
		$h_count = 0;
		$filt ['p'] ['max'] = false;
		$filt ['p'] ['min'] = false;
		$filt ['loc'] = array ();
		$filt ['star'] = array ();
		$filt ['currency'] = '';
		$filters = array ();
		foreach ( $hl ['hotel_list'] as $hr => $hd ) {
			$filt ['currency'] = $hd['currency'];
			// filters
			$StarRating = intval ( @$hd ['star_rating'] );
			$HotelLocation = $hd ['destination_name'];
			$AccomodationType = $hd['accomodation_type'];

			if (isset ( $filt ['star'] [$StarRating] ) == false) {
				$filt ['star'] [$StarRating] ['c'] = 1;
				$filt ['star'] [$StarRating] ['v'] = $StarRating;
			} else {
				$filt ['star'] [$StarRating] ['c'] ++;
			}

			if (($filt ['p'] ['max'] != false && $filt ['p'] ['max'] < $hd ['price']) || $filt ['p'] ['max'] == false) {
				$filt ['p'] ['max'] = roundoff_number ( $hd ['price']);
			}

			if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $hd ['price']) || $filt ['p'] ['min'] == false) {
				$filt ['p'] ['min'] = floor($hd ['price']);
			}
			$hloc = ucfirst ( strtolower ( $HotelLocation ) );
			if (isset ( $filt ['loc'] [$hloc] ) == false) {
				$filt ['loc'] [$hloc] ['c'] = 1;
				$filt ['loc'] [$hloc] ['v'] = $hloc;
			} else {
				$filt ['loc'] [$hloc] ['c'] ++;
			}

			$a_type =  $AccomodationType ;
			if (isset ( $filt ['a_type'] [$a_type] ) == false) {
				$filt ['a_type'] [$a_type] ['c'] = 1;
				$filt ['a_type'] [$a_type] ['v'] = $a_type;
			} else {
				$filt ['a_type'] [$a_type] ['c'] ++;
			}

			
			if (empty($hd['facility']) == false) {
				foreach ($hd['facility'] as $fk => $fv) {
					if (isset($filt['facility'][$fv['fc']]) == false) {
						$filt ['facility'] [$fv['fc']] ['c'] = 1;
						$filt ['facility'] [$fv['fc']] ['v'] = $fv['name'];
						$filt ['facility'] [$fv['fc']] ['icon'] = $fv['icon_class'];
						$filt ['facility'] [$fv['fc']] ['cstr'] = $fv['cstr'];
					} else {
						$filt ['facility'] [$fv['fc']] ['c'] ++;
					}
				}
			}

			$filters ['data'] = $filt;
			$h_count ++;
		}

		ksort ( $filters ['data'] ['loc'] );
		$filters ['hotel_count'] = $h_count;
		return $filters;
	}
	
	/**
	 * cache room details by hotelcode
	 * 
	 * @param array $hotel_room_details
	 */
	function cache_hotel_room_details(& $hotel_room_details)
	{
		$token = array();
		$this->ins_token_file = time().rand(100, 10000);
		$hotel_id = $hotel_room_details['hotel_code'];
		$tkn_key = $hotel_id;
		$this->push_token($hotel_room_details, $token, $tkn_key);
		$this->save_token($token);
	}
	
	/**
	 * 
	 * @param array $rooms
	 * @param string $room_rate_Keys
	 * @param date $from_date
	 * @param date $to_date
	 */
	protected function get_rate_comment_details($rooms, $room_rate_Keys, $from_date, $to_date) {
		return '';
	}
	/**
	 * Markup for search result
	 *
	 * @param array $price_summary
	 * @param object $currency_obj
	 * @param number $search_id
	 */
	function update_search_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
		$search_data = $this->search_data ( $search_id );
		$no_of_nights = $this->master_search_data ['no_of_nights'];
		$no_of_rooms = $this->master_search_data ['room_count'];
		$multiplier = ($no_of_nights * $no_of_rooms);
		return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
	}
	
	/**
	 * adds token and token key to flight and push data to token for caching
	 * @param array $hotel_room_data	Flight for which token and token key has to be generated
	 * @param array $token	Token array for caching
	 * @param string $key	Key to be used for caching
	 */
	private function push_token(& $hotel_room_data, & $token, $key)
	{
		//push data inside token before adding token and key values
		$token[$key] = $hotel_room_data;

		//Adding token and token key
		$hotel_room_data['Token'] = serialized_data($this->ins_token_file.DB_SAFE_SEPARATOR.$key);
		$hotel_room_data['TokenKey'] = md5($hotel_room_data['Token']);
	}

	public function read_token($token_key)
	{
		$token_key = explode(DB_SAFE_SEPARATOR, unserialized_data($token_key));

		if (valid_array($token_key) == true) {
			$file = DOMAIN_TMP_UPLOAD_DIR.$token_key[0].'.json';//File name
			$index = $token_key[1]; // access key

			if (file_exists($file) == true) {
				$token_content = file_get_contents($file);
				if (empty($token_content) == false) {
					$token = json_decode($token_content, true);

					if (valid_array($token) == true && isset($token[$index]) == true) {
						return $token[$index];
					} else {
						return false;
						echo 'Token data not found';
						exit;
					}
				} else {
					return false;
					echo 'Invalid File access';
					exit;
				}
			} else {
				return false;
				echo 'Invalid Token access';
				exit;
			}
		} else {
			return false;
			echo 'Invalid Token passed';
			exit;
		}
	}
	
	/**
	 * Save token and cache the data
	 * @param array $token
	 */
	private function save_token($token)
	{
		$file = DOMAIN_TMP_UPLOAD_DIR.$this->ins_token_file.'.json';
		file_put_contents($file, json_encode($token));
	}
	
	
	
}