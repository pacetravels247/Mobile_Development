<section class="passenger-details">
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default b-r-0 m-0">
					<div class="panel-body p-b-0">
						<h1 class="h3 m-t-0">Travel Details</h1>
						<h2 class="h4 text-p">Hotel Price</h2>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>Hotel Price</td>
								<td class="text-right"><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <span class="h4 text-i"><?=$pre_booking_params['markup_price_summary']['RoomPrice']?></span></td>
							</tr>
							<tr>
								<td>Taxes and Fees</td>
								<td class="text-right"><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <span class="h4 text-i"><?=($convenience_fees+$tax_service_sum)?></span></td>
							</tr>
							<tr>
								<td><strong>Grand Total</strong></td>
								<td class="text-right"><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <span class="h4 text-i"><?=$convenience_fees+$total_price?></span></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="panel panel-default b-r-0 m-0">
					<div class="panel-body p-t-0 p-b-0 text-center">
						<h2 class="h4 text-p text-left">Hotel Details</h2>
						<div class="clearfix">
							<div class="pull-left">
								<h3 class="h5"><b><?=$pre_booking_params['HotelName']?></b></h3>
							</div>
							<div class="pull-right">
								<h5>
									<span class="rating-no">
										<?=print_star_rating($pre_booking_params['StarRating'])?>
									</span>
								</h5>
							</div>
						</div>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>Check-In Date</td>
								<td class="text-right"><b><?=app_friendly_day($search_data['from_date'])?></b></td>
							</tr>
							<tr>
								<td>Check-Out Date</td>
								<td class="text-right"><b><?=app_friendly_day($search_data['to_date'])?></b></td>
							</tr>
							<tr>
								<td>Night(s)</td>
								<td class="text-right"><b><?=$search_data['no_of_nights']?></b></td>
							</tr>
							<tr>
								<td>Total Room(s)</td>
								<td class="text-right"><b><?=$search_data['room_count']?></b></td>
							</tr>
							<tr>
								<td>Total Passengers</td>
								<td class="text-right"><b>Adult : <?=array_sum($search_data['adult_config'])?>, Child : <?=array_sum($search_data['child_config'])?></b></td>
							</tr>
							<tr class="hide">
								<td><a href="#">More Details</a></td>
								<td class="text-right"><a href="#">Fare Breakup</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-8">
				<div class="panel panel-default b-r-0">
					<div class="panel-body">
						<form action="<?=base_url().'index.php/hotel/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off">
							<div class="hide">
								<?php $dynamic_params_url = serialized_data($pre_booking_params);?>
								<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
								<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
								<input type="hidden" required="required" name="op"			value="book_flight">
								<input type="hidden" required="required" name="booking_source"		value="<?=$booking_source?>" readonly>
								
							</div>
							<div class="clearfix">
								<div class="pull-left">
									<h1 class="h3 m-t-0"><img src="<?=$GLOBALS['CI']->template->template_images('icons/angle-arrow-down.png')?>" alt="Angle Arrow Down Icon"> Passenger Details</h1>
								</div>
								</div>
<?php
/**
 * Collection field name 
 */
//Title, Firstname, Middlename, Lastname, Phoneno, Email, PaxType, LeadPassenger, Age, PassportNo, PassportIssueDate, PassportExpDate
$total_adult_count	= array_sum($search_data['adult_config']);
$total_child_count	= array_sum($search_data['child_config']);
//------------------------------ DATEPICKER START
$i = 1;
$datepicker_list = array();
if ($total_adult_count > 0) {
	for ($i=1; $i<=$total_adult_count; $i++) {
		$datepicker_list[] = array('adult-date-picker-'.$i, ADULT_DATE_PICKER);
	}
}

