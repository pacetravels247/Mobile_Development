<?php
if( isset($_GET['eid']) == TRUE OR validation_errors() != FALSE || (isset($_GET['op']) == true && $_GET['op'] == 'add')) {			
	$tab1="active";
	$tab2="";			
} else {
	$tab2="active";
	$tab1="";
}
?>
<div id="bank_details"
	class="bodyContent col-md-12">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?php echo $tab1;?>"><a href="#fromList"
		aria-controls="home" role="tab" data-toggle="tab"><?php echo get_app_message('AL00323');?>
	<span class="glyphicon glyphicon-pencil"></span></a></li>
	<li role="presentation" class="<?php echo $tab2;?>"><a href="#tabList"
		aria-controls="home" role="tab" data-toggle="tab"><?php echo get_app_message('AL00324');?>
	<span class="glyphicon glyphicon-book"></span></a></li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class=""><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?php echo $tab1;?>" id="fromList">
<div class="">
<?php
/** Generating Form**/
if( isset($_GET['eid']) == false || empty($_GET['eid']) == true ) {
     
     echo $this->current_page->generate_form('bank_account_details',$form_data);
     } else {
     
     echo $this->current_page->generate_form('bank_account_details_edit',$form_data);
     }
?>
</div>
</div>
<!-- Table List -->
<div role="tabpanel" class="tab-pane clearfix <?php echo $tab2;?>" id="tabList">
<div class="col-md-12">
<?php
echo get_table($table_data);
?>
</div>
</div>

</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>

<?php
function get_table($table_data='')
{
	$table = '
<div class="table-responsive col-md-12"><table class="table table-hover table-striped table-bordered table-condensed">';
	$table .= '<tr>
<th><i class="fa fa-sort-numeric-asc"></i> '.get_app_message('AL006').'</th>
<th>'.get_app_message('AL00330').'</th>
<th>'.get_app_message('AL00325').'</th>
<th>'.get_app_message('AL00326').'</th>
<th>'.get_app_message('AL00327').'</th>
<th>'.get_app_message('AL00331').'</th>
<th>'.get_app_message('AL00328').'</th>
<th>'.get_app_message('AL00329').'</th>
<th>'.get_app_message('AL0035').'</th>
<th>'.get_app_message('AL0047').'</th>
<th>'.get_app_message('AL0012').'</th>
</tr>';		

	if (valid_array($table_data) == true) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		foreach ($table_data as $k => $v) {			
			$table .= '<tr>
			<td>'.(++$current_record).'</td>
			<td><img height="75px" width="75px" src="'.$GLOBALS ['CI']->template->domain_images('bank_logo/'.$v['bank_icon']).'" alt="Bank Logo"></td>
			<td>'.$v['en_account_name'].'</td>
			<td>'.$v['account_number'].'</td>
			<td>'.$v['en_bank_name'].'</td>
			<td>'.$v['en_branch_name'].'</td>
			<td>'.$v['ifsc_code'].'</td>
			<td>'.$v['pan_number'].'</td>
			<td>'.app_friendly_date($v['created_datetime']).'</td>
			<td>'.get_status_label($v['status']).'</td>
			<td>'.get_edit_button($v['origin']).'</td>			
</tr>';
		}
	} else {
		$table .= '<tr><td colspan="9">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</table></div>';
	return $table;
}

function get_edit_button($id)
{
	return '<a role="button" href="'.base_url().'management/bank_account_details?eid='.$id.'" class="btn btn-default btn-sm btn-primary">
		'.get_app_message('AL0041').' <span class="glyphicon glyphicon-pencil"></span></a>
		';
}

function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success glyphicon glyphicon-hand-right">'.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '<span class="label label-danger glyphicon glyphicon-hand-right">'.get_enum_list('status', INACTIVE).'</span>
		<a role="button" href="" class="hide">'.get_app_message('AL0020').'</a>';
	}
}
?>
