<div class="container mb-4 mt-4 m-mt-2">
  <div class="col-md-12">
    <div class="row text-right">
      <p class="id-back-to"><a href="<?php echo base_url()?>index.php/tours/details/<?=$package_details[0]['id']?>/<?=$prev_page?>" class="pull_right id-back-s">< Back to package</a></p>
    </div>
	<form action="<?php echo base_url();?>index.php/tours/fare_breakup_details" method="post" id="send_booking">
	<div class="hide">
		<input type="hidden" class="pack_id" name="pack_id" value="<?=$package_details[0]['id']?>">
		<input type="hidden" class="sel_departure_date" name="sel_departure_date" value="<?=$sel_departure_date?>">
		<input type="hidden" class="sel_adult_count"  name="sel_adult_count" value="">
		<input type="hidden" class="sel_child_wb_count" name="sel_child_wb_count" value="">
		<input type="hidden" class="sel_child_wob_count" name="sel_child_wob_count" value="">
		<input type="hidden" class="sel_infant_count" name="sel_infant_count" value="">
		<input type="hidden" class="sel_room_count" name="sel_room_count" value="1">
		<input type="hidden" class="prev_page" name="prev_page" value="<?=$prev_page?>">
	</div>
	<?php 
		//debug($b2c_tour_price);
		foreach ($b2c_tour_price as $tour_price_fly) {
			$per_person_price[$tour_price_fly['occupancy']]=$tour_price_fly['netprice_price']+$markup_val[0]['value'];
			$per_person_market_price[$tour_price_fly['occupancy']]=$tour_price_fly['market_price'];
			echo "<input type='hidden' class='occ_".$tour_price_fly['occupancy']."' value='".$tour_price_fly['occupancy']."' name='occ_".$tour_price_fly['occupancy']."'>";
		} 
			
			
	?> 
    <div class="id-main-optional-div">
      <?php 
			$package_type=$package_details[0]['package_type'];
		 ?>
      <div class="row">
        <h3 class="div-h3">Rooms & Travellers</h3>
      </div>
      <div class="room_sec">
			<input type="hidden" class="no_room" value="1">
			<input type="hidden" class="room_count" value="1">
            <div class="row room_block">
              <ul class="list-inline">
               <li class="id-room"><p class="room_label">Room 1 </p>
               </li>
               <li>
                  <label>Adult</label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse adult" id="decrease_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number" class="form-control no-padding add-color text-center height-25 decrease_1 increase1 adult_count" name="adult[]" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red adult btn-pluss" id="increase1"   type="button">+</button>
                     </span>
                  </div>
                  <small>Above 12 years</small>
               </li>
                <li>
                  <label>Child <small>(with bed)</small></label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreaseValue_1"  type="button">-</button>
                     </span>
                     <input type="number" name="child_with_bed[]" id="number1" class="form-control no-padding add-color text-center height-25 decreaseValue_1 increaseValue_1 child_wb_count" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red child_wb btn-pluss" id="increaseValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>Below 12 years</small>
               </li>
               <li>
                  <label>Child <small>(without bed)</small></label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreasewoValue_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number2" class="form-control no-padding add-color text-center height-25 decreasewoValue_1 increasewoValue_1 child_wob_count" name="child_without_bed[]" readonly maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red child_wob btn-pluss" id="increasewoValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>Below 12 years</small>
               </li>
               <li>
                  <label>Infant</label>
                  <div class="input-group">
                     <span class="input-group-btn">
                     <button class="btn btn-white btn-minuse" id="decreaseinfValue_1"  type="button">-</button>
                     </span>
                     <input type="number" id="number3" name="infant[]" class="form-control no-padding add-color text-center height-25 decreaseinfValue_1 increaseinfValue_1 infant_count" readonly  maxlength="3" value="0">
                     <span class="input-group-btn">
                     <button class="btn btn-red infant btn-pluss" id="increaseinfValue_1"  type="button">+</button>
                     </span>
                  </div>
                  <small>(0-2 years)</small>
               </li>
            </ul>
            </div>
            
            <hr class="mb-0">
            <div class="row mt-1">
        <div class="col-sm-2 nopad">
          <div class="row">
                  <p class="id-add-rooms" id="add_rooms">+ &nbsp;Add Rooms</p>
                </div>
        </div>
        <div class="col-sm-offset-7 col-sm-3 nopad">
          <!-- <input type="text" name="" class="input-date" placeholder="Select Departure Date"> -->
        </div>
      </div>

          </div>
    </div>
    <div class="id-main-optional-div mt-2 m-mb-5">
      <h3 class="div-h3">Optional Tours</h3>

      <div class="row id-dest-o1">
        <h3><?=$package_details[0]['package_name']?></h3>
        <p><span><i class="fa fa-clock" aria-hidden="true"></i>&nbsp; Duration : </span><?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
        <p><span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Destination : </span><?=$city?></p>
        <p><span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; Departure Date : </span><?= $sel_departure_date ?></p>
      </div>
      <div class="row id-optional-div1 text-center">
        <div class="col-sm-2 col-xs-6 nopad"><p><span><i class="fa fa-bed" aria-hidden="true"></i> Room : </span><span class="select_room">1</span></p>
        </div>
        <div class="col-sm-2 col-xs-6 nopad"><p><span><i class="fa fa-male" aria-hidden="true"></i> Adult : </span><span class="total_adult_count">0</span></p>
        </div>
        <div class="col-sm-3 col-xs-6 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (With Bed)</small> : </span><span class="total_child_wb_count">0</p>
        </div>
        <div class="col-sm-3 col-xs-6 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (Without Bed)</small> : </span><span class="total_child_wob_count">0</span></p>
        </div>
        <div class="col-sm-2 col-xs-12 nopad"><p><span><i class="fa fa-child" style="font-size: 0.8em" aria-hidden="true"></i> Infant : </span><span class="total_infant_count">0</span></p>
        </div>
      </div>
	  <?php if(!empty($optional_tour_details)) { ?>
      <div class="row id-optional-table1">
        <h4>Optional Services</h4>
			<?php 
				//error_reporting(E_ALL);
				$city_wise_opt_tour=array();
				foreach($optional_tour_details as $opt_key => $opt_val){
					$city_wise_opt_tour[$opt_val['city_name']][$opt_key]=$opt_val;
				}
				//debug($city_wise_opt_tour); 
				foreach($city_wise_opt_tour as $opt_key => $copt_val){
						
			?>
			<div class="table-responsive">
				<table class="table">
				  <tr>
					<th style="text-align:left;">Services</th>
					<th>Price Per Adult</th>
					<th>Price Per Child</th>
					<th>Price Per Infant</th>
				  </tr>
				  <?php 
					foreach($copt_val as $copt_key => $opt_val){
					?>
				  <tr>
					<td style="text-align:left;">
						<label for="op3">
						<input type="checkbox" id="op3" name="sel_opt_tour[]" value="<?=$opt_val['opt_id']?>" class="id-optional-check">
	  					<span><?=$opt_val['tour_name']?></span></label>
					</td>
					<td>INR <?=$opt_val['adult_price']?></td>
					<td>INR <?=$opt_val['child_price']?></td>
					<td>INR <?=$opt_val['infant_price']?></td>
				  </tr>
				<?php } 
				?>
				</table>
				<?php
				} ?>
			</div>
			<?php 
				}else{
			?>
				<div class="row id-optional-table">
					<h3>No Optional Services</h3>
				</div>
			<?php
				} 
			?>
        
      </div>
      <div class="row id-optional-btn">
        <div class="col-sm-offset-4 col-sm-4 nopad">
          <button class="btn btn-danger form-control">Continue &nbsp;&nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>
	</form>
  </div>
