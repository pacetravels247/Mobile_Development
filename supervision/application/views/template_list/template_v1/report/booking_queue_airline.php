<!--<script src="<?php echo SYSTEM_RESOURCE_LIBRARY ?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY ?>/datatables/dataTables.bootstrap.min.js"></script> -->
<?= $GLOBALS['CI']->template->isolated_view('report/email_popup') ?>

<?php
if (is_array($search_params)) {
    extract($search_params);
    //echo '<pre>'; print_r($search_params);die;
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>
<div class="modal fade" id="pax_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document" style="width: 880px;">    <div class="modal-content">    <div class="modal-header">        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-users"></i> 
                    Customer Details</h4> 
            </div>   
            <div class="modal-body">  
                <div id="customer_parameters">    	
                </div>   
            </div>  
        </div> 
    </div>
</div>
<div class="bodyContent col-md-12">
    <div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
       
        <div class="panel-body">
           <h4>Advanced Search Panel <button class="btn btn-primary btn-sm toggle-btn" data-toggle="collapse" data-target="#show-search">+
                </button> </h4>
            <hr>
            <div id="show-search" class="collapse">

                <form method="GET" autocomplete="off" action="<?= base_url() . 'report/b2b_flight_report?' ?>">

                    <div class="clearfix form-group">
                        <div class="col-xs-4">
                            <label>
                                Suppliers
                            </label>
                            <select class="form-control" name="supplier_id">
                                <option value="">Select</option>
                                <?= generate_options($supplier_list, (array) @$supplier_id) ?>
                            </select>
                        </div>
                       
                        <div class="col-xs-4">
                            <label>
                                PNR
                            </label>
                            <input type="text" class="form-control" name="pnr" value="<?= @$pnr ?>" placeholder="PNR">
                        </div>
                        <div class="col-xs-4">
                            <label>
                                Application Reference
                            </label>
                            <input type="text" class="form-control" name="app_reference" value="<?= @$app_reference ?>" placeholder="Application Reference">
                        </div>
                        <!--<div class="col-xs-4">
                                <label>
                                Phone
                                </label>
                                <input type="text" class="form-control numeric" name="phone" value="<?= @$phone ?>" placeholder="Phone">
                        </div>
                        <div class="col-xs-4">
                                <label>
                                Email
                                </label>
                                <input type="text" class="form-control" name="email" value="<?= @$email ?>" placeholder="Email">
                        </div>-->
                        <!-- <div class="col-xs-4">
                            <label>
                                Status
                            </label>
                            <select class="form-control" name="status">
                                <option>All</option>
                                <?= generate_options($status_options, array(@$status)) ?>
                            </select>
                        </div> -->
                        <div class="col-xs-4">
                            <label>
                                Booked From Date
                            </label>
                            <input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?= @$created_datetime_from ?>" placeholder="Request Date">
                        </div>
                        <div class="col-xs-4">
                            <label>
                                Booked To Date
                            </label>
                            <input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?= @$created_datetime_to ?>" placeholder="Request Date">
                        </div>
                    </div>
                    <div class="col-sm-12 well well-sm">
                        <button type="submit" class="btn btn-primary">Search</button> 
                        <button type="reset" id="btn-reset" class="btn btn-warning">Reset</button>
                        <a href="<?= base_url() . 'report/b2b_flight_report?' ?>" id="clear-filter" class="btn btn-primary">Clear Filter</a>
                    </div>

                </form>
            </div>
            <!-- EXCEL/PDF EXPORT STARTS -->
            <?php if($total_records > 0){ ?>
            <div class="clearfix"></div>
                <div class="dropdown col-xs-3">
                    <button class="btn btn-info dropdown-toggle" type="button" id="excel_imp_drop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-download" aria-hidden="true"></i> Excel
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="excel_imp_drop">
                        <li >
                            <a href="<?php echo base_url(); ?>index.php/report/export_confirmed_booking_airline_report_b2b/excel/confirmed_cancelled<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Confirmed & Cancelled Booking</a>
                        </li>
                        <li >
                            <a href="<?php echo base_url(); ?>index.php/report/export_confirmed_booking_airline_report_b2b/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Confirmed Booking</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/report/export_cancelled_booking_airline_report_b2b/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Cancelled Booking</a>
                        </li>
                    </ul>
                </div>
            
            <?php } ?>

        </div>

        <div class="clearfix table-responsive"><!-- PANEL BODY START -->
            <div class="pull-left">
                <?php echo $this->pagination->create_links(); ?> <span class="">Total <?php echo $total_rows ?> Bookings</span>
            </div> 

            <table class="table table-condensed table-bordered" id="b2b_report_airline_table rigid_actions">
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Application Reference</th>
                        <th>Status</th>
                        <th>Lead Pax <br/>Details</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Type</th>
                        <th>BookedOn</th>
                        <th>JourneyDate</th>
                        <th>Sup. Name</th>
                        <th>Airline Name</th>
                        <th>PNR</th>
                        <th>GDS PNR</th>
                        <th>Base Fare</th>
                        <th>Tax</th>
                        <th>Extra Services</th>
                        <th>Comm.Fare</th>
                        <th>Sup. Commission</th>
                        <th>Sup. TDS</th>
                        <th>Pace Commission</th>
                        <th>Pace Tds</th>
                        <th>Admin NetFare</th>
                        <th>Admin<br/>Markup</th>
                        <th>GST</th>
                        <th>Agent<br/>Commission</th>
                        <th>Agent<br/>TDS</th>
                        <th>Agent <br/>Net Fare</th>
                        <th>Agent<br/>Markup</th>
                        <th>Convenience</th>
                        <th>TotalFare</th>
                        <th>Your<br/>Profit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno</th>
                        <th>Application Reference</th>
                        <th>Status</th>
                        <th>Lead Pax <br/>Details</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Type</th>
                        <th>BookedOn</th>
                        <th>JourneyDate</th>
                        <th>Sup. Name</th>
                        <th>Airline Name</th>
                        <th>PNR</th>
                        <th>GDS PNR</th>
                        <th>Base Fare</th>
                        <th>Tax</th>
                        <th>Extra Services</th>
                        <th>Comm.Fare</th>
                        <th>Sup. Commission</th>
                        <th>Sup. TDS</th>
                        <th>Pace Commission</th>
                        <th>Pace Tds</th>
                        <th>Admin NetFare</th>
                        <th>Admin<br/>Markup</th>
                        <th>GST</th>
                        <th>Agent <br/>Commission</th>
                        <th>Agent<br/>TDS</th>
                        <th>Agent <br/>Net Fare</th>
                        <th>Agent<br/>Markup</th>
                        <th>Convenience</th>
                        
                        <th>TotalFare</th>
                        <th>Your<br/>Profit</th>
                        <th>Action</th>
                    </tr>
                </tfoot><tbody>
                    <?php
                    //debug($table_data['booking_details']);exit;
                    if (valid_array($table_data['booking_details']) == true) {
                        $booking_details = $table_data['booking_details'];
                        $segment_3 = $GLOBALS['CI']->uri->segment(3);
                        $current_record = (empty($segment_3) ? 1 : $segment_3 + 1);
                        foreach ($booking_details as $parent_k => $parent_v) {
                            extract($parent_v);
                            $action = '';
                            $cancellation_btn = '';
                            $voucher_btn = '';

                            $booked_by = '';
                            
                            $pace_commission = $admin_commission-$agent_commission;
                            $pace_tds = $admin_tds-$agent_tds;
                            $airline_name = $booking_itinerary_details[0]["airline_name"];
                            //debug($parent_v); exit;
                            //Status Update Button
                            /* if (in_array($status, array('BOOKING_CONFIRMED')) == false) {
                              switch ($booking_source) {
                              case PROVAB_FLIGHT_BOOKING_SOURCE :
                              $status_update_btn = '<button class="btn btn-success btn-sm update-source-status" data-app-reference="'.$app_reference.'"><i class="far fa-database"></i> Update Status</button>';
                              break;
                              }
                              } */



                            $voucher_btn = flight_voucher($app_reference, $booking_source, $status);
                            //$invoice = flight_invoice($app_reference, $booking_source, $status);
                            $invoice = flight_GST_Invoice($app_reference, $booking_source, $status);
                            $cancel_btn = flight_cancel($app_reference, $booking_source, $status);
                            $pdf_btn = flight_pdf($app_reference, $booking_source, $status);
                            $email_btn = flight_voucher_email($app_reference, $booking_source, $status, $email);
                            $multi_voucher_btn = multi_voucher_links($app_reference, $booking_source, $status);
							$customer_details = customer_details($app_reference, $booking_source, $status, $is_domestic);
                            $error_details = error_details($app_reference, $booking_source, $status);
                            $jrny_date = date('Y-m-d', strtotime($journey_start));
                            $tdy_date = date('Y-m-d');
                            $diff = get_date_difference($tdy_date, $jrny_date);
                            $edit_ticket .= check_run_ticket_method($parent_v['app_reference'], $parent_v['booking_source'], $status, $parent_v['is_domestic'], $parent_v['journey_start']);
                            $edit_voucher_btn = edit_ticket_button($app_reference, $booking_source, $status);
                            $action .= $voucher_btn;
                            $action .= '<br />' . $pdf_btn;
                            $action .= '<br />' . $email_btn;
                            $action .= '<br />' . $customer_details;
                            $action .= '<br />' . $invoice;
                            $action .= '<br />' . $edit_voucher_btn;
							$action .=$multi_voucher_btn;
                            if ($status != 'BOOKING_CONFIRMED' && $status != 'BOOKING_HOLD' && $status != 'BOOKING_CANCELLED' && $status != 'BOOKING_INPROGRESS') {
                                $action .= '<br />' . $error_details;
                            }
                            if ($diff > 0 || $diff <= 0) {
                                $action .= $cancel_btn;
                            }

                            if ($status != 'BOOKING_CANCELLED') {

                               /* if (strtotime('now') < strtotime($parent_v['journey_start'])) {
                                    $update_booking_details_btn = update_booking_details($app_reference, $booking_source, $status);
                                    $action .= '<br />' . $update_booking_details_btn;
                                }*/
                                /*$action .='<a href="'.base_url().'index.php/flight/flight_offline_cancel/'.$app_reference.'/'.$booking_source.'/'.$status.'/show_voucher/'.$current_module.'" class="btn btn-sm btn-warning" title="Offline Cancellation" target="_blank"><i class="fa fa-ban" aria-hidden="true"></i></a>';*/   
                            }

                            $action .= get_cancellation_details_button($parent_v['app_reference'], $parent_v['booking_source'], $parent_v['status'], $parent_v['booking_transaction_details']);
							$es_dets = $GLOBALS["CI"]->flight_model->get_extra_service_details($app_reference);
							$esc = $GLOBALS["CI"]->flight_model->get_extra_service_charges($es_dets);
                            $profit = $admin_markup+$pace_commission-$pace_tds;

                            $total_profit += $profit;
                            $gds_pnr = $booking_transaction_details[0]['gds_pnr'];
                            if(empty($gds_pnr) == true){
                                $gds_pnr = $pnr;
                            }
							//debug($esc); exit;
                            ?>
                            <tr>
                                <td><?= ($current_record++) ?></td>
                                <td><?php echo $app_reference; ?></td>
                                <td><span class="<?php echo booking_status_label($status) ?>"><?php echo $status ?></span></td>
                                <td>
                                    <?php
                                    echo $lead_pax_name . '<br/>' .
                                    $email . "<br/>" .
                                    $phone;
                                    ?>
                                </td>
                                <td><?php echo $from_loc ?></td>
                                <td><?php echo $to_loc ?></td>
                                <td><?php echo $trip_type_label ?></td>
                                <td><?php echo date('d-m-Y', strtotime($booked_date)) ?></td>
                                <td><?php echo date('d-m-Y', strtotime($journey_start)) ?></td>
                                <td><?= @$supp_name ?></td>
                                <td><?= @$airline_name ?></td>
                                <td><?= @$pnr ?></td>
                                <td><?=@$gds_pnr?></td>
                                <td><?php echo $total_api_base_fare; ?></td>
                                <td><?php echo $total_api_tax; ?></td>
                                <td><?php echo ($esc["baggage"]+$esc["seat"]+$esc["meal"]); ?></td>
                                <td><?php echo $fare ?></td>
                                <td><?php echo $admin_commission ?></td>
                                <td><?php echo $admin_tds ?></td>
                                <td><?php echo $pace_commission; ?></td>
                                <td><?php echo $pace_tds; ?></td>
                                <td><?php echo ($net_fare+$esc["baggage"]+$esc["seat"]+$esc["meal"]); ?></td>
                                <td><?php echo $admin_markup ?></td>
                                <td><?php echo $gst ?></td>
                                <td><?php echo $agent_commission ?></td>
                                <td><?php echo $agent_tds ?></td>
                                <td><?php echo ($agent_buying_price+$esc["baggage"]+$esc["seat"]+$esc["meal"]); ?></td>
                                <td><?php echo $agent_markup ?></td>
                                <td><?php echo $convinence_amount ?></td>

                                <td><?php echo ($grand_total+$esc["baggage"]+$esc["seat"]+$esc["meal"]); ?></td>
                                <td><?php echo $profit ?></td>
                                <td><div class="action_system" role="group"><?php echo $action; ?></div></td>
                            </tr>
                            <?php
                        } ?>
                        <tr>
                            <th colspan="22">Total</th>
                            <th colspan="2"><?php echo $total_profit; ?></th>
                        </tr>
                    <?php    
                    } else {
                        echo '<tr><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
										   <td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
										   <td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>';
                    }
                    ?>
                </tbody>
            </table>


        </div>
    </div>
</div>
<!-- Exception Log Modal starts -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="exception_log_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Error Log Details - <strong><i id="exception_app_reference"></i></strong></h4>
            </div>
            <div class="modal-body" id="exception_log_container">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Exception Log Modal ends -->
<script>
    $(document).ready(function () {
        //update-source-status update status of the booking from api
        $(document).on('click', '.update-source-status', function (e) {
            e.preventDefault();
            $(this).attr('disabled', 'disabled');//disable button
            var app_ref = $(this).data('app-reference');
            $.get(app_base_url + 'index.php/flight/get_booking_details/' + app_ref, function (response) {

                console.log(response);
            });
        });
        $('.issue_hold_ticket').on('click', function (e) {
            e.preventDefault();
            var confirm_ticket = confirm('Do you want to confirm the ticket ?');
            if (confirm_ticket == true) {
                var _user_status = this.value;
                var _opp_url = app_base_url + 'index.php/flight/run_ticketing_method/';
                _opp_url = _opp_url + $(this).data('app-reference') + '/' + $(this).data('booking-source');
                toastr.info('Please Wait!!!');
                $.get(_opp_url, function (res) {
                    var obj = JSON.parse(res);
                    toastr.info(obj.Message);
                });
            }
        });
        /*$('.update_flight_booking_details').on('click', function(e) {
         e.preventDefault();
         var _user_status = this.value;
         var _opp_url = app_base_url+'index.php/report/update_flight_booking_details/';
         _opp_url = _opp_url+$(this).data('app-reference')+'/'+$(this).data('booking-source');
         toastr.info('Please Wait!!!');
         $.get(_opp_url, function() {
         toastr.info('Updated Successfully!!!');
         });
         });*/
        $('.update_flight_booking_details').on('click', function (e) {
            e.preventDefault();
            var _user_status = this.value;
            var _opp_url = app_base_url + 'index.php/report/update_pnr_details/';
            _opp_url = _opp_url + $(this).data('app-reference') + '/' + $(this).data('booking-source') + '/' + $(this).data('booking-status');
            toastr.info('Please Wait!!!');
            $.get(_opp_url, function () {
                toastr.info('Updated Successfully!!!');
                //  location.reload();
            });
        });

        //send the email voucher
        $('.send_email_voucher').on('click', function (e) {
            $("#mail_voucher_modal").modal('show');
            $('#mail_voucher_error_message').empty();
            email = $(this).data('recipient_email');
            $("#voucher_recipient_email").val(email);
            app_reference = $(this).data('app-reference');
            book_reference = $(this).data('booking-source');
            app_status = $(this).data('app-status');
            $("#send_mail_btn").off('click').on('click', function (e) {
                email = $("#voucher_recipient_email").val();
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if (email != '') {
                    if (!emailReg.test(email)) {
                        $('#mail_voucher_error_message').empty().text('Please Enter Correct Email Id');
                        return false;
                    }

                    var _opp_url = app_base_url + 'index.php/voucher/flight/';
                    _opp_url = _opp_url + app_reference + '/' + book_reference + '/' + app_status + '/email_voucher/' + email;
                    toastr.info('Please Wait!!!');
                    $.get(_opp_url, function () {

                        toastr.info('Email sent  Successfully!!!');
                        $("#mail_voucher_modal").modal('hide');
                    });
                } else {
                    $('#mail_voucher_error_message').empty().text('Please Enter Email ID');
                }
            });

        });

        $(document).on('click', '.error_log', function (e) {
            e.preventDefault();
            var app_reference = $(this).data('app-reference');
            var booking_source = $(this).data('booking_source');
            var status = $(this).data('status');
            $.get(app_base_url + 'index.php/flight/exception_log_details?app_reference=' + app_reference + '&booking_source=' + booking_source + '&status=' + status, function (response) {
                $('#exception_app_reference').empty().text(app_reference);
                $('#exception_log_container').empty().html(response);
                $('#exception_log_modal').modal();

            });
        });

    });
</script>
<?php
function multi_voucher_links($app_reference, $booking_source, $status)
{
	return '<a class="btn btn-sm btn-primary" title="Multi Voucher" href="'.base_url().'index.php/voucher/multivoucher/'.$app_reference.'/'.$booking_source.'/'.$status.'"><i class="far fa-list"></i></a>';
}
function get_accomodation_cancellation($courseType, $refId) {
    return '<a href="' . base_url() . 'index.php/booking/accomodation_cancellation?courseType=' . $courseType . '&refId=' . $refId . '" class="btn btn-sm btn-danger "><i class="far fa-exclamation-triangle"></i> Cancel</a>';
}

function update_booking_details($app_reference, $booking_source, $booking_status) {

    return '<a class="btn btn-sm btn-danger update_flight_booking_details" data-app-reference="' . $app_reference . '" data-booking-source="' . $booking_source . '"data-booking-status="' . $booking_status . '" title="Update PNR Details"><i class="far fa-sync"></i></a>';
}

function edit_ticket_button($app_reference, $booking_source, $booking_status) {

    return '<a class="btn btn-sm btn-danger edit_ticket_details" href="' . base_url().'index.php/voucher/voucher_edit/'.$app_reference.'/'.$booking_source.'/'.$booking_status.'/show_voucher/b2b_flight_voucher" title="Edit Voucher"><i class="far fa-pencil"></i></a>';
}

function flight_voucher_email($app_reference, $booking_source, $status, $recipient_email) {

    return '<a class="btn btn-sm btn-primary send_email_voucher" data-app-status="' . $status . '"   data-app-reference="' . $app_reference . '" data-booking-source="' . $booking_source . '"data-recipient_email="'.$recipient_email.'" title="Email Voucher">
    <i class="far fa-envelope"></i></a>';
}

function get_cancellation_details_button($app_reference, $booking_source, $master_booking_status, $booking_customer_details) {
    //echo '<pre>'; 	print_r ($master_booking_status); die;
    $status = 'BOOKING_CONFIRMED';
	if($master_booking_status == 'BOOKING_CANCELLED'){
		$status = 'BOOKING_CANCELLED';
	} else{
		foreach($booking_customer_details as $tk => $tv){
			foreach($tv['booking_customer_details'] as $pk => $pv){
				if($pv['status'] == 'BOOKING_CANCELLED'){
					$status = 'BOOKING_CANCELLED';
					break;
				}
			}
		}
	}
	if($status == 'BOOKING_CANCELLED'){
		return '<a target="_blank" href="'.base_url().'index.php/flight/ticket_cancellation_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$master_booking_status.'" class="col-md-12 btn btn-sm btn-info "><i class="far fa-info"></i> Cancellation Details</a>';
	}
}

function customer_details($app_reference, $booking_source = '', $status = '', $is_domestic='') {
    return '<a  target="_blank" data-app-reference="' . $app_reference . '" data-booking-status="' . $status . '" data-isdomestic = "'.$is_domestic.'" data-booking-source="' . $booking_source . '" class="btn btn-sm btn-primary flight_u customer_details" title="Pax Profile">
    <i class="fa fa-users"></i> <small></small></a>';
}

function error_details($app_reference, $booking_source = '', $status = '') {
    return '<a data-app-reference="' . $app_reference . '" data-booking_source="' . $booking_source . '" data-status="' . $master_booking_status . '" class="error_log btn btn-sm btn-danger " title="ErroLog">
    <i class="fa fa-exclamation"></i> <small></small></a>';
}

///$parent_v['app_reference'], $parent_v['booking_source'], $parent_v['status'], $parent_v['booking_transaction_details']
function get_offline_cancellation_button($app_reference,$booking_source,$status,$booking_transaction_details){
    return '<a data-app-reference="' . $app_reference . '" data-booking_source="' . $booking_source . '" data-status="' . $status . '" class="error_log btn btn-sm btn-warning " title="Offline Cancellation">
    <i class="fa fa-exclamation"></i> <small></small></a>';
}

?>
<script type="text/javascript">// Show the customer Details
    $(document).ready(function(){
        $(".select2").select2();
    });
    $(document).on('click', '.customer_details', function (e) {

        e.preventDefault();
        //$(this).attr('disabled', 'disabled');//disable button
        var app_ref = $(this).data('app-reference');
        var booking_src = $(this).data('booking-source');
        var status = $(this).data('booking-status');
        var is_domestic = $(this).data('isdomestic');
        var module = 'flight';
        jQuery.ajax({
            type: "GET",
            url: app_base_url + 'index.php/report/get_customer_details/' + app_ref + '/' + booking_src + '/' + status + '/' + module + '/'+is_domestic,
            dataType: 'json',
            success: function (res) {

                $('#customer_parameters').html(res.data);
                $('#pax_modal').modal('show');
            }
        });
    });
</script>
