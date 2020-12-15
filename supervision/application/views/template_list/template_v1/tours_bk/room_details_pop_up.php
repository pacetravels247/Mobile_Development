<!-- Mail - Voucher  starts-->
<?php 

$pre_booking_params = (array)json_decode(base64_decode($temp_booking_details[0]['book_attributes']['pre_booking_params']));
$adult_array=explode('|',$pre_booking_params['sel_adult_count']);
$child_wb_array=explode('|',$pre_booking_params['sel_child_wb_count']);
$child_wob_array=explode('|',$pre_booking_params['sel_child_wob_count']);
$infant_array=explode('|',$pre_booking_params['sel_infant_count']);

//array_shift($adult_array);
//array_shift($child_wb_array);
//array_shift($child_wob_array);
//array_shift($infant_array);
//debug();
$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->tours_model->tours_city_name();

?>
<div class="modal fade" id="room_details_pop_up_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			<?=$booking_package_details['package_name']?>(<?=$booking_package_details['tour_code']?>) - Passanger Details
		</h4>
      </div>
	  <?php //debug($attributes);  ?>
      <div class="modal-body">
		<div class="sec_box">
			<div class="col-sm-12 hdr">
				<div class="col-sm-2"><p>Room</p></div>
				<div class="col-sm-2"><p>Adult</p></div>
				<div class="col-sm-3"><p>Child with Bed</p></div>
				<div class="col-sm-3"><p>Child No Bed</p></div>
				<div class="col-sm-2"><p>Infant</p></div>
			</div>
			<?php
				for($i=1;$i<=$attributes['roomCount'];$i++){
			?>
	
				<div class="col-sm-12 nopad tbl">
					<div class="col-sm-2">Room <?=$i?></div>
					<div class="col-sm-2"><?=$adult_array[$i]?></div>
					<div class="col-sm-3"><?=$child_wb_array[$i]?></div>
					<div class="col-sm-3"><?=$child_wob_array[$i]?></div>
					<div class="col-sm-2"><?=$infant_array[$i]?></div>
				</div>
	
			<?php 
				} 
			?>
		</div>
      </div>
      <div class="modal-footer"></div>
     
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->
<style type="text/css">
.col-sm-12.hdr {
    background: #eee;
    padding: 5px;
    border: 1px solid #ccc;
    margin: 2px;
}
.col-sm-12.mem_details {
    background: #eee;
    padding: 15px;
    margin: 10px 0px;
    border-radius: 4px;
    border: 1px solid #ccc;
}
.col-sm-12.hdr p {
    font-size: 14px;
    text-transform: capitalize;
    font-weight: 600;
}
.tbl .col-sm-4, .tbl .col-sm-3, .tbl .col-sm-2 {
    padding: 10px;
    border: 2px solid #ccc;
    height: 50px;
}
.sec_box {
    margin: 10px 0px;
    height: 120px;
}
.opt_tr_hd
{
  font-size: 18px;
}
.modal-footer {
    padding: 15px;
    border-top: 1px solid #e5e5e5;
    text-align: center;
}
.opt_tr_hd {
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
}
</style>