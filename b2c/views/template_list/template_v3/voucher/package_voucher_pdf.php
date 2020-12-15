<?php 
//debug($data['booking_details']);
$temp_booking_details = $data['booking_details'][0]['temp_booking_details'];
$package_cost=$temp_booking_details[0]['book_attributes']['total_trip_cost_without_gst']-$temp_booking_details[0]['book_attributes']['agent_markup'];
$total_cost=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost'];
$paid=$temp_booking_details[0]['book_attributes']['paid'];
$pending=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost']-$temp_booking_details[0]['book_attributes']['paid'];
$remaining_amount = $temp_booking_details[0]['book_attributes']['remaining_amount']
?>


      <table style="font-size:12px;font-family: 'Open Sans', sans-serif;width:100%; background-color:#fff; max-width:970px; margin:0 auto;border: 5px solid #ccc;">
         <tbody>

            <tr>
               <td style="padding:0;">
                  <table width="100%" cellpadding="0" style="font-size:8px; ">
                     <tbody>
                        <tr>
                           <td style="font-size:10px;font-weight:600"><img width="80" src="http://pacetravels.org/extras/system/template_list/template_v1/images/pace_logo1.png">                     </td>
                           
                        </tr>
                        <tr>
                           <td style="font-size:12px;font-weight:600">  <h2 style="text-transform: uppercase;text-align: center;color: #337ab7;">booking confirmation</h2>                     </td>
                           
                        </tr>

                        
                        <tr>
                        	<td>
                        		<table width="100%" cellpadding="4"  style="border-collapse: collapse;border: 1px solid #ccc;">
                        			<tr>
										<td width="40%" style="padding:0px;text-transform: uppercase;font-size: 9px;color: #222;font-weight: 700;border: 1px solid #ccc;">Passanger name</td>
										<td width="60%" style="padding:0px;border: 1px solid #ccc;"><?=$data['user_details'][0]['first_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding:0px;text-transform: uppercase;font-size: 9px;color: #222;font-weight: 700;border: 1px solid #ccc;">tour code</td>
										<td width="60%" style="padding:0px;border: 1px solid #ccc;"><?=$data['booking_details'][0]['booking_package_details']['tour_code']?></td></tr>
                        			<tr>
										<td width="40%" style="padding:0px;text-transform: uppercase;font-size: 9px;color: #222;font-weight: 700;border: 1px solid #ccc;">package name</td>
										<td width="60%" style="padding:0px;border: 1px solid #ccc;"><?=$data['booking_details'][0]['booking_package_details']['package_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding:0px;text-transform: uppercase;font-size: 9px;color: #222;font-weight: 700;border: 1px solid #ccc;">date of depature</td>
										<td width="60%" style="padding:0px;border: 1px solid #ccc;"><?=$data['booking_details'][0]['attributes']['departure_date']?></td></tr>
                        			<tr>
										<td width="40%" style="padding:0px;text-transform: uppercase;font-size: 9px;color: #222;font-weight: 700;border: 1px solid #ccc;">number of nights</td>
										<td width="60%" style="padding:0px;border: 1px solid #ccc;"><?php echo $data['booking_details'][0]['booking_package_details']['duration']+1 . ' Days / ' . ( $data['booking_details'][0]['booking_package_details']['duration'] ) . (( $data['booking_details'][0]['booking_package_details']['duration']==1)?'  Night': ' Nights'); ?></td>
									</tr>
                        		</table>
                        	</td>
                        </tr>
                           
                         <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#337ab7;font-size: 12px;font-weight: 700;padding:10px;margin-bottom: -10px">&nbsp;</td>
                        </tr>
                        <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#337ab7;font-size: 12px;font-weight: 700;padding:10px;margin-bottom: 10px;"><b style="border-bottom: 3px solid #337ab7;">payment details</b></td>
                        </tr>
                       
                         <tr>
                        	<td style="padding:5px;">
                        		<table width="100%" cellpadding="4"  style="border-collapse: collapse;border: 1px solid #ccc;">
                        			<tr>
										<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">number of pax : <?=$data['booking_details'][0]['attributes']['adult_count'] + $data['booking_details'][0]['attributes']['child_count']?></td>
                        				<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">adult : <?=$data['booking_details'][0]['attributes']['adult_count']?></td>
                        				<td colspan="2" style="padding:8px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">child : <?=$data['booking_details'][0]['attributes']['child_count']?></td>
									</tr>
                        			<tr>
										<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">package cost : <?=number_format($package_cost,2)?></td>
                        				<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">(total cost) : <?=number_format($total_cost,2)?></td>
                        				<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">(amount paid) : <?=number_format($paid,2)?></td>
                        				<td style="padding:0px;text-transform: uppercase;font-size: 10px;color: #222;font-weight: 700;border: 1px solid #ccc;">(balance) : <?=number_format($pending,2)?></td>
                        			</tr>
                        			
                        		</table>
                        	</td>
                        </tr>
                         <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#337ab7;font-size: 12px;font-weight: 700;padding:10px;margin-bottom: 10px">&nbsp;</td>
                        </tr> 
                        <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#337ab7;font-size: 12px;font-weight: 700;padding: 0;padding: 10px;margin-top: 10px"><b style="border-bottom: 3px solid #337ab7;">Terms and conditions</b></td>
                        </tr>
                        
                        <tr style="margin-top: 0px;">
                        	<td style="padding:0;font-size: 10px;">
                        		<p style="font-size: 10px;"><?=$data['booking_details'][0]['booking_package_details']['terms']?></p>
                        	</td>
                        </tr>
                        <tr><td style="text-align: center;text-transform: uppercase;font-size:15px;font-weight: 700;padding: 10px; ">Thanks for booking with us</td></tr>
                        
         </tbody>
      </table>
  </td>
</tr>
</tbody>
</table>

   