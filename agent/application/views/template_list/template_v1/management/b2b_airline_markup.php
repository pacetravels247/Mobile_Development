<!-- HTML BEGIN -->

<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
        
        <div class="panel_title">
        	<?php include 'b2b_markup_header_tab.php';?>
        </div>
        
			<div class="set_wraper">
            <div class="panel_title_bak">
            <div class="pull-left">
				<i class="fa fa-edit"></i> Manage Flight Markup
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span style="">Maximum Markup Value : 
            <strong id="limit_value">
            <?echo $markup_limits[0]['value'];?>	
            </strong>&nbsp;
            <strong id="limit_type">
            	<?
	            if($markup_limits[0]['value_type'] == 'plus'){
	            	echo 'INR';
	            }else{
	            	echo '%';
	            }
            ?>
            </strong>
            </span>

			<span class="pull-right">Note : Application Default Currency - 
            <strong><?=get_application_default_currency()?></strong>			
            </span>
		
			</div>
            </div>
            
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- Add Airline Starts-->
			<fieldset><legend><i class="fa fa-plane"></i> Add Airline  <i class=" fa fa-plus"></i></legend>
			<?php echo $this->session->flashdata("msg"); ?>
				<form action="<?php echo base_url("/index.php/management/b2b_airline_markup/"); ?>" class="form-horizontal" method="POST" autocomplete="off">
				<input type="hidden" name="form_values_origin" value="add_airline" />
				<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="new_airline_value" class="col-sm-3 control-label">Airlines<span class="text-danger">*</span></label>
								<div class="col-md-9">
									<select class="form-control" name="airline_code" required="required">
										<option value="">Please Select</option>
										<?php echo generate_options($airline_list);?>
									</select>
								</div>
							</div>
						</div>
					
						<div class="col-md-4">
							<div class="radio">
								<label for="value_type" class="col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
								<label for="airline_value_type_plus" class="radio-inline">
									<input checked="checked" type="radio" value="plus" id="airline_value_type_plus" name="value_type" class=" value_type_plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
								</label>
								<label for="airline_value_type_percent" class="radio-inline sup_perc_mt">
									<input type="radio" value="percentage" id="airline_value_type_percent" name="value_type" class=" value_type_percent radioIp" required=""> Percentage(%)
								</label>
							</div>
							<div class="radio">
								<label for="value_type_i" class="col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
								<label for="airline_value_type_plus_i" class="radio-inline">
									<input checked="checked" type="radio" value="plus" id="airline_value_type_plus_i" name="value_type_i" class=" value_type_plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
								</label>
								<label for="airline_value_type_percent_i" class="radio-inline sup_perc_mt">
									<input type="radio" value="percentage" id="airline_value_type_percent_i" name="value_type_i" class=" value_type_percent radioIp" required=""> Percentage(%)
								</label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="new_airline_value" class="col-sm-4 control-label">Domestic</label>
								<input type="text" id="new_airline_value" name="specific_value" class=" generic_value numeric" placeholder="Markup Value" value="" />
							</div>
							<div class="form-group">
								<label for="new_airline_value_i" class="col-sm-4 control-label">Inernational</label>
								<input type="text" id="new_airline_value_i" name="specific_value_i" class=" generic_value numeric" placeholder="Markup Value" value="" />
							</div>
						</div>
					</div>
					<div class="well well-sm">
						<div class="clearfix col-md-offset-1">
							<button class=" btn btn-sm btn-success " id="add-airline-submit-btn" type="submit">Add</button>
							<button class=" btn btn-sm btn-warning " id="add-airline-reset-btn" type="reset">Reset</button>
						</div>
					</div>
				</form>
			</fieldset>
		</div><!-- Add Airline Ends-->
		<div class="panel_bdy">
		<!-- PANEL BODY START -->
			<fieldset><legend><i class="fa fa-plane"></i> Flight - General Markup</legend>
				<form action="<?php echo base_url("/index.php/management/b2b_airline_markup/"); ?>" class="form-horizontal" method="POST" autocomplete="off">
					<div class="hide">
						<input type="hidden" name="form_values_origin" value="generic" />
						<input type="hidden" name="markup_origin" value="<?=@$generic_markup_list[0]['markup_origin']?>" />
					</div>
					<?php
					$default_percentage_status = $default_plus_status = '';
					if (isset($generic_markup_list[0]) == false || $generic_markup_list[0]['value_type'] == 'percentage') {
						$default_plus_status = 'checked="checked"';
						//$default_percentage_status = 'checked="checked"';
					} else {
						$default_plus_status = 'checked="checked"';
					}
					?>
					<div class="row">
						<div class="col-md-6">
							<div class="radio">
								<label for="value_type" class="col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
								<label for="value_type_plus" class="radio-inline">
									<input <?=$default_plus_status?> type="radio" value="plus" id="value_type_plus" name="value_type" class=" value_type_plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
								</label>
								<label for="value_type_percent" class="radio-inline sup_perc_mt">
									<input <?=$default_percentage_status?> type="radio" value="percentage" id="value_type_percent" name="value_type" class=" value_type_percent radioIp" required=""> Percentage(%)
								</label>
							</div>
							<div class="radio">
								<label for="value_type_i" class="col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
								<label for="value_type_plus_i" class="radio-inline">
									<input <?=$default_plus_status?> type="radio" value="plus" id="value_type_plus_i" name="value_type_i" class=" value_type_plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
								</label>
								<label for="value_type_percent_i" class="radio-inline sup_perc_mt">
									<input <?=$default_percentage_status?> type="radio" value="percentage" id="value_type_percent_i" name="value_type_i" class=" value_type_percent radioIp" required=""> Percentage(%)
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="generic_value" class="col-sm-4 control-label">Domestic<span class="text-danger">*</span></label>
								<input type="text" id="generic_value" name="generic_value" class=" generic_value numeric" placeholder="Markup Value" required="" value="<?=@$generic_markup_list[0]['value']?>" />
							</div>
							<div class="form-group">
								<label for="generic_value_i" class="col-sm-4 control-label">International<span class="text-danger">*</span></label>
								<input type="text" id="generic_value_i" name="generic_value_i" class=" generic_value numeric" placeholder="Markup Value" required="" value="<?=@$generic_markup_list[0]['int_value']?>" />
							</div>
						</div>
					</div>
					<div class="well well-sm">
						<div class="clearfix col-md-offset-1">
							<button class=" btn btn-sm btn-success " id="general-markup-submit-btn" type="submit">Save</button>
							<button class=" btn btn-sm btn-warning " id="general-markup-reset-btn" type="reset">Reset</button>
						</div>
					</div>
				</form>
			</fieldset>
		</div><!-- PANEL BODY END -->
		<?php if (valid_array($specific_markup_list)) {//Check if airline list is present -Start IF ?>
		<div class="panel_bdy"><!-- PANEL BODY START -->
			<fieldset><legend><i class="fa fa-plane"></i> Flight - Specific Airline Markup</legend>
				<form action="<?php echo base_url("/index.php/management/b2b_airline_markup/"); ?>" class="form-horizontal" method="POST" autocomplete="off">
					<input type="hidden" name="form_values_origin" value="specific" />
				<?php foreach ($specific_markup_list as $__airline_index => $__airline_record) {
						$default_percentage_status = $default_plus_status = '';
						if (empty($__airline_record['value_type']) == true || $__airline_record['value_type'] == 'percentage') {
							$default_plus_status = 'checked="checked"';
							//$default_percentage_status = 'checked="checked"';
						} else {
							$default_plus_status = 'checked="checked"';
						}
				?>
						<div class="hide">
							<input type="hidden" name="airline_origin[]" value="<?=$__airline_record['airline_origin']?>" />
							<input type="hidden" name="markup_origin[]" value="<?=$__airline_record['markup_origin']?>" />
						</div>
						<div class="row">
							<div class="col-md-2">
								<?=($__airline_index+1);?>
								<img src="<?=SYSTEM_IMAGE_DIR?>airline_logo/<?=$__airline_record['airline_code']?>.gif" alt="<?=$__airline_record['airline_name']?>">
							</div>
							<div class="col-md-2">
								<?=$__airline_record['airline_name']?>
							</div>
							<div class="col-md-4">
								<div class="radio">
									<label class="hide col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
									<label for="value-type-plus-<?=$__airline_index?>" class="radio-inline">
										<input <?=$default_plus_status?> type="radio" value="plus" id="value-type-plus-<?=$__airline_index?>" name="value_type_<?=$__airline_record['airline_origin']?>" class=" value-type-plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
									</label>
									<label for="value-type-percent-<?=$__airline_index?>" class="radio-inline sup_perc_mt">
										<input <?=$default_percentage_status?> type="radio" value="percentage" id="value-type-percent-<?=$__airline_index?>" name="value_type_<?=$__airline_record['airline_origin']?>" class=" value-type-percent radioIp" required=""> Percentage(%)
									</label>
								</div>
								<div class="radio">
									<label class="hide col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
									<label for="value-type-plus-i-<?=$__airline_index?>" class="radio-inline">
										<input <?=$default_plus_status?> type="radio" value="plus" id="value-type-plus-i-<?=$__airline_index?>" name="value_type_i_<?=$__airline_record['airline_origin']?>" class=" value-type-plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
									</label>
									<label for="value-type-percent-i-<?=$__airline_index?>" class="radio-inline sup_perc_mt">
										<input <?=$default_percentage_status?> type="radio" value="percentage" id="value-type-percent-i-<?=$__airline_index?>" name="value_type_i_<?=$__airline_record['airline_origin']?>" class=" value-type-percent radioIp" required=""> Percentage(%)
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="specific-value-<?=$__airline_index?>" class="col-sm-4 control-label">Domestic</label>
									<input type="text" id="specific-value-<?=$__airline_index?>" name="specific_value[]" class=" specific-value numeric" placeholder="Markup Value" value="<?=$__airline_record['value']?>" />
								</div>
								<div class="form-group">
									<label for="specific-value-i-<?=$__airline_index?>" class="col-sm-4 control-label">International</label>
									<input type="text" id="specific-value-i-<?=$__airline_index?>" name="specific_value_i[]" class=" specific-value numeric" placeholder="Markup Value" value="<?=$__airline_record['int_value']?>" />
								</div>
							</div>
						</div>
						<hr>
				<?php } ?>
				<div class="well well-sm">
					<div class="clearfix col-md-offset-1">
						<button class=" btn btn-sm btn-success " type="submit">Save</button>
						<button class=" btn btn-sm btn-warning " type="reset">Reset</button>
					</div>
				</div>
				</form>
			</fieldset>
		</div><!-- PANEL BODY END -->
		<?php } //check if airline list is present - End IF?>
	</div><!-- PANEL WRAP END -->
</div>
