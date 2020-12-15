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
?>
<form action="<?=base_url("index.php/general/save_offline_baggage_meal_seat/".$app_reference."/".$url_params)?>" method="post">
<h4>Onward Extra Information</h4>
<?php
//debug($osds); exit;
foreach($osds AS $osk => $osd)
{
	foreach($ocds AS $ock => $ocd)
	{	
	$pfk = $ocd["origin"];
	$from_loc = $osd["from_airport_code"];
	$to_loc = $osd["to_airport_code"];
	//Passenger Details
	$pass_name = $ocd["title"]." ".$ocd["first_name"]." ".$ocd["last_name"];
	?>
		<div class="clearfix form-group">
		<h4><?=$pass_name." (".$from_loc." - ".$to_loc.")"?></h4>
		<div class="col-md-2">
			<label>Extra Baggage (In KGs)</label> 
			<input type="text" class="form-control" name="bgg_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">           
		</div>
		<div class="col-md-2">
			<label>Amount</label> 
			<input type="number" class="form-control" name="bggamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">                
		</div>
		<div class="col-md-2">
			<label>Meals Prefereence</label> 
			<input type="text" class="form-control" name="mlpf_<?=$pfk."_".$from_loc."_".$to_loc?>">                
		</div>
		<div class="col-md-2">
			<label>Amount</label>
			<input type="number" class="form-control" name="mlamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
		</div>
		<div class="col-md-2">
			<label>Seat No</label> 
			<input type="text" class="form-control" name="stno_<?=$pfk."_".$from_loc."_".$to_loc?>">                
		</div>
		<div class="col-md-2">
			<label>Amount</label>
			<input type="number" class="form-control" name="stamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
		</div>
		</div>
	<?php }
}
//Return LFD
if(isset($trans[1])){
$rt = $trans[1];
if(isset($rt["segment_details"][0][0]))
	$rsds = $rt["segment_details"][0];
else
	$rsds = $rt["segment_details"];
$rcds = $rt["booking_customer_details"];
?>
<h4>Return Extra Information</h4>
<?php 
foreach($rsds AS $rsk => $rsd)
{
	foreach($rcds AS $rck => $rcd)
	{	
	$pfk = $rcd["origin"];
	$from_loc = $rsd["from_airport_code"];
	$to_loc = $rsd["to_airport_code"];
	//Passenger Details
	$pass_name = $rcd["title"]." ".$rcd["first_name"]." ".$rcd["last_name"];
	?>
		<div class="clearfix form-group">
		<h4><?=$pass_name." (".$from_loc." - ".$to_loc.")"?></h4>
		<div class="col-md-2">
			<label>Extra Baggage (In KGs)</label> 
			<input type="text" class="form-control" name="bgg_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">           
		</div>
		<div class="col-md-2">
			<label>Amount</label> 
			<input type="number" class="form-control" name="bggamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">                
		</div>
		<div class="col-md-2">
			<label>Meals Prefereence</label> 
			<input type="text" class="form-control" name="mlpf_<?=$pfk."_".$from_loc."_".$to_loc?>">                
		</div>
		<div class="col-md-2">
			<label>Amount</label>
			<input type="number" class="form-control" name="mlamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
		</div>
		<div class="col-md-2">
			<label>Seat No</label> 
			<input type="text" class="form-control" name="stno_<?=$pfk."_".$from_loc."_".$to_loc?>">                
		</div>
		<div class="col-md-2">
			<label>Amount</label>
			<input type="number" class="form-control" name="stamt_<?=$pfk."_".$from_loc."_".$to_loc?>" value="0">
		</div>
		</div>
	<?php }
}
}
?>
<div class="col-md-2">
	<input type="submit" class="form-control btn btn-primary" name="bms_submit" value="Save Extra Information">
</div>
</form>