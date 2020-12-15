<?php
$add_default_commission = false;
if((isset($agent_ref_id) == true && empty($agent_ref_id) == false) || (isset($default_commission) == true && $default_commission == ACTIVE)) {
	//update the commission for specific agent or default commission
	$show_update_form = true;
	$tab1 = " active ";
	$tab2 = "";
	$flight_commission_details = $commission_details['flight_commission_details'];
	$hotel_commission_details = $commission_details['hotel_commission_details'];
	$bus_commission_details = $commission_details['bus_commission_details'];
	// debug($commission_details);
	// exit;
	$sightseeing_commission_details = $commission_details['sightseeing_commission_details'];

	$transfer_commission_details = $commission_details['transfer_commission_details'];


	if(isset($agent_ref_id) == true && empty($agent_ref_id) == false) {
		$agent_details = $commission_details['agent_details'];
		$update_tab_label = 'AL00321';
	} else if(isset($default_commission) == true && $default_commission == ACTIVE) {
		$add_default_commission = true;
		$update_tab_label = 'AL00321';
	}
} else {
	$show_update_form = false;
	$tab2 = " active ";
	$tab1 = "";
}
?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
<?php if($show_update_form == true || $add_default_commission == true) { ?>
	<li role="presentation" class="<?php echo $tab1; ?>"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-edit"></i> <?php echo get_app_message($update_tab_label);?>
	</a></li>
	<?php  } ?>
	<?php if($add_default_commission == false) { ?>
	<li role="presentation" class="<?php echo $tab2; ?>"><a
		href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
	<i class="fa fa-users"></i> <?php echo get_app_message('AL00319');?> </a>
	</li>
	<?php } ?>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content"><?php if($show_update_form == true || $add_default_commission == true) { ?>
<div role="tabpanel" class="tab-pane clearfix <?php echo $tab1; ?>" id="fromList">
<div class="">
<div class"col-md-12">
	<div class="">
		Note : Commission amount is calculated from the commission which you get from API.
	</div>
	<?php
	if($add_default_commission == false) {
		echo get_agent_details($agent_details);
	}
	?>
</div>
<form method="post" name="commission_form">
	<div class="col-md-12 table-responsive">
		<div class="panel panel-info clearfix">
			<?php echo flight_commission_tab($flight_commission_details, @$agent_ref_id, $super_admin_flight_commission);?>
			<?php echo bus_commission_tab($bus_commission_details, @$agent_ref_id, $super_admin_bus_commission);?>
			<?php echo sightseeing_commission_tab($sightseeing_commission_details,@$agent_ref_id,$super_admin_sightseeing_commission);?>
			<?php echo transfer_commission_tab($transfer_commission_details,@$agent_ref_id,$super_admin_transfer_commission); ?>

		</div>
	</div>
	<div class="col-md-12">
      <input type="submit" class="btn btn-sm btn-success" value="Update">
      <button type="reset" class="btn btn-sm btn-warning">Reset</button>
    </div>
</form>
</div>
</div>
<?php } ?>
<div role="tabpanel" class="clearfix tab-pane <?php echo $tab2; ?>"
	id="tableList">
