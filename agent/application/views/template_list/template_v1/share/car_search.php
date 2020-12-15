<?php
	//Read cookie when user has not given any search
	if ((isset($car_search_params) == false) || (isset($car_search_params) == true && valid_array($car_search_params) == false)) {
		$sparam = $this->input->cookie('sparam', TRUE);
		$sparam = unserialize($sparam);
		$sid = intval(@$sparam[META_CAR_COURSE]);
		// echo $sid;exit;
		if ($sid > 0) {
			$car_search_params = $this->car_model->get_safe_search_data($sid, true);
			$car_search_params = $car_search_params['data'];
			// debug($car_search_params);exit;
			if (strtotime(@$car_search_params['bus_date_1']) < time()) {
				$bus_search_params['bus_date_1'] = date('d-m-Y', strtotime(add_days_to_date(3)));
			}
		}
	}
	Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/car_suggest.js'), 'defer' => 'defer');
	
	$car_datepicker = array(array('car_datepicker1', FUTURE_DATE_DISABLED_MONTH), array('car_datepicker2', FUTURE_DATE_DISABLED_MONTH));
	$this->current_page->set_datepicker($car_datepicker);
	
?>

<style type="text/css">
	.car_form .padselct { padding: 0px; }
</style>


<!--  novalidate="novalidate" -->
<form id="trasfer" name="car" autocomplete="off" action="<?=base_url()?>index.php/general/pre_car_search">
	<div class="tabspl forbusonly car_form" style="padding: 15px 0px;">
		<div class="clearfix"></div>
		<div class="outsideserach custom_divclass">
			<div class="clearfix"></div>
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 nopad cr_plce">
			<div class="col-sm-6 col-xs-6 padfive">
				<div class="marginbotom10">
					<span class="formlabel">Pick-Up</span>
					<div class="relativemask plcemark"> 
                    <span class="maskimg hfrom"></span> 
                    <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
						<input type="text" value="<?php echo @$car_search_params['car_from'] ?>" placeholder="From, Airport, City" name="car_from" id="car_from" class="b-r-0 hotelin normalinput fromcar ui-autocomplete-input" required="" aria-required="true" autocomplete="off">
						<input class="hide loc_id_holder" id="car_from_loc_id" name="from_loc_id" type="hidden" value="<?=@@$car_search_params['from_loc_id']?>"  >
						<input class="hide loc_code_holder" id="car_from_loc_code" name="car_from_loc_code" type="hidden" value="<?=@@$car_search_params['car_from_loc_code']?>"  >
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xs-6 padfive">
				<div class="marginbotom10">
					<span class="formlabel">Drop-Off</span>
					<div class="relativemask plcemark">  <span class="maskimg hfrom"></span> <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
						<input type="text" value="<?php echo @$car_search_params['car_to'] ?>" placeholder="To, Airport, City" id="car_to" name="car_to" class="b-r-0 departcar hotelin normalinput ui-autocomplete-input" required="" aria-required="true" autocomplete="off">
						<input class="hide loc_id_holder" name="to_loc_id" id="car_to_loc_id" type="hidden" value="<?=@$car_search_params['to_loc_id']?>" >
						<input class="hide loc_code_holder" id="car_to_loc_code" name="car_to_loc_code" type="hidden" value="<?=@@$car_search_params['car_from_loc_code']?>"  >
					</div>
				</div>
			</div>
			</div>
			<?php
			if(@@$car_search_params['depature']!='')
			{
				$depature = date('d-m-Y', strtotime(@@$car_search_params['depature'])) ;
				$depature_time = date('H:i', strtotime(@@$car_search_params['depature_time'])) ;
			}else
			{
				$depature = '';
				$depature_time = '09:00';
			}

			if(@@$car_search_params['return']!='')
			{
				$return = date('d-m-Y', strtotime(@@$car_search_params['return'])) ;
				$return_time = date('H:i', strtotime(@@$car_search_params['return_time'])) ;
			}else
			{
				$return = '';
				$return_time = '09:00';
			}
			// $depature = $depature_time = $return = $return_time = '';
			?>			
			<div class="col-md-5 col-xs-12 nopad">
				<div class="col-xs-4 padfive">					
						<span class="formlabel">Pick-Up Date</span>
						<div class="relativemask datemark pkupdt"> <span class="maskimg caln"></span>
							<input type="text" readonly placeholder="Depature Date" value="<?php echo @$depature; ?>" class="b-r-0 forminput date_picker normalinput"  id="car_datepicker1" name="depature" required/>
						</div>
				</div>
				<div class="col-xs-2 padfive">					
						<span class="formlabel">&nbsp;</span>
						<div class="relativemask"> <span class="maskimg caln"></span>
							<select name="depature_time" id="depature_time" class="normalsel padselct dep_t arimo">
								<?php
								$start = "00:00";
								$end = "23:30";
								$tStart = strtotime($start);
								$tEnd = strtotime($end);
								$tNow = $tStart;
								while($tNow <= $tEnd){
									$time=date("H:i",$tNow);
									$selected = ($depature_time ==  $time) ? 'selected="selected"' : '' ;
									
									echo '<option value="'.$time.'" '.$selected.'>'.$time.'</option>';
									$tNow = strtotime('+30 minutes',$tNow);
								}
								?>
							</select>
						</div>
				</div>
				<div class="col-xs-4 padfive">
					
						<span class="formlabel">Return Date</span>
						<div class="relativemask datemark retdt"> <span class="maskimg caln"></span>
							<input type="text" readonly placeholder="Return Date" value="<?php echo @$return; ?>" class="b-r-0 forminput date_picker normalinput" id="car_datepicker2" name="return" required/>
						</div>
				</div>
				<div class="col-xs-2 padfive">					
						<span class="formlabel">&nbsp;</span>
						<div class="relativemask"> <span class="maskimg caln"></span>
							<select name="return_time" id="return_time" class="normalsel b-r-0 padselct dep_t arimo">
								<?php
								$start = "00:00";
								$end = "23:59";
								$tStart = strtotime($start);
								$tEnd = strtotime($end);
								$tNow = $tStart;
								while($tNow <= $tEnd){
									$time=date("H:i",$tNow);
									$selected = ($return_time ==  $time) ? 'selected="selected"' : '' ;

									echo '<option value="'.$time.'" '.$selected.'>'.$time.'</option>';
									$tNow = strtotime('+30 minutes',$tNow);
								}
								?>
							</select>
						</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-xs-12 nopad marginbotom10 aftremarg">
					<div class="pull-right hide remove_age" id="remove_age">
					 <div class="squaredThree2">
						<input type="checkbox" name="driver_age" id='driver_age' value="30" checked="checked">
						<label for="driver_age"></label>
					 </div>
					 <label for="driver_age" class="lbllbl">Driver's age between 30 - 65</label>
					</div>
					<!-- <div id='add_age' class="hide pull-right">
					<div class=" padfive" style="width: 237px;">
					<span class="formlabel">Driver's Age</span>
						<input type="text" id="cus_numeric" placeholder=" " value="<?php echo @$car_search_params['driver_age']; ?>" class="input_form numeric"  maxlength="2" minlength="2"  name="age_1" />
					</div>
					</div> -->
				</div>
			</div>

			
            <div class="col-md-2 col-xs-12 padfive">
            <div class="formlabel">&nbsp;</div>
				<div class="searchsbmtfot">
					<button class="searchsbmt comncolor">Search<span class="srcharow"></span></button>
				</div>
			</div>

			
            
		</div>
	</div>
</form>

<script>

	var age_type = "<?php echo @$car_search_params['age_type']; ?>"
	
	if(age_type == true){
		$('#add_age').removeClass("hide");
	}else{
		$('#remove_age').removeClass("hide");
	}

	$('#cus_numeric').bind("keyup blur change focus", function() {
		if (this.value != '' || this.value != null) {
			$(this).val($(this).val().replace(/[^-?][^0-9.]/, ''));
		}
	});
	
	$("input[type=checkbox]").change(function(){

		$("#cus_numeric").show();
		if ($(this).is(':not(:checked)')){
			$( "#remove_age" ).remove();
			$("#add_age").removeClass("hide");
			$("#age_type").val(1)
		}
	});


	$( "#trasfer" ).submit(function( event ) {

		var age = $('#age_type').val();
		var age_val = $('#cus_numeric').val()

		if((age == 1) && (isNaN(age_val) == false) && (age_val >= 16)){

		  }else{

		  	if(age == 1){
		  		alert("Enter age (Minimum driver's age is 16) ");
		  		event.preventDefault();
		  	}
		}	
	});

</script>
