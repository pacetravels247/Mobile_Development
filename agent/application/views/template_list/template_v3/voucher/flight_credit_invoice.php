<?php
// echo 'hffg';
$booking_details = $data ['booking_details'] [0];
$booking_itinerary_details = $booking_details['booking_itinerary_details'];
$booking_transaction_details = $booking_details ['booking_transaction_details'];
$itinerary_details = $booking_details ['booking_itinerary_details'];
// generate onword and return
$admin_markup = $booking_details["admin_markup"];
$agent_markup = $booking_details["agent_markup"];
$pass_count = 0;
foreach ($booking_transaction_details as $key => $value) {
  $pass_count += count($value["booking_customer_details"]);
    foreach($value["booking_customer_details"] AS $cust_val){
      if(($cust_val["origin"]==$_GET["passenger_origin"]) && ($cust_val["status"]=="BOOKING_CANCELLED")){
        $this_person_cancel_count += 1;
        $cancelled_person_exist_in_this_trans_key = $key;
      }
  }
}
$commission_reversed = $cancellation_details["data"][0]["commission_reversed"];
$this_user_markup = 0; //(($admin_markup)/$pass_count);

$other_refund = 0;
if($to_customer)
{
  $this_user_markup = ($agent_markup/$pass_count)*$this_person_cancel_count;
  //$other_refund = $agent_markup;
}

  # code...
if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {

    $onward_segment_details = array ();
    $return_segment_details = array ();
    $segment_indicator_arr = array ();
    $segment_indicator_sort = array ();
    
    foreach ( $itinerary_details as $key => $key_sort_data ) {
        $segment_indicator_sort [$key] = $key_sort_data ['origin'];
    }
    array_multisort ( $segment_indicator_sort, SORT_ASC, $itinerary_details );
    
    foreach ( $itinerary_details as $k => $sub_details ) {
        $segment_indicator_arr [] = $sub_details ['segment_indicator'];
        $count_value = array_count_values ( $segment_indicator_arr );
        
        if ($count_value [1] == 1) {
            $onward_segment_details [] = $sub_details;
        } else {
            $return_segment_details [] = $sub_details;
        }
    }
}
#@debug($return_segment_details);

if(isset($return_segment_details)){

    $retur_fare_details = json_decode($booking_transaction_details[1]['attributes'],True);
}

$fare_details = json_decode($booking_transaction_details[0]['attributes'],True);

$BaseFare = $fare_details['Fare']['BaseFare']+@$retur_fare_details['Fare']['BaseFare'];
$Tax = $fare_details['Fare']['Tax']+@$retur_fare_details['Fare']['Tax'];
$GST = $booking_transaction_details[0]['gst']+@$booking_transaction_details[1]['gst'];

?>
<?php foreach($booking_transaction_details as $t_key=>$t_value): 
if($cancelled_person_exist_in_this_trans_key != $t_key)
  continue;
?>
<div class="pad margin no-print">
   <div style="margin-bottom: 0!important;" class="callout callout-info">
      <h4><i class="fa fa-info"></i> Note:</h4>
      This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
   </div>
