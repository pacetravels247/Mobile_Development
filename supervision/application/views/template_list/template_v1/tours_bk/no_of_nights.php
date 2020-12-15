<?php 
error_reporting(0);
        for($i=1;$i<=$no_of_nights;$i++)
		{
            $return =  '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Day '.$i.' </label>
								</div>';
		    $return .= '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Place Name </label>
								<div class="col-sm-4 controls">
									<input type="text" name="place_name'.$i.'"
										placeholder="Enter place name" data-rule-required="true"
										class="form-control" required>									
								</div>
							</div>';
			/*$return .= '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Description
								</label>
								<div class="col-sm-4 controls">
								<textarea name="itinerary_des'.$i.'" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Highlights"></textarea>
								</div>
							</div>';*/
			$return .= '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Accomodation </label>
								<div class="col-sm-4 controls">
									<input type="checkbox" name="accomodation'.$i.'[]" value="1"> Breakfast <br>									
									<input type="checkbox" name="accomodation'.$i.'[]" value="2"> Lunch <br>									
									<input type="checkbox" name="accomodation'.$i.'[]" value="3"> Dinner <br>									
								</div>
							</div>';							
		}
		return $return;
?>