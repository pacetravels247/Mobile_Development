<script type="text/javascript">
	$("#show_inbound").click(function(){
        $("#t-w-i-1").show();
        $("#top-sort-list-1").show();
        $("#t-w-i-2").hide();
        $("#top-sort-list-2").hide();
    });

    $("#show_outbound").click(function(){
        $("#t-w-i-2").show();
        $("#top-sort-list-2").show();
        $("#t-w-i-1").hide();
        $("#top-sort-list-1").hide();
    });
	
	$(".dom_tab_div a").click(function(){
		$(".dom_tab_div a").removeClass("active");
		$(this).addClass("active");
	});
</script>
<?php
$api_code = $booking_source;
//Images Url
$template_images = $GLOBALS['CI']->template->template_images();
$journey_summary = $raw_flight_list['JourneySummary'];
$IsDomestic = $journey_summary['IsDomestic'];
$flights_data = $raw_flight_list['Flights'];
$mini_loading_image = '<div class="text-center loader-image">Please Wait</div>';
//Dividing cols
$col_parent_division = '';
$special_class = '';
$col_division = '';
if ($route_count == 1) {
    $col_division = 'rondnone';
    $special_class = "one_way_only";
    //check if not
    if ($trip_type != 'oneway') {
        $col_parent_division = 'round-trip';
        $arrowclass = 'fa fa-exchange';
    } else {
        $arrowclass = 'fa fa-long-arrow-right';
    }
    $div_dom = '';
} elseif ($route_count == 2) {
    $col_division = 'rondnone';
    $col_parent_division = 'round-domestk';
    $arrowclass = 'fa fa-long-arrow-right';
    $div_dom = '<div class="dom_tab"><div class="dom_tab_div"> <a class="active" href="#" id="show_inbound">' . $journey_summary["Origin"] . ' to ' . $journey_summary["Destination"] . '</a> <a href="#" id="show_outbound">' . $journey_summary["Destination"] . ' to ' . $journey_summary["Origin"] . '</a>
	             </div></div>';
}

