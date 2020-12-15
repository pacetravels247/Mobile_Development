<?php 

if (form_visible_operation()) {
	$tab1 = " active ";
	$tab2 = "";
} else {
	$tab2 = " active ";
	$tab1 = "";
}

?>

<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?=$tab1?>">
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Promo Code</a>
	</li>
	<li role="presentation" class="<?=$tab2?>">
		<a	href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">Promo Code List </a>
	</li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?=$tab1?>" id="fromList">
<div class="panel-body">
<?php
	/************************ GENERATE CURRENT PAGE FORM ************************/
	if (isset($_GET['eid']) == false || empty($_GET['eid']) == true) {
		//ADD FORM
		
		echo $promo_code_page_obj->generate_form('promo_codes_form', $from_data);
	} else {
		//EDIT FORM

		echo $promo_code_page_obj->generate_form('promo_codes_form_edit', $from_data);
	}
	/************************ GENERATE UPDATE PAGE FORM ************************/
?>
</div>
</div>
<div role="tabpanel" class="clearfix tab-pane <?=$tab2?>" id="tableList">
<!--/************************ GENERATE Filter Form ************************/-->
<h4>Search Panel</h4>
<hr>
<form method="GET" autocomplete="off" id="search_promocode_form">
	<div class="clearfix form-group">
		<div class="col-xs-4">
			<label>Promo Code</label>
			<input type="text" placeholder="Promo Code" value="<?=@$_GET['promo_code']?>" name="promo_code" id="filter_promo_code" class="form-control">
		</div>
		<div class="col-xs-4">
			<label>Module</label>
			<select name="module" class="form-control">
				<option value="">Please select</option>
				<?php echo generate_options($promocode_module_options, (array)@$_GET['module']);?>
			</select>
		</div>
	</div>
	<div class="col-sm-12 well well-sm">
		<button class="btn btn-primary" type="submit">Search</button> 
		<button class="btn btn-warning" type="reset">Reset</button>
		<a href="<?php echo base_url(); ?>index.php/utilities/manage_promo_code" id="clear-filter" class="btn btn-primary">ClearFilter</a>
	</div>
