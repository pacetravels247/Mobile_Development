<?php
//--------------------------------------------------------------------------------------------------------------------

/**
 * get trip type based on internal code
 * @param string $type
 */
function get_trip_type($type)
{
	switch($type) {
		case 'circle' : $type = 'Round Way';
		break;
		case 'oneway' : $type = 'One Way';
		break;
		default : $type = 'Multi Stop';
		break;
	}
	return $type;
}