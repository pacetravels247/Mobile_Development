<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			Limt Agent Markups
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<h4>Markups</h4>
			<hr>
			<form method="POST" autocomplete="off"
				action="<?php echo base_url(). 'index.php/management/b2b_markup_limit/';?>">
				<?php 
				if (isset($details) && valid_array($details)) {
				    foreach ($details as $key => $val) {
				    	$perc_checked = ""; $plus_checked = "";
				    	if($val['value_type']=='percentage')
				    	{
				    		$plus_checked = "checked = checked";
				    		//$perc_checked = "checked = checked";
				    	}
				    	if($val['value_type']=='plus')
				    	{
				    		$plus_checked = "checked = checked";
				    	}
				        ?><input type="hidden" class="form-control" name="origin[]" value="<?=@$val['origin']?>">
				        <div class="clearfix form-group">
				        	<div class="col-md-2">
				        		<label><?=strtoupper(@$val['module_type'])?></label>
				        	</div>
        					<input type="hidden" name="module_type[]" value="<?=@$val['module_type']?>">
        				
        					<div class="col-md-3">
        						<label>Markup Value</label> 
        						<input type="text" class="form-control" name="markup[]" value="<?=@$val['value']?>" placeholder="value">
        					</div>
        					<div class="col-md-3">
        						<label>Value Type</label><br>
        						<label for="radio_plus" class="radio-inline">
        						<input <?php echo $plus_checked; ?>  type="radio" value="plus" id="radio_plus_<?=@$val['origin']?>" name="value_type[]_<?=@$val['origin']?>" class=" value_type_plus radioIp" required=""> 
        						Plus(+ INR)</label>

        						<label for="radio_prct" class="radio-inline sup_perc_mt">
        						<input <?php echo $perc_checked; ?> type="radio" value="percentage" id="radio_prct_<?=@$val['origin']?>" name="value_type[]_<?=@$val['origin']?>" class="value_type_percent radioIp invalid-ip" required=""> 
        						Percentage(%)</label>
        					</div>
        				</div>
				        <?php 
				    }
				}
				?>
				<div class="clearfix form-group">
					<div class="col-xs-6">
						<button type="submit" class="btn btn-warning">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>