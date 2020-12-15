<?php
	$total_adult_count=array_sum($adult);
	$total_child_count=array_sum($child_with_bed)+array_sum($child_without_bed);
	$total_infant_count=array_sum($infant);
	//echo phpinfo();
	$prev_page_params=(array)json_decode(base64_decode($prev_page_params));
	//debug($prev_page_params);
	if($agent_markup==''){
		$agent_markup=0;
	}
?>
<div class="container id-mt-3">
	<div class="col-md-12">
		<div class="id-main-optional-div">
			<form action="<?php echo base_url();?>index.php/tours/pre_booking" method="post" id="send_booking">
				<div class="row">
					<div class="col-sm-10 nopad">
						<h2><?=$package_details[0]['package_name']?> (<?=$package_details[0]['tour_code']?>)</h2>
					</div>
					<div class="col-sm-2 nopad">
						<p class="id-back-to"><a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$package_details[0]['id']?>/<?=$prev_page?>">< Back to Package</a></p>
					</div>
				</div>
				<div class="row id-dest-o">
					<p><span>Duration :</span><?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
					<p><span>Destination :</span> <?=$city?></p>
					<p><span>Departure Date :</span> <?=$dep_date?></p>
					
				</div>
				<div class="row id-optional-div text-center">
					<div class="col-sm-2 nopad"><p><span><i class="fa fa-bed" aria-hidden="true"></i> Room : </span><?=$no_rooms?></p></div>
					<div class="col-sm-2 nopad"><p><span><i class="fa fa-male" aria-hidden="true"></i> Adult : </span><?php echo array_sum($adult); ?></p></div>
					<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (With Bed)</small> :</span> <?php echo array_sum($child_with_bed); ?></p></div>
					<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (Without Bed)</small> :</span> <?php echo array_sum($child_without_bed); ?></p></div>
					<div class="col-sm-2 nopad"><p><span><i class="fa fa-child" style="font-size: 0.8em" aria-hidden="true"></i> Infant : </span> <?php echo array_sum($infant); ?></p></div>
				</div>
				<div class="row id-optional-table">
					<h3>Basic Tour Cost</h3>
					<table class="table " >
						<tr>
							<th>Room</th>
							<th>Adult</th>
							<th>Child (With Bed)</th>
							<th>Child (Without Bed)</th>
							<th>Infant</th>
						</tr>
						<?php 
							$adult_price=0;
							$child_wb_price=0;
							$child_wob_price=0;
							$infant_price=0;
							
							$total_pack_price=0;
							for($i=1;$i<=$no_rooms;$i++){
								if($adult[$i-1]==1){
									$adult_cal_price=$package_price_details[8]['netprice_price']+$agent_markup;
								}else if($adult[$i-1]==2){
									$adult_cal_price=$package_price_details[10]['netprice_price']+$agent_markup;
								}else{
									$adult_cal_price=$package_price_details[14]['netprice_price']+$agent_markup;
								}
								
								$adult_price	+=$adult_cal_price * $adult[$i-1];
								$child_wb_price	+=($package_price_details[11]['netprice_price']+$agent_markup) * $child_with_bed[$i-1];
								$child_wob_price+=($package_price_details[12]['netprice_price']+$agent_markup)* $child_without_bed[$i-1];
								$infant_price	+=($package_price_details[13]['netprice_price']+$agent_markup) * $infant[$i-1];
								
							//	$total_pack_price+=(($package_price_details[10]['netprice_price']+$agent_markup) * $adult[$i-1])+(($package_price_details[11]['netprice_price']+$agent_markup) * $child_with_bed[$i-1])+(($package_price_details[12]['netprice_price']+$agent_markup) * $child_without_bed[$i-1])+(($package_price_details[13]['netprice_price']+$agent_markup) * $infant[$i-1]);
								
								$total_pack_price+=(($adult_cal_price) * $adult[$i-1])+(($package_price_details[11]['netprice_price']+$agent_markup) * $child_with_bed[$i-1])+(($package_price_details[12]['netprice_price']+$agent_markup) * $child_without_bed[$i-1])+(($package_price_details[13]['netprice_price']+$agent_markup) * $infant[$i-1]);
						?>
								<tr>
									<td><?=$i?></td>
									<td><?php if($adult[$i-1]!=0){echo number_format($adult_cal_price,2).' x '. $adult[$i-1];}else{echo '0.00';}?></td>
									<td><?php if($child_with_bed[$i-1]!=0){echo number_format($package_price_details[11]['netprice_price']+$agent_markup,0).' x '.$child_with_bed[$i-1];}else{echo '0.00';}?></td>
									<td><?php if($child_without_bed[$i-1]!=0){echo number_format($package_price_details[12]['netprice_price']+$agent_markup,0).' x '.$child_without_bed[$i-1];}else{echo '0.00';}?></td>
									<td><?php if($infant[$i-1]!=0){echo number_format($package_price_details[13]['netprice_price']+$agent_markup,0).' x '.$infant[$i-1];}else{echo '0.00';}?></td>
								</tr>
						<?php		
						//echo $total_pack_price.'<br/>';
							}
							//echo $total_pack_price;
						?>
						
						<tr>
							<td style="text-align: right;" colspan="5">
								<span>Total Package Cost: <strong>INR <?=number_format($total_pack_price,0)?></strong></span>
							</td>
						</tr>
					</table>
					<?php 
						if(!empty($optional_tour_details)){
					?>
						<h3>Optional Tour Cost</h3>
						<table class="table " >
							<tr>
								<th style="text-align: left;">Option Name</th>
								<th>Adult</th>
								<th>Child</th>
								<th>Infant</th>
							</tr>
							<?php 
							$total_opt_tour_cost=0;
							$opt_tour_adult=0;
							$opt_tour_child=0;
							$opt_tour_infant=0;
								foreach($optional_tour_details as $opt_Val){
									$opt_tour_adult=$opt_Val['adult_price']*$total_adult_count;
									$opt_tour_child=$opt_Val['child_price']*$total_child_count;
									$opt_tour_infant=$opt_Val['infant_price']*$total_infant_count;
									$total_opt_tour_cost+=($opt_Val['adult_price']*$total_adult_count)+($opt_Val['child_price']*$total_child_count)+($opt_Val['infant_price']*$total_infant_count);
							?>
							<tr>
								<td style="text-align: left;"><?=$opt_Val['tour_name']?></td>
								<td><?php if($total_adult_count!=0){echo number_format($opt_Val['adult_price'],2).' x '.$total_adult_count;}else{echo '0.00';}?></td>
								<td><?php if($total_child_count!=0){echo number_format($opt_Val['child_price'],2).' x '.$total_child_count;}else{echo '0.00';}?></td>
								<td><?php if($total_infant_count!=0){echo number_format($opt_Val['infant_price'],2).' x '.$total_infant_count;}else{echo '0.00';}?></td>
							</tr>
							<?php } ?>
							<tr>
								<td style="text-align: right;" colspan="5">
									<span>Total Options Package Cost: <strong>INR <?=number_format($total_opt_tour_cost,2)?></strong></span>
								</td>
							</tr>
						</table>
						<input type="hidden" class="" name="opt_tour_adult" value="<?=$opt_tour_adult?>">
						<input type="hidden" class="" name="opt_tour_child" value="<?=$opt_tour_child?>">
						<input type="hidden" class="" name="opt_tour_infant" value="<?=$opt_tour_infant?>">
					<?php
						} 
						
						
					?>
					
					<?php
					
						$total_trip_cost_without_gst=$total_pack_price+$total_opt_tour_cost;
						$gst_cost = ($total_trip_cost_without_gst*5)/100;
						$total_trip_with_gst_cost = $total_trip_cost_without_gst+$gst_cost;
						$b2b_adv_amount = ($total_trip_with_gst_cost*@$package_details[0]['b2b_adv_pay'])/100;
						//$b2b_adv_amount = $total_trip_cost_without_gst-$b2b_adv_pay;
					?>
					
				</div>
				<input type="hidden" class="" name="total_trip_cost_without_gst" value="<?=$total_trip_cost_without_gst?>">
				<input type="hidden" class="" name="gst_cost" value="<?=$gst_cost?>">
				<input type="hidden" class="" name="total_trip_with_gst_cost" value="<?=$total_trip_with_gst_cost?>">
				<input type="hidden" class="" name="b2b_adv_amount" value="<?=$b2b_adv_amount?>">
				<input type="hidden" class="" name="total_pack_price" value="<?=$total_pack_price?>">
				<input type="hidden" class="" name="total_opt_tour_cost" value="<?=$total_opt_tour_cost?>">
				<input type="hidden" class="" name="pre_booking_params" value="<?=$pre_booking_params?>">
				<input type="hidden" class="" name="booking_source" value="<?=PROVAB_PACKAGE_BOOKING_SOURCE?>">
				<input type="hidden" class="" name="agent_markup" value="<?=$agent_markup?>">
				<input type="hidden" class="" name="total_agent_markup" value="<?=($agent_markup*$total_adult_count)+($agent_markup*$total_child_count)+($agent_markup*$total_infant_count)?>">
				<input type="hidden" class="paid" name="paid" value="<?=$total_trip_with_gst_cost?>">
				
				<input type="hidden" class="" name="tour_adult_price" value="<?=$adult_price?>">
				<input type="hidden" class="" name="tour_child_wb_price" value="<?=$child_wb_price?>">
				<input type="hidden" class="" name="tour_child_wob_price" value="<?=$child_wob_price?>">
				<input type="hidden" class="" name="tour_infant_price" value="<?=$infant_price?>">
				
				
				<div class="pay-shadow-div">
					<div class="row">
				
				
						<h3>Booking Summary</h3>
						<table class="table " >
							<tr>
								<td>Total Tour Cost</td>
								<td>INR <?=number_format($total_trip_cost_without_gst,2)?></td>
							</tr>
							<tr>
								<td>GST (5%)</td>
								<td>INR <?=number_format($gst_cost,2)?></td>
							</tr>
							<tr>
								<td>Grand Total</td>
								<td>INR <?=number_format($total_trip_with_gst_cost,2)?></td>
							</tr>
						</table>
						
						<div class="row">
							<p class="select-pay">Select Payment Amount</p>
							<div class="col-sm-4 padfive disabled">
								<div class="id-pay-div">
									<label for="pay1">
									<input type="radio" id="pay1" name="pay1" value="advance_pay" class="id-optional-check"  data-amount="<?=$b2b_adv_amount?>" <?php if($b2b_adv_amount==0) echo "disabled";?>>
									
									<span>Pay advance & book your seats</span><p class="id-pay-amt">INR <?=number_format($b2b_adv_amount,2)?></p></label>
								</div>
							</div>
							<div class="col-sm-4 padfive">
								<div class="id-pay-div">
									<label for="pay2">
									<input type="radio" id="pay2" value="full_pay" name="pay1" class="id-optional-check" checked="checked" data-amount="<?=$total_trip_with_gst_cost?>">
									<span>Pay full Amount</span><p class="id-pay-amt">INR <?=number_format($total_trip_with_gst_cost,2)?></p></label>
								</div>
							</div>
							<div class="col-sm-4 padfive">
								<div class="id-pay-div">
									<label for="pay3">
									<input type="radio" id="pay3" value="wish_pay" name="pay1" class="id-optional-check wish_pay" <?php if($b2b_adv_amount==0) echo "disabled";?>>
									<span>Enter the amount you wish to pay</span><br><input class="id-pay-check user_amount" type="number" disabled name="user_amount" placeholder="Enter Amount"></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="contbk">
								<div class="contcthdngs">Select Payment Mode</div>
								<input type="radio" name="selected_pm" value="WALLET" checked/> Wallet
								<input type="radio" name="selected_pm" value="PAYTM_CC" /> Credit Card
								<input type="radio" name="selected_pm" value="PAYTM_DC" /> Debit Card
								<input type="radio" name="selected_pm" value="PAYTM_PPI" /> PAYTM Wallet
								<input type="radio" name="selected_pm" value="TECHP" /> Net Banking
							</div>
						</div>
					</div>
					<div class="row id-optional-btn">
						<div class="col-sm-offset-3 col-sm-6 nopad">
							<!-- <button class="btn btn-info back_prev_page">Back</button> -->
							<button class="btn btn-danger click_pay">Continue Payment</button>
						</div>
					</div>
				</div>
			</form>
			<form action="<?php echo base_url();?>index.php/tours/optional_tour_details/<?=$prev_page_params['pack_id']?>" method="post" id="prev_page_Data">
				<input type="hidden" class="" name="pack_id" value="<?=$prev_page_params['pack_id']?>">
				<input type="hidden" class="" name="sel_departure_date" value="<?=$prev_page_params['sel_departure_date']?>">
				<input type="hidden" class="" name="sel_adult_count" value="<?=$prev_page_params['sel_adult_count']?>">
				<input type="hidden" class="" name="sel_child_wb_count" value="<?=$prev_page_params['sel_child_wb_count']?>">
				<input type="hidden" class="" name="sel_child_wob_count" value="<?=$prev_page_params['sel_child_wob_count']?>">
				<input type="hidden" class="" name="sel_infant_count" value="<?=$prev_page_params['sel_infant_count']?>">
				<input type="hidden" class="" name="sel_room_count" value="<?=$prev_page_params['sel_room_count']?>">
				<input type="hidden" class="" name="agent_markup" value="<?=prev_page_params['agent_markup']?>">
				
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(document).on('click','.wish_pay',function(){
			
				if($(this).prop("checked") == true){
					$('.user_amount').prop('disabled',false);
				}else if($(this).prop("checked") == false){
					$('.user_amount').prop('disabled',true);
				}	
		});
		$(document).on('click','#pay1,#pay2,#pay3',function(){
			var adv_amt="<?php echo $b2b_adv_amount; ?>";
			if(adv_amt=='0'){
				$('#pay1').prop("checked",false);
				$('#pay1').prop("disabled",true);
				$('#pay2').prop("checked",true);
			}else{
				
				if($('#pay3').prop("checked") == false){
					$('.user_amount').prop('disabled',true);
					var paid_amount=$(this).data('amount');
					$('.paid').val(paid_amount);
				}else{
					var paid_amount=$('.user_amount').val();
					$('.paid').val(paid_amount);
				}
				
			}
		});
		$(document).on('keyup','.user_amount',function(){
			var paid_amount=$('.user_amount').val();
			$('.paid').val(paid_amount);
		});
		$(document).on('click','.click_pay',function(e){
			var user_amount=parseInt($('.user_amount').val());
			var adv_amt="<?php echo $b2b_adv_amount; ?>";
			var min_val = parseInt('<?php echo $b2b_adv_amount; ?>');
			var max_val = parseInt('<?php echo $total_trip_with_gst_cost; ?>');
			if($('#pay3').prop("checked") == true){
				
				if(user_amount<min_val || user_amount>max_val){
					e.preventDefault();
					alert('Please enter amount which is greater than the advance amount and less than the full amount');
					$('.user_amount').val('');
				}else if(adv_amt=='0' && ($('#pay3').prop("checked") == true || $('#pay1').prop("checked") == true)){
					e.preventDefault();
					alert('Please select full amount to pay');
					$('.user_amount').val('');
				}
			}	
			
		});
		$(document).on('click','.back_prev_page',function(e){
			var form = document.getElementById("prev_page_Data");
			form.submit();
		});
	$("input[name='selected_pm']").change(function(){
		
    	shoConFees();
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
	function shoConFees()
	{
		
		var amount = $(".paid").val();
		//var amt_arr = amount.split(" ");
		//amount = amt_arr[amt_arr.length-1];
		var selected_radio = $("input[name='selected_pm']:checked");
		var selected_pm = selected_radio.val();
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

<style type="text/css">
	.id-main-optional-div{
		padding: 40px;
		background-color: #fff;
		/*border:1px solid #ccc;*/
		box-shadow: 0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19) !important;
	}
   .id-mt-3{
    margin-top: 30px;
   }
   .id-optional-div{
   	padding: 10px;
   	border-radius: 8px;
   	/*background-color: #ccc;*/
   	background: linear-gradient(0deg,#002042,#0a8ec1);
   	color: #fff;
   }
   .id-optional-div span{
   	/*font-weight: bold;*/
   }
   .id-optional-div p{
   	margin: 0;
   	font-size: 15px;
   }
   .id-optional-div small{
   	font-weight: normal;
   }
   .id-dest-o p{
   	font-size: 15px;
   }
   .id-dest-o{
   	padding: 10px;
   	padding-left: 0;
   }
   .id-dest-o span{
   	font-weight: bold;
   }
   .id-optional-table label{
   	cursor: pointer;
   	padding: 0;
   }
   .id-optional-table table, td, th{
   	font-size: 13px!important;
   	border:1px solid #ccc;
   	vertical-align: middle!important;
   }
   .id-optional-table th,td{
   	text-align: center;
   }
   .id-optional-table span{
   	padding-bottom: 15px;
   }
   .id-optional-table table{
   	box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   }
   .pay-shadow-div{
   	box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   	margin: 0;
   	padding: 10px;
   	padding-bottom: 20px;
   	margin-bottom: 10px;
   }
   .id-optional-table h3{
   	color: #288abc;
   	margin-bottom: 10px;
   	text-align: left;
   }
   .pay-shadow-div h3{
   	color: #288abc;
   	margin-bottom: 10px;
   	text-align: left;
   	margin-top: 0;
   }
   .pay-shadow-div table{
   	margin-bottom: 10px;
   }
   .id-optional-btn button{
   	height: 50px;
   	margin-top: 10px;
   	font-size: 20px;
   }
   .id-optional-table th{
   	background-color: #eee;
   }
   .id-main-optional-div h2{
   	color: #ef1a16;
   	margin-top: 0;
   	font-weight: bold;
   	/*text-align: center;*/
   }
   .content-wrapper{
    background-color:#f7f7f7!important;
   }
   .id-main-optional-div .id-back-to{
   	color: #288abc;
   	text-align: right;
   	font-size: 15px;
   	cursor: pointer;
   }
   .id-pay-div span{
   	font-size: 15px;
   	color: #666;
   }
   .id-optional-check:not(:checked), .id-optional-check:checked{
   	position: relative;
    left: 0; 
    top: 5px;
    width: 40px;
    height: 20px;
    margin-top: 5px!important;
    cursor: pointer;
    
   }
   .id-optional-check:not(:checked) + label:after, .id-optional-check:checked + label:after {
   	content: unset!important;
   }
   .id-optional-check{
   	margin: 0;
   }
   .id-pay-div label{
   	cursor: pointer;
   	padding: 0;
   	width: 100%;
   }
   .id-pay-div{
   	background-color: #f1faff;
    border: 1px solid #9ab6c3;
   	height: 80px;
   	margin-bottom: 10px;
   	border-radius: 15px;
   }
   .id-pay-check{
   	background: transparent;
    border: none;
    font-size: 18px;
    margin: 0;
    border-bottom: 1px solid #ccc;
    width: 80%;
    padding-bottom: 4px;
    margin-top: 10px;
    margin-left: 40px;
   }
   .id-pay-div .id-pay-amt{
   	font-size: 24px;
   	text-align: center;
    color: #ef1a16;
    font-weight: bold;
    padding-top: 5px;
    /*padding-left: 40px;*/
   }
  .pay-shadow-div .select-pay{
  	font-size: 13px;
  	text-align: center;
  	margin-bottom: 5px;
  	margin-top: 10px;
  	color: #666;
  }
   .row.id-optional-btn {
    display: block;
    width: 100%;
    
    text-align: center;
}
.id-optional-btn .col-sm-6.nopad {
    display: flex;
}
.id-optional-btn button {
    height: 50px;
    font-size: 20px;
    flex: 1 1 0;
    width: 0;
    margin: 0px 5px;
    border-radius: 0;
}
</style>