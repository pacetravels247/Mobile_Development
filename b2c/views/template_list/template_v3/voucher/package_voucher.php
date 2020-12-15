<?php 
//debug($data['booking_details']);
$temp_booking_details = $data['booking_details'][0]['temp_booking_details'];
$package_cost=$temp_booking_details[0]['book_attributes']['total_trip_cost_without_gst'];
$total_cost=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost'];
$paid=$temp_booking_details[0]['book_attributes']['paid'];
$pending=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost']-$temp_booking_details[0]['book_attributes']['paid'];
$remaining_amount = $temp_booking_details[0]['book_attributes']['remaining_amount']
?>
      <table style="font-size:12px;font-family: 'Open Sans', sans-serif;width:100%; background-color:#fff; max-width:970px; margin:30px auto;border: 5px solid #fff;box-shadow: 2px 2px 10px #ccc;">
         <tbody>

            <tr>
               <td style="padding:5px 15px;">
                  <table width="100%" cellpadding="8" style="font-size:12px; line-height:22px;">
                     <tbody>
                        <tr>
                           <td style="font-size:18px;font-weight:600"><img width="134" src="http://pacetravels.org/extras/system/template_list/template_v1/images/pace_logo1.png">                     </td>
                           
                        </tr>
                        <tr>
                           <td style="font-size:18px;font-weight:600">  <h2 style="text-transform: uppercase;text-align: center;color:#337ab7;font-weight: 600;">booking confirmation</h2>                     </td>
                           
                        </tr>

                        
                        <tr>
                        	<td>
                        		<table width="80%" cellpadding="8" border="1" style="border-collapse: collapse;margin:0 auto;">
                        			<tr>
										<td width="40%" style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Passanger name</td>
										<td width="60%" style="padding:8px;"><?=$data['user_details'][0]['first_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Tour code</td>
										<td width="60%" style="padding:8px;"><?=$data['booking_details'][0]['booking_package_details']['tour_code']?></td></tr>
                        			<tr>
										<td width="40%" style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Package name</td>
										<td width="60%" style="padding:8px;"><?=$data['booking_details'][0]['booking_package_details']['package_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Date of depature</td>
										<td width="60%" style="padding:8px;"><?=$data['booking_details'][0]['attributes']['departure_date']?></td></tr>
                        			<tr>
										<td width="40%" style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Number of nights</td>
										<td width="60%" style="padding:8px;"><?php echo $data['booking_details'][0]['booking_package_details']['duration']+1 . ' Days / ' . ( $data['booking_details'][0]['booking_package_details']['duration'] ) . (( $data['booking_details'][0]['booking_package_details']['duration']==1)?'  Night': ' Nights'); ?></td>
									</tr>
                        		</table>
                        	</td>
                        </tr>
                           
                        
                        <tr>
                           <td  style="text-align: center;text-transform: uppercase;color:#337ab7;font-size: 20px;font-weight: 700;padding: 20px 0px 0px;"><b style="border-bottom: 3px solid #337ab7;">Payment details</b></td>
                        </tr>
                         <tr>
                        	<td style="padding: 20px 0px;">
                        		<table width="80%" cellpadding="8" border="1" style="border-collapse: collapse;margin:0 auto;">
                        			<tr>
										<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Number of pax : <?=$data['booking_details'][0]['attributes']['adult_count'] + $data['booking_details'][0]['attributes']['child_count']?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Adult : <?=$data['booking_details'][0]['attributes']['adult_count']?></td>
                        				<td  style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Child : <?=$data['booking_details'][0]['attributes']['child_count']?></td>
										<td  style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Infant : <?=$data['booking_details'][0]['attributes']['infant_count']?></td>
									</tr>
                        			<tr>
										<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">Package cost : <?=number_format($package_cost,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">(Total cost) : <?=number_format($total_cost,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">(Amount paid) : <?=number_format($paid,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 16px;color: #222;font-weight: 700;">(Balance) : <?=number_format($pending,2)?></td>
                        			</tr>
                        			
                        		</table>
                        	</td>
                        </tr>
                        
                        <tr>
                           <td  style="text-align: center;text-transform: uppercase;color:#337ab7;font-size: 20px;font-weight: 700;padding: 20px 0px 0px;"><b style="border-bottom: 3px solid #337ab7;">Terms and conditions</b></td>
                        </tr>
                        <tr>
                        	<td style="padding: 20px 30px">
                        		<?=$data['booking_details'][0]['booking_package_details']['terms']?>
                        	</td>
                        </tr>
                        <tr><td style="text-align: center;text-transform: uppercase;font-size:22px;font-weight: 700;padding: 30px; ">Thanks for booking with us</td></tr>
                        
         </tbody>
      </table>
  </td>
</tr>
</tbody>
</table>

   