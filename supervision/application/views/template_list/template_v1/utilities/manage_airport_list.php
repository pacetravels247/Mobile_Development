<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-head">
			<div class="clearfix">
				<h3 style="padding-left: 1%">City List</h3>
				<div class="col-sm-3">
					<select id="airport_search" class="form-control" name="status" onchange="airport_search(this.value);">
                            <option value="">City Name</option>
                            <?php
                                foreach ($aliport_code as $key => $value) {
                            ?>
                            <option value="<?=$value['origin'];?>"><?=$value['airport_city'].'['.$value['airport_code'].']';?></option>
                            <?php } ?>
                        </select>
				</div>
				<div class="pull-right" style="padding-right: 1%" data-toggle="modal" data-target="#myModal">
					<button  class="btn btn-success">Add Airport</button>
				</div>
				
			</div>
		</div>
		<div class="panel-body">
			<div class="clearfix">
				<table id="example" class="display nowrap" style="width:100%">
			        <thead>
			            <tr>
			                <th>Sl No</th>
			                <th>airport_code</th>
			                <th>airport_name</th>
			                <th>airport_city</th>
			                <th>country</th>
			                <th>CountryCode</th>
			                <th>timezonename</th>
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
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/add_new_airport" method="POST">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_code">Airport Code:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="a_code" id="a_code" placeholder="Airport Code" required="required" maxlength="3">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airport Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="a_name" id="a_name" placeholder="Airport Name" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airport City:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="a_city" id="a_city" placeholder="Airport City" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="country">Country:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="country" id="country" placeholder="Country" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="c_code">Country Code:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="c_code" id="c_code" placeholder="Country Code" required="required" maxlength="5">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="timezone">Timezone Name:</label>
			    <div class="col-sm-5"> 
			      <input type="test" class="form-control" name="timezone" id="timezone" placeholder="Time zone" required="required">
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
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/update_new_airport" method="POST">
			  <input type="hidden" id='o_id' name="o_id">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_code">Airport Code:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="a_code" id="a_code_e" placeholder="Airport Code" required="required" maxlength="3">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airport Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="a_name" id="a_name_e" placeholder="Airport Name" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Airport City:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="a_city" id="a_city_e" placeholder="Airport City" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="country">Country:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="country" id="country_e" placeholder="Country" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="c_code">Country Code:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="c_code" id="c_code_e" placeholder="Country Code" required="required" maxlength="5">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="timezone">Timezone Name:</label>
			    <div class="col-sm-5"> 
			      <input type="test" class="form-control" name="timezone" id="timezone_e" placeholder="Time zone" required="required">
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
	$("#airport_search").select2();
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
	$('#a_code_e').val($(this).data('a_code'));
	$('#a_name_e').val($(this).data('a_name'));
	$('#a_city_e').val($(this).data('a_city'));
	$('#country_e').val($(this).data('country'));
	$('#c_code_e').val($(this).data('c_code'));
	$('#timezone_e').val($(this).data('tzone'));

})
var query = '';
$(document).on('click','.delete_details',function(){
	var res = confirm("Are you sure, to delete the record ?");
	if(res == true){
	var oid = $(this).data('id');
		$.ajax({
			'url' : app_base_url+"index.php/utilities/delete_airport/",
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
        	"url":app_base_url+'index.php/utilities/getAirportLists?'+query,
        	"type":"POST",
        },
        "searching": false
    });
}

function airport_search(airport_id){
	var query ='airport='+airport_id;
	table.destroy();
	build_datatable(query);
}
</script>

