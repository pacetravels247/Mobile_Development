<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-head">
			<div class="clearfix">
				<h3 style="padding-left: 1%">Bus Station List</h3>
				<div class="col-sm-3">
					<select id="bus_search" class="form-control" name="status" onchange="city_search(this.value);">
                            <option value="">Bus Station</option>
                            <?php
                                foreach ($aliport_code as $key => $value) {
                            ?>
                            <option value="<?=$value['origin'];?>"><?=$value['ets_city_name'];?></option>
                            <?php } ?>
                        </select>
				</div>
				<div class="pull-right" style="padding-right: 1%" data-toggle="modal" data-target="#myModal">
					<button  class="btn btn-success">Add Station</button>
				</div>
				
			</div>
		</div>
		<div class="panel-body">
			<div class="clearfix">
				<table id="example" class="display nowrap" style="width:100%">
			        <thead>
			            <tr>
			                <th>Sl No</th>
			                <th>Station name</th>
			                <th>Vrl Id</th>
			                <th>Bitla Id</th>
			                <th>sr_city_id</th>
			                <th>ganesh_city_id</th>
			                <th>seabird_city_id</th>
			                <th>sugama_city_id</th>
			                <th>kukkeshree_city_id</th>
			                <th>krl_city_id</th>
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
        <h4 class="modal-title">Add Bus Station</h4>
      </div>
      <div class="modal-body">
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/add_new_station" method="POST">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="s_name">Station Name:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="s_name" id="s_name" placeholder="Station Name" required="required" maxlength="50">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="status">Status</label>
			    <div class="col-sm-5"> 
			    <select name="status" id="status" class="form-control">
			      <?php echo generate_options(get_enum_list('status'),array(1)); ?>
			     </select>
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="vrl_id">Vrl Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="vrl_id" id="vrl_id" placeholder="Vrl Id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">Bitla Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="bitla_id" id="bitla_id" placeholder="Bitla Id" required="required">
			    </div>
			  </div>


			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">sr_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="sr_city_id" id="sr_city_id" placeholder="sr_city_id" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">ganesh_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="ganesh_city_id" id="ganesh_city_id" placeholder="ganesh_city_id" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">seabird_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="seabird_city_id" id="seabird_city_id" placeholder="seabird_city_id" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">sugama_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="sugama_city_id" id="sugama_city_id" placeholder="sugama_city_id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">kukkeshree_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="kukkeshree_city_id" id="kukkeshree_city_id" placeholder="kukkeshree_city_id" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="a_name">krl_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="krl_city_id" id="krl_city_id" placeholder="krl_city_id" required="required">
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
        	<form class="form-horizontal" action="<?php echo base_url()?>utilities/update_new_stations" method="POST">
			  <input type="hidden" id='o_id' name="o_id">
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="s_name">Station Name:</label>
			    <div class="col-sm-5">
			      <input type="text" class="form-control" name="s_name" id="s_name_e" placeholder="Airport Code" required="required" maxlength="50">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="status">Status</label>
			    <div class="col-sm-5"> 
			    <select name="status" id="edit_status" class="form-control">
			      <?php echo generate_options(get_enum_list('status'),array(1)); ?>
			     </select>
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="vrl_id">Vrl Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="vrl_id" id="vrl_id_e" placeholder="Vrl Id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">Bitla Id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="bitla_id" id="bitla_id_e" placeholder="Bitla Id" required="required">
			    </div>
			  </div>


			   <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">sr_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="sr_city_id" id="sr_city_id_e" placeholder="sr_city_id" required="required">
			    </div>
			  </div>
			   <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">ganesh_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="ganesh_city_id" id="ganesh_city_id_e" placeholder="ganesh_city_id" required="required">
			    </div>
			  </div>
			   <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">seabird_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="seabird_city_id" id="seabird_city_id_e" placeholder="seabird_city_id" required="required">
			    </div>
			  </div>
			   <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">sugama_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="sugama_city_id" id="sugama_city_id_e" placeholder="sugama_city_id" required="required">
			    </div>
			  </div>

			  <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">kukkeshree_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="kukkeshree_city_id" id="kukkeshree_city_id_e" placeholder="kukkeshree_city_id" required="required">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="control-label col-sm-3" for="bitla_id">krl_city_id:</label>
			    <div class="col-sm-5"> 
			      <input type="text" class="form-control" name="krl_city_id" id="krl_city_id_e" placeholder="krl_city_id" required="required">
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
	$("#bus_search").select2();
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
	$('#s_name_e').val($(this).data('c_name'));
	$('#edit_status').val($(this).data('status')); 
	$('#vrl_id_e').val($(this).data('vrl_id'));
	$('#bitla_id_e').val($(this).data('bitla_id'));
	$('#sr_city_id_e').val($(this).data('sr_city_id'));
	$('#ganesh_city_id_e').val($(this).data('ganesh_city_id'));
	$('#seabird_city_id_e').val($(this).data('seabird_city_id'));
	$('#sugama_city_id_e').val($(this).data('sugama_city_id'));
	$('#kukkeshree_city_id_e').val($(this).data('kukkeshree_city_id'));
	$('#krl_city_id_e').val($(this).data('krl_city_id'));
	$('#edit_status').select2("destroy").select2();
})
var query = '';
$(document).on('click','.delete_details',function(){
	var res = confirm("Are you sure, to delete the record ?");
	if(res == true){
	var oid = $(this).data('id');
		$.ajax({
			'url' : app_base_url+"index.php/utilities/delete_station/",
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
        	"url":app_base_url+'index.php/utilities/get_bus_stations?'+query,
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

