
<?php
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait</div>';
$no_of_nights = $raw_hotel_list['search_data']['no_of_nights'];
$no_of_rooms = $raw_hotel_list['search_data']['room_count'];
$loading_image		 = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="bounce4"></div></div>';
//debug($raw_hotel_list['HotelSearchResult']['HotelResults']);exit;
foreach ($raw_hotel_list['HotelSearchResult']['HotelResults'] as $hd_key=> $hd) {
	$current_hotel_rate = $hd['StarRating'];
	$hotel_code = preg_replace("/[^a-zA-Z0-9]/", "",$hd['HotelCode']);
	//check image exists in that url or not
	//$file_headers = @get_headers($hd['HotelPicture']);
	$image_found=1;
	// if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
	// 	$image_found = 0;
	// }
?>
<div class="rowresult r-r-i item">
	<div class="madgrid forhtlpopover shapcs id-padding-10" id="result_<?=$hd_key?>" data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" data-access-key="<?=@$hd['Latitude'].'_'.@$hd['Longitude']?>">
		<div class="col-xs-2 nopad listimage full_mobile">
			<div class="imagehtldis">
				<?php 
                                $search_id = intval($attr['search_id']);	
                                if($hd['HotelPicture']&&$image_found==true):?>
<?php  ?>
<!-- <img src="" alt="Hotel img" data-src="<?php //echo base_url().'index.php/hotel/image_cdn/'.$hd['ResultIndex'].'/'.$search_id.'/'.base64_encode($hd['HotelCode'])?>" class="lazy h-img"> -->
					<img src="<?=$hd['HotelPicture']?>" alt="Hotel Image" data-src="<?=$hd['HotelPicture']?>" class="lazy h-img load-image hide id-hotel-search-img">

					<img src="<?=$GLOBALS['CI']->template->template_images('image_loader.gif')?>" class='loader-image'>

					<!-- <img src="" alt="Hotel img" data-src="<?php echo base_url().'index.php/hotel/image_hide/'.$hd['ResultIndex'].'/'.$search_id?>" class="lazy h-img"> -->

				<?php else:?>
					<img src="<?=$hd['HotelPicture']?>" alt="Hotel Image" data-src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg') ?>" class="lazy h-img id-hotel-search-img">
				<?php endif;?>
				<?php
					/**
					 * HOTEL PRICE SECTION With Markup price will be returned
					 * 
					 */
					 //Getting RoomPrice from API per night wise					
									
					$RoomPrice = round(($hd['Price']['RoomPrice']/$no_of_nights)/$no_of_rooms);
					//$RoomPrice					= $hd['Price']['RoomPrice'];
					//debug(getimagesize($hd['HotelPicture']));
					?>
				<!-- <img src="" alt="Hotel img" data-src="<?//=$hd['HotelPicture'] ?>" class="lazy h-img"> -->
				<?php if($hd['HotelPicture']&&$image_found==true):?>
				<!-- <a data-target="map-box-modal" data-result-token="<?=urlencode($hd['ResultToken'])?>" data-booking-source="<?=urlencode($booking_source)?>" data-price="<?=$RoomPrice?>" data-star-rating="<?=$current_hotel_rate?>"  data-hotel-name="<?php echo $hd['HotelName']?>" id="map_id_<?=str_replace("!","H",$hd['HotelCode'])?>" data-trip-url="<?=$hd['trip_adv_url']?>" data-trip-rating="<?=$hd['trip_rating']?>" data-id="<?=str_replace("!","H",$hd['HotelCode'])?>" class="hotel-image-gal mapviewhtlhotl fal fa-image view-photo-btn" data-hotel-code="<?=$hd['HotelCode']?>"></a> --> 
				<?php endif;?>
				<a class="hotel_location" data-lat="<?=@$hd['Latitude']?>" data-lon="<?=@$hd['Longitude']?>"></a>
				
			</div>
		</div>
		<div class="col-xs-10 nopad listfull full_mobile">
			<div class="sidenamedesc">
				<div class="celhtl width80">
					<div class="innd">
					   <div class="imptpldz">
						<div class="property-type" data-property-type="hotel"></div>
						<div class="shtlnamehotl">
							 <span class="h-name"><?php echo $hd['HotelName']?></span> 
						</div>
						<span class="result_token hide"><?=urlencode($hd['ResultToken'])?></span>
						<div class="clearfix"></div>
						<div class="starrtinghotl rating-no">
								<span class="h-sr hide"><?php echo $current_hotel_rate?></span>
								<?php
									if($current_hotel_rate <=1){
										echo '<span class="star" style="font-family: none;">Budget Hotel</span>';
									}else{
										echo print_star_rating($current_hotel_rate);
									}
								?>
						</div>
						<div class="adreshotle h-adr" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $hd['HotelAddress']?>"><?php echo $hd['HotelAddress']?>
						</div>
						<div class="preclsdv">
							<?php if(isset($hd['Free_cancel_date'])):?>
								<?php if($hd['Free_cancel_date']):?>
						 		 <span class="canplyto"><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation till:<b><?=local_month_date($hd['Free_cancel_date']);?></b></span>
						 		 <input type="hidden" class="free_cancel" type="text" value="1" data-free-cancel="1">
						 		<?php else:?>
						 			<input type="hidden" class="free_cancel" type="text" value="0" data-free-cancel="0">
						 		<?php endif;?>
						 	<?php else:?>
						 		<input type="hidden" data-free-cancel="0" class="free_cancel" type="text" value="0">
							<?php endif;?>

						</div>

						<div class="bothicntri">
						<div class="mwifdiv">
                           <ul class="htl_spr">                         
				         	<?php if(isset($hd['HotelAmenities'])):?>
				         		<?php if($hd['HotelAmenities']):?>
				         			<?php
				         				//debug($hd['HotelAmenities']);
				         			   	$in_search_params = "".strtolower('wireless')."";
										$in_input = preg_quote(@$in_search_params, '~'); // don't forget to quote input string!
										$internet_result = preg_grep('~' . $in_input . '~', $hd['HotelAmenities']);
										$inn_search_params = "Wi-Fi";
										$inn_input = preg_quote(@$inn_search_params, '~'); 
										$innternet_result = preg_grep('~' . $inn_input . '~', $hd['HotelAmenities']);

										//checking free wifi
										
										$wf_search_params = "Wi";
										$wf_input = preg_quote(@$wf_search_params, '~'); 
										$wf_result = preg_grep('~' . $wf_input . '~', $hd['HotelAmenities']);

										$b_search_params = "".strtolower('breakfast')."";
										$b_input = preg_quote(@$b_search_params, '~'); 
										$b_result = preg_grep('~' . $b_input . '~', $hd['HotelAmenities']);
										//checking breakfast 
										$bf_search_params = "Breakfast";
										$bf_input = preg_quote(@$bf_search_params, '~'); 
										$bf_result = preg_grep('~' . $bf_input . '~', $hd['HotelAmenities']);

										$p_search_params = "".strtolower('parking')."";
										$p_input = preg_quote(@$p_search_params, '~'); 
										$p_result = preg_grep('~' . $p_input . '~', $hd['HotelAmenities']);
										//car parking
										$cp_search_params = "".strtolower('park')."";
										$cp_input = preg_quote(@$cp_search_params, '~'); 
										$cp_result = preg_grep('~' . $cp_input . '~', $hd['HotelAmenities']);

										$s_search_params = "pool";
										$s_input = preg_quote(@$s_search_params, '~'); 
										$s_result = preg_grep('~' . $s_input . '~', $hd['HotelAmenities']);
										$swim = "Swim";
								
										$sw_input = preg_quote(@$swim, '~'); 
										$sw_result = preg_grep('~' . $sw_input . '~', $hd['HotelAmenities']);
				         			?>
				         				<?php if($internet_result||$innternet_result|| $wf_result):?>
				         					<li class="wf" data-toggle="tooltip" data-placement="top" title="Wifi"><span>Wifi</span></li>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         				<?php endif;?>
				         				<?php if($b_result||$bf_result):?>
				         					<li class="bf" data-toggle="tooltip" data-placement="top" title="Breakfast"><span>Breakfast</span></li>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         				<?php endif;?>
				         				<?php if($p_result || $cp_result):?>
				         						 <li class="pr" data-toggle="tooltip" data-placement="top" title="Parking"><span>Parking</span></li>
		         						 		<input type="hidden" value="filter" id="parking" data-parking ="1" class="parking">
		         						<?php else:?>
		         								<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         				<?php endif;?>
				         				<?php if($s_result||$sw_result):?>
				         						 <li class="sf" data-toggle="tooltip" data-placement="top" title="Swimming pool"><span>Swimming pool</span></li>
				         						 <input type="hidden" value="filter" id="pool" class="pool" data-pool="1">
				         				<?php else:?>
				         					 <input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         				<?php endif;?>			         			 
				         			<?php else:?>
				         				<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
						         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
						         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
						         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         		<?php endif;?>
				         	<?php else:?>
				         		<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         	<?php endif;?>
                           
                           </ul>
						</div>

						<?php if(isset($hd['trip_adv_url'])&&empty($hd['trip_adv_url'])==false):?>
						  <div class="tripad">
						    <a href="#"><img src="<?=$hd['trip_adv_url']?>"></a>
						    <span>Rating <?=$hd['trip_rating']?></span>
						  </div>
						<?php endif;?>
						 </div>
						 
						
						<div class="mwifdiv">
								<ul class="htl_brd">                         
								<?php if(isset($hd['RoomDetails'])):?>
				         		<?php if($hd['RoomDetails']):?>
				         			<?php
				         				
										
										//checking room only 
										$ro_search_params = "".strtolower('Room Only')."";
										$ro_input = preg_quote(@$ro_search_params, '~'); 
										$ro_result = preg_grep('~' . $ro_input . '~', $hd['RoomDetails']);
										
										//checking breakfast 
										$bf_search_params = "".strtolower('Breakfast')."";
										$bf_input = preg_quote(@bf_search_params, '~'); 
										$bf_result = preg_grep('~' . $bf_input . '~', $hd['RoomDetails']);
										
										//checking half board 
										$hb_search_params = "".strtolower('Half Board')."";
										$hb_input = preg_quote(@hb_search_params, '~'); 
										$hb_result = preg_grep('~' . $hb_input . '~', $hd['RoomDetails']);
										
										//checking full board 
										$fb_search_params = "".strtolower('Full Board')."";
										$fb_input = preg_quote(@fb_search_params, '~'); 
										$fb_result = preg_grep('~' . $fb_input . '~', $hd['RoomDetails']);
										
				         			?>
				         				<?php if(in_array("Room Only", $hd['RoomDetails'])):?>
				         					<li class="ro" data-toggle="tooltip" data-placement="top" title="Roomonly"><span>Room Only</span></li>
				         					<input type="hidden" value="filter" id="roomonly" class="roomonly" data-roomonly="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="roomonly" class="roomonly" data-roomonly="0">
				         				<?php endif;?>
				         				<?php if(in_array("Breakfast", $hd['RoomDetails'])):?>
				         					<li class="bf" data-toggle="tooltip" data-placement="top" title="break_fast"><span>Breakfast</span></li>
				         					<input type="hidden" value="filter" id="break_fast" class="break_fast" data-break_fast="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="break_fast" class="break_fast" data-break_fast="0">
				         				<?php endif;?>
				         				<?php if(in_array("Half Board", $hd['RoomDetails'])):?>
				         						 <li class="hb" data-toggle="tooltip" data-placement="top" title="Halfboard"><span>Half Board</span></li>
		         						 		<input type="hidden" value="filter" id="halfboard" data-halfboard ="1" class="halfboard">
		         						<?php else:?>
		         								<input type="hidden" value="filter" id="halfboard" class="halfboard" data-halfboard="0">
				         				<?php endif;?>
				         				<?php if(in_array("Full Board", $hd['RoomDetails'])):?>
				         						 <li class="fb" data-toggle="tooltip" data-placement="top" title="Fullboard"><span>Full Board</span></li>
				         						 <input type="hidden" value="filter" id="fullboard" class="fullboard" data-fullboard="1">
				         				<?php else:?>
				         					 <input type="hidden" value="filter" id="fullboard" class="fullboard" data-fullboard="0">
				         				<?php endif;?>			         			 
				         			<?php else:?>
				         				<input type="hidden" value="filter" id="roomonly"  class="roomonly" data-roomonly="0">
						         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
						         		<input type="hidden" value="filter" id="halfboard" class="halfboard" data-halfboard="0">
						         		<input type="hidden" value="filter" id="fullboard" class="fullboard" data-fullboard="0">
				         		<?php endif;?>
				         	<?php else:?>
									<input type="hidden" value="filter" id="roomonly"  class="roomonly" data-roomonly="0">
									<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
									<input type="hidden" value="filter" id="halfboard" class="halfboard" data-halfboard="0">
									<input type="hidden" value="filter" id="fullboard" class="fullboard" data-fullboard="0">
				         	<?php endif;?>
                           
                           </ul>
						</div>

						
						 
						 
						 
						 
						 
						 
						 
						 
						 
						 
						 
						 
						</div>
						<div class="maprew">
						  <div class="hoteloctnf">
						  <a href="<?php echo base_url().'index.php/hotel/map?lat='.$hd['Latitude'].'&lon='.$hd['Longitude'].'&hn='.urlencode($hd['HotelName']).'&sr='.intval($hd['StarRating']).'&c='.urlencode($hd['HotelLocation']).'&price='.$RoomPrice.'&img='.urlencode($hd['HotelPicture'])?>" class="location-map  fa fa-map-marker" target="map_box_frame" data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" data-star-rating="<?=$hd['StarRating']?>" data-hotel-name="<?=$hd['HotelName']?>" id="location_<?=$hotel_code?>_<?=$hd_key?>" data-toggle="tooltip" data-placement="top" data-original-title="View Map"></a>
						   </div>
						  
						</div>
					</div>
				</div>
				
				<div class="celhtl width20">
					<div class="sidepricewrp">
				
						<div class="priceflights">
							<strong> <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> </strong>
							<span class="h-p"><?php echo roundoff_number($RoomPrice); ?></span>
							<div class="prcstrtingt">Per Room / Per Night</div>
							<!--<div class="prcstrtingt">Avg / Night</div>-->
						</div>
						<form action="<?php echo base_url().'index.php/hotel/hotel_details/'.($search_id)?>">
						
							<input type="hidden" id="mangrid_id_<?=$hd_key?>_<?=$hotel_code?>" value="<?=urlencode($hd['ResultToken'])?>" name="ResultIndex"  data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" class="result-index ResultIndex">


							<input type="hidden" id="booking_source_<?=$hd_key?>_<?=$hotel_code?>" value="<?=urlencode($booking_source)?>" name="booking_source"  data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" class="booking_source">
							
							<input type="hidden" value="get_details" name="op" class="operation op">

							<input type="hidden" name="hotel_code" value="<?=$hotel_code?>" class="operation hotel_code">
							<input type="hidden" name="result_index" value="<?=$hd_key?>" class="result_index">
							<input type="hidden" name="search_id" value="<?=$search_id?>" class="search_id">
							<!--<button class="confirmBTN b-btn bookallbtn splhotltoy" type="submit">Book</button>-->
						</form>
						<button class="b-btn bookallbtn view_details_btn" type="button" data-toggle="collapse" data-target="#hotelbtn_<?=$hd_key?>" aria-expanded="false" aria-controls="hotelbtn">View Details</button>
						<div class="viewhotlrmtgle hide">
							<button class="vwrums room-btn" type="button">View Rooms</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="collapse" id="hotelbtn_<?=$hd_key?>">
	      <div class="col-md-12">
	        <div class="innertabs">
	        	<h3 class="mobile_view_header">Description</h3>    <!-- <div class="htldesdv">Hotel Description</div> --><div id="hotel-additional-info" class="padinnerntb"><div class="lettrfty short-text"><?=$loading_image?></div>
	    	</div>


	    	<div class="innertabs">
	    		<h3 class="mobile_view_header">Rooms</h3><div class=""><div id="room-list" class="room-list romlistnh short-text1">
	    		<?=$loading_image?>
	    	</div><!-- pop-up -->
	    	<div class="modal fade" id="can-model_<?=$hd_key?>" role="dialog">
	    		<div class="modal-dialog modal-lg">
	    			<div class="modal-content">
	    				<div class="modal-header">
	    					<button type="button" class="close" data-dismiss="modal">×</button>
	    					<h4 class="modal-title">Cancellation Policy</h4>
	    				</div>
	    				<div class="modal-body">
	    					<p class="can-loader hide"></p>
	    					<div class="text-center loader-image">
	    						<img src="/extras/system/template_list/template_v3/images/loader_v3.gif" alt="Loading........">
	    					</div>
	    					<div id="can-model">
	    						<p class="policy_text"></p>
	    					</div>
	    				</div>
	    				<div class="modal-footer">
	    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    				</div>
	    			</div>
	    		</div>
	    	</div><!-- end -->
	    </div>
	    <!-- <div class="show-rooms">
	    	<a href="#" id="show-more-link" class="">Show More Rooms +</a>
	    </div> -->
	</div>
	</div>
	<div class="tab-pane" id="facility"><div class="innertabs"><h3 class="mobile_view_header">Facilities</h3><div class="padinnerntb htlfac_lity"><!-- <div class="hedinerflty">Hotel Facilities</div> --><?=$loading_image?></div></div></div>
</div>
</div>
		<div class="clearfix"></div>
		<!-- <form class="room-form hide">
			<input type="hidden" value="<?=urlencode($hd['ResultToken'])?>" name="ResultIndex" class="result-index">
			<input type="hidden" value="<?=urlencode($booking_source)?>" name="booking_source" class="booking_source">
			<input type="hidden" name="op" value="get_room_details">
			<input type="hidden" name="search_id" value="<?=$search_id?>">
		</form> -->
		
		<div class="clearfix"></div>
		<div class="room-list" style="display:none">
			<div class="room-summ romlistnh">
				<?=$mini_loading_image?>
			</div>
		</div>
		<?php
			//echo $hd['HotelPromotion'];
			if (isset($hd['HotelPromotion']) == true and empty($hd['HotelPromotion']) == false) {?>	
				<div class="gift-tag">
		          <span class="offdiv deal-status" data-deal="<?php echo ACTIVE?>"><?=$hd['HotelPromotion']?>% Off</span>
		        </div>
			<?php } else {?>
				<span class="deal-status hide" data-deal="<?php echo INACTIVE?>"></span>
				<?php
		}?>
	</div>


</div>

<?php

}
?>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();  
	$(document).on('click','.view_details_btn',function(e){
		var ResultIndex=$(this).parents('.sidepricewrp').find('.ResultIndex').val();
		var booking_source=$(this).parents('.sidepricewrp').find('.booking_source').val();
		var hotel_code=$(this).parents('.sidepricewrp').find('.hotel_code').val();
		var search_id=$(this).parents('.sidepricewrp').find('.search_id').val();
		var result_index=$(this).parents('.sidepricewrp').find('.result_index').val();
		var op=$(this).parents('.sidepricewrp').find('.op').val();
		
		
		$.post('<?=base_url();?>hotel/get_hotel_data',{'ResultIndex':ResultIndex,'booking_source':booking_source,'hotel_code':hotel_code,'search_id':search_id,'result_index':result_index,'op':op},function(data)
		{			        	
			$('#hotelbtn_'+result_index).html(data);
		});
	});
});
</script>
<strong class="currency_symbol hide" > <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> </strong>
<script type="text/javascript">
	$(document).ready(function(){
		var default_loader = "<?=$GLOBALS['CI']->template->template_images('image_loader.gif')?>";
		//console.log("default_loader"+default_loader);
		//$(".load-image").attr('src',default_loader);

		setTimeout(function(){
			$(".load-image").removeClass('hide');
			$(".loader-image").addClass('hide');
			//loader-image
		},3000);

	});
