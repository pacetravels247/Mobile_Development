<?php
	$app_reference = $bd["data"]["booking_details"][0]["app_reference"];
	$booking_source = $bd["data"]["booking_details"][0]["booking_source"];
	$status = $bd["data"]["booking_details"][0]["status"];
	$op = "show_voucher";
	
	$customers = $bd["data"]["booking_customer_details"];
	// debug($bd["data"]["booking_details"][0]);exit;
	$divider = 1;
	if($bd["data"]["booking_details"][0]['booking_source']==PROVAB_FLIGHT_BOOKING_SOURCE && $bd["data"]["booking_details"][0]["trip_type"] == "circle")
		$divider = 2;
	
	$no_of_customers_to_show = count($customers)/$divider;
?>
<div class="bodyContent container">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
                <h3>Please use the below links to generate voucher of your choice.</h3>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->

        <div class="clearfix"></div>
			<div class="tab-content">
				<div id="tableList" class="table-responsive">
					<table class="table table-stripped">
						<?php 
						// debug($customers);exit;
							//0 or 1 - With or without fare
							$full_voucher_with_fare_btn = get_voucher_link($app_reference, $booking_source, $status, 1);
							$full_voucher_without_fare_btn = get_voucher_link($app_reference, $booking_source, $status, 0);
						?>
						<tr>
							<th>Complete Voucher</th>
							<th><?=$full_voucher_with_fare_btn?></th>
							<th><?=$full_voucher_without_fare_btn?></th>
						</tr>
						<?php foreach($customers AS $key => $cust) { 
							if($key >= $no_of_customers_to_show)
								continue;
							extract($cust);
							$full_voucher_with_fare_btn = get_voucher_link($app_reference, $booking_source, $status, 1, $origin);
							$full_voucher_without_fare_btn = get_voucher_link($app_reference, $booking_source, $status, 0, $origin);
						?>
						<tr>
							<th>
								<?php echo $title." ".$first_name." ".$last_name; ?>
							</th>
							<th><?=$full_voucher_with_fare_btn?></th>
							<th><?=$full_voucher_without_fare_btn?></th>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	function get_voucher_link($app_reference, $booking_source, $status, $with_fare = 1, $cust_origin = 0)
	{		
		if($with_fare)
			$btn_name = "With Fare";
		else
			$btn_name = "Without Fare";
		
		$extend_url = "?with_fare=".$with_fare."&cust_origin=".$cust_origin;
		
		return '<a href="'.base_url("index.php/voucher/flight/".$app_reference."/".$booking_source."/".$status."/".$op.$extend_url).'">'.$btn_name.'</a>';
	}
?>