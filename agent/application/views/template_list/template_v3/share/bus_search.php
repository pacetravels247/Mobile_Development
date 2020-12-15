<?php
	//Read cookie when user has not given any search
	if ((isset($bus_search_params) == false) || (isset($bus_search_params) == true && valid_array($bus_search_params) == false)) {
		$sparam = $this->input->cookie('sparam', TRUE);
		$sparam = unserialize($sparam);
		$sid = intval(@$sparam[META_BUS_COURSE]);
		if ($sid > 0) {
			$bus_search_params = $this->bus_model->get_safe_search_data($sid, true);
			$bus_search_params = $bus_search_params['data'];
			if (strtotime(@$bus_search_params['bus_date_1']) < time()) {
				$bus_search_params['bus_date_1'] = date('d-m-Y', strtotime(add_days_to_date(3)));
			}
		}
	}
	$bus_datepicker = array(array('bus-date-1', FUTURE_DATE_DISABLED_MONTH), array('bus-date-2', FUTURE_DATE_DISABLED_MONTH));
	$GLOBALS['CI']->current_page->set_datepicker($bus_datepicker);
	$GLOBALS['CI']->current_page->auto_adjust_datepicker(array(array('bus-date-1', 'bus-date-2')));
	?>
<form autocomplete="on" name="bus" id="bus_form" action="<?php echo base_url();?>index.php/general/pre_bus_search" method="get" class="activeForm oneway_frm" style="">
	<div class="tabspl forbusonly">
		<div class="tabrow bus_search">
			<div class="col-md-8 col-sm-6 col-xs-12 nopad">
				<div class="col-xs-6 padfive full_smal_tab">
					<div class="lablform">From</div>
					<div class="plcetogo locatiomarker sidebord">
						<input type="text" required=""  value="<?php echo @$bus_search_params['bus_station_from'] ?>" placeholder="Type Departure City" id="bus-station-from" class="normalinput bus-station auto-focus form-control b-r-0 valid_class bus-station-from" name="bus_station_from" autocomplete="off">
						<input class="hide loc_id_holder" name="from_station_id" type="hidden" value="<?=@$bus_search_params['from_station_id']?>" >
						<div class="flight_chnge"><i class="far fa-exchange rot_arrow"></i></div>
					</div>
				</div>
				<div class="col-xs-6 padfive full_smal_tab">
					<div class="lablform">To</div>
					<div class="plcetogo locatiomarker sidebord">
						<input type="text" required="" value="<?php echo @$bus_search_params['bus_station_to'] ?>" placeholder="Type Destination City" id="bus-station-to" class="normalinput bus-station bus-station auto-focus form-control b-r-0 valid_class bus-station-to ui-autocomplete-input" name="bus_station_to" autocomplete="off">
						<input class="hide loc_id_holder" name="to_station_id" type="hidden" value="<?=@$bus_search_params['to_station_id']?>" >
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="alert-box" id="bus-alert-box">
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 nopad">
				<div class="col-xs-6 padfive mobile_width">
					<div class="lablform">Date of Journey</div>
					<div class="plcetogo datemark sidebord">
						<input type="text" readonly class="normalinput auto-focus hand-cursor form-control b-r-0" id="bus-date-1" placeholder="dd-mm-yyyy" value="<?php echo @$bus_search_params['bus_date_1'] ?>" name="bus_date_1" required>
					</div>
				</div>
				<div class="col-xs-6 padfive mobile_width">
					<div class="lablform">&nbsp;</div>
					<div class="searchsbmtfot">
						<input type="submit" id="bus-form-submit" class="searchsbmt" value="search" />
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/bus_search.js'), 'defer' => 'defer');
?>
<script>
$(".flight_chnge").click(function(){
   var from = $("input[name='bus_station_from']").val();
   var from_loc_id = $("input[name='from_station_id']").val();

   var to = $("input[name='bus_station_to']").val();
   var to_loc_id = $("input[name='to_station_id']").val();


   $("input[name='bus_station_from']").val(to);
   $("input[name='bus_station_to']").val(from);

   $("input[name='from_station_id']").val(to_loc_id);
   $("input[name='to_station_id']").val(from_loc_id);

   $(".flight_chnge .fa-exchange").toggleClass('rot_arrow');
  });
</script>

<style type="text/css">
	.secndblak {
	    padding: 5px 0;
	    min-height: 120px;
	    position: relative;
	    border-bottom: none;
	    border-radius: 0px;
	    background: linear-gradient(96deg,#002042,#0a8ec1);

	}
</style>