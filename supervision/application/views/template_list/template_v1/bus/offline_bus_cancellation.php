<?php
    $booking_details = $data['booking_details'][0];
    extract($booking_details);
    $default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
    $sup_comm = 0; $sup_tds = 0;
    $agt_comm = 0; $agt_tds = 0;
?>
<div class="search-result">
    <div class="bakrd_color">
         <div class="clearfix"></div>
        <div class="cancellation_page">
            <div class="head_can">
                <h3 class="canc_hed">Cancellation</h3>
                <div class="ref_number">
                    <div class="rows_cancel">Booking ID: <strong><?=$app_reference?></strong></div>
                    <div class="rows_cancel">Booking Date: <?=$booked_date?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="cancel_bkd">
            <?php foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {
                extract($itinerary_v); 
            ?>
                <div class="col-xs-3 nopad">
                    <div class="pad_evry">
                        <div class="imge_can"><span class="fa fa-bus"></span></div>
                        <div class="can_flt_name"><?=$operator?><strong><?=$bus_type?> </strong></div>
                    </div>
                </div>
                <div class="col-xs-4 nopad">
                    <div class="pad_evry">
                        <span class="place_big_text"><?=ucfirst($departure_from)?></span>
                        <span class="date_mension"><?=app_friendly_datetime($departure_datetime)?></span>
                    </div>
                </div>
                <div class="col-xs-1 nopad">
                    <div class="pad_evry">
                        <div class="aroow_can fa fa-long-arrow-right"></div>
                    </div>
                </div>
                <div class="col-xs-4 nopad">
                    <div class="pad_evry">
                        <span class="place_big_text"><?=ucfirst($arrival_to)?></span>
                        <span class="date_mension"><?=app_friendly_datetime($arrival_datetime)?></span>
                    </div>
                </div>
                <?php } ?>
            
            
            <div class="clearfix"></div>
            

            <div class="row_can_table hed_table">
                <?php
                    if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE){
                ?>
                <div class="col-xs-1 nopad">
                    <div class="can_pads">Slno</div>
                </div>
                <?php } ?>
                <div class="col-xs-2 nopad">
                    <div class="can_pads">Passenger Name</div>
                </div>
                <div class="col-xs-2 nopad">
                    <div class="can_pads">Age</div>
                </div>
                <div class="col-xs-3 nopad">
                    <div class="can_pads">PNR</div>
                </div>
                <div class="col-xs-2 nopad">
                    <div class="can_pads">SeatNumber</div>
                </div>
                <div class="col-xs-2 nopad">
                    <div class="can_pads">Status</div>
                </div>
            </div>
            <?php 
            $sup_rev_amt_tt = 0; $agt_rev_amt_tt = 0;
            foreach($booking_customer_details as $customer_k => $customer_v) {
                extract($customer_v);
                $attr_arr = json_decode($attr, true);
                $sup_comm = $attr_arr["_AdminCommission"]; $agt_comm = $attr_arr["_Commission"];
                $sup_tds = 5*($sup_comm/100); $agt_tds = $attr_arr["_tdsCommission"];
                $sup_rev_amt = $sup_comm-$sup_tds; $agt_rev_amt = $agt_comm-$agt_tds;
                $pax_name = $name;
                if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE){
                    if($customer_v['status'] == 'BOOKING_CONFIRMED'){
                        if($booking_details['booking_source'] == VRL_BUS_BOOKING_SOURCE){
                            $pnr_n = explode(',', $booking_details['pnr']);
                            $pax_check_box = '<input type="checkbox" name="passenger_origin[]" class="passenger_fk" value="'.$customer_v['seat_no'].'-'.$pnr_n[$customer_k].'" data-suprev="'.$sup_rev_amt.'"
                            data-agtrev="'.$agt_rev_amt.'">';
                        }else{
                            $pax_check_box = '<input type="checkbox" name="passenger_origin[]" class="passenger_fk" value="'.$customer_v['seat_no'].'" data-suprev="'.$sup_rev_amt.'"
                            data-agtrev="'.$agt_rev_amt.'">';
                        }
                    } else {
                        $pax_check_box = '';
                    }
                }
                else{
                    $sup_rev_amt_tt += $sup_rev_amt; $agt_rev_amt_tt += $agt_rev_amt;
                }
            ?>
                <div class="row_can_table">
                    <?php
                        if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE){
                    ?>
                    <div class="col-xs-1 nopad">
                        <div class="can_pads can_check"><?=($customer_k+1)?>. <?=$pax_check_box?></div>
                    </div>
                    <?php } ?>
                    <div class="col-xs-2 nopad">
                        <div class="can_pads"><?=$pax_name?></div>
                    </div>
                    <div class="col-xs-2 nopad">
                        <div class="can_pads"><?=$age?></div>
                    </div>
                    <div class="col-xs-3 nopad">
                        <div class="can_pads"><?=$pnr?></div>
                    </div>
                    <div class="col-xs-2 nopad">
                        <div class="can_pads"><?=$seat_no?></div>
                    </div>
                    <div class="col-xs-2 nopad">
                        <div class="can_pads"><?=$status?></div>
                    </div>
                 </div>
               <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="row">
            <div class="col-xs-12 nopad">
                <div class="btn_continue">
                    <div class="amnt_disply">
                        Total Amount Paid:
                        <div class="amnt_paid"><?php echo $booking_details['currency'];?> <?=$grand_total?></div>
                    </div>
                </div>
             </div>
             <!-- <div class="col-xs-6 nopad">   
                <div class="btn_continue">
                    <button data-toggle="modal" data-target="#confirm_cancel" class="btn btn-warning bookallbtn" type="button">Confirm</button>
                </div>
             </div> -->
            </div>
