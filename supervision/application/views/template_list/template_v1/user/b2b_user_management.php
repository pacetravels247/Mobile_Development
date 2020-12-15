<?php if (form_visible_operation()) {
	$tab1 = " active ";
	$tab2 = "";
} else {
	$tab2 = " active ";
	$tab1 = "";
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE), array('from_date', PAST_DATE), array('to_date', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
if (is_array($search_params)) {
	extract($search_params);
}
?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="row">
	<div class="col-sm-6"><h4><strong>Agent Registration Report</strong></h4></div>
	<div class="col-sm-6"></div>
</div>
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<?php
if (intval(@$eid) > 0) {
	$i_fil = '';
	if (@$_GET['user_status']) {
		$i_fil .= 'user_status='.intval($_GET['user_status']);
	}
	$cancel_edit_btn = '<a class="btn btn-sm btn-danger pull-right" href="'.base_url().'index.php/user/b2b_user?'.$i_fil.'"><i class="fa fa-trash"></i> Click here to Cancel Editing</a>';
} else {
	$cancel_edit_btn = '';
}
echo $cancel_edit_btn;
?>

<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?php echo $tab1; ?>"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-edit"></i> <?php echo get_app_message('AL0014');?>
	</a></li>
	<li role="presentation" class="<?php echo $tab2; ?>"><a
		href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
	<i class="fa fa-users"></i> 
	<?php 
		if(isset($_GET['user_status']) && $_GET['user_status'] == 1)
			echo 'Active';
		if(isset($_GET['user_status']) && $_GET['user_status'] == 0)
			echo 'Inactive';
		if(isset($_GET['user_status']) && $_GET['user_status'] == 2)
			echo 'Locked';
		if(isset($_GET['due_list']) && $_GET['due_list'] == 1)
			echo 'Overdue';
		if(!isset($_GET['user_status']) && !isset($_GET['due_list']))
			echo 'All';
	?> Agent List </a>
	</li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?php echo $tab1; ?>" id="fromList">
<div class="clearfix">
<?php 
/************************ GENERATE CURRENT PAGE FORM ************************/
$form_data['user_type'] = B2B_USER;
$city_text = '';
$country_text = '';
$dafaultCity = '';
if (isset($eid) == false || empty($eid) == true) {
	/*** GENERATE ADD PAGE FORM ***/
	$form_data['country_code'] = (isset($form_data['country_code']) == false ? INDIA_CODE : $form_data['country_code']);
	$form_data['country_name'] = $form_data['api_country_list']['api_country_list_fk'];
	//$form_data['city'] = $form_data['api_country_list']['api_city_list_fk'];
	$dafaultCity = $form_data['api_country_list']['api_city_list_fk'];
	//$form_data['country_name'] = (isset($form_data['country_code']) == false ? INDIA : $form_data['country_code']);
	//$form_data['country_name'] = (isset($form_data['country_name']) == false ? INDIA : $form_data['country_name']);
	echo $this->current_page->generate_form('b2b_user', $form_data);
} else {
	//$form_data['country_name'] =  INDIA;
	$city_text = $form_data['city_name'];
	$dafaultCity = $city_text;
	$country_text = $form_data['country_name'];
	
	echo $this->current_page->generate_form('b2b_user_edit', $form_data);
}
/************************ GENERATE UPDATE PAGE FORM ************************/
?></div>
</div>
<div role="tabpanel" class="clearfix tab-pane <?php echo $tab2; ?>"	id="tableList">
<!--/************************ GENERATE Filter Form ************************/-->

<div class="row">
	<div class="col-sm-6"><h4>Search Panel</h4></div>
	<div class="col-sm-6"></div>
</div>
<form method="GET" autocomplete="off" id="search_filter_form">
<?php if(isset($user_status)) { ?>
<input type="hidden" name="user_status" value="<?=@$user_status?>" >
<?php } ?>
<?php if(isset($due_list)) { ?>
<input type="hidden" name="due_list" value="<?=@$due_list?>" >
<?php } ?>
<div class="well well-sm">
	<div class="clearfix form-group row">
		<div class="col-xs-3">
			<label>Agency Name</label>
			<select class="form-control select2" name="uuid">
					<option value="">All</option>
					<?php
						foreach ($agency_list as $key => $value) {
					?>
					<option value="<?=provab_decrypt($value['uuid']);?>" <?php if(isset($_GET['uuid']) && provab_decrypt($value['uuid']) == @$uuid) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-xs-3">
			<label>City</label>
			<select class="form-control select2" name="city_id">
					<option value="">All</option>
					<?php
						foreach ($city_list as $key => $value) {
					?>
		<option value="<?=$value['origin'];?>" 
		<?php if(isset($_GET['city_id']) 
		&& $value['origin'] == @$city_id) { echo 'selected'; }?>><?=$value['destination']; ?></option>
				<?php } ?>
			</select>
		</div>
		<!-- <div class="col-xs-4">
			<label>PAN</label>
			<input type="text" placeholder="PAN" value="<?=@$pan_number?>" name="pan_number" class="search_filter form-control">
		</div>
		<div class="col-xs-4">
			<label>Email</label>
			<input type="text" placeholder="Email" value="<?=@$email?>" name="email" class="search_filter form-control">
		</div>
		<div class="col-xs-4">
			<label>Phone</label>
			<input type="text" placeholder="Phone Number" value="<?=@$phone?>" name="phone" class="search_filter numeric form-control">
		</div>
		<div class="col-xs-4">
			<label>Member Since</label>
			<input type="text" placeholder="Registration Date" readonly value="<?=@$created_datetime_from?>" id="created_datetime_from" name="created_datetime_from" class="search_filter form-control">
		</div>
		<div class="col-xs-4">
			<label>Registered Month</label>
			<input type="text" placeholder="Month Of Registration" readonly value="<?=@$in_month?>" id="in_month" name="in_month" class="search_filter form-control">
		</div> -->
		<div class="col-xs-3">
			<label>From Date</label>
			<input type="text" placeholder="From Date" readonly value="<?=@$from_date?>" id="from_date" name="from_date" class="search_filter form-control">
		</div>
		<div class="col-xs-3">
			<label>To Date</label>
			<input type="text" placeholder="To Date" readonly value="<?=@$to_date?>" id="to_date" name="to_date" class="search_filter form-control">
		</div>
	</div>
	<div class="row">
		<div class="col-xs-8"></div>
		<div class="col-xs-1"><button class="btn btn-primary" type="submit" style="width: 90px;">Search</button></div>
		<div class="col-xs-1"><button class="btn btn-warning" type="submit" name="excel_export" value="1" style="width: 90px;">Export</button></div>
		<div class="col-xs-1"><button class="btn btn-info" id="clear_search_filters" style="width: 150px;">Clear Search Filter <i class="fa fa-close"></i></button></div>
		<div class="col-xs-1"></div>
	</div>
</div>
</form>
<div class="clearfix"></div>
<!--/************************ GENERATE Filter Form ************************/-->
<div class="clearfix">
<?php
/************************ GENERATE CURRENT PAGE TABLE ************************/
echo get_table(@$table_data, $total_rows);
/************************ GENERATE CURRENT PAGE TABLE ************************/
?>
</div>
</div>
</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->
<?php
function get_table($table_data='', $total_rows=0)
{
	$table = '';
	$pagination = '<div>'. $GLOBALS['CI']->pagination->create_links().'<span class="">Total '.$total_rows.' agents</span></div>';
	//$table .= $pagination;
	$search_filter = '<div class="">
					<form method="GET" role="search" class="navbar-form" id="filter_agency_form">
					<input type="hidden" name="user_status" value="'.@$_GET['user_status'].'" >
					<div class="form-group">
					<input type="hidden" name="filter" value="search_agent">
					<input type="text" autocomplete="off" placeholder="Search" class="form-control ui-autocomplete-input" id="filter_agency" name="filter_agency" value="'.@$_GET['filter_agency'].'">
					</div>
					<button title="Search:Agency,Email,Mobile,ID" class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
					</form>';
	//$table .= $search_filter;
	$table .= '
   <div class="clearfix">
   <div class="col-md-12 table-responsive rigid_actions" >
   <table class="table table-condensed table-bordered rigid_actions datatables-td">';
	$table .= '<thead><tr>
   <th>sl</th>
   <th>Agency Name</th>
   <th>Agency ID</th>
   <th>City</th>
   <th>Bal</th>
   <th>Cr. Lmt</th>
   <th>Due Amt</th>
   <th>Mobile</th>
   <th>Email</th>
   <th>Refered By</th>
   ';
   //<th>Password</th><th>SU/Days</th><th>Agent Name</th>
   //<th><abbr title="Pending Deposit Request">Deposit Req</abbr></th>';
	// if (is_active_airline_module()) {
	// 	$table .= '<th>Flight</th>';
	// }
	// if (is_active_hotel_module()) {
	// 	$table .= '<th>Hotel</th>';
	// }
	// if (is_active_bus_module()) {
	// 	$table .= '<th>Bus</th>';
	// }
	// if (is_active_transferv1_module()) {
	// 	$table .= '<th>Transfers</th>';
	// }
	// if (is_active_sightseeing_module()) {
	// 	$table .= '<th>Sightseeing</th>';
	// }

   $table .= '
   
   <th>Status</th>
   <th>CreatedOn</th>
   <th>Action</th>
   </tr></thead><tbody>';
   // debug($table_data);exit;
	if (valid_array($table_data) == true) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		$rep_url = base_url().'index.php/report/';
		$dep_url = base_url().'index.php/management/b2b_balance_manager';
	
		foreach ($table_data as $k => $v) {

			/*$last_login = 'Last Login : '.last_login($v['last_login']);
			$login_status = login_status($v['logout_date_time']);*/
			$dep_req = '';
			if (isset($v['dep_req']) == true && isset($v['dep_req']['pending']) == true) {
				$dep_req = intval($v['dep_req']['pending']['count']);
			} else {
				$dep_req = 0;
			}
			
			$booking_summ = '';

			if (is_active_airline_module()) {
				$booking_summ .= '<td>'.intval(@$v['booking_summ']['flight']['BOOKING_CONFIRMED']['count']).' <a target="_blank" href="'.$rep_url.'b2b_flight_report?created_by_id='.$v['user_id'].'">view</a></td>';
			}
			
			if (is_active_hotel_module()) {
				$booking_summ .= '<td>'.intval(@$v['booking_summ']['hotel']['BOOKING_CONFIRMED']['count']).'  <a target="_blank" href="'.$rep_url.'b2b_hotel_report?created_by_id='.$v['user_id'].'">view</a></td>';
			}
			
			if (is_active_bus_module()) {
				$booking_summ .= '<td>'.intval(@$v['booking_summ']['bus']['BOOKING_CONFIRMED']['count']).'  <a target="_blank" href="'.$rep_url.'b2b_bus_report?created_by_id='.$v['user_id'].'">view</a></td>';
			}
			if (is_active_transferv1_module()) {
				$booking_summ .= '<td>'.intval(@$v['booking_summ']['transfer']['BOOKING_CONFIRMED']['count']).'  <a target="_blank" href="'.$rep_url.'b2b_transfers_report?created_by_id='.$v['user_id'].'">view</a></td>';
			}
			if (is_active_sightseeing_module()) {
				$booking_summ .= '<td>'.intval(@$v['booking_summ']['sightseeing']['BOOKING_CONFIRMED']['count']).'  <a target="_blank" href="'.$rep_url.'b2b_activities_report?created_by_id='.$v['user_id'].'">view</a></td>';
			}
			$action_tab = '';
			$action_tab .= get_view_button($v['user_id']);
			$action_tab .= get_edit_button($v['user_id']);
			
			// if($v['status'] == ACTIVE) {
			// 	$action_tab .= send_password($v['user_id'], $v['uuid']);
			// }
			$action_tab .= send_password($v['user_id'], $v['uuid']);
			// $action_tab .= delete_agent_button($v['user_id'], $v['uuid']);
			$action_tab .= view_account_ledger($v['user_id'],$v['created_datetime']);
			$action_tab .= update_credit_limit($v['user_id']);
			$action_tab .= reset_user_password_btn($v['user_id']);
			//Booking
			$days = "NA";
			$today = new DateTime(date("Y-m-d"));
			if($v["updated_datetime"]!=NULL)
			{
				$interval = $today->diff(new DateTime($v["updated_datetime"]));
				$days = $interval->days." days";
			}

			$table .= '<tr>
			<td>'.(++$current_record).'</td>
			<td title="'.(empty($v['agency_name']) == false ? $v['agency_name'] : 'Not Added' ).'">'.(empty($v['agency_name']) == false ? substr($v['agency_name'], 0,12) : 'Not Added' ).'</td>
			<td>'.provab_decrypt($v['uuid']).'</td>
			<td>'.$v['city_name'].'</td>
			<td>'.roundoff_number($v['agent_balance']).'</td>
			<td>'.roundoff_number($v['credit_limit']).'</td>
			<td>'.roundoff_number($v['due_amount']).'</td>
			<td>'.$v['phone'].'</td>
			<td>'.provab_decrypt($v['email']).'</td>
			<td>'.$v['referred_by'].'</td>
		
			<td>'.get_status_toggle_button($v['status'], $v['user_id'], $v['uuid']).'</td>
			<td>'.app_friendly_absolute_date($v['created_datetime']).'</td>
			<td>'.$action_tab.'</td>
</tr>';
		}
	} else {
		$table .= '<tr><td colspan="9">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div></div>';
	return $table;
}

echo $GLOBALS['CI']->template->isolated_view('user/reset_user_password_popup');

function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="fa fa-circle-o"></i> '.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '<span class="label label-danger"><i class="fa fa-circle-o"></i> '.get_enum_list('status', INACTIVE).'</span>
		<a role="button" href="" class="hide">'.get_app_message('AL0020').'</a>';
	}
}
function reset_user_password_btn($user_id){
	return '<a role="button" href="#" class="btn btn-sm btn-primary reset_user_password" title="Reset Password" data-user_id="'.$user_id.'" data-toggle="modal" data-target="#reset_user_password_modal">
				<i class="fa fa-redo-alt"></i>
			</a>
		';
}
function get_status_toggle_button($status, $user_id, $uuid)
{
	$status_options = get_enum_list('status');
	return '<select autocomplete="off" class="toggle-user-status" data-user-id="'.$user_id.'" data-uuid="'.$uuid.'">'.generate_options($status_options, array($status)).'</select>';
	/*if (intval($status) == INACTIVE) {
		return '<a role="button" href="'.base_url().'user/activate_account/'.$user_id.'/'.$uuid.'" class="text-success">Activate</a>';
	} else {
		return '<a role="button" href="'.base_url().'user/deactivate_account/'.$user_id.'/'.$uuid.'" class="text-danger">Deactivate</a>';
	}*/
}

