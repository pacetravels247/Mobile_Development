<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css')?>">
<!-- <div class="fixed_height mb15">
   <div class="col-md-12 nopad">
      <div class="hldyblak blue_bg">
         <div class="container-fluid">
            <div class="tab-content custmtab">
               <form action="<?php echo base_url();?>index.php/tours/holiday_package_listt" method="post" id="holiday_search">
                  <div class="col-md-8 col-md-offset-2 holiday_srch_input">
                     <div class="form-group">
                        <input type="text" id="af_tag" name="search_type" class=" form-control" placeholder="Search City,Country,Place">
                     </div>
                     <div class="search_but">
                        <input type="submit" class="srch_butt" value="search" />
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div> -->
<?php
$image_array=array();
$image_data=array();
//debug($tours_itinerary_dw);
foreach($tours_itinerary_dw as $ite_image){
	$photos=explode(',',$ite_image['banner_image']);
	foreach($photos as $val_pho){
		if($val_pho!=''){
			array_push($image_array,$val_pho);
			array_push($image_data,$ite_image['visited_city_name']);
		}
	}
	
}
//debug($image_array);
//debug($package_details); 
	//$photos=explode(',',$package_details[0]['banner_image']);
	//array_pop($photos);
	//debug($photos);

?>

<div class="clearfix"></div>

<div class="id-spcl_atrct">
  <div class="col-sm-offset-1 col-xs-10">
  <?php echo $this->session->flashdata("msg"); ?>


<div class="row" >
  <div class="col-sm-6 nopad">
    <h3 class=" text-left"><?= $package_details[0]['package_name']?></h3>
  </div>
  <div class="col-sm-6 nopad">
   <!-- <a class="text-right id-back-s">< Back</a>-->
	 <a href="<?php echo base_url()?>menu/dashboard/package?default_view=VHCID1433498322" class="pull_right id-back-s">Back</a>
  </div>
