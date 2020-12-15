<!-- Mail - Voucher  starts-->
	<div class="modal fade" id="reset_user_password_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="rup_modal"><i class="fa fa-envelope-o"></i>
			Reset Password
		</h4>
      </div>
      <div class="modal-body">
        	<div id="email_voucher_parameters">
        			<input type="hidden" id="rup_user_id" class="hiddenIP">
					<input type="password" id="rup_user_password" class="form-control" value="" required="required" placeholder="Password">
					<p>Please enter a strong password</p>
					<div class="row">
						<div class="col-md-4">
							<input type="button" value="Update Password" class="btn btn-success" id="rup_submit_btn">
						</div>
						<div class="col-md-8">
							<strong id="rup_error_message" class="text-danger"></strong>
						</div>
					</div>
				</div>
      </div>
    </div>
  </div>
</div>