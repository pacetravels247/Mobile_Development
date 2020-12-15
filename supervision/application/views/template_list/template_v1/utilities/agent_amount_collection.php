<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script>
<!-- HTML BEGIN -->
<?php
if (is_array($search_params)) {
	extract($search_params);
}

$_datepicker = array(array('created_date_from', PAST_DATE), array('created_date_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_date_from', 'created_date_to')));

?>
<div id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog" role="document">  <div class="modal-content">     <div class="modal-header">        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
        <h4 class="modal-title">Add Agent Collection Amount</h4>     </div><div class="modal-body"><div id="message"></div> 

<form name="request_form" autocomplete="off" action="<?php echo base_url(); ?>index.php/utilities/agent_amount_collection" method="POST" enctype="multipart/form-data" id="request_form" role="form" class="form-horizontal">
	<fieldset form="request_form"><legend class="form_legend">Deposit Request Details</legend>
		


<div class="form-group"><label class="col-sm-3 control-label" for="bank_id" form="request_form">Agent List<span class="text-danger">*</span></label><div class="col-sm-6"><select id="bank_id" class="form-control select2" name="agency_name" required="">
		<option value="">All</option>
		<?php
			foreach ($agency_list as $key => $value) {
		?>
		<option value="<?=$value['user_id'];?>" <?php if(isset($_GET['agent']) && $value['user_id'] == $_GET['agent']) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
	<?php } ?>
</select></div></div>

<div class="form-group"><label class="col-sm-3 control-label" for="bank_id" form="request_form">Request Type<span class="text-danger">*</span></label><div class="col-sm-6">
<select  id="balance_request_type" name="deposite_type" class="normalsel_dash" autocomplete="off"> 
<option value="Transfer/Bank Deposite" selected="selected">BANK DEPOSITE</option>                               
<!-- <option value="Cheque">CHEQUE/DD</option>                             -->
<option value="Cash">CASH</option>                            
</select></div></div>

<div class="form-group"><label class="col-sm-3 control-label" for="bank_name" form="request_form">Bank Name<span class="text-danger">*</span></label><div class="col-sm-6"><select required="" dt="" name="beneficiary_bank_name" class=" bank_id form-control" id="bank_name" data-original-title="" title=""><option value="INVALIDIP">Please Select</option><option value="Canara Bank">Canara Bank</option><option value="ICICI Bank">ICICI Bank</option><option value="Cash Deposited In Office">Cash Deposited In Office</option></select></div></div>


<!-- <div class="form-group" id="payer_bank_name_a"><label class="col-sm-3 control-label" for="amount" form="request_form">Payer Bank Name<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="payer_bank_name" required="" type="text" placeholder="" class="amount form-control" id="payer_bank_name" data-container="body" data-toggle="popover" data-original-title="" data-placement="bottom" data-trigger="hover focus" data-content="Amount Ex:1000"></div></div> -->

<div class="form-group"><label class="col-sm-3 control-label" for="amount" form="request_form">Collected Amount<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="collected_amount" required="" type="number" placeholder="" class=" numeric amount form-control" id="amount" data-container="body" data-toggle="popover" data-original-title="" data-placement="bottom" data-trigger="hover focus" data-content="Amount Ex:1000"></div></div>

<div class="form-group"><label class="col-sm-3 control-label" for="date_of_transaction" form="request_form">Collected Date<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="collected_date" readonly="" required="" type="text" placeholder="" class="dp date_of_transaction form-control" id="collected_date" data-original-title="" title=""></div></div>

<div class="form-group"><label class="col-sm-3 control-label" for="date_of_transaction" form="request_form">Date Of Deposit<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="payment_date" readonly="" required="" type="text" placeholder="" class="dp date_of_transaction form-control" id="payment_date" data-original-title="" title=""></div></div>

