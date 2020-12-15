<?php 
for ($i = 0; $i < $duration; $i++) { ?>
<div class='form-group clearfix'>
	<label class='control-label col-sm-3' for='validation_desc'>Itinerary Description <?php echo $i + 1; ?> </label>
	<div class='col-sm-4 controls'>
		<textarea name="desc[]" class="form-control itenary_elements"
			data-rule-required="true" cols="70" rows="3"
			placeholder="Description" required></textarea>
	</div>
</div>
<div class='form-group clearfix'>
	<label class='control-label col-sm-3' for='validation_name'>Day </label>
	<div class='col-sm-4 controls'>
		<input type="text" name="days[]" id="days<?php echo $i + 1; ?>" data-rule-required='true'
			readonly value="<?php echo $i + 1; ?>"
			class='form-control itenary_elements' required>
	</div>
</div>
<div class='form-group clearfix'>
	<label class='control-label col-sm-3' for='validation_name'>Place </label>
	<div class='col-sm-4 controls'>
		<input type="text" name="place[]" id="Place<?php echo $i + 1; ?>" data-rule-required='true'
			class='form-control itenary_elements' placeholder="Place Name" required>
	</div>
</div>
<div class='form-group clearfix'>
	<label class='control-label col-sm-3' for='validation_company'>Itinerary
		Image</label>
	<div class='col-sm-3 controls'>
		<input type="file" title='Image to add' class='itenary_elements'
			data-rule-required='true' id='image<?php echo $i + 1; ?>' name='image[]' multiple required> <span
			id="pacmimg" style="color: #F00; display: none" >Please Upload
			Itinerary Image</span>
	</div>
</div>
<hr>
<?php } ?>