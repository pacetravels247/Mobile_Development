
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<?php
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('bootstrap-select.css'), 'media' => 'screen');
$booking_url = $GLOBALS['CI']->hotel_lib->booking_url($params['search_id']);
//error_reporting(E_ALL);
/**
 * Generate all the possible combinations among a set of nested arrays.
 *
 * @param array $data  The entrypoint array container.
 * @param array $all   The final container (used internally).
 * @param array $group The sub container (used internally).
 * @param mixed $val   The value to append (used internally).
 * @param int   $i	 The key index (used internally).
 */
function generate_combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
{
	$keys = array_keys($data);
	if (isset($value) === true) {
		array_push($group, $value);
	}

	if ($i >= count($data)) {
		array_push($all, $group);
	} else {
		$currentKey	 = $keys[$i];
		$currentElement = $data[$currentKey];
		foreach ($currentElement as $val) {
			generate_combinations($data, $all, $group, $val, $i + 1);
		}
	}

	return $all;
}
$clean_room_list			 = '';//HOLD DATA TO BE RETURNED
$_HotelRoomsDetails			 = get_room_index_list($raw_room_list['GetHotelRoomResult']['HotelRoomsDetails']);
$_RoomCombinations			 = $raw_room_list['GetHotelRoomResult']['RoomCombinations'];
$_InfoSource				 = $raw_room_list['GetHotelRoomResult']['RoomCombinations']['InfoSource'];
$common_params_url = '';
$common_params_url .= '<input type="hidden" name="booking_source"	value="'.$params['booking_source'].'">';
$common_params_url .= '<input type="hidden" name="search_id"		value="'.$params['search_id'].'">';
$common_params_url .= '<input type="hidden" name="ResultIndex"		value="'.$params['ResultIndex'].'">';
$common_params_url .= '<input type="hidden" name="op"				value="block_room">';
$common_params_url .= '<input type="hidden" name="GuestNationality"	value="'.ISO_INDIA.'" >';
$common_params_url .= '<input type="hidden" name="HotelName"		value="" >';
$common_params_url .= '<input type="hidden" name="StarRating"		value="">';
$common_params_url .= '<input type="hidden" name="HotelImage"		value="">';//Balu A
$common_params_url .= '<input type="hidden" name="HotelAddress"		value="">';//Balu A
$search_id = $attr['search_id'];
/**
 * Forcing room combination to appear in multiple list format
 */
if (isset($_RoomCombinations['RoomCombination'][0]) == false) {
	$_RoomCombinations['RoomCombination'][0] = $_RoomCombinations['RoomCombination'];
}


//print_r($_RoomCombinations['RoomCombination']);echo "<br>test";
/**
 * FIXME
 * Room Details
 * Currently we are supporting Room Of - FixedCombination
 */
$generate_rm_cm = array();
if ($_InfoSource != 'FixedCombination') {


	//print_r($_RoomCombinations['RoomCombination']);
	foreach ($_RoomCombinations['RoomCombination'] as $key => $value) {
		$rm_com = array();
		/*echo "key "; print_r($key);
		echo "<br>value "; print_r($value['RoomIndex']);*/
		$rm_com = $value['RoomIndex'];
		$generate_rm_cm[] = $rm_com;
	}

	$_RoomComb = generate_combinations($generate_rm_cm);
	//echo "<br><pre>";print_r($_RoomComb);
	$RoomComb_fin = array();
	foreach ($_RoomComb as $key => $value) {
		$RoomComb_fin[$key]['RoomIndex']=$value;
		 /*echo"<br>key"; print_r($key);
		 echo "<bR> VALUE";
		 print_r($value);*/
		}
		$_RoomCombinations['RoomCombination'] = $RoomComb_fin;
	//echo "<pre>";	print_r($RoomComb_fin);
	/*	debug($_RoomCombinations);
	exit;*/
}

/**
 * Forcing room combination to appear in multiple list format
 */
if (isset($_RoomCombinations['RoomCombination'][0]) == false) {
	$_RoomCombinations['RoomCombination'][0] = $_RoomCombinations['RoomCombination'];
}