<div class="">
<?php
/************************ GENERATE CURRENT PAGE TABLE ************************/
if($add_default_commission == false) {
	echo get_table(@$agent_list);
}
/************************ GENERATE CURRENT PAGE TABLE ************************/
?></div>
</div>
</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->
<?php
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
	//<th>Commission(%)</th>
	//<td>'.$commission_details.'</td>
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
			
			$flight_commission = 'Flight:'.floatval($v['flight_api_value']);
			$bus_commission = 'Bus:'.floatval($v['bus_api_value']);
			$sightseeing_commission = 'Activities:'.floatval($v['sightseeing_api_value']);
			$transfer_commission = 'Transfers:'.floatval($v['transfer_api_value']);

			$commission_details = $flight_commission.'  '.$bus_commission.' '.$sightseeing_commission.'  '.$transfer_commission;
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
	return '<a role="button" href="'.base_url().'index.php/management/agent_commission?agent_ref_id='.base64_encode($id).'" class="btn btn-sm btn-primary"><i class="fa fa-plus-square"></i> 
		'.get_app_message('AL00318').'</a>';
}
function get_agent_details($agent_details)
{
	$agent_name = get_enum_list('title', $agent_details['title']).' '.ucfirst($agent_details['first_name']).' '.$agent_details['last_name'];
	$agency_name = $agent_details['agency_name'];
	$agent_logo = $GLOBALS['CI']->template->domain_images(get_profile_image($agent_details['agent_logo']));
	$email = provab_decrypt($agent_details['email']);
	$phone = $agent_details['country_code'].$agent_details['phone'];
	$address = $agent_details['address'];
	$agent_id = provab_decrypt($agent_details['uuid']);
	$status = get_enum_list('status', $agent_details['status']);
	$details = '<div class"col-md-12">
			    <div class="text-info">
			    	 <h4><span class="fa fa-user"></span> '.$agency_name.'-'.$agent_id.'</h4>
			    </div>
			    </div>
			';
	return $details;
}
function flight_commission_tab($flight_commission_details, $agent_ref_id, $super_admin_flight_commission)
{
	if(isset($flight_commission_details['origin']) == true && intval($flight_commission_details['origin']) > 0) {
		$flight_commission_origin = $flight_commission_details['origin'];
		$flight_commission_value = $flight_commission_details['value'];
	} else {
		$flight_commission_origin = 0;
		$flight_commission_value = 0;
	}
	$comm = $super_admin_flight_commission['api_value'] + 0;
	$details = '
    
        <div class="panel-heading">
        	<div class="panel-title"><span class="fa fa-plane"></span> flight Commission</div>
       </div>
        <div class="panel-body commission_wrapper">
	        <div class="hide hidden_commission_details">
	        <input type="hidden" name="module[]" value="'.META_AIRLINE_COURSE.'">
	        <input type="hidden" name="agent_ref_id[]" value="'.base64_encode(intval($agent_ref_id)).'">
	        <input type="hidden" name="commission_origin[]" value="'.intval($flight_commission_origin).'">
	        <input type="hidden" name="commission[]" value="'.floatval($flight_commission_value).'">
	        </div>
            <div class="col-md-12">
	               Agents Get 
	               <select name="api_value[]" class="api_value" data-superadmin_commission="'.floatval($super_admin_flight_commission['api_value']).'">
	               	<option value="0">0</option>
	               	'.generate_options(numeric_dropdown(array('size' => ($super_admin_flight_commission['api_value']*2), 'divider' => 2)), array(floatval((@$flight_commission_details['api_value'])))).'
	               </select>
	             	% 
	             	Commission From <strong>'.$comm.'%</strong> Commission
            </div>
        </div>
    ';
	return $details;
}
function bus_commission_tab($bus_commission_details, $agent_ref_id, $super_admin_bus_commission)
{
	
	if(isset($bus_commission_details['origin']) == true && intval($bus_commission_details['origin']) > 0) {
		$bus_commission_origin = $bus_commission_details['origin'];
		$bus_commission_value = $bus_commission_details['value'];
	} else {
		$bus_commission_origin = 0;
		$bus_commission_value = 0;
	}
	$comm = $super_admin_bus_commission['api_value'] + 0;
	$details = '
    
        <div class="panel-heading">
        	<div class="panel-title"><span class="fa fa-bus"></span> Bus Commission</div>
       </div>
        <div class="panel-body commission_wrapper" >
	        <div class="hide hidden_commission_details">
	        <input type="hidden" name="module[]" value="'.META_BUS_COURSE.'">
	        <input type="hidden" name="agent_ref_id[]" value="'.base64_encode(intval($agent_ref_id)).'">
	        <input type="hidden" name="commission_origin[]" value="'.intval($bus_commission_origin).'">
	        <input type="hidden" name="commission[]" value="'.floatval($bus_commission_value).'">
	        </div>
            <div class="col-md-12">
	               Agents Get 
	               <select name="api_value[]" class="api_value" data-superadmin_commission="'.floatval($super_admin_bus_commission['api_value']).'">
	               	<option value="0">0</option>
	               	'.generate_options(numeric_dropdown(array('size' => ($super_admin_bus_commission['api_value']*2), 'divider' => 2)), array(floatval((@$bus_commission_details['api_value'])))).'
	               </select>
	             	% 
	             	Commission From <strong>'.$comm.'%</strong> Commission
            </div>
        </div>
    ';
	return $details;
}
function sightseeing_commission_tab($sightseeing_commission_details, $agent_ref_id, $super_admin_sightseeing_commission){

	if(isset($sightseeing_commission_details['origin']) == true && intval($sightseeing_commission_details['origin']) > 0) {
		$sightseeing_commission_origin = $sightseeing_commission_details['origin'];
		$sightseeing_commission_value = $sightseeing_commission_details['value'];
	} else {
		$sightseeing_commission_origin = 0;
		$sightseeing_commission_value = 0;
	}
	$comm = $super_admin_sightseeing_commission['api_value'] + 0;
	$details = '
    
        <div class="panel-heading">
        	<div class="panel-title"><span class="far fa-binoculars"></span> Activities Commission</div>
       </div>
        <div class="panel-body commission_wrapper" >
	        <div class="hide hidden_commission_details">
	        <input type="hidden" name="module[]" value="'.META_SIGHTSEEING_COURSE.'">
	        <input type="hidden" name="agent_ref_id[]" value="'.base64_encode(intval($agent_ref_id)).'">
	        <input type="hidden" name="commission_origin[]" value="'.intval($sightseeing_commission_origin).'">
	        <input type="hidden" name="commission[]" value="'.floatval($sightseeing_commission_value).'">
	        </div>
            <div class="col-md-12">
	               Agents Get 
	               <select name="api_value[]" class="api_value" data-superadmin_commission="'.floatval($super_admin_sightseeing_commission['api_value']).'">
	               	<option value="0">0</option>
	               	'.generate_options(numeric_dropdown(array('size' => ($super_admin_sightseeing_commission['api_value']*2), 'divider' => 2)), array(floatval((@$sightseeing_commission_details['api_value'])))).'
	               </select>
	             	% 
	             	Commission From <strong>'.$comm.'%</strong> Commission
            </div>
        </div>
    ';
	return $details;
}
function transfer_commission_tab($transfer_commission_details, $agent_ref_id, $super_admin_transfer_commission){

	if(isset($transfer_commission_details['origin']) == true && intval($transfer_commission_details['origin']) > 0) {
		$transfer_commission_origin = $transfer_commission_details['origin'];
		$transfer_commission_value = $transfer_commission_details['value'];
	} else {
		$transfer_commission_origin = 0;
		$transfer_commission_value = 0;
	}
	$comm = $super_admin_transfer_commission['api_value'] + 0;
	$details = '
    
        <div class="panel-heading">
        	<div class="panel-title"><span class="fa fa-taxi"></span> Transfer Commission</div>
       </div>
        <div class="panel-body commission_wrapper" >
	        <div class="hide hidden_commission_details">
	        <input type="hidden" name="module[]" value="'.META_TRANSFERV1_COURSE.'">
	        <input type="hidden" name="agent_ref_id[]" value="'.base64_encode(intval($agent_ref_id)).'">
	        <input type="hidden" name="commission_origin[]" value="'.intval($transfer_commission_origin).'">
	        <input type="hidden" name="commission[]" value="'.floatval($transfer_commission_value).'">
	        </div>
            <div class="col-md-12">
	               Agents Get 
	               <select name="api_value[]" class="api_value" data-superadmin_commission="'.floatval($super_admin_transfer_commission['api_value']).'">
	               	<option value="0">0</option>
	               	'.generate_options(numeric_dropdown(array('size' => ($super_admin_transfer_commission['api_value']*2), 'divider' => 2)), array(floatval((@$transfer_commission_details['api_value'])))).'
	               </select>
	             	% 
	             	Commission From <strong>'.$comm.'%</strong> Commission
            </div>
        </div>
    ';
	return $details;
}

?>
<script>
$(document).ready(function() {
	//Update Commission Percentage
	$(document).on('change', '.api_value', function(){
		var api_value = parseFloat($(this).val().trim());
		alert(api_value);
		var api_commission = parseFloat($(this).data('superadmin_commission'));
		alert(api_commission);
		var given_percentage = (api_value/api_commission)*100;
		$(this).closest('div.commission_wrapper').find('div.hidden_commission_details').find('input[name="commission[]"]').val(given_percentage);
		});
	var cache = {};
	$('#filter_agency', 'form#filter_agency_form').autocomplete({
		source:  function( request, response ) {
	        var term = request.term;
	        if ( term in cache ) {
	          response( cache[ term ] );
	          return;
	        } else {
	        	$.getJSON( app_base_url+"index.php/ajax/auto_suggest_agency_name", request, function( data, status, xhr ) {
	                cache[ term ] = data;
	                response( cache[ term ] );
	              });
	        }
	      },
	    minLength: 1
	 });
});
</script>