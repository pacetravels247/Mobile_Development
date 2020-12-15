<?php
Js_Loader::$js [] = array (
		'src' => $GLOBALS ['CI']->template->template_js_dir ( 'page_resource/hotel_suggest.js' ),
		'defer' => 'defer' 
);
// Read cookie when user has not given any search
if ((isset ( $hotel_search_params ) == false) || (isset ( $hotel_search_params ) == true && valid_array ( $hotel_search_params ) == false)) {
	$sparam = $this->input->cookie ( 'sparam', TRUE );
	$sparam = unserialize ( $sparam );
	$sid = intval ( @$sparam [META_ACCOMODATION_COURSE] );
	
	if ($sid > 0) {
		$this->load->model ( 'hotel_model' );
		// $hotel_search_params = $this->hotel_model->get_safe_search_data ( $sid, true );
		$hotel_search_params = $GLOBALS ['CI']->hotel_model->get_safe_search_data ( $sid );
		$hotel_search_params = $hotel_search_params ['data'];
		
		if (valid_array ( $hotel_search_params ) == true) {
			if (strtotime ( @$hotel_search_params ['hotel_checkin'] ) < time ()) {
				$hotel_search_params ['hotel_checkin'] = date ( 'd-m-Y', strtotime ( add_days_to_date ( 3 ) ) );
				$hotel_search_params ['hotel_checkout'] = date ( 'd-m-Y', strtotime ( add_days_to_date ( 5 ) ) );
			}
		}
	}
}

$hotel_datepicker = array (
		array (
				'hotel_checkin',
				FUTURE_DATE_DISABLED_MONTH 
		),
		array (
				'hotel_checkout',
				FUTURE_DATE_DISABLED_MONTH 
		) 
);
$GLOBALS ['CI']->current_page->set_datepicker ( $hotel_datepicker );
$GLOBALS ['CI']->current_page->auto_adjust_datepicker ( array (
		array (
				'hotel_checkin',
				'hotel_checkout' 
		) 
) );

if (isset ( $hotel_search_params ['room_count'] ) == true) {
	$room_count_config = intval ( $hotel_search_params ['room_count'] );
} else {
	$room_count_config = 1;
}

if (isset ( $hotel_search_params ['adult_config'] ) == true) {
	$room_adult_config = $hotel_search_params ['adult_config'];
} else {
	$room_adult_config = array (
			2 
	);
}

if (isset ( $hotel_search_params ['child_config'] ) == true) {
	$room_child_config = $hotel_search_params ['child_config'];
} else {
	$room_child_config = array (
			0 
	);
}

