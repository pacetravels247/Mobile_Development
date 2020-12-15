<?php 
	//Js_Loader::$js[] = array('src'=>$GLOBALS['CI']->template->template_js_dir('date_formatter.js'),'defer'=>'defer');
	//$arr = json_decode(base64_decode($search_params['search_params']));
   // debug($search_params);exit;
 Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer'); 
	
	if(isset($tourgrade_list[0]['bookingDate'])){
		$booking_date = $tourgrade_list['data'][0]['bookingDate'];	
	}else{
		$booking_date = $search_params['get_year'].'-'.$search_params['get_month'].'-'.$search_params['get_date'];
	}
	$age_band_details_arr = array('Adult','Youth','Senior','Child','Infant');
	$passenger_arr = array();
	$band_id = array();
	$passenger_count= array();
	

	foreach ($age_band_details_arr as $a_key => $a_value) {
		if(isset($search_params[$a_value.'_count'])){
			$passenger_arr[] = $a_value;
			$band_id[] = $search_params[$a_value.'_Band_ID'];
			$passenger_count[$a_value]= $search_params['no_of_'.$a_value];
		}
	}	
	
	$product_date_arr = $tourgrade_list['Available_date'];
	
	$product_dates_arr = array();
	foreach ($product_date_arr as $key => $value) {
		$product_dates_arr[$key] = $value;
	}	
	  $last_year_arr =  end($product_dates_arr);
	  $end_year = key($product_dates_arr);
	  $end_year_date = end($last_year_arr);  
	  $start_date_arr = reset($product_dates_arr);
	  $star_month_year = key($product_dates_arr);
	  $start_date_picker = reset($start_date_arr);
	  $age_band_arr = json_decode(base64_decode($search_params['age_band']),true);
	// $age_band_arr_list = array();
	// debug($age_band);
	// foreach ($age_band as $t_key => $t_value) {
	// 		$age_band_arr_list[$t_key] = $t_value;
	// }
	// debug($age_band_arr_list);exit;

	// debug($search_params);
	  //debug($tourgrade_list);
	  $selected_date_str = '';
	  if(!empty($search_params['select_month'])){
	  		$selected_date_str = $search_params['select_month'];
	  }else{
	  		$selected_date_str = $search_params['get_month'];
	  }

