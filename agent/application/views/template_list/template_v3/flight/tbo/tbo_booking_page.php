<?php
// debug($pre_booking_summery);exit;
$depature = explode('T', $search_data['depature']);
include_once 'process_tbo_response.php';
$template_images = $GLOBALS['CI']->template->template_images();
$FareDetails = $pre_booking_summery['FareDetails']['b2b_PriceDetails'];
$PassengerFareBreakdown = $pre_booking_summery['PassengerFareBreakdown'];
$SegmentDetails = $pre_booking_summery['SegmentDetails'];
// debug($pre_booking_summery);exit;
if($pre_booking_summery['Attr']['IsPriceChanged'] == true){
    echo price_changed();
}

//if(!isset($SegmentDetails[0][0]['OriginDetails']['date_time'])){
    $journey_start_date = date('Y-m-d',strtotime($SegmentDetails[0][0]['OriginDetails']['date_time']));
    if($journey_start_date == '1970-01-01'){
       $journey_start_date = date('Y-m-d',strtotime($SegmentDetails[0][0]['OriginDetails']['DateTime']));   
    }
  /*  $journey_start_date = date('Y-m-d',strtotime($SegmentDetails[0][0]['OriginDetails']['DateTime']));
}*/
$SegmentSummary = $pre_booking_summery['SegmentSummary'];
$hold_ticket = $pre_booking_summery['HoldTicket'];
//Total Fare
$flight_total_amount = $FareDetails['_CustomerBuying'];
$currency_symbol = $FareDetails['CurrencySymbol'];
$trip = $search_data['trip_type'];
//Segment Details
$flight_segment_details = flight_segment_details($SegmentDetails, $SegmentSummary,$trip);
$markup_limits = $markup_limits[0]['value'];

$is_domestic = $pre_booking_params['is_domestic'];
if ($is_domestic != true) {
$pass_mand = '<sup class="text-danger">*</sup>';
$pass_req = 'required="required"';
} else {
$pass_mand = '';
$pass_req = '';
}
$mandatory_filed_marker = '<sup class="text-danger">*</sup>';
//Balu A
$is_domestic_flight = $search_data['is_domestic_flight'];
if ($is_domestic_flight) {
$temp_passport_expiry_date = date('Y-m-d', strtotime('+5 years'));
$static_passport_details = array();
$static_passport_details['passenger_passport_expiry_day'] = date('d', strtotime($temp_passport_expiry_date));
$static_passport_details['passenger_passport_expiry_month'] = date('m', strtotime($temp_passport_expiry_date));
$static_passport_details['passenger_passport_expiry_year'] = date('Y', strtotime($temp_passport_expiry_date));
}
if (is_logged_in_user()) {
$review_active_class = ' success ';
$review_tab_details_class = '';
$review_tab_class = ' inactive_review_tab_marker ';
$travellers_active_class = ' active ';
$travellers_tab_details_class = ' gohel ';
$travellers_tab_class = ' travellers_tab_marker ';
} else {
$review_active_class = ' active ';
$review_tab_details_class = ' gohel ';
$review_tab_class = ' review_tab_marker ';
$travellers_active_class = '';
$travellers_tab_details_class = '';
$travellers_tab_class = ' inactive_travellers_tab_marker ';
}
$user_country_code = '+91';
// echo generate_low_balance_popup($FareDetails['_CustomerBuying']+$FareDetails['_GST']);
/*$IP = $_SERVER['REMOTE_ADDR'];
$computerName = gethostbyaddr($IP);
if($computerName == '192.168.0.59'){
    debug($pre_booking_params);die('----++-----'); 
}*/
?>
<style>
.topssec::after{display:none;}
</style>
<div class="fldealsec">
<div class="container">
<div class="tabcontnue">
<div class="col-xs-4 nopadding">
    <div class="rondsts <?= $review_active_class ?>">
        <a class="taba core_review_tab <?= $review_tab_class ?>" id="stepbk1">
            <div class="iconstatus fa fa-eye"></div>
            <div class="stausline">Review</div>
        </a>
    </div>
</div>
<div class="col-xs-4 nopadding">
    <div class="rondsts <?= $travellers_active_class ?>">
        <a class="taba core_travellers_tab <?= $travellers_tab_class ?>" id="stepbk2">
            <div class="iconstatus fa fa-group"></div>
            <div class="stausline">Travellers</div>
        </a>
    </div>
</div>
<div class="col-xs-4 nopadding">
    <div class="rondsts">
        <a class="taba" id="stepbk3">
            <div class="iconstatus fa fa-money"></div>
            <div class="stausline">Payments</div>
        </a>
    </div>
</div>
</div>
</div>
</div>
<div class="clearfix"></div>
<div class="alldownsectn">
<div class="container">
<!-- <?php if ($is_price_Changed == true) { ?>
<div class="farehd arimobold">
    <span class="text-danger">* Price has been changed from supplier end</span>
</div>
<?php } ?> -->
<div class="ovrgo">
<div class="bktab1 xlbox <?= $review_tab_details_class ?>">
    <div class="col-xs-8 nopadding full_summery_tab">
        <div class="fligthsdets">
            <div class="flitab1">
                <!-- Segment Details Starts-->
                <div class="moreflt boksectn">
                    <?php echo $flight_segment_details['segment_full_details']; ?>
                </div>
                <!-- Segment Details Ends-->
                <div class="clearfix"></div>
                <div class="sepertr"></div>
                <!--
                        <div class="promocode">
                                <div class="col-xs-6">
                                <div class="mailsign">Have a discount / promo code to redeem</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="tablesign">
                                  <div class="inputsign">
                                    <input type="text" placeholder="Enter Coupon" class="newslterinput nputbrd">
                                  </div>
                                  <div class="submitsign">
                                    <button class="promobtn">Apply</button>
                                  </div>
                               </div>
                            </div>
                        </div>
                -->
                <div class="sepertr"></div>
            </div>
        </div>
    </div>
</div> <!-- <form autocomplete="off" name="flight" id="flight_form" action="<?= base_url() . 'index.php/flight/search/' . $search_data['search_id'] ?>" method="get" class="activeForm oneway_frm" style="">
            <input type="hidden" name="trip_type" value="<?php echo $search_data['trip_type']?>">
            <input type="hidden" name="from" value="<?php echo $search_data['from_city']?>">
            <input type="hidden" name="from_loc_id" value="<?php echo $search_data['from_loc_id']?>">
            <input type="hidden" name="to" value="<?php echo $search_data['to_city']?>">
            <input type="hidden" name="to_loc_id" value="<?php echo $search_data['to_loc']?>">
            <input type="hidden" name="depature" value="<?php echo $depature[0]?>">
            
            <input type="hidden" name="adult" value="<?php echo $search_data['adult']?>">
            <input type="hidden" name="child" value="<?php echo $search_data['child']?>">
            <input type="hidden" name="infant" value="<?php echo $search_data['infant']?>">
            <input type="hidden" name="conn_direct" value="">
            <input type="hidden" name="v_class" value="<?php echo $search_data['v_class']?>">
            <input type="hidden" name="carrier" value="<?php echo $search_data['carrier']?>">
            
            <?php foreach($search_data['lcc_gds'] as $carriers){?>
                <input type="hidden" name="lcc_gds[]" value="<?php echo $carriers; ?>">
            <?php } ?>
            <input type="submit" name="search_flight" id="flight-form-submit" class="searchsbmt flight_search_btn" value="Back">

        </form> -->
