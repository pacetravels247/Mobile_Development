<?php 
if($domain_admin_exists == true ) {
	//if domain admin exists and not in update mode, then hide add form
	$domain_admin_exists = true;
} else {
	$domain_admin_exists = false;
}
//echo "<pre>";print_r($subscriber_list);

?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default"><!-- PANEL WRAP START -->
	 <div class="clearfix"></div>
     <div class="dropdown col-xs-3">
	<button class="btn btn-info dropdown-toggle" type="button" id="excel_imp_drop" 	data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
     <i class="fa fa-download" aria-hidden="true"></i> Excel
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="excel_imp_drop">
    <li>
<a href="<?php echo base_url(); ?>index.php/general/export_subscribed_emails_report/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">subscribed email report</a>
    </li>
    </ul>
    </div>
    
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
            <div role="tabpanel" class="clearfix tab-pane active" id="tableList">
               <div class="panel <?=PANEL_WRAPPER?>">
                  <div class="panel-body">
                     <?php
                     /************************ GENERATE CURRENT PAGE TABLE ************************/
                     echo get_table(@$subscriber_list);
                     /************************ GENERATE CURRENT PAGE TABLE ************************/
                     ?>
                  </div>
               </div>
            </div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>
<!-- HTML END -->
<?php 
function get_table($subscriber_list='')
{
	
	if(is_array($_GET) && !empty($_GET)){
	$email = $_GET['email'];	
	$url = base_url().'index.php/general/view_subscribed_emails';
	$url_data = '<a href="'.$url.'">Back</a>';
	}
	else{
		$url_data ='';
	}
	
	
	$table = '
   <form method="GET" role="search">
      <div class="form-group">
         <div class="input-group">
            <input type="text" placeholder="'.get_app_message('AL004').'" class="form-control" name="email">
            <div class="input-group-addon handCursor">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
          	</div>
          	</div>
      </div>
   </form>'
   	.$url_data.
   '<div class="table-responsive">
   <table class="table table-hover table-striped table-bordered table-condensed">';
      $table .= '<thead><tr>
   <th><i class="fa fa-sort-numeric-asc"></i> '.get_app_message('AL006').'</th>
   <th><i class="fa fa-email"></i> '.get_app_message('AL00315').'</th>
   <th><i class="fa fa-flash"></i> '.get_app_message('AL0019').'</th>
   <th><i class="fa fa-flash"></i> '.get_app_message('AL0012').'</th>
   </tr></thead><tbody>';
	
	if (valid_array($subscriber_list) == true) {
		//$segment_3 = $GLOBALS['CI']->uri->segment(3);
		//$current_record = (empty($segment_3) ? 0 : $segment_3);
		foreach ($subscriber_list as $k => $v) {			
			$table .= '<tr>
			<td>'.($k+1).'</td>			
			<td>'.$v->email_id.'</td>
			<td>'.get_status_toggle_button($v->status, $v->id).'</td>
			<td>'.delete_subscription_mail($v->id).'</td>
</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}

function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="fa fa-hand-o-right"></i> '.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '<span class="label label-danger"><i class="fa fa-hand-o-right"></i> '.get_enum_list('status', INACTIVE).'</span>
		<a role="button" href="" class="hide">'.get_app_message('AL0020').'</a>';
	}
}

function get_edit_button($id)
{
		return '<a role="button" href="'.base_url().'index.php/user/user_management?'.$_SERVER['QUERY_STRING'].'&	eid='.$id.'" class="btn btn-default btn-sm btn-primary"><i class="fa fa-edit"></i>
		'.get_app_message('AL0022').'</a>
		';
		/*<a role="button" href="'.base_url().'general/account?uid='.$id.'" class="btn btn-sm">
		<span class="glyphicon glyphicon-zoom-in"></span>'.get_app_message('AL0023').'</a>*/
}

function delete_subscription_mail($id)
{
		return '<a role="button" href="'.base_url().'index.php/general/email_delete/'.$id.'"><button class="label label-danger">Delete</button></a>';
}
function get_status_toggle_button($status, $id)
{
	$status_options = get_enum_list('status');
	return '<select class="toggle-user-status" data-user-id="'.$id.'">'.generate_options($status_options, array($status)).'</select>';
	/*if (intval($status) == INACTIVE) {
		return '<a role="button" href="'.base_url().'user/activate_account/'.$user_id.'/'.$uuid.'" class="text-success">Activate</a>';
	} else {
		return '<a role="button" href="'.base_url().'user/deactivate_account/'.$user_id.'/'.$uuid.'" class="text-danger">Deactivate</a>';
	}*/
}
?>
<script>
$(document).ready(function() {
	$('.toggle-user-status').on('change', function(e) {
		e.preventDefault();
		var _user_status = this.value;
		var _opp_url = app_base_url+'index.php/general/';
		if (parseInt(_user_status) == 1) {
			_opp_url = _opp_url+'active_emails/';
		} else {
			_opp_url = _opp_url+'deactive_emails/';
		}
		_opp_url = _opp_url+$(this).data('user-id');
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Updated Successfully!!!');
		});
	});
});
</script>