<?php
// debug($bus_search_params);exit;
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
$data['result'] = $bus_search_params;
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v1.gif" alt="Loading........"/></div>';
//$loading_image = '';
$flight_o_direction_icon = '<img src="'.$template_images.'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
echo $GLOBALS['CI']->template->isolated_view('bus/search_panel_summary');
?>
<script>
var num_of_act_bs = "<?php echo count($active_booking_source); ?>";
var load_busses = function(booking_source_k, api_count){
	$.ajax({
		type: 'GET',
		url: app_base_url+'index.php/ajax/bus_list?booking_source='+booking_source_k+'&search_id=<?=$bus_search_params['search_id']?>&api_count='+api_count+'&op=load',
		async: false,
		cache: true,
		dataType: 'json',
		success: function(res) {
			var dui;
			var r = res;
			dui = setInterval(function(){
			if (typeof(process_result_update) != "undefined" && $.isFunction(process_result_update) == true) {
				clearInterval(dui);
				process_result_update(r);
			}
			}, 1);
		}
	});
}
//Load buss from active source
//load_busses();

/////
<?php
		foreach ($active_booking_source as $t_k => $t_v ) {
			$api_count = $t_k+1;
			//$active_source [] = $t_v ['source_id'];
?>         
			//api_request_cnt++;
			load_busses('<?=$t_v['source_id']?>','<?=$api_count?>');
<?php
		}
		//$active_source = json_encode ( $active_source );
		//$mini_loading_image = '<div class="text-center loader-image"><img src="' . $GLOBALS ['CI']->template->template_images ( 'loader_v3.gif' ) . '" alt="Loading........"/></div>';
?>
/////
</script>
<span class="hide">
	<input type="hidden" id="pri_active_source" value='<?=($active_source)?>' >
	<input type="hidden" id="pri_search_id" value="<?=$bus_search_params['search_id']?>" >
	<input type="hidden" id="pri_slider_currency" value="<?=$this->currency->get_currency_symbol(get_application_currency_preference())?>" >
	<input type="hidden" value="<?php echo count($active_booking_source); ?>" id="total_api_result_count">
