<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Cron_job_model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class Cron_job_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Not Confirmed Ticket Details
	 */
	public function not_confirmed_tickets()
	{
		$query = 'select distinct(BD.app_reference) from flight_booking_details as BD
					join flight_booking_transaction_details TD on BD.app_reference=TD.app_reference and TD.book_id!=""
					join flight_booking_passenger_details CD on TD.origin=CD.flight_booking_transaction_details_fk
					join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk and FPTI.TicketNumber=""
					where BD.booking_source="'.PROVAB_FLIGHT_BOOKING_SOURCE.'" and BD.journey_end >=NOW() order by BD.origin desc';
		$query_result = $this->db->query($query)->result_array();
		return $query_result;
	}
}