<?php
// echo 'hffg';
$booking_details = $data ['booking_details'] [0];

$booking_itinerary_details = $booking_details['booking_itinerary_details'];
// debug($booking_details);exit;
$booking_transaction_details = $booking_details ['booking_transaction_details'];
$itinerary_details = $booking_details ['booking_itinerary_details'];
// generate onword and return


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
<?php foreach($booking_transaction_details as $t_key=>$t_value):?>
<div class="pad margin no-print">
   <div style="margin-bottom: 0!important;" class="callout callout-info">
      <h4><i class="fa fa-info"></i> Note:</h4>
      This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
   </div>
</div>
<section class="invoice">
   <!-- title row -->    
   <div class="row">
      <div class="col-xs-12">
         <h2 class="page-header">                <i class="fa fa-globe"></i> Sale Invocie
            <small class="pull-right">Date :<?php echo date('d-m-Y')?></small>            
         </h2>
      </div>
      <!-- /.col -->    
   </div>
   <!-- info row -->    
   <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
         <b>From</b>            
         <address>
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
      <div class="col-sm-4 invoice-col">
         <b>To</b>            
         <address>
            <table>
               <tbody>
                  <tr><td><b>User name :</b> <?php echo $booking_details['lead_pax_name']; ?></td></tr>
                    <tr><td><b>Phone :</b> <?php echo $booking_details['lead_pax_phone_number']; ?></td></tr>
                    <tr><td><b>Email:</b><?php echo $booking_details['lead_pax_email']?></td></tr>
                    <tr><td><b>Address:</b><?php echo $booking_details['cutomer_address']?></td></tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->                
        <div class="col-sm-4 invoice-col">
            <b>TMX App Ref No :</b><?php echo $booking_details['app_reference']; ?><br>
            <b>PNR :</b><?php echo $t_value['pnr']; ?><br>
            <b>Date :</b> <?php echo @$booking_details['created_datetime']; ?>
        </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->    <!-- Table row -->    
   <div class="row">
      <div class="col-xs-12 table-responsive">
         <table class="table table-striped">
            <thead>
               <tr>
                  <th>S.No</th>
                  <th>Ticket No</th>
                  <th>PNR</th>
                  <th>Sector</th>
                  <th>Pax Name</th>
                  <th>PAx Type</th>
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

                            foreach ($t_value ['booking_customer_details'] as $key => $cus_details) {
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
                    ?>
            </tbody>
         </table>
      </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->        <!-- info row -->    
   <div class="row invoice-info">
      <div class="col-sm-6 invoice-col" style="width:50%">
         <b>GST Details</b>            
         <address>
            <table>
               <tbody>
                  <tr><td>Service Charge Collected </td></tr>
                    <?php if ($admin_details['state'] == "karnataka") {?>
                        <tr><td>Add : CGST @ 9% </td></tr>
                        <tr><td>Add : SGST @ 9% </td></tr>
                    <?php } else { ?>
                        <tr><td>Add : IGST @ 18% </td></tr>
                    <?php } ?>
                    <tr><td>Total GST </td></tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->                
      <div class="col-sm-6 invoice-col" style="width:50%">
         <b>&nbsp;</b>                                        
         <address>
            <table>
               <tbody>
                  <?php 

                    $TotalGst = $t_value['gst'];
                  ?>
                  <tr>
                     <td><?php echo number_format(($TotalGst), 2, '.', ''); ?></td>
                  </tr>
                  <?php if ($admin_details['state'] == "karnataka") {
                        $CGST  =0;
                        $SGST = 0;
                        $state_gst = ($TotalGst/2);
                        $CGST = $SGST = $state_gst;
                    ?>
                  <tr>
                     <td><?=number_format($CGST, 2, '.', '');?></td>
                  </tr>
                  <tr>
                     <td><?=number_format($SGST, 2, '.', '');?></td>
                  </tr>
                  <?php } else{?>
                  <tr>
                     <td><?=number_format($TotalGst,2,'.','')?></td>
                  </tr>
                  <?php }?>
                  <tr>
                     <td><?=number_format($TotalGst,2,'.','')?></td>
                  </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->    
   <div class="row invoice-info">
      <div class="col-sm-6 invoice-col" style="width:50%">
         <b>Fare Details</b>            
         <address>
            <table>
               <tbody>
                  <tr>
                     <td>Gross Total</td>
                  </tr>
                  <tr>
                     <td>Add : Service Charge Collected</td>
                  </tr>
                  <tr>
                     <td>Add : GST on service charge</td>
                  </tr>
                  <tr>
                     <td>Less : Commission Earned</td>
                  </tr>
                  <tr>
                     <td>Add : TDS Deducted</td>
                  </tr>
                  <tr>
                     <td>Round Off</td>
                  </tr>
                  <tr>
                     <td>Net Amount</td>
                  </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->        
      <div class="col-sm-6 invoice-col" style="width:50%">
         <b>&nbsp;</b>            
         <address>
            <table>
               <tbody>

                <?php 
                   
                    $AgentComission = $t_value['agent_commission'];
                    $AgentTdsOnCommission = $t_value['agent_tds'];
                    $Gst  = $t_value['gst'];
                    
                    $Admin_Markup = $t_value['admin_markup'];
                    $Agent_Markup = $t_value['agent_markup'];
                    $Total_Fare = $t_value['total_fare'];
                   
                    $AgentNetfare = ($Total_Fare+$Admin_Markup+$AgentTdsOnCommission-$AgentComission);

                    
                    $total = ($AgentNetfare+$Agent_Markup+$AgentComission-$AgentTdsOnCommission+$Gst);
                    

                ?>
                  <tr>
                     <td><?php echo number_format($AgentNetfare, 2, '.', ''); ?></td>
                  </tr>
                  <tr>
                     <td><?php echo number_format($Gst, 2, '.', ''); ?></td>
                  </tr>
                  <tr>
                     <td><?php echo number_format($Gst, 2, '.', ''); ?></td>
                  </tr>
                  <tr>
                     <td>-<?php echo number_format($AgentComission, 2, '.', ''); ?></td>
                  </tr>
                  <!-- <tr><td>0.00</td></tr>-->                            
                  <tr>
                     <td><?php echo number_format($AgentTdsOnCommission, 2, '.', ''); ?></td>
                  </tr>
                  <tr>
                     <td><?=round($total)?></td>
                  </tr>
                  <tr>
                     <td><?php echo number_format($total, 2, '.', ''); ?></td>
                  </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->    
   </div>
   <div class="row">
      <div class="col-sm-12 invoice-col" style=" width: 100%;">
         <div><b>Total Rupees in world: <?php echo getIndianCurrency($total); ?></b></div>
         <br>        
      </div>
   </div>
   <!-- info row -->    
   <div class="row invoice-info">
      <div class="col-sm-12 invoice-col" style="width:100%;">
         <p>Terms &amp; Conditions</p>
         <address>
            <table>
               <tbody>
                  <tr>
                     <td>* All Cases Disputes are subject to Bengaluru Jurisdiction.</td>
                  </tr>
                  <tr>
                     <td>* Refunds Cancellations are subject to Airline's approval.
                     </td>
                  </tr>
                  <tr>
                     <td>* Kindly check all details carefully to avoid unnecessary complications.</td>
                  </tr>
                  <tr>
                     <td>* CHEQUE : Must be drawn in favour of "<?=$admin_details['domainname']?>".</td>
                  </tr>
                  <tr>
                     <td>* LATE PAYMENT : Interest @ 24% per annum will be charged on all outstanding bills after due date.</td>
                  </tr>
                  <tr>
                     <td>* Service charges as included above are to be collected from the customers on our behalf.
                        .
                     </td>
                  </tr>
                  <tr>
                     <td>* Kindly check all details carefully to avoid un-necessary complications.</td>
                  </tr>
                  <tr>
                     <td>* Any Disputes or variations should be brought to our notice with in 15 days of the invoice.</td>
                  </tr>
               </tbody>
            </table>
         </address>
      </div>
      <!-- /.col -->    
   </div>
   <!-- /.row -->    <!-- this row will not appear when printing -->    
   <div class="row no-print">
      <div class="col-xs-12">
         <a class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</a>            <!-- <button style="margin-right: 5px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PDF</button> -->        
      </div>
   </div>
</section>
<?php endforeach;?>
<?php 
function getIndianCurrency($number) {
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else
            $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    echo ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

function flight_inovice_pdf($app_reference, $booking_source = '', $status = '') {

    return '<a href="' . flight_invoice_url($app_reference, $booking_source, $status) . '/show_pdf" target="_blank" class="pull-right"><i class="fa fa-download"></i> Generate PDF</a>';
}
?>