</span>
<section class="search-result">
	<div class="container-fluid" id="page-parent">
	<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/bus_result_pre_loader',$data);?>
		<div class="resultalls open">
			<div class="coleft">
			<div class="flteboxwrp">
				<div class="filtersho">
					<div class="avlhtls">
						<strong id="total_records"></strong> Buses Found
					</div>
				</div>
				<div class="fltrboxin">
					<div class="celsrch">
						<div class="row_busses row_top_fltr">
								<a class="snf_btn pull-left active" title="Show Net Fare">
								<span class="fas fa-tag"></span>
								<span class="tag_snf">SNF</span></a>
								<a class="pull-right reset_filter" id="reset_filters">RESET ALL</a>
						</div>
						<div class="row">
							<div class="rangebox">
								<button class="collapsebtn" type="button" data-toggle="collapse" data-target="#price-refine">Price</button>
								<div class="collapse in price-refine">
									<div class="price_slider1">
										<div id="core_min_max_slider_values" class="hide">
											<input type="hiden" id="core_minimum_range_value" value="">
											<input type="hiden" id="core_maximum_range_value" value="">
										</div>
										<p id="amount" class="level"></p>
										<div id="slider-range-1" class="" aria-disabled="false"></div>
										<?=$mini_loading_image;?>
										</div>
										</div>
							</div>
							<div class="clearfix"></div>
							<div class="septor"></div>
							<div class="rangebox">
								<button class="collapsebtn" type="button" data-toggle="collapse" data-target="#bustype-refine">Bus Type</button>
								<div class="collapse in bustype-refine">
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="septor"></div>
							<div class="rangebox">
								<button class="collapsebtn" type="button" data-toggle="collapse" data-target="#travel-refine">Operators</button>
								<div class="collapse in travel-refine">
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="septor"></div>
								<div class="rangebox">
									<button type="button" class="collapsebtn" data-toggle="collapse"
										data-target="#collapse503">Departure Time</button>
									<div class="collapse in" id="collapse503">
										<div id="departureTimeWrapper" class="boxins marret">
											<a
												class="timone toglefil time-wrapper">
												<input type="checkbox"
													value="1" class="time-category hidecheck">
												<div class="starin">
												 <div class="tmxdv">
                                                        <input type="checkbox" value="1" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Early Morning</label>
                                                    </div>
													<div class="flitsprt mng1"></div>
													<span class="htlcount">5-9AM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input
													type="checkbox" value="2" class="time-category hidecheck">
												<div class="starin">
												<div class="tmxdv">
                                                        <input type="checkbox" value="2" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Mid-Day</label>
                                                    </div>
													<div class="flitsprt mng2"></div>
													<span class="htlcount">9-5PM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input
													type="checkbox" value="3" class="time-category hidecheck">
												<div class="starin">
												  <div class="tmxdv">
                                                        <input type="checkbox" value="3" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Evening</label>
                                                    </div>
													<div class="flitsprt mng3"></div>
													<span class="htlcount">5-9PM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input
													type="checkbox" value="4" class="hidecheck time-category">
												<div class="starin">
												 <div class="tmxdv">
                                                        <input type="checkbox" value="4" class="hidecheck time-category">
                                                        <label for="check1" class="ckboxdv">Night</label>
                                                    </div>
													<div class="flitsprt mng4"></div>
													<span class="htlcount">9PM-5AM</span>
												</div>
											</a>
										</div>
									</div>
								</div>
								<div class="rangebox">
									<button type="button" class="collapsebtn" data-toggle="collapse" data-target="#collapse504">Arrival Time</button>
									<div class="collapse in" id="collapse504">
										<div id="arrivalTimeWrapper" class="boxins marret">
											<a class="timone toglefil time-wrapper">
												<input type="checkbox" value="1" class="time-category hidecheck">
												<div class="starin">
												  <div class="tmxdv">
                                                        <input type="checkbox" value="1" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Early Morning</label>
                                                    </div>
													<div class="flitsprt mng1"></div>
													<span class="htlcount">5-9AM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input type="checkbox" value="2" class="time-category hidecheck">
												<div class="starin">
												<div class="tmxdv">
                                                        <input type="checkbox" value="2" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Mid-Day</label>
                                                    </div>
													<div class="flitsprt mng2"></div>
													<span class="htlcount">9-5PM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input type="checkbox" value="3" class="time-category hidecheck">
												<div class="starin">
												 <div class="tmxdv">
                                                        <input type="checkbox" value="3" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Evening</label>
                                                    </div>
													<div class="flitsprt mng3"></div>
													<span class="htlcount">5-9PM</span>
												</div>
											</a>
											<a class="timone toglefil time-wrapper">
												<input type="checkbox" value="4" class="time-category hidecheck">
												<div class="starin">
												 <div class="tmxdv">
                                                        <input type="checkbox" value="4" class="time-category hidecheck">
                                                        <label for="check1" class="ckboxdv">Night</label>
                                                    </div>
													<div class="flitsprt mng4"></div>
													<span class="htlcount">9PM-5AM</span>
												</div>
											</a>
										</div>
									</div>
								</div>
							<div class="clearfix"></div>
							<div class="col-lg-12" style="display:none">
								<button type="button" class="btn btn-t btn-block b-r-0 reset-page-loader">Reset Filters</button>
							</div>
						</div>
					</div>
				</div>
						 </div>
			 </div>
			<div class="colrit">
				<!-- Prev|Next Searcrh Button Starts -->
				<?php
				$core_travel_date = $bus_search_params['bus_date_1'];
				$current_date = date('Y-m-d');
				if(strtotime($core_travel_date) > strtotime($current_date)) {
					$prev_day_search_disabled = '';
					$prev_day_search_date = date('d-m-Y', strtotime('-1 day', strtotime($core_travel_date)));
					$prev_day_search_url = base_url().'index.php/bus/prev_next_day_search/'.$bus_search_params['search_id'].'/'.$prev_day_search_date;
				} else {
					$prev_day_search_url = '';
					$prev_day_search_disabled = ' disabled="disabled" ';
				}
				$next_day_search_date = date('d-m-Y', strtotime('+1 day', strtotime($core_travel_date)));
				$next_day_search_url = base_url().'index.php/bus/prev_next_day_search/'.$bus_search_params['search_id'].'/'.$next_day_search_date;
				?>
				<div class="prev_next_date">
					<a <?=$prev_day_search_disabled?> href="<?=$prev_day_search_url?>" title="<?=(empty($prev_day_search_date) == false ? app_friendly_absolute_date($prev_day_search_date): 'depart is less thant current date')?>" class="btn_dates">
					<i class="fal fa-angle-left"></i> Prev Day</a>
					<span class="datevery">
					<?=app_friendly_absolute_date($core_travel_date)?>
					</span>
					<a href="<?=$next_day_search_url?>" title="<?=(empty($next_day_search_date) == false ? app_friendly_absolute_date($next_day_search_date): 'depart is less than current date')?>" class="btn_dates">Next Day <i class="fal fa-angle-right"></i></a>
					</div>
				<!-- Prev|Next Searcrh Button Ends -->
				<div class="insidebosc">
				<div class="filter_tab fas fa-filter"></div>
				<div class="filterforall" id="top-sort-list-wrapper">
					<div class="topmisty bus_filter" id="top-sort-list-1">
						<div class="col-xs-12 nopad divinsidefltr">
							<div class="insidemyt">
									<ul class="sortul bus_sorting">
										<li class="sortli oprtrli"><a class="sorta travel-l-2-h loader asc"><i class="fas fa-bus"></i> <strong>Operators</strong></a><a
											class="sorta travel-h-2-l hide loader des"><i class="fas fa-bus"></i> <strong>Operators</strong></a></li>
										<li class="sortli deprtli"><a class="sorta departure-l-2-h loader asc"><i class="far fa-calendar-alt"></i> <strong>Depart</strong></a><a
											class="sorta departure-h-2-l hide loader des"><i class="far fa-calendar-alt"></i> <strong>Depart</strong></a></li>
										<li class="sortli durli"><a class="sorta seat-l-2-h loader asc"><i class="far fa-clock"></i> <strong>Duration</strong></a><a
											class="sorta seat-h-2-l hide loader des"><i class="far fa-clock"></i> <strong>Duration</strong></a></li>
										<li class="sortli arrivli"><a class="sorta arrival-l-2-h loader asc"><i class="far fa-calendar-alt"></i> <strong>Arrive</strong></a><a
											class="sorta arrival-h-2-l hide loader des"><i class="far fa-calendar-alt"></i><strong>Arrive</strong></a></li>
											<!--available seats sort-->	
										<li class="sortli seatli"><a class="sorta seatli-l-2-h loader asc"><i class="far fa-calendar-alt"></i> <strong>Seats</strong></a><a
											class="sorta seatli-h-2-l hide loader des"><i class="far fa-calendar-alt"></i><strong>Seats</strong></a></li>
											
										<li class="sortli priceli"><a class="sorta price-l-2-h loader asc"><i class="fas fa-tag"></i> <strong>Price</strong></a><a
											class="sorta price-h-2-l hide loader des"><i class="fas fa-tag"></i> <strong>Price</strong></a></li>
									</ul>
								</div>
						</div>
						</div>
				</div>
					<div class="clearfix"></div>
						<?php echo $loading_image;?>
				<div id="bus_search_result" class="">
					<div class="r-w-g allbusresult">
					</div>
				</div>
					<div  class="" id="empty_bus_search_result" style="display:none">
					<div class="noresultfnd">
						<div class="imagenofnd"><img src="<?=$template_images?>empty.jpg" alt="Empty" /></div>
							<div class="lablfnd">No Result Found!!!</div>
					</div>
					</div>
				<hr class="hr-10">
				<div class="clearfix">
					<div class="pull-right">
						<span class="h5">Showing: <?php echo $mini_loading_image?><span class="visible-row-record-count">...</span> of <span class="total-row-record-count">...</span> Buses</span>
					</div>
				</div>
				<hr class="hr-10 invisible">
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="bus-info-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Bus Details</h4>
			</div>
			<div class="modal-body">
			<?=$mini_loading_image?>
			<div id="bus-info-modal-content">
			</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			</div>
	  </div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="bus-boarding-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg">
	  <div class="modal-content">
		  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Boarding Point Details</h4>
			</div>
			<div class="modal-body">
			<?=$mini_loading_image?>
				<div id="bus-boarding-modal-content"></div>
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div> -->
			</div>
	</div>
	</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fas fa-bus"></i> Oops!</h1>
		<p>No tickets were found for this route today.</p>
		<p>
		Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