</div>
<div class="org_row">
  
  

   <div class="col-md-6 padfive pck_sldr">

        <div class="owl-carousel owl-theme">
        <?php foreach($image_array as $pic_key => $pic){ ?>
          <div class="item">
            <img src="<?php echo $GLOBALS['CI']->template->domain_images($pic)?>" alt="<?=$image_data[$pic_key]?>"> 
            <div class="img-caption"><?=$image_data[$pic_key]?></div>
          </div>
        <?php } ?>
          </div>
          <!-- <a class="left carousel-control" data-slide="prev">
            <i class="fa fa-chevron-left left"></i>
          </a>
          <a class="right carousel-control" data-slide="next">
            <i class="fa fa-chevron-right right"></i>
          </a> -->

   </div>
   <div class="col-md-6 padfive lft_side_dargling">


    <!-- <div class="id-package-details">
      <div class="row">
        <div class="col-sm-8 nopad">
          <p><span>Duration :</span>4 Days / 3Nights</p>
        </div>
        <div class="col-sm-4 nopad">
          <p><span>Starting From :</span>INR 9,000</p>
        </div>
      </div><hr>
      <div class="row">
        <p><span>Destination: </span>Andaman Package</p>
      </div><hr>
      <div class="row">
        <div class="col-sm-4">
          <p><span>Departure Date</span></p>
        </div>
        <div class="col-sm-4">
          <input type="text" name="" class="form-control"> 
        </div>
      </div><hr>
      <div class="row">
        <div class="col-sm-6 nopad">
          <p><span>Tour Code :</span>#PT8712</p>
        </div>
        <div class="col-sm-6 nopad">
          <p><span>Holiday Type :</span>Andaman Package</p>
        </div>
      </div><hr>
      <div class="row">
        
        
      </div>
    </div> -->
		<?php 
		//debug($package_details[0]);
			foreach ($b2b_tour_price as $tour_price_fly) {
				$per_person_price[$tour_price_fly['occupancy']]=$tour_price_fly['market_price'];
			} 
			
			
		?> 
      <div class="col-xs-12 lft_detl">
         <div class="col-xs-12 mn_lst nopad">
            
            <div class="col-xs-8 nopad">
              <p><i class="far fa-clock"></i> &nbsp; <strong> Duration : </strong> &nbsp;<?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
    
               
            </div>
            <div class="col-xs-4 nopad">
              <p><strong><?=$package_price_details[0]['currency']?>: &nbsp; <?=$per_person_price[10]?>/-</strong> <br><small>(Price per person)</small></p>
            </div>
         </div>
         <div class="col-xs-12 mn_lst nopad">
          <div class="row">
            <div class="col-sm-4 nopad">
              <p class="id-depart"><i class="fa fa-calendar" aria-hidden="true"></i> &nbsp; <strong> Departure Date :</strong></p>
            </div>
            <div class="col-sm-4 padfive">
              
             <!-- <input id="datepicker_dat" type="text" class="datpic" value="dd/mm/yyyy"> -->
			<?php  
				
				
				//debug($date_range);
				if($package_details[0]['package_type'] == "fit"){ 
					$from_range=explode(',',$package_details[0]['valid_frm']);
					$to_range=explode(',',$package_details[0]['valid_to']);
					$group_departure=array();
					foreach($from_range as $d_key => $d_val){
						$start_date =$d_val;
						$end_date = $to_range[$d_key];
						while (strtotime($start_date) <= strtotime($end_date)) {
							$group_departure[]=date('j-n-Y', strtotime($start_date));
							$start_date = date ("Y-m-d", strtotime("+1 days",strtotime($start_date)));
						}
					}
					$last_item=end($group_departure);
					$last_item=date('Y-n-j', strtotime($last_item));
					$first_item=date('Y-n-j', strtotime($group_departure[0]));
					$group_departure=implode(',',$group_departure); 
			?>
					<input  id="datepicker_dat_group" type="text" class="form-control id-inputfield" value="dd/mm/yyyy">
			<?php 
				}else{
					$group_departure=array();
					foreach($dep_dates as $dep_key => $dep_val){ 
						$group_departure[]=date('j-n-Y', strtotime($dep_val['dep_date']));
					}
					$last_item=end($group_departure);
					$last_item=date('Y-n-j', strtotime($last_item));
					$first_item=date('Y-n-j', strtotime($group_departure[0]));
					$group_departure=implode(',',$group_departure); 
			?>
				<input  id="datepicker_dat_group" type="text" class="form-control id-inputfield" value="dd/mm/yyyy">
			<?php
				} 
			?>
            </div>
            
            
             

          </div>
          
      </div>

         <div class="col-sm-12 id-package-city">
          <div class="row">
            <div class="col-sm-6 nopad">
              <p><strong>Tour Code :</strong><?=$package_details[0]['tour_code']?></p>
              <p><strong>Country :</strong><?=$country?></p>
            </div>
            <div class="col-sm-6 nopad">
              <p><strong>Holiday Type :</strong>
                <?php 
                  $types=array();
                  foreach($tour_types as $type_val){
                     $types[]=$type_val['tour_type_name'];
                  } 
                  echo implode(',',$types);
                ?> </p>
                <p><strong>City :</strong><span><?=$city?></span></p>
            </div>
          </div>
         </div>
         

         <div class="col-xs-12 mn_lst nopad">
          <div class="room_sec">
            <h4>Select Number of Rooms & Travellers</h4>
			<input type="hidden" class="no_room" value="1">
			<input type="hidden" class="room_count" value="1">
            <div class="row room_block">
              <ul class="list-inline">
               <li class="id-room"><p class="room_label">Room 1 </p></li>
               <li>
                  <label>Adult</label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decrease_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number" class="form-control no-padding add-color text-center height-25 decrease_1 increase1" maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red btn-pluss" id="increase1"   type="button">+</button>
                     </span>
                  </div>
                  <small>Above 12 years</small>
               </li>
                <li>
                  <label>Child <small>(with bed)</small></label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreaseValue_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number1" class="form-control no-padding add-color text-center height-25 decreaseValue_1 increaseValue_1" maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red btn-pluss" id="increaseValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>Below 12 years</small>
               </li>
               <li>
                  <label>Child <small>(without bed)</small></label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreasewoValue_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number2" class="form-control no-padding add-color text-center height-25 decreasewoValue_1 increasewoValue_1" maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red btn-pluss" id="increasewoValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>Below 12 years</small>
               </li>
               <li>
                  <label>Infant</label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreaseinfValue_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number3" class="form-control no-padding add-color text-center height-25 decreaseinfValue_1 increaseinfValue_1" maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red btn-pluss" id="increaseinfValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>(0-2 years)</small>
               </li>
            </ul>
            </div>
            
            <!-- <button type="button" class="btn btn-default btn_enq" data-toggle="modal" data-target="#enquiry_form">Enquiry</button> -->
            <div class="row">
              <p class="id-add-rooms" id="add_rooms">+ &nbsp;Add Rooms</p>
            </div><hr style="margin-top: 0;">
            <div class="row id-package-btn">
              <div class="col-sm-6 padfive">
                <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#enquiry_form">Enquiry</button>
              </div>
              <div class="col-sm-6 padfive">
                <button type="button" class="btn btn-danger form-control" data-toggle="modal" data-target="#optional_form">Choose Optional Service</button>
              </div>
            </div>

          </div>



         </div>

         
     <!-- Modal -->
      <div class="modal fade" id="enquiry_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry" method="post" id="send_enquiry">
            <div class="col-md-10 col-md-offset-1 holiday_srch_input">
            <input type="hidden"  name="pack_id"  value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  value="<?=$package_details[0]['package_name']?>">
             <div class="form-group">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" maxlength="30" required>
             </div>
             <div class="form-group">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" maxlength="45" required>
             </div>
             <div class="form-group">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" maxlength="12" required>
             </div>
             <div class="form-group">
              <label>No. of Passengers</label>
              <input type="number"  name="passenger" class="form-control" placeholder="Enter No Of Guests" required>
             </div>
             <div class="form-group">
              <label>Messenger</label>
              <textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
             </div>
             <div class="form-group">
              <label>Departure Date</label>
              <select name="dep_date" class="form-control">
              
                <?php foreach($dep_dates as $dep_key => $dep_val){ ?>
                <option value="<?=$dep_val['dep_date']?>"><?=date('d M Y',strtotime($dep_val['dep_date']))?></option>
                <?php } ?>
              
              </select>
             </div>
             
           
          
          </div>
          <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
          <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-danger form-control id-enquiry-btn">Send Enquiry</button>
            </div>
          </div>
          </div>
          </form>
           </div>
        </div>
        </div>
      </div>





      <div class="modal fade" id="optional_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Price Details</h4>
          </div>
          <div class="modal-body">
            <p class="id-head-h"><span>Dazzling Andoman</span> &nbsp; <small>24-Jul-2020 - 4 Nights</small></p> 
            <table class="tabel table-bordered" style="margin-right: auto;margin-left: auto;width: 100%;text-align: center;">
                <tr>
                  <th>Adult on twin sharing</th>
                  <th>Triple Sharing</th>
                  <th>Child with bed</th>
                  <th>Child without bed</th>
                  <th>Infant</th>
                </tr>
                <tr>
                  <td><del>₹ 75,000</del></td>
                  <td><del>₹ 65,000</del></td>
                  <td><del>₹ 55,000</del></td>
                  <td><del>₹ 45,000</del></td>
                  <td><del>₹ 35,000</del></td>
                </tr>
                <tr>
                  <td>₹ 75,000</td>
                  <td>₹ 65,000</td>
                  <td>₹ 55,000</td>
                  <td>₹ 45,000</td>
                  <td>₹ 35,000</td>
                </tr>    
                        
              </table>
              <ul>
                <li>Booking fee at the time of confirmation ₹ 36,000</li>
                <li>Booking fee at the time of confirmation ₹ 36,000</li>
                <li>Booking fee at the time of confirmation ₹ 36,000</li>
                <li>Booking fee at the time of confirmation ₹ 36,000</li>
                <li>Booking fee at the time of confirmation ₹ 36,000</li>
              </ul>
              <hr style="margin: 5px;">
              <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                  <button type="submit" class="btn btn-danger form-control id-enquiry-btn">Enquiry Now</button>
                </div>
                <div class="col-sm-4">
                  <button type="submit" class="btn btn-primary form-control id-enquiry-btn">Book</button>
                </div>
              </div>
           </div>
        </div>
        </div>
      </div>










      </div>
   </div>
   