<div class="bktab2 xlbox <?= $travellers_tab_details_class ?> flight_booking_desc">
    <div class="topalldesc">
       
        <div id = "ffff" class="col-xs-9 nopadding celtbcel segment_seg">
            <?php //echo flight_segment_abstract_details($pre_booking_params);
            //echo $flight_segment_details['segment_abstract_details'];
			echo $flight_segment_details['segment_full_details'];
            ?>
        </div>
        <!-- Outer Summary -->
        <div class="col-xs-3 nopadding celtbcel colrcelo">
            <div class="bokkpricesml">
                <div class="travlrs">Travellers: <span class="fa fa-male"></span> <?php echo $search_data['adult']; ?> |  <span class="fa fa-child"></span> <?php echo $search_data['child']; ?> |  <span class="infantbay"><img src="<?= $template_images ?>infant.png" alt="" /></span> <?php echo $search_data['infant']; ?></div>
                <div class="totlbkamnt"> Total Amount <?php echo $currency_symbol; ?> <span id="total_booking_amount"><?php echo round($flight_total_amount); ?></span></div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="padpaspotr">
        <div class="col-xs-8 nopadding tab_pasnger">
            <div class="fligthsdets">
                <?php
                /**
                 * Collection field name 
                 */
                //Title, Firstname, Middlename, Lastname, Phoneno, Email, PaxType, LeadPassenger, Age, PassportNo, PassportIssueDate, PassportExpDate
                $total_adult_count = is_array($search_data['adult_config']) ? array_sum($search_data['adult_config']) : intval($search_data['adult_config']);
                $total_child_count = is_array($search_data['child_config']) ? array_sum($search_data['child_config']) : intval($search_data['child_config']);
                $total_infant_count = is_array($search_data['infant_config']) ? array_sum($search_data['infant_config']) : intval($search_data['infant_config']);
                //------------------------------ DATEPICKER START
                $i = 1;
                $datepicker_list = array();
                if ($total_adult_count > 0) {
                    for ($i = 1; $i <= $total_adult_count; $i++) {
                        $datepicker_list[] = array('adult-date-picker-' . $i, FLIGHT_ADULT_DATE_PICKER);
                    }
                }

                if ($total_child_count > 0) {
                    //id should be auto picked so initialize $i to previous value of $i
                    for ($i = $i; $i <= ($total_child_count + $total_adult_count); $i++) {
                       
                        $end_date = date('d-m-Y', strtotime($journey_start_date.' -2 year'));

                        $start_date = date('d-m-Y', strtotime($journey_start_date.' -12 year'));
                       
                        $end_year = date('Y', strtotime($journey_start_date.' -2 year'));
                        $start_year = date('Y', strtotime($journey_start_date.' -12 year'));
                        $datepicker_list[] = array('child-date-picker-' . $i, FLIGHT_CHILD_DATE_PICKER, $start_date, $end_date, $start_year, $end_year);
                    }
                }

                if ($total_infant_count > 0) {
                    //id should be auto picked so initialize $i to previous value of $i
                    for ($i = $i; $i <= ($total_child_count + $total_adult_count + $total_infant_count); $i++) {
                        $iend_date = date('d-m-Y', strtotime($journey_start_date));
                        $istart_date = date('d-m-Y', strtotime($iend_date.' -2 year'));
                        $istart_date = date('d-m-Y', strtotime($istart_date.' + 1day'));
                        $datepicker_list[] = array('infant-date-picker-' . $i, FLIGHT_INFANT_DATE_PICKER, $istart_date, $iend_date);
                        /*$datepicker_list[] = array('infant-date-picker-' . $i, INFANT_DATE_PICKER);*/
                    }
                }
                $GLOBALS['CI']->current_page->set_datepicker($datepicker_list);
                //------------------------------ DATEPICKER END

                $total_pax_count = $total_adult_count + $total_child_count + $total_infant_count;
                //First Adult is Primary and and Lead Pax
                $adult_enum = $child_enum = get_enum_list('title');
                $gender_enum = get_enum_list('gender');
                unset($adult_enum[MASTER_TITLE]); // Master is for child so not required
                unset($adult_enum[MISS_TITLE]);
                unset($adult_enum[A_MASTER]); // Mstr is for child so not required
                //unset($child_enum[MASTER_TITLE]); // Master is not supported in TBO list
                unset($child_enum[MR_TITLE]);
                //unset($child_enum[MRS_TITLE]);
                unset($child_enum[C_MRS_TITLE]);
                unset($child_enum[MRS_TITLE]);
                unset($child_enum[MASTER_TITLE]);
                $adult_title_options = generate_options($adult_enum, false, true);
                $child_title_options = generate_options($child_enum, false, true);

                $gender_options = generate_options($gender_enum);
                $nationality_options = generate_options($iso_country_list, array(INDIA_CODE)); //FIXME get ISO CODE --- ISO_INDIA
                $passport_issuing_country_options = generate_options($country_list);

                if ($search_data['trip_type'] == 'oneway') {
                    $passport_minimum_expiry_date = date('Y-m-d', strtotime($search_data['depature']));
                } else if ($search_data['trip_type'] == 'circle') {
                    //debug($search_data);exit;
                    $passport_minimum_expiry_date = date('Y-m-d', strtotime($search_data['return']));
                } else {
                    $passport_minimum_expiry_date = date('Y-m-d', strtotime(end($search_data['depature'])));
                }
                //$passport_minimum_expiry_date = date('Y-m-d', strtotime('2018-01-01'));
                //lowest year wanted
                $cutoff = date('Y', strtotime('+10 years', strtotime($passport_minimum_expiry_date)));
                //current year
                //$now = date('Y');
                $now = date('Y', strtotime($passport_minimum_expiry_date));
                $day_options = generate_options(get_day_numbers());
                $month_options = generate_options(get_month_names());
                $year_options = generate_options(get_years($now, $cutoff));

                /**
                 * check if current print index is of adult or child by taking adult and total pax count
                 * @param number $total_pax     total pax count
                 * @param number $total_adult   total adult count
                 */
                function pax_type($pax_index, $total_adult, $total_child, $total_infant) {
                    if ($pax_index <= $total_adult) {
                        $pax_type = 'adult';
                    } elseif ($pax_index <= ($total_adult + $total_child)) {
                        $pax_type = 'child';
                    } else {
                        $pax_type = 'infant';
                    }
                    return $pax_type;
                }

                /**
                 * check if current print index is of adult or child by taking adult and total pax count
                 * @param number $total_pax     total pax count
                 * @param number $total_adult   total adult count
                 */
                function is_adult($pax_index, $total_adult) {
                    return ($pax_index > $total_adult ? false : true);
                }

                function pax_type_count($pax_index, $total_adult, $total_child, $total_infant) {
                    if ($pax_index <= $total_adult) {
                        $pax_count = ($pax_index);
                    } elseif ($pax_index <= ($total_adult + $total_child)) {
                        $pax_count = ($pax_index - $total_adult);
                    } else {
                        $pax_count = ($pax_index - ($total_adult + $total_child));
                    }
                    return $pax_count;
                }

                /**
                 * check if current print index is of adult or child by taking adult and total pax count
                 * @param number $total_pax     total pax count
                 * @param number $total_adult   total adult count
                 */
                function is_lead_pax($pax_count) {
                    return ($pax_count == 1 ? true : false);
                }
                ?>
                <form action="<?= base_url() . 'index.php/flight/pre_booking/' . $search_data['search_id'] ?>" method="POST" autocomplete="off" id="pre-booking-form">
                    <div class="hide">
                        <input type="hidden" required="required" name="search_id"       value="<?= $search_data['search_id']; ?>" />
                        <?php $dynamic_params_url = serialized_data($pre_booking_params); ?>
                        <input type="hidden" required="required" name="token"       value="<?= $dynamic_params_url; ?>" />
                        <input type="hidden" required="required" name="token_key"   value="<?= md5($dynamic_params_url); ?>" />
                        <input type="hidden" required="required" name="op"          value="book_room">
                        <input type="hidden" required="required" name="booking_source"      value="<?= $booking_source ?>" readonly>
                        <!--<input type="hidden" required="required" name="provab_auth_key" value="?=$ProvabAuthKey ?>" readonly>
                        --></div>
                    <div class="flitab1">
                        <div class="moreflt boksectn">
                            <div class="ontyp">
                                <?php if($is_domestic == true){
                                ?>
                                    <div class="labltowr arimobold">Please enter names as on identity proof</div>   
                                <?php    
                                }else{
                                ?>
                                    <div class="labltowr arimobold">Please enter names as on passport</div>
                                <?php    
                                } 
                                $pax_index = 1;
                                  
                                $lead_pax_details = @$pax_details[0];
                              
                                if (is_logged_in_user()) {
                                    //Can Enable this for B2B
                                    //$traveller_class = ' user_traveller_details ';
                                    $traveller_class = '';
                                } else {
                                    $traveller_class = '';
                                }
                                for ($pax_index = 1; $pax_index <= $total_pax_count; $pax_index++) {//START FOR LOOP FOR PAX DETAILS
                                    $cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
                                    $pax_type = pax_type($pax_index, $total_adult_count, $total_child_count, $total_infant_count);
                                    $pax_type_count = pax_type_count($pax_index, $total_adult_count, $total_child_count, $total_infant_count);

                                    if ($pax_type != 'infant') {
                                        $extract_pax_name_cls = ' extract_pax_name_cls ';
                                    } else {
                                        $extract_pax_name_cls = '';
                                    }
                                    ?>
                                    <div class="pasngr_input pasngrinput _passenger_hiiden_inputs">
                                        <div class="hide hidden_pax_details">
                                            <input type="hidden" class="pax_type_for_popup" name="passenger_type[]" value="<?= ucfirst($pax_type) ?>">
                                            <input type="hidden" name="lead_passenger[]" value="<?= (is_lead_pax($pax_index) ? true : false) ?>">
                                            <input type="hidden" name="gender[]" value="1" class="pax_gender">
                                            <input type="hidden" required="required" name="passenger_nationality[]" id="passenger-nationality-<?= $pax_index ?>" value="92">
                                        </div>
                                        <div class="col-xs-1 nopadding full_dets_aps">
                                            <div class="adltnom"><?= ucfirst($pax_type) ?><?= $pax_type_count ?><?= $mandatory_filed_marker ?></div>
                                        </div>
                                        <div class="col-xs-11 nopadding full_dets_aps">
                                            <div class="inptalbox">
                                                <div class="col-xs-3 spllty">
                                                    <div class="selectedwrap">
                                                        <select class="mySelectBoxClass flyinputsnor name_title" name="name_title[]" required="required">
							    <option value="">Title</option>
                                                            <?php echo (is_adult($pax_index, $total_adult_count) ? $adult_title_options : $child_title_options) ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-5 spllty">
                                                    <input value="" required="required" type="text" name="first_name[]" id="passenger-first-name-<?= $pax_index ?>" class="<?= $extract_pax_name_cls ?> clainput alpha_space <?= $traveller_class ?>" maxlength="45" placeholder="Enter First Name" data-row-id="<?= ($pax_index); ?>"/>
                                                </div>
                                                <div class="col-xs-4 spllty">
                                                    <input value="" required="required" type="text" name="last_name[]" id="passenger-last-name-<?= $pax_index ?>" class="<?= $extract_pax_name_cls ?> clainput alpha_space" maxlength="45" placeholder="Enter Last Name" />
                                                </div>
                                                <?php if ($pax_type == 'infant') {//Only For Infant ?>
                                                    <div class="col-xs-6 spllty infant_dob_div">
                                                        <div class="col-xs-4 nopadding"><span class="fmlbl">Date of Birth <?= $mandatory_filed_marker ?></span></div>
                                                        <div class="col-xs-8 nopadding">
                                                            <input placeholder="DOB" type="text" class="clainput"  name="date_of_birth[]" readonly="readonly" <?= (is_adult($pax_index, $total_adult_count) ? 'required="required"' : 'required="required"') ?> id="<?= strtolower(pax_type($pax_index, $total_adult_count, $total_child_count, $total_infant_count)) ?>-date-picker-<?= $pax_index ?>">
                                                        </div>
                                                    </div>
                                                <?php
                                                }  else{ //Adult/Child
                                        if(($pax_type == 'adult' && $is_domestic_flight == false) ) {  ?> 
                                            <div class="col-xs-6 spllty infant_dob_div">
                                                <div class="col-xs-4 nopadding"><span class="fmlbl">Date of Birth <?=$mandatory_filed_marker?></span></div>
                                                <div class="col-xs-8 nopadding">
                                                    <input placeholder="DOB" type="text" class="clainput"  name="date_of_birth[]" readonly <?=(is_adult($pax_index, $total_adult_count) ? 'required="required"' : 'required="required"')?> id="<?=strtolower(pax_type($pax_index, $total_adult_count, $total_child_count, $total_infant_count))?>-date-picker-<?=$pax_index?>">
                                                </div>
                                            </div>
                                            <?php } else if(($pax_type == 'child')) { ?>
                                                <div class="col-xs-6 spllty infant_dob_div">
                                                <div class="col-xs-4 nopadding"><span class="fmlbl">Date of Birth <?=$mandatory_filed_marker?></span></div>
                                                <div class="col-xs-8 nopadding">
                                                    <input placeholder="DOB" type="text" class="clainput"  name="date_of_birth[]" readonly <?=(is_adult($pax_index, $total_adult_count) ? 'required="required"' : 'required="required"')?> id="<?=strtolower(pax_type($pax_index, $total_adult_count, $total_child_count, $total_infant_count))?>-date-picker-<?=$pax_index?>">
                                                </div>
                                            </div>
                                            <?php } 
                                                else{
                                                    $static_date_of_birth = date('Y-m-d', strtotime('-30 years'));
                                                    ?>
                                                     <div class="adult_child_dob_div hide">
                                                    <input type="hidden" name="date_of_birth[]" value="<?=$static_date_of_birth?>">
                                                </div>
                                                <?php }
                                        } ?>
             
                                            
                                                <div class="clearfix"></div>
                                                <!-- Passport Section Starts -->
                                                <div class="passport_content_div">
<?php if ($is_domestic_flight == false) { //For Internatinal Travel ?>
                                                        <div class="international_passport_content_div">
                                                            <div class="col-xs-4 spllty">
                                                                <span class="formlabel">Passport Number <?= $pass_mand ?></span>
                                                                <div class="relativemask"> 
                                                                    <input type="text" name="passenger_passport_number[]" <?= $pass_req ?> id="passenger_passport_number_<?= $pax_index ?>" class="clainput" maxlength="10" placeholder="Passport Number" />
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 spllty">
                                                                <span class="formlabel">Issued Country <?= $pass_mand ?></span>
                                                                <div class="selectedwrap">
                                                                    <select name="passenger_passport_issuing_country[]" <?= $pass_req ?> id="passenger_passport_issuing_country_<?= $pax_index ?>" class="mySelectBoxClass flyinputsnor">
                                                                        <option value="INVALIDIP">Please Select</option>
<?= $passport_issuing_country_options ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-5 spllty">
                                                                <span class="formlabel">Date of Expire <?= $pass_mand ?></span>
                                                                <div class="relativemask">
                                                                    <div class="col-xs-4 splinmar">
                                                                        <div class="selectedwrap">
                                                                            <select name="passenger_passport_expiry_day[]" <?= $pass_req ?> class="mySelectBoxClass flyinputsnor passport_expiry_day" data-expiry-type="day" id="passenger_passport_expiry_day_<?= $pax_index ?>" data-row-id="<?= ($pax_index); ?>">
                                                                                <option value="INVALIDIP">DD</option>
<?= $day_options; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-4 splinmar">
                                                                        <div class="selectedwrap">
                                                                            <select name="passenger_passport_expiry_month[]" <?= $pass_req ?> class="mySelectBoxClass flyinputsnor passport_expiry_month" data-expiry-type="month" id="passenger_passport_expiry_month_<?= $pax_index ?>" data-row-id="<?= ($pax_index); ?>">
                                                                                <option value="INVALIDIP">MM</option>
<?= $month_options; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-4 splinmar">
                                                                        <div class="selectedwrap">
                                                                            <select name="passenger_passport_expiry_year[]" <?= $pass_req ?> class="mySelectBoxClass flyinputsnor passport_expiry_year" data-expiry-type="year" id="passenger_passport_expiry_year_<?= $pax_index ?>" data-row-id="<?= ($pax_index); ?>">
                                                                                <option value="INVALIDIP">YYYY</option>
<?= $year_options; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="pull-right text-danger hide" id="passport_error_msg_<?= $pax_index ?>"></div>
                                                        </div>
                                                    <?php
                                                    } else { //For Domestic Travel, Set Static Passport Data
                                                        $passport_number = rand(1111111111, 9999999999);
                                                        $passport_issuing_country = 92;
                                                        ?>
                                                        <div class="domestic_passport_content_div hide">
                                                            <input type="hidden" name="passenger_passport_number[]" value="<?= $passport_number ?>" id="passenger_passport_number_<?= $pax_index ?>">
                                                            <input type="hidden" name="passenger_passport_issuing_country[]" value="<?= $passport_issuing_country ?>" id="passenger_passport_issuing_country_<?= $pax_index ?>">
                                                            <input type="hidden" name="passenger_passport_expiry_day[]" value="<?= $static_passport_details['passenger_passport_expiry_day'] ?>" id="passenger_passport_expiry_day_<?= $pax_index ?>">
                                                            <input type="hidden" name="passenger_passport_expiry_month[]" value="<?= $static_passport_details['passenger_passport_expiry_month'] ?>" id="passenger_passport_expiry_month_<?= $pax_index ?>">
                                                            <input type="hidden" name="passenger_passport_expiry_year[]" value="<?= $static_passport_details['passenger_passport_expiry_year'] ?>" id="passenger_passport_expiry_year_<?= $pax_index ?>">
                                                        </div>
<?php } ?>
                                                </div>
                                                <!-- Passport Section Ends-->
                                            </div>
                                        </div>
                                    </div>
<?php
}//END FOR LOOP FOR PAX DETAILS
?>
                            </div>
                        </div>

                        <div class="sepertr"></div>
                        <div class="clearfix"></div>
                        <div class="contbk">
                            <div class="contcthdngs">CONTACT DETAILS</div>
                            <div class="col-xs-12 nopad full_smal_forty">
                                <div class="col-xs-12 nopad mb10 full_smal_forty">
                                    <div class="col-xs-3 nopadding">
                                        <div class="hide">
                                            <input type="hidden" name="billing_country" value="92">
                                            <input type="hidden" name="billing_city" value="test">
                                            <input type="hidden" name="billing_zipcode" value="test">
                                        </div>
                                        <select class="newslterinput nputbrd _numeric_only " >
