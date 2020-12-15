
<!DOCTYPE html>
<html>
<body>
<style type="text/css">
p {margin: 0 0 5px;}
.sm_tab.table>tbody>tr>td, .sm_tab.table>thead>tr>th {padding: 5px;font-size: 13px;}
@media print
   {
      .btn_sec, .main-footer, .front_end_link {display: none;}
      .content-wrapper, .right-side { border: none !important; }
   }
   h1,h2,h3,h4,h5,h6
   {
   	font-family: cambria, serif;
   }
   .btn_sec button {
    border: none;
    padding: 5px 24px !important;
}
     .b2b li
   {
      list-style: none;
      padding-left: 30px;

   }
   .b2b li>p {
    margin: -20px 0px 10px;
}
    .b2b ul li::before {
  content: "\2022";
  color: #018798;
  font-weight: bold;
  display: inline-block; 
  width: 1em;
  margin-left: -1em;
}
</style>
<?php  if($menu == true){ ?>
  <div class="container">
    <div class="col-xs-12 text-center mt10"  style="margin-bottom: 10px;">
      <ul class="list-inline btn_sec ">
        <li>
          <button class="btn-sm btn-primary print" onclick="window.print(); return true;">Print</button>
        </li>
       
        <li>
        <a href="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$tour_id.'/show_pdf_voucher';?>"  ><button class="btn-sm btn-primary pdf">PDF</button></a>
       </li>
		<li>
			<a href="<?=$_SERVER['HTTP_REFERER']?>"  ><button class="btn-sm btn-primary pdf">Back</button></a>
       </li>
     </ul>
   </div>
 </div>
 <div class="collapse" id="emailmodel">
  <div class="well max_wd20">
    <h4>Send Email</h4>
    <form name="agent_email" method="post" action="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$tour_id.'/show_broucher/mail';?>">
      <input id="inc_sddress" value="1" type="hidden" name="inc_sddress">
      <input id="inc_fare" value="1" type="hidden" name="inc_fare">
      <div class="row">
        <label>Email Id </label><input id="email" placeholder="Please Enter Email Id" class="airlinecheckbox validate_user_register form-control" type="text" checked name="email">
      </div>
	   <div class="row">
        <label>Email Body </label><input id="email_body" placeholder="Please Enter Email Body" class="airlinecheckbox validate_user_register form-control" type="text" checked name="email_body">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" value="Submit">Send Email</button>
      </div>
    </form>
  </div>
</div>
<?php } ?>
  <table cellspacing="0" cellpadding="10" border="0" style="border: 2px solid #eeeeee; background: #ffffff; font-size: 14px; line-height: 20px; width: 100%; max-width: 600px; margin: 0px auto; font-family: cambria, serif; color:#222;text-align: justify;word-spacing: 5px;" class="b2b">
  <tbody>
  <tr>
  <td style="padding:20px;">
  <table cellspacing="0" cellpadding="10" border="0" style="border-collapse: collapse; width: 100%; margin: 0px auto; font-family: cambria, serif; color:#222;text-align: justify;word-spacing: 5px;">
    <tbody>
      <tr>
        <td style="padding:10px 10px 10px 0px;border-bottom:2px solid #eee;">
          <span style="float: left;"><img style="margin-bottom:0px; height:75px;" src="<?php echo SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->application_domain_logo); ?>" alt="logo"></span>
          <h2 style="margin: 15px 0 0;
    font-size: 26px;
    line-height: 20px;
    font-weight: 600;
    text-align: center;
    color: #ff0000;"><?= $tour_data['package_name']; ?></h2>
          <span style="margin: 8px 0 25px;
    font-size: 20px;
    text-align: center;
    display: block;"><?=($tour_data['duration']+1);?> Days / <?=$tour_data['duration'].(($tour_data['duration']==1)? 'Night': 'Nights');?> </span>
        </td>
      </tr>    
      
      <tr>
        <td style="padding:0px 20px 10px 0;margin-left: auto;margin-right: auto;text-align: center;">
        <span style="width:100%; float:left"><img  style="width: 600px;height: 300px;" src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($tour_data['banner_image']);?>" alt="img" /></span>
        </td>
      </tr>
      <?php 
      if($quotation_details){
        $user_attributes = json_decode($quotation_details['user_attributes'],ture);
      ?>
      <tr>
        <td style="padding:0px 10px 20px 0px">
          <h3 style="margin:0 0 8px ">Quote Reference : <?=$quotation_details['quote_reference']?></h3>
          <h3 style="margin:0 0 8px ">Fare Breakdown (<?=$quotation_details['currency_code']?>)</h3>
          <?php 
          if ($user_attributes['adult_price']) {
            echo '<p>Adult Fare : '. sprintf("%.2f", ceil($user_attributes['adult_price'])).'</p>';
          }
          if ($user_attributes['child_price']) {
            echo '<p>Child Fare : '. sprintf("%.2f", ceil($user_attributes['child_price'])).'</p>';
          }
          if ($user_attributes['infant_price']) {
            echo '<p>Infant Fare : '. sprintf("%.2f", ceil($user_attributes['infant_price'])).'</p>';
          }
          ?>
          <h4 style="margin:0 0 2px;font-weight:bold">Total : <?=$quotation_details['currency_code']?> <?=$quotation_details['quoted_price']?></h4>          
        </td>
      </tr>
      <?php 
      }elseif($booking_details){
        $attributes = json_decode($booking_details['attributes'],ture);
      ?>
      
      <?php 
      }
      ?>
	   <tr>
        <td style="padding:0px 10px 10px 0">
         
          <p style="margin:0 auto;text-align: center">
            <?php 
			if($tour_data['package_type']=="fit"){
				$tour_type="Customised Holiday ";
			}else{
				$tour_type="Group// Fixed Departure";
			}
				
            ?>
          </p>
		   <h2 style="margin: 0 auto;text-align:center;font-size: 16.5px;line-height:20px;font-weight:600;">Tour Type – <?=$tour_type?> || Tour Code - <?=$tour_data['tour_code']?>