</script>

<style type="text/css">
	.romtyprice {
	    color: #473823;
	     font-size: 18px; 
	     width: 100%;
	     font-weight: bold;
	}
	.romtypestd {
	    font-size: 15px;
	}
	.show-more a {
	    color: #39bffb;
	    background: #fff;
	}
	.show-rooms a {
	    color: #39bffb;
	    background: #fff;
	}
	.b-btn.bookallbtn {
		margin-top: 15px;
	}
	.htlrmsdv {
	    font-size: 14px;
	}
	.noof_ave{
		margin: 1px 0;
    	padding: 2px 6px;
    	width: 80%;
    	text-align: center;
	}
	.ninenyt {
    color: #aaa !important;
    font-weight: normal;
}
	.romtyprice {
		margin-top: 10px;
	}
	.innertabs {
		margin-top: 20px;
		margin-bottom: 0;
	}
	.innertabs {
		padding: 0;
	}
	.romsoutdv{
		max-height: 330px;
		overflow: scroll;
	}
	.id-data-list {
		background-color: #fff;
    	margin-bottom: 10px;
    	border: 1px solid #e6e6e6;
    	padding-bottom: 20px;
	}
	.id-data-list .noof_ave{
		width: 100%;
	}
	.allresult{
		overflow: scroll!important;
		height: 1000px;
	}
</style>
