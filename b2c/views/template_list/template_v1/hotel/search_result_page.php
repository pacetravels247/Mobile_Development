<?php
//debug($hotel_search_params);exit;
$data['result'] = $hotel_search_params;
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
function get_sorter_set()
{
	return '<nav class="">
  <div class="container-fluid">
    <!-- Collect the nav links, forms, and other content for toggling -->
      <ul class="nav nav-justified nav-inverse">
        <li class="sortMobhtl"><a href="#">Sort</a></li>
        <li class="dropdown bg-default sortMobhtl">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-arrows-v"></i> Hotel Name <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#" class="name-l-2-h loader"><i class="fa fa-sort-alpha-asc"></i> A To Z</a></li>
            <li class="divider"></li>
            <li><a href="#" class="name-h-2-l loader"><i class="fa fa-sort-alpha-desc"></i> Z to A</a></li>
          </ul>
        </li>
        <li class="dropdown bg-default sortMobhtl">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-arrows-v"></i> Star <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#" class="star-l-2-h loader"><i class="fa fa-sort-numeric-asc"></i> Low To High</a></li>
            <li class="divider"></li>
            <li><a href="#" class="star-h-2-l loader"><i class="fa fa-sort-numeric-desc"></i> High to Low</a></li>
          </ul>
        </li>
        <li class="dropdown bg-default sortMobhtl">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-arrows-v"></i> Price <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#" class="price-l-2-h loader"><i class="fa fa-sort-up"></i> Low To High</a></li>
            <li class="divider"></li>
            <li><a href="#" class="price-h-2-l loader"><i class="fa fa-sort-down"></i> High to Low</a></li>
          </ul>
        </li>
      </ul>
  </div><!-- /.container-fluid -->
</nav>';
}
echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
?>
<section class="search-result">
	<div class="container">
	<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/hotel_result_pre_loader',$data);?>
		<div class="row">
			<div class="col-lg-2">
				<h2 class="h4">Refine your search</h2>
				<div class="panel panel-default b-r-0">
					<div class="panel-body refine">
						<div class="row">
						<form>
							<div class="col-lg-12 col-md-4 col-sm-6 hidden-xs hidden-sm">
								<h3 class="h4 m-t-0 trigger-collapse-view refine-header" data-toggle="collapse" data-target=".price-refine"><img src=" <?php echo $GLOBALS['CI']->template->template_images('icons/price-tag-icon.png')?>" alt="Price Tag Icon"> Price <?php echo $mini_loading_image?></h3>
								<div class="collapse price-refine">
									<div id="price-range"></div>
									<span id="hotel-price"></span>
								</div>
							</div>
							<div class="col-lg-12 col-md-4 col-sm-6 hidden-xs hidden-sm">
								<hr class="hr-10 visible-lg-block">
								<h3 class="h4 m-t-0 trigger-collapse-view refine-header" data-toggle="collapse" data-target=".starrating-refine"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/hotel-star-icon.png')?>" alt="Hotel Star Icon"> Star Rating <?php echo $mini_loading_image?></h3>
								<div class="collapse starrating-refine">
									<?php
									$min_star_rating = 0;
									$max_star_rating = 5;
									$allowed_min_star = 1;
									$allowed_max_star = 5;
									$inverse_star_key = array(0 => -1, 1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1);
									for ($min_star_rating=0; $min_star_rating <= $max_star_rating; $min_star_rating++) {
										$star_rating_id = 'visible-star-rating-value-'.$min_star_rating;
									?>
										<h6 class="h4">
										<input type="checkbox" class="star-filter" checked id="<?php echo $star_rating_id?>" value="<?php echo $min_star_rating; ?>">
											<label for="<?php echo $star_rating_id?>">
												<span class="rating-no">
												<?php
													for ($allowed_min_star=1; $allowed_min_star<=$allowed_max_star; $allowed_min_star++) {
														if ($inverse_star_key[$min_star_rating] == $allowed_min_star) {
															$active_star = 'active';
														} else {
															$active_star = '';
														}
														echo '<span class="star '.$active_star.'"></span>';
													}
												?>
												</span>
												<span class="star-filter-text-help"><?php echo $min_star_rating;?></span>
											</label>
										</h6>
									<?php
									}
									?>
								</div>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6 hidden-xs hidden-sm">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 trigger-collapse-view refine-header" data-toggle="collapse" data-target=".propertytype-refine"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/property-type-icon.png')?>" alt="Property Type Icon"> <abbr title="Property">P</abbr> Type  <?php echo $mini_loading_image?></h3>
								<div class="collapse propertytype-refine">
									<div class="checkbox">
										<label>
											<input type="checkbox" value="" checked> Hotel
										</label>
									</div>
								</div>
							</div>
							<div class="clearfix visible-md-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 trigger-collapse-view refine-header" data-toggle="collapse" data-target=".hotelsearch-refine"> Hotel Name <?php echo $mini_loading_image?></h3>
								<div class="collapse hotelsearch-refine">
									<div class="form-group">
										<label class="sr-only" for="hotel-search">Hotel Name</label>
										<div class="input-group">
											<input type="text" class="form-control b-r-0" id="hotel-name">
											<div class="input-group-btn">
												<button type="submit" class="btn btn-default b-r-0" id="hotel-name-search-btn"><i class="fa fa-search fa-fw"></i></button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6 hidden-xs hidden-sm">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 trigger-collapse-view refine-header" data-toggle="collapse" data-target=".location-refine"> Location <?php echo $mini_loading_image?></h3>
								<div class="collapse location-refine">
									<form>
										<div class="form-group">
											<label class="sr-only" for="hotel-location">Hotel Location</label>
											<select id="hotel-location" class="form-control b-r-0">
												<option value="none">select</option>
											</select>
										</div>
									</form>
								</div>
							</div>
							<div class="col-lg-12">
								<button type="reset" class="btn btn-t btn-block b-r-0 hide">Reset Filters</button>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-10">
				<h2 class="h4 text-p"><?php echo $mini_loading_image?><span id="total_records">...</span> Hotels Found</h2>
				<div class="btn-group" role="group">
					<label type="button" class="btn btn-default btn-lg b-r-0 filter-hotels-view" for="all-hotels-view">
						<input type="radio" id="all-hotels-view" value="all" class="hide deal-status-filter" name="deal_status[]" checked="checked">
						<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/property-hotel-icon.png')?>" alt="Property Hotel Icon"> All Hotels</label>
					<label type="button" class="btn btn-default btn-lg b-r-0 filter-hotels-view" for="deal-hotels-view">
						<input type="radio" id="deal-hotels-view" value="filter" class="hide deal-status-filter" name="deal_status[]">
						<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/hotel-deal-icon.png')?>" alt="Hotel Deal Icon"> Deal
					</label>
				</div>
				<hr class="hr-10">
					<div class="row text-center">
						<?php echo get_sorter_set();?>
					</div>
				<hr class="hr-10">
				<?php echo $loading_image;?>
				<div id="hotel_search_result" class="panel xs-text-center hotel-search-result-panel panel-default b-r-0 m-b-10">
				</div>

				<div id="empty_hotel_search_result">
				</div>

				<hr class="hr-10">
				<div class="clearfix">
					<div class="pull-right">
						<span class="h5">Showing: <?php echo $mini_loading_image?><span class="visible-row-record-count">...</span> of <span class="total-row-record-count">...</span> hotels</span>
					</div>
				</div>
				<hr class="hr-10 invisible">
			</div>
		</div>
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
	/**
	*Load Hotels - Source Trigger
	*/
	function load_hotels() {
		pre_load_audio();
		var _active_booking_source = <?=$active_source?>;
		$.each(_active_booking_source, function(k, booking_source_name) {
			core_hotel_loader(booking_source_name);			
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
				$('.loader-image').hide();
				hide_result_pre_loader();
				if (result.hasOwnProperty('status') == true && result.status == true) {
					$('#hotel_search_result').html(result.data);
					post_load_audio();
					//update total hotel count
					//update_hotel_summary_counter(result.total_result_count);
					update_range_slider();
					enable_location_selector();
					//filter_row_origin_marker();
					update_total_count_summary();
					$('.trigger-collapse-view ').trigger('click');
				}
			}
		});
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
				$('.loader-image', _cur_row_index).show();
				$.post(app_base_url+"index.php/ajax/get_room_details", $('.room-form', _cur_row_index).serializeArray(), function(response) {
					if (response.hasOwnProperty('status') == true && response.status == true) {
						room_list_cache[_result_index] = true;
						$('.loader-image', _cur_row_index).hide();
						$(".room-summ", _cur_row_index).html(response.data);
						//update star rating and hotel name
						$('[name="HotelName"]', _cur_row_index).val(_hotel_name);
						$('[name="StarRating"]', _cur_row_index).val(_hotel_star_rating);
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
echo $this->template->isolated_view('share/media/hotel_search');
?>