if (isset ( $hotel_search_params ['child_age'] ) == true) {
	$room_child_age_config = $hotel_search_params ['child_age'];
} else {
	$room_child_age_config = array (
			1 
	);
}
?>
<div class="col-xs-12 nopad">
	<form name="hotel_search" id="hotel_search" autocomplete="on"
		action="<?php echo base_url().'index.php/general/pre_hotel_search' ?>">
		<div class="tabspl forhotelonly">
			<div class="tabrow">
				<div class="col-md-11 nopad">
						<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12 padfive full_clear">
					<div class="lablform mr7">Going to</div>
					<div class="plcetogo plcemark sidebord">
						<input type="text" id="hotel_destination_search_name"
							class="hotel_city normalinput form-control b-r-0"
							placeholder="Region, City, Area (Worldwide)" name="city" required
							value="<?php echo @$hotel_search_params['location']?>" /> <input
							class="hide loc_id_holder" name="hotel_destination" type="hidden"
							value="<?=@$hotel_search_params['hotel_destination']?>">
							<input
							class="hide rz_city_id_holder" name="rz_city_cd" type="hidden"
							value="<?=@$hotel_search_params['rz_city_code']?>">
							<input
							class="hide rz_country_id_holder" name="rz_country_cd" type="hidden"
							value="<?=@$hotel_search_params['rz_country_code']?>">
							<input
							class="hide grn_city_id_holder" name="grn_city_cd" type="hidden"
							value="<?=@$hotel_search_params['grn_city_code']?>">
							<input
							class="hide oyo_city_holder" name="oyo_city" type="hidden"
							value="<?=@$hotel_search_params['oyo_city']?>">
							<input
							class="hide grn_destination_id_holder" name="grn_destination_cd" type="hidden"
							value="<?=@$hotel_search_params['grn_destination_code']?>">
							<input type="hidden" id="client_nationality" name="client_nationality" value="<?=@($hotel_search_params['client_nationality']?$hotel_search_params['client_nationality']: $this->agent_nationality)?>">
					</div>
				</div>
				<div class="col-md-8 col-sm-8 padfive pTop0 full_clear">
					<div class="col-md-3 col-xs-3 padfive fivfit">
						<div class="lablform">Check-in</div>
						<div class="plcetogo datemark sidebord">
							<input type="text" class="form-control b-r-0 normalinput"
								data-date="true" readonly name="hotel_checkin"
								id="hotel_checkin" placeholder="Check-In" required
								value="<?php echo @$hotel_search_params['from_date']?>" />
						</div>
					</div>
					<div class="col-md-3 col-xs-3 padfive fivfit">
						<div class="lablform">Check-out</div>
						<div class="plcetogo datemark sidebord">
							<input type="text" class="normalinput form-control b-r-0"
								data-date="true" readonly name="hotel_checkout"
								id="hotel_checkout" placeholder="Check-Out" required
								value="<?php echo @$hotel_search_params['to_date']?>" />
						</div>
					</div>



					<div class="col-md-3 col-sm-4 col-xs-4 full_mobile padfive fivfit">
						<div class="lablform">No.of Nights</div>
						<div class="plcetogo nitmark selctmark sidebord">
							<select class="normalsel padselct arimo" id="no_of_nights">
							<?php
							$no_of_days = intval ( get_date_difference ( @$hotel_search_params ['from_date'], @$hotel_search_params ['to_date'] ) );
							for($i = 1; $i <= 10; $i ++) {
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
					<div class="col-md-3 col-sm-4 col-xs-4 full_mobile padfive fivfit">
						<div class="lablform">&nbsp;</div>
						<div class="totlall">
							<input type="hidden" value="<?=$room_count_config?>"
								id="room-count" name="rooms" min="1" max="3"> <span
								class="remngwd" id="hotel-pax-summary">2 Adults, 1 Room</span>
							<div class="roomcount">
								<div class="inallsn">
							<?php
							// Max Rooms
							$max_rooms = 3;
							$min_adults = 2;
							$min_child = 0;
							$max_child = 2;
							$room = 0;
							$child_age_index = 0;
							$visible_rooms = intval ( $room_count_config );
							for($room = 1; $room <= $max_rooms; $room ++) {
								if (intval ( $room ) > $visible_rooms) {
									$room_visibility = 'display:none';
								} else {
									$room_visibility = '';
								}
								$current_room_child_count = intval ( @$room_child_config [($room - 1)] );
								?>
								<!-- Room 1 -->
									<div class="oneroom" id="room-wrapper-<?=$room?>" style="<?=$room_visibility?>">
										<div class="roomone">Room <?=$room?></div>
										<div class="roomrow">
											<div class="celroe col-xs-4">
												Adults<br>
												<span class="agemns">(12+)</span>
											</div>
											<div class="celroe col-xs-8">
												<div class="input-group countmore pax-count-wrapper">
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number"
															data-type="minus" data-field="adult[]">
															<span class="glyphicon glyphicon-minus"></span>
														</button>
													</span> <input type="text" name="adult[]"
														class="form-control input-number centertext"
														value="<?=intval(@$room_adult_config[($room-1)])?>"
														min="1" max="4" /> <span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number"
															data-type="plus" data-field="adult[]">
															<span class="glyphicon glyphicon-plus"></span>
														</button>
													</span>
												</div>
											</div>
										</div>
										<div class="roomrow">
											<div class="celroe col-xs-4">
												Children<br>
												<!-- <span class="agemns">(0-11+)</span> -->
												<span class="agemns">(0-12)</span>
											</div>
											<div class="celroe col-xs-8">
												<div class="input-group countmore pax-count-wrapper">
													<span class="input-group-btn">
														<button type="button" class="btn btn-default btn-number"
															data-type="minus" data-field="child[]">
															<span class="glyphicon glyphicon-minus"></span>
														</button>
													</span> <input type="text" name="child[]"
														class="form-control input-number centertext"
														value="<?=$current_room_child_count?>" min="0" max="2"> <span
														class="input-group-btn">
														<button type="button" class="btn btn-default btn-number"
															data-type="plus" data-field="child[]">
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
								for($child = 1; $child <= $max_child; $child ++) {
									if (($child) > $current_room_child_count) {
										$child_age_visibility = 'display: none;';
										$child_age_value = 1;
									} else {
										$child_age_visibility = '';
										$child_age_value = intval ( @$room_child_age_config [$child_age_index + ($child - 1)] );
									}
									?>
										<div data-child="<?=$child?>" data-currnet="<?=$current_room_child_count?>" class="col-xs-6 padfive child-age-wrapper-<?=$child?>" style="<?=$child_age_visibility?>">
												<div class="mrgnpadd">
													<div class="plcetogo selctmarksml">
														<select name="childAge_<?=$room?>[]"
															class="normalsel padselctsmal arimo">
													<?php //echo generate_options(numeric_dropdown(array('size' => 11)), array($child_age_value))?>
													<?=generate_options(numeric_dropdown(array('size' => 12)), array($child_age_value))?>
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
										<button class="remove_rooms comnbtn_room">
											<span class="fa fa-minus-circle"></span>Remove room
										</button>
									</div>
									<div class="col-xs-6 nopad">
										<button class="add_rooms comnbtn_room">
											<span class="fa fa-plus-circle"></span>Add room
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12 full_mobile padfive pull-right">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot">
						<input type="submit" id="hotel-form-submit" class="searchsbmt"
							value="search" />
					</div>
				</div>

			
		</div>
	</div>
	</form>
	<span class="hide"> <input type="hidden" id="pri_visible_room"
		value="<?=$visible_rooms?>">
	</span>
</div>
<script type="text/javascript">
$(document).ready(function(){
	//alert("HI");
   var check_in=db_date(7);var check_out=db_date(10);
    $('.htd-wrap').on('click',function(e){
      e.preventDefault();
      var curr_destination=$('.top-des-val',this).val();
      var city_id = $('.top_des_id',this).val();
      var rz_id = $('.top_rz_city_id',this).val();
      var rz_cnt_id = $('.top_rz_country_id',this).val();
      var grn_ct_id = $('.top_grn_city_id',this).val();
      var grn_des_id = $('.top_grn_des_id',this).val();
      var oyo_ct = $('.top_oyo_city',this).val();
      var clint = $('.top_client_nationality',this).val();
      $('#hotel_destination_search_name').val(curr_destination);
      $(".loc_id_holder").val(city_id);
      $(".rz_city_id_holder").val(rz_id);
      $(".rz_country_id_holder").val(rz_cnt_id);
      $(".grn_city_id_holder").val(grn_ct_id);
      $(".oyo_city_holder").val(oyo_ct);
      $(".grn_destination_id_holder").val(grn_des_id);
      $("#client_nationality").val(clint);
      $('#hotel_checkin').val(check_in);
      $('#hotel_checkout').val(check_out);
      $('#hotel_search').submit()
   });
});
   </script>



<script type="text/javascript">
   		var logID = 'log',
	    log = $('<div id="'+logID+'"></div>');
		$('body').append(log);
		  $('[type*="radio"]').change(function () {
		    var me = $(this);
		    log.html(me.attr('value'));
		});
   </script>


   <style type="text/css">
   	.id-select-row{
   		background-color: #fff;
   		height: 40px;
   	}
   	.id-select-row p{
   		margin: 0;
   		padding: 12px;
   		color: #666;
   	}
   	.id-select-row select {
   		border: none;
	    border-bottom: 1px solid #aaa;
	    padding-top: 10px;
	    width: 90%;
	    padding-bottom: 5px;
   	}
   	.id-select-row input{
   		border: none;
   		border-bottom: 1px solid #aaa;
   		padding-top: 10px;
   		width: 90%;
   		padding-bottom: 5px;
   	}
   	.id-fa-globe::before{
   		content: "\f0ac";
	    bottom: 0;
	    color: #a100ff;
	    font-family: "Font Awesome 5 Pro";
	    font-weight: 300;
	    font-size: 18px;
	    left: 0;
	    line-height: 42px;
	    position: absolute;
	    text-align: center;
	    top: 0;
	    width: 40px;
   	}
   	.id-fa-globe::before {
		color: #a05f03;
	}
	.id-margin-top-5{
		margin-top: 5px;
	}
	.id-margin-top-12{
		margin-top: 12px;
	}

/* ----------------        start star rating code         ---------------- */
	.star-cb-group {
	  /* remove inline-block whitespace */
	  font-size: 0;
	  /* flip the order so we can use the + and ~ combinators */
	  unicode-bidi: bidi-override;
	  direction: rtl;
	  /* the hidden clearer */
	}
	.star-cb-group * {
	  font-size: 0.65rem;
	  margin-top: 5px;
	}
	.star-cb-group > input {
	  display: none;
	}
	.star-cb-group > input + label {
	  /* only enough room for the star */
	  display: inline-block;
	  overflow: hidden;
	  text-indent: 9999px;
	  width: 1em;
	  white-space: nowrap;
	  cursor: pointer;
	}
	.star-cb-group > input + label:before {
	  display: inline-block;
	  text-indent: -9999px;
	  content: "☆";
	  color: #888;
	}
	.star-cb-group > input:checked ~ label:before, .star-cb-group > input + label:hover ~ label:before, .star-cb-group > input + label:hover:before {
	  content: "★";
	  color: #a05f03;
	  text-shadow: 0 0 1px #333;
	}
	.star-cb-group > .star-cb-clear + label {
	  text-indent: -9999px;
	  width: .5em;
	  margin-left: -.5em;
	}
	.star-cb-group > .star-cb-clear + label:before {
	  width: .5em;
	}
	.star-cb-group:hover > input + label:before {
	  content: "☆";
	  color: #888;
	  text-shadow: none;
	}
	.star-cb-group:hover > input + label:hover ~ label:before, .star-cb-group:hover > input + label:hover:before {
	  content: "★";
	  color: #a05f03;
	  text-shadow: 0 0 1px #333;
	}

	:root {
	  font-size: 2em;
	  font-family: Helvetica, arial, sans-serif;
	}

/* ----------------        end star rating code         ---------------- */
   </style>
   