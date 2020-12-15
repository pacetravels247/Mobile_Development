<?php
$data['result'] = $hotel_search_params;
$mini_loading_image = '<div class="text-center loader-image hide"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image hide"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
$template_images = $GLOBALS['CI']->template->template_images();
function get_sorter_set()
{
	return '<div class="filterforallnty" id="top-sort-list-wrapper">
                        <div class="topmistyhtl" id="top-sort-list-1">
                            <div class="col-xs-12 nopad">
                                <div class="insidemyt">
									<ul class="sortul">
										
										<li class="sortli threonly"><a class="sorta name-l-2-h loader asc"><i class="fa fa-sort-alpha-asc"></i> <strong>Name</strong></a><a
											class="sorta name-h-2-l hide loader des"><i class="fa fa-sort-alpha-desc"></i> <strong>Name</strong></a></li>
										
										<li class="sortli threonly"><a class="sorta star-l-2-h loader asc"><i class="fa fa-star"></i> <strong>Star</strong></a><a
											class="sorta star-h-2-l hide loader des"><i class="fa fa-star"></i> <strong>Star</strong></a></li>
																				
										<li class="sortli threonly"><a class="sorta price-l-2-h loader asc"><i class="fa fa-tag"></i> <strong>Price</strong></a><a
											class="sorta price-h-2-l hide loader des"><i class="fa fa-tag"></i> <strong>Price</strong></a></li>
									</ul>
								</div>
                            </div>
                        </div>
                    </div>';
}
echo $GLOBALS['CI']->template->isolated_view('tours/search_panel_summary');
?>
<section class="search-result hotel_search_results">
	<div class="container"  id="page-parent">
    
	<?php // echo $GLOBALS['CI']->template->isolated_view('share/loader/tours_result_pre_loader',$data);?>
    
		<div class="resultalls open">
        
			<div class="coleft">
                <div class="flteboxwrp">
                    
                    <div class="filtersho">
                    	<div class="avlhtls"><strong id="total_records"><?php echo $mini_loading_image?></strong> Holidays found
                        </div>
                    </div>
                    
                    <div class="fltrboxin">
                    <form autocomplete="off">
                        
                        <div class="celsrch refine">
		 						<div class="row">
									<a class="pull-right" id="reset_filters">RESET ALL</a>
								</div>
                                <div class="rangebox">
                                    <button data-target="#price-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
								Price
								</button>
                                <?php echo $mini_loading_image?>
                                    
                                    <div id="price-refine" class="in">
                                        <div class="price_slider1">
	                                        <div id="core_min_max_slider_values" class="hide">
		                                    <input type="hiden" id="core_minimum_range_value" value="">
		                                    <input type="hiden" id="core_maximum_range_value" value="">
	                                    	</div>
                                            <p id="hotel-price" class="level"></p>
                                            <div id="price-range" class="" aria-disabled="false"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="septor"></div>
                                
                                <div class="rangebox">
                                  <button data-target="#collapse2" data-toggle="collapse" class="collapsebtn" type="button">
                                      Rating
                                  </button>
                                  <div id="collapse2" class="in">
                                      <div class="boxins marret" id="starCountWrapper">
                                        <a class="starone toglefil star-wrapper">
                                        	<input class="hidecheck star-filter" type="checkbox" value="1">
                                            <div class="starin">
                                                1 <span class="starfa fa fa-star"></span>
                                                <span class="htlcount">-</span>
                                            </div>
                                        </a>
                                        <a class="starone toglefil star-wrapper">
                                        	<input class="hidecheck star-filter" type="checkbox" value="2">
                                            <div class="starin">
                                                2 <span class="starfa fa fa-star"></span>
                                                <span class="htlcount">-</span>
                                            </div>
                                        </a>
                                        <a class="starone toglefil star-wrapper">
                                        	<input class="hidecheck star-filter" type="checkbox" value="3">
                                            <div class="starin">
                                                3 <span class="starfa fa fa-star"></span>
                                                <span class="htlcount">-</span>
                                            </div>
                                        </a>
                                        <a class="starone toglefil star-wrapper">
                                        	<input class="hidecheck star-filter" type="checkbox" value="4">
                                            <div class="starin">
                                                4 <span class="starfa fa fa-star"></span>
                                                <span class="htlcount">-</span>
                                            </div>
                                        </a>
                                        <a class="starone toglefil star-wrapper">
                                        	<input class="hidecheck star-filter" type="checkbox" value="5">
                                            <div class="starin">
                                                5 <span class="starfa fa fa-star"></span>
                                                <span class="htlcount">-</span>
                                            </div>
                                        </a>
                                      </div>
                                  </div>
                                </div>
                                
                                
                                <div class="septor"></div>
                               
                                <div class="rangebox">
                                  <button data-target="#hotelsearch-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
                                      Hotel Name
                                  </button>
                                  <div id="hotelsearch-refine" class="in">
                                      <div class="boxins">
                                        <div class="relinput">
                                            <input type="text" class="srchhtl" placeholder="Hotel name" id="hotel-name" />
                                            <input type="submit" class="srchsmall" id="hotel-name-search-btn" value="" />
                                        </div>
                                      </div>
                                  </div>
                                </div>
                                
                                <div class="septor"></div>
                                <div class="rangebox">
                                  <button data-target="#collapse2" data-toggle="collapse" class="collapsebtn" type="button">
                                      Hotel Location
                                  </button>
                                  <div id="collapse2" class="in">
                                      <div class="boxins">
                                        <ul class="locationul" id="hotel-location-wrapper">
                                        </ul>
                                      </div>
                                  </div>
                                </div>
                        </div>
                        </form>
                    </div>
                    
                </div>
            </div>
            
			<div class="colrit">
			<div class="insidebosc">
            <div class="resultall">

				<div class="filter_tab fa fa-filter"></div>
                <div class="vluendsort">
				

                <div class="col-xs-7 nopad">
                    <?php echo get_sorter_set();?>
                </div>
                </div>
                
                
                <div class="tour_display">
                	<div class="row_tours">
                    	<div class="inside_tour">
                        	<div class="col-xs-4 nopad listimage">
                                <div class="imagehtldis_holi">
                                <img src="http://images.cdnpath.com/imageresource.aspx?img=hpRBSdtPJNrQuwRo5I/exPbdtjJGoRI2R1Qo2/mGLJkOdZUWmSzi2FM2dyoe4hWYaJIqtmpn4OIz4UgJWzUObw==" class="hotel-image" alt="Hotel Image">
                                </div>
                            </div>
                            
                            <div class="col-xs-8 nopad listfull">
                                <div class="sidenamedesc">
                                    <div class="col-xs-8 nopad">
                                        <div class="innd_holi">
                                        
                                        <div class="shtlnamehotl"><span class="h-name">The Londonears Hostel</span></div>
                                        <div class="hoteloctnf">
                                        	<span class="fa fa-suitcase"></span>
                                            <span class="hotel-location">Holiday Package</span>
                                        </div>
                                        
                                        <div class="providings">
                                        	<div class="providelist">
                                            	<div class="fa fa-plane comn_fclities"></div>
                                                <span class="prvide_name">Flight</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-bed comn_fclities"></div>
                                                <span class="prvide_name">Hotel</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-car comn_fclities"></div>
                                                <span class="prvide_name">Transfer</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-cutlery comn_fclities"></div>
                                                <span class="prvide_name">Meals</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-binoculars comn_fclities"></div>
                                                <span class="prvide_name">Sightseeing</span>
                                            </div>
                                        </div>
                                        
                                        <div class="adreshotle hotel-address">
                                            1-BARKSTON GARDENS EARLS COURT London LONDON SW5 0EU United Kingdom, , United Kingdom, </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-xs-4 nopad">
                                        <div class="price_holi_section">
                                        
                                        	<div class="daynight">
                                            	<span class="night_area spandays">4 Nights</span>
                                                <span class="day_area spandays">5 Days</span>
                                            </div>
                                        
                                            <div class="priceflights_holi">
                                                <div class="prcstrtingt">starting @ </div>
                                                <strong> ₹ </strong>
                                                <span class="hotel-price">1259</span>
                                            </div>
                                            
                                            <div class="rate_card">
                                            	<img src="<?php echo $GLOBALS['CI']->template->template_images('user-rating-4.png'); ?>" alt="" />
                                            </div>

                                            <button class="bookallbtn holybtn" type="submit"> View Details</button>
                                            
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row_tours">
                    	<div class="inside_tour">
                        	<div class="col-xs-4 nopad listimage">
                                <div class="imagehtldis_holi">
                                <img src="http://images.cdnpath.com/imageresource.aspx?img=hpRBSdtPJNrQuwRo5I/exPbdtjJGoRI2R1Qo2/mGLJkOdZUWmSzi2FM2dyoe4hWYaJIqtmpn4OIz4UgJWzUObw==" class="hotel-image" alt="Hotel Image">
                                </div>
                            </div>
                            
                            <div class="col-xs-8 nopad listfull">
                                <div class="sidenamedesc">
                                    <div class="col-xs-8 nopad">
                                        <div class="innd_holi">
                                        
                                        <div class="shtlnamehotl"><span class="h-name">The Londonears Hostel</span></div>
                                        <div class="hoteloctnf">
                                        	<span class="fa fa-suitcase"></span>
                                            <span class="hotel-location">Holiday Package</span>
                                        </div>
                                        
                                        <div class="providings">
                                        	<div class="providelist">
                                            	<div class="fa fa-plane comn_fclities"></div>
                                                <span class="prvide_name">Flight</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-bed comn_fclities"></div>
                                                <span class="prvide_name">Hotel</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-car comn_fclities"></div>
                                                <span class="prvide_name">Transfer</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-cutlery comn_fclities"></div>
                                                <span class="prvide_name">Meals</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-binoculars comn_fclities"></div>
                                                <span class="prvide_name">Sightseeing</span>
                                            </div>
                                        </div>
                                        
                                        <div class="adreshotle hotel-address">
                                            1-BARKSTON GARDENS EARLS COURT London LONDON SW5 0EU United Kingdom, , United Kingdom, </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-xs-4 nopad">
                                        <div class="price_holi_section">
                                        
                                        	<div class="daynight">
                                            	<span class="night_area spandays">4 Nights</span>
                                                <span class="day_area spandays">5 Days</span>
                                            </div>
                                        
                                            <div class="priceflights_holi">
                                                <div class="prcstrtingt">starting @ </div>
                                                <strong> ₹ </strong>
                                                <span class="hotel-price">1259</span>
                                            </div>
                                            
                                            <div class="rate_card">
                                            	<img src="<?php echo $GLOBALS['CI']->template->template_images('user-rating-4.png'); ?>" alt="" />
                                            </div>

                                            <button class="bookallbtn holybtn" type="submit"> View Details</button>
                                            
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row_tours">
                    	<div class="inside_tour">
                        	<div class="col-xs-4 nopad listimage">
                                <div class="imagehtldis_holi">
                                <img src="http://images.cdnpath.com/imageresource.aspx?img=hpRBSdtPJNrQuwRo5I/exPbdtjJGoRI2R1Qo2/mGLJkOdZUWmSzi2FM2dyoe4hWYaJIqtmpn4OIz4UgJWzUObw==" class="hotel-image" alt="Hotel Image">
                                </div>
                            </div>
                            
                            <div class="col-xs-8 nopad listfull">
                                <div class="sidenamedesc">
                                    <div class="col-xs-8 nopad">
                                        <div class="innd_holi">
                                        
                                        <div class="shtlnamehotl"><span class="h-name">The Londonears Hostel</span></div>
                                        <div class="hoteloctnf">
                                        	<span class="fa fa-suitcase"></span>
                                            <span class="hotel-location">Holiday Package</span>
                                        </div>
                                        
                                        <div class="providings">
                                        	<div class="providelist">
                                            	<div class="fa fa-plane comn_fclities"></div>
                                                <span class="prvide_name">Flight</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-bed comn_fclities"></div>
                                                <span class="prvide_name">Hotel</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-car comn_fclities"></div>
                                                <span class="prvide_name">Transfer</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-cutlery comn_fclities"></div>
                                                <span class="prvide_name">Meals</span>
                                            </div>
                                            <div class="providelist">
                                            	<div class="fa fa-binoculars comn_fclities"></div>
                                                <span class="prvide_name">Sightseeing</span>
                                            </div>
                                        </div>
                                        
                                        <div class="adreshotle hotel-address">
                                            1-BARKSTON GARDENS EARLS COURT London LONDON SW5 0EU United Kingdom, , United Kingdom, </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-xs-4 nopad">
                                        <div class="price_holi_section">
                                        
                                        	<div class="daynight">
                                            	<span class="night_area spandays">4 Nights</span>
                                                <span class="day_area spandays">5 Days</span>
                                            </div>
                                        
                                            <div class="priceflights_holi">
                                                <div class="prcstrtingt">starting @ </div>
                                                <strong> ₹ </strong>
                                                <span class="hotel-price">1259</span>
                                            </div>
                                            
                                            <div class="rate_card">
                                            	<img src="<?php echo $GLOBALS['CI']->template->template_images('user-rating-4.png'); ?>" alt="" />
                                            </div>

                                            <button class="bookallbtn holybtn" type="submit"> View Details</button>
                                            
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>





            </div>    
            </div>   
			</div>
            
		</div>
	</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fa fa-bed"></i> Oops!</h1>
		<p>No hotels were found in this location today.</p>
		<p>
		Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