/**
 * Forcing Room list to appear in multiple list format
 */
if (isset($_HotelRoomsDetails[0]) == false) {
	$_HotelRoomsDetails[0] = $_HotelRoomsDetails;
}

//---------------------------------------------------------------------------Print Combination - START
?>
<?php 
	//---------------------------------------------------------------------------Print Combination - START
	//debug($_HotelRoomsDetails);
	//exit;
foreach ($_RoomCombinations['RoomCombination'] as $__rc_key => $__rc_value) {
	/**
	 * Forcing Combination to appear in multiple format
	 */
	if (valid_array($__rc_value['RoomIndex']) == false) {
		$current_combination_wrapper = array($__rc_value['RoomIndex']);
	} else {
		$current_combination_wrapper = $__rc_value['RoomIndex'];
	}

	$temp_current_combination_count = count($current_combination_wrapper);
	$room_panel_details = $room_panel_summary = $dynamic_params_url = '';//SUPPORT DETAILS
	$total_room_count = count($_HotelRoomsDetails);
	//echo $total_room_count;exit;
	$room_panel_details_refund='';
	$room_panel_details_canc_link='';
	$dynamic_params_url =array();
	foreach ($current_combination_wrapper as $__room_index_key => $__room_index_value) {
		//NOTE : PRINT ROOM DETAILS OF EACH ROOM INDEX VALUE
		$temp_room_details = $_HotelRoomsDetails[$__room_index_value];
		$common_params_url .= '<input type="hidden" name="CancellationPolicy[]"	value="'.$temp_room_details['CancellationPolicy'].'">';//Balu A

		$room_panel_details .='<div class="col-sm-6 nopad">';
		$room_panel_details .='<div class="romsfst">';
		$room_panel_details .=' <span class="romtypestd">'.ucfirst(strtolower($temp_room_details['RoomTypeName'])).'</span>';
		
		$adult_count = 0;
		$child_count = 0;
		foreach ($hotel_search_params['adult_config'] as $a_key => $a_value) {
			$adult_count +=$a_value;
			
		}
		foreach ($hotel_search_params['child_config'] as $a_key => $a_value) {
			
			$child_count +=$a_value;
		}
		if($adult_count>1){
			$room_panel_details .='<span class="noof_adult hide">
			<i class="fa fa-users" aria-hidden="true"></i>'.$adult_count.' <span class="mobile_hide">adults</span></span>';
		}else{
			$room_panel_details .='  <span class="noof_adult hide"><i class="fa fa-user" aria-hidden="true"></i>'.$adult_count.' <span class="mobile_hide">adult</span></span>';
		}
		if($child_count >0){
			if($child_count>1){
				$room_panel_details .='<span class="noof_adult hide"><i class="fa fa-child" aria-hidden="true"></i>'.$child_count.' <span class="mobile_hide">childrens</span></span>';
			}else{
				$room_panel_details .='<span class="noof_adult hide"><i class="fa fa-child" aria-hidden="true"></i>'.$child_count.' <span class="mobile_hide">child</span></span>';
			}
		}
		$room_panel_details .='<span class="noof_adult"><i class="fa fa-hotel" aria-hidden="true"></i><span class="mobile_hide">No Of Rooms:</span>'.$hotel_search_params['room_count'].'</span>';

		$room_panel_details .='</div>';
		$room_panel_details .='</div>';		
		$room_panel_details .='  <div class="col-sm-3 nopad">';
		$room_panel_details .='  <div class="romsfst">';
		//echo $temp_room_details['LastCancellationDate'];
		$today_cancel_date=0;
		if(isset($temp_room_details['LastCancellationDate'])==true&&empty($temp_room_details['LastCancellationDate'])==false){
			if(date('Y-m-d',strtotime($temp_room_details['LastCancellationDate'])) !=date('Y-m-d') && date('Y-m-d',strtotime($temp_room_details['LastCancellationDate'])) > date('Y-m-d')){
				$today_cancel_date=0;
				$room_panel_details_refund .=' <input type="hidden" class="freecanctext" value="1"><span class="romtypefrecan"><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation<p> till '.local_month_date($temp_room_details['LastCancellationDate']).'</p></span>';
			}else{
				//echo $temp_room_details['LastCancellationDate'];
				$today_cancel_date=1;
				$room_panel_details_refund .=' <input type="hidden" class="freecanctext" value="0"><span class="romtypefrecan">Non-Refundable</span>';
			}
		}
		else{
			$today_cancel_date =1;
			$room_panel_details_refund .=' <input type="hidden" class="freecanctext" value="0"><span class="romtypefrecan">Non-Refundable</span>';
		}		
		//echo 'hotel_code-----'.$temp_room_details['HOTEL_CODE'];
		$hotel_code = preg_replace("/[^a-zA-Z0-9]/", "",$temp_room_details['HOTEL_CODE']);
		//debug($temp_room_details);exit;
			$temp_price_details = $GLOBALS['CI']->hotel_lib->update_room_markup_currency($temp_room_details['Price'], $currency_obj, $search_id);
		// debug($temp_price_details);exit;
		$PublishedPrice				= $temp_price_details['PublishedPrice'];
		$PublishedPriceRoundedOff	= $temp_price_details['PublishedPriceRoundedOff'];
		$OfferedPrice				= $temp_price_details['OfferedPrice'];
		$OfferedPriceRoundedOff		= $temp_price_details['OfferedPriceRoundedOff'];
		$RoomPrice					= $temp_price_details['RoomPrice'];
		$non_refundable = 0;
		if(isset($temp_room_details['cancellation_policy_code'])){
			
			if(isset($temp_room_details['non_refundable'])){
				$non_refundable = $temp_room_details['non_refundable'];
			}
			//echo 'policy';
		}
		//debug($temp_room_details['CancellationPolicies']);exit;
		//debug($temp_room_details);
		$no_of_nights = $hotel_search_params['no_of_nights'];
		$night_str = 'Night';
		if($no_of_nights >1){
			$night_str = 'Nights';
		}
		$room_panel_details.='<span class="romtyprice"><i class="" aria-hidden="true"></i>&nbsp;'.$currency_obj->get_currency_symbol($currency_obj->to_currency).' '.$RoomPrice.'</span><p class="ninenyt"> '.$no_of_nights.' '.$night_str.' </p>';
		$room_panel_details_canc_link .='<a id="cancel_'.$hotel_code.'_'.$__rc_key.'" class="shwrmsdv cancel-policy-btn"  data-hotel-code='.$hotel_code.' data-key="'.$__rc_key.'" data-target="#roomCancelModal" data-toggle="modal" 
		data-cancellation-policy="'.base64_encode(json_encode(@$temp_room_details['CancellationPolicies'])).'"
		data-policy-code="'.@$temp_room_details['cancellation_policy_code'].'"
		data-rate-key="'.$temp_room_details['rate_key'].'"
		data-search-id="'.$temp_room_details['SEARCH_ID'].'"
		data-room-price="'.$temp_price_details['RoomPrice'].'"
		data-booking-source="'.$params['booking_source'].'"
		data-tb-search-id="'.$params['search_id'].'"
		data-non-refundable="'.$non_refundable.'" data-today-cancel-date="'.$today_cancel_date.'">View Cancellation Policy</a> <div class="clearfix"></div>';
		if (isset($temp_room_details['Amenities'])) {
			// debug($temp_room_details['Amenities']);exit;
				if($temp_room_details['Amenities']){
						$view_more_btn ='<a data-toggle="collapse" data-target="#datalst_'.$hotel_code.'_'.$__rc_key.'"><span class="noof_view"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;View more</span></a>';
						foreach ($temp_room_details['Amenities'] as $a_key=>$__amenity) {
							//echo $__amenity.'<br/>';
							if($a_key <=1){
								// debug($__amenity);
								$room_panel_details .='<span class="noof_ave" data-toggle="tooltip" data-title="'.$__amenity.'" data-placement="right"><i class="fa fa-check" aria-hidden="true"></i>'.$__amenity.'</span>';
							}				
						}
						if(count($temp_room_details['Amenities'])>2){
							$room_panel_details .=$view_more_btn;
							$room_panel_am_details .='<div id="datalst_'.$hotel_code.'_'.$__rc_key.'" class="collapse"><div class="col-xs-12 nopad">';
							foreach ($temp_room_details['Amenities'] as $m_key => $m_value) {
								
								if($m_key>=2){
									// echo $m_value;
									$room_panel_am_details .='<div class="col-xs-4"><span class="noof_ave" data-toggle="tooltip" data-title="'.$m_value.'" data-placement="right"><i class="fa fa-check" aria-hidden="true"></i>'.$m_value.'</span></div>';
								}
								
							}
							

							$room_panel_am_details .='</div></div>';
						}
						
				}
				
				
		}
		if (isset($temp_room_details['OtherAmennities'])) {
				if($temp_room_details['OtherAmennities']){
					$room_panel_details .='<p>Other Inclusions</p>';
					$view_more_btn ='<a data-toggle="collapse" data-target="#datalst_o_'.$hotel_code.'_'.$__rc_key.'"><span class="noof_view"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;View more</span></a>';
					foreach ($temp_room_details['OtherAmennities'] as $a_key=>$__amenity) {
						//echo $__amenity.'<br/>';
						if($a_key <=1){
							//debug($__amenity);
							$room_panel_details .='<span class="noof_ave" data-toggle="tooltip" data-title="'.$__amenity.'" data-placement="right"><i class="fa fa-check" aria-hidden="true"></i>'.$__amenity.'</span>';
						}				
					}
					if(count($temp_room_details['OtherAmennities'])>2){
						$room_panel_details .=$view_more_btn;
						$room_panel_details .='<div id="datalst_o_'.$hotel_code.'_'.$__rc_key.'" class="collapse">';
						foreach ($temp_room_details['OtherAmennities'] as $m_key => $m_value) {
							if($m_key>=2){
								$room_panel_details .='<span class="noof_ave" data-toggle="tooltip" data-title="'.$__amenity.'" data-placement="right"><i class="fa fa-check" aria-hidden="true"></i>'.$__amenity.'</span>';
							}
							
						}
						

						$room_panel_details .='</div>';
					}
			}
		}
		if(empty($temp_room_details['Amenities'])==true && empty($temp_room_details['OtherAmennities'])==true){
			$room_panel_details .='<span class="noof_ave"><i class="fa fa-check" aria-hidden="true"></i>'.ucfirst($temp_room_details['room_only']).'</span>';
		}
		$room_panel_details .='</div>';
		$room_panel_details .='</div>';

		
		
		$rslt_temp_room_details = $temp_room_details;
		//debug($rslt_temp_room_details);exit;
		$cancelpolicy_string = '';
		$rslt_temp_room_details['RoomTypeName'] = ucfirst(strtolower($rslt_temp_room_details['RoomTypeName']));
		if (intval($__room_index_key) == 0) {
			$temp_book_now_button = '';
			$temp_summary_room_list = array($rslt_temp_room_details['RoomTypeName']);
			$temp_summary_price_list = array($RoomPrice);
		} else {
			$temp_summary_room_list[] = $rslt_temp_room_details['RoomTypeName'];
			$temp_summary_price_list[] = $RoomPrice;
		}

		if (intval($temp_current_combination_count) == intval($__room_index_key+1)) {
			//PIN Summary
			if (valid_array($temp_summary_room_list)) {
				$temp_summary_room_list = implode(' <i class="fa fa-plus"></i> ', $temp_summary_room_list);
			}
			if (valid_array($temp_summary_price_list)) {
				$temp_summary_price_list = array_sum($temp_summary_price_list);
			}			
		}

		$dynamic_params_url[] = $temp_room_details['RoomUniqueId'];
	}//END INDIVIDUAL COMBINATION LOOPING
	$dynamic_params_url = serialized_data($dynamic_params_url);
	$no_of_nights = $hotel_search_params['no_of_nights'];
	$night_str = 'Night';
	if($no_of_nights >1){
		$night_str = 'Nights';
	}
	$temp_dynamic_params_url = '';
	$temp_dynamic_params_url .= '<input type="hidden" name="token" value="'.$dynamic_params_url.'">';
	$temp_dynamic_params_url .= '<input type="hidden" name="token_key" value="'.md5($dynamic_params_url).'">';
	$temp_book_link = '<div class="col-sm-3 nopad">
							<div class="romsfst mobile_bg">
							<form method="POST" action="'.$booking_url.'">
	'.$common_params_url.$temp_dynamic_params_url.'
	
	<button class="b-btn bookallbtn book-now-btn rombtndv" type="submit">Book</button>'.$room_panel_details_canc_link.'
		</form></div>
		</div>';
	
	$clean_room_list .= '<div class="romconoutdv">';
	$clean_room_list .= $room_panel_details.$temp_book_link.$room_panel_details_refund;
	$clean_room_list .=$room_panel_am_details;
$clean_room_list .= '</div>';
}//END COMBINATION LOOPING 
  
  	 $currency_symbol = $currency_obj->get_currency_symbol($currency_obj->to_currency);
  	 $mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>'; 
