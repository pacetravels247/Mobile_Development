<?php
$hotel_datepicker = array(array('hotel_checkin', FUTURE_DATE), array('hotel_checkout', FUTURE_DATE));
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
	$room_adult_config = array(2);
}

if (isset($hotel_search_params['child_config']) == true) {
	$room_child_config = $hotel_search_params['child_config'];
} else {
	$room_child_config = array(0);
}

if (isset($hotel_search_params['child_age']) == true) {
	$room_child_age_config = $hotel_search_params['child_age'];
} else {
	$room_child_age_config = array(0);
}

?>
<form name="hotel_search" id="hotel_search" autocomplete="off" action="<?php echo base_url().'index.php/general/pre_hotel_search' ?>">
	<div class="row">
		<div class="col-md-6">
			<h2 class="h3 txtwrapRow">Book Domestic & International Hotels </h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<input type="text" id="hotel_destination_search_name" class="hotel_city form-control b-r-0" placeholder="Region, City, Area (Worldwide)" name="city" required value="<?php echo @$hotel_search_params['location']?>"/>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<input  type="text" class="form-control b-r-0" data-date="true" readonly name="hotel_checkin" id="hotel_checkin"  placeholder="Check-In" required value="<?php echo @$hotel_search_params['from_date']?>"/>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<input type="text" class="form-control b-r-0" data-date="true" readonly name="hotel_checkout" id="hotel_checkout" placeholder="Check-Out" required value="<?php echo @$hotel_search_params['to_date']?>"/>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 nopad nmbrspnr-outer">
		<div class="col-md-2 col-xs-8">
			<label>Room(s)</label>
			<div class="selectedwrapnum">
				<div class="persnm padult"></div>
				<div class="onlynumwrap">
					<div class="onlynum">
						<span class="btnminus meex cmnum HroomM">-</span>
						<div class="datemix meex"><?php echo $room_count_config?></div>
						<input class="Hroom" type="hidden" id="rooms" name="rooms" value="<?php echo $room_count_config; ?>" required>
						<span class="btnplus meex cmnum HroomP">+</span>
					</div>
				</div>
			</div>
		</div>
        <div class="clearfix visible-sm visible-xs"></div>
		<div class="col-md-7">


			<?php
			$loop_room_index = 1;
			for ($loop_room_index=1; $loop_room_index<=$room_count_config; $loop_room_index++) {
				$current_room_adult_count = $room_adult_config[$loop_room_index-1];
				$current_room_child_count = $room_child_config[$loop_room_index-1];
				?>
				<div class="addedRooms Aroom<?php echo $loop_room_index?>">
					<div class="repeatedroom">
						<div class="childgroup" data-roomid="<?php echo $loop_room_index?>">
							<div class="col-md-12 nopad">
								<div class="col-md-2 col-xs-12 mefullwdhtl nopad">
									<label class="invisible">Rooms</label>
									<div class="roomnumpn"><br><i class="fa fa-bed fa-fw"></i> <strong>(<?php echo $loop_room_index; ?>)</strong></div>
								</div>
								<div class="col-md-4 col-xs-6 mefullwdhtl fiveh noPL">
									<label>Adult</label>
									<div class="selectedwrapnum">
										<div class="persnm padult"></div>
										<div class="onlynumwrap">
											<div class="onlynum">
												<span class="btnminus meex cmnum aminus">-</span>
												<div class="datemix meex"><?php echo $current_room_adult_count;?></div>
												<input class="apax" type="hidden" name="adult[]" value="<?php echo $current_room_adult_count;?>" required>
												<span class="btnplus meex cmnum aplus">+</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4 col-xs-6 mefullwdhtl fiveh noPL noPR">
									<label>Children(2-12 yrs)</label>
									<div class="selectedwrapnum">
										<div class="persnm pachildrn"></div>
										<div class="onlynumwrap">
											<div class="onlynum">
												<span class="btnminus meex cmnum HCminus">-</span>
												<div class="datemix meex"><?php echo $current_room_child_count?></div>
												<input class="HCpax" type="hidden" name="child[]" value="<?php echo $current_room_child_count?>" required>
												<span class="btnplus meex cmnum HCplus">+</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php
					//Start child age looping
							if (intval($current_room_child_count) > 0) {
								$temp_index = 0;
								for($temp_index=0; $temp_index<$current_room_child_count; $temp_index++) {
									$temp_current_child_age = array_shift($room_child_age_config);
									?>
									<div class="col-md-12">
										<div class="col-md-2 col-xs-4 mefullwdhtl"></div>
										<div class="col-md-4 fiveh childAge cl_rt pull-right childAge<?php echo ($temp_index+1)?>">
											<span class="formlabel">Child - <?php echo ($temp_index+1); ?> age</span>
											<div class="selectedwrapnum">
												<div class="persnm pachildrn"></div>
												<div class="onlynumwrap">
													<div class="onlynum">
														<span class="btnminus meex cmnum cminus">-</span>
														<div class="datemix meex"><?php echo $temp_current_child_age?></div>
														<input type="hidden" required="" value="<?php echo $temp_current_child_age?>" name="childAge_<?php echo $loop_room_index;?>[]" class="cpax"><span class="btnplus meex cmnum cplus">+</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php
								}
							}
					//End child age looping
							?>
						</div>
					</div>
				</div>
				<?php
			}
			?>

		</div>
	</div>
	<div class="clearfix">
		<div class="pull-right srchBtnmob">
			<input type="submit" id="hotel-search-btn" class="htlSrchBtn btn btn-lg btn-i b-r-0" value="Search Hotels" />
		</div>
	</div>
</form>

<?=$this->template->isolated_view('share/js/hotel_suggest_js')?>