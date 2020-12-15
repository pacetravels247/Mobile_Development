<?php
$default_view = $GLOBALS ['CI']->uri->segment ( 2 );
?>
<ul class="nav nav-tabs  b2b_navul" role="tablist" id="myTab">
	<?php if (is_active_airline_module()) { ?>
	<li class="<?=(($default_view == 'flight_commission') ? 'active' : '')?>">
		 <a href="<?php echo base_url();?>management/flight_commission"><img alt="Flight Icon" src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.META_AIRLINE_COURSE.'-nav-icon.png')?>"> Flight</a>
	</li>
	<?php } ?>
	<?php if (is_active_bus_module()) { ?>
	<li class="<?=(($default_view == 'bus_commission') ? 'active' : '')?>">
		<a href="<?php echo base_url();?>management/bus_commission"><img alt="BUS Icon" src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.META_BUS_COURSE.'-nav-icon.png')?>"> Bus</a>
	</li>
	<?php } ?>

	<?php if (is_active_transferv1_module()) { ?>
	<li class="<?=(($default_view == 'transfer_commission') ? 'active' : '')?>">
		<a href="<?php echo base_url();?>management/transfer_commission"><img alt="TsIcon" src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.META_TRANSFERV1_COURSE.'-nav-icon.png')?>">Transfers</a>
	</li>
	<?php } ?>

	<?php if (is_active_sightseeing_module()) { ?>
	<li class="<?=(($default_view == 'sightseeing_commission') ? 'active' : '')?>">
		<a href="<?php echo base_url();?>management/sightseeing_commission"><img alt="SSIcon" src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.META_SIGHTSEEING_COURSE.'-nav-icon.png')?>">Activities</a>
	</li>
	<?php } ?>


</ul>
