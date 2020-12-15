<?php
$page_datepicker = array();
if (isset($low_balance_alert)) {
    echo $low_balance_alert;
}
?>
<div class="bodyContent col-md-12">
    <div class="panel panel-primary clearfix">
        <!-- PANEL WRAP START -->
        <div class="panel-heading"><h4>Offline Flight Ticket Booking</h4>
            <!-- PANEL HEAD START -->
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <form method="POST" id="offline_booking" autocomplete="off"
                  action="<?php echo base_url() . 'index.php/general/offline_flight_book' ?>">
                <div class="clearfix form-group">
                    <div class="col-md-4">
                        <label>Select Agent</label> 
                        <select class="form-control select2" name="agent_id" id="agent_id" onchange="check_agent_balance()" required>
                            <option value="">All</option>
                            <?php
                                foreach ($agent_list as $key => $value) {
                            ?>
                            <option value="<?=$value['user_id'];?>" <?php if(isset($_GET['agent_id']) && $value['user_id'] == $_GET['agent_id']) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
                        <?php } ?>
						</select>
                    </div>
                    <div class="col-md-4 hide" id='agent_balance'>
                        <label>Agent Balance : </label>
                        <label id='blance_amount'>Agent Balance</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <label>Booking Type</label> 
                        <select class="form-control" name="booking_type" required>
                            <?= generate_options(get_enum_list('travel_type'), array('domestic')) ?>                
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Carrier Type Onward</label> 
                        <select class="form-control" name="is_lcc" required>
                            <?= generate_options(get_enum_list('flight_carrier'), array(@$is_lcc)) ?>   
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Carrier Type Return</label> 
                        <select class="form-control" name="is_lcc_return"  id="is_lcc_return" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                            <?= generate_options(get_enum_list('flight_carrier'), array(@$is_lcc)) ?>   
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Booking Status</label> 
                        <select class="form-control" name="status" required>
                            <?= generate_options(get_enum_list('booking_status_options'), array(@$status)) ?>   
                        </select>
                    </div> 
                    <div class="col-md-4">
                        <label>Payment Method</label> 
                        <select class="form-control" name="payment_method" required>
                            <?= generate_options(get_enum_list('payment_method'), array('cash')) ?>   
                        </select>
                    </div>
                    <?php
                    $admin_default_currency = admin_base_currency();
                    $store_cur_li = "";
                    foreach ($currency_list as $cur_key => $cur_val) 
                    {
                        $currency = explode(' ',$cur_val);
                     
                        if ($currency[0] != ''){
                            $cur_name = $currency[0];
                        }
                        if ($currency[1] != '' && strlen($currency[1]) > 3){
                            $cur_name  .= ' '.$currency[1];
                        }
                        if ($currency[2] != '' && strlen($currency[2]) > 3){
                            $cur_name  .= ' '.$currency[2];
                        }
                        if (isset($currency[3]) ==true && $currency[3] != '' && strlen($currency[3]) > 3){
                            $cur_name  .= ' '.$currency[3];
                        }

                        if ($admin_default_currency == $cur_key) {

                            $selected_currency = 'selected = selected';
                        } else {
                            $selected_currency = '';
                        }
                        
                        $store_cur_li .= '<option value="'.$cur_key.'" '.$selected_currency.'>'.$cur_name.' -  '.$cur_key.'</option>';

                    }
                    ?>
                    <div class="col-md-4">
                        <label>Currency</label> 
                        <select class="form-control" name="currency" required>
                            <?=$store_cur_li; ?>   
                        </select>
                    </div>                  
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-4">
                        <div class="radio">
                            <label>
                                <input type="radio" name="trip_type" value="oneway" checked>Oneway</label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="trip_type" value="circle" <?= @$trip_type == 'circle' ? 'checked' : ''; ?>>Round Trip</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Num Sectors Onward</label> 
                        <select class="form-control" id ="sect_num_onward" name="sect_num_onward" data-type="onward">
                            <?= generate_options(custom_numeric_dropdown(10, 1, 1), (array) @$sect_num_onward) ?>                           
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Num Sectors Return</label> 
                        <select class="form-control" id ="sect_num_return" name="sect_num_return" data-type="return" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                            <?= generate_options(custom_numeric_dropdown(10, 0, 1), (array) @$sect_num_return) ?>                           
                        </select>
                    </div>
                </div>

                <h4>Passenger Contact Info</h4>
                <div class="clearfix form-group">
                    <div class="col-md-4 ">
                        <label>Supplier ID</label>

                        <select class="form-control" id ="suplier_id" name="suplier_id" value="<?= @$suplier_id; ?>" required>
                            <option value="">Select</option>            
                            <?= generate_options($supliers_list, (array) @$suplier_id) ?>                   
                        </select> 
                    </div>   
                </div>
                <div class="clearfix form-group agent_details">
                    <div class="col-md-4">
                        <label>Title</label> 
                        <select class="form-control" name="passenger_title" required>
                            <?= generate_options(get_enum_list('title'), (array) @$passenger_title) ?>                                                      
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>First Name</label> 
                        <input type="text" class="form-control" name="passenger_first_name" placeholder="First Name" value="<?= @$passenger_first_name; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Last Name</label> 
                        <input type="text" class="form-control" name="passenger_last_name" placeholder="Last Name" value="<?= @$passenger_last_name; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Email</label> 
                        <input type="text" class="form-control" name="passenger_email" placeholder="Email" value="<?= @$passenger_email; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Mobile</label> 
                        <input type="text" class="form-control" name="passenger_phone" placeholder="Phone Num" value="<?= @$passenger_phone; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Address</label> 
                        <input type="text" class="form-control" name="passenger_address" placeholder="address" value="<?= @$passenger_address; ?>" required>
                    </div>
                </div>
                <div class="onward_flight_details">
                    <h4>Onward Flight Details</h4>
                    <div class="clearfix form-group">
                        <div class="col-md-2">
                            <label>Carrier/Booking Class</label>
                        </div>
                        <div class="col-md-2">
                            <label>Flight No.</label>
                        </div>                      
                        <div class="col-md-2">
                            <label>Dep. Airport/Arr. Airport</label>
                        </div>
                        <div class="col-md-2">
                            <label>Dep. Date/Arr. Date</label>
                        </div>
                        <div class="col-md-2">
                            <label>Dep. Time/Arr. Time</label> 
                        </div>
                        <div class="col-md-2">
                            <label>PNR</label> 
                        </div>
                    </div>
                    <div class="onward_flight_row" data-count="<?= @$sect_num_onward; ?>">
                        <?php
                        for ($i = 0; $i < @$sect_num_onward; $i++):
                            $page_datepicker[] = array('dep_date_onward_' . $i, FUTURE_DATE);
                            $page_datepicker[] = array('arr_date_onward_' . $i, FUTURE_DATE);

                            $page_datepicker[] = array('dep_time_onward_' . $i, TIMEPICKER_24H);
                            $page_datepicker[] = array('arr_time_onward_' . $i, TIMEPICKER_24H);
                            ?>
                            <div class="clearfix form-group">
                                <div class="col-md-2">
                                    <select class="form-control" name="career_onward[]" required>
                                    <?= generate_options($airline_list, (array) @$career_onward[$i]) ?>                               
                                </select>
                                    <input type="text" class="form-control" name="booking_class_onward[]" value="<?= @$booking_class_onward[$i]; ?>" placeholder="Booking Class" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="flight_num_onward[]" value="<?= @$flight_num_onward[$i]; ?>" placeholder="Flight No." required>                                
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control fromflight" name="dep_loc_onward[]" value="<?= @$dep_loc_onward[$i]; ?>" placeholder="Dep. Airport Code" maxlength="3" required>
                                    <input type="text" class="form-control departflight" name="arr_loc_onward[]" value="<?= @$arr_loc_onward[$i]; ?>" placeholder="Arr. Airport Code" maxlength="3" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="dep_date_onward_<?= $i; ?>" name="dep_date_onward[]" value="<?= app_friendly_absolute_date(@$dep_date_onward[$i]); ?>" placeholder="Dep. Date" required>
                                    <input type="text" class="form-control" id="arr_date_onward_<?= $i; ?>" name="arr_date_onward[]" value="<?= app_friendly_absolute_date(@$arr_date_onward[$i]); ?>" placeholder="Arr. Date" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="time" class="form-control" id="dep_time_onward_<?= $i; ?>" name="dep_time_onward[]" value="<?= @$dep_time_onward[$i]; ?>" placeholder="Dep. Time" required>
                                    <input type="time" class="form-control" id="arr_time_onward_<?= $i; ?>" name="arr_time_onward[]" value="<?= @$arr_time_onward[$i]; ?>" placeholder="Arr. Time" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="gds_pnr_onward[]" value="<?= @$gds_pnr_onward[$i]; ?>" placeholder="GDS PNR" <?= @$is_lcc != 'gds' ? 'style="display:none;"' : '' ?>>
                                    <input type="text" class="form-control" name="airline_pnr_onward[]" value="<?= @$airline_pnr_onward[$i]; ?>" placeholder="Arline PNR" required>
                                </div>
								<div class="col-md-2">
                                    <input type="text" class="form-control" name="cab_bagg_onward[]" placeholder="Cabbin Baggage" maxlength="5" required>
                                    <input type="text" class="form-control" name="checkin_bagg_onward[]" placeholder="Checkin Baggage" maxlength="5" required>
                                </div>	
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="return_flight_details" <?= $trip_type != 'circle' ? 'style="display:none;"' : '' ?>>
                    <h4>Return Flight Details</h4>
                    <div class="clearfix form-group">
                        <div class="col-md-2">
                            <label>Carrier/Booking Class</label>
                        </div>
                        <div class="col-md-2">
                            <label>Flight No.</label>
                        </div>                      
                        <div class="col-md-2">
                            <label>Dep. Airport/Arr. Airport</label>
                        </div>
                        <div class="col-md-2">
                            <label>Dep. Date/Arr. Date</label>
                        </div>
                        <div class="col-md-2">
                            <label>Dep. Time/Arr. Time</label> 
                        </div>
                        <div class="col-md-2">
                            <label>PNR</label> 
                        </div>                  
                    </div>
                    <div class="return_flight_row" data-count="<?= @$sect_num_return; ?>">
                        <?php
                        for ($i = 0; $i < @$sect_num_return; $i++):
                            $page_datepicker[] = array('dep_date_return_' . $i, FUTURE_DATE);
                            $page_datepicker[] = array('arr_date_return_' . $i, FUTURE_DATE);

                            $page_datepicker[] = array('dep_time_return_' . $i, TIMEPICKER_24H);
                            $page_datepicker[] = array('arr_time_return_' . $i, TIMEPICKER_24H);
                            ?>
                            <div class="clearfix form-group">
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="career_return[]" value="<?= @$career_return[$i]; ?>" placeholder="Carrier" required>
                                    <input type="text" class="form-control" name="booking_class_return[]" value="<?= @$booking_class_return[$i]; ?>" placeholder="Booking Class">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="flight_num_return[]" value="<?= @$flight_num_return[$i]; ?>" placeholder="Flight No." required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control fromflight" name="dep_loc_return[]" value="<?= @$dep_loc_return[$i]; ?>" placeholder="Dep. Airport" required>
                                    <input type="text" class="form-control departflight" name="arr_loc_return[]" value="<?= @$arr_loc_return[$i]; ?>" placeholder="Arr. Airport" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="dep_date_return_<?= $i; ?>" name="dep_date_return[]" value="<?= app_friendly_absolute_date(@$dep_date_return[$i]); ?>" placeholder="Dep. Date" required>
                                    <input type="text" class="form-control" id="arr_date_return_<?= $i; ?>" name="arr_date_return[]" value="<?= app_friendly_absolute_date(@$arr_date_return[$i]); ?>" placeholder="Arr. Date" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="time" class="form-control" id="dep_time_return_<?= $i; ?>" name="dep_time_return[]" value="<?= @$dep_time_return[$i]; ?>" placeholder="Dep. Time" required>
                                    <input type="time" class="form-control" id="arr_time_return_<?= $i; ?>" name="arr_time_return[]" value="<?= @$arr_time_return[$i]; ?>" placeholder="Arr. Time" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="gds_pnr_return[]" value="<?= @$gds_pnr_return[$i]; ?>" placeholder="GDS PNR" <?= @$is_lcc_rerun != 'gds' ? 'style="display:none;"' : '' ?>>
                                    <input type="text" class="form-control" name="airline_pnr_return[]" value="<?= @$airline_pnr_return[$i]; ?>" placeholder="Arline PNR" required>
                                </div>
								<div class="col-md-2">
                                    <input type="text" class="form-control" name="cab_bagg_return[]" placeholder="Cabbin Baggage" maxlength="5" required>
                                    <input type="text" class="form-control" name="checkin_bagg_return[]" placeholder="Checkin Baggage" maxlength="5" required>
                                </div>
                            </div>
                        <?php endfor; ?>                    
                    </div>
                </div>
                <h4>Select Passenger Details</h4>
                <div class="clearfix form-group">
                    <div class="col-md-4">
                        <label>Adults</label> 
                        <select class="form-control" id="adult_count" name="adult_count" data-type="adult" required>
                            <?= generate_options(custom_numeric_dropdown(100, 1, 1), (array) @$adult_count) ?>                          
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Childrens</label>
                        <select class="form-control" id="child_count" name="child_count" data-type="child">
                            <?= generate_options(custom_numeric_dropdown(100, 0, 1), (array) @$child_count) ?>                          
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Infants</label> 
                        <select class="form-control" id="infant_count" name="infant_count" data-type="infant">
                            <?= generate_options(custom_numeric_dropdown(100, 0, 1), (array) @$infant_count) ?>                         
                        </select>
                    </div>
                </div>
                <div class="adult_info">
                    <h4>Adult Info</h4>
                    <div class="clearfix form-group">
                        <div class="col-md-1">
                            <label>Title</label>                            
                        </div>
                        <div class="col-md-2">
                            <label>First Name <button type="button" class="btn btn-primary btn-xs fill-first-name">Fill</button></label>                            
                        </div>
                        <div class="col-md-2">
                            <label>Last Name <button type="button" class="btn btn-primary btn-xs fill-last-name">Fill</button></label>                          
                        </div>
                        <div class="col-md-2">
                            <label>Freq. Flyer No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>Pass Port No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>PPExpiry</label>
                        </div>
                        <div class="col-md-1">
                            <label>Ticket Number</label> 
                        </div>
                    </div>
                    <div class="adult_row" data-count="<?= @$adult_count ?>">
                        <?php
                        for ($i = 0; $i < @$adult_count; $i++):
                            $page_datepicker[] = array('adult_pax_pp_expiry_' . $i, FUTURE_DATE);
                            ?>
                            <div class="clearfix form-group">
                                <div class="col-md-1">
                                    <input type="hidden" name="pax_type[]" value="Adult">
                                    <select class="form-control title_nme" name="pax_title[]" required>
                                        <?= generate_options(get_enum_list('title'), (array) @$pax_title[$i]) ?>                                                        
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_first_name[]" value="<?= @$pax_first_name[$i]; ?>" placeholder="First Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_last_name[]" value="<?= @$pax_last_name[$i]; ?>" placeholder="Last Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_ff_num[]" value="<?= @$pax_ff_num[$i]; ?>" placeholder="Freq. Flyer No.">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_passport_num[]" value="<?= @$pax_passport_num[$i]; ?>" placeholder="Pass Port No." maxlength="10">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="adult_pax_pp_expiry_<?= $i; ?>" name="pax_pp_expiry[]" value="<?= @$pax_pp_expiry[$i]; ?>" placeholder="PPExpiry">
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control" name="pax_ticket_num_onward[]" value="<?= @$pax_ticket_num_onward[$i]; ?>" placeholder="Onward" <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                    <input type="text" class="form-control" name="pax_ticket_num_return[]" value="<?= @$pax_ticket_num_return[$i]; ?>" placeholder="Return" <?= @$trip_type != 'circle' ? 'style="display:none" disabled' : '' ?> <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="child_info" <?= @$child_count <= 0 ? 'style="display:none;"' : '' ?>>
                    <h4>Child Info</h4>
                    <div class="clearfix form-group">
                        <div class="col-md-1">
                            <label>Title</label>                            
                        </div>
                        <div class="col-md-2">
                            <label>First Name</label>                           
                        </div>
                        <div class="col-md-2">
                            <label>Last Name</label>                            
                        </div>
                        <div class="col-md-2">
                            <label>Freq. Flyer No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>Pass Port No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>PPExpiry</label>
                        </div>
                        <div class="col-md-1">
                            <label>Ticket Number</label> 
                        </div>
                    </div>  
                    <div class="child_row" data-count="<?= @$child_count ?>">
                        <?php
                        for ($i = $adult_count; $i < @$adult_count + @$child_count; $i++):
                            $page_datepicker[] = array('child_pax_pp_expiry_' . ($i - $adult_count), FUTURE_DATE);
                            ?>
                            <div class="clearfix form-group">
                                <div class="col-md-1">
                                    <input type="hidden" name="pax_type[]" value="Child">
                                    <select class="form-control" name="pax_title[]">
                                        <?= generate_options(get_enum_list('title'), (array) @$pax_title[$i]) ?>                                                        
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_first_name[]" value="<?= @$pax_first_name[$i]; ?>" placeholder="First Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_last_name[]" value="<?= @$pax_last_name[$i]; ?>" placeholder="Last Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_ff_num[]" value="<?= @$pax_ff_num[$i]; ?>" placeholder="Freq. Flyer No.">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_passport_num[]" value="<?= @$pax_passport_num[$i]; ?>" placeholder="Pass Port No.">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="child_pax_pp_expiry_<?= ($i - $adult_count); ?>" name="pax_pp_expiry[]" value="<?= @$pax_pp_expiry[$i]; ?>" placeholder="PPExpiry">
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control" name="pax_ticket_num_onward[]" value="<?= @$pax_ticket_num_onward[$i]; ?>" placeholder="Onward" <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                    <input type="text" class="form-control" name="pax_ticket_num_return[]" value="<?= @$pax_ticket_num_return[$i]; ?>" placeholder="Return" <?= @$trip_type != 'circle' ? 'style="display:none" disabled' : '' ?> <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>              
                </div>
                <div class="infant_info" <?= @$infant_count <= 0 ? 'style="display:none;"' : '' ?>>
                    <h4>Infant Info</h4>
                    <div class="clearfix form-group">
                        <div class="col-md-1">
                            <label>Title</label>                            
                        </div>
                        <div class="col-md-2">
                            <label>First Name</label>                           
                        </div>
                        <div class="col-md-2">
                            <label>Last Name</label>                            
                        </div>
                        <div class="col-md-2">
                            <label>Freq. Flyer No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>Pass Port No.</label>
                        </div>
                        <div class="col-md-2">
                            <label>PPExpiry</label>
                        </div>
                        <div class="col-md-1">
                            <label>Ticket Number</label> 
                        </div>
                    </div>
                    <div class="infant_row" data-count="<?= @$infant_count ?>">
                        <?php
                        for ($i = @$adult_count + @$child_count; $i < @$adult_count + @$child_count + @$infant_count; $i++):
                            $page_datepicker[] = array('infant_pax_pp_expiry_' . ($i - @$adult_count + @$child_count), FUTURE_DATE);
                            ?>
                            <div class="clearfix form-group">
                                <div class="col-md-1">
                                    <input type="hidden" name="pax_type[]" value="Infant">
                                    <select class="form-control" name="pax_title[]">
                                        <?= generate_options(get_enum_list('title'), (array) @$pax_title[$i]) ?>                                                        
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_first_name[]" value="<?= @$pax_first_name[$i]; ?>" placeholder="First Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_last_name[]" value="<?= @$pax_last_name[$i]; ?>" placeholder="Last Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_ff_num[]" value="<?= @$pax_ff_num[$i]; ?>" placeholder="Freq. Flyer No.">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="pax_passport_num[]" value="<?= @$pax_ticket_num[$i]; ?>" placeholder="Pass Port No.">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="child_pax_pp_expiry_<?= ($i - @$adult_count + @$child_count); ?>" name="pax_pp_expiry[]" value="<?= @$pax_pp_expiry[$i]; ?>" placeholder="PPExpiry">
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control" name="pax_ticket_num_onward[]" value="<?= @$pax_ticket_num_onward[$i]; ?>" placeholder="Onward" <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                    <input type="text" class="form-control" name="pax_ticket_num_return[]" value="<?= @$pax_ticket_num_return[$i]; ?>" placeholder="Return" <?= @$trip_type != 'circle' ? 'style="display:none" disabled' : '' ?> <?= @$is_lcc == 'gds' ? 'required' : '' ?>>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <h4>Passenger Fare Breakup</h4>
                <div class="clearfix form-group">                   
                    <div class="col-md-2">
                        <label>Type</label>                         
                    </div>
                    <div class="col-md-2">
                        <label>TripType</label>                         
                    </div>
                    <div class="col-md-2">
                        <label>Basic Fare</label>                           
                    </div>
                    <div class="col-md-1">
                        <label>YQ</label>                           
                    </div>
                    <div class="col-md-2">
                        <label>Other TAX</label>
                    </div>                  
                    <div class="col-md-1">
                        <label>Count</label>
                    </div>
                    <div class="col-md-2">
                        <label>Total</label> 
                    </div>
                </div>
                <div class="clearfix form-group pax_fare_adult">
                    <?php $c = 0; ?>
                    <div class="pax-fare-row">
                        <div class="col-md-2"><label>Adult</label>
                        </div>
                        <div class="col-md-2"><label>Onward</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_onward[]" value="<?= intval(@$pax_basic_fare_onward[$c]) > 0 ? $pax_basic_fare_onward[$c] : ''; ?>" min= "1" placeholder="Basic" required>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_onward[]" value="<?= intval(@$pax_yq_onward[$c]); ?>" placeholder="YQ">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_onward[]" value="<?= intval(@$pax_other_tax_onward[$c]) > 0 ? $pax_other_tax_onward[$c] : ''; ?>" min = "1" placeholder="Other TAX" required>
                        </div>  
                        <div class="col-md-1"><input type="text" name="pax_type_count_onward[]" value="<?= intval(@$pax_type_count_onward[$c]); ?>"  class="form-control" readonly></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_onward[]" value="<?= intval(@$pax_total_fare_onward[$c]) > 0 ? $pax_total_fare_onward[$c] : ''; ?>" min= "1" placeholder="Total" required>
                        </div>
                    </div>
                    <div class="pax-fare-row trip_circle" <?= @$trip_type != 'circle' ? 'style="display:none;"' : '' ?>>                    
                        <div class="col-md-2"></div>                            
                        <div class="col-md-2"><label>Return</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_return[]" value="<?= intval(@$pax_basic_fare_return[$c]); ?>" placeholder="Basic" <?= @$trip_type != 'circle' ? 'disabled' : '' ?> required>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_return[]" value="<?= intval(@$pax_yq_return[$c]); ?>" placeholder="YQ" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_return[]" value="<?= intval(@$pax_other_tax_return[$c]); ?>" placeholder="Other TAX" <?= @$trip_type != 'circle' ? 'disabled' : '' ?> required>
                        </div>  
                        <div class="col-md-1"><input type="text" name="pax_type_count_return[]" value="<?= intval(@$pax_type_count_return[$c]); ?>" class="form-control" readonly <?= @$trip_type != 'circle' ? 'disabled' : '' ?>></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_return[]" value="<?= intval(@$pax_total_fare_return[$c]); ?>" placeholder="Total" <?= @$trip_type != 'circle' ? 'disabled' : '' ?> required>
                        </div>
                    </div>
                </div>
                <div class="clearfix form-group pax_fare_child" <?= @$child_count <= 0 ? 'style="display:none;"' : '' ?>>
                    <?php $c++; ?>
                    <div class="pax-fare-row">
                        <div class="col-md-2"><label>Child</label>
                        </div>
                        <div class="col-md-2"><label>Onward</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_onward[]" value="<?= intval(@$pax_basic_fare_onward[$c]); ?>" placeholder="Basic">
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_onward[]" value="<?= intval(@$pax_yq_onward[$c]); ?>" placeholder="YQ">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_onward[]" value="<?= intval(@$pax_other_tax_onward[$c]); ?>" placeholder="Other TAX">
                        </div>  
                        <div class="col-md-1"><input type="text" name="pax_type_count_onward[]" value="<?= intval(@$pax_type_count_onward[$c]); ?>" class="form-control" readonly></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_onward[]" value="<?= intval(@$pax_total_fare_onward[$c]); ?>" placeholder="Total">
                        </div>
                    </div>
                    <div class="pax-fare-row trip_circle" <?= @$trip_type != 'circle' ? 'style="display:none;"' : '' ?>>
                        <div class="col-md-2"></div>                            
                        <div class="col-md-2"><label>Return</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_return[]" value="<?= intval(@$pax_basic_fare_return[$c]); ?>" placeholder="Basic" <?= @$trip_type != 'circle' ? 'disabled' : '' ?> required>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_return[]" value="<?= intval(@$pax_yq_return[$c]); ?>" placeholder="YQ" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_return[]" value="<?= intval(@$pax_other_tax_return[$c]); ?>" placeholder="Other TAX" <?= @$trip_type != 'circle' ? 'disabled' : '' ?> required>
                        </div>
                        <div class="col-md-1"><input type="text" name="pax_type_count_return[]" value="<?= intval(@$pax_type_count_return[$c]); ?>" class="form-control" readonly <?= @$trip_type != 'circle' ? 'disabled' : '' ?>></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_return[]" value="<?= intval(@$pax_total_fare_return[$c]); ?>" placeholder="Total" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                    </div>                  
                </div>
                <div class="clearfix form-group pax_fare_infant" <?= @$infant_count <= 0 ? 'style="display:none;"' : '' ?>>
                    <?php $c++; ?>
                    <div class="pax-fare-row">
                        <div class="col-md-2"><label>Infant</label>
                        </div>
                        <div class="col-md-2"><label>Onward</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_onward[]" value="<?= intval(@$pax_basic_fare_onward[$c]); ?>" placeholder="Basic" >
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_onward[]" value="<?= intval(@$pax_yq_onward[$c]); ?>" placeholder="YQ">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_onward[]" value="<?= intval(@$pax_other_tax_onward[$c]); ?>" placeholder="Other TAX">
                        </div>                      
                        <div class="col-md-1"><input type="text" name="pax_type_count_onward[]" value="<?= intval(@$pax_type_count_onward[$c]); ?>" class="form-control" readonly></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_onward[]" value="<?= intval(@$pax_total_fare_onward[$c]); ?>" placeholder="Total">
                        </div>
                    </div>
                    <div class="pax-fare-row trip_circle" <?= @$trip_type != 'circle' ? 'style="display:none;"' : '' ?>>                            
                        <div class="col-md-2"></div>                            
                        <div class="col-md-2"><label>Return</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_basic_fare_return[]" value="<?= intval(@$pax_basic_fare_return[$c]); ?>" placeholder="Basic" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control" name="pax_yq_return[]" value="<?= intval(@$pax_yq_return[$c]); ?>" placeholder="YQ" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_other_tax_return[]" value="<?= intval(@$pax_other_tax_return[$c]); ?>" placeholder="Other TAX">
                        </div>
                        <div class="col-md-1"><input type="text" name="pax_type_count_return[]" value="<?= intval(@$pax_type_count_return[$c]); ?>" class="form-control" readonly <?= @$trip_type != 'circle' ? 'disabled' : '' ?>></div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="pax_total_fare_return[]" value="<?= intval(@$pax_total_fare_return[$c]); ?>" placeholder="Total" <?= @$trip_type != 'circle' ? 'disabled' : '' ?>>
                        </div>
                    </div>                  
                </div>
				<div class="col-xs-12" style="margin-bottom: 20px;">
				<h4>GST Details</h4>
				   <div class="row" style="margin-bottom: 20px;">
					  <div class="col-xs-3">
						 <input type="text" class="form-control" id="gst_number" name="gst_number" placeholder="GST Number" value="">    
					  </div>
					  <div class="col-xs-3">
						 <input type="text" class="form-control" id="gst_company_name" name="gst_company_name" placeholder="Company Name" vaule="">    
					  </div>
					  <div class="col-xs-3">
						 <input type="email" class="form-control" id="gst_email" name="gst_email" placeholder="GST Email" value="">    
					  </div>
					  <div class="col-xs-3">
						 <input type="text" class="form-control" id="gst_phone" name="gst_phone" maxlength="10" placeholder="GST Phone" value="">    
					  </div>
					</div>  
					<div class="row">  
					  <div class="col-xs-3">
						 <input type="text" class="form-control" name="gst_address" id="gst_address" placeholder="GST Address" value="">    
					  </div>
					  <div class="col-xs-3">
					  <?php $state_list = generate_options($state_list); ?>
						<select name="gst_state" class="form-control" id="gststate">
							<option value="INVALIDIP">Select State</option>
							<?=$state_list?>
						</select>
					  </div>
					 </div>
				</div>
                <h4>Commission & Markup </h4>
                <div class="clearfix form-group hide">
                    <div class="col-xs-12">
                        <button type="button" class="btn btn-primary btn-xs comm-auto-fill">Fill Existing Commission</button>                   
                    </div>
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-3">
                        <label>Suplier Commission(+)</label> 
                        <input type="text" class="form-control" name="admin_comm_perc" value="0" placeholder="Commission">
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" name="share_commission">
                        <label>Agent Commission(+)</label> 
                        <input type="text" class="form-control" name="basic_comm" value="0" placeholder="Commission" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Pace Commission</label> 
                        <input type="text" class="form-control" name="pace_commission" value="0" placeholder="Commission" readonly>
                    </div>
                     <div class="col-md-3">
                        <label>Admin Markup(+)</label> 
                        <input type="text" class="form-control" name="add_admin_markup"  value="<?= intval(@$admin_markup); ?>" placeholder="Admin Markup">
                    </div>
                    <!-- <div class="col-md-4">
                        <label>YQ(%)</label> 
                        <select class="form-control" name="yq_comm">
                            <option value="0" selected="selected">0</option><option value="0.5">0.5</option><option value="1">1</option><option value="1.5">1.5</option><option value="2">2</option><option value="2.5">2.5</option><option value="3">3</option><option value="3.5">3.5</option><option value="4">4</option><option value="4.5">4.5</option><option value="5">5</option><option value="5.5">5.5</option><option value="6">6</option><option value="6.5">6.5</option><option value="7">7</option><option value="7.5">7.5</option><option value="8">8</option><option value="8.5">8.5</option><option value="9">9</option><option value="9.5">9.5</option><option value="10">10</option>
                        </select>                       
                    </div>
                    <div class="col-md-4">
                        <label>Handling Charge(+)</label> 
                        <input type="text" class="form-control" name="hc_comm" value="<?= intval(@$hc_comm); ?>" placeholder="Handling Charge">
                    </div> -->                  
                </div>
                <div class="clearfix form-group">
                                    
                    <!-- <div class="col-md-4">
                        <label>Agent Markup(+)</label> 
                        <input type="text" class="form-control" name="agent_markup" value="<?= intval(@$agent_markup); ?>" placeholder="Agent Markup">
                    </div> -->                  
                </div>
                <h4>Grand Total</h4>
                <div class="clearfix form-group">
                    <div class="col-xs-12">
                        <button type="button" class="btn btn-primary btn-xs tot-calc">Calculate</button>                    
                    </div>
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-4">
                        <label>Total Basefare</label> 
                        <input type="text" class="form-control" name="api_total_basic_fare"  value="<?= intval(@$api_total_basic_fare); ?>" placeholder="Basefare" required>
                    </div>
                    <div class="col-md-4">
                        <label>Other Taxes & Charges</label> 
                        <input type="text" class="form-control" name="api_total_tax" value="<?= intval(@$api_total_tax); ?>" placeholder="Other Taxes & Charges" required>
                    </div>
                    <!-- <div class="col-md-4">
                        <label>YQ</label> 
                        <input type="text" class="form-control" name="api_total_yq" value="<?= intval(@$other_fare); ?>" placeholder="YQ">
                    </div> -->
                    <div class="col-md-4">
                        <label>GST</label> 
                        <input type="text" class="form-control" name="service_tax" value="0" placeholder="Service Tax" required>
                    </div>
                    <div class="col-md-4">
                        <label>Commission</label> 
                        <input type="text" class="form-control" name="agt_comm" value="0" readonly>
                    </div> 
                    <div class="col-md-4">
                        <label>TDS</label> 
                        <input type="text" class="form-control" name="tds" value="<?= intval(@$tds); ?>" placeholder="TDS" required>
                    </div> 
                     <div class="col-md-4">
                        <label>Admin Markup</label> 
                        <input type="text" class="form-control" name="admin_markup" value="<?= intval(@$tds); ?>" placeholder="Admin Markup" required>
                    </div>                   
                </div>
                <div class="clearfix form-group">
                    <div class="col-md-4">
                        <label>Total Fare</label>
                        <input type="text" class="form-control" id="agent_buying_price" name="agent_buying_price" max="0" value="<?= intval(@$agent_buying_price); ?>" readonly>
                    </div>
                   <!--  <div class="col-md-4">
                        <label>Total Fare</label>
                        <input type="text" class="form-control" name="api_total_selling_price" value="<?= intval(@$api_total_selling_price); ?>" placeholder="Total Fare">
                    </div> -->
                </div>
                <div class="clearfix form-group">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php
