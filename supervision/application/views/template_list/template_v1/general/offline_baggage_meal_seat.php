<?php
//debug($booking_details); exit;
// generate onword and return
$booking_transaction_details = $booking_details[0]['booking_transaction_details'];
$itinerary_details = $booking_details[0]['booking_itinerary_details'];

// generate onword and return
    $segment_indicator_arr = array();
    $segment_indicator_sort = array();
	$osk = 0; $rsk = 0;
    foreach ($itinerary_details as $key => $key_sort_data) {
        $segment_indicator_sort [$key] = $key_sort_data ['origin'];
    }
    array_multisort($segment_indicator_sort, SORT_ASC, $itinerary_details);
	
	$booking_details[0]['booking_transaction_details'][0]["segment_details"] = array();
	
	if(isset($booking_details[0]['booking_transaction_details'][1]["segment_details"]))
		$booking_details[0]['booking_transaction_details'][1]["segment_details"] = array();
	
    foreach ($itinerary_details as $k => $sub_details) {
        $segment_indicator_arr [] = $sub_details ['segment_indicator'];
        $count_value = array_count_values($segment_indicator_arr);

        if (isset($count_value [1]) && $count_value [1] == 1) {
            $booking_details[0]['booking_transaction_details'][0]["segment_details"][$osk] = $sub_details;
			$osk++;
		} else {
            $booking_details[0]['booking_transaction_details'][1]["segment_details"][$rsk] = $sub_details;
			$rsk++;
        }
    }
	
$trans = $booking_details[0]["booking_transaction_details"];
//debug($trans); exit;
//Onward LFD
$ot = $trans[0];
if(isset($ot["segment_details"][0][0]))
	$osds = $ot["segment_details"][0];
else
	$osds = $ot["segment_details"];

$ocds = $ot["booking_customer_details"];

$sector_count = count($osds);
$baggage_sector = [];
$baggage_sector[0] = $osds[0];
$baggage_sector[0]['to_airport_code'] = $osds[$sector_count - 1]['to_airport_code'];
$baggage_sector[0]['to_airport_name'] = $osds[$sector_count - 1]['to_airport_name'];
$baggage_sector[0]['arrival_datetime'] = $osds[$sector_count - 1]['arrival_datetime'];
$baggage_sector[0]['arrival_datetime'] = $osds[$sector_count - 1]['arrival_datetime'];
?>

