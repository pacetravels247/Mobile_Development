<!-- Mail - Voucher  starts-->
<?php 

$pre_booking_params = (array)json_decode(base64_decode($temp_booking_details[0]['book_attributes']['pre_booking_params']));
$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->tours_model->tours_city_name();

?>
<div class="modal fade" id="optional_tour_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			<?=$booking_package_details['package_name']?>(<?=$booking_package_details['tour_code']?>)
		</h4>
      </div>
	  <?php //debug($attributes);  ?>
      <div class="modal-body">
	<p><i class="far fa-clock"></i> &nbsp; <strong> Duration : </strong> &nbsp;<?= $booking_package_details['duration']+1 . ' Days / ' . ( $booking_package_details['duration'] ) . (( $booking_package_details['duration']==1)?'  Night': ' Nights'); ?></p>
  <hr>
  <p><i class="fa fa-flag"></i> &nbsp; <strong> Destination : </strong> &nbsp;Zurich,Lucerne,Interlaken,Paris</p>
  <hr>
  <p><i class="fa fa-calendar"></i> &nbsp; <strong> Departure : </strong> &nbsp;<?=$attributes['departure_date']?></p>
  
  <div class="col-sm-12 mem_details">
  <div class="col-sm-2"><p><i class="fa fa-home"></i> &nbsp; <strong> Rooms: </strong> &nbsp;<?=$attributes['roomCount']?></p></div>
    <div class="col-sm-2"><p><i class="fa fa-user"></i> &nbsp; <strong> Adult: </strong> &nbsp;<?=$attributes['adult_count']?></p></div>
    <div class="col-sm-3"><p><i class="fa fa-child"></i> &nbsp; <strong> Child(with bed) : </strong> &nbsp;<?=$attributes['child_count']?></p></div>
    <div class="col-sm-3"><p><i class="fa fa-child"></i> &nbsp; <strong> Child(without bed)  : </strong> &nbsp;<?=@$attributes['child_wob_count']?></p></div>
    <div class="col-sm-2"><p><i class="far fa-child"></i> &nbsp; <strong> Infant : </strong> &nbsp;<?=$attributes['infant_count']?></p></div>
  </div>
  <h3>Optional Services</h3>
	<?php if(!empty($selected_optional_tour)){
	
		foreach($selected_optional_tour as $opt_val){
			//debug($opt_val);
			$op_adult_price=$opt_val->adult_price*$attributes['adult_count'];
			$op_child_price=$opt_val->child_price*$attributes['child_count'];
			$op_infant_price=$opt_val->infant_price*$attributes['infant_count'];
			//debug($attributes['adult_count']);debug($opt_val->adult_price);debug($op_adult_price);
	?>
	<div class="sec_box">
		<div class="col-sm-12 hdr">
			<div class="col-sm-3"><p><?=$city_list[$opt_val->city]?></p></div>
			<div class="col-sm-3"><p>price per adult</p></div>
			<div class="col-sm-3"><p>price per child</p></div>
			<div class="col-sm-3"><p>price per infant</p></div>
		</div>
		<div class="col-sm-12 nopad tbl">
			<div class="col-sm-3"><?=$opt_val->tour_name?></div>
			<div class="col-sm-3"><?=$op_adult_price?></div>
			<div class="col-sm-3"><?=$op_child_price?></div>
			<div class="col-sm-3"><?=$op_infant_price?></div>
		</div>
	</div>
		<?php } }else{ ?>
		<div class="sec_box">
			<div class="col-sm-12 hdr">
				<div class="col-sm-3"><p>No Optional Tour Selected</p></div>
			</div>
		</div>
		
	<?php }?>



      </div>
    
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
.tbl .col-sm-4, .tbl .col-sm-3 {
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