</div>
</div>
</div>
<div class="spcl_atrct">
<div class="clearfix"></div>

</div>

<div class="col-sm-1"></div>
<div class="clearfix"></div>
<div class="col-sm-1"></div>
<div class="col-xs-10 hldy_tab nopad">



   <div class="col-sm-9 padfive">
   	<div>
      <ul class="nav nav-tabs responsive-tabs">
         <li class="active"><a href="#home1">Overview</a></li>
         <li><a href="#messages1">Inclusion-Exclusion</a></li>
         <li><a href="#settings1">Hotel Details</a></li>
		     <li><a href="#price">Pricing Details</a></li>
         <li><a href="#cancel">Cancellation Policy</a></li>
         <li><a href="#t_c">T&C</a></li>
         <li><a href="#documentation">Documentation</a></li>
         <li><a href="#payment_policy">Payment Policy</a></li>
      </ul>
      <div class="tab-content description" id="pck_scroll">

         <div class="tab-pane active" id="home1">
              <div class="id-download-itenirary text-right">
                <span><i class="fa fa-envelope" aria-hidden="true"></i> &nbsp;Email Itinerary</span>&nbsp;&nbsp; | &nbsp;&nbsp;<span><i class="fa fa-print" aria-hidden="true"></i> &nbsp;Print Itinerary</span>&nbsp;&nbsp; | &nbsp;&nbsp;<span><i class="fa fa-download" aria-hidden="true"></i>&nbsp; Download Itinerary</span>
              </div>
              <p> <?php
                foreach ($tours_itinerary_dw as $key => $itinary) {
                  $accommodation = $itinary['accomodation'];
                  $accommodation = json_decode($accommodation);
                  $visited_city=json_decode($itinary['visited_city'],1);?>
                  <tr>
                    <td style=""><div class="id-overview-row">
                      <span style="margin:0 0 2px; font-weight:bold"><h4><span class="id-day">Day <?php echo $key+1; ?> -</span> <?php echo  $itinary['visited_city_name']; ?> </h4></span>
                      <p style="margin:0;">
                        <?php echo  htmlspecialchars_decode($itinary['itinerary_des']);   ?>
                      </p>
                  
                      <div class="id-meal-div">Meal Plan:
                        <?php foreach ($accommodation as  $accom) {
                        if ($accom === end($accommodation)){
                             echo $accom;
                          }else{
                             echo $accom.'|';
                          }
                    } ?></div>
                  <!-- <br><br> -->
                    </div>
                </td>
                </tr>
                <?php } ?>
              </p>
            
         </div>
        
          <div class="tab-pane" id="messages1">
                <div class="id-overview-row"> 
          			   <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Package Price Includes:</h4></span>
                  
            			<div class="id-inc-div">
                    <p style="margin:0;white-space: normal; padding-left: 10px;">
                      <?php 
                      $package_details[0]['inclusions'] = str_replace('\n', '', $package_details[0]['inclusions']);
                      echo htmlspecialchars_decode($package_details[0]['inclusions']); 
                      ?>
                    </p>
                  </div>

                </div>
                <div class="id-overview-row"> 
          			   <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Package Price Does Not Includes:</h4></span>
                    <div class="id-inc-div">
              			   <p style="margin:0;white-space: normal;font-weight: normal;">
                        <?php 
                        $package_details[0]['exclusions'] = str_replace('\n', '', $package_details[0]['exclusions']);
                        echo htmlspecialchars_decode($package_details[0]['exclusions']); 
                        ?>
                        </p>
                    </div>
                </div>
          </div>

         <div class="tab-pane" id="settings1">
          <div class="id-overview-row"> 
              <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Hotel Details</h4></span>
              <p style="margin:0;white-space: normal;">
        				<ul>
        				<?php 
        					foreach($tours_hotel_det as $hotel_det_key => $hotel_val){
        				?>
        					<li>
        					<?php if($hotel_val['no_of_night']['hotel_id']!='') {?>
        						<?=$hotel_val['no_of_night']?> Nights Accommodation in <?=$hotel_val['hotel_name']?>
        					<?php }else{ ?>
        						<?=$hotel_val['no_of_night']?> Nights in <?=$hotel_val['city']?>
        					<?php } ?>
        					</li>
        				<?php
        					}
        				?>
        				</ul>
              </p>
            </div>
         </div>

		<div class="tab-pane" id="price">
			<div class="id-overview-row"> 
				<span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Price Details</h4></span>
    			<ul class="price">
					<table class="tabel table-bordered" style="margin-right: auto;margin-left: auto;width: 100%;text-align: center;">
						<?php 
							foreach ($b2b_tour_price as $tour_price_fly) { 
								$occ=$tour_price_fly['occupancy'];
								$query_x = "select * from occupancy_managment where id='$occ'"; 
								$exe   = $this->db->query ( $query_x )->result_array ();
								$fetch_x = $exe[0];
      					?>
      									
							<tr>
								<th><?=$fetch_x['occupancy_name']?></th>
								<td>₹ <?=$tour_price_fly['market_price']?></td>
							</tr>
      					<?php } ?> 
					</table>
				</ul>
			</div>
		</div>

      <div class="tab-pane" id="cancel">
        <div class="id-overview-row id-cancel-div"> 
          <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Cancellation Policy</h4></span>
          <p><?=$package_details[0]['canc_policy']?></p>
        </div>
      </div>

      <div class="tab-pane" id="t_c">
          <div class="id-overview-row"> 
            <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Terms & Conditions</h4></span>
			<p><?=$package_details[0]['terms']?></p>
          </div>
      </div>
      <div class="tab-pane" id="documentation">
          <div class="id-overview-row"> 
            <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Visa Procedure and Documentation</h4></span>
			<p><?=$package_details[0]['visa_procedures']?></p>
          </div>
      </div>
      <div class="tab-pane" id="payment_policy">
          <div class="id-overview-row"> 
            <span style="margin:0 0 2px; font-weight:bold"><h4 class="id-inclusion-e">Payment Policy</h4></span>
			<p><?=$package_details[0]['b2b_payment_policy']?></p>
          </div>
      </div>



      </div>
   </div>
