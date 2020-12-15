<?php

//Read cookie when user has not given any search
if ((isset($flight_search_params) == false) || (isset($flight_search_params) == true && valid_array($flight_search_params) == false)) {
	//parse_str(get_cookie('flight_search'), $flight_search_params);
	$sparam = $this->input->cookie('sparam', TRUE);
	$sparam = unserialize($sparam);
	$sid = intval(@$sparam[META_AIRLINE_COURSE]);
 
	$flight_search_params = array();
	if ($sid > 0) {
		$this->load->model('flight_model');
		$flight_search_params = $this->flight_model->get_safe_search_data($sid, true);
		
		$flight_search_params = @$flight_search_params['data'];
		if ($flight_search_params['trip_type'] != 'multicity' && strtotime(@$flight_search_params['depature']) < time() ) {
			$flight_search_params['depature'] = date('d-m-Y');
			if (isset($flight_search_params['return']) == true) {
				$flight_search_params['return'] = date('d-m-Y', strtotime(add_days_to_date(1)));
			}
		}
	}
}
$onw_rndw_segment_search_params = array();
$multicity_segment_search_params = array();
if(@$flight_search_params['trip_type'] != 'multicity') {
	$onw_rndw_segment_search_params = $flight_search_params;
} else {//MultiCity
	$multicity_segment_search_params = $flight_search_params;
}
$flight_datepicker = array(array('flight_datepicker1', FUTURE_DATE_DISABLED_MONTH), array('flight_datepicker2', FUTURE_DATE_DISABLED_MONTH));
$this->current_page->set_datepicker($flight_datepicker);
$airline_list = $GLOBALS['CI']->db_cache_api->get_airline_code_list();
if(isset($flight_search_params['adult_config']) == false || intval($flight_search_params['adult_config']) < 1) {
	$flight_search_params['adult_config'] = 1;
}
?>
<form autocomplete="off" name="flight" id="flight_form" action="<?php echo base_url();?>index.php/general/pre_flight_search" method="get" class="activeForm oneway_frm" style="">
	<div class="tabspl">
		<div class="tabrow">
			<div class="waywy">
				<div class="smalway trip_radio">
					<label class="wament lttb hand-cursor">
						<input class="" type="radio" name="trip_type" <?=(isset($flight_search_params['trip_type']) == false ? 'checked' : ($flight_search_params['trip_type']) == 'oneway' ? 'checked="checked"' : '')?> id="onew-trp" value="oneway" /><label for="onew-trp"> One way </label>
					</label>
					<label class="wament lttb hand-cursor">
						<input class="" type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'circle' ? 'checked="checked"' : '')?> id="rnd-trp" value="circle"  /><label for="rnd-trp"> Roundtrip</label><span> / Return</span>
					</label>
					<label class="wament lttb hand-cursor">
						<input class="" type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'multicity' ? 'checked="checked"' : '')?> id="multi-trp" value="multicity" /><label for="multi-trp"> Multi-city</label>
					</label>
				</div>
                                  <div class="col-xs-5 col-md-1 nopad pull-right">
                <div class="searchsbmt_speak hide">

                    <a id="rec" class="btn btn-raised btn-default mobbtn dropdown-toggle" data-toggle="dropdown" style="border-radius: 50%; background: #e6e6e6; height: 40px; width: 40px;"><img style=" width: 14px; padding-top: 2px;" src="<?php echo $GLOBALS['CI']->template->template_images('mike.png'); ?>">
                    <span class="beta">Beta</span></a>

                    <div class="dropdown-menu speak_bar_box">
                       <div class="speak_static_text">Please say something like this</div>
                       <div class="bot-text-speak">
		                       <span id="chat-dialog-queue-span" style="opacity: 1;"><i>“</i> Flight for 2 adults from Delhi to Mumbai <i>”</i></span>
                       </div>

                   <div class="mike-noanimate" id="mikewave">
                    <div class="sound-icon">
                      <div class="mike-wave">
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                        <i class="barr"></i>
                      </div>
                    </div>
                  </div>

                    </div>

                </div>

            </div>

				<div class="col-xs-5 col-md-2 nopad pull-right hide">
				<button class="farhomecal" id="flight_fare_calendar"><span class="fal fa-calendar-alt"></span> Fare Calendar</button>
			</div>
			</div>
            
            <div class="searcharea_box_inpt">
			<div id="onw_rndw_fieldset" class="col-md-10 nopad"><!-- Oneway/Roundway Fileds Starts-->
				<div class="col-md-7 nopad placerows">
					<div class="col-xs-6 nopad">
						<div class="lablform">From</div>
						<div class="plcetogo deprtures sidebord">
							<input type="text" autocomplete="off" name="from" class="normalinput  input_nrml auto-focus valid_class fromflight form-control " id="from" placeholder="Type Departure City" value="<?php echo @$onw_rndw_segment_search_params['from'] ?>" required />
							<input class="hide loc_id_holder" id="from_loc_id" name="from_loc_id" type="hidden" value="<?=@$onw_rndw_segment_search_params['from_loc_id']?>" >

					<div class="flight_chnge"><i class="far fa-exchange rot_arrow"></i></div>
						</div>
						
					</div>
					<div class="col-xs-6 nopad">
						
						<div class="plcetogo destinatios sidebord">
							<div class="lablform">To</div>
							<input type="text" autocomplete="off" name="to"  class="normalinput input_nrml auto-focus valid_class departflight form-control " id="to" placeholder="Type Destination City" value="<?php echo @$onw_rndw_segment_search_params['to'] ?>" required/>
							<input class="hide loc_id_holder" id="to_loc_id" name="to_loc_id" type="hidden" value="<?=@$onw_rndw_segment_search_params['to_loc_id']?>" >
						</div>
					</div>
				</div>
				<div class="col-md-5 nopad secndates">
					<div class="col-xs-6 nopad">
						<div class="lablform">Departure</div>
						<div class="plcetogo datemark sidebord datepicker_new1" iditem="flight_datepicker1">
							<input type="text" readonly class="normalinput input_nrml auto-focus hand-cursor form-control " id="flight_datepicker1" placeholder="Select Date" value="<?php echo @$onw_rndw_segment_search_params['depature'] ?>" name="depature" required/>
						</div>
					</div>
					<div class="col-xs-6 nopad date-wrapper">
						<div class="lablform">Return</div>
						<div class="plcetogo datemark sidebord datepicker_new2" iditem="flight_datepicker2">
							<input type="text" readonly class="normalinput input_nrml auto-focus hand-cursor form-control " id="flight_datepicker2" name="return" placeholder="Select Date" value="<?php echo @$onw_rndw_segment_search_params['return'] ?>" <?=(@$onw_rndw_segment_search_params['trip_type'] != 'circle' ? 'disabled="disabled"' : '')?> />
						</div>
					</div>
				</div>
			</div><!-- Oneway/Roundway Fileds Ends-->
            
            
			<?=$GLOBALS['CI']->template->isolated_view('share/flight_multi_way_search', array('multicity_segment_search_params' => $multicity_segment_search_params))?><!-- Multiway-->
            
			<div class="col-md-2 col-xs-12  thrdtraveller nopad">
				<div class="col-xs-12 nopad mobile_width">
					<div class="lablform">Travellers & class</div>
					<div class="totlall trveller ">
						<span class="remngwd"><span class="total_pax_count"></span> <span id="travel_text">Traveller</span></span>
						<div class="roomcount pax_count_div">

						<?php
				//debug($flight_search_params);exit;
						//Airline Class
						// $v_class = array('Economy' => 'Economy', 'PremiumEconomy' => 'Premium Economy', 'Business' => 'Business', 'PremiumBusiness' => 'Premium Business', 'First' => 'First');
						$v_class = array('Economy' => 'Economy', 'PremiumEconomy' => 'Premium Economy','Business' => 'Business');
						$airline_classes = '';
						if(isset($flight_search_params['v_class']) == true && empty($flight_search_params['v_class']) == false) {
							$choosen_airline_class = $v_class[$flight_search_params['v_class']];
							$irline_class_value = $flight_search_params['v_class'];
							//$air_class ='';
						} else {
							$choosen_airline_class = 'Economy';
							$irline_class_value = 'Economy';
							//$air_class = 'active';
						}
						foreach($v_class as $v_class_k => $v_class_v) {
							if($v_class_v == $choosen_airline_class){
								$air_class = 'active';
							}
							else{
								$air_class ='';
							}
							$airline_classes .= '<a class="adscrla choose_airline_class '.$air_class.'" data-airline_class="'.$v_class_k.'">'.$v_class_v.'</a>';
						}
						//Preferred Airlines
						if(isset($flight_search_params['carrier']) == true && empty($flight_search_params['carrier']) == false &&  $flight_search_params['carrier'] != 'all') {
							$choosen_airline_name = $airline_list[$flight_search_params['carrier']];
						} else {
							$choosen_airline_name = 'Preferred Airline';
						}
						$preferred_airlines = '<a class="adscrla choose_preferred_airline" data-airline_code="">All</a>';
						foreach($airline_list as $airline_list_k => $airline_list_v) {
							$preferred_airlines .= '<a class="adscrla choose_preferred_airline" data-airline_code="'.$airline_list_k.'">'.$airline_list_v.'</a>';
						}
					?>
                   <div class="advance_opt">
					<div class="col-xs-12 nopad">
					<div class="lablform2">Cabin Class</div>
						<div class="alladvnce">
							<span class="remngwd" id="choosen_airline_class"><?php echo $choosen_airline_class;?></span>
							<input type="hidden" autocomplete="off" name="v_class" id="class" value="<?php echo $irline_class_value;?>" >
							<div class="advncedown spladvnce class_advance_div">
								<div class="inallsnnw">
									<div class="scroladvc">
										<?php echo $airline_classes;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 nopad">
					<div class="lablform2"> </div>
						<div class="alladvnce">
							<select class="js-example-basic-single" name="conn_direct">
								<!-- <option>Routing</option> -->
								<?php echo generate_options(get_enum_list('conn_direct'), array(@$_GET["conn_direct"])); ?>
							</select>
						</div>
					</div>
					<div class="col-xs-12 nopad">
					<div class="lablform2">Preferred Airline</div>
						<div class="alladvnce">
						<select class="js-example-basic-single" name="carrier">
						<option value="0">All</option>
										<?php 

										foreach($airline_list as $airline_list_k => $airline_list_v) {?>
										
										  <option value="<?php echo $airline_list_k; ?>"><?php echo $airline_list_v; ?></option>
										  <?php } ?>
										 </select>
                                                    <?php  //debug($flight_search_params);exit; ?>