?>
<div class="turgrde">
  <div class="col-xs-12 nopad">
    <div class="col-md-12 nopad">
    <div class="org_row">
      <div class="col-md-12 actresdv">
       <div class="pidaydiv1">
         <h2>Tour/Activity List</h2>
         <p> Pricing based on <span>
       	<?php foreach($passenger_count as $p_key=>$p_val):?>
      				 <?php echo $p_val.' '.$p_key;?>
   			<?php endforeach;?></span>
       <?php  echo date("l\, jS F Y",strtotime($booking_date)); ?></p>
        </div>
        <?php if(isset($tourgrade_list['Message'])&&!empty($tourgrade_list['Message'])):?>
        		<div class="tourguidiveut">
        			<p><?=implode(",", $tourgrade_list['Message']);?></p>
        			<p>Please Change the date</p>
        		</div>
        <?php else:?>
        		
        <?php foreach($tourgrade_list['Trip_list'] as $t_key=>$t_value): ?>
        	<?php 
        		$class='hide_grade';
        		if($t_value['available']==true){
        			$class = 'show_grade';
        		}
        	?>

	       <div class="tourguidiveut <?=$class?>">
		      	<div class="light-border-t mbl">
		            <div class="line light-border-b option-row">
		             <?php
		               		$tour_description = $tourgrade_list['ProductDetails']['ProductName'];
		               		$grade_title = $tour_description;
		               		$grade_code=$tourgrade_list['ProductDetails'][
		               		'ProductCode'];
		               		if($t_value['gradeCode']!='DEFAULT'){
		               			$tour_description=$t_value['gradeDescription'];
		               			$grade_title = $t_value['gradeTitle'];
		               			$grade_code =$t_value['gradeCode'];
		               		}


		               ?>


		               <div class="unit size1of4">
		                <p class="mas"><span class="strong"><?=$grade_title?></span><br><span class="xsmall hint">Code: <?=$grade_code?></span></p>
		               </div>

		               <div class="unit size1of2">
		              
		               <p class="mhs mvs">Description: <?=$tour_description?></p>
		               <?php if($t_value['gradeDepartureTime']):?>
		               		 <p class="mhs mvs green-color">DepartureTime: <?=$t_value['gradeDepartureTime']?></p>
		               <?php endif;?>
		               <?php if($t_value['TotalPax']):?>
		               			<p class="mhs">Total Traveler: <?=$t_value['TotalPax']?></p>
		           		<?php endif;?>
		               	<?php if(!empty($t_value['unavailableReason'])):?>
		               			<p>We're Sorry This option is not available</p>

		               	<?php endif;?>
		               	<?php if(isset($t_value['ageBandsRequired'])):?>
		               		<?php 

		               			foreach ($t_value['ageBandsRequired'] as $key => $value) {
		               				foreach ($value as $c_key => $c_value) {
		               					if(empty($c_value['maximumCountRequired'])){
		               						$text = ' more ';
		               					}else{
		               						$text = $c_value['maximumCountRequired'];
		               					}
		               					echo '<div class="alert-box"><p class="mhs">Traveller Count Start from '.$c_value['minimumCountRequired'].' to '.$text.' '.$passenger_arr[$c_key].' Required.</p></div>';
		               				}
		               			}
		               		?>
		               	<?php endif;?>
		               
		                <?php if(isset($t_value['langServices'])):?>
		                <div class="lang">
		                	<p class="mhs">Language Service : <?php echo implode(",",$t_value['langServices']);?></p>
		                </div>
		            <?php endif;?>
		               </div>
		               <?php if($t_value['available']==true){?>
		               
		               <div class="txtR mas line">

		                 <div class="price-from">Total </div>
		                 <div class="h2 strong"><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?>  <?=$t_value['Price']['TotalDisplayFare']?></div>

		                <div class="">
		                 <form method="post" action="<?=base_url()?>index.php/transferv1/booking"> 

		                <input type="hidden" name="booking_source" value="<?=$search_params['booking_source']?>">

		                <?php 
		                	if(isset($search_params['search_params'])):
		                ?>
		            		<input type="hidden" name="search_params" value="<?=$search_params['search_params']?>">

		            	<?php else:?>
		            		<input type="hidden" name="additional_info" value="<?=$search_params['additional_info']?>">
		            		<input type="hidden" name="inclusions" value="<?=$search_params['inclusions']?>">
	            			<input type="hidden" name="exclusions" value="<?=$search_params['exclusions']?>">
            				<input type="hidden" name="short_desc" value="<?=$search_params['short_desc']?>">
            				<input type="hidden" name="voucher_req" value="<?=$search_params['voucher_req']?>">
		            	<?php endif;?>
		            	
	                  	<input type="hidden" name="search_id" value="<?=$search_params['search_id']?>">

                        <input type="hidden" name="product_code" id="product_code" value="<?=$tourgrade_list['ProductDetails']['ProductCode']?>">

		                 <input type="hidden" name="product_title" id="product_title" vlaue="<?=$tourgrade_list['ProductDetails']['ProductName']?>">

		                 <input type="hidden" name="grade_title" id="grade_title" value="<?=$t_value['gradeTitle']?>">

		                 <input type="hidden" name="grade_code" id="grade_code" value="<?=$t_value['gradeCode']?>">

		                 <input type="hidden" name="grade_desc" id="grade_desc" vlaue="<?=$t_value['gradeDescription']?>">
		                 <input type="hidden" name="booking_date" id="booking_date" value="<?=$t_value['bookingDate']?>">

				         <input type="hidden" name="tour_uniq_id" value="<?=$t_value['TourUniqueId']?>">

				         <input type="hidden" name="age_band" id="age_band_<?=$t_key?>" value="<?php echo base64_encode(json_encode($t_value['AgeBands']))?>">

				        <input type="hidden" name="op" value="block_trip">
		                <button type="submit"  class="sight_book">Book</button>
		                </form>
		                </div>
		               </div>
		               <?php } ?>
		            </div>
		        </div>
	       </div> 
	    <?php endforeach; ?>
	<?php endif;?>
      </div>

	  </div>
    </div>
  </div>
</div>
<?php
	
	$selected_date_str = base64_encode(json_encode($product_date_arr,JSON_FORCE_OBJECT));
	
?>

