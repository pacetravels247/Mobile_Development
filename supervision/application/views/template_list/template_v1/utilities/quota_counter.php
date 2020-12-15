<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-credit-card"></i>  
			Quota counter List</div>
			<div><button id="add_airline" class="btb btn-success" data-toggle="modal" data-target="#myModal">Add Airlines</button></div>
		</div>
		<!-- PANEL HEAD START -->
		<?php echo $this->session->flashdata("msg"); ?>
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="table-responsive" id="checkbox_div">
				<table class="table table-striped">
					<tr>
						<th>Sl.No</th>
						<th>Airline</th>
						<th>Airline Code</th>
						<th>Supplier</th>
						<th>Supplier Code</th>
						<th>Given Quota</th>
						<th>Consumed Quota</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
					<?php
					$i=1;
					foreach($qcls AS $qcl) { ?>
						<tr>
					<form action="<?php echo base_url("index.php/utilities/save_quota_counter/".$qcl["qc_id"]); ?>" method="post">
						<td><?php echo $i; ?></td>
						<td><?php echo $qcl["a_name"]; ?></td>
						<td><?php echo $qcl["qc_a_code"]; ?></td>
						<td><?php echo $qcl["bs_name"]; ?></td>
						<td><?php echo $qcl["qc_bs_id"]; ?></td>
						<td>
						<input type="text" name="given_quota" value="<?php echo $qcl["qc_quota"]; ?>">
						</td>
						<td>
						<input type="text" name="consumed_quota" 
						value="<?php echo $qcl["qc_consumed_quota"]; ?>">
						</td>
						<td>
							<select name="type">
								<?php echo generate_options(get_enum_list("quota_type"), array($qcl["qc_type"])); ?>
							<select>
						</td>
						<td><input type="submit" class="btn btn-primary" value="Update"></td>
						</form>
						</tr>
					<?php $i++; } ?>
				</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Airline Quota</h4>
      </div>
      <div class="modal-body">
        <form method="post" class="form-horizontal" action="<?php echo base_url()?>utilities/add_new_quota">
		  <div class="form-group">
		    <label class="control-label col-sm-3" for="booking_source">booking source:</label>
		    <div class="col-sm-9">
		      <select id="booking_source" name="booking_source">
		      	<option value="0">Select</option>
		      	<option value="PTBSID0000000010">Travelport GDS</option>
		      </select>
		    </div>
		  </div>
		  <div class="form-group">
		    <label class="control-label col-sm-3" for="a_code">Quota Type:</label>
		    <div class="col-sm-9"> 
				<select name="type">
					<?php echo generate_options(get_enum_list("quota_type")); ?> 
				<select>
		    </div>
		  </div>
		  <div class="form-group">
		    <label class="control-label col-sm-3" for="a_code">Airline code:</label>
		    <div class="col-sm-9"> 
		      <input type="text" class="form-control" id="a_code" name="a_code" placeholder="Airline Code">
		    </div>
		  </div>

		  <div class="form-group">
		    <label class="control-label col-sm-3" for="quota">Given quota:</label>
		    <div class="col-sm-9"> 
		      <input type="text" class="form-control" id="quota" name="quota" placeholder="quota">
		    </div>
		  </div>
		  
		  <div class="form-group"> 
		    <div class="col-sm-offset-3 col-sm-9">
		      <button type="submit" class="btn btn-default">Submit</button>
		    </div>
		  </div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>