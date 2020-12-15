<?php
	$total_adult_count=array_sum($adult);
	$total_child_count=array_sum($child_with_bed)+array_sum($child_without_bed);
	$total_infant_count=array_sum($infant);
	//echo phpinfo();
	$prev_page_params=(array)json_decode(base64_decode($prev_page_params));
	
?>
<div class="container mb-4 mt-4 m-mt-2">
	<div class="col-md-12">
		<div class="row text-right">
		  <p class="id-back-to"><a class="pull_right id-back-s" href="<?php echo base_url().'index.php/tours/optional_tour_details/'.$package_details[0]['id']?>/<?=$prev_page?>">< Back to Package</a>
		  
		  </p>
		</div>
    <form action="<?php echo base_url();?>index.php/tours/pre_booking" method="post" id="send_booking">
		<div class="id-main-optional-div mt-1 m-mb-5">
			<h3 class="div-h3">Fare Breakup Details</h3>

			<div class="row id-dest-o1">
				<h3><?=$package_details[0]['package_name']?></h3>
				<p><span><i class="fa fa-clock" aria-hidden="true"></i>&nbsp; Duration : </span><?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
				<p><span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Destination : </span><?=$city?></p>
				<p><span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; Departure Date : </span> <?=$dep_date?></p>
			</div>
			<div class="row id-optional-div1 text-center">
				<div class="col-sm-2 col-xs-6 nopad"><p><span><i class="fa fa-bed" aria-hidden="true"></i> Room : </span><span class=""><?=$no_rooms?></span></p>
				</div>
				<div class="col-sm-2 col-xs-6 nopad"><p><span><i class="fa fa-male" aria-hidden="true"></i> Adult : </span><span class="total_adult_count"><?php echo array_sum($adult); ?></span></p>
				</div>
				<div class="col-sm-3 nopad col-xs-6"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (With Bed)</small> : </span><span class="total_child_wb_count"><?php echo array_sum($child_with_bed); ?></p>
				</div>
				<div class="col-sm-3 col-xs-6 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (Without Bed)</small> : </span><span class="total_child_wob_count"><?php echo array_sum($child_without_bed); ?></span></p>
				</div>
				<div class="col-sm-2 col-xs-12 nopad"><p><span><i class="fa fa-child" style="font-size: 0.8em" aria-hidden="true"></i> Infant : </span><span class="total_infant_count"><?php echo array_sum($infant); ?></span></p>
				</div>
			</div>

			<div class="row id-optional-table1">
				<h4>Basic Tour Cost</h4>
				<div class="table-responsive">
					<table class="table">
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
									$adult_cal_price=$package_price_details[8]['netprice_price'];
								}else if($adult[$i-1]==2){
									$adult_cal_price=$package_price_details[10]['netprice_price'];
								}else{
									$adult_cal_price=$package_price_details[14]['netprice_price'];
								}
								
								$adult_price	+=$adult_cal_price * $adult[$i-1];
								$child_wb_price	+=($package_price_details[11]['netprice_price']) * $child_with_bed[$i-1];
								$child_wob_price+=($package_price_details[12]['netprice_price'])* $child_without_bed[$i-1];
								$infant_price	+=($package_price_details[13]['netprice_price']) * $infant[$i-1];
							
								
								$total_pack_price+=(($adult_cal_price) * $adult[$i-1])+(($package_price_details[11]['netprice_price']) * $child_with_bed[$i-1])+(($package_price_details[12]['netprice_price']) * $child_without_bed[$i-1])+(($package_price_details[13]['netprice_price']) * $infant[$i-1]);
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
							<td style="text-align: right;" colspan="5">Total Package Cost: <strong>INR <?=number_format($total_pack_price,0)?></strong></td>
						</tr>
					</table>
				</div>
			

      <div class="row id-optional-table1">
		<?php 
			if(!empty($optional_tour_details)){
		?>
        <h4>Optional Services</h4>
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th style="text-align:left;">Services</th>
              <th>Price Per Adult</th>
              <th>Price Per Child</th>
              <th>Price Per Infant</th>
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
				<td><?=number_format($opt_Val['adult_price'],2)?> x <?=$total_adult_count?></td>
				<td><?=number_format($opt_Val['child_price'],2)?> x <?=$total_child_count?></td>
				<td><?=number_format($opt_Val['infant_price'],2)?> x <?=$total_infant_count?></td>
			</tr>
			<?php } ?>
            <tr>
              <td style="text-align: right;" colspan="4">Total Options Package Cost: <strong>INR <?=number_format($total_opt_tour_cost,2)?></strong></td>
            </tr>
          </table>
			<input type="hidden" class="" name="opt_tour_adult" value="<?=$opt_tour_adult?>">
			<input type="hidden" class="" name="opt_tour_child" value="<?=$opt_tour_child?>">
			<input type="hidden" class="" name="opt_tour_infant" value="<?=$opt_tour_infant?>">
			</div>
				<?php
					} 
					
					
				?>
				
				<?php
				//debug($package_details[0]);
					$total_trip_cost_without_gst=$total_pack_price+$total_opt_tour_cost;
					$gst_cost = ($total_trip_cost_without_gst*5)/100;
					$total_trip_with_gst_cost = $total_trip_cost_without_gst+$gst_cost;
					$b2c_adv_amount = ($total_trip_with_gst_cost*@$package_details[0]['b2c_adv_pay'])/100;
					
					//$b2b_adv_amount = $total_trip_cost_without_gst-$b2b_adv_pay;
				?>
				
			</div>
			<input type="hidden" class="" name="total_trip_cost_without_gst" value="<?=$total_trip_cost_without_gst?>">
			<input type="hidden" class="" name="gst_cost" value="<?=$gst_cost?>">
			<input type="hidden" class="" name="total_trip_with_gst_cost" value="<?=$total_trip_with_gst_cost?>">
			<input type="hidden" class="" name="b2c_adv_amount" value="<?=$b2c_adv_amount?>">
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
        </div>
      

      <div class="row id-optional-table1 mt-2">
        <h4>Booking Summary</h4>
        <div class="table-responsive">
          <table class="table">
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
        </div>
      </div>

      <div class="row b2c_pay-div">
        <p class="select-pay1">Select Payment Amount</p>
        <div class="col-sm-4 padfive">
          <div class="id-pay-div1">
            <label for="pay1">
              <input type="radio" id="pay1" name="pay1" value="advance_pay" class="id-optional-check1"  data-amount="<?=$b2c_adv_amount?>" <?php if($b2c_adv_amount==0) echo "disabled";?>>
              <span>Pay advance &amp; book your seats</span>
              <p class="id-pay-amt1">&#8377; <?=number_format($b2c_adv_amount,2)?></p>
            </label>
          </div>
        </div>
        <div class="col-sm-4 padfive">
          <div class="id-pay-div1">
            <label for="pay2">
              <input type="radio" id="pay2" value="full_pay" name="pay1" class="id-optional-check1" checked="checked" data-amount="<?=$total_trip_with_gst_cost?>">
              <span>Pay full Amount</span>
              <p class="id-pay-amt1">&#8377; <?=number_format($total_trip_with_gst_cost,2)?></p>
            </label>
          </div>
        </div>
        <div class="col-sm-4 padfive">
          <div class="id-pay-div1">
            <label for="pay3">
              <input type="radio" id="pay3" value="wish_pay" name="pay1" class="id-optional-check1 wish_pay" <?php if($b2c_adv_amount==0) echo "disabled";?>>
              <span>Enter the amount you wish to pay</span>
              <br>
              <input class="id-pay-check1 user_amount" type="number" disabled name="user_amount" placeholder="Enter Amount">
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 padfive">
          <div class="contbk">
            <div class="contcthdngs">Select Payment Mode</div>
            <!--<input type="radio" name="selected_pm" value="WALLET" checked/> Wallet-->
            <input type="radio" name="selected_pm" value="PAYTM_CC" checked /> Credit Card
            <input type="radio" name="selected_pm" value="PAYTM_DC" /> Debit Card
            <input type="radio" name="selected_pm" value="PAYTM_PPI" /> PAYTM Wallet
            <input type="radio" name="selected_pm" value="TECHP" /> Net Banking
          </div>
        </div>
      </div>


      <div class="row id-optional-btn">
        <div class="col-sm-offset-4 col-sm-4 nopad">
          <button class="btn btn-primary form-control click_pay">Continue &nbsp;&nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>
	</form>
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
			var adv_amt="<?php echo $b2c_adv_amount; ?>";
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
		//alert();
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
		//alert(amount);
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
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
    
  }
  .mt-2{
    margin-top: 20px;
  }
  .mt-4{
    margin-top: 40px;
  }
   .id-mt-3{
    margin-top: 30px;
   }
   .id-optional-div1{
    padding: 10px;
    border-radius: 5px;
    background-color: #fc901b;
    /*background: linear-gradient(0deg,#002042,#0a8ec1);*/
    color: #fff;
   }
   .id-optional-div1 span{
    /*font-weight: bold;*/
   }
   .id-optional-div1 p{
    margin: 0;
    font-size: 15px;
   }
   .id-optional-div1 small{
    font-weight: normal;
   }
   .id-dest-o1 p{
    font-size: 15px;
   }
   .id-dest-o1{
    padding: 10px;
    padding-left: 0;
    margin-top: 20px;
   }
   .id-dest-o1 span{
    font-weight: bold;
   }
   .id-optional-table1 label{
    cursor: pointer;
    padding: 0;
   }
   .id-optional-table1 table, td, th{
    font-size: 15px!important;
    border:1px solid #ccc;
    vertical-align: middle!important;
   }
   .id-optional-table1 th,td{
    text-align: center;
   }
   .id-optional-table1 span{
    padding-bottom: 15px;
   }
   .id-optional-table1 table{
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   }
   .id-optional-table1 h4{
    color: #1fa9d7;
    margin-bottom: 10px;
    margin-top: 0;
    /*text-align: center;*/
   }
   .id-optional-btn button{
    height: 50px;
    margin-top: 10px;
    font-size: 15px;
   }
   .id-optional-table1 th{
    background-color: #eee;
   }
   .id-dest-o1 h3{
    color: #ef1a16;
    margin-top: 0;
   }
   .id-main-optional-div .div-h3{
    color: #000;
    text-align: center;
    padding: 10px;
    background-color: #f2f2f2;
    margin-top: 0;
   }
   .content-wrapper{
    background-color:#f7f7f7!important;
   }
   .id-dest-o1 .fa{
    color: #0a8ec1;
    font-size: 14px;
   }
  .id-back-to{
    color: #1fa9d7;
    text-align: right;
    font-size: 13px;
    cursor: pointer;
    margin-bottom: 0;
   }
   .id-dest-o1 p{
    display: inline-block;
    margin-right: 20px;
   }
   .mb-4{
    margin-bottom: 40px;
   }
  .mb-1{
    margin-bottom: 10px;
  }
  .m-0{
    margin: 0;
  }
  .mb-0{
    margin-bottom: 0;
  }
  .mt-1{
    margin-top: 10px;
  }
  .b2c_pay-div .id-pay-div1 span{
    font-size: 15px;
    color: #666;
   }
 .id-pay-div1 .id-pay-amt1{
  font-size: 24px;
  text-align: center;
  color: #ef1a16;
  font-weight: bold;
  padding-top: 5px;
  /*padding-left: 40px;*/
 }
 .select-pay1{
    font-size: 13px;
    text-align: center;
    margin-bottom: 5px;
    margin-top: 10px;
    color: #666;
  }
  .b2c_pay-div .id-optional-check1:not(:checked), .id-optional-check1:checked{
    position: relative;
    left: 0; 
    top: 5px;
    width: 40px;
    height: 20px;
    margin-top: 5px!important;
    cursor: pointer;
    
   }
   .b2c_pay-div .id-optional-check1:not(:checked) + label:after, .id-optional-check1:checked + label:after {
    content: unset!important;
    background-color: red!important;
   }
   .b2c_pay-div .id-optional-check1{
    margin: 0;
   }
   .id-pay-div1 label{
    cursor: pointer;
    padding: 0;
    width: 100%;
   }
   .b2c_pay-div .id-pay-div1{
    background-color: #ffffff;
    border: 2px solid #d6d6d6;
    height: 86px;
    /*margin-bottom: 15px;*/
    border-radius: 5px;
   }
   .b2c_pay-div .id-pay-check1{
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
   .id-pay-div1 .id-pay-amt1{
    font-size: 24px;
    text-align: center;
    color: #ef1a16;
    font-weight: bold;
    padding-top: 5px;
    /*padding-left: 40px;*/
   }
   .id-pay-div1 span{
    font-size: 15px;
    color: #666;
   }
   .padfive{
    padding: 0 2px!important;
   }
   .contbk {
      border: 2px solid #d6d6d6;
      border-radius: 5px;
      margin-bottom: 15px;
  }
  @media only screen and (max-width: 767px){
  .room_sec ul li {
      width: 46%;
      margin-bottom: 10px;
  }
  li.id-room{
    width: 100%!important;
  }
  .id-optional-div1 p {
    margin: 4px;
    font-size: 12px;
  }
  .id-dest-o1 h3 {
    margin-bottom: 10px;
  }
  .id-dest-o1 p{
    margin-bottom: 10px;
  }
  .m-mb-5{
    margin-bottom: 50px;
  }
  .id-back-to{
    font-size: 14px;
  }
  .menuandall .menu{
    display: none;
  }
  .m-mt-2{
    margin-top: 20px!important;
  }
  .input-date{
    margin-top: 10px;
  }
  .list-inline .room_label{
    margin-top: 0;
  }
  hr{
    display: none;
  }
  .m-mb-1{
    margin-bottom: 10px;
  }
  .id-pay-div1{
    margin-bottom: 10px;
  }
  .topssec {
    height: auto!important;
  }
  .section_top {
      padding: 0 0 10px 0px;
  }
  .userimage img {
    border: 1px solid #aaa;
    padding: 2px;
    border-radius: 100%;
    width: 100%;
  }
}

input#pay1:disabled {
    cursor: not-allowed;
}
input#pay3:disabled
{
	 cursor: not-allowed;
}
</style>