</div>

<div class="col-sm-3 pck_img_sec">
    <h4 class="h4">Related Packages</h4>
	
	<?php 
	//debug($related_packages);
	$package_count=1;
		foreach($related_packages as $rel_val){
			foreach($rel_val as $related_val){
				if($package_count>=5){
					break 2;
				}
	?>
		<div class="id-selected-package-d">
			<div class="id-image-d">
			  <div class="id-content-d">
				<div class="id-content-overlay-d"></div>
				<img src="<?php echo $GLOBALS['CI']->template->domain_images($related_val['banner_image'])?>" alt="image">
				<div class="id-content-details-d id-fadeIn-bottom-d">
				  <div class="price_package-d">
					<div>
					  <span class="id-head-cost">Package cost</span><br>
					  <span><del>₹87,000/-<del></span>&nbsp;&nbsp;
					  <span>₹ <?=$related_val['market_price']?>/-</span>
					</div>
					<div>
					  <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$related_val['pack_id']?>" class="btn btn-default">View more</a>
					  <a class="btn btn-danger">Quick enquiry</a>
					</div>
				  </div>
				</div>
			  </div>
			</div>
			<div class="caption text-center">
			  <h4><?=$related_val['package_name']?></h4>
			</div>
		</div>
	<?php
	$package_count++;
			}
		}
	?>
    
    

  <!-- <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div>
  <div class="pack_card">
    <img src="http://pacetravels.org/extras/custom/TMX1512291534825461/images/5eea020495.jpg" alt="image">
      <div class="caption_card">
        <h3><a href="#" class="p_name pull-left">bangkok</a></h3>
        <p class="price pull_right">13000</p>
      </div>
  </div> -->
  
