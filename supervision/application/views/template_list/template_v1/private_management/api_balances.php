<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-shield"></i> Api Balances</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Si No</th>
					<th>Booking Source</th>
					<th>Api Name</th>
					<th>Balance (INR)</th>
					<th>Minimum Balance (INR)</th>
					<th>Credit Limit (INR)</th>
					<th>Due (INR)</th>
					<th>Actions</th>
				</tr>
			<?php
			if (valid_array($suppliers)) {
				$sino = 1;
				foreach ($suppliers as $supplier) {	
					$source_id = $supplier["source_id"];
					$actions="<span class='status'>Direct API</span>";
					if(!isset($balance_details[$source_id])) {
						$balance_details[$source_id] = $supplier;
						$actions = get_action_buttons($source_id);
					}
					$balance_details[$source_id]["name"] = $supplier["name"];
					 ?>
						<tr id="<?php echo $source_id; ?>">
							<td><?php echo $sino; ?></td>
							<td><?php echo $source_id; ?></td>
							<td><?php echo $balance_details[$source_id]["name"]; ?></td>
							<td><?php echo $balance_details[$source_id]["balance"]; ?></td>
							<td><?php echo $balance_details[$source_id]["minimum_balance"]; ?></td>
							<td><?php echo $balance_details[$source_id]["credit_limit"]; ?></td>
							<td><?php echo $balance_details[$source_id]["due_amount"]; ?></td>
							<td><?php echo $actions; ?></td>
						</tr>
				<?php
						$sino++;
				}
			} else {
				echo '<tr><td colspan="4">No Data Found</td></tr>';
			}
			?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>

<div id="edit_balance_modal" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false">
<div class="modal-dialog" role="document">
  <div class="modal-content">
     <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
        <h4 class="modal-title">Supplier Balance Information</h4>
     </div>
<div class="modal-body">
<div id="message"></div>
 <form class="form-horizontal" role="form" id="edit_balance_form">
   <fieldset form="edit_balance_fields">
      <div class="form-group">
      <input type="hidden" id="source_id">

         <label form="balance" for="header_title" class="col-sm-3 control-label">
         Balance</label>                                                
         <div class="col-sm-6">
         <input type="text" id="balance" class="form-control" placeholder="Balance" 
         name="balance" required>                                                
         </div>
      </div>
      <div class="form-group">
         <label form="credit_limit" for="header_icon" class="col-sm-3 control-label">
         Credit Limit</label>                                                
         <div class="col-sm-6">
         <input type="text" id="credit_limit" class="form-control" placeholder="Credit Limit" name="credit_limit" required>
         </div>
      </div>
      <div class="form-group">
         <label form="minimum_balance" for="header_title" class="col-sm-3 control-label">
         Minimum Balance</label>                                                
         <div class="col-sm-6">
         <input type="text" id="minimum_balance" class="form-control" placeholder="Minimum Balance" name="minimum_balance" required>
         </div>
      </div>
   </fieldset>
   <div class="form-group">
      <div class="col-sm-8 col-sm-offset-4"> 
      <button class="btn btn-success" type="submit" id="save_quote_btn">Save</button> <button class=" btn btn-warning " id="reset_form" type="reset">Reset</button></div>
   </div>
 </form>
</div>
 <div class="modal-footer">
 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 </div>
  </div>
  <!-- /.modal-content -->  
</div>
<!-- /.modal-dialog -->
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(document).on("click", ".edit_balance", function(){
		$("#reset_form").click();
		$("#message").text("");
		var source_id = $(this).data("val");

		var current_tr = $(this).closest("tr");
		var balance = current_tr.children("td:nth-child(4)").text().trim();
		var minimum_balance = current_tr.children("td:nth-child(5)").text().trim();
		var credit_limit = current_tr.children("td:nth-child(6)").text().trim();

		$("#balance").val(balance);
		$("#minimum_balance").val(minimum_balance);
		$("#credit_limit").val(credit_limit);

		$("#source_id").val(source_id);
		$("#edit_balance_modal").modal("show");
	});
	$(document).on("submit", "#edit_balance_form", function(e){
		e.preventDefault();
		var source_id=$("#source_id").val();
		var data = $(this).serialize()+"&source_id="+source_id;
		$.ajax({
			url: app_base_url+"index.php/private_management/save_supplier_balance",
			type: "post",
			dataType: "json",
			data: data,
			success: function(response){
				$("#message").html(response.msg);
				$("#message").fadeOut(300).fadeIn(300);
				$("tr#"+source_id).children("td:nth-child(4)").text(response.data.balance);
				$("tr#"+source_id).children("td:nth-child(5)").text(response.data.minimum_balance);
				$("tr#"+source_id).children("td:nth-child(6)").text(response.data.credit_limit);
				$("tr#"+source_id).children("td:nth-child(7)").text(response.data.due_amount);
				setTimeout(function() { $("#edit_balance_modal").modal("hide"); }, 1000);
			}
		});
	});
});
</script>
<?php 
	function get_action_buttons($source_id)
	{
		return "<a class='btn btn-primary edit_balance' data-val='".$source_id."'' href='#'>Update Balance</a>";
	}
?>