$this->current_page->set_datepicker($page_datepicker);
?>
<script>
    $(document).ready(function () {
    $('.select2').select2();  
    $('input[name="share_commission"]').on('change', function () {
        if($(this).prop("checked")){
            $("input[name='basic_comm']").removeAttr("readonly");
        }
        else
        {
             $("input[name='basic_comm']").attr("readonly", "readonly");
             $("input[name='basic_comm']").val("0");
        }
    });     
            $.widget("custom.catcomplete", $.ui.autocomplete, {
    _create: function() { this._super(), this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)") },
    _renderMenu: function(t, e) {
        var r = this,
            a = "";
        $.each(e, function(e, o) {
            var n;
           o.category != a && (t.append("<li class='ui-autocomplete-category'>" + o.category + "</li>"), a = o.category), n = r._renderItemData(t, o), o.category && n.attr("aria-label", o.category + " : " + o.label)
        })
    }
});

 $(document).on("focus", ".fromflight, .departflight", function () {
    var cache = {};
     $(this).catcomplete({
            open: function(event, ui) {
            $('.ui-autocomplete').off('menufocus hover mouseover');
        },
            source: function(request, response) {
                var term = request.term;
                if (term in cache) {
                    response(cache[term]);
                    return
                } else {
                    $.getJSON(app_base_url + "index.php/flight/get_airport_code_list", request, function(data, status, xhr) {
                        if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
                            data = cache[""]
                        } else {
                            cache[term] = data;
                            response(cache[term])
                        }
                    })
                }
            },
            minLength: 0,
            autoFocus: false,
            select: function(event, ui) {
                var label = ui.item.label;
                var category = ui.item.category;
                //$(this).siblings('.loc_id_holder').val(ui.item.id);
                auto_focus_input(this.id)
                $(this).val(ui.item.id);
            },
            change: function(ev, ui) {
                if (!ui.item) {
                    $(this).val("")
                }
            }
        }).bind('focus', function() {
            $(this).catcomplete("search")
        }).catcomplete("instance")._renderItem = function(ul, item) {
            var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
            var top = 'Top Searches';
            return $("<li class='custom-auto-complete'>").append('<a><img class="flag_image" src="' + '">' + auto_suggest_value + '</a>').appendTo(ul)
        };
		
});
        $('[name="trip_type"]').on('change', function () {
            if ($(this).val() === 'circle') {
                $('#sect_num_return').prop("disabled", false);
                $('#is_lcc_return').prop("disabled", false);
                $('.trip_circle').show().find('[name]').prop("disabled", false);
                $('.return_flight_details').show().find('[name]').prop("disabled", false);
                $('[name="pax_ticket_num_return[]"]').show().prop("disabled", false);
                //update number of sectors to 1
                show_default_return_segments();
            } else {
                $('#sect_num_return').prop("disabled", true);
                $('#is_lcc_return').prop("disabled", true);
                $('.trip_circle').hide().find('[name]').prop("disabled", true);
                $('.return_flight_details').hide().find('[name]').prop("disabled", true);
                $('[name="pax_ticket_num_return[]"]').hide().prop("disabled", true);
            }
        });


        /**
         *
         */
        function show_default_return_segments()
        {
            var return_seg = $('#sect_num_return');
            var default_segs = parseInt(return_seg.val());
            if (default_segs < 1) {
                var count = 1;
                var type = return_seg.data('type');
                return_seg.val(count);
                return_seg.trigger('change');
                //get_flight_row(type,count);
            }
        }
        $('[name="is_lcc"]').on('change', function () {
            if ($(this).val() === 'gds') {
                $('[name="gds_pnr_onward[]"]').show().prop("disabled", false);
                $('[name="pax_ticket_num_onward[]"]').prop("required", true);
            } else {
                $('[name="gds_pnr_onward[]"]').hide().prop("disabled", true);
                $('[name="pax_ticket_num_onward[]"],[name="pax_ticket_num_return[]"]').prop("required", false);
            }
        });
         $('[name="is_lcc_return"]').on('change', function () {

            if ($(this).val() === 'gds') {
                $('[name="gds_pnr_return[]"]').show().prop("disabled", false);
                $('[name="pax_ticket_num_return[]"]').prop("required", true);
            } else {
                $('[name="gds_pnr_return[]"]').hide().prop("disabled", true);
                $('[name="pax_ticket_num_return[]"]').prop("required", false);
            }
        });
        $('#sect_num_onward,#sect_num_return').on('change', function () {
            var type = $(this).data('type');
            var count = parseInt($(this).val());
            
            if (count > 0) {
                $('.' + type + '_flight_details').show();
            } else {
                $('.' + type + '_flight_details').hide();
            }
            //alert(type+count)
            get_flight_row(type, count);
        });
        $('#child_count,#adult_count,#infant_count').on('change', function () {
            var type = $(this).data('type');
            var count = parseInt($(this).val());
            if (count > 0) {
                $('.' + type + '_info').show();
                $('.pax_fare_' + type).show().find('[name="pax_type_count_onward[]"],[name="pax_type_count_return[]"]').val(count);
            } else {
                $('.' + type + '_info').hide();
                $('.pax_fare_' + type).hide().find('[name="pax_type_count_onward[]"],[name="pax_type_count_return[]"]').val(0);
            }
            get_pax_row(type, count);
        });
        $('.pax-fare-row').on('change', '[name^="pax_basic_fare_"],[name^="pax_yq_"],[name^="pax_other_tax_"],[name="pax_type_count_"]', function () {
            var $cont = $(this).closest('.pax-fare-row');
            var tot = 0;
            var basic = $cont.find('[name^="pax_basic_fare_"]').val();
            var yq = $cont.find('[name^="pax_yq_"]').val();
            var tax = $cont.find('[name^="pax_other_tax_"]').val();
            var count = $cont.find('[name^="pax_type_count_"]').val();
            tot = ((basic ? parseInt(basic) : 0) + (yq ? parseInt(yq) : 0) + (tax ? parseInt(tax) : 0)) * parseInt(count);
            $cont.find('[name^="pax_total_fare_"]').val(tot);
        });
        $('.tot-calc').on('click', function () {
            $.post(app_base_url + "index.php/general/offline_fare_calculate/", $('#offline_booking').serialize(), function (data, status, xhr) {
                data = $.parseJSON(data);
                var pace_commission = parseInt(data.admin_commission)-parseInt(data.commission);
                $('input[name="pace_commission"]').val(pace_commission);
                $('input[name="agt_comm"]').val(parseInt(data.commission));
                $.each(data, function (key, value) {
                    $('[name="' + key + '"]').val(value);
                });
                //alert($("#agent_buying_price").attr('max'));
                if (parseInt($("#agent_buying_price").attr('max')) < parseInt(data.agent_buying_price)){
                   // alert('Insufficient balance');
                }
            });
        });
        $('.comm-auto-fill').on('click', function () {
            //var agent_id = $('#agent_id_holder').val();
            auto_fill_commission_data();
        });
        $('.fill-first-name').on('click', function () {
            var name = $('[name="pax_first_name[]"]').first().val();
            if (name == '')
                name = 'TBA';
            $('[name="pax_first_name[]"]').val(name);
        });
        $('.fill-last-name').on('click', function () {
            var name = $('[name="pax_last_name[]"]').first().val();
            if (name == '')
                name = 'TBA';
            $('[name="pax_last_name[]"]').val(name);
        });
        $('#suplier_id').on('change', function () {
            if ($(this).val() == "PTBSID0000000009")
            {
                $('.supplier_name').show();
                $("#supplier_name").prop('disabled', false);
            } else
            {
                $('.supplier_name').hide();
                $("#supplier_name").prop('disabled', true);
            }
        });

        $("#agent").autocomplete({
            source: function (request, response) {
                var term = request.term;
                var search_key = term;
                var cache = {};
                if (search_key in cache) {
                    response(cache[search_key]);
                    return
                } else {
                    $.getJSON(app_base_url + "index.php/ajax/get_domain_list", request, function (data, status, xhr) {
                        if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
                            data = cache[""]
                        } else {
                            cache[search_key] = data;
                            response(cache[search_key])
                        }
                    })
                }
            },
            minLength: 0,
            autoFocus: true,
            select: function (event, ui) {
                var label = ui.item.label;
                ;
                $(this).siblings('#agent_id_holder').val(ui.item.id);
                $("#agent_buying_price").attr('max', ui.item.balance);
                // $("#agent").val(label);      

                auto_fill_passenger_data(ui.item.balance);
            },
            change: function (ev, ui) {
                if (!ui.item) {
                    $(this).val("")
                }
            }
        }).bind('focus', function () {
            $(this).autocomplete("search");
        }).autocomplete("instance")._renderItem = function (ul, item) {
            var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);

            return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
        };
        function get_flight_row(type, count) {
            //alert(type+count)
            var $_row = $('.' + type + '_flight_row');
            
            var _c = parseInt($_row.data('count'));

            if (_c < count) {
               
                    //if ($.trim(data) != '') {
                        while (++_c <= count) {
                            $.get(app_base_url + "index.php/general/get_offline_flight_row/" + type, {'is_lcc': $('[name="is_lcc_return"]').val()}, function (data, status, xhr) {
                            $_row.append(data);
                            $_row.find('[name="dep_date_' + type + '[]"]').last().attr('id', 'dep_date_' + type + '_' + _c);
                            futureDatepicker('dep_date_' + type + '_' + _c);
                            $_row.find('[name="arr_date_' + type + '[]"]').last().attr('id', 'arr_date_' + type + '_' + _c);
                            futureDatepicker('arr_date_' + type + '_' + _c);

                            $_row.find('[name="dep_time_' + type + '[]"]').last().attr('id', 'dep_time_' + type + '_' + _c);
                            timePicker24('dep_time_' + type + '_' + _c);
                            $_row.find('[name="arr_time_' + type + '[]"]').last().attr('id', 'arr_time_' + type + '_' + _c);
                            timePicker24('arr_time_' + type + '_' + _c);
                            });
                        }
                   // }
                
            } else if (_c > count) {
                
                while (--_c >= count)
                    $_row.find('> .form-group').last().remove();
            }
            $_row.data('count', count)
        }
        function get_pax_row(type, count) {
            //alert(type+count)
            var $_row = $('.' + type + '_row');
            var _c = parseInt($_row.data('count'));
            //alert(_c);
            if (_c < count) {
                $.get(app_base_url + "index.php/general/get_offline_pax_row/" + type, {'trip_type': $('[name="trip_type"]:checked').val(), 'c_type': $('[name="is_lcc"]').val()}, function (data, status, xhr) {
                    if ($.trim(data) != '') {
                        while (++_c <= count) {
                            $_row.append(data);
                            $_row.find('[name="pax_pp_expiry[]"]').last().attr('id', type + '_pax_pp_expiry_' + _c);
                            futureDatepicker(type + '_pax_pp_expiry_' + _c);
                        }
                    }
                });
            } else if (_c > count) {
                while (--_c >= count)
                    $_row.find('> .form-group').last().remove();
            }
            $_row.data('count', count);
        }
        function auto_fill_passenger_data(data) {
            // console.log(data);
            $("#agent_balance_details").html('Domain Balance :' + data);
            /*   $.each(data.details, function (key, value) {
             $('[name="' + key + '"]').val(value);
             }); */
        }
        function auto_fill_commission_data() {
            $.post(app_base_url + "index.php/general/get_current_commission_details/", $('#offline_booking').serialize(), function (data, status, xhr) {
                data = $.parseJSON(data);
                $.each(data, function (key, value) {
                    $('[name="' + key + '"]').val(value);
                });
            });

        } 

    });
    
    /////
    function check_agent_balance(){
        //alert(agent_id);
        var agent_id = $("#agent_id").val();
		//alert(agent_id);
        $.ajax({
            'url' : app_base_url + "index.php/general/get_agent_balance",
            'type' : 'POST',
            'data' : {aid:agent_id},
            'dataType' : 'json',
            'success' : function(response){
                if(response == 'b2c'){
                    $('#agent_balance').addClass('hide');
                    $('#blance_amount').text('');
                }else{
                    $('#agent_balance').removeClass('hide'); 
                    $('#blance_amount').text(response.agent_base_currency +' '+response.balance);
                    var r = response;
                    $('input[name="passenger_title"]').val(r.title);
                    $('input[name="passenger_first_name"]').val(r.first_name);
                    $('input[name="passenger_last_name"]').val(r.last_name);
                    $('input[name="passenger_email"]').val(r.email);
                    $('input[name="passenger_phone"]').val(r.phone);
                    $('input[name="passenger_address"]').val(r.address);
                }
            }
        });
    }
</script>