if ($total_child_count > 0) {
	for ($i=$i; $i<=($total_child_count+$total_adult_count); $i++) {
		$datepicker_list[] = array('child-date-picker-'.$i, CHILD_DATE_PICKER);
	}
}
$GLOBALS['CI']->current_page->set_datepicker($datepicker_list);
//------------------------------ DATEPICKER END
//FIXME - Balu A to make nationality dynamic
if (valid_array($pax_details) == true) {
	//pax details passed as page input
	
} else {
	//lets default the values
	
}
$total_pax_count	= $total_adult_count+$total_child_count;
//First Adult is Primary and and Lead Pax
$adult_enum = $child_enum = get_enum_list('title');
$gender_enum = get_enum_list('gender');
unset($adult_enum[MASTER_TITLE]); // Master is for child so not required
unset($child_enum[MASTER_TITLE]); // Master is not supported in TBO list
$adult_title_options = generate_options($adult_enum, false, true);
$child_title_options = generate_options($child_enum, false, true);
$gender_options	= generate_options($gender_enum);
//$nationality_options = generate_options($iso_country_list, array(INDIA_CODE));//FIXME get ISO CODE --- ISO_INDIA
//$passport_issuing_country_options = generate_options($country_list);
$passport_issuing_country = 92;
$temp_passport_expiry_date = date('Y-m-d', strtotime('+5 years'));
$static_passport_details = array();
$static_passport_details['passenger_passport_expiry_day'] = date('d', strtotime($temp_passport_expiry_date));
$static_passport_details['passenger_passport_expiry_month'] = date('m', strtotime($temp_passport_expiry_date));
$static_passport_details['passenger_passport_expiry_year'] = date('Y', strtotime($temp_passport_expiry_date));

//lowest year wanted
$cutoff = date('Y', strtotime('+20 years'));
//current year
$now = date('Y');
$day_options	= generate_options(get_day_numbers());
$month_options	= generate_options(get_month_names());
$year_options	= generate_options(get_years($now, $cutoff));
/**
 * check if current print index is of adult or child by taking adult and total pax count
 * @param number $total_pax		total pax count
 * @param number $total_adult	total adult count
 */
function is_adult($total_pax, $total_adult)
{
	return ($total_pax>$total_adult ?	false : true);
}

/**
 * check if current print index is of adult or child by taking adult and total pax count
 * @param number $total_pax		total pax count
 * @param number $total_adult	total adult count
 */