<div class="form-group" style="display:none;" id="cheque_dd_no"><label class="col-sm-3 control-label" for="date_of_transaction" form="request_form">Check/DD No<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="cheque_dd_no"   type="text" placeholder="" class="date_of_transaction form-control" id="" data-original-title="" title=""></div></div>

<div class="form-group" style="display:none;" id="cheque_issued_date">><label class="col-sm-3 control-label" for="date_of_transaction" form="request_form">Check/DD Date<span class="text-danger">*</span></label><div class="col-sm-6"><input value="" name="cheque_issued_date" readonly=""  type="text" placeholder="" class="dp date_of_transaction form-control" id="" data-original-title="" title=""></div></div>
<?php $remark = 'Amount Collected by '.$this->entity_user_id.' Date:'.date('d-m-Y H:i:s'); ?>
<div class="form-group"><label class="col-sm-3 control-label" for="remarks" form="request_form">Remarks</label><div class="col-sm-6"><textarea dt="" name="remarks" id="remarks" rows="3" class=" remarks form-control" data-original-title="" title=""><?php echo $remark; ?></textarea></div></div>

<div class="form-group" id="bank_slip_a"><label class="col-sm-3 control-label" for="image" form="request_form">Bank Deposit slip*</label><div class="col-sm-6"><input value="" name="upload_reciept" accept="image/*" type="file" placeholder="Bank Deposit slip" class=" dip_slip_req image image" id="upload" data-container="body" data-toggle="popover" data-original-title="" data-placement="bottom" data-trigger="hover focus" data-content="Bank Deposit slip" required="required"></div></div>

</fieldset><div class="form-group"><div class="col-sm-8 col-sm-offset-4"> <button type="submit" id="request_form_submit" class=" btn btn-success ">Save</button> <button type="reset" id="request_form_reset" class=" btn btn-warning ">Reset</button></div></div>

</form>
   </div> <div class="modal-footer"> <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> </div>  </div> 
  </div></div>

<div id="general_user" class="bodyContent">
<?php echo validation_errors(); ?>
<div class="row">
	<div class="col-sm-6"><h4><strong>Agent Amount Collection</strong></h4></div>
	<div class="col-sm-6"></div>
</div>
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
        <div class="clearfix"></div>
		<div class="panel_bdy"><!-- PANEL BODY START -->
            <div class="panel-body">
            	<?php 
			    if(!empty($this->session->flashdata('msg')))
			    {
			    ?>
            	<div class="row">
		        <div class="col-xs-12">
		          <div class="callout callout-success">
		            <p><?php echo $this->session->flashdata('msg'); ?></p>
		          </div>
		        </div>
		      </div>
		  <?php } ?>
            <?php //if($this->entity_user_type == Executive) {?>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Add Agent Collection Amount</button>
            <?php //} ?>
			<h4>Search Panel</h4>
			<hr>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group well well-sm">
					<!-- <div class="col-xs-3">
						<label>
						Executive List
						</label>
					
						<select class="form-control select2" name="exe_id">
							<option value="">All</option>
							<?php
								foreach ($executive_list as $key => $value) {
							?>
							<option value="<?=$value['user_id'];?>" <?php if(isset($_GET['exe_id']) && $value['user_id'] == $_GET['exe_id']) { echo 'selected';}?>><?=$value['first_name']."  ".$value['last_name'];?></option>
						<?php } ?>
					</select>
					</div> -->
					<!-- <div class="col-xs-3">
						<label>
						Agent List
						</label>
					
						<select class="form-control select2" name="agent">
							<option value="">All</option>
							<?php
								foreach ($agency_list as $key => $value) {
							?>
							<option value="<?=$value['user_id'];?>" <?php if(isset($_GET['agent']) && $value['user_id'] == $_GET['agent']) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
						<?php } ?>
					</select>
					</div> -->
					<!-- <div class="col-xs-3">
						<label>
						Status
						</label>
						<select class="form-control" name="status">
							<option value="">All</option>
							<option value="Pending" <?php if(isset($_GET['status']) && $_GET['status'] == 'Pending') { echo 'selected';}?>>Pending</option>
							<option value="Approved" <?php if(isset($_GET['status']) && $_GET['status'] == 'Approved') { echo 'selected';}?>>Approved</option>
							<option value="Rejected" <?php if(isset($_GET['status']) && $_GET['status'] == 'Rejected') { echo 'selected';}?>>Rejected</option>
						</select>
					</div> -->
					<div class="col-xs-3">
						<label>
						Deposite From
						</label>
						<input type="text" readonly id="created_date_from" class="form-control" name="created_date_from" value="<?=@$created_date_from?>" placeholder="Request Date">
					</div>
					<div class="col-xs-3">
						<label>
						Deposite To
						</label>
						<input type="text" readonly id="created_date_to" class="form-control disable-date-auto-update" name="created_date_to" value="<?=@$created_date_to?>" placeholder="Request Date">
					</div>
					<div class="col-xs-3" style="margin-top: 23px;">
						<button type="submit" class="btn btn-primary">Search</button> 
						<a href="<?php echo base_url(); ?>index.php/utilities/agent_amount_collection" id="clear-filter" class="btn btn-primary">ClearFilter</a>
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
				
				</div>
			</form>
		</div>
				<?php
					/************************ GENERATE CURRENT PAGE TABLE ************************/
					echo get_table($table_data);
					/************************ GENERATE CURRENT PAGE TABLE ************************/
				?>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