</div>


</div>
<div class="clearfix"></div>
<script type="text/javascript" src="<?php echo $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js')?>"></script>
<script>   
  $(document).ready(function() {
  	//alert("gyjhg");
     $(".owl-carousel").owlCarousel({
     	
       items:1,
       itemsDesktop: [1000, 1],
       itemsDesktopSmall: [900, 1],
       itemsTablet: [600,1],
       loop:true,
       margin:10,
       autoplay:true,
       navigation: true,

       pagination: false,
       autoplayTimeout:1000,
       autoplayHoverPause:true
   });
     $( ".owl-prev").html('<i class="fa fa-chevron-left"></i>');
 $( ".owl-next").html('<i class="fa fa-chevron-right"></i>');
  });
</script>
<script type="text/javascript">
   ! function($) {
     "use strict";
     var a = {
         accordionOn: ["xs"]
     };
     $.fn.responsiveTabs = function(e) {
         var t = $.extend({}, a, e),
             s = "";
         return $.each(t.accordionOn, function(a, e) {
             s += " accordion-" + e
         }), this.each(function() {
             var a = $(this),
                 e = a.find("> li > a"),
                 t = $(e.first().attr("href")).parent(".tab-content"),
                 i = t.children(".tab-pane");
             a.add(t).wrapAll('<div class="responsive-tabs-container" />');
             var n = a.parent(".responsive-tabs-container");
             n.addClass(s), e.each(function(a) {
                 var t = $(this),
                     s = t.attr("href"),
                     i = "",
                     n = "",
                     r = "";
                 t.parent("li").hasClass("active") && (i = " active"), 0 === a && (n = " first"), a === e.length - 1 && (r = " last"), t.clone(!1).addClass("accordion-link" + i + n + r).insertBefore(s)
             });
             var r = t.children(".accordion-link");
             e.on("click", function(a) {
                 a.preventDefault();
                 var e = $(this),
                     s = e.parent("li"),
                     n = s.siblings("li"),
                     c = e.attr("href"),
                     l = t.children('a[href="' + c + '"]');
                 s.hasClass("active") || (s.addClass("active"), n.removeClass("active"), i.removeClass("active"), $(c).addClass("active"), r.removeClass("active"), l.addClass("active"))
             }), r.on("click", function(t) {
                 t.preventDefault();
                 var s = $(this),
                     n = s.attr("href"),
                     c = a.find('li > a[href="' + n + '"]').parent("li");
                 s.hasClass("active") || (r.removeClass("active"), s.addClass("active"), i.removeClass("active"), $(n).addClass("active"), e.parent("li").removeClass("active"), c.addClass("active"))
             })
         })
     }
   }(jQuery);
   
   
   $('.responsive-tabs').responsiveTabs({
                          accordionOn: ['xs', 'sm']
                   });
  $(document).ready(function(){
	
	$(document).on('keyup','#af_tag',function(e){
		var search_val=$(this).val();
		//alert(search_val);
		$.ajax({
			
            type: "GET",
            url: "<?php echo base_url().'index.php/tours/get_holiday_package_auto_fill/' ?>",
            data: {search_val:search_val},
            success: function(result){
				var availableTags = JSON.parse(result);
				//alert(availableTags);
				$( "#af_tag" ).autocomplete({
					source: availableTags
				});
            }
        });
		
		
	});
	  
  

  $( function() {
    $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });
    var data = [
      { label: "anders", category: "" },
      { label: "andreas", category: "" },
      { label: "antal", category: "" },
      { label: "annhhx10", category: "Products" },
      { label: "annk K12", category: "Products" },
      { label: "annttop C13", category: "Products" },
      { label: "anders andersson", category: "People" },
      { label: "andreas andersson", category: "People" },
      { label: "andreas johnson", category: "People" }
    ];
 
    $( "#search" ).catcomplete({
      delay: 0,
      source: data
    });
  } );
	
	$(document).on('click','#add_rooms',function(){
		var number_room= $('.no_room').val();
		var room_count= $('.room_count').val();
		var current_room_count=parseInt(room_count)+1;
		var current_room=parseInt(number_room)+1;
		var room_text = '<ul class="list-inline"><li class="id-room"><p class="room_label">Room '+current_room_count+'</p></li><li><label>Adult</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decrease_'+current_room+'"  type="button">-</button></span><input type="number" id="number_'+current_room+'" class="form-control no-padding add-color text-center height-25 increase'+current_room+' decrease_'+current_room+'" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red btn-pluss" id="increase'+current_room+'"   type="button">+</button></span></div><small>Above 12 years</small></li>                                                                                              <li><label>Child <small>(with bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseValue_'+current_room+'" type="button">-</button></span><input type="number" id="number1_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseValue_'+current_room+' increaseValue_'+current_room+'" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red btn-pluss" id="increaseValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li>                        <li><label>Child <small>(without bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreasewoValue_'+current_room+'"  type="button">-</button></span><input type="number" id="number2_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreasewoValue_'+current_room+' increasewoValue_'+current_room+'" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red btn-pluss" id="increasewoValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li>                                                                                                                                         <li><label>Infant</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseinfValue'+current_room+'"  type="button">-</button></span><input type="number" id="number3_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseinfValue'+current_room+' increaseinfValue_'+current_room+'" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red btn-pluss" id="increaseinfValue_'+current_room+'"  type="button">+</button></span></div><small>(0-2 years)</small></li><li class="remove text-danger"><span class="fa fa-minus-circle"></span></li></ul>';
		
		$('.room_block').append(room_text);
		var number_room=parseInt(number_room)+1;
		var room_count=parseInt(room_count)+1;
		$('.no_room').val(number_room);
		$('.room_count').val(room_count);
		
		if(current_room_count>=4){
			$('#add_rooms').addClass('hide');
		}else{
			$('#add_rooms').removeClass('hide');
		}
	});
	$(document).on('click','.btn-pluss',function(e){
		e.preventDefault();
		var id=$(this).attr('id');
		var quantity = parseInt($('.'+id).val());
		//alert(quantity);
		if(quantity >= 4){
			alert("You are exeeding the limit.");
		}else{
			$('.'+id).val(quantity + 1);		
		}
	});
	$(document).on('click','.remove',function(e){
		e.preventDefault();
		$(this).parents('.list-inline').remove();

		$( ".list-inline" ).each(function( index ) {
			var room_name_count=index+1;
		  $(this).find('.room_label').text('Room '+room_name_count)
		});
		var room_count= $('.room_count').val();
		var room_count=parseInt(room_count)-1;
		$('.room_count').val(room_count);
		if(room_count>=4){
			$('#add_rooms').addClass('hide');
		}else{
			$('#add_rooms').removeClass('hide');
		}
	});
	$(document).on('click','.btn-minuse',function(e){
		e.preventDefault();
		var id=$(this).attr('id');
		var quantity = parseInt($('.'+id).val());
		//alert(quantity);
		if(quantity >= 4){
			alert("You are exeeding the limit.");
		}else{
			$('.'+id).val(quantity - 1);		
		}
	});
  });
  </script>
