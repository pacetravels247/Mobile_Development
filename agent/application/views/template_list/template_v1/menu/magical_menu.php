<?php
$date = date('d-m-Y');
$active_domain_modules = $this->active_domain_modules;
/**
 * Need to make privilege based system
 * Privilege only for loading menu and access of the web page
 * 
 * Data loading will not be based on privilege.
 * Data loading logic will be different.
 * It depends on many parameters
 */
$menu_list = array();
if (count($active_domain_modules) > 0) {
	$any_domain_module = true;
} else {
	$any_domain_module = false;
}
$airline_module = is_active_airline_module();
$accomodation_module = is_active_hotel_module();
$bus_module = is_active_bus_module();
$package_module = is_active_package_module();
$sightseeing_module = is_active_sightseeing_module();
$car_module = is_active_car_module();
$transfer_module =is_active_transferv1_module();
// debug($car_module);exit;
?>
<ul class="sidebar-menu">
	<li class="header">MAIN NAVIGATION</li>
	<li class="active treeview">
		<a href="<?php echo base_url()?>">
			<i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
		</a>
	</li>
	<!-- USER ACCOUNT MANAGEMENT -->
	<li class="treeview">
		<a href="#">
			<i class="fa fa-search"></i><span> Search </span><i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
		<!-- USER TYPES -->
			<?php if ($airline_module) { ?>
			<li><a href="<?=base_url().'menu/dashboard/flight/?default_view='.META_AIRLINE_COURSE?>"><i class="<?=get_arrangement_icon(META_AIRLINE_COURSE)?> text-blue"></i> <span class="hidden-xs">Flight</span></a></li>
			<?php } ?>
			<?php if ($accomodation_module) { ?>
			<li><a href="<?=base_url().'menu/dashboard/hotel/?default_view='.META_ACCOMODATION_COURSE?>"><i class="<?=get_arrangement_icon(META_ACCOMODATION_COURSE)?> text-green"></i> <span class="hidden-xs">Hotel</span></a></li>
			<?php } ?>
			<?php if ($bus_module) { ?>
			<li><a href="<?=base_url().'menu/dashboard/bus/?default_view='.META_BUS_COURSE?>"><i class="<?=get_arrangement_icon(META_BUS_COURSE)?> text-red"></i> <span class="hidden-xs">Bus</span></a></li>
			<?php } ?>
			<?php if($transfer_module){?>
				<li><a href="<?=base_url().'menu/dashboard/transfers/?default_view='.META_TRANSFERV1_COURSE?>"><i class="<?=get_arrangement_icon(META_TRANSFERV1_COURSE)?> text-red"></i> <span class="hidden-xs">Transfers</span></a></li>
			<?php }?>
			<?php if($sightseeing_module){?>
				<li><a href="<?=base_url().'menu/dashboard/sightseeing/?default_view='.META_SIGHTSEEING_COURSE?>"><i class="<?=get_arrangement_icon(META_SIGHTSEEING_COURSE)?> text-red"></i> <span class="hidden-xs">Activities</span></a></li>
			<?php }?>
			<?php if($car_module){?>
				<li><a href="<?=base_url().'menu/dashboard/car/?default_view='.META_CAR_COURSE?>"><i class="<?=get_arrangement_icon(META_CAR_COURSE)?> text-red"></i> <span class="hidden-xs">Car</span></a></li>
			<?php }?>
			<?php if ($package_module) { ?>
			<li><a href="<?=base_url().'menu/dashboard/package/?default_view='.META_PACKAGE_COURSE?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?> text-yellow"></i> <span class="hidden-xs">Holiday</span></a></li>
			<?php } ?>
		</ul>
	</li>
	<?php if ($any_domain_module) {?>
	<li class="treeview">
		<a href="#">
			<i class="far fa-chart-bar"></i> 
			<span> Reports </span><i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
		<!-- USER TYPES -->
			<li><a href="#"><i class="fa fa-book"></i> Booking Details</a>
				<ul class="treeview-menu">
				<?php if ($airline_module) { ?>
				<li><a href="<?php echo base_url().'report/flight/';?>"><i class="<?=get_arrangement_icon(META_AIRLINE_COURSE)?>"></i> Flight</a></li>
				<?php } ?>
				<?php if ($accomodation_module) { ?>
				<li><a href="<?php echo base_url().'report/hotel/';?>"><i class="<?=get_arrangement_icon(META_ACCOMODATION_COURSE)?>"></i> Hotel</a></li>
				<?php } ?>
				<?php if ($bus_module) { ?>
				<li><a href="<?php echo base_url().'report/bus/';?>"><i class="<?=get_arrangement_icon(META_BUS_COURSE)?>"></i> Bus</a></li>
				<?php } ?>
				<?php if($transfer_module){?>
					<li><a href="<?php echo base_url().'report/transfers/';?>"><i class="<?=get_arrangement_icon(META_TRANSFERV1_COURSE)?>"></i>Transfers</a></li>
				<?php }?>
				<?php if($sightseeing_module):?>
					<li><a href="<?php echo base_url().'report/activities/';?>"><i class="<?=get_arrangement_icon(META_SIGHTSEEING_COURSE)?>"></i>Activities</a></li>
				<?php endif;?>
				<li><a href="<?php echo base_url().'report/package_enquiry_report/';?>"><i class="far fa-cube"></i> <span>Package</span></a></li>
				<?php if($car_module):?>
				<li><a href="<?php echo base_url().'report/car/';?>"><i class="<?=get_arrangement_icon(META_CAR_COURSE)?>"></i>Car</a></li>
				<?php endif;?>
				</ul>
			</li>
			<li><a href="<?php echo base_url().'management/pnr_search'?>"><i class="fa fa-search"></i> <span>PNR Search</span></a></li>
			<li><a href="<?php echo base_url().'report/flight?filter_booking_status=BOOKING_PENDING'?>"><i class="far fa-ticket"></i> <span>Pending Ticket</span></a></li>
			<li><a href="<?php echo base_url().'report/daily_sales_report' ?>"><i class="far fa-chart-bar"></i> <span>Daily Sales Report</span></a></li>
			<li><a href="<?php echo base_url().'management/account_ledger?created_datetime_from='.$date?>"><i class="far fa-calculator"></i> <span>Account Ledger</span></a></li>
			<li class="treeview"><a href="<?php echo base_url().'index.php/transaction/logs?created_datetime_from='.$date?>"><i class="far fa-list-alt"></i> <span> Transaction Logs </span></a></li>
			<li><a href="<?php echo base_url().'index.php/report/bookings_list/flight?created_datetime_from='.$date?>"><i class="far fa-cube"></i> <span>Supplier wise</span></a></li>
		</ul>
	</li>
	<li class="treeview">
		<a href="<?php echo base_url().'management/cancel_ticket'?>">
			<i class="fas fa-ban"></i> <span>Cancellation</span>
		</a>
	</li>
	<li class="treeview">
		<a href="#">
			<i class="far fa-chart-bar"></i> 
			<span> Enquiries </span><i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
		<!-- USER TYPES -->	
			<?php if ($package_module) { ?>
			<li><a href="#"><i class="fa fa-book"></i>Package Enquiry</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo base_url().'index.php/tours/tours_enquiry';?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?>"></i>Pending Enquiry</a></li>
					<li><a href="<?php echo base_url().'index.php/tours/confirmed_tours_enquiry';?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?>"></i>Confirmed Enquiry</a></li>
				</ul>
			</li>
			<li><a href="#"><i class="fa fa-book"></i>Custom Enquiry</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo base_url().'index.php/tours/custom_enquiry_report';?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?>"></i>Pending Enquiry</a></li>
					<li><a href="<?php echo base_url().'index.php/tours/confirmed_custom_enquiry';?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?>"></i>Confirmed Enquiry</a></li>
				</ul>
			</li>	
			<?php } ?>
		</ul>		
			
			
	</li>
	<?php
	if(($airline_module || $bus_module || $sightseeing_module) && 0) {
	?>
	<li class="treeview">
		<a href="#">
			<i class="fa fa-briefcase"></i> 
			<span> My Commission </span><i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
			<?php if ($airline_module) { ?>
			<li><a href="<?=base_url().'management/flight_commission';?>"><i class="<?=get_arrangement_icon(META_AIRLINE_COURSE)?>"></i> Flight</a></li>
			<?php } ?>
			<?php if ($bus_module) { ?>
			<li><a href="<?=base_url().'management/bus_commission';?>"><i class="<?=get_arrangement_icon(META_BUS_COURSE)?>"></i> Bus</a></li>
			<?php } ?>
			<?php if($transfer_module):?>
				<li><a href="<?=base_url().'management/transfer_commission';?>"><i class="<?=get_arrangement_icon(META_TRANSFERV1_COURSE)?>"></i>Transfers</a></li>

			<?php endif;?>
			<?php if($sightseeing_module){?>
				<li><a href="<?=base_url().'management/sightseeing_commission';?>"><i class="<?=get_arrangement_icon(META_SIGHTSEEING_COURSE)?>"></i>Activities</a></li>
			<?php }?>
		</ul>
	</li>
	<?php } ?>
	<li class="treeview">
		<a href="#">
			<i class="fa fa-plus-square"></i> 
			<span> My Markup </span><i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
		<!-- USER TYPES -->
				<?php if ($airline_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_airline_markup/';?>"><i class="<?=get_arrangement_icon(META_AIRLINE_COURSE)?>"></i> Flight</a></li>
				<?php } ?>
				<?php if ($accomodation_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_hotel_markup/';?>"><i class="<?=get_arrangement_icon(META_ACCOMODATION_COURSE)?>"></i> Hotel</a></li>
				<?php } ?>
				<?php if ($bus_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_bus_markup/';?>"><i class="<?=get_arrangement_icon(META_BUS_COURSE)?>"></i> Bus</a></li>
				<?php } ?>

				<?php if ($transfer_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_transfer_markup/';?>"><i class="<?=get_arrangement_icon(META_TRANSFERV1_COURSE)?>"></i>Transfers</a></li>
				<?php } ?>

				<?php if ($sightseeing_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_sightseeing_markup/';?>"><i class="<?=get_arrangement_icon(META_SIGHTSEEING_COURSE)?>"></i>Activities</a></li>
				<?php } ?>
				<?php if ($package_module) { ?>
				<li><a href="<?php echo base_url().'management/b2b_holiday_markup/';?>"><i class="<?=get_arrangement_icon(META_PACKAGE_COURSE)?>"></i>Holiday</a></li>
				<?php } ?>
				<?php if($car_module){?>
				<li><a href="<?=base_url().'management/b2b_car_markup';?>"><i class="<?=get_arrangement_icon(META_CAR_COURSE)?>"></i>Car</a></li>
			<?php }?>

		</ul>
	</li>
	<?php } ?>
	<li><a href="<?php echo base_url().'management/bus_operator_cancellation'?>">
	<i class="fa fa-shield"></i> <span>Bus Operator Cancel request</span></a></li>
	<li><a href="<?php echo base_url().'management/booking_calender'?>">
	<i class="fa fa-shield"></i> <span>Booking Calender</span></a></li>
	<li><a href="<?php echo base_url().'management/tds_certificates'?>">
	<i class="fa fa-shield"></i> <span>TDS Certificates</span></a></li>
	<li class="treeview">
		<a href="#">
			<i class="fab fa-google-wallet"></i> 
			<span> Payment </span><i class="fa fa-angle-left pull-right"></i>
		</a>
		<ul class="treeview-menu">
		<!-- USER TYPES -->
			<li><a href="<?php echo base_url().'management/b2b_balance_manager'?>"><i class="fas fa-rupee-sign"></i> Update Balance</a></li>
			<!-- <li><a href="<?php echo base_url().'management/b2b_credit_limit?transaction_type=Credit'?>"><i class="fas fa-rupee-sign"></i> Update Credit Limit</a></li>-->
			<li><a href="<?php echo base_url().'management/b2b_credit_limit?transaction_type=Instant_Recharge'?>"><i class="fas fa-rupee-sign"></i> Instant Recharge</a></li>
			<li><a href="<?php echo base_url().'index.php/management/bank_account_details'?>"><i class="fas fa-university"></i> Bank Account Details</a></li>
		</ul>
	</li>
	<li><a href="<?php echo base_url().'management/set_balance_alert'?>"><i class="fa fa-bell"></i> <span>Set Balance Alert</span></a></li>

	<li><a href="<?php echo base_url().'management/set_advertisement'?>"><i class="fa fa-laptop"></i> <span>Set Advertisement</span></a></li>
	<li><a href="<?php echo base_url().'general/fare_calender'?>"><i class="far fa-users"></i> <span>Booking Calender</span></a></li>
	<li><a href="<?php echo base_url().'general/group_request'?>"><i class="far fa-users"></i> <span>Group Booking</span></a></li>
	<li><a href="<?php echo base_url().'general/group_booking'?>"><i class="far fa-users"></i> <span>Group Booking requests</span></a></li>

	<li><a href="<?php echo base_url().'user/domain_logo'?>"><i class="fa fa-image"></i> <span>Logo</span></a></li>
	</ul>
