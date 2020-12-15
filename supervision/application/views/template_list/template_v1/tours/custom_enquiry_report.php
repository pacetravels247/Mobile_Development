<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>

<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Custom Enquiries</a></li>
     </ul>
    </div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->
		
				<div class="clearfix"></div>
               

			<div class="tab-content">
				<div id="tableList" class="rigid_actions">
					<div class="pull-right">
					 <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered rigid_actions">
						<tr>
							<th>SN</th>
							<th>Reference No</th>
							<th>Agency Name with ID</th>
							<th>Contact No.</th>
							<th>Travel</th>
							<th>Destination</th>
							<th>Enquiry Date</th>
							<th>Date of Travel</th>
							<th>No of night</th>
							<th>No of pax</th>
							<th>Requests</th>
							<th>Allotment</th>
							<th>Amount</th>
							<!-- <th>Phone</th>
							<th>Email</th> -->
							<th>Status</th>
							
						</tr>
						<?php
						$current_record=1;
							if(!empty($table_data)){
								//debug($table_data);
								$sn=1;
								//array_reverse($table_data);
							foreach($table_data as $parent_k => $parent_v) {
								$agency_details = $this->custom_db->single_table_records("user",'*',array('user_id'=>$parent_v['agent_details']['user_id']))['data'][0];
								extract($parent_v);
						?>
							<tr class="enq_tr">
								<input type="hidden" value="<?=$id?>" class="is_tour_id">
								<td><?php echo ($current_record++)?></td>
								<td><?php echo $agent_name;?></td>
								<td><?php echo $parent_v['agent_details']['agency_name'].' - '.provab_decrypt($agency_details['uuid']);?></td>
								<td><?php echo $parent_v['agent_details']['phone'];?></td>
								<!--<td><?php echo $country_name;?></td>-->
								<td><?php echo ucfirst($travel_type);?></td>
								<td><?php echo $country_name;?></td>
								<td><?php echo date('d-m-Y',strtotime($created_date));?></td>
								
								<td><?php echo date('d/M',strtotime($fr_date)).' - '. date('d/M',strtotime($to_date));?></td>
								<td><?php echo $night; ?></td>
								<td><?php echo $adult.'|'.$child.'|'.$infant;?></td>
								<td><a class="btn btn_sm" data-toggle="modal" data-target="#note_<?=$sn?>" >Notes</a></td>   
								<td><select class="alloted_to"><option value="0">Not Yet Assigned.</option>
								<?php 
									foreach($package_manager as $pack){
										if($pack['user_id'] == $alloted_to){
											$sel="selected";
										}else{
											$sel="";
										}
										echo '<option value="'.$pack['user_id'].'" '.$sel.'>'.$pack['first_name'].' '.$pack['last_name'].'</option>';
		 
									}
								?>
								</select></td>
								<td></td>
								<td><?php echo $status;?></td>
								
							</tr>
						<?php
						$sn++;
							}
							}
							else {
								echo '<tr><td>No Data Found</td></tr>';
							}
						?>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$sn = 1;
	//debug($table_data);
	foreach ($table_data as $key => $data) { 
?>
	<div class="modal" id="note_<?=$sn?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Notes</h4>
				</div>
								<div class="modal-body bot">
		
					<div class="row">
<div class="qf_heading"> Agent Contact</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Id: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['user_id']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Name: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['agency_name']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Phone: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['phone']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">Type of travel</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Travel Type: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=ucfirst($data['travel_type'])?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">WHERE YOU WANT TO TRAVEL</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Destination: </label>
	</div>
	<div class="col-sm-6">
	<?php 
	$country=explode(',',$data['country_name']);
	foreach($country as $contr){
		echo '<span class="tab">'.$contr.'</span>';
	}
	?>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">DEPARTURE CITY</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Departure: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['city']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">More Details</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">From Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['fr_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">To Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['to_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">No. of Night: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['night']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Adult: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['adult']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Child: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['child']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Infant: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['infant']?></span>
	</div>
</div>
</div>

<div class="row">
<div class="qf_heading">ANY SPECIAL REQUESTS</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Requests: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['remark']?></span>
	</div>
</div>
</div>					
					
					
					
					
					
					
					
				</div>

			</div>
		</div>
	</div>
<?php
$sn++;
   }
?>
<script type="text/javascript">
 $(document).ready(function()
 {
	$(document).on('change','.alloted_to',function(e){
		var sel_val = $(this).val();
		var sel_user =$(this).text();
		var enquiry_id =$(this).parents('.enq_tr').find('.is_tour_id').val();
		$response = confirm("Are you sure to assign this enquiry to Package Executive ?");
		if($response==true){ 
			window.location='<?=base_url()?>index.php/tours/assign_custom_enquiry/'+sel_val+'/'+enquiry_id; 
		} else{
			
		}
	});
	
 });
</script>
<style>

span.tab {
    display: block;
    border: 1px solid #ccc;
    padding: 2px 10px;
}
.qf_heading {
    text-align: center;
    text-transform: uppercase;
    font-size: 16px;
    padding: 5px 0px;
    background: #f1f1f1;
    width: 100%;
    display: block;
    margin: 15px auto;
}
.modal-body.bot .col-sm-12 {
    margin-bottom: 10px;
}
</style>