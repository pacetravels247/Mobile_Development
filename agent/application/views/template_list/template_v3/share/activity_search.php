<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/activity_suggest.js'), 'defer' => 'defer');


//Read cookie when user has not given any search
//debug($activity_search_params);exit;
if ((isset($activity_search_params) == false) || (isset($activity_search_params) == true && valid_array($activity_search_params) == false)) {
	$sparam = $GLOBALS['CI']->input->cookie('sparam', TRUE);

	$sparam = unserialize($sparam);

	$sid = intval(@$sparam[META_ACTIVITY_COURSE]);
	if ($sid > 0) {

		$GLOBALS['CI']->load->model('activity_model');
		$activity_search_params = $GLOBALS['CI']->activity_model->get_safe_search_data($sid, true);
		$activity_search_params = $activity_search_params['data'];

		if (valid_array($activity_search_params) == true) {
			if (strtotime(@$activity_search_params['from_date']) < time()) {
				$activity_search_params['from_date'] = date('d-m-Y', strtotime(add_days_to_date(3)));
				$activity_search_params['to_date'] = date('d-m-Y', strtotime(add_days_to_date(5)));
			}
		}
	}
}

$activity_datepicker = array(array('activity_from', FUTURE_DATE_DISABLED_MONTH), array('activity_to', FUTURE_DATE_DISABLED_MONTH));
$GLOBALS['CI']->current_page->set_datepicker($activity_datepicker);
$GLOBALS['CI']->current_page->auto_adjust_datepicker(array(array('activity_from', 'activity_to')));
if(!isset($activity_search_params['adult'])) {
	$activity_search_params['adult'] = 1;
}
if(!isset($activity_search_params['child'])) {
	$activity_search_params['child'] = 0;
}
//debug($activity_search_params);exit;
?>
<form name="activity_search" id="activity_search" autocomplete="on" action="<?php echo base_url().'index.php/general/pre_activity_search' ?>">
	<div class="intabs tabspl">
		<div class="outsideserach" style="float: left; width: 100%; margin-bottom: 15px;">
			<div class="col-lg-4 col-md-4 col-sm-4 fiveh padfive">
				<span class="formlabel">Going </span>
				<div class="relativemask plcemark"> <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
					<!--<input type="text" value="" name="city" id="city" class="ft hotelCityIp ui-autocomplete-input" placeholder="Region, City, Area (Worldwide)" autocomplete="off">
					--><input type="text" id="activity_destination_search_name" class="ft activity_city normalinput form-control b-r-0" placeholder="Region, City, Area (Worldwide)" name="city" required value="<?php echo @$activity_search_params['location']?>"/>
					<input class="hide loc_id_holder" id="activity_loc_id" name="activity_destination" type="hidden" value="<?=@$activity_search_params['activity_destination']?>" >
				</div>
			</div>
            <div class="col-xs-6 nopad aftremarg padfive">
			<div class="col-md-7 col-xs-12 nopad padfive">
				<div class="col-xs-6 fiveh padfive">
					<span class="formlabel">From</span>
					<div class="relativemask datemark"> 
						<input  type="text" class="normalinput" readonly name="activity_from" id="activity_from"  placeholder="From" required value="<?php echo @$activity_search_params['from_date']?>"/>
					</div>
				</div>
				<div class="col-xs-6 fiveh padfive">
					<span class="formlabel">To</span>
					<div class="relativemask datemark"> 
						<input type="text" class="normalinput" readonly name="activity_to" id="activity_to" placeholder="To" required value="<?php echo @$activity_search_params['to_date']?>"/>
					</div>
				</div>
			</div>	

			<div class="col-md-5 col-xs-12 nopad padfive">


			<div class="formlabel">&nbsp;</div>
                    <div class="totlall">
                        <span class="remngwd"><span class="total_pax_count"></span> <span id="travel_text">Traveller</span></span>
                        <div class="roomcount pax_count_div">
                            <div class="mobile_adult_icon">Travelers<i class="fa fa-male"></i></div>

                            <div class="inallsn">
                                <div class="oneroom fltravlr">
                                    <div class="lablform2">Travelers</div>
                                    <div class="clearfix"></div>
                                    <div class="roomrow">

                                        <div class="celroe col-xs-7"><i class="fal fa-male"></i> Adults
                                            <span class="agemns">(12+)</span>
                                        </div>


                                        <div class="celroe col-xs-5">
                                            <div class="selectedwrapnum">
                                                <div class="onlynumwrap wrap1 pax-count-wrapper">
															<div class="onlynum">
															<button data-field="adult" data-type="minus"  class="btn btn-default btn-number btnpot minusValueAct adult" type="button"> 
															<span class="fa fa-minus"></span> </button>
															<input type="text" id="OWT_adult" name="adult" required="" class="form-control input-number centertext valid_class pax_count_value" value="<?=(int)@$activity_search_params['adult']?>" min="1" max="10" readonly>
															<button data-field="adult" data-type="plus" class="btn btn-default btn-number btnpot btn_right plusValAct" type="button"> 
															<span class="fa fa-plus"></span> </button>
															</div>
                                                </div>
                                            </div>
                                        </div>
                         
                                    </div>




                                    <div class="roomrow">
                                        <div class="celroe col-xs-7"><i class="fal fa-child"></i> Children
                                            <span class="agemns">(2-11)</span>
                                        </div>

                                        <div class="celroe col-xs-5">
                                            <div class="selectedwrapnum">
                                                <div class="onlynumwrap wrap1">
													<div id="childs" class="onlynum pax-count-wrapper">
													<button type="button" class="btn btn-default btn-number btnpot minusValueAct child" data-type="minus" data-field="child"> <span class="fa fa-minus"></span> </button>
													<input type="text" id="OWT_child" name="child" class="form-control input-number centertext pax_count_value" value="<?=(int)@$activity_search_params['child']?>" min="0" max="10" readonly>
													<button type="button" class="btn btn-default btn-number btnpot btn_right plusValAct" data-field="child" data-type="plus"> <span class="fa fa-plus"></span> </button>
													</div>
                                                </div>
                                            </div> 
                                        </div>


                                           <div class="col-xs-6 nopad"><?php 
             $child_age_label_clss = '';
             if(isset($activity_search_params['child']) && $activity_search_params['child'] == 0) {
             	$child_age_label_clss = 'hide';
             }
             ?>
                <div class="formlabel padfive_child activity-child-age-label <?=$child_age_label_clss?>">Child age at time of travel</div>
				<?php 	
							for($c = 1;$c<=10;$c++) {
								if(isset($activity_search_params['child_ages'][$c-1])) {
									$classname='';
									$disable = '';
								}else {
									$disable = 'disabled=""';
									$classname = "hide";
								}
								?>
                <div class="col-xs-6 fiveh padfive child-ages">
                
						<div class="selectedwrap child_ageId<?=$c?> <?=$classname?>">
							<select class="flyinputsnor marginbotom10 selectpicker <?=$classname?>" <?=$disable?> id="child_ageId<?=$c?>" name="child_ages[]"><?php 
									for ($j=2; $j<=12; $j++) {
										if(isset($activity_search_params['child_ages'][$c-1]) && $activity_search_params['child_ages'][$c-1] == $j) {
											$selected = "selected";
										}else {
											$selected = '';
										}
										?><option <?=$selected?>><?=$j?></option><?php 
									}
								?></select></div>
				</div>
                
                <?php 
							}
						?>
               </div>         
               	

                                        </div> 

                                    </div>


                                    <a class="done1 comnbtn_room1"><span class="fa fa-check"></span> Done</a>
                                    <!-- Infant Error Message-->
                                </div>
                            </div>
                        </div>
                    </div>

				
				<div class="clearfix"></div> 
			<div class="minVal_error_child_adult col-md-6" style="color:red;font-size:14px;font-weight:bold;"></div>		
			</div>
			
			
				

			<div class="col-xs-2 fiveh padfive pull-right">
			<span class="formlabel">&nbsp;</strong></span>
				<div class="formsubmit">
					<button class="searchsbmt srchbutn comncolor">Search Activities<span class="srcharow"></span></button>
				</div>
			</div>	
		</div>
	</div>
</form>
