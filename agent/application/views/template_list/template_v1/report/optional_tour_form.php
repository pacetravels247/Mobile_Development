<!-- Mail - Voucher  starts-->
<?php 

$pre_booking_params = (array)json_decode(base64_decode($temp_booking_details[0]['book_attributes']['pre_booking_params']));
//debug($pre_booking_params);
$adult_array=explode('|',$pre_booking_params['sel_adult_count']);
$child_wb_array=explode('|',$pre_booking_params['sel_child_wb_count']);
$child_wob_array=explode('|',$pre_booking_params['sel_child_wob_count']);
$infant_array=explode('|',$pre_booking_params['sel_infant_count']);

array_shift($adult_array);
array_shift($child_wb_array);
array_shift($child_wob_array);
array_shift($infant_array);

$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->Package_Model->tours_city_name();

?>
<div class="modal fade modal-pass-details" id="optional_tour_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			<?=$booking_package_details['package_name']?>(<?=$booking_package_details['tour_code']?>)
		</h4>
      </div>
	  <?php //debug($booking_package_details);  
		$city_text='';
		$package_city=explode(',',$booking_package_details['tours_city']);
		foreach($package_city as $pack_city){
			$city_text.=$city_list[$pack_city].',';
		}
	  
	  ?>
      <div class="modal-body">
 <div class="container-fluid id-optional-details nopad">
      <div class="col-sm-12 nopad">
        <div class="row id-jour-details">
          <div class="col-sm-4 nopad">
            <?php //debug($booking_package_details);  
            $city_text='';
            $package_city=explode(',',$booking_package_details['tours_city']);
            foreach($package_city as $pack_city){
              $city_text.=$city_list[$pack_city].',';
            }?>
            <p><span><i class="fa fa-flag"></i> &nbsp; <strong> Destination : </strong></span> &nbsp;<?=$city_text?></p>
          </div>
          <div class="col-sm-4 nopad">
            <p><span><i class="fa fa-calendar"></i> &nbsp; <strong> Departure : </strong></span> &nbsp;<?=$attributes['departure_date']?></p>
          </div>
          <div class="col-sm-4 nopad">
            <p><span><i class="far fa-clock"></i> &nbsp; <strong> Duration : </strong></span> &nbsp;<?= $booking_package_details['duration']+1 . ' Days / ' . ( $booking_package_details['duration'] ) . (( $booking_package_details['duration']==1)?'  Night': ' Nights'); ?></p>
          </div>
        </div>
      </div>
      <div class="col-sm-12 mem_details">
        <div class="col-sm-2">
          <p>
            <i class="fa fa-home"></i> &nbsp; Rooms: &nbsp;<?=$attributes['roomCount']?>
          </p>
        </div>
          <div class="col-sm-2">
            <p>
            <i class="fa fa-user"></i> &nbsp; Adult: &nbsp;<?=$attributes['adult_count']?>
            </p>
        </div>
          <div class="col-sm-3">
            <p>
            <i class="fa fa-child"></i> &nbsp; Child(with bed) : &nbsp;<?=$attributes['child_count']?>
            </p>
        </div>
          <div class="col-sm-3">
            <p>
            <i class="fa fa-child"></i> &nbsp; Child(without bed)  : &nbsp;<?=@$attributes['child_wob_count']?>
        </p>
        </div>
          <div class="col-sm-2">
            <p>
            <i class="far fa-child"></i> &nbsp; Infant : &nbsp;<?=$attributes['infant_count']?>
        </p>
        </div>
    </div>
  </div>
 <h4 class="o-h4">Optional Services</h4>
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
      <!--<div class="modal-footer"><button class="btn btn-danger">Update booking options</button></div>-->
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->
<style type="text/css">
.col-sm-12.hdr {
    background: #eee;
    padding: 15px;
    border: none!important;
}
.col-sm-12.mem_details {
    background: #eee;
    padding: 15px;
    /*margin: 10px 0px;*/
    border-radius: 4px;
    border: none!important;
    margin-bottom: 10px;
}
.col-sm-12.hdr p {
    font-size: 14px;
    text-transform: capitalize;
    margin: 0;
}
/*.tbl .col-sm-4, .tbl .col-sm-3 {
    padding: 10px;
    border: 2px solid #ccc;
    height: 50px;
}*/
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
.id-optional-details .mem_details p{
  margin: 0;
}
.o-h4{
  color: #0094ce;
}
.id-jour-details span{
  color: #666;
}
</style>