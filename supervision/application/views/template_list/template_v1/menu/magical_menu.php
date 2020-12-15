<?php
$date = date('d-m-Y');
$active_domain_modules = $GLOBALS['CI']->active_domain_modules;
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
$sightseen_module = is_active_sightseeing_module();
$car_module = is_active_car_module();
$transferv1_module = is_active_transferv1_module();



$bb = 'b2b';
$bc = 'b2c';
//debug($active_model);exit;
if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.50.110.215")
{
//echo 'ACP',debug($GLOBALS['CI']);exit;
}

$b2b =$active_model[1]['status'];
if(!isset($b2b))
{
   $b2b = is_active_module($bb);
}
$b2c =$active_model[0]['status'];
if(!isset($b2c))
{
   $b2c = is_active_module($bc);
}

//checking social login status 
$social_login = 'facebook';
$social = is_active_social_login($social_login);
//echo "ela".$accomodation_module;exit;
$accomodation_module = 1;

?>
<ul class="sidebar-menu" id="magical-menu">
    <?php if(check_user_previlege('p1')):?>
    <li class="treeview">
        <a href="<?php echo base_url() ?>">
            <i class="far fa-tachometer-alt"></i> <span>Dashboard</span> </a>
    </li>
    <?php endif; ?>
    <?php if (is_domain_user() == false) {

     // ACCESS TO ONLY PROVAB ADMIN ?>
        <li class="treeview">
            <a href="#">
                <i class="far fa-wrench"></i> <span>Management</span> <i class="far fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="<?php echo base_url() . 'index.php/user/user_management' ?>"><i class="far fa-user"></i> User</a></li>
                <li><a href="<?php echo base_url() . 'index.php/user/domain_management' ?>"><i class="far fa-laptop"></i> Domain</a></li>
                <li><a href="<?php echo base_url() . 'index.php/module/module_management' ?>"><i class="far fa-sitemap"></i> Master Module</a></li>
            </ul>
        </li>

        <?php if ($any_domain_module) {?>
            <li class="treeview">
                <a href="#">
                    <i class="far fa-user"></i> <span>Markup</span> <i class="far fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if ($airline_module) { ?>
                        <li><a href="<?php echo base_url() . 'index.php/private_management/airline_domain_markup' ?>"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i> Flight</a></li>
                    <?php } ?>
                    <?php if ($accomodation_module) { ?>
                        <li><a href="<?php echo base_url() . 'index.php/private_management/hotel_domain_markup' ?>"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i> Hotel</a></li>
                    <?php } ?>
                    <?php if ($bus_module) { ?>
                        <li><a href="<?php echo base_url() . 'index.php/private_management/bus_domain_markup' ?>"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i> Bus</a></li>
                    <?php } ?>
                    <?php if ($transferv1_module) { ?>
                        <li><a href="<?php echo base_url() . 'index.php/private_management/transfer_domain_markup' ?>"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i>Transfers</a></li>
                    <?php } ?>

                    <?php if ($sightseen_module) { ?>
                        <li><a href="<?php echo base_url() . 'index.php/private_management/sightseeing_domain_markup' ?>"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i>Activities</a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>
        <li class="treeview">
            <a href="<?php echo base_url() . 'index.php/private_management/process_balance_manager' ?>">
                <i class="far fa-google-wallet"></i> 
                <span> Master Balance Manager </span>
            </a>
        </li>
        <li class="treeview">
            <a href="<?php echo base_url() . 'index.php/private_management/event_logs' ?>">
                <i class="far fa-shield"></i> 
                <span> Event Logs </span>
            </a>
        </li>
    <?php
    } else if ((is_domain_user() == true)) {
    	
        // ACCESS TO ONLY DOMAIN ADMIN
        ?>
        <!-- USER ACCOUNT MANAGEMENT -->
        <?php if(check_user_previlege('p2')):?>

        <li class="treeview">
            <a href="#">
                <i class="far fa-user"></i> 
                <span> Users </span><i class="far fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                <!-- USER TYPES -->
                <?php if ($b2c) { if(check_user_previlege('p17')): ?>
                    <li><a href="<?php echo base_url() . 'index.php/user/b2c_user?filter=user_type&q=' . B2C_USER; ?>"><i class="far fa-circle"></i> B2C</a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo base_url() . 'index.php/user/b2c_user?filter=user_type&q=' . B2C_USER . '&user_status=' . ACTIVE; ?>"><i class="far fa-check"></i> Active</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2c_user?filter=user_type&q=' . B2C_USER . '&user_status=' . INACTIVE; ?>"><i class="far fa-times"></i> InActive</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2c_user?filter=user_type&q=' . B2C_USER . '&user_status=' . LOCK; ?>"><i class="far fa-anchor"></i> Locked</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/get_logged_in_users?filter=user_type&q=' . B2C_USER; ?>"><i class="far fa-circle"></i> Logged In User</a></li>
                        </ul>
                    </li>
                <?php endif; } ?>

                <?php 

                if ($b2b) { if(check_user_previlege('p24')): ?>

                    <li><a href="<?php echo base_url() . 'index.php/user/b2b_user?filter=user_type&q=' . B2B_USER ?>"><i class="far fa-circle"></i> Agents</a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo base_url() . 'index.php/user/b2b_user?user_status=' . ACTIVE; ?>"><i class="far fa-check"></i> Active</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2b_user?user_status=' . INACTIVE; ?>"><i class="far fa-times"></i> Inactive</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2b_user?user_status=' . LOCK; ?>"><i class="far fa-anchor"></i> Locked</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2b_user?due_list=' . ACTIVE; ?>"><i class="fas fa-rupee-sign"></i> Due List</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/b2b_user'; ?>"><i class="far fa-check"></i> All</a></li>
                            <li><a href="<?php echo base_url() . 'index.php/user/get_logged_in_users?filter=user_type&q=' . B2B_USER; ?>"><i class="far fa-circle"></i> Logged In User</a></li>
                        </ul>
                    </li>
                    <li class=""><a href="<?php echo base_url().'index.php/management/user_group'?>"><i class="far fa-circle"></i> User Groups</a></li>
                <?php endif; } if(check_user_previlege('p73')):?>
                <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . SUB_ADMIN ?>"><i class="far fa-circle"></i> Sub Admin</a>
                    <ul class="treeview-menu">
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . SUB_ADMIN . '&user_status=' . ACTIVE; ?>"><i class="far fa-check"></i> Active</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . SUB_ADMIN . '&user_status=' . INACTIVE; ?>"><i class="far fa-times"></i> InActive</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/user/get_logged_in_users?filter=user_type&q=' . SUB_ADMIN; ?>"><i class="far fa-circle"></i> Logged In User</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if(check_user_previlege('p73')): ?>
                <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . Executive ?>"><i class="far fa-circle"></i>Executive</a>
                    <ul class="treeview-menu">
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . Executive . '&user_status=' . ACTIVE; ?>"><i class="far fa-check"></i> Active</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?filter=user_type&q=' . Executive . '&user_status=' . INACTIVE; ?>"><i class="far fa-times"></i> InActive</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/user/get_logged_in_users?filter=user_type&q=' . Executive; ?>"><i class="far fa-circle"></i> Logged In User</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if(check_user_previlege('p129')): ?>
                <li><a href="<?php echo base_url() . 'index.php/user/user_management'; ?>"><i class="far fa-circle"></i>All Users</a>
                    <ul class="treeview-menu">
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?user_status=' . ACTIVE; ?>"><i class="far fa-check"></i> Active</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/user/user_management?user_status=' . INACTIVE; ?>"><i class="far fa-times"></i> InActive</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if(check_user_previlege('p73')): ?>
                <li><a href="<?php echo base_url() . 'index.php/user/user_privilege?eid='?>"><i class="fa fa-address-card"></i>Set User Privilege</a>
                </li>
            <?php endif; ?>
            </ul>
        </li>
        <?php endif; 
         if ($any_domain_module) { if(check_user_previlege('p3')):?>
           <li class="treeview">
                <a href="#">
                    <i class="fas fa-shield"></i> 
                    <span>Cancellation Queues </span><span class="badge bg-red  pull-right" style="position: absolute;font-size: 11px;top: 18%;right: -1%" ><?php echo $this->session->userdata('cancel_queue_count'); ?></span><i class="far fa-angle-left pull-right"></i>
                </a>
                <?php if(check_user_previlege('p71')): ?>
                <ul class="treeview-menu">
                    <li><a href="<?php echo base_url() . 'index.php/report/cancellation_queue?created_datetime_from='.$date?>"><i class="far fa-circle"></i>Flight Cancellation<span class="badge bg-red  pull-right" style="position: absolute;font-size: 11px;top: 42%;" ><?php echo $this->session->userdata('cancel_queue_count'); ?></span></a></li>
                </ul>
            <?php endif; ?>
             </li>
            <?php endif; if(check_user_previlege('p4')): ?>
            <li class="treeview">
                <a href="#">
                    <i class="fas fa-chart-bar"></i> 
                    <span> Reports </span><i class="far fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <!-- USER TYPES -->
                    <?php if(check_user_previlege('p74')): ?>
                    <li><a href="#"><i class="far fa-circle"></i> B2C</a>
                        <ul class="treeview-menu">
                            <?php $today_search = date('Y-m-d'); ?>
                            <?php if ($airline_module) { if(check_user_previlege('p18')):?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_flight_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-plane"></i> Flight</a></li>
                            <?php endif; } ?>
                            <?php if ($accomodation_module) { if(check_user_previlege('p19')):?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_hotel_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-bed"></i> Hotel</a></li>
                            <?php endif; } ?>
                            <?php if ($bus_module) { if(check_user_previlege('p20')):?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_bus_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i> Bus</a></li>
                            <?php endif; } ?>

                            <?php if ($transferv1_module) { if(check_user_previlege('p21')): ?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_transfers_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i> Transfer</a></li>

                            <?php endif; }
                            ?>

                            <?php if ($sightseen_module) { if(check_user_previlege('p22')):?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_activities_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i> Activities</a></li>

                            <?php endif; } ?>
                            <?php if ($car_module) { if(check_user_previlege('p23')):?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2c_car_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i> Car</a></li>
                            <?php  endif; } ?>


                        </ul>
                    </li>
                <?php endif;  

                if(check_user_previlege('p75')):

                 ?>
                    <li><a href="#"><i class="far fa-circle"></i> Agent</a>
                        <ul class="treeview-menu">
                    <?php if ($airline_module) {  if(check_user_previlege('p25')):?> 
                                <li><a href="<?php echo base_url() . 'index.php/report/b2b_flight_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-plane"></i> Flight</a></li>
                            <?php endif; } ?>
                    <?php if ($accomodation_module) { if(check_user_previlege('p26')): ?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2b_hotel_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-bed"></i> Hotel</a></li>
                    <?php endif; } ?>
                    <?php if ($bus_module) { if(check_user_previlege('p27')): ?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2b_bus_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i> Bus</a></li>
                    <?php endif; } 
                    if ($transferv1_module) { if(check_user_previlege('p28')):?>
                        <li><a href="<?php echo base_url() . 'index.php/report/b2b_transfers_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i>Transfers</a></li>
                    <?php endif; } ?>
                    <?php if ($sightseen_module) { if(check_user_previlege('p29')): ?>
                        <li><a href="<?php echo base_url() . 'index.php/report/b2b_activities_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i> Activities</a></li>
                    <?php endif; } ?>
                            <?php if ($car_module) { if(check_user_previlege('p30')): ?>
                                <li><a href="<?php echo base_url() . 'index.php/report/b2b_car_report?today_booking_data='.$today_search.''; ?>"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i> Car</a></li>
                            <?php endif; } ?>
                        <li><a href="<?php echo base_url() . 'index.php/report/b2b_common_report?today_booking_data='.$today_search.''; ?>"><i class="fas fa-chart-bar"></i> Common Report</a></li>
                        <li><a href="<?php echo base_url() . 'index.php/report/b2b_booking_report'; ?>"><i class="fas fa-chart-bar"></i>Booking Report New</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                </ul>
                <?php if(check_user_previlege('p4')) { ?>
                <ul class="treeview-menu">
                    <!--  TYPES -->
                    <?php if(check_user_previlege('p118')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/transaction/logs' ?>">
                            <i class="far fa-shield"></i> 
                            <span> Transaction Logs </span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p121')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/transaction/gateway_logs' ?>">
                            <i class="far fa-shield"></i> 
                            <span> Gateway Transactions (Inward)</span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p122')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/transaction/gateway_logs?refund_list=1' ?>">
                            <i class="far fa-shield"></i> 
                            <span> Gateway Transactions (Refund)</span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p123')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/transaction/search_history' ?>">
                            <i class="far fa-search"></i> 
                            <span> Search History </span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p130')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/report/tds_gst_report' ?>">
                            <i class="far fa-shield"></i> 
                            <span> GST & TDS Reports</span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p124')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/transaction/top_destinations' ?>">
                            <i class="far fa-globe"></i> 
                            <span> Top Destinations</span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if(check_user_previlege('p125')) { ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/management/account_ledger' ?>">
                            <i class="fas fa-chart-bar "></i> 
                            <span> Account Ledger</span>
                        </a>
                    </li>
                    <li><a href="<?php echo base_url().'index.php/report/bookings_list/flight?created_datetime_from='.$date?>"><i class="far fa-cube"></i> <span>Supplier Wise Seats</span></a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php endif; 
            if(check_user_previlege('p5')):?>
            <li class="treeview">
                <a href="#">
                    <i class="far fa-money-bill"></i> <span>Account</span> <i class="far fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <?php if(check_user_previlege('p31')):?>
                <li><a href="<?php echo base_url() . 'private_management/credit_balance' ?>"><i class="far fa-circle"></i> Credit Balance</a></li>
                <?php endif; if(check_user_previlege('p32')):?>    
                <li><a href="<?php echo base_url() . 'private_management/debit_balance' ?>"><i class="far fa-circle"></i> Debit Balance</a></li>
                <?php endif; ?>
                </ul>
            </li>
            <?php endif;
            if ($b2b) { if(check_user_previlege('p6')): ?>
                <li class="treeview">
                    <a href="#">
                        <i class="far fa-briefcase"></i> <span>Commission</span> <i class="far fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <!-- <li><a href="<?php echo base_url() . 'index.php/management/group_commission'; ?>"><i class="far fa-circle"></i> Group Commission</a></li> -->
                        <!-- <li><a href="<?php echo base_url() . 'index.php/management/agent_commissions'; ?>"><i class="far fa-circle"></i> Agent Commission</a></li>
                         <li><a href="<?php echo base_url() . 'index.php/management/master_commission'; ?>"><i class="far fa-circle"></i> Master Commission</a></li> -->
                    <?php if(check_user_previlege('p135')): ?>
                        <!-- <li><a href="<?php echo base_url() . 'index.php/management/agent_commission?default_commission=' . ACTIVE; ?>"><i class="far fa-circle"></i> Default Commission</a></li> -->
                        <li><a href="<?php echo base_url() . 'index.php/management/group_commission'; ?>"><i class="far fa-circle"></i> Group Commission</a></li>
                    <?php endif; if(check_user_previlege('p34')): ?>
                        <!-- <li><a href="<?php echo base_url() . 'index.php/management/agent_commission' ?>"><i class="far fa-circle"></i> Agent's Commission</a></li> -->
                        <li><a href="<?php echo base_url() . 'index.php/management/agent_commissions'; ?>"><i class="far fa-circle"></i> Agent Commission</a></li>
					 <?php endif; if(check_user_previlege('p33')): ?>
                         <li><a href="<?php echo base_url() . 'index.php/management/master_commission'; ?>"><i class="far fa-circle"></i> Master Commission</a></li>
                    <?php endif; ?>
                    <!-- <li><a href="<?php echo base_url() . 'index.php/management/b2b_airline_wise_commission'; ?>"><i class="far fa-circle"></i> Airline Wise Commission</a></li>
                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_supplier_wise_commission'; ?>"><i class="far fa-circle"></i> Supplier Wise Commission</a></li> -->
                    </ul>
                </li>
        <?php endif; } if(check_user_previlege('p7')):?>
            <li class="treeview">
                <a href="#">
                    <i class="far fa-plus-square"></i> 
                    <span> Markup </span><i class="far fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <!-- Markup TYPES -->
        <?php 

         if ($b2c) { if(check_user_previlege('p35')): ?>

                        <li><a href="#"><i class="far fa-circle"></i> B2C</a>
                            <ul class="treeview-menu">
            <?php if ($airline_module) { if(check_user_previlege('p35')):?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_airline_markup/'; ?>"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i> Flight</a></li>
                                <?php endif; } ?>
                                <?php if ($accomodation_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_hotel_markup/'; ?>"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i> Hotel</a></li>
                                <?php endif; } ?>
                                <?php if ($bus_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_bus_markup/'; ?>"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i> Bus</a></li>
                                <?php endif; } ?>

                                <?php if ($transferv1_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_transfer_markup/'; ?>"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i> Transfers</a></li>

                                <?php endif; }
                                ?>


                                <?php if ($sightseen_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_sightseeing_markup/'; ?>"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i> Activities</a></li>

                                <?php endif; }
                                ?>
            <?php if ($car_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2c_car_markup/'; ?>"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i> Car</a></li>

                                <?php endif; }
                                ?>
                            </ul>
                        </li>
                        <?php endif; }
                            if ($b2b) { if(check_user_previlege('p36')):
                                ?>
                        <li><a href="#"><i class="far fa-circle"></i> B2B</a>
                            <ul class="treeview-menu">
                        <?php if ($airline_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_airline_markup/'; ?>"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i> Flight</a></li>
                            <?php endif; } ?>
                                <?php if ($accomodation_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_hotel_markup/'; ?>"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i> Hotel</a></li>
                                <?php endif; } ?>
                                <?php if ($bus_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_bus_markup/'; ?>"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i> Bus</a></li>
                                <?php endif; } ?>

                                <?php if ($transferv1_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_transfer_markup/'; ?>"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i>Transfers</a></li>
                                <?php endif; } ?>


                                <?php if ($sightseen_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_sightseeing_markup/'; ?>"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i> Activities</a></li>

                                <?php endif; }
                                ?>
                                <?php if ($car_module) { if(check_user_previlege('p35')): ?>
                                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_car_markup/'; ?>"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i> Car</a></li>

                                <?php endif; }
                                ?>
                            </ul>
                        </li>
        <?php endif; } ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() . 'index.php/management/b2b_markup_limit' ?>">
                            <i class="far fa-circle"></i> 
                            <span>Set Markup Limit</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <?php endif;
            } if(check_user_previlege('p8')): ?>
        <li class="treeview">
            <a href="<?php echo base_url() . 'index.php/management/gst_master' ?>">
                <i class="fa fa-globe"></i> 
                <span> GST Master </span>
            </a>
        </li>
		<?php endif;?>
		<?php if(check_user_previlege('p142')): ?>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-external-link"></i> <span>Offline Booking</span> <i class="far fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
				<?php if(check_user_previlege('p144')): ?>
                <li><a href="<?php echo base_url() . 'index.php/general/offline_flight_book'; ?>"><i class="far fa-circle"></i>Flight Create Booking</a></li>
				<?php endif;  if(check_user_previlege('p145')):?>
                <li><a href="<?php echo base_url() . 'index.php/report/offline_flight_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-circle"></i>Flight Booking Report</a></li>
				<?php endif;  if(check_user_previlege('p146')):?>

                <li><a href="<?php echo base_url() . 'index.php/general/offline_bus_book'; ?>"><i class="far fa-circle"></i>Bus Create Booking</a></li>
				<?php endif;  if(check_user_previlege('p147')):?>
                <li><a href="<?php echo base_url() . 'index.php/report/offline_bus_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-circle"></i>Bus Booking Report</a></li>
				<?php endif;  if(check_user_previlege('p148')):?>
                <li><a href="<?php echo base_url() . 'index.php/general/offline_hotel_book'; ?>"><i class="far fa-circle"></i>Hotel Create Booking</a></li>
				<?php endif;  if(check_user_previlege('p149')):?>
                <li><a href="<?php echo base_url() . 'index.php/report/offline_hotel_report?today_booking_data='.$today_search.''; ?>"><i class="far fa-circle"></i>Hotel Booking Report</a></li>
				<?php endif; ?>
            </ul>
        </li>
		<?php endif;?>
		<?php if(check_user_previlege('p9')): ?>
        <li class="treeview">
            <a href="<?php echo base_url() . 'index.php/management/bus_operator_cancellation' ?>">
                <i class="fa fa-users"></i> 
                <span>Bus Operator cancel Requests</span>
            </a>
        </li>
         <li class="treeview">
            <a href="<?php echo base_url() . 'index.php/general/group_booking' ?>">
                <i class="fa fa-users"></i> 
                <span>Group Booking Request</span><span class="badge bg-red  pull-right" style="position: absolute;font-size: 11px;top: 18%;right: -1%" ><?php echo $this->session->userdata('group_booking_count'); ?></span><i class="far fa-angle-left pull-right"></i>
            </a>
        </li>
		<?php endif; ?>
    <?php if ($b2b) { if(check_user_previlege('p9')):?>
            <li class="treeview">
                <a href="#">
                    <i class="far fa-money-bill"></i> 
                    <span> Master Balance Manager </span><span class="badge bg-red  pull-right" style="position: absolute;font-size: 11px;top: 18%;right: -1%" ><?php echo $this->session->userdata('notification_count'); ?></span><i class="far fa-angle-left pull-right"></i>
                </a>
                <?php if(check_user_previlege('p37')): ?>
                <ul class="treeview-menu">
                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_balance_manager' ?>"><i class="far fa-circle"></i> B2B<span class="badge bg-red  pull-right" style="position: absolute;font-size: 11px;top: 42%;" ><?php echo $this->session->userdata('notification_count'); ?></span></a></li>
                </ul>
                 <?php endif; if(check_user_previlege('p38')): ?>
                <ul class="treeview-menu">
                    <li><a href="<?php echo base_url() . 'index.php/management/b2b_credit_request' ?>"><i class="far fa-circle"></i> B2B Credit Limt Requests</a></li>
                </ul> 
            <?php endif; ?>
            <ul class="treeview-menu">
                    <li><a href="<?php echo base_url() . 'index.php/private_management/api_balances' ?>"><i class="far fa-circle"></i> API Balance Manager</a></li>
                </ul> 
            </li>

    <?php  endif; } 
    if ($package_module) { ?>
<?php if(check_user_previlege('p78')): ?>
    <?php if(true){ ?>
	<li class="treeview"><a href="#"> <i class="fa fa-plus-square"></i> <span>
                Master Content </span><i class="fa fa-angle-left pull-right"></i>
    </a>
        <ul class="treeview-menu"> 
			<li class=""><a href="<?php echo base_url().'index.php/tours/hotel_list';?>">
            <i class="far fa-circle"></i> Hotel List</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/supplier_list';?>">
            <i class="far fa-circle"></i> Supplier List</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/inclusions_list';?>">
            <i class="far fa-circle"></i>Inclusions</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/exclusions_list';?>">
            <i class="far fa-circle"></i>Exclusions</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/highlight_list';?>">
            <i class="far fa-circle"></i>Hightlights</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/cancellation_list';?>">
			<i class="far fa-circle"></i>Cancellation Policy</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/optional_tour_list';?>">
            <i class="far fa-circle"></i>Optional Tours</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/terms_conditions_list';?>">
            <i class="far fa-circle"></i>Terms and Conditions</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/trip_notes_list';?>">
            <i class="far fa-circle"></i>Trip Notes</a></li>
			<li class=""><a href="<?php echo base_url().'index.php/tours/visa_procedures_list';?>">
            <i class="far fa-circle"></i> Visa Procedures</a></li>
			    <li class=""><a href="<?php echo base_url().'index.php/tours/payment_policy_list'?>">
            <i class="far fa-circle"></i> Payment Policy </a></li>
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_type'?>">
            <i class="far fa-circle"></i> Category </a></li>

            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_region'?>">
            <i class="far fa-circle"></i> Region </a></li>
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_country'?>">
            <i class="far fa-circle"></i> Country </a></li>
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_city'?>">
            <i class="far fa-circle"></i> City </a></li>
                <li class=""><a href="<?php echo base_url().'index.php/tours/occupancy_managment'?>">
            <i class="far fa-circle"></i> Occupancy Management </a></li>

           
            

        </ul></li>
                <li class="treeview"><a href="#"> <i class="fa fa-plus-square"></i> <span>
                Holiday Manager </span><i class="fa fa-angle-left pull-right"></i>
    </a>
        <ul class="treeview-menu">          
            <!--
            <li class="<?php if($fun_name=="add_tour") echo "active";?>"><a href="<?php echo base_url().'index.php/tours/add_tour'?>">
            <i class="far fa-circle"></i> Add Tours </a></li>
            -->
			<li class=""><a href="<?php echo base_url().'index.php/tours/draft_list'?>">
            <i class="far fa-circle"></i> Draft Holiday List </a></li>   
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_list'?>">
            <i class="far fa-circle"></i> Holiday List </a></li>   
			<li class=""><a href="<?php echo base_url().'index.php/tours/verify_tour_list'?>">
            <i class="far fa-circle"></i>Verify Holiday List </a></li> 
			<li class=""><a href="<?php echo base_url().'index.php/tours/published_tour_list'?>">
            <i class="far fa-circle"></i>Published Holiday List </a></li> 
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_list_pending'?>">
            <i class="far fa-circle"></i>Unapproved Holiday List </a></li>
           <!-- <li class=""><a href="<?php echo base_url().'index.php/tours/tours_enquiry';?>">
            <i class="far fa-circle"></i> Inquiry</a></li>-->
            <li class=""><a href="<?php echo base_url().'index.php/tours/quotation_list';?>">
            <i class="far fa-circle"></i> Quotation List</a></li>
			
            <!-- <li class=""><a href="<?php echo base_url().'index.php/tours/tour_booking_request';?>">
            <i class="far fa-circle"></i> Booking Request</a></li> -->
            <!--<li class=""><a href="<?php echo base_url().'index.php/tours/tour_date_list'?>">
            <i class="far fa-circle"></i> Confirmed Departures </a></li>

            
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_destinations'?>">
            <i class="far fa-circle"></i> Main Destinations </a></li>
            
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_country'?>">
            <i class="far fa-circle"></i> Tour Country </a></li>

            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_city'?>">
            <i class="far fa-circle"></i> Tour City </a></li>
            -->

            <!-- <li class=""><a href="<?php echo base_url().'index.php/tours/tour_type'?>">
            <i class="far fa-circle"></i> Category </a></li> -->
            
<!-- 
            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_subtheme'?>">
            <i class="far fa-circle"></i> Theme </a></li> 

            <li class=""><a href="<?php echo base_url().'index.php/tours/tour_subtheme'?>">
            <i class="far fa-circle"></i> Activities </a></li>-->
           

            <!-- <li class=""><a href="<?php echo base_url().'index.php/tours/tour_activity'?>">
            <i class="far fa-circle"></i> Activity </a></li> -->

            <!--<li class=""><a href="<?php echo base_url().'index.php/tours/tour_inclusions'?>">
            <i class="far fa-circle"></i> Tour Inclusions </a></li>-->
            
            

        </ul></li>
        <?php } ?> 
    <?php endif; ?>
    <?php } 
	if ($package_module) { if(check_user_previlege('p10')):?>
	
			<li class="treeview">
				<a href="#"> <i class="fa fa-plus-square"></i> <span>
					Holiday Booking Enquiries </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2C</span><i class="fa fa-angle-left pull-right"></i></a>
						
						<ul class="treeview-menu">
							<li class=""><a href="<?php echo base_url().'index.php/tours/tour_bookings/B2C'?>">
							<i class="far fa-circle"></i> Confirmed Booking List </a></li>
							<li class=""><a href="<?php echo base_url().'index.php/tours/tours_enquiry/B2C'?>">
							<i class="far fa-circle"></i> Package Enquiry List </a></li>
							<li class=""><a href="<?php echo base_url().'index.php/tours/custom_enquiry_report'?>">
							<i class="far fa-circle"></i> Custom Enquiry List </a></li>
						</ul>
						
					</li>
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2B</span><i class="fa fa-angle-left pull-right"></i></a>
						
						<ul class="treeview-menu">
							<li class=""><a href="<?php echo base_url().'index.php/tours/tour_bookings/agent'?>">
							<i class="far fa-circle"></i> Confirmed Booking List </a></li>
							<li class=""><a href="<?php echo base_url().'index.php/tours/tours_enquiry/agent'?>">
							<i class="far fa-circle"></i> Package Enquiry List </a></li>
							<li class=""><a href="<?php echo base_url().'index.php/tours/custom_enquiry_report'?>">
							<i class="far fa-circle"></i> Custom Enquiry List </a></li>
						</ul>
						
					</li>
				</ul>
			</li>
         
    <?php endif; }
    if ($package_module) { ?>
<?php if(check_user_previlege('p131')): ?>
    <?php if(true){ ?>
			<li class="treeview">
				<a href="#"> <i class="fa fa-plus-square"></i> <span>
					Manage Booking Enquiries </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2C</span><i class="fa fa-angle-left pull-right"></i></a>
						
						<ul class="treeview-menu">
							 <?php if(check_user_previlege('p133')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_tours_bookings/B2C'?>">
								<i class="far fa-circle"></i>Confirmed Booking List </a></li>
							 <?php endif; ?>
							<?php if(check_user_previlege('p132')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_tours_enquiry/B2C'?>">
								<i class="far fa-circle"></i>Package Enquiry List </a></li>
							 <?php endif; ?>
							 <?php if(check_user_previlege('p134')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_custom_enquiries'?>">
								<i class="far fa-circle"></i> Custom Enquiry List </a></li>
							 <?php endif; ?>
						</ul>
					</li>
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2B</span><i class="fa fa-angle-left pull-right"></i></a>
						
						<ul class="treeview-menu">
							 <?php if(check_user_previlege('p133')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_tours_bookings/agent'?>">
								<i class="far fa-circle"></i>Confirmed Booking List </a></li>
							 <?php endif; ?>
							<?php if(check_user_previlege('p132')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_tours_enquiry/agent'?>">
								<i class="far fa-circle"></i>Package Enquiry List </a></li>
							 <?php endif; ?>
							 <?php if(check_user_previlege('p134')): ?>
								<li class=""><a href="<?php echo base_url().'index.php/tours/assigned_custom_enquiries'?>">
								<i class="far fa-circle"></i> Custom Enquiry List </a></li>
							 <?php endif; ?>
						</ul>
					</li>
				</ul>
			<li class="treeview">
				<a href="#"> <i class="fa fa-plus-square"></i> <span>
					Package Enquiry </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2C</span><i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
						<?php if(check_user_previlege('p132')): ?>
							<li class=""><a href="<?php echo base_url().'index.php/tours/confirmed_enq_list/B2C'?>">
							<i class="far fa-circle"></i>Confirmed Enquiry List </a></li>
						 <?php endif; ?>
						 <?php if(check_user_previlege('p133')): ?>
							<li class=""><a href="<?php echo base_url().'index.php/tours/cancelled_enq_list/B2C'?>">
							<i class="far fa-circle"></i> Cancelled List </a></li>
						 <?php endif; ?>
						
						</ul>
					</li>
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2B</span><i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
						<?php if(check_user_previlege('p132')): ?>
							<li class=""><a href="<?php echo base_url().'index.php/tours/confirmed_enq_list/agent'?>">
							<i class="far fa-circle"></i>Confirmed Enquiry List </a></li>
						 <?php endif; ?>
						 <?php if(check_user_previlege('p133')): ?>
							<li class=""><a href="<?php echo base_url().'index.php/tours/cancelled_enq_list/agent'?>">
							<i class="far fa-circle"></i> Cancelled List </a></li>
						 <?php endif; ?>
						
						</ul>
					</li>

				</ul>
			</li>
			<li class="treeview">
				<a href="#"> <i class="fa fa-plus-square"></i> <span>
					Custom Enquiry </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2C</span><i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
					<?php if(check_user_previlege('p132')): ?>
						<li class=""><a href="<?php echo base_url().'index.php/tours/confirmed_custom_enq_list'?>">
						<i class="far fa-circle"></i> Confirmed Enquiry List </a></li>
					 <?php endif; ?>
					 <?php if(check_user_previlege('p133')): ?>
						<li class=""><a href="<?php echo base_url().'index.php/tours/cancelled_custom_enq_list'?>">
						<i class="far fa-circle"></i> Cancelled List  </a></li>
					 <?php endif; ?>
					
					</ul>
				</ul>
				<ul class="treeview-menu">
					<li><a href="#"> <i class="fa fa-plus-square"></i> <span>B2B</span><i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
					<?php if(check_user_previlege('p132')): ?>
						<li class=""><a href="<?php echo base_url().'index.php/tours/confirmed_custom_enq_list'?>">
						<i class="far fa-circle"></i> Confirmed Enquiry List </a></li>
					 <?php endif; ?>
					 <?php if(check_user_previlege('p133')): ?>
						<li class=""><a href="<?php echo base_url().'index.php/tours/cancelled_custom_enq_list'?>">
						<i class="far fa-circle"></i> Cancelled List  </a></li>
					 <?php endif; ?>
					
					</ul>
				</ul>
			</li>
        <?php } ?>
    <?php endif; ?>
    <?php }  if(check_user_previlege('p11')):?>
        <li class="treeview">
            <a href="#">
                <i class="far fa-envelope"></i> 
                <span> Email Subscriptions </span><i class="far fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <!-- USER TYPES -->
                <li><a href="<?php echo base_url() . 'index.php/general/view_subscribed_emails' ?>"><i class="far fa-circle"></i> View Emails </a></li>
                <!-- <li><a href="<?php echo base_url() . 'index.php/supplier/add_with_price' ?>"><i class="far fa-circle"></i> Add New Package </a></li>
                <li><a href="<?php echo base_url() . 'index.php/supplier/view_with_price' ?>"><i class="far fa-circle"></i> View Packages </a></li>
                <li><a href="<?php echo base_url() . 'index.php/supplier/enquiries' ?>"><i class="far fa-circle"></i> View Packages Enquiries </a></li> -->
            </ul>
        </li>
<?php endif; } if(check_user_previlege('p13')): ?>
    <li class="treeview">
        <a href="#">
            <i class="far fa-laptop"></i>
            <span>CMS</span><i class="far fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        <?php if(check_user_previlege('p44')): ?>
        <li><a href="<?php echo base_url() . 'index.php/user/banner_images' ?>"><i class="far fa-image"></i> <span>Main Banner Image</span></a></li>
        <?php endif; if(check_user_previlege('p45')):?>
        <li><a href="<?php echo base_url() . 'index.php/cms/add_cms_page' ?>"><i class="far fa-file-alt"></i> <span>Static Page content</span></a></li>

            <!-- Top Destinations START -->
<?php endif; if ($airline_module) { if(check_user_previlege('p46')):?>
                <li class=""><a href="<?php echo base_url() . 'index.php/cms/flight_top_destinations' ?>"><i class="far fa-plane"></i> <span>Flight Top Destinations</span></a></li>
<?php endif; } ?>
<?php if ($accomodation_module) { if(check_user_previlege('p47')): ?>
                <li class=""><a href="<?php echo base_url() . 'index.php/cms/hotel_top_destinations' ?>"><i class="fas fa-bed"></i> <span>Hotel Top Destinations</span></a></li>
            <?php endif; } ?>
            <?php if ($bus_module) { if(check_user_previlege('p51')): ?>
                <li class=""><a href="<?php echo base_url() . 'index.php/cms/bus_top_destinations' ?>"><i class="far fa-bus"></i> <span>Bus Top Destinations</span></a></li>
            <?php endif; } if(check_user_previlege('p52')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/home_page_headings' ?>"><i class="far fa-book"></i> <span>Home Page Headings</span></a></li>
            <?php endif; if(check_user_previlege('p53')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/why_choose_us' ?>"><i class="far fa-question"></i> <span>Why Choose Us</span></a></li>
            <?php endif; if(check_user_previlege('p54')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/top_airlines' ?>"><i class="far fa-plane"></i> <span>Top Airlines</span></a></li>
            <?php endif; if(check_user_previlege('p55')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/tour_styles' ?>"><i class="far fa-binoculars"></i> <span>Tour Styles</span></a></li>
            <?php endif; if(check_user_previlege('p64')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/add_contact_address' ?>"><i class="far fa-address-card"></i> <span>Contact Address</span></a></li>
            <?php endif; ?>
            <?php if(check_user_previlege('p57')):?>
            <li class=""><a href="<?php echo base_url() . 'index.php/cms/terms_conditions' ?>"><i class="far fa-address-card"></i> <span>Voucher Terms & Conditions</span></a></li>
            <?php endif; ?>
            <!-- Top Destinations END -->
        </ul>
    </li>
<?php endif; if(check_user_previlege('p12')):?>
    <li class="treeview">
        <a href="<?php echo base_url() . 'index.php/cms/seo' ?>" >
            <i class="fa fa-university"></i>
            <span>SEO</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
    </li>
<?php endif; if(check_user_previlege('p15')):?>
    <li class="treeview">
        <a href="<?php echo base_url() . 'index.php/management/bank_account_details' ?>">
            <i class="far fa-university"></i> <span>Bank Account Details</span> </a>
    </li>
<?php endif; if(check_user_previlege('p15')):?>

<li class="treeview">
            <a href="<?php echo base_url().'index.php/general/email_configuration'?>">
            <i class="far fa-envelope"></i> <span>Email Configuration</span> </a>
    </li>
<?php endif; ?>
    <!-- 
    <li class="treeview">
                    <a href="<?php //echo base_url().'index.php/utilities/deal_sheets' ?>">
                            <i class="far fa-hand-o-right "></i> <span>Deal Sheets</span>
                    </a>
    </li>
    -->

    <?php
    if(check_user_previlege('p61')): ?>
    <li class="treeview">
        <a href="#"><i class="far fa-envelope"></i> Manage SMS</a>
        <ul class="treeview-menu">
        <li>
        <a href="<?php echo base_url().'index.php/utilities/sms_templates'?>"><i class="far fa-list"></i> <span>SMS Templates</span></a></li>
        <li></li>
        <li></li>
        </ul>
    </li>
    <?php endif;

    if(check_user_previlege('p126')) { ?>
    <li class="treeview">
            <a href="<?php echo base_url().'index.php/management/xml_logs'?>">
            <i class="far fa-shield"></i> <span>XML Logs</span> </a>
    </li>
    <?php } ?>
    <?php  if(check_user_previlege('p127')) {?>
    <li class="treeview">
            <a href="<?php echo base_url().'index.php/management/tds_certificates'?>">
            <i class="far fa-shield"></i> <span>TDS Certificates</span> </a>
    </li>
    <?php }?>
    <?php if(check_user_previlege('p16')): ?>
    <li class="treeview">
        <a href="#">
            <i class="far fa-cogs"></i> 
            <span> Settings </span><i class="far fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <?php if(check_user_previlege('p58')): ?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/insurance_fees' ?>"><i class="far fa-credit-card"></i>Travel Insurance</a>
            </li>
        <?php endif; if(check_user_previlege('p59')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/instant_recharge_convenience_fees' ?>"><i class="far fa-credit-card"></i>Convenience Fees</a>
            </li>
		<?php endif; if(check_user_previlege('p199')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/quota_counter_list' ?>"><i class="far fa-credit-card"></i>Quota Counter</a>
            </li>
        <?php endif; if(check_user_previlege('p60')):?>

            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/manage_promo_code' ?>"><i class="far fa-tag"></i>Promo Code</a>
            </li>
        <?php endif; if(check_user_previlege('p63')):?>

            <li class="">
                <a href="<?php echo base_url() . 'index.php/utilities/manage_source' ?>"><i class="far fa-database"></i> Manage API</a>
            </li>
            <li class="">
				<?php if(check_user_previlege('p200')):?>
                <a href="<?php echo base_url() . 'index.php/utilities/get_airport_details' ?>"><i class="far fa-database"></i>Manage Airport List</a>
				<?php endif; if(check_user_previlege('p201')):?>
                <a href="<?php echo base_url() . 'index.php/utilities/get_airline_details' ?>"><i class="far fa-database"></i>Manage Airline List</a>
				<?php endif; if(check_user_previlege('p202')):?>
                <a href="<?php echo base_url() . 'index.php/utilities/get_busstation_details' ?>"><i class="far fa-database"></i>Manage Bus Stations</a>
				<?php endif; if(check_user_previlege('p203')):?>
                <a href="<?php echo base_url() . 'index.php/utilities/get_hotel_city_details' ?>"><i class="far fa-database"></i>Manage Hotel Cities</a>
				<?php endif; ?>
            </li>
        <?php endif;
         if (is_domain_user() == false) { // ACCESS TO ONLY PROVAB ADMIN  ?>
                <li>
                    <a href="<?php echo base_url() . 'index.php/utilities/module' ?>"><i class="far fa-circle"></i> <span>Manage Modules</span>
                    </a>
                </li>
            <?php } if(check_user_previlege('p62')):?>

            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/currency_converter' ?>"><i class="fas fa-rupee-sign"></i> Currency Conversion </a>
            </li>
        <?php endif; if(check_user_previlege('p65')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/management/event_logs' ?>"><i class="far fa-shield"></i> <span> Event Logs </span></a>
            </li>
        <?php endif; if(check_user_previlege('p66')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/app_settings' ?>"><i class="far fa-laptop"></i> Appearance </a>
            </li>
        <?php endif; if(check_user_previlege('p67')): ?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/social_network' ?>"><i class="fab fa-facebook-square"></i> Social Networks </a>
            </li>

        <?php endif; if(check_user_previlege('p68')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/utilities/social_login' ?>"><i class="fab fa-facebook-f"></i> Social Login </a>
            </li>
<?php endif; if(check_user_previlege('p134')): ?>
			<li>
                <a href="<?php echo base_url() . 'index.php/private_management/supplier_credentials/flight' ?>">
                    <i class="far fa-image"></i> <span>Supplier Credentials</span>
                </a>
            </li>
<?php endif; if(check_user_previlege('p69')):?>
            <li>
                <a href="<?php echo base_url() . 'index.php/user/manage_domain' ?>">
                    <i class="far fa-image"></i> <span>Manage Domain</span>
                </a>
            </li>
<?php endif; if(check_user_previlege('p70')):?>
            <li>
                <a href="<?php echo base_url() ?>index.php/utilities/timeline"><i class="far fa-desktop"></i> <span>Live Events</span></a>
            </li>
<?php endif; ?>
            <!-- <li>
                    <a href="<?= base_url() . 'index.php/utilities/trip_calendar' ?>"><i class="far fa-calendar"></i> <span>Trip Calendar</span></a>
</li> -->           
        </ul>
    </li>
    <?php endif; ?>

    <?php  if(check_user_previlege('p120')) {?>
        <li class="treeview">
            <a href="<?php echo base_url().'index.php/utilities/agent_amount_collection'?>">
            <i class="far fa-shield"></i> <span>Agent amount collection</span> </a>
        </li>
    <?php } ?>
</ul>
