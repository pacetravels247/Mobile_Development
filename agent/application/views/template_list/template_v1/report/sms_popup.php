<!-- Mail - Voucher  starts-->
	<div class="modal fade" id="sms_voucher_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			Send SMS
		</h4>
      </div>
      <div class="modal-body">
        	<div id="sms_voucher_parameters">
        	        <!--<input type="hidden" id="mail_voucher_app_reference" class="hiddenIP">
					<input type="hidden" id="mail_voucher_booking_source" class="hiddenIP">
					<input type="hidden" id="mail_voucher_booking_status" class="hiddenIP">-->
					<input type="number" id="voucher_recipient_phone" class="form-control" value="" required="required" placeholder="Enter Mobile">
					<p>Booking details will be sent through an SMS</p>
					<div class="row">
						<div class="col-md-4">
							<input type="button" value="SEND" class="btn btn-success" id="send_sms_btn">
						</div>
						<div class="col-md-8">
							<strong id="sms_voucher_error_message" class="text-danger"></strong>
						</div>
					</div>
				</div>
      </div>
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->