</div>
<script>   
  $(document).ready(function() {
		var single_share		=$('.occ_8').val();
		var doubl_share			=$('.occ_10').val();
		var triple_share		=$('.occ_14').val();
		var child_with_bed		=$('.occ_11').val();
		var child_without_bed	=$('.occ_12').val();
		var infant				=$('.occ_13').val();
	  $(document).on('click','#add_rooms',function(){
		var number_room= $('.no_room').val();
		var room_count= $('.room_count').val();
		var current_room_count=parseInt(room_count)+1;
		var current_room=parseInt(number_room)+1;
		var room_text = '<ul class="list-inline"><li class="id-room"><p class="room_label">Room '+current_room_count+' </p></li><li><label>Adult</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse adult" id="decrease_'+current_room+'"  type="button">-</button></span><input type="number" name="adult[]" class="form-control no-padding add-color text-center height-25 decrease_'+current_room+' increase'+current_room+' adult_count" readonly maxlength="3" id="number_'+current_room+'" value="0"><span class="input-group-btn"><button class="btn btn-red adult btn-pluss" id="increase'+current_room+'"   type="button">+</button></span></div><small>Above 12 years</small></li><li><label>Child <small>(with bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseValue_'+current_room+'"  type="button">-</button></span><input type="number" name="child_with_bed[]" id="number'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseValue_'+current_room+' increaseValue_'+current_room+' child_wb_count" readonly maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red child_wb btn-pluss" id="increaseValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li><li><label>Child <small>(without bed)</small></label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreasewoValue_'+current_room+'"  type="button">-</button></span><input type="number" name="child_without_bed[]" id="number'+current_room+'" class="form-control no-padding add-color text-center height-25 decreasewoValue_'+current_room+' increasewoValue_'+current_room+' child_wob_count" readonly maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red child_wob btn-pluss" id="increasewoValue_'+current_room+'"  type="button">+</button></span></div><small>Below 12 years</small></li><li><label>Infant</label><div class="input-group"><span class="input-group-btn"><button class="btn btn-white btn-minuse" id="decreaseinfValue_'+current_room+'"  type="button">-</button></span><input type="number" name="infant[]" id="number'+current_room+'" class="form-control no-padding add-color text-center height-25 decreaseinfValue_'+current_room+' increaseinfValue_'+current_room+' infant_count" readonly  maxlength="3" value="0"><span class="input-group-btn"><button class="btn btn-red infant btn-pluss" id="increaseinfValue_'+current_room+'"  type="button">+</button></span></div><small>(0-2 years)</small></li><li class="remove text-danger"><span class="fa fa-minus-circle"></span></li></ul>';
		$('.room_block').append(room_text);
		var number_room=parseInt(number_room)+1;
		var room_count=parseInt(room_count)+1;
		$('.no_room').val(number_room);
		$('.room_count').val(room_count);
		$('.select_room').text(room_count);
		$('.total_room_count').text(room_count);
		if(current_room_count>=4){
			$('#add_rooms').addClass('hide');
		}else{
			$('#add_rooms').removeClass('hide');
		}
	});
	$(document).on('click','.btn-pluss',function(e){
		e.preventDefault();
		var id=$(this).attr('id');
		var quantity = parseInt($('.'+id).val());
		//alert(quantity);
		
		var cwb=$(this).parents('.list-inline').find('.child_wb_count').val();
		var cwob=$(this).parents('.list-inline').find('.child_wob_count').val();
		var adlt=$(this).parents('.list-inline').find('.adult_count').val();
		var infant=$(this).parents('.list-inline').find('.infant_count').val();
		var child=parseInt(cwb)+parseInt(cwob);
		var total_per_room = parseInt(child)+parseInt(adlt)+parseInt(infant);
		if($(this).hasClass('adult')==true){
			var pack_type= "<?php echo $package_type; ?>";
			if(total_per_room >= 4 || adlt >= 3){
				alert("You are exeeding the limit.");
			}else{
				if(pack_type=='fit' && adlt==0){
					$('.'+id).val(quantity + 2);	
	
				}else{
					$('.'+id).val(quantity + 1);
				}
			}
		}
		
		if($(this).hasClass('child_wb')==true){
			if(total_per_room >= 4 || child >= 2){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
		}
		if($(this).hasClass('child_wob')==true){
			if(total_per_room >= 4 || child >= 2){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
		}
		if($(this).hasClass('infant')==true){
			if(total_per_room >= 4 || infant >= 1){
				alert("You are exeeding the limit.");
			}else{
				$('.'+id).val(quantity + 1);		
			}
		}
		var total_adult_count = 0;
		var total_child_wb_count = 0;
		var total_child_wob_count = 0;
		var total_infant_count = 0;
		var is_no_adult=0;
		var adult_text='';
		var cwb_text='';
		var cwob_text='';
		var infant_text='';
		$('.list-inline').each(function( index ) {
			
			total_adult_count+=parseInt($(this).find('.adult_count').val());
			total_child_wb_count+=parseInt($(this).find('.child_wb_count').val());
			total_child_wob_count+=parseInt($(this).find('.child_wob_count').val());
			total_infant_count+=parseInt($(this).find('.infant_count').val());	
			
			
			
			adult_text	= adult_text+'|'+parseInt($(this).find('.adult_count').val());
			cwb_text	= cwb_text+'|'+parseInt($(this).find('.child_wb_count').val());
			cwob_text	= cwob_text+'|'+parseInt($(this).find('.child_wob_count').val());
			infant_text	= infant_text+'|'+parseInt($(this).find('.infant_count').val());
		});
		
		$('.sel_adult_count').val(adult_text);
		$('.sel_child_wb_count').val(cwb_text);
		$('.sel_child_wob_count').val(cwob_text);
		$('.sel_infant_count').val(infant_text);
		
		
		$('.total_adult_count').text(total_adult_count);
		$('.total_child_wb_count').text(total_child_wb_count);
		$('.total_child_wob_count').text(total_child_wob_count);
		$('.total_infant_count').text(total_infant_count);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	});
	$(document).on('click','.remove',function(e){
		e.preventDefault();
		$(this).parents('.list-inline').remove();

		$( ".list-inline" ).each(function( index ) {
			var room_name_count=index+1;
		  $(this).find('.room_label').text('Room '+room_name_count)
		});
		var room_count= $('.room_count').val();
		var room_count=parseInt(room_count)-1;
		$('.room_count').val(room_count);
		$('.select_room').text(room_count);
		$('.total_room_count').text(room_count);
		if(room_count>=4){
			$('#add_rooms').addClass('hide');
		}else{
			$('#add_rooms').removeClass('hide');
		}
	});
	
	$(document).on('click','.btn-minuse',function(e){
		e.preventDefault();
		var id=$(this).attr('id');
		var quantity = parseInt($('.'+id).val());
		var pack_type= "<?php echo $package_type; ?>";
		var adlt=$(this).parents('.list-inline').find('.adult_count').val();
		//alert(quantity);
		if(quantity <= 0){  
			
		}else{
			
			if($(this).hasClass('adult')==true){
				if(pack_type=='fit' && adlt==2){
					$('.'+id).val(quantity - 2);		
				}else{
					$('.'+id).val(quantity - 1);
				}
			}else{
				$('.'+id).val(quantity - 1);		
			}
			
		}
		var total_adult_count = 0;
		var total_child_wb_count = 0;
		var total_child_wob_count = 0;
		var total_infant_count = 0;
		var is_no_adult=0;
		var adult_text='';
		var cwb_text='';
		var cwob_text='';
		var infant_text='';
		$('.list-inline').each(function( index ) {
			
			total_adult_count+=parseInt($(this).find('.adult_count').val());
			total_child_wb_count+=parseInt($(this).find('.child_wb_count').val());
			total_child_wob_count+=parseInt($(this).find('.child_wob_count').val());
			total_infant_count+=parseInt($(this).find('.infant_count').val());	
			
			
			
			adult_text	= adult_text+'|'+parseInt($(this).find('.adult_count').val());
			cwb_text	= cwb_text+'|'+parseInt($(this).find('.child_wb_count').val());
			cwob_text	= cwob_text+'|'+parseInt($(this).find('.child_wob_count').val());
			infant_text	= infant_text+'|'+parseInt($(this).find('.infant_count').val());
		});
		var sel_room_count = $('.room_count').val();
		$('.sel_adult_count').val(adult_text);
		$('.sel_child_wb_count').val(cwb_text);
		$('.sel_child_wob_count').val(cwob_text);
		$('.sel_infant_count').val(infant_text);
		$('.sel_room_count').val(sel_room_count);
		
		$('.total_adult_count').text(total_adult_count);
		$('.total_child_wb_count').text(total_child_wb_count);
		$('.total_child_wob_count').text(total_child_wob_count);
		$('.total_infant_count').text(total_infant_count);
		
		
		
		
		
		
		
		
	});
	$( "#send_booking" ).submit(function(e) {
		var total_adult_count = 0;
		var total_child_wb_count = 0;
		var total_child_wob_count = 0;
		var total_infant_count = 0;
		var is_no_adult=0;
		
		var adult_text='';
		var cwb_text='';
		var cwob_text='';
		var infant_text='';
		var err_text=''
		$('.list-inline').each(function( index ) {
			if($(this).find('.adult_count').val() <=0){
				is_no_adult=1;
			}
			var cur_adult		=parseInt($(this).find('.adult_count').val());
			var cur_child_wb	=parseInt($(this).find('.child_wb_count').val());
			var cur_child_wob	=parseInt($(this).find('.child_wob_count').val());
			var cur_infant		=parseInt($(this).find('.infant_count').val());
			
			if(cur_adult=='1' && typeof single_share =="undefined"){
				err_text=err_text+"Single share not availabe for this package.\n";
			}else if(cur_adult=='2' && typeof doubl_share =="undefined"){
				err_text=err_text+"Double share not availabe for this package.\n";
			}else if(cur_adult=='3' && typeof triple_share =="undefined"){
				err_text=err_text+"Thriple share not availabe for this package.\n";
			}

			if(cur_child_wb!=0 && typeof child_with_bed =="undefined"){
				err_text=err_text+"Child with bed not availabe for this package.\n";
			}
			if(cur_child_wob!=0 && typeof child_without_bed =="undefined"){
				err_text=err_text+"Child with out bed not availabe for this package.\n";
			}
			if(cur_infant!=0 && typeof infant =="undefined"){
				err_text=err_text+"Infant not availabe for this package.\n";
			}
			
			
			total_adult_count+=parseInt($(this).find('.adult_count').val());
			total_child_wb_count+=parseInt($(this).find('.child_wb_count').val());
			total_child_wob_count+=parseInt($(this).find('.child_wob_count').val());
			total_infant_count+=parseInt($(this).find('.infant_count').val());
			
			
			adult_text	= adult_text+'|'+parseInt($(this).find('.adult_count').val());
			cwb_text	= cwb_text+'|'+parseInt($(this).find('.child_wb_count').val());
			cwob_text	= cwob_text+'|'+parseInt($(this).find('.child_wob_count').val());
			infant_text	= infant_text+'|'+parseInt($(this).find('.infant_count').val());
		});
		
		var sel_dep_date=$('#datepicker_dat_group').val();
		if(sel_dep_date=='dd/mm/yyyy'){
			alert("Please select departure date.");
			e.preventDefault();
		}else if(err_text !=''){
			err_text=err_text+" For further details kindly contact customer care.";
			alert(err_text);
			e.preventDefault();
		}
		var sel_room_count = $('.room_count').val();
		$('.sel_dep_date').text(sel_dep_date);
		$('.total_adult_count').text(total_adult_count);
		$('.total_child_wb_count').text(total_child_wb_count);
		$('.total_child_wob_count').text(total_child_wob_count);
		$('.total_infant_count').text(total_infant_count);
		$('.total_infant_count').text(total_infant_count);
		
		
		$('.sel_adult_count').val(adult_text);
		$('.sel_child_wb_count').val(cwb_text);
		$('.sel_child_wob_count').val(cwob_text);
		$('.sel_infant_count').val(infant_text);
		$('.sel_room_count').val(sel_room_count);
		
		
		
		
		
		
		if(is_no_adult ==1){
			alert("Please select atleast one adult per room.");
			e.preventDefault();
		}
	});

  });
</script>
<style type="text/css">
  .id-main-optional-div{
    padding: 20px;
    background-color: #fff;
    /*border:1px solid #ccc;*/
    /*box-shadow: 0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19) !important;*/
    box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
    
  }
  .mt-2{
    margin-top: 20px;
  }
  .mt-4{
    margin-top: 40px;
  }
   .id-mt-3{
    margin-top: 30px;
   }
   .id-optional-div1{
    padding: 10px;
    border-radius: 5px;
    background-color: #fc901b;
    /*background: linear-gradient(0deg,#002042,#0a8ec1);*/
    color: #fff;
   }
   .id-optional-div1 span{
    /*font-weight: bold;*/
   }
   .id-optional-div1 p{
    margin: 0;
    font-size: 15px;
   }
   .id-optional-div1 small{
    font-weight: normal;
   }
   .id-dest-o1 p{
    font-size: 15px;
   }
   .id-dest-o1{
    padding: 10px;
    padding-left: 0;
    margin-top: 20px;
   }
   .id-dest-o1 span{
    font-weight: bold;
   }
   .id-optional-check:not(:checked), .id-optional-check:checked{
    position: relative;
    left: 0; 
    top: 5px;
    width: 40px;
    height: 20px;
    margin-top: 5px!important;
    cursor: pointer;
    
   }
   .id-optional-check:not(:checked) + label:after, .id-optional-check:checked + label:after {
    content: unset!important;
   }
   .id-optional-check{
    margin: 0;
   }
   .id-optional-table1 label{
    cursor: pointer;
    padding: 0;
   }
   .id-optional-table1 table, td, th{
    font-size: 15px!important;
    border:1px solid #ccc;
    vertical-align: middle!important;
   }
   .id-optional-table1 th,td{
    text-align: center;
   }
   .id-optional-table1 span{
    padding-bottom: 15px;
   }
   .id-optional-table1 table{
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   }
   .id-optional-table1 h4{
    color: #1fa9d7;
    margin-bottom: 10px;
    margin-top: 20px;
    /*text-align: center;*/
   }
   .id-optional-btn button{
    height: 50px;
    margin-top: 10px;
    font-size: 15px;
   }
   .id-optional-table1 th{
    background-color: #eee;
   }
   .id-dest-o1 h3{
    color: #ef1a16;
    margin-top: 0;
   }
   .id-main-optional-div .div-h3{
    color: #000;
    text-align: center;
    padding: 10px;
    background-color: #f2f2f2;
    margin-top: 0;
   }
   .content-wrapper{
    background-color:#f7f7f7!important;
   }
   .id-dest-o1 .fa{
    color: #0a8ec1;
    font-size: 14px;
   }
   .id-main-optional-div .id-back-to{
    color: #1fa9d7;
    text-align: right;
    font-size: 15px;
    cursor: pointer;
    margin-bottom: 0;
   }
   .id-dest-o1 p{
    display: inline-block;
    margin-right: 20px;
   }
   .mb-4{
    margin-bottom: 40px;
   }

.list-inline small{
  color: #a9a9a9!important;
}
.room_block{
  text-align: center;
  margin-top: 10px;
}
.list-inline .room_label{
  font-size: 14px;
  color: #ef1a16;
  margin-top: 30px;
  font-weight: bold;
}
.id-add-rooms{
  color: #0a8ec1;
  /*margin-top: 10px;*/
  padding: 8px;
  border:1px solid #ccc;
  border-radius: 15px;
  margin-bottom: 0;
  text-align: center;
  cursor: pointer;
}
.id-add-rooms:hover{
  background-color: #f2f2f2;
}
.room_sec ul li
{
  width: 18%;
  float: left;
  display: inline-block;
}

.nav-tabs>li {
    float: none;
    margin-bottom: 1px;
    border-bottom: 2px solid #fff;
    padding: 10px 10px;
    text-align: center;
}
ul.nav.nav-tabs.responsive-tabs {
    float: left;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
    width: 25%;
    height: 100%;
}
.mb-1{
  margin-bottom: 10px;
}
.input-date{
  width: 100%;
    border: none;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    padding-top: 6px;
    /*margin-top: 10px;*/
}
.m-0{
  margin: 0;
}
.mb-0{
  margin-bottom: 0;
}
.mt-1{
  margin-top: 10px;
}

@media only screen and (max-width: 767px){
  .room_sec ul li {
      width: 46%;
      margin-bottom: 10px;
  }
  li.id-room{
    width: 100%!important;
  }
  .id-optional-div1 p {
    margin: 4px;
    font-size: 12px;
  }
  .id-dest-o1 h3 {
    margin-bottom: 10px;
  }
  .id-dest-o1 p{
    margin-bottom: 10px;
  }
  .m-mb-5{
    margin-bottom: 50px;
  }
  .id-back-to{
    margin-bottom: 10px;
    font-size: 14px;
  }
  .menuandall .menu{
    display: none;
  }
  .m-mt-2{
    margin-top: 20px!important;
  }
  .input-date{
    margin-top: 10px;
  }
  .list-inline .room_label{
    margin-top: 0;
  }
  hr{
    display: none;
  }
  .topssec {
    height: auto!important;
  }
  .section_top {
      padding: 0 0 10px 0px;
  }
  .userimage img {
    border: 1px solid #aaa;
    padding: 2px;
    border-radius: 100%;
    width: 100%;
  }
}
li.remove.text-danger {
    position: relative;
    right: 23px;
    float: right;
    top: 31px;
    width: 20px;
}
span.fa.fa-minus-circle {
    font-size: 18px;
}

</style>