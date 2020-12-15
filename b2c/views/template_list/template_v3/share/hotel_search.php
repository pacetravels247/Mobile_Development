<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_suggest.js'), 'defer' => 'defer');
//Read cookie when user has not given any search
//debug($hotel_search_params);die('77');
$IP = $_SERVER['REMOTE_ADDR'];
$computerName = gethostbyaddr($IP);
if($IP == '192.168.0.59'){
    //debug($hotel_search_params);die('7878');
}
if ((isset($hotel_search_params) == false) || (isset($hotel_search_params) == true && valid_array($hotel_search_params) == false)) {
	$sparam = $this->input->cookie('sparam', TRUE);
	$sparam = unserialize($sparam);
	$sid = intval(@$sparam[META_ACCOMODATION_COURSE]);
	if ($sid > 0) {
		$this->load->model('hotel_model');
		$hotel_search_params = $this->hotel_model->get_safe_search_data($sid, true);
		$hotel_search_params = $hotel_search_params['data'];
		
		if (valid_array($hotel_search_params) == true) {
			if (strtotime(@$hotel_search_params['hotel_checkin']) < time()) {
				$hotel_search_params['hotel_checkin'] = date('d-m-Y', strtotime(add_days_to_date(3)));
				$hotel_search_params['hotel_checkout'] = date('d-m-Y', strtotime(add_days_to_date(5)));
			}
		}
	}
}

$hotel_datepicker = array(array('hotel_checkin', FUTURE_DATE_DISABLED_MONTH), array('hotel_checkout', FUTURE_DATE_DISABLED_MONTH));
$GLOBALS['CI']->current_page->set_datepicker($hotel_datepicker);
$GLOBALS['CI']->current_page->auto_adjust_datepicker(array(array('hotel_checkin', 'hotel_checkout')));

if (isset($hotel_search_params['room_count']) == true) {
	$room_count_config = intval($hotel_search_params['room_count']);
} else {
	$room_count_config = 1;
}

if (isset($hotel_search_params['adult_config']) == true) {
	$room_adult_config = $hotel_search_params['adult_config'];
} else {
	$room_adult_config = array(2,1,1);
}
// debug($room_adult_config);exit;
if (isset($hotel_search_params['child_config']) == true) {
	$room_child_config = $hotel_search_params['child_config'];
} else {
	$room_child_config = array(0);
}

if (isset($hotel_search_params['child_age']) == true) {
	$room_child_age_config = $hotel_search_params['child_age'];
} else {
	$room_child_age_config = array(1);
}
$loc_div_style = 'style="display: none;"';
$city_div_style = '';
$location_check ='';
$search_type = 'city_search';
$radius ='';
$location ='';
if($hotel_search_params['search_type'] == 'location_search'){
	$loc_div_style = '';
	$city_div_style = 'style="display: none;"';
	$location_check = 'checked';
	$search_type = 'location_search';
	$location = $hotel_search_params['location'];
	$radius = $hotel_search_params['radius'];
	// echo debug($hotel_search_params);exit;
}