</h2>
        </td>
      </tr>
	  <tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 0 auto;
    text-align: center;
    font-size: 16.5px;
    line-height: 20px;
    font-weight: 600;
    color: #018798;">Departure Dates</h2>
          <p style="margin:0 auto;text-align: center;font-size: 16.5px;line-height:20px;font-weight:600;">
            <?php 
			if($tour_data['package_type']=="fit"){
				foreach ($dep_dates as $dd_key => $dd) {
				  if($dd_key==0){  
					echo '';
				  }else{
					echo ' || ';  
				  }
				  echo date('M d, Y',strtotime($dd['valid_from'])).' - '.date('M d, Y',strtotime($dd['valid_to']));
				}
			}else{
				//debug($dep_dates);
				$dep_month_date=array();
				foreach ($dep_dates as $dd) {
					$month_name = date('M',strtotime($dd['dep_date']));
					$dep_month_date[$month_name][]=date('d',strtotime($dd['dep_date']));
				  //echo date('d-m-Y',strtotime($dd['dep_date'])).' , ';
				}
			//	debug($dep_month_date);
				$dep_date_text='';
				foreach ($dep_month_date as $dm_key => $dm_val) {
					$dep_date_text.=$dm_key.' ' ;
					foreach ($dm_val as $day_key => $day_val) {
						$dep_date_text.=$day_val.' ' ;
					}
					end($dep_month_date);
					$key=key($dep_month_date);
					//echo $dm_key.'|'.$key.'<br/>';
					if($dm_key!=$key){
						
						$dep_date_text.=' || ' ;
					}
				}
				echo $dep_date_text;
			}
				
            ?>
          </p>
        </td>
      </tr>
	  <tr>
        <td style="padding:0px 10px 10px 0">
         
          <p style="margin:0;">
            <?php
            echo $tour_data['package_description'];
            ?>
          </p>
        </td>
      </tr>
	  
	   <tr>
        <td style="padding:0px 10px 10px 0">
         
          <p style="margin:0;">
            <h1 style="text-align: left;font-size: 20px;line-height: 20px;font-weight: 600;border-bottom: 1px solid #ccc;padding: 5px;color: #018798;">Detailed Itinerary</h1>
          </p>
        </td>
      </tr>
	 
      <?php
	 // debug($tours_itinerary_dw);
      foreach ($tours_itinerary_dw as $key => $itinary) {
        $accommodation = $itinary['accomodation'];
        $accommodation = json_decode($accommodation);
		$visited_city=json_decode($itinary['visited_city'],1);
		//echo $visited_city;
        ?>
        <tr>
          <td style="padding:0px 10px 10px 0">
            <span style="margin:0 0 2px; font-weight:bold">Day <?php echo $key+1; ?> - <?php echo  $itinary['visited_city_name']; ?> </span>
            <p style="margin:0;">
              <?php echo  htmlspecialchars_decode($itinary['itinerary_des']);   ?>
            </p>
           
            <span style="margin:0 0 2px; font-weight:bold">Meal Plan:
              <?php foreach ($accommodation as  $accom) {
                if ($accom === end($accommodation)){
                       echo $accom;
                    }else{
                       echo $accom.'|';
                    }
                  
              } ?></span>
              <br><br>
              <center>
			  <?php $imag_array= explode(',',$itinary['banner_image']);
			  $imag_array=array_reverse($imag_array);
			  foreach($imag_array as $im){
				  if($im!=''){
				echo '<img style="width:200px;height:200px;display: inline-block;margin:3px;" src="'.SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($im).'">';
				  }
			  }
			  
			  ?>
			  </center>
            </td>
          </tr>
          <?php
        }
        ?>
		<tr>
			<td style="padding:0px 10px 10px 0">
			  <p style="margin:0;">
				<h1 style="text-align:center;font-size: 20px;line-height:20px;font-weight:600;"><em>Tour Ends with Sweet Memories’…….</em></h1>
			  </p>
			</td>
		</tr>
		<tr>
			<td style="padding:0px 10px 10px 0">
			  <p style="margin:0;">
				<h1 style="text-align:left;font-size: 16.5px;line-height:20px;font-weight:600;color: #018798;">Detailed Holiday Price: B2B Pricing</h1>
			  </p>
			  <ul>
				<li>The rates are valid for Indian nationals only</li>
				<li>Hotels might ask for a refundable Security Deposit at the time of check-in, which is payable in Cash or by Credit Card</li>
				<li>The value and currency of the deposit might vary as per the hotel policy.</li>
			  </ul>
			  <table class="tabel table-bordered" style="margin:15px auto;">
				<tr>
					<th style="padding: 5px;background: #f1f1f1;color: #222 !important">Room Type</th>
					<th style="padding: 5px;background: #f1f1f1;color: #222 !important">Per Person</th>
				</tr>

				<?php 
				//debug($b2b_tour_price);
				foreach ($b2b_tour_price as $tour_price_fly) { 
				$occ=$tour_price_fly['occupancy'];
				$query_x = "select * from occupancy_managment where id='$occ'"; 
				$exe   = $this->db->query ( $query_x )->result_array ();
				$fetch_x = $exe[0];
				?>
								<tr>
					<td style="padding: 5px;background: #fff;"><?=$fetch_x['occupancy_name']?></td>
					<td style="padding: 5px;background: #fff;"><?=$tour_price_fly['market_price']?></td>
					</tr>
				<?php } ?>
				
			  </table>
			</td>
		</tr>
	
		
		
	
		<tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">ACCOMMODATION</h2>
            <p style="margin:0;white-space: normal;">
				<ul>
				<?php 
				//debug($tours_hotel_det);
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
          </td>
        </tr>
        <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">PACKAGE PRICE INCLUDES:</h2>
            <p style="margin:0;white-space: normal;">
              <?php 
              $tours_itinerary['inclusions'] = str_replace('\n', '', $tour_data['inclusions']);
              echo htmlspecialchars_decode($tours_itinerary['inclusions']); 
              ?>
            </p>
          </td>
        </tr>
        <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">PACKAGE PRICE DOES NOT INCLUDES:
