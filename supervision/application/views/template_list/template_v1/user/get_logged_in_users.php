<?php
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active">
					   <a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
                     <i class="fa fa-users"></i>
					      <?php echo get_app_message('AL00312');?>
					   </a>
			      </li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->

<!-- PANEL HEAD START -->	
			<div class="panel-body">
			<h4>Search Panel</h4>
			<hr>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group">
					
					<div class="col-xs-4">
						<label>
						From Date
						</label>
						<input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="From Date">
					</div>
					<div class="col-xs-4">
						<label>
						To Date
						</label>
						<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="To Date">
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
					<input type="hidden" name="filter" value="<?php echo $_GET['filter']; ?>">
					<input type="hidden" name="q" value="<?php echo $_GET['q']; ?>">
				<button type="submit" class="btn btn-primary">Search</button> 
				<button type="reset" class="btn btn-warning">Reset</button>
				<a href="<?php echo base_url().'index.php/user/get_logged_in_users?filter=user_type&q=3'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>			
			</div>

		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
            <div role="tabpanel" class="clearfix tab-pane active" id="tableList">
               <div class="panel <?=PANEL_WRAPPER?>">
                  <div class="panel-heading"><?php echo get_utility_message('UL0095')?>-<strong>(<?php echo $online_total_users?>)</strong></div>
                  <div class="panel-body">
                     <?php
                     /************************ GET ONLINE USERS ************************/
                     echo get_table($online_users);
                     ?>
                  </div>
               </div>
               <div class="panel <?=PANEL_WRAPPER?>">
                  <div class="panel-heading"><?php echo get_utility_message('UL0096')?> <?php echo date('d-m-Y');?>-<strong>(<?php echo $logged_total_users?>)</strong></div>
                  <div class="panel-body">
                     <?php
                     /************************ GET LOGGED USERS ************************/
                     echo get_table($logged_users, true);
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
function get_table($table_data='', $log_out = false)
{
	$table = '
   <div class="table-responsive">
   <table class="table table-hover table-striped table-bordered table-condensed">';
      $table .= '<thead><tr>
   <th><i class="fa fa-sort-numeric-asc"></i> '.get_app_message('AL006').'</th>
   <th><i class="fa fa-sort-numeric-asc"></i>Agency Name</th>
   <th><i class="fa fa-image"></i> '.get_app_message('AL0011').'</th>
   <th><i class="fa fa-user"></i> '.get_app_message('AL007').'</th>
   <th><i class="fa fa-phone"></i> '.get_app_message('AL008').'</th>
   <th><i class="fa fa-group"></i> '.get_app_message('AL009').'</th>
   <th><i class=""></i> '.get_app_message('AL00308').'</th>';
      if($log_out) {
      	$table .= '<th><i class=""></i> '.get_app_message('AL00310').'</th>
      				<th><i class=""></i> '.get_app_message('AL00311').'</th>';
      } else {
      	$table .= '<th><i class=""></i> '.get_app_message('AL00309').'</th>';
      }
   $table .= '</tr></thead><tbody>';
	
	if (valid_array($table_data) == true) {
		
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		foreach ($table_data as $k => $v) {
			$table .= '<tr>
			<td>'.(++$current_record).'</td>
			<td>'.$v['agency_name'].'-'.provab_decrypt($v['uuid']).'</td>
			<td>'.get_profile_icon($v['image']).'</td>
			<td>'.get_enum_list('title', $v['title']).' '.$v['first_name'].' '.$v['last_name'].'</td>
			<td>'.$v['phone'].'-'.provab_decrypt($v['email']).'</td>
			<td>'.$v['user_type'].'</td>
			<td>'.$v['login_ip'].'</td>';
			if($log_out) {
	      	$table .= '<td>'.app_friendly_datetime($v['login_time']).'</td>
	      				<td>'.app_friendly_datetime($v['logout_time']).'</td>';
	      } else {
	      	$table .= '<td>'.app_friendly_datetime($v['login_time']).'</td>';
	      }
			$table .= '</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">'.get_app_message('AL00313').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}
?>
