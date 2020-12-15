<div class="content-wrapper">
	<div class="container">
    <div class="staffareadash">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/profile_navigator_tab') ?>
            <div class="clearfix"></div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane <?php echo ((isset($_GET['active']) == false || @$_GET['active'] == 'dashboard'))? 'active' : ''?>" id="dashbrd">
                	<div class="trvlwrap">
                    	<h3 class="welcmnotespl">Hi, <?=$full_name?></h3>
                        <div class="smlwel">All your trips booked with us will appear here and you'll be able to manage everything!</div>
                        <div class="bokinstts">
                        	<div class="col-xs-3 nopad">
                            	<div class="insidebx color1">
                                	<div class="ritlstxt">
                                    	<div class="contbokd"><?=$booking_counts['flight_booking_count']?></div>
                                        Flights booked
                                    </div>
                                    <span class="witbook fa fa-plane"></span>
                                    <a href="<?=base_url().'report/flight?default_view='.META_AIRLINE_COURSE?>" class="htview">
                                    	View detail
                                        <span class="fa fa-arrow-right"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-xs-3 nopad">
                            	<div class="insidebx color2">
                                	<div class="ritlstxt">
                                    	<div class="contbokd"><?=$booking_counts['hotel_booking_count']?></div>
                                        Hotel booked
                                    </div>
                                    <span class="witbook fa fa-bed"></span>
                                    <a href="<?=base_url().'report/hotel?default_view='.META_AIRLINE_COURSE?>" class="htview">
                                    	View detail
                                        <span class="fa fa-arrow-right"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-xs-3 nopad">
                            	<div class="insidebx color3">
                                	<div class="ritlstxt">
                                    	<div class="contbokd"><?=$booking_counts['bus_booking_count']?></div>
                                        Buses booked
                                    </div>
                                    <span class="witbook fa fa-bus"></span>
                                    <a href="<?=base_url().'report/bus?default_view='.META_AIRLINE_COURSE?>" class="htview">
                                    	View detail
                                        <span class="fa fa-arrow-right"></span>
                                    </a>
                                </div>
                            </div>
                            <!--
                            <div class="col-xs-3 nopad">
                            	<div class="insidebx color4">
                                	<div class="ritlstxt">
                                    	<div class="contbokd">112</div>
                                        Excursions booked
                                    </div>
                                    <span class="witbook fa fa-suitcase"></span>
                                    <a class="htview">
                                    	View detail
                                        <span class="fa fa-arrow-right"></span>
                                    </a>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="clearfix"></div>
                        <div class="retnset">
                        
                            <div class="col-xs-6 nopad">
                                <div class="insidemar">
                                    <h4 class="dskrty">Notifications</h4>
                                    
                                    <div class="rownotice">
                                        <div class="col-xs-1 nopad5">
                                            <div class="lofa fa fa-plane color1"></div>
                                        </div>
                                        <div class="col-xs-8 nopad5">
                                            <div class="noticemsg">London to Paris flight in <strong>$120</strong></div>
                                        </div>
                                        <div class="col-xs-3 nopad5">
                                            <span class="yrtogo">1 hour ago</span>
                                        </div>
                                    </div>
                                    
                                    <div class="rownotice">
                                        <div class="col-xs-1 nopad5">
                                            <div class="lofa fa fa-bed color2"></div>
                                        </div>
                                        <div class="col-xs-8 nopad5">
                                            <div class="noticemsg">Hilton hotel & resorts in <strong>$120</strong></div>
                                        </div>
                                        <div class="col-xs-3 nopad5">
                                            <span class="yrtogo">1 hour ago</span>
                                        </div>
                                    </div>
                                    
                                    <div class="rownotice">
                                        <div class="col-xs-1 nopad5">
                                            <div class="lofa fa fa-car color3"></div>
                                        </div>
                                        <div class="col-xs-8 nopad5">
                                            <div class="noticemsg">Economy car for 2 days in <strong>$120</strong></div>
                                        </div>
                                        <div class="col-xs-3 nopad5">
                                            <span class="yrtogo">1 hour ago</span>
                                        </div>
                                    </div>
                                    
                                    <div class="rownotice">
                                        <div class="col-xs-1 nopad5">
                                            <div class="lofa fa fa-plane color1"></div>
                                        </div>
                                        <div class="col-xs-8 nopad5">
                                            <div class="noticemsg">London to Paris flight in <strong>$120</strong></div>
                                        </div>
                                        <div class="col-xs-3 nopad5">
                                            <span class="yrtogo">1 hour ago</span>
                                        </div>
                                    </div>
                                    
                                    <div class="rownotice">
                                        <div class="col-xs-1 nopad5">
                                            <div class="lofa fa fa-bed color2"></div>
                                        </div>
                                        <div class="col-xs-8 nopad5">
                                            <div class="noticemsg">Hilton hotel & resorts in <strong>$120</strong></div>
                                        </div>
                                        <div class="col-xs-3 nopad5">
                                            <span class="yrtogo">1 hour ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-6 nopad">
                                <div class="insidemar">
                                    <h4 class="dskrty">Recent Acivities</h4>
                                    <div class="backfully">
                                    <?php foreach($latest_transaction as $lt_k => $lt_v) {
                                    	switch($lt_v['transaction_type']) {
                                    		case 'flight':
                                    			$icon = 'plane';
                                    			$boking_source = PROVAB_FLIGHT_BOOKING_SOURCE;
                                    			break;
                                    		case 'hotel':
                                    			$icon = 'bed';
                                    			$boking_source = PROVAB_HOTEL_BOOKING_SOURCE;
                                    			break;
                                    		case 'bus':
                                    			$icon = 'bus';
                                    			$boking_source = PROVAB_BUS_BOOKING_SOURCE;
                                    			break;
                                    			
                                    	}
                                    	?>
                                    	<a target="_blank" href="<?=base_url();?>voucher/<?=$lt_v['transaction_type']?>/<?=$lt_v['app_reference']?>/<?=$boking_source?>">
                                        <div class="rownotice2">
                                            <div class="col-xs-2 nopad5">
                                                <div class="lofa2 fa fa-<?=$icon?>"></div>
                                            </div>
                                            <div class="col-xs-7 nopad5">
                                                <div class="noticemsg2">
                                                    <?=$lt_v['app_reference']?>
                                                    <strong><?=app_friendly_absolute_date($lt_v['created_datetime'])?></strong>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 nopad5">
                                                <span class="yrtogo2"><?=$currency_obj->get_currency_symbol($lt_v['currency'])?> <?=$lt_v['total_fare']?></span>
                                            </div>
                                        </div>
                                        </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="clearfix"></div>
                    </div>
                </div>
                
                <div role="tabpanel" class="tab-pane <?php echo (@$_GET['active'] == 'profile')? 'active' : ''?>" id="profile">
                	<div class="dashdiv">
                        <div class="alldasbord">
                        	<div class="userfstep">
                        	<h3 class="welcmnote">Hi, <?=$full_name?></h3>
                            <a href="#edit_user_profile" data-aria-controls="home" data-role="tab" data-toggle="tab" class="editpro">Edit profile</a>
                            <div class="clearfix"></div>
                            <!-- Edit User Profile starts-->
                            <div class="tab-content">
                            <div role="tabpanel filldiv" class="tab-pane active" id="show_user_profile">
                                <div class="colusrdash">
                                    <img src="<?=$GLOBALS['CI']->template->domain_images($profile_image)?>" alt="profile Image" />
                                </div>
                                <div class="useralldets">
                                    <h4 class="dashuser"><?=$full_name?></h4>
                                    <div class="rowother">
                                        <span class="fa fa-envelope-o"></span>
                                        <span class="labrti"> <?=$email?></span>
                                    </div>
                                    <div class="rowother">
                                        <span class="fa fa-phone"></span>
                                        <span class="labrti">+<?=$user_country_code?> <?=$phone?></span>
                                    </div>
                                    <div class="rowother">
                                        <span class="fa fa-map-marker"></span>
                                        <span class="labrti"><?=$address?></span>
                                    </div>
                                 </div>
                            </div>
							    <div role="tabpanel" class="tab-pane" id="edit_user_profile">
							    <form method="post" name="edit_user_form" id="edit_user_form" enctype="multipart/form-data" autocomplete="off">
							        <div class="infowone">
							            <div class="clearfix"></div>
							            <div class="paspertorgn2 paspertedit">
							                <div class="col-xs-3 margpas">
							                    <div class="tnlepasport">
							                        <div class="paspolbl cellpas">Title</div>
							                        <div class="lablmain cellpas">
							                            <select name="title" class="clainput" required="required">
							                            <?=generate_options(get_enum_list('title'), (array)$title)?>
							                            </select>
							                        </div>
							                    </div>
							                </div>
							                <div class="col-xs-4 margpas">
							                    <div class="tnlepasport">
							                        <div class="paspolbl cellpas">FirstName</div>
							                        <div class="lablmain cellpas">
							                            <input type="text" name="first_name" placeholder="FirstName" value="<?=$first_name?>" class="clainput" required="required"/>
							                        </div>
							                    </div>
							                </div>
							                <div class="col-xs-5 margpas">
							                    <div class="tnlepasport">
							                        <div class="paspolbl cellpas">LastName</div>
							                        <div class="lablmain cellpas">
							                            <input type="text" name="last_name" placeholder="LastName" value="<?=$last_name?>" class="clainput" required="required"/>
							                        </div>
							                    </div>
							                </div>
							                   <div class="col-xs-3 margpas">
								                    <div class="tnlepasport">
								                        <div class="paspolbl cellpas">CountryCode</div>
								                        <div class="lablmain cellpas">
								                            <select name="country_code" class="clainput" required="required">
								                            <?=generate_options($country_code, (array)$user_country_code)?>
								                            </select>
								                        </div>
								                    </div>
							                	</div>
							                	<div class="col-xs-4 margpas">
								                    <div class="tnlepasport">
								                        <div class="paspolbl cellpas">MobileNumber</div>
								                        <div class="lablmain cellpas">
								                            <input type="text" name="phone" placeholder="MobileNumber" value="<?=$phone?>" class="clainput numeric" required="required"/>
								                        </div>
								                    </div>
							                	</div>
							                	<div class="col-xs-5 margpas">
								                    <div class="tnlepasport">
								                        <div class="paspolbl cellpas">Address</div>
								                        <div class="lablmain cellpas">
								                            <textarea name="address" placeholder="Address" class="clainput" required="required"><?=$address?></textarea>
								                        </div>
								                    </div>
							                	</div>
							                	<div class="col-xs-5 margpas">
								                    <div class="tnlepasport">
								                        <div class="paspolbl cellpas">ProfileImage</div>
								                        <div class="lablmain cellpas">
								                            <input type="file" name="image"/>
								                        </div>
								                    </div>
							                	</div>
							                <div class="clearfix"></div>
							                <button type="submit" class="savepspot">Update</button>
							                <a href="#show_user_profile" data-aria-controls="home" data-role="tab" data-toggle="tab" class="cancelll">Cancel</a>
							            </div>
							        </div>
							        </form>
							    </div>
							</div><!-- Edit User Profile Ends-->
							
                            </div>
                            
                            <div class="clearfix"></div>
                            
                            
                            <div class="othinformtn">
                            	<ul class="nav nav-tabs tabssyb" role="tablist">
                                    <li data-role="presentation" class="active">
                                        <a href="#passportinfo" data-aria-controls="home" data-role="tab" data-toggle="tab">Passport Information</a>
                                    </li>
                                    <li data-role="presentation" class="">
                                        <a href="#visainfo" data-aria-controls="home" data-role="tab" data-toggle="tab">Visa Information</a>
                                    </li>
                                   
                                 </ul>
                                 
                                 
                                 <div class="tab-content">
                                 
                					<div role="tabpanel" class="tab-pane active" id="passportinfo">
                                    	<div class="infowone">
                                        	<div class="paspertorgnl">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">Sunil GR</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nationality</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">2022-11-09</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Passport Number</div>
                                                    	<div class="lablmain cellpas">KO99966</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="editpasport">Edit</a>

                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="paspertorgnl paspertedit">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nationality</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="retnmar">
                                          <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="DD" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="MM" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="YYYY" />
                                            </div>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Passport Number</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="selectwrp custombord">
                                          <select class="custmselct">
                                            <option selected>Select Country</option>
                                            <option>Standard</option>
                                          </select>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="savepspot">Save</a>
                                                <a class="cancelll">Cancel</a>

                                            </div>
                                            </div>                                    
                                     </div>
                                     <div role="tabpanel" class="tab-pane" id="visainfo">
                                    	<div class="infowone">
                                        	<div class="paspertorgnl">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">Sunil GR</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">2022-11-09</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Visa Number</div>
                                                    	<div class="lablmain cellpas">KO99966</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="editpasport">Edit</a>

                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="paspertorgnl paspertedit">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="retnmar">
                                          <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="DD" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="MM" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="YYYY" />
                                            </div>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Visa Number</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="selectwrp custombord">
                                          <select class="custmselct">
                                            <option selected>Select Country</option>
                                            <option>Standard</option>
                                          </select>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="savepspot">Save</a>
                                                <a class="cancelll">Cancel</a>

                                            </div>
                                            </div>                                    
                                     </div>
                                     
                                  </div>
                                 
                            </div>
                            
                        </div>
             		</div>
                </div>
                
                <div role="tabpanel" class="tab-pane <?php echo (@$_GET['active'] == 'traveller')? 'active' : ''?>" id="travellerinfo">
                	<div class="trvlwrap">
                    	<div class="topbokshd">
                        	<h3 class="dashhed">Travellers Details</h3>
                            <a class="addbutton" data-toggle="modal" data-target="#add_traveller_tab">Add Traveller</a>
                        </div>
					<!-- Add Travller Modal Starts-->
					<div class="modal fade" id="add_traveller_tab" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog modal-lg" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title" id="myModalLabel">Add Traveller</h4>
					      </div>
					      <div class="modal-body">
				        <div class="othinformtn">
				            <div class="tab-content">
				                <div class="tab-pane active" role="tabpanel">
				                    <div class="infowone">
				                    <form action="<?=base_url().'user/add_travller'?>" method="post" name="add_traveller_form" id="add_traveller_form">
				                        <div class="paspertedit">
				                        	<div class="col-xs-6 margpas">
				                                <div class="tnlepasport">
				                                    <div class="paspolbl cellpas">Type</div>
				                                    <div class="lablmain cellpas">
				                                     	<select name="travller_type" class="clainput" required="required">
				                                     	<?=generate_options(get_enum_list('travller_type'))?>
				                                     	</select> 
				                                     </div>
				                                </div>
				                            </div>
				                            <div class="col-xs-6 margpas">
				                                <div class="tnlepasport">
				                                    <div class="paspolbl cellpas">Name</div>
				                                    <div class="lablmain cellpas"> 
				                                    	<input name="traveller_name" type="text" class="clainput" placeholder="TravllerName" required="required"> 
				                                    </div>
				                                </div>
				                            </div>
				                            
				                            <div class="col-xs-6 margpas">
				                                <div class="tnlepasport">
				                                    <div class="paspolbl cellpas">DOB</div>
				                                    <div class="lablmain cellpas"> 
				                                    	<input name="traveller_date_of_birth" type="text" class="clainput" placeholder="DOB" required="required"> 
				                                    </div>
				                                </div>
				                            </div>
				                            <div class="col-xs-6 margpas">
				                                <div class="tnlepasport">
				                                    <div class="paspolbl cellpas">Email</div>
				                                    <div class="lablmain cellpas"> 
				                                    	<input name="traveller_email" type="text" class="clainput" placeholder="Email" required="required"> 
				                                    </div>
				                                </div>
				                            </div>
				                            <div class="clearfix"></div> 
				                            <button type="submit" class="savepspot">Add</button> 
				                            <a class="cancelll" data-dismiss="modal">Cancel</a>
				                        </div>
				                        </form>
				                    </div>
				                </div>
				
				            </div>
				        </div>
					      </div>
					      
					    </div>
					  </div>
					</div>
					<!-- Add Travller Modal Ends-->
                        <div class="fulltable">
                        
                        	<div class="trow tblhd">
                            	<div class="col-xs-3 tblpad">
                                	<span class="lavltr">Name</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">Type</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">DOB</span>
                                </div>
                                <div class="col-xs-3 tblpad">
                                	<span class="lavltr">Email</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">Action</span>
                                </div>
                            </div>
                            
                            <div class="trow">
                            	<div class="col-xs-3 tblpad">
                                	<span class="lavltr">Sunil GR</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">ADT</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">1991-11-04</span>
                                </div>
                                <div class="col-xs-3 tblpad">
                                	<span class="lavltr">sunilgr@provab.com</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">
                                    	<a class="detilac" data-toggle="collapse" data-target="#collapse101" aria-expanded="true">Detail</a>
                                        <a class="fa fa-trash-o"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div id="collapse101" class="collapse">
                            	<div class="travemore">
                                	<div class="othinformtn">
                            	<ul class="nav nav-tabs tabssyb" role="tablist">
                                    <li data-role="presentation" class="active">
                                        <a href="#useinform" data-aria-controls="home" data-role="tab" data-toggle="tab">User Information</a>
                                    </li>
                                    <li data-role="presentation" class="">
                                        <a href="#passportinfo1" data-aria-controls="home" data-role="tab" data-toggle="tab">Passport Information</a>
                                    </li>
                                    <li data-role="presentation" class="">
                                        <a href="#visainfo1" data-aria-controls="home" data-role="tab" data-toggle="tab">Visa Information</a>
                                    </li>
                                   
                                 </ul>
                                 <div class="tab-content">
                                 
                                 <div role="tabpanel" class="tab-pane active" id="useinform">
                                    	<div class="infowone">
                                        	<div class="paspertorgnl">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">Sunil GR</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Type</div>
                                                    	<div class="lablmain cellpas">ADT</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">DOB</div>
                                                    	<div class="lablmain cellpas">2022-11-09</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">PEmail</div>
                                                    	<div class="lablmain cellpas">balu.provab@gmail.com</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="clearfix"></div>
                                                <a class="editpasport">Edit</a>

                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="paspertorgnl paspertedit">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Type</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">DOB</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="retnmar">
                                          <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="DD" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="MM" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="YYYY" />
                                            </div>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Email</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="clearfix"></div>
                                                <a class="savepspot">Save</a>
                                                <a class="cancelll">Cancel</a>

                                            </div>
                                            </div>                                    
                                     </div>
                                 
                					<div role="tabpanel" class="tab-pane" id="passportinfo1">
                                    	<div class="infowone">
                                        	<div class="paspertorgnl">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">Sunil GR</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nationality</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">2022-11-09</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Passport Number</div>
                                                    	<div class="lablmain cellpas">KO99966</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="editpasport">Edit</a>

                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="paspertorgnl paspertedit">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nationality</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="retnmar">
                                          <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="DD" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="MM" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="YYYY" />
                                            </div>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Passport Number</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="selectwrp custombord">
                                          <select class="custmselct">
                                            <option selected>Select Country</option>
                                            <option>Standard</option>
                                          </select>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="savepspot">Save</a>
                                                <a class="cancelll">Cancel</a>
                                                </div>
                                            </div>                                    
                                     </div>
                                     
                                     
                                     <div role="tabpanel" class="tab-pane" id="visainfo1">
                                    	<div class="infowone">
                                        	<div class="paspertorgnl">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">Sunil GR</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">2022-11-09</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Visa Number</div>
                                                    	<div class="lablmain cellpas">KO99966</div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">India</div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="editpasport">Edit</a>

                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="paspertorgnl paspertedit">
                                            	<div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Nmae</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Expiry Date</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="retnmar">
                                          <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="DD" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="MM" />
                                            </div>
                                            <div class="col-xs-4 splinmar">
                                            	<input type="text" class="clainput" placeholder="YYYY" />
                                            </div>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Visa Number</div>
                                                    	<div class="lablmain cellpas">
                                                        	<input type="text" class="clainput" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 margpas">
                                                	<div class="tnlepasport">
                                                    	<div class="paspolbl cellpas">Issuing Country</div>
                                                    	<div class="lablmain cellpas">
                                                        	<div class="selectwrp custombord">
                                          <select class="custmselct">
                                            <option selected>Select Country</option>
                                            <option>Standard</option>
                                          </select>
                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <a class="savepspot">Save</a>
                                                <a class="cancelll">Cancel</a>

                                            </div>
                                        </div>
                                     </div>
                                     
                                  </div>
                                 
                            </div>
                                </div>
                            </div>
                            <div class="trow">
                            	<div class="col-xs-3 tblpad">
                                	<span class="lavltr">Sunil GR</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">ADT</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">1991-11-04</span>
                                </div>
                                <div class="col-xs-3 tblpad">
                                	<span class="lavltr">sunilgr@provab.com</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">
                                    	<a class="detilac">Detail</a>
                                        <a class="fa fa-trash-o"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="trow">
                            	<div class="col-xs-3 tblpad">
                                	<span class="lavltr">Sunil GR</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">ADT</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">1991-11-04</span>
                                </div>
                                <div class="col-xs-3 tblpad">
                                	<span class="lavltr">sunilgr@provab.com</span>
                                </div>
                                <div class="col-xs-2 tblpad">
                                	<span class="lavltr">
                                    	<a class="detilac">Detail</a>
                                        <a class="fa fa-trash-o"></a>
                                    </span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
    
	<!-- Main content -->
	<section class="content hide">
		<div class="row">
			<div class="col-md-3">
				<!-- Profile Image -->
				<div class="panel panel-default">
					<div class="panel-body">
						<img alt="User profile picture" src="<?=$GLOBALS['CI']->template->template_images(get_profile_image($GLOBALS['CI']->entity_image));?>" class="profile-user-img img-responsive img-circle">
						<h3 class="text-center"><i class="fa fa-circle text-success"></i> <?=$this->entity_name?></h3>
						<p class="text-muted text-center"><?php echo (empty($this->entity_date_of_birth) == false ? app_friendly_date($this->entity_date_of_birth) : 'Date Of Birth');?></p>
						<ul class="list-group list-group-unbordered">
							<li class="list-group-item">
								<b>User ID:</b> <a class=""><?=$this->entity_uuid?></a>
							</li>
							<li class="list-group-item">
								<b>Email:</b> <a class=""><?=$this->entity_email?></a><br><br>								
							</li>
							<li class="list-group-item">
								<b>Points:</b> <a class="">13,287</a>
							</li>
						</ul>
						<a class="btn btn-default btn-block" href="#">Since :<b><?=app_friendly_date($this->entity_created_datetime)?></b></a>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
				<!-- About Me Box -->
				<div class="panel panel-default">
					<div class="panel-heading with-border">
						<h3 class="panel-title">About Me</h3>
					</div>
					<!-- /.box-header -->
					<div class="panel-body">
						<strong><i class="fa fa-pencil margin-r-5"></i> My Bookings</strong>
						<p>
							<ul class="list-group">
								<?php
								$active_domain_modules = $GLOBALS['CI']->active_domain_modules;
								$master_module_list = $GLOBALS['CI']->config->item('master_module_list');
								foreach ($master_module_list as $k => $v) {
									if (in_array($k, $active_domain_modules)) {
									?>
									<li class="list-group-item <?=get_arrangement_color($k)?>"><a href="<?php echo base_url()?>index.php/report/<?=strtolower($v)?>" class="white-text">
										<i class="<?=get_arrangement_icon(module_name_to_id($v))?>"></i> <span class=""><?=ucfirst($v)?> Booking</span></a></li>
									<?php
									}
								}
								?>
							</ul>
						</p>
						<hr>
						<strong><i class="fa fa-phone margin-r-5"></i>Phone :</strong> 
						<?=(empty($this->entity_phone) == false ? $this->entity_phone : 'Update Now')?>						
						<hr>
						<strong><i class="fa fa-map-marker margin-r-5"></i> Address :</strong> 
						<?=(empty($this->entity_address) == false ? $this->entity_address : 'Update Now')?>
						<hr>
						<strong><i class="fa fa-file-text-o margin-r-5"></i> Language Preference :</strong> 
						<?=strtoupper($this->entity_language_preference)?>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
			<div class="col-md-9">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#activity" aria-expanded="true">Activity</a></li>
						<li class=""><a data-toggle="tab" href="#timeline" aria-expanded="false">Timeline</a></li>
						<li class=""><a data-toggle="tab" href="#settings" aria-expanded="false">Settings</a></li>
					</ul>
					<div class="tab-content">
						<div id="activity" class="tab-pane active">
							Under Working
						</div>
						<!-- /.tab-pane -->
						<div id="timeline" class="tab-pane">
							<!-- The timeline -->
							 <ul class="timeline-wrapper timeline" id="timeline-list">
							 </ul>
							 <div id="event_bottom_chain">
								<div style="" class="data-utility-loader text-center">
									Under Working
									<!-- 
									<span><span/>
									Please Wait <img class="img-responsive center-block" src="/proapp_ng/extras/system/template_list/template_v1/images/tiny_loader_v1.gif">
									 -->
								</div>
							</div>
						</div>
						<?php
						$adult_enum = $child_enum = get_enum_list('title');
						$adult_title_options = generate_options($adult_enum, array($title), true);
						?>
						<!-- /.tab-pane -->
						<div id="settings" class="tab-pane">
							<hr>
							<form class="form-horizontal" autocomplete="off" method="post">
								<div class="form-group">
									<label class="col-sm-2 control-label">Name</label>
									<div class="col-sm-2">
										<select required class="form-control" name="title">
										<?=$adult_title_options?>
										</select>
									</div>
									<div class="col-sm-4">
										<input required type="text" value="<?=$first_name?>" name="first_name" placeholder="First Name" id="" class="form-control">
									</div>
									<div class="col-sm-4">
										<input required type="text" value="<?=$last_name?>" name="last_name" placeholder="Last Name" id="" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Date Of Birth</label>
									<div class="col-sm-2">
										<input required type="text" value="<?=$date_of_birth?>" id="date_of_birth" name="date_of_birth" placeholder="DOB" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="inputName">Phone</label>
									<div class="col-sm-2">
										<select required class="form-control" name="country_code">
										<?=generate_options($country_code, array(INDIA_CODE))?>
										</select>
									</div>
									<div class="col-sm-8">
										<input required type="text" value="<?=$phone?>" name="phone" placeholder="Mobile" id="" class="form-control numeric">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Address</label>
									<div class="col-sm-10">
										<textarea required placeholder="Address" name="address" id="" class="form-control"><?=$address?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="">Signature</label>
									<div class="col-sm-10">
										<textarea placeholder="Signature" name="signature" id="" class="form-control"><?=$signature?></textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<button class="btn btn-primary" type="submit">Submit</button>
									</div>
								</div>
							</form>
						</div>
						<!-- /.tab-pane -->
					</div>
					<!-- /.tab-content -->
				</div>
				<!-- /.nav-tabs-custom -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
</div>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script>
$(document).ready(function() {
	function load_timeline(_event_start, event_limit)
	{
		if ($('#timeline').is(':visible') == false) {
			return false;
		}
		load_event_bottom_chain = false;
		$.get(app_base_url+'index.php/utilities/timeline_rack?oe_start='+_event_start+'&oe_limit='+event_limit, function(response) {
			if (response.status == false) {
				load_event_bottom_chain = false;
				$('#event_bottom_chain').hide();
				$('#timeline-list').append('<li><i class="fa fa-clock-o bg-gray"></i></li>');
			} else {
				$('#timeline-list').append(response.oe_list);
				load_event_bottom_chain = true;
				event_start = (event_start+event_limit);
				lazy_loader();
				adjust_time_label();
			}
		});
	}
	function adjust_time_label()
	{
		var time_head_list = {};
		var _cur_label_id = '';
		var _pre_label_id = '';
		var event_stamp = 'rt_list'+(new Date()).getTime();
		$('.time-label').each(function(k, v) {
			cur_ele = $(this);
			_cur_label_id = this.id;
			if (_cur_label_id == _pre_label_id) {
				$(this).fadeOut(3000).addClass(event_stamp);
			} else {
				_pre_label_id = _cur_label_id;
			}
		});
		setTimeout(function() {
			$('.'+event_stamp).remove();
			lazy_loader();
		}, 3000);
	}
	//Load timeline events
	$(window).scroll(function() {
		lazy_loader();
	});

	function lazy_loader()
	{
		if (isVisibleOnViewPort('#event_bottom_chain') == true && load_event_bottom_chain == true) {
			load_timeline(event_start, event_limit);
		}
	}
	function isVisibleOnViewPort(elem)
	{
		var $elem = $(elem);
		var $window = $(window);

		var docViewTop = $window.scrollTop();
		var docViewBottom = docViewTop + $window.height();

		var elemTop = $elem.offset().top;
		var elemBottom = elemTop + $elem.height();

		return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}

	//
	var event_start = 0;
	var event_limit = 20;
	var load_event_bottom_chain = true;
	lazy_loader(event_start, event_limit);
	var os_event_list = function() {
		var _latest_event_id = parseInt($('.event-origin:first').data('event-id'));
		//_latest_event_id = 50;
		if (_latest_event_id > 0) {
			$.ajax({
				url: app_base_url+'index.php/utilities/latest_timeline_events?last_event_id='+_latest_event_id,
				success: function(response) {
					if (response.status) {
						$('#timeline-list').prepend(response.oa_list);
						adjust_time_label();
					}
				},

				complete: function(){setTimeout(os_event_list, 500);}
			});
		}
	};
	var interval = setTimeout(os_event_list, 5000);
	$('.editpasport').click(function(){
		$(this).parent().parent('.infowone').addClass('editsave');
	});
	
	$('.savepspot, .cancelll').click(function(){
		$(this).parent().parent('.infowone').removeClass('editsave');
	});
});
</script>