</script>

<style type="text/css">
	.spcl_atrct .lft_detl .mn_lst select,.spcl_atrct .lft_detl .mn_lst input.datpic {
    float: left;
    display: inline-block;
    padding: 10px 30px;
    margin: 0px 30px;
    border: 1px solid #ccc;
    background: #eee;
    border-radius: 0!important;
}
.spcl_atrct .lft_detl h4 {
    line-height: 30px;
    margin: 5px 0;
    float: left;
    font-size: 16px;
}
.spcl_atrct .lft_detl .mn_lst {
    padding: 0;
    margin: 5px 0px;
    border-bottom: 1px solid #eee;
}

.lft_detl {
    background: #FFFFFF 0% 0% no-repeat padding-box;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1607843137254902);
    border: 1px solid #A100FF;
   padding: 25px 20px !important;
}
.room_sec ul li
{
	width: 20%;
	float: left;
	display: inline-block;
}
.pck_sldr .owl-carousel .item img
{
	position: relative;
}
.img-caption {
    position: absolute;
    bottom: 0;
    padding: 20px 10px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 15px;
    background: rgb(35 34 34 / 72%);
    border: 1px solid #222222;
    color: #fff;
}
.description {
    overflow-y: scroll;
    height: 800px;
}
.ad_rm span {
    margin: 0px 2px;
    font-size: 12px;
}
.nav-tabs>li {
    float: none;
    margin-bottom: 1px;
    border-bottom: 2px solid #fff;
    padding: 10px 10px;
    text-align: center;
}
ul.nav.nav-tabs.responsive-tabs {
    float: left;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
    width: 25%;
    height: 100%;
}
.description h4 {
    color: #222;
    font-weight: 300;
    font-size: 18px;
    margin-bottom: 20px;
}
.description p {
    color: #555;
    font-size: 14px;
    /*margin-top: 10px;*/
    margin-bottom: 0!important;
}
</style>

