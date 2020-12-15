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
</style>
<?php  if($menu == true){ ?>
  <div class="container">
    <div class="col-xs-12 text-center mt10">
      <ul class="list-inline btn_sec">
        <li>
          <button class="btn-sm btn-primary print" onclick="window.print(); return true;">Print</button>
        </li>
        <li>
          <button type="button" class="btn-sm btn-primary btn-popup bnt_orange" data-toggle="collapse" data-target="#emailmodel" aria-expanded="false" aria-controls="markup_update">Email</button>
        </li>
        <li>
        <a href="<?php echo base_url () . 'index.php/tours/voucher/'.$tour_id.'/show_pdf';?>"  ><button class="btn-sm btn-primary pdf">PDF</button></a>
       </li>
       <li>
       <?php 
       if($this->session->userdata( 'back_link' )){        
         $back_link = $this->session->userdata( 'back_link' );
       }else{
         $back_link = base_url () . 'index.php/tours/tour_list/';
       }
       ?>
         <a href="<?= $back_link ?>"  ><button class="btn-sm btn-primary pdf">Back</button></a>
       </li>
     </ul>
   </div>
 </div>
 <div class="collapse" id="emailmodel">
  <div class="well max_wd20">
    <h4>Send Email</h4>
    <form name="agent_email" method="post" action="<?php echo base_url () . 'index.php/tours/voucher/'.$tour_id.'/show_broucher/mail';?>">
      <input id="inc_sddress" value="1" type="hidden" name="inc_sddress">
      <input id="inc_fare" value="1" type="hidden" name="inc_fare">
      <div class="row">
        <label>Email Id </label><input id="email" placeholder="Please Enter Email Id" class="airlinecheckbox validate_user_register form-control" type="text" checked name="email">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" value="Submit">Send Email</button>
      </div>
    </form>
  </div>
</div>
<?php } ?>
  <table cellspacing="0" cellpadding="10" border="0" style="border: 2px solid #eeeeee; background: #ffffff; font-size: 14px; line-height: 20px; width: 100%; max-width: 600px; margin: 0px auto; font-family: Aller, sans-serif; color:#405364;">
  <tbody>
  <tr>
  <td style="padding:20px;">
  <table cellspacing="0" cellpadding="10" border="0" style="border-collapse: collapse; width: 100%; margin: 0px auto; font-family: Aller, sans-serif; color:#405364;">
    <tbody>
      <tr>
        <td style="padding:10px 10px 10px 0px;border-bottom:2px solid #eee;">
          <span style="float: left;"><img style="margin-bottom:0px; height:50px;" src="<?php echo SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->application_domain_logo); ?>" alt="logo"></span>
        </td>
      </tr>    
      <tr>
        <td style="padding:0px 10px 20px 0px">
          <h2 style="margin:15px 0 0;font-size: 16.5px; line-height:20px; font-weight:600"><?= $tour_data['package_name']; ?></h2>
          <span style="margin:0 0 2px;"><?=($tour_data['duration']+1);?> Days / <?=$tour_data['duration'].(($tour_data['duration']==1)? 'Night': 'Nights');?> </span>
        </td>
      </tr>
      <tr>
        <td style="padding:0px 20px 10px 0">
        <span style="width:100%; float:left"><img  style="width:290px; height:160px;" src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($tour_data['banner_image']);?>" alt="img" /></span>
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
      <tr>
        <td style="padding:0px 10px 20px 0px">
          <h3 style="margin:0 0 8px ">Fare Breakdown (<?=$booking_details['currency_code']?>)</h3>
          <?php 
          if ($attributes['adult_price']) {
            echo '<p>Adult Fare : '. sprintf("%.2f", ceil($attributes['adult_price'])).'</p>';
          }
          if ($attributes['child_price']) {
            echo '<p>Child Fare : '. sprintf("%.2f", ceil($attributes['child_price'])).'</p>';
          }
          if ($attributes['infant_price']) {
            echo '<p>Infant Fare : '. sprintf("%.2f", ceil($attributes['infant_price'])).'</p>';
          }
          ?>
          <h4 style="margin:0 0 2px;font-weight:bold">Total : <?=$booking_details['currency_code']?> <?=$booking_details['basic_fare']?></h4>          
        </td>
      </tr>
      <?php 
      }
      ?>
	  <tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 5px 0 2px;font-size: 16.5px;line-height:20px;font-weight:600;">Departure Dates</h2>
          <p style="margin:0;">
            <?php 
            foreach ($dep_dates as $dd) {
              echo $dd['dep_date'].',';
            }
            ?>
          </p>
        </td>
      </tr>
	  <tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 5px 0 2px;font-size: 16.5px;line-height:20px;font-weight:600;">TYPE</h2>
          <p style="margin:0;">
            <?php
            foreach ($categories as  $cat) {
              echo $cat['tour_type_name'].',';
            }
            ?>
          </p>
        </td>
      </tr>
      <tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 5px 0 2px;font-size: 16.5px;line-height:20px;font-weight:600;">Activities</h2>
          <p style="margin:0;">
            <?php 
            foreach ($activities as  $act) {
              echo $act['tour_subtheme'].',';
            }
            ?>
          </p>
        </td>
      </tr>
      <?php
      foreach ($tours_itinerary_dw as $key => $itinary) {
        $accommodation = $itinary['accomodation'];
        $accommodation = json_decode($accommodation);
        ?>
        <tr>
          <td style="padding:0px 10px 10px 0">
            <span style="margin:0 0 2px; font-weight:bold">Day <?php echo $key+1; ?> - <?php echo  $itinary['program_title']; ?> </span>
            <p style="margin:0;">
              <?php echo  htmlspecialchars_decode($itinary['program_des']);   ?>
            </p>
            <p style="margin:0;">Overnight at hotel <?=$itinary['hotel_name']?></p>
            <p>
              <?php 
              if($itinary['rating'])
              {
                ?>
                <img src="http://www.ziphop.com/extras/custom/keWD7SNXhVwQmNRymfGN/images/star_rating_<?=$itinary['rating']?>.png">
                <?php
              }
              ?>
            </p>
            <span style="margin:0 0 2px; font-weight:bold">Meal Plan:
              <?php foreach ($accommodation as  $accom) {
                if ($accom === end($accommodation)){
                       echo $accom;
                    }else{
                       echo $accom.'|';
                    }
                  
              } ?></span>
            </td>
          </tr>
          <?php
        }
        ?>
        <tr>
          <td style="padding:0px 10px 10px 0">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600">TOUR COST</h2>
            <?php 
            $arr_occ = array();
            // debug($tour_price);exit;
            
            foreach ($tour_price as $tour_price_fly) {
              $arr_occ_fly = explode(',',$tour_price_fly['occ']);
              $arr_occ = array_merge($arr_occ, $arr_occ_fly);
            }
            $arr_occ = array_unique($arr_occ);
            ?>
            <table class="table table-bordered sm_tab" style="margin-bottom:8px; border:1px solid #d6d6d6;">
              <thead>
                <?php  $currencyp = ($this->session->userdata('currency') != "") ? $this->session->userdata('currency') : "CAD"; ?>
                <tr>
                  <th style="background-color:#eee"> From Date</th>
                  <th style="background-color:#eee"> To Date</th>
                  <?php                  
                  foreach ($arr_occ as $occ) {
                    $query_x = "select * from occupancy_managment where id='$occ'"; 
                    $exe   = $this->db->query ( $query_x )->result_array ();
                    $fetch_x = $exe[0];
                    ?>
                    <th style="background-color:#eee">
					  Amount (Occ - 
                      <?=$fetch_x['occupancy_name']?>)
                    </th>
                    <?php           
                  }
                  ?>

                </tr>
              </thead>
              <tbody>
                <?php
                //debug($tour_price);
                foreach ($tour_price as  $value) {
                  $markup = $value['markup'];
                  $arr_pricing_x = explode(',',$value['pricing']);
                  $markup = explode(',',$value['markup']);
                  $arr_occ_x = explode(',',$value['occ']);
                  $arr_pri = array();
                  foreach ($arr_occ_x as $key => $value_x) {
                    $arr_pri[$value_x] = array(
					  'currency' => $value['currency'],
                      'pricing' => $arr_pricing_x[$key],
                      'markup' => $markup[$key]
                    );
                  }
                  ?>
                  <tr>
                    <td>
                      <?=date("d-M-Y", strtotime($value['from_date']) ); ?>
                    </td>
                    <td>
                      <?= date("d-M-Y", strtotime( $value['to_date'] ) );?>
                    </td>
                    <?php
                  foreach ($arr_occ as $key => $occ) {
                    if(array_key_exists($occ, $arr_pri)){
                      ?>
                      <td>
                        <?=$arr_pri[$occ]['currency'].' '.sprintf('%.2f',$arr_pri[$occ]['pricing'])?>
                      </td>
                      <?php
                    }else{
                      ?>
                      <td>N/A</td>
                      <?php
                    }
                    }
                    ?>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding:0px 10px 5px 0">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600">Tour Inclusions</h2>
            <p style="margin:0;white-space: normal;">
              <?php 
              $tours_itinerary['inclusions'] = str_replace('\n', '', $tours_itinerary['inclusions']);
              echo htmlspecialchars_decode($tours_itinerary['inclusions']); 
              ?>
            </p>
          </td>
        </tr>
        <tr>
          <td style="padding:0px 10px 5px 0">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600">Tour Exclusions</h2>
            <p style="margin:0;white-space: normal;">
              <?php 
              $tours_itinerary['exclusions'] = str_replace('\n', '', $tours_itinerary['exclusions']);
              echo htmlspecialchars_decode($tours_itinerary['exclusions']); 
              ?>
            </p>
          </td>
        </tr>
        <tr>
          <td style="padding:0px 10px 5px 0">
            <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600">Terms &amp; Conditions</h2>
            <p style="margin:0;white-space: normal;">
             <?php
            
               $tours_itinerary['terms'] = str_replace('\n', '', $tours_itinerary['terms']);
               echo htmlspecialchars_decode($tours_itinerary['terms']);
                ?>
            </p>
          </tr>
          <tr style="border-bottom: 2px solid #eeeeee;">
            <td style="padding:0px 10px 15px 0">
              <h2 style="margin:5px 0;font-size: 16.5px; line-height:20px; font-weight:600">Cancellation Policy</h2>
              <p style="margin:0;white-space: normal;">
                <?php $tours_itinerary['canc_policy'] = str_replace('\n', '', $tours_itinerary['canc_policy']);

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