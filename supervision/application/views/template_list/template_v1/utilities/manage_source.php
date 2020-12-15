<?php
$active_domain_modules = $GLOBALS['CI']->active_domain_modules;
$master_module_list = $GLOBALS['CI']->config->item('master_module_list');
if (empty($default_view)) {
	$default_view = $GLOBALS['CI']->uri->segment(1);
}
function set_default_active_tab($module_name, &$default_active_tab)
{
	if (empty($default_active_tab) == true || $module_name == $default_active_tab) {
		if (empty($default_active_tab) == true) {
			$default_active_tab = $module_name; // Set default module as current active module
		}
		return 'active';
	}
}
?>
<div class="box box-danger">
	<div class="box-header with-border">
		<h3 class="box-title"><i class="fa fa-database"></i> Manage API Details</h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-5">
				<div class="box box-danger"  style="background:#EFEFEF">
					<div class="box-header with-border">
						<h3 class="box-title">API List</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
			<?php
			if (valid_array($list_data) == true) {
				foreach ($list_data as $k => $v) {
					if($v['name'] == 'Bus' || $v['name'] == 'Flight'){
						$booking_source = implode('<br>', explode(DB_SAFE_SEPARATOR, $v['asm_status_label']));
						echo '<strong><i class="fa fa-book margin-r-5"></i>  '.$v['name'].'</strong>
						<p class="text-muted">
							'.$booking_source.'.
						</p>
						<hr>';
					}else{
						continue;
					}
				}
			}
			?>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-md-7">
				<div class="nav-tabs-custom">
					<!-- Nav pills -->
					<ul class="nav nav-tabs" role="tablist">
						<?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
						<li role="presentation" class="flight-l-bg <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"><a href="#flight" aria-controls="flight" role="pill" data-toggle="pill"><i class="fa fa-plane"></i> <span class="hidden-xs">Flight</span></a></li> 
						<?php } ?>
						<?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
						<li role="presentation" class="hotel-l-bg <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"><a href="#hotel" aria-controls="hotel" role="pill" data-toggle="pill"><i class="fa fa-bed"></i> <span class="hidden-xs">Hotel</span></a></li>
						<?php } ?>
						<?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
						<li role="presentation" class="bus-l-bg <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"><a href="#bus" aria-controls="bus" role="pill" data-toggle="pill"><i class="fa fa-bus"></i> <span class="hidden-xs">Bus</span></a></li>
						<?php } ?>

						<a style="float: right;" href="javascript:void" data-toggle ="modal" data-target="#myModal">Manage Arilines</a>
						<a style="float: right; margin-right: 20px;" href="javascript:void" data-toggle ="modal" data-target="#manage_buses">Manage Buses</a>
						<a style="float: right; margin-right: 20px;" target="_blank" href="get_ets_operators">Manage ETS</a>
					</ul>
				</div>
				<?php
				//Form Booking Source List Details
				foreach ($list_data as $k => $v) {
					switch ($v['course_id']) {
						case META_AIRLINE_COURSE : $airline_api_list = $v;
							break;
						case META_ACCOMODATION_COURSE : $accomodation_api_list = $v;
							break;
						case META_BUS_COURSE : $bus_api_list = $v;
							break;
					}
					
				}
				/**
				 * create array with booking source details
				 */
				function extract_booking_source_details($data_list)
				{
					$data = '';
					$booking_source_name = explode(DB_SAFE_SEPARATOR, $data_list['booking_source']);
					$booking_source_origin = explode(DB_SAFE_SEPARATOR, $data_list['bs_origin']);
					$booking_source_id = explode(DB_SAFE_SEPARATOR, $data_list['booking_source_id']);
					$asm_status = explode(DB_SAFE_SEPARATOR, $data_list['asm_status']);
					$course_origin = $data_list['origin'];
					foreach ($booking_source_id as $k => $v) {
						if ($asm_status[$k] == 'active') {
							$asm_status_needle = 'checked="checked"';
						} else {
							$asm_status_needle = '';
						}
						$data .= '<p class="text-navy"><label><input autocomplete="off" type="checkbox" data-mc-id="'.$course_origin.'" data-bs-id="'.$booking_source_origin[$k].'" class="asm-status-toggle" '.$asm_status_needle.'> '.$booking_source_name[$k].' - '.$booking_source_id[$k].'</label></p><hr>';
					}
					return $data;
				}
				?>
				<!-- Tab panes -->
				<div class="tab-content highlight">
					<p>Click On select box to activate/deactivate API</p>
					<?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
					<div role="tabpanel" class="clearfix tab-pane fade in <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>" id="flight">
						<?php
						//Show All Booking Source
						if (empty($airline_api_list) == false) {
							echo extract_booking_source_details($airline_api_list);
						}
						?>
					</div>
					<?php } ?>
					<?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
					<div role="tabpanel" class="clearfix tab-pane fade in <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>" id="hotel">
						<?php
						if (empty($accomodation_api_list) == false) {
							echo extract_booking_source_details($accomodation_api_list);
						}
						?>
					</div>
					<?php } ?>
					<?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
					<div role="tabpanel" class="clearfix tab-pane fade in <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>" id="bus">
						<?php
						if (empty($bus_api_list) == false) {
							echo extract_booking_source_details($bus_api_list);
						}
						?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<!-- /.box-body -->
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header head">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Blocked Airlines</h4>
      	</div>
      	<div class="modal-body">
        	<div class="row">
        		<table class="table table-striped">
        			<?php 
	        			$tbo = array();
	        			$gds = array(); 
	        			foreach ($manage_airlines as $key => $value) {
	        				if($value['source_id'] == PROVAB_FLIGHT_BOOKING_SOURCE){
	        					array_push($tbo,json_decode($value['airline_attr'],1));
	        				}else if($value['source_id'] == TRAVELPORT_GDS_BOOKING_SOURCE){
	        					array_push($gds,json_decode($value['airline_attr'],1));
	        				}
	        			}
        			?>
        			<thead class="thead-dark">
        				<th scope="col">#</th>
					    <th scope="col">Airlines</th>
					    <th scope="col">TBO</th>
					    <th scope="col">Travelport GDS</th>
        			</thead>
        			<tbody>
        				<form method="post" action="<?= base_url()?>utilities/manage_airlines">
	        				<tr>
	        					<td>1</td>
	        					<td>Indigo</td>
	        					<td><input <?php if(in_array('6E',$tbo[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_tbo[]" value="6E"></td>
	        					<td><input <?php if(in_array('6E',$gds[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_gds[]" value="6E"></td>
	        				</tr>
	        				<tr>
	        					<td>2</td>
	        					<td>Spicejet</td>
	        					<td><input <?php if(in_array('SG',$tbo[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_tbo[]" value="SG"></td>
	        					<td><input <?php if(in_array('SG',$gds[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_gds[]" value="SG"></td>
	        				</tr>
	        				<tr>
	        					<td>3</td>
	        					<td>Air India</td>
	        					<td><input <?php if(in_array('AI',$tbo[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_tbo[]" value="AI"></td>
	        					<td><input <?php if(in_array('AI',$gds[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_gds[]" value="AI"></td>
	        				</tr>
	        				<tr>
	        					<td>4</td>
	        					<td>Vistara</td>
	        					<td><input <?php if(in_array('UK',$tbo[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_tbo[]" value="UK"></td>
	        					<td><input <?php if(in_array('UK',$gds[0])){ echo 'checked'; } ?> type="checkbox" name="air_code_gds[]" value="UK"></td>
	        				</tr>
	        				<tr>
	        					<td colspan="4">
	        						<input type="submit" value="Update" class="btn btn-warning" style="float:right;">
	        					</td>
	        				</tr>
        				</form>
        			</tbody>
        		</table>
        	</div>
      	</div>
      	<!-- <div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	</div> -->
    </div>

  </div>
</div>

<!-- Modal -->
<div id="manage_buses" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header head">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Blocked Buses</h4>
      	</div>
      	<div class="modal-body">
        	<div class="row">
        		<table class="table table-striped">
        			<thead class="thead-dark">
        				<th scope="col">#</th>
					    <th scope="col">Operator</th>
					    <th scope="col">Direct</th>
					    <th scope="col">Bitla</th>
        			</thead>
        			<tbody>
        				<form method="post" action="<?= base_url()?>utilities/manage_buses">
							<?php 
							foreach($manage_buses AS $bk=>$bv)
							{ ?>
	        				<tr>
	        					<td><?=$bv["origin"]?></td>
								<td><?=$bv["operator_name"]?></td>
	        					<td>
	        						<input type="radio" <?php if($bv["is_active"]){ echo 'checked'; } ?>  name="operator_state[<?=$bv["origin"]?>]" value="1">
								</td>
								<td>
									<input type="radio" <?php if(!$bv["is_active"]){ echo 'checked'; } ?>  name="operator_state[<?=$bv["origin"]?>]" value="0">
								</td>
	        				</tr>
							<?php } ?>
	        				<tr>
	        					<td colspan="4">
	        						<input type="submit" value="Update" class="btn btn-warning" style="float:right;">
	        					</td>
	        				</tr>
        				</form>
        			</tbody>
        		</table>
        	</div>
      	</div>
      	<!-- <div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	</div> -->
    </div>

  </div>
</div>


<div id="manage_direct_buses" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header head">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Bitla Direct Buses</h4>
      	</div>
      	<div class="modal-body">
        	<div class="row">
        		<table class="table table-striped">
        			<thead class="thead-dark">
					    <th scope="col">Operator</th>
					    <th scope="col">Bitla</th>
					    <th scope="col">Direct</th>
        			</thead>
        			<tbody>
        				<form method="post" action="<?= base_url()?>utilities/manage_direct_buses">
							<?php if(isset($bitla_direct_buses) && !empty($bitla_direct_buses)){
								foreach ($bitla_direct_buses as $bbd_key => $bbd_value) { ?>
									<tr>
			        					<td><?php echo $bbd_value['name']; ?></td>
										<td><input type="radio" <?php if($bbd_value["is_bitla_direct"] == 0){ echo 'checked'; } ?>  name="operator_state[<?=$bbd_value["origin"]?>]" value="0"></td>
			        					<td><input type="radio" <?php if($bbd_value["is_bitla_direct"] == 1){ echo 'checked'; } ?>  name="operator_state[<?=$bbd_value["origin"]?>]" value="1"></td>
			        				</tr>
								<?php } } ?>
	        				
	        				<tr>
	        					<td colspan="4">
	        						<input type="submit" value="Update" class="btn btn-warning" style="float:right;">
	        					</td>
	        				</tr>
        				</form>
        			</tbody>
        		</table>
        	</div>
      	</div>
      	<!-- <div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	</div> -->
    </div>

  </div>
</div>

<script>
$(document).ready(function() {
	$('.asm-status-toggle').on('change', function() {
		var _bs_id = parseInt($(this).data('bs-id'));
		var _mc_id = parseInt($(this).data('mc-id'));
		if(_bs_id == 37 && _mc_id == 26){
			$('#manage_direct_buses').modal('show');
		}else if (_bs_id > 0 && _mc_id > 0) {
			$.get(app_base_url+"index.php/utilities/toggle_asm_status/"+_bs_id+"/"+_mc_id+"/"+this.checked, function(resp) {
			});
		}
	});
});
</script>