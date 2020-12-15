<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation">
                  <a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
                    <i class="fa fa-edit"></i>
                     <?php echo get_app_message('AL00113');?>
                  </a>
			      </li>
					<li role="presentation" class="active">
					   <a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
                     <i class="fa fa-users"></i>
					      <?php echo get_app_message('AL00112');?>
					   </a>
			      </li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class=""><!-- PANEL BODY START -->
			<div class="tab-content">
           <div role="tabpanel" class="clearfix tab-pane" id="fromList">
               <div class="panel <?=PANEL_WRAPPER?> clearfix">
                  <div class="">
                     <?php 
                        /************************ GENERATE CURRENT PAGE FORM ************************/
                        if (isset($eid) == false || empty($eid) == true) {
                           /*** GENERATE ADD PAGE FORM ***/
                           echo $this->current_page->generate_form('module_management', $form_data);
                        } else {
                           echo $this->current_page->generate_form('module_management_edit', $form_data);
                        }
                        /************************ GENERATE UPDATE PAGE FORM ************************/
                     ?>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="clearfix tab-pane active" id="tableList">
               <div class="panel <?=PANEL_WRAPPER?> clearfix">
                  <div class="">
                     <?php
                     /************************ GENERATE CURRENT PAGE TABLE ************************/
                     echo get_table($table_data);
                     /************************ GENERATE CURRENT PAGE TABLE ************************/
                     ?>
                  </div>
               </div>
            </div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
<script>
$(document).ready(function() {
	<?php
	/**
	 * tab set up
	 */
	if (valid_array($_POST) == true || isset($_GET['eid']) == true) {
	?>
		$('#fromListHead').trigger('click');
	<?php
	}
	?>
	
});
</script>
</div>
<!-- HTML END -->
<?php 
function get_table($table_data='')
{
	$table = '
   <div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed">';
      $table .= '<thead><tr>
   <th>'.get_app_message('AL006').'</th>
   <th>'.get_app_message('AL0092').'</th>
   <th>'.get_app_message('AL00114').'</th>
   <th>'.get_app_message('AL00145').'</th>
   <th>'.get_app_message('AL0019').'</th>
   <th>'.get_app_message('AL0035').'</th>
   <th>'.get_app_message('AL0012').'</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		foreach ($table_data as $k => $v) {
			$action = get_edit_button($v['origin'], $v['course_id']);
			
			$table .= '<tr>
			<td>'.($k+1).'</td>
			<td>'.$v['name'].'</td>
			<td>'.$v['course_id'].'</td>
			<td>'.$v['booking_source'].'</td>
			<td>'.get_status_label($v['status']).'</td>
			<td><p>'. get_account_link($v['created_datetime'], $v['created_by_id'], $v['username']).get_profile_icon($v['user_image'], THUMBNAIL).'</p></td>
			<td><div class="btn-group">'.$action.'</div></td>
	</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}

function get_edit_button($id, $course_id)
{
		return '<a role="button" href="'.base_url().'index.php/module/module_management?eid='.$id.'&course_id='.$course_id.'" class="btn btn-default btn-sm"><i class="fa fa-edit"></i>
		</a>
		';
		/*<a role="button" href="'.base_url().'general/account?uid='.$id.'" class="btn btn-sm">
		<span class="glyphicon glyphicon-zoom-in"></span>'.get_app_message('AL0023').'</a>*/
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
?>