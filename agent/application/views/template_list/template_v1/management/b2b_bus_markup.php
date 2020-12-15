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
                        <i class="fa fa-edit"></i> Manage Bus Markup
                    </div>

                    &nbsp;&nbsp;&nbsp;&nbsp;
		            <span style="">Maximum Markup Value : 
		            <strong><?echo $markup_limits[0]['value'];
			            if($markup_limits[0]['value_type'] == 'plus'){
			            	echo 'INR';
			            }else{
			            	echo '%';
			            }
		            ?></strong>
		            </span>
                    
                    <span class="pull-right">Note : Application Default Currency - <strong><?=get_application_default_currency()?></strong></span>
            
                </div>
            </div>
            
            
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->
			<fieldset><legend><i class="fa fa-bus"></i> Bus - Markup</legend>
			<?php echo $this->session->flashdata("msg"); ?>
				<form action="" class="form-horizontal" method="POST" autocomplete="off">
					<div class="hide">
						<input type="hidden" name="domain_origin" value="0" />
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
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="generic_value" class="col-sm-4 control-label">Markup Value<span class="text-danger">*</span></label>
								<input type="text" id="generic_value" name="generic_value" class=" generic_value numeric" placeholder="Markup Value" required="" value="<?=@$generic_markup_list[0]['value']?>" />
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
		<?php
		//NOTE : NOT NEEDED AS OF NOW - Balu A
		if (valid_array($specific_markup_list)) {//Check if domain list is present -Start IF ?>
		<div class="panel-body"><!-- PANEL BODY START -->
			<fieldset><legend><i class="fa fa-bus"></i> Bus - Specific Domain Markup</legend>
				<form action="<?=$_SERVER['PHP_SELF']?>" class="form-horizontal" method="POST" autocomplete="off">
					<input type="hidden" name="form_values_origin" value="specific" />
				<?php foreach ($specific_markup_list as $__doamin_index => $__doamin_record) {
						$default_percentage_status = $default_plus_status = '';
						if (empty($__doamin_record['value_type']) == true || $__doamin_record['value_type'] == 'percentage') {
							$default_percentage_status = 'checked="checked"';
						} else {
							$default_plus_status = 'checked="checked"';
						}
				?>
						<div class="hide">
							<input type="hidden" name="domain_origin[]" value="<?=$__doamin_record['domain_origin']?>" />
							<input type="hidden" name="markup_origin[]" value="<?=$__doamin_record['markup_origin']?>" />
						</div>
						<div class="row">
							<div class="col-md-2">
								<?=($__doamin_index+1);?>
							</div>
							<div class="col-md-2">
								<?=$__doamin_record['domain_name']?>
							</div>
							<div class="col-md-4">
								<div class="radio">
									<label class="hide col-sm-4 control-label">Markup Type<span class="text-danger">*</span></label>
									<label for="value-type-plus-<?=$__doamin_index?>" class="radio-inline">
										<input <?=$default_plus_status?> type="radio" value="plus" id="value-type-plus-<?=$__doamin_index?>" name="value_type_<?=$__doamin_record['domain_origin']?>" class=" value-type-plus radioIp" checked="checked" required=""> Plus(+ <?=get_application_default_currency()?>)
									</label>
									<label for="value-type-percent-<?=$__doamin_index?>" class="radio-inline">
										<input <?=$default_percentage_status?> type="radio" value="percentage" id="value-type-percent-<?=$__doamin_index?>" name="value_type_<?=$__doamin_record['domain_origin']?>" class=" value-type-percent radioIp" required=""> Percentage(%)
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="specific-value-<?=$__doamin_index?>" class="col-sm-4 control-label">Value</label>
									<input type="text" id="specific-value-<?=$__doamin_index?>" name="specific_value[]" class=" specific-value numeric" placeholder="Markup Value" value="<?=$__doamin_record['value']?>" />
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
		<?php } //check if domain list is present - End IF?>
	</div><!-- PANEL WRAP END -->
</div>