<?php
$module = $customer_details['module'];
$is_domestic = @$customer_details['is_domestic'];
?>
<style>table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;    
}</style>
<table>
    <tbody><tr>
            <th>Title </th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Phone Number</th>
            <th>Gender</th>
            <?php if($module != 'bus'){ ?>
            <th>Pax Type</th>
            <th>Date Of Birth</th>
            <?php } ?>
            <?php if($module == 'bus'){ ?>
            <th>Age</th>
            <?php } ?>
            <?php if($module == 'flight' && $is_domestic != 1){ ?>
            <th>Passport <br>Number </th>
            <th>Passport Issuing <br>Country</th>
            <th>Passport Expiry date<br>(Year-Month-Date)</th>
            <?php } ?>
        </tr>
        
    <?php
    // debug($customer_details);exit;
    // debug($customer_details['data']['booking_customer_details']);exit;
    foreach ($customer_details['data']['booking_customer_details'] as $key => $value) {
                extract($value);
                if($module == 'flight'){
                    $pax_type = $passenger_type;
                    $phone = $customer_details['data']['booking_details'][0]['phone'];
                }
                else if($module == 'hotel'){
                    $pax_type = $pax_type;
                    if($title == 'Mr' || $title == 'Mstr'){
                        $gender = 'Male';
                    }
                    else if($title == 'Ms'){
                        $gender = 'FeMale';
                    }
                   
                }
                else if($module == 'bus'){
                    $name = explode(' ', $name);
                    $first_name = $name[0];
                    $last_name = $name[1];
                    $phone = $customer_details['data']['booking_details'][0]['phone_number'];
                    $date_of_birth = $age;
                    if($gender == 'Male'){
                        $title = 'Mr';
                    }
                    else if($gender == 'FeMale'){
                        $title = 'Mrs';
                    }
                }
                $table_data .= '
							  <tr>
							    <td>'.$title.'</td>
							    <td>'.$first_name.'</td>
							    <td>'.$last_name.'</td>
                                <td>'.$phone.'</td>
							    <td>'.$gender.'</td>';
                                if($module != 'bus'){
							         $table_data .= '<td>'.$pax_type.'</td>';
                                }
							    $table_data .='<td>'.$date_of_birth.'</td>';
                                if($module == 'flight' && $is_domestic != 1){
                                  $table_data .=   '<td>'.$passport_number.'</td>
                                    <td>'.$passport_issuing_country.'</td>
                                    <td>'.$passport_expiry_date.'</td>';
                                }
							   
							  $table_data .= '</tr>';
			}
                        echo $table_data;
                        ?>
    </tbody></table>