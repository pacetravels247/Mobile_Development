<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-head">
			<div class="clearfix">
				<h3 style="padding-left: 1%">Airline List</h3>
				<div class="col-sm-3">
					<select id="airline_search" class="form-control" name="status" onchange="airline_search(this.value);">
                            <option value="">Airline Name</option>
                            <?php
                                foreach ($airline_code as $key => $value) {
                            ?>
                            <option value="<?=$value['origin'];?>"><?=$value['name']." - ".$value['code'];?></option>
                            <?php } ?>
                        </select>
				</div>
				<div class="pull-right" style="padding-right: 1%" data-toggle="modal" data-target="#myModal">
					<button  class="btn btn-success">Add Airline</button>
				</div>
				
			</div>
		</div>
		<div class="panel-body">
		<?php echo $this->session->flashdata("msg"); ?>
			<div class="clearfix">
				<table id="example" class="display nowrap" style="width:100%">
			        <thead>
			            <tr>
			                <th>Sl No</th>
			                <th>airline_code</th>
			                <th>airline_name</th>
			                <th>Action</th>
			            </tr>
			        </thead>
    			</table>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Details</h4>
      </div>
      <div class="modal-body">
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/add_new_airline" method="POST" enctype = "multipart/form-data">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="code">Airline Code:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="code" id="code" placeholder="Airline Code" required="required" maxlength="3">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airline Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="name" id="name" placeholder="Airline Name" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airline logo:</label>
			    <div class="col-sm-5"> 
			      <input type="file" class="form-control" name="logo" id="logo" placeholder="Airline Logo" required="required">
			    </div>
			  </div>

			  <div class="form-group"> 
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-success">Submit</button>
			    </div>
			  </div>
			</form>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>


<div id="myModal_edit" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Details</h4>
      </div>
      <div class="modal-body">
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/update_new_airline" method="POST" enctype = "multipart/form-data">
			  <input type="hidden" id='o_id' name="o_id">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_code">Airline Code:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="code" id="code_e" placeholder="Airline Code" required="required" maxlength="3">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airline Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="name" id="name_e" placeholder="Airline Name" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airline Logo:</label>
			    <div class="col-sm-5"> 
			      <input type="file" class="form-control" name="logo" id="logo_e">
			    </div>
			  </div>
			  <div class="form-group"> 
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-success">Submit</button>
			    </div>
			  </div>
			</form>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>

<script>
//alert('8');
$(document).ready(function() {
	var query ='';
	build_datatable(query);
	$("#airline_search").select2();
});

/*function filter_agent(user_id){
	var query ='requested_by='+user_id;
	table.destroy();
	build_datatable(query);
}*/

/*function filter_date(selected_date){
	//$this.addclass('active');
	var query ='filter_date='+selected_date;
	table.destroy();
	build_datatable(query);
}*/

$(document).on('click','.edit_details',function(){

	$('#myModal_edit').modal('show');
    $('#o_id').val($(this).data('id')); 
	$('#code_e').val($(this).data('code'));
	$('#name_e').val($(this).data('name'));
	$('#logo_e').val($(this).data('logo'));
})
var query = '';
$(document).on('click','.delete_details',function(){
	var res = confirm("Are you sure, to delete the record ?");
	if(res == true){
	var oid = $(this).data('id');
		$.ajax({
			'url' : app_base_url+"index.php/utilities/delete_airline/",
			'type' : 'POST',
			'data' : {id:oid},
			'success' : function(res){
				table.destroy();
				build_datatable(query);
			}
		})
	}
	
})

var table;
function build_datatable(query){
	table = $('#example').DataTable({
    	"processing": true,
    	"serverSide": true,
        "ajax": {
        	"url":app_base_url+'index.php/utilities/getAirlineLists?'+query,
        	"type":"POST",
        },
        "searching": false
    });
}

function airline_search(airline_id){
	var query ='airline='+airline_id;
	table.destroy();
	build_datatable(query);
}
</script>