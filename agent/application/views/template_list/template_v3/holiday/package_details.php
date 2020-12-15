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
	$photos=array_reverse($photos);
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
	<?php 
		if($prev_page=='main_page'){
	?>	
	 <a href="<?php echo base_url()?>menu/dashboard/package?default_view=VHCID1433498322" class="text-right id-back-s"><i class="fa fa-chevron-left"></i>Back</a>
	<?php	
		}else{
			$prev_arr = explode('-',$prev_page);
			if($prev_arr[0]=="country"){
			?>
				<a href="<?php echo base_url()?>tours/holiday_country_package_list/<?=$prev_arr[1]?>" class="text-right id-back-s"><i class="fa fa-chevron-left"></i>Back</a>
			<?php				
			}else{
			?>
				<a href="<?php echo base_url()?>tours/holiday_city_package_list/<?=$prev_arr[1]?>" class="text-right id-back-s"><i class="fa fa-chevron-left"></i>Back</a>
			<?php
			}
			?>
	<?php
		}
	?>
  </div>
</div>
<div class="org_row">
  
  

   <div class="col-md-6 padfive pck_sldr">

        <div class="owl-carousel owl-theme">
        <?php 
		
		foreach($image_array as $pic_key => $pic){ ?>
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
		//debug($b2b_tour_price);
			foreach ($b2b_tour_price as $tour_price_fly) {
				$per_person_price[$tour_price_fly['occupancy']]=$tour_price_fly['netprice_price']+$markup_val[0]['value'];
				$per_person_market_price[$tour_price_fly['occupancy']]=$tour_price_fly['market_price'];
				echo "<input type='hidden' class='occ_".$tour_price_fly['occupancy']."' value='".$tour_price_fly['occupancy']."' name='occ_".$tour_price_fly['occupancy']."'>";
			} 
			
			
		?> 
      <div class="col-xs-12 lft_detl">
         <div class="col-xs-12 mn_lst nopad text-right">
            <div class="col-xs-4 nopad">
				<p class="id-pac-label">Tour Code : <strong><?=$package_details[0]['tour_code']?></strong></p>
			</div>
			<div class="col-xs-8 nopad">
              <p class="id-pac-price"><del>   &#8377; <?=number_format($per_person_market_price[10],0)?></del><span class="id-price">  &#8377; <?=number_format($per_person_price[10],0)?></span> <small>(Price per person)</small></p>
              <!-- <p><strong><?=$package_price_details[0]['currency']?>:<?=$package_price_details[0]['airliner_price']?>/- &nbsp; 5,999</strong> <small>(Price per person)</small></p> -->
            </div>
            <!--<div class="col-xs-7 nopad">
              <p><i class="far fa-clock"></i> &nbsp; <strong> Duration : </strong> &nbsp;<?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
    
               
            </div>
            <div class="col-xs-5 nopad">
              <p><strong><del><?=$package_price_details[0]['currency']?>: &nbsp; <?=number_format($per_person_market_price[10],2)?>/-</del><br/><?=$package_price_details[0]['currency']?>: &nbsp; <?=number_format($per_person_price[10],2)?>/-</strong><small>(Price per person)</small></p>
            </div>-->
         </div>
         <div class="col-xs-12 mn_lst nopad">
          <div class="row">
			<div class="col-xs-5 nopad" style="margin-top: 8px;">
                <p><strong>Duration : </strong>&nbsp;<?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
            </div>
            <div class="col-sm-3 nopad">
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
					$date1 =  date("Y-m-d");
					foreach($from_range as $d_key => $d_val){
						$start_date =$d_val;
						$end_date = $to_range[$d_key];
						while (strtotime($start_date) <= strtotime($end_date)) {
							$date2 = date('Y-m-d', strtotime($start_date));
							if ($date1 > $date2) {
								//echo "less than today".$date2."<br>";
							}else{
								//echo "more than today";
								$group_departure[]=date('j-n-Y', strtotime($start_date));
							}
							
							$start_date = date ("Y-m-d", strtotime("+1 days",strtotime($start_date)));
						}
					}
					if(!empty($group_departure)){
						
						$last_item=end($group_departure);
						$last_item=date('Y-n-j', strtotime($last_item));
						$first_item=date('Y-n-j', strtotime($group_departure[0]));
						$group_departure=implode(',',$group_departure); 
					}else{
						$last_item=date('Y-n-j');
						$first_item=date('Y-n-j');
						$group_departure=date('Y-n-j');
					}
			?>
					<input  id="datepicker_dat_group" type="text" class="form-control id-inputfield" value="dd/mm/yyyy" readonly>
			<?php 
				}else{
					$group_departure=array();
					$date1 =  date("Y-m-d");
					foreach($dep_dates as $dep_key => $dep_val){ 
						$date2 = date('Y-m-d', strtotime($dep_val['dep_date']));
						if ($date1 > $date2) {
								//echo "less than today".$date2."<br>";
						}else{
								//echo "more than today";
							$group_departure[]=date('j-n-Y', strtotime($dep_val['dep_date']));
						}
						
					}
					if(!empty($group_departure)){
						$last_item=end($group_departure);
						$last_item=date('Y-n-j', strtotime($last_item));
						
						$first_item=date('Y-n-j', strtotime($group_departure[0]));
						$group_departure=implode(',',$group_departure); 
					}else{
						$last_item=date('Y-n-j');
						$first_item=date('Y-n-j');
						$group_departure=date('Y-n-j');
					}
			?>
				<input  id="datepicker_dat_group" type="text" class="form-control id-inputfield" value="dd/mm/yyyy" readonly>
			<?php
				} 
			?>
            </div>
            
            
             

          </div>
          
      </div>

         <div class="col-sm-12 id-package-city">
			<div class="row">
        <div class="col-sm-7 padl">
				<p><strong>Package Type :</strong><?php if($package_details[0]['package_type'] == "fit"){ echo "Customize Package"; }else{ echo "Group Package";}?></p>
            </div>
         <div class="col-sm-5 padl">
            <!--<div class="col-sm-6 nopad">
              <p><strong>Tour Code :</strong><?=$package_details[0]['tour_code']?></p>
              <p><strong>Country :</strong><?=$country?></p>
            </div>-->
           <!-- <div class="col-sm-6 nopad">-->
              <p><strong>Holiday Type :</strong>
                <?php 
                  $types=array();
                  foreach($tour_types as $type_val){
                     $types[]=$type_val['tour_type_name'];
                  } 
                  echo implode(',',$types);
                ?> </p>
            </div>
          </div>
			<div class="row">
				<div class="col-sm-7 padl">
				  <p><strong>City : </strong><span><?=$city?></span></p>
				  
				</div>
				<div class="col-sm-5 padl">
				  <p><strong>Country : </strong><?=$country?></p>
				  
					
				</div>
			  </div>
			
         </div>
         <?php 
			$package_type=$package_details[0]['package_type'];
		 ?>

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
                     <button class="btn btn-white btn-minuse adult" id="decrease_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number" class="form-control no-padding add-color text-center height-25 decrease_1 increase1 adult_count" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red adult btn-pluss" id="increase1"   type="button">+</button>
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
                     <input type="number" id="number1" class="form-control no-padding add-color text-center height-25 decreaseValue_1 increaseValue_1 child_wb_count" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red child_wb btn-pluss" id="increaseValue_1"  type="button">+</button>
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
                     <input type="number" id="number2" class="form-control no-padding add-color text-center height-25 decreasewoValue_1 increasewoValue_1 child_wob_count" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red child_wob btn-pluss" id="increasewoValue_1"  type="button">+</button>
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
                     <input type="number" id="number3" class="form-control no-padding add-color text-center height-25 decreaseinfValue_1 increaseinfValue_1 infant_count" readonly  maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red infant btn-pluss" id="increaseinfValue_1"  type="button">+</button>
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
                <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#enquiry_form_out">Enquiry</button>
            </div>
			<div class="col-sm-6 padfive">
				<button type="button" class="btn btn-danger form-control book_tour" data-toggle="modal" data-target="#price_form">Book Now</button>
			</div>
			  <?php// if(!empty($optional_tour_details)) {?>
				<!--  <div class="col-sm-6 padfive">
					<button type="button" class="btn btn-danger form-control option_tour_form" data-toggle="modal" data-target="#optional_form">Choose Optional Service</button>
				  </div>-->
			  <?php /*} else {
				  ?>
				  <div class="col-sm-6 padfive">
					<button type="button" class="btn btn-danger form-control book_tour">Book</button>
				  </div>
				  <?php 
			  }*/?>
            </div>

          </div>



         </div>

         
     <!-- Modal enquriy form out -->
      <div class="modal fade" id="enquiry_form_out" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
            <div class="container-fluid id-enquiry-modal nopad">
            <input type="hidden"  name="pack_id"  value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  value="<?=$package_details[0]['package_name']?>">
			<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
			<div class="form-group hide">
				<label>Package Code</label>
				<input type="text"  name="pack_code" class="form-control pack_code" value="<?=$package_details[0]['tour_code']?>" placeholder="Enter Name" maxlength="30" required readonly>
			</div>
             <div class="form-group hide">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" value="<?=$this->entity_firstname?>" maxlength="30" required readonly>
             </div>
             <div class="form-group hide">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" value="<?=$this->entity_email?>" maxlength="45" required readonly>
             </div>
             <div class="form-group hide">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" value="<?=$this->entity_phone?>" maxlength="12" required readonly>
             </div>
			 <div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong><?=$package_details[0]['tour_code']?></strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p><?=$this->entity_firstname?></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p><?=$this->entity_email?></p>
                  </div>
                  
              </div>
			  <div class="row">
				<div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required>
				  </div>
				<div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					<input  id="enquiry_datepicker_outss" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_out" value="dd/mm/yyyy">
				</div>
            </div>
			<div class="row">
			  <div class="col-sm-12 padfive">
				<label class="id-label">Messenger</label>
				<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
			  </div>
			</div>
			<div class="row">
                <div class="col-sm-offset-8 col-sm-4 nopad">
                      <button type="submit" class="btn btn-danger form-control id-enquiry-btn">Send Enquiry</button>
                </div>
            </div>
           
             
           
          
          </div>
          
          </form>
           </div>
        </div>
        </div>
      </div>


