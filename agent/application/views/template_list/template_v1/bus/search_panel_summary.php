<?php //Images Url
$template_images = $GLOBALS['CI']->template->template_images();
?>
<main class="search-result-main">
	<div class="container">
		<div class="panel panel-default b-r-0">
			<div class="panel-body p-0">
				<div class="row m-0 lg-flex">
					<div class="col-lg-3 col-md-4 col-sm-5 p-tb-10">
						<div class="form-group">
							<label for="bus-from">From</label>
							<input type="text" readonly class="form-control b-r-0 bus_search_form hand-cursor" value="<?=$bus_search_params['bus_station_from']?>" data-toggle="collapse" href="#bus_search_form">
						</div>
					</div>
					<div class="col-lg-2 col-md-4 col-sm-2 text-center mobHide">
						<br>
						<img src="<?=$template_images?>icons/bus-from-to-icon.png" alt="Bus From To Icon">
					</div>
					<div class="col-lg-3 col-md-4 col-sm-5 p-tb-10 b-r">
						<div class="form-group">
							<label for="bus-to">To</label>
							<input type="text" readonly id="bus-to" class="form-control b-r-0 bus_search_form hand-cursor" value="<?=$bus_search_params['bus_station_to']?>" data-toggle="collapse" href="#bus_search_form">
						</div>
					</div>
					<div class="clearfix visible-md-block visible-sm-block"></div>
					<div class="col-lg-2 col-md-4 col-sm-6 p-tb-10 b-r bus_search_form hand-cursor" data-toggle="collapse" href="#bus_search_form">
						<h2 class="h4"><img src="<?=$template_images?>icons/calendar-search-result-icon.png" alt="Calendar Icon"> Date of Journey</h2>
						<p class="h6 m-b-0"><?=date('jS \ M Y',strtotime($bus_search_params['bus_date_1']))?></p>
					</div>
					<div class="col-lg-2 col-md-4 col-sm-6 p-tb-10">
						<br>
						<button type="button" class="btn btn-lg btn-block btn-p b-r-0 bus_search_form" data-toggle="collapse" href="#bus_search_form">Modify Search</button>
					</div>
				</div>
				<div id="bus_search_form" class="collapse">
					<div class="panel panel-default m-0 b-r-0">
						<div class="panel-body">
							<?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>