</section>
<?php
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script>
$(document).ready(function() {
	$('.vlulike').click(function(){
		$('.vlulike').removeClass('active');
		$(this).addClass('active');
	});
	
	/**
	*Load Hotels - Source Trigger
	*/
	function load_hotels() {
		pre_load_audio();
		var _active_booking_source = <?=$active_source?>;
		$.each(_active_booking_source, function(k, booking_source_name) {
			//core_hotel_loader(booking_source_name);			
		});
	}

	/**
	*Load Hotels Core Ajax
	*/
	function core_hotel_loader(booking_source_id)
	{
		$('.loader-image').show();
		var params = {booking_source:booking_source_id, search_id:<?php echo $hotel_search_params['search_id']?>, op:'load'};
		$.ajax({
			type: 'GET',
			url: app_base_url+'index.php/ajax/hotel_list',
			async:true,
			cache: true,
			data: $.param(params),
			dataType: 'json',
			success: function(result) {
				var ___data_r = Date.now();
				$('.loader-image').hide();
				hide_result_pre_loader();
				if (result.hasOwnProperty('status') == true && result.status == true) {
					var ___data_p = Date.now();
					console.log(((___data_p-___data_r)/1000)+' Seconds : Receiving Result');
					$('#hotel_search_result').html(result.data);
					var ___data_p = Date.now();
					console.log(((___data_p-___data_r)/1000)+' Seconds : Appending Result');
					//update total hotel count
					//update_hotel_summary_counter(result.total_result_count);
					post_load_audio();
					update_range_slider();
					enable_location_selector();
					enable_star_wrapper();
					var ___data_p = Date.now();
					console.log(((___data_p-___data_r)/1000)+' Seconds : Loadin Refiners');
					filter_row_origin_marker();
					$('.trigger-collapse-view ').trigger('click');
				} else {
					//No Result Found
					check_empty_search_result();
				}
				var ___data_p = Date.now();
				console.log(((___data_p-___data_r)/1000)+' Seconds : Over Result');
			}
		});
	}

	function check_empty_search_result()
	{
		if ($('.r-r-i:first').index() == -1) {
			$('#empty-search-result').show();
			$('#page-parent').hide();
		}
	}

	var room_list_cache = {};
	$(document).on('click', ".room-btn", function(e){
		e.preventDefault();
		var _cur_row_index = $(this).closest('.r-r-i');
		var _hotel_room_list = $(".room-list", _cur_row_index);
		var _result_index = $('[name="ResultIndex"]:first', _cur_row_index).val();
		if (_hotel_room_list.is(':visible') == false) {
			_hotel_room_list.show();
			if ((_result_index in room_list_cache) == false) {
				var _hotel_name = $('.h-name',_cur_row_index).text();
				var _hotel_star_rating = $('.h-sr',_cur_row_index).text();
				var _hotel_image = $('.hotel-image',_cur_row_index).attr('src');
				var _hotel_address = $('.hotel-address',_cur_row_index).text();
				$('.loader-image', _cur_row_index).show();
				$.post(app_base_url+"index.php/ajax/get_room_details", $('.room-form', _cur_row_index).serializeArray(), function(response) {
					if (response.hasOwnProperty('status') == true && response.status == true) {
						room_list_cache[_result_index] = true;
						$('.loader-image', _cur_row_index).hide();
						$(".room-summ", _cur_row_index).html(response.data);
						//update star rating and hotel name
						$('[name="HotelName"]', _cur_row_index).val(_hotel_name);
						$('[name="StarRating"]', _cur_row_index).val(_hotel_star_rating);
						$('[name="HotelImage"]').val(_hotel_image);//Balu A
						$('[name="HotelAddress"]').val(_hotel_address);//Balu A
					}
				});
			} else {
				$(".room-summ", _cur_row_index).show();
			}
		} else {
			_hotel_room_list.hide();
		}
	});

	//Load hotels from active source
	show_result_pre_loader();
	load_hotels();
	$(document).on('click', '.location-map', function() {
		$('#map-box-modal').modal();
	});
	
	
	/**
		* Toggle active class to highlight current applied sorting
		**/
		$(document).on('click', '.sorta', function(e) {
			e.preventDefault();
			$(this).closest('.sortul').find('.active').removeClass('active');
			//Add to sibling
			$(this).siblings().addClass('active');
		});

		//************************** **********/
		
		$('.toglefil').click(function() {
			$(this).toggleClass('active');
		});
		
		
		/*  Mobile Filter  */
	$('.filter_tab').click(function() {
		$('.resultalls').stop( true, true ).toggleClass('open');
		$('.coleft').stop( true, true ).slideToggle(500);
	});
	
	var widowwidth = $(window).width();
	if(widowwidth < 991)
	{
	$('.resultalls.open #hotel_search_result').on('click',function() {
		$('.resultalls').removeClass('open');
		$('.coleft').slideUp(500);
	});
	}
	
	
});
</script>
<?php
include_once COMMON_SHARED_JS.'/hotel_sorter_set.php';
?>
<div class="modal fade bs-example-modal-lg" id="map-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Hotel Location Map</h4>
			</div>
			<div class="modal-body">
				<iframe src="" id="map-box-frame" name="map_box_frame" style="height: 500px;width: 850px;">
				</iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
echo $this->template->isolated_view('share/media/hotel_search');
?>