$loc_dir_icon = '<div class="arocl fa fa-long-arrow-right"></div>';
//Change booking button based on type of flight
if ($domestic_round_way_flight) {
    $booking_button = '<button class="bookallbtn mfb-btn" type="button">Select</button>'; //multi flight booking
} else {
    $booking_button = '<button class="b-btn bookallbtn" type="submit">Book Now</button>';
}
$flights = '<div class="row ' . $col_parent_division . '">' . $div_dom;
$__root_indicator = 0;
if( get_client_ip() == '27.59.143.42'){
    // debug($flights_data);exit;
}
if(valid_array($flights_data[0])){
foreach ($flights_data as $__tirp_indicator => $__trip_flights) {


    $__root_indicator++;
    $flights .= '<div class="' . $col_division . ' r-w-g nopad '.$special_class.'" id="t-w-i-' . $__root_indicator . '">';
    foreach ($__trip_flights as $__trip_flight_k => $__trip_flight_v) {
        // debug($__trip_flight_v);exit;
        if ($__trip_flight_v['SegmentSummary'][0]['TotalStops'] > 0) {
            $stop_air = $__trip_flight_v['SegmentDetails'][0][0]['DestinationDetails']['AirportCode'];
            $stop_air1 = '<div class="city_code1">' . $stop_air . '</div>';
        } else {
            $stop_air1 = '';
        }
        //echo '$__trip_flight_v';
        // debug($__trip_flight_v);exit;
        $cur_ProvabAuthKey = $__trip_flight_v['ProvabAuthKey'];
        $cur_AirlineRemark = trim($__trip_flight_v['AirlineRemark']);
        $oth_AirlineRemark = trim($__trip_flight_v['Attr']['AirlineRemark']);
        $fare_type = trim($__trip_flight_v['FareType']);
        if(isset($__trip_flight_v['Attr']['ClassOfService'])){
            $class_of_service = " | Cabin Class - ".trim($__trip_flight_v['Attr']['ClassOfService']);
        }
        else
            $class_of_service = "";
        if($class_of_service == "")
        {
            //debug($__trip_flight_v); exit;
            $class_of_service = " | Cabin Class - ".trim($__trip_flight_v['SegmentSummary'][0]['AirlineDetails']['FareClass']);
        }
        $remark_separator = empty($cur_AirlineRemark) == false ? '* ' : '';
        $cur_FareDetails = $__trip_flight_v['FareDetails']['b2c_PriceDetails'];
        $passenger_count = 0;
        $adult_fare_det = $__trip_flight_v['PassengerFareBreakdown']['ADT'];
        $adt_count = $adult_fare_det["PassengerCount"];
        if(($api_code == TRAVELPORT_ACH_BOOKING_SOURCE) || ($api_code == TRAVELPORT_GDS_BOOKING_SOURCE)){
            $single_adt_bf = $adult_fare_det["BaseFare"];
            $single_adt_tax = $adult_fare_det["Tax"];
        }
        else{
            $single_adt_bf = $adult_fare_det["BaseFare"]/$adt_count;
            $single_adt_tax = $adult_fare_det["Tax"]/$adt_count;
        }
        $passenger_count+=$adt_count;
        
        if(isset($__trip_flight_v['PassengerFareBreakdown']['CHD'])){
        $child_fare_det = $__trip_flight_v['PassengerFareBreakdown']['CHD'];
        $chd_count = $child_fare_det["PassengerCount"];
        if(($api_code == TRAVELPORT_ACH_BOOKING_SOURCE) || ($api_code == TRAVELPORT_GDS_BOOKING_SOURCE)){
            $single_chd_bf = $child_fare_det["BaseFare"];
        }
        else{
            $single_chd_bf = $child_fare_det["BaseFare"]/$chd_count;
        }
        $passenger_count+=$chd_count;
        }

        if(isset($__trip_flight_v['PassengerFareBreakdown']['INF'])){
        $infant_fare_det = $__trip_flight_v['PassengerFareBreakdown']['INF'];
        $inf_count = $infant_fare_det["PassengerCount"];
        if(($api_code == TRAVELPORT_ACH_BOOKING_SOURCE) || ($api_code == TRAVELPORT_GDS_BOOKING_SOURCE))
            $single_inf_bf = $infant_fare_det["BaseFare"];
        else
            $single_inf_bf = $infant_fare_det["BaseFare"]/$inf_count;
        $passenger_count+=$inf_count;
        }

        $cur_SegmentDetails = $__trip_flight_v['SegmentDetails'];
        $cur_SegmentSummary = $__trip_flight_v['SegmentSummary'];
        $cur_IsRefundable = $__trip_flight_v['Attr']['IsRefundable'];
        $booking_source = $__trip_flight_v['booking_source'];
        $fare_type = trim($__trip_flight_v['FareType']);
        //Reset This Everytime
        $inner_summary = $outer_summary = '';
        $cur_Origin = $journey_summary['Origin'];
        $cur_Destination = $journey_summary['Destination'];
        $Refundable_lab = ($cur_IsRefundable == false ? 'Non-Refundable' : 'Refundable');
        if(isset($__trip_flight_v['Attr']['FareType'])){
            if(empty($__trip_flight_v['Attr']['AirlineRemark']) == false){
                $cur_AirlineRemark = $__trip_flight_v['Attr']['AirlineRemark'];
            }
            else{
                $cur_AirlineRemark = $__trip_flight_v['Attr']['FareType'];
            }
            
        }
        $is_refundable_intval = ($cur_IsRefundable == false ? 0 : 1);
        if($cur_IsRefundable == false){
            $is_refundable_intval = 0;
        }
        //Price Details
        $o_BaseFare = ($cur_FareDetails['BaseFare']);
        $cur_Currency = $cur_FareDetails['CurrencySymbol'];
        $o_Total_Tax = ($cur_FareDetails['TotalTax']);
        // $o_Total_Fare = ceil($cur_FareDetails['TotalFare']);
        $o_Total_Fare = $cur_FareDetails['TotalFare'];

        //Api price details
        // debug($__trip_flight_v['api_original_price_details']);exit;
        $api_total_price = $__trip_flight_v['api_original_price_details']['TotalDisplayFare'];
        $api_base_price = $__trip_flight_v['api_original_price_details']['BasicFare'];
        $api_tax_price = $__trip_flight_v['api_original_price_details']['Tax'];
        $api_agent_commission = $__trip_flight_v['api_original_price_details']['AgentCommission'];
        $api_offered_fare = $api_total_price-$api_agent_commission;
        $api_currency = $__trip_flight_v['api_original_price_details']['api_currency'];
        $original_markup = $__trip_flight_v['FareDetails']['api_PriceDetails']['original_markup'];
        $markup_type = $__trip_flight_v['FareDetails']['api_PriceDetails']['markup_type'];
       // debug($cur_FareDetails);exit;
        //added by Anitha
        $api_price_details = $__trip_flight_v["FareDetails"]["api_PriceDetails"];
        //Price Details
        $cus_total_fare = $cur_FareDetails['TotalFare'];
        $cus_total_tax = $cur_FareDetails['TotalTax'];
        $cus_base_fare = $cur_FareDetails['BaseFare'];
        $cur_Currency  = $cur_FareDetails['CurrencySymbol'];
        $agent_comm_perc = 0;
        if($api_agent_commission > 0){
            $agent_comm_perc = ($api_agent_commission/$cus_base_fare)*100;
        }
        
        //Single adult price display
        
        //debug($cur_FareDetails['_Commission']); exit;
        $single_adt_org_total = $single_adt_bf+$single_adt_tax;
        $comm_on_single_adt = ($single_adt_bf/100)*$agent_comm_perc;
        
        $tds_oncomm_single_adt = 0;
        if($comm_on_single_adt > 0){
            $tds_oncomm_single_adt = ($comm_on_single_adt/100)*5;
        }else{
            $tds_oncomm_single_adt = 0;
        }
        $single_adt_adm_markup = $cur_FareDetails['_OrgAdminMarkup']/$passenger_count;
        $single_adt_adm_gst = $cur_FareDetails['_GST']/$passenger_count;
        $sigle_adt_adm_toll = $single_adt_adm_markup+$single_adt_adm_gst;

        $single_adt_markup = $cur_FareDetails['_Markup']/$passenger_count;
        $single_adt_total = $single_adt_org_total+$sigle_adt_adm_toll;
        $single_adt_agt_earning = $single_adt_markup+$comm_on_single_adt-$tds_oncomm_single_adt;
        
        $single_adt_pub_fare = round($single_adt_total+$single_adt_markup);
        $single_adt_agt_buying = $single_adt_pub_fare-$single_adt_agt_earning;
        // debug($single_adt_pub_fare);exit;
        //VIEW START
        //SegmentIndicator used to identifies one way or return or multi stop
        $inner_summary .= '<div class="propopum" id="fdp_'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '">';
        $inner_summary .= '<div class="comn_close_pop closepopup">X</div>';
        $inner_summary .= '<div class="p_i_w">';
        $inner_summary .= '<div class="popuphed"><div class="hdngpops">' . $cur_Origin . ' <span class="' . $arrowclass . '"></span> ' . $cur_Destination . ' </div></div>';
        $inner_summary .= '<div class="popconyent">';
        $inner_summary .= '<div class="contfare">';

        $inner_summary .= '
			<ul role="tablist" class="nav nav-tabs flittwifil">
				<li class="active" data-role="presentation"><a data-toggle="tab" data-role="tab" href="#iti_det_'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '">Itinerary</a></li>
				<li data-role="presentation"><a data-toggle="tab" data-form-id=form-id-'.$api_code.'_'. $__root_indicator . $__trip_flight_k . '" class="iti-fare-btn" data-role="tab" href="#fare_det_'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '">Fare Details</a></li>
			</ul>
		';

        $inner_summary .= '<div class="tab-content">';
        $inner_summary .= '<div id="fare_det_'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '" class="tab-pane i-i-f-s-t' . add_special_class('xs-font', '', $domestic_round_way_flight) . '">';
        $inner_summary .= $mini_loading_image;
        $inner_summary .= '<div class="i-s-s-c tabmarg"></div>';
        $inner_summary .= '</div>';
        $inner_summary .= '<div id="iti_det_'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '" class="tab-pane active i-i-s-t ' . add_special_class('xs-font', '', $domestic_round_way_flight) . '">';
        $inner_summary .= '<div class="tabmarg">'; //summary wrapper start
        $inner_summary .= '<div class="alltwobnd">';
        $inner_summary .= '<div class="col-xs-8 nopad full_wher">'; //airline summary start
        foreach ($cur_SegmentDetails as $__segment_k => $__segment_v) {
            //debug($__segment_v);exit;
            $segment_summary = $cur_SegmentSummary[$__segment_k];
            $inner_summary .= '<div class="inboundiv seg-' . $__segment_k . '">';
            //Way Summary in one line - Start
            $inner_summary .= '<div class="hedtowr">';
            $inner_summary .= $segment_summary['OriginDetails']['CityName'] . ' to ' . $segment_summary['DestinationDetails']['CityName'] . ' <strong>(' . $segment_summary['TotalDuaration'] . ')</strong>';
            $inner_summary .= '</div>';
            //Way Summary in one line - End
            foreach ($__segment_v as $__stop => $__segment_flight) {
               // debug($__segment_flight);exit;
                $Baggage = trim($__segment_flight['Baggage']);
                $AvailableSeats = isset($__segment_flight['AvailableSeats']) ? $__segment_flight['AvailableSeats'] . ' seats' : '';
                $CabinBaggage = $__segment_flight['CabinBaggage'];
                //Summary of Way - Start
                $inner_summary .= '<div class="flitone">';
                //airline
                $inner_summary .= '<div class="col-xs-3 nopad5">
										<div class="imagesmflt">
										<img  alt="' . $__segment_flight['AirlineDetails']['AirlineCode'] . ' icon" src="' . SYSTEM_IMAGE_DIR . 'airline_logo/' . $__segment_flight['AirlineDetails']['AirlineCode'] . '.gif" >
										</div>
										<div class="flitsmdets">' . $__segment_flight['AirlineDetails']['AirlineName'] . '<strong>' . $__segment_flight['AirlineDetails']['AirlineCode'] . ' ' . $__segment_flight['AirlineDetails']['FlightNumber'] . ' '.$__segment_flight['AirlineDetails']['FareClassCode'].'</strong></div>
										</div>';
                //Between Content -----
                //depart
                $inner_summary .= '<div class="col-xs-7 nopad5">';
                $inner_summary .= '<div class="col-xs-5 nopad5">
										<div class="dateone">' . $__segment_flight['OriginDetails']['_DateTime'] . '</div>
										<div class="dateone">' . $__segment_flight['OriginDetails']['_Date'] . '</div>
										<div class="termnl">'.$__segment_flight['OriginDetails']['AirportName'].' ('.$__segment_flight['OriginDetails']['AirportCode'].')</div>
										</div>';
                //direction indicator
                $inner_summary .= '<div class="col-xs-2 nopad">
					' . $loc_dir_icon . '</div>';
                //arrival
                $inner_summary .= '<div class="col-xs-5 nopad5">
										<div class="dateone">' . $__segment_flight['DestinationDetails']['_DateTime'] . '</div>
										<div class="dateone">' . $__segment_flight['DestinationDetails']['_Date'] . '</div>
										<div class="termnl">'.$__segment_flight['DestinationDetails']['AirportName'].' ('.$__segment_flight['DestinationDetails']['AirportCode'].')</div>
										</div>';
                $inner_summary .= '</div>';
                //Between Content -----
                $inner_summary .= '<div class="col-xs-2 nopad5">
										<div class="ritstop">
										<div class="termnl">' . $__segment_flight['SegmentDuration'] . '</div>
										<div class="termnl1">Stop : ' . ($__stop) . '</div>';

                $inner_summary .= '</div> </div><span style="float:left"><b>'.$__segment_flight['StopOver'].'</b></span>';
                $inner_summary .= '<div class="Baggage_block">';
                if (empty($Baggage) == false) {
                    $inner_summary .= '<div class="termnl1 flo_w"><em><i class="fa fa-suitcase bag_icon"></i>' . ($Baggage) . '</em></div>';
                }
                if (empty($AvailableSeats) == false) {
                    $inner_summary .= '<div class="termnl1 flo_w"><em><i class="air_seat timings icseats" ></i>' . $AvailableSeats . '</em></div>';
                }
                $inner_summary .= '</div>';
                //Summary of Way - End
                $inner_summary .= '</div>';
                if (isset($__segment_flight['WaitingTime']) == true) {
                   
                    $waiting_time = $__segment_flight['WaitingTime'];
                    $inner_summary .= '
                <div class="clearfix"></div>
                <div class="layoverdiv">
                    <div class="centovr">
                    <span class="fa fa-plane"></span>Plane change at ' . $next_seg_info['OriginDetails']['CityName'] . ' | <span class="fa fa-clock-o"></span> Layover Time: ' . $waiting_time . '
                </div></div>
                <div class="clearfix"></div>';
                }
            }
            $inner_summary .= '</div>';
        }
        $inner_summary .= '</div>'; //airline summary end
        $inner_summary .= '<div class="col-xs-4 nopad full_wher">'; //price summary start
        $inner_summary .= '<div class="inboundiv sidefare">';

        $inner_summary .= '<h4 class="farehdng">Total Fare Breakup</h4>';

        $inner_summary .= '<div class="inboundivinr">';
       $inner_summary .= '
                        <div class="rowfare"><div class="col-xs-8 nopad">
                        <span class="infolbl">Adult Base Fare</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$adt_count.' X '.$cur_Currency.' '.roundoff_number($single_adt_bf).'</span>
                        </div></div>';
                if(isset($child_fare_det["BaseFare"]) && $child_fare_det["BaseFare"]!=0){
                $inner_summary .= '
                        <div class="rowfare"><div class="col-xs-8 nopad">
                        <span class="infolbl">Child Base Fare</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$chd_count.' X '.$cur_Currency.' '.roundoff_number($single_chd_bf).'</span>
                        </div></div>';
                    }
                if(isset($infant_fare_det["BaseFare"]) && $infant_fare_det["BaseFare"]!=0){
                $inner_summary .= '
                        <div class="rowfare"><div class="col-xs-8 nopad">
                        <span class="infolbl">Infant Base Fare</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$inf_count.' X '.$cur_Currency.' '.roundoff_number($single_inf_bf).'</span>
                        </div></div>';
                    }   
                $inner_summary .= '
                        <div class="rowfare"><div class="col-xs-8 nopad">
                        <span class="infolbl">Total Base Fare</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$cur_Currency.' '.roundoff_number($cus_base_fare).'</span>
                        </div></div>';
                $inner_summary .= '
                        <div class="rowfare"><div class="col-xs-8 nopad">
                        <span class="infolbl">Taxes &amp; Fees</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$cur_Currency.' '.roundoff_number($cus_total_tax).'</span>
                        </div></div>';
                $inner_summary .= '
                        <div class="rowfare grandtl"><div class="col-xs-8 nopad">
                        <span class="infolbl">Grand Total</span>
                        </div>
                        <div class="col-xs-4 nopad">
                        <span class="pricelbl">'.$cur_Currency.' '.roundoff_number($cus_total_fare).'</span>
                        </div></div>';
        $inner_summary .= '</div>';
        $inner_summary .= '</div>';

        $inner_summary .= '</div>'; //price summary end
        $inner_summary .= '</div>'; //summary wrapper end
        $inner_summary .= '</div>';
        $inner_summary .= '</div>';
        $inner_summary .= '</div>'; //tab-content

        $inner_summary .= '</div>'; //contfare
        $inner_summary .= '</div>'; //popconyent
        //$inner_summary .= '<div class="popfooter"><div class="futrcnt"><button class="norpopbtn closepopup">Close</button>  </div></div>';
        $inner_summary .= '</div>'; //inned wrap
        $inner_summary .= '</div>'; //propopum
        //Outer Summary - START
        //$outer_summary .= '<div class="madgrid ' . add_special_class('', '', $domestic_round_way_flight) . '">';
        $outer_summary .='<span class="booking_source hide">'.$booking_source.'</span>';
        $outer_summary .='<span class="fare_type hide">'.$fare_type.'</span>';
        $outer_summary .= '<div class="madgrid" data-key="' . $__root_indicator . $__trip_flight_k . '">
		<div class="onlmob"><form method="POST" id="form-id-'.$api_code.'_'.$__root_indicator.$__trip_flight_k.'" action="'.$booking_url.'" class="book-form-wrapper">
									
									'.$GLOBALS['CI']->flight_lib->booking_form($IsDomestic, $__trip_flight_v['Token'], $__trip_flight_v['TokenKey'], $cur_ProvabAuthKey,$booking_source).'
									'.$booking_button.'
								</form></div>';
        $outer_summary .= '<div class="f-s-d-w col-xs-8 nopad wayeght full_same">';
        $total_stop_count = 0;

        foreach ($cur_SegmentSummary as $__segment_k => $__segment_v) {

            //terminals
            $origin_terminal = '';
            $dest_terminal = '';
            if($__segment_flight['OriginDetails']['OriginTerminal'] !=''){
                $origin_terminal = 'Terminal : '.$__segment_flight['OriginDetails']['OriginTerminal'];
            }
            if($__segment_flight['DestinationDetails']['DestinationTerminal'] !=''){
                $dest_terminal = 'Terminal : '.$__segment_flight['DestinationDetails']['DestinationTerminal'];
            }
            $total_segment_travel_duration = $__segment_v['TotalDuaration'];
            
            $dur = $total_segment_travel_duration;

            $dur = explode(' ', $dur);
            $count = count($dur);
//print_r($count);

            $check = (strrpos($dur[0], 'm')) ? $dur[0] * 1 : $dur[0] * 60;

            $h = str_replace('h', '', $dur[0]);
            if (!empty($dur[1])) {
               
                $m = $dur[1] * 1;
                $h = $dur[0] * 60;
                $d = $h + $m;
            } else {
                $d = $check;
            }
            
            $duration = $d;
            /*             * ***************************************** */
            $__stop_count = $__segment_v['TotalStops'];
            $total_stop_count += $__stop_count;
            $stop_image = '';
            for ($image_name = 0; $image_name < 5; $image_name++) {
                if ($__stop_count == $image_name) {
                    $stop_image = $GLOBALS['CI']->template->template_images('stop_' . $image_name . '.png');
                }
            }
            if ($__stop_count > 4) {
                $stop_image = $GLOBALS['CI']->template->template_images('more_stop.png');
            }
            // if($__stop_count==0){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('stop_0.png');
            // }elseif($__stop_count==1){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('stop_1.png');
            // }elseif($__stop_count==2){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('stop_2.png');
            // }
            // elseif($__stop_count==3){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('stop_3.png');
            // }
            // elseif($__stop_count==4){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('stop_4.png');
            // }elseif($__stop_count>4){
            // 	$stop_image =$GLOBALS['CI']->template->template_images('more_stop.png');
            // }
            $outer_summary .= '<div class="allsegments outer-segment-' . $__segment_k . '">';
            //airline
            $outer_summary .= '<div class="quarter_wdth nopad ' . add_special_class('col-xs-3', 'col-xs-3', $domestic_round_way_flight) . '">
										<div class="fligthsmll"><img class="airline-logo" alt="' . $__segment_v['AirlineDetails']['AirlineCode'] . ' icon" src="' . SYSTEM_IMAGE_DIR . 'airline_logo/' . $__segment_v['AirlineDetails']['AirlineCode'] . '.gif"></div>
										<div class="m-b-0 text-center">
											<div class="a-n airlinename" data-code="' . $__segment_v['AirlineDetails']['AirlineCode'] . '">
												' . $__segment_v['AirlineDetails']['AirlineName'] . '
											</div>
											<strong> ' . $__segment_v['AirlineDetails']['AirlineCode'] . ' ' . $__segment_v['AirlineDetails']['FlightNumber'] . ' '.$__segment_v['AirlineDetails']['FareClassCode'].'</strong>
										</div>
									  </div>';
            //depart
            $outer_summary .= '<div class="col-xs-3 nopad quarter_wdth">
											<div class="insidesame">
												<span class="fdtv hide">' . date('Hi', strtotime($__segment_v['OriginDetails']['DateTime'])) . '</span>
												<span>'.date('d-m-Y', strtotime($__segment_flight['OriginDetails']['DateTime'])).'</span>
                                                <div class="f-d-t bigtimef">' . $__segment_v['OriginDetails']['_DateTime'] . '</div>
												<div class="from-loc smalairport_code">' . $__segment_v['OriginDetails']['AirportCode'] . '</div>
												<div class="from-loc smalairport">' . $__segment_v['OriginDetails']['CityName'].' ('.$__segment_v['OriginDetails']['AirportCode'].')</div>
												<div class="flight_terminal"> 
                                                    '.$origin_terminal.'
                                                </div>
                                                <span class="dep_dt hide" data-category="' . time_filter_category($__segment_v['OriginDetails']['DateTime']) . '" data-datetime="' . (number_format((strtotime($__segment_v['OriginDetails']['DateTime']) * 1000), 0, null, '')) . '"></span>
											</div>
										</div>';
            //direction indicator
            //$outer_summary .= '<div class="clearfix visible-sm-block"></div>';
            $outer_summary .= '<div class="col-md-1 p-tb-10 hide">' . $loc_dir_icon . '</div>';
            //$outer_summary .= '<div class="clearfix visible-sm-block"></div>';
            $outer_summary .= '<div class="smal_udayp nopad ' . add_special_class('col-xs-3', 'col-xs-3', $domestic_round_way_flight) . '"><span class="f-d hide">' . $duration . '</span>
											<div class="insidesame">
												<div class="durtntime">' . ($total_segment_travel_duration) . '</div>
												<div class="stop_image"><img src=' . $stop_image . ' alt="stop_0"></div>
												<div class="stop-value">Stop:' . ($__stop_count) . '</div>' . $stop_air1 .
                    '<div class="cabinclass hide">' . ($cabin_class) . '</div>
											</div>
										</div>';
            //arrival
            $outer_summary .= '<div class="col-xs-3 nopad quarter_wdth">
											<div class="insidesame">
												<span class="fatv hide">' . date('Hi', strtotime($__segment_v['DestinationDetails']['DateTime'])) . '</span>
												<span>'.date('d-m-Y', strtotime($__segment_flight['DestinationDetails']['DateTime'])).'</span>
                                                <div class="f-a-t bigtimef">' . $__segment_v['DestinationDetails']['_DateTime'] . '</div>
												<div class="to-loc smalairport">' . $__segment_v['DestinationDetails']['CityName'].' ('.$__segment_v['DestinationDetails']['AirportCode'].')</div>
												<div class="smalairport_code">' . $__segment_v['DestinationDetails']['AirportCode'] . '</div>
												<div class="flight_terminal">
                                                    '.$dest_terminal.'
                                                </div>
                                                <span class="arr_dt hide" data-category="' . time_filter_category($__segment_v['DestinationDetails']['DateTime']) . '" data-datetime="' . (number_format((strtotime($__segment_v['DestinationDetails']['DateTime']) * 1000), 0, null, '')) . '"></span>
											</div>
										</div>';


            $outer_summary .= '</div>';
        }
        $outer_summary .= '</div>';
        $outer_summary .= '
					<div class="col-xs-4 nopad wayfour full_same">
						<span class="hide stp" data-stp="' . $total_stop_count . '" data-category="' . stop_filter_category($total_stop_count) . '"></span>
						<div class="priceanbook">
							<div class="col-xs-6 nopad wayprice">
								<div class="insidesame">
									<div class="priceflights"><strong class="display_currency"> ' . $cur_Currency . ' </strong><span class="f-p">'.number_format($single_adt_pub_fare, 2).'</span></div>
									<span class="hide price f-price" data-price="'.$single_adt_pub_fare.'" data-currency="'.$cur_Currency.'">'.$single_adt_pub_fare.'</span>
									<div data-val="' . $is_refundable_intval . '" class="refnref n-r n-r-t">' . $Refundable_lab . '</div>
                                    <div class="n-r n-r-t">' . $fare_type . '</div>
								</div>
							</div>
							<div class="col-xs-6 nopad waybook">
								<div class="form-wrapper bookbtlfrt">
								<form method="POST" id="form-id-'.$api_code.'_'.$__root_indicator . $__trip_flight_k . '" action="' . $booking_url . '" class="book-form-wrapper">
									
									' . $GLOBALS['CI']->flight_lib->booking_form($IsDomestic, $__trip_flight_v['Token'], $__trip_flight_v['TokenKey'], $cur_ProvabAuthKey, $booking_source) . '
									' . $booking_button . '
								</form>

								</div>
							</div>
						</div>
					</div>';
        $outer_summary .= '<div class="clearfix"></div>';
        //Load Flight Details Button
        $outer_summary .= '<div class="mrinfrmtn">
									<a class="detailsflt iti-btn" data-id="fdp_' .$api_code.'_'.$__root_indicator . $__trip_flight_k . '"><span class="fal fa-info-circle fldetail" data-toggle="tooltip" title="" data-original-title="Flight Itinerary"></span>Flight Details '.$remark_separator.'</a><i>'.$fare_type.''.$new_cur_AirlineRemark.' | Baggage - '.$Baggage.' | '.$AvailableSeats.' available'.'| Cabin Baggage-'.$CabinBaggage;
        if(!empty($cur_AirlineRemark) || !empty($oth_AirlineRemark))
        {
            if(trim($cur_AirlineRemark) == trim($oth_AirlineRemark))
                $oth_AirlineRemark="|";
            else if(!empty(trim($oth_AirlineRemark)))
                $oth_AirlineRemark = " | ".$oth_AirlineRemark.'';
        }

        $outer_summary .= $oth_AirlineRemark.$class_of_service .'</i>
							</div>';
        //Outer Summary - END
        $outer_summary .= '</div>';

        $flights .= '<div class="rowresult p-0 r-r-i t-w-i-' . $__root_indicator . '">
						' . $outer_summary . '
						' . $inner_summary . '
					</div>';
    }
    $flights .= '</div>';
}
}
$flights .= '</div>';
echo $flights;

/**
 * Return class based on type of page
 */
function add_special_class($col_2x_class, $col_1x_class, $domestic_round_way_flight) {
    if ($domestic_round_way_flight) {
        return $col_2x_class;
    } else {
        return $col_1x_class;
    }
}

function time_filter_category($time_value) {
    $category = 1;
    $time_offset = intval(date('H', strtotime($time_value)));
    if ($time_offset < 6) {
        $category = 1;
    } elseif ($time_offset < 12) {
        $category = 2;
    } elseif ($time_offset < 18) {
        $category = 3;
    } else {
        $category = 4;
    }
    return $category;
}

/**
 * Generate Category For Stop
 */
function stop_filter_category($stop_count) {
    $category = 1;
    switch (intval($stop_count)) {
        case 0 : $category = 1;
            break;
        case 1 : $category = 2;
            break;
        default : $category = 3;
            break;
    }
    return $category;
}