<!-- HTML END -->
<?php 
function get_table($table_data='')
{
	$ci=&get_instance();
	$table = '
   <div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed" id="operator_cancel_req_table">';
      $table .= '<thead><tr>
   <th>Sno</th>
   <th>Agency name</th>
   <th>Collected By</th>
   <th>Collected Date</th>
   <th>Benefuciary Bank Name</th>
   <th>Deposite Date</th>
   <th>Deposite Type</th>
   <th>Amount</th>
   <th>Status</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		//$boc_request_statuses = get_enum_list('boc_request_status');
		//debug($table_data);die();
		$sl = 0;
		foreach ($table_data['list'] as $k => $v) {
			if(($ci->entity_user_type == Executive)){
				
				if(trim($v['created_by_id']) == trim($ci->entity_user_id)){
			$link = '';
			if(!empty($v['image'])){
				$link = '<a target="_blank" href="'.$ci->template->domain_uploads().'deposit_slips/'.$v['image'].'">'.$v['transaction_type'].'</a>';
			}else{
				$link = $v['transaction_type'];
			}
			
			$sl++;
			$table .= '<tr>
			<td>'.($sl).'</td>
			<td>'.$v['requested_from'].'</td>
			<td>'.$v['request_user'].'</td>
			<td>'.$v['date_of_transaction'].'</td>
			<td>'.$v['bank'].'</td>
			<td>'.$v['created_datetime'].'</td>
			<td>'.$link.'</td>
			<td>'.$v['amount'].'</td>
			<td>'.$v['status'].'</td>';
			$table .= '</tr>';
			}
			}else{
				$link = '';
			if(!empty($v['image'])){
				$link = '<a target="_blank" href="'.$ci->template->domain_uploads().'deposit_slips/'.$v['image'].'">'.$v['transaction_type'].'</a>';
			}else{
				$link = $v['transaction_type'];
			}
			
			$sl++;
			$table .= '<tr>
			<td>'.($sl).'</td>
			<td>'.$v['requested_from'].'</td>
			<td>'.$v['request_user'].'</td>
			<td>'.$v['date_of_transaction'].'</td>
			<td>'.$v['bank'].'</td>
			<td>'.$v['created_datetime'].'</td>
			<td>'.$link.'</td>
			<td>'.$v['amount'].'</td>
			<td>'.$v['status'].'</td>';
			$table .= '</tr>';
		}
			
		}
	} else {
		$table .= '<tr>
					<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
					<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>';

		/*if($ci->entity_user_type == ADMIN){
				$table .= '<td>---</td>';
			}

			$table .= '</tr>';	*/		
	}
	$table .= '</tbody></table></div>';
	return $table;
}
?>
<style type="text/css">
	.select2 { width: 264; }
	.hideit { display: none; }