<!-- enquir form in -->
      <div class="modal fade" id="enquiry_form_in" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
            <div class="container-fluid id-enquiry-modal nopad">
            <input type="hidden"  name="pack_id"  value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  value="<?=$package_details[0]['package_name']?>">
			<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
			<div class="form-group hide">
				<label>Package Code</label>
				<input type="text"  name="pack_code" class="form-control pack_code" value="<?=$package_details[0]['tour_code']?>" placeholder="Enter Name" maxlength="30" required readonly>
			</div>
             <div class="form-group hide">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" value="<?=$this->entity_firstname?>" maxlength="30" required readonly>
             </div>
             <div class="form-group hide">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" value="<?=$this->entity_email?>" maxlength="45" required readonly>
             </div>
             <div class="form-group hide">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" value="<?=$this->entity_phone?>" maxlength="12" required readonly>
             </div>
             <!--<div class="form-group">
              <label>No. of Passengers</label>
              <input type="number"  name="passenger" class="form-control" placeholder="Enter No Of Guests" required>
             </div>
             <div class="form-group">
              <label>Departure Date</label>
              <input  id="enquiry_datepicker_outss"  name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_in" value="dd/mm/yyyy">
             </div>
             <div class="form-group">
              <label>Messenger</label>
              <textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
             </div>-->
			  <div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong><?=$package_details[0]['tour_code']?></strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p><?=$this->entity_firstname?></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p><?=$this->entity_email?></p>
                  </div>
                  
              </div>
            <div class="row">
				<div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required>
				  </div>
				<div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					<input  id="enquiry_datepicker_outss" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_in" value="dd/mm/yyyy">
				</div>
            </div>
			<div class="row">
			  <div class="col-sm-12 padfive">
				<label class="id-label">Messenger</label>
				<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
			  </div>
			</div>
			<div class="row">
                <div class="col-sm-offset-8 col-sm-4 nopad">
                    <button type="submit" class="btn btn-danger form-control id-enquiry-btn">Send Enquiry</button>
                </div>
            </div>
          
          </div>
          
          </form>
           </div>
        </div>
        </div>
      </div>

   <!------ enquiry form for related package ------------->

	
    <div class="modal fade" id="enquiry_form_rel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
            <div class="container-fluid id-enquiry-modal nopad">
            <input type="hidden"  name="pack_id"  class="pack_id" value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  class="pack_name" value="<?=$package_details[0]['package_name']?>">
			<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
			<div class="form-group hide">
				<label>Package Code</label>
				<input type="text"  name="pack_code" class="form-control pack_code" value="<?=$package_details[0]['tour_code']?>" placeholder="Enter Name" maxlength="30" required readonly>
			</div>
             <div class="form-group hide">
              <label>Name</label>
              <input type="text"  name="name" class="form-control" placeholder="Enter Name" value="<?=$this->entity_firstname?>" maxlength="30" required readonly>
             </div>
             <div class="form-group hide">
              <label>Email</label>
              <input type="Email"  name="Email" class="form-control" placeholder="Enter Email" value="<?=$this->entity_email?>" maxlength="45" required readonly>
             </div>
             <div class="form-group hide">
              <label>Phone</label>
              <input type="text"  name="phone" class="form-control" placeholder="Enter Phone" value="<?=$this->entity_phone?>" maxlength="12" required readonly>
             </div>
			 <div class="row id-body-div">
                  <div class="col-sm-4">
                    <label class="id-label">Package Code</label><p><strong class="pack_id_text"><?=$package_details[0]['tour_code']?></strong></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Agent Name</label><p><?=$this->entity_firstname?></p>
                  </div>
                  <div class="col-sm-4">
                    <label class="id-label">Email</label><p><?=$this->entity_email?></p>
                  </div>
                  
              </div>
         
            <div class="row">
				<div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required>
				  </div>
				<div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					<input  id="enquiry_datepicker_outss" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_rel" value="dd/mm/yyyy">
				</div>
            </div>
			<div class="row">
			  <div class="col-sm-12 padfive">
				<label class="id-label">Messenger</label>
				<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
			  </div>
			</div>
           
          <div class="row">
                <div class="col-sm-offset-8 col-sm-4 nopad">
                    <button type="submit" class="btn btn-danger form-control id-enquiry-btn">Send Enquiry</button>
                </div>
            </div>
          
          </div>
          </form>
           </div>
        </div>
        </div>
    </div>

		  
