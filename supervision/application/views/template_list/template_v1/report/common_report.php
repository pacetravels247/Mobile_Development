
<?php
if (is_array($search_params)) {
    extract($search_params);
    //echo '<pre>'; print_r($search_params);die;
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
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
    <div class="row">
    <div class="col-sm-6"><h4><strong>Agent Report Details</strong></h4></div>
    <div class="col-sm-6"></div>
</div>
    <div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
        
        <div class="panel-body">
            <div>

                <form method="GET" autocomplete="off" action="<?= base_url() . 'report/b2b_common_report?' ?>">
                    <div class="well well-sm">
                    <div class="row clearfix form-group">
                        <!-- <div class="col-xs-4">
                            <label>
                                Agent
                            </label>
                            <select class="form-control select2" name="created_by_id">
                                <option value="">All</option>
                                <?php
                                    foreach ($agency_list as $key => $value) {
                                ?>
                                <option value="<?=$value['user_id'];?>" <?php if(isset($_GET['created_by_id']) && $value['user_id'] == @$created_by_id) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
                            <?php } ?>
                            </select>
                        </div> -->
                        <div class="col-xs-3">
                            <label>
                                Booked From Date
                            </label>
                            <input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?= @$created_datetime_from ?>" placeholder="Request Date">
                        </div>
                        <div class="col-xs-3">
                            <label>
                                Booked To Date
                            </label>
                            <input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?= @$created_datetime_to ?>" placeholder="Request Date">
                        </div>
                        <div class="col-xs-3">
                            <label>
                                Operator PNR
                            </label>
                            <input type="text" class="form-control" name="pnr" value="<?= @$pnr ?>" placeholder="PNR">
                        </div>
                        <div class="col-xs-3">
                            <label>
                                Pace PNR
                            </label>
                            <input type="text" class="form-control" name="app_reference" value="<?= @$app_reference ?>" placeholder="Application Reference">
                        </div>
                        
                        
                        <div class="col-xs-4" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-primary">Search</button> 
                            <button type="reset" id="btn-reset" class="btn btn-warning">Reset</button>
                            <a href="<?= base_url() . 'report/b2b_common_report?today_booking_data='.date('Y-m-d') ?>" id="clear-filter" class="btn btn-primary">Clear Filter</a>
                        </div>
                    </div>
                </div>
                </form>
            </div>
         <div class="col-md-12 table-responsive rigid_actions" >
   <table class="table table-condensed table-bordered rigid_actions datatables-td">
  <thead>
    <tr>
                        <th>Sno</th>
                        <th>PNR</th>
                        <th>Operator PNR</th>
                        <th>Product</th>
                        <th>Agenct ID</th>
                        <th>BookingDate</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th>Commission</th>
                        <th>TDS</th>
                        <th>Status</th>
                        <th>Action</th>
   </tr>
    </thead>
    <tbody>
     <?php
                    if (valid_array($crd) == true) {
                        foreach($crd AS $key=>$val){ 
                            extract($val);
                            if($Flight == 'Flight'){
                                if($status == 'BOOKING_CONFIRMED' || $status == 'BOOKING_CANCELLED'){
                                    $pdf_btn = flight_pdf($app_reference, $booking_source, $status);
                                }else{
                                    $pdf_btn = '';
                                }
                                
                            }else{
                                if($status == 'BOOKING_CONFIRMED' || $status == 'BOOKING_CANCELLED'){
                                    $pdf_btn = bus_pdf($app_reference, $booking_source, $status);
                                }else{
                                    $pdf_btn = '';
                                }
                                
                            }
                            $action = $pdf_btn;
                            ?>
                            <tr>
                                <td><?= (++$key) ?></td>
                                <td><?php echo $app_reference; ?></td>
                                <td><?php echo $sup_pnr; ?></td>
                                <td><?php echo $Flight; ?></td>
                                <td><?php echo $agency_name." - ".provab_decrypt($uuid); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($created_datetime)) ?></td>
                                <td><?php echo $booking_billing_type; ?></td>
                                <td><?php echo $total_fare; ?></td>
                                <td><?php echo number_format($agent_commission,2); ?></td>
                                <td><?php echo number_format($agent_tds,2); ?></td>
                                <td><?php echo $status; ?></td>
                                <td><?php echo $action; ?></td>
                            </tr>
                            <?php
                        } }?>
    </tbody>
</table>

    </div>
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
    //echo '<pre>';     print_r ($master_booking_status); die;
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

function customer_details($app_reference, $booking_source = '', $status = '') {
    return '<a  target="_blank" data-app-reference="' . $app_reference . '" data-booking-status="' . $status . '" data-booking-source="' . $booking_source . '" class="btn btn-sm btn-primary flight_u customer_details" title="Pax Profile">
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
        $('.datatables-td').DataTable();
    });
    $(document).on('click', '.customer_details', function (e) {

        e.preventDefault();
        //$(this).attr('disabled', 'disabled');//disable button
        var app_ref = $(this).data('app-reference');
        var booking_src = $(this).data('booking-source');
        var status = $(this).data('booking-status');
        var module = 'flight';
        jQuery.ajax({
            type: "GET",
            url: app_base_url + 'index.php/report/get_customer_details/' + app_ref + '/' + booking_src + '/' + status + '/' + module + '/',
            dataType: 'json',
            success: function (res) {

                $('#customer_parameters').html(res.data);
                $('#pax_modal').modal('show');
            }
        });
    });
</script>
<style type="text/css">
    .fixed .content-wrapper, .fixed .right-side {
    padding-top: 0px;
}
.dataTables_wrapper .col-sm-12{
        min-height: .01%!important;
        overflow-x: auto!important;
        font-size: 11px!important;
    }
</style>