<!-- 						<select id="airline_select" style="width:300px;">
									
									</select> -->
						<!-- 	<span class="remngwd" id="choosen_preferred_airline"><?php //echo $choosen_airline_name;?></span> -->
							<!-- <input type="hidden" autocomplete="off" name="carrier[]" id="carrier" value="<?php echo @$flight_search_params['carrier'][0];?>" > -->
							<!-- <div class="advncedown spladvnce preferred_airlines_advance_div">

								<div class="inallsnnw">
									<div class="scroladvc">
										
										<?php //echo $preferred_airlines;?>
									</div>
								</div>
							</div> -->
						</div>
					</div>
					</div>

						<div class="mobile_adult_icon">Travellers<i class="fa fa-male"></i></div>
						
							<div class="inallsn">
								<div class="oneroom fltravlr">
								<div class="lablform2">Travellers</div>
									<div class="clearfix"></div>
									<div class="roomrow">

										<div class="celroe col-xs-7"><i class="fal fa-male"></i> Adults
											<span class="agemns">(12+)</span>
										</div>
										<div class="celroe col-xs-5">
											<div class="input-group countmore pax-count-wrapper adult_count_div"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="adult"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_adult" name="adult" class="form-control input-number centertext valid_class pax_count_value" value="<?=(int)@$flight_search_params['adult_config']?>" min="1" max="9" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="adult"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<div class="roomrow">
										<div class="celroe col-xs-7"><i class="fal fa-child"></i> Children
											<span class="agemns">(2-11)</span>
										</div>
										<div class="celroe col-xs-5">
											<div class="input-group countmore pax-count-wrapper child_count_div"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="child"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_child" name="child" class="form-control input-number centertext pax_count_value" value="<?=(int)@$flight_search_params['child_config']?>" min="0" max="9" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="child"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<div class="roomrow last">
										<div class="celroe col-xs-7"><i class="fal fa-child"></i> Infants
											<span class="agemns">(0-2)</span>
										</div>
										<div class="celroe col-xs-5">
											<div class="input-group countmore pax-count-wrapper infant_count_div"> <span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="infant"> <span class="glyphicon glyphicon-minus"></span> </button>
												</span>
												<input type="text" id="OWT_infant" name="infant" class="form-control input-number centertext pax_count_value" value="<?=(int)@$flight_search_params['infant_config']?>" min="0" max="9" readonly>
												<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="infant"> <span class="glyphicon glyphicon-plus"></span> </button>
												</span> 
											</div>
										</div>
									</div>
									<!-- Infant Error Message-->
									<div class="roomrow last">
										<div class="celroe col-xs-12">
										<div class="alert-wrapper hide">
										<div role="alert" class="alert alert-error">
											<span class="alert-content"></span>
										</div>
										</div>
										</div>
									</div>
									<a class="done1 comnbtn_room1"><span class="fa fa-check"></span> Done</a>
									<!-- Infant Error Message-->
								</div>
							</div>
							<div class="col-xs-12 padfive mobile_width">
								<!-- <div class="lablform2">Type Of Flights</div> -->
								<div class="fs_checkboxes">
									<div class="col-xs-6">                            
									  <div class="squaredThree"> 
									  <?php 
									  // debug($flight_search_params);exit;
									  if(isset($flight_search_params['lcc_gds'])){
									  	if(in_array('lcc_apis',$flight_search_params['lcc_gds'])){
									  		$lcc_checked = 'checked="checked"';
									  	}
									  }
									  else{
									  	$lcc_checked = 'checked="checked"';
									  }
									  if(isset($flight_search_params['lcc_gds'])){
									  	if(in_array('gds_apis',$flight_search_params['lcc_gds'])){
									  		$gds_checked = 'checked="checked"';
									  	}
									  }
									  else{
									  		$gds_checked = 'checked="checked"';
									  }
									  ?>                     
									  	<input id="terms_cond1" type="checkbox" name="lcc_gds[]" <?php echo $lcc_checked; ?> value="lcc_apis"> 
									  	<label for="terms_cond1"></label></div>                            
									  	<span class="clikagre" id="clikagre">LCC
		                            </span>     
		                        </div>
		                        <div class="col-xs-6">
		                        <div class="squaredThree">                                
									  	<input id="terms_cond2" type="checkbox" name="lcc_gds[]" <?php echo $gds_checked; ?> value="gds_apis"> 
									  	<label for="terms_cond2"></label>                            
									  </div>                            
									  	<span class="clikagre" id="clikagre">GDS
		                            </span>     
		                        <!-- </div> -->
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
                <div class="clearfix"></div>
                <div class="col-xs-6 nopad">
					<button style="display:none" class="add_city_btn" id="add_city"> <span class="fa fa-plus"></span> Add City</button>
				</div>


			</div>
			<div class="col-xs-12 nopad mobile_width">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot searchbtn">
						<input type="submit" name="search_flight" id="flight-form-submit" class="searchsbmt flight_search_btn searchbtn_inpt" value="search" />
					</div>
				</div>


		</div>
			<div class="clearfix"></div>
			<div class="alert-box" id="flight-alert-box"></div>
			<div class="clearfix"></div>
			
			
		</div>
	</div>
