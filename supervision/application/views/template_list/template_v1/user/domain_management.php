<?php if (valid_array($_POST) == true || isset($_GET['eid']) == true ||  (isset($_GET['op']) == true && $_GET['op'] == 'add')) {
	$tab1 = " active ";
	$tab2 = " ";
} else {
	$tab2 = " active ";
	$tab1 = " ";
}
?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="<?php echo $tab1; ?>">
                  <a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
                    <i class="fa fa-edit"></i>
                     Add/Edit Domain
                  </a>
			      </li>
					<li role="presentation" class="<?php echo $tab2; ?>">
					   <a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
                     	<i class="fa fa-users"></i> Domain List
					   </a>
			      </li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
           <div role="tabpanel" class="clearfix tab-pane <?php echo $tab1;?>" id="fromList">
               
                     <?php 
                        /************************ GENERATE CURRENT PAGE FORM ************************/
                        if (isset($_GET['eid']) == false || empty($_GET['eid']) == true) {
                           /*** GENERATE ADD PAGE FORM ***/
                        	$form_data['origin'] = 0;
                           echo $this->current_page->generate_form('domain', $form_data);
                        } else {
                           echo $this->current_page->generate_form('domain_edit', $form_data);
                        }
                        /************************ GENERATE UPDATE PAGE FORM ************************/
                     ?>
                  
            </div>
            <div role="tabpanel" class="clearfix tab-pane <?php echo $tab2;?>" id="tableList">
               <div class="panel <?=PANEL_WRAPPER?>">
                  <div class="panel-body">
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
</div>
<!-- HTML END -->
<?php 
function get_table($table_data='')
{
	$table = '
   <form action="#" role="search">
      <div class="form-group">
         <div class="input-group">
            <input type="text" placeholder="'.get_app_message('AL004').'" class="form-control">
            <div class="input-group-addon handCursor">
               <i class="fa fa-search"></i>
            </div>
         </div>
      </div>
   </form>
   <div class="table-responsive">
   <table class="table table-hover table-striped table-bordered table-condensed">';
      $table .= '<thead><tr>
   <th><i class="fa fa-sort-numeric-asc"></i> '.get_app_message('AL006').'</th>
   <th>'.get_app_message('AL00302').'</th>
   <th>'.get_app_message('AL00303').'</th>
   <th>'.get_app_message('AL00304').'</th>
   <th>'.get_app_message('AL00306').'</th>
   <th class="hide">'.get_app_message('AL00305').'</th>
   <th class="hide">'.get_app_message('AL0035').'</th>
   <th>'.get_app_message('AL0012').'</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		foreach ($table_data as $k => $v) {
			//ACTION
			$action = get_edit_button($v['origin']);//EDIT BUTTON
			$action .= add_admin_button($v['origin']);//ADD ADMIN BUTTON
			$table .= '<tr>
			<td>'.(++$current_record).'</td>
			<td>'.$v['domain_name'].'</td>
			<td>'.$v['domain_ip'].'</td>
			<td>'.$v['domain_key'].'</td>
			<td>'.get_status_label($v['status']).'</td>
			<td class="hide">'.$v['comment'].'</td>
			<td class="hide">'.get_account_link($v['created_datetime'], $v['created_by_id'], $v['created_user_name']).'</td>
			<td>'.$action.'</td>
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

function get_edit_button($origin)
{
	return '<a role="button" href="'.base_url().'index.php/user/domain_management?eid='.$origin.'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
	'.get_app_message('AL0022').'</a>
	';
}
function add_admin_button($origin)
{
	return '<a role="button" href="'.base_url().'index.php/user/user_management?domain_origin='.$origin.'" class="btn btn-sm btn-info"><i class="fa fa-user"></i>
	'.get_app_message('AL00307').'</a>
	';
}