</section>
<script>
$(document).ready(function() {
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
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/bus_search_result.js'), 'defer' => 'defer');
echo $this->template->isolated_view('share/media/bus_search');
?>


<style type="text/css">
	.coleft {
		width: 20%;
	}
	.resultalls .colrit {
	    width: 80%;
	}
	/*div.content-wrapper {
	    margin-top: 40px !important;
	}*/
	.snf_hnf {
		font-size: 12px;
	}
	.busrows {
	    box-shadow: 0 8px 5px 0 #ccc;
	}
	.busername {
		font-weight: bold;
	}
	.bustype {
		color: #aaa!important;
	}
	.arowwspr .fa {
		color: #aaa;
	}
	.avlhtls strong {
	    color: #fc901b;
	    font-size: 15px;
	}
	.timelabelf {
	    color: #555;
	    font-weight: 600;
	}
	.modinew {
	    background: #002042 none repeat scroll 0 0;
	}
	.placenameflt {
    	color: #fff;
    }
    .contryname {
    	color: #8ba4ab;
    }
    .boxlabl {
    	color: #8ba4ab;
    }
    .datein {
    	color: #fff;
    }
    .splmodify {
    	background: linear-gradient(96deg,#002042,#0a8ec1);
    	/*padding-bottom: 10px;*/
    }
    .inpadbus{
    	float: none;
    }
    #reset_filters {
	    padding: 8px;
	    font-size: 10px;
	}
	.tag_snf{
		font-size: 10px;
	}
	.btn_dates {
		font-size: 10px;
	}
	.sortli.oprtrli{
		width: 32%;
	}
	.sortli.deprtli{
		width: 15%
	}
	.sortli.durli{
		width: 14%;
	}
	.sortli.arrivli{
		width: 15%;
	}
	.sortli.seatli{
		width: 8%;
	}
	.sortli.priceli{
		width: 16%;
	}
	#bus_search_result{
		overflow-y: scroll;
		height: 1550px;
	}
	.modal-header{
		background: linear-gradient(96deg,#002042,#0a8ec1);
		color: #fff;
	}
	.modal-dialog{
		margin-top: 5%;
	}

</style>