<?php echo diaplay_phonecode($phone_code, $active_data,$user_country_code); ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-1">
                                        <div class="sidepo">-</div>
                                    </div>
                                    <?php //@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone']; ?>
                                    <div class="col-xs-8 nopadding">
                                        <input value="" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only" maxlength="10" required="required">
                                    </div>
                                </div>

                                <div class="emailperson col-xs-12 nopad full_smal_forty">
                                    <input value="<?= @$lead_pax_details['email'] ?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
                                </div>

                                <div class="clearfix"></div>

                                <div class="emailperson col-xs-12 nopad full_smal_forty">
                                    <textarea rows="3" name="billing_address_1" class="newsltertextarea nputbrd" placeholder="Address" required="required"><?= @$agent_address ?></textarea>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="notese">Mobile number & Email ID will be used only for sending flight related communication.</div>
                        </div>
                        <div class="panel-group" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default for_gst flight_special_req">
                            <div class="panel-heading" role="tab" id="gst_opt">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#gst_optnl" aria-expanded="true" aria-controls="gst_optnl">
                                        
                                        <div class="labltowr arimobold">GST Information(Optional) <i class="more-less glyphicon glyphicon-plus"></i></div>
                                    </a>
                                </h4>
                            </div>
                            <div id="gst_optnl" class="panel-collapse collapse" role="tabpanel" aria-labelledby="gst_opt">
                           <!-- <div class="contcthdngs">GST Information(Optional)</div> -->
                            <div class="col-xs-12 gst_det" id="gst_form_div">
                               <div class="row">
                                  <div class="col-xs-3"> GST Number </div>
                                  <div class="col-xs-7"> 
                                     <input type="text" class="newslterinput clainput nputbrd" id="gst_number" name="gst_number" value="">    
                                  </div>
                               </div>
                               <div class="row">
                                  <div class="col-xs-3"> GST company Name </div>
                                  <div class="col-xs-7"> 
                                     <input type="text" class="newslterinput nputbrd" id="gst_company_name" name="gst_company_name" vaule="">    
                                  </div>
                               </div>
                               <div class="row">
                                  <div class="col-xs-3"> Email </div>
                                  <div class="col-xs-7"> 
                                     <input type="email" class="newslterinput nputbrd" id="gst_email" name="gst_email" value="">    
                                  </div>
                               </div>                                          
                               <div class="row">
                                  <div class="col-xs-3"> Phone Number </div>
                                  <div class="col-xs-7"> 
                                     <input type="text" class="newslterinput nputbrd _numeric_only" id="gst_phone" name="gst_phone" maxlength="10" value="">    
                                  </div>
                               </div>
                               <div class="row">
                                  <div class="col-xs-3"> Address </div>
                                  <div class="col-xs-7"> 
                                     <input type="text" class="newslterinput nputbrd" name="gst_address" id="gst_address" value="">    
                                  </div>
                               </div>
                               <div class="row">
                                  <div class="col-xs-3"> State </div>
                                  <div class="col-xs-7">
                                  <?php $state_list = generate_options($state_list);?>
                                  <select name="gst_state" class="clainput" id="gststate">
                                        <option value="INVALIDIP">Please Select</option>
                                        <?=$state_list?>
                                    </select>
                                    
                                  </div>
                               </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        
                    </div>
                <!-- </form> -->
            </div>
        </div>
        <div class="col-xs-4 nopadding rit_summery">