</form>
<div class="clearfix"></div>
<!--/************************ GENERATE Filter Form ************************/-->
<?php
/************************ GENERATE CURRENT PAGE TABLE ************************/
echo get_table($promo_code_list);
/************************ GENERATE CURRENT PAGE TABLE ************************/
?>
</div>
</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->
<?php 
function get_table($promo_code_list)
{
	$table  = '';
	$table  .= $GLOBALS['CI']->pagination->create_links();
	$table .= '<table class="table table-bordered table-hover table-condensed">';
	$table .= '<tr>';
	$table .= '<th><i class="fa fa-sort-numeric-asc"></i> Sno</th>';
	$table .= '<th>Promo Code</th>';
	$table .= '<th>Image</th>';
	$table .= '<th>Discount</th>';
	$table .= '<th>Valid Upto</th>';
	$table .= '<th>Minimum Amount</th>';
	$table .= '<th>Module</th>';
	$table .= '<th>Status</th>';
	$table .= '<th>Created On</th>';
	$table .= '<th>Action</th>';
	$table .= '</tr>';
	if(valid_array($promo_code_list)) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = (empty($segment_3) ? 0 : $segment_3);
		$image ='';
		foreach($promo_code_list as $k => $v) {
			if(!empty($v['promo_code_image'])){
				$image ="<img src='".$GLOBALS ['CI']->template->domain_promo_images ($v['promo_code_image'])."' height='100px' width='100px' class='img-thumbnail'>";

			}
			
			$action = '';
			extract($v);
			if(intval(strtotime($expiry_date)) <= 0) {
				$validity_label  = 'Unlimited';
				$days_left = 100;//TO Enable Edit Option, Setting Days left to 100
			} else {
				$days_left = get_date_difference(date('Y-m-d'), $expiry_date);
				if($days_left < 0) {
					$days_left = 0;
				}
				$validity_label = app_friendly_absolute_date($expiry_date).'('.$days_left.' Days Left)';
			}
			if($days_left > 0) {
				$action .= get_edit_button($origin);
				
				$action .= share_button($origin);
			} else {
				$action = 'Validity Expired';
				$action .= get_delete_buttoon($origin);
			}
			$table .= '<tr>';
			$table .= '<td>'.(++$current_record).'</td>';
			$table .= '<td>'.$promo_code.'</td>';
			$table .= '<td> '.$image.'</td>';
			$table .= '<td>'.$value.'  '.get_enum_list('value_type',$value_type).'</td>';
			$table .= '<td>'.$validity_label.'</td>';
			$table .= '<td>'.$v['minimum_amount'].'</td>';
			$table .= '<td>'.ucfirst($module).'</td>';
			$table .= '<td>'.get_status_label($status).'</td>';
			$table .= '<td>'.app_friendly_absolute_date($created_datetime).'</td>';
			$table .= '<td>'.$action.'</td>';
			$table .= '</tr>';
		}
	} else {
		$table .= '<tr><td colspan="6">No Data Found</td></tr>';
	}
	$table .= '</table>';
	return $table;
}
function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="fa fa-circle-o"></i> '.get_enum_list('status', ACTIVE).'</span>';
	} else {
		return '<span class="label label-danger"><i class="fa fa-circle-o"></i> '.get_enum_list('status', INACTIVE).'</span>';
	}
}
function get_edit_button($origin)
{
	return '<a role="button" href="'.base_url().'index.php/utilities/manage_promo_code?eid='.$origin.'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
		'.get_app_message('AL0022').'</a>';
}
function get_delete_buttoon($origin)
{
	return "<a class='btn btn-danger btn-xs has-tooltip' data-placement='top' title='Delete' onclick='return myfunction()' href='".base_url()."index.php/utilities/delete_promo_code/?eid=".$origin."'>
																	<i class='icon-remove'></i>Delete
																</a>";
}
/**
 * FIXME: Balu A--Implement Share It Button
 * @param $origin
 */
function share_button($origin)
{
	return '';
	$social1 = is_active_social_login('facebook');
	if($social1){
		$GLOBALS['CI']->load->library('social_network/facebook');
		//ADD Share Buuton
	}
}
?>
<script>
function myfunction(){
	 var conf = confirm('Are you sure, do you want to delete this record?');
	  
	   	if(conf == true){
	   		return true;
	   	}
	   	else{
	   		return false;
	   	}
}
$(document).ready(function() {
	//Unique PromoCode Validation
	$(document).on('focus blur', 'input#promo_code', function(){
		var has_readonly = $(this).attr('readonly');//FIXME:filter readonly
		
		var promo_code = $(this).val().trim();
		if(promo_code != '' && has_readonly != 'readonly') {
			$.get(app_base_url+'index.php/ajax/is_unique_promocode?promo_code='+promo_code, function(response){
				$('#promocode_unique_error_msg').remove();
				if(response.status == false) {
					$('#promo_code').val('');
					$('#promo_code').parent().append('<span id="promocode_unique_error_msg" class="text-danger"><strong>'+response.promo_code+'</strong> PromoCode Already Exists</span>');
				}
			});
		}
	});
	//Auto Suggest Promo Code
	var cache = {};
	$('#filter_promo_code', 'form#search_promocode_form').autocomplete({
		source:  function( request, response ) {
	        var term = request.term;
	        if ( term in cache ) {
	          response( cache[ term ] );
	          return;
	        } else {
	        	$.getJSON( app_base_url+"index.php/ajax/auto_suggest_promo_code", request, function( data, status, xhr ) {
	                cache[ term ] = data;
	                response( cache[ term ] );
	              });
	        }
	      },
	    minLength: 1
	 });
});
</script>