function is_lead_pax($pax_count)
{
	return ($pax_count == 1 ? true : false);
}
$pax_index = 1;
$lead_pax_details = @$pax_details[0];
$child_age = @$search_data['child_age'];
for($pax_index=1; $pax_index <= $total_pax_count; $pax_index++) {//START FOR LOOP FOR PAX DETAILS
	$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
?>
	<div class="hide">
	<?php
		if(is_adult($pax_index, $total_adult_count) == true) {
	         $static_date_of_birth = date('Y-m-d', strtotime('-30 years'));;
	         } else {//child
	         	$static_date_of_birth = date('Y-m-d', strtotime('-'.intval(array_shift($child_age)).' years'));;
	         }
	         $passport_number = rand(1111111111,9999999999);
	      ?>
		<input type="hidden" name="passenger_type[]" value="<?=(is_adult($pax_index, $total_adult_count) ? 1 : 2)?>">
		<input type="hidden" name="lead_passenger[]" value="<?=(is_lead_pax($pax_index) ? true : false)?>">
		<input type="hidden" name="date_of_birth[]" value="<?=$static_date_of_birth?>">
		<input type="hidden" name="gender[]" value="1" class="pax_gender">
		<input type="hidden" required="required" name="passenger_nationality[]" id="passenger-nationality-<?=$pax_index?>" value="92">
		<!-- Static Passport Details -->
		<input type="hidden" name="passenger_passport_number[]" value="<?=$passport_number?>">
        <input type="hidden" name="passenger_passport_issuing_country[]" value="<?=$passport_issuing_country?>">
        <input type="hidden" name="passenger_passport_expiry_day[]" value="<?=$static_passport_details['passenger_passport_expiry_day']?>">
        <input type="hidden" name="passenger_passport_expiry_month[]" value="<?=$static_passport_details['passenger_passport_expiry_month']?>">
        <input type="hidden" name="passenger_passport_expiry_year[]" value="<?=$static_passport_details['passenger_passport_expiry_year']?>">
	</div>
	<hr>
	<h2 class="h4 text-p">Passenger <?=$pax_index?> / <?=(is_adult($pax_index, $total_adult_count) ? 'Adult' : 'Child')?> <?=(is_lead_pax($pax_index) ? '- Lead Pax' : '')?></h2>
	<div role="passenger">
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label for="passenger-title-1">Title <sup class="text-danger">*</sup></label>
					<select class="form-control b-r-0" name="name_title[]" required>
					 <?php echo (is_adult($pax_index, $total_adult_count) ? $adult_title_options : $child_title_options)?>
					</select>
				</div>
			</div>
			<div class="col-md-10 nopadMob">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="passenger-first-name-<?=$pax_index?>">First Name <sup class="text-danger">*</sup></label>
							<input value="<?=@$cur_pax_info['first_name']?>" required="required" type="text" id="passenger-first-name-<?=$pax_index?>" class="form-control b-r-0" placeholder="First Name" name="first_name[]">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="passenger-last-name-1">Last Name <sup class="text-danger">*</sup></label>
							<input value="<?=@$cur_pax_info['last_name']?>" required="required" type="text" id="passenger-last-name-<?=$pax_index?>" class="form-control b-r-0" placeholder="Last Name" name="last_name[]">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
}//END FOR LOOP FOR PAX DETAILS
?>
							<hr>
							<h1 class="h3 m-t-0"><img src="<?=$GLOBALS['CI']->template->template_images('icons/angle-arrow-down.png')?>" alt="Angle Arrow Down Icon"> Billing Details</h1>
						<div class="hide">
		                    <input type="hidden" name="billing_country" value="92">
		                    <input type="hidden" name="billing_city" value="test">
		                    <input type="hidden" name="billing_zipcode" value="test">
		                    <input type="hidden" name="billing_address_1" value="test">
				         </div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="billing-email">Email <sup class="text-danger">*</sup></label>
										<input value="<?=@$lead_pax_details['email']?>" type="email" required="required" id="billing-email" class="form-control b-r-0" placeholder="Email" name="billing_email">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="passenger-contact">Mobile <sup class="text-danger">*</sup></label>
										<input value="<?=@$lead_pax_details['phone']?>" type="text" required="required" id="passenger-contact" class="form-control b-r-0 numeric " placeholder="Contact Number" name="passenger_contact"  maxlength="10" />
									</div>
								</div>
							</div>
							<?php
							//If single payment option then hide selection and select by default
							if (count($active_payment_options) == 1) {
								$payment_option_visibility = 'hide';
								$default_payment_option = 'checked="checked"';
							} else {
								$payment_option_visibility = 'show';
								$default_payment_option = '';
							}
							
							?>
							<div class="row <?=$payment_option_visibility?>">
							<?php if (in_array(PAY_NOW, $active_payment_options)) {?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="payment-mode-<?=PAY_NOW?>">
											<input <?=$default_payment_option?> name="payment_method" type="radio" required="required" value="<?=PAY_NOW?>" id="payment-mode-<?=PAY_NOW?>" class="form-control b-r-0" placeholder="Payment Mode">
											Pay Now
										</label>
									</div>
								</div>
							<?php } ?>
							<?php if (in_array(PAY_AT_BANK, $active_payment_options)) {?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="payment-mode-<?=PAY_AT_BANK?>">
											<input <?=$default_payment_option?> name="payment_method" type="radio" required="required" value="<?=PAY_AT_BANK?>" id="payment-mode-<?=PAY_AT_BANK?>" class="form-control b-r-0" placeholder="Payment Mode">
											Pay At Bank
										</label>
									</div>
								</div>
							<?php } ?>
							</div>
							<div class="clearfix">
								<div class="pull-right text-right col-xs-12 txtwrapRow">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="tc" required="required"> I accept the <a href="#">Terms and Conditions</a>
										</label>
									</div>
									<button type="submit" class="btn btn-t b-r-0 confirmBTN">Confirm</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');