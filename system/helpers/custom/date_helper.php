<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_day_numbers()
{
	$day_numbers = array();
	// build days menu
	for ($d=1; $d<=31; $d++) {
		$day_numbers[$d] = $d;
	}
	return $day_numbers;
}

function get_month_names()
{
	$month_names = '';
	//build months menu
	/*for ($m=1; $m<=12; $m++) {
		$month_names[($m-1)] = date('M', mktime(0,0,0,$m, 1));
	}*/
	$month_names = get_enum_list('month_names');
	
	return $month_names;
}
function passport_get_day_numbers($start)
{
	$day_numbers = '';
	// build days menu
	for ($d=$start; $d<=31; $d++) {
		$day_numbers[$d] = $d;
	}
	return $day_numbers;
}

function passport_get_month_names($start)
{
	$month_names = '';
	$start = $start-1;
	$end = 12-$start;
	//build months menu
	/*for ($m=1; $m<=12; $m++) {
		$month_names[($m-1)] = date('M', mktime(0,0,0,$m, 1));
	}*/
	$month_names = get_enum_list('month_names');
	$month_names = array_slice($month_names, $start, $end, true);
	
	return $month_names;
}

function get_years($start, $end)
{
	$years = array();
	// build years menu
	for ($y=$start; $y<=$end; $y++) {
		$years[$y] = $y;
	}
	return $years;
}

function add_months_to_date($months, $date='')
{
	if (empty($date) == true) {
		$date = date('d-m-Y');
	}
	return date('Y-m-d', strtotime($date.' +'.$months.' month'));;
}

function add_days_to_date($days, $date='')
{
	if (empty($date) == true) {
		$date = date('d-m-Y');
	}
	return date('d-m-Y H:i:s', strtotime($date.' +'.$days.' day'));;
}

function subtract_days_from_date($days, $date='')
{
	if (empty($date) == true) {
		$date = date('Y-m-d');
	}
	return date('d-m-Y', strtotime($date.' -'.$days.' day'));;
}
/**
 *Getting the dates in selected range
 *This function works best with YYYY-MM-DD
 */
function getDateRange($startDate, $endDate, $format="Y-m-d")
{
	//Create output variable
	$datesArray = array();
	//Calculate number of days in the range
	$total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;
	if($total_days < 0) {
		return false;
	}
	//Populate array of weekdays and counts
	for($day=0; $day<$total_days; $day++)
	{
		$datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));
	}
	//Return results array
	return $datesArray;
}

/*
 * pass date string and function converts to timestamp and return date in friendly format
 */
function app_friendly_date($db_datetime)
{
	$db_datetime = strtotime($db_datetime);
	if ($db_datetime > 0) {
		return friendly_format($db_datetime);
	} else {
		return '<strong class="text-danger">dd-mm-yy</strong>';
	}
}

/*
 * pass date string and function converts to timestamp and return date in friendly format
 */
function app_friendly_datetime($db_datetime)
{
	$db_datetime = strtotime($db_datetime);
	if ($db_datetime > 0) {
		return date('D, d-M H:i a', $db_datetime);
	}
}

function app_friendly_absolute_date($db_datetime)
{
	$db_datetime = strtotime($db_datetime);
	if ($db_datetime > 0) {
		return date('d-M-Y', $db_datetime);
	}
}
/**
 * pass timestamp and get date format in friendly format
 * @param unknown_type $datetime
 */
function app_friendly_day($datetime)
{
	return date('D, d-M ', strtotime($datetime));
}


/**
 * pass timestamp and get date format in friendly format
 * @param unknown_type $datetime
 */
function friendly_format($datetime)
{
	return date('d M Y D', $datetime);
}

/**
 * This does not help to find year
 */
function local_date($date)
{
	return date('D d',strtotime($date));
}
/**
 * This does not help to find year
 */
function local_date1($date)
{
	return date('d l M Y',strtotime($date));
}
/**
 * This does not help to find year
 */
function local_month_date($date)
{
	return date('d M Y',strtotime($date));
}
/**
 * This does not help to find year time
 */
function local_month_time_date($date)
{	
	//d M Y g:i A
	return date('d M Y',strtotime($date));
}
/**
 * This does not help to find year
 */
