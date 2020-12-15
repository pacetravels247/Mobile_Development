<div class="bodyContent col-md-12">
<div class="table_outer_wrper"><!-- PANEL WRAP START -->
<div class="panel_custom_heading"><!-- PANEL HEAD START -->
<div class="panel_title">
<ul class="nav nav-tabs b2b_navul" role="tablist" id="myTab">
	<li role="presentation" class="active"><a href="#tabList"
		aria-controls="home" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span>Bank Account Details
	</a></li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel_bdy"><!-- PANEL BODY START -->
<div class="tab-content">

<!-- Table List -->
<div role="tabpanel" class="tab-pane active clearfix" id="tabList">
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
<th>Bank Logo</th>
<th>Account Name</th>
<th>Account Number</th>
<th>Bank Name</th>
<th>Branch Name</th>
<th>IFSC Code</th>
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
</tr>';
		}
	} else {
		$table .= '<tr><td colspan="7">No Data Found</td></tr>';
	}
	$table .= '</table></div>';
	return $table;
}
?>
