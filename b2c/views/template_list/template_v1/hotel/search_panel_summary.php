<main class="search-result-main">
	<div class="container">
		<div class="panel panel-default b-r-0 ser_hotl_rslts">
			<div class="panel-body p-0">
				<div class="row m-0 lg-flex">
					<div class="col-lg-2 col-md-4 col-sm-6 p-tb-10 lg-text-center b-r">
						<br>
						<h1 class="h4"><?php echo $hotel_search_params['city_name'].' - '.$hotel_search_params['country_name'];?></h1>
					</div>
					<div class="col-lg-2 col-md-4 col-sm-6 col-xs-6 p-tb-10 b-r">
						<h2 class="h4"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/property-type-icon.png'); ?>" alt="Calendar Icon"> Check In</h2>
						<p class="h6 m-b-0"><?php echo app_friendly_absolute_date($hotel_search_params['from_date']); ?></p>
					</div>
					<div class="clearfix visible-sm-block"></div>
					<div class="col-lg-2 col-md-4 col-sm-6 col-xs-6 p-tb-10 b-r">
						<h2 class="h4"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/property-type-icon.png'); ?>" alt="Calendar Icon"> Check Out</h2>
						<p class="h6 m-b-0"><?php echo app_friendly_absolute_date($hotel_search_params['to_date']); ?></p>
					</div>
					<div class="clearfix visible-md-block"></div>
					<div class="col-lg-1 col-md-4 col-sm-6 col-xs-6 p-tb-10 b-r">
						<h2 class="h4">Night(s)</h2>
						<h3 class="h5"><?php echo $hotel_search_params['no_of_nights'];?></h3>
					</div>
					<div class="clearfix visible-sm-block"></div>
					<div class="col-lg-1 col-md-4 col-sm-6 col-xs-6 p-tb-10 b-r">
						<h2 class="h4">Room(s)</h2>
						<h3 class="h5"><?php echo $hotel_search_params['room_count']?></h3>
					</div>
					<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12 p-tb-10 b-r">
						<h2 class="h4">Passengers</h2>
						<div class="btn-toolbar psng-icons" role="toolbar">
							<div class="btn-group btn-group-xs" role="group">
								<button type="button" class="btn btn-default b-r-0">
									<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/male-icon.png')?>" alt="Adult"> <?php echo array_sum($hotel_search_params['adult_config'])?>
								</button>
							</div>
							<div class="btn-group btn-group-xs" role="group">
								<button type="button" class="btn btn-default b-r-0">
									<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/child-icon.png')?>" alt="Child"> <?php echo array_sum($hotel_search_params['child_config'])?>
								</button>
							</div>
						</div>
					</div>
					<div class="clearfix visible-sm-block"></div>
					<div class="col-lg-2 col-md-4 col-sm-6 p-tb-10">
						<button type="button" class="margt15 btn btn-lg btn-block btn-p b-r-0 hotel_search_form" data-toggle="collapse" href="#hotel_search_form">Modify Search</button>
					</div>
				</div>
				<div id="hotel_search_form" class="collapse">
					<div class="panel panel-default m-0 b-r-0">
						<div class="panel-body">
							<?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>