function get_view_button($id)
{
	return '<a role="button" href="'.base_url().'user/view_agent_details?agent_id='.$id.'" target="_blank" class="btn btn-sm btn-success" title="View Details">
				<i class="fa fa-eye"></i>
			</a>
		';
}

function get_edit_button($id)
{
	return '<a role="button" href="'.base_url().'index.php/user/b2b_user?&	eid='.$id.'" class="btn btn-sm btn-primary" title="Edit Details">
				<i class="fa fa-edit"></i>
			</a>
		';
}
function send_password($user_id, $uuid)
{
	return '<a role="button" href="#" class="btn btn-sm btn-info send_agent_new_password" data-user-id="'.$user_id.'" data-uuid="'.$uuid.'" title="Send New Password">
				<i class="fa fa-share"></i>
			</a>
		';
}
function delete_agent_button($user_id, $uuid)
{
	return '<a role="button" href="#" class="btn btn-sm btn-danger delete_agent" data-user-id="'.$user_id.'" data-uuid="'.$uuid.'" title="Delete User">
				<i class="fa fa-trash"></i>
			</a>
		';
}
function view_account_ledger($user_id,$date){
	
	return '<a role="button" href="'.base_url().'management/account_ledger?agent_id='.$user_id.'" target="_blank" class="btn btn-sm btn-success" title="Account Ledger">
				<i class="fa fa-calculator"></i>
			</a>
		';	  
}
function update_credit_limit($user_id){
	return '<a role="button" href="'.base_url().'management/credit_balance_show?agent_id='.$user_id.'" target="_blank" class="btn btn-sm btn-default" title="Manage Credit Limit">
				<i class="fa fa-credit-card"></i>
			</a>
		';	
}

