<?php 
	$adult_array=explode('|',$sel_adult_count);
	$child_wb_array=explode('|',$sel_child_wb_count);
	$child_wob_array=explode('|',$sel_child_wob_count);
	$infant_array=explode('|',$sel_infant_count);
	$optional_tour=implode(',',$sel_opt_tour);
	array_shift($adult_array);
	array_shift($child_wb_array);
	array_shift($child_wob_array);
	array_shift($infant_array);

	//debug($child_wb_array);
//	echo count($child_wb_array);
?>

<div class="container">
<div class="col-md-12">
		<div class="id-main-optional-div">
			<div class="row text-right"><p class="id-back-to"><a href="<?php echo base_url()?>tours/holiday_package_detail/<?=$package_details[0]['id']?>/<?=$prev_page?>" class="pull_right id-back-s">< Back to package</a></p></div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			<h1><?=$package_details[0]['package_name']?> (<?=$package_details[0]['tour_code']?>)</h1>
			<form action="<?php echo base_url();?>index.php/tours/fare_breakup_details" method="post" id="send_booking">
			
			<div class="hide">
				<input type="hidden" class="pack_id" name="pack_id" value="<?=$package_details[0]['id']?>">
				<input type="hidden" class="sel_departure_date" name="sel_departure_date" value="<?=$sel_departure_date?>">
				<input type="hidden" class="sel_adult_count"  name="sel_adult_count" value="<?=$sel_adult_count?>">
				<input type="hidden" class="sel_child_wb_count" name="sel_child_wb_count" value="<?=$sel_child_wb_count?>">
				<input type="hidden" class="sel_child_wob_count" name="sel_child_wob_count" value="<?=$sel_child_wob_count?>">
				<input type="hidden" class="sel_infant_count" name="sel_infant_count" value="<?=$sel_infant_count?>">
				<input type="hidden" class="sel_room_count" name="sel_room_count" value="<?=$sel_room_count?>">
				<input type="hidden" class="agent_markup" name="agent_markup" value="<?=$agent_markup?>">
				<input type="hidden" class="prev_page" name="prev_page" value="<?=$prev_page?>">
			</div>
			
			
			
			
			
			
			
			
			
			
			
			<div class="row id-dest-o">
				<p><span><i class="fa fa-clock" aria-hidden="true"></i>&nbsp; Duration :</span> <?= $package_details[0]['duration']+1 . ' Days / ' . ( $package_details[0]['duration'] ) . (( $package_details[0]['duration']==1)?'  Night': ' Nights'); ?></p>
				<p><span><i class="fa fa-home" aria-hidden="true"></i>&nbsp; Destination :</span><?=$city?></p>
				<p><span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; Departure Date :</span> <span class="sel_dep_date"><?= $sel_departure_date ?></span></p>
			</div>
			<div class="row id-optional-div text-center">
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-bed" aria-hidden="true"></i> Room : </span><span class="total_room_count"><?=$sel_room_count?></span></p></div>
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-male" aria-hidden="true"></i> Adult : </span><span class="total_adult_count"><?=array_sum($adult_array)?></span></p></div>
				<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (With Bed)</small> :</span><span class="total_child_wb_count"> <?= array_sum($child_wb_array)?></span></p></div>
				<div class="col-sm-3 nopad"><p><span><i class="fa fa-child" aria-hidden="true"></i> Child<small> (Without Bed)</small> :</span><span class="total_child_wob_count"> <?=array_sum($child_wob_array)?></span></p></div>
				<div class="col-sm-2 nopad"><p><span><i class="fa fa-child" style="font-size: 0.8em" aria-hidden="true"></i> Infant : </span><span class="total_infant_count"> <?= array_sum($infant_array)?></span></p></div>
			</div>
			<?php if(!empty($optional_tour_details)) { ?>
			<div class="row id-optional-table">
				<h3>Optional Services</h3>
				
				
				
					
					<?php 
					//error_reporting(E_ALL);
					$city_wise_opt_tour=array();
					foreach($optional_tour_details as $opt_key => $opt_val){
						$city_wise_opt_tour[$opt_val['city_name']][$opt_key]=$opt_val;
					}
					//debug($city_wise_opt_tour); 
					foreach($city_wise_opt_tour as $opt_key => $copt_val){
						
					?>
					<table class="table">
					<tr>
						<th style="text-align:left;"><?=$opt_key?></th>
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
			<div class="row id-optional-btn">
				<div class="col-sm-offset-4 col-sm-4 nopad">
					
				<!-- 	<button class="btn btn-info"><i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;&nbsp;Back </button> -->
					<button class="btn btn-danger form-control">Continue &nbsp;&nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
	<style type="text/css">
	.id-main-optional-div{
		padding: 20px;
		background-color: #fff;
		/*border:1px solid #ccc;*/
		box-shadow: 0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19) !important;
		margin-top: 40px;
	}
   .id-mt-3{
    margin-top: 30px;
   }
   .id-optional-div{
   	padding: 10px;
   	border-radius: 15px;
   	/*background-color: #ccc;*/
   	background: linear-gradient(0deg,#002042,#0a8ec1);
   	color: #fff;
   }
   .id-optional-div span{
   	/*font-weight: bold;*/
   }
   .id-optional-div p{
   	margin: 0;
   	font-size: 15px;
   }
   .id-optional-div small{
   	font-weight: normal;
   }
   .id-dest-o p{
   	font-size: 15px;
   }
   .id-dest-o{
   	padding: 10px;
   	padding-left: 0;
   }
   .id-dest-o span{
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
   .id-optional-table label{
   	cursor: pointer;
   	padding: 0;
   }
   .id-optional-table table, td, th{
   	font-size: 15px!important;
   	border:1px solid #ccc;
   	vertical-align: middle!important;
   }
   .id-optional-table th,td{
   	text-align: center;
   }
   .id-optional-table span{
   	padding-bottom: 15px;
   }
   .id-optional-table table{
   	box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
   }
   .id-optional-table h3{
   	color: #288abc;
   	margin-bottom: 20px;
   	/*text-align: center;*/
   }
   .id-optional-btn button{
   	height: 50px;
   	margin-top: 10px;
   	font-size: 15px;
   }
   .id-optional-table th{
   	background-color: #eee;
   }
   .id-main-optional-div h1{
   	color: #ef1a16;
   	margin-top: 0;
   	text-align: center;
   }
   .content-wrapper{
    background-color:#f7f7f7!important;
   }
   .id-dest-o .fa{
   	color: #0a8ec1;
   	font-size: 14px;
   }
   .id-main-optional-div .id-back-to{
   	color: #288abc;
   	text-align: right;
   	font-size: 15px;
   	cursor: pointer;
   	margin-bottom: 0;
   }
 /*  .row.id-optional-btn {
    display: block;
    width: 100%;
    
    text-align: center;
}
.id-optional-btn .col-sm-4.nopad {
    display: flex;
}
.id-optional-btn button {
    height: 50px;
    font-size: 20px;
    flex: 1 1 0;
    width: 0;
    margin: 0px 5px;
}*/
</style>