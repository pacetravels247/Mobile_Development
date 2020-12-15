<style type="text/css">
   li
   {
      list-style: none;
   }
   .term ul li::before {
  content: "\2022";
  color: #018798;
  font-weight: bold;
  display: inline-block; 
  width: 1em;
  margin-left: -1em;
}
</style>

<?php 
//debug($data['booking_details']);
$temp_booking_details = $data['booking_details'][0]['temp_booking_details'];
$package_cost=$temp_booking_details[0]['book_attributes']['total_trip_cost_without_gst'];
$total_cost=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost'];
$paid=$temp_booking_details[0]['book_attributes']['paid'];
$pending=$temp_booking_details[0]['book_attributes']['total_trip_with_gst_cost']-$temp_booking_details[0]['book_attributes']['paid'];
$remaining_amount = $temp_booking_details[0]['book_attributes']['remaining_amount']
?>
      <table style="font-size:12px;font-family: 'Open Sans', sans-serif;width:100%; background-color:#fff; max-width:600px; margin:60px auto 30px;border: 2px solid #eeeeee;">
         <tbody>

            <tr>
               <td style="padding:5px 15px;">
                  <table width="100%" cellpadding="8" style="font-size:12px; line-height:22px;">
                     <tbody>
                        <tr>
                           <td style="font-size:18px;font-weight:600">
                              <img width="80" src="http://pacetravels.net/extras/system/template_list/template_v1/images/pace_logo1.png">  
                               <h2 style="text-transform: uppercase;
    margin: -50px 0px 10px;
    font-size: 24px;
    line-height: 50px;
    font-weight: 600;
    text-align: center;
    color: #018798;">booking confirmation</h2>                   </td>
                           
                        </tr>
                        <tr>
                           <td style="border: 1px solid #eeeeee" colspan="2">                      </td>
                           
                        </tr>

                        
                        <tr>
                        	<td colspan="2">
                        		<table width="90%" cellpadding="8"  style="border-collapse: collapse;margin:10px auto;">
                        			<tr>
										<td width="40%" style="padding: 8px;text-transform: uppercase;font-size: 14px;color: #018798;font-weight: 500;border: 1px solid #018798;">agency name</td>
										<td width="60%" style="padding: 8px;border: 1px solid #018798;"><?=$data['user_details'][0]['agency_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding: 8px;text-transform: uppercase;font-size: 14px;color: #018798;font-weight: 500;border: 1px solid #018798;">tour code</td>
										<td width="60%" style="padding: 8px;border: 1px solid #018798;"><?=$data['booking_details'][0]['booking_package_details']['tour_code']?></td></tr>
                        			<tr>
										<td width="40%" style="padding: 8px;text-transform: uppercase;font-size: 14px;color: #018798;font-weight: 500;border: 1px solid #018798;">package name</td>
										<td width="60%" style="padding: 8px;border: 1px solid #018798;"><?=$data['booking_details'][0]['booking_package_details']['package_name']?></td>
									</tr>
                        			<tr>
										<td width="40%" style="padding: 8px;text-transform: uppercase;font-size: 14px;color: #018798;font-weight: 500;border: 1px solid #018798;">date of depature</td>
										<td width="60%" style="padding: 8px;border: 1px solid #018798;"><?=$data['booking_details'][0]['attributes']['departure_date']?></td></tr>
                        			<tr>
										<td width="40%" style="padding: 8px;text-transform: uppercase;font-size: 14px;color: #018798;font-weight: 500;border: 1px solid #018798;">number of nights</td>
										<td width="60%" style="padding: 8px;border: 1px solid #018798;"><?php echo $data['booking_details'][0]['booking_package_details']['duration']+1 . ' Days / ' . ( $data['booking_details'][0]['booking_package_details']['duration'] ) . (( $data['booking_details'][0]['booking_package_details']['duration']==1)?'  Night': ' Nights'); ?></td>
									</tr>
                        		</table>
                        	</td>
                        </tr>
                           
                        
                        <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#018798;font-size: 18px;font-weight: 700;padding: 20px 0px 0px;">payment details</td>
                        </tr>
                         <tr>
                        	<td style="padding: 20px 0px;">
                        		<table width="90%" cellpadding="8" style="border-collapse: collapse;margin:0 auto;">
                        			<tr>
										<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;white-space: nowrap;">number of pax : <?=$data['booking_details'][0]['attributes']['adult_count'] + $data['booking_details'][0]['attributes']['child_count']?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">adult : <?=$data['booking_details'][0]['attributes']['adult_count']?></td>
                        				<td colspan="2" style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">child : <?=$data['booking_details'][0]['attributes']['child_count']?></td>
									</tr>
                        			<tr>
										<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">package cost : <?=number_format($package_cost,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">(total cost) : <?=number_format($total_cost,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">(amount paid) : <?=number_format($paid,2)?></td>
                        				<td style="padding:8px;text-transform: uppercase;font-size: 14px;color: #222;border: 1px solid #018798;">(balance) : <?=number_format($pending,2)?></td>
                        			</tr>
                        			
                        		</table>
                        	</td>
                        </tr>
                        
                        <tr>
                           <td  style="text-align: left;text-transform: uppercase;color:#018798;font-size: 18px;font-weight: 700;padding: 20px 0px 0px;">Terms and conditions</td>
                        </tr>
                        <tr>
                        	<td style="padding: 20px 30px" class="term">
                        		<?=$data['booking_details'][0]['booking_package_details']['terms']?>
                        	</td>
                        </tr>
                        <tr><td style="text-align: center;text-transform: uppercase;font-size:22px;font-weight: 700;padding: 30px;color: #018798; ">Thanks for booking with us</td></tr>
                        
         </tbody>
      </table>
  </td>
</tr>
</tbody>
</table>

   