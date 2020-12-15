<?php
	$active_domain_modules = $this->active_domain_modules;
	$default_active_tab = $default_view;
	//echo "<pre>"; print_r($holiday_data);exit;
	
	/**
	 * set default active tab
	 * @param string $module_name		 name of current module being output
	 * @param string $default_active_tab default tab name if already its selected otherwise its empty
	 */
	function set_default_active_tab($module_name, &$default_active_tab)
	{
		if (empty($default_active_tab) == true || $module_name == $default_active_tab) {
			if (empty($default_active_tab) == true) {
				$default_active_tab = $module_name; // Set default module as current active module
			}
			return 'active';
		}
	}
	?>
<!-- main -->
<main class="main-main header search-engine" id="top" style="background-image: url('<?=$GLOBALS['CI']->template->domain_images()?>banner-bg.jpg');">
	<div class="container">
		<div role="tabpanel" class="clearfix">
			<!-- Tab panes -->
			<div class="tab-content highlight">
				<?php if (is_active_airline_module()) { ?>
				<div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>" id="flight">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_search') ?>
				</div>
				<?php } ?>
				<?php if (is_active_hotel_module()) { ?>
				<div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>" id="hotel">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search') ?>
				</div>
				<?php } ?>
				<?php if (is_active_package_module()) { ?>
				<div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>" id="holiday">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/holiday_search',$holiday_data) ?>
				</div>
				<?php } ?>
				<?php if (is_active_bus_module()) { ?>
				<div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>" id="bus">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search') ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</main>
<!-- end main -->