function local_date_new($date)
{
	return date('d-m-Y',strtotime($date));
}
/**
 * This does not help to find year
 */
function local_time($date)
{
	return date('H:i',strtotime($date));
}
/*
 * Balu A
 * date format: 26 Jun, 08:30
 */
function month_date_time($date)
{
	return date('d M, H:i',strtotime($date));
}
/*
 * Balu A
 * date format: 26 Jun, 08:30
 */
function month_date_year_time($date)
{
	return date('d M Y, H:i',strtotime($date));
}
//GET no of days between 2 dates
function get_date_difference($date1, $date2)
{
	$date1 = strtotime($date1);
	$date2 = strtotime($date2);
	return floor(($date2-$date1)/(60*60*24));
}

//GET no of years between 2 dates
function get_year_difference($date1, $date2='')
{
	if (empty($date2) == true) {
		$date2 = date('Y-m-d');
	}
	$year1 = date('Y', strtotime($date1));
	$year2 = date('Y', strtotime($date2));
	return floor(($year2-$year1));
}

/**
 * CHECK IF JUNK DATE IS PASSED
 */
function valid_date_value($date)
{
	if (strtotime($date) > 0) {
		return date('d-m-Y', strtotime($date));
	} else {
		return date('d-m-Y');
	}
}
/**
 * Return current datetime in database format
 */
function db_current_datetime($date='')
{
	if (empty($date) == false) {
		return date('Y-m-d H:i:s', strtotime($date));
	} else {
		return date('Y-m-d H:i:s', time());
	}
}


/**
 * return only time from datetime value
 * @param datetime $datetime
 */
function get_time($datetime)
{
	return date('h:i A', strtotime($datetime));
}

/**
 * calculate difference of time in mins between time1 and time2
 * @param mixed $time1
 * @param mixed $time2
 */
function calculate_duration($time1, $time2)
{
	$time1 = strtotime($time1);
	$time2 = strtotime($time2);
	return abs($time2-$time1);
}

/**
 * Report Specific - to get interval details of a date
 * @param string $duration_type indication day, week or year
 *
 * @return array having from and to date
 */
function get_duration_limit($duration_type='day', $interval_date = '')
{
	$to = date('Y-m-d');
	switch($duration_type) {
		case 'day' :
			$from = $to;
			break;
		case 'week' :
			if(empty($interval_date) == false) {
				if(strtolower(date('l', strtotime($interval_date))) != 'sunday') {
					$from = date('Y-m-d', strtotime('last sunday', strtotime($interval_date)));
				} else {
					$from = date('Y-m-d', strtotime($interval_date));
				}
				if(strtolower(date('l', strtotime($interval_date))) != 'saturday') {
					$to = date('Y-m-d', strtotime('next saturday', strtotime($interval_date)));
				} else {
					$to = date('Y-m-d', strtotime($interval_date));
				}
			} else if (strtolower(date('l')) != 'sunday') {
				$from = date('Y-m-d', strtotime('last sunday'));
			} else {
				$from = date('Y-m-d');
			}
			break;
		case 'month' :
			if(empty($interval_date) == false) {
				$from =  date('Y-m-01', strtotime($interval_date));
				$to = date('Y-m-t', strtotime($interval_date));
			} else {
				$from = date('Y-m-1');
			}
			break;
		case 'year' :
			if(empty($interval_date) == false) {
				$from =  date('Y-1-1', strtotime($interval_date));
				$to = date('Y-12-t', strtotime($interval_date));
			} else {
				$from = date('Y-1-1');
			}
			break;
	}
	return array('from' => $from, 'to' => $to);
}


/**
 * get duration label day : hours : minutes
 */
function get_duration_label($seconds)
{
	$dur = '';
	$days = floor($seconds / 86400);
	$hours = floor(($seconds - ($days * 86400)) / 3600);
	$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
	// $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

	if ($days > 0) {
		if (intval($days) > 1) {
			$label = ' Days ';
		} else {
			$label = ' Day ';
		}
		$dur .= $days .$label;
	}
	if ($hours > 0) {
		if (intval($hours) > 1) {
			$label = ' Hrs ';
		} else {
			$label = ' Hr ';
		}
		$dur .= $hours.$label;
	}
	if ($minutes > 0) {
		if (intval($minutes) > 1) {
			$label = ' Mins ';
		} else {
			$label = ' Min ';
		}
		$dur .= $minutes.$label;
	}
	return $dur;
}

