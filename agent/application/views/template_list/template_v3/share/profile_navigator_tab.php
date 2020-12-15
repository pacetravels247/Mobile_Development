<?php
	if($GLOBALS['CI']->uri->segment(2) != 'profile') {
		$navigator_url = base_url().'index.php/user/profile';
		$navigator_toggle = '';
	}else {
		$navigator_url = '';
		$navigator_toggle = 'data-aria-controls="home" data-role="tab" data-toggle="tab"';
	}
?>
<ul class="nav nav-tabs customteam customteam2" role="tablist">
	<li data-role="presentation" class="<?php echo (((isset($_GET['active']) == false && empty($navigator_url) == true) || @$_GET['active'] == 'dashboard'))? 'active' : ''?>">
		<a class="a_dashboard" href="<?php echo empty($navigator_url) ? '#dashbrd' : $navigator_url.'?active=dashboard';?>" <?=$navigator_toggle?>>
		<span class="fa fa-dashboard icon_sml_mob"></span><strong>Dashboard</strong></a>
	</li>
	<li data-role="presentation" class="<?php echo (@$_GET['active'] == 'profile')? 'active' : ''?>">
		<a class="a_profile" href="<?php echo empty($navigator_url) ? '#profile' : $navigator_url.'?active=profile';?>" <?=$navigator_toggle?>>
		<span class="fa fa-user icon_sml_mob"></span><strong>Profile</strong></a>
	</li>
	<li data-role="presentation" class="<?php echo (@$_GET['active'] == 'traveller')? 'active' : ''?>">
		<a class="a_traveller" href="<?php echo empty($navigator_url) ? '#travellerinfo' : $navigator_url.'?active=traveller';?>" <?=$navigator_toggle?>>
		<span class="fa fa-users icon_sml_mob"></span><strong>Traveller Information</strong></a>
	</li>
	<li data-role="presentation" class="<?php echo empty($navigator_url) ? '' : 'active'?>">
		<a href="<?=base_url().'index.php/report/flights'?>"><span class="fa fa-ticket icon_sml_mob"></span><strong>Bookings</strong></a>
	</li>
</ul>