</style>
<div id="approve_reject_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reason For Rejection</h4>
      </div>
      <div class="modal-body">
    	<form method="POST" action="<?php echo base_url();?>index.php/utilities/agent_amount_collection" enctype="multipart/form-data" id="reject_reason_form">
    	<input type="hidden" id="data_to_save">
		 	<div class="col-sm-12">
		 		<div class="form-group">
			    	<label for="pwd">Reason For Rejection:</label>
			    	<textarea name="reject_reason" id="reject_reason" required="required" class="form-control" title="Reason For Rejection" ></textarea>
		 		</div>
		 	</div>
		 	<div class="clearfix"></div>
		 	<button type="submit" class="btn btn-success" id="reject_reason_submit_btn">Submit</button>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>




<script type="text/javascript">
$(document).ready(function() {
	
     $(".select2").select2();
//check_deposite_type(0);
    $("#balance_request_type").change(function(){
      var type=$(this).val();
      check_deposite_type(type);
    });
 
	$(".update_request_status").change(function(){
		$(this).parent("form").submit();
	});
    $('#operator_cancel_req_table').DataTable({
        // Disable initial sort 
        "aaSorting": []
    });

    $('.dp').datepicker({ dateFormat: 'yy-mm-dd' });

    $('.approve_reject').click(function(){
    	var status = $(this).data('status');
    	var o_id = $(this).data('id');
    	var status = $(this).data('status');
    	var agent_id = $(this).data('agent_id');
    	var amount = parseFloat($(this).data('amount'));
    	var ref = $(this).data('ref');
    	var data = "id="+o_id+"&agent_id="+agent_id+"&status="+status+"&amount="+amount+"&ref="+ref;
    	$("#data_to_save").val(data);
    	if(status=="Reject")
    		$("#approve_reject_modal").modal("show");
    	else
    		approve_reject(data);
    });
    $("#reject_reason_form").submit(function(e){
    	e.preventDefault();
    	var rfr = $("#reject_reason").val();
    	var data = $("#data_to_save").val();
    	data += "&rfr="+rfr;
    	approve_reject(data);
    });
    function approve_reject(data){
    	$.ajax({
    		'url' : app_base_url+"index.php/utilities/update_agent_amount_collection/",
    		'type' : 'POST',
    		'data' : data,
    		success : function(response){
    			location.reload(true);
    		}
    	});
    }
});

function check_deposite_type(type){
	if(type == 'Cash'){
		$('#payer_bank_name_a, #upload, #payer, #cheque, #payment, #bank_slip_a').css('display', 'none');
		$('#beneficiary_bank_name, #payer_bank_name, #cheque_dd_no, #upload, #cheque_issued_date, #payment_date').removeAttr('required','required')
	}
	if(type == 'Transfer/Bank Deposite'){
		$('#cheque_dd_no, #cheque_issued_date,#payer_bank_name_a').css('display','none');
		$('#beneficiary,#upload,#payment,#bank_slip_a').css('display','block');
		$('#beneficiary_bank_name, #payer_bank_name, #upload_reciept, #payment_date').attr('required','required');
		$('#payer_bank_name').removeAttr('required','required');
	}
	if(type == 'Cheque') {
		$('#cheque_dd_no, #cheque_issued_date').css('display','block');
		$('#payer, #cheque, #cheque, #payment').css('display','block');
		$('#payer_bank_name,#cheque_dd_no, #cheque_issued_date, #payment_date').attr('required','required');
		
	}
}
</script>