<!DOCTYPE html>
<html>
<body>
<style type="text/css">
p {margin-top: -20px;}
.sm_tab.table>tbody>tr>td, .sm_tab.table>thead>tr>th {font-size: 13px;}
@media print
   {
      .btn_sec, .main-footer, .front_end_link {display: none;}
      .content-wrapper, .right-side { border: none !important; }
   }
    h1,h2,h3,h4,h5,h6
   {
    font-family: cambria, serif;
   }
   table tr
   {
    margin-top: -20px;
    margin-bottom: -20px;
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
        <a href="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$tour_id.'/show_pdf';?>"  ><button class="btn-sm btn-primary pdf">PDF</button></a>
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
    <form name="agent_email" method="post" action="<?php echo base_url () . 'index.php/tours/b2b_voucher/'.$tour_id.'/show_broucher/mail';?>">
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
  <table cellspacing="0" cellpadding="0" border="0" style="border: 2px solid #eeeeee; background: #ffffff; font-size:10px;  width: 100%; max-width: 600px; margin: 0px auto; font-family: cambria,serif; color:#405364;padding: 0;">
  <tbody>
  <tr>
  <td>
  <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; width: 100%; margin: 0px auto; font-family: cambria, serif; color:#405364;">
    <tbody>
      <tr>
        <td style="border-bottom:2px solid #eee;">
          <span style="float: left;"><img style="margin-bottom:0px; height:50px;" src="<?php echo SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->application_domain_logo); ?>" alt="logo"></span>
        </td>
      </tr>    
      <tr>
        <td style="margin-left: auto;margin-right: auto;text-align: center;">
          <h2 style="font-size: 18px; font-weight:600"><?= $tour_data['package_name']; ?></h2>
          <span style="font-size: 16px;"><?=($tour_data['duration']+1);?> Days / <?=$tour_data['duration'].(($tour_data['duration']==1)? 'Night': 'Nights');?> </span>
        </td>
      </tr>
      <tr>
        <td style="margin-left: auto;margin-right: auto;text-align: center;">
        <span style="width:100%; float:left"><img  style="width: 500px;height: 200px;" src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($tour_data['banner_image']);?>" alt="img" /></span>
        </td>
      </tr>
      <?php 
      if($quotation_details){
        $user_attributes = json_decode($quotation_details['user_attributes'],ture);
      ?>
      <tr>
        <td>
          <h3>Quote Reference : <?=$quotation_details['quote_reference']?></h3>
          <h3>Fare Breakdown (<?=$quotation_details['currency_code']?>)</h3>
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
          <h4 style="font-weight:bold">Total : <?=$quotation_details['currency_code']?> <?=$quotation_details['quoted_price']?></h4>          
        </td>
      </tr>
      <?php 
      }elseif($booking_details){
        $attributes = json_decode($booking_details['attributes'],ture);
      ?>
      <!-- <tr>
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
      </tr> -->
      <?php 
      }
      ?>
     <tr>
        <td>
         
          <p style="margin:0 auto;text-align: center">
            <?php 
      if($tour_data['package_type']=="fit"){
        $tour_type="Customised Holiday ";
      }else{
        $tour_type="Group// Fixed Departure";
      }
        
            ?>
          </p>
       <h2 style="margin: 0 auto;text-align:center;font-size:10px;font-weight:600;">Tour Type – <?=$tour_type?> || Tour Code - <?=$tour_data['tour_code']?>
</h2>
        </td>
      </tr>
    <tr>
        <td>
          <h2 style="margin:0px auto;text-align:center;font-size:10px;font-weight:600;">Departure Dates</h2>
          <p style="margin:0px auto;text-align: center;font-size:10px;font-weight:600;">
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
      //  debug($dep_month_date);
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
        <td>
         
          <p style="">
            <?php
            echo $tour_data['package_description'];
            ?>
          </p>
        </td>
      </tr>
    <!--<tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 5px 0 2px;font-size:10px;font-weight:600;">TYPE</h2>
          <p style="">
            <?php
            foreach ($categories as  $cat) {
              echo $cat['tour_type_name'].',';
            }
            ?>
          </p>
        </td>
      </tr>-->
    <!--  <tr>
        <td style="padding:0px 10px 10px 0">
          <h2 style="margin: 5px 0 2px;font-size:10px;font-weight:600;">Activities</h2>
          <p style="">
            <?php 
            foreach ($activities as  $act) {
              echo $act['tour_subtheme'].',';
            }
            ?>
          </p>
        </td>
      </tr>-->
     <tr>
        <td>
         
          <p style="">
            <h1 style="text-align: left;font-size: 14px;font-weight: 600;border-bottom: 1px solid #ccc;margin-top: -20px;">Detailed Itinerary</h1>
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
          <td>
            <span style="font-weight:bold">Day <?php echo $key+1; ?> - <?php echo  $itinary['visited_city_name']; ?> </span>
            <p>
              <?php echo  htmlspecialchars_decode($itinary['itinerary_des']);   ?>
            </p>
           <!-- <p style="">Overnight at hotel <?=$itinary['hotel_name']?></p>
            <p>
              <?php 
              if($itinary['rating'])
              {
                ?>
                <img src="http://www.ziphop.com/extras/custom/keWD7SNXhVwQmNRymfGN/images/star_rating_<?=$itinary['rating']?>.png">
                <?php
              }
              ?>
            </p>-->
            
            <div style="font-weight:bold;padding: 0;margin:0;">Meal Plan: 
              <?php foreach ($accommodation as  $accom) {
                if ($accom === end($accommodation)){
                       echo $accom;
                    }else{
                       echo $accom.'|';
                    }
                  
              } ?></div>
              
             
        <?php $imag_array= explode(',',$itinary['banner_image']);
        foreach($imag_array as $im){
          if($im!=''){
        echo '<img style="width:100px;height:100px;display:block;margin-left: auto;margin-right: auto;text-align: center;" src="'.$this->template->domain_images($im).'">';
          }
        }
        
        ?>
      

      
            </td>
          </tr>
          <?php
        }
        ?>
    <tr>
      <td>
        <p style="margin:-10px 0px;">
        <h1 style="text-align:center;font-size: 14px;font-weight:600;"><em>Tour Ends with Sweet Memories’…….</em></h1>
        </p>
      </td>
    </tr>
    <tr>
      <td>
        <p style="margin:-10px 0px;">
        <h1 style="text-align:left;font-size:10px;font-weight:600;">Detailed Holiday Price: B2B Pricing</h1>
        </p>
        <ul>
        <li>The rates are valid for Indian nationals only</li>
        <li>Hotels might ask for a refundable Security Deposit at the time of check-in, which is payable in Cash or by Credit Card</li>
        <li>The value and currency of the deposit might vary as per the hotel policy.</li>
        </ul>
        <table class="tabel table-bordered" border="1" style="margin-right: auto;margin-left: auto;border:1px solid #ccc;">
        <tr>
          <th style="">Room Type</th>
          <th style="">Per Person</th>
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
          <td style=""><?=$fetch_x['occupancy_name']?></td>
          <td style=""><?=$tour_price_fly['market_price']?></td>
          </tr>
        <?php } ?>
        
        </table>
      </td>
    </tr>
  
    
    
  <!--  <tr>
      <td style="padding:0px 10px 10px 0">
        <h2 style="margin:5px 0;font-size:10px; font-weight:600">B2B TOUR COST</h2>
        <?php
        $arr_occ = array();
        // debug($b2b_tour_price);exit;
        
        foreach ($b2b_tour_price as $tour_price_fly) {
          $arr_occ_fly = explode(',',$tour_price_fly['occ']);
          $arr_occ = array_merge($arr_occ, $arr_occ_fly);
        }
        $arr_occ = array_unique($arr_occ);
        ?>
        <table class="table table-bordered sm_tab" style="margin-bottom:8px; border:1px solid #d6d6d6;">
          <thead>
          <?php  $currencyp = ($this->session->userdata('currency') != "") ? $this->session->userdata('currency') : "CAD"; ?>
          <tr>
           
            <?php   
