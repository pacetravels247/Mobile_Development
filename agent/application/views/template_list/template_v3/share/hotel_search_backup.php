<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_suggest.js'), 'defer' => 'defer');
//Read cookie when user has not given any search
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
//echo debug($hotel_destination);exit;
?>
<form name="hotel_search" id="hotel_search" autocomplete="on" action="<?php echo base_url().'index.php/general/pre_hotel_search' ?>">
	<div class="tabspl forhotelonly">
		<div class="tabrow">
			<div class="col-lg-4 col-md-3 col-sm-6 col-xs-5 padfive full_clear">
				<div class="lablform">Going to</div>
				<div class="plcetogo plcemark sidebord">
				<input type="text" id="hotel_destination_search_name" class="hotel_city normalinput form-control b-r-0" placeholder="Region, City, Area (Worldwide)" name="city" required value="<?php echo @$hotel_search_params['location']?>"/>
				<input class="hide loc_id_holder" name="hotel_destination" type="hidden" value="<?=@$hotel_search_params['hotel_destination']?>" >
				</div>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-7 nopad full_clear">
				<div class="col-md-6 col-xs-6 padfive">
					<div class="lablform">Check-in</div>
					<div class="plcetogo datemark sidebord">
						<input  type="text" class="form-control b-r-0 normalinput" data-date="true" readonly name="hotel_checkin" id="hotel_checkin"  placeholder="Check-In" required value="<?php echo @$hotel_search_params['from_date']?>"/>
					</div>
				</div>
				<div class="col-md-6 col-xs-6 padfive">
					<div class="lablform">Check-out</div>
					<div class="plcetogo datemark sidebord">
						<input type="text" class="normalinput form-control b-r-0" data-date="true" readonly name="hotel_checkout" id="hotel_checkout" placeholder="Check-Out" required value="<?php echo @$hotel_search_params['to_date']?>"/>
					</div>
				</div>
			</div>
			<div class="col-md-5 col-xs-12 nopad">
				<div class="col-md-3 col-sm-4 col-xs-4 mobile_width padfive">
					<div class="lablform">No.of Nights</div>
					<div class="plcetogo nitmark selctmark sidebord">
						<select class="normalsel padselct arimo" id="no_of_nights">
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
				<div class="col-md-5 col-sm-4 col-xs-4 mobile_width padfive">
					<div class="lablform">&nbsp;</div>
					<div class="totlall">
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
										<div class="celroe col-xs-4">Adults<br><span class="agemns">(12+)</span></div>
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
										<div class="celroe col-xs-4">Children<br><span class="agemns">(0-11+)</span></div>
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
				<div class="col-md-4 col-sm-4 col-xs-4 mobile_width padfive">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot">
						<input type="submit" id="hotel-form-submit" class="searchsbmt" value="search" />
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<span class="hide">
<input type="hidden" id="pri_visible_room" value="<?=$visible_rooms?>">
</span>