<div class="bodyContent">
	<h3>Flight Add-ons</h3>
	 <div class="panel panel-default clearfix">
	 	<div class="col-sm-12">
	 		<form action="<?=base_url("index.php/general/save_offline_baggage_meal_seat/".$app_reference."/".$url_params)?>" method="post">
		 		<div class="nav-tabs-custom">
		 			<ul class="nav nav-tabs">
	                   <li class="active"><a href="#baggage-tab" data-toggle="tab">Baggage</a></li>
	                   <li><a href="#meal-tab" data-toggle="tab">Meal</a></li>
	                   <li><a href="#seat-tab" data-toggle="tab">Seat</a></li>
	                </ul>
	                <div class="tab-content">
	                	<div class="active tab-pane" id="baggage-tab" >
	                		<h4 class="text-danger"><strong>Onward Baggage Details</strong></h4>
		                	<?php 
		                	if(!empty($baggage_sector)){
								foreach ($baggage_sector as $bagg_key => $bagg_value) {
									foreach($ocds AS $ock => $ocd)
									{	
										$pfk = $ocd["origin"];
										$from_loc = $bagg_value["from_airport_code"];
										$to_loc = $bagg_value["to_airport_code"];
										//Passenger Details
										$pass_name = $ocd["title"]." ".$ocd["first_name"]." ".$ocd["last_name"];
									?>
										<div class="clearfix form-group">
											<div class="col-md-2">
												<p><strong><?= $ocd['passenger_type']; ?></strong></p>	
											</div>											
											<div class="col-md-4">
												<p><strong><?=$pass_name." (".$from_loc." - ".$to_loc.")"?></strong></p>
											</div>											
											<div class="col-md-3">
												<label>Extra Baggage (In KGs)</label> 
												<input type="text" class="form-control" name="bgg_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">           
											</div>
											<div class="col-md-3">
												<label>Amount</label> 
												<input type="number" class="form-control" name="bggamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">                
											</div>
										</div>	
								<?php	
									} // foreach end
								} // foreach end
							} // If end	
		                	?>	
	                	</div> <!-- baggage-tab end -->

	                	<div class="tab-pane" id="meal-tab" >
	                		<h4 class="text-danger"><strong>Onward Meal Details</strong></h4>
	                	<?php
		                	foreach($osds AS $osk => $osd)
							{
								$from_loc = $osd["from_airport_code"];
								$to_loc = $osd["to_airport_code"];
								?>
								<h4 class="text-center text-primary"><strong><?= $from_loc.'-'.$to_loc; ?></strong></h4>	
								<?php
								foreach($ocds AS $ock => $ocd)
								{	
									$pfk = $ocd["origin"];									
									//Passenger Details
									$pass_name = $ocd["title"]." ".$ocd["first_name"]." ".$ocd["last_name"];
									?>
										<div class="clearfix form-group">
											<div class="col-md-2">
												<p><strong><?= $ocd['passenger_type']; ?></strong></p>	
											</div>
											<div class="col-md-4">
												<p><strong><?=$pass_name." (".$from_loc." - ".$to_loc.")"?><p></strong>
											</div>	
											<div class="col-md-3">
												<label>Meals Prefereence</label> 
												<input type="text" class="form-control" name="mlpf_<?=$pfk."_".$from_loc."_".$to_loc?>">                
											</div>
											<div class="col-md-3">
												<label>Amount</label>
												<input type="number" class="form-control" name="mlamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
											</div>											
										</div>
									<?php 
								}
							}
						?>	
	                	</div> <!-- meal-tab end -->

	                	<div class="tab-pane" id="seat-tab" >
	                		<h4 class="text-danger"><strong>Onward Seat Details</strong></h4>
	                	<?php
		                	foreach($osds AS $osk => $osd)
							{
								$from_loc = $osd["from_airport_code"];
								$to_loc = $osd["to_airport_code"];
								?>
								<h4 class="text-center text-primary"><strong><?= $from_loc.'-'.$to_loc; ?></strong></h4>	
								<?php
								foreach($ocds AS $ock => $ocd)
								{	
									$pfk = $ocd["origin"];									
									//Passenger Details
									$pass_name = $ocd["title"]." ".$ocd["first_name"]." ".$ocd["last_name"];
									?>
										<div class="clearfix form-group">
											<div class="col-md-2">
												<p><strong><?= $ocd['passenger_type']; ?></strong></p>	
											</div>
											<div class="col-md-4">
												<p><strong><?=$pass_name." (".$from_loc." - ".$to_loc.")"?><p></strong>
											</div>	
											<div class="col-md-3">
												<label>Seat No</label> 
												<input type="text" class="form-control" name="stno_<?=$pfk."_".$from_loc."_".$to_loc?>">                
											</div>
											<div class="col-md-3">
												<label>Amount</label>
												<input type="number" class="form-control" name="stamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
											</div>										
										</div>
									<?php 
								}
							}
						?>		
	                	</div> <!-- baggage-tab end -->
	                </div> <!-- tab-content end -->
		 		</div> <!-- nav-tabs-custom end -->
		 		<div class="col-md-2 col-md-offset-8">					
					<input type="submit" class="form-control btn btn-danger" name="bms_submit" value="Skip & Continue">
				</div>
				<div class="col-md-2">					
					<input type="submit" class="form-control btn btn-success" name="bms_submit" value="Continue">
				</div>		
		 	</form>	
	 	</div> <!-- col-sm-12 end -->
	 </div> <!-- panel end -->
</div> <!-- bodyContent end -->

<style type="text/css">
  .content-wrapper{
    padding: 0!important;
  }
  .bodyContent .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .bodyContent .nav-tabs>li.active>a:hover {
    color: #000 !important;
    font-weight: 600;
    cursor: default;
    background-color: #fff !important;
    border: 0 !important;
  }
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: red;
  }
  .btn-common{
    width: 47%;
    margin-left: 5px;
  }
  .panel{
    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
  }
</style>
