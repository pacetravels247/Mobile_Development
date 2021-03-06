<!-- HTML BEGIN -->
<div class="bodyContent">
<?php echo $this->session->flashdata("msg"); ?>
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-shield"></i> TDS Certificates</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<form name="debit_balance" autocomplete="off" action="<?php echo base_url("index.php/management/upload_tds_certificates"); ?>" method="POST" enctype="multipart/form-data"
			class="form-horizontal">
			   <fieldset form="debit_balance">
			      <legend class="form_legend">Upload Certificates</legend>
			      <div class="form-group">
			         <label class="col-sm-3 control-label" for="upload_files">Select PDF Files
			         	<span class="text-danger">*</span>
			         </label>
			         <div class="col-sm-3">
			         	<input type="file" name="tds_certificates[]" multiple>
			         </div>
			         <div class="col-sm-3"> 
				      <button type="submit" id="debit_balance_submit" class=" btn btn-success ">
				      Upload</button>
			      </div>
			      </div>
			   </fieldset>
			</form>

			<div class="">
				<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Records</span>
			</div>
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Sno</th>
					<th>Agency Name</th>
					<th>Agent Id</th>
					<th>Pan No</th>
					<th>Quater</th>
					<th>Year</th>
					<th>Uploaded Date</th>
					<th>Action</th>
				</tr>
			<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) {	?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=($v['agency_name']);?></td>
						<td><?=(provab_decrypt($v['uuid']));?></td>
						<td><?=($v['pan_no']);?></td>
						<td><?=($v['quater']);?></td>
						<td><?=($v['year']);?></td>
						<td><?=$v['upload_date']?></td>
						<td><a href="<?php echo $this->template->domain_uploads()."/tds_certificates/".$v['file_name']; ?>" target="_blank">View</a></td>
					</tr>
				<?php
				}
			} else {
				echo '<tr><td colspan="8">No Data Found</td></tr>';
			}
			?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>