<div class="panel-body"><!-- Add Airline Starts-->
<fieldset><legend><i class="fa fa-plane"></i> Add Airline  <i class=" fa fa-plus"></i></legend>
	<form action="" class="form-horizontal" method="POST" autocomplete="off">
	<input type="hidden" name="form_values_origin" value="add_airline" />
	<div class="row">
			<div class="col-md-6">
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
			<div class="col-md-6">
				<div class="form-group">
					<label for="new_airline_value" class="col-sm-4 control-label">Commission In Percentage</label>
					<input type="text" id="new_airline_value" name="specific_value" class=" generic_value numeric" placeholder="Commission In Percentage" value="" />
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

<?php if (valid_array($specific_markup_list)) {//Check if airline list is present -Start IF ?>
<div class="panel-body"><!-- PANEL BODY START -->
	<fieldset><legend><i class="fa fa-plane"></i> Flight - Specific Airline Commission</legend>
		<form action="<?=$_SERVER['PHP_SELF']?>" class="form-horizontal" method="POST" autocomplete="off">
			<input type="hidden" name="form_values_origin" value="update_existing_airline_commissions" />
		<?php foreach ($specific_markup_list as $__airline_index => $__airline_record) {
		?>
				<div class="hide">
					<input type="hidden" name="airline_origin[]" value="<?=$__airline_record['airline_origin']?>" />
					<input type="hidden" name="com_origin[]" value="<?=$__airline_record['com_origin']?>" />
				</div>
				<div class="row">
					<div class="col-md-4">
						<?=($__airline_index+1);?>
						<img src="<?=SYSTEM_IMAGE_DIR?>airline_logo/<?=$__airline_record['airline_code']?>.gif" alt="<?=$__airline_record['airline_name']?>">
					</div>
					<div class="col-md-4">
						<?=$__airline_record['airline_name']?>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="specific-value-<?=$__airline_index?>" class="col-sm-4 control-label">Commission</label>
							<input type="text" id="specific-value-<?=$__airline_index?>" name="specific_value[]" class=" specific-value numeric" placeholder="Commission In Percentage" value="<?=$__airline_record['api_value']?>" />
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