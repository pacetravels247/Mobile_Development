<!-- Mail - Voucher  starts-->
<?php 


$payment_history=$booking_payment_history_details;
//debug($payment_history);exit;
//array_shift($adult_array);
//array_shift($child_wb_array);
//array_shift($child_wob_array);
//array_shift($infant_array);
//debug();
$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->tours_model->tours_city_name();

?>
<div class="modal fade id-pay-modal" id="payment_details_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			<?=$booking_package_details['package_name']?>(<?=$booking_package_details['tour_code']?>) - Payment Details
		</h4>
      </div>
	  <?php //debug($attributes);  ?>
      <div class="modal-body">
      	<div class="container-fluid">
      		<div class="col-sm-12 nopad">
      			<div class="row">
					<div class="col-sm-6 nopad">
						<label for="total_trip_cost" class="">Total Package Amount &nbsp;&nbsp;
							<input type="text" name="total_trip_cost"  class="pack-amt" value="<?=$payment_history[0]['total_trip_cost']?>" disabled>
						</label>
					</div>
				</div>
				<div class="row">
					<div class="sec_box">
						<div class="col-sm-12 hdr">
							<div class="col-sm-2"><p>Sl .No</p></div>
							<div class="col-sm-2"><p>Description</p></div>
							<div class="col-sm-2"><p>Date</p></div>
							<div class="col-sm-2"><p>Amount</p></div>
							<div class="col-sm-2"><p>Pending</p></div>
							<div class="col-sm-2"><p>Status</p></div>
						</div>
						<?php
							$last_pending_amount=0;
							foreach($payment_history as $pay_key => $pay_val){
								if($pay_val['status']=='SUCCESS'){
									$status_label="label-success";
								}else{
									$status_label="label-primary";
								}
						?>
				
						<div class="col-sm-12 nopad tbl">
							<div class="col-sm-2"><?=$pay_key+1?></div>
							<div class="col-sm-2"><?=$pay_val['description']?></div>
							<div class="col-sm-2"><?=date('d M Y',strtotime($pay_val['payment_date']))?></div>
							<div class="col-sm-2"><?=number_format($pay_val['paid_amount'],2)?></div>
							<div class="col-sm-2"><?=number_format($pay_val['remaining'],2)?></div>
							<div class="col-sm-2"><span class="label <?=$status_label?>"><?=$pay_val['status']?></span></div>
						</div>
				
						<?php 
							$last_pending_amount=$pay_val['remaining'];
							} 
						?>
					</div>
				</div>
				<div class="row">
					<?php 
						if($last_pending_amount>0){
					?>
						<form action="<?php echo base_url();?>index.php/tours/payment_slab" method="post" id="holiday_search_<?=$app_reference?>"> 
							<input type="hidden" name="ref_no" value="<?=$app_reference?>">
							<input type="hidden" name="last_pending_amount" class="last_pending_amount" value="<?=$last_pending_amount?>">
							<input type="hidden" name="package_cost" value="<?=$payment_history[0]['total_trip_cost']?>">
							<input type="hidden" name="pending_amount" class="pending_amount" value="0">
							<input type="hidden" name="module" class="module" value="<?=$created_by?>">
							<div class="col-sm-8" style="padding: 0 10px 0 0;">
								<label for="pay_description">Description</label>
								<input type="text" placeholder="Description" class="form-control" name="pay_description" value="" required>
							</div>
							<div class="col-sm-3">
								<label for="amount">Amount</label>
								<input type="text" class="form-control amount_<?=$app_reference?>" name="amount" value="<?=$last_pending_amount?>" required>
							</div>
							<div class="row">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-danger id-enquiry-btn">send payment link</button>
					</div>
				</div>
						</form>
					<?php 
						}else{
							echo '<h2>Full Amount Paid!!!</h2>';
						} 
					?>
				</div>
      		</div>
      	</div>
		
		
      </div>
		
      <!-- <div class="modal-footer"></div> -->
     
    </div>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('keyup','.amount_<?php echo $app_reference;?>',function(){
			var value=parseFloat($(this).val());
			var remaining_amt = parseFloat($('#payment_details_<?php echo $app_reference; ?>').find('.last_pending_amount').val());
			if(value>remaining_amt){
				alert("The amount you have entered is exeeding the remaining amount .");
				$(this).val('')
				$('#payment_details_<?php echo $app_reference; ?>').find('.pending_amount').val(remaining_amt)
			}else{
				var pending_amount=remaining_amt - value;
				$('#payment_details_<?php echo $app_reference; ?>').find('.pending_amount').val(pending_amount)
			}
		});
	});
</script>
<!-- Mail - Voucher  ends-->
<style type="text/css">
.col-sm-12.hdr {
    background: #eee;
    padding: 5px;
    border: none;
    margin: 0;
}
.col-sm-12.mem_details {
    /*background: #eee;
    padding: 15px;
    margin: 10px 0px;
    border-radius: 4px;
    border: 1px solid #ccc;*/
}
.col-sm-12.hdr p {
    font-size: 14px;
    text-transform: capitalize;
    font-weight: 600;
}
.tbl .col-sm-4, .tbl .col-sm-3, .tbl .col-sm-2 {
    padding: 10px 0px;
    border: 1px solid #ccc;
    height: 40px;
    text-align: center;
    font-size: 13px;
}
.id-pay-modal .sec_box{
	height: auto!important;
}

.opt_tr_hd
{
  font-size: 18px;
}
.modal-footer {
    padding: 15px;
    border-top: 1px solid #e5e5e5;
    text-align: center;
}
.opt_tr_hd {
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
}
.modal-body {
    position: relative;
    padding: 20px;
}
.id-enquiry-btn {
    margin-top: 15px;
    float: right;
    border-radius: 4px!important;
    height: 45px;
    width: 30%;
}
.pack-amt{
	padding-left: 10px;
}
</style>