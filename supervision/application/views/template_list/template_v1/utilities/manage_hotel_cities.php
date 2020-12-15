<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-head">
			<div class="clearfix">
				<h3 style="padding-left: 1%">Hotel City List</h3>
				<div class="col-sm-3">
					<select id="hotel_search" class="form-control" name="status" onchange="city_search(this.value);">
                            <option value="">City Code</option>
                            <?php
                                foreach ($city_code as $key => $value) {
                            ?>
                            <option value="<?=$value['origin'];?>"><?=$value['city_name'];?></option>
                            <?php } ?>
                        </select>
				</div>
				<div class="pull-right" style="padding-right: 1%" data-toggle="modal" data-target="#myModal">
					<button  class="btn btn-success">Add City</button>
				</div>
				
			</div>
		</div>
		<div class="panel-body">
			<div class="clearfix">
				<table id="example" class="display nowrap" style="width:100%">
			        <thead>
			            <tr>
			                <th>Sl No</th>
			                <th>City name</th>
			                <th>Country Code</th>
			                <th>Rezlive City Id</th>
			                <th>Country Name</th>
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
        <h4 class="modal-title">Add Hotel City</h4>
      </div>
      <div class="modal-body">
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/add_new_hotel_city" method="POST">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="c_name">City Name:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="c_name" id="c_name" placeholder="City Name" required="required" maxlength="50">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="c_code">Country Codes:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="c_code" id="c_code" placeholder="Country Code" required="required" maxlength="2">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="rez_c_id">Rezlive City Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="rez_c_id" id="rez_c_id" placeholder="Rezlive City Id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="country">Country Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="country" id="country" placeholder="Country Name" required="required">
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
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/update_new_hotel_city" method="POST">
			  <input type="hidden" id='o_id' name="o_id">
			  
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="s_name">City Name:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="c_name" id="c_name_e" placeholder="City Name" required="required" maxlength="50">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="c_code">Country Codes:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="c_code" id="c_code_e" placeholder="Country Code" required="required" maxlength="2">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="rez_c_id">Rezlive City Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="rez_c_id" id="rez_c_id_e" placeholder="Rezlive City Id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="country">Country Name:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="country" id="country_e" placeholder="Country Name" required="required">
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
	$("#hotel_search").select2();
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
	$('#c_name_e').val($(this).data('c_name'));
	$('#c_code_e').val($(this).data('c_code'));
	$('#rez_c_id_e').val($(this).data('rez_id'));
	$('#country_e').val($(this).data('country'));

})
var query = '';
$(document).on('click','.delete_details',function(){
	var res = confirm("Are you sure, to delete the record ?");
	if(res == true){
	var oid = $(this).data('id');
		$.ajax({
			'url' : app_base_url+"index.php/utilities/delete_hotel_city/",
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
        	"url":app_base_url+'index.php/utilities/get_hotel_cities?'+query,
        	"type":"POST",
        },
        "searching": false
    });
}

function city_search(city_id){
	var query ='city_id='+city_id;
	table.destroy();
	build_datatable(query);
}
</script>

