<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default">
		<!-- PANEL WRAP START -->
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<!--/************************ GENERATE Filter Form ************************/-->
				<?php //debug($get_privilage); 
/*					$user_priv = $get_privilage ;
					debug($user_priv);die();*/
				?>
				
			
			<!--<form method="POST" action="<?= base_url().'index.php/user/add_usertype'?>">
				<div class="clearfix form-group">
					<div class="col-xs-4">
						<label>Add User Type :</label> <input type="text"
							placeholder="Previlage" value=""
							name="new_user_type" class="form-control">
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
					<button type="submit" class="btn btn-success btn-small">Add</button>
				</div>
			</form>-->
			
			<div class="clearfix"></div>
			<!--/************************ GENERATE Filter Form ************************/-->
			<div class="clearfix">
				<?php if($_SERVER['REMOTE_ADDR']=="14.141.47.106"){
					//debug($GLOBALS ['CI']->entity_user_type);exit;
	
					}
?>
				<hr>
				<form id="user_previlege_form" action="<?= base_url().'index.php/user/user_privilege?type='?>"
					method="POST">

					<div class="clearfix form-group">
					<div class="col-xs-4">
						<label>Select User Type :</label>
						<select class="form-control verify-previlages" name="user_type">
							<option value="">User Type</option>
							<?php
								foreach ($user_types['data'] as $key => $value) {
							?>
							<option value="<?=$value['origin'];?>" <?php if(isset($_GET['type']) && $_GET['type'] == $value['origin']) echo('selected') ?>><?=$value['user_type'];?></option>
						<?php } ?>
					</select>
					</div>
				</div>

					
			<hr>
					<!-- <input type="hidden" value="<?=@$eid; ?>" name="user_id" /> -->
<?php
/**
 * ********************** GENERATE CURRENT PAGE TABLE ***********************
 */
echo get_table ( @$table_data ,$get_privilage);
/**
 * ********************** GENERATE CURRENT PAGE TABLE ***********************
 */
?>
<div class="well well-sm text-center">
						<button class="btn btn-primary" type="submit">Save</button>
						<button class="btn btn-warning" type="reset">Reset</button>
					</div>
				</form>
			</div>
			<!-- PANEL BODY END -->
		</div>
		<!-- PANEL WRAP END -->
	</div>
</div>
<!-- HTML END -->
<?php
//$user_priv = $get_privilage ;
function get_table($table_data = '',$get_privilage='') {
	// debug($table_data);exit;
	$table = '';
	$table .= '
   <div class="clearfix">
   <table class="table table-condensed table-bordered">';
	$table .= '<thead><tr>
   <th><i class="fa fa-sort-numeric-asc"></i> SLNo</th>
  
   <th>Previlage Description</th>
   <th>Previlage Code</th>
   <th>Select <button class="btn btn-xs btn-primary" id="btn_select_all" data-check="" type="button">All</button></th>
   </tr></thead><tbody>';
	$header_pivilege = array("p1","p2","p3","p4","p5","p6","p7","p8","p9","p10","p11","p12","p13","p14","p15","p16","p74","p75","p36","p35","p44","p45","p46","p47","p48","p49","p50","p51","p52","p53","p54","p55","p64","p119","p118","p117","p116","p115","p114");
	if (valid_array ( $table_data ) == true) {
		 //debug($user_priv);exit;
		$segment_3 = $GLOBALS ['CI']->uri->segment ( 3 );
		$current_record = (empty ( $segment_3 ) ? 0 : $segment_3);
		//debug($table_data);die();
		foreach ( $table_data as $k => $v ) {
			
			// Booking
			$table .= '<tr>
			<td>' . (++ $current_record) . '</td>
			
			<td>' ; 
			if(in_array($v['privilege_key'], $header_pivilege)){
			$table .= '<b>'.$v ['description'].'</b>' ;
			}
			else {
				$table .= $v ['description'] ;
			}
			$table .= '</td>
			<td>'.$v['privilege_key'].'</td>
			<td>' . get_previlage_checkbox ( $v['origin'],$get_privilage) . 
			'</td>
			</tr>';
		}
	} else {
		$table .= '<tr><td colspan="8">' . get_app_message ( 'AL005' ) . '</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}
function get_previlage_checkbox($val,$get_privilage) {

	$checked = '';
	//debug($user_priv);die();
	if(!empty($get_privilage)){
		//die('78');
		if(in_array($val, $get_privilage)){
			$checked = 'checked';
		}
	}

	//return '<input type="checkbox" name="user_previlages[]" value="'.$val.'" class="toggle-user-previlages"';

	return '<input type="checkbox" name="user_previlages[]" value="'.$val.'" class="toggle-user-previlages" '.$checked.'/>';
}
?>
<script>
$(function(){
	$('#btn_select_all').on('click', function(){
			var check = $(this).attr('data-check');
			if(check == ''){
				$('[name="user_previlages[]"]').prop('checked',true);
				$(this).attr('data-check', 'checked').text('Reset');
			} else if(check == 'checked') {
				$('#user_previlege_form').get(0).reset();
				$(this).attr('data-check', '').text('All');
			}
	});
});

$(document).on('change','.verify-previlages',function(){
	//alert(this.value);
	var user_type = this.value;
	window.location.href = "<?= base_url().'index.php/user/user_privilege?type='?>"+ user_type;

})
</script>
