<?php
$api_count = count($active_booking_source);
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
if($flight_search_params['trip_type'] == 'multicity') {
	$flight_search_params['depature'] = $flight_search_params['depature'][0];
}
//Added
$lcc_gds_no = count($_GET['lcc_gds']);
if($lcc_gds_no == 2)
	$lcc_gds = 0;
else
{
	$lcc_gds = $_GET['lcc_gds'][0];
}
//Added End
?>
<script>
var api_result_counter = 0;
var search_session_alert_expiry = "<?php echo $GLOBALS ['CI']->config->item ( 'flight_search_session_expiry_alert_period' ); ?>";
var search_session_expiry = "<?php echo $GLOBALS ['CI']->config->item ( 'flight_search_session_expiry_period' ); ?>";
var search_hash = '';
var session_time_out_function_call = 0;

var progress_width = 0;
var api_count = "<?php echo $api_count;?>";
var lcc_gds = "<?php echo @$lcc_gds; ?>";
var conn_direct = "<?php echo @$_GET['conn_direct'];?>";
var pref_carrier = "<?php echo @$_GET['carrier'];?>";
if(lcc_gds == ''){
	lcc_gds = '0';
}
if(conn_direct ==''){
	conn_direct = '0';
}
//alert(conn_direct);
var load_flights = function(bs){
	var ij=1;
	$.ajax({
		type: 'GET',
		url: app_base_url+'index.php/ajax/flight_list?booking_source='+bs+'&search_id=<?=$flight_search_params['search_id']?>&lcc_gds='+lcc_gds+'&conn_direct='+conn_direct+'&carrier='+pref_carrier+'&op=load',
		async: true,
		cache: true,
		dataType: 'json',
		success: function(res) {
			var dui;
			var r = res;
			dui = setInterval(function(){ 
			if (typeof(process_result_update) != "undefined" && $.isFunction(process_result_update) == true) {	
				clearInterval(dui);  


				var progress_count = parseInt(100/parseInt(api_count));

				progress_width = progress_width + progress_count ;
		
                if(progress_width < 90){
                	$('.progress-bar').css('width', progress_width + "%");
                }else{
            		$('.loderprtflgt').hide();
                }


				process_result_update(r,'<?php echo $api_count; ?>',ij);
				// search_hash = r.session_expiry_details.search_hash;

				//check_session_time_out(search_hash, r.session_expiry_details.session_start_time); // check session expired or not
			}
			}, 1) 
			$('#onwFltContainer').hide();
			ij++;
		}
	});

}

//load_flights();

/*
	append hidden element search_hash for booking form when submiting
*/
$('body').on('submit', '.book-form-wrapper, #multi-flight-form', function(){
	$('<input />').attr('type', 'hidden')
          .attr('name', "search_hash")
          .attr('value', search_hash)
          .appendTo(this);
      return true;
});
</script>
<?php
	foreach($active_booking_source as $active_key => $act_booking_source){
		$bs = trim($act_booking_source['source_id']);
		echo '<script> load_flights("'.$bs.'");</script>';
	}
?>
<span class="hide">
	<input type="hidden" id="pri_preferred_currency" value='<?=$this->currency->get_currency_symbol(get_application_currency_preference())?>'>
	<input type="hidden" id="pri_trip_type" value='<?=$is_domestic_one_way_flight?>'>
	<input type="hidden" id="pri_active_source" value='<?=$active_source?>'>
	<input type="hidden" id="pri_search_id" value='<?=$flight_search_params['search_id']?>'>
	<input type="hidden" id="pri_airline_lg_path" value='<?=SYSTEM_IMAGE_DIR.'airline_logo/'?>'>
	<input type="hidden" id="pri_search_params" value='<?=json_encode($flight_search_params)?>'>
	<input type="hidden" name="" id="pri_template_image_path" value="<?=$GLOBALS['CI']->template->template_images()?>">
	<input type="hidden" id="pri_def_curr" value='<?=$this->currency->get_currency_symbol($to_currency)?>'>