</h2>
            <p style="margin:0;white-space: normal;">
              <?php 
              $tours_itinerary['exclusions'] = str_replace('\n', '', $tour_data['exclusions']);
              echo htmlspecialchars_decode($tours_itinerary['exclusions']); 
              ?>
            </p> 
          </td>
        </tr>
        <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;"> GENERAL CONDITIONS & REMARKS:</h2>
            <p style="margin:0;white-space: normal;">
             <?php
            
               $tours_itinerary['terms'] = str_replace('\n', '', $tour_data['terms']);
               echo htmlspecialchars_decode($tours_itinerary['terms']);
                ?>
            </p>
          </tr>
		  <tr style="border-bottom: 2px solid #eeeeee;">
            <td style="padding:0px 10px 15px 0">
              <h1 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">TRIP NOTE:</h1>
              <p style="margin:0;white-space: normal;font-size: 13px;">
                <?php $tours_itinerary['trip_notes'] = str_replace('\n', '', $tour_data['trip_notes']);

                echo htmlspecialchars_decode($tours_itinerary['trip_notes']); ?></p>
              </td>
            </tr>
             <tr>
            <td style="padding:0px 10px 15px 0">
              <h1 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">VISA PROCEDURES</h1>
              <p style="margin:0;white-space: normal;font-size: 13px;">
                <?php $tours_itinerary['visa_procedures'] = str_replace('\n', '', $tour_data['visa_procedures']);

                echo htmlspecialchars_decode($tours_itinerary['visa_procedures']); ?></p>
              </td>
            </tr>
			
			 <tr >
            <td style="padding:0px 10px 15px 0">
              <h1 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">PAYMENT POLICY:</h1>
              <p style="margin:0;white-space: normal;font-size: 13px;">
                <?php $tours_itinerary['canc_policy'] = str_replace('\n', '', $tour_data['canc_policy']);

                echo htmlspecialchars_decode($tour_data['b2b_payment_policy']); ?></p>
              </td>
            </tr>
			
          <tr>
            <td style="padding:0px 10px 15px 0">
              <h1 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600;color: #018798;">CANCELLATION POLICY:</h1>
              <p style="margin:0;white-space: normal;font-size: 13px;">
                <?php $tours_itinerary['canc_policy'] = str_replace('\n', '', $tour_data['canc_policy']);

                echo htmlspecialchars_decode($tours_itinerary['canc_policy']); ?></p>
              </td>
            </tr>
			
			
          <tr>
            <td style="padding:10px 20px 10px 0">
              <table>
                <tbody>
                  <tr>
                    <td colspan="3" width="100%"><span style="margin:5px 0;line-height:20px;">&copy; <?=date("Y")?> Pace Travels. All rights reserved.
					</span></td> 
                  </tr>
                </tbody>
              </table>
              </td>
            </tr>
          </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>
      </body>
      </html>