<!-- Mail - Voucher  starts-->
<?php 


$payment_history=$booking_payment_history_details;
//debug($booking_payment_details);
//array_shift($adult_array);
//array_shift($child_wb_array);
//array_shift($child_wob_array);
//array_shift($infant_array);
//debug();
$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->Package_Model->tours_city_name();

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
							$status_text="PAID";
						}else{
							$status_label="label-primary";
							$status_text="PAY NOW";
						}
				?>
				
				<div class="col-sm-12 nopad tbl">
					<div class="col-sm-2"><?=$pay_key+1?></div>
					<div class="col-sm-2"><?=$pay_val['description']?></div>
					<div class="col-sm-2"><?=date('d M Y',strtotime($pay_val['payment_date']))?></div>
					<div class="col-sm-2"><?=number_format($pay_val['paid_amount'],2)?></div>
					<div class="col-sm-2"><?=number_format($pay_val['remaining'],2)?></div>
					<?php 
						if($pay_val['status']=='SUCCESS'){
					?>
						<div class="col-sm-2"><span class="label <?=$status_label?>"><?=$status_text?></span></div>
					<?php 
						}else{
					?>
						<div class="modal fade payment_mode" id="payment_mode_<?=$pay_val['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
											 Payment Details
										</h4>
									</div>
									
									<div class="modal-body">
										<form action="<?php echo base_url();?>index.php/tours/pay_enquiry_amount" method="post"> 
											<input type="hidden" name="app_reference" class="app_reference" value="<?=$app_reference?>">
											<input type="hidden" name="temp_booking_id" class="temp_booking_id" value="<?=$temp_booking_details[0]['id']?>">
											<input type="hidden" name="pay_ref_id" class="pay_ref_id" value="<?=$pay_val['id']?>">
											<input type="hidden" name="amount" class="amount" value="<?=$pay_val['paid_amount']?>">
											<div class="row">
												<div class="contbk">
													<div class="contcthdngs">Select Payment Mode</div>
													<input type="radio" name="selected_pm" value="WALLET" checked/> Wallet
													<input type="radio" name="selected_pm" value="PAYTM_CC" /> Credit Card
													<input type="radio" name="selected_pm" value="PAYTM_DC" /> Debit Card
													<input type="radio" name="selected_pm" value="PAYTM_PPI" /> PAYTM Wallet
													<input type="radio" name="selected_pm" value="TECHP" /> Net Banking
												</div>
												
												<div class="col-sm-2">
													<button type="submit" class="btn btn-danger id-enquiry-btn">PAY NOW</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2"><a class="btn" data-toggle="modal" data-target="#payment_mode_<?=$pay_val['id']?>">PAY</a></div>
						<!--<div class="col-sm-2"><a href="<?php echo base_url().'index.php/payment_gateway/payment/'.$app_reference.'/'.$temp_booking_details[0]['id'].'/PAYTM/'.$pay_val['id']?>"><?=$status_text?></a></div>-->
					<?php
						}
					?>
					
				</div>
		
				<?php 
					$last_pending_amount=$pay_val['remaining'];
					} 
				?>
			</div>
		<?php 
			
			if($last_pending_amount>0){
		
			}else{
				echo '<h2>Full Amount Paid!!!</h2>';
			} 
		?>
    </div>
</div>

		
      <div class="modal-footer" style="border:none;"></div>
     
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
		$('#payment_details_<?php echo $app_reference; ?>').find("input[name='selected_pm']").change(function(){
			var amount = $(this).parents('.payment_mode').find(".amount").val();
			shoConFees(amount);
		});
		 $(document).on("change", "#bank_code", function(){
			shoConFees();
		});
		function showBankList(selected_radio)
		{
			$.ajax({
					url: app_base_url+"index.php/utilities/get_bank_list_options",
					type: "POST",
					dataType: "html",
					async: false,
					success: function(bank_list)
					{
						$(bank_list).insertAfter(selected_radio);
					}
				});
		}
		function shoConFees(amount)
		{
			
			//var amount = $(this).parents('.payment_mode').find(".amount").val();
			var selected_radio = $('#payment_details_<?php echo $app_reference; ?>').find("input[name='selected_pm']:checked");
			var selected_pm = selected_radio.val();
			//alert(selected_pm);
			var bank_code = $("#bank_code").val();
			$(".con_fees_section").remove();
			if((amount.trim() > 0) && (selected_pm!=""))
			{
				if(selected_pm == "TECHP" && bank_code == undefined)
				{
					showBankList(selected_radio);
					return false;
				}
				if(selected_pm != "TECHP")
				{
					bank_code = 0;
					$("#bank_code").remove();
				}
				var pm_arr = selected_pm.split("_");
				var pm = pm_arr[0];
				var method = pm_arr[1];
				var data = "amount="+amount+"&selected_pm="+pm+"&method="+method+"&bank_code="+bank_code;
				$.ajax({
					url: app_base_url+"index.php/utilities/get_instant_recharge_convenience_fees/1",
					type: "POST",
					data: data,
					dataType: "JSON",
					async: false,
					success: function(data)
					{
						var con_fees = parseFloat(data["cf"]).toFixed(2);
						var new_grand_total = 0;
						var prev_con_fees = parseInt($("#convenience_fees").text());
						if(prev_con_fees > 0){
							new_grand_total = amount - prev_con_fees;
						}
						else{
							new_grand_total = data["total"];
						}
						if(isNaN(con_fees))
							con_fees = 0;
						new_grand_total = new_grand_total.toFixed(2);
						$(".grand_total_amount").text(new_grand_total);
						$("#convenience_fees").text(con_fees);
					}
				});
			}
		}
	});
</script>
<!-- Mail - Voucher  ends-->
<style type="text/css">
.col-sm-12.hdr {
    background: #eee;
    padding: 5px;
    border: 1px solid #ccc;
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
.sec_box {
    margin: 10px 0px;
    height: 120px;
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
}
.pack-amt{
	padding-left: 10px;
}
</style>