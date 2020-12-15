<?php if (valid_array($booking_sources) && isset($_GET["agent_id"])) { ?>
<div class="panel-body"><!-- PANEL BODY START -->
	<fieldset><legend><i class="fa fa-plane"></i> Supplier specific Commission</legend>
		<form action="<?=$_SERVER['PHP_SELF']."?agent_id=".$_GET["agent_id"] ?>" class="form-horizontal" method="POST" autocomplete="off">
			<input type="hidden" name="form_values_origin" value="update_existing_airline_commissions" />
		<?php foreach ($booking_sources as $bs_key => $bs_data) { 
				$condition=array("BS.meta_course_list_id = '".META_AIRLINE_COURSE."'",
								"BS.origin = ".$bs_data['origin'], "BFCD.agent_fk= ".$_GET["agent_id"]);
				$comm_detail = $mgmt->module_model->get_booking_source_with_comm($condition)[0];
				if(isset($comm_detail["comm_origin"]))
					$prefill=1;
				else
					$prefill=0;
			?>
				<div class="hide">
					<input type="hidden" name="booking_source_origin[]" 
					value="<?= $bs_data['origin'] ?>" />
					<input type="hidden" name="comm_origin[]" 
					value="<?=($prefill==1) ? $comm_detail['comm_origin'] : ""?>" />
				</div>
				<div class="row">
					<div class="col-md-4">
						<?php echo $bs_data['name']." - ".$bs_data['source_id']; ?>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="specific-value-<?=$bs_key?>" class="col-sm-4 control-label">Commission</label>
							<input type="text" id="specific-value-<?=$bs_key?>" name="specific_value[]" class=" specific-value numeric" placeholder="Commission In Percentage" 
							value="<?=($prefill==1) ? $comm_detail['api_value'] : ""?>" />
						</div>
					</div>
				</div>
				<hr>
		<?php } ?>
		<div class="well well-sm">
			<div class="clearfix col-md-offset-1">
				<button class=" btn btn-sm btn-success " type="submit">Save</button>
				<button class=" btn btn-sm btn-warning " type="reset">Reset</button>
			</div>
		</div>
		</form>
	</fieldset>
</div><!-- PANEL BODY END -->
<?php } //check if airline list is present - End IF

/************************ GENERATE CURRENT PAGE TABLE ************************/
if(!isset($_GET["agent_id"])) {
	echo get_table(@$agent_list);
}
/************************ GENERATE CURRENT PAGE TABLE ************************/

function get_table($table_data='')
{
	$table = '';
	$pagination = $GLOBALS['CI']->pagination->create_links();
	$search_filter = '<div class="">
					<form method="GET" role="search" class="navbar-form" id="filter_agency_form">
					<div class="form-group">
					<input type="hidden" name="filter" value="search_agent">
					<input type="text" autocomplete="off" placeholder="Search" class="form-control ui-autocomplete-input" id="filter_agency" name="filter_agency" value="'.@$_GET['filter_agency'].'">
					</div>
					<button title="Search:Agency,Email,Mobile,ID" class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
					<a title="Clear Search" class="btn btn-default" href="'.base_url().'index.php/management/agent_commission"><i class="fa fa-history"></i></a>
					</form>';
	$table .= $pagination;
	$table .= $search_filter;
	$table .= '
   <div class="table-responsive">
   <table class="table table-hover table-striped table-bordered table-condensed">';
	$table .= '<thead><tr>
   <th><i class="fa fa-sort-numeric-asc"></i> '.get_app_message('AL006').'</th>
   <th>Agency Name</th>
   <th>Agency ID</th>
   <th>Agent Name</th>
   <th>Contact</th> 
   <th>Action</th>
   </tr>
   </thead>
   <tbody>';

	if (valid_array($table_data) == true) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		foreach ($table_data as $k => $v) {
			$table .= '<tr>
			<td>'.(++$current_record).'</td>
			<td>'.(empty($v['agency_name']) == false ? $v['agency_name'] : 'Not Added').'</td>
			<td>'.provab_decrypt($v['uuid']).'</td>
			<td>'.get_enum_list('title', $v['title']).' '.$v['first_name'].' '.$v['last_name'].'</td>
			<td>'.$v['phone'].'-'.provab_decrypt($v['email']).'</td>
			<td>'.update_commission_button($v['user_id']).'</td>
			</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	$table .= $pagination;
	return $table;
}
function update_commission_button($id)
{
	return '<a role="button" href="'.base_url().'index.php/management/b2b_supplier_wise_commission?agent_id='.$id.'" class="btn btn-sm btn-primary"><i class="fa fa-plus-square"></i> 
		'.get_app_message('AL00318').'</a>';
}
?>