?>

<div class="romsoutdv">
		  <div class="romsoutsep">
				    <div class="col-xs-4 nopad">
				      <div class="romstitout">Room Type</div>
				    </div>
				   <!--  <div class="col-xs-3 nopad">
				      <div class="romstitout">Rooms</div>
				    </div> -->
				    <div class="col-xs-4 nopad">
				       <div class="romstitout">Available options</div>
				    </div>
				    <div class="col-xs-4 nopad">
				       <div class="romstitout">Total Booking Cost</div>
				    </div>
		  </div>
<!-- start -->
  	<?php echo $clean_room_list;?>
<!-- end -->
</div>



<!-- pop-up -->
<div class="modal fade" id="roomCancelModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Cancellation Policy</h4>
        </div>
        <div class="modal-body">
        	<p class="can-loader hide"><?=$mini_loading_image?></p>
        	<div id='can-model'>
    		      <p class="policy_text"></p>
        	</div>
    
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<!-- end -->
<?php
 Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('bootstrap-select.min.js'), 'defer' => 'defer');
?>
<script>
	$(document).ready(function(){
		$(document).on('click','.free_canc_filter',function(e){
			console.log("Checkb.");		
		});
	});
	 var total_room_count = "<?php echo $total_room_count ?>";
	 if(parseInt(total_room_count)>2){
	 	$("#show-more-link").removeClass('hide');
	 }else{
	 	$("#show-more-link").addClass('hide');
	 }
	$( ".eachroom" ).hover(
	  function() {
		$('.romlistnh').find('.eachroom').addClass('blur');
		$(this).removeClass('blur');
	  }, function() {
		$('.eachroom').removeClass('blur');
	  }
	);


	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});
	$(".toggle-more-details").click(function(){
		$(".toggle-more-details-wrapper", $(this).closest('.eachroom')).toggle();
	});
	$(".cancel-policy-btn").on("click",function(){
			$("#can-model").html('');
			var Hotel_code = $(this).data('hotel-code');
			var room_key = $(this).data('key');
			var Policy_code = $("#cancel_"+Hotel_code+'_'+room_key).data('policy-code');
			var Policy_details = $("#cancel_"+Hotel_code+'_'+room_key).data('cancellation-policy');
			var Rate_key = $("#cancel_"+Hotel_code+'_'+room_key).data('rate-key');
			var Room_price = $("#cancel_"+Hotel_code+'_'+room_key).data('room-price');
			var Non_refundable = $("#cancel_"+Hotel_code+'_'+room_key).data('non-refundable');
			var Search_id = $("#cancel_"+Hotel_code+'_'+room_key).data('search-id');
			var Booking_Source = $("#cancel_"+Hotel_code+'_'+room_key).data('booking-source');			
			var TB_search_id = $("#cancel_"+Hotel_code+'_'+room_key).data('tb-search-id');			
			var Today_Cancel =$("#cancel_"+Hotel_code+'_'+room_key).data('today-cancel-date'); 
			var Currency_symbol = "<?php echo $currency_symbol?>";
			$(".can-loader").removeClass('hide');
			$(".loader-image").removeClass('hide');
			$.ajax({
				url:"<?php echo base_url()?>"+'index.php/ajax/get_cancellation_policy',		
				data:{
					search_id:Search_id,policy_code:Policy_code,rate_key:Rate_key,
					room_price:Room_price,non_refundable:Non_refundable,booking_source:Booking_Source,tb_search_id:TB_search_id,currency_symbol:Currency_symbol,policy_details:Policy_details,today_cancel_date:Today_Cancel
				},
				success:function(res){					
					//$("#policy_summary_"+Hotel_code+'_'+room_key).html(res);
					//document.getElementById("#cancel_"+Hotel_code+'_'+room_key).setAttribute('title',res);
					var text = '<p class="policy_text">'+res+'</p>';
					$("#can-model").html(text);
					$(".can-loader").addClass('hide');
					$(".loader-image").addClass('hide');
				},
				error:function(res){
					console.log("AJAX ERROR");
				}
			})

		});
