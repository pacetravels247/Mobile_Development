<!-- Mail - Voucher  starts-->
<?php 


$payment_history=$booking_payment_history_details;
//debug($payment_history);exit;
//array_shift($adult_array);
//array_shift($child_wb_array);
//array_shift($child_wob_array);
//array_shift($infant_array);
//debug();
$selected_optional_tour = (array)$pre_booking_params['optional_tour_details'];
$city_list=$this->tours_model->tours_city_name();

?>
<div class="modal fade" id="payment_details_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title opt_tr_hd" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			<?=$booking_package_details['package_name']?>(<?=$booking_package_details['tour_code']?>) - Payment Details
		</h4>
      </div>
	  <?php //debug($attributes);  ?>
      <div class="modal-body">
		<div class="sec_box">
			<div class="col-sm-12 hdr">
				<div class="col-sm-3"><p>Sl .No</p></div>
				<div class="col-sm-3"><p>Date</p></div>
				<div class="col-sm-3"><p>Amount</p></div>
				<div class="col-sm-3"><p>Pending</p></div>
			</div>
			<?php
				foreach($payment_history as $pay_key => $pay_val){
			?>
	
			<div class="col-sm-12 nopad tbl">
				<div class="col-sm-3"><?=$pay_key+1?></div>
				<div class="col-sm-3"><?=date('d M Y',strtotime($pay_val['payment_date']))?></div>
				<div class="col-sm-3"><?=number_format($pay_val['paid_amount'],2)?></div>
				<div class="col-sm-3"><?=number_format($pay_val['remaining'],2)?></div>
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
.modal-body {
    position: relative;
    padding: 15px 15px 30px;
}
</style>