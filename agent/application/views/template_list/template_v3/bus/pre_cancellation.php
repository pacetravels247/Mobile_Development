<?php

	$booking_details = $data['booking_details'][0];
    //debug($booking_details); exit;
    $ticket_no = $booking_details['ticket'];
    $total_pax = count($booking_details['booking_customer_details']);
    $conv_amnt = $booking_details['convinence_amount'];
	extract($booking_details);
    //debug();die('===888');
	$default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
    //debug($data["pre_cancel_data"]["cancel_percent"]);die();
    if($data["pre_cancel_data"]["is_cancellable"])
        $cancellation_note = $data["pre_cancel_data"]["cancel_percent"]."% (INR ".$data["pre_cancel_data"]["cancel_amount"]."/-) charges applicable for cancellation.";
    else
        $cancellation_note = "Cancellation not allowed."
?>
<div class="search-result">
	<div class="container">
	<div class="bakrd_color">
        <div class="cetrel_all">
            <?php echo $GLOBALS['CI']->template->isolated_view('share/navigation', $default_view) ?>
         </div>
         <div class="clearfix"></div>
    	<div class="cancellation_page">
        	<div class="head_can">
            	<h3>Cancellation</h3>
                <br><!-- <span class="subtitle"> <?php echo $cancellation_note; ?></span> --><br>
                 <?php
                    if($booking_details['booking_source'] == ETS_BUS_BOOKING_SOURCE || $booking_details['booking_source'] == INFINITY_BUS_BOOKING_SOURCE){
						$total_fare = $booking_details['agent_buying_price']+$booking_details['agent_commission']-$booking_details['agent_tds'];
						$refund_amount = $booking_details['agent_buying_price'] - $booking_details['total_api_tax'] - $data["pre_cancel_data"]["cancel_amount"]-$booking_details['gst'];
                ?>
				<table>
					<tr>
					<td colspan="2">Grand Total - INR. <?php echo $booking_details['grand_total']; ?>/- | Inclusive of your markup INR. <?php echo $booking_details['agent_markup']; ?>, GST & Commission (Values are below)</td>
					</tr>
				</table>
                <table class="table" style="width: 30%">
                    <tr>
                        <td>Total Fare</td>
                        <td><?php echo $total_fare; ?></td>
                    </tr>
                    <tr>
                        <td>Total Service Tax (-)</td>
                        <td><?php echo $booking_details['total_api_tax']?></td>
                    </tr>
                    <tr>
                        <td>Cancellation Percentage (%)</td>
                        <td><?php echo $data["pre_cancel_data"]["cancel_percent"]?></td>
                    </tr>
                    <tr>
                        <td>Cancellation Amount (-)</td>
                        <td><?php echo $data["pre_cancel_data"]["cancel_amount"]?></td>
                    </tr>
                    <tr>
                        <td>Commission Reversed (-)</td>
                        <td><?php echo ($booking_details['agent_commission'] - $booking_details['agent_tds'])?></td>
                    </tr>
					<tr>
                        <td>TDS (+)</td>
                        <td><?php echo $booking_details['agent_tds']; ?></td>
                    </tr>
					<tr>
                        <td>GST (-)</td>
                        <td><?php echo $booking_details['gst']; ?></td>
                    </tr>
                    <tr>
                        <td>Refund Amount</td>
                        <td><?php echo $refund_amount; ?></td>
                    </tr>
                </table>
                <?php } ?>
                <div class="ref_number">
                	<div class="rows_cancel">Booking ID: <strong><?=$app_reference?></strong></div>
                    <div class="rows_cancel">Booking Date: <?=$booked_date?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <form method="post">
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
                    if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE || $booking_details['booking_source'] != INFINITY_BUS_BOOKING_SOURCE){
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
		    <?php foreach($booking_customer_details as $customer_k => $customer_v) {
                
            	extract($customer_v);
            	$pax_name = $name;
                if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE || $booking_details['booking_source'] != INFINITY_BUS_BOOKING_SOURCE){
                    if($customer_v['status'] == 'BOOKING_CONFIRMED'){
                        if($booking_details['booking_source'] == VRL_BUS_BOOKING_SOURCE){
                            $pnr_n = explode(',', $booking_details['pnr']);
							$pnr = $pnr_n[$customer_k];
                            $pax_check_box = '<input type="checkbox" name="passenger_origin[]" class="passenger_fk" value="'.$customer_v['seat_no'].'-'.$pnr_n[$customer_k].'">';
                        }else{
                            $pax_check_box = '<input type="checkbox" name="passenger_origin[]" class="passenger_fk" value="'.$customer_v['seat_no'].'">';
                        }
                    } else {
                        $pax_check_box = '';
                    }
                }
				$pax_check_box .= '<input type="hidden" class="po_fk" value="'.$origin.'">';	
            ?>        
            	<div class="row_can_table">
                    <?php
                        if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE || $booking_details['booking_source'] != INFINITY_BUS_BOOKING_SOURCE){
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
            </form>
            <div class="clearfix"></div>
            <div class="ritside_can col-xs-4 nopad">
            
            <div class="col-xs-6 nopad">
            	<div class="btn_continue">
                	<div class="amnt_disply">
                    	Total Amount Paid:
                    	<div class="amnt_paid"><?php echo $booking_details['currency'];?> <?=$grand_total?></div>
                    </div>
                </div>
             </div>
             <div class="col-xs-6 nopad">   
                <div class="btn_continue">
                	<button data-toggle="modal" data-target="#confirm_cancel" class="b-btn bookallbtn" type="button">Confirm</button>
                </div>
             </div>
            </div>
            
            
        </div>
    </div>
    </div>
</div>

<!-- Confirm Cancealltion Starts-->
<div class="modal fade" tabindex="-1" role="dialog" id="confirm_cancel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Do you want to cancel the Booking?</h4>
      </div>
      <!--<div class="modal-body"></div>-->
      <div class="modal-footer">
        <div id="table_123_1">
            <table id="table_123" class="table" style="width: 70%">
                   
            </table>
        </div>
        <form method="post" action="<?=base_url().'index.php/bus/cancel_booking'?>">
            <input type="hidden" name="app_reference" value="<?=$app_reference?>">
			<input type="hidden" name="po_list" id="po_list" value="0">
            <input type="hidden" id="b_s" name="booking_source" value="<?=$booking_source?>">
            <input type="hidden" id="selected_seat" name="selected_seat" value="">
            <input type="hidden" id="ticket_no" name="ticket_no" value="<?php echo $ticket_no; ?>">
            <input type="hidden" name="total_pax" id="total_pax" value="<?php echo $total_pax;?>">
            <input type="hidden" name="total_b_p" id="total_b_p" value="<?php echo ($booking_details['total_api_base_fare'] + $booking_details['admin_commission'])?>">
            <input type="hidden" name="total_b2b_com" id="total_b2b_com" value="<?php echo ($booking_details['agent_commission'] - $booking_details['agent_tds'])?>">

            <input type="hidden" name="canc_pct" id="canc_pct" value="<?php echo $data["pre_cancel_data"]["cancel_percent"]?>">
            
        
            <input type="hidden" name="total_tax" id="total_tax" value="<?php echo $booking_details['total_api_tax']?>">
            <input type="hidden" name="total_g" id="total_g" value="<?php echo ($booking_details['grand_total']-$conv_amnt)?>">
        

            <input type="hidden" id="cancel_type" name="cancel_type" value="" />

            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="submit" class="btn btn-danger">Yes</button>
        </form>

        
       <!--  <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <a href="<?=base_url().'index.php/bus/cancel_booking/'.$app_reference.'/'.$booking_source?>" class="btn btn-danger">Yes</a> -->
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->

<script type="text/javascript">
    <?php 
        if($booking_details['booking_source'] != ETS_BUS_BOOKING_SOURCE || $booking_details['booking_source'] != INFINITY_BUS_BOOKING_SOURCE){
    ?>
    $('.bookallbtn').click(function(){
        var favorite = [];
		var po_list = [];
        var count = 0;  
        $('input[name="passenger_origin[]"]:checked').each(function() {
            favorite.push($(this).val());
			po_list.push($(this).siblings(".po_fk").val());
            count++;
        });
        $('#selected_seat').val(favorite);
		$('#po_list').val(po_list);
        //alert(count);
        if ($('input[name="passenger_origin[]"]').not(':checked').length == 0) {
            $('#cancel_type').val('full');
        }else{
            $('#cancel_type').val('partial');
        }
        //alert('22');
        cancel_value(count);
        if(favorite.length == ''){
            alert('Please select any Passenger ..!');
            return false;
        }
    })
    <?php } ?>

    function cancel_value(count){

        var seats = $('#selected_seat').val();
		var po_list = $('#po_list').val();
        var tckt_no = $('#ticket_no').val();
        var b_s = $('#b_s').val();
        var total_pax = $('#total_pax').val();

        var base_price = ($('#total_b_p').val()/total_pax)*count;
        var tax = ($('#total_tax').val()/total_pax)*count;
        var g_total = ($('#total_g').val()/total_pax)*count;

        var agnt_com = ($('#total_b2b_com').val()/total_pax)*count
        var canc_pct = $('#canc_pct').val();
        
        
        var load ='<tr id="load"><td style="color:red" colspan="2">Loading,please wait some time ..!</td></tr>';
        $('#table_123_1 #table_123').html(load);
        //$('#table_123_1 #table_123').empty();
        $.ajax({
            'url' : app_base_url+"index.php/bus/get_partial_cancel_value/",
            'type' : "POST",
            'data' :{seats:seats,t_no:tckt_no,b_s:b_s, po_list: po_list},
            'dataType' : 'json',
            'success' :function(response){
                var varHtml = '';
                console.log(response); 
				var refund_amount = parseFloat(response.bill_det.agent_buying) - parseFloat(response.cancellation_charges) - parseFloat(response.bill_det.api_tax) - parseFloat(response.bill_det.gst);
			varHtml ='<tr><td colspan="2">Grand Total - INR. '+response.bill_det.grand_total+'/- | Inclusive of your Markup INR. '+response.bill_det.agent_markup+'/-, Commission & GST (Values as below)</td></tr>';
				varHtml +='<tr><td colspan="2">Cancellation Details</td></tr>';
                varHtml +='<tr><td>Total Fare</td><td>'+response.bill_det.total_fare+'</td></tr>';
                varHtml +='<tr><td>Total Service Tax (-)</td><td>'+response.bill_det.api_tax+'</td></tr>';
                varHtml +='<tr><td>Cancellation Percentage (%)</td><td>'+response.cancel_percent+'</td></tr>';
                varHtml +='<tr><td>Cancellation Amount (-)</td><td>'+response.cancellation_charges+'</td></tr>';
                varHtml +='<tr><td>Commission Reversed (-)</td><td>'+response.bill_det.commission_reversed+'</td></tr>';
				varHtml +='<tr><td>TDS (+)</td><td>'+response.bill_det.tds+'</td></tr>';
				varHtml +='<tr><td>GST (-)</td><td>'+response.bill_det.gst+'</td></tr>';
                varHtml +='<tr><td>Refund Amount</td><td>'+refund_amount.toFixed(2)+'</td></tr>';
                
                $('#table_123_1 #table_123').html(varHtml);
                $("#load").css('display','none');
            }
        });
       
    }
</script>