<?php 
    $sup_fare = $data['booking_details'][0]['total_api_base_fare'] + $data['booking_details'][0]['total_api_tax'];
    $agent_fare = $data['booking_details'][0]['grand_total'] - $data['booking_details'][0]['agent_markup'];
    $admin_commission = $data['booking_details'][0]['admin_commission'];
    $agent_commission = $data['booking_details'][0]['agent_commission'];
    $admin_tds        = $data['booking_details'][0]['admin_tds'];
    $agent_tds        = $data['booking_details'][0]['agent_tds'];
    if($data['booking_details'][0]['status'] == 'BOOKING_CONFIRMED'){ ?>
        <div class="row well well-sm">
                <div class="col-sm-3">
                    <strong>Supplier Total Fare</strong><br/>
                    <input type="text" id="Sup_total_fare" name="Sup_total_fare" value="<?php echo $sup_fare; ?>" readonly="">
                </div>
                <div class="col-sm-3">
                    <strong>Agent Total Fare</strong><br/>
                    <input type="text" id="agent_total_fare" name="agent_total_fare" value="<?php echo $agent_fare; ?>" readonly="">
                </div>
                <div class="col-sm-3">
                    <strong>Cancellation Charges</strong><br/>
                    <input type="number" id="cancellation_per" name="cancellation_per" value="0" required>
                </div>
                <div class="col-sm-3" id="calculate_refund" style="margin-top: 20px;"><button class="btn-danger">Calculate refund</button></div>
            </div>
<?php } ?>
            
            <div class="col-xs-12 well well-sm" id="refund-div" style="display:none;">
             <form method="POST" action="<?=base_url().'index.php/bus/offline_cancel_booking'?>">
                <input type="Hidden" name="booking_source" value="<?=$booking_source?>">
                <input type="Hidden" name="app_reference" value="<?=$app_reference?>">
                <input type="Hidden" name="agent_id" value="<?=@$booking_details['created_by_id']?>">
                <input type="Hidden" id="selected_seat" name="selected_seat" value="">
                <input type="Hidden" id="cancel_type" name="cancel_type" value="" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">API Cancellation Charge:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="api_cancellation_charge" name="api_cancellation_charge" placeholder="Api Cancellation Charge" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">Cancellation Charge:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="cancellation_charge" name="cancellation_charge" placeholder="Cancellation Charge" required readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">API Comm. Reversed:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="api_comm_rev" name="api_comm_rev" placeholder="Api Commission Reversed" value="<?=$admin_commission?>" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">Comm. Reversed:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="comm_rev" name="comm_rev" placeholder="Commission Reversed" value="<?=$agent_commission?>" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">API TDS:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="api_TDS" name="api_TDS" placeholder="Api TDS" value="<?=$admin_tds?>" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">TDS:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="agent_tds" name="agent_tds" placeholder="Agent Tds" value="<?=$agent_tds?>" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">API Refund Amount:</label>
                    <div class="col-sm-4">
                      <input type="number" step=".01" class="form-control" id="api_r_amount" name="api_r_amount" placeholder="Api Refund Amount" required readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">Refund Amount:</label>
                    <div class="col-sm-4"> 
                        <input type="number" step=".01" class="form-control" id="refund_amount" name="refund_amount" placeholder="Refund amount" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pwd">Refund Status:</label>
                    <div class="col-sm-4"> 
                        <select class="form-control" name="refund_status" required=""><option value="">Please select</option><option value="INPROGRESS">INPROGRESS</option><option value="PROCESSED" selected="selected">PROCESSED</option><option value="REJECTED">REJECTED</option></select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                    <br><button type="submit" class="btn btn-success bookallbtn">Submit</button>
                    </div>
                </div>
             </form>
             </div>
            
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#calculate_refund').click(function(e){
        $('#refund-div').removeAttr("style");
        refundAmt();
    });
    function refundAmt(){
        var api_refund = 0;
        var agent_Refund = 0;
        var supCancelCharge = 0;
        var agentCanCharge = 0;

        var sup_fare = $('#Sup_total_fare').val();
        var cancel_per = $('#cancellation_per').val();
        var sup_commision = $('#api_comm_rev').val();
        var sup_tds = $('#api_TDS').val();
        var agent_commision = $('#comm_rev').val();
        var agent_tds = $('#agent_tds').val();
        
        supCancelCharge   = parseFloat(sup_fare) * (cancel_per/100);
        agentCanCharge = parseFloat(sup_fare) * (cancel_per/100);
        api_refund = (parseFloat(sup_fare) - parseFloat(supCancelCharge)) - parseFloat(sup_commision) + parseFloat(sup_tds);
        agent_Refund  = (parseFloat(sup_fare) - parseFloat(agentCanCharge)) - parseFloat(agent_commision) + parseFloat(agent_tds);
        $('#api_cancellation_charge').val(supCancelCharge.toFixed(2));
        $('#cancellation_charge').val(agentCanCharge.toFixed(2));
        $('#api_r_amount').val(api_refund);
        $('#refund_amount').val(agent_Refund);
    }
    });
</script>
<script type="text/javascript">
    <?php 
        if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE){
    ?>
    $('input[name="passenger_origin[]"]').click(function() {
        var suprev = 0;
        var agtrev = 0;
            $('input[name="passenger_origin[]"]:checked').each(function() {
                suprev += parseFloat($(this).data("suprev"));
                agtrev += parseFloat($(this).data("agtrev"));
            });
            $("#api_comm_rev").val(suprev);
            $("#comm_rev").val(agtrev);
     });
    $('.bookallbtn').click(function(){
        var favorite = [];
        $('input[name="passenger_origin[]"]:checked').each(function() {
            favorite.push($(this).val());
        });
        $('#selected_seat').val(favorite);

        if ($('input[name="passenger_origin[]"]').not(':checked').length == 0) {
            $('#cancel_type').val('full');
        }else{
            $('#cancel_type').val('partial');
        }

        if(favorite.length == ''){
            alert('Please select any Passenger ..!');
            return false;
        }
    })
    <?php } ?>
</script>