</script>

<style type="text/css">
	.short-text1 {
    overflow-y: scroll;
    height: 300px;
}
.innertabs
{
	padding:0;
}
.romsoutdv
{
padding: 0;
}
.romtyprice {
    color: #e20000;
    font-size: 20px;
    width: 100%;
    text-align: center;
    float: left;
    font-weight: 500;
     margin: 8px auto 0px;
}
span.lbllbl-2 {
    margin-left: 20px;
}
.rombtndv {
    width: 100%;
    text-align: center;
    float: none;
    margin: 11px auto 0;
    font-size: 14px;
    padding: 5px;
    line-height: 20px;
    border: 1px solid #286090;
    background: #286090;
    max-width: 110px;
}
.shwrmsdv {
    font-size: 13px;
    font-weight: 500;
    display: block;
    float: left;
    margin: 5px 0;
    color: #666;
}
.noof_ave {
    text-align: center;
    float: none;
    width: 100%;
    font-size: 85%;
    color: #fff;
    margin: 2px auto;
    padding: 2px 4px;
    background: #3caf6f;
    border-radius: 3px;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: none;
}
.noof_view {
    float: none;
    text-align: center;
    font-size: 100%;
    color: #473823;
    padding: 3px 0;
    overflow: hidden;
    clear: both;
    display: none;
    width: 100%;
}
.romtypestd {
    font-size: 16px;
    float: left;
    display: block;
    width: 100%;
    text-align: left;
    overflow: hidden;
    color: #222;
    margin: 8px auto 0px;
}
.noof_adult {
    text-align: left;
    float: left;
    font-size: 85%;
    width: 100%;
    font-weight: 400;
    padding: 0;
    color: #333;
}
.romtypefrecan {
    text-align: left;
    float: left;
    width: 100%;
    font-weight: 500;
    background-color: #f3f3f3;
    padding: 2px 6px;
    color: #666;
    font-size: 12px;
}
.shwrmsdv {
    font-size: 85%;
    font-weight: normal;
    display: block;
    margin: 0px auto;
    color: #222;
    float: none;
    text-align: center;
}
p.ninenyt {
    font-size: 85%;
    font-weight: normal;
    display: block;
    margin: 0px auto;
    color: #222;
    float: none;
    text-align: center;
}
  .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        color: #999;
        background: transparent;
        border: 1px solid #ccc;
    }
    .id-content .form-control {
        background: transparent;
    }
    .bootstrap-select > .dropdown-toggle {
        background: transparent;
        border: 1px solid #ccc;
    }
    .p-cancellation {
        /*background-color: #e7f2ff;*/
        background-color: #f3f3f3;
        padding: 2px 6px;
        color: #666;
        font-size: 12px;
    }
    .datein {
        font-size: 13px;
    }
    .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        border: none!important;
    }
    .id-filter-div .dropdown-menu{
        z-index: 111111;
    }
    .id-filter-div small{
        color: #a7a7a7;
    }
    .bootstrap-select > .dropdown-toggle {
        border: none;
    }
</style>
<?php
//---------------------------------------------------------------------------Support Functions - START
//---------------------------------------------------------------------------Support Functions - END