?>
<script>
$(document).ready(function() {
	$(".select2").select2();
	monthDatepicker('in_month');
	 //set dropdownlist selected
	var objSelect =document.getElementById("country_name");	
	var citySelect = document.getElementById("city");

	var country_edit_text = "<?= $country_text; ?>";
	var city_edit_text = "<?= $city_text; ?>";
	var default_city = "<?= $dafaultCity; ?>";

	if(country_edit_text !=''){

		setSelectedValue(objSelect,country_edit_text);
		
	}
	//set country dropdown list selected
	function setSelectedValue(selectObj, textToSet) {
	    for (var i = 0; i < selectObj.options.length; i++) {	    	
	        if (selectObj.options[i].text.toLowerCase() == textToSet.toLowerCase()) {	

	            selectObj.options[i].selected = true;
	            
	            return;
	        }
	    }
	}
	
	//get_city_lists();
	//Enter only numbers
	$("#phone").on("keypress", function(evt){
  
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });	
	//Enter only numbers and letters
	$("#pan_number").on("keypress", function(event){		
        var ew = event.which;
      
        if ((ew == 0 || ew == 8 )||(ew >= 48 && ew <= 57) || (ew >= 65 && ew <= 90) || (ew >= 97 && ew <= 122 ) ) {
           
            return true;
        }
        return false;
    });
    
    //Enter only letters 
     $("#first_name,#last_name").on("keypress",function(event){
         var inputValue = event.which;
       
        if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0 && inputValue != 8)) { 
            event.preventDefault(); 
        }
    });
    
	//Reset the Search Filters
	$('#clear_search_filters').click(function(){
		$('.search_filter', "form#search_filter_form").val('');
		$("form#search_filter_form").submit();
	});
	//Active/Deactive Agent
	$('.toggle-user-status').on('change', function(e) {
		e.preventDefault();
		var _user_status = this.value;
		var _opp_url = app_base_url+'index.php/user/';
		if (parseInt(_user_status) == 1) {
			_opp_url = _opp_url+'activate_account/';
		} else if(parseInt(_user_status) == 0){
			_opp_url = _opp_url+'deactivate_account/';
		}else if(parseInt(_user_status) == 2){
			_opp_url = _opp_url+'lock_account/';
		}
		_opp_url = _opp_url+$(this).data('user-id')+'/'+$(this).data('uuid');
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Updated Successfully!!!');
		});
	});
	//Send Agent Password
	$('.send_agent_new_password').on('click', function(e) {
		e.preventDefault();
		var _opp_url = app_base_url+'index.php/user/send_agent_new_password/';
		_opp_url = _opp_url+$(this).data('user-id')+'/'+$(this).data('uuid');
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Updated Successfully!!!');
		});
	});
	//Delete Agent
	$('.delete_agent').on('click', function(e) {
		e.preventDefault();
		var _opp_url = app_base_url+'index.php/user/delete_agent/';
		_opp_url = _opp_url+$(this).data('user-id')+'/'+$(this).data('uuid');
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Updated Successfully!!!');
		});
	});
	$('.reset_user_password').on('click', function(e) {
		var user_id = $(this).data('user_id');
		$("#rup_user_id").val(user_id);
	});
	//Reset agent password
	$('#rup_submit_btn').on('click', function(e) {
		var user_id = $('#rup_user_id').val();
		var password = $("#rup_user_password").val();
		if(password.length < 8){
			$("#rup_error_message").text("Password has to contain atleast 8 charectors.");
			return false;
		}
		if(!isNaN(password)){
			$("#rup_error_message").text("Password has to contain alpha numeric charectors.");
			return false;
		}
		var _opp_url = app_base_url+'index.php/user/update_user_password/';
		_opp_url = _opp_url+user_id+'/'+password;
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Password Updated Successfully!!!');
			$('#rup_user_id').val(0);
			$("#rup_user_password").val("");
			$("#reset_user_password_modal").modal("hide");
		});
	});
	//Fiter Agent
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
	 
	

	//get city list based on country code
	
	$("#country_name").on("change",function(){
		get_city_lists();
	});

	function get_city_lists()
    {
      var country_id = $("#country_name").val();
    
      if(country_id == '' || country_id == 'INVALIDIP'){
          $("#city").empty().html('<option value = "" selected="">Select City</option>');
         return false;
      } 
      //console.log("country_id"+country_id);
        
      	$.get(app_base_url+'index.php/ajax/get_city_lists',{country_id : country_id},function( data ) {
      		
	         $("#city").empty().html(data);
	        //console.log("change called,,,");
	       // console.log(city_edit_text.toLowerCase());
	        if(city_edit_text !=''){
	         	 for (var i = 0; i < citySelect.options.length; i++) {
			     	    	
			        if (citySelect.options[i].text.toLowerCase() == city_edit_text.toLowerCase()) {
			      	        	
			            citySelect.options[i].selected = true;
			            return;
			        }
				 }
	         }
	        $("#city").val(default_city);
	        
      	});
    }

});
</script>
<script type="text/javascript">
	$(document).ready( function () {
	    $('.datatables-td').DataTable();
	} );
</script>
<style type="text/css">
	.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
    	font-size: 11px!important;
	}
	.fixed .content-wrapper, .fixed .right-side {
    padding-top: 0px;
}
 .fa-red{
    color: #e80000;
  }
  .fa-grey{
    color: #aaa;
  }
  .fa-darkgrey{
    color: #777;
  }
</style>