</form>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script>
$(document).ready(function() {
	var select_val ='<?php if(isset($flight_search_params['carrier'])){ echo $flight_search_params['carrier']; } ?>';
	//alert(select_val);
    $('.js-example-basic-single').select2();
    //$('.js-example-basic-single').select2().select2('val',select_val)
      $(".flight_chnge").click(function(){
   var from = $('#from').val();
   var from_loc_id = $('#from_loc_id').val();

   var to = $('#to').val();
   var to_loc_id = $('#to_loc_id').val();


   $('#from').val(to);
   $('#to').val(from);

   $('#from_loc_id').val(to_loc_id);
   $('#to_loc_id').val(from_loc_id);

   $(".flight_chnge .fa-exchange").toggleClass('rot_arrow');

  });

  //  $('.close-modify-section').click(function(){
  //  		$('.splmodify').slideToggle(400);
  //  		$(this).stop( true, true ).toggleClass('up');
		// $('.search-result').stop( true, true ).toggleClass('flightresltpage');
		// $('.modfictions').stop( true, true ).toggleClass('fixd');
  //  })




});
$( document ).on( 'focus', ':input', function(){
    $( this ).attr( 'autocomplete', 'new-username' );
});
</script>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_suggest.js'), 'defer' => 'defer');
?>
