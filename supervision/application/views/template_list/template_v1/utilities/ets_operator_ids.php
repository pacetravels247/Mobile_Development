 <?php
//Code here
?>
<div class="box box-danger">
	<div class="box-header with-border">
		<h3 class="box-title"><i class="fa fa-database"></i> ETS Operator List</h3>
		<a href="#" class="add_ets_op btn btn-primary pull-right"><i class="fa fa-plus"></i> Add ETS Operator</a>
	</div>
	<div class="box-body">
		<div class="row">
		<table class="datatables-td datatables table table-striped table-bordered table-responsive"> 
		<thead>
		<tr>
			<th>Sl No</th>
			<th>Operator Id</th>
			<th>Operator Name</th>
			<th>Status</th>
			<th>Actions</th>
		</tr>	
		</thead>
		<tbody>	
			<?php 
				foreach($ets_opts AS $ets_opk => $ets_opv)
				{ ?>
						<tr id="<?php echo $ets_opv["id"]; ?>">
						<td><?php echo ($ets_opk+1); ?></td>
						<td><?php echo $ets_opv["operator_id"]; ?></td>
						<td><?php echo $ets_opv["operator_name"]; ?></td>
						<td><?php echo $ets_opv["status"]; ?></td>
						<td><a href="#" class="edit_ets_op btn btn-primary"><i class="fa fa-pencil"></i></a></td>
						</tr>			
				<?php }
			?>
		</tbody>	
		</table>
		</div>
	</div>
	<!-- /.box-body -->
</div>
<!-- Modal -->
<div id="add_edit_ets_opt" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header head">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add / Edit ETS Operator</h4>
      	</div>
		<form id="ets_op_form" method="post">
      	<div class="modal-body">
			<div class="msg"></div>
        	<input type="hidden" id="ets_tbl_id" value="0">
			<div class="form-group row">    
			<label class="control-label col-sm-3" for="op_id">Operator Id:</label>
				<div class="col-sm-5"> 
					  <input type="text" class="form-control invalid-ip" name="operator_id" id="operator_id" placeholder="Operator Id" required="required">
				</div>
			</div>
			<div class="form-group row">    
			<label class="control-label col-sm-3" for="op_name">Operator Name:</label>
				<div class="col-sm-5"> 
					  <input type="text" class="form-control invalid-ip" name="operator_name" id="operator_name" placeholder="Operator Name" required="required">
				</div>
			</div>
			<div class="form-group row">    
			<label class="control-label col-sm-3" for="status">Status:</label>
				<div class="col-sm-5"> 
					  <select class="form-control invalid-ip" name="status" id="status" placeholder="Status" required="required">
						<option value="1">Active</option>
						<option value="0">In Active</option>
					  </select>
				</div>
			</div>
      	</div>
      	<div class="modal-footer">
			<input type="submit" class="btn btn-primary" value="Save">
        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	</div>
		</form>
    </div>

  </div>
</div>
<script>
$(document).ready(function() {
	$('.datatables-td').DataTable();
	
	$(".add_ets_op").click(function(){
		$("#add_edit_ets_opt").modal("show");
	});
	$(document).on("click", ".edit_ets_op", function(){
		var trid = $(this).closest("tr").attr("id");
		var operator_id = $("tr#"+trid).children("td:nth-child(2)").text();
		var operator_name = $("tr#"+trid).children("td:nth-child(3)").text();
		var status = $("tr#"+trid).children("td:nth-child(4)").text();
		$("#ets_tbl_id").val(trid);
		$("#operator_id").val(operator_id);
		$("#operator_name").val(operator_name);
		$("#status").val(status);
		$("#status").trigger('change');
		$("#add_edit_ets_opt").modal("show");
	});
	$("#ets_op_form").submit(function(e){
		e.preventDefault();
		var id=$("#ets_tbl_id").val();
		var data = $("#ets_op_form").serialize();
		$.ajax({
			url: app_base_url+"index.php/utilities/save_ets_operator/"+id,
			dataType: "json",
			type: "post",
			data: data,
			success: function(res){
				$(".msg").removeClass("alert");
				$(".msg").removeClass("alert-success");
				$(".msg").removeClass("alert-danger");
				$(".msg").addClass("alert");
				if(res.status==1){
					$("tr#"+id).children("td:nth-child(2)").text(res.data.operator_id);
					$("tr#"+id).children("td:nth-child(3)").text(res.data.operator_name);
					$("tr#"+id).children("td:nth-child(4)").text(res.data.status);
					$(".msg").addClass("alert-success");
				}
				else
					$(".msg").addClass("alert-danger");
				
				$(".msg").html(res.msg);
				$(".msg").fadeOut(200).fadeIn(200);
				setTimeout(function() { $("#add_edit_ets_opt").modal("hide"); }, 2000);
			}
		});
	});
});
</script>
<style type="text/css">
.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
	}
</style>