<?php echo get_fare_summary($FareDetails, $PassengerFareBreakdown, $booking_source); ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12 seat_detail">
            <div class="clikdiv">
                            <div class="squaredThree">
                                <input id="terms_cond1" type="checkbox" name="tc" checked="checked" required="required">
                                <label for="terms_cond1"></label>
                            </div>
                            <span class="labltowr arimobold" id="clikagre" data-toggle="modal" data-target="#terms-conditions" style="cursor: pointer;">
                                Terms and Conditions
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="sepertr"></div>
                        <div class="clearfix"></div>
                        <!-- Dyanamic Baggage&Meals Section Starts -->
                        <?php
                        if (valid_array($extra_services) == true) {
                            if (isset($extra_services['ExtraServiceDetails']['Baggage'])) {
                                $baggage_meal_seat_details['baggage_meal_details']['Baggage'] = $extra_services['ExtraServiceDetails']['Baggage'];
                            }
                            if (isset($extra_services['ExtraServiceDetails']['Meals'])) {
                                $baggage_meal_seat_details['baggage_meal_details']['Meals'] = $extra_services['ExtraServiceDetails']['Meals'];
                            }
                            if (isset($extra_services['ExtraServiceDetails']['Seat'])) {
                                $baggage_meal_seat_details['baggage_meal_details']['Seat'] = $extra_services['ExtraServiceDetails']['Seat'];
                            }
                            $baggage_meal_seat_details['total_adult_count'] = $total_adult_count;
                            $baggage_meal_seat_details['total_child_count'] = $total_child_count;
                            $baggage_meal_seat_details['total_infant_count'] = $total_infant_count;
                            $baggage_meal_seat_details['total_pax_count'] = $total_pax_count;
                            echo $GLOBALS['CI']->template->isolated_view('flight/dynamic_baggage_meal_seat_details', $baggage_meal_seat_details);
                        }
                        ?>
                        <!-- Dyanamic Baggage&Meals Section Ends -->
                        <!-- Seats&Meals Preference Section Starts -->
                        <?php
                        if (valid_array($extra_services) == true) {
                            if (isset($extra_services['ExtraServiceDetails']['MealPreference'])) {
                                $seat_meal_preference_details['seat_meal_preference_details']['MealPreference'] = $extra_services['ExtraServiceDetails']['MealPreference'];
                            }
                            if (isset($extra_services['ExtraServiceDetails']['SeatPreference'])) {
                                $seat_meal_preference_details['seat_meal_preference_details']['SeatPreference'] = $extra_services['ExtraServiceDetails']['SeatPreference'];
                            }
                            $seat_meal_preference_details['total_adult_count'] = $total_adult_count;
                            $seat_meal_preference_details['total_child_count'] = $total_child_count;
                            $seat_meal_preference_details['total_infant_count'] = $total_infant_count;
                            $seat_meal_preference_details['total_pax_count'] = $total_pax_count;
                            echo $GLOBALS['CI']->template->isolated_view('flight/seat_meal_preference_details', $seat_meal_preference_details);
                        }
                        ?>
                        <!-- Seats&Meals Preference Section Ends -->
                        <div class="clearfix"></div>
                        <div class="loginspld">
                            <div class="collogg">
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
                                <div class="row <?= $payment_option_visibility ?>">