/**
 * get duration label day : hours : minutes
 */
function get_time_duration_label($seconds)
{
	$dur = '';
	$days = floor($seconds / 86400);
	$hours = floor(($seconds) / 3600);
	$minutes = floor(($seconds - ($hours * 3600)) / 60);
	// $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

	/*if ($days > 0) {
		if (intval($days) > 1) {
		$label = ' Days ';
		} else {
		$label = ' Day ';
		}
		$dur .= $days .$label;
		}*/
	if ($hours > 0) {
		if (intval($hours) > 1) {
			$label = 'h ';//hrs
		} else {
			$label = 'h ';
		}
		$dur .= $hours.$label;
	}
	if ($minutes > 0) {
		if (intval($minutes) > 1) {
			$label = 'm ';//mins
		} else {
			$label = 'm ';
		}
		$dur .= $minutes.$label;
	}
	return $dur;
}

/**
 * Get event class based on datatime passed(to differentiate between past events and future events)
 * @param Date $data
 */
function event_class($data)
{
	if (strtotime($data) > time()) {
		return 'active-event';
	} else {
		return 'inactive-event';
	}
}

/**
 * get day label for days to go or days ago or current day event
 * @param number $day_count
 */
function day_count_label($day_count)
{
	if ($day_count > 0) {
		$day_count = $day_count.' Day(s) To Go ';
	} else if ($day_count < 0) {
		$day_count = abs($day_count).' Day(s) Ago ';
	} else {
		$day_count = ' Today - ';
	}
	return $day_count;
}

function timeline_day_count($date)
{
	$today_start = strtotime(date('Y-m-d'));
	$event_seconds = strtotime($date);
	$event_date = date('Y-m-d', $event_seconds);
	//if current date then print offset else days ago
	if (date('Y-m-d') == $event_date) {
		$count_label = get_duration_label(time()-$event_seconds);
	} else {
		$seconds = ($today_start-strtotime($event_date));
		$count_label = get_duration_label($seconds);
	}
	if (empty($count_label) == true) {
		$count_label = 'few moments';
	}
	return $count_label;
}

/**
 * Date output by javascript format
 */
function front_end_date($date='')
{
	if (empty($date) == true) {
		return false;
	} else {
		return date('Y-m-d', strtotime($date));
	}
}

/**
 * Get Duration between 2 dates in terms of hours and minutes
 */
function get_total_duration($date1, $date2)
{
	$hours = 0;
	$minutes = 0;
	$date1 = strtotime($date1);
	$date2 = strtotime($date2);
	if ($date1 > 0 && $date2 > 0) {
		$duration = $date1 - $date2;
		$hours = floor($duration/3600);
		$minutes = intval(($duration/60) % 60);
	}
	return array('hours' => $hours, 'minutes' => $minutes);
}

/**
 * pass timestamp and get date format in header format
 * 
 * @param unknown_type $db_datetime        	
 */
 
function api_header_date($db_datetime) 
{
	$db_datetime = strtotime ($db_datetime);
	if ($db_datetime > 0) {
		return date ( "d| M 'y D", $db_datetime );
	}
}
/**
Balu A
 */
function hotel_check_in_out_dates($db_datetime)
{
	$db_datetime = strtotime ($db_datetime);
	if ($db_datetime > 0) {
		return date ("d|M|Y", $db_datetime );
	}
}

/**
Elavarasi
 */
function sightseeing_travel_date($db_datetime)
{
	$db_datetime = strtotime ($db_datetime);
	if ($db_datetime > 0) {
		return date ("d|M|Y", $db_datetime );
	}
}


/**
Balu A
 */
function auto_swipe_dates($from_date, $to_date)
{
	if(empty($from_date) == false && empty($to_date) == false) {
		if(strtotime($from_date) > strtotime($to_date)) {//Validating From date and To date
			return array('from_date' => $to_date, 'to_date' => $from_date);
		} else {
			return array('from_date' => $from_date, 'to_date' => $to_date);
		}
	}
}