</span>
<?php
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_search.js'), 'defer' => 'defer');
//Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
//Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_session_expiry_script.js'), 'defer' => 'defer');
$data['trip_details'] = $flight_search_params;
$data['airline_list'] = $airline_list;//Balu A
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v1.gif" alt="Loading........"/></div>';
$flight_o_direction_icon = '<img src="'.$template_images.'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
echo $GLOBALS['CI']->template->isolated_view('flight/search_panel_summary');
?>
<!-- Page Scripts -->
<section class="search-result onlyfrflty">
	<div class="container-fluid"  id="page-parent">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/flight_result_pre_loader',$data);?>
		<div class="resultalls open">
			<div class="coleft">
				<div class="flteboxwrp">
					<div class="filtersho">
						<div class="avlhtls"><strong id="total_records"> </strong> Flights Found
						</div>
						<span class="close_fil_box"><i class="fas fa-times"></i></span>
					</div>
					<!-- Refine Search Filters Start -->
					<div class="fltrboxin">
						<div class="celsrch">
								<div class="row_top_fltr">
									<a class="snf_btn pull-left active" title="Show Net Fare">
									<span class="fas fa-tag"></span>
									<span class="tag_snf">SNF</span></a>
									<a class="pull-right reset_filter" id="reset_filters">RESET ALL</a>
								</div>
							<div class="rangebox">
								<button data-target="#collapse501" data-toggle="collapse" class="collapsebtn" type="button">
								Price
								</button>
								<div id="collapse501" class="in">
									<div class="price_slider1">
									<div id="core_min_max_slider_values" class="hide">
										<input type="hiden" id="core_minimum_range_value" value="">
										<input type="hiden" id="core_maximum_range_value" value="">
									</div>
										<p id="amount" class="level"></p>
										<div id="slider-range" class="" aria-disabled="false"></div>
									</div>
								</div>
							</div>
							<div class="septor"></div>
							<div class="rangebox">
								<button data-target="#collapse502" data-toggle="collapse" class="collapsebtn" type="button">
								No. of Stops
								</button>
								<div id="collapse502" class="collapse in">
									<div class="boxins marret" id="stopCountWrapper">
										<a class="stopone toglefil stop-wrapper">
											<input type="checkbox" class="hidecheck stopcount" value="1"></input>
											<div class="starin">
												<div class="stopbig"> 0 <span class="stopsml">stop</span></div>
												<span class="htlcount min-price">-</span>
											</div>
										</a>
										<a class="stopone toglefil stop-wrapper">
											<input type="checkbox" class="hidecheck stopcount" value="2"></input>
											<div class="starin">
												<div class="stopbig"> 1 <span class="stopsml">stop</span></div>
												<span class="htlcount min-price">-</span>
											</div>
										</a>
										<a class="stopone toglefil stop-wrapper">
											<input type="checkbox" class="stopcount hidecheck" value="3"></input>
											<div class="starin">
												<div class="stopbig"> 1+ <span class="stopsml">stop</span></div>
												<span class="htlcount min-price">-</span>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div class="septor"></div>
							<div class="rangebox">
								<button data-target="#collapse500" data-toggle="collapse" class="collapsebtn" type="button">
								Refund / Non Refund
								</button>
								<div id="collapse500" class="collapse in">
									<div class="boxins marret" id="ref_nref_chks">			
										<a class="timone toglefil ref_nref-wrapper">
											<div class="tmxdv">
											<input type="checkbox" class="ref_nref hidecheck" value="1" />
											<label for="refund" class="ckboxdv">Refundable</label>
											</div>
										</a>
										<a class="timone toglefil ref_nref-wrapper">
											<div class="tmxdv">
											<input type="checkbox" class="ref_nref hidecheck" value="0" />
											<label for="non_refund" class="ckboxdv">Non Refundable</label>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div class="septor"></div>
							<div class="rangebox">
								<button data-target="#collapse503" data-toggle="collapse" class="collapsebtn" type="button">
								Departure Time
								</button>
								<div id="collapse503" class="collapse in">
									<div class="boxins marret" id="departureTimeWrapper">
										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="1"></input>
											<label for="check1" class="ckboxdv">Early Morning</label>
											</div>
											
												<div class="flitsprt mng1"></div>
												<span class="htlcount">12-6AM</span>
											</div>

										</a>
										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="2"></input>
											<label for="check1" class="ckboxdv">Morning</label>
											</div>
												<div class="flitsprt mng2"></div>
												<span class="htlcount">6-12PM</span>
											</div>

										</a>
										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="3"></input>
											<label for="check1" class="ckboxdv">Mid-Day</label>
											</div>
											
												<div class="flitsprt mng3"></div>
												<span class="htlcount">12-6PM</span>
											</div>

										</a>
										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="hidecheck time-category" value="4"></input>
											<label for="check1" class="ckboxdv">Evening</label>
											</div>
											
												<div class="flitsprt mng4"></div>
												<span class="htlcount">6-12AM</span>
											</div>

										</a>
									</div>
								</div>
							</div>
							<div class="rangebox">
								<button data-target="#collapse504" data-toggle="collapse" class="collapsebtn" type="button">
								Arrival Time
								</button>
								<div id="collapse504" class="collapse in">
									<div class="boxins marret" id="arrivalTimeWrapper">

										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="1"></input>
											<label for="check1" class="ckboxdv">Early Morning</label>
											</div>
											
												<div class="flitsprt mng1"></div>
												<span class="htlcount">12-6AM</span>
											</div>

										</a>

										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="2"></input>
											<label for="check1" class="ckboxdv">Morning</label>
											</div>
											
												<div class="flitsprt mng2"></div>
												<span class="htlcount">6-12PM</span>
											</div>

										</a>

										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="3"></input>
											<label for="check1" class="ckboxdv">Mid-Day</label>
											</div>
											
												<div class="flitsprt mng3"></div>
												<span class="htlcount">12-6PM</span>
											</div>

										</a>

										<a class="timone toglefil time-wrapper">
											<div class="starin">
											<div class="tmxdv">
											<input type="checkbox" class="time-category hidecheck" value="4"></input>
											<label for="check1" class="ckboxdv">Evening</label>
											</div>
											
												<div class="flitsprt mng4"></div>
												<span class="htlcount">6-12AM</span>
											</div>

										</a>

									</div>
								</div>
							</div>
							<div class="septor"></div>
							<div class="rangebox">
								<button data-target="#collapse505" data-toggle="collapse" class="collapsebtn" type="button">
								Airlines
								</button>
								<div id="collapse505" class="collapse in">
									<div class="boxins" id="allairlines">
										
									</div>
								</div>
							</div>
							<div class="septor"></div>
						</div>
					</div>
				</div>
				<!-- Refine Search Filters End -->
			</div>
			<div class="colrit">
				<?php if($flight_search_params['trip_type'] != 'multicity'){ ?>
				<!-- Prev|Next Searcrh Button Starts -->
				<?php
				$core_travel_date = $flight_search_params['depature'];
				$current_date = date('Y-m-d');
				if(strtotime($core_travel_date) > strtotime($current_date)) {
					$prev_day_search_disabled = '';
					$prev_day_search_date = date('d-m-Y', strtotime('-1 day', strtotime($core_travel_date)));
					$prev_day_search_url = base_url().'index.php/flight/add_days_todate?search_id='.$flight_search_params['search_id'].'&new_date='.$prev_day_search_date;
				} else {
					$prev_day_search_url = '';
					$prev_day_search_disabled = ' disabled="disabled" ';
				}
				$next_day_search_date = date('d-m-Y', strtotime('+1 day', strtotime($core_travel_date)));
				$next_day_search_url = base_url().'index.php/flight/add_days_todate?search_id='.$flight_search_params['search_id'].'&new_date='.$next_day_search_date;
				?>
                <div class="prev_next_date">
	                <a <?=$prev_day_search_disabled?> href="<?=$prev_day_search_url?>" title="<?=(empty($prev_day_search_date) == false ? app_friendly_absolute_date($prev_day_search_date): 'depart is less thant current date')?>" class="btn_dates"><i class="fal fa-angle-left"></i> Prev Day</a>
                    <span class="datevery">
	                <?=app_friendly_absolute_date($core_travel_date)?>
                    </span>
	                <a href="<?=$next_day_search_url?>" title="<?=(empty($next_day_search_date) == false ? app_friendly_absolute_date($next_day_search_date): 'depart is less than current date')?>" class="btn_dates">Next Day <i class="fal fa-angle-right"></i></a>
                 </div>
               <!-- Prev|Next Searcrh Button Ends -->
               <?php } ?>
				<div class="insidebosc">
				<!-- Fare Calander -->
				<?php if ($is_domestic_one_way_flight == true && $flight_search_params['v_class'] == 'Economy') : ?>
				<div class="calandcal" id="fare_calendar_wrapper">
					<div class="col-xs-12 nopad">
						<div class="farenewcal">
							<div class="matrx">
								<div id="farecal" class="owl-carousel matrixcarsl">
								</div>
								</div>
								</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php endif; // Checking if one way domestic?>
				<!-- Fare Calander End -->
					<!-- Airline Slider Start -->
				<div class="loderprtflgt flgt_prgrs_bar">					
					<div class="progress">
	                	<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 66.6667%;"><i class="fa fa-plane plane_anim"></i>
	                	</div>
	            	</div>
				</div>	
					<div class="airlinrmatrix" id="clone-list-container">
				<div class="inside_shadow_airline">
						<div class="linefstr">
							<div class="airlineall">All Airline</div>
						</div>
						<!-- <div class="linescndr">
							<div class="matrx">
								<div id="arlinemtrx" class="owl-carousel matrixcarsl">
								</div>
							</div>
						</div> -->
						<div class="linescndr">
							<div class="matrx" id="airlinematrix-wrapper"></div>
						</div>
					</div>
                    </div>
					<!-- Airline Slider End -->
                    <div class="clearfix"></div>
        <!--  Current Selection  -->
        <div class="fixincrmnt hide" id="multi-flight-summary-container">
            <div class="insidecurent">
            <div class="col-xs-9 nopad">
                <div class="col-xs-6 nopad onward_multi_flight_data">
                    <div class="selctarln colorretn">
                        <div class="col-xs-2 nopad flightimage">
                            <div class="fligthsmll">
                                <img class="departure-flight-icon" src="<?=$template_images?>airline.png" alt="" />
                            </div>
                            <div class="airlinename departure-flight-name">Please Select</div>
                        </div>
                        
                        <div class="col-xs-10 nopad listfull">
                            <div class="sidenamedesc">
                            
                            
                            <div class="celhtl width80">
                                <div class="waymensn">
                                    <div class="flitruo">
                                        <div class="outbound-details">
                                        </div>
                                        <div class="detlnavi outbound-timing-details">
                                            <div class="col-xs-4 padflt widfty">
                                                <span class="timlbl departure">---</span>
                                            </div>
                                            <div class="col-xs-4 padflt nonefity">
                                                <div class="lyovrtime">
                                                    <span class="flect duration">---</span>
                                                    <span class="flect stop-count">---</span>
                                                </div>
                                            </div>
                                            <div class="col-xs-4 padflt widfty">
                                                <span class="timlbl arrival text_algn_rit">---</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            </div>
                        </div>                  
                    </div>
                </div>
                <div class="col-xs-6 nopad return_multi_flight_data">
                    <div class="selctarln cloroutbnd">
                        <div class="col-xs-2 nopad flightimage">
                            <div class="fligthsmll">
                                <img class="arrival-flight-icon" src="<?=$template_images?>airline.png" alt="" />
                            </div>
                            <div class="airlinename arrival-flight-name">---</div>
                        </div>
                        
                        <div class="col-xs-10 nopad listfull">
                            <div class="sidenamedesc">
                            
                            
                                <div class="celhtl width80">
                                    <div class="waymensn">
                                        <div class="flitruo">
                                            <div class="inbound-details">
                                            </div>
                                            <div class="detlnavi inbound-timing-details">
                                                <div class="col-xs-4 padflt widfty">
                                                    <span class="timlbl departure">---</span>
                                                </div>
                                                <div class="col-xs-4 padflt nonefity">
                                                    <div class="lyovrtime">
                                                        <span class="flect duration">---</span>
                                                        <span class="flect stop-count">---</span>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4 padflt widfty">
                                                    <span class="timlbl arrival text_algn_rit">---</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>                  
                    </div>
                </div>
            </div>
            
            <div class="col-xs-3 nopad">
                    <div class="sidepricewrp"> 
                        <div class="col-xs-12 nopad">             		
                            <div class="sidepricebig">
                            	<strong class="currency"></strong> <span class="f-p"></span>
                            	<span class="round_trip_loader hide"><img class="departure-flight-icon" src="<?= base_url();?>/extras/system/template_list/template_v3/images/loader_v3.gif" alt="" /></span>
                            </div>
                        </div>
                        <div class="col-xs-12 nopad pull-right">
                            <div class="bookbtn">
								<input type="hidden" id="flight-from-price" value="0">
								<input type="hidden" id="flight-to-price" value="0">
								<input type="hidden" id="flight-from-bookingsource" value="0">
								<input type="hidden" id="flight-to-bookingsource" value="0">
								<input type="hidden" id="flight-from-faretype" value="0">
								<input type="hidden" id="flight-to-faretype" value="0">
								<form id="multi-flight-form" action="" method="POST">
									<div class="hide" id="trip-way-wrapper"></div>
									<button class="btn-flat booknow" type="submit" id="multi-flight-booking-btn">Book</button>
								</form>
                            </div>
                        </div>
                    </div>
                    
            </div>
            </div>
        </div>
		<!--  Current Selection  End  -->

                	
					<div class="clearfix"></div>
                    <div class="filter_tab fas fa-filter"></div>
                    <?php $sorting_list = '';?>
                    <div class="filterforall addtwofilter" id="top-sort-list-wrapper">
                        <div class="topmisty" id="top-sort-list-1">
                            <div class="col-xs-12 nopad">
                            <div class="divinsidefltr">
                                <div class="insidemyt col-xs-12 nopad">
                                <?php 
                                    $sorting_list .= '<ul class="sortul">';
                                        $sorting_list .= '<li class="sortli hide_lines">';
                                         $sorting_list .= '<a class="sorta name-l-2-h loader asc"><i class="fas fa-plane"></i> <strong>Airline</strong></a>';
                                         $sorting_list .= '<a class="sorta name-h-2-l hide loader des"><i class="fas fa-plane"></i> <strong>Airline</strong></a>';
                                         $sorting_list .= '</li>';
                                         $sorting_list .= '<li class="sortli">';
                                         $sorting_list .= '<a class="sorta departure-l-2-h loader asc"><i class="far fa-calendar-alt"></i> <strong>Depart</strong></a>';
                                         $sorting_list .= '<a class="sorta departure-h-2-l hide loader des"><i class="far fa-calendar-alt"></i> <strong>Depart</strong></a>';
                                        $sorting_list .= '</li>';
                                          $sorting_list .= '<li class="sortli hide_lines">';
                                        	$sorting_list .= '<a class="sorta duration-l-2-h loader asc"><i class="far fa-clock"></i> <strong>Duration</strong></a>';
                                            $sorting_list .= '<a class="sorta duration-h-2-l hide loader des"><i class="far fa-clock"></i> <strong>Duration</strong></a>';
                                        $sorting_list .= '</li>';
                                        $sorting_list .= '<li class="sortli">';
                                        	$sorting_list .= '<a class="sorta arrival-l-2-h loader asc"><i class="far fa-calendar-alt"></i> <strong>Arrive</strong></a>';
                                            $sorting_list .= '<a class="sorta arrival-h-2-l hide loader des"><i class="far fa-calendar-alt"></i> <strong>Arrive</strong></a>';
                                        $sorting_list .= '</li>';
                                      
                                        $sorting_list .= '<li class="sortli">';
                                        	$sorting_list .= '<a class="sorta price-l-2-h loader asc"><i class="fas fa-tag"></i> <strong>Price</strong></a>';
                                            $sorting_list .= '<a class="sorta price-h-2-l hide loader des"><i class="fas fa-tag"></i> <strong>Price</strong></a>';
                                        $sorting_list .= '</li>';
                                    $sorting_list .= '</ul>';
                                    echo $sorting_list; ?>
                                </div>
                            </div>
                            </div>
                        </div>
                         <?php
                         // debug($flight_search_params);exit;
                          if($flight_search_params['trip_type_label']=="Round Way" && $flight_search_params['is_domestic']==true) { ?>
                        <div class="topmisty" id="top-sort-list-2">
                            <div class="col-xs-10 nopad divinsidefltr">
                                <div class="insidemyt">
                                    <?=$sorting_list?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <!-- FLIGHT SEARCH RESULT START -->
					<div  class="allresult" id="flight_search_result">

					<div class="fl width100 fltRndTripWrap" id="onwFltContainer">
                            <!-- <div class="fl padTB5 width100">
                            </div> -->
                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>
                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>
                        </div>

					</div>
					<div  class="" id="empty_flight_search_result" style="display:none">
                    
                    	<div class="noresultfnd">
                        	<div class="imagenofnd"><img src="<?=$template_images?>empty.jpg" alt="Empty" /></div>
                            <div class="lablfnd">No Result Found!!!</div>
                        </div>
                    
					</div>
					<!-- FLIGHT SEARCH RESULT END -->
				</div>
			</div>
		</div>
	</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fas fa-plane"></i> Oops!</h1>
		<p>No flights were found for this route today.</p>
		<p>
		Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>

	<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_session_expiry_popup');?>
</section>
<?php echo $GLOBALS['CI']->template->isolated_view('flight/fare_type_alert');?>

<script>
$(document).ready(function() {

	$(".close_fil_box").click(function(){
			$(".coleft").hide();
			$(".resultalls").removeClass("open");
      });

	//************************** **********/
	//airline price sort
	<?php
	$i=1;
	for ($i=1; $i<=2; $i++) ://Multiple Filters Need to be loaded - Balu A
	?>
	$("#top-sort-list-<?=$i?> .price-l-2-h").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .price-h-2-l').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.f-price:first',
			item: '.r-r-i',
			order: 'asc',
			is_num: true
		});
	});
	$("#top-sort-list-<?=$i?> .price-h-2-l").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .price-l-2-h').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.f-price:first',
			item: '.r-r-i',
			order: 'desc',
			is_num: true
		});
	});
	//airline name sort
	$("#top-sort-list-<?=$i?> .name-l-2-h").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .name-h-2-l').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.a-n:first',
			item: '.r-r-i',
			order: 'asc',
			is_num: false
		});
	});
	$("#top-sort-list-<?=$i?> .name-h-2-l").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .name-l-2-h').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.a-n:first',
			item: '.r-r-i',
			order: 'desc',
			is_num: false
		});
	});
	//duration sort
	$("#top-sort-list-<?=$i?> .duration-l-2-h").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .duration-h-2-l').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.f-d:first',
			item: '.r-r-i',
			order: 'asc',
			is_num: true
		});
	});
	$("#top-sort-list-<?=$i?> .duration-h-2-l").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .duration-l-2-h').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.f-d:first',
			item: '.r-r-i',
			order: 'desc',
			is_num: true
		});
	});
	//departure name sort
	$("#top-sort-list-<?=$i?> .departure-l-2-h").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .departure-h-2-l').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.fdtv:first',
			item: '.r-r-i',
			order: 'asc',
			is_num: true
		});
	});
	$("#top-sort-list-<?=$i?> .departure-h-2-l").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .departure-l-2-h').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.fdtv:first',
			item: '.r-r-i',
			order: 'desc',
			is_num: true
		});
	});
	//arrival sort
	$("#top-sort-list-<?=$i?> .arrival-l-2-h").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .arrival-h-2-l').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.fatv:first',
			item: '.r-r-i',
			order: 'asc',
			is_num: true
		});
	});
	$("#top-sort-list-<?=$i?> .arrival-h-2-l").click(function() {
		$(this).addClass('hide');
		$('#top-sort-list-<?=$i?> .arrival-l-2-h').removeClass('hide');
		$("#flight_search_result #t-w-i-<?=$i?>").jSort({
			sort_by: '.fatv:first',
			item: '.r-r-i',
			order: 'desc',
			is_num: true
		});
	});
	<?php
	endfor;
	//End Multiple Filter Looping - Balu A
	?>

	//if ($(window).width() < 550) {
//			$(document).on("click",".madgrid",function(){
//				var search_id = $(this).data('key');
//				$( "#form-id-"+ search_id).submit();
//			});
//			
//		}	

	//show/hide net fare	
	$('.snf_btn').click(function(){
		$(this).toggleClass('active');
		$('.net-fare-tag').toggle();
		var title = 'Show Net Fare' ;
		if( $(this).hasClass('active')){
		   title = 'Show Net Fare';
		}
		else{
			title = 'Hide Net Fare';
		}
		$(this).attr('title', title);
		$('.tag_snf', this).text(function(i, text){
			return text === "SNF" ? "HNF" : "SNF";
		});
	});
});
</script>
<?php
echo $this->template->isolated_view('share/media/flight_search');
?>
<style type="text/css">
	.mrinfrmtn > i {
  color: #800e0e;
}
</style>