<!-- <script type="text/javascript">
	$(document).ready(function(){
    //alert("hh");
	function increase() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value++;
  document.getElementById('number').value = value;
}

function decrease() {
  var value = parseInt(document.getElementById('number').value, 10);
  value = isNaN(value) ? 0 : value;
  value < 1 ? value = 1 : '';
  value--;
  document.getElementById('number').value = value;
}
});
</script> -->

<script type="text/javascript">
	jQuery(function(){
		var e_day="<?php echo $group_departure; ?>"; 
		//console.log(e_day);
		var enableDays=e_day.split(',');
		var lastItem = "<?php echo $last_item; ?>";
		var firstItem = "<?php echo $first_item; ?>";
		function enableAllTheseDays(date) {
			var sdate = $.datepicker.formatDate( 'd-m-yy', date)
			
			if($.inArray(sdate, enableDays) != -1) {
			
				return [true];
			}
		
			return [false];
		}
    
		$('#datepicker_dat_group').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
	})
	$(document).ready(function() {
		$( "#datepicker_dat" ).datepicker();
		//$( "#datepicker_dat_fit" ).datepicker();
	});
</script>


<!-- new css start -->
<style type="text/css">
  .row_container{
    background-color: #f2f2f2;
  }
  /*.owl-controls{
    display: none!important;
  }*/
  #id-package-details{
    background-color: #fff;
  }
  .lft_detl {
    border: none;
    padding: 20px!important;
    min-height: 450px;
    border-radius: unset!important;
  }
  .lft_detl::before {
    background: unset;
    box-shadow: unset;
    padding: 0;
    margin: 0
    border:0;
  }
  .spcl_atrct .lft_detl .mn_lst {
    border: none;
  }
  .id-package-city{
    background-color: #f2f2f2;
    margin-top: 10px;
    padding: 15px;
    padding-bottom: 5px;
    border-radius: 8px; 
  }
  .id-spcl_atrct p{
    margin-bottom: 10px;
    font-size: 14px;

  }
  .id-depart{
    padding-top: 8px;
  }
  .room_sec h4{
    /*text-align: center;*/
    color: #3c8dbc;
    /*font-size: 22px;*/
    padding: 10px;
    padding-left: 0;
    margin-left: 0;
  }
  .id-inputfield{
    border-radius: 10px!important;
    text-align: center;
  }
  .id-room{
    width: 12%!important;
  }
 .id-room p {
    margin-top: 25px;
    font-size: 14px;
    white-space: nowrap;
}
  .room_sec ul li {
    width: 22%;
    text-align: center;
  }
  .id-add-rooms{
    font-size: 16px!important;
    cursor: pointer;
    margin-top: 20px;
  }
  .id-add-rooms:hover{
    color: #3c8dbc;
  }
  small{
    color: #828181;
  }
  .id-package-btn button{
    min-height: 45px;
  }

  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  input[type=number] {
    -moz-appearance: textfield;
  }
  h3.str.text-left {
      margin: 20px 0 10px;
  }
  .input-group-btn:first-child>.btn, .input-group-btn:first-child>.btn-group {
      height: 32px;
      border: 1px solid #ccc;
      border-right: none;
  }
  .input-group-btn:last-child>.btn, .input-group-btn:last-child>.btn-group {
      height: 32px;
      border: 1px solid #ccc;
      border-left: none;
  }
  .room_sec input[type=number] {
     height: 32px;
     border: 1px solid #ccc!important;
  }
  .hldy_tab .nav-tabs>li.active>a, .hldy_tab .nav-tabs>li.active>a:focus, .hldy_tab .nav-tabs>li.active>a:hover {
    background: linear-gradient(0deg,#002042,#0a8ec1);
    font-weight: normal;
  }
  .responsive-tabs-container.accordion-xs.accordion-sm {
    border: none;
    border-radius: 0;
  }
  .responsive-tabs-container .tab-content {
    border:none;
  }
  .id-overview-row{
    padding: 10px;
    box-shadow: 2px 2px 6px #ccc;
    margin-bottom: 20px;
  }
  .id-overview-row .id-day{
    font-weight: bold;
    color: #ff0b0b;
  }
  .id-overview-row .id-meal-div{
    background-color: #ecf9ff;
    font-weight: bold;
    padding: 10px;
    font-size: 14px;
    margin-top: 5px;
  }
  .description {
     overflow-y: unset; 
    margin-left: 25%;
    height: auto;
  }
  .id-back-s{
    margin-top: 25px;
    color: #3c8dbc;
    cursor: pointer;
  }
  .id-overview-row .id-inclusion-e{
    background-color: #d9f2ff;
    padding: 10px;
  }
  .id-inc-div p{
    padding-left: 10px;
  }
  .id-inc-div ul{
    padding-left: 10px;
  }
  .table-bordered th {
    background: #f3f3f3;
    color: #000 !important;
  }
  .id-overview-row .price td, th{
    padding: 10px;
  }
  .hldy_tab .nav>li>a {
    font-weight: normal;
  }
  .modal-title{
    text-align: center;
  }
  .modal-header{
    background: linear-gradient(96deg,#002042,#0a8ec1);
    color: #fff;
  }
  .holiday_srch_input input.form-control {
     border-radius: 15 !important; 
     border: 1px solid #ccc; 
     height: 20px;
    box-shadow: unset!important;
  }
  .holiday_srch_input textarea, select {
     border-radius: 15px !important; 
    /* border: 1px solid #9d00f9; */
    box-shadow: unset!important;
  }
  .id-enquiry-btn{
    margin-top: 20px;
    height: 40px;
    width: 100%;
  }
  div#enquiry_form .modal-header .close {
    position: unset;
  }
  #optional_form ul, li{
    list-style-type: disc;
    padding: 2px;

  }
  #optional_form ul{
    margin-top: 10px;
    padding-left: 30px;
  }
  .id-head-h span{
    color: #337ab7;
    font-size: 18px!important;
  }
  .id-head-h small{
    color: #666;
    font-size: 12px!important;
  }
  .id-content-d img{
    width: 100%!important;
    height: 200px;
  }



    .id-image-d{
      margin: 0 auto;
      overflow: hidden;
    }
    .id-content-d {
      position: relative;
      margin: auto;
      overflow: hidden;
    }
    .id-selected-package-d:hover .id-content-overlay-d{
      opacity: 1;
    }
    .id-content-details-d {
      background: rgba(0,0,0,0.7)!important;
      position: absolute;
      text-align: center;
      padding-left: 1em;
      padding-right: 1em;
      width: 100%;
      height: 100%;
      opacity: 0;
      -webkit-transform: translate(-50%, -50%);
      -moz-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      -webkit-transition: all 0.3s ease-in-out 0s;
      -moz-transition: all 0.3s ease-in-out 0s;
      transition: all 0.3s ease-in-out 0s;
    }
    .id-selected-package-d:hover .id-content-details-d{
      top: 50%;
      left: 50%;
      opacity: 1;
    }
    .id-fadeIn-bottom-d{
      top: 50%;
      left: 50%;
    }
    .price_package-d span{
      color: #fff;
      font-size: 18px;
    }
    .price_package-d{
      margin-top: 60px;
    }
    .price_package-d a{
      width: 120px;
      margin-top: 10px;
    }
    .price_package-d .id-head-cost{
      color: #ffe000;
    }
    .id-selected-package-d h4{
      background: #ffc800;
      padding: 12px;
      margin: 0;
      color: #222;
      font-weight: 600;
      text-transform: capitalize;
    }
    .id-selected-package-d{
      /*border: 4px solid #ffc800;*/
      border: 4px solid #fff;
      box-shadow: 2px 2px 6px #ccc;
      margin-bottom: 10px;
    }
    .pck_img_sec{
      padding-right: 0;
    }
    .pck_img_sec .h4{
      color: #777;
      text-align: center;
      /*font-weight: bold;*/
      font-size: 22px;
    }
    .id-cancel-div ul, li{
      padding-left: 10px;
    }
    .id-download-itenirary{
      padding: 5px 0 5px 0; 
      margin-bottom: 10px;
    }
    .id-download-itenirary span{
      color: #337ab7;
      cursor: pointer;
    }
    .id-cancel-div ul{
      padding-left: 20px!important;
    }
    .id-cancel-div li{
      padding: 0!important
    }
    .id-inc-div ul{
      padding-left: 20px!important;
    }
    .id-inc-div li{
      padding: 0!important
    }
    .owl-theme .owl-controls .owl-buttons div {
    color: #FFF;
    display: table;
    zoom: 1;
    margin: 5px;
    padding: 3px 10px;
    font-size: 24px;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
    background: transparent;
    filter: Alpha(Opacity = 50);
    opacity: 0.5;
}
.owl-next {
    position: absolute;
    right: 0;
    top: 50%;
}
.owl-prev {
    position: absolute;
    left: 0;
    top: 50%;
}
  li.remove.text-danger {
   float: right;
   margin-top: -47px;
   margin-right: -68px;
   z-index: 10000;
   color: #d9534f;
   }
   .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
    border: 1px solid #e0cfc2;
    background: #f4f0ec url(images/ui-bg_highlight-hard_100_f4f0ec_1x100.png) 50% 50% repeat-x;
    font-weight: bold;
    color: #fff;
    font-size: 18px;
    background: #2196f3 !important;
    border-radius: 50%;
}
</style>