//debug($b2b_tour_price);           
            foreach ($b2b_tour_price as $tour_price_fly) {
              $occ=$tour_price_fly['occupancy'];
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
          <tr>
            <?php
            foreach ($b2b_tour_price as  $value) {
            ?>
              <td><strike><?=$value['currency'].' '.$value['market_price'].'<br/>'?></strike>
              <?=$value['currency'].' '.sprintf('%.2f',$value['netprice_price'])?>
              </td>
            <?php
            }
            ?>
          </tr>
           
          </tbody>
        </table>

      <td>
    </tr>
    <tr>
      <td style="padding:0px 10px 10px 0">
        <h2 style="margin:5px 0;font-size:10px; font-weight:600">B2C TOUR COST</h2>
        <?php
        $arr_occ = array();
        // debug($b2c_tour_price);exit;
        
        foreach ($b2c_tour_price as $tour_price_fly) {
          $arr_occ_fly = explode(',',$tour_price_fly['occ']);
          $arr_occ = array_merge($arr_occ, $arr_occ_fly);
        }
        $arr_occ = array_unique($arr_occ);
        ?>
        <table class="table table-bordered sm_tab" style="margin-bottom:8px; border:1px solid #d6d6d6;">
          <thead>
          <?php  $currencyp = ($this->session->userdata('currency') != "") ? $this->session->userdata('currency') : "CAD"; ?>
          <tr>
           
            <?php   
//debug($b2c_tour_price);           
            foreach ($b2c_tour_price as $tour_price_fly) {
              $occ=$tour_price_fly['occupancy'];
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
          <tr>
            <?php
            foreach ($b2c_tour_price as  $value) {
            ?>
              <td><strike><?=$value['currency'].' '.$value['market_price'].'<br/>'?></strike>
              <?=$value['currency'].' '.sprintf('%.2f',$value['netprice_price'])?>
              </td>
            <?php
            }
            ?>
          </tr>
           
          </tbody>
        </table>
      <td>
    </tr>
      <!--  <tr>
          <td style="padding:0px 10px 10px 0">
            <h2 style="margin:5px 0;font-size:10px; font-weight:600">TOUR COST</h2>
            <?php 
            $arr_occ = array();
             //debug($tour_price);exit;
            
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
        </tr>-->
    <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="font-size:10px; font-weight:600">ACCOMMODATION</h2>
            <p style="white-space: normal;">
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
            <h2 style="font-size:10px; font-weight:600">PACKAGE PRICE INCLUDES:</h2>
            <p style="white-space: normal;">
              <?php 
              $tours_itinerary['inclusions'] = str_replace('\n', '', $tour_data['inclusions']);
              echo htmlspecialchars_decode($tours_itinerary['inclusions']); 
              ?>
            </p>
          </td>
        </tr>
        <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="font-size:10px; font-weight:600">PACKAGE PRICE DOES NOT INCLUDES:
</h2>
            <p style="white-space: normal;">
              <?php 
              $tours_itinerary['exclusions'] = str_replace('\n', '', $tour_data['exclusions']);
              echo htmlspecialchars_decode($tours_itinerary['exclusions']); 
              ?>
            </p> 
          </td>
        </tr>
        <tr>
          <td style="border-bottom: 2px solid #eeeeee;">
            <h2 style="font-size:10px; font-weight:600"> GENERAL CONDITIONS & REMARKS:</h2>
            <p style="white-space: normal;">
             <?php
            
               $tours_itinerary['terms'] = str_replace('\n', '', $tour_data['terms']);
               echo htmlspecialchars_decode($tours_itinerary['terms']);
                ?>
            </p>
          </tr>
      <tr style="border-bottom: 2px solid #eeeeee;">
            <td>
              <h1 style="font-size:10px; font-weight:600">TRIP NOTE:</h1>
              <p style="white-space: normal;font-size: 10px;">
                <?php $tours_itinerary['trip_notes'] = str_replace('\n', '', $tour_data['trip_notes']);

                echo htmlspecialchars_decode($tours_itinerary['trip_notes']); ?></p>
              </td>
            </tr>
             <tr>
            <td>
              <h1 style="font-size:10px; font-weight:600">VISA PROCEDURES</h1>
              <p style="white-space: normal;font-size: 10px;">
                <?php $tours_itinerary['visa_procedures'] = str_replace('\n', '', $tour_data['visa_procedures']);

                echo htmlspecialchars_decode($tours_itinerary['visa_procedures']); ?></p>
              </td>
            </tr>
      <!-- <tr style="border-bottom: 2px solid #eeeeee;">
        <table>
         
            </table>
            </tr> -->
       <tr >
            <td>
              <h1 style="font-size:10px; font-weight:600">PAYMENT POLICY:</h1>
              <p style="white-space: normal;font-size: 10px;">
                <?php $tour_data['b2b_payment_policy'] = str_replace('\n', '', $tour_data['b2b_payment_policy']);

                echo htmlspecialchars_decode($tour_data['b2b_payment_policy']); ?></p>
              </td>
            </tr>
      
          <tr>
            <td>
              <h1 style="font-size:10px; font-weight:600">CANCELLATION POLICY:</h1>
              <p style="white-space: normal;font-size: 10px;">
                <?php $tours_itinerary['canc_policy'] = str_replace('\n', '', $tour_data['canc_policy']);

                echo htmlspecialchars_decode($tours_itinerary['canc_policy']); ?></p>
              </td>
            </tr>
      
      
          <tr>
            <td>
              <table>
                <tbody>
                  <tr>
                    <td colspan="3" width="100%"><span>&copy; <?=date("Y")?> Pace Travels. All rights reserved.
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