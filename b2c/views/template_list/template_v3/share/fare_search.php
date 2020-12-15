<?php
$flight_datepicker = array(array('flight_datepicker1', FUTURE_DATE_DISABLED_MONTH), array('flight_datepicker2', FUTURE_DATE_DISABLED_MONTH));
$this->current_page->set_datepicker($flight_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('flight_datepicker1', 'flight_datepicker2')));
$airline_list = $GLOBALS['CI']->db_cache_api->get_airline_code_list();
if(isset($flight_search_params['adult_config']) == false || intval($flight_search_params['adult_config']) < 1) {
	//Balu A - By default set adult count to 1 if it is not set
	$flight_search_params['adult_config'] = 1;
}
?>
<form autocomplete="off" name="flight" id="flight_form" action="<?php echo base_url();?>index.php/flight/calendar_fare" method="get" class="activeForm oneway_frm" style="">
	<div class="tabspl nopad">
		<div class="tabrow">
			<div class="waywy hide">
				<div class="smalway">
					<label class="wament hand-cursor">
						<input class="" type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'oneway' ? 'checked="checked"' : '')?> id="onew-trp" value="oneway" checked> One Way
					</label>
					<label class="wament hand-cursor">
						<input class="" type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'circle' ? 'checked="checked"' : '')?> id="rnd-trp" value="circle"> Round Way
					</label>
				</div>
			</div>

			<div class="col-xs-12 padfive marg12">
				<div class="lablform">From</div>
				<div class="plcetogo deprtures">
					<input type="text" autocomplete="off" name="from" class="normalinput auto-focus valid_class fromflight form-control b-r-0" id="from" placeholder="Type Departure City" value="<?php echo @$flight_search_params['from'] ?>" required />
				</div>
			</div>
			<div class="col-xs-12 padfive marg12">
				<div class="lablform">To</div>
				<div class="plcetogo destinatios">
					<input type="text" autocomplete="off" name="to"  class="normalinput auto-focus valid_class departflight form-control b-r-0" id="to" placeholder="Type Destination City" value="<?php echo @$flight_search_params['to'] ?>" required/>
				</div>
			</div>


				<div class="col-xs-12 padfive marg12">
					<div class="lablform">Month</div>
					<div class="plcetogo">
						<div class="selectedwrap datemark ">
						<select class="form-control normalinput" name="depature" required >
							<?php
							$month_count = 13;
							$departure_month = strtotime($flight_search_params['depature']);
							for ($month_count = 0; $month_count < 12; $month_count++) {
								$month_date = strtotime(add_months_to_date($month_count));
								$month_start_date = strtotime(date('Y-m', $month_date).'-1');
								if ($departure_month == $month_start_date) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo '<option '.$selected.' value="'.date('Y-m', $month_date).'-01">'.date('M Y', $month_date).'</option>';
							}
							?>
						</select>
						</div>
					</div>
				</div>
				
				
				<div class="col-xs-12 padfive date-wrapper hide">
					<div class="lablform">Return</div>
					<div class="plcetogo datemark sidebord">
						<input type="text" readonly class="normalinput auto-focus hand-cursor form-control b-r-0" id="flight_datepicker2" name="return" placeholder="Select Date" value="<?php echo @$flight_search_params['return'] ?>" <?=(@$flight_search_params['trip_type'] != 'circle' ? 'disabled="disabled"' : '')?> />
					</div>
				</div>
			
			<div class="col-md-12 nopad thrdtraveller">
				<div class="col-xs-12 padfive">
					<div class="lablform">Traveller(s)</div>
					<div class="totlall">
						<span class="remngwd"><span class="total_pax_count"></span> Traveller(s)</span>
						<div class="roomcount pax_count_div">
							<div class="inallsn">
								<div class="oneroom fltravlr">
									<div class="roomrow">
										<div class="celroe col-xs-4">Adults
											<span class="agemns">(12+)</span>
										</div>
										<div class="celroe col-xs-8">
											<div class="input-group countmore"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="adult"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_adult" name="adult" class="form-control input-number centertext valid_class pax_count_value" value="<?=(int)@$flight_search_params['adult_config']?>" min="1" max="10" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="adult"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<div class="roomrow hide">
										<div class="celroe col-xs-4">Children
											<span class="agemns">(2-11)</span>
										</div>
										<div class="celroe col-xs-8">
											<div class="input-group countmore"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="child"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_child" name="child" class="form-control input-number centertext pax_count_value" value="<?=(int)@$flight_search_params['child_config']?>" min="0" max="10" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="child"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<div class="roomrow hide">
										<div class="celroe col-xs-4">Infants
											<span class="agemns">(0-2)</span>
										</div>
										<div class="celroe col-xs-8">
											<div class="input-group countmore infant_count_div"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="infant"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_infant" name="infant" class="form-control input-number centertext pax_count_value" value="<?=(int)@$flight_search_params['infant_config']?>" min="0" max="10" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="infant"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<!-- Infant Error Message-->
									<div class="roomrow">
										<div class="celroe col-xs-8">
										<div class="alert-wrapper hide">
										<div role="alert" class="alert alert-danger"> 
											<strong>Note :</strong> <span class="alert-content"></span>
										</div>
										</div>
										</div>
									</div>
									<!-- Infant Error Message-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 padfive">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot">
						<input type="submit" name="search_flight" id="flight-form-submit" class="searchsbmt flight_search_btn" value="search" />
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="togleadvnce hide">
				<div class="advncebtn">
					<div class="labladvnce">Advanced options</div>
					<?php
						//Airline Class
						$v_class = array('All' => 'Any', 'Economy' => 'Economy', 'Business' => 'Business', 'First' => 'First');
						$airline_classes = '';
						if(isset($flight_search_params['v_class']) == true && empty($flight_search_params['v_class']) == false) {
							$choosen_airline_class = $v_class[$flight_search_params['v_class']];
							$irline_class_value = $flight_search_params['v_class'];
						} else {
							$choosen_airline_class = 'Class';
							$irline_class_value = 'All';
						}
						foreach($v_class as $v_class_k => $v_class_v) {
							$airline_classes .= '<a class="adscrla choose_airline_class" data-airline_class="'.$v_class_k.'">'.$v_class_v.'</a>';
						}
						//Preferred Airlines
						if(isset($flight_search_params['carrier'][0]) == true && empty($flight_search_params['carrier'][0]) == false && $flight_search_params['carrier'][0]!= 'all') {
							
							$choosen_airline_name = $airline_list[$flight_search_params['carrier'][0]];
						} else {
							$choosen_airline_name = 'Preferred Airline';
						}
						$preferred_airlines = '<a class="adscrla choose_preferred_airline" data-airline_code="">All</a>';
						foreach($airline_list as $airline_list_k => $airline_list_v) {
							$preferred_airlines .= '<a class="adscrla choose_preferred_airline" data-airline_code="'.$airline_list_k.'">'.$airline_list_v.'</a>';
						}
					?>
				</div>
				<div class="advsncerdch">
					<div class="col-xs-3 nopad">
						<div class="alladvnce">
							<span class="remngwd" id="choosen_airline_class"><?php echo $choosen_airline_class;?></span>
							<input type="hidden" name="v_class" id="class" value="<?php echo $irline_class_value;?>" >
							<div class="advncedown spladvnce class_advance_div">
								<div class="inallsnnw">
									<div class="scroladvc">
										<?php echo $airline_classes;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-3 nopad">
						<div class="alladvnce">
							<span class="remngwd" id="choosen_preferred_airline"><?php echo $choosen_airline_name;?></span>
							<input type="hidden" name="carrier[]" id="carrier" value="<?php echo @$flight_search_params['carrier'][0];?>" >
							<div class="advncedown spladvnce preferred_airlines_advance_div">
								<div class="inallsnnw">
									<div class="scroladvc">
										<?php echo $preferred_airlines;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--
					<div class="col-xs-3 nopad">
						<div class="checkadvnce">
							<div class="squaredThree">
								<input id="squaredThree" type="checkbox" name="check" value="None">
								<label for="squaredThree"></label>
							</div>
							<label class="lbllbl" for="squaredThree">Non Stop Flights Only</label>
						</div>
					</div>-->
				</div>
			</div>
		</div>
	</div>
</form>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_suggest.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