<?php if (in_array(PAY_NOW, $active_payment_options)) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="payment-mode-<?= PAY_NOW ?>">
                                                    <input <?= $default_payment_option ?> name="payment_method" type="radio" required="required" value="<?= PAY_NOW ?>" id="payment-mode-<?= PAY_NOW ?>" class="form-control b-r-0" placeholder="Payment Mode">
                                                    Pay Now
                                                </label>
                                            </div>
                                        </div>
<?php } ?>
<?php if (in_array(PAY_AT_BANK, $active_payment_options)) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="payment-mode-<?= PAY_AT_BANK ?>">
                                                    <input <?= $default_payment_option ?> name="payment_method" type="radio" required="required" value="<?= PAY_AT_BANK ?>" id="payment-mode-<?= PAY_AT_BANK ?>" class="form-control b-r-0" placeholder="Payment Mode">
                                                    Pay At Bank
                                                </label>
                                            </div>
                                        </div>
                                <?php } ?>
                                </div>
                                <input type="hidden" name="ticket_method" value="" id="ticket_method" />
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
<input type="hidden" id="markup_copy" style="width:100px">
<?php if ($hold_ticket == true) { ?>
                                    <div class="continye col-sm-3 col-xs-6">
                                        <button type="submit" id="" name="flight" value="direct_ticket"class="continue_booking_button ticket_type_cls bookcont">Confirm Ticket</button>
                                    </div>
                                    <div class="continye col-sm-3 col-xs-6">
                                        <button type="submit" id="" name="flight" value="hold_ticket"class="continue_booking_button ticket_type_cls book_hold_ticket">Hold Ticket</button>
                                    </div>
<?php } else {
?>
                                    <div class="continye col-sm-3 col-xs-6">
                                        <button type="submit" id="" name="flight" class="bookcont continue_booking_button">Confirm Ticket</button>
                                    </div>
<?php } ?>

                                <div class="clearfix"></div>
                                <div class="sepertr"></div>
                                <div class="temsandcndtn">
                                     <?php 
                                    if($is_domestic == false){
                                    ?>  Most countries require travellers to have a passport valid for more than 6 months from the date of entry into or exit from the country. Please check the exact rules for your destination country before completing the booking. <?php    
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
        </div></form>
    </div>
</div>
</div>
</div>
</div>
<span class="hide">
<input type="hidden" id="pri_passport_min_exp" value="<?= $passport_minimum_expiry_date ?>">
</span>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="terms-conditions" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Terms & Conditions</h4>
        </div>
        <div class="modal-body">
          <ol>
            <li> Passenger has to report 2 hours prior for departure.</li>
            <li> Incase passenger cancels the ticket refund will be processed as per the airline policy.</li>
            <li> Passenger details to be entered as per the identity proof, after confirmation name correction is not allowed.</li>
          </ol>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>

<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_session_expiry_popup'); ?>
<?php echo $GLOBALS['CI']->template->isolated_view('share/passenger_confirm_popup'); ?>
<?php
/*
* Balu A
* Flight segment details
* Outer summary and Inner Summary
*/

function flight_segment_details($SegmentDetails, $SegmentSummary,$trip='') {
$loc_dir_icon = '<span class="fadr fa fa-long-arrow-right textcntr"></span>';
$inner_summary = $outer_summary = '';
//Inner Summary
foreach ($SegmentDetails as $__segment_k => $__segment_v) {
$segment_summary = $SegmentSummary[$__segment_k];
//Calculate Total Duration of Onward/Return Journey
$inner_summary .= '<div class="ontyp">';
//Way Summary in one line - Start
$inner_summary .= '<div class="labltowr arimobold">';
$inner_summary .= $segment_summary['OriginDetails']['CityName'] . ' to ' . $segment_summary['DestinationDetails']['CityName'] . '<strong>(' . $segment_summary['TotalDuaration'] . ')</strong>';
$inner_summary .= '</div>';
//Way Summary in one line - End
foreach ($__segment_v as $__stop => $__segment_flight) {
//Summary of Way - Start
$inner_summary .= '<div class="allboxflt">';
//airline
$inner_summary .= '<div class="col-xs-3 nopadding width_adjst">
                        <div class="jetimg">
                        <img  alt="' . $__segment_flight['AirlineDetails']['AirlineCode'] . '" src="' . SYSTEM_IMAGE_DIR . 'airline_logo/' . $__segment_flight['AirlineDetails']['AirlineCode'] . '.gif" >
                        </div>
                        <div class="alldiscrpo">
                        ' . $__segment_flight['AirlineDetails']['AirlineName'] . '
                        <span class="sgsmal">' . $__segment_flight['AirlineDetails']['AirlineCode'] . ' 
                        <br />' . $__segment_flight['AirlineDetails']['FlightNumber'] . '</span>
                        </div>
                      </div>';
//depart
$inner_summary .= '<div class="col-xs-7 nopadding width_adjst">';
$inner_summary .= '<div class="col-xs-5">
                        <span class="airlblxl">' . month_date_time($__segment_flight['OriginDetails']['DateTime']) . '</span>
                        <span class="portnme">' . $__segment_flight['OriginDetails']['AirportName'] . '</span>
                        <span class="portnme">' . $__segment_flight['OriginDetails']['CityName'] . '</span>
                        </div>';
//direction indicator
$inner_summary .= '<div class="col-xs-2">
    ' . $loc_dir_icon . '</div>';
//arrival
$inner_summary .= '<div class="col-xs-5">
                        <span class="airlblxl">' . month_date_time($__segment_flight['DestinationDetails']['DateTime']) . '</span>
                        <span class="portnme">' . $__segment_flight['DestinationDetails']['AirportName'] . '</span>
                        <span class="portnme">' . $__segment_flight['DestinationDetails']['CityName'] . '</span>
                        </div>';
$inner_summary .= '</div>';

//Between Content -----
$inner_summary .= '<div class="col-xs-2 nopadding width_adjst">
                    <span class="portnme textcntr hr_val">' . $__segment_flight['SegmentDuration'] . '</span>
                    <!--<span class="portnme textcntr">Stop : ' . ($__stop) . '</span>-->
                    </div>';
//Summary of Way - End
$inner_summary .= '</div>';
if (isset($__segment_v['WaitingTime']) == true) {
    $next_seg_info = $seg_v[$seg_details_k + 1];
    $waiting_time = $__segment_v['WaitingTime'];
    $inner_summary .= '
<div class="clearfix"></div>
<div class="connectnflt">
    <div class="conctncentr">
    <span class="fa fa-plane"></span>Change of planes at ' . $next_seg_info['OriginDetails']['AirportName'] . ' | <span class="fa fa-clock-o"></span> Waiting : ' . $waiting_time . '
</div>
</div>
<div class="clearfix"></div>';
}
}
$inner_summary .= '</div>';
}
//Outer Summry
$total_stop_count = 0;
$outer_summary .= '<div class="moreflt spltopbk">';

$count = 0;
foreach ($SegmentSummary as $__segment_k => $__segment_v) {
$total_segment_travel_duration = $__segment_v['TotalDuaration'];
$__stop_count = $__segment_v['TotalStops'];
$total_stop_count += $__stop_count;
$outer_summary .= '<div class="ontypsec">
            <div class="allboxflt">';
//$outer_summary .='<div>aaa</div>';          
//airline
$seg = '';
if(($count == 0) && ($trip == 'circle')){
    $seg = 'Onward Trip';
}else if(($count > 0) && ($trip == 'circle')){
    $seg = 'Return Trip';
}            
$outer_summary .= '
                <div><h4>'.$seg.'</h4></div>    
                <div class="col-xs-3 nopadding width_adjst">
                <div class="jetimg">
                <img class="airline-logo" alt="' . $__segment_v['AirlineDetails']['AirlineCode'] . '" src="' . SYSTEM_IMAGE_DIR . 'airline_logo/' . $__segment_v['AirlineDetails']['AirlineCode'] . '.gif">
                </div>
                <div class="alldiscrpo">
                        ' . $__segment_v['AirlineDetails']['AirlineName'] . '
                        <span class="sgsmal"> ' . $__segment_v['AirlineDetails']['AirlineCode'] . '<br />' . $__segment_v['AirlineDetails']['FlightNumber'] . '</span>
                </div>
              </div>';
$outer_summary .= '<div class="col-xs-7 nopadding width_adjst">';
//depart
$outer_summary .= '<div class="col-xs-5">
                        <span class="airlblxl">' . $__segment_v['OriginDetails']['AirportName'] . '</span>
                        <span class="portnme">' . month_date_time($__segment_v['OriginDetails']['DateTime']) . '</span>
                        <span class="portnme">' .$__segment_v['OriginDetails']['CityName'] . '</span>
                </div>';
//direction indicator
$outer_summary .= '<div class="col-xs-2"><span class="fadr fa fa-long-arrow-right textcntr"></span></div>';
//arrival
$outer_summary .= '<div class="col-xs-5">
                        <span class="airlblxl">' . $__segment_v['DestinationDetails']['AirportName'] . '</span>
                        <span class="portnme">' . month_date_time($__segment_v['DestinationDetails']['DateTime']) . '</span>
                        <span class="portnme">' .$__segment_v['DestinationDetails']['CityName'].'</span>
                    </div>
                    </div>';
//Stops/Class details
$outer_summary .= '<div class="col-xs-2 nopadding width_adjst">
                    <span class="portnme textcntr">' . ($total_segment_travel_duration) . '</span>
                    <span class="portnme textcntr" >Stop:' . ($__stop_count) . '</span>
            </div>';
$outer_summary .= '</div></div>';
$count++;
}
$outer_summary .= '</div>';
return array('segment_abstract_details' => $outer_summary, 'segment_full_details' => $inner_summary);
}

function get_fare_summary($FareDetails, $PassengerFareBreakdown, $booking_source) {
// debug($FareDetails);exit;
$adult_fare_det = $PassengerFareBreakdown["ADT"];
$adt_count = $adult_fare_det["Count"];
if(($booking_source == TRAVELPORT_ACH_BOOKING_SOURCE) || ($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE))
    $single_adt_bf = $adult_fare_det["BaseFare"];
else
    $single_adt_bf = $adult_fare_det["BaseFare"]/$adt_count;

if(isset($PassengerFareBreakdown["CHD"]))
{
    $child_fare_det = $PassengerFareBreakdown["CHD"];
    $chd_count = $child_fare_det["Count"];
    if(($booking_source == TRAVELPORT_ACH_BOOKING_SOURCE) || ($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE))
        $single_chd_bf = $child_fare_det["BaseFare"];
    else
        $single_chd_bf = $child_fare_det["BaseFare"]/$chd_count;
}
if(isset($PassengerFareBreakdown["INF"]))
{
    $infant_fare_det = $PassengerFareBreakdown["INF"];
    $inf_count = $infant_fare_det["Count"];
    if(($booking_source == TRAVELPORT_ACH_BOOKING_SOURCE) || ($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE))
        $single_inf_bf = $infant_fare_det["BaseFare"];
        if($infant_fare_det["Tax"] > 0){
            $single_inf_tax = $infant_fare_det["Tax"];
        }
    else
        $single_inf_bf = $infant_fare_det["BaseFare"]/$inf_count;
        if($infant_fare_det["Tax"] > 0){
            $single_inf_tax = $infant_fare_det["Tax"]/$inf_count;
        }
}
$total_payable = $FareDetails['_TotalPayable'];
$total_published_fare = $FareDetails['_CustomerBuying'] - $FareDetails['_GST'];
$grand_total_to_show = $FareDetails['_CustomerBuying'];
$currency_symbol = $FareDetails['CurrencySymbol'];
$gst_data ='';
$gst_data1 = '';
if($FareDetails['_GST'] > 0){
$gst_data = '<div class="col-xs-8 nopadding">
            <div class="faresty">GST</div>
            </div>
        <div class="col-xs-4 nopadding">
            <div class="amnter arimobold">'.$currency_symbol.' <span id="markup_gst">'.$FareDetails['_GST'].'</span>
                <input type="hidden" id="markup_gst_copy" value="'.$FareDetails['_GST'].'">

            </div>
        </div>
        ';
$gst_data1 = '<div class="reptallt">
                            <div class="col-xs-8 nopadding">
                                <div class="faresty">GST</div>
                            </div>

                            <div class="col-xs-4 nopadding">
                                <div class="amnter arimobold">+' . $FareDetails['_GST'] . '
                                </div>
                            </div>
                            </div>';
}
$fare_summary = '<div class="insiefare">
        <div class="farehd arimobold">Fare Summary</div>
        <div class="fredivs">';
$hide_show_fare_details = '<div class="kindrest">
                                <a class="freshd show_details btn btn-sm pull-left" id="hide_show_net_fare" data-toggle="collapse" href="#net_fare_details" aria-expanded="false" aria-controls="net_fare_details">
                                +SNF
                                </a>
                                </div>';
$pax_base_fare_details = '<div class="kindrest">
                    <div class="freshd">Base Fare</div>';
$pax_base_fare_details .= '<div class="reptallt">
		                    <div class="col-xs-8 nopadding">
		                        <div class="faresty">Adult Base Fare</div>
		                    </div>
		                    <div class="col-xs-4 nopadding">
		                        <div class="amnter arimobold">'.$adt_count.' X '.$cur_Currency.' '.roundoff_number($single_adt_bf).'</div>
		                    </div>
	                      </div>';
if(isset($child_fare_det['BaseFare'])){
	$pax_base_fare_details .= '<div class="reptallt">
			                    <div class="col-xs-8 nopadding">
			                        <div class="faresty">Child Base Fare</div>
			                    </div>
			                    <div class="col-xs-4 nopadding">
			                        <div class="amnter arimobold">'.$chd_count.' X '.$cur_Currency.' '.roundoff_number($single_chd_bf).'</div>
			                    </div>
		                      </div>';
}	
if(isset($infant_fare_det['BaseFare']) && $infant_fare_det["BaseFare"]!=0){
    $pax_base_fare_details .= '<div class="reptallt">
                                <div class="col-xs-8 nopadding">
                                    <div class="faresty">Infant Base Fare</div>
                                </div>
                                <div class="col-xs-4 nopadding">
                                    <div class="amnter arimobold">'.$inf_count.' X '.$cur_Currency.' '.roundoff_number($single_inf_bf).'</div>
                                </div>
                              </div>';
}  
if(isset($infant_fare_det['Tax']) && $infant_fare_det["Tax"]!=0){
    $pax_base_fare_details .= '<div class="reptallt">
                                <div class="col-xs-8 nopadding">
                                    <div class="faresty">Infant Tax</div>
                                </div>
                                <div class="col-xs-4 nopadding">
                                    <div class="amnter arimobold">'.$inf_count.' X '.$cur_Currency.' '.roundoff_number($single_inf_tax).'</div>
                                </div>
                              </div>';
}                                          
$pax_base_fare_details .= '<div class="reptallt">
			                    <div class="col-xs-8 nopadding">
			                        <div class="faresty">Total Base Fare</div>
			                    </div>
			                    <div class="col-xs-4 nopadding">
			                        <div class="amnter arimobold">' . $currency_symbol . ' ' . $FareDetails['_BaseFare'] . '</div>
				                </div>
	                		</div>';
$pax_tax_details = '<div class="kindrest">
                        <div class="freshd">Taxes</div>';
$pax_tax_details .= '<div class="reptallt">
                    <div class="col-xs-8 nopadding">
                        <div class="faresty">Taxes & Fees</div>
                    </div>
                    <div class="col-xs-4 nopadding">
                        <div class="amnter arimobold">' . $currency_symbol . ' ' . ($FareDetails['_TaxSum']- $FareDetails['_GST']-$infant_fare_det['Tax']) . ' </div>
                    </div>
                </div>'.$gst_data;
$pax_base_fare_details .= '</div>';
$pax_tax_details .= '</div>';

$extar_service_charge_details = '<div class="">';
$extar_service_charge_details .= '<div class="baggagecharge-agent" id="extra_baggage_charge_label" style="display:none">
                                            <div class="col-xs-8 nopadding">Extra Baggage Charge</div>
                                            <div class="col-xs-4 nopadding text-right">' . $currency_symbol . ' 
                                                <span class="amnter arimobold" id="extra_baggage_charge"></span>
                                                <span class="btn btn-sm btn-default" id="remove_extra_baggage"><i class="fa fa-times" aria-hidden="true"></i></span>
                                                </div>
                                        </div>
                                        <div class="baggagecharge-agent" id="extra_meal_charge_label" style="display:none">
                                            <div class="col-xs-8 nopadding">Meal Charge</div>
                                                <div class="col-xs-4 nopadding text-right">' . $currency_symbol . '
                                                <span class="amnter arimobold" id="extra_meal_charge"></span>
                                                <span class="btn btn-sm btn-default" id="remove_extra_meal"><i class="fa fa-times" aria-hidden="true"></i></span>
                                                </div>
                                        </div>
                                        <div class="baggagecharge-agent" id="extra_seat_charge_label" style="display:none">
                                            <div class="col-xs-8 nopadding">Seat Charge</div>
                                                <div class="col-xs-4 nopadding text-right">' . $currency_symbol . '
                                                <span class="amnter arimobold" id="extra_seat_charge"></span>
                                                <span class="btn btn-sm btn-default" id="remove_extra_seat"><i class="fa fa-times" aria-hidden="true"></i></span>
                                                </div>
                                        </div>
                                        ';

$extar_service_charge_details .= '</div>';


$grand_total = '<div class="clearfix"></div>
            <div class="reptalltftr">
                <a id="markup_show_hide">Show / Hide Markup</a>
            </div>
            <div class="reptalltftr" style="display: none;" id="add_custom_markup">
                <div class="col-xs-8 nopadding">
                    <div class="faresty">Markup</div>
                </div>
                 <div class="col-xs-4 nopadding">
                    <div class="amnter arimobold">
                    <input type="text" id="markup" name="markup" style="width:100px">
                    <input type="button" id="markup_add" value="add">
                    </div>
                </div>
                <div class="col-xs-12 nopadding er_msg" style="display: none;">
                    <p style="color:red" id="show_err_msg"></p>
                </div>
            </div>
            <div class="reptalltftr">
                <div class="col-xs-8 nopadding">
                    <div class="farestybig">Convenience Fees</div>
                </div>
                <div class="col-xs-4 nopadding">
                    <div class="amnterbig arimobold">
                    <span class="convenience_fees">0</span>
                    </div>
                </div>
            </div>
            <div class="reptalltftr">
                <div class="col-xs-8 nopadding">
                    <div class="farestybig">Grand Total</div>
                </div>
                <div class="col-xs-4 nopadding">
                    <div class="amnterbig arimobold">' . $currency_symbol . ' <span class="grand_total_amount">' . $grand_total_to_show . '</span>  
            <input id="grand_total_amount_copy" type="hidden" value="'.$grand_total_to_show.'">        
                    </div>
                </div>
            </div>';
//Net Fare Details
$hnf_details = '<div class="collapse" id="net_fare_details">
                        <div class="kindrest">
                            <div class="freshd">Fare Details</div>
                            <div class="reptallt">
                                <div class="col-xs-8 nopadding">
                                    <div class="faresty">Total Pub. Fare</div>
                                </div>
                                <div class="col-xs-4 nopadding">
                                    <div class="amnter arimobold"><span id="hnf_fare">' . $total_published_fare . '</span> </div>
                                </div>
                            </div>
                            <div class="reptallt">
                                <div class="col-xs-8 nopadding">
                                    <div class="faresty">Markup</div>
                                </div>
                                <div class="col-xs-4 nopadding">
                                    <div class="amnter arimobold">-<span id="hnf_markup">' . $FareDetails['_Markup'] . '</span> </div>
                                </div>
                            </div>
                            <div class="reptallt">
                                <div class="col-xs-8 nopadding">
                                    <div class="faresty">Comm. Earned</div>
                                </div>
                                <div class="col-xs-4 nopadding">
                                    <div class="amnter arimobold">-' . $FareDetails['_Commission'] . ' </div>
                                </div>
                            </div>
                            <div class="reptallt">
                            <div class="col-xs-8 nopadding">
                                <div class="faresty">TdsOnCommission</div>
                            </div>
                            <div class="col-xs-4 nopadding">
                                <div class="amnter arimobold">+' . $FareDetails['_tdsCommission'] . ' </div>
                            </div>
                            </div>'.$gst_data1.'
							<div class="reptalltftr">
								<div class="col-xs-8 nopadding">
									<div class="farestybig">Convenience Fees</div>
								</div>
								<div class="col-xs-4 nopadding">
									<div class="amnterbig arimobold">
									<span class="convenience_fees">0</span>
                                   <span class="convenience_fees_org hide">0</span> 
									</div>
								</div>
							</div>
                            <div class="reptallt_commisn">
                            <div class="col-xs-6 nopadding">
                                <div class="farestybig">Total Payable</div>
                            </div>
                            <div class="col-xs-6 nopadding">
                                <div class="amnterbig">' . $currency_symbol . ' <span id="agent_payable_amount">' . $total_payable . '</span> </div>
                            </div>
                            </div>
                            <div class="reptallt_commisn">
                            <div class="col-xs-8 nopadding">
                                <div class="farestybig">Total Earned</div>
                            </div>
                            <div class="col-xs-4 nopadding">
                                <div class="amnterbig ">' . $currency_symbol . ' <span id="hnf_earned">' . $FareDetails['_AgentEarning'] . '</span> </div>
                            </div>
                            </div>
                        </div>
                        </div>';
$fare_summary .= $hide_show_fare_details;
$fare_summary .= $hnf_details;
$fare_summary .= '<div id="published_fare_details">';
$fare_summary .= $pax_base_fare_details;
$fare_summary .= $pax_tax_details;
$fare_summary .= $extar_service_charge_details;
$fare_summary .= $grand_total;
$fare_summary .= '</div>';
$fare_summary .= '</div>
    </div>';

return $fare_summary;
}

function diaplay_phonecode($phone_code, $active_data, $user_country_code) {


$list = '';
foreach ($phone_code as $code) {
if (!empty($user_country_code)) {
if ($user_country_code == $code['country_code']) {
    $selected = "selected";
} else {
    $selected = "";
}
} else {

if ($active_data['api_country_list_fk'] == $code['origin']) {
    $selected = "selected";
} else {
    $selected = "";
}
}
$list .= "<option value=" . $code['name'] . " " . $code['country_code'] . "  " . $selected . " >" . $code['name'] . " " . $code['country_code'] . "</option>";
}
return $list;
}
?>
<script>
    $(document).on('change', '.choosen_baggage ', function() {
       var b_inc = $(this).data('bag-inc');
       var baggage_obj = $(this).find('option:selected');
       var choose_val = baggage_obj.data('choose-val');
       choose_val = choose_val+1;
        var ss = b_inc+1;
       var seg_count = $(this).data('seg-count');
        for(var i=ss; i<seg_count; i++){
           
            $('select[id=baggage_'+ss+'] option:eq('+choose_val+')').attr('selected', 'selected');
        }
        
       // $('.baggage_selection_'+b_class).val(b_val);
      
    });
$(document).ready(function (e) {
	var system_markup = $('#hnf_markup').text();
	var permanent_sys_mkp = system_markup;
	var prev_markup = 0;
	var custom_markup_ctr = 0;
    $("#markup_show_hide").click(function(){
        $("#add_custom_markup").toggle();
    });
    // $("#update_markup").click(function(){
    $('#markup_add').click(function(){
    var markup_val = parseFloat($('#markup').val());   
    
    var markup_limits = <?=$markup_limits?>;
    var grand_total = $('#grand_total_amount_copy').val();
    var extra_charge = $("#extra_seat_charge").text();
    var new_grand_total = 0;

    var markup_gst = $("#markup_gst_copy").val();
    
    if( $('#markup').val().trim() != "" && parseFloat(markup_val) >= 0 && (parseFloat(markup_val) <= parseFloat(markup_limits))){
       
		if(parseInt(custom_markup_ctr)>0)
			system_markup = 0;
        $('#markup_copy').val(markup_val);
		$('#hnf_markup').text(markup_val);
        var extra_markup_gst = 0; //(parseFloat(markup_val)/100)*10;
        var new_markup_gst = parseFloat(parseFloat(markup_gst)+parseFloat(extra_markup_gst)).toFixed(2);
        var new_grand_total = parseFloat(parseFloat(grand_total)-parseFloat(prev_markup)-parseFloat(system_markup)+parseFloat(markup_val)+parseFloat(extra_markup_gst)).toFixed(2);

        $("#markup_gst").text(new_markup_gst);
        $('.grand_total_amount').text(new_grand_total);
        $('#total_booking_amount').text(new_grand_total);
		$('#grand_total_amount_copy').val(new_grand_total);
		$('#hnf_fare').text(new_grand_total);
		var hnf_earned = $('#hnf_earned').text();
		hnf_earned = parseFloat(parseFloat(hnf_earned)-parseFloat(prev_markup)-parseFloat(system_markup)+parseFloat(markup_val)).toFixed(2);
		$('#hnf_earned').text(hnf_earned);
		prev_markup = markup_val; 
        $('.er_msg').css('display','none');
        $('#show_err_msg').text('');
		custom_markup_ctr++;
    }
    if($('#markup').val().trim() == "")
	{
       
		var markup_copy = $('#markup_copy').val();
		if(permanent_sys_mkp != markup_copy)
		{
			var new_grand_total = parseFloat(parseFloat(grand_total)-parseFloat(prev_markup)+parseFloat(permanent_sys_mkp)).toFixed(2);
			$('#hnf_markup').text(permanent_sys_mkp);
			$('#markup_copy').val(permanent_sys_mkp);
			$('#hnf_fare').text(new_grand_total);
			$('.grand_total_amount').text(new_grand_total);
			$('#total_booking_amount').text(new_grand_total);
			$('#grand_total_amount_copy').val(new_grand_total);
			var hnf_earned = $('#hnf_earned').text();
			hnf_earned = parseFloat(parseFloat(hnf_earned)-parseFloat(prev_markup)+parseFloat(permanent_sys_mkp)).toFixed(2);
			$('#hnf_earned').text(hnf_earned);
			prev_markup = 0;
			system_markup = permanent_sys_mkp;
			custom_markup_ctr = 0;
		}
	}
	
        var bagg = 0;
        var seat =0;
        var meal =0;
        if($('#extra_baggage_charge').text() == ''){
            bagg = 0;
        }else{
            bagg = $('#extra_baggage_charge').text();
        }
        if($('#extra_seat_charge').text() == ''){
             seat = 0;
        }else{
            seat = $('#extra_seat_charge').text();
        }
        if($('#extra_meal_charge').text() == ''){
            meal = 0;
        }else{
            meal = $('#extra_meal_charge').text();
        }
        //alert(bagg);
        if(new_grand_total > 0)
            grand_total = new_grand_total;
        var grand_total1 = (parseFloat(grand_total)+parseFloat(bagg)+parseFloat(seat)+parseFloat(meal));
        $('#show_err_msg').text('System accepts markup values under Rs.'+ markup_limits);
        $('.er_msg').css('display','block');
        //$('#markup').val('');
        $("#markup_gst").text(markup_gst);
        $('.grand_total_amount').text(grand_total1);
        $('#total_booking_amount').text(grand_total1);
		shoConFees();
});

$('.ticket_type_cls').click(function () {
var ticket_type = $(this).val();
$('#ticket_method').val(ticket_type);
});
$('#hide_show_net_fare').click(function () {
if ($(this).hasClass('show_details') == true) {
    $(this).removeClass('show_details').addClass('hide_details');
    $(this).empty().html('-HNF');
    $('#published_fare_details').hide();
} else if ($(this).hasClass('hide_details') == true) {
    $(this).removeClass('hide_details').addClass('show_details');
    $(this).empty().html('+SNF');
    $('#published_fare_details').show();
}
});
    $("input[name='selected_pm']").click(function(){
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
	var prev_conn_fees = 0;
    function shoConFees()
    {
        var amount = $(".grand_total_amount:first").text();
        var selected_radio = $("input[name='selected_pm']:checked");
        var selected_pm = selected_radio.val();
        var bank_code = $("#bank_code").val();
        $(".con_fees_section").remove();
        if((amount.trim() > 0) && (selected_pm!=""))
        {
			amount = parseFloat(amount) - parseFloat(prev_conn_fees);
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
                    if(isNaN(con_fees))
                        con_fees = 0;
					
					var total_copy = $("#grand_total_amount_copy").val();
					total_copy = parseFloat(total_copy) - parseFloat(prev_conn_fees);
					
					prev_conn_fees = con_fees;
					var total = parseFloat(amount)+parseFloat(con_fees);
					$(".grand_total_amount").text(total);
					$('#total_booking_amount').text(total);
					
					total_copy = parseFloat(total_copy) + parseFloat(con_fees);
					$("#grand_total_amount_copy").val(total_copy);
					$('#hnf_fare').text(total_copy);
                    $(".convenience_fees").text(con_fees);
                    $(".convenience_fees_org").text(con_fees);
                }
            });
        }
    }
});
</script>
<?php
//Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_session_expiry_script.js'), 'defer' => 'defer');
// Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_booking.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_extra_services.js'), 'defer' => 'defer');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('flight_extra_services.css'), 'media' => 'screen');
?>
<script type="text/javascript">
/*
session time out variables defined
*/
var search_session_expiry = "<?php echo $GLOBALS ['CI']->config->item('flight_search_session_expiry_period'); ?>";
var search_session_alert_expiry = "<?php echo $GLOBALS ['CI']->config->item('flight_search_session_expiry_alert_period'); ?>";
var search_hash = "<?php echo $session_expiry_details['search_hash']; ?>";
var start_time = "<?php echo $session_expiry_details['session_start_time']; ?>";
var session_time_out_function_call = 1;
</script>