<div class="container id-mt-3 modal fade" class="" id="optional_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="col-md-12">
		<div class="id-main-optional-div">
			<div class="row text-right"><p class="id-back-to">< Back to Package</p></div>
			<h1><?=$package_details[0]['package_name']?> (<?=$package_details[0]['tour_code']?>)</h1>
			<form action="<?php echo base_url();?>index.php/tours/fare_breakup_details" method="post" id="send_booking">
			
			<div class="hide">
				<input type="hidden" class="pack_id" name="pack_id" value="<?=$package_details[0]['id']?>">
				<input type="hidden" class="sel_departure_date" name="sel_departure_date" value="">
				<input type="hidden" class="sel_adult_count"  name="sel_adult_count" value="">
				<input type="hidden" class="sel_child_wb_count" name="sel_child_wb_count" value="">
				<input type="hidden" class="sel_child_wob_count" name="sel_child_wob_count" value="">
				<input type="hidden" class="sel_infant_count" name="sel_infant_count" value="">
				<input type="hidden" class="sel_room_count" name="sel_room_count" value="">
				<input type="hidden" class="agent_markup" name="agent_markup" value="<?=$markup_val[0]['value']?>">
			</div>
			
			
			
			
			
			
			
			
			
			
			
			<div class="row id-dest-o">
				<p><span><i class="fa fa-clock" aria-hidden="true"></i>&nbsp; Duration :</span> <?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
				<p><span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Destination :</span><?=$city?></p>
				<p><span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; Departure Date :</span> <span class="sel_dep_date"> 31/08/2020</span></p>
			</div>
			<div class="row id-optional-div text-center">
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-bed" aria-hidden="true"></i> Room : </span><span class="total_room_count">1</span></p></div>
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-male" aria-hidden="true"></i> Adult : </span><span class="total_adult_count">2</span></p></div>
				<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (With Bed)</small> :</span><span class="total_child_wb_count"> 0</span></p></div>
				<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (Without Bed)</small> :</span><span class="total_child_wob_count"> 0</span></p></div>
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-child" style="font-size: 0.8em" aria-hidden="true"></i> Infant : </span><span class="total_infant_count"> 0</span></p></div>
			</div>
			<?php if(!empty($optional_tour_details)) { ?>
			<div class="row id-optional-table">
				<h3>Optional Services</h3>
				
				
				
					
					<?php 
					//error_reporting(E_ALL);
					$city_wise_opt_tour=array();
					foreach($optional_tour_details as $opt_key => $opt_val){
						$city_wise_opt_tour[$opt_val['city_name']][$opt_key]=$opt_val;
					}
					//debug($city_wise_opt_tour); 
					foreach($city_wise_opt_tour as $opt_key => $copt_val){
						
					?>
					<table class="table">
					<tr>
						<th style="text-align:left;"><?=$opt_key?></th>
						<th>Price Per Adult</th>
						<th>Price Per Child</th>
						<th>Price Per Infant</th>
					</tr>
					<?php 
					foreach($copt_val as $copt_key => $opt_val){
					?>
					<tr>
						<td style="text-align:left;">
							<label for="op3">
							<input type="checkbox" id="op3" name="sel_opt_tour[]" value="<?=$opt_val['opt_id']?>" class="id-optional-check">
	  						<span><?=$opt_val['tour_name']?></span></label>
						</td>
						<td>INR <?=$opt_val['adult_price']?></td>
						<td>INR <?=$opt_val['child_price']?></td>
						<td>INR <?=$opt_val['infant_price']?></td>
					</tr>
					
					<?php } 
					?>
					</table>
					<?php
					} ?>
				
				
			</div>
			<?php } ?>
			<div class="row id-optional-btn">
				<div class="col-sm-offset-5 col-sm-2 nopad">
					<button class="btn btn-danger form-control">Continue &nbsp;&nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
    <div class="modal fade" id="price_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Package Price Details</h4>
			  </div>
			  <div class="modal-body">
				<p class="id-head-h"><span><?=$package_details[0]['package_name']?></span> &nbsp; <small><span class="sel_dep_date"> 31/08/2020</span> - <?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></small></p> 
			<form action="<?php echo base_url();?>index.php/tours/optional_tour_details/<?=$package_details[0]['id']?>" method="post" id="send_booking">	
				
				<div class="hide">
					<input type="hidden" class="pack_id" name="pack_id" value="<?=$package_details[0]['id']?>">
					<input type="hidden" class="sel_departure_date" name="sel_departure_date" value="">
					<input type="hidden" class="sel_adult_count"  name="sel_adult_count" value="">
					<input type="hidden" class="sel_child_wb_count" name="sel_child_wb_count" value="">
					<input type="hidden" class="sel_child_wob_count" name="sel_child_wob_count" value="">
					<input type="hidden" class="sel_infant_count" name="sel_infant_count" value="">
					<input type="hidden" class="sel_room_count" name="sel_room_count" value="">
					<input type="hidden" class="agent_markup" name="agent_markup" value="<?=$markup_val[0]['value']?>">
					<input type="hidden" class="prev_page" name="prev_page" value="<?=$prev_page?>">
				</div>
			
				<table class="tabel table-bordered" style="margin-right: auto;margin-left: auto;width: 100%;text-align: center;">
					<tr>
						<th>Adult on single sharing</th>
						<th>Adult on twin sharing</th>
						<th>Triple Sharing</th>
						<th>Child with bed</th>
						<th>Child without bed</th>
						<th>Infant</th>
					</tr>
					<tr>
						<td><del>₹ <?php echo isset($per_person_market_price[8])?number_format($per_person_market_price[8],2) : "-NA-"; ?></del></td>
						<td><del>₹ <?php echo isset($per_person_market_price[10])?number_format($per_person_market_price[10],2) : "-NA-"; ?></del></td>
						<td><del>₹ <?php echo isset($per_person_market_price[14])?number_format($per_person_market_price[14],2) : "-NA-";?></del></td>
						<td><del>₹ <?php echo isset($per_person_market_price[11])?number_format($per_person_market_price[11],2) : "-NA-";?></del></td>
						<td><del>₹ <?php echo isset($per_person_market_price[12])?number_format($per_person_market_price[12],2) : "-NA-";?></del></td>
						<td><del>₹ <?php echo isset($per_person_market_price[13])?number_format($per_person_market_price[13],2) : "-NA-";?></del></td>
					</tr>
					<tr>
						<td>₹ <?php echo isset($per_person_price[8])?number_format($per_person_price[8],2) : "-NA-"; ?></td>
						<td>₹ <?php echo isset($per_person_price[10])?number_format($per_person_price[10],2) : "-NA-"; ?></td>
						<td>₹ <?php echo isset($per_person_price[14])?number_format($per_person_price[14],2) : "-NA-";?></td>
						<td>₹ <?php echo isset($per_person_price[11])?number_format($per_person_price[11],2) : "-NA-";?></td>
						<td>₹ <?php echo isset($per_person_price[12])?number_format($per_person_price[12],2) : "-NA-";?></td>
						<td>₹ <?php echo isset($per_person_price[13])?number_format($per_person_price[13],2) : "-NA-";?></td>
					</tr>
							
				  </table>
				  <ul>
					<?php echo $package_details[0]['terms']; ?>
				  </ul>
				  <hr style="margin: 5px;">
				  <div class="row" style="display: flex;margin:0 auto;"><button type="button" data-dismiss="modal" class="btn btn-primary" data-toggle="modal" data-target="#enquiry_form_in">Enquiry</button>
            <button type="submit" class="btn btn-danger id-enquiry-btn">Optional Tours</button>
					</div>
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
        <li><a href="#optional_tours">Optional Tours</a></li>
			<li><a href="#cancel">Cancellation Policy</a></li>
			<li><a href="#t_c">T&C</a></li>
			<li><a href="#documentation">Documentation</a></li>
			<li><a href="#payment_policy">Payment Policy</a></li>
		</ul>
      <div class="tab-content description" id="pck_scroll">

         <div class="tab-pane active" id="home1">
              <div class="id-download-itenirary text-right">
                <span>
					<a data-toggle="collapse" data-target="#emailmodel" aria-expanded="false"><i class="fa fa-envelope" aria-hidden="true"></i> &nbsp;Email Itinerary</a></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span><a href="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$package_details[0]['id'].'/show_broucher';?>" ><i class="fa fa-print" aria-hidden="true"></i> &nbsp;Print Itinerary</a></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span><a href="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$package_details[0]['id'].'/show_download_pf';?>" ><i class="fa fa-download" aria-hidden="true"></i>&nbsp; Download Itinerary</a></span>
              </div>
				<div class="collapse" id="emailmodel">
				  <div class="well max_wd20">
					<h4>Send Email</h4>
					<form name="agent_email" method="post" action="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$package_details[0]['id'].'/show_broucher/mail';?>">
					  <input id="inc_sddress" value="1" type="hidden" name="inc_sddress">
					  <input id="inc_fare" value="1" type="hidden" name="inc_fare">
					  <div>
						<label>Email Id </label><input id="email" placeholder="Please Enter Email Id" class="airlinecheckbox validate_user_register form-control" type="text" checked name="email">
            <br>
            <label>Email Body </label><input id="email_body" placeholder="Please Enter Email Body" class="airlinecheckbox validate_user_register form-control" type="text" checked name="email_body">
					  </div>
					   
					  <div class="modal-footer">
						<button type="submit" class="btn btn-primary" value="Submit">Send Email</button>
					  </div>
					</form>
				  </div>
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
					<table class="table table-bordered" style="margin-right: auto;margin-left: auto;width: 100%;text-align: center;">
						<?php 
							foreach ($b2b_tour_price as $tour_price_fly) { 
								$occ=$tour_price_fly['occupancy'];
								$query_x = "select * from occupancy_managment where id='$occ'"; 
								$exe   = $this->db->query ( $query_x )->result_array ();
								$fetch_x = $exe[0];
      					?>
      									
							<tr>
								<th><?=$fetch_x['occupancy_name']?></th>
								<td><del>₹ <?=number_format($tour_price_fly['market_price'],2)?></del>  <br/> ₹ <?=number_format($tour_price_fly['netprice_price']+$markup_val[0]['value'],2)?></td>
							</tr>
      					<?php } ?> 
					</table>
				</ul>
			</div>
		</div>
    <div class="tab-pane" id="optional_tours">
      <div class="id-overview-row"> 
        <h4 class="id-inclusion-e">Optional Services</h4>
        
		<?php
			if(!empty($optional_tour_details)) {
			foreach($city_wise_opt_tour as $opt_key => $copt_val){
		?>
			<table class="table">
			<tr>
				<th style="text-align:left;"><?=$opt_key?></th>
				<th>Price Per Adult</th>
				<th>Price Per Child</th>
				<th>Price Per Infant</th>
			</tr>
			<?php 
			foreach($copt_val as $copt_key => $opt_val){
			?>
			<tr>
				<td style="text-align:left;">
					<label for="op3">
					<span><?=$opt_val['tour_name']?></span></label>
				</td>
				<td>INR <?=$opt_val['adult_price']?></td>
				<td>INR <?=$opt_val['child_price']?></td>
				<td>INR <?=$opt_val['infant_price']?></td>
			</tr>
			
			<?php } 
			?>
			</table>
		<?php
		} 
		
			}else{ ?>
			<table class="table">
			<tr>
				<th>No Optional Services Available</th>
			</tr>
			</table>
			<?php } ?>
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
		foreach($related_packages as $related_val){
			
			if($package_count>=5){
				break 1;
			}
	?>
		<div class="id-selected-package-d">
			<div class="id-image-d">
			  <div class="id-content-d">
				<div class="id-content-overlay-d"></div>
				<img src="<?php echo $GLOBALS['CI']->template->domain_images($related_val['pack_banner'])?>" alt="image">
				<div class="id-content-details-d id-fadeIn-bottom-d">
				  <div class="price_package-d">
					<div>
					  <span class="id-head-cost">Package cost</span><br>
					  <span><del>₹ <?=number_format($related_val['market_price'],0)?>/-<del></span>&nbsp;&nbsp;
					  <span>₹ <?=number_format($related_val['netprice_price'],0)?>/-</span>
					</div>
					<div class="btns">
					  <a href="<?php echo base_url().'index.php/tours/holiday_package_detail/'.$related_val['pack_id']?>" class="btn btn-default">View more</a>
					  <a class="btn btn-danger rel_enq" data-pack_name="<?=$related_val['package_name']?>" data-pack_id="<?=$related_val['pack_id']?>" data-pack_code="<?=$related_val['tour_code']?>" data-toggle="modal" data-target="#enquiry_form_rel">Quick enquiry</a>
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
	  
	var single_share		=$('.occ_8').val();
	var doubl_share			=$('.occ_10').val();
	var triple_share		=$('.occ_14').val();
	var child_with_bed		=$('.occ_11').val();
	var child_without_bed	=$('.occ_12').val();
	var infant				=$('.occ_13').val();
	
	
	
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
		var room_text = '<ul class="list-inline"><li class="id-room"><p class="room_label">Room '+current_room_count+'</p></li><li><label>Adult</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse adult" id="decrease_'+current_room+'"  type="button">-</button></span><input type="number" id="number_'+current_room+'" readonly class="form-control no-padding add-color text-center height-25 increase'+current_room+' decrease_'+current_room+' adult_count" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red adult btn-pluss" id="increase'+current_room+'"   type="button">+</button></span></div><small>Above 12 years</small></li>                                                                                              <li><label>Child <small>(with bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseValue_'+current_room+'" type="button">-</button></span><input type="number" readonly id="number1_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseValue_'+current_room+' increaseValue_'+current_room+' child_wb_count" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red child_wb btn-pluss" id="increaseValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li>                        <li><label>Child <small>(without bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreasewoValue_'+current_room+'"  type="button">-</button></span><input type="number" readonly id="number2_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreasewoValue_'+current_room+' increasewoValue_'+current_room+' child_wob_count" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red child_wob btn-pluss" id="increasewoValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li>                                                                                                                                         <li><label>Infant</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseinfValue'+current_room+'"  type="button">-</button></span><input type="number" readonly id="number3_'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseinfValue'+current_room+' increaseinfValue_'+current_room+' infant_count" maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red infant btn-pluss" id="increaseinfValue_'+current_room+'"  type="button">+</button></span></div><small>(0-2 years)</small></li><li class="remove text-danger"><span class="fa fa-minus-circle"></span></li></ul>';
		
		$('.room_block').append(room_text);
		var number_room=parseInt(number_room)+1;
		var room_count=parseInt(room_count)+1;
		$('.no_room').val(number_room);
		$('.room_count').val(room_count);
		$('.total_room_count').text(room_count);
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
		
		var cwb=$(this).parents('.list-inline').find('.child_wb_count').val();
		var cwob=$(this).parents('.list-inline').find('.child_wob_count').val();
		var adlt=$(this).parents('.list-inline').find('.adult_count').val();
		var infant=$(this).parents('.list-inline').find('.infant_count').val();
		var child=parseInt(cwb)+parseInt(cwob);
		var total_per_room = parseInt(child)+parseInt(adlt)+parseInt(infant);
		if($(this).hasClass('adult')==true){
			var pack_type= "<?php echo $package_type; ?>";
			if(total_per_room >= 4 || adlt >= 3){
				alert("You are exeeding the limit.");
			}else{
				if(pack_type=='fit' && adlt==0){
					$('.'+id).val(quantity + 2);		
				}else{
					$('.'+id).val(quantity + 1);
				}
			}
		}
		
		if($(this).hasClass('child_wb')==true){
			if(total_per_room >= 4 || child >= 2){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
		}
		if($(this).hasClass('child_wob')==true){
			if(total_per_room >= 4 || child >= 2){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
		}
		if($(this).hasClass('infant')==true){
			if(total_per_room >= 4 || infant >= 1){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
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
		$('.total_room_count').text(room_count);
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
		var pack_type= "<?php echo $package_type; ?>";
		var adlt=$(this).parents('.list-inline').find('.adult_count').val();
		//alert(quantity);
		if(quantity <= 0){  
			
		}else{
			
			if($(this).hasClass('adult')==true){
				if(pack_type=='fit' && adlt==2){
					$('.'+id).val(quantity - 2);		
				}else{
					$('.'+id).val(quantity - 1);
				}
			}else{
				$('.'+id).val(quantity - 1);		
			}
			//$('.'+id).val(quantity - 1);	
			/*if(pack_type=='fit' && adlt==2){
				$('.'+id).val(quantity - 2);		
			}else{
				$('.'+id).val(quantity - 1);
			}		*/	
		}
	});
	$('#price_form').on('show.bs.modal', function (e) {
		
		var total_adult_count = 0;
		var total_child_wb_count = 0;
		var total_child_wob_count = 0;
		var total_infant_count = 0;
		var is_no_adult=0;
		
		var adult_text='';
		var cwb_text='';
		var cwob_text='';
		var infant_text='';
		var err_text=''
		$('.list-inline').each(function( index ) {
			if($(this).find('.adult_count').val() <=0){
				is_no_adult=1;
			}
			var cur_adult		=parseInt($(this).find('.adult_count').val());
			var cur_child_wb	=parseInt($(this).find('.child_wb_count').val());
			var cur_child_wob	=parseInt($(this).find('.child_wob_count').val());
			var cur_infant		=parseInt($(this).find('.infant_count').val());
			
			if(cur_adult=='1' && typeof single_share =="undefined"){
				err_text=err_text+"Single share not availabe for this package.\n";
			}else if(cur_adult=='2' && typeof doubl_share =="undefined"){
				err_text=err_text+"Double share not availabe for this package.\n";
			}else if(cur_adult=='3' && typeof triple_share =="undefined"){
				err_text=err_text+"Thriple share not availabe for this package.\n";
			}

			if(cur_child_wb!=0 && typeof child_with_bed =="undefined"){
				err_text=err_text+"Child with bed not availabe for this package.\n";
			}
			if(cur_child_wob!=0 && typeof child_without_bed =="undefined"){
				err_text=err_text+"Child with out bed not availabe for this package.\n";
			}
			if(cur_infant!=0 && typeof infant =="undefined"){
				err_text=err_text+"Infant not availabe for this package.\n";
			}
			
			
			total_adult_count+=parseInt($(this).find('.adult_count').val());
			total_child_wb_count+=parseInt($(this).find('.child_wb_count').val());
			total_child_wob_count+=parseInt($(this).find('.child_wob_count').val());
			total_infant_count+=parseInt($(this).find('.infant_count').val());
			
			
			adult_text	= adult_text+'|'+parseInt($(this).find('.adult_count').val());
			cwb_text	= cwb_text+'|'+parseInt($(this).find('.child_wb_count').val());
			cwob_text	= cwob_text+'|'+parseInt($(this).find('.child_wob_count').val());
			infant_text	= infant_text+'|'+parseInt($(this).find('.infant_count').val());
		});
		
		var sel_dep_date=$('#datepicker_dat_group').val();
		if(sel_dep_date=='dd/mm/yyyy'){
			alert("Please select departure date.");
			e.preventDefault();
		}else if(err_text !=''){
			err_text=err_text+" For further details kindly contact customer care.";
			alert(err_text);
			e.preventDefault();
		}
		var sel_room_count = $('.room_count').val();
		$('.sel_dep_date').text(sel_dep_date);
		$('.total_adult_count').text(total_adult_count);
		$('.total_child_wb_count').text(total_child_wb_count);
		$('.total_child_wob_count').text(total_child_wob_count);
		$('.total_infant_count').text(total_infant_count);
		$('.total_infant_count').text(total_infant_count);
		
		$('.sel_departure_date').val(sel_dep_date);
		$('.sel_adult_count').val(adult_text);
		$('.sel_child_wb_count').val(cwb_text);
		$('.sel_child_wob_count').val(cwob_text);
		$('.sel_infant_count').val(infant_text);
		$('.sel_room_count').val(sel_room_count);
		
		
		
		
		
		
		if(is_no_adult ==1){
			alert("Please select atleast one adult per room.");
			e.preventDefault();
		}
	});
	$(document).on('click','.option_dsastour_form',function(e){
		e.preventDefault();
		var total_adult_count = 0;
		var total_child_wb_count = 0;
		var total_child_wob_count = 0;
		var total_infant_count = 0;
		$('.list-inline').each(function( index ) {
			if($(this).find('.adult_count').val() <=0){
				alert("Please select atleast one adult per room.");
				return false;
				
			}
			total_adult_count+=parseInt($(this).find('.adult_count').val());
			total_child_wb_count+=parseInt($(this).find('.child_wb_count').val());
			total_child_wob_count+=parseInt($(this).find('.child_wob_count').val());
			total_infant_count+=parseInt($(this).find('.infant_count').val());
		});
		//alert(total_adult_count);
		$('.total_adult_count').text(total_adult_count);
		$('.total_child_wb_count').text(total_child_wb_count);
		$('.total_child_wob_count').text(total_child_wob_count);
		$('.total_infant_count').text(total_infant_count);
		
	});
	$( "#enquiry_form_in" ).submit(function(e) {
	  var sel_dep_date=$('.enquiry_datepicker_in').val();
		if(sel_dep_date==''){
			alert("Please select departure date.");
			e.preventDefault();
		}
	});
	$( "#enquiry_form_out" ).submit(function(e) {
	  var sel_dep_date=$('.enquiry_datepicker_out').val();
	  //alert(sel_dep_date);
		if(sel_dep_date==''){
			alert("Please select departure date.");
			e.preventDefault();
		}
	});
	$( "#enquiry_form_rel" ).submit(function(e) {
	  var sel_dep_date=$('.enquiry_datepicker_rel').val();
	  //alert(sel_dep_date);
		if(sel_dep_date==''){
			alert("Please select departure date.");
			e.preventDefault();
		}
	});
	$(document).on('click','.rel_enq',function() {
		var sel_pack_id=$(this).data('pack_id');
		var sel_pack_name=$(this).data('pack_name');
		var sel_pack_code=$(this).data('pack_code');
		//alert(sel_pack_id);
		$('#enquiry_form_rel').find('.pack_id').val(sel_pack_id);
		$('#enquiry_form_rel').find('.pack_id_text').text(sel_pack_code);
		$('#enquiry_form_rel').find('.pack_name').val(sel_pack_name);
		$('#enquiry_form_rel').find('.pack_code').val(sel_pack_code);
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
	var curday = function(sp){
		today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //As January is 0.
		var yyyy = today.getFullYear();

		//if(dd<10) dd='0'+dd;
		//if(mm<10) mm='0'+mm;
		//return (mm+sp+dd+sp+yyyy);
		return (yyyy+sp+mm+sp+dd);
	};
	jQuery(function(){
		var e_day="<?php echo $group_departure; ?>"; 
		//console.log(e_day);
		var enableDays=e_day.split(',');
		var lastItem = "<?php echo $last_item; ?>";
		var firstItem = "<?php echo $first_item; ?>";
		if(firstItem<curday('-')){
			var firstItem = curday('-');
		}
		function enableAllTheseDays(date) {
			var sdate = $.datepicker.formatDate( 'd-m-yy', date)
			
			if($.inArray(sdate, enableDays) != -1) {
			
				return [true];
			}
		
			return [false];
		}
    //console.log(curday('/'));
	//console.log(curday('-'));
	//console.log(firstItem);
	
		$('#datepicker_dat_group,#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		//$('#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		
	})
	$(document).ready(function() {
		$( "#datepicker_dat" ).datepicker();
		//$( "#datepicker_dat_fit" ).datepicker();
	});
</script>


<!-- new css start -->
<style type="text/css">
	.lft_detl .id-pac-label{
      padding: 5px;
      background: linear-gradient(96deg,#002042,#0a8ec1);
      color: #fff;
      width: 100%;
      border-top-right-radius: 15px;
      border-bottom-right-radius: 15px;
      text-align: center;
    }
	.id-pac-price del{
      color: #828181;
      font-size: 16px!important;
    }
    .id-pac-price{
      font-size: 22px!important;
    }
    .id-pac-price small{
      font-size: 12px!important;
    }
    .id-pac-price span{
      color: #d43f3a;
      font-weight: bold;
    }
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
 small {
    color: #828181;
    font-size: 12px;
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
    float: right;
  }
  .id-back-s i {
    padding: 0px 10px;
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
    border-radius: 15px !important;
    border: 1px solid #ccc;
    height: 30px;
    box-shadow: unset!important;
}
  .holiday_srch_input textarea, .holiday_srch_input select{
     border-radius: 15px !important; 
    /* border: 1px solid #9d00f9; */
    box-shadow: unset!important;
  }
  .holiday_srch_input input.form-control:placeholder-shown {
    font-size: 15px;
}
 /* .id-enquiry-btn{
    margin-top: 20px;
    height: 40px;
    width: 100%;
  }*/
  .id-label{
      color: #777!important;
      font-size: 12px;
      margin-top: 5px;
    }
    .id-enquiry-modal button{
      margin-top: 20px;
      border-radius: 4px!important;
      height: 45px;
    }
    .id-enquiry-modal .id-body-div{
      border: 1px solid #ccc;
      padding: 5px;
      margin-bottom: 10px;
      border-radius: 4px;
    }
   /* .id-enquiry-modal .lft_detl h4 {
      margin: 0;
    }*/
    #enquiry_form .modal-body{
      padding: 20px;
    }
    .id-body-div strong{
      color: #2685b7;
    }
    #enquiry_form h4{
      margin: 0;
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
    .price_package-d .btns
    {
      display: flex;
    }
    .price_package-d a{
      flex: 1 1 0;
    margin: 5px 2px;
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
   /* .id-cancel-div ul, li{
      padding-left: 10px;
    }*/
    div#settings1 ul {
    padding-left: 20px;
}
div#t_c ul {
    padding-left: 20px;
}
div#documentation ul
{
   padding-left: 20px;
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
<style type="text/css">
	.id-main-optional-div{
		padding: 40px;
		background-color: #fff;
		/*border:1px solid #ccc;*/
		box-shadow: 0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19) !important;
	}
   .id-mt-3{
    margin-top: 30px;
   }
   .id-optional-div{
   	padding: 20px;
   	border-radius: 15px;
   	/*background-color: #ccc;*/
   	background: linear-gradient(0deg,#002042,#0a8ec1);
   	color: #fff;
   }
   .id-optional-div span{
   	/*font-weight: bold;*/
   }
   .id-optional-div p{
   	margin: 0;
   	font-size: 18px;
   }
   .id-optional-div small{
   	font-weight: normal;
   }
   .id-dest-o p{
   	font-size: 18px;
   }
   .id-dest-o{
   	padding: 10px;
   	padding-left: 0;
   }
   .id-dest-o span{
   	font-weight: bold;
   }
   .id-optional-check:not(:checked), .id-optional-check:checked{
   	position: relative;
    left: 0; 
    top: 5px;
    width: 40px;
    height: 20px;
    margin-top: 5px!important;
    cursor: pointer;
    
   }
   .id-optional-check:not(:checked) + label:after, .id-optional-check:checked + label:after {
   	content: unset!important;
   }
   .id-optional-check{
   	margin: 0;
   }
   .id-optional-table label{
   	cursor: pointer;
   	padding: 0;
   }
   .id-optional-table table, td, th{
   	font-size: 15px!important;
   	border:1px solid #ccc;
   	vertical-align: middle!important;
   }
   .id-optional-table th,td{
   	text-align: center;
   }
   .id-optional-table span{
   	padding-bottom: 15px;
   }
   .id-optional-table table{
   	box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   }
   .id-optional-table h3{
   	color: #288abc;
   	margin-bottom: 20px;
   	/*text-align: center;*/
   }
   .id-optional-btn button{
   	height: 50px;
   	margin-top: 10px;
   	font-size: 20px;
   }
   .id-optional-table th{
   	background-color: #eee;
   }
   .id-main-optional-div h1{
   	color: #ef1a16;
   	margin-top: 0;
   	text-align: center;
   }
   .content-wrapper{
    background-color:#f7f7f7!important;
   }
   .id-dest-o .fa{
   	color: #0a8ec1;
   	font-size: 14px;
   }
   .id-main-optional-div .id-back-to{
   	color: #288abc;
   	text-align: right;
   	font-size: 15px;
   	cursor: pointer;
   	margin-bottom: 0;
   }
   .modal-footer {
    padding: 15px;
    text-align: right;
    border-top: none;
}
.id-spcl_atrct p span {
    word-break: break-all;
}
#enquiry_form_in
{
  overflow-y: scroll !important;
}
.padl
{
  padding-left: 0;
}
</style>