// echo debug($hotel_search_params);exit;
?>
<form name="hotel_search" id="hotel_search" autocomplete="on" action="<?php echo base_url().'index.php/general/pre_hotel_search' ?>">
	<div class="tabspl forhotelonly">
		<div class="tabrow htl_srch">
			<div class="col-lg-4 col-md-3 col-sm-6 col-xs-5 padfive full_clear">
				<div id="citysearch" <?php echo $city_div_style; ?>>	
					<div class="lablform">Going to</div>
					<div class="plcetogo plcemark sidebord">
					<input type="text" id="hotel_destination_search_name" autocomplete="off" class="hotel_city normalinput form-control input_nrml" placeholder="Region, City, Area (Worldwide)" name="city" required value="<?php echo @$hotel_search_params['location']?>"/>
					<input class="hide loc_id_holder" name="hotel_destination" type="hidden" value="<?=@$hotel_search_params['hotel_destination']?>" >
					<input
							class="hide rz_city_id_holder" name="rz_city_cd" type="hidden"
							value="<?=@$hotel_search_params['rz_city_code']?>">
							<input
							class="hide rz_country_id_holder" name="rz_country_cd" type="hidden"
							value="<?=@$hotel_search_params['rz_country_code']?>">
					</div>
					
				</div>
				<div id="addlocation" <?php echo $loc_div_style; ?>>
				 <div class="col-md-8 col-sm-8 col-xs-12 mobile_width padfive">
				 <div class="lablform">Location</div>
		   				<div class="plcetogo plcemark sidebord">
					<input type="text" id="pac-input" name="location" placeholder="Region, City, Area (Worldwide)" class="normalinput form-control input_nrml" value="<?php echo @$hotel_search_params['location']?>"/>
					</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12 mobile_width padfive">
		   					<div class="lablform">Distances</div>
		   					<div class="plcetogo selctmark sidebord">
					<select class="normalsel padselct arimo" id="room_adult_configius" name="radius">
					<option value="1" <?php if($radius == 1){ echo 'selected'; }?> >1 KM</option>
					<option value="2" <?php if($radius == 2){ echo 'selected'; }?>>2 KMs</option>
					<option value="5" <?php if($radius == 5){ echo 'selected'; }?> >5 KMs</option>
					<option value="10" <?php if($radius == 10){ echo 'selected'; }?> >10 KMs</option>
					<option value="15" <?php if($radius == 15){ echo 'selected'; }?> >15 KMs</option>
					<option value="20" <?php if($radius == 20){ echo 'selected'; }?> >20 KMs</option>
					</select>
					</div>
		   		</div>
					<input type="hidden" name="latitude" id="latitude" value="<?php echo $hotel_search_params['latitude'] ?>">
			        <input type="hidden" name="longitude" id="longitude" value="<?php echo $hotel_search_params['longitude'] ?>">
			        <input type="hidden" name="countrycode" id="country_code" value="<?php echo $hotel_search_params['countrycode'] ?>">		
				</div>
				<input type="hidden" name="search_type" id="search_type" value="<?php echo $search_type; ?>">
				<div class="clearfix"></div>
				<div class="pull-left location-search" id="locationsearch">
						 <div class="squaredThree3">
							<input type="checkbox" name="location_check" id='locationsmap' value="30" <?php echo $location_check; ?>>
							<label for="locationsmap"></label>
						 </div>
						 <label for="locationsmap" class="lbllbl">Locations Search</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-7 nopad full_clear">
				<div class="col-md-6 col-xs-6 padfive">
					<div class="lablform">Check-in</div>
					<div class="plcetogo datemark sidebord">
						<input  type="text" class="form-control normalinput input_nrml" data-date="true" readonly name="hotel_checkin" id="hotel_checkin"  placeholder="Check-In" required value="<?php echo @$hotel_search_params['from_date']?>"/>
					</div>
				</div>
				<div class="col-md-6 col-xs-6 padfive">
					<div class="lablform">Check-out</div>
					<div class="plcetogo datemark sidebord">
						<input type="text" class="normalinput form-control input_nrml" data-date="true" readonly name="hotel_checkout" id="hotel_checkout" placeholder="Check-Out" required value="<?php echo @$hotel_search_params['to_date']?>"/>
					</div>
				</div>
			</div>
			<div class="col-md-5 col-xs-12 nopad">
				<div class="col-md-6 col-sm-4 col-xs-4 mobile_width padfive">
					<div class="lablform">No.of Nights</div>
					<div class="plcetogo nitmark selctmark sidebord">
						<select class="normalsel padselct arimo input_nrml" id="no_of_nights">
							<?php
								$no_of_days = intval(get_date_difference(@$hotel_search_params['from_date'], @$hotel_search_params['to_date']));
								for ($i = 1; $i <= 10; $i++) {
									if ($i == $no_of_days) {
										$selected = 'selected="selected"';
									} else {
										$selected = '';
									}
									?>
									<option <?=$selected?>><?=$i?></option>
							<?php
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-sm-4 col-xs-4 mobile_width padfive">
					<div class="lablform">Travellers & class</div>
					<div class="totlall trveller ">
						<input type="hidden" value="<?=$room_count_config?>" id="room-count" name="rooms" min="1" max="3">
						<span class="remngwd" id="hotel-pax-summary">2 Adults, 1 Room</span>
						<div class="roomcount">
							<div class="inallsn">
							<?php
							//Max Rooms
							$max_rooms = 3;
							$min_adults = 2;
							$min_child = 0;
							$max_child = 2;
							$room = 0;
							$child_age_index = 0;
							$visible_rooms = intval($room_count_config);
							for ($room = 1; $room <= $max_rooms; $room++) {
								if (intval($room) > $visible_rooms) {
									$room_visibility = 'display:none';
								} else {
									$room_visibility = '';
								}
								$current_room_child_count = intval(@$room_child_config[($room-1)]);
							?>
								<!-- Room 1 -->
								<div class="oneroom" id="room-wrapper-<?=$room?>" style="<?=$room_visibility?>">
								<div class="mobile_adult_icon">Travellers<i class="fa fa-male"></i></div>
									<div class="roomone">Room <?=$room?></div>
									<div class="roomrow">
										<div class="celroe col-xs-4">Adults<span class="agemns">(12+)</span></div>
										<div class="celroe col-xs-8">
											<div class="input-group countmore pax-count-wrapper">
												<span class="input-group-btn">
													<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="adult[]">
														<span class="glyphicon glyphicon-minus"></span>
													</button>
												</span>
												<input type="text" name="adult[]" class="form-control input-number centertext" value="<?=intval(@$room_adult_config[$room-1])?>" min="1" max="4"  id="adult_text_<?=$room-1?>"/>
												<span class="input-group-btn">
													<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="adult[]">
														<span class="glyphicon glyphicon-plus"></span>
													</button>
												</span>
											</div>
										</div>
									</div>
									<div class="roomrow">
										<div class="celroe col-xs-4">Children<span class="agemns">(0-11+)</span></div>
										<div class="celroe col-xs-8">
											<div class="input-group countmore pax-count-wrapper">
												<span class="input-group-btn">
													<button type="button" class="btn btn-default btn-number" data-type="minus" data-field="child[]">
														<span class="glyphicon glyphicon-minus"></span>
													</button>
												</span>
												<input type="text" name="child[]" class="form-control input-number centertext" value="<?=$current_room_child_count?>" min="0" max="2">
												<span class="input-group-btn">
													<button type="button" class="btn btn-default btn-number" data-type="plus" data-field="child[]">
														<span class="glyphicon glyphicon-plus"></span>
													</button>
												</span>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<?php
									if ($current_room_child_count > 0) {
										$child_room_visibility = '';
									} else {
										$child_room_visibility = 'display:none';
									}
									?>
									<div class="chilagediv" style="<?=$child_room_visibility?>">
										<div class="chldrnage">Children's ages at time of travel</div>
										<?php
										$child = 0;
										for ($child=1; $child <= $max_child; $child++) {
											if (($child) > $current_room_child_count) {
												$child_age_visibility = 'display: none;';
												$child_age_value = 1;
											} else {
												$child_age_visibility = '';
												$child_age_value = intval(@$room_child_age_config[$child_age_index+($child-1)]);
											}
										?>
										<div data-child="<?=$child?>" data-currnet="<?=$current_room_child_count?>" class="col-xs-6 padfive child-age-wrapper-<?=$child?>" style="<?=$child_age_visibility?>">
											<div class="mrgnpadd">
											<div class="plcetogo selctmarksml">
												<select name="childAge_<?=$room?>[]" class="normalsel padselctsmal arimo">
													<?=generate_options(numeric_dropdown(array('size' => 11)), array($child_age_value))?>
												</select>
											</div>
											</div>
										</div>
										<?php
										}
										if ($current_room_child_count > 0) {
											$child_age_index += $max_child;
										}
										?>
									</div>
								</div>
							<?php
							}
							?>
							</div>
								<div class="clearfix"></div>
								<div class="add_remove">
									<div class="col-xs-6 nopad">
										<button class="remove_rooms comnbtn_room"> <span class="fa fa-minus-circle"></span>Remove room </button>
									</div>
									<div class="col-xs-6 nopad">
										<button class="add_rooms comnbtn_room"> <span class="fa fa-plus-circle"></span>Add room </button>
									</div>
								</div>
                                <div class="clearfix"></div>
								<a class="done1 comnbtn_room1"><span class="fa fa-check"></span> Done</a>
						</div>
					</div>
				</div>
			
			</div>
				<div class="col-xs-12 mobile_width padfive">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot searchbtn">
						<input type="submit" id="hotel-form-submit" class="searchsbmt searchbtn_inpt" value="search" />
					</div>
				</div>
		</div>
	</div>
</form>
<span class="hide">
<input type="hidden" id="pri_visible_room" value="<?=$visible_rooms?>">
</span>
<script type="text/javascript">
function initMap() {
  var input = document.getElementById('pac-input');
  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.addListener('place_changed', function() {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
        // User entered the name of a Place that was not suggested and
        // pressed the Enter key, or the Place Details request failed.
        window.alert("No details available for input: '" + place.name + "'");
        return;
    }
    document.getElementById("latitude").value = place.geometry.location.lat();
    document.getElementById("longitude").value = place.geometry.location.lng();
    for (var i = 0; i < place.address_components.length; i++) {
	    var addressType = place.address_components[i].types[0];
	    // for the country, get the country code (the "short name") also
	    if (addressType == "country") {
	      document.getElementById("country_code").value = place.address_components[i].short_name;
	    }
  	}
	var componentForm = {
	  locality: 'long_name',
	  administrative_area_level_1: 'short_name',
	  country: 'long_name',
	};
	// document.getElementById("country_code").value = place.address_components[4].short_name;
   
    // console.log(place.geometry.location.lat());
  });
}
	$(function () {
        $("#locationsmap").click(function () {
            if ($(this).is(":checked")) {
                $("#addlocation").show();
                $("#citysearch").hide();
                $("#search_type").val('location_search');
                $("#pac-input").attr("required" ,"required");
                $('#hotel_destination_search_name').removeAttr("required");
                
            } else {
                $("#addlocation").hide();
                $("#citysearch").show();
                $("#search_type").val('city_search');
                $('#hotel_destination_search_name').attr("required" ,"required");
            }
        });
    });
</script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiR9CLZshY_vQpB7z5M7nIGCg16gfo2E8&libraries=places&callback=initMap"
        async defer></script>