<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title pull-left"><i class="fa fa-credit-card"></i>  
			Convenience Fees</div>
			<div class="pull-right">
				<a href="#" class="btn btn-primary" id="add_bank_tp" data-toggle="modal" data-target="#add_bank_to_np_modal">Add Bank to Net Banking</a>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div id="message"></div>
			<div class="table-responsive" id="checkbox_div">
				<table class="table table-striped icf_table">
					<tr>
						<th>Sl.No</th>
						<th>Payment Mode</th>
						<th>Bank Code</th>
						<th>Bank Name</th>
						<th>Value Type</th>
						<th>Value</th>
						<th>From Amount</th>
						<th>To Amount</th>
						<th>Pace Fees</th>
						<th>Actions</th>
					</tr>
					<?php
					$i=1;
					foreach($icfs AS $icf) { ?>
						<tr id="icf_<?=$icf["id"]?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $icf["payment_mode"]; ?></td>
						<td><?php echo $icf["bank_code"]; ?></td>
						<td><?php echo $icf["bank_name"]; ?></td>
						<td><?php echo $icf["value"]; ?></td>
						<td><?php echo $icf["value_type"]; ?></td>
						<td><?php echo $icf["from_amount"]; ?></td>
						<td><?php echo $icf["to_amount"]; ?></td>
						<td><input type="text" value="<?=$icf["pace_fees"]?>" id="pace_fees"></td>
						<td><a href="#" class="btn btn-primary update_fees_btn">Update</a>
						<a href="#" class="btn btn-primary delete_fees_btn">Delete</a>	
						</td>
						</tr>
					<?php $i++; } ?>
				</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<!-- MODAL TO UPDATE CREDENTIALS -->
<div id="add_bank_to_np_modal" class="modal fade in">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
            <h4 class="modal-title">Add Banks To Netbanking</h4>
         </div>
         <div class="modal-body">
            <!-- Here the data will be loaded by jquery -->
            <div id="modal_message"></div>
                  <form class="form-horizontal" role="form" id="add_bank_to_np_form">
                  <input type="hidden" name="value_type" id="value_type" value="plus"/>
                  <input type="hidden" name="payment_mode" id="payment_mode" value="net_banking"/>
                  <input type="hidden" name="from_amount" id="from_amount" value="0"/>
                  <input type="hidden" name="to_amount" id="to_amount" value="1000000"/>
                     <fieldset id="add_bank_to_np_fields">
                       <div class="form-group">
							<label class="col-sm-3 control-label">Bank Name</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" placeholder="Bank Name" name="bank_name">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Bank Code</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" placeholder="Bank Code" name="bank_code">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Convenience Fees</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" placeholder="Convenience Fees" name="value">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Pace Fees</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" placeholder="Pace Fees" name="pace_fees">
							</div>
						</div>
                     </fieldset>
                     <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-4"> 
                           <button class="btn btn-success" type="submit" id="save_supp_cred_btn">Save</button>
                           <button class=" btn btn-warning " id="reset_form" type="reset">Reset</button>
                        </div>
                     </div>
                  </form>
         </div>
               <div class="modal-footer"> <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> </div>
      </div>
   </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on("click", ".update_fees_btn", function(){
			var tr_id = $(this).closest("tr").attr("id");
			var row_id = tr_id.split("_")[1];
			var pace_fees = $("#"+tr_id).find("#pace_fees").val();
			var data = "pace_fees="+pace_fees+"&id="+row_id;
			$.ajax({
				url: app_base_url+"index.php/utilities/update_instant_recharge_convenience_fees",
				type: "post",
				data: data,
				dataType: "json",
				success: function(resp){
					$("#message").html(resp.msg);
				}
			});
		});
		$(document).on("click", ".delete_fees_btn", function(){
			if(!confirm('Are you sure you want to delete?'))
				return false;
			var tr_id = $(this).closest("tr").attr("id");
			var row_id = tr_id.split("_")[1];
			var data = "id="+row_id;
			$.ajax({
				url: app_base_url+"index.php/utilities/delete_instant_convenience_fees",
				type: "post",
				data: data,
				dataType: "json",
				success: function(resp){
					$("#message").html(resp.msg);
					if(resp.status)
						$("#"+tr_id).remove();
				}
			});
		});
		$("#add_bank_to_np_form").submit(function(e){
			e.preventDefault();
			var data = $(this).serialize();
			$.ajax({
				url: app_base_url+"index.php/utilities/add_bank_to_net_banking",
				type: "post",
				data: data,
				dataType: "json",
				success: function(resp){
					$("#modal_message").html(resp.msg);
					$("#modal_message").fadeOut(300).fadeIn(300);
				setTimeout(function() { $("#add_bank_to_np_modal").modal("hide"); }, 1000);
				if(resp.status)
					$(".icf_table tbody").append(resp.new_tr);
				}
			});
		});
	});
</script>