</div>
<section class="invoice" style="position: relative;background: #fff;border: 1px solid #f4f4f4;padding: 20px;margin: 10px 25px;">
   <!-- title row -->    
   <div class="row" style="white-space: normal;">
      <div class="col-xs-12" style="width: 100%;">
         <h2 class="page-header" style="padding-bottom: 9px;margin: 40px 0 20px;border-bottom: 1px solid #eee;">                <i class="fa fa-globe"></i> Credit Note
            <small class="pull-right" style="color: #666;display: block;margin-top: 5px;float: right!important;">Date :<?php echo date('d-m-Y')?></small>            
         </h2>
      </div>
      <!-- /.col -->    
   </div>
   <!-- info row -->    
   <div class="row invoice-info">
      <div class="col-sm-4 invoice-col" style="width: 33.33333333%;float: left;">
         <b style="font-weight: 700;">From</b>            
         <address style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">
               <?=$admin_details['domainname']?><br>
                Phone : <?=$admin_details['phone']?><br>
                <?php if($admin_details['state']):?>
                    State : <?=$admin_details['state']?><br>
                <?php endif;?>
               <!--  <b>Email :</b> info@sunango.com<br>  -->
                <?=$admin_details['address']?>,<br>
                <br>
                <b>GSTIN:</b> <?=domain_gst_number()?><!-- agent gst number-->
            </address>
      </div>
      <!-- /.col -->                
      <div class="col-sm-4 invoice-col"  style="width: 33.33333333%;float: left;">
         <b style="font-weight: 700;">To</b>            
         <address style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">
         <?php if($to_customer) { ?>
            <table width="100%">
               <tbody>
                  <tr><td><b>User name :</b> <?php echo $booking_details['lead_pax_name']; ?></td></tr>
                    <tr><td><b>Phone :</b> <?php echo $booking_details['lead_pax_phone_number']; ?></td></tr>
                    <tr><td><b>Email:</b><?php echo $booking_details['lead_pax_email']?></td></tr>
                    <tr><td><b>Address:</b><?php echo $booking_details['cutomer_address']?></td></tr>
               </tbody>
            </table>
            <?php } 
            else{ ?>
                <table width="100%">
                    <tr><td><b>User name :</b> <?php echo $data['domainname']; ?></td></tr>
                    <tr><td><b>Address :</b> <?php echo $data['address']; ?></td></tr>

                    <?php if (isset($data['state'])) { ?>
                        <tr><td><b>State :</b> <?php echo $data['state']; ?></td></tr>
                    <?php } ?>
                     <?php if (isset($data['domaincountry'])) { ?>
                        <tr><td><b>Country :</b> <?php echo $data['domaincountry']; ?></td></tr>
                    <?php } ?>
                    <tr><td></td></tr>

                </table>
            <?php } ?>
         </address>
      </div>
      <!-- /.col -->                
        <div class="col-sm-4 invoice-col"  style="width: 33.33333333%;float: left;">
            <b>Pace Ref No :</b><?php echo $booking_details['app_reference']; ?><br>
            <b>PNR :</b><?php echo $t_value['pnr']; ?><br>
            <b>Date :</b> <?php echo @$booking_details['created_datetime']; ?>
        </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->    <!-- Table row -->    
   <div class="row" style="white-space: normal;">
      <div class="col-xs-12 table-responsive" style="width: 100%">
         <table class="table table-striped" width="100%" >
            <thead>
               <tr>
                  <th style="text-align: left;">S.No</th>
                  <th style="text-align: left;">Ticket No</th>
                  <th style="text-align: left;">PNR</th>
                  <th style="text-align: left;">Sector</th>
                  <th style="text-align: left;">Pax Name</th>
                  <th style="text-align: left;">PAx Type</th>
               </tr>
            </thead>
            <tbody>
                 <?php
                   
                    if (count($booking_transaction_details) == 2) {
                        $itinerary_details = array();
                        $itinerary_details = $onward_segment_details;
                    }
                   
                    if (isset($t_value ['booking_customer_details'])) {
                        foreach ($itinerary_details as $segment_details_k => $segment_details_v) {
                          $status = "BOOKING_CONFIRMED";
                            foreach ($t_value ['booking_customer_details'] as $key => $cus_details) {
                              if($cus_details["origin"] == $_GET["passenger_origin"] && $cus_details["status"]=="BOOKING_CANCELLED")
                              {
                                $status = "BOOKING_CANCELLED";
                                ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo $cus_details['TicketNumber']; ?></td>
                                    <td><?php echo $t_value['pnr']; ?></td>
                                    <td><?php echo ucfirst($segment_details_v['from_airport_code']) . '-' . ucfirst($segment_details_v['to_airport_code']); ?></td>
                                    <td><?php echo $cus_details['title'] . ' ' . $cus_details['first_name'] . ' ' . $cus_details['last_name']; ?></td>
                                    <td><?php echo $cus_details['passenger_type']; ?></td>
                                </tr>
                                <?php
                              }
                            }
                        }
                    }
                    ?>
            </tbody>
         </table>
      </div>
      <!-- /.col -->    
   </div>
   <?php 
   $gst_det = $GLOBALS["CI"]->custom_db->single_table_records("gst_master", "gst", array("module"=>"flight"));
	$gst_perc = $gst_det["data"][0]["gst"];
	$gst = 0;
	$tds = ($commission_reversed/100)*5;
   $other_active_fare = $booking_details['grand_total']-$this_user_markup-$refund_amount-$cancel_charge; 
   $this_user_fare = $this_user_markup+$refund_amount+$cancel_charge+$commission_reversed-$tds-$gst;
   if($to_customer)
   {
    $this_user_fare = $this_user_fare-$commission_reversed+$tds;
    $refund_amount += $this_user_markup+$gst;
  }
   ?>                
   <div class="row invoice-info " style="white-space: normal;">
      <div class="col-sm-6 invoice-col" style="width:50%;float: left;">
         <b>Refund Details</b>            
         <address>
            <table>
               <tbody>
                  <tr>
                     <td>Total Fare</td>
                  </tr>
                  <tr>
                     <td>Cancellation Charges (-)</td>
                  </tr>
                  <?php
                  if($other_refund > 0){
                    ?>
                    <tr>
                     <td>Other refunds (+)</td>
                  </tr>
                  <?php }
                  if(!$to_customer){
                    ?>
                    <tr>
                     <td>Commission Reversed (-)</td>
                  </tr>
				  <tr>
                     <td>Tds (+)</td>
                  </tr>
                  <?php } ?>
				  <tr>
                     <td>GST (+)</td>
                  </tr>
                  <tr>
                     <td>Refund Amount</td>
                  </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->        
      <div class="col-sm-6 invoice-col" style="width:50%;float: left;">
         <b>&nbsp;</b>            
         <address>
            <table>
               <tbody>
                  <tr>
                     <td><?=@roundoff_number($this_user_fare) ?>/-</td>
                  </tr>
                  <tr>
                     <td><?=@roundoff_number($cancel_charge) ?>/-</td>
                  </tr>
                  <?php
                  if($other_refund > 0){
                    ?>
                    <tr>
                     <td><?=@roundoff_number($other_refund) ?></td>
                  </tr>
                  <?php }
                  if(!$to_customer){
                    ?>
                    <tr>
                     <td><?=@roundoff_number($commission_reversed) ?></td>
                  </tr>
				  <tr>
                     <td><?=@roundoff_number($tds) ?></td>
                  </tr>
                  <?php } ?>
				  <tr>
                     <td><?=@roundoff_number($gst) ?>/-</td>
				</tr>
                  <tr>
                     <td><?=@roundoff_number($refund_amount) ?>/-</td>
                </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->    
   </div>
   <!-- info row -->    
   <div class="row invoice-info" style="white-space: normal;">
      <div class="col-sm-12 invoice-col" style="width:100%;">
         <p>Terms &amp; Conditions</p>
         <address>
            <!-- <table>
               <tbody>
                  <tr>
                     <td>* All Cases Disputes are subject to Belgaum Jurisdiction.</td>
                  </tr>
                  <tr>
                     <td>* Refunds Cancellations are subject to Supplier's approval.
                     </td>
                  </tr>
                  <tr>
                     <td>* Kindly check all details carefully to avoid unnecessary complications.</td>
                  </tr>
               </tbody>
            </table> -->
            <?=$data ['terms_conditions']?>
         </address>
      </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->    <!-- this row will not appear when printing -->    
   <div class="row no-print" style="white-space: normal;">
      <div class="col-xs-12" style="width:100%;">
         <button class="btn btn-default" onclick="window.print()" ><i class="fa fa-print"></i> Print</button>            <!-- <button style="margin-right: 5px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PDF</button> -->        
      </div>
   </div>
</section>
<?php endforeach;?>
