<!-- Mail - Voucher  starts-->
	<div class="modal fade" id="mail_voucher_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			Email E-Ticket
		</h4>
      </div>
      <div class="modal-body">
        	<div id="email_voucher_parameters">
        	        <!--<input type="hidden" id="mail_voucher_app_reference" class="hiddenIP">
					<input type="hidden" id="mail_voucher_booking_source" class="hiddenIP">
					<input type="hidden" id="mail_voucher_booking_status" class="hiddenIP">-->
					<input type="email" id="voucher_recipient_email" class="form-control" value="" required="required" placeholder="Enter Email">
					<p>Copy of E-Ticket will be sent to the above Email Id</p>
					<div class="row">
						<div class="col-md-4">
							<input type="button" value="SEND" class="btn btn-success" id="send_mail_btn">
						</div>
						<div class="col-md-8">
							
							<strong id="mail_voucher_error_message" class="text-danger"></strong>
						</div>
					</div>
				</div>
      </div>
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->